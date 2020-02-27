<template>
    <div>
        Username: <input type="text" v-model="username">
        Password: <input type="password" v-model="password">
        <button @click="login">Login</button>
    </div>
</template>

<script>
    import toastr from 'toastr';

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
                    .then(result => toastr.success(JSON.stringify(result.data)))
                    .catch(err => {
                        toastr.error(err.response.data.error);
                    })
            }
        }
    }
</script>
