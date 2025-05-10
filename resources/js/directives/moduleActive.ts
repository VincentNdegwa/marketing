import { DirectiveBinding } from 'vue';
import axios from 'axios';

interface ModuleStatuses {
    [key: string]: boolean;
}

export const vModuleActive = {
    mounted(el: HTMLElement, binding: DirectiveBinding) {
        try {
            const moduleName = binding.value;
            if (!moduleName) {
                console.error('Module name is required in v-module-active directive');
                return;
            }

            axios.get('/modules_statuses.json')
                .then(response => {
                    const moduleStatuses = response.data as ModuleStatuses;
                    if (!moduleStatuses[moduleName]) {
                        el.remove();
                    }
                })
                .catch(error => {
                    console.error('Error fetching module statuses:', error);
                    // If we can't fetch the statuses, keep the element
                });
        } catch (error) {
            console.error('Error in v-module-active directive:', error);
            // If there's an error, keep the element
        }
    }
}
