# 01 · The development snippet: integration map for the rental page

Written for the engineers who will build the standalone rental snippet (`blockke_rp_*`, `#bkr`).
Everything below was read from local files: the two copies of Block's development snippet, the saved pages and the theme options dump. Nothing was fetched from the live site.

| | |
|---|---|
| `ROOT` | `/tmp/claude-0/-workspace-Loyd2024/e24ac003-f571-5336-9956-1fd11a814c7d/scratchpad/rp` |
| v1.9.1 | `ROOT/ref/dev-v1.9.1.php`, 17,740 lines, in the repo, **not live**. Line numbers in this file are v1.9.1's unless written `v1.8.2:N`. |
| v1.8.2 | `ROOT/ref/dev-v1.8.2.php`, 15,494 lines, **live today** as WPCode snippet 42706. |
| Live evidence | `ROOT/ref/pages-final/45577.html` (a rental still on the v1.8.2 development layout when fetched on 11 Nov) and `ROOT/ref/pages-stop/45577.html` (the same listing on the classic page). |
| Popup snippet | `ROOT/ref/live-snippets/40752.php` (price-check popup: Zoho sender, channel tagging, attribution keeper). |

**How the two versions differ (exact, by PHP tokenizer).** v1.8.2 defines 353 functions. In v1.9.1, 312 of them are byte-identical, 41 are changed, 69 are new, none is removed. `blockke_dev_css()` (56,738 bytes) and `blockke_dev_js()` (33,301 bytes) are byte-identical in both. Every section below says where the versions differ; where it says nothing, they are the same.

**How section (b) was checked.** Besides reading, I ran both snippets in a small harness: WordPress 7.1.4's real hook classes (`class-wp-hook.php`, `plugin.php`, copied from the rig's core checkout), a stubbed listing, and a stand-in for the rental snippet. It is a simulation, not a WordPress install, so the test team should repeat the same cases on the rig. Harness: `/tmp/claude-0/-workspace-Loyd2024/e24ac003-f571-5336-9956-1fd11a814c7d/scratchpad/devmap-work/harness/run.php` (the recipe it runs is `standdown.php` beside it, the same text as in section (b)).

## Contents

