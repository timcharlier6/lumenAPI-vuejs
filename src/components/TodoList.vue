<template>
	<div>
		<div v-if="!token">
			<h2 v-if="isRegister">Sign Up</h2>
			<h2 v-else>Log In</h2>

			<input v-model="name" placeholder="Name">
			<input v-model="password" type="password" placeholder="Password">
			<button @click="isRegister ? register() : login()">
				{{ isRegister ? "Sign Up" : "Log In" }}
			</button>
			<p @click="isRegister = !isRegister" style="cursor:pointer; color:blue">
			{{ isRegister ? "Already have an account? Log In" : "New user? Sign Up" }}
			</p>
		</div>
		<div v-else>

			<div class="header" id="myDIV">

				<h1>My Todo List</h1>

				<input type="text" id="myInput" v-model="newTodoTitle" placeholder="Title..." />
				<span class="addBtn" @click="addTodo">Add</span>
			</div>
			<div>
				<input v-model="password" type="password" placeholder="Password">
				<button @click="login">Login</button>
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
	</div>
</template>

<script setup>
import { ref, onMounted, watch, nextTick } from 'vue';
import axios from 'axios';

const todos = ref([]);
const loading = ref(true);
const newTodoTitle = ref('');
const editingTodoId = ref(null);
const token = ref(localStorage.getItem('token' || ''));


onMounted(() => {
  if (token.value) fetchTodos();
});

const fetchTodos = () => {
  loading.value = true;
  axios.get('http://localhost:8000/todos', { headers: authHeader() })
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
    axios.post('http://localhost:8000/todos', { title: newTodoTitle.value },  { headers: authHeader() })
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
    .put(`http://localhost:8000/todos/${todo.id}`, 
      {completed: !todo.completed},
	 { headers: authHeader() }
    )
    .then(() => {
      fetchTodos();
    })
    .catch((error) => {
      console.error('Error updating todo:', error);
    });
};

const deleteTodo = (id) => {
  axios
    .delete(`http://localhost:8000/todos/${id}`, { headers: authHeader() })
    .then(() => {
      fetchTodos();
    })
    .catch((error) => {
      console.error('Error deleting todo:', error);
    });
};


const authHeader = () => ({
	Authorization: `Bearer ${token.value}`,
});


const logout = () => {
	token.value = '';
	localStorage.removeItem('token');
};
</script>
