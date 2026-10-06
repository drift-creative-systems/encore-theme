# Changelog

Format: [Keep a Changelog](https://keepachangelog.com/en/1.1.0/). Versioning: [SemVer](https://semver.org/).

## [Unreleased]

### Changed
- `.btn--ghost:hover` text uses `var(--on-accent)` (was `var(--bg)`). The hover background is still `var(--text)`, so set the two to contrasting colours in a child theme.
- Header stays pinned on pages that open with a hero. It starts transparent over the image and fades to the solid header once the page scrolls (`.is-scrolled`, toggled in `main.js`). Other pages were already sticky.
- Footer credit is now "Website by Drift Creative Systems", linked to https://driftcreativesystems.co.uk/. Author URI points there too.

### Fixed
- Forms posted to `/current-page/[object HTMLInputElement]` (a 404) and failed with "JSON.parse: unexpected character". The script read `form.action`, which returns the form's `<input name="action">` rather than its `action` attribute; it now uses `getAttribute( 'action' )`. (1.3.0 wrongly blamed the `page` field; renaming it to `source_page` was harmless but not the fix.)

## [1.3.0] - 2026-10-06

### Changed
- The plugin is now **Encore Website** (formerly Drift Website). The theme calls `encore_website_setting()`, `encore_website_linked_posts()`, `encore_website_form_hidden_fields()` and `Encore_Website_Page_Creator`. Each falls back to its 1.x `drift_*` name, so this theme works with plugin 1.x and 2.x whichever updates first.
- The requirements notice installs `encore-website.zip` from the `encore-website` repo and recognises the plugin under either name or folder.
- Forms: `.drift-form` → `.encore-form`. New `encore_form_hidden_fields()` helper. Body class `no-drift` → `no-encore-website`.
- Contact form posts the page URL as `source_page`, not `page`. `page` is a WordPress admin query var, and logged-in AJAX requests run `admin_init`. Needs Encore Website 2.0.0's map, which reads `source_page`.
- `tools/build-release.py` bundles `encore-website.zip`.
- Update token: also reads `ENCORE_WEBSITE_GITHUB_TOKEN`.

### Fixed
- Forms no longer show the browser's raw "JSON.parse: unexpected character…" message when the server answers with something other than JSON (an HTML error page, a security plugin, a redirect). Visitors see the normal "Something went wrong" message instead.

## [1.2.0] - 2026-10-06

### Added
- Live and merch embeds. When Airtable's Site Settings → **Live Embed** or **Merch Embed** holds iframe code, it replaces the gig list (every `gigs_module`) or the merch grid (`merch_module`). Embeds are click-to-load: the iframe sits in an inert `<template>` until the visitor presses the button, so no third-party requests or cookies happen before that. New helpers `encore_kses_iframe()` (iframe-only, https-only, re-filtered on output) and `encore_embed()`. Needs Drift Website 1.2.0 or later.

### Changed
- Licence: split. PHP stays GPL-2.0-or-later (`GPL-2.0.txt`); everything else (CSS, JS, images, ACF JSON, docs) is proprietary to Drift Creative Systems. See `LICENSE`. The Drift and Encore names are reserved.
- Code comments and changelog no longer name the private projects the theme was originally built from.

## [1.1.0] - 2026-10-04

### Added
- The theme and the Drift Website plugin now work as a pair (`inc/requirements.php`). Until the plugin is active, visitors get a standalone "coming soon" holding page (503 with `Retry-After`, no analytics or fonts), and wp-admin shows a persistent notice with **Install & activate Drift Website** (downloads its latest GitHub release) or **Activate** if it's already installed. Drift Website 1.1.0 does the reverse and blocks its Setup Wizard until Encore is active.
- `tools/build-release.py` builds `encore-theme.zip` and `encore-bundle.zip` (both zips plus an install README) for each release.

### Changed
- The plugin notice moved from `inc/admin.php` to `inc/requirements.php`.

## [1.0.0] - 2026-10-04

### Changed
- First stable release. The repo moved to `drift-creative-systems/encore-theme` (public). The theme URI and the self-update source now point there.
- The theme author is now Drift Creative Systems, matching the Drift Website plugin.

## [0.1.0] - 2026-10-03

### Added
- First release. Lean master theme for Drift: Encore (modular `inc/`, flexible-content page builder, layout-option variants, CSS-only child themes, GitHub self-updates).
- 16 page modules with ACF Local JSON (`acf-json/group_encore_page_builder.json`).
- Default rows from the Drift product map when a page has none, or when ACF is inactive.
- Single gig and release templates; news, listing and 404 templates.
- JSON-LD: MusicGroup (home), MusicEvent (gigs, plus upcoming events on home), MusicAlbum with tracklist (releases).
- Fallback description and Open Graph tags when no SEO plugin is active.
- Brand colours from Airtable as CSS custom properties, with automatic WCAG-based text colour on the accent.
- Child-theme font selection through an `Encore Fonts:` style.css header (no PHP).
- Click-to-play YouTube/Vimeo (youtube-nocookie), gallery filters and a native `<dialog>` lightbox.
- AJAX booking and mailing-list forms through the Drift plugin.
- Optional GA4 behind cookie consent (Customiser → Analytics).
- ACF JSON always saves to the master theme, never the child, so new module fields can't be lost in a child theme.
