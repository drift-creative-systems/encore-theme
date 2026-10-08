# Drift: Surface Theme design tokens

Everything a band's look can change is set in its child theme's `style.css`, in a `:root { }` block. The defaults are in `assets/css/core/base.css`.

## Set by the hub (don't override)

| Token | Source |
|---|---|
| `--accent` | Site Settings → Primary Colour |
| `--accent-2` | Site Settings → Secondary Colour (falls back to the accent) |
| `--on-accent` | Calculated: black or white, whichever has the better WCAG contrast on the accent |
| `--accent-rgb` | Calculated: `r, g, b` for `rgba()` tints |

## Colour

| Token | Default | Notes |
|---|---|---|
| `--bg` | `#000` | Page background |
| `--text` | `#fff` | |
| `--muted` | `rgba(255,255,255,.64)` | Secondary text |
| `--line` | `rgba(255,255,255,.16)` | Rules and borders |
| `--surface` | `rgba(255,255,255,.06)` | Inputs, placeholders |
| `--header-text` | `#fff` | Header text when it sits over a hero image |

For a light theme, set all five colour tokens (see `hollin-wren`).

## Type

| Token | Default | Notes |
|---|---|---|
| `--font-body` | Archivo | |
| `--font-display` | `var(--font-body)` | Headings, gig venues, big numbers |
| `--display-stretch` | `68%` | Archivo's width axis (62%–125%). Use `100%` for fonts without one. |
| `--display-weight` | `850` | |
| `--display-transform` | `none` | `uppercase` for a poster look |
| `--display-tracking` | `-0.015em` | |
| `--display-leading` | `0.88` | Raise it (about 1) for serifs |
| `--body-size` / `--body-leading` | `1.0625rem` / `1.6` | |
| `--step--1` … `--step-5` | fluid scale | Rarely worth changing |

**Fonts:** add a header line to the child's `style.css` comment block:

```
Surface Fonts: https://fonts.googleapis.com/css2?family=Young+Serif&family=Work+Sans:wght@400;600;700&display=swap
```

Only `fonts.googleapis.com` URLs are accepted. Use `Surface Fonts: none` for system fonts. Without the line, Archivo loads.

## Shape and space

| Token | Default |
|---|---|
| `--radius` | `3px` (`999px` gives pill buttons) |
| `--wrap` / `--wrap-narrow` | `1240px` / `720px` |
| `--gutter` | fluid |
| `--section` | fluid vertical rhythm between modules |
| `--header-h` | `4.5rem` |

## The two demos

- **velvet-tides:** the dark default, tighter and uppercase, with outline gig dates.
- **hollin-wren:** daylight palette, Young Serif with Work Sans, pill buttons.

Same theme, same modules, same data shape.
