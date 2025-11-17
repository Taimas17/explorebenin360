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

// Traveler
const TravelerReservations = () => import('@/pages/dashboard/Reservations.vue')
const TravelerReservationDetail = () => import('@/pages/dashboard/ReservationDetail.vue')
const TravelerFavorites = () => import('@/pages/dashboard/Favorites.vue')
const TravelerMessages = () => import('@/pages/dashboard/Messages.vue')
const TravelerMyReviews = () => import('@/pages/dashboard/MyReviews.vue')

// Provider
const ProviderDashboard = () => import('@/pages/provider/ProviderDashboard.vue')
const ProviderReservations = () => import('@/pages/provider/Reservations.vue')
const ProviderOffers = () => import('@/pages/provider/Offers.vue')
const ProviderOfferCreate = () => import('@/pages/provider/OfferCreate.vue')
const ProviderOfferEdit = () => import('@/pages/provider/OfferEdit.vue')
const ProviderCalendar = () => import('@/pages/provider/Calendar.vue')
const ProviderEarnings = () => import('@/pages/provider/Earnings.vue')

// Admin
const AdminDashboard = () => import('@/pages/admin/AdminDashboard.vue')
const AdminReservations = () => import('@/pages/admin/Reservations.vue')
const AdminProviders = () => import('@/pages/admin/Providers.vue')
const AdminModeration = () => import('@/pages/admin/ContentModeration.vue')

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
  { path: '/checkout/:slug', name: 'checkout', component: Checkout, meta: { requiresAuth: true, roles: ['traveler','provider','admin'] } },
  { path: '/checkout/callback', name: 'checkout-callback', component: Callback },

  // Traveler
  { path: '/dashboard/reservations', name: 'reservations', component: TravelerReservations, meta: { requiresAuth: true, roles: ['traveler'] } },
  { path: '/dashboard/reservations/:id', name: 'reservation-detail', component: TravelerReservationDetail, meta: { requiresAuth: true, roles: ['traveler'] } },
  { path: '/dashboard/favorites', name: 'favorites', component: TravelerFavorites, meta: { requiresAuth: true, roles: ['traveler'] } },
  { path: '/dashboard/messages', name: 'messages', component: TravelerMessages, meta: { requiresAuth: true, roles: ['traveler'] } },
  { path: '/dashboard/reviews', name: 'my-reviews', component: TravelerMyReviews, meta: { requiresAuth: true, roles: ['traveler'] } },

  // Provider
  { path: '/provider', name: 'provider-dashboard', component: ProviderDashboard, meta: { requiresAuth: true, roles: ['provider'] } },
  { path: '/provider/reservations', name: 'provider-reservations', component: ProviderReservations, meta: { requiresAuth: true, roles: ['provider'] } },
  { path: '/provider/offers', name: 'provider-offers', component: ProviderOffers, meta: { requiresAuth: true, roles: ['provider'] } },
  { path: '/provider/offers/new', name: 'provider-offer-create', component: ProviderOfferCreate, meta: { requiresAuth: true, roles: ['provider'] } },
  { path: '/provider/offers/:id', name: 'provider-offer-edit', component: ProviderOfferEdit, meta: { requiresAuth: true, roles: ['provider'] } },
  { path: '/provider/calendar', name: 'provider-calendar', component: ProviderCalendar, meta: { requiresAuth: true, roles: ['provider'] } },
  { path: '/provider/earnings', name: 'provider-earnings', component: ProviderEarnings, meta: { requiresAuth: true, roles: ['provider'] } },

  // Admin
  { path: '/admin', name: 'admin-dashboard', component: AdminDashboard, meta: { requiresAuth: true, roles: ['admin'] } },
  { path: '/admin/reservations', name: 'admin-reservations', component: AdminReservations, meta: { requiresAuth: true, roles: ['admin'] } },
  { path: '/admin/providers', name: 'admin-providers', component: AdminProviders, meta: { requiresAuth: true, roles: ['admin'] } },
  { path: '/admin/moderation', name: 'admin-moderation', component: AdminModeration, meta: { requiresAuth: true, roles: ['admin'] } },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
  scrollBehavior() { return { top: 0 } },
})

router.beforeEach((to) => {
  const auth = useAuthStore()
  if (to.meta?.requiresAuth && !auth.isAuthenticated) {
    return { name: 'login', query: { redirect: to.fullPath, reason: 'login_required' } }
  }
  const roles = to.meta?.roles as string[] | undefined
  if (roles && roles.length) {
    const hasRole = roles.some((r) => auth.hasRole(r))
    if (!hasRole) {
      if (auth.hasRole('admin')) return { name: 'admin-dashboard', query: { guard: 'access_denied' } }
      if (auth.hasRole('provider')) return { name: 'provider-dashboard', query: { guard: 'access_denied' } }
      if (auth.hasRole('traveler')) return { name: 'reservations', query: { guard: 'access_denied' } }
      return { name: 'home', query: { guard: 'access_denied' } }
    }
  }
})

export default router
