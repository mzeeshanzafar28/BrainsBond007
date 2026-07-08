<script setup>
import { ref } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';

const locations = ref([]);
const showAddForm = ref(false);
const newLocation = ref({
    name: '',
    address: '',
    latitude: '',
    longitude: '',
    radius_meters: 100,
    type: 'office',
});

const addLocation = () => {
    // Will integrate with API
    locations.value.push({
        id: Date.now(),
        ...newLocation.value,
        is_active: true,
    });
    showAddForm.value = false;
    newLocation.value = { name: '', address: '', latitude: '', longitude: '', radius_meters: 100, type: 'office' };
};

const removeLocation = (id) => {
    locations.value = locations.value.filter(l => l.id !== id);
};
</script>

<template>
    <AppLayout title="Locations">
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="font-bold text-2xl text-gray-800 dark:text-gray-100">Locations</h2>
                <button @click="showAddForm = !showAddForm"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Add Location
                </button>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Add Location Form -->
                <div v-if="showAddForm" class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-5">Add New Location</h3>
                    <form @submit.prevent="addLocation" class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Location Name *</label>
                            <input v-model="newLocation.name" type="text" required
                                class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 dark:text-white"
                                placeholder="Main Office" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Type</label>
                            <select v-model="newLocation.type"
                                class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 dark:text-white">
                                <option value="office">Office</option>
                                <option value="remote">Remote Hub</option>
                                <option value="site">Job Site</option>
                            </select>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Address</label>
                            <input v-model="newLocation.address" type="text"
                                class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 dark:text-white"
                                placeholder="123 Business Street, City" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Latitude *</label>
                            <input v-model="newLocation.latitude" type="number" step="any" required
                                class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 dark:text-white"
                                placeholder="33.6844" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Longitude *</label>
                            <input v-model="newLocation.longitude" type="number" step="any" required
                                class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 dark:text-white"
                                placeholder="73.0479" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Geofence Radius (meters)</label>
                            <input v-model="newLocation.radius_meters" type="number" min="50" max="5000"
                                class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 dark:text-white" />
                        </div>
                        <div class="flex items-end">
                            <button type="submit" class="w-full px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl transition-colors">
                                Save Location
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Locations List -->
                <div v-if="locations.length === 0 && !showAddForm" class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-16 text-center">
                    <svg class="mx-auto w-16 h-16 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <h3 class="mt-4 text-lg font-semibold text-gray-700 dark:text-gray-300">No locations configured</h3>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Add your office and remote work locations for geofencing verification.</p>
                    <button @click="showAddForm = true" class="mt-4 px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-xl transition-colors">
                        Add First Location
                    </button>
                </div>

                <div v-else class="space-y-4">
                    <div v-for="loc in locations" :key="loc.id"
                        class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-5 flex items-center justify-between hover:shadow-md transition-all">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-xl flex items-center justify-center"
                                :class="loc.type === 'office' ? 'bg-blue-50 dark:bg-blue-900/20' : loc.type === 'remote' ? 'bg-cyan-50 dark:bg-cyan-900/20' : 'bg-amber-50 dark:bg-amber-900/20'">
                                <svg v-if="loc.type === 'office'" class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                                <svg v-else class="w-6 h-6" :class="loc.type === 'remote' ? 'text-cyan-600 dark:text-cyan-400' : 'text-amber-600 dark:text-amber-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-900 dark:text-white">{{ loc.name }}</h4>
                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ loc.address || `${loc.latitude}, ${loc.longitude}` }}</p>
                                <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">Radius: {{ loc.radius_meters }}m · {{ loc.type }}</p>
                            </div>
                        </div>
                        <button @click="removeLocation(loc.id)" class="text-gray-400 hover:text-red-500 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
