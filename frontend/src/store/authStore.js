import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { authService } from '../services/authService'
import api from '../config/api'

export const useAuthStore = defineStore('auth', () => {
  const user = ref(null)
  const token = ref(null)

  const isAuthenticated = computed(() => !!token.value)

  function setAuth(userData, authToken) {
    user.value = userData
    token.value = authToken
    api.defaults.headers.common['Authorization'] = `Bearer ${authToken}`
  }

  function clearAuth() {
    user.value = null
    token.value = null
    delete api.defaults.headers.common['Authorization']
  }

  // Restore token on the axios instance (called on app startup)
  function restoreToken() {
    if (token.value) {
      api.defaults.headers.common['Authorization'] = `Bearer ${token.value}`
    }
  }

  async function register(name, email, password, passwordConfirmation) {
    const response = await authService.register(name, email, password, passwordConfirmation)
    setAuth(response.data.user, response.data.token)
    return response
  }

  async function login(email, password) {
    const response = await authService.login(email, password)
    setAuth(response.data.user, response.data.token)
    return response
  }

  async function logout() {
    try {
      await authService.logout()
    } finally {
      clearAuth()
    }
  }

  async function fetchUser() {
    try {
      const response = await authService.getUser()
      user.value = response.data
      return response
    } catch {
      clearAuth()
      throw new Error('Session expired')
    }
  }

  return {
    user,
    token,
    isAuthenticated,
    setAuth,
    clearAuth,
    restoreToken,
    register,
    login,
    logout,
    fetchUser,
  }
}, {
  persist: {
    paths: ['user', 'token'],
  },
})
