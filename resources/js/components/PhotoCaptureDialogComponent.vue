<template>
    <div class="drawer" v-show="shown">
        <div class="top-bar flex flex-row justify-between bg-white">
            <div class="ml-3 my-3">
                <button @click="close()">Go back & save</button>
            </div>
            <div class="mr-3 my-3 flex flex-row items-center">
                <button class="btn btn-warning" @click="start_capture()">Start capture</button>
                <button class="btn btn-warning" @click="take_photo()">Take photo</button>
            </div>
        </div>

        <div class="wrapper">
            <video autoplay></video>
            <canvas id="photo_canvas"></canvas>
        </div>
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
            video: null,
            target_note_component: null,
            target_note: null,
            saved_photo: null
        };
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
        },
        start_capture() {
            this.toggle_preview(false);

            window.navigator.mediaDevices
                .getUserMedia({ audio: false, video: true })
                .then((mediaStream) => this.video.srcObject = mediaStream );
        },
        take_photo() {
            this.toggle_preview(true);
            this.resize_video();

            this.canvas_ctx.drawImage(this.video, 0, 0, this.canvas.width, this.canvas.height);
            this.canvas.toBlob(
                (image_data) => this.saved_photo = image_data,
                "image/jpeg", 1.0
            );
        },
        toggle_preview(is_visible) {
            this.video.style.display = is_visible ? 'none' : 'block';
            this.canvas.style.display = is_visible ? 'block' : 'none';
        },
        resize_video() {
            this.canvas.width = this.video.videoWidth;
            this.canvas.height = this.video.videoHeight;

            this.video.style.width = this.video.videoWidth + 'px';
            this.video.style.height = this.video.videoHeight + 'px';
        },
        initialize() {
            this.canvas = document.querySelector("#photo_canvas");
            this.canvas_ctx = document.querySelector("#photo_canvas").getContext('2d');
            this.video = document.querySelector("video");
        },
        close() {
            window.events.$emit('autosave_photo', this.target_note_component, this.target_note, this.saved_photo);

            this.shown = false;
        }
    }
}
</script>

<style scoped>
    video {
        display: block;
        margin: auto;
    }
    .wrapper {
        background: #000;
        height: 100%;
    }
    canvas {
        display: none;
        margin: auto;
    }
</style>
