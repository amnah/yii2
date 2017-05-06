
import store from './store.js'
import router from './router.js'
import {getLocalStorage} from './functions.js'

// set AppConfig
store.commit('appConfig', window.AppConfig)
delete window.AppConfig

// set User from window
if (window.User) {
    store.commit('user', window.User)
    delete window.User
} else if (getLocalStorage('user')) {
    // set user from localStorage while we check the auth status asynchronously
    // (because localStorage data could be stale)
    store.commit('user', getLocalStorage('user'))
    store.dispatch('checkAuth')
}

// instantiate Vue
new Vue({
    el: '#app',
    store,
    router,
    render: function(createElement) {
        return createElement(require('./components/app.vue'))
    }
})