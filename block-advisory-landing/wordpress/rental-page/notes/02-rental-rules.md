# 02. Rental rules: what was already worked out and approved, and what the new page must keep

Written 11 Nov 2026 by the rental-rules specialist. Read-only work on local files. Nothing on block.ke was called or
changed, and no repository file was touched.

`ROOT` = `/tmp/claude-0/-workspace-Loyd2024/e24ac003-f571-5336-9956-1fd11a814c7d/scratchpad/rp`

**What this file is.** The numbered list of rules for the page of a listing that is for rent. They were worked out
in October, approved by the owner (plan section 7 and its review dispositions; README "Homes for rent") and built
into the rent mode of the development snippet v1.9.1, which was never installed. The new standalone page ports
these rules, not the look (PROJECT.md section 2). Sections 1 to 15 are the rules. Section 16 is what the tests
asserted. Sections 17 to 19 are the gaps, the conflicts with PROJECT.md and the places where the approved code
misreads today's listings. Appendix A is the code to port, pasted verbatim.

## How to read the citations

| Short name | File | Note |
|---|---|---|
| `plan:N` | `ROOT/ref/plan.md` line N | owner-approved plan, 22 Oct 2026 |
| `README:N` | `ROOT/ref/README-wordpress.md` line N | README of the snippets (later than the plan, so it wins where they differ) |
| `v191:N` | `ROOT/ref/dev-v1.9.1.php` line N | development snippet v1.9.1; its comments say "v1.8.3" for the rental work (README:150) |
| `v182:N` | `ROOT/ref/dev-v1.8.2.php` line N | the version live today |
| `inbox:N` | `ROOT/ref/inbox-v1.1.1.php` line N | enquiries inbox |
| `popup:N` | `ROOT/ref/live-snippets/40752.php` line N | price-check popup (Zoho sender) |
| `feed` | `ROOT/ref/listings.json` | generated 2026-11-11 07:16 UTC, 1,009 listings |
| `PROJECT.md` | `ROOT/PROJECT.md` | the brief; "rule N" means section 4, rule N |

The column **New page** says whether a rule carries over: **yes** (keep as it is), **adapt** (keep the rule, change
how it is done) or **no** (does not carry over), with the reason.

## How the facts were checked

- Every rule was read in the plan, the README and the code.
- The v1.9.1 functions were then copied out by line range, unchanged, and run under PHP 8.3 with stand-ins for the
  WordPress calls. The truth tables in sections 1, 2, 3 and 14 are that output, not a reading of the code. No PHP
  notice, warning or deprecation was raised on any real or made-up listing; one was provoked on purpose with text
  that is not valid UTF-8 (Appendix A, port note 3).
- The same verbatim functions were run over the description of every one of the 129 rent-only listings, cut from
  `ROOT/ref/pages-stop/ID.html` (the 29 listings of `ROOT/ref/dev29.txt`) and `ROOT/ref/pages-final/ID.html` (the
  other 100). The counts in sections 3, 10 and 19 come from those runs.
- The scripts and their outputs are kept, outside ROOT, in
  `/tmp/claude-0/-workspace-Loyd2024/e24ac003-f571-5336-9956-1fd11a814c7d/scratchpad/rr-tmp/`
  (`run_phrase.php`, `run_info.php`, `run_mode.php`, `run_ld.php`, `run_name.php`, `run_real.php`,
  `run_real2.php`, `run_real3.php`, `run_real4.php`, `run_units.php`, `run_d6.php`, `run_utf8.php`; the extracted
  functions are `fn*.php`, made by `extract*.py`; `cmpfn.py` compares a function between the two versions). To
  re-run one: `php run_phrase.php`. The `run_real*.php` and `run_units.php` scripts take `ROOT/ref` as their
  argument (`run_real2.php` also a pattern for the row labels); `run_name.php` takes the feed and the ID list.
- What could not be checked is marked **unknown**, with how to find out, and is collected in section 20.

## 0. The ten findings the next team needs first

1. **The rent functions do not exist in the live snippet.** `blockke_dev_rent_mode`, `blockke_dev_rent_phrase`,
   `blockke_dev_rent_info`, `blockke_dev_rent_name` and `blockke_dev_rent_on` are in v1.9.1 only (v191:4107, 7212,
   7139, 7281, 4123). The helpers they call are byte for byte the same in v1.8.2 and v1.9.1 (table in Appendix A).
   The new snippet must carry its own copies of both.
2. **Ported unchanged, the terms reader finds almost nothing on today's descriptions.** Over the 129 listings it
   returns a deposit for 5, a lease for 5, a service charge for 21, an availability for 23, a viewing line for 2 and
   furnishing for 15; 77 listings get no row at all. Most rows really do say "to be confirmed", which is right to
   drop: of the 49 listings with a deposit row, 42 say only that; of the 62 with a lease row, 52. So design for the
   usual case, a rent and little else. But 23 listings lose their whole snapshot table because of how the
   description is split (section 19, D1).
3. **Some rows it does show are stale or are not facts** (section 19, D2 to D5): "From 1st October" is printed as
   the availability in November (40490), "Expected from Q3 2026" on 36135 and 36136, "Subject to apartment
   availability" (36410), "For rent" as an availability (39421, 41862, 41921) and "There is also furnished
   apartments" as the furnishing (43788). And it misses what the listing does state: ten rent rows say the service
   charge is included (45577, 45538 and 34171 among them), yet the page would say the service charge is "confirmed
   when you enquire" for nine of them (D7).
4. **The headline rent must be the price field.** The v1.9.1 layout takes the lowest priced row of any table it
   reads as a unit table before the price field (v191:7397-7398). On 36731 that is a parking charge: the page would
   say "From KES 8,500 per sq ft a month" for an office let at KES 65 per sq ft (section 19, D6).
5. **"From" is not handled the way PROJECT.md rule 12 asks.** 11 of the 129 listings carry the before-price label
   "From". The v1.9.1 layout drops it on three of the four per-sq-ft ones (36814, 36826, 36834). It adds a "From"
   the listing does not have on 17 listings, because their description holds a table it reads as a unit table
   (eight of them are single homes), and on any off-plan rental (33230). The classic rent box of the same version
   has the better rule (section 2, R19; section 18, C3).
6. **The page name rule gives the same H1 to different homes.** Ported unchanged it yields 19 names shared by 47
   listings ("Kings Courtyard" for both 45577 and 45538) and five broken ones: "Nairobi" (40490), "Kilimani"
   (29462), "Westlands" (42340), "Apartments" (43644, 43656) and "Villa for Rent" (42616) (section 10).
7. **Nine published listings are rentals by their title but carry no Buy/Rent term** (45113, 45098, 45054, 45037,
   45003, 44956, 44955, 44051, 44049). The approved rule counts them; PROJECT.md section 5 does not (section 18, C1).
8. **Four listings state their rent in US dollars** in the text: 35583 (USD 5,500), 34171 (USD 2,600), 36816
   (USD 1.30 per sq ft, price field 0) and 44533 (USD 6,500). The reader finds the first three; 44533 has no
   headings, so it would read "KES 841,332 a month" with no word about dollars. Whether `bke_rent_usd` is set on
   any of them is unknown (section 20, Q1).
9. **There is no approved wording for commercial tenants.** v1.9.1 says "home", "tenants" and "household" on every
   rental and shows the bedrooms field (offices 34906 and 36675 carry 1 and 2 bedrooms in the feed). The 28
   commercial listings need new copy under PROJECT.md rule 13 (section 18, C8).
10. **v1.9.1 prints its scripts and JSON-LD as hand-written script tags** and reads script tags with a regex
    (v191:10367-10368, 8546, 8224). PROJECT.md rule 5 forbids both, so those parts are re-done, not ported
    (section 18, C5).

## 1. Which listing counts as a rental

| # | Rule | Source | v1.9.1 | New page |
|---|---|---|---|---|
| R1 | A listing is a rental when its Buy/Rent taxonomy (`property_action_category`) holds the term `for-rent` and not `for-sale`. | README:152; plan:650 | `blockke_dev_action()` v191:4085-4088; `blockke_dev_rent_mode()` v191:4107-4113 | **yes**. It is the only test (PROJECT.md section 5). 129 listings today (feed). |
| R2 | A listing tagged both For Rent and For Sale is never a rental page; it keeps the sale page. | README:152; plan:53 (decision 5); plan:145 | v191:4086-4091 | **yes**. 10 listings today (feed): 43739, 39502, 34618, 33933, 33932, 33909, 31883, 31413, 31265, 30512. |
| R3 | With no Buy/Rent term at all, the title decides: "for rent" or "to let"; with the rental switch on also "to rent" or "for lease"; never "Rent-to-Own". | README:152; plan:650; plan:668 | v191:4092-4096 | **no**: PROJECT.md section 5 limits the page to tagged listings. 9 listings fall out today; see section 18, C1. |
| R4 | With no Buy/Rent term, a title that offers both ("for Rent & Sale", "for Sale or to Let") keeps the sale page. | README:152; v191:213-214 | v191:4114 | **no** as a test (it follows R3). **adapt** as a guard on the automatic rule: section 18, C2. |
| R5 | Land for lease is not a rental page; it keeps its own land wording. | README:152 | v191:7376 (`! $land && …`); `blockke_dev_is_land()` v191:698-722 | **yes**: skip a listing with a land category (`land`, `residential-land`, `agricultural`, `industrial-land`), no home category and no bedrooms. None of the 129 is one (feed categories: Apartments 94, Commercial with Offices 19, Commercial with Warehouses 8, Houses 4, Villas 2, none 2). |
| R6 | Only a published `estate_property` has the page and can receive an enquiry; any other listing ID is refused with "This listing is no longer available." | v191:7292-7295; v191:10583-10585 | same | **yes**. |
| R7 | A rental never gets an ad landing page (`?lp=1`, or a page with `bke_lp_listing`). | README:195; plan:662-670 | `blockke_dev_lp_ok()` v191:735-751 | **yes** (PROJECT.md section 5): the new page reads no `lp` parameter. What the live v1.8.2 does with `?lp=1` on a rental is the development snippet's own matter. |
| R8 | With the switch off nothing changes for visitors; an editor previews with a query parameter; a preview stores no cache. | README:150, 388; plan:125 | `blockke_bkn_on()` v191:12566-12572; `blockke_dev_rent_preview()` v191:4169-4171 | **adapt**: PROJECT.md rule 6 sets the new names (`blockke_rent_page`, `?bkr=1`, `bkr_key`). |

The two regular expressions, verbatim (v191:4094 and v191:4114):

```php
// title says rental, with the rental switch on (v191:4094); with it off only the first two alternatives (v191:4096)
preg_match( '/\bfor rent\b|\bto let\b|\bto rent\b|\bfor lease\b/i', $t ) && ! preg_match( '/rent[- ]to[- ]own/i', $t )
// title offers both rent and sale: not a rental when there is no Buy/Rent term (v191:4114)
preg_match( '/\b(?:rent|let)\s*(?:&|and|or|\/)\s*(?:for\s+)?sale\b|\bsale\s*(?:&|and|or|\/)\s*(?:for\s+|to\s+)?(?:rent|let)\b/i', $title )
```

Checked by running the two functions (`run_mode.php`); "switch on" is `rent_pages`:

| Buy/Rent terms | Title | Switch off | Switch on |
|---|---|---|---|
| for-rent | 3 Bedroom Apartment for Rent in Kilimani | rental | rental |
| for-rent | Purple Tower Mombasa Road … Office Space for Rent & Sale in Nairobi (36662) | rental | rental |
| for-rent | Purple Tower, Mombasa Road … Offices to Buy or Lease in Nairobi (45228) | rental | rental |
| for-rent + for-sale | 2 Bedroom Apartment for Rent in Westlands | sale | sale |
| for-sale | 2 Bedroom Apartment for Rent in Westlands | sale | sale |
| none | 2 Bedroom Apartment for Rent in Westlands | rental | rental |
| none | 2 Bedroom Apartment to Let in Brookside | rental | rental |
| none | 2 Bedroom Apartment to Rent in Kileleshwa | sale | rental |
| none | Warehouse for Lease in Tatu City | sale | rental |
| none | Rent-to-Own 3 Bedroom Apartments in Syokimau | sale | sale |
| none | Apartments to Rent - Rent to Own available | sale | sale |
| none | Office Space for Rent & Sale in Westlands | sale page | sale page |
| none | Office Space for Sale or to Let in Westlands | sale page | sale page |
| none | Offices to Let and for Sale | sale page | sale page |
| none | Rental apartments in Kilimani; Letting office space | sale | sale |

Two things this table shows. A listing tagged For Rent only is a rental whatever its title says: 36662 and 45228
offer sale and lease in their title and text, and are rentals by the rule (README:418 lists nine such listings left
for the owner). And the "both" pattern does not know "to Buy or Lease".

## 2. The rent: one phrase, its period and its currency

| # | Rule | Source | v1.9.1 | New page |
|---|---|---|---|---|
| R9 | One phrase states the rent wherever it is printed: the page, the WhatsApp text, the lead email, cards and structured data. | README:162; plan:108 | `blockke_dev_rent_phrase()` v191:7188-7267 (Appendix A6) | **yes** (PROJECT.md rule 12). |
| R10 | The rent is the listing's price field (`property_price`), in shillings. Editors are told to keep the monthly rent there. | README:181; v191:7137 | The layout uses `price_from`: the lowest priced row of a table it reads as a unit table, else the price field (v191:7397-7398) | **adapt**: use the price field only. The table route puts a parking charge in the headline on 36731 (section 19, D6). |
| R11 | The period comes from the after-price label (`property_label`): "a month" when it says month or p.m., when it is empty, or when it only says per sq ft; "a week", "a day", "a night" or "a year" when it names one; any other label is printed as written, in lower case. | README:162; plan:633 | v191:7216-7230 | **yes**. Two side effects to know: an "as written" label loses its capitals ("plus VAT" prints "plus vat"), and a label that names a period loses its other words ("per month plus service charge" prints "a month"). |
| R12 | Per sq ft: when either price label says sq ft, sqft, sq m, sqm, m² or "per sq…", the rent reads "KES 130 per sq ft a month". | README:162; plan:634 | `blockke_dev_price_flag()` v191:4068-4077; v191:7214, 7261 | **yes**, with one fix: the test also matches square metres, but the phrase always says "per sq ft". No such label exists today (feed labels: `per sq ft`, `per sqft`, `per sq ft per month`). |
| R13 | "Rent on request" when there is no price; or the label says "on application", "on request" or "POA"; or the price is over KES 2,000,000 with an empty label. Then no figure is printed anywhere. | README:162; plan:635; plan:1076 | v191:7218, 7235-7238 | **yes** (PROJECT.md rule 12). |
| R14 | A rent set in US dollars is known from the custom field `bke_rent_usd` (a number; "USD 5,500" and "$5,500" are read too), else from the first rent row of the listing's glance table or terms section when its value starts with USD, US$ or $. | README:162, 181; plan:637-639 | v191:7232-7234; row reader v191:7171-7184 | **yes**. `bke_rent_usd` and `bke_rent_usd_date` are existing listing fields; keep their names. |
| R15 | A USD rent without a dated rate reads in dollars alone, "USD 5,500 a month", with the sentence "The rent is set in US dollars." | README:162 | v191:7255-7259 | **yes**. |
| R16 | KES first, "KES 710,000 a month (USD 5,500)", only when `bke_rent_usd` and `bke_rent_usd_date` (YYYY-MM-DD, a real date) are both set and the price field divided by the USD rent is between 80 and 200. The KES figure is the price field rounded to the nearest thousand (not for per sq ft or under KES 10,000). The sentence becomes "The rent is set in US dollars; KES figures are approximate, at about KES 129.2 to the US dollar on 20 October 2026." | README:162; plan:637-641 | v191:7243-7254 | **yes**. |
| R17 | A known USD rent is a rent even with no price field or a price over KES 2M with no label; a label "on application" still wins over it. | v191:7235 | same | **yes** (rows P32, P38, P39 below). |
| R18 | The rental's own page prints exact figures ("KES 135,000", "KES 6,500"), never the short card form ("KES 135K"). | README:173; plan:715-720 | v191:7193-7195, 8596 | **yes**. |
| R19 | "From" stays when the listing has it. | README:38; PROJECT.md rule 12 | `from_label` v191:7698; layout v191:8568-8587; classic box v191:1884-1894; cards v191:12877-12913 | **adapt**: the three places disagree. Recommended single rule under the tables below. |
| R20 | The hero's rent cell is labelled by the period: "Rent / month", "Rent / day", "Rent / week", "Rent / year"; with a prefix "Rent from / month" or "Rent up to / month"; "Rent" alone for a label that names no period; on request the cell reads "Rent" and "On request". | README:156, 162; plan:626 | v191:8601, 8643 | **adapt**: the design may not use a label and value cell, but these are the approved words. |
| R21 | A listing with several homes shows them in a table: column "Rent a month", a figure or "On request" per row, the row button "Availability & viewing", and the note "Ask us for current availability and a time to view." | v191:8899-8910 | same | **adapt**: the column heading is fixed and would be wrong for a daily rate. |
| R22 | Cards state the rent as the page does, in short figures: "KES 200K a month", "KES 6,500 a day" (exact under KES 10,000), "KES 130 per sq ft a month", "KES 711K a month (USD 5,500)", "USD 5,500 a month", "Rent on request"; "a month", never "/ month". | README:173; plan:713-720 | `blockke_bkn_rent_text()` v191:12858-12869; `blockke_dev_sim_price()` v191:4595-4599 | **yes** for the similar rentals. Note the two roundings: cards round up (`blockke_bkn_money()` v191:13307-13319), the page rounds a KES-first figure to the nearest thousand, so one rent can read "KES 711K" on a card and "KES 710,000" on its page (row P33). |

The period and the "on request" tests, verbatim (v191:7218-7220):

```php
$ask = (bool) preg_match( '/\bon\s+(?:application|request)\b|\bp\.?o\.?a\b/i', $l ); // "Rent on application", "Price on request", "POA"
foreach ( array( 'a month' => '/month|\bp\.?\s?m\b/i', 'a week' => '/\bweek(?:s|ly)?\b/i', 'a day' => '/\b(?:days?|daily)\b/i', 'a night' => '/\bnight(?:s|ly)?\b/i', 'a year' => '/\b(?:years?|yearly|annum|annual(?:ly)?)\b|\bp\.\s?a\b/i' ) as $w => $re ) { … first match wins … }
```

Per sq ft and "up to" (v191:4069-4076), read from the after label and the before label together:

```php
if ( preg_match( '/sq\.?\s*f|sqft|sq\.?\s*m\b|sqm|m²|per\s+(?:sq|square)/u', $l ) ) { return 'unit'; }
…
return false !== strpos( $l, 'up to' ) ? 'upto' : '';
```

### Truth table (output of the verbatim functions, `run_phrase.php`)

Inputs: the price field, the before label (`property_label_before`), the after label (`property_label`),
`bke_rent_usd`, `bke_rent_usd_date`, and a rent row in the listing's text. Outputs: the hero cell (label · value) as
the v1.9.1 layout prints it, the long phrase (the Renting heading), the card phrase and the bracket in the WhatsApp
text. A dash means empty. Rows not marked otherwise are a single home with no unit table. In the first four
tables `bke_rent_usd`, `bke_rent_usd_date` and the rent row are empty.

**Periods**

| Row | Price | Before | After label | Hero cell | Long phrase | Card | WhatsApp bracket |
|---|---|---|---|---|---|---|---|
| P1 | 135000 | - | per month | Rent / month · KES 135,000 | KES 135,000 a month | KES 135K a month | (KES 135,000 a month) |
| P2 | 70000 | - | - | Rent / month · KES 70,000 | KES 70,000 a month | KES 70K a month | (KES 70,000 a month) |
| P3 | 100000 | - | Per Month; p.m.; / month | Rent / month · KES 100,000 | KES 100,000 a month | KES 100K a month | (KES 100,000 a month) |
| P4 | 6500 | - | per day | Rent / day · KES 6,500 | KES 6,500 a day | KES 6,500 a day | (KES 6,500 a day) |
| P5 | 45000 | - | per week | Rent / week · KES 45,000 | KES 45,000 a week | KES 45K a week | (KES 45,000 a week) |
| P6 | 9000 | - | per night | Rent / night · KES 9,000 | KES 9,000 a night | KES 9,000 a night | (KES 9,000 a night) |
| P7 | 1200000 | - | per year; per annum | Rent / year · KES 1,200,000 | KES 1,200,000 a year | KES 1.2M a year | (KES 1,200,000 a year) |
| P8 | 100000 | - | negotiable | Rent · KES 100,000 | KES 100,000 negotiable | KES 100K negotiable | (KES 100,000 negotiable) |
| P9 | 300000 | - | per quarter | Rent / quarter · KES 300,000 | KES 300,000 per quarter | KES 300K per quarter | (KES 300,000 per quarter) |
| P10 | 300000 | - | plus VAT | Rent · KES 300,000 | KES 300,000 plus vat | KES 300K plus vat | (KES 300,000 plus vat) |
| P11 | 300000 | - | per month plus service charge | Rent / month · KES 300,000 | KES 300,000 a month | KES 300K a month | (KES 300,000 a month) |

