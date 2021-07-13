let moment = require('moment');

module.exports = {
    methods: {
        deleteNote() {
            axios.delete('/note/' + this.note.id);

            window.events.$emit('show-notification', 'Note deleted', this.undoDelete);
            window.events.$emit('note_deleted', this.note);
        },
        delete_forever() {
            axios.delete('/note/' + this.note.id);
            window.events.$emit('note_deleted', this.note);
        },
        undoDelete() {
            axios.post('/note/restore/' + this.note.id);

            window.events.$emit('show-notification', 'Action undone');
            window.events.$emit('note_created', this.note);
        },
        restore() {
            axios.post('/note/restore/' + this.note.id);
            window.events.$emit('note_deleted', this.note);
            window.events.$emit('show-notification', 'Note restored', this.undoRestore);
        },
        undoRestore() {
            axios.delete('/note/' + this.note.id);

            window.events.$emit('show-notification', 'Action undone');
            window.events.$emit('note_created', this.note);
        },
    }
};
