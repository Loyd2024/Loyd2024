# Pages built outside the snippet

## `land-and-build.html` (draft, not on block.ke yet)

The land-and-build page: buy a plot in a gated community, then build. It is one `<!-- wp:html -->` block for a page under Block Advisory (`/block-advisory/land-and-build/`), on the *Elementor Full Width* template like the Construction page. Its CSS sits under `.bklb` and every id starts `lb-`.

- **Sections:** hero with two doors (*I need a plot* / *I already have a plot*) and a "Where are you now?" step tracker; *Two contracts, one team beside you*; the six steps from plot to keys; a budget splitter; six gated estates with a shortlist; eight land checks; *Already have a plot?*; building from abroad; links to the Block service pages; FAQs (mirrored in JSON-LD); the enquiry form; dated sources.
- **Enquiries** post to `/wp-json/block/v1/development-lead` with `topic: 'land-build'`, which needs v1.7 of `block-development-page.php` (config `lead_topics`). Before v1.7 is live, the form retries once as a new-developments enquiry, so no lead is lost.
- **Owner answers applied (2 Nov 2026).** These come from the Set-up List, ticked 22 Oct.
  - **T009, items 1 and 3, "remember the construction pages in our website":** Block checks a plot before the buyer pays, including plots it does not list. It orders the official search through the buyer's advocate, brings in a licensed surveyor to confirm the beacons, and reads the estate's rules and the subdivision approval. Block is not the advocate or the surveyor. Block also helps choose a plot and draws up a shortlist. Where the plot work hands over to the build, the page links Block Construction (*Feasibility & cost planning*, `#bkc-services`) and describes it in that page's own words.
  - **T009, item 2: not answered.** This asked whether Investment Advisory's written findings cover plots. The page therefore names no written report for the plot check and no longer offers Investment Advisory's "homework" for plots.
  - **T010:** the disclosure line is approved as written: "On some estates the developer pays Block for introducing buyers. We tell you which, in writing, before you commit." It appears word for word in the Two contracts note, on the Tatu City card and in the FAQ. The design build's `static.py` fails if it goes missing.
  - **Still not answered:** how Block's land-side work is paid. The FAQ "Who pays Block?" states only the free first consultation, Block Construction's fee wording and the disclosure.
  - **Unchanged since 16 Oct:** the page's call and WhatsApp number is still +254 725 937 686. Other pages moved their call button to +254 184 027 349 on 22 Oct.
- **Before publishing:** settle the 20 remaining `<!-- OWNER: … -->` comments:
  - build coverage in Juja, Thika, Athi River, Nyeri and Kitale;
  - whether feasibility applies outside Nairobi;
  - design-committee work;
  - who builds VillaKazi's designs;
  - photo rights, image labels and the share image;
  - the privacy notice under the form;
  - how land-side work is paid;
  - the listing fixes and prices on the six estate cards;
  - PLUPA s.64(1) and the dated sources.

  Also re-check the Ardhisasa line and every "as at" date.
- **Size:** 151,421 bytes (md5 1c8073183bc3b4e1da50242c0221fdad).
