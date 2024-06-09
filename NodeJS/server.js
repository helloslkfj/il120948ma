const express = require('express');
const puppet = require('puppeteer');

const server = express();
const port = 3000;

server.use(express.json());

async function webScrape(url) {
    const browser = await puppet.launch({
        ignoreHTTPSErrors: true,
        headless: true
    });
    const page = await browser.newPage();
    await page.goto(url);
    const content = await page.content();
    await browser.close();

    console.log('done');
    return content;
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