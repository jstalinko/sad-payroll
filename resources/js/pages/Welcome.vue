<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ArrowRight, Lock, CheckCircle, FileText, Building2 } from 'lucide-vue-next';
</script>

<template>
    <Head title="Sinar Arta Payroll System" />
    
    <div class="relative min-h-screen flex flex-col justify-between overflow-hidden bg-slate-50 text-slate-900 dark:bg-zinc-950 dark:text-zinc-50 transition-colors duration-300">
        
        <!-- Premium Abstract Background Gradients -->
        <div class="absolute inset-0 pointer-events-none overflow-hidden select-none">
            <div class="absolute -top-[40%] -right-[10%] w-[800px] h-[800px] rounded-full bg-emerald-400/10 blur-[120px] dark:bg-emerald-500/5"></div>
            <div class="absolute -bottom-[30%] -left-[10%] w-[600px] h-[600px] rounded-full bg-blue-400/10 blur-[100px] dark:bg-blue-500/5"></div>
        </div>

        <!-- Header -->
        <header class="w-full max-w-7xl mx-auto px-6 py-6 flex items-center justify-between z-10 relative">
            <div class="flex items-center gap-3">
                <div class="bg-gradient-to-tr from-emerald-500 to-teal-400 p-2 rounded-xl text-white shadow-lg shadow-emerald-500/10">
                    <Building2 class="h-6 w-6" />
                </div>
                <div>
                    <span class="font-bold text-lg tracking-tight bg-gradient-to-r from-emerald-600 to-teal-500 dark:from-emerald-400 dark:to-teal-300 bg-clip-text text-transparent">
                        PT SINAR ARTA DIGITAL
                    </span>
                    <span class="block text-[10px] uppercase tracking-wider font-semibold text-muted-foreground">Payroll Hub</span>
                </div>
            </div>
            
            <nav class="flex items-center gap-4">
                <Link
                    v-if="$page.props.auth.user"
                    :href="route('dashboard')"
                    class="inline-flex items-center justify-center gap-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-medium text-sm px-5 py-2.5 shadow-md hover:shadow-lg transition-all"
                >
                    Dashboard <ArrowRight class="h-4 w-4" />
                </Link>
                <Link
                    v-else
                    :href="route('login')"
                    class="inline-flex items-center justify-center gap-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-medium text-sm px-5 py-2.5 shadow-md hover:shadow-lg transition-all"
                >
                    Log In <ArrowRight class="h-4 w-4" />
                </Link>
            </nav>
        </header>

        <!-- Main Hero Section -->
        <main class="flex-1 flex items-center justify-center px-6 py-12 z-10 relative">
            <div class="w-full max-w-4xl mx-auto grid md:grid-cols-2 gap-12 items-center">
                
                <!-- Left Content -->
                <div class="space-y-6 text-left">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-100 dark:bg-emerald-950/40 border border-emerald-200/50 dark:border-emerald-900/30 text-emerald-800 dark:text-emerald-300 text-xs font-semibold">
                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        Internal Employee Platform
                    </div>
                    
                    <h1 class="text-4xl md:text-5xl font-extrabold tracking-tight leading-tight">
                        Smart Payroll & <br />
                        <span class="bg-gradient-to-r from-emerald-600 to-teal-500 dark:from-emerald-400 dark:to-teal-300 bg-clip-text text-transparent">
                            WhatsApp Delivery
                        </span>
                    </h1>
                    
                    <p class="text-sm md:text-base text-slate-500 dark:text-zinc-400 leading-relaxed max-w-md">
                        Manage personnel database, calculate salary slips, and automate slip distribution directly to employee WhatsApp numbers securely.
                    </p>

                    <!-- Features bullets -->
                    <div class="space-y-3 pt-2">
                        <div class="flex items-start gap-3">
                            <div class="mt-1 bg-emerald-100 dark:bg-emerald-950/40 p-1 rounded-md text-emerald-600 dark:text-emerald-400">
                                <CheckCircle class="h-4 w-4" />
                            </div>
                            <div>
                                <h3 class="text-sm font-semibold">Automated Slip Distribution</h3>
                                <p class="text-xs text-slate-400 dark:text-zinc-500">Deliver PDF slips instantly to worker WhatsApp numbers upon payment.</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <div class="mt-1 bg-emerald-100 dark:bg-emerald-950/40 p-1 rounded-md text-emerald-600 dark:text-emerald-400">
                                <CheckCircle class="h-4 w-4" />
                            </div>
                            <div>
                                <h3 class="text-sm font-semibold">Interactive Expenditure Chart</h3>
                                <p class="text-xs text-slate-400 dark:text-zinc-500">Analyze and track historical outcomes monthly from January to December.</p>
                            </div>
                        </div>
                    </div>

                    <div class="pt-4 flex gap-4">
                        <Link
                            v-if="$page.props.auth.user"
                            :href="route('dashboard')"
                            class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-slate-900 text-white dark:bg-white dark:text-slate-900 font-semibold text-sm hover:opacity-90 transition-all shadow-md"
                        >
                            Enter Dashboard
                        </Link>
                        <Link
                            v-else
                            :href="route('login')"
                            class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-slate-900 text-white dark:bg-white dark:text-slate-900 font-semibold text-sm hover:opacity-90 transition-all shadow-md"
                        >
                            Log In to Dashboard <Lock class="h-4 w-4" />
                        </Link>
                    </div>
                </div>

                <!-- Right Artwork/Mockup -->
                <div class="relative flex justify-center items-center">
                    <div class="relative w-full max-w-[340px] aspect-[4/5] rounded-3xl border border-slate-200/50 bg-white/70 p-6 shadow-xl backdrop-blur-md dark:border-zinc-800/30 dark:bg-zinc-900/60 flex flex-col justify-between overflow-hidden">
                        
                        <!-- Top header -->
                        <div class="flex items-center justify-between border-b pb-4">
                            <div class="flex items-center gap-2">
                                <div class="h-3 w-3 rounded-full bg-red-400"></div>
                                <div class="h-3 w-3 rounded-full bg-yellow-400"></div>
                                <div class="h-3 w-3 rounded-full bg-green-400"></div>
                            </div>
                            <div class="text-[10px] font-mono text-slate-400 dark:text-zinc-500">payroll_system_v1</div>
                        </div>

                        <!-- Content Mockup -->
                        <div class="space-y-4 my-6 flex-1 flex flex-col justify-center">
                            <div class="space-y-2">
                                <div class="h-4 w-2/3 bg-slate-200 dark:bg-zinc-800 rounded-md"></div>
                                <div class="h-8 w-full bg-gradient-to-r from-emerald-500/20 to-teal-400/20 dark:from-emerald-500/10 dark:to-teal-400/10 border border-emerald-500/10 rounded-xl p-2 flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <FileText class="h-4 w-4 text-emerald-500" />
                                        <div class="h-2 w-20 bg-slate-300 dark:bg-zinc-700 rounded-md"></div>
                                    </div>
                                    <span class="text-[9px] font-bold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider">Paid</span>
                                </div>
                            </div>
                            <div class="space-y-2">
                                <div class="h-3.5 w-1/2 bg-slate-200 dark:bg-zinc-800 rounded-md"></div>
                                <div class="h-3.5 w-3/4 bg-slate-200 dark:bg-zinc-800 rounded-md"></div>
                            </div>
                        </div>

                        <!-- Footer indicator -->
                        <div class="border-t pt-4 text-center">
                            <span class="text-[10px] font-medium text-slate-400 dark:text-zinc-500">PT SINAR ARTA DIGITAL</span>
                        </div>
                    </div>
                </div>

            </div>
        </main>

        <!-- Footer -->
        <footer class="w-full max-w-7xl mx-auto px-6 py-6 flex flex-col sm:flex-row items-center justify-between text-xs text-slate-400 dark:text-zinc-500 border-t border-slate-200/50 dark:border-zinc-900/30 z-10 relative">
            <p>&copy; 2026 PT Sinar Arta Digital. All rights reserved.</p>
            <p class="mt-2 sm:mt-0">Internal Corporate Platform</p>
        </footer>

    </div>
</template>
