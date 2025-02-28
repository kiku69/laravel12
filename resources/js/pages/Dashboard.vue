<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import AgricultureChart from '@/components/AgricultureChart.vue';

const props = defineProps<{
    agricultureData: Array<{ region: string; land_type: string; value: number }>;
}>();

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' }
];
</script>



<template>
    <Head title="Dashboard" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-4">
            <h2 class="text-xl font-semibold mb-4">Põllumajandusandmed</h2>

            <!-- Lisa joondiagramm -->
            <AgricultureChart :agricultureData="props.agricultureData" class="mb-6" />

            <!-- Andmete tabel -->
            <table class="min-w-full bg-white border border-gray-300 shadow-lg">
                <thead>
                    <tr class="bg-gray-200 text-left">
                        <th class="border px-4 py-2">Maakond</th>
                        <th class="border px-4 py-2">Põllumajandusmaa liik</th>
                        <th class="border px-4 py-2">Väärtus</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="row in props.agricultureData" :key="row.region + row.land_type" class="hover:bg-gray-100">
                        <td class="border px-4 py-2">{{ row.region }}</td>
                        <td class="border px-4 py-2">{{ row.land_type }}</td>
                        <td class="border px-4 py-2">{{ row.value.toLocaleString() }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </AppLayout>
</template>
