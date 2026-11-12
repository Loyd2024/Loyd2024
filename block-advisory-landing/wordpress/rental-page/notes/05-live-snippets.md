# 05. Live snippets the rental page has to live with

Read on 11 Nov 2026, about 18:20 to 18:40 UTC, from block.ke through the Block_Website connector: `wp_get_post` and
`wp_get_post_meta` on twelve WPCode snippets (post type `wpcode`). Read-only; nothing on the site was changed.
30 calls, one at a time. Four `wp_get_post_meta` calls on 40752 failed with HTTP 400 "empty body" straight after its
55 KB source came back; the same calls succeeded later, so every snippet and its meta was read in full.

`ROOT` = `/tmp/claude-0/-workspace-Loyd2024/e24ac003-f571-5336-9956-1fd11a814c7d/scratchpad/rp`. Paths below are
relative to ROOT unless they start with `/`.

## In brief

1. The buyer popup (40752) is already off on rent-only listings by its own rule. Set `$_GET['blockpop'] = '0'`
   anyway, before `wp_footer` 40; the attribution keeper still prints (section 2.1).
2. Attribution is one `sessionStorage` entry, `blkbp_attr`, a JSON object with the utm tags, `gclid`, `fbclid`,
   `gbraid`, `wbraid`, `t`, `ref` and `land`. Read it when the form is sent, not when the page loads (2.2, 12.3).
3. Zoho goes through `blockke_bp_send_zoho( $lead )`: fifteen array keys, all required, `areas` an array; it
   returns a status string and sends only when two tokens are in the option `blockke_buyer_popup` (2.5, 12.1).
4. Every WhatsApp link of the rental page needs `data-blkct="1"`, the word "viewing" and "Ref W-{id}". Without the
   marker, 40769 replaces its text with a standard message that says "(KES X/month)" for any rent of 5,000 or
   more, which is wrong for a rent by the day (section 3).
5. After an enquiry is accepted, call `window.blockkeLeadConversion('lead_form')` (33892), and send GA4
   `generate_lead` and Meta `Lead` yourself. Never call `gtag_report_conversion()` (section 4, 12.2).
6. Taps on `tel:`, `wa.me` and `mailto:` links are already logged by 40769 and counted as Google Ads conversions
   by 33892. The page adds nothing for them (3.4, 4.1).
7. Let and Sold are the `property_status` terms `let` and `sold`. The meta `blockke_crm_status` only records what
   Zoho last said (section 5).
8. No feed reacts to a new custom field or option. Only the search feed (39193) rebuilds, and only when a listing
   is saved. The feeds quote the price field and the after-price label as stored (section 6).
9. The "Ask Block" chat launcher (42885) is in test mode: visitors do not see it; they see the Zoho SalesIQ float.
   Both can be moved or hidden with CSS, not from PHP (section 7).
10. 44071 acts only on ad landing pages and 45681 prints one harmless style element; neither changes the rental
    layout (sections 8 and 9).

## 0. Where the evidence is

- **`ref/live-snippets/40752.php`**: the full live source of 40752 (951 lines; CRLF turned into LF, nothing else
  changed; no opening PHP tag). Line numbers below refer to this file. `ref/live-snippets/40752.info.json` has its
  title, dates and SHA-256.
- The other eleven sources came back inline. I read each from first line to last but did not retype them. What they
  **print** on a rent-only listing is saved verbatim, cut by script out of `ref/pages-stop/45577.html` (the classic
  page of fixture 45577, fetched today after the stopgap). I compared each block with the live source and saw no
  difference apart from the per-page JSON:
  `ref/live-snippets/33869.printed.html`, `33892.printed.html`, `40752-keeper.printed.html`, `40769.printed.html`,
  `42885.printed.html`, `45681.printed.html` (source line ranges in `ref/live-snippets/README.md`).
- Facts about PHP that is never printed (REST handlers, feeds, filters) are cited as "live source NNNNN,
  `function()`". To re-check one, call `wp_get_post` with that ID.
- The repository has no copy of any of these snippets (searched `/workspace/Loyd2024` for their function names).
- The scripts that made the reference files and ran the offline check are in
  `/tmp/claude-0/-workspace-Loyd2024/e24ac003-f571-5336-9956-1fd11a814c7d/scratchpad/rp-live-tools/`
  (`extract.py`, `extract_printed.py`, `harness_40752.php`).

## 1. The twelve snippets at a glance

All twelve are `publish` (active). All twelve have the same WPCode meta: `_wpcode_auto_insert` = `1`,
`_wpcode_auto_insert_number` = `1`, `_wpcode_priority` = `10`, `_wpcode_location_extra` = `''`, conditional logic off
(`_wpcode_conditional_logic_enabled` = `''`). 39193 simply lacks the `_wpcode_location_extra` and conditional-logic
keys. So WPCode's own priority tells us nothing about order: it is a twelve-way tie. The order that matters is the hook
and priority each snippet registers, in the last column.

| ID | Title; version in the code; last modified | Kind | What it hooks |
|---|---|---|---|
| 40752 | BLOCK - Buyer price-check popup v1; v1.4 (2026-09-23); 2026-09-23 20:35 | PHP | `wp_footer` 40 `blockke_bp_render` (`40752.php:949`); `rest_api_init` route `block/v1/buyer-lead` (`:548-561`) |
| 40769 | BLOCK - Contact click tracking + WhatsApp prefill v1; v1.0 (2026-09-20); 2026-09-20 14:37 | PHP | `wp_footer` 41 `blockke_ct_render`; `rest_api_init` route `block/v1/contact-click`; `template_redirect` (`?blockreport=N`, admins); `init` and cron hook `blockke_contact_digest` |
| 33892 | BLOCK - Google Ads lead conversion v1; v1.0 with v1.1 to v1.3 changes (2026-10-12); 2026-10-12 20:56 | HTML | Printed in the footer, before the `wp_footer` 30 output (`ref/pages-stop/45577.html:7458-7564`) |
| 33869 | Google Ads - gtag_report_conversion helper (defines function only; nothing calls it); 2026-09-19 22:19 | HTML | Printed in the head (`ref/pages-stop/45577.html:820-837`) |
| 44071 | BLOCK - Ad landing page speed v1; v1.0 (2026-10-22); 2026-10-22 20:19 | PHP | `wp_enqueue_scripts` 9999, `wp_print_styles` 0, `wp_print_scripts` 0, `wp_print_footer_scripts` 0 (`blockke_lps_dequeue`); `template_redirect` 1 (output buffer). All only on ad landing pages |
| 42885 | BLOCK - AI chat gate (Synaplan) v1; v1.0 (2026-10-11); 2026-10-11 15:48 | PHP | `wp_footer` 30 `blockke_ac_render`; `rest_api_init` routes `block/v1/chat-lead`, `block/v1/chat-profile/{32 hex}` |
| 45681 | BLOCK - Segment page canonical + rental page calculator v1; v1.0 (2026-11-11); 2026-11-11 10:28 | PHP | `template_include` 99997 (area segment pages only); `wp_head` 99 (rent-only listings) |
| 42369 | BLOCK - CRM listings sync v1; 2026-10-04 00:32 | PHP | `rest_api_init` routes `block/v1/listings` (GET), `block/v1/listing-status` (POST); `admin_menu` (Tools > Block CRM Sync) |
| 42215 | BLOCK - Front desk listings feed v1.1; 2026-10-01 20:33 | PHP | `rest_api_init` route `block/v1/listings-feed` (GET). Nothing else |
| 39193 | BLOCK - Listings feed for search v1 (2026-08-16); 2026-08-16 09:46 | PHP | `init` 40 (rebuild when stale); `save_post_estate_property`; `trashed_post` |
| 42658 | BLOCK - Email match feeds v1; 2026-10-08 12:17 | PHP | `template_redirect` 0 `blockke_mf_render` (acts only on `?block_feed=matches`, then exits) |
| 42704 | BLOCK - Keep ad click tags on old-address forwards v1; v1.0 (2026-10-08); 2026-10-08 20:32 | PHP | filter `old_slug_redirect_url` (default priority) |

### What a rent-only listing prints today, in order

From `ref/pages-stop/45577.html` (classic page, anonymous visitor, today):

| Line | What | From |
|---|---|---|
| 132-139 | Site Kit Google tag `GT-579R9RZW`; defines `gtag()` and `dataLayer` | Site Kit plugin |
| 146-152 | Google Ads tag `AW-18119754545` (`gtag('config', ...)`) | not one of the twelve; source not identified |
| 766-776 | Meta pixel: defines `fbq`, pixel 1546782380796048, PageView | official-facebook-pixel plugin |
| 820-837 | `gtag_report_conversion()` | **33869** |
| 838 | Zoho PageSense, `cdn.pagesense.io`, loaded synchronously in the head | not one of the twelve; source not identified |
| 839-851 | Google Tag Manager `GTM-58WZGMX6` | Site Kit |
| 926 | style `blockke-rent-nocalc` | **45681** (`wp_head` 99) |
| 958 | style `wp-custom-css` (Customizer Additional CSS) | WordPress (`wp_head` 101) |
| 6635-6643 | Zoho SalesIQ loader (`salesiq.zohopublic.com/widget?plugin_source=wordpress`) | Zoho SalesIQ plugin |
| 6904 | Block's site footer, `footer.bkef` | not one of the twelve; source not identified |
| 7228, 7262, 7399-7420 | theme's back-to-top `a.backtop`, compare tray `.prop-compare`, mobile agent bar `.mobile_agent_area_wrapper d-block d-lg-none` | theme footer |
| 7458-7564 | `window.blockkeLeadConversion` and the click-conversion listener | **33892** |
| 7740-7864 | chat launcher CSS, markup, `window.BLKAC_CFG`, script | **42885** (`wp_footer` 30) |
| 7865 | attribution keeper `script#blkbp-attr`; no popup markup follows on a rent-only listing | **40752** (`wp_footer` 40) |
| 7866-7918 | `window.BLOCKCT` and the click tracker | **40769** (`wp_footer` 41) |

The same blocks are on the development-layout version of that listing (`ref/pages-final/45577.html`: lines 826, 6683,
7336, 7461, 7462), so they follow `wp_head()` and `wp_footer()` whatever template draws the page. The rental layout
gets them as long as its template calls `get_header()` and `get_footer()`.

### What is not known about load order

