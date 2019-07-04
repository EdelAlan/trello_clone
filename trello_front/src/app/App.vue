<template>
    <div>
        <div v-if="alert.message" :class="`alert ${alert.type}`">{{ alert.message }}</div>
        <router-view></router-view>
    </div>
</template>

<script>
export default {
    name: 'app',
    computed: {
        alert() {
            return this.$store.state.alert
        }
    },
    watch:{
        $route (to, from) {
            this.$store.dispatch('alert/clear');
        }
    } 
};
</script>