# block.ke: development property page and payment calculators

`block-development-page.php` is a single WPCode PHP snippet that improves block.ke property pages in four ways. Everything it shows is read from the listing, so a development keeps working like any other block.ke property: same URL, site header and menu, search and maps, agent, similar listings and SEO. To update the page, edit the property as usual.

## 1. Payment calculators on every for-sale property page

| Listing | Calculator |
|---|---|
| **Off-plan**: Property Status *Off-Plan / Ongoing* and Completion not *Ready now* | **Payment plan calculator**: deposit, then monthly or quarterly instalments up to completion. It replaces the theme's mortgage calculator in the same place on the page. |
| **Complete**: Status *Complete*, or Completion *Ready now* | The theme's mortgage calculator, unchanged. |

The instalment calculator fills itself in from the listing:

- **Price:** the listing price. Visitors can change it.
- **Deposit and reservation fee:** read from the description's *Payment Plan* section, for example "Deposit: 20% of the purchase price" and "Reservation fee: KES 200,000". If the listing states no deposit, the calculator uses 20% and says so.
- **Months:** from today until the completion date. The date comes from `bke_completion_stated`, a "Completion" or "Occupation" line in the description, or the Completion year. With no date, visitors choose a period of 6 to 60 months.

Its button opens the agent contact form with "Please send me the official payment plan" already typed in.

**Mortgage defaults need updating.** The theme's mortgage calculator currently assumes 4.125% interest over 30 years, which are US figures. Update them in *WpResidence Options → Property Page → Mortgage calculator*, for example to 13% over 20 years.

## 2. Amenities and nearby places on every property page

These two changes apply to the theme's own (classic) property pages.

**Features & Amenities** opens with up to six amenity tiles, as on the development layout: navy tiles with a brass icon, the amenity's name and a one-line description. Everything else follows as one checklist, titled "The everyday essentials" when it holds only security, parking, lifts, water and power, otherwise "Also included". This replaces the theme's list of feature groups, where most groups held one to three items under their own heading. The items are the listing's *Features & Amenities* ticks, as before, so there is nothing new to fill in.

**Address** gains a *What's nearby* panel. It lists the nearest two business districts, malls, schools or universities, hospitals, parks and airports, with straight-line distances from the listing's map pin. The places are a curated list of 36 Nairobi landmarks in `blockke_dev_landmarks()`, and more can be added there, for example Gateway Mall for Syokimau listings. A group appears only when one of its places is close enough to matter: 6 km for malls and parks, 8 km for schools and hospitals (5 km for universities), and further for business districts and airports. There is no panel for listings outside Nairobi, without a map pin, or with the pin left on the theme's default map centre. To show your own list instead, fill in **Places nearby** in the listing's *Development page* box, one per line as `Place | travel time`.

Both are moved into place by a small script at the end of the page. Without JavaScript, or if the theme's markup changes, the theme's own content stays as it was.

## 3. Development layout (switched on per listing)

A landing-page layout for new developments, drawn between the site's normal header and footer.

| Section | Comes from |
|---|---|
| Hero: name, area, status, *From* price, completion, photo, two-field enquiry form | Title, Area, Property Status, lowest unit price, completion date, featured image |
| Key facts strip | The description's *at a Glance* or *Project Snapshot* table or list, plus unit sizes |
| Overview and *Read the full description* | The description's opening paragraphs and its *Why …* list. The full description stays on the page, collapsed. |
| Photos | The listing gallery, with each photo's Media Library caption when it has one |
| Residences table, with filters | The description's Unit / Size / Price table, linked to the unit listings. With no table, the unit listings (Multi Units) are used. |
| Amenities | Up to six feature tiles for the stand-out amenities (pool, sky lounge, gym, cinema and so on), each with an icon and a one-line description, then a checklist of everything else. The items come from bold-titled groups in the description's *Amenities* section, otherwise the *Features & Amenities* ticks. |
| Payment plan | Off-plan: the *Payment Plan* steps plus the instalment calculator. Complete: a mortgage calculator. |
| Location | Map pin from the listing coordinates, plus places and travel times from the *Location* section. With none listed, the nearest business district, mall, school, hospital, park and airport, with straight-line distances. |
| FAQ | The description's *FAQ* / *Frequently Asked Questions* section |
| Similar developments | Other developments for sale in the same area |
| Enquiry form and advisor | The listing's agent |

