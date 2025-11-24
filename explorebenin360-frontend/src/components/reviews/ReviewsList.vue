<template>
  <div class="space-y-3">
    <div v-if="loading" class="text-sm text-[color:var(--color-text-muted)]">Chargement…</div>
    <div v-else>
      <div v-if="items.length === 0" class="text-sm text-[color:var(--color-text-muted)]">Aucun avis pour le moment.</div>
      <div v-else class="space-y-3">
        <div v-for="r in items" :key="r.id" class="border border-black/10 dark:border-white/10 rounded-md p-4">
          <div class="flex items-start justify-between gap-3">
            <div class="flex-1">
              <div class="flex items-center gap-2">
                <img src="/placeholder/avatar.png" alt="" class="w-8 h-8 rounded-full" />
                <div class="text-sm font-medium">{{ r.user?.name }}</div>
                <div class="text-xs text-[color:var(--color-text-muted)]">• {{ new Date(r.created_at).toLocaleDateString() }}</div>
              </div>
              <div class="mt-2 flex items-center gap-1">
                <span v-for="i in 5" :key="i" class="text-yellow-500">{{ i <= r.rating ? '★' : '☆' }}</span>
              </div>
              <div v-if="r.title" class="mt-1 font-semibold">{{ r.title }}</div>
              <div class="text-sm mt-1 whitespace-pre-line">{{ r.body }}</div>
              <div v-if="r.verified_purchase" class="mt-2 inline-flex items-center text-[10px] px-2 py-0.5 rounded-full bg-green-500/10 text-green-700 dark:text-green-300 border border-green-500/20">Achat vérifié</div>
              <div v-if="r.response" class="mt-3 p-3 rounded-md bg-black/5 dark:bg-white/5">
                <div class="text-xs text-[color:var(--color-text-muted)]">Réponse</div>
                <div class="text-sm">{{ r.response }}</div>
                <div class="text-[10px] text-[color:var(--color-text-muted)] mt-1">par {{ r.responder?.name }} • {{ r.response_at ? new Date(r.response_at).toLocaleString() : '' }}</div>
              </div>
            </div>
            <div class="flex flex-col items-end gap-2">
              <button @click="onHelpful(r)" class="btn-base focus-ring h-8 px-3 rounded-md border border-black/10 dark:border-white/10 text-xs">
                Utile ({{ r.helpful_count }})
              </button>
              <div v-if="isAuthor(r)" class="flex gap-2">
                <button @click="$emit('edit', r)" class="btn-base focus-ring h-8 px-3 rounded-md border border-black/10 dark:border-white/10 text-xs" :disabled="!r.is_editable">Modifier</button>
                <button @click="onDelete(r)" class="btn-base focus-ring h-8 px-3 rounded-md border border-black/10 dark:border-white/10 text-xs">Supprimer</button>
              </div>
            </div>
          </div>
        </div>

        <div class="flex items-center justify-between pt-2" v-if="meta.total > meta.per_page">
          <button class="btn-base h-8 px-3 rounded-md border border-black/10 dark:border-white/10" :disabled="page<=1" @click="page--; load()">Précédent</button>
          <div class="text-xs text-[color:var(--color-text-muted)]">Page {{ meta.current_page }}</div>
          <button class="btn-base h-8 px-3 rounded-md border border-black/10 dark:border-white/10" :disabled="items.length < meta.per_page" @click="page++; load()">Suivant</button>
        </div>
      </div>
    </div>
  </div>
</template>
<script setup lang="ts">
import { ref, onMounted, watch } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { fetchReviews, markHelpful, deleteReview } from '@/lib/services/reviews'
import type { Review, ReviewsResponse } from '@/types/business'

const props = defineProps<{ reviewableType: string; reviewableId: number; verifiedOnly?: boolean; sort?: 'recent'|'rating'|'helpful' }>()
const emit = defineEmits<{ (e: 'updated'): void; (e: 'edit', r: Review): void }>()

const items = ref<Review[]>([])
const meta = ref<ReviewsResponse['meta']>({ total: 0, current_page: 1, per_page: 15, average_rating: 0, total_reviews: 0 })
const loading = ref(false)
const page = ref(1)
const auth = useAuthStore()

onMounted(load)
watch(() => [props.reviewableType, props.reviewableId, props.verifiedOnly, props.sort], () => { page.value = 1; load() })

async function load() {
  loading.value = true
  try {
    const res = await fetchReviews({ reviewable_type: props.reviewableType, reviewable_id: props.reviewableId, verified_only: props.verifiedOnly, sort: props.sort, page: page.value })
    items.value = res.data
    meta.value = res.meta
  } finally { loading.value = false }
}

function isAuthor(r: Review) {
  return auth.user?.id === r.user?.id
}

async function onHelpful(r: Review) {
  try {
    const count = await markHelpful(r.id)
    r.helpful_count = count
  } catch {}
}

async function onDelete(r: Review) {
  if (!confirm('Supprimer cet avis ?')) return
  const prev = [...items.value]
  items.value = items.value.filter(x => x.id !== r.id)
  try {
    await deleteReview(r.id)
    emit('updated')
  } catch { items.value = prev }
}
</script>
