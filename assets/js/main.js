
/**
 * Create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

new Vue({
    el: '#app',
    components: {
        example: require('./components/example.vue')
    },
    methods: {
        refresh() {
            $.ajax('/site/captcha?refresh=1&_=' + new Date().getTime()).then(function(data) {
                $('#contactform-verifycode-image').attr('src', data.url)
            })
        }
    }
})