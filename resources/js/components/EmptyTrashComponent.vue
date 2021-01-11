<template>
    <div class="empty-trash">
        <div class="mt-4 mb-6 text-center italic ">
            <p class="text-lg">Notes in Trash are deleted after 7 days
                <button @click="showConfirmation()" class="ml-6 py-2 px-6 text-blue-500 text-sm font-bold hover:bg-blue-50 focus:bg-blue-100 focus:outline-none">Empty Trash</button>
            </p>
        </div>

        <div class="trash-confirmation fixed top-0 left-0 right-0 bottom-0 flex items-center bg-gray-800 bg-opacity-75 z-10"
             v-if="isConfirmationVisible"
             @click.self="hideConfirmation()">
           <div class="trash-content m-auto">
               <div class="bg-white p-6 rounded-t-lg text-center text-sm">
                   Empty trash? All notes in Trash will be permanently deleted.
               </div>
               <div class="bg-white rounded-b-lg py-2 px-4 text-right">
                   <button
                       @click="hideConfirmation()"
                       class="text-gray-800 text-sm font-medium px-6 py-2 mr-2 hover:bg-gray-100 focus:bg-gray-200 focus:outline-none  rounded-sm">
                       Cancel
                   </button>
                   <button
                       @click="emptyTrash()"
                       class="py-2 px-6 text-blue-500 text-sm font-bold hover:bg-blue-50 focus:bg-blue-100 focus:outline-none">
                       Empty Trash
                   </button>
               </div>
           </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "EmptyTrashComponent",
    data: function() {
        return {
            isConfirmationVisible: false
        };
    },
    methods: {
        showConfirmation() {
            this.isConfirmationVisible = true;
        },
        hideConfirmation() {
            this.isConfirmationVisible = false;
        },
        emptyTrash() {
            this.hideConfirmation();
            alert('empty trash');
        }
    }
}
</script>

<style scoped>

</style>
