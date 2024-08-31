<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    href: {
        type: String,
        required: true,
    }
});

</script>


<template>
  <Link :href="href" class="inline-flex items-center px-4 py-1 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 active:bg-red-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150 m-5">
    <slot/>
  </Link>
</template>


<style scoped>
/* No additional styles needed as Tailwind CSS is used */
</style>
