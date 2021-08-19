<template>
    <a href="#" class="p-2 group"
       v-b-tooltip.hover.bottom
       title="Change theme"
       @click.prevent="changeTheme()">
        <i class="bi text-2xl" :class="(selectedTheme === 'theme-light') ? 'bi-moon text-gray-800 group-hover:text-blue-700'
                                                                         : 'bi-brightness-high group-hover:text-yellow-400'"></i>
    </a>
</template>

<script>
export default {
    name: "ThemeSwitcher",
    data() {
        return {
            selectedTheme: 'theme-light',
        };
    },
    created() {
        this.selectedTheme = localStorage.getItem('theme') ?? 'theme-light';
    },
    watch: {
        selectedTheme() {
            document.body.className = document.body.className.replace(/theme-\w+/, this.selectedTheme);

            localStorage.setItem('theme', this.selectedTheme);
        }
    },
    methods: {
        changeTheme() {
            this.selectedTheme = (this.selectedTheme === 'theme-light') ? 'theme-dark'
                                                                        : 'theme-light';
        }
    }
}
</script>

<style scoped>

</style>
