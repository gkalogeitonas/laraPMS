<script setup>
import { ref } from 'vue';
import axios from 'axios';

const props = defineProps({
  apiEndpoint: {
    type: String,
    required: true
  },
  paramName: {
    type: String,
    required: true,
    default: 'query'
  }
});

const emit = defineEmits(['update:customer']);


const query = ref('');
const results = ref([]);

const autoComplete = () => {
  results.value = [];
  if (query.value.length > 2) {
    axios.get(props.apiEndpoint, {
      params: { [props.paramName]: query.value },
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
  emit('update:customer', result);
};
</script>
<template>
  <div class="relative">
    <input
      type="text"
      placeholder="Search..."
      v-model="query"
      @keyup="autoComplete"
      class="form-control"
    />
    <div class="absolute left-0 right-0 bg-white border border-gray-300 mt-1 p-2 z-10" v-if="results.length">
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
