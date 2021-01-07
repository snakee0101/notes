<template>
    <div class="note border border-gray-300 p-3 hover:shadow-md relative">
        <a href="" class="absolute right-1 top-1 hover:bg-gray-300 p-2 rounded-full" @click.prevent="pin()">
            <svg class="icon icon-small icon-pushpin" viewBox="0 0 32 32">
                <path d="M17 0l-3 3 3 3-7 8h-7l5.5 5.5-8.5 11.269v1.231h1.231l11.269-8.5 5.5 5.5v-7l8-7 3 3 3-3-15-15zM14 17l-2-2 7-7 2 2-7 7z"></path>
            </svg>
        </a>

        <h3 class="font-bold">Note's header</h3>
        <div class="note-content my-4 leading-6 overflow-hidden break-words" style="max-height: 300px">
            note's content
            note's content
            note's content
            </div>
        <div class="toolbar">
            <p>toolbar</p>
        </div>
    </div>
</template>

<script>
export default {
    name: "NoteComponent",
    data() {
        return {
            pinned: false
        };
    },
    methods: {
        pin() {
            this.pinned = !this.pinned;
        }
    }
}
</script>

<style scoped>

</style>
