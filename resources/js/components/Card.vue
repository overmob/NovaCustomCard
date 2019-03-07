<template>
    <div>
        <div class="flex items-center"
             style="justify-content: space-between;"
             v-if="!exapandible"
             v-on:click="expandTable()">

            <div class="text-2xl py-4">{{title}}</div>

            <component
                    v-for="action in actions"
                    :key="action.actionId"
                    :filterId="action.actionId"
                    :value="action.value"
                    :is="action.component"
            />
        </div>
        <card class="flex flex-col">
            <div>
                <div class="flex items-center"
                     style="justify-content: space-between;"
                     v-on:click="expandTable()"
                     v-if="exapandible">

                    <div class="flex items-center">
                        <div class="w-8"></div>
                        <div class="text-2xl py-4">{{title}}</div>
                    </div>
                    <div class="flex items-center">
                        <a v-bind:name="'customTable-' + tableId" href="javascript:;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40"
                                 class="mb-3">
                                <g fill="#A8B9C5" fill-rule="nonzero">
                                    <path v-if="isExpanded" d="M38 26h-28v-4h28v4z"/>
                                    <path v-if="!isExpanded" d="M38 26h-12v12h-4v-12h-12v-4h12v-12h4v12h12v4z"/>
                                </g>
                            </svg>
                        </a>
                        <div class="w-4"></div>

                    </div>
                </div>

                <div class="customTable"
                     v-bind:class="tableCustomClass"
                     v-bind:style="[this.exapandible ? { height: tableHeight+ 'px' } : '']">

                    <div>
                        <div class="py-3 flex items-center border-b border-50">
                            <div class="flex items-end ml-auto px-3" v-if="filters.length > 0">

                                <component
                                        v-if="exapandible"
                                        v-for="action in actions"
                                        :key="action.actionId"
                                        :filterId="action.actionId"
                                        :value="action.value"
                                        :is="action.component"
                                        v-on:click="event.preventDefault()"
                                />

                                <div class="w-4"></div>

                                <dropdown
                                        style="overflow: visible!important;"
                                        data-testid="filter-selector"
                                        dusk="filter-selector"
                                        class="bg-30 hover:bg-40 rounded">

                                    <dropdown-trigger
                                            slot-scope="{toggle}"
                                            :handle-click="toggle"
                                            :class="{ 'bg-primary border-primary': filtersAreApplied }"
                                            :active="filtersAreApplied"
                                            class="bg-30 px-3 border-2 border-30 rounded">
                                        <icon type="filter" :class="filtersAreApplied ? 'text-white' : 'text-80'"/>
                                        <span v-if="filtersAreApplied" class="ml-2 font-bold text-white text-80">
                                    {{ activeFilterCount }}
                                </span>
                                    </dropdown-trigger>

                                    <dropdown-menu slot="menu" width="350" direction="rtl" :dark="true">

                                        <div v-if="filtersAreApplied" class="bg-30 border-b border-60">
                                            <button
                                                    @click="resetFilters()"
                                                    class="py-2 w-full block text-xs uppercase tracking-wide text-center text-80 dim font-bold focus:outline-none"
                                            >
                                                {{ __('Reset Filters') }}
                                            </button>
                                        </div>

                                        <component
                                                v-for="filter in filters"
                                                :key="filter.filterId"
                                                :filterId="filter.filterId"
                                                :value="filter.value"
                                                :is="filter.component"
                                                :ref="tableId + filter.filterId"
                                                v-on:filter-change="filterChange"
                                        />
                                        <div v-if="filterChanged" class="bg-30 border-b border-60">
                                            <button
                                                    @click="applyFilters()"
                                                    class="py-2 w-full block text-xs uppercase tracking-wide text-center text-80 dim font-bold focus:outline-none apply-filter"
                                            >
                                                {{ __('Apply Filters') }}
                                            </button>
                                        </div>
                                    </dropdown-menu>
                                </dropdown>

                            </div>
                        </div>
                    </div>

                    <div v-if="loading" style="margin: 40px">
                        <loader class="text-60"/>
                    </div>
                    <div>
                        <div v-if="items.length">
                            <table class="table w-full">
                                <thead>
                                <tr>
                                    <th class="w-8"></th>
                                    <th v-for="field in fields"
                                        class="text-left"
                                        v-bind:class="[field.class]">
                                        <span v-if="!field.sortable">{{field.displayName}}</span>
                                        <custom-sortable-icon
                                                v-else
                                                @sort="sortBy(field.fieldName)"
                                                :resource-name="tableId"
                                                :uri-key="field.fieldName">
                                            {{field.displayName}}
                                        </custom-sortable-icon>
                                    </th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="item in items">
                                    <td class="w-8"></td>
                                    <td v-for="field of fields"
                                        v-bind:class="[field.class]">
                                        {{ item[field.fieldName] }}
                                    </td>
                                </tr>
                                </tbody>
                                <tfoot>
                                <tr class="bg-20 rounded-b">
                                    <td class="w-8"></td>
                                    <td v-for="field in fields"
                                        v-bind:class="[field.class]">
                                        {{field.footerDisplaName}}
                                        <br>
                                        {{ field['footer' + field.fieldName] }}
                                    </td>
                                </tr>
                                </tfoot>
                            </table>

                        </div>
                        <div v-else class="flex justify-center items-center px-6 py-8">
                            <div class="text-center" v-if="!loading">
                                <svg xmlns="http://www.w3.org/2000/svg" width="65" height="51" viewBox="0 0 65 51"
                                     class="mb-3">
                                    <g fill="none" fill-rule="evenodd">
                                        <g id="05-blank-state" fill="#A8B9C5" fill-rule="nonzero"
                                           transform="translate(-779 -695)">
                                            <path id="Combined-Shape"
                                                  d="M835 735h2c.552285 0 1 .447715 1 1s-.447715 1-1 1h-2v2c0 .552285-.447715 1-1 1s-1-.447715-1-1v-2h-2c-.552285 0-1-.447715-1-1s.447715-1 1-1h2v-2c0-.552285.447715-1 1-1s1 .447715 1 1v2zm-5.364125-8H817v8h7.049375c.350333-3.528515 2.534789-6.517471 5.5865-8zm-5.5865 10H785c-3.313708 0-6-2.686292-6-6v-30c0-3.313708 2.686292-6 6-6h44c3.313708 0 6 2.686292 6 6v25.049375c5.053323.501725 9 4.765277 9 9.950625 0 5.522847-4.477153 10-10 10-5.185348 0-9.4489-3.946677-9.950625-9zM799 725h16v-8h-16v8zm0 2v8h16v-8h-16zm34-2v-8h-16v8h16zm-52 0h16v-8h-16v8zm0 2v4c0 2.209139 1.790861 4 4 4h12v-8h-16zm18-12h16v-8h-16v8zm34 0v-8h-16v8h16zm-52 0h16v-8h-16v8zm52-10v-4c0-2.209139-1.790861-4-4-4h-44c-2.209139 0-4 1.790861-4 4v4h52zm1 39c4.418278 0 8-3.581722 8-8s-3.581722-8-8-8-8 3.581722-8 8 3.581722 8 8 8z"></path>
                                        </g>
                                    </g>
                                </svg>
                                <h3 class="text-base text-80 font-normal mb-6">No entry found for search criteria.</h3>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

        </card>
        <hr>
    </div>
