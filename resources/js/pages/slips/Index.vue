<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, type Karyawan, type Slip } from '@/types';
import { Head, useForm, Link } from '@inertiajs/vue3';
import { ref, computed, watch } from 'vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { 
    Plus, Trash, FileText, CheckSquare, ListFilter, 
    FileDown, BadgeCheck, AlertCircle, RefreshCw 
} from 'lucide-vue-next';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
    {
        title: 'Salary Slips',
        href: '/slips',
    },
];

const props = defineProps<{
    slips: Slip[];
    karyawans: Karyawan[];
}>();

// Selection state for bulk operations
const selectedIds = ref<number[]>([]);

// Toggle select all
const toggleAll = () => {
    if (selectedIds.value.length === props.slips.length) {
        selectedIds.value = [];
    } else {
        selectedIds.value = props.slips.map(s => s.id);
    }
};

// Toggle individual selection
const toggleSelect = (id: number) => {
    const idx = selectedIds.value.indexOf(id);
    if (idx > -1) {
        selectedIds.value.splice(idx, 1);
    } else {
        selectedIds.value.push(id);
    }
};

// Modal open/close status
const isGenerateModalOpen = ref(false);
const isBulkPaidModalOpen = ref(false);
const isSingleDeleteModalOpen = ref(false);
const slipToDelete = ref<Slip | null>(null);

// Form configuration for slip generation
const form = useForm({
    karyawan_id: '',
    periode_start: '',
    periode_end: '',
    basic_salary: 0,
    overtime_salary: 0,
    meal_allowance: 0,
    transportation_allowance: 0,
    bonus_salary: 0,
    bonus_notes: '',
    late_deduction: 0,
    absence_deduction: 0,
    damaged_cost: 0,
    other_deduction: 0,
    other_deduction_notes: '',
});

// Bulk status form
const bulkForm = useForm({
    ids: [] as number[],
});

// Format Rupiah currency
const formatIDR = (value: number) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
    }).format(value);
};

// Automatically set employee basic salary when karyawan_id changes
watch(() => form.karyawan_id, (newId) => {
    if (newId) {
        const selectedEmployee = props.karyawans.find(k => k.id === Number(newId));
        if (selectedEmployee) {
            form.basic_salary = Number(selectedEmployee.salary);
        }
    } else {
        form.basic_salary = 0;
    }
});

// Live computation for modal preview
const computedDeduction = computed(() => {
    return Number(form.late_deduction || 0) +
           Number(form.absence_deduction || 0) +
           Number(form.damaged_cost || 0) +
           Number(form.other_deduction || 0);
});

const computedTotal = computed(() => {
    return Number(form.basic_salary || 0) +
           Number(form.overtime_salary || 0) +
           Number(form.meal_allowance || 0) +
           Number(form.transportation_allowance || 0) +
           Number(form.bonus_salary || 0) -
           computedDeduction.value;
});

// Handle slip generation submit
const handleGenerate = () => {
    form.post(route('slips.store'), {
        onSuccess: () => {
            isGenerateModalOpen.value = false;
            form.reset();
        }
    });
};

// Open bulk paid validation modal
const openBulkPaidModal = () => {
    if (selectedIds.value.length === 0) return;
    bulkForm.ids = [...selectedIds.value];
    isBulkPaidModalOpen.value = true;
};

// Execute marking slips as PAID
const executeBulkPaid = () => {
    bulkForm.post(route('slips.markAsPaid'), {
        onSuccess: () => {
            isBulkPaidModalOpen.value = false;
            selectedIds.value = [];
        }
    });
};

// Handle single mark as paid directly from table row
const markSinglePaid = (id: number) => {
    bulkForm.ids = [id];
    bulkForm.post(route('slips.markAsPaid'), {
        onSuccess: () => {
            selectedIds.value = selectedIds.value.filter(sId => sId !== id);
        }
    });
};

// Confirm single delete
const confirmDeleteSlip = (slip: Slip) => {
    slipToDelete.value = slip;
    isSingleDeleteModalOpen.value = true;
};

// Execute single delete
const executeDeleteSlip = () => {
    if (slipToDelete.value) {
        form.delete(route('slips.destroy', slipToDelete.value.id), {
            onSuccess: () => {
                isSingleDeleteModalOpen.value = false;
                selectedIds.value = selectedIds.value.filter(sId => sId !== slipToDelete.value?.id);
                slipToDelete.value = null;
            }
        });
    }
};

