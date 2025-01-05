import Vue from 'vue';
import { BootstrapVue, IconsPlugin } from 'bootstrap-vue';
import Masonry from 'masonry-layout';

// Import Bootstrap an BootstrapVue CSS files (order is important)
import 'bootstrap/dist/css/bootstrap.css';
import 'bootstrap-vue/dist/bootstrap-vue.css';

// Make BootstrapVue available throughout your project
Vue.use(BootstrapVue);
// Optionally install the BootstrapVue icon components plugin
Vue.use(IconsPlugin);


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
Vue.component('top-bar-component', require('./components/TopBarComponent.vue').default);
Vue.component('edit-note-component', require('./components/EditNoteComponent.vue').default);
Vue.component('image-viewer-component', require('./components/ImageViewerComponent.vue').default);
Vue.component('drawing-dialog-component', require('./components/DrawingDialogComponent.vue').default);
Vue.component('photo-capture-dialog-component', require('./components/PhotoCaptureDialogComponent.vue').default);
Vue.component('theme-switcher-component', require('./components/ThemeSwitcher.vue').default);


new Vue({
    el: '#app'
});

window.onload = function () {
    // open note for editing immediately if its id is present in URL hash
    axios.get('/tag').then( (res) => window.tags_list = res.data );

    let note_id_from_hash = location.hash.substr(1);
    
    if (note_id_from_hash != '') {
        axios.get('/note/' + note_id_from_hash)
            .then((res) => window.events.$emit('open_note_for_editing', res.data));
    }

    window.masonry_layouts = [];

    document.querySelectorAll('.grid').forEach((grid) => { //documentation: https://masonry.desandro.com/methods
        window.masonry_layouts.push(new Masonry(grid, {
            itemSelector: '.grid-item',
            columnWidth: 270,
            gutter: 10
        }));
    });

    window.events.$on('refresh-all-masonry-layouts', function () {
        window.masonry_layouts.forEach(l => l.layout());
    })
};

window.onhashchange = function () {
    //if hash part of url changes - open another note, whic id is in the hash
    let note_id_from_hash = location.hash.substr(1);

    if (note_id_from_hash != '') {
        axios.get('/note/' + note_id_from_hash)
            .then((res) => window.events.$emit('open_note_for_editing', res.data));
    }
};


// display in-browser notification for a note reminder
if (!Notification.permission !== 'granted') {
    Notification.requestPermission();
}

window.redirectToNote = function(){
    location.hash = window.notificationData.link;
};

window.Echo.private('App.Models.User.' + window.userId)
    .notification(function(notification) {
        window.notificationData = notification;
        new Notification(notification.reminder_text).onclick = window.redirectToNote;
    });