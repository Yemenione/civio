const fs = require('fs');
const path = require('path');
const axios = require('axios');

// Manually parse .env to get GROQ_API_KEY
const envPath = path.join(__dirname, '.env');
const envContent = fs.readFileSync(envPath, 'utf8');
const groqMatch = envContent.match(/^GROQ_API_KEY=(.*)$/m);
const GROQ_API_KEY = groqMatch ? groqMatch[1].trim() : null;

if (!GROQ_API_KEY) {
    console.error("GROQ_API_KEY not found in .env");
    process.exit(1);
}

const localesDir = path.join(__dirname, 'resources', 'js', 'locales');
const enPath = path.join(localesDir, 'en.json');

const langMap = {
    'ar': 'Arabic',
    'de': 'German',
    'fr': 'French',
    'it': 'Italian',
    'nl': 'Dutch',
    'pt': 'Portuguese',
    'es': 'Spanish'
};

const delay = ms => new Promise(res => setTimeout(res, ms));

async function translateText(textObj, targetLanguage) {
    if (Object.keys(textObj).length === 0) return {};

    console.log(`Translating ${Object.keys(textObj).length} keys to ${targetLanguage}...`);

    const prompt = `Translate the following JSON object values to ${targetLanguage}. 
Return ONLY the raw JSON object with the exact same keys, but translated values.
Do not include any markdown formatting like \`\`\`json. Just the raw JSON starting with { and ending with }.
Ensure the translations are professional and contextually fit for a Resume Builder application.
Keep placeholders like {lang} or {context} exactly as they are.

JSON to translate:
${JSON.stringify(textObj, null, 2)}`;

    try {
        const response = await axios.post('https://api.groq.com/openai/v1/chat/completions', {
            model: 'llama-3.3-70b-versatile',
            messages: [{ role: 'user', content: prompt }],
            temperature: 0.1
        }, {
            headers: {
                'Authorization': `Bearer ${GROQ_API_KEY}`,
                'Content-Type': 'application/json'
            }
        });

        const reply = response.data.choices[0].message.content.trim();
        // Clean up markdown if any
        let cleanJson = reply;
        if (cleanJson.startsWith('```')) {
            cleanJson = cleanJson.replace(/^```json/, '').replace(/```$/, '').trim();
        }
        return JSON.parse(cleanJson);
    } catch (e) {
        console.error(`Error translating to ${targetLanguage}:`, e.message);
        return null;
    }
}

async function syncLocales() {
    if (!fs.existsSync(enPath)) {
        console.error('en.json not found!');
        return;
    }

    const enData = JSON.parse(fs.readFileSync(enPath, 'utf8').replace(/^\uFEFF/, ''));
    const enKeys = Object.keys(enData);

    const files = fs.readdirSync(localesDir).filter(f => f.endsWith('.json') && f !== 'en.json');

    for (const file of files) {
        const filePath = path.join(localesDir, file);
        const localeData = JSON.parse(fs.readFileSync(filePath, 'utf8').replace(/^\uFEFF/, ''));
        const localeCode = file.replace('.json', '');
        const targetLang = langMap[localeCode];

        if (!targetLang) continue;

        const missingLocales = {};
        for (const key of enKeys) {
            if (localeData[key] === undefined) {
                missingLocales[key] = enData[key];
            }
        }

        if (Object.keys(missingLocales).length > 0) {
            console.log(`\nFound ${Object.keys(missingLocales).length} missing keys in ${file}`);

            // Chunking logic to avoid max token truncation
            const missingEntries = Object.entries(missingLocales);
            const chunkSize = 50;
            const translatedObj = {};

            for (let i = 0; i < missingEntries.length; i += chunkSize) {
                const chunk = Object.fromEntries(missingEntries.slice(i, i + chunkSize));
                console.log(`Translating batch ${Math.floor(i / chunkSize) + 1} of ${Math.ceil(missingEntries.length / chunkSize)}...`);

                let retryCount = 0;
                let chunkResult = null;

                while (retryCount < 3 && !chunkResult) {
                    chunkResult = await translateText(chunk, targetLang);
                    if (!chunkResult) {
                        console.log("Rate limit hit or error. Retrying chunk after 10s...");
                        await delay(10000);
                        retryCount++;
                    }
                }

                if (chunkResult) {
                    Object.assign(translatedObj, chunkResult);
                    console.log("Sleeping to avoid rate limits...");
                    await delay(5000); // 5 seconds between chunks
                } else {
                    console.error("Failed to translate chunk after retries. Aborting for this language.");
                    break;
                }
            }


            if (Object.keys(translatedObj).length > 0) {
                // Merge
                for (const key in translatedObj) {
                    localeData[key] = translatedObj[key];
                }

                // Reorder keys to match en.json
                const orderedData = {};
                for (const key of enKeys) {
                    if (localeData[key] !== undefined) {
                        orderedData[key] = localeData[key];
                    }
                }

                fs.writeFileSync(filePath, JSON.stringify(orderedData, null, 4), 'utf8');
                console.log(`Updated ${file} successfully.`);
            }
        } else {
            console.log(`\n${file} is already up to date.`);
        }
    }

    console.log('\nAll locales synced successfully!');
}

syncLocales();
