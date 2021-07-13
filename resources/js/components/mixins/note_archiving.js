let moment = require('moment');

module.exports = {
    methods: {
        archive() {
            axios.put('/note/' + this.note.id, {'archived': true});

            window.events.$emit('note_deleted', this.note);
            window.events.$emit('show-notification', 'Note archived', this.undo_archive);
        },
        undo_archive() {
            axios.put('/note/' + this.note.id, {'archived': false});

            window.events.$emit('note_created', this.note);
            window.events.$emit('show-notification', 'Action undone');
        },
        unarchive() {
            axios.put('/note/' + this.note.id, {'archived': false});

            window.events.$emit('note_deleted', this.note);
            window.events.$emit('show-notification', 'Note unarchived', this.undo_unarchive);
        },
        undo_unarchive() {
            axios.put('/note/' + this.note.id, {'archived': true});

            window.events.$emit('note_created', this.note);
            window.events.$emit('show-notification', 'Action undone');
        },
    }
};
