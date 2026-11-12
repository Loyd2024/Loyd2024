# Decisions after the mapping round (main session, 11 Nov 2026)

Read after PROJECT.md. Where a note under `ROOT/notes/` asks "the main session decides", the answer is here. These
override the notes; PROJECT.md still overrides everything.

## Facts read from the live site today

- Option `blockke_dev_page` holds: `area_pages`, `card_v16`, `dev_index`, `search_layout`, `view_transitions`,
  `prefetch` (all true), a `neighbours` map, `notify_emails` (four addresses), `rent_pages` true, `classic_inplace`
  `['rent']`. NOT set: `auto`, `auto_units`, `copy_agent`, `count_views`, `rent_why`, `rent_disclaimer`,
  `zoho_enquiry_field`, `landing_pages`, `lp_rent_guard`.
- `property_status` terms that exist: `complete`, `furnished`, `managed-by-block` (0 listings), `offplan-ongoing`,
  `ongoing`, `sold`. There is no `let` term yet; the CRM sync creates it (slug `let`) the first time Zoho marks a
  listing Let.
- `bke_rent_usd` is empty on 34171, `bke_dev_name` is empty on 42616, `property_label_before` is empty on 45577.
  Assume these custom fields are unset everywhere unless a listing proves otherwise; the code must still honour them.
- Listing pages are not cached at the edge (`cf-cache-status: DYNAMIC`).

## Scope and detection

1. A rental = published `estate_property` with the term `for-rent` and without `for-sale` (notes/02 C1: the term
   wins; rentals by title only are out of scope).
2. The automatic rule (`bke_rent_layout` empty) accepts a rental unless its title also offers a sale
   (case-insensitive: "for sale", "to buy", "buy or lease", "rent & sale", "rent and sale", "sale & rent", "sale and
   rent", "rent-to-own"). `on` always wins, `off` always wins (notes/02 C2).
3. A rental whose `property_status` has the term `let` or `sold` still gets the layout, in a "no longer available"
   state: a clear notice at the top, no viewing form, no "Check availability"; instead similar rentals and one
   enquiry "Tell me about similar homes". Nothing is hidden from search engines.
4. Kind: `commercial` when the title or categories say office, commercial, warehouse, godown, retail, shop, showroom,
   industrial or business park AND the title does not say bedroom, apartment, house, villa, studio, maisonette,
   townhouse or bungalow; otherwise `home`. Title words beat categories (37551 is apartments filed under Offices;
   offices 34906 and 36675 carry a bedroom count). Commercial pages never show bedrooms.

## The rent

5. The headline rent is the price field, always (notes/02 D6). Never a figure read from a table.
6. Wording follows the truth table in notes/02 section 2 with its single "From" rule (C3): "From" only when the
   listing's own before-price label says so. No "From" is added for unit tables or status.
7. US dollars: honour `bke_rent_usd` / `bke_rent_usd_date` when set; when they are not set and the listing's own
   rent row states a USD figure, follow the rows of the truth table for that case (notes/02 P27 to P30), including
   D8 (a rent row in a table that sits before any heading must be found).
8. No price, a "Rent on application" label, or a monthly figure over KES 2M with no label: "Rent on request".

## Terms and facts read from the text

9. Sections are found at ANY heading level, and a bold-only paragraph counts as a heading (D1). The description
   parser must be written for today's descriptions, not ported unchanged.
10. Service charge: only a row whose label is the service charge itself; reject labels that also say combined,
    total, rent or contain "+" (D2). When the rent row says the service charge is included or inclusive, show
    "Service charge: included in the rent" and never say it is still to be confirmed (D7).
11. Availability: never print a value that only restates the offer ("For rent", "To let"), that is conditional
    ("subject to", "contact", "please", "on request", "to be confirmed"), or that names a date, month or quarter
    already past, or a date without a year (D3). No "Availability: On request" cell (C10).
12. Furnishing: decided first from the `furnished` status term or a "Furnished" feature, then the title, then a row
    whose value is exactly a furnishing word. Never a sentence (D4).
13. Deposit and lease: shown only with a figure; up to 90 characters; one value is printed once even if it answers
    two labels (D5).
14. Everything the listing leaves open is gathered into ONE short list, "To confirm when you enquire" (deposit, lease
    length, service charge, what the rent includes, viewing times: only the items not already stated). No empty
    rows, no "TBC" cells.
15. Sizes: the size field is square feet. Homes show "1,550 sq ft (144 m²)"; commercial shows sq ft only, because
    commercial rents are quoted per sq ft (D10). No size field, no size.
16. Text clean-up before the description is printed: drop script elements and their text (17 descriptions hold JSON
    in one), drop a first paragraph that only repeats the meta description, and never print square-bracket
    placeholders such as "[Development Name]" (drop the sentence that holds one).

## Names, headings, links

17. H1 = the listing's own title with any trailing price or "from KES…" part removed ("4 Bedroom Villa for Rent in
    Runda"). It is what staff wrote and it is unique. The building name (the `bke_dev_name` field when set, else the
    Project term when it is a real name) is shown as a separate line near the H1, not instead of it. Forms, WhatsApp
    and emails use the same cleaned title.
