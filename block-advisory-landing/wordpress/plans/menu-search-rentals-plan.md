# block.ke: one plan for the menu, the header search, results pages and rental pages (revised after review, 22 Oct 2026)

# Part 1. For the owner (one screen)

**What visitors will notice**
- The header gets **For Sale** and **For Rent** panels built from your live listings, plus a **search** button. The other menus stay as they are but lose repeated links.
- Rental pages will talk to tenants: the rent a month, "Arrange a viewing", and no mortgage calculator or floor-plan offers.
- **/for-sale/** and **/for-rent/** become proper Block pages, and each type gets its own page (for example, every apartment for rent).
- Each part has its own off switch, and nothing goes live without your yes.

**The top of the header**
For Sale · For Rent · Off-Plan · Sell & Manage · Invest · About · Search · Get a Consultation · Contact
("Completion" moves inside Off-Plan.)

**For Sale** (today's numbers; each is the number shown on the page the link opens)
- All homes for sale · 630
- Apartments · 522: Westlands 210 · Kilimani 121 · Kileleshwa 43 · Parklands 23 · Syokimau 19 · Lavington 17
- Villas · 36: Lavington 11 · Karen 6
- Houses · 17
- Commercial · 7
- Land · 15
- Ready to move in
- All neighbourhoods

**For Rent** (today)
- All property for rent · 109 (82 homes, 27 commercial)
- Apartments · 69: Westlands 26 · Kilimani 22 · Kileleshwa 7 · Syokimau 5
- Villas · 2
- Houses · 2
- Commercial · 27: Westlands 17
- All neighbourhoods
- Own a rental? We manage it

Once the data fixes are done, Lavington and Parklands (and possibly Loresho) should appear under Apartments. We recount everything before launch.

**Off-Plan:** All new developments · Off-plan guides by area (the 6 busiest corridors) · Completion 2026 · 2027 · 2028 · 2029+.
**Sell & Manage, Invest, About:** today's links, without the repeats.

**Release order**
0. Data fixes and one search setting. No code.
1. Rental pages (v1.8.3).
2. The new /for-sale/ and /for-rent/ and the type pages (v1.9.0).
3. The new menu and search (v1.9.1), switched on in three steps a day apart.
4. Improvements that also touch sale pages, one a week.
5. Clean-up: redirects from old pages, the sitemap, smarter search, the homepage search box.
6. Later: completion-year pages and city pages.

**Your 8 decisions** (recommended answer in bold)
1. **The header.** "Buy" and "Rent" become "For Sale" and "For Rent". Completion moves inside Off-Plan, and a search button is added. The other panels keep their links in the new style, without repeats, and Off-Plan shows 6 of its 8 corridor guides. Keep the word "Off-Plan", or change it to "New Developments" (wider: the Contact button would then hide on small laptops). **Yes; keep "Off-Plan".**
2. **Which neighbourhoods appear.** Only areas whose page is strong enough for Google (5 or more developments on a type page, 3 or more on an area's homes page). Busiest first, 6 per type, up to 8 when tied. Westlands' sub-areas are counted inside Westlands, and each number matches the page it opens. Villas and Houses keep their own headings, and a type with no strong area shows only its "All …" link. Alternative: no numbers next to areas. **As stated, with numbers.**
3. **New pages.** Our layout takes over /for-sale/ and /for-rent/ (their old content is kept for rollback; tell us whether the 21:46 edit to /for-sale/ today should become its intro). It also takes over Apartments for Sale, Houses & Villas for Sale, All Neighbourhoods and the Kilimani 2-bedroom page. We add 5 pages: apartments, villas & houses, and commercial for rent; commercial and land for sale. From the day each hub goes live, the theme's old For Sale and For Rent archives redirect to it. **Yes.**
4. **Data fixes.** Tags, areas, types, price labels, titles and photos, done through the site tools and logged so each one can be undone. Rentals go first; sale listings follow as a separate batch, after we send you the list of sale pages each fix changes. Staff choose which copy of each duplicate to keep, and removed copies redirect to it. Three rentals get clean names, and the three Runda rentals move onto the new layout. **Yes; the tag, area and type fixes before any counts go live.**
5. **One type per listing** (your listing rules: For Sale or For Rent, never both). 37243, 37258 and Purple Tower 36662 become For Rent only. Bahari 33933, 33932 and 33909 and Brookside Drive 31883 become For Sale only, and staff add separate rental listings where the rent is current. **Yes.**
6. **Improvements that also change sale pages**, each with its own switch, rentals first and then sale pages a week apart:
   - forms that send without JavaScript;
   - Block's extras printed in place on classic pages;
   - area page titles and budgets that mention rent;
   - every photo in the page;
   - price data for Google on sale listings;
   - listing breadcrumbs that point at /for-sale/ and /for-rent/.

   **Yes, in that order.**
7. **Search and old pages.**
   - Our search bar replaces the homepage search box. It has its own switch, and the page itself is not edited.
   - Simple searches jump straight to the matching page.
   - The "Map" link is hidden.
   - The theme's own search bar under the header is hidden or replaced, if it shows.
   - Old theme pages redirect: temporarily for two weeks, then permanently where the target never moves.
   - The sitemap is cleaned up.
   - "Nairobi Market Overview" is kept out of Google until its content is decided.

   **Yes.**
8. **Phone number, decided before the rental pages go live.** Our pages and WhatsApp links use 0725 937 686, the number in your listing rules. The theme's header, footer and Contact page show +254 184 027 349. **Keep 0725 937 686 on our pages until you confirm the new number takes WhatsApp; then switch both numbers in one settings change.**

**We also need from you (not decisions)**
- Who else is changing the site today. That covers the Diamond Bay units, the landing-page speed snippet, the 21:46 /for-sale/ edit, the rental drafts 44095 to 44097, the new Versailles and Westlands Cottage listings, and the Samar and Bahari edits. We need this so no one overwrites anyone else.
- The page source of /, /for-rent/ and one listing, viewed logged out. Or an editor checks our previews for us, because block.ke blocks us.
- An export of Rank Math → Redirections, before release 2.
- In Zoho CRM (your admin), an optional "Enquiry type" field. In Zoho Mail, a filter for "[Block] Rental enquiry".
- Someone to run the live checks and purge Cloudflare after each switch.

---

# Part 2. The build

## 0. Basis and what changed

**Basis**
- Live code: snippet 42706, v1.8.2, md5 b5295848.
- Rebased rental code: `rent/understand/rebase/merged-resolved.php`, md5 b1cf4134.
- Counts come from the live row cache `bkn_rows`, rebuilt 22 Oct 19:39 UTC: 935 rows (937 published; the 2 missing listings are Sold).
- Every menu figure was recomputed for this revision with the Python copy of `blockke_area_v16_data()` (`saleinv/build.py`, extended inline to the rent segments, nothing written), so each figure is the one its landing page prints.
- Notes read:
  - `owner-notes.md`, `summary.md`, `summary-sale-search.md`;
  - `code-map.md`, `menu-and-settings.md`, `inventory.md`, `sale-inventory.md`, `ssr-audit.md`, `search-map.md`, `archives-map.md`, `menu-render.md`;
  - `runda/code/build.md`, `review.md`, `fixes.md`, `contain.md`;
  - the listing rules `listing-prompt/final/block-listing-instructions-v2.md` ("rules v2");
  - `wpcode-cache/41011.txt` and `42847.txt`;
  - the live code and its README.
- **Nothing on block.ke was written.** block.ke returns 403 here, so anything that depends on live HTML is marked **UNCERTAIN**.

**Main changes after review**
1. **No menu writes at all.** The walker finds Buy and Rent by the page they point to, and labels them from config, so switching `nav` off restores today's header exactly.
2. **Every count is the landing page's own figure,** computed by the same function. The For Rent figures change from listings to homes (Apartments 69, not 73), and Parklands (rent, 2 cards, noindex) leaves today's tree.
3. **The lean menu is enforced on every panel.** Off-Plan goes from 17 links to 12. "Ready to move in" moves under For Sale, completion years carry no counts, and duplicates are skipped (`nav_skip`).
4. **The nationwide type pages are real child pages,** taken over by ID like 31708 and 31715, instead of virtual rewrite URLs. Villas and houses for rent share one page.
5. **The rental `?lp=1` refusal is no longer tied to rent mode.** It ships switched on (`lp_rent_guard`), because rentals never get ad landing pages.
6. **Per-day rents, per-sq-ft rents, USD rents and missing rents are all handled explicitly.** USD rents show KES first, with the rate and its date (rules v2).
7. **Live writes go through a pre-write gate** (§10), because other writers are active on block.ke today. The data fixes are split into a rental batch, a sale batch (whose sale-page changes need approval) and a duplicates batch that waits for the redirect code.
8. **Smaller fixes:**
   - the Buy/Rent archive redirects and 38125 move to R2;
   - the sitemap provider;
   - forms on new pages post without script from day one;
   - clearer switches, each with a preview;
   - cache keys include every setting they depend on.

## 1. Principles

- **Lean menu.** The top level stays at 6 panels plus Search.
  - A data panel has at most 5 types, and at most 6 areas per type (8 when tied at 6th).
  - Each data panel has one "All neighbourhoods" link.
  - No list panel holds a repeat of another panel's link.
  - Nothing goes below level 3.
- **Built on the server.** Every link, count, form and filter is in the first HTML response. Script only adds open/close, hover intent and live counts.
- **One switch per part, with an editor preview.** Every new key is off by default except `lp_rent_guard`, which enforces a standing rule. With every new key at its default, every page is byte-identical to v1.8.2, apart from the pinned `?lp=1` list (§7.2).
- **Rentals first.** Any mechanism that also touches sale pages goes on for rentals, then for sale pages a week later.
- **Sale development pages stay byte-identical below the header,** apart from decision 6 changes and the sale data fixes approved in decision 4.
- **The rows decide.** A link's number is the number its landing page prints, from the same function, so the menu and the pages can't disagree.
- **No live write without the pre-write gate** (§10.1).

## 2. Releases

| Release | What it ships | Live writes | Sale development pages |
|---|---|---|---|
| **R0a** (config) | `search_ignore` (§4.4) | 1 option write | unchanged |
| **R0b** (data, rentals) | Batch R of §9 | one logged write per field | unchanged |
| **R0c** (data, sale) | Batch S of §9, after a private-copy diff | one logged write per field | **change as listed in decision 4** |
| **R1 v1.8.3** Rentals | <ul><li>Rent pages (`rent_pages`), `lp_rent_guard`, `rent_cards`</li><li>The classic rent box and phone bar</li><li>Rental leads</li><li>`listing_jsonld`, `classic_inplace`, `gallery_ssr` = 'rent'; `forms_post` = ['rent']</li><li>The `redirects` listings family (for batch D)</li><li>A new inbox snippet</li></ul> | <ul><li>Snippet and inbox snippet, both staged inactive</li><li>Keys, one at a time</li><li>Runda: 3 `bke_dev_layout` writes</li><li>Then batch D</li></ul> | identical; `lp_rent_guard` changes only the pinned `?lp=1` list |
| **R2 v1.9.0** Hubs | <ul><li>`sale_hub`, `rent_hub`, `type_pages`: 31708, 31715, 31733, 38136, plus 5 new child pages</li><li>`seg_intro` (38125), `area_guides`, `sitemap_extra`</li><li>`redirects`: action (302) and pages (38125, 302)</li><li>`forms_post` += 'new'</li><li>`area_rent` code shipped switched off</li></ul> | <ul><li>Snippet</li><li>5 pages created as drafts, then published when switched on</li><li>Keys</li><li>Rank Math noindex on 38125 and 93 (owner)</li></ul> | identical |
| **R3 v1.9.1** Menu and search | Walker and drawer, Search entry, `search_bands`, the `nav_*` config, `theme_search_bar` | Snippet. Option writes on separate days: `nav`, then `nav_search`, then `search_redirect`. `theme_search_bar` after view-source and approval. **No menu writes** | identical below the header (`theme_search_bar` is the approved exception) |
| **R4** (config) | `forms_post`, `classic_inplace`, `gallery_ssr` and `listing_jsonld` to true, and `area_rent` on, one a week apart | 1 option write each | change as approved in decision 6 |
| **R5 v1.9.2** Clean-up | <ul><li>`redirects`: taxonomies and pages (302, then 301 for fixed targets)</li><li>`noindex_features`, `search_loose`, `hero_search`, `list_jsonld`</li><li>`search_map_page` = 0</li><li>`crumb_hubs` once the action redirect is 301</li></ul> | Snippet, keys, Rank Math sitemap toggles (owner) | `crumb_hubs` only (decision 6) |
| **R6** later | Completion-year pages, city pages, card widgets on the guide pages, /nairobi-properties/ content, corridor-page link repoint | separate | — |

R1 comes before the menu because **9 rentals on our layout are drawn as sales today**: 44051, 44050, 44049, 44048, 44020, 43995, 43855, 37243 and 35477. They show floor-plan offers, a mortgage calculator on the rent, and "Buying to". The other 3 layout-on listings with a For Rent tag (Bahari 33933, 33932 and 33909) are sale-and-rent listings, priced for sale and drawn as sales by design.

## 3. The header menu

### 3.1 Top level

```
Today:     Buy ▾ | Rent ▾ | Off-Plan ▾ | Completion ▾ | Sell & Manage ▾ | Invest ▾ | About ▾ | [Get a Consultation] [Contact]
Proposed:  For Sale ▾ | For Rent ▾ | Off-Plan ▾ | Sell & Manage ▾ | Invest ▾ | About ▾ | (search) | [Get a Consultation] [Contact]
```

- **Labels come from `nav_labels`**, default {sale: "For Sale", rent: "For Rent", offplan: "Off-Plan"}, and apply only while `nav` is on. An empty value falls back to the menu 98 title. Menu 98 is never written.
- **Completion folds into Off-Plan** (`nav_fold_completion`). The Completion top-level item is skipped.
- **No change** to the CTA pills, the phone, the login, the logo, the theme's phone bar or the drawer shell.

### 3.2 Mechanism

**The walker**
- A `wp_nav_menu_args` filter swaps in our walker for the `primary` and `mobile` locations only (option D in `menu-render.md`).
- WordPress keeps the theme's container, `ul#menu-main-menu.menu`, the current-page classes, and 41011's Completion children (through `wp_nav_menu_objects`).
- 41011 proves the theme runs `wp_nav_menu()` on every request.

**How items are found (no writes)**

| Item | Found by | What the walker draws |
|---|---|---|
| Buy (36351) | `object_id` = `sale_hub_page` (29678) | The For Sale data panel |
| Rent (36352) | `object_id` = `rent_hub_page` (29769) | The For Rent data panel |
| Off-Plan (36353) | `object_id` = `dev_index_page` (30752) | A list panel, with the completion years folded in |
| Completion (41014) | class `bke-completion-menu` (already set) | Skipped at the top level; its children become the year chips |

**Stored children and skips**
- No stored child of the two data panels is drawn while `nav` is on. That covers Buy's 13 children, and both of Rent's: 36372 "Advanced Search" and 36373 "Own a rental?".
  - The aside link comes from `nav_aside`, default For Rent → "Own a rental? We manage it" → /property-management/.
  - If a stored child appears that is not in the snapshot list `main-menu-98.json`, the walker draws it in the panel's aside and names it in the admin bar. Nothing added by staff disappears silently.
- `nav_skip`, default [36383, 36384, 36387, 36388, 38081], holds the items not drawn while `nav` is on:
  - Diaspora and Investment Advisory under Off-Plan (both are in Invest);
  - "Off-Plan Projects" under Invest (it is in Off-Plan);
  - "Nairobi Market Overview" (a theme listing page, until R6);
  - Block Management under About (the same page as Sell & Manage's Property Management).
- **Fallback:** if `card_v16` is off, or no menu copy can be built, the walker returns the arguments untouched and the theme draws menu 98 as it does today.

**Data and cache**
- **The copy:** a transient `bkn_nav` (about 7 KB), built from `blockke_bkn_rows()`.
- **Per (area, type):** the link target, its indexability and its figure come from `blockke_area_page()`, with `$preview = false`.
- **Version key:** `blockke_bkn_ver( true )` (one option read; the 583 KB rows are never loaded), plus an md5 of the effective values the build reads:
  - `sale_hub`, `rent_hub`, `type_pages`, `area_pages`, `card_v16`;
  - `area_index_min` and `area_type_index_min`;
  - every `nav_*` key, `area_labels` and `nav_links`;
  - the publish status of the hub and type pages.

  A kill switch therefore reaches the menu on the next request.
- **Never stored** from a request that carries any `bke_*` parameter, or from a logged-in user. An editor's preview can't leak into the shared copy.
- **Rebuilds** run on `shutdown` after a bump, under the `bkn_build_lock` pattern. Visitors are always served the previous copy, so a cold build (0.3 to 0.7 s with cold rows) never lands on a visitor's request.
- **Ad landing pages:** the menu's `<style id="bkh-css">` and `<script id="bkh-js">` are hooked only while `nav` is on, and never on an ad landing page. `blockke_dev_lp_document()` calls `wp_head()` and `wp_footer()`, so the hooks check the same flag as the landing-page header.

### 3.3 Rules for what appears

1. **Types, at most 5 per panel.**
   - For Sale: Apartments, Villas, Houses, Commercial, Land.
   - For Rent: Apartments, Villas, Houses, Commercial.
   - Studios are not a menu type. They get a `#studios` section on both nationwide apartment pages, a hub chip and a search field value.
2. **Areas under a type.** An area is listed only when the page its link opens is indexable: `blockke_area_page()['index']`, following the canonical. The link is the canonical URL.
3. **Figures.** Each figure is the number the landing page prints: homes for homes, listings for commercial and land (`blockke_area_v16_count()`), with the noun read out to screen readers.
4. **Order.** Busiest first by that figure; ties go to more cards, then the name. Show 6 (`nav_max`). Areas tied with the 6th are added, up to 8 (`nav_max_tied`). Desktop and drawer use the same rule.
5. **Westlands' sub-areas count inside Westlands** (`nav_rollup`): Riverside, General Mathenge, Brookside, Peponi and Gigiri. The Westlands page already counts them.
6. **A thin type shows only its heading link.**
7. **One "All neighbourhoods" link per panel:** /for-sale/#neighbourhoods and /for-rent/#neighbourhoods. The rent one is left out until `rent_hub` is on.
8. **Labels are curated** (`area_labels`): `upperhill` shows as "Upper Hill".
9. **Fallback targets** (`nav_links`). With a hub or type page off, its link falls back to our results page (`/advanced-search/?for=…&type=…`). Hub figures are dropped while a fallback is in use.

### 3.4 For Sale panel (figures recomputed 22 Oct, 19:39 rows)

```
For Sale ▾                             (without script: a link to /for-sale/)
  All homes for sale · 630             → /for-sale/                       (first in the DOM; placed by grid order)
  Apartments · 522                     → /apartments-for-sale/            (31708)
     Westlands 210                     → /property_area/westlands/apartments-for-sale/
     Kilimani 121                      → /property_area/kilimani/homes-for-sale/      (canonical; apartments = homes cards)
     Kileleshwa 43                     → /property_area/kileleshwa/apartments-for-sale/
     Parklands 23                      → /property_area/parklands/homes-for-sale/     (canonical)
     Syokimau 19                       → /property_area/syokimau/apartments-for-sale/
     Lavington 17                      → /property_area/lavington/apartments-for-sale/
  Villas · 36                          → /for-sale/villas-for-sale/#villas (31715)
     Lavington 11                      → /property_area/lavington/villas-for-sale/
     Karen 6                           → Karen's canonical (homes-for-sale, or /property_area/karen/ when homes is its only kind)
  Houses · 17                          → /for-sale/villas-for-sale/#houses
  Commercial · 7                       → /for-sale/commercial-for-sale/   (new child page)
  Land · 15                            → /for-sale/land-for-sale/         (new child page)
  Ready to move in                     → /new-developments/?completion=ready-now   (aside; no figure)
  All neighbourhoods                   → /for-sale/#neighbourhoods        (aside)
```

- **16 links.**
- **Just missing the bar:** Tatu City apartments (4 cards), Runda villas (4) and Kitisuru apartments (3). Each is one click away on the hub.
- **Dropped from today's Buy menu:** the 8 `/neighbourhoods/<x>/` guide links and "Nairobi Market Overview". The guides stay reachable from the area pages and hub lists (`area_guides`, §6.6), from /neighbourhoods/, and from the footer.

### 3.5 For Rent panel

**Live today (rows of 19:39)**

```
For Rent ▾                             (without script: a link to /for-rent/)
  All property for rent · 109          → /for-rent/                       (82 homes + 27 commercial)
  Apartments · 69                      → /for-rent/apartments-for-rent/   (new child page)
     Westlands 26                      → /property_area/westlands/apartments-for-rent/
     Kilimani 22                       → /property_area/kilimani/apartments-for-rent/
     Kileleshwa 7                      → /property_area/kileleshwa/homes-for-rent/    (canonical)
     Syokimau 5                        → /property_area/syokimau/homes-for-rent/      (canonical)
  Villas · 2                           → /for-rent/villas-and-houses-for-rent/#villas (new child page)
  Houses · 2                           → /for-rent/villas-and-houses-for-rent/#houses
  Commercial · 27                      → /for-rent/commercial-for-rent/   (new child page)
     Westlands 17                      → /property_area/westlands/commercial-for-rent/
  All neighbourhoods                   → /for-rent/#neighbourhoods        (aside, once rent_hub is on)
  Own a rental? We manage it           → /property-management/            (aside)
```

- **12 links.**
- **Parklands is not listed today.** Its 3 rentals form 2 cards, so homes-for-rent is noindex.
- **After batches R and D, expected additions:**
  - Lavington (4 cards, on its homes-for-rent canonical);
  - Parklands (3 cards);
  - Loresho, only if it still has 3 cards once the Loresho Oasis duplicate is removed.

  The tree is recomputed with the Python copy and checked against the live rows before R2 shows any figure.
- **Villas and houses for rent** share one page. Before batch R it holds 4 cards, so it is noindex with its canonical on the hub, and both headings link `/for-rent/#villas-houses`. After batch R it holds 6 cards and becomes indexable (§6.4).

### 3.6 The other panels

| Panel | Contents | Links |
|---|---|---|
| **Off-Plan** | <ul><li>"All new developments", with the index's own headline figure → /new-developments/</li><li>Column "Off-plan guides by area", whose heading is 36374 → 35028: the 6 busiest corridors by off-plan homes (Westlands–Riverside, Kilimani, Kileleshwa, Lavington, Tatu City, Peponi Road)</li><li>"By completion year" (a label, not a link): chips 2026 · 2027 · 2028 · 2029+ → 41011's `/completion/<year>/` URLs, **with no counts** (41011's `bke-cy-count` spans are stripped in nav mode, because its term counts include rentals). "Ready now" moves to For Sale</li></ul> Muguga Green and Rhapta Road are not area terms; they stay reachable from 35028. | 12 |
| **Sell & Manage** | /sell/, Free Sales & Rental Valuation, Property Management | 3 |
| **Invest** | /investment-advisory/, Diaspora (36389) | 2 |
| **About** | /about-us/ and its children minus 38081. Contact Us (36392) stays, because the Contact pill hides at 1200–1279px | 8 |

About 56 links on desktop, and the same in the drawer.

### 3.7 Markup and behaviour

```html
<li class="menu-item bkh-item">
  <a class="bkh-top" href="/for-sale/">For Sale</a>
  <button class="bkh-tog" hidden aria-expanded="false" aria-controls="bkh-p-sale">For Sale</button>
  <div class="bkh-panel" id="bkh-p-sale">…hub link first in the DOM…</div>
</li>
```

**Without script**
- The top-level link goes to the hub.
- The panel opens on `:hover` (only under `(hover:hover) and (pointer:fine)`) and on `:focus-within`.

**With script**
- The link is hidden and the button is revealed: the WAI-ARIA disclosure pattern, not `role=menu`.
- Enter or Space toggles a panel. Escape closes it and returns focus to the button. Only one panel is open at a time. A click outside, or focus leaving the item, closes it.
- Hover opens a panel after about 120 ms and closes it 250 ms after the pointer leaves.
- **Hover then click pins.** The script records how a panel opened (`data-by="hover"|"click"`). A click on a panel opened by hover pins it open instead of closing it, which fixes `50-bkh-proto.js` line 10. A pinned panel ignores `mouseleave`, and closes on Escape, an outside click, focus leaving, or a second click.

**Where you are**
- `aria-current="page"` on the current link.
- The class `current-menu-ancestor` is set on For Rent for rent hubs, rent type pages, rent segments and rental listings, using the same test as the search form's default. It is set on For Sale for the sale equivalents.
- The underline rule is extended to `.current-menu-ancestor > .bkh-tog`.

**Accessibility**
- A 2px navy focus ring (white over the homepage scrim), with `!important`, because the theme strips outlines.
- Counts are read as "Westlands, 210 homes".
- Closed panels use `display:none`.
- `prefers-reduced-motion` is respected.
- Script adds `aria-label="Main"` to `nav#access`.

**Style**
- v1.6 tokens on the menu's own root: Montserrat, navy #0D2440, slate links, a paper #FBF9F6 panel with a #E6DFD4 hairline, and #9E7433 kickers. No Instrument Serif.
- Id-scoped `!important` overrides beat the live `#access a`, submit and input rules.
- Top-level buttons copy 41011's header-fit padding and the `body.home` white text.

**Phone drawer** (3 taps to any type or area: open the drawer, For Rent, Westlands)
1. A compact search form: For sale/For rent radios, Area, Search, and a "More filters" link to /advanced-search/.
2. A `<details>` per panel item. Inside For Sale and For Rent, each type is a 48px link row ("Apartments · 69"). Its areas follow as a wrapped row of inline links, each at least 40px tall, with no nested `<details>`.
3. The CTAs, with a drawer-only pill rule.
4. The theme's phone row (unchanged).

41011's `menu-item-has-children` class is stripped inside the drawer.

**UNCERTAIN: the drawer trigger.**
- If it is a `div`, a small script adds `role=button`, `tabindex` and `aria-expanded`.
- With no script, a `@media (scripting:none)` row under the 56px bar links "For sale · For rent · Search".

### 3.8 Width at 1200–1440px

| Change | Width |
|---|---|
| "For Sale" (+31px), "For Rent" (+27px), search icon (+38px) | +96px |
| Completion removed | −100px |
| **Net** | **about −4px** |

Optional "New Developments" adds about +75px, and then needs fallback 2 at 1200–1365px.

- **Today's margin is thin.** The logo note in 32088 measured the room at 1200–1320px as 100vw − 1029px: 171px at 1200px against a 160px logo minimum, so 11px spare for visitors. Logged-in users already overflow by about 37px.
- **Fallbacks:**
  1. Item padding 7px → 6px at 1200–1279px.
  2. Hide the Contact pill at 1200–1279px (Contact is also in About and the footer).
  3. Re-tune the logo clamp, and rewrite its comment with the new measurements.

  Fallbacks 1 and 2 are **on by default for `.logged-in`** at 1200–1279px. For visitors they are switched on only if measurement shows a wrap.
- **Panels drop below the header,** aligned to its inner container, at most 1080px wide, in four grid columns with the hub link first in the DOM. Panel height is about 300px. The Search panel is 380px wide, aligned right, with fields on a 2×2 grid.
- **Measure** at 1200, 1280, 1366 and 1440px, logged in and out, on the homepage and on a white page.

### 3.9 Weight and speed

- About 56 links on desktop and 56 in the drawer. Today's header probably prints four Elementor mega templates twice (**UNCERTAIN**), so the page should not get heavier.
- A warm build takes about 7 ms (desktop) and 2 ms (drawer).
- The menu varies by request only in `aria-current`, `current-menu-ancestor` and the search form's default Buy/Rent, all of which depend on the URL. Page caching stays safe.

## 4. The header search

### 4.1 The form

- **Desktop:** an icon-only item whose accessible name is "Search".
  - Without script it is `<a href="/advanced-search/">`, and its panel also opens on hover or focus.
  - With script it is a disclosure button.
- **Phone:** the compact form at the top of the drawer (§3.7).

| Field | Name | Values |
|---|---|---|
| Buy or rent | `for` | Radios. For rent is the default on rent hubs, rent type pages, rent segments and rental listings; For sale everywhere else |
| Type | `type` | **Any type (empty value, the default)**, Any home (`homes`), Apartments, Studios, Villas, Houses, Commercial, Land |
| Area | `area` | Any area, then areas with at least `search_min_area_count` listings, busiest first, then "Other areas" (`blockke_search_areas()`) |
| Bedrooms | `beds` | Any, Studio, 1, 2, 3, 4+ |
| Submit | — | "Search", overriding the site's uppercase submit rule |

- **Budget** is left out of the header. A development-name field waits for `search_loose`. The form never sends `elementor_form_id`.
- **Apartments with Studio.** `blockke_search_parse()` reads `type=apartments` with `beds=studio` as apartments plus studios. Today the type filter requires type `apartment`, so Westlands' 16 studio developments vanish. Such a search is never redirected to the apartments segment.
- The form submits a GET to `get_permalink( blockke_search_page_id() )`.

### 4.2 Where simple searches land (`search_redirect`, R3 write 3)

| Search | Lands on |
|---|---|
| One action, one area (area page on), one type | The segment's canonical when that is a segment page (Kileleshwa apartments for rent → homes-for-rent), carrying beds, price and sort |
| One action, one type, no area, with `type_pages` on | The nationwide type page, carrying beds, price and sort as query arguments (a noindex view) |
| One action and nothing else | The hub |
| Any type (empty) with an area | Stays on the results page, which shows every kind. This follows the docblock at v1.8.2 lines 14446–14450 |

- **Campaign tags** kept across the 302: `utm_*`, `gclid`, `gbraid`, `wbraid`, `fbclid`, `_gl`, **`msclkid` and `srsltid`**.
- **Bands:** the redirect reads both the old values (`8-12`) and the new prefixed ones (`s8-12`, `r80-150`), and writes the segment page's own format. Today a price fails `isset( $bands[…] )`, so a search with a price never redirects.

### 4.3 The theme's own search bar (`theme_search_bar`: false / 'hide' / 'ours')

- If view-source shows WPResidence printing its bar under the header (`search_wrapper`, `adv-search-1`, `sticky_adv`, `adv-search-header-mobile`), this key either hides it with page-scoped CSS, or prints our GET bar in the same slot on the page types where the theme showed it.
- It is its own key, with its own preview (`?bke_tsb=1`) and the owner's approval (decision 7), because it is a visible change below the header on sale development pages.
- The site-wide theme options are not touched.

### 4.4 The existing search boxes

1. **R0a, config only.** Store `search_ignore` as the default list plus `elementor_form_id`, `geolocation_lat`, `geolocation_long`, `utm_id`, `msclkid` and `srsltid`.
   - The defaults must be repeated, because the merge is top level.
   - `price_label_component` is deliberately not added: /nairobi-properties/'s second price pair (KES 0–50M) would silently drop every home above KES 50M.
   - **Check first** with the admin-bar note on six searches:
     1. the blank hero;
     2. hero Westlands apartments;
     3. the /for-sale/ form;
     4. a blank /for-rent/;
     5. /for-rent/ with "Kilimani" typed (still the theme);
     6. a hero keyword (still the theme).

     Also check /new-developments/ carrying one of the new ignored keys: `search_ignore` also decides that page's noindex and canonical (line 12530), so it must stay indexable and self-canonical.
2. **R2.** The hubs take over /for-sale/ and /for-rent/, so their Elementor forms are no longer drawn.
3. **R5, `hero_search`** (preview `?bke_hero=1`).
   - On the server, the output of hero widget `aeb11da` is swapped for our bar: through Elementor's widget render filter, or, if Elementor's element cache defeats that, in the page's output buffer.
   - Page 18618 and `ihaf_insert_footer` are not edited. Read the "BKE hero search v2.0" script first, and leave out the markup it hooks onto, so it does nothing.
   - Kill switch: one option write, plus a purge of `/`.
4. **R5, `search_loose`** (preview `?bke_search=1`):
   - strips ", Nairobi", spaces and hyphens ("Upper Hill" becomes upperhill);
   - **only "Nairobi" or an empty value means any area.** Another city or county maps to its curated region's areas (the `region` field in `sale-inventory.json`). Otherwise parse returns null and the theme takes the search;
   - a `property_status[]` value with no exact mapping also returns null;
   - reads `geolocation_search`;
   - a keyword that names a development gets a 302 to its page, one that names an area gets a 302 to its area page, and anything else filters by name;
   - baths are read as a minimum; `paged` becomes `n` and `order` becomes `sort`;
   - **the theme's page is used only when nothing matches.**
5. **Map.**
   - `search_map_page` = 0 hides the "Map" link (R5).
   - /map-search/ gets a 301 to /advanced-search/, with the query kept (`redirects` pages family).

**What stays on the theme:** the "Refine your search" and "Find Your Property" sidebars (they land on our page when they can be read), and /nairobi-properties/ until R6.

## 5. Results and archive pages

### 5.1 What replaces each theme page

| Page today | Replacement | Switch | Release | Index and sitemap |
|---|---|---|---|---|
| /advanced-search/ fallback | Ours for readable searches; the theme fallback stays for the rest. Its `page-template-advanced_search_results` body class is dropped | existing; `search_bands` (body class) | R0a, R3, R5 | noindex |
| /for-sale/ (29678) | Our For Sale hub (§6.2) | `sale_hub` | R2 | index; filtered and `?n=` views noindex |
| /for-rent/ (29769) | Our For Rent hub (§6.1) | `rent_hub` | R2 | index |
| /apartments-for-sale/ (31708) | Nationwide apartments for sale, with a `#studios` section | `type_pages` | R2 | index |
| /for-sale/villas-for-sale/ (31715) | Nationwide villas (`#villas`) and houses (`#houses`) for sale; its FAQ is kept | `type_pages` | R2 | index |
| **New child pages:** /for-rent/apartments-for-rent/, /for-rent/villas-and-houses-for-rent/, /for-rent/commercial-for-rent/, /for-sale/commercial-for-sale/, /for-sale/land-for-sale/ | <ul><li>Real WordPress pages, created as drafts in R2 and published when switched on</li><li>Taken over by ID, like 31708</li><li>Check first that each slug is free</li><li>While `type_pages` doesn't cover a published page, that page sends a 302 to its hub</li><li>Rollback: switch off, or set the page back to draft</li></ul> | `type_pages` | R2 | index at 5 or more cards; below that, `noindex, follow` with the canonical on the hub |
| /neighbourhoods/ (31733) | An A–Z directory of areas: each with its area page link and its guide link (15 guides). No listing grid, no counts | `type_pages` | R2 | index |
| /2-bedroom-apartments-for-sale-kilimani/ (38136) | Kilimani apartments with 2 beds preset, self-canonical. Linked from Kilimani's entry on the sale hub | `type_pages` | R2 | index |
| /for-sale/apartments-for-sale-westlands/ (38125) | Its copy prints under the Westlands apartments-for-sale standfirst, on the plain view only (`seg_intro`). Then 302, and 301 after two weeks, to that segment. Rank Math noindex on 38125 the same day | `seg_intro`, `redirects.pages` | R2 | out of the sitemap |
| `/property_action_category/for-sale/` and `…/for-rent/` (+ `/page/N/`) | 302 to /for-sale/ and /for-rent/ from the day each hub is on; 301 in R5; then the sitemap is switched off | `redirects.action` (fires only while its hub is on) | R2, R5 | sitemap off |
| `/property_category/<x>/` | <ul><li>apartments → /apartments-for-sale/</li><li>studio-apartments → /apartments-for-sale/#studios</li><li>villas-residential, houses-residential → /for-sale/villas-for-sale/</li><li>residential, gated-community → /for-sale/</li><li>commercial, offices, warehouses, shop → /for-rent/commercial-for-rent/ (28 of the 35 are rentals; the page cross-links commercial for sale)</li><li>land family → /for-sale/land-for-sale/</li></ul> | `redirects.taxonomies` | R5 | sitemap off |
| `/property_county_state/<x>/` | nairobi → /for-sale/ (not the 771-listing theme city archive). Others → the same-named city archive, else the area | `redirects.taxonomies` | R5 | sitemap off |
| `/property_status/<x>/` | <ul><li>offplan-ongoing, ongoing → /new-developments/</li><li>complete → /completion/ready-now/</li><li>furnished → /for-rent/</li><li>sold → /for-sale/</li></ul> | `redirects.taxonomies` | R5 | sitemap off |
| `/project/<slug>/` | The development's main listing. **A computed target: stays 302 for good.** Empty terms → /new-developments/ (301) | `redirects.taxonomies` | R5 | sitemap off |
| `/property_features/<x>/` | `noindex, follow` | `noindex_features` | R5 | sitemap off |
| /properties/ (29547), the /property/ archive | 301 to /for-sale/; Rank Math noindex on 29547 the same day | `redirects.pages` | R5 | out |
| /completion/ hub (41012) | 301 to /new-developments/; Rank Math noindex the same day. The year pages stay until R6 | `redirects.pages` | R5 | out |
| /map-search/ | 301 to /advanced-search/, query kept | `redirects.pages` | R5 | — |
| /search/ (39161) | Unlinked | `search_map_page` 0 | R5 | noindex |
| /nairobi-properties/ (93) | Rank Math `noindex, follow` once `sale_hub` is on; its content is for R6 | owner setting | R2 | out |
| Drafted duplicate listings (batch D) | 301 to the kept listing | `redirects.listings` + `redirect_ids` | R1 | — |
| Agent taxonomies | Sitemaps off | Rank Math | R5 | noindex |
| `/property_city/<x>/`, `/completion/<year>/` | Our layout, later | later | R6 | unchanged until then |

**Redirect safety**
- Get the owner's export of Rank Math → Redirections **before R2**, because the read tools can't see that table.
- Dry run: `?bke_redir=1` as an editor shows "would 302 to X" in the admin bar, without redirecting.
- Targets are worked out at request time. A redirect whose target's own switch is off is skipped, so nothing chains through 301 → 302 → an Elementor page.
- 302 for two weeks, then 301 **only for fixed targets.** Computed targets (project → listing, county → area fallback) stay 302.
- When a published page starts redirecting, Rank Math noindex is set on it the same day.
- The owner changes Rank Math's sitemap toggles by hand, after the redirect or noindex is live.

### 5.2 SEO on every page we take over (hubs, 31708, 31715, the 5 new pages, 31733, 38136)

- The index's full override set (v1.8.2, lines 12536–12600):
  - the document title;
  - Rank Math's title, og and twitter titles;
  - the description ×3;
  - canonical and `og:url`;
  - `wp_robots` and `rank_math/frontend/robots`;
  - adjacent links off;
  - the og image through `pre_set_content_image`, so no image or description is taken from the stored Elementor content (/for-rent/ holds "San Jose, California" placeholder copy).
- Rank Math's stored title and description are kept when they fit.
- **JSON-LD** through `rank_math/json_ld`:
  - WebPage retyped to CollectionPage;
  - an ItemList of the cards shown (url, name, position);
  - BreadcrumbList replaced by the visible trail;
  - Article dropped.
- `status_header(200)`.

### 5.3 Sitemap (`sitemap_extra`, R2)

- One Rank Math sitemap provider (confirm the `rank_math/sitemap/providers` filter on the installed version first).
- While the owning keys are on, it emits:
  - each segment page where `blockke_area_page()['index']` is true and the canonical equals the URL;
  - lastmod from the rows' rebuild time.
- `rank_math/sitemap/entry` drops any published nationwide page that is noindex.
- It never emits a noindex or non-canonical URL.

### 5.4 Internal links to retired URLs

- **With `type_pages` on,** the index's "N commercial developments" and "N land projects" links (v1.8.2, line 12084) go to /for-sale/commercial-for-sale/ and /for-sale/land-for-sale/, not to the category archive that R5 sends to rentals. The index is not a development page.
- **`crumb_hubs`** (R5, after the action redirect is 301; decision 6): a `term_link` filter for the for-sale and for-rent action terms points every breadcrumb (ours, the classic page's and Rank Math's) at the hubs. One href changes per listing page.
- **The corridor pages' `/property_status/offplan-ongoing/` links:** repointed in R6, with snapshots. The 301 covers them until then.

### 5.5 What SSR guarantees on every results page

- **Filters** are `<form method="get">`, applied by the server. On phones without script, `@media (scripting:none)` and `:target` show them.
- **Paging** uses `?n=` links, `nofollow`, and the views they open are noindex.
  - **Type pages also print an A–Z of every matching development as plain links,** as the index does. /apartments-for-sale/ (262 cards) then links every development without script, not only the first step.
- **Counts, cards, prices and empty states** are in the first response.
- **Budget bands** (`search_bands`, R3) get distinct values (`s8-12`, `r80-150`):
  - both sets are printed in `<optgroup>`s;
  - the server ignores a band that does not match `for`, and takes `for` from the band when `for` is empty;
  - old values are still read.
- **Fallbacks:**
  - a hub or type page with no rows, or that throws an error, gets the theme page back;
  - the results page keeps its theme fallback, and the admin bar names the reason.

## 6. The hubs and nationwide pages

### 6.1 /for-rent/ (rent_hub; today's figures)

1. **Hero.**
   - Kicker "For rent". H1 **"Homes and offices for rent in Nairobi and nearby"** (`index_metro`).
   - Lede: "{82} homes and {27} offices and warehouses listed for rent. Ask us to confirm availability and arrange a viewing. Rents are shown as the landlord quotes them, mostly a month."
   - The GET bar with the rent bands.
   - Type chips: Apartments 69 · Studios 4 · Villas 2 · Houses 2 · Commercial 27.
2. **Apartments for rent.**
   - Line: "69 homes, from KES {min} to KES {max} a month", computed from monthly rents only.
   - Six cards **with a photo** (`img` > 0 and `ph` ≥ 3, newest first).
   - Area chips at each area's canonical.
   - "See all".
3. **Villas and houses for rent** (`#villas-houses`): all of them, linking the shared page.
4. **Offices and warehouses for rent.**
   - Line: "Most office rents are quoted per sq ft a month; each card shows the rent as quoted."
   - Chips: Offices · Warehouses · Westlands.
   - No budget filter or price sort in this section.
5. **Short stays.** Any rental priced by the day or week (today: 44020, "KES 6,500 a day"), shown in one line, outside every range.
6. **Rent by neighbourhood** (`#neighbourhoods`):
   - every area with a rental, **ordered by figure**;
   - each links its homes-for-rent or commercial-for-rent canonical when indexable, else the area page;
   - the guide link where one exists.
7. **Renting with Block:** `rent_why`, word for word ("We help you check the tenancy agreement, the deposit and what the rent covers before you pay or sign.").
8. **"Own a property to let? We find tenants and manage it"** → /property-management/.
9. **FAQ** (`<details>`):
   - "What do I pay before moving in?";
   - "Can I view before committing?";
   - "Furnished or unfurnished?";
   - "Why are some rents in US dollars?" Answer: the landlord sets them; we show the KES figure first, then the dollar rent, with the rate and the date we used.
10. **Enquiry, "Tell us what you're looking for".**
    - Fields: name, phone, area, type, **bedrooms**, budget (rent bands), move-in (This month / 1–3 months / Later), message.
    - A "Size needed (sq ft)" field shows when Type is Commercial. This uses CSS `:has()`, with no script.
    - It posts as topic `for-rent` and arrives as "[Block] Rental enquiry: {name} - For Rent page".
    - It posts without script from day one (`forms_post` includes 'new').
11. **Disclaimer:** `rent_disclaimer`.

### 6.2 /for-sale/ (sale_hub)

1. **Hero.**
   - Kicker "For sale". H1 **"Property for sale in Nairobi and nearby"**.
   - Lede: "630 homes in 313 developments, plus 7 commercial and 15 land listings. Price lists, floor plans and brochures for new developments on request." The brochure line has the owner's OK.
   - The bar with the sale bands.
   - Chips: Apartments 522 · Studios 45 · Villas 36 · Houses 17 · Commercial 7 · Land 15 · Ready to move in.
2. **Apartments for sale.** Six cards with photos, area chips at the canonicals, and a link to /apartments-for-sale/.
3. **Villas and houses.** Cards, chips Lavington · Karen · Runda, and a link to 31715.
4. **New developments.** Cards from the index's lead picks, completion chips, and a link to /new-developments/.
5. **Commercial and land.** Cards, and links to the two nationwide pages.
6. **Neighbourhoods** (`#neighbourhoods`).
   - Grouped by a curated region, then ordered by figure. Each area links its homes-for-sale canonical and its guide.
   - Kilimani's entry also links "2-bedroom apartments" (38136).
   - A compact "More areas" line at the end.
7. **Why Block:** `area_why`, which opens "Advice, not pressure". Not `why`, which opens "Independent advice".
8. **Enquiry, "Get price lists, floor plans and brochures".**
   - Fields: name, phone, area, type, **bedrooms**, budget, timeline.
   - Topic `for-sale`. It posts without script from day one.
9. **Disclaimer:** `area_disclaimer`.

If the owner wants the 21:46 /for-sale/ edit kept, its copy becomes the hub intro (decision 3).

### 6.3 Shared hub rules

- **Takeover by ID** through `template_include`, as `dev_index` does. Preview `?bke_hub=1`. The Elementor content is kept, not drawn.
- **Titles and descriptions:** "Property for Rent in Nairobi | Block Real Estate" and "Property for Sale in Nairobi | Block Real Estate". Descriptions carry live figures and ranges.
- **Index rules:** the canonical is the page itself, and any query makes the page noindex.
- **Cards:** the v1.6 family, with the rent card rules of §7.4.

### 6.4 Nationwide type pages

- **Built with** the search renderer for `for=X&type=Y`, but with their own H1, title, description, canonical and index rule (§5.2).
- **Each page carries:**
  - the filter bar;
  - `?n=` paging;
  - the A–Z list of developments;
  - type chips and area chips;
  - the enquiry form and the disclaimer;
  - JSON-LD.
- **Studios:** both nationwide apartment pages get a `#studios` section. The H1 and menu figure stay the apartment count.
- **Cross-link under the H1:** "Looking to rent? 69 apartments for rent →" and "Offices for sale (2) →".
- **Commercial for rent:** no monthly budget filter and no price sort. It offers Offices / Warehouses and size instead. Per-sq-ft cards are kept, never dropped by a band.
- **Below 5 cards:** `noindex, follow`, canonical on the hub, and the menu heading links the hub's section anchor.

### 6.5 /neighbourhoods/ and 38136

- **/neighbourhoods/:** an A–Z directory of every area with listings, each with its area page link and its guide link. No counts, so it doesn't duplicate the hub lists.
- **38136:** Kilimani apartments with 2 beds preset, self-canonical.

