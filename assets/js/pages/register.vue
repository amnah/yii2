
<template>
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">Register</div>
                    <div class="panel-body">

                        <div class="alert alert-success" v-if="success">
                            <p>User <strong>{{ form.email }}</strong> registered - Please check your email to confirm your address.</p>
                        </div>

                        <div v-if="!success">
                            <form id="register-form" class="form-horizontal" role="form" @submit.prevent="submit">
                                <div class="form-group" :class="{'has-error': errors.email}">
                                    <label class="col-md-4 control-label" for="dynamicmodel-email">Email</label>
                                    <div class="col-md-6">
                                        <input type="email" id="dynamicmodel-email" class="form-control" autofocus required v-model.trim="form.email">
                                        <span class="help-block" v-if="errors.email"><strong>{{ errors.email[0] }}</strong></span>
                                    </div>
                                </div>

                                <div class="form-group" :class="{'has-error': errors.username}">
                                    <label class="col-md-4 control-label" for="dynamicmodel-username">Username</label>
                                    <div class="col-md-6">
                                        <input type="text" id="dynamicmodel-username" class="form-control" required v-model.trim="form.username">
                                        <span class="help-block" v-if="errors.username"><strong>{{ errors.username[0] }}</strong></span>
                                    </div>
                                </div>

                                <div class="form-group" :class="{'has-error': errors.password}">
                                    <label class="col-md-4 control-label" for="dynamicmodel-password">Password</label>
                                    <div class="col-md-6">
                                        <input type="password" id="dynamicmodel-password" class="form-control" required v-model.trim="form.password">
                                        <span class="help-block" v-if="errors.password"><strong>{{ errors.password[0] }}</strong></span>
                                    </div>
                                </div>

                                <div class="form-group" :class="{'has-error': errors.confirm_password}">
                                    <label class="col-md-4 control-label" for="dynamicmodel-confirm_password">Confirm Password</label>
                                    <div class="col-md-6">
                                        <input type="password" id="dynamicmodel-confirm_password" class="form-control" required v-model.trim="form.confirm_password">
                                        <span class="help-block" v-if="errors.password"><strong>{{ errors.password[0] }}</strong></span>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="col-md-8 col-md-offset-4">
                                        <button type="submit" class="btn btn-primary" :disabled="submitting">Register</button>
                                        <router-link class="btn btn-link" to="/login">Login</router-link>
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
import {post, reset, process} from '../api.js'
export default {
    name: 'register',
    created: function() {
        setPageTitle('Register')
    },
    data: function() {
        return {
            success: false,
            submitting: false,
            errors: {},
            form: {
                email: '',
                username: '',
                password: '',
                confirm_password: ''
            }
        }
    },
    methods: {
        submit: function(e) {
            const vm = this
            reset(vm)
            post('auth/register', {User: vm.form}).then(function(data) {
                process(vm, data)
                if (data.success) {
                    vm.success = true
                }
            });
        }
    }
}
</script>