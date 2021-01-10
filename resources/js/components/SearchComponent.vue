<template>
    <section class="search bg-gray-100 flex flex-grow py-1 px-2 items-center rounded-lg" :class="isSearchFieldActive ? 'active' : ''" style="max-width: 730px">
        <!--TODO: Search box-->
        <!--TODO: When a user focuses Search box, the notes will disappear and instead of them there will be search filter sections-->
        <!--TODO: Search filter sections disappear only when user clicked the cross button-->
        <a href="" class="p-2 rounded-full hover:bg-gray-300">
            <svg class="icon icon-small icon-search" viewBox="0 0 32 32">
                <path d="M31.008 27.231l-7.58-6.447c-0.784-0.705-1.622-1.029-2.299-0.998 1.789-2.096 2.87-4.815 2.87-7.787 0-6.627-5.373-12-12-12s-12 5.373-12 12 5.373 12 12 12c2.972 0 5.691-1.081 7.787-2.87-0.031 0.677 0.293 1.515 0.998 2.299l6.447 7.58c1.104 1.226 2.907 1.33 4.007 0.23s0.997-2.903-0.23-4.007zM12 20c-4.418 0-8-3.582-8-8s3.582-8 8-8 8 3.582 8 8-3.582 8-8 8z"></path>
            </svg>
        </a>
        <input type="text" class="flex-grow focus:outline-none mx-2 bg-gray-100" placeholder="Search"
               @focus="inFocus()"
               @blur="inBlur()">
        <a href="" class="p-2 rounded-full hover:bg-gray-300"
           v-if="isSearchControlsShown"
           @click.prevent="clear()">
            <svg class="icon icon-small icon-close" viewBox="0 0 20 20">
                <path d="M10 8.586l-7.071-7.071-1.414 1.414 7.071 7.071-7.071 7.071 1.414 1.414 7.071-7.071 7.071 7.071 1.414-1.414-7.071-7.071 7.071-7.071-1.414-1.414-7.071 7.071z"></path>
            </svg>
        </a>
    </section>
</template>

<script>
export default {
    name: "SearchComponent",
    data: function () {
        return {
            isSearchFieldActive: false,
            isSearchControlsShown: false
        };
    },
    methods: {
        inFocus() {
            this.isSearchFieldActive = true;
            this.isSearchControlsShown = true;
        },
        inBlur() {
            this.isSearchFieldActive = false;
        },
        clear() {
            this.isSearchFieldActive = false;
            this.isSearchControlsShown = false;
        }
    }
}
</script>

<style scoped>

</style>