### 6.6 Area pages: guides and the 38125 copy

- **`area_guides`** (R2): a map of 15 rows, term slug → guide page ID (`upperhill` → /neighbourhoods/upper-hill/). When the key holds the map, each area page prints "Read our Westlands guide". The hub neighbourhood lists use the same map.
  - Area pages are not development pages; the empty map is the off switch.
  - Swapping each guide's Items_List widget for a link back to its area page is an R6 page edit.
- **`seg_intro`** (R2): a map from segment to page, printed under the segment's standfirst on the plain view only. It starts with `westlands/apartments-for-sale` → 38125.
  - Drafts 44095 and 44096 are added **only if whoever owns them agrees**. We never edit, publish or trash them.

## 7. Rental pages

### 7.1 The development layout in rent mode (R1, v1.8.3)

**Starting point:** `rent/understand/rebase/merged-resolved.php`, md5 b1cf4134.
- **Version number:** set at staging, after checking the live snippet list (§10.1).
- **Renumbering:** "v1.8.2" in the rental work is renumbered in its 32 places. The similar-block cache suffix becomes `|v183r`.

**The switch is `rent_pages`** (preview `?bke_rent=1`). It is renamed from `rent_mode`, which clashed with the function `blockke_dev_rent_mode()`.
- **One helper:** `blockke_dev_rent_on( $id )` = the key is on AND `blockke_dev_rent_mode( $id )`.
- **Gating audit:** grep every `'rent' ===` in the diff from v1.8.2 (for example `merged-resolved.php` 3761, 3767, 3969, 6008), and route each through the helper.
  - With the key off, the similar-block key carries no suffix.
  - Proof: `rebase/h` on the rentals' pages and their `?lp=1`.

