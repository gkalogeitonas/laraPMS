<template>
  <div class="flex justify-end space-x-4 mt-4">
    <EditButton :href="editUrl">
      Edit
    </EditButton>
    <DeleteButton :href="deleteUrl">
      Delete
    </DeleteButton>
  </div>
</template>

<script setup>
import { defineProps } from 'vue';
import EditButton from './EditButton.vue';
import DeleteButton from './DeleteButton.vue';

const props = defineProps({
  editUrl: {
    type: String,
    required: true
  },
  deleteUrl: {
    type: String,
    required: true
  }
});
</script>

<style scoped>
/* No additional styles needed as Tailwind CSS is used */
</style>
