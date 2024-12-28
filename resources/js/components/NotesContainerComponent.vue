<template>
    <div>
        <div v-if="is_empty">
            <div class="pinned">
                <p class="font-bold text-sm mb-2 text-color">PINNED</p>

                <div v-masonry transition-duration="0.3s" item-selector=".note" gutter=".gutter" :origin-top="true" ref="pinned_notes_content">
                    <div class="gutter"></div>

                    <note-component v-masonry-tile
                                    v-for="pinned_note in pinned_notes_collection"
                                    :key="pinned_note.id"
                                    :note="pinned_note"
                                    :isTrashed="isTrashed">

                    </note-component>
                </div>
            </div>

            <div class="others">
                <p class="font-bold text-sm mt-20 mb-2 text-color">OTHERS</p>

                <div v-masonry transition-duration="0.3s" item-selector=".note" gutter=".gutter" :origin-top="true" ref="other_notes_content">
                    <div class="gutter"></div>

                    <note-component v-masonry-tile
                                    v-for="other_note in other_notes_collection"
                                    :key="other_note.id"
                                    :note="other_note"
                                    :isTrashed="isTrashed">

                    </note-component>
                </div>
            </div>
        </div>

        <div v-else>
            <div v-if="isOnPage('/reminder')">
                <p class="text-center text-2xl mb-6 mt-14">
                    <i class="bi bi-bell-fill icon-xl"></i>
                </p>
                <p class="text-center text-2xl text-color font-light">Notes with upcoming reminders appear
                    here</p>
            </div>

            <div v-if="isOnPage('/tag')">
                <p class="text-center text-2xl mb-6 mt-20">
                    <i class="bi bi-tag-fill icon-xl"></i>
                </p>
                <p class="text-center text-2xl text-color font-light">No notes with this label yet</p>
            </div>

            <div v-if="isOnPage('/archive')">
                <p class="text-center text-2xl mb-6 mt-14">
                    <i class="bi bi-save2-fill icon-xl"></i>
                </p>
                <p class="text-center text-2xl text-color font-light">Your archived notes appear here</p>
            </div>

            <div v-if="isOnPage('/trash')">
                <p class="text-center text-2xl mb-6 mt-14">
                    <i class="bi bi-trash-fill icon-xl"></i>
                </p>
                <p class="text-center text-2xl text-color font-light">No notes in Trash</p>
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
            pinned_notes_collection: this.pinned_notes,
            other_notes_collection: this.other_notes,
        };
    },
    computed: {
        is_empty() {
            return this.pinned_notes_collection.length + this.other_notes_collection.length;
        }
    },
    created() {
      window.events.$on('note_created', this.addNote);
      window.events.$on('note_deleted', this.deleteNote);
      window.events.$on('trash_emptied', this.clearAll);
    },
    methods: {
        addNote(note) {
            if(note.pinned)
                this.pinned_notes_collection.unshift(note);
            else
                this.other_notes_collection.unshift(note);
        },
        deleteNote(note) {
            if(note.pinned)
                this.pinned_notes_collection.splice( this.pinned_notes_collection.indexOf(note) ,1);
            else
                this.other_notes_collection.splice( this.other_notes_collection.indexOf(note) ,1);
        },
        clearAll() {
            this.pinned_notes_collection = [];
            this.other_notes_collection = [];
        },
        isOnPage(page) {
            return location.href.includes(page);
        },
    }
}
</script>

<style scoped>
.gutter {
    width: 10px;
    height: 10px;
}
</style>
