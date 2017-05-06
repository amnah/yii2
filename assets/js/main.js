
import store from './store.js'
import router from './router.js'

// set AppConfig
// (first we need to transfer AppConfig.user to the store)
const appConfig = window.AppConfig
delete window.AppConfig
if (appConfig.user) {
    store.commit('user', appConfig.user)
    delete appConfig.user
}
store.commit('appConfig', appConfig)

new Vue({
    el: '#app',
    store,
    router,
    render: function(createElement) {
        return createElement(require('./components/app.vue'))
    }
})