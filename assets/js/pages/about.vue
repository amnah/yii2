
<template>
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">About</div>

                    <div class="panel-body">
                        <p>This is the About page.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import {setPageTitle} from '../functions.js'
export default {
    name: 'about',
    created: function() {
        setPageTitle('About')
    }
}
</script>