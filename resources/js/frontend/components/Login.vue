<template>
    <div class="login-page">
        <div class="container">
            <div class="login-card">
                <h2>Login</h2>
                <form @submit.prevent="handleLogin">
                    <div class="form-group">
                        <label>Email</label>
                        <input 
                            v-model="form.email" 
                            type="email" 
                            required
                            class="form-control"
                            placeholder="Enter your email"
                        />
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <input 
                            v-model="form.password" 
                            type="password" 
                            required
                            class="form-control"
                            placeholder="Enter your password"
                        />
                    </div>
                    <div v-if="error" class="error-message">{{ error }}</div>
                    <button type="submit" class="btn btn-primary" :disabled="loading">
                        {{ loading ? 'Logging in...' : 'Login' }}
                    </button>
                    <p class="signup-link">
                        Don't have an account? 
                        <router-link to="/signup">Sign up</router-link>
                    </p>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';

export default {
    name: 'Login',
    setup() {
        const router = useRouter();
        const form = ref({
            email: '',
            password: '',
        });
        const loading = ref(false);
        const error = ref('');

        const handleLogin = async () => {
            loading.value = true;
            error.value = '';
            try {
                const response = await axios.post('/login', form.value);
                localStorage.setItem('auth_token', response.data.token);
                axios.defaults.headers.common['Authorization'] = `Bearer ${response.data.token}`;
                router.push('/profile');
                // Reload page to update auth state
                window.location.reload();
            } catch (err) {
                error.value = err.response?.data?.message || 'Login failed. Please check your credentials.';
            } finally {
                loading.value = false;
            }
        };

        return {
            form,
            loading,
            error,
            handleLogin,
        };
    },
};
</script>

<style scoped>
.login-page {
    min-height: calc(100vh - 200px);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 2rem 0;
}

.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
}

.login-card {
    max-width: 400px;
    margin: 0 auto;
    background: white;
    padding: 2rem;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.login-card h2 {
    margin: 0 0 2rem 0;
    text-align: center;
    color: #333;
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    color: #333;
    font-weight: 500;
}

.form-control {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 1rem;
    box-sizing: border-box;
}

.form-control:focus {
    outline: none;
    border-color: #007bff;
}

.btn {
    width: 100%;
    padding: 0.75rem;
    background: #007bff;
    color: white;
    border: none;
    border-radius: 4px;
    font-size: 1rem;
    cursor: pointer;
    margin-top: 1rem;
}

.btn:hover:not(:disabled) {
    background: #0056b3;
}

.btn:disabled {
    background: #ccc;
    cursor: not-allowed;
}

.error-message {
    color: #dc3545;
    margin-bottom: 1rem;
    padding: 0.5rem;
    background: #f8d7da;
    border-radius: 4px;
}

.signup-link {
    text-align: center;
    margin-top: 1.5rem;
    color: #666;
}

.signup-link a {
    color: #007bff;
    text-decoration: none;
}

.signup-link a:hover {
    text-decoration: underline;
}
</style>

