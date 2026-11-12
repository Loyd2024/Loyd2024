# Block reference sheet: brand tokens, the development page's anatomy, visual character

Captured from the live site on 11 Nov 2026, 18:26 to 18:41 UTC, read-only. This sheet describes what EXISTS. The rental
page keeps the brand (sections 1 to 11) and must not repeat the page in section 13.

Short names used below:

- `REF` = this folder, `ROOT/design/reference/`
- `DEV` = `ROOT/ref/dev-v1.9.1.php` (every `DEV:9237` is a line number in that file)
- "1440" = desktop window 1440x900, "390" = phone 390x844, both at device scale factor 1

## 0. What is in this folder, and five caveats

| File | What it shows |
|---|---|
| `dev-sale-santorini_desktop-1440x900_first-screen.png` | Development layout, for sale: Santorini Residences (listing 40401), first screen, 1440x900 |
| `dev-sale-santorini_desktop-1440_full-page.png` | Same page, whole page, 1440 x 14,913 |
| `dev-sale-santorini_phone-390x844_first-screen.png` | Same page, first screen, 390x844 |
| `dev-sale-santorini_phone-390_full-page.png` | Same page, whole page, 390 x 18,990 |
| `classic-rental-kilimani-3bed_desktop-1440x900_first-screen.png` | Classic rental page: 3 Bedroom Apartment for Rent in Kilimani, Lenana Road (listing 45577), first screen |
| `classic-rental-kilimani-3bed_desktop-1440_full-page.png` | Same page, whole page, 1440 x 7,050 |
| `classic-rental-kilimani-3bed_phone-390x844_first-screen.png` | Same page, first screen, 390x844 |
| `classic-rental-kilimani-3bed_phone-390_full-page.png` | Same page, whole page, 398 x 12,892 (the page is 8 px wider than the screen, see 16.5) |
| `home_desktop-1440x900_first-screen.png`, `home_phone-390x844_first-screen.png` | Homepage, first screen |
| `area-kilimani_desktop-1440x900_first-screen.png`, `area-kilimani_phone-390x844_first-screen.png` | `/property_area/kilimani/`, first screen |
| `dev-layout-page.html` | The Santorini page's HTML as the server sent it (curl). `<link id="bkd-font">` is line 899, `<style id="bkd-css">` starts at line 900, `<style id="bkn-dev-css">` is line 1494, `<div id="bkd">` opens on line 4644 (hero to overview) and continues on line 4894 (gallery to the overlays), config and script are lines 6856 to 6857 |
| `slices/` | The four whole pages cut for reading: desktop in 1,800 px slices (`..._slice-01_y00000.png`), phone as sheets of four 1,500 px columns read left to right (`..._sheet-01_y00000-to-y06000.png`) |
| `sections/` | One crop per section of each whole page, numbered in page order (`dev-sale-santorini_desktop-1440_09-payment.png`) |
| `states/` | The development page's own overlays opened with their own controls and closed again, at both sizes: enquiry dialog, gallery viewer, search sheet, the full description opened, one FAQ answer opened. Nothing was typed or sent |
| `with-widgets/` | A scrolled view of both listing pages at both sizes: the sticky and floating pieces where they sit while a visitor reads |
| `_work/` | How it was made: `capture.js`, `probe.js`, `stitch.py`, `styles_table.py`; per-page reports in `_work/reports/*.json` (computed styles, section boxes, fixed elements, hosts that failed); the computed styles as one line per element in `_work/styles_*.txt`; the CSS and script taken out of the files (`dev-css-v191.css`, `dev-css-live-v182.css`, `live-bkn-dev-css.css`, `live-bkn-vt.css`, `theme-custom-css.css`, `dev-js-v182.txt`); the Santorini page's DOM after its scripts ran (`dev-layout-page_rendered-dom-after-scripts.html`) |

Caveats:

1. **The cookie banner and the chat bubble are in no screenshot.** This session's network policy refuses the hosts
   they load from (`cdn.pagesense.io` for the Zoho PageSense cookie banner `#zcb-banner`, `salesiq.zohopublic.com` for
   the Zoho SalesIQ chat bubble `#zsiq_float`). Also refused: `maps.google.com` and `maps-api-ssl.google.com` (every
   map is an empty panel), `www.googletagmanager.com`, `connect.facebook.net`, `static.cloudflareinsights.com`
   (`_work/reports/*.json`, key `failed`). So there was nothing to close, and the "as it arrives" shots were
   byte-identical to the clean ones and were removed. What the page source says about the two missing widgets: the
   banner is a bar in navy `#0d2440` with a 2 px gold `#b98a44` top border, a gold Accept button with navy text, an
   outlined Decline button, 6 px button corners and a shadow that points up, which suggests a bar along the bottom of
   the screen (not seen; `REF/dev-layout-page.html:803-826`, the snippet "Cookie banner brand colours"); the
   development CSS lifts the chat bubble to 84 px above the bottom when its own phone bar is showing and hides it
   while a dialog is open (`DEV:9601-9602`).
2. **Whole-page images hide the fixed bars on phones**, because a fixed bar is drawn once, over the first screen's
   content. They are in the first-screen and `with-widgets/` shots. Hidden: the theme's agent bar, back-to-top button,
   compare drawer and the layout's own phone bar (`_work/reports/*_full.json`, key `hiddenFixed`).
3. **The live development snippet is v1.8.2, and its CSS is the same as v1.9.1's.** The live `<style id="bkd-css">` is
   `blockke_dev_css()` of v1.9.1 line for line (0 lines differ), followed by the font rule, `blockke_dev_calc_css()`
   and `blockke_dev_home_css()`. So the line numbers below, all in v1.9.1, describe the live page.
4. **Santorini uses the photo-first hero.** The live page carries `class="bkd-hero bkd-hero-vt"`: the photo is 3:2 and
   comes first on phones, and on desktop two more photos sit under it. Those rules are in `blockke_bkn_dev_css()`
   (`DEV:17579`), printed as `<style id="bkn-dev-css">`, not in `blockke_dev_css()`.
5. **A 1440x900 window is a "short desktop".** The rule at `DEV:9304` (`min-width:1000px` and `max-height:940px`)
   makes the H1 60 px instead of 78 px, tightens the hero and hides the quick form's second line. A taller window shows
   the larger hero.

## 1. Colour

All tokens are custom properties on `#bkd` (`DEV:9237`). "Uses" is how many times the CSS calls the token.

| Token | Hex | Name | Role | Uses |
|---|---|---|---|---|
| `--navy` | `#0D2440` | navy | The brand colour: every heading, primary buttons, dark bands and panels, text typed into fields, the focus outline | 88 |
| `--navy-2` | `#16345C` | second navy | End of the gradient on the "no photo" tile; behind amenity photos while they load | 2 |
| `--ink` | `#22303C` | ink | Body text on paper (the root colour), list rows | 7 |
| `--slate` | `#46586A` | slate | Secondary text: `.bkd-body` paragraphs, the hero tagline, form labels, table cells, breadcrumb links | 23 |
| `--muted` | `#5F6D7E` | muted | Smallest text: fact labels, table headers, fine print, consent, disclaimer | 16 |
| `--paper` | `#FBF9F6` | paper | The page background, a warm off-white; also the sticky section nav at 97% | 3 |
| `--mist` | `#F4EEE4` | sand | The alternate band (`.bkd-sec-mist`), the box behind a loading photo, note boxes | 7 |
| `--line` | `#E6DFD4` | hairline | 1 px rules between rows and around cards on paper and sand | 30 |
| `--line-2` | `#D5CBBB` | strong hairline | Borders of inputs, chips and outline buttons; the underline of text links | 18 |
| `--brass` | `#B98A44` | brass | The accent: the short rule before a label, the dot in the status chip, the primary button on navy | 10 |
| `--brass-d` | `#80602A` | dark brass | Brass for small text on light: section labels, tick icons, step numbers, the FAQ plus | 11 |
| `--brass-t` | `#9E7433` | text brass | The italic words inside H1 and H2 on light backgrounds | 1 |
| `--brass-l` | `#DDB97F` | light brass | Brass on navy: labels, icons, italic heading words, the 01 02 03 numerals, the focus outline on navy | 16 |
| `--brass-tint` | `#F1E4CC` | brass tint | Background of the status chip | 1 |
| `--on-navy` | `#C9D3DF` | on navy | Body text on navy | 9 |
| `--navy-line` | `rgba(255,255,255,.14)` | navy hairline | 1 px rules on navy | 9 |
| `--wa` | `#25D366` | WhatsApp green | Declared but unused; the WhatsApp icon is drawn in `#1DA851` (`DEV:9255`) | 0 |
| `--err` | `#B3261E` | error | Error text and the border of a field in error | 3 |

