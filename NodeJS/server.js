const express = require('express');
const puppet = require('puppeteer');

const server = express();
const port = 3001;

server.use(express.json());

async function webScrape(url) {
    const ua = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36';
    const browser = await puppet.launch({
        ignoreHTTPSErrors: true,
        headless: true  
    });
    const page = await browser.newPage();
    page.setUserAgent(ua);

    try {
        await page.goto(url, {waitUntil: 'networkidle2'});
        await page.waitForSelector('body');
        const content = await evaluateWithRetry(page);
        console.log('Done scraping');
        return content;
    }
    catch (error){
        console.log('Error scraping the page:', error);
    } finally {
        await browser.close();
    }
}

async function evaluateWithRetry(page, retries=2) {
    for (let i = 0; i < retries; i++) {
        try {
            return await page.content();
        } catch (error) {
            if (i === retries - 1) throw error;
            await page.waitForTimeout(1000); 
        }
    }
}

async function getHTMLContent(link, res) {
    const content = await webScrape(link);
    res.status(200).send({html:content, status:"scraping completed"});
}

server.post('/webscrape', (req, res) => {
    const {link} = req.body;

    if(!link) {
        return server.status(400).send({status:"scraping failed"});
    }

    getHTMLContent(link, res);

})

server.listen(port, () => {
    console.log(`Server is successfully running on port: ${port}`);
});