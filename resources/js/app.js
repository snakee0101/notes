import Vue from 'vue';

require('./bootstrap');
require('alpinejs');

window.events = new Vue();

Vue.component('note-component', require('./components/NoteComponent.vue').default);
Vue.component('new-note-component', require('./components/NewNoteComponent.vue').default);
Vue.component('collaborator-dialog-component', require('./components/CollaboratorDialogComponent.vue').default);
Vue.component('edit-labels-component', require('./components/EditLabelsComponent.vue').default);
Vue.component('empty-trash-component', require('./components/EmptyTrashComponent.vue').default);
Vue.component('search-component', require('./components/SearchComponent.vue').default);
Vue.component('search-controls-component', require('./components/SearchControlsComponent.vue').default);
Vue.component('menu-switcher-component', require('./components/MenuSwitcherComponent.vue').default);
Vue.component('menu-component', require('./components/MenuComponent.vue').default);
Vue.component('notification-component', require('./components/NotificationComponent.vue').default);

new Vue({
    el: '#app'
});
