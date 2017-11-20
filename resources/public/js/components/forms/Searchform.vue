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
            Formwrapper,
            Btn
        },
        data() {
            return {
                btns: [
                    {
                        icon: 'fa fa-search',
                        button: 'Search'
                    }
                ],
                formables: [
                    {
                        type: 'text',
                        value: '',
                    }
                ]
            }
        },
        methods: {
            submit() {
                let query = {};
                let token = localStorage.getItem('token');
                this.formables.forEach(form => {
                    query['query'] = form.value;
                });
                axios.get('/search?', {
                    params: query,
                    headers: { Authorization: "Bearer " + token }

                })
                    .then(response => {
                        this.$root.$emit('search', response.data);
                    })
                    .catch(() => {
                        this.$root.$emit('registered');
                    });
            },
        }
    }
</script>

<style scoped>
    .holder {
        padding: 5px 10px 5px;
    }
    .holder div {
        display: inline-block;
    }
    .holder .form-group {
        width: 70%;
    }
    .holder .btn {
        width: 15%;
        float: right;
    }
</style>