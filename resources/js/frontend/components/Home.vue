<template>
    <div class="home">
        <div class="hero">
            <div class="container">
                <h1>Discover the Best Bars</h1>
                <p>Find your perfect night out</p>
                <div class="search-box">
                    <input 
                        v-model="searchQuery" 
                        @input="searchBars"
                        type="text" 
                        placeholder="Search bars..." 
                        class="search-input"
                    />
                </div>
            </div>
        </div>

        <div class="container">
            <div v-if="loading" class="loading">Loading bars...</div>
            <div v-else-if="error" class="error">{{ error }}</div>
            <div v-else>
                <div class="bars-grid">
                    <div 
                        v-for="bar in bars" 
                        :key="bar.id" 
                        class="bar-card"
                        @click="$router.push(`/bar/${bar.id}`)"
                    >
                        <div class="bar-image">
                            <img 
                                :src="getImageUrl(bar.cover_image)" 
                                :alt="bar.name"
                                @error="handleImageError"
                            />
                        </div>
                        <div class="bar-info">
                            <h3>{{ bar.name }}</h3>
                            <p class="bar-description">{{ bar.short_description || 'No description available' }}</p>
                            <div class="bar-meta">
                                <span class="rating">
                                    ⭐ {{ bar.avg_rating || '0.0' }}
                                </span>
                                <span class="reviews">
                                    ({{ bar.approved_reviews_count || 0 }} reviews)
                                </span>
                            </div>
                            <div v-if="bar.location" class="bar-location">
                                📍 {{ getLocation(bar.location) }}
                            </div>
                        </div>
                    </div>
                </div>

                <div v-if="pagination.last_page > 1" class="pagination">
                    <button 
                        @click="loadPage(pagination.current_page - 1)"
                        :disabled="pagination.current_page === 1"
                        class="btn"
                    >
                        Previous
                    </button>
                    <span>Page {{ pagination.current_page }} of {{ pagination.last_page }}</span>
                    <button 
                        @click="loadPage(pagination.current_page + 1)"
                        :disabled="pagination.current_page === pagination.last_page"
                        class="btn"
                    >
                        Next
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { ref, onMounted } from 'vue';
import axios from 'axios';

export default {
    name: 'Home',
    setup() {
        const bars = ref([]);
        const loading = ref(true);
        const error = ref(null);
        const searchQuery = ref('');
        const pagination = ref({
            current_page: 1,
            last_page: 1,
            per_page: 12,
            total: 0,
        });

        const fetchBars = async (page = 1) => {
            loading.value = true;
            error.value = null;
            try {
                const params = { per_page: 12, page };
                if (searchQuery.value) {
                    params.search = searchQuery.value;
                }
                const response = await axios.get('/bars', { params });
                bars.value = response.data.data;
                pagination.value = response.data.pagination;
            } catch (err) {
                error.value = 'Failed to load bars. Please try again.';
                console.error('Error fetching bars:', err);
            } finally {
                loading.value = false;
            }
        };

        const searchBars = () => {
            fetchBars(1);
        };

        const loadPage = (page) => {
            fetchBars(page);
        };

        const getLocation = (location) => {
            if (!location) return 'Location not available';
            const parts = [];
            if (location.city_name) parts.push(location.city_name);
            if (location.state_name) parts.push(location.state_name);
            if (location.country_name) parts.push(location.country_name);
            return parts.join(', ') || 'Location not available';
        };

        const getImageUrl = (imagePath) => {
            if (!imagePath) {
                return '/build/resources/images/logo.svg';
            }
            // If it's already a full URL, return as is
            if (imagePath.startsWith('http://') || imagePath.startsWith('https://')) {
                return imagePath;
            }
            // If it starts with /storage, return as is
            if (imagePath.startsWith('/storage')) {
                return imagePath;
            }
            // Otherwise, prepend /storage/
            return `/storage/${imagePath}`;
        };

        const handleImageError = (event) => {
            event.target.src = '/build/resources/images/logo.svg';
        };

        onMounted(() => {
            fetchBars();
        });

        return {
            bars,
            loading,
            error,
            searchQuery,
            pagination,
            searchBars,
            loadPage,
            getLocation,
            getImageUrl,
            handleImageError,
        };
    },
};
</script>

<style scoped>
.home {
    padding: 2rem 0;
}

.hero {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 4rem 0;
    text-align: center;
    margin-bottom: 3rem;
}

.hero h1 {
    font-size: 3rem;
    margin-bottom: 1rem;
}

.hero p {
    font-size: 1.25rem;
    margin-bottom: 2rem;
}

.search-box {
    max-width: 500px;
    margin: 0 auto;
}

.search-input {
    width: 100%;
    padding: 1rem;
    font-size: 1rem;
    border: none;
    border-radius: 8px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
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

.bars-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 2rem;
    margin-bottom: 3rem;
}

.bar-card {
    background: white;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    cursor: pointer;
    transition: transform 0.2s, box-shadow 0.2s;
}

.bar-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.bar-image {
    width: 100%;
    height: 200px;
    overflow: hidden;
}

.bar-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.bar-info {
    padding: 1.5rem;
}

.bar-info h3 {
    margin: 0 0 0.5rem 0;
    color: #333;
}

.bar-description {
    color: #666;
    margin: 0.5rem 0;
    font-size: 0.9rem;
}

.bar-meta {
    display: flex;
    gap: 1rem;
    margin: 1rem 0;
    align-items: center;
}

.rating {
    font-weight: bold;
    color: #ffc107;
}

.reviews {
    color: #666;
    font-size: 0.9rem;
}

.bar-location {
    color: #666;
    font-size: 0.9rem;
    margin-top: 0.5rem;
}

.pagination {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 1rem;
    margin-top: 3rem;
}

.pagination .btn {
    padding: 0.5rem 1.5rem;
    background: #007bff;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}

.pagination .btn:disabled {
    background: #ccc;
    cursor: not-allowed;
}
</style>

