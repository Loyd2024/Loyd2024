<?php
/* marker line - keeps the connector from Markdown-converting this snippet; harmless in PHP */
/* <div> markup marker - the connector stores content as-is only when it sees a block-level HTML tag; harmless in PHP */
/**
 * BLOCK - Homepage v3.1 (2026-10-29) = v3.0.2 + search by development name
 * v3.1 adds, under the Buy | Rent | Off-plan filters in the hero, "Know the development?": its own small GET form
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
 * feature + developments: "gol" puts Golden Hill first), then to the shorter name. Opened upwards, the list never
 * covers the Buy | Rent | Off-plan tabs or leaves a sliver of the filters above it; where it fits neither way, typing
 * scrolls the page just enough to open it below. A click into the box never moves the page while the button is down
 * (the list opens where it fits and is fitted once the click is over), and the mouse highlights an option only when
 * it really moves. The text shown back, sent to GA4 and carried in the address is cleaned of web and e-mail
 * addresses, @handles and numbers of 5 or more digits. Pages with ?bkdev= are never read from or stored in the page
 * cache (on a page that is not the front page yet, such as /home-v3/, the search posts to that page and its cached
 * copy has its own key). While it has a copy under 12 hours old, the names index waits for 42706 to rebuild its own
 * developments after a listing change (its development pages do), so the homepage does not pay for that rebuild.
 * GA4 'search' fires with placement home_dev. Config 'dev_search' => false hides it. Everything else is v3.0.2
 * unchanged: where the hero is at its minimum height (tall screens) it grows by the strip's height, so nothing above
 * the strip moves, and the Find button takes the width of v3.0.2's Search button, which keeps its own.
 * The block.ke homepage, rendered by this one snippet where the shortcode [wpcode id=<this snippet>] is placed
 * (one Elementor Shortcode widget in a full-width container, the same wiring as v2 on /home-v2/ and the blog hub).
 * v3 = the "bolder" editorial direction Loyd asked for after v2: cinematic dark hero that runs into a proof band,
 * Bodoni Moda display type with an italic second clause, square corners, hairline rules, one mandate spotlight
 * (config 'feature') before three vetted projects, five neighbourhood tiles in an asymmetric grid, a brass-framed
 * founder portrait, an index-style services list. Fewer, bigger moments; everything else quiet.
 * Everything on the page comes from the site itself, so it stays current without edits:
 *   1) Hero: full-bleed photo (config 'hero_image'), statement, Buy | Rent | Off-plan search posting to
 *      /advanced-search/ with the parameters the results page already understands.
 *   2) Proof band: live counts (homes for sale, developments tracked, Nairobi neighbourhoods) + same-day reply.
 *   3) Mandate spotlight: the project in config 'feature' (property_project slug) built from its master listing;
 *      then three more projects (config 'developments'). Links go to the project's ad landing page when one exists.
 *   4) Worth a viewing: featured listings (prop_featured) topped up with the newest, For sale | For rent.
 *   5) Diaspora band, 6) Know your block (five property_area tiles, live counts, term/listing images),
 *   7) Loyd from Block (founder + team from estate_agent), 8) Services, 9) The homework (latest articles),
 *   10) Closing CTA, plus a WhatsApp | Call bar on phones.
 * Conversion: every WhatsApp / call tap is logged to POST /wp-json/block/v1/contact-click (placement home_*),
 * so it appears in the weekly contact report, and fires GA4 contact_click + Meta Contact. Searches fire GA4 'search'.
 * Cache: rendered HTML kept in a transient (blockke_home_v3_<stamp>) for up to an hour; the stamp changes whenever
 * a listing, article or agent is saved or the config option changes. ?bkhome_nocache=1 bypasses it.
 * Config: option blockke_homepage_v3 (array) overrides any key of the defaults in blockke_hm3_config().
 * Kill switch / rollback: deactivate this snippet in WPCode, or set the front page back to the previous layout.
 */

