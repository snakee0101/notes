<template>
    <div class="notification bg-gray-800 pl-6 pr-2 py-3 text-white fixed bottom-6 left-6 rounded flex items-center">
        <p class="flex-grow">Notification</p>
        <p>
            <button
                class="text-yellow-300 text-sm font-medium px-6 py-2 ml-4 hover:bg-gray-700 focus:outline-none focus:bg-gray-600 rounded-sm">
                Undo
            </button>
        </p>
        <div class="tooltip">
            <a href="" class="ml-1 p-2 rounded-full hover:bg-gray-700"
               @click.prevent>
                <svg class="icon icon-small icon-close" viewBox="0 0 20 20" style="fill: #fff">
                    <path
                        d="M10 8.586l-7.071-7.071-1.414 1.414 7.071 7.071-7.071 7.071 1.414 1.414 7.071-7.071 7.071 7.071 1.414-1.414-7.071-7.071 7.071-7.071-1.414-1.414-7.071 7.071z"></path>
                </svg>
            </a>
            <span class="tooltiptext">Dismiss</span>
        </div>
    </div>
</template>

<script>
export default {
    name: "NotificationComponent"
}
</script>

<style scoped>

</style>
