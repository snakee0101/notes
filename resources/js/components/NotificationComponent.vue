<template>
    <div class="notification bg-gray-800 pl-6 pr-2 py-3 text-white fixed bottom-6 left-6 rounded flex items-center hidden">
        <p class="flex-grow notification-message">Notification</p>
        <p>
            <button @click="undo()"
                class="text-yellow-300 text-sm font-medium px-6 py-2 ml-4 hover:bg-gray-700 focus:outline-none focus:bg-gray-600 rounded-sm">
                Undo
            </button>
        </p>
        <div class="tooltip">
            <a href="" class="ml-1 p-2 rounded-full hover:bg-gray-700"
               @click.prevent="dismiss()">
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

//TODO: Example of sending notification:    window.events.$emit('show-notification', '12345', function(){alert('test');});

export default {
    name: "NotificationComponent",
    created() {
      window.events.$on('show-notification', function(message, undo_callback) {
            //if there is previous notification - hide it
            let notification = document.getElementsByClassName('notification')[0];
            notification.classList.add('hidden');

            //assign a callback
            window.undoNotificationCallback = undo_callback;

            //set message
            let message_tag = document.getElementsByClassName('notification-message')[0];
            message_tag.innerHTML = message;

            //showNotification
            notification.classList.remove('hidden');

            //hide notification after timeout
            setTimeout(function() {
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
