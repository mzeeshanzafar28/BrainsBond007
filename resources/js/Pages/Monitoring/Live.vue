<script setup>
import { ref, onMounted } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import axios from 'axios';

const employees = ref([]);
const loading = ref(true);
const selectedEmployee = ref(null);

onMounted(async () => {
    try {
        const res = await axios.post('/get-employees');
        employees.value = res.data;
    } catch (e) {
        employees.value = [];
    } finally {
        loading.value = false;
    }
});

const startScreencast = async (employeeId) => {
    try {
        await axios.post('/start-screencast', { employee_id: employeeId });
        alert('Screencast request sent to employee agent.');
    } catch (e) {
        alert('Failed to start screencast.');
    }
};

const seizeSystem = async (employeeId) => {
    if (!confirm('Are you sure you want to seize this employee\'s system? This is a critical action.')) return;
    try {
        await axios.post('/seize-system', { employee_id: employeeId });
        alert('System seize command sent.');
    } catch (e) {
        alert('Failed to seize system.');
    }
};
</script>

<template>
    <AppLayout title="Live Monitoring">
        <template #header>
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <h2 class="font-bold text-2xl text-gray-800 dark:text-gray-100">Live Monitoring</h2>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-medium bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400">
                        <span class="w-2 h-2 rounded-full bg-red-500 animate-pulse"></span>
                        Live
                    </span>
                </div>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div v-if="loading" class="text-center py-20">
                    <div class="inline-block w-10 h-10 border-4 border-blue-200 border-t-blue-600 rounded-full animate-spin"></div>
                    <p class="mt-4 text-gray-500">Loading monitoring data...</p>
                </div>

                <div v-else-if="employees.length === 0" class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-16 text-center">
                    <svg class="mx-auto w-16 h-16 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                    </svg>
                    <h3 class="mt-4 text-lg font-semibold text-gray-700 dark:text-gray-300">No employees to monitor</h3>
                    <p class="mt-2 text-sm text-gray-500">Add employees first to use live monitoring.</p>
                </div>

                <div v-else>
                    <!-- Monitoring Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
                        <div v-for="emp in employees" :key="emp.id"
                            class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden group hover:shadow-lg transition-all duration-300">
                            <!-- Preview Area -->
                            <div class="relative aspect-video bg-gray-900 flex items-center justify-center">
                                <div class="text-center">
                                    <svg class="mx-auto w-10 h-10 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                    </svg>
                                    <p class="text-xs text-gray-500 mt-2">Awaiting agent connection</p>
                                </div>

                                <!-- Status Badge -->
                                <div class="absolute top-3 right-3">
                                    <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium bg-gray-800/80 text-gray-300 backdrop-blur-sm">
                                        <span class="w-1.5 h-1.5 rounded-full bg-gray-500"></span>
                                        Offline
                                    </span>
                                </div>
                            </div>

                            <!-- Employee Info & Controls -->
                            <div class="p-4">
                                <div class="flex items-center justify-between mb-3">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-lg bg-gradient-to-br from-blue-500 to-violet-500 flex items-center justify-center text-white font-semibold text-sm">
                                            {{ emp.name?.charAt(0)?.toUpperCase() }}
                                        </div>
                                        <div>
                                            <p class="font-medium text-sm text-gray-900 dark:text-white">{{ emp.name }}</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ emp.email }}</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div class="flex gap-2">
                                    <button @click="startScreencast(emp.id)"
                                        class="flex-1 flex items-center justify-center gap-1.5 px-3 py-2 text-xs font-medium text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/20 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                        </svg>
                                        Screen
                                    </button>
                                    <button @click="seizeSystem(emp.id)"
                                        class="flex items-center justify-center gap-1.5 px-3 py-2 text-xs font-medium text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-900/20 rounded-lg hover:bg-red-100 dark:hover:bg-red-900/30 transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                        </svg>
                                        Seize
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
