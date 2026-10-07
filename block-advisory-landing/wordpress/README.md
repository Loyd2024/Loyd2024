# block.ke: development property page and payment calculators

`block-development-page.php` is a single WPCode PHP snippet with two features. Everything it shows is read from the listing, so a development keeps working like any other block.ke property: same URL, site header and menu, search and maps, agent, similar listings and SEO. To update the page, edit the property as usual.

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

## 2. Development layout (switched on per listing)

A landing-page layout for new developments, drawn between the site's normal header and footer.

| Section | Comes from |
|---|---|
| Hero: name, area, status, *From* price, completion, photo, two-field enquiry form | Title, Area, Property Status, lowest unit price, completion date, featured image |
| Key facts strip | The description's *at a Glance* or *Project Snapshot* table or list, plus unit sizes |
| Overview and *Read the full description* | The description's opening paragraphs and its *Why …* list. The full description stays on the page, collapsed. |
| Photos | The listing gallery |
| Residences table, with filters | The description's Unit / Size / Price table, linked to the unit listings. With no table, the unit listings (Multi Units) are used. |
| Amenities | Bold-titled groups in the description's *Amenities* section, otherwise the *Features & Amenities* ticks grouped by category |
| Payment plan | Off-plan: the *Payment Plan* steps plus the instalment calculator. Complete: a mortgage calculator. |
| Location | Map pin from the listing coordinates, plus places and travel times from the *Location* section |
| FAQ | The description's *FAQ* / *Frequently Asked Questions* section |
| Similar developments | Other developments for sale in the same area |
| Enquiry form and advisor | The listing's agent |

**Turn it on for one listing.** Edit the property, find the **Development page** box, and set **Layout: On**. The box also has optional overrides: hero line, key facts, deposit, reservation fee, places, brochure PDF, video and similar listings.

**Preview before switching on.** While logged in, add `?bke_layout=1` to any property URL, or use the admin-bar menu *Development layout → Preview*. Use `?bke_layout=0` to see the classic page.

**Turn it on for every development at once.** Set the option `blockke_dev_page['auto'] = true`. Every listing with units (`property_has_subunits = 1`) then uses the layout unless its box says *Off*.

## Enquiries

Every form posts to `/wp-json/block/v1/development-lead`, which:

- emails the price-check popup's notify list (currently loyd@block.ke) and the listing's agent;
- forwards the lead to Zoho through the popup snippet's Web-to-Lead connection, once its tokens are set;
- tags the source (Google Ads, Meta, organic and so on) using the same rules as the popup;
- keeps the last 200 enquiries in the option `blockke_dev_leads`;
- fires the Google Ads lead conversion (`window.blockkeLeadConversion`), GA4 `generate_lead` and Meta `Lead`.

If the listing has a brochure, either a PDF link in the box or a PDF attached to the listing, it opens straight after the visitor sends their details.

## Settings

The option `blockke_dev_page` (an array) overrides any of these defaults:

| Key | Default | What it does |
|---|---|---|
| `enabled` | `true` | Development layout available |
| `auto` | `false` | Layout on for every listing with units |
| `offplan_calculator` | `true` | Instalment calculator on off-plan pages |
| `price_check_popup` | `false` | Show the site-wide price-check popup on development pages, which already carry their own forms. The popup is unchanged everywhere else. |
| `default_deposit` | `20` | Deposit % used when a listing states none |
| `mortgage_rate`, `mortgage_years`, `mortgage_deposit` | `13`, `20`, `20` | Mortgage calculator defaults on complete developments |
| `notify_emails` | `[]` | Who receives enquiries. Empty uses the popup's list. |
| `count_views` | `true` | Keep the theme's view statistics counting on development pages |

## Install and roll back

1. Open **WPCode → Code Snippets → BLOCK — Development property page + payment calculators v1**. It is staged there as an inactive draft. To install from this file instead, add a new PHP snippet and paste the file without its first `<?php` line.
2. Set *Insert method* to Auto Insert and *Location* to Run Everywhere. Turn on **Active** and click **Update**.
3. Check an off-plan property: the payment plan calculator replaces the mortgage one. Then preview a development with `?bke_layout=1`.

To roll back, deactivate the snippet. Every page returns to the theme's own template and calculator.

## Testing

The snippet was tested on a local WordPress copy that mimics WPResidence 5.6. It used block.ke's live custom CSS and the real Santorini Residences and Luster Arbor descriptions, plus a completed development. The tests covered:

- no sideways scrolling from 320px to 1440px;
- the hero form above the fold at 1440×900;
- form validation and REST submission, and the lead email and log;
- the Google Ads conversion call;
- the unit pop-up, gallery, filters, sticky section bar and mobile bar;
- both calculators;
- the calculator swap on classic off-plan and complete pages.

The real theme's header and footer can differ from the test copy, so preview with `?bke_layout=1` before switching a listing on.
