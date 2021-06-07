<template>
    <div>
        <b-modal ref="delete-confirmation" hide-footer centered class="delete-confirmation">
            <p class="m-2 mb-4"> We’ll delete this label and remove it from all of your notes. Your notes won’t be
                deleted.</p>
            <div class="bg-white rounded-b-lg text-right">
                <button
                    @click="$refs['delete-confirmation'].hide()"
                    class="cancel-button">
                    Cancel
                </button>
                <button
                    @click="deleteLabel()"
                    class="delete-button">
                    Delete
                </button>
            </div>
        </b-modal>

        <b-modal ref="labels-dialog" hide-footer centered class="labels-dialog">
            <div class="bg-white p-4 rounded-t-lg">
                <h3 class="font-medium text-lg">Set labels for the note</h3>
                <div class="mt-3 pt-4 px-2 border-t-2 border-gray-200">
                    <div class="label flex flex-row mb-3 items-center">
                        <a href="" @click.prevent="cancel()"
                           v-b-tooltip.hover.bottom
                           title="Cancel"
                           v-if="isCancelButtonVisible">
                            <i class="bi bi-x icon-sm"></i>
                        </a>
                        <label for="newLabel" class="cursor-pointer" @click="clearNewLabel()"
                               v-if="!isCancelButtonVisible">
                            <i class="bi bi-plus icon-sm"></i>
                        </label>
                        <input
                            class="border-transparent border-b-2 add-label-input ml-4 flex-grow text-sm focus:outline-none focus:border-gray-200"
                            placeholder="Create new label" required v-model="newLabel" id="newLabel"
                            v-on:keyup.enter="addLabel(newLabel)"
                            @focus="setFocusedState(); showCancelButton()">
                        <a href="" class="p-1"
                           v-b-tooltip.hover.bottom
                           title="Add label"
                           @click.prevent="addLabel(newLabel)">
                            <i class="bi bi-check icon"></i>
                        </a>
                    </div>

                    <p class="mb-4 text-red-700" v-if="uniqueErrorShown">Tag names must be unique</p>

                    <div class="label flex flex-row mb-3 items-center" v-for="(label, key) in labels">
                        <a href=""
                           @click.prevent="showDeleteConfirmation(label)"
                           @mouseout="hideDeleteButtonOn('label_' + key)"
                           v-b-tooltip.hover.bottom
                           title="Remove label"
                           v-if="isEditing('label_' + key) || isDeleteButtonVisible('label_' + key)">
                            <i class="bi bi-trash icon-sm"></i>
                        </a>

                        <i class="bi bi-tag-fill icon-sm"
                           @mouseover="showDeleteButtonOn('label_' + key)"
                           v-else></i>

                        <input type="text"
                               @focus="setFocusedState('label_' + key)"
                               :value="label" :ref="'label_' + key"
                               class="border-transparent border-b-2 add-label-input ml-4 flex-grow text-sm focus:outline-none focus:border-gray-200">


                        <a href="" class="p-1"
                           @click.prevent="focusOnLabel('label_' + key)"
                           v-b-tooltip.hover.bottom
                           title="Rename label"
                           v-if="isEditing('label_' + key) === false">
                            <i class="bi bi-pencil-fill icon-sm"></i>
                        </a>
                        <a href="" class="p-1"
                           @click.prevent="renameLabel('label_' + key, key)"
                           v-b-tooltip.hover.bottom
                           title="Rename label"
                           v-else>
                            <i class="bi bi-check icon-sm"></i>
                        </a>
                    </div>
                </div>
            </div>
            <div class="bg-gray-200 rounded-b-lg py-2 px-4 text-right">
                <button @click="hide()" class="done-button">
                    Done
                </button>
            </div>
        </b-modal>
    </div>
</template>



<script>
export default {
    name: "SetLabelsComponent",
    data() {
        return {
            newLabel: '',
            labels: this.$attrs.labels,
            note_id: this.$attrs.note_id,
            isCancelButtonVisible: false,
            editingLabel: '',
            deleteButtonOn: '',
            uniqueErrorShown: false,
            deletingLabel: ''
        };
    },
    created() {
        window.events.$on('open_set_labels_dialog', this.show);
    },
    methods: {
        show(event_note_id) {
            if(this.note_id == event_note_id)
                this.$refs['labels-dialog'].show();
        },
        hide() {
            this.$refs['labels-dialog'].hide();
            this.clearNewLabel();
        },
        showDeleteConfirmation(label) {
            this.$refs['delete-confirmation'].show();
            this.deletingLabel = label;
        },
        hideDeleteConfirmation() {
            this.$refs['delete-confirmation'].hide();
            this.deletingLabel = '';
        },
        deleteLabel() {
            let index = this.labels.indexOf(this.deletingLabel);
            this.labels.splice(index, 1);

            axios.delete('tag/' + this.deletingLabel)
                .then(res => location.href = '');
        },
        focusOnLabel(refName) {
            this.$refs[refName][0].focus();
            this.editingLabel = refName;
        },
        isEditing(refName) {
            return this.editingLabel === refName;
        },
        renameLabel(refName, key) {
            axios.put(this.labels[key], {
                new_name: this.$refs[refName][0].value
            }).then(res => {
                this.labels[key] = this.$refs[refName][0].value;
                this.editingLabel = '';
                location.href = '/';
            });
        },
        hideUniqueError() {
            this.uniqueErrorShown = false;
        },
        addLabel(label) {
            if (label === '')
                return false;

            if (this.labels.includes(label)) {
                this.uniqueErrorShown = true;
                this.clearNewLabel();
                this.hideCancelButton();
                setTimeout(this.hideUniqueError, 2000);
                return false;
            }

            this.clearNewLabel();
            this.hideCancelButton();

            axios.post('tag', {
                'tag_name': label
            }).then(res => this.labels.push(label));

            this.$emit('refreshLabels', this.labels);
        },
        save() {
            this.addLabel(this.newLabel);
            this.hide();
        },
        showCancelButton() {
            this.isCancelButtonVisible = true;
        },
        hideCancelButton() {
            this.isCancelButtonVisible = false;
        },
        setFocusedState(refName) {
            this.isCancelButtonVisible = false;
            this.editingLabel = refName;
        },
        cancel() {
            this.clearNewLabel();
            this.hideCancelButton();
        },
        clearNewLabel() {
            this.newLabel = ''
        },
        showDeleteButtonOn(refName) {
            this.deleteButtonOn = refName;
        },
        hideDeleteButtonOn(refName) {
            if (this.deleteButtonOn === refName)
                this.deleteButtonOn = '';
        },
        isDeleteButtonVisible(refName) {
            return this.deleteButtonOn === refName;
        }
    }
}
</script>

<style scoped>

</style>
