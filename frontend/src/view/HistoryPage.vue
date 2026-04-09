<template>
  <Header />
  <div class="container">
    <h2>Generation History</h2>

    <div v-if="loading" class="loading">Loading history...</div>

    <div v-else-if="histories.length === 0" class="empty-state">
      <p>No generation history yet.</p>
      <router-link to="/" class="go-generate-btn">Generate Combinations</router-link>
    </div>

    <div v-else class="history-list">
      <div v-for="history in histories" :key="history.id" class="history-card">
        <div class="history-info">
          <h3 class="history-name">{{ history.name }}</h3>
          <span class="history-date">{{ formatDate(history.created_at) }}</span>
          <span class="history-count">{{ history.combination_count }} combinations</span>
        </div>
        <div class="history-params">
          <span v-for="(param, idx) in history.parameters" :key="idx" class="param-tag">
            {{ param.parameter }}: {{ param.values.join(', ') }}
          </span>
        </div>
        <div class="history-actions">
          <button @click="loadHistory(history)" class="action-btn load-btn">Load</button>
          <button @click="confirmDelete(history.id)" class="action-btn delete-btn">Delete</button>
        </div>
      </div>

      <div v-if="pagination.lastPage > 1" class="pagination">
        <button
          v-for="page in pagination.lastPage"
          :key="page"
          @click="fetchHistories(page)"
          :class="['page-btn', { active: page === pagination.currentPage }]"
        >
          {{ page }}
        </button>
      </div>
    </div>

    <div v-if="errorMessage" class="error-message">{{ errorMessage }}</div>

    <!-- Delete Confirmation Modal -->
    <div v-if="showDeleteModal" class="modal-overlay" @click.self="cancelDelete">
      <div class="modal-box">
        <h3 class="modal-title">Delete History</h3>
        <p class="modal-message">Are you sure you want to delete this history entry? This action cannot be undone.</p>
        <div class="modal-actions">
          <button @click="cancelDelete" class="modal-btn cancel-btn">Cancel</button>
          <button @click="executeDelete" class="modal-btn confirm-btn" :disabled="deleting">
            {{ deleting ? 'Deleting...' : 'Delete' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { historyService } from '../services/historyService'
import { useCombinationStore } from '../store/combinationStore'
import { useParameterStore } from '../store/parameterStore'
import Header from '../components/Header.vue'

const router = useRouter()
const combinationStore = useCombinationStore()
const parameterStore = useParameterStore()

const histories = ref([])
const loading = ref(true)
const errorMessage = ref('')
const showDeleteModal = ref(false)
const deleteTargetId = ref(null)
const deleting = ref(false)
const pagination = ref({
  currentPage: 1,
  lastPage: 1,
})

const fetchHistories = async (page = 1) => {
  loading.value = true
  errorMessage.value = ''
  try {
    const response = await historyService.getAll(page)
    histories.value = response.data.data
    pagination.value = {
      currentPage: response.data.current_page,
      lastPage: response.data.last_page,
    }
  } catch (error) {
    errorMessage.value = 'Failed to load history. Please try again.'
  } finally {
    loading.value = false
  }
}

const loadHistory = (history) => {
  // Restore parameters to the parameter store
  const params = history.parameters.map(p => ({ values: p.values }))
  const names = history.parameters.map(p => p.parameter)
  parameterStore.setParameters(params, names)

  // Restore combinations
  combinationStore.setCombinations(history.combinations)

  // Navigate to result page
  router.push({ name: 'ResultPage' })
}

const confirmDelete = (id) => {
  deleteTargetId.value = id
  showDeleteModal.value = true
}

const cancelDelete = () => {
  showDeleteModal.value = false
  deleteTargetId.value = null
}

const executeDelete = async () => {
  if (!deleteTargetId.value) return
  deleting.value = true
  try {
    await historyService.remove(deleteTargetId.value)
    histories.value = histories.value.filter(h => h.id !== deleteTargetId.value)
    showDeleteModal.value = false
    deleteTargetId.value = null
  } catch (error) {
    errorMessage.value = 'Failed to delete history entry.'
  } finally {
    deleting.value = false
  }
}

const formatDate = (dateStr) => {
  return new Date(dateStr).toLocaleString('en-MY', { timeZone: 'Asia/Kuala_Lumpur' })
}

onMounted(() => {
  fetchHistories()
})
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

h2 {
  font-size: 2rem;
  color: #333;
  margin-bottom: 24px;
}

.loading {
  font-size: 1.1rem;
  color: #666;
  padding: 40px;
}

.empty-state {
  text-align: center;
  padding: 40px;
  color: #666;
}

.go-generate-btn {
  display: inline-block;
  margin-top: 16px;
  padding: 10px 24px;
  background: #f7d3a6;
  color: #333;
  border-radius: 6px;
  text-decoration: none;
  font-weight: 600;
  border: 1px solid #e0b97a;
  transition: background 0.2s;
}

.go-generate-btn:hover {
  background: #f0c58a;
}

.history-list {
  width: 100%;
  max-width: 800px;
}

.history-card {
  background: #fff;
  border: 1px solid #ddd;
  border-radius: 8px;
  padding: 20px;
  margin-bottom: 16px;
  transition: box-shadow 0.2s;
}

.history-card:hover {
  box-shadow: 0 2px 12px rgba(0, 0, 0, 0.1);
}

.history-info {
  display: flex;
  align-items: baseline;
  gap: 16px;
  flex-wrap: wrap;
  margin-bottom: 12px;
}

.history-name {
  font-size: 1.2rem;
  color: #333;
  margin: 0;
}

.history-date {
  font-size: 0.85rem;
  color: #888;
}

.history-count {
  font-size: 0.85rem;
  color: #666;
  background: #f0f0f0;
  padding: 2px 8px;
  border-radius: 12px;
}

.history-params {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
  margin-bottom: 12px;
}

.param-tag {
  font-size: 0.8rem;
  background: #fef3e2;
  color: #8a6914;
  padding: 4px 10px;
  border-radius: 12px;
  border: 1px solid #f7d3a6;
}

.history-actions {
  display: flex;
  gap: 10px;
}

.action-btn {
  padding: 6px 16px;
  border-radius: 4px;
  font-size: 0.9rem;
  cursor: pointer;
  border: 1px solid #ccc;
  transition: all 0.2s;
}

.load-btn {
  background: #f7d3a6;
  color: #333;
  border-color: #e0b97a;
}

.load-btn:hover {
  background: #f0c58a;
}

.delete-btn {
  background: #fff;
  color: #d9534f;
  border-color: #d9534f;
}

.delete-btn:hover {
  background: #f8d7da;
}

.pagination {
  display: flex;
  justify-content: center;
  gap: 8px;
  margin-top: 24px;
}

.page-btn {
  padding: 6px 12px;
  border: 1px solid #ccc;
  border-radius: 4px;
  background: #fff;
  cursor: pointer;
  transition: all 0.2s;
}

.page-btn.active {
  background: #f7d3a6;
  border-color: #e0b97a;
  font-weight: 600;
}

.page-btn:hover:not(.active) {
  background: #f0f0f0;
}

.error-message {
  margin-top: 16px;
  padding: 10px 16px;
  background: #f8d7da;
  color: #721c24;
  border: 1px solid #f5c6cb;
  border-radius: 4px;
  font-size: 0.9rem;
}

.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 2000;
}

.modal-box {
  background: #fff;
  border-radius: 8px;
  padding: 32px;
  width: 400px;
  max-width: 90%;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
}

.modal-title {
  font-size: 1.3rem;
  color: #333;
  margin: 0 0 12px 0;
}

.modal-message {
  font-size: 0.95rem;
  color: #555;
  margin: 0 0 24px 0;
  line-height: 1.5;
}

.modal-actions {
  display: flex;
  justify-content: flex-end;
  gap: 12px;
}

.modal-btn {
  padding: 8px 20px;
  border-radius: 4px;
  font-size: 0.95rem;
  cursor: pointer;
  border: 1px solid #ccc;
  transition: all 0.2s;
}

.modal-btn.cancel-btn {
  background: #fff;
  color: #333;
}

.modal-btn.cancel-btn:hover {
  background: #f0f0f0;
}

.modal-btn.confirm-btn {
  background: #d9534f;
  color: #fff;
  border-color: #d9534f;
}

.modal-btn.confirm-btn:hover:not(:disabled) {
  background: #c9302c;
}

.modal-btn.confirm-btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}
</style>
