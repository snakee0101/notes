<template>
    <section class="flex flex-row items-center">
        <a href="" class="p-2 rounded-full hover:bg-gray-200" @click.prevent="toggle()">
            <i class="bi bi-list icon-lg text-black"></i>
        </a>
        <img src="/keep.png" alt="">
        <span class="text-xl">Keep</span>
    </section>
</template>

<script>
export default {
    name: "MenuSwitcher",
    data() {
        return {
            menuCollapsed: localStorage.getItem('menu-collapsed')
        };
    },
    methods: {
        toggle() {
            if(this.menuCollapsed === 'true') {
                this.menuCollapsed = 'false';
                window.events.$emit('menu-expanded');
            }
            else {
                this.menuCollapsed = 'true';
                window.events.$emit('menu-collapsed');
            }
        }
    }
}
</script>

<style scoped>

</style>
