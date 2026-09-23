# AAA RV Collision & Service — redesign

Test website: https://kkringlen.github.io/aaa-rv-collision-service/

A complete static redesign using the supplied black-and-gold shield logo, inspired by the broad layout of Edwards Canvas. All HTML, CSS, JavaScript, images, and fonts are included locally. GitHub Pages needs no build command.

## Pages

- Home and RV services overview
- Collision & fiberglass repair
- Paint & graphics
- Roofs & water damage
- Service & maintenance
- Appliances & RV systems
- Trailer chassis & suspension
- Interior repair & restoration
- Claim help, About, Our work/gallery, Contact/estimate policy
- RV resources and Careers
- Custom 404 page

All eight original navigation destinations are retained at their existing paths, and the service detail pages expand the former service list. Careers and resources are in the footer to simplify the primary navigation. A selected set of original gallery images is included.

## Upload this package through GitHub

1. Extract `AAA-RV-Website-Redesign.zip` on your computer.
2. Open https://github.com/kkringlen/aaa-rv-collision-service and select `main`.
3. Choose **Add file → Upload files**.
4. Drag the extracted files and folders into GitHub, including `assets/` and every page folder. Upload the contents, not the ZIP and not an extra containing folder.
5. Commit the upload to `main` and wait for the existing Pages deployment to finish.
6. Open https://kkringlen.github.io/aaa-rv-collision-service/ and refresh.

The root `index.html`, `styles.css`, `favicon.svg`, and `README.md` replace their older counterparts. The existing unused legacy logo may remain without affecting the new site. No file exceeds GitHub's browser upload size limit.

## GitHub Pages

Publish the `main` branch from `/ (root)` using the repository's existing Pages setup. No `CNAME` is included; this is the GitHub testing site. The production domain remains managed separately.

Internal URLs are relative, so the repository subdirectory works correctly. Every page can be opened directly. The custom 404 uses absolute test-site assets because GitHub serves it from arbitrary missing paths.

The test site includes `noindex, nofollow` on every page and a crawler exclusion in `robots.txt`. These are search-engine instructions, not access controls; the test website is public. Before moving to the production domain, update `BASE` and the robots metadata in `scripts/build.py`, then regenerate the HTML.

## Editing

- Shared header, footer, page text, service data, and metadata: `scripts/build.py`
- Styles and responsive rules: `styles.css`
- Mobile navigation and menu keyboard behavior: `site.js`
- Photos, original supplied logo, and licensed local fonts: `assets/`

After editing the generator, run `python3 scripts/build.py`. Generated HTML is committed and requires no Python on the host. Run `python3 scripts/check.py` to audit local links, assets, anchors, headings, metadata, and estimate notices.

Optional live preview: `npm ci` then `npm run dev`. Vite is a development-only dependency. For a plain static preview, use `python3 -m http.server 8000` from this directory and open http://localhost:8000/.

## Customer actions

Calls link to `tel:+14056341429`. Google Maps opens the shop's address. The Contact page retains a secondary link to the existing Shopmonkey request page; the new website does not collect or submit customer data. The external Shopmonkey experience remains controlled by Shopmonkey and should be checked separately for its wording and availability.

## Policy and content

- No estimate charge when AAA RV completes the repairs in the approved estimate. An estimate fee applies if AAA RV prepares an estimate and the customer chooses not to have those approved repairs completed at AAA RV.
- The homepage has no top estimate banner; the benefit and conditions appear in the lower estimate section. Other pages use a positive benefit banner.
- Free quotes are available for service and maintenance.
- Diagnostic labor may apply when troubleshooting is required.
- Exact fee amounts were intentionally not added, as directed.
- Published contact details: 10519 S. Sunnylane, Oklahoma City, OK 73160; (405) 634-1429; Monday–Friday 8 AM–5 PM.
- Lifetime workmanship guarantee is retained from the existing website, with an invitation to ask about coverage for a specific repair.
- Historical staff profiles, old holiday closures, insurer rankings, and conflicting founding-date claims are not presented as current facts.

See `CONTENT-SOURCES.md` for the page mapping and photo provenance.
