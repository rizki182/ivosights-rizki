import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'

const routes = [
  {
    path: '/',
    name: 'home',
    component: HomeView
  },
  {
    path: '/task',
    name: 'task',
    component: () => import(/* webpackChunkName: "about" */ '../views/TaskView.vue')
  },
  {
    path: '/project',
    name: 'project',
    component: () => import(/* webpackChunkName: "about" */ '../views/ProjectView.vue')
  }
]

const router = createRouter({
  history: createWebHistory(process.env.BASE_URL),
  routes
})

export default router
