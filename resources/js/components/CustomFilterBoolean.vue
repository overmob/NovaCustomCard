<template>
    <div>
        <h3 class="text-sm uppercase tracking-wide text-80 bg-30 p-3">{{ value.displayName }}</h3>

        <div v-for="item in value.items">
            <checkbox-with-label
                @change="updateCheckedState(item, $event)"
                :checked="item.checked"
                class="m-2">
                {{ item.displayName }}
            </checkbox-with-label>
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            filterId: String,
            value: Object,
            currentValue: Object
        },
        mounted() {
            for (var item of this.value.items) {
                item.checked = !!item.checked;
            }
        },
        methods: {
            updateCheckedState(item, event) {
                item.checked = !item.checked;

                let newFilter = {
                    filterId: this.filterId,
                    component: 'custom-filter-boolean',
                    data: {
                        items: this.value.items
                    }
                };
                this.$emit('filter-change', newFilter);
            },
            clearFilter() {
                for (var item of this.value.items) {
                    item.checked = false;
                }
            }
        }
    }

</script>
