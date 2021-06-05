<template> <!--TODO: There must be UNDO and REDO buttons while editing the note-->
    <!--TODO: a note or a group of notes could be selected and actions panel should appear instead of top bar-->
    <div class="note border border-gray-300 p-3 hover:shadow-md relative transition-colors m-auto new-note"
         :class="'bg-google-' + note.color"
         style="width: 600px"
         ref="note">

        <!--TODO: there should be editing state for the note-->
        <a class="absolute right-1 top-1 hover:bg-gray-300 p-2 rounded-full"
           @click.prevent="pin()"
           v-b-tooltip.hover.bottom :title="note.pinned ? 'Unpin' : 'Pin'">
            <i class="bi bi-pin-fill icon" v-if="note.pinned"></i>
            <i class="bi bi-pin icon" v-else></i>
        </a>

        <textarea name="note_header" placeholder="Title"
                  class="note-header-input mx-2 focus:outline-none h-auto resize-none font-bold bg-transparent"
                  @input="track_fields()"
                  v-model="note.header">

        </textarea>


        <textarea name="note_content"
                  placeholder="Take a note..."
                  class="note-content-input m-2 mb-4 mt-3 focus:outline-none h-auto resize-none bg-transparent"
                  @input="track_fields()"
                  v-model="note.body">

        </textarea>


        <div class="toolbar">
            <a href="" class="hover:bg-gray-300 rounded-full p-0 inline-block"
               v-b-tooltip.hover.bottom
               title="Remind me"
               @click.prevent>
                <b-dropdown size="sm" variant="link" toggle-class="" no-caret ref="reminder-dropdown">
                    <template #button-content>
                        <i class="bi bi-bell icon-sm p-0"></i>
                    </template>
                    <p class="text-lg p-2 pl-4 m-0 font-bold">Reminder:</p>
                    <b-dropdown-item href="#" @click="storeReminder('later_today')"
                                     class="focus:outline-none py-2.5 hover:bg-gray-200" v-if="isLaterTodayVisible">
                        Later today
                        <span class="text-gray-500">8:00 PM</span>
                    </b-dropdown-item>
                    <b-dropdown-item href="#" @click="storeReminder('tomorrow')"
                                     class="focus:outline-none py-2.5 hover:bg-gray-200">
                        Tomorrow
                        <span class="text-gray-500">8:00 AM</span>
                    </b-dropdown-item>
                    <b-dropdown-item href="#" @click="storeReminder('next_week')"
                                     class="focus:outline-none py-2.5 hover:bg-gray-200">
                        Next week
                        <span class="text-gray-500">Mon., 8:00 AM</span>
                    </b-dropdown-item>
                    <b-dropdown-item href="#"
                                     @click="pickDateAndTime()"
                                     class="focus:outline-none py-2.5 hover:bg-gray-200">
                        <i class="bi bi-alarm-fill mr-3"></i>
                        Pick date & time
                    </b-dropdown-item>
                    <b-dropdown-item href="#" @click="" class="focus:outline-none py-2.5 hover:bg-gray-200">
                        <i class="bi bi-geo-alt-fill mr-3"></i>
                        Pick place
                    </b-dropdown-item>
                </b-dropdown>
            </a>

            <a href="" class="hover:bg-gray-300 p-2 rounded-full"
               v-b-tooltip.hover.bottom
               title="Collaborator"
               @click.prevent="showCollaboratorsDialog()">
                <i class="bi bi-person-plus icon-sm"></i>
            </a>

            <div class="custom-tooltip">
                <a href="" class="hover:bg-gray-300 p-2 rounded-full"
                   v-b-tooltip.hover.bottom
                   title="Change color"
                   @click.prevent>
                    <i class="bi bi-palette icon-sm"></i>

                </a>
                <div class="vertical-tooltiptext rounded-md">
                    <a v-for="color in colors"
                       href=""
                       :class="'color-circle bg-google-' + color + ' ' + isActive(color)"
                       v-b-tooltip.hover.bottom
                       :title="color"
                       @click.prevent="changeColor(color)">
                        <i class="bi bi-check icon-sm"></i>
                    </a>
                </div>
            </div>

            <a href="" class="hover:bg-gray-300 p-2 rounded-full"
               v-b-tooltip.hover.bottom
               title="Add image"
               @click.prevent="selectImage()">
                <i class="bi bi-image icon-sm"></i>
            </a>

            <input type="file" ref="image" class="hidden" accept="image/jpeg,image/png,image/gif"
                   @change="handleFiles()">

            <a href="" class="hover:bg-gray-300 rounded-full p-0 inline-block"
               v-b-tooltip.hover.bottom
               title="More"
               @click.prevent>
                <b-dropdown size="sm" variant="link" toggle-class="text-decoration-none" no-caret>
                    <template #button-content>
                        <i class="bi bi-three-dots-vertical icon-sm p-0"></i>
                    </template>
                    <b-dropdown-item href="#">Add label</b-dropdown-item>
                    <b-dropdown-item href="#">Add drawing</b-dropdown-item>
                    <b-dropdown-item href="#">Show checkboxes</b-dropdown-item>
                </b-dropdown>
            </a>

            <a href="" class="hover:bg-gray-300 p-2 rounded-full"
               v-b-tooltip.hover.bottom
               title="Undo"
               @click.prevent="undo_input()">
                <i class="bi bi-arrow-counterclockwise icon-sm"></i>
            </a>

            <a href="" class="hover:bg-gray-300 p-2 rounded-full"
               v-b-tooltip.hover.bottom
               title="Redo"
               @click.prevent="redo_input()">
                <i class="bi bi-arrow-clockwise icon-sm"></i>
            </a>

            <button type="button" class="btn btn-danger btn-sm" @click="save()">Save</button>
        </div>

        <collaborator-dialog-component v-if="isCollaboratorsDialogVisible"
                                       :emails="collaboratorEmails"
                                       :owner="$attrs.owner"
                                       v-on:hide_dialog="hideCollaboratorsDialog()">

        </collaborator-dialog-component>
    </div>