Colours written out instead of a token: `#fff` (cards, panels, inputs; 52 uses), `#7A5A22` (status chip text),
`#98A3B0` (placeholder), `#FBECEA` (alert background), `#FF8A7A` and `#FFB4A9` (error border and text on navy),
`rgba(8,18,32,.97)` (gallery viewer), `rgba(13,36,64,.5)` (the scrim behind a dialog), navy gradients from 0 to 72% or
86% under photo captions, and white at 90, 84, 70, 68 and 66% for text on navy. The calculators carry their own small
set (`DEV:1443`): `--c-muted #66768A`, `--c-line #D3D9E2` (replaced by `--line-2` inside the layout, `DEV:9519`),
`--c-mist #F5F6F8`.

**Bands.** A page alternates three surfaces: paper, sand, navy. On Santorini, top to bottom: paper (hero), navy (facts
band), paper (overview, gallery, progress), sand (residences), navy (amenities), sand (payment), paper (who it suits,
location), navy (why Block), sand (also consider), paper (FAQ), sand (enquire), then the site's navy footer.

**Contrast** (WCAG ratio; 4.5 passes for text, 3 for large text and for control outlines):

| Pair | Ratio | |
|---|---|---|
| navy on paper / sand / white | 14.9 / 13.5 / 15.6 | passes |
| ink on paper | 12.8 | passes |
| slate on paper / sand | 7.0 / 6.4 | passes |
| muted on paper / sand | 5.0 / 4.6 | passes, with little to spare on sand |
| dark brass (labels) on paper / sand | 5.5 / 5.0 | passes |
| text brass (italic heading words) on paper / sand | 4.0 / 3.6 | large text only; fine at heading sizes, not for small text |
| brass on paper / white | 2.95 / 3.1 | decoration only: never text on a light background |
| navy on brass (the button on navy) | 5.05 | passes |
| light brass on navy, on-navy on navy | 8.4, 10.3 | passes |
| white at 68% on navy (tile text) | 7.9 | passes |
| chip text `#7A5A22` on brass tint | 5.0 | passes |
| placeholder `#98A3B0` on white | 2.6 | fails |
| field border `--line-2` on white | 1.6 | fails the 3:1 guideline for control outlines |

**Colours of the site around the layout** (seen, not tokens of the layout): the top strip, the footer and the header
buttons are the same navy `#0D2440`; the theme's own text is `#1A1A1A` and its options still name `#002147` for text,
links and headings (`ROOT/ref/theme-options.json`, `wp_estate_font_color`, `wp_estate_headings_color`), which shows on a
few classic-page elements; on phones the "Advanced Search" bar and the three buttons of the agent bar are pure black
`#000000`; status tags on classic photos are black at 85%; the footer's column headings are a lighter gold (about
`#C99C55`, sampled) and its "Get a Consultation" button is brass `#B98A44`; the classic page's WhatsApp button is
`#25D366`.

## 2. Type

**Families**

- Display: `"Instrument Serif", Georgia, "Times New Roman", serif`, weight 400 only, roman and italic. Loaded by the
  layout itself from Google Fonts: `css2?family=Instrument+Serif:ital@0;1&display=swap` (`blockke_dev_font_head()`,
  `DEV:1635-1645`; the font name is the option key `display_font`, `DEV:319`; `REF/dev-layout-page.html:899`). Used for
  the H1, every H2, fact values, the quick form's title, amenity tile titles, captions on amenity photos, the 01 02 03
  numerals, the calculator's result, the thank-you heading, dialog titles and the "no photo" tile.
- Text: `"Montserrat", system-ui, -apple-system, "Segoe UI", Roboto, sans-serif`. The layout does not load Montserrat;
  the theme does (faces loaded on the page: 300, 400, 500, 600, 700). The layout uses 300 (lead paragraph, tagline),
  400 (body), 500 (H3, list rows, chips, nav links), 600 (labels, buttons, links, prices). It never uses 700.
- Italic is the accent: an `<em>` inside an H1 or H2 turns into the serif italic in brass (`--brass-t` on light,
  `--brass-l` on navy; `DEV:9260`, `DEV:9406`). Every section's H2 has one.

**Roles** (CSS, then the size the browser computed at 1440 and at 390; `_work/styles_dev-sale-santorini_*.txt`)

| Role | Selector | CSS | 1440 | 390 |
|---|---|---|---|---|
| H1 | `.bkd-h1` | Instrument Serif 400, `clamp(46px,5.4vw,78px)`, line-height 1.02, letter-spacing -0.01em, navy (`DEV:9257-9258`) | 60.5 px (78 px in a window taller than 940 px) | 46 px |
| H2 | `.bkd-h2` | Instrument Serif 400, `clamp(36px,4vw,56px)`, 1.06, -0.01em (`DEV:9259`) | 56 px | 36 px |
| H2 in a dialog | `.bkd-md .bkd-h2` | `clamp(32px,3.4vw,40px)` (`DEV:9598`) | 40 px | 32 px |
| Serif sub-heading | `.bkd-ess-h` | `clamp(30px,3vw,42px)`, 1.08 (`DEV:9502`) | 42 px | 30 px |
| H3 | `.bkd-h3` | Montserrat 500, 18 px, 1.35, -0.01em (`DEV:9261`); 16 px in the payment steps, 17 px on cards | 18 / 17 / 16 px | same |
| Section label | `.bkd-label` | Montserrat 600, 11 px, letter-spacing 0.22em, capitals, dark brass, after a 28 x 1 px brass rule, 14 px gap, 20 px below (`DEV:9262-9263`) | 11 px | 11 px |
| Fact label, table header | `.bkd-hfacts dt`, `.bkd-facts dt`, `.bkd-tbl th` | Montserrat 600, 10 px, 0.18em, capitals, muted (light brass on navy) (`DEV:9311`, `9397`, `9468`) | 10 px | 10 px |
| Form label | `.bkd-f>span` | Montserrat 600, 11 px, 0.12em, capitals, slate (`DEV:9338`) | 11 px | 11 px |
| Lead paragraph | `.bkd-lead` | Montserrat 300, `clamp(18px,1.6vw,21px)`, 1.65, ink (`DEV:9264`) | 21 px | 18 px |
| Hero tagline | `.bkd-hero-tag` | Montserrat 300, `clamp(17px,1.5vw,19px)`, 1.65, slate, at most 480 px wide (`DEV:9301`) | 17 px (19 px in a window taller than 940 px) | 17 px |
| Body | `#bkd` | Montserrat 400, 16 px, 1.75, ink (`DEV:9238`) | list rows 16 px; paragraphs 14 px (see the note) | same |
| Fine print | `.bkd-fine`, `.bkd-consent`, `.bkd-disc` | 12 px, 1.55 to 1.7, muted (`DEV:9268`, `9349`, `9582`) | 12 px | 12 px |
| Fact value | `.bkd-hfacts dd` | Instrument Serif, `clamp(24px,2.2vw,30px)`, 1.2, navy, one line with an ellipsis (`DEV:9312`) | 30 px | 24 px |
| Band value | `.bkd-facts dd` | Instrument Serif, `clamp(23px,2.2vw,30px)`, 1.15, white (`DEV:9398`) | 30 px | 23 px |
| Tile title | `.bkd-amt-i h3` | Instrument Serif, `clamp(25px,2.3vw,32px)`, 1.1 (`DEV:9496`) | 32 px | 25 px |
| Numeral | `.bkd-why b` | Instrument Serif italic, 46 px, light brass (`DEV:9543`) | 46 px | 46 px |
| Calculator result | `#bkd-calc .bkd-co-main strong` | Instrument Serif, `clamp(40px,4.4vw,54px)` (`DEV:9520`) | 54 px | 40 px |
| Button | `.bkd-btn` | Montserrat 600, 12.5 px, 1.2, 0.12em, capitals (`DEV:9270`) | 12.5 px | 12.5 px |
| Text link | `.bkd-link` | Montserrat 600, 14 px, 1.4 (`DEV:9281`) | 14 px | 14 px |
| Price in the table | `.bkd-tbl td.bkd-p` | Montserrat 600, 15 px, navy (`DEV:9472`) | 15 px | 15 px |