**What it gives (R1–R20 from `build.md`)**
- "For rent" and "Rent / month", with the stated Availability or Viewing.
- A Renting section in place of the mortgage, with **Arrange a viewing**.
- Viewing forms, and `plans` false.
- Move-in questions, `rent_why`, "Questions tenants ask", `rent_disclaimer`.
- No "Ready now" badge on similar rentals.

**Rents in other units**
- Other periods read as written.
- Per-sq-ft rents read "KES 130 per sq ft a month".
- **A rental with no price** reads "Rent on request". The WhatsApp line drops the brackets, and no Offer is emitted.

**USD rents follow rules v2: KES first.**
- Example: "KES 710,000 a month (USD 5,500)", followed by "The rent is set in US dollars; KES figures are approximate, at about KES 129.2 to the US dollar on {date}."
- **Source:** `bke_rent_usd` is the one source of truth, read before the glance row. `bke_rent_usd_date` gives the date.
- The KES clause appears only when both are set and the price field agrees at KES 80–200 to the dollar.
- The lead line matches: "Rental: Antara Forest Edge - KES 710,000 a month (USD 5,500)".

**The review's open items**

| Item | Fix |
|---|---|
| 4 (both-tagged) | Data (decision 5) |
| 8 (wording) | Strip "to Let" from page names in the new sentences. Give the brochure thank-you its own wording. **Hide "For investors" on rentals** |
| 9 (inbox, Zoho) | §7.5 |
| Missed rentals | The title fallback is widened to "to rent" and "for lease" (excluding "Rent-to-Own"). It lives **in `blockke_dev_action()` under `rent_pages`**, and `blockke_bkn_ver()` gains `|rp` while the key is on, so the area pages and the layout agree and nothing changes with the key off. A pinned test shows only 44049 changes action today, and none after batch R |

