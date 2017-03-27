
<template>
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">Contact</div>

                    <div class="panel-body">

                        <div v-if="success">
                            <div class="alert alert-success">
                                Thank you for contacting us <strong>[ {{ form.name }} ]</strong>. We will respond to you as soon as possible.
                            </div>

                            <p>
                                Note that if you turn on the Yii debugger, you should be able
                                to view the mail message on the mail panel of the debugger.
                            </p>

                            <p>
                                If the application is in development mode, the email is not sent but saved as
                                a file under <code>Yii::$app->mailer->fileTransportPath</code>.
                                Please configure the <code>useFileTransport</code> property of the <code>mail</code>
                                application component to be false to enable email sending.
                            </p>

                            <hr/>
                        </div>

                        <div v-if="!success">
                            <p>If you have business inquiries or other questions, please fill out the following form to contact us. Thank you.</p>

                            <div class="row">
                                <div class="col-md-8">
                                    <form id="contact-form" role="form" @submit.prevent="submit">

                                        <div class="form-group" :class="{'has-error': errors.name}">
                                            <label class="control-label" for="dynamicmodel-name">Name</label>
                                            <input type="text" id="dynamicmodel-name" class="form-control" autofocus required v-model.trim="form.name">
                                            <p class="help-block help-block-error" v-if="errors.name"><strong>{{ errors.name[0] }}</strong></p>
                                        </div>
                                        <div class="form-group" :class="{'has-error': errors.email}">
                                            <label class="control-label" for="dynamicmodel-email">Email</label>
                                            <input type="text" id="dynamicmodel-email" class="form-control" email required v-model.trim="form.email">
                                            <p class="help-block help-block-error" v-if="errors.email"><strong>{{ errors.email[0] }}</strong></p>
                                        </div>
                                        <div class="form-group" :class="{'has-error': errors.subject}">
                                            <label class="control-label" for="dynamicmodel-subject">Subject</label>
                                            <input type="text" id="dynamicmodel-subject" class="form-control" required v-model.trim="form.subject">
                                            <p class="help-block help-block-error" v-if="errors.subject"><strong>{{ errors.subject[0] }}</strong></p>
                                        </div>
                                        <div class="form-group" :class="{'has-error': errors.body}">
                                            <label class="control-label" for="dynamicmodel-body">Body</label>
                                            <textarea id="dynamicmodel-body" class="form-control" rows="6" required v-model.trim="form.body"></textarea>
                                            <p class="help-block help-block-error" v-if="errors.body"><strong>{{ errors.body[0] }}</strong></p>
                                        </div>
                                        <div class="form-group" :class="{'has-error': errors.verificationCode}">
                                            <label class="control-label" for="dynamicmodel-verificationcode">Verification Code</label>
                                            <div class="row">
                                                <div class="col-lg-3" @click="refreshCaptcha()">
                                                    <img id="dynamicmodel-verificationcode-image" :src="verificationCodeUrl" alt="">
                                                </div>
                                                <div class="col-lg-6">
                                                    <input type="text" id="dynamicmodel-verificationcode" class="form-control" required v-model.trim="form.verificationCode">
                                                </div>
                                            </div>
                                            <p class="help-block help-block-error" v-if="errors.verificationCode"><strong>{{ errors.verificationCode[0] }}</strong></p>
                                        </div>
                                        <div class="form-group">
                                            <button type="submit" class="btn btn-primary" :disabled="submitting">Submit</button>
                                        </div>

                                    </form>
                                </div>
                            </div>
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
    name: 'contact',
    created: function() {
        setPageTitle('Contact')
        this.refreshCaptcha()
    },
    data: function() {
        const user = this.$store.state.user
        return {
            success: false,
            submitting: false,
            errors: {},
            numSubmitted: 0,
            verificationCodeUrl: '',
            form: {
                name: user ? user.username : '',
                email: user ? user.email : '',
                subject: '',
                body: '',
                verificationCode: '',
            },
        }
    },
    methods: {
        submit: function(e) {
            const vm = this
            reset(vm)
            vm.numSubmitted++
            post('public/contact', {DynamicModel: vm.form}).then(function(data) {
                process(vm, data)

                // refresh captcha if user failed three times
                if (vm.numSubmitted == 3) {
                    vm.refreshCaptcha()
                    vm.numSubmitted = 0
                }
            });
        },
        refreshCaptcha: function() {
            const vm = this
            get('public/captcha?refresh=1&_=' + new Date().getTime()).then(function(data) {
                vm.verificationCodeUrl = data.url
            })
        }
    }
}
</script>