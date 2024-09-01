<script setup>
import { defineProps } from 'vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';

const props = defineProps({
    form: {
        type: Object,
        required: true
    },
    types: {
        type: Array,
        required: true
    },
    statuses: {
        type: Array,
        required: true
    }
});
</script>

<template>
    <div>
        <div class="mb-4">
            <InputLabel for="name" value="Room Name" />
            <input type="text" id="name" v-model="form.name" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
            <InputError :message="form.errors.name" class="mt-2" />
        </div>
        <div class="mb-4">
            <InputLabel for="type" value="Room Type" />
            <select id="type" v-model="form.type" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
                <option v-for="type in types" :key="type" :value="type">{{ type }}</option>
            </select>
            <InputError :message="form.errors.type" class="mt-2" />
        </div>
        <div class="mb-4">
            <InputLabel for="status" value="Room Status" />
            <select id="status" v-model="form.status" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
                <option v-for="status in statuses" :key="status" :value="status">{{ status }}</option>
            </select>
            <InputError :message="form.errors.status" class="mt-2" />
        </div>
        <div class="mb-4">
            <InputLabel for="description" value="Description" />
            <textarea id="description" v-model="form.description" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" rows="3"></textarea>
            <InputError :message="form.errors.description" class="mt-2" />
        </div>
    </div>
</template>
