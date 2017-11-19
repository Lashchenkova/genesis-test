<template>
    <div class="list">
            <div>{{ listable.user.firstname }}</div>
            <div>{{ listable.user.lastname }}</div>
            <div>{{ listable.user.nickname }}</div>
            <div>{{ listable.user.age }}</div>
    </div>
</template>

<script>
    export default {
        props: {
            listable: {
                type: Object,
                required: true
            }
        }
    }
</script>

<style scoped>
    .list {
        background-color: #fff;
        border-bottom: 1px solid grey;
        margin: 0 auto;
        height: 2%;
        padding: 20px 0;
        text-align: center;
        display: flex;
        justify-content: space-around;
        flex-wrap: nowrap;
        width: 40%;
    }
    .list:hover {
        background: #F0F8FF;
    }
    .list div {
        width: 24%;
        font-size: 16px;
    }
</style>