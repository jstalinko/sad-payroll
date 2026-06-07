<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, type Karyawan } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
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
import { Plus, Pencil, Trash, Search, UserPlus } from 'lucide-vue-next';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
    {
        title: 'Employees',
        href: '/karyawans',
    },
];

const props = defineProps<{
    karyawans: Karyawan[];
}>();

// Search & Filter
const searchQuery = ref('');

// Dialog states
const isDialogOpen = ref(false);
const isEditing = ref(false);
const editingId = ref<number | null>(null);
const isDeleteDialogOpen = ref(false);
const karyawanToDelete = ref<Karyawan | null>(null);

// Form configuration
const form = useForm({
    staff_id: '',
    name: '',
    position: '',
    salary: 0,
    bank_name: '',
    bank_account: '',
    bank_account_name: '',
    npwp: '',
    phone: '',
});

// Format Rupiah currency
const formatIDR = (value: number) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
    }).format(value);
};

// Open dialog for adding new employee
const openAddDialog = () => {
    form.clearErrors();
    form.reset();
    isEditing.value = false;
    editingId.value = null;
    isDialogOpen.value = true;
};

// Open dialog for editing employee
const openEditDialog = (karyawan: Karyawan) => {
    form.clearErrors();
    isEditing.value = true;
    editingId.value = karyawan.id;
    
    form.staff_id = karyawan.staff_id;
    form.name = karyawan.name;
    form.position = karyawan.position;
    form.salary = karyawan.salary;
    form.bank_name = karyawan.bank_name;
    form.bank_account = karyawan.bank_account;
    form.bank_account_name = karyawan.bank_account_name;
    form.npwp = karyawan.npwp || '';
    form.phone = karyawan.phone || '';
    
    isDialogOpen.value = true;
};

// Handle submit
const handleSubmit = () => {
    if (isEditing.value && editingId.value) {
        form.put(route('karyawans.update', editingId.value), {
            onSuccess: () => {
                isDialogOpen.value = false;
                form.reset();
            },
        });
    } else {
        form.post(route('karyawans.store'), {
            onSuccess: () => {
                isDialogOpen.value = false;
                form.reset();
            },
        });
    }
};

// Open delete dialog
const confirmDelete = (karyawan: Karyawan) => {
    karyawanToDelete.value = karyawan;
    isDeleteDialogOpen.value = true;
};

// Execute delete
const handleDelete = () => {
    if (karyawanToDelete.value) {
        form.delete(route('karyawans.destroy', karyawanToDelete.value.id), {
            onSuccess: () => {
                isDeleteDialogOpen.value = false;
                karyawanToDelete.value = null;
            },
        });
    }
};

// Filtered employee list
const getFilteredKaryawans = () => {
    if (!searchQuery.value) return props.karyawans;
    const q = searchQuery.value.toLowerCase();
    return props.karyawans.filter(k => 
        k.name.toLowerCase().includes(q) || 
        k.staff_id.toLowerCase().includes(q) || 
        k.position.toLowerCase().includes(q) ||
        k.bank_name.toLowerCase().includes(q)
    );
};
</script>

