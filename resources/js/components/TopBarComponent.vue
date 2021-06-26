<template>
    <header>

        <section class="note-actions-panel"
                 v-if="notes.length">

            <div class="regular_note_actions flex flex-row justify-between items-center py-2 pb-4 px-3" v-if="true"> <!--TODO: Check whether it is a regular note by page name-->
                <div class="flex flex-row items-center">
                    <a href="" class="mr-2 rounded-full"
                       @click.prevent="clearSelection()"
                       v-b-tooltip.hover.bottom
                       title="Clear selection">
                        <i class="bi bi-x icon-lg text-black"></i>
                    </a>

                    <p class="m-0 text-xl pt-0.5">{{ notes.length }} selected</p>
                </div>

                <div class="flex flex-row items-center">
                    <a href="" class="mr-3 rounded-full"
                       @click.prevent="pin()"
                       v-b-tooltip.hover.bottom
                       title="Pin"> <!--TODO: if only pinned notes were selected then "Unpin" should be here; if there is even one unpinned note then "Pin" should be here-->
                        <i class="bi bi-pin-fill icon" style="color: rgb(26, 86, 219)"></i>
                    </a>

                    <a href="" class="mr-3 rounded-full"
                       @click.prevent="remind()"
                       v-b-tooltip.hover.bottom
                       title="Remind me"> <!--TODO: there should be a context menu that sets the reminder at specific time regardless of the selected notes' reminders-->
                        <i class="bi bi-bell icon" style="color: rgb(26, 86, 219)"></i>
                    </a>

                    <a href="" class="mr-3 rounded-full"
                       @click.prevent="changeColor()"
                       v-b-tooltip.hover.bottom
                       title="Change color">
                        <i class="bi bi-palette icon" style="color: rgb(26, 86, 219)"></i>
                    </a>

                    <a href="" class="mr-3 rounded-full"
                       @click.prevent="toggleArchive()"
                       v-b-tooltip.hover.bottom
                       title="Archive"> <!--TODO: In Archive section it should be Unarchive-->
                        <i class="bi bi-save2-fill icon" style="color: rgb(26, 86, 219)"></i>
                    </a>
                </div>
            </div>

            <div class="trashed_note_actions flex flex-row justify-between items-center py-2 pb-4 px-3 border-b-2 border-gray-300" v-else>
                <div class="flex flex-row items-center">
                    <a href="" class="mr-2 rounded-full"
                       @click.prevent="clearSelection()"
                       v-b-tooltip.hover.bottom
                       title="Clear selection">
                        <i class="bi bi-x icon-lg text-black"></i>
                    </a>

                    <p class="m-0 text-xl pt-0.5">{{ notes.length }} selected</p>
                </div>

                <div class="flex flex-row items-center">
                    <a href="" class="mr-3 rounded-full"
                       @click.prevent="deleteForever()"
                       v-b-tooltip.hover.bottom
                       title="Delete forever">
                        <i class="bi bi-trash2 icon" style="color: rgb(26, 86, 219)"></i>
                    </a>

                    <a href="" class="mr-2 rounded-full mt-1"
                       @click.prevent="restore()"
                       v-b-tooltip.hover.bottom
                       title="Restore">
                        <i class="bi bi-recycle icon-lg text-black">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-recycle text-blue-600" viewBox="0 0 16 16">
                                <path d="M9.302 1.256a1.5 1.5 0 0 0-2.604 0l-1.704 2.98a.5.5 0 0 0 .869.497l1.703-2.981a.5.5 0 0 1 .868 0l2.54 4.444-1.256-.337a.5.5 0 1 0-.26.966l2.415.647a.5.5 0 0 0 .613-.353l.647-2.415a.5.5 0 1 0-.966-.259l-.333 1.242-2.532-4.431zM2.973 7.773l-1.255.337a.5.5 0 1 1-.26-.966l2.416-.647a.5.5 0 0 1 .612.353l.647 2.415a.5.5 0 0 1-.966.259l-.333-1.242-2.545 4.454a.5.5 0 0 0 .434.748H5a.5.5 0 0 1 0 1H1.723A1.5 1.5 0 0 1 .421 12.24l2.552-4.467zm10.89 1.463a.5.5 0 1 0-.868.496l1.716 3.004a.5.5 0 0 1-.434.748h-5.57l.647-.646a.5.5 0 1 0-.708-.707l-1.5 1.5a.498.498 0 0 0 0 .707l1.5 1.5a.5.5 0 1 0 .708-.707l-.647-.647h5.57a1.5 1.5 0 0 0 1.302-2.244l-1.716-3.004z"/>
                            </svg>
                        </i>
                    </a>
                </div>
            </div>

        </section>

        <section class="flex flex-row justify-between py-2 px-3 shadow-sm items-center" v-else>
            <menu-switcher-component></menu-switcher-component>
            <search-component></search-component>

            <section class="flex flex-row items-center">
                <a href="" class="p-2 mr-3 rounded-full hover:bg-gray-200"
                   v-b-tooltip.hover.bottom
                   title="Setings">
                    <i class="bi bi-nut icon-lg text-black"></i>
                </a>

                <a href="" class="p-2 rounded-full hover:bg-gray-200"
                   v-b-tooltip.hover.bottom
                   title="Account">
                    <img class="rounded-full"
                         style="width: 32px; height: 32px"
                         src="data:image/png;base64,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">
                </a>
            </section>
        </section>

    </header>
</template>

<script>
export default {
    name: "TopBarComponent",
    data() {
      return {
          notes: []
      }
    },
    created() {
        window.events.$on('note_selection_changed', this.registerNoteSelection);
    },
    methods: {
        registerNoteSelection(note, selected) {
            (selected) ? this.addNote(note) : this.removeNote(note);
        },
        addNote(note) {
            this.notes.push(note);
        },
        removeNote(note) {
            this.notes.splice( this.notes.indexOf(note) ,1);
        },
        clearSelection() {
            alert('clears the selection');
        },
        deleteForever() {
            alert('deletes the note forever');
        },
        restore() {
            alert('restores the note');
        },
        pin() {
            alert('sets the specific pinned state depend on condition');
        },
        remind() {
            alert('remainder of the notes');
        },
        changeColor() {
            alert('sets selected color for all selected notes');
        },
        toggleArchive() {
            alert('set archived state for all the notes depend on page');
        }
    }
}
</script>

<style scoped>

</style>
