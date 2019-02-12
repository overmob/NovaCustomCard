<template>
    <filter-select>
        <h3 slot="default" class="text-sm uppercase tracking-wide text-80 bg-30 p-3">
            {{__('Data:')}}
        </h3>
        <div class="m-3">
            <div class="flex flex-row items-center">
                <div class="w-1/6 text-center">
                    Da:
                </div>
                <div class="w-2/3">
                    <flat-pickr
                        v-model="dateFrom"
                        :config="fpconfigfrom"
                        @on-open="removeOverflowHidden"
                        class="form-control form-input form-input-bordered fp-cursor-pointer"
                        @input="dateChange"
                        placeholder="Seleziona data">
                    </flat-pickr>
                </div>
                <div class="w-1/6 text-center pl-4">

                </div>
            </div>
            <div class="m-2"></div>
            <div class="flex flex-row items-center">
                <div class="w-1/6 text-center">
                    A:
                </div>
                <div class="w-2/3">
                    <flat-pickr
                        v-model="dateTo"
                        :config="fpconfigto"
                        @on-open="removeOverflowHidden"
                        class="form-control form-input form-input-bordered fp-cursor-pointer"
                        @input="dateChange"
                        placeholder="Seleziona data">
                    </flat-pickr>
                </div>
                <div class="w-1/6 text-center pl-4">
                    <button v-on:click="dateTo = ''; dateFrom = '';">
                        <icon type="delete" class="text-80"/>
                    </button>
                </div>
            </div>
        </div>

    </filter-select>
</template>

<script>

    import flatPickr from 'vue-flatpickr-component';
    import 'flatpickr/dist/flatpickr.css';
    import {Italian} from "flatpickr/dist/l10n/it.js"

    flatpickr.localize(Italian);

    export default {
        props: {
            filterId: String,
            value: Object,
            currentValue: Object
        },
        data: () => ({
            dateFrom: '',
            oldDateFrom: '',
            dateTo: '',
            oldDateTo: '',
            fpconfigfrom: {
                wrap: true,
                altFormat: 'M j, Y',
                altInput: true,
                static: true,
                dateFormat: 'Y-m-d',
            },
            fpconfigto: {
                wrap: true,
                altFormat: 'M j, Y',
                altInput: true,
                static: true,
                dateFormat: 'Y-m-d',
            },
        }),

        mounted() {
            this.initDate();
        },
        methods: {
            initDate() {
                if (this.value) {
                    this.dateFrom = this.value.dateFrom ? this.value.dateFrom : '';
                    this.dateTo = this.value.dateTo ? this.value.dateTo : '';
                    this.oldDateFrom = this.dateFrom;
                    this.oldDateTo = this.dateTo;
                }
            },
            dateChange() {
                if (this.dateFrom !== this.oldDateFrom || this.dateTo !== this.oldDateTo) {
                    this.oldDateFrom = this.dateFrom;
                    this.oldDateTo = this.dateTo;

                    if (this.dateTo) {
                        this.$set(this.fpconfigfrom, 'maxDate', new Date(this.dateTo + " 00:00:00"));
                    } else {
                        this.$set(this.fpconfigfrom, 'maxDate', "");
                    }

                    if (this.dateFrom) {
                        this.$set(this.fpconfigto, 'minDate', new Date(this.dateFrom + " 00:00:00"));
                    } else {
                        this.$set(this.fpconfigto, 'minDate', null);
                    }

                    let newFilter = {
                        filterId: this.filterId,
                        component: 'custom-filter-date-range',
                        data: {
                            dateFrom: this.dateFrom,
                            dateTo: this.dateTo
                        }
                    };
                    if (this.dateFrom && this.dateTo || !this.dateFrom && !this.dateTo) {
                        this.$emit('filter-change', newFilter);
                    }
                }
            },
            removeOverflowHidden() {
                const wrapper = document.querySelector('.dropdown-menu div');
                wrapper.classList.remove('overflow-hidden');
            },
            clearFilter() {
                this.dateFrom = '';
                this.dateTo = '';
                this.oldDateFrom = '';
                this.oldDateTo = '';
            }
        },
        components: {
            flatPickr
        }
    }

</script>
