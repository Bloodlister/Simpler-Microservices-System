<template>
    <div>
        Username: <input type="text" v-model="username">
        Password: <input type="password" v-model="password">
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
                this.axios.post('http://users.simple.com/token', {username: this.username, password: this.password})
                    .then(result => console.log(result.data));
            }
        }
    }
</script>
