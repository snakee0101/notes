<template> <!--TODO: There must be UNDO and REDO buttons while editing the note-->
    <!--TODO: a note or a group of notes could be selected and actions panel should appear instead of top bar-->
    <div class="note border border-gray-300 p-3 hover:shadow-md relative transition-colors"
         :class="'bg-google-' + note.color"
         v-if="shown"
         ref="note">
        <a href="" class="absolute right-2 top-2 hover:bg-gray-300 p-1 rounded-full" @click.prevent="pin()"
           v-if="!trashed">
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
                <span ref="updated_reminder_time">{{ getReminderTime() }}</span>
                <a class="hidden group-hover:inline absolute right-1 group-hover:bg-gray-300 rounded-full z-20"
                   v-b-tooltip.hover.bottom
                   title="Remove reminder"
                   @click.prevent="removeReminder()">
                    <i class="bi bi-x icon"></i>
                </a>
            </a>
            <a v-for="tag in note.tags"
               :href="'/tag/' + tag"
               class="mr-2 border border-black rounded-full px-2 py-0.5 text-sm group relative">
                {{ tag }}
                <a class="hidden group-hover:inline absolute right-1 group-hover:bg-gray-300 rounded-full z-20"
                   v-b-tooltip.hover.bottom
                   title="Remove label"
                   @click.prevent="detach_tag(tag)">
                    <i class="bi bi-x icon"></i>
                </a>
            </a>
        </div>

        <div class="toolbar flex justify-between" v-if="trashed">
            <button
                @click="restore()"
                class="restore-button">
                Restore
            </button>
            <button
                @click="$refs['delete-confirmation'].show()"
                class="delete-forever-button">
                Delete Forever
            </button>
        </div>

        <div class="toolbar" v-else>
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
                                     @click="$refs['dateTimePicker-modal'].show(); $refs['reminder-dropdown'].hide()"
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

            <a href="" class="hover:bg-gray-300 rounded-full p-0 inline-block"
               v-b-tooltip.hover.bottom
               title="More"
               @click.prevent>
                <b-dropdown size="sm" variant="link" toggle-class="text-decoration-none" no-caret>
                    <template #button-content>
                        <i class="bi bi-three-dots-vertical icon-sm p-0"></i>
                    </template>
                    <b-dropdown-item href="#" @click="deleteNote()">Delete note</b-dropdown-item>
                    <b-dropdown-item href="#">Add label</b-dropdown-item>
                    <b-dropdown-item href="#">Add drawing</b-dropdown-item>
                    <b-dropdown-item href="#" @click="copy()">Make a copy</b-dropdown-item>
                    <b-dropdown-item href="#">Show checkboxes</b-dropdown-item>
                </b-dropdown>
            </a>
        </div>

        <b-modal title="BootstrapVue" ref="dateTimePicker-modal"
                 centered hide-footer modal-class="dateTimePicker-modal">
            <p class="text-lg font-bold">
                <a href=""
                   v-b-tooltip.hover.bottom
                   title="Go back"
                   @click.prevent="$refs['dateTimePicker-modal'].hide(); $refs['reminder-dropdown'].show()">
                    <i class="bi bi-arrow-left mr-3"></i>
                </a>Pick date & time
            </p>
            <div>
                <p class="m-0 mb-2 font-bold">Select date</p>
                <b-form-datepicker v-model="pickedDate"></b-form-datepicker>
            </div>
            <div class="mt-4">
                <p class="mb-2 font-bold">Select time</p>
                <b-form-timepicker v-model="pickedTime" locale="en"></b-form-timepicker>
            </div>
            <div class="mt-4">
                <p class="mb-2 font-bold">Select repeat status</p>
                <b-form-select v-model="repeatStatus" class="mb-3" selected="Doesn't repeat" @change="showCustomRepeatOptions()">
                    <b-form-select-option value="Doesn't repeat">Doesn't repeat</b-form-select-option>
                    <b-form-select-option value="Daily">Daily</b-form-select-option>
                    <b-form-select-option value="Weekly">Weekly</b-form-select-option>
                    <b-form-select-option value="Monthly">Monthly</b-form-select-option>
                    <b-form-select-option value="Yearly">Yearly</b-form-select-option>
                    <b-form-select-option value="Custom">Custom</b-form-select-option>
                </b-form-select>
            </div>
            <div v-if="customRepeatStatusShown" class="border-2 border-green-600 mb-2 p-2">
                <div class="flex justify-content-between">
                    <p class="font-bold">Repeat every: </p>
                    <div>
                        <input type="text" size="2" class="p-1 border-b border-gray-500 focus:outline-none text-center"
                               v-model="repeat_every_value">
                        <select v-model="repeat_every_unit" @change="showWeekdays()">
                            <option value="day">Day</option>
                            <option value="week">Week</option>
                            <option value="month">Month</option>
                            <option value="Year">Year</option>
                        </select>
                    </div>
                </div>

                <div v-if="weekdaysShown" class="weekdaysButtons mt-2">
                    <b-form-group>
                        <b-form-checkbox-group
                            v-model="weekdays"
                            :options="weekdaysOptions"
                            buttons
                            button-variant="success"
                        ></b-form-checkbox-group>
                    </b-form-group>
                </div>

                <div class="flex justify-content-between">
                    <p class="font-bold">Ends: </p>
                    <div>
                        <p>
                            <label>
                                <input type="radio" name="repeat_ends" v-model="repeat_ends" value="never"> Never
                            </label>
                        </p>
                        <p>
                            <label>
                                <input type="radio" name="repeat_ends" v-model="repeat_ends" id="occurrences" value="occurrences"
                                       ref="occurrences_switch">
                                After
                                    <input type="text" v-model="repeat_occurrences"
                                           size="2" class="border-b border-gray-500 focus:outline-none text-center"
                                           @focus="$refs['occurrences_switch'].checked = true">
                                occurrences
                            </label>
                        </p>
                        <p>
                            <label>
                                <input type="radio" name="repeat_ends" v-model="repeat_ends" value="date">
                                On:  <b-form-datepicker v-model="pickedRepeatsDate"></b-form-datepicker>
                            </label>
                        </p>
                    </div>
                </div>
            </div>
            <p class="text-right m-0">
                <b-button variant="primary" @click="saveReminder()">Save</b-button>
            </p>
        </b-modal>

        <collaborator-dialog-component :note="note"
                                       :emails="note.collaborators_json"
                                       :owner="note.owner_json">

        </collaborator-dialog-component>

        <b-modal ref="delete-confirmation" hide-footer centered class="delete-confirmation">
            <p class="m-2">Delete note forever?</p>
            <div class="bg-white rounded-b-lg text-right">
                <button @click="$refs['delete-confirmation'].hide()" class="cancel-button">
                    Cancel
                </button>
                <button @click="delete_forever()" class="delete-button">
                    Delete
                </button>
            </div>
        </b-modal>
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
            pickedDate: '',
            pickedTime: '',
            pickedRepeatsDate: '',
            repeatStatus: '',
            isCollaboratorsDialogVisible: false,
            customRepeatStatusShown: false,
            isLaterTodayVisible: false,
            colors: [
                'white', 'red', 'orange', 'yellow',
                'green', 'teal', 'blue', 'dark-blue',
                'purple', 'pink', 'brown', 'grey'
            ],
            trashed: this.$attrs.istrashed,
            note: JSON.parse(this.$attrs.note),
            repeat_ends: 'never',
            repeat_occurrences: 1,
            repeat_every_value: 1,
            repeat_every_unit: 'Day',
            weekdaysShown: false,
            weekdays: [],
            weekdaysOptions: [
                { text: 'Mon', value: 'Monday' },
                { text: 'Tue', value: 'Tuesday' },
                { text: 'Wed', value: 'Wednesday' },
                { text: 'Thu', value: 'Thursday' },
                { text: 'Fri', value: 'Friday' },
                { text: 'Sat', value: 'Saturday' },
                { text: 'Sun', value: 'Sunday' },
            ]
        };
    },
    created() {
        setInterval(this.checkLaterTodayVisibility, 500);

        window.events.$on('refresh_image', this.refreshImage);
        window.events.$on('update_reminder_label', this.updateReminderLabel);
    },
    methods: {
        updateReminderLabel(noteId, time) {
            if(noteId == this.note.id)
                this.$refs['updated_reminder_time'].innerHTML = this.getReminderTime(time);
        },
        saveReminder() {
            let repeatStatus = '';  //TODO: check repeat status
            let time = this.pickedDate + ' ' + this.pickedTime;

            axios.post('/reminder/' + this.note.id, {
                time : time,
                repeat: repeatStatus
            });

            window.events.$emit('update_reminder_label', this.note.id, time);
            this.$refs['dateTimePicker-modal'].hide();
            //TODO: Update reminder label in realtime with event
            //TODO: Load values from database in initial render
        },
        showWeekdays() {
            this.weekdaysShown = (this.repeat_every_unit === 'week');
        },
        showCustomRepeatOptions() {
            this.customRepeatStatusShown = (this.repeatStatus === 'Custom');
        },
        copy() {
            axios.post('/note/duplicate/' + this.note.id)
                .then(res => window.duplicatedNoteId = res.data.id);
            window.events.$emit('show-notification', 'Note created', this.undoCopy);
        },
        undoCopy() {
            axios.delete('note/' + window.duplicatedNoteId);
            axios.delete('note/' + window.duplicatedNoteId); //force delete
            window.events.$emit('show-notification', 'Action undone');
        },
        checkLaterTodayVisibility() {
            let evening = (new Date).setHours(19, 0, 0);
            this.isLaterTodayVisible = Date.now() < evening;
        },
        storeReminder(text_time) {
            let time = {
                'later_today': moment().set({'hour': 20}),
                'tomorrow': moment().add(1, 'days').set({'hour': 8}),
                'next_week': moment().add(1, 'weeks').set({'day': 'Monday', 'hour': 8}),
            };

            let formatted_time = time[text_time].set({'minute': 0, 'second': 0})
                .format('YYYY-MM-DD HH:mm:ss');
            this.hideRemindersDropdown();

            axios.post('/reminder/' + this.note.id, {'time': formatted_time});
            this.note.reminder_json = {'time': formatted_time};
        },
        removeReminder() {
            axios.delete('/reminder/' + this.note.id);

            window.ReminderNoteId = this.note.id;
            window.ReminderTime = this.note.reminder_json.time;

            this.note.reminder_json = null;

            if (location.href.includes('/reminder'))
                this.shown = false;

            window.events.$emit('show-notification', 'Reminder deleted', this.undoReminderRemoval);
        },
        undoReminderRemoval() {
            axios.post('/reminder/' + window.ReminderNoteId, {'time': window.ReminderTime});
            this.note.reminder_json = {'time': window.ReminderTime};

            if (location.href.includes('/reminder'))
                this.shown = true;

            window.events.$emit('show-notification', 'Action undone');
        },
        getReminderTime(time) {
            let reminder_date = this.note.reminder_json.time;
            if(time)
                reminder_date = time;

            if (moment(reminder_date).year() > moment().year())
                return moment(reminder_date).format('MMM D, YYYY, H:mm A');

            return moment(reminder_date).format('MMM D, H:mm A');
        },
        refreshImage(data) {
            if (Object.is(this, window.newImageComponent))
                this.note.images_json.push(data);
        },
        selectImage() {
            this.$refs['image'].click();
        },
        handleFiles() {
            let image = this.$refs['image'].files[0];

            let data = new FormData();
            data.append('image', image, image.name);
            data.append('note_id', this.note.id);

            window.newImageComponent = this;

            axios.post('/image', data).then(function (result) {
                window.events.$emit('refresh_image', result.data);
            });
        },
        openRemindersDropdown(element) {
            if (this.$refs.note.contains(element))
                this.showRemindersDropdown();
        },
        pin() {
            if (this.note.pinned) {
                axios.put('/note/' + this.note.id, {'pinned': false});
                document.querySelector('div.others').appendChild(this.$refs.note);
            } else {
                axios.put('/note/' + this.note.id, {'pinned': true});
                document.querySelector('div.pinned').appendChild(this.$refs.note);
            }

            this.note.pinned = !this.note.pinned;
        },
        isActive(color) {
            return (this.note.color === color) ? 'active' : '';
        },
        changeColor(color) {
            this.note.color = color;
            axios.put('/note/' + this.note.id, {'color': color});
        },
        showCollaboratorsDialog() {
            window.events.$emit('show-collaborators-dialog', this.note.id);
        },
        restore() {
            axios.post('note/restore/' + this.note.id);
            this.shown = false;//TODO: There should be animation while hiding a note

            window.events.$emit('show-notification', 'Note restored', this.undoRestore);
        },
        deleteNote() {
            axios.delete('note/' + this.note.id);
            this.shown = false;

            window.events.$emit('show-notification', 'Note deleted', this.undoDelete);
        },
        undoDelete() {
            axios.post('note/restore/' + this.note.id);
            this.shown = true;

            window.events.$emit('show-notification', 'Action undone');
        },
        undoRestore() {
            axios.delete('note/' + this.note.id);
            this.shown = true;

            window.events.$emit('show-notification', 'Action undone');
        },
        archive() {
            axios.put('/note/' + this.note.id, {'archived': true});

            this.shown = false;
            window.events.$emit('show-notification', 'Note archived', this.undo_archive);
        },
        unarchive() {
            axios.put('/note/' + this.note.id, {'archived': false});

            this.shown = false;
            window.events.$emit('show-notification', 'Note unarchived', this.undo_unarchive);
        },
        undo_archive() {
            axios.put('/note/' + this.note.id, {'archived': false});

            this.shown = true;
            window.events.$emit('show-notification', 'Action undone');
        },
        undo_unarchive() {
            axios.put('/note/' + this.note.id, {'archived': true});

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
            axios.delete('/detach_tag/' + this.note.id + '/' + tag);
            let index = this.note.tags.indexOf(tag);
            this.note.tags.splice(index, 1);

            let tagsLocation = 'tag/' + encodeURIComponent(tag);
            if (location.href.includes(tagsLocation))
                this.shown = false;
        }
    }
}
</script>

<style scoped>

</style>