- [The short version](#the-short-version)
- [(a) How the layout takes over a listing's page](#a-how-the-layout-takes-over-a-listings-page)
- [(b) `$GLOBALS['blockke_dev_active']` and making the snippet stand down](#b-globalsblockke_dev_active-and-making-the-snippet-stand-down)
- [(c) The listing data it reads](#c-the-listing-data-it-reads)
- [(d) How the description is parsed](#d-how-the-description-is-parsed)
- [(e) Images, the gallery and its viewer](#e-images-the-gallery-and-its-viewer)
- [(f) Enquiries end to end](#f-enquiries-end-to-end)
- [(g) Similar homes](#g-similar-homes)
- [(h) CSS](#h-css)
- [(i) Admin](#i-admin)
- [(j) Anything else a second layout snippet must know](#j-anything-else-a-second-layout-snippet-must-know)
- [Recommendations for the rental snippet](#recommendations-for-the-rental-snippet)
- [Unknown, and how to find out](#unknown-and-how-to-find-out)

## The short version

1. The layout renders **inside** a `template_include` filter at priority **99999** (10240–10389; `v1.8.2:8484–8628`): `get_header()`, its own markup, `get_footer()`, then it returns the blank file `wp-content/index.php`. The rental page can do exactly the same.
2. Whether it renders is decided by `blockke_dev_should_render()` (10205–10219), which reads `$_GET['bke_layout']` (editors) and the custom field `bke_dev_layout`. **It never reads the global.** So setting `$GLOBALS['blockke_dev_active']` does not stop the layout: with `bke_dev_layout = on` (or config `auto`) a second whole page is printed after the rental one (harness: two `get_header()` calls).
3. The global only switches the snippet's **classic-page** pieces off. But it also switches two `wp_head` hooks **on** (they are meant for `#bkd` pages): priority 1 prints the view-transition head with `<link rel="expect" href="#bkd-hero-media" blocking="render">`, priority 100 prints `<style id="bkn-dev-css">`. `view_transitions` is **on** on the live site (`pages-final/45577.html:9–11, 1490`), so a rental page that only sets the global would hold its first paint for an element that never arrives.
4. The recipe that makes both versions print nothing at all on a rental page is five lines plus one optional step, run on the `wp` action: the global, the flag `$GLOBALS['blockke_bkn_vt_printed']`, a `get_post_metadata` filter that answers `off` for `bke_dev_layout`, removing `$_GET['bke_layout']`, and `$_GET['blockpop'] = '0'`. Exact code and test results are in section (b).
5. In v1.9.1 with the option already on the site (`rent_pages` true, `classic_inplace` `['rent']`), a rental page that does **not** stand the snippet down also gets v1.9.1's own rent box, its fixed phone bar and "Around this property", all visible. The stand-down must run before `template_redirect` priority 1000.
6. The site stores sizes in **square feet** (`wp_estate_measure_sys` = `ft` in `ROOT/ref/theme-options.json`); the snippet multiplies by 0.092903 and rounds to whole square metres (753–759, 2643–2650, 2889–2897).
7. Leads are rows of the option `blockke_dev_leads` (newest first, capped at 200, not autoloaded), saved **before** Zoho and email (10419–10435, 10808–10829). A row is a rental for the inbox only if it carries `'kind' => 'rent'`; v1.8.2 never writes that key.
8. The rental rules (`blockke_dev_rent_phrase()`, `blockke_dev_rent_info()`, `blockke_dev_rent_name()`, the "From" logic) exist **only in v1.9.1**, so they must be ported, not called.
9. Functions that are byte-identical in both versions and safe to call behind `function_exists()`: `blockke_dev_config()`, `blockke_dev_agent()`, `blockke_dev_lead_to()`, `blockke_dev_save_lead()`, `blockke_dev_count_view()`, `blockke_dev_main_area()`, `blockke_dev_search_url()`, `blockke_dev_nearby()`, `blockke_bkn_client_ip()`. Check for them at run time (inside a hook), not when the file loads: the snippets' load order is not fixed.
10. Names already taken that the rental snippet must not reuse: admin-bar node id `bkr` (v1.9.1, 11309), sessionStorage keys `bkd_lead`, `bkd_from`, `bkn_ctx`, `bkn_back`, `blkbp_attr`, query parameters `bkd_sent`, `bkd_err`, `bke_*`, `lp`, `blockpop`.

## (a) How the layout takes over a listing's page

### The decision

| Function | Lines | `v1.8.2` | What it does |
|---|---|---|---|
| `blockke_dev_layout_on( $id )` | 10173–10186 | 8417–8430 | Config `enabled`, then the custom field `bke_dev_layout`: `off` → no, `on` → yes, anything else → `blockke_dev_layout_auto()`. |
| `blockke_dev_layout_auto( $id )` | 10189–10203 | 8433–8447 | Config `auto` and `property_has_subunits` = `'1'`; or config `auto_units` (true, or a list of area slugs) for a unit (`property_subunits_master`) that is for sale. |
| `blockke_dev_should_render()` | 10205–10219 | 8449–8463 | Front end only, `is_singular( 'estate_property' )`, not a feed or embed, none of `elementor-preview`, `vcv-editable`, `vcv-source-id`, `print` in `$_GET`. An editor's `?bke_layout=1` / `0` wins over everything; otherwise `blockke_dev_layout_on()`. |
| `blockke_dev_lp_target()` | 7912–7937 | 6765–6790 | Ad landing page: a page with a `bke_lp_listing` field, or a listing URL with `?lp=1`, when `blockke_dev_lp_ok()` allows it. Cached in a `static`. |

All four are byte-identical in the two versions.

```php
// 10205-10219
function blockke_dev_should_render() {
	if ( is_admin() || is_feed() || is_embed() || ! is_singular( 'estate_property' ) ) {
		return false;
	}
	foreach ( array( 'elementor-preview', 'vcv-editable', 'vcv-source-id', 'print' ) as $k ) {
		if ( isset( $_GET[ $k ] ) ) { return false; }
	}
	$id = get_queried_object_id();
	if ( isset( $_GET['bke_layout'] ) && is_scalar( $_GET['bke_layout'] ) && current_user_can( 'edit_post', $id ) ) {
		return '1' === (string) $_GET['bke_layout'];
	}
	return blockke_dev_layout_on( $id );
}
```

The preview parameter is `bke_layout` (`1` shows the layout, `0` the classic page) and needs `current_user_can( 'edit_post', $id )`. There is no secret-key preview in the development snippet; the rental snippet's `bkr_key` is new.

### The takeover

One anonymous function on `template_include`, priority **99999** (10240–10389; `v1.8.2:8484–8628`). In order:

| Step | Lines | Note |
|---|---|---|
| Landing page target, else `blockke_dev_should_render()`; otherwise return `$template` untouched | 10243–10246 | |
| `$data = blockke_dev_data( $id )`; null → return `$template` | 10247–10251 | The whole page model, section (c). |
| `$GLOBALS['blockke_dev_active'] = true` | 10252 | Section (b). |
| v1.9.1 only: page returned to by a no-script post → `blockke_dev_np_noindex()` | 10253–10255 | Not in v1.8.2. |
| `$_GET['blockpop'] = '0'` unless config `price_check_popup` | 10256–10258 | The price-check popup's own off switch; it is read in `blockke_bp_context()` (`ROOT/ref/live-snippets/40752.php:105`). |
| `body_class` filter adds `bkd-page` (and `bkd-lp` on a landing page) | 10259–10268 | |
| Landing page only: `wp_robots` and `rank_math/frontend/robots` filters, both priority 99, set noindex, follow | 10269–10289 | The normal layout page changes no robots, title, canonical or Open Graph tag. |
| `wp_head` priority 99: font links and `<style id="bkd-css">` | 10290–10301 | `blockke_dev_font_head( '#bkd' )` + `blockke_dev_css()` + the `--display` rule + `blockke_dev_calc_css()` (+ `blockke_dev_lp_css()`, + `blockke_dev_home_css()` when not land). |
| Land only: `wp_head` priority 100, `<style id="bkd-land-css">` | 10302–10310 | |
| `wp_footer` priority 20: `<script id="bkd-cfg">window.BKDEV={…}</script>` and `<script id="bkd-js">` | 10311–10371 | Both carry `data-cfasync="false" data-no-optimize="1" data-no-defer="1"`. v1.9.1 passes the script through `blockke_dev_rent_js()` for a rental (10362–10363). |
| `blockke_dev_count_view( $id )` (not on a landing page) | 10372–10374 | |
| `if ( have_posts() ) { the_post(); }` | 10375–10377 | Sets the global `$post` for the header and footer. The loop is never closed. |
| `get_header(); echo blockke_dev_render( $data ); get_footer();` (a landing page prints its own document, `blockke_dev_lp_document()`, 7972–7982) | 10378–10384 | |
| `return file_exists( WP_CONTENT_DIR . '/index.php' ) ? that file : ''` | 10385–10386 | The blank template. |

```php
// 10372-10388
if ( ! $lp ) {
	blockke_dev_count_view( $id );
}
if ( have_posts() ) {
	the_post();
}
if ( $lp ) {
	blockke_dev_lp_document( $data );
} else {
	get_header();
	echo blockke_dev_render( $data );
	get_footer();
}
$blank = WP_CONTENT_DIR . '/index.php';
return file_exists( $blank ) ? $blank : '';
},
99999
```

**Why the blank file works.** WordPress 7.1.4 (`ROOT/rig/wp/wp-includes/template-loader.php:114–132`) runs `realpath()` on what the filter returns and includes it only if it is a readable `.php` or `.html` file. `wp-content/index.php` is the "Silence is golden" file, so nothing more is printed. An empty string is not included either. Because the page is printed *during* the filter, it is out before `wp_before_include_template` fires, which is where core would start its optional template-enhancement output buffer (`ROOT/rig/wp/wp-includes/default-filters.php:437`). Nothing on the page relies on that buffer.

**Where the markup lands (live, v1.8.2).** `<div id="bkd">` sits inside the two wrappers the theme header opens and the theme footer closes: `div#all_wrapper.website-wrapper` and `div.container-fluid.px-0.wpresidence_main_wrapper_class` (`ROOT/ref/pages-final/45577.html:4640`, and the comment `<!-- end class container -->` right after `#bkd` ends). The body keeps the theme's classes (`single single-estate_property postid-45577 …`) plus `bkd-page` (`pages-final/45577.html:3403`).

**What the theme header still prints on such a page.** `get_header()` on a single listing prints the theme's property top navigation, `div.wpestate_top_property_navigation`, with links to `#accordion_property_address`, `#accordion_property_payment_calculator`, `#accordion_property_schedule_tour` and so on, plus its start-up script `wpestate_property_sticky()` and an empty `div.header_media` (`pages-final/45577.html:4627–4635`). None of those anchors exists on the layout. The development snippet does not hide this bar. Whether it is visible is in *Unknown*.

### Other classes and states it sets

- `body.bkd-page` (and `bkd-lp`). The area, index and search pages add `bkd-page` too (12401, 14712, 17346).
- `#bkd.bkd-js`: added by the page script; the reveal animation only hides `.bkd-rv` elements under this class, so content shows without script (9622–9624).
- `html.bkd-lock`: a dialog or the photo viewer is open (page scroll locked, 9600). `html.bkd-mb-on`: the mobile bar is showing (9601).

### View counting

`blockke_dev_count_view( $id )` (10221–10238; identical in v1.8.2:8465–8482). The theme counts views in its own property template, which the layout bypasses, so the layout counts them itself.

```php
// 10221-10238 (shortened)
if ( empty( $cfg['count_views'] ) || is_preview() || ( is_user_logged_in() && current_user_can( 'edit_post', $id ) ) ) { return; }
if ( ! empty( $cfg['prefetch'] ) && isset( $_SERVER['HTTP_SEC_PURPOSE'] ) && false !== stripos( (string) $_SERVER['HTTP_SEC_PURPOSE'], 'prefetch' ) ) { return; }
$total = (int) get_post_meta( $id, 'wpestate_total_views', true );
update_post_meta( $id, 'wpestate_total_views', $total + 1 );
$daily = get_post_meta( $id, 'wpestate_detailed_views', true );   // array( 'November 11, 2026' => n, ... )
$day   = date_i18n( 'F j, Y' );
$daily[ $day ] = ( isset( $daily[ $day ] ) ? (int) $daily[ $day ] : 0 ) + 1;
update_post_meta( $id, 'wpestate_detailed_views', $daily );
```

Two meta writes per uncached page view. A prefetched page (cards on the list pages prefetch with `Sec-Purpose: prefetch` while config `prefetch` is on; it is on live: `pages-final/45577.html:5881`) is not counted on fetch; the layout's script later posts to `block/v1/view` (17672–17689), and that route refuses any listing whose `blockke_dev_layout_on()` is false, so it will not count a rental page.

## (b) `$GLOBALS['blockke_dev_active']` and making the snippet stand down

### Every read and write

The global is only ever set to `true`. Neither version unsets it or sets it to `false`.

| v1.9.1 | v1.8.2 | | Where | What it gates |
|---|---|---|---|---|
| 10252 | 8496 | write | Layout and landing-page takeover (`template_include` 99999) | — |
| 12392 | 10390 | write | Area and segment pages (`template_include` 99998) | — |
| 14704 | 12532 | write | New developments index (`template_include` 99998) | — |
| 17338 | 15092 | write | Search results page (`template_include` 99998) | — |
| 1559 | 1454 | read | `blockke_dev_classic_target()`; result kept in a `static` | The classic instalment calculator's target is 0 when the global is set. (It also needs an off-plan listing that is for sale.) |
| 1572 | 1467 | read | `wp_head` 99 | `<style id="bkd-calc-hide">`, which hides the theme's mortgage calculator. |
| 1583 | 1478 | read | `wp_footer` 25 | The instalment calculator block, its CSS and script. |
| 1658 | 1553 | read | `blockke_dev_classic_extras()`; result kept in a `static`, first called on `wp_head` 99 | Amenity tiles and "What's nearby" on classic pages: head CSS and font link (1758–1769), footer blocks, sprite and move script (1771–1822). |
| 1862 | — | read, on every call | `blockke_dev_classic_rent_target()` | v1.9.1's classic rental pieces: the `template_redirect` 1000 page buffer (2064–2081, through `blockke_dev_classic_rent_early()`), rent CSS, body class and `<template id="bke-nearby">` (`wp_head` 99, 2083–2102), rent box, phone bar and scripts (`wp_footer` 26, 2104–2160), Around this property (`wp_footer` 27, 2222–2242), the editor's admin-bar report (2450–2463), the buffer's own check (2341–2345). |
| 8510 | — | read | `blockke_dev_ld_head()` | v1.9.1 structured data. The global only picks the wording (the `$layout` argument of `blockke_dev_ld_graph()`); it does not switch the JSON-LD on or off. |
| 17714 | 15468 | read | `wp_head` priority **1** | Prints the view-transition head **only when the global is set**. |
| 17730 | 15484 | read | `wp_head` priority **100** | Prints `<style id="bkn-dev-css">` **only when the global is set**. |

(1850 is a comment.) `blockke_dev_should_render()`, `blockke_dev_layout_on()` and the `template_include` filter itself do not read it.

The two hooks that the global switches on:

```php
// 17711-17724 (v1.8.2:15465-15478), wp_head priority 1
if ( empty( $GLOBALS['blockke_dev_active'] ) || ! blockke_bkn_vt_on() || ! empty( $GLOBALS['blockke_bkn_vt_printed'] ) ) {
	return;
}
$GLOBALS['blockke_bkn_vt_printed'] = true; // once per page (the search page checks the same flag)
echo blockke_bkn_vt_head( ! is_singular( 'estate_property' ) );
if ( is_singular( 'estate_property' ) ) {
	echo '<link rel="expect" href="#bkd-hero-media" blocking="render">' . "\n";
}

// 17727-17739 (v1.8.2:15481-15493), wp_head priority 100
if ( empty( $GLOBALS['blockke_dev_active'] ) || ! is_singular( 'estate_property' ) || blockke_dev_lp_target() ) {
	return;
}
$vt = blockke_bkn_vt_on();
if ( $vt || blockke_bkn_ctx_on() ) {
	echo '<style id="bkn-dev-css">' . blockke_bkn_dev_css( $vt ) . "</style>\n";
}
```

`blockke_bkn_vt_on()` (17484–17486) is config `view_transitions` (or an editor's `?bke_vt=1`). `blockke_bkn_ctx_on()` (17492–17500) is true when any of `card_v16`, `dev_index`, `search_layout`, `view_transitions` is on, **or the visitor is logged in and can edit posts**. On the live site `view_transitions` is on: the saved development page carries `<style id="bkn-vt">` (line 9), `<link rel="expect" href="#bkd-hero-media" blocking="render">` (line 11) and `<style id="bkn-dev-css">` (line 1490) in `ROOT/ref/pages-final/45577.html`.

### What the snippet prints or does on a property page rendered by another snippet

Rows are the snippet's pieces; "set" means the other snippet set the global to `true`, "unset" means it left it alone. The harness was run for every row except the calculator, the redirects and the REST handlers, which are from reading the code; where it was run it agrees with the cell.

| Piece | v1.8.2, set before `wp_head` | v1.8.2, unset | v1.9.1 (`rent_pages`, `classic_inplace` `['rent']`), set | v1.9.1, unset |
|---|---|---|---|---|
| The development layout itself | Not affected by the global. `bke_dev_layout` = `on`, or config `auto` with sub-units: a **second complete page** after the rental one (its own `get_header()`, `#bkd`, `get_footer()`; views counted; `bkd_sim_{id}` written). `off`, or empty without auto: nothing. | Same. | Same, in rent mode. | Same. |
| Classic instalment calculator | Nothing. | Nothing on a rent-only listing (needs off-plan and for sale, 1563). | Nothing. | Nothing. |
| Amenity tiles and nearby places | Nothing. | Head: `<link id="bkd-font">` with two preconnects, `<style id="bkd-cls-css">`. Footer: icon sprite, `<div id="bkd-cls" hidden>` with `#bkd-feat` and `#bkd-near`, `<script id="bkd-cls-js">`. The script finds no `#accordion_features_details` / `#accordion_property_address` and removes the hidden wrapper. Nothing visible; about 5 KB of CSS, a font request and hidden duplicate amenity text. | Nothing. | As v1.8.2 for the tiles; "What's nearby" is replaced by Around this property (next row). |
| Classic rental box, phone bar, Around this property | Not in v1.8.2. | Not in v1.8.2. | Nothing printed. If the global was set **after** `template_redirect` 1000, the page buffer `blockke_dev_classic_place` is already running (it holds the page to the end and passes it through unchanged), and on a URL with `?bkd_sent=1` or `?bkd_err=…` `nocache_headers()` and noindex were already applied (2071–2073). Set on `wp`: none of that. | Body class `bkd-rent-cls`; `<style id="bkd-rent-css">` (hides `#accordion_property_payment_calculator` and `#accordion_property_schedule_tour`; below 992px hides `.mobile_agent_area_wrapper` and `.blockke-cta-bar` and pads the body, 2005, 2050–2051); `<template id="bke-nearby">`; the rent box in `#bkd-rent-wrap[hidden]` with a move script that **un-hides it when it finds no theme anchor** (2114–2123, the un-hide is 2121), so it shows under the site footer; the fixed phone bar `.bkd-rbar`; `bkd-rent-js`; `#bkd-near-wrap`, un-hidden the same way (2231–2234); the page buffer, which would also insert the blocks into any element of the page with the id `wpestate_property_description_section`, `accordion_features_details` or `accordion_property_address` (2348); `set_transient( 'bkd_area_pins' )` when stale. |
| Calculator swap | Nothing. | Nothing. | Nothing. | The theme calculator is hidden by the rent CSS above (no markup of the theme's is on our page anyway). |
| View-transition head (`wp_head` 1) | **Printed** while `view_transitions` is on (live: on): `<style id="bkn-vt">` with `@view-transition{navigation:auto}`, `<script id="bkn-vt-js">`, `<link rel="expect" href="#bkd-hero-media" blocking="render">`. | Nothing. | Same as v1.8.2 (identical code). | Nothing. |
| `<style id="bkn-dev-css">` (`wp_head` 100) | **Printed** (3,697 bytes of `#bkd …` rules with the transition on, 2,744 without). | Nothing. | Same. | Nothing. |
| Back-to-results and card-context script | Nothing. It is part of the layout's own page script (`blockke_bkn_dev_js()`, 17507–17573, applied in `blockke_dev_js()`), printed only by the layout's `wp_footer` hook (10311). | Nothing. | Nothing. | Nothing. |
| Structured data | Not in v1.8.2. | Not in v1.8.2. | Only if `listing_jsonld` covers `rent` (not in the option today): `script#bke-schema-ld` at `wp_head` 2 (8540–8550) and Rank Math's BreadcrumbList removed (8521–8538). **Not gated by the global or by `bke_dev_layout`**; with the global set the graph is worded as the layout's. | The same, worded as the classic page's. |
| Redirects | Not in v1.8.2. | Not in v1.8.2. | `template_redirect` 0 (11279–11291) acts only on a 404 or an unpublished listing named in config `redirect_ids`. Never on a published rental. | Same. |
| Landing-page guard | `?lp=1` on a rent-only listing is not a landing page: `blockke_dev_lp_ok()` wants a For Sale term, or no Buy/Rent term at all (`v1.8.2:640–646`). | Same. | Same result through `lp_rent_guard` (735–751). | Same. |
| `forms_post` handlers | Not in v1.8.2. | Not in v1.8.2. | REST only: `blockke_dev_lead_route()` (10904–10933) answers a form post carrying `bkd_np=1`. On pages, `?bkd_sent` / `?bkd_err` matter only to the pieces above. | Same. |
| Price-check popup switch, view count, body class `bkd-page` | Only inside the layout's own takeover. | Same. | Same. | Same. |
| Editor's admin bar | Node `bkd` "Development layout: on/off" with preview links (11191–11209); it reads `blockke_dev_layout_on()`. | Same. | Same; the `bkd-rent` report (2450–2463) is not added. | Same, plus the `bkd-rent` report. |

So neither choice is right on its own. Unset leaves the classic pieces (visible duplicates under v1.9.1). Set, alone, starts the view-transition head with a render-blocking `rel="expect"` on an element the rental page does not have, and in neither case is a listing whose `bke_dev_layout` is `on` kept from rendering twice.

### Recommended stand-down: both, on the `wp` action, plus two flags

```php
/**
 * Makes the development snippet (v1.8.2 or v1.9.1) stand down for the one listing this request renders.
 * Call once, on the `wp` action, only when the rental page is going to render this listing.
 */
function blockke_rp_dev_stand_down( $id, $quiet_css = true ) {
	$id = (int) $id;
	// 1. Classic-page pieces off (both versions), and v1.9.1's rent box, phone bar, Around this property and page buffer.
	$GLOBALS['blockke_dev_active'] = true;
	// 2. With that flag up, wp_head priority 1 would print the view-transition head and
	//    <link rel="expect" href="#bkd-hero-media" blocking="render">. This flag reads "already printed".
	$GLOBALS['blockke_bkn_vt_printed'] = true;
	// 3. The layout itself: bke_dev_layout answers 'off' for this listing, for this request only.
	add_filter(
		'get_post_metadata',
		function ( $value, $object_id, $meta_key ) use ( $id ) {
			return 'bke_dev_layout' === $meta_key && (int) $object_id === $id ? array( 'off' ) : $value;
		},
		10,
		3
	);
	// 4. An editor's ?bke_layout=1 outranks the custom field in blockke_dev_should_render().
	unset( $_GET['bke_layout'], $_REQUEST['bke_layout'] );
	// 5. The buyer price-check popup's own off switch.
	$_GET['blockpop'] = '0';
	// 6. Optional: wp_head priority 100 would still print <style id="bkn-dev-css"> (#bkd-only rules). Lower the flag for
	//    that one priority. The two inner callbacks are added while wp_head runs, so the first is the last of priority 99.
	if ( $quiet_css ) {
		add_action(
			'wp_head',
			function () {
				add_action(
					'wp_head',
					function () {
						$GLOBALS['blockke_dev_active'] = false;
					},
					99
				);
				add_action(
					'wp_head',
					function () {
						$GLOBALS['blockke_dev_active'] = true;
					},
					101
				);
			},
			98
		);
	}
}
```

Call it like this (the decision function is the rental snippet's own):

```php
add_action( 'wp', function () {
	$id = blockke_rp_target();            // 0 unless this request is a rental the page will render
	if ( $id ) {
		blockke_rp_dev_stand_down( $id );
	}
} );
```

**Why each step.**

| Step | Stops | Why this way |
|---|---|---|
| 1 Global | Every classic-page piece in both versions (table above). | It is the snippet's own switch. It must be up before `template_redirect` 1000 for v1.9.1 (2064–2081) and before `wp_head` 99, where two results are frozen in `static`s (1559, 1658). `wp` is before both. |
| 2 `blockke_bkn_vt_printed` | The view-transition head and `rel="expect"`. | The hook itself checks this flag (17714; `v1.8.2:15468`). No other code reads it on a listing page. |
| 3 Meta filter | The layout: `on`, config `auto`, config `auto_units`. | `blockke_dev_layout_on()` returns false on `off` before it looks at anything else (10178–10181). `get_post_meta()` passes through `get_post_metadata` in WordPress 7.1.4 (`ROOT/rig/wp/wp-includes/meta.php:664–671`); returning `array( 'off' )` gives `'off'` for a single read. |
| 4 Drop `bke_layout` | An editor's `?bke_layout=1`, which is tested before the custom field (10215–10217). | Nothing else reads the parameter. An editor who wants the development layout of a rental uses `?bkr=0&bke_layout=1`. |
| 5 `blockpop` | The buyer popup. | As the layout does (10256–10258). The popup already skips listings without a For Sale term (`ROOT/ref/live-snippets/40752.php:115–117`), so this is belt and braces. |
| 6 Toggle (optional) | `<style id="bkn-dev-css">`, 3.7 KB of rules that match nothing on the page. | The hook has no other switch. `WP_Hook` runs callbacks of one priority in the order they were added, so a callback added during `wp_head` 98 runs after every priority-99 callback registered earlier, whichever snippet loaded first. Leave it out if you prefer one dead `<style>` to a trick. |

**Harness results** (`bke_dev_layout` = `on` in the database unless said; `view_transitions` on, as live):

| Case | v1.8.2 | v1.9.1 |
|---|---|---|
| No stand-down, field `off` | 1 header; classic CSS, hidden amenity and nearby blocks | the same plus rent CSS, rent box, phone bar, Around this property, page buffer |
| No stand-down, field `on` | **2 headers**, `#bkd` printed after the rental body, views counted | the same |
| Global only (set in `template_include`), field `off` | 1 header; view-transition head with `rel="expect"`; `bkn-dev-css` | the same; buffer already started |
| Global only, field `on` | **2 headers** | **2 headers** |
| Global and meta filter, both added in `template_include` | 1 header; view-transition head with `rel="expect"`; `bkn-dev-css` | the same |
| Steps 1–5 on `wp` | 1 header; only `bkn-dev-css` (3,730 bytes with its tag) | the same |
| Steps 1–6 on `wp` | **nothing from the snippet**, no writes, no buffer | **nothing from the snippet**, no writes, no buffer |
| Steps 1–6, editor, `?bke_layout=1&lp=1`, rental snippet loaded before or after, its `template_include` at 99990 or 100000 (8 runs) | nothing | nothing |
| Steps 1 to 3 on `wp` but `bke_layout` left in `$_GET`; editor with `?bke_layout=1` | 2 headers | 2 headers |
| No stand-down, visitor, `?lp=1` | no landing page; as the first row | no landing page; as the first row |
| Steps 1–6, editor: the admin bar | `bkd` "Development layout: off", `bkd-on`, `bkd-off`, `bkd-edit` | the same; no `bkd-rent` nodes |
| Config `auto` on, listing has sub-units, field empty: without / with the recipe | 2 headers / nothing | 2 headers / nothing |
| v1.9.1 with `listing_jsonld` `['rent']`, steps 1–6 | — | `script#bke-schema-ld` printed once; everything else nothing |

**Risks and limits.**

- *The filter is request-wide for that one key and listing.* It also answers `update_post_meta()`'s "is the value unchanged?" read. Nothing writes `bke_dev_layout` on a front-end page request (the only writer is the edit screen's save handler, 11151–11189), and the function is only called on the front end.
- *Nothing cached is built from the filtered value.* The only readers through `get_post_meta()` are `blockke_dev_layout_on()` (called by `blockke_dev_should_render()`, the admin bar at 11199 and the REST view route at 17675) and the meta box (11108). The cards' cached rows read the field with their own SQL (`blockke_dev_pm_prime()`, 4019–4040; row key `lo`, 12776, 12805), so they keep the database value. If a rental's stored value is `on`, list cards still mark it `data-vt="1"`; the rental page has no `@view-transition` rule, so the browser just navigates.
- *The editor's admin bar* will read "Development layout: off" on a rental page (11199–11200), and its "Preview the development layout" link does nothing there because of step 4.
- *Timing.* If the recipe runs later than `wp` (for example inside `template_include`), v1.9.1 has already started its page buffer and, on `?bkd_sent` / `?bkd_err` URLs, sent no-cache headers and noindex. Keep it on `wp`.
- *v1.9.1 structured data is not covered.* If the owner later sets `listing_jsonld` to cover `rent`, v1.9.1 prints `script#bke-schema-ld` on the rental page whatever the recipe does. Section (j) says how to stay at one listing graph per page.
- *A future version* could read the global somewhere new. The test for that is one request on the rig: the page source must not contain `bkd` or `bkn-`.

**Alternatives looked at and not recommended.**

- *Global only, or meta filter only*: see the table; each leaves half of the problem.
- *`remove_all_filters( 'template_include', 99999 )`*: the snippet's callbacks are closures, so they cannot be removed one by one; this would also drop anyone else's callback at that priority, and it does nothing about the classic pieces.
- *Filtering the option `blockke_dev_page`* (for example `enabled` false): `blockke_dev_config()` keeps its result in a `static` after the first call (287–290), so it only works if nothing has read the config before; and it would change every helper the rental page might call.
- *Answering `land` for `bke_dev_kind`*: it does silence v1.9.1's classic rental pieces and JSON-LD (`blockke_dev_is_land()`, 698–722), but it lies to every helper about what the listing is.

## (c) The listing data it reads

Everything the page shows comes from one function, `blockke_dev_data( $id )` (7287–7745; `v1.8.2:6148–6601`), cached per request in a `static`. It returns `null` for a post that is not an `estate_property`. v1.9.1 differs from v1.8.2 in four places only: `$rent` and the chip `For rent` (7376–7379), no completion fact for a rental (7715), `plans` false for a rental (7735), and the new key `rent` (7736).

Its return keys, for orientation (v1.9.1): `id`, `slug`, `title`, `name` (the H1), `short` (name used in sentences), `url`, `chip`, `area`, `area_line`, `address`, `offplan`, `tagline`, `price_from`, `from_label`, `from_said`, `calc_price`, `completion`, `comp_row`, `ready_t`, `title_beds`, `beds_kv`, `units`, `subs`, `images` (attachment ids), `captions` (id => text), `hero_label`, `gallery_note`, `video`, `facts` (label/value pairs), `overview` (`heading`, `paras`, `highlights`), `desc` (HTML), `amenities` (groups), `amen` (`tiles`, `rest`, `essentials`), `amen_intro`, `plan`, `suits`, `investors`, `lat`, `lng`, `location` (`intro`, `places`, `note`), `faqs`, `ctx`, `agent`, `brochure`, `plans`, `rent`, `crumbs`, `crumb_here`, `progress`, `land` (7684–7742).

### Price, labels and the "From" logic

| What | Source | Lines |
|---|---|---|
| Price | meta `property_price`, cast to float. `price_from` is the lowest unit price when the listing has units with prices, else this field. | 7397–7398 |
| After-price label | meta `property_label` ("per month", "per sq ft", "per day", "Rent on application"). | 4069, 7216 |
| Before-price label | meta `property_label_before` ("From", "Up to"). | 4069, 7698 |
| `flag` | `blockke_dev_price_flag( $id )`: `'unit'` when either label says per sq ft or per sq m, `'upto'` when one says "up to", else `''`. | 4068–4077 (`v1.8.2:3239–3245`, without the `$period` argument) |
| `from_label` | the before-label starts with "from" (land has one more case). | 7698 |
| `from_said` | `blockke_dev_from_said()`: the title, excerpt or text words this very price (within 1%) as a starting price or a range ("from KES 44 million", "KES 168.5M – 194M", "onwards"). | 3820–3844, 7699 |

How the headline label is chosen (8568–8585, identical logic in v1.8.2):

```php
$p_lab = 'From';
$one   = /* a villa or house, not a development page, at most one unit row, no "from" said, one bedroom count in the title */;
if ( $d['price_from'] && ( ! $d['units'] || $one ) && /* price_from is the listing's own price */ ) {
	if ( 'upto' === $cx0['flag'] ) {
		$p_lab = 'Up to';
	} elseif ( 'unit' === $cx0['flag'] ) {
		$p_lab = 'Price';
		$p_txt = blockke_dev_money( $d['price_from'] ) . ' per sq ft';
	} elseif ( ( ! $d['offplan'] || $one ) && ! $d['from_label'] ) {
		$p_lab = 'Price';
	}
}
```

So: a listing with a unit table or sub-units says "From" its lowest unit price; a single ready listing says "Price" unless its own before-label says "From"; "Up to" comes only from the label. v1.9.1's rent mode turns this into `$r_pre` (`''`, `'From '`, `'Up to '`) in front of the rent (8587–8601); its classic rent box says "From" when the before-label says so or the units have more than one distinct price (`blockke_dev_classic_rent_say()`, 1884–1894). The rent wording itself (`blockke_dev_rent_phrase()`, 7212–7267, v1.9.1 only) is in section (f) and belongs to the rules notes.

Money helpers (identical in both): `blockke_dev_money()` "KES 135,000" or "On request" (483–486); `blockke_dev_short()` "KES 135K", "KES 9.2M" (488–498); `blockke_dev_parse_money()` text → number (501–516). The currency word is config `currency` (`KES`).

### Sizes and units

- **Confirmed: the site stores square feet.** `ROOT/ref/theme-options.json` has `"wp_estate_measure_sys": "ft"` (and `wp_estate_measure_sys_lot_size` `ac`). `blockke_dev_sqft_factor()` (753–759) returns 1 only when that option is `m`, else 0.092903.
- Square metres are derived as `round( property_size * 0.092903 )`, whole numbers, with " m²": sub-units (2643–2650), a single home's size fact (2889–2897), similar-home cards (4586–4587). The size figures the layout works out itself are always square metres; only a per-sq-ft price keeps "per sq ft" (8581), and an editor's own `bke_dev_facts` lines are printed as typed.
- The single-home size is shown only when plausible: not within 1% of 1/8, 1/4, 1/3, 1/2 or 1 acre, at least `max( 15, 20 × bedrooms )` m², at most 3,000 m², and the listing has bedrooms or is a studio (2892–2897).
- Sizes written in the text go through `blockke_dev_size( $s, $assume_ft = false )` (531–578): smallest and largest area in the string, square feet converted to whole square metres, terraces, plots and summed areas left out; returns e.g. `"84.6 – 126.5 m²"` or `''`.
- Unit rows come from the description's unit table (`blockke_dev_units_from_table()`, 2679–2768: a table whose header has a unit/type column and a price or size column) or, failing that, from the sub-unit listings (`blockke_dev_units_from_subs()`, 2779–2794). Each unit: `type`, `note`, `key` (bedroom key), `group`, `size`, `price`, `pnote`, `url`.

### Bedrooms, bathrooms, rooms

- `property_bedrooms` (int) and `property_bathrooms` (float) are read as facts for a listing without units, each only when the "at a Glance" table has no short `Bedrooms` / `Bathrooms` row of its own (2859–2869).
- Bedrooms are also read from titles: `blockke_dev_title_beds()` (2569–2588) returns `array( 'beds' => [ints], 'studio' => bool )`; `blockke_dev_bed_key()` (2515–2543; changed in v1.9.1 so that "1.5 Bedroom" is 1, not 5) gives `'studio'`, `'penthouse'`, `'loft'`, an int, or a range `"3-4"`.
- `blockke_dev_sim_key()` (4048–4060) is the comparison key: `'studio'`, `'1'`…`'4'` (4 means four or more), `''` for ranges. A listing counts as a studio when its title says so, it is in the category `studio-apartments`, or it has at most one bedroom and under 500 sq ft with no bedroom count in the title.
- **Rooms are never read.** Neither version reads `property_rooms` or any other room count.

### Gallery

```php
// 7403-7423
$gal = get_post_meta( $id, 'wpestate_property_gallery', true );
if ( ! is_array( $gal ) ) {
	$gal = explode( ',', (string) get_post_meta( $id, 'image_to_attach', true ) );
}
$ids = array_values( array_unique( array_filter( array_map( 'intval', array_merge( array( (int) get_post_thumbnail_id( $id ) ), $gal ) ) ) ) );
if ( $ids ) {
	$att = get_posts( array( 'post_type' => 'attachment', 'post_status' => 'inherit', 'post__in' => $ids, 'orderby' => 'post__in', 'posts_per_page' => 120 ) );
	// keeps only attachments whose post_mime_type starts with "image/"
}
// captions: wp_get_attachment_caption( $aid ), plain text, at most 80 characters
```

- Order: the featured image first, then the gallery in its stored order; duplicates dropped; deleted attachments and non-images dropped; at most 120.
- Meta keys: `_thumbnail_id`, `wpestate_property_gallery` (array of attachment ids), `image_to_attach` (comma-separated ids, the fallback).
- Captions: the attachment's Caption field (7417–7423). Alt text: the attachment's `_wp_attachment_image_alt`, else a fallback the caller passes (`blockke_dev_alt()`, 7809–7812): "{short}, {area line}" for the main photo (8737), "{short} photo N" for gallery tiles (8847).
- Video: `bke_dev_video` (YouTube or Vimeo link), else the theme's `embed_video_id` + `embed_video_type`, else the `src` of `embed_virtual_tour` (4905–4924).

### Features taxonomy and its groups

`blockke_dev_feature_groups( $id )` (2986–3021): the listing's `property_features` terms. Child terms are grouped under their parent term; the parents are ordered by slug `lifestyle-recreation`, `family-convenience`, `security-access`, `building-parking`, `water-power`, `unit-features`, `work-connectivity`, anything else after; top-level terms without children go into a last group, "Also included" (or "Amenities" when it is the only one). Each group: `title`, `icon`, `items` (term names), `rank`.

`blockke_dev_amenities( $id, $sections )` (2957–2983) prefers the description: in its Amenities section, every sub-heading or bold-only paragraph followed by a list is a group, and it needs **two or more** such groups; a single flat list is ignored and the taxonomy is used instead.

`blockke_dev_amenity_layout( $groups, $layout )` (3118–3288) turns the items into up to six feature tiles (`icon`, `title`, `text`) and a checklist (`rest`), using the pattern library `blockke_dev_amenity_lib_v17()` (3081–3107). The tile lines are fixed marketing sentences ("Morning laps, or a slow afternoon by the water."); see *What to avoid*.

### Address, map pin

- `address`: meta `property_address`; if empty, the glance row `Location` / `Address`, cut at a bracket or dash, without ", Nairobi" / ", Kenya"; if still empty, the area line (7360–7368, 7694).
- `area_line`: main area and city joined, "Kilimani, Nairobi" (7359).
- Pin: meta `property_latitude`, `property_longitude` as floats (7546–7547). 0/0 means no pin. `blockke_dev_nearby()` also treats the theme's default map centre (`wp_estate_general_latitude` / `_longitude`) and anything over 60 km from Nairobi CBD as no pin (3436–3446).
- The map is a keyless Google embed, `https://maps.google.com/maps?q={lat,lng or address}&z=15&output=embed`, lazy-loaded, plus a link to `https://www.google.com/maps/search/?api=1&query=…` (9138–9139).

### Agent

`blockke_dev_agent( $aid )` (2469–2499; identical in v1.8.2:1711–1741). `$aid` is the listing's meta `property_agent` (7666).

```php
$a = array( 'name' => '', 'role' => '', 'photo' => '', 'url' => '',
	'phone' => $cfg['phone'], 'phone_display' => $cfg['phone_display'], 'wa' => blockke_dev_digits( $cfg['whatsapp'] ), 'email' => '' );
if ( $aid && 'publish' === get_post_status( $aid ) ) {
	$a['name']  = blockke_dev_text( get_the_title( $aid ) );
	$a['role']  = blockke_dev_text( get_post_meta( $aid, 'agent_position', true ) );
	$a['photo'] = (string) get_the_post_thumbnail_url( $aid, 'thumbnail' );
	$a['url']   = get_permalink( $aid );
	$mob        = get_post_meta( $aid, 'agent_mobile', true );
	if ( ! $mob ) { $mob = get_post_meta( $aid, 'agent_phone', true ); }
	$d = blockke_dev_phone( $mob );
	if ( $d ) { $a['phone'] = '+' . $d; $a['phone_display'] = blockke_dev_phone_display( $d ); $a['wa'] = $d; }
	$a['email'] = sanitize_email( get_post_meta( $aid, 'agent_email', true ) );
}
```

Defaults come from config: `phone` `+254725937686`, `phone_display` `0725 937 686`, `whatsapp` `254725937686` (306–308). So the phone and WhatsApp are the agent's mobile when the agent post is published and has a usable number, else Block's default: project rule 14. `blockke_dev_phone()` (585–601) normalises Kenyan numbers to `2547…` (it calls the popup's `blockke_bp_normalize_phone()` when that exists); `blockke_dev_phone_display()` (603–608) prints `0725 937 686` for `254…` numbers and `+…` otherwise. On the live page the agent of 45577 resolves to `"wa":"254180711259"` (`pages-final/45577.html:6908`), which is whatever the agent post holds.

### Project term, `bke_dev_name`, the H1

- Project: the first `property_project` term (7304–7305).
- `name` (the H1, 7306–7340): `bke_dev_name` if set; else the first part of the title (split on " — ", " | ", ": "). If that part "sells rather than names" (`blockke_dev_seo_name()`, 3852–3855: it contains "for sale", "for rent" or "to let", or starts with a bedroom count), it is replaced by the development's `bke_dev_name`, else the Project term in the title's own letters (`blockke_dev_term_name()`, 3878–3919), else another title part, else what follows " at ". A name over 52 characters gives way to the Project term.
- `short` (7343, 7356–7358): `bke_dev_name`, else the development's, else the Project term, else `name`. Used in the breadcrumb, section bar, forms, alt text and WhatsApp text.
- Effect on a rental today: 45577 "3 Bedroom Apartment for Rent in Kilimani, Lenana Road" gets the H1 "Kings Courtyard" (`pages-final/45577.html:6908`, `"name":"Kings Courtyard"`). v1.9.1 strips "to Let", "for Rent", "to Rent", "for Lease" from the name inside sentences (`blockke_dev_rent_name()`, 7281–7285).
- `blockke_dev_listing_name( $pid )` (3922–3941; one regex changed in v1.9.1 so "USD 5" is not taken for a building name): the short display name used on similar-home cards and in leads: the Project term, else "… at {Name}", else the part after the dash, else the title without its selling words.

### Sub-units and the master listing

- A development ("master") has `property_has_subunits` = `'1'` and lists its units in `property_subunits_list` (array of ids) and `property_subunits_list_manual` (ids separated by commas or spaces) (`blockke_dev_subunits()`, 2616–2654: published units only, in list order, at most 60). A unit points back with `property_subunits_master`.
- `ctx['kind']` is `'unit'` (has a published master other than itself), `'master'` (`property_has_subunits` = `'1'`) or `'single'` (7587).
- A unit page gets a switcher between the development's homes (`blockke_dev_unit_nav()`, 4737–4773) and a breadcrumb through the master (7633–7650). A unit's label is `bke_unit_label`, else derived from its title (`blockke_dev_unit_label_from_title()`, 2501–2512).
- One key per development for de-duplication: `blockke_dev_dev_key()` (4187–4197): `m{master id}`, else `p{project term id}`, else `i{id}`.

### Main area and city

`blockke_dev_main_area( $id )` (3951–3986; identical in both; returns a `WP_Term` or `null`, cached per request): the slug in `bke_dev_area` (the listing's, else its master's); else the master's `property_area` terms, else the listing's; of several, the term with the highest `count` (ties: lowest term id). City: the first `property_city` term (7345).

### Status terms, sold and let, completion

- Off-plan: `blockke_dev_is_offplan()` (690–695): `property_status` has `offplan-ongoing`, and neither `property_completion` `ready-now` nor `property_status` `complete`.
- Ready: `property_status` `complete` or `property_completion` `ready-now` (7370). Every rental carries these, which is why v1.9.1 replaces the chip with "For rent" (7376–7379).
- **Sold or let:** `has_term( array( 'sold', 'let' ), 'property_status', $id )`. It is used to leave listings out of lists: the cards' rows (12744), similar homes (4237, 4328), land and area pools (6789, 11608); `sold` alone bars a landing page and the "for sale" test (725, 750). **The listing's own page does nothing with it**: no badge, no changed form, in either version. The public feed has two `Sold` listings and no `Let` term at all (`ROOT/ref/listings.json`, field `st`: `Off-Plan / Ongoing` 680, `Complete` 284, `Furnished` 13, `Ongoing` 3, `Sold` 2), so whether a `let` term exists on the site is unknown.
- Furnished: v1.9.1 treats `property_features` `furnished` or `property_status` `furnished` as furnished, else a "Furnishing" row that plainly says so (`blockke_bkn_rent_row()`, 12834–12842; used for cards while `rent_cards` is on). The feed shows `Furnished` as a status on 13 listings.
- Buy/Rent: `blockke_dev_action( $id )` (4085–4097; `v1.8.2:3248–3256`): `'rent'` when tagged `for-rent` and not `for-sale`; `'sale'` when tagged `for-sale`; with no term, the title decides ("for rent", "to let"; v1.9.1 with `rent_pages` also "to rent", "for lease", never "rent-to-own").
- Kind: `blockke_area_kind( $id )` (11360–11386; identical in both) returns `array( 'home'|'commercial'|'land', type )` from `property_category`: land slugs `land`, `residential-land`, `agricultural`, `industrial-land`; commercial slugs `commercial`, `offices`, `warehouses`, `shop` (unless the listing is also in a home category and has bedrooms or units); home slugs `apartments`, `villas-residential`, `houses-residential`, `residential`, `gated-community`, and config `studio_cat` (`studio-apartments`).
- Completion (not shown for rentals in v1.9.1): `bke_completion_stated`, else a glance row with a date, else the `property_completion` term other than `ready-now` (`blockke_dev_completion()`, 989–1008).

### Every meta key

Listing (`estate_property`):

| Key | Read at | Holds |
|---|---|---|
| `property_price` | 7397, 2649, 4211, 10686 | number |
| `property_label` | 4069, 7216, 740, 4294 | after-price label |
| `property_label_before` | 4069, 7698 | before-price label |
| `property_size` | 2643, 2890, 7599, 4213 | number, sq ft |
| `property_bedrooms` | 2863, 7596, 4053, 2647 | int |
| `property_bathrooms` | 2863 (key named at 2860) | number |
| `property_address` | 7360 | text |
| `property_latitude`, `property_longitude` | 7546–7547, 1675, 2189–2190 | decimal strings |
| `property_agent` | 7666, 10410, 1590 | agent post id |
| `property_has_subunits` | 7587, 10191, 4192 | `'1'` on a development |
| `property_subunits_list`, `property_subunits_list_manual` | 2617, 2619 | array of ids; id list as text |
| `property_subunits_master` | 7307, 3958, 10195 | master id on a unit |
| `_thumbnail_id` | 7407 (through `get_post_thumbnail_id()`), 12775 | attachment id |
| `wpestate_property_gallery`, `image_to_attach` | 7403, 7405 | array of ids; comma list |
| `embed_video_id`, `embed_video_type`, `embed_virtual_tour` | 4915–4919 | theme video fields |
| `use_floor_plans`, `plan_image`, `plan_image_attach` | 4966–4967 | theme floor plans (read only when config `floor_plans_default` is off) |
| `rank_math_description` | 7444 | tagline fallback |
| `prop_featured` | 12804 | cards only |
| `wpestate_total_views`, `wpestate_detailed_views` | 10229–10237 | **written** by the view counter |
| `bke_dev_layout` | 10178, 11108, 12776 | `''`, `on`, `off` |
| `bke_dev_kind` | 708 | `land` or `homes` override |
| `bke_dev_name`, `bke_dev_tagline`, `bke_dev_overview_heading`, `bke_dev_facts` | 7306, 7439, 7717, 2839 | editor overrides |
| `bke_dev_places` | 3604 | "Place \| travel time" per line |
| `bke_dev_hero_label`, `bke_dev_gallery_note` | 7655–7656 | photo labels (`none` hides) |
| `bke_dev_brochure`, `bke_dev_video` | 4927, 4906 | URLs |
| `bke_dev_similar`, `bke_dev_similar_off` | 4316, 7605 | ids to show first; any value hides the block |
| `bke_dev_area` | 3961 | main-area slug |
| `bke_unit_label` | 2639, 7648 | a unit's label |
| `bke_completion_stated` | 990, 4178 | completion as text |
| `bke_dev_calc_price`, `bke_dev_deposit_pct`, `bke_dev_reservation`, `bke_dev_plan_until`, `bke_dev_plan_months`, `bke_dev_plan_text` | 7700, 1075, 1079, 1096, 1113, 1098 | payment plan and calculator (sale only) |
| `bke_dev_progress` | 5539 | construction reports |
| `bke_index_line` | 12806 | cards only |
| `bke_rent_usd`, `bke_rent_usd_date` | 7232, 7243, 12830–12832 | **v1.9.1 only**: rent in US dollars and the date of the KES figure (`YYYY-MM-DD`) |

Other posts: agent `agent_position` (2483), `agent_mobile` (2486), `agent_phone` (2488), `agent_email` (2496); attachment `_wp_attachment_image_alt` (7810), `_wp_attached_file` (4979); page `bke_lp_listing` (7929).

Taxonomies: `property_action_category` (`for-rent`, `for-sale`), `property_category`, `property_area`, `property_city`, `property_status` (`offplan-ongoing`, `complete`, `sold`, `let`, `furnished`), `property_completion` (`ready-now`, years), `property_project`, `property_features`. The seven that invalidate caches are listed in `blockke_dev_sim_taxes()` (11027–11029).

## (d) How the description is parsed

All of the functions in this section are byte-identical in v1.8.2 and v1.9.1, except `blockke_dev_rent_info()`, which exists only in v1.9.1.

### From `post_content` to HTML

```php
// 765-779
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

It does **not** run `the_content` filters, so nothing other plugins hang on that filter (share buttons, related posts) gets in. `wptexturize()` turns straight quotes and dashes into typographic ones, which every later pattern has to allow for (’ as well as ').

### Tokens and sections

| Function | Lines | Returns |
|---|---|---|
| `blockke_dev_tokens( $html )` | 782–805 | A flat list of the block elements it reads: `<(h[1-6]\|p\|ul\|ol\|figure\|table\|details\|blockquote)…>…</…>`, non-greedy. Each token: `tag` (`h`, `p`, `ul`, `table`, `details`, `blockquote`), `html`, `inner`, `pos`, and `level` for headings. `ol` is filed as `ul`; a `figure` counts only when it holds a table and is then filed as `table`. |
| `blockke_dev_sections( $html )` | 808–848 | `array( 'level', 'intro', 'sections', 'tokens', 'html' )`. `level` is the highest heading level present from H2 down (H2, or H3 when there is no H2; 7 when there are no headings). `intro` is the tokens before the first heading of that level. Each section: `title` (plain text), `level`, `tokens` (everything up to the next heading of that level, lower headings included), `start`, `html` (the slice of the source). |
| `blockke_dev_parsed( $post )` | 850–856 | The above for a post, cached per request. |
| `blockke_dev_find( $sections, $re, $not = '' )` | 858–865 | Index of the first section whose title matches `$re` and not `$not`, else −1. |
| `blockke_dev_sub_section( $parsed, $re )` | 3708–3732 | A heading **below** the top level whose text matches, with the tokens and HTML up to the next heading as high. Skips headings that end in "?" or carry a FAQ-question class. Used only for the FAQ and the payment plan. |

Known limits of the token reader: it is a regex, so a list nested in a list ends the outer list at the inner `</ul>`; text that is not inside one of the listed tags (a bare `<div>`, a Rank Math answer `<div>`) produces no token, though it stays in the section's `html`.

### Section matching by heading

| Section | Title pattern | Excluding | Line | What is read |
|---|---|---|---|---|
| Glance | `/glance\|snapshot\|key facts\|quick facts\|in brief\|at a look\|summary/i` | | 980 | Label/value pairs (`blockke_dev_kv()`). |
| Highlights | `/^why\b\|highlights\|key features\|why (buy\|choose\|invest)/i` | `/investor/i` | 7469 | The first list; each item's bold label, else its text, when 110 characters or fewer; six at most (7470–7484, 7719). |
| Location | `/location\|neighbou?rhood\|the area\|getting around\|connectivity/i` | | 7487 | Intro paragraph, places, note (below). |
| Amenities | `/amenit\|facilit\|lifestyle/i` | `/finish\|spec/i` | 2958, 3333 | Groups (below) and an intro paragraph. |
| FAQ | `/faq\|frequently asked\|questions/i`; if none, a sub-heading `/faq\|frequently asked/i` | | 7568, 7571 | Questions and answers (below). Left out of the full description. |
| Contact | `/enquir\|call to action\|contact\|get in touch\|book a viewing\|next step/i` | | 7569 | Nothing: the section is left out of the full description. |
| Who it suits | `/\bwho\b.*\b(suits?\|for)\b\|suits? (best\|well)\|ideal (for\|buyers)\|right for you/i` | `/faq\|question/i` | 3641 | First list, and a note. |
| Investors | `/investor\|investment\|rental yield\|returns/i` | `/faq\|question/i` | 3670 | Up to three sentences. v1.9.1 does not show it on a rental (9092). |
| Payment | `/payment/i` | | 1023 | Sale only. |
| Rental terms (v1.9.1) | `/\b(?:rental\|rent\|lease\|leasing\|tenancy\|letting)\s+(?:and\s+\w+\s+)?terms?\b\|\bterms of (?:the )?(?:lease\|tenancy\|rent)\b\|^rent(?:al)?(?:\s+details)?$\|payment plan\|^pricing\b/i` | `/faq\|question/i` | 7142 | Label/value pairs, added to the glance pairs. |

Two things to know about today's rental headings: "Why It Works for Tenants" matches the Highlights pattern (`^why\b`), and "Arrange a Viewing" does **not** match the Contact pattern (only "book a viewing" does), so on the development layout that section stays in the full description.

### Key/value tables ("Project Snapshot", "at a Glance")

- `blockke_dev_table( $html )` (902–931) → `array( 'head' => [cells], 'rows' => [ array( 'cells' => [text], 'links' => [href or ''] ) ] )`. The first row is the header when all its cells are `<th>`, or when the table has a `<thead>`.
- `blockke_dev_list_items( $inner )` (867–885) → for each `<li>`: `html`, `text`, `label` (a leading `<strong>` / `<b>`), `rest`.
- `blockke_dev_split_kv( $item )` (888–900) → `array( label, value )` from a bold label (60 characters at most) or a plain "Label: value" (label 2 to 48 characters).
- `blockke_dev_kv( $tokens )` (934–956) → pairs from every table row with two or more cells (first cell without its trailing colon, second cell) and every list item that splits.
- `blockke_dev_kv_get( $kv, $re )` (958–965) → the first value whose label matches; `blockke_dev_kv_key()` (968–975) → that label.
- `blockke_dev_glance( $post )` (978–982) → the pairs of the Glance section.
- Facts built from them: `blockke_dev_facts()` (2838–2934): `bke_dev_facts` if the editor filled it ("Label | Value" per line, six at most), else Residences, Homes, Bedrooms, Bathrooms, Size(s), Floors or Building, Lifts, Parking, Plot or Land, and Completion when fewer than three facts were found.

### FAQ formats

`blockke_dev_faqs( $tokens, $html = '' )` (3734–3813) returns `array( array( question, answer HTML ), … )`. In order:

1. **Rank Math and Yoast FAQ blocks**: elements with the class `rank-math-question` or `schema-faq-question`, each followed by one with `rank-math-answer` or `schema-faq-answer` (3751–3762). If any are found, only these are returned.
2. **A heading** is a question; the paragraphs and lists up to the next heading are its answer (3764–3771, 3805–3811).
3. **`<details><summary>Question</summary>Answer</details>`** (3772–3775).
4. **`<p><strong>Question</strong><br>Answer</p>`** (3777–3785).
5. **`<p><strong>Question?</strong> Answer</p>`**: the bold part must end in "?" (3786–3795).
6. **`<p><strong>Question?</strong></p>`** followed by paragraphs or lists (3796–3803).

Answers go through `wp_kses()` with `p`, `a[href|target|rel]`, `strong`, `b`, `em`, `i`, `br`, `ul`, `ol`, `li` (3738–3749).

### Amenity lists

`blockke_dev_amenities()` (2957–2983): inside the Amenities section a group is a sub-heading, or a paragraph that is only bold text, followed by a list. It needs two groups; otherwise the Features taxonomy is used (section (c)). `blockke_dev_amenity_intro()` (3332–3347) takes the section's first real paragraph (40 characters or more, not ending in a colon).

### Location lists and tables

In `blockke_dev_data()` (7486–7565):

- The first paragraph over 40 characters is the intro, cut to whole sentences within 520 characters (`blockke_dev_fit_sentences()`, 460–481).
- The first list or table gives the places. A list item is split into name and value; a table row uses its first two cells, and a header written as an ordinary row ("Destination | Drive time") is skipped (7511–7526).
- Every row passes through `blockke_dev_place_row( $name, $val )` (3551–3581), which returns `array( name, value )` or `null`. It turns "~15 minutes | to Westlands" round, and rejects sentences, topics ("Schools | Brookhouse and Rosslyn are close by") and names over 60 characters.
- `blockke_dev_place_here()` (3590–3600) moves "within the estate", "on site", "next door" from the value into the name.
- A later paragraph that mentions "approximate", "vary" or "traffic" becomes the note.
- Overrides: the editor's `bke_dev_places` box wins (`blockke_dev_places_override()`, 3602–3614), then the development's box for a unit (`blockke_dev_master_places()`, 3617–3623). With no places at all, the nearest landmark of each kind from the map pin is listed with the note "Straight-line distances from the map pin. Road distances and travel times will be longer." (7548–7555; `blockke_dev_nearby()`, 3433–3477, 36 Nairobi landmarks at 3375–3414).

### "Who it suits"

`blockke_dev_suits( $sections )` (3636–3661) → `array( 'items' => [...], 'note' => '' )`: the first list of the section, eight items at most, each kept whole up to 220 characters; the note is a following paragraph that says "less well", "less suited", "not ideal", "may not suit" and the like, two sentences at most.

### Lead text and the full description

- Intro paragraphs: up to three paragraphs over 30 characters before the first section; a title-like first line (110 characters or fewer, no closing punctuation, no link or image) is skipped (7426–7438).
- Tagline: `bke_dev_tagline`; else the first sentence of the excerpt; else the Rank Math description without its call-to-action sentences; else the first intro sentence; a leading "{Name} is …" is dropped; 240 characters at most (7439–7466).
- Full description: the intro's leftovers, then every section except FAQ and Contact, with headings moved down one level (H2 → H3 … H5 → H6) (7575–7584). It is printed as it is inside `<details class="bkd-more">` (8835), **not** through `wp_kses_post()`.

### Run against today's 129 rental descriptions

Method: the description panel of each rent-only listing was cut out of the saved classic pages (`ROOT/ref/pages-final/ID.html`, and `ROOT/ref/pages-stop/ID.html` for the 29 in `dev29.txt`), which is the theme's rendering of the same text, and fed to v1.9.1's own functions in the harness. Scripts: `/tmp/claude-0/-workspace-Loyd2024/e24ac003-f571-5336-9956-1fd11a814c7d/scratchpad/devmap-work/parse-check.php`, and `parse-flat.php`, `parse-values.php`, `parse-more.php` in the same folder (the 129 cut-out descriptions are in its `desc/` folder).

| Finding | Count |
|---|---|
| Top heading level is H2 / H3 / H4 / no headings at all | 88 / 33 / 2 / 6 |
| **Exactly one top-level section**: the description opens with a title-like heading and every real section is one level below it | **18** (41690, 42279, 42340, 43453, 43523, 43712, 43819, 44135, 44339, 44747, 44748, 45228, 45231, 45318, 45367, 45507, 45538, 45577) |
| No headings | 6 (31842, 43995, 44020, 44533, 44745, 44746) |
| Glance section found at the top level / only below it | 94 / 18 |
| Rental terms section found at the top level / only below it | 73 / 21 |
| Location section: top level / only below | 104 / 18 |
| Amenities section: top level / only below | 97 / 18 |
| FAQ: top level / through the sub-heading fallback / at least one question read | 99 / 23 / 120 |
| Amenities section with two or more titled groups (so the text, not the taxonomy, is used) | 12 |
| Highlights taken from a "Why It Works for…" section | 88 |
| "Who it suits" items found | 90 |
| An Investors section still present | 5 |
| Contact section matched ("Call to Action", "Book a Viewing") | 36; "Arrange a Viewing" (38 at top level) is not matched |

**The top-level rule fails for the 18.** For them `blockke_dev_find()` sees one section, named like the listing, so the layout reads no glance table, no rental terms, no amenities and no location; only the FAQ is found, through `blockke_dev_sub_section()`. Fixtures 45577, 45538 and 45231 are among them. The rental parser should find a section by its heading at **any** level and take everything up to the next heading of the same or a higher level, which is what `blockke_dev_sub_section()` already does for one heading. Splitting at every heading is not the answer either: it cuts an Amenities section into its sub-headed groups.

Most common top-level section titles: Location Advantage (89), Finishes and Specifications (79), Amenities (72), Project Snapshot (70), Who This Development Suits Best (53), FAQ (51), Unit Types (43), Rental Terms (40), Frequently Asked Questions (38), Why It Works for Tenants (38), Arrange a Viewing (38), Why It Works for Companies Housing Staff (37), Call to Action (19), Why It Works for Residents (13), Why It Works for Business Occupiers (12), Rent and Lease Terms (11). "Opening Introduction", a template leftover, is still a heading in eight descriptions.

What `blockke_dev_rent_info()` (7139–7186) returns as *stated* on these texts (with every heading treated as a section, so the 18 are included):

| Row | Listings | Values that are not facts |
|---|---|---|
| Availability | 25 | "For rent" (39421, 41862, 41921), "To let" (34906), "Contact Block Real Estate for current vacancy status" (41690), "Subject to apartment availability" (36410) |
| Service charge | 21 | none seen; amounts or "included" |
| Furnishing | 15 | "Theres also unfurnished apartments" (43523), "There is also furnished apartments" (43788), "Furnished space available" (40298) |
| Deposit | 5 | none seen |
| Lease | 6 | "2 months rent and one month deposit" (43855) and "Two months deposit and one month rent" (43856): a deposit, read as a lease from a row labelled for both |
| Viewing | 2 | none |
| USD rent | 3 | 34171 (2,600), 35583 (5,500), 36816 (1.30 per sq ft) |

So the reader's "still open" pattern (7146) needs "contact", "subject to" and bare "for rent" / "to let" added before its rows can be shown as facts under project rule 11, and a deposit sentence must not be accepted as a lease. That is for the rules notes; it is recorded here because the function is on the port list.

### Helpers that are self-contained enough to port

All line ranges are exact (tokenizer). "Pure" means it needs nothing but PHP and the helpers named.

| Helper | Lines | Depends on | Note |
|---|---|---|---|
| `blockke_dev_text( $html )` | 436–442 | `wp_strip_all_tags()` | Plain text: `<br>` to space, entities decoded, no-break and zero-width spaces handled, whitespace collapsed. |
| `blockke_dev_trim_words()`, `blockke_dev_sentences()`, `blockke_dev_fit_sentences()` | 444–452, 454–457, 460–481 | pure | Sentence and length cutting. |
| `blockke_dev_content_html( $post )` | 765–779 | WordPress only | |
| `blockke_dev_tokens()`, `blockke_dev_sections()`, `blockke_dev_find()` | 782–805, 808–848, 858–865 | `blockke_dev_text()` | Port the tokens; replace the top-level rule (see above). |
| `blockke_dev_sub_section()` | 3708–3732 | `blockke_dev_text()` | The "any level" section reader to build on. |
| `blockke_dev_list_items()`, `blockke_dev_split_kv()`, `blockke_dev_table()`, `blockke_dev_kv()`, `blockke_dev_kv_get()` | 867–885, 888–900, 902–931, 934–956, 958–965 | `blockke_dev_text()` | |
| `blockke_dev_faqs()` | 3734–3813 | `blockke_dev_text()`, `wp_kses()` | Uses `\x{2013}` in a `/u` pattern, which the connector leaves alone (it only rewrites a backslash followed by `u`). |
| `blockke_dev_place_row()`, `blockke_dev_place_here()` | 3551–3581, 3590–3600 | pure | |
| `blockke_dev_suits()` | 3636–3661 | section helpers | |
| `blockke_dev_size()` | 531–578 | `blockke_dev_text()`, `blockke_dev_num()` (518–521) | |
| `blockke_dev_money()`, `blockke_dev_short()`, `blockke_dev_parse_money()` | 483–486, 488–498, 501–516 | config `currency` | |
| `blockke_dev_feature_groups()` | 2986–3021 | `blockke_dev_text()`, `blockke_dev_amenity_icon()` (2936–2955) | |
| `blockke_dev_rent_info()` | 7139–7186 | `blockke_dev_kv()` | v1.9.1 only. Port with the corrections above. |
| `blockke_dev_rent_phrase()` | 7212–7267 | `blockke_dev_pm()` (reads `property_label`), `get_post_meta()`, the money helpers | v1.9.1 only. The owner-approved rent wording. |
| `blockke_dev_rent_name()` | 7281–7285 | pure | v1.9.1 only. |
| `blockke_dev_from_said()` | 3820–3844 | `blockke_dev_parse_money()` | Only if the page words "From" as the layout does. |

Not worth porting as they are: `blockke_dev_amenity_layout()` (3118–3288, 170 lines of title heuristics plus fixed tile sentences), `blockke_dev_data()` (459 lines, most of it sale logic), `blockke_dev_plans_said()` (5029 on; it holds a pattern that matches script and style tags, line 5049, which the site's firewall rejects), `blockke_dev_html_ids()` and `blockke_dev_ld_own()` (the same kind of pattern at 2257 and 8224).

## (e) Images, the gallery and its viewer

Identical in both versions, except the optional `gallery_ssr` links, which are v1.9.1 only.

### `blockke_dev_img()` and `blockke_dev_alt()`

```php
// 7804-7812
function blockke_dev_img( $aid, $size, $attr ) {
	$html = $aid ? wp_get_attachment_image( $aid, $size, false, $attr ) : '';
	return $html ? $html : '<div class="bkd-ph"><b>' . esc_html( isset( $attr['data-ph'] ) ? $attr['data-ph'] : '' ) . '</b><span>Images on request</span></div>';
}
function blockke_dev_alt( $aid, $fallback ) {
	$alt = trim( (string) get_post_meta( $aid, '_wp_attachment_image_alt', true ) );
	return '' !== $alt ? $alt : $fallback;
}
```

- **Sizes and `srcset`.** WordPress builds `srcset` and prints `width` and `height` itself; the snippet only passes `sizes`, `loading`, `decoding`, `alt` and a `data-ph` (the text for a placeholder). The live hero image carries widths 105, 120, 215, 300, 500, 585, 768, 1024, 1536, 1620 and 2048 in its `srcset` (`ROOT/ref/pages-final/45577.html:4640`), so those sizes exist on the site.
- **Placeholders.** No attachment, or one WordPress cannot render: the navy box `.bkd-ph` with the name and "Images on request" (CSS 9618–9620).
- **Missing files.** A file that fails to load is handled in the browser: the `error` listener hides the image and inserts the same placeholder; a failed agent photo becomes an initial in a circle (9853–9858).

Where each size is used:

| Use | Size | `sizes` | Loading | Line |
|---|---|---|---|---|
| Main photo | `full` | `(min-width: 1000px) 56vw, 100vw` | `eager`, `fetchpriority="high"`, `decoding="async"` | 8727–8743 |
| Two thumbs under the main photo (only with the photo transition on) | `medium_large` | `(min-width: 1000px) 340px, 1px` | lazy | 8750 |
| Gallery tiles, photos 1 to 5 | `large` | first: `(min-width: 900px) 66vw, 100vw`; others: `(min-width: 900px) 33vw, 50vw` | lazy | 8847 |
| Amenity photo strip (captioned photos) | `large` | `(min-width: 1000px) 30vw, 80vw` | lazy | 8933 |
| Similar-home cards | `medium_large` | `(min-width: 1000px) 33vw, 78vw` | lazy | 9164 |
| Viewer, large file | URL of size `1536x1536`, else the original | — | on demand | 10317–10320 |
| Viewer, strip | URL of size `thumbnail` | — | lazy | 10321 |
| Agent photo | `thumbnail` URL, drawn 64 × 64 | — | lazy | 9200 |
| Video poster | YouTube `hqdefault.jpg`, else the main photo's `large` URL | — | lazy | 8862 |

Two things to do differently on the rental page: the main photo uses size `full`, so its `src` is the original upload (2048 px wide on 45577); and the live markup has `fetchpriority="high"` and `loading="lazy"` **twice** on the same tags (`pages-final/45577.html:4640`: `<img fetchpriority="high" width="2048" … loading="eager" fetchpriority="high" …>`, `<img loading="lazy" width="768" … loading="lazy" …>`). Where the first copy comes from is in *Unknown*.

Photo labels: `hero_label` is "Project render" on off-plan listings and empty otherwise; `gallery_note` is "Project renders." or "Photos of {short}."; the custom fields `bke_dev_hero_label` and `bke_dev_gallery_note` override, and `none` hides (7652–7664).

### Gallery markup

```html
<!-- 8840-8854; only when the listing has 3 or more photos -->
<section class="bkd-sec bkd-sec-tight" id="bkd-gallery"><div class="bkd-wrap"><div class="bkd-gal bkd-rv">
  <button type="button" data-bkd-open="0" aria-label="Open photo 1: Rooftop pool">
    <span class="bkd-gal-cap">Rooftop pool</span><img …>
  </button>
  … up to five buttons …
</div>
<div class="bkd-sh-row bkd-gal-foot"><p class="bkd-fine">Photos of Kings Courtyard.</p>
  <button class="bkd-link" type="button" data-bkd-open="0">View all 14 photos</button></div></div></section>
```

- Five buttons are printed, three are shown: `#bkd .bkd-gal button:nth-child(n+4){display:none}` (9451). The first is 16:9 and full width on phones, and four of six columns from 900px (9445–9452).
- The main photo carries its own "View N photos" button when there is more than one photo (8742).
- Every opener is a `<button data-bkd-open="index">`. **Photos 6 and up exist only in the script's data** (`window.BKDEV.gallery`, 10315–10322: `[ large URL, alt, thumbnail URL, caption ]` per photo). Without script nothing opens and only the main photo and three tiles are visible. v1.9.1's `gallery_ssr` (off by default) adds visually hidden links for photos 6 and up (`blockke_dev_gallery_more()`, 8170–8184).

### The viewer

```html
<!-- 9220-9223 -->
<div class="bkd-lb" id="bkd-lb" hidden role="dialog" aria-modal="true" aria-label="Photo gallery">
  <div class="bkd-lb-top"><span id="bkd-lb-n"></span>
    <button class="bkd-lb-x" type="button" data-bkd-lbclose aria-label="Close gallery">×</button></div>
  <div class="bkd-lb-stage"><button class="bkd-lb-prev" type="button" aria-label="Previous photo">‹</button>
    <img id="bkd-lb-img" alt=""><button class="bkd-lb-next" type="button" aria-label="Next photo">›</button></div>
  <div class="bkd-lb-strip" id="bkd-lb-strip"></div>
</div>
```

Script behaviour (10032–10072):

| | |
|---|---|
| Open | A click on any `[data-bkd-open]` (10039). The strip of thumbnail buttons is built on first open (10034). The page scroll is locked with `html.bkd-lock` (9955, CSS 9600). The event `gallery_open` is tracked. |
| Showing a photo | `show(i)` wraps round at both ends, sets `src` and `alt`, writes "3 / 14  ·  caption" into `#bkd-lb-n`, marks the strip button `.on` and scrolls it into view (10035–10036). No preloading of the next photo. |
| Keyboard | `Escape` closes, `ArrowLeft` / `ArrowRight` step (10061–10062). `Tab` is trapped inside the dialog (10044, `trap()` at 10059–10060). |
| Swipe | On the stage: one finger, more than 40 px sideways and more sideways than 1.2 × vertical, steps one photo (10055–10057). Both listeners are passive. |
| Focus | On open, the element that had focus is remembered and focus goes to the close button; on close it returns to that element (10037–10038). |
| Captions | From the Media Library caption, in the counter line and on the gallery tile (`.bkd-gal-cap`). |
| Not there | A click on the backdrop does not close it (only the ✕). The browser's Back button does not close it (no history entry, unlike the search sheet at 9990). No pinch zoom. |

CSS: fixed, `inset:0`, `z-index:999993`, three rows (top bar, stage, strip); the photo is `object-fit:contain`; on phones the stage has no side padding and the arrows sit over the photo (9604–9616, 9753–9755). The enquiry dialog is `z-index:999992` (9590).

## (f) Enquiries end to end

### Form markup

| | Lines | `v1.8.2` |
|---|---|---|
| `blockke_dev_quick_form( $source, $cta, $d )`: name and phone | 8108–8114 | 6837–6841 |
| `blockke_dev_full_form( $d, $alert = '' )`: the enquiry section's form | 8116–8138 | 6843–6858 |
| `blockke_dev_field( $name, $label, $type, $ac, $ph = '', $optional = false )` | 7814–7818 | same |
| `blockke_dev_chips( $name, $legend, $options, $checked )`: radio "chips" | 7820–7826 | same |
| `blockke_dev_ok( $more = '' )`: the thank-you panel, printed right after each form | 7881–7885 | same |
| `blockke_dev_more( $d, $key )`: the optional questions inside the thank-you | 7856–7879 | 6711–6732 |
| `blockke_dev_consent( $d )`, `blockke_dev_privacy_url()` | 7898–7902, 7887–7896 | same |

```html
<!-- quick form, v1.8.2 (v1.9.1 adds method/action and hidden fields only on its no-script path) -->
<form class="bkd-form" data-bkd-lead="hero" novalidate>
  <input class="bkd-hp" type="text" name="bkd_hp" tabindex="-1" autocomplete="off" aria-hidden="true">
  <div class="bkd-qgrid">
    <label class="bkd-f" data-f="name"><span>Full name</span><input name="name" type="text" autocomplete="name"><em class="bkd-err" aria-live="polite"></em></label>
    <label class="bkd-f" data-f="phone"><span>Phone / WhatsApp</span><input name="phone" type="tel" autocomplete="tel" placeholder="07XX XXX XXX" inputmode="tel"><em class="bkd-err" aria-live="polite"></em></label>
  </div>
  <button class="bkd-btn bkd-btn-navy bkd-btn-block" type="submit">Send me the price list …</button>
  <div class="bkd-consent">We'll reply by WhatsApp or phone. By sending this you agree to be contacted about {short}. <a href="…">Privacy policy</a></div>
</form>
<div class="bkd-ok" hidden tabindex="-1"> … </div>
```

- **Fields.** Quick form: `name`, `phone`. Full form: `name`, `phone`, `email` (optional), radio groups `unit` ("Interested in": the unit groups plus "Not sure yet", only with two or more groups), `purpose` ("Buying to": Live in / Invest / Both), `timeline` ("When would you like to buy?": Within 3 months / 3–12 months / Just exploring), `contact` ("Best way to reach you": WhatsApp / Phone call / Email, WhatsApp preselected), and `message` (textarea). In v1.9.1 rent mode `purpose` is gone and `timeline` reads "When would you like to move in?" with This month / 1–3 months / Later (8128–8133, 7829–7831).
- **Honeypot.** `<input class="bkd-hp" type="text" name="bkd_hp" tabindex="-1" autocomplete="off" aria-hidden="true">`, not drawn at all (`#bkd .bkd-hp{display:none!important}`, 9348). The script sends its value as `hp`. It was called `company` before v1.3.1, which browser AutoFill filled in for real visitors (header comment, 85–89).
- **Hidden fields.** None on the script path: the listing id, the page URL, the form's source and intent and the attribution all come from the script. v1.9.1's no-script path prints `bkd_np=1`, `listing`, `page`, `source` and (classic box) `bkd_back` (8018–8022), and `rp=1` on an editor's preview (4150–4152).
- **`novalidate`** on every form: the browser's own validation never runs; the script validates, then the server.
- **Triggers.** Any `[data-bkd-enquire="source"]` button opens the dialog `#bkd-md`, which holds one quick form; the trigger's value becomes the form's `data-intent`, and `data-unit` its unit (9956–9975). Sources in use: `hero`, `modal`, `contact`, `subnav`, `mobile-bar`, `floorplan`, `payment-plan`, `mortgage`, `brochure`, `comparables`, `compare`, `find`, `alternatives`, `search-shortlist`, `lp-header`, `progress`, `progress-next`, and v1.9.1's `viewing` and `classic` (10690–10727).

### The client script

`blockke_dev_js()`, lead part 9872–9942 (identical in both versions; v1.9.1 swaps words in it for rentals with `blockke_dev_rent_js()`, 7086–7129).

```js
// 9916-9941, shortened
root.addEventListener('submit',function(e){
  var form=e.target.closest&&e.target.closest('form[data-bkd-lead]');if(!form){return;}
  e.preventDefault();
  // name: 2+ characters; phone: 9 to 15 digits; email, if given: /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/
  var p={listing:C.id,name:d.name,phone:d.phone,email:d.email||'',
    unit:d.unit&&d.unit!=='Not sure yet'?d.unit:(form.getAttribute('data-unit')||''),
    purpose:d.purpose||'',timeline:d.timeline||'',contact:d.contact||'',message:d.message||'',
    source:form.getAttribute('data-bkd-lead'),intent:form.getAttribute('data-intent')||'',
    page:location.href.slice(0,300),attr:attr(),hp:d.bkd_hp||''};
  fetch(C.endpoint,{method:'POST',headers:{'Content-Type':'application/json',Accept:'application/json'},body:JSON.stringify(p),credentials:'same-origin'})
    .then(/* not ok, or no j.ok: throw with j.message and the status */)
    .then(function(j){
      track('generate_lead',{lead_source:p.source,lead_unit:p.unit});
      try{if(typeof window.blockkeLeadConversion==='function'){window.blockkeLeadConversion('lead_form');}}catch(x){}
      try{if(typeof window.fbq==='function'){window.fbq('track','Lead',{content_name:C.name});}}catch(x){}
      success(form,p,j);
    })
    .catch(function(err){fail(form,p,err&&err.msg,err&&err.code);});
});
```

- Field errors are written into each field's `<em class="bkd-err">` and the first bad field is focused (9920–9925).
- The submit button is disabled while the request runs (9930).
- Name, phone and email are remembered for the session in `sessionStorage.bkd_lead` and pre-filled into the dialog (9873–9874, 9929).
- Visitors whose time zone is not `Africa/Nairobi` see an international phone example as the placeholder (9943–9951).

### Client-side conversions: the exact calls

| Destination | Call | Line |
|---|---|---|
| GA4 | `window.gtag('event','generate_lead',{lead_source, lead_unit, development: C.slug, listing_id: String(C.id)})`; when `gtag` is not a function the same object is pushed to `window.dataLayer` with `event:'generate_lead'` | 9867–9868, 9935 |
| Google Ads | `window.blockkeLeadConversion('lead_form')` if that function exists. It is defined by the site's "Google Ads lead conversion" header script (`ROOT/ref/live-snippets/33892.printed.html`), not by this snippet. | 9936 |
| Meta | `window.fbq('track','Lead',{content_name: C.name})` if `fbq` exists | 9937 |

Other events go through the same `track()`: `click_whatsapp` (every `[data-bkd-wa]` link), `click_call` (every `tel:` link) (9869–9870), `enquiry_open`, `gallery_open`, `share`, `lead_details`, `similar_view`, `similar_click`, `similar_see_all`, `video_play`, `tour_open`, `back_to_results`, `asset_open`.

The v1.9.1 classic rent box fires the same three calls after its no-script post returns, once per session per listing (key `bkd_rent_lead_{id}`), with `lead_source:'classic'` (2141–2150).

### Attribution: where gclid and the UTM tags come from

```js
// 9861-9866
function attr(){
  var a={};try{a=JSON.parse(sessionStorage.getItem('blkbp_attr')||'{}');}catch(e){a={};}
  try{var q=new URLSearchParams(location.search);['utm_source','utm_medium','utm_campaign','utm_term','utm_content','gclid','fbclid','gbraid','wbraid'].forEach(function(k){var v=q.get(k);if(v&&!a[k]){a[k]=v.slice(0,120);}});}catch(e){}
  if(!a.land){a.land=location.href.slice(0,250);a.ref=(document.referrer||'').slice(0,200);}
  return a;
}
```

- `sessionStorage.blkbp_attr` is written on every front-end page by the price-check popup snippet's "attribution keeper" (`ROOT/ref/live-snippets/40752.php:565`, printed on `wp_footer` 40): the first value seen in the session of each of the nine tags, plus `t` (time), `ref` (the first referrer) and `land` (the first URL).
- The page's own query string fills any tag the keeper does not have yet. If the keeper has not run, the current page becomes the landing page.
- The server keeps eleven keys (`utm_source`, `utm_medium`, `utm_campaign`, `utm_term`, `utm_content`, `gclid`, `fbclid`, `gbraid`, `wbraid`, `ref`, `land`), each cut to 250 characters (10606–10613), and turns them into a channel with the popup's `blockke_bp_channel( $attr )` ("Google Ads", "Meta", "Organic search", "Website direct", …) and a Zoho lead source with `blockke_bp_lead_source( $channel, $attr )`; without the popup snippet: "Google Ads" with a gclid, "Meta" with an fbclid, else "Website", and lead source "Website - block.ke" (10614–10616).
- **The raw tags are not stored in the lead row.** The row keeps `channel` and `source` only; gclid, UTM, landing page and referrer appear in the email and in the Zoho description (10791–10804).
- v1.9.1 no-script posts: the classic box prints `attr[utm_source]` … `attr[land]` hidden inputs from the request's own query (1915–1924) and its small script adds the session's values just before the post (2129–2140); otherwise the server reads the tags from the query string of the posted `page` (`blockke_dev_np_attr()`, 10936–10948).

### REST route

```php
// 10961-10974 (v1.8.2:9067-9080, with 'callback' => 'blockke_dev_handle_lead')
register_rest_route( 'block/v1', '/development-lead', array(
	'methods'             => 'POST',
	'callback'            => 'blockke_dev_lead_route',
	'permission_callback' => '__return_true',
) );
```

Public, no nonce. Other routes in the same namespace: `block/v1/view` (17697, only while config `prefetch` is on), the popup's `block/v1/buyer-lead` (`40752.php:548–561`), and `block/v1/contact-click` (seen in `ROOT/ref/live-snippets/40769.printed.html`). `block/v1/rental-lead` is free.

### The handler, step by step

`blockke_dev_handle_lead( $req )` (10537–10893; `v1.8.2:8744–9065`).

| Step | Lines | Detail |
|---|---|---|
| Read | 10538–10544 | JSON body, else request parameters. Every scalar goes through `sanitize_text_field()` and `mb_substr()`. |
| Rate limit | 10545–10557 | Address: `HTTP_CF_CONNECTING_IP` if present, else `REMOTE_ADDR` (not checked against Cloudflare's ranges here). Transient `bkd_rl_{md5(address)}`; at 8 the answer is HTTP 429 `{ok:false, message:"Too many requests from this connection. Please WhatsApp us directly."}`; every request, spam and follow-ups included, adds one and restarts the hour. |
| Anti-spam | 10559–10564 | `hp` or `website` not empty → `blockke_dev_note_spam()` and HTTP 200 `{ok:true}`, so a bot learns nothing. Nothing is emailed or saved as a lead. |
| Follow-up | 10566–10570 | A `more` parameter (the lead's `ref`) → `blockke_dev_lead_more()`. |
| Listing | 10572–10585 | Must be a published `estate_property`, else HTTP 400 "This listing is no longer available." (Area pages, the index and "topics" are other branches.) |
| Validation | 10586–10597 | `name` at most 80, at least 2 characters (400 "Please add your name."); `phone` through `blockke_dev_phone()` (400 "Please add a phone or WhatsApp number we can reach you on."); `email` dropped if not valid. |
| Other fields | 10598–10613 | `unit` ≤ 160; `purpose` ≤ 20; `timeline` only one of the offered answers; `contact` ≤ 20; `source`, `intent` `[a-z0-9_-]` ≤ 30; `message` `sanitize_textarea_field()` ≤ 1,500; `page` `esc_url_raw()` ≤ 300; `attr` as above. |
| Wording | 10690–10805 | A label for the form (`$forms`), the email's lines. v1.9.1: `$rent_lead` (10717–10732) switches to rental wording. |
| **Save** | 10808–10829 | `blockke_dev_save_lead()` with `zoho` "not sent" and `mail` false. **Before** Zoho and email. |
| Zoho | 10831–10864 | Below. |
| Email | 10866–10874 | Below. |
| Update the row | 10875–10881 | `blockke_dev_save_lead( array( 'zoho' => $zoho, 'mail' => $mail_ok ), $ref )`. |
| Answer | 10883–10892 | HTTP 200 `{ ok: true, phone_display: "0712 345 678", ref: "…20 characters…" }`, plus `asset` (the brochure URL) when the listing has one. |

### The lead row

`blockke_dev_save_lead( $fields, $ref = '' )` (10419–10435; identical in v1.8.2:8658–8674):

```php
$log = get_option( 'blockke_dev_leads', array() );
if ( ! is_array( $log ) ) { $log = array(); }
if ( '' === $ref ) {
	array_unshift( $log, $fields );                      // newest first
} else {
	foreach ( $log as $k => $row ) {                     // update the row with this ref
		if ( is_array( $row ) && isset( $row['ref'] ) && $row['ref'] === $ref ) { $log[ $k ] = array_merge( $row, $fields ); break; }
	}
}
update_option( 'blockke_dev_leads', array_slice( $log, 0, 200 ), false );   // cap 200, autoload off
```

| Key | Example | Note |
|---|---|---|
| `time` | `2026-11-11 14:05:33` | `current_time( 'mysql' )`, site time. The inbox sorts on it as text. |
| `listing` | `45577` | int; 0 for area, index and topic leads |
| `dev` | `Kings Courtyard` | the name the inbox shows; v1.8.2: the Project term, else the title; v1.9.1 rental: the page's short name |
| `unit` | `3 Bed` or `''` | ≤ 160 |
| `name` | `Jane Wanjiru` | ≤ 80 |
| `phone` | `254712345678` | digits |
| `email` | `jane@example.com` or `''` | |
| `purpose` | `Live in` or `''` | ≤ 20 |
| `timeline` | `Within 3 months`; rental: `1–3 months` | or `''` |
| `form` | `hero`, `modal/floorplan`, `contact`; other pages: `area page: hero`, `new developments: dev_index`, `land-build page: …` | source, then `/intent` when different |
| `channel` | `Google Ads` | |
| `source` | `Website - block.ke` | the Zoho lead source |
| `page` | `https://block.ke/property/…/?gclid=…` | ≤ 300 |
| `zoho` | `not sent`, then `ok (http 200)`, `skipped (no tokens)`, `error: …`, `http 500`, or `skipped (price-check popup snippet not active)` | |
| `mail` | `false`, then `true` / `false` | what `wp_mail()` returned |
| `ref` | 20 letters and digits | `wp_generate_password( 20, false, false )`; lets the visitor's follow-up answers find the row |
| `kind` | `rent` | **v1.9.1 only**, rental rows only (10828). v1.8.2 never writes it. |
| `more` | `2026-11-11 14:06:10` | added when follow-up answers arrive (10498) |

There is no lock around the read-modify-write, so two leads landing in the same instant can lose one; a second snippet writing the same option adds to that only marginally. The inbox (`ROOT/ref/inbox-v1.1.1.php:260–290`) reads `time`, `name`, `phone`, `email`, `dev`, `unit`, `purpose`, `timeline`, `form`, `channel`, `page`, `mail`, `zoho`, `kind` and `listing`; it labels the form "Development page ({form})" whatever wrote the row, and shows Type *Rental* when `kind` is `rent` (`inbox-v1.1.1.php:206–210`).

Option `blockke_dev_spam` (`blockke_dev_note_spam()`, 10438–10455): the last 30 honeypot hits, newest first, autoload off, each `time`, `name`, `phone`, `email`, `trap` (what was in the trap, ≤ 80), `page`.

### Emails

- **Recipients**: `blockke_dev_lead_to( $lid )` (10396–10416; identical in v1.8.2:8635–8655): config `notify_emails`; if empty, the popup's `blockke_bp_config()['notify_emails']` (its default is `loyd@block.ke`, `40752.php:49`); if still empty `loyd@block.ke`; plus, while config `copy_agent` is on (the default), the listing agent's `agent_email`. Sanitised and de-duplicated.
- **Subject**: v1.8.2 `[Block] Development enquiry: {name} - {dev}{ - unit}`; v1.9.1 rental `[Block] Rental enquiry: {name} - {dev}{ - unit}` (10868).
- **Headers**: `Content-Type: text/plain; charset=UTF-8`, and `Reply-To: {name} <{email}>` when an email was given (10869–10872). The name goes into the header as typed (single line, tags stripped); a comma or quote in it can make the header malformed.
- **Body**: the description, then `Reply on WhatsApp: https://wa.me/{phone}`, `Zoho: {status}`, and "SLA: first reply within 15 minutes; log the lead in Zoho under your name." (10873).

The description (10742–10806), v1.9.1 rental:

```
RENTAL ENQUIRY (property page)
Rental: Kings Courtyard - KES 135,000 a month
Interested in: -

Name: Jane Wanjiru
Phone / WhatsApp: 0712 345 678  (https://wa.me/254712345678)
Email: -
Move-in: 1–3 months
Best way to reach them: WhatsApp
Message: …

Form: Hero form (viewing)
Listing: 3 Bedroom Apartment for Rent in Kilimani, Lenana Road (Ref W-45577)
Page: https://block.ke/property/3-bedroom-apartment-for-rent-in-kilimani-lenana-road/
Channel (auto-tagged): Google Ads | campaign: …
UTM: google / cpc / term: …
gclid: …
Landing page: …
Referrer: …
Received: Wed 11 Nov 2026, 14:05 EAT
```

v1.8.2 writes the same lead for the same listing as `DEVELOPMENT ENQUIRY (property page)`, `Development: Kings Courtyard - from KES 135,000`, `Timeline:` and `Buying to:`. "EAT" is literal text after `wp_date( 'D j M Y, H:i' )`.

The follow-up note has the subject `[Block] More details: {name} - {dev}` (10503).

### The Zoho call

```php
// 10831-10864 (v1.8.2: from 9013, without the filter)
$zoho = 'skipped (price-check popup snippet not active)';
if ( function_exists( 'blockke_bp_send_zoho' ) ) {
	$zr = $rent_lead || $rent_area ? blockke_dev_zoho_rent_args() : null;   // v1.9.1
	if ( $zr ) { add_filter( 'http_request_args', $zr, 10, 2 ); }
	try {
		$zoho = blockke_bp_send_zoho( array(
			'lead_source' => $lead_source,
			'submarket'   => in_array( $sm, $submarkets, true ) ? $sm : ( $area ? 'Other' : '-None-' ),
			'buyer_type'  => 0 === strpos( $phone, '254' ) ? 'Local' : 'Diaspora',
			'name' => $name, 'phone' => $phone, 'email' => $email,
			'areas'       => $area ? array( $area ) : array(),
			'budget'      => '',
			'unit'        => trim( $dev_name . ( $unit ? ' - ' . $unit : '' ) ),
			'alerts'      => false,
			'channel' => $channel, 'campaign' => $campaign,
			'page'        => $page ? $page : get_permalink( $lid ),
			'page_title'  => $title,
			'description' => $description,
		) );
	} finally {
		if ( $zr ) { remove_filter( 'http_request_args', $zr, 10 ); }
	}
}
```

- `blockke_bp_send_zoho( $lead )` is the popup snippet's (`ROOT/ref/live-snippets/40752.php:287–350`). It needs **all** of these keys (it reads them without `isset`): `name`, `phone`, `email`, `areas` (array), `budget`, `unit`, `alerts`, `channel`, `campaign`, `page`, `page_title`, `description`, `lead_source`, `submarket`, `buyer_type`. It posts a Zoho web-to-lead form with a 12-second timeout and returns a string: `skipped (no tokens)`, `error: {message}`, `ok (http 200)` or `http {code}`.
- The result is stored in the row's `zoho` and printed in the email.
- `$submarkets` is a fixed list of 23 names (10833); `Upper Hill` is sent as `Upperhill`; an area not on the list is `Other`.
- v1.9.1 only, `blockke_dev_zoho_rent_args()` (10516–10535): a one-shot `http_request_args` filter that, for a post to the popup's own Zoho endpoint, changes `Company` from "Individual buyer" to "Individual tenant" and, when config `zoho_enquiry_field` names a field, sends it as "Rental". It is removed in `finally`.
- The call is synchronous: the visitor waits for Zoho (up to 12 s) and then for `wp_mail()`.

### The optional follow-up questions

- After a **quick** form succeeds, its thank-you panel shows `div.bkd-more[data-more]` (not a `<form>`, so analytics do not count a second submission): "Which home?" (only with several unit groups), "When would you like to buy?" and "Buying to"; v1.9.1 rentals: "When would you like to move in?" only (7856–7879).
- "Send these answers" posts `{ more: ref, listing, unit, timeline, purpose }` to the same endpoint (`sendMore()`, 9897–9907) and tracks `lead_details`.
- Server: `blockke_dev_lead_more( $ref, $get )` (10458–10506): finds the row by `ref` (`hash_equals`), refuses if the row already has `more` or is older than a day (HTTP 400 "Those answers could not be added, but your enquiry was received."), fills `unit` (if empty), `timeline`, `purpose`, sets `more` (10498), and emails the note. v1.8.2 validates `timeline` against the buyers' answers only, so a rental row's "This month" would be dropped there (`v1.8.2:8709–8711`).

### Thank-you and error states

- Success (`success()`, 9878–9895): the form is hidden and its sibling `.bkd-ok` shown and focused: a tick, "Thank you, {first name}.", "An advisor will be in touch shortly on {phone_display} {tail}." (the tail depends on the intent), the brochure button when the answer carries `asset`, the optional questions, and "Continue on WhatsApp" with a prepared message.
- Failure (`fail()`, 9908–9914): the button is enabled again and `div.bkd-alert[role="alert"]` is put before it: the server's message, or "Sorry, that didn't go through (error {status}).", then "Send it on WhatsApp instead" with the same prepared message.
- v1.9.1 server-drawn states after a no-script post: `blockke_dev_np_thanks()` (8063–8070) and `blockke_dev_np_alert()` (8073–8076).

### v1.9.1's no-JavaScript fallback (`forms_post`)

Off unless config `forms_post` covers `rent` (not set today); the classic rent box always uses it.

1. The form gets `method="post" action="{rest_url}block/v1/development-lead"` (`blockke_dev_np_form_attr()`, 8008–8010) and the hidden fields `bkd_np=1`, `listing`, `page` (this request's URL on the site's own host, `blockke_dev_np_page()`, 8025–8033), `source`, optionally `bkd_back` (8018–8022).
2. `blockke_dev_lead_route( $req )` (10904–10933): only a request whose content type is `application/x-www-form-urlencoded` **and** carries `bkd_np=1` takes this path; everything else goes straight to `blockke_dev_handle_lead()`. It maps `bkd_hp` to `hp`, "Not sure yet" to no unit, builds `attr` from the page's query if the form sent none, runs the normal handler, and answers **HTTP 303** with `Location:` the page plus `?bkd_sent=1` or `?bkd_err={rate|name|phone|gone|fail}` and `#bkd-enquire` (or `#bkd-rent`), with no-cache headers.
3. `blockke_dev_np_back()` (10951–10959) only redirects to the site's own host (scheme, host and port compared; `wp_validate_redirect()`); anything else goes to the home page.
4. On the returned page `blockke_dev_np_state()` (8036–8045) reads the result, the page is sent `nocache_headers()` and noindex (`blockke_dev_np_noindex()`, 8085–8106), and the form is replaced by the thank-you or shown again with the alert. The pop-up triggers become links to `#bkd-enquire` (`blockke_dev_np_triggers()`, 8079–8082).
5. The script still takes over when it runs: it sends its own JSON and ignores the hidden fields.

## (g) Similar homes

Entry point: `blockke_dev_similar_block( $d )` (4618–4734; `v1.8.2:3699–3809`), called once from the render function (8606) with the whole `blockke_dev_data()` array. It returns `null` when the listing's `bke_dev_similar_off` is set, hands land to `blockke_dev_similar_land_block()`, and otherwise returns the block below, from cache when it can.

### Which kind of block

```php
// 4643-4655
if ( 'master' !== $c['kind'] && '' !== $c['key'] && ! empty( $cfg['similar_units'] ) ) {
	$units = blockke_dev_similar_units( $d );
	if ( count( $units['rows'] ) >= $min || $picks( $units ) ) { $res = $units; }   // $min = config similar_min, 3
}
if ( ! $res ) {
	$res = blockke_dev_similar_devs( $d );
	if ( count( $res['rows'] ) < $min && ! $picks( $res ) ) {
		$res = $units && $units['rows'] ? $units : ( $res['rows'] ? $res : array( 'mode' => 'empty', 'rows' => array() ) );
	}
}
```

Modes: `units` (homes like this one), `devs` (developments, that is listings with sub-units), `empty` (a heading "No other 2-bedroom rentals in Kilimani listed right now" with two ways forward). `$c['key']` is the bedroom key from `blockke_dev_sim_key()`.

### Tiers

`blockke_dev_similar_units( $d )` (4348–4429), up to config `similar_count` (6) cards, filled tier by tier:

| Tier | What | Price band |
|---|---|---|
| 0 | The editor's "Similar listings (IDs)", `bke_dev_similar`, in their order (`blockke_dev_sim_picks()`, 4313–4340): published, same Buy/Rent, not sold or let | — |
| 1 | Same bedroom key, in the listing's main area | within ±25% (config `price_band`) |
| 2 | Same bedroom key, main area | any price |
| 3 | Same bedroom key, in the first ring of neighbouring areas | banded first, then any |
| 4 | One bedroom more or fewer, main area | within the band |
| 5 | Same bedroom key, second ring; only while fewer than `similar_min` cards were found | any |

Within a tier rows are ordered by `blockke_dev_sim_score()` (4304–4310): `abs( log( price / own price ) )`, plus half of the same for the size, plus 0.1 when one is off-plan and the other is not; ties go to the newest.

`blockke_dev_similar_devs( $d )` (4432–4488): tier 1 main area within ±35% of the "from" price (config `dev_price_band`), 2 main area at any price, 3 first ring, 5 second ring, 9 anywhere (the newest; for villas and houses the closest in price).

Neighbouring areas: config `neighbours` (327–347), a map `area slug => array( first ring slugs, second ring slugs )` for 18 areas; `blockke_dev_neighbours( $slug )` (3989–4009) returns the two rings as term ids.

### The query

`blockke_dev_sim_pool( $action, $area_ids, $key, $limit = 0 )` (4231–4301), one `WP_Query` per pool:

```php
'post_type' => 'estate_property', 'post_status' => 'publish', 'fields' => 'ids', 'no_found_rows' => true,
'posts_per_page' => $cap,                 // config pool_cap, 200
'orderby' => 'date', 'order' => 'DESC',
'tax_query' => array( 'relation' => 'AND',
	array( 'taxonomy' => 'property_action_category', 'field' => 'slug', 'terms' => array( 'rent' === $action ? 'for-rent' : 'for-sale' ) ),
	array( 'taxonomy' => 'property_status', 'field' => 'slug', 'terms' => array( 'sold', 'let' ), 'operator' => 'NOT IN' ),
	array( 'taxonomy' => 'property_category', 'field' => 'slug', 'terms' => array( 'commercial', 'offices', 'warehouses', 'land', 'agricultural', 'industrial-land', 'residential-land' ), 'operator' => 'NOT IN' ),
	array( 'taxonomy' => 'property_area', 'field' => 'term_id', 'terms' => $area_ids ),      // main area and both rings
),
'meta_query' => /* property_bedrooms = key (studio: '', 0, 1 or missing; '4': >= 4), and not a development */
```

After the query the few fields it needs are loaded for the whole pool with one SQL statement (`blockke_dev_pm_prime()`, 4019–4040), and rows whose price contradicts their tag are dropped: for rent, a price over KES 2,000,000, or a listing that is not rent-only and has no "month" in its label; for sale, a monthly label or a price under KES 1,000,000 that is not per sq ft (4290–4298). Each survivor becomes a row (`blockke_dev_sim_row()`, 4199–4219): `id`, `master`, `is_dev`, `project`, `dkey`, `key`, `price`, `flag`, `sqft`, `areas`, `offplan`, `house`, `date`.

### Exclusions

- The listing itself; its development and that development's other units; anything with the same Project term (4357–4360).
- Listings priced per sq ft (`flag` `unit`).
- When the listing is a villa or house, anything that is not (`blockke_dev_is_house()`, 4222–4224).
- One card per development (`dkey`); developments themselves in `units` mode (4375).
- In banded tiers: a missing price on either side, or an "up to" price on either side (4378).
- At pool level: sold and let listings, commercial and land categories, the other Buy/Rent.

Two consequences for rentals. **Commercial listings get nothing useful**: the pool excludes every commercial category, an office has no bedroom key, and the fallback is residential developments for rent in the area. Worse, `blockke_dev_sim_key()` files any listing with at most one bedroom and under 500 sq ft as a `studio` (4056–4057), so a small office is compared with studio flats. The rental page needs its own rule for commercial space.

### Caching

- Transient **`bkd_sim_{listing id}`**, holding `array( 'ver' => $ver, 'block' => $block )` (4631–4635, 4730–4732).
- Lifetime `max( 300, config sim_ttl )`: 43,200 s (12 hours).
- `$ver` = option `bkd_sim_ver` + `|v18|` + an md5 of the config keys that shape the block (4627); v1.9.1 appends `|v183r` for a rental with `rent_pages` on and the suffix of `blockke_dev_rent_ver()` (`|rp`, `|rc`) (4628–4630).
- Nothing deletes the transient. A stale one is replaced on the next view because its `ver` no longer matches.
- What changes `bkd_sim_ver` (`blockke_dev_sim_bump()`, 11006–11021, which writes `microtime( true )` now and again on `shutdown`): saving a published listing (11031–11040); any publish/unpublish transition (11041–11050); deleting a published listing (11051–11060); a change to a published listing's terms in one of seven taxonomies (11061–11073, list at 11027–11029); editing a term of those taxonomies (11074–11083); and adding, changing or deleting one of these meta keys on a published listing (11084–11096): `property_price`, `property_label`, `property_label_before`, `property_bedrooms`, `property_size`, `property_subunits_master`, `property_has_subunits`, `property_subunits_list`, `_thumbnail_id`, `bke_dev_similar`, `bke_dev_area`, `bke_completion_stated`, `bke_unit_label`, `prop_featured`, `bke_dev_layout`, `bke_index_line`, `wpestate_property_gallery`, `image_to_attach`, `bke_dev_kind` (v1.9.1 adds `bke_rent_usd`, `bke_rent_usd_date`). The view counter's two keys are not on the list, so page views do not flush anything.
- v1.9.1: an editor's rental preview stores nothing (4730).

### The block and the card markup

Block: `mode`, `title` (HTML: the area is wrapped in `<em>`), `all_url`, `all_nf` (add `rel="nofollow"`), `all_text` ("See all 3-bed rentals in Kilimani"), `cards`, `tier`, `beds`, `area` (4719–4729). Card: `id`, `url`, `img` (the featured image id), `name` (`blockke_dev_listing_name()`), `price`, `meta` ("3 bed · 153 m² · Kilimani", `blockke_dev_sim_meta()`, 4575–4593), `badge`, `tier`, `same` (1 when in the same area) (4706–4717).

```html
<!-- 9156-9170 -->
<section class="bkd-sec bkd-sim" id="bkd-similar" data-mode="units" data-tier="2" data-beds="3" data-area="Kilimani"><div class="bkd-wrap">
  <div class="bkd-sh bkd-sh-row bkd-rv"><div><p class="bkd-label">Also consider</p><h2 class="bkd-h2">Other 3-bedroom rentals in <em>Kilimani</em></h2></div>
    <a class="bkd-link bkd-sim-top" data-sim-all href="…">See all 3-bed rentals in Kilimani</a></div>
  <div class="bkd-cards" style="--n:3">
    <a class="bkd-card bkd-rv" href="…" data-id="45538" data-sim-pos="1" data-tier="1" data-same="1">
      <div class="bkd-card-img"><img …><span class="bkd-card-badge">Ready now</span></div>
      <h3 class="bkd-h3">Kings Courtyard</h3><b>KES 110K / month</b><span>2 bed · 111 m² · Kilimani</span></a>
    …
    <a class="bkd-card-all" data-sim-all href="…"><span>See all 3-bed rentals in Kilimani</span></a>
  </div>
  <div class="bkd-sim-act bkd-rv"><a class="bkd-btn bkd-btn-line bkd-btn-sm" data-bkd-search="similar" href="…">Refine search</a>
    <button class="bkd-btn bkd-btn-navy bkd-btn-sm" type="button" data-bkd-enquire="compare">Compare these with an advisor</button></div>
</div></section>
```

What differs for rentals between the versions (`blockke_dev_sim_price()`, 4595–4611; cards at 4706–4717): v1.8.2 prints the rent as "KES 110K / month" and keeps the sale badge "Ready now" on rental cards; v1.9.1 with `rent_pages` drops the badge; only with `rent_cards` on does the price read "KES 110K a month" and "Furnished" appear. None of these is the rental page's wording (project rule 12), so the cached block's `price` strings cannot be reused; its card ids could.

Tracking: `similar_view` once when half the grid is on screen, `similar_click` with position, target id, tier and same-area flag, `similar_see_all` (10117–10127).

## (h) CSS

`blockke_dev_css()` (9235–9759; `v1.8.2:7479–8003`) is one nowdoc of 56,738 bytes, byte-identical in both versions, printed inline as `<style id="bkd-css">` on `wp_head` 99. The rental snippet's budget is about 35 KB, so this is a reference, not something to copy.

### Design tokens

All custom properties are declared on `#bkd` (9237):

| Group | Tokens |
|---|---|
| Navy and text | `--navy:#0D2440`, `--navy-2:#16345C`, `--ink:#22303C`, `--slate:#46586A`, `--muted:#5F6D7E` |
| Paper and lines | `--paper:#FBF9F6` (page), `--mist:#F4EEE4` (alternate band), `--line:#E6DFD4`, `--line-2:#D5CBBB` |
| Brass | `--brass:#B98A44`, `--brass-d:#80602A` (small labels), `--brass-t:#9E7433` (italic accent in headings), `--brass-l:#DDB97F` (on navy), `--brass-tint:#F1E4CC` (status chip) |
| On navy | `--on-navy:#C9D3DF`, `--navy-line:rgba(255,255,255,.14)` |
| Signals | `--wa:#25D366` (the WhatsApp icon itself is drawn `#1DA851`, 9255), `--err:#B3261E` |
| Fonts | `--font:"Montserrat",system-ui,-apple-system,"Segoe UI",Roboto,sans-serif`; `--display:"Instrument Serif",Georgia,"Times New Roman",serif`; `--display-w:400`; `--display-ls:-.01em` |
| Layout | `--max:1200px`; `--gutter:20px` (40px from 768px, 9239); `--sec:clamp(80px,10vw,150px)` (section padding); `--top:0px` (set by script) |

Type and controls:

| | Value | Line |
|---|---|---|
| Body | `400 16px/1.75 var(--font)`, colour `--ink`, background `--paper` | 9238 |
| H1 | `clamp(46px,5.4vw,78px)`, line-height 1.02, display font | 9258 |
| H2 | `clamp(36px,4vw,56px)`, line-height 1.06, display font; `<em>` inside is italic in `--brass-t` | 9259–9260 |
| H3 | 18px, weight 500 | 9261 |
| Eyebrow label | 11px, weight 600, `.22em`, uppercase, `--brass-d`, with a 28px rule before it | 9262–9263 |
| Lead paragraph | `clamp(18px,1.6vw,21px)`, weight 300 | 9264 |
| Buttons | min-height 52px (small 42px), 12.5px, weight 600, `.12em`, uppercase, square corners | 9270, 9278 |
| Inputs | height 52px, 16px text, 1px `--line-2` border, square corners; focus: navy border and a 3px ring | 9341–9344 |
| Radio chips | min-height 42px | 9354 |
| Radii | 0 everywhere, except the status chip and the search pill (999px) and round avatars | 9299, 9628 |
| Focus | `outline:2px solid var(--navy); outline-offset:3px` on `:focus-visible` (brass on navy bands) | 9249 |

### How it protects itself from the theme

1. **Everything is scoped under `#bkd`.** 496 rules start with it; an id outranks the theme's class selectors.
2. **A soft reset in `:where()`**, which adds no specificity, so its own rules win over it without `!important` (9241–9248):

```css
#bkd :where(*,*::before,*::after){box-sizing:border-box}
#bkd [hidden]{display:none!important}
#bkd :where(h1,h2,h3,h4,h5,h6,p,ul,ol,li,dl,dt,dd,figure,blockquote,table,form,fieldset,legend,details,summary){margin:0;padding:0;border:0}
#bkd :where(ul,ol){list-style:none}
#bkd :where(img,svg,iframe){display:block;max-width:100%}
#bkd :where(a){color:inherit;text-decoration:none}
#bkd a:hover{color:inherit!important}
#bkd :where(button,input,select,textarea){font:inherit;color:inherit;letter-spacing:inherit;text-transform:none;margin:0}
```

3. **`!important` where the theme is known to push**: 286 uses. Headings' font family, colour and text-transform (9256); buttons' and links' padding, border, radius, font, letter-spacing, background, box-shadow and colour (9270–9282); inputs' border, radius, background, font and colour (9341); images that fill a box (`width:100%!important;height:100%!important;max-width:none!important`, 9305, 9448); link hover colour (9247).
4. **Full bleed inside the theme's container**: `#bkd{width:var(--bkd-vw,100vw);max-width:none;margin:0 0 0 calc(50% - var(--bkd-vw,100vw) / 2)}` (9238) with `body.bkd-page{overflow-x:clip}` (9240). The script sets `--bkd-vw` to `document.documentElement.clientWidth` on load and on resize, so the scrollbar's width is left out (9833–9834). Without script it falls back to `100vw`.

### Fonts

```php
// 1635-1645
function blockke_dev_font_head( $scope ) {
	$font = blockke_dev_display_font();          // config display_font, default 'Instrument Serif'; letters, digits and spaces only
	if ( '' === $font ) { return array( '', '' ); }
	return array(
		'<link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>'
			. '<link id="bkd-font" rel="stylesheet" href="' . esc_url( 'https://fonts.googleapis.com/css2?family=' . str_replace( ' ', '+', $font ) . ':ital@0;1&display=swap' ) . "\">\n",
		$scope . '{--display:"' . $font . '",Georgia,"Times New Roman",serif;--display-w:400}',
	);
}
```

- It returns the link tags and one CSS rule; the caller prints the links before its `<style>` (10293–10298). With `display_font` empty, headings fall back to the body font at weight 300 and smaller sizes (10294–10296).
- **The snippet loads only the display font.** Montserrat, the body font, is assumed to be on the page already. On the live site it comes from an `@import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap')` at the top of the Customizer's `<style id="wp-custom-css">` (`ROOT/ref/pages-final/45577.html:1491–1492`).
- The link id `bkd-font` is printed again by the classic-page CSS on classic pages (1765–1766), which is why the stand-down matters for a page that wants to load the same font once.

### Breakpoints

Counted in `blockke_dev_css()`: `min-width:1000px` (12 blocks: two-column hero and split sections, the section bar appears, the mobile bar goes), `min-width:900px` (3: gallery and facts grids), `min-width:768px` (the gutter), `min-width:760px`, `800px`, `min-width:700px` (5: dialog centred, breadcrumb), `min-width:640px` (4), `min-width:560px` (form fields side by side), `max-width:999px`, `899px`, `719px`, `699px`, `639px`, `559px`, `max-width:379px` (small phones: tighter buttons, icon-only tools), `(min-width:1000px) and (max-height:940px)` (a compact hero for short laptop screens, 9304), `prefers-reduced-motion:reduce` (9624: all animation and transition off, reveal shown), and `print` (twice).

### The sticky section bar

```css
/* 9379-9381 */
#bkd .bkd-subnav{position:sticky;top:var(--top);z-index:40;display:none;background:rgba(251,249,246,.97);backdrop-filter:blur(10px);border-top:1px solid var(--line);border-bottom:1px solid var(--line)}
@media (min-width:1000px){#bkd .bkd-subnav{display:block}}
#bkd .bkd-subnav-in{display:flex;align-items:center;gap:32px;height:64px}
```

- Desktop only (from 1000px), 64px high, `z-index:40`.
- `--top` is measured by script, `topOffset()` (9836–9851): it asks `document.elementsFromPoint( innerWidth / 2, 2 )`, walks up from each hit, and for every `position:fixed` or `sticky` element outside `#bkd` that starts within 2px of the top and is under 220px tall takes its bottom edge. So the bar sits under the admin bar and under whatever site header is stuck to the top at that moment. Re-measured at most every 250 ms while scrolling, and on resize (10136, 10152).
- Offsets that follow it: section anchors `scroll-margin-top:calc(var(--top) + 72px)` (9401); the sticky heading column of split sections `top:calc(var(--top) + 112px)` (9412).
- The active link is the last section whose top is above `--top + 120px` (10141–10143); the bar scrolls it into view when the links overflow.

### The mobile bar

```css
/* 9584-9588 */
#bkd .bkd-mb{position:fixed;left:0;right:0;bottom:0;z-index:990;display:grid;grid-template-columns:52px 1fr;gap:10px;padding:8px 16px calc(8px + env(safe-area-inset-bottom));background:#fff;border-top:1px solid var(--line);box-shadow:0 -6px 18px rgba(13,36,64,.08);transform:translateY(110%);transition:transform .3s}
#bkd .bkd-mb.on{transform:none}
@media (min-width:1000px){#bkd .bkd-mb{display:none}}
```

- Below 1000px; a 52px WhatsApp square and one button; `z-index:990`.
- It slides in (class `on`) once the hero's quick form has scrolled out of view, and hides while the enquiry section is on screen. **It also stays hidden whenever the theme's own agent bar `.mobile_agent_area_wrapper` is visible** (10132, 10140), so the two never stack.
- The Zoho SalesIQ chat button is moved out of its way: `html.bkd-mb-on #zsiq_float{bottom:calc(84px + env(safe-area-inset-bottom))!important}`, and hidden while a dialog is open: `html.bkd-lock #zsiq_float{display:none!important}` (9601–9602).
- Other layers: enquiry dialog and search sheet `z-index:999992` (9590), photo viewer `999993` (9604), the landing page's own header 45.
- v1.9.1's classic phone bar `.bkd-rbar` uses the same `z-index:990`, below 992px, and hides `.mobile_agent_area_wrapper` and `.blockke-cta-bar` there (2050–2052).

### Theme leftovers

The layout does **not** hide anything of the theme's. It does not need to for the theme's property sections, because the theme's property template never runs. What the theme header still prints (the property top navigation and the empty `header_media`, section (a)) is left as it is. Block's own property-page CSS in the theme options (`wp_estate_custom_css` in `ROOT/ref/theme-options.json`, 55,854 characters, all scoped `body.single-estate_property …`) still loads on these pages, since the body keeps that class; it has no rule for `#bkd`, `.bkd-page` or `bkr`.

Three of its rules reach into any layout drawn on a listing page, and explain some of the `!important`s above:

- `body.single-estate_property img { max-width: 100% !important; }`. This is why the layout's cover images say `max-width:none!important` (9305, 9448).
- Attribute-substring selectors: `[class*="property_description" i] p` (16.5px, colour, `max-width:68ch`, all `!important`), `[class*="feature_chapter" i]` (Space Grotesk 24px, `!important`), `[class*="feature_block"]`, `[id*="gmap"]` and `[class*="gmap_wrapper"]` (`border-radius:0!important`). So no class or id on the rental page may contain `property_description`, `feature_chapter`, `feature_block` or `gmap`.
- `body.single-estate_property ::selection { background: #0D2440.; color: #ffffff; }`. The background value has a stray full stop, so browsers drop that declaration and keep `color:#ffffff`: selected text turns white on the browser's own highlight colour. Cosmetic.

### Print

```css
/* 9687 */
@media print{#bkd .bkd-subnav,#bkd .bkd-mb,#bkd .bkd-md,#bkd .bkd-lb,#bkd .bkd-quick,#bkd .bkd-formcard,#bkd .bkd-map,#bkd .bkd-un,#bkd .bkd-hero-tools,#bkd .bkd-sim-act,#bkd .bkd-card-all,#bkd .bkd-alt{display:none!important}#bkd .bkd-rv{opacity:1!important;transform:none!important}}
```

Bars, dialogs, forms, the map and action links are dropped and the reveal animation is undone. Nothing else is print-specific (no page breaks, no link expansion).

## (i) Admin

All of this is identical in the two versions unless noted.

### The "Development page" box

```php
// 11099-11149 (v1.8.2:9205-9255)
add_action( 'add_meta_boxes_estate_property', function () {
	add_meta_box( 'bkd-meta', 'Development page', function ( $post ) {
		wp_nonce_field( 'bkd_meta', 'bkd_meta_nonce' );
		…
	}, 'estate_property', 'normal', 'default' );
} );
```

- Box id `bkd-meta`, title "Development page", on the `estate_property` edit screen, context `normal`.
- Nonce: action `bkd_meta`, field `bkd_meta_nonce` (11106).
- First line: what the layout is, with a "Preview it" link to the listing's URL plus `?bke_layout=1`, in a new tab (11109). Then, when landing pages are on and `blockke_dev_lp_ok()` allows, the listing's `?lp=1` link (11110–11113).
- A `form-table` with one row per field of `blockke_dev_fields()` (10980–11003). The field name is the meta key:

| Meta key | Label | Control |
|---|---|---|
| `bke_dev_layout` | Layout | select: `''` "Automatic (currently on/off)", `on` "On: development layout", `off` "Off: classic property page" (11121–11126) |
| `bke_dev_kind` | Listing kind | text (`land` or `homes`) |
| `bke_dev_tagline` | Hero line | textarea |
| `bke_dev_overview_heading` | Overview headline | text |
| `bke_dev_facts` | Key facts | textarea, "Label \| Value" per line |
| `bke_dev_deposit_pct`, `bke_dev_reservation`, `bke_dev_plan_months`, `bke_dev_calc_price` | Deposit %, Reservation fee (KES), Instalment period (months), Calculator price (KES) | number |
| `bke_dev_plan_until` | Instalments until (YYYY-MM) | text |
| `bke_dev_plan_text` | Payment plan text | textarea |
| `bke_dev_places` | Places nearby | textarea, "Place \| travel time" per line |
| `bke_dev_hero_label`, `bke_dev_gallery_note` | Main photo label, Gallery note | text |
| `bke_dev_brochure`, `bke_dev_video` | Brochure (PDF link), Video (YouTube or Vimeo link) | url |
| `bke_dev_progress` | Site progress | textarea, 10 rows, **administrators only** (`'cap' => 'manage_options'`) |
| `bke_dev_similar` | Similar listings (IDs) | text |
| `bke_dev_area` | Main area (slug) | text |
| `bke_dev_similar_off` | Hide "Also consider" | checkbox (a hidden empty input before it, so unticking posts `''`) |

- **Capability.** The box is shown to whoever can open the edit screen. A field with a `cap` is skipped for users without it, both when drawing (11116–11118) and when saving (11161).

### The save handler

```php
// 11151-11189 (v1.8.2:9257-9295), save_post_estate_property, default priority, one argument
if ( ! isset( $_POST['bkd_meta_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['bkd_meta_nonce'] ) ), 'bkd_meta' ) ) { return; }
if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || ! current_user_can( 'edit_post', $post_id ) ) { return; }
foreach ( blockke_dev_fields() as $key => $f ) {
	if ( ! isset( $_POST[ $key ] ) || ( ! empty( $f['cap'] ) && ! current_user_can( $f['cap'] ) ) ) { continue; }
	$raw = wp_unslash( $_POST[ $key ] );
	// select: 'on' | 'off' | ''        checkbox: '1' | ''         textarea: sanitize_textarea_field()
	// url: esc_url_raw( trim() )        number: (string) (float), '' when blank      text: sanitize_text_field()
	if ( '' === $v ) { delete_post_meta( $post_id, $key ); } else { update_post_meta( $post_id, $key, $v ); }
}
```

An empty value deletes the meta row, so "Automatic" is the absence of `bke_dev_layout`. Saving a published listing also bumps the cache version (section (g)).

### Admin-bar links and previews

`admin_bar_menu`, priority 90, front end only, on a single listing, for a user who can edit it (11191–11209; `v1.8.2:9297–9315`):

| Node id | Title | Link |
|---|---|---|
| `bkd` | "Development layout: on" / "off" (from `blockke_dev_layout_on()`) | the edit screen, `#bkd-meta` |
| `bkd-on` | Preview the development layout | `?bke_layout=1` |
| `bkd-off` | Preview the classic page | `?bke_layout=0` |
| `bkd-lp` | Open the ad landing page (only when allowed) | `?lp=1` |
| `bkd-edit` | Layout settings | the edit screen, `#bkd-meta` |

v1.9.1 adds, at priority 91, a `bkd-rent` group that reports where the classic rental blocks were placed (2450–2463), and, at priority 90, a node with the id **`bkr`** carrying the redirect dry-run note for `?bke_redir=1` (11295–11312).

How previews are offered: only through these links and the box's "Preview it" link. Every preview is a query parameter checked against a capability, `edit_post` for `bke_layout`, `edit_posts` for the rest: `bke_area`, `bke_cards`, `bke_index`, `bke_search`, `bke_vt` in both versions; `bke_rent`, `bke_rcards`, `bke_cls`, `bke_forms`, `bke_gal`, `bke_ld`, `bke_redir` in v1.9.1. There is no secret-key preview, and a preview response is not marked noindex or no-cache by the snippet (v1.9.1's rental previews only avoid *storing* caches, `blockke_dev_rent_preview()`, 4169–4171).

## (j) Anything else a second layout snippet must know

### Loading and names

- The whole file sits inside `if ( ! function_exists( 'blockke_dev_config' ) ) { … }` (262), so whichever version WPCode runs first wins and the others skip themselves. The README stages v1.9.1 at WPCode priority 3 and the live 42706 at 4 (`ROOT/ref/README-wordpress.md:408`). Where the rental snippet falls in that order is not fixed, so **test for the development snippet's functions inside hooks, never while the file loads**.
- Nothing in the snippet reads its config while the file loads; the first `blockke_dev_config()` call happens inside whichever hook needs it first (in v1.9.1 no later than `template_redirect` priority 0, 11282). The config is `array_merge( $defaults, get_option( 'blockke_dev_page' ) )`, a shallow merge, cached in a `static` (287–290, 424–429).
- Names in use. Admin-bar node ids: `bkd`, `bkd-on`, `bkd-off`, `bkd-lp`, `bkd-edit`, `bkd-rent`, `bkd-rent-*`, and **`bkr`** (11309). WordPress merges nodes that share an id, so the rental snippet's admin-bar node needs another id (`bkr-page`, say). Globals: `blockke_dev_active`, `blockke_bkn_vt_printed`, `blockke_dev_rent_rp`, `blockke_dev_cls_place`, `blockke_dev_cls_out`, `blockke_dev_cls_rent`, `blockke_dev_cls_near`, `blockke_bkn_rows_ver`, `blockke_area_rule`, `blockke_search_preview`, `blockke_cards_preview`. Script globals: `window.BKDEV`, `window.bknVT`, `window.bknNav`. sessionStorage: `bkd_lead`, `bkd_from`, `bkn_ctx`, `bkn_back`, `bkd_rent_lead_{id}`, plus the popup's `blkbp_attr`. Query parameters: `bke_layout`, `lp`, `blockpop`, `bkd_sent`, `bkd_err`, the `bke_*` previews in section (i), `bke_count`, and the list filters `beds`, `price`, `completion`, `sort`, `n`. `bkr` and `bkr_key` are free.

### Transients, options and what clears them

| Name | Holds | Lifetime | Lines |
|---|---|---|---|
| `bkd_sim_{listing id}` | the "Also consider" block (homes and land) | 12 h (`sim_ttl`) | 4631, 4731, 6877, 7005 |
| `bkd_area_{term id}` | an area page's data (v1.5 layout) | 12 h | 11587, 11711 |
| `bkd_search_lists` | areas and completion years for the search sheet | 12 h | 4779, 4803 |
| `bkd_area_pins` | v1.9.1: map points shared by several developments | 12 h | 3502, 3539 |
| `bkn_rows` | one row per published listing, for every card and list | kept a week, rebuilt daily or on a version change | 12624, 12705 |
| `bkn_build_lock` | "someone is rebuilding the rows" | 60 s | 12635, 12648, 12706 |
| `bkn_devs`, `bkn_index` | developments and the index page, built from the rows | 1 day | 12984, 13055, 13687, 13833 |
| `bkd_sim_cap_warn` | throttles a log line | 1 day | 4282–4283 |
| `bkd_rl_{md5(address)}` | lead rate limit | 1 h | 10552–10557 |
| `bkd_rl_v_{md5(address\|id)}` | view-count rate limit | 10 min | 17683–17686 |

Options: `blockke_dev_page` (settings), `blockke_dev_leads` and `blockke_dev_spam` (section (f)), and `bkd_sim_ver` (a `microtime` string, not autoloaded; every cache above that is built from listings carries it in its version and is rebuilt when it changes).

Hooks that bump `bkd_sim_ver` are listed in section (g) (11031–11096). For a second snippet this means: saving a listing from its own meta box bumps the version anyway (`save_post_estate_property`); writing its own custom field `bke_rent_layout` does not, because the meta hooks only react to a fixed list of keys; the view counter's two keys are not on that list either.

### The cards and list pages point at the listing's URL

- Every card on the index, search, area and segment pages is built from the cached rows (`blockke_bkn_row()`, 12743–12813). A row's `lo` flag says "this listing's page is the development layout" and is computed from the stored `bke_dev_layout`, config `auto` and `auto_units` (12805). It is read by SQL, so the stand-down filter does not change it.
- A card click writes `sessionStorage.bkn_ctx` = `{ id, n, path, from, label, src, beds, vt, im, k, y, t }` (13569–13572): `path` is the listing's pathname, `from` the list's URL, `label` the list's name, `t` the time. The development page reads it to offer "Back to {label}" when `path` is the current path, it is under 30 minutes old and the referrer matches (17529–17531). A rental page can read the same key the same way; it must not write it.
- While config `prefetch` is on (it is, live), list pages add speculation rules that **prefetch** a card's page when the pointer rests on it (13596–13599). The request carries `Sec-Purpose: prefetch`. So a rental page will be fetched without being seen: no side effects on GET, and do not count a view for a prefetch.
- Cross-page photo transitions need `@view-transition{navigation:auto}` on both pages. With step 2 of the recipe the rental page does not have it, so a card simply navigates.

### Structured data: one listing graph per page

Three things can describe a listing page:

1. Rank Math's graph (a BreadcrumbList on listing pages today).
2. The site's header script `script#bke-listing-schema` ("BKE listing schema v1.2"), which builds a `RealEstateListing` **in the browser** on every page whose body has the class `single-estate_property`, reading the price and facts out of the theme's markup, and appends it to the head as `script#bke-schema-ld`. It returns at once if an element with the id `bke-schema-ld` already exists (`ROOT/ref/pages-stop/45577.html:2927`, the guard at 3021, the append at 3124).
3. v1.9.1's server-side graph, printed with that same id, `script#bke-schema-ld`, at `wp_head` priority 2 when config `listing_jsonld` covers `rent` (8540–8550), with Rank Math's BreadcrumbList removed (8521–8538).

The rental page keeps the theme's body class, so (2) will run on it and find little to read (its fallback for the name is the first `h1`). To end up with exactly one listing graph: when v1.9.1 is going to print its own, let it (`function_exists( 'blockke_dev_ld_target' ) && blockke_dev_ld_target()` is non-zero exactly then; 8210–8220); otherwise print the rental page's JSON-LD in the head **with the id `bke-schema-ld`**, which makes (2) stand down. That id is an existing handshake between two pieces of the site, not a new name.

### Markup that other code looks for

Do not give any element of the rental page these ids, because scripts move content next to them or hide them: `wpestate_property_description_section`, `accordion_features_details`, `accordion_property_address`, `accordion_property_details_map`, `accordion_property_payment_calculator`, `accordion_property_schedule_tour` (1603–1606, 1803–1811, 2118–2119, 2232, 2348, 2005), and `bke-nearby` (an older Places script returns when it exists; v1.9.1 prints an empty `<template id="bke-nearby">` for that reason, 2099).

Classes with behaviour attached: `.mobile_agent_area_wrapper` (the theme's agent bar on phones; the layout's own bar yields to it, 10132) and `.blockke-cta-bar` (a script-built booking bar styled in the theme's custom CSS with `z-index:99999`; v1.9.1 hides both on classic rental pages, 2050). Whether either appears on a page drawn inside `get_header()` / `get_footer()` is in *Unknown*.

WhatsApp links: the development layout marks them `data-bkd-wa` for click tracking; v1.9.1's rent mode also adds `data-blkct="1"` and a "Ref W-{id}" in the text so that the site's contact-click snippet (40769) leaves them alone (8590–8600). v1.8.2 does neither.

### Things the layout does not do, which a new layout might assume it does

- It sends no cache headers of its own on a normal page, and changes no Rank Math output.
- It does not render the theme's `the_content`, comments, or sidebar, and it does not call `wp_reset_postdata()` after `the_post()`.
- It prints its description HTML unescaped by `wp_kses_post()` (8835) and its scripts as hand-written `<script>` tags (19 places; `wp_print_inline_script_tag()` is used nowhere). Both are things the rental snippet does differently by rule.
- It does not handle password-protected listings specially.

## Recommendations for the rental snippet

### 1. Take the page over the way the layout does, after standing it down

```php
// On `wp`: the main query is known, nothing has been printed, and it is before template_redirect.
add_action( 'wp', function () {
	$id = blockke_rp_target();                 // the rental snippet's own decision; 0 = leave the request alone
	if ( $id ) {
		blockke_rp_dev_stand_down( $id );      // section (b)
	}
} );

// Below 99998, so it runs before every template_include callback of the development snippet.
add_filter( 'template_include', function ( $template ) {
	$id = blockke_rp_target();
	if ( ! $id ) {
		return $template;
	}
	// body_class, wp_head (styles), wp_footer (script) hooks are added here, as closures
	// count the view, unless this is an editor, a preview or a prefetch
	if ( have_posts() ) {
		the_post();
	}
	get_header();
	echo blockke_rp_render( $id );
	get_footer();
	$blank = WP_CONTENT_DIR . '/index.php';
	return file_exists( $blank ) ? $blank : '';
}, 99990 );
```

- `blockke_rp_target()` should refuse what `blockke_dev_should_render()` refuses (10206–10213): the admin, feeds, embeds, anything but a single `estate_property`, and a request carrying `elementor-preview`, `vcv-editable`, `vcv-source-id` or `print`. Cache its answer in a `static`, since two hooks ask.
- For a preview response, the pattern v1.9.1 uses is ready to copy (`blockke_dev_np_noindex()`, 8085–8106): `nocache_headers()`, a `wp_robots` filter at 99 (`unset( $r['index'] ); $r['noindex'] = true; $r['follow'] = true;`) and a `rank_math/frontend/robots` filter at 99 (`$r['index'] = 'noindex'; $r['follow'] = 'follow';`). Add both before `get_header()`.
- Give the inline script the same opt-outs the layout uses, through `wp_print_inline_script_tag( $js, array( 'id' => 'bkr-js', 'data-cfasync' => 'false', 'data-no-optimize' => '1', 'data-no-defer' => '1' ) )`.

### 2. What to port (v1.9.1 line ranges)

| What | Lines | Change while porting |
|---|---|---|
| Text helpers: `blockke_dev_text`, `_trim_words`, `_sentences`, `_fit_sentences` | 436–481 | none |
| Money and size: `_money`, `_short`, `_parse_money`, `_num`, `_size` | 483–578 | currency from the rental snippet's own config |
| Phone: `_digits`, `_phone`, `_phone_display` | 580–608 | keep the `blockke_bp_normalize_phone()` call behind `function_exists()` |
| Description: `_content_html`, `_tokens`, `_sections`, `_find`, `_list_items`, `_split_kv`, `_table`, `_kv`, `_kv_get` | 765–965 | **replace the top-level rule**: match a heading at any level and read to the next heading as high (`_sub_section`, 3708–3732, shows how). Add "arrange a viewing" to the contact pattern (7569). Pass the description through `wp_kses_post()` before printing. |
| FAQ: `_faqs` | 3734–3813 | none |
| Places: `_place_row`, `_place_here`, `_places_override`, `_master_places` | 3551–3623 | none |
| "Who it suits": `_suits` | 3636–3661 | none |
| Features: `_feature_groups` | 2986–3021 | drop the icon if the design has none |
| Agent: `_agent` | 2469–2499 | none (project rule 14 is exactly this function) |
| Main area: `_main_area` | 3951–3986 | `get_post_meta()` instead of `blockke_dev_pm()` |
| Rent wording: `_rent_phrase`, `_rent_amount`, `_rent_name` | 7212–7285 | v1.9.1 only; this is the owner-approved wording |
| Rent terms: `_rent_info` | 7139–7186 | v1.9.1 only; tighten what counts as stated (section (d)) |
| "From" and "Up to" | 8568–8601, 1884–1894 | the logic, not the labels |
| What is left open: "The deposit, the lease length … are confirmed when you enquire." | 9009–9017 | check against the content rules |
| Tenant wording: Why Block, disclaimer, move-in answers | 276–285, 7829–7831 | check against the content rules |
| Lead handler: order of work, limits, messages | 10537–10893 | own route; save first; `'kind' => 'rent'` always; own subject |
| Lead storage and spam log: `_save_lead`, `_note_spam` | 10419–10455 | same options, same caps (200 and 30), autoload off |
| Follow-up answers: `_lead_more` | 10458–10506 | the tenants' answers only |
| Recipients: `_lead_to` | 10396–10416 | none |
| Zoho "Individual tenant": `_zoho_rent_args` | 10516–10535 | v1.9.1 only |
| No-script form: `_lead_route`, `_np_attr`, `_np_back`, `_np_page`, `_np_state`, `_np_errors` | 10904–10959, 8025–8056 | own marker, own return parameters (not `bkd_sent` / `bkd_err`) |
| View counter: `_count_view` | 10221–10238 | none |
| Visitor address behind Cloudflare: `blockke_bkn_is_cf`, `blockke_bkn_client_ip` | 17625–17664 | use it for the rate limit instead of the bare header at 10546 |
| Script: attribution `attr()`, `track()`, the three conversion calls | 9861–9868, 9935–9937 | keep the calls exactly; they are what the site's tags listen for |
| Script: photo viewer | 10032–10072 | add what it lacks if wanted (Back closes it, backdrop click) |
| Script: top offset probe and full-bleed width | 9833–9851 | none |
| Script: failed-image placeholders | 9853–9858 | none |
| CSS: tokens and the `:where()` reset | 9237–9249 | own prefix |
| Nearby places, if the design shows distances: `_place_kinds`, `_landmarks`, `_km`, `_km_label`, `_nearby` | 3362–3477 | 36 landmarks, about 2.4 KB |

### 3. What to call when it is there

All byte-identical in v1.8.2 and v1.9.1 (tokenizer comparison), except where noted. Check with `function_exists()` inside the hook that needs it, and keep the ported copy as the fallback.

| Function | Signature | Returns |
|---|---|---|
| `blockke_dev_config()` | `()` | array. Keys present in both versions and safe to read: `brand`, `brand_full`, `phone` (`+254725937686`), `phone_display` (`0725 937 686`), `whatsapp` (`254725937686`), `email`, `address`, `notify_emails`, `copy_agent`, `currency`, `neighbours`, `studio_cat`, `search_url`, `count_views`, `prefetch` (303–312, 317, 327, 350, 357, 391). |
| `blockke_dev_agent( $aid )` | agent post id | `array( 'name', 'role', 'photo', 'url', 'phone' (`+254…`), 'phone_display' (`07xx xxx xxx`), 'wa' (digits), 'email' )`; Block's defaults when the agent has no number (2469–2499). |
| `blockke_dev_lead_to( $lid )` | listing id | list of email addresses: the notify list plus the listing's agent (10396–10416). |
| `blockke_dev_save_lead( $fields, $ref = '' )` | a row; or fields and the `ref` of the row to update | nothing. Writes option `blockke_dev_leads` (10419–10435). |
| `blockke_dev_count_view( $id )` | listing id | nothing. Skips editors, previews and prefetches (10221–10238). |
| `blockke_dev_main_area( $id )` | listing id | `WP_Term` or `null` (3951–3986). |
| `blockke_dev_search_url( $q )` | `array( 'area' => name, 'action' => 'rent', 'beds' => '3' \| 'studio' \| '' )` | a URL into the site search, e.g. `/advanced-search/?location=Kilimani&property_action_category%5B%5D=for-rent&componentsbeds=3` (4527–4544). |
| `blockke_dev_seg_url( $url )` | a URL from the function above | the matching area segment page while the v1.6 area pages are on, else `''` (4553–4572). |
| `blockke_dev_nearby( $lat, $lng )` | two numbers | `array( array( 'label', 'icon', 'places' => array( array( name, km, kind ), … ) ), … )`, or `array()` without a usable pin (3433–3477). v1.9.1 accepts a third argument; call it with two. |
| `blockke_dev_km_label( $km )` | number | `"2.4 km"` or `"12 km"` (3423–3425). |
| `blockke_dev_places_override( $id )` | listing id | `array( array( place, value ), … )` from `bke_dev_places` (3602–3614). |
| `blockke_bkn_client_ip()` | `()` | the visitor's address: `CF-Connecting-IP` only when the request came from a Cloudflare range, else `REMOTE_ADDR` (17660–17664). |
| `blockke_bp_send_zoho( $lead )` (popup snippet) | array with **all** of `name`, `phone`, `email`, `areas`, `budget`, `unit`, `alerts`, `channel`, `campaign`, `page`, `page_title`, `description`, `lead_source`, `submarket`, `buyer_type` | a status string (`40752.php:287–350`). |
| `blockke_bp_channel( $attr )`, `blockke_bp_lead_source( $channel, $attr )` (popup) | the attribution array; the channel and the array | strings (`40752.php:212–284`). |
| `blockke_bp_config()` (popup) | `()` | array; `notify_emails`, `privacy_url`, `zoho['endpoint']`. |
| `blockke_dev_ld_target()` (**v1.9.1 only**) | `()` | the listing id when v1.9.1 will print its own JSON-LD on this request, else 0 (8210–8220). |

### 4. What to avoid

- **Setting only the global.** It prints the view-transition head with a render-blocking `rel="expect"` on the live site, and it does not stop the layout.
- **Calling** `blockke_dev_data()`, `blockke_dev_render()`, `blockke_dev_similar_block()` or any `blockke_dev_rent_*()`: they differ between the versions, the first is 459 lines of mostly sale logic per call, and their wording is not the rental page's.
- **Posting to `block/v1/development-lead`.** v1.8.2 would file a rental as a development enquiry with no `kind`, and would drop the tenants' move-in answers.
- **The top-heading-level rule** in `blockke_dev_sections()`: it loses every section of 18 of the 129 rentals, three fixtures among them.
- **The amenity tiles' sentences** (3081–3107): "Movie nights without the Nairobi traffic.", "Game on, without driving to the club.", "Weekend nyama choma with family and neighbours." They are claims that do not come from the listing (project rules 11 and 15).
- **`blockke_dev_rent_info()` as it stands**: it passes "For rent", "To let", "Contact Block Real Estate …" as an availability and a deposit sentence as a lease (section (d)).
- **Similar homes as built**: nothing for commercial space, a small office is treated as a studio, and the cached `price` strings read "/ month".
- **Names**: `bkd-*`, `bkn-*`, `BKDEV`, the sessionStorage keys and query parameters in section (j), the admin-bar node id `bkr`; element ids `wpestate_property_description_section` and the `accordion_*` ids; class or id text containing `property_description`, `feature_chapter`, `feature_block` or `gmap`.
- **Photos that exist only in script data** (photos 6 and up on the layout), and `<button>` openers with no link behind them.
- **Size `full` as the main photo's `src`**; ask for `large` or `1536x1536` and let `srcset` do the rest.
- **The raw visitor name in `Reply-To`** (10871) and **the unverified `CF-Connecting-IP`** for the rate limit (10546).
- **Hand-written script tags and patterns that match script or style tags** (2257, 5049, 8224): the firewall rejects them, and project rule 5 forbids them.
- **`the_content` filters** for the description: the layout's `blockke_dev_content_html()` avoids them for a reason.

### 5. The stand-down recipe

The function in section (b), unchanged: on `wp`, for the listing about to be rendered,

1. `$GLOBALS['blockke_dev_active'] = true;`
2. `$GLOBALS['blockke_bkn_vt_printed'] = true;`
3. a `get_post_metadata` filter that returns `array( 'off' )` for `bke_dev_layout` of that listing;
4. `unset( $_GET['bke_layout'], $_REQUEST['bke_layout'] );`
5. `$_GET['blockpop'] = '0';`
6. optionally, lower the global for `wp_head` priority 100 only (two callbacks added during `wp_head` 98).

Then, for JSON-LD, print the rental page's own graph with the id `bke-schema-ld` unless `blockke_dev_ld_target()` exists and returns the listing.

Cases to repeat on the rig, each against v1.8.2 and against v1.9.1 with `rent_pages` true and `classic_inplace` `['rent']`, and with `view_transitions` on: (a) `bke_dev_layout` = `on`, `off` and empty; (b) config `auto` on with a rental that has sub-units; (c) a visitor, and an editor with `?bke_layout=1`; (d) the rental snippet loaded before and after the development snippet; (e) v1.9.1 with `?bkd_sent=1` in the URL. Pass: one `<header>` of the site, one `#bkr`, and the page source contains neither `bkd` nor `bkn-` (with step 6), or only `<style id="bkn-dev-css">` (without it).

## Unknown, and how to find out

| Unknown | How to find out |
|---|---|
| Whether the theme's property top navigation (`div.wpestate_top_property_navigation`, printed by `get_header()` on every listing, `pages-final/45577.html:4627`) is **visible** on a page drawn without the theme's property template, and whether `.mobile_agent_area_wrapper` and `.blockke-cta-bar` appear there. If they do, the rental page must hide them, because their links and buttons point at theme sections that are not on the page. | Open a development-layout page of the rig (or a live one, read-only) in a browser at 390px and 1280px, scroll, and look; or read the theme's `wpestate_property_sticky()` and the script that builds `.blockke-cta-bar` (site-shell and live-snippets notes). |
| Where the duplicated `fetchpriority="high"` and `loading="lazy"` attributes on the live markup come from (`pages-final/45577.html:4640`): WordPress 7.1.4's own loading optimisation, or an optimiser that rewrites the HTML (the page also carries `data-wpr-language`). | On the rig, print `wp_get_attachment_image( $id, 'full', false, array( 'fetchpriority' => 'high', 'loading' => 'eager' ) )` and look at the tag; compare with the live plugin list. |
| The live option `blockke_dev_page` beyond `rent_pages` and `classic_inplace`. From the saved pages: `view_transitions` and `prefetch` are on. Not known: `auto`, `auto_units`, `card_v16`, `dev_index`, `search_layout`, `notify_emails`, `copy_agent`, `count_views`. | One read of the option by the main session. `auto` matters most: with it on, a rental that has sub-units gets the layout without any custom field (the recipe covers it either way). |
| Whether a `let` term exists in `property_status`, and how staff mark a rental that has been let. The code tests the slug `let`; the public feed shows only `Sold` (2 listings). | List the taxonomy's terms once (main session), or the data audit. |
| Whether listing pages are cached at the edge or by a page cache. It decides how often the view counter and any per-request logic run, and whether a no-script form's return page can be served stale. | Response headers of a listing (`cf-cache-status`, `x-cache`), site-shell notes. |
| Whether `Sec-Purpose: prefetch` reaches PHP through Cloudflare, so that prefetches can be told from visits. | Log `$_SERVER['HTTP_SEC_PURPOSE']` on the rig behind a proxy, or check on the live site with the owner's consent. |
| Whether the popup snippet's Zoho tokens are set, so that `blockke_bp_send_zoho()` really posts (it returns `skipped (no tokens)` otherwise). | The `zoho` column of the Enquiries inbox for recent leads. |
| The harness is not WordPress. Section (b)'s table is from WordPress 7.1.4's hook classes with every data function stubbed. | Repeat the cases listed in recommendation 5 on the rig. |
| The description findings in section (d) come from the theme's rendering of each description in the saved classic pages, not from `post_content` passed through `blockke_dev_content_html()`. Headings and tables should be the same; block wrappers may differ. | Re-run `parse-check.php` (path in section (d)) on the rig against the stored `post_content` of the 18 listings. |
