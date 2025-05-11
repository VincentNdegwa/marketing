<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
</script>

<template>

    <Head title="Welcome">
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    </Head>
    <div
        class="flex min-h-screen flex-col items-center bg-[#FDFDFC] p-6 text-[#1b1b18] lg:justify-center lg:p-8 dark:bg-[#0a0a0a]">
        <header class="mb-6 w-full max-w-[335px] text-sm not-has-[nav]:hidden lg:max-w-4xl">
            <nav class="flex items-center justify-end gap-4">
                <Link v-if="$page.props.auth.user" :href="route('dashboard')"
                    class="inline-block rounded-sm border border-[#19140035] px-5 py-1.5 text-sm leading-normal text-[#1b1b18] hover:border-[#1915014a] dark:border-[#3E3E3A] dark:text-[#EDEDEC] dark:hover:border-[#62605b]">
                Dashboard
                </Link>
                <template v-else>
                    <Link :href="route('login')"
                        class="inline-block rounded-sm border border-transparent px-5 py-1.5 text-sm leading-normal text-[#1b1b18] hover:border-[#19140035] dark:text-[#EDEDEC] dark:hover:border-[#3E3E3A]">
                    Log in
                    </Link>
                    <Link :href="route('register')"
                        class="inline-block rounded-sm border border-[#19140035] px-5 py-1.5 text-sm leading-normal text-[#1b1b18] hover:border-[#1915014a] dark:border-[#3E3E3A] dark:text-[#EDEDEC] dark:hover:border-[#62605b]">
                    Register
                    </Link>
                </template>
            </nav>
        </header>

        <div
            class="flex w-full items-center justify-center opacity-100 transition-opacity duration-750 lg:grow starting:opacity-0">
            <main class="flex w-full max-w-4xl flex-col items-center justify-center gap-12 px-4 py-12">
                <div class="text-center">
                    <h1 class="text-5xl font-bold leading-tight tracking-tight text-[#1b1b18] dark:text-[#EDEDEC]">
                        Welcome to Our Marketing Platform
                    </h1>
                    <p class="mt-6 text-xl text-[#706f6c] dark:text-[#A1A09A] max-w-2xl mx-auto">
                        Transform your marketing strategy with our powerful tools and insights
                    </p>
                </div>

                <div class="grid w-full grid-cols-1 gap-10 md:grid-cols-2 lg:grid-cols-3">
                    <div
                        class="group relative overflow-hidden rounded-xl border border-[#e3e3e0] bg-white p-8 shadow-sm transition-all duration-300 hover:shadow-lg dark:border-[#3E3E3A] dark:bg-[#161615]">
                        <div class="absolute inset-0 bg-gradient-to-r from-transparent via-[#f53003]/10 to-transparent opacity-0 transition-opacity duration-300 group-hover:opacity-100 dark:via-[#FF4433]/10"></div>
                        <h3 class="relative z-10 text-2xl font-bold text-[#1b1b18] dark:text-[#EDEDEC]">
                            Campaign Management
                        </h3>
                        <p class="mt-4 relative z-10 text-lg text-[#706f6c] dark:text-[#A1A09A]">
                            Create, track, and optimize your marketing campaigns across multiple channels
                        </p>
                    </div>

                    <div
                        class="group relative overflow-hidden rounded-xl border border-[#e3e3e0] bg-white p-8 shadow-sm transition-all duration-300 hover:shadow-lg dark:border-[#3E3E3A] dark:bg-[#161615]">
                        <div class="absolute inset-0 bg-gradient-to-r from-transparent via-[#f53003]/10 to-transparent opacity-0 transition-opacity duration-300 group-hover:opacity-100 dark:via-[#FF4433]/10"></div>
                        <h3 class="relative z-10 text-2xl font-bold text-[#1b1b18] dark:text-[#EDEDEC]">
                            Analytics & Insights
                        </h3>
                        <p class="mt-4 relative z-10 text-lg text-[#706f6c] dark:text-[#A1A09A]">
                            Get detailed analytics and actionable insights to improve your marketing performance
                        </p>
                    </div>

                    <div
                        class="group relative overflow-hidden rounded-xl border border-[#e3e3e0] bg-white p-8 shadow-sm transition-all duration-300 hover:shadow-lg dark:border-[#3E3E3A] dark:bg-[#161615]">
                        <div class="absolute inset-0 bg-gradient-to-r from-transparent via-[#f53003]/10 to-transparent opacity-0 transition-opacity duration-300 group-hover:opacity-100 dark:via-[#FF4433]/10"></div>
                        <h3 class="relative z-10 text-2xl font-bold text-[#1b1b18] dark:text-[#EDEDEC]">
                            Automation Tools
                        </h3>
                        <p class="mt-4 relative z-10 text-lg text-[#706f6c] dark:text-[#A1A09A]">
                            Automate your marketing workflows and save time with our powerful automation features
                        </p>
                    </div>
                </div>

                <div class="mt-12 text-center">
                    <Link :href="route('register')"
                        class="inline-flex items-center justify-center rounded-full border border-[#f53003] bg-[#f53003] px-10 py-4 text-base font-semibold text-white transition-all duration-300 hover:bg-[#FF4433] hover:border-[#FF4433] dark:border-[#FF4433] dark:bg-[#FF4433]">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                        Get Started
                    </Link>
                </div>
            </main>
        </div>
        <div class="hidden h-14.5 lg:block"></div>
    </div>
</template>
