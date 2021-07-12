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
    }
};
