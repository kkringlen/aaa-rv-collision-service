# Gallery update — version 1.3.0

The new owner-supplied Tiffin Allegro before/after photo is the main feature. Its original JPEG is preserved, with an optimized WebP used on the page. The previous collision repair photos rotate below it and above the existing category gallery.

## Publish to the existing WordPress site

1. Download `AAA-RV-WordPress-Theme-v1.3.0.zip` from the project conversation.
2. In WordPress, open Appearance → Themes → Add Theme → Upload Theme.
3. Choose the ZIP, then Install Now and Replace current with uploaded. This updates the existing AAA RV Redesign theme.
4. Return to the WordPress dashboard so the one-time gallery migration can run, then review `/gallery/`.

The migration replaces only the old comparison block, preserves other page content and existing edits, and keeps a recovery copy and revision. If the old block cannot be identified safely, WordPress displays a notice and leaves the page unchanged. Keep `[aaa_rv_gallery_showcase]` intact in the page editor.

## Add future repairs

Add each repair's images to `assets/`, then append a project to `data/gallery-projects.json`. Supply its title, before/after filenames, descriptive alt text, captions, and actual image dimensions. Rebuild using `python3 scripts/build.py` followed by `python3 scripts/build_wordpress.py`.

## Validation

The static site passed all 1,026 local link and asset checks across 17 pages. Slideshow checks covered automatic rotation, previous/next, keyboard controls, swipe logic, pause behavior, reduced-motion preferences, offscreen suspension, and no-JavaScript fallback. An isolated WordPress 7.1.2 / PHP 7.4.33 upgrade preserved page IDs, owner edits, the lower gallery, and all other pages; repeated migration was verified to be harmless.
