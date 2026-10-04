# Encore theme

The master WordPress theme for **Drift: Encore** band and artist sites, by The Bonsai Digital Collective. It renders the content that the [Drift Website](https://github.com/drift-creative-systems/drift-website) plugin syncs from each band's Airtable base. Each band gets a CSS-only child theme for branding.

- **Requires:** WordPress 6.2+, PHP 8.0+, and the Drift Website plugin (product: Encore). ACF Pro is needed to edit page modules; without it, pages show their default modules.
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
3. Build the zip from the parent folder: `zip -r encore-theme.zip encore-theme -x "encore-theme/.git/*"`.
4. Create a GitHub release tagged `vX.Y.Z` with the zip attached.

For a private repo, define `ENCORE_GITHUB_TOKEN` (or reuse `DRIFT_WEBSITE_GITHUB_TOKEN`) in `wp-config.php`.
