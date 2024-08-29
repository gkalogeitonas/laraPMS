<script setup>
import { computed } from 'vue';
import NavLink from './NavLink.vue'; // Adjust the import path as necessary

const props = defineProps({
    href: {
        type: String,
        required: true,
    }
});

</script>


<template>
  <NavLink :href="href" class="inline-flex items-center px-4 py-1 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 active:bg-red-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150 m-5">
    Edit
  </NavLink>
</template>


<style scoped>
/* No additional styles needed as Tailwind CSS is used */
</style>
