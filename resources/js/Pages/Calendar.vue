<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Scheduler from '@/Components/Scheduler.vue'
import { Head } from '@inertiajs/vue3';


const props = defineProps(['events', 'resources']);


const events = [
    {
      id: 1,
      resource: 'R1',
      start: "2024-09-04T12:00:00",
      end: "2024-09-08T12:00:00",
      text: 'Event 1',
      color: '#1155cc'
    },
    {
      id: 2,
      resource: 'R1',
      start: "2024-09-08T12:00:00",
      end: "2024-09-28T12:00:00",
      text: 'Event 2',
      color: '#6aa84f'
    },
    {
      id: 3,
      resource: 'R3',
      start: "2024-09-04T00:00:00",
      end: "2024-09-08T00:00:00",
      text: 'Event 3',
      color: '#1155cc'
    },
    {
      id: 4,
      resource: 'R3',
      start: "2024-09-10T00:00:00",
      end: "2024-09-18T00:00:00",
      text: 'Event 4',
      color: '#6aa84f'
    },
    {
      id: 5,
      resource: 'R3',
      start: "2024-09-20T00:00:00",
      end: "2024-09-28T00:00:00",
      text: 'Event 5',
      color: '#f1c232'
    }
]



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
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
