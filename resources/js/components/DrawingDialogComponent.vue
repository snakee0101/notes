<template>
    <div class="drawer flex flex-col" v-show="shown">
        <div class="top-bar flex flex-row justify-between bg-white">
            <div class="ml-3 my-3">
                <a href="#" @click.prevent class="p-2 pt-3 hover:bg-gray-100 mr-2">
                    <i class="bi bi-arrow-left text-gray-800" style="font-size: 1.5rem"></i>
                </a>

                <b-dropdown size="lg" variant="link" split toggle-class="text-decoration-none" no-caret class="p-0 mr-3">
                    <template #button-content>
                        &#x1f50d;<span class="sr-only">Search</span>
                    </template>

                    <b-dropdown-item href="#">Action</b-dropdown-item>
                    <b-dropdown-item href="#">Another action</b-dropdown-item>
                    <b-dropdown-item href="#">Something else here...</b-dropdown-item>
                </b-dropdown>

                <b-dropdown size="lg" variant="link" split toggle-class="text-decoration-none" no-caret class="p-0 mr-3">
                    <template #button-content>
                        &#x1f50d;<span class="sr-only">Search</span>
                    </template>

                    <b-dropdown-item href="#">Action</b-dropdown-item>
                    <b-dropdown-item href="#">Another action</b-dropdown-item>
                    <b-dropdown-item href="#">Something else here...</b-dropdown-item>
                </b-dropdown>

                <b-dropdown size="lg" variant="link" split toggle-class="text-decoration-none" no-caret class="p-0 mr-3">
                    <template #button-content>
                        &#x1f50d;<span class="sr-only">Search</span>
                    </template>

                    <b-dropdown-item href="#">Action</b-dropdown-item>
                    <b-dropdown-item href="#">Another action</b-dropdown-item>
                    <b-dropdown-item href="#">Something else here...</b-dropdown-item>
                </b-dropdown>

                <b-dropdown size="lg" variant="link" split toggle-class="text-decoration-none" no-caret class="p-0 mr-3">
                    <template #button-content>
                        &#x1f50d;<span class="sr-only">Search</span>
                    </template>

                    <b-dropdown-item href="#">Action</b-dropdown-item>
                    <b-dropdown-item href="#">Another action</b-dropdown-item>
                    <b-dropdown-item href="#">Something else here...</b-dropdown-item>
                </b-dropdown>

                <b-dropdown size="lg" variant="link" split toggle-class="text-decoration-none" no-caret class="p-0">
                    <template #button-content>
                        &#x1f50d;<span class="sr-only">Search</span>
                    </template>

                    <b-dropdown-item href="#">Action</b-dropdown-item>
                    <b-dropdown-item href="#">Another action</b-dropdown-item>
                    <b-dropdown-item href="#">Something else here...</b-dropdown-item>
                </b-dropdown>
            </div>
            <div class="mr-3 my-3">
                <a href="#" @click.prevent class="p-2 pt-3 hover:bg-gray-800">
                    <i class="bi bi-type text-white" style="font-size: 1.5rem"></i>
                </a>

                <a href="#" @click.prevent class="p-2 pt-3 hover:bg-gray-800">
                    <i class="bi bi-printer text-white" style="font-size: 1.5rem"></i>
                </a>

                <a href="#" @click.prevent class="p-2 pt-3 hover:bg-gray-800">
                    <i class="bi bi-pen text-white" style="font-size: 1.5rem"></i>
                </a>
            </div>
        </div>
        <canvas class="flex-grow" ref="drawing_area" id="canvas"> </canvas>
    </div>
</template>

<script>
export default {
    name: "DrawingDialogComponent",
    data() {
      return {
        shown: false,
        canvas: {}
      };
    },
    created() {
        window.events.$on('show_drawing_dialog', this.open);
    },
    methods: {
        initializeCanvas() {
            this.canvas = this.$refs['drawing_area'].getContext('2d');

            this.canvas.fillStyle = "#eee";
            this.canvas.rect(0, 0, window.innerWidth, this.$refs['drawing_area'].height);
            this.canvas.fill();
        },
        open() {
            this.shown = true;
            this.initializeCanvas();
        },
        close() {
            this.shown = false;
        }
    }
}
</script>

<style scoped>

</style>
