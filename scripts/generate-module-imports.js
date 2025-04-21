import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';

// Get the current file's directory
const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

const modulesStatusesPath = path.resolve(__dirname, '../modules_statuses.json');
const moduleStatuses = JSON.parse(fs.readFileSync(modulesStatusesPath, 'utf8'));

const activeModules = Object.keys(moduleStatuses).filter(module => moduleStatuses[module]);

let importStatements = '// Import pages for each module\n// Add new modules here when you create them\n';
let moduleMapEntries = [];

activeModules.forEach(moduleName => {
  const constName = `${moduleName.toUpperCase().replace(/-/g, '_')}_PAGES`;
  importStatements += `const ${constName} = import.meta.glob<DefineComponent>('../../../Modules/${moduleName}/Resources/js/pages/**/*.vue');\n`;
  moduleMapEntries.push(`  '${moduleName.toLowerCase()}': ${constName}`);
});

// Generate the module map
const moduleMap = `// Create a mapping of module names to their page imports
const MODULE_PAGES_MAP: Record<string, Record<string, () => Promise<DefineComponent>>> = {
${moduleMapEntries.join(',\n')}
};`;

// Read the current modulePageResolver.ts file
const resolverPath = path.resolve(__dirname, '../resources/js/utils/modulePageResolver.ts');
let resolverContent = fs.readFileSync(resolverPath, 'utf8');

// Replace the imports and module map sections
const importsRegex = /\/\/ Import pages for each module[\s\S]*?\/\/ Create a mapping of module names to their page imports[\s\S]*?};/;
const replacement = `${importStatements}\n${moduleMap}`;

resolverContent = resolverContent.replace(importsRegex, replacement);

// Write the updated content back to the file
fs.writeFileSync(resolverPath, resolverContent);

console.log('Module imports updated successfully!');
console.log(`Added imports for ${activeModules.length} active modules: ${activeModules.join(', ')}`);
