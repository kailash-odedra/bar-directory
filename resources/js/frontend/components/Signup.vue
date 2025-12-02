<template>
    <div class="signup-page">
        <div class="container">
            <div class="signup-card">
                <h2>Sign Up</h2>
                <form @submit.prevent="handleSignup">
                    <div class="form-group">
                        <label>Name</label>
                        <input 
                            v-model="form.name" 
                            type="text" 
                            required
                            class="form-control"
                            placeholder="Enter your name"
                        />
                    </div>
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
                            minlength="8"
                            class="form-control"
                            placeholder="Enter your password (min 8 characters)"
                        />
                    </div>
                    <div class="form-group">
                        <label>Confirm Password</label>
                        <input 
                            v-model="form.password_confirmation" 
                            type="password" 
                            required
                            class="form-control"
                            placeholder="Confirm your password"
                        />
                    </div>
                    <div v-if="error" class="error-message">{{ error }}</div>
                    <div v-if="success" class="success-message">{{ success }}</div>
                    <button type="submit" class="btn btn-primary" :disabled="loading">
                        {{ loading ? 'Creating account...' : 'Sign Up' }}
                    </button>
                    <p class="login-link">
                        Already have an account? 
                        <router-link to="/login">Login</router-link>
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
    name: 'Signup',
    setup() {
        const router = useRouter();
        const form = ref({
            name: '',
            email: '',
            password: '',
            password_confirmation: '',
        });
        const loading = ref(false);
        const error = ref('');
        const success = ref('');

        const handleSignup = async () => {
            if (form.value.password !== form.value.password_confirmation) {
                error.value = 'Passwords do not match.';
                return;
            }

            loading.value = true;
            error.value = '';
            success.value = '';
            try {
                const response = await axios.post('/register', form.value);
                success.value = 'Account created successfully! Redirecting to login...';
                setTimeout(() => {
                    router.push('/login');
                }, 2000);
            } catch (err) {
                if (err.response?.data?.errors) {
                    const errors = err.response.data.errors;
                    error.value = Object.values(errors).flat().join(', ');
                } else {
                    error.value = err.response?.data?.message || 'Signup failed. Please try again.';
                }
            } finally {
                loading.value = false;
            }
        };

        return {
            form,
            loading,
            error,
            success,
            handleSignup,
        };
    },
};
</script>

<style scoped>
.signup-page {
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

.signup-card {
    max-width: 400px;
    margin: 0 auto;
    background: white;
    padding: 2rem;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.signup-card h2 {
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

.success-message {
    color: #155724;
    margin-bottom: 1rem;
    padding: 0.5rem;
    background: #d4edda;
    border-radius: 4px;
}

.login-link {
    text-align: center;
    margin-top: 1.5rem;
    color: #666;
}

.login-link a {
    color: #007bff;
    text-decoration: none;
}

.login-link a:hover {
    text-decoration: underline;
}
</style>

