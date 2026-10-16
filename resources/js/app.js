import './bootstrap';

// Register Alpine stores and hooks when Alpine initializes
document.addEventListener('alpine:init', () => {
    window.Alpine.store('toasts', {
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
});

// Toast notification helper
window.showToast = (message, type = 'success') => {
    if (window.Alpine && window.Alpine.store('toasts')) {
        window.Alpine.store('toasts').add(message, type);
    }
};

// CSRF Token & Session Keep-Alive Manager
const refreshCsrfToken = async () => {
    try {
        const response = await fetch('/csrf-token', {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        });
        if (response.ok) {
            const data = await response.json();
            if (data && data.token) {
                const metaTag = document.querySelector('meta[name="csrf-token"]');
                if (metaTag) {
                    metaTag.setAttribute('content', data.token);
                }
                document.querySelectorAll('input[name="_token"]').forEach(input => {
                    input.value = data.token;
                });
                if (window.axios) {
                    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = data.token;
                }
                return data.token;
            }
        }
    } catch (e) {
        // Silently catch background errors
    }
    return null;
};

// 1. Keep-alive heartbeat every 10 minutes
setInterval(refreshCsrfToken, 10 * 60 * 1000);

// 2. Refresh on tab focus / wake up from sleep
document.addEventListener('visibilitychange', () => {
    if (document.visibilityState === 'visible') {
        refreshCsrfToken();
    }
});

// 3. Livewire 3 419 Session-Expired Interception
document.addEventListener('livewire:init', () => {
    try {
        if (window.Livewire && typeof window.Livewire.hook === 'function') {
            window.Livewire.hook('request', ({ fail }) => {
                if (typeof fail === 'function') {
                    fail(({ status, preventDefault }) => {
                        if (status === 419) {
                            if (typeof preventDefault === 'function') preventDefault();
                            refreshCsrfToken().finally(() => {
                                window.location.reload();
                            });
                        }
                    });
                }
            });
        }
    } catch (e) {
        // Silently continue if hook is unsupported
    }
});


