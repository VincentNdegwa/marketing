<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
</script>

<template>

    <Head title="Welcome">
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    </Head>
    <div
        class="flex min-h-screen flex-col items-center bg-[#FDFDFC] p-6 text-[#1b1b18] lg:justify-center lg:p-8 dark:bg-[#0a0a0a] ">
        <header class="mb-6 w-full max-w-[335px] text-sm not-has-[nav]:hidden lg:max-w-4xl z-2">
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
        <img src="/images/meeting.jpg" alt="SaaS background"
            class="absolute inset-0 w-full h-full object-cover opacity-10 -z-1" />
        <div
            class="flex w-full items-center justify-center opacity-100 transition-opacity duration-750 lg:grow starting:opacity-0">

            <main class="w-full max-w-4xl flex flex-col items-center gap-12 px-4 py-12 text-center relative">
                <div class="z-10 relative">
                    <h1 class="text-5xl sm:text-6xl font-bold tracking-tight text-[#1b1b18] dark:text-[#EDEDEC] mb-4">
                        The All-in-One Platform for Modern Teams
                    </h1>
                    <p class="text-xl sm:text-2xl text-[#706f6c] dark:text-[#A1A09A] max-w-2xl mx-auto mb-8">
                        Automate tasks, manage projects, and collaborate effortlessly — all in one powerful SaaS
                        platform
                        built for
                        productivity and growth.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <Link :href="route('register')"
                            class="inline-flex items-center justify-center px-10 py-4 rounded-full bg-[#f53003] text-white font-semibold border border-[#f53003] hover:bg-[#FF4433] hover:border-[#FF4433] dark:border-[#FF4433] dark:bg-[#FF4433]">
                        <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                        Try Free for 14 Days
                        </Link>
                        <Link :href="route('login')"
                            class="inline-flex items-center justify-center px-10 py-4 rounded-full font-semibold text-[#f53003] border border-[#f53003] hover:bg-[#f53003]/10 hover:text-white dark:border-[#FF4433] dark:text-[#FF4433] dark:hover:bg-[#FF4433]/10">
                        Log In
                        </Link>
                    </div>
                </div>
            </main>



        </div>
        <div class="hidden h-14.5 lg:block"></div>
    </div>
</template>
