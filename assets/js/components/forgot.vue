
<template>
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">Forgot Password</div>
                    <div class="panel-body">

                        <div class="alert alert-success" v-if="success">
                            We have e-mailed your password reset link to <strong>{{ form.email }}</strong>
                        </div>

                        <div v-if="!success">
                            <form id="forgot-form" class="form-horizontal" role="form" @submit.prevent="submit">
                                <div class="form-group" :class="{'has-error': errors.email}">
                                    <label class="col-md-4 control-label" for="dynamicmodel-email">Email</label>
                                    <div class="col-md-6">
                                        <input type="email" id="dynamicmodel-email" class="form-control" autofocus required v-model.trim="form.email">
                                        <span class="help-block" v-if="errors.email"><strong>{{ errors.email[0] }}</strong></span>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="col-md-8 col-md-offset-4">
                                        <button type="submit" class="btn btn-primary" :disabled="submitting">Send Password Reset Link</button>
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
    name: 'forgot',
    created: function() {
        setPageTitle('Forgot Password')
    },
    data: function() {
        return {
            success: false,
            submitting: false,
            errors: {},
            form: {
                email: ''
            }
        }
    },
    methods: {
        submit: function(e) {
            const vm = this
            reset(vm)
            post('auth/forgot', {DynamicModel: vm.form}).then(function(data) {
                process(vm, data)
            });
        }
    }
}
</script>