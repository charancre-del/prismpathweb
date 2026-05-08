const fs = require('fs');
const path = require('path');

const cssPath = path.join(__dirname, '..', 'assets', 'css', 'main.css');
const css = fs.readFileSync(cssPath, 'utf8');

if (!css.includes('.home-hero') || !css.includes('.consult-section')) {
  throw new Error('Compiled CSS is missing required Prismpath Health sections.');
}

console.log('Prismpath Health CSS verified.');
