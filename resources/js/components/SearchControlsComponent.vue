<template>
    <div class="search-controls m-auto" :class="isSearchActive ? 'active' :''">
        <div class="m-auto" :class="areSearchControlsVisible ? 'd-block' : 'd-none'" style="max-width: 640px">
            <div class="type-controls mb-4">
                <h2 class="p-2 pb-4">Types</h2>
                <div class="flex flex-row justify-between">
                    <button class="blue-search-box-button" @click="filterByType('checklist')">
                        <i class="bi bi-list icon-lg text-white"></i>
                        <p class="mt-10 mb-4">Lists</p>
                    </button>
                    <button class="blue-search-box-button" @click="filterByType('image')">
                        <i class="bi bi-image icon-lg text-white"></i>
                        <p class="mt-10 mb-4">Images</p>
                    </button>
                    <button class="blue-search-box-button" @click="filterByType('drawing')">
                        <!--TODO: Drawings feature is not implemented yet-->
                        <i class="bi bi-brush-fill icon-lg text-white"></i>
                        <p class="mt-10 mb-4">Drawings</p>
                    </button>
                    <button class="blue-search-box-button" @click="filterByType('links')">
                        <i class="bi bi-link icon-lg text-white"></i>
                        <p class="mt-10 mb-4">URLs</p>
                    </button>
                </div>
            </div>
            <div class="label-controls mb-4" v-if="tags_list.length">
                <h2 class="p-2 pb-4">Labels</h2>
                <div class="flex flex-row items-start flex-wrap justify-between">
                    <button class="gray-search-box-button mb-2" @click="filterByLabel(tag.name)"
                            v-for="tag in tags_list">
                        <i class="bi bi-tags-fill black-icon-lg"></i>
                        <p class="mt-10 mb-4">{{ tag.name }}</p>
                    </button>
                </div>
            </div>
            <div class="color-controls mb-4">
                <h2 class="p-2 pb-2">Colors</h2>
                <div class="p-4 flex flex-row flex-wrap">
                    <a v-for="color in colors" href=""
                       class="color-circle"
                       :class="'bg-google-' + color"
                       v-b-tooltip.hover.bottom :title="color"
                       @click.prevent="filterByColor(color)"></a>
                </div>
            </div>
        </div>
        <div class="searchResults notes-container flex justify-between" v-if="results != undefined" v-masonry transition-duration="0.3s" item-selector=".note"
             gutter=".gutter" :origin-top="true">
            <div class="gutter"></div>

            <note-component v-masonry-tile
                            v-for="note in results"
                            :key="note.id"
                            :note="note"
                            :isTrashed="false">

            </note-component>
        </div>

        <div class="searchResults notes-container" v-if="resultsNotFound">
            <div class="alert alert-danger" role="alert">
                No results found
            </div>
        </div>
    </div>

</template>

<script>
export default {
    name: "SearchControlsComponent.vue",
    data: function () {
        return {
            isSearchActive: false,
            areSearchControlsVisible: false,
            resultsNotFound: false,
            colors: [
                'white', 'red', 'orange', 'yellow',
                'green', 'teal', 'blue', 'dark-blue',
                'purple', 'pink', 'brown', 'grey'
            ],
            tags_list: [],
            results: []
        };
    },
    created() {
        window.events.$on('searchActivated', this.activateSearch);
        window.events.$on('searchCleared', this.deactivateSearch);
        window.events.$on('searchResultsRetrieved', this.showSearchResults);
        window.events.$on('refreshLabels', this.refreshLabels);
    },
    methods: {
        refreshLabels(labels) {
            this.tags_list = labels;
        },
        assignResults: async function (results) {
            this.resultsNotFound = false;
            this.results = results;
        },
        showSearchResults(results) {
            this.assignResults(results).then(
                () => this.checkForResults()
            );
        },
        checkForResults() {
            this.resultsNotFound = !(this.results.length);
            this.areSearchControlsVisible = false;
        },
        activateSearch() {
            this.isSearchActive = true;

            if (window.searchFilters == undefined) {
                window.searchFilters = {
                    'filterBy': '',
                    'filterValue': ''
                };
            }

            if(window.searchFilters.filterBy == '') {
                this.results = [];
                this.tags_list = window.tags_list;
                this.areSearchControlsVisible = true;
            }
        },
        deactivateSearch() {
            this.isSearchActive = false;
            this.areSearchControlsVisible = false;
            this.results = [];
            this.resultsNotFound = false;
            window.searchText = '';

            window.searchFilters = {
                'filterBy': '',
                'filterValue': ''
            };
        },
        saveFilters(filterBy, filterValue) {
            this.areSearchControlsVisible = false;

            window.searchFilters = {
                'filterBy': filterBy,
                'filterValue': filterValue
            };
        },
        filterData(filterBy, filterValue) {
            axios.post('/search', {
                'query': '',
                'filterBy': window.searchFilters.filterBy,
                'filterValue': window.searchFilters.filterValue,
            }).then(res => window.events.$emit('searchResultsRetrieved', res.data));
        },
        filterByType(type) {
            this.saveFilters('type', type);
            this.filterData('type', type);
        },
        filterByLabel(label) {
            this.saveFilters('tags', label);
            this.filterData('tags', label);
        },
        filterByColor(color) {
            this.saveFilters('color', color);
            this.filterData('color', color);
        }
    }
}
</script>

<style scoped>

</style>
