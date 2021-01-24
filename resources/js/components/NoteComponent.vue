<template> <!--TODO: There must be UNDO and REDO buttons while editing the note-->
    <!--TODO: a note or a group of notes could be selected and actions panel should appear instead of top bar-->
    <div class="note border border-gray-300 p-3 hover:shadow-md relative transition-colors"
         :class="'bg-google-' + note.color"
         v-if="shown">
        <a href="" class="absolute right-1 top-1 hover:bg-gray-300 p-2 rounded-full" @click.prevent="pin()" v-if="!trashed">
            <div class="tooltip" v-if="note.pinned">
                <svg class="icon icon-small icon-cancel-circle" viewBox="0 0 32 32">
                    <path
                        d="M16 0c-8.837 0-16 7.163-16 16s7.163 16 16 16 16-7.163 16-16-7.163-16-16-16zM16 29c-7.18 0-13-5.82-13-13s5.82-13 13-13 13 5.82 13 13-5.82 13-13 13z"></path>
                    <path d="M21 8l-5 5-5-5-3 3 5 5-5 5 3 3 5-5 5 5 3-3-5-5 5-5z"></path>
                </svg>
                <span class="tooltiptext">Unpin</span>
            </div>
            <!--TODO: there should be editing state for the note-->
            <div class="tooltip" v-else>
                <svg class="icon icon-small icon-pushpin" viewBox="0 0 32 32">
                    <path
                        d="M17 0l-3 3 3 3-7 8h-7l5.5 5.5-8.5 11.269v1.231h1.231l11.269-8.5 5.5 5.5v-7l8-7 3 3 3-3-15-15zM14 17l-2-2 7-7 2 2-7 7z"></path>
                </svg>
                <span class="tooltiptext">Pin</span>
            </div>


        </a>


        <div v-if="editing">
            <textarea name="note_header" placeholder="Title"
                      class="note-header-input mx-2 focus:outline-none h-auto resize-none font-bold bg-transparent"
                      @input="setInputHeight('note-header-input')"
                      v-model="note.header">

            </textarea>
        </div>
        <h3 class="font-bold" v-else>{{ note.header }}</h3>


        <div v-if="editing">
            <textarea name="note_content"
                      placeholder="Take a note..."
                      class="note-content-input m-2 mb-4 mt-3 focus:outline-none h-auto resize-none bg-transparent"
                      @input="setInputHeight('note-content-input')"
                      v-model="note.body">

            </textarea>
        </div>
        <div class="note-content my-4 leading-6 overflow-hidden break-words" style="max-height: 300px" v-else>
            {{ note.body }}
        </div>

        <div class="tags mb-4">
            <a v-for="tag in note.tags"
               :href="'/tag/' + tag"
               class="mr-2 border border-black rounded-full px-2 py-0.5 text-sm group relative">
                {{ tag }}
                <a class="hidden group-hover:inline absolute right-1 group-hover:bg-gray-300 rounded-full px-1 z-20"
                      @click.prevent="detach_tag(tag)">
                    <svg class="icon icon-xs icon-close" viewBox="0 0 20 20">
                        <path d="M10 8.586l-7.071-7.071-1.414 1.414 7.071 7.071-7.071 7.071 1.414 1.414 7.071-7.071 7.071 7.071 1.414-1.414-7.071-7.071 7.071-7.071-1.414-1.414-7.071 7.071z"></path>
                    </svg>
                </a>
            </a>
        </div>

        <div class="toolbar flex justify-between" v-if="trashed">
            <button
                @click="restore()"
                class="text-white bg-green-500 border border-green-600 text-sm font-medium px-2 py-2 mr-2 hover:bg-green-700 focus:bg-green-900 focus:outline-none rounded-sm">
                Restore
            </button>
            <button
                @click="showDeleteConfirmation()"
                class="text-white bg-red-500 border border-red-800 text-sm font-medium px-2 py-2 hover:bg-red-700 focus:bg-red-900 focus:outline-none  rounded-sm">
                Delete Forever
            </button>
        </div>

        <div class="toolbar" v-else>
            <div class="tooltip">
                <a href="" class="hover:bg-gray-300 p-2 rounded-full" @click.prevent>
                    <svg class="icon icon-small icon-bell" viewBox="0 0 32 32">
                        <path
                            d="M32.047 25c0-9-8-7-8-14 0-0.58-0.056-1.076-0.158-1.498-0.526-3.532-2.88-6.366-5.93-7.23 0.027-0.123 0.041-0.251 0.041-0.382 0-1.040-0.9-1.891-2-1.891s-2 0.851-2 1.891c0 0.131 0.014 0.258 0.041 0.382-3.421 0.969-5.966 4.416-6.039 8.545-0.001 0.060-0.002 0.121-0.002 0.183 0 7-8 5-8 14 0 2.382 5.331 4.375 12.468 4.878 0.673 1.263 2.002 2.122 3.532 2.122s2.86-0.86 3.532-2.122c7.137-0.503 12.468-2.495 12.468-4.878 0-0.007-0.001-0.014-0.001-0.021l0.048 0.021zM25.82 26.691c-1.695 0.452-3.692 0.777-5.837 0.958-0.178-2.044-1.893-3.648-3.984-3.648s-3.805 1.604-3.984 3.648c-2.144-0.18-4.142-0.506-5.837-0.958-2.332-0.622-3.447-1.318-3.855-1.691 0.408-0.372 1.523-1.068 3.855-1.691 2.712-0.724 6.199-1.122 9.82-1.122s7.109 0.398 9.82 1.122c2.332 0.622 3.447 1.318 3.855 1.691-0.408 0.372-1.523 1.068-3.855 1.691z"></path>
                    </svg>
                </a>
                <span class="tooltiptext">Remind me</span> <!--TODO: Remind Me button should show a dropdown-->
            </div>

            <div class="tooltip">
                <a href="" class="hover:bg-gray-300 p-2 rounded-full" @click.prevent="showCollaboratorsDialog()">
                    <svg class="icon icon-small icon-user-plus" viewBox="0 0 32 32">
                        <path
                            d="M12 23c0-4.726 2.996-8.765 7.189-10.319 0.509-1.142 0.811-2.411 0.811-3.681 0-4.971 0-9-6-9s-6 4.029-6 9c0 3.096 1.797 6.191 4 7.432v1.649c-6.784 0.555-12 3.888-12 7.918h12.416c-0.271-0.954-0.416-1.96-0.416-3z"></path>
                        <path
                            d="M23 14c-4.971 0-9 4.029-9 9s4.029 9 9 9c4.971 0 9-4.029 9-9s-4.029-9-9-9zM28 24h-4v4h-2v-4h-4v-2h4v-4h2v4h4v2z"></path>
                    </svg>
                </a>
                <span class="tooltiptext">Collaborator</span>
            </div>


            <div class="tooltip">
                <a href="" class="hover:bg-gray-300 p-2 rounded-full" @click.prevent>
                    <svg class="icon icon-small icon-palette" viewBox="0 0 24 24">
                        <path
                            d="M17.484 12q0.609 0 1.055-0.422t0.445-1.078-0.445-1.078-1.055-0.422-1.055 0.422-0.445 1.078 0.445 1.078 1.055 0.422zM14.484 8.016q0.609 0 1.055-0.445t0.445-1.055-0.445-1.055-1.055-0.445-1.055 0.445-0.445 1.055 0.445 1.055 1.055 0.445zM9.516 8.016q0.609 0 1.055-0.445t0.445-1.055-0.445-1.055-1.055-0.445-1.055 0.445-0.445 1.055 0.445 1.055 1.055 0.445zM6.516 12q0.609 0 1.055-0.422t0.445-1.078-0.445-1.078-1.055-0.422-1.055 0.422-0.445 1.078 0.445 1.078 1.055 0.422zM12 3q3.703 0 6.352 2.344t2.648 5.672q0 2.063-1.477 3.516t-3.539 1.453h-1.734q-0.656 0-1.078 0.445t-0.422 1.055q0 0.516 0.375 0.984t0.375 1.031q0 0.656-0.422 1.078t-1.078 0.422q-3.75 0-6.375-2.625t-2.625-6.375 2.625-6.375 6.375-2.625z"></path>
                    </svg>
                </a>
                <span class="tooltiptext">Change color</span>
                <div class="vertical-tooltiptext rounded-md"> <!--TODO: Color circles' tooltips work another way - they're smaller and they're closed when the cursor is out of the color circle (even when it was on the tooltip). So when the cursor is on the tooltip, it will be closed.-->
                    <div class="tooltip2" v-for="color in colors">
                        <a href=""
                           :class="'color-circle border-2 transition border-transparent p-2 m-1 d-inline-block rounded-full bg-google-' + color + ' ' + isActive(color)"
                           @click.prevent="changeColor(color)">
                            <svg class="icon icon-small icon-checkmark -mt-1 opacity-0 transition" viewBox="0 0 32 32">
                                <path d="M27 4l-15 15-7-7-5 5 12 12 20-20z"></path>
                            </svg>
                        </a>
                        <span class="tooltip2text" v-text="color"></span>
                    </div>
                </div>
            </div>


            <div class="tooltip">
                <a href="" class="hover:bg-gray-300 p-2 rounded-full" @click.prevent>
                    <svg class="icon icon-small icon-image" viewBox="0 0 32 32">
                        <path
                            d="M29.996 4c0.001 0.001 0.003 0.002 0.004 0.004v23.993c-0.001 0.001-0.002 0.003-0.004 0.004h-27.993c-0.001-0.001-0.003-0.002-0.004-0.004v-23.993c0.001-0.001 0.002-0.003 0.004-0.004h27.993zM30 2h-28c-1.1 0-2 0.9-2 2v24c0 1.1 0.9 2 2 2h28c1.1 0 2-0.9 2-2v-24c0-1.1-0.9-2-2-2v0z"></path>
                        <path d="M26 9c0 1.657-1.343 3-3 3s-3-1.343-3-3 1.343-3 3-3 3 1.343 3 3z"></path>
                        <path d="M28 26h-24v-4l7-12 8 10h2l7-6z"></path>
                    </svg>
                </a>
                <span class="tooltiptext">Add image</span> <!--TODO: Add Image button should show and image selecting dialog and save the image into internal array-->
            </div>

            <div class="tooltip" v-if="note.archived">
                <a href="" class="hover:bg-gray-300 p-2 rounded-full" @click.prevent="unarchive()">
                    <svg class="icon icon-small icon-box-add" viewBox="0 0 32 32">
                        <path
                            d="M26 2h-20l-6 6v21c0 0.552 0.448 1 1 1h30c0.552 0 1-0.448 1-1v-21l-6-6zM16 26l-10-8h6v-6h8v6h6l-10 8zM4.828 6l2-2h18.343l2 2h-22.343z"></path>
                    </svg>
                </a>
                <span class="tooltiptext">Unarchive</span>
            </div>

            <div class="tooltip" v-else>
                <a href="" class="hover:bg-gray-300 p-2 rounded-full" @click.prevent="archive()">
                    <svg class="icon icon-small icon-box-add" viewBox="0 0 32 32">
                        <path
                            d="M26 2h-20l-6 6v21c0 0.552 0.448 1 1 1h30c0.552 0 1-0.448 1-1v-21l-6-6zM16 26l-10-8h6v-6h8v6h6l-10 8zM4.828 6l2-2h18.343l2 2h-22.343z"></path>
                    </svg>
                </a>
                <span class="tooltiptext">Archive</span>
            </div>

            <div class="tooltip">
                <a href="" class="hover:bg-gray-300 p-2 rounded-full dropdown-opener" @click.prevent>
                    <svg class="icon icon-small icon-ellipsis-v" viewBox="0 0 6 28">
                        <path
                            d="M6 19.5v3c0 0.828-0.672 1.5-1.5 1.5h-3c-0.828 0-1.5-0.672-1.5-1.5v-3c0-0.828 0.672-1.5 1.5-1.5h3c0.828 0 1.5 0.672 1.5 1.5zM6 11.5v3c0 0.828-0.672 1.5-1.5 1.5h-3c-0.828 0-1.5-0.672-1.5-1.5v-3c0-0.828 0.672-1.5 1.5-1.5h3c0.828 0 1.5 0.672 1.5 1.5zM6 3.5v3c0 0.828-0.672 1.5-1.5 1.5h-3c-0.828 0-1.5-0.672-1.5-1.5v-3c0-0.828 0.672-1.5 1.5-1.5h3c0.828 0 1.5 0.672 1.5 1.5z"></path>
                    </svg>
                </a>
                <span class="tooltiptext">More</span> <!--TODO: More button should show a dropdown-->
                <div class="dropdown more-dropdown">
                    <div class="dropdown-content p-0 rounded-md bg-clip-padding">
                        <button class="dropdown-item focus:outline-none d-block w-full p-2 pl-4 text-left hover:bg-gray-200">Delete note</button>
                        <button class="dropdown-item focus:outline-none d-block w-full p-2 pl-4 text-left hover:bg-gray-200">Add label</button>
                        <button class="dropdown-item focus:outline-none d-block w-full p-2 pl-4 text-left hover:bg-gray-200">Add drawing</button>
                        <button class="dropdown-item focus:outline-none d-block w-full p-2 pl-4 text-left hover:bg-gray-200">Make a copy</button>
                        <button class="dropdown-item focus:outline-none d-block w-full p-2 pl-4 text-left hover:bg-gray-200">Show checkboxes</button>
                    </div>
                </div>
            </div>

        </div>

        <collaborator-dialog-component v-show="isCollaboratorsDialogVisible"
                                       :note="note"
                                       :emails="note.collaborators_json"
                                       :owner="note.owner_json"
                                       v-on:hide_dialog="hideCollaboratorsDialog()">

        </collaborator-dialog-component>

        <div class="confirmation fixed top-0 left-0 right-0 bottom-0 flex items-center bg-gray-800 bg-opacity-75 z-20"
             v-if="isDeleteConfirmationVisible"
             @click.self="hideDeleteConfirmation()">
            <div class="m-auto">
                <div class="bg-white p-6 rounded-t-lg text-center text-sm">
                    Delete note forever?
                </div>
                <div class="bg-white rounded-b-lg py-2 px-4 text-right">
                    <button
                        @click="hideDeleteConfirmation()"
                        class="text-gray-800 text-sm font-medium px-6 py-2 mr-2 hover:bg-gray-100 focus:bg-gray-200 focus:outline-none  rounded-sm">
                        Cancel
                    </button>
                    <button
                        @click="delete_forever()"
                        class="py-2 px-6 text-red-500 text-sm font-bold hover:bg-red-50 focus:bg-red-100 focus:outline-none">
                        Delete
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "NoteComponent",
    data() {
        return {
            editing: false,
            shown: true,
            isCollaboratorsDialogVisible: false,
            isDeleteConfirmationVisible: false,
            colors: [
                'white', 'red', 'orange', 'yellow',
                'green', 'teal', 'blue', 'dark-blue',
                'purple', 'pink', 'brown', 'grey'
            ],
            trashed: this.$attrs.istrashed,
            note: JSON.parse(this.$attrs.note)
        };
    },
    methods: {
        pin() {
            if(this.note.pinned)
                axios.put('/note/' + this.note.id, {
                    'pinned' : false
                });
            else
                axios.put('/note/' + this.note.id, {
                    'pinned' : true
                });

            this.note.pinned = !this.note.pinned;
        },
        hide() {
            this.shown = false;
        },
        show() {
            this.shown = true;
        },
        isActive(color) {
            return (this.note.color === color) ? 'active' : '';
        },
        changeColor(color) {
            this.note.color = color;

            axios.put('/note/' + this.note.id, {
                'color' : color
            });
        },
        hideCollaboratorsDialog() {
            this.isCollaboratorsDialogVisible = false;
        },
        showCollaboratorsDialog() {
            this.isCollaboratorsDialogVisible = true;
        },
        hideDeleteConfirmation() {
            this.isDeleteConfirmationVisible = false;
        },
        showDeleteConfirmation() {
            this.isDeleteConfirmationVisible = true;
        },
        restore() {
            axios.post('note/restore/' + this.note.id);
            this.hide();//TODO: There should be animation while hiding a note

            window.events.$emit('show-notification', 'Note restored', this.deleteNote);
        },
        deleteNote(){
            axios.delete('note/' + this.note.id);
            this.show();

            window.events.$emit('show-notification', 'Action undone');
        },
        archive() {
            axios.put('/note/' + this.note.id, {
                'archived' : true
            });

            this.hide();
            window.events.$emit('show-notification', 'Note archived', this.undo_archive);
        },
        unarchive() {
            axios.put('/note/' + this.note.id, {
                'archived' : false
            });

            this.hide();
            window.events.$emit('show-notification', 'Note unarchived', this.undo_unarchive);
        },
        undo_archive() {
            axios.delete('/unarchive/' + this.note.id);

            this.show();
            window.events.$emit('show-notification', 'Action undone');
        },
        undo_unarchive() {
            axios.post('/archive/' + this.note.id);

            this.show();
            window.events.$emit('show-notification', 'Action undone');
        },
        delete_forever() {
            this.shown = false;
            axios.delete('note/' + this.note.id);
        },
        setInputHeight(itemClass) {
            let element = document.getElementsByClassName(itemClass)[0];

            element.style.height = "auto";
            element.style.height = (element.scrollHeight) + "px";
        },
        detach_tag(tag) {
           axios.delete('/detach_tag/'+ this.note.id +'/' + tag);
           let index = this.note.tags.indexOf(tag);
           this.note.tags.splice(index, 1);

           let tagsLocation = 'tag/' + encodeURIComponent(tag);
           if( location.href.includes(tagsLocation) )
           {
               this.hide();
           }
        }
    }
}
</script>

<style scoped>

</style>
