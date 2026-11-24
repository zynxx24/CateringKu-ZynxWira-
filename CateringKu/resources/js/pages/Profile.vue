<script setup lang="ts">
import CateringLayout from '@/layouts/CateringLayout.vue'
import { Head, useForm, usePage } from '@inertiajs/vue3'
import { ref } from 'vue'
import StarRating from '@/components/catering/StarRating.vue'
import OrderStatusBadge from '@/components/catering/OrderStatusBadge.vue'

interface Order {
    order_id: number
    order_number: string
    status: string
    total_amount: number
    created_at: string
}

interface Review {
    review_id: number
    vendor_name: string
    rating: number
    comment: string
    created_at: string
}

const props = defineProps<{
    user: {
        id: number
        name: string
        username: string
        email: string
        phone: string | null
        address: string | null
        created_at: string
    }
    recentOrders: Order[]
    reviews: Review[]
    stats: {
        totalOrders: number
        completedOrders: number
        totalSpent: number
    }
}>()

const editing = ref(false)
const form = useForm({
    name: props.user.name,
    phone: props.user.phone ?? '',
    address: props.user.address ?? '',
})

function saveProfile() {
    form.patch('/settings/profile', {
        onSuccess: () => { editing.value = false },
    })
}

function formatPrice(price: number) {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(price)
}

function formatDate(date: string) {
    return new Date(date).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })
}
</script>