</template>

<script>
export default {
    name: "NewNoteComponent",
    data() {
        return {
            isCollaboratorsDialogVisible: false,
            isLaterTodayVisible: false,
            changes: [
                {'header': '', 'content': ''},
            ],
            currentChangeIndex: 0,
            colors: [
                'white', 'red', 'orange', 'yellow',
                'green', 'teal', 'blue', 'dark-blue',
                'purple', 'pink', 'brown', 'grey'
            ],
            collaboratorEmails: ['email1@gmail.com', 'email2@gmail.com'],
            note: {
                header: '',
                body: '',
                pinned: false,
                archived: false,
                color: 'white',
                type: 'text'
            }
        };
    },
    created() {
        setInterval(this.checkLaterTodayVisibility, 500);
        //Save the note when clicked outside
        /*
                window.addEventListener("click", function (event) {
                    let clicked_exactly_on_container = document.getElementsByClassName('new-note')[0] === event.target;
                    let clicked_in_the_container = document.getElementsByClassName('new-note')[0].contains(event.target);

                    //TODO: A bug with "delete collaborator button" - when you click it - it emits an event, that this click was outside
                    if (!(clicked_exactly_on_container || clicked_in_the_container))
                        window.events.$emit('save_new_note');

                });

                window.events.$on('save_new_note', this.save);
        */
    },
    methods: {
        checkLaterTodayVisibility() {
            let evening = (new Date).setHours(19, 0, 0);
            this.isLaterTodayVisible = Date.now() < evening;
        },
        delay(callback, ms) {
            if (window.noteInputTimer)
                clearTimeout(window.noteInputTimer);

            window.noteInputTimer = setTimeout(function () {
                callback.apply(this);
            }, ms);
        },
        save() {
            axios.post('/note/', {
                header: this.note.header,
                body: this.note.body,
                pinned: this.note.pinned,
                archived: false,
                color: this.note.color,
                type: this.note.type
            }).then( () => location.reload() );
        },
        pin() {
            this.note.pinned = !this.note.pinned;
        },
        isActive(color) {
            return (this.note.color === color) ? 'active' : '';
        },
        changeColor(color) {
            this.note.color = color;
        },
        hideCollaboratorsDialog() {
            this.isCollaboratorsDialogVisible = false;
        },
        showCollaboratorsDialog() {
            this.isCollaboratorsDialogVisible = true;
        },
        setInputHeight(itemClass) {
            let element = document.getElementsByClassName(itemClass)[0];

            element.style.height = "auto";
            element.style.height = (element.scrollHeight) + "px";
        },
        undo_input() {
            if (this.currentChangeIndex > 0) {
                let change = this.changes[--this.currentChangeIndex];

                this.note.header = change.header;
                this.note.body = change.content;
            }
        },
        redo_input() {
            if (this.currentChangeIndex < (this.changes.length - 1)) {
                this.currentChangeIndex++;
                let change = this.changes[this.currentChangeIndex];

                this.note.header = change.header;
                this.note.body = change.content;
            }
        },
        track_fields() {
            if (this.currentChangeIndex < (this.changes.length - 1))
                this.changes.splice(this.currentChangeIndex + 1, 100);

            this.delay(this.track, 500);
            this.setInputHeight('note-header-input');
            this.setInputHeight('note-content-input');
        },
        track() {
            this.changes.push({'header': this.note.header, 'content': this.note.body});
            this.currentChangeIndex = this.changes.length - 1;
        }
    }
}
</script>

<style scoped>

</style>
