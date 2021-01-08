<template>
    <div class="note border border-gray-300 p-3 hover:shadow-md relative transition-colors"
         :class="'bg-google-' + this.color">
        <a href="" class="absolute right-1 top-1 hover:bg-gray-300 p-2 rounded-full" @click.prevent="pin()">
            <div class="tooltip" v-if="pinned">
                <svg class="icon icon-small icon-pushpin" viewBox="0 0 32 32">
                    <path
                        d="M17 0l-3 3 3 3-7 8h-7l5.5 5.5-8.5 11.269v1.231h1.231l11.269-8.5 5.5 5.5v-7l8-7 3 3 3-3-15-15zM14 17l-2-2 7-7 2 2-7 7z"></path>
                </svg>
                <span class="tooltiptext">Pin</span>
            </div>

            <div class="tooltip" v-else>
                <svg class="icon icon-small icon-cancel-circle" viewBox="0 0 32 32">
                    <path
                        d="M16 0c-8.837 0-16 7.163-16 16s7.163 16 16 16 16-7.163 16-16-7.163-16-16-16zM16 29c-7.18 0-13-5.82-13-13s5.82-13 13-13 13 5.82 13 13-5.82 13-13 13z"></path>
                    <path d="M21 8l-5 5-5-5-3 3 5 5-5 5 3 3 5-5 5 5 3-3-5-5 5-5z"></path>
                </svg>
                <span class="tooltiptext">Unpin</span>
            </div>
        </a>

        <h3 class="font-bold">Note's header</h3>
        <div class="note-content my-4 leading-6 overflow-hidden break-words" style="max-height: 300px">
            note's content
            note's content
            note's content
        </div>
        <div class="toolbar">
            <div class="tooltip">
                <a href="" class="hover:bg-gray-300 p-2 rounded-full" @click.prevent>
                    <svg class="icon icon-small icon-bell" viewBox="0 0 32 32">
                        <path
                            d="M32.047 25c0-9-8-7-8-14 0-0.58-0.056-1.076-0.158-1.498-0.526-3.532-2.88-6.366-5.93-7.23 0.027-0.123 0.041-0.251 0.041-0.382 0-1.040-0.9-1.891-2-1.891s-2 0.851-2 1.891c0 0.131 0.014 0.258 0.041 0.382-3.421 0.969-5.966 4.416-6.039 8.545-0.001 0.060-0.002 0.121-0.002 0.183 0 7-8 5-8 14 0 2.382 5.331 4.375 12.468 4.878 0.673 1.263 2.002 2.122 3.532 2.122s2.86-0.86 3.532-2.122c7.137-0.503 12.468-2.495 12.468-4.878 0-0.007-0.001-0.014-0.001-0.021l0.048 0.021zM25.82 26.691c-1.695 0.452-3.692 0.777-5.837 0.958-0.178-2.044-1.893-3.648-3.984-3.648s-3.805 1.604-3.984 3.648c-2.144-0.18-4.142-0.506-5.837-0.958-2.332-0.622-3.447-1.318-3.855-1.691 0.408-0.372 1.523-1.068 3.855-1.691 2.712-0.724 6.199-1.122 9.82-1.122s7.109 0.398 9.82 1.122c2.332 0.622 3.447 1.318 3.855 1.691-0.408 0.372-1.523 1.068-3.855 1.691z"></path>
                    </svg>
                </a>
                <span class="tooltiptext">Remind me</span>
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
                <div class="vertical-tooltiptext rounded-md">
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
                <span class="tooltiptext">Add image</span>
            </div>

            <div class="tooltip">
                <a href="" class="hover:bg-gray-300 p-2 rounded-full" @click.prevent>
                    <svg class="icon icon-small icon-box-add" viewBox="0 0 32 32">
                        <path
                            d="M26 2h-20l-6 6v21c0 0.552 0.448 1 1 1h30c0.552 0 1-0.448 1-1v-21l-6-6zM16 26l-10-8h6v-6h8v6h6l-10 8zM4.828 6l2-2h18.343l2 2h-22.343z"></path>
                    </svg>
                </a>
                <span class="tooltiptext">Archive</span>
            </div>

            <div class="tooltip">
                <a href="" class="hover:bg-gray-300 p-2 rounded-full" @click.prevent>
                    <svg class="icon icon-small icon-ellipsis-v" viewBox="0 0 6 28">
                        <path
                            d="M6 19.5v3c0 0.828-0.672 1.5-1.5 1.5h-3c-0.828 0-1.5-0.672-1.5-1.5v-3c0-0.828 0.672-1.5 1.5-1.5h3c0.828 0 1.5 0.672 1.5 1.5zM6 11.5v3c0 0.828-0.672 1.5-1.5 1.5h-3c-0.828 0-1.5-0.672-1.5-1.5v-3c0-0.828 0.672-1.5 1.5-1.5h3c0.828 0 1.5 0.672 1.5 1.5zM6 3.5v3c0 0.828-0.672 1.5-1.5 1.5h-3c-0.828 0-1.5-0.672-1.5-1.5v-3c0-0.828 0.672-1.5 1.5-1.5h3c0.828 0 1.5 0.672 1.5 1.5z"></path>
                    </svg>
                </a>
                <span class="tooltiptext">More</span>
            </div>

        </div>

        <div v-if="isCollaboratorsDialogVisible"
             @click.self="hideCollaboratorsDialog()"
             class="collaborators-dialog fixed top-0 left-0 right-0
             bottom-0 flex items-center bg-gray-800 bg-opacity-75">
            <div class="collaborators-content m-auto">
                <div class="bg-white p-4 rounded-t-xl">
                    <h3 class="font-medium text-lg">Collaborators</h3>
                    <div class="mt-3 pt-4 px-2 border-t-2 border-gray-200">
                        <div class="collaborator flex flex-row mb-4 items-center">
                            <img class="rounded-full"
                                 style="width: 32px; height: 32px"
                                 src="data:image/png;base64,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">
                            <div class="ml-4">
                                <p class="text-sm font-bold">Username <span class="italic text-gray-600 font-normal">(Owner)</span>
                                </p>
                                <p class="text-sm text-gray-500">email@gmail.com</p>
                            </div>
                        </div>

                        <div class="collaborator flex flex-row mb-4 items-center" v-for="i in [1,2]">
                            <img class="rounded-full"
                                 style="width: 32px; height: 32px"
                                 src="data:image/png;base64,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">
                            <div class="ml-4 flex-grow">
                                <p class="text-sm font-bold">email@gmail.com</p>
                            </div>
                            <div class="tooltip">
                                <a href="" class="pt-1 px-2 pb-2 rounded-full hover:bg-gray-200" @click.prevent>
                                    <svg class="icon icon-xs icon-close" viewBox="0 0 20 20">
                                        <path d="M10 8.586l-7.071-7.071-1.414 1.414 7.071 7.071-7.071 7.071 1.414 1.414 7.071-7.071 7.071 7.071 1.414-1.414-7.071-7.071 7.071-7.071-1.414-1.414-7.071 7.071z"></path>
                                    </svg>
                                </a>
                                <span class="tooltip3text">Delete</span>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="bg-gray-200 rounded-b-xl py-2 px-4 text-right">
                    <button
                        class="font-normal px-6 py-2 mr-2 hover:bg-gray-300 focus:outline-none focus:bg-gray-400 rounded-sm">
                        Cancel
                    </button>
                    <button
                        class="font-normal px-6 py-2 hover:bg-gray-300 focus:outline-none focus:bg-gray-400 rounded-sm">
                        Save
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
            pinned: false,
            isCollaboratorsDialogVisible: false,
            colors: [
                'white', 'red', 'orange', 'yellow',
                'green', 'teal', 'blue', 'dark-blue',
                'purple', 'pink', 'brown', 'grey'
            ],
            color: this.$attrs.notecolor
        };
    },
    methods: {
        pin() {
            this.pinned = !this.pinned;
        },
        isActive(color) {
            return (this.color == color) ? 'active' : '';
        },
        changeColor(color) {
            this.color = color;
        },
        showCollaboratorsDialog() {
            this.isCollaboratorsDialogVisible = true;
        },
        hideCollaboratorsDialog() {
            this.isCollaboratorsDialogVisible = false;
        }
    }
}
</script>

<style scoped>

</style>
