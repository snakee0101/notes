import Vue from 'vue';

require('./bootstrap');
require('alpinejs');

Vue.component('note-component', require('./components/NoteComponent.vue').default);

new Vue({
    el: '#app'
});
