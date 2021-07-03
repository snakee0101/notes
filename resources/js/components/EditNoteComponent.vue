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

            <div class="images mt-4" v-if="encoded_images.length">
                <h6 class="pb-1">Note images (images are saved immediately)</h6>
                <div class="inline-block relative m-2" v-for="(encoded_image, index) in encoded_images">
                    <img :src="encoded_image" style="height: 120px; width: 120px">
                    <a class="bg-gray-300 rounded-full absolute top-1 left-1"
                       v-b-tooltip.hover.bottom
                       title="Delete image"
                       style="cursor: pointer"
                       @click.prevent="delete_image(index)">
                        <i class="bi bi-x icon"></i>
                    </a>
                </div>
                <p class="mt-2">
                    <button class="btn btn-success btn-sm">Add image</button>
                </p>
            </div>
        </div>
    </b-modal>
</template>

<script>
export default {

    //TODO: delete image
    //TODO: add image
    //TODO: reflect these changes in NoteComponent in container

    name: "EditNoteComponent",
    data() {
        return {
            note: {},
            header: '',
            body: '',
            encoded_images: []
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
            this.encoded_images = this.note.images.map(image => image.thumbnail_large_path);

            this.$refs["edit-note-modal"].show();
        },
        applyChanges() {
            this.body = this.$refs['note-editor'].value;
            console.log('apply');
        },
        cancel() {
            console.log('cancel');
        }
    }
}
</script>

<style scoped>

</style>
