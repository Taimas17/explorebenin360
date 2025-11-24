<template>
  <div class="container-px mx-auto py-8">
    <BrandBanner :src="banner" :title="isEdit ? 'Modifier destination' : 'Créer destination'" class="mb-6" />

    <form @submit.prevent="save" class="grid md:grid-cols-2 gap-4">
      <div class="rounded-md border border-black/10 dark:border-white/10 p-4">
        <div class="grid gap-3">
          <div>
            <label class="form-label">Titre</label>
            <input v-model="form.title" required class="form-input" />
          </div>
          <div>
            <label class="form-label">Type</label>
            <select v-model="form.type" required class="form-input">
              <option value="city">city</option>
              <option value="site">site</option>
              <option value="museum">museum</option>
              <option value="park">park</option>
              <option value="beach">beach</option>
              <option value="culture">culture</option>
              <option value="history">history</option>
              <option value="gastronomy">gastronomy</option>
              <option value="adventure">adventure</option>
              <option value="other">other</option>
            </select>
          </div>
          <div>
            <label class="form-label">Ville</label>
            <input v-model="form.city" required class="form-input" />
          </div>
          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="form-label">Latitude</label>
              <input v-model.number="form.lat" type="number" step="0.000001" required class="form-input" />
            </div>
            <div>
              <label class="form-label">Longitude</label>
              <input v-model.number="form.lng" type="number" step="0.000001" required class="form-input" />
            </div>
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
            <RichTextEditor v-model="form.description" placeholder="Description de la destination" />
          </div>
          <div>
            <label class="form-label">Tags (séparés par des virgules)</label>
            <input v-model="tagsInput" @blur="syncTags" class="form-input" placeholder="plage, musée" />
          </div>
        </div>
      </div>

      <div class="rounded-md border border-black/10 dark:border-white/10 p-4">
        <div class="grid gap-3">
          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="form-label">Prix dès</label>
              <input v-model.number="form.price_from" type="number" step="0.01" class="form-input" />
            </div>
            <div>
              <label class="form-label">Pays</label>
              <input v-model="form.country" class="form-input" />
            </div>
          </div>
          <div>
            <label class="form-label">Horaires (JSON)</label>
            <textarea v-model="openingHoursInput" class="form-input min-h-24" placeholder='{"mon":"9:00-17:00"}'></textarea>
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
            <RouterLink :to="{ name: 'admin-places' }" class="btn-base focus-ring h-9 px-4 rounded-md border border-black/10 dark:border-white/10">Annuler</RouterLink>
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
import { createPlace, getPlace, updatePlace } from '@/lib/services/admin-content'

const banner = '/src/assets/brand/images/dashboard/admin/header.png'
const route = useRoute()
const router = useRouter()
const isEdit = computed(() => !!route.params.id)

const form = ref<any>({ title: '', type: 'city', city: '', lat: 0, lng: 0, status: 'published', description: '', tags: [], price_from: null, country: 'Benin', opening_hours: null, cover_image_url: '', featured: false })
const tagsInput = ref('')
const openingHoursInput = ref('')
function syncTags() { form.value.tags = tagsInput.value.split(',').map(s => s.trim()).filter(Boolean) }
function syncOpening() { try { form.value.opening_hours = openingHoursInput.value ? JSON.parse(openingHoursInput.value) : null } catch (e) { alert('Horaires JSON invalides'); throw e } }

onMounted(async () => {
  if (isEdit.value) {
    const res = await getPlace(Number(route.params.id))
    const data = res.data || res
    form.value = { ...form.value, ...data }
    tagsInput.value = (data.tags || []).join(', ')
    openingHoursInput.value = data.opening_hours ? JSON.stringify(data.opening_hours) : ''
  }
})

async function save() {
  syncTags(); syncOpening()
  if (isEdit.value) await updatePlace(Number(route.params.id), form.value)
  else await createPlace(form.value)
  router.push({ name: 'admin-places' })
}
</script>
<style scoped>
.form-label{ @apply text-xs text-[color:var(--color-text-muted)] block mb-1 }
.form-input{ @apply text-sm rounded-md border border-black/10 dark:border-white/10 bg-transparent px-2 py-1 focus-ring w-full }
</style>
