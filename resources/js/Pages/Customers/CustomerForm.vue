<template>
  <div class="flex flex-wrap -mb-8 -mr-6 p-8">
    <div class="mt-4 w-full lg:w-full">
      <InputLabel for="name" value="Name" />
      <TextInput v-model="form.name" :error="form.errors.name" class="w-full lg:w-full" label="Name" />
      <InputError class="mt-2" :message="form.errors.name" />
    </div>
    <div class="mt-4 w-full lg:w-full">
      <InputLabel for="email" value="Email" />
      <TextInput v-model="form.email" :error="form.errors.email" class="w-full lg:w-full" label="Email" />
      <InputError class="mt-2" :message="form.errors.email" />
    </div>
    <div class="mt-4 w-full lg:w-full">
      <InputLabel for="phone" value="Phone" />
      <TextInput v-model="form.phone" :error="form.errors.phone" class="w-full lg:w-full" label="Phone" />
      <InputError class="mt-2" :message="form.errors.phone" />
    </div>
    <div class="mt-4 w-full lg:w-full">
      <InputLabel for="address" value="Address" />
      <TextInput v-model="form.address" :error="form.errors.address" class="w-full lg:w-full" label="Address" />
      <InputError class="mt-2" :message="form.errors.address" />
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