- The **WPCode location** of each snippet (Run Everywhere, Frontend only, Site Wide Header and so on) is a taxonomy
  term (`wpcode_location`), not meta, so the two permitted tools could not read it. What the evidence shows: 33869
  prints in the head and 33892 in the footer (table above); 40752's and 40769's own WPCode notes say "Runs
  everywhere (auto-insert)"; the PHP snippets register `init`, `rest_api_init` and `template_redirect` hooks that
  demonstrably work (the feeds answer, leads arrive), so they are loaded before `init` on front-end and REST requests.
- The order in which WPCode includes snippets of equal priority is not known. **The rental snippet must not depend
  on it**: test for other snippets' functions only inside hook callbacks (`template_include`, `wp_footer`, REST
  callbacks), never while the file is being included.
- Install fact recorded in the WPCode notes of 40752, 40769 and 39193 (`_wpcode_note`): a snippet created or changed
  through the connector is not picked up until someone opens it in the WPCode admin screen and clicks Update (or
  toggles Active), because only that screen rebuilds WPCode's auto-insert cache and safe-tests the PHP.

## 2. (a) 40752, Buyer price-check popup v1.4

Purpose: a two-step "free price check" popup for buyers, a REST endpoint that emails the lead and forwards it to
Zoho, and a site-wide attribution keeper. Source: `ref/live-snippets/40752.php` (cited below as `40752.php:LINE`).

### 2.1 Where it shows, and how a page keeps it off

- `blockke_bp_render()` runs on `wp_footer` priority 40 (`40752.php:563`, `:949`). It first prints the attribution
  keeper, unconditionally (`:565`), then asks `blockke_bp_context()` (`:567`) and stops if that returns null
  (`:568-570`).
- `blockke_bp_context()` (`:98-186`) returns null, so no popup and no pill, when any of these is true:
  - `is_admin()`, `is_feed()`, `is_preview()`, `is_404()` or `is_search()` (`:99-101`);
  - `$_GET` holds `elementor-preview`, `vcv-editable` or `vcv-source-id` (`:102-104`);
  - **`$_GET['blockpop']` is the string `'0'` (`:105-107`): this is the switch**;
  - config `enabled` is empty (`:108-111`);
  - on a listing: it does not have the term `for-sale` in `property_action_category` (`:115-117`), or it has `sold`
    in `property_status` (`:118-120`).
- So **on a rent-only listing the popup is already off by its own rule** (`:115-117`). Evidence on the live page:
  `ref/pages-stop/45577.html:7865` is the keeper alone; there is no `blkbp-css`, `blkbp-cfg` or `blkbp-js`.
