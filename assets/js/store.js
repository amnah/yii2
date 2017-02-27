
import {get, post} from './api.js'

// --------------------------------------------------------
// Root state
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
    user: state => state.user,
    loginUrl: state => state.loginUrl,
    statusMsg: state => state.statusMsg,
}

// --------------------------------------------------------
// Mutations
// --------------------------------------------------------
const mutations = {
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
    login(state, data) {
        doLogin(state, data)
    },
    logout(state) {
        doLogout(state)
    },
    checkAuth(state) {
        if (!state.getters.user) {
            return
        }
        get('auth/check-auth').then(function(data) {
            if (data.success) {
                doLogin(state, data)
            } else {
                doLogout(state)
            }
        });
    },
    restoreFromStorage(state) {
        try {
            state.commit('user', JSON.parse(localStorage.getItem('user')))
        } catch(e) {}
    }
}

// --------------------------------------------------------
// Helper functions for actions
// --------------------------------------------------------
function doLogin(state, data) {
    state.commit('user', data.user)
    localStorage.setItem('user', JSON.stringify(data.user))
}

function doLogout(state) {
    post('auth/logout')
    state.commit('user', null)
    localStorage.removeItem('user')
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