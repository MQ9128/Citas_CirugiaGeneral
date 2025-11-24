<script setup>
import { ref, computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    doctors: Array
});

const selectedDoctor = ref(null);

const daysOfWeek = ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'];

const getDoctorSchedule = (doctor) => {
    const scheduleByDay = {};
    doctor.schedules.forEach(schedule => {
        if (!scheduleByDay[schedule.day_of_week]) {
            scheduleByDay[schedule.day_of_week] = [];
        }
        scheduleByDay[schedule.day_of_week].push(
            `${schedule.start_time.slice(0, 5)} - ${schedule.end_time.slice(0, 5)}`
        );
    });
    return scheduleByDay;
};
</script>

<template>
    <Head title="Agendar Cita - Cirugía General" />

    <div class="min-h-screen bg-gradient-to-br from-indigo-50 to-blue-100">
        <!-- Mensaje de éxito -->
        <div v-if="$page.props.flash.success" class="fixed top-4 right-4 z-50 max-w-md">
            <div class="bg-green-500 text-white px-6 py-4 rounded-lg shadow-lg flex items-center">
                <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ $page.props.flash.success }}</span>
            </div>
        </div>
        <!-- Header -->
        <header class="bg-white shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Sistema de Citas</h1>
                        <p class="text-gray-600">Cirugía General</p>
                    </div>
                    <Link href="/login"
                        class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                    Acceso Administrativo
                    </Link>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <!-- Intro -->
            <div class="text-center mb-12">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">
                    Agenda tu Cita Médica
                </h2>
                <p class="text-xl text-gray-600">
                    Selecciona un médico especialista para ver su disponibilidad
                </p>
            </div>

            <!-- Grid de Médicos -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div v-for="doctor in doctors" :key="doctor.id"
                    class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-2xl transition-shadow duration-300">
                    <!-- Card Header -->
                    <div class="bg-gradient-to-r from-indigo-600 to-blue-600 p-6 text-white">
                        <div
                            class="w-20 h-20 bg-white rounded-full flex items-center justify-center text-indigo-600 text-3xl font-bold mx-auto mb-4">
                            {{ doctor.name.charAt(0) }}
                        </div>
                        <h3 class="text-2xl font-bold text-center">{{ doctor.name }}</h3>
                        <p class="text-indigo-100 text-center mt-1">{{ doctor.specialty }}</p>
                    </div>

                    <!-- Card Body -->
                    <div class="p-6">
                        <p class="text-gray-600 text-sm mb-4 line-clamp-3">
                            {{ doctor.bio || 'Médico especialista en cirugía general.' }}
                        </p>

                        <!-- Contacto -->
                        <div class="space-y-2 mb-4 text-sm">
                            <div class="flex items-center text-gray-700">
                                <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                                {{ doctor.email }}
                            </div>
                            <div v-if="doctor.phone" class="flex items-center text-gray-700">
                                <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                                {{ doctor.phone }}
                            </div>
                        </div>

                        <!-- Horarios -->
                        <div class="mb-4">
                            <h4 class="font-semibold text-gray-700 mb-2 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Horarios de Atención
                            </h4>
                            <div class="space-y-1 text-sm">
                                <div v-for="day in [1, 2, 3, 4, 5]" :key="day"
                                    class="flex justify-between text-gray-600">
                                    <span class="font-medium">{{ daysOfWeek[day] }}:</span>
                                    <span v-if="getDoctorSchedule(doctor)[day]">
                                        {{ getDoctorSchedule(doctor)[day].join(', ') }}
                                    </span>
                                    <span v-else class="text-gray-400">No disponible</span>
                                </div>
                            </div>
                        </div>

                        <!-- Botones -->
                        <div class="space-y-2">
                            <Link :href="`/doctor/${doctor.slug}`"
                                class="block w-full py-3 px-4 bg-indigo-600 text-white text-center rounded-lg hover:bg-indigo-700 transition font-semibold">
                            Ver Disponibilidad
                            </Link>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Información adicional -->
            <div class="mt-16 bg-white rounded-xl shadow-lg p-8">
                <h3 class="text-2xl font-bold text-gray-900 mb-4 text-center">
                    ¿Cómo funciona?
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-8">
                    <div class="text-center">
                        <div class="w-16 h-16 bg-indigo-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <span class="text-2xl font-bold text-indigo-600">1</span>
                        </div>
                        <h4 class="font-semibold text-gray-900 mb-2">Selecciona un Médico</h4>
                        <p class="text-gray-600">Elige al especialista que mejor se adapte a tus necesidades.</p>
                    </div>
                    <div class="text-center">
                        <div class="w-16 h-16 bg-indigo-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <span class="text-2xl font-bold text-indigo-600">2</span>
                        </div>
                        <h4 class="font-semibold text-gray-900 mb-2">Escoge un Horario</h4>
                        <p class="text-gray-600">Revisa la disponibilidad y selecciona la fecha que prefieras.</p>
                    </div>
                    <div class="text-center">
                        <div class="w-16 h-16 bg-indigo-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <span class="text-2xl font-bold text-indigo-600">3</span>
                        </div>
                        <h4 class="font-semibold text-gray-900 mb-2">Confirma tu Cita</h4>
                        <p class="text-gray-600">Recibirás un correo con los detalles de tu cita.</p>
                    </div>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="bg-white mt-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 text-center text-gray-600">
                <p>&copy; 2024 Sistema de Citas - Cirugía General. Todos los derechos reservados.</p>
            </div>
        </footer>
    </div>
</template>