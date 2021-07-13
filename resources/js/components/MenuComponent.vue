<template>
    <nav class="flex flex-col bg-white"
         :class="isCollapsed ? 'collapsed' : ''"
         ref="menu">
        <a href="/" class="p-2.5 pl-4 rounded-r-full hover:bg-gray-200" :class="setActiveLink('notes')">
            <i class="bi bi-lightbulb icon-lg mr-3 text-black"></i> Notes
        </a>
        <a href="/reminder" class="p-2.5 pl-4 rounded-r-full hover:bg-gray-200" :class="setActiveLink('reminder.index')">
            <i class="bi bi-bell icon-lg mr-3 text-black"></i> Reminders
        </a>
        <a :href="'/tag/' + tag_name" class="p-2.5 pl-4 rounded-r-full hover:bg-gray-200" :class="setActiveTagLink(tag_name)" v-for="tag_name in tag_names">
            <i class="bi bi-tag-fill icon-lg mr-3 text-black"></i> {{ tag_name }}
        </a>

        <edit-labels-component :labels="tag_names" v-on:refreshLabels="tag_names = $event">

        </edit-labels-component>

        <a href="/archive" class="p-2.5 pl-4 rounded-r-full hover:bg-gray-200" :class="setActiveLink('archive')">
            <i class="bi bi-save2-fill icon-lg mr-3 text-black"></i> Archive
        </a>
        <a href="/trash" class="p-2.5 pl-4 rounded-r-full hover:bg-gray-200" :class="setActiveLink('trash')">
            <i class="bi bi-trash icon-lg mr-3 text-black"></i> Trash
        </a>
    </nav>
</template>

<script>
export default {
    name: "MenuComponent",
    data() {
        return {
            tag_names: JSON.parse(this.$attrs.tag_names),
            isCollapsed: (localStorage.getItem('menu-collapsed') === 'true')
        };
    },
    created() {
        window.events.$on('menu-change-state', this.setCollapsedState);
        window.events.$on('refreshLabels', this.refreshMenu);
    },
    methods: {
        setCollapsedState(collapsed) {
            this.isCollapsed = collapsed;
        },
        refreshMenu(labels) {
            this.tag_names = labels.valueOf().flat();
        },
        setActiveLink(route) {
            return (this.$attrs.current_route === route) ? 'active' : '';
        },
        setActiveTagLink(tag) {
            return (this.$attrs.tag_link === tag) ? 'active' : '';
        },
    }
}
</script>

<style scoped>

</style>
