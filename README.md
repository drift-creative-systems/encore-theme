# Encore theme

The master WordPress theme for **Encore** band and artist sites, by [Drift Creative Systems](https://driftcreativesystems.co.uk/). It renders the content that the [Encore Website](https://github.com/drift-creative-systems/encore-website) plugin syncs from each band's Airtable base. Each band gets a CSS-only child theme for branding.

- **Requires:** WordPress 6.2+, PHP 8.0+, and the Encore Website plugin (product: Encore). ACF Pro is needed to edit page modules; without it, pages show their default modules.
- **Theme and plugin are a pair.** Until Encore Website is active, visitors get a "coming soon" page (503) and wp-admin shows **Install & activate Encore Website**. The plugin likewise switches its Setup Wizard off until Encore is active. Each release has an `encore-bundle.zip` containing both.
- **Repo:** https://github.com/drift-creative-systems/encore-theme. The theme self-updates from GitHub releases.

## What's in it

**16 page modules:**

| Module | What it shows |
|---|---|
| Hero | Full-bleed opener |
| Page header | Opener for inner pages |
| Latest release | The current release, large |
| Gigs | The tour list (the signature element) |
| Releases | Discography grid |
| Streaming links | The band's platform profiles |
| Bio | Full or short bio, optional press-kit download |
| Members | Band line-up |
| Press quotes | Review quotes |
| Videos | Click-to-play videos |
| Gallery | Photos with album filters and a lightbox |
| News | Latest posts |
| Merch | Items linking to the band's store |
| Mailing list | Signup form or external link |
| Contact | Booking form plus contact emails |
| Content | Free text, optionally with an image |

**Single templates:**
- Gig, with MusicEvent schema.
- Release, with tracklist, lyrics and MusicAlbum schema.
- News article, plus listings and a 404 page.

**Site-wide:**
- MusicGroup JSON-LD on the home page.
- Description and Open Graph tags, only when no SEO plugin is active.
- Optional GA4, which only loads after cookie consent (set it in the Customiser).
- Videos make no third-party requests until someone presses play.

**Default modules:** a page with no modules of its own shows the modules the plugin's map defines for it, so a site built by the Setup Wizard is presentable straight away.

## Child themes

A child theme is one `style.css` with design tokens: colours, fonts, type width and weight, radius. It contains no PHP. Fonts are chosen with an `Encore Fonts:` header line in that file. Copy `child-themes/encore-child-starter` (in the Encore project folder) and see `DESIGN.md` for every token.

The accent colours always come from Airtable (Site Settings → Primary/Secondary Colour).

## Releasing

1. Bump `Version:` in `style.css` and add a `CHANGELOG.md` entry.
2. Push to `main`.
3. Build the zips: `python tools/build-release.py` writes `dist/encore-theme.zip` (with an `encore-theme/` top folder; `.git`, `tools/`, `dist/` and `CLAUDE.md` left out) and `dist/encore-bundle.zip` (theme zip + the latest `encore-website.zip` + an install README). Pass `--plugin-zip path/to/encore-website.zip` to bundle a specific plugin build.
4. Create a GitHub release tagged `vX.Y.Z` with both zips attached. `encore-theme.zip` must be there: the updater uses release assets, and the plugin's Install button downloads it by that name.

For a private repo, define `ENCORE_GITHUB_TOKEN` (or reuse the plugin's `ENCORE_WEBSITE_GITHUB_TOKEN`; 1.x `DRIFT_WEBSITE_GITHUB_TOKEN` still works) in `wp-config.php`.

## Licence

Split licence, © Drift Creative Systems:

- **PHP files:** GPL-2.0-or-later (`GPL-2.0.txt`), because they run inside WordPress.
- **Everything else** (CSS, JavaScript, images, ACF JSON, docs): proprietary, all rights reserved. They can't be copied, modified, redistributed or used in a competing product without written permission.
- **`lib/plugin-update-checker/`:** MIT, by its author.
- **Names:** "Drift" and "Encore" are reserved. Modified versions can't be distributed under them.

The full terms are in `LICENSE`.
