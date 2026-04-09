import api from '../config/api'

export const authService = {
  async register(name, email, password, passwordConfirmation) {
    const response = await api.post('/register', {
      name,
      email,
      password,
      password_confirmation: passwordConfirmation,
    })
    return response.data
  },

  async login(email, password) {
    const response = await api.post('/login', { email, password })
    return response.data
  },

  async logout() {
    const response = await api.post('/logout')
    return response.data
  },

  async getUser() {
    const response = await api.get('/user')
    return response.data
  },

  async changePassword(currentPassword, password, passwordConfirmation) {
    const response = await api.put('/change-password', {
      current_password: currentPassword,
      password,
      password_confirmation: passwordConfirmation,
    })
    return response.data
  },
}
