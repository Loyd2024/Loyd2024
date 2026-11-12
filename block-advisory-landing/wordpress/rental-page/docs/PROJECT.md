# Block rental page — project brief (read this first)

Owner's request (11 Nov 2026): "create a landing page for the rental units like the ones we have for developments;
for the rentals it should be different."

## 1. What we are building

block.ke (WordPress 7.1.4, PHP 8.5, MySQL 8.4, theme WPResidence 5.6, 31 plugins incl. Elementor, WPBakery, Rank Math,
WPCode Lite, Cloudflare in front) shows each listing (post type `estate_property`) either on the theme's classic
property page or, when switched on for that listing, on Block's **development layout**: a landing-page layout rendered
by a WPCode PHP snippet (live snippet 42706; repo file `block-advisory-landing/wordpress/block-development-page.php`).
The development layout is written for BUYERS of new developments: price list, floor plans, payment plan or mortgage
calculator, "When would you like to buy?", investor view.

We are building the **rental counterpart**: a landing-page layout for a listing that is FOR RENT, at the listing's own
URL, inside the site's own header and footer, written for tenants (and for business occupiers when the listing is an
office, shop or warehouse). Same brand, different job. It must look and read like a different page from the
development one: the rent, what it includes, when the home is available, how to view it and move in. It never shows a
mortgage, payment plan, price list, floor-plan offer, investor view, "buy" wording or a sale badge.

It ships as ONE standalone WPCode PHP snippet (one PHP file; pasted without its opening tag), working name
"BLOCK — Rental page v1". It must work whether the development snippet running on the site is v1.8.2 (live today),
v1.9.1 (in the repo, not live) or absent.

## 2. Where things stand on the site (11 Nov 2026)

- 129 published listings are tagged *For Rent* only (taxonomy `property_action_category`, term slug `for-rent`, and not
  `for-sale`): about 101 homes (apartments, houses, villas, studios) and 28 commercial (offices, warehouses, retail).
  Ten more are tagged both For Rent and For Sale; those are NOT rentals for this project.
- 29 of the rentals used to be on the development layout (custom field `bke_dev_layout` = `on`), where they showed a
  Mortgage section and buyer forms. Today they were set to `off` (classic page) as a stopgap. List: `ref/dev29.txt`.
- Today 77 rental descriptions were rewritten for tenants (headings such as *Rental Terms*, *Why It Works for Tenants*,
  *Why It Works for Companies Housing Staff*, *Arrange a Viewing*). Typical section headings are listed in the data audit.
- A "rent mode" of the development layout exists in v1.9.1 of the development snippet (`ref/dev-v1.9.1.php`, behind the
  option key `rent_pages`), built and tested in October but never installed. Its RULES were reviewed and approved by the
  owner (plan `ref/plan.md` section 7 and README `ref/README-wordpress.md`, "Homes for rent"). The new page replaces
  that rent mode for the listings it renders: **port its rules, not its look**.
- The option `blockke_dev_page` already holds `rent_pages: true` and `classic_inplace: ['rent']`; the live v1.8.2
  ignores both. If v1.9.1 is installed later it will act on them, so the new page must make v1.9.1's classic-page
  rental pieces stand down on the pages it renders.
- Enquiries inbox v1.1.1 is live (snippet 45683, `ref/inbox-v1.1.1.php`): it lists rows of the option
  `blockke_dev_leads` and shows Type *Rental* for a row saved with `'kind' => 'rent'`.
- Snippet 45681 (live) hides the theme's mortgage calculator on rent-only listings with CSS. Harmless to us.

## 3. Reference material (all local; do not re-download)

`ROOT` = `/tmp/claude-0/-workspace-Loyd2024/e24ac003-f571-5336-9956-1fd11a814c7d/scratchpad/rp`

