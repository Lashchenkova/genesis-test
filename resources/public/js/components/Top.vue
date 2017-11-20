<template>
    <div class="hbtn">
        <Btn
                v-if="active === 'search'"
                v-for="btn in logouts"
                :key="btn.id"
                :btn="btn"
                @submit="logout"
        >
        </Btn>
        <Btn
                v-else-if="active === 'reg'"
                v-for="btn in logins"
                :key="btn.id"
                :btn="btn"
                @submit="signin"
        >
        </Btn>
        <Btn
                v-else-if="active === 'log'"
                v-for="btn in signups"
                :key="btn.id"
                :btn="btn"
                @submit="signup"
        >
        </Btn>
    </div>
</template>

<script>
    import axios from 'axios'
    import Btn from './Btn.vue'

    export default {
        components: {
            Btn
        },
        data() {
            return {
                active: 'log',
                signups: [
                    {
                        icon: 'fa fa-user-plus',
                        button: 'Sign Up'
                    }
                ],
                logins: [
                    {
                        icon: 'fa fa-sign-in',
                        button: 'Sign In'
                    }
                ],
                logouts: [
                    {
                        icon: 'fa fa-sign-out',
                        button: 'Logout'
                    }
                ],
            }
        },
        mounted: function () {
            this.$root.$on('registered', () => {
                this.active = 'log';
            });
            this.$root.$on('newuser', () => {
                this.active = 'reg';
            });
            this.$root.$on('loggedin', () => {
                this.active = 'search';
            });
            this.$root.$on('loggedout', () => {
                this.active = 'log';
            });
        },
        methods: {
            logout() {
                this.$root.$emit('loggedout');
                localStorage.removeItem('token');
                localStorage.removeItem('id_user');
            },
            signin() {
                this.$root.$emit('registered');
                this.$root.active = 'log';
            },
            signup() {
                this.active = 'reg';
                this.$root.$emit('newuser');
            },
        }
    }
</script>

<style scoped>
    .hbtn {
        width: 10%;
        float: right;
    }
    .hbtn .btn {
        background-color: #FFF;
        color: #582469;
        height: 90%;
        margin-right: 4%;
        margin-top: 3%;
        margin-bottom: 3%;
    }
</style>