**Note: the theme's text rules leak in.** The layout resets margins but not sizes, so the theme's `p` rule wins
wherever the layout sets no size: `.bkd-body` paragraphs, the text under headings and FAQ answers compute to
**14 px / 1.65**, while list rows next to them compute to 16 px and the lead to 21 px. The same way, a list row with no
colour of its own (`.bkd-steps li`, `.bkd-places li`) computes to the theme's `#1A1A1A`, not ink. A new layout should
set size, line height and colour on its own `p` and `li`.

Numbers are Montserrat's default figures; only the newer listing cards ask for tabular figures (`DEV:13494`).
The serif carries the headline numbers ("KES 9.2M", "Dec 2029", "20%", "KES 198,919"); prices in the table are sans 600.

**The other pages, for comparison** (`_work/styles_*.txt`): the classic listing page sets its title in Montserrat 700
at 46 px (phone 30 px), the rent in Montserrat 700 at 34 px with "PER MONTH" in 11 px capitals, panel headings in
Montserrat 700 at 24 px, description text at 16.5 px / 1.75 in `#2A2A2A`. The homepage H1 is Instrument Serif at 86 px
(phone 40 px), its H2 52 px (phone 34 px). The area page H1 is Instrument Serif at 64 px (phone 40 px), H2 40 px
(phone 30 px), and its card names are Instrument Serif at 26 px.

## 3. Layout and spacing

- **Full bleed.** `#bkd` breaks out of the theme's container: `width: var(--bkd-vw,100vw)`, `margin-left: calc(50% -
  var(--bkd-vw,100vw) / 2)`, and `body.bkd-page{overflow-x:clip}` (`DEV:9238`, `9240`). The script sets `--bkd-vw` to
  the window's width without the scrollbar, and `--top` to the height of any bar fixed to the top, which the sticky
  pieces add to their offset (`REF/_work/dev-js-v182.txt:14-33`).
- **Container.** `.bkd-wrap`: at most 1,200 px of content plus the gutters; gutter 20 px below 768 px, 40 px from 768 px
  (`DEV:9237`, `9239`, `9251`). At 1440 the content is 1,200 px wide with 120 px either side; at 390 it is 350 px.
- **Section rhythm.** `--sec: clamp(80px,10vw,150px)` above and below every section (`DEV:9237`, `9401`): 144 px at
  1440, 80 px at 390. `.bkd-sec-tight` drops the top padding (gallery, progress). The hero has `clamp(24px,3.4vw,52px)`
  above and `clamp(64px,8vw,104px)` below (`DEV:9285`). A section's heading block has `clamp(40px,5.6vw,68px)` below it
  (`DEV:9413`).
- **Two columns.** `.bkd-split` is 5fr and 6fr with an 80 px gap from 1,000 px (509 + 80 + 611 at 1440), one column
  with a 40 px gap below; the left column, which holds the label and the H2, can stick at `--top` + 112 px
  (`DEV:9411-9412`). The live hero is 5fr and 7fr with a 56 px gap (477 + 56 + 667) and its photo column sticks at
  `--top` + 24 px (`DEV:17579`).
- **Measure.** Text is capped: 480 px (tagline), 540 px (text under a heading), 620 px (intro on navy), 640 px (FAQ
  answer), 920 px (disclaimer), 58 to 60ch elsewhere.
- **Spacing values.** There is no strict 4 or 8 px grid. The values the CSS uses most for padding, margin and gap, with
  their counts: 16 (37), 14 (27), 22 (25), 24 (23), 10 (22), 12 (18), 20 (15), 18 (15), 6 (15), 8 (13), 40 (11), 28 (10),
  4 (10), 26 (8), 30 (5), 32 (5), 36 (4), 80 (3). A working scale that covers them: 4, 6, 8, 10, 12, 14, 16, 18, 20, 22,
  24, 28, 32, 36, 40, 48, 56, 64, 80, 104, 144.
- **Inside components.** Rows in a list: 16 px above and below (15 px on navy). Table rows: 24 px. Paragraphs in a
  flow: 22 px apart. Quick form: `clamp(22px,2.4vw,30px)` all round. White panel and form card: `clamp(24px,4vw,48px)`.
  Amenity tile: 24 x 20 px, then 30 x 26 px from 640 px, then 40 x 34 px from 1,000 px. Investor box:
  `clamp(24px,3.4vw,36px)`. Note box: 18 x 20 px. Form: 16 px between fields (22 px in the form card), 6 px between a
  label and its field, 14 px between two fields side by side, 8 px between chips. Gallery: 12 px gaps, 16 px from 900 px.
  Cards: 24 px gaps.
- **Heights.** Button 52 px (small 42 px), input and select 52 px, textarea 104 px, chip 42 px (38 px in the follow-up
  questions, 46 px in a two-way switch), search pill 40 px, section nav 64 px, the phone bar's button 52 px inside 8 px
  padding (69 px in all), dialog close button 44 x 44 px, each direct contact row at least 44 px.
- **Breakpoints.** 1,000 px is the main one (13 rules: two columns, the section nav, the desktop gallery, cards as a
  grid). Others: 560, 640, 700, 720, 760, 768, 800 and 900 px. Below 380 px buttons tighten (`DEV:9625`).
- **Layers.** Section nav: sticky, `z-index:40`, desktop only. Phone bar: fixed, 990. Dialog and search sheet: 999992.
  Gallery viewer: 999993. Section 12 explains why these numbers do not win against the theme's bars.

## 4. Corners, borders, shadows

- **Corners.** 0 on everything rectangular: buttons, inputs, chips, cards, photos, panels, dialogs. The CSS says
  `border-radius:0!important` 12 times to beat the theme. Pills (`999px`) on three things only: the status chip, the
  "Search homes" button, the round search button in the section nav. Circles: the agent's photo (64 px), the play button
  (84 px), the tick after sending (52 px), the 6 px dot in the status chip, the slider's handle.
- **Borders.** 1 px hairlines and nothing heavier: `--line` between rows and around cards, `--line-2` around controls,
  a 1 px navy rule under the table's header and above the calculator's result, a 2 px brass top on one kind of card.
- **Shadows.** None on cards, buttons or photos (`box-shadow:none!important` 15 times). The exceptions: the focus ring
  on a field (`0 0 0 3px rgba(13,36,64,.1)`), the phone bar (`0 -6px 18px rgba(13,36,64,.08)`), the play button
  (`0 10px 30px rgba(0,0,0,.25)`), and a text shadow under captions that sit on photos.
- Depth comes from bands of colour, not from elevation.

## 5. Buttons

`.bkd-btn` (`DEV:9270-9280`): inline-flex with a 10 px gap, at least 52 px high, 26 px side padding, 1 px border, square
corners, Montserrat 600 at 12.5 px in capitals with 0.12em letter-spacing, one line, no shadow, colours ease over 0.2 s.
A 17 px arrow often follows the label.

| Variant | Rest | Hover and keyboard focus | Seen on |
|---|---|---|---|
| `.bkd-btn-navy` | navy fill, white text | inverts: white fill, navy text, navy border | "Send my enquiry", "Get the price list", "Compare these with an advisor" |
| the same inside a navy panel | brass fill `#B98A44`, navy text | light brass fill | "Send me the price list" in the hero |
| `.bkd-btn-line` | white fill, `--line-2` border, navy text; on navy: clear with a 45% white border | border turns navy (white on navy) | "Ask for site photos", "Refine search" |
| `.bkd-btn-white` | white fill, navy text | navy fill, white text | "View 34 photos" on the hero photo |
| `.bkd-btn-sm` | 42 px high, 18 px side padding, 11.5 px text | | the section nav's button, photo count |
| `.bkd-btn-block` | full width | | form submit buttons |
| disabled | 60% opacity, "busy" cursor | | while a form sends |
| below 380 px | 16 px side padding, 0.08em letter-spacing | | |

