<script setup>
import { ref, onMounted, computed } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';
import axios from 'axios';

const employees = ref([]);
const searchQuery = ref('');
const filterStatus = ref('all');
const loading = ref(true);

const filteredEmployees = computed(() => {
    let result = employees.value;

    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase();
        result = result.filter(e =>
            e.name?.toLowerCase().includes(query) ||
            e.email?.toLowerCase().includes(query) ||
            e.department?.toLowerCase().includes(query)
        );
    }

    if (filterStatus.value === 'remote') {
        result = result.filter(e => e.allow_remote);
    } else if (filterStatus.value === 'onsite') {
        result = result.filter(e => !e.allow_remote);
    }

    return result;
});

const deleteEmployee = async (id) => {
    if (!confirm('Are you sure you want to delete this employee?')) return;
    try {
        await axios.post('/delete-employee', { employee_id: id });
        employees.value = employees.value.filter(e => e.id !== id);
    } catch (err) {
        alert('Failed to delete employee.');
    }
};

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
</script>

<template>
    <AppLayout title="Employees">
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="font-bold text-2xl text-gray-800 dark:text-gray-100">Employees</h2>
                <Link :href="route('employees.create')"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl shadow-sm transition-all hover:shadow-md">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Add Employee
                </Link>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Search & Filter Bar -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-4 mb-6">
                    <div class="flex flex-col sm:flex-row gap-4">
                        <div class="relative flex-1">
                            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            <input v-model="searchQuery" type="text" placeholder="Search employees by name, email, or department..."
                                class="w-full pl-10 pr-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:text-white" />
                        </div>
                        <div class="flex gap-2">
                            <button @click="filterStatus = 'all'" :class="filterStatus === 'all' ? 'bg-blue-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300'"
                                class="px-4 py-2 text-sm font-medium rounded-lg transition-colors">All</button>
                            <button @click="filterStatus = 'remote'" :class="filterStatus === 'remote' ? 'bg-cyan-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300'"
                                class="px-4 py-2 text-sm font-medium rounded-lg transition-colors">Remote</button>
                            <button @click="filterStatus = 'onsite'" :class="filterStatus === 'onsite' ? 'bg-indigo-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300'"
                                class="px-4 py-2 text-sm font-medium rounded-lg transition-colors">Onsite</button>
                        </div>
                    </div>
                </div>

                <!-- Loading State -->
                <div v-if="loading" class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-16 text-center">
                    <div class="inline-block w-10 h-10 border-4 border-blue-200 border-t-blue-600 rounded-full animate-spin"></div>
                    <p class="mt-4 text-gray-500">Loading employees...</p>
                </div>

                <!-- Empty State -->
                <div v-else-if="filteredEmployees.length === 0 && !searchQuery" class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-16 text-center">
                    <svg class="mx-auto w-20 h-20 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <h3 class="mt-4 text-xl font-semibold text-gray-700 dark:text-gray-300">No employees yet</h3>
                    <p class="mt-2 text-gray-500 dark:text-gray-400">Add your first employee to start monitoring.</p>
                    <Link :href="route('employees.create')" class="mt-6 inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-xl transition-colors">
                        Add Your First Employee
                    </Link>
                </div>

                <!-- Employee Cards Grid -->
                <div v-else class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
                    <div v-for="employee in filteredEmployees" :key="employee.id"
                        class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden hover:shadow-lg transition-all duration-300 group">
                        <!-- Card Header with gradient -->
                        <div class="h-2 bg-gradient-to-r" :class="employee.allow_remote ? 'from-cyan-500 to-blue-500' : 'from-violet-500 to-indigo-500'"></div>

                        <div class="p-5">
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br flex items-center justify-center text-white font-bold text-lg"
                                        :class="employee.allow_remote ? 'from-cyan-500 to-blue-500' : 'from-violet-500 to-indigo-500'">
                                        {{ employee.name?.charAt(0)?.toUpperCase() || '?' }}
                                    </div>
                                    <div>
                                        <h4 class="font-semibold text-gray-900 dark:text-white">{{ employee.name }}</h4>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ employee.email }}</p>
                                    </div>
                                </div>
                                <span :class="employee.allow_remote ? 'bg-cyan-100 text-cyan-700 dark:bg-cyan-900/30 dark:text-cyan-400' : 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-400'"
                                    class="px-2.5 py-1 rounded-full text-xs font-medium">
                                    {{ employee.allow_remote ? 'Remote' : 'Onsite' }}
                                </span>
                            </div>

                            <div class="space-y-2 text-sm text-gray-600 dark:text-gray-400">
                                <div class="flex justify-between">
                                    <span>CNIC/ID</span>
                                    <span class="font-medium text-gray-800 dark:text-gray-200">{{ employee.cnic || 'N/A' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Working Hours</span>
                                    <span class="font-medium text-gray-800 dark:text-gray-200">{{ employee.start_working_hour }} - {{ employee.end_working_hour }}</span>
                                </div>
                                <div v-if="employee.department" class="flex justify-between">
                                    <span>Department</span>
                                    <span class="font-medium text-gray-800 dark:text-gray-200">{{ employee.department }}</span>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700 flex items-center gap-2">
                                <Link :href="route('employees.show', employee.id)"
                                    class="flex-1 text-center px-3 py-2 text-sm font-medium text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/20 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-colors">
                                    View
                                </Link>
                                <button @click="deleteEmployee(employee.id)"
                                    class="px-3 py-2 text-sm font-medium text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-900/20 rounded-lg hover:bg-red-100 dark:hover:bg-red-900/30 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Results count -->
                <div v-if="searchQuery && filteredEmployees.length > 0" class="mt-4 text-sm text-gray-500 dark:text-gray-400 text-center">
                    Showing {{ filteredEmployees.length }} of {{ employees.length }} employees
                </div>
            </div>
        </div>
    </AppLayout>
</template>
