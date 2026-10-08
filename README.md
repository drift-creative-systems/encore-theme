# Drift: Surface Theme

The master WordPress theme for **Drift: Surface** band and artist sites, by [Drift Creative Systems](https://driftcreativesystems.co.uk/). It renders the content that the [Drift: Surface](https://github.com/drift-creative-systems/drift-surface) plugin syncs from each band's Drift: Surface Hub. Each band gets a CSS-only child theme for branding.

- **Requires:** WordPress 6.2+, PHP 8.0+, and the Drift: Surface plugin (product: Surface). ACF Pro is needed to edit page modules; without it, pages show their default modules.
- **Theme and plugin are a pair.** Until Drift: Surface is active, visitors get a "coming soon" page (503) and wp-admin shows **Install & activate Drift: Surface**. The plugin likewise switches its Setup Wizard off until this theme is active. Each release has a `surface-bundle.zip` containing both.
- **Repo:** https://github.com/drift-creative-systems/surface-theme. The theme self-updates from GitHub releases.

## How the four Drift: Surface repos fit together

Drift: Surface is four repos, released separately. This section is the same in all four READMEs; update it in all four.

| Repo | Runs on | Job |
|---|---|---|
| [`drift-hub`](https://github.com/drift-creative-systems/drift-hub) (plugin) | the hub site | Where content is edited. `schemas/surface.php` defines every table and field. Serves the website API (`/wp-json/drift-hub/v0/`) and sends Publish webhooks. |
| [`drift-hub-theme`](https://github.com/drift-creative-systems/drift-hub-theme) (theme) | the hub site | Blank. Redirects the front end to the hub; 503 page if the plugin is off. |
| [`drift-surface`](https://github.com/drift-creative-systems/drift-surface) (plugin) | each artist site | Syncs from the hub. `maps/surface.php` says which hub table/field lands in which post type, meta key or setting. Receives Publish at `/wp-json/drift-surface/v1/publish`. |
| [`surface-theme`](https://github.com/drift-creative-systems/surface-theme) (theme) | each artist site | Renders what `drift-surface` wrote: `surface_*` post types, post meta, settings. Module names are a contract with the map's `pages[].rows`. |

```
drift-hub schemas/surface.php ──API──▶ drift-surface maps/surface.php ──WP posts/meta/settings──▶ surface theme templates
        ▲                                        │
        └──────── Publish webhook (hub → site) ──┘
drift-hub-theme: only cares about the hub's URL (Drift_Hub_App::url())
```

### When something changes in the hub

**Adding or changing a field or table** in `drift-hub/schemas/surface.php`:
1. **drift-hub:** add it to the schema. If it's `'hub_only' => true` (e.g. Hub Avatar), stop here: websites never see it.
2. **drift-surface:** add the same table/field name, type and select options to `maps/surface.php`, with its `to` key (meta key or setting). Names must match exactly; **Check connection** on the Connection tab compares them.
3. **Surface theme:** show it in the module or single template that needs it (read via `get_post_meta()` or the setting helpers). Add ACF JSON if a module gets a new option.
4. **drift-hub-theme:** usually nothing. It only changes if the hub's URL, root mode or `Drift_Hub_App::url()` changes.

**Release order:**
- **New fields:** release the **hub first**, then drift-surface + theme. The website asks for the fields in its map (`fields[]`), and the hub answers **422** to any field name it doesn't know, so a map that runs ahead of the hub breaks that table's sync.
- **Renames and removals:** the **website first** (stop asking for the old name), then the hub. In the hub, use `'was'` for renames; there are no migrations.
- **Webhook or API contract changes** (path, header, response shape): release both together and say so in both CHANGELOGs (e.g. hub 1.4.0 ↔ Drift: Surface 3.0).

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

A child theme is one `style.css` with design tokens: colours, fonts, type width and weight, radius. It contains no PHP. Its header needs `Template: surface-theme`. Fonts are chosen with a `Surface Fonts:` header line in that file. See `DESIGN.md` for every token.

The accent colours always come from the hub (Site Settings → Primary/Secondary Colour).

## Releasing

1. Bump `Version:` in `style.css` and add a `CHANGELOG.md` entry.
2. Push to `main`.
3. Build the zips: `python tools/build-release.py` writes `dist/surface-theme.zip` (with a `surface-theme/` top folder; `.git`, `tools/`, `dist/` and `CLAUDE.md` left out) and `dist/surface-bundle.zip` (theme zip + the latest `drift-surface.zip` + an install README). Pass `--plugin-zip path/to/drift-surface.zip` to bundle a specific plugin build.
4. Create a GitHub release tagged `vX.Y.Z` with both zips attached. `surface-theme.zip` must be there: the updater uses release assets, and the plugin's Install button downloads it by that name.

For a private repo, define `SURFACE_THEME_GITHUB_TOKEN` (or reuse the plugin's `DRIFT_SURFACE_GITHUB_TOKEN`) in `wp-config.php`.

## Licence

Split licence, © Drift Creative Systems:

- **PHP files:** GPL-2.0-or-later (`GPL-2.0.txt`), because they run inside WordPress.
- **Everything else** (CSS, JavaScript, images, ACF JSON, docs): proprietary, all rights reserved. They can't be copied, modified, redistributed or used in a competing product without written permission.
- **`lib/plugin-update-checker/`:** MIT, by its author.
- **Names:** "Drift", "Drift: Surface" and "Surface" are reserved. Modified versions can't be distributed under them.

The full terms are in `LICENSE`.
