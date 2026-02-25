import puppeteer from 'puppeteer';
import { resolve } from 'path';

/**
 * PDF Generation Script
 * Usage: node scripts/pdf.js <url> <output_path>
 */

const [url, outputPath] = process.argv.slice(2);

if (!url || !outputPath) {
    console.error('Usage: node scripts/pdf.js <url> <output_path>');
    process.exit(1);
}

(async () => {
    let browser;
    try {
        browser = await puppeteer.launch({
            headless: 'new',
            args: ['--no-sandbox', '--disable-setuid-sandbox', '--disable-web-security']
        });

        const page = await browser.newPage();

        // Emulate A4 screen size to help with responsive rendering
        await page.setViewport({
            width: 1200,
            height: 1600,
            deviceScaleFactor: 2, // High DPI
        });

        console.log(`Navigating to: ${url}`);

        // Wait until network is idle
        await page.goto(url, {
            waitUntil: 'networkidle0',
            timeout: 60000
        });

        // Small extra delay for any Vue animations or dynamic font loading
        await new Promise(r => setTimeout(r, 2000));

        console.log(`Generating PDF to: ${outputPath}`);

        await page.pdf({
            path: resolve(outputPath),
            format: 'A4',
            printBackground: true,
            margin: {
                top: '0mm',
                right: '0mm',
                bottom: '0mm',
                left: '0mm'
            },
            displayHeaderFooter: false,
            preferCSSPageSize: true
        });

        console.log('PDF Generated successfully.');
    } catch (error) {
        console.error('Error generating PDF:', error);
        process.exit(1);
    } finally {
        if (browser) {
            await browser.close();
        }
    }
})();
