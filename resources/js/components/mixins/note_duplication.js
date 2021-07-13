let moment = require('moment');

module.exports = {
    methods: {
        copy() {
            axios.post('/note/duplicate/' + this.note.id)
                .then(this.copyCallback);
        },
        copyCallback(res) {
            window.duplicatedNote = res.data;
            window.events.$emit('note_created', res.data);
            window.events.$emit('show-notification', 'Note created', this.undoCopy);
        },
        undoCopy() {
            axios.delete('/note/' + window.duplicatedNote.id);
            axios.delete('/note/' + window.duplicatedNote.id)//force delete
                .then(this.undoCopyCallback);
        },
        undoCopyCallback() {
            window.events.$emit('note_deleted', window.duplicatedNote);
            window.events.$emit('show-notification', 'Action undone');
        },
    }
};
