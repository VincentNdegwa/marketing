import fs from 'fs/promises';
import path from 'path';

async function getModulePaths(paths = [], modulesPath = 'Modules') {
  modulesPath = path.join(__dirname, modulesPath);

  const moduleStatusesPath = path.join(__dirname, 'modules_statuses.json');
  const result = [];

  try {
    // Read module_statuses.json
    const moduleStatusesContent = await fs.readFile(moduleStatusesPath, 'utf-8');
    const moduleStatuses = JSON.parse(moduleStatusesContent);

    // Read module directories
    const moduleDirectories = await fs.readdir(modulesPath);

    for (const moduleDir of moduleDirectories) {
      if (moduleDir === '.DS_Store') {
        continue;
      }

      // Check if the module is enabled (status is true)
      if (moduleStatuses[moduleDir] === true) {
        // Add the module's app.ts file to the result
        result.push(`Modules/${moduleDir}/Resources/js/app.ts`);
      }
    }
  } catch (error) {
    console.error(`Error reading module statuses or module configurations: ${error}`);
  }

  return result;
}

export default getModulePaths;