**Look and feel.** The page uses block.ke's warm off-white with sand-coloured sections. Navy is used for the key facts strip, the hero enquiry card, Amenities and *Why Block*, with brass accents. Headings are set in Instrument Serif, a Google Font, and a few words in each are picked out in brass italics, for example "What comes *with the keys.*". To accent part of the overview headline override, wrap those words in asterisks: `About *the building*`. Setting `display_font` to `''` returns all headings to the site font.

**Amenity tiles.** Tiles are chosen by kind, best first, and appear in full rows of 1, 2, 3, 4 or 6. The kinds are: beach, pool, sky lounge or sky garden, cinema, spa, gym, nature, kids' play, café, golf, gardens, yoga, shops, co-working, sports courts, BBQ, residents' lounge and jogging track. A tile keeps the listing's own name when it is short. A longer item becomes the tile's description under a short title, for example "Sky garden" over "Terrace sky garden with panoramic city views". Security, parking, lifts, power and water go in the checklist, titled "The everyday essentials" when that is all it holds.

**Amenity photos.** Give a listing's photos a caption in the Media Library (*Media → the photo → Caption*), for example "Indoor heated pool", "Sky Lounge" or "Private cinema". Photos whose caption names an amenity then appear as a captioned photo strip at the top of Amenities, before the tiles. On phones it swipes; on desktop it shows one full row of 2, 3 or 4 photos (6 or 8 in two rows). Tapping a photo opens the gallery on it. Other captions, such as "Lantana Road frontage", show on the gallery photos and in the photo viewer. With fewer than two amenity captions, the section looks as before. The caption is WordPress's own field, so the theme may also show it wherever it displays captions.

**Turn it on for one listing.** Edit the property, find the **Development page** box, and set **Layout: On**. The box also has optional overrides: hero line, key facts, deposit, reservation fee, places, brochure PDF, video and similar listings.

**Preview before switching on.** While logged in, add `?bke_layout=1` to any property URL, or use the admin-bar menu *Development layout → Preview*. Use `?bke_layout=0` to see the classic page.

**Turn it on for every development at once.** Set the option `blockke_dev_page['auto'] = true`. Every listing with units (`property_has_subunits = 1`) then uses the layout unless its box says *Off*.

## 4. Ad landing pages (for Google Ads and other campaigns)

Every for-sale listing has a landing page for paid campaigns, with nothing to set up: its URL plus `?lp=1`, for example `https://block.ke/property/santorini-residences-westlands-1-4-bed-apartments-lofts/?lp=1`. For a tidier address, create a page under the private page *Landing pages* (slug `lp`) and add a custom field `bke_lp_listing` holding the listing's ID. Santorini's is ready as a draft: `https://block.ke/lp/santorini-residences/` (page 42708, listing 40401); publish it once the v1.3 snippet is on. On these pages, tick Rank Math's *No Index* so they stay out of the sitemap, and set the listing's main photo as the featured image so WhatsApp and Facebook link previews show it. The page's own text appears only if the snippet is switched off, so a line linking to the listing is a good fallback.

It shows the development layout's content, built for visitors arriving from an ad:

- **A slim header** with the logo, phone, WhatsApp and *Get the price list*, instead of the site menu. The footer holds only the address, contact details and privacy policy.
- **No ways off the page but contact.** Breadcrumbs, the share button, *Similar developments*, the unit "Details" links and links inside the description and FAQ are left out.
- **Phone first.** A tall photo carries the status, area, development name, its homes and street (for example "1 – 3 Bed & Lofts · Lantana Road") and the price, completion and deposit. The two-field form follows straight after, with its "Sent straight to your WhatsApp" line, and the whole form, button included, fits the first screen of an iPhone with Safari's or Chrome's bars showing (390×664) and of most Android phones. The advisor's photo and name sit under the form, and the WhatsApp and price list bar appears once the form scrolls away.
- **Hidden from search** (`noindex, follow`), so it never competes with the listing in Google results.
- **Tracking as usual.** The page runs WordPress's head and footer hooks, so Google Ads conversions (form, WhatsApp and call clicks), GA4, Meta Pixel, Zoho PageSense and SalesIQ load as on every page. Leads carry the ad's gclid and UTM tags, and the lead email shows the landing page address.

Rentals never become landing pages. Set `landing_pages` to `false` to switch them off.

**Who it suits best.** Both layouts now show the description's "Who … suits best" list, its note on who it suits less well, and the first three sentences of its "… for Investors" section with a button to ask for rental and resale comparables.

## Enquiries

Every form posts to `/wp-json/block/v1/development-lead`, which:

- saves the enquiry in the option `blockke_dev_leads` (the last 200) before Zoho and the email, so a slow or failing step can't lose it;
- emails the enquiry addresses (`notify_emails`; on block.ke loyd@block.ke, sales@block.ke and loydmokaya@gmail.com) and the listing's agent;
- forwards the lead to Zoho through the popup snippet's Web-to-Lead connection, once its tokens are set;
- tags the source (Google Ads, Meta, organic and so on) using the same rules as the popup;
- fires the Google Ads lead conversion (`window.blockkeLeadConversion`), GA4 `generate_lead` and Meta `Lead`.

If the listing has a brochure, either a PDF link in the box or a PDF attached to the listing, it opens straight after the visitor sends their details.

**Optional questions after a quick enquiry.** The hero and pop-up forms ask only for a name and phone number. Once the lead is in, the thank-you panel offers three one-tap questions: which home, when they would like to buy (within 3 months, 3–12 months, just exploring) and whether they are buying to live in or invest. Answers are added to the same lead in `blockke_dev_leads` and emailed as "[Block] More details: …"; add them to the Zoho lead by hand, as Web-to-Lead can't update a lead. Skipping them loses nothing. The full enquiry form asks the timeline as well. The questions are not a separate form, so GA4 and Meta form tracking don't count the lead twice.

**Spam and AutoFill.** Each form has a hidden field that people never see and bots fill in. A send that fills it is answered as if it worked, but nothing is emailed or saved as a lead; the last 30 are kept in `blockke_dev_spam` in case one was a person. Before v1.3.1 the field was called `company`, which browsers' AutoFill fills in, so enquiries from visitors using AutoFill were dropped without a trace. It now has a name AutoFill leaves alone and isn't drawn at all. When a send fails, the form shows the error code (for example "error 403") before the WhatsApp link, so the cause can be traced.

## Getting around: search, similar homes and the unit switcher (v1.4)

**Search homes.** A "Search" button sits next to Share at the top of every development page, and a search icon sits in the desktop section bar. It opens a short search already filled in from the listing, for example Buy · Westlands · 2 bed · KES 12M–18M. "Show homes" opens the site's own search results (`/advanced-search/?location=Westlands&property_action_category[]=for-sale&componentsbeds=2&price_low=…&price_max=…`, plus `completion` for sales). "Or ask an advisor to shortlist for me" turns the same search into an enquiry. On phones the browser's back button closes the search. Without JavaScript the button is a plain link to the filled-in search.

**Also consider.** On a home's page (a unit or a single listing), this shows up to 6 homes with the same number of bedrooms. It picks them in this order:
1. Homes in the same area within 25% of the price, closest in price and size first.
2. Homes in the same area at any price.
3. Homes in the nearest neighbouring areas (config `neighbours`).
4. Homes with one bedroom more or fewer in the area.
5. The next ring of areas, only while fewer than 3 have been found.

