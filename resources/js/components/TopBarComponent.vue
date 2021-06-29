<template>
    <header>
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

            <div class="regular_note_actions flex flex-row justify-between items-center py-2 pb-4 px-3" v-else>
                <div class="flex flex-row items-center">
                    <a href="" class="mr-2 rounded-full"
                       @click.prevent="deselectAll()"
                       v-b-tooltip.hover.bottom
                       title="Clear selection">
                        <i class="bi bi-x icon-lg text-black"></i>
                    </a>

                    <p class="m-0 text-xl pt-0.5">{{ notes.length }} selected</p>
                </div>

                <div class="flex flex-row items-center">
                    <a href="" class="mr-3 rounded-full"
                       @click.prevent="unpin()"
                       v-b-tooltip.hover.bottom
                       title="Unpin"
                       v-if="isAllNotesPinned">
                        <i class="bi bi-pin icon" style="color: rgb(26, 86, 219)"></i>
                    </a>

                    <a href="" class="mr-3 rounded-full"
                       @click.prevent="pin()"
                       v-b-tooltip.hover.bottom
                       title="Pin"
                       v-else>
                        <i class="bi bi-pin-fill icon" style="color: rgb(26, 86, 219)"></i>
                    </a>


                    <a href="" class="hover:bg-gray-300 rounded-full p-0 inline-block"
                       v-b-tooltip.hover.bottom
                       title="Remind me"
                       @click.prevent>
                        <b-dropdown size="sm" variant="link" toggle-class="" no-caret ref="reminder-dropdown" right>
                            <template #button-content>
                                <i class="bi bi-bell icon-sm p-0" style="color: rgb(26, 86, 219)"></i>
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

                    <div class="custom-tooltip">
                        <a href="" class="hover:bg-gray-300 p-2 rounded-full"
                           v-b-tooltip.hover.lefttop
                           title="Change color"
                           @click.prevent>
                            <i class="bi bi-palette icon" style="color: rgb(26, 86, 219)"></i>
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
                        <i class="bi icon"
                           :class="isOnPage('/archive') ? 'bi-arrow-up-square' : 'bi-arrow-down-square-fill'" style="color: rgb(26, 86, 219)"></i>
                    </a>

                    <a href="" class="rounded-full"
                       v-b-tooltip.hover.bottom
                       title="More"
                       @click.prevent>
                        <b-dropdown size="sm" variant="link" toggle-class="text-decoration-none" no-caret right>
                            <template #button-content>
                                <i class="bi bi-three-dots-vertical icon-sm p-0"></i>
                            </template>
                            <b-dropdown-item href="#" @click="deleteNotes()">Delete notes</b-dropdown-item>
                            <b-dropdown-item href="#" @click="openSetLabelsDialog()">Add label</b-dropdown-item>
                            <b-dropdown-item href="#" @click="copy()">Make a copy</b-dropdown-item>
                        </b-dropdown>
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
        //TODO: After most action manually redraw masonry with Vuemasonry.redraw
        bindTags(label, action) {
            if(action === 'add')
                this.notes.forEach((note) => axios.post('/tag/add/' + note.id + '/' + label)
                                                  .then(res => window.events.$emit('reload_note_tags', note.id)));

            if(action === 'remove')
                this.notes.forEach((note) => axios.delete('/tag/remove/' + note.id + '/' + label)
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
            alert('should pin');
            //TODO: pin the note
        },
        unpin() {
            alert('should unpin');
            //TODO: unpin the note
        },
        changeColor(color) {
            this.notes.forEach((note) => window.events.$emit('perform_note_action', note, 'changeColor', color));
            this.deselectAll();
        },
        toggleArchive() {
            if(this.isOnPage('/archive'))
                this.notes.forEach((note) => window.events.$emit('perform_note_action', note, 'unarchive', ''));
            else
                this.notes.forEach((note) => window.events.$emit('perform_note_action', note, 'archive', ''));

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
            this.notes.forEach((note) => window.events.$emit('perform_note_action', note, 'copy', ''));
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
        }

        //TODO: How to undo multiple note actions?
    }
}
</script>

<style scoped>

</style>
