<template>
  <div class="flex flex-wrap -mb-8 -mr-6 p-8">
    <div class="mt-4 w-full lg:w-full">
      <InputLabel for="name" value="Name" />
      <TextInput v-model="form.name" :error="form.errors.name" class="w-full lg:w-full" label="Name" />
      <InputError class="mt-2" :message="form.errors.name" />
    </div>
    <div class="mt-4 w-full lg:w-full">
      <InputLabel for="address" value="Address" />
      <TextInput v-model="form.address" :error="form.errors.address" class="w-full lg:w-full" label="Address" />
      <InputError class="mt-2" :message="form.errors.address" />
    </div>
    <div class="mt-4 w-full lg:w-full">
      <InputLabel for="description" value="Description" />
      <TextInput v-model="form.description" :error="form.errors.description" class="w-full lg:w-full" label="Description" />
      <InputError class="mt-2" :message="form.errors.description" />
    </div>
    <div class="mt-4 w-full lg:w-full">
      <InputLabel for="type" value="Type" />
      <select v-model="form.type" :error="form.errors.type" class="w-full lg:w-full" label="Type">
        <option value="house">House</option>
        <option value="apartment">Apartment</option>
        <option value="condo">Condo</option>
      </select>
      <InputError class="mt-2" :message="form.errors.type" />
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
