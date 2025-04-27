import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

interface Permission {
  list: string[];
  is_business_admin: boolean;
  is_superadmin: boolean;
}

export function usePermissions() {
  const page = usePage();
  
  // Get all permissions as an array
  const permissions = computed(() => {
    const perm = page.props.permissions as Permission;
    return perm?.list || [];
  });
  
  // Check if user is business admin for current business
  const isBusinessAdmin = computed(() => {
    const perm = page.props.permissions as Permission;
    return perm?.is_business_admin || false;
  });
  
  // Check if user is superadmin
  const isSuperAdmin = computed(() => {
    const perm = page.props.permissions as Permission;
    return perm?.is_superadmin || false;
  });
  
  // Check if user has a specific permission
  const hasPermission = (permission: string): boolean => {
    // Superadmins always have all permissions
    if (isSuperAdmin.value) return true;
    return permissions.value.includes(permission);
  };
  
  // Check if user has any of the given permissions
  const hasAnyPermission = (permissionList: string[]): boolean => {
    // Superadmins always have all permissions
    if (isSuperAdmin.value) return true;
    return permissionList.some(permission => permissions.value.includes(permission));
  };
  
  // Check if user has all of the given permissions
  const hasAllPermissions = (permissionList: string[]): boolean => {
    // Superadmins always have all permissions
    if (isSuperAdmin.value) return true;
    return permissionList.every(permission => permissions.value.includes(permission));
  };
  
  return {
    permissions,
    isBusinessAdmin,
    isSuperAdmin,
    hasPermission,
    hasAnyPermission,
    hasAllPermissions
  };
}
