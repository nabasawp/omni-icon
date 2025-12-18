// import './iframe.css';

// Workaround to load CSS in Gutenberg Blocks' iframe using import.meta.url
const iframeCss = document.createElement('link');
iframeCss.rel = 'stylesheet';
(async () => {
    iframeCss.href = new URL('./iframe.css', import.meta.url).href;
    document.head.appendChild(iframeCss);
})();