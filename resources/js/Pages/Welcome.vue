<script setup>
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    canLogin: { type: Boolean },
    canRegister: { type: Boolean },
    laravelVersion: { type: String, required: true },
    phpVersion: { type: String, required: true },
});
</script>

<template>
    <Head title="BrainsBond007 — Workforce Monitoring Platform" />

    <div class="min-h-screen bg-gray-950 text-white overflow-hidden">
        <!-- Animated Background Gradient -->
        <div class="fixed inset-0 pointer-events-none">
            <div class="absolute -top-40 -right-40 w-96 h-96 bg-blue-600/20 rounded-full blur-3xl animate-pulse"></div>
            <div class="absolute top-1/3 -left-40 w-80 h-80 bg-violet-600/15 rounded-full blur-3xl animate-pulse" style="animation-delay: 2s;"></div>
            <div class="absolute bottom-20 right-1/4 w-72 h-72 bg-cyan-600/10 rounded-full blur-3xl animate-pulse" style="animation-delay: 4s;"></div>
        </div>

        <!-- Navigation -->
        <nav class="relative z-10 max-w-7xl mx-auto px-6 py-6 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-violet-600 flex items-center justify-center font-bold text-lg shadow-lg shadow-blue-500/25">
                    B
                </div>
                <span class="text-xl font-bold tracking-tight">BrainsBond<span class="text-blue-400">007</span></span>
            </div>

            <div v-if="canLogin" class="flex items-center gap-4">
                <Link v-if="$page.props.auth?.user" :href="route('dashboard')"
                    class="px-5 py-2.5 text-sm font-semibold text-white bg-blue-600 hover:bg-blue-500 rounded-xl transition-all shadow-lg shadow-blue-600/25 hover:shadow-blue-500/40">
                    Dashboard
                </Link>
                <template v-else>
                    <Link :href="route('login')"
                        class="px-5 py-2.5 text-sm font-medium text-gray-300 hover:text-white transition-colors">
                        Log in
                    </Link>
                    <Link v-if="canRegister" :href="route('register')"
                        class="px-5 py-2.5 text-sm font-semibold text-white bg-blue-600 hover:bg-blue-500 rounded-xl transition-all shadow-lg shadow-blue-600/25 hover:shadow-blue-500/40">
                        Get Started Free
                    </Link>
                </template>
            </div>
        </nav>

        <!-- Hero Section -->
        <section class="relative z-10 max-w-7xl mx-auto px-6 pt-16 pb-24 text-center">
            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-blue-500/10 border border-blue-500/20 text-blue-400 text-sm font-medium mb-8">
                <span class="w-2 h-2 rounded-full bg-blue-400 animate-pulse"></span>
                Now in Beta — Start monitoring for free
            </div>

            <h1 class="text-5xl sm:text-6xl lg:text-7xl font-extrabold tracking-tight leading-tight">
                Monitor Your<br>
                <span class="bg-gradient-to-r from-blue-400 via-violet-400 to-cyan-400 bg-clip-text text-transparent">
                    Workforce Smarter
                </span>
            </h1>

            <p class="mt-6 text-lg sm:text-xl text-gray-400 max-w-2xl mx-auto leading-relaxed">
                Face recognition. Geofencing. Live screen monitoring. Screenshot capture.
                BrainsBond007 gives you complete visibility into your remote and onsite teams — securely and effortlessly.
            </p>

            <div class="mt-10 flex flex-col sm:flex-row items-center justify-center gap-4">
                <Link v-if="canRegister" :href="route('register')"
                    class="px-8 py-3.5 text-base font-semibold text-white bg-gradient-to-r from-blue-600 to-violet-600 hover:from-blue-500 hover:to-violet-500 rounded-xl transition-all shadow-xl shadow-blue-600/25 hover:shadow-blue-500/40 hover:-translate-y-0.5">
                    Start Free Trial →
                </Link>
                <a href="#features"
                    class="px-8 py-3.5 text-base font-semibold text-gray-300 bg-white/5 hover:bg-white/10 border border-white/10 hover:border-white/20 rounded-xl transition-all">
                    See Features
                </a>
            </div>

            <!-- Stats Bar -->
            <div class="mt-20 grid grid-cols-2 sm:grid-cols-4 gap-8 max-w-3xl mx-auto">
                <div>
                    <p class="text-3xl font-bold text-white">99.9%</p>
                    <p class="text-sm text-gray-500 mt-1">Uptime SLA</p>
                </div>
                <div>
                    <p class="text-3xl font-bold text-white">256-bit</p>
                    <p class="text-sm text-gray-500 mt-1">Encryption</p>
                </div>
                <div>
                    <p class="text-3xl font-bold text-white">&lt;70ms</p>
                    <p class="text-sm text-gray-500 mt-1">Face Match</p>
                </div>
                <div>
                    <p class="text-3xl font-bold text-white">24/7</p>
                    <p class="text-sm text-gray-500 mt-1">Monitoring</p>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section id="features" class="relative z-10 max-w-7xl mx-auto px-6 py-24">
            <div class="text-center mb-16">
                <h2 class="text-3xl sm:text-4xl font-bold">Everything you need to<br><span class="text-blue-400">manage your workforce</span></h2>
                <p class="mt-4 text-gray-400 max-w-xl mx-auto">From biometric check-ins to live screen monitoring, BrainsBond007 covers every aspect of employee management.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Feature 1 -->
                <div class="group p-6 rounded-2xl bg-white/[0.03] border border-white/[0.06] hover:bg-white/[0.06] hover:border-white/[0.12] transition-all duration-300">
                    <div class="w-12 h-12 rounded-xl bg-blue-500/10 flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-white mb-2">Face Recognition</h3>
                    <p class="text-sm text-gray-400 leading-relaxed">Biometric face verification at login and checkout. Auto-detects if the wrong person is at the desk.</p>
                </div>

                <!-- Feature 2 -->
                <div class="group p-6 rounded-2xl bg-white/[0.03] border border-white/[0.06] hover:bg-white/[0.06] hover:border-white/[0.12] transition-all duration-300">
                    <div class="w-12 h-12 rounded-xl bg-emerald-500/10 flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-white mb-2">Location Geofencing</h3>
                    <p class="text-sm text-gray-400 leading-relaxed">Verify employees are working from approved locations. Supports multiple offices and remote hubs.</p>
                </div>

                <!-- Feature 3 -->
                <div class="group p-6 rounded-2xl bg-white/[0.03] border border-white/[0.06] hover:bg-white/[0.06] hover:border-white/[0.12] transition-all duration-300">
                    <div class="w-12 h-12 rounded-xl bg-violet-500/10 flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6 text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-white mb-2">Live Screen Casting</h3>
                    <p class="text-sm text-gray-400 leading-relaxed">View employee screens in real-time. Monitor activity and ensure task compliance instantly.</p>
                </div>

                <!-- Feature 4 -->
                <div class="group p-6 rounded-2xl bg-white/[0.03] border border-white/[0.06] hover:bg-white/[0.06] hover:border-white/[0.12] transition-all duration-300">
                    <div class="w-12 h-12 rounded-xl bg-amber-500/10 flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-white mb-2">Smart Screenshots</h3>
                    <p class="text-sm text-gray-400 leading-relaxed">Random screenshots at configurable intervals. Automatic webcam captures throughout the work session.</p>
                </div>

                <!-- Feature 5 -->
                <div class="group p-6 rounded-2xl bg-white/[0.03] border border-white/[0.06] hover:bg-white/[0.06] hover:border-white/[0.12] transition-all duration-300">
                    <div class="w-12 h-12 rounded-xl bg-cyan-500/10 flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-white mb-2">Attendance Tracking</h3>
                    <p class="text-sm text-gray-400 leading-relaxed">Automated attendance with face-verified clock-in/out. Daily, weekly, and monthly reports.</p>
                </div>

                <!-- Feature 6 -->
                <div class="group p-6 rounded-2xl bg-white/[0.03] border border-white/[0.06] hover:bg-white/[0.06] hover:border-white/[0.12] transition-all duration-300">
                    <div class="w-12 h-12 rounded-xl bg-red-500/10 flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-white mb-2">System Seize</h3>
                    <p class="text-sm text-gray-400 leading-relaxed">Remotely lock down an employee's system in case of suspicious activity or policy violations.</p>
                </div>
            </div>
        </section>

        <!-- Pricing Section -->
        <section id="pricing" class="relative z-10 max-w-7xl mx-auto px-6 py-24">
            <div class="text-center mb-16">
                <h2 class="text-3xl sm:text-4xl font-bold">Simple, transparent <span class="text-blue-400">pricing</span></h2>
                <p class="mt-4 text-gray-400">Start free. Upgrade as you grow.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 max-w-5xl mx-auto">
                <!-- Free -->
                <div class="p-6 rounded-2xl bg-white/[0.03] border border-white/[0.06] text-center">
                    <h3 class="text-lg font-semibold text-white">Free</h3>
                    <p class="text-4xl font-bold text-white mt-4">$0</p>
                    <p class="text-sm text-gray-500 mt-1">per month</p>
                    <ul class="mt-6 space-y-3 text-sm text-gray-400 text-left">
                        <li class="flex items-center gap-2"><svg class="w-4 h-4 text-emerald-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>3 employees</li>
                        <li class="flex items-center gap-2"><svg class="w-4 h-4 text-emerald-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>Screenshots</li>
                        <li class="flex items-center gap-2"><svg class="w-4 h-4 text-emerald-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>Attendance</li>
                    </ul>
                    <Link v-if="canRegister" :href="route('register')"
                        class="mt-8 block w-full py-2.5 text-sm font-semibold text-white bg-white/10 hover:bg-white/20 rounded-xl transition-colors">
                        Get Started
                    </Link>
                </div>

                <!-- Starter -->
                <div class="p-6 rounded-2xl bg-white/[0.03] border border-white/[0.06] text-center">
                    <h3 class="text-lg font-semibold text-white">Starter</h3>
                    <p class="text-4xl font-bold text-white mt-4">$29</p>
                    <p class="text-sm text-gray-500 mt-1">per month</p>
                    <ul class="mt-6 space-y-3 text-sm text-gray-400 text-left">
                        <li class="flex items-center gap-2"><svg class="w-4 h-4 text-emerald-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>15 employees</li>
                        <li class="flex items-center gap-2"><svg class="w-4 h-4 text-emerald-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>Screenshots + Webcam</li>
                        <li class="flex items-center gap-2"><svg class="w-4 h-4 text-emerald-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>Email support</li>
                    </ul>
                    <button class="mt-8 block w-full py-2.5 text-sm font-semibold text-white bg-white/10 hover:bg-white/20 rounded-xl transition-colors">
                        Choose Starter
                    </button>
                </div>

                <!-- Pro (Highlighted) -->
                <div class="p-6 rounded-2xl bg-gradient-to-b from-blue-600/20 to-violet-600/10 border-2 border-blue-500/30 text-center relative">
                    <div class="absolute -top-3 left-1/2 -translate-x-1/2 px-4 py-1 bg-gradient-to-r from-blue-500 to-violet-500 text-white text-xs font-bold rounded-full shadow-lg">
                        MOST POPULAR
                    </div>
                    <h3 class="text-lg font-semibold text-white">Pro</h3>
                    <p class="text-4xl font-bold text-white mt-4">$79</p>
                    <p class="text-sm text-gray-400 mt-1">per month</p>
                    <ul class="mt-6 space-y-3 text-sm text-gray-300 text-left">
                        <li class="flex items-center gap-2"><svg class="w-4 h-4 text-emerald-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>50 employees</li>
                        <li class="flex items-center gap-2"><svg class="w-4 h-4 text-emerald-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>Live monitoring</li>
                        <li class="flex items-center gap-2"><svg class="w-4 h-4 text-emerald-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>Face recognition</li>
                        <li class="flex items-center gap-2"><svg class="w-4 h-4 text-emerald-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>System seize</li>
                    </ul>
                    <button class="mt-8 block w-full py-2.5 text-sm font-semibold text-white bg-gradient-to-r from-blue-600 to-violet-600 hover:from-blue-500 hover:to-violet-500 rounded-xl transition-all shadow-lg shadow-blue-600/25">
                        Choose Pro
                    </button>
                </div>

                <!-- Enterprise -->
                <div class="p-6 rounded-2xl bg-white/[0.03] border border-white/[0.06] text-center">
                    <h3 class="text-lg font-semibold text-white">Enterprise</h3>
                    <p class="text-4xl font-bold text-white mt-4">$199</p>
                    <p class="text-sm text-gray-500 mt-1">per month</p>
                    <ul class="mt-6 space-y-3 text-sm text-gray-400 text-left">
                        <li class="flex items-center gap-2"><svg class="w-4 h-4 text-emerald-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>Unlimited employees</li>
                        <li class="flex items-center gap-2"><svg class="w-4 h-4 text-emerald-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>Full API access</li>
                        <li class="flex items-center gap-2"><svg class="w-4 h-4 text-emerald-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>Custom branding</li>
                        <li class="flex items-center gap-2"><svg class="w-4 h-4 text-emerald-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>Priority support</li>
                    </ul>
                    <button class="mt-8 block w-full py-2.5 text-sm font-semibold text-white bg-white/10 hover:bg-white/20 rounded-xl transition-colors">
                        Contact Sales
                    </button>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="relative z-10 max-w-4xl mx-auto px-6 py-24 text-center">
            <div class="p-12 rounded-3xl bg-gradient-to-r from-blue-600/20 via-violet-600/20 to-cyan-600/20 border border-white/10">
                <h2 class="text-3xl sm:text-4xl font-bold">Ready to take control of your workforce?</h2>
                <p class="mt-4 text-gray-400 max-w-lg mx-auto">Join organizations using BrainsBond007 to monitor, verify, and manage their employees with cutting-edge technology.</p>
                <div class="mt-8 flex flex-col sm:flex-row items-center justify-center gap-4">
                    <Link v-if="canRegister" :href="route('register')"
                        class="px-8 py-3.5 text-base font-semibold text-white bg-gradient-to-r from-blue-600 to-violet-600 hover:from-blue-500 hover:to-violet-500 rounded-xl transition-all shadow-xl shadow-blue-600/25">
                        Start Free Trial →
                    </Link>
                    <Link v-if="canLogin" :href="route('login')"
                        class="px-8 py-3.5 text-base font-medium text-gray-300 hover:text-white transition-colors">
                        Already have an account? Log in
                    </Link>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="relative z-10 border-t border-white/[0.06] py-10">
            <div class="max-w-7xl mx-auto px-6 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-2">
                    <div class="w-7 h-7 rounded-lg bg-gradient-to-br from-blue-500 to-violet-600 flex items-center justify-center font-bold text-xs">B</div>
                    <span class="text-sm font-semibold">BrainsBond007</span>
                </div>
                <p class="text-sm text-gray-500">&copy; {{ new Date().getFullYear() }} BrainsBond007. All rights reserved.</p>
            </div>
        </footer>
    </div>
</template>
