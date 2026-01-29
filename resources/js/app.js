import './bootstrap';

import Alpine from 'alpinejs';
import { icons } from 'lucide';

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
                if (iconContainer && icons[this.iconName]) {
                    iconContainer.innerHTML = icons[this.iconName].toSvg({ 
                        size: 20, 
                        color: 'black',
                        class: 'transition-colors duration-200'
                    });
                }
            }
        });
    }
}));

Alpine.start();
