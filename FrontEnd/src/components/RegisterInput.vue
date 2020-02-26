<template>
    <div>
        Username: <input type="text" v-model="username">
        Password: <input type="password" v-model="password">
        Confirm Password: <input type="password" v-model="passwordConf">
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
                passwordConf: '',
            }
        },
        methods: {
            register() {
                let requestData = {
                    username: this.username,
                    password: this.password,
                    passwordConf: this.passwordConf,
                };

                this.axios.post('http://users.simple.com/users', requestData)
                    .then(response => console.log(response.data));
            }
        }
    }
</script>

<style scoped>

</style>
