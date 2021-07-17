<template>
    <div class="note border border-gray-300 p-3 hover:shadow-md relative transition-colors m-auto new-note"
         :class="'bg-google-' + note.color"
         style="width: 630px"
         ref="note">

        <a class="absolute right-1 top-1 hover:bg-gray-300 p-2 rounded-full"
           @click.prevent="pin()"
           v-b-tooltip.hover.bottom :title="note.pinned ? 'Unpin' : 'Pin'">
            <i class="bi bi-pin-fill icon" v-if="note.pinned"></i>
            <i class="bi bi-pin icon" v-else></i>
        </a>

        <div class="images">
            <div class="inline-block relative m-2" v-for="(encoded_image, index) in encoded_images">
                <img :src="encoded_image" style="height: 120px; width: 120px">
                <a class="bg-gray-300 rounded-full absolute top-1 left-1"
                   v-b-tooltip.hover.bottom
                   title="Delete image"
                   style="cursor: pointer"
                   @click.prevent="delete_image(index)">
                    <i class="bi bi-x icon"></i>
                </a>
            </div>
        </div>

        <textarea name="note_header" placeholder="Title"
                  class="note-header-input mx-2 focus:outline-none h-auto resize-none font-bold bg-transparent text-xl"
                  v-model="note.header">

        </textarea>

        <div v-show="isChecklist">
            <div class="form-check mb-2 flex flex-col" v-for="(item, index) in checklist" :key="item.key">
                <div class="flex flex-row">
                    <a href="#" @click.prevent="moveItem(item, index, 'up')">
                        <i class="bi bi-arrow-up text-green-700"></i>
                    </a>
                    <a href="#" @click.prevent="moveItem(item, index, 'down')">
                        <i class="bi bi-arrow-down text-red-700"></i>
                    </a>
                    <input class="form-check-input mt-2" type="checkbox" :value="item.completed"
                           @click="setChecklistItemState(item, index)">
                    <input type="text" class="flex-grow" v-model="checklist[index].text"
                           :ref="'checklist-item-' + index">
                    <a href="#" @click.prevent="removeChecklistItem(index)"> <span class="bi bi-x text-lg"></span> </a>
                </div>
            </div>

            <div class="flex flex-row">
                <button class="btn btn-primary btn-sm" @click="addToChecklist()"><i class="bi bi-plus"></i></button>
                <input type="text" v-model="newChecklistItem" placeholder="List Item"
                       class="flex-grow ml-2 border-b-2 border-gray-300 focus:outline-none">
            </div>
        </div>

        <div v-show="isChecklist === false">
            <trix-editor input="note_content" ref="new-note-editor"></trix-editor>
        </div>

        <div class="tags my-3">
            <a v-if="note.reminder_json.time"
               @click.self.prevent="$refs['dateTimePicker-modal'].show()"
               href="#"
               class="inline-block mr-2 rounded-full pl-2 pr-1 py-0 text-sm mb-2"
               style="border: 1px solid black!important;">
                <i class="bi bi-alarm icon" @click.self.prevent="$refs['dateTimePicker-modal'].show()"></i>
                <span ref="updated_reminder_time" @click.self.prevent="$refs['dateTimePicker-modal'].show()">{{ remainder_time_formatted }}</span>
                <a class="bg-gray-300 rounded-full"
                   v-b-tooltip.hover.bottom
                   title="Remove reminder"
                   @click.prevent="note.reminder_json = {}">
                    <i class="bi bi-x icon"></i>
                </a>
            </a>
            <a v-for="tag in tags"
               :href="'/tag/' + tag"
               class="inline-block mr-2 rounded-full pl-2 pr-1 py-0 text-sm mb-2"
               style="border: 1px solid black!important;">
                {{ tag }}
                <a class="bg-gray-300 rounded-full"
                   v-b-tooltip.hover.bottom
                   title="Remove label"
                   @click.prevent="detach_tag(tag)">
                    <i class="bi bi-x icon"></i>
                </a>
            </a>
            <a v-for="collaboratorEmail in collaboratorEmails" href="#"
               @click.prevent="showCollaboratorsDialog()"
               class="inline-block mr-2 rounded-full px-2 py-0 text-sm group"
               style="border: 1px solid black!important;">
                Shared with {{ collaboratorEmail }}
            </a>
        </div>

        <div class="toolbar">
            <a href="" class="hover:bg-gray-300 rounded-full p-0 inline-block"
               v-b-tooltip.hover.bottom
               title="Remind me"
               @click.prevent>
                <b-dropdown size="sm" variant="link" toggle-class="" no-caret ref="reminder-dropdown" v-on:show="checkLaterTodayVisibility()">
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
                                     @click="$refs['dateTimePicker-modal'].show()"
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
                       :class="'color-circle bg-google-' + color + ' ' + isColorActive(color)"
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
                    <b-dropdown-item href="#" @click="openSetLabelsDialog()">Add label</b-dropdown-item>
                    <b-dropdown-item href="#">Add drawing</b-dropdown-item>
                    <b-dropdown-item href="#" @click="convertToText()" v-if="isChecklist">Hide checkboxes
                    </b-dropdown-item>
                    <b-dropdown-item href="#" @click="uncheckAll()" v-if="isChecklist">Uncheck all
                    </b-dropdown-item>
                    <b-dropdown-item href="#" @click="removeCompleted()" v-if="isChecklist">Remove completed
                    </b-dropdown-item>
                    <b-dropdown-item href="#" @click="convertToChecklist()" v-else>Show checkboxes</b-dropdown-item>
                </b-dropdown>
            </a>

            <button type="button" class="btn btn-danger btn-sm" @click="save()">Save</button>
        </div>

        <set-labels-component :note="{id: 'new_note'}">

        </set-labels-component>

        <collaborator-dialog-component :note="{id: 'new_note'}"
                                       :owner="owner_object">

        </collaborator-dialog-component>

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
                <b-form-select v-model="repeatStatus" class="mb-3" selected="Doesn't repeat"
                               @change="showCustomRepeatOptions()">
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
                                <input type="radio" name="repeat_ends" v-model="repeat_ends" id="occurrences"
                                       value="occurrences"
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
                                On:
                                <b-form-datepicker v-model="pickedRepeatsDate"></b-form-datepicker>
                            </label>
                        </p>
                    </div>
                </div>
            </div>
            <p class="text-right m-0">
                <b-button variant="primary" @click="saveReminder()">Save</b-button>
            </p>
        </b-modal>
    </div>
