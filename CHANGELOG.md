# Changelog

Format: [Keep a Changelog](https://keepachangelog.com/en/1.1.0/). Versioning: [SemVer](https://semver.org/).

## [2.1.0] - 2026-10-08

Pairs with Drift: Surface 3.1.0.

### Added
- Hero: plays the uploaded **Hero Video** (`hero_video_file`) when there is one, falling back to Hero Video URL.
- Contact: lists the **General Email** first, labelled "General".

## [2.0.1] - 2026-10-08

### Changed
- LICENSE: reserved names updated to the current product names, matching Drift: Surface.
- Changelog starts at 2.0.0, the first release of Drift: Surface Theme.

## [2.0.0] - 2026-10-08

First release of **Drift: Surface Theme**, the master theme for Drift: Surface band and artist websites. Needs Drift: Surface 3.0.0 or later.

### Added
- Modular theme (`inc/`) with an ACF flexible-content page builder (`acf-json/group_surface_page_builder.json`) and 16 page modules. Layout-option variants resolve child-first; ACF JSON always saves to this theme, never a child.
- Default rows from the plugin's product map when a page has none, or when ACF is inactive.
- Templates for gigs (`single-surface_gig.php`), releases (`single-surface_release.php`, with tracklist and lyrics), news, listings, search and 404. Reads the plugin's `surface_*` post types and `surface_release_type` / `surface_album` taxonomies.
- Plugin pairing (`inc/requirements.php`): until Drift: Surface is active, visitors get a "coming soon" holding page (503 with `Retry-After`, no analytics or fonts) and wp-admin offers **Install & activate Drift: Surface** or **Activate**.
- Plugin API through `drift_surface_setting()`, `drift_surface_linked_posts()`, `drift_surface_form_hidden_fields()` and `Drift_Surface_Page_Creator`, all guarded, wrapped by `surface_setting()` and friends.
- JSON-LD: MusicGroup (home), MusicEvent with offers (gigs, plus upcoming events on home), MusicAlbum with an ordered tracklist (releases). Fallback description and Open Graph tags when no SEO plugin is active.
- Brand colours from the hub as CSS custom properties, with WCAG-based text colour on the accent.
- CSS-only child themes (`Template: surface-theme`), with fonts chosen through a `Surface Fonts:` style.css header.
- Click-to-load live and merch embeds (iframe-only, https-only, held in an inert `<template>` until pressed), click-to-play YouTube/Vimeo (youtube-nocookie), gallery filters and a native `<dialog>` lightbox.
- AJAX booking and mailing-list forms through the plugin. Forms read `getAttribute( 'action' )`, because the plugin's `<input name="action">` shadows `form.action`.
- Optional GA4 behind cookie consent (Customiser → Analytics).
- Header stays pinned over hero pages, starting transparent and turning solid on scroll.
- Footer credit "Website by Drift Creative Systems".
- Self-updates from GitHub releases (`surface-theme.zip`). `tools/build-release.py` also builds `surface-bundle.zip` with the plugin and an install README. Private repos: `SURFACE_THEME_GITHUB_TOKEN` or the plugin's `DRIFT_SURFACE_GITHUB_TOKEN`.
- Split licence: PHP is GPL-2.0-or-later, everything else is proprietary to Drift Creative Systems. See `LICENSE`.
