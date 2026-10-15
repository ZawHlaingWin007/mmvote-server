const fs = require('fs');
const path = require('path');

const root = path.join(__dirname, '..');
const cacheDir =
    process.env.PUPPETEER_CACHE_DIR || path.join(root, '.puppeteer-cache');

process.chdir(root);
process.env.PUPPETEER_CACHE_DIR = cacheDir;

const puppeteer = require(path.join(root, 'node_modules', 'puppeteer'));

async function main() {
    const configPath = process.argv[2];
    if (!configPath) {
        console.error('Usage: node render-html-to-pdf.cjs <config.json>');
        process.exit(1);
    }

    const config = JSON.parse(fs.readFileSync(configPath, 'utf8'));
    const html = fs.readFileSync(config.htmlPath, 'utf8');
    const timeout = config.timeoutMs || 120000;

    const launchOptions = {
        headless: true,
    };

    if (config.executablePath) {
        launchOptions.executablePath = config.executablePath;
    }

    if (config.noSandbox) {
        launchOptions.args = ['--no-sandbox', '--disable-setuid-sandbox'];
    }

    const browser = await puppeteer.launch(launchOptions);
    try {
        const page = await browser.newPage();
        await page.setContent(html, {
            waitUntil: 'networkidle0',
            timeout,
        });

        const margin = config.margin || {
            top: '12mm',
            right: '10mm',
            bottom: '14mm',
            left: '10mm',
        };

        await page.pdf({
            path: config.outputPath,
            format: config.format || 'A4',
            printBackground: config.printBackground !== false,
            margin,
        });
    } finally {
        await browser.close();
    }
}

main().catch(function (err) {
    console.error(err && err.message ? err.message : err);
    process.exit(1);
});
