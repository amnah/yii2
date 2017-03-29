
<template>
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel" :class="error ? 'panel-danger' : 'panel-default'">
                    <div class="panel-heading">Reset</div>
                    <div class="panel-body">

                        <p v-if="error">{{ error }}</p>

                        <div v-if="form.email">
                            <form id="reset-form" class="form-horizontal" role="form" @submit.prevent="submit">
                                <div class="form-group">
                                    <label class="col-md-4 control-label" for="user-email">Email</label>
                                    <div class="col-md-6">
                                        <input type="email" id="user-email" class="form-control" disabled v-model.trim="form.email">
                                    </div>
                                </div>

                                <div class="form-group" :class="{'has-error': errors.password}">
                                    <label class="col-md-4 control-label" for="user-password">Password</label>
                                    <div class="col-md-6">
                                        <input type="password" id="user-password" class="form-control" autofocus required v-model.trim="form.password">
                                        <span class="help-block" v-if="errors.password"><strong>{{ errors.password[0] }}</strong></span>
                                    </div>
                                </div>

                                <div class="form-group" :class="{'has-error': errors.confirm_password}">
                                    <label class="col-md-4 control-label" for="user-confirm_password">Confirm Password</label>
                                    <div class="col-md-6">
                                        <input type="password" id="user-confirm_password" class="form-control" required v-model.trim="form.confirm_password">
                                        <span class="help-block" v-if="errors.confirm_password"><strong>{{ errors.confirm_password[0] }}</strong></span>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="col-md-8 col-md-offset-4">
                                        <button type="submit" class="btn btn-primary" :disabled="submitting">Reset password</button>
                                    </div>
                                </div>

                            </form>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import {setPageTitle} from '../functions.js'
import {get, post, reset, process} from '../api.js'
export default {
    name: 'reset',
    created: function() {
        setPageTitle('Reset Password')
        const vm = this
        get(vm.formUrl).then(function(data) {
            if (data.error) {
                vm.error = data.error
            } else if (data.user) {
                vm.form = data.user
            }
        });
    },
    data: function() {
        return {
            submitting: false,
            errors: {},
            error: null,
            formUrl: `auth/reset?token=${this.$route.query.token}`,
            form: {
                email: '',
                password: '',
                confirm_password: ''
            }
        }
    },
    methods: {
        submit: function(e) {
            const vm = this
            reset(vm)
            post(vm.formUrl, {User: vm.form}).then(function(data) {
                process(vm, data)
                if (data.success) {
                    vm.$store.commit('user', data.user)
                    vm.$store.commit('statusMsg', 'Password has been reset')
                    vm.$router.push('/')
                }
            });
        }
    }
}
</script>