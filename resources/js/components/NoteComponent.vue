<template> <!--TODO: There must be UNDO and REDO buttons while editing the note-->
    <!--TODO: a note or a group of notes could be selected and actions panel should appear instead of top bar-->
    <div class="note border border-gray-300 p-3 hover:shadow-md relative transition-colors"
         :class="'bg-google-' + note.color"
         v-if="shown"
         ref="note">
        <a href="" class="absolute right-2 top-2 hover:bg-gray-300 p-1 rounded-full" @click.prevent="pin()" v-if="!trashed">
            <!--TODO: there should be editing state for the note-->
                <i class="bi bi-pin-fill icon text-black"
                   v-b-tooltip.hover.bottom title="Unpin" v-if="note.pinned"></i>

                <i class="bi bi-pin icon text-black"
                   v-b-tooltip.hover.bottom title="Pin" v-else></i>
        </a>

        <div class="images">
            <img :src="image.thumbnail_small_path" v-for="image in note.images_json">
        </div>

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
            <a v-if="note.reminder_json"
               href="/reminders"
               class="mr-2 border border-black rounded-full px-2 py-0.5 text-sm group relative">
                <i class="bi bi-alarm icon"></i>
                {{ getReminderTime() }}
                <a class="hidden group-hover:inline absolute right-1 group-hover:bg-gray-300 rounded-full px-1 z-20"
                   @click.prevent="removeReminder()">
                    <i class="bi bi-x icon"></i>
                </a>
            </a>
            <a v-for="tag in note.tags"
               :href="'/tag/' + tag"
               class="mr-2 border border-black rounded-full px-2 py-0.5 text-sm group relative">
                {{ tag }}
                <a class="hidden group-hover:inline absolute right-1 group-hover:bg-gray-300 rounded-full px-1 z-20"
                      @click.prevent="detach_tag(tag)">
                    <i class="bi bi-x icon"></i>
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
            <div class="tooltip reminders-dropdown-tooltip">
                <a href="" class="hover:bg-gray-300 p-2 rounded-full" @click.prevent="showRemindersDropdown()">
                    <svg class="icon icon-sm icon-bell" viewBox="0 0 32 32">
                        <path
                            d="M32.047 25c0-9-8-7-8-14 0-0.58-0.056-1.076-0.158-1.498-0.526-3.532-2.88-6.366-5.93-7.23 0.027-0.123 0.041-0.251 0.041-0.382 0-1.040-0.9-1.891-2-1.891s-2 0.851-2 1.891c0 0.131 0.014 0.258 0.041 0.382-3.421 0.969-5.966 4.416-6.039 8.545-0.001 0.060-0.002 0.121-0.002 0.183 0 7-8 5-8 14 0 2.382 5.331 4.375 12.468 4.878 0.673 1.263 2.002 2.122 3.532 2.122s2.86-0.86 3.532-2.122c7.137-0.503 12.468-2.495 12.468-4.878 0-0.007-0.001-0.014-0.001-0.021l0.048 0.021zM25.82 26.691c-1.695 0.452-3.692 0.777-5.837 0.958-0.178-2.044-1.893-3.648-3.984-3.648s-3.805 1.604-3.984 3.648c-2.144-0.18-4.142-0.506-5.837-0.958-2.332-0.622-3.447-1.318-3.855-1.691 0.408-0.372 1.523-1.068 3.855-1.691 2.712-0.724 6.199-1.122 9.82-1.122s7.109 0.398 9.82 1.122c2.332 0.622 3.447 1.318 3.855 1.691-0.408 0.372-1.523 1.068-3.855 1.691z"></path>
                    </svg>
                </a>
                <span class="tooltiptext" @click.stop.prevent>Remind me</span>
                <div class="dropdown">
                    <div class="dropdown-content p-0 rounded-md bg-clip-padding" v-if="remindersDropdownShown" style="width: 300px">
                        <p class="text-lg p-2 pl-4 font-bold">Reminder:</p>
                        <button @click="storeReminder('later_today')" v-if="isLaterTodayVisible" class="dropdown-item focus:outline-none d-block w-full p-3 pl-4 text-left hover:bg-gray-200 relative">
                            Later today
                            <span class="absolute right-4 text-gray-500">8:00 PM</span>
                        </button>
                        <button @click="storeReminder('tomorrow')" class="dropdown-item focus:outline-none d-block w-full p-3 pl-4 text-left hover:bg-gray-200 relative">
                            Tomorrow
                            <span class="absolute right-4 text-gray-500">8:00 AM</span>
                        </button>
                        <button @click="storeReminder('next_week')" class="dropdown-item focus:outline-none d-block w-full p-3 pl-4 text-left hover:bg-gray-200 relative">
                            Next week
                            <span class="absolute right-4 text-gray-500">Mon., 8:00 AM</span>
                        </button>
                        <button @click="" class="dropdown-item focus:outline-none d-block w-full p-3 pl-4 text-left hover:bg-gray-200">
                            <svg class="icon icon-small icon-alarm mr-3" viewBox="0 0 32 32">
                                <path d="M16 4c-7.732 0-14 6.268-14 14s6.268 14 14 14 14-6.268 14-14-6.268-14-14-14zM16 29.25c-6.213 0-11.25-5.037-11.25-11.25s5.037-11.25 11.25-11.25c6.213 0 11.25 5.037 11.25 11.25s-5.037 11.25-11.25 11.25zM29.212 8.974c0.501-0.877 0.788-1.892 0.788-2.974 0-3.314-2.686-6-6-6-1.932 0-3.65 0.913-4.747 2.331 4.121 0.851 7.663 3.287 9.96 6.643v0zM12.748 2.331c-1.097-1.418-2.816-2.331-4.748-2.331-3.314 0-6 2.686-6 6 0 1.082 0.287 2.098 0.788 2.974 2.297-3.356 5.838-5.792 9.96-6.643z"></path>
                                <path d="M16 18v-8h-2v10h8v-2z"></path>
                            </svg>
                            Pick date & time
                        </button>
                        <button @click="" class="dropdown-item focus:outline-none d-block w-full p-3 pl-4 text-left hover:bg-gray-200">
                            <svg class="icon icon-small icon-location2 mr-3" viewBox="0 0 32 32">
                                <path d="M16 0c-5.523 0-10 4.477-10 10 0 10 10 22 10 22s10-12 10-22c0-5.523-4.477-10-10-10zM16 16.125c-3.383 0-6.125-2.742-6.125-6.125s2.742-6.125 6.125-6.125 6.125 2.742 6.125 6.125-2.742 6.125-6.125 6.125zM12.125 10c0-2.14 1.735-3.875 3.875-3.875s3.875 1.735 3.875 3.875c0 2.14-1.735 3.875-3.875 3.875s-3.875-1.735-3.875-3.875z"></path>
                            </svg>
                            Pick place
                        </button>
                    </div>
                </div>
            </div>

            <a href="" class="hover:bg-gray-300 p-2 rounded-full"
               v-b-tooltip.hover.bottom
               title="Collaborator"
               @click.prevent="showCollaboratorsDialog()">
                <i class="bi bi-person-plus icon-sm"></i>
            </a>

            <a href="" class="hover:bg-gray-300 p-2 rounded-full"
               v-b-tooltip.hover.bottom
               title="Change color"
               @click.prevent>
                <i class="bi bi-palette icon-sm"></i>
            </a>

