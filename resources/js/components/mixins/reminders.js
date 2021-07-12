let moment = require('moment');

module.exports = {
    data() {
        return {
            isLaterTodayVisible: false,
            pickedDate: '',
            pickedTime: '',
            pickedRepeatsDate: '',
            repeatStatus: '',
            customRepeatStatusShown: false,
            repeat_ends: 'never',
            repeat_occurrences: 1,
            repeat_every_value: 1,
            repeat_every_unit: 'day',
            weekdaysShown: false,
            weekdays: [],
            weekdaysOptions: [
                {text: 'Mon', value: 'Monday'},
                {text: 'Tue', value: 'Tuesday'},
                {text: 'Wed', value: 'Wednesday'},
                {text: 'Thu', value: 'Thursday'},
                {text: 'Fri', value: 'Friday'},
                {text: 'Sat', value: 'Saturday'},
                {text: 'Sun', value: 'Sunday'},
            ]
        }
    },
    computed: {
        remainder_time_formatted() {
            let reminder_date = this.note.reminder_json.time;

            if (moment(reminder_date).year() > moment().year())
                return moment(reminder_date).format('MMM D, YYYY, H:mm A');

            return moment(reminder_date).format('MMM D, H:mm A');
        }
    },
    created() {
        setInterval(this.checkLaterTodayVisibility, 500);
    },
    methods: {
        checkLaterTodayVisibility() { //TODO: Fix issue - this method is called 100 times per second - because each NoteComponent calls it every 500ms - it must only be called if the context menu was opened
            let evening = (new Date).setHours(19, 0, 0);
            this.isLaterTodayVisible = Date.now() < evening;
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
        removeReminder() {
            axios.delete('/reminder/' + this.note.id);

            window.ReminderNoteId = this.note.id;
            window.ReminderTime = this.note.reminder_json.time;

            this.note.reminder_json = null;

            if (location.href.includes('/reminder'))
                window.events.$emit('note_deleted', this.note);

            window.events.$emit('show-notification', 'Reminder deleted', this.undoReminderRemoval);
        },
        undoReminderRemoval() {
            axios.post('/reminder/' + window.ReminderNoteId, {'time': window.ReminderTime});
            this.note.reminder_json = {'time': window.ReminderTime};

            if (location.href.includes('/reminder'))
                window.events.$emit('note_created', this.note);

            window.events.$emit('show-notification', 'Action undone');
        },
        updateReminder(json_time) {
            this.note.reminder_json = json_time;
        },
        reload_reminder_json(res) {
            this.note.reminder_json = res.data;
        },
        pickDateAndTime() {
            this.$refs['dateTimePicker-modal'].show();
            this.$refs['reminder-dropdown'].hide()
            this.initializeRepeatFields();
        },
        getReminderTime() {
            let reminder_date = this.reminder_json.time;

            if (moment(reminder_date).year() > moment().year())
                return moment(reminder_date).format('MMM D, YYYY, H:mm A');

            return moment(reminder_date).format('MMM D, H:mm A');
        },
        formatDate(text_time) {
            let time = {
                'later_today': moment().set({'hour': 20}),
                'tomorrow': moment().add(1, 'days').set({'hour': 8}),
                'next_week': moment().add(1, 'weeks').set({'day': 'Monday', 'hour': 8}),
                'soon': moment().add(3, 'hours'),
            };

            return time[text_time];
        },
        initializeRepeatFields() {
            let repeat_statuses = {
                'day' : 'Daily',
                'week' : 'Weekly',
                'month' : 'Monthly',
                'year' : 'Yearly'
            };

            let json = this.note.reminder_json;

            this.pickedDate = json.time ? moment(json.time).format('YYYY-MM-DD') : moment().format('YYYY-MM-DD');
            this.pickedTime = json.time ? moment(json.time).format('HH:mm:ss') : moment().format('HH:mm:ss');

            //initialize repeat status dropdown
            if(Object.keys(json.repeat).length == 0) {  //Object.keys(obj).length == 0  - check if the object is empty
                this.repeatStatus = "Doesn't repeat";
                return;
            }

            if( (json.repeat.every.number == 1) &&
                (json.repeat.ends == undefined) &&
                (json.repeat.every.weekdays == undefined)) {
                this.repeatStatus = repeat_statuses[json.repeat.every.unit];
            } else {
                //initialize custom repeat status controls
                this.repeatStatus = "Custom";
                this.customRepeatStatusShown = true;

                this.repeat_every_value = json.repeat.every.number;
                this.repeat_every_unit = json.repeat.every.unit;

                if(json.repeat.ends != '') {
                    if(json.repeat.ends.after != '') {
                        this.repeat_occurrences = json.repeat.ends.after;
                        this.repeat_ends = 'occurrences';
                    }

                    if(json.repeat.ends.on_date != '') {
                        this.pickedRepeatsDate = moment(json.repeat.ends.on_date).format('YYYY-MM-DD');
                        this.repeat_ends = 'date';
                    }
                }

                if(json.repeat.every.weekdays != undefined) {
                    this.weekdays = json.repeat.every.weekdays;
                    this.weekdaysShown = true;
                }
            }
        },
    }
};
