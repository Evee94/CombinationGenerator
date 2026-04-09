import { createApp } from 'vue'
import { createPinia } from 'pinia'
import piniaPluginPersistedstate from 'pinia-plugin-persistedstate'
import { createRouter, createWebHistory } from 'vue-router'
import App from './App.vue'
import './style.css'
import FoodFestivalPlanner from './view/FoodFestivalPlanner.vue'
import ResultPage from './view/ResultPage.vue'
import LoginPage from './view/LoginPage.vue'
import RegisterPage from './view/RegisterPage.vue'
import HistoryPage from './view/HistoryPage.vue'
import ProfilePage from './view/ProfilePage.vue'

const routes = [
  { path: '/', component: FoodFestivalPlanner, meta: { requiresAuth: true } },
  { path: '/result', name: 'ResultPage', component: ResultPage, meta: { requiresAuth: true } },
  { path: '/login', name: 'Login', component: LoginPage, meta: { guest: true } },
  { path: '/register', name: 'Register', component: RegisterPage, meta: { guest: true } },
  { path: '/history', name: 'History', component: HistoryPage, meta: { requiresAuth: true } },
  { path: '/profile', name: 'Profile', component: ProfilePage, meta: { requiresAuth: true } },
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

const pinia = createPinia()
pinia.use(piniaPluginPersistedstate)

const app = createApp(App)
app.use(pinia)
app.use(router)

// Import auth store after pinia is installed
import { useAuthStore } from './store/authStore'

router.beforeEach((to, from, next) => {
  const authStore = useAuthStore()

  // Restore token on axios headers if present
  authStore.restoreToken()

  if (to.meta.requiresAuth && !authStore.isAuthenticated) {
    next({ name: 'Login' })
  } else if (to.meta.guest && authStore.isAuthenticated) {
    next('/')
  } else {
    next()
  }
})

app.mount('#app')