<!--            <div class="tooltip">   TODO: Problems with color circles
                <div class="vertical-tooltiptext rounded-md"> &lt;!&ndash;TODO: Color circles' tooltips work another way - they're smaller and they're closed when the cursor is out of the color circle (even when it was on the tooltip). So when the cursor is on the tooltip, it will be closed.&ndash;&gt;
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
            </div>-->


            <a href="" class="hover:bg-gray-300 p-2 rounded-full"
               v-b-tooltip.hover.bottom
               title="Add image"
               @click.prevent="selectImage()">
                <i class="bi bi-image icon-sm"></i>
            </a>

            <input type="file" ref="image" class="hidden" accept="image/jpeg,image/png,image/gif"
                       @change="handleFiles()">


            <a href="" class="hover:bg-gray-300 p-2 rounded-full"
               v-b-tooltip.hover.bottom
               title="Unarchive"
               @click.prevent="unarchive()"
               v-if="note.archived">
                <i class="bi bi-save2-fill icon-sm"></i>
            </a>

            <a href="" class="hover:bg-gray-300 p-2 rounded-full"
               v-b-tooltip.hover.bottom
               title="Archive"
               @click.prevent="archive()"
               v-else>
                <i class="bi bi-save2-fill icon-sm"></i>
            </a>

            <div class="tooltip dropdown-tooltip">
                <a href="" class="hover:bg-gray-300 p-2 rounded-full dropdown-opener" @click.prevent="showDropdown()">
                    <svg class="icon icon-small icon-ellipsis-v" viewBox="0 0 6 28">
                        <path
                            d="M6 19.5v3c0 0.828-0.672 1.5-1.5 1.5h-3c-0.828 0-1.5-0.672-1.5-1.5v-3c0-0.828 0.672-1.5 1.5-1.5h3c0.828 0 1.5 0.672 1.5 1.5zM6 11.5v3c0 0.828-0.672 1.5-1.5 1.5h-3c-0.828 0-1.5-0.672-1.5-1.5v-3c0-0.828 0.672-1.5 1.5-1.5h3c0.828 0 1.5 0.672 1.5 1.5zM6 3.5v3c0 0.828-0.672 1.5-1.5 1.5h-3c-0.828 0-1.5-0.672-1.5-1.5v-3c0-0.828 0.672-1.5 1.5-1.5h3c0.828 0 1.5 0.672 1.5 1.5z"></path>
                    </svg>
                </a>
                <span class="tooltiptext more-button" @click.stop.prevent>More</span>
                <div class="dropdown more-dropdown">
                    <div class="dropdown-content p-0 rounded-md bg-clip-padding" v-if="dropdownShown">
                        <button @click="deleteNote()" class="dropdown-item focus:outline-none d-block w-full p-2 pl-4 text-left hover:bg-gray-200">Delete note</button>
                        <button class="dropdown-item focus:outline-none d-block w-full p-2 pl-4 text-left hover:bg-gray-200">Add label</button>
                        <button class="dropdown-item focus:outline-none d-block w-full p-2 pl-4 text-left hover:bg-gray-200">Add drawing</button>
                        <button @click="copy()" class="dropdown-item focus:outline-none d-block w-full p-2 pl-4 text-left hover:bg-gray-200">Make a copy</button>
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
import moment from 'moment';

