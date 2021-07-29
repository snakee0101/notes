<template>
    <div class="drawer flex flex-col" v-show="shown">
        <div class="top-bar flex flex-row justify-between bg-white">
            <div class="ml-3 my-3">
                <a href="#" @click.prevent class="p-2 pt-3 hover:bg-gray-100 mr-4">
                    <i class="bi bi-arrow-left text-gray-800" style="font-size: 1.5rem"></i>
                </a>

                <b-dropdown size="lg" variant="link" split toggle-class="text-decoration-none" no-caret
                            class="p-0 mr-3">
                    <template #button-content>
                        <i class="bi bi-eraser-fill text-gray-800" style="font-size: 1.5rem"></i>
                    </template>

                    <b-dropdown-item href="#">Clear page</b-dropdown-item>
                </b-dropdown>

                <b-dropdown size="lg" variant="link" split toggle-class="text-decoration-none" no-caret
                            class="p-0 mr-3">
                    <template #button-content>
                        <i class="bi bi-brush text-gray-800" style="font-size: 1.5rem"></i>
                    </template>

                    <b-dropdown-item href="#">Action</b-dropdown-item>
                    <b-dropdown-item href="#">Another action</b-dropdown-item>
                    <b-dropdown-item href="#">Something else here...</b-dropdown-item>
                </b-dropdown>

                <b-dropdown size="lg" variant="link" split toggle-class="text-decoration-none" no-caret
                            class="p-0 mr-3">
                    <template #button-content>
                        <i class="bi bi-vector-pen text-gray-800" style="font-size: 1.5rem"></i>
                    </template>

                    <b-dropdown-item>Action</b-dropdown-item>
                </b-dropdown>

                <b-dropdown size="lg" variant="link" split toggle-class="text-decoration-none" no-caret
                            class="p-0 mr-3">
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
                    <div class="flex flex-row text-center">
                        <b-dropdown-item href="#" class="grid-dropdown-item" @click="setGrid('Square')">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48"
                                 style="user-select: none;">
                                <circle class="n0tgWb-LkdAo" cx="24" cy="24" r="23.5"
                                        style="user-select: none;"></circle>
                                <g class="rTEl-NkY1gc" style="user-select: none;">
                                    <line x1="12.33" y1="44.59" x2="12.33" y2="3.41" style="user-select: none;"></line>
                                    <line x1="24" y1="48" x2="24" style="user-select: none;"></line>
                                    <line x1="35.67" y1="44.59" x2="35.67" y2="3.41" style="user-select: none;"></line>
                                    <line x1="3.56" y1="12.33" x2="44.44" y2="12.33" style="user-select: none;"></line>
                                    <line y1="24" x2="48" y2="24" style="user-select: none;"></line>
                                    <line x1="3.56" y1="35.67" x2="44.44" y2="35.67" style="user-select: none;"></line>
                                </g>
                                <g class="check" style="user-select: none;">
                                    <circle cx="8" cy="8" r="6.33" style="user-select: none;"></circle>
                                    <path
                                        d="M8,0a8,8,0,1,0,8,8A8,8,0,0,0,8,0ZM6.4,12l-4-4L3.53,6.87,6.4,9.74l6.07-6.08L13.6,4.8Z"
                                        style="user-select: none;"></path>
                                </g>
                            </svg>
                            <br>Square
                        </b-dropdown-item>
                        <b-dropdown-item href="#" class="grid-dropdown-item" @click="setGrid('Dots')">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48"
                                 style="user-select: none;">
                                <circle class="n0tgWb-LkdAo" cx="24" cy="24" r="23.5"
                                        style="user-select: none;"></circle>
                                <g class="rTEl-NkY1gc" style="user-select: none;">
                                    <circle cx="11.37" cy="11.49" r="1.08" style="user-select: none;"></circle>
                                    <circle cx="24" cy="11.49" r="1.08" style="user-select: none;"></circle>
                                    <circle cx="36.63" cy="11.49" r="1.08" style="user-select: none;"></circle>
                                    <circle cx="11.37" cy="24" r="1.08" style="user-select: none;"></circle>
                                    <circle cx="24" cy="24" r="1.08" style="user-select: none;"></circle>
                                    <circle cx="36.63" cy="24" r="1.08" style="user-select: none;"></circle>
                                    <circle cx="24" cy="36.51" r="1.08" style="user-select: none;"></circle>
                                    <circle cx="11.37" cy="36.51" r="1.08" style="user-select: none;"></circle>
                                    <circle cx="36.63" cy="36.51" r="1.08" style="user-select: none;"></circle>
                                </g>
                                <g class="check" style="user-select: none;">
                                    <circle cx="8" cy="8" r="6.33" style="user-select: none;"></circle>
                                    <path
                                        d="M8,0a8,8,0,1,0,8,8A8,8,0,0,0,8,0ZM6.4,12l-4-4L3.53,6.87,6.4,9.74l6.07-6.08L13.6,4.8Z"
                                        style="user-select: none;"></path>
                                </g>
                            </svg>
                            <br>Dots
                        </b-dropdown-item>
                        <b-dropdown-item href="#" class="grid-dropdown-item" @click="setGrid('Rules')">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48"
                                 style="user-select: none;">
                                <circle class="n0tgWb-LkdAo" cx="24" cy="24" r="23.5"
                                        style="user-select: none;"></circle>
                                <g class="rTEl-NkY1gc" style="user-select: none;">
                                    <line x1="3.56" y1="12.33" x2="44.44" y2="12.33" style="user-select: none;"></line>
                                    <line y1="24" x2="48" y2="24" style="user-select: none;"></line>
                                    <line x1="3.56" y1="35.67" x2="44.44" y2="35.67" style="user-select: none;"></line>
                                </g>
                                <g class="check" style="user-select: none;">
                                    <circle cx="8" cy="8" r="6.33" style="user-select: none;"></circle>
                                    <path
                                        d="M8,0a8,8,0,1,0,8,8A8,8,0,0,0,8,0ZM6.4,12l-4-4L3.53,6.87,6.4,9.74l6.07-6.08L13.6,4.8Z"
                                        style="user-select: none;"></path>
                                </g>
                            </svg>
                            <br>Rules
                        </b-dropdown-item>
                        <b-dropdown-item href="#" class="grid-dropdown-item" @click="setGrid('None')">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48"
                                 style="user-select: none;">
                                <circle class="n0tgWb-LkdAo" cx="24" cy="24" r="23.5"
                                        style="user-select: none;"></circle>
                                <g class="rTEl-NkY1gc" style="user-select: none;"></g>
                                <g class="check" style="user-select: none;">
                                    <circle cx="8" cy="8" r="6.33" style="user-select: none;"></circle>
                                    <path
                                        d="M8,0a8,8,0,1,0,8,8A8,8,0,0,0,8,0ZM6.4,12l-4-4L3.53,6.87,6.4,9.74l6.07-6.08L13.6,4.8Z"
                                        style="user-select: none;"></path>
                                </g>
                            </svg>
                            <br>None
                        </b-dropdown-item>
                    </div>
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
        <canvas class="flex-grow" ref="drawing_area" id="canvas"></canvas>
    </div>
</template>

<script>
export default {
    name: "DrawingDialogComponent",
    data() {
        return {
            shown: false,
            canvas: {},
            grid: 'None'
        };
    },
    created() {
        window.events.$on('show_drawing_dialog', this.open);
    },
    methods: {
        setGrid(grid_type) {
            this.grid = grid_type;
        },
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
