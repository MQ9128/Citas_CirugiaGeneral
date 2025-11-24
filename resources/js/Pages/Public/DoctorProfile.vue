<script setup>
import { ref, computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    doctor: Object,
    availableSlots: Array
});

const selectedDate = ref(null);
const currentWeekStart = ref(0);

const daysOfWeek = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];

// Agrupar slots por día
const slotsByDay = computed(() => {
    const grouped = {};
    props.availableSlots.forEach(slot => {
        const date = new Date(slot.datetime);
        const dateKey = date.toISOString().split('T')[0];
        
        if (!grouped[dateKey]) {
            grouped[dateKey] = {
                date: dateKey,
                dayName: daysOfWeek[date.getDay()],
                dayNumber: date.getDate(),
                month: date.toLocaleString('es', { month: 'short' }),
                slots: []
            };
        }
        grouped[dateKey].slots.push(slot);
    });
    return Object.values(grouped).slice(0, 7); // Próximos 7 días
});

const selectSlot = (slot) => {
    selectedDate.value = slot.datetime;
};
</script>

<template>
    <Head :title="`${doctor.name} - Disponibilidad`" />

    <div class="min-h-screen bg-gradient-to-br from-indigo-50 to-blue-100">
        <!-- Header -->
        <header class="bg-white shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                <Link href="/" class="inline-flex items-center text-indigo-600 hover:text-indigo-800">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Volver a la lista de médicos
                </Link>
            </div>
        </header>

        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <!-- Perfil del Doctor -->
            <div class="bg-white rounded-xl shadow-lg p-8 mb-8">
                <div class="flex flex-col md:flex-row items-center md:items-start gap-6">
                    <div class="w-32 h-32 bg-gradient-to-r from-indigo-600 to-blue-600 rounded-full flex items-center justify-center text-white text-5xl font-bold">
                        {{ doctor.name.charAt(0) }}
                    </div>
                    <div class="flex-1 text-center md:text-left">
                        <h1 class="text-4xl font-bold text-gray-900 mb-2">{{ doctor.name }}</h1>
                        <p class="text-xl text-indigo-600 mb-4">{{ doctor.specialty }}</p>
                        <p class="text-gray-600 mb-4">{{ doctor.bio }}</p>
                        <div class="flex flex-col sm:flex-row gap-4 justify-center md:justify-start">
                            <div class="flex items-center text-gray-700">
                                <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                                {{ doctor.email }}
                            </div>
                            <div v-if="doctor.phone" class="flex items-center text-gray-700">
                                <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                                {{ doctor.phone }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Calendario de Disponibilidad -->
            <div class="bg-white rounded-xl shadow-lg p-8">
                <h2 class="text-3xl font-bold text-gray-900 mb-6">Horarios Disponibles</h2>

                <div v-if="slotsByDay.length === 0" class="text-center py-12">
                    <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <p class="text-xl text-gray-600">No hay horarios disponibles en los próximos días.</p>
                    <p class="text-gray-500 mt-2">Por favor intenta más tarde o contacta al médico.</p>
                </div>

                <!-- Grid de días -->
                <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    <div 
                        v-for="day in slotsByDay" 
                        :key="day.date"
                        class="border border-gray-200 rounded-lg p-4 hover:border-indigo-300 transition"
                    >
                        <!-- Header del día -->
                        <div class="text-center mb-4 pb-3 border-b border-gray-200">
                            <div class="text-3xl font-bold text-indigo-600">{{ day.dayNumber }}</div>
                            <div class="text-sm font-semibold text-gray-700">{{ day.dayName }}</div>
                            <div class="text-xs text-gray-500">{{ day.month }}</div>
                        </div>

                        <!-- Slots disponibles -->
                        <div class="space-y-2">
                            <Link
                                v-for="slot in day.slots"
                                :key="slot.datetime"
                                :href="`/appointments/new?doctor=${doctor.slug}&start=${slot.datetime}`"
                                class="block w-full py-2 px-3 text-center bg-indigo-50 text-indigo-700 rounded hover:bg-indigo-600 hover:text-white transition font-medium text-sm"
                            >
                                {{ new Date(slot.datetime).toLocaleTimeString('es', { hour: '2-digit', minute: '2-digit', hour12: true }) }}
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</template>