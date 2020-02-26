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
                this.axios.post('http://users.simple.com', {username: this.username, password: this.password})
                    .then(result => console.log(result.data));
            }
        }
    }
</script>
