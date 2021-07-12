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
    }
};
