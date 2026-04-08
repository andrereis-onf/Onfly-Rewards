<template>
  <AppShell>
    <div class="max-w-4xl mx-auto space-y-6">
      <!-- Header -->
      <div>
        <h1 class="text-2xl md:text-3xl font-bold text-slate-900" style="font-family: var(--font-display); letter-spacing: -0.025em;">Dashboard de Economia</h1>
        <p class="text-sm text-slate-500 mt-1">Visão geral da economia gerada pela equipe</p>
      </div>

      <!-- Loading -->
      <div v-if="loading" class="grid grid-cols-2 gap-4">
        <div v-for="i in 4" :key="i" class="bg-white rounded-2xl border border-slate-100 p-5 animate-pulse h-28"></div>
      </div>

      <!-- Metrics grid -->
      <div v-else class="grid grid-cols-2 gap-4">
        <MetricCard
          label="Economia Total da Empresa"
          :value="data.total_company_savings"
          format="currency"
          :icon="TrendingDown"
          icon-bg="bg-blue-50"
          icon-color="text-blue-600"
          value-color="text-blue-700"
          subtitle="Soma das economias geradas"
        />
        <MetricCard
          label="Onhappy Coins Distribuídos"
          :value="data.total_onhappy_coins_distributed"
          format="currency"
          :icon="Wallet"
          icon-bg="bg-green-50"
          icon-color="text-green-600"
          value-color="text-green-700"
          subtitle="Créditos na carteira dos viajantes"
        />
        <MetricCard
          label="Reservas com Economia"
          :value="data.total_bookings_with_savings"
          format="number"
          :icon="CheckCircle"
          icon-bg="bg-slate-100"
          icon-color="text-slate-600"
          value-color="text-slate-900"
          subtitle="Bookings abaixo do teto da política"
        />
        <MetricCard
          label="Viajantes Ativos"
          :value="data.active_travelers"
          format="number"
          :icon="Users"
          icon-bg="bg-purple-50"
          icon-color="text-purple-600"
          value-color="text-slate-900"
          subtitle="Com pelo menos 1 reserva"
        />
      </div>

      <!-- By modal breakdown -->
      <div v-if="!loading" class="bg-white rounded-2xl border border-slate-100 p-6">
        <h2 class="text-base font-semibold text-slate-900 mb-4">Economia por Modal</h2>
        <div class="grid grid-cols-4 gap-4">
          <div
            v-for="(info, modal) in data.by_modal"
            :key="modal"
            class="flex flex-col items-center gap-2 p-4 bg-slate-50 rounded-xl text-center"
          >
            <component :is="modalIcon(modal)" class="w-7 h-7 text-slate-500" />
            <p class="text-xs font-medium text-slate-500 capitalize">{{ modalLabel(modal) }}</p>
            <p class="text-lg font-bold text-blue-700 font-mono">{{ formatCurrency(info.savings) }}</p>
            <p class="text-xs text-slate-400">{{ info.count }} reserva{{ info.count !== 1 ? 's' : '' }}</p>
          </div>
        </div>
      </div>

      <!-- Win-Win callout -->
      <div v-if="!loading && data.total_company_savings > 0" class="rounded-2xl bg-gradient-to-r from-blue-600 to-green-600 p-6 text-white">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm font-medium text-blue-100 mb-1">Modelo Win-Win em ação</p>
            <p class="text-2xl font-bold font-mono">{{ formatCurrency(data.total_company_savings + data.total_onhappy_coins_distributed) }}</p>
            <p class="text-sm text-blue-100 mt-1">em valor total gerado para empresa e viajantes</p>
          </div>
          <div class="text-5xl">🎯</div>
        </div>
      </div>
    </div>
  </AppShell>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import { TrendingDown, Wallet, CheckCircle, Users, Hotel, Plane, Bus, Car } from 'lucide-vue-next'
import AppShell from '../../layouts/AppShell.vue'
import MetricCard from '../../components/approver/MetricCard.vue'

const loading = ref(true)
const data = ref({
  total_company_savings: 0,
  total_onhappy_coins_distributed: 0,
  total_bookings_with_savings: 0,
  active_travelers: 0,
  by_modal: {},
})

function formatCurrency(v) {
  return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(v ?? 0)
}

function modalIcon(modal) {
  return { hotel: Hotel, flight: Plane, bus: Bus, car: Car }[modal] ?? Plane
}

function modalLabel(modal) {
  return { hotel: 'Hotel', flight: 'Aéreo', bus: 'Ônibus', car: 'Carro' }[modal] ?? modal
}

onMounted(async () => {
  try {
    const res = await axios.get('/approver/dashboard')
    data.value = res.data
  } catch (e) {
    console.error('Erro ao carregar dashboard', e)
  } finally {
    loading.value = false
  }
})
</script>
