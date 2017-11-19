<template>
    <div id="app">
        <div class="header">
            <Top></Top>
        </div>
        <div class="container">
            <Panel></Panel>
            <div class="listContainer">
                <div class="thead" v-if="enable"
                >
                    <div>Firstname</div>
                    <div>Lastname</div>
                    <div>Nickname</div>
                    <div>Age</div>
                </div>
                <Users
                        v-if="enable"
                        v-for="listable in lists"
                        :key="listable.id"
                        :listable="listable"
                >
                </Users>
            </div>
        </div>
    </div>
</template>

<script>
    import axios from 'axios'
    import Panel from './components/Panel.vue'
    import Users from './components/Users.vue'
    import Top from './components/Top.vue'

    export default {
        components: {
            Panel,
            Users,
            Top
        },
        data() {
            return {
                enable: false,
                lists: []
            }
        },
        mounted: function () {
            this.$root.$on('search', (data) => {
                this.enable = true;
                this.lists.length = 0;
                for (let i = 0; i < data.length; i++) {
                    this.lists.push({
                        user: {
                            firstname: data[i].firstname,
                            lastname: data[i].lastname,
                            nickname: data[i].username,
                            age: data[i].age
                        }
                    })
                }
            });
        }
    }
</script>


<style scoped>
    .container {
        top: 5%;
        height: 98%;
        width: 99%;
        position: absolute;
        animation-name: fadeIn;
        animation-duration: 1s;
    }

    .listContainer {
        position: absolute;
        width: 80%;
        margin: 7em 0 43px 10em;
    }
    .header {
        width: 99%;
        height: 50px;
        position: absolute;
        background-color: #4b1d59;
    }
    .thead {
        background: #f9f9f9;
        border-bottom: 1px solid grey;
        margin: 0 auto;
        height: 2%;
        padding: 20px 0;
        text-align: center;
        display: flex;
        justify-content: space-around;
        flex-wrap: nowrap;
        width: 40%;
        font-weight: 600;
    }
</style>