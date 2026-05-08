# Content Migration Runbook

Run this only on staging after a full database backup.

## Brand Search/Replace

Use WP-CLI to replace legacy brand language in posts, pages, menus, widgets, and common metadata tables.

```bash
wp search-replace "Prismpath Wellness" "Prismpath Health" --all-tables-with-prefix --precise --report-changed-only
wp search-replace "LBee Health" "Prismpath Health" --all-tables-with-prefix --precise --report-changed-only
wp search-replace "LBee" "Prismpath" --all-tables-with-prefix --precise --report-changed-only
```

Do not replace email addresses or domains until the final Prismpath Health domain/contact details are confirmed.

## Slugs And Pages

Confirm these new pages exist:

- `/services/`
- `/therapy/`
- `/psychiatry/`
- `/adhd-autism-assessments/`
- `/occupational-therapy/`
- `/whole-family-mental-health/`
- `/approach/`
- `/team/`
- `/contact/`
- `/group-support/`
- `/referral-partners/`
- `/accommodations/`

## Redirects

Import or verify every redirect in `REDIRECTS.csv`. The theme includes these redirects as a safety net, but production hosting or a redirect plugin can also own them.

## Final Audit

```bash
wp db search "LBee"
wp db search "Prismpath Wellness"
wp db search "lbeehealth.com"
```

Expected result: only intentional legacy redirect/source notes or old contact details that are still approved for temporary use.
