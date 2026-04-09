<template>
  <header class="header">
    <div class="header-left">
      <button class="back-btn" @click="goBack">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M15 19L8 12L15 5" stroke="#333" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        <span>Back</span>
      </button>
    </div>
    <div class="header-right">
      <router-link to="/history" class="nav-link">History</router-link>
      <router-link to="/profile" class="nav-link">{{ authStore.user?.name }}</router-link>
      <button class="logout-btn" @click="handleLogout">Logout</button>
    </div>
  </header>
</template>

<script setup>
import { useRouter } from 'vue-router'
import { useAuthStore } from '../store/authStore'

const router = useRouter()
const authStore = useAuthStore()

function goBack() {
  router.back()
}

async function handleLogout() {
  await authStore.logout()
  router.push({ name: 'Login' })
}
</script>

<style scoped>
.header {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  padding: 12px 24px;
  background: #f7d3a6;
  z-index: 1000;
  display: flex;
  align-items: center;
  justify-content: space-between;
  box-sizing: border-box;
}
.header-left {
  display: flex;
  align-items: center;
}
.header-right {
  display: flex;
  align-items: center;
  gap: 16px;
}
.back-btn {
  display: flex;
  align-items: center;
  background: none;
  border: none;
  color: #333;
  font-size: 1.1rem;
  cursor: pointer;
  padding: 4px 12px 4px 0;
  transition: background 0.2s;
}
.back-btn:hover {
  background: #f0c58a;
  border-radius: 6px;
}
.back-btn svg {
  margin-right: 8px;
}
.nav-link {
  color: #333;
  text-decoration: none;
  font-weight: 600;
  font-size: 0.95rem;
  padding: 4px 10px;
  border-radius: 4px;
  transition: background 0.2s;
}
.nav-link:hover {
  background: #f0c58a;
}
.user-name {
  color: #555;
  font-size: 0.9rem;
  font-weight: 500;
}
.logout-btn {
  background: none;
  border: 1px solid #999;
  color: #333;
  padding: 4px 14px;
  border-radius: 4px;
  font-size: 0.9rem;
  cursor: pointer;
  transition: all 0.2s;
}
.logout-btn:hover {
  background: #333;
  color: #fff;
  border-color: #333;
}
</style>