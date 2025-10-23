<template>
  <div class="min-h-screen flex flex-col">
    <Navbar />
    <main class="flex-1">
      <RouterView />
    </main>
    <Footer />
    <MobileTabBar />
  </div>
</template>

<script setup>
import { RouterView } from 'vue-router'
import Navbar from '@/components/layout/Navbar.vue'
import Footer from '@/components/layout/Footer.vue'
import MobileTabBar from '@/components/layout/MobileTabBar.vue'

const stored = localStorage.getItem('eb360:theme')
if (stored) {
  document.documentElement.classList.toggle('dark', stored === 'dark')
} else if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
  document.documentElement.classList.add('dark')
}
</script>
