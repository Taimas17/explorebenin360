<template>
  <div class="container-px mx-auto py-8">
    <div v-if="!auth.user" class="text-sm">{{ t('nav.login') }}</div>
    <div v-else>
      <div class="space-y-4">
        <CoverImageUpload :current-cover-url="local.cover_image_url" @uploaded="onCoverUploaded" />
        <div class="flex items-end gap-4 -mt-10 pl-2">
          <AvatarUpload :current-avatar-url="local.avatar_url" :name="local.name" :size="112" @uploaded="onAvatarUploaded" />
          <div class="pb-2">
            <h1 class="text-2xl font-bold">{{ local.name }}</h1>
            <p class="text-[color:var(--color-text-muted)] text-sm">{{ local.email }}</p>
          </div>
        </div>

        <div class="grid md:grid-cols-2 gap-6">
          <section class="border border-black/10 dark:border-white/10 rounded-lg p-4">
            <h2 class="font-semibold mb-3">Informations personnelles</h2>
            <form @submit.prevent="saveSection('personal')" class="grid gap-3">
              <div class="grid sm:grid-cols-2 gap-3">
                <div>
                  <label class="block text-sm mb-1">Nom</label>
                  <input v-model="local.name" class="input" />
                </div>
                <div>
                  <label class="block text-sm mb-1">Email</label>
                  <input v-model="local.email" type="email" class="input" />
                </div>
              </div>
              <div class="grid sm:grid-cols-3 gap-3">
                <div>
                  <label class="block text-sm mb-1">Téléphone</label>
                  <input v-model="local.phone" class="input" />
                </div>
                <div>
                  <label class="block text-sm mb-1">Date de naissance</label>
                  <input v-model="local.date_of_birth" type="date" class="input" />
                </div>
                <div>
                  <label class="block text-sm mb-1">Genre</label>
                  <select v-model="local.gender" class="input">
                    <option :value="null">-</option>
                    <option value="male">Homme</option>
                    <option value="female">Femme</option>
                    <option value="other">Autre</option>
                    <option value="prefer_not_to_say">Ne pas dire</option>
                  </select>
                </div>
              </div>
              <div>
                <label class="block text-sm mb-1">À propos de moi</label>
                <textarea v-model="local.about_me" rows="3" class="input"></textarea>
              </div>
              <div class="flex justify-end">
                <button class="btn-base h-10 px-4 rounded-md text-white" :style="{ backgroundColor: 'var(--color-primary)' }" :disabled="saving.personal">
                  <span v-if="!saving.personal">Enregistrer</span>
                  <span v-else>Sauvegarde...</span>
                </button>
              </div>
            </form>
          </section>

          <section class="border border-black/10 dark:border-white/10 rounded-lg p-4">
            <h2 class="font-semibold mb-3">Localisation</h2>
            <form @submit.prevent="saveSection('location')" class="grid gap-3">
              <div class="grid sm:grid-cols-2 gap-3">
                <div>
                  <label class="block text-sm mb-1">Pays</label>
                  <input v-model="local.country" class="input" />
                </div>
                <div>
                  <label class="block text-sm mb-1">Ville</label>
                  <input v-model="local.city" class="input" />
                </div>
              </div>
              <div class="grid sm:grid-cols-2 gap-3">
                <div>
                  <label class="block text-sm mb-1">Adresse</label>
                  <input v-model="local.address" class="input" />
                </div>
                <div>
                  <label class="block text-sm mb-1">Code postal</label>
                  <input v-model="local.postal_code" class="input" />
                </div>
              </div>
              <div class="flex justify-end">
                <button class="btn-base h-10 px-4 rounded-md text-white" :style="{ backgroundColor: 'var(--color-primary)' }" :disabled="saving.location">
                  <span v-if="!saving.location">Enregistrer</span>
                  <span v-else>Sauvegarde...</span>
                </button>
              </div>
            </form>
          </section>

          <section class="border border-black/10 dark:border-white/10 rounded-lg p-4">
            <h2 class="font-semibold mb-3">Liens sociaux</h2>
            <form @submit.prevent="saveSection('social')" class="grid gap-3">
              <div>
                <label class="block text-sm mb-1">Site web</label>
                <input v-model="local.website_url" class="input" />
              </div>
              <div class="grid sm:grid-cols-2 gap-3">
                <div>
                  <label class="block text-sm mb-1">Facebook</label>
                  <input v-model="local.social_links.facebook" class="input" />
                </div>
                <div>
                  <label class="block text-sm mb-1">Instagram</label>
                  <input v-model="local.social_links.instagram" class="input" />
                </div>
                <div>
                  <label class="block text-sm mb-1">Twitter</label>
                  <input v-model="local.social_links.twitter" class="input" />
                </div>
                <div>
                  <label class="block text-sm mb-1">LinkedIn</label>
                  <input v-model="local.social_links.linkedin" class="input" />
                </div>
              </div>
              <div class="flex justify-end">
                <button class="btn-base h-10 px-4 rounded-md text-white" :style="{ backgroundColor: 'var(--color-primary)' }" :disabled="saving.social">
                  <span v-if="!saving.social">Enregistrer</span>
                  <span v-else>Sauvegarde...</span>
                </button>
              </div>
            </form>
          </section>

          <section class="border border-black/10 dark:border-white/10 rounded-lg p-4">
            <h2 class="font-semibold mb-3">Préférences</h2>
            <form @submit.prevent="saveSection('preferences')" class="grid gap-3">
              <div class="grid sm:grid-cols-3 gap-3">
                <div>
                  <label class="block text-sm mb-1">Langue</label>
                  <select v-model="local.preferences.language" class="input">
                    <option value="fr">Français</option>
                    <option value="en">English</option>
                  </select>
                </div>
                <div>
                  <label class="block text-sm mb-1">Devise</label>
                  <select v-model="local.preferences.currency" class="input">
                    <option value="XOF">XOF</option>
                    <option value="EUR">EUR</option>
                    <option value="USD">USD</option>
                  </select>
                </div>
                <label class="inline-flex items-center gap-2 mt-6">
                  <input type="checkbox" v-model="local.preferences.notifications_enabled" />
                  <span>Notifications</span>
                </label>
              </div>
              <div class="flex justify-end">
                <button class="btn-base h-10 px-4 rounded-md text-white" :style="{ backgroundColor: 'var(--color-primary)' }" :disabled="saving.preferences">
                  <span v-if="!saving.preferences">Enregistrer</span>
                  <span v-else>Sauvegarde...</span>
                </button>
              </div>
            </form>
          </section>

          <section v-if="isProvider" class="border border-black/10 dark:border-white/10 rounded-lg p-4">
            <h2 class="font-semibold mb-3">Profil professionnel</h2>
            <form @submit.prevent="saveSection('provider')" class="grid gap-3">
              <div class="grid sm:grid-cols-2 gap-3">
                <div>
                  <label class="block text-sm mb-1">Nom de l'entreprise</label>
                  <input v-model="local.business_name" class="input" />
                </div>
                <div>
                  <label class="block text-sm mb-1">Statut</label>
                  <input :value="auth.user?.provider_status" class="input" disabled />
                </div>
              </div>
              <div>
                <label class="block text-sm mb-1">Bio professionnelle</label>
                <textarea v-model="local.bio" rows="3" class="input"></textarea>
              </div>
              <div class="flex justify-end">
                <button class="btn-base h-10 px-4 rounded-md text-white" :style="{ backgroundColor: 'var(--color-primary)' }" :disabled="saving.provider">
                  <span v-if="!saving.provider">Enregistrer</span>
                  <span v-else>Sauvegarde...</span>
                </button>
              </div>
            </form>
          </section>

          <section class="border border-black/10 dark:border-white/10 rounded-lg p-4">
            <h2 class="font-semibold mb-3">Sécurité</h2>
            <div class="flex flex-wrap gap-2">
              <button @click="openChangePassword" class="btn-base h-10 px-4 rounded-md border border-black/10 dark:border-white/10">Changer le mot de passe</button>
              <button @click="openDelete" class="btn-base h-10 px-4 rounded-md bg-red-600 text-white">Supprimer mon compte</button>
            </div>
          </section>
        </div>
      </div>
    </div>

    <ChangePasswordModal :is-open="modals.password" @close="modals.password=false" @changed="onPasswordChanged" />
    <DeleteAccountModal :is-open="modals.delete" @close="modals.delete=false" @deleted="onAccountDeleted" />
  </div>
