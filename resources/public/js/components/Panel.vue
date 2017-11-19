<template>
    <div class="panel">
        <Regform
                v-if="active === 'reg'"
        >
        </Regform>
        <Logform
                v-else-if="active === 'log'"
        >
        </Logform>
        <Searchform
                v-else-if="active === 'search'"
        >
        </Searchform>
    </div>
</template>

<script>
    import Regform from './forms/Regform.vue'
    import Logform from './forms/Logform.vue'
    import Searchform from './forms/Searchform.vue'

    export default {
        components: {
            Regform,
            Logform,
            Searchform
        },
        data() {
            return {
                active: 'log'
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
            })
        },
    }
</script>

<style scoped>
    .panel {
        top: 10%;
        margin: 0 auto;
        position: relative;
        width: 25%;
        background: #f3f1f1;
        color: #333;
        border: 1px solid rgb(197, 197, 197);
        border-radius: 6px;
    }
</style>