18. A rental never takes its name, breadcrumb or "other homes" from a listing that is not itself a rental (D9).
    "More to rent in this building" lists other published rent-only listings that share the Project term.
19. "See all rentals in {area}" points to the area's rent page (`/property_area/{slug}/homes-for-rent/`, or
    `commercial-for-rent` for commercial) when the development snippet's area pages serve it (use its URL helper
    behind `function_exists`), else to the term archive `/property_area/{slug}/`.
20. In-page links are handled by script (`preventDefault` + `scrollIntoView`), because the theme clears the URL hash
    on every hashchange (notes/04 item 7). Nothing relies on `:target`. Without JavaScript the links still work as
    plain anchors.

## Copy

21. Dropped: "We usually reply within a few hours on working days." and "often before they are listed" (C7). No
    reply-time promise anywhere.
22. The tenant "Why Block" texts and the rental disclaimer approved in October (notes/02 section 11) are used word
    for word on home pages. Commercial pages need their own versions in the same spirit; the design round writes them.
23. A line about viewing fees ("Block does not charge a viewing fee") is a strong trust signal in Nairobi but is the
    owner's to confirm. It is an option string (`trust_line`), empty by default, shown near the viewing form only when
    the owner has filled it in. Do not design the page around it.
24. The viewing form never implies a booked slot or a calendar. Required: name and phone or WhatsApp. Optional: when
    they would like to move in (This month, 1–3 months, Later), and a message. International numbers are accepted.

## Shell facts that bind the design (notes/04)

25. Everything inside `#bkr` sits inside the site's `#all_wrapper` stacking context (z-index 1). A full-screen photo
    viewer and any dialog use `<dialog>` with `showModal()`. The phone bottom bar and the dialogs are printed at
    `wp_footer`, outside the wrapper.
26. Below 992px the theme shows its own agent bar at the bottom of every property page and the site pads the body
    for it; a site script adds a second bar between 992px and 1024px. The rental page hides both and owns the bottom
    of the screen on phones with one bar of its own (rent, WhatsApp, Arrange a viewing), no taller than 64px plus the
    safe-area inset, and it must not cover the focused field (WCAG 2.4.11).
27. The site header is a 56px sticky bar below 1200px and a 130px non-sticky header from 1200px. A sticky section
    bar on the rental page sits at top 56px below 1200px and top 0 from 1200px.
28. Fonts already on every page: Montserrat 300–700, Frank Ruhl Libre 300–900, Roboto, Roboto Slab. The development
    layout additionally loads Instrument Serif from Google Fonts. The rental page may use fonts the site already
    loads, plus at most ONE display font loaded the way the development snippet does it; decide in design.
29. Block's custom CSS and the theme style bare tags with `!important` (Montserrat on text, heading colours,
    14px paragraphs, list bullets removed, table borders, submit-button skin, no focus outline). Every rule of the
    rental page is scoped under `#bkr` with enough specificity to win, and restores what it needs (focus outlines,
    list markers where wanted, paragraph size).
30. Class and id names to avoid in the markup are listed in notes/04 item 12 (anything containing
    `property_description`, `feature_chapter`, `gmap`; `single-content`, `property_listing`, `lightbox_trigger`,
    Bootstrap component names and `data-bs-*`; and no `<header>` or `<footer>` element around call, WhatsApp or e-mail
    links).
31. The site's schema script writes a RealEstateListing into the head unless an element with id `bke-schema-ld`
    exists. Version 1 of the rental page prints its own `script#bke-schema-ld` (through `wp_print_inline_script_tag`)
    only if the engineers can make it exactly match the visible rent; otherwise it leaves structured data alone and
    says so in the README.

## Making the development snippet stand down

32. Use the six-step recipe in notes/01 section (b) (`blockke_rp_dev_stand_down( $id )` on the `wp` action), and
    repeat its cases on the rig with dev-v1.8.2 and dev-v1.9.1 enabled.
