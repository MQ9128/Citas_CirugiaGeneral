<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    pendingAppointments: Array,
    confirmedAppointments: Array,
    doctors: Array,
    stats: Object,
    filters: Object
});

const selectedDoctor = ref(props.filters.doctor_id || '');

const filterByDoctor = () => {
    router.get('/dashboard', { doctor_id: selectedDoctor.value }, {
        preserveState: true,
        preserveScroll: true
    });
};

const formatDateTime = (datetime) => {
    return new Date(datetime).toLocaleString('es', {
        weekday: 'short',
        day: '2-digit',
        month: 'short',
        hour: '2-digit',
        minute: '2-digit'
    });
};

const getStatusColor = (status) => {
    const colors = {
        pendiente: 'bg-yellow-100 text-yellow-800',
        confirmada: 'bg-green-100 text-green-800',
        rechazada: 'bg-red-100 text-red-800',
        completada: 'bg-blue-100 text-blue-800'
    };
    return colors[status] || 'bg-gray-100 text-gray-800';
};
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Dashboard</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Estadísticas -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm text-gray-600">Pendientes</p>
                                <p class="text-2xl font-bold text-gray-900">{{ stats.total_pending }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-green-100 text-green-600">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm text-gray-600">Confirmadas</p>
                                <p class="text-2xl font-bold text-gray-900">{{ stats.total_confirmed }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-red-100 text-red-600">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm text-gray-600">Rechazadas</p>
                                <p class="text-2xl font-bold text-gray-900">{{ stats.total_rejected }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm text-gray-600">Hoy</p>
                                <p class="text-2xl font-bold text-gray-900">{{ stats.today_appointments }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filtros y Enlaces Rápidos -->
                <div class="bg-white rounded-lg shadow p-6 mb-8">
                    <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                        <div class="flex-1 w-full md:w-auto">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Filtrar por Médico</label>
                            <select 
                                v-model="selectedDoctor" 
                                @change="filterByDoctor"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                            >
                                <option value="">Todos los médicos</option>
                                <option v-for="doctor in doctors" :key="doctor.id" :value="doctor.id">
                                    {{ doctor.name }}
                                </option>
                            </select>
                        </div>

                        <div class="flex gap-3">
                            <Link 
                                href="/calendar" 
                                class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition flex items-center"
                            >
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                Ver Calendario
                            </Link>
                            <Link 
                                href="/appointments" 
                                class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition flex items-center"
                            >
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                                Todas las Citas
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- Citas Pendientes -->
                <div class="bg-white rounded-lg shadow mb-8">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Citas Pendientes de Aprobación
                        </h3>
                    </div>

                    <div v-if="pendingAppointments.length === 0" class="p-8 text-center text-gray-500">
                        <svg class="w-16 h-16 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p>No hay citas pendientes</p>
                    </div>

                    <div v-else class="divide-y divide-gray-200">
                        <div 
                            v-for="appointment in pendingAppointments" 
                            :key="appointment.id"
                            class="p-6 hover:bg-gray-50 transition"
                        >
                            <div class="flex flex-col md:flex-row justify-between items-start gap-4">
                                <div class="flex-1">
                                    <div class="flex items-center gap-3 mb-2">
                                        <h4 class="text-lg font-semibold text-gray-900">{{ appointment.patient_name }}</h4>
                                        <span :class="getStatusColor(appointment.status)" class="px-3 py-1 rounded-full text-xs font-semibold">
                                            {{ appointment.status.toUpperCase() }}
                                        </span>
                                    </div>
                                    <div class="space-y-1 text-sm text-gray-600">
                                        <p class="flex items-center">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                            </svg>
                                            <strong>Médico:</strong> {{ appointment.doctor.name }}
                                        </p>
                                        <p class="flex items-center">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                            <strong>Fecha:</strong> {{ formatDateTime(appointment.appointment_date) }}
                                        </p>
                                        <p class="flex items-center">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                            </svg>
                                            {{ appointment.patient_email }}
                                        </p>
                                        <p v-if="appointment.reason" class="flex items-start mt-2">
                                            <svg class="w-4 h-4 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            <span><strong>Motivo:</strong> {{ appointment.reason }}</span>
                                        </p>
                                    </div>
                                </div>

                                <div class="flex gap-2">
                                    <Link
                                        :href="`/appointments/${appointment.slug}/accept`"
                                        method="post"
                                        as="button"
                                        class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition text-sm font-semibold"
                                    >
                                        Aceptar
                                    </Link>
                                    <Link
                                        :href="`/appointments/${appointment.slug}/reject`"
                                        method="post"
                                        as="button"
                                        class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition text-sm font-semibold"
                                    >
                                        Rechazar
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Próximas Citas Confirmadas -->
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Próximas Citas Confirmadas
                        </h3>
                    </div>

                    <div v-if="confirmedAppointments.length === 0" class="p-8 text-center text-gray-500">
                        <svg class="w-16 h-16 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <p>No hay citas confirmadas</p>
                    </div>

                    <div v-else class="divide-y divide-gray-200">
                        <div 
                            v-for="appointment in confirmedAppointments" 
                            :key="appointment.id"
                            class="p-6 hover:bg-gray-50 transition"
                        >
                            <div class="flex flex-col md:flex-row justify-between items-start gap-4">
                                <div class="flex-1">
                                    <div class="flex items-center gap-3 mb-2">
                                        <h4 class="text-lg font-semibold text-gray-900">{{ appointment.patient_name }}</h4>
                                        <span :class="getStatusColor(appointment.status)" class="px-3 py-1 rounded-full text-xs font-semibold">
                                            {{ appointment.status.toUpperCase() }}
                                        </span>
                                    </div>
                                    <div class="space-y-1 text-sm text-gray-600">
                                        <p class="flex items-center">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                            </svg>
                                            <strong>Médico:</strong> {{ appointment.doctor.name }}
                                        </p>
                                        <p class="flex items-center">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                            <strong>Fecha:</strong> {{ formatDateTime(appointment.appointment_date) }}
                                        </p>
                                    </div>
                                </div>

                                <Link
                                    :href="`/appointments/${appointment.id}`"
                                    class="px-4 py-2 bg-indigo-100 text-indigo-700 rounded-lg hover:bg-indigo-200 transition text-sm font-semibold"
                                >
                                    Ver Detalles
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>