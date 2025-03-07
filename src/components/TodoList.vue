<template>
  <div>
	<div class="header" id="myDIV">

    <h1>Todo List</h1>

    <input type="text" id="myInput" v-model="newTodoTitle" placeholder="Title..." />
    <span class="addBtn" @click="addTodo">Add</span>
	</div>
    <p v-if="loading">Loading...</p>
    <ul id="myUL" v-else>
      <li
        v-for="todo in todos"
        :key="todo.id"
        :data-id="todo.id"
        :data-completed="todo.completed"
        :class="{ checked: todo.completed }"
        @click="toggleCompleted(todo)"
        @dblclick="editTodo(todo)"
      >
        <span v-if="editingTodoId !== todo.id">{{ todo.title }}</span>
        <input
          v-else
          type="text"
          v-model="editedTitle"
          @blur="saveTodo(todo)"
          @keyup.enter="saveTodo(todo)"
          ref="editInput"
        />
        <span class="close" @click.stop="deleteTodo(todo.id)">&#xD7;</span>
      </li>
    </ul>
  </div>
</template>

<script setup>
import { ref, onMounted, watch, nextTick } from 'vue';
import axios from 'axios';

const todos = ref([]);
const loading = ref(true);
const newTodoTitle = ref('');
const editingTodoId = ref(null);
const editedTitle = ref('');
const editInput = ref(null);

onMounted(() => {
  fetchTodos();
});

const fetchTodos = () => {
  loading.value = true;
  axios.get('http://localhost:8000/todos')
    .then(response => {
      todos.value = response.data;
      nextTick(()=>{loading.value = false;});
    })
    .catch(error => {
      console.error('Error fetching todos:', error);
      nextTick(()=>{loading.value = false;});
    });
};

const addTodo = () => {
  if (newTodoTitle.value.trim()) {
    axios.post('http://localhost:8000/todos', { title: newTodoTitle.value })
      .then(() => {
        newTodoTitle.value = '';
        fetchTodos();
      })
      .catch(error => {
        console.error('Error adding todo:', error);
      });
  }
};

const toggleCompleted = (todo) => {
  axios
    .put(`http://localhost:8000/todos/${todo.id}`, {
      ...todo,
      completed: !todo.completed,
    })
    .then(() => {
      fetchTodos();
    })
    .catch((error) => {
      console.error('Error updating todo:', error);
    });
};

const deleteTodo = (id) => {
  axios
    .delete(`http://localhost:8000/todos/${id}`)
    .then(() => {
      fetchTodos();
    })
    .catch((error) => {
      console.error('Error deleting todo:', error);
    });
};

const editTodo = (todo) => {
  editingTodoId.value = todo.id;
  editedTitle.value = todo.title;
  nextTick(() => {
    if (editInput.value) {
      editInput.value.focus();
    }
  });
};

const saveTodo = (todo) => {
  editingTodoId.value = null;
  axios
    .put(`http://localhost:8000/todos/${todo.id}`, {
      ...todo,
      title: editedTitle.value,
    })
    .then(() => {
      fetchTodos();
    })
    .catch((error) => {
      console.error('Error updating todo:', error);
    });
};
</script>
