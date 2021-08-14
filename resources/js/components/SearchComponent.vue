<template>
    <section class="search bg-gray-100 flex flex-grow p-2 items-center rounded-lg"
             :class="isSearchFieldActive ? 'active' : ''" style="max-width: 730px">
        <a href="" class="p-2 rounded-full hover:bg-gray-300"
           v-b-tooltip.hover.bottom
           title="Search"
           @click.prevent="search()">
            <i class="bi bi-search icon text-black"></i>
        </a><!--TODO: Restore the tooltip (Search)-->
        <input type="text" class="flex-grow focus:outline-none mx-2 bg-gray-100" placeholder="Search"
               @focus="inFocus()"
               @blur="isSearchFieldActive = false"
               @keypress.enter="search()"
               @input="delayedSearch()"
               v-model="searchText">
        <a href="" class="p-1 rounded-full hover:bg-gray-300"
           v-if="isSearchControlsShown"
           v-b-tooltip.hover.bottom
           title="Clear search"
           @click.prevent="clear()">
            <i class="bi bi-x icon-lg text-black"></i>
        </a>
        <!--TODO: Restore the tooltip (Clear search)-->
    </section>
</template>

<script>
export default {
    name: "SearchComponent",
    data: function () {
        return {
            isSearchFieldActive: false,
            isSearchControlsShown: false,
            searchText: ''
        };
    },
    methods: {
        inFocus() {
            this.isSearchFieldActive = true;
            this.isSearchControlsShown = true;

            window.events.$emit('searchActivated');
        },
        clear() {
            this.isSearchFieldActive = false;
            this.isSearchControlsShown = false;

            this.searchText = '';
            window.events.$emit('searchCleared');
        },
        search() {
            if (this.searchText === '')
                return;

            axios.post('/search', {
                'query': this.searchText,
                'filterBy': window.searchFilters.filterBy,
                'filterValue': window.searchFilters.filterValue,
            }).then(res => window.events.$emit('searchResultsRetrieved', res.data.data));
        },
        delayedSearch() {
            clearTimeout(window.searchTimeoutId);
            window.searchTimeoutId = setTimeout(this.search, 500);
        }
    }
}
</script>

<style scoped>

</style>
