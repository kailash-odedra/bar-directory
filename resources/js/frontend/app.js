import { createApp } from 'vue';
import { createRouter, createWebHistory } from 'vue-router';
import axios from 'axios';
import App from './App.vue';
import Home from './components/Home.vue';
import Login from './components/Login.vue';
import Signup from './components/Signup.vue';
import BarDetail from './components/BarDetail.vue';
import Profile from './components/Profile.vue';

// Configure axios base URL
axios.defaults.baseURL = '/api/v1';
axios.defaults.headers.common['Accept'] = 'application/json';
axios.defaults.headers.common['Content-Type'] = 'application/json';

// Add token to requests if available
const token = localStorage.getItem('auth_token');
if (token) {
    axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
}

// Router configuration
const routes = [
    { path: '/', component: Home, name: 'home' },
    { path: '/login', component: Login, name: 'login' },
    { path: '/signup', component: Signup, name: 'signup' },
    { path: '/profile', component: Profile, name: 'profile', meta: { requiresAuth: true } },
    { path: '/bar/:id', component: BarDetail, name: 'bar-detail' },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

// Navigation guard to protect routes that require authentication
router.beforeEach((to, from, next) => {
    const token = localStorage.getItem('auth_token');
    if (to.meta.requiresAuth && !token) {
        next('/login');
    } else {
        next();
    }
});

// Create Vue app
const app = createApp(App);
app.use(router);
app.mount('#app');

