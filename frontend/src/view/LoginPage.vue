<template>
  <div class="auth-container">
    <div class="auth-box">
      <h1 class="auth-title">Login</h1>
      <form @submit.prevent="handleLogin">
        <label class="auth-label">Email</label>
        <input
          v-model="email"
          type="email"
          class="auth-input"
          placeholder="Enter your email"
          required
        />

        <label class="auth-label">Password</label>
        <input
          v-model="password"
          type="password"
          class="auth-input"
          placeholder="Enter your password"
          required
        />

        <button type="submit" class="auth-btn" :disabled="loading">
          {{ loading ? 'Logging in...' : 'Login' }}
        </button>
      </form>

      <div v-if="errorMessage" class="auth-error">{{ errorMessage }}</div>

      <p class="auth-switch">
        Don't have an account?
        <router-link to="/register">Register here</router-link>
      </p>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../store/authStore'

const email = ref('')
const password = ref('')
const errorMessage = ref('')
const loading = ref(false)
const router = useRouter()
const authStore = useAuthStore()

const handleLogin = async () => {
  errorMessage.value = ''
  loading.value = true
  try {
    await authStore.login(email.value, password.value)
    router.push('/')
  } catch (error) {
    errorMessage.value =
      'Login failed. Please try again.'
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
.auth-container {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 20px;
}
.auth-box {
  background: #fff;
  padding: 40px 50px;
  border-radius: 8px;
  border: 1px solid #000;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
  width: 400px;
  max-width: 100%;
}
.auth-title {
  font-size: 2rem;
  margin-bottom: 24px;
  text-align: center;
  color: #333;
}
.auth-label {
  display: block;
  font-size: 1rem;
  font-weight: 600;
  color: #444;
  margin-bottom: 6px;
  margin-top: 16px;
}
.auth-input {
  width: 100%;
  padding: 10px 12px;
  font-size: 1rem;
  border: 1px solid #ccc;
  border-radius: 4px;
  outline: none;
  box-sizing: border-box;
  transition: border-color 0.2s;
}
.auth-input:focus {
  border-color: #f7d3a6;
}
.auth-btn {
  width: 100%;
  margin-top: 24px;
  padding: 12px;
  font-size: 1.1rem;
  font-weight: 600;
  background: #f7d3a6;
  color: #333;
  border: 1px solid #e0b97a;
  border-radius: 6px;
  cursor: pointer;
  transition: background 0.2s;
}
.auth-btn:hover:not(:disabled) {
  background: #f0c58a;
}
.auth-btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}
.auth-error {
  margin-top: 16px;
  padding: 10px;
  background: #f8d7da;
  color: #721c24;
  border: 1px solid #f5c6cb;
  border-radius: 4px;
  font-size: 0.9rem;
  text-align: center;
}
.auth-switch {
  margin-top: 20px;
  text-align: center;
  color: #666;
  font-size: 0.95rem;
}
.auth-switch a {
  color: #d4913a;
  font-weight: 600;
  text-decoration: none;
}
.auth-switch a:hover {
  text-decoration: underline;
}
</style>
