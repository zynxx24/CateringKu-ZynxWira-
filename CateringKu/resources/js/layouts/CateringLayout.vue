<script setup lang="ts">
import { Link, usePage, Head } from '@inertiajs/vue3'
import { ref, computed, onMounted, onUnmounted } from 'vue'
import DarkModeToggle from '@/components/DarkModeToggle.vue'

const page = usePage()
const user = computed(() => (page.props as any).auth?.user)
const cartCount = computed(() => (page.props as any).cartCount ?? 0)
const mobileMenuOpen = ref(false)
const userMenuOpen = ref(false)
const scrolled = ref(false)

const navLinks = [
    { name: 'Beranda', href: '/', routeName: 'home', icon: '🏠' },
    { name: 'Cari Vendor', href: '/search', routeName: 'search', icon: '🔍' },
    { name: 'Tentang', href: '/about', routeName: 'about', icon: 'ℹ️' },
    { name: 'Kontak', href: '/contact', routeName: 'contact', icon: '✉️' },
]

const site = computed(() => (page.props as any).site ?? { contact: {}, social: {} })
const showFooterCta = computed(() => !['/cart', '/checkout', '/orders', '/profile'].some((p) => page.url.startsWith(p)))

// Only links that exist in config are rendered; an icon pointing at "#" is a dead control.
const socialLinks = computed(() => [
    { key: 'instagram', label: 'Instagram' },
    { key: 'whatsapp', label: 'WhatsApp' },
    { key: 'telegram', label: 'Telegram' },
    { key: 'x', label: 'X' },
].filter((s) => site.value.social?.[s.key]).map((s) => ({ ...s, href: site.value.social[s.key] })))

const contactRows = computed(() => [
    { key: 'email', label: 'Email', icon: '📧' },
    { key: 'phone', label: 'Telepon', icon: '📞' },
    { key: 'address', label: 'Alamat', icon: '📍' },
    { key: 'hours', label: 'Jam operasional', icon: '🕐' },
].filter((r) => site.value.contact?.[r.key]).map((r) => ({ ...r, value: site.value.contact[r.key] })))

function isActive(link: { href: string }) {
    if (link.href === '/') return page.url === '/'
    return page.url.startsWith(link.href)
}

function handleScroll() {
    scrolled.value = window.scrollY > 10
}

function closeMenus() {
    userMenuOpen.value = false
    mobileMenuOpen.value = false
}

onMounted(() => {
    window.addEventListener('scroll', handleScroll)
    document.addEventListener('click', (e: Event) => {
        const target = e.target as HTMLElement
        if (!target.closest('.user-menu-container')) {
            userMenuOpen.value = false
        }
    })
})

onUnmounted(() => {
    window.removeEventListener('scroll', handleScroll)
})
</script>

