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
            <div class="max-w-10xl mx-auto sm:px-2 lg:px-2">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <Scheduler :events="events.data" :resources="resources.data" />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
