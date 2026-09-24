# AAA RV WordPress release

Run `python3 scripts/build.py` and then `python3 scripts/build_wordpress.py` from the repository root. The second command creates `AAA-RV-WordPress-Theme.zip` beside the checkout.

The uploadable theme is `wordpress/aaa-rv-redesign`. Upload its ZIP through **Appearance → Themes → Add Theme → Upload Theme**. Use Live Preview before activation.

The first activation imports the 15 approved pages. It reuses existing page IDs and addresses, adds the seven service pages, and keeps other WordPress pages. Original content and metadata are retained in `_aaa_rv_legacy_snapshot`, alongside WordPress revisions and the full prelaunch backup. The import runs once; later theme switches do not overwrite edited pages.

The redesign content is stored in regular WordPress Pages as Custom HTML blocks. Use the page editor's Code Editor to make layout-aware edits. The theme provides shared navigation, footer, styling, and the `[aaa_rv_work_request]` shortcode for Shopmonkey. No application passwords, credentials, or private backups are included.

Production links resolve through WordPress `home_url()` and theme asset URLs. The test-only `robots.txt` and `noindex` markup are not packaged. WordPress Reading settings and existing SEO-plugin settings remain authoritative. Yoast receives the approved page titles and descriptions; existing analytics and plugin integrations retain `wp_head()` and `wp_footer()` hooks.

Before activation, take a full WordPress database/files backup. The prelaunch backup was created using the site's existing All-in-One WP Migration plugin on September 23, 2026. Keep the previous `AAARVColFV1B` theme installed. For a complete rollback, restore the prelaunch backup. Switching themes alone restores the old theme's appearance but does not restore the imported page content.

Validation: the theme was tested in an isolated WordPress 7.1.2 installation on PHP 7.4.33. Checks covered all 15 page imports, preserved page IDs and original content, unrelated pages, repeat activation, Shopmonkey shortcode rendering, production URLs, the updated hero image, inline icons, and indexable homepage output. The existing WordPress Customizer was used to check desktop and mobile layouts and the live Shopmonkey form without submitting a work request.

## Version 1.1.0 — Text us and Request Service

The compact header Text us link uses `sms:+14056341429`, alongside the shop's existing phone link. The mobile action bar includes Call, Text, and Request Service. SMS links open the visitor's configured messaging app; devices without a messaging handler may not support them. The destination currently matches the displayed shop number; a different business texting line can be configured in `scripts/build.py` before rebuilding.

Request Service is a regular published WordPress page at `/request-service/`, reachable from the utility bar, mobile menu/action bar, and footer. It embeds the existing Shopmonkey form with immediate loading and a direct-form fallback. The Contact page keeps its form and existing anchors and links to the new page.

Uploading version 1.1.0 over the active theme creates only the new page through an administrator-only, one-time update. It preserves existing page IDs and owner edits, changes only the known Contact introduction/link, and saves a recovery copy and revision for that edit. Repeated updates do not overwrite later page edits. No additional WordPress plan or page allowance is needed on this self-hosted installation.
