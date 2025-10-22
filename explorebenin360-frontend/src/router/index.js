import { createRouter, createWebHistory } from 'vue-router'

const Home = () => import('@/pages/Home.vue')
const Destinations = () => import('@/pages/Destinations.vue')
const Experiences = () => import('@/pages/Experiences.vue')
const Hebergements = () => import('@/pages/Hebergements.vue')
const Guides = () => import('@/pages/Guides.vue')
const Blog = () => import('@/pages/Blog.vue')
const Contact = () => import('@/pages/Contact.vue')
const Login = () => import('@/pages/Auth/Login.vue')
const Register = () => import('@/pages/Auth/Register.vue')
const Profile = () => import('@/pages/Profile.vue')
const Explorer = () => import('@/pages/Explorer.vue')

const routes = [
  { path: '/', name: 'home', component: Home },
  { path: '/destinations', name: 'destinations', component: Destinations },
  { path: '/experiences', name: 'experiences', component: Experiences },
  { path: '/hebergements', name: 'hebergements', component: Hebergements },
  { path: '/guides', name: 'guides', component: Guides },
  { path: '/blog', name: 'blog', component: Blog },
  { path: '/contact', name: 'contact', component: Contact },
  { path: '/login', name: 'login', component: Login },
  { path: '/register', name: 'register', component: Register },
  { path: '/profile', name: 'profile', component: Profile },
  { path: '/explorer', name: 'explorer', component: Explorer },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
  scrollBehavior() { return { top: 0 } },
})

export default router
