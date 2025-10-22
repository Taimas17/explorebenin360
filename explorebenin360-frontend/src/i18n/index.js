import { createI18n } from 'vue-i18n'
import fr from './fr.json'
import en from './en.json'

const stored = localStorage.getItem('eb360:locale')
const locale = stored || (navigator.language?.startsWith('fr') ? 'fr' : 'en')

const i18n = createI18n({
  legacy: false,
  locale,
  fallbackLocale: 'en',
  messages: { fr, en },
})

export default i18n
