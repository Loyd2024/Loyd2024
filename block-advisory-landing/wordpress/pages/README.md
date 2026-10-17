# Pages built outside the snippet

## `land-and-build.html` (draft, not on block.ke yet)

The land-and-build page: buy a plot in a gated community, then build. It is one `<!-- wp:html -->` block for a page under Block Advisory (`/block-advisory/land-and-build/`), on the *Elementor Full Width* template like the Construction page. Its CSS sits under `.bklb` and every id starts `lb-`.

- **Sections:** hero with two doors (*I need a plot* / *I already have a plot*) and a "Where are you now?" step tracker; *Two contracts, one team beside you*; the six steps from plot to keys; a budget splitter; six gated estates with a shortlist; eight land checks; *Already have a plot?*; building from abroad; links to the Block service pages; FAQs (mirrored in JSON-LD); the enquiry form; dated sources.
- **Enquiries** post to `/wp-json/block/v1/development-lead` with `topic: 'land-build'`, which needs v1.7 of `block-development-page.php` (config `lead_topics`). Before v1.7 is live, the form retries once as a new-developments enquiry, so no lead is lost.
- **Before publishing:** answer or remove the `<!-- OWNER: … -->` comments (the land-side service and fees, the disclosure line, build coverage outside Nairobi, design-committee work, plot-choosing help, photo rights, the privacy notice) and re-check the Ardhisasa line and every "as at" date.
