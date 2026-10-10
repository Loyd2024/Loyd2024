<?php
/* marker line - keeps the connector from Markdown-converting this snippet; harmless in PHP */
/* <div> markup marker - the connector stores content as-is only when it sees a block-level HTML tag; harmless in PHP */
/**
 * BLOCK - Development property page + payment calculators v1.3 (2026-10-08)
 *
 * 1) CLASSIC PROPERTY PAGES (the theme's own template)
 *    PAYMENT CALCULATORS on every for-sale property page:
 *    - Off-plan listings (Property Status "Off-Plan / Ongoing", completion not "Ready now"): the theme's
 *      mortgage calculator (#accordion_property_payment_calculator) is hidden and a payment-plan
 *      instalment calculator takes its place: deposit, then monthly or quarterly instalments up to
 *      completion. It fills itself from the listing: price, the deposit % and reservation fee in the
 *      description's "Payment Plan" section, and the completion date (bke_completion_stated, the
 *      "Completion" / "Occupation" row, or the Completion year).
 *    - Complete listings keep the theme's mortgage calculator.
 *    AMENITIES AND NEARBY PLACES on every classic property page:
 *    - Features & Amenities (#accordion_features_details) shows up to six amenity tiles (pool, gym, sky
 *      lounge...) and the rest as one checklist, instead of a list per feature group (config
 *      classic_amenities).
 *    - Address (#accordion_property_address) adds "What's nearby": the nearest business districts,
 *      malls, schools and universities, hospitals, parks and airports with straight-line distances from
 *      the map pin (blockke_dev_landmarks(), Nairobi only), or the listing's own "Places nearby" box
 *      (config classic_nearby).
 *    Both are moved into place by a small script; without JavaScript the theme's own content stays.
 *
 * 2) DEVELOPMENT LAYOUT (opt-in per listing): a landing-page layout for new developments that is still a
 *    normal block.ke property page: same URL, the site's header, menu and footer, Rank Math SEO, search,
 *    maps and listing grids. Everything comes from the listing, so editing the property updates the page:
 *      hero (title, area, status, lowest unit price, completion, featured image, 2-field enquiry form),
 *      key facts ("at a Glance" / "Project Snapshot" rows + unit sizes), residences (the Unit / Size /
 *      Price table in the description, linked to the sub-unit listings; else the sub-units), amenities
 *      (up to six feature tiles plus a checklist, from the Amenities section, else from Features &
 *      Amenities), payment plan (instalment calculator for off-plan, mortgage calculator for complete),
 *      location (map + places, else distances to nearby places), FAQ, similar developments in the same
 *      area, the listing's agent, and the full description under "Read more".
 *    Turn it on: Edit property > "Development page" box > Layout: On.
 *    AD LANDING PAGES (config landing_pages): the same content as a focused page for Google Ads and other
 *    campaigns, with its own slim header and footer instead of the site's menu, no breadcrumbs, share
 *    button, similar listings or links out, and noindex. On phones the photo carries the name, homes,
 *    street, price, completion and deposit, and the whole form fits the first screen of an iPhone with its
 *    browser bars (390x664). Any for-sale listing: its URL + ?lp=1. A tidy URL: a page with a custom field
 *    bke_lp_listing = the listing ID (e.g. /lp/santorini-residences/). Tracking (Google Ads conversions,
 *    GA4, Meta, Zoho) runs as on every page, through wp_head and wp_footer.
 *    Every listing with units: option blockke_dev_page['auto'] = true.
 *    Preview while logged in: any property URL + ?bke_layout=1 (?bke_layout=0 shows the classic page).
 *    Enquiries: POST /wp-json/block/v1/development-lead - emails the price-check popup's notify list
 *    plus the listing's agent, forwards to Zoho through blockke_bp_send_zoho() once that snippet's tokens
 *    are set, keeps the last 200 in option blockke_dev_leads, and fires the Google Ads lead conversion
 *    (window.blockkeLeadConversion), GA4 generate_lead and Meta Lead. After a quick (name + phone)
 *    enquiry, three optional one-tap questions (which home, when, live in or invest) add to the same
 *    lead and email a "More details" note; the full form asks the timeline too.
 *
 * Config: option blockke_dev_page (array) overrides any key of blockke_dev_config().
 * Rollback: deactivate this snippet; every page returns to the theme's own template and calculator.
 * Created by Claude session 2026-10-07.
 * v1.1 (2026-10-08): warmer colours (paper and sand backgrounds, navy bands, brass accents), serif
 * headings (config display_font), and amenities as feature tiles plus an essentials checklist.
 * v1.2 (2026-10-08): the amenity tiles and checklist on classic property pages too, and distances to
 * nearby places under Address (and in the development layout's Location when the description lists none).
 * v1.3 (2026-10-08): ad landing pages, and a "Who it suits best" section with the investor view, read from
 * the description's "Who ... suits" and "... for Investors" sections. Optional questions after a quick
 * enquiry, a timeline question, international phone examples for visitors abroad, and the visitor's
 * name in the thank-you heading kept in the heading font. Photo captions from the Media Library: on the
 * gallery and in the viewer, and photos captioned with an amenity ("Indoor heated pool") as a photo strip
 * in Amenities (swipe on phones, full rows on desktop).
 * v1.3.1 (2026-10-10): enquiries from visitors who use their browser's AutoFill are no longer dropped (the
 * hidden anti-spam field was called "company", which AutoFill fills in; it now has a name AutoFill leaves
 * alone and is not drawn at all). Each enquiry is saved before Zoho and the email, so a slow or failing
 * step can't lose it. Anti-spam catches are kept in option blockke_dev_spam (last 30), and a failed send
 * shows its error code.
 */

