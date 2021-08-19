<template>
    <nav class="flex flex-col"
         :class="isCollapsed ? 'collapsed' : ''"
         ref="menu">
        <a href="/" class="p-2.5 pl-4 rounded-r-full hover:bg-gray-200" :class="setActiveLink('notes')">
            <i class="bi bi-lightbulb icon-lg mr-3 menu-icon-color"></i> Notes
        </a>
        <a href="/reminder" class="p-2.5 pl-4 rounded-r-full hover:bg-gray-200" :class="setActiveLink('reminder.index')">
            <i class="bi bi-bell icon-lg mr-3 menu-icon-color"></i> Reminders
        </a>
        <a href="/collaborator" class="p-2.5 pl-4 rounded-r-full hover:bg-gray-200" :class="setActiveLink('collaborator.index')">
            <i class="bi bi-people icon-lg mr-3 menu-icon-color"></i> Collaborator notes
        </a>

        <a :href="'/tag/' + tag_item.name" class="p-2.5 pl-4 rounded-r-full hover:bg-gray-200" :class="setActiveTagLink(tag_item)" v-for="tag_item in tags_list">
            <i class="bi bi-tag-fill icon-lg mr-3 menu-icon-color"></i> {{ tag_item.name }}
        </a>

        <edit-labels-component :labels="tags_list" v-on:refreshLabels="tags_list = $event">

        </edit-labels-component>

        <a href="/archive" class="p-2.5 pl-4 rounded-r-full hover:bg-gray-200" :class="setActiveLink('archive')">
            <i class="bi bi-save2-fill icon-lg mr-3 menu-icon-color"></i> Archive
        </a>
        <a href="/trash" class="p-2.5 pl-4 rounded-r-full hover:bg-gray-200" :class="setActiveLink('trash')">
            <i class="bi bi-trash icon-lg mr-3 menu-icon-color"></i> Trash
        </a>
    </nav>
</template>

<script>
export default {
    name: "MenuComponent",
    props: ['tag', 'tags', 'current_route'],
    data() {
        return {
            tags_list: JSON.parse(this.tags),
            current_tag: JSON.parse(this.tag),
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
            this.tags_list = labels;
        },
        setActiveLink(route) {
            return (this.current_route === route) ? 'active' : '';
        },
        setActiveTagLink(tag) {
            return (this.current_tag.name === tag.name) ? 'active' : '';
        },
    }
}
</script>

<style scoped>

</style>
