<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    appointments: Object,
    doctors: Array,
    filters: Object
});

const selectedDoctor = ref(props.filters.doctor_id || '');
const selectedStatus = ref(props.filters.status || '');

const applyFilters = () => {
    router.get(route('admin.appointments.index'), {
        doctor_id: selectedDoctor.value,
        status: selectedStatus.value
    }, {
        preserveState: true,
        preserveScroll: true
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

const formatDateTime = (datetime) => {
    return new Date(datetime).toLocaleString('es', {
        weekday: 'short',
        day: '2-digit',
        month: 'short',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};
</script>

<template>

    <Head title="Gestión de Citas" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Gestión de Citas</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Filtros -->
                <div class="bg-white rounded-lg shadow p-6 mb-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Médico</label>
                            <select v-model="selectedDoctor" @change="applyFilters"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                                <option value="">Todos</option>
                                <option v-for="doctor in doctors" :key="doctor.id" :value="doctor.id">
                                    {{ doctor.name }}
                                </option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Estado</label>
                            <select v-model="selectedStatus" @change="applyFilters"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                                <option value="">Todos</option>
                                <option value="pendiente">Pendiente</option>
                                <option value="confirmada">Confirmada</option>
                                <option value="rechazada">Rechazada</option>
                                <option value="completada">Completada</option>
                            </select>
                        </div>

                        <div class="flex items-end">
                            <Link href="/dashboard"
                                class="w-full px-4 py-2 bg-gray-200 text-gray-700 text-center rounded-lg hover:bg-gray-300 transition">
                            Volver al Dashboard
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- Tabla de Citas -->
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Paciente
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Médico
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Fecha y Hora
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Estado
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Acciones
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="appointment in appointments.data" :key="appointment.id" class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ appointment.patient_name }}</div>
                                    <div class="text-sm text-gray-500">{{ appointment.patient_email }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ appointment.doctor.name }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ formatDateTime(appointment.appointment_date)
                                    }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span :class="getStatusColor(appointment.status)"
                                        class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full">
                                        {{ appointment.status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex gap-2">
                                        <Link :href="route('admin.appointments.show', appointment.id)"
                                            class="text-indigo-600 hover:text-indigo-900">
                                        Ver
                                        </Link>
                                        <Link v-if="appointment.status === 'pendiente'"
                                            :href="route('admin.appointments.accept', appointment.id)" method="post"
                                            as="button" class="text-green-600 hover:text-green-900">
                                        Aceptar
                                        </Link>
                                        <Link v-if="appointment.status === 'pendiente'"
                                            :href="route('admin.appointments.reject', appointment.id)" method="post"
                                            as="button" class="text-red-600 hover:text-red-900">
                                        Rechazar
                                        </Link>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <!-- Paginación -->
                    <div v-if="appointments.links.length > 3"
                        class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                        <div class="flex items-center justify-between">
                            <div class="text-sm text-gray-700">
                                Mostrando
                                <span class="font-medium">{{ appointments.from }}</span>
                                a
                                <span class="font-medium">{{ appointments.to }}</span>
                                de
                                <span class="font-medium">{{ appointments.total }}</span>
                                resultados
                            </div>
                            <div class="flex gap-2">
                                <Link v-for="link in appointments.links" :key="link.label" :href="link.url" :class="[
                                    'px-3 py-2 text-sm font-medium rounded-md',
                                    link.active
                                        ? 'bg-indigo-600 text-white'
                                        : 'bg-white text-gray-700 hover:bg-gray-50 border border-gray-300'
                                ]" v-html="link.label" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>