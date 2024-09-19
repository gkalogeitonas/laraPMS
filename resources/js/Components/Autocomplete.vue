<script setup>
  import { ref } from 'vue';
  import { router } from '@inertiajs/vue3'

  const props = defineProps({
    apiEndpoint: {
        type: String,
        required: true
    }
  });

  const query = ref('');
  const results = ref([]);

  const autoComplete = () => {
    results.value = [];
    if (query.value.length > 2) {
        axios.get(props.apiEndpoint, { query: query.value }, {
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        }).then(response => {
            results.value = response.data;
      });
    }
  };

const selectResult = (result) => {
  query.value = result.name;
  results.value = [];
};
</script>
<template>
    <div class="relative">
      <input
        type="text"
        placeholder="what are you looking for?"
        v-model="query"
        @keyup="autoComplete"
        class="form-control"
      />
      <div class="absolute left-0 right-0 bg-white border border-gray-300 mt-1 z-10" v-if="results.length">
        <ul class="list-group">
          <li
            class="list-group-item cursor-pointer hover:bg-gray-200"
            v-for="result in results"
            :key="result.id"
            @click="selectResult(result)"
          >
            {{ result.name }}
          </li>
        </ul>
      </div>
    </div>
  </template>


