<template>
  <div class="flex flex-wrap -mb-8 -mr-6 p-8">
    <div class="mt-4 w-full lg:w-full">
      <InputLabel for="name" value="Name" />
      <TextInput v-model="form.name" :error="form.errors.name" class="w-full lg:w-full" label="Name" />
      <InputError class="mt-2" :message="form.errors.name" />
    </div>
    <div class="mt-4 w-full lg:w-full">
      <InputLabel for="color" value="Color" />
      <input type="color" v-model="form.color" id="color" class="w-full lg:w-full border-gray-300 rounded-md shadow-sm" />
      <InputError class="mt-2" :message="form.errors.color" />
    </div>
  </div>
</template>

<script setup>
import { defineProps } from 'vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';

const props = defineProps({
  form: {
    type: Object,
    required: true,
  },
});
</script>
