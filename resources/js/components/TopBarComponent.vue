<template>
    <header class="border-b border-gray-200">
        <set-labels-component :is-global="true" ref="labels-dialog">

        </set-labels-component>

        <section class="note-actions-panel"
                 v-if="notes.length">

            <div class="trashed_note_actions flex flex-row justify-between items-center py-2 pb-4 px-3 border-b-2 border-gray-300" v-if="isOnPage('/trash')">
                <div class="flex flex-row items-center">
                    <a href="" class="mr-2 rounded-full"
                       @click.prevent="deselectAll()"
                       v-b-tooltip.hover.bottom
                       title="Clear selection">
                        <i class="bi bi-x icon-lg top-bar-icon-color"></i>
                    </a>

                    <p class="m-0 text-xl pt-0.5 text-color">{{ notes.length }} selected</p>
                </div>

                <div class="flex flex-row items-center">
                    <a href="" class="mr-3 rounded-full"
                       @click.prevent="deleteForever()"
                       v-b-tooltip.hover.bottom
                       title="Delete forever">
                        <i class="bi bi-trash2 icon top-bar-icon-color"></i>
                    </a>

                    <a href="" class="mr-2 rounded-full mt-1"
                       @click.prevent="restore()"
                       v-b-tooltip.hover.bottom
                       title="Restore">
                        <i class="bi bi-recycle icon-lg text-black">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-recycle top-bar-icon-color" viewBox="0 0 16 16">
                                <path d="M9.302 1.256a1.5 1.5 0 0 0-2.604 0l-1.704 2.98a.5.5 0 0 0 .869.497l1.703-2.981a.5.5 0 0 1 .868 0l2.54 4.444-1.256-.337a.5.5 0 1 0-.26.966l2.415.647a.5.5 0 0 0 .613-.353l.647-2.415a.5.5 0 1 0-.966-.259l-.333 1.242-2.532-4.431zM2.973 7.773l-1.255.337a.5.5 0 1 1-.26-.966l2.416-.647a.5.5 0 0 1 .612.353l.647 2.415a.5.5 0 0 1-.966.259l-.333-1.242-2.545 4.454a.5.5 0 0 0 .434.748H5a.5.5 0 0 1 0 1H1.723A1.5 1.5 0 0 1 .421 12.24l2.552-4.467zm10.89 1.463a.5.5 0 1 0-.868.496l1.716 3.004a.5.5 0 0 1-.434.748h-5.57l.647-.646a.5.5 0 1 0-.708-.707l-1.5 1.5a.498.498 0 0 0 0 .707l1.5 1.5a.5.5 0 1 0 .708-.707l-.647-.647h5.57a1.5 1.5 0 0 0 1.302-2.244l-1.716-3.004z"/>
                            </svg>
                        </i>
                    </a>
                </div>
            </div>

            <div class="regular_note_actions flex flex-row justify-between items-center py-2 pb-4 px-3" v-else>
                <div class="flex flex-row items-center">
                    <a href="" class="mr-2 rounded-full"
                       @click.prevent="deselectAll()"
                       v-b-tooltip.hover.bottom
                       title="Clear selection">
                        <i class="bi bi-x icon-lg top-bar-icon-color"></i>
                    </a>

                    <p class="m-0 text-xl pt-0.5 text-color">{{ notes.length }} selected</p>
                </div>

                <div class="flex flex-row items-center">
                    <a href="" class="mr-3 rounded-full"
                       @click.prevent="unpin()"
                       v-b-tooltip.hover.bottom
                       title="Unpin"
                       v-if="isAllNotesPinned">
                        <i class="bi bi-pin icon top-bar-icon-color"></i>
                    </a>

                    <a href="" class="mr-3 rounded-full"
                       @click.prevent="pin()"
                       v-b-tooltip.hover.bottom
                       title="Pin"
                       v-else>
                        <i class="bi bi-pin-fill icon top-bar-icon-color"></i>
                    </a>


                    <a href="" class="rounded-full p-0 inline-block"
                       v-b-tooltip.hover.bottom
                       title="Remind me"
                       @click.prevent>
                        <b-dropdown size="sm" variant="link" toggle-class="" no-caret ref="reminder-dropdown" right>
                            <template #button-content>
                                <i class="bi bi-bell icon p-0 top-bar-icon-color"></i>
                            </template>
                            <p class="text-lg p-2 pl-4 m-0 font-bold text-color">Reminder:</p>
                            <b-dropdown-item href="#" @click="storeReminder('later_today')"
                                             class="focus:outline-none py-2.5" v-if="isLaterTodayVisible">
                                Later today
                                <span class="text-gray-500">8:00 PM</span>
                            </b-dropdown-item>
                            <b-dropdown-item href="#" @click="storeReminder('tomorrow')"
                                             class="focus:outline-none py-2.5">
                                Tomorrow
                                <span class="text-gray-500">8:00 AM</span>
                            </b-dropdown-item>
                            <b-dropdown-item href="#" @click="storeReminder('next_week')"
                                             class="focus:outline-none py-2.5">
                                Next week
                                <span class="text-gray-500">Mon., 8:00 AM</span>
                            </b-dropdown-item>
                            <b-dropdown-item href="#"
                                             @click="pickDateAndTime()"
                                             class="focus:outline-none py-2.5">
                                <i class="bi bi-alarm-fill mr-3"></i>
                                Pick date & time
                            </b-dropdown-item>
                        </b-dropdown>
                    </a>

                    <div class="custom-tooltip">
                        <a href="" class="p-2 rounded-full"
                           v-b-tooltip.hover.lefttop
                           title="Change color"
                           @click.prevent>
                            <i class="bi bi-palette icon top-bar-icon-color"></i>
                        </a>
                        <div class="vertical-tooltiptext topbar-vertical-tooltiptext rounded-md"> <!--TODO: fix displaying - hides when hovered on another circle-->
                            <a v-for="color in colors"
                               href=""
                               :class="'color-circle bg-google-' + color"
                               v-b-tooltip.hover.lefttop
                               :title="color"
                               @click.prevent="changeColor(color)">
                                <i class="bi bi-check icon-sm"></i>
                            </a>
                        </div>
                    </div>

                    <a href="" class="mr-3 rounded-full"
                       @click.prevent="toggleArchive()"
                       v-b-tooltip.hover.bottom
                       :title="isOnPage('/archive') ? 'Unarchive' : 'Archive'">
                        <i class="bi icon top-bar-icon-color"
                           :class="isOnPage('/archive') ? 'bi-arrow-up-square' : 'bi-arrow-down-square-fill'"></i>
                    </a>

                    <a href="" class="rounded-full"
                       v-b-tooltip.hover.bottom
                       title="More"
                       @click.prevent>
                        <b-dropdown size="sm" variant="link" toggle-class="text-decoration-none" no-caret right>
                            <template #button-content>
                                <i class="bi bi-three-dots-vertical icon-sm p-0 top-bar-icon-color"></i>
                            </template>
                            <b-dropdown-item href="#" @click="deleteNotes()">Delete notes</b-dropdown-item>
                            <b-dropdown-item href="#" @click="openSetLabelsDialog()">Add label</b-dropdown-item>
                            <b-dropdown-item href="#" @click="copy()">Make a copy</b-dropdown-item>
                        </b-dropdown>
                    </a>
                </div>
            </div>

        </section>

        <section class="flex flex-row justify-between py-2 px-3 items-center" v-else>
            <menu-switcher-component></menu-switcher-component>
            <search-component></search-component>

            <section class="flex flex-row items-center">
                <theme-switcher-component></theme-switcher-component>

                <a href="#" class="p-2 group"
                   v-b-tooltip.hover.bottom
                   title="Logout"
                   @click.prevent="logout()">
                    <i class="bi bi-box-arrow-right text-2xl group-hover:text-red-700"></i>
                </a>
            </section>
        </section>

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

        <drawing-dialog-component></drawing-dialog-component>
        <photo-capture-dialog-component></photo-capture-dialog-component>
    </header>
