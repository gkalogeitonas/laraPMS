<script setup>
import { defineProps } from 'vue';
import ActionButtons from '@/Components/ActionButtons.vue';
import NavLink from '@/Components/NavLink.vue';

const props = defineProps({
  room: {
    type: Object,
    required: true
  }
});
</script>

<template>
  <div class="flex justify-center items-center bg-gray-100 p-5 mb-3">
    <span class="mr-7 flex-grow">
        <NavLink :href="route('rooms.show', room.id)" class="mr-7 text-black text-xl">
        <strong>{{ room.name }}</strong>
        </NavLink>
    </span>
    <span class="ml-5 flex-grow"> {{ room.type }}</span>
    <span class="ml-5 flex-grow"> {{ room.status }}</span>
    <ActionButtons class="ml-auto lex-grow"
      :editUrl="route('rooms.edit', room.id)"
      :deleteUrl="route('rooms.destroy', room.id)"
    />
  </div>
</template>

<style scoped>
/* No additional styles needed as Tailwind CSS is used */
</style>
