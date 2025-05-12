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
                <!-- Hero Section -->
                <div class="relative">
                    <div class="absolute inset-0">
                        <img src="/images/meeting.jpg" alt="Business meeting" class="w-full h-full object-cover opacity-20">
                    </div>
                    <div class="relative text-center">
                        <h1 class="text-6xl font-bold leading-tight tracking-tight text-[#1b1b18] dark:text-[#EDEDEC] mb-4">
                            Business Growth Simplified
                        </h1>
                        <p class="mt-6 text-2xl text-[#706f6c] dark:text-[#A1A09A] max-w-2xl mx-auto mb-8">
                            Transform your business with our comprehensive suite of tools designed to streamline operations, boost productivity, and drive growth
                        </p>
                        <div class="flex flex-col sm:flex-row gap-4 justify-center">
                            <Link :href="route('register')"
                                class="inline-flex items-center justify-center rounded-full border border-[#f53003] bg-[#f53003] px-10 py-4 text-base font-semibold text-white transition-all duration-300 hover:bg-[#FF4433] hover:border-[#FF4433] dark:border-[#FF4433] dark:bg-[#FF4433]">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                                Start Free Trial
                            </Link>
                            <Link :href="route('login')"
                                class="inline-flex items-center justify-center rounded-full border border-[#f53003] px-10 py-4 text-base font-semibold text-[#f53003] transition-all duration-300 hover:bg-[#f53003]/10 hover:text-white dark:border-[#FF4433] dark:text-[#FF4433] dark:hover:bg-[#FF4433]/10">
                                Login to Your Account
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- Business Solutions Section -->
                <div class="grid w-full grid-cols-1 gap-12 md:grid-cols-2">
                    <!-- Solution 1 -->
                    <div class="bg-white dark:bg-[#161615] rounded-2xl p-8 shadow-lg">
                        <div class="flex items-center gap-4 mb-6">
                            <div class="p-4 bg-[#f53003]/10 dark:bg-[#FF4433]/10 rounded-xl">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-[#f53003] dark:text-[#FF4433]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <h2 class="text-2xl font-bold text-[#1b1b18] dark:text-[#EDEDEC]">Business Management</h2>
                        </div>
                        <p class="text-lg text-[#706f6c] dark:text-[#A1A09A] mb-6">
                            Streamline your business operations with our comprehensive management tools. From customer relationship management to inventory control, we've got you covered.
                        </p>
                        <ul class="space-y-3 text-[#706f6c] dark:text-[#A1A09A]">
                            <li class="flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#f53003] dark:text-[#FF4433]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                CRM Integration
                            </li>
                            <li class="flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#f53003] dark:text-[#FF4433]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Inventory Management
                            </li>
                            <li class="flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#f53003] dark:text-[#FF4433]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Sales Analytics
                            </li>
                        </ul>
                    </div>

                    <!-- Solution 2 -->
                    <div class="bg-white dark:bg-[#161615] rounded-2xl p-8 shadow-lg">
                        <div class="flex items-center gap-4 mb-6">
                            <div class="p-4 bg-[#f53003]/10 dark:bg-[#FF4433]/10 rounded-xl">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-[#f53003] dark:text-[#FF4433]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                            </div>
                            <h2 class="text-2xl font-bold text-[#1b1b18] dark:text-[#EDEDEC]">Marketing & Growth</h2>
                        </div>
                        <p class="text-lg text-[#706f6c] dark:text-[#A1A09A] mb-6">
                            Boost your business growth with our advanced marketing tools. Reach more customers and convert leads into loyal clients.
                        </p>
                        <ul class="space-y-3 text-[#706f6c] dark:text-[#A1A09A]">
                            <li class="flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#f53003] dark:text-[#FF4433]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Social Media Management
                            </li>
                            <li class="flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#f53003] dark:text-[#FF4433]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Email Marketing
                            </li>
                            <li class="flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#f53003] dark:text-[#FF4433]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                SEO Optimization
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Benefits Section -->
                <div class="bg-[#f53003]/5 dark:bg-[#FF4433]/5 rounded-2xl p-8">
                    <h2 class="text-3xl font-bold text-center mb-8 text-[#1b1b18] dark:text-[#EDEDEC]">Why Choose Us</h2>
                    <div class="grid w-full grid-cols-1 gap-8 md:grid-cols-3">
                        <div class="text-center">
                            <div class="p-4 bg-[#f53003]/10 dark:bg-[#FF4433]/10 rounded-xl mb-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-[#f53003] dark:text-[#FF4433]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold mb-2">All-in-One Solution</h3>
                            <p class="text-[#706f6c] dark:text-[#A1A09A]">Access all business tools in one integrated platform</p>
                        </div>
                        <div class="text-center">
                            <div class="p-4 bg-[#f53003]/10 dark:bg-[#FF4433]/10 rounded-xl mb-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-[#f53003] dark:text-[#FF4433]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold mb-2">Scalable Growth</h3>
                            <p class="text-[#706f6c] dark:text-[#A1A09A]">Tools that grow with your business, from startup to enterprise</p>
                        </div>
                        <div class="text-center">
                            <div class="p-4 bg-[#f53003]/10 dark:bg-[#FF4433]/10 rounded-xl mb-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-[#f53003] dark:text-[#FF4433]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold mb-2">24/7 Support</h3>
                            <p class="text-[#706f6c] dark:text-[#A1A09A]">Dedicated support team available round the clock</p>
                        </div>
                    </div>
                </div>

                <!-- CTA Section -->
                <div class="bg-gradient-to-r from-[#f53003]/10 to-[#FF4433]/10 dark:from-[#FF4433]/10 dark:to-[#f53003]/10 rounded-2xl p-8 text-center">
                    <h2 class="text-3xl font-bold mb-4 text-[#1b1b18] dark:text-[#EDEDEC]">Ready to Transform Your Business?</h2>
                    <p class="text-lg text-[#706f6c] dark:text-[#A1A09A] mb-8">
                        Join thousands of successful businesses already using our platform to grow and thrive
                    </p>
                    <Link :href="route('register')"
                        class="inline-flex items-center justify-center rounded-full border border-[#f53003] bg-[#f53003] px-12 py-4 text-lg font-semibold text-white transition-all duration-300 hover:bg-[#FF4433] hover:border-[#FF4433] dark:border-[#FF4433] dark:bg-[#FF4433]">
                        Start Your Free Trial
                    </Link>
                </div>
            </main>



            
        </div>
        <div class="hidden h-14.5 lg:block"></div>
    </div>
</template>