**Which rentals use the layout**
- The 12 with `bke_dev_layout=on`.
- The three Runda rentals (35583, 42616, 43543), after 42616 gets its `bke_dev_name` (decision 4). Today its H1 would read "Villa for Rent", from junk Project term 1129.

**Tests**
- Rebase `runda/code/tests/rent.php` (121 checks) onto a database built by `runda/tools/import.php`, with the database name parameterised.
- Judge its "as v1.8.1" sale checks against v1.8.2.
- `rebase/h`: 1,846 sale renders and 1,694 sale leads equal to v1.8.2, and the rentals mapped to R1–R20.
- Merge `runda/code/readme-addition.md` into the README.

### 7.2 The ad landing page guard (`lp_rent_guard`, default on, R1)

**What it refuses.** `blockke_dev_lp_ok()` also refuses:
- any listing the rows read as a rental;
- any listing whose price label names a period (month, week, day, night, year, annum);
- a listing tagged For Rent whose price is under KES 1M and not per sq ft;
- a listing with no Buy/Rent term whose title says "for rent", "to rent", "to let" or "for lease".

**Not tied to `rent_pages`.** It ships on, because rentals never get ad landing pages. Switching it off is only for an emergency rollback.

**Where the same test applies:** `bke_lp_listing` pages, the editor box and the admin bar.

