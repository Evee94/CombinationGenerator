import api from '../config/api'

export const historyService = {
  async getAll() {
    const response = await api.get('/histories')
    return response.data
  },

  async save(parameters, combinations, name = null) {
    const response = await api.post('/histories', {
      name,
      parameters,
      combinations,
    })
    return response.data
  },

  async getById(id) {
    const response = await api.get(`/histories/${id}`)
    return response.data
  },

  async remove(id) {
    const response = await api.delete(`/histories/${id}`)
    return response.data
  },
}
