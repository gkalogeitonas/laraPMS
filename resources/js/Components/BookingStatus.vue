<template>
  <span :class="['px-2', 'inline-flex', 'text-xs', 'leading-5', 'font-semibold', 'rounded-full']" :style="{ backgroundColor: booking_status.color }">
    {{ booking_status.name }}
  </span>
</template>

<script setup>
import { defineProps } from 'vue';

const props = defineProps({
  booking_status: {
    type: Object,
    required: true,
  }
});
</script>

<style scoped>
/* Add any additional styling if needed */
</style>
