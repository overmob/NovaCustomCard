<template>
    <div>
        <a v-on:click.stop="download()"
           class=" btn btn-default btn-primary hover:bg-primary-dark"
           v-bind:href="value.useFilter ? value.endpoint + value.param || '' : value.endpoint"
           download>
            {{value.actionName}}
        </a>
    </div>
</template>

<script>

    export default {
        props: {
            value: Object
        },
        data: () => ({}),

        mounted() {

        },
        methods: {
            download(){

            }
        }
    }

</script>
