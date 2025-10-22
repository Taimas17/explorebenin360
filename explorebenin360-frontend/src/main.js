import { createApp } from 'vue'
import App from './App.vue'
import './styles/globals.css'
import router from './router'
import i18n from './i18n'
import { createHead } from '@vueuse/head'

const app = createApp(App)
app.use(router)
app.use(i18n)
app.use(createHead())
app.mount('#app')
