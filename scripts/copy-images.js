import { promises as fs } from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

const sourceDir = path.join(__dirname, '..', 'resources', 'img');
const targetDir = path.join(__dirname, '..', 'public', 'img');

async function copyImages() {
    try {
        // Ensure target directory exists
        await fs.mkdir(targetDir, { recursive: true });

        // Read all files from source directory
        const files = await fs.readdir(sourceDir);

        console.log(`Found ${files.length} files in resources/img`);

        // Copy each file to target directory
        for (const file of files) {
            const sourcePath = path.join(sourceDir, file);
            const targetPath = path.join(targetDir, file);

            // Get file stats to check if it's a directory
            const stats = await fs.stat(sourcePath);

            if (stats.isFile()) {
                await fs.copyFile(sourcePath, targetPath);
                console.log(`Copied: ${file}`);
            }
        }

        console.log('All images copied successfully from resources/img to public/img');
    } catch (error) {
        console.error('Error copying images:', error);
        process.exit(1);
    }
}

copyImages();
