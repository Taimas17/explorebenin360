<template>
  <div class="container-px mx-auto py-8">
    <BrandBanner :src="banner" :title="isEdit ? 'Modifier article' : 'Créer article'" class="mb-6" />

    <form @submit.prevent="save" class="grid md:grid-cols-2 gap-4">
      <div class="rounded-md border border-black/10 dark:border-white/10 p-4">
        <div class="grid gap-3">
          <div>
            <label class="form-label">Titre</label>
            <input v-model="form.title" required class="form-input" />
          </div>
          <div>
            <label class="form-label">Auteur</label>
            <input v-model="form.author_name" required class="form-input" />
          </div>
          <div>
            <label class="form-label">Catégorie</label>
            <input v-model="form.category" class="form-input" placeholder="culture, nature..." />
          </div>
          <div>
            <label class="form-label">Statut</label>
            <select v-model="form.status" class="form-input">
              <option value="published">published</option>
              <option value="draft">draft</option>
            </select>
          </div>
          <div>
            <label class="form-label">Extrait</label>
            <textarea v-model="form.excerpt" class="form-input min-h-24"></textarea>
          </div>
          <div>
            <label class="form-label">Contenu</label>
            <RichTextEditor v-model="form.body" placeholder="Contenu de l'article" />
          </div>
        </div>
      </div>

      <div class="rounded-md border border-black/10 dark:border-white/10 p-4">
        <div class="grid gap-3">
          <div>
            <label class="form-label">Image de couverture</label>
            <ImageUpload @uploaded="(url) => form.cover_image_url = url" />
            <input v-model="form.cover_image_url" class="form-input mt-2" placeholder="ou collez une URL" />
          </div>
          <div>
            <label class="form-label">Tags (séparés par des virgules)</label>
            <input v-model="tagsInput" @blur="syncTags" class="form-input" placeholder="benin, voyage" />
          </div>
          <div class="flex gap-2">
            <button type="submit" class="btn-base focus-ring h-9 px-4 rounded-md border border-black/10 dark:border-white/10">{{ isEdit ? 'Mettre à jour' : 'Créer' }}</button>
            <RouterLink :to="{ name: 'admin-articles' }" class="btn-base focus-ring h-9 px-4 rounded-md border border-black/10 dark:border-white/10">Annuler</RouterLink>
          </div>
        </div>
      </div>
    </form>
  </div>
</template>
<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import BrandBanner from '@/components/ui/BrandBanner.vue'
import ImageUpload from '@/components/admin/ImageUpload.vue'
import RichTextEditor from '@/components/admin/RichTextEditor.vue'
import { createArticle, getArticle, updateArticle } from '@/lib/services/admin-content'

const banner = '/src/assets/brand/images/dashboard/admin/header.png'
const route = useRoute()
const router = useRouter()
const isEdit = computed(() => !!route.params.id)

const form = ref<any>({ title: '', author_name: '', category: 'general', status: 'published', excerpt: '', body: '', cover_image_url: '', tags: [] })
const tagsInput = ref('')
function syncTags() { form.value.tags = tagsInput.value.split(',').map(s => s.trim()).filter(Boolean) }

onMounted(async () => {
  if (isEdit.value) {
    const res = await getArticle(Number(route.params.id))
    const data = res.data || res
    form.value = { ...form.value, ...data }
    tagsInput.value = (data.tags || []).join(', ')
  }
})

async function save() {
  syncTags()
  if (isEdit.value) await updateArticle(Number(route.params.id), form.value)
  else await createArticle(form.value)
  router.push({ name: 'admin-articles' })
}
</script>
<style scoped>
.form-label{ @apply text-xs text-[color:var(--color-text-muted)] block mb-1 }
.form-input{ @apply text-sm rounded-md border border-black/10 dark:border-white/10 bg-transparent px-2 py-1 focus-ring w-full }
</style>
