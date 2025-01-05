<template>
    <div class="note p-3 hover:shadow-md relative transition-colors mb-4 group"
         :class="('bg-google-' + note.color) + ' ' + (selected ? 'border-black' : 'border-gray-200')"
         ref="note" :data-note-id="note.id">
        <a href="" class="absolute right-2 top-2 p-1 rounded-full" @click.prevent="togglePin()"
           v-if="!trashed">
            <i class="bi bi-pin-fill icon text-black"
               v-b-tooltip.hover.bottom title="Unpin" v-if="note.pinned"></i>

            <i class="bi bi-pin icon text-black"
               v-b-tooltip.hover.bottom title="Pin" v-else></i>
        </a>

        <a href="" class="absolute bg-black rounded-full" style="top: -0.5rem; left: -0.5rem; padding: 0.1rem"
           @click.prevent="toggleSelect()"
           v-if="selected">
            <i class="bi bi-check icon-sm text-white"
               v-b-tooltip.hover.bottom title="Deselect note"></i>
        </a>

        <a href="" class="absolute bg-black rounded-full hidden group-hover:inline-block" style="top: -0.5rem; left: -0.5rem; padding: 0.1rem"
           @click.prevent="toggleSelect()"
           v-else>
            <i class="bi bi-check icon-sm text-white"
               v-b-tooltip.hover.bottom title="Select note"></i>
        </a>

        <div @click="openForEditing" style="cursor: pointer">
            <div class="images flex justify-stretch">
                <img :src="'data:image/jpg;base64,' + drawing.thumbnail_encoded" v-for="drawing in note.drawings.slice(0,2)" 
                     :style="{'max-width': note.drawings.length == 1 ? '100%' : '50%'}">
            </div>
            <p v-if="note.drawings.length > 2">and {{ note.drawings.length - 2 }} more</p>
            <div class="images flex justify-stretch">
                <img :src="'data:image/jpg;base64,' + image.thumbnail_encoded" v-for="image in note.images.slice(0,2)"
                     :style="{'max-width': note.images.length == 1 ? '100%' : '50%'}">
            </div>
            <p v-if="note.images.length > 2">and {{ note.images.length - 2 }} more</p>
            <h3 class="font-bold mr-3 break-words">{{ note.header }}</h3>

            <div v-if="note.checklist && note.checklist.tasks.length > 0">
                <div class="form-check mb-2 flex flex-row" v-for="(task, index) in note.checklist.tasks.slice(0,4)">
                    <input class="form-check-input mt-2" type="checkbox" :checked="task.completed" disabled>
                    {{ task.text }}
                </div>

                <p v-if="note.checklist.tasks.length > 4">...</p>
            </div>

            <div v-html="note.body"
                 class="note-content my-4 leading-6 overflow-hidden break-words"
                 style="max-height: 300px"
                 v-else>
            </div>
        </div>

        <div class="tags mb-4">
            <a v-if="note.reminder"
               @click.self.prevent="pickDateAndTime()"
               href="#"
               class="inline-block mr-2 rounded-full pl-2 pr-1 py-0 text-sm mb-2 pill">
                <i class="bi bi-alarm icon" @click.self.prevent="pickDateAndTime()"></i>
                <span ref="updated_reminder_time" @click.self.prevent="pickDateAndTime()">{{ this.remainder_time_formatted }}</span>
                <a class="x-button rounded-full"
                   v-b-tooltip.hover.bottom
                   title="Remove reminder"
                   @click.prevent="removeReminder()">
                    <i class="bi bi-x icon"></i>
                </a>
            </a>
            <a v-for="tag in note.tags"
               :href="'/tag/' + tag.name"
               class="inline-block mr-2 rounded-full pl-2 pr-1 py-0 text-sm mb-2 pill">
                {{ tag.name }}
                <a class="x-button rounded-full"
                   v-b-tooltip.hover.bottom
                   title="Remove label"
                   @click.prevent="detach_tag(tag)">
                    <i class="bi bi-x icon"></i>
                </a>
            </a>
            <a v-for="collaborator in note.collaborators" href="#"
               @click.prevent="showCollaboratorsDialog()"
               class="inline-block mr-2 rounded-full px-2 py-0 text-sm group pill">
                Shared with {{ collaborator.email }}
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
            <a href="" class="rounded-full p-0 inline-block"
               v-b-tooltip.hover.bottom
               title="Remind me"
               @click.prevent>
                <b-dropdown size="sm" variant="link" toggle-class="" no-caret ref="reminder-dropdown" v-on:show="checkLaterTodayVisibility()">
                    <template #button-content>
                        <i class="bi bi-bell icon-sm p-0"></i>
                    </template>
                    <p class="text-lg p-2 pl-4 m-0 font-bold">Reminder:</p>
                    <b-dropdown-item href="#" @click="storeReminder('later_today')"
                                     class="focus:outline-none" v-if="isLaterTodayVisible">
                        Later today
                        <span class="text-gray-500">8:00 PM</span>
                    </b-dropdown-item>
                    <b-dropdown-item href="#" @click="storeReminder('tomorrow')"
                                     class="focus:outline-none">
                        Tomorrow
                        <span class="text-gray-500">8:00 AM</span>
                    </b-dropdown-item>
                    <b-dropdown-item href="#" @click="storeReminder('next_week')"
                                     class="focus:outline-none">
                        Next week
                        <span class="text-gray-500">Mon., 8:00 AM</span>
                    </b-dropdown-item>
                    <b-dropdown-item href="#"
                                     @click="pickDateAndTime()"
                                     class="focus:outline-none">
                        <i class="bi bi-alarm-fill mr-3"></i>
                        Pick date & time
                    </b-dropdown-item>
                </b-dropdown>
            </a>

            <a href="" class="p-2 rounded-full"
               v-b-tooltip.hover.bottom
               title="Collaborator"
               @click.prevent="showCollaboratorsDialog()">
                <i class="bi bi-person-plus icon-sm"></i>
            </a>

            <div class="custom-tooltip">
                <a href="" class="p-2 rounded-full"
                   v-b-tooltip.hover.bottom
                   title="Change color"
                   @click.prevent>
                    <i class="bi bi-palette icon-sm"></i>
                </a>
                <div class="vertical-tooltiptext ">
                    <a v-for="color in colors"
                       href=""
                       :class="'rounded-md color-circle bg-google-' + color + ' ' + isColorActive(color)"
                       @click.prevent="changeColor(color)">
                        <i class="bi bi-check icon-sm"></i>
                    </a>
                </div>
            </div>

            <a href="" class="p-2 rounded-full"
               v-b-tooltip.hover.bottom
               title="Add image"
               @click.prevent="selectImage()">
                <i class="bi bi-image icon-sm"></i>
            </a>

            <input type="file" ref="image" class="hidden" accept="image/jpeg,image/png,image/gif"
                   @change="handleFiles()">


            <a href="" class="p-2 rounded-full"
               v-b-tooltip.hover.bottom
               title="Unarchive"
               @click.prevent="unarchive()"
               v-if="note.archived">
                <i class="bi bi-arrow-up-square icon-sm"></i>
            </a>

            <a href="" class="p-2 rounded-full"
               v-b-tooltip.hover.bottom
               title="Archive"
               @click.prevent="archive()"
               v-else>
                <i class="bi bi-arrow-down-square-fill icon-sm"></i>
            </a>

            <a href="" class="rounded-full p-0 inline-block"
               v-b-tooltip.hover.bottom
               title="More"
               @click.prevent>
                <b-dropdown size="sm" variant="link" toggle-class="text-decoration-none" no-caret>
                    <template #button-content>
                        <i class="bi bi-three-dots-vertical icon-sm p-0"></i>
                    </template>
                    <b-dropdown-item href="#" @click="deleteNote()">Delete note</b-dropdown-item>
                    <b-dropdown-item href="#" @click="openSetLabelsDialog()">Add label</b-dropdown-item>
                    <b-dropdown-item href="#" @click="add_drawing()">Add drawing</b-dropdown-item>
                    <b-dropdown-item href="#" @click="copy()">Make a copy</b-dropdown-item>

                    <b-dropdown-item href="#" @click="convertToText()" v-if="note.checklist">Hide checkboxes</b-dropdown-item>
                    <b-dropdown-item href="#" @click="convertToChecklist()" v-else>Show checkboxes</b-dropdown-item>
                </b-dropdown>
            </a>
        </div>

        <div v-for="link in note.links" class="mt-1">
            <div class="flex flex-row mt-2 items-center">
                <img style="height: 40px; width: 40px"
                     :src="link.favicon_path" alt="" v-if="link.favicon_path">
                <i class="bi bi-globe text-3xl text-center mt-1" style="height: 40px; width: 40px" v-else></i>
                <div class="ml-2 flex-grow overflow-hidden">
                    <h6 class="m-0 mb-1">{{ link.name }}</h6>
                    <p class="text-sm m-0 text-gray-400">{{ link.domain }}</p>
                </div>
                <div>
                    <a :href="link.url"
                       target="_blank"
                       v-b-tooltip.hover.bottom
                       title="Go to link">
                        <i class="bi bi-box-arrow-up-right"></i>
                    </a>
                </div>
            </div>
        </div>

        <set-labels-component :note="note">

        </set-labels-component>

        <b-modal title="BootstrapVue" ref="dateTimePicker-modal"
                 centered hide-footer modal-class="dateTimePicker-modal" id="dateTimePicker-modal">
            <p class="text-lg font-bold">
                <a href=""
                   v-b-tooltip.hover.bottom
                   title="Go back"
                   @click.prevent="$bvModal.hide('dateTimePicker-modal')">
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
                                       :owner="note.owner">

        </collaborator-dialog-component>

        <b-modal ref="delete-confirmation" hide-footer centered class="delete-confirmation">
            <p class="m-3">Delete note forever?</p>
            <div class="bg-white rounded-b-lg text-right p-2">
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
import SetLabelsComponent from "./SetLabelsComponent";

