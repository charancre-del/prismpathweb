# Prismpath Health WordPress Package

Production package for the rebrand from LBee Health to Prismpath Health.

## Included

- `prismpath-health-theme/` - custom WordPress theme.
- `plugins/prismpath-seo-engine/` - SEO, schema, sitemap, robots, and monthly sitemap ping.
- `plugins/prismpath-lead-log/` - private lead-log CPT.
- `plugins/prismpath-consult-form/` - secure consultation form shortcode.
- `docs/` - install, redirects, content inventory, launch QA, and rollback notes.
- `dist/` - generated installable zip files after packaging.

## Build And Package

```powershell
cd "C:\Users\chara\Documents\New project 2"
Compress-Archive -Path .\prismpath-health-theme\* -DestinationPath .\dist\prismpath-health-theme.zip -Force
Compress-Archive -Path .\plugins\prismpath-seo-engine\* -DestinationPath .\dist\prismpath-seo-engine.zip -Force
Compress-Archive -Path .\plugins\prismpath-lead-log\* -DestinationPath .\dist\prismpath-lead-log.zip -Force
Compress-Archive -Path .\plugins\prismpath-consult-form\* -DestinationPath .\dist\prismpath-consult-form.zip -Force
Compress-Archive -Path .\docs -DestinationPath .\dist\prismpath-production-docs.zip -Force
```

## Verification

Run PHP lint across the theme and plugins, run the theme CSS verifier, then activate on staging and complete `docs/LAUNCH-QA.md`.
