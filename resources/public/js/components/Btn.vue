<template>
    <div class="btn" @click="$emit('submit')" v-on:click="track">
        <i :class="btn.icon" aria-hidden="true"></i> {{ btn.button }}
    </div>
</template>

<script>
    import axios from 'axios'

    export default {
        props: {
            btn: {
                type: Object,
                required: true
            }
        },
        created () {
            window.addEventListener('keypress', this.enter);
        },
        methods: {
            enter(e) {
                let key = e.which || e.keyCode;
                if (key === 13) {
                    this.$emit('submit');
                }
            },
            track() {
                let user_id = localStorage.getItem('id_user');
                let id_user = null;

                if (user_id && user_id !== undefined) {
                    let id_user = user_id;
                }

                let event = {
                    id_user: id_user,
                    button_label: this.btn.button,
                    date_created: new Date().toISOString().slice(0, 19).replace('T', ' '),
                };

                axios.post('/trackers', event);
            }
        }
    }
</script>

<style>
    .btn {
        background-color: #582469;
        color: #FFF;
        font-size: 15px;
        font-weight: 400;
        cursor: pointer;
        border: 1px solid transparent;
        padding: 10px 20px;
        margin-top: 4px;
        border-radius: 4px;
        text-align: center;
    }

    .btn:hover {
        background-color: #4b1d59;
    }
</style>