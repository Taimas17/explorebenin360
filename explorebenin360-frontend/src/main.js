import { createApp } from 'vue'
import App from './App.vue'
import './styles/globals.css'
import router from './router'
import i18n from './i18n'
import { createHead } from '@vueuse/head'
import { createPinia } from 'pinia'
import { useAuthStore } from './stores/auth'
import { useFavoritesStore } from './stores/favorites'
import { vClickOutside } from './directives/clickOutside'

const app = createApp(App)
const pinia = createPinia()
app.use(pinia)
app.use(router)
app.use(i18n)
app.use(createHead())
app.directive('click-outside', vClickOutside)

const authStore = useAuthStore()
const favoritesStore = useFavoritesStore()

authStore.init()
favoritesStore.init()

if (authStore.isAuthenticated) {
  favoritesStore.syncOnLogin().catch(console.error)
}

app.mount('#app')