It shows one home per development, and never this development or sold, let, commercial, office or per-sq-ft-priced listings. Rentals are compared with rentals. A studio stored as a 1-bedroom is recognised from its title, or from a size under 500 sq ft when the title gives no bedroom count (so a compact "1 Bedroom" stays a 1-bed). Listings for a range of homes ("2–4 Bed Lofts", "Studio & 1 Bed") are left out. Under the cards are "See all 2-bed homes in Westlands" (the site search), "Refine search" and "Compare these with an advisor". Development pages show similar developments instead. If fewer than 3 matches turn up, the block shows developments in the area. If those are short too, it shows the one or two homes it did find. Only when there are none does it say so and offer "Ask an advisor to find one". `bke_dev_similar` (listing IDs) puts chosen listings first on every kind of page and is never dropped. "Hide Also consider" turns the block off for one listing. The block is cached for each listing. It is rebuilt whenever a published listing's price, bedrooms, area, status or photo changes, a listing is published, unpublished or deleted, or an area or project is renamed. Drafts don't count. The cache reads only the few fields it needs, not each listing's view statistics.

**Unit switcher and breadcrumb.** A unit's page shows a row of the development's homes (1 Bed 6.5M · 2 Bed 13.5M · 3 Bed 18M · All homes). The breadcrumb reads Home / For Sale / Westlands / Golden Mansion / 2 Bedroom Apartment; on phones it shortens to "‹ Golden Mansion". A visitor who came from the site's search or a listings page sees "‹ Back to results" instead, which survives hops between the development's homes. Under the residences table, "See all homes for sale in Westlands" follows the bedroom filter ("Comparing? See other 2-bed homes in Westlands").

**Main area.** A listing tagged with several areas uses its development's area, else the one with the most listings. The Golden Mansion 2-bed, tagged Industrial Area and Westlands, therefore reads Westlands everywhere: the hero, breadcrumb, similar homes and the enquiry email. Set "Main area (slug)" on a listing to choose it by hand.

**Ad landing pages** stay a closed funnel: no search, no similar homes and none of their database queries. They show "Not sure Golden Mansion is the one? Send me 3 alternatives", which opens the enquiry form ("Alternatives request" in the email). Only after the visitor has enquired does the thank-you panel add one link: "While you wait: see other 2-bed homes in Westlands".

**Rolling it out.** `auto_units` (default off) switches the layout on for unit listings for sale without ticking each one. Set it to `true` for all of them, or to a list of area slugs, e.g. `array( 'westlands' )`, to go area by area.

**Tracking.** GA4 / dataLayer events:
- `search_open`, `search_submit` (with the fields and how many the visitor changed), `search_shortlist`;
- `similar_view` (once), `similar_click` (with position and tier), `similar_see_all`;
- `unit_switch`, `units_filter`, `residences_see_all`, `back_to_results`, `lp_after_click`.

The new enquiry sources `alternatives`, `search-shortlist`, `compare` and `find` are named in the lead email.