<template>
    <Head title="Employees" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-6">
            <!-- Header Section -->
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight">Employee Database</h1>
                    <p class="text-sm text-muted-foreground">Manage organizational payroll profiles, bank accounts, and salary details.</p>
                </div>
                <Button @click="openAddDialog" class="w-full sm:w-auto">
                    <Plus class="h-4 w-4 mr-2" /> Add Employee
                </Button>
            </div>

            <!-- Toolbar/Search Section -->
            <div class="flex items-center gap-2 rounded-lg border bg-card p-3 shadow-sm">
                <div class="relative flex-1 max-w-sm">
                    <Search class="absolute left-2.5 top-2.5 h-4 w-4 text-muted-foreground" />
                    <Input 
                        v-model="searchQuery" 
                        placeholder="Search by name, ID, or position..." 
                        class="pl-9 h-9"
                    />
                </div>
            </div>

            <!-- Main Table Card -->
            <div class="rounded-lg border bg-card shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left border-collapse">
                        <thead>
                            <tr class="border-b bg-muted/50 text-muted-foreground font-medium text-xs uppercase tracking-wider">
                                <th class="p-4">Staff ID</th>
                                <th class="p-4">Employee Name</th>
                                <th class="p-4">Position</th>
                                <th class="p-4">Basic Salary</th>
                                <th class="p-4">Bank Accounts Details</th>
                                <th class="p-4">NPWP</th>
                                <th class="p-4">Phone (WA)</th>
                                <th class="p-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            <tr v-if="getFilteredKaryawans().length === 0" class="hover:bg-muted/10">
                                <td colspan="8" class="p-8 text-center text-muted-foreground">
                                    <div class="flex flex-col items-center gap-2">
                                        <UserPlus class="h-8 w-8 text-muted-foreground/60" />
                                        <p class="font-medium">No employees found</p>
                                        <p class="text-xs">Add a new employee to get started.</p>
                                    </div>
                                </td>
                            </tr>
                            <tr v-for="k in getFilteredKaryawans()" :key="k.id" class="hover:bg-muted/30 transition-colors">
                                <td class="p-4 font-mono font-semibold">{{ k.staff_id }}</td>
                                <td class="p-4">
                                    <div class="font-medium text-foreground">{{ k.name }}</div>
                                </td>
                                <td class="p-4 text-muted-foreground">{{ k.position }}</td>
                                <td class="p-4 font-medium text-foreground">
                                    {{ formatIDR(k.salary) }}
                                </td>
                                <td class="p-4">
                                    <div class="text-xs">
                                        <span class="font-medium text-foreground">{{ k.bank_name }}</span> &bull; <span>{{ k.bank_account }}</span>
                                    </div>
                                    <div class="text-[10px] text-muted-foreground mt-0.5">
                                        a.n. {{ k.bank_account_name }}
                                    </div>
                                </td>
                                <td class="p-4 text-muted-foreground font-mono text-xs">{{ k.npwp || '-' }}</td>
                                <td class="p-4 text-muted-foreground font-mono text-xs">{{ k.phone || '-' }}</td>
                                <td class="p-4 text-right">
                                    <div class="flex justify-end gap-1.5">
                                        <Button @click="openEditDialog(k)" size="icon" variant="ghost" class="h-8 w-8 text-muted-foreground hover:text-foreground">
                                            <Pencil class="h-3.5 w-3.5" />
                                            <span class="sr-only">Edit</span>
                                        </Button>
                                        <Button @click="confirmDelete(k)" size="icon" variant="ghost" class="h-8 w-8 text-destructive hover:bg-destructive/10">
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

            <!-- Create/Edit Form Dialog -->
            <Dialog v-model:open="isDialogOpen">
                <DialogContent class="sm:max-w-[500px]">
                    <DialogHeader>
                        <DialogTitle>{{ isEditing ? 'Edit Employee Profile' : 'Add New Employee' }}</DialogTitle>
                        <DialogDescription>
                            {{ isEditing ? "Update employee's basic salary, position, and bank account information." : 'Register a new employee for monthly payroll distribution.' }}
                        </DialogDescription>
                    </DialogHeader>

                    <form @submit.prevent="handleSubmit" class="space-y-4 py-2">
                        <div class="grid grid-cols-2 gap-4">
                            <!-- Staff ID -->
                            <div class="space-y-1">
                                <Label for="staff_id">Staff ID</Label>
                                <Input id="staff_id" v-model="form.staff_id" placeholder="e.g. EMP001" :disabled="isEditing" required />
                                <span v-if="form.errors.staff_id" class="text-xs text-destructive font-medium">{{ form.errors.staff_id }}</span>
                            </div>

                            <!-- Name -->
                            <div class="space-y-1">
                                <Label for="name">Full Name</Label>
                                <Input id="name" v-model="form.name" placeholder="e.g. John Doe" required />
                                <span v-if="form.errors.name" class="text-xs text-destructive font-medium">{{ form.errors.name }}</span>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <!-- Position -->
                            <div class="space-y-1">
                                <Label for="position">Position</Label>
                                <Input id="position" v-model="form.position" placeholder="e.g. Developer" required />
                                <span v-if="form.errors.position" class="text-xs text-destructive font-medium">{{ form.errors.position }}</span>
                            </div>

                            <!-- Basic Salary -->
                            <div class="space-y-1">
                                <Label for="salary">Monthly Basic Salary (IDR)</Label>
                                <Input id="salary" type="number" v-model="form.salary" placeholder="5000000" min="0" required />
                                <span v-if="form.errors.salary" class="text-xs text-destructive font-medium">{{ form.errors.salary }}</span>
                            </div>
                        </div>

                        <div class="rounded-md border p-3 bg-muted/30 space-y-3">
                            <h3 class="text-xs font-semibold uppercase tracking-wider text-muted-foreground">Bank Account Details</h3>
                            
                            <div class="grid grid-cols-2 gap-3">
                                <!-- Bank Name -->
                                <div class="space-y-1">
                                    <Label for="bank_name">Bank Name</Label>
                                    <Input id="bank_name" v-model="form.bank_name" placeholder="e.g. BCA" required />
                                    <span v-if="form.errors.bank_name" class="text-xs text-destructive font-medium">{{ form.errors.bank_name }}</span>
                                </div>

                                <!-- Bank Account Number -->
                                <div class="space-y-1">
                                    <Label for="bank_account">Account Number</Label>
                                    <Input id="bank_account" v-model="form.bank_account" placeholder="e.g. 5220392019" required />
                                    <span v-if="form.errors.bank_account" class="text-xs text-destructive font-medium">{{ form.errors.bank_account }}</span>
                                </div>
                            </div>

                            <!-- Bank Account Holder Name -->
                            <div class="space-y-1">
                                <Label for="bank_account_name">Account Holder Name</Label>
                                <Input id="bank_account_name" v-model="form.bank_account_name" placeholder="e.g. John Doe" required />
                                <span v-if="form.errors.bank_account_name" class="text-xs text-destructive font-medium">{{ form.errors.bank_account_name }}</span>
                            </div>
                        </div>

                        <!-- NPWP & Phone -->
                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-1">
                                <Label for="npwp">NPWP (Optional)</Label>
                                <Input id="npwp" v-model="form.npwp" placeholder="e.g. 12.345.678.9-012.000" />
                                <span v-if="form.errors.npwp" class="text-xs text-destructive font-medium">{{ form.errors.npwp }}</span>
                            </div>
                            <div class="space-y-1">
                                <Label for="phone">Phone / WhatsApp</Label>
                                <Input id="phone" v-model="form.phone" placeholder="e.g. 6287857580910" />
                                <span v-if="form.errors.phone" class="text-xs text-destructive font-medium">{{ form.errors.phone }}</span>
                            </div>
                        </div>

                        <DialogFooter class="pt-4 border-t">
                            <Button type="button" variant="outline" @click="isDialogOpen = false" :disabled="form.processing">Cancel</Button>
                            <Button type="submit" :disabled="form.processing">
                                {{ form.processing ? 'Saving...' : 'Save Employee' }}
                            </Button>
                        </DialogFooter>
                    </form>
                </DialogContent>
            </Dialog>

            <!-- Delete Confirmation Dialog -->
            <Dialog v-model:open="isDeleteDialogOpen">
                <DialogContent class="sm:max-w-[425px]">
                    <DialogHeader>
                        <DialogTitle class="text-destructive">Delete Employee Record</DialogTitle>
                        <DialogDescription>
                            Are you absolutely sure you want to delete <span class="font-semibold text-foreground">{{ karyawanToDelete?.name }}</span> ({{ karyawanToDelete?.staff_id }})? This action will permanently remove the employee and all their salary history.
                        </DialogDescription>
                    </DialogHeader>
                    <DialogFooter class="pt-2">
                        <Button variant="outline" @click="isDeleteDialogOpen = false" :disabled="form.processing">Cancel</Button>
                        <Button variant="destructive" @click="handleDelete" :disabled="form.processing">
                            {{ form.processing ? 'Deleting...' : 'Delete Permanently' }}
                        </Button>
                    </DialogFooter>
                </DialogContent>
            </Dialog>
        </div>
    </AppLayout>
</template>
