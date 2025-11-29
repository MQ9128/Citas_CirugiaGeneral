<script setup>
import { Head, Link, router } from '@inertiajs/vue3';

const props = defineProps({
    appointment: Object
});

const getStatusColor = (status) => {
    const colors = {
        pendiente: 'bg-yellow-100 text-yellow-800 border-yellow-400',
        confirmada: 'bg-green-100 text-green-800 border-green-400',
        rechazada: 'bg-red-100 text-red-800 border-red-400',
        completada: 'bg-blue-100 text-blue-800 border-blue-400'
    };
    return colors[status] || 'bg-gray-100 text-gray-800 border-gray-400';
};

const formatDateTime = (datetime) => {
    return new Date(datetime).toLocaleString('es', {
        weekday: 'long',
        day: 'numeric',
        month: 'long',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};

const acceptAppointment = () => {
    if (confirm('¿Está seguro de que desea aceptar esta cita?')) {
        router.post(route('admin.appointments.accept', props.appointment.id));
    }
};

const rejectAppointment = () => {
    if (confirm('¿Está seguro de que desea rechazar esta cita?')) {
        const notes = prompt('Motivo del rechazo (opcional):');
        router.post(route('admin.appointments.reject', props.appointment.id), {
            notes: notes
        });
    }
};

// NUEVA FUNCIÓN: Completar cita
const completeAppointment = () => {
    if (confirm('¿Confirmar que el paciente asistió a su cita?')) {
        router.post(route('admin.appointments.complete', props.appointment.id));
    }
};

// Verificar si la cita ya ocurrió
const isPastAppointment = () => {
    return new Date(props.appointment.appointment_date) < new Date();
};
</script>

<template>
    <Head title="Detalle de Cita" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Detalle de Cita</h2>
                <Link :href="route('admin.appointments.index')" class="text-indigo-600 hover:text-indigo-800 text-sm">
                    ← Volver a la lista
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <!-- Estado y Acciones -->
                <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
                    <div class="flex justify-between items-center">
                        <div>
                            <h3 class="text-2xl font-bold text-gray-900 mb-2">
                                Cita #{{ appointment.id }}
                            </h3>
                            <div :class="getStatusColor(appointment.status)" class="inline-flex items-center px-4 py-2 rounded-full border-2 font-semibold">
                                {{ appointment.status.toUpperCase() }}
                            </div>
                        </div>

                        <!-- Botones según el estado -->
                        <div class="flex gap-3">
                            <!-- Si está pendiente: Aceptar o Rechazar -->
                            <template v-if="appointment.status === 'pendiente'">
                                <button
                                    @click="acceptAppointment"
                                    class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-semibold flex items-center"
                                >
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    Aceptar Cita
                                </button>
                                <button
                                    @click="rejectAppointment"
                                    class="px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition font-semibold flex items-center"
                                >
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                    Rechazar Cita
                                </button>
                            </template>

                            <!-- Si está confirmada Y la fecha ya pasó: Marcar como completada -->
                            <template v-if="appointment.status === 'confirmada' && isPastAppointment()">
                                <button
                                    @click="completeAppointment"
                                    class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-semibold flex items-center"
                                >
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Marcar como Completada
                                </button>
                            </template>

                            <!-- Si está confirmada PERO la fecha no ha pasado -->
                            <template v-if="appointment.status === 'confirmada' && !isPastAppointment()">
                                <div class="px-6 py-3 bg-gray-100 text-gray-600 rounded-lg font-semibold">
                                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Cita programada para el futuro
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Información del Paciente -->
                    <div class="bg-white rounded-lg shadow-lg p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            Información del Paciente
                        </h3>
                        <div class="space-y-3">
                            <div>
                                <label class="text-sm font-semibold text-gray-600">Nombre</label>
                                <p class="text-gray-900 text-lg">{{ appointment.patient_name }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-semibold text-gray-600">Cédula</label>
                                <p class="text-gray-900">{{ appointment.patient_cedula }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-semibold text-gray-600">Correo Electrónico</label>
                                <p class="text-gray-900">{{ appointment.patient_email }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-semibold text-gray-600">Teléfono</label>
                                <p class="text-gray-900">{{ appointment.patient_phone }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Información del Médico -->
                    <div class="bg-white rounded-lg shadow-lg p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            Información del Médico
                        </h3>
                        <div class="space-y-3">
                            <div>
                                <label class="text-sm font-semibold text-gray-600">Médico</label>
                                <p class="text-gray-900 text-lg">{{ appointment.doctor.name }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-semibold text-gray-600">Especialidad</label>
                                <p class="text-gray-900">{{ appointment.doctor.specialty }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Detalles de la Cita -->
                <div class="bg-white rounded-lg shadow-lg p-6 mt-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        Detalles de la Cita
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="text-sm font-semibold text-gray-600">Fecha y Hora</label>
                            <p class="text-gray-900 text-lg">{{ formatDateTime(appointment.appointment_date) }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-semibold text-gray-600">Duración</label>
                            <p class="text-gray-900 text-lg">{{ appointment.duration_minutes }} minutos</p>
                        </div>
                    </div>

                    <div v-if="appointment.reason" class="mt-6">
                        <label class="text-sm font-semibold text-gray-600">Motivo de la Consulta</label>
                        <p class="text-gray-900 mt-2 p-4 bg-gray-50 rounded-lg">{{ appointment.reason }}</p>
                    </div>

                    <div v-if="appointment.notes" class="mt-6">
                        <label class="text-sm font-semibold text-gray-600">Notas Administrativas</label>
                        <p class="text-gray-900 mt-2 p-4 bg-yellow-50 rounded-lg border border-yellow-200">{{ appointment.notes }}</p>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>