export default {
    name: "NoteComponent",
    data() {
        return {
            editing: false,
            shown: true,
            dropdownShown: false,
            isCollaboratorsDialogVisible: false,
            isDeleteConfirmationVisible: false,
            remindersDropdownShown: false,
            isLaterTodayVisible: false,
            colors: [
                'white', 'red', 'orange', 'yellow',
                'green', 'teal', 'blue', 'dark-blue',
                'purple', 'pink', 'brown', 'grey'
            ],
            trashed: this.$attrs.istrashed,
            note: JSON.parse(this.$attrs.note)
        };
    },
    created() {
        setInterval(this.checkLaterTodayVisibility,500);

        //Close all dropdowns
        window.addEventListener("click", function (event) {
            window.events.$emit('close_dropdown');
            window.events.$emit('close_reminders_dropdown');

            let collection = document.getElementsByClassName('dropdown-tooltip');

            Array.prototype.filter.call(collection, function(element){
                if(element.contains(event.target))
                    window.events.$emit('open_dropdown', element);
            });

            let collection2 = document.getElementsByClassName('reminders-dropdown-tooltip');

            Array.prototype.filter.call(collection2, function(element){
                if(element.contains(event.target))
                    window.events.$emit('open_reminders_dropdown', element);
            });
        });

        window.events.$on('close_dropdown', this.hideDropdown);
        window.events.$on('close_reminders_dropdown', this.hideRemindersDropdown);
        window.events.$on('open_dropdown', this.openDropdown);
        window.events.$on('open_reminders_dropdown', this.openRemindersDropdown);

        window.events.$on('refresh_image', this.refreshImage);
    },
    methods: {
        copy()
        {
            axios.post('/note/duplicate/' + this.note.id)
                 .then(res => window.duplicatedNoteId = res.data.id);
            window.events.$emit('show-notification', 'Note created', this.undoCopy);
        },
        undoCopy()
        {
            axios.delete('note/' + window.duplicatedNoteId);
            axios.delete('note/' + window.duplicatedNoteId); //force delete
            window.events.$emit('show-notification', 'Action undone');
        },
        checkLaterTodayVisibility()
        {
          let evening = (new Date).setHours(19, 0, 0);
          this.isLaterTodayVisible = Date.now() < evening;
        },
        storeReminder(text_time)
        {
            let time = {
                'later_today': moment().set({'hour': 20}),
                'tomorrow': moment().add(1, 'days').set({'hour': 8}),
                'next_week': moment().add(1, 'weeks').set({'day' : 'Monday', 'hour': 8}),
            };

            let formatted_time = time[text_time].set({'minute': 0, 'second':0})
                                                .format('YYYY-MM-DD HH:mm:ss');
            this.hideRemindersDropdown();

            axios.post('/reminder/' + this.note.id, {'time' : formatted_time} );
            this.note.reminder_json = {'time' : formatted_time};
        },
        removeReminder()
        {
            axios.delete('/reminder/' + this.note.id);

            window.ReminderNoteId = this.note.id;
            window.ReminderTime = this.note.reminder_json.time;

            this.note.reminder_json = null;

            if( location.href.includes('/reminder') )
                this.shown = false;

            window.events.$emit('show-notification', 'Reminder deleted', this.undoReminderRemoval);
        },
        undoReminderRemoval()
        {
            axios.post('/reminder/' + window.ReminderNoteId,  {'time' : window.ReminderTime} );
            this.note.reminder_json = {'time' : window.ReminderTime};

            if( location.href.includes('/reminder') )
                this.shown = true;

            window.events.$emit('show-notification', 'Action undone');
        },
        getReminderTime()
        {
            let reminder_date = this.note.reminder_json.time;
            if (moment(reminder_date).year() > moment().year())
                return moment(reminder_date).format('MMM D, YYYY, H:mm A');

            return moment(reminder_date).format('MMM D, H:mm A');
        },
        refreshImage(data)
        {
            if(Object.is(this, window.newImageComponent))
                this.note.images_json.push(data);
        },
        selectImage()
        {
            this.$refs['image'].click();
        },
        handleFiles()
        {
            let image = this.$refs['image'].files[0];

            let data = new FormData();
            data.append('image', image, image.name);
            data.append('note_id', this.note.id);

            window.newImageComponent = this;

            axios.post('/image', data).then(function(result) {
                window.events.$emit('refresh_image', result.data);
            });
        },
        openDropdown(element)
        {
            if(this.$refs.note.contains(element))
                this.showDropdown();
        },
        openRemindersDropdown(element)
        {
            if(this.$refs.note.contains(element))
                this.showRemindersDropdown();
        },
        showDropdown()
        {
            this.dropdownShown = true;
        },
        showRemindersDropdown()
        {
            this.remindersDropdownShown = true;
        },
        hideRemindersDropdown()
        {
            this.remindersDropdownShown = false;
        },
        hideDropdown(){
            this.dropdownShown = false;
        },
        pin() {
            if(this.note.pinned) {
                axios.put('/note/' + this.note.id, {'pinned': false} );
                document.querySelector('div.others').appendChild(this.$refs.note);
            } else {
                axios.put('/note/' + this.note.id, {'pinned' : true} );
                document.querySelector('div.pinned').appendChild(this.$refs.note);
            }

            this.note.pinned = !this.note.pinned;
        },
        isActive(color) {
            return (this.note.color === color) ? 'active' : '';
        },
        changeColor(color) {
            this.note.color = color;
            axios.put('/note/' + this.note.id, {'color' : color} );
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
            this.shown = false;//TODO: There should be animation while hiding a note

            window.events.$emit('show-notification', 'Note restored', this.undoRestore);
        },
        deleteNote(){
            axios.delete('note/' + this.note.id);
            this.shown = false;

            window.events.$emit('show-notification', 'Note deleted', this.undoDelete);
        },
        undoDelete(){
            axios.post('note/restore/' + this.note.id);
            this.shown = true;

            window.events.$emit('show-notification', 'Action undone');
        },
        undoRestore(){
            axios.delete('note/' + this.note.id);
            this.shown = true;

            window.events.$emit('show-notification', 'Action undone');
        },
        archive() {
            axios.put('/note/' + this.note.id, {'archived' : true} );

            this.shown = false;
            window.events.$emit('show-notification', 'Note archived', this.undo_archive);
        },
        unarchive() {
            axios.put('/note/' + this.note.id, {'archived' : false} );

            this.shown = false;
            window.events.$emit('show-notification', 'Note unarchived', this.undo_unarchive);
        },
        undo_archive() {
            axios.put('/note/' + this.note.id, {'archived' : false} );

            this.shown = true;
            window.events.$emit('show-notification', 'Action undone');
        },
        undo_unarchive() {
            axios.put('/note/' + this.note.id, {'archived' : true} );

            this.shown = true;
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
               this.shown = false;
        }
    }
}
</script>

<style scoped>

</style>
