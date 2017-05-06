
<template>
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">Profile</div>
                    <div class="panel-body">

                        <div class="alert alert-success" v-if="success">
                            <p>Profile updated</p>
                        </div>

                        <form id="register-form" class="form-horizontal" role="form" @submit.prevent="submit">
                            <div class="form-group" :class="{'has-error': errors.username}">
                                <label class="col-md-4 control-label" for="user-username">Username</label>
                                <div class="col-md-6">
                                    <input type="text" id="user-username" class="form-control" required v-model.trim="form.username">
                                    <span class="help-block" v-if="errors.username"><strong>{{ errors.username[0] }}</strong></span>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-md-8 col-md-offset-4">
                                    <button type="submit" class="btn btn-primary" :disabled="submitting">Update</button>
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
    name: 'profile',
    created: function() {
        setPageTitle('Profile')
    },
    data: function() {
        // check if user is logged in
        if (!this.$store.state.user) {
            this.$router.push('/')
        }
        return {
            success: false,
            submitting: false,
            errors: {},
            form: {
                username: this.$store.state.user.username,
            }
        }
    },
    methods: {
        submit: function(e) {
            const vm = this
            reset(vm)
            post('user/profile', {User: vm.form}).then(function(data) {
                process(vm, data)
                if (data.success) {
                    vm.success = true
                    vm.$store.commit('user', data.user)
                }
            });
        }
    }
}
</script>