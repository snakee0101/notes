<template>
    <div>
        <a href="" class="p-4 rounded-r-full hover:bg-gray-200 block" @click.prevent="show()">
            <svg class="icon icon-pencil2 mr-3" viewBox="0 0 32 32">
                <path d="M12 20l4-2 14-14-2-2-14 14-2 4zM9.041 27.097c-0.989-2.085-2.052-3.149-4.137-4.137l3.097-8.525 4-2.435 12-12h-6l-12 12-6 20 20-6 12-12v-6l-12 12-2.435 4z"></path>
            </svg> Edit labels
        </a>
    </div>
</template>

<script>
export default {
    name: "EditLabelsComponent",
    methods: {
        show() {
            alert('show dialog');
        }
    }
}
</script>

<style scoped>

</style>
