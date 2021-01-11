import Vue from 'vue';

require('./bootstrap');
require('alpinejs');

Vue.component('note-component', require('./components/NoteComponent.vue').default);
Vue.component('collaborator-dialog-component', require('./components/CollaboratorDialogComponent.vue').default);
Vue.component('edit-labels-component', require('./components/EditLabelsComponent.vue').default);
Vue.component('empty-trash-component', require('./components/EmptyTrashComponent.vue').default);
Vue.component('search-component', require('./components/SearchComponent.vue').default);
Vue.component('search-controls-component', require('./components/SearchControlsComponent.vue').default);

new Vue({
    el: '#app'
});