| Path | What |
|---|---|
| `ROOT/ref/dev-v1.8.2.php` | development snippet as live today (v1.8.2; live adds one regex line, irrelevant) |
| `ROOT/ref/dev-v1.9.1.php` | development snippet v1.9.1 with rent mode (18k lines); superset, best reference |
| `ROOT/ref/README-wordpress.md` | README of the snippets (development layout, rent mode, enquiries, settings, install) |
| `ROOT/ref/plan.md` | owner-approved plan, section 7 = rental pages, plus review dispositions |
| `ROOT/ref/inbox-v1.1.1.php` | enquiries inbox (reads `blockke_dev_leads`) |
| `ROOT/ref/listings.json` | public feed of all 1,009 listings: id, t(itle), u(rl), p(rice), pl (after-price label), bd, ba, sz (size, sq ft), la, ln, ad, im, dt, ac (Buy/Rent), ct (categories), ar (areas), ci, st (status), pj (project), fe (features) |
| `ROOT/ref/rent-only-ids.txt` | the 129 rent-only listing IDs |
| `ROOT/ref/urls.txt` | `ID URL` for all listings with a Rent tag |
| `ROOT/ref/pages-final/ID.html` | live HTML of each rent-only listing, fetched today after the rewrites. 100 are classic pages; the 29 in `ref/dev29.txt` were still on the development layout when fetched |
| `ROOT/ref/pages-stop/ID.html` | the same 29 listings after the stopgap (classic pages) |
| `ROOT/ref/theme-options.json` | the theme's options dump, incl. `wp_estate_custom_css` (Block's custom CSS for property pages) |
| `ROOT/ref/live-snippets/` | sources or notes of other live snippets (filled by the live-snippets reader) |
| `ROOT/notes/` | findings of the mapping round (one file per topic) |
| `ROOT/rig/` | local WordPress test site (see `ROOT/rig/README.md` once built) |
| `ROOT/design/` | design concepts, final spec and mock |
| `/workspace/Loyd2024/block-advisory-landing/wordpress/rental-page/` | where the snippet's sources, build script and tests will live (git repo, branch `claude/elegant-gauss-odiqfw`) |

Fixture listings chosen for tests (use these IDs unless told otherwise):
homes 45577, 45538 (same building: Kings Courtyard 3-bed and 2-bed), 44020 (furnished, KES 6,500 per day), 43543
(Runda townhouse), 42616 (Runda villa, 650k), 34171 (rent stated in USD), 42450, 42449 (sub-units of a sale listing),
36499, 36506, 36502 (Taurus: a "2 & 3 bedroom" listing and its units), 39421 (studio to 2-bed "from KES 30K"), 37551
(apartments wrongly categorised Commercial/Offices; Rank Math FAQ block), 44048 and 42590 (price with no "per month"
label), 41862 (furnished), 40490 (house), 43583 (text still has "[Development Name]" placeholders);
commercial 36809 (offices, rent a month), 36834 (KES 125 per sq ft), 36816 (USD per sq ft, price field 0), 35986
(warehouses, no price), 45231 (warehouse), 36826 (retail per sq ft), 40298 (offices); and 43656 (no price, no category).

## 4. Hard rules

### Technical
1. One file. Everything wrapped in `if ( ! function_exists( 'blockke_rp_config' ) ) { ... }`. PHP prefix `blockke_rp_`,
   DOM root `<div id="bkr">`, CSS/DOM class prefix `bkr-`, JS global `window.BKR`, body class `bkr-page`, REST route
   `block/v1/rental-lead`, option `blockke_rent_page`, per-listing custom field `bke_rent_layout` (`''` automatic,
   `on`, `off`), transients `bkr_*`, preview parameters `bkr` and `bkr_key`. Never reuse the development snippet's
   names (`blockke_dev_*`, `bkd-*`, `bke_dev_*`) for new things.
2. Valid on PHP 7.4 through 8.5 with no notices, warnings or deprecations (`array()` syntax, no typed properties, no
   implicit nullable parameters, no dynamic properties). WordPress APIs only; no external libraries, no build step on
   the server, no files written on the server.
3. Calls into other snippets only behind `function_exists()` / `is_callable()`, only for the functions the notes list
   as stable, and with a built-in fallback, so the page works without them.
4. The source is stored through a site connector with two quirks: a backslash directly followed by the letter `u` and
   four hex digits is turned into a character, and text without a block-level HTML tag is Markdown-converted. So: no
   `\uXXXX` escapes anywhere (write the character itself, or build it with `chr()`/`html_entity_decode`), and the file
   starts with the same two marker comment lines as `ref/inbox-v1.1.1.php`. Unix newlines, tabs for indentation,
   UTF-8 without BOM, file ends with a newline.
5. Inline JavaScript is printed with WordPress's own `wp_print_inline_script_tag()` (and JSON-LD the same way), never
   as a hand-written script tag in a PHP string: it is the API WordPress provides, it works with a Content Security
   Policy, and the site's firewall rejects uploads that carry hand-written script-tag text or patterns that match such
   tags. Do not write regexes that match script or style tags (use `wp_strip_all_tags()`), and do not encode, split or
   disguise any text to get it past the firewall. If something would need that, leave it out and say so.
