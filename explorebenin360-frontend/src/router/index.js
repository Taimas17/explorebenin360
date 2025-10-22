import { createRouter, createWebHistory } from 'vue-router'

const Home = () => import('@/views/Home.vue')
const Destinations = () => import('@/views/Destinations.vue')
const Hebergements = () => import('@/views/Hebergements.vue')
const Guides = () => import('@/views/Guides.vue')
const Blog = () => import('@/views/Blog.vue')

export const router = createRouter({
  history: createWebHistory(),
  routes: [
    { path: '/', name: 'home', component: Home },
    { path: '/destinations', name: 'destinations', component: Destinations },
    { path: '/hebergements', name: 'hebergements', component: Hebergements },
    { path: '/guides', name: 'guides', component: Guides },
    { path: '/blog', name: 'blog', component: Blog },
  ],
  scrollBehavior() { return { top: 0 } },
})
