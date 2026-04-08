<template>
  <div class="min-h-screen bg-slate-50 flex items-center justify-center px-4">
    <div class="w-full max-w-md">
      <!-- Logo -->
      <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-blue-600 mb-4">
          <span class="text-white text-2xl font-bold">O</span>
        </div>
        <h1 class="text-3xl font-bold text-slate-900" style="font-family: var(--font-display); letter-spacing: -0.03em;">Onfly Rewards</h1>
        <p class="text-sm text-slate-500 mt-1">Faça login para continuar</p>
      </div>

      <!-- Card -->
      <div class="bg-white rounded-2xl border border-slate-100 p-8 shadow-sm">
        <form @submit.prevent="handleLogin" class="space-y-5">
          <!-- Email -->
          <div class="flex flex-col gap-1.5">
            <label for="email" class="text-sm font-medium text-slate-700">Email corporativo</label>
            <input
              id="email"
              v-model="form.email"
              type="email"
              placeholder="seu@email.com.br"
              required
              autocomplete="email"
              :disabled="loading"
              class="w-full h-10 px-3 text-sm text-slate-900 bg-slate-50 rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-300 focus:border-blue-400 transition-all duration-150 disabled:opacity-50"
            />
          </div>

          <!-- Password -->
          <div class="flex flex-col gap-1.5">
            <label for="password" class="text-sm font-medium text-slate-700">Senha</label>
            <input
              id="password"
              v-model="form.password"
              type="password"
              placeholder="••••••••"
              required
              autocomplete="current-password"
              :disabled="loading"
              class="w-full h-10 px-3 text-sm text-slate-900 bg-slate-50 rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-300 focus:border-blue-400 transition-all duration-150 disabled:opacity-50"
            />
          </div>

          <!-- Error message -->
          <div v-if="error" class="flex items-center gap-2 p-3 rounded-lg bg-red-50 border border-red-200 text-sm text-red-700">
            <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
            </svg>
            {{ error }}
          </div>

          <!-- Submit button -->
          <button
            type="submit"
            :disabled="loading"
            class="w-full h-10 flex items-center justify-center gap-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 active:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-150 disabled:opacity-60 disabled:cursor-not-allowed"
          >
            <svg v-if="loading" class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 22 6.477 22 12h-4zm2 9.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            {{ loading ? 'Entrando...' : 'Entrar' }}
          </button>
        </form>

        <!-- Hint for demo -->
        <div class="mt-6 pt-5 border-t border-slate-100">
          <p class="text-xs text-slate-400 text-center">Demo: use qualquer email de usuário seeded com senha <span class="font-mono">password</span></p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const auth = useAuthStore()
const router = useRouter()

const form = ref({ email: '', password: '' })
const loading = ref(false)
const error = ref('')

async function handleLogin() {
  error.value = ''
  loading.value = true

  try {
    const user = await auth.login(form.value.email, form.value.password)

    if (user.role === 'traveler') {
      router.push('/traveler/search')
    } else {
      router.push('/approver/dashboard')
    }
  } catch (e) {
    error.value = e.response?.data?.message || 'Email ou senha incorretos.'
  } finally {
    loading.value = false
  }
}
</script>
