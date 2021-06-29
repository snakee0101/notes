<template>
    <div>
        <div v-if="notes_count">
            <div class="pinned">
                <p class="font-bold text-sm mb-2">PINNED</p>

                <div v-masonry transition-duration="0.3s" item-selector=".note" gutter=".gutter" :origin-top="true" ref="pinned_notes_content">
                    <div class="gutter"></div>

                    <note-component v-masonry-tile
                                    v-for="note in pinned_notes_collection"
                                    :key="note.id"
                                    :note="note"
                                    :isTrashed="isTrashed">

                    </note-component>
                </div>

                <div>
                    <button class="btn btn-primary" @click="prev_pinned_page()" v-if="pinned_notes_paginator.prev_page_url !== null">< Previous page</button>
                    <button class="btn btn-primary" @click="next_pinned_page()" v-if="pinned_notes_paginator.next_page_url !== null">Next page ></button>
                </div>
            </div>

            <div class="others">
                <p class="font-bold text-sm mt-20 mb-2">OTHERS</p>

                <div v-masonry transition-duration="0.3s" item-selector=".note" gutter=".gutter" :origin-top="true" ref="other_notes_content">
                    <div class="gutter"></div>

                    <note-component v-masonry-tile
                                    v-for="note in other_notes_collection"
                                    :key="note.id"
                                    :note="note"
                                    :isTrashed="isTrashed">

                    </note-component>
                </div>

                <div>
                    <button class="btn btn-primary" @click="prev_other_page()" v-if="other_notes_paginator.prev_page_url !== null">< Previous page</button>
                    <button class="btn btn-primary" @click="next_other_page()" v-if="other_notes_paginator.next_page_url !== null">Next page ></button>
                </div>
            </div>
        </div>

        <div v-else>
            <div v-if="isOnPage('/reminder')">
                <p class="text-center text-2xl mb-6 mt-14">
                    <i class="bi bi-bell-fill icon-xl"></i>
                </p>
                <p class="text-center text-2xl text-gray-600 font-light">Notes with upcoming reminders appear
                    here</p>
            </div>

            <div v-if="isOnPage('/tag')">
                <p class="text-center text-2xl mb-6 mt-20">
                    <i class="bi bi-tag-fill icon-xl"></i>
                </p>
                <p class="text-center text-2xl text-gray-600 font-light">No notes with this label yet</p>
            </div>

            <div v-if="isOnPage('/archive')">
                <p class="text-center text-2xl mb-6 mt-14">
                    <i class="bi bi-save2-fill icon-xl"></i>
                </p>
                <p class="text-center text-2xl text-gray-600 font-light">Your archived notes appear here</p>
            </div>

            <div v-if="isOnPage('/trash')">
                <p class="text-center text-2xl mb-6 mt-14">
                    <i class="bi bi-trash-fill icon-xl"></i>
                </p>
                <p class="text-center text-2xl text-gray-600 font-light">No notes in Trash</p>
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
    computed: {
        notes_count() {
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


        prev_pinned_page() {
            this.pinned_page--;

            axios.get(this.pinned_notes_paginator.prev_page_url+'&notes_type=pinned_notes')
                .then(this.set_pinned_page);
        },
        next_pinned_page() {
            this.pinned_page++;

            axios.get(this.pinned_notes_paginator.next_page_url+'&notes_type=pinned_notes')
                .then(this.set_pinned_page);
        },
        set_pinned_page(res) {
            this.pinned_notes_paginator = res.data;

            Object.assign(this.pinned_notes_collection, res.data.data); //force refresh of the paginator to make it reactive

            let contentHeight = this.$refs['pinned_notes_content'].clientHeight;
            window.scrollBy(0,-contentHeight/2);
        },


        prev_other_page() {
            this.other_page--;

            axios.get(this.other_notes_paginator.prev_page_url+'&notes_type=other_notes')
                .then(this.set_other_page);
        },
        next_other_page() {
            this.other_page++;

            axios.get(this.other_notes_paginator.next_page_url+'&notes_type=other_notes')
                .then(this.set_other_page);
        },
        set_other_page(res) {
            this.other_notes_paginator = res.data;

            Object.assign(this.other_notes_collection, res.data.data); //force refresh of the paginator to make it reactive

            let contentHeight = this.$refs['other_notes_content'].clientHeight;
            window.scrollBy(0,-contentHeight/2);
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