Other controls that act as buttons: the calculator's `.bkd-cbtn` (50 px, 12 px text, same look; `DEV:1479`); filter
buttons above the table (40 px, 13 px sentence case, pressed = navy fill; `DEV:9465-9466`); a two-way switch (46 px,
14 px 600; `DEV:9682-9684`).

The newest pages already write buttons differently: on the list pages (`#bkd.bkn`) a button is 15 px, sentence case,
no letter-spacing and no arrow, and form labels are 14 px sentence case (`DEV:13521-13523`, the end of
`blockke_bkn_card_css()`).

## 6. Links

- **Action link** `.bkd-link` (`DEV:9281-9283`): navy, Montserrat 600 at 14 px, underlined by a 1 px `--line-2` border
  3 px below the text; the border turns navy on hover; an icon may sit before it (grid, pin, search) or after it (arrow),
  8 px away. On navy: white text, white underline at 45 to 50%. Examples: "View all 34 photos", "Floor plan & price",
  "See all homes for sale in Westlands", "Open in Google Maps".
- **Link inside a sentence** `.bkd-inline` (`DEV:9269`): navy 600, underlined, offset 4 px ("Ask us on WhatsApp").
- **Links in the listing's own text** (`DEV:9436`, `9567`): navy, underlined, offset 3 px.
- **Breadcrumb** (`DEV:9292-9295`): 12 px, slate, underlined on hover, "/" between steps. On phones only one step
  back shows, as "‹ Westlands" in 13 px navy 600, 40 px high (`DEV:9636`).
- **Contact rows** (`DEV:9577-9579`): an 18 px icon, then 15 px navy 500; underlined on hover; at least 44 px high.
- **Share**: a text button in 12 px capitals with the share icon (`DEV:9296`).
- `#bkd a:hover{color:inherit!important}` stops the theme's link hover colour (`DEV:9247`).

## 7. Cards, panels, rows

- **Similar-home card** `.bkd-card` (`DEV:9547-9557`, badge `9659`): no box at all. Photo 4:3 on a `--line` ground,
  16 px gap, name in Montserrat 500 at 17 px, price in 600 at 15 px navy ("From KES 9M"), a line of facts in 14 px slate
  ("1–3 bed · Westlands"). Badge 12 px in from the photo's top left: navy at 90%, white 10.5 px capitals ("OFF-PLAN ·
  2027"). The photo grows to 1.04 over 0.8 s on hover. From 1,000 px a three-column grid with 24 px gaps; below, a row
  that scrolls sideways and snaps (cards 78% wide, 44% from 640 px) and ends in a navy "See all" tile.
- **Newer listing card** `.bkn-card` (area, index and search pages; `DEV:13482`): photo 3:2; a paper-coloured tag at
  the photo's bottom left in 12 px dark brass; the name in the serif at 26 px (24 px on phones); place in 14 px slate;
  a facts line in 15 px with the price in navy 600; on hover the photo dims 6% and the name is underlined. A row
  version has a 112 px photo beside the text.
- **White panel** `.bkd-panel` (`DEV:9517`): white, no border, padding `clamp(24px,4vw,48px)`. Holds the calculator.
- **Form card** `.bkd-formcard` (`DEV:9580`): white with a 1 px `--line` border, same padding.
- **Navy panel**: the hero's quick form `.bkd-quick` (`DEV:9313`) and the investor box `.bkd-inv` (`DEV:9526`): navy
  ground, on-navy text, title in the serif or in small light-brass capitals.
- **Note box** `.bkd-suits-note` (`DEV:9525`): sand ground, 14.5 px slate.
- **Bordered card** `.bkd-prog-card`, `.bkd-prog-box` (`DEV:9706`, `9708`, `9739`): white, 1 px `--line`, sometimes a
  2 px brass top.
- **Tile grid on navy** `.bkd-amt` (`DEV:9493-9499`): tiles separated by 1 px lines (the grid's own ground showing
  through at 14% white). Each tile: a 28 px line icon in light brass, a serif title, one sentence in white at 68%.
