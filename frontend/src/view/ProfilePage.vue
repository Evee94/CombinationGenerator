<template>
  <Header />
  <div class="container">
    <div class="profile-box">
      <h2>Profile</h2>

      <div class="info-section">
        <div class="info-row">
          <span class="info-label">Name</span>
          <span class="info-value">{{ authStore.user?.name }}</span>
        </div>
        <div class="info-row">
          <span class="info-label">Email</span>
          <span class="info-value">{{ authStore.user?.email }}</span>
        </div>
      </div>

      <h3>Change Password</h3>
      <form @submit.prevent="handleChangePassword">
        <label class="form-label">Current Password</label>
        <input
          v-model="currentPassword"
          type="password"
          class="form-input"
          placeholder="Enter current password"
          required
        />

        <label class="form-label">New Password</label>
        <input
          v-model="newPassword"
          type="password"
          class="form-input"
          placeholder="Min. 8 characters"
          required
        />

        <label class="form-label">Confirm New Password</label>
        <input
          v-model="confirmPassword"
          type="password"
          class="form-input"
          placeholder="Confirm new password"
          required
        />

        <button type="submit" class="submit-btn" :disabled="loading">
          {{ loading ? 'Changing...' : 'Change Password' }}
        </button>
      </form>

      <div v-if="message" class="message" :class="messageType">{{ message }}</div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useAuthStore } from '../store/authStore'
import { authService } from '../services/authService'
import Header from '../components/Header.vue'

const authStore = useAuthStore()
const currentPassword = ref('')
const newPassword = ref('')
const confirmPassword = ref('')
const loading = ref(false)
const message = ref('')
const messageType = ref('success')

const handleChangePassword = async () => {
  message.value = ''

  if (newPassword.value !== confirmPassword.value) {
    message.value = 'New passwords do not match.'
    messageType.value = 'error'
    return
  }

  loading.value = true
  try {
    const response = await authService.changePassword(
      currentPassword.value,
      newPassword.value,
      confirmPassword.value
    )
    message.value = response.message
    messageType.value = 'success'
    currentPassword.value = ''
    newPassword.value = ''
    confirmPassword.value = ''
  } catch (error) {
    const data = error.response?.data
    if (data?.errors) {
      message.value = Object.values(data.errors).flat().join(' ')
    } else {
      message.value = data?.message || 'Failed to change password.'
    }
    messageType.value = 'error'
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
.container {
  min-height: 80vh;
  display: flex;
  flex-direction: column;
  align-items: center;
  padding-top: 70px;
  padding-left: 20px;
  padding-right: 20px;
}

.profile-box {
  background: #fff;
  padding: 40px 50px;
  border-radius: 8px;
  border: 1px solid #000;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
  width: 450px;
  max-width: 100%;
}

h2 {
  font-size: 2rem;
  color: #333;
  margin-top: 0;
  margin-bottom: 24px;
  text-align: center;
}

h3 {
  font-size: 1.3rem;
  color: #333;
  margin-top: 32px;
  margin-bottom: 16px;
  padding-top: 20px;
  border-top: 1px solid #eee;
}

.info-section {
  margin-bottom: 8px;
}

.info-row {
  display: flex;
  justify-content: space-between;
  padding: 10px 0;
  border-bottom: 1px solid #f0f0f0;
}

.info-label {
  font-weight: 600;
  color: #555;
}

.info-value {
  color: #333;
}

.form-label {
  display: block;
  font-size: 1rem;
  font-weight: 600;
  color: #444;
  margin-bottom: 6px;
  margin-top: 16px;
}

.form-input {
  width: 100%;
  padding: 10px 12px;
  font-size: 1rem;
  border: 1px solid #ccc;
  border-radius: 4px;
  outline: none;
  box-sizing: border-box;
  transition: border-color 0.2s;
}

.form-input:focus {
  border-color: #f7d3a6;
}

.submit-btn {
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

.submit-btn:hover:not(:disabled) {
  background: #f0c58a;
}

.submit-btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.message {
  margin-top: 16px;
  padding: 10px;
  border-radius: 4px;
  font-size: 0.9rem;
  text-align: center;
}

.message.success {
  background: #d4edda;
  color: #155724;
  border: 1px solid #c3e6cb;
}

.message.error {
  background: #f8d7da;
  color: #721c24;
  border: 1px solid #f5c6cb;
}
</style>
