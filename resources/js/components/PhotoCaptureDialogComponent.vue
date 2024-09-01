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
            <div class="mr-3 my-3">
                Select camera:
                <select name="camera_selection" id="selected_camera">
                    <option :value="videoDevice.deviceId" v-for="videoDevice in video_devices">{{ videoDevice.label }}</option>
                </select>
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
            canvas_width: 0,
            canvas_height: 0,
            target_note_component: null,
            target_note: null,
            is_photo_taken: false,
            video_settings: {},
            video_devices: [],
            saved_photo: null
        };
    },
    computed: {
    },
    watch: {
        is_photo_taken: function (newValue, oldValue) {

        }
    },
    created() {
        window.events.$on('show_photo_capture_dialog', this.open);
    },
    methods: {
        open(target_note_component, target_note = null) {
            this.target_note_component = target_note_component;
            this.target_note = target_note;

            this.is_photo_taken = false;
            this.shown = true;

            setTimeout(this.initialize, 50);
        },
        start_capture() {
            let video = document.querySelector("video");
            let canvas = document.querySelector("#photo_canvas");

            video.style.display = "inline-block";
            canvas.style.display = 'none';

            const constraints = {
                audio: false,
                video: true,
            };

            window.navigator.mediaDevices
                .getUserMedia(constraints)
                .then((mediaStream) => {
                    const video = document.querySelector("video");
                    video.srcObject = mediaStream;

                    video.onloadedmetadata = () => {
                        video.style.width = video.videoWidth + 'px';
                        video.style.height = video.videoHeight + 'px';
                        video.play();
                    };
                })
                .catch((err) => {
                    // always check for errors at the end.
                    console.error(`${err.name}: ${err.message}`);
                });
        },
        take_photo() {
            const video = document.querySelector("video");
            video.style.display = "none";

            let canvas = document.querySelector("#photo_canvas");
            canvas.style.display = 'inline-block';
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;

            video.style.width = video.videoWidth + 'px';
            video.style.height = video.videoHeight + 'px';

            let ctx = canvas.getContext('2d');
            ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

            canvas.toBlob(
                (image_data) => this.saved_photo = image_data,
                "image/jpeg", 1.0
            );
        },
        initialize() {
            this.canvas_width = window.innerWidth;

            let top_panel_height = document.querySelector('.top-bar').offsetHeight;
            let inner_area_height = window.innerHeight;
            this.canvas_height = inner_area_height - top_panel_height;

            this.canvas_ctx = this.$refs['drawing_area'].getContext('2d');
            this.canvas = this.$refs['drawing_area'];

            setTimeout(this.clearCanvas, 100)
        },
        clearCanvas() {
            /*if(this.drawing == null) {
                this.canvas_ctx.fillStyle = 'white';
                this.canvas_ctx.fillRect(0, 0, this.canvas_width, this.canvas_height);
            } else {
                this.canvas_ctx.clearRect(0, 0, this.canvas_width, this.canvas_height);

                var img = new Image;
                img.onload = () => this.canvas_ctx.drawImage(img,0,0);
                img.src = 'data:image/jpg;base64,' + this.drawing.image_encoded;
            }*/
        },
        close() {
            let canvas = document.querySelector("#photo_canvas");
            canvas.toBlob(
                (image_data) => window.events.$emit('autosave_photo', this.target_note_component, this.target_note, this.saved_photo),
                "image/jpeg", 1.0
            );

            this.shown = false;
        }
    }
}
</script>

<style scoped>
    video {
        display: inline-block;
        margin: auto;
        width: 70%;
    }
    .wrapper {
        background: #000;
        height: 100%;
    }
    canvas {
        display: none;
    }
</style>
