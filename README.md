# Prismpath Health WordPress Source

Production WordPress source for the rebrand from LBee Health to Prismpath Health.

## Included

- `prismpath-health-theme/` - custom WordPress theme.
- `plugins/prismpath-seo-engine/` - SEO, schema, sitemap, robots, and monthly sitemap ping.
- `plugins/prismpath-lead-log/` - private lead-log CPT.
- `plugins/prismpath-consult-form/` - secure consultation form shortcode.
- `plugins/chroma-agent-api/` - API-key protected automation layer from the Wptstchroma repo.
- `docs/` - install, redirects, content inventory, launch QA, and rollback notes.

## Git Sync To WordPress

Sync these source folders into the matching WordPress locations:

- `prismpath-health-theme/` -> `wp-content/themes/prismpath-health-theme/`
- `plugins/prismpath-seo-engine/` -> `wp-content/plugins/prismpath-seo-engine/`
- `plugins/prismpath-lead-log/` -> `wp-content/plugins/prismpath-lead-log/`
- `plugins/prismpath-consult-form/` -> `wp-content/plugins/prismpath-consult-form/`
- `plugins/chroma-agent-api/` -> `wp-content/plugins/chroma-agent-api/`

Generated ZIP files are intentionally not tracked. The repository is ready for direct git/file sync into WordPress.

## Verification

Run PHP lint across the theme and plugins, run the theme CSS verifier, sync the folders to staging, then activate and complete `docs/LAUNCH-QA.md`.
