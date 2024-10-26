<template>
    <select v-model="selectedRoom" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
        <option value="">Select a room</option>
        <template v-for="property in groupedRoomsByProperty" :key="property.id">
            <optgroup :label="property.name">
                <option v-for="room in property.rooms" :value="room.id" :key="room.id">
                    {{ room.name }}
                </option>
            </optgroup>
        </template>
    </select>
</template>

<script setup>
import { computed, defineProps, defineEmits } from 'vue';

const props = defineProps({
    rooms: {
        type: Array,
        required: true,
    },
    modelValue: {
        type: [String, Number],
        default: '',
    },
});

const emits = defineEmits(['update:modelValue']);

const selectedRoom = computed({
    get: () => props.modelValue,
    set: (value) => emits('update:modelValue', value),
});

const groupedRoomsByProperty = computed(() => {
    const grouped = props.rooms.reduce((acc, room) => {
        const property = room.property;
        if (!acc[property.id]) {
            acc[property.id] = {
                id: property.id,
                name: property.name,
                rooms: [],
            };
        }
        acc[property.id].rooms.push(room);
        return acc;
    }, {});
    return Object.values(grouped);
});
</script>
