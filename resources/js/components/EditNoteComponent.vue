<template>
    <b-modal id="edit-note-modal" title="Edit note" ref="edit-note-modal" centered v-on:ok="applyChanges()" v-on:cancel="cancel()">

    </b-modal>
</template>

<script>
export default {
    name: "edit-note-component",
    created() {
        window.events.$on('open_note_for_editing', this.openModal);
    },
    methods: {
        openModal() {
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