**Per sq ft**

| Row | Price | Before | After label | Hero cell | Long phrase | Card | WhatsApp bracket |
|---|---|---|---|---|---|---|---|
| P12 | 125 | - | per sq ft | Rent / month · KES 125 per sq ft | KES 125 per sq ft a month | KES 125 per sq ft a month | (KES 125 per sq ft a month) |
| P13 | 200 | - | per sq ft per month | Rent / month · KES 200 per sq ft | KES 200 per sq ft a month | KES 200 per sq ft a month | (KES 200 per sq ft a month) |
| P14 | 35 | - | per sqft | Rent / month · KES 35 per sq ft | KES 35 per sq ft a month | KES 35 per sq ft a month | (KES 35 per sq ft a month) |
| P15 | 125 | From | per sq ft | Rent / month · KES 125 per sq ft | KES 125 per sq ft a month | KES 125 per sq ft a month | (KES 125 per sq ft a month) |
| P16 | 1500 | - | per sq ft per year | Rent / year · KES 1,500 per sq ft | KES 1,500 per sq ft a year | KES 1,500 per sq ft a year | (KES 1,500 per sq ft a year) |

Row P15 is listing 36834 as it stands ("From KES 125 per sq ft" on its classic page): the layout loses the "From".
The classic rent box of the same version prints "From KES 125 per sq ft a month" for it.

**"From" and "Up to"**

| Row | Price | Before | After label | Unit table | Hero cell | Long phrase | Card | WhatsApp bracket |
|---|---|---|---|---|---|---|---|---|
| P17 | 30000 | From | per month | none | Rent from / month · KES 30,000 | From KES 30,000 a month | KES 30K a month | (from KES 30,000 a month) |
| P18 | 60000 | - | per month | 2 rows | Rent from / month · KES 60,000 | From KES 60,000 a month | KES 60K a month | (from KES 60,000 a month) |
| P19 | 90000 | Up to | per month | none | Rent up to / month · KES 90,000 | Up to KES 90,000 a month | KES 90K a month | (up to KES 90,000 a month) |