<template>
    <div class="min-h-screen flex flex-col bg-[var(--ck-body-bg)] text-[var(--ck-text-primary)] transition-colors duration-300">
        <!-- Navbar -->
        <nav
            :class="[
                'sticky top-0 z-50 transition-all duration-300',
                scrolled
                    ? 'shadow-lg dark:shadow-black/30'
                    : ''
            ]"
            class="bg-[var(--ck-nav-bg)] backdrop-blur-xl border-b border-[var(--ck-nav-border)]"
        >
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <!-- Logo -->
                    <div class="flex items-center">
                        <Link href="/" class="flex items-center gap-2.5 group">
                            <img
                                src="/images/logo.svg"
                                alt="CateringKu"
                                class="h-10 w-10 rounded-xl shadow-sm group-hover:shadow-md transition-shadow"
                            />
                            <span class="text-xl font-bold bg-gradient-to-r from-ck-primary to-ck-coral bg-clip-text text-transparent hidden sm:block">
                                CateringKu
                            </span>
                        </Link>
                    </div>

                    <!-- Desktop Nav -->
                    <div class="hidden md:flex items-center gap-1">
                        <Link
                            v-for="link in navLinks"
                            :key="link.routeName"
                            :href="link.href"
                            class="px-4 py-2 rounded-xl text-sm font-medium transition-all duration-200"
                            :class="isActive(link)
                                ? 'text-ck-primary bg-ck-primary/8 shadow-sm shadow-ck-primary/10'
                                : 'text-[var(--ck-text-secondary)] hover:text-ck-primary hover:bg-ck-primary/5'"
                        >
                            {{ link.name }}
                        </Link>
                    </div>

                    <!-- Right side -->
                    <div class="flex items-center gap-2">
                        <!-- Dark Mode Toggle -->
                        <DarkModeToggle variant="icon" size="sm" />

                        <!-- Cart -->
                        <Link
                            v-if="user"
                            href="/cart"
                            class="relative p-2.5 text-[var(--ck-text-muted)] hover:text-ck-primary hover:bg-ck-primary/5 rounded-xl transition-all"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            <span
                                v-if="cartCount > 0"
                                class="absolute -top-0.5 -right-0.5 w-5 h-5 bg-gradient-to-br from-ck-primary to-ck-coral text-white text-[10px] flex items-center justify-center rounded-full font-bold shadow-lg shadow-ck-primary/30 animate-pulse"
                            >
                                {{ cartCount }}
                            </span>
                        </Link>

                        <!-- User Menu -->
                        <template v-if="user">
                            <div class="relative user-menu-container">
                                <button
                                    @click="userMenuOpen = !userMenuOpen"
                                    class="flex items-center gap-2 p-1.5 rounded-xl hover:bg-[var(--ck-surface-border)] transition-all"
                                >
                                    <div class="w-8 h-8 bg-gradient-to-br from-ck-primary to-ck-coral text-white rounded-xl flex items-center justify-center text-xs font-bold shadow-sm">
                                        {{ user.name?.charAt(0).toUpperCase() }}
                                    </div>
                                    <span class="hidden sm:block text-sm font-medium text-[var(--ck-text-primary)] max-w-[100px] truncate">{{ user.name }}</span>
                                    <svg :class="['w-3.5 h-3.5 text-[var(--ck-text-muted)] transition-transform', userMenuOpen && 'rotate-180']" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                                </button>

                                <Transition
                                    enter-active-class="transition duration-200 ease-out"
                                    enter-from-class="opacity-0 scale-95 -translate-y-1"
                                    enter-to-class="opacity-100 scale-100 translate-y-0"
                                    leave-active-class="transition duration-150 ease-in"
                                    leave-from-class="opacity-100 scale-100 translate-y-0"
                                    leave-to-class="opacity-0 scale-95 -translate-y-1"
                                >
                                    <div
                                        v-if="userMenuOpen"
                                        class="absolute right-0 mt-2 w-52 bg-[var(--ck-surface)] rounded-2xl shadow-xl shadow-black/10 dark:shadow-black/40 border border-[var(--ck-surface-border)] py-2 z-50 overflow-hidden"
                                    >
                                        <div class="px-4 py-2 border-b border-[var(--ck-surface-border)] mb-1">
                                            <p class="text-sm font-semibold text-[var(--ck-text-primary)] truncate">{{ user.name }}</p>
                                            <p class="text-xs text-[var(--ck-text-muted)] capitalize">{{ user.role || 'customer' }}</p>
                                        </div>
                                        <Link @click="closeMenus" href="/orders" class="flex items-center gap-3 px-4 py-2.5 text-sm text-[var(--ck-text-secondary)] hover:bg-ck-primary/5 hover:text-orange-300 transition-colors">
                                            <span class="text-base">📦</span> Pesanan Saya
                                        </Link>
                                        <Link @click="closeMenus" href="/profile" class="flex items-center gap-3 px-4 py-2.5 text-sm text-[var(--ck-text-secondary)] hover:bg-ck-primary/5 hover:text-orange-300 transition-colors">
                                            <span class="text-base">👤</span> Profil
                                        </Link>
                                        <Link @click="closeMenus" href="/dashboard" class="flex items-center gap-3 px-4 py-2.5 text-sm text-[var(--ck-text-secondary)] hover:bg-ck-primary/5 hover:text-orange-300 transition-colors">
                                            <span class="text-base">📊</span> Dashboard
                                        </Link>
                                        <Link @click="closeMenus" href="/settings/profile" class="flex items-center gap-3 px-4 py-2.5 text-sm text-[var(--ck-text-secondary)] hover:bg-ck-primary/5 hover:text-orange-300 transition-colors">
                                            <span class="text-base">⚙️</span> Pengaturan
                                        </Link>
                                        <hr class="my-1 border-[var(--ck-surface-border)]">
                                        <Link @click="closeMenus" href="/logout" method="post" as="button" class="flex items-center gap-3 w-full px-4 py-2.5 text-sm text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10 transition-colors">
                                            <span class="text-base">🚪</span> Keluar
                                        </Link>
                                    </div>
                                </Transition>
                            </div>
                        </template>
                        <template v-else>
                            <Link href="/login" class="text-sm font-medium text-[var(--ck-text-secondary)] hover:text-ck-primary transition-colors px-3 py-2">
                                Masuk
                            </Link>
                            <Link href="/register" class="text-sm font-semibold text-white bg-gradient-to-r from-ck-primary to-ck-coral hover:shadow-lg hover:shadow-ck-primary/25 px-5 py-2.5 rounded-xl transition-all hover:-translate-y-0.5">
                                Daftar
                            </Link>
                        </template>

                        <!-- Mobile menu button -->
                        <button type="button" :aria-expanded="mobileMenuOpen" aria-label="Buka menu navigasi" @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden w-11 h-11 flex items-center justify-center text-[var(--ck-text-muted)] hover:text-ck-primary rounded-xl hover:bg-ck-primary/5 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path v-if="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Mobile Nav -->
                <Transition
                    enter-active-class="transition duration-200 ease-out"
                    enter-from-class="opacity-0 -translate-y-2"
                    leave-active-class="transition duration-150 ease-in"
                    leave-to-class="opacity-0 -translate-y-2"
                >
                    <div v-if="mobileMenuOpen" class="md:hidden py-3 space-y-1 border-t border-[var(--ck-nav-border)]">
                        <Link
                            v-for="link in navLinks"
                            :key="link.routeName"
                            :href="link.href"
                            @click="mobileMenuOpen = false"
                            :class="[
                                'flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors',
                                isActive(link)
                                    ? 'text-ck-primary bg-ck-primary/5'
                                    : 'text-[var(--ck-text-secondary)] hover:bg-ck-primary/5 hover:text-ck-primary'
                            ]"
                        >
                            <span>{{ link.icon }}</span>
                            {{ link.name }}
                        </Link>
                    </div>
                </Transition>
            </div>
        </nav>

        <!-- Flash Messages -->
        <Transition
            enter-active-class="transition duration-300 ease-out"
            enter-from-class="opacity-0 -translate-y-3"
            leave-active-class="transition duration-200 ease-in"
            leave-to-class="opacity-0 translate-y-1"
        >
            <div v-if="($page.props as any).flash?.success || ($page.props as any).flash?.error" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
                <div
                    v-if="($page.props as any).flash?.success"
                    class="p-4 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 rounded-2xl text-emerald-700 dark:text-emerald-300 text-sm flex items-center gap-3 shadow-sm"
                >
                    <span class="w-8 h-8 bg-emerald-100 dark:bg-emerald-800/40 rounded-xl flex items-center justify-center shrink-0">✓</span>
                    {{ ($page.props as any).flash.success }}
                </div>
                <div
                    v-if="($page.props as any).flash?.error"
                    class="p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-2xl text-red-700 dark:text-red-300 text-sm flex items-center gap-3 shadow-sm"
                >
                    <span class="w-8 h-8 bg-red-100 dark:bg-red-800/40 rounded-xl flex items-center justify-center shrink-0">✕</span>
                    {{ ($page.props as any).flash.error }}
                </div>
            </div>
        </Transition>

        <!-- Main Content -->
        <main class="flex-1 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-8 pb-16 w-full">
            <slot />
        </main>

        <!-- Footer -->
        <footer class="bg-gradient-to-b from-gray-900 via-gray-900 to-gray-950 dark:from-[#0f0f1a] dark:via-[#0c0c17] dark:to-[#080812] text-gray-300 mt-auto">
            <!-- CTA Section -->
            <div v-if="showFooterCta" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="relative -top-8">
                    <div class="bg-gradient-to-r from-ck-primary via-ck-coral to-ck-amber rounded-2xl p-8 md:p-10 text-white text-center shadow-2xl shadow-ck-primary/20 overflow-hidden relative">
                        <div class="absolute top-0 right-0 w-40 h-40 bg-white/10 rounded-full -mr-10 -mt-10"></div>
                        <div class="absolute bottom-0 left-0 w-32 h-32 bg-white/10 rounded-full -ml-8 -mb-8"></div>
                        <div class="absolute top-1/2 left-1/4 w-24 h-24 bg-white/5 rounded-full animate-float"></div>
                        <div class="relative z-10">
                            <h3 class="text-2xl md:text-3xl font-bold mb-3">Sudah tahu tanggal acaranya?</h3>
                            <p class="text-orange-100 mb-6 max-w-lg mx-auto">
                                Bandingkan menu, isi jumlah tamu, lalu pesan untuk tanggal acara Anda.
                            </p>
                            <Link href="/search" class="inline-flex items-center gap-2 bg-white text-ck-primary font-bold px-8 py-3.5 rounded-xl hover:shadow-xl transition-all hover:-translate-y-0.5">
                                Cari vendor di kota Anda
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" /></svg>
                            </Link>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer Content -->
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-12" :class="showFooterCta ? '' : 'pt-12'">
                <div class="grid grid-cols-1 md:grid-cols-12 gap-8 md:gap-12">
                    <!-- Brand -->
                    <div class="md:col-span-4">
                        <div class="flex items-center gap-2.5 mb-4">
                            <img src="/images/logo.svg" alt="CateringKu" class="h-10 w-10 rounded-xl" />
                            <span class="text-xl font-bold text-white">CateringKu</span>
                        </div>
                        <p class="text-gray-400 text-sm leading-relaxed mb-5">
                            Pesan katering dari vendor di kota Anda: prasmanan, nasi kotak, snack box, dan lainnya.
                        </p>
                        <div v-if="socialLinks.length" class="flex gap-3">
                            <a
                                v-for="social in socialLinks"
                                :key="social.key"
                                :href="social.href"
                                target="_blank"
                                rel="noopener noreferrer"
                                :aria-label="social.label"
                                class="min-w-11 h-11 px-3 bg-gray-800 hover:bg-gray-700 rounded-xl flex items-center justify-center text-gray-300 hover:text-white transition-colors text-sm"
                            >{{ social.label }}</a>
                        </div>
                    </div>

                    <!-- Nav Links -->
                    <div class="md:col-span-2">
                        <h4 class="font-semibold text-white mb-4 text-sm">Menu</h4>
                        <div class="space-y-2.5">
                            <Link v-for="link in navLinks" :key="link.routeName" :href="link.href" class="block text-sm text-gray-400 hover:text-orange-300 transition-colors">
                                {{ link.name }}
                            </Link>
                        </div>
                    </div>

                    <!-- Layanan -->
                    <div class="md:col-span-3">
                        <h4 class="font-semibold text-white mb-4 text-sm">Layanan</h4>
                        <div class="space-y-2.5">
                            <Link href="/search?type=prasmanan" class="block text-sm text-gray-400 hover:text-orange-300 transition-colors">Prasmanan</Link>
                            <Link href="/search?type=nasi-kotak" class="block text-sm text-gray-400 hover:text-orange-300 transition-colors">Nasi Kotak</Link>
                            <Link href="/search?type=snack-box" class="block text-sm text-gray-400 hover:text-orange-300 transition-colors">Snack Box</Link>
                            <Link href="/search?type=tumpeng" class="block text-sm text-gray-400 hover:text-orange-300 transition-colors">Tumpeng</Link>
                        </div>
                    </div>

                    <!-- Kontak -->
                    <div class="md:col-span-3">
                        <h4 class="font-semibold text-white mb-4 text-sm">Kontak</h4>
                        <dl v-if="contactRows.length" class="space-y-3 text-sm text-gray-400">
                            <div v-for="row in contactRows" :key="row.key" class="flex items-start gap-3">
                                <span class="text-base mt-0.5" aria-hidden="true">{{ row.icon }}</span>
                                <div><dt class="text-gray-300 font-medium">{{ row.label }}</dt><dd>{{ row.value }}</dd></div>
                            </div>
                        </dl>
                        <Link v-else href="/contact" class="text-sm text-gray-300 underline underline-offset-4 hover:text-white">Kirim pesan lewat formulir kontak</Link>
                    </div>
                </div>

                <!-- Bottom Bar -->
                <div class="mt-10 pt-6 border-t border-gray-800 flex flex-col md:flex-row items-center justify-between gap-4">
                    <p class="text-sm text-gray-400">
                        &copy; {{ new Date().getFullYear() }} CateringKu. Semua hak dilindungi.
                    </p>
                    <div class="flex items-center gap-6 text-sm text-gray-400">
                        <Link href="/terms" class="hover:text-white transition-colors">Syarat &amp; Ketentuan</Link>
                        <Link href="/privacy" class="hover:text-white transition-colors">Kebijakan Privasi</Link>
                    </div>
                </div>
            </div>
        </footer>
    </div>
</template>