let reminders = require('./mixins/reminders');
let note_deletion = require('./mixins/note_deletion');
let note_archiving = require('./mixins/note_archiving');
let note_duplication = require('./mixins/note_duplication');

let _ = {
    without : require('lodash/without')
};

export default {
    name: "NoteComponent",
    mixins: [reminders, note_deletion, note_archiving, note_duplication],
    components: {SetLabelsComponent},
    props: ['isTrashed'],
    data() {
        return {
            isCollaboratorsDialogVisible: false,
            colors: [
                'white', 'red', 'orange', 'yellow',
                'green', 'teal', 'blue', 'dark-blue',
                'purple', 'pink', 'brown', 'grey'
            ],
            trashed: this.isTrashed,
            note: this.$attrs.note,
            selected: false,
        };
    },
    created() {
        window.events.$on('reload_note_tags', this.reload_tags);
        window.events.$on('reload_note', this.reload_note);
        window.events.$on('deselect_all', this.deselectAll);
        window.events.$on('perform_note_action', this.performAction);
        window.events.$on('note-links-updated', this.reloadLinks);
        window.events.$on('autosave_drawing', this.autosave_drawing);
    },
    methods: {
        autosave_drawing(target_note_component, target_note, exported_image_data, drawing) {
            if(target_note_component !== 'note-component' || target_note.id != this.note.id)
                return false;

            //create non-existing drawing
            let data = new FormData();

            data.append('image', new File([exported_image_data], 'test.jpg'));
            data.append('note_id', target_note.id);

            axios.post('/drawing', data)
                 .then(response => this.note.drawings.push(response.data));
            
        },
        reloadLinks(note) {
            if(this.note.id === note.id)
                this.note.links = note.links;
        },
        convertToChecklist() {
            let fake_HTML_Element = document.createElement('div');
            fake_HTML_Element.innerHTML = this.note.body.replaceAll("<br>","\n");
            let html_removed = fake_HTML_Element.innerText;

            let items = html_removed.split(/\n/m);
            let blanks_deleted = items.filter(function (item) {
                return !(new RegExp(/^\s+$/)).test(item); //remove spaces
            }).filter(function (item) {
                return item != ''; //remove empty lines
            }).map(function (text) {
                return {
                    text: text,
                    completed: false
                };
            });

            //actually convert to checklist with POST /checklist
            axios.post('/checklist', {
                'checklist_data': blanks_deleted,
                'note_id': this.note.id
            }).then(res => this.note = res.data);

        },
        convertToText() {
            axios.post('/checklist/delete/' + this.note.id)
                 .then(res => this.note = res.data);
        },
        openForEditing() {
            window.events.$emit('open_note_for_editing', this.note);
        },
        deselectAll() {
            this.selected = false;
        },
        toggleSelect() {
            this.selected = !this.selected;
            window.events.$emit('note_selection_changed', this.note, this.selected);
        },
        reload_tags(note_id) {
            if(note_id == this.note.id) {
                axios.get('/note/' + note_id)
                    .then((res) => {
                        this.note.tags = res.data.tags;

                        setTimeout(() => {
                            window.masonry_layouts['pinned_notes_masonry'].layout();
                            window.masonry_layouts['other_notes_masonry'].layout();
                        }, 200);
                    });

                this.note.tags.forEach(function(tag) {
                    if( location.href.includes('tag/' + encodeURIComponent(tag.name)) ) { //if the current tag was deleted from the note
                        window.events.$emit('note_deleted', this.note);
                    }
                });

            }
        },
        reload_note(note) {
            if(note.id == this.note.id) {
                this.note.header = note.header;
                this.note.body = note.body;
                this.note.images = note.images ? note.images : {};

                this.note.checklist = note.checklist;
            }
        },
        openSetLabelsDialog() {
            window.events.$emit('open_set_labels_dialog', this.note.id, this.note.tags);
        },
        add_drawing() {
            window.events.$emit('show_drawing_dialog', 'note-component', this.note);
        },
        saveReminder() {
            let time = this.pickedDate + ' ' + this.pickedTime;
            let repeat = this.buildRepeatObjectFromData();

            axios.post('/reminder/' + this.note.id, {
                time : time,
                repeat: JSON.stringify(repeat),
                client_timezone: moment().format('Z')
            }).then(res => this.reload_reminder(res));

            this.$refs['dateTimePicker-modal'].hide();
        },
        storeReminder(text_time) {
            let formatted_time = this.formatDate(text_time, 'YYYY-MM-DD HH:mm:ss');

            axios.post('/reminder/' + this.note.id, {'time': formatted_time});
            this.note.reminder = { 'time': formatted_time };

            setTimeout(() => {
                window.masonry_layouts['pinned_notes_masonry'].layout();
                window.masonry_layouts['other_notes_masonry'].layout();
            }, 200);
        },
        refreshImage(data) {
            this.note.images.push(data);
        },
        selectImage() {
            this.$refs['image'].click();
        },
        handleFiles() {
            let image = this.$refs['image'].files[0];

            let data = new FormData();
            data.append('image', image, image.name);
            data.append('note_id', this.note.id);

            axios.post('/image', data).then( (res) => this.refreshImage(res.data) );
        },
        togglePin() {
            window.events.$emit('note_deleted', this.note);
            this.note.pinned = !this.note.pinned;

            axios.put('/note/' + this.note.id, {'pinned': this.note.pinned})
                 .then(res => window.events.$emit('note_created', this.note));
        },
        pin() {
            window.events.$emit('note_deleted', this.note);
            this.note.pinned = true;

            axios.put('/note/' + this.note.id, {'pinned': this.note.pinned})
                .then(res => window.events.$emit('note_created', this.note));
        },
        unpin() {
            window.events.$emit('note_deleted', this.note);
            this.note.pinned = false;

            axios.put('/note/' + this.note.id, {'pinned': this.note.pinned})
                .then(res => window.events.$emit('note_created', this.note));
        },
        isColorActive(color) {
            return (this.note.color === color) ? 'active' : '';
        },
        changeColor(color) {
            this.note.color = color;
            axios.put('/note/' + this.note.id, {'color': color});
        },
        showCollaboratorsDialog() {
            window.events.$emit('show-collaborators-dialog', this.note.id);
        },
        setInputHeight(itemClass) {
            let element = document.getElementsByClassName(itemClass)[0];

            element.style.height = "auto";
            element.style.height = (element.scrollHeight) + "px";
        },
        detach_tag(tag) {
            axios.delete('/tag/detach/' + this.note.id + '/' + tag.name);

            this.note.tags = _.without(this.note.tags, tag);

            let tagsLocation = 'tag/' + encodeURIComponent(tag.name);
            if (location.href.includes(tagsLocation))
                window.events.$emit('note_deleted', this.note);

            setTimeout(() => {
                window.masonry_layouts['pinned_notes_masonry'].layout();
                window.masonry_layouts['other_notes_masonry'].layout();
            }, 200);
        },
        performAction(note, action, parameter) {
            if(this.note.id !== note.id)
                return;

            if(action === 'changeColor')
                return this.changeColor(parameter);

            if(action === 'update_reminder')
                return this.updateReminder(parameter);

            this[action]();
        }
    }
}
</script>

<style scoped>

</style>
