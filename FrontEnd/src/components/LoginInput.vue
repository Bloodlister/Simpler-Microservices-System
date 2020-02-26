<template>
    <div>
        Username: <input type="text" v-bind="username">
        Password: <input type="password" v-bind="password">
        <button @click="login">Login</button>
    </div>
</template>

<script>
    export default {
        name: "LoginInput",
        data() {
            return {
                username: '',
                password: '',
            }
        },
        methods: {
            login() {
                console.log('Logging in');
            }
        }
    }
</script>
