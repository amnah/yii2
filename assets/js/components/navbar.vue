
<template>
    <nav class="navbar navbar-default navbar-static-top">
        <div class="container">
            <div class="navbar-header">

                <!-- Collapsed Hamburger -->
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse">
                    <span class="sr-only">Toggle Navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>

                <!-- Branding Image -->
                <router-link class="navbar-brand" to="/">My Company</router-link>
            </div>

            <div id="navbar-collapse" class="collapse navbar-collapse">
                <!-- Left Side Of Navbar -->
                <ul class="nav navbar-nav">
                    &nbsp;
                </ul>

                <!-- Right Side Of Navbar -->
                <ul class="nav navbar-nav navbar-right">
                    <router-link tag="li" to="/about" @click.native="collapse"><a>About</a></router-link>
                    <router-link tag="li" to="/contact" @click.native="collapse"><a>Contact</a></router-link>

                    <router-link v-if="!user" tag="li" to="/login" @click.native="collapse"><a>Login</a></router-link>
                    <router-link v-if="!user" tag="li" to="/register" @click.native="collapse"><a>Register</a></router-link>

                    <router-link v-if="user" tag="li" to="/profile" @click.native="collapse"><a>Profile</a></router-link>
                    <router-link v-if="user" tag="li" to="/logout" @click.native="logout"><a>Logout ({{ user.username }})</a></router-link>
                </ul>
            </div>
        </div>
    </nav>
</template>

<script>
export default {
    name: 'navbar',
    computed: Vuex.mapState([
        'user',
    ]),
    methods: {
        logout: function(e) {
            this.$store.dispatch('logout')
            this.$router.push('/')
        },
        collapse: function(e) {
            const $navbar = $('#navbar-collapse')
            if ($navbar.hasClass('in')) {
                $navbar.collapse('hide')
            }
        }
    }
}
</script>