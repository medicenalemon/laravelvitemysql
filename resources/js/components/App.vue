<template>
  <div class="container">
    <h1>Mis Tareas</h1>

    <div class="input-group">
      <input 
        v-model="newTask" 
        @keyup.enter="addTask" 
        type="text" 
        placeholder="¿Qué tienes que hacer hoy?" 
      />
      <button @click="addTask">Agregar</button>
    </div>

    <ul class="task-list">
      <li v-for="task in tasks" :key="task.id" :class="{ completed: task.completed }">
        <div class="task-info">
          <input 
            type="checkbox" 
            :checked="task.completed" 
            @change="toggleTask(task)"
          />
          <span>{{ task.title }}</span>
        </div>
        <button class="delete-btn" @click="deleteTask(task.id)">🗑️</button>
      </li>
    </ul>
    
    <p v-if="tasks.length === 0" class="empty-state">No hay tareas pendientes. ¡A disfrutar!</p>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const tasks = ref([]);
const newTask = ref('');


const fetchTasks = async () => {
    try {
        const response = await axios.get('/api/tasks');
        tasks.value = response.data;
    } catch (error) {
        console.error('Error cargando tareas:', error);
    }
};


const addTask = async () => {
    if (newTask.value.trim() === '') return;

    try {
        const response = await axios.post('/api/tasks', {
            title: newTask.value
        });
        tasks.value.unshift(response.data);
        newTask.value = '';
    } catch (error) {
        console.error('Error creando tarea:', error);
    }
};


const toggleTask = async (task) => {
    try {
        task.completed = !task.completed; 
        
        await axios.put(`/api/tasks/${task.id}`, {
            completed: task.completed
        });
    } catch (error) {
        console.error('Error actualizando tarea:', error);
    }
};


const deleteTask = async (id) => {
    if (!confirm('¿Estás seguro de borrar esta tarea?')) return;

    try {
        await axios.delete(`/api/tasks/${id}`);
        tasks.value = tasks.value.filter(t => t.id !== id);
    } catch (error) {
        console.error('Error borrando tarea:', error);
    }
};


onMounted(() => {
    fetchTasks();
});
</script>

<style scoped>

.container {
  max-width: 500px;
  margin: 50px auto;
  font-family: 'Arial', sans-serif;
  padding: 20px;
  background: #f9f9f9;
  border-radius: 10px;
  box-shadow: 0 4px 6px rgba(0,0,0,0.1);
}

h1 { text-align: center; color: #333; }

.input-group { display: flex; gap: 10px; margin-bottom: 20px; }

input[type="text"] {
  flex: 1;
  padding: 10px;
  border: 1px solid #ddd;
  border-radius: 5px;
  outline: none;
}

button {
  padding: 10px 20px;
  background-color: #42b883; /* Verde Vue */
  color: white;
  border: none;
  border-radius: 5px;
  cursor: pointer;
}

button:hover { background-color: #3aa876; }

.task-list { list-style: none; padding: 0; }

.task-list li {
  background: white;
  border-bottom: 1px solid #eee;
  padding: 10px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  transition: all 0.2s;
}

.task-info { display: flex; align-items: center; gap: 10px; }

.completed span { text-decoration: line-through; color: #888; }

.delete-btn {
  background: transparent;
  color: #ff4444;
  padding: 5px;
  font-size: 1.2rem;
}

.delete-btn:hover { background: #ffebeb; }

.empty-state { text-align: center; color: #777; margin-top: 20px; }
</style>