<script setup>
import { ref, computed } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    appointments: Array,
    doctors: Array,
    weekStart: String,
    weekEnd: String,
    selectedDoctor: String
});

const selectedDoctorId = ref(props.selectedDoctor || '');

const weekDays = computed(() => {
    const days = [];
    const start = new Date(props.weekStart);
    
    for (let i = 0; i < 7; i++) {
        const date = new Date(start);
        date.setDate(start.getDate() + i);
        days.push({
            date: date.toISOString().split('T')[0],
            dayName: date.toLocaleDateString('es', { weekday: 'short' }),
            dayNumber: date.getDate(),
            month: date.toLocaleDateString('es', { month: 'short' })
        });
    }
    
    return days;
});

const getAppointmentsForDay = (dateString) => {
    return props.appointments.filter(apt => {
        const aptDate = new Date(apt.appointment_date).toISOString().split('T')[0];
        return aptDate === dateString;
    }).sort((a, b) => new Date(a.appointment_date) - new Date(b.appointment_date));
};

const navigateWeek = (direction) => {
    const current = new Date(props.weekStart);
    current.setDate(current.getDate() + (direction * 7));
    
    router.get('/calendar', {
        week_start: current.toISOString().split('T')[0],
        doctor: selectedDoctorId.value
    }, {
        preserveState: true
    });
};

const filterByDoctor = () => {
    router.get('/calendar', {
        week_start: props.weekStart,
        doctor: selectedDoctorId.value
    }, {
        preserveState: true
    });
};

const getStatusColor = (status) => {
    const colors = {
        pendiente: 'bg-yellow-100 border-yellow-400 text-yellow-800',
        confirmada: 'bg-green-100 border-green-400 text-green-800',
        rechazada: 'bg-red-100 border-red-400 text-red-800',
        completada: 'bg-blue-100 border-blue-400 text-blue-800'
    };
    return colors[status] || 'bg-gray-100 border-gray-400 text-gray-800';
};

const formatTime = (datetime) => {
    return new Date(datetime).toLocaleTimeString('es', {
        hour: '2-digit',
        minute: '2-digit'
    });
};
</script>

<template>
    <Head title="Calendario Semanal" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Calendario Semanal</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Controles -->
                <div class="bg-white rounded-lg shadow p-6 mb-6">
                    <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                        <!-- Navegación de semana -->
                        <div class="flex items-center gap-4">
                            <button 
                                @click="navigateWeek(-1)"
                                class="p-2 bg-gray-200 rounded-lg hover:bg-gray-300 transition"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                </svg>
                            </button>
                            
                            <div class="text-center">
                                <p class="text-lg font-semibold text-gray-900">
                                    {{ new Date(weekStart).toLocaleDateString('es', { day: 'numeric', month: 'long' }) }}
                                    -
                                    {{ new Date(weekEnd).toLocaleDateString('es', { day: 'numeric', month: 'long', year: 'numeric' }) }}
                                </p>
                            </div>
                            
                            <button 
                                @click="navigateWeek(1)"
                                class="p-2 bg-gray-200 rounded-lg hover:bg-gray-300 transition"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </button>
                        </div>

                        <!-- Filtro por médico -->
                        <div class="flex items-center gap-3">
                            <select 
                                v-model="selectedDoctorId" 
                                @change="filterByDoctor"
                                class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                            >
                                <option value="">Todos los médicos</option>
                                <option v-for="doctor in doctors" :key="doctor.id" :value="doctor.slug">
                                    {{ doctor.name }}
                                </option>
                            </select>

                            <Link 
                                href="/dashboard" 
                                class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition"
                            >
                                Volver al Dashboard
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- Calendario -->
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="grid grid-cols-7 border-b border-gray-200">
                        <div 
                            v-for="day in weekDays" 
                            :key="day.date"
                            class="p-4 text-center border-r border-gray-200 last:border-r-0"
                            :class="{ 'bg-indigo-50': day.date === new Date().toISOString().split('T')[0] }"
                        >
                            <div class="text-sm font-semibold text-gray-700">{{ day.dayName }}</div>
                            <div class="text-2xl font-bold text-gray-900 mt-1">{{ day.dayNumber }}</div>
                            <div class="text-xs text-gray-500">{{ day.month }}</div>
                        </div>
                    </div>

                    <div class="grid grid-cols-7 min-h-[500px]">
                        <div 
                            v-for="day in weekDays" 
                            :key="day.date"
                            class="border-r border-gray-200 last:border-r-0 p-2 bg-gray-50"
                        >
                            <div class="space-y-2">
                                <div 
                                    v-for="appointment in getAppointmentsForDay(day.date)" 
                                    :key="appointment.id"
                                    :class="getStatusColor(appointment.status)"
                                    class="p-2 rounded border-l-4 text-xs"
                                >
                                    <div class="font-semibold">{{ formatTime(appointment.appointment_date) }}</div>
                                    <div class="truncate">{{ appointment.patient_name }}</div>
                                    <div class="text-xs opacity-75 truncate">{{ appointment.doctor.name }}</div>
                                    <Link 
                                        :href="`/appointments/${appointment.id}`"
                                        class="text-xs underline mt-1 block hover:opacity-75"
                                    >
                                        Ver detalles
                                    </Link>
                                </div>

                                <div v-if="getAppointmentsForDay(day.date).length === 0" class="text-center text-gray-400 text-xs py-4">
                                    Sin citas
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Leyenda -->
                <div class="bg-white rounded-lg shadow p-4 mt-6">
                    <h3 class="font-semibold text-gray-900 mb-3">Leyenda</h3>
                    <div class="flex flex-wrap gap-4">
                        <div class="flex items-center gap-2">
                            <div class="w-4 h-4 bg-yellow-100 border-2 border-yellow-400 rounded"></div>
                            <span class="text-sm text-gray-700">Pendiente</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-4 h-4 bg-green-100 border-2 border-green-400 rounded"></div>
                            <span class="text-sm text-gray-700">Confirmada</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-4 h-4 bg-red-100 border-2 border-red-400 rounded"></div>
                            <span class="text-sm text-gray-700">Rechazada</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-4 h-4 bg-blue-100 border-2 border-blue-400 rounded"></div>
                            <span class="text-sm text-gray-700">Completada</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>