**Settings** (option `blockke_dev_page`):
- similar homes: `similar_count` (6), `similar_min` (3), `price_band` (0.25), `dev_price_band` (0.35), `neighbours`, `pool_cap` (200), `sim_ttl` (12 hours), `similar_units`;
- search: `search`, `search_url`, `search_min_area_count` (5), `budget_bands`, `beds_mode` (`exact`: links use the home's own bedrooms, so a 5-bed links to 5-bed homes, and the search offers 1 to 5; set `min` if the site search treats bedrooms as "at least", so labels read "2+ bed");
- navigation and landing pages: `unit_switcher`, `back_to_results`, `lp_alternatives`, `lp_after_link`, `auto_units`.

**Visitors abroad.** When the browser's time zone isn't Nairobi, the phone fields show an international example for that country (for example `+44 7700 900123` in the UK), so diaspora buyers include their country code.

## Settings

The option `blockke_dev_page` (an array) overrides any of these defaults:

| Key | Default | What it does |
|---|---|---|
| `enabled` | `true` | Development layout available |
| `auto` | `false` | Layout on for every listing with units |
| `offplan_calculator` | `true` | Instalment calculator on off-plan pages |
| `classic_amenities` | `true` | Amenity tiles and checklist in *Features & Amenities* on classic pages |
| `classic_nearby` | `true` | *What's nearby* in *Address* on classic pages |
| `landing_pages` | `true` | Ad landing pages at `?lp=1` and on pages with a `bke_lp_listing` field |
| `logo` | `''` | Landing page header logo. `''` uses the theme logo. |
| `address` | Upper Hill Gardens… | Office address in the landing page footer |
| `price_check_popup` | `false` | Show the site-wide price-check popup on development pages, which already carry their own forms. The popup is unchanged everywhere else. |
| `display_font` | `'Instrument Serif'` | Heading font on development pages, and in the amenity tiles and *What's nearby* on classic pages. Use a Google Font that has an italic. `''` keeps the site font. |
| `default_deposit` | `20` | Deposit % used when a listing states none |
| `mortgage_rate`, `mortgage_years`, `mortgage_deposit` | `13`, `20`, `20` | Mortgage calculator defaults on complete developments |
| `notify_emails` | `[]` | Who receives enquiries. Empty uses the popup's list. |
| `count_views` | `true` | Keep the theme's view statistics counting on development pages |

## Install and roll back

1. Open **WPCode → Code Snippets**. To install from this file instead, add a new PHP snippet and paste the file without its first `<?php` line.
2. Set *Insert method* to Auto Insert and *Location* to Run Everywhere. Turn on **Active** and click **Update**.
3. Check an off-plan property: the payment plan calculator replaces the mortgage one, *Features & Amenities* shows the tiles, and *Address* shows *What's nearby*. Then preview a development with `?bke_layout=1`.

To roll back, deactivate the snippet. Every page returns to the theme's own template and calculator.

**Upgrading.** Each version is staged as its own snippet with a lower priority number than the one before, so it runs first: v1.3 at priority 4, v1.1 at 5, v1.0 at 10. (v1.2 was never switched on; its snippet became v1.3.) The whole file is wrapped in `if ( ! function_exists( 'blockke_dev_config' ) )`, so whichever version runs first is the one used, and the others skip themselves. To upgrade, switch the new snippet on, and it takes over at once. Then switch the older ones off, or keep the newest of them as a fallback. To go back, switch the new one off.

WPCode runs a cached copy of each active snippet, refreshed when a snippet is saved in WPCode. A snippet edited through the API stays as it was on the site until someone clicks **Update** on it, or on any other snippet.

## Testing

The snippet was tested on a local WordPress copy that mimics WPResidence 5.6. It used block.ke's live custom CSS and the real Santorini Residences and Luster Arbor descriptions, plus a completed development. The tests covered:

- no sideways scrolling from 320px to 1440px;
- the hero form above the fold at 1440×900;
- form validation and REST submission, and the lead email and log;
- the Google Ads conversion call;
- the unit pop-up, gallery, filters, sticky section bar and mobile bar;
- both calculators;
- the calculator swap on classic off-plan and complete pages;
- v1.1: the amenity tiles and checklist on three listings at desktop and mobile widths (full tile rows, no clipped text), the fonts and colours, and the `display_font = ''` fallback;
- v1.3: landing pages at `?lp=1` and on a page, for an off-plan development, a unit and a completed development: no theme header or footer, no links off the page but the privacy policy, `noindex, follow`, lead submission with gclid and the Google Ads conversion call, the contact bar, rentals refused, the `landing_pages` switch, and no sideways scrolling from 320px to 1440px. Also at real phone viewports with browser bars (390×664, 360×640, 412×780, 430×740): the whole form on the first screen and the cover copy on the photo. The optional questions were checked end to end: saved on the same lead, emailed, accepted once, unknown references refused, no extra form submit event, the contact bar never covering them, and no "Which home?" after a unit's own enquiry. Also tested: the timeline on the full form, the phone examples for six time zones, and captioned photos (the amenity strip swiping on phones and in one full row on desktop, non-amenity captions kept out of it, captions on the gallery and in the viewer, and no strip without captions);
- v1.4: on a copy seeded from block.ke's Westlands inventory (Golden Mansion and its units, 15 two-beds from other developments, Peponi, Parklands and Kilimani, rentals, and the data traps: a studio stored as a 1-bed, a range listing, a per-sq-ft price, an office, a rental with no Buy/Rent tag, a sold unit), 67 checks. They covered the breadcrumb and main area (no "Industrial Area"), similar homes (3–6, none from Golden Mansion, one per development, the first within 25%, no traps), rentals, a thin area widening to its neighbours, similar developments on a development page, the residences link following the filter, landing pages staying closed with one link after an enquiry, the search sheet (filled in, Tab, Escape, back button, Rent, the exact search parameters, shortlist, no-JavaScript link), no sideways scroll from 320px with 40px targets, the desktop section bar from 1000px to 1440px, back to results across the switcher, the cache following price, sold and status changes, the tracking events and print;
- v1.3.1: the landing page's "Speak to an advisor" form at phone and desktop widths: sent, saved and emailed, with the Zoho and email results written onto the saved lead; no field called `company` and the anti-spam field not drawn; an AutoFilled `company` from a cached older page accepted; bot sends caught and kept aside; error codes for a firewall page, a crash and a non-JSON answer, the site's own refusal message, and the plain message with no connection; and the enquiry kept when the mail server crashes mid-send;
- v1.2: on classic pages built with WPResidence's own *Features* and *Address* markup, the tiles and checklist replace the feature groups, and *What's nearby* follows the address. The checks covered expected distances for Santorini Residences (MP Shah Hospital 0.4 km, Sarit Centre 1.5 km, JKIA 14 km), listings with only essentials, no map pin, a default pin and their own places list, both config switches, the page without JavaScript, no sideways scrolling from 320px to 1440px, and the development layout's Location fallback.

The real theme's header and footer can differ from the test copy, so preview with `?bke_layout=1` before switching a listing on.

## Enquiries inbox (`block-enquiries-inbox.php`)

A separate WPCode PHP snippet that keeps every website enquiry inside WordPress, whether or not the emails arrive. It adds an **Enquiries** page (administrators only) and a "Latest enquiries" box on the dashboard:

- **Website enquiries:** the price-check popup's leads and the development and landing page leads, newest first, with WhatsApp, call and email links, and whether the email was handed over and what Zoho said.
- **Emails the site sent:** every email WordPress tries to send, the theme's contact forms included, with its text and whether WordPress could hand it to the mail server. Password, login and code emails are listed without their text.
- **Zoho CRM replies:** what Zoho answered for each lead, to find out why leads don't appear in the CRM.
- **Download all as CSV**, and **Send a test email** to the enquiry addresses.

Enquiries the theme's contact forms send to an agent are copied (Bcc) to the enquiry addresses. Those addresses are the price-check popup's `notify_emails` (option `blockke_buyer_popup`), also used by the development pages (option `blockke_dev_page`). On block.ke both are set to loyd@block.ke, sales@block.ke and loydmokaya@gmail.com.

**Emails still need an email service.** WordPress hands email to the web server, and many inboxes reject or hide those messages. Install WP Mail SMTP, or a similar plugin, and connect Gmail, Google Workspace, Zoho Mail or Brevo; then use *Send a test email*. Until then, the Enquiries page shows a warning.

Tested on the local WordPress copy: leads from both sources in date order, the agent copy, failed and sent emails, private subjects, Zoho replies, the page, the dashboard box, the CSV (formulas neutralised, phones kept as text, refused without its security token) and the test email.
