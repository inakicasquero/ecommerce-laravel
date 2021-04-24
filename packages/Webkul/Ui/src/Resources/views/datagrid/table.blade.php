<div class="table">
    <datagrid-filters
        csrf="{{ csrf_token() }}"
        index="{{ $results['index'] }}"
        enable-actions="{{ $results['enableActions'] }}"
        enable-mass-actions="{{ $results['enableMassActions'] }}"
        items-per-page="{{ $results['itemsPerPage'] ?: 10 }}"
        paginated="{{ $results['paginated'] }}"
        pagination-html="{{ $results['records']->links()->render() }}"
        :records="{{ json_encode($results['records']) }}"
        :columns="{{ json_encode($results['columns']) }}"
        :actions="{{ json_encode($results['actions']) }}"
        :mass-actions="{{ json_encode($results['massactions']) }}"
        :extra-filters="{{ json_encode($results['extraFilters']) }}"
        :translations="{{ json_encode($results['translations']) }}"
    ></datagrid-filters>

    @push('scripts')
        <script type="text/x-template" id="datagrid-filters">
            <div>
                <div class="grid-container">
                    <div class="datagrid-filters">
                        <div class="filter-left">
                            <div class="dropdown-filters per-page" v-if="extraFilters.channels != undefined">
                                <div class="control-group">
                                    <select class="control" id="channel-switcher" name="channel" onchange="reloadPage('channel', this.value)">
                                        <option value="all" :selected="extraFilters.current.channel == 'all'" v-text="translations.allChannels"></option>
                                        <option v-for="channel in extraFilters.channels"
                                            v-text="channel.name"
                                            :value="channel.code"
                                            :selected="channel.code == extraFilters.current.channel">
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <div class="dropdown-filters per-page" v-if="extraFilters.locales != undefined">
                                <div class="control-group">
                                    <select class="control" id="locale-switcher" name="locale" onchange="reloadPage('locale', this.value)">
                                        <option value="all" :selected="extraFilters.current.locale == 'all'" v-text="translations.allLocales"></option>
                                        <option v-for="locale in extraFilters.locales"
                                            v-text="locale.name"
                                            :value="locale.code"
                                            :selected="locale.code == extraFilters.current.locale">
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <div class="dropdown-filters per-page" v-if="extraFilters.customer_groups != undefined">
                                <div class="control-group">
                                    <select class="control" id="customer-group-switcher" name="customer_group" onchange="reloadPage('customer_group', this.value)">
                                        <option value="all" :selected="extraFilters.current.customer_group == 'all'" v-text="translations.allCustomerGroups"></option>
                                        <option v-for="customerGroup in extraFilters.customer_groups"
                                            v-text="customerGroup.name"
                                            :value="customerGroup.id"
                                            :selected="customerGroup.id == extraFilters.current.customer_group">
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="datagrid-filters" id="datagrid-filters">
                        <div class="filter-left">
                            <div class="search-filter">
                                <input type="search" id="search-field" class="control"
                                    :placeholder="translations.search" v-model="searchValue"
                                    v-on:keyup.enter="searchCollection(searchValue)"/>

                                <div class="icon-wrapper">
                                    <span class="icon search-icon search-btn" v-on:click="searchCollection(searchValue)"></span>
                                </div>
                            </div>
                        </div>

                        <div class="filter-right">
                            <div class="dropdown-filters per-page">
                                <div class="control-group">
                                    <label class="per-page-label" for="perPage" v-text="translations.itemsPerPage"></label>

                                    <select id="perPage" name="perPage" class="control" v-model="perPage"
                                        v-on:change="paginate">
                                        <option v-for="index in this.perPageProduct" v-text="index" :key="index" :value="index"></option>
                                    </select>
                                </div>
                            </div>

                            <div class="dropdown-filters">
                                <div class="dropdown-toggle">
                                    <div class="grid-dropdown-header">
                                        <span class="name" v-text="translations.filter"></span>
                                        <i class="icon arrow-down-icon active"></i>
                                    </div>
                                </div>

                                <div class="dropdown-list dropdown-container" style="display: none;">
                                    <ul>
                                        <li>
                                            <div class="control-group">
                                                <select class="filter-column-select control" v-model="filterColumn"
                                                        v-on:click="getColumnOrAlias(filterColumn)">
                                                    <option v-text="translations.column" selected disabled></option>
                                                    <option v-for="column in columns" :value="column.index"
                                                        v-text="column.label" v-if="typeof column.filterable !== 'undefined' && column.filterable"></option>
                                                </select>
                                            </div>
                                        </li>

                                        <li v-if='stringConditionSelect'>
                                            <div class="control-group">
                                                <select class="control" v-model="stringCondition">
                                                    <option v-text="translations.condition" selected disabled></option>
                                                    <option v-text="translations.contains" value="like"></option>
                                                    <option v-text="translations.ncontains" value="nlike"></option>
                                                    <option v-text="translations.equals" value="eq"></option>
                                                    <option v-text="translations.nequals" value="neqs"></option>
                                                </select>
                                            </div>
                                        </li>

                                        <li v-if='stringCondition != null'>
                                            <div class="control-group">
                                                <input type="text" class="control response-string"
                                                    :placeholder="translations.valueHere" v-model="stringValue"/>
                                            </div>
                                        </li>

                                        <li v-if='numberConditionSelect'>
                                            <div class="control-group">
                                                <select class="control" v-model="numberCondition">
                                                    <option v-text="translations.condition" selected disabled></option>
                                                    <option v-text="translations.equals" value="eq"></option>
                                                    <option v-text="translations.nequals" value="neqs"></option>
                                                    <option v-text="translations.greater" value="gt"></option>
                                                    <option v-text="translations.less" value="lt"></option>
                                                    <option v-text="translations.greatere" value="gte"></option>
                                                    <option v-text="translations.lesse" value="lte"></option>
                                                </select>
                                            </div>
                                        </li>

                                        <li v-if='numberCondition != null'>
                                            <div class="control-group">
                                                <input type="text" class="control response-number"
                                                    v-on:input="filterNumberInput" v-model="numberValue"
                                                    :placeholder="translations.numericValueHere"/>
                                            </div>
                                        </li>

                                        <li v-if='booleanConditionSelect'>
                                            <div class="control-group">
                                                <select class="control" v-model="booleanCondition">
                                                    <option v-text="translations.condition" selected disabled></option>
                                                    <option v-text="translations.equals" value="eq"></option>
                                                    <option v-text="translations.nequals" value="neqs"></option>
                                                </select>
                                            </div>
                                        </li>

                                        <li v-if='booleanCondition != null'>
                                            <div class="control-group">
                                                <select class="control" v-model="booleanValue">
                                                    <option v-text="translations.value" selected disabled></option>
                                                    <option v-text="translations.true" value="1"></option>
                                                    <option v-text="translations.false" value="0"></option>
                                                </select>
                                            </div>
                                        </li>

                                        <li v-if='datetimeConditionSelect'>
                                            <div class="control-group">
                                                <select class="control" v-model="datetimeCondition">
                                                    <option v-text="translations.condition" selected disabled></option>
                                                    <option v-text="translations.equals" value="eq"></option>
                                                    <option v-text="translations.nequals" value="neqs"></option>
                                                    <option v-text="translations.greater" value="gt"></option>
                                                    <option v-text="translations.less" value="lt"></option>
                                                    <option v-text="translations.greatere" value="gte"></option>
                                                    <option v-text="translations.lesse" value="lte"></option>
                                                </select>
                                            </div>
                                        </li>

                                        <li v-if='datetimeCondition != null'>
                                            <div class="control-group">
                                                <input class="control" v-model="datetimeValue" type="date">
                                            </div>
                                        </li>

                                        <button v-text="translations.apply" class="btn btn-sm btn-primary apply-filter" v-on:click="getResponse"></button>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="filtered-tags">
                        <span class="filter-tag" v-if="filters.length > 0" v-for="filter in filters"
                            style="text-transform: capitalize;">
                            <span v-if="filter.column == 'perPage'">perPage</span>
                            <span v-else>@{{ filter.label }}</span>

                            <span class="wrapper" v-if="filter.prettyValue">
                                @{{ filter.prettyValue }}
                                <span class="icon cross-icon" v-on:click="removeFilter(filter)"></span>
                            </span>
                            <span class="wrapper" v-else>
                                @{{ decodeURIComponent(filter.val) }}
                                <span class="icon cross-icon" v-on:click="removeFilter(filter)"></span>
                            </span>
                        </span>
                    </div>

                    <table class="table">
                        <thead v-if="massActionsToggle">
                            <tr class="mass-action" v-if="massActionsToggle" style="height: 65px;">
                                <th colspan="100%">
                                    <div class="mass-action-wrapper" style="display: flex; flex-direction: row; align-items: center; justify-content: flex-start;">

                                        <span class="massaction-remove" v-on:click="removeMassActions" style="margin-right: 10px; margin-top: 3px;">
                                            <span class="icon checkbox-dash-icon"></span>
                                        </span>

                                        <form method="POST" id="mass-action-form" style="display: inline-flex;" action="" :onsubmit="`return confirm('${massActionConfirmText}')`">
                                            <input type="hidden" name="_token" :value="csrf">
                                            <input type="hidden" id="indexes" name="indexes" v-model="dataIds">

                                            <div class="control-group">
                                                <select class="control" v-model="massActionType" @change="changeMassActionTarget" name="massaction-type" required>
                                                    <option v-for="(massAction, index) in massActions" v-text="massAction.label" :key="index" :value="massAction.type"></option>
                                                </select>
                                            </div>

                                            <div class="control-group" style="margin-left: 10px;" v-if="massActionType == 'update'">
                                                <select class="control" v-model="massActionUpdateValue" name="update-options" required>
                                                    <option v-for="(massActionValue, id) in massActionValues" :value="massActionValue" v-text="id"></option>
                                                </select>
                                            </div>

                                            <button v-text="translations.submit" type="submit" class="btn btn-sm btn-primary"
                                                style="margin-left: 10px;">
                                            </button>
                                        </form>
                                    </div>
                                </th>
                            </tr>
                        </thead>

                        <thead v-if="massActionsToggle == false">
                            <tr style="height: 65px;">
                                <th v-if="enableMassActions" class="grid_head" id="mastercheckbox" style="width: 50px;">
                                    <span class="checkbox">
                                        <input type="checkbox" v-model="allSelected" v-on:change="selectAll">

                                        <label class="checkbox-view" for="checkbox"></label>
                                    </span>
                                </th>

                                <th v-for="column in columns" v-text="column.label"
                                    class="grid_head" :style="typeof column.width !== 'undefined' && column.width ? `width: ${column.width}` : ''"
                                    v-on:click="typeof column.sortable !== 'undefined' && column.sortable ? sortCollection(column.index) : {}">
                                </th>

                                <th v-if="enableActions" v-text="translations.actions"></th>
                            </tr>
                        </thead>

                        <tbody>
                            <template v-if="records.data.length">
                                <tr v-for="record in records.data">
                                    <td v-if="enableMassActions">
                                        <span class="checkbox">
                                            <input type="checkbox" v-model="dataIds" @change="select" :value="record[index]">

                                            <label class="checkbox-view" for="checkbox"></label>
                                        </span>
                                    </td>

                                    <td v-for="column in columns" v-html="record[column.index]"
                                        :data-value="column.label">
                                    </td>

                                    <td class="actions" style="white-space: nowrap; width: 100px;" :data-value="translations.actions">
                                        <div class="action">
                                            <a v-for="action in actions" v-if="record[`${action.title.toLowerCase()}_to_display`]"
                                                :id="record[typeof action.index !== 'undefined' && action.index ? action.index : index]"
                                                :href="action.method == 'GET' ? record[`${action.title.toLowerCase()}_url`] : 'javascript:void(0);'"
                                                v-on:click="action.method != 'GET' ? ( typeof action.function !== 'undefined' && action.function ? action.function : doAction($event) ) : {}"
                                                :data-method="action.method"
                                                :data-action="record[`${action.title.toLowerCase()}_url`]"
                                                :data-token="csrf"
                                                :target="typeof action.target !== 'undefined' && action.target ? action.target : ''"
                                                :title="typeof action.title !== 'undefined' && action.title ? action.title : ''">
                                                <span :class="action.icon"></span>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                            <template v-else>
                                <tr>
                                    <td colspan="10">
                                        <p style="text-align: center;" v-text="translations.norecords"></p>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                <div class="pagination" v-if="typeof paginated !== 'undefined' && paginated"
                    v-html="paginationHtml">
                </div>
            </div>
        </script>

        <script>
            Vue.component('datagrid-filters', {
                template: '#datagrid-filters',

                props: [
                    'csrf',
                    'index',
                    'records',
                    'columns',
                    'actions',
                    'enableActions',
                    'massActions',
                    'enableMassActions',
                    'paginated',
                    'paginationHtml',
                    'itemsPerPage',
                    'extraFilters',
                    'translations'
                ],

                data: function() {
                    return {
                        url: new URL(window.location.href),
                        filterIndex: this.index,
                        gridCurrentData: this.records,
                        massActionsToggle: false,
                        massActionTarget: null,
                        massActionConfirmText: this.translations.clickOnAction,
                        massActionType: null,
                        massActionValues: [],
                        massActionTargets: [],
                        massActionUpdateValue: null,
                        currentSort: null,
                        dataIds: [],
                        allSelected: false,
                        sortDesc: 'desc',
                        sortAsc: 'asc',
                        sortUpIcon: 'sort-up-icon',
                        sortDownIcon: 'sort-down-icon',
                        currentSortIcon: null,
                        isActive: false,
                        isHidden: true,
                        searchValue: '',
                        filterColumn: true,
                        filters: [],
                        columnOrAlias: '',
                        type: null,
                        stringCondition: null,
                        booleanCondition: null,
                        numberCondition: null,
                        datetimeCondition: null,
                        stringValue: null,
                        booleanValue: null,
                        datetimeValue: '2000-01-01',
                        numberValue: 0,
                        stringConditionSelect: false,
                        booleanConditionSelect: false,
                        numberConditionSelect: false,
                        datetimeConditionSelect: false,
                        perPage: this.itemsPerPage,
                        perPageProduct: [10, 20, 30, 40, 50],
                    }
                },

                mounted: function() {
                    this.setParamsAndUrl();

                    if (this.filters.length) {
                        for (let i = 0; i < this.filters.length; i++) {
                            if (this.filters[i].column === 'perPage') {
                                this.perPage = this.filters[i].val;
                            }
                        }
                    }

                    if (this.perPageProduct.indexOf(parseInt(this.perPage)) === -1) {
                        this.perPageProduct.unshift(this.perPage);
                    }

                    /* testing log */
                    console.log(
                        'Index--------------> ', this.index, '\n\n',
                        'Records------------> ', this.records, '\n\n',
                        'columns------------> ', this.columns, '\n\n',
                        'actions------------> ', this.actions, '\n\n',
                        'enableActions------> ', this.enableActions, '\n\n',
                        'massActions--------> ', this.massActions, '\n\n',
                        'enableMassActions--> ', this.enableMassActions, '\n\n',
                        'paginated----------> ', this.paginated, '\n\n',
                        'itemsPerPage-------> ', this.itemsPerPage, '\n\n',
                        'extraFilters-------> ', this.extraFilters, '\n\n',
                    );
                },

                methods: {
                    getColumnOrAlias: function(columnOrAlias) {
                        this.columnOrAlias = columnOrAlias;

                        for (column in this.columns) {
                            if (this.columns[column].index === this.columnOrAlias) {
                                this.type = this.columns[column].type;

                                switch (this.type) {
                                    case 'string': {
                                        this.stringConditionSelect = true;
                                        this.datetimeConditionSelect = false;
                                        this.booleanConditionSelect = false;
                                        this.numberConditionSelect = false;

                                        this.nullify();
                                        break;
                                    }

                                    case 'datetime': {
                                        this.datetimeConditionSelect = true;
                                        this.stringConditionSelect = false;
                                        this.booleanConditionSelect = false;
                                        this.numberConditionSelect = false;

                                        this.nullify();
                                        break;
                                    }

                                    case 'boolean': {
                                        this.booleanConditionSelect = true;
                                        this.datetimeConditionSelect = false;
                                        this.stringConditionSelect = false;
                                        this.numberConditionSelect = false;

                                        this.nullify();
                                        break;
                                    }

                                    case 'number': {
                                        this.numberConditionSelect = true;
                                        this.booleanConditionSelect = false;
                                        this.datetimeConditionSelect = false;
                                        this.stringConditionSelect = false;

                                        this.nullify();
                                        break;
                                    }

                                    case 'price': {
                                        this.numberConditionSelect = true;
                                        this.booleanConditionSelect = false;
                                        this.datetimeConditionSelect = false;
                                        this.stringConditionSelect = false;

                                        this.nullify();
                                        break;
                                    }
                                }
                            }
                        }
                    },

                    nullify: function() {
                        this.stringCondition = null;
                        this.datetimeCondition = null;
                        this.booleanCondition = null;
                        this.numberCondition = null;
                    },

                    filterNumberInput: function(e) {
                        this.numberValue = e.target.value.replace(/[^0-9\,\.]+/g, '');
                    },

                    getResponse: function() {
                        label = '';

                        for (let colIndex in this.columns) {
                            if (this.columns[colIndex].index == this.columnOrAlias) {
                                label = this.columns[colIndex].label;
                                break;
                            }
                        }

                        if (this.type === 'string' && this.stringValue !== null) {
                            this.formURL(this.columnOrAlias, this.stringCondition, encodeURIComponent(this
                                .stringValue), label)
                        } else if (this.type === 'number') {
                            indexConditions = true;

                            if (this.filterIndex === this.columnOrAlias &&
                                (this.numberValue === 0 || this.numberValue < 0)) {
                                indexConditions = false;

                                alert(this.translations.zeroIndex);
                            }

                            if (indexConditions) {
                                this.formURL(this.columnOrAlias, this.numberCondition, this.numberValue, label);
                            }
                        } else if (this.type === 'boolean') {
                            this.formURL(this.columnOrAlias, this.booleanCondition, this.booleanValue, label);
                        } else if (this.type === 'datetime') {
                            this.formURL(this.columnOrAlias, this.datetimeCondition, this.datetimeValue, label);
                        } else if (this.type === 'price') {
                            this.formURL(this.columnOrAlias, this.numberCondition, this.numberValue, label);
                        }
                    },

                    sortCollection: function(alias) {
                        let label = '';

                        for (let colIndex in this.columns) {
                            if (this.columns[colIndex].index === alias) {
                                matched = 0;
                                label = this.columns[colIndex].label;
                                break;
                            }
                        }

                        this.formURL("sort", alias, this.sortAsc, label);
                    },

                    searchCollection: function(searchValue) {
                        this.formURL("search", 'all', searchValue, 'Search');
                    },

                    /* function triggered to check whether the query exists or not and then call the make filters from the url */
                    setParamsAndUrl: function() {
                        params = (new URL(window.location.href)).search;

                        if (params.slice(1, params.length).length > 0) {
                            this.arrayFromUrl();
                        }

                        for (let id in this.massActions) {
                            targetObj = {
                                'type': this.massActions[id].type,
                                'action': this.massActions[id].action,
                                'confirm_text': this.massActions[id].confirm_text
                            };

                            this.massActionTargets.push(targetObj);

                            targetObj = {};

                            if (this.massActions[id].type === 'update') {
                                this.massActionValues = this.massActions[id].options;
                            }
                        }
                    },

                    findCurrentSort: function() {
                        for (let i in this.filters) {
                            if (this.filters[i].column === 'sort') {
                                this.currentSort = this.filters[i].val;
                            }
                        }
                    },

                    changeMassActionTarget: function() {
                        if (this.massActionType === 'delete') {
                            for (let i in this.massActionTargets) {
                                if (this.massActionTargets[i].type === 'delete') {
                                    this.massActionTarget = this.massActionTargets[i].action;
                                    this.massActionConfirmText = this.massActionTargets[i].confirm_text ? this
                                        .massActionTargets[i].confirm_text : this.massActionConfirmText;

                                    break;
                                }
                            }
                        }

                        if (this.massActionType === 'update') {
                            for (let i in this.massActionTargets) {
                                if (this.massActionTargets[i].type === 'update') {
                                    this.massActionTarget = this.massActionTargets[i].action;
                                    this.massActionConfirmText = this.massActionTargets[i].confirm_text ? this
                                        .massActionTargets[i].confirm_text : this.massActionConfirmText;

                                    break;
                                }
                            }
                        }

                        document.getElementById('mass-action-form').action = this.massActionTarget;
                    },

                    /* make array of filters, sort and search */
                    formURL: function(column, condition, response, label) {
                        let obj = {};

                        if (column === "" || condition === "" || response === "" ||
                            column === null || condition === null || response === null) {
                            alert(this.translations.filterFieldsMissing);

                            return false;
                        } else {
                            if (this.filters.length > 0) {
                                if (column !== "sort" && column !== "search") {
                                    let filterRepeated = false;

                                    for (let j = 0; j < this.filters.length; j++) {
                                        if (this.filters[j].column === column) {
                                            if (this.filters[j].cond === condition && this.filters[j].val ===
                                                response) {
                                                filterRepeated = true;

                                                return false;
                                            } else if (this.filters[j].cond === condition && this.filters[j]
                                                .val !== response) {
                                                filterRepeated = true;

                                                this.filters[j].val = response;

                                                this.makeURL();
                                            }
                                        }
                                    }

                                    if (filterRepeated === false) {
                                        obj.column = column;
                                        obj.cond = condition;
                                        obj.val = response;
                                        obj.label = label;

                                        this.filters.push(obj);
                                        obj = {};

                                        this.makeURL();
                                    }
                                }

                                if (column === "sort") {
                                    let sort_exists = false;

                                    for (let j = 0; j < this.filters.length; j++) {
                                        if (this.filters[j].column === "sort") {
                                            if (this.filters[j].column === column && this.filters[j].cond ===
                                                condition) {
                                                this.findCurrentSort();

                                                if (this.currentSort === "asc") {
                                                    this.filters[j].column = column;
                                                    this.filters[j].cond = condition;
                                                    this.filters[j].val = this.sortDesc;

                                                    this.makeURL();
                                                } else {
                                                    this.filters[j].column = column;
                                                    this.filters[j].cond = condition;
                                                    this.filters[j].val = this.sortAsc;

                                                    this.makeURL();
                                                }
                                            } else {
                                                this.filters[j].column = column;
                                                this.filters[j].cond = condition;
                                                this.filters[j].val = response;
                                                this.filters[j].label = label;

                                                this.makeURL();
                                            }

                                            sort_exists = true;
                                        }
                                    }

                                    if (sort_exists === false) {
                                        if (this.currentSort === null)
                                            this.currentSort = this.sortAsc;

                                        obj.column = column;
                                        obj.cond = condition;
                                        obj.val = this.currentSort;
                                        obj.label = label;

                                        this.filters.push(obj);

                                        obj = {};

                                        this.makeURL();
                                    }
                                }

                                if (column === "search") {
                                    let search_found = false;

                                    for (let j = 0; j < this.filters.length; j++) {
                                        if (this.filters[j].column === "search") {
                                            this.filters[j].column = column;
                                            this.filters[j].cond = condition;
                                            this.filters[j].val = encodeURIComponent(response);
                                            this.filters[j].label = label;

                                            this.makeURL();
                                        }
                                    }

                                    for (let j = 0; j < this.filters.length; j++) {
                                        if (this.filters[j].column === "search") {
                                            search_found = true;
                                        }
                                    }

                                    if (search_found === false) {
                                        obj.column = column;
                                        obj.cond = condition;
                                        obj.val = encodeURIComponent(response);
                                        obj.label = label;

                                        this.filters.push(obj);

                                        obj = {};

                                        this.makeURL();
                                    }
                                }
                            } else {
                                obj.column = column;
                                obj.cond = condition;
                                obj.val = encodeURIComponent(response);
                                obj.label = label;

                                this.filters.push(obj);

                                obj = {};

                                this.makeURL();
                            }
                        }
                    },

                    /* make the url from the array and redirect */
                    makeURL: function() {
                        newParams = '';

                        for (let i = 0; i < this.filters.length; i++) {
                            if (this.filters[i].column == 'status' || this.filters[i].column ==
                                'value_per_locale' || this.filters[i].column == 'value_per_channel' || this
                                .filters[i].column == 'is_unique') {
                                if (this.filters[i].val.includes("True")) {
                                    this.filters[i].val = 1;
                                } else if (this.filters[i].val.includes("False")) {
                                    this.filters[i].val = 0;
                                }
                            }

                            let condition = '';
                            if (this.filters[i].cond !== undefined) {
                                condition = '[' + this.filters[i].cond + ']';
                            }

                            if (i == 0) {
                                newParams = '?' + this.filters[i].column + condition + '=' + this.filters[i]
                                    .val;
                            } else {
                                newParams = newParams + '&' + this.filters[i].column + condition + '=' + this
                                    .filters[i].val;
                            }
                        }

                        let uri = window.location.href.toString();

                        let clean_uri = uri.substring(0, uri.indexOf("?")).trim();

                        window.location.href = clean_uri + newParams;
                    },

                    /* make the filter array from url after being redirected */
                    arrayFromUrl: function() {
                        let obj = {};
                        const processedUrl = this.url.search.slice(1, this.url.length);
                        let splitted = [];
                        let moreSplitted = [];

                        splitted = processedUrl.split('&');

                        for (let i = 0; i < splitted.length; i++) {
                            moreSplitted.push(splitted[i].split('='));
                        }

                        for (let i = 0; i < moreSplitted.length; i++) {
                            const key = decodeURI(moreSplitted[i][0]);
                            let value = decodeURI(moreSplitted[i][1]);

                            if (value.includes('+')) {
                                value = value.replace('+', ' ');
                            }

                            obj.column = key.replace(']', '').split('[')[0];
                            obj.cond = key.replace(']', '').split('[')[1]
                            obj.val = value;

                            switch (obj.column) {
                                case "search":
                                    obj.label = "Search";
                                    break;
                                case "channel":
                                    obj.label = "Channel";
                                    if ('channels' in this.extraFilters) {
                                        obj.prettyValue = this.extraFilters['channels'].find(channel => channel
                                            .code == obj.val);

                                        if (obj.prettyValue !== undefined) {
                                            obj.prettyValue = obj.prettyValue.name;
                                        }
                                    }
                                    break;
                                case "locale":
                                    obj.label = "Locale";
                                    if ('locales' in this.extraFilters) {
                                        obj.prettyValue = this.extraFilters['locales'].find(locale => locale
                                            .code === obj.val);

                                        if (obj.prettyValue !== undefined) {
                                            obj.prettyValue = obj.prettyValue.name;
                                        }
                                    }
                                    break;
                                case "customer_group":
                                    obj.label = "Customer Group";
                                    if ('customer_groups' in this.extraFilters) {
                                        obj.prettyValue = this.extraFilters['customer_groups'].find(
                                            customer_group => customer_group.id === parseInt(obj.val, 10));

                                        if (obj.prettyValue !== undefined) {
                                            obj.prettyValue = obj.prettyValue.name;
                                        }
                                    }
                                    break;
                                case "sort":
                                    for (let colIndex in this.columns) {
                                        if (this.columns[colIndex].index === obj.cond) {
                                            obj.label = this.columns[colIndex].label;
                                            break;
                                        }
                                    }
                                    break;
                                default:
                                    for (let colIndex in this.columns) {
                                        if (this.columns[colIndex].index === obj.column) {
                                            obj.label = this.columns[colIndex].label;

                                            if (this.columns[colIndex].type === 'boolean') {
                                                if (obj.val === '1') {
                                                    obj.val = this.translations.true;
                                                } else {
                                                    obj.val = this.translations.false;
                                                }
                                            }
                                        }
                                    }
                                    break;
                            }

                            if (obj.column !== undefined && obj.val !== undefined) {
                                this.filters.push(obj);
                            }

                            obj = {};
                        }
                    },

                    removeFilter: function(filter) {
                        for (let i in this.filters) {
                            if (this.filters[i].column === filter.column &&
                                this.filters[i].cond === filter.cond &&
                                this.filters[i].val === filter.val) {
                                this.filters.splice(i, 1);

                                this.makeURL();
                            }
                        }
                    },

                    /* triggered when any select box is clicked in the datagrid */
                    select: function() {
                        this.allSelected = false;

                        if (this.dataIds.length === 0) {
                            this.massActionsToggle = false;
                            this.massActionType = null;
                        } else {
                            this.massActionsToggle = true;
                        }
                    },

                    /* triggered when master checkbox is clicked */
                    selectAll: function() {
                        this.dataIds = [];

                        this.massActionsToggle = true;

                        if (this.allSelected) {
                            if (this.gridCurrentData.hasOwnProperty("data")) {
                                for (let currentData in this.gridCurrentData.data) {

                                    let i = 0;
                                    for (let currentId in this.gridCurrentData.data[currentData]) {
                                        if (i == 0) {
                                            this.dataIds.push(this.gridCurrentData.data[currentData][this
                                                .filterIndex
                                            ]);
                                        }

                                        i++;
                                    }
                                }
                            } else {
                                for (currentData in this.gridCurrentData) {

                                    let i = 0;
                                    for (let currentId in this.gridCurrentData[currentData]) {
                                        if (i === 0)
                                            this.dataIds.push(this.gridCurrentData[currentData][currentId]);

                                        i++;
                                    }
                                }
                            }
                        }
                    },

                    captureColumn: function(id) {
                        element = document.getElementById(id);
                    },

                    removeMassActions: function() {
                        this.dataIds = [];

                        this.massActionsToggle = false;

                        this.allSelected = false;

                        this.massActionType = null;
                    },

                    paginate: function(e) {
                        for (let i = 0; i < this.filters.length; i++) {
                            if (this.filters[i].column == 'perPage') {
                                this.filters.splice(i, 1);
                            }
                        }

                        this.filters.push({
                            "column": "perPage",
                            "cond": "eq",
                            "val": e.target.value
                        });

                        this.makeURL();
                    }
                }
            });


            function doAction(e, message, type) {
                let element = e.currentTarget;
                console.log(element);

                if (message) {
                    element = e.target.parentElement;
                }

                message = message || 'Are you sure?';

                if (confirm(message)) {
                    axios.post(element.getAttribute('data-action'), {
                        _token: element.getAttribute('data-token'),
                        _method: element.getAttribute('data-method')
                    }).then(function(response) {
                        this.result = response;

                        if (response.data.redirect) {
                            // window.location.href = response.data.redirect;
                        } else {
                            // location.reload();
                        }
                    }).catch(function(error) {
                        // location.reload();
                    });

                    e.preventDefault();
                } else {
                    e.preventDefault();
                }
            }

        </script>
    @endpush
</div>