</template>

<script>

    import CustomFilterDateRange from "./CustomFilterDateRange";
    import CustomActionDownload from "./CustomActionDownload";
    import CustomFilterBoolean from "./CustomFilterBoolean";
    import CustomSortableIcon from "./CustomSortableIcon";

    export default {
        components: {
            CustomActionDownload,
            CustomSortableIcon,
            CustomFilterDateRange,
            CustomFilterBoolean
        },
        props: [
            'card',
        ],
        data: () => ({
            loading: false,
            tableData: null,
            title: "",
            tableId: "",
            exapandible: false,
            isExpanded: true,
            items: [],
            fields: [],
            filters: [],
            actions: [],
            filtersAreApplied: false,
            activeFilterCount: 0,
            tableHeight: 0,
            calculatedTableHeight: 0,
            tableCustomClass: '',
            fetchStarted: false,
            filterChanged: null
        }),

        mounted() {
            this.tableData = this.card.tableData;
            this.fields = this.card.tableData.fields;
            this.actions = this.card.actions;
            this.filters = this.card.filters;
            this.title = this.tableData.title;
            this.tableId = this.tableData.id;
            this.exapandible = this.tableData.exapandible;
            if (this.exapandible) {
                this.isExpanded = false;
                this.updateTableClass();
            }
            this.readQueryFilter();
            if (!this.exapandible) {
                this.fetch();
            }
        },
        methods: {
            readQueryFilter() {

            },
            fetch() {
                this.fetchStarted = true;
                let query = this.$route.query[this.tableId];
                let queryParam = query && query !== '' ? JSON.parse(atob(query)) : {};

                let param = this.getParam(queryParam);

                if (queryParam.order && queryParam.order !== '' && queryParam.direction && queryParam.direction !== '') {
                    param = param === "" ? "?" : param + "&";
                    param = param + "orderBy=" + queryParam.order;
                    param = param + "&orderByDirection=" + queryParam.direction;
                }
                this.loading = true;
                this.updateTableHeight();
                Nova.request().get(this.tableData.endpoint + param).then(
                    ({
                         data: {
                             results
                         },
                     }) => {

                        this.items = results;
                        for (var field of this.fields) {
                            switch (field.footerType) {
                                case 'total':
                                    var count = 0;
                                    for (var item of this.items) {
                                        count = count + (+item[field.fieldName])
                                    }
                                    field['footer' + field.fieldName] = count;
                                    break;
                            }
                        }
                        this.loading = false;
                        this.updateTableHeight();
                    }
                )
            },
            updateTableHeight() {
                let tableheaderHeight = this.filters.length || this.actions.length ? 61 : 25;
                let rowHeight = 61;
                let columnHeight = 39;
                let rows = this.items.length ? rowHeight * this.items.length + columnHeight + rowHeight : this.loading ? 0 : 173;
                let loading = this.loading ? 92 : 0;
                this.calculatedTableHeight = tableheaderHeight + rows + loading;
                if (this.isExpanded) {
                    this.tableHeight = this.calculatedTableHeight;
                }
            },
            getParam(queryParam) {
                let param = '';
                this.filtersAreApplied = false;
                this.activeFilterCount = 0;
                for (let filter of this.filters) {
                    switch (filter.component) {
                        case 'custom-filter-date-range':
                            let dateFilter = queryParam[filter.filterId];
                            if (dateFilter && dateFilter !== '' && dateFilter.dateFrom && dateFilter.dateFrom !== '' && dateFilter.dateTo && dateFilter.dateTo !== '') {
                                this.filtersAreApplied = true;
                                this.activeFilterCount++;
                                param = param === "" ? "?" : param + "&";
                                param = param + "startAt=" + dateFilter.dateFrom;
                                param = param + "&endAt=" + dateFilter.dateTo;
                                filter.value = {
                                    dateFrom: dateFilter.dateFrom,
                                    dateTo: dateFilter.dateTo
                                }
                            } else {
                                filter.value = undefined;
                            }
                            break;
                        case 'custom-filter-boolean':
                            let booleanFilter = queryParam[filter.filterId];
                            if (booleanFilter) {
                                this.filtersAreApplied = true;
                                this.activeFilterCount++;
                                for (let item of booleanFilter) {
                                    if (item.checked) {
                                        param = param === "" ? "?" : param + "&";
                                        param = param + filter.filterId + "[]=" + item.fieldName;
                                        for (let i of filter.value.items) {
                                            if (i.fieldName === item.fieldName) {
                                                i.checked = true;
                                            }
                                        }
                                    }
                                }
                            }
                            break;
                    }
                }


                for (let action of this.actions) {
                    if (action.value.useFilter) {
                        action.value.param = param;
                        action.value = _.clone(action.value);
                    }
                }

                return param;
            },
            sortBy(field) {
                let nq = _.clone(this.$route.query);
                let query = nq[this.tableId];
                let queryOrder = query && query !== '' ? JSON.parse(atob(query)) : {};
                queryOrder.order = field;
                if ('asc' === queryOrder.direction) {
                    queryOrder.direction = 'desc';
                } else {
                    queryOrder.direction = 'asc';
                }
                nq[this.tableId] = btoa(JSON.stringify(queryOrder));
                this.$router.push({query: nq});
                setTimeout(() => {
                    this.fetch();
                });
            },
            updateQueryParam(updatedFilter) {
                let nq = _.clone(this.$route.query);
                let query = nq[this.tableId];
                let queryFilters = query && query !== '' ? JSON.parse(atob(query)) : {};
                switch (updatedFilter.component) {
                    case 'custom-filter-date-range':
                        if (updatedFilter.data.dateFrom !== '' && updatedFilter.data.dateTo !== '') {
                            queryFilters[updatedFilter.filterId] = {};
                            queryFilters[updatedFilter.filterId].dateFrom = updatedFilter.data.dateFrom;
                            queryFilters[updatedFilter.filterId].dateTo = updatedFilter.data.dateTo;
                        } else {
                            delete queryFilters[updatedFilter.filterId];
                        }
                        break;

                    case 'custom-filter-boolean':
                        let del = true;
                        for (let item of updatedFilter.data.items) {
                            if (item.checked) {
                                del = false;
                            }
                        }
                        if (del) {
                            delete queryFilters[updatedFilter.filterId];
                        } else {
                            queryFilters[updatedFilter.filterId] = updatedFilter.data.items;
                        }
                        break;
                }

                nq[this.tableId] = btoa(JSON.stringify(queryFilters));
                this.$router.push({query: nq});
            },
            filterChange(updatedFilter) {
                this.filterChanged = updatedFilter;
            },
            resetFilters() {
                for (let filter of this.filters) {
                    this.$refs[this.tableId + filter.filterId][0].clearFilter();
                }
                let nq = _.clone(this.$route.query);
                delete nq[this.tableId];
                this.$router.push({query: nq});
                setTimeout(() => {
                    this.fetch();
                });
            },
            applyFilters() {
                this.updateQueryParam(this.filterChanged);
                this.filterChanged = null;
                setTimeout(() => {
                    this.fetch();
                });
            },
            expandTable() {
                if (this.exapandible) {
                    this.isExpanded = this.tableHeight === 0;
                    this.tableHeight = this.isExpanded ? this.calculatedTableHeight : 0;
                    this.updateTableClass();
                    if (this.isExpanded && !this.fetchStarted) {
                        this.fetch();
                    }
                }
            },
            updateTableClass() {
                if (this.isExpanded) {
                    setTimeout(() => {
                        this.tableCustomClass = 'overflow-unset';
                    }, 400);
                } else {
                    this.tableCustomClass = 'overflow-hidden';
                }
            }
        }
    }

</script>