</template>

<script>
import SetLabelsComponent from "./SetLabelsComponent";
import moment from 'moment';

export default {
    name: "TopBarComponent",
    components: {SetLabelsComponent},
    data() {
      return {
          notes: [],
          colors: [
              'white', 'red', 'orange', 'yellow',
              'green', 'teal', 'blue', 'dark-blue',
              'purple', 'pink', 'brown', 'grey'
          ],
          pickedDate: '',
          pickedTime: '',
          pickedRepeatsDate: '',
          repeatStatus: "Doesn't repeat",
          customRepeatStatusShown: false,
          isLaterTodayVisible: false,
          repeat_ends: 'never',
          repeat_occurrences: 1,
          repeat_every_value: 1,
          repeat_every_unit: 'day',
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
      }
    },
    created() {
        window.events.$on('note_selection_changed', this.registerNoteSelection);
        window.events.$on('reload_top_bar_tags', this.bindTags);
    },
    computed: {
        isAllNotesPinned() {
            return this.notes.every( (note) => note.pinned );
        }
    },
    methods: {
        //TODO: Problem - sometimes notes are duplicating (for example, with pin/unpin actions)
        bindTags(label, action) {
            if(action === 'add')
                this.notes.forEach((note) => axios.post('/tag/attach/' + note.id + '/' + label.name)
                                                  .then(res => window.events.$emit('reload_note_tags', note.id)));

            if(action === 'remove')
                this.notes.forEach((note) => axios.delete('/tag/detach/' + note.id + '/' + label.name)
                                                  .then(res => window.events.$emit('reload_note_tags', note.id)));

            this.deselectAll();
            this.$refs['labels-dialog'].hide();
        },
        registerNoteSelection(note, selected) {
            (selected) ? this.addNote(note) : this.removeNote(note);
        },
        addNote(note) {
            this.notes.push(note);
        },
        removeNote(note) {
            this.notes.splice( this.notes.indexOf(note) ,1);
        },
        isOnPage(page) {
            return location.href.includes(page);
        },
        deselectAll() {
            window.events.$emit('deselect_all');
            this.notes = [];
        },
        deleteForever() {
            this.notes.forEach((note) => window.events.$emit('perform_note_action', note, 'delete_forever', ''));
            this.deselectAll();
        },
        restore() {
            this.notes.forEach((note) => window.events.$emit('perform_note_action', note, 'restore', ''));
            this.deselectAll();
        },
        pin() {
            let notes_to_be_pinned = this.notes.filter(note => note.pinned === false);

            notes_to_be_pinned.forEach(note => {
                setTimeout(() => window.events.$emit('perform_note_action', note, 'pin', ''), 50)
            });

            this.deselectAll();
        },
        unpin() {
            this.notes.forEach(note => {
                setTimeout(() => window.events.$emit('perform_note_action', note, 'unpin', ''), 50) 
            }); //unpinned notes are already selected by isAllNotesPinned computed property
            this.deselectAll();
        },
        changeColor(color) {
            this.notes.forEach((note) => window.events.$emit('perform_note_action', note, 'changeColor', color));
            this.deselectAll();
        },
        toggleArchive() {
            let action = this.isOnPage('/archive') ? 'unarchive' : 'archive';
            this.notes.forEach((note) => window.events.$emit('perform_note_action', note, action, ''));

            this.deselectAll();
        },
        deleteNotes() {
            this.notes.forEach((note) => window.events.$emit('perform_note_action', note, 'deleteNote', ''));
            this.deselectAll();
        },
        openSetLabelsDialog() {
            window.events.$emit('open_set_labels_dialog');
        },
        copy() {
            this.notes.forEach((note) => {
                setTimeout(() => window.events.$emit('perform_note_action', note, 'copy', ''), 50); 
            });
            
            this.deselectAll();
        },
        pickDateAndTime() {
            this.$refs['dateTimePicker-modal'].show();
            this.$refs['reminder-dropdown'].hide()
            this.initializeRepeatFields();
        },
        initializeRepeatFields() {
            this.pickedDate = moment().format('YYYY-MM-DD');
            this.pickedTime = moment().format('HH:mm:ss');
        },
        storeReminder(text_time) {
            let time = {
                'later_today': moment().set({'hour': 20}),
                'tomorrow': moment().add(1, 'days').set({'hour': 8}),
                'next_week': moment().add(1, 'weeks').set({'day': 'Monday', 'hour': 8}),
            };

            let formatted_time = time[text_time].set({'minute': 0, 'second': 0})
                .format('YYYY-MM-DD HH:mm:ss');

            this.notes.forEach( note => axios.post('/reminder/' + note.id, {'time': formatted_time})
                                             .then(window.events.$emit('perform_note_action', note, 'update_reminder', {'time': formatted_time})));
        },
        saveReminder() {
            let time = this.pickedDate + ' ' + this.pickedTime;
            let repeat = '';

            if(this.repeatStatus !== "Doesn't repeat") {
                repeat = {
                    every : {
                        number: Number(this.repeat_every_value),
                        unit: this.repeat_every_unit
                    }
                };

                if(this.weekdays.length > 0)
                    repeat.every.weekdays = this.weekdays;

                if(this.repeat_ends !== 'never')
                    repeat.ends = {after : '', on_date : ''};

                if(this.repeat_ends === "occurrences")
                    repeat.ends.after = Number(this.repeat_occurrences);

                if(this.repeat_ends === "date")
                    repeat.ends.on_date = this.pickedRepeatsDate + ' 00:00:00';
            }

            this.notes.forEach( note => axios.post('/reminder/' + note.id, {
                time: time,
                repeat: JSON.stringify(repeat)
            }).then(window.events.$emit('perform_note_action', note, 'update_reminder', {'time': time})));

            this.$refs['dateTimePicker-modal'].hide();
        },
        showWeekdays() {
            this.weekdaysShown = (this.repeat_every_unit === 'week');
        },
        showCustomRepeatOptions() {
            this.customRepeatStatusShown = (this.repeatStatus === 'Custom');
            let repeat_units = {
                'Daily': 'day',
                'Weekly': 'week',
                'Monthly': 'month',
                'Yearly': 'year',
                'Custom': 'day'
            };
            this.repeat_ends = 'never';
            this.repeat_occurrences = 1;
            this.repeat_every_value = 1;
            this.weekdays = [];
            this.repeat_every_unit = repeat_units[this.repeatStatus];
        },
        //TODO: How to undo multiple note actions?
        logout() {
            axios.get('/logout')
                 .then(res => location.reload());
        }
    }
}
</script>

<style scoped>

</style>
