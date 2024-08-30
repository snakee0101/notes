<template>
    <div class="drawer" v-show="shown">
        <div class="top-bar flex flex-row justify-between bg-white">
            <div class="ml-3 my-3">
                <a href="#" @click.prevent="close()" class="p-2 pt-3 hover:bg-gray-100 mr-4">
                    <i class="bi bi-arrow-left text-gray-800" style="font-size: 1.5rem"></i>
                </a>
            </div>
            <div class="mr-3 my-3 flex flex-row items-center">
                <button class="btn btn-warning">Take photo</button>
            </div>
        </div>
        <canvas ref="drawing_area" id="canvas"
                :width="canvas_width"
                :height="canvas_height"></canvas>
    </div>
</template>

<script>
export default {
    name: "PhotoCaptureDialogComponent",
    data() {
        return {
            shown: false,
            canvas: null,
            canvas_ctx: null,
            canvas_width: 0,
            canvas_height: 0,
            target_note_component: null,
            target_note: null
        };
    },
    computed: {
    },
    watch: {
    },
    created() {
        window.events.$on('show_photo_capture_dialog', this.open);
    },
    methods: {
        open(target_note_component, target_note = null) {
            this.target_note_component = target_note_component;
            this.target_note = target_note;

            this.shown = true;

            setTimeout(this.initialize, 50);
            this.setDefaultTool();
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
            if(this.drawing == null) {
                this.canvas_ctx.fillStyle = 'white';
                this.canvas_ctx.fillRect(0, 0, this.canvas_width, this.canvas_height);
            } else {
                this.canvas_ctx.clearRect(0, 0, this.canvas_width, this.canvas_height);

                var img = new Image;
                img.onload = () => this.canvas_ctx.drawImage(img,0,0);
                img.src = 'data:image/jpg;base64,' + this.drawing.image_encoded;
            }
        },
        close() {
            this.shown = false;

            this.canvas.toBlob(
                (image_data) => {
                    window.events.$emit('autosave_drawing', this.target_note_component, this.target_note, image_data, this.drawing);
                }, "image/jpeg", 1.0
            );

            this.clearCanvas();
        }
    }
}
</script>

<style scoped>

</style>
