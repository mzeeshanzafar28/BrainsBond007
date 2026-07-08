<script setup>
import { ref, onMounted, computed } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';

const selectedDate = ref(new Date().toISOString().split('T')[0]);
const records = ref([]);
const loading = ref(false);

// Placeholder data - will be populated from API when available
const summaryStats = computed(() => ({
    present: records.value.filter(r => r.status === 'present').length,
    absent: records.value.filter(r => r.status === 'absent').length,
    late: records.value.filter(r => r.status === 'late').length,
    remote: records.value.filter(r => r.status === 'remote').length,
}));

const statusColors = {
    present: 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400',
    absent: 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
    late: 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400',
    remote: 'bg-cyan-100 text-cyan-700 dark:bg-cyan-900/30 dark:text-cyan-400',
    half_day: 'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400',
};
</script>

<template>
    <AppLayout title="Attendance">
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="font-bold text-2xl text-gray-800 dark:text-gray-100">Attendance</h2>
                <div class="flex items-center gap-3">
                    <input v-model="selectedDate" type="date"
                        class="px-4 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 dark:text-white" />
                </div>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Summary Cards -->
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 p-5">
                        <p class="text-sm text-gray-500 dark:text-gray-400">Present</p>
                        <p class="text-3xl font-bold text-emerald-600 dark:text-emerald-400 mt-1">{{ summaryStats.present }}</p>
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 p-5">
                        <p class="text-sm text-gray-500 dark:text-gray-400">Absent</p>
                        <p class="text-3xl font-bold text-red-600 dark:text-red-400 mt-1">{{ summaryStats.absent }}</p>
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 p-5">
                        <p class="text-sm text-gray-500 dark:text-gray-400">Late</p>
                        <p class="text-3xl font-bold text-amber-600 dark:text-amber-400 mt-1">{{ summaryStats.late }}</p>
                    </div>
                    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 p-5">
                        <p class="text-sm text-gray-500 dark:text-gray-400">Remote</p>
                        <p class="text-3xl font-bold text-cyan-600 dark:text-cyan-400 mt-1">{{ summaryStats.remote }}</p>
                    </div>
                </div>

                <!-- Attendance Table -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Daily Attendance — {{ selectedDate }}</h3>
                    </div>

                    <div v-if="records.length === 0" class="p-12 text-center">
                        <svg class="mx-auto w-14 h-14 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        <h4 class="mt-3 text-lg font-semibold text-gray-700 dark:text-gray-300">No attendance records</h4>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Attendance data will populate as employees check in via the desktop agent.</p>
                    </div>

                    <table v-else class="w-full">
                        <thead class="bg-gray-50 dark:bg-gray-700/50">
                            <tr>
                                <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Employee</th>
                                <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Check In</th>
                                <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Check Out</th>
                                <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Hours</th>
                                <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            <tr v-for="record in records" :key="record.id" class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-white">{{ record.employee_name }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">{{ record.check_in || '—' }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">{{ record.check_out || '—' }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">{{ record.total_hours }}h</td>
                                <td class="px-6 py-4">
                                    <span :class="statusColors[record.status]" class="px-2.5 py-1 rounded-full text-xs font-medium capitalize">
                                        {{ record.status }}
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
