import Vue from 'vue';
import { BootstrapVue, IconsPlugin } from 'bootstrap-vue'

let VueMasonryPlugin = window["vue-masonry-plugin"].VueMasonryPlugin;
Vue.use(VueMasonryPlugin);

// Import Bootstrap an BootstrapVue CSS files (order is important)
import 'bootstrap/dist/css/bootstrap.css'
import 'bootstrap-vue/dist/bootstrap-vue.css'

// Make BootstrapVue available throughout your project
Vue.use(BootstrapVue)
// Optionally install the BootstrapVue icon components plugin
Vue.use(IconsPlugin)


require('./bootstrap');
require('alpinejs');

window.events = new Vue();

Vue.component('edit-labels-component', require('./components/SetLabelsComponent.vue').default);
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
Vue.component('notes-container-component', require('./components/NotesContainerComponent.vue').default);

new Vue({
    el: '#app'
});

window.onload = function() {
    axios.get('/tag').then( (res) => window.tags_list = res.data );
};
