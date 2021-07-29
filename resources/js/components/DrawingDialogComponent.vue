<template>
    <div class="drawer flex flex-col" v-show="shown">
        <div class="top-bar flex flex-row justify-between bg-white">
            <div class="ml-3 my-3">
                <a href="#" @click.prevent class="p-2 pt-3 hover:bg-gray-100 mr-4">
                    <i class="bi bi-arrow-left text-gray-800" style="font-size: 1.5rem"></i>
                </a>

                <b-dropdown size="lg" variant="link" split toggle-class="text-decoration-none" no-caret class="p-0 mr-3">
                    <template #button-content>
                        <i class="bi bi-eraser-fill text-gray-800" style="font-size: 1.5rem"></i>
                    </template>

                    <b-dropdown-item href="#">Clear page</b-dropdown-item>
                </b-dropdown>

                <b-dropdown size="lg" variant="link" split toggle-class="text-decoration-none" no-caret class="p-0 mr-3">
                    <template #button-content>
                        <i class="bi bi-brush text-gray-800" style="font-size: 1.5rem"></i>
                    </template>

                    <b-dropdown-item href="#">Action</b-dropdown-item>
                    <b-dropdown-item href="#">Another action</b-dropdown-item>
                    <b-dropdown-item href="#">Something else here...</b-dropdown-item>
                </b-dropdown>

                <b-dropdown size="lg" variant="link" split toggle-class="text-decoration-none" no-caret class="p-0 mr-3">
                    <template #button-content>
                        <i class="bi bi-vector-pen text-gray-800" style="font-size: 1.5rem"></i>
                    </template>

                    <b-dropdown-item>Action</b-dropdown-item>
                </b-dropdown>

                <b-dropdown size="lg" variant="link" split toggle-class="text-decoration-none" no-caret class="p-0 mr-3">
                    <template #button-content>
                        <i class="bi bi-pen-fill text-gray-800" style="font-size: 1.5rem"></i>
                    </template>

                    <b-dropdown-item href="#">Action</b-dropdown-item>
                    <b-dropdown-item href="#">Another action</b-dropdown-item>
                    <b-dropdown-item href="#">Something else here...</b-dropdown-item>
                </b-dropdown>

                <b-dropdown size="lg" variant="link" split toggle-class="text-decoration-none" no-caret class="p-0">
                    <template #button-content>
                        <i class="bi bi-grid-3x3 text-gray-800" style="font-size: 1.5rem"></i>
                    </template>

                    <b-dropdown-item href="#">Action</b-dropdown-item>
                    <b-dropdown-item href="#">Another action</b-dropdown-item>
                    <b-dropdown-item href="#">Something else here...</b-dropdown-item>
                </b-dropdown>
            </div>
            <div class="mr-3 my-3 flex flex-row items-center">
                <a href="#" @click.prevent class="p-2 hover:bg-gray-100">
                    <i class="bi bi-arrow-counterclockwise text-gray-800" style="font-size: 1.5rem"></i>
                </a>

                <a href="#" @click.prevent class="p-2 hover:bg-gray-100">
                    <i class="bi bi-arrow-clockwise text-gray-800" style="font-size: 1.5rem"></i>
                </a>

                <b-dropdown size="sm" variant="link" toggle-class="text-decoration-none" no-caret>
                    <template #button-content>
                        <i class="bi bi-three-dots-vertical icon-sm p-2 hover:bg-gray-100"></i>
                    </template>
                    <b-dropdown-item href="#">New drawing</b-dropdown-item>
                    <b-dropdown-item href="#">Export as image</b-dropdown-item>
                    <b-dropdown-item href="#">Delete current drawing</b-dropdown-item>
                </b-dropdown>
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