**Pinned test.** On today's data, `lp_ok` changes only for:
- 37243;
- 37258;
- the no-term "to Rent" titles;
- MJ1 39502, until batch S labels it "per sq ft".

After batches R and S it changes nothing. This list is T1's only exception.

### 7.3 The classic WPResidence rental page (R1, `classic_inplace` = 'rent')

**Rent summary box.**
- **The rent with its period:**
  - "KES 650,000 a month";
  - "KES 710,000 a month (USD 5,500)";
  - "KES 130 per sq ft a month";
  - "KES 6,500 a day";
  - "Rent on request".
- **Then the stated terms,** from `blockke_dev_rent_info()`.
- **Then "Arrange a viewing":**
  - name, phone and move-in;
  - WhatsApp ("I'm interested in renting {name} ({rent}). Is it still available, and when could I view it?"; no brackets when there is no rent);
  - a call link.

**Placed in the page by the server.** An output buffer on `template_redirect` places the box before `#accordion_features_details`. The amenity tiles go into that accordion's body, and *What's nearby* into `#accordion_property_address`. The `hidden` wrapper and the move script go.
- If an anchor is missing, that block falls back to today's route (printed after the footer and moved by script), and the admin bar says so.
- A higher anchor (the description) is used once view-source confirms its id.

**Phone action bar.** On classic rent pages below 992px, a fixed bottom bar printed in `wp_footer` holds the rent with its period, WhatsApp and "Arrange a viewing" (→ `#bkd-rent`). Before building it:
- check `ihaf_insert_header`'s mobile CTA in view-source, and replace its contents on rent pages rather than stacking two bars;
- look for the theme's own agent or "schedule a tour" form. If there is one, hide it on rent pages, or label its leads as rentals.

**No mortgage.** Server-printed CSS hides `#accordion_property_payment_calculator` on rent pages, through a `bkd-rent-cls` body class.

**The bare price on the classic page** is fixed by data (batch R, the B1 labels).

### 7.4 Rental cards everywhere (`rent_cards`, R1)

This covers cards on area pages, segments, search, hubs and type pages, and similar rentals. The row cache gains `|rc` while the key is on, and so does the similar-block cache key, so switching takes effect at once and the keys-off bytes stay as today.

**How a rent reads**
- "a month", never "/ month".
- **A period flag.** With the key on, `blockke_dev_price_flag()` returns `'period'` for a label with day, night or week. Sale rows never carry such a label.
  - **Where period rows are kept out** (as `'unit'` rows already are): `rfrom`/`rto`, area and hub ranges, budget-band matching and price sorting (they sort last).
  - **Where they stay:** they are still counted and shown as cards, as "KES 6,500 a day".
- **Per-sq-ft rents are shown,** from the row's `p` and `f`: "KES 130 per sq ft a month".
- **USD rents** are shown KES first ("KES 711K a month (USD 5,500)"), from `bke_rent_usd`, which is added to the cache-bump meta list.
- **No price** reads "Rent on request".

**What a rent card adds or changes**
- **A "Furnished" tag** comes from the feature, the status or `rent_info`'s Furnishing.
- **A rent card never links to a sale page.** Its URL is the development's rental (`blockke_bkn_for_act()`). "See all N in the price list" becomes "See all N rentals".
- **Search WhatsApp:** "Please send current options and arrange viewings."
- **A per-sq-ft-only commercial segment** states its rent per sq ft.

**Search rules for rent rows only**
- **Range rentals** (35945, 39456, 39421, 37551, 34301, 35498, 36499): when both beds and price are set, a range row passes the budget only if the bedrooms asked for are its lowest.
- **Hub and section picks** need a photo (`img` > 0, `ph` ≥ 3). The "See all" pages still list everything.

