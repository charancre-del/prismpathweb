const fs = require('fs');
const path = require('path');

const cssPath = path.join(__dirname, '..', 'assets', 'css', 'main.css');
const minPath = path.join(__dirname, '..', 'assets', 'css', 'main.min.css');
const css = fs.readFileSync(cssPath, 'utf8');

if (!css.includes('.home-hero') || !css.includes('.consult-section')) {
  throw new Error('Compiled CSS is missing required Prismpath Health sections.');
}

const minified = css
  .replace(/\/\*[\s\S]*?\*\//g, '')
  .replace(/\s+/g, ' ')
  .replace(/\s*([{}:;,>+~])\s*/g, '$1')
  .replace(/;}/g, '}')
  .trim();

fs.writeFileSync(minPath, minified);

console.log('Prismpath Health CSS verified.');
