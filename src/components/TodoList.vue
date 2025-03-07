<template>
  <div>
    <h1>Todo List</h1>
    <p v-if="loading">Loading...</p>
    <ul v-else>
      <li v-for="todo in todos" :key="todo.id">
        {{ todo.title }} - {{ todo.completed ? 'Completed' : 'Pending' }}
      </li>
    </ul>
  </div>
</template>

<script setup>
import { ref, onMounted, nextTick } from 'vue';
import axios from 'axios';

const todos = ref([]);
const loading = ref(true);

onMounted(() => {
  axios.get('http://localhost:8000/api/todos')
    .then(response => {
      console.log('API Data:', response.data);
      todos.value = response.data;
      console.log('Todos Value:', todos.value);
      nextTick(() => {
        loading.value = false;
        console.log("Loading Value after nextTick", loading.value);
      })

    })
    .catch(error => {
      console.error('Error fetching todos:', error);
      nextTick(() => {
        loading.value = false;
        console.log("Loading Value after nextTick(catch)", loading.value);
      });
    });
});
</script>