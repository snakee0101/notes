<template> <!--TODO: Доделать диалог Edit Labels-->
    <div>
        <a href="" class="p-4 rounded-r-full hover:bg-gray-200 block" @click.prevent="show()">
            <svg class="icon icon-pencil2 mr-3" viewBox="0 0 32 32">
                <path d="M12 20l4-2 14-14-2-2-14 14-2 4zM9.041 27.097c-0.989-2.085-2.052-3.149-4.137-4.137l3.097-8.525 4-2.435 12-12h-6l-12 12-6 20 20-6 12-12v-6l-12 12-2.435 4z"></path>
            </svg>
            <span>Edit labels</span>
        </a>

        <div class="delete-confirmation fixed top-0 left-0 right-0 bottom-0 flex items-center bg-gray-800 bg-opacity-75 z-20"
             v-if="isDeleteConfirmationVisible"
             @click.self="hideDeleteConfirmation()">
            <div class="trash-content m-auto">
                <div class="bg-white p-6 rounded-t-lg text-center text-sm">
                    We’ll delete this label and remove it from all of your notes. Your notes won’t be deleted.
                </div>
                <div class="bg-white rounded-b-lg py-2 px-4 text-right">
                    <button
                        @click="hideDeleteConfirmation()"
                        class="text-gray-800 text-sm font-medium px-6 py-2 mr-2 hover:bg-gray-100 focus:bg-gray-200 focus:outline-none  rounded-sm">
                        Cancel
                    </button>
                    <button
                        @click="deleteLabel()"
                        class="py-2 px-6 text-red-500 text-sm font-bold hover:bg-red-50 focus:bg-red-100 focus:outline-none">
                        Delete
                    </button>
                </div>
            </div>
        </div>


        <div v-if="isDialogVisible"
             @mousedown.self="hide()"
             class="labels-dialog fixed top-0 left-0 right-0
             bottom-0 flex items-center bg-gray-800 bg-opacity-75 z-10">
            <div class="labels-content m-auto">
                <div class="bg-white p-4 rounded-t-lg">
                    <h3 class="font-medium text-lg">Edit labels</h3>
                    <div class="mt-3 pt-4 px-2 border-t-2 border-gray-200">
                        <div class="label flex flex-row mb-4 items-center">
                            <a href="" @click.prevent="cancel()" v-if="isCancelButtonVisible">
                                <svg class="icon icon-xs icon-close cursor-pointer" viewBox="0 0 20 20">
                                    <path d="M10 8.586l-7.071-7.071-1.414 1.414 7.071 7.071-7.071 7.071 1.414 1.414 7.071-7.071 7.071 7.071 1.414-1.414-7.071-7.071 7.071-7.071-1.414-1.414-7.071 7.071z"></path>
                                </svg>
                            </a>
                            <label for="newLabel" class="cursor-pointer" @click="clearNewLabel()" v-if="!isCancelButtonVisible">
                                <svg class="icon icon-xs icon-plus" viewBox="0 0 32 32">
                                    <path d="M31 12h-11v-11c0-0.552-0.448-1-1-1h-6c-0.552 0-1 0.448-1 1v11h-11c-0.552 0-1 0.448-1 1v6c0 0.552 0.448 1 1 1h11v11c0 0.552 0.448 1 1 1h6c0.552 0 1-0.448 1-1v-11h11c0.552 0 1-0.448 1-1v-6c0-0.552-0.448-1-1-1z"></path>
                                </svg>
                            </label>
                            <input class="border-transparent border-b-2 add-label-input ml-4 flex-grow text-sm focus:outline-none focus:border-gray-200"
                                   placeholder="Create new label" required v-model="newLabel" id="newLabel"
                                   v-on:keyup.enter="addLabel(newLabel)"
                                   @focus="setFocusedState(); showCancelButton()">
                            <div class="tooltip">
                                <a href="" class="pt-1 px-2 pb-2 rounded-full hover:bg-gray-200"
                                   @click.prevent="addLabel(newLabel)">
                                    <svg class="icon icon-xs icon-checkmark" viewBox="0 0 32 32">
                                        <path d="M27 4l-15 15-7-7-5 5 12 12 20-20z"></path>
                                    </svg>
                                </a>
                                <span class="tooltip4text">Add label</span>
                            </div>
                        </div>

                        <p class="mb-4 text-red-700" v-if="uniqueErrorShown">Tag names must be unique</p>

                        <div class="label flex flex-row mb-4 items-center" v-for="(label, key) in labels">
                            <a href=""
                               @click.prevent="showDeleteConfirmation(label)"
                               @mouseout="hideDeleteButtonOn('label_' + key)"
                               v-if="isEditing('label_' + key) || isDeleteButtonVisible('label_' + key)">
                                <svg class="icon icon-xs icon-bin" viewBox="0 0 32 32">
                                    <path d="M4 10v20c0 1.1 0.9 2 2 2h18c1.1 0 2-0.9 2-2v-20h-22zM10 28h-2v-14h2v14zM14 28h-2v-14h2v14zM18 28h-2v-14h2v14zM22 28h-2v-14h2v14z"></path>
                                    <path d="M26.5 4h-6.5v-2.5c0-0.825-0.675-1.5-1.5-1.5h-7c-0.825 0-1.5 0.675-1.5 1.5v2.5h-6.5c-0.825 0-1.5 0.675-1.5 1.5v2.5h26v-2.5c0-0.825-0.675-1.5-1.5-1.5zM18 4h-6v-1.975h6v1.975z"></path>
                                </svg>
                            </a>

                            <svg class="icon icon-xs icon-pricetag" viewBox="0 0 32 32"
                                 @mouseover="showDeleteButtonOn('label_' + key)"
                                 v-else>
                                <path d="M30.5 0h-12c-0.825 0-1.977 0.477-2.561 1.061l-14.879 14.879c-0.583 0.583-0.583 1.538 0 2.121l12.879 12.879c0.583 0.583 1.538 0.583 2.121 0l14.879-14.879c0.583-0.583 1.061-1.736 1.061-2.561v-12c0-0.825-0.675-1.5-1.5-1.5zM23 12c-1.657 0-3-1.343-3-3s1.343-3 3-3 3 1.343 3 3-1.343 3-3 3z"></path>
                            </svg>

                            <input type="text"
                                   @focus="setFocusedState('label_' + key)"
                                   :value="label" :ref="'label_' + key"
                                   class="border-transparent border-b-2 add-label-input ml-4 flex-grow text-sm focus:outline-none focus:border-gray-200">

                            <div class="tooltip">
                                <a href="" class="pt-1 px-2 pb-2 rounded-full hover:bg-gray-200"
                                   @click.prevent="focusOnLabel('label_' + key)"
                                   v-if="isEditing('label_' + key) === false">
                                    <svg class="icon icon-xs icon-pencil" viewBox="0 0 32 32">
                                        <path d="M27 0c2.761 0 5 2.239 5 5 0 1.126-0.372 2.164-1 3l-2 2-7-7 2-2c0.836-0.628 1.874-1 3-1zM2 23l-2 9 9-2 18.5-18.5-7-7-18.5 18.5zM22.362 11.362l-14 14-1.724-1.724 14-14 1.724 1.724z"></path>
                                    </svg>
                                </a>
                                <a href="" class="pt-1 px-2 pb-2 rounded-full hover:bg-gray-200"
                                   @click.prevent="renameLabel('label_' + key, key)"
                                   v-else>
                                    <svg class="icon icon-xs icon-checkmark" viewBox="0 0 32 32">
                                        <path d="M27 4l-15 15-7-7-5 5 12 12 20-20z"></path>
                                    </svg>
                                </a>
                                <span class="tooltip3text">Rename label</span>  <!--TODO: There should be confirmation dialog for deleting a label-->
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-200 rounded-b-lg py-2 px-4 text-right">
                    <button
                        @click="hide()"
                        class="text-gray-800 text-sm font-medium px-6 py-2 mr-2 hover:bg-gray-300 focus:outline-none focus:bg-gray-400 rounded-sm">
                        Done
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "EditLabelsComponent",
    data() {
        return {
            newLabel: '',
            labels: this.$attrs.labels,
            isDialogVisible: false,
            isCancelButtonVisible: false,
            editingLabel: '',
            deleteButtonOn: '',
            uniqueErrorShown: false,
            isDeleteConfirmationVisible: false,
            deletingLabel: ''
        };
    },
    methods: {
        show() {
            this.isDialogVisible = true;
        },
        hide() {
            this.isDialogVisible = false;
            this.clearNewLabel();
        },
        showDeleteConfirmation(label) {
            this.isDeleteConfirmationVisible = true;
            this.deletingLabel = label;
        },
        hideDeleteConfirmation() {
            this.isDeleteConfirmationVisible = false;
            this.deletingLabel = '';
        },
        deleteLabel() {
            let index = this.labels.indexOf(this.deletingLabel);
            this.labels.splice(index,1);

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
            axios.put('tag/' + this.labels[key], {
                new_name : this.$refs[refName][0].value
            }).then(res => {
                this.labels[key] = this.$refs[refName][0].value;
                this.editingLabel = '';
            });
        },
        hideUniqueError() {
            this.uniqueErrorShown = false;
        },
        addLabel(label) {
            if (label === '')
                return false;

            if (this.labels.includes(label))
            {
                this.uniqueErrorShown = true;
                this.clearNewLabel();
                this.hideCancelButton();
                setTimeout(this.hideUniqueError,2000);
                return false;
            }

            this.clearNewLabel();
            this.hideCancelButton();

            axios.post('tag', {
                'tag_name' : label
            }).then(res => this.labels.push(label));
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
