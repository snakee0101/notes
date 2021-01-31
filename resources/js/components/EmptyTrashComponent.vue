<template>
    <div class="empty-trash">
        <div class="mt-4 mb-6 text-center italic ">
            <p class="text-lg">Notes in Trash are deleted after 7 days
                <button @click="$refs['trash-confirmation'].show()" class="ml-6 py-2 px-6 text-blue-500 text-sm font-bold hover:bg-blue-50 focus:bg-blue-100 focus:outline-none">Empty Trash</button>
            </p>
        </div>

        <b-modal ref="trash-confirmation" hide-footer centered class="delete-confirmation">
            <p class="m-2">Empty trash? All notes in Trash will be permanently deleted.</p>
            <div class="bg-white rounded-b-lg py-2 px-4 text-right">
                <button
                    @click="$refs['trash-confirmation'].hide()"
                    class="text-gray-800 text-sm font-medium px-6 py-2 mr-2 hover:bg-gray-100 focus:bg-gray-200 focus:outline-none  rounded-sm">
                    Cancel
                </button>
                <button
                    @click="emptyTrash()"
                    class="py-2 px-6 text-blue-500 text-sm font-bold hover:bg-blue-50 focus:bg-blue-100 focus:outline-none">
                    Empty Trash
                </button>
            </div>
        </b-modal>
    </div>
</template>

<script>
export default {
    name: "EmptyTrashComponent",
    methods: {
        emptyTrash() {
            this.$refs['trash-confirmation'].hide();
            axios.delete('/trash/empty').then(
                res => location.reload()
            );
        }
    }
}
</script>

<style scoped>

</style>
