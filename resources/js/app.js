import './bootstrap';
import Alpine from 'alpinejs';

window.Alpine = Alpine;

// Toast Notification Store
Alpine.store('toasts', {
    items: [],
    add(message, type = 'success', duration = 4000) {
        const id = Date.now() + Math.random();
        this.items.push({ id, message, type });
        setTimeout(() => {
            this.remove(id);
        }, duration);
    },
    remove(id) {
        this.items = this.items.filter(item => item.id !== id);
    }
});

// Modal & Drawer global events
window.showToast = (message, type = 'success') => {
    Alpine.store('toasts').add(message, type);
};

Alpine.start();
