<?php
/* marker line - keeps the connector from Markdown-converting this snippet; harmless in PHP */
/* <div> markup marker - the connector stores content as-is only when it sees a block-level HTML tag; harmless in PHP */
/**
 * BLOCK - Homepage v2.0 (2026-10-23)
 * The block.ke homepage, rendered by this one snippet where the shortcode [wpcode id=<this snippet>] is placed
 * (one Elementor Shortcode widget in a full-width container, the same wiring as the blog hub on /blog/).
 * Everything on the page comes from the site itself, so it stays current without edits:
 *   1) Editorial hero: full-bleed photo, one statement, and a search that posts to /advanced-search/ with the
 *      same parameters the results page already understands (property_action_category, property_area,
 *      property_category, bedrooms, price_max, completion). Buy | Rent | Off-plan tabs.
 *   2) Proof strip: live counts (homes for sale, developments tracked, Nairobi neighbourhoods) + same-day reply.
 *   3) New developments: the projects in config 'developments' (property_project slugs); each card is built
 *      from the project's master listing (property_has_subunits) and its unit listings: lowest price, bed
 *      range, completion, photo; links to the project's ad landing page (page with bke_lp_listing) when one
 *      exists, else the listing. 'mandates' marks the ones Block sells for the developer.
 *   4) Worth a viewing: featured listings (prop_featured) topped up with the newest, For sale | For rent.
 *   5) Diaspora band, 6) Know your block (property_area tiles with live counts and the term images),
 *   7) Loyd from Block (founder + team from estate_agent), 8) Services, 9) The homework (latest articles),
 *   10) Closing CTA, plus a WhatsApp | Call bar on phones.
 * Conversion: every WhatsApp / call tap is logged to the existing POST /wp-json/block/v1/contact-click
 * endpoint (placement home_*), so it appears in the weekly contact report, and fires GA4 contact_click +
 * Meta Contact. Searches fire GA4 'search'.
 * Cache: the rendered HTML is kept in a transient (blockke_home_v2_<stamp>) for up to an hour; the stamp changes
 * whenever a listing, article or agent is saved or the config option changes, so edits show on the next load.
 * ?bkhome_nocache=1 bypasses it. Cloudflare may still serve an older copy until purged.
 * Config: option blockke_homepage (array) overrides any key of the defaults in blockke_home_config().
 * Kill switch / rollback: deactivate this snippet in WPCode, or set the front page back to the previous layout.
 */

