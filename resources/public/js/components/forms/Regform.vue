<template>
    <div class="holder">
        <Formwrapper
                v-for="formable in formables"
                :key="formable.id"
                :formable="formable"
        ></Formwrapper>
        <Btn
                v-for="btn in btns"
                :key="btn.id"
                :btn="btn"
                @submit="submit"
        ></Btn>
    </div>
</template>

<script>
    import axios from 'axios'
    import Formwrapper from './Formwrapper.vue'
    import Btn from './../Btn.vue'

    export default {
        components: {
            Formwrapper, Btn
        },
        data() {
            return {
                btns: [
                    {
                        faico: 'fa fa-user-plus',
                        button: 'Sign Up'
                    }
                ],
                formables: [
                    {
                        label: 'Firstname',
                        type: 'text',
                        value: '',
                        error: 'Use only letters',
                        valid: true,
                        regex: '^[a-zA-Z\\s]+$',
                        name: 'firstname'
                    },
                    {
                        label: 'Lastname',
                        type: 'text',
                        value: '',
                        error: 'Use only letters',
                        valid: true,
                        regex: '^[a-zA-Z\\s]+$',
                        name: 'lastname'
                    },
                    {
                        label: 'Nickname',
                        type: 'text',
                        value: '',
                        error: 'Use only letters, numbers and underscore',
                        valid: true,
                        regex: '^[A-z0-9_]+$',
                        name: 'username'
                    },
                    {
                        label: 'Age',
                        type: 'text',
                        value: '',
                        error: 'Must contain numbers between 1 and 200',
                        valid: true,
                        regex: '^(0?[1-9]|[1-9][0-9]|[1][1-9][1-9]|200)$',
                        name: 'age'
                    },
                    {
                        label: 'Password',
                        type: 'password',
                        value: '',
                        error: 'Passwords must be at least 6 characters in length',
                        valid: true,
                        regex: '^.{6,}$',
                        name: 'password'
                    }
                ]
            }
        },
        methods: {
            submit() {
                let user = {};
                this.formables.forEach(form => {
                    if (form.value === '' || !form.value.match(form.regex)) {
                        form.valid = false;
                    } else {
                        form.valid = true;
                        user[form.name] = form.value;
                    }
                });
                if (Object.keys(user).length === 5) {
                    axios.post('/users', user).then(response => {
                        if (!response.data) {
                            this.formables.forEach(form => {
                                if (form.name === 'username') {
                                    form.error = 'This nickname is already used';
                                    form.valid = false;
                                }
                            })
                        } else {
                            this.$root.$emit('registered');
                        }
                    });
                }
            }
        },
    }
</script>

<style scoped>
    .holder {
        padding: 15px 20px 15px;
    }
</style>