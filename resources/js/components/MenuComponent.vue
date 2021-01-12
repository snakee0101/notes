<template>
    <nav class="flex flex-col" style="width: 280px" :class="setCollapsedState()">
        <a href="/" class="p-4 rounded-r-full hover:bg-gray-200" :class="setActiveLink('notes')">
            <svg class="icon icon-bulb mr-3" viewBox="0 0 32 32">
                <path
                    d="M16 0c-6.076 0-11 4.924-11 11 0 4.031 3.688 8.303 5.031 12.055 2.003 5.595 1.781 8.945 5.969 8.945 4.25 0 3.965-3.334 5.969-8.922 1.347-3.76 5.031-8.078 5.031-12.078 0-6.076-4.926-11-11-11zM18.592 27.176l-4.958 0.619c-0.177-0.512-0.367-1.111-0.598-1.893-0.003-0.010-0.007-0.021-0.009-0.031l6.188-0.773c-0.088 0.295-0.182 0.605-0.264 0.883-0.131 0.449-0.248 0.839-0.359 1.195zM12.736 24.908c-0.182-0.602-0.387-1.236-0.615-1.908h7.766c-0.123 0.359-0.246 0.719-0.352 1.059l-6.799 0.849zM16 30c-1.013 0-1.479-0.117-1.997-1.25l4.238-0.531c-0.614 1.654-1.061 1.781-2.241 1.781zM20.672 21h-9.333c-0.498-1.080-1.096-2.16-1.686-3.217-1.305-2.335-2.653-4.75-2.653-6.783 0-4.963 4.037-9 9-9s9 4.037 9 9c0 2.018-1.35 4.446-2.656 6.795-0.584 1.053-1.178 2.131-1.672 3.205zM16 5c0.275 0 0.5 0.224 0.5 0.5s-0.224 0.5-0.5 0.5c-2.757 0-5 2.243-5 5 0 0.276-0.224 0.5-0.5 0.5s-0.5-0.224-0.5-0.5c0-3.309 2.691-6 6-6z"></path>
            </svg>
            Notes
        </a>
        <a href="/reminders" class="p-4 rounded-r-full hover:bg-gray-200" :class="setActiveLink('reminders')">
            <svg class="icon icon-bell mr-3" viewBox="0 0 32 32">
                <path
                    d="M32.047 25c0-9-8-7-8-14 0-0.58-0.056-1.076-0.158-1.498-0.526-3.532-2.88-6.366-5.93-7.23 0.027-0.123 0.041-0.251 0.041-0.382 0-1.040-0.9-1.891-2-1.891s-2 0.851-2 1.891c0 0.131 0.014 0.258 0.041 0.382-3.421 0.969-5.966 4.416-6.039 8.545-0.001 0.060-0.002 0.121-0.002 0.183 0 7-8 5-8 14 0 2.382 5.331 4.375 12.468 4.878 0.673 1.263 2.002 2.122 3.532 2.122s2.86-0.86 3.532-2.122c7.137-0.503 12.468-2.495 12.468-4.878 0-0.007-0.001-0.014-0.001-0.021l0.048 0.021zM25.82 26.691c-1.695 0.452-3.692 0.777-5.837 0.958-0.178-2.044-1.893-3.648-3.984-3.648s-3.805 1.604-3.984 3.648c-2.144-0.18-4.142-0.506-5.837-0.958-2.332-0.622-3.447-1.318-3.855-1.691 0.408-0.372 1.523-1.068 3.855-1.691 2.712-0.724 6.199-1.122 9.82-1.122s7.109 0.398 9.82 1.122c2.332 0.622 3.447 1.318 3.855 1.691-0.408 0.372-1.523 1.068-3.855 1.691z"></path>
            </svg>
            Reminders
        </a>
        <a href="/tag/tag 1" class="p-4 rounded-r-full hover:bg-gray-200" :class="setActiveTagLink('tag 1')">
            <svg class="icon icon-price-tags mr-3" viewBox="0 0 40 32">
                <path
                    d="M38.5 0h-12c-0.825 0-1.977 0.477-2.561 1.061l-14.879 14.879c-0.583 0.583-0.583 1.538 0 2.121l12.879 12.879c0.583 0.583 1.538 0.583 2.121 0l14.879-14.879c0.583-0.583 1.061-1.736 1.061-2.561v-12c0-0.825-0.675-1.5-1.5-1.5zM31 12c-1.657 0-3-1.343-3-3s1.343-3 3-3 3 1.343 3 3-1.343 3-3 3z"></path>
                <path
                    d="M4 17l17-17h-2.5c-0.825 0-1.977 0.477-2.561 1.061l-14.879 14.879c-0.583 0.583-0.583 1.538 0 2.121l12.879 12.879c0.583 0.583 1.538 0.583 2.121 0l0.939-0.939-13-13z"></path>
            </svg>
            Tag 1
        </a>
        <a href="/tag/tag 2" class="p-4 rounded-r-full hover:bg-gray-200" :class="setActiveTagLink('tag 2')">
            <svg class="icon icon-price-tags mr-3" viewBox="0 0 40 32">
                <path
                    d="M38.5 0h-12c-0.825 0-1.977 0.477-2.561 1.061l-14.879 14.879c-0.583 0.583-0.583 1.538 0 2.121l12.879 12.879c0.583 0.583 1.538 0.583 2.121 0l14.879-14.879c0.583-0.583 1.061-1.736 1.061-2.561v-12c0-0.825-0.675-1.5-1.5-1.5zM31 12c-1.657 0-3-1.343-3-3s1.343-3 3-3 3 1.343 3 3-1.343 3-3 3z"></path>
                <path
                    d="M4 17l17-17h-2.5c-0.825 0-1.977 0.477-2.561 1.061l-14.879 14.879c-0.583 0.583-0.583 1.538 0 2.121l12.879 12.879c0.583 0.583 1.538 0.583 2.121 0l0.939-0.939-13-13z"></path>
            </svg>
            Tag 2
        </a>

        <edit-labels-component :labels="['label 1','label 2']">

        </edit-labels-component>

        <a href="/archive" class="p-4 rounded-r-full hover:bg-gray-200" :class="setActiveLink('archive')">
            <svg class="icon icon-box-add mr-3" viewBox="0 0 32 32">
                <path
                    d="M26 2h-20l-6 6v21c0 0.552 0.448 1 1 1h30c0.552 0 1-0.448 1-1v-21l-6-6zM16 26l-10-8h6v-6h8v6h6l-10 8zM4.828 6l2-2h18.343l2 2h-22.343z"></path>
            </svg>
            Archive
        </a>
        <a href="/trash" class="p-4 rounded-r-full hover:bg-gray-200" :class="setActiveLink('trash')">
            <svg class="icon icon-bin mr-3" viewBox="0 0 32 32">
                <path
                    d="M4 10v20c0 1.1 0.9 2 2 2h18c1.1 0 2-0.9 2-2v-20h-22zM10 28h-2v-14h2v14zM14 28h-2v-14h2v14zM18 28h-2v-14h2v14zM22 28h-2v-14h2v14z"></path>
                <path
                    d="M26.5 4h-6.5v-2.5c0-0.825-0.675-1.5-1.5-1.5h-7c-0.825 0-1.5 0.675-1.5 1.5v2.5h-6.5c-0.825 0-1.5 0.675-1.5 1.5v2.5h26v-2.5c0-0.825-0.675-1.5-1.5-1.5zM18 4h-6v-1.975h6v1.975z"></path>
            </svg>
            Trash
        </a>
    </nav>
</template>

<script>
export default {
    name: "MenuComponent",
    created() {
        window.events.$on('menu-collapsed', function () {
            localStorage.setItem('menu-collapsed', true);

            let navigation = document.getElementsByTagName('nav')[0];
            navigation.classList.add('collapsed');
        });

        window.events.$on('menu-expanded', function () {
            localStorage.setItem('menu-collapsed', false);

            let navigation = document.getElementsByTagName('nav')[0];
            navigation.classList.remove('collapsed');
        });
    },
    methods: {
        setActiveLink(route) {
            return (this.$attrs.current_route === route) ? 'active' : '';
        },
        setActiveTagLink(tag) {
            return (this.$attrs.tag_link === tag) ? 'active' : '';
        },
        setCollapsedState() {
            return (window.localStorage.getItem('menu-collapsed') == 'true') ? 'collapsed' : '';
        }
    }
}
</script>

<style scoped>

</style>