if ( ! function_exists( 'blockke_home_config' ) ) {

	function blockke_home_ver() {
		return '2.0.2';
	}

	function blockke_home_config() {
		static $cfg = null;
		if ( null !== $cfg ) {
			return $cfg;
		}
		$defaults = array(
			'hero_image'     => 40994,  // attachment id; a Nairobi photo or a project render (captioned)
			'hero_caption'   => 'Santorini Residences, Westlands · project render',
			'hero_eyebrow'   => 'Nairobi · Residential & Commercial',
			'hero_title'     => 'We know Nairobi block by block.',
			'hero_sub'       => 'Homes for sale, rentals and new developments — every one checked before you view, and negotiated on your side.',
			'quick_areas'    => array( 'westlands', 'kilimani', 'kileleshwa', 'lavington', 'tatu-city' ),
			'developments'   => array( 'golden-hill', 'capital-heights', 'santorini-residences', 'gaia-brookside', 'astra-bay', 'mara-grove-apartments' ),
			'mandates'       => array( 'golden-hill' ), // project slugs Block sells for the developer (badge)
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
				'build'        => '/block-advisory/',
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

	function blockke_home_url( $key ) {
		$cfg = blockke_home_config();
		$u   = isset( $cfg['urls'][ $key ] ) ? $cfg['urls'][ $key ] : '/';
		if ( 0 === strpos( $u, 'http' ) ) {
			return $u;
		}
		return home_url( $u );
	}

	function blockke_home_wa( $topic ) {
		$cfg = blockke_home_config();
		return 'https://wa.me/' . preg_replace( '/\D+/', '', $cfg['whatsapp'] ) . '?text=' . rawurlencode( $cfg['wa_text'] . $topic );
	}

	/** KES price in M notation for sales, K a month for rentals. */
	function blockke_home_price( $n, $rent = false ) {
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
	function blockke_home_plus( $n ) {
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
	function blockke_home_short_title( $title, $max = 46 ) {
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

	function blockke_home_first_term( $id, $tax ) {
		$terms = get_the_terms( $id, $tax );
		if ( is_array( $terms ) && ! empty( $terms ) ) {
			return $terms[0];
		}
		return null;
	}

	function blockke_home_img( $attachment_id, $size, $class, $alt, $eager = false, $sizes = '' ) {
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
	function blockke_home_term_image( $term_id ) {
		$opt = get_option( 'taxonomy_' . (int) $term_id );
		if ( is_array( $opt ) && ! empty( $opt['category_attach_id'] ) ) {
			return (int) $opt['category_attach_id'];
		}
		$m = get_term_meta( (int) $term_id, 'category_attach_id', true );
		return $m ? (int) $m : 0;
	}

	/** Area page: /neighbourhoods/<slug>/ when that page exists, else the area archive. */
	function blockke_home_area_url( $term ) {
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

	function blockke_home_stats() {
		$key = 'blockke_home_v2_stats_' . blockke_home_stamp();
		$s   = get_transient( $key );
		if ( is_array( $s ) ) {
			return $s;
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
	function blockke_home_listing( $id ) {
		$id     = (int) $id;
		$rent   = has_term( 'for-rent', 'property_action_category', $id );
		$price  = (float) get_post_meta( $id, 'property_price', true );
		$label  = trim( (string) get_post_meta( $id, 'property_label_before', true ) );
		$subs   = get_post_meta( $id, 'property_has_subunits', true );
		$from   = ( ! $rent ) && ( $subs || preg_match( '/from/i', $label ) );
		$area   = blockke_home_first_term( $id, 'property_area' );
		$city   = blockke_home_first_term( $id, 'property_city' );
		$comp   = blockke_home_first_term( $id, 'property_completion' );
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
		$project = blockke_home_first_term( $id, 'property_project' );
		$title   = ( $project && mb_strlen( $project->name ) <= 40 && ! preg_match( '/bedroom|apartments? on|for sale/i', $project->name ) )
			? html_entity_decode( $project->name, ENT_QUOTES, 'UTF-8' )
			: blockke_home_short_title( get_the_title( $id ) );
		return array(
			'id'     => $id,
			'url'    => get_permalink( $id ),
			'title'  => $title,
			'full'   => html_entity_decode( get_the_title( $id ), ENT_QUOTES, 'UTF-8' ),
			'thumb'  => (int) get_post_thumbnail_id( $id ),
			'price'  => $price ? ( ( $from ? 'From ' : '' ) . blockke_home_price( $price, $rent ) ) : 'Price on request',
			'beds'   => (int) get_post_meta( $id, 'property_bedrooms', true ),
			'baths'  => (int) get_post_meta( $id, 'property_bathrooms', true ),
			'size'   => (float) get_post_meta( $id, 'property_size', true ),
			'loc'    => implode( ', ', $loc ),
			'status' => $status,
			'rent'   => $rent,
		);
	}

	function blockke_home_listings( $action, $n ) {
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
			$pt  = blockke_home_first_term( $id, 'property_project' );
			$key = $pt ? 'p' . $pt->term_id : 'l' . $id;
			if ( isset( $projects[ $key ] ) ) {
				continue;
			}
			$projects[ $key ] = true;
			$ids[]            = (int) $id;
		}
		$out = array();
		foreach ( $ids as $id ) {
			$out[] = blockke_home_listing( $id );
		}
		return $out;
	}

	/** Development card from a property_project slug. */
	function blockke_home_development( $slug ) {
		$cfg  = blockke_home_config();
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
		$area = blockke_home_first_term( $master, 'property_area' );
		$comp = get_post_meta( $master, 'bke_completion_stated', true );
		if ( ! $comp ) {
			$ct   = blockke_home_first_term( $master, 'property_completion' );
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
			'price'   => $min_price ? 'From ' . blockke_home_price( $min_price ) : 'Prices on request',
			'mix'     => $mix,
			'comp'    => $comp,
			'thumb'   => (int) get_post_thumbnail_id( $master ),
			'url'     => $url,
			'mandate' => in_array( $slug, (array) $cfg['mandates'], true ),
		);
	}

	function blockke_home_areas() {
		$cfg = blockke_home_config();
		$out = array();
		foreach ( (array) $cfg['neighbourhoods'] as $slug ) {
			$t = get_term_by( 'slug', $slug, 'property_area' );
			if ( ! $t || (int) $t->count < 1 ) {
				continue;
			}
			$thumb = blockke_home_term_image( $t->term_id );
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
				'url'   => blockke_home_area_url( $t ),
				'thumb' => $thumb,
			);
		}
		return $out;
	}

	function blockke_home_articles( $n = 4 ) {
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

	function blockke_home_agent( $id ) {
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

	/* ------------------------------------------------------------------ CSS */

	function blockke_home_css() {
		return <<<'CSS'
.bkh{--navy:#0D2440;--navy-2:#16345C;--navy-3:#091A2E;--ink:#22303C;--slate:#46586A;--muted:#5F6D7E;--paper:#FBF9F6;--mist:#F4EEE4;--line:#E6DFD4;--line-2:#D5CBBB;--brass:#B98A44;--brass-d:#80602A;--brass-l:#DDB97F;--brass-tint:#F1E4CC;--on-navy:#C9D3DF;--wa:#1EBE5D;--font:"Montserrat",system-ui,-apple-system,"Segoe UI",Roboto,sans-serif;--display:"Instrument Serif",Georgia,"Times New Roman",serif;--max:1200px;--gutter:clamp(20px,4vw,48px);--sec:clamp(72px,9vw,128px);
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
/* hero */
.bkh .bkh-hero{position:relative;isolation:isolate;min-height:min(92vh,880px);display:flex;align-items:flex-end;color:#fff;background:var(--navy-3);overflow:hidden}
.bkh .bkh-hero-img{position:absolute;inset:0;z-index:-2;width:100%;height:100%;object-fit:cover;object-position:center 40%}
.bkh .bkh-hero::before{content:"";position:absolute;inset:0;z-index:-1;background:linear-gradient(180deg,rgba(9,26,46,.52) 0%,rgba(9,26,46,.18) 38%,rgba(9,26,46,.62) 72%,rgba(9,26,46,.9) 100%)}
.bkh .bkh-hero::after{content:"";position:absolute;inset:0;z-index:-1;background:linear-gradient(90deg,rgba(9,26,46,.55) 0%,rgba(9,26,46,0) 60%)}
.bkh .bkh-hero .bkh-wrap{position:relative;width:100%;padding-top:clamp(132px,20vh,200px);padding-bottom:clamp(44px,6vw,72px)}
.bkh .bkh-hero .bkh-eyebrow{color:var(--brass-l)}
.bkh .bkh-hero h1.bkh-display{font-size:clamp(46px,6.4vw,86px)!important;color:#fff!important;max-width:16ch;margin:16px 0 0;text-shadow:0 2px 24px rgba(0,0,0,.25)}
.bkh .bkh-hero .bkh-hero-sub{font-size:clamp(16px,1.3vw,19px);line-height:1.55;color:rgba(255,255,255,.86);max-width:54ch;margin:20px 0 0}
.bkh .bkh-hero-quick{display:flex;flex-wrap:wrap;gap:8px 18px;align-items:center;margin:26px 0 0;font:500 12.5px/1.4 var(--font);color:rgba(255,255,255,.62)}
.bkh .bkh-hero-quick a{color:rgba(255,255,255,.88);border-bottom:1px solid rgba(255,255,255,.28);padding-bottom:1px}
.bkh .bkh-hero-quick a:hover{color:#fff;border-color:var(--brass-l)}
.bkh .bkh-hero-cap{position:absolute;right:var(--gutter);bottom:14px;font:500 10.5px/1.3 var(--font);letter-spacing:.06em;color:rgba(255,255,255,.5);text-transform:uppercase}
/* search */
.bkh .bkh-search{margin-top:clamp(28px,4vw,40px);max-width:1040px}
.bkh .bkh-tabs{display:inline-flex;gap:4px;padding:4px;border-radius:999px;background:rgba(9,26,46,.55);backdrop-filter:blur(8px);-webkit-backdrop-filter:blur(8px);border:1px solid rgba(255,255,255,.14)}
.bkh .bkh-tab{appearance:none;border:0;background:transparent;color:rgba(255,255,255,.78);font:600 12.5px/1 var(--font);letter-spacing:.08em;text-transform:uppercase;padding:12px 20px;border-radius:999px;cursor:pointer;transition:background .2s,color .2s;white-space:nowrap;box-shadow:none}
.bkh .bkh-tab[aria-selected="true"]{background:#fff;color:var(--navy)}
.bkh .bkh-form{display:grid;grid-template-columns:1.5fr 1fr 1fr 1fr auto;align-items:stretch;margin-top:10px;background:#fff;border-radius:18px;box-shadow:0 24px 60px rgba(5,18,36,.35);overflow:hidden}
.bkh .bkh-form.is-offplan{grid-template-columns:1.4fr 1fr .9fr 1fr 1fr auto}
.bkh .bkh-field{position:relative;display:flex;flex-direction:column;justify-content:center;padding:14px 18px 12px;border-right:1px solid var(--line);min-width:0}
.bkh .bkh-field label{font:600 10px/1.2 var(--font);letter-spacing:.16em;text-transform:uppercase;color:var(--muted);margin-bottom:5px}
.bkh .bkh-field select{appearance:none;-webkit-appearance:none;width:100%;border:0;border-radius:0;background:transparent;box-shadow:none;height:auto;min-height:0;padding:0 22px 0 0;margin:0;font:500 15px/1.3 var(--font);color:var(--navy);cursor:pointer;outline:none}
.bkh .bkh-field::after{content:"";position:absolute;right:18px;bottom:19px;width:8px;height:8px;border-right:1.5px solid var(--muted);border-bottom:1.5px solid var(--muted);transform:rotate(45deg);pointer-events:none}
.bkh .bkh-field--completion{display:none}
.bkh .bkh-form.is-offplan .bkh-field--completion{display:flex}
.bkh .bkh-form.is-rent .bkh-field--completion{display:none}
.bkh .bkh-submit{appearance:none;border:0;margin:8px;padding:0 30px;border-radius:14px;background:var(--navy);color:#fff;font:600 12.5px/1 var(--font);letter-spacing:.14em;text-transform:uppercase;cursor:pointer;display:inline-flex;align-items:center;gap:10px;transition:background .2s}
.bkh .bkh-submit:hover{background:var(--navy-2)}
.bkh .bkh-submit svg{width:17px;height:17px}
/* proof strip */
.bkh .bkh-strip{padding:clamp(34px,4vw,48px) 0 clamp(30px,3.5vw,44px);border-bottom:1px solid var(--line)}
.bkh .bkh-stats{display:grid;grid-template-columns:repeat(4,1fr);gap:20px}
.bkh .bkh-stat{padding-left:18px;border-left:1px solid var(--line-2)}
.bkh .bkh-stat b{display:block;font:400 clamp(30px,3vw,40px)/1 var(--display);color:var(--navy);letter-spacing:-.01em}
.bkh .bkh-stat span{display:block;margin-top:8px;font:600 10.5px/1.4 var(--font);letter-spacing:.16em;text-transform:uppercase;color:var(--muted)}
/* developments */
.bkh .bkh-devs{display:grid;grid-template-columns:repeat(3,1fr);gap:28px}
.bkh .bkh-dev{display:block;position:relative;color:var(--ink)}
.bkh .bkh-dev-media{position:relative;aspect-ratio:4/3;border-radius:14px;overflow:hidden;background:var(--mist)}
.bkh .bkh-dev-media img,.bkh .bkh-dev-media .bkh-noimg{width:100%;height:100%;object-fit:cover;transition:transform .6s cubic-bezier(.2,.7,.2,1)}
.bkh .bkh-dev:hover .bkh-dev-media img{transform:scale(1.04)}
.bkh .bkh-tag{position:absolute;top:14px;left:14px;display:inline-flex;align-items:center;gap:6px;padding:7px 11px;border-radius:999px;background:rgba(13,36,64,.86);color:#fff;font:600 10px/1 var(--font);letter-spacing:.14em;text-transform:uppercase;backdrop-filter:blur(6px);white-space:nowrap}
.bkh .bkh-tag svg{width:11px;height:11px}
.bkh .bkh-tag--brass{background:var(--brass);color:#fff}
.bkh .bkh-dev-body{padding:18px 4px 0}
.bkh .bkh-dev-meta{font:500 12px/1.4 var(--font);letter-spacing:.04em;color:var(--muted);text-transform:uppercase}
.bkh .bkh-dev h3{font:600 18px/1.3 var(--font)!important;color:var(--navy)!important;margin-top:6px!important}
.bkh .bkh-dev-price{display:flex;align-items:baseline;justify-content:space-between;gap:12px;margin-top:10px;padding-top:10px;border-top:1px solid var(--line)}
.bkh .bkh-dev-price b{font:400 22px/1 var(--display);color:var(--navy);letter-spacing:-.01em}
.bkh .bkh-dev-price span{font:500 12.5px/1.3 var(--font);color:var(--slate)}
.bkh .bkh-dev-cta{display:inline-flex;align-items:center;gap:8px;margin-top:12px;font:600 11.5px/1 var(--font);letter-spacing:.12em;text-transform:uppercase;color:var(--brass-d)}
.bkh .bkh-dev:hover .bkh-dev-cta{color:var(--navy)}
/* listings */
.bkh .bkh-ltabs{display:flex;gap:28px;border-bottom:1px solid var(--line);margin-bottom:28px}
.bkh .bkh-ltab{appearance:none;border:0;background:transparent;padding:0 0 14px;margin-bottom:-1px;font:600 12.5px/1 var(--font);letter-spacing:.12em;text-transform:uppercase;color:var(--muted);border-bottom:2px solid transparent;cursor:pointer}
.bkh .bkh-ltab[aria-selected="true"]{color:var(--navy);border-color:var(--brass)}
.bkh .bkh-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:28px 24px}
.bkh .bkh-grid[hidden]{display:none}
.bkh .bkh-card{display:block;color:var(--ink)}
.bkh .bkh-card-media{position:relative;aspect-ratio:3/2;border-radius:12px;overflow:hidden;background:var(--mist)}
.bkh .bkh-card-media img,.bkh .bkh-card-media .bkh-noimg{width:100%;height:100%;object-fit:cover;transition:transform .6s cubic-bezier(.2,.7,.2,1)}
.bkh .bkh-card:hover .bkh-card-media img{transform:scale(1.04)}
.bkh .bkh-card-body{padding:14px 2px 0}
.bkh .bkh-card h3{font:600 15px/1.35 var(--font)!important;color:var(--navy)!important;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden}
.bkh .bkh-card-price{font:400 22px/1.1 var(--display);color:var(--navy);margin-top:6px;letter-spacing:-.01em}
.bkh .bkh-card-loc{font:500 12.5px/1.4 var(--font);color:var(--muted);margin-top:4px}
.bkh .bkh-specs{display:flex;gap:14px;margin-top:10px;padding-top:10px;border-top:1px solid var(--line);font:500 11.5px/1 var(--font);letter-spacing:.06em;text-transform:uppercase;color:var(--slate)}
.bkh .bkh-specs span b{font-weight:700;color:var(--navy)}
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
/* services */
.bkh .bkh-services{display:grid;grid-template-columns:repeat(3,1fr);gap:0 40px;border-top:1px solid var(--line-2)}
.bkh .bkh-service{display:flex;flex-direction:column;gap:8px;padding:26px 0;border-bottom:1px solid var(--line-2);color:var(--ink);position:relative}
.bkh .bkh-service h3{display:flex;align-items:center;justify-content:space-between;gap:12px;font:600 16px/1.3 var(--font)!important;color:var(--navy)!important}
.bkh .bkh-service h3 svg{width:18px;height:18px;flex:none;color:var(--brass);transition:transform .2s}
.bkh .bkh-service:hover h3 svg{transform:translateX(4px)}
.bkh .bkh-service p{font:400 14px/1.6 var(--font);color:var(--slate)}
.bkh .bkh-val{display:flex;align-items:center;justify-content:space-between;gap:24px;flex-wrap:wrap;margin-top:40px;padding:26px 30px;border-radius:16px;background:var(--navy);color:#fff}
.bkh .bkh-val h3{font:400 clamp(22px,2vw,28px)/1.2 var(--display)!important;color:#fff!important}
.bkh .bkh-val p{font:400 14px/1.5 var(--font);color:var(--on-navy);margin-top:6px}
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
.bkh .bkh-devs{grid-template-columns:repeat(2,1fr)}
.bkh .bkh-areas{grid-template-columns:repeat(4,1fr)}
.bkh .bkh-services{grid-template-columns:repeat(2,1fr)}
}
@media (max-width:900px){
.bkh .bkh-form,.bkh .bkh-form.is-offplan{grid-template-columns:1fr 1fr;border-radius:16px}
.bkh .bkh-field{border-bottom:1px solid var(--line);border-right:0}
.bkh .bkh-field--where{grid-column:1/-1}
.bkh .bkh-field--type{border-right:1px solid var(--line)}
.bkh .bkh-field--budget{grid-column:1/-1}
.bkh .bkh-form.is-offplan .bkh-field--budget{grid-column:auto;border-right:1px solid var(--line)}
.bkh .bkh-submit{grid-column:1/-1;margin:10px;min-height:52px;justify-content:center}
.bkh .bkh-stats{grid-template-columns:repeat(2,1fr);gap:22px 16px}
.bkh .bkh-dia-grid,.bkh .bkh-founder,.bkh .bkh-hw{grid-template-columns:1fr}
.bkh .bkh-founder{gap:32px}
.bkh .bkh-portrait{max-width:440px}
.bkh .bkh-grid{grid-template-columns:repeat(2,1fr);gap:22px 16px}
}
@media (max-width:767px){
.bkh{font-size:15px}
.bkh .bkh-hero{min-height:0}
.bkh .bkh-hero .bkh-wrap{padding-top:124px;padding-bottom:28px}
.bkh .bkh-hero h1.bkh-display{font-size:clamp(36px,10.2vw,48px)!important;max-width:none}
.bkh .bkh-hero .bkh-hero-sub{font-size:15.5px;margin-top:14px}
.bkh .bkh-hero-quick{display:none}
.bkh .bkh-hero{flex-direction:column;justify-content:flex-end}
.bkh .bkh-hero-cap{position:static;display:block;width:100%;padding:0 var(--gutter) 12px;margin-top:-10px;text-align:right}
.bkh .bkh-tabs{display:flex;width:100%}
.bkh .bkh-tab{flex:1;padding:12px 6px;text-align:center;font-size:11.5px;letter-spacing:.06em}
.bkh .bkh-hero-img{object-position:62% 40%}
.bkh .bkh-strip{padding-top:28px}
.bkh .bkh-head{margin-bottom:22px}
.bkh .bkh-head .bkh-more{order:3}
.bkh .bkh-devs{display:flex;gap:14px;overflow-x:auto;scroll-snap-type:x mandatory;padding:0 var(--gutter) 10px;margin:0 calc(-1*var(--gutter));scrollbar-width:none}
.bkh .bkh-devs::-webkit-scrollbar{display:none}
.bkh .bkh-dev{flex:0 0 78%;scroll-snap-align:start}
.bkh .bkh-grid{grid-template-columns:1fr;gap:22px}
.bkh .bkh-grid .bkh-card:nth-child(n+5){display:none}
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
@media (prefers-reduced-motion:reduce){.bkh *{transition:none!important}}
CSS;
	}

	/* ------------------------------------------------------------------ JS */

	function blockke_home_js() {
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
  /* listing tabs */
  var lt=root.querySelectorAll('.bkh-ltab');
  for(var t=0;t<lt.length;t++){lt[t].addEventListener('click',function(){
    var id=this.getAttribute('aria-controls');
    for(var a=0;a<lt.length;a++){lt[a].setAttribute('aria-selected',lt[a]===this?'true':'false');}
    var grids=root.querySelectorAll('.bkh-grid');for(var g=0;g<grids.length;g++){grids[g].hidden=grids[g].id!==id;}
    var foot=root.querySelectorAll('.bkh-lfoot a');for(var f=0;f<foot.length;f++){foot[f].hidden=foot[f].getAttribute('data-for')!==id;}
  });}
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

	function blockke_home_svg( $name ) {
		$svgs = array(
			'wa'     => '<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38a9.87 9.87 0 0 0 4.74 1.21c5.46 0 9.91-4.45 9.91-9.91S17.5 2 12.04 2zm0 18.15c-1.48 0-2.93-.4-4.2-1.15l-.3-.18-3.12.82.83-3.04-.2-.31a8.2 8.2 0 0 1-1.26-4.38c0-4.54 3.7-8.24 8.25-8.24 4.54 0 8.24 3.7 8.24 8.24 0 4.55-3.7 8.24-8.24 8.24zm4.52-6.16c-.25-.12-1.47-.72-1.69-.81-.23-.08-.39-.12-.56.12-.16.25-.64.81-.78.97-.14.17-.29.19-.54.06-.25-.12-1.05-.39-1.99-1.23-.74-.66-1.23-1.47-1.38-1.72-.14-.25-.02-.38.11-.51.11-.11.25-.29.37-.43.12-.14.16-.25.25-.41.08-.17.04-.31-.02-.43-.06-.12-.56-1.34-.76-1.84-.2-.48-.41-.42-.56-.43h-.48c-.17 0-.43.06-.66.31-.22.25-.86.85-.86 2.07 0 1.22.89 2.4 1.01 2.56.12.17 1.75 2.67 4.23 3.74.59.26 1.05.41 1.41.52.59.19 1.13.16 1.56.1.48-.07 1.47-.6 1.67-1.18.21-.58.21-1.07.14-1.18-.06-.1-.22-.16-.47-.28z"/></svg>',
			'phone'  => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.8 19.8 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.8 19.8 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.13.96.36 1.9.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.91.34 1.85.57 2.81.7A2 2 0 0 1 22 16.92z"/></svg>',
			'arrow'  => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>',
			'search' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>',
			'check'  => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m5 12 4.5 4.5L19 7"/></svg>',
		);
		return isset( $svgs[ $name ] ) ? $svgs[ $name ] : '';
	}

	/* ------------------------------------------------------------------ render */

	function blockke_home_render() {
		$cfg   = blockke_home_config();
		$u     = 'blockke_home_url';
		$stats = blockke_home_stats();
		$wa_n  = preg_replace( '/\D+/', '', $cfg['whatsapp'] );
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
				$quick .= '<a href="' . esc_url( blockke_home_area_url( $t ) ) . '">' . esc_html( $t->name ) . '</a>';
			}
		}
		$h .= '<section class="bkh-hero" aria-label="Search Nairobi property">';
		$h .= blockke_home_img( $cfg['hero_image'], 'full', 'bkh-hero-img', '', true, '100vw' );
		$h .= '<div class="bkh-wrap">';
		$h .= '<span class="bkh-eyebrow">' . esc_html( $cfg['hero_eyebrow'] ) . '</span>';
		$h .= '<h1 class="bkh-display">' . esc_html( $cfg['hero_title'] ) . '</h1>';
		$h .= '<p class="bkh-hero-sub">' . esc_html( $cfg['hero_sub'] ) . '</p>';
		$h .= '<div class="bkh-search">';
		$h .= '<div class="bkh-tabs" role="tablist" aria-label="What are you looking for?">';
		$h .= '<button type="button" class="bkh-tab" role="tab" data-mode="buy" aria-selected="true">Buy</button>';
		$h .= '<button type="button" class="bkh-tab" role="tab" data-mode="rent" aria-selected="false">Rent</button>';
		$h .= '<button type="button" class="bkh-tab" role="tab" data-mode="offplan" aria-selected="false">Off-plan</button>';
		$h .= '</div>';
		$h .= '<form class="bkh-form bke-cy-has" data-mode="buy" method="get" action="' . esc_url( $u( 'search' ) ) . '">';
		$h .= '<input type="hidden" name="property_action_category[]" value="for-sale">';
		$h .= '<div class="bkh-field bkh-field--where"><label for="bkh-where">Where</label><select id="bkh-where" name="property_area[]">' . $area_opts . '</select></div>';
		$h .= '<div class="bkh-field bkh-field--type"><label for="bkh-type">Type</label><select id="bkh-type" name="property_category[]"><option value="">Any type</option><option value="apartments">Apartments</option><option value="houses-residential">Houses</option><option value="villas-residential">Villas</option><option value="studio-apartments">Studios</option><option value="land">Land</option><option value="offices">Offices</option><option value="warehouses">Warehouses</option></select></div>';
		$h .= '<div class="bkh-field bkh-field--beds"><label for="bkh-beds">Bedrooms</label><select id="bkh-beds" name="bedrooms"><option value="">Any</option><option value="studio">Studio</option><option value="1">1 bed</option><option value="2">2 bed</option><option value="3">3 bed</option><option value="4">4 bed</option><option value="5">5+ bed</option></select></div>';
		$h .= '<div class="bkh-field bkh-field--budget"><label for="bkh-budget">Budget</label><select id="bkh-budget" name="price_max"><option value="">Any budget</option><option value="5000000">Under KES 5M</option><option value="8000000">Under KES 8M</option><option value="12000000">Under KES 12M</option><option value="20000000">Under KES 20M</option><option value="35000000">Under KES 35M</option><option value="60000000">Under KES 60M</option></select></div>';
		$h .= '<div class="bkh-field bkh-field--completion"><label for="bkh-completion">Completion</label><select id="bkh-completion" name="completion"><option value="any">All projects</option><option value="ready-now">Ready now</option><option value="2026">2026</option><option value="2027">2027</option><option value="2028">2028</option><option value="2029-plus">2029+</option></select></div>';
		$h .= '<button type="submit" class="bkh-submit">' . blockke_home_svg( 'search' ) . 'Search</button>';
		$h .= '</form>';
		$h .= '</div>';
		if ( $quick ) {
			$h .= '<div class="bkh-hero-quick"><span>Popular:</span>' . $quick . '</div>';
		}
		$h .= '</div>';
		if ( ! empty( $cfg['hero_caption'] ) ) {
			$h .= '<span class="bkh-hero-cap">' . esc_html( $cfg['hero_caption'] ) . '</span>';
		}
		$h .= '</section>';

		/* ---- proof strip ---- */
		$h .= '<section class="bkh-strip" aria-label="Block in numbers"><div class="bkh-wrap"><div class="bkh-stats">';
		$h .= '<div class="bkh-stat"><b>' . esc_html( blockke_home_plus( $stats['sale'] ) ) . '</b><span>Homes for sale</span></div>';
		$h .= '<div class="bkh-stat"><b>' . esc_html( blockke_home_plus( $stats['projects'] ) ) . '</b><span>Developments tracked</span></div>';
		$h .= '<div class="bkh-stat"><b>' . esc_html( blockke_home_plus( $stats['areas'] ) ) . '</b><span>Nairobi neighbourhoods</span></div>';
		$h .= '<div class="bkh-stat"><b>Same day</b><span>Replies from a real person</span></div>';
		$h .= '</div></div></section>';

		/* ---- developments ---- */
		$devs = array();
		foreach ( (array) $cfg['developments'] as $slug ) {
			$d = blockke_home_development( $slug );
			if ( $d ) {
				$devs[] = $d;
			}
		}
		if ( $devs ) {
			$h .= '<section class="bkh-sec" id="developments"><div class="bkh-wrap">';
			$h .= '<div class="bkh-head"><div><span class="bkh-eyebrow">New developments</span><h2 class="bkh-display">Buy before it&rsquo;s built, with the homework done.</h2>';
			$h .= '<p class="bkh-lede">Every project here has been through our checks &mdash; developer record, title, approvals, payment terms &mdash; before it earned a place on the site. On some we sell directly for the developer.</p></div>';
			$h .= '<a class="bkh-more" href="' . esc_url( $u( 'offplan' ) ) . '">All off-plan projects' . blockke_home_svg( 'arrow' ) . '</a></div>';
			$h .= '<div class="bkh-devs">';
			foreach ( $devs as $d ) {
				$h .= '<a class="bkh-dev" href="' . esc_url( $d['url'] ) . '">';
				$h .= '<div class="bkh-dev-media">' . blockke_home_img( $d['thumb'], 'large', '', $d['name'] . ( $d['area'] ? ', ' . $d['area'] : '' ), false, '(max-width: 767px) 80vw, (max-width: 1100px) 50vw, 33vw' );
				if ( $d['mandate'] ) {
					$h .= '<span class="bkh-tag bkh-tag--brass">' . blockke_home_svg( 'check' ) . 'Authorised sales agent</span>';
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
				$h .= '<span class="bkh-dev-cta">Prices &amp; floor plans' . blockke_home_svg( 'arrow' ) . '</span>';
				$h .= '</div></a>';
			}
			$h .= '</div></div></section>';
		}

		/* ---- worth a viewing ---- */
		$sale = blockke_home_listings( 'for-sale', (int) $cfg['listings_count'] );
		$rent = blockke_home_listings( 'for-rent', (int) $cfg['listings_count'] );
		$card = function ( $l ) {
			$o  = '<a class="bkh-card" href="' . esc_url( $l['url'] ) . '" title="' . esc_attr( $l['full'] ) . '">';
			$o .= '<div class="bkh-card-media">' . blockke_home_img( $l['thumb'], 'large', '', $l['full'], false, '(max-width: 767px) 100vw, (max-width: 900px) 50vw, 33vw' );
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
			$h .= '<section class="bkh-sec bkh-sec--white" id="listings"><div class="bkh-wrap">';
			$h .= '<div class="bkh-head"><div><span class="bkh-eyebrow">Hand-picked</span><h2 class="bkh-display">Worth a viewing this week.</h2>';
			$h .= '<p class="bkh-lede">The current shortlist, chosen by the team and refreshed as the market moves.</p></div></div>';
			$h .= '<div class="bkh-ltabs" role="tablist"><button type="button" class="bkh-ltab" role="tab" aria-selected="true" aria-controls="bkh-sale">For sale</button><button type="button" class="bkh-ltab" role="tab" aria-selected="false" aria-controls="bkh-rent">For rent</button></div>';
			$h .= '<div class="bkh-grid" id="bkh-sale">';
			foreach ( $sale as $l ) {
				$h .= $card( $l );
			}
			$h .= '</div><div class="bkh-grid" id="bkh-rent" hidden>';
			foreach ( $rent as $l ) {
				$h .= $card( $l );
			}
			$h .= '</div>';
			$h .= '<div class="bkh-lfoot"><a class="bkh-btn bkh-btn--navy" data-for="bkh-sale" href="' . esc_url( $u( 'for_sale' ) ) . '">Browse all ' . esc_html( blockke_home_plus( $stats['sale'] ) ) . ' homes for sale</a><a class="bkh-btn bkh-btn--navy" data-for="bkh-rent" href="' . esc_url( $u( 'for_rent' ) ) . '" hidden>Browse all rentals</a></div>';
			$h .= '</div></section>';
		}

		/* ---- diaspora ---- */
		$h .= '<section class="bkh-sec bkh-sec--navy bkh-dia" id="diaspora" data-where="home_diaspora"><div class="bkh-wrap"><div class="bkh-dia-grid">';
		$h .= '<div><span class="bkh-eyebrow">Buying from abroad</span><h2 class="bkh-display">Most of our buyers aren&rsquo;t in Nairobi. That&rsquo;s the point.</h2>';
		$h .= '<p class="bkh-lede">From London, Dallas, Dubai or Toronto you can&rsquo;t walk the street or sit across from the developer. We do &mdash; and you see everything on video before you commit a shilling.</p>';
		$h .= '<div class="bkh-ctas"><a class="bkh-btn bkh-btn--white" href="' . esc_url( blockke_home_wa( 'buying from abroad.' ) ) . '" target="_blank" rel="noopener">' . blockke_home_svg( 'wa' ) . 'WhatsApp Loyd</a><a class="bkh-btn bkh-btn--ghostw" href="' . esc_url( $u( 'diaspora_guide' ) ) . '">Read the diaspora guide</a></div>';
		$h .= '<div class="bkh-dia-proof"><span>UK</span><span>USA</span><span>UAE</span><span>Canada</span><span>Australia</span>Most of the homes we close are bought from abroad.</div>';
		$h .= '</div>';
		$h .= '<ol class="bkh-steps">';
		$h .= '<li class="bkh-step"><i>1</i><div><h3>Tell us your brief</h3><p>Budget, area, timeline and whether it&rsquo;s to live in or let out &mdash; on WhatsApp, in your time zone.</p></div></li>';
		$h .= '<li class="bkh-step"><i>2</i><div><h3>We shortlist and video-view</h3><p>Every unit walked, filmed and priced against real comparables, with the developer&rsquo;s record and approvals checked.</p></div></li>';
		$h .= '<li class="bkh-step"><i>3</i><div><h3>You buy with the checks done</h3><p>Title search, payment plan and contract reviewed; we negotiate on your side. The seller pays our fee, not you.</p></div></li>';
		$h .= '</ol>';
		$h .= '</div></div></section>';

		/* ---- know your block ---- */
		$areas = blockke_home_areas();
		if ( $areas ) {
			$h .= '<section class="bkh-sec bkh-sec--mist" id="neighbourhoods"><div class="bkh-wrap">';
			$h .= '<div class="bkh-head"><div><span class="bkh-eyebrow">Neighbourhoods</span><h2 class="bkh-display">Know your block before you buy it.</h2>';
			$h .= '<p class="bkh-lede">Prices, commutes, schools, noise &mdash; everything the listings won&rsquo;t tell you, neighbourhood by neighbourhood.</p></div>';
			$h .= '<a class="bkh-more" href="' . esc_url( $u( 'neighbourhoods' ) ) . '">All neighbourhoods' . blockke_home_svg( 'arrow' ) . '</a></div>';
			$h .= '<div class="bkh-areas">';
			foreach ( $areas as $a ) {
				$h .= '<a class="bkh-area" href="' . esc_url( $a['url'] ) . '">' . blockke_home_img( $a['thumb'], 'large', '', $a['name'], false, '(max-width: 767px) 50vw, 20vw' );
				$h .= '<div><h3>' . esc_html( $a['name'] ) . '</h3><span>' . esc_html( number_format( $a['count'] ) ) . ' ' . ( 1 === $a['count'] ? 'home' : 'homes' ) . '</span></div></a>';
			}
			$h .= '</div></div></section>';
		}

		/* ---- founder ---- */
		$founder = blockke_home_agent( $cfg['founder_agent'] );
		$team    = array();
		foreach ( (array) $cfg['team_agents'] as $aid ) {
			$ag = blockke_home_agent( $aid );
			if ( $ag ) {
				$team[] = $ag;
			}
		}
		$h .= '<section class="bkh-sec bkh-sec--white" id="loyd" data-where="home_founder"><div class="bkh-wrap"><div class="bkh-founder">';
		$h .= '<figure class="bkh-portrait">' . blockke_home_img( $cfg['founder_image'], 'large', '', 'Loyd Mokaya, founder of Block Real Estate', false, '(max-width: 900px) 90vw, 40vw' ) . '<figcaption>Loyd Mokaya · Founder</figcaption></figure>';
		$h .= '<div><span class="bkh-eyebrow">Loyd from Block</span><h2 class="bkh-display">You&rsquo;ll deal with a person, not a portal.</h2>';
		$h .= '<p class="bkh-quote">&ldquo;I started Block because buying in Nairobi shouldn&rsquo;t feel like a gamble.&rdquo;</p>';
		$h .= '<p class="bkh-body">So we check everything first &mdash; the market, the title, the street &mdash; and tell you straight, even when the honest answer is &ldquo;not this one&rdquo;. Nairobi moves fast and unevenly; a price in Kilimani tells you nothing about Lavington. That&rsquo;s why we work it block by block, and why when you message us you get a person who has stood in the building.</p>';
		$h .= '<div class="bkh-sign">Loyd Mokaya<small>Founder, Block Real Estate</small></div>';
		$h .= '<div class="bkh-ctas"><a class="bkh-btn bkh-btn--navy" href="' . esc_url( blockke_home_wa( 'a property.' ) ) . '" target="_blank" rel="noopener">' . blockke_home_svg( 'wa' ) . 'WhatsApp Loyd</a><a class="bkh-btn bkh-btn--ghost" href="' . esc_url( $u( 'consult' ) ) . '">Book a free consultation</a></div>';
		if ( $team ) {
			$h .= '<div class="bkh-team"><div class="bkh-avatars">';
			foreach ( $team as $ag ) {
				$h .= blockke_home_img( $ag['thumb'], 'thumbnail', '', $ag['name'], false, '44px' );
			}
			$h .= '</div><p>' . esc_html( implode( ', ', array_map( function ( $ag ) { return preg_replace( '/\s.*$/', '', $ag['name'] ); }, $team ) ) ) . ' and the rest of the team &mdash; <a href="' . esc_url( $u( 'team' ) ) . '">meet them</a></p></div>';
		}
		$h .= '</div></div></div></section>';

		/* ---- services ---- */
		$services = array(
			array( 'Buy a home', 'Vetted listings, comparables run, title checked. You negotiate from facts.', $u( 'for_sale' ) ),
			array( 'Rent a home', 'A place you&rsquo;d actually want to live in, with a lease that holds up.', $u( 'for_rent' ) ),
			array( 'Sell or let your property', 'An honest valuation, proper marketing, and buyers or tenants vetted before they view.', $u( 'sell' ) ),
			array( 'Property management', 'Rent collection, repairs, inspections, statements &mdash; the returns without the 2am calls.', $u( 'management' ) ),
			array( 'Off-plan &amp; investment advisory', 'Which project, which unit, which payment plan &mdash; and which to walk away from.', $u( 'advisory' ) ),
			array( 'Build &amp; design', 'Client-side project management, architecture and interiors through Block Advisory.', $u( 'build' ) ),
		);
		$h .= '<section class="bkh-sec" id="services"><div class="bkh-wrap">';
		$h .= '<div class="bkh-head"><div><span class="bkh-eyebrow">Services</span><h2 class="bkh-display">A service for every move.</h2><p class="bkh-lede">Buy, rent, sell, let, manage or build. One team, one standard throughout.</p></div></div>';
		$h .= '<div class="bkh-services">';
		foreach ( $services as $s ) {
			$h .= '<a class="bkh-service" href="' . esc_url( $s[2] ) . '"><h3>' . $s[0] . blockke_home_svg( 'arrow' ) . '</h3><p>' . $s[1] . '</p></a>';
		}
		$h .= '</div>';
		$h .= '<div class="bkh-val"><div><h3>Own a property in Nairobi?</h3><p>Tell us about it and we&rsquo;ll tell you what it&rsquo;s worth &mdash; before we list a thing.</p></div><a class="bkh-btn bkh-btn--white" href="' . esc_url( $u( 'valuation' ) ) . '">Get a free valuation</a></div>';
		$h .= '</div></section>';

		/* ---- homework ---- */
		$arts = blockke_home_articles( 4 );
		if ( $arts ) {
			$lead = array_shift( $arts );
			$h   .= '<section class="bkh-sec bkh-sec--white" id="homework"><div class="bkh-wrap">';
			$h   .= '<div class="bkh-head"><div><span class="bkh-eyebrow">The homework</span><h2 class="bkh-display">What we&rsquo;d tell you over coffee.</h2><p class="bkh-lede">Market data, legal landmines and neighbourhood deep-dives, written by the people doing the deals.</p></div>';
			$h   .= '<a class="bkh-more" href="' . esc_url( $u( 'blog' ) ) . '">All articles' . blockke_home_svg( 'arrow' ) . '</a></div>';
			$h   .= '<div class="bkh-hw">';
			$h   .= '<a class="bkh-lead" href="' . esc_url( $lead['url'] ) . '"><div class="bkh-lead-media">' . blockke_home_img( $lead['thumb'], 'large', '', $lead['title'], false, '(max-width: 900px) 100vw, 55vw' ) . '</div>';
			$h   .= '<span class="bkh-cat">' . esc_html( $lead['cat'] ) . '</span><h3>' . esc_html( $lead['title'] ) . '</h3><p>' . esc_html( $lead['ex'] ) . '</p><time>' . esc_html( $lead['date'] ) . '</time></a>';
			$h   .= '<div class="bkh-list">';
			foreach ( $arts as $a ) {
				$h .= '<a class="bkh-item" href="' . esc_url( $a['url'] ) . '"><div class="bkh-item-media">' . blockke_home_img( $a['thumb'], 'medium', '', $a['title'], false, '112px' ) . '</div><div><span class="bkh-cat">' . esc_html( $a['cat'] ) . '</span><h3>' . esc_html( $a['title'] ) . '</h3><time>' . esc_html( $a['date'] ) . '</time></div></a>';
			}
			$h .= '</div></div></div></section>';
		}

		/* ---- closing ---- */
		$h .= '<section class="bkh-sec bkh-sec--navy bkh-close" id="contact" data-where="home_close"><div class="bkh-wrap">';
		$h .= '<span class="bkh-eyebrow">Start here</span><h2 class="bkh-display">Your next move starts with one message.</h2>';
		$h .= '<p class="bkh-lede">A real person replies the same day. No bots, no runaround.</p>';
		$h .= '<div class="bkh-ctas"><a class="bkh-btn bkh-btn--white" href="' . esc_url( blockke_home_wa( 'my next move.' ) ) . '" target="_blank" rel="noopener">' . blockke_home_svg( 'wa' ) . 'WhatsApp us</a><a class="bkh-btn bkh-btn--ghostw" href="' . esc_attr( $tel ) . '">' . blockke_home_svg( 'phone' ) . 'Call ' . esc_html( $cfg['phone_label'] ) . '</a></div>';
		$h .= '<p class="bkh-alt">Prefer a sit-down? <a href="' . esc_url( $u( 'consult' ) ) . '">Book a free consultation</a> &middot; Mon&ndash;Sat 07:00&ndash;21:00</p>';
		$h .= '</div></section>';

		/* ---- sticky bar (phones) ---- */
		$bar = '<div class="bkh-bar" data-where="home_sticky" aria-label="Contact Block"><a class="bkh-bar-wa" href="' . esc_url( blockke_home_wa( 'a property.' ) ) . '" target="_blank" rel="noopener">' . blockke_home_svg( 'wa' ) . 'WhatsApp</a><a class="bkh-bar-call" href="' . esc_attr( $tel ) . '">' . blockke_home_svg( 'phone' ) . 'Call us</a></div>';

		$js_cfg = array( 'endpoint' => esc_url_raw( rest_url( 'block/v1/contact-click' ) ) );
		$out    = '<!-- BLOCK homepage v' . blockke_home_ver() . ' -->';
		$out   .= '<link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&amp;display=swap">';
		$out   .= '<style id="bkh-css">' . blockke_home_css() . '</style>';
		$out   .= '<div class="bkh" id="bkh" data-where="home_hero">' . $h . '</div>' . $bar;
		$out   .= '<script id="bkh-cfg">window.BLOCKHOME=' . wp_json_encode( $js_cfg, JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS ) . ';</script>';
		$out   .= '<script id="bkh-js">' . blockke_home_js() . '</script>';
		return $out;
	}

	/**
	 * Content stamp: the newest change to any listing, article or agent. Part of every cache key, so the
	 * page rebuilds itself after an edit without hooks (this code only runs where the shortcode renders).
	 */
	function blockke_home_stamp() {
		static $stamp = null;
		if ( null !== $stamp ) {
			return $stamp;
		}
		global $wpdb;
		$stamp = (string) $wpdb->get_var( "SELECT MAX(post_modified_gmt) FROM {$wpdb->posts} WHERE post_type IN ('estate_property','post','estate_agent') AND post_status = 'publish'" );
		$stamp = substr( md5( blockke_home_ver() . '|' . $stamp . '|' . wp_json_encode( get_option( 'blockke_homepage', array() ) ) ), 0, 12 );
		return $stamp;
	}

	function blockke_home_output() {
		$cfg   = blockke_home_config();
		$key   = 'blockke_home_v2_' . blockke_home_stamp();
		$skip  = isset( $_GET['bkhome_nocache'] ) || isset( $_GET['elementor-preview'] ) || is_preview();
		$cache = $skip ? false : get_transient( $key );
		if ( is_string( $cache ) && '' !== $cache ) {
			return $cache;
		}
		try {
			$html = blockke_home_render();
		} catch ( Throwable $e ) {
			error_log( 'BLOCK homepage v2: ' . $e->getMessage() );
			return '<div style="padding:120px 20px 60px;text-align:center;font-family:Montserrat,sans-serif"><h1 style="font-size:32px">Block Real Estate</h1><p>Nairobi property, block by block.</p><p><a href="' . esc_url( home_url( '/for-sale/' ) ) . '">Homes for sale</a> · <a href="' . esc_url( home_url( '/for-rent/' ) ) . '">Rentals</a> · <a href="' . esc_url( home_url( '/contact-us/' ) ) . '">Contact</a></p></div>';
		}
		if ( ! $skip ) {
			set_transient( $key, $html, max( 1, (int) $cfg['cache_minutes'] ) * MINUTE_IN_SECONDS );
		}
		return $html;
	}
}

echo blockke_home_output();
