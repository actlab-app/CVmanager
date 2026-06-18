import { createIcons, icons } from 'lucide';

window.lucide = {
    createIcons: (options) => {
        return createIcons({
            icons,
            ...options
        });
    }
};