- How another snippet switches it off: set `$_GET['blockpop'] = '0';` in PHP at any time before `wp_footer`
  priority 40. The value is read once per request, in `blockke_bp_context()`. The development snippet does this
  inside its `template_include` callback: `ref/dev-v1.8.2.php:8498` ("the popup snippet's own off switch; its
  attribution keeper still runs"), `ref/dev-v1.9.1.php:10257`. The keeper still prints, because it is echoed before
  the switch is read.
- `?blockpop=1` only forces the popup in JavaScript (`:759`), on pages where PHP already rendered it. It does
  nothing on a rent-only listing.
- When the popup is rendered (for-sale listings, buyer articles, hubs, /neighbourhoods/ pages) it adds: the pill
  `#blkbp-pill` (on for-sale listings only, `:622`), bottom left, z-index 999980 (`:678`; at 991px and below `bottom: calc(76px +
  env(safe-area-inset-bottom))`, `:693-695`, and its script measures the theme's mobile agent bar, `:915-921`); the
  backdrop at z-index 999990 (`:636`) and the dialog at 999991 (`:638`). While it is open, `body.blkbp-lock` hides
  the SalesIQ float (`:681-682`). None of this is on a rent-only listing.

### 2.2 The attribution keeper (what holds gclid, the utm tags and the landing page)

Printed on every front-end page that runs `wp_footer`, whether or not the popup shows and even when `enabled` is
false (`40752.php:565`; as printed: `ref/live-snippets/40752-keeper.printed.html:1`).

- **Storage: `sessionStorage`, key `blkbp_attr`, value a JSON object.** There is no cookie and nothing in
  `localStorage` for attribution.
- Keys of the object:
  - `utm_source`, `utm_medium`, `utm_campaign`, `utm_term`, `utm_content`, `gclid`, `fbclid`, `gbraid`, `wbraid`:
    strings copied from the page URL's query, each cut to 120 characters. A key is written only if it is not
    already set (`if(v&&!a[n])`), so the first value seen in the tab session wins; a later page can add keys that
    were missing but never overwrites.
  - `t`: number, `Date.now()` (milliseconds) of the first page of the session.
  - `ref`: `document.referrer` of that first page, cut to 200 characters; may be `''`.
  - `land`: `location.href` of that first page, cut to 250 characters; it includes the query string, so the gclid
    and utm tags are usually inside it too.
- Example: `{"utm_source":"google","utm_medium":"cpc","gclid":"Cj0...","t":1794421185881,"ref":"https://www.google.com/","land":"https://block.ke/property/some-home/?utm_source=google&utm_medium=cpc&gclid=Cj0..."}`
- It is per tab and per session. It is written in the footer, so a script that runs earlier on the visitor's first
  page will not find it yet: **read it at submit time, not at page load.**
- Who reads it: the popup's own form (`:866`), the contact click tracker (`ref/live-snippets/40769.printed.html:44`),
  the chat gate (`ref/live-snippets/42885.printed.html:111`), and the development layout
  (`ref/dev-v1.8.2.php:8105-8110`), which reads it, adds any of the nine URL parameters not yet in it, and sets
  `land` and `ref` itself when they are missing. Nobody but the keeper writes it.
- Server side, the handlers accept `attr` as an object and keep only these keys, each through
  `sanitize_text_field` and cut to 250 characters: `utm_source`, `utm_medium`, `utm_campaign`, `utm_term`,
  `utm_content`, `gclid`, `fbclid`, `gbraid`, `wbraid`, `ref`, `land` (`40752.php:413-420`; the development snippet
  uses the same list, `ref/dev-v1.8.2.php:8812-8819`).
- Other browser storage these snippets use (do not reuse the names): `sessionStorage` `blkbp_sid` (40769's session
  id), `blkbp_lviews`, `blkbp_shown` (`40752.php:762`, `:769`, `:800`); `localStorage` `blkbp_done`,
  `blkbp_dismissed` (`:766-767`, `:813`, `:878`), `blkac_test`, `blkac_vid`, `blkac_first`, `blkac_t`
  (`ref/live-snippets/42885.printed.html:68-70`, `:80`).

### 2.3 `blockke_bp_config()`

`function blockke_bp_config()` (`40752.php:41-95`). No parameters. Returns the defaults (`:46-80`) overlaid with the
option `blockke_buyer_popup` (`:81-93`), cached in a static for the rest of the request (`:42-45`).

| Key | Default | Note |
|---|---|---|
| `enabled` | `true` | switches the popup, not the keeper and not `blockke_bp_send_zoho()` |
| `whatsapp` | `'254725937686'` | Block's number, digits only |
| `notify_emails` | a list holding one block.ke address (`:49`) | a saved list replaces the default whole (`:86-90`). The README names the three addresses block.ke has here and in `blockke_dev_page` (`ref/README-wordpress.md:293`, `:500`); I did not read the option itself |
| `reply_promise` | `'usually within 15 minutes during working hours'` | shown in the popup |
| `dismiss_days` | `14` | |
| `areas`, `budgets` | chip lists | popup only |
| `article_categories`, `hub_pages` | slug lists | where the popup shows |
| `handbook_url` | `home_url( '/buyers-handbook/' )` | |
| `privacy_url` | `home_url( '/privacy-policy-2/' )` | the development snippet uses it for its privacy link (`ref/dev-v1.9.1.php:7887-7896`) |
| `pill`, `pill_label` | `true`, `'Free price check'` | |
| `buttons` | `area_selector`, `eyebrow`, `alt_label`, `contact_url` | /neighbourhoods/ pages |
| `triggers` | `listing_scroll` 40, `listing_seconds` 45, `article_scroll` 50, `article_seconds` 60, `second_listing_seconds` 10, `exit_min_seconds` 5 | |
| `zoho` | `endpoint` `'https://crm.zoho.com/crm/WebToLeadForm'`, `xnQsjsdp` `''`, `xmIwtLD` `''`, `map` `array()` | a saved `map` replaces the default whole (`:91-93`) |

The development snippet's notify list falls back to this one: its own `notify_emails`, else
`blockke_bp_config()['notify_emails']`, else one hard-coded block.ke address, plus the listing agent's email when
`copy_agent` is on (`ref/dev-v1.8.2.php:8635-8655`).

### 2.4 The helpers other snippets already call

All are defined inside `if ( ! function_exists( 'blockke_bp_config' ) ) { ... }` (`40752.php:39`), so they exist as
a set or not at all. The live development snippet calls all of them except `blockke_bp_phone_display()` behind
`function_exists()`, and `blockke_bp_config()` too (`ref/dev-v1.8.2.php:6742-6743`, `:8641-8642`); the chat gate
calls the two phone helpers the same way (live source 42885, `blockke_ac_phone()`, `blockke_ac_phone_display()`).

| Function | Lines | In, out |
|---|---|---|
| `blockke_bp_normalize_phone( $raw )` | `:189-202` | digits only; `07..`/`01..` (10 digits), 9 digits starting 7 or 1, and `2540...` (13 digits) become `254...`; returns `''` if not 9 to 15 digits. Used at `ref/dev-v1.8.2.php:493-496` |
| `blockke_bp_phone_display( $digits )` | `:204-209` | `254725937686` becomes `+254 725 937 686`; anything else `+digits` |
| `blockke_bp_channel( $attr )` | `:212-248` | array of the attribution keys; returns `'Google Ads'`, `'Meta'`, `'Social: tiktok'` (and youtube, linkedin, x, twitter), `'Direct message / email'`, `'Campaign: src / medium'`, `'Organic search'`, `'Meta (organic)'`, `'Referral: host'` or `'Website direct'`. Used at `ref/dev-v1.8.2.php:8820` |
| `blockke_bp_lead_source( $channel, $attr )` | `:251-284` | returns a value of Block's Zoho *Lead Source* picklist: `'Google Ads'`, `'Meta Ads'`, `'Instagram'`, `'Facebook'`, `'TikTok'`, `'YouTube'`, `'Linkedin'`, `'X (Twitter)'`, `'WhatsApp'`, else `'Website - block.ke'`. Used at `ref/dev-v1.8.2.php:8821` |
| `blockke_bp_send_zoho( $lead )` | `:287-350` | see 2.5 |

### 2.5 `blockke_bp_send_zoho()`

**Signature:** `function blockke_bp_send_zoho( $lead )` (`40752.php:287`). One parameter, an array, no type hints,
no defaults.

**Returns** a short status string for the log (`:291`, `:345-349`):
`'skipped (no tokens)'`, `'error: ' . <WP_Error message>`, `'ok (http 200)'` (any code from 200 to 399 reads
`'ok (http CODE)'`), or `'http CODE'`.

**What must be configured for it to send:** the option `blockke_buyer_popup` must hold non-empty
`zoho['xnQsjsdp']` and `zoho['xmIwtLD']`, the two tokens of the Zoho Web-to-Lead form (`:288-292`). Nothing else:
it does not look at `enabled`. `zoho['endpoint']` defaults to `https://crm.zoho.com/crm/WebToLeadForm`. With no
tokens it returns before touching `$lead`. Whether the tokens are set on block.ke: not read (see section 11); the
Enquiries page shows each lead's Zoho answer (`ref/inbox-v1.1.1.php:394`, `:417`).

**The array keys it reads, all without `isset()`** (`:293-315`). Pass all fifteen, with these types, or PHP 8
raises warnings (and `implode()` on a missing `areas` is a fatal TypeError):

| Key | Type | Used as |
|---|---|---|
| `name` | string, not empty | split on the first space into `{first}` and `{last}`; one word fills both (`:293-295`) |
| `phone` | string of digits in international form, no plus | `{phone}` is `'+' . $lead['phone']` (`:300`) |
| `email` | string, `''` if none | `{email}` |
| `areas` | **array** of strings, may be empty | `{areas}` joined with `', '`; also the fallback of `{unit_or_areas}` |
| `budget` | string | `{budget}` |
| `unit` | string | `{unit}`, and `{unit_or_areas}` when not empty |
| `alerts` | bool | `{alerts}` reads `'Yes'` or `'No'` |
| `channel` | string | `{channel}` |
| `campaign` | string | `{campaign}` |
| `page` | string (URL) | `{page}` |
| `page_title` | string | `{page_title}` |
| `description` | string, the full plain-text enquiry | `{description}` |
| `lead_source` | string | `{lead_source}` |
| `submarket` | string | `{submarket}` |
| `buyer_type` | string | `{buyer_type}` |

**The Zoho field mapping** (`:316-335`): the request body is `xnQsjsdp`, `xmIwtLD`, `actionType` `'TGVhZHM='`,
`returnURL` `home_url( '/' )`, `zc_gad` `''`, plus one entry per row of the map, each value passed through
`strtr()` with the placeholders above. The map is `zoho['map']` from the option when that is a non-empty array,
else this default:

```
'First Name' => '{first}',  'Last Name' => '{last}',  'Phone' => '{phone}',  'Mobile' => '{phone}',
'Email' => '{email}',  'Company' => 'Individual buyer',  'Lead Source' => 'Website popup',
'Description' => '{description}'
```

The map in force on block.ke was not read (it is in the option). The chat gate, which posts to the same Zoho form
with the same tokens, shows which field names that form takes: `Lead Source`, `LEADCF9` (enquiry type), `LEADCF10`
(submarket), `LEADCF2` (area), `LEADCF7` (buyer type), `Description` (live source 42885, `blockke_ac_send_zoho()`).

**The request** (`:336-344`): `wp_remote_post()` to `$z['endpoint']` with `timeout` 12, `redirection` 0, the body
above and the snippet's own client name (`:342`). It is synchronous: a slow Zoho holds the visitor's request for up
to 12 seconds.

**How the callers fill it.** The popup itself: `:457-489`. The development snippet: `ref/dev-v1.8.2.php:9013-9036`,
with `submarket` one of the 23 Zoho Submarket values (`40752.php:457`; "Upper Hill" is sent as `Upperhill`), else
`'Other'`, or `'-None-'` when there is no area; `buyer_type` `'Local'` when the phone starts `254`, else
`'Diaspora'`; `areas` the one area in an array; `budget` `''`; `alerts` `false`; `unit` the listing name.

**Rental leads.** The map has no placeholder for *Company*, so v1.9.1 changes a rental lead's post in flight: it
adds an `http_request_args` filter just before the call and removes it just after, in a `finally`; the filter acts
once and only on a post to the popup's own endpoint, turns `Company` `'Individual buyer'` into `'Individual
tenant'`, and, if its config `zoho_enquiry_field` names a Zoho field, sends that field as `'Rental'`
(`ref/dev-v1.9.1.php:10508-10535`, `:10835-10863`; README `ref/README-wordpress.md:160`, `:304`, `:398`). The owner
approved that behaviour for the rent mode. Note the chat gate already files rental chats differently: `LEADCF9` =
`'Rent - Residential'` or `'Rent - Commercial'` (section 7). Which one Zoho should get from the rental page is an
open question (section 11).

**Checked offline.** I ran the saved source under PHP 8.3 with WordPress stubbed out and nothing sent
(`/tmp/claude-0/-workspace-Loyd2024/e24ac003-f571-5336-9956-1fd11a814c7d/scratchpad/rp-live-tools/harness_40752.php`).
Results: no tokens returns `'skipped (no tokens)'` even for an empty array; with tokens and the fifteen keys it
posts the body described above and returns `'ok (http 200)'`; a 302 gives `'ok (http 302)'`, a 500 gives
`'http 500'`, a transport error gives `'error: ...'`; with tokens and no `areas` key it throws `TypeError:
implode(): Argument #1 ($array) must be of type array, string given`; a saved `map` replaces the default map
whole, and a saved `notify_emails` replaces the default list. The helpers gave: `0725 937 686`, `725937686` and
`2540725937686` all become `254725937686`; `12345` becomes `''`; an attribution with a `gclid` gives channel and
lead source "Google Ads"; the email feeds' tags (`utm_source=zoho-ma`, `utm_medium=email`) give "Direct message
/ email" and "Website - block.ke"; nothing at all gives "Website direct" and "Website - block.ke".

### 2.6 The popup's own endpoint, for comparison

`POST /wp-json/block/v1/buyer-lead` (`40752.php:548-561`, handler `:352-546`): honeypot field `website`, answered
as a success (`:362-364`); client IP from `HTTP_CF_CONNECTING_IP`, then `REMOTE_ADDR` (`:365-371`: the site is
behind Cloudflare, so `REMOTE_ADDR` alone would put every visitor in one bucket); rate limit 6 an hour per IP in
the transient `blkbp_rl_{md5 of IP}` (`:372-377`); then Zoho (`:489`), email (`:491-507`) and last the log in the
option `blockke_buyer_leads` (`:509-532`). It saves last, which is the order rule 8 of the brief forbids for the
rental endpoint. On success its script fires `generate_lead` and Meta `Lead` (`:883-884`); the Google Ads
conversion comes from 33892's `fetch` hook (section 4).

## 3. (b) 40769, Contact click tracking and WhatsApp prefill v1.0

Purpose: on every listing page, for sale or for rent, it rewrites the text of WhatsApp contact links to one standard
message, and logs every tap on a call, WhatsApp or email link. As printed on a rent-only listing:
`ref/live-snippets/40769.printed.html` (cited below as `40769p:LINE`).

### 3.1 When it runs

- `blockke_ct_render()` on `wp_footer` priority 41. It prints only when its config `enabled` is on and
  `blockke_ct_context()` gives a listing: the request is `is_singular( 'estate_property' )`, not `is_admin()`, not
  `is_preview()`, and `$_GET` holds neither `elementor-preview` nor `vcv-editable` (live source 40769,
  `blockke_ct_context()`).
- The rental layout is drawn at the listing's own URL, so 40769 will run on it, including on `?bkr=1` and
  `?bkr_key=...` previews (`is_preview()` is WordPress's own draft preview, not ours).
- Config: option `blockke_contact_tracking` over the defaults of `blockke_ct_config()`: `enabled` true, `prefill`
  true, `brand` `'Block'`, `agents` (digits to name; default one entry, `254725937686` to "Loyd"), `digest_enabled`,
  `digest_emails`, `raw_limit` 500, `daily_keep` 120.

### 3.2 What it knows about the listing (`window.BLOCKCT`)

Printed as `window.BLOCKCT = { endpoint, listing: { id, title, url, price, action, area, agent }, prefill, msg, ref }`
(`40769p:1`). Built in PHP by `blockke_ct_context()` and `blockke_ct_prefill()` (live source 40769):

- `action`: `'sale'` if the listing has `for-sale`; else `'rent'` if it has `for-rent`; else `'other'`. A listing
  tagged both ways is `'sale'`.
- `price`: `'KES ' . number_format( property_price )` when the price is above 0; **for a rental with a price of
  5,000 or more it always adds `/month`**, whatever the after-price label says; for an off-plan sale it adds
  `from ` in front. No price gives `''`.
- `agent`: the title of the agent post in the listing's `property_agent` field; the agent's number is that post's
  `agent_mobile`, else `agent_phone`.
- `ref`: `'Ref W-' . <listing ID>`. **`W-{ID}` is the site-wide listing reference**: the chat gate uses it
  (`ref/pages-final/45577.html:7396`, `"ref":"W-45577"`), the Zoho feed uses it (live source 42369, `'ref' => 'W-' .
  $pid`), and the lead emails print it (`ref/dev-v1.9.1.php:10790`).
- `msg`, the standard WhatsApp text: `Hi {brand}, {verb} {title}{ (price)}. Ref W-{id}. {url}`, where the verb is
  "I'd like to enquire about" for a rental and "I'm interested in" otherwise, and the bracket is left out when
  there is no price.

Live examples (the `window.BLOCKCT` line of each saved page):

| Listing | The listing says | 40769 says | Where |
|---|---|---|---|
| 45577 | KES 135,000 per month | `(KES 135,000/month)` | `ref/pages-stop/45577.html:7866` |
| 44020 | KES 6,500 **per day** | `(KES 6,500/month)` | `ref/pages-final/44020.html:7134` |
| 34171 | USD 2,600 per month in the title; price field 337,000 | `(KES 337,000/month)` | `ref/pages-final/34171.html:7630` |
| 36834 | KES 125 per sq ft | `(KES 125)` | `ref/pages-final/36834.html:8862` |
| 44048 | 70,000, no label | `(KES 70,000/month)` | `ref/pages-final/44048.html:7230` |
| 36816 | price field 0 | no bracket | `ref/pages-final/36816.html:8671` |

So the standard message is wrong for a rent by the day and bare for a rent per square foot. That is why the rental
page's own WhatsApp links must opt out (3.3), and it is a fault in 40769 that the rental snippet cannot repair for
the links outside its own markup (section 11).

### 3.3 Which WhatsApp links it rewrites, and how a link opts out

`rewrite(a)` (`40769p:8-22`) is run over every `a[href]` in the document when the script starts (`40769p:23-24`)
and over every element added to `document.body` later (a MutationObserver on `childList`, `subtree`; `40769p:25`).
It covers the whole page: header, site footer, theme agent card, theme mobile bar and whatever the page's template
prints. A link is rewritten when all of these hold:

1. `BLOCKCT.prefill` is true (`40769p:9`);
2. its `href` attribute starts with `http(s)://api.whatsapp.com/`, `wa.me/`, `web.whatsapp.com/`,
   `chat.whatsapp.com/`, or `whatsapp:` (`isWa`, `40769p:6`, `:10`);
3. the link names a phone: `wa.me/` followed by 6 to 16 digits (a leading plus is allowed), or a `phone=` parameter
   (`waPhone`, `40769p:7`, `:11`). Share links such as `https://wa.me/?text=...` have no number and are left alone;
4. **it has no `data-blkct` attribute with a value** (`if(a.getAttribute('data-blkct')){return;}`, `40769p:12`).

**Opt-out: put `data-blkct="1"` on the link.** Any non-empty value works; an empty attribute does not. 40769 sets
the same attribute on every link it has rewritten (`40769p:13`), so the attribute means "leave this alone", not
"ours".

What a rewrite does (`40769p:14-21`): reads the link's `text` parameter; if that text contains "viewing" (any
case) it keeps the wording and appends ` Ref W-{id}.` unless `Ref W-{id}` is already in it (`40769p:17`); otherwise
it replaces the text with `BLOCKCT.msg` (`40769p:18`); then rebuilds the `href` with the new `text` last.

The approved rental wording already satisfies both tests, so it survives even without the marker: "Hi Block, I'm
interested in renting Runda Paradise 1 (KES 200,000 a month). Is it still available, and when could I arrange a
viewing? Ref W-43543." (`ref/README-wordpress.md:159`; built at `ref/dev-v1.9.1.php:1901-1907`). v1.9.1 uses all
three: the word "viewing", its own `Ref W-{id}`, and `data-blkct="1"` on every WhatsApp link, including the ones its
script fills in later (`ref/dev-v1.9.1.php:1988`, `:1998`, `:7111`, `:7113`, `:8069`, `:8075`).

The observer watches added elements only, not attribute changes. A link that is in the HTML with `href="#"` and
gets its WhatsApp address from script later is not looked at again, but do not rely on that: put `data-blkct="1"`
in the markup of every WhatsApp link, placeholders included.

### 3.4 Which taps it tracks

One click listener on `document`, in the capture phase (`40769p:36-51`). It takes `event.target.closest('a[href]')`
and reads the `href` attribute:

| Link | Counted as | `number` |
|---|---|---|
| `tel:...` | `call` | the digits of the href (`40769p:40`) |
| a WhatsApp link with a phone number (same tests as 3.3) | `whatsapp` | the digits (`40769p:41`) |
| `mailto:...` | `email` | the address (`40769p:42`) |
| anything else, WhatsApp share links, buttons that are not links | not tracked | |

There is **no opt-out from tracking**: `data-blkct` only stops the rewrite. So every `tel:`, `wa.me` and `mailto:`
link the rental page prints is tracked without the page doing anything, provided it is a real `a` element with the
address in its `href`.

For each tap it sends (`40769p:44-50`):

- a beacon (`navigator.sendBeacon`, else `fetch` with `keepalive`) to `POST /wp-json/block/v1/contact-click` with
  `{ type, number, where, listing: BLOCKCT.listing, attr: <blkbp_attr>, page: location.href, sid, device }`. The
  server keeps the last 500 in the option `blockke_contact_clicks` and daily totals in `blockke_contact_daily`,
  maps the number to an agent name through config `agents` (an unlisted number reads `unknown (digits)`), and adds
  the channel and Zoho lead source with the popup's helpers (live source 40769, `blockke_ct_handle()`,
  `blockke_ct_record()`). Rate limit: 120 an hour per IP.
- **GA4:** `gtag('event', 'contact_click', { method, listing_id, listing_title, listing_action, agent_number,
  placement })`, only if `window.gtag` is a function; `method` is `call`, `whatsapp` or `email`; `listing_id` is a
  string; `listing_action` is `rent` on a rental (`40769p:49`).
- **Meta:** `fbq('track', 'Contact', { content_name: <listing title>, content_category: <action> })`, only if
  `window.fbq` is a function (`40769p:50`).
- **It pushes nothing to `dataLayer` itself.**

`where` / `placement` comes from the link's surroundings (`40769p:26-35`), first match wins:
`.mobile_agent_area_wrapper` gives `mobile_bar`; `.realtor-contact-buttons`, `.agent_unit`,
`.agent-listing-img-wrapper`, `.property_agent_wrapper` give `agent_card`; `.blockke-cta-bar` gives `cta_bar`;
`.blockke-wa-card` gives `wa_card`; `footer`, `.footer`, `#footer`, `.bkef-crow`, `.bkef` give `footer`; `header`,
`.header_wrapper`, `.header_phone`, `.mobile_header` give `header`; `#blkbp` gives `popup`; else `other`.
Links inside `#bkr` will read `other`, unless they sit inside a `header` or `footer` **element**, which would file
them as the site header or footer. Use `div` or `section` for the parts of the rental page that hold contact links.

`sid` is a per-session id kept in `sessionStorage` `blkbp_sid` (`40769p:5`). `device` is `mobile` when
`(pointer:coarse)` matches.

Admins can read the log at any front-end URL with `?blockreport=7` (`&format=json`); a digest is emailed weekly
(live source 40769).

## 4. (c) 33892 and 33869, the Google Ads conversion

### 4.1 33892: `window.blockkeLeadConversion( kind, extra )`

HTML snippet printed in the footer of every page. Whole text: `ref/live-snippets/33892.printed.html` (cited as
`33892p:LINE`). **This is the function a page calls after a successful enquiry** (its own header says so,
`33892p:8`).

- **Name:** `window.blockkeLeadConversion` (`33892p:44`).
- **Parameters:** `kind`, one of `'lead_form'`, `'whatsapp'`, `'call'` (the keys of `LABELS`, `33892p:23`);
  `extra`, an optional object whose own properties are copied onto the conversion payload (`33892p:36`).
- **Returns** `true` when it sent, `false` when there is no label for that kind or that kind was already sent on
  this page view (`33892p:32-34`). So **each kind counts once per page load**, however often it is called.
- **What `blockkeLeadConversion('lead_form')` sends:**
  1. Zoho Marketing Automation goal: `window.pagesense.push(['trackEvent', 'form_lead'])`, once per page
     (`33892p:28-31`);
  2. Google Ads: `gtag('event', 'conversion', { send_to: 'AW-18119754545/zvMECJSf3aIcELGGlsBD', value: 1.0,
     currency: 'USD' })` plus anything in `extra` (`33892p:35-38`); if `gtag` is not a function it pushes the same
     to `dataLayer` as an array (`33892p:39`);
  3. `dataLayer.push({ event: 'blockke_lead', lead_type: kind })` (`33892p:41`).
- **It does not send a Meta `Lead`** for a caller. Its `metaLead()` is private and used only for the theme's
  contact forms (`33892p:46-57`, `:70`). The popup and the development layout send their own
  (`40752.php:884`; `ref/dev-v1.8.2.php:8181`).
- **It does not send GA4 `generate_lead`.** Callers do (`40752.php:883`; `ref/dev-v1.8.2.php:8179`).

What it does without being called:

- wraps `XMLHttpRequest.prototype.send`: a theme contact form (`action=wpestate_ajax_agent_contact_form`) answered
  with `sent` fires `lead_form` and the Meta lead (`33892p:60-77`);
- wraps `window.fetch`: a POST whose URL contains `/wp-json/block/v1/buyer-lead` answered 2xx fires `lead_form`
  (`33892p:80-93`). **The rental endpoint `/wp-json/block/v1/rental-lead` does not match, so the rental page must
  make the call itself**, as the development layout does (`ref/dev-v1.8.2.php:8180`);
- one click listener on `document`, capture phase (`33892p:97-105`): the first tap on a WhatsApp link with a number
  (`wa.me/` and at least 6 digits, or `whatsapp.com/...phone=`) fires the `whatsapp` conversion
  (`AW-18119754545/9YcqCNmOwoEdELGGlsBD`); the first tap on a `tel:` link fires the `call` conversion
  (`AW-18119754545/LbP0CNyOwoEdELGGlsBD`). Both labels are set, so both are live. **The rental page's WhatsApp
  and phone links are therefore already counted; it must not report them a second way.**

The live development layout's three lines after a lead is accepted (`ref/dev-v1.8.2.php:8179-8181`):

```
track('generate_lead',{lead_source:p.source,lead_unit:p.unit});
try{if(typeof window.blockkeLeadConversion==='function'){window.blockkeLeadConversion('lead_form');}}catch(x){}
try{if(typeof window.fbq==='function'){window.fbq('track','Lead',{content_name:C.name});}}catch(x){}
```

Its `track()` sends `gtag('event', name, params)` when `gtag` is a function, else pushes `{ event: name, ...params }`
to `dataLayer`, and adds `development` (the slug) and `listing_id` (a string) to every event
(`ref/dev-v1.8.2.php:8111-8112`). It also sends GA4 `click_whatsapp` and `click_call` on its own links
(`ref/dev-v1.8.2.php:8113-8114`), next to 40769's `contact_click`.

Where the globals come from on a listing page: `gtag` and `dataLayer` from Site Kit
(`ref/pages-stop/45577.html:132-139`), with the Ads tag configured at `:146-152`; `fbq` from the Meta pixel plugin
(`:766-776`); `pagesense` from the PageSense script (`:838`).

### 4.2 33869: `gtag_report_conversion( url )`. Do not call it

HTML snippet printed in the head (`ref/live-snippets/33869.printed.html:1-18`). Its title on the site says "defines
function only; nothing calls it". It is Google's stock snippet: it sends the **same** conversion label as
`lead_form` (`AW-18119754545/zvMECJSf3aIcELGGlsBD`, `:11`) with no once-per-page guard, calls `gtag` without
checking it exists (`:10`), and, given a `url`, sends the browser there in the callback (`:5-9`). Calling it as
well as `blockkeLeadConversion('lead_form')` would count one enquiry twice.

## 5. (d) 42369, CRM listings sync v1: Sold, Let, Available

Purpose: a feed Zoho CRM reads (`GET /wp-json/block/v1/listings`) and a signed call Zoho makes to mark a listing
(`POST /wp-json/block/v1/listing-status`). Live source 42369; nothing is printed on pages.

**How a listing is marked** (`blockke_crm_listing_status()`): both a taxonomy term and post meta.

- Request: `id`, `status` (`sold`, `let` or `available`, any case), `ts` (within 15 minutes), `sig` (HMAC-SHA256 of
  `id|status|ts` with the secret in the option `blockke_crm_sync_secret`).
- **Taxonomy `property_status`, term slugs `sold` and `let`.** The handler first removes both terms from the
  listing, then:
  - `sold`: adds the term with slug `sold` (it must already exist);
  - `let`: adds the term with slug `let`; if there is no such term it creates it: name `Let`, slug `let`;
  - `available`: adds nothing, so the listing ends with neither term.
  Other status terms on the listing (Complete, Furnished and so on) are kept: the terms are added with append on.
- **Post meta:** `blockke_crm_status` = `sold`, `let` or `available` (lower case) and `blockke_crm_status_at` = the
  UTC time, `Y-m-d H:i:s`.
- Then `clean_post_cache()`, a best-effort Cloudflare purge of the listing's URLs, and a row in the option
  `blockke_crm_status_log` (last 25).
- It does not change `post_status` and does not save the post. The listing stays published at its URL, so **the
  rental page will be asked to draw a home that has been let**.

**Which signal to trust.** The term. An editor can add or remove *Let* or *Sold* by hand in the listing editor,
which leaves the meta stale or absent; the meta is only what Zoho last said. The development snippet reads only the
terms: `has_term( array( 'sold', 'let' ), 'property_status', $id )` (`ref/dev-v1.9.1.php:12744`; and `'terms' =>
array( 'sold', 'let' ), 'operator' => 'NOT IN'` in its queries, `:4237`, `:6789`, `:11608`). The popup checks
`sold` the same way (`40752.php:118`). For the rental page: **Let = `has_term( 'let', 'property_status', $id )`,
Sold = `has_term( 'sold', 'property_status', $id )`, Available = neither.**

**Which statuses exist.**

- From Zoho: `sold`, `let`, `available`, nothing else.
- The snippet's admin page says Zoho marks single-unit listings. The endpoint itself does not check; Zoho goes by
  the feed's `single_unit` flag (`blockke_crm_single_unit()`: false when the listing has sub-units, a project
  term, a plural word such as "apartments" or "offices" in the title, or "from" / "starting"). So a listing
  for several homes is not expected to be marked Let by Zoho; an editor can still add the term by hand.
