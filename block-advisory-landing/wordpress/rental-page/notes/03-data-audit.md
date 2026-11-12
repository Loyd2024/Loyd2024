# 03 · Data audit: what the 129 rent-only listings actually contain

For the designers and engineers of the rental page. Written 11 November 2026 from local files only (no live calls).
The machine-readable version of everything below, with one record per listing, is `notes/03-data-audit.json`
(section 14 says how to read it). Scripts: `notes/tools/extract.py`, `notes/tools/analyse.py`, `notes/tools/build_md.py`.

**Words used here.** *Field* = a value WordPress stores for the listing (price, label, bedrooms, size, terms), as the feed
and the classic page print it. *Text* = the listing description (`post_content`). *Row* = a label/value pair in the text: a
two-column table row, a "Label: value" bullet or a bold-label line. *The approved rule* = the rent rules of the development
snippet v1.9.1 that the owner signed off (`ref/dev-v1.9.1.php`), mirrored in Python by `notes/tools/rp_v191.py` and checked against the PHP itself.

## 0. Read this first

**Sources.** `ref/listings.json` (generated 2026-11-11T07:16:30+00:00, 1009 listings), `ref/rent-only-ids.txt` (129 IDs), and the saved live page of each listing:
`ref/pages-final/ID.html` for 100 listings and `ref/pages-stop/ID.html` for the 29 in `ref/dev29.txt`. All 129 saved pages are
the theme's classic page (body class `single-estate_property`; overview, description, address, details and map panels on every page, a features
panel on 105, `ref/pages-stop/45577.html:4315-4760`).

**The feed and the pages agree.** Title, URL, price, after-price label, categories, area, city, status, bedrooms, bathrooms and
the map pin are identical in the feed and on the page for all 129 listings. Only the size differs, by rounding, on 7 listings
(the feed has `1205.56`, the page prints `1,206 ft²`). So the feed can stand in for the fields; the before-price label, rooms,
gallery, features grouping, agent and the text come from the pages.

**Limits of this audit.**

- The pages were saved from the public site behind Cloudflare: e-mail addresses in descriptions read "[email protected]" and a Cloudflare e-mail script tag sits in 17 descriptions. Neither is in the database.
- Custom fields (bke_rent_usd, bke_rent_usd_date, bke_dev_layout, property_label_before and the like) are not in the feed or the pages; what the page prints is used instead.
- ref/pages-final/40248.html is cut off after the tour panel: its agent card and similar listings are missing; every other field of 40248 was read.
- Kind (home/commercial) is decided by the title words first and the categories second, so that miscategorised listings are counted where a visitor would put them; kind_by_category_rule is what the category rule of dev-v1.9.1.php:11360 returns.
- The "v1.9.1" columns come from a Python mirror of the readers in ref/dev-v1.9.1.php (notes/tools/rp_v191.py). The mirror was checked against the PHP functions themselves, copied verbatim and run with PHP 8.3.6 on the saved description of every listing (notes/tools/php_check/make_harness.py): sections, snapshot rows, blockke_dev_rent_info() and blockke_dev_rent_phrase() agree on all 129 listings. Custom fields were taken as empty and the input was the description as the classic page printed it.

**Counts.** 129 listings: 102 homes and 27 commercial by what their titles say (by category alone it is also 102 and 27, but
with two listings swapped: section 6). 29 were on the development layout until today (`ref/dev29.txt`).

### The ten findings that matter most

1. **The fields are thin; the facts live in the text.** Every listing has a title, a URL and a description; 88% have a price with a
   usable label and 98% have a photo. But only 59% have a size field, 73% a project term, 81% features and 18% an address.
   92% of descriptions open with a key/value "snapshot" table, which is the best source for everything else (section 11).
2. **Rent comes in six shapes** (section 1): a month (97), no label (6), per sq ft (16), a day (1), no price (9), and US dollars
   stated in the text of 7 listings whose price field holds a shilling conversion (or nothing).
3. **"From" is unreliable.** 11 listings carry the "From" label; 28 more give a range or a "from" rent in their title or text with no label;
   and a stray "from" sits on the unused second price of 54 listings, 28 of which state a single rent.
4. **Deposit and lease are almost never stated.** A deposit figure: 6 listings. A lease length: 6. Most descriptions say these are
   "confirmed at point of enquiry". A service charge fact: 50 listings (23 in a row of their own). An availability row with a value: 28.
   A viewing row: 2. Furnishing in a row, the title, the status term or a feature: 35 (section 2).
5. **One template, many heading levels.** 98 descriptions have the same eleven sections in the same order, but the section headings are H2 in 70,
   H3 in 47, H4 in 4, H1 in 2 and bold paragraphs in 6; 19 have one title heading above the section level. Read as v1.9.1 reads
   them (cut at the top heading level), 24 descriptions collapse to one section or none, fixtures 45577 and 45538 among them (section 3).
6. **Photos: median 11, but 2 listings have none, 10 have one and 25 have fewer than five** (19 of the 27 commercial listings).
   The first photo is portrait on 19 listings and square on 4 (section 4).
7. **Sizes are written in square metres, the field shows square feet.** 34 texts give sizes in sq m only; the size field is set on 76
   listings and holds the sq m number on 5 of them (printed as ft²), plus 3 more values that cannot be right (section 5).
8. **Categories cannot be trusted alone.** 2 listings have no category (one is an office building that the category rule would
   treat as a home), one block of apartments is filed under Commercial/Offices, and 2 commercial listings carry a bedroom count (section 6).
9. **Map pins are neighbourhood points.** 104 of the 121 pins are shared with other developments (the Westlands pin by 171 listings
   site-wide); 8 listings have no pin and 5 have a pin far outside Nairobi (section 12).
10. **The text needs cleaning before it is shown.** 17 descriptions contain a script element holding JSON (printed through
    `wp_kses_post()` its text would appear on the page); in 60 the first paragraph is the SEO description again; 37 have tables of three
    or four columns; 9 still carry sale-template headings; 1 has "[Development Name]" placeholders (section 10).

## 1. Rent, period and currency

Where it is printed: `.price_area` (`ref/pages-stop/45577.html:4128-4130`). The theme prints an (unused) second price first, then the
listing's own before-label, amount and after-label. Examples of the variants: "From" and per sq ft `ref/pages-final/36834.html:4876-4878`;
a USD figure typed into the after-label and no amount `ref/pages-final/36816.html:4574-4576`; nothing at all `ref/pages-final/35986.html:4170-4172`.
The approved wording of each case is `blockke_dev_rent_phrase()`, `ref/dev-v1.9.1.php:7212-7267`; "rent phrase" below is what that function returns
for the listing's price and label (it never adds "From"; the layout adds that, section 1.1).

| Rent shape | Listings | Share | Homes | Commercial | IDs (or examples) |
| --- | ---: | ---: | ---: | ---: | --- |
| a month | 97 | 75% | 93 | 4 | 45577, 45538, 45507, 45367 |
| price with no after-label | 6 | 5% | 6 | 0 | 45318, 44746, 44048, 42591, 42590, 35398 |
| per sq ft | 16 | 12% | 0 | 16 | 45231, 40298, 40275, 39038, 37563, 36834, 36826, 36823, 36822 and 7 more |
| a day | 1 | 1% | 1 | 0 | 44020 |
| no price (field 0) | 9 | 7% | 2 | 7 | 45228, 43656, 36816, 36136, 36135, 35986, 35609, 35437, 34906 |

**After-price labels, exactly as typed** (meta `property_label`; the page must cope with every one):

| After-price label | Listings | IDs |
| --- | ---: | --- |
| `per month` | 97 | most homes |
| `(empty)` | 12 | 45318, 45228, 44746, 44048, 43656, 42591, 42590, 36136, 36135, 35986, 35609, 35398 |
| `per sq ft per month` | 6 | 36826, 36823, 36814, 36731, 36675, 35427 |
| `per sqft` | 5 | 45231, 40298, 39038, 37563, 36038 |
| `per sq ft` | 5 | 40275, 36834, 36822, 36792, 36662 |
| `Rent on application` | 2 | 35437, 34906 |
| `per day` | 1 | 44020 |
| `USD 1.30 per sq ft per month plus VAT` | 1 | 36816 |

As rent phrases these read: "KES N a month" on 101; "KES N per sq ft a month" on 16; "Rent on request" on 8; "USD N a month" on 2; "KES N a day" on 1; "USD N per sq ft a month" on 1.
The longest is "USD 1.30 per sq ft a month" (36816). Monthly home rents run from KES 30,000 to KES 841,332, median KES 120,000; 4 are above KES 500,000 (44533, 42616, 35583, 29501) and two are not round
thousands because they are dollar conversions (44533: KES 841,332, 35583: KES 710,490). Per-sq-ft rates run from KES 35 to KES 200.

### 1.1 "From"

Three different things say "from", and they disagree:

- **The listing's own before-label** (meta `property_label_before`; v1.9.1 reads it as `from_label`, `ref/dev-v1.9.1.php:7698`, and its layout also says
  "from" when it finds unit rows, `:8568-8587`): "From" on 11 listings: 39276, 37551, 37243, 36834, 36826, 36814, 36809, 36731, 36410, 36220, 35550. It is printed with a trailing space.
- **The before-label of the theme's second price**, printed inside `.second_price_area` although no second price exists
  (`ref/pages-final/34171.html:4037`): "from" on 31 listings and "From" on 23, 54 in all. 28 of them are listings that state one rent
  (for example 42616, a single villa, and 34171), so it is not a signal. Its meta key is unknown (see section 15).
- **The title and the text.** 15 titles say "from KES ..."; 7 of those have no "From" label (42591, 42590, 42340, 39421, 36822, 35609, 35427). And 33 listings give a range or a "from" rent
  in their snapshot rent row, for example 43803 ("KES 70,000 – KES 80,000, depending on floor", field 70,000, no label) and 39421
  ("Studio: KES 30,000 · One-Bedroom: KES 40,000 – 45,000 · Two-Bedroom: KES 50,000", field 30,000, no label).

So the rent is really a starting figure on 39 listings: 11 with the label and 28 without (44048, 43803, 43788, 43712, 43656, 42591, 42590, 42340, 40248, 39421, 39341, 39066, 37494, 37099, 36822, 36792, 36739, 36662, 35945, 35609, 35498, 35477, 35427, 35398, 34336, 34335, 34334, 34301).
Read from the price field and label alone, those 28 show one figure ("KES 70,000 a month") where the listing itself gives a range or a "from".
The stray second-price "from" is on 26 of the 39 starting-figure listings and on 28 others.

### 1.2 No price, no label

| ID | Kind | After-label | What the text says about rent | Rent phrase (v1.9.1) |
| --- | --- | --- | --- | --- |
| 45228 | commercial | `(empty)` | Price and service charge: Please request the latest price list | Rent on request |
| 43656 | home | `(empty)` | Rent: KES 90,000 to KES 110,000 per month, depending on unit | Rent on request |
| 36816 | commercial | `USD 1.30 per sq ft per month plus VAT` | Rent: USD 1.30 per sq ft per month, plus VAT | USD 1.30 per sq ft a month |
| 36135 | commercial | `(empty)` | Rental Price: On application | Rent on request |
| 36136 | commercial | `(empty)` | nothing in the snapshot | Rent on request |
| 35986 | commercial | `(empty)` | Price / lease terms: On application — to be confirmed at point of enquiry | Rent on request |
| 35609 | home | `(empty)` | 2 Bedroom Rent: KES 150,000–180,000 per month, depending on floor; 3 Bedroom Rent: KES 250,000–300,000 per mo… | Rent on request |
| 35437 | commercial | `Rent on application` | Rent: Confirmed at the point of enquiry | Rent on request |
| 34906 | commercial | `Rent on application` | Rent: Quoted on application | Rent on request |

Two of these are homes whose text states the rent (43656, 35609): the price field is simply empty. 36816 has no amount, a USD figure in
its after-label, and reads in dollars only because its glance row "Rent | USD 1.30 per sq ft per month, plus VAT" is found.

A price with no after-label (6 homes: 45318, 44746, 44048, 42591, 42590, 35398) reads "a month" by the rule. None is above KES 2,000,000, so none trips the
"over KES 2M with no label reads Rent on request" clause.

### 1.3 Per square foot, per day

16 listings are priced per sq ft (all commercial by title); the price field holds the rate (KES 35 to 200) and the label is spelt three
ways (`per sqft`, `per sq ft`, `per sq ft per month`). 4 of them carry "From" (36834, 36826, 36814, 36731). One, 36814, has no category, so a category-only test
treats a per-sq-ft office as a home. 40298 has a rate but no size field. 4 commercial listings are priced by the month instead: 36809, 36739, 36636, 33776.

One listing is let by the day: 44020, `KES 6,500` `per day`, a furnished two-bedroom whose title does not say "rent" at all. Nightly rates
also appear inside tables of two multi-unit listings as the furnished option (35498 "From KES 6,000/night", 35398 "from KES 7,000/night").

### 1.4 US dollars

| ID | Price field | After-label | Where USD is stated | Implied KES per USD | Rent phrase (v1.9.1, no custom field) |
| --- | ---: | --- | --- | --- | ---: |
| 44533 | 841,332 | `per month` | snapshot rent row starts with USD | 129.44 | KES 841,332 a month |
| 40298 | 168 | `per sqft` | snapshot rent row starts with USD; rental terms row in USD | 129.23 | KES 168 per sq ft a month |
| 36816 | 0 | `USD 1.30 per sq ft per month plus VAT` | after-price label holds a USD figure; title; snapshot rent row starts with USD;… |  | USD 1.30 per sq ft a month |
| 36792 | 155 | `per sq ft` | snapshot rent row starts with USD; rental terms row in USD | 129.17 | KES 155 per sq ft a month |
| 35583 | 710,490 | `per month` | title; snapshot rent row starts with USD | 129.18 | USD 5,500 a month |
| 34171 | 337,000 | `per month` | title; snapshot rent row starts with USD | 129.62 | USD 2,600 a month |
| 29501 | 650,000 | `per month` | text only ($5,000, USD 5,000) | 130.0 | KES 650,000 a month |

The KES figures are conversions at between 129.2 and 130.0 shillings to the dollar; they go stale as the rate moves.
A reader that finds every snapshot or terms row sees a USD rent row on 6 listings (44533, 40298, 36816, 36792, 35583, 34171); the v1.9.1 reader sees 3 (36816, 35583, 34171), because
44533 has no heading tags and the rows of 40298 and 36792 are labelled "Rental Charges" and "Rental Guide", which its rent-row pattern
(`ref/dev-v1.9.1.php:7174`) does not match. Whether `bke_rent_usd` is filled on any of them is unknown (section 15).
36809 mentions USD only for parking.

### 1.5 Prices in titles

41 titles carry a money amount ("| KES 250,000", "— KES 110,000/mo", "at KSh 140K", "USD 5,500/mo"). They agree with the price field
everywhere except: 36816 (title states the rent in US dollars (USD 1.30 )); 35609 (title says KES 150,000, price field empty); 35583 (title states the rent in US dollars (USD 5,500)); 34171 (title states the rent in US dollars (USD 2,600 )).
A page that prints the title as its H1 and the rent beside it will state the rent twice on those 41 listings.

## 2. Deposit, lease term, service charge, furnishing, availability, viewing

The test for "states it" is rule 11 of the brief and the v1.9.1 code (`blockke_dev_rent_info()`, `ref/dev-v1.9.1.php:7139-7186`): a deposit or a lease needs a
figure, a service charge an amount or "included", and nothing that is "to be confirmed", "on enquiry", "on request", "shared by our team"
counts. Each listing is counted once per topic, in the best place where it states the topic.

