# Rollback

1. Keep a full database and `wp-content` backup before activating the package.
2. Keep the previous theme and plugin zip files available.
3. If activation causes an issue:
   - Switch back to the previous theme in Appearance -> Themes.
   - Deactivate Prismpath plugins.
   - Restore permalink settings if needed.
4. If admin access is unavailable:
   - Rename `wp-content/themes/prismpath-health-theme`.
   - Rename the Prismpath plugin folders.
   - WordPress will fall back to another installed theme/plugin state.
5. Restore the database backup if redirect rules, settings, or seeded pages need to be reverted.