</template>

<script>
import moment from 'moment';
import SetLabelsComponent from "./SetLabelsComponent";

let reminders = require('./mixins/reminders.js');

export default {
    name: "NewNoteComponent",
    mixins: [reminders],
    components: {SetLabelsComponent},
    props: {
        hasRemainder: Boolean,
        owner: String
    },
    data() {
        return {
            isCollaboratorsDialogVisible: false,
            colors: [
                'white', 'red', 'orange', 'yellow',
                'green', 'teal', 'blue', 'dark-blue',
                'purple', 'pink', 'brown', 'grey'
            ],
            collaboratorEmails: [],
            images: [],
            isChecklist: false,
            checklist: [],
            newChecklistItem: '',
            encoded_images: [],
            note: {
                header: '',
                body: '',
                pinned: false,
                archived: false,
                color: 'white',
                type: 'text',
                reminder_json: {},
            },
            owner_object: JSON.parse(this.owner),
            tags: [],
        };
    },
    created() {
        window.events.$on('reload_new_note_tags', this.reload_tags);
        window.events.$on('save_new_note_collaborators', this.reload_collaborators);

        //TODO: Save the note when clicked outside feature
        this.initialize_dependencies();
    },
    watch: {},
    methods: {
        addToChecklist() {
            this.checklist.push({
                text: this.newChecklistItem,
                completed: false,
                key: new Date().getTime()
            });

            this.newChecklistItem = '';
        },
        uncheckAll() { //operation is immediate
            this.checklist.forEach((item, index) => this.$set(this.checklist[index], 'completed', false));
        },
        removeCompleted() {
            this.checklist = this.checklist.filter( task => task.completed == false );
        },
        removeChecklistItem(index) {
            this.checklist.splice(index, 1);
        },
        setChecklistItemState(item, index) {
            item.completed = event.target.checked;
        },
        moveItem(item, index, direction) {
            if((index === 0) && direction === 'up')
                return;

            if((index === this.checklist.length - 1) && direction === 'down')
                return;

            let increment = (direction === 'up') ? -1 : +1;

            let item_1 = this.checklist[index];
            let item_2 = this.checklist[index + increment];

            this.$set(this.checklist, index, item_2);
            this.$set(this.checklist, index + increment, item_1);
        },
        convertToChecklist() {
            let unformatted_text = this.$refs['new-note-editor'].editor.element.innerText;
            let items = unformatted_text.split(/\n/m);
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

            this.checklist = blanks_deleted;
            this.isChecklist = true;
        },
        convertToText() {
            let _text = this.checklist.reduce(function (accumulator, task) {
                return accumulator + task.text + '<br>';
            }, '');

            this.$refs['new-note-editor'].value = _text;

            this.checklist = {};
            this.isChecklist = false;
        },
        initialize_dependencies() {
            //Set the tag if it exists
            if (this.$attrs.tag_name)
                this.tags[0] = this.$attrs.tag_name;

            //Set the remainder if it exists
            if (this.hasRemainder)
                this.storeReminder('soon');
        },
        reload_collaborators(emails) {
            this.collaboratorEmails = emails;
        },
        showCollaboratorsDialog() {
            window.events.$emit('show-collaborators-dialog', 'new_note');
        },
        detach_tag(tag_name) {
            this.tags.splice(this.tags.indexOf(tag_name), 1);
        },
        reload_tags(tag_name, isChecked) {
            if (isChecked)
                this.tags.push(tag_name);
            else
                this.detach_tag(tag_name);
        },
        openSetLabelsDialog() {
            window.events.$emit('open_set_labels_dialog', 'new_note', this.tags);
        },
        saveReminder() {
            let time = this.pickedDate + ' ' + this.pickedTime;
            let repeat = this.buildRepeatObjectFromData();

            this.note.reminder_json = {
                time: time,
                repeat: JSON.stringify(repeat)
            };

            this.$refs['dateTimePicker-modal'].hide();
        },
        storeReminder(text_time) {
            let formatted_time = this.formatDate(text_time, 'YYYY-MM-DD HH:mm:ss');

            this.note.reminder_json = {'time': formatted_time};

            this.pickedDate = this.formatDate(text_time, 'YYYY-MM-DD');
            this.pickedTime = this.formatDate(text_time, 'HH:mm:ss');
            this.repeatStatus = "Doesn't repeat";
        },
        save() {
            axios.post('/note/', {
                header: this.note.header,
                body: this.isChecklist ? '_' : this.$refs['new-note-editor'].editor.element.innerHTML,
                pinned: this.note.pinned,
                archived: false,
                color: this.note.color,
                type: this.note.type,
                reminder_json: JSON.stringify(this.note.reminder_json),
                tags: this.tags,
                collaboratorEmails: this.collaboratorEmails
            }).then(res => this.saveChecklist(res))
              .then(res => this.attach_images())
              .then(res => this.refreshNotesContainer(window.newNote))
              .then(res => this.reset());
        },
        saveChecklist(result) {
            let note = result.data;
            window.newNote = note;

            if (this.isChecklist)
                axios.post('/checklist', {
                    'checklist_data': this.checklist,
                    'note_id': note.id
                }).then(res => window.newNote = res.data);
        },
        attach_images() {
            let note = window.newNote;

            this.images.forEach(function (image) {
                let data = new FormData();
                data.append('image', image, image.name);
                data.append('note_id', note.id);

                axios.post('/image', data);
            });

            axios.get('/note')
                 .then(res => window.newNote = res.data);
        },
        refreshNotesContainer(note) {
            window.events.$emit('note_created', note);
        },
        reset() {
            this.note = {
                header: '',
                body: '',
                pinned: false,
                archived: false,
                color: 'white',
                type: 'text'
            };
            this.$refs['new-note-editor'].value = '';

            this.collaboratorEmails = [];
            this.images = [];
            this.encoded_images = [];

            this.pickedDate = '';
            this.pickedTime = '';
            this.pickedRepeatsDate = '';
            this.repeatStatus = '';
            this.customRepeatStatusShown = false;

            this.note.reminder_json = {};
            this.tags = [];
            this.initialize_dependencies();

            this.repeat_ends = 'never';
            this.repeat_occurrences = 1;
            this.repeat_every_value = 1;
            this.repeat_every_unit = 'day';
            this.weekdaysShown = false;
            this.weekdays = [];

            this.checklist = [];
            this.newChecklistItem = '';
        },
        pin() {
            this.note.pinned = !this.note.pinned;
        },
        isColorActive(color) {
            return (this.note.color === color) ? 'active' : '';
        },
        changeColor(color) {
            this.note.color = color;
        },
        selectImage() {
            this.$refs['image'].click();
        },
        handleFiles() {
            let file = this.$refs['image'].files[0];

            this.images.push(file);
            const reader = new FileReader();

            reader.onloadend = () => {
                this.encoded_images.push(reader.result);
            };

            reader.readAsDataURL(file);
        },
        delete_image(index) {
            this.images.splice(index, 1);
            this.encoded_images.splice(index, 1);
        }
    }
}
</script>

<style scoped>

</style>
