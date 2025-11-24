<template>
  <div class="container-px mx-auto py-8">
    <BrandBanner :src="banner" :title="isEdit ? 'Modifier hébergement' : 'Créer hébergement'" class="mb-6" />

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
              <option value="hotel">hotel</option>
              <option value="guesthouse">guesthouse</option>
              <option value="ecolodge">ecolodge</option>
              <option value="bnb">bnb</option>
              <option value="other">other</option>
            </select>
          </div>
          <div>
            <label class="form-label">Adresse</label>
            <input v-model="form.address" required class="form-input" />
          </div>
          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="form-label">Ville</label>
              <input v-model="form.city" required class="form-input" />
            </div>
            <div>
              <label class="form-label">Capacité</label>
              <input v-model.number="form.capacity" type="number" min="1" class="form-input" />
            </div>
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
          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="form-label">Prix/nuit</label>
              <input v-model.number="form.price_per_night" type="number" step="0.01" required class="form-input" />
            </div>
            <div>
              <label class="form-label">Devise</label>
              <input v-model="form.currency" maxlength="3" class="form-input" />
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
            <RichTextEditor v-model="form.description" placeholder="Description détaillée" />
          </div>
          <div>
            <label class="form-label">Commodités (séparées par des virgules)</label>
            <input v-model="amenitiesInput" @blur="syncAmenities" class="form-input" placeholder="wifi, parking, piscine" />
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
          <div class="flex items-center gap-2">
            <input id="featured" type="checkbox" v-model="form.featured" />
            <label for="featured">Mettre en avant</label>
          </div>
          <div class="flex gap-2">
            <button type="submit" class="btn-base focus-ring h-9 px-4 rounded-md border border-black/10 dark:border-white/10">{{ isEdit ? 'Mettre à jour' : 'Créer' }}</button>
            <RouterLink :to="{ name: 'admin-accommodations' }" class="btn-base focus-ring h-9 px-4 rounded-md border border-black/10 dark:border-white/10">Annuler</RouterLink>
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
import { createAccommodation, getAccommodation, updateAccommodation } from '@/lib/services/admin-content'

const banner = '/src/assets/brand/images/dashboard/admin/header.png'
const route = useRoute()
const router = useRouter()
const isEdit = computed(() => !!route.params.id)

const form = ref<any>({
  title: '', type: 'hotel', address: '', city: '', lat: 0, lng: 0, price_per_night: 0, currency: 'XOF',
  status: 'published', description: '', amenities: [], featured: false, cover_image_url: ''
})
const amenitiesInput = ref('')
function syncAmenities() { form.value.amenities = amenitiesInput.value.split(',').map(s => s.trim()).filter(Boolean) }

onMounted(async () => {
  if (isEdit.value) {
    const res = await getAccommodation(Number(route.params.id))
    const data = res.data || res
    form.value = { ...form.value, ...data }
    amenitiesInput.value = (data.amenities || []).join(', ')
  }
})

async function save() {
  syncAmenities()
  if (isEdit.value) await updateAccommodation(Number(route.params.id), form.value)
  else await createAccommodation(form.value)
  router.push({ name: 'admin-accommodations' })
}
</script>
<style scoped>
.form-label{ @apply text-xs text-[color:var(--color-text-muted)] block mb-1 }
.form-input{ @apply text-sm rounded-md border border-black/10 dark:border-white/10 bg-transparent px-2 py-1 focus-ring w-full }
</style>
