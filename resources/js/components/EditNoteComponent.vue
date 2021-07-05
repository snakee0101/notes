<template>
    <b-modal id="edit-note-modal" title="Edit note" ref="edit-note-modal" centered
             v-on:ok="applyChanges()"
             v-on:cancel="cancel()"
             modal-class="edit-note-modal">

        <div class="p-4">
            <h3 class="pb-3">Edit note</h3>

            <h6 class="pb-1">Header</h6>
            <textarea name="note_header" placeholder="Title"
                      class="note-header-input mx-2 focus:outline-none h-auto resize-none font-bold bg-transparent text-xl"
                      v-model="header">

            </textarea>

            <h6 class="pb-1">Note content</h6>
            <div>
                <input type="hidden" id="note_content" v-model="note.body">
                <trix-editor input="note_content" ref="note-editor" @change="note.body = $event.target.value"></trix-editor>
            </div>

            <div class="images mt-4" v-if="images.length">
                <h6 class="pb-1">Note images (images are saved immediately)</h6>
                <div class="inline-block relative m-2" v-for="(image, index) in images">
                    <img :src="image.thumbnail_large_path" style="height: 120px; width: 120px; cursor: pointer" @click="openImageViewer(image)">
                    <a class="bg-gray-300 rounded-full absolute top-1 left-1"
                       v-b-tooltip.hover.bottom
                       title="Delete image"
                       style="cursor: pointer"
                       @click.prevent="delete_image(index)">
                        <i class="bi bi-x icon"></i>
                    </a>
                </div>
            </div>

            <p class="mt-2">
                <button class="btn btn-success btn-sm" @click="selectImage()">Add image</button>
                <input type="file" ref="image" class="hidden" accept="image/jpeg,image/png,image/gif"
                       @change="handleFile()">
            </p>
        </div>
    </b-modal>
</template>

<script>
export default {

    //TODO: reflect these changes in NoteComponent in container
    //TODO: image could be viewed in a full-screen mode

    name: "EditNoteComponent",
    data() {
        return {
            note: {},
            header: '',
            body: '',
            images: [],
        };
    },
    created() {
        window.events.$on('open_note_for_editing', this.openModal);
    },
    methods: {
        openModal(note) {
            this.note = JSON.parse(JSON.stringify(note));
            this.header = this.note.header;
            this.body = this.note.body;
            this.images = this.note.images_json;

            this.$refs["edit-note-modal"].show();
        },
        applyChanges() {
            this.body = this.$refs['note-editor'].value;
            console.log('apply');
        },
        cancel() {
            this.$refs["edit-note-modal"].hide();
        },
        selectImage() {
            this.$refs['image'].click();
        },
        handleFile() {
            let file = this.$refs['image'].files[0];
            const reader = new FileReader();

            reader.onloadend = () => {
                let image = this.$refs['image'].files[0];

                let data = new FormData();
                data.append('image', image, image.name);
                data.append('note_id', this.note.id);

                axios.post('/image', data).then( (res) => this.images.push(res.data) );
            };

            reader.readAsDataURL(file);
        },
        delete_image(index) {
            axios.post('/image/delete/' + this.images[index].id)
                 .then(res => this.deleteImageCallback(res.data, index));
        },
        deleteImageCallback(deleted_image_id, index) {
            window.deleted_image_id = deleted_image_id;

            this.images.splice(index, 1);
            window.events.$emit('show-notification', 'Image deleted', this.undoImageDeletion);
        },
        undoImageDeletion() {
            axios.put('/image/restore/' + window.deleted_image_id)
                 .then( (res) => this.images.push(res.data) );

            window.events.$emit('show-notification', 'Action undone');
        },
        openImageViewer(current_image) {
            window.events.$emit('open-image-viewer', current_image, this.images);
        }
    },
}
</script>

<style scoped>

</style>