| Topic | A row of its own states it | Stated only inside another row, a sentence or an FAQ answer | A row exists but says "to be confirmed" | Only mentioned | Not mentioned | v1.9.1 rule returns a value |
| --- | ---: | ---: | ---: | ---: | ---: | ---: |
| Deposit | 5 (4%) | 1 (1%) | 44 (34%) | 58 (45%) | 21 (16%) | 5 |
| Lease term | 6 (5%) | 0 (0%) | 56 (43%) | 50 (39%) | 17 (13%) | 5 |
| Service charge | 23 (18%) | 27 (21%) | 17 (13%) | 18 (14%) | 44 (34%) | 21 |
| Furnishing | 19 (15%) | 36 (28%) | 0 (0%) | 9 (7%) | 65 (50%) | 15 |
| Availability | 28 (22%) | 61 (47%) | 14 (11%) | 0 (0%) | 26 (20%) | 23 |
| Viewing arrangement | 2 (2%) | 29 (22%) | 0 (0%) | 93 (72%) | 5 (4%) | 2 |

Read the second column with care for furnishing and availability: it counts any sentence that says "unfurnished", "ready for occupation"
or "available to let", which most descriptions do. For the page, only the first column is a labelled fact.

### 2.1 Deposit

49 listings have a row that names the deposit, under 23 different labels. It states a figure on 5: 43856 (`Lease terms and deposit`: "Two months deposit and one month rent"); 43855 (`Lease terms and deposit`: "2 months rent and one month deposit"); 43482 (`Deposit`: "2 months deposit"); 37206 (`Security Deposit`: "2 months’ rent"); 35477 (`Deposit`: "2 months deposit").
One more states it inside another row: 39301 (`Lease Terms`: "One month’s rent upfront, two months’ deposit, one-year lease agreement").
The stated deposit is two months on five of the six and one month on 43855, which reverses the wording of its sister listing 43856 in the same building.
The other rows read "To be confirmed at point of enquiry", "Confirmed at the point of enquiry",
"Shared by your Block Real Estate adviser on enquiry", "Shared in the lease summary on request" or are empty (45367, 42260).

| Label as written | Listings | Rows with a fact | Rows "to be confirmed" or empty | Other rows | Section (rows) | Example |
| --- | ---: | ---: | ---: | ---: | --- | --- |
| `Deposit` | 9 | 2 | 7 | 0 | terms 9 | 43482: 2 months deposit |
| `Security Deposit` | 7 | 1 | 7 | 0 | terms 7, snapshot 1 | 37206: 2 months’ rent |
| `Deposit & lease terms` | 4 | 0 | 4 | 0 | snapshot 4 | 36506: To be confirmed at point of enquiry |
| `Lease terms and deposit` | 4 | 2 | 2 | 0 | snapshot 4 | 43856: Two months deposit and one month rent |
| `Deposit & Lease Terms` | 2 | 0 | 2 | 0 | snapshot 2 | 39421: To be confirmed at point of enquiry |
| `Deposit and advance rent` | 2 | 0 | 2 | 0 | terms 2 | 42292: To be confirmed at point of enquiry |
| `Deposit and lease term` | 2 | 0 | 2 | 0 | snapshot 2 | 36220: Standard residential lease terms; deposit and… |
| `Deposit and lease terms` | 2 | 0 | 2 | 0 | terms 1, snapshot 1 | 36220: standard residential lease terms apply. The d… |
| `Deposit and service charge` | 2 | 0 | 2 | 0 | intro 1, snapshot 1 | 42260: (empty) |
| `Deposit, advance rent and agency fees` | 2 | 0 | 2 | 0 | terms 2 | 45367: (empty) |
| `Deposit, advance rent and minimum lease term` | 2 | 0 | 2 | 0 | terms 2 | 35498: To be confirmed at point of enquiry |
| `Deposit, lease length and move-in costs` | 2 | 0 | 2 | 0 | terms 2 | 44746: Shared by your Block Real Estate adviser on e… |

Plus 11 more wordings used by one or two listings each, for example `Deposit, lease length and service charge inclusions`, `Lease terms, deposit and availability date`, `Deposit & lease term`, `Deposit requirement`, `Deposit, advance rent, service charge and lease length`, `Deposit, lease duration and tenancy terms`. The full list is in the JSON (`summary.terms.deposit.row_labels`).

Traps. (a) Combined labels: "Lease terms and deposit", "Deposit & lease terms", "Deposit, lease length and service charge inclusions" hold one
value for two or three topics. (b) 4 listings still carry sale text with a *purchase* deposit ("a 20% deposit, with 40% payable ..."): 34336, 34335, 34334, 34301.
That is not a rental deposit. (c) "Utility Deposits" rows on three office listings (36834, 36826, 36822) are a different charge.

### 2.2 Lease term

62 listings have a lease row (32 wordings); 6 state a length: 42292 (`Lease`: "1 or 2 year contracts preferred"); 42279 (`Lease`: "1 or 2 year contracts preferred"); 42260 (`Lease term`: "Long-term contracts, one year and above"); 39301 (`Lease Terms`: "One month’s rent upfront, two months’ deposit, one-year lea…"); 36731 (`Lease term`: "Standard 6-year commercial lease indicated"); 35550 (`Lease term`: "Long-term contracts, one year and above").
The same listings repeat it in a sentence ("on lease terms of one year and above", "on a standard six-year lease"). Nothing else states a length.

| Label as written | Listings | Rows with a fact | Rows "to be confirmed" or empty | Other rows | Section (rows) | Example |
| --- | ---: | ---: | ---: | ---: | --- | --- |
| `Lease term` | 8 | 3 | 5 | 0 | snapshot 7, intro 1 | 42260: Long-term contracts, one year and above |
| `Lease Terms` | 7 | 1 | 6 | 0 | snapshot 7 | 39301: One month’s rent upfront, two months’ deposit… |
| `Lease length` | 5 | 2 | 3 | 0 | terms 5 | 42292: 1 or 2 year contracts preferred |
| `Deposit & lease terms` | 4 | 0 | 4 | 0 | snapshot 4 | 36506: To be confirmed at point of enquiry |
| `Lease terms and deposit` | 4 | 0 | 2 | 2 | snapshot 4 | 43856: Two months deposit and one month rent |
| `Minimum Lease Period` | 4 | 0 | 4 | 0 | terms 4 | 36834: To be confirmed |
| `Lease Period` | 3 | 0 | 3 | 0 | terms 2, snapshot 1 | 36834: To be confirmed at the point of enquiry |
| `Lease Term` | 3 | 1 | 2 | 0 | terms 2, units 1 | 36731: Standard 6-year lease indicated |
| `Lease term and renewal terms` | 3 | 0 | 3 | 0 | terms 3 | 45367: Shared by our team on request |
| `Lease terms` | 3 | 0 | 3 | 0 | snapshot 2, intro 1 | 39066: To be confirmed at point of enquiry |
| `Deposit & Lease Terms` | 2 | 0 | 2 | 0 | snapshot 2 | 39421: To be confirmed at point of enquiry |
| `Deposit and lease term` | 2 | 0 | 2 | 0 | snapshot 2 | 36220: Standard residential lease terms; deposit and… |

Plus 20 more wordings used by one or two listings each, for example `Deposit and lease terms`, `Deposit, advance rent and minimum lease term`, `Deposit, lease length and move-in costs`, `Deposit, lease length and service charge inclusions`, `Lease`, `Lease Duration`. The full list is in the JSON (`summary.terms.lease.row_labels`).

Trap: the v1.9.1 lease pattern (`ref/dev-v1.9.1.php:7150`) matches the combined label "Lease terms and deposit", so for 43856, 43855 it returns the deposit sentence as the lease
("Two months deposit and one month rent"). And it drops 39301 because the value is longer than 60 characters (`:7166`).

### 2.3 Service charge

47 listings have a service charge row; 23 state something: "included" on 9 (43856, 43855, 43482, 40248, 39276, 39066, 36834, 36822, 36809) and an amount on 14 (43583, 40298, 40275, 39341, 37563, 36826, 36823, 36816, 36814, 36731 and 4 more; some state both).
Amounts are per month ("KES 22,500 per month", 43583), per year ("KES 10,000, payable once a year", 39341) or per sq ft ("KES 20 per sq ft per month").
Another 27 listings state it only elsewhere, most often inside the rent row: 11 rent rows read like "KES 135,000 per month, service charge included"
(45577, 45538, 44746, 44745, 40490, 39066, 37258, 37243, 36809, 34926, 34171). In all, 50 listings (39%) state a service charge fact somewhere.

| Label as written | Listings | Rows with a fact | Rows "to be confirmed" or empty | Other rows | Section (rows) | Example |
| --- | ---: | ---: | ---: | ---: | --- | --- |
| `Service Charge` | 21 | 51 | 7 | 0 | units 27, terms 19, snapshot 12 | 40298: KES 30 per sq. ft. per month |
| `Service charge` | 20 | 17 | 6 | 0 | snapshot 15, terms 5, units 1, intro 1, amenities 1 | 43856: Inclusive in the rent |
| `Service charge and utilities` | 6 | 0 | 8 | 0 | terms 6, snapshot 2 | 45318: To be confirmed at point of enquiry |
| `Deposit and service charge` | 2 | 0 | 2 | 0 | intro 1, snapshot 1 | 42260: (empty) |
| `Deposit, lease length and service charge inclusions` | 2 | 0 | 2 | 0 | terms 2 | 44748: Shared in the lease summary on request |
| `Estimated Service Charge` | 2 | 2 | 0 | 0 | terms 1, snapshot 1 | 36675: KES 20 per sq.ft per month |
| `Monthly Service Charge` | 2 | 6 | 0 | 0 | units 5, terms 1 | 36814: From KES 40,000 |
| `Deposit, advance rent, service charge and lease length` | 1 | 0 | 1 | 0 | terms 1 | 43523: to be confirmed at point of enquiry |
| `Estimated Monthly Service Charge` | 1 | 1 | 0 | 0 | units 1 | 36823: KES 432,000 before VAT |
| `Lease terms, deposit and service charge` | 1 | 0 | 1 | 0 | snapshot 1 | 43523: To be confirmed at point of enquiry |

Plus 9 more wordings used by one or two listings each, for example `Monthly service charge`, `Price and service charge`, `Service Charge Per Sq. Ft.`, `Service Charge at KES 18 psf`, `Service Charge at KES 29 per sq. ft.`, `Service Charge at USD 0.20 per sq. ft.`. The full list is in the JSON (`summary.terms.service.row_labels`).

### 2.4 Furnishing

Furnishing is said in five places, and they overlap:

- a **row** (`Furnishing`, `Furnishing status`, `Furnished / unfurnished`) with a value on 19 listings: furnished 8 (44533, 44020, 43833, 43482, 42260, 41946, 40298, 35235), unfurnished 8 (43543, 41921, 41562, 37206, 37099, 36502, 36499, 34171),
  semi-furnished 1 (34926: "Semi-furnished (fully furnished available on request)"), and two hand-written notes that are not a status
  (43788: "There is also furnished apartments"; 43523: "Theres also unfurnished apartments");
- the **title** says "Furnished" on 16 listings (44533, 44020, 43833, 43482, 43453, 42292, 42260, 41946, 41862, 35235, 29531, 29475, 29462, 29436, 29419, 28919);
- the **status term** "Furnished" on 9 (40248, 35235, 29531, 29489, 29475, 29462, 29436, 29419, 28919);
- the **feature** "Furnished" on 5 (44020, 43482, 42292, 42260, 41862);
- **two rents in a table**, furnished and unfurnished, on 5 multi-unit listings (43712, 37243, 36220, 35498, 35398).

35 listings carry at least one of these; 20 say "furnished" in a structured place (44533, 44020, 43833, 43482, 43453, 42292, 42260, 41946, 41862, 40298, 40248, 35235, 34926, 29531, 29489, 29475, 29462, 29436, 29419, 28919). No listing contradicts itself.
The title says furnished but the status term is missing on 9 (44533, 44020, 43833, 43482, 43453, 42292, 42260, 41946, 41862); the term is set without the title on 40248, 29489.
Beyond that the word appears in running text or an FAQ answer ("Is the apartment furnished?" is the second most common question).

| Label as written | Listings | Rows with a fact | Rows "to be confirmed" or empty | Other rows | Section (rows) | Example |
| --- | ---: | ---: | ---: | ---: | --- | --- |
| `Furnishing` | 24 | 17 | 11 | 0 | snapshot 19, units 8, intro 1 | 44533: Furnished |
| `Furnished or unfurnished` | 5 | 0 | 4 | 1 | terms 3, finishes 2 | 43656: To be confirmed at point of enquiry |
| `Furnished / unfurnished` | 3 | 2 | 1 | 0 | snapshot 3 | 36502: Unfurnished |
| `Furnished` | 2 | 1 | 1 | 2 | terms 3, amenities 1 | 40298: Furnished space available for tenants seeking… |
| `Furnishing Status` | 2 | 1 | 1 | 0 | terms 2 | 37206: Unfurnished |
| `Unfurnished (per month)` | 2 | 0 | 1 | 4 | terms 3, units 2 | 35498: From KES 65,000 |
| `1 bedroom furnished` | 1 | 0 | 0 | 1 | snapshot 1 | 36220: KES 170,000 to 180,000 per month |
| `Furnished / Serviced` | 1 | 0 | 1 | 1 | units 2 | 35398: From KES 7,000/night (~KES 130,000/mo) |

Plus 15 more wordings used by one or two listings each, for example `Furnished / serviced`, `Furnished 3 Bedroom`, `Furnished Option`, `Furnished Options`, `Furnished Rent`, `Furnishing status`. The full list is in the JSON (`summary.terms.furnish.row_labels`).

### 2.5 Availability

55 listings have an availability row (`Availability` on 49 of them; also `Occupation`, `Occupation Status`, `Occupation Date`, `Occupancy Status`).
It has a value on 28 listings and reads "to be confirmed" on the rest. The values, as written:

| Value of the row | Rows |
| --- | ---: |
| It is available | 5 |
| Available | 4 |
| Ready for immediate occupation | 4 |
| Available now | 3 |
| For rent | 3 |
| Ready for occupation | 3 |
| Expected from Q3 2026 | 2 |
| From 1st October | 2 |
| Available to let — immediate | 1 |
| Contact Block Real Estate for current vacancy status | 1 |
| One available | 1 |
| Phase 2 operational H2 2026 — units available from Q3 2026 | 1 |
| The unit is available | 1 |
| To let | 1 |

Three things to notice. (a) **Dates go stale**: 40490 "From 1st October"; 36135 "Expected from Q3 2026"; 36136 "Expected from Q3 2026"; 35986 "Phase 2 operational H2 2026 — units available from Q3 2026".
Today is 11 November 2026: "Q3 2026" has passed and "1st October" has no year. (b) Some values are not facts but pass a test that only looks for
hedge words: "Contact Block Real Estate for current vacancy status" (41690), "Subject to apartment availability" (36410, which the v1.9.1 rule returns),
"For rent" (41921, 41862, 39421), "To let" (34906). (c) 35 listings also have a `Status` row in the snapshot ("Available for rent", "Ready for occupation",
"Completed June 2026, occupation ongoing"); the approved rule does not read it as availability (`ref/README-wordpress.md:181`).

### 2.6 Viewing

Only 3 listings have a viewing row: 43543 and 36823 ("By appointment") and 33230 ("Private viewing available on request", which reads as
unconfirmed because of "on request"). Everywhere else viewing is a call to action: "Can I arrange a viewing?" (30 times) and "How do I arrange
a viewing?" (19) are among the most common FAQ questions, answered "Yes. Contact us to book a viewing." 15 texts mention a virtual viewing or tour.

