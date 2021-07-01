<template>
    <b-modal id="edit-note-modal" title="Edit note" ref="edit-note-modal" centered
             v-on:ok="applyChanges()"
             v-on:cancel="cancel()"
             modal-class="edit-note-modal">
        <note-component :note="note" :isEditing="true" style="width: 630px">

        </note-component>
    </b-modal>
</template>

<script>
export default {
    name: "edit-note-component",
    data() {
        return {
            note: {}
        };
    },
    created() {
        window.events.$on('open_note_for_editing', this.openModal);
    },
    methods: {
        openModal(note) {
            this.note = note;
            this.$refs['edit-note-modal'].show();
        },
        applyChanges() {
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
