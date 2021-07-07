<template>
    <div class="image-viewer flex flex-col" @wheel="zoom()" v-if="shown">
        <div class="top-bar flex flex-row justify-between bg-black">
            <div class="ml-3 my-3">
                <a href="#" @click.prevent="close()" class="p-2 pt-3 hover:bg-gray-800">
                    <i class="bi bi-arrow-left text-white" style="font-size: 1.5rem"></i>
                </a>
            </div>
            <div class="mr-3 my-3">
                <a href="#" @click.prevent="recognizeText()" class="p-2 pt-3 hover:bg-gray-800">
                    <i class="bi bi-type text-white" style="font-size: 1.5rem"></i>
                </a>

                <a href="#" @click.prevent="print()" class="p-2 pt-3 hover:bg-gray-800">
                    <i class="bi bi-printer text-white" style="font-size: 1.5rem"></i>
                </a>

                <a href="#" @click.prevent="edit()" class="p-2 pt-3 hover:bg-gray-800">
                    <i class="bi bi-pen text-white" style="font-size: 1.5rem"></i>
                </a>
            </div>
        </div>
        <div class="content flex-grow flex align-items-center justify-content-center overflow-hidden"
             style="background: rgba(0,0,0,0.85)"
             @click.self="close()">
            <a href="#" @click.prevent="prev()" class="absolute left-4 rounded-full" v-if="prev_shown">
                <i class="bi bi-arrow-left-circle text-white" style="font-size: 3rem"></i>
            </a>
            <img :src="current_image.image_path" style="width: 600px" ref="image">
            <a href="#" @click.prevent="next()" class="absolute right-4 rounded-full" v-if="next_shown">
                <i class="bi bi-arrow-right-circle text-white" style="font-size: 3rem"></i>
            </a>
        </div>
    </div>
</template>

<script>
export default {
    name: "ImageViewerComponent",
    data() {
      return {
        shown: false,
        prev_shown: true,
        next_shown: true,
        current_image: {},
        images: []
      };
    },
    created() {
        window.events.$on('open-image-viewer', this.open);
    },
    methods: {
        open(current_image, images) {
            this.current_image = current_image;
            this.images = images;

            let index = images.indexOf(current_image);

            this.prev_shown = (index - 1 >= 0); //if the previous image exists - show prev button
            this.next_shown = (index + 1 <= images.length - 1); //if the next image exists - show next button

            this.shown = true;
        },
        zoom() {
            let image = this.$refs['image'];

            let aspectRatio = image.clientWidth/image.clientHeight;

            let newWidth = image.clientWidth - event.deltaY * aspectRatio;
            let newHeight = image.clientHeight - event.deltaY;

            if(newWidth < 400 || newWidth > image.naturalWidth)
                return;

            image.style.width = newWidth + 'px';
            image.style.height = newHeight + 'px';
        },
        close() {
            this.shown = false;
        },
        print() {
            let win = window.open(this.current_image.image_path, '__blank', 'visible=none');

            win.addEventListener('afterprint', event => event.target.close() );
            win.print();
        },
        edit() {
            alert('edit'); //TODO: Edit
        },
        prev() { //TODO: when loading - width should be original or maximum allowed
            let index = this.images.indexOf(this.current_image);
            this.current_image = this.images[index - 1];

            this.next_shown = true;

            if(index - 2 < 0) //if it was the second image - the previous image of first image is not exists - then prev button must be hidden
                this.prev_shown = false;
        },
        next() {
            let index = this.images.indexOf(this.current_image);
            this.current_image = this.images[index + 1];

            this.prev_shown = true;

            if(index + 2 > this.images.length - 1) //next button should be hidden, if second image to current does not exists
                this.next_shown = false;
        },
        recognizeText() {
            axios.post('/image/recognize', {
                'image_path' : this.current_image.image_path
            }).then(function(res) {
                console.log(res.data);
            });

            //TODO: copy it to clipboard
            //TODO: show notification "recognized text was copied to clipboard"
        }
    }
}
</script>

<style scoped>

</style>