if ( ! function_exists( 'blockke_dev_config' ) ) {

	function blockke_dev_config() {
		static $cfg = null;
		if ( null !== $cfg ) {
			return $cfg;
		}
		$defaults = array(
			'enabled'            => true,  // development layout
			'auto'               => false, // true: every listing with units gets the layout unless switched off
			'offplan_calculator' => true,  // instalment calculator instead of the mortgage one on off-plan pages
			'classic_amenities'  => true,  // classic property pages: amenity tiles and a checklist in Features & Amenities
			'classic_nearby'     => true,  // classic property pages: distances to nearby places under Address
			'landing_pages'      => true,  // ad landing pages: {listing URL}?lp=1, or a page with a bke_lp_listing field
			'logo'               => '',    // landing page header logo URL; '' uses the theme logo
			'address'            => 'Upper Hill Gardens, Suite D15, Upper Hill, Nairobi',
			'brand'              => 'Block',
			'brand_full'         => 'Block Real Estate',
			'phone'              => '+254725937686',
			'phone_display'      => '0725 937 686',
			'whatsapp'           => '254725937686',
			'email'              => 'sales@block.ke',
			'notify_emails'      => array(), // empty: the price-check popup's list, else loyd@block.ke
			'copy_agent'         => true,
			'currency'           => 'KES',
			'default_deposit'    => 20,   // instalment calculator, when the listing states none
			'mortgage_rate'      => 13,   // % a year; Kenyan bank mortgages, adjust as rates move
			'mortgage_years'     => 20,
			'mortgage_deposit'   => 20,
			'count_views'        => true, // keep the theme's view statistics counting on development pages
			'price_check_popup'  => false, // the site-wide price-check popup on development pages (they carry their own forms)
			'display_font'       => 'Instrument Serif', // headings: a Google Font with an italic; '' keeps the site font
			'similar_count'      => 4,
			'why'                => array(
				array( 'Independent advice', 'We compare developments, layouts and payment plans side by side, so you choose with clarity rather than pressure.' ),
				array( 'Checks before you commit', 'We help you review the developer, approvals and sale documents before you reserve or pay a deposit.' ),
				array( 'One advisor to handover', 'From reservation and payments to construction updates and handover, the same advisor stays with you.' ),
			),
			'disclaimer'         => "All information is provided in good faith from the developer's published material and is subject to change without notice. Prices, sizes, availability, payment terms and completion dates should be confirmed before you commit. Images may be artist's impressions. Block Real Estate does not provide financial or legal advice.",
		);
		$saved = get_option( 'blockke_dev_page', array() );
		if ( ! is_array( $saved ) ) {
			$saved = array();
		}
		$cfg = array_merge( $defaults, $saved );
		return $cfg;
	}

	/* =====================================================================
	   Small helpers
	   ===================================================================== */

	function blockke_dev_text( $html ) {
		$t = preg_replace( '#<br\s*/?>#i', ' ', (string) $html );
		$t = wp_strip_all_tags( $t );
		$t = html_entity_decode( $t, ENT_QUOTES | ENT_HTML5, 'UTF-8' );
		$t = str_replace( array( "\xC2\xA0", "\xE2\x80\x8B" ), array( ' ', '' ), $t );
		return trim( preg_replace( '/\s+/u', ' ', $t ) );
	}

	function blockke_dev_trim_words( $text, $max_chars ) {
		$text = trim( (string) $text );
		if ( mb_strlen( $text ) <= $max_chars ) {
			return $text;
		}
		$cut = mb_substr( $text, 0, $max_chars );
		$sp  = strrpos( $cut, ' ' );
		return preg_replace( '/[\s,.;:\-–—]+$/u', '', $sp ? substr( $cut, 0, $sp ) : $cut ) . '…';
	}

	function blockke_dev_sentences( $text, $n ) {
		$parts = preg_split( '/(?<=[.!?])\s+(?=[A-Z0-9“"])/u', trim( (string) $text ) );
		return trim( implode( ' ', array_slice( $parts, 0, $n ) ) );
	}

	function blockke_dev_money( $n ) {
		$c = blockke_dev_config();
		return $n > 0 ? $c['currency'] . ' ' . number_format( (float) $n ) : 'On request';
	}

	function blockke_dev_short( $n ) {
		$c = blockke_dev_config();
		if ( $n <= 0 ) {
			return 'On request';
		}
		if ( $n >= 1000000 ) {
			$v = rtrim( rtrim( number_format( $n / 1000000, 2, '.', '' ), '0' ), '.' );
			return $c['currency'] . ' ' . $v . 'M';
		}
		return $c['currency'] . ' ' . round( $n / 1000 ) . 'K';
	}

	/** "KES 9.2M" -> 9200000, "From KES 4,600,000" -> 4600000, "On request" -> 0 */
	function blockke_dev_parse_money( $s ) {
		$s = strtoupper( str_replace( array( ',', ' ' ), '', blockke_dev_text( $s ) ) );
		if ( preg_match( '/(\d+(?:\.\d+)?)(MILLION|MN|M|BN|B|K)?/', $s, $m ) ) {
			$v = (float) $m[1];
			$u = isset( $m[2] ) ? $m[2] : '';
			if ( 'M' === $u || 'MN' === $u || 'MILLION' === $u ) {
				$v *= 1000000;
			} elseif ( 'K' === $u ) {
				$v *= 1000;
			} elseif ( 'B' === $u || 'BN' === $u ) {
				$v *= 1000000000;
			}
			return $v >= 1000 ? round( $v ) : 0;
		}
		return 0;
	}

	function blockke_dev_num( $v ) {
		$v = round( (float) $v, 1 );
		return ( floor( $v ) == $v ) ? number_format( $v, 0 ) : number_format( $v, 1 );
	}

	/** Any size text ("84.60 – 126.49", "48 sqm", "1,390 sq ft") -> "84.6 – 126.5 m²" */
	function blockke_dev_size( $s, $assume_ft = false ) {
		$t = blockke_dev_text( $s );
		if ( '' === $t ) {
			return '';
		}
		if ( ! preg_match_all( '/\d+(?:\.\d+)?/', str_replace( ',', '', $t ), $m ) ) {
			return '';
		}
		$is_ft = $assume_ft || preg_match( '/sq\.?\s*ft|sqft|ft²|square f/i', $t );
		$vals  = array();
		foreach ( array_slice( $m[0], 0, 2 ) as $n ) {
			$n      = (float) $n;
			$vals[] = blockke_dev_num( $is_ft ? $n * 0.092903 : $n );
		}
		return implode( ' – ', array_unique( $vals ) ) . ' m²';
	}

	function blockke_dev_digits( $s ) {
		return preg_replace( '/\D+/', '', (string) $s );
	}

	/** Kenyan numbers to 2547... (same rules as the price-check popup). */
	function blockke_dev_phone( $raw ) {
		if ( function_exists( 'blockke_bp_normalize_phone' ) ) {
			return blockke_bp_normalize_phone( $raw );
		}
		$d = blockke_dev_digits( $raw );
		if ( strlen( $d ) < 9 || strlen( $d ) > 15 ) {
			return '';
		}
		if ( 10 === strlen( $d ) && '0' === $d[0] ) {
			$d = '254' . substr( $d, 1 );
		} elseif ( 9 === strlen( $d ) && ( '7' === $d[0] || '1' === $d[0] ) ) {
			$d = '254' . $d;
		} elseif ( 13 === strlen( $d ) && 0 === strpos( $d, '2540' ) ) {
			$d = '254' . substr( $d, 4 );
		}
		return $d;
	}

	function blockke_dev_phone_display( $digits ) {
		if ( 12 === strlen( $digits ) && 0 === strpos( $digits, '254' ) ) {
			return '0' . substr( $digits, 3, 3 ) . ' ' . substr( $digits, 6, 3 ) . ' ' . substr( $digits, 9 );
		}
		return '+' . $digits;
	}

	/** "Dec 2029", "Q4 2027", "2029+", "December 2029" -> "2029-12"; "" when unknown. */
	function blockke_dev_completion_ym( $s ) {
		$s = trim( (string) $s );
		if ( '' === $s ) {
			return '';
		}
		if ( preg_match( '/^(\d{4})-(\d{1,2})$/', $s, $m ) ) {
			return sprintf( '%04d-%02d', $m[1], $m[2] );
		}
		if ( ! preg_match( '/(20\d{2})/', $s, $y ) ) {
			return '';
		}
		$year = (int) $y[1];
		if ( preg_match( '/Q([1-4])/i', $s, $q ) ) {
			return sprintf( '%04d-%02d', $year, (int) $q[1] * 3 );
		}
		if ( preg_match( '/\bH([12])\b/i', $s, $h ) ) {
			return sprintf( '%04d-%02d', $year, (int) $h[1] * 6 );
		}
		$months = array( 'jan' => 1, 'feb' => 2, 'mar' => 3, 'apr' => 4, 'may' => 5, 'jun' => 6, 'jul' => 7, 'aug' => 8, 'sep' => 9, 'oct' => 10, 'nov' => 11, 'dec' => 12 );
		foreach ( $months as $k => $n ) {
			if ( preg_match( '/\b' . $k . '/i', $s ) ) {
				return sprintf( '%04d-%02d', $year, $n );
			}
		}
		if ( preg_match( '/mid/i', $s ) ) {
			return sprintf( '%04d-06', $year );
		}
		if ( preg_match( '/early/i', $s ) ) {
			return sprintf( '%04d-03', $year );
		}
		return sprintf( '%04d-12', $year );
	}

	function blockke_dev_months_until( $ym ) {
		if ( ! preg_match( '/^(\d{4})-(\d{2})$/', (string) $ym, $m ) ) {
			return 0;
		}
		$n = (int) $m[1] * 12 + (int) $m[2] - ( (int) current_time( 'Y' ) * 12 + (int) current_time( 'n' ) );
		return $n > 0 ? $n : 0;
	}

	function blockke_dev_first_term( $id, $tax ) {
		$t = get_the_terms( $id, $tax );
		return ( is_array( $t ) && $t ) ? $t[0] : null;
	}

	function blockke_dev_is_offplan( $id ) {
		if ( has_term( 'ready-now', 'property_completion', $id ) || has_term( 'complete', 'property_status', $id ) ) {
			return false;
		}
		return has_term( 'offplan-ongoing', 'property_status', $id );
	}

	function blockke_dev_is_for_sale( $id ) {
		return has_term( 'for-sale', 'property_action_category', $id ) && ! has_term( 'sold', 'property_status', $id );
	}

	function blockke_dev_sqft_factor() {
		$sys = '';
		if ( function_exists( 'wpresidence_get_option' ) ) {
			$sys = (string) wpresidence_get_option( 'wp_estate_measure_sys', '' );
		}
		return ( 'm' === strtolower( trim( $sys ) ) ) ? 1 : 0.092903;
	}

	/* =====================================================================
	   Reading the listing description (block or classic HTML)
	   ===================================================================== */

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

	/** Flat list of the block-level elements we read: headings, paragraphs, lists, tables, details. */
	function blockke_dev_tokens( $html ) {
		$out = array();
		if ( preg_match_all( '#<(h[1-6]|p|ul|ol|figure|table|details|blockquote)\b[^>]*>(.*?)</\1>#is', $html, $m, PREG_SET_ORDER | PREG_OFFSET_CAPTURE ) ) {
			foreach ( $m as $x ) {
				$tag = strtolower( $x[1][0] );
				$t   = array( 'tag' => $tag, 'html' => $x[0][0], 'inner' => $x[2][0], 'pos' => $x[0][1] );
				if ( 'figure' === $tag ) {
					if ( false === stripos( $x[2][0], '<table' ) ) {
						continue;
					}
					$t['tag'] = 'table';
				}
				if ( 'h' === $tag[0] ) {
					$t['tag']   = 'h';
					$t['level'] = (int) $tag[1];
				}
				if ( 'ol' === $tag ) {
					$t['tag'] = 'ul';
				}
				$out[] = $t;
			}
		}
		return $out;
	}

	/** Splits the description into an intro and sections at its top heading level (H2, or H3 when there are no H2s). */
	function blockke_dev_sections( $html ) {
		$tokens = blockke_dev_tokens( $html );
		$level  = 7;
		foreach ( $tokens as $t ) {
			if ( 'h' === $t['tag'] && $t['level'] >= 2 && $t['level'] < $level ) {
				$level = $t['level'];
			}
		}
		$intro    = array();
		$sections = array();
		$cur      = null;
		foreach ( $tokens as $t ) {
			if ( 'h' === $t['tag'] && $t['level'] <= $level ) {
				if ( $cur ) {
					$sections[] = $cur;
				}
				$cur = array( 'title' => blockke_dev_text( $t['inner'] ), 'level' => $t['level'], 'tokens' => array(), 'start' => $t['pos'] );
				continue;
			}
			if ( $cur ) {
				$cur['tokens'][] = $t;
			} else {
				$intro[] = $t;
			}
		}
		if ( $cur ) {
			$sections[] = $cur;
		}
		$len = strlen( $html );
		foreach ( $sections as $i => $s ) {
			$end                      = isset( $sections[ $i + 1 ] ) ? $sections[ $i + 1 ]['start'] : $len;
			$sections[ $i ]['html'] = substr( $html, $s['start'], $end - $s['start'] );
		}
		return array(
			'level'    => $level,
			'intro'    => $intro,
			'sections' => $sections,
			'tokens'   => $tokens,
			'html'     => $html,
		);
	}

	function blockke_dev_parsed( $post ) {
		static $cache = array();
		if ( ! isset( $cache[ $post->ID ] ) ) {
			$cache[ $post->ID ] = blockke_dev_sections( blockke_dev_content_html( $post ) );
		}
		return $cache[ $post->ID ];
	}

	function blockke_dev_find( $sections, $re, $not = '' ) {
		foreach ( $sections as $i => $s ) {
			if ( preg_match( $re, $s['title'] ) && ( '' === $not || ! preg_match( $not, $s['title'] ) ) ) {
				return $i;
			}
		}
		return -1;
	}

	function blockke_dev_list_items( $inner ) {
		$items = array();
		if ( preg_match_all( '#<li\b[^>]*>(.*?)</li>#is', $inner, $m ) ) {
			foreach ( $m[1] as $li ) {
				$li    = preg_replace( '#<(ul|ol)\b.*$#is', '', $li );
				$label = '';
				$rest  = '';
				if ( preg_match( '#^\s*<(strong|b)\b[^>]*>(.*?)</\1>\s*(.*)$#is', $li, $k ) ) {
					$label = blockke_dev_text( $k[2] );
					$rest  = $k[3];
				}
				$text = blockke_dev_text( $li );
				if ( '' !== $text ) {
					$items[] = array( 'html' => trim( $li ), 'text' => $text, 'label' => $label, 'rest' => $rest );
				}
			}
		}
		return $items;
	}

	/** "Label: value" from a list item, bold label or plain colon. */
	function blockke_dev_split_kv( $it ) {
		if ( '' !== $it['label'] ) {
			$k = trim( rtrim( trim( $it['label'] ), ':' ) );
			$v = trim( preg_replace( '/^\s*[:–—-]\s*/u', '', blockke_dev_text( $it['rest'] ) ) );
			if ( '' !== $v && mb_strlen( $k ) <= 60 ) {
				return array( $k, $v );
			}
		}
		if ( preg_match( '/^([^:]{2,48}):\s*(.+)$/u', $it['text'], $m ) ) {
			return array( trim( $m[1] ), trim( $m[2] ) );
		}
		return array( '', $it['text'] );
	}

	function blockke_dev_table( $html ) {
		if ( ! preg_match_all( '#<tr\b[^>]*>(.*?)</tr>#is', $html, $trs ) ) {
			return null;
		}
		$has_thead = (bool) preg_match( '#<thead\b#i', $html );
		$head      = array();
		$rows      = array();
		foreach ( $trs[1] as $i => $tr ) {
			preg_match_all( '#<t([hd])\b[^>]*>(.*?)</t\1>#is', $tr, $cells, PREG_SET_ORDER );
			$row    = array();
			$links  = array();
			$all_th = true;
			foreach ( $cells as $c ) {
				$row[]   = blockke_dev_text( $c[2] );
				$links[] = preg_match( '#<a\b[^>]*href=["\']([^"\']+)["\']#i', $c[2], $a ) ? html_entity_decode( $a[1], ENT_QUOTES, 'UTF-8' ) : '';
				if ( 'h' !== strtolower( $c[1] ) ) {
					$all_th = false;
				}
			}
			if ( ! $row ) {
				continue;
			}
			if ( ! $head && ! $rows && ( $all_th || ( $has_thead && 0 === $i ) ) ) {
				$head = $row;
				continue;
			}
			$rows[] = array( 'cells' => $row, 'links' => $links );
		}
		return array( 'head' => $head, 'rows' => $rows );
	}

	/** Label / value pairs from tables and "Label: value" lists in a section. */
	function blockke_dev_kv( $tokens ) {
		$kv = array();
		foreach ( $tokens as $t ) {
			if ( 'table' === $t['tag'] ) {
				$tb = blockke_dev_table( $t['html'] );
				if ( $tb ) {
					foreach ( $tb['rows'] as $r ) {
						if ( count( $r['cells'] ) >= 2 && '' !== $r['cells'][0] ) {
							$kv[] = array( rtrim( $r['cells'][0], ': ' ), $r['cells'][1] );
						}
					}
				}
			} elseif ( 'ul' === $t['tag'] ) {
				foreach ( blockke_dev_list_items( $t['inner'] ) as $it ) {
					$p = blockke_dev_split_kv( $it );
					if ( '' !== $p[0] ) {
						$kv[] = $p;
					}
				}
			}
		}
		return $kv;
	}

	function blockke_dev_kv_get( $kv, $re ) {
		foreach ( $kv as $p ) {
			if ( preg_match( $re, $p[0] ) ) {
				return $p[1];
			}
		}
		return '';
	}

	/** Glance / snapshot facts of the listing (cached per request). */
	function blockke_dev_glance( $post ) {
		$parsed = blockke_dev_parsed( $post );
		$i      = blockke_dev_find( $parsed['sections'], '/glance|snapshot|key facts|quick facts|in brief|at a look|summary/i' );
		return $i >= 0 ? blockke_dev_kv( $parsed['sections'][ $i ]['tokens'] ) : array();
	}

	function blockke_dev_completion( $post ) {
		$c = trim( blockke_dev_text( get_post_meta( $post->ID, 'bke_completion_stated', true ) ) );
		if ( '' === $c ) {
			$c = blockke_dev_kv_get( blockke_dev_glance( $post ), '/completion|occupation|handover|delivery/i' );
		}
		if ( '' === $c ) {
			$t = blockke_dev_first_term( $post->ID, 'property_completion' );
			if ( $t && 'ready-now' !== $t->slug ) {
				$c = blockke_dev_text( $t->name );
			}
		}
		return blockke_dev_trim_words( $c, 32 );
	}

	/* =====================================================================
	   Payment plan (both the classic calculator and the development layout)
	   ===================================================================== */

	function blockke_dev_plan_info( $post ) {
		$id     = $post->ID;
		$parsed = blockke_dev_parsed( $post );
		$i      = blockke_dev_find( $parsed['sections'], '/payment/i' );
		$steps  = array();
		$text   = '';
		$all    = '';
		if ( $i >= 0 ) {
			foreach ( $parsed['sections'][ $i ]['tokens'] as $t ) {
				if ( 'ul' === $t['tag'] ) {
					foreach ( blockke_dev_list_items( $t['inner'] ) as $it ) {
						$p       = blockke_dev_split_kv( $it );
						$steps[] = '' !== $p[0] ? array( $p[0], ucfirst( $p[1] ) ) : array( $it['text'], '' );
						$all    .= ' ' . $it['text'] . '.';
					}
				} elseif ( 'table' === $t['tag'] ) {
					$tb = blockke_dev_table( $t['html'] );
					foreach ( $tb ? $tb['rows'] : array() as $r ) {
						if ( count( $r['cells'] ) >= 2 ) {
							$steps[] = array( $r['cells'][0], $r['cells'][1] );
							$all    .= ' ' . $r['cells'][0] . ': ' . $r['cells'][1] . '.';
						}
					}
				} elseif ( 'p' === $t['tag'] ) {
					$tx   = blockke_dev_text( $t['inner'] );
					$all .= ' ' . $tx;
					if ( '' === $text && mb_strlen( $tx ) > 20 ) {
						$text = $tx;
					}
				}
			}
		}
		$glance = blockke_dev_glance( $post );
		foreach ( $glance as $p ) {
			if ( preg_match( '/deposit|reserv|balance|instal|payment/i', $p[0] ) ) {
				$all .= ' ' . $p[0] . ': ' . $p[1] . '.';
			}
		}

		$dep = (float) get_post_meta( $id, 'bke_dev_deposit_pct', true );
		if ( $dep <= 0 && preg_match( '/deposit[^.%\d]{0,40}(\d{1,2}(?:\.\d+)?)\s*%|(\d{1,2}(?:\.\d+)?)\s*%\s*(?:deposit|down)/i', $all, $m ) ) {
			$dep = (float) ( '' !== $m[1] ? $m[1] : $m[2] );
		}
		$res = (float) get_post_meta( $id, 'bke_dev_reservation', true );
		if ( $res <= 0 && preg_match( '/reserv\w*[^.]{0,40}?(?:KES|KSH|Ksh|Kshs)\.?\s*([\d,]+(?:\.\d+)?\s*[MK]?)/i', $all, $m ) ) {
			$res = blockke_dev_parse_money( $m[1] );
		}
		$on_completion = 0;
		if ( preg_match( '/(\d{1,2})\s*%[^.]{0,30}?(?:on|at|upon)\s+(?:completion|handover|hand-over)/i', $all, $m ) ) {
			$on_completion = (int) $m[1];
		}
		$completion = blockke_dev_completion( $post );
		$until      = trim( (string) get_post_meta( $id, 'bke_dev_plan_until', true ) );
		if ( '' === $until ) {
			$until = blockke_dev_completion_ym( $completion );
		}
		$override_text = trim( (string) get_post_meta( $id, 'bke_dev_plan_text', true ) );
		if ( '' !== $override_text ) {
			$text = $override_text;
		}
		return array(
			'found'         => $i >= 0 || $dep > 0 || '' !== $override_text,
			'steps'         => array_slice( $steps, 0, 6 ),
			'text'          => $text,
			'deposit'       => $dep,
			'reservation'   => $res,
			'on_completion' => $on_completion,
			'completion'    => $completion,
			'until'         => $until,
			'months'        => blockke_dev_months_until( $until ),
			'quarterly'     => (bool) preg_match( '/quarter|bi-?annual|semi-?annual/i', $all ),
			'flexible'      => (bool) preg_match( '/flexib/i', $all ),
		);
	}

	/* =====================================================================
	   Calculators (shared markup, styles and script)
	   ===================================================================== */

	function blockke_dev_calc_instalment( $price, $plan, $cta = '' ) {
		$cfg    = blockke_dev_config();
		$cur    = $cfg['currency'];
		$dep    = $plan['deposit'] > 0 ? $plan['deposit'] : (float) $cfg['default_deposit'];
		$months = (int) $plan['months'];
		$h      = '<div class="bkd-calc" id="bkd-calc" data-bkd-calc="instalment" data-cur="' . esc_attr( $cur ) . '" data-deposit="' . esc_attr( $dep ) . '"'
			. ' data-reservation="' . esc_attr( (float) $plan['reservation'] ) . '" data-until="' . esc_attr( $plan['until'] ) . '" data-months="' . esc_attr( $months ) . '"'
			. ' data-completion="' . esc_attr( $plan['completion'] ) . '" data-oncompletion="' . esc_attr( (int) $plan['on_completion'] ) . '">';
		$h     .= '<div class="bkd-calc-grid"><div class="bkd-calc-fields">';
		$h     .= '<label class="bkd-cf"><span class="bkd-cl">Price (' . esc_html( $cur ) . ')</span><input class="bkd-ci" data-c="price" inputmode="numeric" autocomplete="off" value="' . esc_attr( $price > 0 ? number_format( $price ) : '' ) . '" aria-label="Price"></label>';
		$h     .= '<label class="bkd-cf"><span class="bkd-cl">Deposit <output data-c="dep-out">' . esc_html( blockke_dev_num( $dep ) ) . '%</output></span>'
			. '<input class="bkd-range" type="range" data-c="dep" min="0" max="60" step="5" value="' . esc_attr( round( $dep / 5 ) * 5 ) . '" aria-label="Deposit percentage"></label>';
		$h     .= '<div class="bkd-cf" role="group" aria-label="Pay the balance"><span class="bkd-cl">Pay the balance</span><div class="bkd-seg">'
			. '<button type="button" data-c="freq" data-v="1" aria-pressed="true">Monthly</button><button type="button" data-c="freq" data-v="3" aria-pressed="false">Quarterly</button></div></div>';
		if ( $months < 1 ) {
			$h .= '<label class="bkd-cf"><span class="bkd-cl">Over</span><select class="bkd-ci" data-c="months" aria-label="Number of months">';
			foreach ( array( 6, 12, 18, 24, 30, 36, 48, 60 ) as $mo ) {
				$h .= '<option value="' . $mo . '"' . selected( 24, $mo, false ) . '>' . $mo . ' months</option>';
			}
			$h .= '</select></label>';
		}
		$h .= '</div><div class="bkd-calc-out" aria-live="polite">';
		$h .= '<div class="bkd-co-main"><span data-c="per-label">Per month</span><strong data-c="per">—</strong></div>';
		$h .= '<div class="bkd-co-row"><span>Deposit<small data-c="dep-note"></small></span><strong data-c="deposit">—</strong></div>';
		$h .= '<div class="bkd-co-row"><span>Instalments</span><strong data-c="count">—</strong></div>';
		$h .= '<div class="bkd-co-row" data-c="comp-row" hidden><span>On completion</span><strong data-c="comp">—</strong></div>';
		$h .= '<div class="bkd-co-row bkd-co-total"><span>Total price</span><strong data-c="total">—</strong></div>';
		$h .= '</div></div>';
		$note = 'Illustrative only: equal instalments from next month' . ( $months > 0 ? ' until completion' . ( $plan['completion'] ? ' in ' . $plan['completion'] : '' ) : '' ) . '.';
		if ( $plan['deposit'] <= 0 ) {
			$note .= ' The deposit shown is typical; this listing has not stated one.';
		}
		$h .= '<div class="bkd-calc-note">' . esc_html( $note . " The developer's official schedule may differ." ) . '</div>';
		$h .= $cta;
		$h .= '</div>';
		return $h;
	}

	function blockke_dev_calc_mortgage( $price, $cta = '' ) {
		$cfg = blockke_dev_config();
		$cur = $cfg['currency'];
		$h   = '<div class="bkd-calc" id="bkd-calc" data-bkd-calc="mortgage" data-cur="' . esc_attr( $cur ) . '">';
		$h  .= '<div class="bkd-calc-grid"><div class="bkd-calc-fields">';
		$h  .= '<label class="bkd-cf"><span class="bkd-cl">Price (' . esc_html( $cur ) . ')</span><input class="bkd-ci" data-c="price" inputmode="numeric" autocomplete="off" value="' . esc_attr( $price > 0 ? number_format( $price ) : '' ) . '" aria-label="Price"></label>';
		$h  .= '<label class="bkd-cf"><span class="bkd-cl">Deposit <output data-c="dep-out">' . (int) $cfg['mortgage_deposit'] . '%</output></span>'
			. '<input class="bkd-range" type="range" data-c="dep" min="10" max="60" step="5" value="' . (int) $cfg['mortgage_deposit'] . '" aria-label="Deposit percentage"></label>';
		$h  .= '<div class="bkd-calc-two"><label class="bkd-cf"><span class="bkd-cl">Interest (% a year)</span><input class="bkd-ci" data-c="rate" inputmode="decimal" autocomplete="off" value="' . esc_attr( $cfg['mortgage_rate'] ) . '" aria-label="Interest rate"></label>';
		$h  .= '<label class="bkd-cf"><span class="bkd-cl">Term</span><select class="bkd-ci" data-c="years" aria-label="Term in years">';
		foreach ( array( 5, 10, 15, 20, 25 ) as $y ) {
			$h .= '<option value="' . $y . '"' . selected( (int) $cfg['mortgage_years'], $y, false ) . '>' . $y . ' years</option>';
		}
		$h .= '</select></label></div>';
		$h .= '</div><div class="bkd-calc-out" aria-live="polite">';
		$h .= '<div class="bkd-co-main"><span>Monthly repayment</span><strong data-c="per">—</strong></div>';
		$h .= '<div class="bkd-co-row"><span>Deposit</span><strong data-c="deposit">—</strong></div>';
		$h .= '<div class="bkd-co-row"><span>Loan</span><strong data-c="loan">—</strong></div>';
		$h .= '<div class="bkd-co-row bkd-co-total"><span>Total interest</span><strong data-c="interest">—</strong></div>';
		$h .= '</div></div>';
		$h .= '<div class="bkd-calc-note">Illustrative only. Rates, fees and terms vary by lender and borrower. Ask us to compare mortgage offers.</div>';
		$h .= $cta;
		$h .= '</div>';
		return $h;
	}

	function blockke_dev_calc_css() {
		return <<<'CSS'
#bkd-calc{container-type:inline-size;--c-navy:#0D2440;--c-slate:#46586A;--c-muted:#66768A;--c-line:#D3D9E2;--c-mist:#F5F6F8;font-family:"Montserrat",system-ui,-apple-system,"Segoe UI",Roboto,sans-serif;color:var(--c-navy);text-align:left}
#bkd-calc *,#bkd-calc *::before,#bkd-calc *::after{box-sizing:border-box}
#bkd-calc .bkd-calc-grid{display:grid;gap:28px}
@container (min-width:620px){#bkd-calc .bkd-calc-grid{grid-template-columns:minmax(0,1fr) minmax(0,1fr);gap:44px;align-items:start}}
#bkd-calc .bkd-calc-fields{display:grid;gap:20px}
#bkd-calc .bkd-calc-two{display:grid;grid-template-columns:1fr 1fr;gap:16px}
#bkd-calc .bkd-cf{display:grid;gap:8px;margin:0;padding:0;border:0;min-width:0}
#bkd-calc .bkd-cl{display:flex;justify-content:space-between;align-items:baseline;gap:12px;font-size:11px!important;font-weight:600!important;letter-spacing:.14em!important;text-transform:uppercase;color:var(--c-slate)!important;line-height:1.4}
#bkd-calc .bkd-cl output{letter-spacing:0;text-transform:none;font-size:14px;font-weight:600;color:var(--c-navy)}
#bkd-calc .bkd-ci{display:block;width:100%;height:52px;margin:0;padding:0 16px;border:1px solid var(--c-line)!important;border-radius:0!important;background:#fff!important;box-shadow:none!important;font:500 17px/1.2 "Montserrat",system-ui,sans-serif!important;color:var(--c-navy)!important;-webkit-appearance:none;appearance:none;max-width:none;min-height:0}
#bkd-calc select.bkd-ci{padding-right:40px;background:#fff url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath d='M1 1l5 5 5-5' fill='none' stroke='%230D2440' stroke-width='1.6'/%3E%3C/svg%3E") no-repeat right 16px center!important;cursor:pointer}
#bkd-calc .bkd-ci:focus{outline:0;border-color:var(--c-navy)!important;box-shadow:0 0 0 3px rgba(13,36,64,.1)!important}
#bkd-calc .bkd-range{-webkit-appearance:none;appearance:none;width:100%;height:36px;margin:0;padding:0;border:0!important;background:transparent!important;box-shadow:none!important;cursor:pointer}
#bkd-calc .bkd-range::-webkit-slider-runnable-track{height:2px;background:linear-gradient(var(--c-navy),var(--c-navy)) 0/var(--p,30%) 100% no-repeat,var(--c-line)}
#bkd-calc .bkd-range::-moz-range-track{height:2px;background:linear-gradient(var(--c-navy),var(--c-navy)) 0/var(--p,30%) 100% no-repeat,var(--c-line)}
#bkd-calc .bkd-range::-webkit-slider-thumb{-webkit-appearance:none;width:22px;height:22px;margin-top:-10px;border-radius:50%;background:var(--c-navy);border:4px solid #fff;box-shadow:0 0 0 1px var(--c-navy),0 2px 6px rgba(13,36,64,.25)}
#bkd-calc .bkd-range::-moz-range-thumb{width:14px;height:14px;border-radius:50%;background:var(--c-navy);border:4px solid #fff;box-shadow:0 0 0 1px var(--c-navy),0 2px 6px rgba(13,36,64,.25)}
#bkd-calc .bkd-range:focus{outline:0}
#bkd-calc .bkd-range:focus-visible::-webkit-slider-thumb{box-shadow:0 0 0 1px var(--c-navy),0 0 0 6px rgba(13,36,64,.18)}
#bkd-calc .bkd-seg{display:grid;grid-template-columns:1fr 1fr;border:1px solid var(--c-line)}
#bkd-calc .bkd-seg button{min-height:46px;margin:0;padding:0 12px;border:0!important;border-radius:0!important;background:#fff!important;color:var(--c-navy)!important;font:600 12px/1 "Montserrat",system-ui,sans-serif!important;letter-spacing:.1em!important;text-transform:uppercase!important;cursor:pointer;box-shadow:none!important;transition:background-color .2s,color .2s}
#bkd-calc .bkd-seg button+button{border-left:1px solid var(--c-line)!important}
#bkd-calc .bkd-seg button[aria-pressed="true"]{background:var(--c-navy)!important;color:#fff!important}
#bkd-calc .bkd-seg button:focus-visible{outline:2px solid var(--c-navy);outline-offset:2px}
#bkd-calc .bkd-calc-out{border-top:1px solid var(--c-navy)}
#bkd-calc .bkd-co-main{padding:22px 0 20px;border-bottom:1px solid var(--c-line)}
#bkd-calc .bkd-calc-out span{display:block;font-size:11px;font-weight:600;letter-spacing:.14em;text-transform:uppercase;color:var(--c-muted);line-height:1.4}
#bkd-calc .bkd-calc-out span small{display:block;margin-top:3px;font-size:12px;font-weight:400;letter-spacing:0;text-transform:none;color:var(--c-muted)}
#bkd-calc .bkd-co-main strong{display:block;margin-top:8px;font-size:clamp(30px,4vw,42px);font-weight:300;letter-spacing:-.02em;line-height:1.1;color:var(--c-navy);white-space:nowrap}
#bkd-calc .bkd-co-row{display:flex;justify-content:space-between;align-items:baseline;gap:16px;padding:14px 0;border-bottom:1px solid var(--c-line)}
#bkd-calc .bkd-co-row[hidden]{display:none}
#bkd-calc .bkd-co-row strong{font-size:16px;font-weight:600;color:var(--c-navy);white-space:nowrap;text-align:right}
#bkd-calc .bkd-co-total{border-bottom:0}
#bkd-calc .bkd-co-total strong{font-weight:500;color:var(--c-slate)}
#bkd-calc .bkd-calc-note{margin-top:22px;font-size:12.5px;line-height:1.6;color:var(--c-muted)}
#bkd-calc .bkd-calc-cta{display:flex;flex-wrap:wrap;gap:12px;margin-top:22px}
#bkd-calc .bkd-cbtn{display:inline-flex;align-items:center;justify-content:center;gap:10px;min-height:50px;margin:0;padding:0 24px!important;border:1px solid var(--c-navy)!important;border-radius:0!important;background:var(--c-navy)!important;color:#fff!important;font:600 12px/1.2 "Montserrat",system-ui,sans-serif!important;letter-spacing:.12em!important;text-transform:uppercase!important;text-decoration:none!important;cursor:pointer;box-shadow:none!important;transition:background-color .2s,color .2s}
#bkd-calc .bkd-cbtn:hover,#bkd-calc .bkd-cbtn:focus-visible{background:#fff!important;color:var(--c-navy)!important}
#bkd-calc .bkd-cbtn-line{background:#fff!important;color:var(--c-navy)!important;border-color:var(--c-line)!important}
#bkd-calc .bkd-cbtn-line:hover{border-color:var(--c-navy)!important}
#bkd-calc .bkd-cbtn svg{width:18px;height:18px;flex:none}
@media (max-width:420px){#bkd-calc .bkd-calc-two{grid-template-columns:1fr}#bkd-calc .bkd-co-row{flex-wrap:wrap}#bkd-calc .bkd-co-row strong{white-space:normal}}
CSS;
	}

	function blockke_dev_calc_js() {
		return <<<'JS'
(function(){
  var box=document.getElementById('bkd-calc');if(!box||box.getAttribute('data-ready')){return;}
  box.setAttribute('data-ready','1');
  var type=box.getAttribute('data-bkd-calc'),cur=box.getAttribute('data-cur')||'KES';
  function q(n){return box.querySelector('[data-c="'+n+'"]');}
  function dg(s){return String(s||'').replace(/[^\d.]/g,'');}
  function fmt(n){return cur+' '+Math.round(n).toLocaleString('en-KE');}
  function num(el){return el?(parseFloat(dg(el.value))||0):0;}
  function setP(r){r.style.setProperty('--p',((r.value-r.min)/(r.max-r.min)*100)+'%');}
  var price=q('price'),dep=q('dep');
  var freq=1;
  function monthsLeft(){
    var m=/^(\d{4})-(\d{1,2})$/.exec(box.getAttribute('data-until')||'');
    if(m){var d=new Date(),n=(+m[1])*12+(+m[2])-(d.getFullYear()*12+d.getMonth()+1);if(n>0){return n;}}
    var sel=q('months');if(sel){return +sel.value||24;}
    return +box.getAttribute('data-months')||0;
  }
  function calc(){
    var p=num(price),d=+dep.value;
    q('dep-out').textContent=d+'%';setP(dep);
    if(type==='mortgage'){
      var rate=num(q('rate'))/100/12,n=(+q('years').value||20)*12,loan=p*(1-d/100);
      var pay=loan<=0?0:(rate>0?loan*rate/(1-Math.pow(1+rate,-n)):loan/n);
      q('per').textContent=p?fmt(pay):'—';
      q('deposit').textContent=p?fmt(p*d/100):'—';
      q('loan').textContent=p?fmt(loan):'—';
      q('interest').textContent=p?fmt(Math.max(0,pay*n-loan)):'—';
      return;
    }
    var res=+box.getAttribute('data-reservation')||0,oc=+box.getAttribute('data-oncompletion')||0;
    var months=monthsLeft(),count=Math.max(1,Math.floor(months/freq)||1);
    var depAmt=p*d/100,compAmt=p*oc/100,inst=Math.max(0,p-depAmt-compAmt),per=inst/count;
    var unit=freq===3?'quarter':'month',comp=box.getAttribute('data-completion')||'';
    var until=box.getAttribute('data-until')&&monthsLeft()>0&&!q('months');
    q('per-label').textContent='Per '+unit+(until?', until completion'+(comp?' in '+comp:''):', over '+months+' months');
    q('per').textContent=p?fmt(per):'—';
    q('deposit').textContent=p?fmt(depAmt):'—';
    q('dep-note').textContent=res&&p&&depAmt>=res?'Includes the '+fmt(res)+' reservation':'';
    q('count').textContent=p?count+' × '+fmt(per):'—';
    var cr=q('comp-row');if(cr){cr.hidden=!oc;q('comp').textContent=p?fmt(compAmt):'—';}
    q('total').textContent=p?fmt(p):'—';
  }
  box.addEventListener('input',function(e){if(e.target.matches('input,select')){calc();}});
  box.addEventListener('change',function(e){if(e.target.matches('select')){calc();}});
  price.addEventListener('blur',function(){var v=Math.round(num(price));price.value=v?v.toLocaleString('en-KE'):'';calc();});
  box.addEventListener('click',function(e){
    var b=e.target.closest&&e.target.closest('[data-c="freq"]');if(!b){return;}
    freq=+b.getAttribute('data-v')||1;
    Array.prototype.forEach.call(box.querySelectorAll('[data-c="freq"]'),function(x){x.setAttribute('aria-pressed',String(x===b));});
    calc();
  });
  calc();
})();
JS;
	}

	/* =====================================================================
	   1) Classic property pages: instalment calculator for off-plan listings
	   ===================================================================== */

	function blockke_dev_classic_target() {
		static $id = null;
		if ( null !== $id ) {
			return $id;
		}
		$id  = 0;
		$cfg = blockke_dev_config();
		if ( empty( $cfg['offplan_calculator'] ) || is_admin() || ! is_singular( 'estate_property' ) || ! empty( $GLOBALS['blockke_dev_active'] ) ) {
			return $id;
		}
		$pid = get_queried_object_id();
		if ( blockke_dev_is_offplan( $pid ) && blockke_dev_is_for_sale( $pid ) ) {
			$id = $pid;
		}
		return $id;
	}

	add_action(
		'wp_head',
		function () {
			if ( blockke_dev_classic_target() && empty( $GLOBALS['blockke_dev_active'] ) ) {
				echo "<style id=\"bkd-calc-hide\">#accordion_property_payment_calculator{display:none!important}</style>\n";
			}
		},
		99
	);

	add_action(
		'wp_footer',
		function () {
			$id = blockke_dev_classic_target();
			if ( ! $id || ! empty( $GLOBALS['blockke_dev_active'] ) ) {
				return;
			}
			$post  = get_post( $id );
			$plan  = blockke_dev_plan_info( $post );
			$price = (float) get_post_meta( $id, 'property_price', true );
			$cfg   = blockke_dev_config();
			$agent = blockke_dev_agent( (int) get_post_meta( $id, 'property_agent', true ) );
			$wa    = 'https://wa.me/' . $agent['wa'] . '?text=' . rawurlencode( 'Hi ' . $cfg['brand'] . ', please send me the official payment plan for ' . blockke_dev_text( get_the_title( $id ) ) . '. ' . get_permalink( $id ) );
			$cta   = '<div class="bkd-calc-cta"><button type="button" class="bkd-cbtn" data-bkd-plan-cta>Request the official payment plan</button>'
				. '<a class="bkd-cbtn bkd-cbtn-line" href="' . esc_url( $wa ) . '" target="_blank" rel="noopener">WhatsApp us</a></div>';
			echo '<style id="bkd-calc-css">' . blockke_dev_calc_css() . "</style>\n";
			echo '<div id="bkd-calc-section" hidden><div class="accordion property-panel" id="accordion_property_instalment_calculator"><div class="accordion-item">'
				. '<h2 class="accordion-header" id="bkd-calc-head"><button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#bkd-calc-collapse" aria-expanded="true" aria-controls="bkd-calc-collapse">Payment plan calculator</button></h2>'
				. '<div id="bkd-calc-collapse" class="accordion-collapse collapse show" aria-labelledby="bkd-calc-head"><div class="accordion-body">'
				. blockke_dev_calc_instalment( $price, $plan, $cta )
				. "</div></div></div></div></div>\n";
			$move = <<<'JS'
(function(){
  var wrap=document.getElementById('bkd-calc-section');if(!wrap){return;}
  var sec=wrap.firstElementChild,anchor=document.getElementById('accordion_property_payment_calculator');
  if(anchor&&anchor.parentNode){anchor.parentNode.insertBefore(sec,anchor);}
  else{var alt=document.getElementById('accordion_property_details_map')||document.getElementById('accordion_property_address');
    if(alt&&alt.parentNode){alt.parentNode.insertBefore(sec,alt.nextSibling);}else{return;}}
  wrap.parentNode.removeChild(wrap);
  document.addEventListener('click',function(e){
    var b=e.target.closest&&e.target.closest('[data-bkd-plan-cta]');if(!b){return;}
    var form=document.querySelector('#wpestate_sidebar_property_contact_tabs form, #wpestate_sidebar_property_contact_tabs, .agent_contanct_form, .contact_form_flex, #show_contact_form');
    var ta=form&&form.querySelector('textarea');
    if(form&&form.offsetParent!==null){
      if(ta&&!ta.value){ta.value='Please send me the official payment plan for this property.';}
      form.scrollIntoView({behavior:'smooth',block:'center'});
      var f=form.querySelector('input:not([type=hidden]),textarea');if(f){setTimeout(function(){f.focus({preventScroll:true});},500);}
    }else{var wa=b.parentNode.querySelector('a[href*="wa.me"]');if(wa){window.open(wa.href,'_blank','noopener');}}
  });
})();
JS;
			echo '<script id="bkd-calc-js" data-cfasync="false" data-no-optimize="1" data-no-defer="1">' . $move . "\n" . blockke_dev_calc_js() . "</script>\n";
		},
		25
	);

	/* =====================================================================
	   1b) Classic property pages: amenity tiles, and distances to nearby places
	   ===================================================================== */

	/** The heading font from the config, '' for the site font. */
	function blockke_dev_display_font() {
		return trim( preg_replace( '/[^A-Za-z0-9 ]/', '', (string) blockke_dev_config()['display_font'] ) );
	}

	/** The Google Fonts link and the --display value for a scope, e.g. "#bkd". */
	function blockke_dev_font_head( $scope ) {
		$font = blockke_dev_display_font();
		if ( '' === $font ) {
			return array( '', '' );
		}
		return array(
			'<link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>'
				. '<link id="bkd-font" rel="stylesheet" href="' . esc_url( 'https://fonts.googleapis.com/css2?family=' . str_replace( ' ', '+', $font ) . ':ital@0;1&display=swap' ) . "\">\n",
			$scope . '{--display:"' . $font . '",Georgia,"Times New Roman",serif;--display-w:400}',
		);
	}

	/** What a classic property page gets: the amenity layout (Features & Amenities) and nearby places (Address). */
	function blockke_dev_classic_extras() {
		static $x = null;
		if ( null !== $x ) {
			return $x;
		}
		$x = array(
			'feat' => null,
			'near' => array(),
			'own'  => array(),
		);
		if ( is_admin() || ! is_singular( 'estate_property' ) || ! empty( $GLOBALS['blockke_dev_active'] ) ) {
			return $x;
		}
		$cfg = blockke_dev_config();
		$id  = get_queried_object_id();
		if ( ! empty( $cfg['classic_amenities'] ) ) {
			$am = blockke_dev_amenity_layout( blockke_dev_feature_groups( $id ) );
			if ( $am['tiles'] || $am['rest'] ) {
				$x['feat'] = $am;
			}
		}
		if ( ! empty( $cfg['classic_nearby'] ) ) {
			$x['own'] = blockke_dev_places_override( $id );
			if ( ! $x['own'] ) {
				$x['near'] = blockke_dev_nearby( get_post_meta( $id, 'property_latitude', true ), get_post_meta( $id, 'property_longitude', true ) );
			}
		}
		return $x;
	}

	function blockke_dev_classic_features( $am ) {
		$o = '<div class="bkd-cls" id="bkd-feat">';
		if ( $am['tiles'] ) {
			$o .= '<ul class="bkd-ft" data-n="' . count( $am['tiles'] ) . '">';
			foreach ( $am['tiles'] as $t ) {
				$o .= '<li>' . blockke_dev_icon( $t['icon'] ) . '<b>' . esc_html( $t['title'] ) . '</b><span>' . esc_html( $t['text'] ) . '</span></li>';
			}
			$o .= '</ul>';
		}
		if ( $am['rest'] ) {
			$o .= '<div class="bkd-fe">' . ( $am['tiles'] ? '<h3 class="bkd-cls-h">' . ( $am['essentials'] ? 'The everyday <em>essentials</em>' : 'Also <em>included</em>' ) . '</h3>' : '' ) . '<ul>';
			foreach ( $am['rest'] as $r ) {
				$o .= '<li>' . blockke_dev_icon( 'check' ) . '<span>' . esc_html( $r ) . '</span></li>';
			}
			$o .= '</ul></div>';
		}
		return $o . '</div>';
	}

	function blockke_dev_classic_nearby( $groups, $own ) {
		$o = '<div class="bkd-cls" id="bkd-near"><h3 class="bkd-cls-h">What’s <em>nearby</em></h3>';
		if ( $own ) {
			$o .= '<ul class="bkd-nr-own">';
			foreach ( $own as $p ) {
				$o .= '<li><span>' . esc_html( $p[0] ) . '</span>' . ( '' !== $p[1] ? '<b>' . esc_html( $p[1] ) . '</b>' : '' ) . '</li>';
			}
			return $o . '</ul><p class="bkd-nr-note">Approximate travel times; they vary with traffic.</p></div>';
		}
		$o .= '<div class="bkd-nr">';
		foreach ( $groups as $g ) {
			$o .= '<div class="bkd-nr-c"><p class="bkd-nr-k">' . blockke_dev_icon( $g['icon'] ) . esc_html( $g['label'] ) . '</p><ul>';
			foreach ( $g['places'] as $p ) {
				$o .= '<li><span>' . esc_html( $p[0] ) . '</span><b>' . blockke_dev_km_label( $p[1] ) . '</b></li>';
			}
			$o .= '</ul></div>';
		}
		return $o . '</div><p class="bkd-nr-note">Straight-line distances from the map pin. Road distances and travel times will be longer.</p></div>';
	}

	function blockke_dev_classic_css() {
		return <<<'CSS'
.bkd-cls{--navy:#0D2440;--navy-l:#2B4568;--ink:#22303C;--muted:#5F6D7E;--mist:#F4EEE4;--line:#E6DFD4;--line-2:#D5CBBB;--brass:#B98A44;--brass-d:#80602A;--brass-t:#9E7433;--brass-l:#DDB97F;--font:"Montserrat",system-ui,-apple-system,"Segoe UI",Roboto,sans-serif;--display:var(--font);--display-w:600;
display:block;clear:both;float:none;flex:0 0 100%;width:100%;max-width:100%;margin:0;padding:0;container-type:inline-size;text-align:left;font:400 15px/1.6 var(--font);color:var(--ink);-webkit-font-smoothing:antialiased}
.bkd-cls :where(*,*::before,*::after){box-sizing:border-box}
.bkd-cls :is(h3,p,ul,li){margin:0!important;padding:0!important;border:0;background:none;list-style:none!important;text-transform:none}
.bkd-cls li::before,.bkd-cls li::after{content:none!important;display:none!important}
.bkd-cls .bkd-i{display:block;width:18px;height:18px;flex:none;fill:none;stroke:currentColor;stroke-width:1.6;stroke-linecap:round;stroke-linejoin:round}
#accordion_features_details .bkd-feat-on>:not(#bkd-feat){display:none!important}
:is(#bkd-feat,#bkd-near) .bkd-cls-h{margin:0 0 10px!important;font-family:var(--display)!important;font-size:27px!important;font-weight:var(--display-w)!important;font-style:normal!important;line-height:1.15!important;letter-spacing:0!important;color:var(--navy)!important}
:is(#bkd-feat,#bkd-near) .bkd-cls-h em{font-family:inherit!important;font-style:italic;font-weight:inherit;color:var(--brass-t)!important}
#bkd-feat .bkd-ft{display:grid;grid-template-columns:minmax(0,1fr);gap:1px;margin:0 0 34px!important;background:var(--navy-l);border:1px solid var(--navy)}
#bkd-feat .bkd-ft li{display:grid;grid-template-columns:26px minmax(0,1fr);align-content:start;gap:2px 14px;padding:20px!important;background:var(--navy)}
#bkd-feat .bkd-ft .bkd-i{grid-row:span 2;width:26px;height:26px;margin-top:3px;color:var(--brass-l);stroke-width:1.4}
#bkd-feat .bkd-ft b{display:block;font-family:var(--display)!important;font-size:23px!important;font-weight:var(--display-w)!important;line-height:1.2!important;letter-spacing:0!important;color:#fff!important}
#bkd-feat .bkd-ft span{display:block;font-size:14px!important;line-height:1.55!important;color:rgba(255,255,255,.72)!important}
#bkd-feat .bkd-fe ul{display:grid;grid-template-columns:minmax(0,1fr);column-gap:28px}
#bkd-feat .bkd-fe li{display:flex;align-items:flex-start;gap:12px;padding:12px 0!important;border-bottom:1px solid var(--line)!important;font-size:15px!important;font-weight:500!important;line-height:1.45!important;color:var(--navy)!important}
#bkd-feat .bkd-fe li span{color:inherit!important;font-size:inherit!important;font-weight:inherit!important}
#bkd-feat .bkd-fe .bkd-i{width:16px;height:16px;margin-top:2px;color:var(--brass-d);stroke-width:2.2}
#bkd-near{padding-top:44px!important}
#bkd-near .bkd-nr{display:grid;grid-template-columns:minmax(0,1fr);gap:24px 32px;margin-top:6px!important;padding:24px!important;background:var(--mist)}
#bkd-near .bkd-nr-k{display:flex;align-items:center;gap:10px;margin:0 0 4px!important;font-size:11px!important;font-weight:600!important;line-height:1.4!important;letter-spacing:.16em!important;text-transform:uppercase!important;color:var(--brass-d)!important}
#bkd-near .bkd-nr-k .bkd-i{width:20px;height:20px;color:var(--brass)}
#bkd-near li{display:flex;justify-content:space-between;align-items:baseline;gap:16px;padding:9px 0!important;border-bottom:1px solid var(--line-2)!important;font-size:14.5px!important;line-height:1.4!important;color:var(--ink)!important}
#bkd-near .bkd-nr li:last-child{border-bottom:0!important}
#bkd-near .bkd-nr-own{margin-top:6px!important;border-top:1px solid var(--line)!important}
#bkd-near .bkd-nr-own li{border-bottom-color:var(--line)!important}
#bkd-near li span{color:inherit!important;font-size:inherit!important}
#bkd-near li b{flex:none;font-weight:600!important;color:var(--navy)!important;font-variant-numeric:tabular-nums;white-space:nowrap}
#bkd-near .bkd-nr-note{margin-top:12px!important;font-size:12px!important;line-height:1.55!important;color:var(--muted)!important}
@container (min-width:460px){#bkd-feat .bkd-fe ul{grid-template-columns:repeat(2,minmax(0,1fr))}}
@container (min-width:520px){#bkd-near .bkd-nr{grid-template-columns:repeat(2,minmax(0,1fr))}}
@container (min-width:560px){#bkd-feat .bkd-ft:is([data-n="2"],[data-n="4"],[data-n="6"]){grid-template-columns:repeat(2,minmax(0,1fr))}#bkd-feat .bkd-ft:not([data-n="1"],[data-n="3"]) li{display:block;padding:26px 24px!important}#bkd-feat .bkd-ft:not([data-n="1"],[data-n="3"]) b{margin:16px 0 6px!important}}
@container (min-width:680px){#bkd-feat .bkd-ft:is([data-n="3"],[data-n="6"]){grid-template-columns:repeat(3,minmax(0,1fr))}#bkd-feat .bkd-ft[data-n="3"] li{display:block;padding:26px 24px!important}#bkd-feat .bkd-ft[data-n="3"] b{margin:16px 0 6px!important}#bkd-feat .bkd-fe ul{grid-template-columns:repeat(3,minmax(0,1fr))}}
CSS;
	}

	add_action(
		'wp_head',
		function () {
			$x = blockke_dev_classic_extras();
			if ( ! $x['feat'] && ! $x['near'] && ! $x['own'] ) {
				return;
			}
			$font = blockke_dev_font_head( '.bkd-cls' );
			echo $font[0] . '<style id="bkd-cls-css">' . blockke_dev_classic_css() . $font[1] . "</style>\n"; // phpcs:ignore WordPress.Security.EscapeOutput -- fixed markup.
		},
		99
	);

	add_action(
		'wp_footer',
		function () {
			$x = blockke_dev_classic_extras();
			if ( ! $x['feat'] && ! $x['near'] && ! $x['own'] ) {
				return;
			}
			$html  = '';
			$icons = array( 'check' );
			if ( $x['feat'] ) {
				$html .= blockke_dev_classic_features( $x['feat'] );
				foreach ( $x['feat']['tiles'] as $t ) {
					$icons[] = $t['icon'];
				}
			}
			if ( $x['near'] || $x['own'] ) {
				$html .= blockke_dev_classic_nearby( $x['near'], $x['own'] );
				foreach ( $x['near'] as $g ) {
					$icons[] = $g['icon'];
				}
			}
			$move = <<<'JS'
(function(){
  var w=document.getElementById('bkd-cls');if(!w){return;}
  function box(id){var a=document.getElementById(id),b,d;if(!a){return null;}
    b=a.querySelector('.accordion-body,.panel-body');
    if(!b&&(d=a.querySelector('.listing_detail'))){b=d.parentNode;}
    return b;}
  var f=document.getElementById('bkd-feat'),fb=f&&box('accordion_features_details');
  if(fb){fb.insertBefore(f,fb.firstChild);fb.classList.add('bkd-feat-on');}
  var n=document.getElementById('bkd-near'),nb=n&&box('accordion_property_address');
  if(nb){nb.appendChild(n);}
  w.parentNode.removeChild(w);
})();
JS;
			echo blockke_dev_sprite( array_unique( $icons ) ) . '<div id="bkd-cls" hidden>' . $html . "</div>\n" // phpcs:ignore WordPress.Security.EscapeOutput -- escaped while building.
				. '<script id="bkd-cls-js" data-cfasync="false" data-no-optimize="1" data-no-defer="1">' . $move . "</script>\n";
		},
		25
	);

	/* =====================================================================
	   2) Development layout: data
	   ===================================================================== */

	function blockke_dev_agent( $aid ) {
		$cfg = blockke_dev_config();
		$a   = array(
			'name'          => '',
			'role'          => '',
			'photo'         => '',
			'url'           => '',
			'phone'         => $cfg['phone'],
			'phone_display' => $cfg['phone_display'],
			'wa'            => blockke_dev_digits( $cfg['whatsapp'] ),
			'email'         => '',
		);
		if ( $aid && 'publish' === get_post_status( $aid ) ) {
			$a['name']  = blockke_dev_text( get_the_title( $aid ) );
			$a['role']  = blockke_dev_text( get_post_meta( $aid, 'agent_position', true ) );
			$a['photo'] = (string) get_the_post_thumbnail_url( $aid, 'thumbnail' );
			$a['url']   = get_permalink( $aid );
			$mob        = get_post_meta( $aid, 'agent_mobile', true );
			if ( ! $mob ) {
				$mob = get_post_meta( $aid, 'agent_phone', true );
			}
			$d = blockke_dev_phone( $mob );
			if ( $d ) {
				$a['phone']         = '+' . $d;
				$a['phone_display'] = blockke_dev_phone_display( $d );
				$a['wa']            = $d;
			}
			$a['email'] = sanitize_email( get_post_meta( $aid, 'agent_email', true ) );
		}
		return $a;
	}

	function blockke_dev_unit_label_from_title( $title ) {
		$t     = blockke_dev_text( $title );
		$parts = preg_split( '/\s+[—–]\s+|\s*\|\s*|:\s+/u', $t );
		foreach ( $parts as $p ) {
			if ( preg_match( '/studio|bed|loft|penthouse|duplex|townhouse|villa|apartment|maisonette|unit/i', $p ) ) {
				$t = $p;
				break;
			}
		}
		$t = preg_replace( '/\s+(for sale|for rent|to let|from kes|from\s+\d|in\s+[A-Z][\w-]+).*$/iu', '', $t );
		return preg_replace( '/^[\s,.\-–—]+|[\s,.\-–—]+$/u', '', $t );
	}

	/** studio | loft | penthouse | number of bedrooms | '' */
	function blockke_dev_bed_key( $label, $beds = 0 ) {
		$l = strtolower( $label );
		if ( false !== strpos( $l, 'studio' ) || false !== strpos( $l, 'bedsitter' ) ) {
			return 'studio';
		}
		if ( false !== strpos( $l, 'penthouse' ) ) {
			return 'penthouse';
		}
		if ( false !== strpos( $l, 'loft' ) ) {
			return 'loft';
		}
		if ( preg_match( '/(\d+)\s*(?:(?:-|–|to|&|and)\s*\d+\s*)?(?:bed|br\b)/u', $l, $m ) ) {
			return (int) $m[1];
		}
		$words = array( 'one' => 1, 'two' => 2, 'three' => 3, 'four' => 4, 'five' => 5, 'six' => 6 );
		if ( preg_match( '/\b(one|two|three|four|five|six)[\s-]*bed/', $l, $m ) ) {
			return $words[ $m[1] ];
		}
		return $beds > 0 ? (int) $beds : '';
	}

	function blockke_dev_group_label( $key, $fallback ) {
		if ( 'studio' === $key ) {
			return 'Studio';
		}
		if ( 'loft' === $key ) {
			return 'Lofts';
		}
		if ( 'penthouse' === $key ) {
			return 'Penthouses';
		}
		if ( is_int( $key ) ) {
			return $key . ' Bed';
		}
		return $fallback;
	}

	function blockke_dev_subunits( $id ) {
		$list = get_post_meta( $id, 'property_subunits_list', true );
		$ids  = is_array( $list ) ? $list : array();
		$man  = trim( (string) get_post_meta( $id, 'property_subunits_list_manual', true ) );
		if ( '' !== $man ) {
			$ids = array_merge( $ids, preg_split( '/[\s,]+/', $man ) );
		}
		$ids = array_values( array_unique( array_filter( array_map( 'intval', $ids ) ) ) );
		if ( ! $ids ) {
			return array();
		}
		$posts  = get_posts(
			array(
				'post_type'      => 'estate_property',
				'post_status'    => 'publish',
				'post__in'       => $ids,
				'orderby'        => 'post__in',
				'posts_per_page' => 60,
			)
		);
		$factor = blockke_dev_sqft_factor();
		$out    = array();
		foreach ( $posts as $p ) {
			$label = trim( (string) get_post_meta( $p->ID, 'bke_unit_label', true ) );
			if ( '' === $label ) {
				$label = blockke_dev_unit_label_from_title( $p->post_title );
			}
			$size  = (float) get_post_meta( $p->ID, 'property_size', true );
			$out[] = array(
				'id'    => $p->ID,
				'label' => $label,
				'key'   => blockke_dev_bed_key( $label, (int) get_post_meta( $p->ID, 'property_bedrooms', true ) ),
				'url'   => get_permalink( $p ),
				'price' => (float) get_post_meta( $p->ID, 'property_price', true ),
				'size'  => $size > 0 ? blockke_dev_num( $size * $factor ) . ' m²' : '',
			);
		}
		return $out;
	}

	function blockke_dev_type_note( $t ) {
		$t    = trim( $t );
		$note = '';
		if ( preg_match( '/^(.*?)\s*\(([^)]+)\)\s*(.*)$/u', $t, $m ) ) {
			$t    = trim( $m[1] . ' ' . $m[3] );
			$note = trim( $m[2] );
		}
		if ( preg_match( '/^(.{4,}?)\s*(\+|,|–|—|\s-\s)\s*(.+)$/u', $t, $m ) ) {
			$t    = trim( $m[1] );
			$rest = trim( $m[3] );
			$rest = '+' === $m[2] ? '+ ' . $rest : ucfirst( $rest );
			$note = trim( $note . ( $note ? '; ' : '' ) . $rest );
		}
		return array( $t, $note );
	}

	/** Units from the description's unit table (Unit type / Size / Price), linked to the sub-unit listings. */
	function blockke_dev_units_from_table( $tokens, $subs ) {
		foreach ( $tokens as $t ) {
			if ( 'table' !== $t['tag'] ) {
				continue;
			}
			$tb = blockke_dev_table( $t['html'] );
			if ( ! $tb || ! $tb['head'] || ! $tb['rows'] ) {
				continue;
			}
			$ti = -1;
			$si = -1;
			$fi = -1;
			$pi = -1;
			foreach ( $tb['head'] as $i => $hd ) {
				$hl = strtolower( $hd );
				if ( $pi < 0 && preg_match( '/price|kes|amount|cost/', $hl ) ) {
					$pi = $i;
				} elseif ( preg_match( '/sq\.?\s*ft|sqft|ft²|square f/', $hl ) ) {
					if ( $fi < 0 ) {
						$fi = $i;
					}
				} elseif ( $si < 0 && preg_match( '/size|sqm|sq\.?\s*m|m²|m2|area/', $hl ) ) {
					$si = $i;
				} elseif ( $ti < 0 && preg_match( '/unit|type|residence|apartment|bed|layout|typolog|home/', $hl ) ) {
					$ti = $i;
				}
			}
			if ( $ti < 0 || ( $pi < 0 && $si < 0 && $fi < 0 ) || preg_match( '/feature|facilit|amenit|item/', strtolower( $tb['head'][0] ) ) ) {
				continue;
			}
			$units = array();
			foreach ( $tb['rows'] as $r ) {
				$c = $r['cells'];
				if ( ! isset( $c[ $ti ] ) || '' === $c[ $ti ] ) {
					continue;
				}
				list( $type, $note ) = blockke_dev_type_note( $c[ $ti ] );
				$key  = blockke_dev_bed_key( $type . ' ' . $note );
				$url  = '';
				foreach ( $r['links'] as $l ) {
					if ( $l ) {
						$url = $l;
						break;
					}
				}
				if ( '' === $url && '' !== $key ) {
					foreach ( $subs as $s ) {
						if ( $s['key'] === $key ) {
							$url = $s['url'];
							break;
						}
					}
				}
				$size = '';
				if ( $si >= 0 && isset( $c[ $si ] ) ) {
					$size = blockke_dev_size( $c[ $si ] );
				} elseif ( $fi >= 0 && isset( $c[ $fi ] ) ) {
					$size = blockke_dev_size( $c[ $fi ], true );
				}
				$units[] = array(
					'type'  => $type,
					'note'  => $note,
					'key'   => $key,
					'group' => blockke_dev_group_label( $key, $type ),
					'size'  => $size,
					'price' => ( $pi >= 0 && isset( $c[ $pi ] ) ) ? blockke_dev_parse_money( $c[ $pi ] ) : 0,
					'url'   => $url,
				);
			}
			if ( $units ) {
				return $units;
			}
		}
		return array();
	}

	function blockke_dev_units_from_subs( $subs ) {
		$units = array();
		foreach ( $subs as $s ) {
			$units[] = array(
				'type'  => $s['label'],
				'note'  => '',
				'key'   => $s['key'],
				'group' => blockke_dev_group_label( $s['key'], $s['label'] ),
				'size'  => $s['size'],
				'price' => $s['price'],
				'url'   => $s['url'],
			);
		}
		return $units;
	}

	function blockke_dev_size_range( $units ) {
		$vals = array();
		foreach ( $units as $u ) {
			if ( preg_match_all( '/\d+(?:\.\d+)?/', str_replace( ',', '', $u['size'] ), $m ) ) {
				foreach ( $m[0] as $v ) {
					$vals[] = (float) $v;
				}
			}
		}
		if ( ! $vals ) {
			return '';
		}
		$lo = round( min( $vals ) );
		$hi = round( max( $vals ) );
		return ( $lo === $hi ? number_format( $lo ) : number_format( $lo ) . ' – ' . number_format( $hi ) ) . ' m²';
	}

	function blockke_dev_homes_label( $units ) {
		$keys = array();
		foreach ( $units as $u ) {
			$keys[] = $u['key'];
		}
		$ints = array_values( array_filter( $keys, 'is_int' ) );
		$s    = in_array( 'studio', $keys, true );
		$out  = '';
		if ( $ints ) {
			$lo  = $s ? 'Studio' : min( $ints );
			$hi  = max( $ints );
			$out = ( (string) $lo === (string) $hi ) ? $hi . ' Bed' : $lo . ' – ' . $hi . ' Bed';
		} elseif ( $s ) {
			$out = 'Studios';
		}
		if ( in_array( 'loft', $keys, true ) ) {
			$out .= ( $out ? ' & ' : '' ) . 'Lofts';
		}
		if ( in_array( 'penthouse', $keys, true ) ) {
			$out .= ( $out ? ' & ' : '' ) . 'Penthouses';
		}
		return $out;
	}

	function blockke_dev_facts( $id, $kv, $units, $completion_shown ) {
		$raw = trim( (string) get_post_meta( $id, 'bke_dev_facts', true ) );
		if ( '' !== $raw ) {
			$facts = array();
			foreach ( preg_split( '/\r?\n/', $raw ) as $line ) {
				$p = array_map( 'trim', explode( '|', $line, 2 ) );
				if ( 2 === count( $p ) && '' !== $p[0] && '' !== $p[1] ) {
					$facts[] = $p;
				}
			}
			return array_slice( $facts, 0, 6 );
		}
		$facts = array();
		$total = blockke_dev_kv_get( $kv, '/total units|number of units|no\.? of units|^units$|^residences$|^homes$/i' );
		if ( $total && preg_match( '/\d/', $total ) && mb_strlen( $total ) <= 24 ) {
			$facts[] = array( 'Residences', $total );
		}
		$homes = blockke_dev_homes_label( $units );
		if ( $homes ) {
			$facts[] = array( 'Homes', $homes );
		}
		$sizes = blockke_dev_size_range( $units );
		if ( ! $sizes ) {
			$sz = blockke_dev_kv_get( $kv, '/size/i' );
			if ( $sz ) {
				$sizes = blockke_dev_size( $sz );
			}
		}
		if ( $sizes ) {
			$facts[] = array( 'Sizes', $sizes );
		}
		$fl = blockke_dev_kv_get( $kv, '/floors|storeys|stories|building|tower/i' );
		if ( $fl ) {
			if ( preg_match( '/(\d+)\s*[- ]?\s*(?:floor|storey|stor(?:y|ies)|level)/i', $fl, $m ) ) {
				$facts[] = array( 'Floors', $m[1] );
			} elseif ( mb_strlen( $fl ) <= 28 ) {
				$facts[] = array( 'Building', $fl );
			}
		}
		$lifts = blockke_dev_kv_get( $kv, '/elevator|lift/i' );
		if ( $lifts && mb_strlen( $lifts ) <= 24 ) {
			$facts[] = array( 'Lifts', $lifts );
		}
		$park = blockke_dev_kv_get( $kv, '/parking/i' );
		if ( $park && mb_strlen( $park ) <= 28 ) {
			$facts[] = array( 'Parking', ucfirst( $park ) );
		}
		$land = blockke_dev_kv_get( $kv, '/land size|plot|acre/i' );
		if ( $land && mb_strlen( $land ) <= 24 ) {
			$facts[] = array( 'Land', $land );
		}
		if ( count( $facts ) < 3 && $completion_shown ) {
			$facts[] = array( 'Completion', $completion_shown );
		}
		return array_slice( $facts, 0, 6 );
	}

	function blockke_dev_amenity_icon( $title ) {
		$t = strtolower( $title );
		$map = array(
			'/secur|safety|access/'                    => 'shield',
			'/work|study|connect|internet|office/'      => 'laptop',
			'/family|social|kid|child|play/'            => 'users',
			'/parking|car\b/'                           => 'car',
			'/water|power|energy|solar|green|eco/'      => 'leaf',
			'/unit|interior|finish|spec|kitchen|home/'  => 'sofa',
			'/wellness|leisure|recreation|pool|sport|lifestyle|fitness/' => 'waves',
			'/convenience|service|retail|shop|building|lobby/' => 'building',
			'/entertain|cinema|lounge/'                 => 'film',
		);
		foreach ( $map as $re => $icon ) {
			if ( preg_match( $re, $t ) ) {
				return $icon;
			}
		}
		return 'star';
	}

	function blockke_dev_amenities( $id, $sections ) {
		$ai = blockke_dev_find( $sections, '/amenit|facilit|lifestyle/i', '/finish|spec/i' );
		if ( $ai >= 0 ) {
			$groups = array();
			$title  = '';
			foreach ( $sections[ $ai ]['tokens'] as $t ) {
				if ( 'h' === $t['tag'] ) {
					$title = blockke_dev_text( $t['inner'] );
				} elseif ( 'p' === $t['tag'] ) {
					$title = preg_match( '#^\s*<(strong|b)\b[^>]*>(.*?)</\1>\s*:?\s*$#is', $t['inner'], $s ) ? rtrim( blockke_dev_text( $s[2] ), ': ' ) : '';
				} elseif ( 'ul' === $t['tag'] && '' !== $title ) {
					$items = array();
					foreach ( blockke_dev_list_items( $t['inner'] ) as $it ) {
						$items[] = $it['text'];
					}
					if ( $items ) {
						$groups[] = array( 'title' => $title, 'icon' => blockke_dev_amenity_icon( $title ), 'items' => $items );
					}
					$title = '';
				}
			}
			if ( count( $groups ) >= 2 ) {
				return $groups;
			}
		}
		return blockke_dev_feature_groups( $id );
	}

	/** The listing's Features & Amenities terms, grouped under their parent terms (best groups first). */
	function blockke_dev_feature_groups( $id ) {
		$terms = get_the_terms( $id, 'property_features' );
		if ( ! is_array( $terms ) || ! $terms ) {
			return array();
		}
		$order   = array( 'lifestyle-recreation', 'family-convenience', 'security-access', 'building-parking', 'water-power', 'unit-features', 'work-connectivity' );
		$buckets = array();
		$other   = array();
		foreach ( $terms as $t ) {
			if ( $t->parent ) {
				$buckets[ $t->parent ][] = blockke_dev_text( $t->name );
			} elseif ( ! get_term_children( $t->term_id, 'property_features' ) ) {
				$other[] = blockke_dev_text( $t->name );
			}
		}
		$groups = array();
		foreach ( $buckets as $pid => $items ) {
			$p = get_term( $pid, 'property_features' );
			if ( ! $p || is_wp_error( $p ) ) {
				continue;
			}
			$title    = blockke_dev_text( $p->name );
			$rank     = array_search( $p->slug, $order, true );
			$groups[] = array( 'title' => $title, 'icon' => blockke_dev_amenity_icon( $title ), 'items' => $items, 'rank' => false === $rank ? 99 : $rank );
		}
		usort(
			$groups,
			function ( $a, $b ) {
				return $a['rank'] - $b['rank'];
			}
		);
		if ( $other ) {
			$groups[] = array( 'title' => $groups ? 'Also included' : 'Amenities', 'icon' => 'star', 'items' => $other );
		}
		return $groups;
	}

	/**
	 * Amenities worth a feature tile: pattern on the lower-case item, kind, short title, icon, rank, one line.
	 * The first match wins, so specific patterns come before general ones. Anything else goes in the checklist.
	 */
	function blockke_dev_amenity_lib() {
		return array(
			array( '/\bbeach|ocean ?front|sea ?front|water ?front/u', 'beach', 'Beach access', 'waves', 100, 'Sand, sea and salt air on your doorstep.' ),
			array( '/infinity/u', 'pool', 'Infinity pool', 'waves', 99, 'Swim to the edge of the view.' ),
			array( '/(kid|child|paddling|toddler)[^,;]{0,14}pool/u', 'kidspool', 'Kids’ pool', 'waves', 70, 'Shallow water for the youngest swimmers.' ),
			array( '/(roof ?top|sky|terrace)[^,;]{0,14}pool/u', 'pool', 'Rooftop pool', 'waves', 98, 'Laps with the skyline for company.' ),
			array( '/(heated|indoor)[^,;]{0,20}pool/u', 'pool', 'Heated pool', 'waves', 97, 'Warm water all year round, whatever the weather.' ),
			array( '/swimming|(?<!car )\bpool\b(?! ?table)/u', 'pool', 'Swimming pool', 'waves', 95, 'Morning laps, or a slow afternoon by the water.' ),
			array( '/(sky|roof ?top|roof|terrace)[^,;]{0,8}gardens?/u', 'skygarden', 'Sky garden', 'leaf', 90, 'Green space with a view, a lift ride from home.' ),
			array( '/sky ?(lounge|bar|terrace|deck|restaurant|club)|roof ?top ?(lounge|terrace|deck|bar|restaurant|area|space|club)|roof (terrace|deck|lounge)|^roof ?top$/u', 'sky', 'Sky lounge', 'glass', 92, 'Sundowners and long evenings above the city.' ),
			array( '/cinema|theat(re|er)|movie|screening room|media room/u', 'cinema', 'Private cinema', 'film', 89, 'Movie nights without the Nairobi traffic.' ),
			array( '/\bspa\b|sauna|steam|jacuzzi|hot tub|massage|hammam|wellness (centre|center|lounge|suite)/u', 'spa', 'Spa and sauna', 'lotus', 88, 'Unwind after a long day without leaving home.' ),
			array( '/\bgym|fitness|health club|work ?out/u', 'gym', 'Gym', 'dumbbell', 87, 'Train before work, with no membership or commute.' ),
			array( '/forest|botanical|arboretum|\bnature\b/u', 'nature', 'Nature walks', 'leaf', 86, 'Trees and birdsong instead of traffic.' ),
			array( '/\bkids?\b|child|play ?(area|ground|room|park|zone)|creche|crèche|day ?care/u', 'kids', 'Kids’ play area', 'kite', 85, 'A safe place for children to play, close to home.' ),
			array( '/restaurant|caf[eé]|coffee|bistro|eatery|food court/u', 'cafe', 'Café and restaurant', 'cup', 84, 'Coffee, lunch or dinner without getting in the car.' ),
			array( '/golf/u', 'golf', 'Golf', 'flag', 83, 'Tee times close to home.' ),
			array( '/garden|landscap|green (space|area)|lawn|(?<!car )\bpark\b|courtyard|orchard/u', 'garden', 'Gardens', 'leaf', 82, 'Green space for slow mornings and fresh air.' ),
			array( '/yoga|pilates|aerobics|dance studio|meditation/u', 'yoga', 'Yoga studio', 'lotus', 80, 'Room to stretch, breathe and reset.' ),
			array( '/supermarket|grocer|mini ?mart|convenience store|retail|\bshops?\b|shopping|\bmall\b/u', 'shops', 'Shops on site', 'bag', 79, 'Groceries and everyday errands, close at hand.' ),
			array( '/co-?working|business (centre|center|lounge)|work ?(space|lounge|station)|study|library|reading/u', 'work', 'Co-working lounge', 'laptop', 78, 'A quiet place to work or study, steps from home.' ),
			array( '/tennis|squash|basketball|padel|paddle|volleyball|badminton|football|sports? (court|ground|field|facilit)/u', 'sport', 'Sports courts', 'ball', 77, 'Game on, without driving to the club.' ),
			array( '/bbq|barbe?cue|braai|nyama|grill (area|deck|terrace)/u', 'bbq', 'BBQ area', 'flame', 76, 'Weekend nyama choma with family and neighbours.' ),
			array( '/club ?house|lounge|social|entertainment|function (room|hall)|events?\b|party|multi-?purpose|dining|residents/u', 'social', 'Residents’ lounge', 'users', 74, 'Room to host friends, family and celebrations.' ),
			array( '/jog|running|walking (track|trail|path)|cycl|\btrails?\b|\btracks?\b/u', 'trail', 'Jogging track', 'route', 72, 'Morning runs without leaving the gate.' ),
		);
	}

	/** Splits the amenity groups into feature tiles (one per kind, best first, 1-4 or 6) and a checklist. */
	function blockke_dev_amenity_layout( $groups ) {
		$lib   = blockke_dev_amenity_lib();
		$items = array();
		$seen  = array();
		foreach ( $groups as $g ) {
			foreach ( $g['items'] as $it ) {
				$it = trim( preg_replace( '/\s+/u', ' ', (string) $it ) );
				$lc = strtolower( $it );
				if ( '' === $it || isset( $seen[ $lc ] ) ) {
					continue;
				}
				$seen[ $lc ] = true;
				$hit         = null;
				foreach ( $lib as $l ) {
					if ( preg_match( $l[0], $lc ) ) {
						$hit = $l;
						break;
					}
				}
				$items[] = array(
					'text' => $it,
					'lib'  => $hit,
					'i'    => count( $items ),
				);
			}
		}
		$cand = array();
		foreach ( $items as $x ) {
			if ( $x['lib'] && $x['lib'][4] >= 50 ) {
				$cand[] = $x;
			}
		}
		usort(
			$cand,
			function ( $a, $b ) {
				return $a['lib'][4] === $b['lib'][4] ? $a['i'] - $b['i'] : $b['lib'][4] - $a['lib'][4];
			}
		);
		$picked = array();
		$kinds  = array();
		foreach ( $cand as $c ) {
			if ( count( $picked ) >= 6 ) {
				break;
			}
			if ( ! isset( $kinds[ $c['lib'][1] ] ) ) {
				$kinds[ $c['lib'][1] ] = true;
				$picked[]              = $c;
			}
		}
		if ( 5 === count( $picked ) ) {
			array_pop( $picked ); // full rows: 1, 2, 3, 4 or 6 tiles
		}
		$tiles = array();
		$used  = array();
		$shown = array();
		foreach ( $picked as $c ) {
			$used[ $c['i'] ]       = true;
			$shown[ $c['lib'][1] ] = true;
			$long                  = mb_strlen( $c['text'] ) > 30;
			$tiles[]               = array(
				'icon'  => $c['lib'][3],
				'title' => $long ? $c['lib'][2] : ucfirst( $c['text'] ),
				'text'  => $long ? preg_replace( '/[.;:,\s]+$/u', '', $c['text'] ) . '.' : $c['lib'][5],
			);
		}
		$rest       = array();
		$essentials = true;
		foreach ( $items as $x ) {
			if ( ! isset( $used[ $x['i'] ] ) && ! ( $x['lib'] && isset( $shown[ $x['lib'][1] ] ) ) ) { // a second pool or gym adds nothing
				$rest[]     = $x['text'];
				$essentials = $essentials && ! ( $x['lib'] && $x['lib'][4] >= 50 );
			}
		}
		return array(
			'tiles'      => $tiles,
			'rest'       => $rest,
			'essentials' => $essentials,
		);
	}

	/** Photos whose caption names an amenity ("Indoor heated pool", "Sky Lounge"), in gallery order, for the Amenities photo strip. */
	function blockke_dev_amenity_photos( $captions ) {
		$lib = blockke_dev_amenity_lib();
		$out = array();
		foreach ( $captions as $aid => $cap ) {
			$lc  = strtolower( $cap );
			$hit = (bool) preg_match( '/lobby|reception|concierge|roof ?top|amenit/u', $lc );
			foreach ( $lib as $l ) {
				if ( $hit ) {
					break;
				}
				$hit = (bool) preg_match( $l[0], $lc );
			}
			if ( $hit ) {
				$out[] = $aid;
			}
		}
		return array_slice( $out, 0, 8 );
	}

	/** The Amenities section's own introduction, if the description has one. */
	function blockke_dev_amenity_intro( $sections ) {
		$ai = blockke_dev_find( $sections, '/amenit|facilit|lifestyle/i', '/finish|spec/i' );
		if ( $ai < 0 ) {
			return '';
		}
		foreach ( $sections[ $ai ]['tokens'] as $t ) {
			if ( 'p' !== $t['tag'] || preg_match( '#^\s*<(strong|b)\b[^>]*>(.*?)</\1>\s*:?\s*$#is', $t['inner'] ) ) {
				continue;
			}
			$txt = blockke_dev_text( $t['inner'] );
			if ( mb_strlen( $txt ) >= 40 && ! preg_match( '/:$/', $txt ) ) {
				return blockke_dev_trim_words( blockke_dev_sentences( $txt, 2 ), 260 );
			}
		}
		return '';
	}

	/** Escapes a heading and turns *words* into its italic accent. */
	function blockke_dev_accent( $s ) {
		return preg_replace( '/\*([^*]+)\*/u', '<em>$1</em>', esc_html( $s ) );
	}

	/* =====================================================================
	   Nearby places: straight-line distances from the listing's map pin
	   ===================================================================== */

	/**
	 * Kinds of place, in display order: group heading, name for one place, icon, search radius (km), places shown.
	 * Kinds that share a heading are listed together (schools and universities under Education).
	 */
	function blockke_dev_place_kinds() {
		return array(
			'work'   => array( 'Business districts', 'Business district', 'building', 40, 2 ),
			'shop'   => array( 'Shopping', 'Shopping', 'bag', 6, 2 ),
			'school' => array( 'Education', 'School', 'cap', 8, 2 ),
			'uni'    => array( 'Education', 'University', 'cap', 5, 2 ),
			'health' => array( 'Hospitals', 'Hospital', 'medical', 8, 2 ),
			'park'   => array( 'Parks', 'Park', 'leaf', 6, 2 ),
			'air'    => array( 'Airports', 'Airport', 'plane', 60, 2 ),
		);
	}

	/** Nairobi landmarks: name, kind, latitude, longitude[, own radius in km]. Add places here to widen coverage. */
	function blockke_dev_landmarks() {
		return array(
			array( 'Nairobi CBD', 'work', -1.28840, 36.82280 ),
			array( 'Westlands', 'work', -1.26694, 36.81167 ),
			array( 'Upper Hill', 'work', -1.29972, 36.81610, 8 ),
			array( 'UN offices, Gigiri', 'work', -1.23233, 36.80753, 8 ),
			array( 'Sarit Centre', 'shop', -1.25972, 36.80139 ),
			array( 'Westgate Mall', 'shop', -1.25694, 36.80333 ),
			array( 'The Village Market', 'shop', -1.22917, 36.80472 ),
			array( 'Two Rivers Mall', 'shop', -1.21056, 36.79444 ),
			array( 'Rosslyn Riviera Mall', 'shop', -1.21595, 36.79954 ),
			array( 'The Junction Mall', 'shop', -1.29840, 36.76250 ),
			array( 'Yaya Centre', 'shop', -1.29250, 36.78750 ),
			array( 'Galleria Mall', 'shop', -1.34370, 36.76560 ),
			array( 'The Hub Karen', 'shop', -1.32036, 36.70411 ),
			array( 'Garden City Mall', 'shop', -1.23194, 36.87778 ),
			array( 'Thika Road Mall', 'shop', -1.21956, 36.88833 ),
			array( 'International School of Kenya', 'school', -1.23165, 36.76424 ),
			array( 'Rosslyn Academy', 'school', -1.22480, 36.80854 ),
			array( 'Hillcrest International Schools', 'school', -1.33849, 36.74215 ),
			array( 'Brookhouse School', 'school', -1.34403, 36.76478 ),
			array( 'Braeburn School', 'school', -1.28892, 36.75561 ),
			array( 'Kenton College', 'school', -1.27943, 36.78707 ),
			array( 'Aga Khan Junior Academy', 'school', -1.26600, 36.82271 ),
			array( 'Strathmore University', 'uni', -1.31000, 36.81333 ),
			array( 'University of Nairobi', 'uni', -1.28040, 36.81630 ),
			array( 'USIU-Africa', 'uni', -1.21806, 36.87917 ),
			array( 'Aga Khan University Hospital', 'health', -1.26180, 36.82386 ),
			array( 'MP Shah Hospital', 'health', -1.26353, 36.81211 ),
			array( 'The Nairobi Hospital', 'health', -1.29612, 36.80472 ),
			array( 'Kenyatta National Hospital', 'health', -1.30054, 36.80695 ),
			array( 'The Karen Hospital', 'health', -1.33611, 36.72615 ),
			array( 'Gertrude’s Children’s Hospital', 'health', -1.25606, 36.83167 ),
			array( 'Karura Forest', 'park', -1.23700, 36.83033 ),
			array( 'Nairobi Arboretum', 'park', -1.27430, 36.81310 ),
			array( 'City Park', 'park', -1.26330, 36.83060 ),
			array( 'Jomo Kenyatta International Airport', 'air', -1.31920, 36.92780 ),
			array( 'Wilson Airport', 'air', -1.32172, 36.81483, 10 ),
		);
	}

	/** Great-circle distance in km. */
	function blockke_dev_km( $lat1, $lng1, $lat2, $lng2 ) {
		$p = M_PI / 180;
		$a = sin( ( $lat2 - $lat1 ) * $p / 2 ) ** 2 + cos( $lat1 * $p ) * cos( $lat2 * $p ) * sin( ( $lng2 - $lng1 ) * $p / 2 ) ** 2;
		return 12742 * asin( min( 1, sqrt( $a ) ) );
	}

	function blockke_dev_km_label( $km ) {
		return ( $km < 10 ? number_format( max( 0.1, $km ), 1 ) : number_format( round( $km ) ) ) . ' km';
	}

	/**
	 * The nearest places of each kind around a map pin, grouped for display:
	 * array( array( 'label', 'icon', 'places' => array( array( name, km, name for one place ), ... ) ), ... ).
	 * Empty when the pin is missing or outside Nairobi, which the landmark list covers.
	 */
	function blockke_dev_nearby( $lat, $lng ) {
		$lat = (float) $lat;
		$lng = (float) $lng;
		if ( ( ! $lat && ! $lng ) || abs( $lat ) > 90 || abs( $lng ) > 180 || blockke_dev_km( $lat, $lng, -1.28840, 36.82280 ) > 60 ) {
			return array();
		}
		if ( function_exists( 'wpresidence_get_option' ) ) { // a pin never moved off the theme's default map centre is not a location
			$dlat = (float) wpresidence_get_option( 'wp_estate_general_latitude', '' );
			$dlng = (float) wpresidence_get_option( 'wp_estate_general_longitude', '' );
			if ( ( $dlat || $dlng ) && abs( $lat - $dlat ) < 0.00001 && abs( $lng - $dlng ) < 0.00001 ) {
				return array();
			}
		}
		$kinds  = blockke_dev_place_kinds();
		$groups = array();
		foreach ( $kinds as $k ) {
			$groups[ $k[0] ] = array( 'label' => $k[0], 'icon' => $k[2], 'max' => $k[4], 'places' => array() );
		}
		foreach ( blockke_dev_landmarks() as $l ) {
			if ( ! isset( $kinds[ $l[1] ] ) ) {
				continue;
			}
			$k  = $kinds[ $l[1] ];
			$km = blockke_dev_km( $lat, $lng, $l[2], $l[3] );
			if ( $km <= ( isset( $l[4] ) ? $l[4] : $k[3] ) ) {
				$groups[ $k[0] ]['places'][] = array( $l[0], $km, $k[1] );
			}
		}
		$out = array();
		foreach ( $groups as $g ) {
			if ( ! $g['places'] ) {
				continue;
			}
			usort(
				$g['places'],
				function ( $a, $b ) {
					return $a[1] < $b[1] ? -1 : ( $a[1] > $b[1] ? 1 : 0 );
				}
			);
			$g['places'] = array_slice( $g['places'], 0, $g['max'] );
			unset( $g['max'] );
			$out[] = $g;
		}
		return $out;
	}

	/** The listing's own "Places nearby" box (Place | travel time per line), if filled in. */
	function blockke_dev_places_override( $id ) {
		$places = array();
		$raw    = trim( (string) get_post_meta( $id, 'bke_dev_places', true ) );
		if ( '' !== $raw ) {
			foreach ( preg_split( '/\r?\n/', $raw ) as $line ) {
				$p = array_map( 'trim', explode( '|', $line, 2 ) );
				if ( '' !== $p[0] ) {
					$places[] = array( $p[0], isset( $p[1] ) ? $p[1] : '' );
				}
			}
		}
		return $places;
	}

	/** "Who it suits" from the description: its list, and the closing note on who it suits less well. */
	function blockke_dev_suits( $sections ) {
		$out = array(
			'items' => array(),
			'note'  => '',
		);
		$i   = blockke_dev_find( $sections, '/\bwho\b.*\b(suits?|for)\b|suits? (best|well)|ideal (for|buyers)|right for you/i', '/faq|question/i' );
		if ( $i < 0 ) {
			return $out;
		}
		foreach ( $sections[ $i ]['tokens'] as $t ) {
			if ( 'ul' === $t['tag'] && ! $out['items'] ) {
				foreach ( blockke_dev_list_items( $t['inner'] ) as $it ) {
					if ( mb_strlen( $it['text'] ) <= 140 ) {
						$out['items'][] = rtrim( $it['text'], ' .;' );
					}
				}
			} elseif ( 'p' === $t['tag'] && $out['items'] && '' === $out['note'] ) {
				$tx = blockke_dev_text( $t['inner'] );
				if ( preg_match( '/less well|not (ideal|suited|for)|may not suit|isn.t for/i', $tx ) ) {
					$out['note'] = blockke_dev_trim_words( blockke_dev_sentences( $tx, 2 ), 320 );
				}
			}
		}
		$out['items'] = array_slice( $out['items'], 0, 8 );
		return $out;
	}

	/** The description's investor view: up to three sentences, leaving out links and "see our analysis" asides. */
	function blockke_dev_investors( $sections ) {
		$i = blockke_dev_find( $sections, '/investor|investment|rental yield|returns/i', '/faq|question/i' );
		if ( $i < 0 ) {
			return '';
		}
		$keep = array();
		foreach ( $sections[ $i ]['tokens'] as $t ) {
			if ( 'p' !== $t['tag'] ) {
				continue;
			}
			foreach ( preg_split( '/(?<=[.!?])\s+(?=[A-Z0-9“"])/u', blockke_dev_text( $t['inner'] ) ) as $sn ) {
				if ( count( $keep ) < 3 && '' !== $sn && ! preg_match( '/\b(see|read) our\b|\bask (block|us)\b|\bclick\b/i', $sn ) ) {
					$keep[] = $sn;
				}
			}
		}
		return blockke_dev_trim_words( implode( ' ', $keep ), 640 );
	}

	function blockke_dev_faqs( $tokens ) {
		$faqs  = array();
		$q     = '';
		$a     = array();
		$allow = array(
			'p'      => array(),
			'a'      => array( 'href' => true, 'target' => true, 'rel' => true ),
			'strong' => array(),
			'b'      => array(),
			'em'     => array(),
			'i'      => array(),
			'br'     => array(),
			'ul'     => array(),
			'ol'     => array(),
			'li'     => array(),
		);
		foreach ( $tokens as $t ) {
			if ( 'h' === $t['tag'] ) {
				if ( '' !== $q && $a ) {
					$faqs[] = array( $q, implode( '', $a ) );
				}
				$q = rtrim( blockke_dev_text( $t['inner'] ), ':' );
				$a = array();
				continue;
			}
			if ( 'details' === $t['tag'] && preg_match( '#<summary[^>]*>(.*?)</summary>(.*)#is', $t['inner'], $d ) ) {
				$faqs[] = array( blockke_dev_text( $d[1] ), wp_kses( $d[2], $allow ) );
				continue;
			}
			if ( 'p' === $t['tag'] ) {
				if ( preg_match( '#^\s*<(strong|b)\b[^>]*>(.*?)</\1>\s*<br\s*/?>\s*(.+)$#is', $t['inner'], $s ) ) {
					if ( '' !== $q && $a ) {
						$faqs[] = array( $q, implode( '', $a ) );
					}
					$q      = '';
					$a      = array();
					$faqs[] = array( rtrim( blockke_dev_text( $s[2] ), ':' ), wp_kses( '<p>' . $s[3] . '</p>', $allow ) );
					continue;
				}
				if ( preg_match( '#^\s*<(strong|b)\b[^>]*>(.*?)</\1>\s*$#is', $t['inner'], $s ) && preg_match( '/\?\s*$/', blockke_dev_text( $s[2] ) ) ) {
					if ( '' !== $q && $a ) {
						$faqs[] = array( $q, implode( '', $a ) );
					}
					$q = blockke_dev_text( $s[2] );
					$a = array();
					continue;
				}
			}
			if ( '' !== $q && ( 'p' === $t['tag'] || 'ul' === $t['tag'] ) ) {
				$a[] = wp_kses( $t['html'], $allow );
			}
		}
		if ( '' !== $q && $a ) {
			$faqs[] = array( $q, implode( '', $a ) );
		}
		return $faqs;
	}

	/** Short display name for a listing: its Project, else the title without the selling words. */
	function blockke_dev_listing_name( $pid ) {
		$proj = blockke_dev_first_term( $pid, 'property_project' );
		if ( $proj ) {
			return blockke_dev_text( $proj->name );
		}
		$t = blockke_dev_text( get_the_title( $pid ) );
		if ( preg_match( '/\bfor (?:sale|rent) at ([A-Z][^,|—–]+)/u', $t, $m ) ) {
			return trim( $m[1] );
		}
		$parts = preg_split( '/\s+[—–]\s+|\s*\|\s*|:\s+/u', $t );
		$n     = preg_replace( '/\s+(?:(?:apartments?|homes|houses|villas|townhouses|maisonettes|units)\s+)?for\s+(?:sale|rent)\b.*$/iu', '', $parts[0] );
		return trim( $n, ' ,.-' );
	}

	function blockke_dev_similar( $id, $area_t, $exclude, $limit ) {
		$base   = array(
			'post_type'           => 'estate_property',
			'post_status'         => 'publish',
			'no_found_rows'       => true,
			'ignore_sticky_posts' => true,
			'orderby'             => 'date',
			'order'               => 'DESC',
			'fields'              => 'ids',
		);
		$sale   = array(
			array( 'taxonomy' => 'property_action_category', 'field' => 'slug', 'terms' => array( 'for-sale' ) ),
			array( 'taxonomy' => 'property_status', 'field' => 'slug', 'terms' => array( 'sold' ), 'operator' => 'NOT IN' ),
		);
		$master = array( array( 'key' => 'property_has_subunits', 'value' => '1' ) );
		$single = array(
			'relation' => 'OR',
			array( 'key' => 'property_subunits_master', 'compare' => 'NOT EXISTS' ),
			array( 'key' => 'property_subunits_master', 'value' => array( '', '0' ), 'compare' => 'IN' ),
		);
		$tries  = array();
		$manual = array_filter( array_map( 'intval', preg_split( '/[\s,]+/', (string) get_post_meta( $id, 'bke_dev_similar', true ) ) ) );
		if ( $manual ) {
			$tries[] = array( 'post__in' => $manual, 'orderby' => 'post__in' );
		}
		if ( $area_t ) {
			$area    = array( 'taxonomy' => 'property_area', 'field' => 'term_id', 'terms' => array( $area_t->term_id ) );
			$tries[] = array( 'tax_query' => array_merge( $sale, array( $area ) ), 'meta_query' => $master );
			$tries[] = array( 'tax_query' => array_merge( $sale, array( $area ) ), 'meta_query' => $single );
		}
		$tries[] = array( 'tax_query' => $sale, 'meta_query' => $master );
		$picked  = array();
		foreach ( $tries as $t ) {
			if ( count( $picked ) >= $limit ) {
				break;
			}
			$q      = new WP_Query( array_merge( $base, $t, array( 'post__not_in' => array_merge( $exclude, $picked ), 'posts_per_page' => $limit - count( $picked ) ) ) );
			$picked = array_merge( $picked, array_map( 'intval', $q->posts ) );
		}
		$cards = array();
		foreach ( $picked as $pid ) {
			$name  = blockke_dev_listing_name( $pid );
			$price = (float) get_post_meta( $pid, 'property_price', true );
			$from  = '1' === (string) get_post_meta( $pid, 'property_has_subunits', true ) || blockke_dev_is_offplan( $pid );
			$area  = blockke_dev_first_term( $pid, 'property_area' );
			$beds  = (int) get_post_meta( $pid, 'property_bedrooms', true );
			$comp  = blockke_dev_text( get_post_meta( $pid, 'bke_completion_stated', true ) );
			$meta  = array();
			if ( ! $from && $beds ) {
				$meta[] = $beds . ' Bed';
			}
			if ( $area ) {
				$meta[] = blockke_dev_text( $area->name );
			}
			if ( blockke_dev_is_offplan( $pid ) ) {
				$meta[] = $comp ? 'Completion ' . $comp : 'Off-plan';
			} else {
				$meta[] = 'Ready';
			}
			$cards[] = array(
				'url'   => get_permalink( $pid ),
				'img'   => (int) get_post_thumbnail_id( $pid ),
				'name'  => $name,
				'price' => $price > 0 ? ( $from ? 'From ' : '' ) . blockke_dev_short( $price ) : 'Price on request',
				'meta'  => implode( ' · ', $meta ),
			);
		}
		return $cards;
	}

	function blockke_dev_video( $id ) {
		$ov = trim( (string) get_post_meta( $id, 'bke_dev_video', true ) );
		if ( $ov ) {
			if ( preg_match( '#(?:youtu\.be/|v=|embed/|shorts/)([\w-]{11})#', $ov, $m ) ) {
				return array( 'type' => 'youtube', 'id' => $m[1] );
			}
			if ( preg_match( '#vimeo\.com/(?:video/)?(\d+)#', $ov, $m ) ) {
				return array( 'type' => 'vimeo', 'id' => $m[1] );
			}
		}
		$vid = trim( (string) get_post_meta( $id, 'embed_video_id', true ) );
		if ( $vid && preg_match( '/^[\w-]+$/', $vid ) ) {
			return array( 'type' => 'vimeo' === get_post_meta( $id, 'embed_video_type', true ) ? 'vimeo' : 'youtube', 'id' => $vid );
		}
		$tour = (string) get_post_meta( $id, 'embed_virtual_tour', true );
		if ( $tour && preg_match( '#src=["\'](https://[^"\']+)["\']#i', $tour, $m ) ) {
			return array( 'type' => 'tour', 'id' => esc_url_raw( html_entity_decode( $m[1] ) ) );
		}
		return null;
	}

	function blockke_dev_brochure( $id ) {
		$b = trim( (string) get_post_meta( $id, 'bke_dev_brochure', true ) );
		if ( $b ) {
			return esc_url_raw( $b );
		}
		$pdf = get_posts(
			array(
				'post_type'      => 'attachment',
				'post_parent'    => $id,
				'post_mime_type' => 'application/pdf',
				'posts_per_page' => 1,
				'orderby'        => 'menu_order date',
				'order'          => 'ASC',
			)
		);
		return $pdf ? wp_get_attachment_url( $pdf[0]->ID ) : '';
	}

	function blockke_dev_data( $id ) {
		static $cache = array();
		if ( isset( $cache[ $id ] ) ) {
			return $cache[ $id ];
		}
		$post = get_post( $id );
		if ( ! $post || 'estate_property' !== $post->post_type ) {
			return null;
		}
		$cfg    = blockke_dev_config();
		$parsed = blockke_dev_parsed( $post );
		$secs   = $parsed['sections'];
		$kv     = blockke_dev_glance( $post );

		// Names and place
		$title   = blockke_dev_text( get_the_title( $id ) );
		$project = blockke_dev_first_term( $id, 'property_project' );
		$name    = blockke_dev_text( get_post_meta( $id, 'bke_dev_name', true ) );
		if ( '' === $name ) {
			$parts = preg_split( '/\s+[—–]\s+|\s*\|\s*|:\s+/u', $title );
			$name  = trim( $parts[0] );
			if ( mb_strlen( $name ) > 52 && $project ) {
				$name = blockke_dev_text( $project->name );
			}
		}
		$short     = $project ? blockke_dev_text( $project->name ) : $name;
		$area_t    = blockke_dev_first_term( $id, 'property_area' );
		$city_t    = blockke_dev_first_term( $id, 'property_city' );
		$area      = $area_t ? blockke_dev_text( $area_t->name ) : '';
		// "Santorini Residences Westlands" -> "Santorini Residences": the area is already shown above the name.
		if ( $project && $area && 0 === stripos( $name, $short ) && 0 === strcasecmp( trim( substr( $name, strlen( $short ) ) ), $area ) ) {
			$name = $short;
		}
		$area_line = implode( ', ', array_unique( array_filter( array( $area, $city_t ? blockke_dev_text( $city_t->name ) : '' ) ) ) );
		$address   = blockke_dev_text( get_post_meta( $id, 'property_address', true ) );
		if ( '' === $address ) {
			$address = preg_replace( '/(,\s*(Nairobi|Kenya))+$/i', '', blockke_dev_kv_get( $kv, '/^location|^address/i' ) );
		}
		$offplan = blockke_dev_is_offplan( $id );
		$status  = $offplan ? 'Off-plan' : ( has_term( 'complete', 'property_status', $id ) || has_term( 'ready-now', 'property_completion', $id ) ? 'Ready to move in' : '' );
		$action  = blockke_dev_first_term( $id, 'property_action_category' );
		$chip    = implode( ' · ', array_filter( array( $status, $action ? ucfirst( strtolower( blockke_dev_text( $action->name ) ) ) : '' ) ) );

		// Units and price
		$subs  = blockke_dev_subunits( $id );
		$units = blockke_dev_units_from_table( $parsed['tokens'], $subs );
		if ( ! $units ) {
			$units = blockke_dev_units_from_subs( $subs );
		}
		$prices = array();
		foreach ( $units as $u ) {
			if ( $u['price'] > 0 ) {
				$prices[] = $u['price'];
			}
		}
		$meta_price = (float) get_post_meta( $id, 'property_price', true );
		$price_from = $prices ? min( $prices ) : $meta_price;
		$completion = blockke_dev_completion( $post );
		$plan       = blockke_dev_plan_info( $post );

		// Images
		$gal = get_post_meta( $id, 'wpestate_property_gallery', true );
		if ( ! is_array( $gal ) ) {
			$gal = explode( ',', (string) get_post_meta( $id, 'image_to_attach', true ) );
		}
		$ids = array_values( array_unique( array_filter( array_map( 'intval', array_merge( array( (int) get_post_thumbnail_id( $id ) ), $gal ) ) ) ) );
		if ( $ids ) {
			$att = get_posts( array( 'post_type' => 'attachment', 'post_status' => 'inherit', 'post__in' => $ids, 'orderby' => 'post__in', 'posts_per_page' => 120 ) );
			$ids = array();
			foreach ( $att as $a ) {
				if ( 0 === strpos( (string) $a->post_mime_type, 'image/' ) ) {
					$ids[] = $a->ID;
				}
			}
		}
		$captions = array(); // Media Library captions, e.g. "Indoor heated pool"
		foreach ( $ids as $aid ) {
			$cap = trim( blockke_dev_text( (string) wp_get_attachment_caption( $aid ) ) );
			if ( '' !== $cap ) {
				$captions[ $aid ] = mb_substr( $cap, 0, 80 );
			}
		}

		// Text
		$intro_p = array();
		$rest    = '';
		foreach ( $parsed['intro'] as $t ) {
			$tx = 'p' === $t['tag'] ? blockke_dev_text( $t['inner'] ) : '';
			if ( 'p' === $t['tag'] && mb_strlen( $tx ) > 30 && count( $intro_p ) < 3 ) {
				$intro_p[] = $tx;
			} else {
				$rest .= $t['html'];
			}
		}
		$tagline = blockke_dev_text( get_post_meta( $id, 'bke_dev_tagline', true ) );
		if ( '' === $tagline && '' !== trim( $post->post_excerpt ) ) {
			$tagline = blockke_dev_sentences( blockke_dev_text( $post->post_excerpt ), 1 );
		}
		if ( '' === $tagline ) {
			$rm = blockke_dev_text( get_post_meta( $id, 'rank_math_description', true ) );
			$s  = array();
			foreach ( preg_split( '/(?<=[.!?])\s+/u', $rm ) as $x ) {
				if ( '' !== $x && ! preg_match( '/enquire|contact|call us|block real estate|whatsapp/i', $x ) ) {
					$s[] = $x;
				}
			}
			$tagline = implode( ' ', array_slice( $s, 0, 2 ) );
		}
		if ( '' === $tagline && $intro_p ) {
			$tagline = blockke_dev_sentences( $intro_p[0], 1 );
		}
		// Drop a leading "{Name} on Lantana Road, Westlands —" or "{Name} is": the name sits right above.
		foreach ( array_unique( array( $name, $short ) ) as $nm ) {
			if ( '' !== $nm && 0 === stripos( $tagline, $nm ) ) {
				$rest = substr( $tagline, strlen( $nm ) );
				if ( preg_match( '/^[^—–:.]{0,70}?\s[—–]\s|^[^:.]{0,70}?:\s/u', $rest, $m ) || preg_match( '/^,?\s*(?:is|are|offers|brings|features|has)\s+/i', $rest, $m ) ) {
					$tagline = ucfirst( ltrim( substr( $rest, strlen( $m[0] ) ), ' ,' ) );
				}
				break;
			}
		}
		$tagline = blockke_dev_trim_words( $tagline, 240 );

		$hi         = blockke_dev_find( $secs, '/^why\b|highlights|key features|why (buy|choose|invest)/i', '/investor/i' );
		$highlights = array();
		if ( $hi >= 0 ) {
			foreach ( $secs[ $hi ]['tokens'] as $t ) {
				if ( 'ul' !== $t['tag'] ) {
					continue;
				}
				foreach ( blockke_dev_list_items( $t['inner'] ) as $it ) {
					$h = '' !== $it['label'] ? rtrim( $it['label'], ' .:' ) : $it['text'];
					if ( mb_strlen( $h ) <= 110 ) {
						$highlights[] = $h;
					}
				}
				break;
			}
		}

		// Location
		$li  = blockke_dev_find( $secs, '/location|neighbou?rhood|the area|getting around|connectivity/i' );
		$loc = array( 'intro' => '', 'places' => array(), 'note' => '' );
		if ( $li >= 0 ) {
			foreach ( $secs[ $li ]['tokens'] as $t ) {
				if ( 'p' === $t['tag'] ) {
					$tx = blockke_dev_text( $t['inner'] );
					if ( '' === $loc['intro'] && ! $loc['places'] && mb_strlen( $tx ) > 40 ) {
						$loc['intro'] = blockke_dev_trim_words( blockke_dev_sentences( $tx, 2 ), 520 );
					} elseif ( $loc['places'] && preg_match( '/approximate|vary|traffic/i', $tx ) ) {
						$loc['note'] = $tx;
					}
				} elseif ( 'ul' === $t['tag'] && ! $loc['places'] ) {
					foreach ( blockke_dev_list_items( $t['inner'] ) as $it ) {
						$p               = blockke_dev_split_kv( $it );
						$loc['places'][] = '' !== $p[0] ? array( $p[0], $p[1] ) : array( $it['text'], '' );
					}
				}
			}
		}
		$own = blockke_dev_places_override( $id );
		if ( $own ) {
			$loc['places'] = $own;
		}
		$lat = (float) get_post_meta( $id, 'property_latitude', true );
		$lng = (float) get_post_meta( $id, 'property_longitude', true );
		if ( ! $loc['places'] ) {
			foreach ( blockke_dev_nearby( $lat, $lng ) as $g ) {
				$loc['places'][] = array( $g['places'][0][0], $g['places'][0][2] . ' · ' . blockke_dev_km_label( $g['places'][0][1] ) );
			}
			if ( $loc['places'] ) {
				$loc['note'] = 'Straight-line distances from the map pin. Road distances and travel times will be longer.';
			}
		}
		if ( ! $loc['note'] ) {
			$loc['note'] = $loc['places'] ? 'Approximate travel times; they vary with traffic.' : 'Ask us for travel times to your office, school or embassy.';
		}

		// FAQ, description, everything else
		$fi   = blockke_dev_find( $secs, '/faq|frequently asked|questions/i' );
		$ci   = blockke_dev_find( $secs, '/enquir|call to action|contact|get in touch|book a viewing|next step/i' );
		$desc = $rest;
		foreach ( $secs as $i => $s ) {
			if ( $i !== $fi && $i !== $ci ) {
				$desc .= $s['html'];
			}
		}
		$desc = preg_replace( array( '#<(/?)h5\b#i', '#<(/?)h4\b#i', '#<(/?)h3\b#i', '#<(/?)h2\b#i' ), array( '<$1h6', '<$1h5', '<$1h4', '<$1h3' ), $desc );

		$crumbs = array();
		foreach ( array( $action, $area_t ) as $t ) {
			if ( $t ) {
				$link = get_term_link( $t );
				if ( ! is_wp_error( $link ) ) {
					$crumbs[] = array( blockke_dev_text( $t->name ), $link );
				}
			}
		}

		$agent     = blockke_dev_agent( (int) get_post_meta( $id, 'property_agent', true ) );
		$amenities = blockke_dev_amenities( $id, $secs );
		$data      = array(
			'id'         => $id,
			'slug'       => $post->post_name,
			'title'      => $title,
			'name'       => $name,
			'short'      => $short,
			'url'        => get_permalink( $id ),
			'chip'       => $chip,
			'area'       => $area,
			'area_line'  => $area_line,
			'address'    => $address ? $address : $area_line,
			'offplan'    => $offplan,
			'tagline'    => $tagline,
			'price_from' => $price_from,
			'completion' => $completion,
			'units'      => $units,
			'subs'       => $subs,
			'images'     => $ids,
			'captions'   => $captions,
			'video'      => blockke_dev_video( $id ),
			'facts'      => blockke_dev_facts( $id, $kv, $units, $completion ),
			'overview'   => array(
				'heading'    => blockke_dev_text( get_post_meta( $id, 'bke_dev_overview_heading', true ) ),
				'paras'      => $intro_p,
				'highlights' => array_slice( $highlights, 0, 6 ),
			),
			'desc'       => trim( $desc ),
			'amenities'  => $amenities,
			'amen'       => blockke_dev_amenity_layout( $amenities ),
			'amen_intro' => $amenities ? blockke_dev_amenity_intro( $secs ) : '',
			'plan'       => $plan,
			'suits'      => blockke_dev_suits( $secs ),
			'investors'  => blockke_dev_investors( $secs ),
			'lat'        => $lat,
			'lng'        => $lng,
			'location'   => $loc,
			'faqs'       => $fi >= 0 ? blockke_dev_faqs( $secs[ $fi ]['tokens'] ) : array(),
			'similar'    => blockke_dev_similar( $id, $area_t, array_merge( array( $id ), wp_list_pluck( $subs, 'id' ) ), (int) $cfg['similar_count'] ),
			'agent'      => $agent,
			'brochure'   => '' !== blockke_dev_brochure( $id ),
			'crumbs'     => $crumbs,
		);
		$cache[ $id ] = $data;
		return $data;
	}

	/* =====================================================================
	   2) Development layout: markup
	   ===================================================================== */

	function blockke_dev_icon( $n, $cls = '' ) {
		return '<svg class="bkd-i' . ( 'wa' === $n ? ' bkd-i-f' : '' ) . ( $cls ? ' ' . $cls : '' ) . '" aria-hidden="true" focusable="false"><use href="#bkd-i-' . $n . '"></use></svg>';
	}

	/** The icon sprite; $only limits it to the icons a page uses. */
	function blockke_dev_sprite( $only = array() ) {
		$i = array(
			'arrow'    => '<path d="M5 12h14M13 6l6 6-6 6"/>',
			'check'    => '<path d="M20 6 9 17l-5-5"/>',
			'close'    => '<path d="M6 6l12 12M18 6 6 18"/>',
			'phone'    => '<path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.8 19.8 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.12 4.18 2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.13.96.36 1.9.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.91.34 1.85.57 2.81.7A2 2 0 0 1 22 16.92z"/>',
			'mail'     => '<rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-10 6L2 7"/>',
			'pin'      => '<path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/>',
			'grid'     => '<rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/>',
			'share'    => '<circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><path d="m8.6 13.5 6.8 4M15.4 6.5l-6.8 4"/>',
			'waves'    => '<path d="M2 7c.6.5 1.2 1 2.5 1 2.5 0 2.5-2 5-2s2.4 2 5 2c2.5 0 2.5-2 5-2 1.3 0 1.9.5 2.5 1M2 13c.6.5 1.2 1 2.5 1 2.5 0 2.5-2 5-2s2.4 2 5 2c2.5 0 2.5-2 5-2 1.3 0 1.9.5 2.5 1M2 19c.6.5 1.2 1 2.5 1 2.5 0 2.5-2 5-2s2.4 2 5 2c2.5 0 2.5-2 5-2 1.3 0 1.9.5 2.5 1"/>',
			'users'    => '<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>',
			'shield'   => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10Z"/><path d="m9 12 2 2 4-4"/>',
			'leaf'     => '<path d="M11 20A7 7 0 0 1 9.8 6.1C15.5 5 17 4.48 19 2c1 2 2 4.18 2 8 0 5.5-4.78 10-10 10Z"/><path d="M2 21c0-3 1.85-5.36 5.08-6C9.5 14.52 12 13 13 12"/>',
			'film'     => '<rect x="2" y="4" width="20" height="16" rx="2"/><path d="M7 4v16M17 4v16M2 9h5M2 15h5M17 9h5M17 15h5"/>',
			'laptop'   => '<rect x="4" y="5" width="16" height="11" rx="1.5"/><path d="M2 19h20"/>',
			'car'      => '<path d="M19 17h2c.6 0 1-.4 1-1v-3c0-.9-.7-1.7-1.5-1.9C18.7 10.6 16 10 16 10s-1.3-1.4-2.2-2.3c-.5-.4-1.1-.7-1.8-.7H5c-.6 0-1.1.4-1.4.9l-1.4 2.9A3.7 3.7 0 0 0 2 12v4c0 .6.4 1 1 1h2"/><circle cx="7" cy="17" r="2"/><path d="M9 17h6"/><circle cx="17" cy="17" r="2"/>',
			'building' => '<rect x="4" y="2" width="16" height="20" rx="1"/><path d="M9 22v-4h6v4M8 6h.01M12 6h.01M16 6h.01M8 10h.01M12 10h.01M16 10h.01M8 14h.01M12 14h.01M16 14h.01"/>',
			'sofa'     => '<path d="M20 9V7a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v2"/><path d="M2 11a2 2 0 0 1 4 0v2h12v-2a2 2 0 0 1 4 0v6a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1Z"/><path d="M5 18v2M19 18v2"/>',
			'star'     => '<path d="m12 2 3.1 6.3 6.9 1-5 4.9 1.2 6.8L12 17.8 5.8 21l1.2-6.8-5-4.9 6.9-1z"/>',
			'play'     => '<path d="M8 5v14l11-7z"/>',
			'glass'    => '<path d="M8 22h8M12 15v7M7 3h10l-.4 5.6A4.6 4.6 0 0 1 12 13a4.6 4.6 0 0 1-4.6-4.4Z"/><path d="M7.3 7h9.4"/>',
			'lotus'    => '<path d="M12 21c-2.6-2-4-5-3-9.6 1.3.7 2.3 1.6 3 2.9.7-1.3 1.7-2.2 3-2.9 1 4.6-.4 7.6-3 9.6Z"/><path d="M12 21c-4.4 0-8.2-2.2-9.6-6.4 2.6-.5 5.2.3 7 2.1M12 21c4.4 0 8.2-2.2 9.6-6.4-2.6-.5-5.2.3-7 2.1"/>',
			'dumbbell' => '<path d="M6.5 7v10M3.5 9.5v5M17.5 7v10M20.5 9.5v5M6.5 12h11"/>',
			'kite'     => '<path d="M12 2 5.5 9.5 12 17l6.5-7.5Z"/><path d="M5.5 9.5h13M12 2v15M12 17c0 2-1.2 3.2-3 3.4M9 20.4c-1.4.1-2.4.7-2.8 1.6"/>',
			'cup'      => '<path d="M17 9h1.5a3 3 0 0 1 0 6H17"/><path d="M3 9h14v6a5 5 0 0 1-5 5H8a5 5 0 0 1-5-5Z"/><path d="M7 2.5v3M10 2.5v3M13 2.5v3"/>',
			'bag'      => '<path d="M5 8h14l-1 13H6Z"/><path d="M9 8V6.5a3 3 0 0 1 6 0V8"/>',
			'flame'    => '<path d="M12 22a7 7 0 0 0 7-7c0-3-1.6-5.2-3.4-7-.2 1.8-1 3-2.1 3.5C13.8 8 12.5 4.6 9.5 2c.3 3.4-1.5 5.5-3 7.4A8.4 8.4 0 0 0 5 15a7 7 0 0 0 7 7Z"/>',
			'ball'     => '<circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3v18M5.6 5.6c3.6 3.6 3.6 9.2 0 12.8M18.4 5.6c-3.6 3.6-3.6 9.2 0 12.8"/>',
			'flag'     => '<path d="M6 22V3"/><path d="M6 3.5 18 8 6 12.5"/><path d="M3 22h7"/>',
			'route'    => '<circle cx="6" cy="19" r="2.5"/><circle cx="18" cy="5" r="2.5"/><path d="M8.5 19H16a3.5 3.5 0 0 0 0-7H8a3.5 3.5 0 0 1 0-7h7.5"/>',
			'medical'  => '<path d="M9 3h6v6h6v6h-6v6H9v-6H3V9h6Z"/>',
			'cap'      => '<path d="M2 9l10-5 10 5-10 5Z"/><path d="M6 11v5c3 2 9 2 12 0v-5"/><path d="M22 9v5"/>',
			'chart'    => '<path d="M22 7 13.5 15.5l-5-5L2 17"/><path d="M16 7h6v6"/>',
			'plane'    => '<path d="M17.8 19.2 16 11l3.5-3.5C21 6 21.5 4 21 3c-1-.5-3 0-4.5 1.5L13 8 4.8 6.2c-.5-.1-.9.1-1.1.5l-.3.5c-.2.5-.1 1 .3 1.3L9 12l-2 3H4l-1 1 3 2 2 3 1-1v-3l3-2 3.5 5.3c.3.4.8.5 1.3.3l.5-.2c.4-.3.6-.7.5-1.2Z"/>',
		);
		if ( $only ) {
			$i = array_intersect_key( $i, array_flip( $only ) );
		}
		$wa = '<path d="M17.47 14.38c-.3-.15-1.76-.87-2.03-.97-.27-.1-.47-.15-.67.15-.2.3-.77.97-.94 1.16-.17.2-.35.22-.64.07-.3-.15-1.26-.46-2.39-1.47-.88-.79-1.48-1.76-1.65-2.06-.17-.3-.02-.46.13-.6.13-.14.3-.35.45-.52.15-.18.2-.3.3-.5.1-.2.05-.37-.03-.52-.07-.15-.67-1.61-.91-2.2-.24-.58-.49-.5-.67-.51h-.57c-.2 0-.52.07-.79.37-.27.3-1.04 1.02-1.04 2.48s1.07 2.88 1.21 3.07c.15.2 2.1 3.2 5.08 4.49.71.3 1.26.49 1.7.63.71.22 1.36.19 1.87.12.57-.09 1.76-.72 2-1.41.25-.7.25-1.29.18-1.41-.08-.13-.27-.2-.57-.35m-5.42 7.4h-.01a9.87 9.87 0 0 1-5.03-1.38l-.36-.21-3.74.98 1-3.65-.24-.37a9.86 9.86 0 0 1-1.51-5.26c0-5.45 4.44-9.88 9.89-9.88a9.82 9.82 0 0 1 9.88 9.89c0 5.45-4.44 9.88-9.88 9.88m8.41-18.3A11.82 11.82 0 0 0 12.05 0C5.5 0 .16 5.34.16 11.89c0 2.1.55 4.14 1.59 5.95L.06 24l6.3-1.65a11.88 11.88 0 0 0 5.68 1.45h.01c6.55 0 11.89-5.34 11.89-11.89a11.82 11.82 0 0 0-3.48-8.41Z"/>';
		$out = '<svg class="bkd-sprite" width="0" height="0" style="position:absolute;width:0;height:0;overflow:hidden" aria-hidden="true" focusable="false">';
		foreach ( $i as $k => $p ) {
			$out .= '<symbol id="bkd-i-' . $k . '" viewBox="0 0 24 24">' . $p . '</symbol>';
		}
		return $out . ( ! $only || in_array( 'wa', $only, true ) ? '<symbol id="bkd-i-wa" viewBox="0 0 24 24">' . $wa . '</symbol>' : '' ) . '</svg>';
	}

	function blockke_dev_img( $aid, $size, $attr ) {
		$html = $aid ? wp_get_attachment_image( $aid, $size, false, $attr ) : '';
		return $html ? $html : '<div class="bkd-ph"><b>' . esc_html( isset( $attr['data-ph'] ) ? $attr['data-ph'] : '' ) . '</b><span>Images on request</span></div>';
	}

	function blockke_dev_alt( $aid, $fallback ) {
		$alt = trim( (string) get_post_meta( $aid, '_wp_attachment_image_alt', true ) );
		return '' !== $alt ? $alt : $fallback;
	}

	function blockke_dev_field( $name, $label, $type, $ac, $ph = '', $optional = false ) {
		return '<label class="bkd-f" data-f="' . $name . '"><span>' . $label . ( $optional ? ' <small>(optional)</small>' : '' ) . '</span>'
			. '<input name="' . $name . '" type="' . $type . '" autocomplete="' . $ac . '"' . ( $ph ? ' placeholder="' . esc_attr( $ph ) . '"' : '' ) . ( 'tel' === $type ? ' inputmode="tel"' : '' ) . '>'
			. '<em class="bkd-err" aria-live="polite"></em></label>';
	}

	function blockke_dev_chips( $name, $legend, $options, $checked ) {
		$h = '<fieldset class="bkd-f"><legend>' . esc_html( $legend ) . '</legend><div class="bkd-chips">';
		foreach ( $options as $o ) {
			$h .= '<label class="bkd-chip"><input type="radio" name="' . $name . '" value="' . esc_attr( $o ) . '"' . checked( $o, $checked, false ) . '><span>' . esc_html( $o ) . '</span></label>';
		}
		return $h . '</div></fieldset>';
	}

	function blockke_dev_timelines() {
		return array( 'Within 3 months', '3–12 months', 'Just exploring' );
	}

	function blockke_dev_timeline( $s ) {
		return in_array( $s, blockke_dev_timelines(), true ) ? $s : '';
	}

	function blockke_dev_unit_groups( $units ) {
		$groups = array();
		foreach ( $units as $u ) {
			if ( ! in_array( $u['group'], $groups, true ) ) {
				$groups[] = $u['group'];
			}
		}
		return $groups;
	}

	/**
	 * Optional questions shown once a quick enquiry is in; the answers are added to the same lead.
	 * Not a <form>, so tools that count form submissions (GA4, Meta) don't count the lead twice.
	 */
	function blockke_dev_more( $d, $key ) {
		$groups = blockke_dev_unit_groups( $d['units'] );
		return '<div class="bkd-more" data-more role="group" aria-label="Optional questions" hidden><p class="bkd-more-t">Help us send the right details <small>(optional)</small></p>'
			. ( count( $groups ) > 1 ? '<div data-more-unit>' . blockke_dev_chips( 'unit-' . $key, 'Which home?', $groups, '' ) . '</div>' : '' )
			. blockke_dev_chips( 'timeline-' . $key, 'When would you like to buy?', blockke_dev_timelines(), '' )
			. blockke_dev_chips( 'purpose-' . $key, 'Buying to', array( 'Live in', 'Invest', 'Both' ), '' )
			. '<button class="bkd-btn bkd-btn-line bkd-btn-sm" type="button" data-more-send>Send these answers</button>'
			. '<p class="bkd-more-done" role="status" hidden></p></div>';
	}

	function blockke_dev_ok( $more = '' ) {
		return '<div class="bkd-ok" hidden tabindex="-1"><div class="bkd-ok-tick">' . blockke_dev_icon( 'check' ) . '</div><h3>Thank you<span data-first></span>.</h3>'
			. '<div class="bkd-ok-msg" data-okmsg></div>' . $more . '<div class="bkd-ok-actions"><a class="bkd-btn bkd-btn-navy" data-asset hidden target="_blank" rel="noopener"></a>'
			. '<a class="bkd-btn bkd-btn-line" data-wa-ok href="#" target="_blank" rel="noopener">' . blockke_dev_icon( 'wa', 'bkd-wa-ic' ) . 'Continue on WhatsApp</a></div></div>';
	}

	function blockke_dev_privacy_url() {
		$privacy = function_exists( 'get_privacy_policy_url' ) ? get_privacy_policy_url() : '';
		if ( function_exists( 'blockke_bp_config' ) ) {
			$bp = blockke_bp_config();
			if ( ! empty( $bp['privacy_url'] ) ) {
				$privacy = $bp['privacy_url'];
			}
		}
		return $privacy;
	}

	function blockke_dev_consent( $d ) {
		$privacy = blockke_dev_privacy_url();
		return '<div class="bkd-consent">We\'ll reply by WhatsApp or phone. By sending this you agree to be contacted about ' . esc_html( $d['short'] ) . '.'
			. ( $privacy ? ' <a href="' . esc_url( $privacy ) . '" target="_blank" rel="noopener">Privacy policy</a>' : '' ) . '</div>';
	}

	/* =====================================================================
	   2) Development layout: ad landing pages (no site menu or footer)
	   ===================================================================== */

	/**
	 * The listing an ad landing page shows, or 0: a page with a bke_lp_listing field (listing ID),
	 * or a for-sale listing's own URL with ?lp=1.
	 */
	function blockke_dev_lp_target() {
		static $id = null;
		if ( null !== $id ) {
			return $id;
		}
		$id  = 0;
		$cfg = blockke_dev_config();
		if ( empty( $cfg['landing_pages'] ) || is_admin() || is_feed() || is_embed() ) {
			return $id;
		}
		foreach ( array( 'elementor-preview', 'vcv-editable', 'vcv-source-id', 'print' ) as $k ) {
			if ( isset( $_GET[ $k ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
				return $id;
			}
		}
		$lid = 0;
		if ( is_page() ) {
			$lid = (int) get_post_meta( get_queried_object_id(), 'bke_lp_listing', true );
		} elseif ( is_singular( 'estate_property' ) && isset( $_GET['lp'] ) && '1' === (string) $_GET['lp'] ) { // phpcs:ignore WordPress.Security.NonceVerification
			$lid = get_queried_object_id();
		}
		if ( $lid && 'estate_property' === get_post_type( $lid ) && 'publish' === get_post_status( $lid ) && blockke_dev_is_for_sale( $lid ) ) {
			$id = $lid;
		}
		return $id;
	}

	function blockke_dev_logo() {
		$cfg = blockke_dev_config();
		if ( '' !== (string) $cfg['logo'] ) {
			return (string) $cfg['logo'];
		}
		$l = function_exists( 'wpresidence_get_option' ) ? wpresidence_get_option( 'wp_estate_logo_image', '' ) : '';
		if ( is_array( $l ) && ! empty( $l['url'] ) ) {
			return (string) $l['url'];
		}
		return is_string( $l ) && '' !== $l ? $l : content_url( '/uploads/2026/09/block-advisory-horizontal.png' );
	}

	/** Landing page header: logo, phone, WhatsApp and the price list button. No menu, so ad visitors stay on the page. */
	function blockke_dev_lp_header( $d, $wa ) {
		$cfg = blockke_dev_config();
		$ag  = $d['agent'];
		return '<header class="bkd-lph"><div class="bkd-wrap bkd-lph-in">'
			. '<img class="bkd-lph-logo" src="' . esc_url( blockke_dev_logo() ) . '" alt="' . esc_attr( $cfg['brand_full'] ) . '" width="193" height="28">'
			. '<div class="bkd-lph-r"><a class="bkd-lph-a" href="tel:' . esc_attr( $ag['phone'] ) . '" aria-label="Call ' . esc_attr( $ag['phone_display'] ) . '">' . blockke_dev_icon( 'phone' ) . '<span>' . esc_html( $ag['phone_display'] ) . '</span></a>'
			. '<a class="bkd-lph-a" href="' . esc_url( $wa ) . '" target="_blank" rel="noopener" data-bkd-wa aria-label="WhatsApp">' . blockke_dev_icon( 'wa', 'bkd-wa-ic' ) . '<span>WhatsApp</span></a>'
			. '<button class="bkd-btn bkd-btn-navy bkd-btn-sm bkd-lph-cta" type="button" data-bkd-enquire="lp-header">Get the price list</button></div></div></header>';
	}

	function blockke_dev_lp_footer( $d ) {
		$cfg     = blockke_dev_config();
		$ag      = $d['agent'];
		$privacy = blockke_dev_privacy_url();
		return '<footer class="bkd-lpf"><div class="bkd-wrap"><p><b>' . esc_html( $cfg['brand_full'] ) . '</b>' . ( $cfg['address'] ? '<span>' . esc_html( $cfg['address'] ) . '</span>' : '' ) . '</p>'
			. '<p><a href="tel:' . esc_attr( $ag['phone'] ) . '">' . esc_html( $ag['phone_display'] ) . '</a><a href="mailto:' . esc_attr( $cfg['email'] ) . '">' . esc_html( $cfg['email'] ) . '</a>'
			. ( $privacy ? '<a href="' . esc_url( $privacy ) . '" target="_blank" rel="noopener">Privacy policy</a>' : '' ) . '<span>© ' . esc_html( wp_date( 'Y' ) ) . '</span></p></div></footer>';
	}

	/** A whole landing page document: the site's head and footer hooks (tracking, chat) without the theme's header, menu or footer. */
	function blockke_dev_lp_document( $d ) {
		echo "<!DOCTYPE html>\n<html " . get_language_attributes() . '><head><meta charset="' . esc_attr( get_bloginfo( 'charset' ) ) . '"><meta name="viewport" content="width=device-width, initial-scale=1">' . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput
		wp_head();
		echo '</head><body class="' . esc_attr( implode( ' ', get_body_class() ) ) . '">' . "\n";
		if ( function_exists( 'wp_body_open' ) ) {
			wp_body_open();
		}
		echo blockke_dev_render( $d, true ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped while building.
		wp_footer();
		echo "\n</body></html>\n";
	}

	function blockke_dev_quick_form( $source, $cta, $d ) {
		return '<form class="bkd-form" data-bkd-lead="' . esc_attr( $source ) . '" novalidate><input class="bkd-hp" type="text" name="bkd_hp" tabindex="-1" autocomplete="off" aria-hidden="true">'
			. '<div class="bkd-qgrid">' . blockke_dev_field( 'name', 'Full name', 'text', 'name' ) . blockke_dev_field( 'phone', 'Phone / WhatsApp', 'tel', 'tel', '07XX XXX XXX' ) . '</div>'
			. '<button class="bkd-btn bkd-btn-navy bkd-btn-block" type="submit">' . esc_html( $cta ) . blockke_dev_icon( 'arrow' ) . '</button>' . blockke_dev_consent( $d ) . '</form>' . blockke_dev_ok( blockke_dev_more( $d, $source ) );
	}

	function blockke_dev_full_form( $d ) {
		$groups = blockke_dev_unit_groups( $d['units'] );
		$h      = '<form class="bkd-form" data-bkd-lead="contact" novalidate><input class="bkd-hp" type="text" name="bkd_hp" tabindex="-1" autocomplete="off" aria-hidden="true">';
		$h .= '<div class="bkd-two">' . blockke_dev_field( 'name', 'Full name', 'text', 'name' ) . blockke_dev_field( 'phone', 'Phone / WhatsApp', 'tel', 'tel', '07XX XXX XXX' ) . '</div>';
		$h .= blockke_dev_field( 'email', 'Email', 'email', 'email', '', true );
		if ( count( $groups ) > 1 ) {
			$h .= blockke_dev_chips( 'unit', 'Interested in', array_merge( $groups, array( 'Not sure yet' ) ), 'Not sure yet' );
		}
		$h .= blockke_dev_chips( 'purpose', 'Buying to', array( 'Live in', 'Invest', 'Both' ), 'Live in' );
		$h .= blockke_dev_chips( 'timeline', 'When would you like to buy?', blockke_dev_timelines(), '' );
		$h .= blockke_dev_chips( 'contact', 'Best way to reach you', array( 'WhatsApp', 'Phone call', 'Email' ), 'WhatsApp' );
		$h .= '<label class="bkd-f"><span>Message <small>(optional)</small></span><textarea name="message" placeholder="Anything we should know: budget, timing, questions."></textarea></label>';
		$h .= '<button class="bkd-btn bkd-btn-navy bkd-btn-block" type="submit">Send my enquiry' . blockke_dev_icon( 'arrow' ) . '</button>' . blockke_dev_consent( $d ) . '</form>' . blockke_dev_ok();
		return $h;
	}

	/** The development layout; $lp: as an ad landing page (own header and footer, no ways off the page but contact). */
	function blockke_dev_render( $d, $lp = false ) {
		$cfg   = blockke_dev_config();
		$ag    = $d['agent'];
		$e     = 'esc_html';
		$imgs  = $d['images'];
		$hero  = $imgs ? $imgs[0] : 0;
		$n_img = count( $imgs );
		$plan  = $d['plan'];
		$wa    = 'https://wa.me/' . $ag['wa'] . '?text=' . rawurlencode( 'Hi ' . $cfg['brand'] . ", I'm interested in " . $d['short'] . ( $d['price_from'] ? ' (from ' . blockke_dev_short( $d['price_from'] ) . ')' : '' ) . '. Please share the price list and floor plans. ' . $d['url'] );
		$o     = array();

		$o[] = '<div id="bkd" class="bkd' . ( $lp ? ' bkd-lpm' : '' ) . '" data-listing="' . (int) $d['id'] . '">' . blockke_dev_sprite();
		if ( $lp ) {
			$o[] = blockke_dev_lp_header( $d, $wa );
		}

		// Hero
		$crumbs = '<a href="' . esc_url( home_url( '/' ) ) . '">Home</a>';
		foreach ( $d['crumbs'] as $c ) {
			$crumbs .= '<span aria-hidden="true">/</span><a href="' . esc_url( $c[1] ) . '">' . $e( $c[0] ) . '</a>';
		}
		$hfacts   = array();
		$hfacts[] = $d['price_from'] ? array( 'From', blockke_dev_short( $d['price_from'] ) ) : array( 'Price', 'On request' );
		if ( $d['completion'] ) {
			$hfacts[] = array( 'Completion', $d['completion'] );
		} elseif ( ! $d['offplan'] ) {
			$hfacts[] = array( 'Status', 'Ready' );
		}
		if ( $d['offplan'] && $plan['deposit'] > 0 ) {
			$hfacts[] = array( 'Deposit', blockke_dev_num( $plan['deposit'] ) . '%' );
		} elseif ( $d['area'] ) {
			$hfacts[] = array( 'Location', $d['area'] );
		}
		$hf_html = '';
		foreach ( $hfacts as $f ) {
			$hf_html .= '<div><dt>' . $e( $f[0] ) . '</dt><dd>' . $e( $f[1] ) . '</dd></div>';
		}
		// Landing pages on phones lay the offer and the key figures over the photo, like a cover. The area
		// label stays above the name, so the offer names the street without repeating the area.
		$street = $d['address'] === $d['area_line'] ? '' : preg_replace( '/,\s*' . preg_quote( $d['area'], '/' ) . '$/iu', '', $d['address'] );
		$offer  = $lp ? implode( ' · ', array_map( function ( $s ) { return '<span>' . esc_html( $s ) . '</span>'; }, array_filter( array( blockke_dev_homes_label( $d['units'] ), $street ) ) ) ) : '';
		$o[]    = '<section class="bkd-hero" id="bkd-top"><div class="bkd-wrap bkd-hero-grid">'
			. '<div class="bkd-hero-head">' . ( $lp ? '' : '<div class="bkd-hero-top"><nav class="bkd-crumbs" aria-label="Breadcrumb">' . $crumbs . '</nav>'
			. '<button class="bkd-share" type="button" data-bkd-share aria-label="Share this property">' . blockke_dev_icon( 'share' ) . '<span>Share</span></button></div>' )
			. '<p class="bkd-status">' . ( $d['chip'] ? '<b>' . $e( $d['chip'] ) . '</b>' : '' ) . '<span>' . $e( $d['area_line'] ) . '</span></p>'
			. '<h1 class="bkd-h1">' . $e( $d['name'] ) . '</h1>'
			. ( $d['tagline'] ? '<p class="bkd-hero-tag">' . $e( $d['tagline'] ) . '</p>' : '' )
			. ( $offer ? '<p class="bkd-hero-offer">' . $offer . '</p>' : '' )
			. ( $lp ? '<dl class="bkd-hfacts bkd-hfacts-cover">' . $hf_html . '</dl>' : '' ) . '</div>'
			. '<figure class="bkd-hero-media">'
			. blockke_dev_img(
				$hero,
				'full',
				array(
					'class'         => 'bkd-hero-img',
					'sizes'         => '(min-width: 1000px) 56vw, 100vw',
					'loading'       => 'eager',
					'fetchpriority' => 'high',
					'decoding'      => 'async',
					'alt'           => blockke_dev_alt( $hero, $d['short'] . ', ' . $d['area_line'] ),
					'data-ph'       => $d['short'],
				)
			)
			. ( $hero && $d['offplan'] ? '<figcaption>Artist\'s impression</figcaption>' : '' )
			. ( $n_img > 1 ? '<button class="bkd-btn bkd-btn-white bkd-btn-sm bkd-all" type="button" data-bkd-open="0">' . blockke_dev_icon( 'grid' ) . 'View ' . $n_img . ' photos</button>' : '' )
			. '</figure><div class="bkd-hero-body"><dl class="bkd-hfacts">' . $hf_html;
		$o[] = '</dl><div class="bkd-quick" id="bkd-quick"><div class="bkd-quick-t">Get the price list and floor plans</div>'
			. '<div class="bkd-quick-s">Sent straight to your WhatsApp. Two details, no obligation.</div>'
			. blockke_dev_quick_form( 'hero', 'Send me the price list', $d )
			. ( $d['brochure'] ? '<div class="bkd-quick-alt"><button class="bkd-link" type="button" data-bkd-enquire="brochure">Or download the brochure' . blockke_dev_icon( 'arrow' ) . '</button></div>' : '' )
			. ( $lp && $ag['name'] ? '<div class="bkd-qa">' . ( $ag['photo'] ? '<img src="' . esc_url( $ag['photo'] ) . '" alt="" width="40" height="40" loading="lazy">' : '<span class="bkd-qa-av">' . $e( mb_substr( $ag['name'], 0, 1 ) ) . '</span>' )
				. '<span><b>' . $e( $ag['name'] ) . '</b>' . $e( $ag['role'] ? $ag['role'] : 'Your advisor' ) . '</span></div>' : '' )
			. '</div></div></div></section>';

		// Section nav
		$nav = array();
		if ( $d['overview']['paras'] || $d['desc'] ) {
			$nav['overview'] = 'Overview';
		}
		if ( $d['units'] ) {
			$nav['residences'] = 'Residences';
		}
		if ( $d['amenities'] ) {
			$nav['amenities'] = 'Amenities';
		}
		$nav['payment'] = $d['offplan'] ? 'Payment plan' : 'Mortgage';
		if ( $d['lat'] || $d['location']['intro'] ) {
			$nav['location'] = 'Location';
		}
		if ( $d['faqs'] ) {
			$nav['faq'] = 'FAQ';
		}
		$o[] = '<nav class="bkd-subnav" id="bkd-subnav" aria-label="On this page"><div class="bkd-wrap bkd-subnav-in"><b class="bkd-subnav-name">' . $e( $d['short'] ) . '</b><div class="bkd-subnav-links">';
		foreach ( $nav as $k => $label ) {
			$o[] = '<a href="#bkd-' . $k . '">' . $e( $label ) . '</a>';
		}
		$o[] = '</div><div class="bkd-subnav-r"><a class="bkd-subnav-wa" href="' . esc_url( $wa ) . '" target="_blank" rel="noopener" data-bkd-wa>' . blockke_dev_icon( 'wa', 'bkd-wa-ic' ) . '<span>WhatsApp</span></a>'
			. '<button class="bkd-btn bkd-btn-navy bkd-btn-sm" type="button" data-bkd-enquire="subnav">Get the price list</button></div></div></nav>';

		// Facts band
		if ( $d['facts'] ) {
			$o[] = '<section class="bkd-facts" aria-label="Key facts"><div class="bkd-wrap"><dl style="--n:' . min( count( $d['facts'] ), 6 ) . '">';
			foreach ( $d['facts'] as $f ) {
				$o[] = '<div><dt>' . $e( $f[0] ) . '</dt><dd>' . $e( $f[1] ) . '</dd></div>';
			}
			$o[] = '</dl></div></section>';
		}

		// Overview + full description
		if ( isset( $nav['overview'] ) ) {
			$ov  = $d['overview'];
			$o[] = '<section class="bkd-sec" id="bkd-overview"><div class="bkd-wrap bkd-split"><div class="bkd-sticky bkd-rv"><p class="bkd-label">Overview</p><h2 class="bkd-h2">'
				. ( $ov['heading'] ? blockke_dev_accent( $ov['heading'] ) : 'About <em>' . $e( $d['short'] ) . '</em>' ) . '</h2></div><div class="bkd-flow bkd-rv">';
			foreach ( $ov['paras'] as $i => $p ) {
				$o[] = '<p class="' . ( 0 === $i ? 'bkd-lead' : 'bkd-body' ) . '">' . $e( $p ) . '</p>';
			}
			if ( $ov['highlights'] ) {
				$o[] = '<ul class="bkd-hl">';
				foreach ( $ov['highlights'] as $hl ) {
					$o[] = '<li>' . blockke_dev_icon( 'check' ) . '<span>' . $e( $hl ) . '</span></li>';
				}
				$o[] = '</ul>';
			}
			if ( $d['desc'] ) {
				$o[] = '<details class="bkd-more"><summary><span class="bkd-more-open">Read the full description</span><span class="bkd-more-close">Show less</span></summary><div class="bkd-prose">' . ( $lp ? preg_replace( '#</?a\b[^>]*>#i', '', $d['desc'] ) : $d['desc'] ) . '</div></details>';
			}
			$o[] = '</div></div></section>';
		}

		// Gallery
		if ( $n_img >= 3 ) {
			$o[] = '<section class="bkd-sec bkd-sec-tight" id="bkd-gallery"><div class="bkd-wrap"><div class="bkd-gal bkd-rv">';
			foreach ( array_slice( $imgs, 0, 5 ) as $i => $aid ) {
				$cap = isset( $d['captions'][ $aid ] ) ? $d['captions'][ $aid ] : '';
				$o[] = '<button type="button" data-bkd-open="' . $i . '" aria-label="Open photo ' . ( $i + 1 ) . ( $cap ? ': ' . esc_attr( $cap ) : '' ) . '">'
					. ( $cap ? '<span class="bkd-gal-cap">' . $e( $cap ) . '</span>' : '' )
					. blockke_dev_img( $aid, 'large', array( 'loading' => 'lazy', 'decoding' => 'async', 'sizes' => 0 === $i ? '(min-width: 900px) 66vw, 100vw' : '(min-width: 900px) 33vw, 50vw', 'alt' => blockke_dev_alt( $aid, $d['short'] . ' photo ' . ( $i + 1 ) ), 'data-ph' => $d['short'] ) )
					. '</button>';
			}
			$o[] = '</div><div class="bkd-sh-row bkd-gal-foot"><p class="bkd-fine">' . ( $d['offplan'] ? 'Images are artist\'s impressions.' : 'Photos of ' . $e( $d['short'] ) . '.' ) . '</p><button class="bkd-link" type="button" data-bkd-open="0">' . blockke_dev_icon( 'grid' ) . 'View all ' . $n_img . ' photos</button></div></div></section>';
		}

		// Video or virtual tour
		if ( $d['video'] ) {
			$v      = $d['video'];
			$poster = 'youtube' === $v['type'] ? 'https://i.ytimg.com/vi/' . rawurlencode( $v['id'] ) . '/hqdefault.jpg' : ( $hero ? wp_get_attachment_image_url( $hero, 'large' ) : '' );
			$label  = 'tour' === $v['type'] ? 'Take the virtual tour' : 'Play the video';
			$o[]    = '<section class="bkd-sec bkd-sec-tight" id="bkd-video"><div class="bkd-wrap"><div class="bkd-video bkd-rv" data-bkd-video="' . esc_attr( $v['type'] ) . '" data-id="' . esc_attr( $v['id'] ) . '">'
				. '<button type="button" aria-label="' . esc_attr( $label . ': ' . $d['short'] ) . '">' . ( $poster ? '<img loading="lazy" alt="" src="' . esc_url( $poster ) . '">' : '' )
				. '<span class="bkd-play">' . blockke_dev_icon( 'play' ) . '</span><span class="bkd-video-l">' . $e( $label ) . '</span></button></div></div></section>';
		}

		// Residences
		if ( $d['units'] ) {
			$groups   = array();
			$has_size = false;
			$has_pr   = false;
			foreach ( $d['units'] as $u ) {
				if ( ! in_array( $u['group'], $groups, true ) ) {
					$groups[] = $u['group'];
				}
				$has_size = $has_size || '' !== $u['size'];
				$has_pr   = $has_pr || $u['price'] > 0;
			}
			$filters = '';
			if ( count( $groups ) > 1 && count( $d['units'] ) > 4 ) {
				$filters = '<div class="bkd-filters" role="group" aria-label="Filter residences"><button type="button" aria-pressed="true" data-g="*">All</button>';
				foreach ( $groups as $g ) {
					$filters .= '<button type="button" aria-pressed="false" data-g="' . esc_attr( $g ) . '">' . $e( $g ) . '</button>';
				}
				$filters .= '</div>';
			}
			$o[] = '<section class="bkd-sec bkd-sec-mist" id="bkd-residences"><div class="bkd-wrap"><div class="bkd-sh bkd-sh-row bkd-rv"><div><p class="bkd-label">Residences</p><h2 class="bkd-h2">Availability <em>and pricing</em></h2></div>' . $filters . '</div>'
				. '<table class="bkd-tbl bkd-rv"><thead><tr><th>Residence</th>' . ( $has_size ? '<th>Size</th>' : '' ) . ( $has_pr ? '<th>Price from</th>' : '' ) . '<th><span class="bkd-sr">Actions</span></th></tr></thead><tbody>';
			foreach ( $d['units'] as $u ) {
				$label = $u['type'] . ( $u['size'] ? ' · ' . $u['size'] : '' );
				$o[]   = '<tr data-g="' . esc_attr( $u['group'] ) . '"><td class="bkd-t">' . $e( $u['type'] ) . ( $u['note'] ? '<small>' . $e( $u['note'] ) . '</small>' : '' ) . '</td>'
					. ( $has_size ? '<td class="bkd-s">' . $e( $u['size'] ? $u['size'] : '—' ) . '</td>' : '' )
					. ( $has_pr ? '<td class="bkd-p">' . $e( $u['price'] ? blockke_dev_money( $u['price'] ) : 'On request' ) . '</td>' : '' )
					. '<td class="bkd-a">' . ( $u['url'] && ! $lp ? '<a class="bkd-link" href="' . esc_url( $u['url'] ) . '">Details</a>' : '' )
					. '<button class="bkd-link" type="button" data-bkd-enquire="floorplan" data-unit="' . esc_attr( $label ) . '">Floor plan &amp; price' . blockke_dev_icon( 'arrow' ) . '</button></td></tr>';
			}
			$o[] = '</tbody></table><p class="bkd-fine bkd-tbl-note">Starting prices. Ask for the latest price list for current availability.</p></div></section>';
		}

		// Amenities: feature tiles, then the checklist
		if ( $d['amenities'] ) {
			$am  = $d['amen'];
			$o[] = '<section class="bkd-sec bkd-sec-navy" id="bkd-amenities"><div class="bkd-wrap"><div class="bkd-sh bkd-rv"><p class="bkd-label">Amenities</p><h2 class="bkd-h2">What comes <em>with the keys.</em></h2>'
				. ( $d['amen_intro'] ? '<p class="bkd-lede">' . $e( $d['amen_intro'] ) . '</p>' : '' ) . '</div>';
			$amp = blockke_dev_amenity_photos( $d['captions'] );
			if ( count( $amp ) >= 2 ) { // captioned amenity photos, swipeable on phones; each opens the gallery
				$o[] = '<div class="bkd-amp bkd-rv" role="list" data-n="' . count( $amp ) . '">';
				foreach ( $amp as $aid ) {
					$o[] = '<figure class="bkd-amp-i" role="listitem"><button type="button" data-bkd-open="' . (int) array_search( $aid, $imgs, true ) . '" aria-label="' . esc_attr( 'Photo: ' . $d['captions'][ $aid ] ) . '">'
						. blockke_dev_img( $aid, 'large', array( 'loading' => 'lazy', 'decoding' => 'async', 'sizes' => '(min-width: 1000px) 30vw, 80vw', 'alt' => '' ) )
						. '</button><figcaption>' . $e( $d['captions'][ $aid ] ) . '</figcaption></figure>';
				}
				$o[] = '</div>';
			}
			if ( $am['tiles'] ) {
				$o[] = '<div class="bkd-amt bkd-rv" data-n="' . count( $am['tiles'] ) . '">';
				foreach ( $am['tiles'] as $t ) {
					$o[] = '<div class="bkd-amt-i">' . blockke_dev_icon( $t['icon'] ) . '<h3>' . $e( $t['title'] ) . '</h3><p>' . $e( $t['text'] ) . '</p></div>';
				}
				$o[] = '</div>';
			}
			if ( $am['rest'] ) {
				$o[] = '<div class="bkd-ess bkd-rv' . ( $am['tiles'] ? '' : ' bkd-ess-solo' ) . '"><div><h3 class="bkd-ess-h">' . ( $am['essentials'] ? 'The everyday <em>essentials</em>' : 'Also <em>included</em>' ) . '</h3>'
					. '<p>' . ( $am['essentials'] ? 'The practical things buyers ask about first.' : 'More of what comes with a home at ' . $e( $d['short'] ) . '.' ) . '</p></div><ul>';
				foreach ( $am['rest'] as $it ) {
					$o[] = '<li>' . blockke_dev_icon( 'check' ) . '<span>' . $e( $it ) . '</span></li>';
				}
				$o[] = '</ul></div>';
			}
			$o[] = '</div></section>';
		}

		// Payment plan (off-plan) or mortgage (complete)
		if ( $d['offplan'] ) {
			$heading = $plan['reservation'] > 0 ? 'Reserve with <em>' . $e( blockke_dev_money( $plan['reservation'] ) ) . '</em>' : ( $plan['deposit'] > 0 ? '<em>' . $e( blockke_dev_num( $plan['deposit'] ) ) . '% deposit,</em> then instalments' : ( $plan['flexible'] ? 'A flexible <em>payment plan</em>' : 'Pay in <em>instalments</em>' ) );
			$text    = $plan['text'] ? $plan['text'] : 'Spread the cost while the building goes up: a deposit, then instalments up to completion. Ask for the developer\'s official schedule.';
			$steps   = $plan['steps'];
			$titles  = '';
			foreach ( $steps as $s ) {
				$titles .= ' ' . $s[0];
			}
			if ( $d['completion'] && $steps && ! preg_match( '/complet|handover|occupation/i', $titles ) ) {
				$steps[] = array( 'Completion', 'Scheduled for ' . $d['completion'] . '.' );
			}
			$o[] = '<section class="bkd-sec bkd-sec-mist" id="bkd-payment"><div class="bkd-wrap bkd-split"><div class="bkd-rv"><p class="bkd-label">Payment plan</p><h2 class="bkd-h2">' . $heading . '</h2>'
				. '<p class="bkd-body bkd-mt">' . $e( $text ) . '</p>';
			if ( $steps ) {
				$o[] = '<ol class="bkd-steps">';
				foreach ( $steps as $s ) {
					$o[] = '<li><h3 class="bkd-h3">' . $e( $s[0] ) . '</h3>' . ( '' !== $s[1] ? '<p>' . $e( $s[1] ) . '</p>' : '' ) . '</li>';
				}
				$o[] = '</ol>';
			}
			$cta = '<div class="bkd-calc-cta"><button class="bkd-cbtn" type="button" data-bkd-enquire="payment-plan">Get the official payment plan</button></div>';
			$o[] = '</div><div class="bkd-panel bkd-rv"><p class="bkd-panel-t">Estimate your instalments</p>' . blockke_dev_calc_instalment( $d['price_from'], $plan, $cta ) . '</div></div></section>';
		} else {
			$cta = '<div class="bkd-calc-cta"><button class="bkd-cbtn" type="button" data-bkd-enquire="mortgage">Talk to us about financing</button></div>';
			$o[] = '<section class="bkd-sec bkd-sec-mist" id="bkd-payment"><div class="bkd-wrap bkd-split"><div class="bkd-rv"><p class="bkd-label">Mortgage</p><h2 class="bkd-h2">Ready to move in. <em>Finance it your way.</em></h2>'
				. '<p class="bkd-body bkd-mt">Pay cash, or put down a deposit and finance the rest with a mortgage. We can introduce you to lenders and compare offers before you commit.</p></div>'
				. '<div class="bkd-panel bkd-rv"><p class="bkd-panel-t">Estimate your monthly repayment</p>' . blockke_dev_calc_mortgage( $d['price_from'], $cta ) . '</div></div></section>';
		}

		// Who it suits, and the investor view
		$suits = $d['suits'];
		if ( $suits['items'] || $d['investors'] ) {
			$o[] = '<section class="bkd-sec" id="bkd-suits"><div class="bkd-wrap bkd-split"><div class="bkd-sticky bkd-rv"><p class="bkd-label">' . ( $suits['items'] ? 'Is it right for you?' : 'For investors' ) . '</p><h2 class="bkd-h2">'
				. ( $suits['items'] ? 'Who it <em>suits best</em>' : 'The <em>investor view</em>' ) . '</h2>'
				. ( $suits['note'] ? '<p class="bkd-body bkd-mt">Our honest view of who it suits, and who it may not.</p>' : '' ) . '</div><div class="bkd-rv">';
			if ( $suits['items'] ) {
				$o[] = '<ul class="bkd-suits">';
				foreach ( $suits['items'] as $it ) {
					$o[] = '<li>' . blockke_dev_icon( 'check' ) . '<span>' . $e( $it ) . '</span></li>';
				}
				$o[] = '</ul>' . ( $suits['note'] ? '<p class="bkd-suits-note">' . $e( $suits['note'] ) . '</p>' : '' );
			}
			if ( $d['investors'] ) {
				$o[] = '<div class="bkd-inv"><p class="bkd-inv-t">' . blockke_dev_icon( 'chart' ) . 'For investors</p><p class="bkd-inv-p">' . $e( $d['investors'] ) . '</p>'
					. '<button class="bkd-link" type="button" data-bkd-enquire="comparables">Ask for rental and resale comparables' . blockke_dev_icon( 'arrow' ) . '</button></div>';
			}
			$o[] = '</div></div></section>';
		}

		// Location
		if ( isset( $nav['location'] ) ) {
			$loc = $d['location'];
			$q   = $d['lat'] && $d['lng'] ? $d['lat'] . ',' . $d['lng'] : $d['address'] . ', ' . $d['area_line'];
			$cut = strrpos( $d['address'], ',' );
			$end = false === $cut ? '' : trim( substr( $d['address'], $cut + 1 ) );
			$adr = '' === $end ? $e( $d['address'] ) : $e( substr( $d['address'], 0, $cut + 1 ) ) . ' <em>' . $e( $end ) . '</em>';
			$o[] = '<section class="bkd-sec" id="bkd-location"><div class="bkd-wrap bkd-split"><div class="bkd-rv"><p class="bkd-label">Location</p><h2 class="bkd-h2">' . $adr . '</h2>'
				. ( $loc['intro'] ? '<p class="bkd-body bkd-mt">' . $e( $loc['intro'] ) . '</p>' : '' );
			if ( $loc['places'] ) {
				$o[] = '<ul class="bkd-places">';
				foreach ( $loc['places'] as $p ) {
					$o[] = '<li><b>' . $e( $p[0] ) . '</b>' . ( '' !== $p[1] ? '<span>' . $e( $p[1] ) . '</span>' : '' ) . '</li>';
				}
				$o[] = '</ul>';
			}
			$o[] = '<p class="bkd-fine bkd-mt-s">' . $e( $loc['note'] ) . '</p></div>'
				. '<div class="bkd-map bkd-rv"><iframe title="Map of ' . esc_attr( $d['short'] ) . '" loading="lazy" referrerpolicy="no-referrer-when-downgrade" src="' . esc_url( 'https://maps.google.com/maps?q=' . rawurlencode( $q ) . '&z=15&output=embed' ) . '"></iframe>'
				. '<a class="bkd-link" target="_blank" rel="noopener" href="' . esc_url( 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode( $q ) ) . '">' . blockke_dev_icon( 'pin' ) . 'Open in Google Maps</a></div></div></section>';
		}

		// Why Block
		if ( ! empty( $cfg['why'] ) ) {
			$o[] = '<section class="bkd-sec bkd-sec-navy" id="bkd-why"><div class="bkd-wrap"><div class="bkd-sh bkd-rv"><p class="bkd-label">Why ' . $e( $cfg['brand'] ) . '</p><h2 class="bkd-h2">Advice first. <em>Then a decision.</em></h2></div><div class="bkd-why">';
			foreach ( $cfg['why'] as $i => $w ) {
				$o[] = '<div class="bkd-rv"><b>0' . ( $i + 1 ) . '</b><h3 class="bkd-h3">' . $e( $w[0] ) . '</h3><p>' . $e( $w[1] ) . '</p></div>';
			}
			$o[] = '</div></div></section>';
		}

		// Similar developments (not on landing pages, where they would only lead visitors away)
		if ( $d['similar'] && ! $lp ) {
			$all = $d['area'] && ( $t = get_term_by( 'name', $d['area'], 'property_area' ) ) ? get_term_link( $t ) : home_url( '/' );
			$o[] = '<section class="bkd-sec bkd-sec-mist" id="bkd-similar"><div class="bkd-wrap"><div class="bkd-sh bkd-sh-row bkd-rv"><div><p class="bkd-label">Also consider</p><h2 class="bkd-h2">' . ( $d['area'] ? 'More in <em>' . $e( $d['area'] ) . '</em>' : 'Similar <em>developments</em>' ) . '</h2></div>'
				. '<a class="bkd-link" href="' . esc_url( is_wp_error( $all ) ? home_url( '/' ) : $all ) . '">See all' . ( $d['area'] ? ' in ' . $e( $d['area'] ) : '' ) . blockke_dev_icon( 'arrow' ) . '</a></div><div class="bkd-cards" style="--n:' . min( count( $d['similar'] ), 4 ) . '">';
			foreach ( $d['similar'] as $s ) {
				$o[] = '<a class="bkd-card bkd-rv" href="' . esc_url( $s['url'] ) . '"><div class="bkd-card-img">'
					. blockke_dev_img( $s['img'], 'medium_large', array( 'loading' => 'lazy', 'decoding' => 'async', 'sizes' => '(min-width: 1000px) 25vw, 78vw', 'alt' => $s['name'], 'data-ph' => $s['name'] ) )
					. '</div><h3 class="bkd-h3">' . $e( $s['name'] ) . '</h3><b>' . $e( $s['price'] ) . '</b><span>' . $e( $s['meta'] ) . '</span></a>';
			}
			$o[] = '</div></div></section>';
		}

		// FAQ
		if ( $d['faqs'] ) {
			$o[] = '<section class="bkd-sec" id="bkd-faq"><div class="bkd-wrap bkd-split"><div class="bkd-sticky bkd-rv"><p class="bkd-label">FAQ</p><h2 class="bkd-h2">Questions buyers <em>ask</em></h2>'
				. '<p class="bkd-body bkd-mt">Can\'t see yours? <a class="bkd-inline" href="' . esc_url( $wa ) . '" target="_blank" rel="noopener" data-bkd-wa>Ask us on WhatsApp</a>.</p></div><div class="bkd-faq bkd-rv">';
			foreach ( $d['faqs'] as $f ) {
				$o[] = '<details><summary>' . $e( $f[0] ) . '</summary><div class="bkd-faq-a">' . ( $lp ? preg_replace( '#</?a\b[^>]*>#i', '', $f[1] ) : $f[1] ) . '</div></details>';
			}
			$o[] = '</div></div></section>';
		}

		// Enquire
		$o[] = '<section class="bkd-sec bkd-sec-mist" id="bkd-enquire"><div class="bkd-wrap bkd-split"><div class="bkd-sticky bkd-rv"><p class="bkd-label">Enquire</p><h2 class="bkd-h2">Speak to an advisor about <em>' . $e( $d['short'] ) . '</em></h2>'
			. '<p class="bkd-body bkd-mt">Get the price list, floor plans and payment plan, or book a viewing. We usually reply within a few hours on working days.</p>';
		if ( $ag['name'] ) {
			$o[] = '<div class="bkd-agent">' . ( $ag['photo'] ? '<img src="' . esc_url( $ag['photo'] ) . '" alt="" loading="lazy" width="64" height="64">' : '<div class="bkd-av">' . $e( mb_substr( $ag['name'], 0, 1 ) ) . '</div>' )
				. '<div><b>' . $e( $ag['name'] ) . '</b><span>' . $e( trim( $ag['role'] . ', ' . $cfg['brand_full'], ', ' ) ) . '</span>'
				. ( $ag['url'] && ! $lp ? '<a class="bkd-inline" href="' . esc_url( $ag['url'] ) . '">View profile</a>' : '' ) . '</div></div>';
		}
		$o[] = '<div class="bkd-direct"><a href="tel:' . esc_attr( $ag['phone'] ) . '">' . blockke_dev_icon( 'phone' ) . $e( $ag['phone_display'] ) . '</a>'
			. '<a href="' . esc_url( $wa ) . '" target="_blank" rel="noopener" data-bkd-wa>' . blockke_dev_icon( 'wa', 'bkd-wa-ic' ) . 'WhatsApp</a>'
			. '<a href="mailto:' . esc_attr( $cfg['email'] ) . '?subject=' . rawurlencode( $d['short'] . ' enquiry' ) . '">' . blockke_dev_icon( 'mail' ) . $e( $cfg['email'] ) . '</a></div></div>'
			. '<div class="bkd-formcard bkd-rv">' . blockke_dev_full_form( $d ) . '</div></div>'
			. '<div class="bkd-wrap"><p class="bkd-disc">' . $e( $cfg['disclaimer'] ) . '</p></div></section>';

		// Mobile bar, enquiry dialog, gallery viewer
		$o[] = '<nav class="bkd-mb" id="bkd-mb" aria-label="Quick contact"><a class="bkd-mb-wa" href="' . esc_url( $wa ) . '" target="_blank" rel="noopener" data-bkd-wa aria-label="WhatsApp">' . blockke_dev_icon( 'wa', 'bkd-wa-ic' ) . '</a>'
			. '<button class="bkd-btn bkd-btn-navy" type="button" data-bkd-enquire="mobile-bar">Get the price list</button></nav>';
		$o[] = '<div class="bkd-md" id="bkd-md" hidden role="dialog" aria-modal="true" aria-labelledby="bkd-md-t"><div class="bkd-md-box">'
			. '<button class="bkd-md-x" type="button" data-bkd-close aria-label="Close">' . blockke_dev_icon( 'close' ) . '</button>'
			. '<p class="bkd-label">' . $e( $d['short'] ) . '</p><h2 class="bkd-h2" id="bkd-md-t">Get the price list</h2><p class="bkd-md-sub" id="bkd-md-sub">Floor plans and the payment plan, sent to your WhatsApp.</p>'
			. '<div id="bkd-md-form">' . blockke_dev_quick_form( 'modal', 'Send it to me', $d ) . '</div></div></div>';
		$o[] = '<div class="bkd-lb" id="bkd-lb" hidden role="dialog" aria-modal="true" aria-label="Photo gallery"><div class="bkd-lb-top"><span id="bkd-lb-n"></span>'
			. '<button class="bkd-lb-x" type="button" data-bkd-lbclose aria-label="Close gallery">' . blockke_dev_icon( 'close' ) . '</button></div>'
			. '<div class="bkd-lb-stage"><button class="bkd-lb-prev" type="button" aria-label="Previous photo">‹</button><img id="bkd-lb-img" alt=""><button class="bkd-lb-next" type="button" aria-label="Next photo">›</button></div>'
			. '<div class="bkd-lb-strip" id="bkd-lb-strip"></div></div>';
		if ( $lp ) {
			$o[] = blockke_dev_lp_footer( $d );
		}
		$o[] = '</div>';
		return implode( '', $o );
	}

	/* =====================================================================
	   2) Development layout: styles
	   ===================================================================== */

	function blockke_dev_css() {
		return <<<'CSS'
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
#bkd .bkd-h1{font-size:clamp(46px,5.4vw,78px)!important;line-height:1.02!important}
#bkd .bkd-h2{font-size:clamp(36px,4vw,56px)!important;line-height:1.06!important}
#bkd :is(.bkd-h1,.bkd-h2,.bkd-ess-h) em{font-family:inherit!important;font-style:italic;font-weight:inherit;color:var(--brass-t)}
#bkd .bkd-h3{font-size:18px!important;font-weight:500!important;letter-spacing:-.01em!important;line-height:1.35!important}
#bkd .bkd-label{display:flex;align-items:center;gap:14px;margin:0 0 20px;font-size:11px;font-weight:600;letter-spacing:.22em;text-transform:uppercase;color:var(--brass-d)}
#bkd .bkd-label::before{content:"";width:28px;height:1px;background:var(--brass)}
#bkd .bkd-lead{font-size:clamp(18px,1.6vw,21px);line-height:1.65;font-weight:300;color:var(--ink)}
#bkd .bkd-body{color:var(--slate)}
#bkd .bkd-mt{margin-top:22px;max-width:540px}
#bkd .bkd-mt-s{margin-top:16px}
#bkd .bkd-fine{font-size:12px;line-height:1.6;color:var(--muted)}
#bkd .bkd-inline{color:var(--navy)!important;font-weight:600;text-decoration:underline;text-underline-offset:4px}
#bkd .bkd-btn{display:inline-flex;align-items:center;justify-content:center;gap:10px;width:auto;min-height:52px;height:auto;margin:0;padding:0 26px!important;border:1px solid transparent!important;border-radius:0!important;font:600 12.5px/1.2 var(--font)!important;letter-spacing:.12em!important;text-transform:uppercase!important;text-decoration:none!important;text-align:center;white-space:nowrap;cursor:pointer;box-shadow:none!important;transition:background-color .2s,border-color .2s,color .2s}
#bkd .bkd-btn:disabled{opacity:.6;cursor:progress}
#bkd .bkd-btn-navy{background:var(--navy)!important;border-color:var(--navy)!important;color:#fff!important}
#bkd .bkd-btn-navy:hover,#bkd .bkd-btn-navy:focus-visible{background:#fff!important;color:var(--navy)!important;border-color:var(--navy)!important}
#bkd .bkd-btn-line{background:#fff!important;border-color:var(--line-2)!important;color:var(--navy)!important}
#bkd .bkd-btn-line:hover{border-color:var(--navy)!important;color:var(--navy)!important}
#bkd .bkd-btn-white{background:#fff!important;border-color:#fff!important;color:var(--navy)!important}
#bkd .bkd-btn-white:hover{background:var(--navy)!important;border-color:var(--navy)!important;color:#fff!important}
#bkd .bkd-btn-sm{min-height:42px;padding:0 18px!important;font-size:11.5px!important}
#bkd .bkd-btn-block{display:flex;width:100%}
#bkd .bkd-btn .bkd-i{width:17px;height:17px}
#bkd .bkd-link{display:inline-flex;align-items:center;gap:8px;margin:0;padding:0 0 3px!important;border:0!important;border-bottom:1px solid var(--line-2)!important;border-radius:0!important;background:none!important;box-shadow:none!important;font:600 14px/1.4 var(--font)!important;letter-spacing:0!important;text-transform:none!important;color:var(--navy)!important;text-decoration:none!important;cursor:pointer;transition:border-color .2s}
#bkd .bkd-link:hover{border-color:var(--navy)!important;color:var(--navy)!important}
#bkd .bkd-link .bkd-i{width:15px;height:15px}
/* Hero */
#bkd .bkd-hero{padding:clamp(24px,3.4vw,52px) 0 clamp(64px,8vw,104px)}
#bkd .bkd-hero-grid{display:grid;gap:30px;grid-template-areas:"head" "media" "body"}
#bkd .bkd-hero-head{grid-area:head;min-width:0}
#bkd .bkd-hero-media{grid-area:media}
#bkd .bkd-hero-body{grid-area:body;min-width:0}
@media (min-width:1000px){#bkd .bkd-hero-grid{grid-template-columns:minmax(0,5fr) minmax(0,6fr);grid-template-areas:"head media" "body media";column-gap:80px;row-gap:40px;align-items:start}}
#bkd .bkd-hero-top{display:flex;justify-content:space-between;align-items:center;gap:16px;margin-bottom:28px}
#bkd .bkd-crumbs{display:flex;flex-wrap:wrap;gap:6px 10px;font-size:12px;line-height:1.5;color:var(--muted);min-width:0}
#bkd .bkd-crumbs a{color:var(--slate)}
#bkd .bkd-crumbs a:hover{color:var(--navy)!important;text-decoration:underline}
#bkd .bkd-crumbs span[aria-current]{color:var(--muted)}
#bkd .bkd-share{display:inline-flex;align-items:center;gap:8px;flex:none;margin:0;padding:6px 0!important;border:0!important;background:none!important;box-shadow:none!important;font:600 12px/1 var(--font)!important;letter-spacing:.08em!important;text-transform:uppercase!important;color:var(--navy)!important;cursor:pointer}
#bkd .bkd-share .bkd-i{width:16px;height:16px}
#bkd .bkd-status{display:flex;flex-wrap:wrap;align-items:center;gap:10px 14px;margin:0 0 22px;font-size:11px;font-weight:600;letter-spacing:.2em;text-transform:uppercase;color:var(--slate)}
#bkd .bkd-status b{display:inline-flex;align-items:center;gap:8px;padding:7px 13px;border:0;border-radius:999px;background:var(--brass-tint);font-weight:600;letter-spacing:.14em;color:#7A5A22}
#bkd .bkd-status b::before{content:"";width:6px;height:6px;border-radius:50%;background:var(--brass)}
#bkd .bkd-hero-tag{margin-top:20px;max-width:480px;font-size:clamp(17px,1.5vw,19px);font-weight:300;line-height:1.65;color:var(--slate)}
#bkd .bkd-hero-media{position:relative;margin:0 calc(var(--gutter) * -1);aspect-ratio:4/3;overflow:hidden;background:var(--mist)}
@media (min-width:1000px){#bkd .bkd-hero-media{margin:0;aspect-ratio:4/5}}
@media (min-width:1000px) and (max-height:940px){#bkd .bkd-hero{padding-top:18px}#bkd .bkd-hero-top{margin-bottom:16px}#bkd .bkd-status{margin-bottom:14px}#bkd .bkd-h1{font-size:clamp(44px,4.2vw,62px)!important}#bkd .bkd-hero-tag{margin-top:12px;font-size:17px;line-height:1.6}#bkd .bkd-hfacts{margin-bottom:16px}#bkd .bkd-hfacts>div{padding:11px 0}#bkd .bkd-quick{padding:18px 24px}#bkd .bkd-quick-s{display:none}#bkd .bkd-quick-t{margin-bottom:12px}#bkd .bkd-hero-grid{row-gap:28px}}
#bkd .bkd-hero-media>img{position:absolute;inset:0;width:100%!important;height:100%!important;max-width:none!important;object-fit:cover}
#bkd .bkd-hero-media figcaption{position:absolute;left:16px;bottom:16px;font-size:11px;letter-spacing:.06em;color:#fff;text-shadow:0 1px 10px rgba(0,0,0,.45)}
#bkd .bkd-all{position:absolute;right:16px;bottom:16px}
#bkd .bkd-hfacts{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));margin:0 0 24px;border-top:1px solid var(--line);border-bottom:1px solid var(--line)}
#bkd .bkd-hfacts>div{padding:16px 0;min-width:0}
#bkd .bkd-hfacts>div+div{padding-left:18px;border-left:1px solid var(--line)}
#bkd .bkd-hfacts dt{font-size:10px;font-weight:600;letter-spacing:.18em;text-transform:uppercase;color:var(--muted)}
#bkd .bkd-hfacts dd{margin:4px 0 0;font-family:var(--display);font-size:clamp(24px,2.2vw,30px);font-weight:var(--display-w);line-height:1.2;letter-spacing:0;color:var(--navy);white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
#bkd .bkd-quick{padding:clamp(22px,2.4vw,30px);background:var(--navy);color:var(--on-navy)}
#bkd .bkd-quick-t{margin:0 0 4px;font-family:var(--display)!important;font-size:clamp(24px,2vw,28px);font-weight:var(--display-w);line-height:1.15;color:#fff}
#bkd .bkd-quick-s{margin:0 0 16px;font-size:14px;line-height:1.5;color:var(--on-navy)}
#bkd .bkd-quick-alt{margin-top:16px;text-align:center}
#bkd .bkd-quick :focus-visible{outline-color:var(--brass-l)}
#bkd .bkd-quick .bkd-f>span{color:var(--on-navy)}
#bkd .bkd-quick .bkd-f>input{border-color:#fff!important}
#bkd .bkd-quick .bkd-f>input:focus{border-color:var(--brass-l)!important;box-shadow:0 0 0 3px rgba(221,185,127,.4)!important}
#bkd .bkd-quick .bkd-f.bkd-bad>input{border-color:#FF8A7A!important;box-shadow:0 0 0 2px rgba(255,138,122,.55)!important}
#bkd .bkd-quick .bkd-err{color:#FFB4A9}
#bkd .bkd-quick .bkd-btn-navy{background:var(--brass)!important;border-color:var(--brass)!important;color:var(--navy)!important}
#bkd .bkd-quick .bkd-btn-navy:hover,#bkd .bkd-quick .bkd-btn-navy:focus-visible{background:var(--brass-l)!important;border-color:var(--brass-l)!important;color:var(--navy)!important}
#bkd .bkd-quick .bkd-consent{color:rgba(255,255,255,.66)}
#bkd .bkd-quick .bkd-consent a{color:#fff}
#bkd .bkd-quick .bkd-link,#bkd .bkd-quick .bkd-link:hover{border-bottom-color:rgba(255,255,255,.45)!important;color:#fff!important}
#bkd .bkd-quick .bkd-ok h3{color:#fff!important}
#bkd .bkd-quick .bkd-ok-msg{color:var(--on-navy)}
#bkd .bkd-quick .bkd-ok-tick{background:var(--brass);color:var(--navy)}
#bkd .bkd-quick .bkd-btn-line{background:transparent!important;border-color:rgba(255,255,255,.45)!important;color:#fff!important}
#bkd .bkd-quick .bkd-btn-line:hover{border-color:#fff!important;color:#fff!important}
/* Forms */
#bkd .bkd-form{display:grid;gap:16px}
#bkd .bkd-qgrid,#bkd .bkd-two{display:grid;gap:14px}
@media (min-width:560px){#bkd .bkd-qgrid,#bkd .bkd-two{grid-template-columns:1fr 1fr}}
#bkd .bkd-f{display:grid;gap:6px;min-width:0;margin:0;padding:0;border:0}
#bkd .bkd-f>span,#bkd .bkd-f legend{padding:0;font-size:11px;font-weight:600;line-height:1.4;letter-spacing:.12em;text-transform:uppercase;color:var(--slate)}
#bkd .bkd-f legend{margin-bottom:10px}
#bkd .bkd-f small{text-transform:none;letter-spacing:0;font-weight:400;color:var(--muted)}
#bkd .bkd-f>input,#bkd .bkd-f>textarea{display:block;width:100%;max-width:none;height:52px;min-height:0;margin:0;padding:0 16px;border:1px solid var(--line-2)!important;border-radius:0!important;background:#fff!important;box-shadow:none!important;font:400 16px/1.4 var(--font)!important;color:var(--navy)!important;-webkit-appearance:none;appearance:none;transition:border-color .2s,box-shadow .2s}
#bkd .bkd-f>textarea{height:104px;padding:14px 16px;resize:vertical;line-height:1.5!important}
#bkd .bkd-f input::placeholder,#bkd .bkd-f textarea::placeholder{color:#98A3B0;opacity:1}
#bkd .bkd-f>input:focus,#bkd .bkd-f>textarea:focus{outline:0;border-color:var(--navy)!important;box-shadow:0 0 0 3px rgba(13,36,64,.1)!important}
#bkd .bkd-f.bkd-bad>input{border-color:var(--err)!important}
#bkd .bkd-err{font-size:12px;font-style:normal;font-weight:600;color:var(--err)}
#bkd .bkd-err:empty{display:none}
#bkd .bkd-hp{display:none!important}
#bkd .bkd-consent{font-size:12px;line-height:1.55;color:var(--muted)}
#bkd .bkd-consent a{text-decoration:underline}
#bkd .bkd-chips{display:flex;flex-wrap:wrap;gap:8px}
#bkd .bkd-chip{position:relative;display:block;margin:0}
#bkd .bkd-chip input{position:absolute;inset:0;width:100%;height:100%;margin:0;opacity:0;cursor:pointer}
#bkd .bkd-chip span{display:inline-flex;align-items:center;min-height:42px;padding:0 16px;border:1px solid var(--line-2);background:#fff;font-size:13px;font-weight:500;color:var(--navy);transition:background-color .2s,border-color .2s,color .2s}
#bkd .bkd-chip input:checked+span{background:var(--navy);border-color:var(--navy);color:#fff}
#bkd .bkd-chip input:focus-visible+span{outline:2px solid var(--navy);outline-offset:2px}
#bkd .bkd-alert{padding:12px 14px;background:#FBECEA;color:var(--err);font-size:14px;font-weight:600;line-height:1.5}
#bkd .bkd-alert a{text-decoration:underline}
#bkd .bkd-ok{padding:4px 0}
#bkd .bkd-ok-tick{display:grid;place-items:center;width:52px;height:52px;margin-bottom:22px;border-radius:50%;background:var(--navy);color:#fff}
#bkd .bkd-ok-tick .bkd-i{width:24px;height:24px;stroke-width:2.2}
#bkd .bkd-ok h3{margin-bottom:10px!important;font-family:var(--display)!important;font-size:32px!important;font-weight:var(--display-w)!important;letter-spacing:var(--display-ls)!important;line-height:1.15!important}
#bkd .bkd-ok h3 span{font-family:inherit!important;font-weight:inherit!important;letter-spacing:inherit!important}
#bkd .bkd-ok-msg{margin-bottom:22px;color:var(--slate)}
#bkd .bkd-ok-actions{display:flex;flex-wrap:wrap;gap:10px}
#bkd .bkd-more{display:grid;gap:14px;margin:0 0 22px;padding-top:18px;border-top:1px solid var(--line)}
#bkd .bkd-more-t{margin:0;font-size:14px;font-weight:600;line-height:1.4;color:var(--navy)}
#bkd .bkd-more-t small{font-weight:400;color:var(--muted)}
#bkd .bkd-more .bkd-chip span{min-height:38px;padding:0 13px}
#bkd .bkd-more>.bkd-btn{justify-self:start}
#bkd .bkd-more-done{margin:0;font-size:14px;line-height:1.55;color:var(--slate)}
#bkd .bkd-quick .bkd-more{border-top-color:rgba(255,255,255,.16)}
#bkd .bkd-quick .bkd-more-t{color:#fff}
#bkd .bkd-quick .bkd-more-t small,#bkd .bkd-quick .bkd-more .bkd-f legend,#bkd .bkd-quick .bkd-more-done{color:var(--on-navy)}
#bkd .bkd-quick .bkd-chip span{background:transparent;border-color:rgba(255,255,255,.35);color:#fff}
#bkd .bkd-quick .bkd-chip input:checked+span{background:var(--brass);border-color:var(--brass);color:var(--navy)}
#bkd .bkd-quick .bkd-chip input:focus-visible+span{outline-color:var(--brass-l)}
/* Section nav */
#bkd .bkd-subnav{position:sticky;top:var(--top);z-index:40;display:none;background:rgba(251,249,246,.97);-webkit-backdrop-filter:blur(10px);backdrop-filter:blur(10px);border-top:1px solid var(--line);border-bottom:1px solid var(--line)}
@media (min-width:1000px){#bkd .bkd-subnav{display:block}}
#bkd .bkd-subnav-in{display:flex;align-items:center;gap:32px;height:64px}
#bkd .bkd-subnav-name{flex:none;font-size:14px;font-weight:600;color:var(--navy);white-space:nowrap}
#bkd .bkd-subnav-links{display:flex;gap:26px;flex:1;min-width:0;overflow:hidden}
#bkd .bkd-subnav-links a{position:relative;padding:21px 0;font-size:13px;font-weight:500;color:var(--slate);white-space:nowrap;transition:color .2s}
#bkd .bkd-subnav-links a:hover,#bkd .bkd-subnav-links a.on{color:var(--navy)!important}
#bkd .bkd-subnav-links a.on::after{content:"";position:absolute;left:0;right:0;bottom:-1px;height:2px;background:var(--navy)}
#bkd .bkd-subnav-r{display:flex;align-items:center;gap:22px;flex:none}
#bkd .bkd-subnav-wa{display:inline-flex;align-items:center;gap:8px;font-size:13px;font-weight:600;color:var(--navy)}
#bkd .bkd-subnav-wa .bkd-i{width:20px;height:20px}
/* Facts band */
#bkd .bkd-facts{background:var(--navy)}
#bkd .bkd-facts dl{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));margin:0}
#bkd .bkd-facts dl>div{padding:24px 0;min-width:0}
#bkd .bkd-facts dl>div:nth-child(even){padding-left:20px;border-left:1px solid var(--navy-line)}
#bkd .bkd-facts dl>div:nth-child(n+3){border-top:1px solid var(--navy-line)}
#bkd .bkd-facts dt{font-size:10px;font-weight:600;letter-spacing:.18em;text-transform:uppercase;color:var(--brass-l)}
#bkd .bkd-facts dd{margin:6px 0 0;font-family:var(--display);font-size:clamp(23px,2.2vw,30px);font-weight:var(--display-w);line-height:1.15;letter-spacing:0;color:#fff}
@media (min-width:900px){#bkd .bkd-facts dl{grid-template-columns:repeat(var(--n,4),minmax(0,1fr))}#bkd .bkd-facts dl>div{padding:32px 0;border-top:0!important}#bkd .bkd-facts dl>div:nth-child(even){padding-left:0;border-left:0}#bkd .bkd-facts dl>div+div{padding-left:28px!important;border-left:1px solid var(--navy-line)!important}}
/* Sections */
#bkd .bkd-sec{padding:var(--sec) 0;scroll-margin-top:calc(var(--top) + 72px)}
#bkd .bkd-sec-tight{padding-top:0}
#bkd .bkd-sec-mist{background:var(--mist)}
#bkd .bkd-sec-navy{background:var(--navy);color:var(--on-navy)}
#bkd .bkd-sec-navy .bkd-h2,#bkd .bkd-sec-navy h3{color:#fff!important}
#bkd .bkd-sec-navy :is(.bkd-h2,.bkd-ess-h) em{color:var(--brass-l)}
#bkd .bkd-sec-navy .bkd-label{color:var(--brass-l)}
#bkd .bkd-sec-navy .bkd-label::before{background:var(--brass-l)}
#bkd .bkd-sec-navy :focus-visible{outline-color:var(--brass-l)}
#bkd .bkd-lede{margin-top:22px;max-width:620px;font-size:clamp(17px,1.5vw,19px);font-weight:300;line-height:1.65;color:var(--on-navy)}
#bkd .bkd-split{display:grid;gap:40px}
@media (min-width:1000px){#bkd .bkd-split{grid-template-columns:minmax(0,5fr) minmax(0,6fr);column-gap:80px;align-items:start}#bkd .bkd-split .bkd-sticky{position:sticky;top:calc(var(--top) + 112px)}}
#bkd .bkd-sh{margin-bottom:clamp(40px,5.6vw,68px)}
#bkd .bkd-sh-row{display:flex;flex-wrap:wrap;align-items:flex-end;justify-content:space-between;gap:20px 40px}
#bkd .bkd-flow>*+*{margin-top:22px}
#bkd .bkd-hl{margin:40px 0 0!important;border-top:1px solid var(--line)}
#bkd .bkd-hl li{display:flex;gap:16px;padding:16px 0;border-bottom:1px solid var(--line);color:var(--ink);line-height:1.6}
#bkd .bkd-hl .bkd-i{margin-top:4px;color:var(--brass-d);stroke-width:2}
/* Full description */
#bkd .bkd-more{margin-top:36px!important;border-top:1px solid var(--line)}
#bkd .bkd-more summary{display:inline-flex;align-items:center;gap:10px;margin-top:22px;padding-bottom:3px;border-bottom:1px solid var(--line-2);font-size:14px;font-weight:600;color:var(--navy);cursor:pointer;list-style:none}
#bkd .bkd-more summary::-webkit-details-marker{display:none}
#bkd .bkd-more summary::after{content:"";width:8px;height:8px;margin-top:-3px;border-right:1.5px solid var(--navy);border-bottom:1.5px solid var(--navy);transform:rotate(45deg);transition:transform .2s}
#bkd .bkd-more[open] summary::after{transform:rotate(-135deg);margin-top:3px}
#bkd .bkd-more .bkd-more-close,#bkd .bkd-more[open] .bkd-more-open{display:none}
#bkd .bkd-more[open] .bkd-more-close{display:inline}
#bkd .bkd-prose{margin-top:28px;color:var(--slate);font-size:15.5px;line-height:1.75}
#bkd .bkd-prose>*+*{margin-top:16px}
#bkd .bkd-prose :is(h3,h4,h5,h6){margin-top:36px!important;font-size:19px!important;font-weight:500!important;letter-spacing:-.01em!important;line-height:1.35!important}
#bkd .bkd-prose :is(h4,h5,h6){margin-top:26px!important;font-size:16px!important}
#bkd .bkd-prose p{margin:0;margin-top:14px}
#bkd .bkd-prose :is(ul,ol){margin-top:14px;padding-left:22px}
#bkd .bkd-prose ul{list-style:disc}
#bkd .bkd-prose ol{list-style:decimal}
#bkd .bkd-prose li+li{margin-top:6px}
#bkd .bkd-prose a{color:var(--navy);text-decoration:underline;text-underline-offset:3px}
#bkd .bkd-prose strong{color:var(--ink);font-weight:600}
#bkd .bkd-prose figure{margin:20px 0 0;overflow-x:auto}
#bkd .bkd-prose table{width:100%;border-collapse:collapse;font-size:14px}
#bkd .bkd-prose :is(th,td){padding:11px 12px 11px 0;border-bottom:1px solid var(--line);text-align:left;vertical-align:top}
#bkd .bkd-prose th{font-size:10px;font-weight:600;letter-spacing:.14em;text-transform:uppercase;color:var(--muted);border-bottom-color:var(--navy)}
#bkd .bkd-prose img{height:auto;margin-top:16px}
/* Gallery */
#bkd .bkd-gal{display:grid;gap:12px;grid-template-columns:repeat(2,minmax(0,1fr))}
#bkd .bkd-gal button{position:relative;display:block;margin:0;padding:0!important;border:0!important;border-radius:0!important;overflow:hidden;aspect-ratio:4/3;background:var(--mist)!important;cursor:zoom-in;box-shadow:none!important}
#bkd .bkd-gal button:first-child{grid-column:1/-1;aspect-ratio:16/9}
#bkd .bkd-gal img{position:absolute;inset:0;width:100%!important;height:100%!important;max-width:none!important;object-fit:cover;transition:transform .8s ease}
#bkd .bkd-gal button:hover img{transform:scale(1.03)}
#bkd .bkd-gal-cap{position:absolute;left:0;right:0;bottom:0;z-index:1;padding:28px 14px 12px;background:linear-gradient(180deg,rgba(13,36,64,0),rgba(13,36,64,.72));color:#fff;font-size:13px;font-weight:500;line-height:1.35;text-align:left;pointer-events:none}
#bkd .bkd-gal button:nth-child(n+4){display:none}
@media (min-width:900px){#bkd .bkd-gal{grid-template-columns:repeat(6,minmax(0,1fr));gap:16px}#bkd .bkd-gal button:first-child{grid-column:span 4;grid-row:span 2;aspect-ratio:auto}#bkd .bkd-gal button:nth-child(2),#bkd .bkd-gal button:nth-child(3){grid-column:span 2}}
#bkd .bkd-gal-foot{margin-top:22px;align-items:center}
/* Video */
#bkd .bkd-video{position:relative;aspect-ratio:16/9;overflow:hidden;background:var(--navy)}
#bkd .bkd-video button{position:absolute;inset:0;display:grid;place-items:center;align-content:center;gap:16px;width:100%;margin:0;padding:0!important;border:0!important;border-radius:0!important;background:none!important;box-shadow:none!important;cursor:pointer}
#bkd .bkd-video img{position:absolute;inset:0;width:100%!important;height:100%!important;max-width:none!important;object-fit:cover;opacity:.8;transition:opacity .3s}
#bkd .bkd-video button:hover img{opacity:.95}
#bkd .bkd-play{position:relative;display:grid;place-items:center;width:84px;height:84px;border-radius:50%;background:#fff;color:var(--navy);box-shadow:0 10px 30px rgba(0,0,0,.25)}
#bkd .bkd-play .bkd-i{width:26px;height:26px;margin-left:4px;fill:currentColor;stroke:none}
#bkd .bkd-video-l{position:relative;font-size:12px;font-weight:600;letter-spacing:.14em;text-transform:uppercase;color:#fff;text-shadow:0 1px 10px rgba(0,0,0,.5)}
#bkd .bkd-video iframe{position:absolute;inset:0;width:100%;height:100%;border:0}
/* Residences */
#bkd .bkd-filters{display:flex;flex-wrap:wrap;gap:8px}
#bkd .bkd-filters button{min-height:40px;margin:0;padding:0 16px!important;border:1px solid var(--line-2)!important;border-radius:0!important;background:#fff!important;box-shadow:none!important;font:500 13px/1 var(--font)!important;letter-spacing:0!important;text-transform:none!important;color:var(--navy)!important;cursor:pointer;transition:all .2s}
#bkd .bkd-filters button[aria-pressed="true"]{background:var(--navy)!important;border-color:var(--navy)!important;color:#fff!important}
#bkd .bkd-tbl{width:100%;border-collapse:collapse;background:transparent}
#bkd .bkd-tbl th{padding:0 16px 16px 0;background:none;text-align:left;font-size:10px;font-weight:600;letter-spacing:.18em;text-transform:uppercase;color:var(--muted);border:0;border-bottom:1px solid var(--navy)}
#bkd .bkd-tbl td{padding:24px 16px 24px 0;background:none;border:0;border-bottom:1px solid var(--line);vertical-align:middle;font-size:15px;color:var(--slate)}
#bkd .bkd-tbl td.bkd-t{font-size:17px;font-weight:500;color:var(--navy)}
#bkd .bkd-tbl td.bkd-t small{display:block;margin-top:2px;font-size:13px;font-weight:400;color:var(--muted)}
#bkd .bkd-tbl td.bkd-p{font-weight:600;color:var(--navy);white-space:nowrap}
#bkd .bkd-tbl td.bkd-a{padding-right:0;text-align:right;white-space:nowrap}
#bkd .bkd-tbl td.bkd-a>*+*{margin-left:22px}
#bkd .bkd-tbl-note{margin-top:22px}
@media (max-width:719px){#bkd .bkd-tbl thead{display:none}#bkd .bkd-tbl,#bkd .bkd-tbl tbody,#bkd .bkd-tbl tr,#bkd .bkd-tbl td{display:block;width:100%}#bkd .bkd-tbl tr{display:grid;grid-template-columns:1fr auto;gap:4px 16px;padding:22px 0;border-bottom:1px solid var(--line)}#bkd .bkd-tbl tr[hidden]{display:none}#bkd .bkd-tbl td{padding:0;border:0}#bkd .bkd-tbl td.bkd-s{grid-column:1;font-size:14px}#bkd .bkd-tbl td.bkd-p{grid-column:2;grid-row:1;text-align:right}#bkd .bkd-tbl td.bkd-a{grid-column:1/-1;margin-top:12px;text-align:left;white-space:normal}}
/* Amenities: captioned photos, feature tiles + checklist (navy section) */
#bkd .bkd-amp{display:grid;grid-auto-flow:column;grid-auto-columns:80%;gap:12px;margin:0 calc(var(--gutter) * -1) clamp(28px,3.4vw,40px);padding:0 var(--gutter);overflow-x:auto;scroll-snap-type:x mandatory;scroll-padding:0 var(--gutter);overscroll-behavior-x:contain;scrollbar-width:none}
#bkd .bkd-amp::-webkit-scrollbar{display:none}
#bkd .bkd-amp-i{position:relative;margin:0;aspect-ratio:4/3;overflow:hidden;background:var(--navy-2);scroll-snap-align:start}
#bkd .bkd-amp-i button{position:absolute;inset:0;display:block;width:100%;margin:0;padding:0!important;border:0!important;border-radius:0!important;background:none!important;box-shadow:none!important;cursor:zoom-in}
#bkd .bkd-amp-i img{position:absolute;inset:0;width:100%!important;height:100%!important;max-width:none!important;object-fit:cover;transition:transform .8s ease}
#bkd .bkd-amp-i button:hover img{transform:scale(1.03)}
#bkd .bkd-amp-i figcaption{position:absolute;left:0;right:0;bottom:0;padding:44px 18px 16px;background:linear-gradient(180deg,rgba(13,36,64,0),rgba(13,36,64,.86));font-family:var(--display)!important;font-size:clamp(21px,2vw,25px);font-weight:var(--display-w);line-height:1.15;color:#fff;pointer-events:none}
@media (min-width:700px){#bkd .bkd-amp{grid-auto-columns:calc((100% - 24px) / 2.4)}}
@media (min-width:1000px){
#bkd .bkd-amp{grid-auto-flow:row;grid-template-columns:repeat(3,minmax(0,1fr));gap:16px;margin-left:0;margin-right:0;padding:0;overflow:visible}
#bkd .bkd-amp[data-n="2"]{grid-template-columns:repeat(2,minmax(0,1fr))}
#bkd .bkd-amp[data-n="4"],#bkd .bkd-amp[data-n="5"],#bkd .bkd-amp[data-n="8"]{grid-template-columns:repeat(4,minmax(0,1fr))}
#bkd .bkd-amp[data-n="5"] .bkd-amp-i:nth-child(n+5),#bkd .bkd-amp[data-n="7"] .bkd-amp-i:nth-child(n+7){display:none}
}
#bkd .bkd-amt{display:grid;gap:1px;background:var(--navy-line);border:1px solid var(--navy-line)}
#bkd .bkd-amt-i{display:grid;grid-template-columns:28px minmax(0,1fr);gap:4px 16px;padding:24px 20px;background:var(--navy)}
#bkd .bkd-amt-i .bkd-i{grid-row:span 2;width:28px;height:28px;margin-top:2px;color:var(--brass-l);stroke-width:1.4}
#bkd .bkd-amt-i h3{margin:0!important;font-family:var(--display)!important;font-size:clamp(25px,2.3vw,32px)!important;font-weight:var(--display-w)!important;letter-spacing:var(--display-ls)!important;line-height:1.1!important}
#bkd .bkd-amt-i p{font-size:15px;line-height:1.6;color:rgba(255,255,255,.68)}
@media (min-width:640px){#bkd .bkd-amt{grid-template-columns:repeat(2,minmax(0,1fr))}#bkd .bkd-amt[data-n="3"]{grid-template-columns:repeat(3,minmax(0,1fr))}#bkd .bkd-amt[data-n="1"]{grid-template-columns:1fr}#bkd .bkd-amt-i{display:block;padding:30px 26px}#bkd .bkd-amt-i h3{margin:22px 0 8px!important}}
@media (min-width:1000px){#bkd .bkd-amt{grid-template-columns:repeat(3,minmax(0,1fr))}#bkd .bkd-amt[data-n="2"]{grid-template-columns:repeat(2,minmax(0,1fr))}#bkd .bkd-amt[data-n="4"]{grid-template-columns:repeat(4,minmax(0,1fr))}#bkd .bkd-amt[data-n="1"]{grid-template-columns:1fr}#bkd .bkd-amt-i{padding:40px 34px}}
#bkd .bkd-ess{display:grid;gap:28px;margin-top:clamp(56px,7vw,88px)}
#bkd .bkd-ess-solo{margin-top:0}
#bkd .bkd-ess-h{font-family:var(--display)!important;font-size:clamp(30px,3vw,42px)!important;font-weight:var(--display-w)!important;letter-spacing:var(--display-ls)!important;line-height:1.08!important}
#bkd .bkd-ess-h+p{margin-top:12px;max-width:340px;font-size:15px;line-height:1.6;color:rgba(255,255,255,.68)}
#bkd .bkd-ess ul{display:grid;border-top:1px solid var(--navy-line)}
#bkd .bkd-ess li{display:flex;align-items:flex-start;gap:14px;padding:15px 0;border-bottom:1px solid var(--navy-line);font-weight:500;line-height:1.5;color:rgba(255,255,255,.9)}
#bkd .bkd-ess li .bkd-i{margin-top:3px;color:var(--brass-l);stroke-width:2}
@media (min-width:640px){#bkd .bkd-ess ul{grid-template-columns:repeat(2,minmax(0,1fr));column-gap:32px}}
@media (min-width:1000px){#bkd .bkd-ess{grid-template-columns:minmax(0,1fr) minmax(0,2fr);gap:64px}}
/* Payment */
#bkd .bkd-steps{margin:40px 0 0!important;counter-reset:s}
#bkd .bkd-steps li{position:relative;display:grid;grid-template-columns:44px 1fr;gap:2px 14px;padding:0 0 26px}
#bkd .bkd-steps li::before{counter-increment:s;content:counter(s,decimal-leading-zero);grid-row:span 2;padding-top:1px;font-size:12px;font-weight:600;letter-spacing:.08em;color:var(--brass-d)}
#bkd .bkd-steps li::after{content:"";position:absolute;left:9px;top:26px;bottom:6px;width:1px;background:var(--line-2)}
#bkd .bkd-steps li:last-child::after{display:none}
#bkd .bkd-steps h3{font-size:16px!important}
#bkd .bkd-steps p{font-size:15px;line-height:1.6;color:var(--slate)}
#bkd .bkd-panel{padding:clamp(24px,4vw,48px);background:#fff}
#bkd .bkd-panel-t{margin:0 0 24px;font-size:16px;font-weight:600;line-height:1.4;color:var(--navy)}
#bkd #bkd-calc{--c-line:var(--line-2);--c-muted:var(--muted)}
#bkd #bkd-calc .bkd-co-main strong{font-family:var(--display)!important;font-size:clamp(40px,4.4vw,54px);font-weight:var(--display-w);letter-spacing:0;line-height:1.05}
/* Who it suits, investors */
#bkd .bkd-suits{border-top:1px solid var(--line)}
#bkd .bkd-suits li{display:flex;align-items:flex-start;gap:14px;padding:16px 0;border-bottom:1px solid var(--line);line-height:1.55;color:var(--ink)}
#bkd .bkd-suits .bkd-i{margin-top:4px;color:var(--brass-d);stroke-width:2}
#bkd .bkd-suits-note{margin-top:20px;padding:18px 20px;background:var(--mist);font-size:14.5px;line-height:1.65;color:var(--slate)}
#bkd .bkd-inv{margin-top:36px;padding:clamp(24px,3.4vw,36px);background:var(--navy);color:var(--on-navy)}
#bkd .bkd-inv-t{display:flex;align-items:center;gap:10px;margin-bottom:14px;font-size:11px;font-weight:600;letter-spacing:.2em;text-transform:uppercase;color:var(--brass-l)}
#bkd .bkd-inv-t .bkd-i{width:20px;height:20px}
#bkd .bkd-inv-p{font-size:15.5px;line-height:1.75;color:rgba(255,255,255,.84)}
#bkd .bkd-inv .bkd-link,#bkd .bkd-inv .bkd-link:hover{margin-top:22px;color:#fff!important;border-bottom-color:rgba(255,255,255,.5)!important}
#bkd .bkd-inv :focus-visible{outline-color:var(--brass-l)}
/* Location */
#bkd .bkd-map{position:relative;aspect-ratio:4/3;overflow:hidden;background:var(--mist)}
#bkd .bkd-map iframe{position:absolute;inset:0;width:100%;height:100%;border:0;filter:grayscale(1) contrast(1.04)}
#bkd .bkd-map .bkd-link{position:absolute;left:16px;bottom:16px;padding:10px 14px!important;background:#fff!important;border:0!important}
#bkd .bkd-places{margin:34px 0 0!important;border-top:1px solid var(--line)}
#bkd .bkd-places li{display:flex;justify-content:space-between;align-items:baseline;gap:20px;padding:16px 0;border-bottom:1px solid var(--line);line-height:1.5}
#bkd .bkd-places b{font-weight:500;color:var(--ink)}
#bkd .bkd-places span{max-width:58%;font-size:14px;color:var(--slate);text-align:right}
/* Why */
#bkd .bkd-why{display:grid;border-top:1px solid var(--navy-line)}
#bkd .bkd-why>div{padding:32px 0;border-bottom:1px solid var(--navy-line)}
#bkd .bkd-why b{display:block;margin-bottom:14px;font-family:var(--display)!important;font-size:46px;font-style:italic;font-weight:var(--display-w);line-height:1;letter-spacing:0;color:var(--brass-l)}
#bkd .bkd-why h3{margin-bottom:10px!important}
#bkd .bkd-why p{font-size:15px;line-height:1.65;color:rgba(255,255,255,.7)}
@media (min-width:900px){#bkd .bkd-why{grid-template-columns:repeat(3,minmax(0,1fr));column-gap:56px}}
/* Cards */
#bkd .bkd-cards{display:grid;gap:24px;grid-auto-flow:column;grid-auto-columns:minmax(240px,78%);overflow-x:auto;scroll-snap-type:x mandatory;padding-bottom:10px;scrollbar-width:thin}
@media (min-width:640px){#bkd .bkd-cards{grid-auto-columns:minmax(240px,44%)}}
@media (min-width:1000px){#bkd .bkd-cards{grid-auto-flow:row;grid-template-columns:repeat(var(--n,4),minmax(0,1fr));overflow:visible}}
#bkd .bkd-card{display:block;scroll-snap-align:start;min-width:0}
#bkd .bkd-card-img{position:relative;aspect-ratio:4/3;overflow:hidden;margin-bottom:16px;background:var(--line)}
#bkd .bkd-card-img img{position:absolute;inset:0;width:100%!important;height:100%!important;max-width:none!important;object-fit:cover;transition:transform .8s ease}
#bkd .bkd-card:hover .bkd-card-img img{transform:scale(1.04)}
#bkd .bkd-card h3{font-size:17px!important}
#bkd .bkd-card b{display:block;margin-top:4px;font-size:15px;font-weight:600;color:var(--navy)}
#bkd .bkd-card span{display:block;margin-top:2px;font-size:14px;color:var(--slate)}
/* FAQ */
#bkd .bkd-faq{border-top:1px solid var(--line)}
#bkd .bkd-faq details{border-bottom:1px solid var(--line)}
#bkd .bkd-faq summary{display:flex;justify-content:space-between;align-items:flex-start;gap:24px;padding:22px 0;cursor:pointer;list-style:none;font-size:17px;font-weight:500;line-height:1.45;color:var(--navy)}
#bkd .bkd-faq summary::-webkit-details-marker{display:none}
#bkd .bkd-faq summary::after{content:"";flex:none;width:12px;height:12px;margin-top:7px;background:linear-gradient(var(--brass-d),var(--brass-d)) center/12px 1.5px no-repeat,linear-gradient(var(--brass-d),var(--brass-d)) center/1.5px 12px no-repeat;transition:transform .25s}
#bkd .bkd-faq details[open] summary::after{transform:rotate(45deg)}
#bkd .bkd-faq-a{padding:0 0 24px;max-width:640px;color:var(--slate)}
#bkd .bkd-faq-a>*+*{margin-top:10px}
#bkd .bkd-faq-a a{color:var(--navy);text-decoration:underline}
#bkd .bkd-faq-a ul{padding-left:20px;list-style:disc}
/* Enquire */
#bkd .bkd-agent{display:flex;align-items:center;gap:18px;margin-top:36px;padding-top:30px;border-top:1px solid var(--line-2)}
#bkd .bkd-agent img,#bkd .bkd-av{width:64px!important;height:64px!important;flex:none;border-radius:50%;object-fit:cover;background:var(--line)}
#bkd .bkd-av{display:grid;place-items:center;font-weight:600;color:var(--navy)}
#bkd .bkd-agent b{display:block;color:var(--navy);font-weight:600}
#bkd .bkd-agent span{display:block;font-size:14px;line-height:1.5;color:var(--slate)}
#bkd .bkd-agent .bkd-inline{font-size:13px}
#bkd .bkd-direct{display:grid;gap:14px;margin-top:26px}
#bkd .bkd-direct a{display:inline-flex;align-items:center;gap:14px;font-size:15px;font-weight:500;color:var(--navy)}
#bkd .bkd-direct a:hover{text-decoration:underline;text-underline-offset:4px}
#bkd .bkd-direct .bkd-i:not(.bkd-wa-ic){color:var(--slate)}
#bkd .bkd-formcard{padding:clamp(24px,4vw,48px);background:#fff;border:1px solid var(--line)}
#bkd .bkd-formcard .bkd-form{gap:22px}
#bkd .bkd-disc{margin-top:clamp(56px,7vw,88px);padding-top:28px;border-top:1px solid var(--line-2);max-width:920px;font-size:12px;line-height:1.7;color:var(--muted)}
/* Mobile bar */
#bkd .bkd-mb{position:fixed;left:0;right:0;bottom:0;z-index:990;display:grid;grid-template-columns:52px 1fr;gap:10px;padding:8px 16px calc(8px + env(safe-area-inset-bottom));background:#fff;border-top:1px solid var(--line);box-shadow:0 -6px 18px rgba(13,36,64,.08);transform:translateY(110%);transition:transform .3s}
#bkd .bkd-mb.on{transform:none}
#bkd .bkd-mb-wa{display:grid;place-items:center;min-height:52px;border:1px solid var(--line-2)}
#bkd .bkd-mb-wa .bkd-i{width:24px;height:24px}
@media (min-width:1000px){#bkd .bkd-mb{display:none}}
/* Enquiry dialog */
#bkd .bkd-md{position:fixed;inset:0;z-index:999992;display:grid;align-items:end;background:rgba(13,36,64,.5)}
@media (min-width:700px){#bkd .bkd-md{place-items:center;padding:24px}}
#bkd .bkd-md-box{position:relative;width:100%;max-width:540px;max-height:94vh;overflow:auto;padding:40px 24px calc(28px + env(safe-area-inset-bottom));background:#fff;animation:bkdSheet .32s cubic-bezier(.2,.7,.2,1)}
@media (min-width:700px){#bkd .bkd-md-box{padding:52px 48px 44px;animation-name:bkdPop}}
@keyframes bkdSheet{from{transform:translateY(40px);opacity:0}to{transform:none;opacity:1}}
@keyframes bkdPop{from{transform:translateY(12px) scale(.98);opacity:0}to{transform:none;opacity:1}}
#bkd .bkd-md-x{position:absolute;top:12px;right:12px;display:grid;place-items:center;width:44px;height:44px;margin:0;padding:0!important;border:0!important;background:none!important;box-shadow:none!important;color:var(--navy)!important;cursor:pointer}
#bkd .bkd-md-x .bkd-i{width:22px;height:22px}
#bkd .bkd-md .bkd-h2{font-size:clamp(32px,3.4vw,40px)!important}
#bkd .bkd-md-sub{margin:10px 0 24px;color:var(--slate)}
html.bkd-lock,html.bkd-lock body{overflow:hidden}
html.bkd-mb-on #zsiq_float{bottom:calc(84px + env(safe-area-inset-bottom))!important;transition:bottom .3s}
html.bkd-lock #zsiq_float{display:none!important}
/* Gallery viewer */
#bkd .bkd-lb{position:fixed;inset:0;z-index:999993;display:grid;grid-template-rows:auto 1fr auto;background:rgba(8,18,32,.97);color:#fff}
#bkd .bkd-lb-top{display:flex;justify-content:space-between;align-items:center;padding:12px 16px;font-size:13px;letter-spacing:.06em}
#bkd .bkd-lb-stage{position:relative;display:grid;place-items:center;min-height:0;padding:0 56px}
#bkd .bkd-lb-stage img{max-width:100%;max-height:100%;object-fit:contain}
#bkd .bkd-lb button{margin:0;padding:0!important;border:0!important;border-radius:0!important;background:rgba(255,255,255,.1)!important;color:#fff!important;box-shadow:none!important;cursor:pointer}
#bkd .bkd-lb-x{display:grid;place-items:center;width:44px;height:44px}
#bkd .bkd-lb-prev,#bkd .bkd-lb-next{position:absolute;top:50%;transform:translateY(-50%);width:44px;height:56px;font-size:22px!important}
#bkd .bkd-lb-prev{left:6px}
#bkd .bkd-lb-next{right:6px}
#bkd .bkd-lb-strip{display:flex;gap:6px;overflow-x:auto;padding:12px 16px 18px}
#bkd .bkd-lb-strip button{flex:none;width:76px;height:54px;overflow:hidden;opacity:.45}
#bkd .bkd-lb-strip button.on{opacity:1;outline:2px solid #fff;outline-offset:-2px}
#bkd .bkd-lb-strip img{width:100%;height:100%;object-fit:cover}
/* Placeholder */
#bkd .bkd-ph{display:grid;place-items:center;align-content:center;width:100%;height:100%;padding:24px;background:linear-gradient(145deg,var(--navy) 0%,var(--navy-2) 100%);text-align:center}
#bkd .bkd-ph b{display:block;font-family:var(--display)!important;font-size:clamp(26px,3.4vw,46px);font-weight:var(--display-w);letter-spacing:var(--display-ls);line-height:1.05;color:#fff}
#bkd .bkd-ph span{display:block;margin-top:12px;font-size:10px;font-weight:600;letter-spacing:.22em;text-transform:uppercase;color:var(--brass)}
/* Reveal */
#bkd.bkd-js .bkd-rv{opacity:0;transform:translateY(18px);transition:opacity .9s cubic-bezier(.2,.7,.2,1),transform .9s cubic-bezier(.2,.7,.2,1)}
#bkd.bkd-js .bkd-rv.in{opacity:1;transform:none}
@media (prefers-reduced-motion:reduce){#bkd *,#bkd *::before,#bkd *::after{animation:none!important;transition:none!important}#bkd.bkd-js .bkd-rv{opacity:1;transform:none}}
@media (max-width:379px){#bkd .bkd-btn{padding:0 16px!important;letter-spacing:.08em!important}}
@media print{#bkd .bkd-subnav,#bkd .bkd-mb,#bkd .bkd-md,#bkd .bkd-lb,#bkd .bkd-quick,#bkd .bkd-formcard,#bkd .bkd-map{display:none!important}#bkd .bkd-rv{opacity:1!important;transform:none!important}}
CSS;
	}

	/** Extra styles on ad landing pages: their own header and footer, and a phone-first hero. */
	function blockke_dev_lp_css() {
		return <<<'CSS'
html body.bkd-lp{margin:0!important;padding:0!important;background:#FBF9F6!important}
body.bkd-lp #bkd{margin:0;width:100%}
#bkd .bkd-lph{position:relative;z-index:45;background:#fff;border-bottom:1px solid var(--line)}
#bkd .bkd-lph-in{display:flex;align-items:center;justify-content:space-between;gap:16px;height:64px}
#bkd .bkd-lph-logo{display:block;width:auto!important;max-width:58vw;height:26px!important;object-fit:contain;object-position:left center}
#bkd .bkd-lph-r{display:flex;align-items:center;gap:8px}
#bkd .bkd-lph-a{display:inline-flex;align-items:center;justify-content:center;gap:9px;min-width:44px;height:44px;border:1px solid var(--line-2);border-radius:50%;color:var(--navy);font-size:14px;font-weight:600;white-space:nowrap}
#bkd .bkd-lph-a span,#bkd .bkd-lph-cta{display:none}
#bkd .bkd-lph-a .bkd-i{width:19px;height:19px}
#bkd .bkd-lph-a:hover{border-color:var(--navy)}
@media (min-width:1000px){#bkd .bkd-lph-in{height:80px}#bkd .bkd-lph-logo{height:30px!important}#bkd .bkd-lph-r{gap:26px}#bkd .bkd-lph-a{min-width:0;height:auto;border:0;border-radius:0}#bkd .bkd-lph-a span{display:inline}#bkd .bkd-lph-a:hover span{text-decoration:underline;text-underline-offset:4px}#bkd .bkd-lph-cta{display:inline-flex}}
#bkd .bkd-qa{display:flex;align-items:center;gap:12px;margin-top:20px;padding-top:18px;border-top:1px solid rgba(255,255,255,.16);font-size:13px;line-height:1.4;color:var(--on-navy)}
#bkd .bkd-qa img,#bkd .bkd-qa-av{flex:none;width:40px!important;height:40px!important;border-radius:50%;object-fit:cover;background:var(--navy-2)}
#bkd .bkd-qa-av{display:grid;place-items:center;font-weight:600;color:#fff}
#bkd .bkd-qa b{display:block;font-weight:600;color:#fff}
#bkd .bkd-lpf{padding:34px 0 calc(110px + env(safe-area-inset-bottom));background:var(--navy);color:var(--on-navy);font-size:13px;line-height:1.7}
#bkd .bkd-lpf p{display:flex;flex-wrap:wrap;gap:4px 18px;color:var(--on-navy)}
#bkd .bkd-lpf p+p{margin-top:6px}
#bkd .bkd-lpf b{color:#fff;font-weight:600}
#bkd .bkd-lpf a{color:#fff;text-decoration:underline;text-underline-offset:3px}
@media (min-width:1000px){#bkd .bkd-lpf{padding-bottom:34px}}
#bkd .bkd-hero-offer,#bkd .bkd-hfacts-cover{display:none}
@media (max-width:999px){
#bkd.bkd-lpm .bkd-hero{padding-top:0}
#bkd.bkd-lpm .bkd-hero-grid{grid-template-areas:"top" "body";row-gap:0}
#bkd.bkd-lpm .bkd-hero-head,#bkd.bkd-lpm .bkd-hero-media{grid-area:top}
#bkd.bkd-lpm .bkd-hero-media{aspect-ratio:auto;min-height:clamp(306px,85vw,500px)}
#bkd.bkd-lpm .bkd-hero-media::after{content:"";position:absolute;inset:0;background:linear-gradient(180deg,rgba(13,36,64,0) 24%,rgba(13,36,64,.5) 52%,rgba(13,36,64,.94) 88%,#0D2440 100%)}
#bkd.bkd-lpm .bkd-hero-media figcaption{left:auto;right:16px;top:14px;bottom:auto;z-index:1}
#bkd.bkd-lpm .bkd-all{display:none}
#bkd.bkd-lpm .bkd-hero-head{position:relative;z-index:2;align-self:end;padding:0 0 18px;pointer-events:none}
#bkd.bkd-lpm .bkd-status{margin-bottom:10px;color:rgba(255,255,255,.88)}
#bkd.bkd-lpm .bkd-status b{background:rgba(255,255,255,.16);color:#fff;-webkit-backdrop-filter:blur(6px);backdrop-filter:blur(6px)}
#bkd.bkd-lpm .bkd-status b::before{background:var(--brass-l)}
#bkd.bkd-lpm .bkd-h1{color:#fff!important;font-size:clamp(40px,11vw,60px)!important;text-shadow:0 2px 24px rgba(0,0,0,.25)}
#bkd.bkd-lpm .bkd-hero-tag{display:none}
#bkd.bkd-lpm .bkd-hero-offer{display:block;margin:8px 0 0;font-size:15px;line-height:1.45;color:rgba(255,255,255,.9)}
#bkd.bkd-lpm .bkd-hero-offer span{display:inline-block}
#bkd.bkd-lpm .bkd-hfacts-cover{display:grid;margin:16px 0 0;border-top:1px solid rgba(255,255,255,.24);border-bottom:0}
#bkd.bkd-lpm .bkd-hfacts-cover>div{padding:12px 0 0}
#bkd.bkd-lpm .bkd-hfacts-cover>div+div{padding-left:14px;border-left-color:rgba(255,255,255,.24)}
#bkd.bkd-lpm .bkd-hfacts-cover dt{color:rgba(255,255,255,.72)}
#bkd.bkd-lpm .bkd-hfacts-cover dd{color:#fff;font-size:clamp(21px,6.2vw,30px)}
#bkd.bkd-lpm .bkd-hero-body{padding-top:0}
#bkd.bkd-lpm .bkd-hero-body>.bkd-hfacts{display:none}
#bkd.bkd-lpm .bkd-quick{margin:0 calc(var(--gutter) * -1);padding:20px var(--gutter) 24px}
}
@media (min-width:360px) and (max-width:559px){#bkd.bkd-lpm .bkd-quick .bkd-qgrid{grid-template-columns:1fr 1fr;gap:12px}#bkd.bkd-lpm .bkd-quick .bkd-f>input{padding:0 12px}}
CSS;
	}

	/* =====================================================================
	   2) Development layout: script
	   ===================================================================== */

	function blockke_dev_js() {
		return <<<'JS'
(function(){
  'use strict';
  var C=window.BKDEV,root=document.getElementById('bkd');if(!C||!root){return;}
  root.classList.add('bkd-js');
  var $=function(s,r){return (r||root).querySelector(s);},$$=function(s,r){return Array.prototype.slice.call((r||root).querySelectorAll(s));};
  var dl=(window.dataLayer=window.dataLayer||[]);
  var reduce=window.matchMedia&&window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  var esc=function(s){return String(s==null?'':s).replace(/[&<>"']/g,function(c){return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c];});};
  var digits=function(s){return String(s||'').replace(/\D/g,'');};
  var html=document.documentElement;

  /* full-bleed width without the scrollbar; offset for fixed bars (admin bar, sticky site header) */
  function sizes(){root.style.setProperty('--bkd-vw',html.clientWidth+'px');}
  sizes();window.addEventListener('resize',sizes);
  var lastTop=-1;
  function topOffset(){
    var h=0,x=Math.round(window.innerWidth/2),seen=[];
    try{
      var els=document.elementsFromPoint(x,2);
      for(var i=0;i<els.length;i++){
        var el=els[i];
        while(el&&el!==document.body&&el!==html){
          if(seen.indexOf(el)<0){seen.push(el);
            if(!root.contains(el)){var cs=getComputedStyle(el);
              if(cs.position==='fixed'||cs.position==='sticky'){var r=el.getBoundingClientRect();if(r.top<=2&&r.height<220&&r.bottom>h){h=r.bottom;}}}}
          el=el.parentElement;
        }
      }
    }catch(e){}
    h=Math.round(h);if(h!==lastTop){lastTop=h;root.style.setProperty('--top',h+'px');}
  }

  /* images that fail: branded placeholder */
  $$('img').forEach(function(im){
    im.addEventListener('error',function(){var b=im.parentNode;if(!b||im.getAttribute('data-bkd-failed')){return;}im.setAttribute('data-bkd-failed','1');
      if(b.closest('.bkd-agent')){im.outerHTML='<div class="bkd-av">'+esc((C.agent||'B').charAt(0))+'</div>';return;}
      im.style.display='none';b.insertAdjacentHTML('afterbegin','<div class="bkd-ph"><b>'+esc(C.name)+'</b><span>Images on request</span></div>');},{once:true});
  });

  /* attribution (the site-wide keeper stores it in sessionStorage blkbp_attr) */
  function attr(){
    var a={};try{a=JSON.parse(sessionStorage.getItem('blkbp_attr')||'{}');}catch(e){a={};}
    try{var q=new URLSearchParams(location.search);['utm_source','utm_medium','utm_campaign','utm_term','utm_content','gclid','fbclid','gbraid','wbraid'].forEach(function(k){var v=q.get(k);if(v&&!a[k]){a[k]=v.slice(0,120);}});}catch(e){}
    if(!a.land){a.land=location.href.slice(0,250);a.ref=(document.referrer||'').slice(0,200);}
    return a;
  }
  function track(name,extra){var p=extra||{};p.development=C.slug;p.listing_id=String(C.id);
    try{if(typeof window.gtag==='function'){window.gtag('event',name,p);}else{p.event=name;dl.push(p);}}catch(e){}}
  $$('[data-bkd-wa]').forEach(function(a){a.addEventListener('click',function(){track('click_whatsapp');});});
  $$('a[href^="tel:"]').forEach(function(a){a.addEventListener('click',function(){track('click_call');});});

  /* lead forms */
  var remembered={};try{remembered=JSON.parse(sessionStorage.getItem('bkd_lead')||'{}');}catch(e){remembered={};}
  function prefill(form){['name','phone','email'].forEach(function(k){var i=form.querySelector('[name="'+k+'"]');if(i&&!i.value&&remembered[k]){i.value=remembered[k];}});}
  function icon(n,cls){return '<svg class="bkd-i'+(n==='wa'?' bkd-i-f':'')+(cls?' '+cls:'')+'" aria-hidden="true"><use href="#bkd-i-'+n+'"></use></svg>';}
  function waUrl(text){return 'https://wa.me/'+C.wa+'?text='+encodeURIComponent(text);}
  function leadText(p){return ['Hi '+C.brand+", I've just sent an enquiry about "+C.name+' on block.ke.',p.unit?'Interested in: '+p.unit:'','Name: '+p.name,C.url].filter(Boolean).join('\n');}
  function success(form,p,j){
    var ok=form.nextElementSibling,first=(p.name||'').trim().split(/\s+/)[0];
    ok.querySelector('[data-first]').textContent=first?', '+first:'';
    ok.querySelector('[data-okmsg]').textContent='An advisor will be in touch shortly'+(j&&j.phone_display?' on '+j.phone_display:'')+' with the price list and floor plans for '+C.name+'.';
    ok.querySelector('[data-wa-ok]').href=waUrl(leadText(p));
    var ab=ok.querySelector('[data-asset]');
    if(j&&j.asset){ab.href=j.asset;ab.hidden=false;ab.innerHTML='Download the brochure'+icon('arrow');ab.addEventListener('click',function(){track('asset_open');},{once:true});}else{ab.hidden=true;}
    var mf=ok.querySelector('[data-more]');
    if(mf){$$('input',mf).forEach(function(i){i.checked=false;});mf.setAttribute('data-ref',(j&&j.ref)||'');mf.hidden=!(j&&j.ref);
      $$('fieldset,.bkd-more-t,[data-more-send]',mf).forEach(function(x){x.hidden=false;});mf.querySelector('[data-more-send]').disabled=false;
      var dn=mf.querySelector('.bkd-more-done');dn.hidden=true;dn.textContent='';
      var mu=mf.querySelector('[data-more-unit]');if(mu){mu.hidden=!!p.unit;}}
    form.hidden=true;ok.hidden=false;try{ok.focus({preventScroll:true});}catch(e){}
  }
  /* the optional questions after a quick enquiry: added to the same lead */
  function sendMore(mf){
    var q={more:mf.getAttribute('data-ref')||'',listing:C.id,unit:'',timeline:'',purpose:''};
    $$('input:checked',mf).forEach(function(i){q[i.name.split('-')[0]]=i.value;});
    if(!q.unit&&!q.timeline&&!q.purpose){var r0=$$('input',mf).filter(function(i){return i.offsetParent!==null;})[0];if(r0){r0.focus();}return;}
    mf.querySelector('[data-more-send]').disabled=true;
    function done(msg){$$('fieldset,.bkd-more-t,[data-more-send]',mf).forEach(function(x){x.hidden=true;});var dn=mf.querySelector('.bkd-more-done');dn.textContent=msg;dn.hidden=false;}
    fetch(C.endpoint,{method:'POST',headers:{'Content-Type':'application/json',Accept:'application/json'},body:JSON.stringify(q),credentials:'same-origin'})
      .then(function(r){return r.json().catch(function(){return {};}).then(function(j){if(!r.ok||!j||!j.ok){throw new Error('fail');}});})
      .then(function(){track('lead_details',{lead_unit:q.unit,timeline:q.timeline,purpose:q.purpose});done('Thank you, noted. Your advisor will have this before they get in touch.');})
      .catch(function(){done('Those answers did not go through, but your enquiry is with us.');});
  }
  function fail(form,p,msg,code){
    var btn=form.querySelector('[type=submit]');btn.disabled=false;
    var al=form.querySelector('.bkd-alert')||document.createElement('div');
    al.className='bkd-alert';al.setAttribute('role','alert');
    al.innerHTML=(msg?esc(msg)+' ':"Sorry, that didn't go through"+(code?' (error '+esc(code)+')':'')+'. ')+'<a target="_blank" rel="noopener" href="'+esc(waUrl(leadText(p)))+'">Send it on WhatsApp instead</a>.';
    if(!al.parentNode){btn.insertAdjacentElement('beforebegin',al);}
  }
  root.addEventListener('click',function(e){var s=e.target.closest&&e.target.closest('[data-more-send]');if(s){sendMore(s.closest('[data-more]'));}});
  root.addEventListener('submit',function(e){
    var form=e.target.closest&&e.target.closest('form[data-bkd-lead]');if(!form){return;}
    e.preventDefault();
    var fd=new FormData(form),d={};fd.forEach(function(v,k){d[k]=typeof v==='string'?v.trim():v;});
    var errs={};
    if(!d.name||d.name.length<2){errs.name='Please enter your name.';}
    var n=digits(d.phone);if(n.length<9||n.length>15){errs.phone='Please enter a valid phone number.';}
    if(d.email&&!/^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(d.email)){errs.email='Please check your email address.';}
    $$('[data-f]',form).forEach(function(w){var k=w.getAttribute('data-f');w.classList.toggle('bkd-bad',!!errs[k]);var er=w.querySelector('.bkd-err');if(er){er.textContent=errs[k]||'';}});
    var firstErr=Object.keys(errs)[0];if(firstErr){form.querySelector('[name="'+firstErr+'"]').focus();return;}
    var p={listing:C.id,name:d.name,phone:d.phone,email:d.email||'',unit:d.unit&&d.unit!=='Not sure yet'?d.unit:(form.getAttribute('data-unit')||''),purpose:d.purpose||'',timeline:d.timeline||'',contact:d.contact||'',message:d.message||'',
      source:form.getAttribute('data-bkd-lead'),intent:form.getAttribute('data-intent')||'',page:location.href.slice(0,300),attr:attr(),hp:d.bkd_hp||''};
    remembered={name:d.name,phone:d.phone,email:d.email||remembered.email||''};try{sessionStorage.setItem('bkd_lead',JSON.stringify(remembered));}catch(er){}
    var btn=form.querySelector('[type=submit]');btn.disabled=true;
    var al=form.querySelector('.bkd-alert');if(al){al.remove();}
    fetch(C.endpoint,{method:'POST',headers:{'Content-Type':'application/json',Accept:'application/json'},body:JSON.stringify(p),credentials:'same-origin'})
      .then(function(r){return r.json().catch(function(){return {};}).then(function(j){if(!r.ok||!j||!j.ok){var x=new Error('fail');x.msg=j&&j.ok===false&&j.message;x.code=r.status;throw x;}return j;});})
      .then(function(j){
        track('generate_lead',{lead_source:p.source,lead_unit:p.unit});
        try{if(typeof window.blockkeLeadConversion==='function'){window.blockkeLeadConversion('lead_form');}}catch(x){}
        try{if(typeof window.fbq==='function'){window.fbq('track','Lead',{content_name:C.name});}}catch(x){}
        success(form,p,j);
      })
      .catch(function(err){fail(form,p,err&&err.msg,err&&err.code);});
  });
  $$('form[data-bkd-lead] input').forEach(function(i){i.addEventListener('input',function(){var w=i.closest('[data-f]');if(w){w.classList.remove('bkd-bad');var er=w.querySelector('.bkd-err');if(er){er.textContent='';}}});});
  /* visitors abroad see an international example in the phone fields */
  try{var tz=Intl.DateTimeFormat().resolvedOptions().timeZone||'';
    if(tz&&tz!=='Africa/Nairobi'){
      var ph={'Europe/London':'+44 7700 900123','Europe/Dublin':'+353 85 123 4567','Europe/Berlin':'+49 151 23456789','Europe/Amsterdam':'+31 6 12345678','Europe/Paris':'+33 6 12 34 56 78','Europe/Stockholm':'+46 70 123 4567','Europe/Oslo':'+47 412 34 567','Europe/Zurich':'+41 78 123 45 67',
        'Asia/Dubai':'+971 50 123 4567','Asia/Qatar':'+974 3312 3456','Asia/Riyadh':'+966 50 123 4567','Asia/Kolkata':'+91 98765 43210','Africa/Kampala':'+256 772 123456','Africa/Dar_es_Salaam':'+255 712 345 678','Africa/Kigali':'+250 788 123 456',
        'Africa/Addis_Ababa':'+251 91 123 4567','Africa/Lagos':'+234 802 123 4567','Africa/Johannesburg':'+27 82 123 4567'}[tz]
        ||(/^America\/(New_York|Chicago|Denver|Los_Angeles|Phoenix|Anchorage|Detroit|Indiana|Kentucky|Boise|Toronto|Vancouver|Edmonton|Winnipeg|Halifax|Regina)/.test(tz)?'+1 202 555 0123':(/^Australia\//.test(tz)?'+61 412 345 678':'+ code and number'));
      $$('input[name="phone"]').forEach(function(i){i.placeholder=ph;});
    }}catch(e){}

  /* enquiry dialog */
  var md=document.getElementById('bkd-md'),mdForm=document.getElementById('bkd-md-form'),lastFocus=null;
  function lock(on){html.classList.toggle('bkd-lock',on);}
  function openModal(source,unit){
    var f=$('form',mdForm),ok=$('.bkd-ok',mdForm);f.hidden=false;ok.hidden=true;
    var b=f.querySelector('[type=submit]');b.disabled=false;var al=f.querySelector('.bkd-alert');if(al){al.remove();}
    f.setAttribute('data-unit',unit||'');f.setAttribute('data-intent',source||'');
    var titles={'payment-plan':'Get the payment plan',mortgage:'Talk to us about financing',brochure:'Download the brochure',comparables:'Get rental and resale comparables'};
    document.getElementById('bkd-md-t').textContent=unit?'Floor plan and price':(titles[source]||'Get the price list');
    document.getElementById('bkd-md-sub').textContent=unit?unit+' at '+C.name+'. Sent to your WhatsApp.':(source==='brochure'?'Leave your details and the brochure opens straight away.':(source==='mortgage'?'We will compare mortgage options for you.':(source==='comparables'?'Recent rents and resale prices near '+C.name+', sent to your WhatsApp.':'Floor plans and the payment plan, sent to your WhatsApp.')));
    prefill(f);lastFocus=document.activeElement;md.hidden=false;lock(true);
    var first=f.querySelector('input[name="name"]');setTimeout(function(){(first.value?f.querySelector('[type=submit]'):first).focus();},30);
    track('enquiry_open',{source:source,unit:unit||''});
  }
  function closeModal(){md.hidden=true;lock(false);if(lastFocus){lastFocus.focus();}}
  root.addEventListener('click',function(e){
    var t=e.target.closest&&e.target.closest('[data-bkd-enquire]');
    if(t){e.preventDefault();openModal(t.getAttribute('data-bkd-enquire'),t.getAttribute('data-unit'));return;}
    if(e.target===md||(e.target.closest&&e.target.closest('[data-bkd-close]'))){closeModal();}
  });

  /* share */
  var sh=$('[data-bkd-share]');
  if(sh){sh.addEventListener('click',function(){
    var data={title:C.title,text:C.name,url:C.url};
    if(navigator.share){navigator.share(data).catch(function(){});track('share',{method:'native'});return;}
    var done=function(){var s=sh.querySelector('span');var t=s.textContent;s.textContent='Link copied';setTimeout(function(){s.textContent=t;},2000);};
    if(navigator.clipboard){navigator.clipboard.writeText(C.url).then(done,function(){window.prompt('Copy this link',C.url);});}else{window.prompt('Copy this link',C.url);}
    track('share',{method:'copy'});
  });}

  /* gallery viewer */
  var G=C.gallery||[],lb=document.getElementById('bkd-lb'),lbImg=document.getElementById('bkd-lb-img'),strip=document.getElementById('bkd-lb-strip'),cur=0,lbFocus=null,built=false;
  function build(){if(built){return;}built=true;G.forEach(function(p,i){var b=document.createElement('button');b.type='button';b.setAttribute('aria-label','Photo '+(i+1));b.innerHTML='<img loading="lazy" alt="" src="'+esc(p[2]||p[0])+'">';b.addEventListener('click',function(){show(i);});strip.appendChild(b);});}
  function show(i){if(!G.length){return;}cur=(i+G.length)%G.length;lbImg.src=G[cur][0];lbImg.alt=G[cur][1]||'';document.getElementById('bkd-lb-n').textContent=(cur+1)+' / '+G.length+(G[cur][3]?'  ·  '+G[cur][3]:'');
    $$('button',strip).forEach(function(b,j){b.classList.toggle('on',j===cur);});if(strip.children[cur]){strip.children[cur].scrollIntoView({block:'nearest',inline:'center'});}}
  function openLb(i){if(!G.length){return;}build();lbFocus=document.activeElement;lb.hidden=false;lock(true);show(i);lb.querySelector('.bkd-lb-x').focus();track('gallery_open');}
  function closeLb(){lb.hidden=true;lock(false);if(lbFocus){lbFocus.focus();}}
  root.addEventListener('click',function(e){var o=e.target.closest&&e.target.closest('[data-bkd-open]');if(o){openLb(+o.getAttribute('data-bkd-open'));}if(e.target.closest&&e.target.closest('[data-bkd-lbclose]')){closeLb();}});
  if(lb){lb.querySelector('.bkd-lb-prev').addEventListener('click',function(){show(cur-1);});lb.querySelector('.bkd-lb-next').addEventListener('click',function(){show(cur+1);});}
  document.addEventListener('keydown',function(e){
    if(lb&&!lb.hidden){if(e.key==='Escape'){closeLb();}if(e.key==='ArrowLeft'){show(cur-1);}if(e.key==='ArrowRight'){show(cur+1);}return;}
    if(md&&!md.hidden){
      if(e.key==='Escape'){closeModal();return;}
      if(e.key==='Tab'){var f=$$('button, input, a[href], textarea',md).filter(function(x){return x.offsetParent!==null&&!x.classList.contains('bkd-hp')&&x.tabIndex!==-1;});
        if(!f.length){return;}if(e.shiftKey&&document.activeElement===f[0]){e.preventDefault();f[f.length-1].focus();}else if(!e.shiftKey&&document.activeElement===f[f.length-1]){e.preventDefault();f[0].focus();}}
    }
  });

  /* video and virtual tour */
  $$('[data-bkd-video] button').forEach(function(b){b.addEventListener('click',function(){
    var box=b.parentNode,t=box.getAttribute('data-bkd-video'),id=box.getAttribute('data-id'),src;
    if(t==='youtube'){src='https://www.youtube-nocookie.com/embed/'+encodeURIComponent(id)+'?autoplay=1&rel=0';}
    else if(t==='vimeo'){src='https://player.vimeo.com/video/'+encodeURIComponent(id)+'?autoplay=1';}
    else{src=id;}
    box.innerHTML='<iframe src="'+esc(src)+'" title="'+esc(C.name)+'" allow="autoplay; fullscreen; encrypted-media; picture-in-picture; xr-spatial-tracking; gyroscope; accelerometer" allowfullscreen></iframe>';
    track(t==='tour'?'tour_open':'video_play');
  });});

  /* residence filter */
  var rows=$$('.bkd-tbl tbody tr');
  $$('.bkd-filters button').forEach(function(b){b.addEventListener('click',function(){
    $$('.bkd-filters button').forEach(function(x){x.setAttribute('aria-pressed',String(x===b));});
    rows.forEach(function(r){r.hidden=b.getAttribute('data-g')!=='*'&&r.getAttribute('data-g')!==b.getAttribute('data-g');});
  });});

  /* mobile bar, section nav highlight */
  var mb=document.getElementById('bkd-mb'),quick=document.getElementById('bkd-quick'),enquire=document.getElementById('bkd-enquire');
  var links=$$('.bkd-subnav-links a'),targets=links.map(function(a){return document.getElementById(a.getAttribute('href').slice(1));});
  var themeBar=function(){var t=document.querySelector('.mobile_agent_area_wrapper');if(!t){return false;}var cs=getComputedStyle(t);return cs.display!=='none'&&cs.visibility!=='hidden';};
  var ticking=false,lastProbe=0;
  function onScroll(){
    ticking=false;
    var now=Date.now();if(now-lastProbe>250){lastProbe=now;topOffset();}
    var qb=quick?quick.getBoundingClientRect().bottom:0,eb=enquire?enquire.getBoundingClientRect():null;
    var inContact=eb&&eb.top<window.innerHeight*0.75&&eb.bottom>0;
    if(mb){var mbOn=qb<0&&!inContact&&!themeBar();mb.classList.toggle('on',mbOn);html.classList.toggle('bkd-mb-on',mbOn&&window.innerWidth<1000);}
    var on=-1,line=lastTop+120;
    targets.forEach(function(t,i){if(t&&t.getBoundingClientRect().top<line){on=i;}});
    links.forEach(function(a,i){a.classList.toggle('on',i===on);});
  }
  window.addEventListener('scroll',function(){if(!ticking){ticking=true;requestAnimationFrame(onScroll);}},{passive:true});
  window.addEventListener('resize',function(){lastProbe=0;onScroll();});
  onScroll();

  /* gentle reveal */
  var rv=$$('.bkd-rv');
  if('IntersectionObserver' in window&&!reduce){
    var io=new IntersectionObserver(function(es){es.forEach(function(en){if(en.isIntersecting){en.target.classList.add('in');io.unobserve(en.target);}});},{rootMargin:'0px 0px -6% 0px',threshold:0.06});
    rv.forEach(function(x){io.observe(x);});
  }else{rv.forEach(function(x){x.classList.add('in');});}
})();
JS;
	}

	/* =====================================================================
	   2) Development layout: when and how it renders
	   ===================================================================== */

	function blockke_dev_layout_on( $id ) {
		$cfg = blockke_dev_config();
		if ( empty( $cfg['enabled'] ) ) {
			return false;
		}
		$mode = (string) get_post_meta( $id, 'bke_dev_layout', true );
		if ( 'off' === $mode ) {
			return false;
		}
		if ( 'on' === $mode ) {
			return true;
		}
		return ! empty( $cfg['auto'] ) && '1' === (string) get_post_meta( $id, 'property_has_subunits', true );
	}

	function blockke_dev_should_render() {
		if ( is_admin() || is_feed() || is_embed() || ! is_singular( 'estate_property' ) ) {
			return false;
		}
		foreach ( array( 'elementor-preview', 'vcv-editable', 'vcv-source-id', 'print' ) as $k ) {
			if ( isset( $_GET[ $k ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
				return false;
			}
		}
		$id = get_queried_object_id();
		if ( isset( $_GET['bke_layout'] ) && current_user_can( 'edit_post', $id ) ) { // phpcs:ignore WordPress.Security.NonceVerification
			return '1' === (string) $_GET['bke_layout']; // phpcs:ignore WordPress.Security.NonceVerification
		}
		return blockke_dev_layout_on( $id );
	}

	function blockke_dev_count_view( $id ) {
		$cfg = blockke_dev_config();
		if ( empty( $cfg['count_views'] ) || is_preview() || ( is_user_logged_in() && current_user_can( 'edit_post', $id ) ) ) {
			return;
		}
		$total = (int) get_post_meta( $id, 'wpestate_total_views', true );
		update_post_meta( $id, 'wpestate_total_views', $total + 1 );
		$daily = get_post_meta( $id, 'wpestate_detailed_views', true );
		if ( ! is_array( $daily ) ) {
			$daily = array();
		}
		$day           = date_i18n( 'F j, Y' );
		$daily[ $day ] = ( isset( $daily[ $day ] ) ? (int) $daily[ $day ] : 0 ) + 1;
		update_post_meta( $id, 'wpestate_detailed_views', $daily );
	}

	add_filter(
		'template_include',
		function ( $template ) {
			$lp = blockke_dev_lp_target();
			if ( ! $lp && ! blockke_dev_should_render() ) {
				return $template;
			}
			$id   = $lp ? $lp : get_queried_object_id();
			$data = blockke_dev_data( $id );
			if ( ! $data ) {
				return $template;
			}
			$GLOBALS['blockke_dev_active'] = true;
			if ( empty( blockke_dev_config()['price_check_popup'] ) ) {
				$_GET['blockpop'] = '0'; // the popup snippet's own off switch; its attribution keeper still runs
			}
			add_filter(
				'body_class',
				function ( $c ) use ( $lp ) {
					$c[] = 'bkd-page';
					if ( $lp ) {
						$c[] = 'bkd-lp';
					}
					return $c;
				}
			);
			if ( $lp ) { // ad landing pages stay out of search results; the listing itself is the page to index
				add_filter(
					'wp_robots',
					function ( $r ) {
						unset( $r['index'] );
						$r['noindex'] = true;
						$r['follow']  = true;
						return $r;
					},
					99
				);
				add_filter(
					'rank_math/frontend/robots',
					function ( $r ) {
						$r['index']  = 'noindex';
						$r['follow'] = 'follow';
						return $r;
					},
					99
				);
			}
			add_action(
				'wp_head',
				function () use ( $lp ) {
					$font = blockke_dev_font_head( '#bkd' );
					if ( '' === $font[0] ) {
						$font[1] = '#bkd{--display:var(--font);--display-w:300;--display-ls:-.025em}#bkd .bkd-h1{font-size:clamp(38px,4.8vw,64px)!important}#bkd .bkd-h2{font-size:clamp(28px,3.3vw,44px)!important}';
					}
					echo $font[0] . '<style id="bkd-css">' . blockke_dev_css() . $font[1] . blockke_dev_calc_css() . ( $lp ? blockke_dev_lp_css() : '' ) . "</style>\n";
				},
				99
			);
			add_action(
				'wp_footer',
				function () use ( $data, $lp ) {
					$cfg     = blockke_dev_config();
					$gallery = array();
					foreach ( $data['images'] as $aid ) {
						$full = wp_get_attachment_image_url( $aid, '1536x1536' );
						if ( ! $full ) {
							$full = wp_get_attachment_url( $aid );
						}
						$gallery[] = array( $full, blockke_dev_alt( $aid, $data['short'] ), wp_get_attachment_image_url( $aid, 'thumbnail' ), isset( $data['captions'][ $aid ] ) ? $data['captions'][ $aid ] : '' );
					}
					$js = array(
						'id'       => $data['id'],
						'slug'     => $data['slug'],
						'name'     => $data['short'],
						'title'    => $data['title'],
						'url'      => $data['url'],
						'brand'    => $cfg['brand'],
						'agent'    => $data['agent']['name'],
						'wa'       => $data['agent']['wa'],
						'endpoint' => esc_url_raw( rest_url( 'block/v1/development-lead' ) ),
						'gallery'  => $gallery,
						'lp'       => $lp ? 1 : 0,
					);
					echo '<script id="bkd-cfg" data-cfasync="false" data-no-optimize="1" data-no-defer="1">window.BKDEV=' . wp_json_encode( $js, JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS ) . ";</script>\n";
					echo '<script id="bkd-js" data-cfasync="false" data-no-optimize="1" data-no-defer="1">' . blockke_dev_js() . "\n" . blockke_dev_calc_js() . "</script>\n";
				},
				20
			);
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
				echo blockke_dev_render( $data ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped while building.
				get_footer();
			}
			$blank = WP_CONTENT_DIR . '/index.php';
			return file_exists( $blank ) ? $blank : '';
		},
		99999
	);

	/* =====================================================================
	   Enquiries: POST /wp-json/block/v1/development-lead
	   ===================================================================== */

	/** Who gets the lead emails: the notify list (or the popup's), plus the listing's agent. */
	function blockke_dev_lead_to( $lid ) {
		$cfg = blockke_dev_config();
		$to  = array();
		foreach ( (array) $cfg['notify_emails'] as $em ) {
			$to[] = $em;
		}
		if ( ! $to && function_exists( 'blockke_bp_config' ) ) {
			$bp = blockke_bp_config();
			$to = (array) $bp['notify_emails'];
		}
		if ( ! $to ) {
			$to = array( 'loyd@block.ke' );
		}
		if ( ! empty( $cfg['copy_agent'] ) ) {
			$ag = blockke_dev_agent( (int) get_post_meta( $lid, 'property_agent', true ) );
			if ( $ag['email'] ) {
				$to[] = $ag['email'];
			}
		}
		return array_values( array_unique( array_filter( array_map( 'sanitize_email', $to ) ) ) );
	}

	/** Adds a lead to the top of the log (the last 200 are kept), or with $ref, updates that lead's fields. */
	function blockke_dev_save_lead( $fields, $ref = '' ) {
		$log = get_option( 'blockke_dev_leads', array() );
		if ( ! is_array( $log ) ) {
			$log = array();
		}
		if ( '' === $ref ) {
			array_unshift( $log, $fields );
		} else {
			foreach ( $log as $k => $row ) {
				if ( is_array( $row ) && isset( $row['ref'] ) && $row['ref'] === $ref ) {
					$log[ $k ] = array_merge( $row, $fields );
					break;
				}
			}
		}
		update_option( 'blockke_dev_leads', array_slice( $log, 0, 200 ), false );
	}

	/** Sends caught by the hidden anti-spam field: not emailed or sent to Zoho, but the last 30 are kept in case one was a person. */
	function blockke_dev_note_spam( $get ) {
		$log = get_option( 'blockke_dev_spam', array() );
		if ( ! is_array( $log ) ) {
			$log = array();
		}
		array_unshift(
			$log,
			array(
				'time'  => current_time( 'mysql' ),
				'name'  => $get( 'name', 80 ),
				'phone' => $get( 'phone', 40 ),
				'email' => $get( 'email', 120 ),
				'trap'  => mb_substr( $get( 'hp', 80 ) . $get( 'website', 80 ), 0, 80 ),
				'page'  => esc_url_raw( $get( 'page', 300 ) ),
			)
		);
		update_option( 'blockke_dev_spam', array_slice( $log, 0, 30 ), false );
	}

	/** The optional answers given after a quick enquiry: added to the stored lead (once, within a day) and emailed. */
	function blockke_dev_lead_more( $ref, $get ) {
		$log = get_option( 'blockke_dev_leads', array() );
		$key = null;
		foreach ( is_array( $log ) ? $log : array() as $k => $row ) {
			if ( ! empty( $row['ref'] ) && hash_equals( (string) $row['ref'], $ref ) ) {
				$key = $k;
				break;
			}
		}
		if ( null === $key || ! empty( $log[ $key ]['more'] ) || strtotime( $log[ $key ]['time'] ) < strtotime( current_time( 'mysql' ) ) - DAY_IN_SECONDS ) {
			return new WP_REST_Response( array( 'ok' => false, 'message' => 'Those answers could not be added, but your enquiry was received.' ), 400 );
		}
		$row      = $log[ $key ];
		$unit     = $get( 'unit', 160 );
		$timeline = blockke_dev_timeline( $get( 'timeline', 40 ) );
		$purpose  = in_array( $get( 'purpose', 20 ), array( 'Live in', 'Invest', 'Both' ), true ) ? $get( 'purpose', 20 ) : '';
		if ( '' === $unit . $timeline . $purpose ) {
			return new WP_REST_Response( array( 'ok' => true ), 200 );
		}
		$lines = array( 'MORE DETAILS ON A DEVELOPMENT ENQUIRY', $row['name'] . ' answered the optional questions after enquiring about ' . $row['dev'] . ' (' . $row['time'] . ').', '' );
		if ( $unit ) {
			$lines[] = 'Interested in: ' . $unit;
			if ( empty( $row['unit'] ) ) {
				$row['unit'] = $unit;
			}
		}
		if ( $timeline ) {
			$lines[]         = 'Timeline: ' . $timeline;
			$row['timeline'] = $timeline;
		}
		if ( $purpose ) {
			$lines[]        = 'Buying to: ' . $purpose;
			$row['purpose'] = $purpose;
		}
		$lines[]     = '';
		$lines[]     = 'Phone / WhatsApp: ' . blockke_dev_phone_display( $row['phone'] ) . '  (https://wa.me/' . $row['phone'] . ')';
		$lines[]     = 'Add these to the lead in Zoho.';
		$row['more'] = current_time( 'mysql' );
		$log[ $key ] = $row;
		update_option( 'blockke_dev_leads', $log, false );
		$to = blockke_dev_lead_to( (int) $row['listing'] );
		if ( $to ) {
			wp_mail( $to, sprintf( '[Block] More details: %s - %s', $row['name'], $row['dev'] ), implode( "\n", $lines ), array( 'Content-Type: text/plain; charset=UTF-8' ) );
		}
		return new WP_REST_Response( array( 'ok' => true ), 200 );
	}

	function blockke_dev_handle_lead( $req ) {
		$p = $req->get_json_params();
		if ( ! is_array( $p ) ) {
			$p = $req->get_params();
		}
		$get = function ( $k, $max = 200 ) use ( $p ) {
			return ( isset( $p[ $k ] ) && is_scalar( $p[ $k ] ) ) ? mb_substr( sanitize_text_field( (string) $p[ $k ] ), 0, $max ) : '';
		};
		$ip = '';
		foreach ( array( 'HTTP_CF_CONNECTING_IP', 'REMOTE_ADDR' ) as $h ) {
			if ( ! empty( $_SERVER[ $h ] ) ) {
				$ip = sanitize_text_field( wp_unslash( $_SERVER[ $h ] ) );
				break;
			}
		}
		$rk = 'bkd_rl_' . md5( $ip );
		$n  = (int) get_transient( $rk );
		if ( $n >= 8 ) {
			return new WP_REST_Response( array( 'ok' => false, 'message' => 'Too many requests from this connection. Please WhatsApp us directly.' ), 429 );
		}
		set_transient( $rk, $n + 1, HOUR_IN_SECONDS );

		// The hidden anti-spam field. Pages from before v1.3.1 named it "company", which browsers' AutoFill
		// fills in for real visitors, so that name is no longer treated as spam.
		if ( '' !== $get( 'hp' ) || '' !== $get( 'website' ) ) {
			blockke_dev_note_spam( $get );
			return new WP_REST_Response( array( 'ok' => true ), 200 ); // pretend, so bots learn nothing
		}

		$more = preg_replace( '/[^A-Za-z0-9]/', '', $get( 'more', 40 ) );
		if ( '' !== $more ) {
			return blockke_dev_lead_more( $more, $get );
		}

		$lid  = (int) $get( 'listing', 12 );
		$post = $lid ? get_post( $lid ) : null;
		if ( ! $post || 'estate_property' !== $post->post_type || 'publish' !== $post->post_status ) {
			return new WP_REST_Response( array( 'ok' => false, 'message' => 'This listing is no longer available.' ), 400 );
		}
		$name  = $get( 'name', 80 );
		$phone = blockke_dev_phone( $get( 'phone', 40 ) );
		$email = sanitize_email( $get( 'email', 120 ) );
		if ( mb_strlen( $name ) < 2 ) {
			return new WP_REST_Response( array( 'ok' => false, 'message' => 'Please add your name.' ), 400 );
		}
		if ( '' === $phone ) {
			return new WP_REST_Response( array( 'ok' => false, 'message' => 'Please add a phone or WhatsApp number we can reach you on.' ), 400 );
		}
		if ( ! is_email( $email ) ) {
			$email = '';
		}
		$unit     = $get( 'unit', 160 );
		$purpose  = $get( 'purpose', 20 );
		$timeline = blockke_dev_timeline( $get( 'timeline', 40 ) );
		$contact  = $get( 'contact', 20 );
		$source   = preg_replace( '/[^a-z0-9-]/', '', strtolower( $get( 'source', 30 ) ) );
		$intent   = preg_replace( '/[^a-z0-9-]/', '', strtolower( $get( 'intent', 30 ) ) );
		$message  = isset( $p['message'] ) && is_scalar( $p['message'] ) ? mb_substr( sanitize_textarea_field( (string) $p['message'] ), 0, 1500 ) : '';
		$page     = esc_url_raw( $get( 'page', 300 ) );
		$attr     = array();
		if ( isset( $p['attr'] ) && is_array( $p['attr'] ) ) {
			foreach ( array( 'utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content', 'gclid', 'fbclid', 'gbraid', 'wbraid', 'ref', 'land' ) as $k ) {
				if ( isset( $p['attr'][ $k ] ) && is_scalar( $p['attr'][ $k ] ) ) {
					$attr[ $k ] = mb_substr( sanitize_text_field( (string) $p['attr'][ $k ] ), 0, 250 );
				}
			}
		}
		$channel     = function_exists( 'blockke_bp_channel' ) ? blockke_bp_channel( $attr ) : ( ! empty( $attr['gclid'] ) ? 'Google Ads' : ( ! empty( $attr['fbclid'] ) ? 'Meta' : 'Website' ) );
		$lead_source = function_exists( 'blockke_bp_lead_source' ) ? blockke_bp_lead_source( $channel, $attr ) : 'Website - block.ke';
		$campaign    = isset( $attr['utm_campaign'] ) ? $attr['utm_campaign'] : '';
		$title       = blockke_dev_text( get_the_title( $lid ) );
		$proj        = blockke_dev_first_term( $lid, 'property_project' );
		$dev_name    = $proj ? blockke_dev_text( $proj->name ) : $title;
		$area_t      = blockke_dev_first_term( $lid, 'property_area' );
		$area        = $area_t ? blockke_dev_text( $area_t->name ) : '';
		$price       = (float) get_post_meta( $lid, 'property_price', true );
		$forms       = array(
			'hero'         => 'Hero form (price list)',
			'modal'        => 'Enquiry pop-up',
			'contact'      => 'Full enquiry form',
			'payment-plan' => 'Payment plan request',
			'mortgage'     => 'Financing request',
			'floorplan'    => 'Floor plan & price request',
			'comparables'  => 'Rental and resale comparables request',
			'lp-header'    => 'Landing page header',
			'brochure'     => 'Brochure download',
			'mobile-bar'   => 'Mobile bar',
			'subnav'       => 'Section bar',
		);
		$form_label  = isset( $forms[ $intent ] ) ? $forms[ $intent ] : ( isset( $forms[ $source ] ) ? $forms[ $source ] : $source );

		$lines   = array();
		$lines[] = 'DEVELOPMENT ENQUIRY (property page)';
		$lines[] = 'Development: ' . $dev_name . ( $price > 0 ? ' - from ' . blockke_dev_money( $price ) : '' );
		$lines[] = 'Interested in: ' . ( $unit ? $unit : '-' );
		$lines[] = '';
		$lines[] = 'Name: ' . $name;
		$lines[] = 'Phone / WhatsApp: ' . blockke_dev_phone_display( $phone ) . '  (https://wa.me/' . $phone . ')';
		$lines[] = 'Email: ' . ( $email ? $email : '-' );
		if ( $purpose ) {
			$lines[] = 'Buying to: ' . $purpose;
		}
		if ( $timeline ) {
			$lines[] = 'Timeline: ' . $timeline;
		}
		if ( $contact ) {
			$lines[] = 'Best way to reach them: ' . $contact;
		}
		if ( $message ) {
			$lines[] = 'Message: ' . $message;
		}
		$lines[] = '';
		$lines[] = 'Form: ' . $form_label;
		$lines[] = 'Listing: ' . $title . ' (Ref W-' . $lid . ')';
		$lines[] = 'Page: ' . ( $page ? $page : get_permalink( $lid ) );
		$lines[] = 'Channel (auto-tagged): ' . $channel . ( $campaign ? ' | campaign: ' . $campaign : '' );
		if ( ! empty( $attr['utm_source'] ) || ! empty( $attr['utm_medium'] ) ) {
			$lines[] = 'UTM: ' . ( isset( $attr['utm_source'] ) ? $attr['utm_source'] : '' ) . ' / ' . ( isset( $attr['utm_medium'] ) ? $attr['utm_medium'] : '' ) . ( ! empty( $attr['utm_term'] ) ? ' / term: ' . $attr['utm_term'] : '' );
		}
		if ( ! empty( $attr['gclid'] ) ) {
			$lines[] = 'gclid: ' . $attr['gclid'];
		}
		if ( ! empty( $attr['land'] ) ) {
			$lines[] = 'Landing page: ' . $attr['land'];
		}
		if ( ! empty( $attr['ref'] ) ) {
			$lines[] = 'Referrer: ' . $attr['ref'];
		}
		$lines[]     = 'Received: ' . wp_date( 'D j M Y, H:i' ) . ' EAT';
		$description = implode( "\n", $lines );

		// Saved before Zoho and the email, so a slow or failing step can't lose the enquiry.
		$ref = wp_generate_password( 20, false, false ); // lets the visitor add the optional answers to this lead
		blockke_dev_save_lead(
			array(
				'time'     => current_time( 'mysql' ),
				'listing'  => $lid,
				'dev'      => $dev_name,
				'unit'     => $unit,
				'name'     => $name,
				'phone'    => $phone,
				'email'    => $email,
				'purpose'  => $purpose,
				'timeline' => $timeline,
				'form'     => $source . ( $intent && $intent !== $source ? '/' . $intent : '' ),
				'channel'  => $channel,
				'source'   => $lead_source,
				'page'     => $page,
				'zoho'     => 'not sent',
				'mail'     => false,
				'ref'      => $ref,
			)
		);

		$zoho = 'skipped (price-check popup snippet not active)';
		if ( function_exists( 'blockke_bp_send_zoho' ) ) {
			$submarkets = array( 'Westlands', 'Kilimani', 'Kileleshwa', 'Lavington', 'Parklands', 'Karen', 'Upperhill', 'Riverside', 'Runda', 'Muthaiga', 'Spring Valley', 'Loresho', 'Kitisuru', 'Ruaka', 'Syokimau', 'Athi River', 'Ngong Road', 'Langata', 'South B / South C', 'Nairobi CBD', 'Thika Road', 'Kiambu Road', 'Mombasa Road' );
			$sm         = 'upper hill' === strtolower( $area ) ? 'Upperhill' : $area;
			$zoho       = blockke_bp_send_zoho(
				array(
					'lead_source' => $lead_source,
					'submarket'   => in_array( $sm, $submarkets, true ) ? $sm : ( $area ? 'Other' : '-None-' ),
					'buyer_type'  => 0 === strpos( $phone, '254' ) ? 'Local' : 'Diaspora',
					'name'        => $name,
					'phone'       => $phone,
					'email'       => $email,
					'areas'       => $area ? array( $area ) : array(),
					'budget'      => '',
					'unit'        => trim( $dev_name . ( $unit ? ' - ' . $unit : '' ) ),
					'alerts'      => false,
					'channel'     => $channel,
					'campaign'    => $campaign,
					'page'        => $page ? $page : get_permalink( $lid ),
					'page_title'  => $title,
					'description' => $description,
				)
			);
		}

		$to      = blockke_dev_lead_to( $lid );
		$subject = sprintf( '[Block] Development enquiry: %s - %s%s', $name, $dev_name, $unit ? ' - ' . $unit : '' );
		$headers = array( 'Content-Type: text/plain; charset=UTF-8' );
		if ( $email ) {
			$headers[] = 'Reply-To: ' . $name . ' <' . $email . '>';
		}
		$body    = $description . "\n\nReply on WhatsApp: https://wa.me/" . $phone . "\nZoho: " . $zoho . "\n\nSLA: first reply within 15 minutes; log the lead in Zoho under your name.";
		$mail_ok = $to ? (bool) wp_mail( $to, $subject, $body, $headers ) : false;
		blockke_dev_save_lead(
			array(
				'zoho' => $zoho,
				'mail' => $mail_ok,
			),
			$ref
		);

		$out = array(
			'ok'            => true,
			'phone_display' => blockke_dev_phone_display( $phone ),
			'ref'           => $ref,
		);
		$brochure = blockke_dev_brochure( $lid );
		if ( $brochure ) {
			$out['asset'] = $brochure;
		}
		return new WP_REST_Response( $out, 200 );
	}

	add_action(
		'rest_api_init',
		function () {
			register_rest_route(
				'block/v1',
				'/development-lead',
				array(
					'methods'             => 'POST',
					'callback'            => 'blockke_dev_handle_lead',
					'permission_callback' => '__return_true',
				)
			);
		}
	);

	/* =====================================================================
	   Editor: "Development page" box on Properties, and admin-bar previews
	   ===================================================================== */

	function blockke_dev_fields() {
		return array(
			'bke_dev_layout'           => array( 'Layout', 'select' ),
			'bke_dev_tagline'          => array( 'Hero line', 'textarea', 'One or two sentences under the name. Empty: the excerpt or SEO description.' ),
			'bke_dev_overview_heading' => array( 'Overview headline', 'text', 'Empty: "About {development}".' ),
			'bke_dev_facts'            => array( 'Key facts', 'textarea', 'One per line as Label | Value, e.g. Residences | 328. Empty: read from the "at a Glance" section and the units.' ),
			'bke_dev_deposit_pct'      => array( 'Deposit %', 'number', 'Empty: read from the Payment Plan section.' ),
			'bke_dev_reservation'      => array( 'Reservation fee (KES)', 'number', 'Empty: read from the Payment Plan section.' ),
			'bke_dev_plan_until'       => array( 'Instalments until (YYYY-MM)', 'text', 'Empty: the completion date.' ),
			'bke_dev_plan_text'        => array( 'Payment plan text', 'textarea', 'Empty: the first paragraph of the Payment Plan section.' ),
			'bke_dev_places'           => array( 'Places nearby', 'textarea', 'One per line as Place | travel time. Shown under Address and in Location. Empty: the description\'s Location section, else straight-line distances from the map pin.' ),
			'bke_dev_brochure'         => array( 'Brochure (PDF link)', 'url', 'Opens for the visitor right after they send their details. Empty: the first PDF attached to the listing.' ),
			'bke_dev_video'            => array( 'Video (YouTube or Vimeo link)', 'url', 'Empty: the listing\'s video, else its virtual tour.' ),
			'bke_dev_similar'          => array( 'Similar listings (IDs)', 'text', 'Comma-separated listing IDs. Empty: other developments in the same area.' ),
		);
	}

	add_action(
		'add_meta_boxes_estate_property',
		function () {
			add_meta_box(
				'bkd-meta',
				'Development page',
				function ( $post ) {
					wp_nonce_field( 'bkd_meta', 'bkd_meta_nonce' );
					$cfg  = blockke_dev_config();
					$mode = (string) get_post_meta( $post->ID, 'bke_dev_layout', true );
					echo '<p style="margin-top:0">The development layout turns this listing into a landing page with the price list form, residences table, payment plan calculator, map and FAQ, all read from this listing. <a href="' . esc_url( add_query_arg( 'bke_layout', '1', get_permalink( $post ) ) ) . '" target="_blank">Preview it</a>.</p>';
					if ( ! empty( $cfg['landing_pages'] ) && blockke_dev_is_for_sale( $post->ID ) ) {
						$lp_url = add_query_arg( 'lp', '1', get_permalink( $post ) );
						echo '<p>Ad landing page for Google Ads and other campaigns: <a href="' . esc_url( $lp_url ) . '" target="_blank">' . esc_html( $lp_url ) . '</a>. The same content without the site menu or footer, hidden from search engines. Nothing to set up.</p>';
					}
					echo '<table class="form-table" role="presentation"><tbody>';
					foreach ( blockke_dev_fields() as $key => $f ) {
						$val = (string) get_post_meta( $post->ID, $key, true );
						echo '<tr><th scope="row"><label for="' . esc_attr( $key ) . '">' . esc_html( $f[0] ) . '</label></th><td>';
						if ( 'select' === $f[1] ) {
							$auto = ! empty( $cfg['auto'] ) && '1' === (string) get_post_meta( $post->ID, 'property_has_subunits', true );
							echo '<select id="bke_dev_layout" name="bke_dev_layout">'
								. '<option value=""' . selected( $mode, '', false ) . '>Automatic (currently ' . ( $auto ? 'on' : 'off' ) . ')</option>'
								. '<option value="on"' . selected( $mode, 'on', false ) . '>On: development layout</option>'
								. '<option value="off"' . selected( $mode, 'off', false ) . '>Off: classic property page</option></select>';
						} elseif ( 'textarea' === $f[1] ) {
							echo '<textarea class="large-text" rows="3" id="' . esc_attr( $key ) . '" name="' . esc_attr( $key ) . '">' . esc_textarea( $val ) . '</textarea>';
						} else {
							echo '<input class="regular-text" type="' . ( 'number' === $f[1] ? 'number" step="any' : esc_attr( $f[1] ) ) . '" id="' . esc_attr( $key ) . '" name="' . esc_attr( $key ) . '" value="' . esc_attr( $val ) . '">';
						}
						if ( ! empty( $f[2] ) ) {
							echo '<p class="description">' . esc_html( $f[2] ) . '</p>';
						}
						echo '</td></tr>';
					}
					echo '</tbody></table>';
				},
				'estate_property',
				'normal',
				'default'
			);
		}
	);

	add_action(
		'save_post_estate_property',
		function ( $post_id ) {
			if ( ! isset( $_POST['bkd_meta_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['bkd_meta_nonce'] ) ), 'bkd_meta' ) ) {
				return;
			}
			if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || ! current_user_can( 'edit_post', $post_id ) ) {
				return;
			}
			foreach ( blockke_dev_fields() as $key => $f ) {
				if ( ! isset( $_POST[ $key ] ) ) {
					continue;
				}
				$raw = wp_unslash( $_POST[ $key ] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
				if ( 'select' === $f[1] ) {
					$v = in_array( $raw, array( 'on', 'off' ), true ) ? $raw : '';
				} elseif ( 'textarea' === $f[1] ) {
					$v = sanitize_textarea_field( $raw );
				} elseif ( 'url' === $f[1] ) {
					$v = esc_url_raw( trim( $raw ) );
				} elseif ( 'number' === $f[1] ) {
					$v = '' === trim( $raw ) ? '' : (string) (float) $raw;
				} else {
					$v = sanitize_text_field( $raw );
				}
				if ( '' === $v ) {
					delete_post_meta( $post_id, $key );
				} else {
					update_post_meta( $post_id, $key, $v );
				}
			}
		}
	);

	add_action(
		'admin_bar_menu',
		function ( $bar ) {
			if ( is_admin() || ! is_singular( 'estate_property' ) || ! current_user_can( 'edit_post', get_queried_object_id() ) ) {
				return;
			}
			$id   = get_queried_object_id();
			$url  = get_permalink( $id );
			$live = blockke_dev_layout_on( $id );
			$bar->add_node( array( 'id' => 'bkd', 'title' => 'Development layout: ' . ( $live ? 'on' : 'off' ), 'href' => get_edit_post_link( $id ) . '#bkd-meta' ) );
			$bar->add_node( array( 'parent' => 'bkd', 'id' => 'bkd-on', 'title' => 'Preview the development layout', 'href' => add_query_arg( 'bke_layout', '1', $url ) ) );
			$bar->add_node( array( 'parent' => 'bkd', 'id' => 'bkd-off', 'title' => 'Preview the classic page', 'href' => add_query_arg( 'bke_layout', '0', $url ) ) );
			if ( ! empty( blockke_dev_config()['landing_pages'] ) && blockke_dev_is_for_sale( $id ) ) {
				$bar->add_node( array( 'parent' => 'bkd', 'id' => 'bkd-lp', 'title' => 'Open the ad landing page', 'href' => add_query_arg( 'lp', '1', $url ) ) );
			}
			$bar->add_node( array( 'parent' => 'bkd', 'id' => 'bkd-edit', 'title' => 'Layout settings', 'href' => get_edit_post_link( $id ) . '#bkd-meta' ) );
		},
		90
	);
}
