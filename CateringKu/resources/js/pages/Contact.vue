<script setup lang="ts">
import CateringLayout from '@/layouts/CateringLayout.vue'
import { Head, useForm, usePage } from '@inertiajs/vue3'
import { computed } from 'vue'

const page = usePage()
const form = useForm({ name: '', email: '', subject: '', message: '' })

// Contact details come from config (CATERINGKU_CONTACT_*); nothing is shown until the owner fills them in.
const contactRows = computed(() => {
    const contact = (page.props as any).site?.contact ?? {}

    return [
        { key: 'email', label: 'Email', icon: '📧' },
        { key: 'phone', label: 'Telepon', icon: '📞' },
        { key: 'address', label: 'Alamat', icon: '📍' },
        { key: 'hours', label: 'Jam operasional', icon: '🕒' },
    ].filter((r) => contact[r.key]).map((r) => ({ ...r, value: contact[r.key] as string }))
})

const fieldClass = 'w-full px-4 py-3 rounded-xl border border-[var(--ck-surface-border)] bg-[var(--ck-surface)] text-[var(--ck-text-primary)] focus:ring-2 focus:ring-ck-primary focus:border-ck-primary text-sm'

function submit() {
    form.post('/contact', {
        preserveScroll: true,
        onSuccess: () => form.reset(),
    })
}
</script>

<template>
    <CateringLayout>
        <Head title="Hubungi Kami | CateringKu" />
        <h1 class="text-3xl font-bold text-[var(--ck-text-primary)] mb-2">Hubungi Kami</h1>
        <p class="text-[var(--ck-text-secondary)] mb-8">Pertanyaan soal pesanan, kerja sama, atau ingin mendaftar sebagai vendor? Tulis di sini.</p>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2">
                <div class="bg-[var(--ck-surface)] rounded-2xl border border-[var(--ck-surface-border)] p-6 md:p-8">
                    <h2 class="font-bold text-[var(--ck-text-primary)] text-lg mb-6">Kirim pesan</h2>
                    <form class="space-y-5" novalidate @submit.prevent="submit">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label for="contact-name" class="block text-sm font-medium text-[var(--ck-text-primary)] mb-1.5">Nama *</label>
                                <input id="contact-name" v-model="form.name" type="text" required maxlength="100" autocomplete="name" :class="fieldClass" placeholder="Nama lengkap" :aria-invalid="!!form.errors.name" aria-describedby="contact-name-error" />
                                <p v-if="form.errors.name" id="contact-name-error" class="text-red-600 dark:text-red-400 text-xs mt-1">{{ form.errors.name }}</p>
                            </div>
                            <div>
                                <label for="contact-email" class="block text-sm font-medium text-[var(--ck-text-primary)] mb-1.5">Email *</label>
                                <input id="contact-email" v-model="form.email" type="email" required maxlength="100" autocomplete="email" :class="fieldClass" placeholder="nama@email.com" :aria-invalid="!!form.errors.email" aria-describedby="contact-email-error" />
                                <p v-if="form.errors.email" id="contact-email-error" class="text-red-600 dark:text-red-400 text-xs mt-1">{{ form.errors.email }}</p>
                            </div>
                        </div>
                        <div>
                            <label for="contact-subject" class="block text-sm font-medium text-[var(--ck-text-primary)] mb-1.5">Subjek *</label>
                            <select id="contact-subject" v-model="form.subject" required :class="fieldClass" :aria-invalid="!!form.errors.subject" aria-describedby="contact-subject-error">
                                <option value="">Pilih subjek</option>
                                <option value="general">Pertanyaan Umum</option>
                                <option value="order">Masalah Pesanan</option>
                                <option value="partnership">Kerjasama Bisnis</option>
                                <option value="vendor">Daftar sebagai Vendor</option>
                                <option value="feedback">Saran & Masukan</option>
                                <option value="complaint">Komplain</option>
                            </select>
                            <p v-if="form.errors.subject" id="contact-subject-error" class="text-red-600 dark:text-red-400 text-xs mt-1">{{ form.errors.subject }}</p>
                        </div>
                        <div>
                            <label for="contact-message" class="block text-sm font-medium text-[var(--ck-text-primary)] mb-1.5">Pesan *</label>
                            <textarea id="contact-message" v-model="form.message" rows="5" required maxlength="2000" :class="fieldClass" placeholder="Tulis pesan Anda..." :aria-invalid="!!form.errors.message" aria-describedby="contact-message-error"></textarea>
                            <p v-if="form.errors.message" id="contact-message-error" class="text-red-600 dark:text-red-400 text-xs mt-1">{{ form.errors.message }}</p>
                        </div>
                        <button type="submit" :disabled="form.processing" class="btn-primary disabled:opacity-60">
                            {{ form.processing ? 'Mengirim...' : 'Kirim pesan' }}
                        </button>
                    </form>
                </div>
            </div>

            <aside v-if="contactRows.length" class="space-y-4" aria-label="Kontak">
                <div v-for="info in contactRows" :key="info.key" class="bg-[var(--ck-surface)] rounded-2xl border border-[var(--ck-surface-border)] p-5 flex items-center gap-4">
                    <span class="w-12 h-12 bg-ck-primary-lighter dark:bg-ck-primary/15 rounded-xl flex items-center justify-center text-xl shrink-0" aria-hidden="true">{{ info.icon }}</span>
                    <div>
                        <p class="font-semibold text-[var(--ck-text-primary)] text-sm">{{ info.label }}</p>
                        <p class="text-[var(--ck-text-secondary)] text-sm">{{ info.value }}</p>
                    </div>
                </div>
            </aside>
        </div>
    </CateringLayout>
</template>