- `property_status` terms carried by listings in today's search feed (`ref/listings.json`, 1,009 listings): "Off-Plan
  / Ongoing" 680, "Complete" 284, "Furnished" 13, "Ongoing" 3, "Sold" 2, no status 36. Among the 129 rent-only
  listings: "Complete" 109, "Furnished" 9, "Off-Plan / Ongoing" 1, none 15 (a listing can carry two). **No listing
  carries "Let" today**; whether the term exists yet is not known (section 11). The theme has its own sold marker
  setting, `wpestate_mark_sold_status` = `77` (`ref/theme-options.json`).
- Caution: the search feed is rebuilt only when a listing is saved (section 6.2), and this call does not save, so a
  home Zoho has just marked Let can still read as available in `listings.json` and on /search.
- A pointer for going live, not a finding: this snippet purges Cloudflare for a listing's URLs when its status
  changes, and its feed sends no-cache headers for Cloudflare and LiteSpeed. That suggests listing pages may be
  cached in front of WordPress, in which case cached classic pages would outlive the moment the rental layout is
  switched on, until purged. Whether HTML is cached at the edge was not checked here; see the site-shell notes.

The feed `GET /wp-json/block/v1/listings` is built on each request with no cache and no hooks: per listing `id`,
`ref` (`W-{id}`), `status` (post status), `modified`, and for published ones `title`, `url`, `price` (integer or
null), `price_label` (the after-price label as stored), `action`, `categories`, `area`, `city`, `county`,
`project`, `bedrooms`, `size_sqft`, `web_status` (the status term names), `single_unit`, and `crm` values for
Zoho (`Listing_Type` is `To Let` for rent only, `For Sale or To Let` for both). It also defines
`blockke_crm_submarket( $area )`, which maps an area name to the Zoho Submarket value (more aliases than the
popup's list: Brookside and Peponi to Westlands, Garden City and Thome to Thika Road, Ridgeways to Kiambu Road),
returning `'Other'` for an unknown area and `''` for none.

## 6. (e) The three feeds: 42215, 39193, 42658

None of them prints anything on a listing page. None of them reads custom fields other than the ones named below,
so **a new custom field `bke_rent_layout` and a new option `blockke_rent_page` break nothing in any of them**.

### 6.1 42215, Front desk listings feed v1.1 (for the AI front desk)

- `GET /wp-json/block/v1/listings-feed` (paged JSON) and `?format=files` (Markdown knowledge files, one per purpose
  and area, plus an index). Live source 42215.
- **Rebuild:** on request only. The whole feed is cached for 15 minutes in the transient `blockke_fd_feed_v11`;
  `&fresh=1` skips the cache. Its only hook is `rest_api_init`: **no `save_post`, no meta hooks**.
- Reads, by direct SQL, exactly these meta keys: `property_price`, `property_label`, `property_label_before`,
  `property_second_price`, `property_second_price_label`, `property_label_before_second_price`,
  `property_bedrooms`, `property_bathrooms`, `property_size`, `property_agent`; and the taxonomies
  `property_action_category`, `property_category`, `property_city`, `property_area`, `property_status`,
  `property_completion`, `property_project`.
- **How it words a rent** (`blockke_fd_price_text()`, `blockke_fd_money()`): `{label before} {currency}
  {1,234,567} {label after}`, with the currency from the theme option (KES) and a short form in brackets for round
  figures of a million or more. A rental reads "KES 135,000 per month". No price reads **"Price on request"**. A
  rent-only listing whose labels name no unit (no month, year, week, night, day, sq, acre, "per " or "/") reads
  "KES 70,000 **(rent as listed, period not stated)**" and is listed under `flags.rent_no_period`. The index tells
  the assistant: "For rentals the figure is the rent as written on each listing."
- Status: only the slug `sold` is treated as gone ("Availability: SOLD, no longer available"). A *Let* term would
  appear in "Status: Let" but the listing would still count as available in the index.

### 6.2 39193, Listings feed for search v1 (the file behind /search)

- Writes `wp-content/uploads/blockke-search/listings.json` (the local copy is `ref/listings.json`: `v`, `generated`,
  `count`, `items`). Live source 39193.
- **Rebuild:** `save_post_estate_property` and `trashed_post` (for a listing) set the option `blockke_feed_force`
  to the current time. On the next request, at `init` priority 40, the snippet compares it with
  `blockke_feed_stamp` and, if they differ or the file is missing, rebuilds the whole file (all published listings:
  9 meta reads, 7 term queries and a thumbnail each) inside that request.
- **Not on meta updates and not on term changes.** `update_post_meta()` and `wp_set_object_terms()` alone do not
  mark the feed stale. So setting `bke_rent_layout` through the connector (meta only) rebuilds nothing; setting it
  in the editor and pressing Update rebuilds the feed once, as any edit does. Zoho's Sold / Let call does not
  rebuild it (section 5).
- Consequence for the rental snippet: **never save a listing (`wp_update_post`, `wp_insert_post`) from a page view
  or from the enquiry endpoint**; each save makes some later visitor's request rebuild 1,009 listings.
- **How it words a rent:** it does not. It stores `p` (the price as a number) and `pl` (the after-price label as
  stored); /search does the wording. It does not carry the before-price label or any rental custom field.
- The after-price labels of the 129 rent-only listings in today's feed: "per month" 97, none 12, "per sq ft per
  month" 6, "per sqft" 5, "per sq ft" 5, "Rent on application" 2, "per day" 1, and one long label, "USD 1.30 per sq
  ft per month plus VAT" (listing 36816, price field 0).

### 6.3 42658, Email match feeds v1 (RSS for Zoho Marketing Automation)

- `https://block.ke/?block_feed=matches&area={key}&deal=sale|rent&days=7&limit=6`, answered at
  `template_redirect` priority 0 and then `exit`. Live source 42658.
- **Rebuild:** on request; each feed is cached for 15 minutes in a transient `blockke_mf_{md5}`. **No `save_post`,
  no meta hooks.**
- A rent feed takes listings with the term `for-rent` (so also those tagged both ways), newest first, and drops
  any whose title says "for sale".
- **How it words a rent** (`blockke_mf_price()`): `KES 135,000` plus the after-price label, so "KES 135,000 per
  month", "KES 6,500 per day"; with **no label it adds `/month`**: "KES 70,000/month"; a before-price label goes in
  front with a capital ("From KES 30,000 per month"); no price reads **"Price on request"**. Always "KES".
- **Its links carry utm tags:** `utm_source=zoho-ma&utm_medium=email&utm_campaign=matches-{area}-{deal}&utm_content=listing-{ID}`.
  Tenants arriving from these emails land on the rental page with that query string; the keeper stores it and
  `blockke_bp_channel()` files it as "Direct message / email" (`40752.php:228-230`). The rental page must render
  normally with any query string, and must not redirect it away.

### 6.4 The same rent, as each place words it

| Listing | Price field, label | 40769 (WhatsApp text, click log, GA) | 42215 (AI front desk) | 42658 (emails) | Rental page, rule 12 and `ref/README-wordpress.md:162` |
|---|---|---|---|---|---|
| 45577 | 135,000, "per month" | KES 135,000/month | KES 135,000 per month | KES 135,000 per month | KES 135,000 a month |
| 44048 | 70,000, no label | KES 70,000/month | KES 70,000 (rent as listed, period not stated) | KES 70,000/month | KES 70,000 a month |
| 44020 | 6,500, "per day" | **KES 6,500/month** | KES 6,500 per day | KES 6,500 per day | KES 6,500 a day |
| 36834 | 125, "per sq ft" | KES 125 | KES 125 per sq ft | KES 125 per sq ft | KES 125 per sq ft a month |
| 34171 | 337,000, "per month" (USD 2,600 in the title) | KES 337,000/month | KES 337,000 per month | KES 337,000 per month | by the `bke_rent_usd` rules |
| 36816, 35986, 43656 | 0 | no figure | Price on request | Price on request | Rent on request |

(Price fields and labels: `ref/listings.json`. The chat gate's page context carries the bare figure, "KES 135,000",
`ref/pages-final/45577.html:7396`.)

What to take from it:

- "a month", "per month" and "/month" are the same fact; the rental page's wording does not contradict the feeds.
- The figure must be the price field. If the rental page shows a KES figure for a dollar rent, it should be the
  price field's figure, since the emails and the AI front desk will quote "KES 337,000 per month" for 34171.
- "Rent on request" (page) and "Price on request" (feeds) are the same fact. The one real difference: a monthly
  figure over KES 2M with no label reads "Rent on request" on the page, while the feeds will still quote the
  figure. It is an edge case; note it, do not chase it.
- A rent with no label: the page says "a month" (owner-approved, README line 162); the emails agree; the AI front
  desk says the period is not stated. Not a contradiction, but the fix is to label those listings.
- 40769's "/month" on a rent by the day is wrong today on the live site, independent of this project.

## 7. (f) 42885, AI chat gate v1.0, and the other floating things

Purpose: an "Ask Block" launcher that asks for name, WhatsApp number and email, sends that as a lead (Zoho, email,
log), then loads the Synaplan chat widget. As printed: `ref/live-snippets/42885.printed.html` (cited as
`42885p:LINE`). Hook: `wp_footer` priority 30.

### 7.1 Who sees the launcher today

- Config option `blockke_ai_chat`, key `mode`: `'test'` (the default), `'live'` or `'off'` (live source 42885,
  `blockke_ac_config()`).
- **The mode on block.ke today is `test`**: the page prints `window.BLKAC_CFG={"mode":"test",...}`
  (`ref/pages-stop/45577.html:7800`; `ref/pages-final/45577.html:7396`).
- In test mode the launcher is in the HTML of every page but hidden (`class="blkac-hidden"`, `42885p:39`,
  `42885p:6`). The script shows it only to a logged-in administrator or to a browser that once opened a page with
  `?blockchat=1` (kept in `localStorage` `blkac_test`; `?blockchat=0` clears it) (`42885p:68-71`, `:78`). For those
  testers it also hides the Zoho SalesIQ float, with `body.blkac-test` (`42885p:28`, `:75`).
- So **ordinary visitors do not see "Ask Block" today; they see the Zoho SalesIQ float.** If the owner sets `mode`
  to `live`, every visitor gets the launcher, and SalesIQ is left as it is.

### 7.2 The launcher: position, size, z-index

All from `42885p:2-6` and `42885p:35-37`. Sizes are worked out from the CSS, not measured in a browser.

| | Desktop, 992px and wider | Phones and tablets, 991px and narrower |
|---|---|---|
| Position | `position:fixed; right:16px; bottom:16px` | `right:16px; bottom:calc(76px + env(safe-area-inset-bottom))` |
| Box | pill, `padding:12px 18px 12px 14px`, `font-size:14px`, `line-height:1.2`, `border-radius:999px`; a 9px gold dot, an 8px gap and the words "Ask Block" | `padding:11px 16px 11px 12px`, `font-size:13.5px` |
| Size | about 41px tall, about 115px wide | about 38px tall, about 108px wide |
| z-index | 999985 | 999985 |
| Colours | background `#0d2440`, white text, shadow `0 10px 30px rgba(13,36,64,.35)` | same |

- The 76px on small screens is there to clear the theme's mobile agent bar; the popup's pill uses the same figure
  on the left (`40752.php:693-695`).
- Its dialog: backdrop z-index 999990 (`42885p:7`), dialog 999991, centred, `width:min(460px, 100% - 32px)`
  (`42885p:8`); at 640px and below it is a bottom sheet (`42885p:30-34`). While open, `body.blkac-lock` stops
  scrolling (`42885p:29`).
- Once a chat starts, the launcher is hidden and the Synaplan widget takes over at `position:'bottom-right'`
  (`42885p:91-93`). That widget's own button, size and z-index come from a third-party script and are not known.

### 7.3 Can a page move or hide it?

- **No server-side switch per page.** `blockke_ac_render()` skips only admin, feeds, `is_preview()`, Ajax, the
  page-builder query parameters and `mode` `off` (live source 42885). There is no filter and no `$_GET` switch
  like the popup's. Setting `$_GET['vcv-editable']` to hide it would also switch off 40769's tracking and is not
  acceptable.
- **CSS can move it.** Its rules are plain `#blkac-launch { ... }` with no `!important` on the position
  (`42885p:2`, `:36`), so a rule with one more class wins wherever it is printed:
  `body.bkr-page #blkac-launch { bottom: calc(<height of the page's own bar> + 12px + env(safe-area-inset-bottom)); }`
- **CSS can hide it**, for example while the page's own full-screen viewer or dialog is open:
  `html.bkr-lock #blkac-launch, html.bkr-lock #zsiq_float { display: none !important; }`. Hiding the chat on
  rental pages altogether is possible the same way but is an owner decision, not a technical one: it is a lead
  channel, and its copy speaks to tenants too ("Are you looking to buy or to rent?", `42885p:87`).
- The chat gate knows the page is a rental: its context sends `purpose` `rent` and `kind` `residential` or
  `commercial` (commercial when the listing has the category `commercial`, `offices` or `warehouses`), and files
  the Zoho lead with `LEADCF9` = `Rent - Residential` or `Rent - Commercial` (live source 42885,
  `blockke_ac_context()`, `blockke_ac_handle_lead()`, `blockke_ac_send_zoho()`). None of that depends on the
  template, so it keeps working on the rental layout.

### 7.4 The Zoho SalesIQ float, which is what visitors see now

- Loaded by the SalesIQ plugin (`ref/pages-stop/45577.html:6635-6643`). Its element is `#zsiq_float`. Its position,
  size and z-index are set by Zoho's script and are **not known** from any local file (section 11).
- What the other snippets do about it: the development layout lifts it above its own mobile bar,
  `html.bkd-mb-on #zsiq_float{bottom:calc(84px + env(safe-area-inset-bottom))!important;transition:bottom .3s}`,
  and hides it while one of its dialogs is open, `html.bkd-lock #zsiq_float{display:none!important}`
  (`ref/dev-v1.8.2.php:7845-7846`; the class is toggled at `:8384`). It uses `!important` to move it, which
  suggests Zoho positions the float with inline styles or its own `!important`; I could not check that. The popup
  hides it while open (`40752.php:682`).
- The live development layout has no rule for `#blkac-launch` (searched `ref/dev-v1.8.2.php` and
  `ref/dev-v1.9.1.php`: none).

### 7.5 Everything fixed to the viewport on a rent-only listing today

| Element | From | Where | z-index |
|---|---|---|---|
| `#zsiq_float` | Zoho SalesIQ | not known | not known |
| `#blkac-launch` (testers only for now) | 42885 | bottom right; 76px up at 991px and below | 999985 |
| `.mobile_agent_area_wrapper d-block d-lg-none` (agent photo, email, call, WhatsApp) | theme footer, `ref/pages-stop/45577.html:7399-7420` | shown below 992px; 40752's script treats it as a fixed or sticky bar (`40752.php:917-919`) | see the site-shell notes |
| `a.backtop`, `.prop-compare` | theme footer, `:7228`, `:7262` | see the site-shell notes | |
| `#blkbp-pill`, `#blkbp` | 40752 | **not on rent-only listings** | 999980, 999991 |

The theme footer prints its bar, back-to-top link and compare tray on the development layout too
(`ref/pages-final/45577.html:6410`, `:6444`, `:6581`), so the rental layout will get them. Ladder to stay under or
go over on purpose: 999980 pill, 999985 launcher, 999990 backdrops, 999991 dialogs, 999993 the development
layout's gallery viewer (`ref/pages-final/45577.html:1263`).

## 8. (g) 44071, Ad landing page speed v1.0

Purpose: makes the development snippet's ad landing pages lighter. Live source 44071; nothing of it is in the
saved rental pages.

**It acts only on ad landing pages.** `blockke_lps_on()` is true only when `blockke_dev_lp_target()` exists and
returns a listing, the request is not admin, Ajax or a feed, `?lp_lean=0` is not set, and config `enabled` is on
(or `?lp_lean=1` / `2`). An ad landing page is a page with the field `bke_lp_listing`, or a listing URL with
`?lp=1` that passes `blockke_dev_lp_ok()` (`ref/dev-v1.8.2.php:6765-6790`). In the live v1.8.2 that test accepts
for-sale listings and listings with no Buy/Rent term whose title does not say rent (`ref/dev-v1.8.2.php:636-646`);
v1.9.1 adds an explicit rental guard (`ref/dev-v1.9.1.php:735-751`). **A rent-only listing is never a landing
page, so 44071 changes nothing on a rental page, and nothing on an ordinary development page either.**

What it does on a landing page:

1. Dequeues (does not deregister) 38 script handles and 7 style handles the landing page never uses: Slider
   Revolution, Bootstrap JS and select, slick, owl carousel, the theme's `control`, jQuery UI pieces, fancybox,
   venobox, charts, date pickers, the theme's map scripts and Google Maps, `wpestate_property`, `ajaxcalls`,
   `wpestate_login_register`. Hooks: `wp_enqueue_scripts` 9999, `wp_print_styles` 0, `wp_print_scripts` 0,
   `wp_print_footer_scripts` 0.
2. Buffers the page from `template_redirect` priority 1 and, **only if the HTML contains the string `bkd-lpm`**
   (the landing page class on the development root, `ref/dev-v1.8.2.php:6907`): removes, by regular expression,
   the Customizer's `wp-custom-css` style, the theme's colour-variable style, the script `blockke-project-map` and
   Slider Revolution's inline setup; adds `async` to the PageSense script; adds the Montserrat stylesheet; and adds
   `<style id="bke-lps-css">.mobile_agent_area_wrapper,.prop-compare,.backtop{display:none!important}</style>`
   before the end of the head.

What a rental page might also want:

- **The hide rule.** If the rental page has its own bar on phones, it wants the theme's mobile agent bar out of
  the way, and probably the compare tray and back-to-top link: `.mobile_agent_area_wrapper`, `.prop-compare`,
  `.backtop`. 44071 does it for landing pages only, so the rental page has to print its own rule, scoped to
  `body.bkr-page`, in its own CSS. (v1.9.1 does the same on its classic rental pages: `html body.bkd-rent-cls
  .mobile_agent_area_wrapper, html body.bkd-rent-cls .blockke-cta-bar{display:none!important}`,
  `ref/dev-v1.9.1.php:2050`.) The hidden theme bar's WhatsApp link stays in the HTML and is still rewritten by
  40769; that is harmless once it cannot be seen.
- **Fewer scripts.** A listing page inside the site shell loads the whole kit: 58 external scripts and 27
  stylesheets on the classic rental page, 57 and 27 on the development layout (`ref/pages-stop/45577.html`,
  `ref/pages-final/45577.html`; all 38 script handles and 6 of the 7 style handles on 44071's list are on both).
  44071's own list cannot be copied: landing pages have no site menu, while the rental page keeps the theme
  header, search and login, which need Bootstrap, `control`, `ajaxcalls`, the autocomplete and the login script. Dequeuing the handles that only
  the classic listing body uses (maps, charts, carousels, date pickers) is an option for a later version, by
  handle, never deregistered, and only after the test site shows the header and footer still work. It is not
  needed for v1.

What it must avoid:

- **The string `bkd-lpm` anywhere in the rental page's HTML** (it is 44071's marker; the `bkd-` prefix is ruled out
  anyway).
- Its method. 44071 edits the finished page with regular expressions that match style and script tags; rule 5 of
  the brief forbids that in the rental snippet, and the site's firewall refuses such text on upload.
- Removing `wp-custom-css`. The landing pages can live without the Customizer's CSS; the rental page sits inside
  the theme header and footer, which may depend on it.
- Depending on 44071 in any way: it needs the development snippet's function, and the rental page must work when
  that snippet is absent.

## 9. (h) 45681, Segment page canonical and rental page calculator v1.0

Confirmed, live source 45681 (installed today, 2026-11-11 10:28). It does two things and nothing else:

1. Filter `template_include`, priority 99997: on a `property_area` archive whose query variable `bka_seg` is not
   empty (the area segment pages), it calls `RankMath\Paper\Paper::reset()` if that is callable, and returns the
   template unchanged. It does not touch listings.
2. Action `wp_head`, priority 99: on `is_singular( 'estate_property' )` where the listing has `for-rent` and not
   `for-sale`, it prints
   `<style id="blockke-rent-nocalc">#accordion_property_payment_calculator{display:none!important}</style>`
   (as printed: `ref/pages-stop/45577.html:926`, saved in `ref/live-snippets/45681.printed.html:1`).

On the rental layout the style element will still be printed in the head, because the test looks at the request,
not at the template. It hides an element of the theme's classic page that the rental layout does not have, so it
is harmless. One thing to know: its term test is `has_term( 'for-rent', 'property_action_category' )` with no post
ID, so it reads the global `$post` at `wp_head`. A template that changes the global post before `get_header()`
(a secondary loop without `wp_reset_postdata()`) would make it test the wrong post.

## 10. 42704, Keep ad click tags on old-address forwards v1.0

One filter, `old_slug_redirect_url`: when WordPress forwards a renamed listing's old address, the query string is
appended to the new address if it is 1,500 characters or shorter and has no tags or line breaks (live source
42704). So `gclid`, the utm tags and also `bkr`, `bkr_key`, `blockpop` and `lp` survive a renamed listing. The
rental snippet has nothing to do here. The lesson it records applies to us: a redirect that drops the query string
loses the ad click, so the rental snippet should not redirect a listing URL at all.

## 11. Not established, and how to find out

1. **The WPCode location of each snippet.** It is a taxonomy term (`wpcode_location`), which the two permitted
   tools do not return. To find out: read that taxonomy's terms for each ID, or look at the Location field on the
   snippet's WPCode screen. It does not change the contract below, which relies only on hooks.
2. **What the options hold on block.ke**: `blockke_buyer_popup` (are the two Zoho tokens set, what is in
   `zoho.map`, the notify list), `blockke_contact_tracking` (the `agents` map; whether `prefill` is still on),
   `blockke_ai_chat` beyond `mode`. To find out: the main session reads each option (the popup option holds the
   Zoho form tokens, so it should not be copied into notes). For Zoho alone, the Enquiries page is enough: a recent
   lead whose Zoho column reads "ok (http ...)" means the tokens are set; "skipped (no tokens)" means they are not.
3. **Whether the term `let` exists yet in `property_status`, and whether any rental carries it.** No listing in
   today's search feed does, but that feed lags (section 6.2). To find out: list the terms of `property_status`;
   or read `GET /wp-json/block/v1/listings` and look at `web_status`; or open Tools > Block CRM Sync, "Recent
   updates from Zoho".
4. **The size, position and z-index of the Zoho SalesIQ float (`#zsiq_float`)**, and of the Synaplan widget's own
   button once a chat has started. Both come from third-party scripts. To find out: open a live rent-only listing
   in a browser at 390px and at 1440px and read the computed style of `#zsiq_float`. The local test site cannot
   show it.
5. **What Zoho should receive for a rental enquiry.** v1.9.1 (owner-approved for the rent mode) sends Company
   "Individual tenant" and, if a field is named in its settings, "Rental" in it. The chat gate sends `LEADCF9` =
   "Rent - Residential" or "Rent - Commercial" to the same Zoho form. They may be the same Zoho field with
   different values. To find out: read the popup's `zoho.map`, look up the field behind `LEADCF9` in Zoho CRM
   (Setup > Web Forms, or the Leads field list), and ask the owner which value they filter on. Until then, do what
   v1.9.1 does and keep the field name and its value as settings.
6. **40769's "/month" on rents that are not monthly** (44020 by the day; per-sq-ft rents shown bare). It affects
   the WhatsApp links in the site header, footer and theme bar on rental pages and the click log, today, with or
   without this project. The rental snippet can only protect its own links. Fixing it means a new version of
   40769 whose price wording follows the after-price label: a decision for the main session and the owner.
7. **The order in which WPCode includes snippets of equal priority, and the hook it runs PHP snippets on.** Not
   needed if the contract is followed. To find out: the test site with WPCode installed, or WPCode's source.
8. **Whether GA4 has custom dimensions for the development layout's event parameters** (`development`,
   `listing_id`, `lead_source`), which would argue for reusing those names exactly. To find out: GA4 Admin >
   Custom definitions (the owner).

