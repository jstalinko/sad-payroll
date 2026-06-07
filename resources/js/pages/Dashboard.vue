<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Users, CreditCard, Clock, CheckSquare } from 'lucide-vue-next';

import { ref, computed } from 'vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
];

interface MonthlyOutcome {
    period: string;
    period_name: string;
    total: number;
    count: number;
}

interface ChartData {
    month: string;
    total: number;
}

interface Stats {
    totalOutcome: number;
    totalKaryawans: number;
    monthlyOutcome: MonthlyOutcome[];
    draftCount: number;
    draftSum: number;
    chartYear: number;
    chartData: ChartData[];
}

const props = defineProps<{
    stats: Stats;
}>();

const formatIDR = (value: number) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
    }).format(value);
};

const hoveredBar = ref<number | null>(null);

const maxTotal = computed(() => {
    const vals = props.stats.chartData.map(d => d.total);
    return Math.max(...vals, 1000000);
});

const chartBars = computed(() => {
    const max = maxTotal.value;
    const width = 500;
    const height = 150;
    const barWidth = 22;
    const gap = (width - (barWidth * 12)) / 13;
    
    return props.stats.chartData.map((d, i) => {
        const barHeight = max > 0 ? (d.total / max) * height : 0;
        const x = gap + i * (barWidth + gap);
        const y = height - barHeight;
        return {
            month: d.month,
            total: d.total,
            x,
            y,
            w: barWidth,
            h: barHeight,
        };
    });
});
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-6">
            <div>
                <h1 class="text-2xl font-bold tracking-tight">Payroll Overview</h1>
                <p class="text-sm text-muted-foreground">Monitor outcome, employees status, and historical payroll statistics.</p>
            </div>

            <!-- Stats Cards Grid -->
            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                <!-- Total Employees -->
                <Card class="relative overflow-hidden transition-all hover:shadow-md">
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Total Employees</CardTitle>
                        <Users class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ stats.totalKaryawans }}</div>
                        <p class="text-xs text-muted-foreground">Active personnel in system</p>
                    </CardContent>
                </Card>

                <!-- Total Paid Outcome -->
                <Card class="relative overflow-hidden transition-all hover:shadow-md">
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Total Paid Outcome</CardTitle>
                        <CheckSquare class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold text-emerald-600 dark:text-emerald-500">
                            {{ formatIDR(stats.totalOutcome) }}
                        </div>
                        <p class="text-xs text-muted-foreground">Cumulative processed payouts</p>
                    </CardContent>
                </Card>

                <!-- Draft Slips -->
                <Card class="relative overflow-hidden transition-all hover:shadow-md">
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Pending Draft Slips</CardTitle>
                        <Clock class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold text-blue-600 dark:text-blue-500">{{ stats.draftCount }}</div>
                        <p class="text-xs text-muted-foreground">Awaiting validation and approval</p>
                    </CardContent>
                </Card>

                <!-- Draft Sum -->
                <Card class="relative overflow-hidden transition-all hover:shadow-md">
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Draft Liability</CardTitle>
                        <CreditCard class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold text-amber-600 dark:text-amber-500">
                            {{ formatIDR(stats.draftSum) }}
                        </div>
                        <p class="text-xs text-muted-foreground">Expected payout for active drafts</p>
                    </CardContent>
                </Card>
            </div>

            <!-- Monthly Outcome Section -->
            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-7">
                <Card class="col-span-4 transition-all">
                    <CardHeader>
                        <CardTitle>Monthly Salary Outcome</CardTitle>
                        <p class="text-xs text-muted-foreground">Historical salary payouts processed grouped by month.</p>
                    </CardHeader>
                    <CardContent>
                        <div v-if="stats.monthlyOutcome.length === 0" class="flex h-[200px] items-center justify-center text-sm text-muted-foreground">
                            No paid transactions recorded yet.
                        </div>
                        <div v-else class="space-y-4">
                            <div v-for="item in stats.monthlyOutcome" :key="item.period" class="flex items-center justify-between border-b pb-3 last:border-0 last:pb-0">
                                <div class="space-y-1">
                                    <p class="text-sm font-medium leading-none">{{ item.period_name }}</p>
                                    <p class="text-xs text-muted-foreground">{{ item.count }} slips paid</p>
                                </div>
                                <div class="font-semibold text-emerald-600 dark:text-emerald-500">
                                    {{ formatIDR(item.total) }}
                                </div>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <Card class="col-span-3 transition-all">
                    <CardHeader class="pb-2">
                        <CardTitle>Expenditure Chart ({{ stats.chartYear }})</CardTitle>
                        <p class="text-xs text-muted-foreground">Monthly payroll expenditure from Jan to Dec.</p>
                    </CardHeader>
                    <CardContent class="relative pt-0">
                        <div class="relative w-full h-[180px] mt-4 select-none">
                            <svg class="w-full h-full overflow-visible" viewBox="0 0 500 180">
                                <!-- Grid lines -->
                                <g class="stroke-muted/40" stroke-width="1">
                                    <line x1="0" y1="15" x2="500" y2="15" stroke-dasharray="3" />
                                    <line x1="0" y1="65" x2="500" y2="65" stroke-dasharray="3" />
                                    <line x1="0" y1="115" x2="500" y2="115" stroke-dasharray="3" />
                                    <line x1="0" y1="150" x2="500" y2="150" />
                                </g>
                                
                                <!-- Bars -->
                                <g>
                                    <rect 
                                        v-for="(bar, i) in chartBars" 
                                        :key="bar.month"
                                        :x="bar.x" 
                                        :y="bar.y" 
                                        :width="bar.w" 
                                        :height="bar.h" 
                                        rx="3" 
                                        class="fill-primary/85 hover:fill-primary transition-all duration-300 cursor-pointer"
                                        @mouseenter="hoveredBar = i"
                                        @mouseleave="hoveredBar = null"
                                    />
                                </g>

                                <!-- X-axis Text labels -->
                                <g>
                                    <text 
                                        v-for="bar in chartBars" 
                                        :key="bar.month"
                                        :x="bar.x + bar.w / 2" 
                                        y="170" 
                                        text-anchor="middle" 
                                        class="text-[10px] fill-muted-foreground font-medium select-none pointer-events-none"
                                    >
                                        {{ bar.month }}
                                    </text>
                                </g>
                            </svg>

                            <!-- HTML Tooltip -->
                            <div 
                                v-if="hoveredBar !== null"
                                class="absolute z-10 rounded-md border bg-popover px-3 py-1.5 text-xs text-popover-foreground shadow-md transition-all duration-150 pointer-events-none"
                                :style="{
                                    left: `${(chartBars[hoveredBar].x / 500) * 100}%`,
                                    bottom: `${((150 - chartBars[hoveredBar].h + 15) / 180) * 100}%`,
                                    transform: 'translateX(-35%)'
                                }"
                            >
                                <div class="font-semibold text-[10px] uppercase text-muted-foreground">{{ chartBars[hoveredBar].month }} Outcome</div>
                                <div class="text-emerald-600 dark:text-emerald-400 font-bold mt-0.5 text-xs">{{ formatIDR(chartBars[hoveredBar].total) }}</div>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
