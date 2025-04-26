import type { DefineComponent } from 'vue';
import moduleStatuses from '../../../modules_statuses.json';

/**
 * Module Page Resolver
 * 
 * This utility provides a flexible page resolution mechanism for Laravel applications
 * with multiple modules. It allows for seamless navigation between the main application
 * and any module pages.
 * 
 * To add a new module:
 * 1. Add it to modules_statuses.json
 * 2. Add an import statement for the module's pages
 */

// Get active modules from modules_statuses.json
const ACTIVE_MODULES = Object.keys(moduleStatuses).filter(module => moduleStatuses[module as keyof typeof moduleStatuses]);
console.log('Active modules:', ACTIVE_MODULES);

// Import pages for each module
// Add new modules here when you create them
const USERMANAGEMENT_PAGES = import.meta.glob<DefineComponent>('../../../Modules/UserManagement/resources/js/pages/**/*.vue');
const BUSINESS_PAGES = import.meta.glob<DefineComponent>('../../../Modules/Business/resources/js/pages/**/*.vue');

// Create a mapping of module names to their page imports
const MODULE_PAGES_MAP: Record<string, Record<string, () => Promise<DefineComponent>>> = {
  'usermanagement': USERMANAGEMENT_PAGES,
  'business': BUSINESS_PAGES
};

// Log available pages for each module
// Object.entries(MODULE_PAGES_MAP).forEach(([moduleName, pages]) => {
//   console.log(`${moduleName} pages:`, Object.keys(pages));
// });

// Import main application pages
const MAIN_APP_PAGES = import.meta.glob<DefineComponent>('../pages/**/*.vue');

// ==============================
// Page Processing Functions
// ==============================

/**
 * Creates a normalized map of main application pages
 */
function processMainAppPages() {
  const result: Record<string, () => Promise<DefineComponent>> = {};
  
  Object.entries(MAIN_APP_PAGES).forEach(([path, importFn]) => {
    // Convert '../pages/Dashboard.vue' to 'Dashboard'
    const normalizedPath = path.replace('../pages/', '').replace('.vue', '');
    
    // Add both normal and lowercase versions for case-insensitive lookup
    result[normalizedPath] = importFn;
    result[normalizedPath.toLowerCase()] = importFn;
  });
  
  return result;
}

/**
 * Creates a normalized map of module pages with automatic subfolder discovery
 */
function processModulePages() {
  const result: Record<string, Record<string, () => Promise<DefineComponent>>> = {};
  
  // Process each active module
  Object.entries(MODULE_PAGES_MAP).forEach(([moduleNameLower, modulePages]) => {
    // Skip inactive modules
    if (!ACTIVE_MODULES.some(m => m.toLowerCase() === moduleNameLower)) return;
    
    result[moduleNameLower] = {};
    const allPaths: string[] = [];
    
    // Process each page in this module
    Object.entries(modulePages).forEach(([path, importFn]) => {
      // Extract page path from the full path
      const pathMatch = path.match(/\/pages\/(.+)\.vue$/);
      if (!pathMatch) return;
      
      const pagePath = pathMatch[1];
      allPaths.push(pagePath);
      const moduleName = moduleNameLower.charAt(0).toUpperCase() + moduleNameLower.slice(1);
      const pageNameOnly = pagePath.split('/').pop() || '';
      
      // Add various formats for flexible lookup
      result[moduleNameLower][pagePath] = importFn;                                    // folder/Page
      result[moduleNameLower][pagePath.toLowerCase()] = importFn;                      // folder/page
      result[moduleNameLower][`${moduleName}/${pagePath}`] = importFn;                // Blog/folder/Page
      result[moduleNameLower][`${moduleNameLower}/${pagePath.toLowerCase()}`] = importFn; // blog/folder/page
      result[moduleNameLower][pageNameOnly] = importFn;                               // Page
      result[moduleNameLower][pageNameOnly.toLowerCase()] = importFn;                 // page
    });
    
    // Process subfolders for direct access
    const subfolders = new Set(allPaths.filter(p => p.includes('/')).map(p => p.split('/')[0]));
    
    // Create direct mappings for each subfolder
    subfolders.forEach(subfolder => {
      const subfolderLower = subfolder.toLowerCase();
      allPaths.filter(path => path.startsWith(`${subfolder}/`)).forEach(fullPath => {
        const pageName = fullPath.substring(subfolder.length + 1);
        result[moduleNameLower][`${subfolder}/${pageName}`] = result[moduleNameLower][fullPath];
        result[moduleNameLower][`${subfolderLower}/${pageName.toLowerCase()}`] = result[moduleNameLower][fullPath];
      });
    });
  });
  
  return result;
}

