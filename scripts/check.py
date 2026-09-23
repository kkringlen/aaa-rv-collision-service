"""Audit every generated page's navigation, media, metadata, and estimate notice."""
from html.parser import HTMLParser
from pathlib import Path
from urllib.parse import urlsplit, unquote
import json, re

ROOT=Path(__file__).resolve().parents[1]
class Page(HTMLParser):
    def __init__(self, text):
        super().__init__(); self.links=[]; self.ids=[]; self.h1=0; self.images=[];self.feed(text)
    def handle_starttag(self, tag, attrs):
        a=dict(attrs)
        if 'id' in a:self.ids.append(a['id'])
        if tag=='h1':self.h1+=1
        if tag=='img':self.images.append(a)
        if tag in ('a','link','script','img'):
            href=a.get('href',a.get('src'))
            if href:self.links.append(href)

pages={p:Page(p.read_text()) for p in ROOT.rglob('*.html') if not {'node_modules', 'wordpress'}.intersection(p.relative_to(ROOT).parts)}
errors=[];count=0
for p,doc in pages.items():
    raw=p.read_text(); rel=str(p.relative_to(ROOT))
    if doc.h1!=1:errors.append(f'{rel}: expected one h1, got {doc.h1}')
    if len(doc.ids)!=len(set(doc.ids)):errors.append(f'{rel}: duplicate IDs')
    if '{{R}}' in raw:errors.append(f'{rel}: unresolved prefix')
    if 'contact/#estimate-policy' not in raw:errors.append(f'{rel}: missing estimate policy link')
    if '<meta name="description"' not in raw:errors.append(f'{rel}: missing description')
    for payload in re.findall(r'<script type="application/ld\+json">(.*?)</script>',raw):json.loads(payload)
    for im in doc.images:
        if not im.get('alt'):errors.append(f'{rel}: missing image alt')
    for href in doc.links:
        count+=1;u=urlsplit(href)
        if u.scheme or u.netloc:continue
        target=(p.parent/unquote(u.path)).resolve() if u.path else p
        if target.is_dir():target=target/'index.html'
        if not target.exists():errors.append(f'{rel}: missing {href}')
        elif u.fragment and target in pages and u.fragment not in pages[target].ids:errors.append(f'{rel}: missing anchor {href}')
for css in [ROOT/'styles.css',ROOT/'assets/fonts.css']:
    for ref in re.findall(r'url\([\'"]?([^\)\'\"]+)',css.read_text()):
        if not urlsplit(ref).scheme and not (css.parent/ref).exists():errors.append(f'{css.name}: missing {ref}')
if errors:print('\n'.join(errors));raise SystemExit(1)
print(f'PASS: {len(pages)} pages; {count} link and asset references; estimate notices, headings, image labels, anchors, and structured data.')
