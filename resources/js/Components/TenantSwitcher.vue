<script setup>
import { ref, watch } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { useForm } from "@inertiajs/vue3";

const props = defineProps({
  tenants: {
    type: Array,
    required: true
  },
  activeTenant: {
    type: Object,
    required: false,
    default: null
  }
});

const selectedTenant = ref(props.activeTenant ? props.activeTenant.id : null);

const form = useForm({
    tenant_id: selectedTenant.value
});

const switchTenant = () => {
  form.tenant_id = selectedTenant.value;
  form.post('/switch-tenant', {
    preserveState: true,
    replace: true,
    onSuccess: () => {
    },
    onError: (errors) => {
      console.error('Error switching tenant:', errors);
    }
  });
};
</script>


<template>
  <div>
    <form @submit.prevent="switchTenant">
        <label for="tenant-switcher">Switch Company: </label>
        <select id="tenant-switcher" v-model="selectedTenant" @change="switchTenant">
        <option v-for="tenant in tenants" :key="tenant.id" :value="tenant.id">
            {{ tenant.name }}
        </option>
        </select>
    </form>
  </div>
</template>
