<template>
    <section class="flex flex-row items-center menu-switcher">
        <a href="" class="p-2"
           v-b-tooltip.hover.bottom
           title="Main menu"
           @click.prevent="toggle()">
            <i class="bi bi-list icon menu-icon-color"></i>
        </a>
        <span class="text-xl text-color">Notes</span>
    </section>
</template>

<script>
export default {
    name: "MenuSwitcher",
    data() {
        return {
            menuCollapsed: (localStorage.getItem('menu-collapsed') === 'true')
        };
    },
    methods: {
        toggle() {
            this.menuCollapsed = !this.menuCollapsed;
            localStorage.setItem('menu-collapsed', this.menuCollapsed ? 'true' : 'false');

            window.events.$emit('menu-change-state', this.menuCollapsed);
        }
    }
}
</script>

<style scoped>

</style>
