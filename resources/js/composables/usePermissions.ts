import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

export function usePermissions() {
  const page = usePage();
  
  // Get all permissions as an array
  const permissions = computed(() => {
    return page.props.permissions?.list || [];
  });
  
  // Check if user is business admin for current business
  const isBusinessAdmin = computed(() => {
    return page.props.permissions?.is_business_admin || false;
  });
  
  // Check if user is superadmin
  const isSuperAdmin = computed(() => {
    return page.props.auth?.is_superadmin || false;
  });
  
  // Check if user has a specific permission
  const hasPermission = (permission: string): boolean => {
    return permissions.value.includes(permission);
  };
  
  // Check if user has any of the given permissions
  const hasAnyPermission = (permissionList: string[]): boolean => {
    return permissionList.some(permission => hasPermission(permission));
  };
  
  // Check if user has all of the given permissions
  const hasAllPermissions = (permissionList: string[]): boolean => {
    return permissionList.every(permission => hasPermission(permission));
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
