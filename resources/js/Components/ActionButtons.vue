<template>
  <div class="flex justify-end space-x-4 mt-4">
    <NavLink v-if="showUrl" :href="showUrl"   class=" items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 active:bg-red-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
        <EyeIcon class="w-5 h-5" />
    </NavLink>
    <EditButton v-if="editUrl" :href="editUrl">
        <PencilIcon class="w-5 h-5" />
    </EditButton>
    <DeleteButton v-if="deleteUrl" :href="deleteUrl">
        <TrashIcon class="w-5 h-5" />
    </DeleteButton>
  </div>
</template>

<script setup>
import { defineProps } from 'vue';
import NavLink from '@/Components/NavLink.vue';
import EditButton from '@/Components/EditButton.vue';
import DeleteButton from '@/Components/DeleteButton.vue';
import { EyeIcon, PencilIcon, TrashIcon } from '@heroicons/vue/24/solid'


const props = defineProps({
  editUrl: {
    type: String,
    required: true
  },
  deleteUrl: {
    type: String,
    required: true
  },
  showUrl: {
    type: String,
    required: true
  }
});
</script>

<style scoped>
/* No additional styles needed as Tailwind CSS is used */
</style>
