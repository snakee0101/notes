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
        </div>
    </b-modal>
</template>

<script>
export default {
    name: "EditNoteComponent",
    data() {
        return {
            note: {},
            header: '',
            body: ''
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
