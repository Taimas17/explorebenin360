<template>
  <div class="container-px mx-auto py-8">
    <BrandBanner :src="banner" :title="isEdit ? 'Modifier événement' : 'Créer événement'" class="mb-6" />

    <form @submit.prevent="save" class="grid md:grid-cols-2 gap-4">
      <div class="rounded-md border border-black/10 dark:border-white/10 p-4">
        <div class="grid gap-3">
          <div>
            <label class="form-label">Titre</label>
            <input v-model="form.title" required class="form-input" />
          </div>
          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="form-label">Ville</label>
              <input v-model="form.city" required class="form-input" />
            </div>
            <div>
              <label class="form-label">Catégorie</label>
              <input v-model="form.category" class="form-input" />
            </div>
          </div>
          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="form-label">Date début</label>
              <input v-model="form.start_date" type="date" required class="form-input" />
            </div>
            <div>
              <label class="form-label">Date fin</label>
              <input v-model="form.end_date" type="date" required class="form-input" />
            </div>
          </div>
          <div>
            <label class="form-label">Organisateur</label>
            <input v-model="form.organizer_name" class="form-input" />
          </div>
          <div>
            <label class="form-label">Contact</label>
            <input v-model="form.organizer_contact" class="form-input" />
          </div>
          <div>
            <label class="form-label">Statut</label>
            <select v-model="form.status" class="form-input">
              <option value="published">published</option>
              <option value="draft">draft</option>
            </select>
          </div>
          <div>
            <label class="form-label">Description</label>
            <RichTextEditor v-model="form.description" placeholder="Description de l'événement" />
          </div>
        </div>
      </div>

      <div class="rounded-md border border-black/10 dark:border-white/10 p-4">
        <div class="grid gap-3">
          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="form-label">Prix</label>
              <input v-model.number="form.price" type="number" step="0.01" class="form-input" />
            </div>
            <div>
              <label class="form-label">Devise</label>
              <input v-model="form.currency" maxlength="3" class="form-input" />
            </div>
          </div>
          <div>
            <label class="form-label">Image de couverture</label>
            <ImageUpload @uploaded="(url) => form.cover_image_url = url" />
            <input v-model="form.cover_image_url" class="form-input mt-2" placeholder="ou collez une URL" />
          </div>
          <div class="flex items-center gap-2">
            <input id="featured" type="checkbox" v-model="form.featured" />
            <label for="featured">Mettre en avant</label>
          </div>
          <div class="flex gap-2">
            <button type="submit" class="btn-base focus-ring h-9 px-4 rounded-md border border-black/10 dark:border-white/10">{{ isEdit ? 'Mettre à jour' : 'Créer' }}</button>
            <RouterLink :to="{ name: 'admin-events' }" class="btn-base focus-ring h-9 px-4 rounded-md border border-black/10 dark:border-white/10">Annuler</RouterLink>
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
import { createEvent, getEvent, updateEvent } from '@/lib/services/admin-content'

const banner = '/src/assets/brand/images/dashboard/admin/header.png'
const route = useRoute()
const router = useRouter()
const isEdit = computed(() => !!route.params.id)

const form = ref<any>({ title: '', city: '', category: 'general', start_date: '', end_date: '', organizer_name: '', organizer_contact: '', status: 'published', description: '', price: null, currency: 'XOF', cover_image_url: '', featured: false })

onMounted(async () => {
  if (isEdit.value) {
    const res = await getEvent(Number(route.params.id))
    const data = res.data || res
    form.value = { ...form.value, ...data }
  }
})

async function save() {
  if (isEdit.value) await updateEvent(Number(route.params.id), form.value)
  else await createEvent(form.value)
  router.push({ name: 'admin-events' })
}
</script>
<style scoped>
.form-label{ @apply text-xs text-[color:var(--color-text-muted)] block mb-1 }
.form-input{ @apply text-sm rounded-md border border-black/10 dark:border-white/10 bg-transparent px-2 py-1 focus-ring w-full }
</style>
