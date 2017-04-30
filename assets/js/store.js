
import {get, post} from './api.js'

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
    updateAppConfig(state, data) {
        state.appConfig = Object.assign({}, state.appConfig, data)
    },
    user(state, user) {
        state.user = user
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
        store.commit('user', null)
        return post('auth/logout')
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