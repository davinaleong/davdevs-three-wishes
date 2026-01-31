import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

// Password toggle component
Alpine.data('passwordToggle', () => ({
    type: 'password',
    show: false,
    toggle() {
        this.show = !this.show;
        this.type = this.show ? 'text' : 'password';
        this.renderIcon();
    },
    get iconName() {
        return this.show ? 'eye-off' : 'eye';
    },
    get title() {
        return this.show ? 'Hide password' : 'Show password';
    },
    init() {
        this.renderIcon();
    },
    renderIcon() {
        this.$nextTick(() => {
            const button = this.$el.querySelector('.password-toggle-btn');
            if (button) {
                const iconContainer = button.querySelector('.lucide-icon');
                if (iconContainer) {
                    // Simple inline SVG icons instead of lucide dependency
                    const eyeIcon = '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="transition-colors duration-200"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>';
                    const eyeOffIcon = '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="transition-colors duration-200"><path d="m9.88 9.88 4.24 4.24"/><path d="m10.73 5.08-1.24 1.24A11.46 11.46 0 0 1 12 6c7 0 11 6 11 6a11.77 11.77 0 0 1-1.17 1.42l.95.95a1 1 0 1 1-1.41 1.41L3.71 2.22a1 1 0 0 1 1.41-1.41l5.66 5.66Z"/></svg>';
                    iconContainer.innerHTML = this.show ? eyeOffIcon : eyeIcon;
                }
            }
        });
    }
}));

Alpine.start();

Alpine.start();