// ==============================
// Initialize Page Maps
// ==============================

// Process main application pages
const mainPageMap = processMainAppPages();
console.log('Main app pages:', Object.keys(mainPageMap));

// Process module pages (only for active modules)
const modulePageMap = processModulePages();

// ==============================
// Page Resolution Function
// ==============================

/**
 * Resolves a page component across multiple modules
 * This allows for seamless navigation between modules
 */
export function resolveModulePage(name: string): Promise<DefineComponent> {
  console.log(`Resolving page: ${name}`);
  const nameLower = name.toLowerCase();
  
  // ===== Main Application Pages =====
  
  // Try direct match in main application
  if (mainPageMap[name]) {
    console.log(`Found in main app: ${name}`);
    return mainPageMap[name]();
  }
  
  // Try case-insensitive match in main application
  if (mainPageMap[nameLower]) {
    console.log(`Found in main app (case-insensitive): ${nameLower}`);
    return mainPageMap[nameLower]();
  }
  
  // ===== Module Pages =====
  
  // Check if this is a module page (format: moduleName/pageName or moduleName/category/pageName)
  if (name.includes('/')) {
    // Handle both simple paths (blog/Index) and nested paths (blog/category/Index)
    const parts = name.split('/');
    const moduleName = parts[0];
    const moduleNameLower = moduleName.toLowerCase();
    
    // For simple paths like 'blog/Index'
    const pageName = parts.length > 1 ? parts[1] : '';
    const pageNameLower = pageName.toLowerCase();
    
    // For nested paths like 'blog/category/Index'
    const fullPath = parts.slice(1).join('/');
    const fullPathLower = fullPath.toLowerCase();
    
    // Check if we have this module
    if (modulePageMap[moduleNameLower]) {
      const modulePages = modulePageMap[moduleNameLower];
      
      // Try various formats
      if (modulePages[name]) {
        console.log(`Found in ${moduleName} module (full name): ${name}`);
        return modulePages[name]();
      }
      
      if (modulePages[pageName]) {
        console.log(`Found in ${moduleName} module (page name): ${pageName}`);
        return modulePages[pageName]();
      }
      
      if (modulePages[pageNameLower]) {
        console.log(`Found in ${moduleName} module (page name, case-insensitive): ${pageNameLower}`);
        return modulePages[pageNameLower]();
      }
      
      if (modulePages[nameLower]) {
        console.log(`Found in ${moduleName} module (full name, case-insensitive): ${nameLower}`);
        return modulePages[nameLower]();
      }
      
      // Handle nested paths (e.g., blog/category/Index)
      if (parts.length > 2) {
        // Try to find the exact nested path
        if (modulePages[fullPath]) {
          console.log(`Found nested path in ${moduleName} module: ${fullPath}`);
          return modulePages[fullPath]();
        }
        
        // Try case-insensitive match for nested path
        if (modulePages[fullPathLower]) {
          console.log(`Found nested path in ${moduleName} module (case-insensitive): ${fullPathLower}`);
          return modulePages[fullPathLower]();
        }
        
        // The last part of the path might be the actual page name
        const lastPart = parts[parts.length - 1];
        const lastPartLower = lastPart.toLowerCase();
        
        // Try with just the last part
        if (modulePages[lastPart]) {
          console.log(`Found in ${moduleName} module (last part): ${lastPart}`);
          return modulePages[lastPart]();
        }
        
        // Try case-insensitive with just the last part
        if (modulePages[lastPartLower]) {
          console.log(`Found in ${moduleName} module (last part, case-insensitive): ${lastPartLower}`);
          return modulePages[lastPartLower]();
        }
      }
      
      // Special handling for various folder structures
      for (const key in modulePages) {
        // Check for exact path match (ignoring module name)
        if (key === fullPath) {
          console.log(`Found exact path match in ${moduleName} module: ${key}`);
          return modulePages[key]();
        }
        
        // Check if key contains the full path (for nested structures)
        if (key.includes(fullPath)) {
          console.log(`Found path in ${moduleName} module (contains): ${key}`);
          return modulePages[key]();
        }
        
        // Check if key ends with the full path
        if (key.endsWith(`/${fullPath}`)) {
          console.log(`Found path in ${moduleName} module (ends with): ${key}`);
          return modulePages[key]();
        }
        
        // Check if key contains folder structure (e.g., category/Index)
        if (key.includes('/') && parts.length > 2 && key.includes(parts[1])) {
          console.log(`Found in ${moduleName} module (folder match): ${key}`);
          return modulePages[key]();
        }
        
        // Check if key contains the page name with different prefixes
        if (key.endsWith(`/${pageName}`)) {
          console.log(`Found in ${moduleName} module (different prefix): ${key}`);
          return modulePages[key]();
        }
      }
    }
  } else if (name.includes('/')) {
    // This could be a path without a module prefix (e.g., "category/Index")
    // Try to find it in any module
    
    // First, check if the first part might actually be a folder, not a module
    for (const [moduleNameLower, modulePages] of Object.entries(modulePageMap)) {
      // Try the full path as is
      if (modulePages[name]) {
        console.log(`Found path in ${moduleNameLower} module: ${name}`);
        return modulePages[name]();
      }
      
      // Try case-insensitive
      if (modulePages[nameLower]) {
        console.log(`Found path in ${moduleNameLower} module (case-insensitive): ${nameLower}`);
        return modulePages[nameLower]();
      }
      
      // Check if any key contains this path
      for (const key in modulePages) {
        if (key.includes(name)) {
          console.log(`Found in ${moduleNameLower} module (contains path): ${key}`);
          return modulePages[key]();
        }
        
        if (key.toLowerCase().includes(nameLower)) {
          console.log(`Found in ${moduleNameLower} module (contains path, case-insensitive): ${key}`);
          return modulePages[key]();
        }
        
        // Check if the key ends with this path
        if (key.endsWith(`/${name}`)) {
          console.log(`Found in ${moduleNameLower} module (ends with path): ${key}`);
          return modulePages[key]();
        }
      }
    }
  } else {
    // Simple page name - check all modules
    for (const [moduleNameLower, modulePages] of Object.entries(modulePageMap)) {
      if (modulePages[name]) {
        console.log(`Found in ${moduleNameLower} module: ${name}`);
        return modulePages[name]();
      }
      
      // Case-insensitive match
      if (modulePages[nameLower]) {
        console.log(`Found in ${moduleNameLower} module (case-insensitive): ${nameLower}`);
        return modulePages[nameLower]();
      }
      
      // Look for partial matches (for nested folders)
      for (const key in modulePages) {
        if (key.includes(name) || key.toLowerCase().includes(nameLower)) {
          console.log(`Found in ${moduleNameLower} module (partial match): ${key}`);
          return modulePages[key]();
        }
      }
    }
  }
  
  // ===== Fallback Strategies =====
  
  // Try path matching in main app
  for (const [path, importFn] of Object.entries(MAIN_APP_PAGES)) {
    if (path.includes(name) || path.toLowerCase().includes(nameLower)) {
      console.log(`Found in main app (path match): ${path}`);
      return importFn();
    }
  }
  
  // Try path matching in module pages
  for (const moduleNameLower of Object.keys(modulePageMap)) {
    if (!ACTIVE_MODULES.some(m => m.toLowerCase() === moduleNameLower)) continue;
    
    for (const [path, importFn] of Object.entries(modulePageMap[moduleNameLower])) {
      if (path.includes(name) || path.toLowerCase().includes(nameLower)) {
        console.log(`Found in ${moduleNameLower} module (path match): ${path}`);
        return importFn();
      }
    }
  }
  
  // Page not found
  console.error(`Page not found: ${name}`);
  throw new Error(`Page not found: ${name}`);
}
