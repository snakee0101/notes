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
                        <i class="bi bi-brush-fill text-gray-800" style="font-size: 1.5rem" :style="'color:' + selected_brush_color"></i>
                    </template>

                    <div class="p-2 flex flex-row items-center">
                        <a href="#" v-for="color_1 in brush_colors[0]" class="color-circle" :class="(selected_brush_color === color_1) ? 'active' : ''" :style="'background:' + color_1" @click="selected_brush_color = color_1"></a>
                    </div>
                    <div class="p-2 flex flex-row items-center">
                        <a href="#" v-for="color_2 in brush_colors[1]" class="color-circle" :class="(selected_brush_color === color_2) ? 'active' : ''" :style="'background:' + color_2" @click="selected_brush_color = color_2"></a>
                    </div>
                    <div class="p-2 flex flex-row items-center">
                        <a href="#" v-for="color_3 in brush_colors[2]" class="color-circle" :class="(selected_brush_color === color_3) ? 'active' : ''" :style="'background:' + color_3" @click="selected_brush_color = color_3"></a>
                    </div>
                    <div class="p-2 flex flex-row items-center">
                        <a href="#" v-for="color_4 in brush_colors[3]" class="color-circle" :class="(selected_brush_color === color_4) ? 'active' : ''" :style="'background:' + color_4" @click="selected_brush_color = color_4"></a>
                    </div>
                    <div class="p-2 flex flex-row items-center border-t-2 border-gray-200 justify-center">
                        <a href="#" v-for="brush_size in brush_sizes" class="size-circle" :class="(selected_brush_size === brush_size) ? 'active' : ''" :style="'padding:' + brush_size + 'px'" @click="selected_brush_size = brush_size"></a>
                    </div>
                </b-dropdown>

                <b-dropdown size="lg" variant="link" split toggle-class="text-decoration-none" no-caret
                            class="p-0 mr-3">
                    <template #button-content>
                        <i class="bi bi-vector-pen text-gray-800" style="font-size: 1.5rem" :style="'color:' + selected_pen_color"></i>
                    </template>

                    <div class="p-2 flex flex-row items-center">
                        <a href="#" v-for="color_5 in brush_colors[0]" class="color-circle" :class="(selected_pen_color === color_5) ? 'active' : ''" :style="'background:' + color_5" @click="selected_pen_color = color_5"></a>
                    </div>
                    <div class="p-2 flex flex-row items-center">
                        <a href="#" v-for="color_6 in brush_colors[1]" class="color-circle" :class="(selected_pen_color === color_6) ? 'active' : ''" :style="'background:' + color_6" @click="selected_pen_color = color_6"></a>
                    </div>
                    <div class="p-2 flex flex-row items-center">
                        <a href="#" v-for="color_7 in brush_colors[2]" class="color-circle" :class="(selected_pen_color === color_7) ? 'active' : ''" :style="'background:' + color_7" @click="selected_pen_color = color_7"></a>
                    </div>
                    <div class="p-2 flex flex-row items-center">
                        <a href="#" v-for="color_8 in brush_colors[3]" class="color-circle" :class="(selected_pen_color === color_8) ? 'active' : ''" :style="'background:' + color_8" @click="selected_pen_color = color_8"></a>
                    </div>
                    <div class="p-2 flex flex-row items-center border-t-2 border-gray-200 justify-center">
                        <a href="#" v-for="pen_size in brush_sizes" class="size-circle" :class="(selected_pen_size === pen_size) ? 'active' : ''" :style="'padding:' + pen_size + 'px'" @click="selected_pen_size = pen_size"></a>
                    </div>
                </b-dropdown>

                <b-dropdown size="lg" variant="link" split toggle-class="text-decoration-none" no-caret
                            class="p-0 mr-3">
                    <template #button-content>
                        <i class="bi bi-pen-fill" style="font-size: 1.5rem" :style="'color:' + selected_marker_color"></i>
                    </template>

                    <div class="p-2 flex flex-row items-center">
                        <a href="#" v-for="color_9 in brush_colors[0]" class="color-circle" :class="(selected_marker_color === color_9) ? 'active' : ''" :style="'background:' + color_9" @click="selected_marker_color = color_9"></a>
                    </div>
                    <div class="p-2 flex flex-row items-center">
                        <a href="#" v-for="color_10 in brush_colors[1]" class="color-circle" :class="(selected_marker_color === color_10) ? 'active' : ''" :style="'background:' + color_10" @click="selected_marker_color = color_10"></a>
                    </div>
                    <div class="p-2 flex flex-row items-center">
                        <a href="#" v-for="color_11 in brush_colors[2]" class="color-circle" :class="(selected_marker_color === color_11) ? 'active' : ''" :style="'background:' + color_11" @click="selected_marker_color = color_11"></a>
                    </div>
                    <div class="p-2 flex flex-row items-center">
                        <a href="#" v-for="color_12 in brush_colors[3]" class="color-circle" :class="(selected_marker_color === color_12) ? 'active' : ''" :style="'background:' + color_12" @click="selected_marker_color = color_12"></a>
                    </div>
                    <div class="p-2 flex flex-row items-center border-t-2 border-gray-200 justify-center">
                        <a href="#" v-for="marker_size in brush_sizes" class="size-circle" :class="(selected_marker_size === marker_size) ? 'active' : ''" :style="'padding:' + marker_size + 'px'" @click="selected_marker_size = marker_size"></a>
                    </div>
                </b-dropdown>

                <b-dropdown size="lg" variant="link" split toggle-class="text-decoration-none" no-caret class="p-0">
                    <template #button-content>
                        <i class="bi bi-grid-3x3 text-gray-800" style="font-size: 1.5rem"></i>
                    </template>
                    <div class="flex flex-row text-center">
                        <b-dropdown-item href="#" class="grid-dropdown-item" @click="setGrid('Square')">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48"
                                 style="user-select: none;">
                                <circle :class="(grid === 'Square') ? 'grid-item-active' : ''" cx="24" cy="24" r="23.5"
                                        style="user-select: none"></circle>
                                <g class="rTEl-NkY1gc" style="user-select: none;">
                                    <line x1="12.33" y1="44.59" x2="12.33" y2="3.41" style="user-select: none;"></line>
                                    <line x1="24" y1="48" x2="24" style="user-select: none;"></line>
                                    <line x1="35.67" y1="44.59" x2="35.67" y2="3.41" style="user-select: none;"></line>
                                    <line x1="3.56" y1="12.33" x2="44.44" y2="12.33" style="user-select: none;"></line>
                                    <line y1="24" x2="48" y2="24" style="user-select: none;"></line>
                                    <line x1="3.56" y1="35.67" x2="44.44" y2="35.67" style="user-select: none;"></line>
                                </g>
                                <g class="check" style="user-select: none;" v-if="grid === 'Square'">
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
                                <circle :class="(grid === 'Dots') ? 'grid-item-active' : ''" cx="24" cy="24" r="23.5"
                                        style="user-select: none"></circle>
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
                                <g class="check" style="user-select: none;" v-if="grid === 'Dots'">
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
                                <circle :class="(grid === 'Rules') ? 'grid-item-active' : ''" cx="24" cy="24" r="23.5"
                                        style="user-select: none"></circle>
                                <g class="rTEl-NkY1gc" style="user-select: none;">
                                    <line x1="3.56" y1="12.33" x2="44.44" y2="12.33" style="user-select: none;"></line>
                                    <line y1="24" x2="48" y2="24" style="user-select: none;"></line>
                                    <line x1="3.56" y1="35.67" x2="44.44" y2="35.67" style="user-select: none;"></line>
                                </g>
                                <g class="check" style="user-select: none;" v-if="grid === 'Rules'">
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
                                <circle :class="(grid === 'None') ? 'grid-item-active' : ''" cx="24" cy="24" r="23.5"
                                        style="user-select: none"></circle>
                                <g class="rTEl-NkY1gc" style="user-select: none"></g>
                                <g class="check" style="user-select: none;" v-if="grid === 'None'">
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
        <canvas class="flex-grow" ref="drawing_area" id="canvas" :style="grid_style"></canvas>
    </div>
