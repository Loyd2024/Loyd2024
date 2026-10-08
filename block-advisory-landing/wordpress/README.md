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

- emails the price-check popup's notify list (currently loyd@block.ke) and the listing's agent;
- forwards the lead to Zoho through the popup snippet's Web-to-Lead connection, once its tokens are set;
- tags the source (Google Ads, Meta, organic and so on) using the same rules as the popup;
- keeps the last 200 enquiries in the option `blockke_dev_leads`;
- fires the Google Ads lead conversion (`window.blockkeLeadConversion`), GA4 `generate_lead` and Meta `Lead`.

If the listing has a brochure, either a PDF link in the box or a PDF attached to the listing, it opens straight after the visitor sends their details.

**Optional questions after a quick enquiry.** The hero and pop-up forms ask only for a name and phone number. Once the lead is in, the thank-you panel offers three one-tap questions: which home, when they would like to buy (within 3 months, 3–12 months, just exploring) and whether they are buying to live in or invest. Answers are added to the same lead in `blockke_dev_leads` and emailed as "[Block] More details: …"; add them to the Zoho lead by hand, as Web-to-Lead can't update a lead. Skipping them loses nothing. The full enquiry form asks the timeline as well. The questions are not a separate form, so GA4 and Meta form tracking don't count the lead twice.

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
- v1.2: on classic pages built with WPResidence's own *Features* and *Address* markup, the tiles and checklist replace the feature groups, and *What's nearby* follows the address. The checks covered expected distances for Santorini Residences (MP Shah Hospital 0.4 km, Sarit Centre 1.5 km, JKIA 14 km), listings with only essentials, no map pin, a default pin and their own places list, both config switches, the page without JavaScript, no sideways scrolling from 320px to 1440px, and the development layout's Location fallback.

The real theme's header and footer can differ from the test copy, so preview with `?bke_layout=1` before switching a listing on.
