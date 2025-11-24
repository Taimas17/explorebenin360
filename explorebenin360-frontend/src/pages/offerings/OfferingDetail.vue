<template>
  <div class="container-px mx-auto py-8" v-if="item">
    <h1 class="text-3xl font-bold mb-2">{{ item.title }}</h1>
    <p class="text-[color:var(--color-text-muted)] mb-4">{{ item.type }} • {{ item.currency }} {{ item.price }} • {{ t('offerings.capacity') }} {{ item.capacity }}</p>
    <p class="mb-6">{{ item.description }}</p>
    <RouterLink :to="{ name: 'checkout', params: { slug: item.slug } }" class="inline-flex btn-base focus-ring h-10 px-5 rounded-md text-white" :style="{ backgroundColor: 'var(--color-primary)' }">
      {{ t('checkout.book_now') }}
    </RouterLink>

    <section class="mt-10 space-y-4">
      <h2 class="text-xl font-semibold">Avis</h2>
      <RatingSummary v-if="item.reviews_summary" :summary="item.reviews_summary" />
      <ReviewForm v-if="isAuthenticated" :reviewable-type="'App\\\\Models\\\\Offering'" :reviewable-id="item.id" @success="reload" />
      <ReviewsList :reviewable-type="'App\\\\Models\\\\Offering'" :reviewable-id="item.id" @updated="reload" />
    </section>
  </div>
</template>
<script setup>
import { onMounted, ref, computed } from 'vue'
import { useRoute } from 'vue-router'
import { useI18n } from 'vue-i18n'
import { fetchOffering } from '@/lib/api'
import RatingSummary from '@/components/reviews/RatingSummary.vue'
import ReviewsList from '@/components/reviews/ReviewsList.vue'
import ReviewForm from '@/components/reviews/ReviewForm.vue'
import { useAuthStore } from '@/stores/auth'
import { setPageMeta } from '@/utils/meta'

const { t } = useI18n()
const route = useRoute()
const item = ref<any>(null)
const auth = useAuthStore()
const isAuthenticated = computed(() => auth.isAuthenticated)

onMounted(load)

async function load() {
  const res = await fetchOffering(route.params.slug)
  item.value = res.data
  setPageMeta({ title: `${item.value.title} — ExploreBenin360`, description: (item.value.description || '').slice(0,160), path: `/offerings/${item.value.slug}` })
}

function reload() { load() }
</script>