### 7.5 Rental leads

**Subjects.** Every rental lead arrives as "[Block] Rental enquiry: {name} - {what}".

| Source | Description line | Form |
|---|---|---|
| Layout page | "RENTAL ENQUIRY (property page)" | "Hero form (viewing)" / "Viewing request" |
| Classic rental box | "RENTAL ENQUIRY (classic page)" | "Classic page (viewing)" |
| Rent area or segment page | "AREA ENQUIRY … Looking to: Rent" (the subject moves from "Area enquiry" to "Rental enquiry") | — |
| Rent hub and rent type pages | topic `for-rent` | — |

**Saved enquiries.** Rental rows gain `'kind' => 'rent'` in `blockke_dev_leads`. Sale rows are unchanged.

**Inbox**
- **Read live 42847 first.** The repo file is the 10 Oct copy, and live changed on 11 Oct. Diff the two and rebase.
- **Stage the new inbox as a new inactive snippet** at a lower priority number. Its body is guarded by `function_exists( 'blockke_inbox_recipients' )`, so whichever is active first wins. Activate it on purpose, and keep 42847 active as the fallback.
- **What it adds:** a Type column (Sale, Rental, Area, Index, Topic), "Move-in" in place of "Timeline" on rentals, no "Buying to", and a Type column in the CSV.
- **It ships before `rent_pages` is switched on.**

**Zoho CRM (owner, in the admin)**
- An **optional** Leads picklist "Enquiry type" (Sale / Rental).
- Rental leads send "Company: Individual tenant" instead of "Individual buyer". The code supplies both values per lead.
- The owner makes the mapping change in WordPress admin, not through a tool write, because the option also holds the Zoho tokens. Snapshot first, then send one test lead.

**Zoho Mail (owner):** a filter or label for "[Block] Rental enquiry". Check every rule keyed on "[Block] Development enquiry" or "DEVELOPMENT ENQUIRY".

### 7.6 Structured data (`listing_jsonld`: 'rent' in R1, true in R4)

**Rentals (layout and classic pages)**
- **The page node.** Rank Math's WebPage node is retyped to `RealEstateListing`, as the index retypes it at line 12483, with `datePosted` and `image` (every photo). Block stays as Rank Math's publisher. No second page entity is added.
- **`about`: the home.**
  - Apartment, House or SingleFamilyResidence, with bedrooms, floor size and address to area level.
  - Commercial listings use `Place`, with the size as `additionalProperty`.
- **`offers`:** an Offer with `businessFunction` LeaseOut, and the price and currency **as the lease is set**. USD is used only after batch R sets `bke_rent_usd`.
  - A `UnitPriceSpecification` carries the period: `unitCode` MON, DAY, WEE or ANN. Per sq ft uses `unitCode` FTK, `referenceQuantity` 1 FTK, and `unitText` "per sq ft per month".
  - "From" prices become an AggregateOffer.
  - **No `availability`** (except `availabilityStarts` when an "Available from" row is stated), **no `seller`**, and no Offer when there is no rent or it is "on application".
- **BreadcrumbList** is replaced by the visible trail.

**Sale listings (true, R4, decision 6)**
- An Offer with `businessFunction` Sell, priced at the visible hero price.
- "From" prices become an AggregateOffer (`lowPrice`, `highPrice`, `offerCount` from the residences rows).
- No Offer on "Price on request". No availability or seller claims.
- Listings whose content already holds `RealEstateListing` are skipped (the 5 villas).
- The containment test is head-only.

## 8. Switches that also touch sale pages (decision 6)

| Switch | What it does | Containment test | Staging |
|---|---|---|---|
| `forms_post` (false / list / true) | <ul><li>`form.bkd-form` gets `method="post"` and `action="/wp-json/block/v1/development-lead"`, plus hidden context fields.</li><li>A form-encoded post maps `bkd_hp` → `hp`, runs `blockke_dev_handle_lead()`, and answers 303 to the page (same host only) with `?bkd_sent=1#bkd-enquire` or `?bkd_err=…`.</li><li>The thank-you is drawn on the server, noindex and no-cache.</li><li>The pop-up triggers become `<a href="#bkd-enquire" data-bkd-enquire>`.</li><li>Script leads stay byte-identical.</li></ul> | The only HTML differences are form open tags, hidden inputs and trigger tags. 1,694 script leads are identical. A no-script POST from each page type gives one lead. The rate limit and honeypot still work | ['rent'] R1; +'new' (hubs, type pages, 31733, 38136) R2; true R4 |
| `classic_inplace` | Classic extras printed in place by the buffer, with today's route as the fallback | Only the moved blocks differ, and their bytes are equal | 'rent' R1; true R4 |
| `area_rent` | **Area pages mention rent,** only for areas with rent rows: <ul><li>titles and descriptions through Rank Math's filters, with the 60-character rule and the area's real city (Syokimau is Machakos). Example: "Apartments & Houses for Sale & Rent in Westlands", dropping the brand past 60 characters, after a Search Console check of what these pages rank for;</li><li>the bottom budget select offers both band sets in `<optgroup>`s.</li></ul> | Sale-only areas are byte-identical | code R2; on R4 |
| `gallery_ssr` | Photos 6+ printed as lazy `<a><img loading=lazy>` links | Gallery block only | 'rent' R1; true R4 |
| `listing_jsonld` | §7.6 | Head only | 'rent' R1; true R4 |
| `crumb_hubs` | Action-term links → the hubs (§5.4) | One href per listing page | R5, after the action redirect is 301 |
| `search_bands` | Distinct band values | Results page only | R3 |
| `list_jsonld` | ItemList and BreadcrumbList on indexable segments | Head only | R5 |

## 9. Data fixes

**Before every write**
- Compare `post_modified` with the planning read.
- **Skip anything modified on 22 Oct** until whoever made that change confirms. Today that means 44048–44051, 37243, Bahari 33933, 33932 and 33909, and anything in §10.1's do-not-touch list.
- Log the value before each write; undo by replaying the log.
- Run each batch in one low-traffic window, then warm the caches.

### Batch R: rental listings (R0b, before R2 shows figures)

| # | Listings | Change |
|---|---|---|
| R1 | 44051, 44049, 43819, 43656, 43482, 37494, 36662 | Add For Rent. 36662 is For Rent only; if it is also for sale, staff create a separate sale listing |
| R2 | 37243 (remove For Sale); 37258 (For Rent in place of For Sale) | One type per listing (decision 5) |
| R3 | 44049 Kileleshwa · 43819 Loresho · 43656 Westlands · 37494 Kitisuru · 36792 Riverside · 36662 Mombasa Road · 36135 Tatu City · 36038 Mombasa Road (confirm against Industrial Area) · 42370 Ridgeways only if staff confirm | Add an area |
| R4 | <ul><li>44051, 44049, 43819, 43656, 43482, 37494 → Apartments</li><li>36814 → Offices</li><li>42616 → Villas</li><li>40490 → Houses</li><li>37551 → Apartments (remove Offices)</li><li>43543 → Houses (remove Villas)</li></ul> | Categories |
| R5 | 43995, 41690, 41650 → Lavington (remove Kilimani); 37099 → Kilimani (remove Westlands) | Areas |
| R6 | Every rental carrying a `property_completion` term (the list is produced before the write) | Remove the term |
| R7 | 42616, 40490, 42370 | `bke_dev_name`, as staff name it. This avoids "Villa for Rent", "4 Bedroom" and "Windsor 2 bedroom" as H1s and card names. The junk terms are left alone |
| B1 | <ul><li>The 20 unlabelled rentals (43543, 43450, 42616, 42592, 42591, 42590, 42450, 42449, 42370, 41562, 40248, 35583, 35477, 35398, 31842, 29462, 28919, 28868, 36739, 33776)</li><li>The 10 labelled "Per Month"</li></ul> | Label "per month" (rules v2) |
| B2 | 35583 (USD 5,500), 34171 (USD 2,600), 36816 (USD 1.30 per sq ft) | Set `bke_rent_usd` and `bke_rent_usd_date` |
| B3 | 40275 | Label "per sq ft". (44020 already says "per day": no write) |
| B4 | 44048, 43656, 43523, 31842 | Bedrooms |
| B5 | 43788, 43583, 39138, 36739 (glued attachment ids); 43656, 43482 (no featured image) | Repair the gallery or featured image |
| B6 | <ul><li>The 13 rental titles giving KES with no period, and the stray pipes (43583, 43803)</li><li>The Rank Math SEO titles of 42616 (stray spaces) and 43803 (trailing pipe)</li></ul> | Retitle to the rules v2 pattern "{Home} for Rent in {Area} \| KES {rent} per Month" |
| B7 | 44020, 43833, 43482, 43453, 42292, 41946 (and check 40248) | The Furnished feature |

### Batch S: sale listings (R0c)

**First:** apply the batch to a private copy holding those listings' live state. Run `rebase/h` and the full-URL comparison. Send the owner the list of every sale development page and `?lp=1` that changes (decision 4).

| # | Listings | Change |
|---|---|---|
| S1 | 43739, 39502, 34618, 32712, 31413, 31265, 30512; and 33933, 33932, 33909, 31883 (sale-and-rent; staff add rental listings) | Remove For Rent. The status chip on these pages then reads "For sale" |
| S2 | 43162 Kilimani · 34735 Lang'ata · 33490 Tatu City · 33981 Ngong · 36610 Vipingo (also Commercial and a city) · 33336, 38352 (staff name the area) | Add an area |
| S3 | <ul><li>35221, 35232, 35236, 35536 → Villas</li><li>35651, 35663, 35667, 35581, 36274, 35134, 27457, 27468, 43162 → Apartments</li><li>33132 and 39015 → remove Houses</li></ul> | Types |
| S4 | 39502 | Label "per sq ft" |

### Batch D: duplicates (after R1 ships `redirects.listings`)

- **Staff decide which copy to keep:**
  - Versailles: 44048–44050, today's layout-on listings with no photos, against 42590–42592 from 7 Oct;
  - Westlands Cottage 44051, 43656 and 43644;
  - Loresho Oasis 43856 and 43819;
  - Skypark 40275 and 36814;
  - Jamesborough 43833 and 43482 (first confirm they are not two units).
- Each removed copy is drafted, and its URL 301s to the kept listing (`redirect_ids`) in the same session.
- **Project terms are merged by retagging only.** Terms are never deleted, and the empty term's `/project/` URL is handled by the R5 project redirect.

### Staff work (C)

| # | Work |
|---|---|
| C1 | Separate rental listings for Bahari, Brookside Drive and Loresho Springs where the rent is current |
| C2 | The area mismatches in D5 |
| C3 | <ul><li>D9 status gaps</li><li>Samsara 42206 / 33782</li><li>Merge `ongoing` into `offplan-ongoing`</li><li>33230's Off-Plan status</li><li>City terms (D6)</li></ul> |
| C4 | Per-layout rents for the 7 range rentals |

**Removed from our list:** the old C4 (drafts 44095–44097). They are not ours to change (§10.1).

