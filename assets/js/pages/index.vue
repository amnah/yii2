
<template>
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">Welcome!</div>

                    <div class="panel-body">

                        <div class="alert alert-success" v-if="statusMsg">
                            {{ statusMsg }}
                        </div>

                        <p v-if="user">Logged in as <strong>{{ user.email }}</strong></p>
                        <p v-if="!user"><router-link to="/login">Login</router-link></p>
                        <p v-if="!user"><router-link to="/forgot">Forgot</router-link></p>
                        <p v-if="!user"><router-link to="/register">Register</router-link></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import {setPageTitle} from '../functions.js'
export default {
    name: 'index',
    created: function() {
        setPageTitle()
    },
    computed: Vuex.mapState([
        'user',
        'statusMsg',
    ]),
    destroyed: function() {
        // check for statusMsg before clearing it out
        // (this prevents an unnecessary state update)
        if (this.$store.state.statusMsg) {
            this.$store.commit('statusMsg', null)
        }
    }
}
</script>