# Changelog

Format: [Keep a Changelog](https://keepachangelog.com/en/1.1.0/). Versioning: [SemVer](https://semver.org/).

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
- First release. Lean master theme for Drift: Encore, built on vision_base_theme's architecture (modular `inc/`, flexible-content page builder, layout-option variants, CSS-only child themes, GitHub self-updates). The TTNG-specific code was not carried over.
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
- ACF JSON always saves to the master theme, never the child (fixes the vision_base_theme save-point gotcha).
