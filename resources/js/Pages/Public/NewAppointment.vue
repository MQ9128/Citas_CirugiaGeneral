<script setup>
import { ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    doctor: Object,
    appointmentDate: String
});

// Convertir la fecha a formato ISO sin timezone
const parseAppointmentDate = (dateString) => {
    const date = new Date(dateString);
    // Formato: YYYY-MM-DD HH:mm:ss
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    const hours = String(date.getHours()).padStart(2, '0');
    const minutes = String(date.getMinutes()).padStart(2, '0');
    const seconds = '00';
    
    return `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
};

const form = useForm({
    doctor_id: props.doctor.id,
    patient_name: '',
    patient_email: '',
    patient_phone: '',
    reason: '',
    appointment_date: parseAppointmentDate(props.appointmentDate) // ← Cambio aquí
});

const submit = () => {
    form.post('/appointments', {
        preserveScroll: true,
        onSuccess: (response) => {
            console.log('Cita creada exitosamente');
        },
        onError: (errors) => {
            console.error('Errores:', errors);
        }
    });
};

const formatDateTime = (datetime) => {
    const date = new Date(datetime);
    return date.toLocaleString('es', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};
</script>

<template>
    <Head title="Confirmar Cita" />

    <div class="min-h-screen bg-gradient-to-br from-indigo-50 to-blue-100">
        <header class="bg-white shadow-sm">
            <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                <Link :href="`/doctor/${doctor.slug}`" class="inline-flex items-center text-indigo-600 hover:text-indigo-800">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Volver
                </Link>
            </div>
        </header>

        <main class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="bg-white rounded-xl shadow-lg p-8">
                <h1 class="text-3xl font-bold text-gray-900 mb-6 text-center">Confirmar Cita Médica</h1>

                <!-- Resumen de la cita -->
                <div class="bg-indigo-50 border border-indigo-200 rounded-lg p-6 mb-8">
                    <h2 class="text-lg font-semibold text-indigo-900 mb-3">Detalles de la Cita</h2>
                    <div class="space-y-2 text-gray-700">
                        <p><span class="font-semibold">Médico:</span> {{ doctor.name }}</p>
                        <p><span class="font-semibold">Especialidad:</span> {{ doctor.specialty }}</p>
                        <p><span class="font-semibold">Fecha y Hora:</span> {{ formatDateTime(appointmentDate) }}</p>
                        <p><span class="font-semibold">Duración:</span> 20 minutos</p>
                    </div>
                </div>

                <!-- Formulario -->
                <form @submit.prevent="submit" class="space-y-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Nombre Completo *
                        </label>
                        <input
                            v-model="form.patient_name"
                            type="text"
                            required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                            placeholder="Ej: Juan Pérez García"
                        />
                        <div v-if="form.errors.patient_name" class="text-red-600 text-sm mt-1">
                            {{ form.errors.patient_name }}
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Correo Electrónico *
                        </label>
                        <input
                            v-model="form.patient_email"
                            type="email"
                            required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                            placeholder="correo@ejemplo.com"
                        />
                        <div v-if="form.errors.patient_email" class="text-red-600 text-sm mt-1">
                            {{ form.errors.patient_email }}
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Teléfono *
                        </label>
                        <input
                            v-model="form.patient_phone"
                            type="tel"
                            required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                            placeholder="300 123 4567"
                        />
                        <div v-if="form.errors.patient_phone" class="text-red-600 text-sm mt-1">
                            {{ form.errors.patient_phone }}
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Motivo de Consulta
                        </label>
                        <textarea
                            v-model="form.reason"
                            rows="4"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                            placeholder="Describe brevemente el motivo de tu consulta..."
                        ></textarea>
                        <div v-if="form.errors.reason" class="text-red-600 text-sm mt-1">
                            {{ form.errors.reason }}
                        </div>
                    </div>

                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <p class="text-sm text-yellow-800">
                            <strong>Nota:</strong> Tu cita quedará en estado pendiente hasta que el médico la confirme. 
                            Recibirás un correo electrónico con los detalles y el estado de tu cita.
                        </p>
                    </div>

                    <div class="flex gap-4">
                        <Link
                            :href="`/doctor/${doctor.slug}`"
                            class="flex-1 py-3 px-6 bg-gray-200 text-gray-700 text-center rounded-lg hover:bg-gray-300 transition font-semibold"
                        >
                            Cancelar
                        </Link>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="flex-1 py-3 px-6 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition font-semibold disabled:opacity-50"
                        >
                            {{ form.processing ? 'Agendando...' : 'Confirmar Cita' }}
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </div>
</template>