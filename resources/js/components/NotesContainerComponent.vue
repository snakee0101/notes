<template>
    <div v-masonry="app" transition-duration="0.3s" item-selector=".note" gutter=".gutter">
        <div class="gutter"></div>

        <note-component v-masonry-tile
                        v-for="noteObject in notesCollection"
                        :key="noteObject.id"
                        :note="noteObject"
                        :isTrashed="is_trashed">

        </note-component>
    </div>
</template>

<script>
    export default {
        props: ['notes', 'isTrashed'],
        data() {
            return {
                notesCollection: this.notes,
                is_trashed: !!(this.isTrashed)
            };
        },
        name: "NotesContainerComponent"
    }
</script>

<style scoped>
    .gutter {
        width: 10px;
        height: 10px;
    }
</style>