## 12. Contract for the rental snippet

Everything here follows from the sections above; the citations are there.

### 12.1 PHP: the only calls into other snippets

Five functions of 40752 (`blockke_bp_normalize_phone`, `blockke_bp_channel`, `blockke_bp_lead_source`,
`blockke_bp_send_zoho`, `blockke_bp_config`), each behind `function_exists()`, each with a fallback, **each called
only inside a hook callback or the REST callback, never while the snippet file is being included**. The live
development snippet calls the same five the same way. `blockke_bp_phone_display()` is not needed: format the
number with the snippet's own code, as the development snippet does.

**(1) Keep the buyer popup off.** In the `template_include` callback, once this request will draw the rental
layout:

```php
$_GET['blockpop'] = '0'; // 40752's own off switch, read at wp_footer 40; its attribution keeper still prints
```

No guard is needed: it is harmless when 40752 is absent. On a rent-only listing the popup is already off by its
own rule; this line protects against a later popup version, and costs nothing.

**(2) In the REST callback of `block/v1/rental-lead`, in this order:**

```php
// a. honeypot; rate limit per IP in a transient 'bkr_rl_' . md5( $ip ), with the IP taken from
//    $_SERVER['HTTP_CF_CONNECTING_IP'] first, then REMOTE_ADDR (the site is behind Cloudflare); validation.

// b. phone: same rules as every other form on the site
$phone = function_exists( 'blockke_bp_normalize_phone' ) ? blockke_bp_normalize_phone( $raw ) : blockke_rp_phone( $raw );
//    ('' means not a phone number; blockke_rp_phone() is our copy of the same rules, 40752.php:189-202)

// c. attribution: keep only these keys of the posted attr object, each sanitize_text_field() and cut to 250:
//    utm_source utm_medium utm_campaign utm_term utm_content gclid fbclid gbraid wbraid ref land
$channel     = function_exists( 'blockke_bp_channel' ) ? blockke_bp_channel( $attr )
	: ( ! empty( $attr['gclid'] ) ? 'Google Ads' : ( ! empty( $attr['fbclid'] ) ? 'Meta' : 'Website' ) );
$lead_source = function_exists( 'blockke_bp_lead_source' ) ? blockke_bp_lead_source( $channel, $attr ) : 'Website - block.ke';
$campaign    = isset( $attr['utm_campaign'] ) ? $attr['utm_campaign'] : '';

// d. SAVE the row in blockke_dev_leads now ('kind' => 'rent', 'zoho' => 'not sent', 'mail' => false, ...).

// e. Zoho, through the popup snippet when it is there
$zoho = 'skipped (price-check popup snippet not active)';
if ( function_exists( 'blockke_bp_send_zoho' ) ) {
	$flt = blockke_rp_zoho_args(); // one-shot closure: our port of blockke_dev_zoho_rent_args(), dev-v1.9.1.php:10516-10535
	add_filter( 'http_request_args', $flt, 10, 2 );
	try {
		$zoho = (string) blockke_bp_send_zoho(
			array(
				'lead_source' => $lead_source,
				'submarket'   => $submarket,        // one of the 23 Zoho values (40752.php:457), else 'Other'; '-None-' with no area
				'buyer_type'  => 0 === strpos( $phone, '254' ) ? 'Local' : 'Diaspora',
				'name'        => $name,
				'phone'       => $phone,            // digits, international, no plus
				'email'       => $email,            // '' when none
				'areas'       => $area ? array( $area ) : array(), // must be an array
				'budget'      => '',
				'unit'        => $listing_name,
				'alerts'      => false,
				'channel'     => $channel,
				'campaign'    => $campaign,
				'page'        => $page_url,
				'page_title'  => $title,
				'description' => $description,      // the same plain text as the email
			)
		);
	} catch ( \Throwable $e ) {
		$zoho = 'error: ' . $e->getMessage();
	} finally {
		remove_filter( 'http_request_args', $flt, 10 );
	}
}

// f. email the notify list and the listing's agent; g. write $zoho and the mail result onto the saved row;
// h. answer { ok: true, ... }.
```

