<script setup>
import { ref } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { usePage } from '@inertiajs/vue3';

const page = usePage();
const user = page.props.auth?.user;

const orgName = ref(user?.organization_name || '');
const timezone = ref(user?.timezone || 'UTC');
const saving = ref(false);
const saved = ref(false);
</script>

<template>
    <AppLayout title="Settings">
        <template #header>
            <h2 class="font-bold text-2xl text-gray-800 dark:text-gray-100">Settings</h2>
        </template>

        <div class="py-8">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
                <!-- Organization Settings -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-5">Organization</h3>
                    <div class="space-y-5">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Organization Name</label>
                            <input v-model="orgName" type="text"
                                class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 dark:text-white"
                                placeholder="Your Company Name" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Timezone</label>
                            <select v-model="timezone"
                                class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 dark:text-white">
                                <option value="UTC">UTC</option>
                                <option value="Asia/Karachi">Asia/Karachi (PKT)</option>
                                <option value="America/New_York">America/New_York (EST)</option>
                                <option value="Europe/London">Europe/London (GMT)</option>
                                <option value="Asia/Dubai">Asia/Dubai (GST)</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Subscription Plan -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-5">Subscription Plan</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <!-- Free -->
                        <div class="border-2 border-gray-200 dark:border-gray-600 rounded-xl p-5 text-center relative"
                            :class="{ 'border-blue-500 dark:border-blue-400 bg-blue-50/50 dark:bg-blue-900/10': !user?.plan_type || user?.plan_type === 'free' }">
                            <div v-if="!user?.plan_type || user?.plan_type === 'free'" class="absolute -top-3 left-1/2 -translate-x-1/2 px-3 py-0.5 bg-blue-600 text-white text-xs font-semibold rounded-full">Current</div>
                            <h4 class="font-bold text-gray-900 dark:text-white">Free</h4>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white mt-2">$0<span class="text-sm font-normal text-gray-500">/mo</span></p>
                            <ul class="mt-3 text-xs text-gray-500 dark:text-gray-400 space-y-1">
                                <li>3 employees</li>
                                <li>Basic monitoring</li>
                                <li>Screenshots only</li>
                            </ul>
                        </div>
                        <!-- Starter -->
                        <div class="border-2 border-gray-200 dark:border-gray-600 rounded-xl p-5 text-center hover:border-blue-300 dark:hover:border-blue-500 transition-colors cursor-pointer">
                            <h4 class="font-bold text-gray-900 dark:text-white">Starter</h4>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white mt-2">$29<span class="text-sm font-normal text-gray-500">/mo</span></p>
                            <ul class="mt-3 text-xs text-gray-500 dark:text-gray-400 space-y-1">
                                <li>15 employees</li>
                                <li>Screenshots + Attendance</li>
                                <li>Email support</li>
                            </ul>
                        </div>
                        <!-- Pro -->
                        <div class="border-2 border-gray-200 dark:border-gray-600 rounded-xl p-5 text-center relative hover:border-violet-300 dark:hover:border-violet-500 transition-colors cursor-pointer">
                            <div class="absolute -top-3 left-1/2 -translate-x-1/2 px-3 py-0.5 bg-violet-600 text-white text-xs font-semibold rounded-full">Popular</div>
                            <h4 class="font-bold text-gray-900 dark:text-white">Pro</h4>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white mt-2">$79<span class="text-sm font-normal text-gray-500">/mo</span></p>
                            <ul class="mt-3 text-xs text-gray-500 dark:text-gray-400 space-y-1">
                                <li>50 employees</li>
                                <li>Live monitoring</li>
                                <li>Face recognition</li>
                            </ul>
                        </div>
                        <!-- Enterprise -->
                        <div class="border-2 border-gray-200 dark:border-gray-600 rounded-xl p-5 text-center hover:border-amber-300 dark:hover:border-amber-500 transition-colors cursor-pointer">
                            <h4 class="font-bold text-gray-900 dark:text-white">Enterprise</h4>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white mt-2">$199<span class="text-sm font-normal text-gray-500">/mo</span></p>
                            <ul class="mt-3 text-xs text-gray-500 dark:text-gray-400 space-y-1">
                                <li>Unlimited employees</li>
                                <li>API access</li>
                                <li>Priority support</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Desktop Agent Download -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">Desktop Agent</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Download and distribute the desktop agent to your employees. The agent handles face verification, screenshots, and activity monitoring.</p>
                    <button class="inline-flex items-center gap-2 px-5 py-2.5 bg-gray-900 hover:bg-gray-800 dark:bg-gray-700 dark:hover:bg-gray-600 text-white text-sm font-semibold rounded-xl transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        Generate & Download Agent EXE
                    </button>
                </div>

                <!-- Danger Zone -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-red-200 dark:border-red-900/30 p-6">
                    <h3 class="text-lg font-semibold text-red-600 dark:text-red-400 mb-3">Danger Zone</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Permanently delete your organization and all associated data. This action cannot be undone.</p>
                    <button class="px-5 py-2.5 bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 text-sm font-semibold rounded-xl border border-red-200 dark:border-red-800 hover:bg-red-100 dark:hover:bg-red-900/30 transition-colors">
                        Delete Organization
                    </button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
