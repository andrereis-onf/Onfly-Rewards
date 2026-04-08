<template>
  <div class="min-h-screen bg-slate-50">
    <!-- Header -->
    <TheHeader>
      <template #title>
        <span class="text-sm text-slate-600">{{ pageTitle }}</span>
      </template>
    </TheHeader>

    <!-- Sidebar -->
    <TheSidebar :role="auth.role" />

    <!-- Main content -->
    <main class="ml-60 pt-16 min-h-screen">
      <div class="p-8">
        <slot />
      </div>
    </main>

    <!-- Mobile overlay (when sidebar is open) -->
    <!-- Mobile support: sidebar is always visible on desktop -->
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { useRoute } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import TheHeader from '../components/layout/TheHeader.vue'
import TheSidebar from '../components/layout/TheSidebar.vue'

const auth = useAuthStore()
const route = useRoute()

const pageTitles = {
  'traveler.search': 'Buscar Hotel',
  'traveler.wallet': 'Minha Carteira',
  'approver.dashboard': 'Dashboard de Economia',
  'approver.ranking': 'Ranking de Economia',
}

const pageTitle = computed(() => pageTitles[route.name] || '')
</script>
