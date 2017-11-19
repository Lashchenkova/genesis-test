<template>
    <div class="form-group">
        <label class="form-label">{{ formable.label }}</label>
        <input v-model.trim="formable.value" :type="formable.type" class="form-input">
        <span v-if="!formable.valid">{{ formable.error }}</span>
    </div>
</template>

<script>
    export default {
        props: {
            formable: {
                type: Object,
                required: true
            }
        }
    }
</script>

<style>
    .form-group {
        margin-bottom: 15px;
        transition: 0.5s;

    }

    .form-group span {
        color: red;
    }

    .form-label {
        color: #5d5c5c;
        font-size: 14px;
        font-weight: 400;
        display: block;
        margin-bottom: 5px;
    }

    .form-input {
        display: block;
        padding: 0 0 0 5px;
        width: 100%;
        height: 30px;
        font-size: 14px;
        line-height: 1.42857143;
        color: #555;
        background-color: #fff;
        border: 1px solid #ccc;
        border-radius: 4px;
        box-shadow: inset 0 0 50px rgba(0, 0, 0, .075);
    }

</style>