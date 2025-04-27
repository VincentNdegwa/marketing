import { DirectiveBinding } from 'vue';
import { usePage } from '@inertiajs/vue3';

interface permission{
  list: string[],
  is_business_admin: boolean,
  is_superadmin:boolean
}

function getPermissions() {
  const page = usePage();
  const perm = page.props.permissions as permission
  return perm.list;
}

function isSuperAdmin() {
  const page = usePage();
  const perm = page.props.permissions as permission
  return perm.is_superadmin;
}

export const vPermission = {
  mounted(el: HTMLElement, binding: DirectiveBinding) {
    const permissions = getPermissions();
    const value = binding.value;
    console.log("Checking permission: "+value);
    
    const superadmin = isSuperAdmin();
    if (superadmin) return;
    
    if (!permissions.includes(value)) {
      el.parentNode?.removeChild(el);
    }
  }
};

export const vCan = {
  mounted(el: HTMLElement, binding: DirectiveBinding) {
    const permissions = getPermissions();
    const superadmin = isSuperAdmin();
    
    if (superadmin) return;
    
    const { value, modifiers } = binding;
    
    // Handle array of permissions
    if (Array.isArray(value)) {
      if (modifiers.and) {
        const hasAllPermissions = value.every(permission => permissions.includes(permission));
        if (!hasAllPermissions) {
          el.parentNode?.removeChild(el);
        }
      } 
      else {
        const hasAnyPermission = value.some(permission => permissions.includes(permission));
        if (!hasAnyPermission) {
          el.parentNode?.removeChild(el);
        }
      }
    } 
    else if (typeof value === 'string') {
      if (!permissions.includes(value)) {
        el.parentNode?.removeChild(el);
      }
    }
  }
};
