<template>
    <div class="holder">
        <Formwrapper
                v-for="formable in formables"
                :key="formable.id"
                :formable="formable"
        ></Formwrapper>
        <div class="btns">
            <Btn
                    v-for="btn in btns"
                    :key="btn.id"
                    :btn="btn"
                    @submit="submit"
            ></Btn>
        </div>
    </div>
</template>

<script>
    import axios from 'axios'
    import Formwrapper from './Formwrapper.vue'
    import Btn from './../Btn.vue'

    export default {
        components: {
            Formwrapper,
            Btn
        },
        data() {
            return {
                btns: [
                    {
                        icon: 'fa fa-sign-in',
                        button: 'Sign in'
                    }
                ],
                formables: [
                    {
                        label: 'Nickname',
                        type: 'text',
                        value: '',
                        error: 'Something went Wrong, try again',
                        valid: true,
                        name: '_username'
                    },
                    {
                        label: 'Password',
                        type: 'password',
                        value: '',
                        error: 'Something went Wrong, try again',
                        valid: true,
                        name: '_password'
                    }
                ]
            }
        },
        methods: {
            submit() {
                let credentials = {};
                this.formables.forEach(form => {
                    credentials[form.name] = form.value;
                });
                axios.post('/login', credentials)
                    .then(response => {
                        if (response.data.success) {
                            localStorage.setItem('id_user', response.data.id);
                            localStorage.setItem('token', response.data.token);

                            this.$root.$emit('loggedin', credentials);
                        } else {
                            this.formables.forEach(form => {
                                form.valid = false;
                            })
                        }
                    })
                    .catch((e) => {
                        console.log(e.message);
                    });
            }
        }
    }
</script>

<style scoped>
    .holder {
        padding: 15px 20px 15px;
    }
</style>