<script setup>
</script>

<template>
  <div class="min-h-screen bg-white text-[#0b0f16] dark:bg-[#0b0f16] dark:text-white">
    <header class="border-b border-gray-200 dark:border-white/10">
      <nav class="mx-auto max-w-6xl px-4 py-3 flex items-center justify-between">
        <a href="/" class="text-2xl font-semibold" style="color:#FF6B35">ExploreBenin360</a>
        <ul class="flex gap-4 text-sm">
          <li><router-link to="/" class="hover:underline">Home</router-link></li>
          <li><router-link to="/destinations" class="hover:underline">Destinations</router-link></li>
          <li><router-link to="/hebergements" class="hover:underline">Hébergements</router-link></li>
          <li><router-link to="/guides" class="hover:underline">Guides</router-link></li>
          <li><router-link to="/blog" class="hover:underline">Blog</router-link></li>
        </ul>
      </nav>
    </header>
    <main class="mx-auto max-w-6xl px-4 py-8">
      <router-view />
    </main>
  </div>
</template>

<style scoped>
:root { font-family: Inter, system-ui, -apple-system, Segoe UI, Roboto, Ubuntu, Cantarell, Noto Sans, Arial; }
</style>
