<template>
    <div>
        <h1>This is the first component</h1>
        <router-view></router-view>
    </div>
</template>
<script>
    export default {
        mounted() {
            console.log('Component mounted.')
        }
    }
</script>