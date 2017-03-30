
<template>
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-danger">
                    <div class="panel-heading">Not Found (#404)</div>

                    <div class="panel-body">
                        <p>Page not found</p>
                        <p><strong>{{ fullPath }}</strong></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import {setPageTitle} from '../functions.js'
export default {
    name: 'error404',
    created: function() {
        setPageTitle('404')
    },
    data: function() {
        return {
            fullPath: this.$route.fullPath
        }
    }
}
</script>