if ( ! function_exists( 'blockke_hm3_config' ) ) {

	function blockke_hm3_ver() {
		return '3.1.0';
	}

	function blockke_hm3_config() {
		static $cfg = null;
		if ( null !== $cfg ) {
			return $cfg;
		}
		$defaults = array(
			'hero_image'     => 40994,  // attachment id; a Nairobi photo or a project render (captioned)
			'hero_caption'   => 'Santorini Residences, Westlands · project render',
			'hero_eyebrow'   => 'Nairobi · Residential & Commercial',
			'hero_title'     => 'We know Nairobi |block by block.|',   // |…| sets the italic clause
			'hero_sub'       => 'Homes for sale, rentals and new developments — every one checked before you view, and negotiated on your side.',
			'quick_areas'    => array( 'westlands', 'kilimani', 'kileleshwa', 'lavington', 'tatu-city' ),
			'feature'        => 'golden-hill',   // property_project slug for the mandate spotlight ('' hides it)
			'feature_eyebrow' => 'New developments · Authorised sales agent',
			'feature_text'   => 'Block is the developer’s appointed sales agent on Golden Hill, so prices, payment plans and viewings come straight from us — with our own checks on title and approvals done first.',
			'developments'   => array( 'capital-heights', 'santorini-residences', 'astra-bay' ),
			'mandates'       => array( 'golden-hill' ), // project slugs Block sells for the developer (badge)
			'dev_link'       => 'lp',                  // 'lp' = ad landing page when one exists, 'listing' = always the listing
			'neighbourhoods' => array( 'westlands', 'kilimani', 'kileleshwa', 'lavington', 'riverside' ), // first = lead tile
			'listings_count' => 6,
			'founder_image'  => 36538,
			'founder_agent'  => 27447,
			'team_agents'    => array( 35365, 35366, 35367 ),
			'cities'         => 'London · Dallas · Dubai · Toronto · Sydney',
			'whatsapp'       => '254725937686',
			'phone'          => '+254184027349',
			'phone_label'    => '0184 027 349',
			'wa_text'        => "Hi Block — I'm on block.ke and I'd like to talk about ",
			'cache_minutes'  => 60,   // the key also changes whenever a listing, article or agent is saved
			'dev_search'     => true, // v3.1: "Know the development?" search by development name (false hides it)
			'urls'           => array(
				'search'         => '/advanced-search/',
				'for_sale'       => '/for-sale/',
				'for_rent'       => '/for-rent/',
				'offplan'        => '/new-developments/offplan-projects-for-sale-in-nairobi/',
				'neighbourhoods' => '/neighbourhoods/',
				'diaspora'       => '/diaspora/',
				'diaspora_guide' => '/market-insights/how-to-buy-property-in-kenya-from-the-diaspora-2026-the-complete-step-by-step-guide/37607/',
				'team'           => '/our-team/',
				'consult'        => '/get-a-consultation/',
				'sell'           => '/sell/',
				'valuation'      => '/home-valuation/',
				'management'     => '/property-management/',
				'advisory'       => '/investment-advisory/',
				'build'          => '/block-advisory/',
				'blog'           => '/blog/',
				'contact'        => '/contact-us/',
			),
		);
		$saved = get_option( 'blockke_homepage_v3', array() );
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

	function blockke_hm3_url( $key ) {
		$cfg = blockke_hm3_config();
		$u   = isset( $cfg['urls'][ $key ] ) ? $cfg['urls'][ $key ] : '/';
		if ( 0 === strpos( $u, 'http' ) ) {
			return $u;
		}
		return home_url( $u );
	}

	function blockke_hm3_wa( $topic ) {
		$cfg = blockke_hm3_config();
		return 'https://wa.me/' . preg_replace( '/\D+/', '', $cfg['whatsapp'] ) . '?text=' . rawurlencode( $cfg['wa_text'] . $topic );
	}

	/** Escaped heading text; |clause| becomes the italic clause. */
	function blockke_hm3_h( $text ) {
		return preg_replace( '/\|([^|]+)\|/u', '<em>$1</em>', esc_html( $text ) );
	}

	/** KES price in M notation for sales, K a month for rentals. */
	function blockke_hm3_price( $n, $rent = false ) {
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
	function blockke_hm3_plus( $n ) {
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
	function blockke_hm3_short_title( $title, $max = 46 ) {
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

	function blockke_hm3_first_term( $id, $tax ) {
		$terms = get_the_terms( $id, $tax );
		if ( is_array( $terms ) && ! empty( $terms ) ) {
			return $terms[0];
		}
		return null;
	}

	function blockke_hm3_img( $attachment_id, $size, $class, $alt, $eager = false, $sizes = '' ) {
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
	function blockke_hm3_term_image( $term_id ) {
		$opt = get_option( 'taxonomy_' . (int) $term_id );
		if ( is_array( $opt ) && ! empty( $opt['category_attach_id'] ) ) {
			return (int) $opt['category_attach_id'];
		}
		$m = get_term_meta( (int) $term_id, 'category_attach_id', true );
		return $m ? (int) $m : 0;
	}

	/** Area page: /neighbourhoods/<slug>/ when that page exists, else the area archive. */
	function blockke_hm3_area_url( $term ) {
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

	function blockke_hm3_stats() {
		$key = 'blockke_home_v3_stats_' . blockke_hm3_stamp();
		$s   = get_transient( $key );
		if ( is_array( $s ) ) {
			return $s;
		}
		$sale     = get_term_by( 'slug', 'for-sale', 'property_action_category' );
		$rent     = get_term_by( 'slug', 'for-rent', 'property_action_category' );
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
			$areas = (int) wp_count_terms( array( 'taxonomy' => 'property_area', 'hide_empty' => true ) );
		}
		$s = array(
			'sale'     => $sale ? (int) $sale->count : 0,
			'rent'     => $rent ? (int) $rent->count : 0,
			'projects' => $projects,
			'areas'    => $areas,
		);
		set_transient( $key, $s, 12 * HOUR_IN_SECONDS );
		return $s;
	}

	/** One listing's card data. */
	function blockke_hm3_listing( $id ) {
		$id     = (int) $id;
		$rent   = has_term( 'for-rent', 'property_action_category', $id );
		$price  = (float) get_post_meta( $id, 'property_price', true );
		$label  = trim( (string) get_post_meta( $id, 'property_label_before', true ) );
		$subs   = get_post_meta( $id, 'property_has_subunits', true );
		$from   = ( ! $rent ) && ( $subs || preg_match( '/from/i', $label ) );
		$area   = blockke_hm3_first_term( $id, 'property_area' );
		$city   = blockke_hm3_first_term( $id, 'property_city' );
		$comp   = blockke_hm3_first_term( $id, 'property_completion' );
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
		$project = blockke_hm3_first_term( $id, 'property_project' );
		$title   = ( $project && mb_strlen( $project->name ) <= 40 && ! preg_match( '/bedroom|apartments? on|for sale/i', $project->name ) )
			? html_entity_decode( $project->name, ENT_QUOTES, 'UTF-8' )
			: blockke_hm3_short_title( get_the_title( $id ) );
		return array(
			'id'     => $id,
			'url'    => get_permalink( $id ),
			'title'  => $title,
			'full'   => html_entity_decode( get_the_title( $id ), ENT_QUOTES, 'UTF-8' ),
			'thumb'  => (int) get_post_thumbnail_id( $id ),
			'price'  => $price ? ( ( $from ? 'From ' : '' ) . blockke_hm3_price( $price, $rent ) ) : 'Price on request',
			'beds'   => (int) get_post_meta( $id, 'property_bedrooms', true ),
			'baths'  => (int) get_post_meta( $id, 'property_bathrooms', true ),
			'size'   => (float) get_post_meta( $id, 'property_size', true ),
			'loc'    => implode( ', ', $loc ),
			'status' => $status,
			'rent'   => $rent,
		);
	}

	function blockke_hm3_listings( $action, $n ) {
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
		/* one card per project, featured first, newest after; every card needs a photo */
		$ids      = array();
		$projects = array();
		foreach ( array_merge( $featured, $newest ) as $id ) {
			if ( count( $ids ) >= $n ) {
				break;
			}
			if ( ! get_post_thumbnail_id( $id ) ) {
				continue;
			}
			$pt  = blockke_hm3_first_term( $id, 'property_project' );
			$key = $pt ? 'p' . $pt->term_id : 'l' . $id;
			if ( isset( $projects[ $key ] ) ) {
				continue;
			}
			$projects[ $key ] = true;
			$ids[]            = (int) $id;
		}
		$out = array();
		foreach ( $ids as $id ) {
			$out[] = blockke_hm3_listing( $id );
		}
		return $out;
	}

	/** Development card from a property_project slug. */
	function blockke_hm3_development( $slug ) {
		$cfg  = blockke_hm3_config();
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
		$area = blockke_hm3_first_term( $master, 'property_area' );
		$comp = get_post_meta( $master, 'bke_completion_stated', true );
		if ( ! $comp ) {
			$ct   = blockke_hm3_first_term( $master, 'property_completion' );
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
			'id'      => $master,
			'name'    => html_entity_decode( $term->name, ENT_QUOTES, 'UTF-8' ),
			'area'    => $area ? $area->name : '',
			'price'   => $min_price ? 'From ' . blockke_hm3_price( $min_price ) : 'Prices on request',
			'from'    => $min_price ? blockke_hm3_price( $min_price ) : 'On request',
			'mix'     => $mix,
			'comp'    => $comp,
			'thumb'   => (int) get_post_thumbnail_id( $master ),
			'url'     => $url,
			'mandate' => in_array( $slug, (array) $cfg['mandates'], true ),
		);
	}

	function blockke_hm3_areas() {
		$cfg = blockke_hm3_config();
		$out = array();
		foreach ( (array) $cfg['neighbourhoods'] as $slug ) {
			$t = get_term_by( 'slug', $slug, 'property_area' );
			if ( ! $t || (int) $t->count < 1 ) {
				continue;
			}
			$thumb = blockke_hm3_term_image( $t->term_id );
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
				'count' => (int) $t->count,
				'url'   => blockke_hm3_area_url( $t ),
				'thumb' => $thumb,
			);
		}
		return $out;
	}

	function blockke_hm3_articles( $n = 4 ) {
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

	function blockke_hm3_agent( $id ) {
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

	/* ------------------------------------------------------------------ development-name search (v3.1) */

	/** Words a development's name is often typed with or without ("Capella" finds "Capella Residency"). */
	function blockke_hm3_dev_filler() {
		return array( 'apartment' => 1, 'apartments' => 1, 'residence' => 1, 'residences' => 1, 'residency' => 1, 'residencies' => 1, 'homes' => 1, 'estate' => 1, 'the' => 1, 'at' => 1 );
	}

	/**
	 * Lowercase ASCII words: accents folded, "&" read as "and", and so is a lone "n" or "n’" between two words ("Oak n Ivy";
	 * as the last word it is a name still being typed, "N" for Nova), apostrophes dropped, other punctuation a space.
	 * Same as fold() in the page script.
	 */
	function blockke_hm3_dev_fold( $s ) {
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
	function blockke_hm3_dev_prep( $s ) {
		static $memo = array(); // the same names are compared for every query
		$s = (string) $s;
		if ( isset( $memo[ $s ] ) ) {
			return $memo[ $s ];
		}
		if ( count( $memo ) > 5000 ) {
			$memo = array();
		}
		$memo[ $s ] = blockke_hm3_dev_prep_words( $s );
		return $memo[ $s ];
	}

	/** prep() without the memo. */
	function blockke_hm3_dev_prep_words( $s ) {
		$f = blockke_hm3_dev_fold( $s );
		$w = '' === $f ? array() : explode( ' ', $f );
		$x = blockke_hm3_dev_filler();
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
	function blockke_hm3_dev_osa( $a, $b ) {
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
	function blockke_hm3_dev_lead( $t, $u ) {
		return '' !== $t && '' !== $u && ( $t[0] === $u[0] || ( false !== strpos( 'ck', $t[0] ) && false !== strpos( 'ck', $u[0] ) ) );
	}

	/** One typed word is a whole-word slip of a word of the name: the same first letter (c = k), one slip, two from eight letters. Same as slip() in the page script. */
	function blockke_hm3_dev_slip( $t, $u ) {
		$n = strlen( $t );
		return $n >= 4 && $n <= 40 && strlen( $u ) <= 60 && blockke_hm3_dev_lead( $t, $u ) && blockke_hm3_dev_osa( $t, $u ) <= ( $n >= 8 ? 2 : 1 ); // no name has such long words
	}

	/** One typed word is a near miss for a word of the name: a whole-word slip, or one slip from how the word starts. Same as near() in the page script. */
	function blockke_hm3_dev_near( $t, $u ) {
		$n = strlen( $t );
		if ( $n < 4 || $n > 40 || strlen( $u ) > 60 || ! blockke_hm3_dev_lead( $t, $u ) ) {
			return false;
		}
		if ( blockke_hm3_dev_slip( $t, $u ) ) {
			return true;
		}
		if ( strlen( $u ) > $n ) {
			for ( $k = $n - 1; $k <= $n + 1; $k++ ) {
				if ( blockke_hm3_dev_osa( $t, substr( $u, 0, $k ) ) <= 1 ) {
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
	function blockke_hm3_dev_score( $q, $m ) {
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
				if ( 0 === strpos( $u, $t ) || blockke_hm3_dev_near( $t, $u ) ) {
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
	function blockke_hm3_dev_pref( $b, $mode ) {
		$b = (int) $b;
		if ( 'rent' === $mode ) {
			return ( $b & 2 ) ? 2 : 0;
		}
		return ( ( $b & 1 ) ? 2 : 0 ) + ( ( 'offplan' === $mode && ( $b & 4 ) ) ? 1 : 0 );
	}

	/** A query against a development's name and its aliases (Project names that read differently), an alias scoring 1 lower. Same as all() in the page script. */
	function blockke_hm3_dev_score_all( $q, $e ) {
		$s = blockke_hm3_dev_score( $q, blockke_hm3_dev_prep( $e['n'] ) );
		foreach ( (array) $e['al'] as $a ) {
			$s = max( $s, blockke_hm3_dev_score( $q, blockke_hm3_dev_prep( $a ) ) - 1 );
		}
		return $s;
	}

	/** Words of an area's name that say nothing on their own ("Ngong Road", "Tatu City", "Lower Kabete"): never taken for the area. */
	function blockke_hm3_dev_notarea() {
		return array( 'road' => 1, 'city' => 1, 'town' => 1, 'general' => 1, 'river' => 1, 'rivers' => 1, 'spring' => 1, 'springs' => 1, 'valley' => 1, 'south' => 1, 'north' => 1, 'east' => 1, 'west' => 1, 'upper' => 1, 'lower' => 1, 'garden' => 1, 'gardens' => 1, 'plains' => 1, 'park' => 1, 'hill' => 1, 'hills' => 1, 'view' => 1, 'views' => 1, 'estate' => 1, 'beach' => 1, 'heights' => 1, 'ridge' => 1, 'gate' => 1, 'green' => 1, 'court' => 1, 'place' => 1, 'square' => 1 );
	}

	/** Every area word the index knows (from each development's place, at least 4 letters, not a word like "Road"), plus Nairobi and Kenya. Same as areas() in the page script. */
	function blockke_hm3_dev_areas( $d ) {
		$no  = blockke_hm3_dev_notarea();
		$out = array();
		foreach ( $d as $e ) {
			foreach ( blockke_hm3_dev_prep( $e['pl'] )['w'] as $u ) {
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
	function blockke_hm3_dev_unplace( $q, $place ) {
		$n = count( $q['w'] );
		if ( ! $place || $n < 2 ) {
			return null;
		}
		$keep = array();
		foreach ( $q['w'] as $i => $t ) {
			$hit = false;
			foreach ( $place as $u ) {
				if ( $u === $t || ( $i === $n - 1 && 0 === strpos( $u, $t ) ) || ( strlen( $t ) >= 5 && blockke_hm3_dev_slip( $t, $u ) ) ) {
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
		$r = blockke_hm3_dev_prep( implode( ' ', $keep ) );
		return $r['o'] ? null : $r;
	}

	/**
	 * Developments matching $text, best first: array of array( index, score ). Near misses only when nothing better matched;
	 * the query inside a word of the name (40) only when no name starts with it or has a word that does ("unity": Unity
	 * Parkside, not Karunguru Gated Community).
	 * A query that also names the development's own area is matched without it, 1 lower ("Capella Westlands", "Capella W");
	 * when nothing better than a near miss matches, without any area word the index knows, 2 lower ("Capella Kilimani").
	 * Ties: the mode's preference, then the homepage's own developments in their order (config feature + developments, see
	 * blockke_hm3_dev_feat()), then the shorter name, then the index order (busiest first). Same as find() in the page script.
	 */
	function blockke_hm3_dev_find( $text, $d, $mode ) {
		$q = blockke_hm3_dev_prep( $text );
		if ( '' === $q['c'] ) {
			return array();
		}
		$ft  = blockke_hm3_dev_feat( $d );
		$sc  = array();
		$len = array();
		$top = 0;
		foreach ( $d as $i => $e ) {
			$len[ $i ] = strlen( blockke_hm3_dev_prep( $e['n'] )['c'] );
			$s         = blockke_hm3_dev_score_all( $q, $e );
			$q1        = blockke_hm3_dev_unplace( $q, blockke_hm3_dev_prep( $e['pl'] )['w'] );
			if ( $q1 ) {
				$s = max( $s, blockke_hm3_dev_score_all( $q1, $e ) - 1 );
			}
			$sc[ $i ] = $s;
			$top      = max( $top, $s );
		}
		$q2 = $top < 35 ? blockke_hm3_dev_unplace( $q, blockke_hm3_dev_areas( $d ) ) : null;
		if ( $q2 ) {
			foreach ( $d as $i => $e ) {
				$sc[ $i ] = max( $sc[ $i ], blockke_hm3_dev_score_all( $q2, $e ) - 2 );
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
				$px = blockke_hm3_dev_pref( $d[ $x[0] ]['b'], $mode );
				$py = blockke_hm3_dev_pref( $d[ $y[0] ]['b'], $mode );
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
	 * The homepage's own developments (config feature, then developments: property_project slugs) as index => position in that
	 * list; a development not on it is left out. It breaks ties in blockke_hm3_dev_find() and orders blockke_hm3_dev_top(), and
	 * the page script gets it as dv.f. Worked out per request from the config, so a config change counts at once.
	 */
	function blockke_hm3_dev_feat( $d ) {
		static $memo = array();
		$cfg   = blockke_hm3_config();
		$slugs = array_values( array_filter( array_merge( array( $cfg['feature'] ), (array) $cfg['developments'] ), 'is_string' ) );
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
	function blockke_hm3_dev_strong( $res, $text, $d ) {
		$q = blockke_hm3_dev_prep( $text );
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
		$k = blockke_hm3_dev_unplace( $q, blockke_hm3_dev_prep( $d[ $i ]['pl'] )['w'] );
		$k = $k ? $k : $q;
		if ( preg_match_all( '/[a-z]/', $k['c'] ) < 5 ) {
			return null;
		}
		foreach ( array_merge( array( $d[ $i ]['n'] ), (array) $d[ $i ]['al'] ) as $nm ) {
			$w  = blockke_hm3_dev_prep( $nm )['w'];
			$ok = true;
			foreach ( $k['k'] as $t ) {
				$hit = false;
				foreach ( $w as $u ) {
					if ( 0 === strpos( $u, $t ) || blockke_hm3_dev_slip( $t, $u ) ) {
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
	function blockke_hm3_dev_named( $n ) {
		$n = trim( (string) $n );
		return '' !== $n && mb_strlen( $n ) <= 60 && preg_match( '/\p{L}/u', $n ) && ! preg_match( '/\b(?:bedrooms?|for sale|for rent|to let)\b/iu', $n );
	}

	/** Project names that read differently from the development's own name (compared as the search compares them). */
	function blockke_hm3_dev_aliases( $name, $names ) {
		$seen = array( blockke_hm3_dev_prep( $name )['c'] => true );
		$out  = array();
		foreach ( $names as $n ) {
			$n = trim( html_entity_decode( (string) $n, ENT_QUOTES, 'UTF-8' ) );
			$c = blockke_hm3_dev_prep( $n )['c'];
			if ( '' === $c || isset( $seen[ $c ] ) || ! blockke_hm3_dev_named( $n ) ) {
				continue;
			}
			$seen[ $c ] = true;
			$out[]      = $n;
		}
		return array_slice( $out, 0, 4 );
	}

	/** Listing id => array( term_id => term name ) for property_project, in one query per 500 listings. */
	function blockke_hm3_dev_projects( $ids ) {
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
	function blockke_hm3_dev_from_bkn() {
		$all = blockke_bkn_devs();
		if ( empty( $all['devs'] ) || ! is_array( $all['devs'] ) ) {
			return array();
		}
		$ids = array();
		foreach ( $all['devs'] as $c ) {
			$ids = array_merge( $ids, (array) $c['ids'] );
		}
		$pj  = blockke_hm3_dev_projects( $ids );
		$out = array();
		foreach ( $all['devs'] as $c ) {
			$name = trim( (string) $c['name'] );
			if ( ! blockke_hm3_dev_named( $name ) || empty( $c['url'] ) ) {
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
				'al' => blockke_hm3_dev_aliases( $name, $terms ),
				'pj' => array_keys( $terms ),
				'k'  => (int) $c['n'],
				'ft' => ! empty( $c['ft'] ),
				'vt' => ! empty( $c['vt'] ),
			);
		}
		return $out;
	}

	/**
	 * Without snippet 42706: property_project terms with listings, each linked to its main listing (the one with units, else
	 * the newest by date), as blockke_hm3_development() does. Its area: the main listing's bke_dev_area (a slug) when set, else
	 * the one of its areas that most of the development's listings carry, else the first. A few queries for every project
	 * together, not one per project.
	 */
	function blockke_hm3_dev_from_terms() {
		global $wpdb;
		$ids = get_posts( array(
			'post_type'      => 'estate_property',
			'post_status'    => 'publish',
			'posts_per_page' => 3000,
			'fields'         => 'ids',
			'no_found_rows'  => true,
			'orderby'        => array( 'date' => 'DESC', 'ID' => 'ASC' ), // newest first, as blockke_hm3_development() takes a project's listings (same date: the lower ID, as MariaDB returns them there)
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
			if ( ! $live || ! blockke_hm3_dev_named( $name ) ) {
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
	 * Kept in one transient (blockke_home_v3_devs, a single row whatever the stamp), current while the content stamp is the
	 * same and it is younger than config cache_minutes. Built from snippet 42706's developments, it is rebuilt only once 42706
	 * has rebuilt them for the current listings, which its development, area and search pages do: until then (after a listing
	 * is saved, say) the copy is kept, at most 12 hours old, so the homepage neither pays for that rebuild (about half a second)
	 * nor waits on 42706's build lock. Names and links rarely change; prices catch up within the hour.
	 */
	function blockke_hm3_dev_index() {
		static $idx = null;
		if ( null !== $idx ) {
			return $idx;
		}
		$cfg  = blockke_hm3_config();
		$key  = 'blockke_home_v3_devs';
		$st   = blockke_hm3_stamp();
		$skip = isset( $_GET['bkhome_nocache'] ) || isset( $_GET['elementor-preview'] ) || is_preview(); // phpcs:ignore WordPress.Security.NonceVerification
		$hit  = $skip ? false : get_transient( $key );
		$age  = is_array( $hit ) && isset( $hit['st'], $hit['src'], $hit['d'], $hit['t'] ) && is_array( $hit['d'] ) && $hit['d'] ? time() - (int) $hit['t'] : null;
		if ( null !== $age && $st === $hit['st'] && $age < max( 1, (int) $cfg['cache_minutes'] ) * MINUTE_IN_SECONDS ) {
			$idx = $hit;
			return $idx;
		}
		if ( null !== $age && 'bkn' === $hit['src'] && $age < 12 * HOUR_IN_SECONDS && ! blockke_hm3_dev_bkn_ready() ) {
			$idx = $hit;
			return $idx;
		}
		$d   = array();
		$src = '';
		if ( function_exists( 'blockke_bkn_devs' ) ) {
			try {
				$d   = blockke_hm3_dev_from_bkn();
				$src = 'bkn';
			} catch ( Throwable $e ) {
				error_log( 'BLOCK homepage v3.1 (developments from 42706): ' . $e->getMessage() ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions
				$d = array();
			}
		}
		if ( ! $d ) {
			$d   = blockke_hm3_dev_from_terms();
			$src = $d ? 'terms' : '';
		}
		usort(
			$d,
			function ( $a, $b ) {
				return $a['k'] !== $b['k'] ? $b['k'] - $a['k'] : strcasecmp( $a['n'], $b['n'] );
			}
		);
		$idx = array( 'st' => $st, 'src' => $src, 't' => time(), 'd' => array_values( $d ) );
		if ( ! $skip && $d ) {
			set_transient( $key, $idx, 12 * HOUR_IN_SECONDS );
		}
		return $idx;
	}

	/**
	 * Whether snippet 42706 holds its listing rows for the current listings (transient bkn_rows at the version and within the
	 * day blockke_bkn_rows() accepts), so reading its developments costs no rebuild. True when that cannot be told (another
	 * 42706 version): the index is then built as it always was.
	 */
	function blockke_hm3_dev_bkn_ready() {
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
	function blockke_hm3_dev_query() {
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
	function blockke_hm3_dev_mode() {
		$m = isset( $_GET['bkfor'] ) && is_scalar( $_GET['bkfor'] ) ? (string) $_GET['bkfor'] : ''; // phpcs:ignore WordPress.Security.NonceVerification
		return in_array( $m, array( 'rent', 'offplan' ), true ) ? $m : 'buy';
	}

	/** The page showing this layout when it is not the front page yet (a preview such as /home-v3/), else 0. */
	function blockke_hm3_dev_page() {
		return ( ! is_front_page() && is_singular() ) ? (int) get_queried_object_id() : 0;
	}

	/** Where the name search posts: the homepage, or the preview page it is on (its cached copy is kept apart, see blockke_hm3_output()). */
	function blockke_hm3_dev_action() {
		$id = blockke_hm3_dev_page();
		$p  = $id ? get_permalink( $id ) : '';
		return $p ? $p : home_url( '/' );
	}

	/** "39.5M" / "120K": the price without the currency or "a month" (the page script adds them back). */
	function blockke_hm3_dev_short( $n, $rent = false ) {
		return preg_replace( '/^KES\s+|\s+a month$/', '', blockke_hm3_price( $n, $rent ) );
	}

	/** What one development shows in a mode: array( url, price, meta ). Same as show() in the page script. */
	function blockke_hm3_dev_show( $e, $mode ) {
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
			'price' => $n > 0 ? ( $from ? 'From ' : '' ) . blockke_hm3_price( $n, $use ) : 'Prices on request',
			'meta'  => implode( ' · ', $meta ),
		);
	}

	/**
	 * The index for the page script, as small as it will go: b = the common start of the listing paths, d = one array per
	 * development: [ name, place, path, sale price ("+" = from), rent price, bits, rental path, aliases ], trailing blanks dropped;
	 * f = the homepage's own developments, index => position (blockke_hm3_dev_feat()), which break ties as on the server.
	 */
	function blockke_hm3_dev_data( $d ) {
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
				$e['s'] > 0 ? ( $e['sf'] ? '+' : '' ) . blockke_hm3_dev_short( $e['s'] ) : '',
				$e['r'] > 0 ? ( $e['rf'] ? '+' : '' ) . blockke_hm3_dev_short( $e['r'], true ) : '',
				(int) $e['b'],
				'' !== $e['ru'] ? $path( $e['ru'] ) : '',
				array_values( (array) $e['al'] ),
			);
			while ( count( $row ) > 3 && in_array( end( $row ), array( '', 0, array() ), true ) ) {
				array_pop( $row );
			}
			$out[] = $row;
		}
		return array( 'b' => $base, 'f' => (object) blockke_hm3_dev_feat( $d ), 'd' => $out );
	}

	/**
	 * The developments offered when nothing matches: those that suit the mode first (Rent: with rentals), then the rest, so the
	 * list is never short where few developments have rentals; within each, the homepage's own (config feature + developments),
	 * then featured, then on the new layout, then the busiest.
	 */
	function blockke_hm3_dev_top( $d, $mode, $n = 6 ) {
		$ft   = blockke_hm3_dev_feat( $d );
		$rank = array();
		foreach ( $d as $i => $e ) {
			$w          = isset( $ft[ $i ] ) ? $ft[ $i ] : 999;
			$rank[ $i ] = array( blockke_hm3_dev_pref( $e['b'], $mode ) < 2 ? 1 : 0, $w, $e['ft'] ? 0 : 1, $e['vt'] ? 0 : 1, $i );
		}
		uasort(
			$rank,
			function ( $a, $b ) {
				return $a <=> $b;
			}
		);
		return array_slice( array_keys( $rank ), 0, $n );
	}

	/** The search form under the filters: its own GET form, with a datalist of every name for browsers without the page script. */
	function blockke_hm3_dev_form( $d, $q ) {
		$names = array();
		foreach ( $d as $e ) {
			$names[ $e['n'] ] = true;
		}
		$names = array_keys( $names );
		usort( $names, 'strcasecmp' );
		$o  = '<form class="bkh-ds" role="search" aria-label="Find a development by name" method="get" action="' . esc_url( blockke_hm3_dev_action() . '#bkh-dr' ) . '">';
		$o .= '<div class="bkh-ds-field"><label for="bkh-ds-q">Know the development?</label>';
		$o .= '<div class="bkh-ds-row">' . blockke_hm3_svg( 'search' ) . '<input id="bkh-ds-q" type="search" name="bkdev" value="' . esc_attr( (string) $q ) . '" maxlength="80" autocomplete="off" autocorrect="off" autocapitalize="words" spellcheck="false" enterkeyhint="search" placeholder="e.g. Golden Hill" data-long="Type a development name, e.g. Golden Hill" list="bkh-ds-list"></div></div>';
		$o .= '<input type="hidden" name="bkfor" value="" disabled>';
		$o .= '<button type="submit" class="bkh-ds-go"><span>Find</span>' . blockke_hm3_svg( 'arrow' ) . '</button>';
		$o .= '<datalist id="bkh-ds-list">';
		foreach ( $names as $n ) {
			$o .= '<option value="' . esc_attr( $n ) . '">';
		}
		$o .= '</datalist></form>';
		return $o;
	}

	/**
	 * For a name that matches nothing: the developments with a word starting like one of its words (three letters or more),
	 * most such words first, then as the mode prefers. When no name has such a word, those with a word two slips or fewer
	 * from one of its words (five letters or more, the same first letter, c = k: "kapela" for Capella).
	 */
	function blockke_hm3_dev_partial( $q, $d, $mode ) {
		$tw = array();
		foreach ( blockke_hm3_dev_prep( $q )['k'] as $t ) {
			if ( strlen( $t ) >= 3 && 'and' !== $t ) {
				$tw[ $t ] = true;
			}
		}
		$hit = array();
		foreach ( array( 'start', 'slip' ) as $how ) {
			foreach ( $tw && ! $hit ? $d : array() as $i => $e ) {
				$best = 0;
				foreach ( array_merge( array( $e['n'] ), (array) $e['al'] ) as $nm ) {
					$w = blockke_hm3_dev_prep( $nm )['w'];
					$c = 0;
					foreach ( array_keys( $tw ) as $t ) {
						$t = (string) $t;
						foreach ( $w as $u ) {
							if ( 'start' === $how ? 0 === strpos( $u, $t ) : ( strlen( $t ) >= 5 && strlen( $t ) <= 40 && strlen( $u ) <= 60 && blockke_hm3_dev_lead( $t, $u ) && blockke_hm3_dev_osa( $t, $u ) <= 2 ) ) {
								$c++;
								break;
							}
						}
					}
					$best = max( $best, $c );
				}
				if ( $best ) {
					$hit[] = array( (int) $i, $best, blockke_hm3_dev_pref( $e['b'], $mode ) );
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

	/** The results panel for ?bkdev= (rendered fresh, never cached): the matches best first, or a plain "no match" with somewhere to go next. */
	function blockke_hm3_dev_results( $d, $q, $mode ) {
		$res  = '' === $q ? array() : blockke_hm3_dev_find( $q, $d, $mode );
		$best = blockke_hm3_dev_strong( $res, $q, $d );
		$all  = count( $res );
		$near = $res && $res[0][1] < 35;
		$show = array();
		foreach ( array_slice( $res, 0, 12 ) as $r ) {
			$show[] = $r[0];
		}
		if ( null !== $best ) {
			$show = array_values( array_unique( array_merge( array( $best ), $show ) ) );
		}
		$part = ( $show || '' === $q ) ? array() : array_slice( blockke_hm3_dev_partial( $q, $d, $mode ), 0, 6 );
		$top  = $show ? array() : array_slice( array_values( array_unique( array_merge( $part, blockke_hm3_dev_top( $d, $mode ) ) ) ), 0, 6 );
		$said = mb_strlen( $q ) > 48 ? rtrim( mb_substr( $q, 0, 47 ) ) . '…' : $q;
		$item = function ( $i, $is_best ) use ( $d, $mode ) {
			$p  = blockke_hm3_dev_show( $d[ $i ], $mode );
			$o  = '<li' . ( $is_best ? ' class="is-best"' : '' ) . '><a class="bkh-dr-item' . ( $is_best ? ' is-best' : '' ) . '" href="' . esc_url( $p['url'] ) . '">';
			$o .= $is_best ? '<span class="bkh-dr-tag">Best match</span>' : '';
			$o .= '<span class="bkh-dr-name">' . esc_html( $d[ $i ]['n'] ) . '</span>';
			$o .= '' !== $p['meta'] ? '<span class="bkh-dr-meta">' . esc_html( $p['meta'] ) . '</span>' : '';
			$o .= '<span class="bkh-dr-price">' . esc_html( $p['price'] ) . '</span>';
			$o .= $is_best ? '<span class="bkh-dr-cta">View the development' . blockke_hm3_svg( 'arrow' ) . '</span>' : '';
			return $o . '</a></li>';
		};
		$o = '<div class="bkh-dr" id="bkh-dr" role="region" aria-labelledby="bkh-dr-h" data-where="home_dev"><div class="bkh-dr-head">';
		if ( '' === $q ) {
			$o .= '<h2 class="bkh-dr-title" id="bkh-dr-h">Search by <em>development name</em></h2>';
			$o .= '<p class="bkh-dr-sub">Type the name above &mdash; Golden Hill, say &mdash; or start with one of these.</p>';
		} elseif ( ! $show ) {
			$o .= '<h2 class="bkh-dr-title" id="bkh-dr-h">No development called <em>&ldquo;' . esc_html( $said ) . '&rdquo;</em> on Block yet</h2>';
			$o .= '<p class="bkh-dr-sub">' . ( $part ? 'Check the spelling. The closest names come first.' : 'Check the spelling, or start with one of these.' ) . ' If it isn&rsquo;t listed, send us the name and we&rsquo;ll check it for you.</p>';
		} elseif ( $near && null !== $best ) {
			$o .= '<h2 class="bkh-dr-title" id="bkh-dr-h">Did you mean <em>' . esc_html( $d[ $best ]['n'] ) . '</em>?</h2>';
			$o .= '<p class="bkh-dr-sub">No exact match for &ldquo;' . esc_html( $said ) . '&rdquo;. This is the closest name on Block.</p>';
		} elseif ( $near ) {
			$o .= '<h2 class="bkh-dr-title" id="bkh-dr-h">No exact match for <em>&ldquo;' . esc_html( $said ) . '&rdquo;</em></h2>';
			$o .= '<p class="bkh-dr-sub">Did you mean ' . ( 1 === count( $show ) ? 'this one' : 'one of these' ) . '?</p>';
		} elseif ( 1 === $all ) {
			$o .= '<h2 class="bkh-dr-title" id="bkh-dr-h">One development matches <em>&ldquo;' . esc_html( $said ) . '&rdquo;</em></h2>';
		} else {
			$o .= '<h2 class="bkh-dr-title" id="bkh-dr-h">Developments matching <em>&ldquo;' . esc_html( $said ) . '&rdquo;</em></h2>';
			$o .= '<span class="bkh-dr-n">' . ( $all > 12 ? 'Best 12 of ' . (int) $all : (int) $all . ' developments' ) . '</span>';
			$o .= $all > 12 ? '<p class="bkh-dr-sub">Add another word of the name to narrow it down.</p>' : '';
		}
		$o .= '</div><ol class="bkh-dr-list">';
		foreach ( $show ? $show : $top as $pos => $i ) {
			$o .= $item( $i, null !== $best && 0 === $pos );
		}
		$o .= '</ol><div class="bkh-dr-foot">';
		$o .= '<a class="bkh-more" href="' . esc_url( blockke_hm3_url( 'offplan' ) ) . '">All new developments' . blockke_hm3_svg( 'arrow' ) . '</a>';
		$topic = ( '' !== $q && ! $show ) ? 'a development called “' . $said . '”.' : 'a development.';
		$o .= '<a class="bkh-dr-wa" href="' . esc_url( blockke_hm3_wa( $topic ) ) . '" target="_blank" rel="noopener">' . blockke_hm3_svg( 'wa' ) . 'Ask us on WhatsApp</a>';
		$o .= '<a class="bkh-dr-alt" href="' . esc_url( blockke_hm3_url( 'consult' ) ) . '">Or book a free consultation</a>';
		return $o . '</div></div>';
	}

	/* ------------------------------------------------------------------ CSS */

	function blockke_hm3_css() {
		return <<<'CSS'
.bkh{--navy:#0B1F3A;--navy-deep:#07172C;--navy-2:#13294B;--ink:#182230;--slate:#4B5768;--muted:#717C8A;--paper:#FFFFFF;--stone:#F4F2EE;--line:#E3DFD8;--line-2:#CDC7BC;--brass:#B48D4B;--brass-d:#8A6A31;--brass-l:#D8B97C;--on-navy:#BFC9D6;--wa:#1EBE5D;--sans:"Montserrat",system-ui,-apple-system,"Segoe UI",Roboto,sans-serif;--display:"Bodoni Moda","Didot","Bodoni 72",Georgia,"Times New Roman",serif;--max:1280px;--gutter:clamp(20px,5vw,72px);--sec:clamp(84px,9.5vw,144px);--r:2px;
position:relative;display:block;float:none;clear:both;width:100%;max-width:100%;margin:0;padding:0;text-align:left;font:400 15.5px/1.65 var(--sans);color:var(--ink);background:var(--paper);-webkit-font-smoothing:antialiased;overflow-x:clip}
.bkh *,.bkh *::before,.bkh *::after{box-sizing:border-box}
.bkh :is(h1,h2,h3,h4,p,ul,ol,li,figure){margin:0;padding:0;border:0;background:none;list-style:none;text-transform:none;letter-spacing:normal}
.bkh li::before,.bkh li::after{content:none!important;display:none!important}
.bkh a{color:inherit;text-decoration:none;transition:color .2s,opacity .2s,border-color .2s;box-shadow:none}
.bkh img{display:block;max-width:100%;height:auto;border:0}
.bkh button,.bkh select{font:inherit;color:inherit}
.bkh svg{width:16px;height:16px;flex:none}
.bkh [hidden]{display:none!important}
.bkh h3{font-family:var(--sans)!important;margin:0!important;padding:0!important;letter-spacing:normal!important;text-transform:none!important}
.bkh em{font-style:italic;font-weight:inherit}
.bkh .bkh-wrap{max-width:var(--max);margin:0 auto;padding:0 var(--gutter)}
.bkh .bkh-eyebrow{display:inline-flex;align-items:center;gap:12px;font:600 10.5px/1.4 var(--sans);letter-spacing:.24em;text-transform:uppercase;color:var(--brass)}
.bkh .bkh-eyebrow::before{content:"";display:block;width:28px;height:1px;background:currentColor;opacity:.9}
.bkh :is(h1,h2).bkh-display{font-family:var(--display)!important;font-weight:500!important;font-variation-settings:"opsz" 30;letter-spacing:-.005em!important;line-height:1.04!important;color:var(--navy)!important;text-transform:none!important;text-wrap:balance}
.bkh :is(.bkh-stat b,.bkh-card-price,.bkh-dev-price b,.bkh-area h3,.bkh-lead h3,.bkh-val h3,.bkh-feat-meta b.bkh-num,.bkh-step i,.bkh-quote){font-variation-settings:"opsz" 24}
.bkh .bkh-stat b small{font:500 .62em/1 var(--display);vertical-align:10%;margin-left:2px}
.bkh h2.bkh-display{font-size:clamp(36px,4.3vw,60px)!important;margin:18px 0 0}
.bkh .bkh-lede{font-size:clamp(16px,1.2vw,17.5px);line-height:1.65;color:var(--slate);max-width:60ch;margin-top:18px}
.bkh .bkh-head{display:flex;align-items:flex-end;justify-content:space-between;gap:24px 48px;flex-wrap:wrap;margin-bottom:clamp(32px,4.5vw,60px)}
.bkh .bkh-head>div{max-width:800px}
.bkh .bkh-more{display:inline-flex;align-items:center;gap:10px;font:600 11px/1 var(--sans);letter-spacing:.2em;text-transform:uppercase;color:var(--navy);padding-bottom:8px;border-bottom:1px solid var(--brass);white-space:nowrap}
.bkh .bkh-more:hover{color:var(--brass-d)}
.bkh .bkh-more svg{width:15px;height:15px;transition:transform .25s}
.bkh .bkh-more:hover svg{transform:translateX(4px)}
.bkh .bkh-btn{display:inline-flex;align-items:center;justify-content:center;gap:10px;min-height:54px;padding:0 32px;border-radius:var(--r);font:600 11.5px/1 var(--sans);letter-spacing:.18em;text-transform:uppercase;cursor:pointer;border:1px solid transparent;transition:background .2s,color .2s,border-color .2s,transform .2s;white-space:nowrap}
.bkh .bkh-btn:hover{transform:translateY(-1px)}
.bkh .bkh-btn svg{width:17px;height:17px}
.bkh .bkh-btn--navy{background:var(--navy);color:#fff}
.bkh .bkh-btn--navy:hover{background:var(--navy-2);color:#fff}
.bkh .bkh-btn--white{background:#fff;color:var(--navy)}
.bkh .bkh-btn--white:hover{background:var(--stone);color:var(--navy)}
.bkh .bkh-btn--ghost{background:transparent;color:var(--navy);border-color:var(--line-2)}
.bkh .bkh-btn--ghost:hover{border-color:var(--navy);color:var(--navy)}
.bkh .bkh-btn--ghostw{background:transparent;color:#fff;border-color:rgba(255,255,255,.42)}
.bkh .bkh-btn--ghostw:hover{border-color:#fff;color:#fff}
.bkh .bkh-sec{padding:var(--sec) 0}
.bkh .bkh-sec--stone{background:var(--stone)}
.bkh .bkh-sec--navy{background:var(--navy);color:#fff}
.bkh .bkh-sec--deep{background:var(--navy-deep);color:#fff}
.bkh .bkh-sec--rule{border-top:1px solid var(--line)}
.bkh :is(.bkh-sec--navy,.bkh-sec--deep) .bkh-eyebrow{color:var(--brass-l)}
.bkh :is(.bkh-sec--navy,.bkh-sec--deep) h2.bkh-display{color:#fff!important}
.bkh :is(.bkh-sec--navy,.bkh-sec--deep) .bkh-lede{color:var(--on-navy)}
.bkh :is(.bkh-sec--navy,.bkh-sec--deep) .bkh-more{color:#fff;border-color:var(--brass-l)}
.bkh .bkh-noimg{display:block;background:linear-gradient(135deg,#17315a,#0b1f3a)}
.bkh .bkh-media{position:relative;overflow:hidden;border-radius:var(--r);background:var(--stone)}
.bkh .bkh-media img,.bkh .bkh-media .bkh-noimg{width:100%;height:100%;object-fit:cover;transition:transform 1.1s cubic-bezier(.2,.7,.2,1)}
.bkh a:hover .bkh-media img{transform:scale(1.035)}
.bkh .bkh-tag{position:absolute;top:16px;left:16px;z-index:1;display:inline-flex;align-items:center;gap:7px;padding:8px 11px;border-radius:var(--r);background:rgba(7,23,44,.82);color:#fff;font:600 10px/1 var(--sans);letter-spacing:.18em;text-transform:uppercase;backdrop-filter:blur(6px);-webkit-backdrop-filter:blur(6px);white-space:nowrap}
.bkh .bkh-tag svg{width:11px;height:11px}
.bkh .bkh-tag--brass{background:var(--brass);color:#fff}
.bkh .bkh-ctas{display:flex;flex-wrap:wrap;gap:12px;margin-top:32px}
.bkh .bkh-rv{transform:translateY(18px);transition:transform .9s cubic-bezier(.2,.7,.2,1)}
.bkh .bkh-rv.is-in{transform:none}
/* hero */
.bkh .bkh-hero{position:relative;isolation:isolate;min-height:min(92vh,920px);display:flex;align-items:flex-end;color:#fff;background:var(--navy-deep);overflow:hidden}
.bkh .bkh-hero-img{position:absolute;inset:0;z-index:-2;width:100%;height:100%;object-fit:cover;object-position:center 42%;transform:scale(1.06);animation:bkhSettle 16s cubic-bezier(.2,.6,.2,1) forwards}
@keyframes bkhSettle{to{transform:scale(1)}}
.bkh .bkh-hero::before{content:"";position:absolute;inset:0;z-index:-1;background:linear-gradient(180deg,rgba(7,23,44,.62) 0%,rgba(7,23,44,.12) 34%,rgba(7,23,44,.58) 66%,rgba(7,23,44,.95) 100%)}
.bkh .bkh-hero::after{content:"";position:absolute;inset:0;z-index:-1;background:linear-gradient(90deg,rgba(7,23,44,.74) 0%,rgba(7,23,44,0) 62%)}
.bkh .bkh-hero .bkh-wrap{position:relative;width:100%;padding-top:clamp(150px,22vh,224px);padding-bottom:clamp(52px,7vw,92px)}
.bkh .bkh-hero .bkh-eyebrow{color:var(--brass-l)}
.bkh .bkh-hero h1.bkh-display{font-size:clamp(50px,7.1vw,106px)!important;font-weight:600!important;font-variation-settings:"opsz" 40;line-height:.98!important;color:#fff!important;max-width:16ch;margin:22px 0 0;text-shadow:0 2px 30px rgba(0,0,0,.28)}
.bkh .bkh-hero-sub{font-size:clamp(16px,1.3vw,19px);line-height:1.55;color:rgba(255,255,255,.86);max-width:50ch;margin:26px 0 0}
.bkh .bkh-hero-quick{display:flex;flex-wrap:wrap;gap:8px 20px;align-items:center;margin:22px 0 0;font:500 12px/1.4 var(--sans);letter-spacing:.04em;color:rgba(255,255,255,.58)}
.bkh .bkh-hero-quick a{color:rgba(255,255,255,.9);border-bottom:1px solid rgba(255,255,255,.26);padding-bottom:2px}
.bkh .bkh-hero-quick a:hover{color:#fff;border-color:var(--brass-l)}
.bkh .bkh-hero-cap{position:absolute;right:var(--gutter);bottom:16px;font:500 10px/1.3 var(--sans);letter-spacing:.14em;color:rgba(255,255,255,.46);text-transform:uppercase}
/* search */
.bkh .bkh-search{margin-top:clamp(34px,4.5vw,52px);max-width:1100px}
.bkh .bkh-tabs{display:flex;gap:30px;border-bottom:1px solid rgba(255,255,255,.18)}
.bkh .bkh-tab{appearance:none;border:0;border-bottom:2px solid transparent;border-radius:0;margin:0 0 -1px;background:transparent;color:rgba(255,255,255,.62);font:600 11.5px/1 var(--sans);letter-spacing:.2em;text-transform:uppercase;padding:4px 2px 14px;cursor:pointer;transition:color .2s,border-color .2s;white-space:nowrap;box-shadow:none}
.bkh .bkh-tab:hover{color:#fff}
.bkh .bkh-tab[aria-selected="true"]{color:#fff;border-color:var(--brass-l)}
.bkh .bkh-form{display:grid;grid-template-columns:1.6fr 1fr 1fr 1fr auto;align-items:stretch;margin-top:18px;background:#fff;border-radius:var(--r);box-shadow:0 30px 80px rgba(2,10,24,.45);overflow:hidden}
.bkh .bkh-form.is-offplan{grid-template-columns:1.5fr 1fr .9fr 1fr 1fr auto}
.bkh .bkh-field{position:relative;display:flex;flex-direction:column;justify-content:center;padding:12px 40px 11px 22px;border-right:1px solid var(--line);min-width:0;min-height:66px}
.bkh .bkh-field label{font:600 9.5px/1.2 var(--sans);letter-spacing:.2em;text-transform:uppercase;color:var(--muted);margin-bottom:4px}
.bkh .bkh-field select{appearance:none;-webkit-appearance:none;width:100%;border:0;border-radius:0;background:transparent;box-shadow:none;height:auto;min-height:0;padding:0;margin:0;font:500 14.5px/1.3 var(--sans);color:var(--navy);cursor:pointer;outline:none;text-overflow:ellipsis}
.bkh .bkh-field::after{content:"";position:absolute;right:20px;top:50%;width:7px;height:7px;margin-top:-5px;border-right:1.5px solid var(--navy);border-bottom:1.5px solid var(--navy);transform:rotate(45deg);pointer-events:none;opacity:.7}
.bkh .bkh-field--completion{display:none}
.bkh .bkh-form.is-offplan .bkh-field--completion{display:flex}
.bkh .bkh-form.is-rent .bkh-field--completion{display:none}
.bkh .bkh-submit{appearance:none;border:0;margin:0;padding:0 34px;border-radius:0;background:var(--navy);color:#fff;font:600 11.5px/1 var(--sans);letter-spacing:.2em;text-transform:uppercase;cursor:pointer;display:inline-flex;align-items:center;justify-content:center;gap:10px;transition:background .2s;min-height:66px;box-shadow:none}
.bkh .bkh-submit:hover{background:var(--navy-2)}
.bkh .bkh-submit svg{width:16px;height:16px}
/* development-name search (v3.1): a stone strip under the filters, the typeahead (.bkh-dl, placed by the script) and the ?bkdev= panel */
.bkh .bkh-ds{position:relative;display:flex;align-items:stretch;margin:0;background:var(--stone);border-top:1px solid var(--line);border-radius:0 0 var(--r) var(--r);box-shadow:0 30px 80px rgba(2,10,24,.45);clip-path:inset(0 -160px -160px -160px);transition:background .2s}
.bkh .bkh-ds:focus-within{background:#fff;box-shadow:inset 0 -2px 0 var(--brass),0 30px 80px rgba(2,10,24,.45)}
.bkh .bkh-ds-field{flex:1;min-width:0;display:flex;flex-direction:column;justify-content:center;padding:11px 22px 10px;min-height:62px}
.bkh .bkh-ds label{font:600 9.5px/1.2 var(--sans);letter-spacing:.2em;text-transform:uppercase;color:#7F6129;margin:0 0 5px;padding:0;cursor:text}
.bkh .bkh-ds-row{display:flex;align-items:center;gap:10px;min-width:0}
.bkh .bkh-ds-row svg{width:15px;height:15px;color:var(--muted)}
.bkh .bkh-ds input[type="search"]{appearance:none;-webkit-appearance:none;flex:1;display:block;min-width:0;width:100%;max-width:none;height:auto;min-height:0;margin:0;padding:0;border:0;border-radius:0;background:transparent;box-shadow:none;outline:none;font:500 15px/1.3 var(--sans);letter-spacing:normal;color:var(--navy);text-overflow:ellipsis}
.bkh .bkh-ds input[type="search"]::placeholder{color:var(--slate);opacity:1;font-weight:400}
.bkh .bkh-ds input[type="search"]::-webkit-search-decoration{-webkit-appearance:none}
.bkh .bkh-ds input[type="search"]::-webkit-search-cancel-button{-webkit-appearance:none;appearance:none;flex:none;width:13px;height:13px;margin:0 0 0 12px;cursor:pointer;opacity:.7;background:linear-gradient(45deg,transparent 43%,var(--navy) 43%,var(--navy) 57%,transparent 57%),linear-gradient(-45deg,transparent 43%,var(--navy) 43%,var(--navy) 57%,transparent 57%)}
.bkh .bkh-ds-go{appearance:none;flex:none;position:relative;display:inline-flex;align-items:center;justify-content:center;gap:10px;min-height:62px;margin:0;padding:0 30px;border:0;border-left:1px solid var(--line-2);border-radius:0;background:transparent;box-shadow:none;color:var(--navy);font:600 11.5px/1 var(--sans);letter-spacing:.2em;text-transform:uppercase;cursor:pointer;transition:background .2s,color .2s}
.bkh .bkh-ds-go:hover{background:var(--navy);color:#fff}
.bkh .bkh-ds-go svg{width:15px;height:15px;transition:transform .25s}
.bkh .bkh-ds-go:hover svg{transform:translateX(3px)}
.bkh .bkh-ds-go:focus-visible,.bkh .bkh-dr a:focus-visible{outline:2px solid var(--brass);outline-offset:-4px}
@media (min-width:901px){.bkh .bkh-ds-go{min-width:157px}} /* Find as wide as v3.0.2's Search above it (157px in the page's fonts; the script matches Search's exact width), so the strip's rule lines up with the Search block */
@media (min-width:768px){.bkh .bkh-hero:has(.bkh-ds){min-height:calc(min(92vh,920px) + 63px)}} /* where the hero is at its minimum height, the strip (62px and its rule) adds to it instead of lifting the headline */
.bkh .bkh-hero-quick{position:relative} /* the Popular links paint above the strip's shadow, as they paint above the filters' shadow in v3.0.2 (the strip's clip-path makes it a layer of its own) */
.bkh .bkh-sr{position:absolute!important;width:1px;height:1px;margin:-1px;padding:0;overflow:hidden;clip:rect(0 0 0 0);white-space:nowrap;border:0}
.bkh .bkh-dl{position:absolute;z-index:60;top:0;left:0;max-height:460px;overflow-y:auto;overscroll-behavior:contain;background:#fff;border:1px solid var(--line-2);border-radius:var(--r);box-shadow:0 28px 70px rgba(2,10,24,.34);text-align:left;-webkit-overflow-scrolling:touch}
.bkh .bkh-dl.is-up{box-shadow:0 -24px 64px rgba(2,10,24,.32)}
.bkh .bkh-dl ul{display:block;margin:0;padding:0;list-style:none}
.bkh .bkh-dl li{display:grid;grid-template-columns:minmax(0,1fr) auto;align-items:center;gap:3px 18px;margin:0;padding:12px 20px;border-top:1px solid var(--line);cursor:pointer;color:var(--ink);list-style:none;background:none}
.bkh .bkh-dl li:first-child{border-top:0}
.bkh .bkh-dl li[aria-selected="true"]{background:var(--stone);box-shadow:inset 2px 0 0 var(--brass)}
.bkh .bkh-dl-name{grid-column:1;font:600 14.5px/1.3 var(--sans);color:var(--navy);overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.bkh .bkh-dl-name mark{background:none;color:inherit;padding:0;text-decoration:underline;text-decoration-color:var(--brass);text-decoration-thickness:1px;text-underline-offset:3px}
.bkh .bkh-dl-meta{grid-column:1;font:600 9.5px/1.4 var(--sans);letter-spacing:.16em;text-transform:uppercase;color:var(--slate);overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.bkh .bkh-dl-price{grid-column:2;grid-row:1/span 2;font:500 15.5px/1.1 var(--display);font-variation-settings:"opsz" 24;color:var(--navy);white-space:nowrap}
.bkh .bkh-dl li.bkh-dl-all{display:flex;align-items:center;gap:8px;padding:13px 20px;background:var(--stone);font:600 10.5px/1.2 var(--sans);letter-spacing:.18em;text-transform:uppercase;color:#7F6129}
.bkh .bkh-dl li.bkh-dl-all svg{width:14px;height:14px}
.bkh .bkh-dl li.bkh-dl-all[aria-selected="true"]{color:var(--navy)}
.bkh .bkh-dl-msg{margin:0;padding:15px 20px;font:500 13.5px/1.5 var(--sans);color:var(--slate)}
.bkh .bkh-dr{position:relative;margin-top:14px;padding:26px 30px 24px;background:#fff;color:var(--ink);border-radius:var(--r);box-shadow:0 30px 80px rgba(2,10,24,.45);clip-path:inset(0 -160px -160px -160px);scroll-margin-top:110px}
.bkh .bkh-dr-head{display:flex;align-items:baseline;justify-content:space-between;flex-wrap:wrap;gap:8px 28px;padding-bottom:16px;border-bottom:1px solid var(--line)}
.bkh .bkh-dr h2.bkh-dr-title{flex:1 1 420px;margin:0!important;padding:0!important;font:500 clamp(23px,2.3vw,31px)/1.15 var(--display)!important;font-variation-settings:"opsz" 24;letter-spacing:-.005em!important;text-transform:none!important;color:var(--navy)!important;overflow-wrap:anywhere}
.bkh .bkh-dr-n{font:600 10px/1.4 var(--sans);letter-spacing:.2em;text-transform:uppercase;color:var(--slate);white-space:nowrap}
.bkh .bkh-dr-sub{flex:1 1 100%;font:400 14.5px/1.6 var(--sans);color:var(--slate);margin:0}
.bkh .bkh-dr-list{display:grid;grid-template-columns:1fr 1fr;gap:0 40px}
.bkh .bkh-dr-list li.is-best,.bkh .bkh-dr-list li:only-child{grid-column:1/-1}
.bkh .bkh-dr-list li.is-best{padding:18px 0 8px}
.bkh .bkh-dr-item{display:grid;grid-template-columns:minmax(0,1fr) auto;align-items:center;gap:3px 18px;height:100%;padding:14px 0;border-bottom:1px solid var(--line);color:var(--ink)}
.bkh .bkh-dr-name{grid-column:1;font:600 15px/1.35 var(--sans);color:var(--navy);transition:color .2s}
.bkh .bkh-dr-item:hover .bkh-dr-name{color:var(--brass-d)}
.bkh .bkh-dr-meta{grid-column:1;font:600 9.5px/1.4 var(--sans);letter-spacing:.16em;text-transform:uppercase;color:var(--slate)}
.bkh .bkh-dr-price{grid-column:2;grid-row:1/span 2;font:500 17px/1.1 var(--display);font-variation-settings:"opsz" 24;color:var(--navy);white-space:nowrap}
.bkh .bkh-dr-item.is-best{padding:20px 24px;border:1px solid var(--line);border-left:2px solid var(--brass);background:var(--stone)}
.bkh .bkh-dr-tag{grid-column:1/-1;margin-bottom:4px;font:600 9.5px/1.3 var(--sans);letter-spacing:.22em;text-transform:uppercase;color:#7F6129}
.bkh .bkh-dr-item.is-best .bkh-dr-name{font:500 clamp(25px,2.6vw,34px)/1.08 var(--display);letter-spacing:-.01em}
.bkh .bkh-dr-item.is-best .bkh-dr-price{grid-row:2/span 2;font-size:clamp(19px,1.8vw,24px)}
.bkh .bkh-dr-cta{grid-column:1;display:inline-flex;align-items:center;gap:8px;margin-top:10px;font:600 10.5px/1 var(--sans);letter-spacing:.18em;text-transform:uppercase;color:#7F6129}
.bkh .bkh-dr-cta svg{width:14px;height:14px;transition:transform .25s}
.bkh .bkh-dr-item.is-best:hover .bkh-dr-cta svg{transform:translateX(4px)}
.bkh .bkh-dr-foot{display:flex;flex-wrap:wrap;align-items:center;gap:14px 30px;margin-top:22px}
.bkh .bkh-dr-wa{display:inline-flex;align-items:center;gap:8px;font:600 11px/1 var(--sans);letter-spacing:.16em;text-transform:uppercase;color:var(--navy)}
.bkh .bkh-dr-wa svg{width:16px;height:16px;color:var(--wa)}
.bkh .bkh-dr-alt{font:500 13px/1.4 var(--sans);color:var(--slate);border-bottom:1px solid var(--line-2)}
.bkh .bkh-dr-alt:hover,.bkh .bkh-dr-wa:hover{color:var(--brass-d)}
@media (max-width:767px){
.bkh .bkh-ds-field{padding:10px 14px 9px 22px;min-height:60px}
.bkh .bkh-ds input[type="search"]{font-size:16px}
.bkh .bkh-ds-go{min-height:60px;padding:0 19px}
.bkh .bkh-ds-go span{position:absolute;width:1px;height:1px;overflow:hidden;clip:rect(0 0 0 0);white-space:nowrap}
.bkh .bkh-ds-go svg{width:18px;height:18px}
.bkh .bkh-dl li{padding:12px 16px}
.bkh .bkh-dr{padding:20px 18px 18px}
.bkh .bkh-dr-list{grid-template-columns:1fr}
.bkh .bkh-dr-item.is-best{padding:18px 16px}
.bkh .bkh-dr-foot{gap:14px 22px}
}
@media (max-width:480px){
.bkh .bkh-dr-item:not(.is-best) .bkh-dr-name,.bkh .bkh-dl-name{grid-column:1/-1}
.bkh .bkh-dr-item:not(.is-best) .bkh-dr-price,.bkh .bkh-dl-price{grid-row:2;align-self:center;font-size:15px}
.bkh .bkh-dr-item.is-best{grid-template-columns:minmax(0,1fr)}
.bkh .bkh-dr-item.is-best .bkh-dr-price{grid-column:1;grid-row:auto;margin-top:6px}
}
/* proof band */
.bkh .bkh-strip{background:var(--navy-deep);color:#fff;border-top:1px solid rgba(255,255,255,.08);padding:clamp(30px,3.6vw,46px) 0}
.bkh .bkh-stats{display:grid;grid-template-columns:repeat(4,1fr);gap:24px}
.bkh .bkh-stat{padding-left:22px;border-left:1px solid rgba(255,255,255,.16)}
.bkh .bkh-stat b{display:block;font:500 clamp(30px,3.1vw,44px)/1 var(--display);color:#fff;letter-spacing:-.01em;font-variant-numeric:lining-nums tabular-nums}
.bkh .bkh-stat span{display:block;margin-top:10px;font:600 10px/1.4 var(--sans);letter-spacing:.2em;text-transform:uppercase;color:rgba(255,255,255,.55)}
/* mandate spotlight + developments */
.bkh .bkh-feat{display:grid;grid-template-columns:6.5fr 5.5fr;gap:48px clamp(40px,5vw,80px);align-items:center}
.bkh .bkh-feat-media{aspect-ratio:5/4}
.bkh .bkh-feat .bkh-ctas .bkh-btn{padding:0 24px}
.bkh .bkh-feat h2.bkh-display{margin-top:16px}
.bkh .bkh-feat-meta{display:grid;grid-template-columns:1fr 1fr;gap:0 28px;margin-top:28px;border-top:1px solid var(--line)}
.bkh .bkh-feat-meta div{padding:14px 0 12px;border-bottom:1px solid var(--line)}
.bkh .bkh-feat-meta span{display:block;font:600 9.5px/1.3 var(--sans);letter-spacing:.2em;text-transform:uppercase;color:var(--muted)}
.bkh .bkh-feat-meta b{display:block;margin-top:5px;font:500 15px/1.3 var(--sans);color:var(--navy)}
.bkh .bkh-feat-meta b.bkh-num{font:500 24px/1.1 var(--display);letter-spacing:-.01em}
.bkh .bkh-feat p.bkh-lede{margin-top:22px;font-size:15.5px}
.bkh .bkh-subhead{display:flex;align-items:center;justify-content:space-between;gap:24px;flex-wrap:wrap;margin:clamp(56px,7vw,96px) 0 28px;padding-top:26px;border-top:1px solid var(--line)}
.bkh .bkh-subhead h3{font:600 11px/1.4 var(--sans)!important;letter-spacing:.22em!important;text-transform:uppercase!important;color:var(--muted)!important}
.bkh .bkh-devs{display:grid;grid-template-columns:repeat(3,1fr);gap:32px}
.bkh .bkh-dev{display:block;color:var(--ink)}
.bkh .bkh-dev .bkh-media{aspect-ratio:4/3}
.bkh .bkh-dev-body{padding-top:18px}
.bkh .bkh-dev-meta{font:600 10.5px/1.4 var(--sans);letter-spacing:.14em;color:var(--muted);text-transform:uppercase}
.bkh .bkh-dev h3{font:600 17px/1.3 var(--sans)!important;color:var(--navy)!important;margin-top:8px!important}
.bkh .bkh-dev-price{display:flex;align-items:baseline;justify-content:space-between;gap:12px;margin-top:12px;padding-top:12px;border-top:1px solid var(--line)}
.bkh .bkh-dev-price b{font:500 23px/1 var(--display);color:var(--navy);letter-spacing:-.01em}
.bkh .bkh-dev-price span{font:500 12.5px/1.3 var(--sans);color:var(--slate)}
.bkh .bkh-dev-cta{display:inline-flex;align-items:center;gap:8px;margin-top:14px;font:600 10.5px/1 var(--sans);letter-spacing:.18em;text-transform:uppercase;color:var(--brass-d)}
.bkh .bkh-dev:hover .bkh-dev-cta{color:var(--navy)}
/* listings */
.bkh .bkh-ltabs{display:flex;gap:32px;border-bottom:1px solid var(--line-2);margin-bottom:32px}
.bkh .bkh-ltab{appearance:none;border:0;border-radius:0;background:transparent;padding:0 0 14px;margin:0 0 -1px;font:600 11px/1 var(--sans);letter-spacing:.2em;text-transform:uppercase;color:var(--muted);border-bottom:2px solid transparent;cursor:pointer;box-shadow:none}
.bkh .bkh-ltab[aria-selected="true"]{color:var(--navy);border-color:var(--brass)}
.bkh .bkh-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:40px 28px}
.bkh .bkh-grid[hidden]{display:none}
.bkh .bkh-card{display:block;color:var(--ink)}
.bkh .bkh-card .bkh-media{aspect-ratio:3/2}
.bkh .bkh-card-body{padding-top:16px}
.bkh .bkh-card h3{font:600 15px/1.4 var(--sans)!important;color:var(--navy)!important;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden}
.bkh .bkh-card-price{font:500 24px/1.1 var(--display);color:var(--navy);margin-top:8px;letter-spacing:-.01em}
.bkh .bkh-card-loc{font:500 12.5px/1.4 var(--sans);color:var(--muted);margin-top:5px}
.bkh .bkh-specs{display:flex;gap:16px;margin-top:12px;padding-top:12px;border-top:1px solid var(--line);font:500 11px/1 var(--sans);letter-spacing:.1em;text-transform:uppercase;color:var(--slate)}
.bkh .bkh-specs span b{font-weight:700;color:var(--navy)}
.bkh .bkh-lfoot{display:flex;gap:16px;flex-wrap:wrap;justify-content:center;margin-top:52px}
/* diaspora */
.bkh .bkh-dia-grid{display:grid;grid-template-columns:1.05fr 1fr;gap:48px clamp(48px,7vw,110px);align-items:center}
.bkh .bkh-cities{margin-top:34px;font:600 10.5px/1.7 var(--sans);letter-spacing:.22em;text-transform:uppercase;color:rgba(255,255,255,.55)}
.bkh .bkh-cities b{display:block;margin-top:6px;font:400 14.5px/1.5 var(--sans);letter-spacing:0;text-transform:none;color:var(--on-navy)}
.bkh .bkh-steps{display:grid;border-top:1px solid rgba(255,255,255,.14)}
.bkh .bkh-step{display:grid;grid-template-columns:64px 1fr;gap:12px;padding:26px 0;border-bottom:1px solid rgba(255,255,255,.14)}
.bkh .bkh-step i{font:400 30px/1 var(--display);font-style:normal;color:var(--brass-l);letter-spacing:-.02em;padding-top:2px}
.bkh .bkh-step h3{font:600 16px/1.4 var(--sans)!important;color:#fff!important}
.bkh .bkh-step p{font:400 14.5px/1.6 var(--sans);color:var(--on-navy);margin-top:6px}
/* areas */
.bkh .bkh-areas{display:grid;grid-template-columns:repeat(4,1fr);grid-template-rows:repeat(2,minmax(220px,24vw));gap:16px}
.bkh .bkh-area{position:relative;display:block;overflow:hidden;border-radius:var(--r);background:var(--navy);color:#fff;min-width:0}
.bkh .bkh-area--lead{grid-column:span 2;grid-row:span 2}
.bkh .bkh-area img,.bkh .bkh-area .bkh-noimg{position:absolute;inset:0;width:100%;height:100%;object-fit:cover;transition:transform 1.1s cubic-bezier(.2,.7,.2,1)}
.bkh .bkh-area:hover img{transform:scale(1.04)}
.bkh .bkh-area::after{content:"";position:absolute;inset:0;background:linear-gradient(180deg,rgba(7,23,44,0) 40%,rgba(7,23,44,.88) 100%)}
.bkh .bkh-area div{position:absolute;left:22px;right:22px;bottom:22px;z-index:1}
.bkh .bkh-area h3{font:500 28px/1.05 var(--display)!important;color:#fff!important;letter-spacing:-.01em!important}
.bkh .bkh-area--lead h3{font-size:clamp(34px,3.4vw,46px)!important}
.bkh .bkh-area span{display:block;margin-top:8px;font:600 10px/1.3 var(--sans);letter-spacing:.2em;text-transform:uppercase;color:rgba(255,255,255,.7)}
/* founder */
.bkh .bkh-founder{display:grid;grid-template-columns:.95fr 1.05fr;gap:48px clamp(48px,6vw,96px);align-items:center}
.bkh .bkh-portrait{position:relative;margin:16px 0 0 16px}
.bkh .bkh-portrait::before{content:"";position:absolute;top:-16px;left:-16px;right:16px;bottom:16px;border:1px solid var(--brass);border-radius:var(--r);pointer-events:none}
.bkh .bkh-portrait .bkh-media{aspect-ratio:4/5;background:#0b0b0b;box-shadow:0 30px 70px rgba(11,31,58,.18)}
.bkh .bkh-portrait .bkh-media img{object-position:center top}
.bkh .bkh-portrait figcaption{position:absolute;left:22px;bottom:20px;z-index:1;color:#fff;font:600 10px/1.4 var(--sans);letter-spacing:.2em;text-transform:uppercase;opacity:.9}
.bkh .bkh-quote{font:400 clamp(21px,1.8vw,26px)/1.4 var(--display);font-family:var(--display)!important;font-style:italic!important;color:var(--navy);margin-top:24px;max-width:30ch}
.bkh .bkh-body{font-size:15.5px;line-height:1.75;color:var(--slate);margin-top:20px;max-width:58ch}
.bkh .bkh-sign{margin-top:24px;font:600 11px/1.5 var(--sans);letter-spacing:.2em;text-transform:uppercase;color:var(--navy)}
.bkh .bkh-sign small{display:block;font:500 10.5px/1.5 var(--sans);letter-spacing:.14em;color:var(--muted)}
.bkh .bkh-team{display:flex;align-items:center;gap:16px;margin-top:36px;padding-top:26px;border-top:1px solid var(--line-2)}
.bkh .bkh-avatars{display:flex}
.bkh .bkh-avatars img,.bkh .bkh-avatars .bkh-noimg{width:44px;height:44px;border-radius:50%;object-fit:cover;border:2px solid var(--stone);margin-left:-10px;box-shadow:0 2px 8px rgba(0,0,0,.12)}
.bkh .bkh-avatars :first-child{margin-left:0}
.bkh .bkh-team p{font:500 13px/1.5 var(--sans);color:var(--slate)}
.bkh .bkh-team p a{color:var(--navy);font-weight:600;border-bottom:1px solid var(--brass)}
/* services */
.bkh .bkh-services{display:grid;grid-template-columns:1fr 1fr;gap:0 clamp(40px,5vw,80px);border-bottom:1px solid var(--line-2)}
.bkh .bkh-service{display:grid;grid-template-columns:1fr auto;align-items:start;gap:6px 20px;padding:26px 0;border-top:1px solid var(--line-2);color:var(--ink)}
.bkh .bkh-service h3{font:600 16.5px/1.3 var(--sans)!important;color:var(--navy)!important}
.bkh .bkh-service p{grid-column:1;font:400 14.5px/1.6 var(--sans);color:var(--slate)}
.bkh .bkh-service svg{grid-column:2;grid-row:1/span 2;align-self:center;width:18px;height:18px;color:var(--brass);transition:transform .25s}
.bkh .bkh-service:hover svg{transform:translateX(5px)}
.bkh .bkh-val{display:flex;align-items:center;justify-content:space-between;gap:24px 40px;flex-wrap:wrap;margin-top:clamp(40px,5vw,64px);padding:30px 36px;border-radius:var(--r);background:var(--navy);color:#fff}
.bkh .bkh-val h3{font:500 clamp(22px,2vw,28px)/1.2 var(--display)!important;color:#fff!important}
.bkh .bkh-val p{font:400 14.5px/1.5 var(--sans);color:var(--on-navy);margin-top:6px}
/* homework */
.bkh .bkh-hw{display:grid;grid-template-columns:1.3fr 1fr;gap:40px clamp(40px,5vw,72px);align-items:start}
.bkh .bkh-lead{display:block;color:var(--ink)}
.bkh .bkh-lead .bkh-media{aspect-ratio:16/9}
.bkh .bkh-cat{display:inline-block;font:600 10px/1.3 var(--sans);letter-spacing:.2em;text-transform:uppercase;color:var(--brass-d)}
.bkh .bkh-lead .bkh-cat{margin-top:20px}
.bkh .bkh-lead h3{font:500 clamp(24px,2.2vw,32px)/1.18 var(--display)!important;color:var(--navy)!important;margin-top:10px!important}
.bkh .bkh-lead p{font:400 15px/1.65 var(--sans);color:var(--slate);margin-top:12px;max-width:60ch}
.bkh .bkh-lead time{display:block;margin-top:12px;font:500 11.5px/1.4 var(--sans);letter-spacing:.06em;color:var(--muted)}
.bkh .bkh-list{display:grid}
.bkh .bkh-item{display:grid;grid-template-columns:116px 1fr;gap:20px;align-items:center;padding:20px 0;border-top:1px solid var(--line);color:var(--ink)}
.bkh .bkh-item:last-child{border-bottom:1px solid var(--line)}
.bkh .bkh-item .bkh-media{aspect-ratio:4/3}
.bkh .bkh-item h3{font:600 15px/1.4 var(--sans)!important;color:var(--navy)!important;margin-top:5px!important;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden}
.bkh .bkh-item:hover h3{color:var(--brass-d)!important}
.bkh .bkh-item time{display:block;margin-top:5px;font:500 11px/1.4 var(--sans);letter-spacing:.06em;color:var(--muted)}
/* closing */
.bkh .bkh-close{text-align:center;position:relative;overflow:hidden}
.bkh .bkh-close::before{content:"";position:absolute;left:50%;top:-40%;width:80vw;max-width:1000px;aspect-ratio:1;transform:translateX(-50%);border-radius:50%;background:radial-gradient(closest-side,rgba(19,41,75,.95),rgba(7,23,44,0));pointer-events:none}
.bkh .bkh-close .bkh-wrap{position:relative;max-width:880px}
.bkh .bkh-close .bkh-eyebrow::before{display:none}
.bkh .bkh-close h2.bkh-display{font-size:clamp(40px,5.4vw,72px)!important;margin-top:18px}
.bkh .bkh-close .bkh-lede{margin:20px auto 0}
.bkh .bkh-close .bkh-ctas{justify-content:center;margin-top:36px}
.bkh .bkh-close .bkh-alt{margin-top:24px;font:500 13px/1.5 var(--sans);color:var(--on-navy)}
.bkh .bkh-close .bkh-alt a{color:#fff;border-bottom:1px solid rgba(255,255,255,.35)}
/* sticky bar */
.bkh-bar{position:fixed;left:0;right:0;bottom:0;z-index:9990;display:none;grid-template-columns:1fr 1fr;gap:8px;padding:8px 10px calc(8px + env(safe-area-inset-bottom));background:rgba(255,255,255,.96);backdrop-filter:blur(10px);-webkit-backdrop-filter:blur(10px);border-top:1px solid #E3DFD8;transform:translateY(110%);transition:transform .3s}
.bkh-bar.is-on{transform:none}
.bkh-bar a{display:flex;align-items:center;justify-content:center;gap:9px;min-height:48px;border-radius:2px;font:600 11.5px/1 "Montserrat",system-ui,sans-serif;letter-spacing:.16em;text-transform:uppercase;text-decoration:none}
.bkh-bar a svg{width:18px;height:18px}
.bkh-bar .bkh-bar-wa{background:#1EBE5D;color:#fff}
.bkh-bar .bkh-bar-call{background:#0B1F3A;color:#fff}
body.bkh-bar-on #blkac,body.bkh-bar-on .mwai-chatbot-container{transform:translateY(-62px)}
@media (max-width:767px){body.home #zsiq_float{top:auto!important;bottom:140px!important}}
/* responsive */
@media (max-width:1100px){
.bkh .bkh-feat{grid-template-columns:1fr;gap:36px}
.bkh .bkh-feat-media{aspect-ratio:16/10}
.bkh .bkh-devs{gap:20px}
.bkh .bkh-areas{grid-template-columns:repeat(3,1fr);grid-template-rows:repeat(2,minmax(200px,30vw))}
.bkh .bkh-services{gap:0 40px}
}
@media (max-width:900px){
.bkh .bkh-form,.bkh .bkh-form.is-offplan{grid-template-columns:1fr 1fr}
.bkh .bkh-field{border-bottom:1px solid var(--line);border-right:0}
.bkh .bkh-field--where{grid-column:1/-1}
.bkh .bkh-field--type{border-right:1px solid var(--line)}
.bkh .bkh-field--budget{grid-column:1/-1}
.bkh .bkh-form.is-offplan .bkh-field--budget{grid-column:auto;border-right:1px solid var(--line)}
.bkh .bkh-submit{grid-column:1/-1;min-height:58px}
.bkh .bkh-stats{grid-template-columns:repeat(2,1fr);gap:26px 16px}
.bkh .bkh-dia-grid,.bkh .bkh-founder,.bkh .bkh-hw{grid-template-columns:1fr}
.bkh .bkh-founder{gap:40px}
.bkh .bkh-portrait{max-width:460px}
.bkh .bkh-grid{grid-template-columns:repeat(2,1fr);gap:28px 18px}
.bkh .bkh-services{grid-template-columns:1fr}
}
@media (max-width:767px){
.bkh{font-size:15px}
.bkh .bkh-hero{min-height:0;flex-direction:column;justify-content:flex-end}
.bkh .bkh-hero .bkh-wrap{padding-top:120px;padding-bottom:30px}
.bkh .bkh-hero h1.bkh-display{font-size:clamp(36px,10vw,48px)!important;max-width:none;margin-top:16px}
.bkh .bkh-hero-sub{font-size:15.5px;margin-top:16px}
.bkh .bkh-hero .bkh-eyebrow{font-size:10px;letter-spacing:.18em}
.bkh .bkh-hero-quick{display:none}
.bkh .bkh-hero-cap{position:static;display:block;width:100%;padding:0 var(--gutter) 12px;margin-top:-12px;text-align:right}
.bkh .bkh-hero-img{object-position:60% 40%}
.bkh .bkh-search{margin-top:26px}
.bkh .bkh-tabs{gap:0}
.bkh .bkh-tab{flex:1;text-align:center;padding:4px 0 12px;font-size:10.5px;letter-spacing:.14em}
.bkh .bkh-strip{padding:26px 0}
.bkh .bkh-head{margin-bottom:26px}
.bkh .bkh-head .bkh-more{order:3}
.bkh .bkh-feat-meta{gap:0 16px}
.bkh .bkh-subhead{margin:48px 0 20px}
.bkh .bkh-devs{display:flex;gap:14px;overflow-x:auto;scroll-snap-type:x mandatory;padding:0 var(--gutter) 10px;margin:0 calc(-1*var(--gutter));scrollbar-width:none}
.bkh .bkh-devs::-webkit-scrollbar{display:none}
.bkh .bkh-dev{flex:0 0 80%;scroll-snap-align:start}
.bkh .bkh-grid{grid-template-columns:1fr;gap:26px}
.bkh .bkh-grid .bkh-card:nth-child(n+5){display:none}
.bkh .bkh-areas{grid-template-columns:repeat(2,1fr);grid-template-rows:none;grid-auto-rows:minmax(150px,42vw);gap:10px}
.bkh .bkh-area--lead{grid-column:span 2;grid-row:auto}
.bkh .bkh-area div{left:14px;right:14px;bottom:14px}
.bkh .bkh-area h3{font-size:22px!important}
.bkh .bkh-area--lead h3{font-size:30px!important}
.bkh .bkh-step{grid-template-columns:48px 1fr;padding:20px 0}
.bkh .bkh-step i{font-size:24px}
.bkh .bkh-portrait{margin:12px 0 0 12px}
.bkh .bkh-portrait::before{top:-12px;left:-12px;right:12px;bottom:12px}
.bkh .bkh-item{grid-template-columns:96px 1fr;gap:14px}
.bkh .bkh-val{padding:24px}
.bkh .bkh-ctas .bkh-btn{width:100%}
.bkh-bar{display:grid}
body.bkh-bar-on{padding-bottom:64px}
}
@media (prefers-reduced-motion:reduce){.bkh *{transition:none!important;animation:none!important}.bkh .bkh-hero-img{transform:none}.bkh .bkh-rv{transform:none}}
CSS;
	}

	/* ------------------------------------------------------------------ JS */

	function blockke_hm3_js() {
		return <<<'JS'
(function(){
  var root=document.querySelector('.bkh');if(!root){return;}
  var C=window.BLOCKHOME||{};
  /* the theme styles the transparent header for body.home; keep that true on previews of this layout too */
  try{document.body.classList.add('home');}catch(e){}
  /* search */
  var form=root.querySelector('.bkh-form'),tabs=root.querySelectorAll('.bkh-tab'),action=root.querySelector('[name="property_action_category[]"]'),comp=root.querySelector('[name="completion"]'),budget=root.querySelector('[name="price_max"]'),beds=root.querySelector('[name="bedrooms"]'),cat=root.querySelector('[name="property_category[]"]');
  var saleBudget=[['','Any budget'],['5000000','Under KES 5M'],['8000000','Under KES 8M'],['12000000','Under KES 12M'],['20000000','Under KES 20M'],['35000000','Under KES 35M'],['60000000','Under KES 60M']];
  var rentBudget=[['','Any budget'],['50000','Under KES 50K a month'],['80000','Under KES 80K a month'],['120000','Under KES 120K a month'],['200000','Under KES 200K a month'],['350000','Under KES 350K a month']];
  function fill(sel,list){var cur=sel.value;sel.innerHTML='';list.forEach(function(o){var op=document.createElement('option');op.value=o[0];op.textContent=o[1];sel.appendChild(op);});sel.value=cur;if(sel.selectedIndex<0){sel.selectedIndex=0;}}
  function setMode(m){
    if(!form){return;}
    form.classList.toggle('is-rent',m==='rent');form.classList.toggle('is-offplan',m==='offplan');
    for(var i=0;i<tabs.length;i++){tabs[i].setAttribute('aria-selected',tabs[i].getAttribute('data-mode')===m?'true':'false');}
    if(action){action.value=(m==='rent')?'for-rent':'for-sale';}
    if(budget){fill(budget,m==='rent'?rentBudget:saleBudget);}
    if(comp){if(m==='offplan'){if(!comp.value){comp.value='any';}}else{comp.selectedIndex=-1;}}
    form.setAttribute('data-mode',m);
  }
  for(var i=0;i<tabs.length;i++){tabs[i].addEventListener('click',function(e){e.preventDefault();setMode(this.getAttribute('data-mode'));});}
  if(form){
    setMode(form.getAttribute('data-mode')||'buy');
    form.addEventListener('submit',function(){
      var extra=null;
      if(comp){if(form.getAttribute('data-mode')==='offplan'){if(!comp.value){comp.value='any';}}else{comp.selectedIndex=-1;}}
      if(beds&&beds.value==='studio'){beds.value='';if(cat&&!cat.value){cat.value='studio-apartments';}else{extra=document.createElement('input');extra.type='hidden';extra.name='property_category[]';extra.value='studio-apartments';form.appendChild(extra);}}
      var els=form.querySelectorAll('select,input');
      for(var j=0;j<els.length;j++){if(els[j].name&&els[j].value===''){els[j].disabled=true;}}
      setTimeout(function(){for(var k=0;k<els.length;k++){els[k].disabled=false;}if(extra&&extra.parentNode){extra.parentNode.removeChild(extra);}},800);
      try{if(typeof window.gtag==='function'){window.gtag('event','search',{search_term:(form.getAttribute('data-mode')||'buy')+' '+(root.querySelector('[name="property_area[]"]')||{value:''}).value,placement:'home_hero'});}}catch(x){}
    });
  }
  /* development-name search (v3.1): a typeahead over C.dv, ARIA 1.2 combobox + listbox; matching = blockke_hm3_dev_find() */
  (function(){
    var ds=root.querySelector('.bkh-ds'),inp=ds&&ds.querySelector('input[name="bkdev"]'),dv=C.dv;
    if(!ds||!inp||!dv||!dv.d||!dv.d.length){return;}
    var D=dv.d,FP=dv.f||{},fy=ds.querySelector('input[name="bkfor"]'),FILL={apartment:1,apartments:1,residence:1,residences:1,residency:1,residencies:1,homes:1,estate:1,the:1,at:1};
    function fold(s){s=String(s==null?'':s);try{s=s.normalize('NFD').replace(/[\u0300-\u036f]/g,'');}catch(e){}s=s.toLowerCase().replace(/\u00df/g,'ss').replace(/\u00e6/g,'ae').replace(/\u0153/g,'oe').replace(/\u00f8/g,'o').replace(/&/g,' and ').replace(/['\u2019`]/g,'');var w=s.replace(/[^a-z0-9]+/g,' ').replace(/^ | $/g,'').split(' '),i;for(i=0;i<w.length-1;i++){if(w[i]==='n'){w[i]='and';}}return w.join(' ');}
    function prep(s){var f=fold(s),w=f?f.split(' '):[],k=[],i;for(i=0;i<w.length;i++){if(!FILL[w[i]]){k.push(w[i]);}}var o=w.length>0&&!k.length;if(!k.length){k=w;}return {w:w,k:k,c:k.join(''),f:w.join(''),o:o};}
    /* edits between two words, a swap of two neighbouring letters counting as one (optimal string alignment) = blockke_hm3_dev_osa() */
    function osa(a,b){if(a===b){return 0;}var m=a.length,n=b.length,pp=[],p=[],c,i,j;if(!m||!n){return Math.max(m,n);}for(j=0;j<=n;j++){p[j]=j;}for(i=1;i<=m;i++){c=[i];for(j=1;j<=n;j++){c[j]=Math.min(p[j]+1,c[j-1]+1,p[j-1]+(a.charAt(i-1)===b.charAt(j-1)?0:1));if(i>1&&j>1&&a.charAt(i-1)===b.charAt(j-2)&&a.charAt(i-2)===b.charAt(j-1)){c[j]=Math.min(c[j],pp[j-2]+1);}}pp=p;p=c;}return p[n];}
    /* the same first letter, c = k (= blockke_hm3_dev_lead()); a whole-word slip (= blockke_hm3_dev_slip()); a near miss (= blockke_hm3_dev_near()) */
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
    var P=[],PL=[],AREA=null,NOTAREA={road:1,city:1,town:1,general:1,river:1,rivers:1,spring:1,springs:1,valley:1,south:1,north:1,east:1,west:1,upper:1,lower:1,garden:1,gardens:1,plains:1,park:1,hill:1,hills:1,view:1,views:1,estate:1,beach:1,heights:1,ridge:1,gate:1,green:1,court:1,place:1,square:1},n,a;
    for(n=0;n<D.length;n++){P[n]=[prep(D[n][0])];for(a=0;a<(D[n][7]||[]).length;a++){P[n].push(prep(D[n][7][a]));}PL[n]=prep(D[n][1]||'').w;}
    function all(q,i){var s=score(q,P[i][0]),x;for(x=1;x<P[i].length;x++){s=Math.max(s,score(q,P[i][x])-1);}return s;}
    /* area words (= blockke_hm3_dev_areas()) and the query without them (= blockke_hm3_dev_unplace()) */
    function areas(){if(AREA){return AREA;}var seen={},i,j,u;AREA=[];for(i=0;i<PL.length;i++){for(j=0;j<PL[i].length;j++){u=PL[i][j];if(u.length>=4&&!NOTAREA[u]&&!seen[u]){seen[u]=1;AREA.push(u);}}}if(!seen.nairobi){AREA.push('nairobi');}if(!seen.kenya){AREA.push('kenya');}return AREA;}
    function unplace(q,pl){
      var n=q.w.length,keep=[],i,j,t,u,hit,r;if(!pl.length||n<2){return null;}
      for(i=0;i<n;i++){t=q.w[i];hit=false;for(j=0;j<pl.length&&!hit;j++){u=pl[j];hit=u===t||(i===n-1&&u.indexOf(t)===0)||(t.length>=5&&slip(t,u));}if(!hit){keep.push(t);}}
      if(!keep.length||keep.length===n){return null;}
      r=prep(keep.join(' '));return r.o?null:r;
    }
    function pref(i,mode){var b=D[i][5]||0;return mode==='rent'?((b&2)?2:0):((b&1)?2:0)+(mode==='offplan'&&(b&4)?1:0);}
    function fp(i){var v=FP[i];return typeof v==='number'?v:999;}
    function find(text,mode){
      var q=prep(text),r=[],sc=[],top=0,i,s,q1,q2,lo;if(!q.c){return r;}
      for(i=0;i<D.length;i++){s=all(q,i);q1=unplace(q,PL[i]);if(q1){s=Math.max(s,all(q1,i)-1);}sc[i]=s;if(s>top){top=s;}}
      q2=top<35?unplace(q,areas()):null;
      if(q2){for(i=0;i<D.length;i++){sc[i]=Math.max(sc[i],all(q2,i)-2);if(sc[i]>top){top=sc[i];}}}
      lo=top<35?1:top>=50?50:35;
      for(i=0;i<D.length;i++){if(sc[i]>=lo){r.push([i,sc[i]]);}}
      r.sort(function(x,y){return (y[1]-x[1])||(pref(y[0],mode)-pref(x[0],mode))||(fp(x[0])-fp(y[0]))||(P[x[0]][0].c.length-P[y[0]][0].c.length)||(x[0]-y[0]);});
      return r;
    }
    /* the one development a search clearly means, or -1 (= blockke_hm3_dev_strong()): a single near miss counts when the name typed has 5 letters or more and each word of it starts a word of the name or is a whole-word slip of one */
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
    /* what the server keeps of the text typed (= blockke_hm3_dev_query()): tags, web and e-mail addresses, @handles and every digit
       once there are 5 or more go, at most 80 characters. GA4 and the address only ever get this, never the text as typed */
    var RX=null;
    function clean(s){
      var w,keep=[],dot=false,i,t,tld,cp;
      s=String(s==null?'':s);
      if(s.indexOf('<')>=0){s=s.replace(/<[^>]*?(?=<|>|$)>?/g,function(m){return m.indexOf('>')>=0?m:m.replace(/&(?![a-z][a-z0-9]*;|#[0-9]+;|#x[0-9a-f]+;)/gi,'&amp;').replace(/</g,'&lt;').replace(/"/g,'&quot;').replace(/'/g,'&#039;');}).replace(/<(script|style)[^>]*?>[\s\S]*?<\/\1>/gi,'').replace(/<(?!\s)[^>]*>/g,'');}
      s=s.replace(/[\r\n\t ]+/g,' ');while(/%[a-f0-9]{2}/i.test(s)){s=s.replace(/%[a-f0-9]{2}/gi,'');}
      if(!RX){try{RX=[new RegExp('[^\\p{L}\\p{M}0-9\\s&\'\\u2019.,@()\\-]+','gu'),new RegExp('\\.[\\p{L}\\p{M}0-9]{2}|^(?:https?|www)$|^[\\-.,()\'\\u2019]+$','iu'),new RegExp('^[\\p{L}\\p{M}]+@[\\p{L}\\p{M}]+$','u')];}catch(x){RX=[/[^A-Za-z\u00c0-\u024f\u0300-\u036f0-9\s&'\u2019.,@()\-]+/g,/\.[A-Za-z\u00c0-\u024f0-9]{2}|^(?:https?|www)$|^[\-.,()'\u2019]+$/i,/^[A-Za-z\u00c0-\u024f]+@[A-Za-z\u00c0-\u024f]+$/];}}
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
      return {url:link(use&&e[6]?e[6]:e[2]),price:price||'Prices on request',meta:meta.join(' \u00b7 ')};
    }
    function getMode(){var f=root.querySelector('.bkh-form');return (f&&f.getAttribute('data-mode'))||'buy';}
    /* the listbox lives in .bkh itself (the hero clips what overflows it) and is placed under the search, or over it when there is more room above */
    var pop=document.createElement('div'),lb=document.createElement('ul'),msg=document.createElement('p'),live=document.createElement('div'),tl=root.querySelector('.bkh-tabs');
    pop.className='bkh-dl';pop.hidden=true;lb.id='bkh-ds-lb';lb.setAttribute('role','listbox');lb.setAttribute('aria-label','Developments');
    msg.className='bkh-dl-msg';msg.hidden=true;pop.appendChild(lb);pop.appendChild(msg);root.appendChild(pop);
    live.className='bkh-sr';live.setAttribute('role','status');live.setAttribute('aria-live','polite');ds.appendChild(live);
    inp.removeAttribute('list');
    inp.setAttribute('role','combobox');inp.setAttribute('aria-autocomplete','list');inp.setAttribute('aria-expanded','false');inp.setAttribute('aria-controls',lb.id);
    /* the short placeholder fits a phone with or without the script; wider screens get the long one */
    var phs=inp.getAttribute('placeholder')||'',phl=inp.getAttribute('data-long')||'',mq=window.matchMedia?window.matchMedia('(max-width:600px)'):null;
    function fitPh(){if(phl&&mq){inp.setAttribute('placeholder',mq.matches?phs:phl);}}
    fitPh();
    /* from 901px Find is exactly as wide as v3.0.2's Search above it (CSS gives it 157px; this follows the fonts the page really has) */
    var sub=root.querySelector('.bkh-submit'),go=ds.querySelector('.bkh-ds-go'),wq=window.matchMedia?window.matchMedia('(min-width:901px)'):null;
    function fitGo(){if(!sub||!go||!wq){return;}var w=wq.matches?sub.getBoundingClientRect().width:0;go.style.minWidth=w>0?w+'px':'';}
    fitGo();try{document.fonts.ready.then(fitGo);}catch(x){}
    var opts=[],act=-1,open=false,down=false,said='',sent='',tmr=0;
    function say(t){if(t===said){return;}said=t;clearTimeout(tmr);tmr=setTimeout(function(){live.textContent=t;},350);}
    function track(q){if(!q||q===sent){return;}sent=q;try{if(typeof window.gtag==='function'){window.gtag('event','search',{search_term:q,placement:'home_dev'});}}catch(x){}}
    /* opened upwards, the list stops under the Buy | Rent | Off-plan tabs while they are on screen, so they can still be clicked;
       when not even 200px fit either way (laptop and phone screens), the list scrolls the page up just enough to open below,
       keeping the tabs on screen and the strip clear of a header */
    function place(fit){
      if(pop.hidden){return;}
      var vv=window.visualViewport,vt=vv?vv.offsetTop:0,vh=vv?vv.height:(window.innerHeight||document.documentElement.clientHeight),bar=document.body.classList.contains('bkh-bar-on')?64:0;
      var r,tb,on,below,above,need,want,d;
      function room(){r=ds.getBoundingClientRect();tb=tl?tl.getBoundingClientRect():null;on=!!(tb&&tb.height&&tb.bottom>vt&&tb.top<vt+vh);below=vt+vh-r.bottom-10-bar;above=on?r.top-tb.bottom-8:r.top-vt-10;}
      pop.style.maxHeight='none';pop.style.minHeight='';
      need=Math.min(pop.scrollHeight,460);want=Math.min(need,280);room();
      if(fit&&below<Math.min(need,200)&&above<Math.min(need,200)){
        d=Math.floor(Math.min(want-below,r.top-vt-96,(on&&tb.top>=vt+8)?tb.top-vt-8:1e4));
        if(d>8){try{window.scrollBy({top:d,left:0,behavior:'instant'});}catch(x){window.scrollBy(0,d);}room();}
      }
      var up=below<want&&above>below,rm=up?Math.max(64,Math.min(460,Math.floor(above))):Math.max(150,Math.min(460,Math.floor(below)));
      pop.style.maxHeight=rm+'px';
      var h=Math.min(pop.scrollHeight+2,rm),rr=root.getBoundingClientRect(),top=Math.round(up?(r.top-rr.top-h-4):(r.bottom-rr.top+4)),fb=up&&form?form.getBoundingClientRect():null,ft;
      /* opened upwards over the filters (two rows of them on tablets), a list that would stop just short of their top edge is
         raised to it, so no sliver of the form shows above the list under its shadow */
      if(fb&&(ft=Math.floor(fb.top-rr.top))<top&&top<ft+24&&r.top-rr.top-4-ft<=rm){top=ft;h=r.top-rr.top-4-ft;pop.style.maxHeight=pop.style.minHeight=h+'px';}
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
    function render(nofit){
      var q=inp.value.replace(/^\s+|\s+$/g,''),mode=getMode(),r,i,li,s,sp;
      if(!q){close();say('');return;}
      r=find(q,mode);opts=[];act=-1;lb.innerHTML='';inp.removeAttribute('aria-activedescendant');
      for(i=0;i<r.length&&i<8;i++){
        s=show(r[i][0],mode);li=document.createElement('li');li.id='bkh-ds-o'+i;li.setAttribute('role','option');li.setAttribute('aria-selected','false');
        li.setAttribute('aria-label',D[r[i][0]][0]+(s.meta?', '+s.meta:'')+', '+s.price);
        sp=document.createElement('span');sp.className='bkh-dl-name';mark(sp,D[r[i][0]][0],q);li.appendChild(sp);
        if(s.meta){sp=document.createElement('span');sp.className='bkh-dl-meta';sp.textContent=s.meta;li.appendChild(sp);}
        sp=document.createElement('span');sp.className='bkh-dl-price';sp.textContent=s.price;li.appendChild(sp);
        lb.appendChild(li);opts.push({li:li,url:s.url});
      }
      if(r.length>8){
        li=document.createElement('li');li.id='bkh-ds-o'+i;li.className='bkh-dl-all';li.setAttribute('role','option');li.setAttribute('aria-selected','false');
        li.textContent=r.length>12?'See the best 12 of '+r.length:'See all '+r.length+' matches';li.insertAdjacentHTML('beforeend','<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>');
        lb.appendChild(li);opts.push({li:li,all:true});
      }
      if(opts.length){msg.hidden=true;lb.hidden=false;}
      else{lb.hidden=true;msg.hidden=false;msg.textContent='No development matches \u201c'+(q.length>40?q.slice(0,39)+'\u2026':q)+'\u201d yet. Search anyway and we\u2019ll suggest some.';}
      open=opts.length>0;pop.hidden=false;inp.setAttribute('aria-expanded',open?'true':'false');place(nofit!==true);
      say(opts.length?(r.length+(r.length===1?' development matches':' developments match')+'. Use the up and down arrows to choose one.'):'No developments match.');
    }
    function setAct(i,scroll){
      if(act>=0&&opts[act]){opts[act].li.setAttribute('aria-selected','false');}
      act=i;
      if(act<0||!opts[act]){act=-1;inp.removeAttribute('aria-activedescendant');return;}
      var li=opts[act].li;li.setAttribute('aria-selected','true');inp.setAttribute('aria-activedescendant',li.id);
      if(scroll){var t=li.offsetTop,b=t+li.offsetHeight;if(t<pop.scrollTop){pop.scrollTop=t;}else if(b>pop.scrollTop+pop.clientHeight){pop.scrollTop=b-pop.clientHeight;}}
    }
    function move(d){if(!opts.length){return;}var i=act+d;if(i>=opts.length){i=-1;}else if(i<-1){i=opts.length-1;}setAct(i,true);}
    function ready(){var m=getMode();if(fy){if(m!=='buy'){fy.value=m;fy.disabled=false;}else{fy.value='';fy.disabled=true;}}}
    function choose(i){
      var o=opts[i],q=clean(inp.value);if(!o){return;}
      track(q);
      close();
      if(o.all){inp.value=q;ready();ds.submit();return;}
      window.location.href=o.url;
    }
    /* a press that starts in the search or the list (pd) is never taken for a click outside it, and never scrolls the page
       while the button is down: focus from it opens the list where it fits, and the list is fitted once the click is over */
    var pd=false,pdT=0,refit=false;
    function settle(){clearTimeout(pdT);pd=false;if(refit){refit=false;if(!pop.hidden){place(true);}}}
    document.addEventListener('pointerdown',function(e){clearTimeout(pdT);pd=!!(e.target&&(ds.contains(e.target)||pop.contains(e.target)));},true);
    function lift(){if(pd){clearTimeout(pdT);pdT=setTimeout(settle,700);}} /* no click after it (dragged away, cancelled) */
    document.addEventListener('pointerup',lift,true);document.addEventListener('pointercancel',lift,true);
    inp.addEventListener('input',function(){refit=false;render();});
    inp.addEventListener('focus',function(){if(inp.value.replace(/\s+/g,'')){if(pd){refit=true;render(true);}else{render();}}});
    inp.addEventListener('click',function(){if(pop.hidden&&inp.value.replace(/\s+/g,'')){if(pd){refit=true;render(true);}else{render();}}}); /* closed with Escape: a click opens it again */
    inp.addEventListener('keydown',function(e){
      var k=e.key;
      if(refit){refit=false;if(open&&k!=='Escape'&&k!=='Esc'&&k!=='Tab'){place(true);}}
      if(k==='ArrowDown'||k==='Down'){e.preventDefault();if(!open){render();}move(1);}
      else if(k==='ArrowUp'||k==='Up'){e.preventDefault();if(!open){render();}move(-1);}
      else if(k==='Enter'){if(open&&act>=0){e.preventDefault();choose(act);}}
      else if(k==='Escape'||k==='Esc'){if(!pop.hidden){e.preventDefault();close();}else if(inp.value){e.preventDefault();inp.value='';say('');}}
      else if(k==='Tab'){close();}
      else if((k==='ArrowLeft'||k==='ArrowRight'||k==='Left'||k==='Right'||k==='Home'||k==='End')&&act>=0){setAct(-1,false);} /* the caret moves: focus is back in the text */
    });
    inp.addEventListener('blur',function(){setTimeout(function(){if(!down&&document.activeElement!==inp){close();}},150);});
    pop.addEventListener('mousedown',function(e){e.preventDefault();});
    pop.addEventListener('pointerdown',function(){down=true;});
    document.addEventListener('pointerup',function(){setTimeout(function(){down=false;},300);},true);
    /* the mouse highlights an option only when it really moves: browsers also send mousemove when the list opens, re-renders or
       scrolls under a resting pointer (WebKit), and that must not override the keyboard's choice */
    var mx=null,my=null,mv=false;
    document.addEventListener('mousemove',function(e){mv=mx===null?!!(e.movementX||e.movementY):(e.clientX!==mx||e.clientY!==my);mx=e.clientX;my=e.clientY;},true);
    pop.addEventListener('mousemove',function(e){if(!mv){return;}var li=e.target&&e.target.closest?e.target.closest('li[role="option"]'):null;if(li){for(var i=0;i<opts.length;i++){if(opts[i].li===li&&act!==i){setAct(i,false);}}}});
    pop.addEventListener('click',function(e){var li=e.target&&e.target.closest?e.target.closest('li[role="option"]'):null;if(!li){return;}for(var i=0;i<opts.length;i++){if(opts[i].li===li){choose(i);return;}}});
    /* Buy | Rent | Off-plan while the list is open: it stays open, re-ordered for the new mode, and the text keeps the focus */
    var keep=false;
    if(tl){tl.addEventListener('pointerdown',function(){keep=!pop.hidden;if(keep){down=true;}});tl.addEventListener('click',function(){if(keep||!pop.hidden){keep=false;render();try{inp.focus({preventScroll:true});}catch(x){inp.focus();}}});}
    document.addEventListener('click',function(e){if(pd){clearTimeout(pdT);pdT=setTimeout(settle,0);return;}var t=e.target;if(t&&!ds.contains(t)&&!pop.contains(t)&&!(tl&&tl.contains(t))){close();}});
    ds.addEventListener('submit',function(e){
      var q=inp.value.replace(/^\s+|\s+$/g,'');
      if(!q){e.preventDefault();inp.focus();return;}
      var m=getMode(),hit=strong(find(q,m),q);
      track(clean(q));
      if(hit>=0){e.preventDefault();close();window.location.href=show(hit,m).url;return;}
      inp.value=clean(q); /* the address carries only the cleaned name (none left: the panel suggests some) */
      ready();close();
    });
    var raf=0;function later(){if(!raf){raf=(window.requestAnimationFrame||setTimeout)(function(){raf=0;place(false);});}}
    window.addEventListener('resize',function(){fitPh();fitGo();later();});
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
  /* reveal (transform only; everything is visible at rest) */
  var rv=root.querySelectorAll('.bkh-rv');
  if('IntersectionObserver' in window&&rv.length){var io=new IntersectionObserver(function(es){es.forEach(function(e){if(e.isIntersecting){e.target.classList.add('is-in');io.unobserve(e.target);}});},{rootMargin:'0px 0px -6% 0px'});for(var r=0;r<rv.length;r++){io.observe(rv[r]);}}
  else{for(var q=0;q<rv.length;q++){rv[q].classList.add('is-in');}}
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

	function blockke_hm3_svg( $name ) {
		$svgs = array(
			'wa'     => '<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38a9.87 9.87 0 0 0 4.74 1.21c5.46 0 9.91-4.45 9.91-9.91S17.5 2 12.04 2zm0 18.15c-1.48 0-2.93-.4-4.2-1.15l-.3-.18-3.12.82.83-3.04-.2-.31a8.2 8.2 0 0 1-1.26-4.38c0-4.54 3.7-8.24 8.25-8.24 4.54 0 8.24 3.7 8.24 8.24 0 4.55-3.7 8.24-8.24 8.24zm4.52-6.16c-.25-.12-1.47-.72-1.69-.81-.23-.08-.39-.12-.56.12-.16.25-.64.81-.78.97-.14.17-.29.19-.54.06-.25-.12-1.05-.39-1.99-1.23-.74-.66-1.23-1.47-1.38-1.72-.14-.25-.02-.38.11-.51.11-.11.25-.29.37-.43.12-.14.16-.25.25-.41.08-.17.04-.31-.02-.43-.06-.12-.56-1.34-.76-1.84-.2-.48-.41-.42-.56-.43h-.48c-.17 0-.43.06-.66.31-.22.25-.86.85-.86 2.07 0 1.22.89 2.4 1.01 2.56.12.17 1.75 2.67 4.23 3.74.59.26 1.05.41 1.41.52.59.19 1.13.16 1.56.1.48-.07 1.47-.6 1.67-1.18.21-.58.21-1.07.14-1.18-.06-.1-.22-.16-.47-.28z"/></svg>',
			'phone'  => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.8 19.8 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.8 19.8 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.13.96.36 1.9.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.91.34 1.85.57 2.81.7A2 2 0 0 1 22 16.92z"/></svg>',
			'arrow'  => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>',
			'search' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>',
			'check'  => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m5 12 4.5 4.5L19 7"/></svg>',
		);
		return isset( $svgs[ $name ] ) ? $svgs[ $name ] : '';
	}

	/* ------------------------------------------------------------------ render */

	function blockke_hm3_render() {
		$cfg   = blockke_hm3_config();
		$u     = 'blockke_hm3_url';
		$stats = blockke_hm3_stats();
		$tel   = 'tel:' . preg_replace( '/[^\d+]/', '', $cfg['phone'] );
		$h     = '';

		/* ---- hero ---- */
		$areas_all = get_terms( array( 'taxonomy' => 'property_area', 'hide_empty' => true, 'orderby' => 'count', 'order' => 'DESC', 'number' => 40 ) );
		$area_opts = '<option value="">Anywhere in Nairobi &amp; beyond</option>';
		if ( ! is_wp_error( $areas_all ) ) {
			$top  = array();
			$rest = array();
			foreach ( $areas_all as $t ) {
				if ( (int) $t->count >= 8 ) {
					$top[] = $t;
				} else {
					$rest[] = $t;
				}
			}
			usort( $top, function ( $a, $b ) { return strcmp( $a->name, $b->name ); } );
			usort( $rest, function ( $a, $b ) { return strcmp( $a->name, $b->name ); } );
			foreach ( array_merge( $top, $rest ) as $t ) {
				$area_opts .= '<option value="' . esc_attr( $t->slug ) . '">' . esc_html( $t->name ) . '</option>';
			}
		}
		$quick = '';
		foreach ( (array) $cfg['quick_areas'] as $qs ) {
			$t = get_term_by( 'slug', $qs, 'property_area' );
			if ( $t ) {
				$quick .= '<a href="' . esc_url( blockke_hm3_area_url( $t ) ) . '">' . esc_html( $t->name ) . '</a>';
			}
		}
		$h .= '<section class="bkh-hero" aria-label="Search Nairobi property">';
		$h .= blockke_hm3_img( $cfg['hero_image'], 'full', 'bkh-hero-img', '', true, '100vw' );
		$h .= '<div class="bkh-wrap">';
		$h .= '<span class="bkh-eyebrow">' . esc_html( $cfg['hero_eyebrow'] ) . '</span>';
		$h .= '<h1 class="bkh-display">' . blockke_hm3_h( $cfg['hero_title'] ) . '</h1>';
		$h .= '<p class="bkh-hero-sub">' . esc_html( $cfg['hero_sub'] ) . '</p>';
		$h .= '<div class="bkh-search">';
		$h .= '<div class="bkh-tabs" role="tablist" aria-label="What are you looking for?">';
		$h .= '<button type="button" class="bkh-tab" role="tab" data-mode="buy" aria-selected="true">Buy</button>';
		$h .= '<button type="button" class="bkh-tab" role="tab" data-mode="rent" aria-selected="false">Rent</button>';
		$h .= '<button type="button" class="bkh-tab" role="tab" data-mode="offplan" aria-selected="false">Off-plan</button>';
		$h .= '</div>';
		/* v3.1: the development-name search; ?bkdev= pages come back in the mode they were searched in */
		$devq  = blockke_hm3_dev_query();
		$devm  = null !== $devq ? blockke_hm3_dev_mode() : 'buy';
		$devix = ! empty( $cfg['dev_search'] ) ? blockke_hm3_dev_index() : array( 'd' => array() );
		$h .= '<form class="bkh-form bke-cy-has" data-mode="' . esc_attr( $devm ) . '" method="get" action="' . esc_url( $u( 'search' ) ) . '">';
		$h .= '<input type="hidden" name="property_action_category[]" value="for-sale">';
		$h .= '<div class="bkh-field bkh-field--where"><label for="bkh-where">Where</label><select id="bkh-where" name="property_area[]">' . $area_opts . '</select></div>';
		$h .= '<div class="bkh-field bkh-field--type"><label for="bkh-type">Type</label><select id="bkh-type" name="property_category[]"><option value="">Any type</option><option value="apartments">Apartments</option><option value="houses-residential">Houses</option><option value="villas-residential">Villas</option><option value="studio-apartments">Studios</option><option value="land">Land</option><option value="offices">Offices</option><option value="warehouses">Warehouses</option></select></div>';
		$h .= '<div class="bkh-field bkh-field--beds"><label for="bkh-beds">Bedrooms</label><select id="bkh-beds" name="bedrooms"><option value="">Any</option><option value="studio">Studio</option><option value="1">1 bed</option><option value="2">2 bed</option><option value="3">3 bed</option><option value="4">4 bed</option><option value="5">5+ bed</option></select></div>';
		$h .= '<div class="bkh-field bkh-field--budget"><label for="bkh-budget">Budget</label><select id="bkh-budget" name="price_max"><option value="">Any budget</option><option value="5000000">Under KES 5M</option><option value="8000000">Under KES 8M</option><option value="12000000">Under KES 12M</option><option value="20000000">Under KES 20M</option><option value="35000000">Under KES 35M</option><option value="60000000">Under KES 60M</option></select></div>';
		$h .= '<div class="bkh-field bkh-field--completion"><label for="bkh-completion">Completion</label><select id="bkh-completion" name="completion"><option value="any">All projects</option><option value="ready-now">Ready now</option><option value="2026">2026</option><option value="2027">2027</option><option value="2028">2028</option><option value="2029-plus">2029+</option></select></div>';
		$h .= '<button type="submit" class="bkh-submit">' . blockke_hm3_svg( 'search' ) . 'Search</button>';
		$h .= '</form>';
		if ( $devix['d'] ) {
			$h .= blockke_hm3_dev_form( $devix['d'], $devq );
			if ( null !== $devq ) {
				$h .= blockke_hm3_dev_results( $devix['d'], $devq, $devm );
			}
		}
		$h .= '</div>';
		if ( $quick ) {
			$h .= '<div class="bkh-hero-quick"><span>Popular:</span>' . $quick . '</div>';
		}
		$h .= '</div>';
		if ( ! empty( $cfg['hero_caption'] ) ) {
			$h .= '<span class="bkh-hero-cap">' . esc_html( $cfg['hero_caption'] ) . '</span>';
		}
		$h .= '</section>';

		/* ---- proof band ---- */
		$num = function ( $n ) {
			return str_replace( '+', '<small>+</small>', esc_html( blockke_hm3_plus( $n ) ) );
		};
		$h .= '<section class="bkh-strip" aria-label="Block in numbers"><div class="bkh-wrap"><div class="bkh-stats">';
		$h .= '<div class="bkh-stat"><b>' . $num( $stats['sale'] ) . '</b><span>Homes for sale</span></div>';
		$h .= '<div class="bkh-stat"><b>' . $num( $stats['projects'] ) . '</b><span>Developments tracked</span></div>';
		$h .= '<div class="bkh-stat"><b>' . $num( $stats['areas'] ) . '</b><span>Nairobi neighbourhoods</span></div>';
		$h .= '<div class="bkh-stat"><b>Same day</b><span>Replies from a real person</span></div>';
		$h .= '</div></div></section>';

		/* ---- mandate spotlight + developments ---- */
		$feat = ! empty( $cfg['feature'] ) ? blockke_hm3_development( $cfg['feature'] ) : null;
		$devs = array();
		foreach ( (array) $cfg['developments'] as $slug ) {
			if ( $feat && $slug === $feat['slug'] ) {
				continue;
			}
			$d = blockke_hm3_development( $slug );
			if ( $d ) {
				$devs[] = $d;
			}
		}
		if ( $feat || $devs ) {
			$h .= '<section class="bkh-sec" id="developments"><div class="bkh-wrap">';
			if ( $feat ) {
				$comp_v = $feat['comp'] ? preg_replace( '/^Completion\s+/i', '', $feat['comp'] ) : '';
				$h .= '<div class="bkh-feat bkh-rv">';
				$h .= '<a class="bkh-media bkh-feat-media" href="' . esc_url( $feat['url'] ) . '" aria-label="' . esc_attr( $feat['name'] ) . '">' . blockke_hm3_img( $feat['thumb'], '2048x2048', '', $feat['name'] . ( $feat['area'] ? ', ' . $feat['area'] : '' ), false, '(max-width: 1100px) 100vw, 58vw' );
				if ( $feat['mandate'] ) {
					$h .= '<span class="bkh-tag bkh-tag--brass">' . blockke_hm3_svg( 'check' ) . 'Authorised sales agent</span>';
				}
				$h .= '</a>';
				$h .= '<div><span class="bkh-eyebrow">' . esc_html( $cfg['feature_eyebrow'] ) . '</span>';
				$h .= '<h2 class="bkh-display">' . esc_html( $feat['name'] ) . ( $feat['area'] ? ', <em>' . esc_html( $feat['area'] ) . '</em>' : '' ) . '</h2>';
				$h .= '<div class="bkh-feat-meta">';
				$h .= '<div><span>From</span><b class="bkh-num">' . esc_html( $feat['from'] ) . '</b></div>';
				$h .= '<div><span>Homes</span><b>' . esc_html( $feat['mix'] ? $feat['mix'] : 'Mixed' ) . '</b></div>';
				if ( $comp_v ) {
					$h .= '<div><span>' . ( 0 === stripos( $comp_v, 'ready' ) ? 'Status' : 'Completion' ) . '</span><b>' . esc_html( $comp_v ) . '</b></div>';
				}
				$h .= '<div><span>Sold by</span><b>' . ( $feat['mandate'] ? 'Block, for the developer' : 'Block Real Estate' ) . '</b></div>';
				$h .= '</div>';
				if ( ! empty( $cfg['feature_text'] ) ) {
					$h .= '<p class="bkh-lede">' . esc_html( $cfg['feature_text'] ) . '</p>';
				}
				$h .= '<div class="bkh-ctas"><a class="bkh-btn bkh-btn--navy" href="' . esc_url( $feat['url'] ) . '">Prices &amp; floor plans' . blockke_hm3_svg( 'arrow' ) . '</a><a class="bkh-btn bkh-btn--ghost" href="' . esc_url( blockke_hm3_wa( $feat['name'] . '.' ) ) . '" target="_blank" rel="noopener" data-where="home_feature">' . blockke_hm3_svg( 'wa' ) . 'WhatsApp Loyd</a></div>';
				$h .= '</div></div>';
			}
			if ( $devs ) {
				$h .= '<div class="bkh-subhead"><h3>' . ( $feat ? 'More projects we&rsquo;ve vetted' : 'New developments' ) . '</h3><a class="bkh-more" href="' . esc_url( $u( 'offplan' ) ) . '">All off-plan projects' . blockke_hm3_svg( 'arrow' ) . '</a></div>';
				$h .= '<div class="bkh-devs bkh-rv">';
				foreach ( $devs as $d ) {
					$h .= '<a class="bkh-dev" href="' . esc_url( $d['url'] ) . '">';
					$h .= '<div class="bkh-media">' . blockke_hm3_img( $d['thumb'], 'large', '', $d['name'] . ( $d['area'] ? ', ' . $d['area'] : '' ), false, '(max-width: 767px) 80vw, 33vw' );
					if ( $d['mandate'] ) {
						$h .= '<span class="bkh-tag bkh-tag--brass">' . blockke_hm3_svg( 'check' ) . 'Authorised sales agent</span>';
					} elseif ( $d['comp'] ) {
						$h .= '<span class="bkh-tag">' . esc_html( $d['comp'] ) . '</span>';
					}
					$h .= '</div><div class="bkh-dev-body">';
					$meta = array();
					if ( $d['area'] ) {
						$meta[] = $d['area'];
					}
					if ( $d['mandate'] && $d['comp'] ) {
						$meta[] = $d['comp'];
					}
					$h .= '<div class="bkh-dev-meta">' . esc_html( implode( ' · ', $meta ) ) . '</div>';
					$h .= '<h3>' . esc_html( $d['name'] ) . '</h3>';
					$h .= '<div class="bkh-dev-price"><b>' . esc_html( $d['price'] ) . '</b>' . ( $d['mix'] ? '<span>' . esc_html( $d['mix'] ) . '</span>' : '' ) . '</div>';
					$h .= '<span class="bkh-dev-cta">Prices &amp; floor plans' . blockke_hm3_svg( 'arrow' ) . '</span>';
					$h .= '</div></a>';
				}
				$h .= '</div>';
			}
			$h .= '</div></section>';
		}

		/* ---- worth a viewing ---- */
		$sale = blockke_hm3_listings( 'for-sale', (int) $cfg['listings_count'] );
		$rent = blockke_hm3_listings( 'for-rent', (int) $cfg['listings_count'] );
		$card = function ( $l ) {
			$o  = '<a class="bkh-card" href="' . esc_url( $l['url'] ) . '" title="' . esc_attr( $l['full'] ) . '">';
			$o .= '<div class="bkh-media">' . blockke_hm3_img( $l['thumb'], 'large', '', $l['full'], false, '(max-width: 767px) 100vw, (max-width: 900px) 50vw, 33vw' );
			if ( $l['status'] ) {
				$o .= '<span class="bkh-tag">' . esc_html( $l['status'] ) . '</span>';
			}
			$o .= '</div><div class="bkh-card-body"><h3>' . esc_html( $l['title'] ) . '</h3>';
			$o .= '<div class="bkh-card-price">' . esc_html( $l['price'] ) . '</div>';
			if ( $l['loc'] ) {
				$o .= '<div class="bkh-card-loc">' . esc_html( $l['loc'] ) . '</div>';
			}
			$specs = array();
			if ( $l['beds'] > 0 ) {
				$specs[] = '<span><b>' . (int) $l['beds'] . '</b> bed</span>';
			} elseif ( ! $l['rent'] && preg_match( '/studio/i', $l['full'] ) ) {
				$specs[] = '<span>Studio</span>';
			}
			if ( $l['baths'] > 0 ) {
				$specs[] = '<span><b>' . (int) $l['baths'] . '</b> bath</span>';
			}
			if ( $l['size'] >= 150 ) {
				$specs[] = '<span><b>' . esc_html( number_format( $l['size'] ) ) . '</b> sq ft</span>';
			}
			if ( $specs ) {
				$o .= '<div class="bkh-specs">' . implode( '', $specs ) . '</div>';
			}
			$o .= '</div></a>';
			return $o;
		};
		if ( $sale || $rent ) {
			$h .= '<section class="bkh-sec bkh-sec--stone" id="listings"><div class="bkh-wrap">';
			$h .= '<div class="bkh-head bkh-rv"><div><span class="bkh-eyebrow">Hand-picked</span><h2 class="bkh-display">' . blockke_hm3_h( 'Worth a viewing |this week.|' ) . '</h2>';
			$h .= '<p class="bkh-lede">The current shortlist, chosen by the team and refreshed as the market moves.</p></div></div>';
			$h .= '<div class="bkh-ltabs" role="tablist"><button type="button" class="bkh-ltab" role="tab" aria-selected="true" aria-controls="bkh-sale">For sale</button><button type="button" class="bkh-ltab" role="tab" aria-selected="false" aria-controls="bkh-rent">For rent</button></div>';
			$h .= '<div class="bkh-grid bkh-rv" id="bkh-sale">';
			foreach ( $sale as $l ) {
				$h .= $card( $l );
			}
			$h .= '</div><div class="bkh-grid" id="bkh-rent" hidden>';
			foreach ( $rent as $l ) {
				$h .= $card( $l );
			}
			$h .= '</div>';
			$h .= '<div class="bkh-lfoot"><a class="bkh-btn bkh-btn--navy" data-for="bkh-sale" href="' . esc_url( $u( 'for_sale' ) ) . '">Browse all ' . esc_html( blockke_hm3_plus( $stats['sale'] ) ) . ' homes for sale</a><a class="bkh-btn bkh-btn--navy" data-for="bkh-rent" href="' . esc_url( $u( 'for_rent' ) ) . '" hidden>Browse all rentals</a></div>';
			$h .= '</div></section>';
		}

		/* ---- diaspora ---- */
		$h .= '<section class="bkh-sec bkh-sec--navy bkh-dia" id="diaspora" data-where="home_diaspora"><div class="bkh-wrap"><div class="bkh-dia-grid">';
		$h .= '<div class="bkh-rv"><span class="bkh-eyebrow">Buying from abroad</span><h2 class="bkh-display">' . blockke_hm3_h( 'Most of our buyers aren’t in Nairobi. |That’s the point.|' ) . '</h2>';
		$h .= '<p class="bkh-lede">From London, Dallas, Dubai or Toronto you can&rsquo;t walk the street or sit across from the developer. We do &mdash; and you see everything on video before you commit a shilling.</p>';
		$h .= '<div class="bkh-ctas"><a class="bkh-btn bkh-btn--white" href="' . esc_url( blockke_hm3_wa( 'buying from abroad.' ) ) . '" target="_blank" rel="noopener">' . blockke_hm3_svg( 'wa' ) . 'WhatsApp Loyd</a><a class="bkh-btn bkh-btn--ghostw" href="' . esc_url( $u( 'diaspora_guide' ) ) . '">Read the diaspora guide</a></div>';
		$h .= '<div class="bkh-cities">' . esc_html( $cfg['cities'] ) . '<b>Most of the homes we close are bought from abroad.</b></div>';
		$h .= '</div>';
		$h .= '<ol class="bkh-steps bkh-rv">';
		$h .= '<li class="bkh-step"><i>01</i><div><h3>Tell us your brief</h3><p>Budget, area, timeline and whether it&rsquo;s to live in or let out &mdash; on WhatsApp, in your time zone.</p></div></li>';
		$h .= '<li class="bkh-step"><i>02</i><div><h3>We shortlist and video-view</h3><p>Every unit walked, filmed and priced against real comparables, with the developer&rsquo;s record and approvals checked.</p></div></li>';
		$h .= '<li class="bkh-step"><i>03</i><div><h3>You buy with the checks done</h3><p>Title search, payment plan and contract reviewed; we negotiate on your side. The seller pays our fee, not you.</p></div></li>';
		$h .= '</ol>';
		$h .= '</div></div></section>';

		/* ---- know your block ---- */
		$areas = blockke_hm3_areas();
		if ( $areas ) {
			$h .= '<section class="bkh-sec" id="neighbourhoods"><div class="bkh-wrap">';
			$h .= '<div class="bkh-head bkh-rv"><div><span class="bkh-eyebrow">Neighbourhoods</span><h2 class="bkh-display">' . blockke_hm3_h( 'Know your block |before you buy it.|' ) . '</h2>';
			$h .= '<p class="bkh-lede">Prices, commutes, schools, noise &mdash; everything the listings won&rsquo;t tell you, neighbourhood by neighbourhood.</p></div>';
			$h .= '<a class="bkh-more" href="' . esc_url( $u( 'neighbourhoods' ) ) . '">All neighbourhoods' . blockke_hm3_svg( 'arrow' ) . '</a></div>';
			$h .= '<div class="bkh-areas bkh-rv">';
			$i = 0;
			foreach ( $areas as $a ) {
				$lead = ( 0 === $i );
				$h   .= '<a class="bkh-area' . ( $lead ? ' bkh-area--lead' : '' ) . '" href="' . esc_url( $a['url'] ) . '">' . blockke_hm3_img( $a['thumb'], $lead ? '2048x2048' : 'large', '', $a['name'], false, $lead ? '(max-width: 767px) 100vw, 50vw' : '(max-width: 767px) 50vw, 25vw' );
				$h   .= '<div><h3>' . esc_html( $a['name'] ) . '</h3><span>' . esc_html( number_format( $a['count'] ) ) . ' ' . ( 1 === $a['count'] ? 'home' : 'homes' ) . '</span></div></a>';
				$i++;
			}
			$h .= '</div></div></section>';
		}

		/* ---- founder ---- */
		$founder = blockke_hm3_agent( $cfg['founder_agent'] );
		$team    = array();
		foreach ( (array) $cfg['team_agents'] as $aid ) {
			$ag = blockke_hm3_agent( $aid );
			if ( $ag ) {
				$team[] = $ag;
			}
		}
		$h .= '<section class="bkh-sec bkh-sec--stone" id="loyd" data-where="home_founder"><div class="bkh-wrap"><div class="bkh-founder">';
		$h .= '<figure class="bkh-portrait bkh-rv"><div class="bkh-media">' . blockke_hm3_img( $cfg['founder_image'], 'large', '', 'Loyd Mokaya, founder of Block Real Estate', false, '(max-width: 900px) 90vw, 40vw' ) . '</div><figcaption>Loyd Mokaya · Founder</figcaption></figure>';
		$h .= '<div class="bkh-rv"><span class="bkh-eyebrow">Loyd from Block</span><h2 class="bkh-display">' . blockke_hm3_h( 'You’ll deal with a person, |not a portal.|' ) . '</h2>';
		$h .= '<p class="bkh-quote">&ldquo;I started Block because buying in Nairobi shouldn&rsquo;t feel like a gamble.&rdquo;</p>';
		$h .= '<p class="bkh-body">So we check everything first &mdash; the market, the title, the street &mdash; and tell you straight, even when the honest answer is &ldquo;not this one&rdquo;. Nairobi moves fast and unevenly; a price in Kilimani tells you nothing about Lavington. That&rsquo;s why we work it block by block, and why when you message us you get a person who has stood in the building.</p>';
		$h .= '<div class="bkh-sign">Loyd Mokaya<small>Founder, Block Real Estate</small></div>';
		$h .= '<div class="bkh-ctas"><a class="bkh-btn bkh-btn--navy" href="' . esc_url( blockke_hm3_wa( 'a property.' ) ) . '" target="_blank" rel="noopener">' . blockke_hm3_svg( 'wa' ) . 'WhatsApp Loyd</a><a class="bkh-btn bkh-btn--ghost" href="' . esc_url( $u( 'consult' ) ) . '">Book a free consultation</a></div>';
		if ( $team ) {
			$h .= '<div class="bkh-team"><div class="bkh-avatars">';
			foreach ( $team as $ag ) {
				$h .= blockke_hm3_img( $ag['thumb'], 'thumbnail', '', $ag['name'], false, '44px' );
			}
			$h .= '</div><p>' . esc_html( implode( ', ', array_map( function ( $ag ) { return preg_replace( '/\s.*$/', '', $ag['name'] ); }, $team ) ) ) . ' and the rest of the team &mdash; <a href="' . esc_url( $u( 'team' ) ) . '">meet them</a></p></div>';
		}
		$h .= '</div></div></div></section>';

		/* ---- services ---- */
		$services = array(
			array( 'Buy a home', 'Vetted listings, comparables run, title checked. You negotiate from facts.', $u( 'for_sale' ) ),
			array( 'Sell or let your property', 'An honest valuation, proper marketing, and buyers or tenants vetted before they view.', $u( 'sell' ) ),
			array( 'Rent a home', 'A place you&rsquo;d actually want to live in, with a lease that holds up.', $u( 'for_rent' ) ),
			array( 'Property management', 'Rent collection, repairs, inspections, statements &mdash; the returns without the 2am calls.', $u( 'management' ) ),
			array( 'Off-plan &amp; investment advisory', 'Which project, which unit, which payment plan &mdash; and which to walk away from.', $u( 'advisory' ) ),
			array( 'Build &amp; design', 'Client-side project management, architecture and interiors through Block Advisory.', $u( 'build' ) ),
		);
		$h .= '<section class="bkh-sec" id="services"><div class="bkh-wrap">';
		$h .= '<div class="bkh-head bkh-rv"><div><span class="bkh-eyebrow">Services</span><h2 class="bkh-display">' . blockke_hm3_h( 'A service for |every move.|' ) . '</h2><p class="bkh-lede">Buy, rent, sell, let, manage or build. One team, one standard throughout.</p></div></div>';
		$h .= '<div class="bkh-services bkh-rv">';
		foreach ( $services as $s ) {
			$h .= '<a class="bkh-service" href="' . esc_url( $s[2] ) . '"><h3>' . $s[0] . '</h3>' . blockke_hm3_svg( 'arrow' ) . '<p>' . $s[1] . '</p></a>';
		}
		$h .= '</div>';
		$h .= '<div class="bkh-val"><div><h3>Own a property in Nairobi?</h3><p>Tell us about it and we&rsquo;ll tell you what it&rsquo;s worth &mdash; before we list a thing.</p></div><a class="bkh-btn bkh-btn--white" href="' . esc_url( $u( 'valuation' ) ) . '">Get a free valuation</a></div>';
		$h .= '</div></section>';

		/* ---- homework ---- */
		$arts = blockke_hm3_articles( 4 );
		if ( $arts ) {
			$lead = array_shift( $arts );
			$h   .= '<section class="bkh-sec bkh-sec--rule" id="homework"><div class="bkh-wrap">';
			$h   .= '<div class="bkh-head bkh-rv"><div><span class="bkh-eyebrow">The homework</span><h2 class="bkh-display">' . blockke_hm3_h( 'What we’d tell you |over coffee.|' ) . '</h2><p class="bkh-lede">Market data, legal landmines and neighbourhood deep-dives, written by the people doing the deals.</p></div>';
			$h   .= '<a class="bkh-more" href="' . esc_url( $u( 'blog' ) ) . '">All articles' . blockke_hm3_svg( 'arrow' ) . '</a></div>';
			$h   .= '<div class="bkh-hw bkh-rv">';
			$h   .= '<a class="bkh-lead" href="' . esc_url( $lead['url'] ) . '"><div class="bkh-media">' . blockke_hm3_img( $lead['thumb'], 'large', '', $lead['title'], false, '(max-width: 900px) 100vw, 55vw' ) . '</div>';
			$h   .= '<span class="bkh-cat">' . esc_html( $lead['cat'] ) . '</span><h3>' . esc_html( $lead['title'] ) . '</h3><p>' . esc_html( $lead['ex'] ) . '</p><time>' . esc_html( $lead['date'] ) . '</time></a>';
			$h   .= '<div class="bkh-list">';
			foreach ( $arts as $a ) {
				$h .= '<a class="bkh-item" href="' . esc_url( $a['url'] ) . '"><div class="bkh-media">' . blockke_hm3_img( $a['thumb'], 'medium', '', $a['title'], false, '116px' ) . '</div><div><span class="bkh-cat">' . esc_html( $a['cat'] ) . '</span><h3>' . esc_html( $a['title'] ) . '</h3><time>' . esc_html( $a['date'] ) . '</time></div></a>';
			}
			$h .= '</div></div></div></section>';
		}

		/* ---- closing ---- */
		$h .= '<section class="bkh-sec bkh-sec--deep bkh-close" id="contact" data-where="home_close"><div class="bkh-wrap">';
		$h .= '<span class="bkh-eyebrow">Start here</span><h2 class="bkh-display">' . blockke_hm3_h( 'Your next move starts with |one message.|' ) . '</h2>';
		$h .= '<p class="bkh-lede">A real person replies the same day. No bots, no runaround.</p>';
		$h .= '<div class="bkh-ctas"><a class="bkh-btn bkh-btn--white" href="' . esc_url( blockke_hm3_wa( 'my next move.' ) ) . '" target="_blank" rel="noopener">' . blockke_hm3_svg( 'wa' ) . 'WhatsApp us</a><a class="bkh-btn bkh-btn--ghostw" href="' . esc_attr( $tel ) . '">' . blockke_hm3_svg( 'phone' ) . 'Call ' . esc_html( $cfg['phone_label'] ) . '</a></div>';
		$h .= '<p class="bkh-alt">Prefer a sit-down? <a href="' . esc_url( $u( 'consult' ) ) . '">Book a free consultation</a> &middot; Mon&ndash;Sat 07:00&ndash;21:00</p>';
		$h .= '</div></section>';

		/* ---- sticky bar (phones) ---- */
		$bar = '<div class="bkh-bar" data-where="home_sticky" aria-label="Contact Block"><a class="bkh-bar-wa" href="' . esc_url( blockke_hm3_wa( 'a property.' ) ) . '" target="_blank" rel="noopener">' . blockke_hm3_svg( 'wa' ) . 'WhatsApp</a><a class="bkh-bar-call" href="' . esc_attr( $tel ) . '">' . blockke_hm3_svg( 'phone' ) . 'Call us</a></div>';

		$js_cfg = array( 'endpoint' => esc_url_raw( rest_url( 'block/v1/contact-click' ) ) );
		if ( $devix['d'] ) {
			$js_cfg['dv'] = blockke_hm3_dev_data( $devix['d'] ); // v3.1: the development-name typeahead
		}
		$out    = '<!-- BLOCK homepage v' . blockke_hm3_ver() . ' -->';
		$out   .= '<link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Bodoni+Moda:ital,opsz,wght@0,6..96,400..700;1,6..96,400..700&amp;display=swap">';
		$out   .= '<style id="bkh-css">' . blockke_hm3_css() . '</style>';
		$out   .= '<div class="bkh" id="bkh" data-where="home_hero">' . $h . '</div>' . $bar;
		$out   .= '<script id="bkh-cfg">window.BLOCKHOME=' . wp_json_encode( $js_cfg, JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS ) . ';</script>';
		$out   .= '<script id="bkh-js">' . blockke_hm3_js() . '</script>';
		return $out;
	}

	/**
	 * Content stamp: the newest change to any listing, article or agent. Part of every cache key, so the
	 * page rebuilds itself after an edit without hooks (this code only runs where the shortcode renders).
	 */
	function blockke_hm3_stamp() {
		static $stamp = null;
		if ( null !== $stamp ) {
			return $stamp;
		}
		global $wpdb;
		$stamp = (string) $wpdb->get_var( "SELECT MAX(post_modified_gmt) FROM {$wpdb->posts} WHERE post_type IN ('estate_property','post','estate_agent') AND post_status = 'publish'" );
		$stamp = substr( md5( blockke_hm3_ver() . '|' . $stamp . '|' . wp_json_encode( get_option( 'blockke_homepage_v3', array() ) ) ), 0, 12 );
		return $stamp;
	}

	function blockke_hm3_output() {
		$cfg   = blockke_hm3_config();
		$key   = 'blockke_home_v3_' . blockke_hm3_stamp() . ( blockke_hm3_dev_page() ? '_p' . blockke_hm3_dev_page() : '' ); // v3.1: a preview page's copy (its name search posts to itself) is not the front page's
		$skip  = isset( $_GET['bkhome_nocache'] ) || isset( $_GET['elementor-preview'] ) || is_preview() || isset( $_GET['bkdev'] ); // v3.1: a name search is rendered fresh and never stored
		$cache = $skip ? false : get_transient( $key );
		if ( is_string( $cache ) && '' !== $cache ) {
			return $cache;
		}
		try {
			$html = blockke_hm3_render();
		} catch ( Throwable $e ) {
			error_log( 'BLOCK homepage v3: ' . $e->getMessage() );
			return '<div style="padding:120px 20px 60px;text-align:center;font-family:Montserrat,sans-serif"><h1 style="font-size:32px">Block Real Estate</h1><p>Nairobi property, block by block.</p><p><a href="' . esc_url( home_url( '/for-sale/' ) ) . '">Homes for sale</a> · <a href="' . esc_url( home_url( '/for-rent/' ) ) . '">Rentals</a> · <a href="' . esc_url( home_url( '/contact-us/' ) ) . '">Contact</a></p></div>';
		}
		if ( ! $skip ) {
			set_transient( $key, $html, max( 1, (int) $cfg['cache_minutes'] ) * MINUTE_IN_SECONDS );
		}
		return $html;
	}
}

echo blockke_hm3_output();
