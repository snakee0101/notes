<template>
    <div class="image-viewer flex flex-col" @wheel="zoom_on_mouse_wheel($event.deltaY)" v-if="shown">
        <div class="top-bar flex flex-row justify-between bg-black">
            <div class="ml-3 my-3">
                <a href="#" @click.prevent="close()" class="icon-link">
                    <i class="bi-arrow-left"></i>
                </a>
            </div>
            <div class="mr-3 my-3">
                <a href="#" @click.prevent="recognizeText()" class="icon-link" title="recognize text">
                    <i class="bi-type"></i>
                </a>

                <a href="#" @click.prevent="print()" class="icon-link" title="print">
                    <i class="bi-printer"></i>
                </a>

                <a href="#" @click.prevent="edit()" class="icon-link" tilte="edit image">
                    <i class="bi-pen"></i>
                </a>
            </div>
        </div>
        <div class="image-container" @click.self="close()">
            <a href="#" @click.prevent="prev()" class="slider_controls left-4" v-if="prev_shown">
                <i class="bi bi-arrow-left-circle text-white" style="font-size: 3rem"></i>
            </a>
            <img :src="'data:image/jpg;base64,' + current_image.image_encoded" ref="image">
            <a href="#" @click.prevent="next()" class="slider_controls right-4" v-if="next_shown">
                <i class="bi bi-arrow-right-circle text-white" style="font-size: 3rem"></i>
            </a>
        </div>

        <div class="flex flex-row bg-black justify-center absolute bottom-6 left-1/2">
            <div class="inline bg-black flex flex-row align-items-stretch">
                <a href="#" @click.prevent="zoomIn()" class="icon-link">
                    <i class="bi-zoom-in"></i>
                </a>

                <a href="#" @click.prevent="resetZoom()" class="hover:bg-gray-800 text-white pt-3">
                    {{ scaleRatio }}%
                </a>

                <a href="#" @click.prevent="zoomOut()" class="icon-link">
                    <i class="bi-zoom-out"></i>
                </a>
            </div>
        </div>
    </div>
</template>

<style scoped>
.image-container {
    align-items: center;
    background: rgba(0,0,0,0.85);
    display: flex;
    flex-grow: 1;
    justify-content: center;
    overflow: hidden;
}

.icon-link {
    @apply p-2 pt-3 hover:bg-gray-800;
}

.icon-link i {
    @apply text-white;
    font-size: 1.5rem;
}

.slider_controls {
    @apply absolute rounded-full;
    z-index: 1;
}

img {
    transition: .1s;
}
</style>


<script>
export default {
    name: "ImageViewerComponent",
    data() {
      return {
        shown: false,
        prev_shown: true,
        next_shown: true,
        current_image: {},
        images: [],
        scale: 1.0,
        zoom_delta: 0.1
      };
    },
    computed: {
        scaleRatio() {
            return Math.round(this.scale * 100);
        }
    },
    watch: {
        scale(newScale, oldScale) {
            let image = this.$refs['image'];

            image.style.transform = 'scale(' + newScale + ')';
        }
    },
    created() {
        window.events.$on('open-image-viewer', this.open);
    },
    methods: {
        exceedsOriginalSizeOnZoom(image) {
            let actualWidth = image.clientWidth * (this.scale + this.zoom_delta);
            let actualHeight = image.clientHeight * (this.scale + this.zoom_delta);

            return actualWidth > image.naturalWidth || actualHeight > image.naturalHeight;
        },
        zoomIn() {
            if ( this.exceedsOriginalSizeOnZoom(this.$refs['image']) ) {
                return;
            }

            this.scale += this.zoom_delta;
        },
        zoomOut() { // You should not zoom out infinitely (minimum scale is 100%)
            if (this.scale == 1.0)
                return;

            this.scale -= 0.1;
        },
        resetZoom() {
            this.scale = 1;
        },
        open(current_image, images) {
            this.scale = 1;

            this.current_image = current_image;
            this.images = images;

            let index = images.indexOf(current_image);

            this.prev_shown = (index - 1 >= 0); //if the previous image exists - show prev button
            this.next_shown = (index + 1 <= images.length - 1); //if the next image exists - show next button

            this.shown = true;
        },
        /**
         * I need only a sign of deltaY (+, -) to determine direction - zoom-in or zoom-out.
         * * "-" means "scroll Up" - which must "zoom in" the image;  "+" means "scroll Down" - which must "zoom out" the image.
         * Zoom ratio itself is determined by zoomIn() and zoomOut() methods
         * */
        zoom_on_mouse_wheel(deltaY) {
            deltaY < 0 ? this.zoomIn() : this.zoomOut();
        },
        close() {
            this.shown = false;
        },
        print() {
            let win = window.open(this.current_image.image_encoded, '__blank', 'visible=none');

            win.addEventListener('afterprint', event => event.target.close() );
            win.print();
        },
        edit() {
            alert('edit'); //TODO: Edit
        },
        prev() { //TODO: when loading - width should be original or maximum allowed
            this.scale = 1;

            let index = this.images.indexOf(this.current_image);
            this.current_image = this.images[index - 1];

            this.next_shown = true;

            if(index - 2 < 0) //if it was the second image - the previous image of first image is not exists - then prev button must be hidden
                this.prev_shown = false;
        },
        next() {
            this.scale = 1;

            let index = this.images.indexOf(this.current_image);
            this.current_image = this.images[index + 1];

            this.prev_shown = true;

            if(index + 2 > this.images.length - 1) //next button should be hidden, if second image to current does not exists
                this.next_shown = false;
        },
        recognizeText() {
            navigator.clipboard.writeText(this.current_image.recognized_text).then(function() {
                window.events.$emit('show-notification', 'Recognized text was copied to clipboard');
            });
        }
    }
}
</script>
