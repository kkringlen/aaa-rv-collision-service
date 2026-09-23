"""Package the approved static design as a native WordPress theme."""
from pathlib import Path
from html import unescape
import json, re, shutil, zipfile

ROOT = Path(__file__).resolve().parents[1]
THEME = ROOT / 'wordpress' / 'aaa-rv-redesign'
THEME.mkdir(parents=True, exist_ok=True)
(THEME / 'parts').mkdir(exist_ok=True)
TITLES = {'home':'Home','rv-services':'RV Services','claim-help':'Claim Help','contact':'Contact',
          'about':'About','gallery':'Our Work','join-our-team':'Join Our Team','links':'RV Resources'}

def portable(markup):
    def url(match):
        attr, value = match.groups()
        test_base = 'https://kkringlen.github.io/aaa-rv-collision-service/'
        if value.startswith(test_base): value = './' + value[len(test_base):]
        if value.startswith(('./', '../')):
            value = re.sub(r'^(?:\./|\.\./)', '', value)
            value = ('{{AAA_THEME}}/' if value.startswith('assets/') or value == 'favicon.svg' else '{{AAA_HOME}}') + value
        return attr + '="' + value + '"'
    return re.sub(r'(href|src)="([^"]*)"', url, markup)

pages = {}
sources = [ROOT/'index.html'] + sorted(p for p in ROOT.glob('*/index.html') if p.parent.name != 'node_modules') + [ROOT/'404.html']
for path in sources:
    key = 'home' if path == ROOT/'index.html' else ('404' if path.name == '404.html' else path.parent.name)
    raw = path.read_text()
    body = portable(re.search(r'<main id="main">(.*?)</main>', raw, re.S).group(1))
    body = re.sub(r'<iframe id="shopmonkey-work-request".*?</iframe>', '[aaa_rv_work_request]', body, flags=re.S)
    title = unescape(re.search(r'<title>(.*?)</title>', raw).group(1))
    desc = unescape(re.search(r'<meta name="description" content="([^"]*)"', raw).group(1))
    pages[key] = {'title':TITLES.get(key, title.split(' | ')[0]), 'seo_title':title, 'description':desc, 'content':body}
    if key == 'home':
        header = re.search(r'<body>(.*?)<main id="main">',raw,re.S).group(1)
        footer = re.search(r'</main>(.*?)</body>',raw,re.S).group(1)
        (THEME/'parts/header.html').write_text(portable(header))
        (THEME/'parts/footer.html').write_text(portable(footer))
    if key == 'contact':
        strip = re.search(r'(<div class="policy-strip">.*?)<main id="main">',raw,re.S).group(1)
        (THEME/'parts/policy.html').write_text(portable(strip))

(THEME/'content.json').write_text(json.dumps(pages,ensure_ascii=False,indent=2)+'\n')
shutil.copytree(ROOT/'assets',THEME/'assets',dirs_exist_ok=True)
for name in ('styles.css','site.js','favicon.svg'): shutil.copy2(ROOT/name,THEME/name)
with (THEME/'styles.css').open('a') as f:
    f.write('\nbody.admin-bar .site-header{top:32px}@media(max-width:782px){body.admin-bar .site-header{top:46px}}@media(max-width:600px){body.admin-bar .site-header{top:0}}\n')
package = ROOT.parent/'AAA-RV-WordPress-Theme.zip'
with zipfile.ZipFile(package,'w',zipfile.ZIP_DEFLATED) as z:
    for path in sorted(THEME.rglob('*')):
        if path.is_file(): z.write(path, 'aaa-rv-redesign/'+path.relative_to(THEME).as_posix())
print(f'Packaged {len(pages)-1} WordPress pages and 404 template: {package} ({package.stat().st_size:,} bytes)')
