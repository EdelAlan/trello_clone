<template>
    <div>
        <h1>Hi {{user.name}}!</h1>
        <p>
            <router-link to="/login">Logout</router-link>
        </p>
    </div>
</template>

<script>
export default {
    computed: {
        user() {
            return this.$store.state.user.user;
        },
    },
    created() {
        this.$store.dispatch('user/getUser');
    }
};
</script>