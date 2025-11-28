<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

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

                        <div v-if="appointment.status === 'pendiente'" class="flex gap-3">
                            <button
                                @click="acceptAppointment"
                                class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-semibold"
                            >
                                Aceptar Cita
                            </button>
                            <button
                                @click="rejectAppointment"
                                class="px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition font-semibold"
                            >
                                Rechazar Cita
                            </button>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Información del Paciente -->
                    <div class="bg-white rounded-lg shadow-lg p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Información del Paciente</h3>
                        <div class="space-y-3">
                            <div>
                                <label class="text-sm font-semibold text-gray-600">Nombre</label>
                                <p class="text-gray-900 text-lg">{{ appointment.patient_name }}</p>
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
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Información del Médico</h3>
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
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Detalles de la Cita</h3>
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