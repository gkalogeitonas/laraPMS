<script setup>
  import { ref } from 'vue';
  import { router } from '@inertiajs/vue3'

  const query = ref('');
  const results = ref([]);

  const autoComplete = () => {
    results.value = [];
    if (query.value.length > 2) {
       router.get('/customers/search', { query: query.value }, {
        preserveState: true,
        onSuccess: (page) => {
          results.value = page.props.results;
        }
      });
    }
  };
</script>
<template>
    <div>
      <input
        type="text"
        placeholder="what are you looking for?"
        v-model="query"
        @keyup="autoComplete"
        class="form-control"
      />
      <div class="panel-footer" v-if="results.length">
        <ul class="list-group">
          <li class="list-group-item" v-for="result in results" :key="result.id">
            {{ result.name }}
          </li>
        </ul>
      </div>
    </div>
</template>


