<template>
    <div class="profile-page">
        <div class="container">
            <div v-if="loading" class="loading">Loading profile...</div>
            <div v-else-if="error" class="error">{{ error }}</div>
            <div v-else-if="user" class="profile-container">
                <div class="profile-header">
                    <h1>My Profile</h1>
                    <button @click="logout" class="btn btn-outline">Logout</button>
                </div>

                <div class="profile-content">
                    <div class="profile-card">
                        <div class="profile-avatar">
                            <img 
                                :src="user.image_url || '/build/resources/images/profile-30.png'" 
                                :alt="user.name"
                                @error="handleImageError"
                            />
                        </div>
                        <h2>{{ user.name }}</h2>
                        <p class="user-email">{{ user.email }}</p>
                    </div>

                    <div class="profile-form-card">
                        <h3>Edit Profile</h3>
                        <form @submit.prevent="updateProfile">
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
                            <div v-if="successMessage" class="success-message">{{ successMessage }}</div>
                            <div v-if="errorMessage" class="error-message">{{ errorMessage }}</div>
                            <button type="submit" class="btn btn-primary" :disabled="updating">
                                {{ updating ? 'Updating...' : 'Update Profile' }}
                            </button>
                        </form>
                    </div>

                    <div class="profile-stats-card">
                        <h3>My Activity</h3>
                        <div class="stats-grid">
                            <div class="stat-item">
                                <div class="stat-value">{{ myReviewsCount }}</div>
                                <div class="stat-label">Reviews Written</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-value">{{ memberSince }}</div>
                                <div class="stat-label">Member Since</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { ref, onMounted, computed } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';

export default {
    name: 'Profile',
    setup() {
        const router = useRouter();
        const user = ref(null);
        const loading = ref(true);
        const error = ref('');
        const updating = ref(false);
        const errorMessage = ref('');
        const successMessage = ref('');
        const myReviewsCount = ref(0);

        const form = ref({
            name: '',
            email: '',
        });

        const memberSince = computed(() => {
            if (!user.value?.created_at) return 'N/A';
            const date = new Date(user.value.created_at);
            return date.toLocaleDateString('en-US', { year: 'numeric', month: 'long' });
        });

        const fetchUser = async () => {
            loading.value = true;
            error.value = '';
            try {
                const response = await axios.get('/user');
                user.value = response.data;
                form.value.name = user.value.name;
                form.value.email = user.value.email;
                
                // Fetch user's reviews count
                try {
                    const reviewsResponse = await axios.get('/bars', { params: { per_page: 1000 } });
                    // Count reviews by this user (this is a simple approach)
                    // In a real app, you'd have a dedicated endpoint for user's reviews
                    myReviewsCount.value = 0; // Will be updated when we have user reviews endpoint
                } catch (e) {
                    console.error('Error fetching reviews:', e);
                }
            } catch (err) {
                if (err.response?.status === 401) {
                    router.push('/login');
                } else {
                    error.value = 'Failed to load profile.';
                }
            } finally {
                loading.value = false;
            }
        };

        const updateProfile = async () => {
            updating.value = true;
            errorMessage.value = '';
            successMessage.value = '';
            try {
                const response = await axios.put('/user', form.value);
                user.value = response.data.user || response.data;
                form.value.name = user.value.name;
                form.value.email = user.value.email;
                successMessage.value = 'Profile updated successfully!';
                setTimeout(() => {
                    successMessage.value = '';
                }, 3000);
            } catch (err) {
                if (err.response?.data?.errors) {
                    const errors = err.response.data.errors;
                    errorMessage.value = Object.values(errors).flat().join(', ');
                } else {
                    errorMessage.value = err.response?.data?.message || 'Failed to update profile.';
                }
            } finally {
                updating.value = false;
            }
        };

        const logout = async () => {
            try {
                await axios.post('/logout');
            } catch (error) {
                console.error('Logout error:', error);
            }
            localStorage.removeItem('auth_token');
            delete axios.defaults.headers.common['Authorization'];
            router.push('/');
        };

        const handleImageError = (event) => {
            event.target.src = '/build/resources/images/profile-30.png';
        };

        onMounted(() => {
            fetchUser();
        });

        return {
            user,
            loading,
            error,
            form,
            updating,
            errorMessage,
            successMessage,
            myReviewsCount,
            memberSince,
            updateProfile,
            logout,
            handleImageError,
        };
    },
};
</script>

<style scoped>
.profile-page {
    padding: 2rem 0;
    min-height: calc(100vh - 200px);
}

.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
}

.loading, .error {
    text-align: center;
    padding: 3rem;
    font-size: 1.25rem;
}

.error {
    color: #dc3545;
}

.profile-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
}

.profile-header h1 {
    margin: 0;
    color: #333;
}

.profile-content {
    display: grid;
    gap: 2rem;
}

.profile-card {
    background: white;
    padding: 2rem;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    text-align: center;
}

.profile-avatar {
    width: 120px;
    height: 120px;
    margin: 0 auto 1.5rem;
    border-radius: 50%;
    overflow: hidden;
    border: 4px solid #007bff;
}

.profile-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.profile-card h2 {
    margin: 0 0 0.5rem 0;
    color: #333;
}

.user-email {
    color: #666;
    margin: 0;
}

.profile-form-card,
.profile-stats-card {
    background: white;
    padding: 2rem;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.profile-form-card h3,
.profile-stats-card h3 {
    margin: 0 0 1.5rem 0;
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
    padding: 0.75rem 1.5rem;
    border: none;
    border-radius: 4px;
    font-size: 1rem;
    cursor: pointer;
    text-decoration: none;
    display: inline-block;
}

.btn-primary {
    background: #007bff;
    color: white;
}

.btn-primary:hover:not(:disabled) {
    background: #0056b3;
}

.btn-primary:disabled {
    background: #ccc;
    cursor: not-allowed;
}

.btn-outline {
    background: transparent;
    border: 1px solid #007bff;
    color: #007bff;
}

.btn-outline:hover {
    background: #007bff;
    color: white;
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

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1.5rem;
}

.stat-item {
    text-align: center;
    padding: 1.5rem;
    background: #f8f9fa;
    border-radius: 8px;
}

.stat-value {
    font-size: 2rem;
    font-weight: bold;
    color: #007bff;
    margin-bottom: 0.5rem;
}

.stat-label {
    color: #666;
    font-size: 0.9rem;
}
</style>

