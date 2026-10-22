// Run from any directory: node docs/check_assets.cjs [--http=http://localhost/it-rspi2/]
// Checks local asset references; it does not execute PHP or change business data.
const fs = require('node:fs');
const path = require('node:path');
const root = path.resolve(__dirname, '..');
const walk = dir => fs.readdirSync(dir, {withFileTypes: true}).flatMap(entry =>
  entry.name === '.git' ? [] : entry.isDirectory() ? walk(path.join(dir, entry.name)) : [path.join(dir, entry.name)]);
const files = walk(root);
const issues = [], targets = new Set(), stylesheets = new Set();
let references = 0;
function check(file, url) {
  if (/^(https?:|data:|\/\/|#)/i.test(url)) return;
  // Included dashboard modules may prepend the application base with PHP.
  url = url.replace(/<\?=.*?\?>/g, '').split(/[?#]/)[0];
  if (/[<$]/.test(url) || !/\.(css|js|woff2?|ttf|eot|svg|png|gif|jpg)$/i.test(url)) return;
  const absolute = url.includes('assets/')
    ? path.join(root, url.slice(url.indexOf('assets/')))
    : path.resolve(path.dirname(file), url);
  references++;
  if (!absolute.startsWith(root + path.sep) || !fs.existsSync(absolute)) {
    issues.push(`${path.relative(root, file)}: missing ${url}`);
    return;
  }
  targets.add(absolute);
  if (!absolute.endsWith('.css') || stylesheets.has(absolute)) return;
  stylesheets.add(absolute);
  for (const match of fs.readFileSync(absolute, 'utf8').matchAll(/url\(\s*['"]?([^'"\s)]+)['"]?\s*\)/g)) check(absolute, match[1]);
}
for (const file of files) {
  const relative = path.relative(root, file);
  if (/\.(css|js)$/i.test(file) && !relative.startsWith('assets' + path.sep + 'dist' + path.sep)) {
    issues.push(`${relative}: CSS/JS outside assets/dist`);
  }
  if (!/\.(php|html)$/.test(file) || /^(assets|library|docs)[/\\]/.test(relative)) continue;
  const source = fs.readFileSync(file, 'utf8');
  for (const match of source.matchAll(/(?:src|href)\s*=\s*["']([^"']+)["']/g)) check(file, match[1]);
}
(async () => {
  const option = process.argv.find(arg => arg.startsWith('--http='));
  let checkedHttp = 0;
  if (option) {
    const base = new URL(option.slice(7).replace(/\/?$/, '/'));
    if (!['http:', 'https:'].includes(base.protocol)) throw new Error('HTTP(S) base URL required');
    for (const target of targets) {
      const relative = path.relative(root, target).split(path.sep).map(encodeURIComponent).join('/');
      const response = await fetch(new URL(relative, base), {method: 'HEAD', signal: AbortSignal.timeout(10000)});
      checkedHttp++;
      if (!response.ok) issues.push(`${relative}: HTTP ${response.status}`);
    }
  }
  for (const issue of issues) console.error(issue);
  console.log(`${issues.length ? 'FAIL' : 'PASS'}: ${references} local references, ${targets.size} unique assets, ${stylesheets.size} stylesheets${option ? ', ' + checkedHttp + ' HTTP requests' : ''}; ${issues.length} issues.`);
  process.exitCode = issues.length ? 1 : 0;
})().catch(error => { console.error(error.message); process.exitCode = 1; });
