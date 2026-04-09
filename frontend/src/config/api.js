import axios from 'axios';

const api = axios.create({
    baseURL: 'http://localhost:8000/api',
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
    },
    withCredentials: true
});

// Intercept 401 responses to handle expired tokens
api.interceptors.response.use(
    response => response,
    error => {
        if (error.response?.status === 401) {
            // Clear stored auth data
            localStorage.removeItem('auth');
            delete api.defaults.headers.common['Authorization'];
            // Redirect to login if not already there
            if (window.location.pathname !== '/login' && window.location.pathname !== '/register') {
                window.location.href = '/login';
            }
        }
        return Promise.reject(error);
    }
);

export const saveParameters = (parameters, values) =>
    api.post('/save-parameters', { parameters, values });

export const getParameters = () =>
    api.get('/get-parameters');

export const getParameterValues = (id) =>
    api.get(`/parameters/${id}`);

export default api; 