### 2.7 The rental terms section itself

114 listings have a section about rent and terms (wordings: "rental terms" 55, "rent and lease terms" 17, "payment plan" 6, "rent and lease structure" 6, "lease terms" 4, "rent" 3, "pricing & payment" 3; 26 different wordings in all).
It is built as: paragraphs only (44 sections), list (32 sections), table (27 sections), list+table (9 sections), grouped lists (5 sections).
The typical one is a two-row table (monthly rent; "Deposit, lease length and move-in costs | shared on enquiry") or a short paragraph such as
"Our team will take you through the deposit, advance payment, lease length and renewal terms when you enquire" (45577). 15 listings have no
such section: 42592, 42591, 42590, 42370, 40037, 40016, 29531, 29501, 29489, 29475, 29462, 29436, 29419, 28919, 28868.

The v1.9.1 pattern for a terms heading (`ref/dev-v1.9.1.php:7142`) matches the section on 73 listings. It does not match these wordings: "Rent and Lease Structure" (6), "Rent & Terms" (2), "Availability & Terms" (1), "Indicative Rental Structure" (1), "Lease Structure and Commercial Terms" (1), "Lease Structure and Payment" (1), "Price Guide" (1), "Rent and Terms" (1), "Rent and What’s Included" (1), "Rent, Service Charge and Lease Structure" (1), "Rental Considerations" (1), "Rental Price & Terms" (1), "Rental Summary" (1), "Rental and Purchase Options" (1), "What to Confirm Before Renting" (1).
It does match "Payment Plan" and "Pricing & Payment", which on the nine sale-template listings mix rental and purchase terms: the "Payment Plan" of 39341
holds its rent by floor and a yearly service charge, that of 34301 a 20% purchase deposit.

### 2.8 What the approved rule returns today, and what it misses

| Topic | Listings | IDs where `blockke_dev_rent_info()` returns a value | Rows with a fact that it does not return, and why |
| --- | ---: | --- | --- |
| Deposit | 5 | 43856, 43855, 43482, 37206, 35477 | — |
| Lease term | 5 | 43856, 43855, 42292, 36731, 35550 | 42279 (the reader finds one section only (a title heading sits above the section headings)); 42260 (the table comes before any heading (its own heading is glued into a paragraph)); 39301 (value longer than 60 characters) |
| Service charge | 21 | 43856, 43855, 43583, 43482, 40298, 40275, 40248, 39276, 37563, 36834, 36826, 36823, 36822, 36816, 36814, 36809, 36731, 36675, 36662, 36038, 35427 | 39341 (its section heading ("Payment Plan") is not one the rule reads); 39066 (row sits outside the snapshot and rental terms sections (in "Unit Types")) |
| Furnishing | 15 | 43833, 43788, 43543, 43523, 43482, 41946, 41921, 41562, 40298, 37206, 37099, 36502, 36499, 35235, 34171 | 44533 (no heading tags: the reader finds no sections); 44020 (no heading tags: the reader finds no sections); 42260 (the table comes before any heading (its own heading is glued into a paragraph)); 34926 (value has a word the rule treats as unconfirmed) |
| Availability | 23 | 44422, 44050, 44048, 43833, 43788, 43482, 42616, 41946, 41921, 41862, 40490, 40275, 39456, 39421, 37258, 36502, 36410, 36136, 36135, 36038, 35986, 35583, 34906 | 44135 (the reader finds one section only (a title heading sits above the section headings)); 41690 (the reader finds one section only (a title heading sits above the section headings)); 36834 (label "Occupation Status" is not one the rule reads); 36814 (label "Occupation Status" is not one the rule reads; only the first snapshot section ("Building at a Glance") is read; this row is in "Project Snapshot"); 36662 (label "Occupation Status" is not one the rule reads); 35609 (label "Occupancy Status" is not one the rule reads) |
| Viewing arrangement | 2 | 43543, 36823 | — |

The rule also returns values that are not facts: the lease on 43856 and 43855 (above), "Furnishing: There is also furnished apartments" (43788),
"Availability: It is available" (five listings) and "Availability: Subject to apartment availability" (36410).

## 3. Headings: vocabulary and order

### 3.1 How headings are set

- 123 descriptions use heading tags; 6 use none and set their section titles in bold, either as a paragraph of their own
  (`<p><strong>Project Snapshot</strong></p>`) or run into the first line (`<p><strong>Amenities</strong><br>Guests have access ...</p>`): 44746, 44745, 44533, 44020, 43995, 31842.
  5 of the six were on the development layout until today.
- The level that carries the sections is H2 on 70 listings, H3 on 47, H4 on 4 (43819, 43803, 43788, 43712) and H1 on 2 (37563, 37258).
- 19 listings have **one heading above the section level that repeats the title** (an H2 over H3 sections, an H3 over H4, or on 33776 an H1 over H2): 45577, 45538, 45507, 45367, 45318, 45231, 45228, 44748, 44747, 44339, 44135, 43819, 43712, 43523, 43453, 42340, 42279, 41690, 33776.
- H1 tags inside the description on 3 listings (37563, 37258, 33776): with the page title that makes 12, 24 and 2 H1s on those classic pages.
- Headings per description: median 12, maximum 86 (37258); 24 listings have more than 30 (FAQ questions and amenity names set as headings).
  26 listings set FAQ questions as headings (302 question headings in all).
- Oddities: bold markup inside headings (13 listings), a bold run that stops mid-word (45538 "...Lenana Roa**d**"; 29419, whose heading reads
  "KES 135,000 / Montes."), one empty heading (36834), one whole sentence set as an H3 (43656).

### 3.2 The sections, by what they are for

Each heading at the section level was put in a slot by its wording (`notes/tools/rp_sem.py`, `SLOT_RULES`). "Position" is the median place of
the slot in the description, 0 = first, 1 = last.

| Slot | Listings | Share | Position | Most common wordings (sections) | Different wordings |
| --- | ---: | ---: | ---: | --- | ---: |
| title repeated | 29 | 22% | 0.00 | # bedroom apartment for rent in kilimani, off lenana road (2); # bedroom apartment for rent in kilimani, george padmore road (2); premium turnkey living on elgeyo marakwet road \| {amount} / month (2); # bedroom duplexes for rent in lavington, off gitanga road \| {project}es (1); #-bedroom smart apartment for rent in riverside, nairobi (1) | 26 |
| introduction | 12 | 9% | 0.00 | opening introduction (8); introduction (4) | 2 |
| snapshot (key facts) | 118 | 92% | 0.00 | project snapshot (93); building at a glance (7); property snapshot (3); rental snapshot (3); development at a glance (3) | 12 |
| what it was designed for | 117 | 91% | 0.09 | a development designed for ... (93); a home designed for ... (14); the home (5); a space designed for ... (2); a spacious home for comfortable family living (1) | 9 |
| the home or the units | 121 | 94% | 0.18 | unit types (60); unit type (11); unit details (10); available office spaces (5); the turnkey experience: what's included? (5) | 38 |
| amenities | 121 | 94% | 0.27 | amenities (95); amenities and building features (3); building amenities (3); amenities at {project} (2); lifestyle and amenities (2) | 21 |
| location | 129 | 100% | 0.36 | location advantage (112); the prime kilimani location (5); the location (3); prime kileleshwa location (2); location advantage: parklands, nairobi (1) | 11 |
| tax incentives (SEZ) | 3 | 2% | 0.50 | tatu city special economic zone incentives (2); tatu city sez incentives (1) | 2 |
| rent and terms | 114 | 88% | 0.45 | rental terms (55); rent and lease terms (17); payment plan (6); rent and lease structure (6); lease terms (4) | 26 |
| why it works for ... | 115 | 89% | 0.55 | why it works for ... (196); why {name} works for ... (2); why you'll love living here (2); why this apartment works for ... (1) | 4 |
| finishes and specifications | 108 | 84% | 0.73 | finishes and specifications (102); finishes and technical specifications (2); furnishing and interior features (1); construction & technical specifications (1); finishes and furnishing (1) | 6 |
| who it suits | 120 | 93% | 0.82 | who this development suits best (70); who this home suits best (14); who this apartment suits best (6); who this unit suits best (3); who this rental suits best (3) | 21 |
| renting or buying from abroad | 5 | 4% | 0.62 | buying from abroad (3); renting from abroad or on behalf of family (1); buying from abroad (diaspora buyers) (1) | 3 |
| FAQ | 127 | 98% | 0.91 | faq (71); frequently asked questions (55); {project} - more questions answered (3); frequently asked questions about {project} (1); skypark - more questions answered (1) | 10 |
| links to other listings | 14 | 11% | 0.87 | other commercial space in westlands (10); more properties in westlands (6); more properties in kilimani (2); also available at nivina towers (2); other marina bay homes in westlands (1) | 6 |
| viewing / call to action | 119 | 92% | 1.00 | arrange a viewing (54); call to action (26); book a viewing (8); book a viewing at {project} (3); enquire about this home (3) | 37 |
| anything else | 10 | 8% | 0.11 | {project} apartments sits on lantana road, one of westlands' more settled residential addresses, close to the shops, offices and restaurants that make the neighbourhood so convenient (1); comfortable living in a convenient windsor, nothern bypass (1); spacious living and dining areas (1); open-plan fitted kitchen (1); pantry (1) | 19 |

"Why it works for ..." comes once or twice per listing; who it is for: Companies Housing Staff (50), Tenants (50), Residents (19), Business Occupiers (12), Corporate and Longer-Term Tenants (8), Renters (7), Investors (6), Businesses (5), Corporate and Relocating Tenants (5), Homeowners (5), Occupiers (3), Relocating Professionals and Corporate Tenants (3), and 23 more audiences used once or twice. "Homeowners" and "Investors" are sale-template leftovers (section 10).

### 3.3 Heading vocabulary, normalised

All headings of all levels (questions left out; numbers written as #, amounts as {amount}, the listing's own project name as {project};
"A Development Designed for ..." and "Why It Works for ..." counted as one wording each). 135 wordings are used by two or more listings; 530 are used once.

| Heading | Listings | Heading | Listings |
| --- | ---: | --- | ---: |
| location advantage | 112 | building features | 7 |
| why it works for ... | 111 | more properties in westlands | 6 |
| finishes and specifications | 102 | payment plan | 6 |
| amenities | 95 | rent and lease structure | 6 |
| a development designed for ... | 93 | who this apartment suits best | 6 |
| project snapshot | 93 | available office spaces | 5 |
| faq | 71 | borehole water | 5 |
| who this development suits best | 70 | may not suit tenants who need | 5 |
| unit types | 60 | parking | 5 |
| rental terms | 57 | the home | 5 |
| frequently asked questions | 55 | the prime kilimani location | 5 |
| arrange a viewing | 54 | the turnkey experience: what's included? | 5 |
| call to action | 26 | why {name} works for ... | 4 |
| rent and lease terms | 17 | # bedroom apartment: {amount} per month | 4 |
| a home designed for ... | 14 | apartment features | 4 |
| who this home suits best | 14 | flexible office sizes | 4 |
| unit type | 11 | introduction | 4 |
| other commercial space in westlands | 10 | lease terms | 4 |
| unit details | 10 | occupier benefits | 4 |
| book a viewing | 8 | secure parking | 4 |
| opening introduction | 8 | security | 4 |
| building at a glance | 7 | # bedroom apartment | 3 |

### 3.4 The typical order

**Snapshot, what it was designed for, the units, amenities, location, rent and terms, why it works (once for tenants, once for companies), finishes,
who it suits, FAQ, viewing.** 98 listings (76%) have all eleven in exactly that order (a repeated title or an introduction may come first); 23 (18%) keep the order with some missing;
8 depart from it (42616, 42370, 41946, 36822, 36136, 36135, 34906, 31842).
The most common full sequences:

| Listings | Sequence of slots |
| ---: | --- |
| 47 | snapshot > concept > units > amenities > location > terms > why > why > finishes > suits > faq > cta |
| 13 | title > snapshot > concept > units > amenities > location > terms > why > why > finishes > suits > faq > cta |
| 6 | intro > snapshot > concept > units > amenities > location > terms > why > why > finishes > suits > faq > cta |
| 6 | snapshot > concept > units > amenities > location > terms > why > finishes > suits > faq > cta |
| 5 | title > intro > snapshot > concept > units > amenities > location > terms > why > why > finishes > suits > faq > cta |
| 4 | title > units > units > location > faq |
| 3 | snapshot > concept > units > amenities > location > why > why > finishes > suits > faq > cta |
| 3 | snapshot > concept > units > amenities > location > terms > why > finishes > suits > faq > faq > crosslinks > cta |

Where sections are missing: finishes and specifications on 21; rent and terms on 15; why it works for ... on 14; what it was designed for on 12; snapshot (key facts) on 11; viewing / call to action on 10; who it suits on 9; the home or the units on 8; amenities on 8; FAQ on 2.
The older listings (IDs 28868 to 29531) follow a shorter five-heading pattern (price line, what is included, inside, location, FAQ) and 31842 and 42370 follow none.
On the eight listings with a Rank Math FAQ block and a second question list, the FAQ heading is an H3 inside the "who it suits" H2, followed by an H2
"... — More Questions Answered" (36826, 36816, 36814, 36809, 36731, 36675, 35437, 35427).

### 3.5 What the v1.9.1 reader makes of this

`blockke_dev_sections()` cuts the description at its top heading level (`ref/dev-v1.9.1.php:808-848`), and `blockke_dev_glance()` then looks for a
section titled glance, snapshot, key facts ... (`:978-982`). Mirrored on today's texts:

- **one section only** on 18 listings, because the top level holds just the repeated title: 45577, 45538, 45507, 45367, 45318, 45231, 45228, 44748, 44747, 44339, 44135, 43819, 43712, 43523, 43453, 42340, 42279, 41690. This morning's development-layout copy of 45577
  shows it: the whole description sits under "About Kings Courtyard" with the title as an H3 (`ref/pages-final/45577.html`, section `#bkd-overview`);
- **no section** on 6 listings with no heading tags: 44746, 44745, 44533, 44020, 43995, 31842;
- a snapshot section found on 94 listings, not found on 35;
- a rental terms section found on 73.

A reader for the new page should find the section level itself (the level where "Project Snapshot", "Amenities", "Location Advantage" sit), treat a
bold-only paragraph or a bold first line as a heading when there are no heading tags, and skip a first heading that repeats the title.

## 4. Photos

### 4.1 How many

| Gallery photos | Listings | Share |
| --- | ---: | ---: |
| 0 | 2 | 2% |
| 1 | 10 | 8% |
| 2-4 | 13 | 10% |
| 5-9 | 30 | 23% |
| 10-14 | 40 | 31% |
| 15-24 | 29 | 22% |
| 25+ | 5 | 4% |

Median 11, mean 11.6, maximum 59 (44533); 1496 photos in all. No photo at all: 45231, 45228 (both commercial; they have no featured image either, so cards and
the hero have nothing to show). One photo: 37258, 36834, 36816, 36814, 36809, 36792, 36731, 36506, 34906, 34171. Fewer than five: 25 listings, 19 of them commercial (commercial median 3, homes 13).
More than 25: 44533, 44260, 44135, 42591, 29501. A gallery design has to work with 0, 1, 2 to 4 and 59 photos.

### 4.2 Where the images are and what sizes exist

The classic gallery (`ref/pages-stop/45577.html:4165-4261`) prints, per photo, a link to the original and an image at the theme's slider size:

- **original** `https://block.ke/wp-content/uploads/YYYY/MM/NAME.EXT` (link `a.prettygalery`). Every file is on block.ke. 465 of 1496 are WordPress
  `-scaled` originals. Extensions: jpg 803, jpeg 618, webp 56, png 19. 126 file names carry two extensions
  (`IMG_3192.jpg-scaled.jpeg`), so never derive a URL by string surgery;
- **slider size** `NAME-<width>x1080.EXT`: 1080 px high, width free. 1252 photos (84%) have it: `-1620x1080` (927, 3:2), `-813x1080`/`-810x1080` (portrait, 140),
  `-1440x1080` (61, 4:3), `-1920x1080` (11). The other 244 photos (16%) are shorter than 1080 px and are printed at their original size, with no suffix;
- **thumbnail** `NAME-143x83.EXT` (hard crop) for 1475 photos;
- **feed image** (`im`, the featured image) at `NAME-768x<height>.EXT` on 115 listings, without a suffix on 12 (narrower than 768 px);
- one `srcset` on the page lists every size WordPress made for a 2048 px original (`ref/pages-stop/45577.html:4757`): 105x70, 120x80, 215x143,
  300x200, 500x333, 585x390, 768x512, 1024x683, 1536x1024, 1620x1080.

The classic gallery images carry no `width`, `height`, `loading` or `srcset`, and `alt` is empty on 888 of 1496 photos (59%); 47 listings have no alt on any photo.
Where an alt exists it is the project name ("Vienna Gardens" on 80 photos). The first gallery photo is the featured image on all 127 listings that have photos.
The gallery's attachment IDs are in the custom field `wpestate_property_gallery` (older listings: `image_to_attach`), `ref/dev-v1.9.1.php:4986-4987`;
real widths and heights have to come from the attachment, not from the URL.

### 4.3 The first photo

| Aspect of the first photo | Listings | Share |
| --- | ---: | ---: |
| 3:2 | 69 | 54% |
| 3:4 | 17 | 13% |
| 4:3 | 16 | 12% |
| 16:9 | 9 | 7% |
| other landscape | 9 | 7% |
| 1:1 | 4 | 3% |
| unknown | 2 | 2% |
| 9:16 | 1 | 1% |
| other portrait | 1 | 1% |
| wider than 16:9 | 1 | 1% |

Derived from the slider URL (88 listings), else the feed image URL (29), else the og:image width and height (10); not derivable for the 2 with no photo.

- **Portrait first photo** on 19 listings: 43819, 41690, 40248, 40037, 40016, 39456, 39421, 39301, 39276, 39066, 37551, 36816, 36814, 36506, 35945, 35498, 35427, 34906, 28919. Square: 36809, 36502, 36499, 34171.
- **Small first photo**: on 39 listings the first photo is under 1080 px high, and on 10 the lead image is under 900 px wide: 34906 (415x739), 36816 (644x859), 36822 (651x488), 36506 (720x950), 36502 (720x720), 36499 (720x720), 39341 (721x406), 36814 (765x1020), 35427 (765x1020), 37494 (768x701).
- Across all photos: 1059 landscape, 191 portrait, 2 square, 244 of unknown shape. 22 listings have at least one portrait photo; in 15 most photos are portrait (43819, 41690, 40248, 40037, 40016, 39456, 39421, 39301, 39276, 39066, 37551, 37206, 35945, 35398, 28919).
- The same first photo opens two listings: 44259 and 44050; 44048 and 42591; 43450 and 37206; 41921 and 41862; 36136 and 35986.

## 5. Size units, bedrooms, studios

### 5.1 The size field against the text

The size field (meta `property_size`) is printed as square feet ("1,550 ft²", `ref/pages-final/42450.html:4326`). It is set on 76 listings (59%).

| What the field holds | Listings | IDs |
| --- | ---: | --- |
| field matches a sq ft figure in the text | 40 | 45231, 45228, 42450, 42449, 41946, 40275, 39421, 39301, 39066, 39038, 37563, 37494 and 28 more |
| no size anywhere | 33 | 45318, 44748, 44747, 44339, 44135, 44020, 43995, 43856, 43855, 43833, 43819, 43656 and 21 more |
| field empty; text states a size | 20 | 45577, 45538, 45507, 45367, 44746, 44745, 44533, 44260, 44259, 44258, 44050, 44048 and 8 more |
| field is the sq m figure converted to sq ft | 14 | 44422, 42783, 42781, 42592, 42591, 42590, 41921, 39341, 39276, 36136, 36135, 35945 and 2 more |
| field set; text states no size | 11 | 42260, 39456, 37258, 36220, 35583, 34926, 34171, 29531, 29475, 29419, 28919 |
| field holds the sq m number (shown as ft²) | 5 | 43543, 43450, 40248, 40037, 36410 |
| field differs from the sizes in the text | 3 | 35986, 35477, 35398 |
| field differs from the sizes in the text; implausibly large for a home | 1 | 43712 |
| field set; text states no size; implausibly small for sq ft | 1 | 40016 |
| field set; text states no size; implausibly large for a home | 1 | 31842 |

Wrong or doubtful values, as printed today:

| ID | Field (shown as ft²) | Sizes in the text | Verdict |
| --- | ---: | --- | --- |
| 43712 | 260,000 | 200 sqm, 260 sqm | field differs from the sizes in the text; implausibly large for a home |
| 43543 | 232 | 230 m² | field holds the sq m number (shown as ft²) |
| 43450 | 254 | 254 sqm | field holds the sq m number (shown as ft²) |
| 40248 | 120 | 120 sqm | field holds the sq m number (shown as ft²) |
| 40037 | 40 | 40 sqm | field holds the sq m number (shown as ft²) |
| 40016 | 110 | (none) | field set; text states no size; implausibly small for sq ft |
| 36410 | 141.5 | 141.5 m², 147 m² | field holds the sq m number (shown as ft²) |
| 35986 | 19,450 | 10,000 sqm, 808 sqm | field differs from the sizes in the text |
| 35477 | 1,022 | 53 sqm | field differs from the sizes in the text |
| 35398 | 560 | 57 / 62 sqm, 80.5 sqm | field differs from the sizes in the text |
| 31842 | 21,778 | (none) | field set; text states no size; implausibly large for a home |

**Units in the text.** 34 descriptions give sizes in sq m only, 33 in both, 16 in sq ft only (mostly commercial), 46 give no size.
Spellings: `sqm` 361, `sq ft` 240, `sq. ft.` 178, `sqft` 71, `sq.ft` 35, `m²` 19, `sqm.` 16, `sq. m` 13, `sq. ft` 12, `sq ft.` 10, `sq.m` 5, `square metres` 5.
So homes are described in square metres ("Approx. 162 sq. m (about 1,750 sq. ft)", 45577) while the field, when set, is in square feet;
20 listings have a size in the text and nothing in the field, and 33 have neither.

### 5.2 Bedrooms and bathrooms

Homes by the bedrooms field: 0 bed: 7, 1 bed: 18, 2 bed: 35, 3 bed: 33, 4 bed: 7, 5 bed: 2.

- **Zero bedrooms on a home** (7): four are studios (below); the other three are data gaps: 43656 (home with bedrooms field 0 (title says 2 bedroom)); 43523 (home with bedrooms field 0 (title says 2 bedroom)); 31842 (home with bedrooms field 0).
- **Zero bedrooms on commercial**: 25 of 27, as expected. But two offices carry a bedroom count: 36675 (bedrooms 2); 34906 (bedrooms 1). Never print bedrooms on a commercial listing.
- **Listings for several sizes** put one number in the field: the largest size on 8 and the smallest on 4 (section 7).
- Bathrooms are 0 on three homes (43656, 43523, 31842). The "Rooms" field is set on 94 listings (45577: 7 rooms for a three-bedroom); it is a count no tenant asks for.

### 5.3 Studios

| ID | Title | Studio category | Bedrooms field | Bathrooms | Size field |
| --- | --- | --- | ---: | ---: | ---: |
| 39456 | Padmore Residences Kilimani \| Studio, 1 & 2 Bedroom Apartment… | no | 2 | 2 | 914 |
| 39421 | Alba Apartments Riruta \| Studio, 1 & 2 Bed Units to Rent from… | no | 2 | 2 | 657 |
| 29531 | Fully Furnished & Serviced Studio For Rent in Kilimani | yes | 0 | 1 | 431 |
| 29489 | The Ultimate Executive Studio: 10th-Floor Serviced Living in… | yes | 0 | 1 | (empty) |
| 29475 | Fully Furnished & Serviced Premium Studio Apartment for Rent… | yes | 0 | 1 | 431 |
| 29462 | Furnished Executive Studio for Rent \| Kilimani | yes | 0 | 1 | (empty) |

The four single studios are filed under the Studio category with bedrooms 0 and bathrooms 1: "0 bedrooms" must read "Studio". The two listings that
offer a studio among other sizes (39456, 39421) are not in the Studio category and carry bedrooms 2.

## 6. Kinds: home, commercial, other

By what the title says: **102 homes, 27 commercial, 0 other**. Category sets as stored: Apartments + Residential (90), Commercial + Offices (19), Commercial + Warehouses (8), Houses + Residential (4), Apartments + Residential + Studio (4), (none) (2), Residential + Villas (2).
Category slugs seen: `apartments`, `commercial`, `houses-residential`, `offices`, `residential`, `studio-apartments`, `villas-residential`, `warehouses`.
There is no category for retail or shops on any rental, and no "other" kind (no land, no hotel).
By title words the commercial listings are: offices 17, offices with retail or showrooms 2, retail only 1, warehouses or godowns 7.

The category rule of the development snippet (`blockke_area_kind()`, `ref/dev-v1.9.1.php:11360-11386`: commercial when a commercial category is set
and no home category with bedrooms; otherwise a home) gets these wrong or incomplete:

| ID | Categories | Title | Problem | Category rule says |
| --- | --- | --- | --- | --- |
| 45228 | Commercial + Warehouses | Purple Tower, Mombasa Road \| Grade A Offices to Buy or Lease in Nairo… | category Warehouses, title says offices | commercial |
| 43656 | (none) | Apartments \| 2 Bedroom Apartments for Rent on Lantana Road, Westlands | no category | home |
| 39038 | Commercial + Offices | Museum Station, Westlands \| Retail & Office Spaces To Let, Muthithi R… | category Offices only, title says retail/showroom and offices | commercial |
| 37551 | Commercial + Offices | 1, 2 & 3 Bedroom Apartments for Rent at Malkya Heights, Westlands — F… | category says commercial, title says home | commercial |
| 36826 | Commercial + Offices | Hookwood Square, Westlands — Retail Space to Let from KES 200 per Sq… | category Offices, title says retail (the site has no retail category on rentals) | commercial |
| 36814 | (none) | Skypark, Parklands Road, Westlands — Grade A Offices to Let from KES… | no category | home |
| 36739 | Commercial + Offices | Muthaiga Business Centre – Offices, Showrooms & Storage To Let | category Offices only, title says retail/showroom and offices | commercial |

37551 (fixture) is the clear case: three sizes of apartments, bedrooms 3, a "From" monthly rent, filed under Commercial/Offices. 36814 is the reverse:
an office building priced per sq ft with no category at all, which the category rule would show as a home. 43656 has no category, no price, no bedrooms,
no agent, no features and no pin; only its title, text and photos say what it is.

## 7. Listings that stand for several homes or spaces

52 listings (40%) show at least one sign that they are not one home at one rent:

**(a) The title names several sizes** (12):

| ID | Title | Sizes in the title | Bedrooms field | Price field | "From" label | Unit table |
| --- | --- | --- | ---: | ---: | --- | --- |
| 39456 | Padmore Residences Kilimani \| Studio, 1 & 2 Bedroom Apartments… | studio/1/2 | 2 | 60,000 | no | no |
| 39421 | Alba Apartments Riruta \| Studio, 1 & 2 Bed Units to Rent from K… | studio/1/2 | 2 | 30,000 | no | yes |
| 37551 | 1, 2 & 3 Bedroom Apartments for Rent at Malkya Heights, Westlan… | 1/2/3 | 3 | 80,000 | yes | yes |
| 36499 | 2 & 3 Bedroom Apartments for Rent in Syokimau \| Taurus Apartmen… | 2/3 | 2 | 60,000 | no | no |
| 36220 | Marina Bay, Sports Road, Westlands — 1 and 2 Bedroom Apartments… | 1/2 | 1 | 85,000 | yes | yes |
| 35945 | Kindaruma Homes Kilimani \| Apartments to Rent (1–4 Bed) | 1/2/3/4 | 4 | 55,000 | no | yes |
| 35609 | The Colosseum Residences, Parklands \| 2 & 3 Bedroom Apartments… | 2/3 | 3 | 0 | no | no |
| 35550 | Silver Terraces, Rhapta Road, Westlands — 2 and 3 Bedroom Apart… | 2/3 | 2 | 150,000 | yes | no |
| 35498 | City Oasis Gardens, Valley Arcade \| 1, 2 & 3 Bedroom Apartments… | 1/2/3 | 3 | 65,000 | no | yes |
| 35477 | Golden Mango Heights, Kilimani \| 1 & 2 Bedroom Apartments for R… | 1/2 | 2 | 60,000 | no | no |
| 35398 | Brickford Heights, Kilimani \| 1 & 2 Bed Apartments to Rent | 1/2 | 1 | 65,000 | no | yes |
| 34301 | Meteor Garden Apartments, Kilimani \| 1–3 Bed Residences | 1/2/3 | 3 | 65,000 | no | yes |

**(b) One size, several homes**: the title is plural ("3 Bedroom Apartments for Rent ...", "5-Bedroom Villas") on 13 homes: 43712, 43656, 42340, 41690, 40248, 39341, 39276, 39066, 37243, 36506, 36502, 36410, 35583.

**(c) Commercial buildings let in parts** ("Offices to Let from 4,000 Sq Ft", "Warehouses to Let"): 13 listings: 45228, 40275, 39038, 36834, 36822, 36816, 36814, 36809, 36739, 36731, 36636, 35986, 35427.

**(d) A table of units or sizes with rents** (two rows or more) in 18 descriptions: 43712, 43656, 43644, 39421, 39341, 39066, 37551, 37243, 36834, 36826, 36822, 36809, 36731, 36410, 36220, 35945, 35427, 34301. Column headers vary:

