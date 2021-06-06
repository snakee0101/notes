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

        <div class="tags mb-4">
            <a v-if="reminder_json.time"
               @click.self.prevent="pickDateAndTime()"
               href="#"
               class="mr-2 border border-black rounded-full px-2 py-0.5 text-sm group relative">
                <i class="bi bi-alarm icon" @click.self.prevent="pickDateAndTime()"></i>
                <span ref="updated_reminder_time" @click.self.prevent="pickDateAndTime()">{{ getReminderTime() }}</span>
                <a class="hidden group-hover:inline absolute right-1 group-hover:bg-gray-300 rounded-full z-20"
                   v-b-tooltip.hover.bottom
                   title="Remove reminder"
                   @click.prevent="removeReminder()">
                    <i class="bi bi-x icon"></i>
                </a>
            </a>
<!--            <a v-for="tag in note.tags"
               :href="'/tag/' + tag"
               class="mr-2 border border-black rounded-full px-2 py-0.5 text-sm group relative">
                {{ tag }}
                <a class="hidden group-hover:inline absolute right-1 group-hover:bg-gray-300 rounded-full z-20"
                   v-b-tooltip.hover.bottom
                   title="Remove label"
                   @click.prevent="detach_tag(tag)">
                    <i class="bi bi-x icon"></i>
                </a>
            </a>-->
        </div>

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

        <b-modal title="BootstrapVue" ref="dateTimePicker-modal"
                 centered hide-footer modal-class="dateTimePicker-modal">
            <p class="text-lg font-bold">
                <a href=""
                   v-b-tooltip.hover.bottom
                   title="Go back"
                   @click.prevent="this.$refs['dateTimePicker-modal'].hide(); this.$refs['reminder-dropdown'].show();">
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
    </div>
</template>

<script>
import moment from 'moment';

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
            },
            pickedDate: '',
            pickedTime: '',
            pickedRepeatsDate: '',
            repeatStatus: '',
            reminder_json: {},
            customRepeatStatusShown: false,
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
        pickDateAndTime() {
            this.$refs['dateTimePicker-modal'].show();
            this.$refs['reminder-dropdown'].hide()
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

            this.reminder_json = {
                time : time,
                repeat: JSON.stringify(repeat)
            };

            //window.events.$emit('update_reminder_label', this.note.id, time);
            this.$refs['dateTimePicker-modal'].hide();
        },
        storeReminder(text_time) {
            let time = {
                'later_today': moment().set({'hour': 20}),
                'tomorrow': moment().add(1, 'days').set({'hour': 8}),
                'next_week': moment().add(1, 'weeks').set({'day': 'Monday', 'hour': 8}),
            };

            let formatted_time = time[text_time].set({'minute': 0, 'second': 0})
                .format('YYYY-MM-DD HH:mm:ss');

            this.reminder_json = {'time': formatted_time};
        },
        getReminderTime() {
            let reminder_date = this.reminder_json.time;

            if (moment(reminder_date).year() > moment().year())
                return moment(reminder_date).format('MMM D, YYYY, H:mm A');

            return moment(reminder_date).format('MMM D, H:mm A');
        },
        removeReminder() {
          this.reminder_json = { };
        },
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
                type: this.note.type,
                reminder_json: JSON.stringify(this.reminder_json)
            }).finally(() => location.reload());
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
        },
        selectImage() {
            this.$refs['image'].click();
        },
        handleFiles() {
            let image = this.$refs['image'].files[0];

            let data = new FormData();
            data.append('image', image, image.name);

            //window.newImageComponent = this;

            /*axios.post('/image', data).then(function (result) {
                window.events.$emit('refresh_image', result.data);
            });*/
        }
    }
}
</script>

<style scoped>

</style>
