<script setup>
import { ref } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { router } from '@inertiajs/vue3';
import axios from 'axios';

const form = ref({
    name: '',
    email: '',
    age: '',
    phone: '',
    department: '',
    designation: '',
    cnic: '',
    start_working_hour: '09:00',
    end_working_hour: '17:00',
    allow_remote: false,
    face_images: '[]',
    remote_locations: null,
});

const errors = ref({});
const submitting = ref(false);
const success = ref(false);

const submitForm = async () => {
    submitting.value = true;
    errors.value = {};

    try {
        const payload = {
            ...form.value,
            face_images: form.value.face_images || '[]',
            remote_locations: form.value.allow_remote && form.value.remote_locations
                ? form.value.remote_locations
                : null,
        };

        await axios.post('/add-employee', payload);
        success.value = true;
        setTimeout(() => {
            router.visit(route('employees.index'));
        }, 1500);
    } catch (err) {
        if (err.response?.status === 422) {
            errors.value = err.response.data.errors || {};
        } else {
            errors.value = { general: ['Failed to add employee. Please try again.'] };
        }
    } finally {
        submitting.value = false;
    }
};
</script>

<template>
    <AppLayout title="Add Employee">
        <template #header>
            <div class="flex items-center gap-4">
                <a :href="route('employees.index')" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>
                <h2 class="font-bold text-2xl text-gray-800 dark:text-gray-100">Add Employee</h2>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Success Message -->
                <div v-if="success" class="mb-6 p-4 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 rounded-xl flex items-center gap-3">
                    <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span class="text-emerald-700 dark:text-emerald-300 font-medium">Employee added successfully! Redirecting...</span>
                </div>

                <!-- Error Message -->
                <div v-if="errors.general" class="mb-6 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl">
                    <p class="text-red-700 dark:text-red-300 text-sm">{{ errors.general[0] }}</p>
                </div>

                <form @submit.prevent="submitForm" class="space-y-6">
                    <!-- Personal Information -->
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-5">Personal Information</h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Full Name *</label>
                                <input v-model="form.name" type="text" required
                                    class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:text-white"
                                    placeholder="John Doe" />
                                <p v-if="errors.name" class="mt-1 text-xs text-red-500">{{ errors.name[0] }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Email *</label>
                                <input v-model="form.email" type="email" required
                                    class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:text-white"
                                    placeholder="john@company.com" />
                                <p v-if="errors.email" class="mt-1 text-xs text-red-500">{{ errors.email[0] }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Age</label>
                                <input v-model="form.age" type="number" min="18" max="100"
                                    class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:text-white"
                                    placeholder="25" />
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Phone</label>
                                <input v-model="form.phone" type="tel"
                                    class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:text-white"
                                    placeholder="+92 300 1234567" />
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">CNIC / ID *</label>
                                <input v-model="form.cnic" type="text" required
                                    class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:text-white"
                                    placeholder="12345-6789012-3" />
                                <p v-if="errors.cnic" class="mt-1 text-xs text-red-500">{{ errors.cnic[0] }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Department</label>
                                <input v-model="form.department" type="text"
                                    class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:text-white"
                                    placeholder="Engineering" />
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Designation</label>
                                <input v-model="form.designation" type="text"
                                    class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:text-white"
                                    placeholder="Software Developer" />
                            </div>
                        </div>
                    </div>

                    <!-- Work Schedule -->
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-5">Work Schedule</h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Start Time *</label>
                                <input v-model="form.start_working_hour" type="time" required
                                    class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:text-white" />
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">End Time *</label>
                                <input v-model="form.end_working_hour" type="time" required
                                    class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:text-white" />
                            </div>
                        </div>

                        <!-- Remote Toggle -->
                        <div class="mt-5 flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl">
                            <div>
                                <p class="font-medium text-gray-900 dark:text-white text-sm">Allow Remote Work</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Employee can work from approved remote locations</p>
                            </div>
                            <button type="button" @click="form.allow_remote = !form.allow_remote"
                                :class="form.allow_remote ? 'bg-blue-600' : 'bg-gray-300 dark:bg-gray-600'"
                                class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors">
                                <span :class="form.allow_remote ? 'translate-x-6' : 'translate-x-1'"
                                    class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform" />
                            </button>
                        </div>
                    </div>

                    <!-- Submit -->
                    <div class="flex items-center justify-end gap-3">
                        <a :href="route('employees.index')"
                            class="px-5 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                            Cancel
                        </a>
                        <button type="submit" :disabled="submitting"
                            class="px-6 py-2.5 text-sm font-semibold text-white bg-blue-600 rounded-xl hover:bg-blue-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed shadow-sm hover:shadow-md">
                            <span v-if="submitting" class="flex items-center gap-2">
                                <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                                </svg>
                                Adding...
                            </span>
                            <span v-else>Add Employee</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