| ID | Header row | Rows | First cells |
| --- | --- | ---: | --- |
| 43712 | Unit Type \| Size \| Unfurnished Rent (KES per month) | 3 | 3-Bedroom (conventional, 2 rooms ensuite); 3-Bedroom; 3-Bed… |
| 43656 | Unit Type \| Layout \| Rent per Month \| Notes | 3 | 2-Bedroom (Deluxe); 2-Bedroom (Regular); 1-Bedroom |
| 43644 | Unit Type \| Layout \| Rent per Month \| Notes | 2 | 2-Bedroom (featured); 1-Bedroom |
| 39421 | Unit Type \| Size \| Monthly Rent | 4 | Studio; Junior 1 Bedroom; Standard 1 Bedroom |
| 39341 | Floor Range \| Monthly Rent | 2 | Floors 1–10; Floors 11 and above |
| 39066 | Layout \| Size \| Rent \| Service Charge | 2 | 3 Bedroom + DSQ; 3 Bedroom + DSQ |
| 37551 | Unit type \| Layout \| Rent from | 3 | 1 bedroom; 2 bedroom; 3 bedroom |
| 37243 | Option \| Indicative Rent | 2 | Unfurnished 3 Bedroom; Furnished 3 Bedroom |
| 36834 | Office Size \| Monthly Rent \| Service Charge | 4 | 1,800 sq. ft.; 3,500 sq. ft.; 6,400 sq. ft. |
| 36826 | Floor \| Base Rent Per Sq. Ft. \| Service Charge Per Sq. Ft. \| Indicative Combined Rate | 3 | Ground Floor; First Floor; Second Floor |
| 36822 | Office Size \| Rental Calculation | 5 | 1,230 sq. ft.; 2,000 sq. ft.; 3,000 sq. ft. |
| 36809 | Office \| Monthly Rent \| Service Charge \| Parking | 2 | Third Floor Suite (3,706.95 sq. ft.); First Floor, Unit C (… |
| 36731 | Office Size \| Rent at KES 65 psf \| Service Charge at KES 18 psf \| Total Before VAT & Parking | 2 | 2,700 sq ft; 4,200 sq ft |
| 36410 | Apartment Size \| Approximate Size in Sq. Ft. \| Layout Details \| Monthly Rent | 5 | 141.5 m²; 142 m²; 143 m² |
| 36220 | Home \| Rent — Unfurnished \| Rent — Furnished | 2 | 1 Bedroom Apartment; 2 Bedroom Apartment |
| 35945 | Unit type \| Approx. size \| Indicative monthly rent | 5 | 1 Bedroom (with balcony); 1 Bedroom + Study; 2 Bedroom (mas… |
| 35427 | Office Size \| Rent at KES 120 per sq. ft. \| Service Charge at KES 29 per sq. ft. \| Monthly Total Before VAT | 2 | 3,500 sq. ft.; 7,180 sq. ft. |
| 34301 | Unit type \| Size (indicative) \| Indicative sale guide \| Indicative rent guide | 4 | 1 Bedroom; 2 Bedroom; 2 Bedroom + DSQ |

**(e) The rent is a starting figure** on 39 listings (section 1.1).

**(f) The theme's own unit links** ("Other units in ...", `ref/pages-final/42450.html:4328-4365`) appear on 5 rentals:

| ID | Role | Parent | Units the block lists |
| --- | --- | --- | --- |
| 42450 | sub-unit | 31509 (For Sale, 2 & 3 Bedroom Apartments for Sale in Syokimau \| S…) | 2 Bedroom Apartment for Rent in Syokima… — KES 60,000 per month |
| 42449 | sub-unit | 31509 (For Sale, 2 & 3 Bedroom Apartments for Sale in Syokimau \| S…) | 3 Bedroom Apartment for Rent in Syokima… — KES 80,000 per month |
| 36135 | sub-unit | 35986 (For Rent, The Link Logistics Park, Tatu City \| Warehouses t…) | 808 sqm Warehouse to Let at The Link Lo… — no price |
| 36136 | sub-unit | 35986 (For Rent, The Link Logistics Park, Tatu City \| Warehouses t…) | 999 sqm Corner Warehouse to Let at The… — no price |
| 35986 | master | — | 999 sqm Corner Warehouse to Let at The… — no price; 808 sqm Warehouse to Let at The Link Lo… — no price |

42450 and 42449 are the only units of a For Sale listing (31509), so each says "Other units in 2 & 3 Bedroom Apartments for Sale ..." and links to its
sibling; a rental page must not take its name, price or wording from that parent. 35986 is a rental master whose two units have no price.
The Taurus listings (36499 "2 & 3 Bedroom", 36506, 36502) are not linked this way at all; they share only the project term.

## 8. Other rentals in the same project or building

94 rentals carry a project term (`property_project`; 68 different terms), 35 carry none.
**18 projects have two or more rent-only listings, covering 44 listings (34%)**. These could carry "Other homes to rent in this building" today:

| Project term | Rentals | The rent-only listings (bedrooms, price field) | Other listings with the term |
| --- | ---: | --- | --- |
| Versailles Garden | 5 | 44050 (3 bed, 120,000); 44048 (1 bed, 70,000); 42592 (3 bed, 120,000); 42591 (2 bed, 90,000); 42590 (1 bed, 70,000) | 33346 (For Sale) |
| Meteor Garden Apartments | 4 | 34336 (3 bed, 120,000); 34335 (2 bed, 85,000); 34334 (1 bed, 65,000); 34301 (3 bed, 65,000) | — |
| Greenville Gardens | 3 | 42783 (3 bed, 160,000); 42781 (2 bed, 120,000); 42755 (1 bed, 90,000) | 31953 (For Sale) |
| Taurus Apartments | 3 | 36506 (2 bed, 60,000); 36502 (3 bed, 80,000); 36499 (2 bed, 60,000) | — |
| Vienna Gardens | 3 | 44260 (3 bed, 130,000); 44259 (2 bed, 100,000); 44258 (1 bed, 70,000) | — |
| Grandview Gardens | 2 | 43450 (4 bed, 200,000); 37206 (3 bed, 150,000) | — |
| Kings Courtyard | 2 | 45577 (3 bed, 135,000); 45538 (2 bed, 100,000) | — |
| Loresho Oasis | 2 | 43856 (3 bed, 100,000); 43855 (2 bed, 80,000) | — |
| Oak Classic | 2 | 44748 (1 bed, 75,000); 44747 (2 bed, 110,000) | — |
| Purple Tower | 2 | 45228 (no beds, no price); 36662 (no beds, 110) | — |
| Riara One | 2 | 44020 (2 bed, 6,500); 43995 (2 bed, 110,000) | — |
| Samar Residency | 2 | 37243 (3 bed, 190,000); 34926 (3 bed, 185,000) | — |
| Silver Terraces | 2 | 42260 (2 bed, 230,000); 35550 (2 bed, 150,000) | — |
| Sky Valley | 2 | 43803 (1 bed, 70,000); 43788 (2 bed, 100,000) | 33338 (For Sale), 33337 (For Sale), 33326 (For Sale), 33289 (For Sale) |
| Skypark Offices | 2 | 40275 (no beds, 100); 36814 (no beds, 100) | — |
| Sunpark Residence II | 2 | 42450 (3 bed, 80,000); 42449 (2 bed, 60,000) | 37198 (For Sale), 37190 (For Sale), 31509 (For Sale) |
| Urban Dreams | 2 | 44746 (2 bed, 137,000); 44745 (3 bed, 165,000) | — |
| Westlands Cottage | 2 | 43656 (no beds, no price); 43644 (2 bed, 110,000) | — |

What to watch:

- **Duplicates.** Same project, same bedrooms, same price: 44050 and 42592 (Versailles Garden); 44048 and 42590 (Versailles Garden); 40275 and 36814 (Skypark Offices); 36506 and 36499 (Taurus Apartments).
  A "same building" row would show a home next to its own copy (44048 and 42590 are already named as duplicates in `ref/README-wordpress.md:179`). In Westlands Cottage one of the
  two (43656) has no price or bedrooms.
- **A listing for several sizes sits next to its own units** (Taurus: 36499 with 36506 and 36502; Meteor Garden: 34301 with 34334 to 34336; The Link: 35986 with its units).
- **Two terms for one building**: "Loresho Oasis" (43856, 43855) and "Loresho Oasis Apartments" (43819); "Riara One" (44020, 43995) and "Riara One Apartments" (40016).
- **Buildings with no term**: by the names in titles and texts, 18 more listings belong with another rental: 43833, 43819, 43523, 43482, 42292, 42279, 41921, 41862, 40899, 40016, 37563, 36823, 36792, 36675, 36136, 36135, 35986, 35498. For example 108 Riverside (41921, 41862),
  Nivina Towers (36823, 36675), Ivory Terraces (43523 has the term; 42292, 42279 do not), The Convex (36792 and 37563), The Link units (36135, 36136).
  With those reconciled, 62 listings (48%) would have a sibling. The matching was done on names and is not proof; the fix is to set the term.
- **Terms that are not names**: "Emaar residences" on 43712 (lower-case noun in a name); "Graylands Industrial PARK" on 36636 (word in capitals); "Padmore residences" on 39456 (lower-case noun in a name); "Villa for Rent" on 42616 (reads like a listing title); "Windsor 2 bedroom" on 42370 (reads like a listing title); "Zamara building" on 34906 (lower-case noun in a name).
  A project term cannot be printed as a building name without a check.
- **Sale siblings.** 10 single-rental projects also hold For Sale listings (for example Pinecrest View Apartments: 44422, Diamond Ivy: 44339, Aramis Apartments: 41946), and Bahari Homes Residency holds three listings tagged both For Rent and For Sale
  (33933, 33932, 33909), which are not rentals for this project. A "same building" query must filter on rent-only, not on the project alone.
- The fixture pair works as intended: Kings Courtyard has 45577 (3 bed, KES 135,000) and 45538 (2 bed, KES 100,000), nothing else.

## 9. Areas: where a "similar rentals" row has enough to show

Area terms on the rentals (a listing with two areas is counted in both). "Fewest others" is the smallest number of other rentals of the same kind that any
listing in the area can be shown, after leaving out its own project (the copies and sister units of section 8):

| Area | Homes to rent | Fewest others | Row of three similar homes | Commercial to rent | Fewest others | Row of three similar spaces |
| --- | ---: | ---: | --- | ---: | ---: | --- |
| Westlands | 32 | 29 | enough for every listing | 14 | 12 | enough for every listing |
| Kilimani | 30 | 26 | enough for every listing | 1 | 0 | none |
| Kileleshwa | 11 | 7 | enough for every listing |  |  | — |
| Lavington | 6 | 4 | enough for every listing |  |  | — |
| Syokimau | 6 | 3 | enough for every listing |  |  | — |
| General Mathenge | 5 | 2 | enough for 2 of 5 |  |  | — |
| Loresho | 4 | 2 | enough for 2 of 4 |  |  | — |
| Parklands | 4 | 2 | enough for 2 of 4 |  |  | — |
| Mombasa Road |  |  | — | 3 | 1 | too few (1 or 2) |
| Runda | 3 | 2 | too few (1 or 2) |  |  | — |
| Riverside | 2 | 1 | too few (1) |  |  | — |
| Tatu City |  |  | — | 2 | 1 | too few (1) |
| Athi River |  |  | — | 1 | 0 | none |
| Brookside | 1 | 0 | none |  |  | — |
| Industrial Area |  |  | — | 1 | 0 | none |
| Kitisuru | 1 | 0 | none |  |  | — |
| Muthaiga |  |  | — | 1 | 0 | none |
| Riruta | 1 | 0 | none |  |  | — |
| Rosslyn | 1 | 0 | none |  |  | — |
| Thika | 1 | 0 | none |  |  | — |
| Upperhill |  |  | — | 1 | 0 | none |

**Enough for a row of three:** homes in Westlands, Kilimani, Kileleshwa, Lavington, Syokimau; commercial in Westlands only.
**Enough for some listings only:** homes in General Mathenge, Loresho, Parklands (four or five listings from two or three projects: a listing there has two or three others).
**Too few:** homes in Runda, Riverside, Brookside, Kitisuru, Riruta, Rosslyn, Thika; commercial in Kilimani, Mombasa Road, Tatu City, Athi River, Industrial Area, Muthaiga, Upperhill.

Counting across all the areas of a listing: 104 listings (90 homes, 14 commercial) have three or more candidates, 12 have one or two (45231, 45228, 43856, 43855, 43543, 43450, 42616, 37206, 36662, 36136, 35986, 35583) and 13 have none (42370, 39421, 37494, 36834, 36822, 36792, 36739, 36636, 36135, 36038, 33776, 31842, 29501).
Asking also for bedrooms within one of the listing's own leaves 101 with three or more. Widening to the same city gives three or more to 120 listings;
6 have none even then (45507, 43819, 39341, 37494, 36792, 36636), mostly because they have no city term. The three Runda homes rent at KES 200,000, 650,000 and 710,490, so "similar" by area alone can be far apart in rent.

Area data problems: no area term on 42370, 36792, 36135, 36038; no city on 45507, 43819, 39341, 37494, 36792; two area terms on 7 listings (General Mathenge + Westlands, Riverside + Westlands, Brookside + Westlands);
the title names an area the term does not on 11: 45367 (title Riverside, term Westlands); 45318 (title Riverside, term Westlands); 44135 (title Riverside, term Westlands); 44048 (title Kileleshwa, term Lavington); 43583 (title General Mathenge, term Westlands); 41921 (title Riverside, term Westlands); 37563 (title Riverside, term Westlands); 36792 (title Riverside, term none); 36135 (title Tatu City, term none); 36038 (title Mombasa Road, term none); 35498 (title Valley Arcade, term Kilimani).
The "city" term is a county for 14 listings (Kiambu 7, Machakos 7), so "Tatu City, Kiambu" and "Syokimau, Machakos" are what a place line would read.

The theme's own "Similar Listings" block shows 6 cards on 89 rental pages and none on 11; on 17 pages it includes For Sale listings (mostly on commercial
rentals: 42370, 40298, 40275, 39038, 37563, 37551, 36826, 36823, 36816, 36809, 36792, 36731 and 5 more).

## 10. Data problems the page must survive

Counted over the 129 listings. "IDs" lists every listing when there are few, otherwise examples; the JSON has all of them
(`listings[].edge_flags` and `summary.oddities.by_code`).

### 10.1 In the fields

| Problem | Listings | IDs | Note |
| --- | ---: | --- | --- |
| No price | 9 | 45228, 43656, 36816, 36136, 36135, 35986, 35609, 35437, 34906 | reads "Rent on request" (one reads in USD) |
| Price without an after-label | 6 | 45318, 44746, 44048, 42591, 42590, 35398 | reads "a month" |
| Rent is a starting figure, no "From" label | 28 | 44048, 43803, 43788, 43712, 43656, 42591, 42590, 42340 and 20 more | a single figure would understate the range |
| Stray "from" on the unused second price | 54 | 44422, 42783, 42781, 42755, 42616, 42592 and 48 more | must not be read as the listing's "From" |
| Rent stated in US dollars in title or text | 7 | 44533, 40298, 36816, 36792, 35583, 34171, 29501 | price field is a dated conversion, or empty |
| Price in the title | 41 | 45367, 45318, 44422, 43856, 43855, 43583 and 35 more | rent printed twice; three titles in USD |
| No category | 2 | 43656, 36814 | 36814 is an office building; 43656 is a block of flats |
| Category contradicts the title | 5 | 45228, 39038, 37551, 36826, 36739 | 37551: apartments filed as offices |
| Home with bedrooms 0 (not a studio) | 3 | 43656, 43523, 31842 | the title of two says 2 bedroom |
| Studio stored as bedrooms 0 | 4 | 29531, 29489, 29475, 29462 | must read "Studio" |
| Commercial with a bedroom count | 2 | 36675, 34906 | never print bedrooms on commercial |
| Several sizes, one bedrooms number | 12 | 39456, 39421, 37551, 36499, 36220, 35945 and 6 more | largest on 8, smallest on 4 |
| Size field empty | 53 | (53 listings) | 20 of them state a size in the text |
| Size field in the wrong unit or implausible | 8 | 43712, 43543, 43450, 40248, 40037, 40016, 36410, 31842 | "232 ft²" for a four-bedroom townhouse; "260,000 ft²" |
| No photo at all | 2 | 45231, 45228 | no featured image either |
| One photo | 10 | 37258, 36834, 36816, 36814, 36809, 36792, 36731, 36506, 34906, 34171 |  |
| First photo portrait or square | 23 | 43819, 41690, 40248, 40037, 40016, 39456 and 13 more |  |
| Lead image under 900 px wide | 10 | 34906, 36816, 36822, 36506, 36502, 36499, 39341, 36814, 35427, 37494 | smallest 415 px |
| No features | 24 | 44533, 43819, 43656, 43523, 40490, 37563, 37494, 36826 and 16 more | 17 commercial, 7 homes |
| No area / no city | 4 / 5 | 42370, 36792, 36135, 36038 / 45507, 43819, 39341, 37494, 36792 |  |
| Two area terms | 7 | 44745, 42783, 42781, 42755, 41862, 41562, 37243 |  |
| Title names another area than the term | 11 | 45367, 45318, 44135, 44048, 43583, 41921 and 5 more | 44048: title Kileleshwa, term Lavington |
| No map pin | 8 | 44533, 44258, 44135, 43833, 43819, 43656, 43543, 43450 |  |
| Pin far from Nairobi | 5 | 45231, 45228, 44745, 36038, 34926 | three on the Mombasa highway near Tsavo, one in Mombasa, one at the centre of Kenya |
| Pin is a neighbourhood point | 104 | (most listings) | no distance can be measured from it |
| No agent | 1 | 43656 | falls back to Block's number |
| No status term | 15 | 45507, 44258, 43819, 43788, 43656, 43523 and 9 more |  |
| Status "Off-Plan / Ongoing" on a rental | 1 | 33230 | its text says "available to let" |
| No project term | 35 | 43833, 42340, 42292, 42279, 41921, 41862 and 29 more |  |
| Project term that is not a name | 6 | 43712, 36636, 39456, 42616, 42370, 34906 | "Villa for Rent", "Windsor 2 bedroom" |
| Probable duplicate of another rental | 8 | 44050, 44048, 42592, 42590, 40275, 36814, 36506, 36499 |  |
| Title does not say rent, let or lease | 11 | 45231, 44020, 36636, 34336, 34335, 34334, 34301, 29501, 29489, 29419, 28919 |  |
| Title offers sale and rent | 3 | 45228, 37243, 36662 |  |
| Title longer than 80 characters | 15 | 43543, 43450, 42783, 39066, 37551, 37206 and 9 more | longest 116 characters (31842) |
| Title untidy | 9 | 28919 (double space); 33776 (ends with "\| Block"); 43803, 31842 (ends with a bare "\|"); 37494 (ends with a full stop); 42370 (spelling: "Nothern"); 43656, 43644 (starts with "Apartments \|"); 42340 (starts with the area name and a bar) |  |

The feed's `fe` list mixes the names of the feature groups ("Building & Parking", "Security & Access") with the features on 53 listings; the page groups them properly.

### 10.2 In the markup of the text

| Problem | Listings | IDs |
| --- | ---: | --- |
| No heading tags (bold paragraphs or bold first lines act as headings) | 6 | 44746, 44745, 44533, 44020, 43995, 31842 |
| One title heading above the section level | 19 | 45577, 45538, 45507, 45367, 45318, 45231, 45228, 44748 and 11 more |
| H1 tags inside the description | 3 | 37563, 37258, 33776 |
| Sections at H4, or at H1 | 4, 2 | 43819, 43803, 43788, 43712; 37563, 37258 |
| A script element holding RealEstateListing JSON, with no type attribute | 17 | 37551, 37099, 36834, 36826, 36823, 36816, 36814, 36809, 36731, 36675, 36220, 35550, 35437, 35427, 34906, 34171, 33230 |
| Gutenberg block comment printed as an HTML comment, and an empty H3 | 1 | 36834 |
| Rank Math FAQ block (`div.rank-math-block`, questions as H4, answers in a div) | 17 | 37551, 37099, 36834, 36826, 36823, 36816 and 11 more |
| Tables with three or four columns | 37 | 45507, 45228, 44748, 44747, 44050, 44048, 43819, 43803 and 29 more; four columns on 14 |
| PDF embed block (`wp-block-pdfemb-pdf-embedder-viewer`) | 2 | 35986, 35583 |
| First paragraph starts with a line break | 24 | 45318, 45228, 44748, 44747, 44746, 44533 and 18 more |
| Empty paragraphs | 8 | 43543, 43450, 40899, 36739, 34926, 31842, 29419, 28868 |
| Bold markup inside headings; a bold run that stops mid-word | 13; 2 | 45577, 45538, 45507, 41562, 29531 and 8 more; 45538, 29419 |
| A whole sentence set as a heading | 1 | 43656 |

**The script element is the dangerous one.** On 17 listings the text holds, near its end, `<script>{"@context":"https://schema.org","@type":"RealEstateListing",...}</script>`
(`ref/pages-final/34171.html:4460`). In the saved pages the tag has no `type` attribute, so a browser treats it as script, not as data. `wp_kses_post()`
removes the tags of a script element but keeps what is between them, so a page that prints the description through it would show the JSON as text.
The same descriptions hold "Listing updated: 14 August 2026 · Verified by Block Real Estate" as a paragraph, and the prices in the JSON equal the price field.

### 10.3 In the words of the text

| Problem | Listings | IDs and examples |
| --- | ---: | --- |
| First paragraph is the SEO description again | 60 | identical on 38; with the title glued on 18; cut short on 2 (44259 "...Enquire wi", 43523 "...per mont"); 44258 ends "...Block Real EstateB" |
| Title repeated as a plain paragraph near the top | 20 | 44746, 44745, 44533, 44260, 44259, 44258, 44048, 44020 and 12 more (44020: cut off, "...Riara Road, Lavingto") |
| Two texts glued without a space | 17 | 43482 "...Block Real Estate.Jamesborough Residences, ..."; 42260 "...lifestyle districts.Project Snapshot" (the heading is lost in the paragraph) |
| Text cut off mid-word | 4 | 44259, 43523 (first paragraph); 44020 (title line); 39421 (last line: "...at Alba Apartme") |
| Template placeholders | 1 | 43583: "[Development Name]" and "[Location]", six times, also in its snapshot table |
| Markdown left in the text | 2 | 31842 (a Markdown table as paragraphs, "[block.ke](https://block.ke)"); 28919 ("[+254725937686]") |
| Drafting notes: the text talks about its own source | 12 | 37258, 37206, 36816, 36809, 36792, 36731, 36662, 36135, 35477, 35437, 35427, 34301. E.g. 37258 "The supplied property information indicates three bathrooms." |
| Notes to the editor | 3 | 36816 "The official project name should be confirmed before publication ..."; 36135 "... references to both 999 sqm and 909 sqm"; 36809 "... does not reconcile with the unit quotations below" |
| Sale-template headings still in place | 9 | 45228, 39341, 37243, 36662, 36636, 34336, 34335, 34334, 34301: Payment Plan (6), Why It Works for Investors (5), Why It Works for Homeowners (4), Buying from Abroad (3), Pricing & Payment (3) |
| Sale wording in the body (five or more hits) | 8 | 45228, 39341, 37243, 36662, 36636, 36220, 34334, 34301; "for sale" appears in 7 texts, "price list" in 6, "investors" in 8 |
| Links to For Sale listings | 6 | 36826, 36816, 36814, 36809, 36220, 34906 (13 links); 29531 ends "Browse more apartments for sale in Kilimani." |
| Links to listings that are not in the feed | 6 | 37551, 36834, 36826, 36816, 36814, 36809: two URLs, possibly gone (section 15) |
| A contact block closes the text | 96 | the phone is written six ways ("0725 937686" 49, "0725 937 686" 41, "+254 725 937 686" 9, "0725937686" 8, "+254 725 937686" 5, "+254725937686" 2); 37258 gives a second number, +254 784 848 993 |
| Three office addresses |  | "Upperhill, Nairobi" in 64 texts, "14th Floor, GTC, The Spaces, Westlands" in 4 texts, "Upper Hill Gardens, Suite D15" in 4 texts |
| A named person signs off | 12 | 39066, 37258, 34926, 29531, 29501, 29489 and 6 more ("Loyd from Block", "Speak to Loyd at Block Real Estate") |
| Emoji | 33 | mostly in the contact block (📞 📍 📧 🌐) |
| "Listing updated: ... · Verified by Block Real Estate" | 17 | 14 August 2026 on 13, 9 August 2026 on 4 |
| An availability date that has passed or has no year | 4 | 40490, 36135, 36136, 35986 (section 2.5) |
| Very long or very short |  | over 2,000 words: 37258, 37243, 36834, 36826, 36822, 36816 and 9 more; under 600: 42370, 40037, 40016, 29489, 29419, 28919, 28868 |

E-mail addresses read "[email protected]" in the saved pages of 62 listings because Cloudflare rewrites them on the way out; the database holds the real address.

## 11. What the text is built from

### 11.1 Length

Words per description: minimum 282, median 1091, 90th percentile 2051, maximum 3729 (36826). Homes: median 964; commercial: median 1953.
Blocks in all 129 texts: 4157 paragraphs, 2281 headings, 721 lists, 302 tables, 17 Rank Math FAQ blocks, 6 rules, 3 quotes, 2 PDF embeds.
**No description contains an image, a video or an iframe.**

### 11.2 The snapshot table

119 listings (92%) have one: a table in 108 and "Label: value" bullets in 11. Missing on 31842, 29531, 29501, 29489, 29475, 29462, 29436, 29419, 28919, 28868. On 42260 the table is there but its heading is glued into the
paragraph before it. Four commercial listings have two (a "Building at a Glance" list and a "Project Snapshot" table): 36826, 36816, 36814, 36809.
Header rows: `Detail | Information` 66, `Feature | Details` 23, `(two empty cells)` 18, `(none)` 12.
Rows per snapshot: 5 to 27, median 10. There are 329 different labels; the common ones:

| Label | Listings | Example value | Label | Listings | Example value |
| --- | ---: | --- | --- | ---: | --- |
| location | 117 | Off Lenana Road, Kilimani, Nairobi | tenure | 19 | Rental tenancy |
| development | 65 | Kings Courtyard | listed by | 12 | Block Real Estate, Upperhill |
| property type | 53 | 4-bedroom duplex apartments | development name | 11 | Neptune Residency |
| parking | 53 | 2 parking spaces per unit | available space | 11 | 2,500 sq ft |
| rent | 51 | KES 135,000 per month, service charge i… | water | 10 | Dedicated borehole |
| availability | 48 | Available now | developer | 10 | Purple Dot International Ltd |
| size | 36 | Approx. 162 sq. m (about 1,750 sq. ft) | fit-out status | 10 | Fitted / partitioned office accommodati… |
| status | 35 | Available for rent | listing type | 9 | For rent |
| monthly rent | 34 | KES 250,000 | bathrooms | 9 | 4 |
| unit type | 30 | 2 bedroom apartment | lease terms | 9 | To be confirmed at point of enquiry |
| service charge | 24 | Inclusive in the rent | bedrooms and bathrooms | 8 | 3 bedrooms, all en-suite |
| security | 23 | Watchtowers, electric fencing, 24/7 on-… | lease term | 8 | Long-term contracts, one year and above |
| building | 22 | 10 floors, 40 units | combined rate | 8 | KSh 120 per sq ft |
| furnishing | 20 | Fully furnished and serviced | unit available | 7 | 1 bedroom apartment |
| bedrooms | 19 | 1 | approximate size | 7 | 136 sqm |

"Location" is the one row nearly every snapshot has, and it is more precise than the area term ("Off Lenana Road, Kilimani, Nairobi"). The rent row is
labelled `Rent` (51), `Monthly Rent` or `Monthly rent` (34), or by size (`2 Bedroom Rent`).

### 11.3 Tables

302 tables: 261 with two columns, 24 with three, 17 with four. 37 listings have a table of three or more columns and 14 a table of four; 19 listings have no table.
Up to 8 tables in one description. The longest cell has 195 characters (36834). 18 tables have a header row of empty cells and 12 have no header row at all;
in some of the latter the first body row is the header ("Item | Details" on 36792, "Payment Item | Details" on 36662).

### 11.4 Amenities, Location Advantage, Who it suits

| Section | Listings | How it is written (listings) | Bullets per listing | Longest bullet (words) |
| --- | ---: | --- | --- | ---: |
| Amenities | 121 | list 62; grouped lists 24; table 20; sub-headings + paragraphs 11; paragraphs only 4 | 3 to 29, median 10 (86 listings) | 29 |
| Location | 129 | list 64; paragraphs only 41; table 14; grouped lists 10 | 2 to 25, median 5 (74 listings) | 41 |
| Who it suits | 120 | list 106; grouped lists 7; sub-headings + paragraphs 5; paragraphs only 2 | 3 to 23, median 5 (113 listings) | 27 |

- **Amenities.** A plain list on 62 listings; lists under bold group labels on 24 (labels such as "Security", "Comfort and utilities", "Lifestyle and wellness", "Convenience and infrastructure"); a table on 20 (headers `Category | Details`, `Category | What’s Included`, `Category | Amenities`); a sub-heading per
  amenity with a paragraph under it on 11 (37099 has fifteen H4 amenity headings); prose only on 4. Missing on 31842, 29531, 29501, 29489, 29475, 29462, 29436, 29419.
- **Location.** A list on 74 listings, prose only on 41, a table on 14 (headers `Nearby | Why it matters`, `Destination | Why it matters`, `Destination | Distance`). On 28 listings the bullets start with a bold label
  ("Healthcare" 10, "Shopping" 7, "Operating hours" 6, "Schools" 6, "Transport" 4, "Education" 3). Only 31 listings give a distance or a travel time as a figure; the rest describe nearness in words.
- **Who it suits.** A list on 113 listings (3 to 23 bullets, median 5); a sub-heading per audience on 5 (37563, 37258, 37243, 37099, 36731); prose on 35583, 31842. 13 add who it suits less well
  ("May Not Be Ideal For", "May Not Suit Tenants Who Need"): 42450, 42449, 37206, 36823, 36792, 36675, 36662, 35498, 35477, 35437, 35427, 34926, 33230. Missing on 29531, 29501, 29489, 29475, 29462, 29436, 29419, 28919, 28868.

### 11.5 FAQ

127 listings have an FAQ; 42370, 31842 have none. 1098 questions in all; per listing 3 to 24, median 8 (more than 15 on 37258, 36826, 36822, 36816, 36814, 36809, 36792). Answers: median 17 words, longest 76.
The heading is "FAQ" (71) or "Frequently Asked Questions" (55). Four formats:

| Format | Listings | Markup | Examples |
| --- | ---: | --- | --- |
| Bold question, line break, answer, in one paragraph | 56 | `<p><strong>Q?</strong><br>Answer</p>` | 45577, 45538, 45507, 45367, 45318 and 7 more |
| Bold question and the answer run on in one paragraph | 37 | `<p><strong>Q?</strong> Answer</p>` | 40490, 40298, 39456, 39421, 39341 and 7 more |
| Question as a heading, answer in the paragraphs after it | 26 | `<h3>Q?</h3><p>Answer</p>` (H2 on 37563 and 37258) | 43450, 42616, 41946, 41562, 40275 and 7 more |
| Rank Math FAQ block | 17 | `div.rank-math-block` > `h4.rank-math-question` + `div.rank-math-answer` | 37551, 37099, 36834, 36826, 36823 and 7 more |

Combinations: bold-p+br (56), bold-p-inline (36), heading (18), heading + rank-math-block (8), rank-math-block (8), none (2), bold-p-inline + rank-math-block (1).
16 of the 17 Rank Math listings also get an FAQPage in the page head from Rank Math; 36834 does not (its block is the `wp-block-rank-math-faq-block` variant
with the block comment showing). Eight commercial listings have the Rank Math block and then a second list of heading questions. The questions asked most:
"Can i arrange a viewing?" (30), "Is the apartment furnished?" (22), "How much is the rent?" (20), "Is parking available?" (20), "How do i arrange a viewing?" (19), "How big is the apartment?" (16), "What amenities are available?" (13), "Is there parking?" (9).

### 11.6 Links, media, scripts

| Link kind | Links | Listings |
| --- | ---: | ---: |
| internal listing | 93 | 18 |
| email (Cloudflare-protected) | 73 | 61 |
| internal area | 17 | 17 |
| tel | 14 | 14 |
| internal home | 2 | 2 |
| internal file | 2 | 2 |
| external | 1 | 1 |
| empty | 1 | 1 |

65 descriptions contain no link at all. The 93 links to listings sit in 18 descriptions (cross-link sections such as "Other Commercial Space in Westlands",
"More Properties in Westlands", "Also Available at Nivina Towers"): 60 go to other rent-only listings, 14 to the listing itself, 13 to For Sale listings and 6 to
two URLs that are not in the feed. Area links go to `/property_area/westlands/` (13) and `/property_area/kilimani/` (4). All `tel:` links are +254725937686.
The one external link is a Google Maps link with the real coordinates of PwC Towers (40298), which differ from its pin.
Embedded media: PDF viewer blocks on 35986, 35583, nothing else. Scripts: the JSON-LD element on 17 listings (section 10.2).

## 12. Other fields: agent, status, features, address, map pin

**Agent** (sidebar card, `ref/pages-stop/45577.html:5400-5403`; buttons `:5486-5495`):

| Agent shown | Position | Phone on the page | Listings |
| --- | --- | --- | ---: |
| Loyd Mokaya | CEO & Lead Agent | +254725937686 | 89 |
| Ricky Wekesa | Sales Executive | +254180711259 | 34 |
| Bevin Nyakinda | Sales Executive | +254180711258 | 5 |
| (none shown) |  |  | 1 |

43656 has no agent (`agent_id` 0), so it needs Block's default number. The agent of 40248 was read from the earlier copy of its page
(`ref/pages-before/40248.html`) because the later copy is cut off.

**Status terms**: Complete on 104; (none) on 15; Complete + Furnished on 5; Furnished on 4; Off-Plan / Ongoing on 1. "Furnished" is a status term, not a field.

**Features** (`ref/pages-stop/45577.html:4729-4742`): 105 listings have them, grouped under 8 headings (Building & Parking, Water & Power, Family & Convenience, Lifestyle & Recreation, Security & Access, Unit Features, Work & Connectivity, Other Features); 2 to 25 features, median 10.
Most common: Lifts (95), Backup Generator (93), Ample Parking (88), Kids Play Area (88), CCTV Surveillance (84), 24/7 Manned Security (82), Borehole Water (71), Swimming pool (69), Fitted Kitchen (68), Gym (58), Wheelchair Accessible (48), Fibre Internet Ready (47).
24 listings have none (17 commercial, 7 homes: 44533, 43819, 43656, 43523, 40490, 37494, 29501).

**Address.** The address field is filled on 23 listings (18%), with values from "Kilimani" to "Kirichwa Road, next to Quickmart Kilimani, Kilimani, Nairobi".
The address panel prints City (124), Area (125), State/County (113), Country (129) and Address (23). The "Open in Google Maps" link is built from the address and city,
so on 93 listings it is just `maps.google.com/?q=,Nairobi`.

**Map pin.** 121 listings have a latitude and longitude, 8 do not (44533, 44258, 44135, 43833, 43819, 43656, 43543, 43450). The 121 pins are only 29 different points:

| Pin | Listings on the site at this point | Rent-only | Projects |
| --- | ---: | ---: | ---: |
| -1.267500, 36.812022 | 171 | 34 | 91 |
| -1.289279, 36.786931 | 157 | 33 | 73 |
| -1.280739, 36.781686 | 61 | 11 | 28 |
| -1.259882, 36.817892 | 29 | 3 | 20 |
| -1.359227, 36.937984 | 20 | 4 | 7 |
| -1.157033, 36.887916 | 18 | 3 | 9 |

104 of the pins are points shared by three or more listings from two or more projects, i.e. the centre of a neighbourhood. Only 12 rentals have a pin of their own (44745, 42370, 40275, 39341, 37563, 37551, 36739, 36662, 34926, 33776, 31842, 29501),
the rest sit on shared points. Five pins are far outside Nairobi: 45231, 45228 and 36038 share one at -2.732, 38.217 (on the Mombasa highway, more than 200 km
south-east of the city: a geocoder's answer to "Mombasa Road"); 34926 is at -4.072, 39.671 (Mombasa); 44745 is at -0.024, 37.906 (the centre point of Kenya).
A map can show "about here" at best; a distance or a travel time must never be computed from the pin.

## 13. The three lists

### 13.1 What the page can show for at least 90% of rentals

| What | Listings | Share | But |
| --- | ---: | ---: | --- |
| Title (also the classic page's H1) and the listing URL | 129 of 129 | 100% | titles run to 116 characters and 41 contain a price |
| "For Rent" and nothing else as the Buy/Rent term | 129 of 129 | 100% | by definition of the set |
| A rent phrase by the approved rule | 129 of 129 | 100% | 8 of them read "Rent on request"; 28 are starting figures with no "From" label (section 1.1) |
| A price above zero | 120 of 129 | 93% |  |
| An after-price label | 117 of 129 | 91% | eight different strings |
| A category | 127 of 129 | 98% | not always the right one (section 6) |
| Home or commercial, decided by title words with the category as fallback | 129 of 129 | 100% | the category alone misplaces 36814 and 37551 |
| Bedrooms for a home (a number, or "Studio") | 99 of 102 | 97% | of homes; one number for several sizes on 12 |
| Bathrooms for a home | 99 of 102 | 97% | of homes |
| Area | 125 of 129 | 97% | 7 listings have two |
| City | 124 of 129 | 96% | a county name on 14 |
| At least one photo (and so a first photo and a featured image) | 127 of 129 | 98% | portrait or square on 23 |
| A map pin | 121 of 129 | 94% | a neighbourhood point on 104; wrong on 5 |
| An agent with a phone and WhatsApp number | 128 of 129 | 99% | three people; default number for 43656 |
| A description of 280 words or more | 129 of 129 | 100% | median 1091 words |
| A description with heading tags | 123 of 129 | 95% | at H2, H3, H4 or H1 |
| A snapshot table or list | 119 of 129 | 92% | with a "Location" row on 117 |
| A location section | 129 of 129 | 100% | a list on 74, prose on 41 |
| An amenities section | 121 of 129 | 94% | five different shapes |
| A "who it suits" section | 120 of 129 | 93% |  |
| An FAQ with three or more questions | 127 of 129 | 98% | four markups |
| A meta description (Rank Math) | 129 of 129 | 100% | repeated as the first paragraph on 60 |

### 13.2 What only some listings have

| What | Listings | Share | Note |
| --- | ---: | ---: | --- |
| A price with a label that names a period or a unit | 114 | 88% |  |
| A status term | 114 | 88% | "Complete" on 109 |
| A rental terms section | 114 | 88% | usually without a single stated term |
| A "who it suits" list of two or more bullets | 113 | 88% |  |
| Features (grouped) | 105 | 81% | 17 of the 27 commercial listings have none |
| Three or more other rentals of the same kind in the same area | 104 | 81% |  |
| Five or more photos | 104 | 81% |  |
| A landscape first photo | 104 | 81% |  |
| A project term | 94 | 73% | not always a usable name |
| The "Rooms" field | 94 | 73% |  |
| A size stated in the text | 83 | 64% | in sq m only on 34 |
| A size field | 76 | 59% | wrong or doubtful on 11 |
| A sign that the listing covers several homes or spaces | 52 | 40% |  |
| A service charge fact (included, or an amount) | 50 | 39% | in a row of its own on 23 |
| Another rent-only listing with the same project term | 44 | 34% | 62 once building names are reconciled |
| Rent that is a starting figure | 39 | 30% | labelled "From" on 11 |
| A furnishing signal in a row, the title, the status term or a feature | 35 | 27% | furnished on 20 |
| An availability row with a value | 28 | 22% | dated on 4 |
| An address field | 23 | 18% |  |
| A Rank Math FAQ block and JSON-LD inside the text | 17 | 13% |  |
| Per sq ft pricing | 16 | 12% | all commercial |
| No price | 9 | 7% |  |
| A rent stated in US dollars | 7 | 5% |  |
| A stated lease length | 6 | 5% |  |
| A stated deposit | 6 | 5% | two months on five, one month on one |
| A viewing row | 3 | 2% | "By appointment" on 2 |
| A rent by the day | 1 | 1% |  |

### 13.3 Edge-case checklist for testers

Fixture listings from the brief are in **bold**. Every case below exists on the live site today.

**Rent**

- [ ] A plain monthly rent with the label "per month": **45577**, **45538**, **43543**, **42616**, **40490**
- [ ] Price with no after-label: **44048**, **42590**, 45318, 44746, 42591, 35398
- [ ] Rent by the day: **44020**
- [ ] Per sq ft, label `per sqft`: **45231**, **40298**, 39038, 37563, 36038
- [ ] Per sq ft, label `per sq ft per month`: **36826**, 36823, 36814, 36731, 36675, 35427
- [ ] Per sq ft with "From": **36834**, **36826**, 36814, 36731
- [ ] Commercial priced by the month: **36809**, 36739, 36636, 33776
- [ ] No price, empty label: **43656**, **35986**, 45228, 36135, 36136, 35609
- [ ] No price, label "Rent on application": 35437, 34906
- [ ] No price, a USD figure in the label: **36816**
- [ ] USD in the title and the snapshot, KES conversion in the price field: **34171**, 35583
- [ ] USD in the snapshot only, on a text with no heading tags: 44533
- [ ] USD per sq ft in the text, KES rate in the field: **40298**, 36792
- [ ] "From" label: **37551**, **36834**, **36826**, **36809**, 39276, 37243, 36814, 36731, 36410, 36220 (+1)
- [ ] Title or text gives a range or "from", label has no "From": **44048**, **43656**, **42590**, **39421**, 43803, 43788, 43712, 42591, 42340, 40248 (+18)
- [ ] Stray "from" on the second price of a listing with one rent: **42616**, **42450**, **42449**, **41862**, **36816**, **36506**, **36502**, **36499**, **34171**, 44422 (+18)
- [ ] Title says a price but the field is empty: 35609
- [ ] Largest monthly figures (44533: KES 841,332; 35583: KES 710,490; 36809: KES 695,054): **36809**, 44533, 35583

**Kind, bedrooms, size**

- [ ] Apartments filed under Commercial/Offices: **37551**
- [ ] No category: a block of flats, an office building: **43656**, 36814
- [ ] Retail filed under Offices: **36826**, 39038, 36739
- [ ] Offices filed under Warehouses: 45228
- [ ] Commercial with a bedroom count: 36675, 34906
- [ ] Home with bedrooms 0 that is not a studio: **43656**, 43523, 31842
- [ ] Studio (bedrooms 0, Studio category): 29531, 29489, 29475, 29462
- [ ] Several sizes in one listing: **39421**, **37551**, **36499**, 39456, 36220, 35945, 35609, 35550, 35498, 35477 (+2)
- [ ] Size field holds the sq m number: **43543**, 43450, 40248, 40037, 36410
- [ ] Size field impossible (260,000; a half-acre plot): 43712, 31842
- [ ] Size in the text only (sq m and sq ft): **45577**, **45538**, 44746
- [ ] No size anywhere: **42616**, **44020**, **43656**, 41650
- [ ] Villa, house, townhouse: **42616**, **43543**, **40490**, 35583, 29501, 31842
- [ ] Warehouse or godown: **45231**, **35986**, 36636, 36136, 36135, 36038, 33776

**Photos**

- [ ] No photo and no featured image: **45231**, 45228
- [ ] One photo: **36834**, **36816**, **36809**, **36506**, **34171**, 37258, 36814, 36792, 36731, 34906
- [ ] Two to four photos: **36826**, 43450, 40899, 40275, 37563, 36823, 36822, 36739, 36675, 36662 (+3)
- [ ] More than 25 photos (59 on 44533): 44533, 44260, 44135, 42591, 29501
- [ ] Portrait first photo: **39421**, **37551**, **36816**, **36506**, 43819, 41690, 40248, 40037, 40016, 39456 (+9)
- [ ] Square first photo: **36809**, **36502**, **36499**, **34171**
- [ ] Lead image under 900 px wide (415 px on 34906): **36816**, **36506**, **36502**, **36499**, 34906, 36822, 39341, 36814, 35427, 37494
- [ ] Most photos portrait: **39421**, **37551**, 43819, 41690, 40248, 40037, 40016, 39456, 39301, 39276 (+5)
- [ ] First photo under 1080 px high (no slider-size file): **40490**, **40298**, **36834**, **36826**, **36816**, **36809**, **36506**, **36502**, **36499**, 43450 (+29)

**Place and people**

- [ ] No map pin: **43656**, **43543**, 44533, 44258, 44135, 43833, 43819, 43450
- [ ] Pin hundreds of kilometres away: **45231**, 45228, 44745, 36038, 34926
- [ ] No area term: 42370, 36792, 36135, 36038
- [ ] No city term: 45507, 43819, 39341, 37494, 36792
- [ ] Two area terms: **41862**, 44745, 42783, 42781, 42755, 41562, 37243
- [ ] Title says one area, the term another: **44048**
- [ ] City term is a county (Kiambu, Machakos): **43543**, **42450**, **42449**, **36506**, **36502**, **36499**, **35986**, 43856, 42370, 36636 (+4)
- [ ] Address field filled: **42450**, **42449**, **39421**, **36499**, 43819, 43450, 42783, 42781, 42755, 41562 (+13)
- [ ] No agent: **43656**
- [ ] Agent Ricky Wekesa: **45577**, **45538**, **45231**, **44048**, **44020**, **43583**, 45507, 45367, 45318, 45228 (+24)
- [ ] Agent Bevin Nyakinda: **43543**, 42370, 41562, 35609, 35550
- [ ] No status term: **43656**, 45507, 44258, 43819, 43788, 43523, 43453, 42292, 42279, 37494 (+5)
- [ ] Status "Furnished": 40248, 35235, 29531, 29489, 29475, 29462, 29436, 29419, 28919
- [ ] Status "Off-Plan / Ongoing": 33230
- [ ] No features: **43656**, **40490**, **36826**, **36816**, **36809**, **35986**, 44533, 43819, 43523, 37563 (+14)

**Terms**

- [ ] Deposit stated (two months; one month on 43855): 43856, 43855, 43482, 39301, 37206, 35477
- [ ] Lease length stated: 42292, 42279, 42260, 39301, 36731, 35550
- [ ] One value for deposit and lease together ("Lease terms and deposit"): 43856, 43855
- [ ] Service charge included, said inside the rent row: **45577**, **45538**, **40490**, **36809**, **34171**, 44746, 44745, 39066, 37258, 37243 (+1)
- [ ] Service charge as an amount (per month, per year, per sq ft): **43583**, 39341, 36814, 36731
- [ ] Furnished: title, row and feature: **44020**, 43482, 42260
- [ ] Furnished in the title only: 43453
- [ ] "Unfurnished" row: **43543**, **36502**, **36499**, **34171**, 41921, 41562, 37206, 37099
- [ ] Furnishing row that is a note, not a status: 43788, 43523
- [ ] Furnished and unfurnished rents side by side: 43712, 37243, 36220, 35498, 35398
- [ ] Availability row with a plain value: **42616**, 44422, 39456, 35583
- [ ] Availability row with a date that has passed or has no year: **40490**, **35986**, 36135, 36136
- [ ] Availability row that is not a fact: 41690, 36410
- [ ] Viewing row "By appointment": **43543**, 36823
- [ ] Every term "to be confirmed at point of enquiry": 43819, 43803, 43788, 43453
- [ ] No rental terms section at all: **42590**, 42592, 42591, 42370, 40037, 40016, 29531, 29501, 29489, 29475 (+5)

**Structure of the text**

- [ ] Sections at H2: **43543**, **42450**, **42449**, **40490**, **36499**, **36506**, **36502**, **39421**
- [ ] Sections at H3 under one H2 title: **45577**, **45538**, **45231**
- [ ] Sections at H3, no title heading: **41862**, **44048**, **42590**, **43583**, 43482
- [ ] Sections at H4: 43819, 43803, 43788, 43712
- [ ] Sections at H1 (12 and 24 H1s on the classic page): 37563, 37258
- [ ] No heading tags: bold paragraphs and bold first lines: **44020**, 44746, 44745, 44533, 43995, 31842
- [ ] The short five-heading pattern of the oldest listings: 29531, 29501, 29489, 29475, 29462, 29436, 29419, 28919, 28868
- [ ] No snapshot: 31842, 29531, 29501, 29489, 29475, 29462, 29436, 29419, 28919, 28868
- [ ] Snapshot table with its heading lost in the paragraph before: 42260
- [ ] Two snapshots (glance list and snapshot table): **36826**, **36816**, **36809**, 36814
- [ ] FAQ as bold question + line break + answer: **45577**, **45538**, **41862**, 43482
- [ ] FAQ as bold question running into the answer: **40490**, **39421**, **36499**, **36506**, **36502**, **35986**
- [ ] FAQ questions as headings: **42616**, **40298**, 37563, 37258
- [ ] Rank Math FAQ block: **37551**, **36834**, **36826**, **36816**, **36809**, **34171**, 37099, 36823, 36814, 36731 (+7)
- [ ] Rank Math block and a second list of heading questions: **36826**, **36816**, **36809**, 36814, 36731, 36675, 35437, 35427
- [ ] No FAQ: 42370, 31842
- [ ] 24 questions; 86 headings: 37258
- [ ] Tables with four columns: **43656**, **43583**, **36826**, **36809**, **35986**, 43644, 41690, 39066, 36731, 36410 (+4)
- [ ] Amenities as a table: **45231**, 45228, 44748, 44747, 44746, 44745, 44533, 44339
- [ ] Amenities as sub-headings with paragraphs: **42616**, 41562, 40275, 37258, 37206, 37099, 36792, 36731
- [ ] JSON-LD script inside the text: **37551**, **36834**, **36826**, **36816**, **36809**, **34171**, 37099, 36823, 36814, 36731 (+7)
- [ ] PDF embed block: **35986**, 35583
- [ ] Block comment and an empty heading: **36834**
- [ ] Longest text (3,729 words); shortest (282 words): **36826**, 42370

**Words of the text**

- [ ] "[Development Name]" and "[Location]" placeholders: **43583**
- [ ] Sale template still in place (Payment Plan, Homeowners, Investors): 45228, 39341, 37243, 36662, 36636, 34336, 34335, 34334, 34301
- [ ] First paragraph is the SEO description: **45577**, **45538**, **45231**, **44020**, **43543**, 36038
- [ ] First paragraph cut off mid-word: 44259, 43523
- [ ] Last line cut off mid-word: **39421**
- [ ] Title line cut off mid-word: **44020**
- [ ] Markdown left in the text: 31842, 28919
- [ ] Notes to the editor ("before publication", conflicting sizes): **36816**, **36809**, 36135
- [ ] A second phone number in the text: 37258
- [ ] Office address "14th Floor, GTC" or "Upper Hill Gardens, Suite D15": **35986**, 37258, 36135, 36038, 35945, 35498, 35477, 35398
- [ ] Emoji in the contact block: **43543**, **40490**, **40298**, **39421**, **36506**, **36502**, **36499**, **35986**, 44746, 44745 (+23)
- [ ] Links to For Sale listings: **36826**, **36816**, **36809**, 36814, 36220, 34906
- [ ] Links to URLs that are not in the feed: **37551**, **36834**, **36826**, **36816**, **36809**, 36814
- [ ] "Listing updated: ... Verified by Block Real Estate": **37551**, **36834**, **36826**, **36816**, **36809**, **34171**, 37099, 36823, 36814, 36731 (+7)

**Other rentals nearby**

- [ ] Two rentals in one project: **45577**, **45538**
- [ ] Five in one project, two pairs of duplicates: **44048**, **42590**, 44050, 42592, 42591
- [ ] A multi-size listing and its units, linked only by the project term: **36499**, **36506**, **36502**
- [ ] A rental master and its units, linked by the theme: **35986**, 36135, 36136
- [ ] Units whose theme parent is a For Sale listing: **42450**, **42449**
- [ ] Same building, no shared term: **41862**, **35986**, 43833, 43819, 43523, 43482, 42292, 42279, 41921, 40899 (+8)
- [ ] No project term: **41862**, **40490**, **37551**, **36809**, 43833, 42340, 42292, 42279, 41921, 41690 (+25)
- [ ] No other rental of its kind in the area: **39421**, **36834**, 42370, 37494, 36822, 36792, 36739, 36636, 36135, 36038 (+3)
- [ ] One or two others in the area: **45231**, **43543**, **42616**, **35986**, 45228, 43856, 43855, 43450, 37206, 36662 (+2)

## 14. The JSON, the tools and the fixtures

`notes/03-data-audit.json` has three keys:

- `meta`: sources, counts, caveats.
- `summary`: every distribution in this document, with IDs: `coverage`, `rent`, `terms` (per topic: `row_labels`, `concrete_rows`, `v191_rule_reads_it`, ...),
  `headings`, `description` (`snapshot`, `lists`, `faq`, `links`, `media`, `intro`), `photos`, `sizes`, `kinds`, `multi_unit`, `projects`, `areas`, `map`, `agents`,
  `status`, `features`, `oddities`.
- `listings`: 129 records. Each has the fields (`price`, `after_label`, `before_label`, `bedrooms`, `size_field_sqft`, `categories`, `area`, `project`, `agent`, ...),
  what was derived (`kind`, `rent_class`, `rent_phrase_by_v191_rule`, `first_photo`, `size_verdict`, `multi_unit`, `other_rentals_same_project`, `similar_pool`),
  `edge_flags` (plain-language list of everything unusual about the listing) and `description`: `headings` (level, text, slot), `slot_sequence`, `snapshot.rows`,
  `tables`, `rental_terms`, `lists` (amenities, location, suits), `faq`, `stated` (per topic: rows with their state, statements elsewhere, what the v1.9.1 rule returns),
  `links`, `media`, `scripts`, `oddities`, `contact`, `intro`.

Example: `python3 -I -c "import json;d=json.load(open('notes/03-data-audit.json'));print([l['id'] for l in d['listings'] if 'rent: per day' in l['edge_flags']])"`.

To rebuild everything (about a minute): `cd notes/tools && python3 -I extract.py && python3 -I php_check/make_harness.py && python3 -I analyse.py && python3 -I build_md.py`.
`extract.py` also writes each description as the page printed it to `notes/tools/out/desc/ID.html` and as plain text to `ID.txt`, handy as test input. Modules:
`rp_page.py` (classic page parser), `rp_desc.py` (description to blocks), `rp_sem.py` (sections, rows, topics, FAQ, oddities), `rp_v191.py` (Python mirror of the
v1.9.1 readers). `php_check/make_harness.py` copies thirteen functions verbatim from `ref/dev-v1.9.1.php` into `out/php_check/harness.php`, runs them with the local
`php` on every saved description and compares: no difference on any of the 129 listings (`out/php_check/report.json`).

### The fixture listings at a glance

| ID | Kind | Price field, label | Rent phrase (v1.9.1) | Photos, first | Sections | FAQ (questions) | Rows that state a fact | Notable |
| --- | --- | --- | --- | --- | --- | --- | --- | --- |
| 45577 | home | 135,000 `per month` | KES 135,000 a month | 19, landscape | h3 +title | bold-p+br (7) | service, available |  |
| 45538 | home | 100,000 `per month` | KES 100,000 a month | 19, landscape | h3 +title | bold-p+br (7) | service, available |  |
| 44020 | home | 6,500 `per day` | KES 6,500 a day | 10, landscape | bold | bold-p+br (6) | furnish | text: text cut off mid-word |
| 43543 | home | 200,000 `per month` | KES 200,000 a month | 21, landscape | h2 | bold-p+br (8) | furnish, viewing | size: field holds the sq m number (shown as ft²); no map pin |
| 42616 | home | 650,000 `per month` | KES 650,000 a month | 6, landscape | h2 | heading (12) | available | rent: stray "from" on the unused second price |
| 34171 | home | 337,000 `per month` | USD 2,600 a month | 1, square | h2 | rank-math-block (8) | service, furnish | rent: stray "from" on the unused second price; rent: US dollars in the listing; text: JSON-LD script inside the description |
| 42450 | home | 80,000 `per month` | KES 80,000 a month | 14, landscape | h2 | bold-p+br (10) | — | rent: stray "from" on the unused second price; theme sub-unit block: Other units in 2 & 3 Bedroom Apartmen… |
| 42449 | home | 60,000 `per month` | KES 60,000 a month | 9, landscape | h2 | bold-p+br (10) | — | rent: stray "from" on the unused second price; theme sub-unit block: Other units in 2 & 3 Bedroom Apartmen… |
| 36499 | home | 60,000 `per month` | KES 60,000 a month | 11, square | h2 | bold-p-inline (9) | furnish | rent: stray "from" on the unused second price; beds: title lists 2/3, field holds 2 (smallest); several homes or spaces in one listing |
| 36506 | home | 60,000 `per month` | KES 60,000 a month | 1, portrait | h2 | bold-p-inline (8) | — | rent: stray "from" on the unused second price; several homes or spaces in one listing |
| 36502 | home | 80,000 `per month` | KES 80,000 a month | 10, square | h2 | bold-p-inline (8) | furnish, available | rent: stray "from" on the unused second price; several homes or spaces in one listing |
| 39421 | home | 30,000 `per month` | KES 30,000 a month | 16, portrait | h2 | bold-p-inline (7) | available | rent: text gives a range or "from", label has no "From"; beds: title lists studio/1/2, field holds 2 (largest); several homes or spaces in one listing; text: text cut off mid-word |
| 37551 | home | 80,000 `per month` From | KES 80,000 a month | 7, portrait | h3 | rank-math-block (8) | available | kind: category says commercial, title says home; beds: title lists 1/2/3, field holds 3 (largest); several homes or spaces in one listing; no project term; text: JSON-LD script inside the description |
| 44048 | home | 70,000 `` | KES 70,000 a month | 14, landscape | h3 | bold-p+br (6) | available | rent: text gives a range or "from", label has no "From" |
| 42590 | home | 70,000 `` | KES 70,000 a month | 24, landscape | h3 | bold-p+br (9) | — | rent: text gives a range or "from", label has no "From"; rent: stray "from" on the unused second price |
| 41862 | home | 270,000 `per month` | KES 270,000 a month | 11, landscape | h3 | bold-p+br (9) | furnish, available | rent: stray "from" on the unused second price; no project term |
| 40490 | home | 150,000 `per month` | KES 150,000 a month | 7, landscape | h2 | bold-p-inline (8) | service, available | no features; no project term |
| 43583 | home | 220,000 `per month` | KES 220,000 a month | 14, landscape | h3 | bold-p+br (7) | service | text: placeholder in square brackets |
| 36809 | commercial | 695,054 `per month` From | KES 695,054 a month | 1, square | h2 | heading+rank-math-block (18) | service, furnish | kind: commercial; several homes or spaces in one listing; no features; no project term; text: JSON-LD script inside the description |
| 36834 | commercial | 125 `per sq ft` From | KES 125 per sq ft a month | 1, landscape | h3 | rank-math-block (8) | service, available | kind: commercial; several homes or spaces in one listing; text: JSON-LD script inside the description |
| 36816 | commercial | 0 `USD 1.30 per sq ft per month plus VAT` | USD 1.30 per sq ft a month | 1, portrait | h2 | heading+rank-math-block (19) | service | rent: stray "from" on the unused second price; rent: US dollars in the listing; kind: commercial; several homes or spaces in one listing; no features; text: JSON-LD script inside the description |
| 35986 | commercial | 0 `` | Rent on request | 6, landscape | h2 | bold-p-inline (9) | available | kind: commercial; size: field differs from the sizes in the text; several homes or spaces in one listing; theme sub-unit block: Available Units; no features |
| 45231 | commercial | 35 `per sqft` | KES 35 per sq ft a month | 0, unknown | h3 +title | bold-p+br (9) | — | kind: commercial; map pin far from the listing (-2.732, 38.217) |
| 36826 | commercial | 200 `per sq ft per month` From | KES 200 per sq ft a month | 2, landscape | h2 | heading+rank-math-block (17) | service | kind: category Offices, title says retail (the site has no…; kind: commercial; several homes or spaces in one listing; no features; text: JSON-LD script inside the description |
| 40298 | commercial | 168 `per sqft` | KES 168 per sq ft a month | 10, landscape | h2 | bold-p-inline (9) | service, furnish | rent: US dollars in the listing; kind: commercial |
| 43656 | home | 0 `` | Rent on request | 13, landscape | h3 | bold-p+br (10) | — | rent: text gives a range or "from", label has no "From"; kind: no category; beds: home with bedrooms field 0 (title says 2 bedroom); several homes or spaces in one listing; no features; no map pin; no agent; no status term |

"Sections" is the heading level that carries the sections ("+title": one title heading above it; "bold": no heading tags). "Rows that state a fact" lists the topics of
section 2 that the listing states in a row.

## 15. What this audit could not establish

- **The meta key of the second price's before-label** (the stray "from" on 54 listings). Read the custom fields of 34171 on the test site or with the connector and look for the value "from".
- **Whether `bke_rent_usd` / `bke_rent_usd_date` are filled** on 44533, 35583, 34171, 36816, 40298, 36792, 29501. They are not in the feed or the pages; read the post meta.
- **Whether the JSON-LD script is stored without a `type` attribute or loses it on output.** Read `post_content` of 34171; the saved page has `<script>{...}` (`ref/pages-final/34171.html:4460`).
- **Whether the two linked URLs that are not in the feed still resolve**: `/property/brand-new-grade-a-office-space-for-rent-in-westlands-nairobi/` and
  `/property/fadaq-park-westlands-grade-a-office-space-for-rent-on-brookside-close/`. One request each would tell (not made here: no live calls).
- **Real pixel sizes of the photos.** URLs give the aspect of 1252 of 1496 photos; the rest need the attachment metadata (`wp_get_attachment_metadata()` on the test site).
- **The name of the theme's 1080-high image size** and whether every listing's gallery has it; check `wp_get_registered_image_subsizes()` on the test site.
- **Whether the description in the database equals what the page prints** for the PDF embed blocks (35986, 35583) and the Rank Math blocks; the audit read rendered HTML only.
- **Which buildings the listings without a project term belong to** beyond the name matches in section 8 (for example whether the six furnished Elgeyo Marakwet Road
  listings 29531, 29489, 29475, 29462, 29436, 29419 are one building). Only the owner can say.
- **Whether 33230 is really off-plan** (its status term says so, its text says "available to let") and whether the sale-and-rent listings (45228, 37243, 36662 and the Meteor Garden four)
  will be retagged, as the README proposes.
- **What v1.9.1 does with the text as stored.** The v1.9.1 columns were checked by running its own functions on each description as the classic page printed it
  (section 14). On the site the snippet reads `do_blocks( post_content )`; a spot check on the test site with v1.9.1 loaded (45577, 44020, 43482) would close that gap.
