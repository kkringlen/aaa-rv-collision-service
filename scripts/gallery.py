"""Render the featured repair and an expandable before/after photo rotation."""
import json
from html import escape
from pathlib import Path

def gallery_showcase(root: Path) -> str:
    projects = json.loads((root/'data/gallery-projects.json').read_text())['projects']
    slides = []
    for project in projects:
        for phase in ('before', 'after'):
            photo = project[phase]
            if not (root/'assets'/photo['image']).is_file():
                raise ValueError('Missing gallery image: ' + photo['image'])
            src = '{{R}}assets/' + photo['image']
            title, label = escape(project['title'], quote=True), phase.title()
            slides.append(f'''<figure class="repair-slide" data-title="{title}" data-phase="{label}" data-description="{escape(photo['description'], quote=True)}">
<a href="{src}" target="_blank" rel="noopener" aria-label="Open {label.lower()} photo: {title}"><img src="{src}" alt="{escape(photo['alt'], quote=True)}" width="{photo['width']}" height="{photo['height']}" loading="lazy"></a>
<figcaption class="repair-phase">{label}</figcaption></figure>''')
    if not slides:
        raise ValueError('Add at least one repair pair to data/gallery-projects.json')
    first = projects[0]
    return '''<!-- aaa-gallery-showcase:start -->
<figure class="gallery-feature">
<a href="{{R}}assets/tiffin-before-after.jpg" target="_blank" rel="noopener" aria-label="Open the Tiffin Allegro before and after photograph at full size"><img src="{{R}}assets/tiffin-before-after.webp" alt="Tiffin Allegro Open Road before and after front-end collision repair; damaged front cap on the left and repaired front cap on the right" width="1672" height="941" loading="lazy"></a>
<figcaption><span>Tiffin Allegro Open Road</span><span>Before &amp; after · Collision repair</span></figcaption></figure>
<section class="repair-carousel" data-repair-carousel aria-roledescription="carousel" aria-labelledby="repair-carousel-title">
<div class="repair-carousel-heading"><div><p class="eyebrow">MORE FROM OUR SHOP</p><h2 id="repair-carousel-title">From damage to repair.</h2></div><p>See the difference, one repair at a time.</p></div>
<div class="repair-carousel-body"><div class="repair-viewport"><div class="repair-track" id="repair-photos">''' + ''.join(slides) + '''</div></div>
<div class="repair-copy"><p class="small-label">COLLISION REPAIR</p><h3 data-repair-title>''' + escape(first['title']) + '''</h3><p class="repair-description" data-repair-description>''' + escape(first['before']['description']) + '''</p>
<p class="repair-status" data-repair-status aria-live="off" aria-atomic="true">Before · Photo 1 of ''' + str(len(slides)) + '''</p>
<div class="repair-controls" hidden><button type="button" data-repair-pause aria-controls="repair-photos">Pause slideshow</button><div class="repair-nav"><button type="button" data-repair-previous aria-label="Previous repair photo" aria-controls="repair-photos">←</button><span data-repair-count aria-hidden="true">1 / ''' + str(len(slides)) + '''</span><button type="button" data-repair-next aria-label="Next repair photo" aria-controls="repair-photos">→</button></div></div>
</div></div></section>
<!-- aaa-gallery-showcase:end -->'''
