<template>
    <div class="mt-4 mb-6 text-center italic ">
        <p class="text-lg">Notes in Trash are deleted after 7 days
            <button @click="showConfirmation()" class="ml-6 py-2 px-6 text-blue-500 text-sm font-bold hover:bg-blue-50 focus:bg-blue-100 focus:outline-none">Empty Trash</button>
        </p>
    </div>
</template>

<script>
export default {
    name: "EmptyTrashComponent",
    methods: {
        showConfirmation() {
            alert('show confirmation dialog');
        }
    }
}
</script>

<style scoped>

</style>
