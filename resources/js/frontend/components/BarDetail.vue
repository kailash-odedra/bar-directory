<template>
    <div class="bar-detail">
        <div class="container">
            <div v-if="loading" class="loading">Loading bar details...</div>
            <div v-else-if="error" class="error">{{ error }}</div>
            <div v-else-if="bar">
                <div class="bar-header">
                    <button @click="$router.back()" class="back-btn">← Back</button>
                    <h1>{{ bar.name }}</h1>
                    <div class="bar-rating">
                        <span class="stars">⭐ {{ bar.average_rating || '0.0' }}</span>
                        <span class="review-count">({{ bar.total_reviews || 0 }} reviews)</span>
                    </div>
                </div>

                <div class="bar-content">
                    <div class="bar-image-large">
                        <img 
                            :src="bar.cover_image || '/build/resources/images/logo.svg'" 
                            :alt="bar.name"
                            @error="handleImageError"
                        />
                    </div>

                    <div class="bar-info-section">
                        <div class="info-card">
                            <h3>About</h3>
                            <p>{{ bar.full_description || bar.short_description || 'No description available.' }}</p>
                        </div>

                        <div v-if="bar.location" class="info-card">
                            <h3>Location</h3>
                            <p>📍 {{ getLocation(bar.location) }}</p>
                        </div>

                        <div v-if="bar.tags && bar.tags.length > 0" class="info-card">
                            <h3>Tags</h3>
                            <div class="tags">
                                <span v-for="tag in bar.tags" :key="tag.id" class="tag">{{ tag.name }}</span>
                            </div>
                        </div>

                        <div v-if="bar.website || bar.facebook || bar.instagram" class="info-card">
                            <h3>Links</h3>
                            <div class="links">
                                <a v-if="bar.website" :href="bar.website" target="_blank" class="link">🌐 Website</a>
                                <a v-if="bar.facebook" :href="bar.facebook" target="_blank" class="link">📘 Facebook</a>
                                <a v-if="bar.instagram" :href="bar.instagram" target="_blank" class="link">📷 Instagram</a>
                            </div>
                        </div>
                    </div>

                    <div class="reviews-section">
                        <h2>Reviews</h2>
                        <div v-if="isAuthenticated" class="add-review">
                            <h3>Write a Review</h3>
                            <form @submit.prevent="submitReview">
                                <div class="form-group">
                                    <label>Rating</label>
                                    <select v-model="reviewForm.rating" required class="form-control">
                                        <option value="">Select rating</option>
                                        <option value="5">5 - Excellent</option>
                                        <option value="4">4 - Very Good</option>
                                        <option value="3">3 - Good</option>
                                        <option value="2">2 - Fair</option>
                                        <option value="1">1 - Poor</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Comment</label>
                                    <textarea 
                                        v-model="reviewForm.comment" 
                                        required
                                        class="form-control"
                                        rows="4"
                                        placeholder="Write your review..."
                                    ></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary" :disabled="submittingReview">
                                    {{ submittingReview ? 'Submitting...' : 'Submit Review' }}
                                </button>
                            </form>
                        </div>
                        <div v-else class="login-prompt">
                            <p>Please <router-link to="/login">login</router-link> to write a review.</p>
                        </div>

                        <div class="reviews-list">
                            <div v-if="reviewsLoading" class="loading">Loading reviews...</div>
                            <div v-else-if="reviews.length === 0" class="no-reviews">No reviews yet.</div>
                            <div v-else>
                                <div v-for="review in reviews" :key="review.id" class="review-card">
                                    <div class="review-header">
                                        <strong>{{ review.user?.name || 'Anonymous' }}</strong>
                                        <span class="review-rating">⭐ {{ review.rating }}</span>
                                    </div>
                                    <p class="review-comment">{{ review.comment }}</p>
                                    <small class="review-date">{{ formatDate(review.created_at) }}</small>
                                </div>
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
import { useRoute } from 'vue-router';
import axios from 'axios';