<template>
    <Head title="Profil Saya | CateringKu" />
    <CateringLayout>
        <div class="max-w-4xl mx-auto">
            <h1 class="text-2xl font-bold text-[var(--ck-text-primary)] mb-8 flex items-center gap-3">
                <span class="w-1 h-8 bg-gradient-to-b from-ck-primary to-ck-coral rounded-full"></span>
                Profil Saya
            </h1>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Profile Card -->
                <div class="lg:col-span-1">
                    <div class="bg-[var(--ck-surface)] rounded-2xl border border-[var(--ck-surface-border)] shadow-sm p-6 text-center sticky top-24">
                        <div class="w-20 h-20 bg-gradient-to-br from-ck-primary to-ck-coral rounded-2xl flex items-center justify-center text-white text-3xl font-bold mx-auto mb-4 shadow-lg shadow-ck-primary/20">
                            {{ user.name?.charAt(0).toUpperCase() }}
                        </div>
                        <h2 class="text-xl font-bold text-[var(--ck-text-primary)]">{{ user.name }}</h2>
                        <p class="text-[var(--ck-text-muted)] text-sm">@{{ user.username }}</p>
                        <p class="text-[var(--ck-text-muted)] text-xs mt-2">Bergabung sejak {{ formatDate(user.created_at) }}</p>

                        <!-- Stats -->
                        <div class="grid grid-cols-2 gap-3 mt-6">
                            <div class="bg-[var(--ck-body-bg)] rounded-xl p-3">
                                <p class="text-2xl font-bold text-[var(--ck-text-primary)]">{{ stats.totalOrders }}</p>
                                <p class="text-xs text-[var(--ck-text-muted)]">Pesanan</p>
                            </div>
                            <div class="bg-[var(--ck-body-bg)] rounded-xl p-3">
                                <p class="text-2xl font-bold text-[var(--ck-text-primary)]">{{ stats.completedOrders }}</p>
                                <p class="text-xs text-[var(--ck-text-muted)]">Selesai</p>
                            </div>
                        </div>

                        <div class="mt-4 bg-ck-bg-warm dark:bg-white/5 rounded-xl p-3">
                            <p class="text-xs text-[var(--ck-text-muted)] mb-1">Total Belanja</p>
                            <p class="text-lg font-bold text-ck-primary">{{ formatPrice(stats.totalSpent) }}</p>
                        </div>
                    </div>
                </div>

                <!-- Profile Details -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Info Card -->
                    <div class="bg-[var(--ck-surface)] rounded-2xl border border-[var(--ck-surface-border)] shadow-sm p-6">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="font-bold text-[var(--ck-text-primary)] text-lg">Informasi Pribadi</h3>
                            <button
                                type="button"
                                :aria-expanded="editing"
                                @click="editing = !editing"
                                class="text-sm font-medium text-ck-primary hover:text-ck-primary-dark transition-colors"
                            >
                                {{ editing ? 'Batal' : 'Edit' }}
                            </button>
                        </div>

                        <form v-if="editing" @submit.prevent="saveProfile" class="space-y-4">
                            <div>
                                <label for="profile-field-1" class="block text-sm font-medium text-[var(--ck-text-secondary)] mb-1">Nama Lengkap</label>
                                <input id="profile-field-1" v-model="form.name" type="text" class="w-full px-4 py-3 rounded-xl border border-[var(--ck-surface-border)] focus:ring-2 focus:ring-ck-primary/30 focus:border-ck-primary focus:outline-none" />
                            </div>
                            <div>
                                <label for="profile-field-2" class="block text-sm font-medium text-[var(--ck-text-secondary)] mb-1">Telepon</label>
                                <input id="profile-field-2" v-model="form.phone" type="text" class="w-full px-4 py-3 rounded-xl border border-[var(--ck-surface-border)] focus:ring-2 focus:ring-ck-primary/30 focus:border-ck-primary focus:outline-none" />
                            </div>
                            <div>
                                <label for="profile-field-3" class="block text-sm font-medium text-[var(--ck-text-secondary)] mb-1">Alamat</label>
                                <textarea id="profile-field-3" v-model="form.address" rows="3" class="w-full px-4 py-3 rounded-xl border border-[var(--ck-surface-border)] focus:ring-2 focus:ring-ck-primary/30 focus:border-ck-primary focus:outline-none resize-none"></textarea>
                            </div>
                            <button type="submit" :disabled="form.processing" class="btn-primary w-full">
                                {{ form.processing ? 'Menyimpan...' : 'Simpan Perubahan' }}
                            </button>
                        </form>

                        <div v-else class="space-y-4">
                            <div class="flex items-center gap-4 p-3 rounded-xl bg-[var(--ck-body-bg)]">
                                <span class="text-[var(--ck-text-muted)] text-sm w-24 shrink-0">Email</span>
                                <span class="text-[var(--ck-text-primary)] font-medium">{{ user.email }}</span>
                            </div>
                            <div class="flex items-center gap-4 p-3 rounded-xl bg-[var(--ck-body-bg)]">
                                <span class="text-[var(--ck-text-muted)] text-sm w-24 shrink-0">Telepon</span>
                                <span class="text-[var(--ck-text-primary)] font-medium">{{ user.phone || '-' }}</span>
                            </div>
                            <div class="flex items-center gap-4 p-3 rounded-xl bg-[var(--ck-body-bg)]">
                                <span class="text-[var(--ck-text-muted)] text-sm w-24 shrink-0">Alamat</span>
                                <span class="text-[var(--ck-text-primary)] font-medium">{{ user.address || '-' }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Orders -->
                    <div class="bg-[var(--ck-surface)] rounded-2xl border border-[var(--ck-surface-border)] shadow-sm p-6">
                        <h3 class="font-bold text-[var(--ck-text-primary)] text-lg mb-4">Pesanan Terakhir</h3>
                        <div v-if="recentOrders.length === 0" class="text-center py-8 text-[var(--ck-text-muted)]">
                            <p class="text-4xl mb-2">📦</p>
                            <p>Belum ada pesanan</p>
                        </div>
                        <div v-else class="space-y-3">
                            <a
                                v-for="order in recentOrders"
                                :key="order.order_id"
                                :href="`/orders/${order.order_id}`"
                                class="flex items-center justify-between p-4 rounded-xl border border-[var(--ck-surface-border)] hover:border-ck-primary/20 hover:shadow-sm transition-all"
                            >
                                <div>
                                    <p class="font-semibold text-[var(--ck-text-primary)] text-sm">#{{ order.order_number }}</p>
                                    <p class="text-xs text-[var(--ck-text-muted)] mt-1">{{ formatDate(order.created_at) }}</p>
                                </div>
                                <div class="text-right">
                                    <OrderStatusBadge :status="order.status" />
                                    <p class="text-sm font-bold text-[var(--ck-text-primary)] mt-1">{{ formatPrice(order.total_amount) }}</p>
                                </div>
                            </a>
                        </div>
                    </div>

                    <!-- Reviews -->
                    <div class="bg-[var(--ck-surface)] rounded-2xl border border-[var(--ck-surface-border)] shadow-sm p-6">
                        <h3 class="font-bold text-[var(--ck-text-primary)] text-lg mb-4">Ulasan Saya</h3>
                        <div v-if="reviews.length === 0" class="text-center py-8 text-[var(--ck-text-muted)]">
                            <p class="text-4xl mb-2">⭐</p>
                            <p>Belum ada ulasan</p>
                        </div>
                        <div v-else class="space-y-4">
                            <div
                                v-for="review in reviews"
                                :key="review.review_id"
                                class="p-4 rounded-xl bg-[var(--ck-body-bg)]"
                            >
                                <div class="flex items-center justify-between mb-2">
                                    <span class="font-semibold text-[var(--ck-text-primary)] text-sm">{{ review.vendor_name }}</span>
                                    <StarRating :rating="review.rating" readonly size="sm" />
                                </div>
                                <p class="text-[var(--ck-text-secondary)] text-sm">{{ review.comment }}</p>
                                <p class="text-xs text-[var(--ck-text-muted)] mt-2">{{ formatDate(review.created_at) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </CateringLayout>
</template>
