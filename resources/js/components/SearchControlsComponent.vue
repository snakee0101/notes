<template>
    <div class="search-controls m-auto" :class="isSearchActive ? 'active' :''">
        <div class="m-auto" style="max-width: 640px">
            <div class="type-controls shadow-lg border border-gray-300 mb-4">
                <h2 class="font-bold p-2 pb-4">Types</h2>
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
            <div class="label-controls shadow-lg border border-gray-300 mb-4">
                <h2 class="font-bold p-2 pb-4">Labels</h2>
                <div class="flex flex-row items-start flex-wrap justify-between">
                    <button class="gray-search-box-button mb-2" @click="filterByLabel(tag.name)" v-for="tag in tags_list">
                        <i class="bi bi-tags-fill icon-lg"></i>
                        <p class="mt-10 mb-4">{{ tag.name }}</p>
                    </button>
                </div>
            </div>
            <div class="color-controls shadow-lg border border-gray-300 mb-4">
                <h2 class="font-bold p-2 pb-2">Colors</h2>
                <div class="p-4 flex flex-row flex-wrap">
                    <a v-for="color in colors" href=""
                       class="color-circle"
                       :class="'bg-google-' + color"
                       v-b-tooltip.hover.bottom :title="color"
                       @click.prevent="filterByColor(color)"></a>
                </div>
            </div>
        </div>
        <div class="searchResults">

        </div>
    </div>

</template>

<script>
export default {
    name: "SearchControlsComponent.vue",
    data: function () {
        return {
            isSearchActive: false,
            colors: [
                'white', 'red', 'orange', 'yellow',
                'green', 'teal', 'blue', 'dark-blue',
                'purple', 'pink', 'brown', 'grey'
            ],
            tags_list: []
        };
    },
    created() {
        window.events.$on('searchActivated', this.activateSearch);
        window.events.$on('searchCleared', this.deactivateSearch);
    },
    methods: {
        activateSearch() {
            this.tags_list = window.tags_list;
            this.isSearchActive = true;

            if(window.searchFilters == undefined) {
                window.searchFilters = {
                    'filterBy': '',
                    'filterValue': ''
                };
            }
        },
        deactivateSearch() {
            this.isSearchActive = false;
        },
        saveFilters(filterBy, filterValue) {
            window.searchFilters = {
                'filterBy': filterBy,
                'filterValue': filterValue
            };
        },
        filterByType(type) {
            this.saveFilters('type', type);

            axios.post('/search', {
                'query': '',
                'filterBy': 'type',
                'filterValue': type,
            }).then(res => console.log(res.data));
        },
        filterByLabel(label) {
            this.saveFilters('tag', label);

            axios.post('/search', {
                'query': '',
                'filterBy': 'tag',
                'filterValue': label,
            }).then(res => console.log(res.data));
        },
        filterByColor(color) {
            this.saveFilters('color', color);

            axios.post('/search', {
                'query': '',
                'filterBy': 'color',
                'filterValue': color,
            }).then(res => console.log(res.data));
        }
    }
}
</script>

<style scoped>

</style>
