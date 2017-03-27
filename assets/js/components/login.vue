
<template>
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">Login</div>
                    <div class="panel-body">

                        <div class="alert alert-info" v-if="loginUrl">
                            After logging in, you will be redirected to <strong>{{ loginUrl }}</strong>
                        </div>

                        <form id="login-form" class="form-horizontal" role="form" @submit.prevent="submit">
                            <div class="form-group" :class="{'has-error': errors.email}">
                                <label class="col-md-4 control-label" for="dynamicmodel-email">Email</label>
                                <div class="col-md-6">
                                    <input type="text" id="dynamicmodel-email" class="form-control" autofocus required v-model.trim="form.email">
                                    <span class="help-block" v-if="errors.email"><strong>{{ errors.email[0] }}</strong></span>
                                </div>
                            </div>

                            <div class="form-group" :class="{'has-error': errors.password}">
                                <label class="col-md-4 control-label" for="dynamicmodel-password">Password</label>
                                <div class="col-md-6">
                                    <input type="password" id="dynamicmodel-password" class="form-control" required v-model.trim="form.password">
                                    <span class="help-block" v-if="errors.password"><strong>{{ errors.password[0] }}</strong></span>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-md-6 col-md-offset-4">
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" id="dynamicmodel-rememberme" v-model="form.rememberMe" v-bind:true-value="1" v-bind:false-value="0">
                                            Remember Me
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-md-8 col-md-offset-4">
                                    <button type="submit" class="btn btn-primary" :disabled="submitting">Login</button>
                                    <router-link class="btn btn-link" to="/forgot">Forgot Your Password?</router-link>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import {setPageTitle} from '../functions.js'
import {post, reset, process} from '../api.js'
export default {
    name: 'login',
    created: function() {
        setPageTitle('Login')
    },
    data: function() {
        return {
            success: false,
            submitting: false,
            errors: {},
            loginUrl: this.$store.state.loginUrl,
            form: {
                email: '',
                password: '',
                rememberMe: 1
            }
        }
    },
    methods: {
        submit: function(e) {
            const vm = this
            reset(vm)
            post('auth/login', {DynamicModel: vm.form}).then(function(data) {
                process(vm, data)
                if (data.success) {
                    vm.$store.commit('user', data.user)
                    if (vm.$store.state.loginUrl) {
                        vm.$store.commit('loginUrl', null)
                    }
                    vm.$router.push(vm.loginUrl || '/')
                }
            });
        }
    }
}
</script>