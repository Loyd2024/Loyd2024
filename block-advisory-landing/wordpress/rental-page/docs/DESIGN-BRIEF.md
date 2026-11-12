# Design brief: the block.ke rental page

Read `ROOT/PROJECT.md` and `ROOT/DECISIONS.md` first. This brief is for the design round: three independent concepts,
then a judged synthesis. `ROOT` = `/tmp/claude-0/-workspace-Loyd2024/e24ac003-f571-5336-9956-1fd11a814c7d/scratchpad/rp`.

## The job of the page

A person looking for a place to rent in Nairobi (or an office, shop or warehouse for their business) opens one
listing, most often on a phone, often from WhatsApp, Google or a portal, sometimes from abroad. Within one screen
they want to know: what the rent is and what it covers, whether it is still available, what it looks like, where it
is, and how to see it. Then they either message on WhatsApp, call, or ask for a viewing. The page succeeds when
that person contacts Block about this home, or moves on to a similar one on block.ke, and never feels sold to.

The owner's words: "a landing page for the rental units like the ones we have for developments; for the rentals it
should be different." So: same brand and the same level of finish as the development layout, but a visibly different
page with a different job. A visitor who has seen a development page must not think "the same template with the
words changed".

## What to read (in this order, selectively: the notes are long)

1. `ROOT/design/reference/TOKENS.md` and the screenshots in `ROOT/design/reference/` (the brand, the development
   page's anatomy, and what the classic rental page looks like today). Look at the PNGs.
2. `ROOT/notes/06-rental-ux.md`: section 0, then sections 7 to 13 (the first-screen ranking, page anatomy, patterns to
   borrow and avoid, forms, trust copy, accessibility, ten ideas for being different).
3. `ROOT/notes/03-data-audit.md`: section 0 and section 13 (what almost every listing has, what only some have, edge
   cases). The page must be designed for the usual case: a rent, photos, a long description, and few stated terms.
4. `ROOT/notes/02-rental-rules.md`: section 2 (rent wording truth table), sections 5 to 12 (approved wording), and
   sections 18 and 19 as amended by `ROOT/DECISIONS.md`.
5. `ROOT/notes/04-site-shell.md`: "In brief" and its recommendations (what surrounds the page, what bleeds in).
6. `ROOT/notes/01-dev-layout.md` section (h) for how the development CSS scopes and resets itself.

## The three listings every concept must be shown on

| Mock | Listing | Why |
|---|---|---|
| `home` | 45577, 3-bedroom apartment, Kings Courtyard, Kilimani: 19 photos, KES 135,000 a month with the service charge included, a sibling 2-bed in the same building (45538), full description with snapshot table, rental terms, tenant sections and FAQ | the rich case |
| `sparse` | 34171, Eden Heights, Westlands, 3-bed + DSQ: ONE photo, rent stated in US dollars in the text (price field holds a shilling figure), few stated terms | the thin case: 10 rentals have one photo, 25 have fewer than five |
| `commercial` | 36809, The Address, Westlands, offices: one square photo, "From KES 695,054 a month", a service charge row, several spaces in one listing, no bedrooms, no features | business occupier wording |

Real content only. Take each listing's text from its saved live page (`ROOT/ref/pages-stop/45577.html`,
`ROOT/ref/pages-final/34171.html`, `ROOT/ref/pages-final/36809.html`: the description panel) and its parsed fields
from `ROOT/notes/03-data-audit.json` (`listings[]`, by `id`). Photos are in `ROOT/design/assets/<id>/NN.jpg`
(1620px renditions; `ROOT/design/assets/manifest.json` has sizes and alt text, plus 12 thumbnails and fields of other
rentals under `cards` for "similar rentals"). Serve them in mocks as `/design/assets/...`. Never invent a fact, a
figure, an amenity or a testimonial; generic copy must obey PROJECT.md rules 11 to 15 and DECISIONS 21 to 24.

## What the page must contain (how is up to you)

- The rent as one honest sentence, in the approved wording, with what the listing says it includes (for example
  "service charge included") and "From" only when the listing has it.
- Availability and furnishing only when the listing states them (DECISIONS 11, 12).
- Photos: a gallery that works with 19 photos and with one; a full-screen viewer (`<dialog>`); slides in the HTML;
  no auto-rotation; thumbnails or a count rather than dots.
- Key facts: bedrooms, bathrooms, size (homes); size, rent basis, parking or power when stated (commercial).
- Two ways forward everywhere: **Arrange a viewing** (a short form: name, phone or WhatsApp; optional move-in choice
  and message) and **WhatsApp** (prefilled message, DECISIONS and notes/02 section 9), plus a phone number. The
  listing's agent with photo, name and role when there is one.
- "What you pay" / rental terms: only what the listing states; everything open goes into one short "To confirm when
  you enquire" list (DECISIONS 14).
- The description, readable and complete in the HTML (it is long: 600 to 1,500 words, with tables and lists). You may
  lift parts of it into designed sections (amenities, location, who it suits, FAQ) when the parser can find them, and
  must degrade to "the description as written" when it cannot.