</template>

<script>
export default {
    name: "DrawingDialogComponent",
    data() {
        return {
            shown: false,
            canvas: {},
            grid: 'None',
            grid_style: '',
            brush_colors: [
                ['#000', '#ff5252', '#ffbc00', '#00c853', '#00b0ff', '#d500f9', '#8d6e63'],
                ['#fafafa', '#a52714', '#ee8100', '#558b2f', '#01579b', '#8e24aa', '#4e342e'],
                ['#90a4ae', '#ff4081', '#ff6e40', '#aeea00', '#304ffe', '#7c4dff', '#1de9b6'],
                ['#cfd8dc', '#f8bbd0', '#ffccbc', '#f0f4c3', '#9fa8da', '#d1c4e9', '#b2dfdb'],
            ],
            brush_sizes: [2, 4, 6, 8, 10, 12, 14],
            selected_brush_color: '#000',
            selected_pen_color: '#000',
            selected_marker_color: '#000',
            selected_brush_size: 2,
            selected_pen_size: 2,
            selected_marker_size: 2,
        };
    },
    created() {
        window.events.$on('show_drawing_dialog', this.open);
    },
    methods: {
        setGrid(grid_type) {
            this.grid = grid_type;

            let grid_styles = {
                'Square' : 'background-image: linear-gradient(rgb(221 221 221) .1em, transparent .1em), linear-gradient(90deg, rgb(221 221 221) .1em, transparent .1em); background-size: 5em 5em;',
                'Rules' : 'background-image: linear-gradient(rgb(170 202 237) .1em, transparent .1em), linear-gradient(rgb(170 202 237) .1em, transparent .1em); background-size: 5em 5em;',
                'None' : '',
                'Dots' : 'background-image: radial-gradient(circle at center, rgb(204,204,204) 0, rgb(204,204,204) 5px, #f7f7f7 5px, #f7f7f7 100%); background-repeat: repeat; background-position: left center; background-size: 5rem 5rem;'
            };

            this.grid_style = grid_styles[grid_type];
        },
        open() {
            this.shown = true;
            this.canvas = this.$refs['drawing_area'].getContext('2d');
        },
        close() {
            this.shown = false;
        }
    }
}
</script>

<style scoped>

</style>
