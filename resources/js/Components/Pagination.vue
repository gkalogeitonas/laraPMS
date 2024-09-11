
<script setup>
import { Link } from '@inertiajs/vue3';
const props = defineProps({
    links: Array
});

</script>
<template>
    <div>
      <Component
        :is="link.url ? Link : 'span'"
        v-for="link in links"
        :href="link.url"
        v-html="link.label"
        class="py-1 px-2 m-1  bg-gray-100"
        :class="{ 'text-gray-900': ! link.url, 'font-bold' : link.active }"
      />
    </div>
  </template>