- Features (the listing's feature terms) and amenities from the text.
- Location: area, address when present, nearby places from the text; the map pin is usually a neighbourhood point,
  so no "exact location" claims; a link to open the area in Google Maps is enough.
- "More to rent in this building" when other rentals share the Project term; "Similar rentals" (cards) and a link to
  all rentals in the area.
- "Questions tenants ask" from the listing's own FAQ.
- How viewing and renting with Block works, in a few promise-free steps; the approved tenant "Why Block" points and
  the rental disclaimer (notes/02 section 11). Write the commercial equivalents.
- A phone bottom bar (rent, WhatsApp, Arrange a viewing) printed outside the site wrapper, and whatever the desktop
  equivalent of "always one tap from contact" is in your concept.
- States: no price ("Rent on request"), per sq ft, per day, US dollars, "From", one photo, no photo, no agent, no
  features, no pin, a very long title, a let listing (DECISIONS 3), an enquiry sent (thank-you), a form error.

## What makes it different from the development page

The development page (see `TOKENS.md`, "Anatomy of the development page") is a sales brochure: split hero with a
price-list form, unit table with floor plans, payment plan or mortgage calculator, investor view, construction
progress. The rental page must not reuse that skeleton with new labels. It is about one home someone could move
into next month. Decide what that changes: scale, order, tone, photography, the contact model, the type.

## Constraints that bind every concept

- Brand: the tokens in `TOKENS.md` (colours, square or near-square corners as the brand does, calm type). Fonts: only
  those the site already loads, plus at most one display font (DECISIONS 28). No icon fonts of your own; inline SVG
  only, and few.
- Everything under one root `<div id="bkr" class="bkr">`; classes `bkr-*`; CSS scoped under `#bkr` with enough
  specificity to beat the theme and Block's custom CSS (DECISIONS 29, 30); no CSS frameworks, no libraries, no
  external requests except fonts. Shared CSS at most about 35 KB, shared JavaScript at most about 18 KB, both the
  SAME files for all three mocks (the page markup differs, not the code).
- Works without JavaScript (photos visible, text readable, phone and WhatsApp links work, the form is a real form).
  JavaScript enhances: viewer, sticky states, in-page scrolling, form sending, the thank-you state.
- Accessible: one H1, ordered headings, 44px targets, visible focus, `prefers-reduced-motion`, dialog focus
  handling, labels above fields, errors announced, colour contrast AA. No sideways scroll at 320, 390, 768, 1024,
  1200 and 1440px. No layout shift when the script loads.
- The shell: the site header is 56px and sticky below 1200px; 130px and not sticky from 1200px. The theme's own
  bottom bars are hidden on this page (hide them in your CSS: notes/04 section 1.9 and 5.2 name the selectors). Your
  phone bar and dialogs go in `foot.html`.

## How to build and show a mock

A mock is a directory under `ROOT/design/<your concept>/` with three fragments, printed where the real snippet will
print: `head.html` (one style element, id `bkr-css`, plus any font link), `page.html` (the `#bkr` root only),
`foot.html` (the phone bar, dialogs, and one script element). To keep one code base, write your CSS and JavaScript
once (`ROOT/design/<concept>/shared/bkr.css`, `bkr.js`) and generate the three mocks' `head.html` and `foot.html`
from them with a small script.

The local test site renders it inside the real site header, footer and CSS:
`http://127.0.0.1:8089/?rig_mock=<concept>/<mock>` (for example `?rig_mock=concept-a/home`). Take screenshots with
`ROOT/rig/bin/shot '<url path>' <out.png> [width] [full]` (it also reports sideways scroll and the fonts used); run
it with no arguments or read `ROOT/rig/bin/shot.js` for its options. If the site does not answer, wait a minute and
retry (another agent may be restarting it); `ROOT/rig/bin/start` starts it.

Look at your own screenshots (Read the PNG files) and iterate until you would sign your name under them. At least
two rounds of look-and-fix at 390px and at 1440px.

## What to hand in (per concept, under `ROOT/design/<concept>/`)

1. `spec.md`: the idea in three sentences; what is on the first screen at 390px and at 1440px; the section order
   for homes and for commercial; each component with its states and the data it needs; the complete copy deck (every
   generic string, home and commercial variants, the let state, the thank-you and error lines, the WhatsApp message);
   how each case in "States" above is handled; what you chose to leave out and why; how it differs from the
   development page; known weaknesses.
2. `home/`, `sparse/`, `commercial/` mocks as described, sharing one CSS and one JavaScript file.
3. `shots/`: for each mock, 390 first screen (390x844), 390 full page, 1440 first screen (1440x900), 1440 full
   page; plus, for `home`, the photo viewer open at 390 and at 1440, the viewing form with one field in error at 390,
   and the thank-you state; plus one 320px and one 768px full-page shot of `home`.
4. `sizes.txt`: byte sizes of the shared CSS and JavaScript.
