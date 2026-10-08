"""Builds the release zips for a GitHub release.

    dist/surface-theme.zip   the theme, under a surface-theme/ top folder
    dist/surface-bundle.zip  surface-theme.zip + drift-surface.zip + README.txt,
                            for installing the pair by hand

Entries use forward slashes, so WordPress unpacks them correctly on any host
(PowerShell 5.1's Compress-Archive writes backslashes, which break on Linux).

drift-surface.zip is taken from --plugin-zip, or downloaded from the latest
Drift: Surface release.

Usage, from the theme folder:
    python tools/build-release.py
    python tools/build-release.py --plugin-zip ../../plugins/drift-surface/dist/drift-surface.zip
"""

import argparse
import os
import re
import urllib.request
import zipfile

SLUG = "surface-theme"
PLUGIN_ZIP_URL = "https://github.com/drift-creative-systems/drift-surface/releases/latest/download/drift-surface.zip"
ROOT = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
EXCLUDE_DIRS = {".git", "tests", "tools", "dist", "node_modules", ".idea", ".vscode"}
EXCLUDE_FILES = {".DS_Store", "Thumbs.db", "CLAUDE.md"}  # CLAUDE.md is local-only (global gitignore)

BUNDLE_README = """Drift: Surface - Surface theme {theme} + Drift: Surface plugin {plugin}

The theme and plugin only work as a pair. Without the plugin, the site shows
a "coming soon" page; without the theme, the plugin's Setup Wizard is off.

Install (either order works):
1. Plugins > Add New > Upload Plugin > drift-surface.zip > Install > Activate.
2. Appearance > Themes > Add New > Upload Theme > surface-theme.zip > Install > Activate.
3. Drift: Surface > Connection: add the band's hub address, then run the Setup Wizard.

Both update themselves from GitHub after that:
https://github.com/drift-creative-systems/surface-theme
https://github.com/drift-creative-systems/drift-surface
"""


def header_version(text, pattern):
    match = re.search(pattern, text, re.MULTILINE)
    return match.group(1).strip() if match else "?"


def build_theme(out_dir):
    out = os.path.join(out_dir, SLUG + ".zip")
    count = 0
    with zipfile.ZipFile(out, "w", zipfile.ZIP_DEFLATED) as zf:
        for dirpath, dirnames, filenames in os.walk(ROOT):
            # Only prune at the top level; lib/ may contain its own "tests" etc.
            if os.path.relpath(dirpath, ROOT) == ".":
                dirnames[:] = [d for d in dirnames if d not in EXCLUDE_DIRS]
            dirnames.sort()
            for name in sorted(filenames):
                if name in EXCLUDE_FILES or name.endswith((".log", ".bak")):
                    continue
                full = os.path.join(dirpath, name)
                zf.write(full, SLUG + "/" + os.path.relpath(full, ROOT).replace(os.sep, "/"))
                count += 1
    print(f"{out} ({count} files, {os.path.getsize(out) // 1024} KB)")
    return out


def plugin_zip_bytes(path):
    if path:
        with open(path, "rb") as fh:
            return fh.read()
    print(f"Downloading {PLUGIN_ZIP_URL}")
    with urllib.request.urlopen(PLUGIN_ZIP_URL, timeout=60) as resp:
        return resp.read()


def main():
    parser = argparse.ArgumentParser()
    parser.add_argument("--plugin-zip", help="local drift-surface.zip (default: latest GitHub release)")
    args = parser.parse_args()

    out_dir = os.path.join(ROOT, "dist")
    os.makedirs(out_dir, exist_ok=True)
    theme_zip = build_theme(out_dir)

    plugin = plugin_zip_bytes(args.plugin_zip)
    plugin_tmp = os.path.join(out_dir, "drift-surface.zip")
    with open(plugin_tmp, "wb") as fh:
        fh.write(plugin)
    with zipfile.ZipFile(plugin_tmp) as zf:
        # Fail loudly rather than bundle something WordPress can't install.
        if "drift-surface/drift-surface.php" not in zf.namelist():
            raise SystemExit("drift-surface.zip has no drift-surface/drift-surface.php - wrong file?")
        plugin_version = header_version(zf.read("drift-surface/drift-surface.php").decode("utf-8"), r"^\s*\*\s*Version:\s*(.+)$")

    with open(os.path.join(ROOT, "style.css"), encoding="utf-8") as fh:
        theme_version = header_version(fh.read(), r"^Version:\s*(.+)$")

    bundle = os.path.join(out_dir, "surface-bundle.zip")
    with zipfile.ZipFile(bundle, "w", zipfile.ZIP_DEFLATED) as zf:
        zf.write(theme_zip, "surface-theme.zip")
        zf.write(plugin_tmp, "drift-surface.zip")
        zf.writestr("README.txt", BUNDLE_README.format(theme=theme_version, plugin=plugin_version))
    os.remove(plugin_tmp)
    print(f"{bundle} (theme {theme_version}, plugin {plugin_version}, {os.path.getsize(bundle) // 1024} KB)")


if __name__ == "__main__":
    main()
