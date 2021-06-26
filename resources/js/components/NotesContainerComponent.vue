<template>
    <div>
        <div class="pinned">
            <p class="font-bold text-sm mb-2">PINNED</p>

            <div v-masonry transition-duration="0.3s" item-selector=".note" gutter=".gutter" :origin-top="true">
                <div class="gutter"></div>

                <note-component v-masonry-tile
                                v-for="note in pinned_notes"
                                :key="note.id"
                                :note="note"
                                :isTrashed="isTrashed">

                    </note-component>
            </div>
        </div>

        <div class="others">
            <p class="font-bold text-sm mt-20 mb-2">OTHERS</p>

            <div v-masonry transition-duration="0.3s" item-selector=".note" gutter=".gutter" :origin-top="true">
                <div class="gutter"></div>

                <note-component v-masonry-tile
                                v-for="note in other_notes"
                                :key="note.id"
                                :note="note"
                                :isTrashed="isTrashed">

                </note-component>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "NotesContainerComponent",
    props: ['notes', 'isTrashed'],
    data() {
        return {
            notesCollection: this.notes
        };
    },
    created() {
      window.events.$on('note_created', this.addNote);
      window.events.$on('note_deleted', this.deleteNote);
    },
    computed: {
        pinned_notes() {
            return this.notesCollection.filter( function(value){
                    return value.pinned === true;
            });
        },
        other_notes() {
            return this.notesCollection.filter( function(value){
                    return value.pinned === false;
            });
        }
    },
    methods: {
        addNote(note) {
            this.notesCollection.unshift(note);
        },
        deleteNote(note) {
            this.notesCollection.splice( this.notesCollection.indexOf(note) ,1);
        }
    }
}
</script>

<style scoped>
.gutter {
    width: 10px;
    height: 10px;
}
</style>
