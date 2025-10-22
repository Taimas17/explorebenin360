import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const Home = () => import('@/pages/Home.vue')
const Destinations = () => import('@/pages/Destinations.vue')
const DestinationDetail = () => import('@/pages/DestinationDetail.vue')
const Experiences = () => import('@/pages/Experiences.vue')
const Hebergements = () => import('@/pages/Hebergements.vue')
const HebergementDetail = () => import('@/pages/HebergementDetail.vue')
const Guides = () => import('@/pages/Guides.vue')
const Blog = () => import('@/pages/Blog.vue')
const ArticleDetail = () => import('@/pages/ArticleDetail.vue')
const Agenda = () => import('@/pages/Agenda.vue')
const EventDetail = () => import('@/pages/EventDetail.vue')
const Contact = () => import('@/pages/Contact.vue')
const Login = () => import('@/pages/Auth/Login.vue')
const Register = () => import('@/pages/Auth/Register.vue')
const Profile = () => import('@/pages/Profile.vue')
const Explorer = () => import('@/pages/Explorer.vue')

const Offerings = () => import('@/pages/offerings/Offerings.vue')
const OfferingDetail = () => import('@/pages/offerings/OfferingDetail.vue')
const Checkout = () => import('@/pages/checkout/Checkout.vue')
const Callback = () => import('@/pages/checkout/Callback.vue')

const TravelerReservations = () => import('@/pages/dashboard/Reservations.vue')
const TravelerReservationDetail = () => import('@/pages/dashboard/ReservationDetail.vue')
const ProviderReservations = () => import('@/pages/provider/Reservations.vue')
const AdminReservations = () => import('@/pages/admin/Reservations.vue')

const routes = [
  { path: '/', name: 'home', component: Home },
  { path: '/destinations', name: 'destinations', component: Destinations },
  { path: '/destinations/:slug', name: 'destination-detail', component: DestinationDetail },
  { path: '/experiences', name: 'experiences', component: Experiences },
  { path: '/hebergements', name: 'hebergements', component: Hebergements },
  { path: '/hebergements/:slug', name: 'hebergement-detail', component: HebergementDetail },
  { path: '/guides', name: 'guides', component: Guides },
  { path: '/blog', name: 'blog', component: Blog },
  { path: '/blog/:slug', name: 'article-detail', component: ArticleDetail },
  { path: '/agenda', name: 'agenda', component: Agenda },
  { path: '/agenda/:slug', name: 'event-detail', component: EventDetail },
  { path: '/contact', name: 'contact', component: Contact },
  { path: '/login', name: 'login', component: Login },
  { path: '/register', name: 'register', component: Register },
  { path: '/profile', name: 'profile', component: Profile },
  { path: '/explorer', name: 'explorer', component: Explorer },

  { path: '/offerings', name: 'offerings', component: Offerings },
  { path: '/offerings/:slug', name: 'offering-detail', component: OfferingDetail },
  { path: '/checkout/:slug', name: 'checkout', component: Checkout, meta: { requiresAuth: true } },
  { path: '/checkout/callback', name: 'checkout-callback', component: Callback },

  { path: '/dashboard/reservations', name: 'reservations', component: TravelerReservations, meta: { requiresAuth: true } },
  { path: '/dashboard/reservations/:id', name: 'reservation-detail', component: TravelerReservationDetail, meta: { requiresAuth: true } },
  { path: '/provider/reservations', name: 'provider-reservations', component: ProviderReservations, meta: { requiresAuth: true } },
  { path: '/admin/reservations', name: 'admin-reservations', component: AdminReservations, meta: { requiresAuth: true } },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
  scrollBehavior() { return { top: 0 } },
})

router.beforeEach((to) => {
  const auth = useAuthStore()
  if (to.meta?.requiresAuth && !auth.isAuthenticated) {
    return { name: 'login', query: { redirect: to.fullPath } }
  }
})

export default router
