<template>
    <b-modal id="edit-note-modal" title="Edit note" ref="edit-note-modal" centered
             v-on:ok="applyChanges()"
             v-on:cancel="cancel()"
             modal-class="edit-note-modal">

        <div class="p-4">
            <h3 class="pb-3">Edit note</h3>

            <h6 class="pb-1">Header</h6>
            <textarea name="note_header" placeholder="Title"
                      class="note-header-input mx-2 focus:outline-none h-auto resize-none font-bold bg-transparent text-xl"
                      v-model="note.header">

            </textarea>

            <div v-if="isChecklist" class="mb-2">
                <h6 class="pb-1">Note content</h6>
                <div class="form-check mb-2 flex flex-col" v-for="(item, index) in note.checklist.tasks">
                    <div class="flex flex-row">
                        <a href="#" @click.prevent="moveItem(item, index, 'up')">
                            <i class="bi bi-arrow-up text-green-700"></i>
                        </a>
                        <a href="#" @click.prevent="moveItem(item, index, 'down')">
                            <i class="bi bi-arrow-down text-red-700"></i>
                        </a>
                        <input class="form-check-input mt-2" type="checkbox" v-model="item.completed"
                               @click="setChecklistItemState(item)">
                        <input type="text" class="flex-grow" v-model="note.checklist.tasks[index].text"
                               :ref="'checklist-item-' + index">
                        <a href="#" @click.prevent="removeChecklistItem(index)"> <span class="bi bi-x text-lg"></span> </a>
                    </div>
                </div>

                <div class="flex flex-row">
                    <button class="btn btn-primary btn-sm" @click="addToChecklist()"><i class="bi bi-plus"></i></button>
                    <input type="text" v-model="newChecklistItem" placeholder="List Item"
                           class="flex-grow ml-2 border-b-2 border-gray-300 focus:outline-none">
                </div>
            </div>

            <div v-else>
                <h6 class="pb-1">Note content</h6>
                <div>
                    <input type="hidden" id="note_content" v-model="note.body">
                    <trix-editor input="note_content" ref="note-editor" @change="note.body = $event.target.value"></trix-editor>
                </div>
            </div>

            <div class="images mt-4" v-if="note.images.length">
                <h6 class="pb-1">Note images (images are saved immediately)</h6>
                <div class="inline-block relative m-2" v-for="(image, index) in note.images">
                    <img :src="image.thumbnail_large_path" style="height: 120px; width: 120px; cursor: pointer" @click="openImageViewer(image)">
                    <a class="bg-gray-300 rounded-full absolute top-1 left-1"
                       v-b-tooltip.hover.bottom
                       title="Delete image"
                       style="cursor: pointer"
                       @click.prevent="delete_image(index)">
                        <i class="bi bi-x icon"></i>
                    </a>
                </div>
            </div>

            <p class="mt-2">
                <button class="btn btn-success btn-sm" @click="selectImage()">Add image</button>
                <input type="file" ref="image" class="hidden" accept="image/jpeg,image/png,image/gif"
                       @change="handleFile()">

                <button class="btn btn-danger btn-sm" @click="convertToText()" v-if="isChecklist">Hide checkboxes</button>
                <button class="btn btn-warning btn-sm" @click="uncheckAll()" v-if="isChecklist">Uncheck all</button>
                <button class="btn btn-danger btn-sm" @click="removeCompleted()" v-if="isChecklist">Remove completed</button>
                <button class="btn btn-primary btn-sm" @click="convertToChecklist()" v-else>Show checkboxes</button>
            </p>

            <div v-for="link in note.links" class="mt-1">
                <div class="flex flex-row mt-2 items-center">
                    <img style="height: 40px; width: 40px"
                         :src="link.favicon_path" alt="" v-if="link.favicon_path">
                    <i class="bi bi-globe text-3xl text-center mt-1" style="height: 40px; width: 40px" v-else></i>
                    <div class="ml-2 flex-grow">
                        <h5 class="m-0 text-blue-400">
                            <a :href="link.url"
                               target="_blank"">
                                {{ link.name }}
                            </a>
                        </h5>
                        <p class="text-sm m-0 text-gray-600">{{ link.domain }}</p>
                    </div>
                    <div>
                        <a href="" class="hover:bg-gray-300 rounded-full p-0 inline-block"
                           v-b-tooltip.hover.top
                           title="More"
                           @click.prevent>
                            <b-dropdown size="sm" variant="link" toggle-class="text-decoration-none" no-caret>
                                <template #button-content>
                                    <i class="bi bi-three-dots-vertical icon-sm p-0"></i>
                                </template>
                                <b-dropdown-item href="#" @click="removeLink(link)">Remove</b-dropdown-item>
                                <b-dropdown-item href="#" @click="copyLinkURL(link)">Copy URL</b-dropdown-item>
                            </b-dropdown>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </b-modal>
</template>