- **Fact cells**: no boxes, only rules. The hero has three cells between a top and a bottom rule with 1 px dividers
  (`DEV:9308-9312`); the navy band has two columns on phones and up to six on desktop (`DEV:9390-9399`). A value is
  kept on one line and cut with an ellipsis; on pages for homes a long one (class `bkd-hf-long`, such as "Spring
  Valley") may take two lines and drops to 19 px on phones (`blockke_dev_home_css()`, `DEV:6689-6691`).
- **Rows**: a tick in dark brass, then text, 16 px above and below, a 1 px rule between rows (highlights, who it suits,
  essentials); or a name on the left and a value on the right in 14 px slate (nearby places; `DEV:9536-9539`).
- **Table** (`DEV:9463-9477`): header in 10 px capitals over a 1 px navy rule; rows with 24 px above and below and a
  `--line` rule; name 17 px navy 500 with a 13 px muted note under it; price 15 px navy 600; two text links at the right. Below
  720 px each row turns into a small grid: name left, price right, size under, links below.
- **FAQ** (`DEV:9558-9568`): rows between 1 px rules; question 17 px navy 500 with 22 px above and below; a 12 px plus
  in dark brass, drawn with two gradients, that turns 45° into a cross when open; answer in slate, at most 640 px wide.
- **Steps** (`DEV:9509-9516`): 01 to 04 in 12 px dark brass, joined by a 1 px vertical line.

## 8. Form fields

`DEV:9333-9377`, select and switch `DEV:9680-9686`.

- **Label** above the field: Montserrat 600, 11 px capitals, 0.12em, slate; "(optional)" beside it in lower-case 400
  muted.
- **Input**: 52 px high, 16 px side padding, 1 px `--line-2` border, square, white, Montserrat 400 at 16 px (16 px keeps
  iPhones from zooming), text in navy, placeholder `#98A3B0` ("07XX XXX XXX").
- **Focus**: the border turns navy and a 3 px ring appears (`rgba(13,36,64,.1)`). On a navy panel the field's border is
  white, and on focus it turns light brass with a ring of `rgba(221,185,127,.4)`.
- **Error**: the border turns `--err`; a message in 12 px 600 `--err` sits under the field (`aria-live="polite"`); on
  navy the border is `#FF8A7A` and the message `#FFB4A9`. A form-wide alert is a `#FBECEA` box with `--err` text.
- **Textarea**: 104 px, 14 x 16 px padding, can be dragged taller.
- **Select**: 52 px, same border, its own chevron (an inline SVG in navy).
- **Choice chips** (radio buttons): at least 42 px high, 16 px side padding, 1 px `--line-2`, white, 13 px navy 500;
  chosen = navy fill with white text (on navy: brass fill with navy text); 2 px navy outline on keyboard focus. Used
  for "Interested in", "Buying to", "When would you like to buy?" and "Best way to reach you".
- **Two-way switch**: two chips joined, 46 px, 14 px 600 ("Buy | Rent" in the search sheet).
- **Honeypot**: `<input class="bkd-hp" name="bkd_hp" tabindex="-1" autocomplete="off" aria-hidden="true">`, hidden
  with `display:none` (`DEV:9348`, markup `DEV:8110`).
- **Consent**: one 12 px muted line under the button with an underlined "Privacy policy".
- **Quick form**: two fields (Full name, Phone / WhatsApp) and one button. After sending: a tick in a 52 px circle, a
  serif "Thank you", then an optional second step with chips ("Help us send the right details") and "Continue on
  WhatsApp" (`blockke_dev_quick_form()`, `DEV:8108`).
- **Full form**: name and phone side by side from 560 px, email (optional), three or four rows of chips, a message, a
  full-width navy button (`blockke_dev_full_form()`, `DEV:8116`).

## 9. Icon sprite

`blockke_dev_sprite()`, `DEV:7756-7801`; used through `blockke_dev_icon()`, `DEV:7751`. 35 icons: 34 outlines and the
WhatsApp mark; two more for land pages. The sprite is one hidden
`<svg>` printed first inside `#bkd`; an icon is `<svg class="bkd-i"><use href="#bkd-i-NAME"></use></svg>`. All icons
are drawn on a 24 x 24 grid as outlines: stroke in the text colour, 1.6 wide (2 for ticks, 1.4 for the 28 px tile icons),
round ends and joins, no fill, 18 px unless a rule says otherwise (`DEV:9253`). WhatsApp is the only icon drawn
filled; the play triangle is filled where it is used (`DEV:9460`).

| id | Shows | Where it is used |
|---|---|---|
| `arrow` | arrow pointing right | after button and link labels |
| `search` | magnifying glass | "Search homes", "Refine search" |
| `check` | tick | highlight rows, essentials, who it suits, the tick after sending |
| `close` | cross | closing a dialog or the gallery viewer |
| `phone` | telephone handset | the phone row |
| `mail` | envelope | the email row |
| `pin` | map pin | "Open in Google Maps" |
| `grid` | four squares | "View 34 photos" |
| `share` | three joined dots | Share |
| `waves` | three wavy lines | pools, beach, water; a feature group about wellness, leisure or lifestyle |
| `users` | two people | residents' lounge, clubhouse; a group about family or social life |
| `shield` | shield with a tick | a group about security or access |
| `leaf` | leaf | gardens, sky garden, nature walks; a group about water, power or green features |
| `film` | strip of film | private cinema; a group about entertainment |
| `laptop` | laptop | co-working lounge; a group about work or internet |
| `car` | car | a group about parking |
| `building` | block with windows and a door | a group about convenience, retail or the building; business districts in the nearby list |
| `sofa` | sofa | a group about the unit, interiors or the kitchen |
| `star` | five-point star | a group that matches nothing else |
| `play` | play triangle | the video poster |
| `glass` | stemmed glass | sky lounge, rooftop bar |
| `lotus` | lotus flower | spa, sauna, yoga |
| `dumbbell` | dumbbell | gym |
| `kite` | kite with a tail | children's play area |
| `cup` | cup with steam | café, restaurant |
| `bag` | shopping bag | shops on site; shopping in the nearby list |
| `flame` | flame | barbecue area |
| `ball` | ball with seams | sports courts |
| `flag` | flag on a pole | golf |
| `route` | path between two points | jogging track |
| `medical` | cross | hospitals in the nearby list |
| `cap` | graduation cap | education in the nearby list |
| `chart` | rising line with an arrow | "For investors" |
| `plane` | aeroplane | airports in the nearby list |
| `wa` | WhatsApp mark, filled, shown in `#1DA851` | every WhatsApp link |
| `bolt`, `drop` | lightning bolt, water drop | land pages only, in their own sprite (`DEV:6698`) |

The words that pick an icon are in `blockke_dev_amenity_icon()` (`DEV:2936`) and the amenity library (`DEV:3081`
onwards), which also holds one stock sentence per amenity ("Train before work, with no membership or commute.").

Not in the sprite, and likely to be wanted on a tenant's page: bed, bath, floor area, key, calendar, clock, money,
document, lift, wifi, pets, furniture. The classic page's overview strip uses the theme's own icons for bedrooms, rooms
and bathrooms, in a different drawing style (`REF/sections/classic-rental-kilimani-3bed_desktop-1440_06-single-overview-section.png`).

## 10. Photos

- **Treatment.** Square corners, no border, no shadow, no tint. A photo fills its box (`object-fit:cover`); the box is
  sand (or `--line` on cards) until the photo loads. The only overlay is a navy gradient under a caption.
- **Hero.** Base CSS: 4:3 on phones, running to both edges of the screen, and 4:5 from 1,000 px (`DEV:9302-9303`). Live
  variant: 3:2 at every width, first on phones, and from 1,000 px photos 2 and 3 sit under it as two 3:2 thumbnails
  8 px apart (`DEV:17579`). A white small button at the photo's bottom right says "VIEW 34 PHOTOS".
- **Gallery** (`DEV:9445-9453`): the first five photos are in the HTML and three show at every width (`DEV:9451`).
  On phones: photo 1 at 16:9 across the width, photos 2 and 3 at 4:3 side by side, 12 px gaps. From 900 px a six-column
  grid with 16 px gaps: photo 1 takes four columns and two rows, photos 2 and 3 stack at the right. Under it: a
  fine-print note ("Project renders.") and the link "View all 34 photos".
- **Other ratios.** Amenity photos 4:3; similar-home cards 4:3; newer listing cards 3:2; video 16:9 with an 84 px white
  play button; map 4:3.
- **Captions.** On a gallery tile: a gradient from clear to navy at 72% along the bottom, white 13 px 500, left aligned
  (`DEV:9450`). On an amenity photo: the gradient reaches 86% and the caption is in the serif at 21 to 25 px
  (`DEV:9485`). On the hero in the base CSS: 11 px white with a soft shadow, 16 px in from the bottom left
  (`DEV:9306`); in the live variant that label moves into the viewer ("1 / 34 · Project render").
- **Hover.** The photo grows to 1.03 (1.04 on cards) over 0.8 s inside its fixed box; the pointer becomes a
  magnifier on gallery tiles.
- **Viewer** (`DEV:9603-9616`, `9753-9755`; `REF/states/*_state-gallery-viewer.png`): the whole screen in near-black
  navy `rgba(8,18,32,.97)`; at the top a counter with the caption, and a 44 px close button; the photo fitted inside;
  previous and next as 44 x 56 px "‹ ›" on white at 10%; a strip of 76 x 54 px thumbnails at 45% opacity, the current
  one outlined in white. On phones the photo takes the full width, the arrows sit over its edges, and a swipe works.
- **No photo.** A navy gradient tile (145°, navy to second navy) with the listing's name in the serif and "IMAGES ON
  REQUEST" in brass capitals (`DEV:9617-9620`).
- **Map.** A Google Maps embed in greyscale (`filter:grayscale(1) contrast(1.04)`), 4:3, with a white "Open in Google
  Maps" label at its bottom left (`DEV:9532-9535`). Blank in these screenshots (caveat 1).
- **What the photos are.** Santorini's are the developer's renders: a night aerial, a dusk exterior, staged interiors.
  The rental listing's are agent photographs of empty rooms, each carrying a large "BLOCK ADVISORY" watermark across
  its middle, thumbnails and similar-listing cards included
  (`REF/classic-rental-kilimani-3bed_desktop-1440x900_first-screen.png`).

## 11. Motion

- **Reveal on scroll.** A block with `.bkd-rv` starts clear and 18 px low and settles when 6% of it is in view:
  opacity and position over 0.9 s with `cubic-bezier(.2,.7,.2,1)`. It applies only once the script has added `bkd-js`
  to `#bkd`, so a page without script shows everything (`DEV:9621-9623`; `REF/_work/dev-js-v182.txt:338-343`).
- **Reduced motion.** `prefers-reduced-motion: reduce` switches off every animation and transition and shows the
  blocks at once (`DEV:9624`).
- **Hover.** Colours and borders ease over 0.2 s; photos grow over 0.8 s; the chevron on "Read the full description"
  and the FAQ plus turn over 0.2 to 0.25 s.
- **Dialog.** On phones a sheet rises 40 px from the bottom as it fades in, 0.32 s, same curve; from 700 px it is a
  centred box that rises 12 px and grows from 98% (`DEV:9590-9595`).
- **Phone bar.** Slides up from below in 0.3 s once the hero's form has scrolled away, and leaves when the enquiry
  section comes into view (`DEV:9584-9585`; script line 322). Not seen on the live page: section 12.
- **Section nav.** The current section's link gets a 2 px navy underline; the row of links scrolls itself smoothly to
  keep it in view (script lines 324-326).
- **Between pages.** The hero photo carries over from the card it was opened from: a cross-document view transition,
  280 ms with `cubic-bezier(.2,0,0,1)`, the rest of the page 160 ms; off with reduced motion
  (`REF/_work/live-bkn-vt.css`).
- Nothing moves by itself: no autoplay, no parallax, no self-advancing carousel.

## 12. The site around the layout (what a new layout sits inside)

Measured in `_work/reports/*.json` (keys `shell`, `floatersArrived`, `floatersScrolled`) and `_work/reports/probe.json`
(keys `dev.ancestors`, `dev.fixedBars`, `dev.onTopWithDialogOpen`, `rent.wide`).

**Desktop, 1440.** A navy strip 40 px high with nothing in it, then a white header 90 px high: logo 230 x 30 px at the
left, the menu in Montserrat 600 at 14 px (Buy, Rent, Off-Plan, Completion, Sell & Manage, Invest, About), a navy
"GET A CONSULTATION" button, an outlined "CONTACT" button, the phone number, an account icon. The layout starts at
y = 130. Nothing is fixed to the top after scrolling; the layout's own section nav takes that place at y = 0. Two white
50 x 50 px buttons with 10 px corners appear at the bottom right once the visitor scrolls: an envelope
(`a.contact-box`, 75 px from the right edge) and back-to-top (`a.backtop`, 15 px from the right edge). The footer
(`footer.bkef`) is navy, 1,222 px high: logo, one line about the firm, a brass pill-shaped "Get a Consultation" button,
five columns of links under gold capital headings, then "Popular searches".

**Phone, 390.** A white header 56 px high that sticks to the top (`z-index:100`): menu icon at the left, the stacked
logo in the middle, an account icon at the right. On listing and area pages a black bar 53 px high, "Advanced Search",
follows it and scrolls away. The layout starts at y = 109. On listing pages `body` has 96 px of padding at the bottom.
The footer is 2,612 px high.

**Fixed pieces that belong to the theme** (all direct children of `<body>`):

| Piece | Where | Size and layer | Notes |
|---|---|---|---|
| `.mobile_agent_area_wrapper` | bottom of the screen on listing pages below 992 px | 390 x 62 px, `z-index:990`, white, shadow upwards | Agent's photo and name, then three black buttons with rounded corners: email, call, WhatsApp. On a classic rental it also shows the rent ("KES 135,000 per month") |
| `a.backtop` | 15 px from the right; on phones 90 px above the bottom (y 704 to 754 of 844) | 50 x 50 px, `z-index:102`, white, 10 px corners | Appears after scrolling |
| `a.contact-box` | desktop only, left of back-to-top | 50 x 50 px, `z-index:100` | Envelope button |
| the layout's own phone bar `#bkd-mb` | never seen live | 69 px, `z-index:990` | It stands down whenever the theme's agent bar is showing (`REF/_work/dev-js-v182.txt:314`, `322`). On this page the agent bar shows from the first screen to the last, so phone visitors never get "Get the price list" in a bar |

**Why the theme's bars sit on top of the layout's dialogs.** The theme's page wrapper `#all_wrapper` is
`position:relative; z-index:1`, which makes it a stacking context. Everything inside it, the whole of `#bkd` with its
dialog at 999992 and its viewer at 999993, is painted as one layer at level 1, below any child of `<body>` with a
higher z-index. So on phones the agent bar and the back-to-top button stay on top of the open enquiry sheet, search
sheet and gallery viewer: the sheet's consent line is under the bar, the "Send it to me" button is partly under the
back-to-top button, the viewer's thumbnails are under the bar (`REF/states/dev-sale-santorini_phone-390x844_state-*.png`;
`probe.json`, key `dev.onTopWithDialogOpen`: at the centre of the bar the top element is the bar, at the centre of the
back-to-top button it is that button, with the sheet's submit button beneath it). An overlay that must cover them has to
be printed outside `#all_wrapper` (the footer hook prints there: the "Ask Block" dialog `#blkac` is a child of `<body>`),
or the page has to hide those bars while an overlay is open. In the saved HTML the wrapper opens on line 4167 and
closes on line 5761; the back-to-top and envelope buttons are on lines 6358 and 6359, the agent bar on line 6529 and
`#blkac` on line 7324, all after it (`REF/dev-layout-page.html`).

**Theme rules that reach into the layout.** Paragraphs take the theme's 14 px / 1.65 unless the layout sets a size
(section 2). A list row without its own colour takes the theme's `#1A1A1A`. A table inside the listing's own text gets
dark vertical cell borders and loses its left padding (`REF/states/dev-sale-santorini_desktop-1440x900_state-full-description-open.png`).

**The homepage and the area page, first screens.** The homepage opens on a photograph across the whole width under a
navy wash, with the header laid over it in white: a short brass rule, a line of small capitals ("NAIROBI · RESIDENTIAL
& COMMERCIAL"), the H1 in the white serif ("We know Nairobi block by block.", 86 px at 1440, 40 px at 390), one
sentence, and a white square-cornered search card with three tabs ("BUY", "RENT", "OFF-PLAN"; the current one
underlined in dark brass), one field and a navy "SEARCH" button; under it a row of figures in the serif with capital
labels ("850+ HOMES FOR SALE") (`REF/home_desktop-1440x900_first-screen.png`). The area page is plain paper: a
breadcrumb, "Search homes" and "Share" as underlined text links, the H1 "Property in Kilimani" in the serif (64 px at
1440, 40 px at 390), an 18 px introduction, then one long paragraph that fills the rest of the screen
(`REF/area-kilimani_desktop-1440x900_first-screen.png`).

**The menu icon on phones is almost invisible** in these captures: `#F9F9F9` lines on the white header
(`REF/dev-sale-santorini_phone-390x844_first-screen.png`, top left). Worth checking on a real phone; it is the theme's,
not the layout's.

## 13. Anatomy of the development page

Markup: `blockke_dev_render()`, `DEV:8553-9229`. Live HTML: `REF/dev-layout-page.html` lines 4644 and 4894. One crop per
section: `REF/sections/dev-sale-santorini_desktop-1440_NN-name.png` and `..._phone-390_NN-name.png`. The page is 14,913 px
long at 1440 (16.6 screens) and 18,990 px at 390 (22.5 screens). Wording in quotation marks is the live page's.

| # | Section (`id`, `DEV` line) | Band | What it is, in one line | The wording that makes it a BUYER's page |
|---|---|---|---|---|
| 1 | Hero (`bkd-top`, 8622) | paper | Breadcrumb, a search pill and Share; a status chip and the area; the name as H1; one sentence; three figures; a navy two-field form; the photo with a photo count | Chip "OFF-PLAN · FOR SALE"; figures "FROM KES 9.2M", "COMPLETION Dec 2029", "DEPOSIT 20%"; form "Get the price list and floor plans", "Sent straight to your WhatsApp. Two details, no obligation.", button "Send me the price list" |
| 2 | Section nav (`bkd-subnav`, 8771) | paper, sticky, desktop only | The name, links to the sections, a search button, WhatsApp, one button | Links "Residences", "Payment plan"; button "Get the price list" |
| 3 | Facts band (no id, 8810) | navy | Up to six figures across the page | "RESIDENCES 328", "HOMES 1 – 3 Bed & Lofts", "SIZES 64 – 160 m²", "LIFTS 6", "PARKING 4 underground levels": the scale of a development |
| 4 | Overview (`bkd-overview`, 8819) | paper | Label and H2 at the left; a large first paragraph, a second paragraph, six ticked points, and the whole description folded behind "Read the full description" | H2 "About *Santorini Residences*"; "a premium off-plan development of 328 homes ... for sale from KES 9.2M ... the payment plan starts with a KES 200,000 reservation and a 20% deposit" |
| 5 | Gallery (`bkd-gallery`, 8840) | paper | One large photo and two small ones, a note, a link to all photos | Note "Project renders." (the home does not exist yet) |
| 6 | Construction progress (`bkd-progress`, 8856) | paper, a bordered white card | The completion date and an offer of site photos | Label "ON SITE", "Construction progress", "Completion: Dec 2029. Ask us for the developer's latest site photos and progress report", button "Ask for site photos" |
| 7 | Residences (`bkd-residences`, 8869) | sand | A table of unit types with size and starting price, two links per row | H2 "Availability *and pricing*"; columns "RESIDENCE, SIZE, PRICE FROM"; links "Details", "Floor plan & price"; "Starting prices. Ask for the latest price list for current availability."; "See all homes for sale in Westlands" |
| 8 | Amenities (`bkd-amenities`, 8923) | navy | An intro, six tiles with an icon, a serif title and a stock sentence, then a ticked list of basics | H2 "What comes *with the keys.*"; "The everyday *essentials*", "The practical things buyers ask about first." |
| 9 | Payment (`bkd-payment`, 8956) | sand | The plan in a heading, four numbered steps, and a white calculator | Label "PAYMENT PLAN"; H2 "Reserve with *KES 200,000*"; steps "Reservation fee, Deposit, Balance, Completion"; "Estimate your instalments" with price, deposit slider, "MONTHLY / QUARTERLY", "KES 198,919", "INSTALMENTS 37 × KES 198,919", "TOTAL PRICE"; button "Get the official payment plan". On a finished home this section is "Mortgage": "Ready to move in. *Finance it your way.*" and "Estimate your monthly repayment" (`DEV:9080-9087`) |
| 10 | Who it suits (`bkd-suits`, 9090) | paper | A ticked list of who the home suits, a sand note on who it suits less, and a navy investor box | Label "IS IT RIGHT FOR YOU?"; H2 "Who it *suits best*"; "Homeowners and families ...", "Property investors targeting Westlands' rental and resale market", "Diaspora buyers ..."; "It suits less well buyers who need to move in before 2030"; box "FOR INVESTORS", link "Ask for rental and resale comparables" |
| 11 | Location (`bkd-location`, 9111) | paper | The address as H2, a paragraph, six nearby places with their kind and distance, a greyscale map | H2 "Lantana Road, *Westlands*" (neutral: nothing here is about buying) |
| 12 | Why Block (`bkd-why`, 9142) | navy | Three numbered promises | H2 "Advice first. *Then a decision.*"; "We compare developments, layouts and payment plans"; "review the developer, approvals and sale documents before you reserve or pay a deposit"; "One advisor to handover" |
| 13 | Also consider (`bkd-similar`, 9155) | sand | Six cards of other developments and two buttons | H2 "More developments in *Westlands*"; cards "From KES 9M", badge "OFF-PLAN · 2027"; "See all homes for sale in Westlands"; "Compare these with an advisor" |
| 14 | FAQ (`bkd-faq`, 9185) | paper | Nine questions that open in place | H2 "Questions buyers *ask*"; "How much are apartments at Santorini Residences?", "What is the payment plan?", "Can I buy from abroad?" |
| 15 | Enquire (`bkd-enquire`, 9195) | sand | The agent with phone, WhatsApp and email at the left; a white form card at the right; a disclaimer | H2 "Speak to an advisor about *Santorini Residences*"; "Get the price list, floor plans and payment plan, or book a viewing."; chips "BUYING TO: Live in / Invest / Both" and "WHEN WOULD YOU LIKE TO BUY?: Within 3 months / 3–12 months / Just exploring"; disclaimer about "the developer's published material", "payment terms and completion dates" |
| 16 | Overlays (9210) | | A phone bar, an enquiry dialog, a search sheet, a photo viewer | Bar and dialog "Get the price list", "Floor plans and the payment plan, sent to your WhatsApp.", button "Send it to me"; sheet "Find homes *like this one*" with "LOOKING TO: Buy / Rent", "BUDGET", "COMPLETION" |

**The formula it repeats.** Ten of the fifteen sections open the same way (overview, residences, amenities, payment,
who it suits, location, why Block, also consider, FAQ, enquire), and the progress card copies it: a small brass label
with a rule, then a serif H2 with one italic brass phrase. Six of them put the heading in the left column of a 5-to-6
split and the content in the right; four put the heading across the top. The band behind alternates paper, sand and
navy. Every call to action leads to one thing, the price list. That formula, more than any one section, is what a
visitor recognises as "the development page".

**What a reskin would look like, so it can be avoided.** The October "rent mode" inside the development snippet kept
this page and changed its words: the same hero with "Rent / month", "Availability" and "Location" in the three cells
and "Arrange a viewing" on the navy form (`DEV:8591-8602`, `8644-8649`, `8763-8765`); the same table headed
"Availability *and rent*" (`DEV:8899`); the payment band renamed "Renting" with the rent in the H2 and a "See it in
person" panel where the calculator was (`DEV:9000-9030`); "Questions tenants *ask*" (`DEV:9187`); the same enquiry
section, phone bar and dialog (`DEV:9198`, `9212`, `9215`). Its rules were approved and are to be ported; its look is
the thing not to port (`ROOT/PROJECT.md`, section 2). A page with these seven marks will read as that reskin, whatever
its words say:

1. A hero made of the name, one sentence, three figures in a ruled row, and a navy two-field form beside a photo.
2. A navy band of four to six figures directly under the hero.
3. The same label and serif H2 with an italic phrase at the head of every section, most of them in a 5-to-6 split.
4. A sand band holding a price table with one text link per row.
5. A navy band of icon tiles with a stock sentence each, then a ticked list.
6. A sand band where a heading states the money and a white panel sits at the right.
7. Three numbered promises on navy, then cards, then FAQ, then a chip-heavy form at 75 to 82% of the page's length.

What is brand and stays: the colours, the two typefaces and the italic accent, hairlines instead of boxes, square
corners, the line icons, calm wording. What is this page's signature and is free to change: the order, what the first
screen answers, how the photos are used, which sections exist, how many times the label-and-serif-heading pattern
appears, and where the form sits.

## 14. The classic rental page, as tenants see it today

`REF/classic-rental-kilimani-3bed_*`, `REF/sections/classic-rental-kilimani-3bed_*`. This is the theme's own template
with Block's custom CSS on it; the rental layout will replace it for the listings it renders. 7,050 px long at 1440,
12,892 px at 390.

In order at 1440: breadcrumb; two outlined capital tags ("FOR RENT", "APARTMENTS, RESIDENTIAL"); the listing's whole
title as H1 in Montserrat 700 at 46 px ("3 Bedroom Apartment for Rent in Kilimani, Lenana Road", two lines); at the
right "KES 135,000" in Montserrat 700 at 34 px with "PER MONTH" in small capitals; "Nairobi, Kilimani"; Share,
Favorite and Print buttons; a photo slider with 10 px corners, a "Complete" tag and a strip of grey thumbnails; beside
it a navy contact card that follows the scroll ("REQUEST INFO | SCHEDULE A TOUR", the agent, Name, Email, Phone, a
message, SEND, CALL, WHATSAPP US, "Response within 1 hour, 8am–8pm EAT · No obligation"); then "Overview" (updated
date, category, property ID, bedrooms, rooms, bathrooms); "Description" in a white card, folded behind "READ FULL
DESCRIPTION"; "Address" with "What's *nearby*" on sand; "Details"; "Features" as navy tiles and a ticked list; "Map";
"Schedule a tour" (dates, a time, In Person or Video Chat, a second form); "Similar Listings" (six cards); the footer.

On a phone the order is the same, except that the contact card moves to the very end, after the six similar listings:
it starts at 8,553 px of 12,892 px, two thirds of the way down. A bar at the bottom of the screen carries the agent's
name, the rent and three buttons throughout.

The "What's nearby" panel, the navy feature tiles and "The everyday *essentials*" are the development snippet's
additions to classic pages (`blockke_dev_classic_css()`, `DEV:1720`); they are the only places where this page uses the
serif and the brass.

## 15. The brand's visual character, in ten points

From the screenshots (`REF/slices/`, `REF/*_first-screen.png`):

1. **Three surfaces and one accent.** Deep navy, a warm paper white and a sand band, with brass as the only accent. No
   other hue appears except WhatsApp green and whatever is in the photographs.
2. **A tall, narrow serif for anything worth remembering.** Names, headings and figures are set large and tight in
   Instrument Serif, in one weight; Montserrat does everything functional. The contrast between the two is the look.
3. **One italic brass phrase per heading.** "Availability *and pricing*", "What comes *with the keys.*", "Advice
   first. *Then a decision.*" It is the brand's signature gesture, and on this page eleven headings carry it.
4. **Small capitals as signposts.** A short brass rule and a widely spaced 11 px label open each section; 10 px
   capital labels sit over figures. They organise the page without colour.
5. **Square corners.** Buttons, fields, photos and panels are all sharp rectangles. The only curves are two pill
   shapes (status, search) and round portraits.
6. **Lines and bands instead of boxes.** Rows are separated by warm 1 px rules; sections are separated by full-width
   changes of colour. There are no drop shadows and almost no card outlines.
7. **A lot of air.** 144 px above and below each section on a desktop, 80 px on a phone; a heading alone in the left
   column while the content runs down the right. It reads slowly and looks expensive.
8. **Figures as headlines.** Prices, dates and counts are shown in the serif at 24 to 54 px with a tiny label above
   them: "FROM / KES 9.2M", "COMPLETION / Dec 2029", "KES 198,919".
9. **Photographs large and unframed.** Edge to edge on phones, square-cornered, captions laid over a navy fade. On
   development pages they are renders, mostly dusk and night, which suit the navy.
10. **A calm adviser's voice, and quiet motion.** "Our honest view of who it suits, and who it may not." Buttons name
    exactly what will be sent. Blocks fade up once as they enter; nothing moves on its own.

## 16. Five things that look weak or dated, not to be copied

1. **On a phone the theme's bars crowd the page and beat its overlays, and two of them are black.** Of 844 px, 171 px
   is chrome: the 56 px header, a 53 px black "Advanced Search" bar, and a 62 px agent bar with three black rounded
   buttons, behind one of which a green WhatsApp circle shows. Black appears nowhere in the brand. On the development
   page the first screen ends at the labels of the three figures, with the figures themselves under the agent bar; on
   the classic rental page it ends partway down the first photo (`REF/dev-sale-santorini_phone-390x844_first-screen.png`,
   `REF/classic-rental-kilimani-3bed_phone-390x844_first-screen.png`). With the enquiry sheet open, the agent bar
   covers its consent line and the back-to-top button sits on its submit button; the same bar covers the search
   sheet's button and the viewer's thumbnails (`REF/states/dev-sale-santorini_phone-390x844_state-enquiry-dialog.png`,
   section 12).
2. **Capitals with wide letter-spacing on every button and label.** 12.5 px capitals at 0.12em on buttons, 10 and
   11 px capitals at 0.18 to 0.22em on labels. It is the "luxury brochure" manner of some years ago, it is slow to
   read, and long labels break: "GET THE OFFICIAL PAYMENT PLAN" takes two lines at 390
   (`REF/sections/dev-sale-santorini_phone-390_08-payment.png`). Block's newest pages already use 15 px sentence-case
   buttons (`DEV:13521-13523`).
3. **Uneven, small and faint text and controls.** Paragraphs at 14 px beside list rows at 16 px and a lead at 21 px;
   FAQ answers at 14 px under 17 px questions; 10 px labels; placeholder text at 2.6:1 and field outlines at 1.6:1
   against white. Tables inside the full description have stray dark vertical lines with the text touching them
   (`REF/states/dev-sale-santorini_desktop-1440x900_state-full-description-open.png`).
4. **A long page built from one repeated block, with the same photos twice and the form near the end.** Fifteen
   sections, 16.6 desktop screens and 22.5 phone screens; the same label and serif heading ten times; the hero's
   photos shown again as the gallery two screens later; the full form at 75% (phone) and 82% (desktop) of the page's
   length (`REF/slices/dev-sale-santorini_*`).
5. **The classic listing page's leftovers.** A 46 px bold sans title, two lines long, whose words come back as the
   description's first heading; black tags at 85% with 10 px corners beside square navy buttons; a "Complete" tag
   printed over the photo counter; a large "BLOCK ADVISORY" watermark across the middle of every photo and thumbnail;
   grey thumbnails; a "Details" block that repeats the overview; a second form ("Schedule a tour") in the middle of
   the page and, on phones, the first form after six other listings; rounded 10 px search boxes under it; a bright
   green WhatsApp button; a contact card that is cut off after "CALL" in a 900 px high desktop window, with the two
   floating buttons on top of it (`REF/with-widgets/classic-rental-kilimani-3bed_desktop-1440x900_scrolled-35pct_sticky-and-floating.png`);
   and a page that is 398 px wide on a 390 px screen, so it slides sideways by 8 px (the date slider's arrows stick
   out; `probe.json`, key `rent.wide`).

Smaller things in the same spirit: an empty 40 px navy strip above the desktop header; four corner styles on one site
(square in the layout, 6 px on the cookie banner's buttons, 10 px on theme cards and floating buttons, a pill in the
footer); a menu icon that is almost white on white; a homepage hero photograph of a family carrying boxes that could
belong to any agency; an area page whose first phone screen is one long paragraph
(`REF/area-kilimani_phone-390x844_first-screen.png`).

## 17. Not established, and how to find out

| Unknown | How to find out |
|---|---|
| Where the cookie banner and the chat bubble sit on a real phone, how tall they are, and what they cover (their hosts are refused in this session) | A screenshot from a real phone or an ordinary browser on first visit, at about 390 px wide, before and after "Accept"; or allow `cdn.pagesense.io` and `salesiq.zohopublic.com` for a capture session and rerun `REF/_work/capture.js` |
| What the Google map looks like in place (greyscale embed) | Same: an ordinary browser, or allow `maps.google.com` |
| Whether the "BLOCK ADVISORY" watermark is in the photo files or added on the site | Open one original upload of listing 45577 from the Media Library; the theme options dump has no watermark setting |
| Whether the menu icon is as faint on real phones as in these captures | Look at any page on a phone |
| Whether the classic page's contact card scrolls inside its column or is simply cut at 750 px | Scroll with the pointer over the card on a 900 px high window |
| Hover, keyboard-focus and error states as pictures | Not photographed; sections 5 to 8 describe them from the CSS. A local copy of the page can be driven to show them without touching the live forms |
| Widths between 391 and 1439 px (tablet, small laptop) | Not captured; rerun `capture.js` with another viewport if a design depends on them |
| The page for a finished development (the "Mortgage" section) | Only an off-plan example was captured; `DEV:9080-9087` has the wording |
| How the pages look to a signed-in editor (the admin bar adds 32 or 46 px at the top) | The local test site (`ROOT/rig/`) |
