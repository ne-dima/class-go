import { createApp } from 'vue'
import App from './App.vue'
import router from './router'
import toastPlugin from './plugins/toast'
import '@fortawesome/fontawesome-free/css/all.min.css'
import './assets/fonts.css'


const app = createApp(App)
app.use(router)
app.use(toastPlugin)
app.mount('#app')
