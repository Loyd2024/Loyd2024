<?php
/* marker line - keeps the connector from Markdown-converting this snippet; harmless in PHP */
/* <div> markup marker - the connector stores content as-is only when it sees a block-level HTML tag; harmless in PHP */
/**
 * BLOCK - Homepage v2.2.1 (2026-11-03) = v2.2 + the original homepage's family picture as the hero, and "Our promise" over the skyline
 * v2.2.1 (the owner's three points):
 *   1) Hero photo: attachment 37801, the original homepage's header photo, taken to be the family picture the owner asked for (config
 *      hero_image; confirm on the preview), with no caption (config hero_caption '') and config hero_focus as the photo's
 *      object-position (default "50% 50%"; "center bottom" shows it as the original page did). The photo could not be measured, so
 *      the scrim alone gives every hero text AA over a pure-white photo too: a light veil over the whole photo and one soft pool of
 *      navy behind the text only; the eyebrow is the pale brass, and the search box's focus ring has a navy edge.
 *   2) "Our promise": the services band opens with Block's promise (config promise_title, "An excellent-service powerhouse."), a
 *      two-sentence statement, "Meet the team" (/our-team/) and a thick brass bar, on the grid of v2.2's four service cards, which
 *      follow with the valuation band, over the owner's Nairobi skyline (config services_image 45212).
 *   3) Carousel: a card reached with the keyboard lands wholly in view with reduced motion, or a mouse resting over the row, too.
 * BLOCK - Homepage v2.2 (2026-11-02) = v2.1.2 + a Nairobi hero, one search box and the sections below redrawn
 * v2.2 (the owner's eight points, after serhant.com's homepage):
 *   1) Hero photo: config hero_image takes an attachment id (the Nairobi skyline, once uploaded) or a URL; with "{w}" in the
 *      URL it names copies at config hero_widths for a srcset. It loads first (fetchpriority high, its width and height set),
 *      under a navy scrim strong enough for the white text over the photo's brightest part. Caption "Nairobi" (while
 *      hero_image is still the old render 40994, that render's own caption).
 *   2) One search box in place of v2.1's filter card and name strip: Buy | Rent | Off-plan (radio buttons, so the mode is sent
 *      without the script too), one field for an area, a development or an estate, Search, and "More filters" (the search page
 *      for the mode). The script's typeahead offers areas (property_area, with their listings in the mode) and developments
 *      (v2.1's names index, matching and input safety). Enter goes to a clear area's page for the mode (its homes for sale or
 *      to rent on the area pages; Off-plan: the new developments index for that area), or to a clear development, else to
 *      the ?bkdev= results panel, which now lists the areas that match too; without the script the form asks the homepage
 *      and the server renders that panel. No "Popular:" links in the hero.
 *   3) Neighbourhood grid under the proof strip: one text button per area with listings, with its live count, linking to
 *      the area page; the busiest config area_grid (20), A to Z.
 *   4) Everything after the hero sits on a paper panel with large rounded top corners over the foot of the hero.
 *   5) Developments as a carousel of tall cards (config developments first, then off-plan developments on the new layout
 *      with a large featured image, featured first): name and area over a dark fade; on screens with a mouse the card pointed
 *      at or focused widens to show "From KES ... · Completion ..." (what the data has) and "Prices & floor plans", on touch
 *      screens every card shows them. Arrows (script), "All off-plan projects", a Play link only from config videos.
 *   6) "Worth a viewing this week." on near-black: tall photo cards (main photo at least config listing_min_px), price,
 *      title, place, a specs line and a status tab (Off-plan | Ready | To rent, from the listing). Grids are whole rows.
 *   7) The diaspora band adds, when the stored website enquiries (option blockke_dev_leads) clear config diaspora_min, the
 *      number of countries besides Kenya they came from and the share from outside Kenya (by the phone number's country
 *      code, over the last config diaspora_days), their top countries and a dotted world map (inline SVG). Counts only,
 *      cached in a transient; below the minimums, v2.1's band unchanged.
 *   8) "A service for every move." as four cards (config services) over one full-width photo (config services_image).
 *   Every function is blockke_hm22_* and every transient blockke_home_v22_*, so this snippet and 45184 never shadow each other.
 * v2.1.2: the same page as v2.1.1, with every function renamed from blockke_home_* to blockke_hm21_*. Snippet 44128
 * (Home v2.0.2, same function names) sits in WPCode's run list (option wpcode_snippets) under "everywhere", so WPCode
 * runs it on every request at plugins_loaded, before WPResidence registers its taxonomies. Its early run cached the
 * counts as 0 / 0 / 1 and defined the blockke_home_* functions first, so v2.1.1's own code was skipped and the front
 * page kept rendering v2.0.2. Under its own names this code cannot be shadowed. Cache keys keep their form
 * (blockke_home_v2_*); the version inside the stamp keeps them apart from v2.0.2's.
 * v2.1.1: the counts under the hero (homes for sale, developments, neighbourhoods) are never stored or shown when
 * WPResidence's taxonomies are not registered in that request or a count comes back empty or as an error. Such a count is
 * left out rather than shown as 0, and the page itself is then not stored in the page cache either.
 * v2.1 adds, under the Buy | Rent | Off-plan filters in the hero, "Know the development?": its own small GET form
 * (?bkdev=<name>, posted to the homepage) with a server-rendered <datalist> of every development name, so it works
 * without JavaScript. The page script turns it into a typeahead (ARIA 1.2 combobox + listbox): up to 8 developments
 * with area and price as you type, Rent mode preferring developments with rentals; choosing one, or submitting an exact
 * or single clear match, goes straight to that development's page. Otherwise ?bkdev= renders a results panel under the
 * search (up to 12, best first; no match says so and offers the closest names, the main developments, all new
 * developments and WhatsApp). Names come from snippet 42706's developments (blockke_bkn_devs(): homes, land and
 * commercial; Project names that read differently are aliases), else from property_project terms with listings.
 * Matching ignores case, accents, punctuation and filler words (apartments, residence(s), residency, homes, estate, the,
 * at), reads "&" and a lone "n" between words as "and" ("Oak n Ivy"), and leaves out the development's own area typed with the name
 * ("Capella Westlands", "Capella W"); a near miss (one slip, a swap of two letters included, c for k) is offered only
 * when nothing else matches, and when it is the only one and every word typed is a slip of a word of the name
 * ("capela", "golden hills") it too goes straight there; letters inside a word ("commUNITY") are offered only when no
 * name starts with them. Equal matches go to the mode's preference, then to the homepage's own developments (config
 * developments: "gol" puts Golden Hill first), then to the shorter name. Opened upwards, the list never covers the
 * Buy | Rent | Off-plan tabs or leaves a sliver of the filters above it; where it fits neither way, typing scrolls the
 * page just enough to open it below. A click into the box never moves the page while the button is down (the list
 * opens where it fits and is fitted once the click is over), and the mouse highlights an option only when it really
 * moves. The text shown back, sent to GA4 and carried in the address is cleaned of web and e-mail addresses, @handles
 * and numbers of 5 or more digits. Pages with ?bkdev= are never read from or stored in the page cache (on a page that
 * is not the front page, such as a preview, the search posts to that page and its cached copy has its own key). While
 * it has a copy under 12 hours old, the names index waits for 42706 to rebuild its own developments after a listing
 * change (its development pages do), so the homepage does not pay for that rebuild. GA4 'search' fires with placement
 * home_dev. Config 'dev_search' => false hides it. The matching, the panel's wording and the input safety are Home
 * v3.1's, under this snippet's own names (blockke_hm22_dev_*); the look is v2's: a slim rounded card under the filter
 * card, a rounded list and panel, Montserrat labels and Instrument Serif prices.
 * v2.1 also shows no "Authorised sales agent" tag: config 'mandates' is empty by default (the live option already
 * says so; the mandate code is otherwise unchanged), and the developments lede no longer says "On some we sell
 * directly for the developer." Everything else is v2.0.2 unchanged: where the hero is at its minimum height (screens
 * 768px and wider) it grows by the strip's height (70px with its margin), so nothing above the strip moves, and from
 * 901px the Find button takes the width of the Search button above it, which keeps its own.
 * The block.ke homepage, rendered by this one snippet where the shortcode [wpcode id=<this snippet>] is placed
 * (one Elementor Shortcode widget in a full-width container, the same wiring as the blog hub on /blog/).
 * Everything on the page comes from the site itself, so it stays current without edits:
 *   1) Hero: the photo, one statement and the one search box (areas, developments, estates; Buy | Rent | Off-plan).
 *   2) On the panel: the proof strip, live counts (homes for sale, developments tracked, Nairobi neighbourhoods) +
 *      same-day reply, then the neighbourhood grid (areas with their live listings).
 *   3) New developments: a carousel of the projects in config 'developments' (property_project slugs), each built from
 *      the project's master listing (property_has_subunits) and its unit listings (lowest price, completion, photo;
 *      links to the project's ad landing page, a page with bke_lp_listing, when one exists, else the listing), then other
 *      off-plan developments on the new layout.
 *   4) Worth a viewing: featured listings (prop_featured) topped up with the newest, For sale | For rent, on near-black.
 *   5) Diaspora band (with numbers from the website's enquiries when there are enough), 6) Know your block (property_area
 *      tiles with live counts and the term images), 7) Loyd from Block (founder + team from estate_agent), 8) Services over
 *      a photo, 9) The homework (latest articles), 10) Closing CTA, plus a WhatsApp | Call bar on phones.
 * Conversion: every WhatsApp / call tap is logged to the existing POST /wp-json/block/v1/contact-click
 * endpoint (placement home_*), so it appears in the weekly contact report, and fires GA4 contact_click +
 * Meta Contact. Searches fire GA4 'search'.
 * Cache: the rendered HTML is kept in a transient (blockke_home_v22_<stamp>) for up to an hour; the stamp changes
 * whenever a listing, article or agent is saved or the config option changes, so edits show on the next load.
 * ?bkhome_nocache=1 bypasses it. Cloudflare may still serve an older copy until purged.
 * Config: option blockke_homepage (array) overrides any key of the defaults in blockke_hm22_config().
 * Kill switch / rollback: deactivate this snippet in WPCode, or set the front page back to the previous layout.
 */

