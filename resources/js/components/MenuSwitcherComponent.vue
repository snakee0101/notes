<template>
    <section class="flex flex-row items-center">
        <a href="" class="pt-2 px-2 pb-3 rounded-full hover:bg-gray-200"
           @click.prevent="toggle()">
            <svg class="icon icon-menu" viewBox="0 0 32 32">
                <path d="M2 6h28v6h-28zM2 14h28v6h-28zM2 22h28v6h-28z"></path>
            </svg>
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
            menuCollapsed: false
        };
    },
    methods: {
        toggle() {
            this.menuCollapsed = !this.menuCollapsed;

            if(this.menuCollapsed)
                window.events.$emit('menu-collapsed')
            else
                window.events.$emit('menu-expanded');
        }
    }
}
</script>

<style scoped>

</style>
