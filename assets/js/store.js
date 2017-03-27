
import {get, post} from './api.js'

// --------------------------------------------------------
// State
// --------------------------------------------------------
const state = {
    user: null,
    loginUrl: null,
    statusMsg: null,
}

// --------------------------------------------------------
// Getters
// --------------------------------------------------------
const getters = {
    //user: state => state.user,
}

// --------------------------------------------------------
// Mutations
// --------------------------------------------------------
const mutations = {
    user(state, user) {
        state.user = user
        localStorage.setItem('user', JSON.stringify(user))
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
        localStorage.removeItem('user')
        return post('auth/logout')
    },
    checkAuth(store) {
        // attempt to restore user from localStorage
        // note: we commit the user here to make it appear instantaneous
        try {
            store.commit('user', JSON.parse(localStorage.getItem('user')))
        } catch(e) {}
        if (!store.state.user) {
            return
        }

        // call backend to get fresh data
        get('auth/check-auth').then(function(data) {
            if (data.success) {
                store.commit('user', data.user)
            } else {
                store.commit('user', null)
            }
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