All fifteen keys are required, with those types (section 2.5). The call can take up to 12 seconds, which is why
the row is saved first. The `catch` is our addition: the development snippet has only `try` / `finally`; with the
row already saved, a failure inside another snippet should still let the email go and the visitor get an answer.

**(3) Reading the popup's settings**, behind `function_exists( 'blockke_bp_config' )`:
`$bp = blockke_bp_config();` then `$bp['notify_emails']` (the last fallback of the notify list, after the rental
page's own setting and the development snippet's list), `$bp['privacy_url']`, `$bp['whatsapp']` and
`$bp['zoho']['endpoint']` (for the one-shot filter to recognise the Zoho post). Each needs a built-in default for
when 40752 is absent.

**Call nothing else from the twelve.** Not `blockke_bp_handle_lead()`, `blockke_bp_context()` or
`blockke_bp_render()`; nothing of 40769 (`blockke_ct_*`), 42885 (`blockke_ac_*`), 44071 (`blockke_lps_*`), 42369
(`blockke_crm_*`), 42215 (`blockke_fd_*`) or 42658 (`blockke_mf_*`). They need nothing from the rental page: 40769,
42885, 33892 and the keeper attach themselves to any listing page that runs `wp_head()` and `wp_footer()`.

**Availability:** Let is `has_term( 'let', 'property_status', $id )`; Sold is `has_term( 'sold', 'property_status',
$id )`. Do not use the meta `blockke_crm_status` on its own (section 5).

