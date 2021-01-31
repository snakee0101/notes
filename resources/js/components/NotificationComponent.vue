<template>
    <div class="notification bg-gray-800 pl-6 pr-2 py-1.5 text-white fixed bottom-6 left-6 rounded flex items-center hidden">
        <p class="flex-grow notification-message m-0">Notification</p>
        <p class="undo-button m-0">
            <button @click="undo()" class="notification_undo_button">
                Undo
            </button>
        </p>
        <a href="" class="ml-1 p-2 rounded-full hover:bg-gray-700"
           v-b-tooltip.hover.bottom title="Dismiss"
           @click.prevent="dismiss()">
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
            'canBeUndone': true
        };
    },
    created() {
        window.events.$on('show-notification', function (message, undo_callback) {
            //if there is previous notification - hide it
            clearTimeout(window.notificationTimeout);

            let notification = document.getElementsByClassName('notification')[0];
            notification.classList.add('hidden');

            //assign a callback
            let undo_button = document.getElementsByClassName('undo-button')[0];

            window.undoNotificationCallback = undo_callback;
            if (undo_callback)
                undo_button.classList.remove('hidden');
            else
                undo_button.classList.add('hidden');


            //set message
            let message_tag = document.getElementsByClassName('notification-message')[0];
            message_tag.innerHTML = message;

            //showNotification
            notification.classList.remove('hidden');

            //hide notification after timeout
            window.notificationTimeout = setTimeout(function () {
                let notification = document.getElementsByClassName('notification')[0];
                notification.classList.add('hidden');
            }, 5000);
        });
    },
    methods: {
        undo() {
            window.undoNotificationCallback();
        },
        dismiss() {
            let notification = document.getElementsByClassName('notification')[0];
            notification.classList.add('hidden');
        }
    }
}
</script>

<style scoped>

</style>
