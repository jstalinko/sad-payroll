<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem, type Slip } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { 
    ArrowLeft, FileDown, CheckSquare, Search, 
    Calendar, User, CreditCard 
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
    {
        title: 'Paid List',
        href: '/slips/paid-list',
    },
];

const props = defineProps<{
    slips: Slip[];
}>();

const searchQuery = ref('');

// Format Rupiah currency
const formatIDR = (value: number) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
    }).format(value);
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

// Format Timestamp to human readable date-time
const formatDateTime = (dateTimeStr: string | null) => {
    if (!dateTimeStr) return '-';
    try {
        const date = new Date(dateTimeStr);
        return date.toLocaleDateString('id-ID', {
            day: '2-digit',
            month: 'short',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    } catch (e) {
        return dateTimeStr;
    }
};

// Filtered paid slips
const getFilteredSlips = () => {
    if (!searchQuery.value) return props.slips;
    const q = searchQuery.value.toLowerCase();
    return props.slips.filter(s => 
        s.karyawan?.name.toLowerCase().includes(q) || 
        s.karyawan?.staff_id.toLowerCase().includes(q) || 
        (s.paid_by && s.paid_by.toLowerCase().includes(q))
    );
};
</script>

<template>
    <Head title="Paid Salary Slips" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-6">
            <!-- Header Section -->
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center gap-3">
                    <Link :href="route('slips.index')">
                        <Button variant="ghost" size="icon" class="h-9 w-9 border">
                            <ArrowLeft class="h-4 w-4" />
                        </Button>
                    </Link>
                    <div>
                        <h1 class="text-2xl font-bold tracking-tight">Paid Salaries History</h1>
                        <p class="text-sm text-muted-foreground">Historical view of all processed and disbursed payroll salary slips.</p>
                    </div>
                </div>
                <div>
                    <a :href="route('slips.downloadPaidPdf')" target="_blank">
                        <Button class="bg-emerald-600 hover:bg-emerald-700 text-white w-full sm:w-auto">
                            <FileDown class="h-4 w-4 mr-2" /> Download Paid Data (PDF)
                        </Button>
                    </a>
                </div>
            </div>

            <!-- Toolbar/Search Section -->
            <div class="flex items-center gap-2 rounded-lg border bg-card p-3 shadow-sm">
                <div class="relative flex-1 max-w-sm">
                    <Search class="absolute left-2.5 top-2.5 h-4 w-4 text-muted-foreground" />
                    <Input 
                        v-model="searchQuery" 
                        placeholder="Search by employee name, ID or processor..." 
                        class="pl-9 h-9"
                    />
                </div>
            </div>

            <!-- Paid Slips Table -->
            <div class="rounded-lg border bg-card shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left border-collapse">
                        <thead>
                            <tr class="border-b bg-muted/50 text-muted-foreground font-medium text-xs uppercase tracking-wider">
                                <th class="p-4">Employee</th>
                                <th class="p-4">Period</th>
                                <th class="p-4">Base Payout</th>
                                <th class="p-4">Allowances</th>
                                <th class="p-4">Deductions</th>
                                <th class="p-4">Net Payout</th>
                                <th class="p-4">Payment Info</th>
                                <th class="p-4">Status</th>
                                <th class="p-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            <tr v-if="getFilteredSlips().length === 0" class="hover:bg-muted/10">
                                <td colspan="9" class="p-8 text-center text-muted-foreground">
                                    <div class="flex flex-col items-center gap-2">
                                        <CheckSquare class="h-8 w-8 text-muted-foreground/60" />
                                        <p class="font-medium">No paid records found</p>
                                        <p class="text-xs">No payroll slips have been marked as paid yet.</p>
                                    </div>
                                </td>
                            </tr>
                            <tr v-for="s in getFilteredSlips()" :key="s.id" class="hover:bg-muted/30 transition-colors">
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
                                    {{ formatIDR(s.basic_salary) }}
                                </td>
                                <td class="p-4 text-emerald-600 dark:text-emerald-500 font-medium">
                                    +{{ formatIDR(Number(s.overtime_salary) + Number(s.meal_allowance) + Number(s.transportation_allowance) + Number(s.bonus_salary)) }}
                                </td>
                                <td class="p-4 text-rose-600 dark:text-rose-500 font-medium">
                                    -{{ formatIDR(s.total_deduction) }}
                                </td>
                                <td class="p-4 font-semibold text-emerald-600 dark:text-emerald-500">
                                    {{ formatIDR(s.total_salary) }}
                                </td>
                                <td class="p-4">
                                    <div class="flex items-center gap-1 text-xs text-foreground">
                                        <Calendar class="h-3 w-3 text-muted-foreground" />
                                        <span>{{ formatDateTime(s.paid_at) }}</span>
                                    </div>
                                    <div class="flex items-center gap-1 text-[10px] text-muted-foreground mt-0.5">
                                        <User class="h-2.5 w-2.5" />
                                        <span>Processed by: {{ s.paid_by || 'System' }}</span>
                                    </div>
                                </td>
                                <td class="p-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 dark:bg-emerald-950/40 dark:text-emerald-400 border border-emerald-200/50">
                                        {{ s.status }}
                                    </span>
                                </td>
                                <td class="p-4 text-right">
                                    <a :href="route('slips.downloadIndividualPdf', s.id)" target="_blank">
                                        <Button size="sm" variant="outline" class="h-8 text-xs bg-card hover:bg-muted text-foreground">
                                            <FileDown class="h-3.5 w-3.5 mr-1" /> PDF
                                        </Button>
                                    </a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
