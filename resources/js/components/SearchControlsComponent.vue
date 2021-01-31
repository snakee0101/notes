<template>
    <div class="search-controls m-auto" style="max-width: 640px">
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
                <button class="mr-2 search-box-button bg-gray-200 text-black pt-14 text-center flex flex-col items-center"
                        @click="filterByLabel('Tag 1')">
                    <svg class="icon icon-lg icon-price-tags" viewBox="0 0 40 32">
                        <path d="M38.5 0h-12c-0.825 0-1.977 0.477-2.561 1.061l-14.879 14.879c-0.583 0.583-0.583 1.538 0 2.121l12.879 12.879c0.583 0.583 1.538 0.583 2.121 0l14.879-14.879c0.583-0.583 1.061-1.736 1.061-2.561v-12c0-0.825-0.675-1.5-1.5-1.5zM31 12c-1.657 0-3-1.343-3-3s1.343-3 3-3 3 1.343 3 3-1.343 3-3 3z"></path>
                        <path d="M4 17l17-17h-2.5c-0.825 0-1.977 0.477-2.561 1.061l-14.879 14.879c-0.583 0.583-0.583 1.538 0 2.121l12.879 12.879c0.583 0.583 1.538 0.583 2.121 0l0.939-0.939-13-13z"></path>
                    </svg>
                    <p class="mt-10 mb-4">Tag 1</p>
                </button>
                <button class="search-box-button bg-gray-200 text-black pt-14 text-center flex flex-col items-center"
                        @click="filterByLabel('Tag 2')">
                    <svg class="icon icon-lg icon-price-tags" viewBox="0 0 40 32">
                        <path d="M38.5 0h-12c-0.825 0-1.977 0.477-2.561 1.061l-14.879 14.879c-0.583 0.583-0.583 1.538 0 2.121l12.879 12.879c0.583 0.583 1.538 0.583 2.121 0l14.879-14.879c0.583-0.583 1.061-1.736 1.061-2.561v-12c0-0.825-0.675-1.5-1.5-1.5zM31 12c-1.657 0-3-1.343-3-3s1.343-3 3-3 3 1.343 3 3-1.343 3-3 3z"></path>
                        <path d="M4 17l17-17h-2.5c-0.825 0-1.977 0.477-2.561 1.061l-14.879 14.879c-0.583 0.583-0.583 1.538 0 2.121l12.879 12.879c0.583 0.583 1.538 0.583 2.121 0l0.939-0.939-13-13z"></path>
                    </svg>
                    <p class="mt-10 mb-4">Tag 2</p>
                </button>
            </div>
        </div>
        <div class="color-controls shadow-lg border border-gray-300 mb-4">
            <h2 class="font-bold p-2 pb-2">Colors</h2>
            <div class="p-4 flex flex-row flex-wrap">
                <div class="tooltip2 mx-2" v-for="color in colors">
                    <span class="tooltip2text">{{ color }}</span>
                    <a href=""
                       class="p-4 px-6 color-circle border transition border-gray-400 d-inline-block rounded-full"
                       :class="'bg-google-' + color"
                       @click.prevent="filterByColor(color)"></a>
                    <!--TODO: Problems with color circles positioning-->
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "SearchControlsComponent.vue",
    data: function(){
        return {
            colors: [
                'white', 'red', 'orange', 'yellow',
                'green', 'teal', 'blue', 'dark-blue',
                'purple', 'pink', 'brown', 'grey'
            ],
        };
    },
    created() {
        window.events.$on('searchActivated', function () {
            let controls = document.getElementsByClassName('search-controls')[0];
            controls.classList.add('active');
        });

        window.events.$on('searchCleared', function () {
            let controls = document.getElementsByClassName('search-controls')[0];
            controls.classList.remove('active');
        });
    },
    methods: {
        filterByType(type) {
            alert('Content is filtered by type ' + type);
        },
        filterByLabel(label) {
            alert('Content is filtered by label ' + label);
        },
        filterByColor(color) {
            alert('Content is filtered by color ' + color);
        }
    }
}
</script>

<style scoped>

</style>
