# Content Inventory

All public-facing brand content is rebranded to Prismpath Health. Prismpath Wellness should not appear in templates, plugins, metadata, schema, or documentation. LBee should appear only as the legal entity disclosure: Lbee Health Practive Group PLLC dba Prismpath Health.

## Required Pages

- Home -> `front-page.php`
- Services -> `page-services.php`
- Therapy -> `page-therapy.php`
- Psychiatry -> `page-psychiatry.php`
- ADHD & Autism Assessments -> `page-adhd-autism-assessments.php`
- Occupational Therapy -> `page-occupational-therapy.php`
- Whole Family Mental Health -> `page-whole-family-mental-health.php`
- Approach -> `page-approach.php`
- About Us -> `page-about.php`
- Insurance & Payment -> `page-insurance-payment.php`
- Team -> `page-team.php`
- Contact -> `page-contact.php`
- Group Support -> `page-group-support.php`
- Referral Partners -> `page-referral-partners.php`
- Accommodations -> `page-accommodations.php`
- Team bios -> `single-team_member.php` with `/bio/{slug}` URLs
- Privacy Policy -> generated website inquiry privacy content, pending final legal review before launch
- HIPAA Policy -> generated HIPAA notice-style policy content, pending final legal review before launch
- Accessibility Statement -> generated page content

## Positioning

Prismpath Health offers adult neuroaffirming therapy, psychiatric care, occupational therapy, ADHD & Autism assessments, and caregiver-centered Whole Family Mental Health.

Legal-facing content should use the wording "Lbee Health Practive Group PLLC dba Prismpath Health."

Whole Family Mental Health is framed as family-systems support: parent/caregiver guidance, communication, co-regulation, routines, and coordinated care. Pediatric ABA, speech, and occupational therapy services should be routed to Chroma Early Start.

Insurance and payment content carries forward LBee Health's listed plan coverage: Optum, Aetna, AvMed, Oscar, Blue Cross Blue Shield, Employers Health Network, Medicare, Cigna, and Humana. Public copy should state that benefits are verified before care and that coverage, deductible, copay, coinsurance, prior authorization, provider, state, service, and member-plan rules may affect patient responsibility.

## Content Guardrails

- Do not promise diagnoses, outcomes, coverage, or availability.
- Insurance pages may list accepted plans, but must not guarantee individual coverage before benefits verification.
- Do not collect detailed symptoms or clinical history through the basic consult form.
- Do not use childcare, bee, or LBee brand language.
- Chroma Early Start may be referenced only for pediatric therapy pathways.
- Default WordPress starter content, including Sample Page, must not be public.
- Team profiles are seeded from the migrated legacy bio templates and should be reviewed before final launch if credentials or availability change.
- Use "where provider licensure allows" for virtual care claims.
- FAQ content should be visible on the page before FAQ schema is emitted.
- Do not publish blog/resource pages unless requested.
- Page-level SEO and service-template copy can be adjusted from the Prismpath SEO & Template Content metabox. `inc/seeder.php` fills those fields from the seeded content model for service-style pages and leaves later editor changes intact.
- Agent API schema writes should use the dedicated schema endpoints; the SEO plugin will render approved Agent-managed schema overrides when enabled.
