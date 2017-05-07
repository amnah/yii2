
import {get, post} from './api.js'
import {setLocalStorage} from './functions.js'

// --------------------------------------------------------
// State
// --------------------------------------------------------
const state = {
    appConfig: {},
    user: null,
    loginUrl: null,
    statusMsg: null,
}

// --------------------------------------------------------
// Getters
// --------------------------------------------------------
const getters = {
    appConfig: function(state) {
        return function(key, defaultValue = null) {
            return (key in state.appConfig) ? state.appConfig[key] : defaultValue
        }
    }
}

// --------------------------------------------------------
// Mutations
// --------------------------------------------------------
const mutations = {
    appConfig(state, appConfig) {
        state.appConfig = appConfig
    },
    appConfigToken(state, token) {
        state.appConfig.token = token
        setLocalStorage('token', token)
    },
    user(state, user) {
        state.user = user
        setLocalStorage('user', user)
    },
    loginUrl(state, loginUrl) {
        state.loginUrl = loginUrl
    },
    statusMsg(state, statusMsg) {
        state.statusMsg = statusMsg
    }
}

// --------------------------------------------------------
// Actions
// --------------------------------------------------------
const actions = {
    logout(store) {
        // make post request first before clearing out store variables
        // note that we don't wait until it succeeds. logout should be instantaneous
        const url = store.state.appConfig.csrf ? 'auth/logout' : 'auth/logout-api'
        post(url)
        store.commit('appConfigToken', null)
        store.commit('user', null)
    },
    checkAuth(store) {
        return get('auth/check-auth').then(function(data) {
            const user = data.user || null
            store.commit('user', user)
        })
    },
}

// --------------------------------------------------------
// Vuex instance
// --------------------------------------------------------
export default new Vuex.Store({
    state,
    getters,
    mutations,
    actions,
})