A card adds "From" on its own rule: when the card stands for several sizes or its rentals differ in rent
(v191:12889-12909). The lead email line never carries "From" (it uses the phrase's `line`, v191:10762).

**Rent on request**

| Row | Price | After label | Hero cell | Long phrase | Card | WhatsApp bracket | Lead line ends |
|---|---|---|---|---|---|---|---|
| P20 | 0 | - | Rent · On request | Rent on request | Rent on request | none | - rent on request |
| P21 | 0 | per month | Rent · On request | Rent on request | Rent on request | none | - rent on request |
| P22 | 0 | Rent on application | Rent · On request | Rent on request | Rent on request | none | - rent on request |
| P23 | 250000 | Rent on application; POA; Price on request | Rent · On request | Rent on request | Rent on request | none | - rent on request |
| P24 | 2500000 | - | Rent · On request | Rent on request | Rent on request | none | - rent on request |
| P25 | 2500000 | per month | Rent / month · KES 2,500,000 | KES 2,500,000 a month | KES 2.5M a month | (KES 2,500,000 a month) | - KES 2,500,000 a month |
| P26 | 2000000 | - | Rent / month · KES 2,000,000 | KES 2,000,000 a month | KES 2M a month | (KES 2,000,000 a month) | - KES 2,000,000 a month |

**US dollars**

| Row | Price | After label | `bke_rent_usd` | `bke_rent_usd_date` | Rent row in the text | Hero cell | Long phrase | Card | WhatsApp bracket |
|---|---|---|---|---|---|---|---|---|---|
| P27 | 337000 | per month | 2600 | - | - | Rent / month · USD 2,600 | USD 2,600 a month | USD 2,600 a month | (USD 2,600 a month) |
| P28 | 337000 | per month | 2600 | 2026-10-20 | - | Rent / month · KES 337,000 | KES 337,000 a month (USD 2,600) | KES 337K a month (USD 2,600) | (KES 337,000 a month) |
| P29 | 100000 | per month | 2600 | 2026-10-20 | - | Rent / month · USD 2,600 | USD 2,600 a month | USD 2,600 a month | (USD 2,600 a month) |
| P30 | 337000 | per month | - | - | Rent: USD 2,600 a month | Rent / month · USD 2,600 | USD 2,600 a month | **KES 337K a month** | (USD 2,600 a month) |
| P31 | 337000 | per month | - | 2026-10-20 | Rent: USD 2,600 a month | Rent / month · USD 2,600 | USD 2,600 a month | **KES 337K a month** | (USD 2,600 a month) |
| P32 | 0 | per month | 5500 | 2026-10-20 | - | Rent / month · USD 5,500 | USD 5,500 a month | USD 5,500 a month | (USD 5,500 a month) |
| P33 | 710400 | per month | USD 5,500 | 2026-10-20 | - | Rent / month · KES 710,000 | KES 710,000 a month (USD 5,500) | KES 711K a month (USD 5,500) | (KES 710,000 a month) |
| P34 | 0 | USD 1.30 per sq ft per month plus VAT | 1.30 | - | - | Rent / month · USD 1.30 per sq ft | USD 1.30 per sq ft a month | USD 1.30 per sq ft a month | (USD 1.30 per sq ft a month) |
| P35 | 0 | USD 1.30 per sq ft per month plus VAT | - | - | - | Rent · On request | Rent on request | Rent on request | none |
| P36 | 337000 | per month | 2600 | 2026-13-40 | - | Rent / month · USD 2,600 | USD 2,600 a month | USD 2,600 a month | (USD 2,600 a month) |
| P37 | 168 | per sq ft | 1.30 | 2026-10-20 | - | Rent / month · KES 168 per sq ft | KES 168 per sq ft a month (USD 1.30 per sq ft) | same as the long phrase | (KES 168 per sq ft a month) |
| P38 | 2600000 | - | 20000 | 2026-10-20 | - | Rent / month · KES 2,600,000 | KES 2,600,000 a month (USD 20,000) | KES 2.6M a month (USD 20,000) | (KES 2,600,000 a month) |
| P39 | 337000 | Rent on application | 2600 | 2026-10-20 | - | Rent · On request | Rent on request | Rent on request | none |
| P40 | 6500 | per day | 50 | 2026-10-20 | - | Rent / day · KES 6,500 | KES 6,500 a day (USD 50) | KES 6,500 a day (USD 50) | (KES 6,500 a day) |
| P41 | 337000 | per month | about 2600 | - | - | Rent / month · KES 337,000 | KES 337,000 a month | KES 337K a month | (KES 337,000 a month) |

Notes on the dollar rows:
- The sentence that goes with the rent: rows P27, P29 to P32, P34 and P36 "The rent is set in US dollars."; row P28 "The
  rent is set in US dollars; KES figures are approximate, at about KES 129.6 to the US dollar on 20 October 2026.";
  row P33 the same with 129.2; rows P37, P38 and P40 the same with 129.2, 130 and 130.
- The lead email line carries the dollars: "Rental: {name} - KES 710,000 a month (USD 5,500)" (plan:641). The
  WhatsApp bracket does not (v191:8599 uses the amount and the period only).
- Rows P30 and P31: a card only knows the custom field, so it prints shillings for a rent the page states in dollars
  (v191:12860 calls the phrase without the row's figure). The similar rentals of the new page should use one
  helper for both.
- Row P35 is listing 36816 as the feed has it (price 0, the dollars only in the label): without `bke_rent_usd` or a
  rent row it is "Rent on request". Its text does have the row "Rent → USD 1.30 per sq ft per month, plus VAT", so
  the reader gives row P34 for it.
- The structured data states dollars for every row from P27 to P40 where a rent is known, KES first or not
  (section 14).

### "From": how the three places decide, and one rule for the new page

| Place | "From" when | "Up to" when | Lines |
|---|---|---|---|
| Layout hero, Renting heading, WhatsApp | The listing has a unit table or sub-unit listings (unless it is a villa or house with at most one row); or the before label starts with "from"; or the listing is off-plan. Never when the rent is per sq ft on a single listing. | A price label says "up to" | v191:8568-8587, 8591-8601 |
| Classic rent box and phone bar | The before label starts with "from", or the unit rows state more than one rent | never | v191:1884-1894 |
| Cards | The card covers several sizes, or its rentals differ in rent | never | v191:12877-12913, 13295 |

What the data says (saved pages and feed): 11 of the 129 listings have the before label "From" (35550, 36220, 36410,
36731, 36809, 36814, 36826, 36834, 37243, 37551, 39276); 118 have none; none has "Up to". 19 descriptions hold a
table the layout reads as a unit table (`run_units.php`); 17 of them have no "From" label, and 8 of those have a
single row (39301, 39341, 41650, 43583, 43788, 43803, 44048, 44050): on the v1.9.1 layout those single homes
would read "Rent from". One rent-only listing is tagged Off-Plan / Ongoing (33230).

Recommended rule for the new page (it follows PROJECT.md rule 12 and the classic box, the stricter of the three):
"From" when (a) the before label starts with "from", or (b) the listing's own rows state more than one rent, or
(c) its title names more than one size ("Studio, 1 & 2 Bed", "2 & 3 Bedroom"; `blockke_dev_title_beds()`
v191:2569-2588), since one figure cannot be the rent of every size (the cards' rule, v191:12873-12890). "Up to"
when a price label says "up to". Nothing else adds a prefix: not a unit table as such, not the off-plan status, and
per sq ft rents keep their "From". Fixture 39421 is the test for (c): its title says "from KES 30K", its before
label is empty, and its text gives KES 30,000 for a studio and KES 50,000 for two bedrooms.

## 3. Reading the deposit, lease, service charge, furnishing, availability and viewing

All of this is one function, `blockke_dev_rent_info( $kv, $secs )` (v191:7131-7186, Appendix A5). `$kv` is the
glance table's rows; `$secs` is every section of the description.

| # | Rule | Source | v1.9.1 | New page |
|---|---|---|---|---|
| R23 | Six things are read from the listing's own rows: deposit, lease, service charge, furnishing, availability, viewing. Each is shown under a fixed label ("Deposit", "Lease", "Service charge", "Furnishing", "Availability", "Viewing") with the listing's value, first letter capitalised. | README:157 | v191:7148-7170 | **yes**. |
| R24 | Rows come from the glance table first (the first section whose heading matches `glance`, `snapshot`, `key facts`, `quick facts`, `in brief`, `at a look` or `summary`), then from every section headed as rental terms. The first acceptable row wins, so a glance row that is "to be confirmed" gives way to a terms row with a figure. | README:157 | `blockke_dev_glance()` v191:978-982; v191:7140-7145, 7163-7170 | **adapt**: keep the order, but find the sections at any heading level and under bold-paragraph headings (section 19, D1). |
| R25 | A section counts as rental terms when its heading matches the pattern below and does not contain "faq" or "question". | README:157 | v191:7142 | **adapt**: add what the pattern misses (list below). |
| R26 | A row is a table row (its first two cells) or a list item written "Label: value" (bold label, or text up to a colon). Sentences and paragraphs are not read. | README:157 | `blockke_dev_kv()` v191:934-956; `blockke_dev_split_kv()` v191:888-900; `blockke_dev_table()` v191:902-931 | **yes**. |
| R27 | A value is shown only when it is not empty, is at most 60 characters, does not match the "still open" pattern, and meets its own test (R28 to R33). | README:157; PROJECT.md rule 11 | v191:7165-7168 | **yes**. The 60-character limit drops one real term today (section 19, D5). |
| R28 | Deposit: any row whose label contains "deposit"; the value must hold a figure (a digit, a number word, or "a month" / "a year"). | README:157 | v191:7147, 7149 | **yes** (PROJECT.md rule 11: "only when the listing states it with a figure"). |
| R29 | Lease: a row labelled "Lease", "Lease term / length / period / duration", "Minimum lease / term / stay / let / tenancy" or "Tenancy term / length / period"; the value must hold a figure and must not be a land tenure ("leasehold", "freehold", "99 years", "999"). | README:157; v191:214-215 | v191:7150 | **yes**. |
| R30 | Service charge: a row whose label contains "service charge" or "estate fee(s) / charge(s) / levy"; the value must hold a digit or a word starting "inclu" (included, inclusive). | README:157 | v191:7151 | **adapt**: the label test is too loose (section 19, D2). |
| R31 | Furnishing: a row whose label contains "furnish", from the glance table, a terms section or any other table or list outside the FAQ; the value must itself contain "furnish". | README:157; v191:215 | v191:7152, 7157-7162 | **adapt**: also read the *Furnished* feature and status term, as the cards do (v191:12834), and do not drop "Furnished (full inventory to be confirmed…)" (section 19, D4). |
| R32 | Availability: only a row labelled availability, available (from / date / status), move-in (date), vacant (from), occupation (date) or possession (date). Any value that is not "still open" is shown. | README:156, 181 | v191:7153 | **adapt**: values such as "Subject to availability", "For rent" and a date that has passed need a guard (section 19, D3). |
| R33 | Viewing: only a row labelled "Viewing", "Viewings", "Viewing arrangement(s)" or "Viewing time(s)". | README:156 | v191:7154 | **yes**. |
| R34 | "Still open" values are never shown: anything saying confirm, to be advised / agreed / announced / shared / discussed / determined / provided, TBA / TBC / TBD, on request, upon request, point of enquiry, enquir…, vary / varies / variable, negotiab…, if applicable, depend…, ask, n/a, or only dashes. | README:157; PROJECT.md rule 11 | v191:7146 | **yes**. |
| R35 | The rent itself is not read from these rows; it is the price field (R10). The one exception is a rent row that starts with US dollars (R14). | v191:7137, 7171-7184 | same | **yes**. |
| R36 | The classic rent box says a value once when the same text was read under two labels (for example "Lease terms and deposit → Two months…"), and leaves the later label open. | v191:1946-1953 | same | **yes** for any place that lists the rows. |

The patterns, verbatim:

```php
// R25: which section headings feed the rows (v191:7142)
preg_match( '/\b(?:rental|rent|lease|leasing|tenancy|letting)\s+(?:and\s+\w+\s+)?terms?\b|\bterms of (?:the )?(?:lease|tenancy|rent)\b|^rent(?:al)?(?:\s+details)?$|payment plan|^pricing\b/i', $sec['title'] ) && ! preg_match( '/faq|question/i', $sec['title'] )

// R34: a value that is still open (v191:7146)
$open = '/\bconfirm|\bto be (?:advised|agreed|announced|shared|discussed|determined|provided)\b|\btb[acd]\b|\bon request\b|\bupon request\b|\bpoint of enquiry\b|\benquir|\bvar(?:y|ies|iable)\b|\bnegotiab|\bif applicable\b|\bdepend|\bask\b|\bn\/a\b|^-+$/i';

// R28, R29: a figure (v191:7147)
$fig  = '/\d|\b(?:one|two|three|four|five|six|seven|eight|nine|ten|eleven|twelve|eighteen|twenty[- ]four|thirty[- ]six)\b|\ba (?:month|year)\b/i';

// R28 to R33: key => array( label shown, label pattern, value must match, value must not match ) (v191:7148-7155)
'deposit'   => array( 'Deposit', '/deposit/i', $fig ),
'lease'     => array( 'Lease', '/^lease$|lease (?:term|length|period|duration)|minimum (?:lease|term|stay|let|tenancy)|tenancy (?:term|length|period)/i', $fig, '/leasehold|freehold|\b99[- ]?years?\b|\b999\b/i' ),
'service'   => array( 'Service charge', '/service charge|estate (?:fees?|charges?|levy)/i', '/\d|\binclu/i' ),
'furnish'   => array( 'Furnishing', '/furnish/i', '/furnish/i' ),
'available' => array( 'Availability', '/^(?:availab(?:le|ility)(?:\s+(?:from|date|status))?|move[- ]in(?:\s+date)?|vacant(?:\s+from)?|occupation(?:\s+date)?|possession(?:\s+date)?)$/i', '' ),
'viewing'   => array( 'Viewing', '/^viewings?$|^viewing (?:arrangements?|times?)$/i', '' ),

// R14: the first rent row decides; its value must start with dollars (v191:7174, 7176)
preg_match( '/^(?:monthly\s+|asking\s+)?rent(?:al)?(?:\s+(?:a|per)\s+month|\s*\/\s*month|\s*\(\s*monthly\s*\))?$|^monthly rent(?:al)?$/i', trim( $p[0] ) )
preg_match( '/^(?:usd|us\$|\$)\s*(\d[\d,]*(?:\.\d+)?)\s*(k\b)?/i', $v, $m )
```

### What the reader does with a row (output of `run_info.php`)

| Row in the listing (label → value) | Result |
|---|---|
| Deposit → Two months' rent | Deposit: Two months' rent |
| Deposit → 2 months; Security deposit → KES 270,000; Deposit → One month; Deposit → A month | shown |
| Deposit → Refundable security deposit | not shown (no figure) |
| Deposit → To be confirmed at point of enquiry; Negotiable; On request; Varies by unit; N/A | not shown (still open) |
| Deposit → a value of 61 characters or more | not shown (too long) |
| Deposit and lease → Two months' rent; 12-month lease | Deposit: Two months' rent; 12-month lease (the label contains "deposit") |
| Lease → 12 months minimum; Lease term → 1 year, renewable; Lease → A year; Minimum lease → Six months | shown as Lease |
| Minimum stay → 3 nights | Lease: 3 nights |
| Lease terms → Two months' deposit, one-year lease | Lease: Two months' deposit, one-year lease |
| Lease → Flexible; Lease type → Company lease, 2 years; Tenure → 99-year leasehold; Lease → 99-year leasehold | not shown |
| Service charge → Included in the rent; Inclusive; KES 15,000 per month; Estate fees → KES 5,000 a month | shown as Service charge |
| Service charge → Paid by the landlord; On request | not shown |
| Furnishing → Unfurnished; Furnishing status → Fully furnished; Furnishing → Semi-furnished (white goods included) | shown as Furnishing |
| Furnished → Yes; Furnishing → Furnished or unfurnished, to be confirmed | not shown |
| Availability → Available; Available immediately; Available from → 1 December 2026; Available → Now | shown as Availability |
| Move-in → Immediate; Move-in date → January 2027; Occupation → Immediate; Possession → On signing; Vacant → Yes | shown as Availability |
| Availability of parking → 2 bays; Available units → 3; Move-in ready → Yes; Status → Available | not shown (label not an availability label) |
| Availability → Enquire for current availability; To be confirmed | not shown (still open) |
| Viewing → By appointment; Viewings → Daily, 9am to 5pm; Viewing arrangements → …; Viewing times → Weekdays | shown as Viewing |
| Viewing → On request | not shown |
| Rent → USD 5,500 a month; Monthly rent → US$ 5,500; Rent per month → $5.5k; Asking rent → USD 2,600 per month; Rent (monthly) → USD 2,600 | USD rent 5,500 / 5,500 / 5,500 / 2,600 / 2,600 |
| Rent → USD 1.30 per sq ft per month plus VAT | USD rent 1.3 (the "per sq ft" comes from the price label, R12) |
| Rent → KES 650k / $5,000; Rent per sq ft → USD 1.30; Rent → To be confirmed (USD) | no USD rent |

Which headings feed the rows (a Deposit row placed under each, no glance table):

- **Read:** Rental Terms; Lease Terms; Rent and Lease Terms; Tenancy Terms; Terms of the Lease; Terms of tenancy;
  Rent; Rental; Rental details; Rent and Terms of Lease; Letting terms; Leasing Terms; Payment Plan; Pricing;
  Pricing and Availability. Also the glance headings: Property Snapshot, Project Snapshot, Quick Facts, Summary, At
  a Glance, Key Facts.
- **Not read:** Rent and terms; Rent & Terms; Price and Terms; Terms; Rental Terms FAQ; Why It Works for Tenants;
  Arrange a Viewing; Key Details; Overview; Unit Types (furnishing is still read from any of these but the FAQ).
  The code comment at v191:7141 names "Rent and terms" as read; the pattern does not match it.

Which forms a row can take (a Deposit row under "Rental Terms"):

- **Read:** a table row; a table with a header row; a table inside a `figure`; a list item with a bold label; a list
  item with a bold label and a dash; a list item with a plain colon; an ordered list item.
- **Not read:** a paragraph "Deposit: 2 months"; a paragraph with a bold label; a sentence; a list in the intro
  before any heading; and a "Rental Terms" heading that is one level below the description's top heading level,
  because the description is split only at its top level (`blockke_dev_sections()` v191:808-848).

### How it does on the 129 listings today (`run_real.php`, `run_real2.php`, `run_real3.php`)

| | Listings (of 129) |
|---|---|
| Top heading level of the description | H2: 88; H3: 33; H4: 2; no headings at all: 6 |
| A glance or snapshot section found, as v1.9.1 splits the text | 94 |
| A glance or snapshot heading exists at some level | 112 |
| At least one of the six rows shown | 52 |
| Deposit shown | 5 (35477, 37206, 43482, 43855, 43856) |
| Lease shown | 5 (35550, 36731, 42292, 43855, 43856) |
| Service charge shown | 21 |
| Furnishing shown | 15 |
| Availability shown | 23 |
| Viewing shown | 2 (36823, 43543) |
| USD rent row found | 3 (35583, 34171, 36816) |

How much there is to find at all (`run_real3.php`: every table row and "Label: value" item outside the FAQ, in
any section):

| Row label | Listings that carry such a row | of which every such row says confirm, enquiry or on request | Listings where a value passes |
|---|---|---|---|
| Deposit | 49 | 42 | 5 |
| Lease | 62 | 52 | 7 |
| Service charge | 45 | 20 | 21 |
| Furnishing | 37 | 14 | 18 |
| Availability | 53 | 23 | 28 |
| Viewing | 3 | 1 | 2 |

So dropping most deposit and lease rows is the rule working, not a fault: the listings say "To be confirmed at
point of enquiry". What the reader gives for the fixture listings, as v1.9.1 splits the text:

| Fixture | Rows the v1.9.1 reader returns |
|---|---|
| 45577, 45538 | nothing (the snapshot sits under a title heading; and its rows are Rent, Status, Size, not the six labels) |
| 44020 | nothing (no headings; "Furnishing → Furnished" is in a table under a bold paragraph) |
| 43543 | Furnishing: Unfurnished; Viewing: By appointment |
| 42616 | Availability: Available |
| 34171 | Furnishing: Unfurnished; USD rent 2,600 |
| 42450, 42449, 36506, 37551, 42590, 43656, 45231 | nothing |
| 36499 | Furnishing: Unfurnished |
| 36502 | Furnishing: Unfurnished; Availability: Available |
| 39421, 41862 | Availability: For rent |
| 44048 | Availability: It is available |
| 40490 | Availability: From 1st October |
| 43583 | Service charge: KES 22,500 per month |
| 36809 | Service charge: Included in the quoted monthly rent |
| 36834 | Service charge: Included in the advertised rent |
| 36816 | Service charge: USD 0.20 per sq ft per month, plus VAT; USD rent 1.30 |
| 35986 | Availability: Phase 2 operational H2 2026 — units available from Q3 2026 |
| 36826 | Service charge: KES 30 per sq ft per month |
| 40298 | Service charge: KES 30 per sq. ft. per month; Furnishing: Furnished space available |

## 4. The top of the page: status line and key facts

| # | Rule | Source | v1.9.1 | New page |
|---|---|---|---|---|
| R37 | The status line reads "For rent", never "Ready to move in · For rent": the Complete and Ready-now terms every rental carries are sale statuses. | README:156 | v191:7370-7379; printed at v191:8722 next to the area line | **yes**. |
| R38 | The first facts are, in this order: the rent (R20); then the availability the listing states, else its viewing row, else "Availability: On request"; then "Location" with the area. They never read Price, Status: Ready, Completion or Deposit. | README:156; plan:626 | v191:8642-8648 | **adapt**: keep the order and the "never" list. The fallback "Availability: On request" is a placeholder, not a fact, so the design may leave the cell out instead. |
| R39 | The key facts strip has no completion fact on a rental. Its other facts are the layout's own (homes, bedrooms, bathrooms, size, floors, lifts, parking, plot), read from the glance table and the listing fields, or the `bke_dev_facts` override. | README:161 | v191:7715; `blockke_dev_facts()` v191:2838-2934 | **adapt**: commercial listings must not show the bedrooms field (PROJECT.md rule 13; section 18, C8). |
| R40 | The hero carries a two-field form headed "Arrange a viewing" (section 6). | README:158 | v191:8763-8765 | **yes**. |
| R41 | The first photo is eager with `fetchpriority="high"`, alt text "{short name}, {area line}" unless the image has its own alt; a button "View N photos" opens the viewer; with `gallery_ssr`, photos 6 and up are also in the HTML as links to their large file. | README:176; plan:785 | v191:8727-8742; `blockke_dev_gallery_more()` v191:8170-8184 | **adapt**: PROJECT.md rule 9 goes further (every photo visible without script). |

## 5. The Renting section

It takes the place of the payment plan or mortgage section (README:157; v191:9000-9030, Appendix A9). The section
bar links to it as "Renting" (v191:8785-8786).

| # | Rule | Source | v1.9.1 | New page |
|---|---|---|---|---|
| R42 | Small label "Renting"; the heading is the rent itself: "KES 200,000 a month", "From KES 30,000 a month", "KES 710,000 a month (USD 5,500)", or "Rent on request". | README:157; plan:627 | v191:9019-9020 | **yes** (wording); the design chooses the form. |
| R43 | Under it one paragraph: the US-dollar sentence when there is one (R15, R16); then a sentence naming what the listing leaves open; then "Ask for the full tenancy agreement in writing before you pay a deposit or sign." | README:157 | v191:9017-9021 | **yes**. |
| R44 | The open sentence lists, of "the deposit", "the lease length" and "any service charge", those the listing does not state, and always ends with "what the rent covers": "The deposit, the lease length, any service charge and what the rent covers are confirmed when you enquire." With all three stated: "What the rent covers is confirmed when you enquire." | README:157 | v191:9009-9017 | **adapt**: it must not say a service charge is still to be confirmed when the rent row says it is included (section 19, D7). |
| R45 | Then the stated rows in this order: Deposit, Lease, Service charge, Furnishing, Availability, Viewing; only those the reader returned (section 3). | README:157 | v191:9003-9008, 9022-9028 | **yes**; say one value once (R36). |
| R46 | Beside it a panel "See it in person": "Tell us when suits you. We'll confirm that {name} is still available and arrange a time for you to view it." and the button "Arrange a viewing". | README:157 | v191:9029-9030 | **yes**. |
| R47 | There is no calculator, deposit estimate, financing button or investor view. | README:157; plan:627 | the branch at v191:9000 replaces all of v191:9031-9087 | **yes** (PROJECT.md section 1). |

## 6. Forms: wording and fields

| # | Rule | Source | v1.9.1 | New page |
|---|---|---|---|---|
| R48 | Every form asks about availability and a viewing, never the price list, floor plans or payment plan. | README:158; plan:628 | `plans` is false on a rental, v191:7735 | **yes**. |
| R49 | Hero form: title "Arrange a viewing"; line "We'll confirm availability and a time to view. Two details, no obligation."; fields "Full name" and "Phone / WhatsApp" (placeholder "07XX XXX XXX"); button "Check availability". | README:158 | v191:8763-8765; `blockke_dev_quick_form()` v191:8108-8114 | **yes**. |
| R50 | Section bar and mobile bar: the button reads "Arrange a viewing"; both carry a WhatsApp link. | README:158 | v191:8807-8808, 9211-9212 | **yes**. The classic rent bar also shows the rent with its period (plan:701; v191:1993-2000); the new page's bar should too. |
| R51 | Pop-up: small label = the page's short name; title "Arrange a viewing"; line "Availability and viewing times, sent to your WhatsApp."; the same two fields; button "Check availability". Opened from a home's row: title "Availability and viewing", line "{row} at {name}. Sent to your WhatsApp." | README:158 | v191:9213-9216; script swaps v191:7090-7093 | **yes**. |
| R52 | Enquiry section: heading "Speak to an advisor about {short name}"; line "Ask about availability, the lease terms and what the rent covers, or arrange a viewing. We usually reply within a few hours on working days."; the agent's name, role and photo; phone, WhatsApp and email links. | README:158 | v191:9197-9206 | **adapt**: the reply-time sentence is a promise of a timeline; see section 18, C7. |
| R53 | Full form fields, in order: Full name; Phone / WhatsApp; Email (optional); "Interested in" chips when the listing has more than one kind of home, with "Not sure yet" preselected; "When would you like to move in?" chips: This month, 1–3 months, Later (none preselected); "Best way to reach you" chips: WhatsApp (preselected), Phone call, Email; Message (optional), placeholder "Anything we should know: move-in date, household, questions."; button "Send my enquiry". No "Buying to". | README:158 | `blockke_dev_full_form()` v191:8116-8138; `blockke_dev_timelines()` v191:7829-7831 | **yes**; the placeholder needs a commercial variant ("household" is a home word). |
| R54 | Under every form: "We'll reply by WhatsApp or phone. By sending this you agree to be contacted about {short name}." and a link "Privacy policy". | v191:7898-7902; `blockke_dev_privacy_url()` v191:7887-7896 | same | **yes**. The privacy URL is the popup's `privacy_url` (`/privacy-policy-2/`, popup:57) when that snippet is active, else WordPress's own. |
| R55 | Browser checks: name at least 2 characters ("Please enter your name."); phone 9 to 15 digits ("Please enter a valid phone number."); email, if given, well formed ("Please check your email address."). | v191:9921-9925 | same | **yes**. |
| R56 | Server checks and limits: name 2 to 80 characters ("Please add your name."); phone normalised to 2547… and not empty ("Please add a phone or WhatsApp number we can reach you on."); email dropped if invalid; unit 160, timeline one of the three answers, contact 20, source and intent 30 characters of `a-z0-9_-`, message 1,500, page URL 300; campaign tags: `utm_source`, `utm_medium`, `utm_campaign`, `utm_term`, `utm_content`, `gclid`, `fbclid`, `gbraid`, `wbraid`, `ref`, `land`, 250 each. | v191:10586-10613 | `blockke_dev_handle_lead()` | **yes** (PROJECT.md rule 8). Phone rule: `blockke_dev_phone()` v191:585-601, the same as the popup's (popup:189-202). |
| R57 | Spam: a hidden field people never see; a send that fills it is answered as if it worked, is not emailed or saved as a lead, and the last 30 are kept in the option `blockke_dev_spam`. At most 8 sends an hour per IP address, then "Too many requests from this connection. Please WhatsApp us directly." | README:306 | v191:10545-10564; `blockke_dev_note_spam()` v191:10438-10455 | **yes** with new names (PROJECT.md rules 1 and 8: a field name AutoFill leaves alone; transients `bkr_*`). |
| R58 | The forms also post without script: a form-encoded post gets a 303 back to the page with `?bkd_sent=1` or `?bkd_err=` and a code; the thank-you or the error note is then drawn by the server; that returned page is `noindex` and never cached. | README:175; plan:782 | v191:7990-8106, 10904-10959 | **adapt**: PROJECT.md rule 9 requires the no-script post; new parameter names. On `noindex` for the returned page see section 18, C9. |
| R59 | Error note in the form: the server's message, or "Sorry, that didn't go through (error {code})." then the link "Send it on WhatsApp instead". No-script codes and their texts: `rate`, `name`, `phone`, `gone` ("This listing is no longer available."), `fail` ("Sorry, that didn't go through."). | README:306 | v191:9908-9914; `blockke_dev_np_errors()` v191:8048-8056 | **yes**. |
| R60 | A brochure link "Or download the brochure" shows only when the listing has a brochure PDF; its thank-you keeps its own words, "with the brochure for {name}". | plan:648 | v191:8766; script swap v191:7104-7105 | **adapt**: optional. PROJECT.md does not list it; no rental is known to have a brochure (unknown, section 20, Q9). |

## 7. The follow-up questions

| # | Rule | Source | v1.9.1 | New page |
|---|---|---|---|---|
| R61 | After a quick (two-field) enquiry the thank-you panel offers optional questions under "Help us send the right details (optional)". On a rental: "Which home?" when the listing has more than one kind of home, and "When would you like to move in?" with This month, 1–3 months, Later. There is no "Buying to". The button reads "Send these answers". | README:300, 302 | `blockke_dev_more()` v191:7856-7879 | **yes**. |
| R62 | The answers are added to the same saved lead, once, within a day of it, found by the lead's reference; they are emailed as "[Block] More details: {name} - {listing}" with the first line "MORE DETAILS ON A RENTAL ENQUIRY" and "Move-in: {answer}". | README:160, 300 | `blockke_dev_lead_more()` v191:10458-10506 | **yes**. Full text in section 13. |
| R63 | They are not a form element, so form-submit tracking does not count the lead twice. After sending: "Thank you, noted. Your advisor will have this before they get in touch."; on failure: "Those answers did not go through, but your enquiry is with us." | README:300 | v191:7852-7855, 9897-9907 | **yes**. Without script the questions are simply not offered; the full form asks the move-in question itself. |

## 8. The thank-you lines

| # | Rule | Source | v1.9.1 | New page |
|---|---|---|---|---|
| R64 | Heading "Thank you, {first name}." Then "An advisor will be in touch shortly on {the visitor's number} to arrange a viewing of {name}." | README:158 | v191:9878-9883; swap v191:7088-7089 | **yes**. |
| R65 | Other endings by what was asked: a comparison "to compare {name} with similar rentals"; find me one "with matches as they come up"; the brochure "with the brochure for {name}". | plan:648 | v191:9882; swaps v191:7098-7099, 7104-7105 | **yes** where the page has those requests. |
| R66 | Without script, and in the classic box: "Thank you." and "An advisor will be in touch shortly to arrange a viewing of {name}." | README:166 | `blockke_dev_np_thanks()` v191:8063-8070; v191:1976 | **yes**. |
| R67 | Under it a button "Continue on WhatsApp" (section 9, R72), and the brochure button when there is one. | v191:7881-7885 | same | **yes**. |
| R68 | The lead conversions fire once per enquiry: Google Ads `window.blockkeLeadConversion('lead_form')`, GA4 `generate_lead`, Meta `Lead`. After a no-script post they fire on the return page, once per visit. | README:296, 166 | v191:9935-9937; classic return v191:2141-2150 | **yes**. The function and what it needs are in `ROOT/notes/05-live-snippets.md`. |

## 9. WhatsApp and the Ref code

| # | Rule | Source | v1.9.1 | New page |
|---|---|---|---|---|
| R69 | The page's WhatsApp text: "Hi Block, I'm interested in renting {name} ({rent with its period}). Is it still available, and when could I arrange a viewing? Ref W-{listing ID}. {listing URL}". | README:159; plan:694 | v191:8599 | **yes**. Example: "Hi Block, I'm interested in renting Runda Paradise 1 (KES 200,000 a month). Is it still available, and when could I arrange a viewing? Ref W-43543. https://block.ke/property/…/". |
| R70 | With "From" the bracket reads "(from KES 30,000 a month)"; with no rent there are no brackets at all. | plan:635, 694 | v191:8599; classic v191:1901-1907 | **yes**. |
| R71 | {name} is the page's short name without "to Let", "for Rent", "to Rent" or "for Lease" (section 10, R77). | README:159; plan:648 | `blockke_dev_rent_name()` v191:7281-7285 | **yes**. |
| R72 | After an enquiry, "Continue on WhatsApp" carries: "Hi Block, I've just sent an enquiry about renting {name} on block.ke. When could I arrange a viewing?", then on their own lines "Interested in: {home}" if one was picked, "Name: {visitor's name}", the URL and "Ref W-{ID}". Without script: "…When could I arrange a viewing? Ref W-{ID}. {URL}". | README:159 | swaps v191:7106-7109; no-script v191:8066; classic v191:1904 | **yes**. |
| R73 | Every WhatsApp link on a rental page, the ones the script builds included, says "viewing", carries the Ref and has `data-blkct="1"`. | README:159; PROJECT.md rule 14 | v191:8590, 8600, 8807, 9188, 9205, 9211; swaps v191:7110-7113 | **yes**. Why: the site-wide snippet 40769 rewrites the text of any WhatsApp link without that attribute to its own sale-style message, "Hi Block, I'd like to enquire about {title} ({price}). Ref W-{ID}. {URL}"; a text that contains "viewing" is kept and only gets the Ref added (`ROOT/ref/live-snippets/40769.printed.html` lines 1, 12-18). |
| R74 | The Ref code is "Ref W-" followed by the listing's post ID, for example "Ref W-45577". It is the same code snippet 40769 uses, and the lead email repeats it: "Listing: {title} (Ref W-{ID})". | README:159 | v191:8599, 10790; `40769.printed.html` line 1 (`"ref":"Ref W-45577"`) | **yes**. |
| R75 | The number is the listing agent's mobile (else phone) when the agent is published and has one, else Block's default 0725 937 686 (+254725937686; WhatsApp 254725937686). | plan:73 (decision 8); PROJECT.md rule 14 | `blockke_dev_agent()` v191:2469-2499 (identical in v182:1711-1741); defaults v191:306-308. The classic rent box uses the default only (v191:1897-1906, 1989) | **yes**, the layout's rule (PROJECT.md rule 14). |

## 10. The page's name

| # | Rule | Source | v1.9.1 | New page |
|---|---|---|---|---|
| R76 | The H1 is a name, not a selling phrase. In order: the listing's custom field `bke_dev_name`; else the first part of the title (split at " — ", " – ", " \| " or ": "). When that part sells ("for sale", "for rent", "to let", or it starts with a bedroom count) it gives way to: the development page's `bke_dev_name` (for a unit), else the Project term as the title writes it, else another part of the title that is a name, else what follows "at". A name over 52 characters gives way to the Project term. A trailing ", {area}" or ", {area}, {city}" is dropped. | v191:160-163; README:160 | naming block of `blockke_dev_data()` v191:7302-7358; `blockke_dev_seo_name()` v191:3852-3855; `blockke_dev_term_name()` v191:3878-3919 | **adapt**: keep `bke_dev_name` first and the Project term next, but do not port the rest as it stands; see the results below. |
| R77 | In sentences that already say the home is to rent (the WhatsApp text, "See it in person", the lead's "Rental:" line, the thank-you), the name drops "to Let", "for Rent", "to Rent" and "for Lease", never "Rent-to-Own"; if nothing would be left the name stays as it is. "Furnished 2 Bedroom Apartment to Let in Brookside" reads "Furnished 2 Bedroom Apartment in Brookside". | README:159; plan:648 | `blockke_dev_rent_name()` v191:7275-7285 | **yes**. |
| R78 | The short name (breadcrumb, section bar, form consent line, pop-up label, photo alt, leads) is `bke_dev_name`, else the development's, else the Project term, else the H1 name. | v191:162-163 | v191:7341-7343, 7356-7358, 7610-7613 | **yes**. |
| R79 | A lead names the listing by the page's short name, not by its whole SEO title. | README:160 | v191:10727-10729 | **yes**. |
| R80 | A similar-rental card is named by its Project term, else by its title without the selling words. | v191:3921 | `blockke_dev_listing_name()` v191:3922-3941 | **adapt**: with no Project term it returns "3 Bedroom" for "3 Bedroom Apartment for Rent in Kilimani, Lenana Road" and "Nairobi" for 40490's title (`run_mode.php`). |
| R81 | Three rentals were to get a `bke_dev_name` from staff, because the rule gives them "Villa for Rent", "4 Bedroom" and "Windsor 2 bedroom": 42616, 40490, 42370. | plan:809 (the plan's data fix "R7", not this file's R7); plan:654; plan:1008 | data, not code | **unknown** whether it was done (section 20, Q2). In the feed 42616 still carries the Project term "Villa for Rent". |

The clean-up pattern of R77, verbatim (v191:7282-7283):

```php
$n = preg_replace( '/(?:^|\s+)(?:to\s+let|for\s+rent|to\s+rent|for\s+lease)\b(?![- ]to[- ]own)/iu', '', (string) $short );
$n = preg_replace( '/^[\s,;:|–—-]+|[\s,;:|–—-]+$/u', '', preg_replace( '/\s+/u', ' ', $n ) );
```

**What the naming rule gives on the 129 listings** (`run_name.php`: the v1.9.1 naming block run on each title,
Project term, area and city from the feed; `bke_dev_name` and unit masters are not in the feed, so they are taken
as empty; the main area is taken as the first area listed):

- 75 H1s are the Project term; 35 listings have no Project term.
- **19 names are shared by 47 listings.** The H1 names the building, not the home: "Kings Courtyard" for 45577
  (3 bedrooms) and 45538 (2 bedrooms); "Taurus Apartments" for 36499, 36502 and 36506; "Versailles Garden" for
  42590, 42591, 42592, 44048 and 44050; "Sunpark Residence II" for 42449 and 42450; "The Link Logistics Park" for
  35986, 36135 and 36136; "Riara One" for 43995 and 44020.
- **Six H1s are wrong:** 40490 "Nairobi" (title "4 Bedroom House For Rent, Ole Odume Road, Kilimani – Nairobi");
  29462 "Kilimani" ("Furnished Executive Studio for Rent \| Kilimani"); 42340 "Westlands" ("Westlands \|
  3-Bedroom Apartments to Let from KES 150,000"); 43644 and 43656 "Apartments" (titles that start "Apartments \|");
  42616 "Villa for Rent" (its Project term).
- 16 H1s keep a rent phrase, because the title has no other name: "2 Bedroom Apartment for Rent in Westlands"
  (33230), "Furnished 3 Bedroom Apartment to Rent in Riverside" (41862). The selling-phrase test of R76 does not
  know "to rent" or "for lease" (v191:3853), though the rental test does (R3).
- Two Project terms are misspelt and reach the short name, so they would reach leads and WhatsApp texts:
  "Muthaiga Busines s Center" (36739) and "Graylands Industrial PARK" (36636).

So for a rental the building's name alone is not enough: the page has to say which home it is (type and
bedrooms, from the listing's own title, category and bedrooms field) in or beside the H1. That is a design
decision; the rule to keep is R76's first two steps and R77.

## 11. Why Block, the disclaimer, the FAQ heading and other tenant wording

| # | Rule | Source | v1.9.1 | New page |
|---|---|---|---|---|
| R82 | "Why Block" on a rental is the tenant's version, used word for word. Heading: small label "Why Block", then "Advice first. *Then a decision.*"; three numbered items (01, 02, 03). | README:161, 387; plan:629; plan:1065 | `blockke_dev_rent_defaults()` v191:276-285; drawn at v191:9143-9153 | **yes**, verbatim (below). |
| R83 | The disclaimer at the foot of the enquiry section is the rental one. | README:161, 387; plan:629; plan:1065 | v191:283; drawn at v191:9208 | **yes**, verbatim (below). |
| R84 | Both texts can be overridden by settings (`rent_why`, `rent_disclaimer` in the option `blockke_dev_page`); empty falls back to the code's own. | README:387 | v191:405-406, 9144-9145, 9208 | **adapt**: the new snippet has its own option (`blockke_rent_page`). Whether to honour values saved in `blockke_dev_page` is section 20, Q3. |
| R85 | The FAQ heading reads "Questions tenants *ask*" (small label "FAQ"), with "Can't see yours? Ask us on WhatsApp." | README:161; plan:629 | v191:9186-9188 | **yes** (PROJECT.md rule 13). |
| R86 | The note over the amenities checklist says "The practical things tenants ask about first." | README:161 | v191:8947 | **yes**; commercial needs its own word. |
| R87 | There is no "For investors" box on a rental, even when the description has an investor section. "Who it suits best" stays when the description has that list. | plan:648; README:197 | v191:9092; `blockke_dev_suits()` v191:3636 | **yes**. The header comment at v191:205-207 still says the investor view "shows only when the listing has one"; the code at v191:9092 is later and wins. |
| R88 | Nothing on the page offers a price list, floor plans, a payment plan or a mortgage; similar rentals carry no "Ready now" badge. | README:158, 161; plan:627-630 | v191:7735 (`plans` false), 9000, 4714 | **yes** (PROJECT.md section 1). |

The texts of R82 and R83, verbatim from v191:278-283:

```php
'rent_why'        => array(
	array( 'Advice, not pressure', 'We compare rentals, streets and rents across the area side by side, so you choose with clarity.' ),
	array( 'Checks before you commit', 'We help you check the tenancy agreement, the deposit and what the rent covers before you pay or sign.' ),
	array( 'One advisor throughout', 'From the first viewing to signing the lease, the same advisor stays with you.' ),
),
'rent_disclaimer' => 'Listings are published in good faith from information supplied by owners and landlords, and may change without notice. Please confirm the rent, availability, the deposit and the lease terms before you commit. Block Real Estate does not give financial or legal advice; we recommend reading the full tenancy agreement before you sign or pay a deposit.',
```

## 12. Similar rentals

| # | Rule | Source | v1.9.1 | New page |
|---|---|---|---|---|
| R89 | Matching: other published listings with the For Rent term, not *Sold* or *Let*, not commercial or land; same number of bedrooms in the same area within 25% of the rent, then at any rent, then in the nearest neighbouring areas, then one bedroom more or fewer in the same area, then the next ring of areas while fewer than 3 are found. Up to 6 cards. | README:316-323, 340-341 | `blockke_dev_sim_pool()` v191:4231-4301; `blockke_dev_similar_units()` v191:4348-4429; settings v191:320-348 | **adapt**: the order is right for homes. A commercial listing finds nothing this way (the pool leaves commercial out, v191:4238): offices need offices, warehouses need warehouses (section 18, C8). |
| R90 | One card per development; never this listing, its own development or Project; never a rent per sq ft; a rent over KES 2M or a both-tagged listing without a monthly label is left out; villas and houses are compared only with villas and houses. | README:316-323 | v191:4295, 4357-4360, 4375 | **yes**. |
| R91 | The editor's own picks (custom field `bke_dev_similar`, listing IDs) come first, in their order. `bke_dev_similar_off` hides the block. | README:56 | `blockke_dev_sim_picks()` v191:4313-4340; v191:4621 | **adapt**: existing fields; reading them is optional. |
| R92 | A rental card carries no sale badge ("Ready now" is a sale stage). | README:161, 173; plan:630 | v191:4714 | **yes**. |
| R93 | The card shows: photo, name (R80), the rent as a card states it (R22), then "2 bed · 111 m² · Westlands", plus "Furnished" when the rental is furnished (the *Furnished* feature, the *Furnished* status, or a Furnishing row that says furnished and not unfurnished, semi, part, "or", optional or "also"). | README:173; plan:723 | v191:4706-4717; `blockke_dev_sim_meta()` v191:4575-4593; furnished test v191:12834-12841 | **yes**. |
| R94 | Heading: small label "Also consider"; then "Other 2-bedroom rentals in *Kilimani*", or "2-bedroom rentals in and around *Kilimani*" when neighbours were needed, or "Similar rentals in *Kilimani*" when the cards differ in bedrooms. | README:316-323 | v191:4669-4689; `blockke_dev_key_phrase()` v191:4491-4499 | **yes**. |
| R95 | The "See all" link reads "See all 2-bed rentals in Kilimani" ("See all studio rentals in …", "See all rentals in …" without a bedroom count). It goes to the area's rent page when the v1.6 area pages are on, else to the site search for that area, For Rent and those bedrooms (`/advanced-search/?location=Kilimani&property_action_category[]=for-rent&componentsbeds=2`). | README:236 | v191:4701-4703, 4722-4724; `blockke_dev_search_url()` v191:4527-4544; `blockke_dev_seg_url()` v191:4553-4572 | **adapt**: the wording is the rule. Where it should point on the live site is unknown here (section 20, Q4). |
| R96 | On a card that stands for several rentals of one development (area and search pages), "See all N in the price list" becomes "See all N rentals", and the card never links to a sale page. | plan:724; README:173 | `blockke_bkn_for_act()` v191:13082 | **no**: the page's similar cards are single listings. |
| R97 | Under the cards: "Compare these with an advisor" (pop-up "Compare with an advisor": "We'll compare {name} with similar rentals on rent, size and location."). With no match: the heading "No other 2-bedroom rentals in Kilimani listed right now", the line "Tell us what you're looking for and an advisor will send you matches as they come up, often before they are listed." and the button "Ask an advisor to find one" (pop-up "Find me a rental like this"). | README:316-323 | v191:9169-9175, 4697-4699; swaps v191:7100-7103 | **adapt**: "often before they are listed" is a claim about a service; see section 18, C7. |

## 13. Enquiries: the email, Zoho, the saved row and the inbox

| # | Rule | Source | v1.9.1 | New page |
|---|---|---|---|---|
| R98 | The enquiry is saved first, then sent to Zoho, then emailed, and the saved row is updated with what Zoho answered and whether the email was handed over. A slow or failing step cannot lose it. | README:292; PROJECT.md rule 8 | v191:10808-10829, 10831-10864, 10866-10881 | **yes**. |
| R99 | Subject: "[Block] Rental enquiry: {visitor's name} - {page's short name}", with " - {home}" added when the visitor picked one. | README:160, 304; plan:734; PROJECT.md section 5 | v191:10868 | **yes**. |
| R100 | First line of the body: "RENTAL ENQUIRY (property page)". Second: "Rental: {name without "to Let" and the like} - {the rent phrase}", or "- rent on request". Third: "Interested in: {home}" or "Interested in: -". | README:160, 304; plan:738, 641 | v191:10760-10763 | **yes**. The classic box said "(classic page)" (v191:10761); the new page is one page, so one description line. |
| R101 | The move-in answer is printed as "Move-in: {answer}", never "Timeline:"; there is no "Buying to" line. | README:160, 302 | v191:10776-10781 | **yes**. |
| R102 | The form is named in a line "Form: …": "Hero form (viewing)", "Enquiry pop-up", "Full enquiry form", "Viewing request" (the Renting section's button), "Availability and viewing request" (a home's row), "Section bar", "Mobile bar", "Compare request (similar rentals)", "Find-me-one request (no similar homes listed)", "Brochure download", "Comparable rents request", "Classic page (viewing)". | README:160; plan:738-739 | v191:10688-10726, 10741 | **yes** for the forms the page has. The label is looked up by the form's intent, then its source (v191:10741). |
| R103 | Recipients: the notify list (`notify_emails` of `blockke_dev_page`; empty means the popup's list, `blockke_buyer_popup`; else loyd@block.ke) plus the listing agent's email. Plain text; Reply-To is the visitor when an email was given. | README:293, 365, 500 | `blockke_dev_lead_to()` v191:10396-10416 (identical in v182:8635-8655); headers v191:10869-10872 | **yes** (PROJECT.md section 5). |
| R104 | Zoho gets the same description through the popup snippet's `blockke_bp_send_zoho()`, with Company "Individual tenant" in place of "Individual buyer", and "Rental" in the owner's *Enquiry type* field once its web-form name is set (`zoho_enquiry_field`); sale leads never send that field. | README:160, 304, 398; plan:751-754; plan:1084 | v191:10831-10864; `blockke_dev_zoho_rent_args()` v191:10508-10535 (Appendix A13) | **yes**. The popup hard-codes the Company (popup:322), so the swap is done in flight with an `http_request_args` filter added just before the call and removed just after. |
| R105 | The saved row goes at the top of the option `blockke_dev_leads` (the last 200 are kept, not autoloaded) and carries `'kind' => 'rent'`. | README:292, 304; plan:743; PROJECT.md section 5 | `blockke_dev_save_lead()` v191:10419-10435; row v191:10810-10828 | **yes**: same option, same keys (list below). |
| R106 | The inbox shows Type *Rental* for a row with `kind` = `rent`, "Move-in" in place of "Timeline", and no "Buying to". | README:498; plan:748 | inbox:206-223, 274-289 | **yes**: nothing to build; the row format is the contract. |
| R107 | The follow-up answers arrive as "[Block] More details: {name} - {listing}", first line "MORE DETAILS ON A RENTAL ENQUIRY". | README:160 | v191:10480-10503 | **yes**. |
| R108 | The answer to the browser carries `ok`, the visitor's number as displayed and the lead's reference (for the follow-up questions), plus the brochure URL when there is one. | v191:10883-10892 | same | **yes**. |

**The route in v1.9.1.** `POST /wp-json/block/v1/development-lead`, open to everyone (v191:10961-10974). The page
script posts JSON: `listing`, `name`, `phone`, `email`, `unit`, `purpose`, `timeline`, `contact`, `message`,
`source` (the form), `intent` (what opened the pop-up), `page`, `attr` (the campaign tags) and `hp` (the hidden
field) (v191:9926-9927). A request that carries `more` (a lead's reference) is the follow-up answers instead:
`more`, `listing`, `unit`, `timeline` (v191:9898, 10567-10570). A no-script form posts the same fields
form-encoded with a marker field, and is answered with a 303 (v191:10904-10933). The new page has its own route,
`block/v1/rental-lead` (PROJECT.md rule 1), and can keep this shape.

**The email body, line by line** (v191:10760-10806, then v191:10873). Lines in square brackets appear only when
there is something to say:

```
RENTAL ENQUIRY (property page)
Rental: {name} - {rent phrase}                      e.g. Rental: Runda Paradise 1 - KES 200,000 a month
Interested in: {home, or -}

Name: {name}
Phone / WhatsApp: {0712 345 678}  (https://wa.me/{254712345678})
Email: {email, or -}
[Purpose: {purpose}]                                 only if a form sends one; the rental forms do not
[Move-in: {This month | 1–3 months | Later}]
[Best way to reach them: {WhatsApp | Phone call | Email}]
[Message: {message}]

Form: {form name, R102}
Listing: {full listing title} (Ref W-{ID})
Page: {page URL as sent, else the permalink}
Channel (auto-tagged): {channel}[ | campaign: {utm_campaign}]
[UTM: {utm_source} / {utm_medium}[ / term: {utm_term}]]
[gclid: {gclid}]
[Landing page: {first page of the visit}]
[Referrer: {referrer}]
Received: {Wed 11 Nov 2026, 18:40} EAT

Reply on WhatsApp: https://wa.me/{254712345678}
Zoho: {what Zoho answered}

SLA: first reply within 15 minutes; log the lead in Zoho under your name.
```

The channel and the lead source come from the popup snippet's `blockke_bp_channel()` and
`blockke_bp_lead_source()` when it is active, else "Google Ads" with a gclid, "Meta" with an fbclid, "Website"
otherwise, and the source "Website - block.ke" (v191:10614-10615; popup:212-284).

**The follow-up email** (v191:10480-10497): subject "[Block] More details: {name} - {listing}"; body

```
MORE DETAILS ON A RENTAL ENQUIRY
{name} answered the optional questions after enquiring about {listing} ({time of the enquiry}).

[Interested in: {home}]
[Move-in: {answer}]

Phone / WhatsApp: {0712 345 678}  (https://wa.me/{254712345678})
Add these to the lead in Zoho.
```

**What Zoho is sent** (v191:10840-10857; popup:287-350): `lead_source`; `submarket` (the area when it is one of the
23 names at v191:10833, "Upperhill" for Upper Hill, else "Other", or "-None-" with no area); `buyer_type` ("Local"
when the number starts 254, else "Diaspora"); `name`, `phone`, `email`; `areas` (the area); `budget` empty; `unit`
("{short name} - {home}"); `alerts` false; `channel`; `campaign`; `page`; `page_title` (the listing title);
`description` (the email's description lines). The popup maps these onto the web form (its saved map, or First
Name, Last Name, Phone, Mobile, Email, Company, Lead Source, Description by default). The swap to "Individual
tenant" only happens when the posted Company is exactly "Individual buyer" (v191:10527).

**The saved row** (v191:10810-10828), which is what the inbox reads (inbox:276-289):

| Key | Value | The inbox shows |
|---|---|---|
| `time` | `current_time( 'mysql' )` | date |
| `listing` | listing ID (integer) | used to type old rows with no `kind` |
| `dev` | the page's short name | Interest: "{dev} – {unit}" |
| `unit` | the home picked, or '' | see `dev` |
| `name`, `phone`, `email` | as checked (phone in digits, 2547…) | name, call and WhatsApp links, email |
| `purpose` | '' on a rental | nothing on a Rental row |
| `timeline` | the move-in answer or '' | "Move-in: …" |
| `form` | "{source}" or "{source}/{intent}", for example `hero`, `modal/viewing` | "Development page ({form})" |
| `channel`, `source` | the channel and the Zoho lead source | Source column shows `channel` |
| `page` | page URL | link on the Interest cell |
| `zoho` | "not sent", then Zoho's answer | Zoho column |
| `mail` | false, then whether `wp_mail` accepted it | "Handed over" or "No" |
| `ref` | 20 letters and digits (`wp_generate_password( 20, false, false )`) | not shown; the follow-up questions quote it |
| `kind` | `rent` | Type "Rental" |
| `more` | set to the time when the follow-up answers are added | not shown |

Three things about the row for the builder. The inbox prints "Development page (…)" before the form name on every
row of this option (inbox:283); a rental row will read "Development page (hero)" unless the inbox is changed. A
`form` value that starts "area page:", "new developments:" or "{word} page:" would be typed Area, Index or Topic if
`kind` were missing (inbox:212-220), so keep `kind` and keep `form` in the v1.9.1 shape. And both snippets write
the same option, so the new one must read, add and save in one step, as `blockke_dev_save_lead()` does, and keep
the cap of 200 and the `false` autoload.

## 14. Structured data

All of it sits behind the switch `listing_jsonld` in v1.9.1 (README:177, 395; plan:758-769).

| # | Rule | Source | v1.9.1 | New page |
|---|---|---|---|---|
| R109 | One JSON-LD block in the head, `script#bke-schema-ld`: a `RealEstateListing` for the page and the `BreadcrumbList` of the trail the page shows. | README:177; plan:761, 769 | `blockke_dev_ld_graph()` v191:8442-8503; printed at v191:8540-8550 | **adapt**: print it with `wp_print_inline_script_tag()` (PROJECT.md rule 5); keep the id (R116). |
| R110 | The listing node: `@id` "{URL}#webpage", `url`, `name` (the H1), `datePosted` (the post date), `breadcrumb` → "{URL}#breadcrumb", `image` (every photo, the 1536 size else the original), `about` and `offers`. Block stays Rank Math's publisher; no `seller`. | plan:761, 768 | v191:8470-8500 | **yes**. |
| R111 | `about` is what is let: `Apartment`, `House` (Houses category) or `SingleFamilyResidence` (Villas category), from the category, or from the title's words when the category gives none or several; with `numberOfBedrooms`, `floorSize` and an address down to the area (`addressLocality` = area, `addressRegion` = city when different, `addressCountry` "KE"). No map pin. | plan:762-763 | `blockke_dev_ld_about()` v191:8299-8366 | **yes**. |
| R112 | A commercial listing (category Commercial, Offices or Warehouses; or no category and a title naming offices, warehouses, godowns, shops, commercial or retail) is a `Place`; its size field goes in `additionalProperty` "Floor size"; no bedrooms. | plan:764 | v191:8302-8303, 8338-8350 | **adapt**: fixture 37551 (apartments filed under Commercial and Offices) would be a `Place`; the commercial test needs the title too (section 18, C8). |
| R113 | `offers` is an `Offer` with `businessFunction` `http://purl.org/goodrelations/v1#LeaseOut`, the price and currency as the lease is set (dollars for a USD rent, KES first or not), and a `UnitPriceSpecification` with the period: `unitCode` MON, WEE, DAY or ANN; a night is DAY with `unitText` "per night"; per sq ft is `unitCode` FTK, `referenceQuantity` 1 FTK and `unitText` "per sq ft per month"; a label that names no period goes in `unitText` as written. | plan:765-766; README:177 | `blockke_dev_ld_offer()` v191:8377-8430 (Appendix A11) | **yes**. |
| R114 | A rent the page gives as "From" is an `AggregateOffer` with `lowPrice` (and `highPrice` and `offerCount` when the homes' rows state different KES rents); "Up to" is an `AggregateOffer` with `highPrice`. | plan:767 | v191:8387-8408 | **yes**, following the page's own "From" rule (R19). |
| R115 | No `availability`. `availabilityStarts` only when the Availability row is a date ("1 December 2026", "December 2026", "2026-12-01"). No Offer at all when the rent is on request. | plan:768; plan:999, 1065, 1076 | v191:8379-8381, 8424-8427; `blockke_dev_ld_day()` v191:8282-8291 | **yes**. A month without a day is given as the 1st ("December 2026" → 2026-12-01), which says more than the listing does; and a date already past should not be printed (section 19, D3). |
| R116 | Rank Math's own `BreadcrumbList` gives way to this one; a Rank Math `WebPage` node with the same `@id` is retyped `RealEstateListing`; and the header injection's browser-built listing (`script#bke-listing-schema`, which states InStock) stands down because `#bke-schema-ld` exists. | README:177; plan:761, 769 | `blockke_dev_ld_rank_math()` v191:8521-8538; v191:8433-8435 | **yes**: keep the id `bke-schema-ld`, and always print the block (at least the trail). The header injection is on every listing page today: `ROOT/ref/pages-stop/45577.html:2927` is `script#bke-listing-schema`, line 3021 is its guard `if (document.getElementById('bke-schema-ld')) { return; }`, and it runs once the document is parsed, so a block printed anywhere in the HTML stops it. Left to itself it builds a listing from the classic page's markup with `availability` InStock (same file, lines 3030-3124). |
| R117 | A listing whose own text already holds a `RealEstateListing` keeps that one; only the trail is printed. | README:177; plan:775 | `blockke_dev_ld_own()` v191:8223-8233; v191:8471 | **adapt**: the test is a regex over script tags, which PROJECT.md rule 5 forbids. Test the raw post content with plain string search for `application/ld+json` and `RealEstateListing` instead. |
| R118 | The breadcrumb is the page's visible trail: Home, the Buy/Rent term (For Rent), the area, the development when the listing is a unit, then the page itself. | plan:769 | v191:8448-8469; crumbs v191:7633-7650 | **yes**: whatever trail the new page shows. |
| R119 | The price, currency and unit in the data must equal the rent the page shows. | plan:957 | tested (section 16) | **yes**: one helper for both. |

What the Offer looks like for each kind of rent (output of the verbatim function, `run_ld.php`; `url` left out):

| Rent on the page | `offers` |
|---|---|
| KES 135,000 a month | `Offer`, `price` 135000, `priceCurrency` KES, spec `price` 135000, `unitCode` MON |
| KES 6,500 a day | `Offer`, 6500 KES, `unitCode` DAY |
| KES 45,000 a week | `Offer`, 45000 KES, `unitCode` WEE |
| KES 9,000 a night | `Offer`, 9000 KES, `unitCode` DAY, `unitText` "per night" |
| KES 1,200,000 a year | `Offer`, 1200000 KES, `unitCode` ANN |
| KES 125 per sq ft a month | `Offer`, 125 KES, `unitCode` FTK, `referenceQuantity` {1, FTK}, `unitText` "per sq ft per month" |
| From KES 30,000 a month | `AggregateOffer`, `lowPrice` 30000, spec `minPrice` 30000, MON |
| From KES 60,000 a month, rows at 60,000 and 80,000 | `AggregateOffer`, `lowPrice` 60000, `highPrice` 80000, `offerCount` 2, spec `minPrice` and `maxPrice`, MON |
| Up to KES 90,000 a month | `AggregateOffer`, `highPrice` 90000, spec `maxPrice` 90000, MON |
| USD 2,600 a month, and KES 337,000 a month (USD 2,600) | `Offer`, `price` 2600, `priceCurrency` USD, MON |
| USD 1.30 per sq ft a month | `Offer`, `price` 1.3, USD, FTK, "per sq ft per month" |
| KES 300,000 per quarter | `Offer`, 300000 KES, no `unitCode`, `unitText` "per quarter" |
| KES 135,000 a month, Availability "1 December 2026" | as the first row, plus `availabilityStarts` "2026-12-01" |
| Rent on request | no `offers` |

## 15. What a rental page never shows, and other behaviour that goes with the rules

| # | Rule | Source | v1.9.1 | New page |
|---|---|---|---|---|
| R120 | No mortgage or instalment calculator, no payment plan, no deposit estimate, no price list or floor-plan offer, no investor box, no "Buying to", no completion fact, no "Ready" status, no sale badge on similar rentals, no ad landing page. | README:156-161; plan:7, 625-630 | v191:7715, 7735, 7874-7876, 8128-8133, 8644-8648, 9000, 9092, 4714; `blockke_dev_lp_ok()` v191:735-751 | **yes** (PROJECT.md section 1). |
| R121 | The buyer price-check popup stays off on the page; its attribution keeper still runs. | README:361; PROJECT.md section 5 | `$_GET['blockpop'] = '0'` inside `template_include`, v191:10256-10258 | **yes**. The popup already skips a listing without the For Sale term (popup:115-117; `ROOT/notes/05-live-snippets.md` section 2.1), so on a rent-only listing this is a second guard. |
| R122 | Campaign tags travel with the enquiry: the page script reads `sessionStorage` `blkbp_attr` (written by the popup snippet's keeper), adds any of the nine URL tags it lacks, and sets `land` and `ref` when missing. A no-script post takes the tags from the form's page URL. | README:295 | v191:9862-9865; `blockke_dev_np_attr()` v191:10936-10948 | **yes**. Details of the keeper: `ROOT/notes/05-live-snippets.md` section 2.2. |
| R123 | Events the page sends (GA4 or dataLayer): `click_whatsapp`, `click_call`, `enquiry_open` (with the source), `generate_lead` (with `lead_source` and `lead_unit`), `lead_details` (the follow-up answers), each with `development` (the slug) and `listing_id`. | README:296 | v191:9867-9870, 9905, 9935, 9968 | **adapt**: keep the names so existing reports still count rentals; the script is new. Snippet 40769 separately sends `contact_click` for every tel, WhatsApp and mailto click (`ROOT/ref/live-snippets/40769.printed.html` line 49). |
| R124 | Visitors whose browser time zone is not Nairobi see an international example in the phone field ("+44 7700 900123" in the UK). | README:345 | v191:9943-9951 | **adapt**: optional; it costs script bytes (PROJECT.md rule 9). |
| R125 | The theme's view statistics keep counting on the page (not for editors, previews or prefetches). | README:366 | `blockke_dev_count_view()` v191:10221-10238 | **adapt**: a development-layout rule, not a rental one; see `ROOT/notes/01-dev-layout.md` ("View counting"). |
| R126 | The page a no-script post returns to is `noindex, follow` and sent with no-cache headers. | README:393; plan:782 | `blockke_dev_np_noindex()` v191:8085-8106 | **adapt**: section 18, C9. |
| R127 | On classic pages of rentals, v1.9.1 adds a rent box (`#bkd-rent`), a phone bar and "Around this property", hides the theme's mortgage calculator, in-page tour form and agent bar, and marks the body `bkd-rent-cls`. All of it stands down when the layout draws the page (`$GLOBALS['blockke_dev_active']`). | README:164-169; plan:682-707 | `blockke_dev_classic_rent_target()` v191:1853-1863; CSS v191:2005, 2050 | **no** as features (the new page replaces the classic page). **yes** as a duty: the new page must make these stand down where it renders (PROJECT.md section 2). The check is the global at v191:1862; how and when to set it is in `ROOT/notes/01-dev-layout.md` section (b). |

## 16. What the rental tests asserted (as far as the README and the plan describe them)

The test file itself (`rent.php`, 236 checks) is not in the repository or its history, nor in this session's
scratchpad; the plan names an older scratchpad path that no longer exists (plan:976). What is known:

| # | Asserted | Source |
|---|---|---|
| T1 | 236 rental checks pass with `rent_pages` on; the v1.8.2 code passes 21 of them. They grew from 121. | README:482; plan:657 |
| T2 | Covered: the Runda rentals (35583, 42616, 43543) and the Runda sale; made-up listings; the rent phrase in every form: a day, a week, per sq ft, on request, US dollars with and without a dated rate. | README:482; plan:654, 957 |
| T3 | The classic page's rent box and its anchors; no mortgage; one bottom bar on a classic rental below 992px, with the rent and the viewing button visible without scrolling. | README:482, 484; plan:957 |
| T4 | The structured data of 20 made-up rentals against hand-checked output; with `listing_jsonld` one structured-data block; the JSON-LD price, currency and unit equal the visible rent on all 230 live rental pages and 26 local ones (the plan asked for all 121 rentals). | README:482, 484, 485; plan:957 |
| T5 | 23 rent-card phrases. | README:482 |
| T6 | The lead subjects and the saved `kind` of a classic-box lead, a layout lead, a sale lead and an area lead; the inbox's Types (both inbox versions loaded in either order, the real admin page and the CSV). | README:482, 485, 506 |
| T7 | Every switch off: pages byte for byte those of today's snippet 42706, over HTTP (18,501 requests a pass, four site copies, snippets 40752, 40769 and 44071 loaded) and as 7,048 CLI renders with their leads; the only differences are the three landing pages the new guard refuses and listing-order ties that also vary between two runs of the old code. | README:483; plan:951 |
| T8 | Each switch alone and in the staging order (18 passes, 52,227 requests): `rent_pages` changes only rental pages and their editor views; `rent_cards` only rent cards; no sale listing, sale segment or sale search changes in any state. | README:484; plan:952-954 |
| T9 | In a browser (Chromium, 1440 and 390 px, the live site's floating elements present): no page or console error on a sale development page, its landing page, classic sale and rental pages, rentals on the layout, an area page, a rent segment and a rent search, in any of the 18 states. | README:484; plan:952 |
| T10 | No PHP notices; `php -l` clean; the PHP 7.4 compatibility scan clean. | README:485; plan:961 |
| T11 | The title widening (R3) changes the action of only 44049 on that day's data. | plan:650, 953 |
| T12 | A no-script post from a page makes one lead and a 303 back with `?bkd_sent=1`; the rate limit and the honeypot still work. | plan:782, 960 |

For the new page the ones to carry over as its own tests are T2 (every row of the truth table in section 2), T4
(data equals the visible rent on all 129), T6 (subject, body lines, saved `kind`, inbox Type), T9, T10 and T12,
plus "inert with the option unset" in place of T7.

## 17. Decided in the plan, but not in v1.9.1 (or done differently)

| # | The plan said | What v1.9.1 does | For the new page |
|---|---|---|---|
| G1 | WhatsApp on the classic box: "…Is it still available, and when could I view it?" (plan:694). | "…and when could I arrange a viewing? Ref W-{id}." (v191:1905, 8599), so snippet 40769 leaves the text alone. README:159 has this wording. | The README wording (R69). |
| G2 | "USD is used only after batch R sets `bke_rent_usd`" (plan:765); "`bke_rent_usd` is the one source of truth" (plan:639). | A rent row in the text that starts with dollars also makes the rent a dollar rent, page and structured data (v191:7171-7184, 7234). README:162 describes this. | The README rule (R14). KES first still needs the field and its date. |
| G3 | Phone number: "Our pages and WhatsApp links use 0725 937 686" (plan:73, decision 8). | The layout uses the listing agent's number when the agent has one (v191:2486-2495); the classic rent box uses the default (v191:1897-1906). | PROJECT.md rule 14: the agent's number, else the default. |
| G4 | The phone bar holds "the rent with its period, WhatsApp and Arrange a viewing" (plan:701). | Done for the classic bar (v191:1993-2000). The layout's own mobile bar has WhatsApp and the button but no rent (v191:9211-9212). | The new page's bar should state the rent (T3). |
| G5 | Rental leads from the rent hub and the rent type pages arrive with the topic `for-rent` (plan:741). | Not built: the hubs are release 2. | Out of scope. |
| G6 | An area or segment enquiry "Looking to: Rent" becomes a rental enquiry (plan:740). | Only for the full area form; the v1.6 area forms that are live send no "Looking to" (README:304). | Out of scope. |
| G7 | Hub and section picks need a photo (plan:730). | Done for area-page picks. Similar rentals on a listing's page have no photo rule; a card without a photo shows a placeholder "Images on request" (v191:7806). | Prefer rentals with a featured image; decide in design. |
| G8 | Per-layout rents for the seven "range" rentals are staff work (plan:847; plan:1073): 35945, 39456, 39421, 37551, 34301, 35498, 36499 (plan:729). | No code. The page states the one figure in the price field. | "From" for a title that names several sizes (R19 c). |
| G9 | Unit codes MON, DAY, WEE or ANN (plan:766). | Also "a night" (DAY with `unitText` "per night") and, for a label that names no period, `unitText` as written (v191:8385, 8415-8422). | As v1.9.1 (R113). |
| G10 | "R1–R20 from `build.md`" (plan:625) is the October list of rental changes (its numbers are not this file's rule numbers). | `build.md`, `review.md` and `fixes.md` are not in the repository; the plan cites them from an older scratchpad (plan:96). | Unknown beyond what plan section 7, the README and the code state (section 20, Q6). |
| G11 | Batch R data fixes (plan:799-816): labels, categories, areas, `bke_dev_name`, `bke_rent_usd`, the Furnished feature. | Data, not code. The feed of 11 Nov shows some not done: 12 rent-only listings still have an empty after-price label; 37551 is still Commercial and Offices (plan:806 said Apartments); 36814 and 43656 have no category; 36038, 36135, 36792 and 42370 have no area (plan:805); 42616 still has the Project term "Villa for Rent". | The rules above cope with each case (empty label reads "a month"); the page name does not (section 10). The data audit has the full list. |

One thing the code's own comment claims that the code does not do: v191:7141 lists a section headed "Rent and
terms" as read; the pattern does not match it (section 3).

## 18. Where the approved rules and PROJECT.md disagree, and which should win

| # | The disagreement | Which wins, and why |
|---|---|---|
| C1 | **Rentals by title.** The approved rule (R3) counts a listing with no Buy/Rent term whose title says "for rent", "to let", "to rent" or "for lease". PROJECT.md section 5 limits the page to listings tagged For Rent and not For Sale. Nine published listings are caught between the two today (feed): 45113, 45098, 44956, 44955 (Kileleshwa, Mandera Road), 45054, 45037 (offices, Brookside Close), 45003, 44051, 44049. | **PROJECT.md**: it is a decision already taken, and a term is a firmer test than a title. The cost is that these nine keep the classic page, where the theme's mortgage calculator still shows (snippet 45681 hides it only on listings tagged For Rent and not For Sale; `ROOT/notes/05-live-snippets.md` section 9). The remedy is data, not code: give them the For Rent term. Batch R planned that for 44051 and 44049 (plan:803) and the feed shows it was not done for those two. Report the nine to the main session. |
| C2 | **Listings that offer sale and lease in their text.** By R1 a listing tagged For Rent only is a rental whatever its title says. Nine are known (README:418): 45228 and 36662 (Purple Tower), 37243, 36636, 39341, 34334, 34335, 34336, 34301. Their text still has sale wording ("to Buy or Lease", "for Rent & Sale"). | **PROJECT.md** on scope (they are rent-only, so they are in). But the automatic rule of PROJECT.md rule 6 should not accept a listing whose title offers both; use the pattern of R4 extended with "buy or lease", or set `bke_rent_layout` to `off` on the nine until the owner decides (README:418 leaves that choice to the owner). |
| C3 | **"From".** PROJECT.md rule 12: "From" is kept when the listing has it. The v1.9.1 layout drops it for per sq ft rents and adds it for any unit table and for off-plan status (section 2). | **PROJECT.md**. Use the single rule under the truth table in section 2. It is the classic box's rule plus the cards' "several sizes" rule, both already in v1.9.1. |
| C4 | **Phone and WhatsApp number.** Plan decision 8 and the classic rent box use Block's default number only; the layout uses the listing agent's when the agent has one. | **PROJECT.md rule 14** (the layout's rule), and it says so itself. |
| C5 | **How scripts are printed and read.** v1.9.1 echoes script tags from PHP strings for the page config, the page script and the JSON-LD (v191:10367-10368, 8546), and reads script tags with regular expressions (`blockke_dev_ld_own()` v191:8224; the classic buffer v191:2257). | **PROJECT.md rule 5**: `wp_print_inline_script_tag()` for all three, and no pattern that matches script or style tags. So R117 is re-done with plain string tests, and nothing of the classic buffer is ported. |
| C6 | **Names.** v1.9.1's rental pieces use the development snippet's names: `rent_pages`, `?bke_rent=1`, `#bkd-rent`, `bkd_sent`, `bkd_err`, `bkd_hp`, `data-bkd-*`, `window.BKDEV`, transient `bkd_rl_*`, option `blockke_dev_spam`. | **PROJECT.md rule 1** for everything new. The names below must stay, because other code or stored data depends on them. |
| C7 | **Generic sentences that promise something.** PROJECT.md rule 11 allows no invented services or timelines. Two sentences of the layout do: "We usually reply within a few hours on working days." (v191:9198) and "…an advisor will send you matches as they come up, often before they are listed." (v191:9172). Both are also in the live v1.8.2, so they are on development pages today. | **PROJECT.md**. Drop "often before they are listed". Keep the reply-time sentence only if the main session confirms the owner stands by it for rentals (section 20, Q7). The tenant "Why Block" texts and the disclaimer are approved word for word (plan:1065) and stay. |
| C8 | **Commercial listings.** PROJECT.md rule 13 asks for business occupier wording and no bedrooms. v1.9.1 has one rental wording, a home's: "tenants", "household", "Find me a rental like this"; it prints the bedrooms field in the key facts; its similar-homes pool leaves commercial out; and it decides "commercial" by category alone. | **PROJECT.md**. This is new copy and one new test, not a port. The test needs the title as well as the category: 37551 is three kinds of apartment filed under Commercial and Offices; 36814 is offices with no category; offices 34906 and 36675 carry 1 and 2 in the bedrooms field (feed). Proposed: commercial when a category says Commercial, Offices or Warehouses, or there is no category and the title names offices, a warehouse, a godown, a shop or retail space; unless the title names bedrooms, an apartment, a house, a villa or a studio. |
| C9 | **`noindex`.** PROJECT.md rule 7: no `noindex` on the live layout. v1.9.1 marks the page a no-script post returns to (`?bkd_sent=1`, `?bkd_err=…`) `noindex, follow` and sends no-cache headers (v191:8085-8106). | **Both can hold.** The listing's own URL stays indexable; the return URL carries a parameter and a personal state, and a cached copy of it would show one visitor's thank-you to the next. Recommended: keep `noindex` and no-cache on the return URL only. If the main session reads rule 7 strictly, the no-cache headers still stay. |
| C10 | **Placeholders shown as facts.** v1.9.1 prints "Availability: On request" in the hero when the listing states neither availability nor viewing (v191:8645). PROJECT.md rule 11: anything to be confirmed is not shown as a fact. | **PROJECT.md**. Leave the cell out; the "Check availability" button already says it. |

**Names that must not be renamed** (C6):

| Name | Why it must stay | Where |
|---|---|---|
| option `blockke_dev_leads`, its row keys, `'kind' => 'rent'` | the inbox reads them | inbox:260-289; PROJECT.md section 5 |
| email subject "[Block] Rental enquiry: …" | the owner's Zoho Mail filter keys on it | plan:756; README:304 |
| `data-blkct="1"` on WhatsApp links, and "Ref W-{ID}" | snippet 40769 | `ROOT/ref/live-snippets/40769.printed.html` lines 1, 12-18 |
| listing fields `bke_rent_usd`, `bke_rent_usd_date`, `bke_dev_name` (read only) | editors already fill them | README:181; PROJECT.md rule 12 |
| `script#bke-schema-ld` | the header injection stands down when it exists | `ROOT/ref/pages-stop/45577.html:3021` |
| `sessionStorage` key `blkbp_attr` (read only) | the attribution keeper writes it | v191:9862; `ROOT/notes/05-live-snippets.md` section 2.2 |
| `window.blockkeLeadConversion( 'lead_form' )`, GA4 `generate_lead`, Meta `Lead` | the site's conversion tracking | README:296; v191:9935-9937 |
| `$_GET['blockpop'] = '0'` | the popup's off switch | popup:105-107; v191:10257 |
| `$GLOBALS['blockke_dev_active']` | v1.9.1's classic rental pieces stand down when it is set | v191:1862; the full recipe is in `ROOT/notes/01-dev-layout.md` section (b) |
| `blockke_bp_send_zoho()`, `blockke_bp_config()`, `blockke_bp_channel()`, `blockke_bp_lead_source()`, `blockke_bp_normalize_phone()` | the popup snippet's functions, each called behind `function_exists()` with a fallback | v191:586-588, 10402-10405, 10614-10615, 10832; popup:41, 189, 212, 251, 287 |

## 19. Where the approved code misreads today's listings

These are not approved rules. They are what the runs over the 129 descriptions show, with a proposal for each that
stays inside PROJECT.md rule 11. The main session decides.

| # | What happens | Evidence | Proposal |
|---|---|---|---|
| D1 | **The snapshot table is not found on 23 listings.** The description is split into sections only at its top heading level (`blockke_dev_sections()` v191:808-848). 18 listings open with a title heading and put "Project Snapshot" and "Rental Terms" one level down; 5 write their headings as bold paragraphs. | `run_real.php`: glance section found in 94 listings as v1.9.1 splits, 112 when every heading starts a section. Below the top level: 41690, 42279, 42340, 43453, 43523, 43712, 43819, 44135, 44339, 44747, 44748, 45228, 45231, 45318, 45367, 45507, 45538, 45577. Bold paragraphs: 43995, 44020, 44533, 44745, 44746 (31842 has bold headings and no table). | When looking for the glance and terms sections, let every heading from H2 to H6 start a section, and treat a paragraph that holds only bold text as a heading. |
| D2 | **The service charge label test is loose.** Any label containing "service charge" passes, so "Combined Rent + Service Charge → KES 83 per sq ft per month" (36731), "Estimated Monthly Service Charge → KES 432,000 before VAT" (36823) and "Service Charge at USD 0.20 per sq ft → USD 800 plus VAT" (36816, one size's worked example) would each be printed as the service charge. Today the right row happens to come first on all three. | `run_real2.php` | Reject a label that also says combined, total, rent or "+"; prefer a label that is exactly "Service charge". |
| D3 | **Availability values that are stale or are not facts are printed.** | As v1.9.1 splits: 40490 "From 1st October"; 36135 and 36136 "Expected from Q3 2026"; 35986 "Phase 2 operational H2 2026 — units available from Q3 2026"; 36410 "Subject to apartment availability"; 39421, 41862, 41921 "For rent"; 34906 "To let"; 43482, 43788, 43833, 44048, 44050 "It is available". Once D1 is fixed also 41690 "Contact Block Real Estate for current vacancy status" and 44135 "One available". | Also treat as still open: "subject to", "contact", "please". Do not print a value that only restates the offer ("For rent", "To let"). Do not print a date, month or quarter that has passed, or a date with no year. Print "It is available" as "Available". |
| D4 | **Furnishing is lost or garbled.** "Furnished (full inventory to be confirmed at point of enquiry)" (41862) and "Furnished; full inventory and furniture quality to be confirmed at point of enquiry" (42292) are dropped whole because of the word "confirmed", though both listings carry the *Furnished* feature. "Theres also unfurnished apartments" (43523) and "There is also furnished apartments" (43788) are printed as the furnishing. 44020 (furnished, daily rate) shows none. | `run_real2.php`; feed: the *Furnished* feature is on 41862, 42260, 42292, 43482, 44020; the *Furnished* status on 9 listings; 16 titles say "Furnished". | Decide furnished from the feature or the status first, as the cards do (v191:12834), then from the first words of the row; print only "Furnished", "Fully furnished", "Semi-furnished" or "Unfurnished", never a remark. |
| D5 | **A stated term is dropped for length, and one value is printed twice.** 39301 states "One month's rent upfront, two months' deposit, one-year lease agreement" (71 characters) and gets nothing. 43855 and 43856 state "Lease terms and deposit → 2 months rent and one month deposit"; the layout prints it under Deposit and again under Lease. | `run_real2.php`; v191:7166 (60 characters); v191:9003-9008 (no check for repeats) | Raise the limit to about 90 characters, and say one value once (the classic box's rule, R36). |
| D6 | **A table of charges is read as the homes for rent, and its lowest figure becomes the rent.** The layout takes `price_from` from the lowest priced row of any table it accepts as a unit table. On 36731 the table "Expense Type · Cost Rate · Notes" is accepted, its only figures of KES 1,000 or more are the parking charges, and the page would print "From KES 8,500 per sq ft a month". | v191:7391-7398; `blockke_dev_units_from_table()` v191:2679-2768; `run_units.php`, `run_d6.php`. 19 of the 129 descriptions hold a table it accepts; only 43712's is a real list of homes with rents (215,000, 225,000 and 265,000). | The rent is the price field, always (R10). Do not port the unit-table reader for the headline. If the design lists a listing's homes, take only rows that name a home and a rent. |
| D7 | **The page would say the service charge is still to be confirmed when the listing says it is included.** Ten listings state it in the rent row: "Rent → KES 135,000 per month, service charge included" (45577), 45538, 34171, 34926, 36809, 37258, 39066, 40490, 44745, 44746. Only 36809 also has a Service charge row. For the other nine, R44 would print "…any service charge and what the rent covers are confirmed when you enquire." 37 descriptions say it somewhere in their text. | `run_real4.php` | When the listing's rent row says the service charge is included or inclusive, show "Service charge: Included in the rent" and leave it out of the open sentence. This is PROJECT.md rule 11's "an amount or included". |
| D8 | **A dollar rent is missed.** 44533 states "Rent → USD 6,500 per month" in a table before any heading (it has none), so the reader never sees it, and the page would print "KES 841,332 a month" with no word about dollars. | `run_real.php`; feed price 841332 | Fixed by D1's bold-paragraph headings. Or set `bke_rent_usd` on the four dollar listings (35583, 34171, 36816, 44533). |
| D9 | **Rentals listed as units of a sale listing.** 42449 and 42450 are the only sub-units of the sale page 31509; their classic pages say "Other units in 2 & 3 Bedroom Apartments for Sale". Where a rental names such a listing as its development (`property_subunits_master`), the layout links its breadcrumb and its unit switcher to that sale page and takes that page's `bke_dev_name` as its own name. | README:418; naming v191:7307-7313; crumbs v191:7643-7645; switcher v191:8632-8641 | A rental page never takes its name, breadcrumb or "other homes" from a listing that is not itself a rental (the same principle as plan:724, "a rent card never links to a sale page"). |
| D10 | **Sizes.** The layout converts every size to square metres (`blockke_dev_size()` v191:531-578; the facts at v191:2889-2898), while commercial rents are quoted per sq ft. A page would say "KES 125 per sq ft a month" beside a size in m². | v191:2871-2902 | Show the size in the unit the listing states (sq ft for the per sq ft listings). A design choice, noted here because the rent phrase depends on it. |

## 20. What could not be established, and how to find out

| # | Unknown | How to find out |
|---|---|---|
| Q1 | Whether `bke_rent_usd` and `bke_rent_usd_date` are set on 35583, 34171, 36816 or 44533 (batch R item B2, plan:811). It decides between rows P27, P28 and P30 of the truth table for them. | The main session reads the two custom fields of those four listings through the site connector, or from the local test site's database once it holds live data. |
| Q2 | Whether `bke_dev_name` is set on any rental (the plan's batch R named 42616, 40490 and 42370 for it; plan:809), and which rentals are sub-units of another listing (`property_subunits_master`). The name results in section 10 assume neither. | Same: read `bke_dev_name` and `property_subunits_master` for the 129 IDs. |
| Q3 | Whether the option `blockke_dev_page` holds `rent_why`, `rent_disclaimer`, `zoho_enquiry_field` or `notify_emails`, and whether the new snippet should honour them. README:416 says only `rent_pages` and `classic_inplace` were written on 11 Nov and the other keys were left as they were. | The main session reads the option. Suggested rule: the new option's own key first, then the same key of `blockke_dev_page` when it is set, then the built-in text. |
| Q4 | Where "See all rentals in {area}" should point on the live site: an area rent page (`/property_area/{slug}/homes-for-rent/`, README:406) or the theme search. It depends on whether `card_v16` and `area_pages` are on in the live option. | The site-shell notes, or the option read of Q3. |
| Q5 | Which of the before-price labels are current. The 11 "From" labels were read from the saved pages of 11 Nov (`ROOT/ref/pages-stop`, `ROOT/ref/pages-final`), not from the database; the feed has no before label. | The data audit, or the local test site. |
| Q6 | The October list of rental changes that the plan calls "R1–R20" (`build.md`; not this file's rule numbers), its review and fixes notes, and the test file `rent.php` (236 checks). None is in the repository, its git history or this session's scratchpad. | Ask the main session whether an older scratchpad or a backup still holds `runda/code/`. Sections 1 to 16 are complete as far as the plan, the README and the code go. |
| Q7 | Whether the owner stands by "We usually reply within a few hours on working days." on rental pages (C7). | The main session asks, or drops the sentence. |
| Q8 | Whether the popup's saved Zoho map posts Company as exactly "Individual buyer". If it posts anything else, the swap to "Individual tenant" does nothing (v191:10527). The map is in the option `blockke_buyer_popup`, next to the Zoho tokens, so agents should not read it (plan:754). | The owner looks in the popup's settings, or one test rental enquiry is sent after go-live and checked in Zoho. |
| Q9 | Whether any rental has a brochure PDF (the field `bke_dev_brochure`, or a PDF attached to the listing). It decides whether R60 is needed at all. | The data audit or the local test site: attachments of type PDF whose parent is one of the 129 IDs. |
| Q10 | Nothing open, but note it: if v1.9.1 is installed later with `listing_jsonld` covering rentals, it prints its own `script#bke-schema-ld` on a page the new snippet renders, because its target test does not look at `blockke_dev_active` (v191:8210-8220). The key is not set today (PROJECT.md section 2). | Already worked out in `ROOT/notes/01-dev-layout.md`, section (b) and its section on structured data; follow that recipe. |

## Appendix A. Code to port (verbatim from `ROOT/ref/dev-v1.9.1.php`, with line numbers)

The blocks below are cut from the file by line range by a script (`make_appendix.py`), not retyped. Tabs are as in
the source. Read these notes first.

**Port notes**

1. **Copy, do not call.** The rent functions are missing from the live v1.8.2, and the development snippet may be
   absent altogether (PROJECT.md section 1). Give every copy the `blockke_rp_` prefix (PROJECT.md rule 1).
2. **What is the same in both versions.** Compared function by function (`cmpfn.py`):

   | Function | v1.8.2 lines | v1.9.1 lines | Same? |
   |---|---|---|---|
   | `blockke_dev_text`, `_money`, `_short`, `_num`, `_digits` | 344-350, 391-394, 396-406, 426-429, 488-490 | 436-442, 483-486, 488-498, 518-521, 580-582 | identical |
   | `blockke_dev_phone`, `_phone_display` | 493-509, 511-516 | 585-601, 603-608 | identical |
   | `blockke_dev_content_html`, `_tokens`, `_sections`, `_parsed`, `_find` | 660-674, 677-700, 703-743, 745-751, 753-760 | 765-779, 782-805, 808-848, 850-856, 858-865 | identical |
   | `blockke_dev_list_items`, `_split_kv`, `_table`, `_kv`, `_kv_get`, `_glance` | 762-780, 783-795, 797-826, 829-851, 853-860, 873-877 | 867-885, 888-900, 902-931, 934-956, 958-965, 978-982 | identical |
   | `blockke_dev_agent`, `_first_term`, `_main_area`, `_is_land` | 1711-1741, 593-596, 3127-3162, 606-630 | 2469-2499, 685-688, 3951-3986, 698-722 | identical |
   | `blockke_dev_lead_to`, `_save_lead` | 8635-8655, 8658-8674 | 10396-10416, 10419-10435 | identical |
   | `blockke_dev_seo_name`, `_recase`, `_term_name`, `_same_place` | 3028-3031, 3038-3044, 3054-3095, 2802-2809 | 3852-3855, 3862-3868, 3878-3919, 3626-3633 | identical |
   | `blockke_dev_action`, `_price_flag`, `_listing_name`, `_handle_lead`, `_lead_more`, `_similar_block`, `_sim_price`, `_data`, `_render` | 3248-3256, 3239-3245, 3098-3117, 8744-9065, 8697-8742, 3699-3809, 3680-3692, 6148-6601, 6861-7473 | 4085-4097, 4068-4077, 3922-3941, 10537-10893, 10458-10506, 4618-4734, 4595-4611, 7287-7745, 8553-9229 | **different** |
   | `blockke_dev_rent_mode`, `_rent_on`, `_rent_phrase`, `_rent_info`, `_rent_name` | none | 4107-4115, 4123-4126, 7212-7267, 7139-7186, 7281-7285 | **only in v1.9.1** |

3. **PHP 7.4 to 8.5 without notices (PROJECT.md rule 2).** The blocks run clean on valid text (PHP 8.3, all
   harness runs). On text that is not valid UTF-8, a `preg_replace()` with the `u` flag returns `null`, and three
   places pass that on: `blockke_dev_text()` line 441, `blockke_dev_rent_phrase()` line 7216 and
   `blockke_dev_rent_name()` lines 7282-7283. From PHP 8.1 that raises "Passing null to parameter" deprecations,
   and `blockke_dev_text()` returns an empty string for the whole title (`run_utf8.php`). Cast each result to a
   string so nothing is raised, and in `blockke_dev_text()` fall back to the same pattern without the `u` flag so
   the text is not lost.
4. **PROJECT.md rules 4 and 5.** The blocks were searched: none holds a backslash-u escape, and none holds
   script-tag or style-tag text or a pattern that matches one. Two pieces of v1.9.1 that do are left out on
   purpose and must be re-done, not ported: the JSON-LD printer (v191:8540-8550) and the "listing already has its
   own RealEstateListing" test (v191:8223-8233). The page script's swap table (v191:7086-7129) is left out too,
   because the new page has its own script; its wording is in sections 6 to 9.
5. **Do not port as they stand:** `price_from` (v191:7397-7398; section 19, D6); the layout's "From" logic at
   v191:8568-8585 (section 2); the naming block v191:7302-7358 beyond its first two steps (section 10); the
   section split of A2 when looking for the snapshot and terms (section 19, D1).
6. **`blockke_dev_pm( $id, $key )`** in these blocks is `get_post_meta( $id, $key, true )` behind a per-request
   cache (v191:4042-4045). **`blockke_bkn_on( $key, $preview )`** reads a switch of `blockke_dev_page` or an
   editor's preview parameter (v191:12566-12572). **`blockke_dev_config()['currency']`** is "KES" (v191:312).

### A1. Small helpers

**v191:436-442.** `blockke_dev_text()`: HTML to plain text. Identical in v1.8.2 (v182:344-350). Port note: line 441 passes the result of a `/u` replace straight to `trim()`; on text that is not valid UTF-8 that is `null`, which raises a deprecation from PHP 8.1 and blanks the whole string (seen in `run_utf8.php`). Cast or fall back.

```php
	function blockke_dev_text( $html ) {
		$t = preg_replace( '#<br\s*/?>#i', ' ', (string) $html );
		$t = wp_strip_all_tags( $t );
		$t = html_entity_decode( $t, ENT_QUOTES | ENT_HTML5, 'UTF-8' );
		$t = str_replace( array( "\xC2\xA0", "\xE2\x80\x8B" ), array( ' ', '' ), $t );
		return trim( preg_replace( '/\s+/u', ' ', $t ) );
	}
```

**v191:483-486.** `blockke_dev_money()`: "KES 135,000". `blockke_dev_config()['currency']` is `KES` (v191:312).

```php
	function blockke_dev_money( $n ) {
		$c = blockke_dev_config();
		return $n > 0 ? $c['currency'] . ' ' . number_format( (float) $n ) : 'On request';
	}
```

**v191:488-498.** `blockke_dev_short()`: "KES 135K", "KES 1.2M". For cards only; it turns 6,500 into "KES 7K", so never use it under KES 10,000 (v191:12867 does not).

```php
	function blockke_dev_short( $n ) {
		$c = blockke_dev_config();
		if ( $n <= 0 ) {
			return 'On request';
		}
		if ( $n >= 1000000 ) {
			$v = rtrim( rtrim( number_format( $n / 1000000, 2, '.', '' ), '0' ), '.' );
			return $c['currency'] . ' ' . $v . 'M';
		}
		return $c['currency'] . ' ' . round( $n / 1000 ) . 'K';
	}
```

### A2. Reading the description: tokens, sections, rows

**v191:765-779.** `blockke_dev_content_html()`: the description as the page would print it (blocks, autop, shortcodes).

```php
	function blockke_dev_content_html( $post ) {
		$c = (string) $post->post_content;
		if ( function_exists( 'has_blocks' ) && has_blocks( $c ) ) {
			$c = do_blocks( $c );
		} else {
			$c = wpautop( $c );
		}
		$c = shortcode_unautop( $c );
		$c = do_shortcode( $c );
		$c = wptexturize( $c );
		if ( function_exists( 'wp_filter_content_tags' ) ) {
			$c = wp_filter_content_tags( $c );
		}
		return $c;
	}
```

**v191:781-805.** `blockke_dev_tokens()`: the block-level elements that are read. A table inside a `figure` counts as a table.

```php
	/** Flat list of the block-level elements we read: headings, paragraphs, lists, tables, details. */
	function blockke_dev_tokens( $html ) {
		$out = array();
		if ( preg_match_all( '#<(h[1-6]|p|ul|ol|figure|table|details|blockquote)\b[^>]*>(.*?)</\1>#is', $html, $m, PREG_SET_ORDER | PREG_OFFSET_CAPTURE ) ) {
			foreach ( $m as $x ) {
				$tag = strtolower( $x[1][0] );
				$t   = array( 'tag' => $tag, 'html' => $x[0][0], 'inner' => $x[2][0], 'pos' => $x[0][1] );
				if ( 'figure' === $tag ) {
					if ( false === stripos( $x[2][0], '<table' ) ) {
						continue;
					}
					$t['tag'] = 'table';
				}
				if ( 'h' === $tag[0] ) {
					$t['tag']   = 'h';
					$t['level'] = (int) $tag[1];
				}
				if ( 'ol' === $tag ) {
					$t['tag'] = 'ul';
				}
				$out[] = $t;
			}
		}
		return $out;
	}
```

**v191:807-848.** `blockke_dev_sections()`: splits at the top heading level only. This is the cause of D1 (section 19); change it for the rental reader.

```php
	/** Splits the description into an intro and sections at its top heading level (H2, or H3 when there are no H2s). */
	function blockke_dev_sections( $html ) {
		$tokens = blockke_dev_tokens( $html );
		$level  = 7;
		foreach ( $tokens as $t ) {
			if ( 'h' === $t['tag'] && $t['level'] >= 2 && $t['level'] < $level ) {
				$level = $t['level'];
			}
		}
		$intro    = array();
		$sections = array();
		$cur      = null;
		foreach ( $tokens as $t ) {
			if ( 'h' === $t['tag'] && $t['level'] <= $level ) {
				if ( $cur ) {
					$sections[] = $cur;
				}
				$cur = array( 'title' => blockke_dev_text( $t['inner'] ), 'level' => $t['level'], 'tokens' => array(), 'start' => $t['pos'] );
				continue;
			}
			if ( $cur ) {
				$cur['tokens'][] = $t;
			} else {
				$intro[] = $t;
			}
		}
		if ( $cur ) {
			$sections[] = $cur;
		}
		$len = strlen( $html );
		foreach ( $sections as $i => $s ) {
			$end                      = isset( $sections[ $i + 1 ] ) ? $sections[ $i + 1 ]['start'] : $len;
			$sections[ $i ]['html'] = substr( $html, $s['start'], $end - $s['start'] );
		}
		return array(
			'level'    => $level,
			'intro'    => $intro,
			'sections' => $sections,
			'tokens'   => $tokens,
			'html'     => $html,
		);
	}
```

**v191:850-865.** `blockke_dev_parsed()` (per-request cache) and `blockke_dev_find()` (first section whose title matches).

```php
	function blockke_dev_parsed( $post ) {
		static $cache = array();
		if ( ! isset( $cache[ $post->ID ] ) ) {
			$cache[ $post->ID ] = blockke_dev_sections( blockke_dev_content_html( $post ) );
		}
		return $cache[ $post->ID ];
	}

	function blockke_dev_find( $sections, $re, $not = '' ) {
		foreach ( $sections as $i => $s ) {
			if ( preg_match( $re, $s['title'] ) && ( '' === $not || ! preg_match( $not, $s['title'] ) ) ) {
				return $i;
			}
		}
		return -1;
	}
```

**v191:867-900.** `blockke_dev_list_items()` and `blockke_dev_split_kv()`: a list item as "Label: value".

```php
	function blockke_dev_list_items( $inner ) {
		$items = array();
		if ( preg_match_all( '#<li\b[^>]*>(.*?)</li>#is', $inner, $m ) ) {
			foreach ( $m[1] as $li ) {
				$li    = preg_replace( '#<(ul|ol)\b.*$#is', '', $li );
				$label = '';
				$rest  = '';
				if ( preg_match( '#^\s*<(strong|b)\b[^>]*>(.*?)</\1>\s*(.*)$#is', $li, $k ) ) {
					$label = blockke_dev_text( $k[2] );
					$rest  = $k[3];
				}
				$text = blockke_dev_text( $li );
				if ( '' !== $text ) {
					$items[] = array( 'html' => trim( $li ), 'text' => $text, 'label' => $label, 'rest' => $rest );
				}
			}
		}
		return $items;
	}

	/** "Label: value" from a list item, bold label or plain colon. */
	function blockke_dev_split_kv( $it ) {
		if ( '' !== $it['label'] ) {
			$k = trim( rtrim( trim( $it['label'] ), ':' ) );
			$v = trim( preg_replace( '/^\s*[:–—-]\s*/u', '', blockke_dev_text( $it['rest'] ) ) );
			if ( '' !== $v && mb_strlen( $k ) <= 60 ) {
				return array( $k, $v );
			}
		}
		if ( preg_match( '/^([^:]{2,48}):\s*(.+)$/u', $it['text'], $m ) ) {
			return array( trim( $m[1] ), trim( $m[2] ) );
		}
		return array( '', $it['text'] );
	}
```

**v191:902-931.** `blockke_dev_table()`: header row and rows of a table.

```php
	function blockke_dev_table( $html ) {
		if ( ! preg_match_all( '#<tr\b[^>]*>(.*?)</tr>#is', $html, $trs ) ) {
			return null;
		}
		$has_thead = (bool) preg_match( '#<thead\b#i', $html );
		$head      = array();
		$rows      = array();
		foreach ( $trs[1] as $i => $tr ) {
			preg_match_all( '#<t([hd])\b[^>]*>(.*?)</t\1>#is', $tr, $cells, PREG_SET_ORDER );
			$row    = array();
			$links  = array();
			$all_th = true;
			foreach ( $cells as $c ) {
				$row[]   = blockke_dev_text( $c[2] );
				$links[] = preg_match( '#<a\b[^>]*href=["\']([^"\']+)["\']#i', $c[2], $a ) ? html_entity_decode( $a[1], ENT_QUOTES, 'UTF-8' ) : '';
				if ( 'h' !== strtolower( $c[1] ) ) {
					$all_th = false;
				}
			}
			if ( ! $row ) {
				continue;
			}
			if ( ! $head && ! $rows && ( $all_th || ( $has_thead && 0 === $i ) ) ) {
				$head = $row;
				continue;
			}
			$rows[] = array( 'cells' => $row, 'links' => $links );
		}
		return array( 'head' => $head, 'rows' => $rows );
	}
```

**v191:933-965.** `blockke_dev_kv()` and `blockke_dev_kv_get()`: label and value pairs of a section.

```php
	/** Label / value pairs from tables and "Label: value" lists in a section. */
	function blockke_dev_kv( $tokens ) {
		$kv = array();
		foreach ( $tokens as $t ) {
			if ( 'table' === $t['tag'] ) {
				$tb = blockke_dev_table( $t['html'] );
				if ( $tb ) {
					foreach ( $tb['rows'] as $r ) {
						if ( count( $r['cells'] ) >= 2 && '' !== $r['cells'][0] ) {
							$kv[] = array( rtrim( $r['cells'][0], ': ' ), $r['cells'][1] );
						}
					}
				}
			} elseif ( 'ul' === $t['tag'] ) {
				foreach ( blockke_dev_list_items( $t['inner'] ) as $it ) {
					$p = blockke_dev_split_kv( $it );
					if ( '' !== $p[0] ) {
						$kv[] = $p;
					}
				}
			}
		}
		return $kv;
	}

	function blockke_dev_kv_get( $kv, $re ) {
		foreach ( $kv as $p ) {
			if ( preg_match( $re, $p[0] ) ) {
				return $p[1];
			}
		}
		return '';
	}
```

**v191:977-982.** `blockke_dev_glance()`: the glance table's rows.

```php
	/** Glance / snapshot facts of the listing (cached per request). */
	function blockke_dev_glance( $post ) {
		$parsed = blockke_dev_parsed( $post );
		$i      = blockke_dev_find( $parsed['sections'], '/glance|snapshot|key facts|quick facts|in brief|at a look|summary/i' );
		return $i >= 0 ? blockke_dev_kv( $parsed['sections'][ $i ]['tokens'] ) : array();
	}
```

### A3. The price flag (per sq ft, up to)

**v191:4062-4077.** `blockke_dev_price_flag()`. `blockke_dev_pm( $id, $key )` is `get_post_meta( $id, $key, true )` with a cache (v191:4042-4045). The `$period` branch belongs to the cards switch; the page does not need it.

```php
	/**
	 * 'upto' when the price is a ceiling ("Up to"), 'unit' when it is per square foot or metre (offices); else ''. v1.8.3 (R1):
	 * $period (a rental's row, blockke_bkn_row()) while 'rent_cards' is on (or previewed: ?bke_rcards=1): 'period' when the
	 * price is a day's, a night's or a week's ("per day"), so cards, area and search pages keep it out of price ranges,
	 * budgets and price sorts as they do 'unit' (it is still counted and shown). Every other caller reads the flag as before.
	 */
	function blockke_dev_price_flag( $id, $period = false ) {
		$l = strtolower( (string) blockke_dev_pm( $id, 'property_label' ) . ' ' . (string) blockke_dev_pm( $id, 'property_label_before' ) );
		if ( preg_match( '/sq\.?\s*f|sqft|sq\.?\s*m\b|sqm|m²|per\s+(?:sq|square)/u', $l ) ) {
			return 'unit';
		}
		if ( $period && preg_match( '/\b(day|night|week)\b|daily|nightly|weekly/i', $l ) && blockke_bkn_on( 'rent_cards', 'bke_rcards' ) ) {
			return 'period';
		}
		return false !== strpos( $l, 'up to' ) ? 'upto' : '';
	}
```

### A4. The rental test

**v191:4079-4115.** `blockke_dev_action()` and `blockke_dev_rent_mode()`. `blockke_bkn_on( 'rent_pages', 'bke_rent' )` is the switch (v191:12566-12572). For the new page only the first test of `blockke_dev_action()` applies (R1, R2); the rest is here for the record (R3, R4).

```php
	/**
	 * 'rent' or 'sale': the Buy/Rent term, else the title ("for rent", "to let"). v1.8.3 (R1): while 'rent_pages' is on (or
	 * previewed: ?bke_rent=1), the title test also reads "to rent" and "for lease", never "Rent-to-Own"; the cache versions
	 * carry blockke_dev_rent_ver(), so the rows, similar homes and area pages built from it agree. $wide: that title test
	 * whatever the switch, for one listing only: the lead of an editor's ?bke_rent=1 preview (blockke_dev_rent_rp()).
	 */
	function blockke_dev_action( $id, $wide = false ) {
		if ( has_term( 'for-rent', 'property_action_category', $id ) && ! has_term( 'for-sale', 'property_action_category', $id ) ) {
			return 'rent';
		}
		if ( has_term( 'for-sale', 'property_action_category', $id ) ) {
			return 'sale';
		}
		if ( $wide || blockke_bkn_on( 'rent_pages', 'bke_rent' ) ) {
			$t = (string) get_the_title( $id );
			return preg_match( '/\bfor rent\b|\bto let\b|\bto rent\b|\bfor lease\b/i', $t ) && ! preg_match( '/rent[- ]to[- ]own/i', $t ) ? 'rent' : 'sale';
		}
		return preg_match( '/\bfor rent\b|\bto let\b/i', (string) get_the_title( $id ) ) ? 'rent' : 'sale';
	}

	/**
	 * v1.8.3 fix: whether a home's own page and its leads read as a rental. blockke_dev_action() 'rent', except a listing
	 * with no Buy/Rent term whose title offers both ("Office Space for Rent & Sale"), which keeps the sale page, as a
	 * listing tagged both does. blockke_dev_action() keeps the v1.8.2 title test while 'rent_pages' is off; with it on (or
	 * $wide) a title with no Buy/Rent term also counts when it says "to rent" or "for lease" (search links, similar homes and
	 * area pages follow it).
	 * $wide: blockke_dev_action()'s widened title test, for an editor's preview lead (blockke_dev_rent_rp()).
	 */
	function blockke_dev_rent_mode( $id, $wide = false ) {
		if ( 'rent' !== blockke_dev_action( $id, $wide ) ) {
			return false;
		}
		if ( has_term( 'for-rent', 'property_action_category', $id ) ) {
			return true;
		}
		return ! preg_match( '/\b(?:rent|let)\s*(?:&|and|or|\/)\s*(?:for\s+)?sale\b|\bsale\s*(?:&|and|or|\/)\s*(?:for\s+|to\s+)?(?:rent|let)\b/i', blockke_dev_text( get_the_title( $id ) ) );
	}
```

### A5. Reading the rental terms

**v191:7131-7186.** `blockke_dev_rent_info( $kv, $secs )`: `$kv` = `blockke_dev_glance( $post )`, `$secs` = `blockke_dev_parsed( $post )['sections']` (as called at v191:7736). Returns keyed rows `array( label, value )` for `deposit`, `lease`, `service`, `furnish`, `available`, `viewing`, and `usd` => `array( 'Rent', number )`.

```php
	/**
	 * v1.8.3: what a home for rent states about renting it, for its Renting section and hero: the deposit, the lease, a
	 * service charge, furnishing, availability and viewing, each from the listing's own rows ("Furnishing status |
	 * Unfurnished", "Availability | Available", "Viewing | By appointment"), its glance or snapshot table first, then every
	 * rental, lease or tenancy terms section, or a rental's "Payment Plan" ("Deposit: two months' rent"). A value still to be confirmed ("confirmed at point of
	 * enquiry", "to be confirmed", "on request", "varies") is not stated, nor a deposit or lease without a figure, nor a
	 * service charge without an amount or "included". The rent itself is the listing's price. Keyed rows: label, value.
	 */
	function blockke_dev_rent_info( $kv, $secs ) {
		$rows = $kv;
		foreach ( $secs as $sec ) { // "Rental Terms", "Lease Terms", "Tenancy terms", "Rent and terms", a rental's "Payment Plan" or "Pricing"
			if ( preg_match( '/\b(?:rental|rent|lease|leasing|tenancy|letting)\s+(?:and\s+\w+\s+)?terms?\b|\bterms of (?:the )?(?:lease|tenancy|rent)\b|^rent(?:al)?(?:\s+details)?$|payment plan|^pricing\b/i', $sec['title'] ) && ! preg_match( '/faq|question/i', $sec['title'] ) ) {
				$rows = array_merge( $rows, blockke_dev_kv( $sec['tokens'] ) );
			}
		}
		$open = '/\bconfirm|\bto be (?:advised|agreed|announced|shared|discussed|determined|provided)\b|\btb[acd]\b|\bon request\b|\bupon request\b|\bpoint of enquiry\b|\benquir|\bvar(?:y|ies|iable)\b|\bnegotiab|\bif applicable\b|\bdepend|\bask\b|\bn\/a\b|^-+$/i';
		$fig  = '/\d|\b(?:one|two|three|four|five|six|seven|eight|nine|ten|eleven|twelve|eighteen|twenty[- ]four|thirty[- ]six)\b|\ba (?:month|year)\b/i'; // fix: "Five months", "Nine months"
		$want = array(
			'deposit'   => array( 'Deposit', '/deposit/i', $fig ),
			'lease'     => array( 'Lease', '/^lease$|lease (?:term|length|period|duration)|minimum (?:lease|term|stay|let|tenancy)|tenancy (?:term|length|period)/i', $fig, '/leasehold|freehold|\b99[- ]?years?\b|\b999\b/i' ), // fix: a title's "99-year leasehold" is not a tenancy
			'service'   => array( 'Service charge', '/service charge|estate (?:fees?|charges?|levy)/i', '/\d|\binclu/i' ),
			'furnish'   => array( 'Furnishing', '/furnish/i', '/furnish/i' ),
			'available' => array( 'Availability', '/^(?:availab(?:le|ility)(?:\s+(?:from|date|status))?|move[- ]in(?:\s+date)?|vacant(?:\s+from)?|occupation(?:\s+date)?|possession(?:\s+date)?)$/i', '' ), // fix: not "Availability of parking", "Available units" or "Move-in ready"
			'viewing'   => array( 'Viewing', '/^viewings?$|^viewing (?:arrangements?|times?)$/i', '' ),
		);
		$out  = array();
		$all  = array( 'furnish' => array() ); // fix: furnishing from any table or "Label: value" list (43482's "Unit Types" table)
		foreach ( $secs as $sec ) {
			if ( ! preg_match( '/faq|question/i', $sec['title'] ) ) {
				$all['furnish'] = array_merge( $all['furnish'], blockke_dev_kv( $sec['tokens'] ) );
			}
		}
		foreach ( $want as $k => $w ) {
			foreach ( isset( $all[ $k ] ) ? array_merge( $rows, $all[ $k ] ) : $rows as $p ) {
				$v = trim( (string) $p[1], " \t.;" );
				if ( ! isset( $out[ $k ] ) && preg_match( $w[1], $p[0] ) && '' !== $v && mb_strlen( $v ) <= 60 && ! preg_match( $open, $v ) && ( '' === $w[2] || preg_match( $w[2], $v ) ) && ( empty( $w[3] ) || ! preg_match( $w[3], $v ) ) ) {
					$out[ $k ] = array( $w[0], ucfirst( $v ) );
				}
			}
		}
		// fix: a rent the listing states in US dollars first ("Rent | USD 5,500 a month", "Monthly rent | US$ 5,500"): the
		// first rent row decides; a KES rent with a USD figure after it ("KES 650k / $5,000") stays a KES rent
		foreach ( $rows as $p ) {
			if ( preg_match( '/^(?:monthly\s+|asking\s+)?rent(?:al)?(?:\s+(?:a|per)\s+month|\s*\/\s*month|\s*\(\s*monthly\s*\))?$|^monthly rent(?:al)?$/i', trim( $p[0] ) ) ) {
				$v = trim( (string) $p[1] );
				if ( ! preg_match( $open, $v ) && preg_match( '/^(?:usd|us\$|\$)\s*(\d[\d,]*(?:\.\d+)?)\s*(k\b)?/i', $v, $m ) ) {
					$usd = (float) str_replace( ',', '', $m[1] ) * ( empty( $m[2] ) ? 1 : 1000 );
					if ( $usd > 0 ) {
						$out['usd'] = array( 'Rent', $usd );
					}
				}
				break;
			}
		}
		return $out;
	}
```

### A6. The rent phrase

**v191:7188-7267.** `blockke_dev_rent_phrase( $id, $price, $flag, $usd = null )`. Port notes: line 7216 has the same `trim( preg_replace( … /u … ) )` risk as `blockke_dev_text()`; pass the price field as `$price` (R10), the flag from A3, and the `usd` figure from A5.

```php
	/**
	 * v1.8.3 (R1): the one phrase for a rental's rent, wherever it is printed (the layout and its leads; the classic box, the
	 * cards and the structured data use it too). From the price ($price: the page's price, per sq ft when $flag is 'unit'),
	 * the listing's after-price label and its rent in US dollars: the meta bke_rent_usd (a number) first, else $usd, the USD
	 * rent the listing's own rows state (blockke_dev_rent_info()). Returns:
	 *   'amt'        the rent as it leads: "KES 200,000", "KES 130 per sq ft", "USD 5,500"; '' when on request;
	 *   'short'      a short figure for tight places such as cards: "KES 200K", "KES 130 per sq ft", "USD 5,500" (a rental's
	 *                own page states 'amt', exact, in its hero cell, WhatsApp text and Renting section);
	 *   'per'        "a month", "a week", "a day", "a night" or "a year" from the label ("per day" reads "a day"); "a month"
	 *                with no label or a label that only says per sq ft; any other label as written, lower case;
	 *   'kes'        a USD rent's KES figure when it is shown KES first ("KES 710,000"), else '';
	 *   'usd'        a USD rent ("USD 5,500", "USD 1.30 per sq ft"), else '';
	 *   'date'       the date of that KES figure ("20 October 2026"), and 'rate' its KES to the dollar ("129.2"), with 'kes' only;
	 *   'on_request' true with no price, a label "on application", "on request" or "POA", or over KES 2M with no label;
	 *   'line'       the whole rent: "KES 200,000 a month", "KES 130 per sq ft a month", "KES 710,000 a month (USD 5,500)",
	 *                "USD 5,500 a month"; '' when on request;
	 *   'note'       a USD rent's sentence: "The rent is set in US dollars." or, KES first, "The rent is set in US dollars; KES
	 *                figures are approximate, at about KES 129.2 to the US dollar on 20 October 2026."; else ''.
	 *   'num', 'cur' the rent as a number and the currency the lease is set in, as the page states it (the structured data):
	 *                200000 and 'KES'; 130 and 'KES' per sq ft; 5500 and 'USD' for a rent set in US dollars, KES first or not;
	 *                0 and '' when on request.
	 * KES first only when bke_rent_usd and bke_rent_usd_date (YYYY-MM-DD) are both set and the price agrees at KES 80-200 to
	 * the dollar; otherwise a USD rent is stated in US dollars alone, with no KES figure.
	 */
	function blockke_dev_rent_phrase( $id, $price, $flag, $usd = null ) {
		$price = (float) $price;
		$unit  = 'unit' === $flag ? ' per sq ft' : '';
		$cur   = blockke_dev_config()['currency'];
		$l     = trim( preg_replace( '/\s+/u', ' ', blockke_dev_text( (string) blockke_dev_pm( $id, 'property_label' ) ) ) );
		$out   = array( 'amt' => '', 'short' => '', 'per' => 'a month', 'kes' => '', 'usd' => '', 'date' => '', 'rate' => '', 'on_request' => false, 'line' => '', 'note' => '', 'num' => 0, 'cur' => '' );
		$ask   = (bool) preg_match( '/\bon\s+(?:application|request)\b|\bp\.?o\.?a\b/i', $l ); // "Rent on application", "Price on request", "POA"
		$named = '';
		foreach ( array( 'a month' => '/month|\bp\.?\s?m\b/i', 'a week' => '/\bweek(?:s|ly)?\b/i', 'a day' => '/\b(?:days?|daily)\b/i', 'a night' => '/\bnight(?:s|ly)?\b/i', 'a year' => '/\b(?:years?|yearly|annum|annual(?:ly)?)\b|\bp\.\s?a\b/i' ) as $w => $re ) {
			if ( preg_match( $re, $l ) ) {
				$named = $w;
				break;
			}
		}
		if ( '' !== $named ) {
			$out['per'] = $named;
		} elseif ( '' !== $l && '' === $unit && ! $ask ) {
			$out['per'] = mb_strtolower( $l ); // a label that names no period: as written
		}
		$m  = array();
		$mu = get_post_meta( (int) $id, 'bke_rent_usd', true );
		$mu = is_scalar( $mu ) && preg_match( '/^\s*(?:usd|us\$|\$)?\s*(\d[\d,]*(?:\.\d+)?)\s*$/i', (string) $mu, $m ) ? (float) str_replace( ',', '', $m[1] ) : 0.0;
		$u  = $mu > 0 ? $mu : ( is_numeric( $usd ) ? (float) $usd : 0.0 );
		if ( $ask || ( $u <= 0 && ( $price <= 0 || ( '' === $l && $price > 2000000 ) ) ) ) {
			$out['on_request'] = true;
			return $out;
		}
		if ( $u > 0 ) {
			$out['usd'] = 'USD ' . number_format( $u, floor( $u ) === $u ? 0 : 2 ) . $unit;
			$out['num'] = floor( $u ) === $u ? $u : round( $u, 2 );
			$out['cur'] = 'USD';
			$dt         = $mu > 0 ? get_post_meta( (int) $id, 'bke_rent_usd_date', true ) : '';
			if ( is_scalar( $dt ) && preg_match( '/^\s*(\d{4})-(\d{1,2})-(\d{1,2})\s*$/', (string) $dt, $m ) && checkdate( (int) $m[2], (int) $m[3], (int) $m[1] ) && $price > 0 && $price / $u >= 80 && $price / $u <= 200 ) {
				$kes          = '' === $unit && $price >= 10000 ? round( $price, -3 ) : $price;
				$out['kes']   = blockke_dev_money( $kes );
				$out['amt']   = $out['kes'] . $unit;
				$out['short'] = '' === $unit ? blockke_dev_short( $kes ) : $out['amt'];
				$out['date']  = gmdate( 'j F Y', gmmktime( 0, 0, 0, (int) $m[2], (int) $m[3], (int) $m[1] ) );
				$out['rate']  = rtrim( rtrim( number_format( $price / $u, 1, '.', '' ), '0' ), '.' );
				$out['line']  = $out['amt'] . ' ' . $out['per'] . ' (' . $out['usd'] . ')';
				$out['note']  = 'The rent is set in US dollars; ' . $cur . ' figures are approximate, at about ' . $cur . ' ' . $out['rate'] . ' to the US dollar on ' . $out['date'] . '.';
				return $out;
			}
			$out['amt']   = $out['usd'];
			$out['short'] = $out['usd'];
			$out['line']  = $out['amt'] . ' ' . $out['per'];
			$out['note']  = 'The rent is set in US dollars.';
			return $out;
		}
		$out['amt']   = blockke_dev_money( $price ) . $unit;
		$out['num']   = round( $price );
		$out['cur']   = $cur;
		$out['short'] = '' === $unit ? blockke_dev_short( $price ) : $out['amt'];
		$out['line']  = $out['amt'] . ' ' . $out['per'];
		return $out;
	}
```

**v191:7269-7285.** `blockke_dev_rent_amount()` (how the layout calls the phrase: note `price_from`, which the new page replaces with the price field) and `blockke_dev_rent_name()` (R77). Port note: on text that is not valid UTF-8 line 7282 returns `null` and line 7283 then raises a deprecation; cast to string first.

```php
	/** v1.8.3 (R1): the rent of a rental's page (blockke_dev_data()) as blockke_dev_rent_phrase() states it, with the USD rent its rows state. */
	function blockke_dev_rent_amount( $d ) {
		$R = isset( $d['rent']['stated'] ) ? (array) $d['rent']['stated'] : array();
		return blockke_dev_rent_phrase( $d['id'], $d['price_from'], $d['ctx']['flag'], isset( $R['usd'] ) ? $R['usd'][1] : null );
	}

	/**
	 * v1.8.3 (R1, review item 8): a rental's page name for the sentences that already say it is to rent (the WhatsApp text,
	 * "See it in person", the lead's "Rental:" line, the script's thank-you): without "to Let", "for Rent", "to Rent" or "for
	 * Lease" ("Furnished 2 Bedroom Apartment to Let in Brookside" reads "Furnished 2 Bedroom Apartment in Brookside"), never
	 * "Rent-to-Own"; the name as it is when nothing else would be left.
	 */
	function blockke_dev_rent_name( $short ) {
		$n = preg_replace( '/(?:^|\s+)(?:to\s+let|for\s+rent|to\s+rent|for\s+lease)\b(?![- ]to[- ]own)/iu', '', (string) $short );
		$n = preg_replace( '/^[\s,;:|–—-]+|[\s,;:|–—-]+$/u', '', preg_replace( '/\s+/u', ' ', $n ) );
		return '' !== $n ? $n : (string) $short;
	}
```

### A7. "From" on the classic rent box (the rule to follow, R19)

**v191:1880-1894.** `blockke_dev_classic_rent_say()`. `$d['from_label']` is `preg_match( '/^\s*from\b/i', property_label_before )` (v191:7698); `$d['units']` are the listing's home rows.

```php
	/**
	 * v1.8.3 (R1): the rent as the classic box and the phone bar state it: blockke_dev_rent_amount() (the lead line's phrase),
	 * plus 'pre', "From " when the listing's own label says "from" or its homes are let at different rents.
	 */
	function blockke_dev_classic_rent_say( $d ) {
		$ra = blockke_dev_rent_amount( $d );
		$up = array();
		foreach ( (array) $d['units'] as $u ) {
			if ( ! empty( $u['price'] ) ) {
				$up[ (string) (float) $u['price'] ] = true;
			}
		}
		$ra['pre'] = '' !== $ra['amt'] && ( ! empty( $d['from_label'] ) || count( $up ) > 1 ) ? 'From ' : '';
		return $ra;
	}
```

### A8. How the layout builds the hero cell and the WhatsApp text (excerpts of `blockke_dev_render()`)

**v191:8568-8602.** The label and prefix logic. For a rental the lines that matter are 8587 to 8601; lines 8568 to 8585 are the sale logic it starts from, and are the source of the "From" differences in section 2.

```php
		$p_lab = 'From';
		$p_txt = $d['price_from'] ? blockke_dev_short( $d['price_from'] ) : '';
		// v1.8: one fixed price reads "Price" off-plan too (the Runda townhouse, a unit at KES 53.16M, 1899's KES 116.4M): a villa
		// or house, not a development page, with no table or a one-row table at the listing's price, one bedroom count in its
		// title, and no "from" or range on that price in its text (blockke_dev_from_said()). An apartment, studio or office keeps
		// "From": its price usually starts a range of floors and views
		$tbeds = isset( $d['title_beds'] ) ? $d['title_beds'] : array( 'beds' => array(), 'studio' => false );
		$one   = ! $land && ! empty( $cx0['house'] ) && 'master' !== $cx0['kind'] && count( $d['units'] ) <= 1 && empty( $d['from_said'] ) && count( $tbeds['beds'] ) + (int) $tbeds['studio'] <= 1 && ! preg_match( '/\b\d+\s*(?:&|and|to|or|–|-|,)\s*\d+[\s-]*bed/iu', $d['title'] );
		if ( $d['price_from'] && ( ! $d['units'] || $one ) && ( (float) $d['price_from'] === (float) $cx0['price'] || ( $one && abs( $d['price_from'] - $cx0['price'] ) <= 0.01 * $cx0['price'] ) ) ) { // a row's "KES 116.4M" is the listing's 116,402,520
			if ( 'upto' === $cx0['flag'] ) {
				$p_lab = 'Up to';
			} elseif ( 'unit' === $cx0['flag'] ) {
				$p_lab = 'Price';
				$p_txt = blockke_dev_money( $d['price_from'] ) . ' per sq ft';
			} elseif ( ( ! $d['offplan'] || $one ) && ! $d['from_label'] ) { // the listing's own "From" stays (a starting price)
				$p_lab = 'Price';
			}
		}
		$wa    = 'https://wa.me/' . $ag['wa'] . '?text=' . rawurlencode( 'Hi ' . $cfg['brand'] . ", I'm interested in " . $d['short'] . ( $p_txt ? ' (' . ( 'Price' === $p_lab ? '' : strtolower( $p_lab ) . ' ' ) . $p_txt . ')' : '' ) . ( $land ? '. Please share the price list, the site plan and the payment plan. ' : ( $d['plans'] ? '. Please share the price list and floor plans. ' : '. Please share the price list. ' ) ) . $d['url'] );
		$r_pre = 'Price' === $p_lab ? '' : $p_lab . ' '; // v1.8.3 (rent): "From", "Up to" before the monthly rent
		$r_amt = $d['price_from'] ? blockke_dev_money( $d['price_from'] ) . ( 'unit' === $cx0['flag'] && 'Price' === $p_lab && false !== strpos( $p_txt, 'per sq ft' ) ? ' per sq ft' : '' ) : '';
		$RA    = $rent ? blockke_dev_rent_amount( $d ) : array(); // v1.8.3 fix: the rent's own unit ("a month", "a night") and currency (a USD rent); R1: blockke_dev_rent_phrase()
		$wa_ct = ''; // R1: a rental's WhatsApp links are marked done for snippet 40769 (data-blkct), which would rewrite their text
		if ( $rent ) { // v1.8.3: "Rent / month  KES 200,000" (R1: the exact rent, not the cards' short "KES 200K"); the WhatsApp asks to view
			if ( ! $d['price_from'] && '' !== $RA['amt'] ) { // a USD rent with no price field: one rent, not a "From"
				$p_lab = 'Price';
				$r_pre = '';
			}
			$p_txt = $RA['amt']; // R1: the exact rent in the hero cell and the WhatsApp text ("KES 6,500", "KES 710,000", "KES 130 per sq ft", "USD 5,500"), never "KES 7K"; '' on request
			$r_amt = $RA['amt'];
			// R1: "viewing" and its own "Ref W-{id}", so snippet 40769 leaves the text alone (plan-check H2); the name without "to Let"
			$wa    = 'https://wa.me/' . $ag['wa'] . '?text=' . rawurlencode( 'Hi ' . $cfg['brand'] . ", I'm interested in renting " . blockke_dev_rent_name( $d['short'] ) . ( $p_txt ? ' (' . strtolower( $r_pre ) . $p_txt . ' ' . $RA['per'] . ')' : '' ) . '. Is it still available, and when could I arrange a viewing? Ref W-' . (int) $d['id'] . '. ' . $d['url'] );
			$wa_ct = ' data-blkct="1"';
			$p_lab = ( 'Price' === $p_lab ? 'Rent' : 'Rent ' . strtolower( $p_lab ) ) . ( preg_match( '/^(?:a|an|per)\s+(\w+)$/u', $RA['per'], $pm ) ? ' / ' . $pm[1] : '' );
		}
```

**v191:8642-8648.** The hero facts of a rental (R38).

```php
		$hfacts   = array();
		$hfacts[] = $p_txt ? array( $p_lab, $p_txt ) : array( $rent ? 'Rent' : 'Price', 'On request' );
		if ( $rent ) { // v1.8.3: the availability the listing states, else its viewing arrangement; never "Status Ready", "Completion" or a deposit
			$hfacts[] = isset( $R['available'] ) ? $R['available'] : ( isset( $R['viewing'] ) ? $R['viewing'] : array( 'Availability', 'On request' ) );
			if ( $d['area'] ) {
				$hfacts[] = array( 'Location', $d['area'] );
			}
```

### A9. The Renting section (excerpt of `blockke_dev_render()`)

**v191:9000-9030.** `$R` is the reader's result (A5), `$RA` the phrase (A6), `$r_pre` and `$r_amt` come from A8, `$e` is `esc_html`.

```php
		} elseif ( $rent ) {
			// v1.8.3: a home for rent: the monthly rent, what its listing states about renting it (blockke_dev_rent_info()) and
			// a way to view it; never a payment plan, a deposit estimate or a mortgage. What the listing leaves open is named
			$rows = array();
			foreach ( array( 'deposit', 'lease', 'service', 'furnish', 'available', 'viewing' ) as $k ) {
				if ( isset( $R[ $k ] ) ) {
					$rows[] = $R[ $k ];
				}
			}
			$left = array();
			foreach ( array( 'deposit' => 'the deposit', 'lease' => 'the lease length', 'service' => 'any service charge' ) as $k => $w ) {
				if ( ! isset( $R[ $k ] ) ) {
					$left[] = $w;
				}
			}
			$left[] = 'what the rent covers';
			$last   = array_pop( $left );
			$open   = ucfirst( ( $left ? implode( ', ', $left ) . ' and ' : '' ) . $last ) . ( $left ? ' are' : ' is' ) . ' confirmed when you enquire.';
			$cur    = '' !== $RA['note'] ? $RA['note'] . ' ' : ''; // v1.8.3 fix: a USD rent says so; R1: KES first only with a dated rate (bke_rent_usd_date)
			$o[]    = '<section class="bkd-sec bkd-sec-mist" id="bkd-renting"><div class="bkd-wrap bkd-split"><div class="bkd-rv"><p class="bkd-label">Renting</p><h2 class="bkd-h2">'
				. ( '' !== $r_amt ? $e( $r_pre ) . '<em>' . $e( $r_amt ) . '</em> ' . $e( $RA['per'] ) . ( '' !== $RA['kes'] ? ' ' . $e( '(' . $RA['usd'] . ')' ) : '' ) : 'Rent <em>on request</em>' ) . '</h2>'
				. '<p class="bkd-body bkd-mt">' . $e( $cur . $open . ' Ask for the full tenancy agreement in writing before you pay a deposit or sign.' ) . '</p>';
			if ( $rows ) {
				$o[] = '<ul class="bkd-places">';
				foreach ( $rows as $r ) {
					$o[] = '<li><b>' . $e( $r[0] ) . '</b><span>' . $e( $r[1] ) . '</span></li>';
				}
				$o[] = '</ul>';
			}
			$o[] = '</div><div class="bkd-panel bkd-rv"><p class="bkd-panel-t">See it in person</p><p class="bkd-body">' . $e( 'Tell us when suits you. We\'ll confirm that ' . blockke_dev_rent_name( $d['short'] ) . ' is still available and arrange a time for you to view it.' ) . '</p>'
				. '<p class="bkd-mt"><button class="bkd-btn bkd-btn-navy" type="button" data-bkd-enquire="viewing">Arrange a viewing' . blockke_dev_icon( 'arrow' ) . '</button></p></div></div></section>';
```

### A10. The card phrase and the furnished test

**v191:12828-12849.** `blockke_bkn_rent_row()`: the furnished test of R93 is lines 12834 to 12841.

```php
	function blockke_bkn_rent_row( $id, $price = 0.0, $flag = '' ) {
		$m   = array();
		$usd = get_post_meta( $id, 'bke_rent_usd', true );
		$usd = is_scalar( $usd ) && preg_match( '/^\s*(?:usd|us\$|\$)?\s*(\d[\d,]*(?:\.\d+)?)\s*$/i', (string) $usd, $m ) ? (float) str_replace( ',', '', $m[1] ) : 0.0;
		$dt  = $usd > 0 ? get_post_meta( $id, 'bke_rent_usd_date', true ) : '';
		$dt  = is_scalar( $dt ) && preg_match( '/^\s*(\d{4})-(\d{1,2})-(\d{1,2})\s*$/', (string) $dt, $m ) && checkdate( (int) $m[2], (int) $m[3], (int) $m[1] ) ? trim( (string) $dt ) : '';
		$fu  = has_term( 'furnished', 'property_features', $id ) || has_term( 'furnished', 'property_status', $id );
		$post = $fu ? null : get_post( $id );
		if ( $post ) {
			$c   = (string) $post->post_content;
			$p   = blockke_dev_sections( function_exists( 'has_blocks' ) && has_blocks( $c ) ? $c : wpautop( $c ) );
			$i   = blockke_dev_find( $p['sections'], '/glance|snapshot|key facts|quick facts|in brief|at a look|summary/i' ); // its glance table, as blockke_dev_glance()
			$f   = blockke_dev_rent_info( $i >= 0 ? blockke_dev_kv( $p['sections'][ $i ]['tokens'] ) : array(), $p['sections'] );
			$fu  = isset( $f['furnish'] ) && preg_match( '/\bfurnished\b/i', $f['furnish'][1] ) && ! preg_match( '/\bun-?\s?furnished|\bsemi|\bpart(?:ly|ially)?\b|\bor\b|\boptional|\balso\b/i', $f['furnish'][1] );
		}
		return array(
			'usd'  => $usd,
			'usdd' => $dt,
			'fu'   => (bool) $fu,
			'rs'   => blockke_bkn_rent_text( $id, $price, $flag ),
		);
	}
```

**v191:12851-12869.** `blockke_bkn_rent_text()`: the rent as a card states it (R22). It calls the phrase without the row's dollar figure (rows P30 and P31 of the truth table).

```php
	/**
	 * v1.8.3 (R1, 'rent_cards'): one rental's rent as a card states it, worded by blockke_dev_rent_phrase() as its page states
	 * it: "KES 200K a month", "KES 6,500 a day", "KES 130 per sq ft a month", "KES 711K a month (USD 5,500)" (KES first: the
	 * dated US dollar rent), "USD 5,500 a month" (US dollars with no dated rate: no KES figure); '' when the rent is on request
	 * (the card then says "Rent on request"). The figure is the cards' own (rounded up, as every card's: blockke_bkn_money()),
	 * exact under KES 10,000 ("KES 6,500 a day", never "KES 7K") and, as on the page, per sq ft or in US dollars.
	 */
	function blockke_bkn_rent_text( $id, $p, $f ) {
		$p  = (float) $p;
		$ph = blockke_dev_rent_phrase( $id, $p, 'unit' === $f ? 'unit' : '' );
		if ( $ph['on_request'] ) {
			return '';
		}
		if ( '' !== $ph['usd'] && '' === $ph['kes'] ) {
			return $ph['line'];
		}
		$fig = 'unit' === $f ? $ph['amt'] : ( $p < 10000 ? blockke_dev_money( $p ) : blockke_bkn_money( $p ) );
		return $fig . ' ' . $ph['per'] . ( '' !== $ph['kes'] ? ' (' . $ph['usd'] . ')' : '' );
	}
```

**v191:13306-13319.** `blockke_bkn_money()`: the cards' short figure, rounded up.

```php
	/** "KES 6.06M", "KES 25.3M", "KES 180K": rounded up, so a "from" price is never understated. */
	function blockke_bkn_money( $v ) {
		$cur = blockke_dev_config()['currency'];
		if ( $v <= 0 ) {
			return '';
		}
		if ( $v >= 1000000 ) {
			$dp = $v >= 10000000 ? 1 : 2;
			$x  = ceil( round( $v / 1000000 * pow( 10, $dp ), 6 ) ) / pow( 10, $dp ); // scaled before rounding off the float noise: 4.9 * 100 is 490.00000000000006
			return $cur . ' ' . rtrim( rtrim( number_format( $x, $dp, '.', '' ), '0' ), '.' ) . 'M';
		}
		$k = ceil( round( $v / 1000, 6 ) );
		return $cur . ' ' . ( $k >= 1000 ? '1M' : $k . 'K' );
	}
```

### A11. Structured data: the lease offer

**v191:8254-8257.** `blockke_dev_ld_n()`.

```php
	/** v1.8.3 (R1, listing_jsonld): a figure for the JSON: a whole number as an integer (100000, not 100000.0). */
	function blockke_dev_ld_n( $n ) {
		return floor( (float) $n ) === (float) $n && abs( (float) $n ) < 1e15 ? (int) $n : (float) $n;
	}
```

**v191:8281-8291.** `blockke_dev_ld_day()`: an availability row as a date (R115).

```php
	/** v1.8.3 (R1, listing_jsonld): an "Available from" row's date ("1 November 2026", "November 2026", "2026-11-01") as Y-m-d, else ''. */
	function blockke_dev_ld_day( $s ) {
		$s = trim( preg_replace( '/(\d)(?:st|nd|rd|th)\b/i', '$1', preg_replace( '/^(?:available\s+)?(?:from|on)\s+/i', '', trim( (string) $s ) ) ) );
		foreach ( array( 'Y-m-d', 'j F Y', 'j M Y', 'F j, Y', 'M j, Y', 'F j Y', 'F Y', 'M Y' ) as $f ) {
			$x = DateTime::createFromFormat( '!' . $f, $s );
			if ( $x && 0 === strcasecmp( $x->format( $f ), $s ) ) {
				return $x->format( 'Y-m-d' );
			}
		}
		return '';
	}
```

**v191:8368-8430.** `blockke_dev_ld_offer( $d, $layout )`. `$pre` is the "From" / "Up to" prefix the page shows; `$d['ctx']['flag']` is the price flag (A3); `$d['units']` the home rows.

```php
	/**
	 * v1.8.3 (R1, listing_jsonld; spec 7.6): the rent as the page states it, as an Offer with businessFunction LeaseOut: the price
	 * and currency the lease is set in (blockke_dev_rent_phrase()'s 'num' and 'cur': a rent set in US dollars in US dollars,
	 * KES first or not) and a UnitPriceSpecification with its period (unitCode MON, WEE, DAY or ANN; per sq ft: FTK, a
	 * referenceQuantity of 1 FTK and unitText "per sq ft per month"). A rent the page gives as "From" is an AggregateOffer (and
	 * "Up to" one with its highest price); the dearest home's rent is its highPrice when the homes are let at different KES
	 * rents. No availability but availabilityStarts from a dated "Available from" row; no seller; null when the rent is on
	 * request. $layout: the development layout draws the page (its hero's "From"), else the classic page (its rent box's).
	 */
	function blockke_dev_ld_offer( $d, $layout ) {
		$ra = $layout ? blockke_dev_rent_amount( $d ) : blockke_dev_classic_rent_say( $d );
		if ( ! empty( $ra['on_request'] ) || '' === $ra['amt'] || $ra['num'] <= 0 ) {
			return null;
		}
		$pre  = $layout ? blockke_dev_ld_lead( $d, $ra ) : trim( $ra['pre'] );
		$num  = blockke_dev_ld_n( $ra['num'] );
		$sqft = 'unit' === $d['ctx']['flag'];
		$per  = array( 'a month' => 'MON', 'a week' => 'WEE', 'a day' => 'DAY', 'a night' => 'DAY', 'a year' => 'ANN' );
		$ps   = array( '@type' => 'UnitPriceSpecification' );
		$o    = array( '@type' => '' === $pre ? 'Offer' : 'AggregateOffer', 'businessFunction' => 'http://purl.org/goodrelations/v1#LeaseOut' );
		if ( '' === $pre ) {
			$o['price']  = $num;
			$ps['price'] = $num;
		} elseif ( 'Up to' === $pre ) {
			$o['highPrice']  = $num;
			$ps['maxPrice'] = $num;
		} else {
			$o['lowPrice']  = $num;
			$ps['minPrice'] = $num;
			$up             = array();
			foreach ( (array) $d['units'] as $u ) {
				if ( $u['price'] > 0 ) {
					$up[] = blockke_dev_ld_n( round( (float) $u['price'] ) );
				}
			}
			if ( ! $sqft && blockke_dev_config()['currency'] === $ra['cur'] && count( $up ) > 1 && max( $up ) > $num ) { // per sq ft rows may be whole rents
				$o['highPrice']  = max( $up );
				$ps['maxPrice'] = max( $up );
				$o['offerCount'] = count( $up );
			}
		}
		$o['priceCurrency']  = $ra['cur'];
		$ps['priceCurrency'] = $ra['cur'];
		if ( $sqft ) {
			$ps['unitCode']          = 'FTK';
			$ps['referenceQuantity'] = array( '@type' => 'QuantitativeValue', 'value' => 1, 'unitCode' => 'FTK' );
			$ps['unitText']          = 'per sq ft ' . preg_replace( '/^an?\s+/', 'per ', $ra['per'] );
		} elseif ( isset( $per[ $ra['per'] ] ) ) {
			$ps['unitCode'] = $per[ $ra['per'] ];
			if ( 'a night' === $ra['per'] ) {
				$ps['unitText'] = 'per night';
			}
		} else {
			$ps['unitText'] = $ra['per']; // a label that names no period, as the page writes it
		}
		$o['priceSpecification'] = $ps;
		$st                      = isset( $d['rent']['stated']['available'] ) ? blockke_dev_ld_day( $d['rent']['stated']['available'][1] ) : '';
		if ( '' !== $st ) {
			$o['availabilityStarts'] = $st;
		}
		$o['url'] = $d['url'];
		return $o;
	}
```

### A12. The move-in answers

**v191:7828-7835.** `blockke_dev_timelines()` and `blockke_dev_timeline()`: the server accepts only these three answers.

```php
	/** When the visitor would like to buy; v1.8.3 ($rent): to move in, for a home for rent. */
	function blockke_dev_timelines( $rent = false ) {
		return $rent ? array( 'This month', '1–3 months', 'Later' ) : array( 'Within 3 months', '3–12 months', 'Just exploring' );
	}

	function blockke_dev_timeline( $s, $rent = false ) {
		return in_array( $s, blockke_dev_timelines( $rent ), true ) ? $s : '';
	}
```

### A13. Zoho: "Individual tenant" and the Enquiry type field

**v191:10508-10535.** `blockke_dev_zoho_rent_args()`. Used at v191:10835-10863: `add_filter( 'http_request_args', $zr, 10, 2 )` just before `blockke_bp_send_zoho()`, `remove_filter()` in a `finally`.

```php
	/**
	 * v1.8.3 (R1, Zoho option A): a rental enquiry's Zoho web-form post, changed in flight. The price-check popup snippet (40752)
	 * posts 'Company' => 'Individual buyer' for every lead and has no placeholder for it, so for a rental lead the handler adds
	 * this http_request_args filter just before blockke_bp_send_zoho() and removes it just after, on every path. It acts once,
	 * and only on a post to that snippet's own Zoho endpoint: "Individual buyer" becomes "Individual tenant", and when config
	 * 'zoho_enquiry_field' names the owner's optional "Enquiry type" field, that field is sent as "Rental". Sale leads never
	 * get it, so their post is unchanged (Zoho's own default fills "Enquiry type" for them).
	 */
	function blockke_dev_zoho_rent_args() {
		$bp   = function_exists( 'blockke_bp_config' ) ? blockke_bp_config() : array();
		$url  = isset( $bp['zoho']['endpoint'] ) && is_string( $bp['zoho']['endpoint'] ) ? $bp['zoho']['endpoint'] : '';
		$fld  = blockke_dev_config()['zoho_enquiry_field'];
		$fld  = is_scalar( $fld ) ? trim( (string) $fld ) : '';
		$done = false;
		return function ( $args, $to ) use ( $url, $fld, &$done ) {
			if ( $done || '' === $url || $to !== $url || ! isset( $args['body'] ) || ! is_array( $args['body'] ) ) {
				return $args;
			}
			$done = true;
			if ( isset( $args['body']['Company'] ) && 'Individual buyer' === $args['body']['Company'] ) {
				$args['body']['Company'] = 'Individual tenant';
			}
			if ( '' !== $fld ) {
				$args['body'][ $fld ] = 'Rental';
			}
			return $args;
		};
	}
```
