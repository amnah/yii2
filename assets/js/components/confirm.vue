
<template>
    <div v-if="error">
        <div class="container">
            <div class="row">
                <div class="col-md-8 col-md-offset-2">
                    <div class="panel panel-danger">
                        <div class="panel-heading">Confirm Email</div>
                        <div class="panel-body">
                            {{ error }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import {setPageTitle} from '../functions.js'
import {get, reset, process} from '../api.js'
export default {
    name: 'confirm',
    beforeCreate: function() {
        setPageTitle('Confirm Email')
        const vm = this
        get('auth/confirm', vm.$route.query).then(function(data) {
            process(vm, data)
            if (data.success) {
                vm.success = data.success
                vm.$store.dispatch('login', data)
                vm.$store.commit('statusMsg', 'Email confirmed')
                vm.$router.push('/')
            }
        });
    },
    data: function() {
        return {
            success: false,
            error: false,
        }
    }
}
</script>