if ( ! function_exists( 'blockke_hm22_config' ) ) {

	function blockke_hm22_ver() {
		return '2.2.1';
	}

	function blockke_hm22_config() {
		static $cfg = null;
		if ( null !== $cfg ) {
			return $cfg;
		}
		$defaults = array(
			'hero_image'     => 37801,  // v2.2.1: the original homepage's header photo (attachment 37801), the family picture; v2.2: an attachment id or a URL ('{w}' = one of hero_widths)
			'hero_widths'    => array( 480, 768, 1024, 1536, 1920 ), // v2.2: the copies a hero_image URL with {w} has
			'hero_size'      => array( 1920, 1080 ), // v2.2: a hero_image URL's width and height (no layout shift)
			'hero_image_phone' => 0,    // v2.2: optional portrait crop of the hero photo for phones held upright (attachment id or URL); 0 = none (v2.2.1: no upright crop of 37801 yet)
			'hero_phone_widths' => array( 540, 810 ), // v2.2: the copies a hero_image_phone URL with {w} has
			'hero_phone_size' => array( 810, 1080 ), // v2.2: a hero_image_phone URL's width and height
			'hero_caption'   => '',     // v2.2.1: none (the family picture is not a place); text = that caption; null = none too (40994 keeps its render caption)
			'hero_focus'     => '50% 50%', // v2.2.1: the hero photo's CSS object-position ("center bottom" shows 37801 as the original page did)
			'hero_eyebrow'   => 'Nairobi · Residential & Commercial',
			'hero_title'     => 'We know Nairobi block by block.',
			'hero_sub'       => 'Homes for sale, rentals and new developments — every one checked before you view, and negotiated on your side.',
			'developments'   => array( 'golden-hill', 'capital-heights', 'santorini-residences', 'gaia-brookside', 'astra-bay', 'mara-grove-apartments' ),
			'mandates'       => array(),                // v2.2: not shown (the carousel has no mandate tag); kept so the live option still reads
			'dev_link'       => 'lp',                  // 'lp' = ad landing page when one exists, 'listing' = always the listing
			'neighbourhoods' => array( 'westlands', 'kilimani', 'kileleshwa', 'lavington', 'riverside', 'parklands', 'runda', 'karen', 'syokimau', 'tatu-city' ),
			'listings_count' => 6,
			'founder_image'  => 36538,
			'founder_agent'  => 27447,
			'team_agents'    => array( 35365, 35366, 35367 ),
			'whatsapp'       => '254725937686',
			'phone'          => '+254184027349',
			'phone_label'    => '0184 027 349',
			'wa_text'        => "Hi Block — I'm on block.ke and I'd like to talk about ",
			'cache_minutes'  => 60,   // the key also changes whenever a listing, article or agent is saved
			'dev_search'     => true, // v2.1: development names in the search (false: areas only)
			'area_grid'      => 20,   // v2.2: neighbourhood buttons under the proof strip (the busiest, A to Z); 0 hides the grid
			'carousel_count' => 10,   // v2.2: development cards in the carousel (config developments first, then filled)
			'carousel_min_px' => array( 1000, 700 ), // v2.2: smallest featured image (width, height) a filled card may have
			'videos'         => array(), // v2.2: property_project slug => video URL: a Play link on that card (none today)
			'listing_min_px' => array( 900, 600 ), // v2.2: smallest main photo (width, height) for a "Worth a viewing" card
			'diaspora_stats' => true, // v2.2: numbers from the website's stored enquiries (options blockke_dev_leads, blockke_buyer_leads, blockke_consult_leads); false = v2.1's band
			'diaspora_days'  => 365,  // v2.2: the period they cover
			'diaspora_min'   => array( 'enquiries' => 30, 'abroad' => 5, 'countries' => 3 ), // v2.2: below any of these, no numbers
			'services_image' => 45212, // v2.2.1: the owner's Nairobi skyline (attachment 45212) behind "Our promise"; v2.2: an attachment id or URL; 0 = navy; navy too while it is the hero's photo
			'promise_title'  => 'An excellent-service powerhouse.', // v2.2.1: the title of "Our promise"
			'services_caption' => null, // v2.2: null = "Project render" while services_image is 40994, else none
			'services'       => array( // v2.2: the four cards: title, text, a key of urls (or a URL)
				array( 'Block Advisory', 'Off-plan and investment advisory: which project, which unit, which payment plan &mdash; and which to walk away from.', 'build' ), // the Block Advisory page
				array( 'Block Construction', 'Client-side project management of your build, through Block Advisory.', 'construction' ),
				array( 'Block Architecture and Design', 'Architecture and interiors, through Block Advisory.', 'design' ),
				array( 'Property Management', 'Rent collection, repairs, inspections, statements &mdash; the returns without the 2am calls.', 'management' ),
			),
			'urls'           => array(
				'search'       => '/advanced-search/',
				'for_sale'     => '/for-sale/',
				'for_rent'     => '/for-rent/',
				'offplan'      => '/new-developments/offplan-projects-for-sale-in-nairobi/',
				'neighbourhoods' => '/neighbourhoods/',
				'diaspora'     => '/diaspora/',
				'diaspora_guide' => '/market-insights/how-to-buy-property-in-kenya-from-the-diaspora-2026-the-complete-step-by-step-guide/37607/',
				'team'         => '/our-team/',
				'consult'      => '/get-a-consultation/',
				'sell'         => '/sell/',
				'valuation'    => '/home-valuation/',
				'management'   => '/property-management/',
				'advisory'     => '/investment-advisory/',
				'build'        => '/block-advisory/', // the Block Advisory page
				'construction' => '/block-advisory/construction/', // v2.2: the Block Construction page
				'design'       => '/block-advisory/architecture-design/', // v2.2: the Block Architecture and Design page
				'blog'         => '/blog/',
				'contact'      => '/contact-us/',
			),
		);
		$saved = get_option( 'blockke_homepage', array() );
		if ( ! is_array( $saved ) ) {
			$saved = array();
		}
		$cfg = array_merge( $defaults, $saved );
		if ( isset( $saved['urls'] ) && is_array( $saved['urls'] ) ) {
			$cfg['urls'] = array_merge( $defaults['urls'], $saved['urls'] );
		}
		return $cfg;
	}

	/* ------------------------------------------------------------------ helpers */

	function blockke_hm22_url( $key ) {
		$cfg = blockke_hm22_config();
		$u   = isset( $cfg['urls'][ $key ] ) ? $cfg['urls'][ $key ] : '/';
		if ( 0 === strpos( $u, 'http' ) ) {
			return $u;
		}
		return home_url( $u );
	}

	function blockke_hm22_wa( $topic ) {
		$cfg = blockke_hm22_config();
		return 'https://wa.me/' . preg_replace( '/\D+/', '', $cfg['whatsapp'] ) . '?text=' . rawurlencode( $cfg['wa_text'] . $topic );
	}

	/** KES price in M notation for sales, K a month for rentals. */
	function blockke_hm22_price( $n, $rent = false ) {
		$n = (float) $n;
		if ( $n <= 0 ) {
			return '';
		}
		if ( $rent ) {
			if ( $n >= 1000000 ) {
				return 'KES ' . rtrim( rtrim( number_format( $n / 1000000, 2 ), '0' ), '.' ) . 'M a month';
			}
			if ( $n >= 1000 ) {
				return 'KES ' . rtrim( rtrim( number_format( $n / 1000, 1 ), '0' ), '.' ) . 'K a month';
			}
			return 'KES ' . number_format( $n ) . ' a month';
		}
		if ( $n >= 1000000 ) {
			$m = $n / 1000000;
			$s = ( $m >= 100 ) ? number_format( $m, 0 ) : rtrim( rtrim( number_format( $m, ( $m < 10 ) ? 2 : 1 ), '0' ), '.' );
			return 'KES ' . $s . 'M';
		}
		return 'KES ' . number_format( $n );
	}

	/** "800+" style rounding for live counts. */
	function blockke_hm22_plus( $n ) {
		$n = (int) $n;
		if ( $n >= 1000 ) {
			return number_format( floor( $n / 100 ) * 100 ) . '+';
		}
		if ( $n >= 100 ) {
			return ( floor( $n / 50 ) * 50 ) . '+';
		}
		if ( $n >= 20 ) {
			return ( floor( $n / 10 ) * 10 ) . '+';
		}
		return (string) $n;
	}

	/** Short card title from the long SEO listing title. */
	function blockke_hm22_short_title( $title, $max = 46 ) {
		$t = html_entity_decode( (string) $title, ENT_QUOTES, 'UTF-8' );
		$t = preg_replace( '/\s*(—|–|\||:)\s*.*$/u', '', $t );
		$t = preg_replace( '/\s+(for sale|for rent|to let)\b.*$/iu', '', $t );
		$t = trim( $t );
		if ( '' === $t ) {
			$t = html_entity_decode( (string) $title, ENT_QUOTES, 'UTF-8' );
		}
		if ( mb_strlen( $t ) > $max ) {
			$t = rtrim( mb_substr( $t, 0, $max - 1 ) ) . '…';
		}
		return $t;
	}

	function blockke_hm22_first_term( $id, $tax ) {
		$terms = get_the_terms( $id, $tax );
		if ( is_array( $terms ) && ! empty( $terms ) ) {
			return $terms[0];
		}
		return null;
	}

	function blockke_hm22_img( $attachment_id, $size, $class, $alt, $eager = false, $sizes = '' ) {
		$attachment_id = (int) $attachment_id;
		if ( ! $attachment_id ) {
			return '<span class="' . esc_attr( $class ) . ' bkh-noimg"></span>';
		}
		$src = wp_get_attachment_image_src( $attachment_id, $size );
		if ( ! $src ) {
			return '<span class="' . esc_attr( $class ) . ' bkh-noimg"></span>';
		}
		$srcset = wp_get_attachment_image_srcset( $attachment_id, $size );
		$attr   = ' class="' . esc_attr( $class ) . '" src="' . esc_url( $src[0] ) . '" alt="' . esc_attr( $alt ) . '"';
		if ( $srcset ) {
			$attr .= ' srcset="' . esc_attr( $srcset ) . '"';
			$attr .= ' sizes="' . esc_attr( $sizes ? $sizes : '(max-width: 767px) 100vw, 33vw' ) . '"';
		}
		if ( ! empty( $src[1] ) && ! empty( $src[2] ) ) {
			$attr .= ' width="' . (int) $src[1] . '" height="' . (int) $src[2] . '"';
		}
		$attr .= $eager ? ' fetchpriority="high" decoding="async"' : ' loading="lazy" decoding="async"';
		return '<img' . $attr . '>';
	}

	/** Term image from WpResidence's taxonomy options (category_attach_id), else null. */
	function blockke_hm22_term_image( $term_id ) {
		$opt = get_option( 'taxonomy_' . (int) $term_id );
		if ( is_array( $opt ) && ! empty( $opt['category_attach_id'] ) ) {
			return (int) $opt['category_attach_id'];
		}
		$m = get_term_meta( (int) $term_id, 'category_attach_id', true );
		return $m ? (int) $m : 0;
	}

	/** Area page: /neighbourhoods/<slug>/ when that page exists, else the area archive. */
	function blockke_hm22_area_url( $term ) {
		static $parent = null;
		if ( null === $parent ) {
			$p      = get_page_by_path( 'neighbourhoods' );
			$parent = $p ? (int) $p->ID : 0;
		}
		if ( $parent ) {
			$page = get_page_by_path( 'neighbourhoods/' . $term->slug );
			if ( $page && 'publish' === $page->post_status ) {
				return get_permalink( $page );
			}
		}
		$link = get_term_link( $term );
		return is_wp_error( $link ) ? home_url( '/' ) : $link;
	}

	/* ------------------------------------------------------------------ data */

	/** v2.1.1: true only when every taxonomy the counts and cards read is registered in this request. */
	function blockke_hm22_tax_ready() {
		foreach ( array( 'property_action_category', 'property_project', 'property_area', 'property_city' ) as $tax ) {
			if ( ! taxonomy_exists( $tax ) ) {
				return false;
			}
		}
		return post_type_exists( 'estate_property' );
	}

	/** v2.1.1: marks this render as one that must not be stored in the page cache. */
	function blockke_hm22_nostore( $set = null ) {
		static $flag = false;
		if ( null !== $set ) {
			$flag = (bool) $set;
		}
		return $flag;
	}

	function blockke_hm22_stats() {
		$key = 'blockke_home_v22_stats_' . blockke_hm22_stamp();
		$s   = get_transient( $key );
		if ( is_array( $s ) && ! empty( $s['sale'] ) && ! empty( $s['projects'] ) && ! empty( $s['areas'] ) ) {
			return $s;
		}
		if ( ! blockke_hm22_tax_ready() ) {
			blockke_hm22_nostore( true );
			return array( 'sale' => 0, 'rent' => 0, 'projects' => 0, 'areas' => 0 );
		}
		$sale = get_term_by( 'slug', 'for-sale', 'property_action_category' );
		$rent = get_term_by( 'slug', 'for-rent', 'property_action_category' );
		$projects = wp_count_terms( array( 'taxonomy' => 'property_project', 'hide_empty' => true ) );
		$projects = is_wp_error( $projects ) ? 0 : (int) $projects;
		$areas    = 0;
		$ids      = get_posts( array(
			'post_type'      => 'estate_property',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'fields'         => 'ids',
			'no_found_rows'  => true,
			'tax_query'      => array( array( 'taxonomy' => 'property_city', 'field' => 'slug', 'terms' => 'nairobi' ) ),
		) );
		if ( $ids ) {
			$t = wp_get_object_terms( $ids, 'property_area', array( 'fields' => 'ids' ) );
			if ( ! is_wp_error( $t ) ) {
				$areas = count( array_unique( $t ) );
			}
		}
		if ( ! $areas ) {
			$n     = wp_count_terms( array( 'taxonomy' => 'property_area', 'hide_empty' => true ) );
			$areas = is_wp_error( $n ) ? 0 : (int) $n;
		}
		$s = array(
			'sale'     => $sale ? (int) $sale->count : 0,
			'rent'     => $rent ? (int) $rent->count : 0,
			'projects' => $projects,
			'areas'    => $areas,
		);
		if ( $s['sale'] && $s['projects'] && $s['areas'] ) {
			set_transient( $key, $s, 12 * HOUR_IN_SECONDS );
		} else {
			blockke_hm22_nostore( true );
		}
		return $s;
	}

	/** One listing's card data. */
	function blockke_hm22_listing( $id ) {
		$id     = (int) $id;
		$rent   = has_term( 'for-rent', 'property_action_category', $id );
		$price  = (float) get_post_meta( $id, 'property_price', true );
		$label  = trim( (string) get_post_meta( $id, 'property_label_before', true ) );
		$subs   = get_post_meta( $id, 'property_has_subunits', true );
		$from   = ( ! $rent ) && ( $subs || preg_match( '/from/i', $label ) );
		$area   = blockke_hm22_first_term( $id, 'property_area' );
		$city   = blockke_hm22_first_term( $id, 'property_city' );
		$comp   = blockke_hm22_first_term( $id, 'property_completion' );
		$status = '';
		if ( ! $rent && has_term( 'offplan-ongoing', 'property_status', $id ) ) {
			$status = 'Off-plan';
			if ( $comp && 'ready-now' !== $comp->slug ) {
				$status .= ' · ' . $comp->name;
			} elseif ( $comp ) {
				$status = 'Ready now';
			}
		} elseif ( has_term( 'furnished', 'property_status', $id ) ) {
			$status = 'Furnished';
		} elseif ( ! $rent && $comp && 'ready-now' === $comp->slug ) {
			$status = 'Ready now';
		}
		$loc = array();
		if ( $area ) {
			$loc[] = $area->name;
		}
		if ( $city && ( ! $area || $city->name !== $area->name ) ) {
			$loc[] = $city->name;
		}
		$project = blockke_hm22_first_term( $id, 'property_project' );
		$title   = ( $project && mb_strlen( $project->name ) <= 40 && ! preg_match( '/bedroom|apartments? on|for sale/i', $project->name ) )
			? html_entity_decode( $project->name, ENT_QUOTES, 'UTF-8' )
			: blockke_hm22_short_title( get_the_title( $id ) );
		return array(
			'id'     => $id,
			'url'    => get_permalink( $id ),
			'title'  => $title,
			'full'   => html_entity_decode( get_the_title( $id ), ENT_QUOTES, 'UTF-8' ),
			'thumb'  => (int) get_post_thumbnail_id( $id ),
			'price'  => $price ? ( ( $from ? 'From ' : '' ) . blockke_hm22_price( $price, $rent ) ) : 'Price on request',
			'beds'   => (int) get_post_meta( $id, 'property_bedrooms', true ),
			'baths'  => (int) get_post_meta( $id, 'property_bathrooms', true ),
			'size'   => (float) get_post_meta( $id, 'property_size', true ),
			'loc'    => implode( ', ', $loc ),
			'status' => $status,
			'tab'    => blockke_hm22_tab( $id ), // v2.2: Off-plan | Ready | To rent
			'rent'   => $rent,
		);
	}

	function blockke_hm22_listings( $action, $n ) {
		$base = array(
			'post_type'      => 'estate_property',
			'post_status'    => 'publish',
			'fields'         => 'ids',
			'no_found_rows'  => true,
			'tax_query'      => array(
				'relation' => 'AND',
				array( 'taxonomy' => 'property_action_category', 'field' => 'slug', 'terms' => $action ),
				array( 'taxonomy' => 'property_status', 'field' => 'slug', 'terms' => array( 'sold' ), 'operator' => 'NOT IN' ),
			),
		);
		$featured = get_posts( array_merge( $base, array(
			'posts_per_page' => $n * 3,
			'orderby'        => 'date',
			'order'          => 'DESC',
			'meta_query'     => array( array( 'key' => 'prop_featured', 'value' => '1' ) ),
		) ) );
		$newest = get_posts( array_merge( $base, array(
			'posts_per_page' => $n * 4,
			'orderby'        => 'date',
			'order'          => 'DESC',
			'post__not_in'   => $featured ? $featured : array( 0 ),
		) ) );
		/* one card per project, featured first, newest after; every card needs a photo, and v2.2's tall cards one of at least
		   config listing_min_px (if fewer than three have one, photos down to 640 x 420 make up the row); then whole rows only:
		   a multiple of three, or the one or two there are */
		$cfg      = blockke_hm22_config();
		$px       = is_array( $cfg['listing_min_px'] ) ? array_values( $cfg['listing_min_px'] ) : array();
		$px       = isset( $px[0] ) && is_numeric( $px[0] ) ? array( (int) $px[0], isset( $px[1] ) && is_numeric( $px[1] ) ? (int) $px[1] : 0 ) : array( 900, 600 );
		$ids      = array();
		$projects = array();
		foreach ( array( $px, array( 640, 420 ) ) as $tier => $min ) {
			if ( $tier && count( $ids ) >= 3 ) {
				break;
			}
			foreach ( array_merge( $featured, $newest ) as $id ) {
				if ( count( $ids ) >= $n ) {
					break;
				}
				$th = (int) get_post_thumbnail_id( $id );
				if ( ! $th || in_array( (int) $id, $ids, true ) || ! blockke_hm22_big( $th, $min[0], $min[1] ) ) {
					continue;
				}
				$pt  = blockke_hm22_first_term( $id, 'property_project' );
				$key = $pt ? 'p' . $pt->term_id : 'l' . $id;
				if ( isset( $projects[ $key ] ) ) {
					continue;
				}
				$projects[ $key ] = true;
				$ids[]            = (int) $id;
			}
		}
		if ( count( $ids ) > 3 ) {
			$ids = array_slice( $ids, 0, count( $ids ) - count( $ids ) % 3 );
		}
		$out = array();
		foreach ( $ids as $id ) {
			$out[] = blockke_hm22_listing( $id ) + array( 'feat' => in_array( (int) $id, array_map( 'intval', $featured ), true ) ); // v2.2: one the team featured (prop_featured)
		}
		return $out;
	}

	/** Development card from a property_project slug. */
	function blockke_hm22_development( $slug ) {
		$cfg  = blockke_hm22_config();
		$term = get_term_by( 'slug', $slug, 'property_project' );
		if ( ! $term ) {
			return null;
		}
		$ids = get_posts( array(
			'post_type'      => 'estate_property',
			'post_status'    => 'publish',
			'posts_per_page' => 30,
			'fields'         => 'ids',
			'no_found_rows'  => true,
			'tax_query'      => array( array( 'taxonomy' => 'property_project', 'field' => 'term_id', 'terms' => (int) $term->term_id ) ),
		) );
		if ( ! $ids ) {
			return null;
		}
		$master = 0;
		foreach ( $ids as $id ) {
			if ( get_post_meta( $id, 'property_has_subunits', true ) ) {
				$master = (int) $id;
				break;
			}
		}
		if ( ! $master ) {
			$master = (int) $ids[0];
		}
		$units = get_post_meta( $master, 'property_subunits_list', true );
		$units = is_array( $units ) ? array_map( 'intval', $units ) : array();
		$pool  = array_unique( array_merge( array( $master ), $units, array_map( 'intval', $ids ) ) );
		$min_price = 0;
		$beds      = array();
		foreach ( $pool as $pid ) {
			if ( 'publish' !== get_post_status( $pid ) ) {
				continue;
			}
			$p = (float) get_post_meta( $pid, 'property_price', true );
			if ( $p > 0 && ( ! $min_price || $p < $min_price ) ) {
				$min_price = $p;
			}
			$b = get_post_meta( $pid, 'property_bedrooms', true );
			if ( '' !== $b && null !== $b ) {
				$beds[] = (int) $b;
			}
		}
		$mix = '';
		if ( $beds ) {
			$lo = min( $beds );
			$hi = max( $beds );
			$f  = function ( $n ) {
				return $n <= 0 ? 'Studio' : $n . ' bed';
			};
			$mix = ( $lo === $hi ) ? $f( $lo ) : $f( $lo ) . ' – ' . $f( $hi );
		}
		$area = blockke_hm22_first_term( $master, 'property_area' );
		$comp = get_post_meta( $master, 'bke_completion_stated', true );
		if ( ! $comp ) {
			$ct   = blockke_hm22_first_term( $master, 'property_completion' );
			$comp = $ct ? ( 'ready-now' === $ct->slug ? 'Ready now' : 'Completion ' . $ct->name ) : '';
		} else {
			$comp = ( 0 === stripos( $comp, 'ready' ) ) ? $comp : 'Completion ' . $comp;
		}
		$url = get_permalink( $master );
		if ( 'lp' === $cfg['dev_link'] ) {
			$lp = get_posts( array(
				'post_type'      => 'page',
				'post_status'    => 'publish',
				'posts_per_page' => 1,
				'fields'         => 'ids',
				'no_found_rows'  => true,
				'meta_key'       => 'bke_lp_listing',
				'meta_value'     => $master,
			) );
			if ( $lp ) {
				$url = get_permalink( $lp[0] );
			}
		}
		return array(
			'slug'    => $slug,
			'name'    => html_entity_decode( $term->name, ENT_QUOTES, 'UTF-8' ),
			'area'    => $area ? $area->name : '',
			'price'   => $min_price ? 'From ' . blockke_hm22_price( $min_price ) : 'Prices on request',
			'mix'     => $mix,
			'comp'    => $comp,
			'thumb'   => (int) get_post_thumbnail_id( $master ),
			'url'     => $url,
			'mandate' => in_array( $slug, (array) $cfg['mandates'], true ),
		);
	}

	function blockke_hm22_areas() {
		$cfg = blockke_hm22_config();
		$out = array();
		foreach ( (array) $cfg['neighbourhoods'] as $slug ) {
			$t = get_term_by( 'slug', $slug, 'property_area' );
			if ( ! $t || (int) $t->count < 1 ) {
				continue;
			}
			$thumb = blockke_hm22_term_image( $t->term_id );
			if ( ! $thumb ) {
				/* no image on the area itself: borrow the newest listing photo in that area */
				$recent = get_posts( array(
					'post_type'      => 'estate_property',
					'post_status'    => 'publish',
					'posts_per_page' => 6,
					'fields'         => 'ids',
					'no_found_rows'  => true,
					'tax_query'      => array( array( 'taxonomy' => 'property_area', 'field' => 'term_id', 'terms' => (int) $t->term_id ) ),
				) );
				foreach ( $recent as $rid ) {
					$thumb = (int) get_post_thumbnail_id( $rid );
					if ( $thumb ) {
						break;
					}
				}
			}
			$out[] = array(
				'name'  => $t->name,
				'count' => blockke_hm22_area_live( $t ), // v2.2: the same live count as the neighbourhood grid
				'url'   => blockke_hm22_area_url( $t ),
				'thumb' => $thumb,
			);
		}
		return $out;
	}

	function blockke_hm22_articles( $n = 4 ) {
		$posts = get_posts( array(
			'post_type'      => 'post',
			'post_status'    => 'publish',
			'posts_per_page' => $n,
			'no_found_rows'  => true,
		) );
		$out = array();
		foreach ( $posts as $p ) {
			$cat  = get_the_category( $p->ID );
			$name = ( $cat && ! empty( $cat[0] ) ) ? $cat[0]->name : '';
			$ex   = $p->post_excerpt ? $p->post_excerpt : wp_strip_all_tags( strip_shortcodes( $p->post_content ) );
			$out[] = array(
				'title' => html_entity_decode( get_the_title( $p ), ENT_QUOTES, 'UTF-8' ),
				'url'   => get_permalink( $p ),
				'cat'   => $name,
				'date'  => get_the_date( 'j M Y', $p ),
				'thumb' => (int) get_post_thumbnail_id( $p ),
				'ex'    => wp_trim_words( $ex, 24, '…' ),
			);
		}
		return $out;
	}

	function blockke_hm22_agent( $id ) {
		$id = (int) $id;
		if ( ! $id || 'publish' !== get_post_status( $id ) ) {
			return null;
		}
		$pos = get_post_meta( $id, 'agent_position', true );
		return array(
			'name'  => html_entity_decode( get_the_title( $id ), ENT_QUOTES, 'UTF-8' ),
			'role'  => $pos ? $pos : '',
			'thumb' => (int) get_post_thumbnail_id( $id ),
			'url'   => get_permalink( $id ),
		);
	}

	/* ------------------------------------------------------------------ development-name search (v2.1) */

	/** Words a development's name is often typed with or without ("Capella" finds "Capella Residency"). */
	function blockke_hm22_dev_filler() {
		return array( 'apartment' => 1, 'apartments' => 1, 'residence' => 1, 'residences' => 1, 'residency' => 1, 'residencies' => 1, 'homes' => 1, 'estate' => 1, 'the' => 1, 'at' => 1 );
	}

	/**
	 * Lowercase ASCII words: accents folded, "&" read as "and", and so is a lone "n" or "n’" between two words ("Oak n Ivy";
	 * as the last word it is a name still being typed, "N" for Nova), apostrophes dropped, other punctuation a space.
	 * Same as fold() in the page script.
	 */
	function blockke_hm22_dev_fold( $s ) {
		$s = html_entity_decode( (string) $s, ENT_QUOTES, 'UTF-8' );
		$s = str_replace( array( '&', "'", "\xE2\x80\x99", '`' ), array( ' and ', '', '', '' ), $s );
		$s = strtolower( remove_accents( $s ) );
		$w = explode( ' ', trim( preg_replace( '/[^a-z0-9]+/', ' ', $s ) ) );
		for ( $i = 0, $last = count( $w ) - 1; $i < $last; $i++ ) {
			if ( 'n' === $w[ $i ] ) {
				$w[ $i ] = 'and';
			}
		}
		return implode( ' ', $w );
	}

	/** A name or query ready to compare: w = its words, k = without the filler words (all of them when nothing else is left), c / f = k / w run together, o = only filler words. Same as prep() in the page script. */
	function blockke_hm22_dev_prep( $s ) {
		static $memo = array(); // the same names are compared for every query
		$s = (string) $s;
		if ( isset( $memo[ $s ] ) ) {
			return $memo[ $s ];
		}
		if ( count( $memo ) > 5000 ) {
			$memo = array();
		}
		$memo[ $s ] = blockke_hm22_dev_prep_words( $s );
		return $memo[ $s ];
	}

	/** prep() without the memo. */
	function blockke_hm22_dev_prep_words( $s ) {
		$f = blockke_hm22_dev_fold( $s );
		$w = '' === $f ? array() : explode( ' ', $f );
		$x = blockke_hm22_dev_filler();
		$k = array();
		foreach ( $w as $word ) {
			if ( ! isset( $x[ $word ] ) ) {
				$k[] = $word;
			}
		}
		$o = $w && ! $k;
		if ( ! $k ) {
			$k = $w;
		}
		return array( 'w' => $w, 'k' => $k, 'c' => implode( '', $k ), 'f' => implode( '', $w ), 'o' => $o );
	}

	/**
	 * Edits between two words, counting two neighbouring letters swapped ("wset" for "west") as one edit, the commonest slip
	 * (optimal string alignment distance; PHP's levenshtein() counts a swap as two). Same as osa() in the page script.
	 */
	function blockke_hm22_dev_osa( $a, $b ) {
		if ( $a === $b ) {
			return 0;
		}
		$m = strlen( $a );
		$n = strlen( $b );
		if ( ! $m || ! $n ) {
			return max( $m, $n );
		}
		$pp = array();
		$p  = range( 0, $n );
		for ( $i = 1; $i <= $m; $i++ ) {
			$c = array( $i );
			for ( $j = 1; $j <= $n; $j++ ) {
				$c[ $j ] = min( $p[ $j ] + 1, $c[ $j - 1 ] + 1, $p[ $j - 1 ] + ( $a[ $i - 1 ] === $b[ $j - 1 ] ? 0 : 1 ) );
				if ( $i > 1 && $j > 1 && $a[ $i - 1 ] === $b[ $j - 2 ] && $a[ $i - 2 ] === $b[ $j - 1 ] ) {
					$c[ $j ] = min( $c[ $j ], $pp[ $j - 2 ] + 1 );
				}
			}
			$pp = $p;
			$p  = $c;
		}
		return $p[ $n ];
	}

	/** Two words start with the same letter, c and k counting as one ("Kapella" for "Capella"): the first letter is rarely the slip, but it is often spelt by its sound. Same as lead() in the page script. */
	function blockke_hm22_dev_lead( $t, $u ) {
		return '' !== $t && '' !== $u && ( $t[0] === $u[0] || ( false !== strpos( 'ck', $t[0] ) && false !== strpos( 'ck', $u[0] ) ) );
	}

	/** One typed word is a whole-word slip of a word of the name: the same first letter (c = k), one slip, two from eight letters. Same as slip() in the page script. */
	function blockke_hm22_dev_slip( $t, $u ) {
		$n = strlen( $t );
		return $n >= 4 && $n <= 40 && strlen( $u ) <= 60 && blockke_hm22_dev_lead( $t, $u ) && blockke_hm22_dev_osa( $t, $u ) <= ( $n >= 8 ? 2 : 1 ); // no name has such long words
	}

	/** One typed word is a near miss for a word of the name: a whole-word slip, or one slip from how the word starts. Same as near() in the page script. */
	function blockke_hm22_dev_near( $t, $u ) {
		$n = strlen( $t );
		if ( $n < 4 || $n > 40 || strlen( $u ) > 60 || ! blockke_hm22_dev_lead( $t, $u ) ) {
			return false;
		}
		if ( blockke_hm22_dev_slip( $t, $u ) ) {
			return true;
		}
		if ( strlen( $u ) > $n ) {
			for ( $k = $n - 1; $k <= $n + 1; $k++ ) {
				if ( blockke_hm22_dev_osa( $t, substr( $u, 0, $k ) ) <= 1 ) {
					return true;
				}
			}
		}
		return false;
	}

	/**
	 * How well a query matches one name: 100 the same name, 99 the same once filler words are left out ("Oak Rise" for
	 * "Oak Rise Residency"), 80 the name starts with it, 60 each word starts a word of the name (64 when the first one starts
	 * the name), 40 inside the name, 20 a near miss, 0 not at all. Same as score() in the page script.
	 */
	function blockke_hm22_dev_score( $q, $m ) {
		if ( '' === $q['c'] ) {
			return 0;
		}
		if ( $m['f'] === $q['f'] ) {
			return 100;
		}
		if ( $m['c'] === $q['c'] ) {
			return 99;
		}
		$tg = $q['o'] ? $m['f'] : $m['c'];
		if ( 0 === strpos( $tg, $q['c'] ) ) {
			return 80;
		}
		$all = true;
		foreach ( $q['k'] as $t ) {
			$hit = false;
			foreach ( $m['w'] as $u ) {
				if ( 0 === strpos( $u, $t ) ) {
					$hit = true;
					break;
				}
			}
			if ( ! $hit ) {
				$all = false;
				break;
			}
		}
		if ( $all ) {
			return 60 + ( $m['k'] && 0 === strpos( $m['k'][0], $q['k'][0] ) ? 4 : 0 );
		}
		if ( strlen( $q['c'] ) >= 3 && false !== strpos( $tg, $q['c'] ) ) {
			return 40;
		}
		foreach ( $q['k'] as $t ) {
			$hit = false;
			foreach ( $m['w'] as $u ) {
				if ( 0 === strpos( $u, $t ) || blockke_hm22_dev_near( $t, $u ) ) {
					$hit = true;
					break;
				}
			}
			if ( ! $hit ) {
				return 0;
			}
		}
		return 20;
	}

	/** Which developments suit the mode first: Rent prefers those with rentals, Buy and Off-plan those for sale (Off-plan: off-plan ones first). */
	function blockke_hm22_dev_pref( $b, $mode ) {
		$b = (int) $b;
		if ( 'rent' === $mode ) {
			return ( $b & 2 ) ? 2 : 0;
		}
		return ( ( $b & 1 ) ? 2 : 0 ) + ( ( 'offplan' === $mode && ( $b & 4 ) ) ? 1 : 0 );
	}

	/** A query against a development's name and its aliases (Project names that read differently), an alias scoring 1 lower. Same as all() in the page script. */
	function blockke_hm22_dev_score_all( $q, $e ) {
		$s = blockke_hm22_dev_score( $q, blockke_hm22_dev_prep( $e['n'] ) );
		foreach ( (array) $e['al'] as $a ) {
			$s = max( $s, blockke_hm22_dev_score( $q, blockke_hm22_dev_prep( $a ) ) - 1 );
		}
		return $s;
	}

	/** Words of an area's name that say nothing on their own ("Ngong Road", "Tatu City", "Lower Kabete"): never taken for the area. */
	function blockke_hm22_dev_notarea() {
		return array( 'road' => 1, 'city' => 1, 'town' => 1, 'general' => 1, 'river' => 1, 'rivers' => 1, 'spring' => 1, 'springs' => 1, 'valley' => 1, 'south' => 1, 'north' => 1, 'east' => 1, 'west' => 1, 'upper' => 1, 'lower' => 1, 'garden' => 1, 'gardens' => 1, 'plains' => 1, 'park' => 1, 'hill' => 1, 'hills' => 1, 'view' => 1, 'views' => 1, 'estate' => 1, 'beach' => 1, 'heights' => 1, 'ridge' => 1, 'gate' => 1, 'green' => 1, 'court' => 1, 'place' => 1, 'square' => 1 );
	}

	/** Every area word the index knows (from each development's place, at least 4 letters, not a word like "Road"), plus Nairobi and Kenya. Same as areas() in the page script. */
	function blockke_hm22_dev_areas( $d ) {
		$no  = blockke_hm22_dev_notarea();
		$out = array();
		foreach ( $d as $e ) {
			foreach ( blockke_hm22_dev_prep( $e['pl'] )['w'] as $u ) {
				if ( strlen( $u ) >= 4 && ! isset( $no[ $u ] ) ) {
					$out[ $u ] = true;
				}
			}
		}
		return array_keys( $out + array( 'nairobi' => true, 'kenya' => true ) );
	}

	/**
	 * The query without the words that name a place ("Capella Westlands"): a word of the place, a slip of one (from five
	 * letters), or the start of one when it is the last word, still being typed ("Capella W"). Null when no word is a place
	 * word, or when nothing but place or filler words would be left. Same as unplace() in the page script.
	 */
	function blockke_hm22_dev_unplace( $q, $place ) {
		$n = count( $q['w'] );
		if ( ! $place || $n < 2 ) {
			return null;
		}
		$keep = array();
		foreach ( $q['w'] as $i => $t ) {
			$hit = false;
			foreach ( $place as $u ) {
				if ( $u === $t || ( $i === $n - 1 && 0 === strpos( $u, $t ) ) || ( strlen( $t ) >= 5 && blockke_hm22_dev_slip( $t, $u ) ) ) {
					$hit = true;
					break;
				}
			}
			if ( ! $hit ) {
				$keep[] = $t;
			}
		}
		if ( ! $keep || count( $keep ) === $n ) {
			return null;
		}
		$r = blockke_hm22_dev_prep( implode( ' ', $keep ) );
		return $r['o'] ? null : $r;
	}

	/**
	 * Developments matching $text, best first: array of array( index, score ). Near misses only when nothing better matched;
	 * the query inside a word of the name (40) only when no name starts with it or has a word that does ("unity": Unity
	 * Parkside, not Karunguru Gated Community).
	 * A query that also names the development's own area is matched without it, 1 lower ("Capella Westlands", "Capella W");
	 * when nothing better than a near miss matches, without any area word the index knows, 2 lower ("Capella Kilimani").
	 * Ties: the mode's preference, then the homepage's own developments in their order (config developments, see
	 * blockke_hm22_dev_feat()), then the shorter name, then the index order (busiest first). Same as find() in the page script.
	 */
	function blockke_hm22_dev_find( $text, $d, $mode ) {
		$q = blockke_hm22_dev_prep( $text );
		if ( '' === $q['c'] ) {
			return array();
		}
		$ft  = blockke_hm22_dev_feat( $d );
		$sc  = array();
		$len = array();
		$top = 0;
		foreach ( $d as $i => $e ) {
			$len[ $i ] = strlen( blockke_hm22_dev_prep( $e['n'] )['c'] );
			$s         = blockke_hm22_dev_score_all( $q, $e );
			$q1        = blockke_hm22_dev_unplace( $q, blockke_hm22_dev_prep( $e['pl'] )['w'] );
			if ( $q1 ) {
				$s = max( $s, blockke_hm22_dev_score_all( $q1, $e ) - 1 );
			}
			$sc[ $i ] = $s;
			$top      = max( $top, $s );
		}
		$q2 = $top < 35 ? blockke_hm22_dev_unplace( $q, blockke_hm22_dev_areas( $d ) ) : null;
		if ( $q2 ) {
			foreach ( $d as $i => $e ) {
				$sc[ $i ] = max( $sc[ $i ], blockke_hm22_dev_score_all( $q2, $e ) - 2 );
				$top      = max( $top, $sc[ $i ] );
			}
		}
		$out = array();
		$min = $top < 35 ? 1 : ( $top >= 50 ? 50 : 35 ); // near misses only when nothing better; inside a word ("commUNITY") only when nothing starts with it
		foreach ( $sc as $i => $s ) {
			if ( $s >= $min ) {
				$out[] = array( (int) $i, $s );
			}
		}
		usort(
			$out,
			function ( $x, $y ) use ( $d, $mode, $len, $ft ) {
				if ( $x[1] !== $y[1] ) {
					return $y[1] - $x[1];
				}
				$px = blockke_hm22_dev_pref( $d[ $x[0] ]['b'], $mode );
				$py = blockke_hm22_dev_pref( $d[ $y[0] ]['b'], $mode );
				if ( $px !== $py ) {
					return $py - $px;
				}
				$fx = isset( $ft[ $x[0] ] ) ? $ft[ $x[0] ] : 999;
				$fy = isset( $ft[ $y[0] ] ) ? $ft[ $y[0] ] : 999;
				if ( $fx !== $fy ) {
					return $fx - $fy;
				}
				if ( $len[ $x[0] ] !== $len[ $y[0] ] ) {
					return $len[ $x[0] ] - $len[ $y[0] ];
				}
				return $x[0] - $y[0];
			}
		);
		return $out;
	}

	/**
	 * The homepage's own developments (config developments: property_project slugs) as index => position in that
	 * list; a development not on it is left out. It breaks ties in blockke_hm22_dev_find() and orders blockke_hm22_dev_top(), and
	 * the page script gets it as dv.f. Worked out per request from the config, so a config change counts at once.
	 */
	function blockke_hm22_dev_feat( $d ) {
		static $memo = array();
		$cfg   = blockke_hm22_config();
		$slugs = array_values( array_filter( (array) $cfg['developments'], 'is_string' ) );
		$key   = md5( wp_json_encode( array( $slugs, count( $d ), isset( $d[0]['n'] ) ? (string) $d[0]['n'] : '' ) ) ); // the index is a list
		if ( isset( $memo[ $key ] ) ) {
			return $memo[ $key ];
		}
		$want = array();
		foreach ( $slugs as $slug ) {
			$t = '' !== $slug ? get_term_by( 'slug', $slug, 'property_project' ) : null;
			if ( $t ) {
				$want[] = (int) $t->term_id;
			}
		}
		$out = array();
		foreach ( $want ? $d : array() as $i => $e ) {
			foreach ( $want as $pos => $tid ) {
				if ( in_array( $tid, (array) $e['pj'], true ) ) {
					$out[ $i ] = $pos;
					break;
				}
			}
		}
		$memo[ $key ] = $out;
		return $out;
	}

	/**
	 * The one development a search clearly means (index), or null (never for filler words alone): the exact name, scoring above
	 * every other (the full name beats one that differs only by "Residency"); the only result, when more than a near miss; or
	 * the only one the name starts or words start with, when the rest merely contain the letters. A near miss counts when it is
	 * the only result, the name typed has five letters or more, and every word of it starts a word of the name (or of one
	 * alias) or is a whole-word slip of one ("capela", "golden hills", "capella recidency"); words naming the development's own
	 * area are left out first ("capela westlands"). A slip of how a word starts does not count ("santa" is not Santorini).
	 * Same as strong() in the page script.
	 */
	function blockke_hm22_dev_strong( $res, $text, $d ) {
		$q = blockke_hm22_dev_prep( $text );
		if ( ! $res || $q['o'] ) {
			return null;
		}
		$i = $res[0][0];
		$a = $res[0][1];
		$b = count( $res ) > 1 ? $res[1][1] : 0;
		if ( ( $a >= 99 && $b < $a ) || ( 1 === count( $res ) && $a >= 40 ) || ( $a >= 60 && $b <= 40 ) ) {
			return $i;
		}
		if ( 1 !== count( $res ) || $a >= 35 || ! isset( $d[ $i ] ) ) {
			return null;
		}
		$k = blockke_hm22_dev_unplace( $q, blockke_hm22_dev_prep( $d[ $i ]['pl'] )['w'] );
		$k = $k ? $k : $q;
		if ( preg_match_all( '/[a-z]/', $k['c'] ) < 5 ) {
			return null;
		}
		foreach ( array_merge( array( $d[ $i ]['n'] ), (array) $d[ $i ]['al'] ) as $nm ) {
			$w  = blockke_hm22_dev_prep( $nm )['w'];
			$ok = true;
			foreach ( $k['k'] as $t ) {
				$hit = false;
				foreach ( $w as $u ) {
					if ( 0 === strpos( $u, $t ) || blockke_hm22_dev_slip( $t, $u ) ) {
						$hit = true;
						break;
					}
				}
				if ( ! $hit ) {
					$ok = false;
					break;
				}
			}
			if ( $ok ) {
				return $i;
			}
		}
		return null;
	}

	/** A name worth offering: not a listing title ("3 Bedroom Apartment for Sale"), at most 60 characters. */
	function blockke_hm22_dev_named( $n ) {
		$n = trim( (string) $n );
		return '' !== $n && mb_strlen( $n ) <= 60 && preg_match( '/\p{L}/u', $n ) && ! preg_match( '/\b(?:bedrooms?|for sale|for rent|to let)\b/iu', $n );
	}

	/** Project names that read differently from the development's own name (compared as the search compares them). */
	function blockke_hm22_dev_aliases( $name, $names ) {
		$seen = array( blockke_hm22_dev_prep( $name )['c'] => true );
		$out  = array();
		foreach ( $names as $n ) {
			$n = trim( html_entity_decode( (string) $n, ENT_QUOTES, 'UTF-8' ) );
			$c = blockke_hm22_dev_prep( $n )['c'];
			if ( '' === $c || isset( $seen[ $c ] ) || ! blockke_hm22_dev_named( $n ) ) {
				continue;
			}
			$seen[ $c ] = true;
			$out[]      = $n;
		}
		return array_slice( $out, 0, 4 );
	}

	/** Listing id => array( term_id => term name ) for property_project, in one query per 500 listings. */
	function blockke_hm22_dev_projects( $ids ) {
		$out = array();
		$ids = array_values( array_unique( array_filter( array_map( 'intval', (array) $ids ) ) ) );
		foreach ( array_chunk( $ids, 500 ) as $chunk ) {
			$rel = wp_get_object_terms( $chunk, 'property_project', array( 'fields' => 'all_with_object_id', 'update_term_meta_cache' => false ) );
			if ( is_wp_error( $rel ) ) {
				continue;
			}
			foreach ( $rel as $t ) {
				$out[ (int) $t->object_id ][ (int) $t->term_id ] = $t->name;
			}
		}
		return $out;
	}

	/** The developments from snippet 42706 (blockke_bkn_devs()): one per development, its main listing, the site's own cleaned name. */
	function blockke_hm22_dev_from_bkn() {
		$all = blockke_bkn_devs();
		if ( empty( $all['devs'] ) || ! is_array( $all['devs'] ) ) {
			return array();
		}
		$ids = array();
		foreach ( $all['devs'] as $c ) {
			$ids = array_merge( $ids, (array) $c['ids'] );
		}
		$pj  = blockke_hm22_dev_projects( $ids );
		$out = array();
		foreach ( $all['devs'] as $c ) {
			$name = trim( (string) $c['name'] );
			if ( ! blockke_hm22_dev_named( $name ) || empty( $c['url'] ) ) {
				continue;
			}
			$acts = (array) $c['acts'];
			$b    = ( in_array( 'sale', $acts, true ) ? 1 : 0 ) | ( in_array( 'rent', $acts, true ) ? 2 : 0 ) | ( 'offplan' === $c['st'] ? 4 : 0 ) | ( 'land' === $c['kind'] ? 8 : ( 'commercial' === $c['kind'] ? 16 : 0 ) );
			$ru   = '';
			if ( 3 === ( $b & 3 ) && function_exists( 'blockke_bkn_for_act' ) ) { // for sale and to rent: Rent mode opens a rental
				$r = blockke_bkn_for_act( $c, (array) $c['ids'], 'rent' );
				if ( ! empty( $r['url'] ) && $r['url'] !== $c['url'] ) {
					$ru = (string) $r['url'];
				}
			}
			$terms = array();
			foreach ( (array) $c['ids'] as $id ) {
				if ( isset( $pj[ (int) $id ] ) ) {
					$terms += $pj[ (int) $id ];
				}
			}
			$out[] = array(
				'n'  => $name,
				'pl' => (string) $c['place'],
				'u'  => (string) $c['url'],
				'ru' => $ru,
				's'  => (float) $c['from'],
				'sf' => $c['to'] > $c['from'] || count( (array) $c['beds'] ) > 1,
				'r'  => (float) $c['rfrom'],
				'rf' => $c['rto'] > $c['rfrom'],
				'b'  => $b,
				'al' => blockke_hm22_dev_aliases( $name, $terms ),
				'pj' => array_keys( $terms ),
				'k'  => (int) $c['n'],
				'ft' => ! empty( $c['ft'] ),
				'vt' => ! empty( $c['vt'] ),
				'id' => (int) $c['id'], // v2.2: the main listing
			);
		}
		return $out;
	}

	/**
	 * Without snippet 42706: property_project terms with listings, each linked to its main listing (the one with units, else
	 * the newest by date), as blockke_hm22_development() does. Its area: the main listing's bke_dev_area (a slug) when set, else
	 * the one of its areas that most of the development's listings carry, else the first. A few queries for every project
	 * together, not one per project.
	 */
	function blockke_hm22_dev_from_terms() {
		global $wpdb;
		$ids = get_posts( array(
			'post_type'      => 'estate_property',
			'post_status'    => 'publish',
			'posts_per_page' => 3000,
			'fields'         => 'ids',
			'no_found_rows'  => true,
			'orderby'        => array( 'date' => 'DESC', 'ID' => 'ASC' ), // newest first, as blockke_hm22_development() takes a project's listings (same date: the lower ID, as MariaDB returns them there)
			'tax_query'      => array( array( 'taxonomy' => 'property_project', 'operator' => 'EXISTS' ) ),
		) );
		$ids = array_map( 'intval', $ids );
		if ( ! $ids ) {
			return array();
		}
		$rank = array_flip( $ids ); // a listing's place in that order
		$tx   = array();
		$proj = array();
		foreach ( array_chunk( $ids, 500 ) as $chunk ) {
			$rel = wp_get_object_terms( $chunk, array( 'property_project', 'property_action_category', 'property_status', 'property_area', 'property_category' ), array( 'fields' => 'all_with_object_id', 'update_term_meta_cache' => false ) );
			if ( is_wp_error( $rel ) ) {
				continue;
			}
			foreach ( $rel as $t ) {
				$oid = (int) $t->object_id;
				if ( 'property_project' === $t->taxonomy ) {
					$proj[ (int) $t->term_id ]['name']  = $t->name;
					$proj[ (int) $t->term_id ]['ids'][] = $oid;
				} elseif ( 'property_area' === $t->taxonomy ) {
					$tx[ $oid ]['area'][ (string) $t->slug ] = $t->name;
				} else {
					$tx[ $oid ][ $t->taxonomy ][] = $t->slug;
				}
			}
		}
		$meta = array();
		foreach ( array_chunk( $ids, 500 ) as $chunk ) {
			$rows = $wpdb->get_results( "SELECT post_id, meta_key, meta_value FROM {$wpdb->postmeta} WHERE meta_key IN ('property_price','property_has_subunits','bke_dev_area') AND post_id IN (" . implode( ',', array_map( 'intval', $chunk ) ) . ')' ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
			foreach ( (array) $rows as $r ) {
				$meta[ (int) $r->post_id ][ $r->meta_key ] = $r->meta_value;
			}
		}
		$has = function ( $id, $tax, $slugs ) use ( $tx ) {
			return isset( $tx[ $id ][ $tax ] ) && array_intersect( (array) $slugs, $tx[ $id ][ $tax ] );
		};
		$devs = array();
		$need = array();
		foreach ( $proj as $tid => $p ) {
			$live = array();
			foreach ( array_unique( $p['ids'] ) as $id ) {
				if ( ! $has( $id, 'property_status', array( 'sold', 'let' ) ) ) {
					$live[] = $id;
				}
			}
			$name = trim( html_entity_decode( (string) $p['name'], ENT_QUOTES, 'UTF-8' ) );
			if ( ! $live || ! blockke_hm22_dev_named( $name ) ) {
				continue;
			}
			usort(
				$live,
				function ( $a, $b ) use ( $rank ) {
					return $rank[ $a ] - $rank[ $b ];
				}
			);
			$master = 0;
			$rent   = 0;
			$sale   = array();
			$rents  = array();
			$cats   = array();
			$b      = 0;
			foreach ( $live as $id ) {
				if ( ! $master && ! empty( $meta[ $id ]['property_has_subunits'] ) ) {
					$master = $id;
				}
				$p0 = isset( $meta[ $id ]['property_price'] ) ? (float) $meta[ $id ]['property_price'] : 0;
				if ( $has( $id, 'property_action_category', 'for-rent' ) ) {
					$b |= 2;
					$rent = $rent ? $rent : $id;
					if ( $p0 > 0 ) {
						$rents[] = $p0;
					}
				} elseif ( $has( $id, 'property_action_category', 'for-sale' ) ) {
					$b |= 1;
					if ( $p0 > 0 ) {
						$sale[] = $p0;
					}
				}
				if ( $has( $id, 'property_status', 'offplan-ongoing' ) ) {
					$b |= 4;
				}
				$cats = array_merge( $cats, isset( $tx[ $id ]['property_category'] ) ? $tx[ $id ]['property_category'] : array() );
			}
			$master = $master ? $master : $live[0];
			$cats   = array_unique( $cats );
			$homes  = array_intersect( $cats, array( 'residential', 'apartments', 'studio-apartments', 'villas-residential', 'houses-residential', 'studio' ) );
			if ( ! $homes && array_intersect( $cats, array( 'land', 'residential-land', 'commercial-land' ) ) ) {
				$b |= 8;
			} elseif ( ! $homes && array_intersect( $cats, array( 'commercial', 'offices', 'warehouses' ) ) ) {
				$b |= 16;
			}
			$need[] = $master;
			if ( 3 === ( $b & 3 ) && $rent ) {
				$need[] = $rent;
			}
			$pl  = '';
			$own = isset( $meta[ $master ]['bke_dev_area'] ) ? sanitize_title( (string) $meta[ $master ]['bke_dev_area'] ) : '';
			if ( '' !== $own ) { // the main listing's own choice of area, as on its development page
				foreach ( $live as $id ) {
					if ( isset( $tx[ $id ]['area'][ $own ] ) ) {
						$pl = $tx[ $id ]['area'][ $own ];
						break;
					}
				}
				if ( '' === $pl ) {
					$at = get_term_by( 'slug', $own, 'property_area' );
					$pl = $at ? $at->name : '';
				}
			}
			if ( '' === $pl ) { // else the main listing's area that most of the development's listings carry, else its first
				$cand = isset( $tx[ $master ]['area'] ) ? $tx[ $master ]['area'] : array();
				if ( ! $cand ) {
					foreach ( $live as $id ) {
						$cand += isset( $tx[ $id ]['area'] ) ? $tx[ $id ]['area'] : array();
					}
				}
				$most = 0;
				foreach ( $cand as $slug => $an ) {
					$c = 0;
					foreach ( $live as $id ) {
						$c += isset( $tx[ $id ]['area'][ $slug ] ) ? 1 : 0;
					}
					if ( '' === $pl || $c > $most ) {
						$pl   = (string) $an;
						$most = $c;
					}
				}
			}
			$devs[] = array(
				'n'  => $name,
				'pl' => html_entity_decode( $pl, ENT_QUOTES, 'UTF-8' ),
				'u'  => $master,
				'ru' => 3 === ( $b & 3 ) ? $rent : 0,
				's'  => $sale ? min( $sale ) : 0,
				'sf' => count( $sale ) > 1 || ! empty( $meta[ $master ]['property_has_subunits'] ),
				'r'  => $rents ? min( $rents ) : 0,
				'rf' => $rents && max( $rents ) > min( $rents ),
				'b'  => $b,
				'al' => array(),
				'pj' => array( (int) $tid ),
				'k'  => count( $live ),
				'ft' => false,
				'vt' => false,
				'id' => (int) $master, // v2.2: the main listing
			);
		}
		if ( $need && function_exists( '_prime_post_caches' ) ) {
			_prime_post_caches( array_unique( $need ), false, false );
		}
		foreach ( $devs as $i => $e ) {
			$devs[ $i ]['u']  = (string) get_permalink( $e['u'] );
			$devs[ $i ]['ru'] = $e['ru'] ? (string) get_permalink( $e['ru'] ) : '';
		}
		return $devs;
	}

	/**
	 * Every development the search knows, busiest first: array( 'src' => 'bkn' | 'terms' | '', 'd' => list ). Each:
	 * n name, pl place, u url, ru rental url (when it is also for sale), s / r lowest sale / rent price, sf / rf a "from" price,
	 * b bits (1 for sale, 2 to rent, 4 off-plan, 8 land, 16 commercial), al aliases, pj Project term ids, k listings, ft featured, vt new layout.
	 * Kept in one transient (blockke_home_v22_devs, a single row whatever the stamp), current while the content stamp is the
	 * same and it is younger than config cache_minutes. Built from snippet 42706's developments, it is rebuilt only once 42706
	 * has rebuilt them for the current listings, which its development, area and search pages do: until then (after a listing
	 * is saved, say) the copy is kept, at most 12 hours old, so the homepage neither pays for that rebuild (about half a second)
	 * nor waits on 42706's build lock. Names and links rarely change; prices catch up within the hour.
	 */
	function blockke_hm22_dev_index() {
		static $idx = null;
		if ( null !== $idx ) {
			return $idx;
		}
		$cfg  = blockke_hm22_config();
		$key  = 'blockke_home_v22_devs';
		$st   = blockke_hm22_stamp();
		$skip = isset( $_GET['bkhome_nocache'] ) || isset( $_GET['elementor-preview'] ) || is_preview(); // phpcs:ignore WordPress.Security.NonceVerification
		$hit  = $skip ? false : get_transient( $key );
		$age  = is_array( $hit ) && isset( $hit['st'], $hit['src'], $hit['d'], $hit['t'] ) && is_array( $hit['d'] ) && $hit['d'] ? time() - (int) $hit['t'] : null;
		if ( null !== $age && $st === $hit['st'] && $age < max( 1, (int) $cfg['cache_minutes'] ) * MINUTE_IN_SECONDS ) {
			$idx = $hit;
			return $idx;
		}
		if ( null !== $age && 'bkn' === $hit['src'] && $age < 12 * HOUR_IN_SECONDS && ! blockke_hm22_dev_bkn_ready() ) {
			$idx = $hit;
			return $idx;
		}
		$d   = array();
		$src = '';
		if ( function_exists( 'blockke_bkn_devs' ) ) {
			try {
				$d   = blockke_hm22_dev_from_bkn();
				$src = 'bkn';
			} catch ( Throwable $e ) {
				error_log( 'BLOCK homepage v2.2 (developments from 42706): ' . $e->getMessage() ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions
				$d = array();
			}
		}
		if ( ! $d ) {
			$d   = blockke_hm22_dev_from_terms();
			$src = $d ? 'terms' : '';
		}
		usort(
			$d,
			function ( $a, $b ) {
				return $a['k'] !== $b['k'] ? $b['k'] - $a['k'] : strcasecmp( $a['n'], $b['n'] );
			}
		);
		$idx = array( 'st' => $st, 'src' => $src, 't' => time(), 'd' => array_values( $d ) );
		if ( ! $skip && $d && blockke_hm22_tax_ready() ) { // v2.1.1
			set_transient( $key, $idx, 12 * HOUR_IN_SECONDS );
		}
		return $idx;
	}

	/**
	 * Whether snippet 42706 holds its listing rows for the current listings (transient bkn_rows at the version and within the
	 * day blockke_bkn_rows() accepts), so reading its developments costs no rebuild. True when that cannot be told (another
	 * 42706 version): the index is then built as it always was.
	 */
	function blockke_hm22_dev_bkn_ready() {
		if ( ! function_exists( 'blockke_bkn_ver' ) ) {
			return true;
		}
		try {
			$h = get_transient( 'bkn_rows' );
			return is_array( $h ) && isset( $h['ver'], $h['rows'] ) && $h['ver'] === blockke_bkn_ver( true ) && ( ! isset( $h['t'] ) || (int) $h['t'] >= time() - DAY_IN_SECONDS );
		} catch ( Throwable $e ) {
			return true;
		}
	}

	/**
	 * The name asked for with ?bkdev=, null without it. It is shown on the homepage, so only what a building's name is written
	 * with is kept (letters, the digits 0-9, spaces and & ' ’ . , @ ( ) -), and nothing that reads as contact details: words
	 * with a web address in them (a dot followed by two letters or digits, "www", "http"), e-mail addresses and @handles
	 * (an @ is kept only between letters, as in Pazuri@Vipingo), and every digit once there are five or more of them, however
	 * they are spaced (phone, till and paybill numbers). At most 80 characters.
	 */
	function blockke_hm22_dev_query() {
		static $q = false;
		if ( false !== $q ) {
			return $q;
		}
		if ( ! isset( $_GET['bkdev'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
			$q = null;
			return $q;
		}
		$s    = is_scalar( $_GET['bkdev'] ) ? sanitize_text_field( wp_unslash( (string) $_GET['bkdev'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
		$s    = (string) preg_replace( '/[^\p{L}\p{M}0-9\s&\'\x{2019}.,@()\-]+/u', ' ', $s );
		if ( preg_match_all( '/[0-9]/', $s ) >= 5 ) {
			$s = (string) preg_replace( '/[0-9]+/', ' ', $s );
		}
		$keep = array();
		$dot  = false;
		foreach ( preg_split( '/\s+/u', $s, -1, PREG_SPLIT_NO_EMPTY ) as $w ) {
			$tld = $dot && preg_match( '/^(?:com|co|ke|net|org|info|biz|io|me)$/i', $w );
			$dot = 0 === strcasecmp( $w, 'dot' );
			if ( $tld ) {
				array_pop( $keep ); // "dot com": the "dot" goes too
				continue;
			}
			if ( preg_match( '/\.[\p{L}\p{M}0-9]{2}|^(?:https?|www)$|^[\-.,()\'\x{2019}]+$/iu', $w ) || ( false !== strpos( $w, '@' ) && ! preg_match( '/^[\p{L}\p{M}]+@[\p{L}\p{M}]+$/u', $w ) ) ) {
				continue; // a web or e-mail address, an @handle, or punctuation left on its own
			}
			$keep[] = $w;
		}
		$s = implode( ' ', $keep );
		if ( mb_strlen( $s ) > 80 ) {
			$s = trim( mb_substr( $s, 0, 80 ) );
		}
		$q = $s;
		return $q;
	}

	/** The search mode the visitor was in (?bkfor=rent | offplan, set by the page script), else 'buy'. */
	function blockke_hm22_dev_mode() {
		$m = isset( $_GET['bkfor'] ) && is_scalar( $_GET['bkfor'] ) ? (string) $_GET['bkfor'] : ''; // phpcs:ignore WordPress.Security.NonceVerification
		return in_array( $m, array( 'rent', 'offplan' ), true ) ? $m : 'buy';
	}

	/** The page showing this layout when it is not the front page yet (a preview of this layout), else 0. */
	function blockke_hm22_dev_page() {
		return ( ! is_front_page() && is_singular() ) ? (int) get_queried_object_id() : 0;
	}

	/** Where the name search posts: the homepage, or the preview page it is on (its cached copy is kept apart, see blockke_hm22_output()). */
	function blockke_hm22_dev_action() {
		$id = blockke_hm22_dev_page();
		$p  = $id ? get_permalink( $id ) : '';
		return $p ? $p : home_url( '/' );
	}

	/** "39.5M" / "120K": the price without the currency or "a month" (the page script adds them back). */
	function blockke_hm22_dev_short( $n, $rent = false ) {
		return preg_replace( '/^KES\s+|\s+a month$/', '', blockke_hm22_price( $n, $rent ) );
	}

	/** What one development shows in a mode: array( url, price, meta ). Same as show() in the page script. */
	function blockke_hm22_dev_show( $e, $mode ) {
		$b    = (int) $e['b'];
		$rent = 'rent' === $mode;
		$use  = $rent ? ( ( $b & 2 ) || ! ( $b & 1 ) ) : ( ! ( $b & 1 ) && ( $b & 2 ) );
		$n    = $use ? $e['r'] : $e['s'];
		$from = $use ? $e['rf'] : $e['sf'];
		$meta = array();
		if ( '' !== $e['pl'] ) {
			$meta[] = $e['pl'];
		}
		if ( $b & 8 ) {
			$meta[] = 'Land';
		} elseif ( $b & 16 ) {
			$meta[] = 'Commercial';
		}
		if ( ! $rent && ( $b & 4 ) && ! $use ) {
			$meta[] = 'Off-plan';
		}
		if ( $rent && ! ( $b & 2 ) && ( $b & 1 ) ) {
			$meta[] = 'For sale';
		}
		if ( ! $rent && $use ) {
			$meta[] = 'To rent';
		}
		return array(
			'url'   => ( $use && '' !== $e['ru'] ) ? $e['ru'] : $e['u'],
			'price' => $n > 0 ? ( $from ? 'From ' : '' ) . blockke_hm22_price( $n, $use ) : 'Prices on request',
			'meta'  => implode( ' · ', $meta ),
		);
	}

	/**
	 * The index for the page script, as small as it will go: b = the common start of the listing paths, d = one array per
	 * development: [ name, place, path, sale price ("+" = from), rent price, bits, rental path, aliases ], trailing blanks dropped;
	 * f = the homepage's own developments, index => position (blockke_hm22_dev_feat()), which break ties as on the server.
	 */
	function blockke_hm22_dev_data( $d ) {
		$home = untrailingslashit( home_url() );
		$rel  = function ( $u ) use ( $home ) {
			return ( '' !== $home && 0 === strpos( $u, $home . '/' ) ) ? substr( $u, strlen( $home ) ) : $u;
		};
		$seg = array();
		foreach ( $d as $e ) {
			if ( preg_match( '#^(/[^/?]+/)[^?]*/$#', $rel( $e['u'] ), $m ) ) {
				$seg[ $m[1] ] = isset( $seg[ $m[1] ] ) ? $seg[ $m[1] ] + 1 : 1;
			}
		}
		arsort( $seg );
		$base = $seg ? (string) key( $seg ) : '';
		$path = function ( $u ) use ( $rel, $base ) {
			$r = $rel( $u );
			if ( '' !== $base && 0 === strpos( $r, $base ) && '/' === substr( $r, -1 ) && strlen( $r ) > strlen( $base ) + 1 && false === strpos( $r, '?' ) ) {
				return substr( $r, strlen( $base ), -1 );
			}
			return $r;
		};
		$out = array();
		foreach ( $d as $e ) {
			$row = array(
				$e['n'],
				$e['pl'],
				$path( $e['u'] ),
				$e['s'] > 0 ? ( $e['sf'] ? '+' : '' ) . blockke_hm22_dev_short( $e['s'] ) : '',
				$e['r'] > 0 ? ( $e['rf'] ? '+' : '' ) . blockke_hm22_dev_short( $e['r'], true ) : '',
				(int) $e['b'],
				'' !== $e['ru'] ? $path( $e['ru'] ) : '',
				array_values( (array) $e['al'] ),
			);
			while ( count( $row ) > 3 && in_array( end( $row ), array( '', 0, array() ), true ) ) {
				array_pop( $row );
			}
			$out[] = $row;
		}
		return array( 'b' => $base, 'f' => (object) blockke_hm22_dev_feat( $d ), 'd' => $out );
	}

	/**
	 * The developments offered when nothing matches: those that suit the mode first (Rent: with rentals), then the rest, so the
	 * list is never short where few developments have rentals; within each, the homepage's own (config developments),
	 * then featured, then on the new layout, then the busiest.
	 */
	function blockke_hm22_dev_top( $d, $mode, $n = 6 ) {
		$ft   = blockke_hm22_dev_feat( $d );
		$rank = array();
		foreach ( $d as $i => $e ) {
			$w          = isset( $ft[ $i ] ) ? $ft[ $i ] : 999;
			$rank[ $i ] = array( blockke_hm22_dev_pref( $e['b'], $mode ) < 2 ? 1 : 0, $w, $e['ft'] ? 0 : 1, $e['vt'] ? 0 : 1, $i );
		}
		uasort(
			$rank,
			function ( $a, $b ) {
				return $a <=> $b;
			}
		);
		return array_slice( array_keys( $rank ), 0, $n );
	}

	/**
	 * For a name that matches nothing: the developments with a word starting like one of its words (three letters or more),
	 * most such words first, then as the mode prefers. When no name has such a word, those with a word two slips or fewer
	 * from one of its words (five letters or more, the same first letter, c = k: "kapela" for Capella).
	 */
	function blockke_hm22_dev_partial( $q, $d, $mode ) {
		$tw = array();
		foreach ( blockke_hm22_dev_prep( $q )['k'] as $t ) {
			if ( strlen( $t ) >= 3 && 'and' !== $t ) {
				$tw[ $t ] = true;
			}
		}
		$hit = array();
		foreach ( array( 'start', 'slip' ) as $how ) {
			foreach ( $tw && ! $hit ? $d : array() as $i => $e ) {
				$best = 0;
				foreach ( array_merge( array( $e['n'] ), (array) $e['al'] ) as $nm ) {
					$w = blockke_hm22_dev_prep( $nm )['w'];
					$c = 0;
					foreach ( array_keys( $tw ) as $t ) {
						$t = (string) $t;
						foreach ( $w as $u ) {
							if ( 'start' === $how ? 0 === strpos( $u, $t ) : ( strlen( $t ) >= 5 && strlen( $t ) <= 40 && strlen( $u ) <= 60 && blockke_hm22_dev_lead( $t, $u ) && blockke_hm22_dev_osa( $t, $u ) <= 2 ) ) {
								$c++;
								break;
							}
						}
					}
					$best = max( $best, $c );
				}
				if ( $best ) {
					$hit[] = array( (int) $i, $best, blockke_hm22_dev_pref( $e['b'], $mode ) );
				}
			}
		}
		usort(
			$hit,
			function ( $x, $y ) {
				return array( $y[1], $y[2], $x[0] ) <=> array( $x[1], $x[2], $y[0] );
			}
		);
		$out = array();
		foreach ( $hit as $h ) {
			$out[] = $h[0];
		}
		return $out;
	}

	/**
	 * The results panel for ?bkdev= (rendered fresh, never cached): v2.1's panel for development names, with the neighbourhoods
	 * that match listed too (v2.2). A clear match (blockke_hm22_pick()) comes first as the "Best match": the area's homes in the
	 * mode, or the development; otherwise the matches best first, or a plain "no match" with somewhere to go next.
	 */
	function blockke_hm22_dev_results( $d, $a, $q, $mode ) {
		$res  = '' === $q || ! $d ? array() : blockke_hm22_dev_find( $q, $d, $mode );
		$ares = '' === $q || ! $a ? array() : blockke_hm22_area_find( $q, $a, $mode );
		$res  = blockke_hm22_dev_trim( $res, $ares );
		$pick = '' === $q ? null : blockke_hm22_pick( $q, $d, $a, $res, $ares );
		$best = $pick && 'dev' === $pick[0] ? $pick[1] : null;
		$abest = $pick && 'area' === $pick[0] ? $pick[1] : null;
		$all  = count( $res );
		$near = $res && $res[0][1] < 35;
		$show = array();
		foreach ( array_slice( $res, 0, null !== $abest ? 6 : 12 ) as $r ) {
			$show[] = $r[0];
		}
		if ( null !== $best ) {
			$show = array_values( array_unique( array_merge( array( $best ), $show ) ) );
		}
		$ashow = array();
		foreach ( array_slice( $ares, 0, 6 ) as $r ) {
			$ashow[] = $r[0];
		}
		$any  = $show || $ashow;
		$part = ( $any || '' === $q || ! $d ) ? array() : array_slice( blockke_hm22_dev_partial( $q, $d, $mode ), 0, 6 );
		$top  = $any || ! $d ? array() : array_slice( array_values( array_unique( array_merge( $part, blockke_hm22_dev_top( $d, $mode ) ) ) ), 0, 6 );
		$atop = $any || ! $a ? array() : blockke_hm22_area_top( $a, $mode, 6 );
		$said = mb_strlen( $q ) > 48 ? rtrim( mb_substr( $q, 0, 47 ) ) . '…' : $q;
		$mw   = array( 'buy' => 'Homes for sale in', 'rent' => 'Homes to rent in', 'offplan' => 'Off-plan homes in' );
		$mp   = array( 'buy' => 'Property for sale in', 'rent' => 'Property to rent in', 'offplan' => 'Off-plan property in' ); // v2.2: an area whose listings in the mode are all land or commercial
		$meta = function ( $m ) { // v2.2: a tag after the place ("Off-plan", "To rent") never breaks at its hyphen or across lines
			$p = explode( ' · ', (string) $m );
			$o = esc_html( array_shift( $p ) );
			foreach ( $p as $x ) {
				$o .= ' · <span class="bkh-nw">' . esc_html( $x ) . '</span>';
			}
			return $o;
		};
		$item = function ( $i, $is_best ) use ( $d, $mode, $meta ) {
			$p  = blockke_hm22_dev_show( $d[ $i ], $mode );
			$o  = '<li' . ( $is_best ? ' class="is-best"' : '' ) . '><a class="bkh-dr-item' . ( $is_best ? ' is-best' : '' ) . '" href="' . esc_url( $p['url'] ) . '">';
			$o .= $is_best ? '<span class="bkh-dr-tag">Best match</span>' : '';
			$o .= '<span class="bkh-dr-name">' . esc_html( $d[ $i ]['n'] ) . '</span>';
			$o .= '' !== $p['meta'] ? '<span class="bkh-dr-meta">' . $meta( $p['meta'] ) . '</span>' : '';
			$o .= '<span class="bkh-dr-price">' . esc_html( $p['price'] ) . '</span>';
			$o .= $is_best ? '<span class="bkh-dr-cta">View the development' . blockke_hm22_svg( 'arrow' ) . '</span>' : '';
			return $o . '</a></li>';
		};
		$aitem = function ( $i, $is_best ) use ( $a, $d, $mode ) {
			$e  = $a[ $i ];
			$o  = '<li' . ( $is_best ? ' class="is-best"' : '' ) . '><a class="bkh-dr-item bkh-dr-area' . ( $is_best ? ' is-best' : '' ) . '" href="' . esc_url( blockke_hm22_area_go( $e, $mode, $d ) ) . '">';
			$o .= $is_best ? '<span class="bkh-dr-tag">Best match</span>' : '';
			$o .= '<span class="bkh-dr-name">' . esc_html( $e['n'] ) . '</span>';
			$o .= '<span class="bkh-dr-meta">' . esc_html( 'Neighbourhood' . ( '' !== $e['ci'] && 0 !== strcasecmp( $e['ci'], $e['n'] ) ? ' · ' . $e['ci'] : '' ) ) . '</span>';
			$o .= '<span class="bkh-dr-price">' . esc_html( blockke_hm22_area_say( $e, $mode ) ) . '</span>';
			$o .= $is_best ? '<span class="bkh-dr-cta">' . esc_html( blockke_hm22_area_cta( $e, $mode, $d ) ) . blockke_hm22_svg( 'arrow' ) . '</span>' : '';
			return $o . '</a></li>';
		};
		$o = '<div class="bkh-dr" id="bkh-dr" role="region" aria-labelledby="bkh-dr-h" data-where="home_dev"><div class="bkh-dr-head">';
		if ( '' === $q ) {
			$o .= '<h2 class="bkh-dr-title" id="bkh-dr-h">Search by <em>area or development</em></h2>';
			$o .= '<p class="bkh-dr-sub">Type a neighbourhood or a development&rsquo;s name &mdash; Westlands or Golden Hill, say &mdash; or start with one of these.</p>';
		} elseif ( ! $any ) {
			$o .= '<h2 class="bkh-dr-title" id="bkh-dr-h">No area or development called <em>&ldquo;' . esc_html( $said ) . '&rdquo;</em> on Block yet</h2>';
			$o .= '<p class="bkh-dr-sub">' . ( $part ? 'Check the spelling. The closest names come first.' : 'Check the spelling, or start with one of these.' ) . ' If it isn&rsquo;t listed, send us the name and we&rsquo;ll check it for you.</p>';
		} elseif ( null !== $abest && blockke_hm22_area_n( $a[ $abest ], $mode ) ) {
			$o .= '<h2 class="bkh-dr-title" id="bkh-dr-h">' . esc_html( blockke_hm22_area_hn( $a[ $abest ], $mode ) ? $mw[ $mode ] : $mp[ $mode ] ) . ' <em>' . esc_html( $a[ $abest ]['n'] ) . '</em></h2>';
		} elseif ( null !== $abest ) {
			$o .= '<h2 class="bkh-dr-title" id="bkh-dr-h"><em>' . esc_html( $a[ $abest ]['n'] ) . '</em></h2>';
			$o .= '<p class="bkh-dr-sub">' . esc_html( array( 'buy' => 'Nothing for sale is listed there at the moment', 'rent' => 'Nothing to rent is listed there at the moment', 'offplan' => 'No off-plan homes are listed there at the moment' )[ $mode ] ) . '; the area page shows what is.</p>';
		} elseif ( ! $show ) {
			$o .= '<h2 class="bkh-dr-title" id="bkh-dr-h">' . ( 1 === count( $ares ) ? 'One neighbourhood matches' : 'Neighbourhoods matching' ) . ' <em>&ldquo;' . esc_html( $said ) . '&rdquo;</em></h2>';
		} elseif ( $near && null !== $best ) {
			$o .= '<h2 class="bkh-dr-title" id="bkh-dr-h">Did you mean <em>' . esc_html( $d[ $best ]['n'] ) . '</em>?</h2>';
			$o .= '<p class="bkh-dr-sub">No exact match for &ldquo;' . esc_html( $said ) . '&rdquo;. This is the closest name on Block.</p>';
		} elseif ( $near ) {
			$o .= '<h2 class="bkh-dr-title" id="bkh-dr-h">No exact match for <em>&ldquo;' . esc_html( $said ) . '&rdquo;</em></h2>';
			$o .= '<p class="bkh-dr-sub">Did you mean ' . ( 1 === count( $show ) + count( $ashow ) ? 'this one' : 'one of these' ) . '?</p>';
		} elseif ( $ashow ) {
			$o .= '<h2 class="bkh-dr-title" id="bkh-dr-h">Areas and developments matching <em>&ldquo;' . esc_html( $said ) . '&rdquo;</em></h2>';
		} elseif ( 1 === $all ) {
			$o .= '<h2 class="bkh-dr-title" id="bkh-dr-h">One development matches <em>&ldquo;' . esc_html( $said ) . '&rdquo;</em></h2>';
		} else {
			$o .= '<h2 class="bkh-dr-title" id="bkh-dr-h">Developments matching <em>&ldquo;' . esc_html( $said ) . '&rdquo;</em></h2>';
			$o .= '<span class="bkh-dr-n">' . ( $all > 12 ? 'Best 12 of ' . (int) $all : (int) $all . ' developments' ) . '</span>';
			$o .= $all > 12 ? '<p class="bkh-dr-sub">Add another word of the name to narrow it down.</p>' : '';
		}
		$o    .= '</div>';
		$alist = $ashow ? $ashow : $atop;
		$dlist = $show ? $show : $top;
		$two   = $alist && $dlist;
		if ( null !== $abest ) {
			$alist = array_values( array_unique( array_merge( array( $abest ), $alist ) ) );
		}
		if ( $alist ) {
			$o .= $two ? '<h3 class="bkh-dr-g">Neighbourhoods</h3>' : '';
			$o .= '<ol class="bkh-dr-list">';
			foreach ( $alist as $pos => $i ) {
				$o .= $aitem( $i, null !== $abest && 0 === $pos );
			}
			$o .= '</ol>';
		}
		if ( $dlist ) {
			$o .= $two ? '<h3 class="bkh-dr-g">Developments</h3>' : '';
			$o .= '<ol class="bkh-dr-list">';
			foreach ( $dlist as $pos => $i ) {
				$o .= $item( $i, null !== $best && 0 === $pos );
			}
			$o .= '</ol>';
		}
		$o .= '<div class="bkh-dr-foot">';
		$o .= '<a class="bkh-more" href="' . esc_url( blockke_hm22_url( 'offplan' ) ) . '">All new developments' . blockke_hm22_svg( 'arrow' ) . '</a>';
		$topic = ( '' !== $q && ! $any ) ? 'a development called “' . $said . '”.' : 'a development.';
		$o .= '<a class="bkh-dr-wa" href="' . esc_url( blockke_hm22_wa( $topic ) ) . '" target="_blank" rel="noopener">' . blockke_hm22_svg( 'wa' ) . 'Ask us on WhatsApp</a>';
		$o .= '<a class="bkh-dr-alt" href="' . esc_url( blockke_hm22_url( 'consult' ) ) . '">Or book a free consultation</a>';
		return $o . '</div></div>';
	}

	/* ------------------------------------------------------------------ v2.2: images given by attachment id or URL */

	/**
	 * An image given as an attachment id (as on the live site) or a URL (a root-relative path or a full address). A URL with
	 * "{w}" in it names a set of copies, one per width in $widths (config hero_widths for the hero), for a srcset; its full size
	 * is $dim (config hero_size). Returns array( src, srcset, width, height ) or null.
	 */
	function blockke_hm22_src( $spec, $size = 'full', $widths = null, $dim = null ) {
		$cfg    = blockke_hm22_config();
		$widths = null === $widths ? $cfg['hero_widths'] : $widths;
		$dim    = null === $dim ? $cfg['hero_size'] : $dim;
		if ( is_numeric( $spec ) ) {
			$id  = (int) $spec;
			$src = $id ? wp_get_attachment_image_src( $id, $size ) : false;
			if ( ! $src ) {
				return null;
			}
			$set = wp_get_attachment_image_srcset( $id, $size );
			return array( $src[0], $set ? $set : '', (int) $src[1], (int) $src[2] );
		}
		$url = is_string( $spec ) ? trim( $spec ) : '';
		if ( '' === $url ) {
			return null;
		}
		if ( 0 === strpos( $url, '/' ) && 0 !== strpos( $url, '//' ) ) {
			$url = home_url( $url );
		}
		$dim = is_array( $dim ) && 2 === count( $dim ) ? array_map( 'intval', array_values( $dim ) ) : array( 0, 0 );
		if ( false === strpos( $url, '{w}' ) ) {
			return array( $url, '', $dim[0], $dim[1] );
		}
		$ws = array_values( array_unique( array_filter( array_map( 'intval', (array) $widths ) ) ) );
		sort( $ws );
		if ( ! $ws ) {
			return null;
		}
		$set = array();
		foreach ( $ws as $w ) {
			$set[] = str_replace( '{w}', (string) $w, $url ) . ' ' . $w . 'w';
		}
		$top = end( $ws );
		$h   = $dim[0] > 0 ? (int) round( $dim[1] * $top / $dim[0] ) : 0;
		return array( str_replace( '{w}', (string) $top, $url ), implode( ', ', $set ), $top, $h );
	}

	/**
	 * The hero photo: the first thing on the page, so it loads first (fetchpriority high, never lazy), with its width and height.
	 * It covers the hero, so it is drawn wider than the screen whenever the hero is taller than the photo's shape: on phones the
	 * hero is about 600px tall whatever the screen (it is as tall as its text), on larger screens at least 92% of the screen's
	 * height (at most 880px), which is what sizes says. On phones held upright, config hero_image_phone (an attachment id or a URL
	 * with {w}: a portrait crop of the same photo) is used instead when it is set, so a phone does not fetch the whole wide photo
	 * to show a third of it.
	 */
	function blockke_hm22_hero_img( $spec ) {
		$cfg = blockke_hm22_config();
		$s   = blockke_hm22_src( $spec );
		if ( ! $s ) {
			return '';
		}
		$ar = $s[2] > 0 && $s[3] > 0 ? $s[2] / $s[3] : 16 / 9;
		$o  = '<img class="bkh-hero-img" src="' . esc_url( $s[0] ) . '" alt=""';
		$fo = blockke_hm22_focus( isset( $cfg['hero_focus'] ) ? $cfg['hero_focus'] : '' ); // v2.2.1
		$o .= '' !== $fo ? ' style="object-position:' . esc_attr( $fo ) . '"' : '';
		if ( '' !== $s[1] ) {
			$tall = round( 0.92 * $ar, 3 ); // a screen narrower than this (width / height) shows the hero taller than the photo is wide
			$o   .= ' srcset="' . esc_attr( $s[1] ) . '" sizes="(max-width: 767px) ' . (int) ceil( 600 * $ar ) . 'px, (max-aspect-ratio: ' . (int) round( $tall * 1000 ) . '/1000) and (max-height: 956px) ' . (int) ceil( 92 * $ar ) . 'vh, (max-aspect-ratio: ' . (int) round( $tall * 1000 ) . '/1000) ' . (int) ceil( 880 * $ar ) . 'px, 100vw"';
		}
		if ( $s[2] > 0 && $s[3] > 0 ) {
			$o .= ' width="' . (int) $s[2] . '" height="' . (int) $s[3] . '"';
		}
		$o .= ' fetchpriority="high" decoding="async">';
		$p  = empty( $cfg['hero_image_phone'] ) ? null : blockke_hm22_src( $cfg['hero_image_phone'], 'full', $cfg['hero_phone_widths'], $cfg['hero_phone_size'] );
		if ( ! $p ) {
			return $o;
		}
		$par = $p[2] > 0 && $p[3] > 0 ? $p[2] / $p[3] : 0.75;
		$w   = (int) ceil( 620 * $par ); // the crop is drawn at least this wide on a phone (the hero is up to about 620px tall)
		$src = '<source media="(max-width: 767px) and (orientation: portrait)" srcset="' . esc_attr( '' !== $p[1] ? $p[1] : $p[0] ) . '"' . ( '' !== $p[1] ? ' sizes="(max-width: ' . $w . 'px) ' . $w . 'px, 100vw"' : '' );
		$src .= $p[2] > 0 && $p[3] > 0 ? ' width="' . (int) $p[2] . '" height="' . (int) $p[3] . '">' : '>';
		return '<picture class="bkh-hero-pic">' . $src . $o . '</picture>';
	}

	/** v2.2.1: config hero_focus as a CSS object-position ("center bottom", "50% 50%", "30% 70%", "left 20px top"), or '' for anything else. */
	function blockke_hm22_focus( $v ) {
		$v = is_string( $v ) ? strtolower( trim( (string) preg_replace( '/\s+/', ' ', $v ) ) ) : '';
		$t = '(?:left|right|top|bottom|center|-?\d{1,4}(?:\.\d{1,2})?(?:%|px)|0)';
		return preg_match( '/^' . $t . '(?: ' . $t . '){0,3}$/', $v ) ? $v : '';
	}

	/**
	 * v2.2.1: whether two images (each an attachment id or a URL) are the same photo: the same file in the uploads, whichever size copy,
	 * address form or {w} copy names it. An id is read as its attachment's file; nothing (0, '', an unknown id) is never the same.
	 */
	function blockke_hm22_same_img( $a, $b ) {
		$key = function ( $spec ) {
			$u = is_numeric( $spec ) ? ( (int) $spec > 0 ? wp_get_attachment_url( (int) $spec ) : '' ) : ( is_string( $spec ) ? trim( $spec ) : '' );
			$p = is_string( $u ) && '' !== $u ? (string) wp_parse_url( str_replace( '{w}', '', $u ), PHP_URL_PATH ) : '';
			return strtolower( (string) preg_replace( '/-(?:\d+x\d+|scaled)(?=\.[a-z0-9]+$)|-+(?=\.[a-z0-9]+$)/i', '', $p ) );
		};
		$ka = $key( $a );
		return '' !== $ka && $ka === $key( $b );
	}

	/** A full-width photo further down the page (lazy), with the sizes it is drawn at. */
	function blockke_hm22_band_img( $spec, $class, $sizes = '100vw' ) {
		$s = blockke_hm22_src( $spec, 'full', null, array() );
		if ( ! $s ) {
			return '';
		}
		$o = '<img class="' . esc_attr( $class ) . '" src="' . esc_url( $s[0] ) . '" alt=""';
		if ( '' !== $s[1] ) {
			$o .= ' srcset="' . esc_attr( $s[1] ) . '" sizes="' . esc_attr( $sizes instanceof Closure ? call_user_func( $sizes, $s[2] > 0 && $s[3] > 0 ? $s[2] / $s[3] : 1.5 ) : (string) $sizes ) . '"';
		}
		if ( $s[2] > 0 && $s[3] > 0 ) {
			$o .= ' width="' . (int) $s[2] . '" height="' . (int) $s[3] . '"';
		}
		return $o . ' loading="lazy" decoding="async">';
	}

	/** An attachment's original width / height (from its metadata), or 0 when it is not known. */
	function blockke_hm22_ratio( $att ) {
		$m = $att ? wp_get_attachment_metadata( (int) $att ) : null;
		return is_array( $m ) && ! empty( $m['width'] ) && ! empty( $m['height'] ) ? (int) $m['width'] / (int) $m['height'] : 0;
	}

	/**
	 * sizes for a photo that covers a tall card (object-fit: cover): a landscape photo is drawn as wide as the card is tall times
	 * its shape, so the browser is told that width, not the card's. $rows: array( media query or '', card width, card height ), both
	 * in the same unit ('px' or 'vw', given as array( number, unit )).
	 */
	function blockke_hm22_cover_sizes( $att, $rows ) {
		$ar  = blockke_hm22_ratio( $att );
		$ar  = $ar > 0 ? $ar : 1.5;
		$out = array();
		foreach ( $rows as $r ) {
			$v     = max( $r[1][0], $r[2][0] * $ar );
			$out[] = ( '' !== $r[0] ? $r[0] . ' ' : '' ) . (int) ceil( $v ) . $r[1][1];
		}
		return implode( ', ', $out );
	}

	/** Whether an attachment's original is at least $w x $h pixels (from its metadata; unknown = no). */
	function blockke_hm22_big( $att, $w, $h ) {
		$m = $att ? wp_get_attachment_metadata( (int) $att ) : null;
		return is_array( $m ) && ! empty( $m['width'] ) && ! empty( $m['height'] ) && (int) $m['width'] >= (int) $w && (int) $m['height'] >= (int) $h;
	}

	/** "Completion 2027", "Ready now" or '': the listing's stated completion, else its Completion term (as blockke_hm22_development()). */
	function blockke_hm22_comp( $id ) {
		$comp = get_post_meta( (int) $id, 'bke_completion_stated', true );
		if ( ! $comp ) {
			$ct = blockke_hm22_first_term( (int) $id, 'property_completion' );
			return $ct ? ( 'ready-now' === $ct->slug ? 'Ready now' : 'Completion ' . $ct->name ) : '';
		}
		return ( 0 === stripos( $comp, 'ready' ) ) ? (string) $comp : 'Completion ' . $comp;
	}

	/* ------------------------------------------------------------------ v2.2: neighbourhoods (the search and the grid) */

	/**
	 * Every area with listings, busiest first: array( 'v', 'st', 't', 'a' => list ). Each: n name, s slug, id term id, all live
	 * listings, c = array( for sale, to rent, off-plan for sale ), h = the same for homes only (not land or commercial), ci its main
	 * city, cs its cities (each city with at least a quarter of its listings, and the main one), u the area page (snippet 42706's
	 * area page when the area pages are on for it, else blockke_hm22_area_url()), ub / ur its homes for sale / to rent page when the
	 * area pages are on for it and it has such homes, else ''. Live = published, not Sold or Let. Kept in one transient
	 * (blockke_home_v22_areas), current while the content stamp is the same and it is younger than config cache_minutes.
	 */
	function blockke_hm22_area_index() {
		static $idx = null;
		if ( null !== $idx ) {
			return $idx;
		}
		$cfg  = blockke_hm22_config();
		$key  = 'blockke_home_v22_areas';
		$st   = blockke_hm22_stamp();
		$skip = isset( $_GET['bkhome_nocache'] ) || isset( $_GET['elementor-preview'] ) || is_preview(); // phpcs:ignore WordPress.Security.NonceVerification
		$hit  = $skip ? false : get_transient( $key );
		if ( is_array( $hit ) && isset( $hit['v'], $hit['st'], $hit['t'], $hit['a'] ) && 2 === $hit['v'] && $st === $hit['st'] && is_array( $hit['a'] ) && $hit['a'] && time() - (int) $hit['t'] < max( 1, (int) $cfg['cache_minutes'] ) * MINUTE_IN_SECONDS ) {
			$idx = $hit;
			return $idx;
		}
		$a   = blockke_hm22_tax_ready() ? blockke_hm22_area_build() : array();
		$idx = array( 'v' => 2, 'st' => $st, 't' => time(), 'a' => $a );
		if ( ! $skip && $a ) {
			set_transient( $key, $idx, 12 * HOUR_IN_SECONDS );
		} elseif ( ! $a ) {
			blockke_hm22_nostore( true );
		}
		return $idx;
	}

	function blockke_hm22_area_build() {
		$ids = get_posts( array(
			'post_type'      => 'estate_property',
			'post_status'    => 'publish',
			'posts_per_page' => 5000,
			'fields'         => 'ids',
			'no_found_rows'  => true,
			'orderby'        => 'ID',
			'order'          => 'ASC',
		) );
		$tx = array();
		foreach ( array_chunk( array_map( 'intval', $ids ), 500 ) as $chunk ) {
			$rel = wp_get_object_terms( $chunk, array( 'property_area', 'property_action_category', 'property_status', 'property_category', 'property_city', 'property_completion' ), array( 'fields' => 'all_with_object_id', 'update_term_meta_cache' => false ) );
			if ( is_wp_error( $rel ) ) {
				continue;
			}
			foreach ( $rel as $t ) {
				$tx[ (int) $t->object_id ][ $t->taxonomy ][ (int) $t->term_id ] = $t;
			}
		}
		$slugs = function ( $t, $tax ) {
			return isset( $t[ $tax ] ) ? array_values( wp_list_pluck( $t[ $tax ], 'slug' ) ) : array();
		};
		$land = array( 'land', 'residential-land', 'commercial-land', 'agricultural', 'industrial-land' );
		$comm = array( 'commercial', 'offices', 'warehouses', 'shop' );
		$home = array( 'apartments', 'villas-residential', 'houses-residential', 'residential', 'gated-community', 'studio-apartments', 'studio' );
		$per  = array();
		foreach ( $tx as $t ) {
			if ( empty( $t['property_area'] ) ) {
				continue;
			}
			$st = $slugs( $t, 'property_status' );
			if ( array_intersect( $st, array( 'sold', 'let' ) ) ) {
				continue;
			}
			$ac   = $slugs( $t, 'property_action_category' );
			$sale = in_array( 'for-sale', $ac, true );
			$rent = in_array( 'for-rent', $ac, true );
			if ( ! $sale && ! $rent ) {
				continue;
			}
			$ca   = $slugs( $t, 'property_category' );
			$hm   = ! array_intersect( $ca, $land ) && ( ! array_intersect( $ca, $comm ) || array_intersect( $ca, $home ) ) ? 1 : 0;
			$off  = $sale && in_array( 'offplan-ongoing', $st, true ) && ! in_array( 'ready-now', $slugs( $t, 'property_completion' ), true );
			$city = isset( $t['property_city'] ) ? html_entity_decode( (string) reset( $t['property_city'] )->name, ENT_QUOTES, 'UTF-8' ) : '';
			foreach ( $t['property_area'] as $tid => $term ) {
				if ( ! isset( $per[ $tid ] ) ) {
					$per[ $tid ] = array( 'term' => $term, 'all' => 0, 'c' => array( 0, 0, 0 ), 'h' => array( 0, 0, 0 ), 'ci' => array() );
				}
				$per[ $tid ]['all']++;
				foreach ( array( $sale, $rent, $off ) as $k => $yes ) {
					if ( $yes ) {
						$per[ $tid ]['c'][ $k ]++;
						$per[ $tid ]['h'][ $k ] += $hm;
					}
				}
				if ( '' !== $city ) {
					$per[ $tid ]['ci'][ $city ] = isset( $per[ $tid ]['ci'][ $city ] ) ? $per[ $tid ]['ci'][ $city ] + 1 : 1;
				}
			}
		}
		$out = array();
		foreach ( $per as $tid => $p ) {
			$term = $p['term'];
			$name = trim( html_entity_decode( (string) $term->name, ENT_QUOTES, 'UTF-8' ) );
			if ( '' === $name ) {
				continue;
			}
			arsort( $p['ci'] );
			$cs = array();
			foreach ( $p['ci'] as $cn => $k ) {
				if ( ! $cs || 4 * $k >= $p['all'] ) {
					$cs[] = (string) $cn;
				}
			}
			$on  = function_exists( 'blockke_area_on' ) && function_exists( 'blockke_area_url' ) && blockke_area_on( $term );
			$seg = function ( $slug ) use ( $term ) {
				$x = blockke_area_url( $term, $slug, false );
				return is_string( $x ) ? $x : '';
			};
			$au = $on ? $seg( '' ) : ''; // one kind of area page for every area: 42706's (the listings) when it is on, as the search's
			$au = '' !== $au ? $au : (string) blockke_hm22_area_url( $term );
			$out[] = array(
				'n'   => $name,
				's'   => (string) $term->slug,
				'id'  => (int) $tid,
				'all' => (int) $p['all'],
				'c'   => $p['c'],
				'h'   => $p['h'],
				'ci'  => $p['ci'] ? (string) key( $p['ci'] ) : '',
				'cs'  => $cs,
				'u'   => $au,
				'ub'  => $on && $p['h'][0] ? $seg( 'homes-for-sale' ) : '',
				'ur'  => $on && $p['h'][1] ? $seg( 'homes-for-rent' ) : '',
			);
		}
		usort(
			$out,
			function ( $x, $y ) {
				return $x['all'] !== $y['all'] ? $y['all'] - $x['all'] : strcasecmp( $x['n'], $y['n'] );
			}
		);
		return $out;
	}

	/** An area's live listings as the grid counts them (published, not Sold or Let), for "Know your block" too; else its term count. */
	function blockke_hm22_area_live( $term ) {
		foreach ( blockke_hm22_area_index()['a'] as $e ) {
			if ( (int) $e['id'] === (int) $term->term_id ) {
				return (int) $e['all'];
			}
		}
		return (int) $term->count;
	}

	/** The new developments index (snippet 42706's /new-developments/, which filters by ?area=) when it is on, else ''. */
	function blockke_hm22_index_url() {
		static $u = null;
		if ( null !== $u ) {
			return $u;
		}
		$u = '';
		if ( function_exists( 'blockke_index_page_id' ) && function_exists( 'blockke_bkn_on' ) && blockke_bkn_on( 'dev_index' ) ) {
			$id = (int) blockke_index_page_id();
			if ( $id && 'publish' === get_post_status( $id ) ) {
				$u = (string) get_permalink( $id );
			}
		}
		return $u;
	}

	/**
	 * Where an area goes in a mode: Buy its homes for sale, Rent its homes to rent (the area pages' own pages), Off-plan the new
	 * developments index for that area when the index lists an off-plan development there (dv: the names index, from 42706),
	 * else as Buy. Without such a page: the area page.
	 */
	function blockke_hm22_area_go( $e, $mode, $d = array() ) {
		if ( 'rent' === $mode ) {
			return '' !== $e['ur'] ? $e['ur'] : $e['u'];
		}
		if ( 'offplan' === $mode && $e['c'][2] && '' !== blockke_hm22_index_url() && blockke_hm22_area_has_offplan( $e, $d ) ) {
			return add_query_arg( 'area', rawurlencode( $e['s'] ), blockke_hm22_index_url() );
		}
		return '' !== $e['ub'] ? $e['ub'] : $e['u'];
	}

	/**
	 * Whether the new developments index shows a development for ?area=<this area's slug>. Decided from what the index itself
	 * filters on: the area slugs of the developments in snippet 42706's index grid (blockke_index_data(), cached by 42706). Without
	 * it, from the names index's places, each part of "Brookside, Westlands" counting as a place.
	 */
	function blockke_hm22_area_has_offplan( $e, $d ) {
		static $by = null;
		if ( null === $by ) {
			$by = array();
			$ix = function_exists( 'blockke_index_data' ) ? blockke_index_data() : null;
			if ( is_array( $ix ) && isset( $ix['grid'] ) && is_array( $ix['grid'] ) ) {
				foreach ( $ix['grid'] as $c ) {
					foreach ( isset( $c['areas'] ) ? (array) $c['areas'] : array() as $slug ) {
						$by[ 's:' . $slug ] = true;
					}
				}
			} else {
				foreach ( (array) $d as $x ) {
					if ( 5 === ( (int) $x['b'] & 5 ) ) {
						foreach ( explode( ',', (string) $x['pl'] ) as $part ) {
							$f = blockke_hm22_dev_prep( trim( $part ) )['f'];
							if ( '' !== $f ) {
								$by[ 'n:' . $f ] = true;
							}
						}
					}
				}
			}
		}
		return isset( $by[ 's:' . $e['s'] ] ) || isset( $by[ 'n:' . blockke_hm22_dev_prep( $e['n'] )['f'] ] );
	}

	/** The area's listings in a mode: Buy = for sale, Rent = to rent, Off-plan = off-plan for sale. */
	function blockke_hm22_area_n( $e, $mode ) {
		return (int) $e['c'][ 'rent' === $mode ? 1 : ( 'offplan' === $mode ? 2 : 0 ) ];
	}

	/** The area's homes (not land or commercial) in a mode. */
	function blockke_hm22_area_hn( $e, $mode ) {
		return (int) $e['h'][ 'rent' === $mode ? 1 : ( 'offplan' === $mode ? 2 : 0 ) ];
	}

	/**
	 * The words of the panel's link to an area, in step with where blockke_hm22_area_go() sends it: "See off-plan developments in
	 * Brookside" (the new developments index), "See homes for sale in Juja" (its homes page), "See Juja" (the area page).
	 */
	function blockke_hm22_area_cta( $e, $mode, $d ) {
		$g = blockke_hm22_area_go( $e, $mode, $d );
		if ( 'offplan' === $mode && '' !== blockke_hm22_index_url() && 0 === strpos( $g, blockke_hm22_index_url() ) && $g !== $e['u'] ) {
			return 'See off-plan developments in ' . $e['n'];
		}
		if ( '' !== $e['ub'] && $g === $e['ub'] ) {
			return 'See homes for sale in ' . $e['n'];
		}
		if ( '' !== $e['ur'] && $g === $e['ur'] ) {
			return 'See homes to rent in ' . $e['n'];
		}
		return 'See ' . $e['n'];
	}

	/** "214 for sale", "38 to rent", "12 off-plan", or that there are none. Same as asay() in the page script. */
	function blockke_hm22_area_say( $e, $mode ) {
		$n = blockke_hm22_area_n( $e, $mode );
		if ( 'rent' === $mode ) {
			return $n ? number_format( $n ) . ' to rent' : 'None listed to rent';
		}
		if ( 'offplan' === $mode ) {
			return $n ? number_format( $n ) . ' off-plan' : 'No off-plan listed';
		}
		return $n ? number_format( $n ) . ' for sale' : 'None listed for sale';
	}

	/**
	 * Words a search for homes in an area often carries ("2 bedroom apartments in Kilimani"), and the words around a place ("area",
	 * "county", "Kenya"), left out to read the area. Same as ANOISE in the page script.
	 */
	function blockke_hm22_area_noise() {
		return array( 'home' => 1, 'homes' => 1, 'house' => 1, 'houses' => 1, 'apartment' => 1, 'apartments' => 1, 'flat' => 1, 'flats' => 1, 'villa' => 1, 'villas' => 1, 'townhouse' => 1, 'townhouses' => 1, 'maisonette' => 1, 'maisonettes' => 1, 'studio' => 1, 'studios' => 1, 'bedroom' => 1, 'bedrooms' => 1, 'bedroomed' => 1, 'bed' => 1, 'beds' => 1, 'br' => 1, 'for' => 1, 'sale' => 1, 'rent' => 1, 'rental' => 1, 'rentals' => 1, 'to' => 1, 'let' => 1, 'buy' => 1, 'in' => 1, 'at' => 1, 'near' => 1, 'around' => 1, 'property' => 1, 'properties' => 1, 'plot' => 1, 'plots' => 1, 'offplan' => 1, 'off' => 1, 'plan' => 1, 'new' => 1, 'the' => 1, 'a' => 1, 'and' => 1, 'area' => 1, 'areas' => 1, 'neighbourhood' => 1, 'neighborhood' => 1, 'county' => 1, 'region' => 1, 'kenya' => 1 );
	}

	/** A word that is only a number, or a number of bedrooms written as one word ("3br", "2bedroom", "4bdr"). Same as num() in the page script. */
	function blockke_hm22_area_numword( $t ) {
		return (bool) preg_match( '/^[0-9]*(?:br|brs|bd|bdr|bdrm|bdrms|bed|beds|bedrm|bedroom|bedrooms|bedroomed)$|^[0-9]+$/', $t );
	}

	/** The cities the areas are in (property_city of their listings), folded => name. Same as CITY in the page script. */
	function blockke_hm22_area_cities( $a ) {
		static $c = null;
		if ( ! $a ) {
			return array();
		}
		if ( null === $c ) {
			$c = array();
			foreach ( (array) $a as $e ) {
				foreach ( isset( $e['cs'] ) ? (array) $e['cs'] : array() as $n ) {
					$f = blockke_hm22_dev_prep( $n )['f'];
					if ( '' !== $f ) {
						$c[ $f ] = (string) $n;
					}
				}
			}
		}
		return $c;
	}

	/** Whether the last word typed, still being typed, could be the start of one of those words ("westlands n" on the way to Nairobi). */
	function blockke_hm22_area_partial( $t, $a ) {
		foreach ( array( blockke_hm22_area_noise(), blockke_hm22_area_cities( $a ) ) as $set ) {
			foreach ( $set as $k => $v ) {
				if ( strlen( (string) $k ) > strlen( $t ) && 0 === strpos( (string) $k, $t ) ) {
					return true;
				}
			}
		}
		return false;
	}

	/**
	 * The query without those words, numbers and city names ("Westlands, Nairobi"), and without a last word that is only the start
	 * of one of them; null when that leaves nothing or changes nothing. Same as aq() in the page script.
	 */
	function blockke_hm22_area_q( $text, $a = array() ) {
		$q    = blockke_hm22_dev_prep( $text );
		$no   = blockke_hm22_area_noise();
		$ci   = blockke_hm22_area_cities( $a );
		$n    = count( $q['w'] );
		$keep = array();
		foreach ( $q['w'] as $i => $t ) {
			if ( isset( $no[ $t ] ) || isset( $ci[ $t ] ) || blockke_hm22_area_numword( $t ) ) {
				continue;
			}
			if ( $n > 1 && $i === $n - 1 && blockke_hm22_area_partial( $t, $a ) ) {
				continue;
			}
			$keep[] = $t;
		}
		if ( ! $keep || count( $keep ) === $n ) {
			return null;
		}
		$r = blockke_hm22_dev_prep( implode( ' ', $keep ) );
		return '' === $r['c'] ? null : $r;
	}

	/** The city a search names on its own ("mombasa", "homes in Kiambu"), folded, or ''. Same as acity() in the page script. */
	function blockke_hm22_area_city( $text, $a ) {
		$q    = blockke_hm22_dev_prep( $text );
		$no   = blockke_hm22_area_noise();
		$rest = '';
		foreach ( $q['w'] as $t ) {
			if ( ! isset( $no[ $t ] ) && ! blockke_hm22_area_numword( $t ) ) {
				$rest .= $t;
			}
		}
		$ci = blockke_hm22_area_cities( $a );
		return '' !== $rest && isset( $ci[ $rest ] ) ? $rest : '';
	}

	/**
	 * Areas matching $text, best first: array of array( index, score ), scored as development names are (blockke_hm22_dev_score()),
	 * the query read without the words of a homes search 1 lower. A search for a city on its own lists the areas in that city
	 * (85, after an area of exactly that name). Letters inside a word only when no area starts with them, near misses only when
	 * nothing better. Ties: more listings in the mode, then more in all, then A to Z. Same as afind() in the page script.
	 */
	function blockke_hm22_area_find( $text, $a, $mode ) {
		$q = blockke_hm22_dev_prep( $text );
		if ( '' === $q['c'] ) {
			return array();
		}
		$q1  = blockke_hm22_area_q( $text, $a );
		$city = blockke_hm22_area_city( $text, $a );
		$sc  = array();
		$top = 0;
		foreach ( $a as $i => $e ) {
			$m = blockke_hm22_dev_prep( $e['n'] );
			$s = blockke_hm22_dev_score( $q, $m );
			if ( $q1 ) {
				$s = max( $s, blockke_hm22_dev_score( $q1, $m ) - 1 );
			}
			if ( '' !== $city && $s < 85 ) {
				foreach ( isset( $e['cs'] ) ? (array) $e['cs'] : array() as $cn ) {
					if ( blockke_hm22_dev_prep( $cn )['f'] === $city ) {
						$s = 85;
					}
				}
			}
			$sc[ $i ] = $s;
			$top      = max( $top, $s );
		}
		$min = $top >= 60 ? 60 : ( $top >= 35 ? 35 : 1 );
		$out = array();
		foreach ( $sc as $i => $s ) {
			if ( $s >= $min ) {
				$out[] = array( (int) $i, $s );
			}
		}
		usort(
			$out,
			function ( $x, $y ) use ( $a, $mode ) {
				if ( $x[1] !== $y[1] ) {
					return $y[1] - $x[1];
				}
				$nx = blockke_hm22_area_n( $a[ $x[0] ], $mode );
				$ny = blockke_hm22_area_n( $a[ $y[0] ], $mode );
				if ( $nx !== $ny ) {
					return $ny - $nx;
				}
				if ( $a[ $x[0] ]['all'] !== $a[ $y[0] ]['all'] ) {
					return $a[ $y[0] ]['all'] - $a[ $x[0] ]['all'];
				}
				return strcasecmp( $a[ $x[0] ]['n'], $a[ $y[0] ]['n'] );
			}
		);
		return $out;
	}

	/** The busiest areas in a mode, for an empty search or one that matches nothing. */
	function blockke_hm22_area_top( $a, $mode, $n = 6 ) {
		$keys = array_keys( $a );
		usort(
			$keys,
			function ( $x, $y ) use ( $a, $mode ) {
				$nx = blockke_hm22_area_n( $a[ $x ], $mode );
				$ny = blockke_hm22_area_n( $a[ $y ], $mode );
				return $nx !== $ny ? $ny - $nx : ( $a[ $y ]['all'] !== $a[ $x ]['all'] ? $a[ $y ]['all'] - $a[ $x ]['all'] : $x - $y );
			}
		);
		return array_slice( $keys, 0, $n );
	}

	/**
	 * What a search clearly means: array( 'area', index ), array( 'dev', index ) or null (the results panel). An area when its name
	 * is typed (with or without the words of a homes search) and no development has exactly that name; an area typed with a slip,
	 * when it is the only area that matches and no development matches better than a near miss, and every word typed (five letters
	 * or more) is a slip or the start of a word of its name ("westlnds", "lavingtn"); a development when the development search is
	 * clear about it (blockke_hm22_dev_strong()) and no area matches as well; an area when it is the only one the words start and no
	 * development comes close. A city typed on its own ("mombasa") goes to the panel, which lists its areas, unless an area has
	 * exactly that name. Same as pick() in the page script.
	 */
	function blockke_hm22_pick( $text, $d, $a, $res, $ares ) {
		$q = blockke_hm22_dev_prep( $text );
		if ( '' === $q['c'] || $q['o'] ) {
			return null;
		}
		$a1 = $ares ? $ares[0][1] : 0;
		$a2 = count( $ares ) > 1 ? $ares[1][1] : 0;
		$d1 = $res ? $res[0][1] : 0;
		if ( $a1 >= 98 && $a2 < 98 && $d1 < 100 ) {
			return array( 'area', $ares[0][0] );
		}
		$city = '' !== blockke_hm22_area_city( $text, $a );
		if ( ! $city && 1 === count( $ares ) && $a1 < 35 && $d1 < 35 ) {
			$k = blockke_hm22_area_q( $text, $a );
			$k = $k ? $k : $q;
			if ( preg_match_all( '/[a-z]/', $k['c'] ) >= 5 ) {
				$w  = blockke_hm22_dev_prep( $a[ $ares[0][0] ]['n'] )['w'];
				$ok = true;
				foreach ( $k['k'] as $t ) {
					$hit = false;
					foreach ( $w as $u ) {
						if ( 0 === strpos( $u, $t ) || blockke_hm22_dev_slip( $t, $u ) ) {
							$hit = true;
							break;
						}
					}
					$ok = $ok && $hit;
				}
				if ( $ok ) {
					return array( 'area', $ares[0][0] );
				}
			}
		}
		$ds = blockke_hm22_dev_strong( $res, $text, $d );
		if ( null !== $ds && ( $d1 > $a1 || $a1 < 35 ) ) {
			return array( 'dev', $ds );
		}
		if ( ! $city && $a1 >= 60 && $a2 < 60 && $d1 < 35 ) {
			return array( 'area', $ares[0][0] );
		}
		return null;
	}

	/** With an area's name typed exactly, the developments that only match a stray word of the search ("homes in" Kilimani) are left out. Same as trim() in the page script. */
	function blockke_hm22_dev_trim( $res, $ares ) {
		if ( ! $ares || $ares[0][1] < 98 ) {
			return $res;
		}
		$out = array();
		foreach ( $res as $r ) {
			if ( $r[1] >= 60 ) {
				$out[] = $r;
			}
		}
		return $out;
	}

	/**
	 * The areas for the page script: one row each, [ name, slug, area page, Buy page, Rent page, Off-plan page, for sale, to rent,
	 * off-plan, all listings, its cities joined by "|" ], the pages as paths on this site ('' = the area page).
	 */
	function blockke_hm22_area_data( $a, $d ) {
		$home = untrailingslashit( home_url() );
		$rel  = function ( $u ) use ( $home ) {
			return ( '' !== $home && 0 === strpos( $u, $home . '/' ) ) ? substr( $u, strlen( $home ) ) : $u;
		};
		$out = array();
		foreach ( $a as $e ) {
			$u   = $rel( $e['u'] );
			$row = array( $e['n'], $e['s'], $u );
			foreach ( array( 'buy', 'rent', 'offplan' ) as $m ) {
				$g     = $rel( blockke_hm22_area_go( $e, $m, $d ) );
				$row[] = $g === $u ? '' : $g;
			}
			$out[] = array_merge( $row, array( (int) $e['c'][0], (int) $e['c'][1], (int) $e['c'][2], (int) $e['all'], implode( '|', isset( $e['cs'] ) ? (array) $e['cs'] : array() ) ) );
		}
		return $out;
	}

	/** "More filters": the search page for the mode (Off-plan: the new developments index when it is on, else the search). */
	function blockke_hm22_more_url( $mode ) {
		$s = blockke_hm22_url( 'search' );
		if ( 'rent' === $mode ) {
			return add_query_arg( 'property_action_category%5B%5D', 'for-rent', $s );
		}
		if ( 'offplan' === $mode ) {
			$ix = blockke_hm22_index_url();
			return '' !== $ix ? $ix : add_query_arg( array( 'property_action_category%5B%5D' => 'for-sale', 'completion' => 'any' ), $s );
		}
		return add_query_arg( 'property_action_category%5B%5D', 'for-sale', $s );
	}

	/**
	 * v2.2: the one search box. Buy | Rent | Off-plan as radio buttons (so the mode is sent without the page script too), one field for
	 * an area, a development or an estate, and Search. It is a GET form to the homepage (?bkdev=&bkfor=), so without the script the
	 * server answers with the results panel; the datalist offers every area and development name. The script turns it into a
	 * typeahead and goes straight to a clear match (blockke_hm22_pick()).
	 */
	function blockke_hm22_search_form( $d, $a, $q, $mode ) {
		$names = array();
		foreach ( $a as $e ) {
			$names[ $e['n'] ] = true;
		}
		foreach ( $d as $e ) {
			$names[ $e['n'] ] = true;
		}
		$names = array_keys( $names );
		usort( $names, 'strcasecmp' );
		$o  = '<form class="bkh-q" role="search" aria-label="Search areas and developments" method="get" action="' . esc_url( blockke_hm22_dev_action() . '#bkh-dr' ) . '" data-mode="' . esc_attr( $mode ) . '">';
		$o .= '<fieldset class="bkh-pills"><legend class="bkh-sr">Looking to</legend>';
		foreach ( array( 'buy' => 'Buy', 'rent' => 'Rent', 'offplan' => 'Off-plan' ) as $k => $l ) {
			$o .= '<label class="bkh-pill"><input type="radio" name="bkfor" value="' . esc_attr( $k ) . '"' . ( $mode === $k ? ' checked' : '' ) . '><span>' . esc_html( $l ) . '</span></label>';
		}
		$o .= '</fieldset>';
		/* the label holds the icon and the box's left edge, and the field fills the box's height: a tap anywhere on the white box but the button lands in the field */
		$o .= '<div class="bkh-q-box"><label class="bkh-q-lab" for="bkh-q-in"><span class="bkh-sr">Area, development or estate</span>' . blockke_hm22_svg( 'search' ) . '</label>';
		$o .= '<input id="bkh-q-in" type="search" name="bkdev" value="' . esc_attr( (string) $q ) . '" maxlength="80" autocomplete="off" autocorrect="off" autocapitalize="words" spellcheck="false" enterkeyhint="search" placeholder="Area, development or estate" list="bkh-q-list">';
		$o .= '<button type="submit" class="bkh-q-go"><span>Search</span>' . blockke_hm22_svg( 'arrow' ) . '</button></div>';
		$o .= '<datalist id="bkh-q-list">';
		foreach ( $names as $n ) {
			$o .= '<option value="' . esc_attr( $n ) . '">';
		}
		$o .= '</datalist>';
		$o .= '<a class="bkh-q-more" href="' . esc_url( blockke_hm22_more_url( $mode ) ) . '" data-buy="' . esc_url( blockke_hm22_more_url( 'buy' ) ) . '" data-rent="' . esc_url( blockke_hm22_more_url( 'rent' ) ) . '" data-offplan="' . esc_url( blockke_hm22_more_url( 'offplan' ) ) . '">More filters' . blockke_hm22_svg( 'arrow' ) . '</a>';
		return $o . '</form>';
	}

	/* ------------------------------------------------------------------ v2.2: developments carousel */

	/**
	 * The carousel's cards: the config developments first (blockke_hm22_development()), then developments for sale that are
	 * off-plan homes on the new layout (snippet 42706's, or bke_dev_layout = on without it) with a featured image of at least
	 * config carousel_min_px, featured first, then the busiest; up to config carousel_count. Each: slug, name, area, price
	 * ('' when none is stated), comp ('' when none), thumb, url.
	 */
	function blockke_hm22_carousel( $devix ) {
		$cfg  = blockke_hm22_config();
		$max  = max( 1, (int) $cfg['carousel_count'] );
		$px   = is_array( $cfg['carousel_min_px'] ) ? array_values( $cfg['carousel_min_px'] ) : array();
		$px   = isset( $px[0] ) && is_numeric( $px[0] ) ? array( (int) $px[0], isset( $px[1] ) && is_numeric( $px[1] ) ? (int) $px[1] : 0 ) : array( 1000, 700 );
		$out  = array();
		$have = array();
		foreach ( (array) $cfg['developments'] as $slug ) {
			if ( count( $out ) >= $max || ! is_string( $slug ) ) {
				continue;
			}
			$x = blockke_hm22_development( $slug );
			$t = $x ? get_term_by( 'slug', $slug, 'property_project' ) : null;
			if ( ! $x || ! $t ) {
				continue;
			}
			$have[ (int) $t->term_id ] = true;
			$out[] = array(
				'slug'  => $slug,
				'name'  => $x['name'],
				'area'  => $x['area'],
				'price' => 0 === strpos( $x['price'], 'From' ) ? $x['price'] : '',
				'comp'  => $x['comp'],
				'thumb' => $x['thumb'],
				'url'   => $x['url'],
			);
		}
		$d    = isset( $devix['d'] ) ? (array) $devix['d'] : array();
		$bkn  = isset( $devix['src'] ) && 'bkn' === $devix['src'];
		$cand = array();
		foreach ( count( $out ) < $max ? $d : array() as $i => $e ) {
			$b = (int) $e['b'];
			if ( 5 !== ( $b & 5 ) || ( $b & 24 ) || empty( $e['id'] ) || array_intersect_key( array_flip( (array) $e['pj'] ), $have ) ) {
				continue;
			}
			if ( ! ( $bkn ? ! empty( $e['vt'] ) : 'on' === (string) get_post_meta( (int) $e['id'], 'bke_dev_layout', true ) ) ) {
				continue;
			}
			$cand[] = array( $i, empty( $e['ft'] ) ? 1 : 0 );
		}
		usort(
			$cand,
			function ( $x, $y ) {
				return array( $x[1], $x[0] ) <=> array( $y[1], $y[0] );
			}
		);
		foreach ( $cand as $c ) {
			if ( count( $out ) >= $max ) {
				break;
			}
			$e  = $d[ $c[0] ];
			$th = (int) get_post_thumbnail_id( (int) $e['id'] );
			if ( ! $th || ! blockke_hm22_big( $th, $px[0], $px[1] ) ) {
				continue;
			}
			$pj    = (array) $e['pj'];
			$t     = $pj ? get_term( (int) $pj[0], 'property_project' ) : null;
			$out[] = array(
				'slug'  => $t && ! is_wp_error( $t ) ? (string) $t->slug : '',
				'name'  => (string) $e['n'],
				'area'  => (string) $e['pl'],
				'price' => $e['s'] > 0 ? ( $e['sf'] ? 'From ' : '' ) . blockke_hm22_price( $e['s'] ) : '',
				'comp'  => blockke_hm22_comp( (int) $e['id'] ),
				'thumb' => $th,
				'url'   => (string) $e['u'],
			);
			foreach ( $pj as $tid ) {
				$have[ (int) $tid ] = true;
			}
		}
		return $out;
	}

	/* ------------------------------------------------------------------ v2.2: where website enquiries come from */

	/**
	 * International calling codes (ITU, from the world-countries data, 5.1.0) => country: the code's digits, a space, the ISO code.
	 * A code several places share goes to an independent country, else to libphonenumber's main country for it (262: Reunion);
	 * +1 is the United States (with its territories) unless its area code belongs to Canada or another NANP member (those are
	 * listed with their area code).
	 */
	function blockke_hm22_cc_codes() {
		static $map = null;
		if ( null === $map ) {
			$map = array();
			foreach ( explode( ',', '1 US,20 EG,27 ZA,30 GR,31 NL,32 BE,33 FR,34 ES,36 HU,39 IT,40 RO,41 CH,43 AT,44 GB,45 DK,46 SE,47 NO,48 PL,49 DE,51 PE,52 MX,53 CU,54 AR,55 BR,56 CL,57 CO,58 VE,60 MY,61 AU,62 ID,63 PH,64 NZ,65 SG,66 TH,73 RU,74 RU,75 RU,76 KZ,77 KZ,78 RU,79 RU,81 JP,82 KR,84 VN,86 CN,90 TR,91 IN,92 PK,93 AF,94 LK,95 MM,98 IR,211 SS,212 MA,213 DZ,216 TN,218 LY,220 GM,221 SN,222 MR,223 ML,224 GN,225 CI,226 BF,227 NE,228 TG,229 BJ,230 MU,231 LR,232 SL,233 GH,234 NG,235 TD,236 CF,237 CM,238 CV,239 ST,240 GQ,241 GA,242 CG,243 CD,244 AO,245 GW,246 IO,247 SH,248 SC,249 SD,250 RW,251 ET,252 SO,253 DJ,254 KE,255 TZ,256 UG,257 BI,258 MZ,260 ZM,261 MG,262 RE,263 ZW,264 NA,265 MW,266 LS,267 BW,268 SZ,269 KM,290 SH,291 ER,297 AW,298 FO,299 GL,350 GI,351 PT,352 LU,353 IE,354 IS,355 AL,356 MT,357 CY,358 FI,359 BG,370 LT,371 LV,372 EE,373 MD,374 AM,375 BY,376 AD,377 MC,378 SM,379 VA,380 UA,381 RS,382 ME,383 XK,385 HR,386 SI,387 BA,389 MK,420 CZ,421 SK,423 LI,500 FK,501 BZ,502 GT,503 SV,504 HN,505 NI,506 CR,507 PA,508 PM,509 HT,590 GP,591 BO,592 GY,593 EC,594 GF,595 PY,596 MQ,597 SR,598 UY,599 CW,670 TL,672 NF,673 BN,674 NR,675 PG,676 TO,677 SB,678 VU,679 FJ,680 PW,681 WF,682 CK,683 NU,685 WS,686 KI,687 NC,688 TV,689 PF,690 TK,691 FM,692 MH,850 KP,852 HK,853 MO,855 KH,856 LA,880 BD,886 TW,960 MV,961 LB,962 JO,963 SY,964 IQ,965 KW,966 SA,967 YE,968 OM,970 PS,971 AE,972 IL,973 BH,974 QA,975 BT,976 MN,977 NP,992 TJ,993 TM,994 AZ,995 GE,996 KG,998 UZ,1204 CA,1226 CA,1236 CA,1242 BS,1246 BB,1249 CA,1250 CA,1263 CA,1264 AI,1268 AG,1284 VG,1289 CA,1306 CA,1343 CA,1345 KY,1354 CA,1365 CA,1367 CA,1368 CA,1382 CA,1387 CA,1403 CA,1416 CA,1418 CA,1428 CA,1431 CA,1437 CA,1438 CA,1441 BM,1450 CA,1468 CA,1473 GD,1474 CA,1506 CA,1514 CA,1519 CA,1548 CA,1579 CA,1581 CA,1584 CA,1587 CA,1600 CA,1604 CA,1613 CA,1622 CA,1633 CA,1639 CA,1644 CA,1647 CA,1649 TC,1655 CA,1664 MS,1672 CA,1677 CA,1683 CA,1688 CA,1705 CA,1709 CA,1721 SX,1742 CA,1753 CA,1758 LC,1767 DM,1778 CA,1780 CA,1782 CA,1784 VC,1807 CA,1809 DO,1819 CA,1825 CA,1829 DO,1849 DO,1867 CA,1868 TT,1869 KN,1873 CA,1876 JM,1879 CA,1902 CA,1905 CA,1942 CA,4779 SJ,35818 AX,2125288 EH,2125289 EH,3906698 VA' ) as $pair ) {
				$p = explode( ' ', $pair );
				if ( 2 === count( $p ) ) {
					$map[ $p[0] ] = $p[1];
				}
			}
		}
		return $map;
	}

	/** ISO code => array( latitude, longitude, short name, whether it is an independent country ) for every country in that table. */
	function blockke_hm22_cc_info() {
		static $info = null;
		if ( null === $info ) {
			$info = array();
			foreach ( explode( ';', 'AD 42.5 1.5 1 Andorra;AE 24 54 1 UAE;AF 33 65 1 Afghanistan;AG 17.1 -61.8 1 Antigua and Barbuda;AI 18.2 -63.2 0 Anguilla;AL 41 20 1 Albania;AM 40 45 1 Armenia;AO -12.5 18.5 1 Angola;AR -34 -64 1 Argentina;AT 47.3 13.3 1 Austria;AU -27 133 1 Australia;AW 12.5 -70 0 Aruba;AX 60.1 19.9 0 Åland Islands;AZ 40.5 47.5 1 Azerbaijan;BA 44 18 1 Bosnia and Herzegovina;BB 13.2 -59.5 1 Barbados;BD 24 90 1 Bangladesh;BE 50.8 4 1 Belgium;BF 13 -2 1 Burkina Faso;BG 43 25 1 Bulgaria;BH 26 50.5 1 Bahrain;BI -3.5 30 1 Burundi;BJ 9.5 2.2 1 Benin;BM 32.3 -64.8 0 Bermuda;BN 4.5 114.7 1 Brunei;BO -17 -65 1 Bolivia;BR -10 -55 1 Brazil;BS 24.2 -76 1 Bahamas;BT 27.5 90.5 1 Bhutan;BW -22 24 1 Botswana;BY 53 28 1 Belarus;BZ 17.2 -88.8 1 Belize;CA 60 -95 1 Canada;CD 0 25 1 DR Congo;CF 7 21 1 Central African Republic;CG -1 15 1 Republic of the Congo;CH 47 8 1 Switzerland;CI 8 -5 1 Ivory Coast;CK -21.2 -159.8 0 Cook Islands;CL -30 -71 1 Chile;CM 6 12 1 Cameroon;CN 35 105 1 China;CO 4 -72 1 Colombia;CR 10 -84 1 Costa Rica;CU 21.5 -80 1 Cuba;CV 16 -24 1 Cape Verde;CW 12.1 -68.9 0 Curaçao;CY 35 33 1 Cyprus;CZ 49.8 15.5 1 Czechia;DE 51 9 1 Germany;DJ 11.5 43 1 Djibouti;DK 56 10 1 Denmark;DM 15.4 -61.3 1 Dominica;DO 19 -70.7 1 Dominican Republic;DZ 28 3 1 Algeria;EC -2 -77.5 1 Ecuador;EE 59 26 1 Estonia;EG 27 30 1 Egypt;EH 24.5 -13 0 Western Sahara;ER 15 39 1 Eritrea;ES 40 -4 1 Spain;ET 8 38 1 Ethiopia;FI 64 26 1 Finland;FJ -18 175 1 Fiji;FK -51.8 -59 0 Falkland Islands;FM 6.9 158.2 1 Micronesia;FO 62 -7 0 Faroe Islands;FR 46 2 1 France;GA -1 11.8 1 Gabon;GB 54 -2 1 UK;GD 12.1 -61.7 1 Grenada;GE 42 43.5 1 Georgia;GF 4 -53 0 French Guiana;GH 8 -2 1 Ghana;GI 36.1 -5.3 0 Gibraltar;GL 72 -40 0 Greenland;GM 13.5 -16.6 1 Gambia;GN 11 -10 1 Guinea;GP 16.2 -61.6 0 Guadeloupe;GQ 2 10 1 Equatorial Guinea;GR 39 22 1 Greece;GT 15.5 -90.2 1 Guatemala;GW 12 -15 1 Guinea-Bissau;GY 5 -59 1 Guyana;HK 22.3 114.2 0 Hong Kong;HN 15 -86.5 1 Honduras;HR 45.2 15.5 1 Croatia;HT 19 -72.4 1 Haiti;HU 47 20 1 Hungary;ID -5 120 1 Indonesia;IE 53 -8 1 Ireland;IL 31.5 35.1 1 Israel;IN 20 77 1 India;IO -6 71.5 0 British Indian Ocean Territory;IQ 33 44 1 Iraq;IR 32 53 1 Iran;IS 65 -18 1 Iceland;IT 42.8 12.8 1 Italy;JM 18.2 -77.5 1 Jamaica;JO 31 36 1 Jordan;JP 36 138 1 Japan;KE 1 38 1 Kenya;KG 41 75 1 Kyrgyzstan;KH 13 105 1 Cambodia;KI 1.4 173 1 Kiribati;KM -12.2 44.2 1 Comoros;KN 17.3 -62.8 1 Saint Kitts and Nevis;KP 40 127 1 North Korea;KR 37 127.5 1 South Korea;KW 29.5 45.8 1 Kuwait;KY 19.5 -80.5 0 Cayman Islands;KZ 48 68 1 Kazakhstan;LA 18 105 1 Laos;LB 33.8 35.8 1 Lebanon;LC 13.9 -61 1 Saint Lucia;LI 47.3 9.5 1 Liechtenstein;LK 7 81 1 Sri Lanka;LR 6.5 -9.5 1 Liberia;LS -29.5 28.5 1 Lesotho;LT 56 24 1 Lithuania;LU 49.8 6.2 1 Luxembourg;LV 57 25 1 Latvia;LY 25 17 1 Libya;MA 32 -5 1 Morocco;MC 43.7 7.4 1 Monaco;MD 47 29 1 Moldova;ME 42.5 19.3 1 Montenegro;MG -20 47 1 Madagascar;MH 9 168 1 Marshall Islands;MK 41.8 22 1 North Macedonia;ML 17 -4 1 Mali;MM 22 98 1 Myanmar;MN 46 105 1 Mongolia;MO 22.2 113.5 0 Macau;MQ 14.7 -61 0 Martinique;MR 20 -12 1 Mauritania;MS 16.8 -62.2 0 Montserrat;MT 35.8 14.6 1 Malta;MU -20.3 57.5 1 Mauritius;MV 3.2 73 1 Maldives;MW -13.5 34 1 Malawi;MX 23 -102 1 Mexico;MY 2.5 112.5 1 Malaysia;MZ -18.2 35 1 Mozambique;NA -22 17 1 Namibia;NC -21.5 165.5 0 New Caledonia;NE 16 8 1 Niger;NF -29 167.9 0 Norfolk Island;NG 10 8 1 Nigeria;NI 13 -85 1 Nicaragua;NL 52.5 5.8 1 Netherlands;NO 62 10 1 Norway;NP 28 84 1 Nepal;NR -0.5 166.9 1 Nauru;NU -19 -169.9 0 Niue;NZ -41 174 1 New Zealand;OM 21 57 1 Oman;PA 9 -80 1 Panama;PE -10 -76 1 Peru;PF -15 -140 0 French Polynesia;PG -6 147 1 Papua New Guinea;PH 13 122 1 Philippines;PK 30 70 1 Pakistan;PL 52 20 1 Poland;PM 46.8 -56.3 0 Saint Pierre and Miquelon;PS 31.9 35.2 0 Palestine;PT 39.5 -8 1 Portugal;PW 7.5 134.5 1 Palau;PY -23 -58 1 Paraguay;QA 25.5 51.2 1 Qatar;RE -21.1 55.5 0 Réunion;RO 46 25 1 Romania;RS 44 21 1 Serbia;RU 60 100 1 Russia;RW -2 30 1 Rwanda;SA 25 45 1 Saudi Arabia;SB -8 159 1 Solomon Islands;SC -4.6 55.7 1 Seychelles;SD 15 30 1 Sudan;SE 62 15 1 Sweden;SG 1.4 103.8 1 Singapore;SH -15.9 -5.7 0 Saint Helena, Ascension and Tristan da Cunha;SI 46.1 14.8 1 Slovenia;SJ 78 20 0 Svalbard and Jan Mayen;SK 48.7 19.5 1 Slovakia;SL 8.5 -11.5 1 Sierra Leone;SM 43.8 12.4 1 San Marino;SN 14 -14 1 Senegal;SO 10 49 1 Somalia;SR 4 -56 1 Suriname;SS 7 30 1 South Sudan;ST 1 7 1 São Tomé and Príncipe;SV 13.8 -88.9 1 El Salvador;SX 18 -63 0 Sint Maarten;SY 35 38 1 Syria;SZ -26.5 31.5 1 Eswatini;TC 21.8 -71.6 0 Turks and Caicos Islands;TD 15 19 1 Chad;TG 8 1.2 1 Togo;TH 15 100 1 Thailand;TJ 39 71 1 Tajikistan;TK -9 -172 0 Tokelau;TL -8.8 125.9 1 Timor-Leste;TM 40 60 1 Turkmenistan;TN 34 9 1 Tunisia;TO -20 -175 1 Tonga;TR 39 35 1 Türkiye;TT 11 -61 1 Trinidad and Tobago;TV -8 178 1 Tuvalu;TW 23.5 121 0 Taiwan;TZ -6 35 1 Tanzania;UA 49 32 1 Ukraine;UG 1 32 1 Uganda;US 38 -97 1 USA;UY -33 -56 1 Uruguay;UZ 41 64 1 Uzbekistan;VA 41.9 12.4 1 Vatican City;VC 13.2 -61.2 1 Saint Vincent and the Grenadines;VE 8 -66 1 Venezuela;VG 18.4 -64.6 0 British Virgin Islands;VN 16.2 107.8 1 Vietnam;VU -16 167 1 Vanuatu;WF -13.3 -176.2 0 Wallis and Futuna;WS -13.6 -172.3 1 Samoa;XK 42.7 21.2 0 Kosovo;YE 15 48 1 Yemen;ZA -29 24 1 South Africa;ZM -15 30 1 Zambia;ZW -20 30 1 Zimbabwe' ) as $row ) {
				$p = explode( ' ', $row, 5 );
				if ( 5 === count( $p ) ) {
					$info[ $p[0] ] = array( (float) $p[1], (float) $p[2], $p[4], '1' === $p[3] );
				}
			}
		}
		return $info;
	}

	/** Calling code (1 to 3 digits) => the lengths a national number after it can have (libphonenumber-js 1.13.15's metadata). */
	function blockke_hm22_cc_lens() {
		static $lens = null;
		if ( null === $lens ) {
			$lens = array();
			foreach ( explode( ',', '1:7.10,7:10.14,20:8.9.10,27:5.6.7.8.9.10,30:10.11.12,31:5.6.7.8.9.10.11,32:8.9,33:9,34:9,36:8.9,39:6.7.8.9.10.11.12,40:6.9,41:9.12,43:4.5.6.7.8.9.10.11.12.13,44:7.9.10,45:8,46:6.7.8.9.10.12,47:5.8,48:6.7.8.9.10,49:4.5.6.7.8.9.10.11.12.13.14.15,51:8.9,52:10,53:6.7.8.10,54:10.11,55:8.9.10.11,56:9.10.11,57:8.10.11,58:10,60:8.9.10,61:5.6.7.8.9.10.12,62:7.8.9.10.11.12.13.14.15.16.17,63:6.8.9.10.11.12.13,64:5.6.7.8.9.10,65:8.10.11,66:8.9.10.13,81:8.9.10.11.12.13.14.15.16.17,82:5.6.8.9.10.11.12.13.14,84:7.8.9.10,86:7.8.9.10.11.12,90:7.10.12.13,91:8.9.10.11.12.13,92:8.9.10.11.12,93:9,94:9,95:6.7.8.9.10,98:4.5.6.7.10,211:9,212:9,213:8.9,216:8,218:9,220:7.9,221:9,222:8,223:8,224:8.9,225:10,226:8,227:8,228:8,229:8.10,230:7.8.10,231:7.8.9,232:8,233:8.9,234:10.11.12.13.14,235:8,236:8,237:8.9,238:7,239:7,240:9,241:7.8,242:9,243:7.8.9.10,244:9,245:7.9,246:7,247:5.6,248:7,249:9,250:8.9,251:9,252:6.7.8.9,253:8,254:7.8.9.10,255:9,256:9,257:8,258:8.9,260:9,261:9,262:9,263:7.9.10,264:8.9,265:7.9,266:8,267:7.8.10,268:8.9,269:7,290:4.5,291:7,297:7,298:6,299:6,350:8,351:9,352:4.5.6.7.8.9.10.11,353:7.8.9.10,354:7.9,355:6.7.8.9,356:8,357:8,358:5.6.7.8.9.10.11.12,359:6.7.8.9.12,370:8,371:8,372:7.8.10,373:8,374:8,375:6.7.8.9.10.11,376:6.8.9,377:8.9,378:8.10,380:9.10,381:6.7.8.9.10.11.12,382:8.9,383:8.9.10.11.12,385:7.8.9,386:5.6.7.8,387:8.9,389:8,420:9.10.11.12,421:6.7.9,423:7.9,500:5,501:7.11,502:8.11,503:7.8.11,504:8.11,505:8,506:8.10,507:7.8.10.11,508:6.9,509:8,590:9,591:8.9,592:7,593:8.9.10.11,594:9,595:6.7.8.9.10.11,596:9,597:6.7,598:4.5.6.7.8.9.10.11.12.13,599:7.8,670:7.8,672:6,673:7,674:7,675:7.8,676:5.7,677:5.7,678:5.7,679:7.11,680:7,681:6.9,682:5,683:4.7,685:5.6.7.10,686:5.8,687:6,688:5.6.7,689:6.8.9,690:4.5.6.7,691:7,692:7,850:8.10,852:5.6.7.8.9.11,853:7.8,855:8.9.10,856:8.9.10,880:6.7.8.9.10,886:7.8.9.10.11,960:7.10,961:7.8,962:8.9,963:8.9,964:8.9.10,965:7.8,966:9.10,967:7.8.9,968:7.8.9,970:8.9.10,971:5.6.7.8.9.10.11.12,972:7.8.9.10.11.12,973:8,974:7.8.9.11,975:7.8,976:8.9.10,977:8.10.11,992:9,993:8,994:9,995:9,996:9.10,998:9' ) as $pair ) {
				$p = explode( ':', $pair );
				if ( 2 === count( $p ) ) {
					$lens[ $p[0] ] = array_flip( array_map( 'intval', explode( '.', $p[1] ) ) );
				}
			}
		}
		return $lens;
	}

	/**
	 * The country of a stored phone number, or '' when it cannot be told for sure. The enquiry forms keep digits only and turn a
	 * Kenyan number typed as 07..., 01... or 7... into 2547... / 2541... (snippet 42706's blockke_dev_phone(), the same rules as
	 * the price check); a number given with + or 00 keeps its country code; anything else is kept as typed. So:
	 * - 254 counts as Kenya only for a mobile (2547 / 2541) or a Nairobi line (25420) with nine digits after 254. A foreign number
	 *   typed in its own local form with a leading 0 and ten digits (a UAE 050..., an Australian 04...) is stored as 254 too, but
	 *   in a range Kenyan mobiles do not use, so it is left out; one that lands in 2547 (a South African 07...) cannot be told apart.
	 * - Ten digits shaped like a US or Canadian number without its 1 (2025550123) are left out: they would otherwise read as
	 *   another country's code (20 is Egypt).
	 * - Otherwise the longest calling code the number starts with, and only when the digits after it have a length that country's
	 *   numbers can have (a UK 7700 900123 saved without 44 is not Kazakhstan); a +1 number must be a whole NANP number.
	 * The same clean-up as the forms is applied first, for rows saved by a form that did not apply it.
	 */
	function blockke_hm22_cc( $raw ) {
		$p = blockke_hm22_cc_norm( $raw );
		if ( strlen( $p ) < 8 || strlen( $p ) > 15 ) {
			return '';
		}
		if ( 0 === strpos( $p, '254' ) ) {
			return preg_match( '/^254(?:[17]\d{8}|20\d{7})$/', $p ) ? 'KE' : '';
		}
		if ( preg_match( '/^[2-9]\d\d[2-9]\d{6}$/', $p ) ) {
			return '';
		}
		$lens = blockke_hm22_cc_lens();
		for ( $n = 3; $n >= 1; $n-- ) {
			$code = substr( $p, 0, $n );
			if ( isset( $lens[ $code ] ) ) {
				if ( ! isset( $lens[ $code ][ strlen( $p ) - $n ] ) || ( '1' === $code && ! preg_match( '/^1[2-9]\d\d[2-9]\d{6}$/', $p ) ) ) {
					return '';
				}
				break;
			}
		}
		if ( $n < 1 ) {
			return '';
		}
		$map = blockke_hm22_cc_codes();
		for ( $k = min( 4, strlen( $p ) - 4 ); $k >= 1; $k-- ) {
			if ( isset( $map[ substr( $p, 0, $k ) ] ) ) {
				return $map[ substr( $p, 0, $k ) ];
			}
		}
		return '';
	}

	/** A phone number as the forms store it: digits only, 00 read as the international prefix, a Kenyan local number as 254... */
	function blockke_hm22_cc_norm( $raw ) {
		$p = preg_replace( '/\D+/', '', is_scalar( $raw ) ? (string) $raw : '' );
		if ( 0 === strpos( $p, '00' ) ) {
			return substr( $p, 2 );
		}
		if ( 10 === strlen( $p ) && '0' === $p[0] ) {
			return '254' . substr( $p, 1 );
		}
		if ( 9 === strlen( $p ) && ( '7' === $p[0] || '1' === $p[0] ) ) {
			return '254' . $p;
		}
		if ( 13 === strlen( $p ) && 0 === strpos( $p, '2540' ) ) {
			return '254' . substr( $p, 4 );
		}
		return $p;
	}

	/**
	 * Whether a stored enquiry is one the team sent itself, a test, or one from an editor's preview: a block.ke e-mail address, a
	 * name with the word "test", a page address with a preview switch, or a number from the ranges set aside for fiction that the
	 * forms' own examples use (UK 07700 900xxx, North American 555-01xx). Read here only, never kept.
	 */
	function blockke_hm22_dia_skip( $row, $digits ) {
		$mail = isset( $row['email'] ) && is_scalar( $row['email'] ) ? strtolower( trim( (string) $row['email'] ) ) : '';
		$name = isset( $row['name'] ) && is_scalar( $row['name'] ) ? (string) $row['name'] : '';
		$page = isset( $row['page'] ) && is_scalar( $row['page'] ) ? (string) $row['page'] : '';
		$d    = blockke_hm22_cc_norm( $digits );
		return (bool) ( preg_match( '/@(?:[a-z0-9-]+\.)*block\.ke$/', $mail ) || preg_match( '/\btest(?:ing)?\b/i', $name )
			|| preg_match( '/[?&](?:preview|preview_id|elementor-preview|bke_(?:layout|rent|area|forms|cards))=/i', $page )
			|| preg_match( '/^(?:447700900\d{3}|1[2-9]\d\d55501\d\d)$/', $d ) );
	}

	/**
	 * Where the website's enquiries came from, as counts only: the stored enquiries of the forms that keep them (option
	 * blockke_dev_leads: snippet 42706's development, landing, area, index and topic page forms, the last 200; blockke_buyer_leads:
	 * the price-check popup; blockke_consult_leads: the consultation page), in the last config diaspora_days, by the country code
	 * of each phone number (blockke_hm22_cc()). Rows without a number that can be placed, Block's own numbers, the team's tests
	 * (blockke_hm22_dia_skip()) and repeats (the same number on the same day, in any of the three) are left out. Returns array(
	 * total, abroad, by => ISO code => count, busiest first, from => the first counted enquiry (a timestamp), full => whether the
	 * stored enquiries reach back past the period ), cached for six hours in transient blockke_home_v22_dia. No name, number or
	 * e-mail address is kept or shown: they are only read here, one row at a time.
	 */
	function blockke_hm22_dia() {
		static $res = false;
		if ( false !== $res ) {
			return $res;
		}
		$cfg  = blockke_hm22_config();
		$res  = null;
		$days = max( 30, (int) $cfg['diaspora_days'] );
		if ( empty( $cfg['diaspora_stats'] ) ) {
			return $res;
		}
		$key  = 'blockke_home_v22_dia';
		$skip = isset( $_GET['bkhome_nocache'] ) || isset( $_GET['elementor-preview'] ) || is_preview(); // phpcs:ignore WordPress.Security.NonceVerification
		$hit  = $skip ? false : get_transient( $key );
		if ( is_array( $hit ) && isset( $hit['v'], $hit['days'], $hit['total'], $hit['abroad'], $hit['by'], $hit['from'], $hit['full'] ) && 2 === $hit['v'] && $days === $hit['days'] ) {
			$res = $hit;
			return $res;
		}
		$tz    = wp_timezone();
		$since = time() - $days * DAY_IN_SECONDS;
		$own   = array();
		foreach ( array( $cfg['whatsapp'], $cfg['phone'] ) as $n ) {
			$own[ blockke_hm22_cc_norm( $n ) ] = true;
		}
		$seen  = array();
		$by    = array();
		$total = 0;
		$first = 0;
		$full  = false;
		foreach ( array( 'blockke_dev_leads', 'blockke_buyer_leads', 'blockke_consult_leads' ) as $opt ) {
			$log = get_option( $opt, array() );
			foreach ( is_array( $log ) ? $log : array() as $row ) {
				if ( ! is_array( $row ) || empty( $row['time'] ) || ! is_string( $row['time'] ) || ! isset( $row['phone'] ) || ! is_scalar( $row['phone'] ) ) {
					continue;
				}
				$dt = date_create( $row['time'], $tz );
				if ( ! $dt ) {
					continue;
				}
				$ts = (int) $dt->getTimestamp();
				if ( $ts < $since ) {
					$full = true;
					continue;
				}
				$p  = blockke_hm22_cc_norm( $row['phone'] );
				$cc = blockke_hm22_cc( $p );
				if ( '' === $cc || isset( $own[ $p ] ) || blockke_hm22_dia_skip( $row, $p ) ) {
					continue;
				}
				$once = md5( $p . '|' . substr( $row['time'], 0, 10 ) );
				if ( isset( $seen[ $once ] ) ) {
					continue;
				}
				$seen[ $once ] = true;
				$total++;
				$first = $first ? min( $first, $ts ) : $ts;
				if ( 'KE' !== $cc ) {
					$by[ $cc ] = isset( $by[ $cc ] ) ? $by[ $cc ] + 1 : 1;
				}
			}
			unset( $log );
		}
		unset( $seen );
		$info = blockke_hm22_cc_info();
		uksort(
			$by,
			function ( $x, $y ) use ( $by, $info ) {
				return $by[ $x ] !== $by[ $y ] ? $by[ $y ] - $by[ $x ] : strcasecmp( isset( $info[ $x ] ) ? $info[ $x ][2] : $x, isset( $info[ $y ] ) ? $info[ $y ][2] : $y );
			}
		);
		$res = array( 'v' => 2, 'days' => $days, 'total' => $total, 'abroad' => (int) array_sum( $by ), 'by' => $by, 'from' => $first, 'full' => $full );
		if ( ! $skip ) {
			set_transient( $key, $res, 6 * HOUR_IN_SECONDS );
		}
		return $res;
	}

	/** "countries" or "countries and territories" (Hong Kong, Reunion ...), for the places those counts name. */
	function blockke_hm22_dia_places( $by ) {
		$info = blockke_hm22_cc_info();
		foreach ( array_keys( (array) $by ) as $cc ) {
			if ( isset( $info[ $cc ] ) && ! $info[ $cc ][3] ) {
				return 'countries and territories';
			}
		}
		return 'countries';
	}

	/** Whether those counts clear config diaspora_min (enquiries in all, from abroad, countries besides Kenya). */
	function blockke_hm22_dia_ok( $s ) {
		$cfg = blockke_hm22_config();
		$min = is_array( $cfg['diaspora_min'] ) ? $cfg['diaspora_min'] : array();
		$get = function ( $k, $v ) use ( $min ) {
			return isset( $min[ $k ] ) ? max( 1, (int) $min[ $k ] ) : $v;
		};
		return is_array( $s ) && $s['total'] >= $get( 'enquiries', 30 ) && $s['abroad'] >= $get( 'abroad', 5 ) && count( $s['by'] ) >= $get( 'countries', 3 );
	}

	/**
	 * A dotted world map (inline SVG, no images or scripts): land as dots every 2 degrees (Natural Earth 1:110m, drawn as dashed
	 * rows), a brass dot on each country the enquiries came from, and Nairobi. Plate carree: x = longitude, y = minus latitude.
	 */
	function blockke_hm22_map_svg( $by, $label ) {
		$info = blockke_hm22_cc_info();
		$nx   = 36.8;
		$ny   = -1.3;
		$arcs = '';
		$dots = '';
		foreach ( array_keys( (array) $by ) as $cc ) {
			if ( ! isset( $info[ $cc ] ) ) {
				continue;
			}
			$x     = round( $info[ $cc ][1], 1 );
			$y     = round( -$info[ $cc ][0], 1 );
			$mx    = round( ( $nx + $x ) / 2, 1 );
			$my    = round( min( $ny, $y ) - abs( $x - $nx ) * 0.22 - 4, 1 );
			$arcs .= 'M' . $nx . ' ' . $ny . 'Q' . $mx . ' ' . $my . ' ' . $x . ' ' . $y;
			$dots .= '<circle cx="' . $x . '" cy="' . $y . '" r="2.1"/>';
		}
		$o  = '<svg class="bkh-map" viewBox="-180 -84 360 142" role="img" aria-labelledby="bkh-map-t" focusable="false"><title id="bkh-map-t">' . esc_html( $label ) . '</title>';
		$o .= '<path class="bkh-map-land" d="M-81 -83h14M-43 -83h18M-93 -81h0M-89 -81h20M-63 -81h48M95 -81h0M-105 -79h2M-93 -79h4M-85 -79h8M-67 -79h46M13 -79h8M97 -79h6M-119 -77h2M-95 -77h0M-89 -77h8M-71 -77h52M17 -77h0M103 -77h2M-111 -75h4M-99 -75h0M-95 -75h0M-91 -75h10M-57 -75h36M57 -75h2M87 -75h24M139 -75h4M147 -75h2M-123 -73h4M-115 -73h0M-107 -73h0M-99 -73h0M-95 -73h2M-89 -73h6M-79 -73h2M-55 -73h32M53 -73h2M81 -73h40M125 -73h2M-179 -71h0M-157 -71h2M-123 -71h0M-117 -71h12M-95 -71h2M-87 -71h14M-51 -71h24M-23 -71h0M25 -71h2M55 -71h0M67 -71h4M75 -71h16M131 -71h0M153 -71h2M159 -71h18M-163 -69h12M-137 -69h0M-117 -69h2M-107 -69h2M-101 -69h2M-95 -69h0M-87 -69h0M-81 -69h4M-67 -69h14M-25 -69h42M37 -69h22M67 -69h0M73 -69h0M169 -69h0M-173 -67h8M-81 -67h6M-63 -67h8M-33 -67h46M43 -67h0M47 -67h0M73 -67h0M-179 -65h2M-171 -65h4M-87 -65h0M-83 -65h4M-65 -65h12M-39 -65h16M-13 -65h24M23 -65h2M35 -65h0M39 -65h0M-163 -63h72M-71 -63h2M-65 -63h0M-49 -63h6M9 -63h8M23 -63h156M-165 -61h12M-149 -61h54M-77 -61h6M-47 -61h4M7 -61h10M23 -61h132M161 -61h0M165 -61h6M-161 -59h6M-137 -59h42M-77 -59h6M-65 -59h0M7 -59h10M25 -59h116M153 -59h0M161 -59h0M-157 -57h0M-133 -57h42M-75 -57h12M-5 -57h2M9 -57h0M13 -57h2M23 -57h114M157 -57h6M-163 -55h0M-129 -55h46M-77 -55h18M-7 -55h4M9 -55h0M21 -55h114M157 -55h4M-129 -53h46M-77 -53h20M-9 -53h2M-3 -53h2M5 -53h138M157 -53h0M-127 -51h70M-3 -51h2M3 -51h136M143 -51h0M-125 -49h0M-121 -49h52M-65 -49h0M-57 -49h2M-1 -49h140M143 -49h0M-123 -47h58M-53 -47h0M-1 -47h138M143 -47h0M-123 -45h54M-65 -45h2M-1 -45h12M15 -45h14M35 -45h0M39 -45h8M53 -45h82M-123 -43h52M-9 -43h12M11 -43h2M19 -43h8M41 -43h6M53 -43h80M141 -43h2M-123 -41h50M-7 -41h6M9 -41h0M15 -41h2M21 -41h6M31 -41h18M55 -41h74M141 -41h0M-123 -39h46M-9 -39h8M17 -39h0M21 -39h2M27 -39h22M55 -39h62M127 -39h0M141 -39h0M-121 -37h44M-5 -37h2M7 -37h4M15 -37h0M29 -37h20M55 -37h66M127 -37h2M137 -37h2M-119 -35h42M-5 -35h14M25 -35h0M33 -35h0M37 -35h82M127 -35h0M133 -35h6M-117 -33h36M-7 -33h18M37 -33h82M131 -33h2M-115 -31h32M-9 -31h26M21 -31h6M31 -31h90M-111 -29h14M-81 -29h0M-9 -29h56M51 -29h70M-113 -27h0M-109 -27h10M-81 -27h0M-13 -27h46M37 -27h12M55 -27h64M-111 -25h0M-107 -25h8M-13 -25h46M39 -25h12M55 -25h0M67 -25h52M-105 -23h6M-83 -23h2M-15 -23h50M39 -23h20M71 -23h44M121 -23h0M-105 -21h6M-89 -21h2M-77 -21h0M-17 -21h54M41 -21h16M71 -21h16M93 -21h14M-103 -19h6M-91 -19h2M-71 -19h0M-15 -19h52M43 -19h14M73 -19h10M95 -19h10M109 -19h0M-99 -17h10M-15 -17h52M43 -17h10M75 -17h6M95 -17h12M121 -17h0M-91 -15h6M-17 -15h56M43 -15h6M75 -15h4M99 -15h8M121 -15h0M-87 -13h2M-15 -13h56M45 -13h0M75 -13h4M99 -13h10M121 -13h0M-85 -11h0M-75 -11h6M-15 -11h58M49 -11h2M77 -11h2M99 -11h0M105 -11h2M125 -11h0M-83 -9h0M-79 -9h0M-75 -9h14M-13 -9h62M77 -9h0M81 -9h0M99 -9h0M105 -9h0M-77 -7h18M-11 -7h60M81 -7h0M125 -7h0M-77 -5h24M-9 -5h2M-3 -5h0M7 -5h40M97 -5h0M101 -5h2M115 -5h2M-77 -3h26M11 -3h34M99 -3h0M103 -3h0M113 -3h4M-79 -1h28M11 -1h32M99 -1h2M111 -1h6M121 -1h0M-79 1h32M9 1h32M101 1h2M111 1h6M131 1h2M-79 3h38M11 3h28M103 3h2M111 3h4M119 3h2M129 3h0M133 3h0M137 3h4M-81 5h44M13 5h26M105 5h0M123 5h0M139 5h6M-79 7h44M13 7h26M107 7h4M139 7h8M157 7h0M-77 9h40M13 9h26M117 9h0M125 9h0M141 9h2M147 9h0M161 9h0M-77 11h38M15 11h24M-75 13h36M13 13h26M49 13h0M131 13h4M143 13h0M-75 15h36M13 15h26M49 15h0M125 15h10M143 15h2M167 15h0M-71 17h30M13 17h24M45 17h4M123 17h14M143 17h2M-69 19h28M13 19h22M45 19h4M123 19h22M-69 21h28M15 21h20M45 21h2M117 21h32M165 21h0M-69 23h24M15 23h20M45 23h2M115 23h34M-69 25h20M15 25h18M45 25h0M115 25h36M-69 27h20M17 27h14M115 27h38M-71 29h20M17 29h14M115 29h38M-71 31h20M19 31h10M117 31h36M-71 33h18M19 33h8M117 33h6M135 33h16M-71 35h12M117 35h0M139 35h10M173 35h0M-73 37h16M141 37h8M175 37h0M-73 39h10M175 39h2M-73 41h6M-63 41h0M145 41h2M173 41h2M-71 43h6M147 43h0M171 43h2M-73 45h6M169 45h2M-73 47h6M-75 49h6M69 49h0M-75 51h4M-73 53h4M-71 55h2"/>';
		$o .= '' !== $arcs ? '<path class="bkh-map-arc" d="' . esc_attr( $arcs ) . '"/>' : '';
		$o .= '<g class="bkh-map-dot">' . $dots . '</g>';
		$o .= '<circle class="bkh-map-home" cx="' . $nx . '" cy="' . $ny . '" r="2.4"/><circle class="bkh-map-ring" cx="' . $nx . '" cy="' . $ny . '" r="5"/>';
		return $o . '</svg>';
	}

	/* ------------------------------------------------------------------ v2.2: "Worth a viewing" cards */

	/** The status tab on a listing card, from the listing's own data: "To rent", "Off-plan", "Ready", or ''. */
	function blockke_hm22_tab( $id ) {
		$id = (int) $id;
		if ( has_term( 'for-rent', 'property_action_category', $id ) ) {
			return 'To rent';
		}
		$ready = has_term( 'ready-now', 'property_completion', $id );
		if ( has_term( 'offplan-ongoing', 'property_status', $id ) && ! $ready ) {
			return 'Off-plan';
		}
		if ( $ready || has_term( 'complete', 'property_status', $id ) ) {
			return 'Ready';
		}
		return '';
	}

	/* ------------------------------------------------------------------ CSS */

	function blockke_hm22_css() {
		return <<<'CSS'
.bkh{--navy:#0D2440;--navy-2:#16345C;--navy-3:#091A2E;--ink:#22303C;--slate:#46586A;--muted:#5F6D7E;--paper:#FBF9F6;--mist:#F4EEE4;--line:#E6DFD4;--line-2:#D5CBBB;--brass:#B98A44;--brass-d:#80602A;--brass-l:#DDB97F;--brass-tint:#F1E4CC;--on-navy:#C9D3DF;--wa:#1EBE5D;--font:"Montserrat",system-ui,-apple-system,"Segoe UI",Roboto,sans-serif;--display:"Instrument Serif",Georgia,"Times New Roman",serif;--max:1200px;--gutter:clamp(20px,4vw,48px);--sec:clamp(72px,9vw,128px);--black:#0B0E13;--lap:clamp(28px,4vw,48px);--lapr:clamp(24px,3.4vw,44px);
position:relative;display:block;float:none;clear:both;width:100%;max-width:100%;margin:0;padding:0;text-align:left;font:400 16px/1.6 var(--font);color:var(--ink);background:var(--paper);-webkit-font-smoothing:antialiased;overflow-x:clip}
.bkh *,.bkh *::before,.bkh *::after{box-sizing:border-box}
.bkh :is(h1,h2,h3,h4,p,ul,ol,li,figure){margin:0;padding:0;border:0;background:none;list-style:none;text-transform:none;letter-spacing:normal}
.bkh li::before,.bkh li::after{content:none!important;display:none!important}
.bkh a{color:inherit;text-decoration:none;transition:color .2s,opacity .2s;box-shadow:none}
.bkh img{display:block;max-width:100%;height:auto;border:0}
.bkh button,.bkh select{font:inherit;color:inherit}
.bkh svg{width:16px;height:16px;flex:none}
.bkh [hidden]{display:none!important}
.bkh h3{font-family:var(--font)!important;margin:0!important;padding:0!important;letter-spacing:normal!important;text-transform:none!important}
.bkh .bkh-wrap{max-width:var(--max);margin:0 auto;padding:0 var(--gutter)}
.bkh .bkh-eyebrow{display:block;font:600 11px/1.4 var(--font);letter-spacing:.2em;text-transform:uppercase;color:var(--brass)}
.bkh :is(h1,h2).bkh-display{font-family:var(--display)!important;font-weight:400!important;letter-spacing:-.012em!important;line-height:1.06!important;color:var(--navy)!important;text-transform:none!important}
.bkh h2.bkh-display{font-size:clamp(34px,4.1vw,52px)!important;margin:14px 0 0}
.bkh .bkh-lede{font-size:clamp(16px,1.25vw,18px);line-height:1.6;color:var(--slate);max-width:62ch;margin-top:16px}
.bkh .bkh-head{display:flex;align-items:flex-end;justify-content:space-between;gap:24px 40px;flex-wrap:wrap;margin-bottom:clamp(28px,4vw,44px)}
.bkh .bkh-head>div{max-width:760px}
.bkh .bkh-more{display:inline-flex;align-items:center;gap:8px;font:600 12px/1 var(--font);letter-spacing:.14em;text-transform:uppercase;color:var(--navy);padding-bottom:6px;border-bottom:1.5px solid var(--brass);white-space:nowrap}
.bkh .bkh-more:hover{color:var(--brass-d)}
.bkh .bkh-btn{display:inline-flex;align-items:center;justify-content:center;gap:10px;min-height:50px;padding:0 26px;border-radius:999px;font:600 12.5px/1 var(--font);letter-spacing:.12em;text-transform:uppercase;cursor:pointer;border:1.5px solid transparent;transition:transform .2s,background .2s,color .2s,border-color .2s;white-space:nowrap}
.bkh .bkh-btn:hover{transform:translateY(-1px)}
.bkh .bkh-btn svg{width:18px;height:18px;flex:none}
.bkh .bkh-btn--navy{background:var(--navy);color:#fff}
.bkh .bkh-btn--navy:hover{background:var(--navy-2);color:#fff}
.bkh .bkh-btn--white{background:#fff;color:var(--navy)}
.bkh .bkh-btn--white:hover{background:var(--mist);color:var(--navy)}
.bkh .bkh-btn--ghost{background:transparent;color:var(--navy);border-color:var(--line-2)}
.bkh .bkh-btn--ghost:hover{border-color:var(--navy);color:var(--navy)}
.bkh .bkh-btn--ghostw{background:transparent;color:#fff;border-color:rgba(255,255,255,.38)}
.bkh .bkh-btn--ghostw:hover{border-color:#fff;color:#fff}
.bkh .bkh-sec{padding:var(--sec) 0}
.bkh .bkh-sec--white{background:#fff}
.bkh .bkh-sec--mist{background:var(--mist)}
.bkh .bkh-sec--navy{background:var(--navy);color:#fff}
.bkh .bkh-sec--navy .bkh-eyebrow{color:var(--brass-l)}
.bkh .bkh-sec--navy h2.bkh-display{color:#fff!important}
.bkh .bkh-sec--navy .bkh-lede{color:var(--on-navy)}
.bkh .bkh-sec--navy .bkh-more{color:#fff;border-color:var(--brass-l)}
.bkh .bkh-sec--navy .bkh-more:hover{color:var(--brass-l)}
.bkh .bkh-noimg{display:block;background:linear-gradient(135deg,#1b3558,#0d2440)}
/* hero (v2.2: the Nairobi photo under a navy scrim strong enough for white text over its brightest part; the panel below overlaps its foot) */
.bkh .bkh-hero{position:relative;isolation:isolate;min-height:min(92vh,880px);display:flex;align-items:flex-end;color:#fff;background:var(--navy-3);overflow:hidden}
.bkh .bkh-hero-img{position:absolute;inset:0;z-index:-2;width:100%;height:100%;max-width:none;object-fit:cover;object-position:center 42%}
.bkh .bkh-hero-pic{position:absolute;inset:0;z-index:-2;display:block;margin:0;padding:0}
/* v2.2.1: the hero photo can be any photo (the family picture 37801 could not be measured), so the scrim alone gives every hero text
   WCAG AA, over a pure-white photo too, at 360 to 1920px, in two layers: a light veil over the whole photo (darker at the top, under
   the theme's header), and one soft pool of navy behind the text only (.bkh-wrap::before: a box from above the eyebrow to the foot and from
   left of the text to past the width of the sub, a little darker at the eyebrow's row, blurred by 72px so that it fades out over about
   180px and has no edge anywhere; the margin left of the text stays light on wide screens). The rest of the photo stays bright. */
.bkh .bkh-hero::before{content:"";position:absolute;inset:0;z-index:-1;background:linear-gradient(180deg,rgba(9,26,46,.64) 0,rgba(9,26,46,.6) 92px,rgba(9,26,46,.22) 250px,rgba(9,26,46,.12) 50%,rgba(9,26,46,.24) 100%)}
.bkh .bkh-hero .bkh-wrap{--hpt:clamp(132px,20vh,200px);--hpad:calc(clamp(40px,5vw,64px) + var(--lap));position:relative;width:100%;padding-top:var(--hpt);padding-bottom:var(--hpad)}
.bkh .bkh-hero .bkh-wrap::before{content:"";position:absolute;z-index:-1;top:calc(var(--hpt) - 144px);bottom:calc(var(--hpad) - 174px);left:calc(var(--gutter) - 144px);width:min(calc(100% - 2 * var(--gutter) + 288px),calc(54ch + 288px));font-size:clamp(16px,1.3vw,19px);border-radius:184px;background:linear-gradient(180deg,rgba(9,26,46,.66) 0,rgba(9,26,46,.66) 184px,rgba(9,26,46,.6) 284px);filter:blur(72px);pointer-events:none}
.bkh .bkh-hero .bkh-eyebrow{color:var(--brass-tint)} /* v2.2.1: the pale brass, so the pool behind the text can stay light */
.bkh .bkh-hero h1.bkh-display{font-size:clamp(46px,6.4vw,86px)!important;color:#fff!important;max-width:16ch;margin:16px 0 0;text-shadow:0 2px 24px rgba(0,0,0,.25)}
.bkh .bkh-hero .bkh-hero-sub{font-size:clamp(16px,1.3vw,19px);line-height:1.55;color:rgba(255,255,255,.9);max-width:54ch;margin:20px 0 0}
.bkh .bkh-hero-cap{position:absolute;right:var(--gutter);bottom:calc(var(--lap) + 14px);padding:5px 10px;border-radius:999px;background:rgba(9,26,46,.74);font:500 10.5px/1.3 var(--font);letter-spacing:.1em;color:rgba(255,255,255,.86);text-transform:uppercase} /* v2.2.1: its own backing, over any photo */
/* the one search box (v2.2) */
.bkh .bkh-search{margin-top:clamp(26px,3.6vw,38px);max-width:780px}
.bkh .bkh-q{display:block;margin:0;padding:0}
.bkh .bkh-pills{display:inline-flex;gap:4px;margin:0;padding:4px;min-width:0;border:1px solid rgba(255,255,255,.18);border-radius:999px;background:rgba(9,26,46,.6);backdrop-filter:blur(8px);-webkit-backdrop-filter:blur(8px)}
.bkh .bkh-pill{position:relative;display:block;margin:0;padding:0;cursor:pointer}
.bkh .bkh-pill input{position:absolute;inset:0;width:100%;height:100%;min-height:0;margin:0;padding:0;border:0;border-radius:999px;opacity:0;cursor:pointer;-webkit-appearance:none;appearance:none;box-shadow:none;background:none}
.bkh .bkh-pill span{display:block;padding:12px 20px;border-radius:999px;font:600 12.5px/1 var(--font);letter-spacing:.08em;text-transform:uppercase;color:rgba(255,255,255,.88);transition:background .2s,color .2s;white-space:nowrap}
.bkh .bkh-pill:hover span{color:#fff}
.bkh .bkh-pill input:checked+span{background:#fff;color:var(--navy)}
.bkh .bkh-pill input:focus-visible+span{outline:2px solid var(--brass-l);outline-offset:2px}
/* the field fills the white box's height and its label holds the icon and the box's left edge, so a tap anywhere on the box but the button lands in the field */
.bkh .bkh-q-box{display:flex;align-items:stretch;gap:0;height:66px;margin-top:12px;padding:0 9px 0 0;background:#fff;border-radius:999px;box-shadow:0 24px 60px rgba(5,18,36,.35);transition:box-shadow .2s}
.bkh .bkh-q-lab{display:flex;align-items:center;flex:none;margin:0;padding:0 12px 0 24px;border-radius:999px 0 0 999px;cursor:text;color:var(--muted)}
.bkh .bkh-q-box:focus-within{box-shadow:0 0 0 3px var(--brass-l),0 0 0 5px var(--navy-3),0 24px 60px rgba(5,18,36,.35)} /* v2.2.1: a navy edge round the brass ring, so it shows over a bright photo too */
.bkh .bkh-q-lab>svg{width:18px;height:18px;color:var(--muted)}
.bkh .bkh-q input[type="search"]{-webkit-appearance:none;appearance:none;flex:1;align-self:stretch;display:block;min-width:0;width:100%;max-width:none;height:auto;min-height:0;margin:0;padding:0 12px 0 0;border:0;border-radius:0;background:transparent;box-shadow:none;outline:none;font:500 16px/1.3 var(--font);letter-spacing:normal;color:var(--navy);text-overflow:ellipsis}
.bkh .bkh-q input[type="search"]::placeholder{color:var(--slate);opacity:1;font-weight:400}
.bkh .bkh-q input[type="search"]::-webkit-search-decoration{-webkit-appearance:none}
.bkh .bkh-q input[type="search"]::-webkit-search-cancel-button{-webkit-appearance:none;appearance:none;flex:none;width:13px;height:13px;margin:0 0 0 12px;cursor:pointer;opacity:.7;background:linear-gradient(45deg,transparent 43%,var(--navy) 43%,var(--navy) 57%,transparent 57%),linear-gradient(-45deg,transparent 43%,var(--navy) 43%,var(--navy) 57%,transparent 57%)}
.bkh .bkh-q-go{-webkit-appearance:none;appearance:none;flex:none;align-self:center;display:inline-flex;align-items:center;justify-content:center;gap:10px;height:48px;min-height:0;margin:0;padding:0 26px 0 30px;border:0;border-radius:999px;background:var(--navy);box-shadow:none;color:#fff;font:600 12.5px/1 var(--font);letter-spacing:.14em;text-transform:uppercase;cursor:pointer;transition:background .2s}
.bkh .bkh-q-go:hover{background:var(--navy-2)}
.bkh .bkh-q-go svg{width:17px;height:17px;transition:transform .25s}
.bkh .bkh-q-go:hover svg{transform:translateX(3px)}
.bkh .bkh-q-go:focus-visible,.bkh .bkh-dr a:focus-visible{outline:2px solid var(--brass);outline-offset:2px}
.bkh .bkh-q-more{display:inline-flex;align-items:center;gap:7px;margin:16px 0 0 24px;padding-bottom:4px;border-bottom:1px solid rgba(255,255,255,.45);font:600 11.5px/1 var(--font);letter-spacing:.12em;text-transform:uppercase;color:#fff}
.bkh .bkh-q-more:hover{border-color:var(--brass-l);color:#fff}
.bkh .bkh-q-more svg{width:14px;height:14px}
.bkh .bkh-q-more:focus-visible{outline:2px solid var(--brass-l);outline-offset:3px}
.bkh .bkh-sr{position:absolute!important;width:1px;height:1px;margin:-1px;padding:0;overflow:hidden;clip:rect(0 0 0 0);white-space:nowrap;border:0}
/* the typeahead (.bkh-dl, placed by the script) and the ?bkdev= results panel */
.bkh .bkh-dl{position:absolute;z-index:60;top:0;left:0;max-height:460px;overflow-y:auto;overscroll-behavior:contain;background:#fff;border:1px solid var(--line);border-radius:20px;box-shadow:0 28px 70px rgba(5,18,36,.34);text-align:left;-webkit-overflow-scrolling:touch}
.bkh .bkh-dl.is-up{box-shadow:0 -24px 64px rgba(5,18,36,.3)}
.bkh .bkh-dl ul{display:block;margin:0;padding:0;list-style:none}
.bkh .bkh-dl li{display:grid;grid-template-columns:minmax(0,1fr) auto;align-items:center;gap:3px 18px;margin:0;padding:12px 22px;border-top:1px solid var(--line);cursor:pointer;color:var(--ink);list-style:none;background:none}
.bkh .bkh-dl li:first-child{border-top:0}
.bkh .bkh-dl li.bkh-dl-gh{display:block;padding:14px 22px 6px;cursor:default;font:600 10px/1.2 var(--font);letter-spacing:.16em;text-transform:uppercase;color:var(--brass-d)}
.bkh .bkh-dl li.bkh-dl-gh+li{border-top:0}
.bkh .bkh-dl li[aria-selected="true"]{background:var(--mist);box-shadow:inset 3px 0 0 var(--brass)}
.bkh .bkh-dl-name{grid-column:1;font:600 14.5px/1.3 var(--font);color:var(--navy);overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.bkh .bkh-dl-name mark{background:none;color:inherit;padding:0;text-decoration:underline;text-decoration-color:var(--brass);text-decoration-thickness:1.5px;text-underline-offset:3px}
.bkh .bkh-dl-meta{grid-column:1;font:600 10px/1.4 var(--font);letter-spacing:.12em;text-transform:uppercase;color:var(--muted);overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.bkh .bkh-dl-price{grid-column:2;grid-row:1/span 2;font:400 19px/1.1 var(--display);color:var(--navy);white-space:nowrap}
.bkh .bkh-dl li.bkh-dl-all{display:flex;align-items:center;gap:8px;padding:13px 22px;background:var(--paper);font:600 11px/1.2 var(--font);letter-spacing:.14em;text-transform:uppercase;color:var(--brass-d)}
.bkh .bkh-dl li.bkh-dl-all svg{width:15px;height:15px}
.bkh .bkh-dl li.bkh-dl-all[aria-selected="true"]{color:var(--navy);background:var(--mist)}
.bkh .bkh-dl-msg{margin:0;padding:15px 22px;font:500 13.5px/1.5 var(--font);color:var(--slate)}
.bkh .bkh-dr{margin-top:14px;padding:28px 30px 24px;background:#fff;color:var(--ink);border-radius:22px;box-shadow:0 34px 36px -4px rgba(5,18,36,.3);scroll-margin-top:110px}
.bkh .bkh-dr-head{display:flex;align-items:baseline;justify-content:space-between;flex-wrap:wrap;gap:8px 28px;padding-bottom:16px;border-bottom:1px solid var(--line)}
.bkh .bkh-dr h2.bkh-dr-title{flex:1 1 420px;margin:0!important;padding:0!important;font:400 clamp(28px,2.6vw,36px)/1.1 var(--display)!important;letter-spacing:-.01em!important;text-transform:none!important;color:var(--navy)!important;overflow-wrap:anywhere}
.bkh .bkh-dr-title em{font-style:italic}
.bkh .bkh-dr-n{font:600 10.5px/1.4 var(--font);letter-spacing:.16em;text-transform:uppercase;color:var(--muted);white-space:nowrap}
.bkh .bkh-dr-sub{flex:1 1 100%;font:400 15px/1.6 var(--font);color:var(--slate);margin:0}
.bkh .bkh-dr h3.bkh-dr-g{margin:22px 0 0!important;font:600 10.5px/1.4 var(--font)!important;letter-spacing:.16em!important;text-transform:uppercase!important;color:var(--brass-d)!important}
.bkh .bkh-dr-list{display:grid;grid-template-columns:1fr 1fr;gap:0 40px}
.bkh .bkh-dr-list li.is-best,.bkh .bkh-dr-list li:only-child{grid-column:1/-1}
.bkh .bkh-dr-list li.is-best{padding:18px 0 8px}
.bkh .bkh-dr-item{display:grid;grid-template-columns:minmax(0,1fr) auto;align-items:center;gap:3px 18px;height:100%;padding:14px 0;border-bottom:1px solid var(--line);color:var(--ink)}
.bkh .bkh-dr-name{grid-column:1;font:600 15px/1.35 var(--font);color:var(--navy);transition:color .2s}
.bkh .bkh-dr-item:hover .bkh-dr-name{color:var(--brass-d)}
.bkh .bkh-dr-meta{grid-column:1;font:600 10px/1.4 var(--font);letter-spacing:.12em;text-transform:uppercase;color:var(--muted)}
.bkh .bkh-dr-price{grid-column:2;grid-row:1/span 2;font:400 21px/1.1 var(--display);color:var(--navy);white-space:nowrap}
.bkh .bkh-dr-item.is-best{padding:20px 24px;border:1px solid var(--line);border-radius:14px;background:var(--paper)}
.bkh .bkh-dr-tag{grid-column:1/-1;justify-self:start;margin-bottom:8px;padding:6px 10px;border-radius:999px;background:var(--brass-tint);font:600 10px/1 var(--font);letter-spacing:.14em;text-transform:uppercase;color:var(--brass-d)}
.bkh .bkh-dr-item.is-best .bkh-dr-name{font:400 clamp(28px,2.8vw,38px)/1.05 var(--display);letter-spacing:-.01em}
.bkh .bkh-dr-item.is-best .bkh-dr-price{grid-row:2/span 2;font-size:clamp(22px,2vw,28px)}
.bkh .bkh-dr-cta{grid-column:1;display:inline-flex;align-items:center;gap:8px;margin-top:10px;font:600 11.5px/1 var(--font);letter-spacing:.12em;text-transform:uppercase;color:var(--brass-d)}
.bkh .bkh-dr-cta svg{width:16px;height:16px;transition:transform .25s}
.bkh .bkh-dr-item.is-best:hover .bkh-dr-cta svg{transform:translateX(4px)}
.bkh .bkh-dr-foot{display:flex;flex-wrap:wrap;align-items:center;gap:14px 30px;margin-top:22px}
.bkh .bkh-dr-wa{display:inline-flex;align-items:center;gap:8px;font:600 12px/1 var(--font);letter-spacing:.12em;text-transform:uppercase;color:var(--navy)}
.bkh .bkh-dr-wa svg{width:17px;height:17px;color:var(--wa)}
.bkh .bkh-dr-alt{font:500 13px/1.4 var(--font);color:var(--slate);border-bottom:1px solid var(--line-2)}
.bkh .bkh-dr-alt:hover,.bkh .bkh-dr-wa:hover{color:var(--brass-d)}
@media (max-width:900px){.bkh .bkh-dr{border-radius:18px}}
@media (max-width:767px){
.bkh .bkh-q-box{height:60px;padding:0 6px 0 0}
.bkh .bkh-q-lab{padding:0 10px 0 18px}
.bkh .bkh-q-go{width:48px;height:48px;padding:0}
.bkh .bkh-q-go span{position:absolute;width:1px;height:1px;overflow:hidden;clip:rect(0 0 0 0);white-space:nowrap}
.bkh .bkh-q-go svg{width:19px;height:19px}
/* "More filters" as a 44px target on phones: the padding takes the taps, the underline stays under the words */
.bkh .bkh-q-more{margin:4px 0 0 18px;padding:15px 0;border-bottom:0;background:linear-gradient(rgba(255,255,255,.45),rgba(255,255,255,.45)) 0 calc(100% - 10px)/100% 1px no-repeat}
.bkh .bkh-q-more:hover{background-image:linear-gradient(var(--brass-l),var(--brass-l))}
.bkh .bkh-dl li{padding:12px 16px}
.bkh .bkh-dl li.bkh-dl-gh{padding:12px 16px 6px}
.bkh .bkh-dr{padding:22px 18px 18px}
.bkh .bkh-dr-list{grid-template-columns:1fr}
.bkh .bkh-dr-item.is-best{padding:18px 16px}
.bkh .bkh-dr-foot{gap:14px 22px}
}
@media (max-width:480px){
.bkh .bkh-q-lab{padding:0 8px 0 20px}
.bkh .bkh-q-lab>svg{display:none}
.bkh .bkh-q input[type="search"]{padding-right:8px}
.bkh .bkh-dr-item:not(.is-best) .bkh-dr-name,.bkh .bkh-dl-name{grid-column:1/-1}
.bkh .bkh-dr-item:not(.is-best) .bkh-dr-price,.bkh .bkh-dl-price{grid-row:2;align-self:center;font-size:17px}
.bkh .bkh-dr-item.is-best{grid-template-columns:minmax(0,1fr)}
.bkh .bkh-dr-item.is-best .bkh-dr-price{grid-column:1;grid-row:auto;margin-top:6px}
}
/* the placeholder is the field's only visible label: on the narrowest phones it is set a size smaller so it is never cut short (the
   field itself stays 16px, so iOS does not zoom in on focus) */
@media (max-width:400px){.bkh .bkh-q input[type="search"]::placeholder{font-size:15px;letter-spacing:-.005em}}
.bkh .bkh-nw{white-space:nowrap}
/* the panel (v2.2): everything after the hero, on paper, its large rounded top corners over the foot of the hero */
.bkh .bkh-panel{position:relative;z-index:2;margin-top:calc(-1 * var(--lap));background:var(--paper);border-radius:var(--lapr) var(--lapr) 0 0}
/* proof strip */
.bkh .bkh-strip{padding:clamp(34px,4vw,48px) 0 clamp(30px,3.5vw,44px);border-bottom:1px solid var(--line)}
.bkh .bkh-stats{display:grid;grid-template-columns:repeat(4,1fr);gap:20px}
.bkh .bkh-stat{padding-left:18px;border-left:1px solid var(--line-2)}
.bkh .bkh-stat b{display:block;font:400 clamp(30px,3vw,40px)/1 var(--display);color:var(--navy);letter-spacing:-.01em}
.bkh .bkh-stat span{display:block;margin-top:8px;font:600 10.5px/1.4 var(--font);letter-spacing:.16em;text-transform:uppercase;color:var(--muted)}
/* neighbourhood grid (v2.2) */
.bkh .bkh-hoods{padding:clamp(44px,5.5vw,72px) 0 0}
.bkh .bkh-hood-grid{display:grid;grid-template-columns:repeat(5,minmax(0,1fr));gap:10px}
.bkh .bkh-hood-grid li{display:block;margin:0;padding:0;list-style:none}
.bkh .bkh-hood{position:relative;display:flex;flex-direction:column;justify-content:center;gap:3px;height:100%;min-height:76px;padding:14px 42px 14px 18px;border:1px solid var(--line-2);border-radius:14px;background:#fff;color:var(--navy);transition:background .2s,border-color .2s,color .2s}
.bkh .bkh-hood-n{font:600 15px/1.25 var(--font);color:inherit}
.bkh .bkh-hood-c{font:500 12px/1.3 var(--font);color:var(--muted);transition:color .2s}
.bkh .bkh-hood svg{position:absolute;right:16px;top:50%;width:16px;height:16px;margin-top:-8px;color:var(--brass);transition:transform .2s,color .2s}
.bkh .bkh-hood:hover,.bkh .bkh-hood:focus-visible{background:var(--navy);border-color:var(--navy);color:#fff}
.bkh .bkh-hood:hover .bkh-hood-c,.bkh .bkh-hood:focus-visible .bkh-hood-c{color:var(--on-navy)}
.bkh .bkh-hood:hover svg,.bkh .bkh-hood:focus-visible svg{transform:translateX(3px);color:var(--brass-l)}
.bkh .bkh-hood:focus-visible{outline:2px solid var(--brass);outline-offset:2px}
/* developments (v2.2: a carousel of tall cards) */
.bkh .bkh-dc-nav{display:flex;align-items:center;gap:10px}
.bkh .bkh-dc-btn{-webkit-appearance:none;appearance:none;display:inline-flex;align-items:center;justify-content:center;width:46px;height:46px;min-height:0;margin:0;padding:0;border:1.5px solid var(--line-2);border-radius:50%;background:transparent;box-shadow:none;color:var(--navy);cursor:pointer;transition:background .2s,border-color .2s,color .2s,opacity .2s}
.bkh .bkh-dc-btn svg{width:18px;height:18px}
.bkh .bkh-dc-btn[data-dir="-1"] svg{transform:rotate(180deg)}
/* at either end an arrow is aria-disabled, not disabled, so it keeps the keyboard focus (the script ignores its clicks) */
.bkh .bkh-dc-btn:hover:not([aria-disabled="true"]){background:var(--navy);border-color:var(--navy);color:#fff}
.bkh .bkh-dc-btn[aria-disabled="true"]{opacity:.35;cursor:default}
.bkh .bkh-dc-btn:focus-visible{outline:2px solid var(--brass);outline-offset:2px}
.bkh .bkh-dc-nav .bkh-more{margin-left:14px}
.bkh .bkh-dc-row{display:flex;gap:14px;margin:0 calc(50% - 50vw) 0 0;padding:0 var(--gutter) 14px 0;overflow-x:auto;overscroll-behavior-x:contain;scroll-snap-type:x mandatory;scrollbar-width:thin;scrollbar-color:var(--line-2) transparent;list-style:none}
.bkh .bkh-dc{--dc-w:300px;position:relative;isolation:isolate;display:flex;flex-direction:column;justify-content:flex-end;flex:0 0 var(--dc-w);height:clamp(460px,40vw,540px);margin:0;padding:0;border-radius:16px;overflow:hidden;background:var(--navy);color:#fff;scroll-snap-align:start;list-style:none;transition:flex-basis .5s cubic-bezier(.2,.7,.2,1)}
.bkh .bkh-dc-img,.bkh .bkh-dc>.bkh-noimg{position:absolute;inset:0;z-index:-2;width:100%;height:100%;max-width:none;object-fit:cover;transition:transform .8s cubic-bezier(.2,.7,.2,1)}
.bkh .bkh-dc-body{padding:150px 22px 24px;background:linear-gradient(180deg,rgba(6,12,20,0) 0,rgba(6,12,20,.5) 64px,rgba(6,12,20,.84) 136px,rgba(6,12,20,.9) 190px,rgba(6,12,20,.95) 100%)} /* the fade is the text block's own background (the reset above takes ::before off every li); its stops are in px from the block's top, so the area label (150px down) always sits on at least .84, however tall the block */
.bkh .bkh-dc-area{display:block;font:600 10.5px/1.3 var(--font);letter-spacing:.16em;text-transform:uppercase;color:var(--brass-l)}
.bkh .bkh-dc h3.bkh-dc-name{margin-top:6px!important;font:400 clamp(28px,2.4vw,34px)/1.05 var(--display)!important;letter-spacing:-.01em!important;color:#fff!important}
.bkh .bkh-dc-line{margin-top:10px;font:500 13px/1.45 var(--font);color:rgba(255,255,255,.9)}
.bkh .bkh-dc-cta{display:inline-flex;align-items:center;gap:8px;margin-top:14px;padding-bottom:5px;border-bottom:1.5px solid var(--brass-l);font:600 11.5px/1 var(--font);letter-spacing:.12em;text-transform:uppercase;color:#fff}
.bkh .bkh-dc-cta svg{width:16px;height:16px;transition:transform .25s}
.bkh .bkh-dc-cta::after{content:"";position:absolute;inset:0;z-index:1;border-radius:16px}
.bkh .bkh-dc-cta:focus-visible{outline:none}
/* the focus ring is drawn inside the card (the scrolling row would clip one outside it): white then navy, so one of them stands out on any photo */
.bkh .bkh-dc-cta:focus-visible::after{box-shadow:inset 0 0 0 3px #fff,inset 0 0 0 6px var(--navy)}
.bkh .bkh-dc-play:focus-visible{outline:2px solid #fff;outline-offset:2px}
.bkh .bkh-dc:hover .bkh-dc-cta svg{transform:translateX(4px)}
.bkh .bkh-dc-play{position:relative;z-index:2;display:inline-flex;align-items:center;gap:8px;margin:14px 0 0 18px;padding:8px 14px 8px 10px;border-radius:999px;background:rgba(255,255,255,.16);font:600 11px/1 var(--font);letter-spacing:.12em;text-transform:uppercase;color:#fff;backdrop-filter:blur(6px);-webkit-backdrop-filter:blur(6px)}
.bkh .bkh-dc-play svg{width:14px;height:14px}
/* card widths: the script sets --dc-c (a card) and --dc-o (an open card: two cards and a gap) on the row so that the next card
   always shows by about 56px at the right edge, wherever the arrows leave the row */
@media (min-width:901px){.bkh .bkh-dc{--dc-w:var(--dc-c,300px)}}
.bkh .bkh-dc-row.is-sizing .bkh-dc{transition:none}
@media (hover:hover) and (pointer:fine) and (min-width:901px){
.bkh .bkh-dc-row{scroll-snap-type:none}
.bkh .bkh-dc{--dc-w:var(--dc-c,250px)}
.bkh .bkh-dc-more{max-height:0;opacity:0;overflow:hidden;transition:max-height .45s cubic-bezier(.2,.7,.2,1),opacity .3s .1s}
.bkh .bkh-dc:is(:first-child,:hover,:focus-within){--dc-w:var(--dc-o,470px)}
.bkh .bkh-dc:is(:first-child,:hover,:focus-within) .bkh-dc-more{max-height:170px;opacity:1}
/* the first card closes for another one only while the row is at its start (the script marks a moved row is-moved): once the row has
   moved, the first card is off to the left and a card opening never shifts the cards under the pointer */
.bkh .bkh-dc-row:not(.is-moved):has(.bkh-dc:hover,.bkh-dc:focus-within) .bkh-dc:first-child:not(:hover):not(:focus-within){--dc-w:var(--dc-c,250px)}
.bkh .bkh-dc-row:not(.is-moved):has(.bkh-dc:hover,.bkh-dc:focus-within) .bkh-dc:first-child:not(:hover):not(:focus-within) .bkh-dc-more{max-height:0;opacity:0}
/* v2.2.1: while the keyboard moves through the row (the script marks it is-kb), a card under a resting pointer stays closed, and at
   the row's start the first card closes for the focused one even under the pointer: the row moves for the focused card only */
.bkh .bkh-dc-row.is-kb .bkh-dc:hover:not(:focus-within):not(:first-child){--dc-w:var(--dc-c,250px)}
.bkh .bkh-dc-row.is-kb .bkh-dc:hover:not(:focus-within):not(:first-child) .bkh-dc-more{max-height:0;opacity:0}
.bkh .bkh-dc-row.is-kb:not(.is-moved):has(.bkh-dc:focus-within) .bkh-dc:first-child:not(:focus-within){--dc-w:var(--dc-c,250px)}
.bkh .bkh-dc-row.is-kb:not(.is-moved):has(.bkh-dc:focus-within) .bkh-dc:first-child:not(:focus-within) .bkh-dc-more{max-height:0;opacity:0}
.bkh .bkh-dc:hover .bkh-dc-img{transform:scale(1.04)}
}
/* listings (v2.2: "Worth a viewing" on near-black, tall photo cards) */
.bkh .bkh-sec--dark{background:var(--black);color:#fff}
.bkh .bkh-sec--dark .bkh-eyebrow{color:var(--brass-l)}
.bkh .bkh-sec--dark h2.bkh-display{color:#fff!important}
.bkh .bkh-sec--dark .bkh-lede{color:#BCC4CE}
.bkh .bkh-ltabs{display:flex;gap:28px;border-bottom:1px solid rgba(255,255,255,.16);margin-bottom:28px}
.bkh .bkh-ltab{-webkit-appearance:none;appearance:none;height:auto;min-height:0;border:0;border-radius:0;background:transparent;box-shadow:none;padding:0 0 14px;margin-bottom:-1px;font:600 12.5px/1 var(--font);letter-spacing:.12em;text-transform:uppercase;color:rgba(255,255,255,.62);border-bottom:2px solid transparent;cursor:pointer}
.bkh .bkh-ltab[aria-selected="true"]{color:#fff;border-color:var(--brass-l)}
.bkh .bkh-ltab:focus-visible{outline:2px solid var(--brass-l);outline-offset:4px}
.bkh .bkh-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:20px}
.bkh .bkh-grid--2{grid-template-columns:repeat(2,minmax(0,1fr))}
.bkh .bkh-grid--1{grid-template-columns:minmax(0,560px)}
.bkh .bkh-grid[hidden]{display:none}
.bkh .bkh-card{position:relative;isolation:isolate;display:flex;flex-direction:column;justify-content:flex-end;aspect-ratio:4/5;border-radius:14px;overflow:hidden;background:#161B22;color:#fff}
.bkh .bkh-card-img,.bkh .bkh-card>.bkh-noimg{position:absolute;inset:0;z-index:-2;width:100%;height:100%;max-width:none;object-fit:cover;transition:transform .7s cubic-bezier(.2,.7,.2,1)}
.bkh .bkh-card::before{content:"";position:absolute;inset:0;z-index:-1;background:linear-gradient(180deg,rgba(0,0,0,0) 38%,rgba(0,0,0,.5) 62%,rgba(0,0,0,.9) 100%)}
.bkh .bkh-card:hover .bkh-card-img{transform:scale(1.04)}
.bkh .bkh-card:focus-visible{outline:2px solid var(--brass-l);outline-offset:3px}
.bkh .bkh-card-tab{position:absolute;top:0;left:0;padding:10px 16px 10px 16px;border-radius:0 0 12px 0;background:#fff;color:var(--black);font:700 10px/1 var(--font);letter-spacing:.16em;text-transform:uppercase}
.bkh .bkh-card-body{padding:0 20px 20px}
.bkh .bkh-card-price{display:block;font:700 22px/1.15 var(--font);letter-spacing:-.005em;color:#fff}
.bkh .bkh-card h3{margin-top:6px!important;font:500 15px/1.35 var(--font)!important;color:#fff!important;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden}
.bkh .bkh-card-loc{display:block;margin-top:2px;font:400 13px/1.4 var(--font);color:rgba(255,255,255,.8)}
.bkh .bkh-specs{display:block;margin-top:12px;padding-top:12px;border-top:1px solid rgba(255,255,255,.22);font:600 10.5px/1.4 var(--font);letter-spacing:.14em;text-transform:uppercase;color:rgba(255,255,255,.92)}
.bkh .bkh-lfoot{display:flex;gap:16px;flex-wrap:wrap;justify-content:center;margin-top:40px}
/* diaspora */
.bkh .bkh-dia{position:relative;overflow:hidden}
.bkh .bkh-dia::before{content:"";position:absolute;inset:auto -10% -40% auto;width:60vw;max-width:720px;aspect-ratio:1;border-radius:50%;background:radial-gradient(closest-side,rgba(185,138,68,.22),rgba(185,138,68,0));pointer-events:none}
.bkh .bkh-dia-grid{display:grid;grid-template-columns:1.05fr 1fr;gap:48px 72px;align-items:center}
.bkh .bkh-steps{display:grid;gap:14px}
.bkh .bkh-step{display:grid;grid-template-columns:44px 1fr;gap:16px;padding:20px 22px;border-radius:14px;background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.1)}
.bkh .bkh-step i{display:flex;align-items:center;justify-content:center;width:44px;height:44px;border-radius:50%;border:1px solid rgba(221,185,127,.6);font:400 20px/1 var(--display);font-style:normal;color:var(--brass-l)}
.bkh .bkh-step h3{font:600 15px/1.4 var(--font)!important;color:#fff!important}
.bkh .bkh-step p{font:400 14px/1.6 var(--font);color:var(--on-navy);margin-top:4px}
.bkh .bkh-dia-proof{display:flex;flex-wrap:wrap;align-items:center;gap:10px 14px;margin-top:28px;font:500 13px/1.5 var(--font);color:var(--on-navy)}
.bkh .bkh-dia-proof span{padding:6px 11px;border-radius:999px;border:1px solid rgba(255,255,255,.18);font:600 10.5px/1 var(--font);letter-spacing:.12em;text-transform:uppercase;color:#fff}
.bkh .bkh-ctas{display:flex;flex-wrap:wrap;gap:12px;margin-top:30px}
/* diaspora with numbers (v2.2): two numbers from the website's enquiries and a dotted world map */
.bkh .bkh-dia--nums .bkh-dia-grid{grid-template-columns:minmax(0,1.05fr) minmax(0,1fr);align-items:end}
.bkh .bkh-dia-nums{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:20px 32px}
.bkh .bkh-dia-num b{display:block;font:400 clamp(54px,5.8vw,80px)/.95 var(--display);letter-spacing:-.02em;color:var(--brass-l)}
.bkh .bkh-dia-num span{display:block;margin-top:10px;max-width:26ch;font:500 13.5px/1.45 var(--font);color:#fff}
.bkh .bkh-dia-note{margin-top:18px;max-width:58ch;font:400 12.5px/1.55 var(--font);color:var(--on-navy)}
.bkh .bkh-dia--nums .bkh-dia-proof{margin-top:20px}
.bkh .bkh-dia-proof-h{flex:0 0 100%;font:500 13px/1.5 var(--font);color:var(--on-navy)} /* the label on its own line, so no chip is left alone on a second one */
/* narrow screens: the chips in rows of three (two for four chips), so none is left alone on the last line */
@media (max-width:600px){.bkh .bkh-dia-proof--top{display:grid;grid-template-columns:repeat(3,max-content)}.bkh .bkh-dia-proof--top.bkh-dia-proof--n4{grid-template-columns:repeat(2,max-content)}.bkh .bkh-dia-proof-h{grid-column:1/-1}.bkh .bkh-dia-proof--top span{text-align:center}}
.bkh .bkh-dia-map{position:relative;max-width:1000px;margin:clamp(36px,4.5vw,56px) auto 0;padding:0}
.bkh .bkh-map{display:block;width:100%;height:auto;overflow:visible}
.bkh .bkh-map-land{fill:none;stroke:rgba(201,211,223,.36);stroke-width:1.05;stroke-linecap:round;stroke-dasharray:0 2}
.bkh .bkh-map-arc{fill:none;stroke:rgba(221,185,127,.5);stroke-width:.45;stroke-linecap:round}
.bkh .bkh-map-dot circle{fill:var(--brass-l)}
.bkh .bkh-map-home{fill:#fff}
.bkh .bkh-map-ring{fill:none;stroke:#fff;stroke-width:.7;opacity:.75}
.bkh .bkh-dia-map figcaption{display:flex;align-items:center;flex-wrap:wrap;gap:6px 8px;margin-top:16px;font:500 12px/1.4 var(--font);color:var(--on-navy)}
.bkh .bkh-map-k{display:inline-block;width:9px;height:9px;margin-left:8px;border-radius:50%;background:var(--brass-l)}
.bkh .bkh-map-k--home{margin-left:0;background:#fff}
.bkh .bkh-steps--row{grid-template-columns:repeat(3,minmax(0,1fr));margin-top:clamp(44px,5vw,64px)}
/* areas */
.bkh .bkh-areas{display:grid;grid-template-columns:repeat(5,1fr);gap:16px}
.bkh .bkh-area{position:relative;display:block;aspect-ratio:4/5;border-radius:14px;overflow:hidden;background:var(--navy);color:#fff}
.bkh .bkh-area img,.bkh .bkh-area .bkh-noimg{position:absolute;inset:0;width:100%;height:100%;object-fit:cover;transition:transform .6s cubic-bezier(.2,.7,.2,1)}
.bkh .bkh-area:hover img{transform:scale(1.05)}
.bkh .bkh-area::after{content:"";position:absolute;inset:0;background:linear-gradient(180deg,rgba(9,26,46,0) 35%,rgba(9,26,46,.86) 100%)}
.bkh .bkh-area div{position:absolute;left:16px;right:16px;bottom:16px;z-index:1}
.bkh .bkh-area h3{font:400 24px/1.1 var(--display)!important;color:#fff!important;letter-spacing:-.01em!important}
.bkh .bkh-area span{display:block;margin-top:5px;font:600 10.5px/1.3 var(--font);letter-spacing:.14em;text-transform:uppercase;color:rgba(255,255,255,.72)}
/* founder */
.bkh .bkh-founder{display:grid;grid-template-columns:.9fr 1.1fr;gap:48px 80px;align-items:center}
.bkh .bkh-portrait{position:relative;border-radius:18px;overflow:hidden;aspect-ratio:4/5;background:#0b0b0b;box-shadow:0 30px 70px rgba(13,36,64,.18)}
.bkh .bkh-portrait img{width:100%;height:100%;object-fit:cover;object-position:center top}
.bkh .bkh-portrait figcaption{position:absolute;left:20px;bottom:18px;color:#fff;font:600 11px/1.4 var(--font);letter-spacing:.14em;text-transform:uppercase;opacity:.85}
.bkh .bkh-quote{font:400 clamp(20px,1.7vw,24px)/1.45 var(--display);color:var(--ink);margin-top:22px;font-style:italic}
.bkh .bkh-body{font-size:16px;line-height:1.7;color:var(--slate);margin-top:18px;max-width:58ch}
.bkh .bkh-sign{margin-top:22px;font:600 12px/1.5 var(--font);letter-spacing:.14em;text-transform:uppercase;color:var(--navy)}
.bkh .bkh-sign small{display:block;font:500 11px/1.5 var(--font);letter-spacing:.1em;color:var(--muted)}
.bkh .bkh-team{display:flex;align-items:center;gap:14px;margin-top:34px;padding-top:24px;border-top:1px solid var(--line)}
.bkh .bkh-avatars{display:flex}
.bkh .bkh-avatars img,.bkh .bkh-avatars .bkh-noimg{width:44px;height:44px;border-radius:50%;object-fit:cover;border:2px solid #fff;margin-left:-10px;box-shadow:0 2px 8px rgba(0,0,0,.12)}
.bkh .bkh-avatars :first-child{margin-left:0}
.bkh .bkh-team p{font:500 13px/1.5 var(--font);color:var(--slate)}
.bkh .bkh-team p a{color:var(--navy);font-weight:600;border-bottom:1px solid var(--brass)}
/* services (v2.2.1: "Our promise": the statement and "Meet the team", a thick brass bar, then the four service cards and the
   valuation band, over one full-width photo, the owner's Nairobi skyline) */
.bkh .bkh-sv{position:relative;isolation:isolate;overflow:hidden;background:var(--navy);color:#fff}
.bkh .bkh-sv-img{position:absolute;inset:0;z-index:-2;width:100%;height:100%;max-width:none;object-fit:cover}
/* the scrim holds .86 to .8 over the promise (eyebrow, title, statement, button: the top 500px) whatever the photo, then opens over the cards */
.bkh .bkh-sv--img::before{content:"";position:absolute;inset:0;z-index:-1;background:linear-gradient(180deg,rgba(9,26,46,.86) 0,rgba(9,26,46,.8) 500px,rgba(9,26,46,.58) 70%,rgba(9,26,46,.84) 100%)}
.bkh .bkh-sv .bkh-eyebrow{color:var(--brass-l)}
.bkh .bkh-sv h2.bkh-display{color:#fff!important}
/* the promise head sits on the cards' own grid (four columns, 16px apart): the eyebrow and title over the first two cards, opening the
   band at its top like every other section; the statement over the last two, its first line level with the title's top */
.bkh .bkh-pm{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:24px 16px;align-items:start}
.bkh .bkh-pm-h{grid-column:1/3;padding-right:clamp(8px,3vw,48px)}
.bkh .bkh-pm-s{grid-column:3/5;display:flex;flex-direction:column;align-items:flex-start;gap:26px;padding-top:calc(11px * 1.4 + 14px)}
.bkh .bkh-pm-s p{font:400 clamp(16px,1.3vw,19px)/1.6 var(--font);color:rgba(255,255,255,.9);max-width:50ch;text-wrap:pretty}
/* the accent: a thick brass bar under the statement, from the statement's left edge (the third card's, offset from the page's) to the
   screen's right edge */
.bkh .bkh-pm-bar{display:block;height:clamp(8px,.8vw,12px);margin:clamp(34px,4.4vw,56px) calc(50% - 50vw) clamp(36px,4.4vw,56px) calc(50% + 8px);background:var(--brass)}
.bkh .bkh-services{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:16px}
.bkh .bkh-service{display:flex;flex-direction:column;gap:12px;min-height:230px;padding:26px 24px 24px;border-radius:16px;background:var(--paper);color:var(--ink);box-shadow:0 24px 56px rgba(5,18,36,.28);transition:transform .25s,background .2s}
.bkh .bkh-service:hover{transform:translateY(-3px);background:#fff}
.bkh .bkh-service:focus-visible{outline:2px solid var(--brass-l);outline-offset:3px}
.bkh .bkh-service h3{display:flex;align-items:flex-start;justify-content:space-between;gap:12px;font:400 26px/1.1 var(--display)!important;letter-spacing:-.01em!important;color:var(--navy)!important}
.bkh .bkh-service h3 svg{width:18px;height:18px;flex:none;margin-top:5px;color:var(--brass);transition:transform .2s}
.bkh .bkh-service:hover h3 svg{transform:translateX(4px)}
.bkh .bkh-service p{margin-top:auto;font:400 14px/1.6 var(--font);color:var(--slate)}
.bkh .bkh-val{display:flex;align-items:center;justify-content:space-between;gap:24px;flex-wrap:wrap;margin-top:18px;padding:26px 30px;border-radius:16px;background:var(--navy);color:#fff}
.bkh .bkh-sv--img .bkh-val{background:rgba(9,26,46,.78);border:1px solid rgba(255,255,255,.16);backdrop-filter:blur(6px);-webkit-backdrop-filter:blur(6px)}
.bkh .bkh-val h3{font:400 clamp(22px,2vw,28px)/1.2 var(--display)!important;color:#fff!important}
.bkh .bkh-val p{font:400 14px/1.5 var(--font);color:var(--on-navy);margin-top:6px}
.bkh .bkh-sv-cap{position:absolute;right:var(--gutter);bottom:14px;font:500 10.5px/1.3 var(--font);letter-spacing:.1em;text-transform:uppercase;color:rgba(255,255,255,.82)}
/* up to 900px the statement goes under the title; the bar still starts at the second card's edge (the cards stand two by two), and
   from 767px down, where they stand one by one, at the text's edge */
@media (max-width:900px){
.bkh .bkh-pm{grid-template-columns:minmax(0,1fr);gap:18px}
.bkh .bkh-pm-h,.bkh .bkh-pm-s{grid-column:auto;padding:0}
}
@media (max-width:767px){.bkh .bkh-pm-bar{margin-left:0}}
/* phones: the cards stand in one tall column, so the photo is a band behind the promise (its top 560px), fading into navy; the
   caption sits at its top right */
@media (max-width:767px){
.bkh .bkh-sv-img{bottom:auto;height:560px}
.bkh .bkh-sv--img::before{background:linear-gradient(180deg,rgba(9,26,46,.86) 0,rgba(9,26,46,.8) 300px,rgba(9,26,46,.7) 440px,var(--navy) 560px)}
.bkh .bkh-sv-cap{top:16px;bottom:auto}
}
/* homework */
.bkh .bkh-hw{display:grid;grid-template-columns:1.25fr 1fr;gap:40px 56px;align-items:start}
.bkh .bkh-lead{display:block;color:var(--ink)}
.bkh .bkh-lead-media{aspect-ratio:16/9;border-radius:14px;overflow:hidden;background:var(--mist)}
.bkh .bkh-lead-media img,.bkh .bkh-lead-media .bkh-noimg{width:100%;height:100%;object-fit:cover}
.bkh .bkh-cat{display:inline-block;font:600 10.5px/1.3 var(--font);letter-spacing:.16em;text-transform:uppercase;color:var(--brass-d)}
.bkh .bkh-lead h3{font:400 clamp(24px,2.2vw,32px)/1.2 var(--display)!important;color:var(--navy)!important;margin-top:8px!important}
.bkh .bkh-lead .bkh-cat{margin-top:18px}
.bkh .bkh-lead p{font:400 15px/1.6 var(--font);color:var(--slate);margin-top:10px;max-width:60ch}
.bkh .bkh-lead time{display:block;margin-top:10px;font:500 12px/1.4 var(--font);color:var(--muted)}
.bkh .bkh-list{display:grid}
.bkh .bkh-item{display:grid;grid-template-columns:112px 1fr;gap:18px;align-items:center;padding:18px 0;border-top:1px solid var(--line);color:var(--ink)}
.bkh .bkh-item:last-child{border-bottom:1px solid var(--line)}
.bkh .bkh-item-media{aspect-ratio:4/3;border-radius:10px;overflow:hidden;background:var(--mist)}
.bkh .bkh-item-media img,.bkh .bkh-item-media .bkh-noimg{width:100%;height:100%;object-fit:cover}
.bkh .bkh-item h3{font:600 15px/1.4 var(--font)!important;color:var(--navy)!important;margin-top:4px!important;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden}
.bkh .bkh-item:hover h3{color:var(--brass-d)!important}
.bkh .bkh-item time{display:block;margin-top:4px;font:500 11.5px/1.4 var(--font);color:var(--muted)}
/* closing */
.bkh .bkh-close{text-align:center;position:relative;overflow:hidden}
.bkh .bkh-close::before{content:"";position:absolute;left:50%;top:-30%;width:70vw;max-width:900px;aspect-ratio:1;transform:translateX(-50%);border-radius:50%;background:radial-gradient(closest-side,rgba(43,69,104,.9),rgba(13,36,64,0));pointer-events:none}
.bkh .bkh-close .bkh-wrap{position:relative;max-width:820px}
.bkh .bkh-close h2.bkh-display{font-size:clamp(38px,5vw,64px)!important;margin-top:14px}
.bkh .bkh-close .bkh-lede{margin:18px auto 0}
.bkh .bkh-close .bkh-ctas{justify-content:center;margin-top:34px}
.bkh .bkh-close .bkh-alt{margin-top:22px;font:500 13px/1.5 var(--font);color:var(--on-navy)}
.bkh .bkh-close .bkh-alt a{color:#fff;border-bottom:1px solid rgba(255,255,255,.35)}
/* sticky bar */
.bkh-bar{position:fixed;left:0;right:0;bottom:0;z-index:9990;display:none;grid-template-columns:1fr 1fr;gap:8px;padding:8px 10px calc(8px + env(safe-area-inset-bottom));background:rgba(251,249,246,.96);backdrop-filter:blur(10px);-webkit-backdrop-filter:blur(10px);border-top:1px solid #E6DFD4;transform:translateY(110%);transition:transform .3s}
.bkh-bar.is-on{transform:none}
.bkh-bar a{display:flex;align-items:center;justify-content:center;gap:9px;min-height:48px;border-radius:12px;font:600 12.5px/1 "Montserrat",system-ui,sans-serif;letter-spacing:.1em;text-transform:uppercase;text-decoration:none}
.bkh-bar a svg{width:18px;height:18px}
.bkh-bar .bkh-bar-wa{background:#1EBE5D;color:#fff}
.bkh-bar .bkh-bar-call{background:#0D2440;color:#fff}
body.bkh-bar-on #blkac,body.bkh-bar-on .mwai-chatbot-container{transform:translateY(-62px)}
@media (max-width:767px){body.home #zsiq_float{top:auto!important;bottom:140px!important}}
/* responsive */
@media (max-width:1100px){
.bkh .bkh-hood-grid{grid-template-columns:repeat(4,minmax(0,1fr))}
.bkh .bkh-areas{grid-template-columns:repeat(4,1fr)}
.bkh .bkh-services{grid-template-columns:repeat(2,minmax(0,1fr))}
}
@media (max-width:900px){
.bkh .bkh-hood-grid{grid-template-columns:repeat(4,minmax(0,1fr))}
.bkh .bkh-hood{padding-right:34px}
.bkh .bkh-hood svg{right:12px}
.bkh .bkh-stats{grid-template-columns:repeat(2,1fr);gap:22px 16px}
.bkh .bkh-dia-grid,.bkh .bkh-dia--nums .bkh-dia-grid,.bkh .bkh-founder,.bkh .bkh-hw{grid-template-columns:1fr}
.bkh .bkh-steps--row{grid-template-columns:1fr}
.bkh .bkh-dia-map{max-width:640px}
.bkh .bkh-founder{gap:32px}
.bkh .bkh-portrait{max-width:440px}
.bkh .bkh-grid,.bkh .bkh-grid--2{grid-template-columns:repeat(2,minmax(0,1fr));gap:16px}
.bkh .bkh-grid--odd>.bkh-card:last-child{grid-column:1/-1;aspect-ratio:16/10}
.bkh .bkh-dc{--dc-w:calc((100vw - var(--gutter) - 84px) / 2)} /* two cards and about 56px of the next */
}
@media (max-width:767px){
.bkh{font-size:15px}
.bkh .bkh-hero{min-height:0}
.bkh .bkh-hero .bkh-wrap{--hpt:124px;--hpad:calc(46px + var(--lap));padding-top:var(--hpt);padding-bottom:var(--hpad)}
.bkh .bkh-hero h1.bkh-display{font-size:clamp(36px,10.2vw,48px)!important;max-width:none}
.bkh .bkh-hero .bkh-hero-sub{font-size:15.5px;margin-top:14px}
.bkh .bkh-hero{flex-direction:column;justify-content:flex-end}
.bkh .bkh-hero-cap{bottom:calc(var(--lap) + 14px)}
.bkh .bkh-pills{display:flex;width:100%}
.bkh .bkh-pill{flex:1}
.bkh .bkh-pill span{display:flex;align-items:center;justify-content:center;min-height:44px;padding:8px 6px;text-align:center;font-size:11.5px;letter-spacing:.06em}
.bkh .bkh-hero-img{object-position:58% 42%}
.bkh .bkh-hero .bkh-wrap::before{left:-144px;width:calc(100% + 288px);border-radius:0} /* v2.2.1: on phones the text runs the full width: the pool does too, past both edges so it never fades at the sides */
.bkh .bkh-strip{padding-top:28px}
.bkh .bkh-head{margin-bottom:22px}
.bkh .bkh-head .bkh-more{order:3}
.bkh .bkh-dc-row{margin:0 calc(-1*var(--gutter));padding:0 var(--gutter) 10px;scroll-padding-left:var(--gutter);scrollbar-width:none}
.bkh .bkh-dc-row::-webkit-scrollbar{display:none}
.bkh .bkh-dc{--dc-w:min(80vw,340px);height:460px}
.bkh .bkh-dc-nav .bkh-dc-btn{display:none}
.bkh .bkh-dc-nav .bkh-more{margin-left:0}
.bkh .bkh-grid,.bkh .bkh-grid--2,.bkh .bkh-grid--1{display:flex;gap:12px;overflow-x:auto;scroll-snap-type:x mandatory;margin:0 calc(-1*var(--gutter));padding:0 var(--gutter) 8px;scroll-padding-left:var(--gutter);scrollbar-width:none}
.bkh .bkh-grid::-webkit-scrollbar{display:none}
.bkh .bkh-grid[hidden]{display:none}
.bkh .bkh-ltab{display:inline-flex;align-items:flex-end;min-height:44px}
.bkh .bkh-grid .bkh-card,.bkh .bkh-grid--odd>.bkh-card:last-child{flex:0 0 min(82vw,360px);aspect-ratio:4/5;scroll-snap-align:start}
.bkh .bkh-grid--1 .bkh-card{flex-basis:100%}
.bkh .bkh-hood-grid{grid-template-columns:repeat(2,minmax(0,1fr));gap:8px}
.bkh .bkh-hood{min-height:64px;padding:12px 34px 12px 14px}
.bkh .bkh-hood svg{right:12px}
.bkh .bkh-dia-nums{gap:18px 20px}
.bkh .bkh-areas{grid-template-columns:repeat(2,1fr);gap:12px}
.bkh .bkh-area{aspect-ratio:1/1}
.bkh .bkh-areas .bkh-area:nth-child(n+7){display:none}
.bkh .bkh-area h3{font-size:21px}
.bkh .bkh-services{grid-template-columns:1fr}
.bkh .bkh-step{grid-template-columns:38px 1fr;padding:16px}
.bkh .bkh-step i{width:38px;height:38px;font-size:17px}
.bkh .bkh-item{grid-template-columns:96px 1fr;gap:14px}
.bkh .bkh-val{padding:22px}
.bkh .bkh-close .bkh-ctas .bkh-btn{width:100%}
.bkh-bar{display:grid}
body.bkh-bar-on{padding-bottom:64px}
}
@media (max-width:600px){.bkh .bkh-services{grid-template-columns:1fr}.bkh .bkh-service{min-height:0}}
@media (prefers-reduced-motion:reduce){.bkh *{transition:none!important;scroll-behavior:auto!important}.bkh .bkh-dc:hover .bkh-dc-img,.bkh .bkh-card:hover .bkh-card-img{transform:none}}
CSS;
	}

	/* ------------------------------------------------------------------ JS */

	function blockke_hm22_js() {
		return <<<'JS'
(function(){
  var root=document.querySelector('.bkh');if(!root){return;}
  var C=window.BLOCKHOME||{};
  /* the theme styles the transparent header for body.home; keep that true on previews of this layout too */
  try{document.body.classList.add('home');}catch(e){}
  /* v2.2: one search box. Buy | Rent | Off-plan are radio buttons (the form works without this script: it asks the homepage, which
     answers with the results panel); this script adds the typeahead over the areas (C.ar) and developments (C.dv), goes straight to
     a clear match on Enter (= blockke_hm22_pick()), and keeps "More filters" on the mode */
  (function(){
    var ds=root.querySelector('.bkh-q'),inp=ds&&ds.querySelector('input[name="bkdev"]'),dv=C.dv||{},AR=C.ar||[];
    if(!ds||!inp){return;}
    var D=dv.d||[],FP=dv.f||{},radios=ds.querySelectorAll('input[name="bkfor"]'),more=ds.querySelector('.bkh-q-more'),tl=ds.querySelector('.bkh-pills'),FILL={apartment:1,apartments:1,residence:1,residences:1,residency:1,residencies:1,homes:1,estate:1,the:1,at:1};
    function getMode(){for(var i=0;i<radios.length;i++){if(radios[i].checked){return radios[i].value;}}return 'buy';}
    function fitMore(){var m=getMode();if(more&&more.getAttribute('data-'+m)){more.setAttribute('href',more.getAttribute('data-'+m));}ds.setAttribute('data-mode',m);}
    fitMore();
    for(var ri=0;ri<radios.length;ri++){radios[ri].addEventListener('change',fitMore);}
    if(!D.length&&!AR.length){return;}
    function fold(s){s=String(s==null?'':s);try{s=s.normalize('NFD').replace(/[̀-ͯ]/g,'');}catch(e){}s=s.toLowerCase().replace(/ß/g,'ss').replace(/æ/g,'ae').replace(/œ/g,'oe').replace(/ø/g,'o').replace(/&/g,' and ').replace(/['’`]/g,'');var w=s.replace(/[^a-z0-9]+/g,' ').replace(/^ | $/g,'').split(' '),i;for(i=0;i<w.length-1;i++){if(w[i]==='n'){w[i]='and';}}return w.join(' ');}
    function own(o,k){return Object.prototype.hasOwnProperty.call(o,k);}
    function prep(s){var f=fold(s),w=f?f.split(' '):[],k=[],i;for(i=0;i<w.length;i++){if(!own(FILL,w[i])){k.push(w[i]);}}var o=w.length>0&&!k.length;if(!k.length){k=w;}return {w:w,k:k,c:k.join(''),f:w.join(''),o:o};}
    /* edits between two words, a swap of two neighbouring letters counting as one (optimal string alignment) = blockke_hm22_dev_osa() */
    function osa(a,b){if(a===b){return 0;}var m=a.length,n=b.length,pp=[],p=[],c,i,j;if(!m||!n){return Math.max(m,n);}for(j=0;j<=n;j++){p[j]=j;}for(i=1;i<=m;i++){c=[i];for(j=1;j<=n;j++){c[j]=Math.min(p[j]+1,c[j-1]+1,p[j-1]+(a.charAt(i-1)===b.charAt(j-1)?0:1));if(i>1&&j>1&&a.charAt(i-1)===b.charAt(j-2)&&a.charAt(i-2)===b.charAt(j-1)){c[j]=Math.min(c[j],pp[j-2]+1);}}pp=p;p=c;}return p[n];}
    /* the same first letter, c = k (= blockke_hm22_dev_lead()); a whole-word slip (= blockke_hm22_dev_slip()); a near miss (= blockke_hm22_dev_near()) */
    function lead(t,u){var a=t.charAt(0),b=u.charAt(0);return !!a&&!!b&&(a===b||('ck'.indexOf(a)>=0&&'ck'.indexOf(b)>=0));}
    function slip(t,u){var n=t.length;return n>=4&&n<=40&&u.length<=60&&lead(t,u)&&osa(t,u)<=(n>=8?2:1);}
    function near(t,u){var n=t.length,k;if(n<4||n>40||u.length>60||!lead(t,u)){return false;}if(slip(t,u)){return true;}if(u.length>n){for(k=n-1;k<=n+1;k++){if(osa(t,u.slice(0,k))<=1){return true;}}}return false;}
    function starts(t,w){for(var j=0;j<w.length;j++){if(w[j].indexOf(t)===0){return true;}}return false;}
    function score(q,m){
      if(!q.c){return 0;}
      if(m.f===q.f){return 100;}
      if(m.c===q.c){return 99;}
      var tg=q.o?m.f:m.c,i,j,ok;
      if(tg.indexOf(q.c)===0){return 80;}
      for(i=0,ok=true;i<q.k.length&&ok;i++){ok=starts(q.k[i],m.w);}
      if(ok){return 60+(m.k.length&&m.k[0].indexOf(q.k[0])===0?4:0);}
      if(q.c.length>=3&&tg.indexOf(q.c)>=0){return 40;}
      for(i=0;i<q.k.length;i++){ok=starts(q.k[i],m.w);for(j=0;!ok&&j<m.w.length;j++){ok=near(q.k[i],m.w[j]);}if(!ok){return 0;}}
      return 20;
    }
    var P=[],PL=[],AP=[],AREA=null,NOTAREA={road:1,city:1,town:1,general:1,river:1,rivers:1,spring:1,springs:1,valley:1,south:1,north:1,east:1,west:1,upper:1,lower:1,garden:1,gardens:1,plains:1,park:1,hill:1,hills:1,view:1,views:1,estate:1,beach:1,heights:1,ridge:1,gate:1,green:1,court:1,place:1,square:1},n,a;
    for(n=0;n<D.length;n++){P[n]=[prep(D[n][0])];for(a=0;a<(D[n][7]||[]).length;a++){P[n].push(prep(D[n][7][a]));}PL[n]=prep(D[n][1]||'').w;}
    for(n=0;n<AR.length;n++){AP[n]=prep(AR[n][0]);}
    function all(q,i){var s=score(q,P[i][0]),x;for(x=1;x<P[i].length;x++){s=Math.max(s,score(q,P[i][x])-1);}return s;}
    /* area words (= blockke_hm22_dev_areas()) and the query without them (= blockke_hm22_dev_unplace()) */
    function areas(){if(AREA){return AREA;}var seen={},i,j,u;AREA=[];for(i=0;i<PL.length;i++){for(j=0;j<PL[i].length;j++){u=PL[i][j];if(u.length>=4&&!own(NOTAREA,u)&&!own(seen,u)){seen[u]=1;AREA.push(u);}}}if(!own(seen,'nairobi')){AREA.push('nairobi');}if(!own(seen,'kenya')){AREA.push('kenya');}return AREA;}
    function unplace(q,pl){
      var n=q.w.length,keep=[],i,j,t,u,hit,r;if(!pl.length||n<2){return null;}
      for(i=0;i<n;i++){t=q.w[i];hit=false;for(j=0;j<pl.length&&!hit;j++){u=pl[j];hit=u===t||(i===n-1&&u.indexOf(t)===0)||(t.length>=5&&slip(t,u));}if(!hit){keep.push(t);}}
      if(!keep.length||keep.length===n){return null;}
      r=prep(keep.join(' '));return r.o?null:r;
    }
    function pref(i,mode){var b=D[i][5]||0;return mode==='rent'?((b&2)?2:0):((b&1)?2:0)+(mode==='offplan'&&(b&4)?1:0);}
    function fp(i){var v=FP[i];return typeof v==='number'?v:999;}
    function find(text,mode){
      var q=prep(text),r=[],sc=[],top=0,i,s,q1,q2,lo;if(!q.c||!D.length){return r;}
      for(i=0;i<D.length;i++){s=all(q,i);q1=unplace(q,PL[i]);if(q1){s=Math.max(s,all(q1,i)-1);}sc[i]=s;if(s>top){top=s;}}
      q2=top<35?unplace(q,areas()):null;
      if(q2){for(i=0;i<D.length;i++){sc[i]=Math.max(sc[i],all(q2,i)-2);if(sc[i]>top){top=sc[i];}}}
      lo=top<35?1:top>=50?50:35;
      for(i=0;i<D.length;i++){if(sc[i]>=lo){r.push([i,sc[i]]);}}
      r.sort(function(x,y){return (y[1]-x[1])||(pref(y[0],mode)-pref(x[0],mode))||(fp(x[0])-fp(y[0]))||(P[x[0]][0].c.length-P[y[0]][0].c.length)||(x[0]-y[0]);});
      return r;
    }
    /* the one development a search clearly means, or -1 (= blockke_hm22_dev_strong()) */
    function strong(r,text){
      var q=prep(text),i,a,b,k,x,j,y,w,ok,hit;if(!r.length||q.o){return -1;}
      i=r[0][0];a=r[0][1];b=r.length>1?r[1][1]:0;
      if((a>=99&&b<a)||(r.length===1&&a>=40)||(a>=60&&b<=40)){return i;}
      if(r.length!==1||a>=35){return -1;}
      k=unplace(q,PL[i])||q;
      if((k.c.match(/[a-z]/g)||[]).length<5){return -1;}
      for(x=0;x<P[i].length;x++){w=P[i][x].w;for(j=0,ok=true;j<k.k.length&&ok;j++){for(y=0,hit=false;y<w.length&&!hit;y++){hit=w[y].indexOf(k.k[j])===0||slip(k.k[j],w[y]);}ok=hit;}if(ok){return i;}}
      return -1;
    }
    /* areas (= blockke_hm22_area_noise(), _area_numword(), _area_cities(), _area_partial(), _area_q(), _area_city(), _area_find(), _area_n(), _area_say()) */
    var ANOISE={home:1,homes:1,house:1,houses:1,apartment:1,apartments:1,flat:1,flats:1,villa:1,villas:1,townhouse:1,townhouses:1,maisonette:1,maisonettes:1,studio:1,studios:1,bedroom:1,bedrooms:1,bedroomed:1,bed:1,beds:1,br:1,'for':1,sale:1,rent:1,rental:1,rentals:1,to:1,let:1,buy:1,'in':1,at:1,near:1,around:1,property:1,properties:1,plot:1,plots:1,offplan:1,off:1,plan:1,'new':1,the:1,a:1,and:1,area:1,areas:1,neighbourhood:1,neighborhood:1,county:1,region:1,kenya:1},NUMW=/^[0-9]*(?:br|brs|bd|bdr|bdrm|bdrms|bed|beds|bedrm|bedroom|bedrooms|bedroomed)$|^[0-9]+$/,CITY={},ACI=[];
    var x0,cf;for(n=0;n<AR.length;n++){ACI[n]=[];for(a=0,x0=String(AR[n][10]||'').split('|');a<x0.length;a++){cf=prep(x0[a]).f;if(cf){ACI[n].push(cf);CITY[cf]=1;}}}
    function partial(t){var k;for(k in ANOISE){if(own(ANOISE,k)&&k.length>t.length&&k.indexOf(t)===0){return true;}}for(k in CITY){if(own(CITY,k)&&k.length>t.length&&k.indexOf(t)===0){return true;}}return false;}
    function aq(text){var q=prep(text),n=q.w.length,keep=[],i,t,r;for(i=0;i<n;i++){t=q.w[i];if(own(ANOISE,t)||own(CITY,t)||NUMW.test(t)){continue;}if(n>1&&i===n-1&&partial(t)){continue;}keep.push(t);}if(!keep.length||keep.length===n){return null;}r=prep(keep.join(' '));return r.c?r:null;}
    function acity(text){var q=prep(text),rest='',i;for(i=0;i<q.w.length;i++){if(!own(ANOISE,q.w[i])&&!NUMW.test(q.w[i])){rest+=q.w[i];}}return rest&&own(CITY,rest)?rest:'';}
    /* an area row: [ name, slug, area page, Buy page, Rent page, Off-plan page ('' = the area page), for sale, to rent, off-plan, all, cities ] */
    function an(i,mode){var e=AR[i];return (mode==='rent'?e[7]:mode==='offplan'?e[8]:e[6])||0;}
    function num(x){return String(x).replace(/\B(?=(\d{3})+(?!\d))/g,',');}
    function asay(i,mode){var x=an(i,mode);if(mode==='rent'){return x?num(x)+' to rent':'None listed to rent';}if(mode==='offplan'){return x?num(x)+' off-plan':'No off-plan listed';}return x?num(x)+' for sale':'None listed for sale';}
    function ago(i,mode){var e=AR[i],g=mode==='rent'?e[4]:mode==='offplan'?e[5]:e[3];return g||e[2];}
    function afind(text,mode){
      var q=prep(text),q1,city,r=[],sc=[],top=0,i,s,lo;if(!q.c||!AR.length){return r;}
      q1=aq(text);city=acity(text);
      for(i=0;i<AR.length;i++){s=score(q,AP[i]);if(q1){s=Math.max(s,score(q1,AP[i])-1);}if(city&&s<85&&ACI[i].indexOf(city)>=0){s=85;}sc[i]=s;if(s>top){top=s;}}
      lo=top>=60?60:top>=35?35:1;
      for(i=0;i<AR.length;i++){if(sc[i]>=lo){r.push([i,sc[i]]);}}
      r.sort(function(x,y){return (y[1]-x[1])||(an(y[0],mode)-an(x[0],mode))||(AR[y[0]][9]-AR[x[0]][9])||cmp(AR[x[0]][0],AR[y[0]][0]);});
      return r;
    }
    function cmp(x,y){x=x.toLowerCase();y=y.toLowerCase();return x<y?-1:x>y?1:0;}
    /* what a search clearly means (= blockke_hm22_pick()): ['a', area] | ['d', development] | null (the results panel) */
    function pick(text,r,ar){
      var q=prep(text),a1,a2,d1,ds,k,w,i,j,ok,hit,city;if(!q.c||q.o){return null;}
      a1=ar.length?ar[0][1]:0;a2=ar.length>1?ar[1][1]:0;d1=r.length?r[0][1]:0;
      if(a1>=98&&a2<98&&d1<100){return ['a',ar[0][0]];}
      city=acity(text)!=='';
      if(!city&&ar.length===1&&a1<35&&d1<35){
        k=aq(text)||q;
        if((k.c.match(/[a-z]/g)||[]).length>=5){w=AP[ar[0][0]].w;for(i=0,ok=true;i<k.k.length;i++){for(j=0,hit=false;j<w.length&&!hit;j++){hit=w[j].indexOf(k.k[i])===0||slip(k.k[i],w[j]);}ok=ok&&hit;}if(ok){return ['a',ar[0][0]];}}
      }
      ds=strong(r,text);
      if(ds>=0&&(d1>a1||a1<35)){return ['d',ds];}
      if(!city&&a1>=60&&a2<60&&d1<35){return ['a',ar[0][0]];}
      return null;
    }
    /* with an area's name typed exactly, developments that only match a stray word ("homes in" Kilimani) go (= blockke_hm22_dev_trim()) */
    function trim(r,ar){if(!ar.length||ar[0][1]<98){return r;}var o=[],i;for(i=0;i<r.length;i++){if(r[i][1]>=60){o.push(r[i]);}}return o;}
    /* what the server keeps of the text typed (= blockke_hm22_dev_query()): tags, web and e-mail addresses, @handles and every digit
       once there are 5 or more go, at most 80 characters. GA4 and the address only ever get this, never the text as typed */
    var RX=null;
    function clean(s){
      var w,keep=[],dot=false,i,t,tld,cp;
      s=String(s==null?'':s);
      if(s.indexOf('<')>=0){s=s.replace(/<[^>]*?(?=<|>|$)>?/g,function(m){return m.indexOf('>')>=0?m:m.replace(/&(?![a-z][a-z0-9]*;|#[0-9]+;|#x[0-9a-f]+;)/gi,'&amp;').replace(/</g,'&lt;').replace(/"/g,'&quot;').replace(/'/g,'&#039;');}).replace(/<(script|style)[^>]*?>[\s\S]*?<\/\1>/gi,'').replace(/<(?!\s)[^>]*>/g,'');}
      s=s.replace(/[\r\n\t ]+/g,' ');while(/%[a-f0-9]{2}/i.test(s)){s=s.replace(/%[a-f0-9]{2}/gi,'');}
      if(!RX){try{RX=[new RegExp('[^\\p{L}\\p{M}0-9\\s&\'\\u2019.,@()\\-]+','gu'),new RegExp('\\.[\\p{L}\\p{M}0-9]{2}|^(?:https?|www)$|^[\\-.,()\'\\u2019]+$','iu'),new RegExp('^[\\p{L}\\p{M}]+@[\\p{L}\\p{M}]+$','u')];}catch(x){RX=[/[^A-Za-zÀ-ɏ̀-ͯ0-9\s&'’.,@()\-]+/g,/\.[A-Za-zÀ-ɏ0-9]{2}|^(?:https?|www)$|^[\-.,()'’]+$/i,/^[A-Za-zÀ-ɏ]+@[A-Za-zÀ-ɏ]+$/];}}
      s=s.replace(RX[0],' ');
      if((s.match(/[0-9]/g)||[]).length>=5){s=s.replace(/[0-9]+/g,' ');}
      w=s.split(/\s+/);
      for(i=0;i<w.length;i++){t=w[i];if(!t){continue;}tld=dot&&/^(?:com|co|ke|net|org|info|biz|io|me)$/i.test(t);dot=t.toLowerCase()==='dot';if(tld){keep.pop();continue;}if(RX[1].test(t)||(t.indexOf('@')>=0&&!RX[2].test(t))){continue;}keep.push(t);}
      s=keep.join(' ');cp=Array.from?Array.from(s):s.split('');
      return cp.length>80?cp.slice(0,80).join('').replace(/\s+$/,''):s;
    }
    function money(s,rent){if(!s){return '';}var f=s.charAt(0)==='+';return (f?'From ':'')+'KES '+(f?s.slice(1):s)+(rent?' a month':'');}
    function link(p){return (p.charAt(0)==='/'||/^https?:/i.test(p))?p:(dv.b||'/')+p+'/';}
    function show(i,mode){
      var e=D[i],b=e[5]||0,rent=mode==='rent',use=rent?!!((b&2)||!(b&1)):!!(!(b&1)&&(b&2)),meta=[],price=use?money(e[4],true):money(e[3],false);
      if(e[1]){meta.push(e[1]);}
      if(b&8){meta.push('Land');}else if(b&16){meta.push('Commercial');}
      if(!rent&&(b&4)&&!use){meta.push('Off-plan');}
      if(rent&&!(b&2)&&(b&1)){meta.push('For sale');}
      if(!rent&&use){meta.push('To rent');}
      return {url:link(use&&e[6]?e[6]:e[2]),price:price||'Prices on request',meta:meta.join(' · ')};
    }
    /* the listbox lives in .bkh itself (the hero clips what overflows it) and is placed under the box, or over it when there is more room above */
    var pop=document.createElement('div'),lb=document.createElement('ul'),msg=document.createElement('p'),live=document.createElement('div');
    pop.className='bkh-dl';pop.hidden=true;lb.id='bkh-q-lb';lb.setAttribute('role','listbox');lb.setAttribute('aria-label','Areas and developments');
    msg.className='bkh-dl-msg';msg.hidden=true;pop.appendChild(lb);pop.appendChild(msg);root.appendChild(pop);
    live.className='bkh-sr';live.setAttribute('role','status');live.setAttribute('aria-live','polite');ds.appendChild(live);
    var box=ds.querySelector('.bkh-q-box')||ds;
    inp.removeAttribute('list');
    inp.setAttribute('role','combobox');inp.setAttribute('aria-autocomplete','list');inp.setAttribute('aria-expanded','false');inp.setAttribute('aria-controls',lb.id);
    var opts=[],act=-1,open=false,down=false,said='',sent='',tmr=0;
    function say(t){if(t===said){return;}said=t;clearTimeout(tmr);tmr=setTimeout(function(){live.textContent=t;},350);}
    function track(term,where){if(!term||term===sent){return;}sent=term;try{if(typeof window.gtag==='function'){window.gtag('event','search',{search_term:term,placement:where});}}catch(x){}}
    function trackArea(i,m){track(m+' '+AR[i][1],'home_hero');} /* as v2.1's filter form did: the mode and the area's slug */
    /* opened upwards, the list stops under the Buy | Rent | Off-plan pills while they are on screen, so they can still be clicked */
    function place(fit){
      if(pop.hidden){return;}
      var vv=window.visualViewport,vt=vv?vv.offsetTop:0,vh=vv?vv.height:(window.innerHeight||document.documentElement.clientHeight),bar=document.body.classList.contains('bkh-bar-on')?64:0;
      var r,tb,on,below,above,need,want,d;
      function room(){r=box.getBoundingClientRect();tb=tl?tl.getBoundingClientRect():null;on=!!(tb&&tb.height&&tb.bottom>vt&&tb.top<vt+vh);below=vt+vh-r.bottom-10-bar;above=on?r.top-tb.bottom-8:r.top-vt-10;}
      pop.style.maxHeight='none';pop.style.minHeight='';
      need=Math.min(pop.scrollHeight,460);want=Math.min(need,340);room();
      /* the pills sit right above the box, so the list nearly always opens below it: when less than it wants fits there, the page
         scrolls up just enough (the box stays 96px from the top and the pills on screen) */
      if(fit&&below<want&&above<Math.min(need,200)){
        d=Math.floor(Math.min(want-below,r.top-vt-96,(on&&tb.top>=vt+8)?tb.top-vt-8:1e4));
        if(d>8){try{window.scrollBy({top:d,left:0,behavior:'instant'});}catch(x){window.scrollBy(0,d);}room();}
      }
      var up=below<want&&above>below,rm=up?Math.max(64,Math.min(460,Math.floor(above))):Math.max(150,Math.min(460,Math.floor(below)));
      pop.style.maxHeight=rm+'px';
      var h=Math.min(pop.scrollHeight+2,rm),rr=root.getBoundingClientRect(),top=Math.round(up?(r.top-rr.top-h-6):(r.bottom-rr.top+6));
      pop.style.left=Math.round(r.left-rr.left)+'px';pop.style.width=Math.round(r.width)+'px';
      pop.style.top=top+'px';
      pop.classList.toggle('is-up',up);
    }
    function close(){if(pop.hidden&&!open){return;}open=false;act=-1;pop.hidden=true;inp.setAttribute('aria-expanded','false');inp.removeAttribute('aria-activedescendant');}
    function mark(el,name,q){
      var i=q?name.toLowerCase().indexOf(q.toLowerCase()):-1;
      if(i<0){el.textContent=name;return;}
      var m=document.createElement('mark');m.textContent=name.substr(i,q.length);
      el.appendChild(document.createTextNode(name.slice(0,i)));el.appendChild(m);el.appendChild(document.createTextNode(name.slice(i+q.length)));
    }
    function head(t){var li=document.createElement('li');li.className='bkh-dl-gh';li.setAttribute('role','presentation');li.textContent=t;lb.appendChild(li);}
    function opt(id,name,q,meta,price,label){
      var li=document.createElement('li'),sp;li.id=id;li.setAttribute('role','option');li.setAttribute('aria-selected','false');li.setAttribute('aria-label',label);
      sp=document.createElement('span');sp.className='bkh-dl-name';mark(sp,name,q);li.appendChild(sp);
      if(meta){sp=document.createElement('span');sp.className='bkh-dl-meta';sp.textContent=meta;li.appendChild(sp);}
      sp=document.createElement('span');sp.className='bkh-dl-price';sp.textContent=price;li.appendChild(sp);
      lb.appendChild(li);return li;
    }
    function render(nofit){
      var q=inp.value.replace(/^\s+|\s+$/g,''),mode=getMode(),r,ar,i,li,s,na,nd,k=0,e,meta;
      if(!q){close();say('');return;}
      ar=afind(q,mode);r=trim(find(q,mode),ar);opts=[];act=-1;lb.innerHTML='';inp.removeAttribute('aria-activedescendant');
      na=Math.min(ar.length,r.length?3:6);nd=Math.min(r.length,na?6:8);
      if(na&&nd){head('Neighbourhoods');}
      for(i=0;i<na;i++){
        e=AR[ar[i][0]];meta='Neighbourhood';s=asay(ar[i][0],mode);
        li=opt('bkh-q-o'+(k++),e[0],q,meta,s,e[0]+', neighbourhood, '+s);
        opts.push({li:li,url:ago(ar[i][0],mode),area:ar[i][0]});
      }
      if(na&&nd){head('Developments');}
      for(i=0;i<nd;i++){
        s=show(r[i][0],mode);
        li=opt('bkh-q-o'+(k++),D[r[i][0]][0],q,s.meta,s.price,D[r[i][0]][0]+(s.meta?', '+s.meta:'')+', '+s.price);
        opts.push({li:li,url:s.url});
      }
      if(ar.length>na||r.length>nd){
        li=document.createElement('li');li.id='bkh-q-o'+(k++);li.className='bkh-dl-all';li.setAttribute('role','option');li.setAttribute('aria-selected','false');
        li.textContent=r.length>12?'See the best 12 of '+r.length:'See all '+(r.length+ar.length)+' matches';li.insertAdjacentHTML('beforeend','<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>');
        lb.appendChild(li);opts.push({li:li,all:true});
      }
      if(opts.length){msg.hidden=true;lb.hidden=false;}
      else{lb.hidden=true;msg.hidden=false;msg.textContent='Nothing matches “'+(q.length>40?q.slice(0,39)+'…':q)+'” yet. Search anyway and we’ll suggest some.';}
      open=opts.length>0;pop.hidden=false;inp.setAttribute('aria-expanded',open?'true':'false');place(nofit!==true);
      say(opts.length?((na?na+(na===1?' neighbourhood':' neighbourhoods'):'')+(na&&nd?' and ':'')+(nd?r.length+(r.length===1?' development':' developments'):'')+(na+nd===1?' matches':' match')+'. Use the up and down arrows to choose one.'):'Nothing matches.');
    }
    function setAct(i,scroll){
      if(act>=0&&opts[act]){opts[act].li.setAttribute('aria-selected','false');}
      act=i;
      if(act<0||!opts[act]){act=-1;inp.removeAttribute('aria-activedescendant');return;}
      var li=opts[act].li;li.setAttribute('aria-selected','true');inp.setAttribute('aria-activedescendant',li.id);
      if(scroll){var t=li.offsetTop,b=t+li.offsetHeight,p=li.previousSibling;if(p&&p.className==='bkh-dl-gh'){t=p.offsetTop;}if(t<pop.scrollTop){pop.scrollTop=t;}else if(b>pop.scrollTop+pop.clientHeight){pop.scrollTop=b-pop.clientHeight;}}
    }
    function move(d){if(!opts.length){return;}var i=act+d;if(i>=opts.length){i=-1;}else if(i<-1){i=opts.length-1;}setAct(i,true);}
    function choose(i){
      var o=opts[i],q=clean(inp.value);if(!o){return;}
      if(o.area!=null){trackArea(o.area,getMode());}else{track(q,'home_dev');}
      close();
      if(o.all){inp.value=q;ds.submit();return;}
      window.location.href=o.url;
    }
    /* a press that starts in the box or the list (pd) is never taken for a click outside it, and never scrolls the page
       while the button is down: focus from it opens the list where it fits, and the list is fitted once the click is over */
    var pd=false,pdT=0,refit=false;
    function settle(){clearTimeout(pdT);pd=false;if(refit){refit=false;if(!pop.hidden){place(true);}}}
    document.addEventListener('pointerdown',function(e){clearTimeout(pdT);pd=!!(e.target&&(box.contains(e.target)||pop.contains(e.target)));},true);
    function lift(){if(pd){clearTimeout(pdT);pdT=setTimeout(settle,700);}}
    document.addEventListener('pointerup',lift,true);document.addEventListener('pointercancel',lift,true);
    inp.addEventListener('input',function(){refit=false;render();});
    inp.addEventListener('focus',function(){if(inp.value.replace(/\s+/g,'')){if(pd){refit=true;render(true);}else{render();}}});
    inp.addEventListener('click',function(){if(pop.hidden&&inp.value.replace(/\s+/g,'')){if(pd){refit=true;render(true);}else{render();}}});
    inp.addEventListener('keydown',function(e){
      var k=e.key;
      if(refit){refit=false;if(open&&k!=='Escape'&&k!=='Esc'&&k!=='Tab'){place(true);}}
      if(k==='ArrowDown'||k==='Down'){e.preventDefault();if(!open){render();}move(1);}
      else if(k==='ArrowUp'||k==='Up'){e.preventDefault();if(!open){render();}move(-1);}
      else if(k==='Enter'){if(open&&act>=0){e.preventDefault();choose(act);}}
      else if(k==='Escape'||k==='Esc'){if(!pop.hidden){e.preventDefault();close();}else if(inp.value){e.preventDefault();inp.value='';say('');}}
      else if(k==='Tab'){close();}
      else if((k==='ArrowLeft'||k==='ArrowRight'||k==='Left'||k==='Right'||k==='Home'||k==='End')&&act>=0){setAct(-1,false);}
    });
    inp.addEventListener('blur',function(){setTimeout(function(){if(!down&&document.activeElement!==inp){close();}},150);});
    pop.addEventListener('mousedown',function(e){e.preventDefault();});
    pop.addEventListener('pointerdown',function(){down=true;});
    document.addEventListener('pointerup',function(){setTimeout(function(){down=false;},300);},true);
    /* the mouse highlights an option only when it really moves (browsers also send mousemove when the list opens under a resting pointer) */
    var mx=null,my=null,mv=false;
    document.addEventListener('mousemove',function(e){mv=mx===null?!!(e.movementX||e.movementY):(e.clientX!==mx||e.clientY!==my);mx=e.clientX;my=e.clientY;},true);
    pop.addEventListener('mousemove',function(e){if(!mv){return;}var li=e.target&&e.target.closest?e.target.closest('li[role="option"]'):null;if(li){for(var i=0;i<opts.length;i++){if(opts[i].li===li&&act!==i){setAct(i,false);}}}});
    pop.addEventListener('click',function(e){var li=e.target&&e.target.closest?e.target.closest('li[role="option"]'):null;if(!li){return;}for(var i=0;i<opts.length;i++){if(opts[i].li===li){choose(i);return;}}});
    /* Buy | Rent | Off-plan while the list is open: it stays open, re-ordered and re-counted for the new mode, and the text keeps the focus */
    var keep=false;
    if(tl){tl.addEventListener('pointerdown',function(){keep=!pop.hidden;if(keep){down=true;}});}
    for(var rj=0;rj<radios.length;rj++){radios[rj].addEventListener('change',function(){if(keep||!pop.hidden){keep=false;render();try{inp.focus({preventScroll:true});}catch(x){inp.focus();}}});}
    document.addEventListener('click',function(e){if(pd){clearTimeout(pdT);pdT=setTimeout(settle,0);return;}var t=e.target;if(t&&!box.contains(t)&&!pop.contains(t)&&!(tl&&tl.contains(t))){close();}});
    ds.addEventListener('submit',function(e){
      var q=inp.value.replace(/^\s+|\s+$/g,'');
      if(!q){e.preventDefault();inp.focus();return;}
      var m=getMode(),ar=afind(q,m),r=trim(find(q,m),ar),hit=pick(q,r,ar);
      if(hit&&hit[0]==='a'){trackArea(hit[1],m);e.preventDefault();close();window.location.href=ago(hit[1],m);return;}
      track(clean(q),'home_dev');
      if(hit){e.preventDefault();close();window.location.href=show(hit[1],m).url;return;}
      inp.value=clean(q); /* the address carries only the cleaned text (none left: the panel suggests some) */
      close();
    });
    var raf=0;function later(){if(!raf){raf=(window.requestAnimationFrame||setTimeout)(function(){raf=0;place(false);});}}
    window.addEventListener('resize',later);
    window.addEventListener('scroll',later,{passive:true});
    if(window.visualViewport){window.visualViewport.addEventListener('resize',later);window.visualViewport.addEventListener('scroll',later);}
  })();
  /* listing tabs */
  var lt=root.querySelectorAll('.bkh-ltab');
  for(var t=0;t<lt.length;t++){lt[t].addEventListener('click',function(){
    var id=this.getAttribute('aria-controls');
    for(var a=0;a<lt.length;a++){lt[a].setAttribute('aria-selected',lt[a]===this?'true':'false');}
    var grids=root.querySelectorAll('.bkh-grid');for(var g=0;g<grids.length;g++){grids[g].hidden=grids[g].id!==id;}
    var foot=root.querySelectorAll('.bkh-lfoot a');for(var f=0;f<foot.length;f++){foot[f].hidden=foot[f].getAttribute('data-for')!==id;}
  });}
  /* developments carousel (v2.2): the arrows show and scroll the row to a card's edge (smoothly, unless reduced motion is asked for):
     Next brings the first card that is not wholly on screen to the left edge, Previous the cards before the first one on screen. At
     either end an arrow is aria-disabled (it keeps the focus; its clicks are ignored); when every card fits there are none. The row
     also scrolls and swipes. Once it has moved it is marked is-moved, so the first card (then off to the left) stays as it is and a
     card opening under the pointer never shifts the row. A press while the row is still gliding steps on from where it is heading. On screens 901px and wider the cards are sized (--dc-c, --dc-o) so the
     next card always shows by about 56px. A card reached with the keyboard is scrolled wholly into view (v2.2.1: with reduced motion
     and with a mouse resting over the row too, which opens no card while the keyboard is in the row) */
  (function(){
    var row=root.querySelector('.bkh-dc-row'),btns=root.querySelectorAll('.bkh-dc-btn'),mm=window.matchMedia,rm=mm?mm('(prefers-reduced-motion: reduce)'):null,hv=mm?mm('(hover: hover) and (pointer: fine) and (min-width: 901px)'):null;
    if(!row||!btns.length){return;}
    var cards=row.querySelectorAll('.bkh-dc');
    function beh(){return rm&&rm.matches?'auto':'smooth';}
    function px(v){return parseFloat(v)||0;}
    function fit(){
      var cs=getComputedStyle(row),p=px(cs.paddingLeft),g=px(cs.columnGap)||14,v=row.clientWidth-p,peek=56,m,w;
      row.classList.add('is-sizing');setTimeout(function(){row.classList.remove('is-sizing');},120); /* sized at once, not animated */
      if((window.innerWidth||document.documentElement.clientWidth)<901||!cards.length){row.style.removeProperty('--dc-c');row.style.removeProperty('--dc-o');return;}
      if(hv&&hv.matches){m=Math.max(3,Math.round((v-peek)/(250+g)));w=Math.max(200,Math.min(300,(v-peek)/m-g));row.style.setProperty('--dc-c',w.toFixed(2)+'px');row.style.setProperty('--dc-o',(2*w+g).toFixed(2)+'px');}
      else{m=Math.max(1,Math.round((v-peek)/(300+g)));w=Math.max(240,Math.min(400,(v-peek)/m-g));row.style.setProperty('--dc-c',w.toFixed(2)+'px');row.style.removeProperty('--dc-o');}
    }
    function geo(){var rl=row.getBoundingClientRect().left,p=px(getComputedStyle(row).paddingLeft),x=row.scrollLeft,L=[],W=[],i,r;for(i=0;i<cards.length;i++){r=cards[i].getBoundingClientRect();L.push(r.left-rl+x-p);W.push(r.width);}return {L:L,W:W,x:x,v:row.clientWidth-p,max:row.scrollWidth-row.clientWidth};}
    var pend=null,pt=0; /* where a smooth scroll the arrows started is heading: a second press steps on from there */
    function go(d){
      var g=geo(),t,i,e,lim;if(pend!==null){g.x=pend;}
      /* 3px of slack either way: card edges fall on fractions of a pixel */
      if(d>0){t=g.max;for(i=0;i<g.L.length;i++){if(g.L[i]+g.W[i]>g.x+g.v+3){t=g.L[i];break;}}if(t<=g.x+3){t=g.x+g.v;}}
      else{for(e=-1,i=0;i<g.L.length&&g.L[i]<g.x-3;i++){e=i;}t=0;if(e>=0){t=g.L[e];lim=g.L[e]+g.W[e]-g.v-3;for(i=0;i<=e;i++){if(g.L[i]>=lim){t=g.L[i];break;}}}}
      t=Math.max(0,Math.min(g.max,Math.round(t)));pend=t;unhold();clearTimeout(pt);pt=setTimeout(function(){pend=null;},1500);
      try{row.scrollTo({left:t,behavior:beh()});}catch(x){row.scrollLeft=t;}
    }
    function upd(){var max=row.scrollWidth-row.clientWidth,i,off;if(pend!==null&&Math.abs(row.scrollLeft-pend)<2){pend=null;}if(hold!==null&&Math.abs(row.scrollLeft-hold)<2){hold=null;}row.classList.toggle('is-moved',row.scrollLeft>2||(hold!==null&&hold>2));for(i=0;i<btns.length;i++){off=+btns[i].getAttribute('data-dir')<0?row.scrollLeft<=2:row.scrollLeft>=max-2;btns[i].hidden=max<=2;btns[i].setAttribute('aria-disabled',off?'true':'false');}}
    for(var i=0;i<btns.length;i++){btns[i].addEventListener('click',function(){if(this.getAttribute('aria-disabled')!=='true'){go(+this.getAttribute('data-dir'));}});}
    var pdn=0,hold=null,ht=0,st=0,px0=null,py0=null;
    /* v2.2.1: while the keyboard moves through the row (is-kb), a pointer resting over it opens no card, so the row moves only for the
       focused card; the pointer's first real move, a press, or the focus leaving the row hands the row back to the pointer */
    function kbOff(){row.classList.remove('is-kb');}
    row.addEventListener('pointerdown',function(){pdn=Date.now();kbOff();},true);
    row.addEventListener('pointermove',function(e){if(px0!==null&&Math.abs(e.screenX-px0)+Math.abs(e.screenY-py0)>2){kbOff();}px0=e.screenX;py0=e.screenY;},{passive:true});
    row.addEventListener('focusout',function(e){if(!e.relatedTarget||!row.contains(e.relatedTarget)){kbOff();}});
    /* v2.2.1: the row is held marked moved while a scroll the script started is on its way (hold: where it is heading); when the hold
       ends, by arriving or after 1.5s, the mark is worked out again from where the row is */
    function hold2(x){hold=x;clearTimeout(ht);ht=setTimeout(function(){hold=null;upd();},1500);row.classList.add('is-moved');}
    function unhold(){hold=null;clearTimeout(ht);}
    /* v2.2.1: with a mouse, where every card will be once the focused one has opened, from the widths the script set (--dc-c, --dc-o),
       not from widths caught halfway through a transition: the first card is open once the row has moved, the focused card is open,
       the others closed (a card under a resting pointer stays closed while the keyboard is in the row) */
    function plan(k,moved){var cs=getComputedStyle(row),c=px(row.style.getPropertyValue('--dc-c')),o=px(row.style.getPropertyValue('--dc-o')),gp=px(cs.columnGap)||14,L=[],W=[],a=0,i,w;for(i=0;i<cards.length;i++){w=(i===0&&moved)||i===k||(!row.classList.contains('is-kb')&&cards[i].matches(':hover'))?o:c;L.push(a);W.push(w);a+=w+gp;}return {L:L,W:W,end:a-gp+px(cs.paddingRight)};}
    /* v2.2.1: once the row and the cards have stopped moving, the focused card is measured again and, if it is not wholly in view,
       scrolled so that it is (at most three times) */
    function settle(c,n){
      var last='',same=0;clearTimeout(st);
      (function tick(){
        var r,rr,lo,hi,d=0,key,x;if(!c.contains(document.activeElement)){return;}
        r=c.getBoundingClientRect();key=Math.round(r.left)+'|'+Math.round(r.width)+'|'+Math.round(row.scrollLeft);
        if(key!==last){last=key;same=0;st=setTimeout(tick,120);return;}
        if(++same<3){st=setTimeout(tick,120);return;}
        rr=row.getBoundingClientRect();lo=rr.left+px(getComputedStyle(row).paddingLeft);hi=Math.min(rr.left+row.clientWidth,document.documentElement.clientWidth);
        if(r.right>hi+1){d=r.right-hi;}if(r.left-d<lo-1){d=r.left-lo;}
        if(Math.abs(d)>1&&n<3){x=Math.max(0,Math.round(row.scrollLeft+d));if(x>2){hold2(x);}else{unhold();}try{row.scrollTo({left:x,behavior:beh()});}catch(y){row.scrollLeft=x;}settle(c,n+1);}
      })();
    }
    row.addEventListener('focusin',function(e){
      var t=e.target,c=t&&t.closest?t.closest('.bkh-dc'):null,g,k,f,lk,wf,x,mx,r,dy,mv=false;if(!c){return;}
      if(Date.now()-pdn<800){return;} /* the focus a click or a tap gives never scrolls the row under the pointer */
      if(hv&&hv.matches){row.classList.add('is-kb');}
      /* where the card will be once it has opened: the least scroll that shows all of it, or its left edge where the row snaps. With a
         mouse (v2.2.1): at the row's start the first card closes for the focused one when that card then fits; when the row has to move,
         it is marked moved before it scrolls, so the first card stays open and the card lands where it was planned */
      g=geo();for(k=0;k<cards.length&&cards[k]!==c;k++){}
      if(hv&&hv.matches&&k>0&&px(row.style.getPropertyValue('--dc-o'))){
        f=plan(k,g.x>2);
        if(g.x<=2&&f.L[k]+f.W[k]>g.v+1){f=plan(k,true);mv=true;}
        lk=f.L[k];wf=f.W[k];mx=Math.max(0,f.end-g.v);
      }else{lk=g.L[k];wf=g.W[k];mx=g.max;}
      x=g.x;
      if(lk<x-1){x=lk;}else if(lk+wf>x+g.v+1){x=getComputedStyle(row).scrollSnapType.indexOf('mandatory')>=0?lk:lk+wf-g.v;}
      x=Math.max(0,Math.min(mx,Math.round(x)));
      if(Math.abs(x-g.x)>1){if(mv||(f&&x>2)){hold2(x);}else{unhold();}try{row.scrollTo({left:x,behavior:beh()});}catch(y){row.scrollLeft=x;}}
      r=c.getBoundingClientRect();dy=r.top<0?r.top-8:(r.bottom>window.innerHeight?Math.min(r.top-8,r.bottom-window.innerHeight+8):0);
      if(dy){try{window.scrollBy({top:dy,left:0,behavior:beh()});}catch(y){window.scrollBy(0,dy);}}
      settle(c,0);
    });
    var tk=0;row.addEventListener('scroll',function(){if(!tk){tk=(window.requestAnimationFrame||setTimeout)(function(){tk=0;upd();});}},{passive:true});
    function all(){fit();upd();}
    window.addEventListener('resize',all);if(hv&&hv.addEventListener){hv.addEventListener('change',all);}all();try{document.fonts.ready.then(upd);}catch(e){}
  })();
  /* contact click logging (same endpoint + report as the listing pages) */
  var sid='';try{sid=sessionStorage.getItem('blkbp_sid')||'';if(!sid){sid=Math.random().toString(36).slice(2,10)+Date.now().toString(36).slice(-4);sessionStorage.setItem('blkbp_sid',sid);}}catch(e){}
  function isWa(h){return /^(https?:\/\/(api\.whatsapp\.com|wa\.me|web\.whatsapp\.com)\/|whatsapp:)/i.test(h);}
  function waPhone(h){var m=h.match(/wa\.me\/(\+?\d{6,16})/i)||h.match(/[?&]phone=(\+?\d{6,16})/i);return m?m[1].replace(/\D/g,''):'';}
  document.addEventListener('click',function(e){
    var t=e.target;if(!t||!t.closest){return;}
    var a=t.closest('a[href]');if(!a){return;}
    if(!a.closest('.bkh')&&!a.closest('.bkh-bar')){return;}
    var h=a.getAttribute('href')||'',type='',number='';
    if(/^tel:/i.test(h)){type='call';number=h.replace(/^tel:/i,'').replace(/\D/g,'');}
    else if(isWa(h)){number=waPhone(h);if(!number){return;}type='whatsapp';}
    else if(/^mailto:/i.test(h)){type='email';number=h.replace(/^mailto:/i,'').split('?')[0];}
    else{return;}
    var where=a.getAttribute('data-where')||(a.closest('[data-where]')||{getAttribute:function(){return 'home';}}).getAttribute('data-where')||'home';
    var attr={};try{attr=JSON.parse(sessionStorage.getItem('blkbp_attr')||'{}');}catch(x){}
    var payload={type:type,number:number,where:where,listing:{id:0,title:'Homepage',action:'home',area:'',price:''},attr:attr,page:location.href,sid:sid,device:(window.matchMedia&&window.matchMedia('(pointer:coarse)').matches)?'mobile':'desktop'};
    var body=JSON.stringify(payload),sent=false;
    if(C.endpoint){try{if(navigator.sendBeacon){sent=navigator.sendBeacon(C.endpoint,new Blob([body],{type:'application/json'}));}}catch(x){}
    if(!sent){try{fetch(C.endpoint,{method:'POST',headers:{'Content-Type':'application/json'},body:body,keepalive:true,credentials:'same-origin'});}catch(x){}}}
    try{if(typeof window.gtag==='function'){window.gtag('event','contact_click',{method:type,listing_id:'0',listing_title:'Homepage',listing_action:'home',agent_number:number,placement:where});}}catch(x){}
    try{if(typeof window.fbq==='function'){window.fbq('track','Contact',{content_name:'Homepage',content_category:'home'});}}catch(x){}
  },true);
  /* sticky bar on phones, after the hero */
  var bar=document.querySelector('.bkh-bar'),hero=root.querySelector('.bkh-hero');
  if(bar&&hero){
    var on=false;
    function check(){var show=window.scrollY>(hero.offsetHeight-80);if(show!==on){on=show;bar.classList.toggle('is-on',on);document.body.classList.toggle('bkh-bar-on',on);}}
    window.addEventListener('scroll',check,{passive:true});check();
  }
})();
JS;
	}

	/* ------------------------------------------------------------------ SVG */

	function blockke_hm22_svg( $name ) {
		$svgs = array(
			'wa'     => '<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38a9.87 9.87 0 0 0 4.74 1.21c5.46 0 9.91-4.45 9.91-9.91S17.5 2 12.04 2zm0 18.15c-1.48 0-2.93-.4-4.2-1.15l-.3-.18-3.12.82.83-3.04-.2-.31a8.2 8.2 0 0 1-1.26-4.38c0-4.54 3.7-8.24 8.25-8.24 4.54 0 8.24 3.7 8.24 8.24 0 4.55-3.7 8.24-8.24 8.24zm4.52-6.16c-.25-.12-1.47-.72-1.69-.81-.23-.08-.39-.12-.56.12-.16.25-.64.81-.78.97-.14.17-.29.19-.54.06-.25-.12-1.05-.39-1.99-1.23-.74-.66-1.23-1.47-1.38-1.72-.14-.25-.02-.38.11-.51.11-.11.25-.29.37-.43.12-.14.16-.25.25-.41.08-.17.04-.31-.02-.43-.06-.12-.56-1.34-.76-1.84-.2-.48-.41-.42-.56-.43h-.48c-.17 0-.43.06-.66.31-.22.25-.86.85-.86 2.07 0 1.22.89 2.4 1.01 2.56.12.17 1.75 2.67 4.23 3.74.59.26 1.05.41 1.41.52.59.19 1.13.16 1.56.1.48-.07 1.47-.6 1.67-1.18.21-.58.21-1.07.14-1.18-.06-.1-.22-.16-.47-.28z"/></svg>',
			'phone'  => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.8 19.8 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.8 19.8 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.13.96.36 1.9.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.91.34 1.85.57 2.81.7A2 2 0 0 1 22 16.92z"/></svg>',
			'arrow'  => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>',
			'search' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>',
			'check'  => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m5 12 4.5 4.5L19 7"/></svg>',
			'play'   => '<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M8 5.5v13l11-6.5z"/></svg>',
		);
		return isset( $svgs[ $name ] ) ? $svgs[ $name ] : '';
	}

	/* ------------------------------------------------------------------ render */

	function blockke_hm22_render() {
		$cfg   = blockke_hm22_config();
		$u     = 'blockke_hm22_url';
		$stats = blockke_hm22_stats();
		$wa_n  = preg_replace( '/\D+/', '', $cfg['whatsapp'] );
		$tel   = 'tel:' . preg_replace( '/[^\d+]/', '', $cfg['phone'] );
		$h     = '';

		/* ---- hero ---- */
		/* v2.2.1: the family picture 37801 (config hero_image), no caption (config hero_caption ''), shown at config hero_focus.
		   v2.2: the hero photo (config hero_image: an attachment id, or a URL), one search box (areas, developments, estates) in
		   place of v2.1's filter card and name strip, and no "Popular:" links (the neighbourhoods have their own grid below) */
		$devq  = blockke_hm22_dev_query();
		$devm  = null !== $devq ? blockke_hm22_dev_mode() : 'buy';
		$devix = ! empty( $cfg['dev_search'] ) ? blockke_hm22_dev_index() : array( 'src' => '', 'd' => array() );
		$arix  = blockke_hm22_area_index();
		$cap   = isset( $cfg['hero_caption'] ) && is_string( $cfg['hero_caption'] ) ? $cfg['hero_caption'] : ( 40994 === (int) $cfg['hero_image'] && is_numeric( $cfg['hero_image'] ) ? 'Santorini Residences, Westlands · project render' : '' ); // v2.2.1: no "Nairobi" for a null caption: no place name over a photo that is not of one
		$himg  = blockke_hm22_hero_img( $cfg['hero_image'] );
		$h .= '<section class="bkh-hero" aria-label="Search Nairobi property">';
		$h .= $himg;
		$h .= '<div class="bkh-wrap">';
		$h .= '<span class="bkh-eyebrow">' . esc_html( $cfg['hero_eyebrow'] ) . '</span>';
		$h .= '<h1 class="bkh-display">' . esc_html( $cfg['hero_title'] ) . '</h1>';
		$h .= '<p class="bkh-hero-sub">' . esc_html( $cfg['hero_sub'] ) . '</p>';
		$h .= '<div class="bkh-search">';
		$h .= blockke_hm22_search_form( $devix['d'], $arix['a'], $devq, $devm );
		if ( null !== $devq ) {
			$h .= blockke_hm22_dev_results( $devix['d'], $arix['a'], $devq, $devm );
		}
		$h .= '</div>';
		$h .= '</div>';
		if ( '' !== trim( $cap ) && '' !== $himg ) { // a caption only over a photo that is there (v2.2.1: and never an empty one)
			$h .= '<span class="bkh-hero-cap">' . esc_html( $cap ) . '</span>';
		}
		$h .= '</section>';

		/* v2.2: everything after the hero sits on one paper panel with large rounded top corners, over the foot of the hero */
		$h .= '<div class="bkh-panel">';

		/* ---- proof strip ---- */
		$h .= '<section class="bkh-strip" aria-label="Block in numbers"><div class="bkh-wrap"><div class="bkh-stats">';
		foreach ( array( 'sale' => 'Homes for sale', 'projects' => 'Developments tracked', 'areas' => 'Nairobi neighbourhoods' ) as $sk => $sl ) {
			if ( ! empty( $stats[ $sk ] ) ) { // v2.1.1: an empty count is left out, never shown as 0
				$h .= '<div class="bkh-stat"><b>' . esc_html( blockke_hm22_plus( $stats[ $sk ] ) ) . '</b><span>' . $sl . '</span></div>';
			}
		}
		$h .= '<div class="bkh-stat"><b>Same day</b><span>Replies from a real person</span></div>';
		$h .= '</div></div></section>';

		/* ---- neighbourhood grid (v2.2) ---- */
		/* one text button per area with listings, its live count, linking to the area page: config area_grid of them, the areas of
		   "Know your block" (config neighbourhoods) first so the two sections never disagree about which areas count, then the busiest;
		   A to Z */
		$gmax = max( 0, (int) $cfg['area_grid'] );
		$grid = array();
		$bys  = array();
		foreach ( $arix['a'] as $e ) {
			$bys[ $e['s'] ] = $e;
		}
		foreach ( array_merge( (array) $cfg['neighbourhoods'], wp_list_pluck( $arix['a'], 's' ) ) as $gs ) {
			if ( count( $grid ) < $gmax && is_string( $gs ) && isset( $bys[ $gs ] ) && ! isset( $grid[ $gs ] ) ) {
				$grid[ $gs ] = $bys[ $gs ];
			}
		}
		$grid = array_values( $grid );
		usort(
			$grid,
			function ( $x, $y ) {
				return strcasecmp( $x['n'], $y['n'] );
			}
		);
		if ( $grid ) {
			$h .= '<section class="bkh-sec bkh-hoods" id="areas" aria-labelledby="bkh-hoods-h"><div class="bkh-wrap">';
			$h .= '<div class="bkh-head"><div><span class="bkh-eyebrow">Browse by area</span><h2 class="bkh-display" id="bkh-hoods-h">Start with a neighbourhood.</h2></div>';
			$h .= '<a class="bkh-more" href="' . esc_url( $u( 'neighbourhoods' ) ) . '">All neighbourhoods' . blockke_hm22_svg( 'arrow' ) . '</a></div>';
			$h .= '<ul class="bkh-hood-grid">';
			foreach ( $grid as $e ) {
				$h .= '<li><a class="bkh-hood" href="' . esc_url( $e['u'] ) . '"><span class="bkh-hood-n">' . esc_html( $e['n'] ) . '</span><span class="bkh-hood-c">' . esc_html( number_format( $e['all'] ) . ( 1 === $e['all'] ? ' listing' : ' listings' ) ) . '</span>' . blockke_hm22_svg( 'arrow' ) . '</a></li>';
			}
			$h .= '</ul></div></section>';
		}

		/* ---- developments ---- */
		/* v2.2: a carousel of tall cards (blockke_hm22_carousel()); on screens with a mouse the card pointed at or focused widens and
		   shows its price line and link, on touch screens every card shows them; the arrows are the script's, the row also scrolls */
		$devs   = blockke_hm22_carousel( $devix );
		$videos = is_array( $cfg['videos'] ) ? $cfg['videos'] : array();
		if ( $devs ) {
			$h .= '<section class="bkh-sec" id="developments" aria-labelledby="bkh-devs-h"><div class="bkh-wrap">';
			$h .= '<div class="bkh-head"><div><span class="bkh-eyebrow">New developments</span><h2 class="bkh-display" id="bkh-devs-h">Buy before it&rsquo;s built, with the homework done.</h2>';
			$h .= '<p class="bkh-lede">Every project here has been through our checks &mdash; developer record, title, approvals, payment terms &mdash; before it earned a place on the site.</p></div>';
			$h .= '<div class="bkh-dc-nav"><button type="button" class="bkh-dc-btn" data-dir="-1" aria-controls="bkh-dc-row" aria-label="Previous developments" hidden>' . blockke_hm22_svg( 'arrow' ) . '</button><button type="button" class="bkh-dc-btn" data-dir="1" aria-controls="bkh-dc-row" aria-label="Next developments" hidden>' . blockke_hm22_svg( 'arrow' ) . '</button>';
			$h .= '<a class="bkh-more" href="' . esc_url( $u( 'offplan' ) ) . '">All off-plan projects' . blockke_hm22_svg( 'arrow' ) . '</a></div></div>';
			$h .= '<ul class="bkh-dc-row" id="bkh-dc-row" aria-label="New developments">';
			foreach ( $devs as $d ) {
				$line = implode( ' · ', array_filter( array( $d['price'], $d['comp'] ) ) );
				$vid  = '' !== $d['slug'] && isset( $videos[ $d['slug'] ] ) && is_string( $videos[ $d['slug'] ] ) && preg_match( '#^(https?:)?//|^/#', $videos[ $d['slug'] ] ) ? $videos[ $d['slug'] ] : '';
				$h   .= '<li class="bkh-dc">' . blockke_hm22_img( $d['thumb'], 'large', 'bkh-dc-img', '', false, blockke_hm22_cover_sizes( $d['thumb'], array( array( '(max-width: 767px)', array( 340, 'px' ), array( 460, 'px' ) ), array( '(max-width: 1150px)', array( 500, 'px' ), array( 460, 'px' ) ), array( '', array( 520, 'px' ), array( 540, 'px' ) ) ) ) );
				$h   .= '<div class="bkh-dc-body">';
				$h   .= '' !== $d['area'] ? '<span class="bkh-dc-area">' . esc_html( $d['area'] ) . '</span>' : '';
				$h   .= '<h3 class="bkh-dc-name">' . esc_html( $d['name'] ) . '</h3>';
				$h   .= '<div class="bkh-dc-more">';
				$h   .= '' !== $line ? '<p class="bkh-dc-line">' . esc_html( $line ) . '</p>' : '';
				$h   .= '<a class="bkh-dc-cta" href="' . esc_url( $d['url'] ) . '" aria-label="' . esc_attr( $d['name'] . ( '' !== $d['area'] ? ', ' . $d['area'] : '' ) . ': prices and floor plans' ) . '">Prices &amp; floor plans' . blockke_hm22_svg( 'arrow' ) . '</a>';
				$h   .= '' !== $vid ? '<a class="bkh-dc-play" href="' . esc_url( $vid ) . '" target="_blank" rel="noopener" aria-label="' . esc_attr( 'Play the video of ' . $d['name'] ) . '">' . blockke_hm22_svg( 'play' ) . 'Play</a>' : '';
				$h   .= '</div></div></li>';
			}
			$h .= '</ul></div></section>';
		}

		/* ---- worth a viewing ---- */
		/* v2.2: on near-black, tall photo cards with the text over a dark fade and a status tab in the corner. Only listings whose
		   main photo is at least config listing_min_px make a card (blockke_hm22_listings()), and a grid is always whole rows */
		$sale = blockke_hm22_listings( 'for-sale', (int) $cfg['listings_count'] );
		$rent = blockke_hm22_listings( 'for-rent', (int) $cfg['listings_count'] );
		$card = function ( $l, $n, $wide ) {
			/* sizes: the photo covers a 4:5 card, so a landscape photo is drawn as wide as the card is tall times its shape */
			$rows = 1 === $n ? array( array( '(max-width: 767px)', array( 90, 'vw' ), array( 112.5, 'vw' ) ), array( '', array( 560, 'px' ), array( 700, 'px' ) ) )
				: array(
					array( '(max-width: 439px)', array( 82, 'vw' ), array( 102.5, 'vw' ) ),
					array( '(max-width: 767px)', array( 360, 'px' ), array( 450, 'px' ) ),
					$wide ? array( '(max-width: 900px)', array( 92, 'vw' ), array( 57.5, 'vw' ) ) : array( '(max-width: 900px)', array( 46, 'vw' ), array( 57.5, 'vw' ) ),
					2 === $n ? array( '(max-width: 1200px)', array( 45, 'vw' ), array( 56.25, 'vw' ) ) : array( '(max-width: 1200px)', array( 30, 'vw' ), array( 37.5, 'vw' ) ),
					2 === $n ? array( '', array( 542, 'px' ), array( 678, 'px' ) ) : array( '', array( 356, 'px' ), array( 445, 'px' ) ),
				);
			$o  = '<a class="bkh-card" href="' . esc_url( $l['url'] ) . '" title="' . esc_attr( $l['full'] ) . '">';
			$o .= blockke_hm22_img( $l['thumb'], 'large', 'bkh-card-img', $l['full'], false, blockke_hm22_cover_sizes( $l['thumb'], $rows ) );
			if ( '' !== $l['tab'] ) {
				$o .= '<span class="bkh-card-tab">' . esc_html( $l['tab'] ) . '</span>';
			}
			$o .= '<div class="bkh-card-body"><span class="bkh-card-price">' . esc_html( $l['price'] ) . '</span>';
			$o .= '<h3>' . esc_html( $l['title'] ) . '</h3>';
			if ( $l['loc'] ) {
				$o .= '<span class="bkh-card-loc">' . esc_html( $l['loc'] ) . '</span>';
			}
			$specs = array();
			if ( $l['beds'] > 0 ) {
				$specs[] = (int) $l['beds'] . ( 1 === (int) $l['beds'] ? ' bed' : ' beds' );
			} elseif ( ! $l['rent'] && preg_match( '/studio/i', $l['full'] ) ) {
				$specs[] = 'Studio';
			}
			if ( $l['baths'] > 0 ) {
				$specs[] = (int) $l['baths'] . ( 1 === (int) $l['baths'] ? ' bath' : ' baths' );
			}
			if ( $l['size'] >= 150 ) {
				$specs[] = number_format( $l['size'] ) . ' sq ft';
			}
			if ( $specs ) {
				$o .= '<span class="bkh-specs">' . esc_html( implode( ' · ', $specs ) ) . '</span>';
			}
			$o .= '</div></a>';
			return $o;
		};
		$gcls = function ( $list ) {
			return 'bkh-grid bkh-grid--' . min( 3, count( $list ) ) . ( 3 === count( $list ) ? ' bkh-grid--odd' : '' );
		};
		$lists = array_filter( array( 'sale' => $sale, 'rent' => $rent ) ); // a tab only for a list with cards, so no empty grid shows
		if ( $lists ) {
			$first = key( $lists );
			$h    .= '<section class="bkh-sec bkh-sec--dark" id="listings"><div class="bkh-wrap">';
			/* "Hand-picked ... chosen by the team" only when every card shown is one the team featured (prop_featured); the rest of
			   a list is the newest listings, and the words then say so */
			$nfeat = 0;
			$ncard = 0;
			foreach ( $lists as $list ) {
				foreach ( $list as $l ) {
					$ncard++;
					$nfeat += empty( $l['feat'] ) ? 0 : 1;
				}
			}
			$h    .= '<div class="bkh-head"><div><span class="bkh-eyebrow">' . ( $nfeat === $ncard ? 'Hand-picked' : 'On the market' ) . '</span><h2 class="bkh-display">Worth a viewing this week.</h2>';
			$h    .= '<p class="bkh-lede">' . ( $nfeat === $ncard ? 'The current shortlist, chosen by the team and refreshed as the market moves.' : ( $nfeat ? 'Listings the team has featured first, then the newest on the site, refreshed as the market moves.' : 'The newest listings on the site, refreshed as the market moves.' ) ) . '</p></div></div>';
			if ( 2 === count( $lists ) ) {
				$h .= '<div class="bkh-ltabs" role="tablist"><button type="button" class="bkh-ltab" role="tab" aria-selected="true" aria-controls="bkh-sale">For sale</button><button type="button" class="bkh-ltab" role="tab" aria-selected="false" aria-controls="bkh-rent">For rent</button></div>';
			}
			foreach ( $lists as $k => $list ) {
				$h .= '<div class="' . esc_attr( $gcls( $list ) ) . '" id="bkh-' . $k . '"' . ( $k === $first ? '' : ' hidden' ) . '>';
				foreach ( $list as $li => $l ) {
					$h .= $card( $l, min( 3, count( $list ) ), 3 === count( $list ) && 2 === $li );
				}
				$h .= '</div>';
			}
			$h .= '<div class="bkh-lfoot">';
			$h .= isset( $lists['sale'] ) ? '<a class="bkh-btn bkh-btn--white" data-for="bkh-sale" href="' . esc_url( $u( 'for_sale' ) ) . '">Browse all ' . ( empty( $stats['sale'] ) ? '' : esc_html( blockke_hm22_plus( $stats['sale'] ) ) . ' ' ) . 'homes for sale</a>' : '';
			$h .= isset( $lists['rent'] ) ? '<a class="bkh-btn bkh-btn--white" data-for="bkh-rent" href="' . esc_url( $u( 'for_rent' ) ) . '"' . ( 'rent' === $first ? '' : ' hidden' ) . '>Browse all rentals</a>' : '';
			$h .= '</div></div></section>';
		}

		/* ---- diaspora ---- */
		/* v2.2: with enough stored website enquiries (blockke_hm22_dia(), blockke_hm22_dia_ok()), two numbers worked out from them,
		   the countries they came from and a dotted world map; below the minimums, v2.1's band as it was. The share from abroad shows
		   as a percentage only when it is at least half (under the heading "Most of our buyers aren't in Nairobi" a smaller share
		   would read as the page contradicting itself), otherwise as the number of enquiries from abroad. Step 3 no longer says who
		   pays the fee (no commission terms on the page) */
		$dia   = blockke_hm22_dia();
		$nums  = blockke_hm22_dia_ok( $dia );
		$steps = '<li class="bkh-step"><i>1</i><div><h3>Tell us your brief</h3><p>Budget, area, timeline and whether it&rsquo;s to live in or let out &mdash; on WhatsApp, in your time zone.</p></div></li>';
		$steps .= '<li class="bkh-step"><i>2</i><div><h3>We shortlist and <span class="bkh-nw">video-view</span></h3><p>Every unit walked, filmed and priced against real comparables, with the developer&rsquo;s record and approvals checked.</p></div></li>';
		$steps .= '<li class="bkh-step"><i>3</i><div><h3>You buy with the checks done</h3><p>Title search, payment plan and contract reviewed; we negotiate on your side.</p></div></li>';
		$ctas  = '<div class="bkh-ctas"><a class="bkh-btn bkh-btn--white" href="' . esc_url( blockke_hm22_wa( 'buying from abroad.' ) ) . '" target="_blank" rel="noopener">' . blockke_hm22_svg( 'wa' ) . 'WhatsApp Loyd</a><a class="bkh-btn bkh-btn--ghostw" href="' . esc_url( $u( 'diaspora_guide' ) ) . '">Read the diaspora guide</a></div>';
		$h .= '<section class="bkh-sec bkh-sec--navy bkh-dia' . ( $nums ? ' bkh-dia--nums' : '' ) . '" id="diaspora" data-where="home_diaspora"><div class="bkh-wrap"><div class="bkh-dia-grid">';
		$h .= '<div><span class="bkh-eyebrow">Buying from abroad</span><h2 class="bkh-display">Most of our buyers aren&rsquo;t in Nairobi. That&rsquo;s the point.</h2>';
		$h .= '<p class="bkh-lede">From London, Dallas, Dubai or Toronto you can&rsquo;t walk the street or sit across from the developer. We do &mdash; and you see everything on video before you commit a shilling.</p>';
		if ( $nums ) {
			$info   = blockke_hm22_cc_info();
			$period = $dia['full'] ? ( 365 === (int) $dia['days'] ? 'in the last 12 months' : 'in the last ' . (int) $dia['days'] . ' days' ) : 'since ' . wp_date( 'j F Y', (int) $dia['from'] );
			$names  = array();
			foreach ( array_keys( $dia['by'] ) as $cc ) {
				$names[] = isset( $info[ $cc ] ) ? $info[ $cc ][2] : $cc;
			}
			$nc    = count( $dia['by'] );
			$share = (int) floor( 100 * $dia['abroad'] / max( 1, $dia['total'] ) );
			$plc   = blockke_hm22_dia_places( $dia['by'] );
			$h    .= $ctas . '</div><div class="bkh-dia-side">';
			$h    .= '<div class="bkh-dia-nums">';
			$h    .= '<div class="bkh-dia-num"><b>' . (int) $nc . '</b><span>' . esc_html( $plc ) . ' besides Kenya that website enquiries came from</span></div>';
			$h    .= $share >= 50 ? '<div class="bkh-dia-num"><b>' . $share . '%</b><span>of website enquiries came from outside Kenya</span></div>' : '<div class="bkh-dia-num"><b>' . esc_html( number_format( (int) $dia['abroad'] ) ) . '</b><span>website enquiries came from outside Kenya</span></div>';
			$h    .= '</div>';
			$h    .= '<p class="bkh-dia-note">Counted from the enquiries block.ke&rsquo;s own forms keep on record (development, area, new developments and consultation pages, and the price check) ' . esc_html( $period ) . ': ' . esc_html( number_format( (int) $dia['total'] ) ) . ', placed by the country code of the phone number given. Repeats, our own tests and numbers that could not be placed are left out.</p>';
			$h    .= '<div class="bkh-dia-proof bkh-dia-proof--top bkh-dia-proof--n' . min( 5, count( $names ) ) . '"><b class="bkh-dia-proof-h">Most enquiries from abroad</b>';
			foreach ( array_slice( $names, 0, 5 ) as $nm ) {
				$h .= '<span>' . esc_html( $nm ) . '</span>';
			}
			$h    .= '</div></div></div>';
			$label = 'World map of where website enquiries to block.ke came from ' . $period . ': Kenya and ' . $nc . ' other ' . $plc . ', most from ' . implode( ', ', array_slice( $names, 0, 3 ) ) . '.';
			$h    .= '<figure class="bkh-dia-map">' . blockke_hm22_map_svg( $dia['by'], $label ) . '<figcaption><span class="bkh-map-k bkh-map-k--home"></span>Nairobi <span class="bkh-map-k"></span>Where website enquiries came from</figcaption></figure>';
			$h    .= '<ol class="bkh-steps bkh-steps--row">' . $steps . '</ol>';
		} else {
			$h .= $ctas;
			$h .= '<div class="bkh-dia-proof"><span>UK</span><span>USA</span><span>UAE</span><span>Canada</span><span>Australia</span>Most of the homes we close are bought from abroad.</div>';
			$h .= '</div>';
			$h .= '<ol class="bkh-steps">' . $steps . '</ol>';
			$h .= '</div>';
		}
		$h .= '</div></section>';

		/* ---- know your block ---- */
		$areas = blockke_hm22_areas();
		if ( $areas ) {
			$h .= '<section class="bkh-sec bkh-sec--mist" id="neighbourhoods"><div class="bkh-wrap">';
			$h .= '<div class="bkh-head"><div><span class="bkh-eyebrow">Neighbourhoods</span><h2 class="bkh-display">Know your block before you buy it.</h2>';
			$h .= '<p class="bkh-lede">Prices, commutes, schools, noise &mdash; everything the listings won&rsquo;t tell you, neighbourhood by neighbourhood.</p></div>';
			$h .= '<a class="bkh-more" href="' . esc_url( $u( 'neighbourhoods' ) ) . '">All neighbourhoods' . blockke_hm22_svg( 'arrow' ) . '</a></div>';
			$h .= '<div class="bkh-areas">';
			foreach ( $areas as $a ) {
				$h .= '<a class="bkh-area" href="' . esc_url( $a['url'] ) . '">' . blockke_hm22_img( $a['thumb'], 'large', '', $a['name'], false, '(max-width: 767px) 50vw, 20vw' );
				$h .= '<div><h3>' . esc_html( $a['name'] ) . '</h3><span>' . esc_html( number_format( $a['count'] ) ) . ' ' . ( 1 === $a['count'] ? 'listing' : 'listings' ) . '</span></div></a>';
			}
			$h .= '</div></div></section>';
		}

		/* ---- founder ---- */
		$founder = blockke_hm22_agent( $cfg['founder_agent'] );
		$team    = array();
		foreach ( (array) $cfg['team_agents'] as $aid ) {
			$ag = blockke_hm22_agent( $aid );
			if ( $ag ) {
				$team[] = $ag;
			}
		}
		$h .= '<section class="bkh-sec bkh-sec--white" id="loyd" data-where="home_founder"><div class="bkh-wrap"><div class="bkh-founder">';
		$h .= '<figure class="bkh-portrait">' . blockke_hm22_img( $cfg['founder_image'], 'large', '', 'Loyd Mokaya, founder of Block Real Estate', false, '(max-width: 900px) 90vw, 40vw' ) . '<figcaption>Loyd Mokaya · Founder</figcaption></figure>';
		$h .= '<div><span class="bkh-eyebrow">Loyd from Block</span><h2 class="bkh-display">You&rsquo;ll deal with a person, not a portal.</h2>';
		$h .= '<p class="bkh-quote">&ldquo;I started Block because buying in Nairobi shouldn&rsquo;t feel like a gamble.&rdquo;</p>';
		$h .= '<p class="bkh-body">So we check everything first &mdash; the market, the title, the street &mdash; and tell you straight, even when the honest answer is &ldquo;not this one&rdquo;. Nairobi moves fast and unevenly; a price in Kilimani tells you nothing about Lavington. That&rsquo;s why we work it block by block, and why when you message us you get a person who has stood in the building.</p>';
		$h .= '<div class="bkh-sign">Loyd Mokaya<small>Founder, Block Real Estate</small></div>';
		$h .= '<div class="bkh-ctas"><a class="bkh-btn bkh-btn--navy" href="' . esc_url( blockke_hm22_wa( 'a property.' ) ) . '" target="_blank" rel="noopener">' . blockke_hm22_svg( 'wa' ) . 'WhatsApp Loyd</a><a class="bkh-btn bkh-btn--ghost" href="' . esc_url( $u( 'consult' ) ) . '">Book a free consultation</a></div>';
		if ( $team ) {
			$h .= '<div class="bkh-team"><div class="bkh-avatars">';
			foreach ( $team as $ag ) {
				$h .= blockke_hm22_img( $ag['thumb'], 'thumbnail', '', $ag['name'], false, '44px' );
			}
			$h .= '</div><p>' . esc_html( implode( ', ', array_map( function ( $ag ) { return preg_replace( '/\s.*$/', '', $ag['name'] ); }, $team ) ) ) . ' and the rest of the team &mdash; <a href="' . esc_url( $u( 'team' ) ) . '">meet them</a></p></div>';
		}
		$h .= '</div></div></div></section>';

		/* ---- services ---- */
		/* v2.2.1: "Our promise" (config promise_title), a two-sentence statement in the site's own claims, "Meet the team" and a brass
		   bar, over the owner's Nairobi skyline (config services_image 45212), then the four service cards (config services)
		   and the valuation band, as in v2.2. v2.2: one full-width photo (config services_image: an attachment id or a URL; none = navy).
		   The hero's photo is never shown twice: when services_image names the same file as hero_image (by id or by URL, any size copy;
		   the two defaults now differ), the band is navy.
		   sizes: the photo covers the band, which is taller than wide on phones (there it covers its top 560px only) */
		$sv_same = blockke_hm22_same_img( $cfg['services_image'], $cfg['hero_image'] ); // v2.2.1: by the file, an id against a URL too
		$sv_img  = $sv_same ? '' : blockke_hm22_band_img(
			$cfg['services_image'],
			'bkh-sv-img',
			function ( $ar ) { // the band is 560px tall on phones, about 970px from 768 to 1100px wide, about 800px above
				$c = (int) ceil( 800 * $ar );
				return '(max-width: 767px) ' . (int) ceil( 560 * $ar ) . 'px, (max-width: 1100px) ' . max( 1100, (int) ceil( 970 * $ar ) ) . 'px, ' . ( $c > 1100 ? '(max-width: ' . $c . 'px) ' . $c . 'px, ' : '' ) . '100vw';
			}
		);
		$sv_cap = isset( $cfg['services_caption'] ) && is_string( $cfg['services_caption'] ) ? $cfg['services_caption'] : ( is_numeric( $cfg['services_image'] ) && 40994 === (int) $cfg['services_image'] ? 'Project render' : '' );
		$pm_t = is_string( $cfg['promise_title'] ) && '' !== trim( $cfg['promise_title'] ) ? $cfg['promise_title'] : 'An excellent-service powerhouse.';
		$h .= '<section class="bkh-sec bkh-sv' . ( '' !== $sv_img ? ' bkh-sv--img' : '' ) . '" id="services" aria-labelledby="bkh-pm-t">' . $sv_img . '<div class="bkh-wrap">';
		$h .= '<div class="bkh-pm"><div class="bkh-pm-h"><span class="bkh-eyebrow">Our promise</span><h2 class="bkh-display" id="bkh-pm-t">' . preg_replace( '/[^\s-]+(?:-[^\s-]+)+/u', '<span class="bkh-nw">$0</span>', esc_html( $pm_t ) ) . '</h2></div>'; // a hyphenated word never breaks at its hyphen
		$h .= '<div class="bkh-pm-s"><p>Block Advisory, Block Architecture and Design, Block Construction and Property Management are one accountable team, held to one standard. Every home is checked before you view it and negotiated on your side, and a real person replies the same day.</p>';
		$h .= '<a class="bkh-btn bkh-btn--ghostw" href="' . esc_url( $u( 'team' ) ) . '">Meet the team</a></div></div>';
		$h .= '<span class="bkh-pm-bar" aria-hidden="true"></span>';
		$h .= '<div class="bkh-services">';
		foreach ( (array) $cfg['services'] as $s ) {
			if ( ! is_array( $s ) || count( $s ) < 3 ) {
				continue;
			}
			$s    = array_values( $s );
			$href = is_string( $s[2] ) && isset( $cfg['urls'][ $s[2] ] ) ? $u( $s[2] ) : ( is_string( $s[2] ) && preg_match( '#^(https?:)?//|^/#', $s[2] ) ? ( 0 === strpos( $s[2], '/' ) && 0 !== strpos( $s[2], '//' ) ? home_url( $s[2] ) : $s[2] ) : '' );
			if ( '' === $href ) {
				continue;
			}
			$h .= '<a class="bkh-service" href="' . esc_url( $href ) . '"><h3>' . wp_kses( (string) $s[0], array() ) . blockke_hm22_svg( 'arrow' ) . '</h3><p>' . wp_kses( (string) $s[1], array() ) . '</p></a>';
		}
		$h .= '</div>';
		$h .= '<div class="bkh-val"><div><h3>Own a property in Nairobi?</h3><p>Tell us about it and we&rsquo;ll tell you what it&rsquo;s worth &mdash; before we list a thing.</p></div><a class="bkh-btn bkh-btn--white" href="' . esc_url( $u( 'valuation' ) ) . '">Get a free valuation</a></div>';
		$h .= '</div>' . ( '' !== $sv_img && '' !== $sv_cap ? '<span class="bkh-sv-cap">' . esc_html( $sv_cap ) . '</span>' : '' ) . '</section>';

		/* ---- homework ---- */
		$arts = blockke_hm22_articles( 4 );
		if ( $arts ) {
			$lead = array_shift( $arts );
			$h   .= '<section class="bkh-sec bkh-sec--white" id="homework"><div class="bkh-wrap">';
			$h   .= '<div class="bkh-head"><div><span class="bkh-eyebrow">The homework</span><h2 class="bkh-display">What we&rsquo;d tell you over coffee.</h2><p class="bkh-lede">Market data, legal landmines and neighbourhood deep-dives, written by the people doing the deals.</p></div>';
			$h   .= '<a class="bkh-more" href="' . esc_url( $u( 'blog' ) ) . '">All articles' . blockke_hm22_svg( 'arrow' ) . '</a></div>';
			$h   .= '<div class="bkh-hw">';
			$h   .= '<a class="bkh-lead" href="' . esc_url( $lead['url'] ) . '"><div class="bkh-lead-media">' . blockke_hm22_img( $lead['thumb'], 'large', '', $lead['title'], false, '(max-width: 900px) 100vw, 55vw' ) . '</div>';
			$h   .= '<span class="bkh-cat">' . esc_html( $lead['cat'] ) . '</span><h3>' . esc_html( $lead['title'] ) . '</h3><p>' . esc_html( $lead['ex'] ) . '</p><time>' . esc_html( $lead['date'] ) . '</time></a>';
			$h   .= '<div class="bkh-list">';
			foreach ( $arts as $a ) {
				$h .= '<a class="bkh-item" href="' . esc_url( $a['url'] ) . '"><div class="bkh-item-media">' . blockke_hm22_img( $a['thumb'], 'medium', '', $a['title'], false, '112px' ) . '</div><div><span class="bkh-cat">' . esc_html( $a['cat'] ) . '</span><h3>' . esc_html( $a['title'] ) . '</h3><time>' . esc_html( $a['date'] ) . '</time></div></a>';
			}
			$h .= '</div></div></div></section>';
		}

		/* ---- closing ---- */
		$h .= '<section class="bkh-sec bkh-sec--navy bkh-close" id="contact" data-where="home_close"><div class="bkh-wrap">';
		$h .= '<span class="bkh-eyebrow">Start here</span><h2 class="bkh-display">Your next move starts with one message.</h2>';
		$h .= '<p class="bkh-lede">A real person replies the same day. No bots, no runaround.</p>';
		$h .= '<div class="bkh-ctas"><a class="bkh-btn bkh-btn--white" href="' . esc_url( blockke_hm22_wa( 'my next move.' ) ) . '" target="_blank" rel="noopener">' . blockke_hm22_svg( 'wa' ) . 'WhatsApp us</a><a class="bkh-btn bkh-btn--ghostw" href="' . esc_attr( $tel ) . '">' . blockke_hm22_svg( 'phone' ) . 'Call ' . esc_html( $cfg['phone_label'] ) . '</a></div>';
		$h .= '<p class="bkh-alt">Prefer a sit-down? <a href="' . esc_url( $u( 'consult' ) ) . '">Book a free consultation</a> &middot; Mon&ndash;Sat 07:00&ndash;21:00</p>';
		$h .= '</div></section>';
		$h .= '</div>'; // v2.2: the end of the panel

		/* ---- sticky bar (phones) ---- */
		$bar = '<div class="bkh-bar" data-where="home_sticky" aria-label="Contact Block"><a class="bkh-bar-wa" href="' . esc_url( blockke_hm22_wa( 'a property.' ) ) . '" target="_blank" rel="noopener">' . blockke_hm22_svg( 'wa' ) . 'WhatsApp</a><a class="bkh-bar-call" href="' . esc_attr( $tel ) . '">' . blockke_hm22_svg( 'phone' ) . 'Call us</a></div>';

		$js_cfg = array( 'endpoint' => esc_url_raw( rest_url( 'block/v1/contact-click' ) ) );
		if ( $devix['d'] ) {
			$js_cfg['dv'] = blockke_hm22_dev_data( $devix['d'] ); // v2.1: the development-name typeahead
		}
		if ( $arix['a'] ) {
			$js_cfg['ar'] = blockke_hm22_area_data( $arix['a'], $devix['d'] ); // v2.2: the areas for the same typeahead
		}
		$out    = '<!-- BLOCK homepage v' . blockke_hm22_ver() . ' -->';
		$out   .= '<link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&amp;display=swap">';
		$out   .= '<style id="bkh-css">' . blockke_hm22_css() . '</style>';
		$out   .= '<div class="bkh" id="bkh" data-where="home_hero">' . $h . '</div>' . $bar;
		$out   .= '<script id="bkh-cfg">window.BLOCKHOME=' . wp_json_encode( $js_cfg, JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS ) . ';</script>';
		$out   .= '<script id="bkh-js">' . blockke_hm22_js() . '</script>';
		return $out;
	}

	/**
	 * Content stamp: the newest change to any listing, article or agent. Part of every cache key, so the
	 * page rebuilds itself after an edit without hooks (this code only runs where the shortcode renders).
	 */
	function blockke_hm22_stamp() {
		static $stamp = null;
		if ( null !== $stamp ) {
			return $stamp;
		}
		global $wpdb;
		$stamp = (string) $wpdb->get_var( "SELECT MAX(post_modified_gmt) FROM {$wpdb->posts} WHERE post_type IN ('estate_property','post','estate_agent') AND post_status = 'publish'" );
		$stamp = substr( md5( blockke_hm22_ver() . '|' . $stamp . '|' . wp_json_encode( get_option( 'blockke_homepage', array() ) ) ), 0, 12 );
		return $stamp;
	}

	function blockke_hm22_output() {
		$cfg   = blockke_hm22_config();
		$key   = 'blockke_home_v22_' . blockke_hm22_stamp() . ( blockke_hm22_dev_page() ? '_p' . blockke_hm22_dev_page() : '' ); // v2.1: a preview page's copy (its name search posts to itself) is not the front page's
		$skip  = isset( $_GET['bkhome_nocache'] ) || isset( $_GET['elementor-preview'] ) || is_preview() || isset( $_GET['bkdev'] ); // v2.1: a name search is rendered fresh and never stored
		$cache = $skip ? false : get_transient( $key );
		if ( is_string( $cache ) && '' !== $cache ) {
			return $cache;
		}
		try {
			$html = blockke_hm22_render();
		} catch ( Throwable $e ) {
			error_log( 'BLOCK homepage v2: ' . $e->getMessage() );
			return '<div style="padding:120px 20px 60px;text-align:center;font-family:Montserrat,sans-serif"><h1 style="font-size:32px">Block Real Estate</h1><p>Nairobi property, block by block.</p><p><a href="' . esc_url( home_url( '/for-sale/' ) ) . '">Homes for sale</a> · <a href="' . esc_url( home_url( '/for-rent/' ) ) . '">Rentals</a> · <a href="' . esc_url( home_url( '/contact-us/' ) ) . '">Contact</a></p></div>';
		}
		if ( ! $skip && ! blockke_hm22_nostore() && blockke_hm22_tax_ready() ) { // v2.1.1
			set_transient( $key, $html, max( 1, (int) $cfg['cache_minutes'] ) * MINUTE_IN_SECONDS );
		}
		return $html;
	}
}

echo blockke_hm22_output();
