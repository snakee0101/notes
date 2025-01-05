<template>
    <div>
        <div v-if="is_empty">
            <div class="pinned">
                <p class="font-bold text-sm mb-2 text-color">PINNED</p>

                <div ref="pinned_notes_content" class="grid" id="pinned_notes_masonry">
                    <note-component v-for="pinned_note in pinned_notes_collection"
                                    :key="pinned_note.id"
                                    :note="pinned_note"
                                    :isTrashed="isTrashed"
                                    class="grid-item">

                    </note-component>
                </div>
            </div>

            <div class="others">
                <p class="font-bold text-sm mt-20 mb-2 text-color">OTHERS</p>

                <div ref="other_notes_content" class="grid" id="other_notes_masonry">
                    <note-component v-for="other_note in other_notes_collection"
                                    :key="other_note.id"
                                    :note="other_note"
                                    :isTrashed="isTrashed"
                                    class="grid-item">

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
    watch: {
        pinned_notes_collection: function (newValue, oldValue) {
            this.answer = 'Waiting for you to stop typing...'
            this.debouncedGetAnswer()
        }
    },
    created() {
      window.events.$on('note_created', this.addNote);
      window.events.$on('note_deleted', this.deleteNote);
      window.events.$on('searchCleared', this.refreshMasonry);
    },
    methods: {
        refreshMasonry() { 
            setTimeout(() => {
                window.events.$emit('refresh-all-masonry-layouts');
            }, 300);
        },
        addNote(note) {
            if (note.pinned) {
                this.pinned_notes_collection.unshift(note);

                setTimeout(() => {
                    let note_element = document.querySelector('[data-note-id="' + note.id + '"]');

                    window.masonry_layouts['pinned_notes_masonry'].prepended(note_element);
                    window.masonry_layouts['pinned_notes_masonry'].layout();
                }, 100); // delay is required because HTML element could not be created immediately
            } else {
                this.other_notes_collection.unshift(note);

                setTimeout(() => {
                    let note_element = document.querySelector('[data-note-id="' + note.id + '"]');

                    window.masonry_layouts['other_notes_masonry'].prepended(note_element);
                    window.masonry_layouts['other_notes_masonry'].layout();
                }, 100); // delay is required because HTML element could not be created immediately
            }
        },
        deleteNote(note) {
            let note_element = document.querySelector('[data-note-id="' + note.id + '"]');

            if (note.pinned) {
                this.pinned_notes_collection.splice(this.pinned_notes_collection.indexOf(note), 1);

                window.masonry_layouts['pinned_notes_masonry'].remove(note_element);
                window.masonry_layouts['pinned_notes_masonry'].layout();
            } else {
                this.other_notes_collection.splice(this.other_notes_collection.indexOf(note), 1);

                window.masonry_layouts['other_notes_masonry'].remove(note_element);
                window.masonry_layouts['other_notes_masonry'].layout();
            }
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
