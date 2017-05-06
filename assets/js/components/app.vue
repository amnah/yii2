
<template>
    <div id="app">
        <navbar></navbar>
        <router-view></router-view>
    </div>
</template>

<script>
export default {
    name: 'app',
    components: {
        navbar: require('./navbar.vue')
    }
}
</script>