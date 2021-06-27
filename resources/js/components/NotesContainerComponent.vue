<template>
    <div>
        <div class="pinned">
            <p class="font-bold text-sm mb-2">PINNED</p>

            <div v-masonry transition-duration="0.3s" item-selector=".note" gutter=".gutter" :origin-top="true">
                <div class="gutter"></div>

                <note-component v-masonry-tile
                                v-for="note in pinned_notes_collection"
                                :key="note.id"
                                :note="note"
                                :isTrashed="isTrashed">

                </note-component>
            </div>

            <div>
                <button class="btn btn-primary" @click="prev_pinned_page()">< Previous page</button>
                <button class="btn btn-primary" @click="next_pinned_page()">Next page ></button>
            </div>
        </div>

        <div class="others">
            <p class="font-bold text-sm mt-20 mb-2">OTHERS</p>

            <div v-masonry transition-duration="0.3s" item-selector=".note" gutter=".gutter" :origin-top="true">
                <div class="gutter"></div>

                <note-component v-masonry-tile
                                v-for="note in other_notes_collection"
                                :key="note.id"
                                :note="note"
                                :isTrashed="isTrashed">

                </note-component>
            </div>

            <div>
                <button class="btn btn-primary" @click="prev_other_page()">< Previous page</button>
                <button class="btn btn-primary" @click="next_other_page()">Next page ></button>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "NotesContainerComponent",
    props: ['notes', 'isTrashed', 'pinned_notes', 'other_notes'],
    data() {
        return {
            pinned_notes_paginator: this.pinned_notes,
            other_notes_paginator: this.other_notes,
            pinned_notes_collection: this.pinned_notes.data,
            other_notes_collection: this.other_notes.data,
            pinned_page: 1,
            other_page: 1,
        };
    },
    created() {
      window.events.$on('note_created', this.addNote);
      window.events.$on('note_deleted', this.deleteNote);
      window.events.$on('trash_emptied', this.clearAll);
    },
    computed: {
        /*pinned_notes() {
            return this.notesCollection.filter( function(value){
                    return value.pinned === true;
            });
        },
        other_notes() {
            return this.notesCollection.filter( function(value){
                    return value.pinned === false;
            });
        }*/
    },
    methods: {
        addNote(note) {
            this.notesCollection.unshift(note); //TODO: review this method
        },
        deleteNote(note) {
            this.notesCollection.splice( this.notesCollection.indexOf(note) ,1); //TODO: review this method
        },
        clearAll() {
            this.notesCollection = []; //TODO: review this method
        },

        prev_pinned_page() {
            this.pinned_page--;
            //get the data
        },
        next_pinned_page() {
            this.pinned_page++;
            //get the data
        },
        prev_other_page() {
            this.other_page--;
            //get the data
        },
        next_other_page() {
            this.other_page++;
            //get the data
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
