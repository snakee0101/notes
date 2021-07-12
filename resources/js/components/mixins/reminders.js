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
    }
};
