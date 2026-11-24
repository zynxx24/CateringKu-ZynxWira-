<script setup lang="ts">
import CateringLayout from '@/layouts/CateringLayout.vue'
import { Head, Link } from '@inertiajs/vue3'
import { computed } from 'vue'

const props = defineProps<{
    stats: { vendors: number, completedOrders: number, cities: number, averageRating: number | null }
}>()

const formatNumber = (n: number) => new Intl.NumberFormat('id-ID').format(n)

// Counted live from this site's database; a rating only appears once there are reviews.
const statItems = computed(() => [
    { value: formatNumber(props.stats.vendors), label: 'vendor aktif' },
    { value: formatNumber(props.stats.completedOrders), label: 'pesanan selesai' },
    { value: formatNumber(props.stats.cities), label: 'kota terlayani' },
    ...(props.stats.averageRating !== null ? [{ value: props.stats.averageRating.toFixed(1), label: 'rata-rata rating' }] : []),
])

const steps = [
    { title: 'Pilih vendor', text: 'Cari berdasarkan nama, kota, atau jenis hidangan, lalu buka menu dan ulasannya.' },
    { title: 'Isi detail acara', text: 'Tanggal, jam, alamat, dan jumlah tamu. Porsi tiap menu dikalikan jumlah tamu.' },
    { title: 'Bayar dan pantau', text: 'Pilih metode bayar, admin memverifikasi, vendor mengubah status sampai pesanan selesai.' },
]
</script>

<template>
    <CateringLayout>
        <Head title="Tentang Kami | CateringKu" />

        <section class="grid gap-10 lg:grid-cols-[1.1fr_1fr] lg:items-start mb-14">
            <div>
                <h1 class="text-4xl font-bold text-[var(--ck-text-primary)] mb-4">Tentang CateringKu</h1>
                <p class="text-lg text-[var(--ck-text-secondary)] leading-relaxed mb-4">
                    CateringKu mempertemukan orang yang sedang menyiapkan acara dengan vendor katering di kotanya.
                    Anda bisa membandingkan menu dan harga per porsi, lalu memesan untuk jumlah tamu yang pas.
                </p>
                <p class="text-[var(--ck-text-secondary)] leading-relaxed">
                    Vendor mengelola menu dan pesanannya sendiri dari panel vendor, sementara admin memverifikasi pembayaran.
                    Rating di setiap vendor berasal dari pelanggan yang pesanannya sudah selesai.
                </p>
            </div>

            <dl class="grid grid-cols-2 gap-px overflow-hidden rounded-2xl border border-[var(--ck-surface-border)] bg-[var(--ck-surface-border)]">
                <div v-for="stat in statItems" :key="stat.label" class="bg-[var(--ck-surface)] p-6">
                    <dt class="text-sm text-[var(--ck-text-secondary)]">{{ stat.label }}</dt>
                    <dd class="text-3xl font-bold text-ck-primary tabular-nums mt-1">{{ stat.value }}</dd>
                </div>
            </dl>
        </section>

        <section class="mb-14">
            <h2 class="text-2xl font-bold text-[var(--ck-text-primary)] mb-6">Cara memesan</h2>
            <ol class="grid gap-6 md:grid-cols-3">
                <li v-for="(step, i) in steps" :key="step.title" class="border-t-2 border-ck-primary pt-4">
                    <p class="text-sm font-semibold text-ck-primary tabular-nums">Langkah {{ i + 1 }}</p>
                    <h3 class="font-bold text-[var(--ck-text-primary)] mt-1 mb-2">{{ step.title }}</h3>
                    <p class="text-sm text-[var(--ck-text-secondary)] leading-relaxed">{{ step.text }}</p>
                </li>
            </ol>
        </section>

        <section class="rounded-2xl bg-[var(--ck-surface)] border border-[var(--ck-surface-border)] p-6 md:p-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold text-[var(--ck-text-primary)]">Punya usaha katering?</h2>
                <p class="text-[var(--ck-text-secondary)] text-sm mt-1">Kirim pesan dengan subjek "Daftar sebagai Vendor" dan tim kami akan menghubungi Anda.</p>
            </div>
            <Link href="/contact" class="btn-primary text-center shrink-0">Hubungi kami</Link>
        </section>
    </CateringLayout>
</template>
