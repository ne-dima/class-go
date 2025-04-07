import { createRouter, createWebHistory } from 'vue-router'

// Основные страницы
import Dashboard from '../views/Dashboard.vue'
import Courses from '../views/Courses.vue'
import Assignments from '../views/Assignments.vue'
import Calendar from '../views/Calendar.vue'
import CourseDetails from '../views/CourseDetails.vue'
import Chats from '../views/Chats.vue'
import Contacts from '../views/Contacts.vue'
import Settings from '../views/Settings.vue'

// Страницы авторизации
import Login from '../views/Login.vue'
import Register from '../views/Register.vue'

// Страница 404
import NotFound from '../views/NotFound.vue'

const routes = [
  { path: '/', name: 'Dashboard', component: Dashboard },
  { path: '/courses', name: 'Courses', component: Courses },
  { path: '/assignments', name: 'Assignments', component: Assignments },
  { path: '/calendar', name: 'Calendar', component: Calendar },
  { path: '/courses/:id', name: 'CourseDetails', component: CourseDetails, props: true },
  { path: '/chats', name: 'Chats', component: Chats }, 
  { path: '/login', name: 'Login', component: Login },
  { path: '/register', name: 'Register', component: Register },
  { path: '/:pathMatch(.*)*', name: 'NotFound', component: NotFound },
  { path: '/contacts', name: 'Contacts', component: Contacts },
  { path: '/settings', name: 'Settings', component: Settings }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

export default router
