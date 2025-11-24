<template>
  <div class="container-px mx-auto py-8">
    <BrandBanner :src="banner" :title="isEdit ? 'Modifier guide' : 'Créer guide'" class="mb-6" />

    <form @submit.prevent="save" class="grid md:grid-cols-2 gap-4">
      <div class="rounded-md border border-black/10 dark:border-white/10 p-4">
        <div class="grid gap-3">
          <div>
            <label class="form-label">Nom</label>
            <input v-model="form.name" required class="form-input" />
          </div>
          <div>
            <label class="form-label">Ville</label>
            <input v-model="form.city" required class="form-input" />
          </div>
          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="form-label">Latitude</label>
              <input v-model.number="form.lat" type="number" step="0.000001" class="form-input" />
            </div>
            <div>
              <label class="form-label">Longitude</label>
              <input v-model.number="form.lng" type="number" step="0.000001" class="form-input" />
            </div>
          </div>
          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="form-label">Prix/jour</label>
              <input v-model.number="form.price_per_day" type="number" step="0.01" class="form-input" />
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
            <label class="form-label">Bio</label>
            <RichTextEditor v-model="form.bio" placeholder="Présentation du guide" />
          </div>
          <div>
            <label class="form-label">Langues (séparées par des virgules)</label>
            <input v-model="languagesInput" @blur="syncLanguages" class="form-input" placeholder="français, anglais" />
          </div>
          <div>
            <label class="form-label">Spécialités (séparées par des virgules)</label>
            <input v-model="specialtiesInput" @blur="syncSpecialties" class="form-input" placeholder="histoire, gastronomie" />
          </div>
        </div>
      </div>

      <div class="rounded-md border border-black/10 dark:border-white/10 p-4">
        <div class="grid gap-3">
          <div>
            <label class="form-label">Avatar</label>
            <ImageUpload @uploaded="(url) => form.avatar_url = url" />
            <input v-model="form.avatar_url" class="form-input mt-2" placeholder="ou collez une URL" />
          </div>
          <div class="flex items-center gap-2">
            <input id="verified" type="checkbox" v-model="form.verified" />
            <label for="verified">Vérifié</label>
          </div>
          <div class="flex gap-2">
            <button type="submit" class="btn-base focus-ring h-9 px-4 rounded-md border border-black/10 dark:border-white/10">{{ isEdit ? 'Mettre à jour' : 'Créer' }}</button>
            <RouterLink :to="{ name: 'admin-guides' }" class="btn-base focus-ring h-9 px-4 rounded-md border border-black/10 dark:border-white/10">Annuler</RouterLink>
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
import { createGuide, getGuide, updateGuide } from '@/lib/services/admin-content'

const banner = '/src/assets/brand/images/dashboard/admin/header.png'
const route = useRoute()
const router = useRouter()
const isEdit = computed(() => !!route.params.id)

const form = ref<any>({ name: '', city: '', lat: null, lng: null, price_per_day: null, currency: 'XOF', status: 'published', bio: '', languages: [], specialties: [], avatar_url: '', verified: false })
const languagesInput = ref('')
const specialtiesInput = ref('')
function syncLanguages() { form.value.languages = languagesInput.value.split(',').map(s => s.trim()).filter(Boolean) }
function syncSpecialties() { form.value.specialties = specialtiesInput.value.split(',').map(s => s.trim()).filter(Boolean) }

onMounted(async () => {
  if (isEdit.value) {
    const res = await getGuide(Number(route.params.id))
    const data = res.data || res
    form.value = { ...form.value, ...data }
    languagesInput.value = (data.languages || []).join(', ')
    specialtiesInput.value = (data.specialties || []).join(', ')
  }
})

async function save() {
  syncLanguages(); syncSpecialties()
  if (isEdit.value) await updateGuide(Number(route.params.id), form.value)
  else await createGuide(form.value)
  router.push({ name: 'admin-guides' })
}
</script>
<style scoped>
.form-label{ @apply text-xs text-[color:var(--color-text-muted)] block mb-1 }
.form-input{ @apply text-sm rounded-md border border-black/10 dark:border-white/10 bg-transparent px-2 py-1 focus-ring w-full }
</style>
