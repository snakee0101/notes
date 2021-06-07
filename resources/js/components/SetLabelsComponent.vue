<template>
    <div>
        <b-modal ref="labels-dialog" hide-footer centered class="labels-dialog">
            <div class="bg-white p-4 rounded-t-lg">
                <h3 class="font-medium text-lg">Set labels for the note</h3>
                <div class="mt-3 pt-4 px-2 border-t-2 border-gray-200">
                    <div class="label flex flex-row mb-3 items-center">
                        <a href="" @click.prevent="cancel()"
                           v-b-tooltip.hover.bottom
                           title="Clear Search"
                           v-if="isCancelButtonVisible">
                            <i class="bi bi-x icon-sm"></i>
                        </a>
                        <input class="border-transparent border-b-2 add-label-input ml-4 flex-grow text-sm focus:outline-none focus:border-gray-200"
                            placeholder="Search for label" required id="searchingLabel"
                            v-on:keyup="search()"
                            @focus="isCancelButtonVisible = true"
                            @blur="isCancelButtonVisible = false"
                            v-model="searchingLabel">
                    </div>

                    <div class="form-check mb-3" v-for="(label, key) in labels">
                        <input class="form-check-input" type="checkbox" value="" :id="'tag-' + key">
                        <label class="form-check-label" :for="'tag-' + key">
                            {{ label }}
                        </label>
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
        };
    },
    created() {
        window.events.$on('open_set_labels_dialog', this.show);
    },
    methods: {
        getTags() {
            this.labels = window.tags_list;
        },
        search() {
            console.log('searching label on keyup');
            //if empty - show all the labels
            //if not empty - search for label containing specified text
        },
        show(event_note_id) {
            if (this.note_id == event_note_id) {
                this.getTags();
                this.$refs['labels-dialog'].show();
            }
        },
        hide() {
            this.$refs['labels-dialog'].hide();
            this.searchingLabel = '';
        },
        focusOnLabel(refName) {
            this.$refs[refName][0].focus();
        },
        cancel() {
            this.searchingLabel = "";
            this.isCancelButtonVisible = false;
        }
    }
}
</script>

<style scoped>

</style>
