
import store from './store.js'
import router from './router.js'
import {setConfig} from './functions.js'

// set AppConfig
// (first we need to transfer AppConfig.user to the store)
const appConfig = window.AppConfig
delete window.AppConfig
if (appConfig.user) {
    store.commit('user', appConfig.user)
    delete appConfig.user
}
setConfig(appConfig)

new Vue({
    el: '#app',
    store,
    router,
    components: {
        navbar: require('./components/navbar.vue')
    }
})