<script>
export default {

    //TODO: reflect these changes in NoteComponent in container
    //TODO: image could be viewed in a full-screen mode

    name: "EditNoteComponent",
    data() {
        return {
            isChecklist: false,
            newChecklistItem: '',
            note: {},
        };
    },
    created() {
        window.events.$on('open_note_for_editing', this.openModal);
    },
    methods: {
        removeLink(link) {
            axios.delete('/link/' + link.id);
            //window.events.$emit('show-notification', 'Image deleted', this.undoImageDeletion);
        },
        copyLinkURL(link) {
            navigator.clipboard.writeText(link.url);
        },
        //apply changes to this.note.checklist.tasks object directly
        addToChecklist() {
            this.note.checklist.tasks.push({
                text: this.newChecklistItem,
                completed: false
            });

            this.newChecklistItem = '';
        },
        uncheckAll() {
            this.note.checklist.tasks = this.note.checklist.tasks.map( function(task) {
                return {
                    text : task.text,
                    completed: false
                };
            } );
        },
        removeCompleted() {
            this.note.checklist.tasks = this.note.checklist.tasks.filter( task => task.completed == false );

            if(this.note.checklist.id != null)
                axios.post('/checklist/remove_completed/' + this.note.checklist.id);
        },
        updateNote(note) {
            this.note = note;
            window.events.$emit('reload_note', this.note);
        },
        removeChecklistItem(index) {
            this.note.checklist.tasks.splice(index, 1);
        },
        setChecklistItemState(item) {
            item.completed = event.target.checked;
        },
        convertToChecklist() {
            let unformatted_text = this.$refs['note-editor'].editor.element.innerText;
            let items = unformatted_text.split(/\n/m);
            let blanks_deleted = items.filter(function (item) {
                return !(new RegExp(/^\s+$/)).test(item); //remove spaces
            }).filter(function (item) {
                return item != ''; //remove empty lines
            }).map(function (text) {
                return {
                    text: text,
                    completed: false
                };
            });

            this.note.checklist.tasks = blanks_deleted;
            this.isChecklist = true;
        },
        convertToText() {
            let _text = this.note.checklist.tasks.reduce(function (accumulator, task) {
                return accumulator + task.text + '<br>';
            }, '');

            this.note.body = _text;

            this.note.checklist.tasks = [];
            this.isChecklist = false;
        },
        openModal(note) {
            this.note = JSON.parse(JSON.stringify(note));

            if(this.note.checklist && this.note.checklist.tasks.length > 0) {
                this.isChecklist = true;
            } else {
                this.note.checklist = {tasks : []};
                this.isChecklist = false;
            }

            this.$refs["edit-note-modal"].show();
        },
        applyChanges() {
            if(!this.isChecklist)
                this.note.body = this.$refs['note-editor'].value;

            axios.put('/note/' + this.note.id, {
                header: this.note.header,
                body: this.note.body
            });

            if(this.isChecklist){
                 if(this.note.checklist.id) {//if the note has already had checklist - replace all checklist items at once
                    axios.put('/checklist/' + this.note.checklist.id, {
                       tasks :  this.note.checklist.tasks
                    }).then(res => this.note = res.data);
                } else { //else - create a new checklist
                    axios.post('/checklist', {
                        'checklist_data': this.note.checklist.tasks,
                        'note_id': this.note.id
                    }).then(res => this.note = res.data);
                }
            } else {
                axios.post('/checklist/delete/' + this.note.id)
                     .then(res => this.note = res.data);
            }

            window.events.$emit('reload_note', this.note);
        },
        cancel() {
            this.$refs["edit-note-modal"].hide();
        },
        selectImage() {
            this.$refs['image'].click();
        },
        handleFile() {
            let file = this.$refs['image'].files[0];
            const reader = new FileReader();

            reader.onloadend = () => {
                let image = this.$refs['image'].files[0];

                let data = new FormData();
                data.append('image', image, image.name);
                data.append('note_id', this.note.id);

                axios.post('/image', data).then( (res) => this.note.images.push(res.data) );
            };

            reader.readAsDataURL(file);
        },
        delete_image(index) {
            axios.post('/image/delete/' + this.note.images[index].id)
                 .then(res => this.deleteImageCallback(res.data, index));
        },
        deleteImageCallback(deleted_image_id, index) {
            window.deleted_image_id = deleted_image_id;

            this.note.images.splice(index, 1);
            window.events.$emit('show-notification', 'Image deleted', this.undoImageDeletion);
        },
        undoImageDeletion() {
            axios.put('/image/restore/' + window.deleted_image_id)
                 .then( (res) => this.note.images.push(res.data) );

            window.events.$emit('show-notification', 'Action undone');
        },
        openImageViewer(current_image) {
            window.events.$emit('open-image-viewer', current_image, this.note.images);
        },
        moveItem(item, index, direction) {
            if((index === 0) && direction === 'up')
                return;

            if((index === this.note.checklist.tasks.length - 1) && direction === 'down')
                return;

            let increment = (direction === 'up') ? -1 : +1;

            let item_1 = this.note.checklist.tasks[index];
            let item_2 = this.note.checklist.tasks[index + increment];

            this.$set(this.note.checklist.tasks, index, item_2);
            this.$set(this.note.checklist.tasks, index + increment, item_1);
        },
    },
}
</script>

<style scoped>

</style>
