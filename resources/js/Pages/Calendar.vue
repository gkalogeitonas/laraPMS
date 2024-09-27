<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Scheduler from '@/Components/Scheduler.vue'
import { Head } from '@inertiajs/vue3';


const props = defineProps(['events', 'resources']);

</script>

<template>
    <Head title="Calendar" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Calendar</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <Scheduler :events="events" :resources="resources" />
                    <Scheduler :events="events" :resources="resources" />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
