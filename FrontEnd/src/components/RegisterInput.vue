<template>
    <div>
        Username: <input type="text" v-bind="username">
        Password: <input type="password" v-bind="password">
        Confirm Password: <input type="password" v-bind="password_conf">
        <button @click="register">Register</button>
    </div>
</template>

<script>
    export default {
        name: "RegisterInput",
        data() {
            return {
                username: '',
                password: '',
                password_conf: '',
            }
        },
        methods: {
            register() {
                console.log('Registering');
            }
        }
    }
</script>

<style scoped>

</style>
