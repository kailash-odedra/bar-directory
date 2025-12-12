<template>
    <div id="app">
        <nav class="navbar">
            <div class="container">
                <div class="navbar-brand">
                    <router-link to="/" class="logo">Bar Directory</router-link>
                </div>
                <div class="navbar-menu">
                    <router-link to="/" class="nav-link">Home</router-link>
                    <template v-if="isAuthenticated">
                        <router-link to="/profile" class="nav-link">{{ user?.name }}</router-link>
                        <button @click="logout" class="btn btn-outline">Logout</button>
                    </template>
                    <template v-else>
                        <router-link to="/login" class="nav-link">Login</router-link>
                        <router-link to="/signup" class="btn btn-primary">Sign Up</router-link>
                    </template>
                </div>
            </div>
        </nav>
        <main>
            <router-view />
        </main>
        <footer class="footer">
            <div class="container">
                <p>&copy; 2025 Bar Directory. All rights reserved.</p>
            </div>
        </footer>
    </div>
</template>

<script>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { useRouter } from 'vue-router';

export default {
    name: 'App',
    setup() {
        const router = useRouter();
        const isAuthenticated = ref(false);
        const user = ref(null);

        const checkAuth = async () => {
            const token = localStorage.getItem('auth_token');
            if (token) {
                try {
                    const response = await axios.get('/user');
                    user.value = response.data;
                    isAuthenticated.value = true;
                } catch (error) {
                    localStorage.removeItem('auth_token');
                    delete axios.defaults.headers.common['Authorization'];
                    isAuthenticated.value = false;
                }
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
            isAuthenticated.value = false;
            user.value = null;
            router.push('/');
        };

        onMounted(() => {
            checkAuth();
        });

        return {
            isAuthenticated,
            user,
            logout,
        };
    },
};
</script>

<style scoped>
.navbar {
    background: #fff;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    padding: 1rem 0;
}

.navbar .container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.logo {
    font-size: 1.5rem;
    font-weight: bold;
    color: #333;
    text-decoration: none;
}

.navbar-menu {
    display: flex;
    gap: 1rem;
    align-items: center;
}

.nav-link {
    color: #333;
    text-decoration: none;
    padding: 0.5rem 1rem;
}

.nav-link:hover {
    color: #007bff;
}

.btn {
    padding: 0.5rem 1.5rem;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    text-decoration: none;
    display: inline-block;
}

.btn-primary {
    background: #007bff;
    color: white;
}

.btn-primary:hover {
    background: #0056b3;
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

main {
    min-height: calc(100vh - 200px);
}

.footer {
    background: #333;
    color: white;
    text-align: center;
    padding: 2rem 0;
    margin-top: 4rem;
}

.footer .container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
}
</style>