</template>
<script setup lang="ts">
import { reactive, watch, computed, onMounted } from 'vue'
import { useI18n } from 'vue-i18n'
import { useAuthStore } from '@/stores/auth'
import { fetchProfile, updateProfile } from '@/lib/services/profile'
import AvatarUpload from '@/components/profile/AvatarUpload.vue'
import CoverImageUpload from '@/components/profile/CoverImageUpload.vue'
import ChangePasswordModal from '@/components/profile/ChangePasswordModal.vue'
import DeleteAccountModal from '@/components/profile/DeleteAccountModal.vue'

const { t } = useI18n()
const auth = useAuthStore()

const local = reactive<any>({
  name: '', email: '', phone: '', date_of_birth: null, gender: null,
  about_me: '', country: '', city: '', address: '', postal_code: '',
  website_url: '', social_links: { facebook: '', instagram: '', twitter: '', linkedin: '' },
  preferences: { language: 'fr', currency: 'XOF', notifications_enabled: true },
  avatar_url: null, cover_image_url: null, business_name: '', bio: ''
})

const saving = reactive({ personal: false, location: false, social: false, preferences: false, provider: false })
const modals = reactive({ password: false, delete: false })

const isProvider = computed(() => auth.user?.provider_status === 'approved')

onMounted(async () => {
  if (!auth.user) return
  const prof = await fetchProfile()
  Object.assign(local, prof)
})

function onAvatarUploaded(url: string) {
  local.avatar_url = url
  if (auth.user) auth.user.avatar_url = url
}
function onCoverUploaded(url: string) {
  local.cover_image_url = url
}

async function saveSection(section: 'personal'|'location'|'social'|'preferences'|'provider') {
  saving[section] = true
  try {
    const payload: any = {}
    if (section === 'personal') Object.assign(payload, {
      name: local.name, email: local.email, phone: local.phone, date_of_birth: local.date_of_birth, gender: local.gender, about_me: local.about_me
    })
    if (section === 'location') Object.assign(payload, {
      country: local.country, city: local.city, address: local.address, postal_code: local.postal_code
    })
    if (section === 'social') Object.assign(payload, {
      website_url: local.website_url, social_links: local.social_links
    })
    if (section === 'preferences') Object.assign(payload, {
      preferences: local.preferences
    })
    if (section === 'provider') Object.assign(payload, {
      business_name: local.business_name, bio: local.bio
    })
    const updated = await updateProfile(payload)
    Object.assign(local, updated)
    if (auth.user) Object.assign(auth.user, updated)
  } finally { saving[section] = false }
}

function openChangePassword() { modals.password = true }
function openDelete() { modals.delete = true }
function onPasswordChanged() {}
function onAccountDeleted() { auth.logout() }
</script>
<style scoped>
.input { width: 100%; border: 1px solid color-mix(in oklab, var(--color-text) 10%, transparent); background: transparent; border-radius: .5rem; padding: .5rem .75rem }
</style>