6. Inert by default: with the option `blockke_rent_page` unset, an active snippet changes nothing for visitors. Editors
   (`current_user_can( 'edit_post', $id )`) preview any rental with `?bkr=1` (`?bkr=0` shows the classic page); anyone
   holding the secret in option key `preview_key` previews with `?bkr_key=SECRET` (compare with `hash_equals`; preview
   responses are `noindex` and never cached). Going live is option key `enabled => true`; then a rent-only listing
   gets the layout when its `bke_rent_layout` is `on`, or is empty and the `auto` rule accepts it; `off` keeps the
   classic page. Rollback = switch the snippet off, or remove the key.
7. The page is the listing's normal URL. Rank Math's title, description, canonical and Open Graph tags stay as they
   are. One H1. The full description text is in the HTML. No `noindex` on the live layout.
8. Everything printed is escaped at output (`esc_html`, `esc_attr`, `esc_url`, `wp_kses_post` for the listing's own
   description HTML). The REST endpoint validates and sanitises every field, is rate-limited, has a honeypot whose
   field name browsers' AutoFill leaves alone (not `company`, `name`, `email`, `phone`, `url`, `address`), and saves the
   enquiry BEFORE sending email or calling Zoho, so a slow or failing step cannot lose it.
9. Budgets: no sideways scroll from 320px to 1440px; tap targets at least 44px; visible focus states; respects
   `prefers-reduced-motion`; images carry width, height and alt, the first photo is eager with `fetchpriority="high"`
   and the rest lazy; CSS under about 35 KB and JavaScript under about 18 KB before compression; no layout shift when
   the script loads; the page is usable without JavaScript (photos visible, text readable, phone, WhatsApp and a form
   that still posts).
10. Do not change anything on the live site. Only the agents told to read from it may call it, one request at a time,
    at least 2 seconds apart, never in parallel; on HTTP 5xx wait 20 seconds and retry once. Nobody but the main session
    writes to the live site, commits or pushes.

### Content
11. **Never invent a fact.** Every figure and claim on the page comes from the listing's own fields or text: rent,
    period, currency, sizes, bedrooms, deposit, lease, service charge, furnishing, availability, distances, amenities.
    A deposit or lease term is shown only when the listing states it with a figure; a service charge only with an
    amount or "included"; anything "to be confirmed" is not shown as a fact. Generic copy (how viewing and renting
    work, Why Block) must be true for every listing and promise nothing: no guarantees, no "safe", no returns, no
    invented services, fees or timelines.
12. Rent wording (owner-approved, see plan 7.1 and README "What the rent is per"): "KES 135,000 a month"; other
    periods as written ("a day", "a week", "a year"); per-sq-ft rents "KES 130 per sq ft a month"; no price, a
    "Rent on application" label, or a monthly figure over KES 2M with no label reads "Rent on request"; a rent set in
    US dollars follows the `bke_rent_usd` / `bke_rent_usd_date` rules. "From" is kept when the listing has it.
13. Tenant wording: "Arrange a viewing", "Check availability", "When would you like to move in?", "Questions tenants
    ask". For commercial listings: business occupier wording ("space", "premises", "your team"), no bedrooms.
14. Phone and WhatsApp: the listing's agent's number when the agent has one, else Block's default (0725 937 686,
    owner decision 8); same rule as the development layout. Every WhatsApp link carries `data-blkct="1"`.
15. Plain British English, calm and specific. No hype, no exclamation marks, no emoji.

## 5. Decisions already taken by the main session (do not reopen)

- Standalone snippet (not a mode of the development snippet), because it must be small enough to install and must not
  depend on the 1 MB development file.
- Applies only to rent-only listings; listings tagged both ways, land for sale, and sale listings are untouched.
- Enquiries: own REST endpoint; each enquiry is appended to the option `blockke_dev_leads` in the development
  snippet's row format with `'kind' => 'rent'` (so the Enquiries inbox shows it as a Rental), emailed to the same
  notify list plus the listing's agent, and passed to Zoho through the price-check popup snippet's function when that
  exists. Lead email subject "[Block] Rental enquiry: {name} - {listing}".
- Rentals do not get ad landing pages (`?lp=1`); this layout is the listing's own page.
- The page must make the development snippet stand down for the listing it renders (whatever `bke_dev_layout` says),
  and must keep the buyer price-check popup off on rental pages.

## 6. Working rules for every agent

- Write your notes or code only where your task says. Do not edit another agent's files. If the Write tool refuses a
  path, write the file with a Bash heredoc instead. Never run `git commit`, `git push`, `git checkout` or `git stash`.
- Quote file paths and line numbers for every claim about existing code, so the next agent can check it.
- If something is unknown, say "unknown" and how to find out; do not guess.
- Keep the live site safe: see rule 10.
