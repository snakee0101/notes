<template>
    <div class="drawer" v-show="shown">
        <div class="top-bar flex flex-row justify-between bg-white">
            <div class="ml-3 my-3">
                <a href="#" @click.prevent="close()" class="p-2 pt-3 hover:bg-gray-100 mr-4">
                    <i class="bi bi-arrow-left text-gray-800" style="font-size: 1.5rem"></i>
                </a>

                <b-dropdown size="lg" variant="link" split toggle-class="text-decoration-none" no-caret
                            class="p-0 mr-3" @click="setTool('eraser')">
                    <template #button-content>
                        <i class="bi bi-eraser-fill text-gray-800" style="font-size: 1.5rem"
                           :style="'color:' + displayed_eraser_color"></i>
                    </template>

                    <b-dropdown-item href="#" @click="clearCanvas">Clear page</b-dropdown-item>
                </b-dropdown>

                <b-dropdown size="lg" variant="link" split toggle-class="text-decoration-none" no-caret
                            class="p-0 mr-3" @click="setTool('brush')">
                    <template #button-content>
                        <i class="bi bi-brush-fill text-gray-800" style="font-size: 1.5rem"
                           :style="'color:' + displayed_brush_color"></i>
                    </template>

                    <div class="p-2 flex flex-row items-center">
                        <a href="#" v-for="color_1 in brush_colors[0]" class="color-circle" :class="(selected_brush_color === color_1) ? 'active' : ''" :style="'background:' + color_1" @click="setToolByOption('brush', 'color', color_1)"></a>
                    </div>
                    <div class="p-2 flex flex-row items-center">
                        <a href="#" v-for="color_2 in brush_colors[1]" class="color-circle" :class="(selected_brush_color === color_2) ? 'active' : ''" :style="'background:' + color_2" @click="setToolByOption('brush', 'color', color_2)"></a>
                    </div>
                    <div class="p-2 flex flex-row items-center">
                        <a href="#" v-for="color_3 in brush_colors[2]" class="color-circle" :class="(selected_brush_color === color_3) ? 'active' : ''" :style="'background:' + color_3" @click="setToolByOption('brush', 'color', color_3)"></a>
                    </div>
                    <div class="p-2 flex flex-row items-center">
                        <a href="#" v-for="color_4 in brush_colors[3]" class="color-circle" :class="(selected_brush_color === color_4) ? 'active' : ''" :style="'background:' + color_4" @click="setToolByOption('brush', 'color', color_4)"></a>
                    </div>
                    <div class="p-2 flex flex-row items-center border-t-2 border-gray-200 justify-center">
                        <a href="#" v-for="brush_size in brush_sizes" class="size-circle" :class="(selected_brush_size === brush_size) ? 'active' : ''" :style="{'padding': brush_size / 2.4 + 'px'}" @click="setToolByOption('brush', 'size', brush_size)"></a>
                    </div>
                </b-dropdown>

                <b-dropdown size="lg" variant="link" split toggle-class="text-decoration-none" no-caret
                            class="p-0 mr-3" @click="setTool('pen')">
                    <template #button-content>
                        <i class="bi bi-vector-pen text-gray-800" style="font-size: 1.5rem"
                           :style="'color:' + displayed_pen_color"></i>
                    </template>

                    <div class="p-2 flex flex-row items-center">
                        <a href="#" v-for="color_5 in brush_colors[0]" class="color-circle" :class="(selected_pen_color === color_5) ? 'active' : ''" :style="'background:' + color_5" @click="setToolByOption('pen', 'color', color_5)"></a>
                    </div>
                    <div class="p-2 flex flex-row items-center">
                        <a href="#" v-for="color_6 in brush_colors[1]" class="color-circle" :class="(selected_pen_color === color_6) ? 'active' : ''" :style="'background:' + color_6" @click="setToolByOption('pen', 'color', color_6)"></a>
                    </div>
                    <div class="p-2 flex flex-row items-center">
                        <a href="#" v-for="color_7 in brush_colors[2]" class="color-circle" :class="(selected_pen_color === color_7) ? 'active' : ''" :style="'background:' + color_7" @click="setToolByOption('pen', 'color', color_7)"></a>
                    </div>
                    <div class="p-2 flex flex-row items-center">
                        <a href="#" v-for="color_8 in brush_colors[3]" class="color-circle" :class="(selected_pen_color === color_8) ? 'active' : ''" :style="'background:' + color_8" @click="setToolByOption('pen', 'color', color_8)"></a>
                    </div>
                    <div class="p-2 flex flex-row items-center border-t-2 border-gray-200 justify-center">
                        <a href="#" v-for="pen_size in brush_sizes" class="size-circle" :class="(selected_pen_size === pen_size) ? 'active' : ''" :style="{'padding': pen_size / 2.4 + 'px'}" @click="setToolByOption('pen', 'size', pen_size)"></a>
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
        <canvas ref="drawing_area" id="canvas"
                v-bind:style="canvas_style"
                :width="canvas_width"
                :height="canvas_height"
                @mousemove="draw"
                @mouseenter="initialize_mouse_position"
                @click="drawInitialShape"></canvas>
        <svg ref="cursor_svg" xmlns="http://www.w3.org/2000/svg" v-bind:width="selected_tool_size" v-bind:height="selected_tool_size" viewBox="0 0 12 12">
            <circle cx="6" cy="6" r="5" stroke="rgb(0,0,0)" v-bind:fill="selected_tool_color"/>
        </svg>
    </div>
