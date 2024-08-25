<template>
  <form :action="href" method="POST" @submit.prevent="submit">
    <input type="hidden" name="_method" value="DELETE">
    <input type="hidden" name="_token" :value="csrfToken">
    <button
      type="submit"
      class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 active:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150"
    >
      <slot />
    </button>
  </form>
</template>

<script setup>
import { ref, computed } from 'vue';

const props = defineProps({
  href: {
    type: String,
    required: true,
  },
});

const csrfToken = computed(() => document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

const submit = (event) => {
  if (confirm('Are you sure you want to delete this item?')) {
    event.target.submit();
  }
};
</script>
