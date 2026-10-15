const fs = require('fs');
const path = require('path');

const root = path.join(__dirname, '..');
const cacheDir =
    process.env.PUPPETEER_CACHE_DIR || path.join(root, '.puppeteer-cache');

process.chdir(root);
process.env.PUPPETEER_CACHE_DIR = cacheDir;

const puppeteerRoot = path.join(root, 'node_modules', 'puppeteer');

if (!fs.existsSync(puppeteerRoot)) {
    console.error('puppeteer is not installed. Run npm install in the project root.');
    process.exit(1);
}

const puppeteer = require(puppeteerRoot);

puppeteer
    .executablePath()
    .then(function (chromePath) {
        console.log(chromePath);
    })
    .catch(function (err) {
        console.error(
            err && err.message
                ? err.message
                : 'Could not resolve Puppeteer Chrome. Run: npm run puppeteer:install-chrome'
        );
        process.exit(1);
    });
