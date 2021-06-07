<template>
    <div>
        <b-modal ref="labels-dialog" hide-footer centered class="labels-dialog">
            <div class="bg-white p-4 rounded-t-lg">
                <h3 class="font-medium text-lg">Search label</h3>
                <div class="mt-3 pt-4 px-2 border-t-2 border-gray-200">
                    <div class="label flex flex-row mb-3 items-center">
                        <a href="" @click.prevent="cancel()"
                           v-b-tooltip.hover.bottom
                           title="Clear Search"
                           v-if="isCancelButtonVisible">
                            <i class="bi bi-x icon-sm"></i>
                        </a>
                        <input
                            class="border-transparent border-b-2 add-label-input ml-4 flex-grow text-sm focus:outline-none focus:border-gray-200"
                            placeholder="Search for label" required v-model="searchingLabel" id="searchingLabel"
                            v-on:keyup="search()"
                            @focus="setFocusedState(); isCancelButtonVisible = true"
                            @blur="isCancelButtonVisible = false">
                    </div>

                    <div class="label flex flex-row mb-3 items-center" v-for="(label, key) in labels">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="" :id="'tag-' + key"
                                   @focus="setFocusedState('label_' + key)">
                            <label class="form-check-label" :for="'tag-' + key">
                                {{ label }}
                            </label>
                        </div>

                        <!--                        <a href=""
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
                        </a>-->
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
            searchingLabel: '',
            labels: this.$attrs.labels,
            note_id: this.$attrs.note_id,
            isCancelButtonVisible: false,
            editingLabel: '',
        };
    },
    created() {
        window.events.$on('open_set_labels_dialog', this.show);
    },
    methods: {
        search() {
          console.log('searching label on keyup');
          //if empty - show all the labels
          //if not empty - search for label containing specified text
        },
        show(event_note_id) {
            if(this.note_id == event_note_id)
                this.$refs['labels-dialog'].show();
        },
        hide() {
            this.$refs['labels-dialog'].hide();
            this.searchingLabel = '';
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
        save() {
            this.addLabel(this.searchingLabel);
            this.hide();
        },
        setFocusedState(refName) {
            this.isCancelButtonVisible = false;
            this.editingLabel = refName;
        },
        cancel() {
            this.searchingLabel = '';
            this.isCancelButtonVisible = false;
        }
    }
}
</script>

<style scoped>

</style>
