<template>
	<div class="container">
		<h1>Todo List</h1>
		<p v-if="loading">Loading...</p>
		<ul v-else>
			<li v-for="todo in todos" :key="todo.id">
				<input type="checkbox" v-model="todo.completed" @change="updateTodo(todo)" />
				<span class="todo" :class="{ 'completed': todo.completed }">{{ todo.title }}</span>
				<button @click="deleteTodo(todo.id)">❌</button>
			</li>
		</ul>
		<div class="add-todo">
			<input type="text" v-model="newTodoTitle" placeholder="Add new todo" />
			<button @click="addTodo">Add</button>
		</div>
	</div>
</template>

<script setup>
import { ref, onMounted, nextTick } from 'vue';
import axios from 'axios';

const todos = ref([]);
const loading = ref(true);
const newTodoTitle = ref('');

onMounted(() => {
	fetchTodos();
});

const fetchTodos = () => {
	loading.value = true;
	axios.get('http://localhost:8000/api/todos')
		.then(response => {
			todos.value = response.data;
			nextTick(() => {
				loading.value = false;
			});
		})
		.catch(error => {
			console.error('Error fetching todos:', error);
			nextTick(() => {
				loading.value = false;
			});
		});
};

const addTodo = () => {
	if (newTodoTitle.value.trim()) {
		axios.post('http://localhost:8000/api/todos', { title: newTodoTitle.value })
			.then(() => {
				newTodoTitle.value = '';
				fetchTodos();
			})
			.catch(error => {
				console.error('Error adding todo:', error);
			});
	}
};

const updateTodo = (todo) => {
	axios.put(`http://localhost:8000/api/todos/${todo.id}`, todo)
		.catch(error => {
			console.error('Error updating todo:', error);
		});
};

const deleteTodo = (id) => {
	axios.delete(`http://localhost:8000/api/todos/${id}`)
		.then(() => {
			fetchTodos();
		})
		.catch(error => {
			console.error('Error deleting todo:', error);
		});
};
</script>