</template>

<script>
export default {
    name: "DrawingDialogComponent",
    data() {
        return {
            shown: false,
            grid: 'None',
            grid_style: {
                image: '',
                size: '',
                repeat_style: '',
                position: ''
            },
            canvas: null,
            canvas_ctx: null,
            canvas_width: 0,
            canvas_height: 0,
            opacity: "/ 100%",
            brush_sizes: [2, 4, 8, 12, 16, 20, 24, 30],
            selected_brush_color: 'rgb(0,0,0)',
            selected_pen_color: 'rgb(0,0,0)',
            selected_brush_size: 2,
            selected_pen_size: 2,
            selected_tool_size: 2,
            selected_tool_color: 'rgb(0,0,0)',
            tool: 'brush',
            last_mouse_position: {
                x: 0,
                y: 0
            },
            target_note_component: null,
            target_note: null
        };
    },
    computed: {
        displayed_eraser_color() {
            return (this.tool === 'eraser') ? '#000' : '#90a4ae';
        },
        displayed_brush_color() {
            return (this.tool === 'brush') ? this.selected_brush_color : '#90a4ae';
        },
        displayed_pen_color() {
            return (this.tool === 'pen') ? this.selected_pen_color : '#90a4ae';
        },
        canvas_style() {
            let style = {};

            style.backgroundImage = this.grid_style.image;
            style.backgroundSize = this.grid_style.size;
            style.backgroundRepeat = this.grid_style.repeat_style;
            style.backgroundPosition = this.grid_style.position;

            return style;
        },
        brush_colors() {
            return [
                [`rgb(0 0 0 ${this.opacity})`, `rgb(255 82 82 ${this.opacity})`, `rgb(255 188 0 ${this.opacity})`, `rgb(0 200 83 ${this.opacity})`, `rgb(0 176 255 ${this.opacity})`, `rgb(213 0 249 ${this.opacity})`, `rgb(141 110 99 ${this.opacity})`],
                [`rgb(250 250 250 ${this.opacity})`, `rgb(165 39 20 ${this.opacity})`, `rgb(238 129 0 ${this.opacity})`, `rgb(85 139 47 ${this.opacity})`, `rgb(1 87 155 ${this.opacity})`, `rgb(142 36 170 ${this.opacity})`, `rgb(78 52 46 ${this.opacity})`],
                [`rgb(144 164 174 ${this.opacity})`, `rgb(255 64 129 ${this.opacity})`, `rgb(255 110 64 ${this.opacity})`, `rgb(174 234 0 ${this.opacity})`, `rgb(48 79 254 ${this.opacity})`, `rgb(124 77 255 ${this.opacity})`, `rgb(29 233 182 ${this.opacity})`],
                [`rgb(207 216 220 ${this.opacity})`, `rgb(248 187 208 ${this.opacity})`, `rgb(255 204 188 ${this.opacity})`, `rgb(240 244 195 ${this.opacity})`, `rgb(159 168 218 ${this.opacity})`, `rgb(209 196 233 ${this.opacity})`, `rgb(178 223 219 ${this.opacity})`],
            ];
        }
    },
    watch: {
        selected_tool_color(newValue, oldValue) {
            this.canvas_ctx.strokeStyle = newValue;
            setTimeout(() => document.getElementById('canvas').style.cursor = 'url(\'data:image/svg+xml;utf8,' + this.$refs['cursor_svg'].outerHTML + '\'), auto', 50);
        },
        selected_tool_size(newValue, oldValue) {
            this.canvas_ctx.lineWidth = newValue;
            setTimeout(() => document.getElementById('canvas').style.cursor = 'url(\'data:image/svg+xml;utf8,' + this.$refs['cursor_svg'].outerHTML + '\'), auto', 50);
        },
    },
    created() {
        window.events.$on('show_drawing_dialog', this.open);
    },
    methods: {
        open(target_note_component, target_note = null) {
            this.target_note_component = target_note_component;
            this.target_note = target_note;

            this.shown = true;

            setTimeout(this.initialize, 50);
            this.setDefaultTool();
        },
        setTool(tool) {
            this.tool = tool;

            this.selected_tool_color = this['selected_' + tool + '_color'];
            this.selected_tool_size = this['selected_' + tool + '_size'];

            setTimeout(() => document.getElementById('canvas').style.cursor = 'url(\'data:image/svg+xml;utf8,' + this.$refs['cursor_svg'].outerHTML + '\'), auto', 50);
        },
        setToolByOption(tool, option, option_value) {
            this['selected_' + tool + '_' + option] = option_value;

            this.setTool(tool);
        },
        setGrid(grid_type) {
            this.grid = grid_type;

           /* let grid_styles =
                'Square' : {
                    image: 'linear-gradient(rgb(221 221 221) .1em, transparent .1em), linear-gradient(90deg, rgb(221 221 221) .1em, transparent .1em)',
                    size: '5em 5em',
                    repeat_style: 'repeat',
                    position: 'left top'
                },
                'Dots' : {
                    image: 'radial-gradient(circle at center, rgb(204,204,204) 0, rgb(204,204,204) 5px, #f7f7f7 5px, #f7f7f7 100%)',
                    size: '5rem 5rem',
                    repeat_style: 'repeat',
                    position: 'left center'
                },
                'Rules' : {
                    image: 'linear-gradient(rgb(170 202 237) .1em, transparent .1em), linear-gradient(rgb(170 202 237) .1em, transparent .1em)',
                    size: '5em 5em',
                    repeat_style: 'repeat',
                    position: 'left center'
                },
                'None' : {
                    image: '',
                    size: '',
                    repeat_style: '',
                    position: ''
                }
            };*/

            if(grid_type == 'Square') {
                let padding = 10;
                let line_gap = 80;

                for (var x = -15; x <= this.canvas_width; x += line_gap) { // negative offset - because grid should have no borders - it must be "infinite"
                    this.canvas_ctx.moveTo(0.5 + x + padding, padding);
                    this.canvas_ctx.lineTo(0.5 + x + padding, this.canvas_height + padding);
                }

                for (var y = -15; y <= this.canvas_height; y += line_gap) { // negative offset - because grid should have no borders - it must be "infinite"
                    this.canvas_ctx.moveTo(padding, 0.5 + y + padding);
                    this.canvas_ctx.lineTo(this.canvas_width + padding, 0.5 + y + padding);
                }

                this.canvas_ctx.strokeStyle = "black";
                this.canvas_ctx.stroke();
            }

            this.grid_style = grid_styles[grid_type];
        },
        initialize() {
            this.canvas_width = window.innerWidth;

            let top_panel_height = document.querySelector('.top-bar').offsetHeight;
            let inner_area_height = window.innerHeight;
            this.canvas_height = inner_area_height - top_panel_height;

            if(this.canvas == null){
                this.canvas_ctx = this.$refs['drawing_area'].getContext('2d');
                this.canvas = this.$refs['drawing_area'];

                setTimeout(this.clearCanvas, 100)
            }
        },
        clearCanvas() {
            this.canvas_ctx.fillStyle = 'white';
            this.canvas_ctx.fillRect(0, 0, this.canvas_width, this.canvas_height);
        },
        setDefaultTool() {
            this.setToolByOption('brush', 'color', 'rgb(0,0,0)');
            this.setToolByOption('brush', 'size', 2);
        },
        close() {
            this.shown = false;

            this.canvas.toBlob(
                (image_data) => {
                    window.events.$emit('autosave_drawing', this.target_note_component, this.target_note, image_data);
                }, "image/jpeg", 1.0
            );

            this.clearCanvas();
        },
        initialize_mouse_position(event) {
            this.last_mouse_position.x = event.offsetX;
            this.last_mouse_position.y = event.offsetY;
        },
        drawInitialShape(event) {
            this.canvas_ctx.beginPath();
            this.canvas_ctx.arc(event.offsetX, event.offsetY, this.selected_tool_size / 2, 0, 2 * Math.PI);
            this.canvas_ctx.fillStyle = this.selected_tool_color;
            this.canvas_ctx.fill();
            this.canvas_ctx.closePath();
        },
        draw(event) {
            if(event.buttons === 1) { //draw only if left button is pressed
                this.canvas_ctx.beginPath();
                this.canvas_ctx.moveTo(this.last_mouse_position.x, this.last_mouse_position.y);   //move to last mouse position
                this.canvas_ctx.lineTo(event.offsetX, event.offsetY);  //and create a line to current mouse position
                this.canvas_ctx.lineCap = 'round';
                this.canvas_ctx.stroke();  //draw a line
                this.canvas_ctx.closePath();
            }

            //it doesn't matter whether we are drawing or not - we must remember last mouse position
            this.last_mouse_position.x = event.offsetX;
            this.last_mouse_position.y = event.offsetY;
        }
    }
}
</script>

<style scoped>

</style>