After batches R and D, the trees in §3.4 and §3.5 are recomputed with the Python copy and checked against the live rows before R2.

## 10. Going live

### 10.1 The pre-write gate (every live write)

1. **Other writers are active today.** The evidence:
   - Diamond Bay units 44065–44068;
   - snippet 44071 "Ad landing page speed v1";
   - the 21:46 /for-sale/ edit;
   - drafts 44095–44097 at 21:58;
   - 44048–44051;
   - the Samar and Bahari edits.

   Before each write, list the other agent sessions (ListAgents) and tell any active one the exact object (snippet, option or post ID), or ask the owner. Wait for a reply.
2. **Do-not-touch list:**
   - 30741 and 44065–44068;
   - 44071;
   - 44095–44097;
   - 29678's stored content (the hub only takes it over).
3. **Snippets:**
   - Just before staging, and again before Activate, list the active WPCode snippets and md5 the active development snippet.
   - It must be b5295848, or the last version we shipped. If not: stop, rebase, re-run T1, and choose a version number nobody else has used.
   - **Never edit an active snippet in place through the API.** It would go live whenever anyone clicks Update (README:361).
4. **Options:**
   1. Re-read `blockke_dev_page` just before writing.
   2. Diff it against the planning snapshot, and abort if any other key changed.
   3. Change one key and write.
   4. Read it back and check the exact JSON types. `area_pages` must stay boolean `true`, because `blockke_area_on()` tests `true ===`.
   5. Log the before and after values, and tell the other writer.

### 10.2 Each release

1. **Stage** a new inactive snippet at a lower priority number. The owner clicks Activate.
2. **With every new key at its default,** T1 passes on a private copy.
3. **Preview each key** as an editor with its `?bke_*=1`. The owner or an editor runs the live checklist: block.ke returns 403 to us.
4. **One option write per key.**
5. **Purge Cloudflare** for the affected pages; for `nav`, purge everything. Then check a logged-out page and `cf-cache-status`.
6. **Keep the previous version active** for two weeks. It skips itself.

### 10.3 Keys (all off by default except `lp_rent_guard`)

| Key | Preview | Release | Off restores |
|---|---|---|---|
| `rent_pages` | `?bke_rent=1` | R1 | v1.8.2 rental pages and leads |
| `lp_rent_guard` (default **true**) | pinned test | R1 | v1.8.2 `?lp=1` rule (emergency only) |
| `rent_cards` | `?bke_rcards=1` | R1 | today's rent cards |
| `listing_jsonld` | `?bke_ld=1` | R1, R4 | no JSON-LD |
| `classic_inplace` | `?bke_cls=1` | R1, R4 | the footer move script |
| `forms_post` | `?bke_forms=1` | R1, R2, R4 | script-only forms |
| `gallery_ssr` | `?bke_gal=1` | R1, R4 | photos 6+ in script only |
| `redirects` ({family: 302\|301}), `redirect_ids` | `?bke_redir=1` (dry run) | R1, R2, R5 | the theme pages |
| `sale_hub`, `rent_hub` (+ `_page`) | `?bke_hub=1` | R2 | the Elementor pages |
| `type_pages` (true or a list) | `?bke_type=1` | R2 | theme list pages; new pages 302 to their hub |
| `seg_intro`, `area_guides` (maps) | `?bke_area=1` | R2 | today's area and segment pages |
| `sitemap_extra` | — | R2 | today's sitemap |
| `area_rent` | `?bke_arent=1` | R2 code, R4 on | Rank Math area titles |
| `nav`, `nav_search` (+ `nav_labels`, `nav_skip`, `nav_max` 6, `nav_max_tied` 8, `nav_rollup`, `nav_fold_completion`, `nav_aside`, `nav_links`, `area_labels`) | `?bke_nav=1` | R3 | the theme menu, exactly |
| `theme_search_bar` | `?bke_tsb=1` | R3 | the theme bar |
| `search_bands` | `?bke_search=1` | R3 | today's bands |
| `search_loose`, `hero_search` | `?bke_search=1`, `?bke_hero=1` | R5 | today's parse; the Elementor hero |
| `noindex_features`, `list_jsonld`, `crumb_hubs` | `?bke_seo=1` | R5 | today's head and breadcrumbs |

- **Existing keys used:** `search_ignore` (R0a), `search_redirect` (R3, write 3), `search_map_page` = 0 (R5).
- **Last resort:** deactivating 42706, which also turns off the development pages.

### 10.4 Turning on the menu (R3)

**No menu writes.**
- **Before:** refresh `main-menu-98.json` and snapshot `blockke_dev_page`.
- **Preview:** `?bke_nav=1` as an editor on /, /for-rent/, a sale development page, a classic rental, an area page and the results page. Check for orphaned mega panels (**UNCERTAIN**), the drawer, and the four widths.
- **The writes, each followed by a purge and a logged-out check:**

| Day | Write |
|---|---|
| 1 | `nav` true |
| 2 | `nav_search` true |
| 3 | `search_redirect` true |
| later | `theme_search_bar`, after view-source and approval |

- **Rollback:** the same key set to false, plus a purge.

### 10.5 Rollback

| What | How |
|---|---|
| A key | One option write, plus a purge |
| A version | Deactivate its snippet (the previous one is still active) |
| New pages | Set back to draft |
| Data | Replay the before-values log; republish drafted duplicates and remove their `redirect_ids` |
| Redirects | 302 first, so browsers have cached nothing |
| Hero | `hero_search` off, plus a purge of `/` |
| Inbox | Deactivate the new snippet; 42847 stays active |

Every rollback ends with a Cloudflare purge and a logged-out check.

## 11. Tests

| Test | What it checks | Pass |
|---|---|---|
| **T1, keys at default** | <ul><li>Full-URL HTTP comparison as in `contain.md` (about 2,900 URLs per copy, 4 copies, 2 passes), with **snippet 44071 added to the copies**</li><li>CLI renders (5,230)</li><li>`rebase/h` (1,846 renders, 1,694 leads)</li></ul> | Byte-identical to v1.8.2, except the similar-homes tie and the §7.2 pinned `?lp=1` list |
| **T2, one key at a time** | Every difference classified | <ul><li>Inside that key's region only</li><li>**Zero console errors** on a sale development page, a classic page and a landing page, with the key off and on (Playwright, editor preview)</li></ul> |
| T2 for `rent_pages` | Rental pages, `?lp=1`, leads | R1–R20; the pinned list; the new lead lines; the title widening changes only 44049 |
| T2 for `rent_cards` | Cards | Only rent cards change; every sale development and sale segment page is byte-identical |
| T2 for `nav`, `nav_search` | Every page, after removing both `ul#menu-main-menu` blocks, `#bkh-css` and `#bkh-js` | Byte-identical; **ad landing pages fully identical, with no `bkh` hooks** |
| T2 for `redirects` | Status codes; the dry run | Only mapped URLs change |
| **T3, rentals** | <ul><li>`rent.php`, rebased (121 checks)</li><li>Per-sq-ft, USD (KES first, dated), per-day and no-price cards and boxes</li><li>The classic box and its anchors; no mortgage</li><li>Phone bar: rent and viewing visible without scrolling</li><li>Lead subjects and inbox rows</li><li>**JSON-LD price, currency and unit equal to the visible rent on all 121 rentals**</li></ul> | All pass |
| **T4, menu** | <ul><li>The tree equals the Python copy on the same rows</li><li>Every link answers 200 **without a redirect** and lands on an indexable page</li><li>Every printed figure equals the landing page's headline figure</li><li>Ties at 6th up to 8</li><li>Playwright keyboard checks, including **hover 300 ms then click → `aria-expanded=true`**</li><li>Links with script off</li><li>Drawer at 390px with script on and off: 3 taps to any area</li><li>Widths at 1200, 1280, 1366 and 1440px, logged in and out</li><li>Cold and warm `bkn_nav`; a preview request stores nothing</li></ul> | <ul><li>Every link present with script off</li><li>No wrap for visitors; logged in no worse than today</li><li>No sideways scroll</li><li>Warm menu under 10 ms</li></ul> |
| **T5, search** | <ul><li>The header form matrix</li><li>Apartments + Studio</li><li>Any type with an area</li><li>The redirect reaches the canonical, the nationwide page or the hub</li><li>`msclkid` kept; bands with script off</li><li>The six R0a searches and /new-developments/ with ignored keys</li></ul> | All land on ours; none "classic" except the planned ones |
| **T6, hubs and pages** | Per URL: <ul><li>one canonical equal to its own URL, `og:url` and JSON-LD `WebPage.url` equal too</li><li>one robots meta; HTTP 200 with no redirect</li><li>figures equal the Python copy</li><li>every development of the type is an `<a href>` in the no-script HTML</li><li>a no-script POST from each new page makes one lead and a 303 back with `?bkd_sent=1`</li><li>every sitemap URL is 200, self-canonical and indexable</li></ul> | All pass |
| **T7, PHP** | Lint, notices, PHP 7.4 | `php -l` clean, 0 notices, manual 7.4 review |

## 12. What we need to see before R1 and R3

View-source of `/`, `/for-rent/`, a classic rental and a layout listing, all logged out, or an editor checking our previews. Search it for:
- `menu-main-menu` (how many times), `menu-item-36359` and `megamenu-studio`;
- `master_header_sticky`;
- `mobile-trigger` (a `div` or a `button`?) and `snap-drawer`;
- `search_wrapper`, `adv-search-1`, `sticky_adv`, `adv-search-header-mobile`;
- on the classic rental: the description block's id, `ihaf`'s mobile CTA, and any theme agent or tour form.

Also needed: the owner's Rank Math → Redirections export (before R2), and a re-read of live 42847 (before R1).

**Files this plan builds on**
- `/tmp/claude-0/-home-user-Loyd2024/5c4db48f-d4ee-51c9-b2e8-4bb27b73aed3/scratchpad/rent/understand/rebase/merged-resolved.php`
- `/tmp/claude-0/-home-user-Loyd2024/5c4db48f-d4ee-51c9-b2e8-4bb27b73aed3/scratchpad/runda/code/tests/rent.php`
- `/tmp/claude-0/-home-user-Loyd2024/5c4db48f-d4ee-51c9-b2e8-4bb27b73aed3/scratchpad/rent/understand/rebase/h/`
- `/tmp/claude-0/-home-user-Loyd2024/5c4db48f-d4ee-51c9-b2e8-4bb27b73aed3/scratchpad/rent/understand/data/main-menu-98.json`
- `/tmp/claude-0/-home-user-Loyd2024/5c4db48f-d4ee-51c9-b2e8-4bb27b73aed3/scratchpad/rent/understand/menu/tools/`
- `/tmp/claude-0/-home-user-Loyd2024/5c4db48f-d4ee-51c9-b2e8-4bb27b73aed3/scratchpad/rent/understand/saleinv/build.py` (and the raw rows next to it)
- `/tmp/claude-0/-home-user-Loyd2024/5c4db48f-d4ee-51c9-b2e8-4bb27b73aed3/scratchpad/rent/understand/rentals.json` and `sale-inventory.json`
- `/tmp/claude-0/-home-user-Loyd2024/5c4db48f-d4ee-51c9-b2e8-4bb27b73aed3/scratchpad/listing-prompt/final/block-listing-instructions-v2.md`
- `/home/user/Loyd2024/block-advisory-landing/wordpress/block-development-page.php` (v1.8.2)
- `/home/user/Loyd2024/block-advisory-landing/wordpress/block-enquiries-inbox.php` (the 10 Oct copy; rebase on live 42847)