// Format Month-Year string (YYYY-MM to Month YYYY)
const formatPeriod = (periodStr: string) => {
    if (!periodStr) return '';
    try {
        const [year, month] = periodStr.split('-');
        const date = new Date(Number(year), Number(month) - 1);
        return date.toLocaleDateString('en-US', { month: 'long', year: 'numeric' });
    } catch (e) {
        return periodStr;
    }
};
</script>

<template>
    <Head title="Salary Slips" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-6">
            <!-- Header Section -->
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight">Active Salary Slips</h1>
                    <p class="text-sm text-muted-foreground">Manage active salary payouts, drafts, and bulk approve transactions.</p>
                </div>
                <div class="flex flex-wrap gap-2">
                    <Link :href="route('slips.paidList')">
                        <Button variant="outline" class="w-full sm:w-auto">
                            <CheckSquare class="h-4 w-4 mr-2" /> Paid List
                        </Button>
                    </Link>
                    <Button @click="isGenerateModalOpen = true" class="w-full sm:w-auto">
                        <Plus class="h-4 w-4 mr-2" /> Generate Slip
                    </Button>
                </div>
            </div>

            <!-- Toolbar/Header Actions for Table -->
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between border rounded-lg p-3 bg-card shadow-sm">
                <!-- Left side: Select count and bulk actions -->
                <div class="flex items-center gap-2">
                    <span class="text-xs text-muted-foreground font-medium">
                        {{ selectedIds.length }} of {{ slips.length }} selected
                    </span>
                    <Button 
                        v-if="selectedIds.length > 0" 
                        size="sm" 
                        variant="default"
                        class="bg-emerald-600 hover:bg-emerald-700 text-white shadow-sm"
                        @click="openBulkPaidModal"
                    >
                        <BadgeCheck class="h-3.5 w-3.5 mr-1" /> Mark as Paid
                    </Button>
                </div>

                <!-- Right side: PDF export for draft slips -->
                <div>
                    <a :href="route('slips.downloadDraftPdf')" target="_blank" class="w-full sm:w-auto inline-block">
                        <Button size="sm" variant="outline" class="w-full">
                            <FileDown class="h-4 w-4 mr-2" /> Download Draft Data (PDF)
                        </Button>
                    </a>
                </div>
            </div>

            <!-- Main Slips Table -->
            <div class="rounded-lg border bg-card shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left border-collapse">
                        <thead>
                            <tr class="border-b bg-muted/50 text-muted-foreground font-medium text-xs uppercase tracking-wider">
                                <th class="p-4 w-12 text-center">
                                    <input 
                                        type="checkbox" 
                                        :checked="selectedIds.length === slips.length && slips.length > 0"
                                        @change="toggleAll"
                                        class="rounded border-gray-300 text-primary focus:ring-primary h-4 w-4 cursor-pointer"
                                    />
                                </th>
                                <th class="p-4">Employee</th>
                                <th class="p-4">Period</th>
                                <th class="p-4">Gross Payout</th>
                                <th class="p-4">Deductions</th>
                                <th class="p-4">Net Salary</th>
                                <th class="p-4">Status</th>
                                <th class="p-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            <tr v-if="slips.length === 0" class="hover:bg-muted/10">
                                <td colspan="8" class="p-8 text-center text-muted-foreground">
                                    <div class="flex flex-col items-center gap-2">
                                        <FileText class="h-8 w-8 text-muted-foreground/60" />
                                        <p class="font-medium">No active slips generated</p>
                                        <p class="text-xs">Generate a salary slip for your employees to distribute wages.</p>
                                    </div>
                                </td>
                            </tr>
                            <tr v-for="s in slips" :key="s.id" class="hover:bg-muted/30 transition-colors">
                                <td class="p-4 text-center">
                                    <input 
                                        type="checkbox" 
                                        :checked="selectedIds.includes(s.id)"
                                        @change="toggleSelect(s.id)"
                                        class="rounded border-gray-300 text-primary focus:ring-primary h-4 w-4 cursor-pointer"
                                    />
                                </td>
                                <td class="p-4">
                                    <div class="font-medium text-foreground">{{ s.karyawan?.name }}</div>
                                    <div class="text-xs text-muted-foreground font-mono mt-0.5">{{ s.karyawan?.staff_id }} &bull; {{ s.karyawan?.position }}</div>
                                </td>
                                <td class="p-4">
                                    <div class="text-xs font-medium text-foreground">
                                        {{ formatPeriod(s.periode_start) }}
                                    </div>
                                    <div class="text-[10px] text-muted-foreground mt-0.5">
                                        to {{ formatPeriod(s.periode_end) }}
                                    </div>
                                </td>
                                <td class="p-4">
                                    {{ formatIDR(Number(s.basic_salary) + Number(s.overtime_salary) + Number(s.meal_allowance) + Number(s.transportation_allowance) + Number(s.bonus_salary)) }}
                                </td>
                                <td class="p-4 text-rose-600 dark:text-rose-500 font-medium">
                                    -{{ formatIDR(s.total_deduction) }}
                                </td>
                                <td class="p-4 font-semibold text-foreground">
                                    {{ formatIDR(s.total_salary) }}
                                </td>
                                <td class="p-4">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-amber-50 text-amber-700 dark:bg-amber-950/40 dark:text-amber-400 border border-amber-200/50">
                                        {{ s.status }}
                                    </span>
                                </td>
                                <td class="p-4 text-right">
                                    <div class="flex justify-end gap-1.5">
                                        <Button 
                                            @click="markSinglePaid(s.id)" 
                                            size="sm" 
                                            variant="outline" 
                                            class="h-8 border-emerald-600 text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-950/20"
                                        >
                                            Paid
                                        </Button>
                                        <Button @click="confirmDeleteSlip(s)" size="icon" variant="ghost" class="h-8 w-8 text-destructive hover:bg-destructive/10">
                                            <Trash class="h-3.5 w-3.5" />
                                            <span class="sr-only">Delete</span>
                                        </Button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Generate Slip Dialog -->
            <Dialog v-model:open="isGenerateModalOpen">
                <DialogContent class="sm:max-w-[650px] max-h-[90vh] overflow-y-auto">
                    <DialogHeader>
                        <DialogTitle>Generate Salary Slip</DialogTitle>
                        <DialogDescription>
                            Create a monthly payroll distribution sheet. Default status is DRAFT.
                        </DialogDescription>
                    </DialogHeader>

                    <form @submit.prevent="handleGenerate" class="space-y-5 py-2">
                        <!-- Employee selection & Period Selection -->
                        <div class="grid grid-cols-3 gap-4">
                            <div class="space-y-1 col-span-1">
                                <Label for="karyawan_id">Employee</Label>
                                <select 
                                    id="karyawan_id" 
                                    v-model="form.karyawan_id" 
                                    class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2"
                                    required
                                >
                                    <option value="">Select Employee</option>
                                    <option v-for="k in karyawans" :key="k.id" :value="k.id">
                                        {{ k.name }} ({{ k.staff_id }})
                                    </option>
                                </select>
                            </div>

                            <div class="space-y-1 col-span-1">
                                <Label for="periode_start">Period Start</Label>
                                <Input id="periode_start" type="month" v-model="form.periode_start" required />
                            </div>

                            <div class="space-y-1 col-span-1">
                                <Label for="periode_end">Period End</Label>
                                <Input id="periode_end" type="month" v-model="form.periode_end" required />
                            </div>
                        </div>

                        <!-- Basic Salary display & Edit -->
                        <div class="space-y-1">
                            <Label for="basic_salary">Basic Salary (Prefilled from profile)</Label>
                            <Input id="basic_salary" type="number" v-model="form.basic_salary" required />
                        </div>

                        <!-- Earnings / Allowance Section -->
                        <div class="rounded-md border p-4 bg-muted/20 space-y-4">
                            <h3 class="text-xs font-semibold uppercase tracking-wider text-muted-foreground">Earnings & Allowances</h3>
                            
                            <div class="grid grid-cols-2 gap-4">
                                <div class="space-y-1">
                                    <Label for="overtime_salary">Overtime Salary</Label>
                                    <Input id="overtime_salary" type="number" v-model="form.overtime_salary" min="0" />
                                </div>
                                <div class="space-y-1">
                                    <Label for="meal_allowance">Meal Allowance</Label>
                                    <Input id="meal_allowance" type="number" v-model="form.meal_allowance" min="0" />
                                </div>
                                <div class="space-y-1">
                                    <Label for="transportation_allowance">Transportation Allowance</Label>
                                    <Input id="transportation_allowance" type="number" v-model="form.transportation_allowance" min="0" />
                                </div>
                                <div class="space-y-1">
                                    <Label for="bonus_salary">Bonus Salary</Label>
                                    <Input id="bonus_salary" type="number" v-model="form.bonus_salary" min="0" />
                                </div>
                            </div>
                            <div class="space-y-1">
                                <Label for="bonus_notes">Bonus Notes</Label>
                                <Input id="bonus_notes" v-model="form.bonus_notes" placeholder="e.g. Q1 sales performance incentive" />
                            </div>
                        </div>

                        <!-- Deductions Section -->
                        <div class="rounded-md border p-4 bg-rose-50/10 dark:bg-rose-950/10 border-rose-200/20 space-y-4">
                            <h3 class="text-xs font-semibold uppercase tracking-wider text-rose-600 dark:text-rose-400">Deductions</h3>
                            
                            <div class="grid grid-cols-2 gap-4">
                                <div class="space-y-1">
                                    <Label for="late_deduction">Late Deduction</Label>
                                    <Input id="late_deduction" type="number" v-model="form.late_deduction" min="0" />
                                </div>
                                <div class="space-y-1">
                                    <Label for="absence_deduction">Absence Deduction</Label>
                                    <Input id="absence_deduction" type="number" v-model="form.absence_deduction" min="0" />
                                </div>
                                <div class="space-y-1">
                                    <Label for="damaged_cost">Damaged Cost Reimbursement</Label>
                                    <Input id="damaged_cost" type="number" v-model="form.damaged_cost" min="0" />
                                </div>
                                <div class="space-y-1">
                                    <Label for="other_deduction">Other Deduction</Label>
                                    <Input id="other_deduction" type="number" v-model="form.other_deduction" min="0" />
                                </div>
                            </div>
                            <div class="space-y-1">
                                <Label for="other_deduction_notes">Other Deduction Notes</Label>
                                <Input id="other_deduction_notes" v-model="form.other_deduction_notes" placeholder="e.g. Office tool damage fine" />
                            </div>
                        </div>

                        <!-- Live Calculation Preview -->
                        <div class="rounded-lg bg-secondary p-4 flex justify-between items-center text-sm">
                            <div>
                                <span class="text-muted-foreground">Estimated Deductions:</span>
                                <div class="font-bold text-rose-600 dark:text-rose-400">{{ formatIDR(computedDeduction) }}</div>
                            </div>
                            <div class="text-right">
                                <span class="text-muted-foreground">Estimated Net Salary:</span>
                                <div class="text-lg font-extrabold text-emerald-600 dark:text-emerald-500">{{ formatIDR(computedTotal) }}</div>
                            </div>
                        </div>

                        <DialogFooter class="pt-4 border-t">
                            <Button type="button" variant="outline" @click="isGenerateModalOpen = false" :disabled="form.processing">Cancel</Button>
                            <Button type="submit" :disabled="form.processing">
                                {{ form.processing ? 'Generating...' : 'Generate Draft Slip' }}
                            </Button>
                        </DialogFooter>
                    </form>
                </DialogContent>
            </Dialog>

            <!-- Bulk Paid Modal -->
            <Dialog v-model:open="isBulkPaidModalOpen">
                <DialogContent class="sm:max-w-[425px]">
                    <DialogHeader>
                        <DialogTitle>Mark Selected Slips as Paid</DialogTitle>
                        <DialogDescription>
                            Are you sure you want to mark <span class="font-bold text-foreground">{{ selectedIds.length }}</span> salary slips as PAID? 
                            This will record the transaction timestamp and mark your administration account as the processor.
                        </DialogDescription>
                    </DialogHeader>
                    <DialogFooter class="pt-4">
                        <Button variant="outline" @click="isBulkPaidModalOpen = false" :disabled="bulkForm.processing">Cancel</Button>
                        <Button variant="default" class="bg-emerald-600 hover:bg-emerald-700 text-white" @click="executeBulkPaid" :disabled="bulkForm.processing">
                            {{ bulkForm.processing ? 'Updating...' : 'Yes, Confirm Payment' }}
                        </Button>
                    </DialogFooter>
                </DialogContent>
            </Dialog>

            <!-- Delete Confirmation Modal -->
            <Dialog v-model:open="isSingleDeleteModalOpen">
                <DialogContent class="sm:max-w-[425px]">
                    <DialogHeader>
                        <DialogTitle class="text-destructive">Delete Salary Slip</DialogTitle>
                        <DialogDescription>
                            Are you absolutely sure you want to delete the draft salary slip for <span class="font-semibold text-foreground">{{ slipToDelete?.karyawan?.name }}</span>? This data is temporary but cannot be recovered once deleted.
                        </DialogDescription>
                    </DialogHeader>
                    <DialogFooter class="pt-2">
                        <Button variant="outline" @click="isSingleDeleteModalOpen = false" :disabled="form.processing">Cancel</Button>
                        <Button variant="destructive" @click="executeDeleteSlip" :disabled="form.processing">
                            {{ form.processing ? 'Deleting...' : 'Delete Permanently' }}
                        </Button>
                    </DialogFooter>
                </DialogContent>
            </Dialog>
        </div>
    </AppLayout>
</template>
