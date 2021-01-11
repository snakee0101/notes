<template>
    <div class="search-controls m-auto" style="max-width: 640px">
        <div class="type-controls shadow-lg border border-gray-300 mb-4">
            <h2 class="font-bold p-2 pb-4">Types</h2>
            <div class="flex flex-row justify-between">
                <button class="search-box-button bg-blue-600 text-white pt-14 text-center flex flex-col items-center"
                        @click="filterByType('list')">
                    <svg class="icon icon-list2 icon-lg" viewBox="0 0 32 32">
                        <path d="M12 2h20v4h-20v-4zM12 14h20v4h-20v-4zM12 26h20v4h-20v-4zM0 4c0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.209-1.791 4-4 4s-4-1.791-4-4zM0 16c0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.209-1.791 4-4 4s-4-1.791-4-4zM0 28c0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.209-1.791 4-4 4s-4-1.791-4-4z"></path>
                    </svg>
                    <p class="mt-10 mb-4">Lists</p>
                </button>
                <button class="search-box-button bg-blue-600 text-white pt-14 text-center flex flex-col items-center"
                        @click="filterByType('image')">
                    <svg class="icon icon-image icon-lg" viewBox="0 0 32 32">
                        <path d="M29.996 4c0.001 0.001 0.003 0.002 0.004 0.004v23.993c-0.001 0.001-0.002 0.003-0.004 0.004h-27.993c-0.001-0.001-0.003-0.002-0.004-0.004v-23.993c0.001-0.001 0.002-0.003 0.004-0.004h27.993zM30 2h-28c-1.1 0-2 0.9-2 2v24c0 1.1 0.9 2 2 2h28c1.1 0 2-0.9 2-2v-24c0-1.1-0.9-2-2-2v0z"></path>
                        <path d="M26 9c0 1.657-1.343 3-3 3s-3-1.343-3-3 1.343-3 3-3 3 1.343 3 3z"></path>
                        <path d="M28 26h-24v-4l7-12 8 10h2l7-6z"></path>
                    </svg>
                    <p class="mt-10 mb-4">Images</p>
                </button>
                <button class="search-box-button bg-blue-600 text-white pt-14 text-center flex flex-col items-center"
                        @click="filterByType('drawing')">
                    <svg class="icon icon-brush icon-lg" viewBox="0 0 32 32">
                        <path d="M27.555 8.42c-1.355 1.647-5.070 6.195-8.021 9.81l-3.747-3.804c3.389-3.016 7.584-6.744 9.1-8.079 2.697-2.377 5.062-3.791 5.576-3.213 0.322 0.32-0.533 2.396-2.908 5.286zM18.879 19.030c-1.143 1.399-2.127 2.604-2.729 3.343l-4.436-4.323c0.719-0.64 1.916-1.705 3.304-2.939l3.861 3.919zM15.489 23.183v-0.012c-2.575 9.88-14.018 4.2-14.018 4.2s4.801 0.605 4.801-3.873c0-4.341 4.412-4.733 4.683-4.753l4.543 4.427c0 0.001-0.009 0.011-0.009 0.011z"></path>
                    </svg>
                    <p class="mt-10 mb-4">Drawings</p>
                </button>
                <button class="search-box-button bg-blue-600 text-white pt-14 text-center flex flex-col items-center"
                        @click="filterByType('url')">
                    <svg class="icon icon-list2" viewBox="0 0 32 32">
                        <path d="M12 2h20v4h-20v-4zM12 14h20v4h-20v-4zM12 26h20v4h-20v-4zM0 4c0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.209-1.791 4-4 4s-4-1.791-4-4zM0 16c0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.209-1.791 4-4 4s-4-1.791-4-4zM0 28c0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.209-1.791 4-4 4s-4-1.791-4-4z"></path>
                    </svg>
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
