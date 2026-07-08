<script setup>
import { ref, onMounted } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';
import axios from 'axios';

const props = defineProps({
    employeeId: [Number, String],
});

const employee = ref(null);
const loading = ref(true);

onMounted(async () => {
    try {
        const res = await axios.post('/get-employees');
        employee.value = res.data.find(e => e.id == props.employeeId);
    } catch (e) {
        employee.value = null;
    } finally {
        loading.value = false;
    }
});
</script>

<template>
    <AppLayout title="Employee Details">
        <template #header>
            <div class="flex items-center gap-4">
                <Link :href="route('employees.index')" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </Link>
                <h2 class="font-bold text-2xl text-gray-800 dark:text-gray-100">
                    {{ employee?.name || 'Employee Details' }}
                </h2>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
                <div v-if="loading" class="text-center py-20">
                    <div class="inline-block w-10 h-10 border-4 border-blue-200 border-t-blue-600 rounded-full animate-spin"></div>
                </div>

                <div v-else-if="!employee" class="text-center py-20">
                    <p class="text-gray-500 dark:text-gray-400">Employee not found.</p>
                </div>

                <div v-else class="space-y-6">
                    <!-- Profile Header -->
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                        <div class="h-32 bg-gradient-to-r" :class="employee.allow_remote ? 'from-cyan-500 via-blue-500 to-indigo-500' : 'from-violet-500 via-purple-500 to-indigo-500'"></div>
                        <div class="px-6 pb-6">
                            <div class="flex flex-col sm:flex-row items-start sm:items-end gap-4 -mt-12">
                                <div class="w-24 h-24 rounded-2xl bg-gradient-to-br from-blue-500 to-violet-500 flex items-center justify-center text-white font-bold text-3xl border-4 border-white dark:border-gray-800 shadow-lg">
                                    {{ employee.name?.charAt(0)?.toUpperCase() }}
                                </div>
                                <div class="flex-1 pt-2">
                                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white">{{ employee.name }}</h3>
                                    <p class="text-gray-500 dark:text-gray-400">{{ employee.designation || employee.department || 'Employee' }}</p>
                                </div>
                                <div class="flex gap-2">
                                    <span :class="employee.allow_remote ? 'bg-cyan-100 text-cyan-700 dark:bg-cyan-900/30 dark:text-cyan-400' : 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-400'"
                                        class="px-3 py-1.5 rounded-full text-xs font-semibold">
                                        {{ employee.allow_remote ? '🌐 Remote' : '🏢 Onsite' }}
                                    </span>
                                    <span class="px-3 py-1.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400">
                                        {{ employee.status || 'Active' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Personal Info -->
                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                            <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Personal Information</h4>
                            <div class="space-y-3">
                                <div class="flex justify-between py-2 border-b border-gray-50 dark:border-gray-700">
                                    <span class="text-sm text-gray-500 dark:text-gray-400">Email</span>
                                    <span class="text-sm font-medium text-gray-900 dark:text-white">{{ employee.email }}</span>
                                </div>
                                <div class="flex justify-between py-2 border-b border-gray-50 dark:border-gray-700">
                                    <span class="text-sm text-gray-500 dark:text-gray-400">CNIC/ID</span>
                                    <span class="text-sm font-medium text-gray-900 dark:text-white">{{ employee.cnic }}</span>
                                </div>
                                <div v-if="employee.age" class="flex justify-between py-2 border-b border-gray-50 dark:border-gray-700">
                                    <span class="text-sm text-gray-500 dark:text-gray-400">Age</span>
                                    <span class="text-sm font-medium text-gray-900 dark:text-white">{{ employee.age }}</span>
                                </div>
                                <div v-if="employee.phone" class="flex justify-between py-2 border-b border-gray-50 dark:border-gray-700">
                                    <span class="text-sm text-gray-500 dark:text-gray-400">Phone</span>
                                    <span class="text-sm font-medium text-gray-900 dark:text-white">{{ employee.phone }}</span>
                                </div>
                                <div v-if="employee.department" class="flex justify-between py-2 border-b border-gray-50 dark:border-gray-700">
                                    <span class="text-sm text-gray-500 dark:text-gray-400">Department</span>
                                    <span class="text-sm font-medium text-gray-900 dark:text-white">{{ employee.department }}</span>
                                </div>
                                <div v-if="employee.designation" class="flex justify-between py-2">
                                    <span class="text-sm text-gray-500 dark:text-gray-400">Designation</span>
                                    <span class="text-sm font-medium text-gray-900 dark:text-white">{{ employee.designation }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Work Schedule -->
                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                            <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Work Schedule</h4>
                            <div class="space-y-3">
                                <div class="flex justify-between py-2 border-b border-gray-50 dark:border-gray-700">
                                    <span class="text-sm text-gray-500 dark:text-gray-400">Start Time</span>
                                    <span class="text-sm font-medium text-gray-900 dark:text-white">{{ employee.start_working_hour }}</span>
                                </div>
                                <div class="flex justify-between py-2 border-b border-gray-50 dark:border-gray-700">
                                    <span class="text-sm text-gray-500 dark:text-gray-400">End Time</span>
                                    <span class="text-sm font-medium text-gray-900 dark:text-white">{{ employee.end_working_hour }}</span>
                                </div>
                                <div class="flex justify-between py-2 border-b border-gray-50 dark:border-gray-700">
                                    <span class="text-sm text-gray-500 dark:text-gray-400">Work Mode</span>
                                    <span class="text-sm font-medium text-gray-900 dark:text-white">{{ employee.allow_remote ? 'Remote Allowed' : 'Onsite Only' }}</span>
                                </div>
                                <div class="flex justify-between py-2">
                                    <span class="text-sm text-gray-500 dark:text-gray-400">Created</span>
                                    <span class="text-sm font-medium text-gray-900 dark:text-white">{{ new Date(employee.created_at).toLocaleDateString() }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Activity Section Placeholder -->
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Recent Activity</h4>
                        <div class="text-center py-8">
                            <svg class="mx-auto w-12 h-12 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            <p class="mt-3 text-sm text-gray-500 dark:text-gray-400">Activity data will appear here once the desktop agent is running.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