export default {
    name: 'BarDetail',
    setup() {
        const route = useRoute();
        const bar = ref(null);
        const reviews = ref([]);
        const loading = ref(true);
        const reviewsLoading = ref(false);
        const error = ref(null);
        const isAuthenticated = ref(false);
        const submittingReview = ref(false);
        const reviewForm = ref({
            rating: '',
            comment: '',
        });

        const checkAuth = () => {
            const token = localStorage.getItem('auth_token');
            isAuthenticated.value = !!token;
        };

        const fetchBar = async () => {
            loading.value = true;
            error.value = null;
            try {
                const response = await axios.get(`/bars/${route.params.id}`);
                bar.value = response.data.data;
                fetchReviews();
            } catch (err) {
                error.value = 'Failed to load bar details.';
                console.error('Error fetching bar:', err);
            } finally {
                loading.value = false;
            }
        };

        const fetchReviews = async () => {
            reviewsLoading.value = true;
            try {
                const response = await axios.get(`/bars/${route.params.id}/reviews`);
                reviews.value = response.data.data;
            } catch (err) {
                console.error('Error fetching reviews:', err);
            } finally {
                reviewsLoading.value = false;
            }
        };

        const submitReview = async () => {
            submittingReview.value = true;
            try {
                await axios.post(`/bars/${route.params.id}/reviews`, reviewForm.value);
                reviewForm.value = { rating: '', comment: '' };
                fetchReviews();
                alert('Review submitted successfully! It will be visible after approval.');
            } catch (err) {
                alert(err.response?.data?.message || 'Failed to submit review.');
            } finally {
                submittingReview.value = false;
            }
        };

        const getLocation = (location) => {
            if (!location) return 'Location not available';
            const parts = [];
            if (location.city_name) parts.push(location.city_name);
            if (location.state_name) parts.push(location.state_name);
            if (location.country_name) parts.push(location.country_name);
            return parts.join(', ') || 'Location not available';
        };

        const formatDate = (dateString) => {
            const date = new Date(dateString);
            return date.toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' });
        };

        const handleImageError = (event) => {
            event.target.src = '/build/resources/images/logo.svg';
        };

        onMounted(() => {
            checkAuth();
            fetchBar();
        });

        return {
            bar,
            reviews,
            loading,
            reviewsLoading,
            error,
            isAuthenticated,
            submittingReview,
            reviewForm,
            submitReview,
            getLocation,
            formatDate,
            handleImageError,
        };
    },
};
</script>

<style scoped>
.bar-detail {
    padding: 2rem 0;
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

.back-btn {
    background: #f0f0f0;
    border: none;
    padding: 0.5rem 1rem;
    border-radius: 4px;
    cursor: pointer;
    margin-bottom: 1rem;
}

.back-btn:hover {
    background: #e0e0e0;
}

.bar-header {
    margin-bottom: 2rem;
}

.bar-header h1 {
    margin: 0.5rem 0;
    color: #333;
}

.bar-rating {
    display: flex;
    gap: 1rem;
    align-items: center;
    margin-top: 0.5rem;
}

.stars {
    font-size: 1.25rem;
    font-weight: bold;
}

.bar-content {
    display: grid;
    gap: 2rem;
}

.bar-image-large {
    width: 100%;
    height: 400px;
    overflow: hidden;
    border-radius: 8px;
}

.bar-image-large img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.bar-info-section {
    display: grid;
    gap: 1.5rem;
}

.info-card {
    background: white;
    padding: 1.5rem;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.info-card h3 {
    margin: 0 0 1rem 0;
    color: #333;
}

.tags {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.tag {
    background: #f0f0f0;
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.9rem;
}

.links {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
}

.link {
    color: #007bff;
    text-decoration: none;
}

.link:hover {
    text-decoration: underline;
}

.reviews-section {
    margin-top: 3rem;
}

.reviews-section h2 {
    margin-bottom: 2rem;
}

.add-review {
    background: white;
    padding: 1.5rem;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    margin-bottom: 2rem;
}

.add-review h3 {
    margin: 0 0 1rem 0;
}

.form-group {
    margin-bottom: 1rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    color: #333;
}

.form-control {
    width: 100%;
    padding: 0.5rem;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 1rem;
    box-sizing: border-box;
}

.btn {
    padding: 0.5rem 1.5rem;
    background: #007bff;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}

.btn:disabled {
    background: #ccc;
    cursor: not-allowed;
}

.login-prompt {
    background: #f8f9fa;
    padding: 1rem;
    border-radius: 4px;
    margin-bottom: 2rem;
    text-align: center;
}

.reviews-list {
    display: grid;
    gap: 1rem;
}

.review-card {
    background: white;
    padding: 1.5rem;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.review-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.5rem;
}

.review-rating {
    color: #ffc107;
    font-weight: bold;
}

.review-comment {
    margin: 0.5rem 0;
    color: #666;
}

.review-date {
    color: #999;
}

.no-reviews {
    text-align: center;
    padding: 2rem;
    color: #666;
}
</style>

