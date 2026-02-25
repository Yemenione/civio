import { reactive, computed } from 'vue';

const state = reactive({
    toasts: [],
    activeAlert: null,
    activeConfirm: null,
    history: JSON.parse(localStorage.getItem('notification_history') || '[]'),
});

let nextToastId = 0;

export const useNotify = () => {
    const toast = (message, type = 'success', duration = 4000) => {
        const id = ++nextToastId;
        const newToast = { id, message, type, timestamp: new Date() };
        state.toasts.push(newToast);

        // Add to history
        state.history.unshift(newToast);
        if (state.history.length > 50) state.history.pop();
        localStorage.setItem('notification_history', JSON.stringify(state.history));

        if (duration > 0) {
            setTimeout(() => dismissToast(id), duration);
        }
    };

    const alert = (title, message, type = 'error') => {
        state.activeAlert = { title, message, type };
    };

    const dismissToast = (id) => {
        state.toasts = state.toasts.filter(t => t.id !== id);
    };

    const confirm = (title, message, onConfirm, onCancel = () => { }) => {
        state.activeConfirm = { title, message, onConfirm, onCancel };
    };

    const closeAlert = () => {
        state.activeAlert = null;
    };

    const resolveConfirm = (confirmed) => {
        if (!state.activeConfirm) return;
        if (confirmed) state.activeConfirm.onConfirm();
        else state.activeConfirm.onCancel();
        state.activeConfirm = null;
    };

    const clearHistory = () => {
        state.history = [];
        localStorage.removeItem('notification_history');
    };

    return {
        toasts: computed(() => state.toasts),
        activeAlert: computed(() => state.activeAlert),
        activeConfirm: computed(() => state.activeConfirm),
        history: computed(() => state.history),
        toast,
        alert,
        confirm,
        dismissToast,
        closeAlert,
        resolveConfirm,
        clearHistory,
        success: (msg) => toast(msg, 'success'),
        error: (msg) => toast(msg, 'error'),
        info: (msg) => toast(msg, 'info'),
        warning: (msg) => toast(msg, 'warning'),
    };
};
