<template>
    <div class="image-viewer flex flex-col" @wheel="zoom()" v-if="shown">
        <div class="top-bar flex flex-row justify-between bg-black">
            <div class="ml-3 my-3">
                <a href="#" @click.prevent class="p-2 pt-3 hover:bg-gray-800">
                    <i class="bi bi-arrow-left text-white" style="font-size: 1.5rem"></i>
                </a>
            </div>
            <div class="mr-3 my-3">
                <a href="#" @click.prevent class="p-2 pt-3 hover:bg-gray-800">
                    <i class="bi bi-printer text-white" style="font-size: 1.5rem"></i>
                </a>

                <a href="#" @click.prevent class="p-2 pt-3 hover:bg-gray-800">
                    <i class="bi bi-pen text-white" style="font-size: 1.5rem"></i>
                </a>
            </div>
        </div>
        <div class="content flex-grow flex align-items-center justify-content-center bg-black overflow-hidden">
            <a href="#" @click.prevent class="absolute left-4 rounded-full">
                <i class="bi bi-arrow-left-circle text-white" style="font-size: 3rem"></i>
            </a>
            <img src="/storage/images/16253313246400548.png" style="width: 600px" ref="image"> <!--TODO: It is just a test image-->
            <a href="#" @click.prevent class="absolute right-4 rounded-full">
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
        shown: false
      };
    },
    methods: {
        zoom() {
            let image = this.$refs['image'];

            let aspectRatio = image.clientWidth/image.clientHeight;

            let newWidth = image.clientWidth - event.deltaY * aspectRatio;
            let newHeight = image.clientHeight - event.deltaY;

            if(newWidth < 400 || newWidth > image.naturalWidth)
                return;

            image.style.width = newWidth + 'px';
            image.style.height = newHeight + 'px';
        }
    }
}
</script>

<style scoped>

</style>
