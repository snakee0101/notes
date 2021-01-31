<template>
    <div class="notification bg-gray-800 pl-6 pr-2 py-1.5 text-white fixed bottom-6 left-6 rounded flex items-center"
    :class="isHidden ? 'hidden' : ''">
        <p class="flex-grow notification-message m-0">{{ message }}</p>
        <p class="undo-button m-0" :class="isUndoHidden ? 'hidden' : ''">
            <button @click="undo()" class="notification_undo_button">
                Undo
            </button>
        </p>
        <a href="" class="ml-1 p-2 rounded-full hover:bg-gray-700"
           v-b-tooltip.hover.bottom title="Dismiss"
           @click.prevent="hide()">
                <i class="bi bi-x icon-lg text-white"></i>
        </a>
    </div>
</template>

<script>

//TODO: Example of sending notification:    window.events.$emit('show-notification', '12345', function(){alert('test');});

export default {
    name: "NotificationComponent",
    data() {
        return {
            'message' : '',
            'canBeUndone' : true,
            'isHidden' : true,
            'isUndoHidden' : true,
            'undo' : function(){}
        };
    },
    created() {
        window.events.$on('show-notification', this.show);
    },
    methods: {
        show(message, undo_callback)
        {
            clearTimeout(window.notificationTimeout);  //if there is previous notification - hide it
            this.isHidden = true;

            this.undo = undo_callback;  //assign a callback
            this.isUndoHidden = !undo_callback;

            this.message = message;
            this.isHidden = false;

            window.notificationTimeout = setTimeout(this.hide, 5000);
        },
        hide() {
            this.isHidden = true;
        }
    }
}
</script>

<style scoped>

</style>