---

# Review dispositions

**Other corrections found while checking, not raised by the reviewers**
- Parklands' rentals are 2 cards today, so its homes-for-rent page is noindex. It is removed from today's For Rent tree.
- For Rent figures are restated as page figures (homes): Apartments 69, Westlands 26, Kilimani 22, Kileleshwa 7, Syokimau 5, total 109.

## ssr-seo
1. **Accepted.** Type pages print an index-style A–Z of every development as plain links (lighter than hidden cards for 262 developments). A T6 check was added.
2. **Accepted.** It is resolved differently: real child pages taken over by ID, with the index's full override set (§5.2) and the T6 per-URL checks.
3. **Accepted in part.** `seg_intro` for 38125 and its redirect plus noindex move to R2. **Rejected:** trashing drafts 44095 and 44096; they are not ours (§10.1).
4. **Accepted.** The action redirects are 302 from the day each hub is on (R2), 301 in R5, then the sitemap is switched off.
5. **Accepted in part.** `listing_jsonld` true in R4 under decision 6. **Rejected:** availability claims, which Block cannot verify (see renter-buyer 3).
6. **Accepted.** `forms_post` includes 'new' pages from R2.
7. **Accepted.** `gallery_ssr` is 'rent' in R1 and true in R4.
8. **Accepted.** The sitemap provider for indexable segments, removal of noindex entries, and noindex on each page the day it starts redirecting.
9. **Accepted in part.** The index links the nationwide sale pages, `crumb_hubs` sits under decision 6, and T4 now requires 200 without a redirect. The corridor-page link repoint is deferred to R6, since 301s cover it.
10. **Accepted.** WebPage retyped to RealEstateListing, the visible breadcrumb, Place for commercial, DAY/WEE/ANN and FTK units, no price-less Offer, USD only after B2, and a 121-rental test.
11. **Accepted.** One villas-and-houses rent page, and the nationwide rule of noindex with the canonical on the hub below 5 cards.
12. **Accepted.** Confirmed: v1.8.2 has no "neighbourhoods/" link. `area_guides` ships in R2; the guide-widget swap waits for R6.
13. **Accepted.** `#studios` sections on both nationwide apartment pages, with the studio redirect pointed there.
14. **Accepted.** `bke_dev_name` on 42616, 40490 and 42370 before the Runda switch (R7), and the Rank Math SEO titles added to B6.
15. **Accepted.** Each figure is the landing page's own (Kilimani 121, Parklands 23), and T4 compares headline figures.
16. **Accepted.** Hub lists and chips use the canonical-plus-index helper, and the redirect covers type-with-no-area → nationwide page.
17. **Accepted.** 93 gets noindex when `sale_hub` is on, Nairobi county goes to /for-sale/, the /completion/ hub goes to /new-developments/ (R5), and 38136 is linked from the hub.
18. **Accepted.** Duplicates are drafted only once `redirect_ids` 301s are live (batch D).
19. **Accepted.** `bkn_nav` is keyed on every setting it reads, built with previews off, and never stored on preview or logged-in requests.
20. **Accepted.** The 60-character rule and the area's real city; "Apartments & Houses" is kept after a Search Console check.

## ia-lean
1. **Accepted.** No menu writes: Buy and Rent are found by their page IDs, and labels come from `nav_labels`.
2. **Accepted.** The drawer goes to 3 taps (type rows with inline area links) and gets a compact search form.
3. **Accepted.** Confirmed at `50-bkh-proto.js` line 10. Hover-then-click now pins, with a Playwright case.
4. **Accepted.** Same fix as ssr-seo 15.
5. **Accepted.** The cited 35028 link is on the index, not on area pages, but the conclusion holds. `area_guides` ships in R2, before the menu.
6. **Accepted.** `nav_skip`, corridors capped at 6 under the 36374 heading; Off-Plan goes to 12 links.
7. **Accepted.** "Ready to move in" moves to the For Sale aside, year chips carry no counts, and the label question is in decision 1 (default "Off-Plan" for width).
8. **Accepted.** "Any type" is empty and labelled apart from "Any home"; redirects go to canonicals, nationwide pages or hubs.
9. **Accepted.** "All homes for sale · 630", one Land label, and one number per link.
10. **Accepted.** Areas tied with 6th are shown, up to 8; the after-fix tree is recomputed, not asserted.
11. **Accepted.** `nav_skip` [36383, 36384, 36387, 36388, 38081]; no stored Buy or Rent child is drawn; 36392 is kept.
12. **Accepted.** Confirmed (100vw − 1029px). Fallbacks are on for logged-in users at 1200–1279px, and T4 requires logged-in to be no worse than today.
13. **Accepted.** The hub link comes first in the DOM, and buttons get `current-menu-ancestor` with the underline rule.
14. **Accepted.** One /for-rent/villas-and-houses-for-rent/ page, indexable after batch R.
15. **Accepted.** `theme_search_bar` can print our bar in the theme's slot ('ours').
16. **Accepted.** /neighbourhoods/ is a directory with no counts, hub lists are ordered by figure, and the action redirects move to R2.

## risk-rules
1. **Accepted as a pre-write gate** (§10.1). It blocks writes, not the design: other writers are evident, but whether they are another Claude session is not confirmed from the inputs.
2. **Accepted.** C4 removed; the drafts are only touched by agreement, and the `seg_intro` mapping is conditional.
3. **Accepted.** Re-read, diff, change one key, read back and check types (§10.1).
4. **Accepted.** Same fix as ia-lean 1.
5. **Accepted.** Confirmed at v1.8.2 lines 10577–10586 and 9721. A full key, `$preview = false`, nothing stored on preview, and the theme menu when `card_v16` is off.
6. **Accepted.** Confirmed: the bump list (line 9195) lacks `bke_rent_usd`. `|rc` and `|rp` suffixes apply only while the keys are on, the widening is gated, and the meta is added to the bump list.
7. **Accepted.** Confirmed at line 6825. The menu hooks are guarded on landing pages, 44071 is added to the test copies, and T2 asserts landing pages unchanged.
8. **Accepted.** Batch S is separate, diffed on a private copy, and its changed sale pages are listed for decision 4.
9. **Accepted.** Confirmed from `rentals.json`: 44048–44051 were created today with the layout on, and 37243 and Bahari were modified today. Staff choose keepers, and 301s go in with batch D.
10. **Accepted.** Confirmed: the repo inbox equals the 10 Oct cache. Read live first, and stage as a new snippet.
11. **Accepted.** Three writes on separate days, and `theme_search_bar` as its own key.
12. **Accepted.** Every key gets a preview, the redirect dry run is added, and the Rank Math export is requested before R2.
13. **Accepted.** The owner or an editor runs the live checks, and every rollback includes a purge.
14. **Accepted.** `hero_search` swaps output on the server, falling back to the output buffer if Elementor's cache defeats the filter; 18618 and `ihaf_insert_footer` are untouched.
15. **Accepted.** Only "Nairobi" means any area; other cities map to a region or go to the theme, and unknown statuses go to the theme.
16. **Accepted.** The previous version stays active for two weeks, and the active list is checked before deactivating anything.
17. **Accepted.** It is moot with real child pages: no rewrite rule, and the drafts change nothing while the key is off.
18. **Accepted.** `type_pages` and `redirects` take a list or families, and `listing_jsonld` and `lp_rent_guard` are separate keys.
19. **Accepted.** Confirmed at `merged-resolved.php` line 3761. The `blockke_dev_rent_on()` helper, the grep audit, and the key renamed to `rent_pages`.
20. **Accepted.** Confirmed at lines 14459, 14472 and 12530. `msclkid` and `srsltid` are kept, bands are translated, and /new-developments/ is added to the checks.
21. **Accepted.** Rebuilds run on `shutdown`, the previous copy is always served, and data batches run in one window followed by a warm-up.
22. **Accepted.** Unknown children go to the aside and are named in the admin bar.
23. **Accepted.** The console-error check is added to T2.
24. **Accepted.** Same fix as ia-lean 6.
25. **Accepted.** An optional field; the owner changes the mapping in admin, after a snapshot, with one test lead.
26. **Accepted.** Computed targets stay 302.

## renter-buyer
1. **Accepted in part.** Confirmed: the `'period'` flag (under `rent_cards`) keeps 44020 out of ranges, bands and sorting, B3's no-op 44020 row is dropped, and the lede is reworded. **Rejected:** removing it from counts and from its segment, because it is a real listing that the page still shows, with its own period.
2. **Accepted.** `lp_rent_guard` is independent of `rent_pages` and ships on, with the pinned test.
3. **Accepted.** The lede no longer claims viewings can be arranged, `rent_why` is used word for word, there is no availability or seller in the JSON-LD, and `rent_disclaimer` is used on rent pages.
4. **Accepted.** 36662 becomes For Rent only, Bahari and 31883 lose For Rent in batch S, and §2 restates the count as 9.
5. **Accepted.** Confirmed in `blockke_search_ok()`. Apartments + Studio reads both, "Any type" is empty, and both are in T5.
6. **Accepted.** `bke_rent_usd` is the one source, with a dated rate. KES comes first per rules v2, so no owner decision is needed.
7. **Accepted.** The classic phone bar is added, after checking the existing ihaf mobile CTA and any theme tour form.
8. **Accepted.** Confirmed: a band drops `'unit'` rows. No budget filter or price sort on commercial rent, and the wording is corrected.
9. **Accepted.** Same fix as ssr-seo 15: one number per link, the landing page's own.
10. **Accepted.** Same fix as ia-lean 7; year counts are dropped rather than recomputed (lean).
11. **Accepted in part.** A rent-only search guard under `rent_cards`, and per-layout rents moved to staff (C4) rather than group B.
12. **Accepted.** Hub and section picks need a photo.
13. **Accepted.** A Furnished tag on rent cards, and the feature tidy-up moved into batch R (B7).
14. **Accepted.** "Rent on request", no brackets, and no Offer.
15. **Accepted.** "Nairobi and nearby" (`index_metro`) in the H1s, and the area's real city in `area_rent` titles.
16. **Accepted in part.** The phone decision moves before R1. **Rejected:** printing the config number in the drawer, because the theme's drawer phone row stays and two numbers would show.
17. **Accepted in part.** "for new developments", and `area_why` on the sale hub. **Rejected:** rewording `why[0]` in this project, because it would change every sale development page; it is raised with the owner separately.
18. **Accepted.** A bedrooms field on both hub forms, and a size field for commercial through CSS `:has()`.
19. **Accepted.** Same fix as ia-lean 6, with years as one row of chips.
20. **Accepted.** One cross-link under each nationwide page's H1.
21. **Accepted.** B6 uses the rules v2 title pattern, and B1 normalises "Per Month".
22. **Accepted.** Rental leads send "Individual tenant", in the same owner-made Zoho change.
