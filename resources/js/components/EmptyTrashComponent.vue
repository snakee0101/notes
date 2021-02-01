<template>
    <div class="empty-trash">
        <div class="mt-4 mb-6 text-center italic ">
            <p class="text-lg">Notes in Trash are deleted after 7 days
                <button @click="$refs['trash-confirmation'].show()" class="ml-6 empty-trash-button">Empty Trash</button>
            </p>
        </div>

        <b-modal ref="trash-confirmation" hide-footer centered class="delete-confirmation">
            <p class="m-2">Empty trash? All notes in Trash will be permanently deleted.</p>
            <div class="bg-white rounded-b-lg py-2 px-4 text-right">
                <button
                    @click="$refs['trash-confirmation'].hide()"
                    class="cancel-button">
                    Cancel
                </button>
                <button
                    @click="emptyTrash()"
                    class="empty-trash-button">
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
