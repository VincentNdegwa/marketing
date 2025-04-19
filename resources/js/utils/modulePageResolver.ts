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
 * 2. Add an entry in REGISTERED_MODULES with the appropriate import.meta.glob statement
 */

// ==============================
// Configuration and Setup
// ==============================

// Get active modules from modules_statuses.json
const ACTIVE_MODULES = Object.keys(moduleStatuses).filter(module => moduleStatuses[module as keyof typeof moduleStatuses]);
console.log('Active modules:', ACTIVE_MODULES);

// Register all possible modules (required for Vite static analysis)
// Add new modules here when they are created
const REGISTERED_MODULES: Record<string, Record<string, () => Promise<DefineComponent>>> = {
  'blog': import.meta.glob<DefineComponent>('../../../Modules/Blog/Resources/js/pages/**/*.vue'),
  // 'commerce': import.meta.glob<DefineComponent>('../../../Modules/Commerce/Resources/js/pages/**/*.vue'),
};

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
 * Processes pages for a specific module with multiple name formats for flexible resolution
 */
function processModulePages(moduleName: string, pages: Record<string, () => Promise<DefineComponent>>) {
  const moduleNameLower = moduleName.toLowerCase();
  const result: Record<string, () => Promise<DefineComponent>> = {};
  
  Object.entries(pages).forEach(([path, importFn]) => {
    // Extract filename without extension (e.g., 'Index' from 'path/to/Index.vue')
    const pathParts = path.split('/');
    const fileName = pathParts[pathParts.length - 1].replace('.vue', '');
    
    // Add multiple formats for flexible resolution
    result[fileName] = importFn;                                  // Format: "Index"
    result[`${moduleNameLower}/${fileName}`] = importFn;         // Format: "blog/Index"
    result[`${moduleName}/${fileName}`] = importFn;              // Format: "Blog/Index"
    result[fileName.toLowerCase()] = importFn;                   // Format: "index"
    result[`${moduleNameLower}/${fileName.toLowerCase()}`] = importFn; // Format: "blog/index"
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
const modulePageMap: Record<string, Record<string, () => Promise<DefineComponent>>> = {};

ACTIVE_MODULES.forEach(moduleName => {
  const moduleNameLower = moduleName.toLowerCase();
  
  // Check if this module is registered
  if (REGISTERED_MODULES[moduleNameLower]) {
    // Process and store the module's pages
    modulePageMap[moduleNameLower] = processModulePages(
      moduleName, 
      REGISTERED_MODULES[moduleNameLower]
    );
    console.log(`${moduleName} module pages:`, Object.keys(modulePageMap[moduleNameLower]));
  } else {
    console.warn(`Module ${moduleName} is active but not registered in modulePageResolver.`);
    modulePageMap[moduleNameLower] = {}; // Initialize empty to avoid errors
  }
});

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
  
  // Check if this is a module page (format: moduleName/pageName)
  if (name.includes('/')) {
    const [moduleName, pageName] = name.split('/', 2);
    const moduleNameLower = moduleName.toLowerCase();
    const pageNameLower = pageName.toLowerCase();
    
    // Check if we have this module
    if (modulePageMap[moduleNameLower]) {
      const modulePages = modulePageMap[moduleNameLower];
      
      // Try various formats
      if (modulePages[name]) return modulePages[name]();
      if (modulePages[pageName]) return modulePages[pageName]();
      if (modulePages[pageNameLower]) return modulePages[pageNameLower]();
      if (modulePages[nameLower]) return modulePages[nameLower]();
    }
  } else {
    // No slash - check all modules for this page name
    for (const [moduleNameLower, modulePages] of Object.entries(modulePageMap)) {
      if (modulePages[name]) {
        console.log(`Found in ${moduleNameLower} module: ${name}`);
        return modulePages[name]();
      }
      
      if (modulePages[nameLower]) {
        console.log(`Found in ${moduleNameLower} module (case-insensitive): ${nameLower}`);
        return modulePages[nameLower]();
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
  for (const moduleNameLower of Object.keys(REGISTERED_MODULES)) {
    if (!ACTIVE_MODULES.some(m => m.toLowerCase() === moduleNameLower)) continue;
    
    for (const [path, importFn] of Object.entries(REGISTERED_MODULES[moduleNameLower])) {
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
