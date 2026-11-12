# 04. Site shell: what surrounds, and bleeds into, a custom layout on a block.ke property page

Written 11 Nov 2026. Paths are relative to `ROOT` = `/tmp/claude-0/-workspace-Loyd2024/e24ac003-f571-5336-9956-1fd11a814c7d/scratchpad/rp`.

Short names used throughout:

| Name | File |
|---|---|
| `STOP` | `ref/pages-stop/45577.html` (listing 45577 as a classic page, 713,508 bytes, 8,011 lines) |
| `DEV` | `ref/pages-final/45577.html` (the same listing on the development layout, 740,066 bytes, 7,607 lines) |
| `MAIN` | `ref/site-css/theme-main.css` (the theme's `public/css/main.css`; 909,527 bytes in 8 lines; nearly everything is on line 4) |
| `STYLE` | `ref/site-css/theme-style.min.css` (the theme's `style.min.css`, one line) |
| `WPBL` | `ref/site-css/wp-block-library.style.min.css` (one line) |
| `ELF` | `ref/site-css/elementor-frontend.min.css` (one line) |
| `CUST` | the Customizer's Additional CSS, `<style id="wp-custom-css">`, `STOP:958-2867` (105 KB, 571 rules) |
| `TOC` | the theme option `wp_estate_custom_css`, printed on one line at `STOP:764` after the theme's variables; source `ref/theme-options.json:1362` (56 KB, 187 rules) |
| `CTRL` | `ref/site-js/theme-control.js` (the theme's `js/control.js`, 6,370 lines, not minified) |
| `D/` | `notes/04-site-shell-data/` (evidence: reports and the offline harness; see its `README.md`) |

## In brief

1. **Where the page goes.** `get_header()` leaves two elements open, `div#all_wrapper.website-wrapper` and inside it
   `div.container-fluid.px-0.wpresidence_main_wrapper_class`; the layout's root is printed straight into the second
   one, and `get_footer()` closes both (`DEV:4163`, `:4166`, `:4640`, `:4946`). That parent is already the full width
   of the viewport, so `#bkr` needs `width:100%` and no `100vw` trick (section 1.6).
2. **`#all_wrapper` is a stacking context at z-index 1** (`#all_wrapper{position:relative!important}` and
   `#all_wrapper{z-index:1!important}`, `MAIN:4`). No z-index inside `#bkr`, however large, can get above an element
   outside that wrapper whose own z-index is 2 or more: the theme's mobile agent bar (990), back-to-top (102) and
   contact (100) buttons, the chat launcher (999985) and, in all likelihood, the Zoho chat float and the cookie
   banner (their z-index is not known). Measured (section 1.11). A full-screen viewer or dialog has to use
   `<dialog>.showModal()` (top layer), or hide those floats while it is open. This corrects
   `notes/05-live-snippets.md` 12.5 ("go above 999991").
3. **Header.** Below 1200px: a 56px bar, `position:sticky; top:0`, z-index 100. From 1200px: a 40px top bar plus a
   90px header, 130px in all, **not sticky** (it scrolls away). So the top offset under the site header is 56px below
   1200px and 0 from 1200px (section 1.10).
4. **Bottom of the screen on phones is already taken.** Below 992px every single-property page shows the theme's
   agent bar (fixed, about 62px, z-index 990, outside `#all_wrapper`) and the site pads `<body>` by 76px for it; a
   site-wide script then raises that padding to 96px and, between 992px and 1024px, shows a second bar
   (`.blockke-cta-bar`, 73px, z-index 99999). A rental page with its own bar must switch both off (sections 1.9, 5.2).
5. **Block's own CSS forces things with `!important` on bare tags**: Montserrat on almost every text element, navy
   and `letter-spacing:-0.5px` on all headings, weight 700 on `h1` and `h2`, `#555` on `a:hover`, a complete skin
   on `button[type=submit]` and `input[type=submit]`, square corners and grey borders on every input, no focus
   shadow (`STOP:1407-1470`). Only an ID selector with `!important` beats them (section 2.3).
6. **The theme styles bare tags too**: every `p` is set to 14px / 1.65em / 400, lists lose their bullets, tables get
   borders and a 10px radius, `td` is `#222`, `blockquote` floats left, `label` is 13px, `legend` floats, checkboxes
   are replaced by a Font Awesome tick, `:focus{outline:0}` (section 2.2). The development layout's reset does not
   undo the `p` and `td` rules: bare paragraphs inside `#bkd` render at 14px today (section 3).
7. **The theme clears the URL hash on every `hashchange` with `history.pushState`** (`CTRL:3773-3776`). After one
   click on a plain `<a href="#section">` the Back button no longer leaves the page (measured in Chrome, section 4.2).
   In-page links on the rental page should be handled by script (`preventDefault` + `scrollIntoView`) and nothing
   should rely on `:target`.
8. **Three site-wide scripts act on any property page, whatever draws it**: the contact tracker rewrites the text
   of every WhatsApp link that lacks `data-blkct` and reports each `tel:`, WhatsApp and `mailto:` tap, placing it by
   `closest('header …')` / `closest('footer …')` (`STOP:7867-7918`); the booking script builds `.blockke-cta-bar`
   (`STOP:5863-6432`); the schema script writes a `RealEstateListing` into the head that calls every listing a
   `Residence` when the classic labels are missing, unless an element with id `bke-schema-ld` already exists
   (`STOP:2927-3131`). Section 4.3.
9. **Cloudflare rewrites e-mail addresses** in the HTML into `/cdn-cgi/l/email-protection#…` links and
   `[email protected]` text, decoded by a script it injects (`DEV:4946`, `:4968`). Without JavaScript the address is
   not readable (section 4.6).
10. **Fonts already loaded on every page**: Montserrat 300, 400, 500, 600, 700 (upright only); Frank Ruhl Libre
    300 to 900; Roboto; Roboto Slab; Font Awesome 6.4.2. Instrument Serif is loaded only by the development snippet
    (section 5.6).
11. **Weight of the shell**: 574 KB of the 713 KB classic page is shell; the page's own content starts at byte
    367,974 (about 75 KB into the compressed stream), after 250 links of menu (section 1.1).
12. **Names to keep out of the markup**: any class containing `property_description`, `feature_chapter` or
    `gmap_wrapper`, any id containing `gmap`, the classes `single-content`, `wpestate_property_description`,
    `property_listing`, `lightbox_trigger`, Bootstrap's component and `data-bs-*` names, and `<header>` / `<footer>`
    elements around call, WhatsApp or e-mail links (section 2.5).

## 0. Sources, method, how to check a citation

**Sources read.** `STOP`, `DEV`, `ref/theme-options.json`, `ref/dev-v1.8.2.php`, `ref/dev-v1.9.1.php`,
`ref/live-snippets/40752.php` and `*.printed.html`, and the static files below. No HTML page was fetched from the live
site.

**Static files downloaded from block.ke** (25 requests in all, one at a time, 2.6 s apart, no errors):
- 21 stylesheets, every `<link rel="stylesheet">` on block.ke that `STOP` and `DEV` reference, into `ref/site-css/`.
  `ref/site-css/MANIFEST.tsv` maps each file to its URL, size, hash and Last-Modified. The Google Fonts stylesheets
  were not fetched (not block.ke).
- 4 theme scripts into `ref/site-js/` (`MANIFEST.tsv` there): `theme-control.js` (`js/control.js`),
  `theme-property.min.js` (`js/property.min.js`), `theme-google_map_code_listing.min.js`, `theme-maps_base.js`.
  Not downloaded, so not read: `ajaxcalls.min.js`, `mapfunctions.min.js`, `wpestate_login_register.min.js`, the
  Bootstrap bundle, Elementor's and other plugins' scripts.

**Offline harness.** `D/harness/` loads a saved page in headless Chrome (Chrome for Testing 153) at its real URL with
every request answered from local files or aborted; nothing leaves the machine. It was used to (a) measure the shell
at 390, 991, 992, 1024, 1199, 1200 and 1440px, (b) list, through the DevTools protocol, every author rule that
matches 86 plain elements placed in a test `#bkr` root, (c) compare their computed styles with a bare document, (d)
verify the reset and the offsets recommended in section 5, (e) run Block's inline scripts on a page without the
classic markup. Limits: web fonts are not loaded (font names are right, text metrics are a fallback font's); with
JavaScript on, only inline scripts run (jQuery, the theme's and the plugins' files are aborted), so anything that
needs the theme's scripts was read from `CTRL`, not run.

**"Measured"** below means measured in that harness. **"CSS"** means read from a stylesheet. **"Unknown"** means
neither.

**Checking a rule in a one-line file.** Citations to `MAIN`, `STYLE`, `WPBL`, `ELF` give the line (4 for `MAIN`, 1
for the others) and the rule's own text; find it with, for example,
`grep -o 'table tbody tr td{[^}]*}' ref/site-css/theme-main.css`. `D/matched-rules-1440.md` gives a column for each
rule (UTF-16 offset within the line; it can differ from a byte offset by a few characters).

## 1. Document skeleton

### 1.1 Size map

| Part | `STOP` bytes (line) | `DEV` bytes (line) |
|---|---|---|
| `<head>` | 0 to 238,571 (`</head>` at 2868) | 0 to 302,429 (3401) |
| `<body>` open, `wp_body_open` output, the two mobile drawers | 238,580 to 308,382 (2870 to 3629) | 302,438 to 372,247 |
| `#all_wrapper` open to end of header | 308,382 to 367,974 (3630 to 4102) | 372,247 to 431,839 (4163 to 4635) |
| page content | 367,974 to 507,377 (4108 to 5764) | 431,839 to 496,388 (4640 to 4946) |
| footer template inside `#all_wrapper` (scripts only) | 507,377 to 555,429 (5764 to 6631) | 496,388 to 544,548 |
| `wp_footer` output to `</body>` | 555,429 to 713,493 (6631 to 8011) | 544,548 to 740,051 |

Whole page gzip -6: 156,500 bytes (`STOP`), 171,892 (`DEV`); everything before the content: 75,460 and 87,721.
The shell is the same HTML on both layouts; `DEV`'s head is 64 KB larger because it carries `<style id="bkd-css">`.

### 1.2 `<html>`, viewport, body classes

- `<html lang="en-US" prefix="og: https://ogp.me/ns#" dir="ltr">` (`STOP:2`). Root font size is the browser's 16px
  (measured: `html` computes to 16px, so `1rem` = 16px).
- `<meta name="viewport" content="width=device-width, initial-scale=1,user-scalable=no">` (`STOP:6`). The theme
  prints it; a snippet cannot change it.
- `<body class="wp-singular estate_property-template-default single single-estate_property postid-45577
  wp-theme-wpresidence wpresidece_has_top_bar wpresidence_header_logo_align_left
  wpresidence_header3_logo_align_left wpresidence_header_menu_type1_align_right
  wpresidence_header_menu_type3_4_text_align_center wpb-js-composer js-comp-ver-8.7.3 vc_responsive
  elementor-default elementor-kit-24" data-postid="45577">` (`STOP:2870`). Spellings are the theme's
  (`wpresidece_has_top_bar`).
- The development layout adds `bkd-page` through the `body_class` filter (`DEV:3403`;
  `ref/dev-v1.9.1.php:10259-10268`). The rental page adds `bkr-page` the same way.
- Added at run time by site scripts: `blockke-bar-open` on every single-property page once the booking script has run
  (section 4.3), `blkac-test` and `blkac-lock` (chat gate, `STOP:7814`, `:7820`), the attribute
  `data-wpr-language="us"` (`STOP:3132`). Logged-in visitors also get WordPress's `logged-in admin-bar` (not in the
  saved pages; section 7).

### 1.3 Head, in order (`STOP`; the full list with every line is `D/head-assets-classic.md`)

| Lines | What | Why it matters here |
|---|---|---|
| 5-7 | charset, viewport, pingback | |
| 9-33 | Rank Math: title, description, robots, canonical, Open Graph, Twitter, JSON-LD (a `BreadcrumbList` only) | untouched by the layout |
| 35-43 | dns-prefetch, preconnect, feeds, oEmbed | |
| 44-47 | style `wp-img-auto-sizes-contain-inline-css` | `img:is([sizes=auto i],[sizes^="auto," i]){contain-intrinsic-size:3000px 1500px}` applies to any lazy image WordPress gives `sizes="auto, …"` |
| 48 | css `elementor-post-26853-css` | footer template, 230 bytes |
| 49-63 | style `wp-emoji-styles-inline-css` | `img.wp-smiley, img.emoji{…!important}` |
| 64 | css `wp-block-library-css` (141 KB) | reaches the classes of the description HTML (section 2.6) |
| 65-96 | styles `classic-theme-styles`, `pdfemb…`, `rank-math-related-posts…`, `ai_summarization`, `global-styles` | variables and class rules; `:where(body){margin:0}` |
| 80, 97, 98 | css `residence_latest_items…`, `sr7css-css` (Slider Revolution), `wpr-translate-language-switcher-css` | class rules only |
| 99-112 | css `wpestate_style-css` (`STYLE`) and its inline block | has a bare `h3` rule (section 2.2) |
| 113 | css `theme-main-css-css` (`MAIN`: Owl Carousel, VenoBox, Bootstrap 5.3 with Reboot, the theme) | the main source of bleed |
| 114 | css `bootstrap-selectcss.min-css` (v1.14.0-beta3) | class rules |
| 115 | css `wpestate-roboto-css`, Google Fonts `css?family=Roboto:wght@300,400,500,700` | malformed for the v1 endpoint; what it returns is unknown (section 7) |
| 116-118 | css Font Awesome 6.4.2 (`font-awesome6.4.2.min-css`), jQuery UI theme 1.10.2, `bsf-Defaults-css` (icon font) | |
| 119 | Redux fonts: `preload` + `media="print" onload` + `<noscript>`: Roboto 400, Frank Ruhl Libre 300 to 900 | loads late, swaps |
| 119-123 | scripts `tp-tools-js`, `sr7-js` (async); `jquery-core-js` 3.7.1, `jquery-migrate-js` 3.4.1, `modernizr.custom.62456-js` (blocking) | jQuery is in the head |
| 124-153 | `facebook-signal-js`, Site Kit `google_gtagjs-js` (`GT-579R9RZW`), Google Ads tag `AW-18119754545` | define `gtag`, `dataLayer` |
| 161-418 | five inline scripts: `bke-desc-collapse`, `bke-mobile-cta`, `bke-properties-redirect`, `bke-hub-archive-redirect`, `bke-category-link-repoint` | section 4.3 |
| 419-763 | the theme's own `og:image` and `og:description` (the whole description text, 343 lines) | printed on both layouts (`DEV:422-766`), besides Rank Math's tags |
| 764 | `<style type='text/css'>`: the theme's option variables (`:root{--wp-estate-…-option:…}`), then `TOC` | 56 KB on one line |
| 766-776 | Meta pixel | defines `fbq` |
| 777-794 | Elementor's lazy-background style | `.e-con.e-parent…` only |
| 796-820 | `<style class="wpcode-css-snippet">` | colours of the cookie banner `#zcb-banner` |
| 820-837 | `gtag_report_conversion()` (snippet 33869) | do not call it (see 05) |
| 838 | `https://cdn.pagesense.io/js/block1/…js`, synchronous | Zoho PageSense: cookie banner, tracking |
| 840-851 | Google Tag Manager `GTM-58WZGMX6` | |
| 857-887 | style `bke-cy-css` | completion filter UI; also the header's side margins (section 5.5) |
| 888-891 | site icons | |
| 892-925 | `link#bkd-font` (Instrument Serif) and style `bkd-cls-css` | the development snippet's additions to a classic page (section 4.3) |
| 926 | style `blockke-rent-nocalc` (snippet 45681), then Slider Revolution's inline script (926-957) | |
| 958-2867 | style `wp-custom-css` = `CUST`; first statement is `@import` of Montserrat (`STOP:959`) | printed after everything else, so it wins ties |
| 2868 | `<noscript><style>` of WPBakery, `</head>` | |

On the development layout (`D/head-assets-dev-layout.md`) the head differs only in what the development snippet
prints: `style#bkn-vt`, `script#bkn-vt-js` and `<link rel="expect" href="#bkd-hero-media" blocking="render">` first
in the head (`DEV:9-11`); `link#bkd-font` and `style#bkd-css` (`DEV:895-1457`, printed on `wp_head` priority 99,
`ref/dev-v1.9.1.php:10290-10301`); `style#bkn-dev-css` (`DEV:1490`); no `bkd-cls-css`. External stylesheets and
scripts are identical on the two pages (compared: same list, same order).

A `<style>` printed by a snippet on `wp_head` at any priority from 9 to 100 lands after every enqueued stylesheet and
before `CUST` (WordPress prints the Customizer CSS at priority 101). Rules of equal specificity and importance in
`CUST` therefore win over the snippet's. Rely on specificity (the `#bkr` id), never on order.

### 1.4 From `<body>` to `#all_wrapper`

| Lines (`STOP`) | What |
|---|---|
| 2871-2875 | Google Tag Manager `<noscript>` iframe |
| 2884-2913 | script `bke-header-phone`: fills `.header_phone a` when the theme left it empty |
| 2927-3131 | script `bke-listing-schema`: builds JSON-LD from the rendered page (section 4.3) |
| 3132 | script setting `body[data-wpr-language]` |
| 3134-3137 | Meta pixel `<noscript>` image |
| 3141-3542 | `div#mobilewrapper_links.mobilewrapper`: the phone menu drawer (`.snap-drawer-left`), header phone, `ul#menu-main-menu.mobilex-menu` with the four Elementor mega-menu templates again (125 links) |
| 3546-3626 | `div#mobilewrapperuser.mobilewrapper-user`: the login, register and forgot-password drawer (17 inputs) |

Both drawers are `display:none; position:fixed` until opened (`.mobilewrapper{…display:none;…position:fixed;top:0;
width:256px}`, `MAIN:4`). They are opened by Slideout.js with `#all_wrapper` as the panel (`CTRL:5029-5101`): while a
drawer is open `#all_wrapper` carries `transform:translateX(…)`, so `position:fixed` elements inside `#bkr` are then
positioned against `#all_wrapper`, not the viewport (measured: a bottom bar moves from y=783 to y=2034). Harmless,
but the test rig should not be surprised by it.

### 1.5 What `get_header()` prints inside the wrappers (the site header)

`get_header()` opens, and leaves open:

```
STOP:3630  <div class="website-wrapper wpresidence_wrapper_for_header_type1  wide " id="all_wrapper" >
STOP:3633    <div class="container-fluid px-0 wpresidence_main_wrapper_class  wide  has_header_type1 ">
```

and prints into the second one, in this order:

| Lines (`STOP`) | Element | Notes |
|---|---|---|
| 3642-3649 | `div.top_bar_wrapper.d-none.d-xl-block.w-100.topbar_show_mobile_no` > `div.top_bar.d-flex.w-100.px-5…` | the top bar; it has no widgets, so from 1200px it is an empty navy strip 40px high (`.top_bar_wrapper{…height:40px;position:relative;width:100%;z-index:99999}`, `MAIN:4`; colour `--wp-estate-top-bar-back-option:#0d2440`, `STOP:764`) |
| 3653-3662 | `div.mobile_header.exclude-rtl.d-xl-none.mobile_header_sticky_yes` | below 1200px. Children: `div.mobile-trigger` (`i.fas.fa-bars`, left), `div.mobile-logo > a > img.img-responsive.retina_ready` (`block-real-estate-primary.svg`, centred, 111 x 43 measured), `div.mobile-trigger-user` (`i.fas.fa-user-circle`, right) |
| 3667-4091 | `div.master_header.d-none.d-xl-block.d-flex.wide` > `header.header_wrapper.d-flex.w-100.header_type1.hover_type_1.no_property_submit` > `div.header_wrapper_inside.mx-5.py-0.d-flex.w-100.flex-wrap.align-items-center.justify-content-between` | from 1200px. `data-logo` and `data-sticky-logo` hold the logo URL |
| 3678-3679 | `div.logo > a > img#logo_image.img-responsive.retina_ready` | `block-real-estate-horizontal.svg`; 230px wide at 1440, 160px at 1200 (measured; `CUST`, `STOP:2830-2837`) |
| 3679-4070 | `nav.wpresidence-navigation-menu.px-5.py-0.navbar.navbar-expand-lg` > `div.menu-mega-menu-updated-container` > `ul#menu-main-menu.menu` | 125 links. Top level: Buy (mega panel, Elementor template 36432), Rent (sub-menu), Off-Plan (mega, 36433), Completion (sub-menu, `li.bke-completion-menu`), Sell & Manage (sub-menu), Invest (mega, 38106), About (mega, 38105), Get a Consultation (`li.bke-cta-solid`), Contact (`li.bke-cta-ghost`) |
| 4071-4087 | `div.user_menu_wrapper` > `div#user_menu_u.user_menu…` > `div.header_phone` (`a[href="tel:+254184027349"]`) and `div.submit_action` (user icon) | |
| 4094 | `div.wpestate_top_property_navigation` with ten `a.wpestate_top_property_navigation_link` (`#single-overview-section`, `#wpestate_property_description_section`, `#accordion_property_address`, …, `#property_similar_listings`) | the theme's own sticky section bar for the classic page. Printed on the development layout too (`DEV:4627`), where none of its targets exist |
| 4094-4100 | inline script `jQuery(document).ready(function(){ wpestate_property_sticky(); });` | |
| 4101-4102 | `div.header_media.d-flex.w-100.mobile_header_media_sticky_yes.header_mediatype_0.with_search_10.header_media_non_elementor` | empty; measured height 0 at every width |

A mega panel is `li.with-megamenu > a.menu-item-link + div.megamenu-studio-panel > div.elementor.elementor-NNNNN`;
`.megamenu-studio-panel{…position:fixed;left:0;width:100%;z-index:999;opacity:0;visibility:hidden}` and it shows on
`.with-megamenu:hover` (`MAIN:4`). It lives inside `.master_header` (z-index 9999), so it paints over anything in
`#bkr`.

The header's visible paint: `.master_header` is white (`--wp-estate-header-color-option:#ffffff`), and `CUST` lays a
translucent navy gradient with `backdrop-filter` over `.master_header .header_wrapper` on every page
(`STOP:2239-2244`).

### 1.6 What wraps the page's own content

**Classic page.** The theme's single-property template prints, inside the open `.wpresidence_main_wrapper_class`:

```
STOP:4108  <div class="wpresidence-content-container-wrapper col-12 d-flex flex-wrap lay1">
STOP:4110    div.col-xs-12.col-md-12.breadcrumb_container > ol.breadcrumb
STOP:4119    div.wpresidence_property_page_title_area…  (labels, h1.entry-title.entry-prop, div.price_area, div.property_categs, div.prop_social)
STOP:4161    div.col-12.col-lg-8.ps-lg-0.pe-lg-3.order-lg-1.wpestate_column_content.full_width_prop > div.single-content.listing-content
               (slider, #single-overview-section, #wpestate_property_description_section, the #accordion_property_* panels, #property_similar_listings)
STOP:5378    div#primary.…col-lg-4…wpestate_sidebar_sticky.widget-area-sidebar > div#primary_sidebar_wrapper (#wpestate_sidebar_property_contact_tabs, widgets)
STOP:5762  <!-- end sidebar --></div>
STOP:5764  <div class="row wpestate_property_disclaimer"></div>
```

`.wpresidence-content-container-wrapper` is the theme's content box: `width:var(--wp-estate-main-grid-content-width-option…)`
= 1540px, `max-width:100%`, `padding:0 15px`, `margin:0 auto` (`MAIN:4`; `--wp-estate-main-grid-content-width-option:1540px`,
`STOP:764`; `ref/theme-options.json:1248`). Measured at 1440: x 0 to 1440 with 15px padding; below 1200: 12px padding.

**Development layout.** No content wrapper at all. `get_header()` output ends with the empty `.header_media` and the
snippet echoes its root at once (`ref/dev-v1.9.1.php:10381-10383`):

```
DEV:4634   <div class="header_media d-flex  w-100 mobile_header_media_sticky_yes header_mediatype_0 with_search_10 header_media_non_elementor">
DEV:4635       </div>
DEV:4640         <div id="bkd" class="bkd" data-listing="45577">…</div></div> <!-- end class container -->      (ends at DEV:4946)
```

So `#bkd` is a direct child of `div.container-fluid.px-0.wpresidence_main_wrapper_class.wide.has_header_type1`, which
is a direct child of `div#all_wrapper`. Measured: that parent is x=0, width = viewport at every width tested
(`.container-fluid{width:100%}` with `.px-0{padding-left:0!important;padding-right:0!important}`, `MAIN:4`; the theme
is in wide mode, `wp_estate_wide_status` "1", `ref/theme-options.json:1247`). `#bkd` starts at y=56 at 390px and y=130
at 1440px. The rental root will sit in exactly the same place.

Ancestors of `#bkr`, with what each carries (measured, `D/matched-rules-1440.md`, "ANCESTOR" entries):

| Ancestor | Computed |
|---|---|
| `html` | 16px; `scroll-behavior:smooth` unless the visitor prefers reduced motion (`@media (prefers-reduced-motion:no-preference){:root{scroll-behavior:smooth}}`, `MAIN:4`; the same for `html` in `ELF:1`) |
| `body` | Montserrat, Helvetica, sans-serif; 14px; line-height 23.1px (declared `1.65em`, so it is inherited as a length); weight 400; colour `#1a1a1a`; background `#fff`; `text-align:left`; `-webkit-text-size-adjust:100%`; `-webkit-tap-highlight-color:transparent`; `padding-bottom:76px` at 991px and below |
| `#all_wrapper` | `position:relative!important; z-index:1!important` (two rules shared with `.position-relative` and `.z-1`); `min-height:100vh; touch-action:pan-y; transition:all .4s ease; width:100%` (all `MAIN:4`) |
| `.wpresidence_main_wrapper_class` | `width:100%`, no padding, static, no background |

### 1.7 What `get_footer()` prints

```
STOP:5764  </div> <!-- end class container -->                      closes div.container-fluid…wpresidence_main_wrapper_class
STOP:5766  <div class="wpestate_elementor_footer_custom">           Elementor template 26853
             div.elementor.elementor-26853 > div.elementor-element-bke0001.e-con-full.e-flex.e-con.e-parent > div.elementor-element-bke0002.elementor-widget-html
               style#blockke-project-cards-css (5770), script#blockke-project-cards (5786), script#blockke-booking-ux (5863),
               style#blockke-nearby-style (6433), script#blockke-nearby-places (6446)
STOP:6630  </div>
STOP:6631  </div> <!-- end website wrapper -->                      closes div#all_wrapper
STOP:6633  … wp_footer() output …
STOP:8011  </body>
```

The Elementor "footer" inside `#all_wrapper` holds only scripts and styles: measured height 0. **The visible site
footer is `<footer class='bkef'>`, printed by `wp_footer` after `#all_wrapper` has closed** (`STOP:6904-7101`): its own
`<style>` (6905-6981), a logo and tagline row, five link columns (Contact, Explore, Neighbourhoods, Company,
Insights), "Popular Searches", a disclaimer, a Google preferred-source band and a legal bar. `.bkef{display:block;
clear:both;width:100%;background:#0D2440;…position:relative;overflow:hidden}` with inner padding 64px (34px at
1024px and below, 22px at 767px and below). Measured height 1,115px at 1440 and 2,421px at 390. Just before it comes
`div#bkef-devs.bkef-devs` ("Popular Developments", `STOP:6645`), which its script moves into the footer and removes
(without JavaScript it stays as a separate navy band, 115px at 1440, 158px at 390).

`footer.bkef` is positioned with `z-index:auto`, so it paints below `#all_wrapper` (z-index 1): a fixed bar inside
`#bkr` stays visible over the footer.

### 1.8 Everything printed by `wp_footer` (`STOP`; `DEV` is the same apart from the last row group)

| Lines | What | From |
|---|---|---|
| 6633-6645 | loader for Zoho SalesIQ (`salesiq.zohopublic.com/widget?…`, `id="zsiqscript"`, deferred) | SalesIQ plugin. Creates the chat float at run time |
| 6645 | `script#blockke-project-map` (`window.BLOCKKE_PROJECTS`, 26 KB: listing id to project name), `style#bkef-devs-css`, `div#bkef-devs`, `script#bkef-devs-js` | footer snippets |
| 6646-6697 | two Meta pixel event scripts (Ninja Forms, WPForms hooks) | pixel plugin |
| 6699-6701 | `<script type="speculationrules">`: prefetch same-site links, eagerness `conservative`, except URLs with a query string, `/wp-*`, `a[rel~="nofollow"]`, `.no-prefetch` | WordPress |
| 6722-6889 | three inline scripts: search polyfill (`wpestate_meta_clause_exists`; strips two attributes from `#price_low`, `#price_max`, `#keyword`), auto filter (results page only), hero search (home only) | WPCode footer channel |
| 6904-7101 | `<footer class='bkef'>` with its `<style>` | WPCode footer channel |
| 7101-7133 | style and a capturing `click` listener for `.mobile_user_menu .wpestate-crm-dashboard > a` only; Cloudflare's `email-decode.min.js` (7114) | theme / Cloudflare |
| 7134-7227 | `div#modal_login_wrapper` (login modal: `.modal_login_back`, `.modal_login_container`) | theme |
| 7228 | `input#wpestate_ajax_log_reg`, `a.backtop` (`i.fas.fa-chevron-up`) | theme |
| 7229 | `a.contact-box` (`i.fas.fa-envelope`) | theme |
| 7233-7260 | `div.contactformwrapper.hidden` > `div#footer-contact-form` (inputs `#foot_contact_name`, `#foot_contact_email`, `#foot_contact_phone`, `#foot_contact_content`, with `tabindex` 373 to 375; `button#btn-cont-submit`) | theme |
| 7262-7268 | `div.prop-compare` > `form#form_compare` | theme |
| 7270-7388 | `div.lightbox_property_wrapper` > `div.lightbox_property_wrapper_level2`: `div#owl-demo` with one `div.item` per gallery photo (inline `background-image`, 1620x1080 files), and a contact form (`.agent_contanct_form`, `#agent_property_id`, `#agent_id`, a second `#agent_property_ajax_nonce`); `div.lighbox_overlay` | theme. Printed on the development layout as well (`DEV:6452-6570`): all 19 photo URLs and the form are in the HTML of any layout |
| 7391-7397 | inline `jQuery(document).ready(function(){ estate_start_lightbox(); });` | theme |
| 7399-7421 | `div.mobile_agent_area_wrapper.d-block.d-lg-none`: agent photo and name (`a` to the agent page), `a.wpestate_mobile_agent_show_contact[href="#agent_contact_name"]`, `a[href="tel:+254180711259"]`, `a[href="https://wa.me/254180711259?text=…"]`; then `input#wpestate_ajax_filtering`, `input#wpestate_payments_nonce` | theme |
| 7422-7457 | Meta pixel CF7 hook, `div#fb-pxl-ajax-code`, Elementor's lazy-background observer | plugins |
| 7458-7564 | Google Ads lead conversion: `window.blockkeLeadConversion`, wrappers round `XMLHttpRequest.prototype.send` and `window.fetch`, a capturing `click` listener | snippet 33892 |
| 7566-7572 | Site Kit "Sign in with Google": `accounts.google.com/gsi/client` and its script; `shouldShowOneTapPrompt:false`, so no One Tap prompt | Site Kit |
| 7575-7604 | a `<style>` of the residence-studio plugin (`.wpestate-library-open-modal…`, class rules) | plugin |
| 7605-7620 | Elementor styles: `elementor-frontend-css` and its inline block, the four mega-menu template stylesheets (`post-36432`, `-36433`, `-38106`, `-38105`), kit `post-24`, `base-desktop`, `base-mobile`, `widget-heading`, `widget-icon-list`; Google Fonts Roboto (all weights) and Roboto Slab | Elementor |
| 7621-7718 | the enqueued scripts (section 4.1) | theme, plugins |
| 7719-7726 | emoji settings and loader (`type="module"`) | WordPress |
| 7727-7739 | `svg.bkd-sprite`, `div#bkd-cls[hidden]` (holding `#bkd-feat`, `#bkd-near`), `script#bkd-cls-js` | development snippet v1.8.2, classic pages only (`ref/dev-v1.8.2.php:1666-1705`). On `DEV` this group is `script#bkd-cfg` and `script#bkd-js` instead (`DEV:6908-7335`) |
| 7740-7864 | `style#blkac-css`, `button#blkac-launch.blkac-hidden`, `div#blkac-backdrop`, `div#blkac[role=dialog]`, `window.BLKAC_CFG`, `script#blkac-js` | snippet 42885 (AI chat gate) |
| 7865 | `script#blkbp-attr` (attribution keeper). No popup markup on a rent-only listing | snippet 40752 |
| 7866-7918 | `script#blkct-cfg` (`window.BLOCKCT`), `script#blkct-js` | snippet 40769 |
| 7919-8010 | `script#bke-cy-cfg`, `script#bke-cy-js` (completion filter) | |

`notes/05-live-snippets.md` section 1 has the snippet-by-snippet view of the same output.

### 1.9 Floating widgets and modals: position, size, z-index

All of these are outside `#all_wrapper` (root stacking context) except the last row. Sizes are measured in the harness
(JavaScript off, fallback font) unless marked CSS.

| Element | 390px | 1440px | z-index | Shown when |
|---|---|---|---|---|
| Theme mobile agent bar `.mobile_agent_area_wrapper` (`STOP:7399`) | `position:fixed; bottom:0; left:0`, 390 x 62, `display:flex!important` (`CUST`, `STOP:1738-1746`) | `display:none` (Bootstrap `d-lg-none`: hidden from 992px) | 990 (`STOP:1746`; theme default 105, `MAIN:4`) | always, below 992px, on every single-property page |
| Back to top `a.backtop` (`STOP:7228`) | 50 x 50; `bottom:90px` (`STOP:1840`); `right:-60px; opacity:0` until `.islive`, then `right:15px; opacity:.9`: x 325 to 375, y 704 to 754 | 50 x 50; `bottom:10px`; with `.islive` x 1375 to 1425, y 840 to 890 | 102 (`.backtop,.contact-box{…z-index:100;z-index:102}`, `MAIN:4`) | after 90px of scroll: `CTRL:81-88` adds `.islive` when `scroll >= control_vars.scroll_trigger`, which is "90" (`STOP:7629-7632`) |
| Contact button `a.contact-box` (`STOP:7229`) | `display:none!important` at 991px and below (`STOP:1839`) | 50 x 50; `bottom:10px`; with `.islive` `right:15px` plus `margin-right:60px`: x 1315 to 1365 | 100 | as above, from 992px |
| `.contactformwrapper` (`STOP:7233`) | never (its button is hidden) | `position:fixed; right:45px; bottom:80px; width:312px; padding:30px` (CSS) | 300 | when the contact button is clicked |
| `.prop-compare` (`STOP:7262`) | `position:fixed; top:167px; right:-240px`, 239 x 133, off screen | same | 99 | slides in when a visitor compares listings |
| `#modal_login_wrapper` (`STOP:7134`) | `display:none; position:fixed`, full screen | same | 9999 | login |
| `.lightbox_property_wrapper` (`STOP:7270`) | `display:none; position:fixed`, full screen | same | 999999 | the classic gallery; nothing opens it on a custom layout (its triggers are `.lightbox_trigger`) |
| AI chat launcher `#blkac-launch` (`STOP:7778`) | `position:fixed; right:16px; bottom:calc(76px + env(safe-area-inset-bottom))` (`STOP:7775`), about 38px high (CSS) | `right:16px; bottom:16px`, about 41px high (CSS) | 999985 | hidden for ordinary visitors while `BLKAC_CFG.mode` is `test` (`STOP:7800`, `:7809-7810`, `:7817`) |
| `#blkac-backdrop`, `#blkac` | bottom sheet at 640px and below (`STOP:7770`) | centred, `width:min(460px, 100% - 32px)` | 999990, 999991 | chat gate dialog |
| `.blockke-cta-bar` (created by script, `STOP:6049-6089`, appended to `<body>`) | `display:none!important` at 991px and below (`STOP:1736`) | `display:none!important` from 1025px (`TOC`) | 99999 (`TOC`: `body.single-estate_property .blockke-cta-bar{position:fixed;left:0;right:0;bottom:0;z-index:99999;…}`) | **between 992px and 1024px**, on every single-property page including the development layout: measured at 1010px as a 1010 x 73 navy bar at the bottom with one "WhatsApp" link, active from page load (section 4.3) |
| Zoho SalesIQ float `#zsiq_float` / `.zsiq_floatmain` (script at `STOP:6633-6645`) | unknown | unknown | unknown | created at run time by Zoho's script; nothing local shows its CSS (section 7). The development layout moves it with `html.bkd-mb-on #zsiq_float{bottom:calc(84px + env(safe-area-inset-bottom))!important}` and hides it with `html.bkd-lock #zsiq_float{display:none!important}` (`ref/dev-v1.9.1.php:9601-9602`) |
| Cookie banner `#zcb-banner` (Zoho PageSense, script at `STOP:838`) | unknown | unknown | unknown | until the visitor accepts or declines. Only its colours are local (`STOP:796-820`; the shadow `0 -4px 16px` there implies a bar at the bottom) |
| Price-check pill `#blkbp-pill` and popup `#blkbp` | not printed on rent-only listings: the popup returns early unless the listing has the term `for-sale` (`ref/live-snippets/40752.php:113-117`) | same | 999980, 999991 | never on a rent-only listing |
| Google One Tap | none: `shouldShowOneTapPrompt:false` (`STOP:7572`) | none | | |
| Theme's sticky property bar `.wpestate_top_property_navigation` (`STOP:4094`, **inside** `#all_wrapper`) | `display:none` at 1200px and below (`MAIN:4`) | `position:fixed; top:-50px; height:50px; width:100%`, parked above the viewport; its `box-shadow` (`3px 1px 20px rgba(0,110,225,.08)`) falls on the top 20px of the screen | 999 | never slides in on this site (section 1.10), but from 1201px its ten links are still in the tab order and invisible |

There is no floating WhatsApp or phone button of its own on the site. Calls and WhatsApp are reached through the
theme's mobile agent bar, `.blockke-cta-bar`, the chat gate's "WhatsApp us" link and the footer.

### 1.10 Header heights and stickiness

| Viewport | What is at the top | Height | Sticky |
|---|---|---|---|
| below 1200px (390, 991, 992, 1024, 1199 measured) | `.mobile_header` | **56px** (`.mobile_header{height:56px;}`, `STOP:2820`; theme default 70px, `MAIN:4`) | **yes**, CSS only: `.mobile_header.mobile_header_sticky_yes{position:sticky;top:0}`, `z-index:100` (`MAIN:4`; theme option `wp_estate_mobile_sticky_header` "yes", `ref/theme-options.json:397`). Measured at y=0 after scrolling 900px. Its containing block is `.wpresidence_main_wrapper_class`, so it stays for the whole page content |
| 1200px and wider (1200, 1440 measured) | `.top_bar_wrapper` 40px, then `.master_header` 90px (`.master_header .header_wrapper{height:var(--wp-estate-header-height-option…)}` = 90px, `ref/theme-options.json:335`) | **130px** | **no**. `.master_header{…position:relative…z-index:9999}` (`MAIN:4`). It becomes sticky only with the class `master_header_sticky` (`.master_header_sticky.master_header{position:sticky;top:0}`), which `wpestate_header_sticky()` adds (`CTRL:182-238`). That function is never called on this site: the scroll handler calls `wpestate_adv_search_sticky()` instead whenever `control_vars.stiky_search==='yes'` (`CTRL:70-78`), and it is "yes" (`STOP:7629-7632`; theme option `wp_estate_sticky_search`, `ref/theme-options.json:1516`). Measured with JavaScript off: header at y=-860 after scrolling 900px. `CUST` says the same in a comment (`STOP:973-974`) |

The same branch is the only place that adds `sticky_property_menu_visible` to the theme's property bar (`CTRL:216`),
which is why that bar never shows.

Logged-in editors (the `?bkr=1` preview) also have WordPress's admin bar; it is not in the saved pages. WordPress
core's defaults are 32px fixed from 783px, 46px from 601px to 782px, and 46px that scrolls away at 600px and below;
the theme adds `.admin-bar{margin-top:32px}` (`MAIN:4`). Not measured (section 7).

### 1.11 Stacking: what paints over what

`#all_wrapper` is positioned with `z-index:1`, both `!important`, so it is a stacking context. Inside it:
`.top_bar_wrapper` 99999, `.master_header` 9999 (mega panels 999 within it), `.mobile_header` 100, `.header_media`
11, `.wpestate_top_property_navigation` 999, and whatever the page adds.

Measured with a test layer (`D/verify-reset-and-stacking.txt`, blocks "scripts stripped, JS on"):

| Test | Result |
|---|---|
| A `position:fixed; inset:0; z-index:2147483647` element inside `#bkr` | covers the header and the page, but `a.backtop`, `a.contact-box` and a stand-in chat float (z-index 2147483647, appended to `<body>`) all paint **over** it |
| The same element appended to `<body>` | paints over all of them |
| `<dialog>` inside `#bkr`, opened with `showModal()` | paints over everything, including the stand-in float and the header (top layer) |
| Sticky bar inside `#bkr`, `top:56px`, `z-index:40`, at 390px after scrolling | sits at y=56 directly under the mobile header; nothing covers it |
| Fixed bottom bar inside `#bkr`, 61px, at 390px | y 783 to 844; `a.backtop` (y 704 to 754) is clear of it |

So the order on the page is: site footer (z-index auto) < everything in `#all_wrapper` (1) < `.prop-compare` (99) <
`a.contact-box` (100) < `a.backtop` (102) < `.contactformwrapper` (300) < theme mobile agent bar (990) <
`#modal_login_wrapper` (9999) < `.blockke-cta-bar` (99999) < chat launcher (999985) < chat and popup dialogs
(999990, 999991) < `.lightbox_property_wrapper` (999999). Where the SalesIQ float and the cookie banner sit in
that order is unknown; if either had no z-index at all it would paint under the page, which is unlikely for a float.

## 2. CSS that reaches plain elements inside a new root

Method: 86 plain elements (headings, text, lists, a table, a form with every control, media, and a block of
description HTML with its `wp-block-*` classes) were put in `<div id="bkr">` where the development layout puts
`#bkd`, in the classic page's shell, with a `bkr-page` body class. For each element Chrome's DevTools protocol listed
every author rule that matches (`D/matched-rules-1440.md`, `D/matched-rules-390.md`), and its computed style was
compared with the same fragment in a bare document (`D/computed-in-shell.md`). The tables below are that evidence,
condensed. "Net" is the computed result at 1440px; it is the same at 390px except where noted.

### 2.1 What every element inherits from `<body>`

Montserrat, Helvetica, sans-serif; **14px**; weight 400; colour `#1a1a1a`; `text-align:left`; and
**`line-height:23.1px`**. The theme declares the body line height in `em` (`--wp-estate-paragraph-line-height:1.65em`,
`MAIN:4`), so descendants inherit the computed length, not a ratio: a 40px heading with no line height of its own
would get 23.1px. The root of the layout must set a unitless `line-height`.

Rules: `body{…color:var(--bs-body-color);font-family:var(--bs-body-font-family);…}` (Bootstrap) and
`body{background-color:var(--wp-estate-background-color-option,…);color:var(--wp-estate-font-color-option,…);
font-family:var(--wp-estate-paragraph-font-family-option,…);font-size:…;font-weight:…;line-height:…;text-align:left}`
with `@media (min-width:576px){body{font-size:14px}}` (`MAIN:4`); `body, p, .elementor-widget-text-editor, input,
textarea, select, li{font-family:'Montserrat', Helvetica, sans-serif !important; color:#1a1a1a;}` (`STOP:1422-1425`);
`body.single-estate_property{background:#ffffff;}` (`TOC`, `STOP:764`).

### 2.2 Rules on bare tags (no class needed), by element

| Element | Net inside `#bkr` | Rules, in cascade order | Source |
|---|---|---|---|
| every element | `box-sizing:border-box` | `*,:after,:before{box-sizing:border-box}` | `MAIN:4` |
| `h1` to `h6` | Montserrat; `#0D2440`; `letter-spacing:-0.5px`; margin `0 0 15px`. h1 34px/44.2px/700; h2 28px/36.4px/700; h3 24px/31.2px/500; h4 18px/24px/500; h5 14px/14px/500; h6 12px/12px/500 | `h1,h2,h3,h4,h5,h6{color:var(--bs-heading-color);font-weight:500;line-height:1.2;margin-bottom:.5rem;margin-top:0}`; `h1{font-size:calc(1.375rem + 1.5vw)}` and so on; `h1,…,h6{color:var(--wp_estate_headings_color-option,var(--wp_estate_headings_color))}`; `h1,…,h6{margin-bottom:15px}`; `h1{font-family:var(--wp-estate-h1-font-family-option,…);font-size:…;font-weight:…;line-height:…}` and the same for h2 to h6 (defaults 34, 28, 24, 18, 14, 12px; `1.3em`, `1em` for h5 and h6; 500) | `MAIN:4` |
| | | `.h3,h3{font-family:"GT Walsheim";color:#202327;font-size:32px;line-height:44px;font-weight:500;letter-spacing:-.8px;padding:0;margin:0}`, `@media (max-width:1500px){.h3,h3{font-size:28px;line-height:40px}}`, `.h3:first-child,h3:first-child{margin-top:0}`. Loaded before `MAIN`, so `MAIN` overrides all but its letter spacing, which `CUST` then overrides | `STYLE:1` |
| | | `h1, h2, h3, h4, h5, h6, .elementor-heading-title, …{font-family:'Montserrat', Helvetica, sans-serif !important; color:#0D2440 !important; letter-spacing:-0.5px !important;}`; `h1, …{font-weight:700 !important;}`; `h2{font-weight:700 !important;}` | `STOP:1413-1421` |
| `p` | 14px / 23.1px / 400; margin `0 0 16px`; `#1a1a1a` set on the element | `p:not(.elementor-widget-container p){font-family:var(--wp-estate-paragraph-font-family-option,…);font-size:…;font-weight:…;line-height:…}` (specificity 0,1,2; defaults 14px, 1.65em, 400); `p{margin-bottom:1rem;margin-top:0}`; `p:empty{margin-bottom:0}` | `MAIN:4` |
| | | `body, p, …, li{font-family:… !important; color:#1a1a1a;}` | `STOP:1422-1425` |
| `a` | `#002147`; no underline; `transition:color .3s linear`; hover `#555`; focus `#000` and no outline | `a{color:rgba(var(--bs-link-color-rgb),var(--bs-link-opacity,1));text-decoration:underline}`; `body a{color:var(--wp_estate_link_color-option,var(--wp_estate_link_color));text-decoration:none;transition:color .3s linear;…}`; `a:hover{color:#0073e1}`; `a:focus,a:hover{color:var(--wp-estate-main-color-option,var(--wp-estate-main-color));outline:none;text-decoration:none}`; `a img{border:0}` | `MAIN:4` |
| | | `a{color:#0D2440;}` (loses to `body a`); `a:hover{color:#555 !important;}` | `STOP:1428-1429` |
| `ul`, `ol` | `list-style:none`; `padding-left:0`; margin `0 0 25px` | `ol,ul{padding-left:2rem}`; `dl,ol,ul{margin-bottom:1rem;margin-top:0}`; `ol,ul{list-style:none;margin-bottom:25px;margin-top:0;padding-left:0}`; `ol ol,ol ul,ul ol,ul ul{margin-bottom:0}` | `MAIN:4` |
| | | `ol,ul{box-sizing:border-box}` | `WPBL:1` |
| `li` | `#1a1a1a` set on the element (so it does not inherit a section's colour) | `body, p, …, li{…}` | `STOP:1422-1425` |
| `dl`, `dt`, `dd` | dl margin-bottom 26px; dt 700; dd margin `0 0 13px` | `dl{margin-bottom:26px}`; `dt{font-weight:700}`; `dd{margin-bottom:.5rem;margin-left:0}`; `dd{margin-bottom:13px}` | `MAIN:4` |
| `strong`, `b` | 700 | `b,strong{font-weight:bolder}`; `b,label,strong{font-weight:500}`; `b,strong{font-weight:700}` | `MAIN:4` |
| `small` | size of its parent | `small{font-size:.875em}`; later `small{font-size:100%}` | `MAIN:4` |
| `blockquote` | `float:left; width:100%`; italic; 14px; `border-left:2px solid #000`; margin `15px 0 30px`; padding `0 15px` | `blockquote{font-size:1.25rem;font-style:italic;margin:0 0 1rem}`; `blockquote{border-left:2px solid var(--wp-estate-main-color-option,…);float:left;font-size:14px;margin:15px 0 30px;padding:0 15px;width:100%}`; `blockquote p{font-style:italic;line-height:22px}`; `blockquote cite,blockquote small{font-weight:700;text-transform:uppercase}` | `MAIN:4` |
| `figure` | margin `0 0 16px` | `figure{margin:0 0 1rem}` | `MAIN:4` |
| | | `:where(figure){margin:0 0 1em}` | `WPBL:1` |
| `img`, `svg` | `vertical-align:middle`. **Nothing gives a plain `img` `max-width:100%` or `height:auto`** | `img,svg{vertical-align:middle}` | `MAIN:4` |
| `iframe`, `video` | `max-width:100%`; iframe without border | `embed,iframe,object,video{max-width:100%}`; `iframe{border:0}` | `MAIN:4` |
| `table` | `border:1px solid #f0f0f0`; radius 10px; `border-collapse:collapse`; margin-bottom 26px; `caption-side:bottom` | `table{caption-side:bottom}`; `table{border:1px solid #f0f0f0;border-collapse:separate;border-radius:var(--wp-estate-border-radius-corner-option,…);margin-bottom:26px}`; `table{border-collapse:collapse;border-spacing:0}` | `MAIN:4` |
| `th` | weight 400; left; padding 7px; `border-right` and `border-bottom` `1px solid #f0f0f0` | `th{text-align:inherit;text-align:-webkit-match-parent}`; `caption,td,th{font-weight:400;text-align:left}`; `table td,table th{border-right:1px solid #f0f0f0}`; `table th,table tr{border-bottom:1px solid #f0f0f0}`; `table th{padding:7px}` | `MAIN:4` |
| `td` | **`#222`**; padding `5px 6px`; `vertical-align:top`; `border-right` 1px | `table tbody tr td{color:#222;padding:5px 6px;vertical-align:top}` (specificity 0,0,4); `td{font-weight:400;text-align:left}` | `MAIN:4` |
| `tr`, `thead`, `tbody` | no borders | `tbody,td,tfoot,th,thead,tr{border:0 solid;border-color:inherit}`; `table tr{border:none}` | `MAIN:4` |
| `caption` | muted colour; padding `8px 0`; left; below the table | `caption{color:var(--bs-secondary-color);padding-bottom:.5rem;padding-top:.5rem;text-align:left}` | `MAIN:4` |
| `hr` | `border-top:1px solid` only; margin `16px 0`; **`opacity:.25`** | `hr{border:0;border-top:var(--bs-border-width) solid;color:inherit;margin:1rem 0;opacity:.25}` | `MAIN:4` |
| `label` | `display:inline-block`; 13px; 500; margin-bottom 7px | `label{display:inline-block}`; `label{font-size:13px;margin-bottom:7px}`; `b,label,strong{font-weight:500}` | `MAIN:4` |
| `fieldset` | no border, padding or margin; `min-width:0` | `fieldset{border:0;margin:0;min-width:0;padding:0}` | `MAIN:4` |
| `legend` | **`float:left; width:100%`**; 24px (21.6px at 390px); margin-bottom 8px; the next element gets `clear:left` | `legend{float:left;font-size:calc(1.275rem + .3vw);line-height:inherit;margin-bottom:.5rem;padding:0;width:100%}`; `@media (min-width:1200px){legend{font-size:1.5rem}}`; `legend+*{clear:left}` | `MAIN:4` |
| `input`, `select`, `textarea` | Montserrat 14px / 23.1px; `#1a1a1a`; margin 0; border colour `#d9d9d9`; radius 0; on focus border `#0D2440`, no shadow, no outline; textarea resizes vertically only | `button,input,optgroup,select,textarea{font-family:inherit;font-size:inherit;line-height:inherit;margin:0}`; `input{border-radius:var(--wp-estate-border-radius-corner-option,…)}`; `textarea{resize:vertical}`; `:focus{outline:0}` | `MAIN:4` |
| | | `input, textarea, select{border-radius:0 !important; border-color:#d9d9d9 !important;}`; `input:focus, textarea:focus, select:focus{border-color:#0D2440 !important; box-shadow:none !important;}` | `STOP:1463-1470` |
| `input[type=checkbox]` | the native box is replaced: `appearance:none`; 15 x 15; `1px solid` border; white; `outline:0 !important`; checked state is a Font Awesome glyph in `::before` | `input[type=checkbox]{-webkit-appearance:none;-moz-appearance:none;appearance:none;background-color:#fff;border:1px solid #e7e7e7;border-radius:…;box-sizing:border-box;color:#565a5c;height:15px;margin-bottom:0;margin-right:5px;margin-top:0;outline:0!important;padding:0;position:relative;vertical-align:top;width:15px}`; `input[type=checkbox]:checked:before{…content:"\f00c";…font-family:Font Awesome\ 5 Free;…}` | `MAIN:4` |
| `button` | radius 0; font inherited; `cursor:pointer`; no outline on mouse focus | `button{border-radius:0}`; `button,select{text-transform:none}`; `[type=button]:not(:disabled),[type=reset]:not(:disabled),[type=submit]:not(:disabled),button:not(:disabled){cursor:pointer}`; `button:focus:not(:focus-visible){outline:0}` | `MAIN:4` |
| `button[type=submit]`, `input[type=submit]` | navy `#0D2440` background; white text; `1px solid #0D2440`; radius 0; uppercase; `letter-spacing:1.5px`; Montserrat 600 12px; hover: white background, navy text. **Every declaration `!important`** | `.wpresidence_button, .wpestate_vc_button, .elementor-button, button[type="submit"], input[type="submit"], .read_more_button{background:#0D2440 !important; color:#ffffff !important; border:1px solid #0D2440 !important; border-radius:0 !important; text-transform:uppercase !important; letter-spacing:1.5px !important; font-family:'Montserrat', sans-serif !important; font-weight:600 !important; font-size:12px !important;}`; hover rule with `background:#ffffff !important; color:#0D2440 !important; border-color:#0D2440 !important;` | `STOP:1434-1457` |
| `summary` | `cursor:pointer` | `summary{cursor:pointer;display:list-item}` | `MAIN:4` |
| `address` | italic; margin `0 0 26px` | `address{font-style:normal;line-height:inherit;margin-bottom:1rem}`; `address{font-style:italic;margin:0 0 26px}` | `MAIN:4` |
| `sup`, `sub`, `mark`, `code`, `kbd`, `pre`, `abbr[title]` | Bootstrap Reboot values (`mark` `#fff3cd`; `code` `#d63384`, monospace; `pre` margin-bottom 26px) | | `MAIN:4` |
| any focused element | `outline:0` | `:focus{outline:0}` | `MAIN:4` |
| `[hidden]` | `display:none !important` | `[hidden]{display:none!important}` | `MAIN:4` |
| selected text | white text on the browser's own highlight colour | `body.single-estate_property ::selection{background: #0D2440.;color: #ffffff;}` (the background value has a stray full stop and is dropped; the colour applies) | `TOC`, `STOP:764` |
| any transition, when the visitor prefers reduced motion | no duration | `@media (prefers-reduced-motion:reduce){html *{transition-delay:0s!important;transition-duration:0s!important}}` | `ELF:1` |

A `<button>` without a `type` attribute does not match `button[type="submit"]` (it is an attribute selector), although
it submits a form all the same. Do not rely on that; override with the id and `!important` (section 5.1).

### 2.3 Every `!important` on a generic selector

| Selector | Declarations | Source |
|---|---|---|
| `body.single-estate_property :is(p, span, a, li, div, td, th, h1, h2, h3, h4, h5, h6, strong, b, em, small, label, button, input, select, textarea, blockquote, figcaption):not([class*="fa-"]):not([class*="fa "]):not([class*="icon"]):not([class*="swiper"]):not([class*="dashicons"])` (specificity 0,6,2) | `font-family:'Montserrat', Helvetica, sans-serif !important` | `STOP:1407-1410` |
| `h1, h2, h3, h4, h5, h6` | `font-family … !important; color:#0D2440 !important; letter-spacing:-0.5px !important` | `STOP:1413-1419` |
| `h1`; `h2` | `font-weight:700 !important` | `STOP:1420`; `STOP:1421` |
| `body, p, input, textarea, select, li` | `font-family … !important` (the colour in the same rule is not important) | `STOP:1422-1425` |
| `a:hover` | `color:#555 !important` | `STOP:1429` |
| `button[type="submit"], input[type="submit"]` and their `:hover` | the whole skin, see 2.2 | `STOP:1434-1457` |
| `input, textarea, select` | `border-radius:0 !important; border-color:#d9d9d9 !important` | `STOP:1463-1466` |
| `input:focus, textarea:focus, select:focus` | `border-color:#0D2440 !important; box-shadow:none !important` | `STOP:1467-1470` |
| `input[type=checkbox]` | `outline:0!important` | `MAIN:4` |
| `[hidden]` | `display:none!important` | `MAIN:4` |
| `[list]:not([type=date])…::-webkit-calendar-picker-indicator` | `display:none!important` | `MAIN:4` |
| `html *` under `prefers-reduced-motion:reduce` | `transition-delay:0s!important; transition-duration:0s!important` | `ELF:1` |

Conditional on a class, but then generic: `.loaded,.loaded *{transition:.2s cubic-bezier(.165,.84,.44,1)!important}`
(`STYLE:1`; nothing in the saved pages carries `loaded`; never put that class on `#bkr` or an ancestor);
`img.wp-smiley, img.emoji{display:inline !important; … height:1em !important; width:1em !important; …}`
(`STOP:49-63`); Elementor's `.e-con.e-parent:nth-of-type(n+4):not(.e-lazyloaded)…, … *{background-image:none !important;}`
(`STOP:777-794`).

A non-important declaration never beats an important one, whatever its specificity. To change any property in this
table for an element inside `#bkr`, the rule needs `!important` and a selector that outranks the site's; any selector
starting with `#bkr` does (an id outranks any number of classes and attributes).

### 2.4 Block's custom CSS that reaches into any single-property page

`TOC` has 187 rules and `CUST` has 104 rules that start with `body.single-estate_property` (counted). All but the
following name ids and classes of the classic template (`#wpestate_sidebar_property_contact_tabs`,
`ul.overview_element`, `.accordion.property-panel`, `.listing_detail`, `.price_area`, `.subunit_wrapper`,
`ol.breadcrumb`, …) and cannot match `bkr-` markup.

| Selector | Declarations | Source |
|---|---|---|
| `body.single-estate_property :is(p, span, a, li, div, …):not(…)` | Montserrat `!important` (2.3) | `STOP:1407-1410` |
| `body.single-estate_property ::selection` | `color:#ffffff` (2.2) | `TOC`, `STOP:764` |
| `body.single-estate_property .single-content p, body.single-estate_property .wpestate_property_description p, body.single-estate_property [class*="property_description" i] p` | `font-size:16.5px !important; line-height:1.75 !important; color:#2a2a2a !important; max-width:68ch` | `TOC`, `STOP:764` |
| `body.single-estate_property .feature_chapter_name, body.single-estate_property [class*="feature_chapter" i]` | `font-family:'Space Grotesk', … !important; font-size:24px !important; font-weight:700 !important; letter-spacing:-0.5px !important; color:#0D2440 !important` (`CUST` then sets Montserrat, `STOP:1354-1401`) | `TOC`, `STOP:764` |
| `body.single-estate_property #googleMap, body.single-estate_property [id*="gmap"], body.single-estate_property [class*="gmap_wrapper"]` | `border-radius:0 !important` | `TOC`, `STOP:764` |
| `body.single-estate_property` | `background:#ffffff` | `TOC`, `STOP:764` |
| `body.single-estate_property` at 991px and below | `padding-bottom:76px` | `STOP:1837` |
| `body.single-estate_property.blockke-bar-open` | `padding-bottom:calc(96px + env(safe-area-inset-bottom, 0px))`; `0` from 1025px | `TOC`, `STOP:764` |
| `body.single-estate_property .mobile_agent_area_wrapper` at 991px and below | `display:flex !important; … z-index:990` and the rules for its children | `STOP:1738-1835` |
| `body.single-estate_property .blockke-cta-bar` | `position:fixed; left:0; right:0; bottom:0; z-index:99999; …`; `display:none !important` from 1025px (`TOC`) and at 991px and below (`STOP:1736`) | `TOC`, `STOP:764`; `STOP:1736` |
| `body.single-estate_property a.contact-box`; `a.backtop` at 991px and below | `display:none !important`; `bottom:90px` | `STOP:1839-1840` |
| in `@media print`: `body.single-estate_property img` and a list of classic blocks | `max-width:100% !important`; `display:none !important` | `TOC`, `STOP:764` |

`TOC` still names the families 'Inter' (20 times) and 'Space Grotesk' (19 times); neither is loaded anywhere, and
`CUST` replaces them with Montserrat.

### 2.5 Names to keep out of the rental markup

The `bkr-` prefix avoids almost all of this. The traps are substring selectors, bare state classes, and elements:

- **Class substrings**: `property_description`, `feature_chapter`, `gmap_wrapper` (2.4). **Id substring**: `gmap`
  (so not `bkr-gmap`).
- **Classes the site styles or scripts by name**: `single-content`, `wpestate_property_description` (also the anchor
  the nearby-places script looks for before it calls the Google Places API, `STOP:6500-6501`), `property_listing`
  (project-name injector, `STOP:5845`), `listing_detail`, `price_area`, `agent_unit`, `agent_contanct_form_sidebar`
  (`STOP:5941-5945`), `lightbox_trigger` (schema script and theme lightbox), `retina_ready` (`CTRL:1163`),
  `venobox`, `nested-link`, `share_unit` (hidden on every click, `CTRL:1490`).
- **Bare class rules in `MAIN`, `STYLE`, `ELF`** that would apply to an unprefixed state or helper class: `collapse`
  (`.collapse:not(.show){display:none}`), `fade` (`.fade:not(.show){opacity:0}`), `collapsing`, `invisible`,
  `visible`, `section` (`@media screen and (max-width:768px){.section{flex-direction:column;height:100%!important}}`),
  `row`, `col`, `container`, `btn`, `card`, `modal`, `nav`, `badge`, `table`, `label`, `lead`, `small`, `mark`,
  `even`, `animated`, `loaded`, `clearfix`, `text-center`, `sr-only`, `visually-hidden`, `screen-reader-text`. There
  is no bare rule for `on`, `open`, `active`, `show`, `hidden` or `is-…`.
- **Attributes Bootstrap's and the theme's scripts act on**: `data-bs-toggle`, `data-bs-target`, `data-bs-dismiss`,
  `data-bs-slide`, `data-toggle` (`[data-bs-toggle="tooltip"]` is initialised on every page, `CTRL:1982-1986`), and
  `li[data-slide-to]` (`CTRL:3836`).
- **Elements**: a call, WhatsApp or e-mail link inside a `<header>` or `<footer>` element is reported by the contact
  tracker as placement `header` or `footer` (`STOP:7896-7897`). Use `section`, `div`, `nav`.
- **Ids the shell already uses** (some twice): `agent_property_ajax_nonce` (`STOP:7250` and in the lightbox),
  `agent_property_id`, `agent_id`, `owl-demo`, `logo_image`, `user_menu_u`, `primary`, `price_low`, `price_max`,
  `keyword`, `bke-nearby`, `bke-schema-ld` (reserved, section 4.3), and everything starting `blkac`, `blkbp`, `blkct`,
  `bkd`, `bkef`, `bke-`, `zsiq`, `zcb`. Nothing in the saved pages or the downloaded files contains `bkr` in any
  form (searched), so `#bkr`, `bkr-…` and `window.BKR` are free.

### 2.6 The description HTML brings its own classes

Across the 129 saved rent-only pages the description holds these tags: `p`, `ul`, `li`, `strong`, `h2`, `h3`, `h4`
(`h1` on 3 pages), `figure > table` with `thead`, `tbody`, `tr`, `th`, `td` (110 pages), `a`, `em`, `span`, `br`,
`hr` (6), `blockquote` (3); and these classes: `wp-block-paragraph`, `wp-block-heading`, `wp-block-list`,
`wp-block-table` with `has-fixed-layout` (110 pages), `wp-block-quote is-layout-flow` (3), Rank Math's FAQ classes
`rank-math-block`, `rank-math-list`, `rank-math-list-item`, `rank-math-question`, `rank-math-answer` (17),
`wp-block-pdfemb-pdf-embedder-viewer` (2), and Cloudflare's `__cf_email__` (62). No inline `style` attributes.
(Counted with `notes/04-site-shell-data/harness/` tools over `ref/pages-final` and `ref/pages-stop`; the data audit
has the content side.)

Rules that match those classes wherever the HTML is printed:

| Selector | Declarations | Source |
|---|---|---|
| `.wp-block-table` | `overflow-x:auto` | `WPBL:1` |
| `.wp-block-table table` | `border-collapse:collapse; width:100%` | `WPBL:1` |
| `.wp-block-table thead`; `tfoot` | `border-bottom:3px solid`; `border-top:3px solid` | `WPBL:1` |
| `.wp-block-table td, .wp-block-table th` | `border:1px solid; padding:.5em` | `WPBL:1` |
| `.wp-block-table .has-fixed-layout` | `table-layout:fixed; width:100%` | `WPBL:1` |
| `.wp-block-table .has-fixed-layout td, … th` | `word-break:break-word` | `WPBL:1` |
| `.wp-block-quote` | `box-sizing:border-box; overflow-wrap:break-word`; `.wp-block-quote>cite{display:block}` | `WPBL:1` |
| `.wp-block-separator` | `border:none; border-top:2px solid` | `WPBL:1` |
| `.single-blog ul, .wp-block-list ul`; `… ol` | `list-style-type:square; margin:0 0 25px 25px`; `decimal` (a list nested inside a `.wp-block-list`) | `MAIN:4` |

`wp-block-paragraph`, `wp-block-heading`, `wp-block-list` themselves and the Rank Math FAQ classes have no rule in
any loaded stylesheet; they get the bare-tag rules of 2.2 (so list items in a description have no bullets until the
layout gives them some). The table rules above give a fixed-layout table full width with `1px solid` cell borders in
the current text colour; with the theme's `table` rules on top, a description table inside `#bkr` has a `#f0f0f0`
outer border, a 10px radius and `#222` cells.

### 2.7 Container widths that exist on the site

| What | Width | Source |
|---|---|---|
| Parent of `#bkr` | 100% of the viewport, no padding | 1.6 |
| Theme content box `.wpresidence-content-container-wrapper` | 1540px max, `padding:0 15px` (12px below 1200px) | `MAIN:4`; `ref/theme-options.json:1248` |
| Site header `.header_wrapper_inside.mx-5` | fluid; side margins 16px from 1200px to 1320px, 24px from 1321px to 1680px, 48px above 1680px | `.mx-5{margin-left:3rem!important;margin-right:3rem!important}` (`MAIN:4`), overridden in `style#bke-cy-css`, `STOP:875-886`. Measured: x 16 to 1184 at 1200px, x 24 to 1416 at 1440px |
| Mobile header | full width; menu icon at the left edge, logo centred, user icon at the right edge | 1.5 |
| Site footer `.bkef-in` | fluid; padding 64px, 34px at 1024px and below, 22px at 767px and below | `STOP:6908`, `:6948`, `:6958` |
| Development layout `.bkd-wrap` | `max-width:calc(1200px + 2 * gutter)`, gutter 20px, 40px from 768px | `ref/dev-v1.9.1.php:9237-9239`, `:9251` |
| Bootstrap `.container` | 540, 720, 960, 1140, 1320px | `MAIN:4` |
| Elementor boxed | 1140px | `ref/site-css/elementor-kit-post-24.css:1` |

## 3. How the development layout's CSS defends itself

The reset, as it is in `ref/dev-v1.9.1.php:9237-9257` (identical in the live `ref/dev-v1.8.2.php:7481-7501`; printed
at `DEV:896-916`):

```css
#bkd{--navy:#0D2440;--navy-2:#16345C;--ink:#22303C;--slate:#46586A;--muted:#5F6D7E;--paper:#FBF9F6;--mist:#F4EEE4;--line:#E6DFD4;--line-2:#D5CBBB;--brass:#B98A44;--brass-d:#80602A;--brass-t:#9E7433;--brass-l:#DDB97F;--brass-tint:#F1E4CC;--on-navy:#C9D3DF;--navy-line:rgba(255,255,255,.14);--wa:#25D366;--err:#B3261E;--font:"Montserrat",system-ui,-apple-system,"Segoe UI",Roboto,sans-serif;--display:"Instrument Serif",Georgia,"Times New Roman",serif;--display-w:400;--display-ls:-.01em;--max:1200px;--gutter:20px;--sec:clamp(80px,10vw,150px);--top:0px;
position:relative;display:block;float:none;clear:both;width:var(--bkd-vw,100vw);max-width:none;margin:0 0 0 calc(50% - var(--bkd-vw,100vw) / 2);padding:0;background:var(--paper);color:var(--ink);font:400 16px/1.75 var(--font);text-align:left;-webkit-font-smoothing:antialiased;overflow-wrap:break-word}
@media (min-width:768px){#bkd{--gutter:40px}}
body.bkd-page{overflow-x:clip}
#bkd :where(*,*::before,*::after){box-sizing:border-box}
#bkd [hidden]{display:none!important}
#bkd :where(h1,h2,h3,h4,h5,h6,p,ul,ol,li,dl,dt,dd,figure,blockquote,table,form,fieldset,legend,details,summary){margin:0;padding:0;border:0}
#bkd :where(ul,ol){list-style:none}
#bkd :where(img,svg,iframe){display:block;max-width:100%}
#bkd :where(a){color:inherit;text-decoration:none}
#bkd a:hover{color:inherit!important}
#bkd :where(button,input,select,textarea){font:inherit;color:inherit;letter-spacing:inherit;text-transform:none;margin:0}
#bkd :focus-visible{outline:2px solid var(--navy);outline-offset:3px}
#bkd .bkd-sprite{position:absolute;width:0;height:0;overflow:hidden}
#bkd .bkd-wrap{width:100%;max-width:calc(var(--max) + var(--gutter) * 2);margin:0 auto;padding:0 var(--gutter)}
#bkd .bkd-sr{position:absolute;width:1px;height:1px;overflow:hidden;clip:rect(0 0 0 0);white-space:nowrap}
#bkd .bkd-i{width:18px;height:18px;flex:none;fill:none;stroke:currentColor;stroke-width:1.6;stroke-linecap:round;stroke-linejoin:round}
#bkd .bkd-i-f{fill:currentColor;stroke:none}
#bkd .bkd-wa-ic{color:#1DA851}
#bkd h1,#bkd h2,#bkd h3,#bkd h4,#bkd h5,#bkd h6{font-family:var(--font)!important;color:var(--navy)!important;text-transform:none!important;margin:0}
#bkd .bkd-h1,#bkd .bkd-h2{font-family:var(--display)!important;font-weight:var(--display-w)!important;letter-spacing:var(--display-ls)!important}
```

The strategy, in four moves:

1. **An id on every selector.** Every rule starts `#bkd`, so its specificity is at least (1,0,0). The most specific
   generic rule the site has is the Montserrat sweep at (0,6,2); the theme's bare-tag rules are (0,0,1) to (0,1,2). An
   id outranks all of them, so no rule depends on where in the head it is printed (it is printed before `CUST`).
2. **`:where()` inside the reset.** `#bkd :where(p)` weighs exactly (1,0,0): enough to beat the theme, and less than
   any component rule (`#bkd .bkd-x` is (1,1,0)), so components never have to fight the reset.
3. **`!important` only where the site uses it** (2.3), and then on every such property: headings (`font-family`,
   `color`, `text-transform` in the reset; `font-weight`, `letter-spacing`, `font-size`, `line-height` on `.bkd-h1`,
   `.bkd-h2`, `.bkd-h3`, lines 9257-9261); `a:hover` (9247, and each link class that wants a hover colour repeats
   `!important`, for example 9294); buttons (`.bkd-btn`: `padding`, `border`, `border-radius`, `font`,
   `letter-spacing`, `text-transform`, `text-decoration`, `box-shadow` all `!important`, 9270, and each colour variant,
   9272-9277); form fields (`#bkd .bkd-f>input,#bkd .bkd-f>textarea{…border:1px solid var(--line-2)!important;
   border-radius:0!important;background:#fff!important;box-shadow:none!important;font:400 16px/1.4 var(--font)!important;
   color:var(--navy)!important;-webkit-appearance:none;appearance:none…}` and its `:focus` with
   `box-shadow:0 0 0 3px rgba(13,36,64,.1)!important`, 9341-9344; the calculator's fields the same way,
   `ref/dev-v1.9.1.php:1452-1454`).
4. **The root cuts inheritance.** `font:400 16px/1.75 var(--font)` (a unitless line height), its own `color`,
   `background`, `text-align`, so nothing arrives from the 14px / 23.1px `<body>`.

Around the reset: the full-bleed width (`width:var(--bkd-vw,100vw)` with a negative margin, `body.bkd-page{overflow-x:clip}`,
and a script that sets `--bkd-vw` to `documentElement.clientWidth`, `ref/dev-v1.9.1.php:9833-9834`) is not needed,
because the parent already spans the viewport (1.6). The sticky offset is the variable `--top`, measured by script: it
looks at `document.elementsFromPoint(innerWidth/2, 2)`, walks up from each hit, and takes the lowest bottom edge of a
fixed or sticky element outside `#bkd` that touches the top and is under 220px high (`:9836-9851`); run on scroll at
most every 250ms. Layers: sticky section bar 40 (`:9379`), phone bar 990 (`:9584`), dialogs 999992 (`:9590`), photo
viewer 999993 (`:9604`); `html.bkd-lock,html.bkd-lock body{overflow:hidden}` and the two `#zsiq_float` rules
(`:9600-9602`).

**What that reset does not cover** (measured on `DEV`; these are live today):

- Bare paragraphs. The theme's `p:not(.elementor-widget-container p)` (0,1,2) and `CUST`'s `p{color:#1a1a1a}` still
  apply: inside `#bkd`, whose root is 16px / 28px / `#22303C`, the 21 `p.wp-block-paragraph` and 7 plain `p` render
  at **14px / 23.1px in `#1a1a1a`**, `p.bkd-body` at 14px, the description's 32 `td` at 14px in `#222`, and `li` in
  `#1a1a1a` (`D/dev-layout-text-sizes.txt`, from `D/harness/devp.js`).
- Stacking. Its dialogs (999992, 999993) are inside `#all_wrapper`, so `a.backtop` and the chat float paint over
  them; it hides the Zoho float while a dialog is open, nothing else.
- The phone bar. The script shows `#bkd-mb` only when the theme's bar is not displayed
  (`themeBar()`, `ref/dev-v1.9.1.php:10132`, `:10140`), and `CUST` forces the theme's bar on below 992px, so on phones
  the development layout shows the theme's agent bar, not its own (measured at 390px: theme bar `display:flex`,
  `#bkd-mb` translated off screen). Between 992px and 1024px `.blockke-cta-bar` appears as well.
- In-page links. Its section links are plain `href="#bkd-…"` anchors, so they trigger the theme's hash handler (4.2).
- `::selection`, `hr` opacity, the checkbox skin, `label`, `legend`, `blockquote`, `td` padding and borders: not
  reset (it sets what it uses, by class).

## 4. JavaScript that runs on a single-property page

### 4.1 What loads, in order

Head: `tp-tools-js` and `sr7-js` (Slider Revolution, async), `jquery-core-js` 3.7.1, `jquery-migrate-js` 3.4.1,
`modernizr.custom.62456-js`, `facebook-signal-js`, `google_gtagjs-js`, the Google Ads tag, Zoho PageSense
(synchronous), Google Tag Manager (`STOP:119-153`, `:838-851`).

Footer, `STOP:7621-7718`, in this order: `wpr-translate-ajax-language-js`; `bootstrap-js-js` (the theme's
`bootstrap.bundle.min.js`); `bootstrap-selectjs-js`; `slick.min-js`; `control-js` (`CTRL`) with `control-js-extra`
(`control_vars`); jQuery UI `core`, `mouse`, `draggable`, `menu`; `wp-dom-ready-js`, `wp-hooks-js`, `wp-i18n-js`,
`wp-a11y-js`; jQuery UI `autocomplete`, `slider`, `datepicker`; `jquery.fancybox.pack-js`,
`jquery.fancybox-thumbs-js`; `dense.min-js`; `placeholders.min-js`; `slideout.min-js`; `venobox.min-js`;
`jquery.chart.min-js`; `datepicker-en-GB-js`, `datepicker-us-js`; `touch-punch-js`; `wpestate_shared_ajax_map-js`;
`mapfunctions-js`; `wpestate_mapfunctions_base-js`; `googlemap-js` (the Google Maps JavaScript API,
`libraries=places&callback=wpestateInitMap`); `infobox-js`; `markerclusterer-js`; `oms.min-js`;
`wpestate_marker_min-js`; `googlecode_property-js`; `wpestate_property-js`; `ajaxcalls-js`;
`wpestate_login_register-js`; two Site Kit event providers (deferred); `elementor-webpack-runtime-js`,
`elementor-frontend-modules-js`, `elementor-frontend-js`; `owl_carousel-js`; the emoji loader.

The list is identical on `DEV` (`DEV:6803-6907`): the theme enqueues all of it for the post type, whatever template
draws the page. So the Google Maps API with the Places library, Chart.js, Slick, Owl, Fancybox and jQuery UI all load
on a rental page too, used or not.

Inline configuration worth knowing: `control_vars` (`STOP:7629-7632`: `scroll_trigger` "90", `stiky_search` "yes",
`content_type` "accordion", `wp_estate_lightbox_slider` "owl"); `googlecode_property_vars` (`STOP:7683-7687`: the
listing's `general_latitude` "-1.26896389", `general_longitude` "36.80662778", `page_custom_zoom` "16",
`current_id` "45577"); `wpestate_property_vars` (`STOP:7689-7692`: daily view counts); `window.BLOCKCT`
(`STOP:7866`); `window.BLOCKKE_PROJECTS` (`STOP:6645`); `window.BLKAC_CFG` (`STOP:7800`).

### 4.2 Global handlers and hijacks

**The hash handler (the one that matters).** At the top level of `CTRL`, so on every page:

```js
CTRL:3773  jQuery(window).on('hashchange', function(event) {
CTRL:3774          event.preventDefault();
CTRL:3775          history.pushState("", document.title, window.location.pathname + window.location.search); // Clear the hash
CTRL:3776  });
```

Measured in Chrome with that handler attached to the test page (`D/hashchange-test.txt`):

| Step | Without the handler | With the handler |
|---|---|---|
| start | `history.length` 2 | 2 |
| click `<a href="#bkr-s2">` | URL ends `#bkr-s2`; length 3; scrolls; `:target` = `bkr-s2` | URL has **no hash**; length **4**; scrolls; `:target` still `bkr-s2` |
| `history.back()` | back at the top, hash gone, `:target` none | stays on the page; URL unchanged; `:target` still `bkr-s2` |
| `history.back()` again | leaves the page | stays, and again for five calls in a row |

Each Back lands on the entry that has the hash, which fires `hashchange`, which pushes a new entry. Chrome's Back
button may skip entries added without a user gesture, so a real press can behave better than `history.back()`; other
browsers were not tested (section 7). Consequences for the rental page: do not let an in-page link perform a hash
navigation when script is running (handle the click, `preventDefault()`, scroll with `scrollIntoView()` and move
focus); do not style or toggle anything with `:target`; do not read `location.hash` after load. Without JavaScript
the handler does not exist and plain anchors work normally. A URL that arrives with a hash keeps it (no `hashchange`
on load).

**Smooth scrolling is CSS, not script.** `:root{scroll-behavior:smooth}` (`MAIN:4`) and `html{scroll-behavior:smooth}`
(`ELF:1`), both only under `prefers-reduced-motion:no-preference`. So anchor jumps, `scrollIntoView()` and
`window.scrollTo()` without an explicit `behavior` all animate. A script that scrolls and then measures must pass
`behavior:'instant'` or wait.

**Other theme handlers** (all in `CTRL`), none of which touches elements by tag:

| Lines | What |
|---|---|
| 70-94 | `window` `scroll`: toggles `.islive` on `.contact-box` and `.backtop` at 90px; binds a click handler to `.contact_close_button` again on every scroll event |
| 263-313 | `wpestate_property_sticky()` (called inline in the header): `click` on `.wpestate_top_property_navigation a` does `preventDefault()` then `jQuery('html, body').animate({scrollTop: $targetElement.offset().top - 80})`. On a page without the classic sections `$targetElement` is empty and `.offset().top` throws a `TypeError`. The links are off screen but, from 1201px, focusable; a keyboard user can reach and press one |
| 722-727, 1479-1503 | `document` `click`: fade out `#results`; hide `#wpestate_header_shoping_cart`, `#user_menu_open`, every `.share_unit` |
| 1163-1166 | `jQuery('.retina_ready').dense(…)`: swaps image sources for `_2x` files on high-density screens, for that class only |
| 1975-1988 | Bootstrap tooltips for every `[data-bs-toggle="tooltip"]` |
| 2132-2161 | delegated `click` on `.megamenu-studio-toggle`; `keydown` Escape closes open mega panels |
| 3836 | `click` on every `li[data-slide-to]` |
| 5029-5101 | Slideout drawers with `#all_wrapper` as the panel (1.4) |
| 731-742, 17-19 | on `window` `load`: for `.property_unit_carousel img`, copies `data-lazy-load-src` into `src` |

Not read (not downloaded): Bootstrap's bundle (its data API listens at `document` for `data-bs-*` attributes),
Elementor's `frontend.min.js` (in Elementor's published source the anchor handler acts only on targets matching
`.elementor-element, .elementor-menu-anchor`; not verified for 4.4.0), `ajaxcalls.min.js`, `mapfunctions.min.js`.
The live development layout runs all of them around a foreign root without visible trouble, which is the evidence
that they key on the theme's own ids and classes.

**Site-wide listeners from Block's snippets** (details in `notes/05-live-snippets.md`):

| Where | What |
|---|---|
| `STOP:7477-7564` (snippet 33892) | wraps `XMLHttpRequest.prototype.send` and `window.fetch` (pass-through; it fires a Google Ads conversion only for the theme's contact form and for a POST whose URL contains `/wp-json/block/v1/buyer-lead`); a capturing `click` listener that fires the WhatsApp or call conversion once per page for `a[href]` to `wa.me/<number>` or `tel:`; defines `window.blockkeLeadConversion` |
| `STOP:7867-7918` (snippet 40769) | a `MutationObserver` on `document.body` and a capturing `click` listener (4.3) |
| `STOP:7114-7133` | capturing `click` listener, acts only on `.mobile_user_menu .wpestate-crm-dashboard > a` |
| `STOP:386-418` | at DOM ready rewrites `a[href*="/category/…"]` for nine category slugs to their hub pages |
| `STOP:7920-8010` (`bke-cy-js`) | adds a "Completion" select to every `form[action*="advanced-search"]`; relabels `.ribbon-inside` in `[data-listid]` cards; `MutationObserver` on the document; a `document` `click` listener that re-syncs its select |
| `STOP:6699-6701` | speculation rules: same-site links are prefetched on pointer down (`eagerness:"conservative"`), not those with a query string, `rel="nofollow"` or class `no-prefetch` |

### 4.3 Block's inline scripts on a page that has no classic markup

Run in the harness on `DEV` and on a test `#bkr` root (`D/inline-scripts-run.txt`; external scripts were aborted, so
the only errors were `jQuery`, `wp` and `FacebookSignal` being undefined, which is the harness, not the page).

| Script | Guard | On a rental layout |
|---|---|---|
| `bke-desc-collapse` (`STOP:161-247`) | needs `#wpestate_property_description_section` (`:174-175`) | nothing |
| `bke-mobile-cta` (`STOP:257-321`) | needs `.mobile_agent_area_wrapper` and a non-empty `.price_area` (`:282-290`) | nothing (no `.price_area`) |
| `bke-header-phone` (`STOP:2884-2913`) | header only | as everywhere |
| `bke-listing-schema` (`STOP:2927-3131`) | `body.single-estate_property`; returns if `#bke-schema-ld` exists (`:3020-3021`); needs an `h1` (`:3025-3027`) | **adds `<script type="application/ld+json" id="bke-schema-ld">` to the head** on DOM ready: a `RealEstateListing` whose `name` is the first `h1`, `description` the meta description, and `about` `{"@type":"Residence","name":…}`. With no `.single_property_labels` the type function gets an empty string and falls through to `'Residence'` (`:3000-3007`), for offices and warehouses too. No image, no offer (they come from `.lightbox_trigger` and `.price_area`). Measured on `DEV`: `"name":"Kings Courtyard","about":{"@type":"Residence",…}`. With an element of that id already in the page it does nothing (measured, `D/listing-schema-test.txt`). v1.9.1's rent mode prints its own `script#bke-schema-ld` for this reason (`ref/dev-v1.9.1.php:8546`) |
| `blockke-booking-ux` (`STOP:5863-6432`) | `body.single-estate_property` (`:5896`); runs once (`:5900`) | finds no booking card, but `hasWa` is true, so it **still builds `div.blockke-cta-bar` and appends it to `<body>`** (`:6048-6089`): one link "WhatsApp" to `wa.me/254725937686` with the text "Hello Block Real Estate, I would like to book a viewing for: {text of the first h1} — {URL}" (`:5951-5961`). Its "hero" is the first `[class*="lightbox_property"]`, which is the theme's hidden lightbox (`:6101-6104`), so the observer reports it gone at once: the bar is `is-active` and `<body>` gets `blockke-bar-open` from page load (`:6093-6111`). Measured: body `padding-bottom` 96px at 390px and 1010px; the bar visible at 1010px (1.9). It also listens at `document` for `focusin` / `focusout` and adds `is-suppressed` to its bar while any `input`, `textarea`, `select`, `button` or `iframe` has focus (`:6122-6145`), and for `click` on `.agent_submit_class` |
| `blockke-project-cards` (`STOP:5786-5862`) | `.property_listing` cards | nothing, unless the page uses that class |
| `blockke-nearby-places` (`STOP:6446-6626`) | `body` class; needs `.wpestate_property_description` or `.single-content` (`:6500-6507`) | nothing. With either class present it would query Google Places and insert `#bke-nearby` |
| development snippet, classic extras: `link#bkd-font`, `style#bkd-cls-css` (`STOP:892-925`), `svg.bkd-sprite`, `div#bkd-cls[hidden]`, `script#bkd-cls-js` (`STOP:7727-7739`) | printed on any single listing where `$GLOBALS['blockke_dev_active']` is empty and the listing has features or nearby places (`ref/dev-v1.8.2.php:1553`, `:1653-1705`) | would print on a rental layout as well: a request for Instrument Serif, 5 KB of CSS, and a hidden copy of the features and nearby lists that its script then deletes (`:1687-1699`) because it finds no accordion. No visible effect. Whether the rental snippet sets that global to silence it belongs to `notes/01-dev-layout.md`; v1.9.1's classic rental pieces test the same global (`ref/dev-v1.9.1.php:1862`) |
| `blkct-js` (`STOP:7867-7918`) | `window.BLOCKCT.listing` | **rewrites every WhatsApp link that has a number and no `data-blkct`** (at load and for links added later): sets `data-blkct="1"`; if the link's `text` contains "viewing" it appends the reference ("Ref W-45577."), otherwise it **replaces the text** with `BLOCKCT.msg` (`:7873-7890`). A link that already carries `data-blkct` is left alone (`:7877`), which is what rule 14 of the brief relies on. Every tap on `tel:`, WhatsApp-with-number and `mailto:` links is sent to `block/v1/contact-click`, to `gtag` as `contact_click` and to `fbq` as `Contact` (`:7901-7916`), with a `where` worked out from the link's ancestors: `.mobile_agent_area_wrapper`, the agent-card classes, `.blockke-cta-bar`, `.blockke-wa-card`, then `footer, .footer, #footer, .bkef-crow, .bkef`, then `header, .header_wrapper, .header_phone, .mobile_header`, `#blkbp`, else `other` (`:7891-7900`). Links in `#bkr` report `other` unless they sit in a `<header>` or `<footer>` |
| `blkac-js` (`STOP:7801-7864`) | `BLKAC_CFG.mode` | hidden for ordinary visitors today (1.9) |
| `blkbp-attr` (`STOP:7865`) | none | stores the click ids and utm tags in `sessionStorage` `blkbp_attr` |

### 4.4 Images: is anything rewriting `<img>`?

- **No lazy-load or image-optimisation plugin** is at work: no `data-src`, `lazysizes`, Jetpack, Smush, ShortPixel or
  similar in either page (searched). The images in `#bkd` keep the attributes the snippet asked for.
- The theme's own cards carry `class="lazyload"` and `data-original` equal to `src`; the only theme code found that
  swaps sources is the `data-lazy-load-src` loop above. Do not use the class `lazyload`.
- **WordPress adds attributes to `wp_get_attachment_image()` output, and on this site they come out twice.** The
  development layout's hero is printed as `<img fetchpriority="high" width="2048" height="1365" src="…" class="bkd-hero-img"
  … loading="eager" fetchpriority="high" decoding="async" …>` and its gallery images as `<img loading="lazy" width=… …
  loading="lazy" decoding="async" sizes="auto, (min-width: 1000px) 340px, 1px" …>` (`DEV:4640`), from calls that pass
  `'loading'` and `'fetchpriority'` once (`ref/dev-v1.9.1.php:8728-8740`, `:8750`). Browsers keep the first of a
  duplicated attribute, so it works, but the HTML is invalid, and `sizes` gains an `auto, ` prefix on lazy images.
  What adds the leading copy is unknown (section 7).
- The emoji loader replaces emoji characters with `<img class="emoji">` when the browser cannot draw them
  (`STOP:7719-7726`).
- Elementor's observer adds `e-lazyloaded` to `.e-con.e-parent` elements only (`STOP:7430-7457`).

### 4.5 Does anything throw when the classic markup is missing?

Nothing was found that throws at load:

- `wpresidence_map_general_start_map()` returns when neither `#googleMap` nor `#googleMapSlider` exists
  (`ref/site-js/theme-maps_base.js:271-277`); the listing map code is guarded by the same ids
  (`ref/site-js/theme-google_map_code_listing.min.js`).
- `ref/site-js/theme-property.min.js` binds by id and class (`#print_page`, `.front_plan_row`, `.venobox`,
  `a.nested-link`) and tests for its canvases before drawing.
- `estate_start_lightbox()` (inline, `DEV:6573-6579`) works on `#owl-demo`, which the theme footer prints on every
  layout.
- Block's inline scripts all return early (4.3).

The one runtime error found is the click on a hidden theme section link (4.2). `ajaxcalls.min.js`,
`mapfunctions.min.js` and `wpestate_login_register.min.js` were not read.

### 4.6 Cloudflare

- **E-mail obfuscation is on.** In the served HTML a `mailto:` link becomes
  `<a href="/cdn-cgi/l/email-protection#…">` and a visible address becomes
  `<span class="__cf_email__" data-cfemail="…">[email&#160;protected]</span>` (`DEV:4946`, inside `#bkd`; `STOP:4143`;
  footer `STOP:6996`). Cloudflare inserts `<script data-cfasync="false" src="/cdn-cgi/scripts/5c5dd728/cloudflare-static/email-decode.min.js">`
  before the next script tag after a protected address (twice in `DEV`: `:4968`, `:6296`; three times in `STOP`:
  `:4303`, `:5622`, `:7114`), and that
  script restores the address in the browser. It applies to anything a snippet prints. Without JavaScript the link
  leads to Cloudflare's own page and the text reads "[email protected]". A script that looks for `a[href^="mailto:"]`
  before the decoder has run finds nothing. Addresses inside `<script>` (JSON-LD) are not touched.
- Rocket Loader is not on (script tags are served unchanged). The development snippet nevertheless marks its scripts
  `data-cfasync="false" data-no-optimize="1" data-no-defer="1"` (`ref/dev-v1.9.1.php:10367-10368`).

## 5. Recommendations

Everything in 5.1 to 5.4 was run in the harness inside the real shell (`D/verify-reset-and-stacking.txt`,
`D/verify-shell-overrides.txt`); the files are `D/harness/reset.css` and `D/harness/shell-overrides.css`.

### 5.1 A minimal reset for `#bkr` (2.1 KB)

```css
#bkr{--bkr-font:"Montserrat",system-ui,-apple-system,"Segoe UI",Roboto,sans-serif;--bkr-ink:#22303C;--bkr-navy:#0D2440;--bkr-top:56px;
position:relative;display:block;float:none;clear:both;width:100%;max-width:none;margin:0;padding:0;background:#FBF9F6;color:var(--bkr-ink);font:400 16px/1.6 var(--bkr-font);letter-spacing:0;text-align:left;text-transform:none;-webkit-font-smoothing:antialiased;overflow-wrap:break-word}
@media (min-width:1200px){#bkr{--bkr-top:0px}}
#bkr :where(*,*::before,*::after){box-sizing:border-box}
#bkr [hidden]{display:none!important}
#bkr :where(h1,h2,h3,h4,h5,h6,p,ul,ol,li,dl,dt,dd,figure,blockquote,table,form,fieldset,legend,details,summary,address,hr){margin:0;padding:0;border:0}
#bkr :where(h1,h2,h3,h4,h5,h6,p,li,dt,dd,td,th,caption,blockquote,cite,label,legend,small,address,figcaption){color:inherit;font-size:inherit;font-weight:inherit;font-style:inherit;line-height:inherit;text-transform:inherit;float:none;width:auto}
#bkr :where(ul,ol){list-style:none}
#bkr :where(hr){opacity:1}
#bkr :where(table){border-collapse:collapse;border-radius:0;caption-side:top}
#bkr :where(th,td){padding:0;border:0;text-align:left;vertical-align:top}
#bkr :where(img,svg,video,iframe){display:block;max-width:100%}
#bkr :where(img){height:auto}
#bkr :where(a){color:inherit;text-decoration:none;transition:none}
#bkr a:hover,#bkr a:focus{color:inherit!important}
#bkr :where(button,input,select,textarea){font:inherit;color:inherit;letter-spacing:inherit;text-transform:none;margin:0}
#bkr :where(label){display:inline;margin:0}
#bkr :where(input[type=checkbox],input[type=radio]){-webkit-appearance:auto;appearance:auto;width:auto;height:auto;margin:0;padding:0;border:0;background:none;position:static;vertical-align:baseline}
#bkr input[type=checkbox]::before{content:none}
#bkr :focus-visible{outline:2px solid var(--bkr-navy)!important;outline-offset:3px}
#bkr ::selection{background:var(--bkr-navy);color:#fff}
#bkr h1,#bkr h2,#bkr h3,#bkr h4,#bkr h5,#bkr h6{font-family:var(--bkr-font)!important;font-weight:600!important;letter-spacing:0!important;color:var(--bkr-navy)!important;text-transform:none!important}
```

The colours, the font stack, the 16px / 1.6 and the heading weight are placeholders for the design; the structure is
the point. It is the development layout's reset plus the lines that close the gaps listed at the end of section 3:

| Line | Why (see) |
|---|---|
| root `font:400 16px/1.6 …`, `letter-spacing`, `text-transform`, `text-align`, own background | cuts the 14px / 23.1px / left / `#1a1a1a` inheritance (2.1) |
| the second `:where(…)` list (`color`, `font-size`, `font-weight`, `font-style`, `line-height`, `text-transform` inherit; `float:none; width:auto`) | the theme sets size, weight and line height on every `p`, colour on `p`, `li`, `td`, size and weight on `label`, floats `blockquote` and `legend`, italics on `blockquote` and `address`, capitals on `blockquote cite` (2.2). With it, text takes the colour and size of its container: measured, a `p` and an `li` in a navy section are white, a bare `p` is 16px / 25.6px |
| `:where(hr){opacity:1}` | the theme's `opacity:.25` (2.2) |
| `:where(table)…`, `:where(th,td)…` | the theme's borders, radius, padding, `caption-side:bottom` (2.2) |
| `:where(img){height:auto}` with `max-width:100%` | nothing on the site does it for a plain `img` (2.2) |
| `a:hover, a:focus{color:inherit!important}` | `a:hover{color:#555 !important}` and the theme's focus colour (2.2). A link class that wants its own hover colour then needs `!important` too |
| checkbox and radio lines | the theme's replacement checkbox (2.2); measured back to a native 13px box |
| `:focus-visible{outline:…!important}` | `:focus{outline:0}` and the checkbox's `outline:0!important` (2.3) |
| `::selection` | white-on-default selection (2.2) |
| headings with `!important` | the five forced properties (2.3). A heading on a dark band needs `#bkr .x h2{color:#fff!important}`; a display face needs `#bkr .x{font-family:…!important}` |

After this reset the listing's description needs prose rules of its own, scoped by a class on its wrapper: bullets
and indentation for `ul` / `ol`, margins between blocks, link underlines, cell padding and borders for tables (the
reset's `:where(th,td)` outranks `WPBL`'s `.wp-block-table td`), `hr`, `blockquote`.

For components, follow the development layout: put `!important` on exactly the properties of 2.3 and on nothing
else. Measured examples from the harness: a submit button `#bkr .bkr-btn{padding:0 20px!important;border:1px solid …!important;
border-radius:4px!important;background:…!important;color:#fff!important;font:600 14px/1.2 var(--bkr-font)!important;
letter-spacing:.02em!important;text-transform:none!important;box-shadow:none!important}` renders at 14px, not
uppercase, with its radius; a field `#bkr .bkr-inp{border:1px solid …!important;border-radius:4px!important}` with
`#bkr .bkr-inp:focus{border-color:…!important;box-shadow:0 0 0 3px …!important}` keeps its border, radius and focus
ring.

Do not put `overflow:hidden` or `overflow:auto` on `#bkr` or on an ancestor of a sticky element: that element would
stick to the box instead of the viewport. `overflow-x:clip` is safe if a guard against sideways scroll is wanted.
With simple content the shell itself causes no sideways scroll from 320px to 1920px (measured at 13 widths:
`scrollWidth` equals `clientWidth`).

### 5.2 Rules for the shell around `#bkr` (elements outside the root)

```css
html body.bkr-page .mobile_agent_area_wrapper,html body.bkr-page .blockke-cta-bar,html body.bkr-page .wpestate_top_property_navigation{display:none!important}
html body.bkr-page.single-estate_property{padding-bottom:0}
@media (max-width:991px){html body.bkr-page.single-estate_property{padding-bottom:calc(64px + env(safe-area-inset-bottom,0px))}html body.bkr-page #zsiq_float{bottom:calc(84px + env(safe-area-inset-bottom,0px))!important}}
```

- `.mobile_agent_area_wrapper`: the site forces it on with `body.single-estate_property .mobile_agent_area_wrapper{display:flex !important}`
  (0,2,1); `html body.bkr-page …` is (0,2,2). Only if the rental page has its own bar on phones; otherwise leave the
  theme's bar and have no bar of your own (the development layout's choice).
- `.blockke-cta-bar`: otherwise it appears between 992px and 1024px on top of everything (1.9). Same specificity
  argument. v1.9.1 does the same for its classic rental page:
  `html body.bkd-rent-cls .mobile_agent_area_wrapper,html body.bkd-rent-cls .blockke-cta-bar{display:none!important}`
  (`ref/dev-v1.9.1.php:2050`).
- `.wpestate_top_property_navigation`: takes its ten dead links out of the tab order and removes the faint shadow
  at the top of the screen from 1201px (1.9, 4.2).
- `padding-bottom`: replaces the site's 76px and the script-driven 96px (`body.single-estate_property.blockke-bar-open`
  is (0,2,1); `html body.bkr-page.single-estate_property` is (0,2,2)) with the bar's real height, so there is no
  white strip under the navy footer. 64px is the example bar; use the real figure.
- `#zsiq_float`: the development layout's own offset, applied without a script. The float's real size is unknown
  (section 7).

Measured with Block's inline scripts running, at 390, 768, 1010 and 1440px: theme bar, `.blockke-cta-bar` and the
theme's section bar all `display:none`; body padding 64px up to 991px and 0 above, although `<body>` still gets
`blockke-bar-open`; own bar at the bottom up to 991px; sticky bar at y=56 below 1200px and y=0 from 1200px.

### 5.3 z-index values and bottom offsets

Inside `#bkr` a z-index only orders against the site header and other things inside `#all_wrapper` (1.11):

| Layer in `#bkr` | z-index | Reason |
|---|---|---|
| sticky section bar | **40** (anything from 12 to 99) | below the mobile header (100), the desktop header (9999) and its mega panels, above `.header_media` (11); the development layout's value |
| bottom bar on phones | **90** (anything from 12 to 99; the development layout's 990 also works) | nothing else in `#all_wrapper` is at the bottom; every float outside paints over it regardless |
| small in-page pop-overs | 50 to 95 | same context |
| full-screen viewer, enquiry dialog | **no z-index: a `<dialog>` opened with `showModal()`** | the only way from inside `#bkr` to cover `a.backtop`, `a.contact-box`, the chat floats and the cookie banner (measured). With script off there is no dialog to open. Lock scrolling with a class on `<html>` (`html.bkr-lock{overflow:hidden}`; the site's own locks are on `<body>`: `body.blkac-lock`, `body.blkbp-lock`) |

Bottom of the screen at 991px and below, measured from the bottom edge, once the rules of 5.2 are in:

| Height above the bottom | Occupant |
|---|---|
| 0 to 64px + safe area | the rental page's bar (keep it at or under 76px, the offset the chat launcher already uses) |
| 76px + safe area | `#blkac-launch`, about 38px high, right 16px (only when the chat gate is live or for testers) |
| 84px + safe area | `#zsiq_float` (with the rule in 5.2; real size unknown) |
| 90px to 140px | `a.backtop`, 50 x 50, right 15px, after 90px of scroll |
| unknown | the cookie banner `#zcb-banner`, until it is answered; it will sit over the bar on a first visit |

`a.backtop` and the lifted Zoho float may overlap each other at the right edge; that is the case on the development
layout today whenever its bar is on. If the design wants the corner clear, add
`html body.bkr-page a.backtop{display:none!important}` below 992px.

From 992px to 1199px there is no bar at the bottom (the site's own breakpoint for it is 992px): `a.backtop` and
`a.contact-box` sit at `bottom:10px` and would overlap the right end of a bar shown there. Keep the page's bottom
bar to 991px and below, the same breakpoint the site uses for the body padding, the chat launcher and the back-to-top
button.

### 5.4 Top offset under the site header

- **56px below 1200px, 0 from 1200px.** As a variable on the root, as in 5.1 (`--bkr-top`), so it works without
  script. Sticky bar: `position:sticky; top:var(--bkr-top)`.
- Section anchors: `scroll-margin-top:calc(var(--bkr-top) + <height of the sticky bar> + 12px)`. For focus not to be
  hidden under a bar, also `scroll-padding-top` and `scroll-padding-bottom` on `html` (no site rule sets either).
- The desktop header is 130px tall at the top of the page and gone after 130px of scroll; a sticky bar with `top:0`
  simply takes over. Nothing needs to know the 130.
- For logged-in editors add the admin bar. The simplest robust way is the development layout's probe
  (`ref/dev-v1.9.1.php:9836-9851`): let script overwrite `--bkr-top` with the bottom edge of whatever fixed or sticky
  element outside `#bkr` touches the top of the viewport. CSS stays the fallback.

### 5.5 Container width

The site header has no fixed width: it is fluid with side margins of 16px (1200px to 1320px), 24px (1321px to
1680px) and 48px (wider), and on phones it runs edge to edge (2.7). Two honest options:

- **Line up with the header**: a fluid wrap with `padding-inline` 16px / 24px / 48px at those breakpoints, text
  columns narrower inside it. At 1440px that is x 24 to 1416, exactly the logo's left edge and the user icon's right
  edge.
- **Match the development layout**: `max-width:1200px` plus gutters of 20px (40px from 768px), centred
  (`ref/dev-v1.9.1.php:9237-9239`, `:9251`). It does not line up with the header (120px in at 1440px); nothing on
  the site does. The classic page's box is 1540px with 15px padding; the footer uses 64px padding.

A fixed-width container cannot line up with a fluid header at more than one viewport width.

### 5.6 Fonts the site already loads

| Family | Weights and styles | How | Where |
|---|---|---|---|
| **Montserrat** | 300, 400, 500, 600, 700; upright only | `@import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap')`, first statement of `CUST` | `STOP:959`; every page |
| Frank Ruhl Libre (a serif) | 300, 400, 500, 600, 700, 800, 900 | Redux: `<link rel="preload" as="style">`, then `media="print" onload="this.media='all'"`, plus a `<noscript>` copy; `display=swap` | `STOP:119`; every page. The theme options name it for `h1` and `h2` (`--wp-estate-h1-font-family-option: Frank Ruhl Libre`, `STOP:764`), but `CUST` forces Montserrat on headings, so today nothing uses it |
| Roboto | 400 (Redux link); `wght@300,400,500,700` (theme link, malformed, result unknown); 100 to 900 with italics (Elementor, in the footer) | three links | `STOP:115`, `:119`, `:7619` |
| Roboto Slab | 100 to 900 | Elementor, in the footer | `STOP:7620` |
| Font Awesome 6.4.2 | "Font Awesome 6 Free" 400 and 900, "Font Awesome 6 Brands" 400, with "Font Awesome 5 …" and "FontAwesome" aliases | theme stylesheet | `STOP:116` |
| Instrument Serif | 400, upright and italic | **only** where the development snippet prints `link#bkd-font`: its own layout (`DEV:895`) and classic pages that get its extras (`STOP:892`) | not there if that snippet is absent or stands down |
| named in CSS but not loaded | Inter, Space Grotesk (`TOC`), "GT Walsheim" (`STYLE:1`) | | they fall back |

- Body text is Montserrat whatever the layout asks for (2.3), and that is the brand face: use it and load nothing.
  There is no Montserrat italic: italic text is synthesised.
- A display face needs `#bkr <selector>{font-family:…!important}` to get past the site's rules.
- If a display serif is wanted without a new request, Frank Ruhl Libre's stylesheet is already on every page; it
  arrives late (non-blocking, swapped in) and, with script off, through the `<noscript>` link. If the design wants
  Instrument Serif to match the development layout, the rental snippet has to print the link itself.
- Icons: Font Awesome is available as classes (`fas fa-…`, `fab fa-whatsapp`), but the development layout uses an
  inline SVG sprite instead, which needs no font file.

### 5.7 Script and markup rules that follow from section 4

1. In-page links: handle the click in script (no hash navigation); never depend on `:target` or on
   `location.hash` (4.2).
2. Every WhatsApp link printed with `data-blkct="1"`, or its text is replaced after load (4.3). Do not report
   call, WhatsApp or e-mail taps yourself: snippets 40769 and 33892 already do, for every `a[href]` on the page.
3. No call, WhatsApp or e-mail link inside a `<header>` or `<footer>` element (4.3).
4. Print the page's JSON-LD with `id="bke-schema-ld"`, or the site adds its own `RealEstateListing` that calls the
   listing a `Residence` (4.3). `wp_print_inline_script_tag( $json, array( 'type' => 'application/ld+json',
   'id' => 'bke-schema-ld' ) )` does both.
5. Dialogs and the photo viewer as `<dialog>` with `showModal()` (5.3).
6. No form whose `action` contains `advanced-search` (the completion script would add a field to it), no element
   with `data-listid`, no `data-bs-*` attributes (4.2, 2.5).
7. When scrolling by script, pass `behavior` explicitly: the page's default is smooth (4.2).
8. E-mail addresses in the HTML are rewritten by Cloudflare (4.6): do not parse them back out of the DOM, and expect
   "[email protected]" without script.
9. One `h1` only: two site scripts read "the first `h1`" for a WhatsApp message and for structured data (4.3).
10. The first photo sits about 368 KB into the HTML (1.1). The `<img>` with `fetchpriority="high"` is enough for
    correctness; a `<link rel="preload" as="image" imagesrcset=… fetchpriority="high">` printed early in `wp_head`
    would let the browser start it sooner. Check the printed tags for duplicated attributes (4.4).

## 6. For the test-rig builder: where the shell ends and begins again

File: `ref/pages-stop/45577.html` (UTF-8, mixed LF and CRLF line ends: 1,838 lines end in CRLF, spread through the
head, the shell and the content; do not normalise them if byte offsets matter). Every marker below occurs exactly once in the file (checked).

**The site header ends** after the empty `.header_media` element:

```
line 4101, byte 367,870:  <div class="header_media d-flex  w-100 mobile_header_media_sticky_yes header_mediatype_0 with_search_10 header_media_non_elementor">
line 4102:                    </div>
```

(two spaces between `d-flex` and `w-100`). The exact string to search for is `header_media_non_elementor">` (byte
367,974); the shell's top part ends with the first `</div>` after it. What follows on lines 4103 to 4107 is
whitespace only (bytes 368,013 to 368,025: `\n\n\n \n\n      \n`), and the page's own content starts on line 4108, byte 368,026:

```
<div class="wpresidence-content-container-wrapper col-12 d-flex flex-wrap lay1">
```

**The footer begins** at this string on line 5764, byte 507,377:

```
</div> <!-- end class container -->
```

That `</div>` closes `div.container-fluid…wpresidence_main_wrapper_class`. On the classic page it is preceded on the
same line by `<div class="row wpestate_property_disclaimer"></div>`, which belongs to the classic template, not to
the shell (it is absent from `DEV`); the classic content therefore ends with that row, and the last line that is
purely content is 5762 (`<!-- end sidebar --></div>`).

So, for a rig that wants "shell + my content":

```python
s = open('ref/pages-stop/45577.html', encoding='utf-8', newline='').read()
a = s.index('header_media_non_elementor">')
a = s.index('</div>', a) + len('</div>')            # end of the header part
b = s.index('</div> <!-- end class container -->')    # start of the footer part
top, bottom = s[:a], s[b:]
page = top + '\n\n\n \n\n      ' + MY_ROOT_HTML + bottom   # exactly how the development layout sits in DEV:4635-4946
```

`bottom` then holds, in order: the close of the main wrapper, `div.wpestate_elementor_footer_custom` (scripts only),
`</div> <!-- end website wrapper -->` (line 6631; the comment is at byte 555,429), the whole `wp_footer` output and
`</body></html>`.
`D/harness/lib.js` (`withContent()`) does exactly this cut and also adds a body class; it is what every measurement
in this note used.

Things the rig should reproduce or knowingly leave out:

- The body class: add `bkr-page` to the class list on line 2870 (after `elementor-kit-24`). Keep
  `single-estate_property`: most of the bleed and three scripts key on it.
- Stylesheets: `ref/site-css/` has every block.ke stylesheet the page links, and `ref/site-css/MANIFEST.tsv` maps
  URL to file. Rewrite the `<link>` hrefs or serve them by path. Without `theme-main.css` and the inline `CUST` /
  `TOC` blocks (which are in the HTML already) the bleed is not there to test against. The Elementor and mega-menu
  styles are linked from the `wp_footer` part (lines 7605-7620).
- Scripts: only four theme scripts are local (`ref/site-js/`). jQuery, Bootstrap, Slideout and the rest are not; a
  rig that runs the saved page as is will log `jQuery is not defined` from the inline theme calls. The inline Block
  scripts that matter for the rental page (`bke-listing-schema`, `blockke-booking-ux`, `blkct-js`,
  `bke-desc-collapse`, `bke-mobile-cta`) are plain DOM code and run without jQuery (4.3). To reproduce the theme's
  hash handler without jQuery, add
  `window.addEventListener('hashchange',function(e){history.pushState('',document.title,location.pathname+location.search);});`
  and, for the back-to-top button, toggle `.islive` on `a.backtop` and `a.contact-box` at 90px of scroll.
- Listing-specific strings inside the shell that a rig serving other listings should swap or ignore: the Rank Math
  head and the theme's `og:` block (lines 10-33, 419-763), `data-postid="45577"` (2870), the theme lightbox with this
  listing's photos and the mobile agent bar with this listing's agent (7270-7421), `googlecode_property_vars` (7683),
  `window.BLKAC_CFG` (7800), `window.BLOCKCT` (7866).
- Third parties the saved HTML would call if loaded online: Google Tag Manager, gtag, Meta pixel, Zoho PageSense and
  SalesIQ, Google Maps, Google Fonts, Google sign-in. Block them in the rig (the harness aborts everything that is
  not local).

## 7. Not established, and how to find out

| # | Unknown | How to find out |
|---|---|---|
| 1 | Size, position, z-index and phone behaviour of the Zoho SalesIQ float `#zsiq_float`, and whether Zoho positions it inline or with `!important` | Open any listing on the live site in a browser, inspect `#zsiq_float` at 390px and 1440px. Its CSS comes from Zoho at run time; nothing local has it |
| 2 | Size, position and z-index of the cookie banner `#zcb-banner` and how long it stays | A private window on the live site; inspect before answering it |
| 3 | The admin bar with the sticky bar for a logged-in editor (`?bkr=1` preview): the saved pages are anonymous | Preview while logged in, at 390px, 700px and 1440px; or rely on the script probe (5.4) |
| 4 | Back-button behaviour after a hash navigation in Safari and Firefox, and with a real Back press in Chrome (4.2 used `history.back()` in headless Chrome) | Click a section link on a live development-layout page (they are plain anchors), then press Back. The recommendation does not depend on the answer |
| 5 | Whether Elementor 4.4.0's `frontend.min.js`, the Bootstrap bundle, `ajaxcalls.min.js`, `mapfunctions.min.js` or `wpestate_login_register.min.js` attach any handler that could touch a foreign root | Not downloaded (outside this task's permission). Read them, or watch the console and the Event Listeners panel on a live development-layout page |
| 6 | What `fonts.googleapis.com/css?family=Roboto:wght@300,400,500,700` (`STOP:115`) returns | Open the URL |
| 7 | Why `wp_get_attachment_image()` output carries `loading` / `fetchpriority` twice on this site (4.4) | Call it in the rig and on the live site with and without those arguments and compare; if it persists, build `<img>` from `wp_get_attachment_image_src()` and `wp_get_attachment_image_srcset()` |
| 8 | Pinch-zoom on iPhone over the page content: `#all_wrapper{touch-action:pan-y}` (`MAIN:4`) should disable it for everything inside the wrapper, a `<dialog>` included, and the viewport meta disables it on Android. Horizontal swipe scrollers inside `#bkr` do work (measured, `D/touch-action-test.txt`) | Try a pinch on a photo on a live development-layout page on an iPhone. If the viewer must zoom, it needs its own zoom control |
| 9 | Real height of the theme's mobile agent bar with web fonts and script (62px in the harness; CSS gives 44px buttons + 16px padding + 1px border) | Measure on a phone; only matters if the theme's bar is kept |
| 10 | Whether the owner wants the empty 40px navy top bar at 1200px and wider, the `.blockke-cta-bar` at 992px to 1024px on development pages, or the invisible focusable theme links | Shell matters outside this project; listed so they are not rediscovered |

## Appendix: files this note left behind

| Path | What |
|---|---|
| `ref/site-css/` | 21 stylesheets from block.ke, as served today, and `MANIFEST.tsv` (file, HTTP status, bytes, hash, Last-Modified, URL) |
| `ref/site-js/` | `theme-control.js`, `theme-property.min.js`, `theme-google_map_code_listing.min.js`, `theme-maps_base.js`, and `MANIFEST.tsv` |
| `notes/04-site-shell-data/` | the evidence: `README.md` lists every file. Start with `matched-rules-1440.md` (which rule hits which tag), `computed-in-shell.md` (the net effect), `geometry.txt` (the shell at seven widths), `verify-reset-and-stacking.txt` and `verify-shell-overrides.txt` (section 5 run in the real shell) |
| `notes/04-site-shell-data/harness/` | the offline harness (`lib.js` and one script per report), the test fragment, the prototype page, `reset.css`, `shell-overrides.css` |
