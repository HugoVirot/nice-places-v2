import './bootstrap';
import axios from 'axios'

// on importe Bootstrap
import "bootstrap/dist/css/bootstrap.min.css"
import "bootstrap"

// On importe createApp
import { createApp } from "vue"

// On importe le composant principal App
import App from "./App.vue"

// On importe le routeur
import router from './router'

// on importe pinia et le plugin de persistance du state
import { createPinia } from 'pinia'
import piniaPluginPersistedstate from 'pinia-plugin-persistedstate'
import { useUserStore } from './stores/userStore'

// on les initialise
const pinia = createPinia()
pinia.use(piniaPluginPersistedstate)

const app = createApp(App).use(router).use(pinia)
const userStore = useUserStore()

axios.interceptors.response.use(
    response => response,
    error => {
        const requestUrl = error.config?.url ?? ""
        if (error.response?.status === 401 && !requestUrl.includes("/api/login")) {
            userStore.clearAuth()
        }
        return Promise.reject(error)
    }
)

const validatePersistedSession = async () => {
    if (!userStore.userLoggedIn) return

    try {
        const response = await axios.get("/api/user")
        userStore.storeUserData(response.data)
    } catch (error) {
        if (error.response?.status !== 401) {
            console.error("Impossible de vérifier la session utilisateur.", error)
        }
    }
}

validatePersistedSession().finally(() => app.mount("#app"))


// Expose a store to window during testing
//import { useUserStore } from './stores/userStore';
// if (window.Cypress && process.env.NODE_ENV === 'development') { 
//   const userStore = useUserStore() 
//   window.userStore = userStore
// }