### 12.2 JavaScript: after the endpoint has answered `ok`

Look the globals up at that moment, not at page load (33892 and 40769 are printed in the footer, possibly after
the rental page's own script):

```js
var e = { lead_source: formId, listing_id: String(BKR.id) };          // names the development layout uses
try { if (typeof window.gtag === 'function') { window.gtag('event', 'generate_lead', e); }
      else { e.event = 'generate_lead'; (window.dataLayer = window.dataLayer || []).push(e); } } catch (x) {}
try { if (typeof window.blockkeLeadConversion === 'function') { window.blockkeLeadConversion('lead_form'); } } catch (x) {}
try { if (typeof window.fbq === 'function') { window.fbq('track', 'Lead', { content_name: BKR.name }); } } catch (x) {}
```

- `window.blockkeLeadConversion('lead_form')` is the one call for Google Ads and for the Zoho Marketing Automation
  goal. It counts once per page load. There is no fallback to write: if 33892 is absent, no conversion is sent.
- GA4 `generate_lead` and Meta `Lead` are the caller's job; 33892 sends neither for us.
- Nothing to call for WhatsApp, phone or email taps: 40769 logs them and sends GA4 `contact_click` and Meta
  `Contact`; 33892 sends the Ads `whatsapp` and `call` conversions.
- A suggestion, not an existing rule: skip these three calls when the enquiry was sent from a preview (`?bkr=1`,
  `?bkr_key=...`), so that test enquiries are not counted as Google Ads conversions. None of the live snippets
  makes that distinction, and taps on a preview page are still counted by 40769 and 33892.

### 12.3 Attribution: what to read, and when

Read at submit time, send as `attr` with the enquiry. `sessionStorage` key **`blkbp_attr`**, a JSON object with
`utm_source`, `utm_medium`, `utm_campaign`, `utm_term`, `utm_content`, `gclid`, `fbclid`, `gbraid`, `wbraid`, `t`,
`ref`, `land` (section 2.2). Never write to that key. The development layout's reader is the pattern
(`ref/dev-v1.8.2.php:8105-8110`), with one fix, the `|| {}` after the parse (v1.9.1 has it at
`ref/dev-v1.9.1.php:2131`):

```js
var KEYS = ['utm_source','utm_medium','utm_campaign','utm_term','utm_content','gclid','fbclid','gbraid','wbraid'];
function attr() {
  var a = {};
  try { a = JSON.parse(sessionStorage.getItem('blkbp_attr') || '{}') || {}; } catch (e) { a = {}; }
  try { var q = new URLSearchParams(location.search);
        KEYS.forEach(function (k) { var v = q.get(k); if (v && !a[k]) { a[k] = v.slice(0, 120); } }); } catch (e) {}
  if (!a.land) { a.land = location.href.slice(0, 250); a.ref = (document.referrer || '').slice(0, 200); }
  return a;
}
```

The second and third steps are the fallback for when 40752 is absent or the keeper has not run yet. The form that
posts without JavaScript carries no attribution; that is accepted.

### 12.4 Markers on links

| Link | Markup | Why |
|---|---|---|
| WhatsApp, to the agent or to Block | a real `a` element, `href="https://wa.me/{digits}?text={encoded text}"`, **`data-blkct="1"`**, digits in international form straight after `wa.me/` | the marker stops 40769 replacing the text; the number makes 40769 log the tap and 33892 count the conversion |
| its text | contains the word **"viewing"** and **"Ref W-{listing ID}"**, as the approved wording does (`ref/README-wordpress.md:159`) | a second line of defence: 40769 keeps a "viewing" text and adds nothing when the Ref is there |
| WhatsApp links whose address is filled in by script | `data-blkct="1"` already in the HTML | 40769 scans elements as they are added |
| Phone | a real `a` element, `href="tel:+{digits}"`, international form | no marker exists or is needed; the digits are what 40769 maps to an agent name |
| Email | `href="mailto:..."` | logged by 40769 as an email tap |
| "Share on WhatsApp", if any | `https://wa.me/?text=...` with **no number** | so that it is not logged as a contact or counted as a conversion |

Contact actions must be links, not buttons that open WhatsApp from script: neither tracker sees a button. Do not
put contact links inside a `header` or `footer` element within `#bkr`, or the click log files them as the site's
header or footer.

### 12.5 Living with the floating things

- The page's own fixed bars should stay below z-index 999980. A full-screen viewer or dialog of the page should
  either go above 999991 or hide the chat floats while it is open:
  `html.bkr-lock #zsiq_float, html.bkr-lock #blkac-launch { display: none !important; }`
- If the page has a bar at the bottom on phones, lift the floats above it: `#blkac-launch` with a rule one class
  more specific than its own; for `#zsiq_float` use `!important`, as the development layout does. The launcher
  already sits 76px up at 991px and below.
- Hide the theme's own mobile agent bar, compare tray and back-to-top link on the rental layout with the page's
  own CSS scoped to `body.bkr-page`, if the design replaces them. Nothing else does it for a rental page.

### 12.6 What not to do

- Do not call `gtag_report_conversion()`: it would count the enquiry a second time.
- Do not call `blockkeLeadConversion('whatsapp')` or `('call')`, and do not send your own Ads conversion: the
  site-wide listener already counts those taps.
- Do not post to `/wp-json/block/v1/buyer-lead` or `/development-lead`, and do not name the rental route so that
  its URL contains `/wp-json/block/v1/buyer-lead` (33892 fires a conversion on any such POST).
- Do not write to `sessionStorage` `blkbp_attr`, and do not reuse the keys `blkbp_*` or `blkac_*`, the ids
  `blkbp-*`, `blkct-*`, `blkac-*`, `bke-lps-css`, `blockke-rent-nocalc`, the globals `BLOCKBP`, `BLOCKCT`,
  `BLKAC_CFG`, `blockkeLeadConversion`, or the query parameters `blockpop`, `blockreport`, `blockchat`,
  `lp_lean`, `block_feed`, `fresh` (the development snippet has its own, such as `lp` and `bke_rent`). Routes already taken in `block/v1`: `buyer-lead`, `contact-click`,
  `chat-lead`, `chat-profile/...`, `listings`, `listing-status`, `listings-feed`, `development-lead`, `view`
  (`ref/dev-v1.8.2.php:9072`, `:15453`). `rental-lead` is free.
- Do not depend on `window.BLOCKCT`, `window.BLKAC_CFG` or `window.blockkeLeadConversion` existing, and never
  assign to them.
- Do not use the popup's hooks on a rental page: the attribute `data-blkbp-open` and links to `#price-check`
  open the buyer popup wherever it is rendered (`40752.php:607`).
- Do not test for other snippets' functions while the file loads; do not rely on WPCode's order.
- Do not set `$_GET['vcv-editable']`, `$_GET['elementor-preview']` or anything of the kind to silence a widget:
  it also switches off the click tracker.
- Do not let the string `bkd-lpm` appear in the page, and do not copy 44071's output buffer, its regular
  expressions over style and script tags, or its dequeue list.
- Do not save the listing (`wp_update_post`) or change its terms from a page view or from the enquiry endpoint:
  a save makes the search feed rebuild on a visitor's request. Writing the custom field `bke_rent_layout` or the
  option `blockke_rent_page` is safe for every feed.
- Do not change the global `$post` before `get_header()` without restoring it; 45681's rule in `wp_head` reads it.
- Do not redirect a listing URL, and do not strip its query string: gclid and the utm tags live there until the
  keeper has stored them.
- Do not show a KES figure that differs from the listing's price field, or a period that contradicts its
  after-price label (a rent labelled per day must never read as monthly); the emails and the AI front desk quote
  the two as stored.
- Do not treat `blockke_crm_status` as the truth about availability; read the `property_status` terms.
- Do not try to correct 40769's wording in the header, footer or theme links from the rental snippet; record it
  for the owner instead (section 11, item 6).
