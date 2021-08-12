<template>
    <div class="search-controls m-auto" style="max-width: 640px" :class="isSearchActive ? 'active' :''">
        <div class="type-controls shadow-lg border border-gray-300 mb-4">
            <h2 class="font-bold p-2 pb-4">Types</h2>
            <div class="flex flex-row justify-between">
                <button class="blue-search-box-button" @click="filterByType('list')">
                    <i class="bi bi-list icon-lg text-white"></i>
                    <p class="mt-10 mb-4">Lists</p>
                </button>
                <button class="blue-search-box-button" @click="filterByType('image')">
                    <i class="bi bi-image icon-lg text-white"></i>
                    <p class="mt-10 mb-4">Images</p>
                </button>
                <button class="blue-search-box-button" @click="filterByType('drawing')">
                    <i class="bi bi-brush-fill icon-lg text-white"></i>
                    <p class="mt-10 mb-4">Drawings</p>
                </button>
                <button class="blue-search-box-button" @click="filterByType('url')">
                    <i class="bi bi-link icon-lg text-white"></i>
                    <p class="mt-10 mb-4">URLs</p>
                </button>
            </div>
        </div>
        <div class="label-controls shadow-lg border border-gray-300 mb-4">
            <h2 class="font-bold p-2 pb-4">Labels</h2>
            <div class="flex flex-row items-start">
                <button class="gray-search-box-button mr-2" @click="filterByLabel('Tag 1')">
                    <i class="bi bi-tags-fill icon-lg"></i>
                    <p class="mt-10 mb-4">Tag 1</p>
                </button>

                <button class="gray-search-box-button mr-2" @click="filterByLabel('Tag 2')">
                    <i class="bi bi-tags-fill icon-lg"></i>
                    <p class="mt-10 mb-4">Tag 2</p>
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
</template>

<script>
export default {
    name: "SearchControlsComponent.vue",
    data: function(){
        return {
            isSearchActive: false,
            colors: [
                'white', 'red', 'orange', 'yellow',
                'green', 'teal', 'blue', 'dark-blue',
                'purple', 'pink', 'brown', 'grey'
            ],
        };
    },
    created() {
        window.events.$on('searchActivated', this.activateSearch);
        window.events.$on('searchCleared', this.deactivateSearch);
    },
    methods: {
        activateSearch() {
            this.isSearchActive = true;
        },
        deactivateSearch() {
            this.isSearchActive = false;
        },
        filterByType(type) {

            alert('Content is filtered by type ' + type);
        },
        filterByLabel(label) {
            alert('Content is filtered by label ' + label);
        },
        filterByColor(color) {
            axios.post('/search', {
                'query' : '',
                'filterBy' : 'color',
                'filterValue' : color,
            }).then(res => console.log(res.data));
        }
    }
}
</script>

<style scoped>

</style>
