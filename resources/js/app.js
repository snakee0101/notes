import Vue from 'vue';

require('./bootstrap');
require('alpinejs');

Vue.component('note-component', require('./components/NoteComponent.vue').default);
Vue.component('collaborator-dialog-component', require('./components/CollaboratorDialogComponent.vue').default);

new Vue({
    el: '#app'
});
