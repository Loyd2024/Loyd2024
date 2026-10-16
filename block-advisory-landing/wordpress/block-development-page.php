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
 * 3) AREA PAGES (v1.5, opt-in per area): a Neighbourhood page (/property_area/lavington/) on the same layout, with one
 *    section per kind of listing (homes for sale, homes to rent, commercial for sale or rent, land), and a page for each
 *    kind (/property_area/lavington/villas-for-sale/, /offices-for-rent/ ...): every grid holds one kind, never mixed.
 *    Turn it on: config area_pages (area slugs, or true); preview while logged in: any area URL + ?bke_area=1.
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
 * v1.3.2 (2026-10-11): photo labels. Off-plan pages no longer say "Artist's impression": the main photo is
 * labelled "Project render" and the gallery note reads "Project renders.", unless the listing sets its own
 * in the custom fields bke_dev_hero_label and bke_dev_gallery_note (e.g. "Show apartment" and "Photos of
 * the show apartment."); "none" hides either. The disclaimer says "Images may include project renders."
 * v1.4 (2026-10-10): getting around. "Search homes" next to Share (and in the desktop section bar) opens a
 * short search filled in from the listing (Buy, area, bedrooms, budget, completion) into the site's search
 * results. "Also consider" now shows homes with the same bedrooms in the same area close in price and size
 * (then neighbouring areas, then one bedroom more or fewer), one per development, never this development,
 * sold, commercial or per-sq-ft listings; developments like this one on development pages. A unit page has
 * a switcher between the development's homes, the breadcrumb names the development and the home, and
 * visitors from a search get "Back to results". Each listing's main area is the development's, or the one
 * with most listings (a unit also tagged "Industrial Area" stays Westlands). Ad landing pages get no search
 * or links away: "Send me 3 alternatives" is an enquiry, and one link to similar homes appears only after
 * the visitor has enquired. Similar homes are cached per listing (bkd_sim_{id}) and refreshed whenever a
 * listing changes. Config: search, similar_*, neighbours, budget_bands, unit_switcher, auto_units and more.
 * The headline price reads "Up to" for a ceiling price and "Price" for a ready home's own price, not "From".
 * v1.4.1 (2026-10-11): sizes written in both units ("43 sqm (463 sq ft)") read as square metres, not "4 – 43 m²";
 * "1-Bedroom" and "2-Bedroom" count as bedrooms; the FAQ is found when it is an H3 inside another section,
 * and Rank Math and Yoast FAQ blocks are read; the payment plan is read from an H3 under "Pricing"; a plan
 * with a fixed share per instalment ("then 10% every 3 months") is calculated that way, with the balance on
 * completion; with no plan stated and completion three months away or less, a note replaces the estimate;
 * and the Location heading drops asides ("(off Waiyaki Way), directly opposite ..."). Includes v1.3.2's photo
 * labels, which can also be set in the Development page box ("Main photo label", "Gallery note").
 * v1.4.2 (2026-10-12): studios are searchable. The search sheet's bedrooms start at "Studio", and links for a
 * studio ("See all studio homes in Westlands", the residences filter) search the site's Studio category
 * (config studio_cat) instead of a bedroom count, since the site search does not filter on 0 bedrooms.
 * v1.4.3 (2026-10-13): FAQs written as "<strong>Question?</strong> Answer" in one paragraph are read too, and a
 * unit page with no places of its own shows its development's "Places nearby" box before straight-line distances.
 * v1.4.4 (2026-10-13): completed homes (from the Tilisi Views review). A price column headed "From" is read, with
 * its "cash"/"instalment" note; the hero keeps a listing's "From"; "Calculator price" (bke_dev_calc_price) starts
 * the calculator from the mortgage price; ready homes offer the instalment plan when the listing has one, take
 * "Payment plan text", and Why Block drops construction updates; amenity and location intros are whole sentences
 * and a shared "Amenities & Location" paragraph is split between them; a title-like first line is not the lead;
 * single homes show bedrooms, bathrooms, size and plot; villas are compared with villas and houses; the full
 * description no longer widens the page on phones.
 * v1.4.5 (2026-10-15): an off-plan page whose listing gives no deposit, reservation fee or instalment wording, or
 * whose completion is weeks away, heads its payment section "Ask for the payment plan" instead of "Pay in
 * instalments"; the kids' play area tile no longer calls it "safe".
 * v1.5 (2026-10-16): area pages (3 above). Sections and segment pages never mix homes, commercial and land, or sale and
 * rent; one card per development; bedroom filter, sort and "Show more"; segment pages with their own title, description
 * and canonical, noindex under 3 listings; area enquiries ("[Block] Area enquiry: ...") with what the visitor is looking for.
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
			'similar_count'      => 6,     // "Also consider" cards
			'similar_units'      => true,  // unit pages: homes with the same bedrooms in the area before developments
			'similar_min'        => 3,     // fewer matches than this: developments in the area instead (never 1-2 cards)
			'price_band'         => 0.25,  // similar homes within ±25% of the price come first
			'dev_price_band'     => 0.35,  // similar developments within ±35% of the "from" price come first
			'pool_cap'           => 200,   // most listings read per similar-homes query
			'sim_ttl'            => 43200, // seconds; also rebuilt whenever a listing changes
			'neighbours'         => array( // area slug => array( nearest areas, the next ring ), for areas with few matches
				'westlands'        => array( array( 'parklands', 'general-mathenge', 'riverside', 'brookside' ), array( 'peponi', 'spring-valley', 'kileleshwa', 'kilimani' ) ),
				'kilimani'         => array( array( 'kileleshwa', 'lavington', 'riverside' ), array( 'upperhill', 'westlands' ) ),
				'kileleshwa'       => array( array( 'kilimani', 'lavington', 'riverside' ), array( 'brookside', 'westlands' ) ),
				'riverside'        => array( array( 'brookside', 'kileleshwa', 'general-mathenge', 'westlands', 'kilimani' ), array( 'spring-valley', 'lavington' ) ),
				'lavington'        => array( array( 'kileleshwa', 'kilimani', 'riverside' ), array( 'loresho', 'riruta' ) ),
				'general-mathenge' => array( array( 'westlands', 'brookside', 'peponi', 'parklands', 'riverside' ), array( 'spring-valley' ) ),
				'parklands'        => array( array( 'westlands', 'general-mathenge', 'brookside' ), array( 'riverside', 'gigiri', 'peponi' ) ),
				'brookside'        => array( array( 'spring-valley', 'general-mathenge', 'peponi', 'riverside', 'westlands' ), array( 'nyari' ) ),
				'peponi'           => array( array( 'spring-valley', 'brookside', 'nyari', 'general-mathenge', 'gigiri' ), array( 'kitisuru', 'westlands' ) ),
				'upperhill'        => array( array( 'kilimani' ), array( 'westlands', 'kileleshwa', 'riverside' ) ),
				'kitisuru'         => array( array( 'runda', 'gigiri', 'nyari', 'ruaka' ), array( 'peponi', 'spring-valley' ) ),
				'riruta'           => array( array( 'lavington', 'kinoo', 'kikuyu' ), array( 'kileleshwa', 'loresho', 'kilimani' ) ),
				'spring-valley'    => array( array( 'peponi', 'brookside', 'nyari' ), array( 'general-mathenge' ) ),
				'gigiri'           => array( array( 'kitisuru', 'nyari', 'runda' ), array( 'peponi' ) ),
				'loresho'          => array( array( 'lower-kabete', 'spring-valley' ), array( 'lavington' ) ),
				'runda'            => array( array( 'kitisuru', 'gigiri' ), array( 'ridgeways' ) ),
				'tatu-city'        => array( array( 'ruiru', 'kiambu-road', 'thika-road' ), array( 'ruaka' ) ),
				'ruiru'            => array( array( 'tatu-city', 'juja', 'thika-road', 'kamiti-road' ), array() ),
				'syokimau'         => array( array( 'mombasa-road', 'athi-river', 'lukenya-plains' ), array( 'south-b', 'south-c' ) ),
				'limuru'           => array( array( 'kikuyu', 'kinoo', 'ruaka', 'limuru-road', 'kitisuru', 'nyari' ), array( 'runda', 'rosslyn', 'gigiri', 'loresho', 'lower-kabete', 'ridgeways' ) ),
			),
			'search'             => true,  // "Search homes" on development pages (never on ad landing pages)
			'search_url'         => '',    // '' = the site's search results page, /advanced-search/
			'search_min_area_count' => 5,  // areas offered in the search sheet: those with at least this many listings
			'budget_bands'       => array( // array( from, up to (0 = no limit), label )
				'sale' => array( array( 0, 8000000, 'Under KES 8M' ), array( 8000000, 12000000, 'KES 8M – 12M' ), array( 12000000, 18000000, 'KES 12M – 18M' ), array( 18000000, 30000000, 'KES 18M – 30M' ), array( 30000000, 60000000, 'KES 30M – 60M' ), array( 60000000, 0, 'KES 60M and above' ) ),
				'rent' => array( array( 0, 80000, 'Under KES 80K a month' ), array( 80000, 150000, 'KES 80K – 150K a month' ), array( 150000, 300000, 'KES 150K – 300K a month' ), array( 300000, 0, 'KES 300K a month and above' ) ),
			),
			'beds_mode'          => 'exact', // 'min' if the site search reads bedrooms as "at least": labels then say "2+ bed"
			'studio_cat'         => 'studio-apartments', // property category slug that studio searches use; '' = no Studio option
			'unit_switcher'      => true,  // unit pages: a row to switch between the development's homes (1 Bed, 2 Bed...)
			'back_to_results'    => true,  // "Back to results" when the visitor came from a search or listings page
			'lp_alternatives'    => true,  // landing pages: "Not sure? Send me 3 alternatives" (an enquiry, not a link)
			'lp_after_link'      => true,  // landing pages: one link to similar homes, shown only after an enquiry is sent
			'auto_units'         => false, // true, or area slugs e.g. array( 'westlands' ): units for sale there get the layout without switching each on
			'area_pages'         => array(), // Neighbourhood pages on the new layout: true for all, or area slugs e.g. array( 'lavington' ); preview: ?bke_area=1
			'area_index_min'     => 3,     // segment pages with fewer listings than this are noindex (still followed)
			'area_type_index_min' => 5,    // the same for type pages (apartments, villas, offices...)
			'area_cards'         => 6,     // cards per section on an area page, then "See all"
			'area_page_size'     => 24,    // cards on a segment page before "Show more"
			'area_cap'           => 400,   // most listings read per area
			'area_why'           => array(
				array( 'Advice, not pressure', 'We compare listings, streets and prices across the area side by side, so you choose with clarity.' ),
				array( 'Checks before you commit', 'We help you check ownership, approvals and the sale or lease agreement before you pay a deposit.' ),
				array( 'One advisor throughout', 'From the first viewing to signing, the same advisor stays with you.' ),
			),
			'card_v16'           => false, // v1.6: area and segment pages use the new card, filter bar and URL state (preview: ?bke_cards=1)
			'dev_index'          => false, // v1.6: /new-developments/ on the new layout (preview: ?bke_index=1)
			'dev_index_page'     => 30752, // the page it takes over
			'index_picks'        => array(), // lead first, then up to 2 listing IDs; empty: chosen automatically
			'index_group_size'   => 8,     // cards per completion year before "Show N more"
			'index_page_size'    => 18,    // flat sorts and search results: cards per step
			'index_min_photos'   => 3,     // fewer photos: listed by name in the A-Z only
			'index_ready_max'    => 8,
			'index_ready_singles' => false, // single ready listings (often resales) in "Ready to move into"
			'index_new_days'     => 30,    // "New on Block" tag
			'index_advisor'      => array( 'name' => '', 'photo' => 0, 'line' => '', 'phone' => '' ), // shown in the shortlist band once filled in
			'index_metro'        => array( 'Nairobi', 'Kiambu', 'Machakos', 'Kajiado' ), // cities counted as "Nairobi and nearby" on the index; others are "elsewhere in Kenya"
			'search_layout'      => false, // v1.6: /advanced-search/ results on the new layout (preview: ?bke_search=1)
			'search_redirect'    => false, // v1.6: simple searches go to the matching area or segment page, or the index
			'search_params'      => array( 'area' => array( 'location', 'property_area', 'advanced_area', 'adv_location' ), 'beds' => array( 'componentsbeds', 'beds', 'bedrooms' ), 'min' => array( 'price_low', 'price_min' ), 'max' => array( 'price_max' ) ),
			'search_ignore'      => array( 'utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content', 'gclid', 'gbraid', 'wbraid', 'fbclid', '_gl', 'submit', 'bke_search' ),
			'view_transitions'   => false, // v1.6: the card photo carries on into the development page (and its hero becomes photo-first, 3:2)
			'prefetch'           => false, // v1.6: fetch a development page while the visitor's pointer rests on its card
			'area_disclaimer'    => 'Listings are published in good faith from information supplied by owners, landlords and developers, and may change without notice. Please confirm prices, sizes, availability and terms before you commit. Block Real Estate does not give financial or legal advice; we recommend an advocate reviews any agreement before you sign.',
			'why'                => array(
				array( 'Independent advice', 'We compare developments, layouts and payment plans side by side, so you choose with clarity rather than pressure.' ),
				array( 'Checks before you commit', 'We help you review the developer, approvals and sale documents before you reserve or pay a deposit.' ),
				array( 'One advisor to handover', 'From reservation and payments to construction updates and handover, the same advisor stays with you.', 'From reservation and payments to transfer and handover, the same advisor stays with you.' ), // third: ready homes
			),
			'disclaimer'         => "All information is provided in good faith from the developer's published material and is subject to change without notice. Prices, sizes, availability, payment terms and completion dates should be confirmed before you commit. Images may include project renders. Block Real Estate does not provide financial or legal advice.",
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

	/** Whole sentences, at most $n, up to $max characters; a longer first sentence ends at its last clause break (", which", " — "). */
	function blockke_dev_fit_sentences( $text, $max, $n = 2 ) {
		$parts = preg_split( '/(?<=[.!?])\s+(?=[A-Z0-9“"])/u', trim( (string) $text ) );
		$out   = '';
		foreach ( array_slice( $parts, 0, $n ) as $p ) {
			$try = '' === $out ? $p : $out . ' ' . $p;
			if ( mb_strlen( $try ) > $max ) {
				break;
			}
			$out = $try;
		}
		if ( '' !== $out || '' === $parts[0] ) {
			return $out;
		}
		$cut = mb_substr( $parts[0], 0, $max );
		if ( preg_match_all( '/,\s+(?:which|where|while|with|and also)\b|;\s|\s[—–]\s/u', $cut, $m, PREG_OFFSET_CAPTURE ) ) {
			$at = end( $m[0] )[1];
			if ( $at >= 60 ) {
				return rtrim( substr( $cut, 0, $at ), ' ,;:—–' ) . '.';
			}
		}
		return blockke_dev_trim_words( $parts[0], $max );
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
		if ( $is_ft && preg_match( '/\d\s*(?:sq\.?\s*m\b|sqm|m²|m2\b|square met)/i', $t ) ) {
			// "43 sqm (463 sq ft)": both units given, so read only the square metres
			$t     = preg_replace( '/\([^)]*(?:sq\.?\s*ft|sqft|ft²|square f)[^)]*\)|\d[\d,.]*(?:\s*(?:-|–|to)\s*\d[\d,.]*)?\s*(?:sq\.?\s*ft|sqft|ft²|square f\w*)/i', ' ', $t );
			$is_ft = false;
			if ( ! preg_match_all( '/\d+(?:\.\d+)?/', str_replace( ',', '', $t ), $m ) ) {
				return '';
			}
		}
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

	/** The label of the first fact whose label matches. */
	function blockke_dev_kv_key( $kv, $re ) {
		foreach ( $kv as $p ) {
			if ( preg_match( $re, $p[0] ) ) {
				return $p[0];
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
		$ptoks  = $i >= 0 ? $parsed['sections'][ $i ]['tokens'] : array();
		if ( $i < 0 ) { // "Payment Plan" as an H3 under "Pricing"
			$sub = blockke_dev_sub_section( $parsed, '/payment (plan|terms|schedule)/i' );
			if ( $sub ) {
				$ptoks = $sub['tokens'];
				$i     = 0;
			}
		}
		if ( $i >= 0 ) {
			foreach ( $ptoks as $t ) {
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
		// A fixed share per instalment ("then 10% every 3 months", "5% monthly"): the rest falls due on completion
		$inst_pct   = 0;
		$inst_every = 0;
		if ( preg_match( '/(\d{1,2}(?:\.\d+)?)\s*%\s*(?:of the (?:purchase )?price\s+)?(?:(?:every|each|per|a)\s+(month|quarter|(?:3|three)\s*months|(?:6|six)\s*months)\b|(monthly|quarterly)\b)/i', $all, $m ) && (float) $m[1] > 0 && (float) $m[1] <= 25 ) {
			$w          = strtolower( isset( $m[3] ) && '' !== $m[3] ? $m[3] : $m[2] );
			$inst_pct   = (float) $m[1];
			$inst_every = ( 'month' === $w || 'monthly' === $w ) ? 1 : ( preg_match( '/6|six/', $w ) ? 6 : 3 );
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
			'own_text'      => '' !== $override_text,
			'instalments'   => (bool) preg_match( '/instal+ment/i', $all ),
			'deposit'       => $dep,
			'reservation'   => $res,
			'on_completion' => $on_completion,
			'inst_pct'      => $inst_pct,
			'inst_every'    => $inst_every,
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
		$ip     = isset( $plan['inst_pct'] ) ? (float) $plan['inst_pct'] : 0;
		$ie     = $ip > 0 ? (int) $plan['inst_every'] : 0;
		$freq   = 3 === $ie ? 3 : 1; // quarterly first only when the plan fixes a share every 3 months
		$h      = '<div class="bkd-calc" id="bkd-calc" data-bkd-calc="instalment" data-cur="' . esc_attr( $cur ) . '" data-deposit="' . esc_attr( $dep ) . '"'
			. ' data-reservation="' . esc_attr( (float) $plan['reservation'] ) . '" data-until="' . esc_attr( $plan['until'] ) . '" data-months="' . esc_attr( $months ) . '"'
			. ' data-completion="' . esc_attr( $plan['completion'] ) . '" data-oncompletion="' . esc_attr( (int) $plan['on_completion'] ) . '"'
			. ( $ip > 0 ? ' data-instpct="' . esc_attr( $ip ) . '" data-instevery="' . esc_attr( $ie ) . '"' : '' ) . '>';
		$h     .= '<div class="bkd-calc-grid"><div class="bkd-calc-fields">';
		$h     .= '<label class="bkd-cf"><span class="bkd-cl">Price (' . esc_html( $cur ) . ')</span><input class="bkd-ci" data-c="price" inputmode="numeric" autocomplete="off" value="' . esc_attr( $price > 0 ? number_format( $price ) : '' ) . '" aria-label="Price"></label>';
		$h     .= '<label class="bkd-cf"><span class="bkd-cl">Deposit <output data-c="dep-out">' . esc_html( blockke_dev_num( $dep ) ) . '%</output></span>'
			. '<input class="bkd-range" type="range" data-c="dep" min="0" max="60" step="5" value="' . esc_attr( round( $dep / 5 ) * 5 ) . '" aria-label="Deposit percentage"></label>';
		$h     .= '<div class="bkd-cf" role="group" aria-label="Pay the balance"><span class="bkd-cl">Pay the balance</span><div class="bkd-seg">'
			. '<button type="button" data-c="freq" data-v="1" aria-pressed="' . ( 1 === $freq ? 'true' : 'false' ) . '">Monthly</button><button type="button" data-c="freq" data-v="3" aria-pressed="' . ( 3 === $freq ? 'true' : 'false' ) . '">Quarterly</button></div></div>';
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
		if ( $ip > 0 ) {
			$note = 'Illustrative only: ' . blockke_dev_num( $ip ) . '% of the price every ' . ( 1 === $ie ? 'month' : $ie . ' months' ) . ' from next month, and the balance on completion' . ( $plan['completion'] ? ' in ' . $plan['completion'] : '' ) . '.';
		}
		if ( $plan['deposit'] <= 0 ) {
			$note .= ' The deposit shown is typical; this listing has not stated one.';
		}
		$h .= '<div class="bkd-calc-note">' . esc_html( $note . " The developer's official schedule may differ." ) . '</div>';
		$h .= $cta;
		$h .= '</div>';
		return $h;
	}

	function blockke_dev_calc_mortgage( $price, $cta = '', $own_price = false ) {
		$cfg = blockke_dev_config();
		$cur = $cfg['currency'];
		$h   = '<div class="bkd-calc" id="bkd-calc" data-bkd-calc="mortgage" data-cur="' . esc_attr( $cur ) . '">';
		$h  .= '<div class="bkd-calc-grid"><div class="bkd-calc-fields">';
		$h  .= '<label class="bkd-cf"><span class="bkd-cl">' . ( $own_price ? 'Mortgage price' : 'Price' ) . ' (' . esc_html( $cur ) . ')</span><input class="bkd-ci" data-c="price" inputmode="numeric" autocomplete="off" value="' . esc_attr( $price > 0 ? number_format( $price ) : '' ) . '" aria-label="' . ( $own_price ? 'Mortgage price' : 'Price' ) . '"></label>';
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
  var fb=box.querySelector('[data-c="freq"][aria-pressed="true"]'),freq=fb?(+fb.getAttribute('data-v')||1):1;
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
    var ip=+box.getAttribute('data-instpct')||0,ie=+box.getAttribute('data-instevery')||3;
    var months=monthsLeft(),count=Math.max(1,Math.floor(months/freq)||1);
    var depAmt=p*d/100,compAmt=p*oc/100,inst=Math.max(0,p-depAmt-compAmt),per=inst/count;
    if(ip>0&&p){per=Math.min(p*ip/100*freq/ie,inst);if(per>0){count=Math.max(1,Math.min(count,Math.floor(inst/per+1e-6)));}compAmt=Math.max(0,p-depAmt-per*count);}
    var unit=freq===3?'quarter':'month',comp=box.getAttribute('data-completion')||'';
    var until=box.getAttribute('data-until')&&monthsLeft()>0&&!q('months');
    q('per-label').textContent='Per '+unit+(until?', until completion'+(comp?' in '+comp:''):', over '+months+' months');
    q('per').textContent=p?fmt(per):'—';
    q('deposit').textContent=p?fmt(depAmt):'—';
    q('dep-note').textContent=res&&p&&depAmt>=res?'Includes the '+fmt(res)+' reservation':'';
    q('count').textContent=p?count+' × '+fmt(per):'—';
    var cr=q('comp-row');if(cr){cr.hidden=!(oc||(ip&&compAmt>=1));q('comp').textContent=p?fmt(compAmt):'—';}
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
				$x['own'] = blockke_dev_master_places( $id );
			}
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
		if ( preg_match( '/(\d+)\s*-?\s*(?:(?:-|–|to|&|and)\s*\d+\s*-?\s*)?(?:bed|br\b)/u', $l, $m ) ) { // "2 Bed", "1-Bedroom", "2-3 bed"
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
				if ( $pi < 0 && preg_match( '/price|kes|amount|cost|^\s*(?:from|starting)\b/', $hl ) ) {
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
				$price = ( $pi >= 0 && isset( $c[ $pi ] ) ) ? blockke_dev_parse_money( $c[ $pi ] ) : 0;
				$units[] = array(
					'type'  => $type,
					'note'  => $note,
					'key'   => $key,
					'group' => blockke_dev_group_label( $key, $type ),
					'size'  => $size,
					'price' => $price,
					'pnote' => $price > 0 ? blockke_dev_price_note( $c[ $pi ] ) : '',
					'url'   => $url,
				);
			}
			if ( $units ) {
				return $units;
			}
		}
		return array();
	}

	/** What a price cell says about its first figure: "KES 26.75M cash · 28M on mortgage" -> "Cash". */
	function blockke_dev_price_note( $cell ) {
		$t = blockke_dev_text( $cell );
		if ( ! preg_match( '/\d(?:[\d,.]*\d)?\s*(?:million|mn|m|bn|b|k)?\b\s*\(?\s*(\p{L}[\p{L}\s\/\-]{1,22}?)\s*(?:\)|[·•;,|]|$)/iu', $t, $m ) ) {
			return '';
		}
		return preg_match( '/^(?:cash|instal+ments?|mortgage|off-?plan|on instal+ments?|on mortgage)(?:\s*\/\s*\p{L}+)?$/iu', trim( $m[1] ) ) ? ucfirst( trim( $m[1] ) ) : '';
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
				'pnote' => '',
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
		if ( ! $units ) { // one home (a unit page or a single listing): its bedrooms and bathrooms
			foreach ( array( 'Bedrooms' => array( '/^(?:bedrooms?|beds?)$/i', 'property_bedrooms' ), 'Bathrooms' => array( '/^(?:bathrooms?|baths?)$/i', 'property_bathrooms' ) ) as $lab => $src ) {
				$v = blockke_dev_kv_get( $kv, $src[0] );
				if ( '' === $v || mb_strlen( $v ) > 24 ) {
					$n = (float) get_post_meta( $id, $src[1], true );
					$v = $n > 0 ? blockke_dev_num( $n ) : '';
				}
				if ( '' !== $v ) {
					$facts[] = array( $lab, $v );
				}
			}
		}
		$sizes = blockke_dev_size_range( $units );
		if ( ! $sizes ) {
			$sz = blockke_dev_kv_get( $kv, '/built area|floor area|internal area|living area|unit size|^sizes?$/i' );
			foreach ( '' === $sz ? $kv : array() as $p ) { // any other size, but not the plot's
				if ( preg_match( '/size/i', $p[0] ) && ! preg_match( '/plot|land/i', $p[0] ) ) {
					$sz = $p[1];
					break;
				}
			}
			if ( $sz ) {
				$sizes = blockke_dev_size( $sz );
			}
		}
		if ( $sizes ) {
			$facts[] = array( false !== strpos( $sizes, '–' ) ? 'Sizes' : 'Size', $sizes );
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
		$land = preg_replace( '/\s*(?:sq\.?\s*m\b|sqm\b|square met(?:er|re)s?\b|m2\b)/i', ' m²', $land ); // the same unit as Size
		if ( mb_strlen( $land ) > 24 ) { // "Approx. 375–531 m² (eighth-acre)" -> "Approx. 375–531 m²"
			$land = trim( preg_replace( '/\s*(?:\(|[,;]\s).*$/u', '', $land ) );
		}
		if ( mb_strlen( $land ) > 24 ) {
			$land = preg_replace( '/\bapprox(?:imately|\.)?\s*/i', '', $land );
		}
		if ( $land && mb_strlen( $land ) <= 24 ) {
			$facts[] = array( preg_match( '/plot/i', blockke_dev_kv_key( $kv, '/land size|plot|acre/i' ) ) ? 'Plot' : 'Land', ucfirst( $land ) );
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
			array( '/\bkids?\b|child|play ?(area|ground|room|park|zone)|creche|crèche|day ?care/u', 'kids', 'Kids’ play area', 'kite', 85, 'Room for children to play, close to home.' ),
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

	/** Drops intro sentences that only repeat a place row: "… 30 km (about 30 minutes) from Nairobi CBD …" over "Nairobi CBD | 30 km". */
	function blockke_dev_loc_intro_dedup( $intro, $places ) {
		if ( '' === $intro || ! $places ) {
			return $intro;
		}
		$keep = array();
		foreach ( preg_split( '/(?<=[.!?])\s+(?=[A-Z0-9“"])/u', $intro ) as $x ) {
			$dup = false;
			foreach ( $places as $p ) {
				if ( '' !== $p[0] && false !== stripos( $x, $p[0] ) && preg_match_all( '/(\d+(?:\.\d+)?)\s*(?:km|min)/i', $x, $nx ) && preg_match_all( '/(\d+(?:\.\d+)?)\s*(?:km|min)/i', $p[0] . ' ' . $p[1], $np ) && ! array_diff( $nx[1], $np[1] ) ) {
					$dup = true;
					break;
				}
			}
			if ( ! $dup ) {
				$keep[] = $x;
			}
		}
		return implode( ' ', $keep );
	}

	/** The Amenities section's own introduction (the whole paragraph), if the description has one. */
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
				return $txt;
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
			array( 'Nairobi Arboretum', 'park', -1.27859, 36.80308 ), // entrance on Arboretum Drive (was -1.27430, 36.81310, which is Museum Hill)
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

	/** A unit's development's "Places nearby" box, for a unit that has none of its own. */
	function blockke_dev_master_places( $id ) {
		$m = (int) get_post_meta( $id, 'property_subunits_master', true );
		if ( ! $m || $m === (int) $id || 'publish' !== get_post_status( $m ) ) {
			return array();
		}
		return blockke_dev_places_override( $m );
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

	/** A heading below the top level ("Frequently Asked Questions" as an H3) and what follows it up to the next heading as high. */
	function blockke_dev_sub_section( $parsed, $re ) {
		$toks = $parsed['tokens'];
		$n    = count( $toks );
		foreach ( $toks as $i => $t ) {
			$ht = blockke_dev_text( $t['inner'] );
			if ( 'h' !== $t['tag'] || $t['level'] <= $parsed['level'] || ! preg_match( $re, $ht ) || '?' === substr( $ht, -1 ) || preg_match( '/faq-question|rank-math-question/', $t['html'] ) ) {
				continue; // not a question such as "What is the payment plan?"
			}
			$out = array();
			$end = strlen( $parsed['html'] );
			for ( $j = $i + 1; $j < $n; $j++ ) {
				$h = $toks[ $j ];
				if ( 'h' === $h['tag'] && $h['level'] <= $t['level'] && ! preg_match( '/faq-question|rank-math-question/', $h['html'] ) ) {
					$end = $h['pos'];
					break;
				}
				$out[] = $h;
			}
			return array(
				'tokens' => $out,
				'html'   => substr( $parsed['html'], $t['pos'], $end - $t['pos'] ),
			);
		}
		return null;
	}

	function blockke_dev_faqs( $tokens, $html = '' ) {
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
		// Rank Math and Yoast FAQ blocks: the answer is a <div>, which the token reader skips
		if ( preg_match_all( '#<(h[2-6]|strong|p|div)\b[^>]*class="[^"]*(?:rank-math-question|schema-faq-question)[^"]*"[^>]*>(.*?)</\1>.*?<(div|p)\b[^>]*class="[^"]*(?:rank-math-answer|schema-faq-answer)[^"]*"[^>]*>(.*?)</\3>#is', $html, $fm, PREG_SET_ORDER ) ) {
			foreach ( $fm as $x ) {
				$fq = rtrim( blockke_dev_text( $x[2] ), ':' );
				$fa = trim( wp_kses( $x[4], $allow ) );
				if ( '' !== $fq && '' !== $fa ) {
					$faqs[] = array( $fq, false === stripos( $fa, '<p' ) && false === stripos( $fa, '<ul' ) && false === stripos( $fa, '<ol' ) ? '<p>' . $fa . '</p>' : $fa );
				}
			}
			if ( $faqs ) {
				return $faqs;
			}
		}
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
				// "<strong>Question?</strong> Answer" in one paragraph
				if ( preg_match( '#^\s*<(strong|b)\b[^>]*>(.*?)</\1>\s*(?:[:\x{2013}\x{2014}-]\s*)?(\S.*)$#isu', $t['inner'], $s ) && preg_match( '/\?\s*$/u', blockke_dev_text( $s[2] ) ) && '' !== blockke_dev_text( $s[3] ) ) {
					if ( '' !== $q && $a ) {
						$faqs[] = array( $q, implode( '', $a ) );
					}
					$q      = '';
					$a      = array();
					$faqs[] = array( blockke_dev_text( $s[2] ), wp_kses( '<p>' . $s[3] . '</p>', $allow ) );
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
		if ( preg_match( '/\b(?:for (?:sale|rent)|to let) at (\p{Lu}[^,|—–]+)/u', $t, $m ) || preg_match( '/\bfor (?:sale|rent) at (\p{Lu}[^,|—–]+)/iu', $t, $m ) ) {
			return trim( $m[1] );
		}
		$parts = preg_split( '/\s+[—–]\s+|\s*\|\s*|:\s+/u', $t );
		// "2 Bedroom Apartment for Sale in Westlands — Golden Mansion, Muthithi Road": the building is after the dash
		if ( count( $parts ) > 1 && preg_match( '/^(?:\d|studio|one|two|three|four|five|penthouse|loft|duplex)\b.*\b(?:bed|bedroom|studio|loft|penthouse|duplex|apartment)s?\b/iu', $parts[0] ) ) {
			$b = trim( preg_replace( '/,.*$/u', '', $parts[1] ), ' ,.-' );
			if ( '' !== $b && ! preg_match( '/^(?:from|kes|ksh|\d)/iu', $b ) ) {
				return $b;
			}
		}
		$n = preg_replace( '/\s+(?:(?:apartments?|homes|houses|villas|townhouses|maisonettes|units)\s+)?(?:for\s+(?:sale|rent)|to\s+let)\b.*$/iu', '', $parts[0] );
		return trim( $n, ' ,.-' );
	}

	/* =====================================================================
	   Similar homes, search and unit navigation (v1.4)
	   ===================================================================== */

	/**
	 * The listing's main area: the bke_dev_area override (a slug), else the development's area for a unit, else
	 * its own; of several (e.g. Brookside + Westlands, or a stray Industrial Area tag), the one with most listings.
	 */
	function blockke_dev_main_area( $id ) {
		static $cache = array();
		$id = (int) $id;
		if ( array_key_exists( $id, $cache ) ) {
			return $cache[ $id ];
		}
		$t      = null;
		$master = (int) blockke_dev_pm( $id, 'property_subunits_master' );
		$master = $master && $master !== $id ? $master : 0;
		foreach ( array_filter( array( $id, $master ) ) as $src ) { // the listing's own choice, else its development's
			$slug = sanitize_title( (string) get_post_meta( $src, 'bke_dev_area', true ) );
			if ( '' !== $slug ) {
				$t = get_term_by( 'slug', $slug, 'property_area' );
				if ( $t ) {
					break;
				}
			}
		}
		if ( ! $t ) {
			$terms = $master ? get_the_terms( $master, 'property_area' ) : false;
			if ( ! is_array( $terms ) || ! $terms ) {
				$terms = get_the_terms( $id, 'property_area' );
			}
			if ( is_array( $terms ) && $terms ) {
				usort(
					$terms,
					function ( $a, $b ) {
						return (int) $b->count === (int) $a->count ? (int) $a->term_id - (int) $b->term_id : (int) $b->count - (int) $a->count;
					}
				);
				$t = $terms[0];
			}
		}
		$cache[ $id ] = $t instanceof WP_Term ? $t : null;
		return $cache[ $id ];
	}

	/** Neighbouring areas from config 'neighbours' (slug => array( ring 1 slugs, ring 2 slugs )), as term IDs. */
	function blockke_dev_neighbours( $slug ) {
		$cfg  = blockke_dev_config();
		$map  = isset( $cfg['neighbours'][ $slug ] ) && is_array( $cfg['neighbours'][ $slug ] ) ? array_values( $cfg['neighbours'][ $slug ] ) : array();
		$want = array_merge( isset( $map[0] ) ? (array) $map[0] : array(), isset( $map[1] ) ? (array) $map[1] : array() );
		$ids  = array();
		if ( $want ) {
			$terms = get_terms( array( 'taxonomy' => 'property_area', 'slug' => array_values( array_unique( $want ) ), 'hide_empty' => false ) );
			foreach ( is_array( $terms ) ? $terms : array() as $t ) {
				$ids[ $t->slug ] = (int) $t->term_id;
			}
		}
		$out = array( array(), array() );
		foreach ( array( 0, 1 ) as $r ) {
			foreach ( isset( $map[ $r ] ) ? (array) $map[ $r ] : array() as $s ) {
				if ( isset( $ids[ $s ] ) && ! in_array( $ids[ $s ], $out[0], true ) && ! in_array( $ids[ $s ], $out[1], true ) ) {
					$out[ $r ][] = $ids[ $s ];
				}
			}
		}
		return $out;
	}

	/**
	 * The few listing fields similar homes read, loaded for a whole pool in one query (not every listing's ~130
	 * meta rows, view statistics included). Listings not primed fall back to get_post_meta().
	 */
	function blockke_dev_pm_keys() {
		return array( 'property_bedrooms', 'property_size', 'property_price', 'property_label', 'property_label_before', 'property_subunits_master', 'property_has_subunits', 'prop_featured', '_thumbnail_id', 'bke_completion_stated', 'bke_dev_layout', 'bke_dev_area', 'image_to_attach', 'wpestate_property_gallery', 'bke_index_line' );
	}

	function blockke_dev_pm_prime( $ids, $set = null ) {
		static $map = array();
		if ( null !== $set ) {
			return isset( $map[ $set ] ) ? $map[ $set ] : null;
		}
		global $wpdb;
		$ids = array_values( array_diff( array_map( 'intval', (array) $ids ), array_keys( $map ) ) );
		if ( ! $ids ) {
			return null;
		}
		$keys = blockke_dev_pm_keys();
		$rows = $wpdb->get_results( 'SELECT post_id, meta_key, meta_value FROM ' . $wpdb->postmeta . ' WHERE post_id IN (' . implode( ',', $ids ) . ") AND meta_key IN ('" . implode( "','", array_map( 'esc_sql', $keys ) ) . "')" ); // phpcs:ignore WordPress.DB.PreparedSQL
		foreach ( $ids as $id ) {
			$map[ $id ] = array_fill_keys( $keys, '' );
		}
		foreach ( (array) $rows as $r ) {
			if ( '' === $map[ (int) $r->post_id ][ $r->meta_key ] ) {
				$map[ (int) $r->post_id ][ $r->meta_key ] = (string) $r->meta_value;
			}
		}
		return null;
	}

	function blockke_dev_pm( $id, $key ) {
		$m = blockke_dev_pm_prime( array(), (int) $id );
		return is_array( $m ) && array_key_exists( $key, $m ) ? $m[ $key ] : get_post_meta( (int) $id, $key, true );
	}

	/** What a home is compared on: 'studio', '1', '2', '3' or '4' (4 and more); '' for ranges ("2–4 Bed Lofts", "Studio & 1 Bed"), developments and non-homes. */
	function blockke_dev_sim_key( $id ) {
		$title = strtolower( blockke_dev_text( get_the_title( $id ) ) );
		if ( preg_match( '/(?:\d+|studios?)(?:\s*-?\s*bed(?:room)?s?)?\s*(?:,|&|and|–|—|-|to)\s*\d+\s*(?:-\s*)?(?:bed|br\b)/u', $title ) ) {
			return '';
		}
		$beds  = (int) blockke_dev_pm( $id, 'property_bedrooms' );
		$sqft  = (float) blockke_dev_pm( $id, 'property_size' );
		$named = preg_match( '/\b(?:[1-9]|one|two|three|four|five)\s*-?\s*(?:bed|br\b)/u', $title ); // "1 Bedroom" beats the size guess
		if ( preg_match( '/\bstudio|bedsitter/', $title ) || has_term( 'studio-apartments', 'property_category', $id ) || ( ! $named && $sqft > 0 && $sqft < 500 && $beds <= 1 ) ) {
			return 'studio';
		}
		return $beds > 0 ? (string) min( 4, $beds ) : '';
	}

	/** 'upto' when the price is a ceiling ("Up to"), 'unit' when it is per square foot or metre (offices); else ''. */
	function blockke_dev_price_flag( $id ) {
		$l = strtolower( (string) blockke_dev_pm( $id, 'property_label' ) . ' ' . (string) blockke_dev_pm( $id, 'property_label_before' ) );
		if ( preg_match( '/sq\.?\s*f|sqft|sq\.?\s*m\b|sqm|m²|per\s+(?:sq|square)/u', $l ) ) {
			return 'unit';
		}
		return false !== strpos( $l, 'up to' ) ? 'upto' : '';
	}

	/** 'rent' or 'sale': the Buy/Rent term, else the title ("for rent", "to let"). */
	function blockke_dev_action( $id ) {
		if ( has_term( 'for-rent', 'property_action_category', $id ) && ! has_term( 'for-sale', 'property_action_category', $id ) ) {
			return 'rent';
		}
		if ( has_term( 'for-sale', 'property_action_category', $id ) ) {
			return 'sale';
		}
		return preg_match( '/\bfor rent\b|\bto let\b/i', (string) get_the_title( $id ) ) ? 'rent' : 'sale';
	}

	/** "Off-plan · Sep 2027", "Ready now" or '' for the card badge. */
	function blockke_dev_stage( $id ) {
		if ( ! blockke_dev_is_offplan( $id ) ) {
			return has_term( 'complete', 'property_status', $id ) || has_term( 'ready-now', 'property_completion', $id ) ? 'Ready now' : '';
		}
		$c = blockke_dev_text( blockke_dev_pm( $id, 'bke_completion_stated' ) );
		if ( '' === $c ) {
			$t = blockke_dev_first_term( $id, 'property_completion' );
			$c = $t && 'ready-now' !== $t->slug ? blockke_dev_text( $t->name ) : '';
		}
		return 'Off-plan' . ( '' !== $c ? ' · ' . $c : '' );
	}

	/** One key per development, so similar homes show one card per building: its master, else its project, else itself. */
	function blockke_dev_dev_key( $id ) {
		$m = (int) blockke_dev_pm( $id, 'property_subunits_master' );
		if ( $m && $m !== (int) $id ) {
			return 'm' . $m;
		}
		if ( '1' === (string) blockke_dev_pm( $id, 'property_has_subunits' ) ) {
			return 'm' . (int) $id;
		}
		$p = blockke_dev_first_term( $id, 'property_project' );
		return $p ? 'p' . $p->term_id : 'i' . (int) $id;
	}

	function blockke_dev_sim_row( $pid ) {
		$pid   = (int) $pid;
		$areas = get_the_terms( $pid, 'property_area' );
		$proj  = blockke_dev_first_term( $pid, 'property_project' );
		$m     = (int) blockke_dev_pm( $pid, 'property_subunits_master' );
		return array(
			'id'      => $pid,
			'master'  => $m && $m !== $pid ? $m : 0,
			'is_dev'  => '1' === (string) blockke_dev_pm( $pid, 'property_has_subunits' ),
			'project' => $proj ? (int) $proj->term_id : 0,
			'dkey'    => blockke_dev_dev_key( $pid ),
			'key'     => blockke_dev_sim_key( $pid ),
			'price'   => (float) blockke_dev_pm( $pid, 'property_price' ),
			'flag'    => blockke_dev_price_flag( $pid ),
			'sqft'    => (float) blockke_dev_pm( $pid, 'property_size' ),
			'areas'   => is_array( $areas ) ? array_map( 'intval', wp_list_pluck( $areas, 'term_id' ) ) : array(),
			'offplan' => blockke_dev_is_offplan( $pid ),
			'house'   => blockke_dev_is_house( $pid ),
			'date'    => (int) get_post_time( 'U', true, $pid ),
		);
	}

	/** Villas and houses: their similar homes are villas and houses too, never apartments. */
	function blockke_dev_is_house( $id ) {
		return has_term( array( 'villas-residential', 'houses-residential' ), 'property_category', $id );
	}

	/**
	 * Published homes for similar-homes matching: same Buy/Rent, not sold, not commercial or land, in these areas
	 * (none: anywhere). $key: a bedroom key (matched on property_bedrooms, then checked again on the rows), or
	 * 'dev' for developments (listings with units).
	 */
	function blockke_dev_sim_pool( $action, $area_ids, $key, $limit = 0 ) {
		$cfg = blockke_dev_config();
		$cap = $limit > 0 ? (int) $limit : max( 20, (int) $cfg['pool_cap'] );
		$tax = array(
			'relation' => 'AND',
			array( 'taxonomy' => 'property_action_category', 'field' => 'slug', 'terms' => array( 'rent' === $action ? 'for-rent' : 'for-sale' ) ),
			array( 'taxonomy' => 'property_status', 'field' => 'slug', 'terms' => array( 'sold', 'let' ), 'operator' => 'NOT IN' ),
			array( 'taxonomy' => 'property_category', 'field' => 'slug', 'terms' => array( 'commercial', 'offices', 'warehouses', 'land', 'agricultural', 'industrial-land', 'residential-land' ), 'operator' => 'NOT IN' ),
		);
		if ( $area_ids ) {
			$tax[] = array( 'taxonomy' => 'property_area', 'field' => 'term_id', 'terms' => array_map( 'intval', $area_ids ) );
		}
		if ( 'dev' === $key ) {
			$meta = array( array( 'key' => 'property_has_subunits', 'value' => '1' ) );
		} elseif ( 'studio' === $key ) {
			$meta = array(
				'relation' => 'OR',
				array( 'key' => 'property_bedrooms', 'value' => array( '', '0', '1' ), 'compare' => 'IN' ),
				array( 'key' => 'property_bedrooms', 'compare' => 'NOT EXISTS' ),
			);
		} elseif ( '4' === (string) $key ) {
			$meta = array( array( 'key' => 'property_bedrooms', 'value' => 4, 'compare' => '>=', 'type' => 'NUMERIC' ) );
		} else {
			$meta = array( array( 'key' => 'property_bedrooms', 'value' => (int) $key, 'compare' => '=', 'type' => 'NUMERIC' ) );
		}
		if ( 'dev' !== $key ) { // homes, not developments: keeps masters from using up the cap
			$meta = array(
				'relation' => 'AND',
				$meta,
				array(
					'relation' => 'OR',
					array( 'key' => 'property_has_subunits', 'compare' => 'NOT EXISTS' ),
					array( 'key' => 'property_has_subunits', 'value' => '1', 'compare' => '!=' ),
				),
			);
		}
		$q   = new WP_Query(
			array(
				'post_type'           => 'estate_property',
				'post_status'         => 'publish',
				'fields'              => 'ids',
				'no_found_rows'       => true,
				'ignore_sticky_posts' => true,
				'posts_per_page'      => $cap,
				'orderby'             => 'date',
				'order'               => 'DESC',
				'tax_query'           => $tax, // phpcs:ignore WordPress.DB.SlowDBQuery
				'meta_query'          => $meta, // phpcs:ignore WordPress.DB.SlowDBQuery
			)
		);
		$ids = array_map( 'intval', $q->posts );
		if ( count( $ids ) >= $cap && $limit <= 0 && ! get_transient( 'bkd_sim_cap_warn' ) ) {
			set_transient( 'bkd_sim_cap_warn', 1, DAY_IN_SECONDS );
			error_log( 'BLOCK development page: similar homes pool reached ' . $cap . ' listings (' . $action . ', ' . $key . ', areas ' . implode( ',', (array) $area_ids ) . '); raise pool_cap if matches go missing.' ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions
		}
		if ( $ids && function_exists( '_prime_post_caches' ) ) {
			_prime_post_caches( $ids, true, false ); // posts and terms; the few meta fields come from blockke_dev_pm_prime()
		}
		blockke_dev_pm_prime( $ids );
		$rows = array();
		foreach ( $ids as $pid ) {
			// listings tagged both for sale and for rent: keep each price with its own kind (monthly rents out of sales, sale prices out of rentals)
			$price   = (float) blockke_dev_pm( $pid, 'property_price' );
			$monthly = false !== stripos( (string) blockke_dev_pm( $pid, 'property_label' ), 'month' );
			if ( 'rent' === $action ? ( $price > 2000000 || ( 'rent' !== blockke_dev_action( $pid ) && ! $monthly ) ) : ( $monthly || ( $price > 0 && $price < 1000000 && 'unit' !== blockke_dev_price_flag( $pid ) ) ) ) {
				continue;
			}
			$rows[] = blockke_dev_sim_row( $pid );
		}
		return $rows;
	}

	/** Closeness to the current home: price first, then size, then ready vs off-plan. Lower is closer. */
	function blockke_dev_sim_score( $r, $p0, $s0, $off0 ) {
		$s = $r['price'] > 0 && $p0 > 0 ? abs( log( $r['price'] / $p0 ) ) : 1.0;
		if ( $r['sqft'] > 0 && $s0 > 0 ) {
			$s += 0.5 * abs( log( $r['sqft'] / $s0 ) );
		}
		return $s + ( $r['offplan'] !== $off0 ? 0.1 : 0 );
	}

	/** The editor's "Similar listings (IDs)" for this page, in their order (tier 0): published, same Buy/Rent, not sold or let, one per development. */
	function blockke_dev_sim_picks( $d, $n, &$seen ) {
		$c      = $d['ctx'];
		$picked = array();
		$manual = array_values( array_filter( array_map( 'intval', preg_split( '/[\s,]+/', (string) get_post_meta( $d['id'], 'bke_dev_similar', true ) ) ) ) );
		if ( ! $manual ) {
			return $picked;
		}
		if ( function_exists( '_prime_post_caches' ) ) {
			_prime_post_caches( $manual, true, false );
		}
		blockke_dev_pm_prime( $manual );
		foreach ( $manual as $pid ) {
			if ( count( $picked ) >= $n ) {
				break;
			}
			if ( $pid === (int) $d['id'] || 'estate_property' !== get_post_type( $pid ) || 'publish' !== get_post_status( $pid ) || blockke_dev_action( $pid ) !== $c['action'] || has_term( array( 'sold', 'let' ), 'property_status', $pid ) ) {
				continue;
			}
			$r = blockke_dev_sim_row( $pid );
			if ( isset( $seen[ $r['dkey'] ] ) ) {
				continue;
			}
			$seen[ $r['dkey'] ] = 1;
			$r['tier']          = 0;
			$picked[]           = $r;
		}
		return $picked;
	}

	/**
	 * Fills up to similar_count homes, tier by tier: the editor's picks (0); the same bedrooms in the main area
	 * within the price band (1), then at any price (2); in ring-1 neighbouring areas (3); one bedroom more or fewer
	 * in the main area within the band (4); the same bedrooms in ring-2 areas, only while under similar_min (5).
	 * One card per development, never this development, never sold, commercial, per-sq-ft priced or rentals for sales.
	 */
	function blockke_dev_similar_units( $d ) {
		$cfg    = blockke_dev_config();
		$c      = $d['ctx'];
		$n      = max( 1, (int) $cfg['similar_count'] );
		$band   = (float) $cfg['price_band'];
		$main   = (int) $c['area_id'];
		$rings  = $main ? blockke_dev_neighbours( $c['area_slug'] ) : array( array(), array() );
		$picked = array();
		$seen   = array( $c['dkey'] => 1 );
		$skip   = function ( $r ) use ( $c, $d ) {
			return (int) $d['id'] === $r['id'] || ( $c['master_id'] && ( $r['master'] === $c['master_id'] || $r['id'] === $c['master_id'] ) )
				|| ( $c['project_id'] && $r['project'] === $c['project_id'] ) || 'unit' === $r['flag'] || ( $c['house'] && ! $r['house'] );
		};
		$add    = function ( $rows, $tier, $banded, $keys ) use ( &$picked, &$seen, $n, $c, $band, $skip ) {
			foreach ( $rows as $i => $r ) {
				$rows[ $i ]['score'] = blockke_dev_sim_score( $r, $c['price'], $c['sqft'], $c['offplan'] );
			}
			usort(
				$rows,
				function ( $a, $b ) {
					return $a['score'] === $b['score'] ? $b['date'] - $a['date'] : ( $a['score'] < $b['score'] ? -1 : 1 );
				}
			);
			foreach ( $rows as $r ) {
				if ( count( $picked ) >= $n ) {
					break;
				}
				if ( isset( $seen[ $r['dkey'] ] ) || $r['is_dev'] || ! in_array( $r['key'], $keys, true ) || $skip( $r ) ) {
					continue;
				}
				if ( $banded && ( $c['price'] <= 0 || $r['price'] <= 0 || 'upto' === $r['flag'] || 'upto' === $c['flag'] || abs( $r['price'] - $c['price'] ) > $band * $c['price'] ) ) {
					continue;
				}
				$seen[ $r['dkey'] ] = 1;
				$r['tier']          = $tier;
				$picked[]           = $r;
			}
		};

		$picked = array_merge( $picked, blockke_dev_sim_picks( $d, $n, $seen ) ); // 0: the editor's picks, in their order

		if ( $main ) {
			$key  = $c['key'];
			$pool = blockke_dev_sim_pool( $c['action'], array_merge( array( $main ), $rings[0], $rings[1] ), $key );
			$in_m = array();
			$in_1 = array();
			$in_2 = array();
			foreach ( $pool as $r ) {
				if ( in_array( $main, $r['areas'], true ) ) {
					$in_m[] = $r;
				} elseif ( array_intersect( $r['areas'], $rings[0] ) ) {
					$in_1[] = $r;
				} elseif ( array_intersect( $r['areas'], $rings[1] ) ) {
					$in_2[] = $r;
				}
			}
			$add( $in_m, 1, true, array( $key ) );
			$add( $in_m, 2, false, array( $key ) );
			$add( $in_1, 3, true, array( $key ) );
			$add( $in_1, 3, false, array( $key ) );
			if ( count( $picked ) < $n ) {
				$adj  = array(
					'studio' => array( '1' ),
					'1'      => array( 'studio', '2' ),
					'2'      => array( '1', '3' ),
					'3'      => array( '2', '4' ),
					'4'      => array( '3' ),
				);
				$near = array();
				foreach ( isset( $adj[ $key ] ) ? $adj[ $key ] : array() as $k2 ) {
					foreach ( blockke_dev_sim_pool( $c['action'], array( $main ), $k2 ) as $r ) {
						$near[] = $r;
					}
				}
				$add( $near, 4, true, isset( $adj[ $key ] ) ? $adj[ $key ] : array() );
			}
			if ( count( $picked ) < (int) $cfg['similar_min'] ) {
				$add( $in_2, 5, false, array( $key ) );
			}
		}
		return array( 'mode' => 'units', 'rows' => $picked );
	}

	/** Developments (listings with units) like this one: in the main area near its "from" price, then anywhere in it, then neighbouring areas, then the newest. */
	function blockke_dev_similar_devs( $d ) {
		$cfg    = blockke_dev_config();
		$c      = $d['ctx'];
		$n      = max( 1, (int) $cfg['similar_count'] );
		$band   = (float) $cfg['dev_price_band'];
		$p0     = (float) $d['price_from'];
		$main   = (int) $c['area_id'];
		$rings  = $main ? blockke_dev_neighbours( $c['area_slug'] ) : array( array(), array() );
		$seen   = array( $c['dkey'] => 1 );
		$picked = blockke_dev_sim_picks( $d, $n, $seen );
		$add    = function ( $rows, $tier, $banded, $by_date = false ) use ( &$picked, &$seen, $n, $c, $d, $band, $p0 ) {
			usort(
				$rows,
				function ( $a, $b ) use ( $p0, $by_date ) {
					$x = $by_date || $p0 <= 0 ? 0 : abs( log( max( 1, $a['price'] ) / $p0 ) ) - abs( log( max( 1, $b['price'] ) / $p0 ) );
					return 0 == $x ? $b['date'] - $a['date'] : ( $x < 0 ? -1 : 1 ); // phpcs:ignore Universal.Operators.StrictComparisons
				}
			);
			foreach ( $rows as $r ) {
				if ( count( $picked ) >= $n ) {
					break;
				}
				if ( isset( $seen[ $r['dkey'] ] ) || ! $r['is_dev'] || (int) $d['id'] === $r['id'] || ( $c['master_id'] && $r['id'] === $c['master_id'] ) || ( $c['project_id'] && $r['project'] === $c['project_id'] ) || ( $c['house'] && ! $r['house'] ) ) {
					continue;
				}
				if ( $banded && ( $p0 <= 0 || $r['price'] <= 0 || abs( $r['price'] - $p0 ) > $band * $p0 ) ) {
					continue;
				}
				$seen[ $r['dkey'] ] = 1;
				$r['tier']          = $tier;
				$picked[]           = $r;
			}
		};
		if ( $main ) {
			$pool = blockke_dev_sim_pool( $c['action'], array_merge( array( $main ), $rings[0], $rings[1] ), 'dev' );
			$in_m = array();
			$in_1 = array();
			$in_2 = array();
			foreach ( $pool as $r ) {
				if ( in_array( $main, $r['areas'], true ) ) {
					$in_m[] = $r;
				} elseif ( array_intersect( $r['areas'], $rings[0] ) ) {
					$in_1[] = $r;
				} elseif ( array_intersect( $r['areas'], $rings[1] ) ) {
					$in_2[] = $r;
				}
			}
			$add( $in_m, 1, true );
			$add( $in_m, 2, false );
			$add( $in_1, 3, false );
			$add( $in_2, 5, false );
		}
		if ( count( $picked ) < (int) $cfg['similar_min'] ) {
			$add( blockke_dev_sim_pool( $c['action'], array(), 'dev', $c['house'] ? 0 : 3 * $n ), 9, false, ! $c['house'] ); // villas: the closest prices, not the newest
		}
		return array( 'mode' => 'devs', 'rows' => $picked );
	}

	/** "2-bedroom homes", "studios", "4+ bedroom rentals". */
	function blockke_dev_key_phrase( $key, $rent ) {
		if ( 'studio' === $key ) {
			return $rent ? 'studio rentals' : 'studios';
		}
		if ( '' === (string) $key ) {
			return $rent ? 'rentals' : 'homes';
		}
		return ( '4' === (string) $key ? '4+ bedroom ' : $key . '-bedroom ' ) . ( $rent ? 'rentals' : 'homes' );
	}

	/**
	 * Bedrooms for links into the site search: '' for studios and ranges. With beds_mode 'exact' a 5-bed home links
	 * to 5 bedrooms (similar homes still group 4 and more); with 'min' the search reads "at least", so 4 stays 4.
	 */
	function blockke_dev_link_beds( $c ) {
		$cfg = blockke_dev_config();
		if ( 'studio' === $c['key'] && '' !== (string) $cfg['studio_cat'] ) {
			return 'studio';
		}
		if ( ! ctype_digit( (string) $c['key'] ) ) {
			return '';
		}
		return '4' === (string) $c['key'] && 'min' !== $cfg['beds_mode'] ? (string) min( 6, max( 4, (int) $c['beds'] ) ) : (string) $c['key'];
	}

	/** "2-bed", or "2+ bed" when the site search reads bedrooms as a minimum; '' for studios and ranges. */
	function blockke_dev_beds_label( $c ) {
		$cfg = blockke_dev_config();
		$b   = blockke_dev_link_beds( $c );
		if ( 'studio' === $b ) {
			return 'studio';
		}
		return '' === $b ? '' : ( 'min' === $cfg['beds_mode'] ? $b . '+ bed' : $b . '-bed' );
	}

	/** A link into the site's search results: area name, Buy/Rent, bedrooms and a price range. */
	function blockke_dev_search_url( $q ) {
		$cfg  = blockke_dev_config();
		$base = '' !== trim( (string) $cfg['search_url'] ) ? (string) $cfg['search_url'] : home_url( '/advanced-search/' );
		$p    = array();
		if ( ! empty( $q['area'] ) ) {
			$p[] = 'location=' . rawurlencode( $q['area'] );
		}
		$p[] = rawurlencode( 'property_action_category[]' ) . '=' . ( isset( $q['action'] ) && 'rent' === $q['action'] ? 'for-rent' : 'for-sale' );
		if ( ! empty( $q['beds'] ) && 'studio' === $q['beds'] && '' !== (string) $cfg['studio_cat'] ) {
			$p[] = rawurlencode( 'property_category[]' ) . '=' . rawurlencode( (string) $cfg['studio_cat'] );
		} elseif ( ! empty( $q['beds'] ) && ctype_digit( (string) $q['beds'] ) ) {
			$p[] = 'componentsbeds=' . (int) $q['beds'];
		}
		return $base . ( false === strpos( $base, '?' ) ? '?' : '&' ) . implode( '&', $p );
	}

	/**
	 * While the v1.6 area pages are on (card_v16): the area segment page a search link stands for, as search would redirect
	 * it ("?location=Kilimani&property_action_category[]=for-sale" → /property_area/kilimani/homes-for-sale/, with ?beds=2
	 * kept), at the page's canonical when it has no filter. '' when there is none (switch off, area page off, nothing listed).
	 */
	function blockke_dev_seg_url( $url ) {
		if ( ! function_exists( 'blockke_search_redirect_url' ) || ! blockke_bkn_on( 'card_v16' ) ) {
			return '';
		}
		parse_str( (string) wp_parse_url( $url, PHP_URL_QUERY ), $get );
		$q = blockke_search_parse( $get );
		if ( ! $q || 1 !== count( $q['area'] ) || 1 !== count( $q['for'] ) ) {
			return '';
		}
		$to   = blockke_search_redirect_url( $q, $get );
		$term = get_term_by( 'slug', $q['area'][0], 'property_area' );
		$seg  = ( $q['types'] ? $q['types'][0] : 'homes' ) . '-for-' . $q['for'][0];
		if ( '' !== $to && $term instanceof WP_Term && blockke_area_url( $term, $seg, false ) === $to ) {
			$to = blockke_area_page( $term, $seg )['canon']; // a segment that is all its area has points at the area page
		}
		return $to;
	}

	/** "2 bed · 111 m² · Westlands", or for a development "1–3 bed · Westlands". $area: the page's area name, when the listing is also tagged with it. */
	function blockke_dev_sim_meta( $r, $area = '' ) {
		$m = array();
		if ( $r['is_dev'] ) {
			if ( preg_match( '/((?:studio|\d+)(?:\s*(?:,|&|and|–|—|-|to)\s*(?:studio|\d+))+)\s*(?:-\s*)?(?:bed|br\b)/iu', blockke_dev_text( get_the_title( $r['id'] ) ), $mm ) ) {
				preg_match_all( '/studio|\d+/i', $mm[1], $all );
				$lo  = $all[0][0];
				$hi  = end( $all[0] );
				$m[] = ( is_numeric( $lo ) ? $lo : 'Studio' ) . '–' . $hi . ' bed';
			}
		} else {
			$m[] = 'studio' === $r['key'] ? 'Studio' : ( '' !== $r['key'] ? ( '4' === $r['key'] ? max( 4, (int) get_post_meta( $r['id'], 'property_bedrooms', true ) ) : $r['key'] ) . ' bed' : '' );
			if ( $r['sqft'] > 0 ) {
				$m[] = blockke_dev_num( round( $r['sqft'] * blockke_dev_sqft_factor() ) ) . ' m²';
			}
		}
		$a = '' !== $area ? null : blockke_dev_main_area( $r['id'] );
		$m[] = '' !== $area ? $area : ( $a ? blockke_dev_text( $a->name ) : '' );
		return implode( ' · ', array_filter( $m ) );
	}

	function blockke_dev_sim_price( $r, $rent ) {
		if ( $r['price'] <= 0 ) {
			return 'Price on request';
		}
		$p = blockke_dev_short( $r['price'] );
		if ( $rent ) {
			return $p . ' / month';
		}
		if ( 'upto' === $r['flag'] ) {
			return 'Up to ' . $p;
		}
		return $r['is_dev'] || $r['offplan'] ? 'From ' . $p : $p;
	}

	/**
	 * The "Also consider" block for a development page (never a landing page): homes like this one, else
	 * developments like it, else an empty state with ways forward. Cached per listing (transient bkd_sim_{id}) and
	 * rebuilt when any listing changes (option bkd_sim_ver), or after sim_ttl.
	 */
	function blockke_dev_similar_block( $d ) {
		$cfg = blockke_dev_config();
		$c   = $d['ctx'];
		if ( $c['sim_off'] ) {
			return null;
		}
		$ver = (string) get_option( 'bkd_sim_ver', '0' ) . '|v144|' . md5( wp_json_encode( array( $cfg['similar_count'], $cfg['similar_min'], $cfg['price_band'], $cfg['dev_price_band'], $cfg['neighbours'], $cfg['similar_units'], $cfg['beds_mode'], $cfg['search_url'], $cfg['studio_cat'], ! empty( $cfg['card_v16'] ) ? $cfg['area_pages'] : 0 ) ) );
		$tk  = 'bkd_sim_' . (int) $d['id'];
		$hit = get_transient( $tk );
		if ( is_array( $hit ) && isset( $hit['ver'] ) && $hit['ver'] === $ver ) {
			return $hit['block'];
		}
		$rent  = 'rent' === $c['action'];
		$res   = null;
		$units = null;
		$min   = (int) $cfg['similar_min'];
		$picks = function ( $r ) {
			return in_array( 0, array_map( 'intval', wp_list_pluck( $r['rows'], 'tier' ) ), true );
		};
		if ( 'master' !== $c['kind'] && '' !== $c['key'] && ! empty( $cfg['similar_units'] ) ) {
			$units = blockke_dev_similar_units( $d );
			if ( count( $units['rows'] ) >= $min || $picks( $units ) ) { // the editor's picks are never thrown away
				$res = $units;
			}
		}
		if ( ! $res ) {
			$res = blockke_dev_similar_devs( $d );
			if ( count( $res['rows'] ) < $min && ! $picks( $res ) ) {
				// developments are short too: show the one or two homes found rather than "none"
				$res = $units && $units['rows'] ? $units : ( $res['rows'] ? $res : array( 'mode' => 'empty', 'rows' => array() ) );
			}
		}
		$area   = $c['area_name'];
		$em     = function ( $s ) {
			return '<em>' . esc_html( $s ) . '</em>';
		};
		$tiers  = array_values( array_unique( array_map( 'intval', wp_list_pluck( $res['rows'], 'tier' ) ) ) );
		$around = (bool) array_intersect( $tiers, array( 3, 5 ) );
		$mixed  = false; // the editor's picks: a different kind of home, or outside the area
		foreach ( $res['rows'] as $r ) {
			if ( 0 === (int) $r['tier'] ) {
				$mixed  = $mixed || ( 'units' === $res['mode'] && ( $r['is_dev'] || $r['key'] !== $c['key'] ) );
				$around = $around || ! $c['area_id'] || ! in_array( $c['area_id'], $r['areas'], true );
			}
		}
		$phrase = blockke_dev_key_phrase( 'units' === $res['mode'] ? $c['key'] : '', $rent );
		$beds   = 'units' === $res['mode'] ? blockke_dev_beds_label( $c ) : '';
		if ( 'units' === $res['mode'] ) {
			if ( $mixed || in_array( 4, $tiers, true ) ) {
				$title = 'Similar ' . ( $rent ? 'rentals' : 'homes' ) . ( $area ? ( $around ? ' in and around ' : ' in ' ) . $em( $area ) : '' );
			} elseif ( $around && $area ) {
				$title = esc_html( ucfirst( $phrase ) ) . ' in and around ' . $em( $area );
			} else {
				$title = 'Other ' . esc_html( $phrase ) . ( $area ? ' in ' . $em( $area ) : '' );
			}
		} elseif ( 'devs' === $res['mode'] ) {
			if ( in_array( 9, $tiers, true ) || ! $area ) {
				$title = 'More new ' . $em( 'developments' );
			} else {
				$title = ( $around ? 'Developments in and around ' : 'More developments in ' ) . $em( $area );
			}
		} else {
			$want  = blockke_dev_key_phrase( $c['key'], $rent );
			$dev   = $c['master_id'] ? $c['master_name'] : ( 'master' === $c['kind'] || $c['project_id'] ? $d['short'] : '' );
			$title = 'No other ' . esc_html( $want ) . ( $area ? ' in ' . esc_html( $area ) : '' ) . ( '' !== $dev ? ' outside ' . esc_html( $dev ) : '' ) . ' listed right now'; // its own homes are left out on purpose
		}
		$all_url  = blockke_dev_search_url( array( 'area' => 'devs' === $res['mode'] && in_array( 9, $tiers, true ) ? '' : $area, 'action' => $c['action'], 'beds' => 'units' === $res['mode'] ? blockke_dev_link_beds( $c ) : '' ) );
		$all_seg  = blockke_dev_seg_url( $all_url );
		$all_text = 'See all ' . ( $beds ? $beds . ' ' : '' ) . ( $rent ? 'rentals' : ( $beds ? 'homes' : 'homes for sale' ) ) . ( $area && ! ( 'devs' === $res['mode'] && in_array( 9, $tiers, true ) ) ? ' in ' . $area : '' );
		$cards    = array();
		foreach ( $res['rows'] as $i => $r ) {
			$cards[] = array(
				'id'    => $r['id'],
				'url'   => get_permalink( $r['id'] ),
				'img'   => (int) get_post_thumbnail_id( $r['id'] ),
				'name'  => blockke_dev_listing_name( $r['id'] ),
				'price' => blockke_dev_sim_price( $r, $rent ),
				'meta'  => blockke_dev_sim_meta( $r, $c['area_id'] && in_array( $c['area_id'], $r['areas'], true ) ? $area : '' ),
				'badge' => blockke_dev_stage( $r['id'] ),
				'tier'  => (int) $r['tier'],
				'same'  => $c['area_id'] && in_array( $c['area_id'], $r['areas'], true ) ? 1 : 0,
			);
		}
		$block = array(
			'mode'     => $res['mode'],
			'title'    => $title,
			'all_url'  => '' !== $all_seg ? $all_seg : $all_url,
			'all_nf'   => '' === $all_seg && blockke_bkn_on( 'card_v16' ), // still a search (noindex): not followed, as the search tools
			'all_text' => $all_text,
			'cards'    => $cards,
			'tier'     => $tiers ? (int) max( $tiers ) : 0,
			'beds'     => (string) $c['key'],
			'area'     => $area,
		);
		set_transient( $tk, array( 'ver' => $ver, 'block' => $block ), max( 300, (int) $cfg['sim_ttl'] ) );
		return $block;
	}

	/** The other homes in this home's development, for the unit switcher: studio first, then by bedrooms and price. */
	function blockke_dev_unit_nav( $d ) {
		$c = $d['ctx'];
		if ( 'unit' !== $c['kind'] || ! $c['master_id'] ) {
			return array();
		}
		$subs = blockke_dev_subunits( $c['master_id'] );
		if ( count( $subs ) < 2 ) {
			return array();
		}
		$rank = function ( $k ) {
			return 'studio' === $k ? 0 : ( is_int( $k ) ? $k : ( 'loft' === $k ? 50 : ( 'penthouse' === $k ? 60 : 99 ) ) );
		};
		usort(
			$subs,
			function ( $a, $b ) use ( $rank ) {
				$x = $rank( $a['key'] ) - $rank( $b['key'] );
				return 0 !== $x ? $x : ( $a['price'] < $b['price'] ? -1 : ( $a['price'] > $b['price'] ? 1 : 0 ) );
			}
		);
		$labels = array();
		foreach ( $subs as $s ) {
			$labels[] = blockke_dev_group_label( $s['key'], $s['label'] );
		}
		$counts = array_count_values( $labels );
		$out    = array();
		foreach ( $subs as $i => $s ) {
			$out[] = array(
				'id'      => (int) $s['id'],
				'label'   => $counts[ $labels[ $i ] ] > 1 && '' !== $s['label'] ? $s['label'] : $labels[ $i ],
				'price'   => (float) $s['price'],
				'url'     => $s['url'],
				'current' => (int) $s['id'] === (int) $d['id'],
			);
		}
		return $out;
	}

	/** Areas for the search sheet (the busiest first) and completion years, cached until a listing changes. */
	function blockke_dev_search_lists() {
		$cfg = blockke_dev_config();
		$ver = (string) get_option( 'bkd_sim_ver', '0' ) . '|' . (int) $cfg['search_min_area_count'];
		$hit = get_transient( 'bkd_search_lists' );
		if ( is_array( $hit ) && isset( $hit['ver'] ) && $hit['ver'] === $ver ) {
			return $hit['lists'];
		}
		$areas = array();
		$terms = get_terms( array( 'taxonomy' => 'property_area', 'hide_empty' => true, 'orderby' => 'count', 'order' => 'DESC', 'number' => 120 ) );
		foreach ( is_array( $terms ) ? $terms : array() as $t ) {
			if ( (int) $t->count >= (int) $cfg['search_min_area_count'] ) {
				$areas[] = blockke_dev_text( $t->name );
			}
		}
		$years = array();
		$terms = taxonomy_exists( 'property_completion' ) ? get_terms( array( 'taxonomy' => 'property_completion', 'hide_empty' => true ) ) : array();
		foreach ( is_array( $terms ) ? $terms : array() as $t ) {
			$years[ $t->slug ] = 'ready-now' === $t->slug ? 'Ready now' : blockke_dev_text( $t->name );
		}
		uksort(
			$years,
			function ( $a, $b ) {
				return 'ready-now' === $a ? -1 : ( 'ready-now' === $b ? 1 : strcmp( $a, $b ) );
			}
		);
		$lists = array( 'areas' => $areas, 'years' => $years );
		set_transient( 'bkd_search_lists', array( 'ver' => $ver, 'lists' => $lists ), 12 * HOUR_IN_SECONDS );
		return $lists;
	}

	/** The search sheet: Buy/Rent, area, bedrooms, budget and completion, filled in from this listing. */
	function blockke_dev_search_sheet( $d ) {
		$cfg   = blockke_dev_config();
		$c     = $d['ctx'];
		$lists = blockke_dev_search_lists();
		$areas = $lists['areas'];
		if ( $c['area_name'] && ! in_array( $c['area_name'], $areas, true ) ) {
			array_unshift( $areas, $c['area_name'] );
		}
		$rent  = 'rent' === $c['action'];
		$beds  = blockke_dev_link_beds( $c );
		$beds  = ctype_digit( $beds ) && 'min' !== $cfg['beds_mode'] ? (string) min( 5, (int) $beds ) : $beds;
		$bands = isset( $cfg['budget_bands'][ $rent ? 'rent' : 'sale' ] ) ? (array) $cfg['budget_bands'][ $rent ? 'rent' : 'sale' ] : array();
		$pick  = '';
		if ( $c['price'] > 0 && ! $c['flag'] ) {
			foreach ( $bands as $b ) {
				if ( $c['price'] >= (float) $b[0] && ( (float) $b[1] <= 0 || $c['price'] < (float) $b[1] ) ) {
					$pick = (float) $b[0] . '-' . (float) $b[1];
					break;
				}
			}
		}
		$opt = function ( $v, $l, $sel ) {
			return '<option value="' . esc_attr( $v ) . '"' . selected( (string) $v, (string) $sel, false ) . '>' . esc_html( $l ) . '</option>';
		};
		$h  = '<div class="bkd-md bkd-sf" id="bkd-sf" hidden role="dialog" aria-modal="true" aria-labelledby="bkd-sf-t"><div class="bkd-md-box">'
			. '<button class="bkd-md-x" type="button" data-bkd-sfclose aria-label="Close search">' . blockke_dev_icon( 'close' ) . '</button>'
			. '<form class="bkd-sf-form" method="get" action="' . esc_url( preg_replace( '/\?.*$/', '', blockke_dev_search_url( array() ) ) ) . '" role="search">'
			. '<div class="bkd-sf-head"><p class="bkd-label">Search homes</p><h2 class="bkd-h2" id="bkd-sf-t">' . ( ! empty( $d['sf_title'] ) ? $d['sf_title'] : 'Find homes <em>like this one</em>' ) . '</h2></div><div class="bkd-sf-body">';
		$h .= '<fieldset class="bkd-f bkd-seg bkd-sf-wide"><legend>Looking to</legend><div class="bkd-chips">'
			. '<label class="bkd-chip"><input type="radio" name="property_action_category[]" value="for-sale"' . checked( ! $rent, true, false ) . '><span>Buy</span></label>'
			. '<label class="bkd-chip"><input type="radio" name="property_action_category[]" value="for-rent"' . checked( $rent, true, false ) . '><span>Rent</span></label></div></fieldset>';
		$h .= '<label class="bkd-f"><span>Area</span><select name="location"><option value="">Any area</option>';
		foreach ( $areas as $a ) {
			$h .= $opt( $a, $a, $c['area_name'] );
		}
		$h .= '</select></label>';
		$h .= '<label class="bkd-f"><span>Budget</span><select data-sf-budget data-mode="' . ( $rent ? 'rent' : 'sale' ) . '"><option value="">Any budget</option>';
		foreach ( $bands as $b ) {
			$h .= $opt( (float) $b[0] . '-' . (float) $b[1], $b[2], $pick );
		}
		$h .= '</select></label><input type="hidden" name="price_low" disabled><input type="hidden" name="price_max" disabled>';
		$h .= '<fieldset class="bkd-f bkd-sf-wide"><legend>Bedrooms</legend><div class="bkd-chips">';
		$chips = 'min' === $cfg['beds_mode'] ? array( '' => 'Any', '1' => '1+', '2' => '2+', '3' => '3+', '4' => '4+' ) : array( '' => 'Any', '1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5' );
		if ( '' !== (string) $cfg['studio_cat'] ) {
			$chips = array_slice( $chips, 0, 1, true ) + array( 'studio' => 'Studio' ) + array_slice( $chips, 1, null, true );
		}
		foreach ( $chips as $v => $l ) {
			$h .= '<label class="bkd-chip"><input type="radio" name="componentsbeds" value="' . esc_attr( $v ) . '"' . checked( (string) $v, $beds, false ) . '><span>' . esc_html( $l ) . '</span></label>';
		}
		$h .= '</div></fieldset>';
		if ( '' !== (string) $cfg['studio_cat'] ) { // sent instead of componentsbeds when Studio is picked
			$h .= '<input type="hidden" name="property_category[]" value="' . esc_attr( $cfg['studio_cat'] ) . '" data-sf-studio disabled>';
		}
		if ( $lists['years'] ) {
			$h .= '<label class="bkd-f bkd-sf-wide" data-sf-comp' . ( $rent ? ' hidden' : '' ) . '><span>Completion</span><select name="completion"><option value="">Any time</option>';
			foreach ( $lists['years'] as $slug => $label ) {
				$h .= $opt( $slug, $label, '' );
			}
			$h .= '</select></label>';
		}
		$h .= '</div><div class="bkd-sf-foot"><button class="bkd-btn bkd-btn-navy bkd-btn-block" type="submit">Show homes' . blockke_dev_icon( 'arrow' ) . '</button>'
			. '<button class="bkd-link" type="button" data-sf-shortlist>Or ask an advisor to shortlist for me</button></div></form></div></div>';
		return $h;
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
		$area_t    = blockke_dev_main_area( $id );
		$city_t    = blockke_dev_first_term( $id, 'property_city' );
		$area      = $area_t ? blockke_dev_text( $area_t->name ) : '';
		// "Santorini Residences Westlands" -> "Santorini Residences": the area is already shown above the name.
		if ( $project && $area && 0 === stripos( $name, $short ) && 0 === strcasecmp( trim( substr( $name, strlen( $short ) ) ), $area ) ) {
			$name = $short;
		}
		$area_line = implode( ', ', array_unique( array_filter( array( $area, $city_t ? blockke_dev_text( $city_t->name ) : '' ) ) ) );
		$address   = blockke_dev_text( get_post_meta( $id, 'property_address', true ) );
		if ( '' === $address ) {
			// "Muthithi Road, Westlands, Nairobi (off Waiyaki Way), directly opposite ..." -> "Muthithi Road, Westlands"
			$address = preg_replace( '/\s*\(.*$|\s+[—–]\s.*$/u', '', blockke_dev_kv_get( $kv, '/^location|^address/i' ) );
			$address = preg_replace( '/(,\s*(Nairobi|Kenya))+$/i', '', $address );
			if ( mb_strlen( $address ) > 60 ) {
				$address = implode( ',', array_slice( explode( ',', $address ), 0, 2 ) );
			}
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
			if ( 'p' === $t['tag'] && ! $intro_p && '' === $rest && '' !== $tx && mb_strlen( $tx ) <= 110 && ! preg_match( '/[.!?…:;"”)]$/u', $tx ) && ! preg_match( '/<(?:a|img)\b/i', $t['inner'] ) ) {
				continue; // a title-like first line ("3 Bedroom Villa at Tilisi Views, Limuru"): the H1 already says it
			}
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
		$li      = blockke_dev_find( $secs, '/location|neighbou?rhood|the area|getting around|connectivity/i' );
		$loc     = array( 'intro' => '', 'places' => array(), 'note' => '' );
		$loc_src = '';
		if ( $li >= 0 ) {
			foreach ( $secs[ $li ]['tokens'] as $t ) {
				if ( 'p' === $t['tag'] ) {
					$tx = blockke_dev_text( $t['inner'] );
					if ( '' === $loc['intro'] && ! $loc['places'] && mb_strlen( $tx ) > 40 ) {
						$loc_src      = $tx;
						$loc['intro'] = blockke_dev_fit_sentences( $tx, 520 );
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
		} elseif ( ! $loc['places'] ) { // a unit page: its development's box before straight-line distances
			$loc['places'] = blockke_dev_master_places( $id );
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
		$faqs = $fi >= 0 ? blockke_dev_faqs( $secs[ $fi ]['tokens'], $secs[ $fi ]['html'] ) : array();
		$fsub = $fi < 0 ? blockke_dev_sub_section( $parsed, '/faq|frequently asked/i' ) : null; // an H3 "Frequently Asked Questions" inside an H2 section
		if ( $fsub ) {
			$faqs = blockke_dev_faqs( $fsub['tokens'], $fsub['html'] );
		}
		$desc = $rest;
		foreach ( $secs as $i => $s ) {
			if ( $i !== $fi && $i !== $ci ) {
				$desc .= $s['html'];
			}
		}
		if ( $fsub && $faqs ) {
			$desc = str_replace( $fsub['html'], '', $desc );
		}
		$desc = preg_replace( array( '#<(/?)h5\b#i', '#<(/?)h4\b#i', '#<(/?)h3\b#i', '#<(/?)h2\b#i' ), array( '<$1h6', '<$1h5', '<$1h4', '<$1h3' ), $desc );

		// Where this home sits: unit, development or single listing, and what it is compared on
		$master_id = (int) get_post_meta( $id, 'property_subunits_master', true );
		if ( $master_id === $id || 'publish' !== get_post_status( $master_id ) || 'estate_property' !== get_post_type( $master_id ) ) {
			$master_id = 0;
		}
		$kind = $master_id ? 'unit' : ( '1' === (string) get_post_meta( $id, 'property_has_subunits', true ) ? 'master' : 'single' );
		$ctx  = array(
			'kind'        => $kind,
			'master_id'   => $master_id,
			'master_name' => $master_id ? blockke_dev_listing_name( $master_id ) : '',
			'master_url'  => $master_id ? get_permalink( $master_id ) : '',
			'project_id'  => $project ? (int) $project->term_id : 0,
			'dkey'        => blockke_dev_dev_key( $id ),
			'key'         => 'master' === $kind ? '' : blockke_dev_sim_key( $id ),
			'beds'        => (int) get_post_meta( $id, 'property_bedrooms', true ),
			'price'       => $meta_price,
			'flag'        => blockke_dev_price_flag( $id ),
			'sqft'        => (float) get_post_meta( $id, 'property_size', true ),
			'offplan'     => $offplan,
			'action'      => blockke_dev_action( $id ),
			'area_id'     => $area_t ? (int) $area_t->term_id : 0,
			'area_slug'   => $area_t ? $area_t->slug : '',
			'area_name'   => $area,
			'sim_off'     => '' !== (string) get_post_meta( $id, 'bke_dev_similar_off', true ),
			'house'       => blockke_dev_is_house( $id ),
		);

		// A unit without a project term is still named after its development (not "2 Bedroom Apartment...")
		if ( ! $project && 'unit' === $kind && '' !== $ctx['master_name'] && '' === blockke_dev_text( get_post_meta( $id, 'bke_dev_name', true ) ) ) {
			$short = $ctx['master_name'];
		}

		// Breadcrumb: Home / For Sale / Westlands / Golden Mansion / 2 Bedroom Apartment
		$crumbs = array();
		foreach ( array( $action, $area_t ) as $t ) {
			if ( $t ) {
				$link = get_term_link( $t );
				if ( ! is_wp_error( $link ) ) {
					$crumbs[] = array( blockke_dev_text( $t->name ), $link );
				}
			}
		}
		if ( $master_id ) {
			$crumbs[] = array( $ctx['master_name'], $ctx['master_url'] );
		}
		$here = $short;
		if ( 'unit' === $kind ) {
			$here = blockke_dev_text( get_post_meta( $id, 'bke_unit_label', true ) );
			$here = '' !== $here ? $here : blockke_dev_unit_label_from_title( $title );
		}

		// Photo labels: the caption on the main photo and the note under the gallery. Off-plan pages say
		// "Project render" / "Project renders." unless the listing sets its own in bke_dev_hero_label and
		// bke_dev_gallery_note (e.g. "Show apartment" / "Photos of the show apartment."); "none" hides one.
		$hero_label   = blockke_dev_text( (string) get_post_meta( $id, 'bke_dev_hero_label', true ) );
		$gallery_note = blockke_dev_text( (string) get_post_meta( $id, 'bke_dev_gallery_note', true ) );
		if ( '' === $hero_label ) {
			$hero_label = $offplan ? 'Project render' : '';
		}
		if ( '' === $gallery_note ) {
			$gallery_note = $offplan ? 'Project renders.' : 'Photos of ' . $short . '.';
		}
		$hero_label   = 'none' === strtolower( $hero_label ) ? '' : $hero_label;
		$gallery_note = 'none' === strtolower( $gallery_note ) ? '' : $gallery_note;

		$agent     = blockke_dev_agent( (int) get_post_meta( $id, 'property_agent', true ) );
		$amenities = blockke_dev_amenities( $id, $secs );
		$amen_src  = $amenities ? blockke_dev_amenity_intro( $secs ) : '';
		if ( '' !== $amen_src && $amen_src === $loc_src ) {
			// One "Amenities & Location" paragraph feeds both sections: the travel sentences go to Location, the rest to Amenities
			$go     = array();
			$others = array();
			foreach ( preg_split( '/(?<=[.!?])\s+(?=[A-Z0-9“"])/u', $loc_src ) as $x ) {
				if ( preg_match( '/\d\s*(?:km|kilomet|min)|\bminutes?\b|\bdrive\b|\bcommut|\bcbd\b|\bfrom (?:nairobi|town|the city)\b|\b(?:is|sits) (?:in|on|off|along)\b|\blocated\b|\bsituated\b/i', $x ) ) {
					$go[] = $x;
				} else {
					$others[] = $x;
				}
			}
			$amen_src     = implode( ' ', $others );
			$loc['intro'] = $go ? blockke_dev_fit_sentences( implode( ' ', $go ), 520 ) : '';
		}
		$loc['intro'] = blockke_dev_loc_intro_dedup( $loc['intro'], $loc['places'] );
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
			'from_label' => (bool) preg_match( '/^\s*from\b/i', (string) get_post_meta( $id, 'property_label_before', true ) ),
			'calc_price' => blockke_dev_parse_money( (string) get_post_meta( $id, 'bke_dev_calc_price', true ) ),
			'completion' => $completion,
			'units'      => $units,
			'subs'       => $subs,
			'images'     => $ids,
			'captions'   => $captions,
			'hero_label' => $hero_label,
			'gallery_note' => $gallery_note,
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
			'amen_intro' => blockke_dev_fit_sentences( $amen_src, 260 ),
			'plan'       => $plan,
			'suits'      => blockke_dev_suits( $secs ),
			'investors'  => blockke_dev_investors( $secs ),
			'lat'        => $lat,
			'lng'        => $lng,
			'location'   => $loc,
			'faqs'       => $faqs,
			'ctx'        => $ctx,
			'agent'      => $agent,
			'brochure'   => '' !== blockke_dev_brochure( $id ),
			'crumbs'     => $crumbs,
			'crumb_here' => $here,
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
			'search'   => '<circle cx="11" cy="11" r="7"/><path d="m20 20-3.6-3.6"/>',
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
		} elseif ( is_singular( 'estate_property' ) && isset( $_GET['lp'] ) && is_scalar( $_GET['lp'] ) && '1' === (string) $_GET['lp'] ) { // phpcs:ignore WordPress.Security.NonceVerification
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
		// How the headline price reads: "From" for a development or an off-plan home; a single home's own price as
		// it is labelled ("Up to KES 23.5M", per sq ft for offices), never a "From" it doesn't have.
		$cx0   = $d['ctx'];
		$p_lab = 'From';
		$p_txt = $d['price_from'] ? blockke_dev_short( $d['price_from'] ) : '';
		if ( $d['price_from'] && ! $d['units'] && (float) $d['price_from'] === (float) $cx0['price'] ) {
			if ( 'upto' === $cx0['flag'] ) {
				$p_lab = 'Up to';
			} elseif ( 'unit' === $cx0['flag'] ) {
				$p_lab = 'Price';
				$p_txt = blockke_dev_money( $d['price_from'] ) . ' per sq ft';
			} elseif ( ! $d['offplan'] && ! $d['from_label'] ) { // the listing's own "From" stays (a starting price)
				$p_lab = 'Price';
			}
		}
		$wa    = 'https://wa.me/' . $ag['wa'] . '?text=' . rawurlencode( 'Hi ' . $cfg['brand'] . ", I'm interested in " . $d['short'] . ( $p_txt ? ' (' . ( 'Price' === $p_lab ? '' : strtolower( $p_lab ) . ' ' ) . $p_txt . ')' : '' ) . '. Please share the price list and floor plans. ' . $d['url'] );
		$o     = array();
		$cx    = $d['ctx'];
		// Landing pages stay a closed funnel: no search, no similar homes and none of their queries
		$sim    = $lp ? null : blockke_dev_similar_block( $d );
		$search = ! $lp && ! empty( $cfg['search'] );
		$s_url  = blockke_dev_search_url( array( 'area' => $cx['area_name'], 'action' => $cx['action'], 'beds' => blockke_dev_link_beds( $cx ) ) );
		$s_nf   = blockke_bkn_on( 'card_v16' ) ? ' rel="nofollow"' : ''; // with the v1.6 area pages on, crawlers follow "See all" to them, not the search tools (as area pages do)

		$o[] = '<div id="bkd" class="bkd' . ( $lp ? ' bkd-lpm' : '' ) . '" data-listing="' . (int) $d['id'] . '">' . blockke_dev_sprite();
		if ( $lp ) {
			$o[] = blockke_dev_lp_header( $d, $wa );
		}

		// Hero
		// On phones only the step up shows ("‹ Golden Mansion"), or "‹ Back to results" when the visitor came from a search
		$crumbs = '<a href="' . esc_url( home_url( '/' ) ) . '"' . ( $d['crumbs'] ? '' : ' class="bkd-cr-up"' ) . '>Home</a>';
		$last   = count( $d['crumbs'] ) - 1;
		foreach ( $d['crumbs'] as $i => $c ) {
			$crumbs .= '<span aria-hidden="true">/</span><a href="' . esc_url( $c[1] ) . '"' . ( $i === $last ? ' class="bkd-cr-up"' : '' ) . '>' . $e( $c[0] ) . '</a>';
		}
		if ( '' !== $d['crumb_here'] ) {
			$crumbs .= '<span aria-hidden="true">/</span><span aria-current="page">' . $e( $d['crumb_here'] ) . '</span>';
		}
		$unav = $lp || empty( $cfg['unit_switcher'] ) ? array() : blockke_dev_unit_nav( $d );
		$un   = '';
		if ( $unav ) {
			$un = '<nav class="bkd-un" aria-label="' . esc_attr( 'Homes at ' . $cx['master_name'] ) . '"><div class="bkd-un-in">';
			foreach ( $unav as $u ) {
				$pr  = $u['price'] > 0 ? '<small>' . $e( trim( str_replace( $cfg['currency'], '', blockke_dev_short( $u['price'] ) ) ) ) . '</small>' : '';
				$un .= $u['current'] ? '<span aria-current="page">' . $e( $u['label'] ) . $pr . '</span>' : '<a href="' . esc_url( $u['url'] ) . '" data-un="' . (int) $u['id'] . '">' . $e( $u['label'] ) . $pr . '</a>';
			}
			$un .= '<a class="bkd-un-all" href="' . esc_url( $cx['master_url'] ) . '">All homes' . blockke_dev_icon( 'arrow' ) . '</a></div></nav>';
		}
		$hfacts   = array();
		$hfacts[] = $p_txt ? array( $p_lab, $p_txt ) : array( 'Price', 'On request' );
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
		// v1.6 (view_transitions): the photo comes first, 3:2 like the card it grows from (the card's own file backs it while
		// the large one loads, set as the page reveals: blockke_bkn_vt_head()); its label moves to the gallery viewer, and
		// the disclaimer already mentions renders
		$vt     = ! $lp && blockke_bkn_vt_on();
		$head   = '<div class="bkd-hero-head">' . ( $lp ? '' : '<div class="bkd-hero-top"><nav class="bkd-crumbs" aria-label="Breadcrumb">' . $crumbs . '</nav><div class="bkd-hero-tools">'
			. ( $search ? '<a class="bkd-srch" data-bkd-search="hero" href="' . esc_url( $s_url ) . '"' . $s_nf . ' aria-label="Search homes" aria-haspopup="dialog">' . blockke_dev_icon( 'search' ) . '<span>Search<span class="bkd-srch-x"> homes</span></span></a>' : '' )
			. '<button class="bkd-share" type="button" data-bkd-share aria-label="Share this property">' . blockke_dev_icon( 'share' ) . '<span>Share</span></button></div></div>' )
			. '<p class="bkd-status">' . ( $d['chip'] ? '<b>' . $e( $d['chip'] ) . '</b>' : '' ) . '<span>' . $e( $d['area_line'] ) . '</span></p>'
			. '<h1 class="bkd-h1">' . $e( $d['name'] ) . '</h1>' . $un
			. ( $d['tagline'] ? '<p class="bkd-hero-tag">' . $e( $d['tagline'] ) . '</p>' : '' )
			. ( $offer ? '<p class="bkd-hero-offer">' . $offer . '</p>' : '' )
			. ( $lp ? '<dl class="bkd-hfacts bkd-hfacts-cover">' . $hf_html . '</dl>' : '' ) . '</div>';
		$fig    = '<figure class="bkd-hero-media"' . ( $vt ? ' id="bkd-hero-media"' . ( $hero && '' !== $d['hero_label'] ? ' data-cap="' . esc_attr( $d['hero_label'] ) . '"' : '' ) : '' ) . '>'
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
			. ( ! $vt && $hero && '' !== $d['hero_label'] ? '<figcaption>' . $e( $d['hero_label'] ) . '</figcaption>' : '' )
			. ( $n_img > 1 ? '<button class="bkd-btn bkd-btn-white bkd-btn-sm bkd-all" type="button" data-bkd-open="0">' . blockke_dev_icon( 'grid' ) . 'View ' . $n_img . ' photos</button>' : '' )
			. '</figure>';
		if ( $vt ) {
			// From 1000px the 3:2 photo is shorter than the name, facts and form beside it, so photos 2 and 3 sit under it,
			// two across, each opening the viewer there (hidden on smaller screens, where the photo is above the text)
			$thumbs = '';
			if ( $n_img >= 3 ) {
				foreach ( array( 1, 2 ) as $i ) {
					$t = wp_get_attachment_image( $imgs[ $i ], 'medium_large', false, array( 'alt' => '', 'loading' => 'lazy', 'decoding' => 'async', 'sizes' => '(min-width: 1000px) 340px, 1px' ) );
					if ( '' === $t ) {
						$thumbs = '';
						break;
					}
					$cap     = isset( $d['captions'][ $imgs[ $i ] ] ) ? $d['captions'][ $imgs[ $i ] ] : '';
					$thumbs .= '<button type="button" data-bkd-open="' . $i . '" aria-label="' . esc_attr( 'Open photo ' . ( $i + 1 ) . ( '' !== $cap ? ': ' . $cap : '' ) ) . '">' . $t . '</button>';
				}
			}
			$fig = '<div class="bkd-hero-side">' . $fig . ( '' !== $thumbs ? '<div class="bkd-hero-thumbs">' . $thumbs . '</div>' : '' ) . '</div>';
		}
		$o[]    = '<section class="bkd-hero' . ( $vt ? ' bkd-hero-vt' : '' ) . '" id="bkd-top"><div class="bkd-wrap bkd-hero-grid">' . ( $vt ? $fig . $head : $head . $fig )
			. '<div class="bkd-hero-body"><dl class="bkd-hfacts">' . $hf_html;
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
		$nav['payment'] = $d['offplan'] ? 'Payment plan' : ( $plan['instalments'] ? 'Ways to pay' : 'Mortgage' );
		if ( $d['lat'] || $d['location']['intro'] ) {
			$nav['location'] = 'Location';
		}
		if ( $sim && $sim['cards'] ) {
			$nav['similar'] = 'Similar';
		}
		if ( $d['faqs'] ) {
			$nav['faq'] = 'FAQ';
		}
		$o[] = '<nav class="bkd-subnav" id="bkd-subnav" aria-label="On this page"><div class="bkd-wrap bkd-subnav-in"><b class="bkd-subnav-name">' . $e( $d['short'] ) . '</b><div class="bkd-subnav-links">';
		foreach ( $nav as $k => $label ) {
			$o[] = '<a href="#bkd-' . $k . '">' . $e( $label ) . '</a>';
		}
		$o[] = '</div><div class="bkd-subnav-r">' . ( $search ? '<a class="bkd-subnav-srch" data-bkd-search="subnav" href="' . esc_url( $s_url ) . '"' . $s_nf . ' aria-label="Search homes" title="Search homes">' . blockke_dev_icon( 'search' ) . '</a>' : '' )
			. '<a class="bkd-subnav-wa" href="' . esc_url( $wa ) . '" target="_blank" rel="noopener" data-bkd-wa>' . blockke_dev_icon( 'wa', 'bkd-wa-ic' ) . '<span>WhatsApp</span></a>'
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
			$o[] = '</div><div class="bkd-sh-row bkd-gal-foot"><p class="bkd-fine">' . $e( $d['gallery_note'] ) . '</p><button class="bkd-link" type="button" data-bkd-open="0">' . blockke_dev_icon( 'grid' ) . 'View all ' . $n_img . ' photos</button></div></div></section>';
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
				$gbeds = array();
				foreach ( $d['units'] as $u ) {
					if ( is_int( $u['key'] ) && $u['key'] > 0 ) {
						$gbeds[ $u['group'] ] = min( 6, $u['key'] );
					} elseif ( 'studio' === $u['key'] && '' !== (string) $cfg['studio_cat'] ) {
						$gbeds[ $u['group'] ] = 'studio';
					}
				}
				$filters = '<div class="bkd-filters" role="group" aria-label="Filter residences"><button type="button" aria-pressed="true" data-g="*">All</button>';
				foreach ( $groups as $g ) {
					$filters .= '<button type="button" aria-pressed="false" data-g="' . esc_attr( $g ) . '"' . ( isset( $gbeds[ $g ] ) ? ' data-beds="' . esc_attr( $gbeds[ $g ] ) . '"' : '' ) . '>' . $e( $g ) . '</button>';
				}
				$filters .= '</div>';
			}
			$o[] = '<section class="bkd-sec bkd-sec-mist" id="bkd-residences"><div class="bkd-wrap"><div class="bkd-sh bkd-sh-row bkd-rv"><div><p class="bkd-label">Residences</p><h2 class="bkd-h2">Availability <em>and pricing</em></h2></div>' . $filters . '</div>'
				. '<table class="bkd-tbl bkd-rv"><thead><tr><th>Residence</th>' . ( $has_size ? '<th>Size</th>' : '' ) . ( $has_pr ? '<th>Price from</th>' : '' ) . '<th><span class="bkd-sr">Actions</span></th></tr></thead><tbody>';
			foreach ( $d['units'] as $u ) {
				$label = $u['type'] . ( $u['size'] ? ' · ' . $u['size'] : '' );
				$o[]   = '<tr data-g="' . esc_attr( $u['group'] ) . '"><td class="bkd-t">' . $e( $u['type'] ) . ( $u['note'] ? '<small>' . $e( $u['note'] ) . '</small>' : '' ) . '</td>'
					. ( $has_size ? '<td class="bkd-s">' . $e( $u['size'] ? $u['size'] : '—' ) . '</td>' : '' )
					. ( $has_pr ? '<td class="bkd-p">' . $e( $u['price'] ? blockke_dev_money( $u['price'] ) : 'On request' ) . ( $u['price'] && ! empty( $u['pnote'] ) ? '<small>' . $e( $u['pnote'] ) . '</small>' : '' ) . '</td>' : '' )
					. '<td class="bkd-a">' . ( $u['url'] && ! $lp ? '<a class="bkd-link" href="' . esc_url( $u['url'] ) . '">Details</a>' : '' )
					. '<button class="bkd-link" type="button" data-bkd-enquire="floorplan" data-unit="' . esc_attr( $label ) . '">Floor plan &amp; price' . blockke_dev_icon( 'arrow' ) . '</button></td></tr>';
			}
			$o[] = '</tbody></table><p class="bkd-fine bkd-tbl-note">' . ( $has_pr ? 'Starting prices. ' : '' ) . 'Ask for the latest price list for current availability.</p>';
			if ( ! $lp && $cx['area_name'] ) {
				$noun = 'rent' === $cx['action'] ? 'rentals' : 'homes for sale';
				$base = blockke_dev_search_url( array( 'area' => $cx['area_name'], 'action' => $cx['action'] ) );
				$o[]  = '<p class="bkd-res-all"><a class="bkd-link" data-res-all href="' . esc_url( $base ) . '" data-base="' . esc_attr( $base ) . '" data-area="' . esc_attr( $cx['area_name'] ) . '" data-noun="' . esc_attr( 'rent' === $cx['action'] ? 'rentals' : 'homes' ) . '">'
					. '<span>See all ' . $e( $noun ) . ' in ' . $e( $cx['area_name'] ) . '</span>' . blockke_dev_icon( 'arrow' ) . '</a></p>';
			}
			$o[] = '</div></section>';
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
			// No stated plan and completion weeks away: an estimate would only show the whole balance over a month or two
			$close   = $plan['deposit'] <= 0 && empty( $plan['inst_pct'] ) && (int) $plan['months'] > 0 && (int) $plan['months'] <= 3;
			// "Pay in instalments" only when the listing mentions instalments and there is time for them
			$heading = $plan['reservation'] > 0 ? 'Reserve with <em>' . $e( blockke_dev_money( $plan['reservation'] ) ) . '</em>' : ( $plan['deposit'] > 0 ? '<em>' . $e( blockke_dev_num( $plan['deposit'] ) ) . '% deposit,</em> then instalments' : ( $plan['flexible'] ? 'A flexible <em>payment plan</em>' : ( $plan['instalments'] && ! $close ? 'Pay in <em>instalments</em>' : 'Ask for the <em>payment plan</em>' ) ) );
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
			if ( $close ) {
				$o[] = '</div><div class="bkd-panel bkd-rv"><p class="bkd-panel-t">Completion is close</p><p class="bkd-body">' . $e( 'Completion is scheduled for ' . $d['completion'] . ', so an instalment plan would be short. Ask for the developer\'s current payment terms, and whether a mortgage can cover the balance.' ) . '</p>'
					. '<p class="bkd-mt"><button class="bkd-link" type="button" data-bkd-enquire="payment-plan">Get the official payment plan' . blockke_dev_icon( 'arrow' ) . '</button></p></div></div></section>';
			} else {
				$o[] = '</div><div class="bkd-panel bkd-rv"><p class="bkd-panel-t">Estimate your instalments</p>' . blockke_dev_calc_instalment( $d['calc_price'] > 0 ? $d['calc_price'] : $d['price_from'], $plan, $cta ) . '</div></div></section>';
			}
		} else {
			$cta = '<div class="bkd-calc-cta"><button class="bkd-cbtn" type="button" data-bkd-enquire="mortgage">Talk to us about financing</button></div>';
			// A ready home: the editor's payment text, else the ways to pay the listing offers
			$ways = $plan['instalments'] ? 'Pay cash, use the developer\'s instalment plan, or put down a deposit and finance the rest with a mortgage.' : 'Pay cash, or put down a deposit and finance the rest with a mortgage.';
			$body = $plan['own_text'] ? $plan['text'] : $ways . ' We can introduce you to lenders and compare offers before you commit.';
			$o[]  = '<section class="bkd-sec bkd-sec-mist" id="bkd-payment"><div class="bkd-wrap bkd-split"><div class="bkd-rv"><p class="bkd-label">' . ( $plan['instalments'] ? 'Ways to pay' : 'Mortgage' ) . '</p><h2 class="bkd-h2">Ready to move in. <em>Finance it your way.</em></h2>'
				. '<p class="bkd-body bkd-mt">' . $e( $body ) . '</p></div>'
				. '<div class="bkd-panel bkd-rv"><p class="bkd-panel-t">Estimate your monthly repayment</p>' . blockke_dev_calc_mortgage( $d['calc_price'] > 0 ? $d['calc_price'] : $d['price_from'], $cta, $d['calc_price'] > 0 ) . '</div></div></section>';
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
				$o[] = '<div class="bkd-rv"><b>0' . ( $i + 1 ) . '</b><h3 class="bkd-h3">' . $e( $w[0] ) . '</h3><p>' . $e( ! $d['offplan'] && ! empty( $w[2] ) ? $w[2] : $w[1] ) . '</p></div>';
			}
			$o[] = '</div></div></section>';
		}

		// Also consider: similar homes (development pages). Landing pages get an enquiry for alternatives instead of links away.
		if ( $sim ) {
			$o[] = '<section class="bkd-sec' . ( $d['faqs'] ? ' bkd-sec-mist' : '' ) . ' bkd-sim" id="bkd-similar" data-mode="' . esc_attr( $sim['mode'] ) . '" data-tier="' . (int) $sim['tier'] . '" data-beds="' . esc_attr( $sim['beds'] ) . '" data-area="' . esc_attr( $sim['area'] ) . '"><div class="bkd-wrap">'
				. '<div class="bkd-sh bkd-sh-row bkd-rv"><div><p class="bkd-label">Also consider</p><h2 class="bkd-h2">' . $sim['title'] . '</h2></div>'
				. ( $sim['cards'] ? '<a class="bkd-link bkd-sim-top" data-sim-all href="' . esc_url( $sim['all_url'] ) . '"' . ( ! empty( $sim['all_nf'] ) ? ' rel="nofollow"' : '' ) . '>' . $e( $sim['all_text'] ) . blockke_dev_icon( 'arrow' ) . '</a>' : '' ) . '</div>';
			if ( $sim['cards'] ) {
				$o[] = '<div class="bkd-cards" style="--n:' . min( count( $sim['cards'] ), 3 ) . '">';
				foreach ( $sim['cards'] as $i => $s ) {
					$o[] = '<a class="bkd-card bkd-rv" href="' . esc_url( $s['url'] ) . '" data-id="' . (int) $s['id'] . '" data-sim-pos="' . ( $i + 1 ) . '" data-tier="' . (int) $s['tier'] . '" data-same="' . (int) $s['same'] . '"><div class="bkd-card-img">'
						. blockke_dev_img( $s['img'], 'medium_large', array( 'loading' => 'lazy', 'decoding' => 'async', 'sizes' => '(min-width: 1000px) 33vw, 78vw', 'alt' => blockke_dev_alt( $s['img'], $s['name'] ), 'data-ph' => $s['name'] ) )
						. ( $s['badge'] ? '<span class="bkd-card-badge">' . $e( $s['badge'] ) . '</span>' : '' )
						. '</div><h3 class="bkd-h3">' . $e( $s['name'] ) . '</h3><b>' . $e( $s['price'] ) . '</b><span>' . $e( $s['meta'] ) . '</span></a>';
				}
				$o[] = '<a class="bkd-card-all" data-sim-all href="' . esc_url( $sim['all_url'] ) . '"' . ( ! empty( $sim['all_nf'] ) ? ' rel="nofollow"' : '' ) . '><span>' . $e( $sim['all_text'] ) . '</span>' . blockke_dev_icon( 'arrow' ) . '</a></div>';
				$o[] = '<div class="bkd-sim-act bkd-rv">' . ( $search ? '<a class="bkd-btn bkd-btn-line bkd-btn-sm" data-bkd-search="similar" href="' . esc_url( $s_url ) . '"' . $s_nf . '>' . blockke_dev_icon( 'search' ) . 'Refine search</a>' : '' )
					. '<button class="bkd-btn bkd-btn-navy bkd-btn-sm" type="button" data-bkd-enquire="compare">Compare these with an advisor</button></div>';
			} else {
				$o[] = '<div class="bkd-sim-empty bkd-rv"><p class="bkd-body">Tell us what you\'re looking for and an advisor will send you matches as they come up, often before they are listed.</p><div class="bkd-sim-act">'
					. '<button class="bkd-btn bkd-btn-navy bkd-btn-sm" type="button" data-bkd-enquire="find">Ask an advisor to find one</button>'
					. ( $search ? '<a class="bkd-btn bkd-btn-line bkd-btn-sm" data-bkd-search="empty" href="' . esc_url( $s_url ) . '"' . $s_nf . '>' . blockke_dev_icon( 'search' ) . 'Search homes</a>' : '' ) . '</div></div>';
			}
			$o[] = '</div></section>';
		} elseif ( $lp && ! empty( $cfg['lp_alternatives'] ) ) {
			$what = blockke_dev_key_phrase( $cx['key'], 'rent' === $cx['action'] );
			$what = 'similar ' . $what . ( $cx['area_name'] ? ' in ' . $cx['area_name'] : '' );
			$o[]  = '<section class="bkd-sec' . ( $d['faqs'] ? ' bkd-sec-mist' : '' ) . ' bkd-alt" id="bkd-alternatives"><div class="bkd-wrap"><div class="bkd-alt-in bkd-rv"><div><p class="bkd-label">Also consider</p>'
				. '<h2 class="bkd-h2">Not sure <em>' . $e( $d['short'] ) . '</em> is the one?</h2><p class="bkd-body bkd-mt">Tell us your budget and we\'ll shortlist 3 ' . $e( $what ) . ', with prices and payment plans, so you can compare before you decide.</p></div>'
				. '<button class="bkd-btn bkd-btn-navy" type="button" data-bkd-enquire="alternatives" data-unit="' . esc_attr( 'Alternatives to ' . $d['short'] . ': ' . ucfirst( $what ) ) . '">Send me 3 alternatives' . blockke_dev_icon( 'arrow' ) . '</button></div></div></section>';
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
		if ( $search ) {
			$o[] = blockke_dev_search_sheet( $d );
		}
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
#bkd .bkd-more{display:grid;grid-template-columns:minmax(0,1fr);gap:14px;margin:0 0 22px;padding-top:18px;border-top:1px solid var(--line)}
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
@media (max-width:899px){#bkd .bkd-facts dl>div:last-child:nth-child(odd):not(:first-child){grid-column:1/-1}}
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
#bkd .bkd-split{display:grid;grid-template-columns:minmax(0,1fr);gap:40px}
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
@media (max-width:719px){#bkd .bkd-prose table{font-size:13px}#bkd .bkd-prose :is(th,td){padding-right:8px}#bkd .bkd-prose th{letter-spacing:.06em}#bkd .bkd-prose figure{background:linear-gradient(90deg,var(--paper) 30%,rgba(251,249,246,0)) 0 0/28px 100% no-repeat local,linear-gradient(270deg,var(--paper) 30%,rgba(251,249,246,0)) 100% 0/28px 100% no-repeat local,radial-gradient(farthest-side at 0 50%,rgba(13,36,64,.18),rgba(13,36,64,0)) 0 0/12px 100% no-repeat scroll,radial-gradient(farthest-side at 100% 50%,rgba(13,36,64,.18),rgba(13,36,64,0)) 100% 0/12px 100% no-repeat scroll}} /* wide tables swipe, with a shadow on the side that has more */
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
#bkd .bkd-tbl td.bkd-p small{display:block;margin-top:2px;font-size:13px;font-weight:400;color:var(--muted)}
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
#bkd .bkd-direct a{display:inline-flex;align-items:center;gap:14px;min-height:44px;font-size:15px;font-weight:500;color:var(--navy)}
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
/* v1.4: search, breadcrumb, unit switcher, similar homes, landing page alternatives */
#bkd .bkd-hero-tools{display:flex;align-items:center;gap:4px 10px;flex:none}
#bkd .bkd-srch{display:inline-flex;align-items:center;gap:8px;min-height:40px;margin:0;padding:0 14px!important;border:1px solid var(--line-2)!important;border-radius:999px!important;background:#fff!important;box-shadow:none!important;font:600 12px/1 var(--font)!important;letter-spacing:.08em!important;text-transform:uppercase!important;color:var(--navy)!important;white-space:nowrap;text-decoration:none!important;transition:border-color .2s}
#bkd .bkd-srch:hover,#bkd .bkd-srch:focus-visible{border-color:var(--navy)!important}
#bkd .bkd-srch .bkd-i{width:16px;height:16px}
#bkd .bkd-share{min-height:40px}
@media (max-width:699px){#bkd .bkd-srch-x{display:none}}
@media (max-width:379px){#bkd .bkd-srch{width:40px;padding:0!important;justify-content:center}#bkd .bkd-srch>span,#bkd .bkd-share span{display:none}#bkd .bkd-share{width:40px;justify-content:center}}
#bkd .bkd-crumbs .bkd-cr-back{font-weight:600;color:var(--navy)}
#bkd .bkd-crumbs span[aria-current]{overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:28ch}
@media (max-width:699px){#bkd .bkd-crumbs>*{display:none}#bkd .bkd-crumbs .bkd-cr-up,#bkd .bkd-crumbs .bkd-cr-back{display:inline-flex;align-items:center;min-height:40px;font-size:13px;font-weight:600;color:var(--navy)}#bkd .bkd-crumbs.bkd-has-back .bkd-cr-up{display:none}#bkd .bkd-cr-up::before,#bkd .bkd-cr-back::before{content:"\2039";margin-right:7px;font-size:20px;line-height:1;font-weight:400}}
@media (min-width:700px){#bkd .bkd-cr-back::before{content:"\2039";margin-right:6px}}
#bkd .bkd-un{margin:18px 0 0;min-width:0}
#bkd .bkd-un-in{display:flex;gap:8px;overflow-x:auto;padding:2px 0 6px;scrollbar-width:none;-webkit-overflow-scrolling:touch;scroll-snap-type:x proximity}
#bkd .bkd-un-in::-webkit-scrollbar{display:none}
#bkd .bkd-un-in{position:relative}
#bkd .bkd-un-in.bkd-ovf,#bkd .bkd-subnav-links.bkd-ovf{-webkit-mask-image:linear-gradient(90deg,#000 calc(100% - 36px),transparent);mask-image:linear-gradient(90deg,#000 calc(100% - 36px),transparent)}
#bkd .bkd-un-in.bkd-ovl,#bkd .bkd-subnav-links.bkd-ovl{-webkit-mask-image:linear-gradient(90deg,transparent,#000 36px);mask-image:linear-gradient(90deg,transparent,#000 36px)}
#bkd .bkd-un-in.bkd-ovf.bkd-ovl,#bkd .bkd-subnav-links.bkd-ovf.bkd-ovl{-webkit-mask-image:linear-gradient(90deg,transparent,#000 36px,#000 calc(100% - 36px),transparent);mask-image:linear-gradient(90deg,transparent,#000 36px,#000 calc(100% - 36px),transparent)}
#bkd .bkd-un-in>*{flex:none;display:inline-flex;align-items:center;gap:7px;min-height:40px;padding:0 14px;border:1px solid var(--line-2);background:#fff;font-size:13px;font-weight:600;line-height:1.2;color:var(--navy);white-space:nowrap;scroll-snap-align:start;transition:border-color .2s}
#bkd .bkd-un-in>a:hover{border-color:var(--navy);color:var(--navy)!important}
#bkd .bkd-un-in small{font-size:12px;font-weight:500;color:var(--slate)}
#bkd .bkd-un-in>span[aria-current]{background:var(--navy);border-color:var(--navy);color:#fff}
#bkd .bkd-un-in>span[aria-current] small{color:var(--on-navy)}
#bkd .bkd-un-in>.bkd-un-all{border-color:transparent;background:none;padding:0 6px;font-weight:600;text-decoration:underline;text-underline-offset:4px}
#bkd .bkd-un-all .bkd-i{width:14px;height:14px}
#bkd .bkd-subnav-links{position:relative;overflow-x:auto;scrollbar-width:none}
#bkd .bkd-subnav-links::-webkit-scrollbar{display:none}
#bkd .bkd-subnav-links a{flex:none}
#bkd .bkd-subnav-srch{display:grid;place-items:center;width:40px;height:40px;border:1px solid var(--line-2);border-radius:999px;color:var(--navy);transition:border-color .2s}
#bkd .bkd-subnav-srch:hover{border-color:var(--navy);color:var(--navy)!important}
#bkd .bkd-subnav-srch .bkd-i{width:17px;height:17px}
#bkd .bkd-res-all{margin:18px 0 0}
#bkd .bkd-card .bkd-card-badge{position:absolute;left:12px;top:12px;z-index:1;display:inline-block;margin:0;padding:6px 10px;background:rgba(13,36,64,.9);font-size:10.5px;font-weight:600;line-height:1.3;letter-spacing:.08em;text-transform:uppercase;color:#fff}
#bkd .bkd-cards>.bkd-card-all{display:none}
@media (max-width:999px){#bkd .bkd-cards>.bkd-card-all{display:flex;flex-direction:column;justify-content:flex-end;gap:14px;aspect-ratio:4/3;padding:22px;background:var(--navy);color:#fff!important;scroll-snap-align:start;font-size:18px;font-weight:600;line-height:1.3}
#bkd .bkd-card-all span{color:#fff}#bkd .bkd-card-all .bkd-i{width:22px;height:22px;color:var(--brass-l)}#bkd .bkd-sim .bkd-sim-top{display:none}}
#bkd .bkd-sim-act{display:flex;flex-wrap:wrap;gap:12px;margin-top:30px}
#bkd .bkd-sim-act .bkd-btn{flex:0 1 auto}
@media (max-width:559px){#bkd .bkd-sim-act .bkd-btn{flex:1 1 100%}}
#bkd .bkd-sim-empty .bkd-body{max-width:60ch;margin:0}
#bkd .bkd-sim-empty .bkd-sim-act{margin-top:24px}
#bkd .bkd-alt-in{display:grid;gap:28px;align-items:end}
@media (min-width:1000px){#bkd .bkd-alt-in{grid-template-columns:minmax(0,1fr) auto;gap:72px}}
#bkd .bkd-alt .bkd-body{max-width:58ch}
#bkd .bkd-ok .bkd-ok-after{margin-top:18px}
#bkd .bkd-sf .bkd-md-box{display:flex;flex-direction:column;max-width:620px;max-height:88vh;max-height:88dvh;padding:0;overflow:hidden}
#bkd .bkd-sf-form{display:flex;flex-direction:column;min-height:0;margin:0}
#bkd .bkd-sf-head{padding:30px 64px 6px 24px}
#bkd .bkd-sf-head .bkd-label{margin-bottom:12px}
#bkd .bkd-sf .bkd-h2{font-size:clamp(26px,3vw,34px)!important}
#bkd .bkd-sf-body{display:grid;gap:22px;min-height:0;overflow:auto;padding:18px 24px 20px;-webkit-overflow-scrolling:touch}
#bkd .bkd-sf-foot{display:grid;gap:12px;justify-items:center;padding:14px 24px calc(14px + env(safe-area-inset-bottom));background:#fff;border-top:1px solid var(--line)}
@media (min-width:700px){#bkd .bkd-sf-head{padding:44px 72px 6px 40px}#bkd .bkd-sf-body{grid-template-columns:1fr 1fr;padding:22px 40px 26px}#bkd .bkd-sf-wide{grid-column:1/-1}#bkd .bkd-sf-foot{padding:18px 40px 24px}}
#bkd .bkd-f>select{display:block;width:100%;height:52px;margin:0;padding:0 42px 0 16px;border:1px solid var(--line-2)!important;border-radius:0!important;background:#fff url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%230D2440' stroke-width='2'%3E%3Cpath d='m6 9 6 6 6-6'/%3E%3C/svg%3E") no-repeat right 14px center/16px!important;box-shadow:none!important;font:400 16px/1.4 var(--font)!important;color:var(--navy)!important;-webkit-appearance:none;appearance:none}
#bkd .bkd-f>select:focus{outline:0;border-color:var(--navy)!important;box-shadow:0 0 0 3px rgba(13,36,64,.1)!important}
#bkd .bkd-seg .bkd-chips{display:grid;grid-template-columns:1fr 1fr;gap:0}
#bkd .bkd-seg .bkd-chip span{justify-content:center;width:100%;min-height:46px;font-size:14px;font-weight:600}
#bkd .bkd-seg .bkd-chip+.bkd-chip span{border-left:0}
#bkd .bkd-sf .bkd-chip span{min-width:44px;padding:0 10px;justify-content:center}
#bkd .bkd-sf .bkd-chips{gap:6px}
@media print{#bkd .bkd-subnav,#bkd .bkd-mb,#bkd .bkd-md,#bkd .bkd-lb,#bkd .bkd-quick,#bkd .bkd-formcard,#bkd .bkd-map,#bkd .bkd-un,#bkd .bkd-hero-tools,#bkd .bkd-sim-act,#bkd .bkd-card-all,#bkd .bkd-alt{display:none!important}#bkd .bkd-rv{opacity:1!important;transform:none!important}}
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
		$js = <<<'JS'
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
      im.style.display='none';b.insertAdjacentHTML('afterbegin','<div class="bkd-ph"><b>'+esc(im.getAttribute('data-ph')||C.name)+'</b><span>Images on request</span></div>');},{once:true});
  });

  /* attribution (the site-wide keeper stores it in sessionStorage blkbp_attr) */
  function attr(){
    var a={};try{a=JSON.parse(sessionStorage.getItem('blkbp_attr')||'{}');}catch(e){a={};}
    try{var q=new URLSearchParams(location.search);['utm_source','utm_medium','utm_campaign','utm_term','utm_content','gclid','fbclid','gbraid','wbraid'].forEach(function(k){var v=q.get(k);if(v&&!a[k]){a[k]=v.slice(0,120);}});}catch(e){}
    if(!a.land){a.land=location.href.slice(0,250);a.ref=(document.referrer||'').slice(0,200);}
    return a;
  }
  function track(name,extra){var p=extra||{};p.development=C.slug;p.listing_id=String(C.id);if(C.areaPage){p.area_page=C.seg||'area';p.area=C.areaSlug||'';}
    try{if(typeof window.gtag==='function'){window.gtag('event',name,p);}else{p.event=name;dl.push(p);}}catch(e){}}
  $$('[data-bkd-wa]').forEach(function(a){a.addEventListener('click',function(){track('click_whatsapp');});});
  $$('a[href^="tel:"]').forEach(function(a){a.addEventListener('click',function(){track('click_call');});});

  /* lead forms */
  var remembered={};try{remembered=JSON.parse(sessionStorage.getItem('bkd_lead')||'{}');}catch(e){remembered={};}
  function prefill(form){['name','phone','email'].forEach(function(k){var i=form.querySelector('[name="'+k+'"]');if(i&&!i.value&&remembered[k]){i.value=remembered[k];}});}
  function icon(n,cls){return '<svg class="bkd-i'+(n==='wa'?' bkd-i-f':'')+(cls?' '+cls:'')+'" aria-hidden="true"><use href="#bkd-i-'+n+'"></use></svg>';}
  function waUrl(text){return 'https://wa.me/'+C.wa+'?text='+encodeURIComponent(text);}
  function leadText(p){return ['Hi '+C.brand+", I've just sent an enquiry about "+(C.leadName||C.name)+' on block.ke.',p.unit?'Interested in: '+p.unit:'','Name: '+p.name,C.url].filter(Boolean).join('\n');}
  function success(form,p,j){
    var ok=form.nextElementSibling,first=(p.name||'').trim().split(/\s+/)[0];
    ok.querySelector('[data-first]').textContent=first?', '+first:'';
    var it=form.getAttribute('data-intent')||'';
    var tail={alternatives:'with 3 similar homes and their prices','search-shortlist':'with a shortlist of matching homes',compare:'to compare '+C.name+' with similar homes',find:'with matches as they come up',comparables:'with recent rents and resale prices near '+C.name,mortgage:'to talk through financing for '+C.name}[it]||C.okTail||'with the price list and floor plans for '+C.name;
    ok.querySelector('[data-okmsg]').textContent='An advisor will be in touch shortly'+(j&&j.phone_display?' on '+j.phone_display:'')+' '+tail+'.';
    ok.querySelector('[data-wa-ok]').href=waUrl(leadText(p));
    var ab=ok.querySelector('[data-asset]');
    if(j&&j.asset){ab.href=j.asset;ab.hidden=false;ab.innerHTML='Download the brochure'+icon('arrow');ab.addEventListener('click',function(){track('asset_open');},{once:true});}else{ab.hidden=true;}
    var mf=ok.querySelector('[data-more]');
    if(mf){$$('input',mf).forEach(function(i){i.checked=false;});mf.setAttribute('data-ref',(j&&j.ref)||'');mf.hidden=!(j&&j.ref);
      $$('fieldset,.bkd-more-t,[data-more-send]',mf).forEach(function(x){x.hidden=false;});mf.querySelector('[data-more-send]').disabled=false;
      var dn=mf.querySelector('.bkd-more-done');dn.hidden=true;dn.textContent='';
      var mu=mf.querySelector('[data-more-unit]');if(mu){mu.hidden=!!p.unit;}}
    if(C.lp&&C.after&&!ok.querySelector('[data-after]')){var la=document.createElement('a');la.className='bkd-link bkd-ok-after';la.setAttribute('data-after','');la.href=C.after.url;la.innerHTML=esc(C.after.label)+icon('arrow');
      la.addEventListener('click',function(){track('lp_after_click',{transport_type:'beacon'});});ok.appendChild(la);}
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
    if(C.areaPage){p.area=C.areaId||0;p.seg=C.seg||'';p.look=d.look||'';p.ptype=d.ptype||'';p.budget=d.budget||'';p.beds=d.beds||'';}
    if(C.indexPage){p.index='1';p.filters=location.search.slice(1,200);p.budget=d.budget||'';}
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
    var titles={'payment-plan':'Get the payment plan',mortgage:'Talk to us about financing',brochure:'Download the brochure',comparables:'Get rental and resale comparables',
      alternatives:'Get 3 similar homes','search-shortlist':'Get a shortlist',compare:'Compare with an advisor',find:C.areaPage?'Ask us to find one':'Find me a home like this'};
    var subs={alternatives:'Similar homes'+(C.area?' in and around '+C.area:'')+', with prices and payment plans, sent to your WhatsApp.','search-shortlist':(unit?unit+'. ':'')+'An advisor picks the best matches and sends them to your WhatsApp.',
      compare:'We\'ll compare '+C.name+' with similar homes on price, payment plan and completion.',find:'Tell us what you need and we\'ll send matches as they come up.'};
    document.getElementById('bkd-md-t').textContent=titles[source]||C.mdTitle||(unit?'Floor plan and price':'Get the price list');
    document.getElementById('bkd-md-sub').textContent=subs[source]?subs[source]:C.mdSub?C.mdSub:unit?unit+' at '+C.name+'. Sent to your WhatsApp.':(source==='brochure'?'Leave your details and the brochure opens straight away.':(source==='mortgage'?'We will compare mortgage options for you.':(source==='comparables'?'Recent rents and resale prices near '+C.name+', sent to your WhatsApp.':'Floor plans and the payment plan, sent to your WhatsApp.')));
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

  /* search homes: a short search, filled in from this listing, into the site's search results */
  var sf=document.getElementById('bkd-sf'),sfFocus=null,sfStart='',sfSending=false,sfAt=0;
  function sfForm(){return sf.querySelector('form');}
  function sfMode(){var r=sf.querySelector('input[name="property_action_category[]"]:checked');return r&&r.value==='for-rent'?'rent':'sale';}
  function sfState(){var f=sfForm(),b=f.querySelector('input[name="componentsbeds"]:checked'),c=f.querySelector('[name="completion"]'),bud=f.querySelector('[data-sf-budget]');
    return {action:sfMode(),area:f.querySelector('[name="location"]').value,beds:b?b.value:'',budget:bud.value,budgetText:bud.value?bud.options[bud.selectedIndex].text:'',completion:c&&sfMode()==='sale'?c.value:''};}
  function sfSummary(){var s=sfState();return [s.beds==='studio'?'Studio':s.beds?s.beds+(s.beds==='4'?'+':'')+' bed':'',s.area||'Any area',s.budgetText,s.action==='rent'?'Rent':'Buy'].filter(Boolean).join(' · ');}
  function sfBands(){var sel=sf.querySelector('[data-sf-budget]'),mode=sfMode();if(sel.getAttribute('data-mode')===mode){return;}
    var bands=(C.bands&&C.bands[mode])||[];sel.innerHTML='<option value="">Any budget</option>'+bands.map(function(b){return '<option value="'+(+b[0])+'-'+(+b[1])+'">'+esc(b[2])+'</option>';}).join('');sel.setAttribute('data-mode',mode);
    var comp=sf.querySelector('[data-sf-comp]');if(comp){comp.hidden=mode==='rent';}}
  function openSheet(source){
    if(!sf){return;}sfBands();sfAt=Date.now();sfFocus=document.activeElement;sf.hidden=false;lock(true);sf.setAttribute('data-source',source||'');
    $$('[name]',sfForm()).forEach(function(x){if(x.type!=='hidden'){x.disabled=false;}});sfStart=JSON.stringify(sfState());
    try{history.pushState({bkdSheet:1},'');}catch(e){}
    setTimeout(function(){var f=sf.querySelector('input[name="property_action_category[]"]:checked')||sf.querySelector('select');if(f){f.focus();}},30);
    track('search_open',{source:source||''});
  }
  function closeSheet(fromPop){
    if(!sf||sf.hidden){return;}sf.hidden=true;lock(false);
    if(!fromPop&&history.state&&history.state.bkdSheet){try{history.back();}catch(e){}}
    if(sfFocus&&sfFocus.focus){try{sfFocus.focus({preventScroll:true});}catch(e){}}
  }
  if(sf){
    root.addEventListener('click',function(e){var t=e.target.closest&&e.target.closest('[data-bkd-search]');if(!t||e.defaultPrevented||e.button!==0||e.ctrlKey||e.metaKey||e.shiftKey||e.altKey){return;}e.preventDefault();openSheet(t.getAttribute('data-bkd-search'));});
    sf.addEventListener('click',function(e){
      if(e.target===sf&&(e.detail>1||Date.now()-sfAt<400)){return;} /* the second click of a double-click on the button */
      if(e.target===sf||(e.target.closest&&e.target.closest('[data-bkd-sfclose]'))){closeSheet();return;}
      if(e.target.closest&&e.target.closest('[data-sf-shortlist]')){var sum=sfSummary();closeSheet();track('search_shortlist');openModal('search-shortlist',sum);}
    });
    sf.addEventListener('change',function(e){if(e.target.name==='property_action_category[]'){sfBands();}});
    window.addEventListener('popstate',function(){if(!sfSending&&!sf.hidden){closeSheet(true);}});
    window.addEventListener('pageshow',function(e){if(e.persisted){sfSending=false;$$('[name]',sfForm()).forEach(function(x){if(x.type!=='hidden'){x.disabled=false;}});if(!sf.hidden){closeSheet(true);}}});
    sfForm().addEventListener('submit',function(e){
      var f=sfForm(),st=sfState(),lo=f.querySelector('[name="price_low"]'),hi=f.querySelector('[name="price_max"]');
      if(st.budget){var pr=st.budget.split('-');lo.value=pr[0];hi.value=+pr[1]>0?pr[1]:'500000000';lo.disabled=hi.disabled=false;}else{lo.disabled=hi.disabled=true;}
      $$('select[name],input[type="radio"][name]',f).forEach(function(x){if(x.value===''||(x.name==='componentsbeds'&&x.value==='studio')){x.disabled=true;}});
      var stu=f.querySelector('[data-sf-studio]');if(stu){stu.disabled=st.beds!=='studio';}
      var c=f.querySelector('[name="completion"]');if(c&&st.action==='rent'){c.disabled=true;}
      var a=JSON.parse(sfStart||'{}'),changed=0;['action','area','beds','budget','completion'].forEach(function(k){if(a[k]!==st[k]){changed++;}});
      track('search_submit',{action:st.action,area:st.area,beds:st.beds,budget:st.budget,completion:st.completion,changed:changed,source:sf.getAttribute('data-source')||'',transport_type:'beacon'});
      if(history.state&&history.state.bkdSheet){e.preventDefault();sfSending=true;var go=function(){if(go.done){return;}go.done=true;f.submit();};
        window.addEventListener('popstate',go,{once:true});setTimeout(go,400);history.back();}
    });
  }

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
  function trap(e,box){var f=$$('a[href], button, input, select, textarea',box).filter(function(x){return x.offsetParent!==null&&!x.disabled&&!x.classList.contains('bkd-hp')&&x.tabIndex!==-1&&!(x.type==='radio'&&!x.checked&&$$('input[name="'+x.name+'"]:checked',box).length);});
    if(!f.length){return;}if(e.shiftKey&&document.activeElement===f[0]){e.preventDefault();f[f.length-1].focus();}else if(!e.shiftKey&&document.activeElement===f[f.length-1]){e.preventDefault();f[0].focus();}}
  document.addEventListener('keydown',function(e){
    if(lb&&!lb.hidden){if(e.key==='Escape'){closeLb();}if(e.key==='ArrowLeft'){show(cur-1);}if(e.key==='ArrowRight'){show(cur+1);}return;}
    if(md&&!md.hidden){
      if(e.key==='Escape'){closeModal();return;}
      if(e.key==='Tab'){trap(e,md);}
      return;
    }
    if(sf&&!sf.hidden){
      if(e.key==='Escape'){closeSheet();return;}
      if(e.key==='Tab'){trap(e,sf);}
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
  var rows=$$('.bkd-tbl tbody tr'),resAll=$('[data-res-all]');
  $$('.bkd-filters button').forEach(function(b){b.addEventListener('click',function(){
    $$('.bkd-filters button').forEach(function(x){x.setAttribute('aria-pressed',String(x===b));});
    rows.forEach(function(r){r.hidden=b.getAttribute('data-g')!=='*'&&r.getAttribute('data-g')!==b.getAttribute('data-g');});
    track('units_filter',{group:b.getAttribute('data-g')});
    if(resAll){var bd=b.getAttribute('data-beds'),area=resAll.getAttribute('data-area'),noun=resAll.getAttribute('data-noun');
      resAll.href=resAll.getAttribute('data-base')+(bd==='studio'?(C.studioCat?'&property_category%5B%5D='+encodeURIComponent(C.studioCat):''):bd?'&componentsbeds='+bd:'');
      resAll.querySelector('span').textContent=bd==='studio'?'Comparing? See other studio '+noun+' in '+area:bd?'Comparing? See other '+bd+(C.bedsMode==='min'?'+ bed ':'-bed ')+noun+' in '+area:'See all '+(noun==='rentals'?'rentals':'homes for sale')+' in '+area;}
  });});
  if(resAll){resAll.addEventListener('click',function(){var p=$('.bkd-filters [aria-pressed="true"]');track('residences_see_all',{group:p?p.getAttribute('data-g'):'*',transport_type:'beacon'});});}

  /* switching between the homes in this development */
  $$('.bkd-un a[data-un]').forEach(function(a){a.addEventListener('click',function(){track('unit_switch',{from_id:String(C.id),to_id:a.getAttribute('data-un'),transport_type:'beacon'});});});

  /* "Back to results": a visitor who came from the site's search or a listings page (also via other homes here) */
  (function(){
    var nav=$('.bkd-crumbs'),from='';if(!C.back||!nav){return;}
    try{var ref=document.referrer||'',u=ref?new URL(ref):null;
      if(u&&u.host===location.host){
        if(/^\/(advanced-search|map-search|search|property_[a-z_]+|project|completion|new-developments|for-sale|for-rent)(\/|$)/.test(u.pathname)){from=ref;}
        else if(/^\/property\//.test(u.pathname)){var st=null;try{st=JSON.parse(sessionStorage.getItem('bkd_from')||'null');}catch(x){st=null;}
          from=st&&st.p===u.pathname&&st.f?st.f:''; /* only when the visitor came from the page that last showed the link */}
      }
      if(from){sessionStorage.setItem('bkd_from',JSON.stringify({f:from,p:location.pathname}));}else{sessionStorage.removeItem('bkd_from');}
    }catch(e){from='';}
    if(!from){return;}
    try{if(!history.state&&!location.hash){history.replaceState({bkdLand:1},'');}}catch(e){}
    var a=document.createElement('a'),sep=document.createElement('span');a.className='bkd-cr-back';a.href=from;a.textContent='Back to results';sep.setAttribute('aria-hidden','true');sep.textContent='/';
    nav.insertBefore(sep,nav.firstChild);nav.insertBefore(a,sep);nav.classList.add('bkd-has-back');
    a.addEventListener('click',function(e){track('back_to_results',{transport_type:'beacon'});if(document.referrer===from&&history.length>1&&history.state&&history.state.bkdLand){e.preventDefault();history.back();}});
  })();

  /* similar homes: seen, clicked, "see all" */
  var simSec=document.getElementById('bkd-similar');
  if(simSec){
    var simInfo={mode:simSec.getAttribute('data-mode'),tier_max:simSec.getAttribute('data-tier'),count:$$('a.bkd-card[data-id]',simSec).length,beds:simSec.getAttribute('data-beds'),area:simSec.getAttribute('data-area')};
    if('IntersectionObserver' in window){var sio=new IntersectionObserver(function(es){es.forEach(function(en){if(en.isIntersecting){track('similar_view',simInfo);sio.disconnect();}});},{threshold:0.5});sio.observe($('.bkd-cards',simSec)||$('.bkd-sh',simSec));}
    simSec.addEventListener('click',function(e){
      var a=e.target.closest&&e.target.closest('a.bkd-card[data-id]');
      if(a){track('similar_click',{position:+a.getAttribute('data-sim-pos'),target_id:a.getAttribute('data-id'),tier:a.getAttribute('data-tier'),same_area:a.getAttribute('data-same'),transport_type:'beacon'});}
      if(e.target.closest&&e.target.closest('[data-sim-all]')){track('similar_see_all',{beds:simInfo.beds,area:simInfo.area,transport_type:'beacon'});}
    });
  }

  /* mobile bar, section nav highlight */
  var mb=document.getElementById('bkd-mb'),quick=document.getElementById('bkd-quick'),enquire=document.getElementById('bkd-enquire');
  var links=$$('.bkd-subnav-links a'),targets=links.map(function(a){return document.getElementById(a.getAttribute('href').slice(1));});
  var themeBar=function(){var t=document.querySelector('.mobile_agent_area_wrapper');if(!t){return false;}var cs=getComputedStyle(t);return cs.display!=='none'&&cs.visibility!=='hidden';};
  var ticking=false,lastProbe=0;
  function onScroll(){
    ticking=false;
    var now=Date.now();if(now-lastProbe>250){lastProbe=now;topOffset();}
    var hb=quick&&quick.offsetParent===null?document.getElementById('bkd-top'):quick; /* area pages on phones: no hero form, so the hero */
    var qb=hb?hb.getBoundingClientRect().bottom:0,eb=enquire?enquire.getBoundingClientRect():null;
    var inContact=eb&&eb.top<window.innerHeight*0.75&&eb.bottom>0;
    if(mb){var mbOn=qb<0&&!inContact&&!themeBar();mb.classList.toggle('on',mbOn);html.classList.toggle('bkd-mb-on',mbOn&&window.innerWidth<1000);}
    var on=-1,line=lastTop+120;
    targets.forEach(function(t,i){if(t&&t.getBoundingClientRect().top<line){on=i;}});
    links.forEach(function(a,i){a.classList.toggle('on',i===on);});
    if(on!==lastOn){lastOn=on;var box=links[0]&&links[0].parentNode;if(on>=0&&box&&box.scrollWidth>box.clientWidth+2){var a=links[on];box.scrollTo({left:Math.max(0,a.offsetLeft-box.clientWidth/2+a.offsetWidth/2),behavior:reduce?'auto':'smooth'});}}
  }
  var lastOn=-2;
  function navOverflow(){$$('.bkd-subnav-links,.bkd-un-in').forEach(function(box){var max=box.scrollWidth-box.clientWidth;box.classList.toggle('bkd-ovf',max>2&&box.scrollLeft<max-2);box.classList.toggle('bkd-ovl',max>2&&box.scrollLeft>2);});}
  $$('.bkd-subnav-links,.bkd-un-in').forEach(function(box){box.addEventListener('scroll',navOverflow,{passive:true});});
  var unCur=$('.bkd-un-in [aria-current]');if(unCur&&unCur.offsetLeft+unCur.offsetWidth>unCur.parentNode.clientWidth){unCur.parentNode.scrollLeft=unCur.offsetLeft-24;navOverflow();}
  navOverflow();window.addEventListener('resize',navOverflow);
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
		return blockke_bkn_dev_js( $js ); // v1.6: the card context (bedrooms, "Back to …"), only while its switches are on
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
		return blockke_dev_layout_auto( $id );
	}

	/** Without a per-listing choice: developments when 'auto' is on; their units for sale when 'auto_units' is on (true, or the unit's area slug is listed). */
	function blockke_dev_layout_auto( $id ) {
		$cfg = blockke_dev_config();
		if ( ! empty( $cfg['auto'] ) && '1' === (string) get_post_meta( $id, 'property_has_subunits', true ) ) {
			return true;
		}
		$au = $cfg['auto_units'];
		if ( ! $au || ! (int) get_post_meta( $id, 'property_subunits_master', true ) || ! blockke_dev_is_for_sale( $id ) ) {
			return false;
		}
		if ( true === $au ) {
			return true;
		}
		$a = blockke_dev_main_area( $id );
		return is_array( $au ) && $a && in_array( $a->slug, $au, true );
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
		if ( isset( $_GET['bke_layout'] ) && is_scalar( $_GET['bke_layout'] ) && current_user_can( 'edit_post', $id ) ) { // phpcs:ignore WordPress.Security.NonceVerification
			return '1' === (string) $_GET['bke_layout']; // phpcs:ignore WordPress.Security.NonceVerification
		}
		return blockke_dev_layout_on( $id );
	}

	function blockke_dev_count_view( $id ) {
		$cfg = blockke_dev_config();
		if ( empty( $cfg['count_views'] ) || is_preview() || ( is_user_logged_in() && current_user_can( 'edit_post', $id ) ) ) {
			return;
		}
		if ( ! empty( $cfg['prefetch'] ) && isset( $_SERVER['HTTP_SEC_PURPOSE'] ) && false !== stripos( (string) $_SERVER['HTTP_SEC_PURPOSE'], 'prefetch' ) ) {
			return; // v1.6: a card prefetch is not a visit (the visitor may never open it); if opened, the page counts it (blockke_bkn_view())
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
						'area'     => $data['ctx']['area_name'],
						'bands'    => $cfg['budget_bands'],
						'back'     => ! $lp && ! empty( $cfg['back_to_results'] ) ? 1 : 0,
						'bedsMode' => $cfg['beds_mode'],
						'studioCat' => (string) $cfg['studio_cat'],
					);
					if ( $lp && ! empty( $cfg['lp_after_link'] ) && $data['ctx']['area_name'] ) {
						$cx          = $data['ctx'];
						$bl          = blockke_dev_beds_label( $cx );
						$js['after'] = array(
							'url'   => blockke_dev_search_url( array( 'area' => $cx['area_name'], 'action' => $cx['action'], 'beds' => blockke_dev_link_beds( $cx ) ) ),
							'label' => 'While you wait: see other ' . ( $bl ? $bl . ' ' : '' ) . ( 'rent' === $cx['action'] ? 'rentals' : 'homes' ) . ' in ' . $cx['area_name'],
						);
					}
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
		$at   = null; // an area page enquiry: no listing, the Neighbourhood and the segment instead
		if ( ! $lid && (int) $get( 'area', 12 ) && function_exists( 'blockke_area_segments' ) ) {
			$at = get_term( (int) $get( 'area', 12 ), 'property_area' );
			$at = $at instanceof WP_Term ? $at : null;
		}
		$ix = ! $lid && ! $at && '1' === $get( 'index', 2 ); // the new developments index
		if ( ! $at && ! $ix && ( ! $post || 'estate_property' !== $post->post_type || 'publish' !== $post->post_status ) ) {
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
		$source   = preg_replace( '/[^a-z0-9_-]/', '', strtolower( $get( 'source', 30 ) ) ); // keeps "dev_index", so its form label is found
		$intent   = preg_replace( '/[^a-z0-9_-]/', '', strtolower( $get( 'intent', 30 ) ) );
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
		$extra       = array();
		if ( $ix ) {
			$title    = 'New developments';
			$dev_name = $title;
			$area     = '';
			$price    = 0;
			$asked    = '';
			parse_str( $get( 'filters', 200 ), $fq );
			if ( function_exists( 'blockke_index_phrase' ) && is_array( $fq ) ) {
				$fv = array();
				foreach ( array( 'area', 'beds', 'price', 'completion' ) as $k ) {
					$fv[ $k ] = isset( $fq[ $k ] ) && is_scalar( $fq[ $k ] ) ? sanitize_text_field( (string) $fq[ $k ] ) : '';
				}
				$fv['beds']       = in_array( $fv['beds'], array( 'studio', '1', '2', '3', '4' ), true ) ? $fv['beds'] : '';
				$fv['price']      = isset( blockke_index_bands()[ $fv['price'] ] ) ? $fv['price'] : '';
				$fv['completion'] = isset( blockke_index_years()[ $fv['completion'] ] ) ? $fv['completion'] : '';
				$an               = array();
				$at2              = '' !== $fv['area'] ? get_term_by( 'slug', sanitize_title( $fv['area'] ), 'property_area' ) : null;
				if ( $at2 ) {
					$an[ $at2->slug ] = blockke_dev_text( $at2->name );
					$fv['area']       = $at2->slug;
				} elseif ( 'other' !== $fv['area'] ) {
					$fv['area'] = '';
				}
				if ( array_filter( $fv ) ) {
					$extra[] = 'Browsing: ' . blockke_index_phrase( $fv, $an );
				}
			}
			if ( '' !== $get( 'budget', 60 ) ) {
				$extra[] = 'Budget: ' . $get( 'budget', 60 );
			}
			$unit     = implode( ' · ', array_filter( array( $get( 'budget', 60 ) ) ) );
			$area_url = function_exists( 'blockke_index_page_id' ) ? get_permalink( blockke_index_page_id() ) : home_url( '/' );
		} elseif ( $at ) {
			$segs     = blockke_area_segments();
			$seg      = sanitize_key( $get( 'seg', 40 ) );
			$seg      = isset( $segs[ $seg ] ) ? $seg : '';
			$area     = blockke_dev_text( $at->name );
			$title    = ( '' !== $seg ? $segs[ $seg ][0] : 'Property' ) . ' in ' . $area;
			$dev_name = $title;
			$price    = 0;
			$brief    = array();
			foreach ( array( 'look' => 'Looking to', 'ptype' => 'Property type', 'beds' => 'Bedrooms', 'budget' => 'Budget' ) as $k => $l ) {
				$v = $get( $k, 60 );
				if ( '' !== $v ) {
					$extra[] = $l . ': ' . $v;
					$brief[] = 'beds' === $k && preg_match( '/^\d/', $v ) ? $v . ' bed' : $v;
				}
			}
			$asked = $unit !== $title ? $unit : ''; // e.g. the search sheet's summary, from "ask an advisor to shortlist"
			$unit  = $brief ? implode( ' · ', $brief ) : $asked; // the enquiries inbox shows it next to the page's name
			$area_url = blockke_area_url( $at, $seg );
		} else {
			$title    = blockke_dev_text( get_the_title( $lid ) );
			$proj     = blockke_dev_first_term( $lid, 'property_project' );
			$dev_name = $proj ? blockke_dev_text( $proj->name ) : $title;
			$area_t   = blockke_dev_main_area( $lid );
			$area     = $area_t ? blockke_dev_text( $area_t->name ) : '';
			$price    = (float) get_post_meta( $lid, 'property_price', true );
		}
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
			'alternatives' => 'Alternatives request (landing page)',
			'search-shortlist' => 'Shortlist request (from the search)',
			'compare'      => 'Compare request (similar homes)',
			'find'         => 'Find-me-one request (no similar homes listed)',
		);
		$forms['dev_index'] = 'Shortlist request (new developments page)';
		if ( $at ) {
			$forms['hero'] = 'Hero form (send me matches)';
			$forms['find'] = 'Find-me-one request (none listed)';
			$forms['inline'] = 'Quick form (between sections)';
		}
		$form_label  = isset( $forms[ $intent ] ) ? $forms[ $intent ] : ( isset( $forms[ $source ] ) ? $forms[ $source ] : $source );

		$lines   = array();
		if ( $ix ) {
			$lines[] = 'NEW DEVELOPMENTS ENQUIRY (new developments page)';
			$lines   = array_merge( $lines, $extra );
		} elseif ( $at ) {
			$lines[] = 'AREA ENQUIRY (area page)';
			$lines[] = 'Looking at: ' . $title;
			if ( '' !== $asked ) {
				$lines[] = 'Interested in: ' . $asked;
			}
			$lines = array_merge( $lines, $extra );
		} else {
			$lines[] = 'DEVELOPMENT ENQUIRY (property page)';
			$lines[] = 'Development: ' . $dev_name . ( $price > 0 ? ' - from ' . blockke_dev_money( $price ) : '' );
			$lines[] = 'Interested in: ' . ( $unit ? $unit : '-' );
		}
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
		$lines[] = $ix ? 'Listing: none (new developments page)' : ( $at ? 'Area: ' . $area . ' (term ' . (int) $at->term_id . ')' : 'Listing: ' . $title . ' (Ref W-' . $lid . ')' );
		$lines[] = 'Page: ' . ( $page ? $page : ( $at || $ix ? $area_url : get_permalink( $lid ) ) );
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
				'form'     => ( $ix ? 'new developments: ' : ( $at ? 'area page: ' : '' ) ) . $source . ( $intent && $intent !== $source ? '/' . $intent : '' ),
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
					'budget'      => '', // the band is in the description; Zoho's budget picklist uses the popup's own bands
					'unit'        => $at || $ix ? $title : trim( $dev_name . ( $unit ? ' - ' . $unit : '' ) ),
					'alerts'      => false,
					'channel'     => $channel,
					'campaign'    => $campaign,
					'page'        => $page ? $page : ( $at || $ix ? $area_url : get_permalink( $lid ) ),
					'page_title'  => $title,
					'description' => $description,
				)
			);
		}

		$to      = blockke_dev_lead_to( $lid );
		$subject = $ix ? sprintf( '[Block] New developments enquiry: %s', $name ) : ( $at ? sprintf( '[Block] Area enquiry: %s - %s', $name, $title ) : null );
		$subject = null !== $subject ? $subject : sprintf( '[Block] Development enquiry: %s - %s%s', $name, $dev_name, $unit ? ' - ' . $unit : '' );
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
		$brochure = $at || $ix ? '' : blockke_dev_brochure( $lid );
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
			'bke_dev_plan_text'        => array( 'Payment plan text', 'textarea', 'Empty: the first paragraph of the Payment Plan section. On a ready home it replaces the Finance text.' ),
			'bke_dev_calc_price'       => array( 'Calculator price (KES)', 'number', 'The price the calculator starts from, e.g. the mortgage price when the cash price shown is lower. A ready home\'s calculator then reads "Mortgage price". Empty: the "from" price.' ),
			'bke_dev_places'           => array( 'Places nearby', 'textarea', 'One per line as Place | travel time. Shown under Address and in Location. Empty: the description\'s Location section, else straight-line distances from the map pin.' ),
			'bke_dev_hero_label'       => array( 'Main photo label', 'text', 'Shown on the main photo, e.g. Show apartment. Empty: "Project render" on off-plan listings, nothing otherwise. "none" hides it.' ),
			'bke_dev_gallery_note'     => array( 'Gallery note', 'text', 'Under the photos, e.g. Photos of the show apartment. Empty: "Project renders." on off-plan listings, else "Photos of {development}." "none" hides it.' ),
			'bke_dev_brochure'         => array( 'Brochure (PDF link)', 'url', 'Opens for the visitor right after they send their details. Empty: the first PDF attached to the listing.' ),
			'bke_dev_video'            => array( 'Video (YouTube or Vimeo link)', 'url', 'Empty: the listing\'s video, else its virtual tour.' ),
			'bke_dev_similar'          => array( 'Similar listings (IDs)', 'text', 'Comma-separated listing IDs, shown first under "Also consider". The rest are picked automatically: homes with the same bedrooms in the same area at a similar price.' ),
			'bke_dev_area'             => array( 'Main area (slug)', 'text', 'For listings tagged with more than one area, e.g. westlands. Empty: the development\'s area, else the area with most listings.' ),
			'bke_dev_similar_off'      => array( 'Hide "Also consider"', 'checkbox', 'No similar homes block on this page.' ),
		);
	}

	/* Similar homes and the search lists are cached; any change to a published listing that matters to them starts a fresh round. */
	function blockke_dev_sim_bump() {
		static $done = false;
		if ( $done ) {
			return;
		}
		$done = true;
		update_option( 'bkd_sim_ver', (string) microtime( true ), false );
		// again once the request ends, so nothing rebuilt halfway through a save is kept under the new version
		add_action(
			'shutdown',
			function () {
				update_option( 'bkd_sim_ver', (string) microtime( true ), false );
			},
			999
		);
	}

	function blockke_dev_sim_live( $id ) {
		return 'estate_property' === get_post_type( $id ) && 'publish' === get_post_status( $id );
	}

	function blockke_dev_sim_taxes() {
		return array( 'property_area', 'property_action_category', 'property_status', 'property_category', 'property_project', 'property_completion', 'property_city' );
	}

	add_action(
		'save_post_estate_property',
		function ( $pid, $post ) {
			if ( ! wp_is_post_revision( $pid ) && ! wp_is_post_autosave( $pid ) && $post && 'publish' === $post->post_status ) {
				blockke_dev_sim_bump();
			}
		},
		10,
		2
	);
	add_action(
		'transition_post_status',
		function ( $new, $old, $post ) {
			if ( $post && 'estate_property' === $post->post_type && $new !== $old && ( 'publish' === $new || 'publish' === $old ) ) {
				blockke_dev_sim_bump(); // published, unpublished, trashed or restored
			}
		},
		10,
		3
	);
	add_action(
		'deleted_post',
		function ( $pid, $post = null ) {
			if ( $post && 'estate_property' === $post->post_type && 'publish' === $post->post_status ) {
				blockke_dev_sim_bump();
			}
		},
		10,
		2
	);
	foreach ( array( 'set_object_terms', 'deleted_term_relationships' ) as $bkd_hook ) {
		add_action(
			$bkd_hook,
			function ( $object_id, $a = null, $b = null, $taxonomy = null ) use ( $bkd_hook ) {
				$tax = 'set_object_terms' === $bkd_hook ? $taxonomy : $b; // deleted_term_relationships: ( $object_id, $tt_ids, $taxonomy )
				if ( in_array( $tax, blockke_dev_sim_taxes(), true ) && blockke_dev_sim_live( $object_id ) ) {
					blockke_dev_sim_bump();
				}
			},
			10,
			4
		);
	}
	add_action(
		'edited_term',
		function ( $term_id, $tt_id, $taxonomy ) {
			if ( in_array( $taxonomy, blockke_dev_sim_taxes(), true ) ) {
				blockke_dev_sim_bump(); // a renamed area or project
			}
		},
		10,
		3
	);
	foreach ( array( 'added_post_meta', 'updated_post_meta', 'deleted_post_meta' ) as $bkd_hook ) {
		add_action(
			$bkd_hook,
			function ( $mid, $object_id, $meta_key ) {
				// the last four feed the v1.6 cards (layout, the editor's line, the photo count), often written without a post save
				if ( in_array( $meta_key, array( 'property_price', 'property_label', 'property_label_before', 'property_bedrooms', 'property_size', 'property_subunits_master', 'property_has_subunits', 'property_subunits_list', '_thumbnail_id', 'bke_dev_similar', 'bke_dev_area', 'bke_completion_stated', 'bke_unit_label', 'prop_featured', 'bke_dev_layout', 'bke_index_line', 'wpestate_property_gallery', 'image_to_attach' ), true ) && blockke_dev_sim_live( $object_id ) ) {
					blockke_dev_sim_bump();
				}
			},
			10,
			3
		);
	}
	unset( $bkd_hook );

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
							$auto = blockke_dev_layout_auto( $post->ID );
							echo '<select id="bke_dev_layout" name="bke_dev_layout">'
								. '<option value=""' . selected( $mode, '', false ) . '>Automatic (currently ' . ( $auto ? 'on' : 'off' ) . ')</option>'
								. '<option value="on"' . selected( $mode, 'on', false ) . '>On: development layout</option>'
								. '<option value="off"' . selected( $mode, 'off', false ) . '>Off: classic property page</option></select>';
						} elseif ( 'checkbox' === $f[1] ) {
							echo '<input type="hidden" name="' . esc_attr( $key ) . '" value=""><label><input type="checkbox" id="' . esc_attr( $key ) . '" name="' . esc_attr( $key ) . '" value="1"' . checked( '' !== $val, true, false ) . '> Yes</label>';
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
				} elseif ( 'checkbox' === $f[1] ) {
					$v = '1' === (string) $raw ? '1' : '';
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

	/* =====================================================================
	   3) Area pages (v1.5): a Neighbourhood archive (/property_area/lavington/) and its segment pages
	      (/property_area/lavington/villas-for-sale/) on the new layout. Every grid holds one kind of listing:
	      homes, commercial or land, and either for sale or for rent, never mixed.
	   ===================================================================== */

	/** Segments: slug => array( heading, action ('sale'|'rent'), kind ('home'|'commercial'|'land'), type ('' = any), parent slug ). */
	function blockke_area_segments() {
		return array(
			'homes-for-sale'      => array( 'Homes for sale', 'sale', 'home', '', '' ),
			'apartments-for-sale' => array( 'Apartments for sale', 'sale', 'home', 'apartment', 'homes-for-sale' ),
			'villas-for-sale'     => array( 'Villas for sale', 'sale', 'home', 'villa', 'homes-for-sale' ),
			'houses-for-sale'     => array( 'Houses for sale', 'sale', 'home', 'house', 'homes-for-sale' ),
			'studios-for-sale'    => array( 'Studios for sale', 'sale', 'home', 'studio', 'homes-for-sale' ),
			'homes-for-rent'      => array( 'Homes for rent', 'rent', 'home', '', '' ),
			'apartments-for-rent' => array( 'Apartments for rent', 'rent', 'home', 'apartment', 'homes-for-rent' ),
			'villas-for-rent'     => array( 'Villas for rent', 'rent', 'home', 'villa', 'homes-for-rent' ),
			'houses-for-rent'     => array( 'Houses for rent', 'rent', 'home', 'house', 'homes-for-rent' ),
			'studios-for-rent'    => array( 'Studios for rent', 'rent', 'home', 'studio', 'homes-for-rent' ),
			'commercial-for-sale' => array( 'Commercial property for sale', 'sale', 'commercial', '', '' ),
			'offices-for-sale'    => array( 'Offices for sale', 'sale', 'commercial', 'office', 'commercial-for-sale' ),
			'warehouses-for-sale' => array( 'Warehouses for sale', 'sale', 'commercial', 'warehouse', 'commercial-for-sale' ),
			'shops-for-sale'      => array( 'Shops for sale', 'sale', 'commercial', 'shop', 'commercial-for-sale' ),
			'commercial-for-rent' => array( 'Commercial property for rent', 'rent', 'commercial', '', '' ),
			'offices-for-rent'    => array( 'Offices for rent', 'rent', 'commercial', 'office', 'commercial-for-rent' ),
			'warehouses-for-rent' => array( 'Warehouses for rent', 'rent', 'commercial', 'warehouse', 'commercial-for-rent' ),
			'shops-for-rent'      => array( 'Shops for rent', 'rent', 'commercial', 'shop', 'commercial-for-rent' ),
			'land-for-sale'       => array( 'Land for sale', 'sale', 'land', '', '' ),
			'land-for-rent'       => array( 'Land for lease', 'rent', 'land', '', '' ),
		);
	}

	/** Whether this area gets the new layout: config 'area_pages' (true, or area slugs), or an editor's ?bke_area=1 / 0 preview. */
	function blockke_area_on( $term ) {
		$cfg = blockke_dev_config();
		if ( ! $term instanceof WP_Term || empty( $cfg['enabled'] ) ) {
			return false;
		}
		if ( isset( $_GET['bke_area'] ) && is_scalar( $_GET['bke_area'] ) && current_user_can( 'edit_posts' ) ) { // phpcs:ignore WordPress.Security.NonceVerification
			return '1' === (string) $_GET['bke_area']; // phpcs:ignore WordPress.Security.NonceVerification
		}
		$on = $cfg['area_pages'];
		return true === $on || ( is_array( $on ) && in_array( $term->slug, $on, true ) );
	}

	/** A listing's kind and type: array( 'home'|'commercial'|'land', 'apartment'|'villa'|'house'|'studio'|'office'|'warehouse'|'shop'|'land'|'' ). */
	function blockke_area_kind( $id ) {
		$cfg  = blockke_dev_config();
		$cats = get_the_terms( $id, 'property_category' );
		$s    = is_array( $cats ) ? wp_list_pluck( $cats, 'slug' ) : array();
		if ( array_intersect( $s, array( 'land', 'residential-land', 'agricultural', 'industrial-land' ) ) ) {
			return array( 'land', 'land' );
		}
		$home_cats = array( 'apartments', 'villas-residential', 'houses-residential', 'residential', 'gated-community', (string) $cfg['studio_cat'] );
		$beds      = (int) blockke_dev_pm( $id, 'property_bedrooms' );
		$is_dev    = '1' === (string) blockke_dev_pm( $id, 'property_has_subunits' );
		// Commercial unless it is also tagged as a home and has bedrooms (a flat above shops stays a home); a mixed-use
		// development's main listing, which has no bedrooms of its own, stays with its homes
		if ( array_intersect( $s, array( 'commercial', 'offices', 'warehouses', 'shop' ) ) && ! ( array_intersect( $s, $home_cats ) && ( $beds > 0 || $is_dev ) ) ) {
			$t = in_array( 'offices', $s, true ) ? 'office' : ( in_array( 'warehouses', $s, true ) ? 'warehouse' : ( in_array( 'shop', $s, true ) ? 'shop' : '' ) );
			return array( 'commercial', $t );
		}
		if ( in_array( 'villas-residential', $s, true ) ) {
			return array( 'home', 'villa' );
		}
		if ( in_array( 'houses-residential', $s, true ) ) {
			return array( 'home', 'house' );
		}
		if ( ( '' !== (string) $cfg['studio_cat'] && in_array( (string) $cfg['studio_cat'], $s, true ) ) || ( ! $is_dev && 'studio' === blockke_dev_sim_key( $id ) ) ) {
			return array( 'home', 'studio' );
		}
		return array( 'home', in_array( 'apartments', $s, true ) ? 'apartment' : '' ); // "Residential" or "Gated Community" only: Homes, no type
	}

	/** 'sale', 'rent', or '' for a home whose price contradicts its Buy/Rent tag (left out, as in similar homes). */
	function blockke_area_action( $id, $kind ) {
		$a       = blockke_dev_action( $id );
		$label   = (string) blockke_dev_pm( $id, 'property_label' );
		$monthly = false !== stripos( $label, 'month' );
		$price   = (float) blockke_dev_pm( $id, 'property_price' );
		if ( 'sale' === $a && $monthly && has_term( 'for-rent', 'property_action_category', $id ) ) {
			return 'rent'; // tagged both, priced by the month
		}
		if ( 'home' !== $kind ) {
			if ( $monthly ) {
				return 'rent';
			}
			if ( 'sale' === $a && $price > 0 && $price < 1000000 && 'unit' !== blockke_dev_price_flag( $id ) ) { // a monthly figure on a listing tagged for sale
				return has_term( 'for-rent', 'property_action_category', $id ) ? 'rent' : ( 'commercial' === $kind ? '' : $a );
			}
			return $a;
		}
		if ( 'rent' === $a && $price > 2000000 && ! $monthly ) {
			return '';
		}
		if ( 'sale' === $a && ( $monthly || ( $price > 0 && $price < 1000000 && 'unit' !== blockke_dev_price_flag( $id ) ) ) ) {
			return '';
		}
		return $a;
	}

	/**
	 * Bedrooms named in a title: array( "Studio–2 bed", bedroom keys ) for "Studio, 1-Bed & 2-Bed" or "1, 2 & 3 Bedroom";
	 * with $single, also one count ("3 Bedroom Apartments"), for a development listed without its homes. Else array( '', array() ).
	 */
	function blockke_area_title_beds( $id, $single = false ) {
		$t   = blockke_dev_text( get_the_title( $id ) );
		$bed = '(?:\s*-?\s*(?:bed(?:room)?s?|br)\b)';
		$n   = array();
		if ( preg_match_all( '/(?:\bstudios?|\b[1-9])' . $bed . '?(?:\s*(?:,|&|\band\b|–|—|-|\bto\b)\s*(?:studios?|[1-9])' . $bed . '?)+' . $bed . '?/iu', $t, $mm ) ) {
			foreach ( $mm[0] as $m ) {
				if ( preg_match( '/bed|br\b/i', $m ) ) {
					preg_match_all( '/studio|[1-9]/i', $m, $all );
					foreach ( $all[0] as $x ) {
						$n[] = ctype_digit( $x ) ? (int) $x : 0;
					}
					break;
				}
			}
		}
		if ( ! $n && $single && preg_match( '/\b([1-9])' . $bed . '|\b(studio)s?\b/iu', $t, $m1 ) ) {
			$n[] = ! empty( $m1[1] ) ? (int) $m1[1] : 0;
		}
		if ( ! $n ) {
			return array( '', array() );
		}
		$lo   = min( $n );
		$hi   = max( $n );
		$keys = array();
		for ( $b = $lo; $b <= $hi; $b++ ) {
			$keys[] = 0 === $b ? 'studio' : (string) min( 4, $b );
		}
		$name = function ( $b ) {
			return 0 === $b ? 'Studio' : (string) $b;
		};
		return array( ( $lo === $hi ? ( 0 === $lo ? 'Studio' : $lo . ' bed' ) : $name( $lo ) . '–' . $hi . ' bed' ), array_values( array_unique( $keys ) ) );
	}

	/** One card from the rows of one development (or one listing) that match a segment. $all: every row of that development. */
	function blockke_area_card( $match, $all, $seg ) {
		$rent   = 'rent' === $seg[1];
		$master = null;
		foreach ( $all as $r ) {
			if ( $r['is_dev'] ) {
				$master = $r;
				break;
			}
		}
		$units = array_values(
			array_filter(
				$match,
				function ( $r ) {
					return ! $r['is_dev'];
				}
			)
		);
		$pool  = $units ? $units : $match;
		$price = array();
		foreach ( $pool as $r ) {
			if ( $r['price'] > 0 ) {
				$price[] = $r['price'];
			}
		}
		usort(
			$pool,
			function ( $a, $b ) {
				return ( $a['price'] > 0 ? $a['price'] : PHP_INT_MAX ) <=> ( $b['price'] > 0 ? $b['price'] : PHP_INT_MAX );
			}
		);
		$target = $master ? $master : $pool[0];
		$img    = $target['img'];
		foreach ( $match as $r ) {
			if ( ! $img && $r['img'] ) {
				$img = $r['img'];
			}
		}
		$min  = $price ? min( $price ) : 0;
		$max  = $price ? max( $price ) : 0;
		$many = count( $units ) > 1 || ( $master && ! $units );
		$flag = 1 === count( $pool ) ? $pool[0]['flag'] : '';
		$unit = false;
		foreach ( $pool as $r ) {
			$unit = $unit || 'unit' === $r['flag'];
		}
		// Price
		if ( $min <= 0 ) {
			$ptxt = 'Price on request';
		} elseif ( $unit ) {
			$ptxt = ( $many ? 'From ' : '' ) . blockke_dev_money( $min ) . ' per sq ft' . ( $rent ? ' a month' : '' );
		} elseif ( $rent ) {
			$ptxt = ( $many ? 'From ' : '' ) . blockke_dev_short( $min ) . ' / month';
		} elseif ( 'upto' === $flag ) {
			$ptxt = 'Up to ' . blockke_dev_short( $min );
		} else {
			$ptxt = ( $many || $target['offplan'] || ( $master && 'home' === $seg[2] ) ? 'From ' : '' ) . blockke_dev_short( $min );
		}
		// What it is: bedrooms (homes) or the type, then the size or how many homes
		$keys = array();
		foreach ( $pool as $r ) {
			if ( '' !== $r['key'] && ! $r['is_dev'] ) {
				$keys[ $r['key'] ] = 1;
			}
		}
		$order = array( 'studio' => 0, '1' => 1, '2' => 2, '3' => 3, '4' => 4 );
		$keys  = array_map( 'strval', array_keys( $keys ) ); // array keys '1'..'4' come back as integers
		usort(
			$keys,
			function ( $a, $b ) use ( $order ) {
				return $order[ $a ] - $order[ $b ];
			}
		);
		$m      = array();
		$ranged = false; // bedrooms read from the title: one listing for several homes, so no single size
		if ( 'home' === $seg[2] ) {
			$tb = $keys ? array( '', array() ) : blockke_area_title_beds( $target['id'], $target['is_dev'] );
			if ( '' !== $tb[0] ) {
				$keys   = $tb[1];
				$ranged = count( $keys ) > 1 || $target['is_dev'];
				$m[]    = $tb[0];
			} elseif ( count( $keys ) > 1 ) {
				$lo  = $keys[0];
				$hi  = end( $keys );
				$top = 0;
				foreach ( $pool as $r ) {
					$top = max( $top, '4' === $r['key'] ? (int) $r['beds'] : 0 );
				}
				$m[] = ( 'studio' === $lo ? 'Studio' : $lo ) . '–' . ( '4' === $hi ? ( $top > 4 ? $top : '4' ) : $hi ) . ' bed';
			} elseif ( 1 === count( $keys ) ) {
				$k   = $keys[0];
				$m[] = 'studio' === $k ? 'Studio' : ( '4' === $k ? max( 4, (int) $pool[0]['beds'] ) : $k ) . ( 'studio' === $k ? '' : ' bed' );
			}
		} else {
			$types = array( 'office' => 'Office', 'warehouse' => 'Warehouse', 'shop' => 'Shop', 'land' => 'Land' );
			$m[]   = isset( $types[ $pool[0]['type'] ] ) ? $types[ $pool[0]['type'] ] : ( 'land' === $seg[2] ? 'Land' : 'Commercial' );
		}
		if ( count( $units ) > 1 ) {
			$m[] = count( $units ) . ( 'home' === $seg[2] ? ' homes' : ' listings' );
		} elseif ( 1 === count( $pool ) && $pool[0]['sqft'] > 0 && ! $pool[0]['is_dev'] && ! $ranged ) {
			$m[] = blockke_dev_num( round( $pool[0]['sqft'] * blockke_dev_sqft_factor() ) ) . ' m²';
		}
		$date = 0;
		$feat = false;
		foreach ( $match as $r ) {
			$date = max( $date, $r['date'] );
			$feat = $feat || $r['featured'];
		}
		return array(
			'id'    => $target['id'],
			'url'   => get_permalink( $target['id'] ),
			'img'   => (int) $img,
			'name'  => blockke_dev_listing_name( $target['id'] ),
			'price' => $min,
			'max'   => $max,
			'ptxt'  => $ptxt,
			'unit'  => $unit,
			'meta'  => implode( ' · ', array_filter( $m ) ),
			'badge' => ! $rent && 'home' === $seg[2] ? blockke_dev_stage( $target['id'] ) : '',
			'beds'  => $keys,
			'date'  => $date,
			'feat'  => $feat,
		);
	}

	/**
	 * Everything an area page shows, per segment: cards (one per development or listing), counts and price range.
	 * Cached per area (transient bkd_area_{term id}); rebuilt when any listing changes (option bkd_sim_ver).
	 */
	function blockke_area_data( $term, $build = true ) {
		if ( blockke_bkn_on( 'card_v16', 'bke_cards' ) ) { // the shared rows are one cache for every area, so there is nothing per area to skip; an editor's preview is kept per request, never cached
			return blockke_area_v16_data( $term );
		}
		$cfg = blockke_dev_config();
		$ver = (string) get_option( 'bkd_sim_ver', '0' ) . '|v15|' . md5( wp_json_encode( array( $cfg['studio_cat'], $cfg['area_cap'], $cfg['beds_mode'] ) ) );
		$tk  = 'bkd_area_' . (int) $term->term_id;
		$hit = get_transient( $tk );
		if ( is_array( $hit ) && isset( $hit['ver'] ) && $hit['ver'] === $ver ) {
			return $hit['data'];
		}
		if ( ! $build ) {
			return null;
		}
		$q   = new WP_Query(
			array(
				'post_type'           => 'estate_property',
				'post_status'         => 'publish',
				'fields'              => 'ids',
				'no_found_rows'       => true,
				'ignore_sticky_posts' => true,
				'posts_per_page'      => max( 50, (int) $cfg['area_cap'] ),
				'orderby'             => 'date',
				'order'               => 'DESC',
				'tax_query'           => array( // phpcs:ignore WordPress.DB.SlowDBQuery
					'relation' => 'AND',
					array( 'taxonomy' => 'property_area', 'field' => 'term_id', 'terms' => array( (int) $term->term_id ), 'include_children' => true ),
					array( 'taxonomy' => 'property_status', 'field' => 'slug', 'terms' => array( 'sold', 'let' ), 'operator' => 'NOT IN' ),
				),
			)
		);
		$ids = array_map( 'intval', $q->posts );
		if ( count( $ids ) >= max( 50, (int) $cfg['area_cap'] ) ) {
			error_log( 'BLOCK area pages: ' . $term->slug . ' reached ' . count( $ids ) . ' listings; raise area_cap if listings go missing.' ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions
		}
		if ( $ids && function_exists( '_prime_post_caches' ) ) {
			_prime_post_caches( $ids, true, false );
		}
		blockke_dev_pm_prime( $ids );
		$groups = array();
		$cities = array();
		$proj   = array(); // development key => its Project, so two listings of one development share a card
		foreach ( $ids as $pid ) {
			$pt = blockke_dev_first_term( $pid, 'property_project' );
			$dk = blockke_dev_dev_key( $pid );
			if ( $pt && empty( $proj[ $dk ] ) ) {
				$proj[ $dk ] = 'p' . (int) $pt->term_id;
			}
		}
		foreach ( $ids as $pid ) {
			$kt     = blockke_area_kind( $pid );
			$action = blockke_area_action( $pid, $kt[0] );
			if ( '' === $action ) {
				continue;
			}
			$city = blockke_dev_first_term( $pid, 'property_city' );
			if ( $city ) {
				$cities[ blockke_dev_text( $city->name ) ] = ( isset( $cities[ blockke_dev_text( $city->name ) ] ) ? $cities[ blockke_dev_text( $city->name ) ] : 0 ) + 1;
			}
			$dk              = blockke_dev_dev_key( $pid );
			$gk              = ( isset( $proj[ $dk ] ) ? $proj[ $dk ] : $dk ) . '|' . $action . '|' . $kt[0];
			$groups[ $gk ][] = array(
				'id'       => $pid,
				'is_dev'   => '1' === (string) blockke_dev_pm( $pid, 'property_has_subunits' ),
				'kind'     => $kt[0],
				'type'     => $kt[1],
				'action'   => $action,
				'price'    => (float) blockke_dev_pm( $pid, 'property_price' ),
				'flag'     => blockke_dev_price_flag( $pid ),
				'sqft'     => (float) blockke_dev_pm( $pid, 'property_size' ),
				'beds'     => (int) blockke_dev_pm( $pid, 'property_bedrooms' ),
				'key'      => 'home' === $kt[0] ? blockke_dev_sim_key( $pid ) : '',
				'offplan'  => blockke_dev_is_offplan( $pid ),
				'featured' => '1' === (string) blockke_dev_pm( $pid, 'prop_featured' ),
				'img'      => (int) blockke_dev_pm( $pid, '_thumbnail_id' ),
				'date'     => (int) get_post_time( 'U', true, $pid ),
			);
		}
		$segs = array();
		foreach ( blockke_area_segments() as $slug => $seg ) {
			$cards = array();
			foreach ( $groups as $rows ) {
				if ( $rows[0]['action'] !== $seg[1] || $rows[0]['kind'] !== $seg[2] ) {
					continue;
				}
				$match = '' === $seg[3] ? $rows : array_values(
					array_filter(
						$rows,
						function ( $r ) use ( $seg ) {
							return $r['type'] === $seg[3];
						}
					)
				);
				if ( $match ) {
					$cards[] = blockke_area_card( $match, $rows, $seg );
				}
			}
			usort(
				$cards,
				function ( $a, $b ) {
					return $a['feat'] !== $b['feat'] ? ( $a['feat'] ? -1 : 1 ) : $b['date'] - $a['date'];
				}
			);
			$ps  = array();
			$per = false;
			foreach ( $cards as $c ) {
				if ( $c['unit'] ) { // a price per sq ft: no range when commercial listings mix the two; a home priced so is left out of it
					$per = $per || 'home' !== $seg[2];
					continue;
				}
				if ( $c['price'] > 0 ) {
					$ps[] = $c['price'];
				}
				if ( $c['max'] > 0 ) {
					$ps[] = $c['max'];
				}
			}
			$segs[ $slug ] = array(
				'n'     => count( $cards ),
				'cards' => $cards,
				'min'   => $ps && ! $per ? min( $ps ) : 0,
				'max'   => $ps && ! $per ? max( $ps ) : 0,
			);
		}
		arsort( $cities );
		$data = array(
			'segs' => $segs,
			'city' => $cities ? (string) key( $cities ) : '',
		);
		set_transient( $tk, array( 'ver' => $ver, 'data' => $data ), max( 300, (int) $cfg['sim_ttl'] ) );
		return $data;
	}

	/**
	 * The segment page URL: /property_area/lavington/villas-for-sale/ ('' = the area page). Editors previewing keep ?bke_area=1
	 * and the v1.6 cards preview (?bke_cards=1 / 0).
	 */
	function blockke_area_url( $term, $slug = '', $preview = true ) {
		$base = get_term_link( $term );
		if ( is_wp_error( $base ) ) {
			return '';
		}
		$url = '' === $slug ? $base : ( get_option( 'permalink_structure' ) ? trailingslashit( $base ) . $slug . '/' : add_query_arg( 'bka_seg', $slug, $base ) );
		return $preview ? blockke_area_keep_preview( $url ) : $url;
	}

	/** A link between area pages keeps an editor's previews: ?bke_area=1, and ?bke_cards=1 / 0 (the v1.6 cards, card_v16). */
	function blockke_area_keep_preview( $url ) {
		if ( ! empty( $GLOBALS['blockke_area_preview'] ) ) {
			$url = add_query_arg( 'bke_area', '1', $url );
		}
		if ( isset( $GLOBALS['blockke_cards_preview'] ) && '' !== $GLOBALS['blockke_cards_preview'] ) {
			$url = add_query_arg( 'bke_cards', $GLOBALS['blockke_cards_preview'], $url );
		}
		return $url;
	}

	/** "Villas for Sale in Lavington, Nairobi": Title Case for titles. */
	function blockke_area_title_case( $s ) {
		$small = array( 'in', 'for', 'to', 'and', 'of', 'at', 'on', 'a', 'the' );
		$w     = explode( ' ', $s );
		foreach ( $w as $i => $x ) {
			$w[ $i ] = 0 === $i || ! in_array( strtolower( $x ), $small, true ) ? ucfirst( $x ) : strtolower( $x );
		}
		return implode( ' ', $w );
	}

	/** A sub-segment adds nothing when it holds every card of its parent ("Apartments for sale" when all homes are apartments). */
	function blockke_area_same_as_parent( $data, $slug ) {
		$segs = blockke_area_segments();
		$p    = $segs[ $slug ][4];
		return '' !== $p && $data['segs'][ $slug ]['n'] > 0 && $data['segs'][ $slug ]['n'] === $data['segs'][ $p ]['n'];
	}

	/** "from KES 4.2M to KES 23.5M a month", "at KES 13M" (one listing at one price), "from KES 25M", or ''. */
	function blockke_area_range( $x, $rent ) {
		if ( $x['min'] <= 0 ) {
			return '';
		}
		$m = $rent ? ' a month' : '';
		if ( $x['max'] > $x['min'] ) {
			return 'from ' . blockke_dev_short( $x['min'] ) . ' to ' . blockke_dev_short( $x['max'] ) . $m;
		}
		$one = 1 === (int) $x['n'] && ( isset( $x['cards'][0]['ptxt'] ) ? 0 !== strpos( $x['cards'][0]['ptxt'], 'From' ) : count( (array) $x['cards'][0]['beds'] ) < 2 ); // a v1.6 card with one price (min = max) and one size
		return ( $one ? 'at ' : 'from ' ) . blockke_dev_short( $x['min'] ) . $m;
	}

	/** Everything one area page or segment page needs. */
	function blockke_area_page( $term, $slug ) {
		$cfg   = blockke_dev_config();
		$data  = blockke_area_data( $term );
		$segs  = blockke_area_segments();
		$area  = blockke_dev_text( $term->name );
		$city  = '' !== $data['city'] && strcasecmp( $data['city'], $area ) ? $data['city'] : '';
		$seg   = '' !== $slug ? $segs[ $slug ] : null;
		$sd    = $seg ? $data['segs'][ $slug ] : null;
		$label = $seg ? $seg[0] : 'Property';
		$url   = blockke_area_url( $term, $slug, false );
		$where = $area . ( '' !== $city ? ', ' . $city : '' );
		$v16   = ! empty( $data['v16'] );
		$noun  = function ( $n ) use ( $v16, $sd, $seg ) {
			if ( $v16 ) { // the v1.6 cards are developments: the page counts the homes in them ("253 homes"), as search does
				return blockke_area_v16_count( $sd['homes'], $seg[2] );
			}
			return 1 === (int) $n ? '1 listing' : (int) $n . ' listings';
		};
		$tag   = '';
		$desc  = '';
		if ( $seg ) {
			$rg = blockke_area_range( $sd, 'rent' === $seg[1] );
			if ( $sd['n'] ) {
				$tag  = $noun( $sd['n'] ) . ( '' !== $rg ? ( 0 === strpos( $rg, 'at ' ) ? ' ' : ', ' ) . $rg : '' ) . '.';
				$desc = $label . ' in ' . $where . ': ' . $noun( $sd['n'] ) . ( '' !== $rg ? ' ' . $rg : '' ) . '. Compare photos and prices, then speak to a ' . $cfg['brand_full'] . ' advisor.';
			} else {
				$desc = 'Looking for ' . strtolower( $label ) . ' in ' . $where . '? Tell ' . $cfg['brand_full'] . ' what you need and an advisor will send you matches as they come up.';
			}
		} else {
			$bits = array();
			foreach ( array( 'homes-for-sale' => 'homes for sale', 'homes-for-rent' => 'homes for rent', 'commercial-for-sale' => 'commercial property for sale', 'commercial-for-rent' => 'commercial property for rent', 'land-for-sale' => 'land for sale', 'land-for-rent' => 'land for lease' ) as $s => $w ) {
				$x = $data['segs'][ $s ];
				if ( $x['n'] ) {
					$bits[] = $w . ( $x['min'] > 0 ? ' from ' . blockke_dev_short( $x['min'] ) . ( 'rent' === $segs[ $s ][1] ? ' a month' : '' ) : '' );
				}
			}
			$last = array_pop( $bits );
			$tag  = $last ? ucfirst( ( $bits ? implode( ', ', $bits ) . ' and ' : '' ) . $last ) . '.' : '';
		}
		$head  = blockke_area_title_case( $label ) . ' in ' . $area;
		$title = $head . ( '' !== $city ? ', ' . $city : '' ) . ' | ' . $cfg['brand_full'];
		if ( mb_strlen( $title ) > 60 ) {
			$title = $head . ' | ' . $cfg['brand_full'];
		}
		// Canonical: a type page holding exactly its parent's listings points at the parent; a top-level segment that is all
		// this area has, and fits on the area page, points at the area page (no chains: the end of the line each time). On
		// the v1.6 page "fits" means every development of it is there: in the photo strip, the grid or by name under it.
		$cs = $slug;
		if ( $seg && blockke_area_same_as_parent( $data, $cs ) ) {
			$cs = $seg[4];
		}
		if ( '' !== $cs && '' === $segs[ $cs ][4] && ( $v16 ? ! empty( blockke_area_v16_sections( $data )[ $cs ]['all'] ) : $data['segs'][ $cs ]['n'] <= max( 3, (int) $cfg['area_cards'] ) ) ) {
			$tops = 0;
			foreach ( $segs as $s => $x ) {
				$tops += '' === $x[4] && $data['segs'][ $s ]['n'] ? 1 : 0;
			}
			if ( 1 === $tops ) {
				$cs = '';
			}
		}
		$canon = blockke_area_url( $term, $cs, false );
		$kind  = $seg ? $seg[2] : 'home';
		$ok    = function ( $x ) use ( $segs, $data, $cfg ) { // indexable on its own: the area page always; a segment with enough listings
			return '' === $x || $data['segs'][ $x ]['n'] >= (int) ( '' !== $segs[ $x ][4] ? $cfg['area_type_index_min'] : $cfg['area_index_min'] );
		};
		return array(
			'term'   => $term,
			'slug'   => $slug,
			'seg'    => $seg,
			'data'   => $data,
			'area'   => $area,
			'city'   => $city,
			'label'  => $label,
			'name'   => $seg ? $label . ' in ' . $area : 'Property in ' . $area,
			'lname'  => ( $seg ? strtolower( $label ) : 'property' ) . ' in ' . $area, // mid-sentence: "villas for sale in Lavington"
			'url'    => $url,
			'tag'    => $tag,
			'title'  => $title,
			'desc'   => $desc,
			'canon'  => $canon,
			'index'  => $ok( $cs ), // a page pointing its canonical elsewhere follows that page, so the two signals agree
			'action' => $seg ? $seg[1] : 'sale',
			'kind'   => $kind,
			'things' => $seg ? ( 'home' === $kind ? 'homes' : ( 'land' === $kind ? 'land' : 'property' ) ) : 'property', // "Get matching homes / property / land"
		);
	}

	/** The development layout's helpers want a listing; area pages hand them this stand-in. */
	function blockke_area_stub( $p ) {
		$cfg = blockke_dev_config();
		return array(
			'id'       => 0,
			'short'    => $p['name'],
			'units'    => array(),
			'sf_title' => ( 'home' === $p['kind'] ? 'Search homes' : 'Search property' ) . ' in <em>' . esc_html( $p['area'] ) . '</em>',
			'ctx'      => array(
				'area_name' => $p['area'],
				'action'    => $p['action'],
				'key'       => '',
				'beds'      => 0,
				'price'     => 0,
				'flag'      => '',
			),
			'agent'    => blockke_dev_agent( 0 ),
		);
	}

	function blockke_area_card_html( $c, $i, $rent ) {
		$e   = 'esc_html';
		$img = blockke_dev_img( $c['img'], 'medium_large', array( 'loading' => 'lazy', 'decoding' => 'async', 'sizes' => '(min-width: 1000px) 33vw, (min-width: 640px) 50vw, 100vw', 'alt' => '', 'data-ph' => $c['name'] ) ); // the name is the card's heading
		return '<a class="bkd-card bkd-rv" href="' . esc_url( $c['url'] ) . '" data-id="' . (int) $c['id'] . '" data-pos="' . ( $i + 1 ) . '" data-price="' . esc_attr( (string) ( $c['unit'] ? 0 : (float) $c['price'] ) ) . '" data-date="' . (int) $c['date'] . '" data-beds="' . esc_attr( implode( ' ', $c['beds'] ) ) . '"><div class="bkd-card-img">'
			. str_replace( '<div class="bkd-ph">', '<div class="bkd-ph" aria-hidden="true">', $img )
			. ( $c['badge'] ? '<span class="bkd-card-badge">' . $e( $c['badge'] ) . '</span>' : '' )
			. '</div><h3 class="bkd-h3">' . $e( $c['name'] ) . '</h3><b>' . $e( $c['ptxt'] ) . '</b><span>' . $e( $c['meta'] ) . '</span></a>';
	}

	/** Links to the sub-segments of $parent (only those with fewer cards than it), or to its siblings. */
	function blockke_area_type_links( $p, $parent, $current = '', $tag = 'nav' ) {
		$segs  = blockke_area_segments();
		$data  = $p['data'];
		$links = array();
		foreach ( $segs as $s => $seg ) {
			if ( $seg[4] === $parent && $data['segs'][ $s ]['n'] > 0 && ! blockke_area_same_as_parent( $data, $s ) ) {
				$links[ $s ] = array( preg_replace( '/ for (sale|rent|lease)$/', '', $seg[0] ), $data['segs'][ $s ]['n'] );
			}
		}
		if ( ! $links ) {
			return '';
		}
		$h = '<' . $tag . ' class="bka-types"' . ( 'nav' === $tag ? ' aria-label="' . esc_attr( $segs[ $parent ][0] . ' by type' ) . '"' : '' ) . '>';
		if ( '' !== $current ) {
			$h .= '<a href="' . esc_url( blockke_area_url( $p['term'], $parent ) ) . '">All<small>' . (int) $data['segs'][ $parent ]['n'] . '</small></a>';
		}
		foreach ( $links as $s => $l ) {
			$h .= $s === $current ? '<span aria-current="page">' . esc_html( $l[0] ) . '<small>' . (int) $l[1] . '</small></span>' : '<a href="' . esc_url( blockke_area_url( $p['term'], $s ) ) . '">' . esc_html( $l[0] ) . '<small>' . (int) $l[1] . '</small></a>';
		}
		return $h . '</' . $tag . '>';
	}

	/**
	 * Nearby areas (config 'neighbours') with listings of the same segment: array( term, count, url ). Reads areas already
	 * cached and builds at most two more per view, so the first view after a listing changes stays quick.
	 */
	function blockke_area_nearby( $p ) {
		$slug = '' !== $p['slug'] ? $p['slug'] : 'homes-for-sale';
		$segs = blockke_area_segments();
		$out  = array();
		$cold = 2;
		foreach ( blockke_dev_neighbours( $p['term']->slug ) as $ring ) {
			foreach ( $ring as $tid ) {
				$t = get_term( $tid, 'property_area' );
				if ( ! $t instanceof WP_Term || ! $t->count ) {
					continue;
				}
				$d = blockke_area_data( $t, false );
				if ( null === $d && $cold > 0 ) {
					$d = blockke_area_data( $t );
					$cold--;
				}
				if ( ! $d || ! $d['segs'][ $slug ]['n'] ) {
					continue;
				}
				$to = blockke_area_same_as_parent( $d, $slug ) ? $segs[ $slug ][4] : $slug; // not a page that points elsewhere
				if ( ! empty( $d['v16'] ) ) { // v1.6: the homes counted as that page counts them, and the link at its canonical
					$out[] = array( $t, $d['segs'][ $slug ]['homes'], blockke_area_on( $t ) ? blockke_area_keep_preview( blockke_area_page( $t, $to )['canon'] ) : get_term_link( $t ) );
					continue;
				}
				$out[] = array( $t, $d['segs'][ $slug ]['n'], blockke_area_on( $t ) ? blockke_area_url( $t, $to ) : get_term_link( $t ) );
			}
		}
		return array_slice( $out, 0, 8 );
	}

	function blockke_area_full_form( $p ) {
		$cfg  = blockke_dev_config();
		$type = $p['seg'] ? ( '' !== $p['seg'][3] ? ucfirst( $p['seg'][3] ) : ( 'land' === $p['seg'][2] ? 'Land' : '' ) ) : '';
		$h    = '<form class="bkd-form" data-bkd-lead="contact" data-unit="' . esc_attr( $p['name'] ) . '" novalidate><input class="bkd-hp" type="text" name="bkd_hp" tabindex="-1" autocomplete="off" aria-hidden="true">';
		$h   .= '<div class="bkd-two">' . blockke_dev_field( 'name', 'Full name', 'text', 'name' ) . blockke_dev_field( 'phone', 'Phone / WhatsApp', 'tel', 'tel', '07XX XXX XXX' ) . '</div>';
		$h   .= blockke_dev_field( 'email', 'Email', 'email', 'email', '', true );
		$h   .= blockke_dev_chips( 'look', 'Looking to', array( 'Buy', 'Rent' ), 'rent' === $p['action'] ? 'Rent' : 'Buy' );
		$h   .= blockke_dev_chips( 'ptype', 'Property type', array( 'Apartment', 'Villa', 'House', 'Studio', 'Office', 'Warehouse', 'Shop', 'Land' ), $type );
		if ( 'home' === $p['kind'] ) {
			$h .= blockke_dev_chips( 'beds', 'Bedrooms', array( 'Studio', '1', '2', '3', '4', '5+' ), '' );
		}
		$h     .= '<label class="bkd-f"><span>Budget <small>(optional)</small></span><select name="budget"><option value="">Not sure yet</option>';
		$groups = $p['seg'] ? array( $p['action'] => 'rent' === $p['action'] ? 'To rent' : 'To buy' ) : array( 'sale' => 'To buy', 'rent' => 'To rent' );
		foreach ( $groups as $k => $g ) {
			$h .= count( $groups ) > 1 ? '<optgroup label="' . esc_attr( $g ) . '">' : '';
			foreach ( isset( $cfg['budget_bands'][ $k ] ) ? (array) $cfg['budget_bands'][ $k ] : array() as $b ) {
				$h .= '<option>' . esc_html( $b[2] ) . '</option>';
			}
			$h .= count( $groups ) > 1 ? '</optgroup>' : '';
		}
		$ph = 'commercial' === $p['kind'] ? 'Anything we should know: size, parking, budget per sq ft, move-in date.' : ( 'land' === $p['kind'] ? 'Anything we should know: plot size, intended use, title type.' : 'Anything we should know: streets you like, move-in date, must-haves.' );
		$h .= '</select></label>';
		$h .= blockke_dev_chips( 'contact', 'Best way to reach you', array( 'WhatsApp', 'Phone call', 'Email' ), 'WhatsApp' );
		$h .= '<label class="bkd-f"><span>Message <small>(optional)</small></span><textarea name="message" placeholder="' . esc_attr( $ph ) . '"></textarea></label>';
		$h .= '<button class="bkd-btn bkd-btn-navy bkd-btn-block" type="submit">Send my request' . blockke_dev_icon( 'arrow' ) . '</button>' . blockke_area_consent( $p, true ) . '</form>' . blockke_dev_ok();
		return $h;
	}

	function blockke_area_consent( $p, $full = false ) {
		$privacy = blockke_dev_privacy_url();
		return '<div class="bkd-consent">' . ( $full ? 'We\'ll reply the way you\'ve chosen.' : 'We\'ll reply by WhatsApp or phone.' ) . ' By sending this, you agree to be contacted about property in ' . esc_html( $p['area'] ) . '.'
			. ( $privacy ? ' <a href="' . esc_url( $privacy ) . '" target="_blank" rel="noopener">Privacy policy</a>' : '' ) . '</div>';
	}

	/** Name and phone. $arrow: the v1.5 pages' arrow after the button's words (the v1.6 pages have none). */
	function blockke_area_quick_form( $source, $cta, $p, $arrow = true ) {
		return '<form class="bkd-form" data-bkd-lead="' . esc_attr( $source ) . '" data-unit="' . esc_attr( $p['name'] ) . '" novalidate><input class="bkd-hp" type="text" name="bkd_hp" tabindex="-1" autocomplete="off" aria-hidden="true">'
			. '<div class="bkd-qgrid">' . blockke_dev_field( 'name', 'Full name', 'text', 'name' ) . blockke_dev_field( 'phone', 'Phone / WhatsApp', 'tel', 'tel', '07XX XXX XXX' ) . '</div>'
			. '<button class="bkd-btn bkd-btn-navy bkd-btn-block" type="submit">' . esc_html( $cta ) . ( $arrow ? blockke_dev_icon( 'arrow' ) : '' ) . '</button>' . blockke_area_consent( $p ) . '</form>' . blockke_dev_ok();
	}

	/** The area page (no segment) or a segment page. */
	function blockke_area_render( $p ) {
		$cfg   = blockke_dev_config();
		$e     = 'esc_html';
		$segs  = blockke_area_segments();
		$data  = $p['data'];
		$term  = $p['term'];
		$area  = $p['area'];
		$stub  = blockke_area_stub( $p );
		$ag    = $stub['agent'];
		$wa    = 'https://wa.me/' . $ag['wa'] . '?text=' . rawurlencode( 'Hi ' . $cfg['brand'] . ", I'm looking at " . $p['lname'] . ' on block.ke. ' . $p['url'] );
		$s_url = blockke_dev_search_url( array( 'area' => $area, 'action' => $p['action'] ) );
		$homes = 'home' === $p['kind'];
		$sheet = ! empty( $cfg['search'] ) && $homes; // the search sheet looks for homes; commercial and land pages link to the site search
		$s_lbl = $homes ? 'Search homes' : 'Search property';
		$open  = array_sum( array_map( function ( $x ) { return $x['n'] ? 1 : 0; }, $data['segs'] ) );
		$o     = array();
		$o[]   = '<div id="bkd" class="bkd bka" data-area="' . (int) $term->term_id . '" data-seg="' . esc_attr( $p['slug'] ) . '">' . blockke_dev_sprite();

		// Hero: where, what, and the ways in
		$crumbs = '<a href="' . esc_url( home_url( '/' ) ) . '">Home</a>';
		if ( $p['seg'] ) {
			$up      = '' !== $p['seg'][4];
			$crumbs .= '<span aria-hidden="true">/</span><a' . ( $up ? '' : ' class="bkd-cr-up"' ) . ' href="' . esc_url( blockke_area_url( $term ) ) . '">' . $e( $area ) . '</a>';
			if ( $up ) {
				$crumbs .= '<span aria-hidden="true">/</span><a class="bkd-cr-up" href="' . esc_url( blockke_area_url( $term, $p['seg'][4] ) ) . '">' . $e( $segs[ $p['seg'][4] ][0] ) . '</a>';
			}
			$crumbs .= '<span aria-hidden="true">/</span><span aria-current="page">' . $e( $p['label'] ) . '</span>';
		} else {
			$crumbs .= '<span aria-hidden="true">/</span><span aria-current="page">' . $e( $area ) . '</span>';
		}
		$facts = array();
		if ( $p['seg'] ) {
			$sd   = $data['segs'][ $p['slug'] ];
			$rent = 'rent' === $p['action'];
			if ( $sd['n'] ) {
				$facts[] = array( 'Listings', (string) $sd['n'] );
				if ( $sd['min'] > 0 ) {
					$one     = $sd['max'] <= $sd['min'] && 1 === (int) $sd['n'] && 0 !== strpos( $sd['cards'][0]['ptxt'], 'From' );
					$facts[] = array( $one ? ( $rent ? 'Rent' : 'Price' ) : ( $rent ? 'Rent from' : 'From' ), blockke_dev_short( $sd['min'] ) );
					if ( $sd['max'] > $sd['min'] ) {
						$facts[] = array( $rent ? 'Rent up to' : 'Up to', blockke_dev_short( $sd['max'] ) );
					}
				}
			}
		} else {
			foreach ( array( 'homes-for-sale' => 'Homes for sale', 'homes-for-rent' => 'Homes for rent', 'commercial-for-sale' => 'Commercial for sale', 'commercial-for-rent' => 'Commercial for rent', 'land-for-sale' => 'Land for sale', 'land-for-rent' => 'Land for lease' ) as $s => $l ) {
				if ( $data['segs'][ $s ]['n'] && count( $facts ) < 3 ) {
					$facts[] = array( $l, (string) $data['segs'][ $s ]['n'] );
				}
			}
		}
		$hf = '';
		foreach ( $facts as $f ) {
			$hf .= '<div><dt>' . $e( $f[0] ) . '</dt><dd>' . $e( $f[1] ) . '</dd></div>';
		}
		$jumps = '';
		if ( ! $p['seg'] ) {
			foreach ( $segs as $s => $seg ) {
				if ( '' === $seg[4] && $data['segs'][ $s ]['n'] ) {
					$jumps .= '<a href="#bka-' . esc_attr( $s ) . '">' . $e( $seg[0] ) . '<small>' . (int) $data['segs'][ $s ]['n'] . '</small></a>';
				}
			}
		}
		$pill = $p['seg'] ? ( 'rent' === $p['action'] ? ( 'land' === $p['kind'] ? 'For lease' : 'For rent' ) : 'For sale' ) : 'Neighbourhood';
		$o[]  = '<section class="bkd-hero bka-hero" id="bkd-top"><div class="bkd-wrap bka-hero-grid"><div class="bkd-hero-head">'
			. '<div class="bkd-hero-top"><nav class="bkd-crumbs" aria-label="Breadcrumb">' . $crumbs . '</nav><div class="bkd-hero-tools">'
			. ( ! empty( $cfg['search'] ) ? '<a class="bkd-srch"' . ( $sheet ? ' data-bkd-search="hero" aria-haspopup="dialog"' : '' ) . ' href="' . esc_url( $s_url ) . '" rel="nofollow" aria-label="' . esc_attr( $s_lbl ) . '">' . blockke_dev_icon( 'search' ) . '<span>Search<span class="bkd-srch-x"> ' . ( $homes ? 'homes' : 'property' ) . '</span></span></a>' : '' )
			. '<button class="bkd-share" type="button" data-bkd-share aria-label="Share this page">' . blockke_dev_icon( 'share' ) . '<span>Share</span></button></div></div>'
			. '<p class="bkd-status"><b>' . $e( $pill ) . '</b><span>' . $e( implode( ', ', array_unique( array_filter( array( $area, $p['city'] ) ) ) ) ) . '</span></p>'
			. '<h1 class="bkd-h1">' . ( $p['seg'] ? $e( $p['label'] ) . ' in <em>' . $e( $area ) . '</em>' : 'Property in <em>' . $e( $area ) . '</em>' ) . '</h1>'
			. ( '' !== $p['tag'] ? '<p class="bkd-hero-tag">' . $e( $p['tag'] ) . '</p>' : '' )
			. ( $jumps ? '<nav class="bka-jumps" aria-label="Listings in ' . esc_attr( $area ) . '">' . $jumps . '</nav>' : '' )
			. ( $p['seg'] ? blockke_area_type_links( $p, '' !== $p['seg'][4] ? $p['seg'][4] : $p['slug'], '' !== $p['seg'][4] ? $p['slug'] : '' ) : '' )
			. '</div><div class="bkd-hero-body">' . ( $hf ? '<dl class="bkd-hfacts">' . $hf . '</dl>' : '' )
			. '<div class="bkd-quick" id="bkd-quick"><div class="bkd-quick-t">' . $e( 'Looking for ' . $p['lname'] . '?' ) . '</div>'
			. '<div class="bkd-quick-s">Tell us what you need and an advisor will send matches to your WhatsApp. Just two details, no obligation.</div>'
			. blockke_area_quick_form( 'hero', 'Send me matches', $p ) . '</div></div></div></section>';

		// Section bar
		$nav = array();
		if ( $p['seg'] ) {
			$nav['bka-list'] = 'Listings';
		} else {
			foreach ( array( 'homes-for-sale' => 'For sale', 'homes-for-rent' => 'For rent', 'commercial-for-sale' => 'Commercial', 'commercial-for-rent' => 'Commercial for rent', 'land-for-sale' => 'Land', 'land-for-rent' => 'Land for lease' ) as $s => $l ) {
				if ( $data['segs'][ $s ]['n'] ) {
					$nav[ 'bka-' . $s ] = $l;
				}
			}
		}
		$near  = blockke_area_nearby( $p );
		$about = $p['seg'] ? '' : trim( (string) $term->description ); // the area's own text stays on the area page
		if ( $near ) {
			$nav['bka-nearby'] = 'Nearby';
		}
		if ( '' !== $about ) {
			$nav['bka-about'] = 'About';
		}
		$nav['bkd-enquire'] = 'Enquire';
		$o[]                = '<nav class="bkd-subnav" id="bkd-subnav" aria-label="On this page"><div class="bkd-wrap bkd-subnav-in"><b class="bkd-subnav-name">' . $e( $p['seg'] ? $p['label'] : $area ) . '</b><div class="bkd-subnav-links">';
		foreach ( $nav as $k => $l ) {
			$o[] = '<a href="#' . esc_attr( $k ) . '">' . $e( $l ) . '</a>';
		}
		$o[] = '</div><div class="bkd-subnav-r">' . ( ! empty( $cfg['search'] ) ? '<a class="bkd-subnav-srch"' . ( $sheet ? ' data-bkd-search="subnav"' : '' ) . ' href="' . esc_url( $s_url ) . '" rel="nofollow" aria-label="' . esc_attr( $s_lbl ) . '" title="' . esc_attr( $s_lbl ) . '">' . blockke_dev_icon( 'search' ) . '</a>' : '' )
			. '<a class="bkd-subnav-wa" href="' . esc_url( $wa ) . '" target="_blank" rel="noopener" data-bkd-wa>' . blockke_dev_icon( 'wa', 'bkd-wa-ic' ) . '<span>WhatsApp</span></a>'
			. '<button class="bkd-btn bkd-btn-navy bkd-btn-sm" type="button" data-bkd-enquire="subnav" data-unit="' . esc_attr( $p['name'] ) . '">Ask an advisor</button></div></div></nav>';

		if ( $p['seg'] ) {
			// The segment: every listing of this one kind, with bedroom filters and sorting
			$sd   = $data['segs'][ $p['slug'] ];
			$rent = 'rent' === $p['action'];
			$o[]  = '<section class="bkd-sec bka-sec" id="bka-list"><div class="bkd-wrap">';
			if ( $sd['n'] ) {
				$beds = array();
				foreach ( $sd['cards'] as $c ) {
					foreach ( $c['beds'] as $b ) {
						$beds[ $b ] = 1;
					}
				}
				$o[] = '<h2 class="bkd-sr">Listings</h2><div class="bka-bar" data-bka-bar><p class="bka-count" data-bka-count aria-live="polite">' . $e( 1 === $sd['n'] ? '1 listing' : $sd['n'] . ' listings' ) . '</p><div class="bka-ctrl">';
				if ( $homes && count( $beds ) > 1 ) {
					$o[] = '<fieldset class="bka-beds"><legend class="bkd-sr">Bedrooms</legend><div class="bkd-chips"><label class="bkd-chip"><input type="radio" name="bka-beds" value="" checked><span>Any</span></label>';
					foreach ( array( 'studio' => 'Studio', '1' => '1 bed', '2' => '2 bed', '3' => '3 bed', '4' => '4+ bed' ) as $k => $l ) {
						if ( isset( $beds[ $k ] ) ) {
							$o[] = '<label class="bkd-chip"><input type="radio" name="bka-beds" value="' . esc_attr( $k ) . '"><span>' . $e( $l ) . '</span></label>';
						}
					}
					$o[] = '</div></fieldset>';
				}
				if ( $sd['n'] > 1 ) {
					$o[] = '<label class="bka-sort"><span class="bkd-sr">Sort</span><select data-bka-sort><option value="">Recommended</option><option value="price-asc">Price: low to high</option><option value="price-desc">Price: high to low</option><option value="new">Newest</option></select></label>';
				}
				$o[] = '</div></div><div class="bkd-cards bka-grid" data-bka-grid data-page="' . max( 6, (int) $cfg['area_page_size'] ) . '">';
				foreach ( $sd['cards'] as $i => $c ) {
					$o[] = blockke_area_card_html( $c, $i, $rent );
				}
				$o[] = '</div><p class="bka-none" data-bka-none hidden>No homes with that number of bedrooms right now. <button class="bkd-inline" type="button" data-bka-any>Show all</button>, or <button class="bkd-inline" type="button" data-bkd-enquire="find" data-unit="' . esc_attr( $p['name'] ) . '">ask us to find one</button>.</p>'
					. '<div class="bka-more-wrap"><button class="bkd-btn bkd-btn-line" type="button" data-bka-more hidden>Show more</button></div>';
			} else {
				$o[] = '<div class="bkd-sim-empty"><h2 class="bkd-h2">No ' . $e( strtolower( $p['label'] ) ) . ' in <em>' . $e( $area ) . '</em> listed right now</h2><p class="bkd-body bkd-mt">Tell us what you\'re looking for and an advisor will send you matches as they come up.</p>'
					. '<div class="bkd-sim-act"><button class="bkd-btn bkd-btn-navy bkd-btn-sm" type="button" data-bkd-enquire="find" data-unit="' . esc_attr( $p['name'] ) . '">Ask an advisor to find one</button></div></div>';
			}
			// The other kinds of listing here, each on its own page, and the area itself
			$other = '';
			foreach ( $segs as $s => $seg ) {
				if ( '' === $seg[4] && $s !== $p['slug'] && $s !== $p['seg'][4] && $data['segs'][ $s ]['n'] ) {
					$other .= '<a href="' . esc_url( blockke_area_url( $term, $s ) ) . '">' . $e( $seg[0] ) . '<small>' . (int) $data['segs'][ $s ]['n'] . '</small></a>';
				}
			}
			$o[] = '<div class="bka-also"><p class="bkd-label">Also in ' . $e( $area ) . '</p><nav class="bka-types" aria-label="' . esc_attr( 'More listings in ' . $area ) . '">' . $other . '<a href="' . esc_url( blockke_area_url( $term ) ) . '">' . $e( '' !== trim( (string) $term->description ) ? 'About ' . $area : 'All property in ' . $area ) . '</a></nav></div>';
			$o[] = '</div></section>';
		} else {
			// The area page: one section per kind of listing, never mixed
			$mist = false;
			foreach ( $segs as $s => $seg ) {
				$sd = $data['segs'][ $s ];
				if ( '' !== $seg[4] || ! $sd['n'] ) {
					continue;
				}
				$rent  = 'rent' === $seg[1];
				$cards = array_slice( $sd['cards'], 0, max( 3, (int) $cfg['area_cards'] ) );
				$more  = $sd['n'] > count( $cards );
				$all   = $more ? 'See all ' . strtolower( $seg[0] ) . ' in ' . $area . ' (' . $sd['n'] . ')' : 'Filter and sort these listings';
				$o[]   = '<section class="bkd-sec' . ( $mist ? ' bkd-sec-mist' : '' ) . ' bka-sec" id="bka-' . esc_attr( $s ) . '"><div class="bkd-wrap">'
					. '<div class="bkd-sh bkd-sh-row bkd-rv"><div><p class="bkd-label">' . $e( $rent ? ( 'land' === $seg[2] ? 'For lease' : 'For rent' ) : 'For sale' ) . '</p><h2 class="bkd-h2">' . $e( $seg[0] ) . ' in <em>' . $e( $area ) . '</em></h2>'
					. '<p class="bkd-body bka-sub">' . $e( 1 === $sd['n'] ? '1 listing' : $sd['n'] . ' listings' ) . ( $sd['min'] > 0 ? $e( ', from ' . blockke_dev_short( $sd['min'] ) . ( $rent ? ' a month' : '' ) ) : '' ) . '</p></div>'
					. '<a class="bkd-link bka-all-top" href="' . esc_url( blockke_area_url( $term, $s ) ) . '">' . $e( $all ) . blockke_dev_icon( 'arrow' ) . '</a></div>'
					. blockke_area_type_links( $p, $s, '', 'div' )
					. '<div class="bkd-cards" style="--n:3">';
				foreach ( $cards as $i => $c ) {
					$o[] = blockke_area_card_html( $c, $i, $rent );
				}
				$o[]  = ( $more ? '<a class="bkd-card-all" href="' . esc_url( blockke_area_url( $term, $s ) ) . '"><span>' . $e( $all ) . '</span>' . blockke_dev_icon( 'arrow' ) . '</a>' : '' ) . '</div>'
					. '<a class="bkd-link bka-all-bottom" href="' . esc_url( blockke_area_url( $term, $s ) ) . '">' . $e( $all ) . blockke_dev_icon( 'arrow' ) . '</a></div></section>';
				$mist = ! $mist;
			}
			if ( ! $open ) {
				$o[] = '<section class="bkd-sec bka-sec"><div class="bkd-wrap"><div class="bkd-sim-empty"><h2 class="bkd-h2">Nothing listed in <em>' . $e( $area ) . '</em> right now</h2><p class="bkd-body bkd-mt">Tell us what you\'re looking for and an advisor will send you matches as they come up.</p>'
					. '<div class="bkd-sim-act"><button class="bkd-btn bkd-btn-navy bkd-btn-sm" type="button" data-bkd-enquire="find" data-unit="' . esc_attr( $p['name'] ) . '">Ask an advisor to find one</button></div></div></div></section>';
			}
		}

		// Nearby areas with the same kind of listing
		if ( $near ) {
			$what = $p['seg'] ? strtolower( $p['label'] ) : 'homes for sale';
			$o[]  = '<section class="bkd-sec bkd-sec-mist bka-sec" id="bka-nearby"><div class="bkd-wrap"><div class="bkd-sh bkd-rv"><p class="bkd-label">Nearby</p><h2 class="bkd-h2">' . $e( ucfirst( $what ) ) . ' <em>nearby</em></h2></div><ul class="bka-near bkd-rv">';
			foreach ( $near as $n ) {
				$o[] = '<li><a href="' . esc_url( $n[2] ) . '"><b>' . $e( blockke_dev_text( $n[0]->name ) ) . '</b><span>' . $e( 1 === $n[1] ? '1 listing' : $n[1] . ' listings' ) . '</span>' . blockke_dev_icon( 'arrow' ) . '</a></li>';
			}
			$o[] = '</ul></div></section>';
		}

		// About the area: the Neighbourhood's own description, on the area page only
		if ( '' !== $about ) {
			$html  = wpautop( wp_kses_post( $about ) );
			$paras = preg_split( '#(?<=</p>)\s*#', trim( $html ), 2 );
			$o[]   = '<section class="bkd-sec bka-sec" id="bka-about"><div class="bkd-wrap bkd-split"><div class="bkd-sticky bkd-rv"><p class="bkd-label">The area</p><h2 class="bkd-h2">About <em>' . $e( $area ) . '</em></h2></div><div class="bka-about bkd-rv">'
				. $paras[0] . ( isset( $paras[1] ) && '' !== trim( $paras[1] ) ? '<details><summary>Read more about ' . $e( $area ) . '</summary>' . $paras[1] . '</details>' : '' ) . '</div></div></section>';
		}

		// Why Block
		$why = ! empty( $cfg['area_why'] ) ? $cfg['area_why'] : $cfg['why'];
		if ( $why ) {
			$o[] = '<section class="bkd-sec bkd-sec-navy" id="bkd-why"><div class="bkd-wrap"><div class="bkd-sh bkd-rv"><p class="bkd-label">Why ' . $e( $cfg['brand'] ) . '</p><h2 class="bkd-h2">Advice first. <em>Then a decision.</em></h2></div><div class="bkd-why">';
			foreach ( $why as $i => $w ) {
				$o[] = '<div class="bkd-rv"><b>0' . ( $i + 1 ) . '</b><h3 class="bkd-h3">' . $e( $w[0] ) . '</h3><p>' . $e( $w[1] ) . '</p></div>';
			}
			$o[] = '</div></div></section>';
		}

		// Enquire
		$o[] = '<section class="bkd-sec bkd-sec-mist" id="bkd-enquire"><div class="bkd-wrap bkd-split"><div class="bkd-sticky bkd-rv"><p class="bkd-label">Enquire</p><h2 class="bkd-h2">Tell us what you\'re looking for in <em>' . $e( $area ) . '</em></h2>'
			. '<p class="bkd-body bkd-mt">An advisor will send you matching listings with prices and terms. Prefer to talk now? Call or WhatsApp us.</p>'
			. '<div class="bkd-direct"><a href="tel:' . esc_attr( $ag['phone'] ) . '">' . blockke_dev_icon( 'phone' ) . $e( $ag['phone_display'] ) . '</a>'
			. '<a href="' . esc_url( $wa ) . '" target="_blank" rel="noopener" data-bkd-wa>' . blockke_dev_icon( 'wa', 'bkd-wa-ic' ) . 'WhatsApp</a>'
			. '<a href="mailto:' . esc_attr( $cfg['email'] ) . '?subject=' . rawurlencode( $p['name'] . ' enquiry' ) . '">' . blockke_dev_icon( 'mail' ) . $e( $cfg['email'] ) . '</a></div></div>'
			. '<div class="bkd-formcard bkd-rv">' . blockke_area_full_form( $p ) . '</div></div>'
			. '<div class="bkd-wrap"><p class="bkd-disc">' . $e( $cfg['area_disclaimer'] ) . '</p></div></section>';

		// Mobile bar, enquiry dialog, search
		$o[] = '<nav class="bkd-mb" id="bkd-mb" aria-label="Quick contact"><a class="bkd-mb-wa" href="' . esc_url( $wa ) . '" target="_blank" rel="noopener" data-bkd-wa aria-label="WhatsApp">' . blockke_dev_icon( 'wa', 'bkd-wa-ic' ) . '</a>'
			. '<button class="bkd-btn bkd-btn-navy" type="button" data-bkd-enquire="mobile-bar" data-unit="' . esc_attr( $p['name'] ) . '">Ask an advisor</button></nav>';
		$o[] = '<div class="bkd-md" id="bkd-md" hidden role="dialog" aria-modal="true" aria-labelledby="bkd-md-t" aria-describedby="bkd-md-sub"><div class="bkd-md-box">'
			. '<button class="bkd-md-x" type="button" data-bkd-close aria-label="Close">' . blockke_dev_icon( 'close' ) . '</button>'
			. '<p class="bkd-label">' . $e( $p['name'] ) . '</p><h2 class="bkd-h2" id="bkd-md-t">' . $e( 'Get matching ' . $p['things'] ) . '</h2><p class="bkd-md-sub" id="bkd-md-sub"></p>'
			. '<div id="bkd-md-form">' . blockke_area_quick_form( 'modal', 'Send me matches', $p ) . '</div></div></div>';
		if ( $sheet ) {
			$o[] = blockke_dev_search_sheet( $stub );
		}
		$o[] = '</div>';
		return implode( '', $o );
	}

	/** Area page styles, on top of the development layout's. */
	function blockke_area_css() {
		return <<<'CSS'
#bkd.bka .bka-hero{padding-bottom:clamp(40px,5vw,72px)}
@media (max-width:999px){#bkd .bka-hero .bkd-hero-body{display:none}#bkd.bka .bka-hero{padding-bottom:8px}#bkd.bka .bkd-subnav+.bka-sec{padding-top:32px}} /* phones: listings first; the bar and Enquire carry the form */
#bkd .bka-hero-grid{display:grid;gap:30px;grid-template-areas:"head" "body"}
@media (min-width:1000px){#bkd .bka-hero-grid{grid-template-columns:minmax(0,7fr) minmax(0,5fr);grid-template-areas:"head body";column-gap:80px;align-items:start}}
#bkd .bka-hero .bkd-h1 em,#bkd .bka-sec .bkd-h2 em{font-style:italic}
#bkd .bka-jumps,#bkd .bka-types{display:flex;flex-wrap:wrap;gap:8px;margin-top:26px}
#bkd .bka-sec .bka-types{margin:-24px 0 28px}
#bkd .bka-jumps a,#bkd .bka-types a,#bkd .bka-types span{display:inline-flex;align-items:center;gap:8px;min-height:40px;padding:0 14px;border:1px solid var(--line-2);background:#fff;font-size:13px;font-weight:500;color:var(--navy)!important;text-decoration:none}
#bkd .bka-jumps a:hover,#bkd .bka-types a:hover{border-color:var(--navy)}
#bkd .bka-types span[aria-current]{background:var(--navy);border-color:var(--navy);color:#fff!important}
#bkd .bka-jumps small,#bkd .bka-types small{font-size:12px;font-weight:600;color:var(--brass-d)}
#bkd .bka-types span[aria-current] small{color:var(--brass-l)}
#bkd .bka-sub{margin-top:10px;color:var(--slate)}
#bkd .bka-bar{display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:14px 24px;margin-bottom:30px}
#bkd .bka-count{margin:0;font-size:14px;font-weight:600;color:var(--slate)}
#bkd .bka-ctrl{display:flex;flex-wrap:wrap;align-items:center;gap:12px 18px}
#bkd .bka-beds{margin:0;padding:0;border:0;min-width:0}
#bkd .bka-beds .bkd-chip span{min-height:42px;padding:0 14px}
#bkd .bka-sort select{height:40px;margin:0;padding:0 34px 0 12px;border:1px solid var(--line-2)!important;border-radius:0!important;background-color:#fff!important;font:500 13px/1 var(--font)!important;color:var(--navy)!important}
#bkd .bkd-sr{position:absolute!important;width:1px;height:1px;padding:0;margin:-1px;overflow:hidden;clip:rect(0,0,0,0);white-space:nowrap;border:0}
#bkd .bka-grid{grid-auto-flow:row;grid-auto-columns:auto;grid-template-columns:minmax(0,1fr);overflow:visible;gap:36px 24px;scroll-snap-type:none}
@media (min-width:640px){#bkd .bka-grid{grid-template-columns:repeat(2,minmax(0,1fr))}}
@media (min-width:1000px){#bkd .bka-grid{grid-template-columns:repeat(3,minmax(0,1fr))}}
#bkd .bka-grid>.bkd-card[hidden]{display:none}
#bkd .bka-none{margin:8px 0 0;font-size:15px;color:var(--slate)}
#bkd .bka-more-wrap{display:flex;justify-content:center;margin-top:36px}
#bkd .bka-also{margin-top:clamp(48px,6vw,80px)}
#bkd .bka-also .bka-types{margin-top:0}
#bkd .bka-near{display:grid;gap:0;margin:0;padding:0;list-style:none;border-top:1px solid var(--line-2)}
@media (min-width:768px){#bkd .bka-near{grid-template-columns:repeat(2,minmax(0,1fr));column-gap:40px}}
#bkd .bka-near li{margin:0;border-bottom:1px solid var(--line-2)}
#bkd .bka-near a{display:flex;align-items:center;gap:14px;padding:18px 0;color:var(--navy)!important;text-decoration:none}
#bkd .bka-near b{font-size:17px;font-weight:600}
#bkd .bka-near span{margin-left:auto;font-size:14px;color:var(--slate)}
#bkd .bka-near .bkd-i{width:18px;height:18px;color:var(--brass-d)}
#bkd .bka-about{font-size:16px;line-height:1.75;color:var(--ink)}
#bkd .bka-about p{margin:0 0 16px}
#bkd .bka-about summary{cursor:pointer;font-weight:600;color:var(--navy)}
#bkd .bka-about details[open] summary{margin-bottom:16px}
@media (max-width:999px){#bkd .bka-sec .bka-all-top{display:none}}
#bkd .bka-all-bottom{display:none;margin-top:22px}
@media (max-width:999px){#bkd .bka-all-bottom{display:inline-flex}#bkd.bka[data-seg=""] .bka-hero .bkd-status{display:none}}
@media (min-width:1000px){#bkd.bka .bkd-subnav+.bka-sec{padding-top:56px}}
#bkd .bka-hero .bkd-hfacts>div{display:flex;flex-direction:column;justify-content:space-between}
@media (max-width:639px){#bkd .bka-bar{display:grid;grid-template-columns:1fr auto;align-items:center;gap:12px 16px}#bkd .bka-ctrl{display:contents}#bkd .bka-sort{grid-column:2;grid-row:1}#bkd .bka-beds{grid-column:1/-1}#bkd .bka-beds .bkd-chips{flex-wrap:nowrap;overflow-x:auto;scrollbar-width:none}#bkd .bka-beds .bkd-chip{flex:none}}
CSS;
	}

	/** Area page behaviour: bedroom filter, sort, "Show more", and what visitors use. */
	function blockke_area_js() {
		return <<<'JS'
(function(){
  'use strict';
  var root=document.getElementById('bkd');if(!root||!root.classList.contains('bka')){return;}
  var dl=(window.dataLayer=window.dataLayer||[]),C=window.BKDEV||{};
  function track(ev,o){var p={area:C.area||'',segment:C.seg||''};for(var k in o){p[k]=o[k];}
    try{if(typeof window.gtag==='function'){window.gtag('event',ev,p);}else{p.event=ev;dl.push(p);}}catch(e){}}
  var grid=root.querySelector('[data-bka-grid]');
  if(grid){
    var cards=Array.prototype.slice.call(grid.querySelectorAll('.bkd-card')),order=cards.slice(),page=+grid.getAttribute('data-page')||24,shown=page;
    var more=root.querySelector('[data-bka-more]'),none=root.querySelector('[data-bka-none]'),count=root.querySelector('[data-bka-count]'),sort=root.querySelector('[data-bka-sort]');
    var bed='';
    function apply(){
      var list=order.filter(function(c){return !bed||(' '+c.getAttribute('data-beds')+' ').indexOf(' '+bed+' ')>-1;});
      cards.forEach(function(c){c.hidden=true;});
      list.forEach(function(c,i){c.hidden=i>=shown;grid.appendChild(c);if(i<shown){c.classList.add('in');}});
      if(more){more.hidden=list.length<=shown;}
      if(none){none.hidden=list.length>0;}
      if(count){count.textContent=list.length===1?'1 listing':list.length+' listings';}
    }
    root.addEventListener('change',function(e){
      var t=e.target;
      if(t.name==='bka-beds'){bed=t.value;shown=page;apply();track('area_filter',{beds:bed||'any'});}
      if(t===sort){var v=sort.value,num=function(c,k){return +c.getAttribute(k)||0;};
        order=cards.slice();
        if(v==='price-asc'){order.sort(function(a,b){var x=num(a,'data-price'),y=num(b,'data-price');return (x||Infinity)-(y||Infinity);});}
        if(v==='price-desc'){order.sort(function(a,b){return num(b,'data-price')-num(a,'data-price');});}
        if(v==='new'){order.sort(function(a,b){return num(b,'data-date')-num(a,'data-date');});}
        shown=page;apply();track('area_sort',{sort:v||'recommended'});}
    });
    if(more){more.addEventListener('click',function(){shown+=page;apply();track('area_more',{shown:shown});});}
    var any=root.querySelector('[data-bka-any]');
    if(any){any.addEventListener('click',function(){var r=root.querySelector('input[name="bka-beds"][value=""]');if(r){r.checked=true;}bed='';shown=page;apply();});}
    apply();
  }
  root.addEventListener('click',function(e){
    var a=e.target.closest&&e.target.closest('a.bkd-card[data-id]');
    if(a){var sec=a.closest('section');track('area_card_click',{target_id:a.getAttribute('data-id'),position:+a.getAttribute('data-pos'),section:sec?sec.id:'',transport_type:'beacon'});}
  });
})();
JS;
	}

	add_filter(
		'query_vars',
		function ( $v ) {
			$v[] = 'bka_seg';
			return $v;
		}
	);

	add_action(
		'init',
		function () {
			$tx = get_taxonomy( 'property_area' );
			if ( ! $tx ) {
				return;
			}
			$slug = is_array( $tx->rewrite ) && ! empty( $tx->rewrite['slug'] ) ? trim( $tx->rewrite['slug'], '/' ) : 'property_area';
			$re   = implode( '|', array_map( 'preg_quote', array_keys( blockke_area_segments() ) ) );
			$GLOBALS['blockke_area_rule'] = '^' . $slug . '/([^/]+)/(' . $re . ')/?$';
			add_rewrite_rule( $GLOBALS['blockke_area_rule'], 'index.php?property_area=$matches[1]&bka_seg=$matches[2]', 'top' );
		},
		99
	);
	add_action(
		'wp_loaded',
		function () {
			// Refresh the stored rules when ours is missing: first run, or after anything rebuilt them while this snippet was off
			$rules = get_option( 'rewrite_rules' );
			if ( ! empty( $GLOBALS['blockke_area_rule'] ) && is_array( $rules ) && ! isset( $rules[ $GLOBALS['blockke_area_rule'] ] ) ) {
				flush_rewrite_rules( false );
			}
		}
	);

	add_filter(
		'template_include',
		function ( $template ) {
			if ( is_admin() || is_feed() || is_embed() || ! is_tax( 'property_area' ) ) {
				return $template;
			}
			$term = get_queried_object();
			$slug = sanitize_key( (string) get_query_var( 'bka_seg' ) );
			if ( ! $term instanceof WP_Term ) {
				return $template;
			}
			if ( ! blockke_area_on( $term ) ) {
				if ( '' !== $slug ) { // segment pages only exist on the new layout
					wp_safe_redirect( get_term_link( $term ), 302 );
					exit;
				}
				return $template;
			}
			$segs = blockke_area_segments();
			if ( ( '' !== $slug && ! isset( $segs[ $slug ] ) ) || ( '' === $slug && is_paged() ) ) {
				wp_safe_redirect( get_term_link( $term ), 301 );
				exit;
			}
			$GLOBALS['blockke_area_preview'] = isset( $_GET['bke_area'] ) && current_user_can( 'edit_posts' ); // phpcs:ignore WordPress.Security.NonceVerification
			// an editor's preview of the v1.6 cards (?bke_cards=1, or 0 for the v1.5 ones), kept on links between area pages
			$GLOBALS['blockke_cards_preview'] = isset( $_GET['bke_cards'] ) && is_scalar( $_GET['bke_cards'] ) && current_user_can( 'edit_posts' ) ? ( '1' === (string) $_GET['bke_cards'] ? '1' : '0' ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
			$p   = blockke_area_page( $term, $slug );
			$v16 = ! empty( $p['data']['v16'] );
			if ( $v16 && $p['seg'] && array_intersect( array_keys( $_GET ), array( 'beds', 'price', 'completion', 'sort', 'n' ) ) ) { // phpcs:ignore WordPress.Security.NonceVerification
				$p['index'] = false; // a filtered or sorted view: the plain segment page is the one in search results
			}
			$GLOBALS['blockke_dev_active'] = true;
			add_filter( 'rank_math/frontend/disable_adjacent_rel_links', '__return_true' ); // the archive's page 2, 3... are not these pages
			if ( empty( blockke_dev_config()['price_check_popup'] ) ) {
				$_GET['blockpop'] = '0';
			}
			status_header( 200 );
			add_filter(
				'body_class',
				function ( $c ) {
					$c[] = 'bkd-page';
					$c[] = 'bka-page';
					return $c;
				}
			);
			if ( $p['seg'] ) {
				// A segment page is its own page to search engines: title, description, canonical, and noindex when thin
				$robots = function ( $r ) use ( $p ) {
					if ( ! $p['index'] ) {
						unset( $r['index'] );
						$r['noindex'] = true;
						$r['follow']  = true;
					}
					return $r;
				};
				add_filter( 'wp_robots', $robots, 99 );
				add_filter(
					'pre_get_document_title',
					function () use ( $p ) {
						return esc_html( $p['title'] );
					},
					99
				);
				foreach ( array( 'rank_math/frontend/title', 'rank_math/opengraph/facebook/og_title', 'rank_math/opengraph/twitter/twitter_title' ) as $h ) {
					add_filter(
						$h,
						function () use ( $p ) {
							return $p['title'];
						},
						99
					);
				}
				foreach ( array( 'rank_math/frontend/description', 'rank_math/opengraph/facebook/og_description', 'rank_math/opengraph/twitter/twitter_description' ) as $h ) {
					add_filter(
						$h,
						function () use ( $p ) {
							return $p['desc'];
						},
						99
					);
				}
				foreach ( array( 'rank_math/frontend/canonical', 'rank_math/opengraph/facebook/og_url' ) as $h ) {
					add_filter(
						$h,
						function () use ( $p ) {
							return $p['canon'];
						},
						99
					);
				}
				add_filter(
					'rank_math/frontend/robots',
					function ( $r ) use ( $p ) {
						if ( ! $p['index'] ) {
							$r['index']  = 'noindex';
							$r['follow'] = 'follow';
						}
						return $r;
					},
					99
				);
				if ( ! defined( 'RANK_MATH_VERSION' ) ) { // without Rank Math, the page says these itself
					add_action(
						'wp_head',
						function () use ( $p ) {
							echo '<meta name="description" content="' . esc_attr( $p['desc'] ) . '">' . "\n" . '<link rel="canonical" href="' . esc_url( $p['canon'] ) . '">' . "\n";
						},
						1
					);
				}
			}
			add_action(
				'wp_head',
				function () use ( $v16 ) {
					$font = blockke_dev_font_head( '#bkd' );
					if ( '' === $font[0] ) {
						$font[1] = '#bkd{--display:var(--font);--display-w:300;--display-ls:-.025em}#bkd .bkd-h1{font-size:clamp(38px,4.8vw,64px)!important}#bkd .bkd-h2{font-size:clamp(28px,3.3vw,44px)!important}';
					}
					$css = $v16 ? blockke_bkn_card_css() . blockke_index_css() . blockke_area_css_v16() : blockke_area_css();
					echo $font[0] . '<style id="bkd-css">' . blockke_dev_css() . $font[1] . $css . "</style>\n"; // phpcs:ignore WordPress.Security.EscapeOutput
				},
				99
			);
			add_action(
				'wp_footer',
				function () use ( $p, $v16 ) {
					$cfg = blockke_dev_config();
					$ag  = blockke_dev_agent( 0 );
					$js  = array(
						'id'        => 0,
						'slug'      => '',
						'areaSlug'  => $p['term']->slug,
						'name'      => $p['name'],
						'leadName'  => $p['lname'],
						'title'     => $p['name'],
						'url'       => $p['url'],
						'brand'     => $cfg['brand'],
						'agent'     => '',
						'wa'        => $ag['wa'],
						'endpoint'  => esc_url_raw( rest_url( 'block/v1/development-lead' ) ),
						'gallery'   => array(),
						'lp'        => 0,
						'area'      => $p['area'],
						'bands'     => $cfg['budget_bands'],
						'back'      => 0,
						'bedsMode'  => $cfg['beds_mode'],
						'studioCat' => (string) $cfg['studio_cat'],
						'areaPage'  => 1,
						'areaId'    => (int) $p['term']->term_id,
						'seg'       => $p['slug'],
						'mdTitle'   => 'Get matching ' . $p['things'],
						'mdSub'     => 'Tell us what you need and an advisor will send matching ' . $p['lname'] . ' to your WhatsApp.',
						'okTail'    => 'with matching ' . $p['lname'],
					);
					if ( $v16 ) { // the development page's "Back to Westlands" / "Back to homes for sale in Westlands"
						$js['listLabel'] = $p['seg'] ? $p['lname'] : $p['area'];
					}
					echo '<script id="bkd-cfg" data-cfasync="false" data-no-optimize="1" data-no-defer="1">window.BKDEV=' . wp_json_encode( $js, JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS ) . ";</script>\n";
					$own = $v16 ? blockke_area_js_v16() . "\n" . blockke_bkn_nav_js() : blockke_area_js();
					echo '<script id="bkd-js" data-cfasync="false" data-no-optimize="1" data-no-defer="1">' . blockke_dev_js() . "\n" . $own . "</script>\n"; // phpcs:ignore WordPress.Security.EscapeOutput
				},
				20
			);
			get_header();
			echo $v16 ? blockke_area_render_v16( $p ) : blockke_area_render( $p ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped while building.
			get_footer();
			$blank = WP_CONTENT_DIR . '/index.php';
			return file_exists( $blank ) ? $blank : '';
		},
		99998
	);

	add_action(
		'admin_bar_menu',
		function ( $bar ) {
			if ( is_admin() || ! is_tax( 'property_area' ) || ! current_user_can( 'edit_posts' ) ) {
				return;
			}
			$term = get_queried_object();
			if ( ! $term instanceof WP_Term ) {
				return;
			}
			$cfg  = blockke_dev_config();
			$live = true === $cfg['area_pages'] || ( is_array( $cfg['area_pages'] ) && in_array( $term->slug, $cfg['area_pages'], true ) );
			$url  = get_term_link( $term );
			$bar->add_node( array( 'id' => 'bka', 'title' => 'Area page: ' . ( $live ? 'on' : 'off' ), 'href' => add_query_arg( 'bke_area', $live ? '0' : '1', $url ) ) );
			$bar->add_node( array( 'parent' => 'bka', 'id' => 'bka-on', 'title' => 'Preview the new area page', 'href' => add_query_arg( 'bke_area', '1', $url ) ) );
			$bar->add_node( array( 'parent' => 'bka', 'id' => 'bka-off', 'title' => 'Preview the classic page', 'href' => add_query_arg( 'bke_area', '0', $url ) ) );
			// the v1.6 cards (card_v16): its own switch, previewed on the new area page
			$new = $live ? $url : add_query_arg( 'bke_area', '1', $url );
			$bar->add_node( array( 'parent' => 'bka', 'id' => 'bka-v16', 'title' => 'v1.6 cards: ' . ( ! empty( $cfg['card_v16'] ) ? 'on' : 'off' ) ) );
			$bar->add_node( array( 'parent' => 'bka-v16', 'id' => 'bka-v16-on', 'title' => 'Preview with the v1.6 cards', 'href' => add_query_arg( 'bke_cards', '1', $new ) ) );
			$bar->add_node( array( 'parent' => 'bka-v16', 'id' => 'bka-v16-off', 'title' => 'Preview with the v1.5 cards', 'href' => add_query_arg( 'bke_cards', '0', $new ) ) );
		},
		90
	);

	/* =====================================================================
	   4) New developments, search results and one card family (v1.6)
	      Every listing read once into rows (cached), grouped into developments; one card markup for the index,
	      search results and area pages. Each feature has its own switch and an editor preview.
	   ===================================================================== */

	/** A v1.6 switch: the config key, or an editor's ?{preview}=1 / 0 (preview '' = no preview; an array there is ignored). */
	function blockke_bkn_on( $key, $preview = '' ) {
		$cfg = blockke_dev_config();
		if ( '' !== $preview && isset( $_GET[ $preview ] ) && is_scalar( $_GET[ $preview ] ) && current_user_can( 'edit_posts' ) ) { // phpcs:ignore WordPress.Security.NonceVerification
			return '1' === (string) $_GET[ $preview ]; // phpcs:ignore WordPress.Security.NonceVerification
		}
		return ! empty( $cfg[ $key ] );
	}

	/**
	 * The cache version of the rows and everything built from them: any listing change, a new row format (v163), or a change to
	 * the settings the rows and developments read. Without $current: the version of the rows this request actually uses, so
	 * developments and the index built from an old copy (served while another request rebuilds) are stored under the old
	 * version and rebuilt by the next request, never kept as if they were new.
	 */
	function blockke_bkn_ver( $current = false ) {
		if ( ! $current ) {
			blockke_bkn_rows();
			return $GLOBALS['blockke_bkn_rows_ver'];
		}
		$cfg = blockke_dev_config();
		return (string) get_option( 'bkd_sim_ver', '0' ) . '|v163|' . substr( md5( wp_json_encode( array( $cfg['enabled'], $cfg['auto'], $cfg['auto_units'], $cfg['neighbours'], $cfg['studio_cat'] ) ) ), 0, 10 );
	}

	/** Lowercase, no punctuation or the generic words, for "is this the same building" checks. */
	function blockke_bkn_norm( $s ) {
		$s = strtolower( blockke_dev_text( $s ) );
		$s = preg_replace( '/\b(?:residences?|apartments?|homes|villas?|the)\b/u', ' ', $s );
		return trim( preg_replace( '/[^a-z0-9]+/u', ' ', $s ) );
	}

	/**
	 * Every published listing as a row: what cards, filters and grouping need. Cached in transient bkn_rows (rebuilt when
	 * any listing changes, option bkd_sim_ver); while one request rebuilds, others serve the copy they have (in the same row
	 * format). An empty build never replaces a good copy. The version of the rows in use is kept for blockke_bkn_ver().
	 */
	function blockke_bkn_rows() {
		static $rows = null;
		if ( null !== $rows ) {
			return $rows;
		}
		$ver = blockke_bkn_ver( true );
		$hit = get_transient( 'bkn_rows' );
		$hit = is_array( $hit ) && isset( $hit['ver'], $hit['rows'] ) && is_array( $hit['rows'] ) ? $hit : null;
		$fmt = function ( $v ) { // the row format part of a version ("v163"): old rows are only any use in the same format
			$p = explode( '|', (string) $v );
			return isset( $p[1] ) ? $p[1] : '';
		};
		if ( $hit && ( $hit['ver'] === $ver || ( $fmt( $hit['ver'] ) === $fmt( $ver ) && get_transient( 'bkn_build_lock' ) ) ) ) {
			$GLOBALS['blockke_bkn_rows_ver'] = $hit['ver'];
			$rows                            = $hit['rows'];
			return $rows;
		}
		set_transient( 'bkn_build_lock', 1, 60 );
		$keep = array( isset( $_GET['completion'] ) ? $_GET['completion'] : null, isset( $_REQUEST['bke_completion'] ) ? $_REQUEST['bke_completion'] : null ); // phpcs:ignore WordPress.Security.NonceVerification
		unset( $_GET['completion'], $_REQUEST['bke_completion'] ); // another snippet narrows property queries by these
		$args = array(
			'post_type'           => 'estate_property',
			'post_status'         => 'publish',
			'fields'              => 'ids',
			'posts_per_page'      => 3000,
			'no_found_rows'       => true,
			'ignore_sticky_posts' => true,
			'orderby'             => 'ID',
			'order'               => 'DESC',
		);
		// Every listing; snippet 41011 skips a query that already names property_completion. Only while that taxonomy exists:
		// without it both clauses match nothing (and with 41011 off nothing narrows the query anyway)
		if ( taxonomy_exists( 'property_completion' ) ) {
			$args['tax_query'] = array( // phpcs:ignore WordPress.DB.SlowDBQuery
				'relation' => 'OR',
				array(
					'taxonomy' => 'property_completion',
					'operator' => 'EXISTS',
				),
				array(
					'taxonomy' => 'property_completion',
					'operator' => 'NOT EXISTS',
				),
			);
		}
		$q = new WP_Query( $args );
		if ( null !== $keep[0] ) {
			$_GET['completion'] = $keep[0];
		}
		if ( null !== $keep[1] ) {
			$_REQUEST['bke_completion'] = $keep[1];
		}
		$ids = array_map( 'intval', $q->posts );
		if ( $ids && function_exists( '_prime_post_caches' ) ) {
			_prime_post_caches( $ids, true, false );
		}
		blockke_dev_pm_prime( $ids );
		$rows = array();
		foreach ( $ids as $id ) {
			$r = blockke_bkn_row( $id );
			if ( $r ) {
				$rows[ $id ] = $r;
			}
		}
		if ( ! $rows && $hit && $hit['rows'] && $fmt( $hit['ver'] ) === $fmt( $ver ) ) {
			// nothing listed at all is far likelier a failed query than the truth: keep the good copy, and leave the lock in
			// place so other requests serve it too for the next minute before one tries again
			error_log( 'Block v1.6: the listing rows came back empty; serving the previous copy.' ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions
			$GLOBALS['blockke_bkn_rows_ver'] = $hit['ver'];
			$rows                            = $hit['rows'];
			return $rows;
		}
		set_transient( 'bkn_rows', array( 'ver' => $ver, 'rows' => $rows ), $rows ? DAY_IN_SECONDS : 5 * MINUTE_IN_SECONDS );
		delete_transient( 'bkn_build_lock' );
		$GLOBALS['blockke_bkn_rows_ver'] = $ver;
		return $rows;
	}

	/** One listing's row, or null when it is sold or let. */
	/** A listing for several sizes ("Studio, 1-Bed & 2-Bed Apartments") has no bedroom key of its own: its sizes from the title. */
	function blockke_bkn_range_beds( $r ) {
		if ( 'home' !== $r['kind'] || $r['dev'] || '' !== $r['k'] ) {
			return array();
		}
		$out = array();
		$tb  = blockke_area_title_beds( $r['id'], true );
		foreach ( $tb[1] as $x ) {
			$out[] = 'studio' === $x ? 0 : (int) $x;
		}
		if ( preg_match( '/(\d+) bed$/', $tb[0], $mm ) ) { // the keys stop at 4 (the "4+" filter): "2, 3, 4 & 5 Bed" keeps its 5
			$out[] = (int) $mm[1];
		}
		return $out;
	}

	function blockke_bkn_row( $id ) {
		if ( has_term( array( 'sold', 'let' ), 'property_status', $id ) ) {
			return null;
		}
		$kt     = blockke_area_kind( $id );
		$act    = blockke_area_action( $id, $kt[0] );
		$cats   = get_the_terms( $id, 'property_category' );
		$areas  = get_the_terms( $id, 'property_area' );
		$master = (int) blockke_dev_pm( $id, 'property_subunits_master' );
		$master = $master && $master !== $id ? $master : 0;
		$proj   = blockke_dev_first_term( $id, 'property_project' );
		$ct     = blockke_dev_first_term( $id, 'property_completion' );
		$cy     = blockke_dev_first_term( $id, 'property_city' );
		// main area: the listing's own choice, else its development's, else the busiest of its areas
		$ma = sanitize_title( (string) blockke_dev_pm( $id, 'bke_dev_area' ) );
		if ( '' === $ma && $master ) {
			$ma = sanitize_title( (string) blockke_dev_pm( $master, 'bke_dev_area' ) );
		}
		$src = $master ? get_the_terms( $master, 'property_area' ) : false;
		$src = is_array( $src ) && $src ? $src : $areas;
		if ( '' === $ma && is_array( $src ) && $src ) {
			usort(
				$src,
				function ( $a, $b ) {
					return (int) $b->count === (int) $a->count ? (int) $a->term_id - (int) $b->term_id : (int) $b->count - (int) $a->count;
				}
			);
			$ma = $src[0]->slug;
		}
		$gal = blockke_dev_pm( $id, 'wpestate_property_gallery' );
		$gal = is_array( $gal ) ? $gal : maybe_unserialize( $gal );
		$gal = is_array( $gal ) ? $gal : explode( ',', (string) blockke_dev_pm( $id, 'image_to_attach' ) );
		$img = (int) blockke_dev_pm( $id, '_thumbnail_id' );
		$lo  = (string) blockke_dev_pm( $id, 'bke_dev_layout' );
		$dev = '1' === (string) blockke_dev_pm( $id, 'property_has_subunits' );
		$cfg = blockke_dev_config();
		return array(
			'id'    => $id,
			't'     => blockke_dev_text( get_the_title( $id ) ),
			'u'     => get_permalink( $id ),
			'dev'   => $dev,
			'm'     => $master,
			'dk'    => blockke_dev_dev_key( $id ),
			'pj'    => $proj ? (int) $proj->term_id : 0,
			'kind'  => $kt[0],
			'type'  => $kt[1],
			'act'   => $act,
			'cats'  => is_array( $cats ) ? array_values( wp_list_pluck( $cats, 'slug' ) ) : array(),
			'areas' => is_array( $areas ) ? array_values( wp_list_pluck( $areas, 'slug' ) ) : array(),
			'ma'    => $ma,
			'cy'    => $cy ? blockke_dev_text( $cy->name ) : '',
			'p'     => (float) blockke_dev_pm( $id, 'property_price' ),
			'f'     => blockke_dev_price_flag( $id ),
			'b'     => (int) blockke_dev_pm( $id, 'property_bedrooms' ),
			'k'     => 'home' === $kt[0] ? blockke_dev_sim_key( $id ) : '',
			'sq'    => (float) blockke_dev_pm( $id, 'property_size' ),
			'st'    => blockke_dev_is_offplan( $id ) ? 'offplan' : ( has_term( 'complete', 'property_status', $id ) || has_term( 'ready-now', 'property_completion', $id ) ? 'ready' : '' ),
			'cs'    => blockke_dev_text( (string) blockke_dev_pm( $id, 'bke_completion_stated' ) ),
			'ct'    => $ct ? $ct->slug : '',
			'img'   => $img,
			'ph'    => count( array_unique( array_filter( array_map( 'intval', array_merge( array( $img ), $gal ) ) ) ) ),
			'ft'    => '1' === (string) blockke_dev_pm( $id, 'prop_featured' ),
			'lo'    => ! empty( $cfg['enabled'] ) && ( 'on' === $lo || ( 'off' !== $lo && ( ( ! empty( $cfg['auto'] ) && $dev ) || ( $master && 'sale' === $act && $cfg['auto_units'] && ( true === $cfg['auto_units'] || ( is_array( $cfg['auto_units'] ) && in_array( $ma, $cfg['auto_units'], true ) ) ) ) ) ) ),
			'ln'    => blockke_dev_text( (string) blockke_dev_pm( $id, 'bke_index_line' ) ),
			'd'     => (int) get_post_time( 'U', true, $id ),
		);
	}

	/**
	 * Completion from a stated date ("Dec 2027", "Q2 2027", "late 2027", "2028") or a Completion term:
	 * array( year, month 0-12, quarter 0-4, word, passed ). Year 0 = unknown.
	 */
	function blockke_bkn_done( $stated, $term ) {
		$y = 0;
		$m = 0;
		$q = 0;
		$w = '';
		$s = strtolower( (string) $stated );
		if ( preg_match( '/\b(20\d\d)\b/', $s, $mm ) ) {
			$y = (int) $mm[1];
			if ( preg_match( '/\b(jan|feb|mar|apr|may|jun|jul|aug|sep|oct|nov|dec)[a-z]*\b/', $s, $mo ) ) {
				$m = 1 + array_search( $mo[1], array( 'jan', 'feb', 'mar', 'apr', 'may', 'jun', 'jul', 'aug', 'sep', 'oct', 'nov', 'dec' ), true );
			} elseif ( preg_match( '/\bq([1-4])\b/', $s, $qq ) ) {
				$q = (int) $qq[1];
			} elseif ( preg_match( '/\b(early|mid|late|end)\b/', $s, $ww ) ) {
				$w = 'end' === $ww[1] ? 'late' : $ww[1];
			}
		} elseif ( preg_match( '/^(20\d\d)/', (string) $term, $mm ) ) {
			$y = (int) $mm[1];
		}
		$now  = (int) current_time( 'Y' ) * 12 + (int) current_time( 'n' );
		$last = $y ? $y * 12 + ( $m ? $m : ( $q ? $q * 3 : 12 ) ) : 0;
		return array( $y, $m, $q, $w, $y && $last < $now );
	}

	/**
	 * Developments: listings grouped as one card each (a main listing and its units; else a Project; else the listing),
	 * merged when two groups are the same building. Cached in transient bkn_devs with the rows.
	 * Returns array( 'devs' => group key => development, 'of' => listing id => group key ).
	 */
	function blockke_bkn_devs() {
		static $out = null;
		if ( null !== $out ) {
			return $out;
		}
		$rows = blockke_bkn_rows();
		$ver  = blockke_bkn_ver() . '|' . count( $rows );
		$hit  = get_transient( 'bkn_devs' );
		if ( is_array( $hit ) && isset( $hit['ver'] ) && $hit['ver'] === $ver ) {
			$out = $hit['data'];
			return $out;
		}
		// 1. groups by development key; units follow their main listing
		$g = array();
		foreach ( $rows as $id => $r ) {
			$k = $r['m'] && isset( $rows[ $r['m'] ] ) ? 'm' . $r['m'] : ( $r['dev'] ? 'm' . $id : ( $r['pj'] ? 'p' . $r['pj'] : 'i' . $id ) );
			$g[ $k ][] = $id;
		}
		// 2. the same building listed twice: two groups sharing a Project, or the same name in the same main area
		$by_pj = array();
		$by_nm = array();
		$alias = array();
		foreach ( $g as $k => $ids ) {
			$lead = $rows[ $ids[0] ];
			foreach ( $ids as $id ) {
				if ( $rows[ $id ]['dev'] ) {
					$lead = $rows[ $id ];
				}
			}
			$keys = array();
			if ( $lead['pj'] ) {
				$keys[] = 'pj' . $lead['pj'];
			}
			$nm = blockke_bkn_norm( blockke_dev_listing_name( $lead['id'] ) );
			if ( strlen( $nm ) > 3 && 'i' !== $k[0] ) { // a single listing joins only by its Project
				$keys[] = 'nm' . $lead['ma'] . '|' . $nm;
			}
			foreach ( $keys as $x ) {
				$to = 0 === strpos( $x, 'pj' ) ? ( isset( $by_pj[ $x ] ) ? $by_pj[ $x ] : null ) : ( isset( $by_nm[ $x ] ) ? $by_nm[ $x ] : null );
				if ( null !== $to && $to !== $k ) {
					$alias[ $k ] = $to;
					break;
				}
			}
			if ( ! isset( $alias[ $k ] ) ) {
				foreach ( $keys as $x ) {
					if ( 0 === strpos( $x, 'pj' ) ) {
						$by_pj[ $x ] = $k;
					} else {
						$by_nm[ $x ] = $k;
					}
				}
			}
		}
		foreach ( $alias as $from => $to ) {
			while ( isset( $alias[ $to ] ) ) {
				$to = $alias[ $to ];
			}
			$g[ $to ] = array_merge( $g[ $to ], $g[ $from ] );
			unset( $g[ $from ] );
		}
		// 3. one development per group
		$devs = array();
		$of   = array();
		foreach ( $g as $k => $ids ) {
			$c = blockke_bkn_dev( $k, $ids, $rows );
			if ( $c ) {
				$devs[ $k ] = $c;
				foreach ( $ids as $id ) {
					$of[ $id ] = $k;
				}
			}
		}
		$out = array(
			'devs' => $devs,
			'of'   => $of,
		);
		set_transient( 'bkn_devs', array( 'ver' => $ver, 'data' => $out ), DAY_IN_SECONDS );
		return $out;
	}

	/**
	 * The order in which a development's listings stand for it (the listing its card opens), for usort on listing ids: listings
	 * for $act first (Buy by default), so a sale card never opens a rental; then one on the new layout; the most photos; the oldest.
	 */
	function blockke_bkn_pick( $rows, $act = 'sale' ) {
		return function ( $a, $b ) use ( $rows, $act ) {
			$x = $rows[ $a ];
			$y = $rows[ $b ];
			if ( ( $act === $x['act'] ) !== ( $act === $y['act'] ) ) {
				return $act === $x['act'] ? -1 : 1;
			}
			if ( $x['lo'] !== $y['lo'] ) {
				return $x['lo'] ? -1 : 1;
			}
			return $y['ph'] !== $x['ph'] ? $y['ph'] - $x['ph'] : $a - $b;
		};
	}

	/**
	 * A card shown for one deal ('sale' or 'rent') opens a listing for that deal: when the development's own listing is for the
	 * other one (homes for rent in a building selling off-plan), the best of $ids for $act instead (main listings first).
	 */
	function blockke_bkn_for_act( $c, $ids, $act ) {
		$rows = blockke_bkn_rows();
		if ( ! isset( $rows[ $c['id'] ] ) || '' === $rows[ $c['id'] ]['act'] || $act === $rows[ $c['id'] ]['act'] ) {
			return $c;
		}
		$cand  = array();
		$mains = array();
		foreach ( $ids as $id ) {
			if ( isset( $rows[ $id ] ) && $act === $rows[ $id ]['act'] ) {
				$cand[] = (int) $id;
				if ( $rows[ $id ]['dev'] ) {
					$mains[] = (int) $id;
				}
			}
		}
		if ( ! $cand ) {
			return $c;
		}
		$cand = $mains ? $mains : $cand;
		usort( $cand, blockke_bkn_pick( $rows, $act ) );
		$r        = $rows[ $cand[0] ];
		$c['id']  = (int) $r['id'];
		$c['url'] = $r['u'];
		$c['vt']  = (bool) $r['lo'];
		return $c;
	}

	/** One development from its listings. */
	function blockke_bkn_dev( $k, $ids, $rows ) {
		$mains = array();
		foreach ( $ids as $id ) {
			if ( $rows[ $id ]['dev'] ) {
				$mains[] = $id;
			}
		}
		$cand = $mains ? $mains : $ids;
		usort( $cand, blockke_bkn_pick( $rows ) );
		$s    = $rows[ $cand[0] ];
		$name = blockke_dev_listing_name( $s['id'] );
		$name = trim( preg_split( '/\s+(?:\||–|—|by)\s+/u', $name )[0], " ,.-\t" );
		// prices, bedrooms, kinds, dates across its listings
		$sale  = array();
		$rent  = array();
		$beds  = array();
		$types = array();
		$acts  = array();
		$kinds = array();
		$areas = array();
		$ft    = false;
		$d     = 0;
		$st    = '';
		foreach ( $ids as $id ) {
			$r             = $rows[ $id ];
			$acts[ $r['act'] ] = 1;
			$kinds[ $r['kind'] ] = 1;
			$ft            = $ft || $r['ft'];
			$d             = max( $d, $r['d'] );
			$areas         = array_merge( $areas, $r['areas'] );
			if ( 'unit' !== $r['f'] && $r['p'] > 0 ) {
				if ( 'sale' === $r['act'] && $r['p'] >= 1000000 ) {
					$sale[] = $r['p'];
				} elseif ( 'rent' === $r['act'] ) {
					$rent[] = $r['p'];
				}
			}
			if ( 'home' === $r['kind'] && ! $r['dev'] && '' !== $r['k'] ) {
				$beds[] = 'studio' === $r['k'] ? 0 : max( 1, $r['b'] );
			}
			$beds = array_merge( $beds, blockke_bkn_range_beds( $r ) ); // a listing for several sizes counts each
			if ( '' !== $r['type'] ) {
				$types[ $r['type'] ] = 1;
			}
			if ( 'offplan' === $r['st'] || ( '' === $st && 'ready' === $r['st'] ) ) {
				$st = $r['st'];
			}
		}
		if ( ! $beds && 'home' === $s['kind'] ) {
			$tb = blockke_area_title_beds( $s['id'], true );
			foreach ( $tb[1] as $x ) {
				$beds[] = 'studio' === $x ? 0 : (int) $x;
			}
			if ( preg_match( '/(\d+) bed$/', $tb[0], $mm ) ) { // "4 & 5 Bedroom Villas" keeps its 5
				$beds[] = (int) $mm[1];
			}
		}
		$beds = array_values( array_unique( $beds ) );
		sort( $beds );
		$cs = $s['cs'];
		foreach ( $ids as $id ) {
			if ( '' === $cs && '' !== $rows[ $id ]['cs'] ) {
				$cs = $rows[ $id ]['cs'];
			}
		}
		$ma    = $s['ma'];
		$at    = $ma ? get_term_by( 'slug', $ma, 'property_area' ) : null;
		$place = $at ? blockke_dev_text( $at->name ) : '';
		$ring  = $ma ? blockke_dev_neighbours( $ma ) : array( array(), array() );
		foreach ( array_unique( $areas ) as $sl ) {
			if ( $sl === $ma ) {
				continue;
			}
			$t = get_term_by( 'slug', $sl, 'property_area' );
			if ( $t && in_array( (int) $t->term_id, $ring[0], true ) ) {
				$place = blockke_dev_text( $t->name ) . ', ' . $place; // "Brookside, Westlands"
				break;
			}
		}
		unset( $kinds[''] );
		return array(
			'g'      => $k,
			'id'     => $s['id'],
			'ids'    => array_values( $ids ),
			'url'    => $s['u'],
			'name'   => '' !== $name ? $name : $s['t'],
			'place'  => $place,
			'area'   => $ma,
			'areas'  => array_values( array_unique( $areas ) ),
			'kind'   => isset( $kinds['home'] ) ? 'home' : ( $kinds ? key( $kinds ) : $s['kind'] ),
			'types'  => array_keys( $types ),
			'acts'   => array_keys( array_filter( $acts ) ),
			'from'   => $sale ? min( $sale ) : 0,
			'to'     => $sale ? max( $sale ) : 0,
			'rfrom'  => $rent ? min( $rent ) : 0,
			'rto'    => $rent ? max( $rent ) : 0,
			'beds'   => $beds,
			'st'     => $st,
			'done'   => blockke_bkn_done( $cs, $s['ct'] ),
			'line'   => mb_substr( $s['ln'], 0, 120 ),
			'photos' => $s['ph'],
			'cover'  => $s['img'] ? $s['img'] : (int) array_reduce( $ids, function ( $c, $id ) use ( $rows ) { return $c ? $c : $rows[ $id ]['img']; }, 0 ),
			'ft'     => $ft,
			'vt'     => (bool) $s['lo'],
			'd'      => $d,
			'n'      => count( $ids ),
		);
	}

	/**
	 * Development cards for a set of matching listings (a segment, a search): each development's card with its prices and
	 * bedrooms narrowed to the listings that matched, and those listings in 'match'. Keyed by development.
	 */
	function blockke_bkn_group( $ids, $rent = false ) {
		$all  = blockke_bkn_devs();
		$rows = blockke_bkn_rows();
		$by   = array();
		foreach ( $ids as $id ) {
			if ( isset( $all['of'][ $id ], $rows[ $id ] ) ) {
				$by[ $all['of'][ $id ] ][] = $rows[ $id ];
			}
		}
		$out = array();
		foreach ( $by as $k => $rs ) {
			$c     = $all['devs'][ $k ];
			$ps    = array();
			$beds  = array();
			$types = array();
			$match = array();
			foreach ( $rs as $r ) {
				if ( 'unit' !== $r['f'] && $r['p'] > 0 && ( $rent || $r['p'] >= 1000000 ) ) {
					$ps[] = $r['p'];
				}
				if ( 'home' === $r['kind'] && ! $r['dev'] && '' !== $r['k'] ) {
					$beds[] = 'studio' === $r['k'] ? 0 : max( 1, $r['b'] );
				}
				$beds = array_merge( $beds, blockke_bkn_range_beds( $r ) );
				if ( '' !== $r['type'] ) {
					$types[ $r['type'] ] = 1;
				}
				if ( ! $r['dev'] ) {
					$match[] = $r;
				}
			}
			$beds = $beds ? array_values( array_unique( $beds ) ) : $c['beds']; // a development listed without its homes
			sort( $beds );
			usort(
				$match,
				function ( $a, $b ) {
					return ( $a['p'] > 0 ? $a['p'] : PHP_INT_MAX ) <=> ( $b['p'] > 0 ? $b['p'] : PHP_INT_MAX );
				}
			);
			$c['beds']  = $beds;
			$c['types'] = $types ? array_keys( $types ) : $c['types'];
			$c['match'] = $match;
			if ( $rent ) {
				$c['rfrom'] = $ps ? min( $ps ) : 0;
				$c['rto']   = $ps ? max( $ps ) : 0;
			} else {
				$c['from'] = $ps ? min( $ps ) : 0;
				$c['to']   = $ps ? max( $ps ) : 0;
			}
			$c['unit'] = ! $ps && (bool) array_filter(
				$rs,
				function ( $r ) {
					return 'unit' === $r['f'];
				}
			);
			$out[ $k ] = blockke_bkn_for_act( $c, wp_list_pluck( $rs, 'id' ), $rent ? 'rent' : 'sale' );
		}
		return $out;
	}

	/** "KES 6.06M", "KES 25.3M", "KES 180K": rounded up, so a "from" price is never understated. */
	function blockke_bkn_money( $v ) {
		$cur = blockke_dev_config()['currency'];
		if ( $v <= 0 ) {
			return '';
		}
		if ( $v >= 1000000 ) {
			$dp = $v >= 10000000 ? 1 : 2;
			$x  = ceil( round( $v / 1000000 * pow( 10, $dp ), 6 ) ) / pow( 10, $dp ); // scaled before rounding off the float noise: 4.9 * 100 is 490.00000000000006
			return $cur . ' ' . rtrim( rtrim( number_format( $x, $dp, '.', '' ), '0' ), '.' ) . 'M';
		}
		$k = ceil( round( $v / 1000, 6 ) );
		return $cur . ' ' . ( $k >= 1000 ? '1M' : $k . 'K' );
	}

	/**
	 * "From KES 6.2M", "KES 168.5M", "KES 6.2M–19.8M" (with a price filter), rents " a month". $many: the card covers several
	 * sizes, so one price is still a "from" price ("1–4 bed · From KES 6M", never a 4-bed at the studio's price).
	 */
	function blockke_bkn_price( $from, $to, $rent = false, $range = false, $many = false ) {
		if ( $from <= 0 ) {
			return '';
		}
		$m = $rent ? ' a month' : '';
		if ( $to > $from ) {
			return $range ? blockke_bkn_money( $from ) . '–' . preg_replace( '/^\D+/', '', blockke_bkn_money( $to ) ) . $m : 'From ' . blockke_bkn_money( $from ) . $m;
		}
		return ( $many ? 'From ' : '' ) . blockke_bkn_money( $from ) . $m;
	}

	/** "Studio", "3 bed", "1–3 bed", "Studio to 3 bed"; villas and houses say so ("5 bed villa", "3–5 bed villas"). */
	function blockke_bkn_beds( $beds, $types = array() ) {
		if ( ! $beds ) {
			return '';
		}
		$lo = min( $beds );
		$hi = max( $beds );
		$t  = '';
		if ( 1 === count( $types ) && in_array( $types[0], array( 'villa', 'house' ), true ) ) {
			$t = ' ' . $types[0] . ( $lo !== $hi ? 's' : '' );
		}
		if ( $lo === $hi ) {
			return 0 === $lo ? 'Studio' . ( $t ? $t : '' ) : $lo . ' bed' . $t;
		}
		return ( 0 === $lo ? 'Studio to ' : $lo . '–' ) . $hi . ' bed' . $t;
	}

	/** "Completion Sep 2028", "Completion Q2 2027", "Completion late 2027", "Completion 2027", "Ready to move in", or ''. */
	function blockke_bkn_done_text( $c, $flat = false ) {
		if ( 'ready' === $c['st'] ) {
			return 'Ready to move in';
		}
		list( $y, $m, $q, $w, $passed ) = $c['done'];
		if ( ! $y || $passed ) {
			return $flat ? 'Completion to be confirmed' : '';
		}
		$mon = array( '', 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec' );
		return 'Completion ' . ( $m ? $mon[ $m ] . ' ' : ( $q ? 'Q' . $q . ' ' : ( $w ? $w . ' ' : '' ) ) ) . $y;
	}

	/** The completion as a sortable key: 202809 for Sep 2028, 202812 for "2028", 999999 when unknown or passed. */
	function blockke_bkn_done_key( $c ) {
		list( $y, $m, $q, $w, $passed ) = $c['done'];
		if ( ! $y || $passed ) {
			return 999999;
		}
		return $y * 100 + ( $m ? $m : ( $q ? $q * 3 : ( 'early' === $w ? 4 : ( 'mid' === $w ? 7 : 12 ) ) ) );
	}

	/**
	 * Photos for cards loaded in two queries (the attachments and their sizes and alt text) rather than one or two per card
	 * on a host without a persistent object cache. $ids: attachment ids; null: every development's cover (the first card
	 * drawn does this once). Pages can add their own, such as the photos of single homes in search results.
	 */
	function blockke_bkn_prime( $ids = null ) {
		$ids = null === $ids ? wp_list_pluck( blockke_bkn_devs()['devs'], 'cover' ) : (array) $ids;
		$ids = array_values( array_unique( array_filter( array_map( 'intval', $ids ) ) ) );
		if ( $ids && function_exists( '_prime_post_caches' ) ) {
			_prime_post_caches( $ids, false, true );
		}
	}

	/**
	 * One card, every page. $v: 'dev' (default), 'lead', 'row' (small, phones), 'group' (search: with the matching homes),
	 * 'unit' (search: one home). $ctx: 'src' (index, area, segment, search), 'scope' (an area slug: drop it from the place
	 * line), 'pos', 'range' (show the price range), 'rent', 'tag' ("New on Block"), 'lazy' (default true).
	 */
	function blockke_card_html( $c, $v = 'dev', $ctx = array() ) {
		static $primed = false;
		if ( ! $primed ) {
			$primed = true;
			blockke_bkn_prime();
		}
		$e     = 'esc_html';
		$ctx   = array_merge( array( 'src' => 'index', 'scope' => '', 'pos' => 0, 'range' => false, 'rent' => false, 'tag' => false, 'lazy' => true, 'sizes' => '(min-width:1000px) 379px, (min-width:768px) calc(50vw - 52px), calc(100vw - 40px)' ), $ctx );
		$rent  = $ctx['rent'];
		$many  = 'unit' !== $v && count( $c['beds'] ) > 1;
		$price = $rent ? blockke_bkn_price( $c['rfrom'], $c['rto'], true, $ctx['range'], $many ) : blockke_bkn_price( $c['from'], $c['to'], false, $ctx['range'], $many );
		$place = $ctx['scope'] && $c['area'] === $ctx['scope'] ? '' : $c['place'];
		$bd    = blockke_bkn_beds( $c['beds'], $c['types'] );
		$done  = $rent ? '' : blockke_bkn_done_text( $c );
		// each part on one line, so a narrow card breaks between "KES 5.9M ·" and "Completion Mar 2027", never inside a date
		$pl    = implode( ' · ', array_filter( array( $e( $place ), '' !== $bd ? '<span class="bkn-nw">' . $e( $bd ) . '</span>' : '' ) ) );
		$dl    = implode( ' · ', array_filter( array( '' !== $price ? '<b>' . $e( $price ) . '</b>' : '', '' !== $done ? '<span class="bkn-nw">' . $e( $done ) . '</span>' : '' ) ) );
		$alt   = $c['cover'] ? trim( (string) get_post_meta( $c['cover'], '_wp_attachment_image_alt', true ) ) : '';
		$alt   = '' !== $alt ? $alt : $c['name'] . ( '' !== $c['place'] ? ', ' . $c['place'] : '' );
		$size  = 'row' === $v ? 'medium' : ( 'lead' === $v ? 'large' : 'medium_large' );
		$attr  = array(
			'alt'      => $alt,
			'decoding' => 'async',
			'sizes'    => 'lead' === $v ? '(min-width:1000px) 690px, 100vw' : ( 'row' === $v ? '112px' : $ctx['sizes'] ),
		);
		if ( 'lead' === $v ) {
			$attr['fetchpriority'] = 'high';
			$attr['loading']       = false; // WordPress would otherwise add loading="lazy" itself
		} else {
			$attr['loading'] = $ctx['lazy'] ? 'lazy' : false;
		}
		$img   = $c['cover'] ? wp_get_attachment_image( $c['cover'], $size, false, $attr ) : '';
		$data  = ' data-id="' . (int) $c['id'] . '" data-areas="' . esc_attr( implode( ' ', $c['areas'] ) ) . '" data-beds="' . esc_attr( implode( ' ', $c['beds'] ) ) . '"'
			. ' data-from="' . (int) ( $rent ? $c['rfrom'] : $c['from'] ) . '" data-to="' . (int) ( $rent ? $c['rto'] : $c['to'] ) . '" data-done="' . (int) blockke_bkn_done_key( $c ) . '" data-d="' . (int) $c['d'] . '" data-st="' . esc_attr( $c['st'] ) . '" data-vt="' . ( $c['vt'] ? 1 : 0 ) . '"';
		$link  = '<a class="bkn-a" href="' . esc_url( $c['url'] ) . '" data-bkn="' . esc_attr( $ctx['src'] ) . '"' . ( $ctx['pos'] ? ' data-pos="' . (int) $ctx['pos'] . '"' : '' ) . '>';
		$tag   = $ctx['tag'] ? '<span class="bkn-tag">New on Block</span>' : '';
		if ( 'unit' === $v ) { // one home, Compass order: price, what it is, where
			$what = array_filter( array( blockke_bkn_beds( $c['beds'], $c['types'] ), ! empty( $c['sqm'] ) ? blockke_dev_num( $c['sqm'] ) . ' m²' : '' ) );
			return '<article class="bkn-card bkn-unit" id="d-' . (int) $c['id'] . '"' . $data . '>' . $link . '<span class="bkn-img">' . $img . $tag . '</span>'
				. '<span class="bkn-uprice">' . $e( '' !== $price ? $price : 'Price on request' ) . '</span></a>'
				. '<p class="bkn-place">' . $e( implode( ' · ', $what ) ) . '</p><p class="bkn-data">' . $e( implode( ', ', array_filter( array( $c['name'], $place ) ) ) ) . '</p></article>';
		}
		if ( 'row' === $v ) {
			return '<article class="bkn-row" id="d-' . (int) $c['id'] . '"' . $data . '>' . $link . '<span class="bkn-img">' . $img . '</span><span class="bkn-rt"><span class="bkn-name">' . $e( $c['name'] ) . '</span>'
				. '<span class="bkn-data">' . $dl . '</span></span></a></article>';
		}
		$h = '<article class="bkn-card' . ( 'lead' === $v ? ' bkn-lead-card' : '' ) . '" id="d-' . (int) $c['id'] . '"' . $data . '>' . $link
			. '<span class="bkn-img">' . $img . $tag . '</span>'
			. ( 'lead' === $v ? '<h2 class="bkn-name">' . $e( $c['name'] ) . '</h2>' : '<h3 class="bkn-name">' . $e( $c['name'] ) . '</h3>' ) . '</a>'
			. ( '' !== $pl ? '<p class="bkn-place">' . $pl . '</p>' : '' )
			. ( '' !== $dl ? '<p class="bkn-data">' . $dl . '</p>' : '' )
			. ( 'lead' === $v && '' !== $c['line'] ? '<p class="bkn-line">' . $e( $c['line'] ) . '</p>' : '' );
		if ( 'group' === $v && ! empty( $c['match'] ) ) { // the homes in it that match a search
			$m  = $c['match'];
			$mb = array();
			foreach ( $m as $r ) {
				$mb[ 'studio' === $r['k'] ? 0 : max( 1, $r['b'] ) ] = 1;
			}
			ksort( $mb );
			$bl = array_map(
				function ( $b ) {
					return 0 === $b ? 'studio' : (string) $b;
				},
				array_keys( $mb )
			);
			$h .= '<div class="bkn-match"><p class="bkn-mh">' . $e( count( $m ) . ( 1 === count( $m ) ? ' home matches' : ' homes match' ) . ( $bl ? ': ' . ( count( $bl ) > 1 ? implode( ', ', array_slice( $bl, 0, -1 ) ) . ' and ' . end( $bl ) : $bl[0] ) . ( 'studio' === end( $bl ) && 1 === count( $bl ) ? '' : ' bed' ) : '' ) ) . '</p><ul>';
			foreach ( array_slice( $m, 0, 3 ) as $i => $r ) {
				$what = array_filter( array( 'studio' === $r['k'] ? 'Studio' : ( $r['b'] ? $r['b'] . ' bed' : '' ), $r['sq'] > 0 ? blockke_dev_num( round( $r['sq'] * blockke_dev_sqft_factor() ) ) . ' m²' : '' ) );
				$h   .= '<li><a href="' . esc_url( $r['u'] ) . '" data-bkn="' . esc_attr( $ctx['src'] ) . '" data-row="' . ( $i + 1 ) . '"><span>' . $e( implode( ' · ', $what ) ) . '</span><b>' . $e( $r['p'] > 0 && 'unit' !== $r['f'] ? blockke_bkn_money( $r['p'] ) . ( $rent ? ' a month' : '' ) : 'Price on request' ) . '</b></a></li>';
			}
			$h .= '</ul>' . ( count( $m ) > 3 ? '<a class="bkn-mall" href="' . esc_url( $c['url'] ) . '#bkd-residences" data-bkn="' . esc_attr( $ctx['src'] ) . '">' . $e( 'See all ' . count( $m ) . ' in the price list' ) . '</a>' : '' ) . '</div>';
		}
		return $h . '</article>';
	}

	/**
	 * The card family's styles: shared by the index, search results and area pages. Also what those list pages (#bkd.bkn) share
	 * beyond the cards: headings set without a lone last word, and forms in sentence case (labels 14px, buttons 15px, no arrows;
	 * development pages keep their own forms). A photo that fails to load leaves the plain photo box, never a placeholder tile.
	 */
	function blockke_bkn_card_css() {
		return <<<'CSS'
#bkd .bkn-card{position:relative;min-width:0}
#bkd .bkn-a{display:block;color:var(--navy)!important;text-decoration:none}
#bkd .bkn-a::after{content:"";position:absolute;inset:0;z-index:0}
#bkd .bkn-a:focus-visible{outline:none}
#bkd .bkn-a:focus-visible::after{outline:2px solid var(--brass-d);outline-offset:4px}
#bkd .bkn-img{position:relative;display:block;aspect-ratio:3/2;overflow:hidden;background:var(--mist)}
#bkd .bkn-img img{width:100%!important;height:100%!important;max-width:none!important;object-fit:cover;transition:filter .2s,opacity .2s}
#bkd .bkn-tag{position:absolute;left:0;bottom:0;padding:6px 10px;background:var(--paper);font-size:12px;font-weight:500;line-height:1.3;color:var(--brass-d)}
#bkd .bkn-name{margin:14px 0 0;font-family:var(--display)!important;font-weight:var(--display-w)!important;font-size:26px!important;line-height:1.15!important;letter-spacing:var(--display-ls)!important;text-transform:none!important;color:var(--navy)!important}
#bkd .bkn-place{margin:6px 0 0;font-size:14px;line-height:1.5;color:var(--slate)}
#bkd .bkn-data{margin:4px 0 0;font-size:15px;line-height:1.5;color:var(--ink);font-variant-numeric:tabular-nums}
#bkd .bkn-data b{font-weight:600;color:var(--navy)}
#bkd .bkn-data b,#bkd .bkn-nw{white-space:nowrap}
#bkd .bkn-img>.bkd-ph{display:none}
#bkd .bkn-line{margin:14px 0 0;max-width:38ch;font-size:16px;line-height:1.6;color:var(--ink)}
@media (hover:hover){#bkd .bkn-card:hover .bkn-img img{filter:brightness(.94)}#bkd .bkn-card:hover .bkn-name{text-decoration:underline;text-decoration-thickness:1px;text-underline-offset:5px}}
#bkd .bkn-uprice{display:block;margin:14px 0 0;font-size:20px;font-weight:600;line-height:1.3;color:var(--navy);font-variant-numeric:tabular-nums}
#bkd .bkn-unit .bkn-place{margin-top:4px;color:var(--ink)}
#bkd .bkn-unit .bkn-data{margin-top:2px;font-size:14px;color:var(--slate)}
#bkd .bkn-match{position:relative;z-index:1;margin:16px 0 0;padding:14px 0 0;border-top:1px solid var(--line)}
#bkd .bkn-mh{margin:0 0 6px;font-size:14px;font-weight:600;color:var(--ink)}
#bkd .bkn-match li{margin:0}
#bkd .bkn-match li a{display:flex;justify-content:space-between;gap:16px;min-height:44px;align-items:center;font-size:14px;color:var(--ink)!important;text-decoration:none;border-bottom:1px solid var(--line)}
#bkd .bkn-match li a b{font-weight:600;color:var(--navy);font-variant-numeric:tabular-nums;white-space:nowrap}
#bkd .bkn-match li a:hover span{text-decoration:underline;text-underline-offset:4px}
#bkd .bkn-mall{display:inline-flex;min-height:44px;align-items:center;font-size:14px;font-weight:500;color:var(--navy)!important;text-decoration:underline;text-underline-offset:4px}
#bkd .bkn-row{position:relative;border-bottom:1px solid var(--line)}
#bkd .bkn-row .bkn-a{display:grid;grid-template-columns:112px minmax(0,1fr);gap:16px;align-items:center;padding:14px 0}
#bkd .bkn-row .bkn-img{width:112px;aspect-ratio:3/2}
#bkd .bkn-rt{display:block;min-width:0}
#bkd .bkn-row .bkn-name{display:block;margin:0;font-size:18px!important;line-height:1.2!important}
#bkd .bkn-row .bkn-data{display:block;margin-top:4px;font-size:14px}
@media (min-width:1000px){#bkd .bkn-grid-4 .bkn-name{font-size:22px!important}}
@media (max-width:639px){#bkd .bkn-name{font-size:24px!important}}
#bkd .bkn-h1,#bkd .bkn-h2,#bkd .bkn-gy,#bkd .bka16-h3,#bkd .bks-h1,#bkd .bks-h2,#bkd .bkn-none-t{text-wrap:balance}
#bkd .bkn-stand,#bkd .bkn-sub,#bkd .bkn-name{text-wrap:pretty}
#bkd.bkn .bkd-f>span,#bkd.bkn .bkd-f legend{font-size:14px;font-weight:500;letter-spacing:0;text-transform:none}
#bkd.bkn .bkd-btn{font-size:15px!important;letter-spacing:0!important;text-transform:none!important}
#bkd.bkn .bkd-btn>.bkd-i:not(.bkd-i-f){display:none}
CSS;
	}

	/**
	 * Shared by every list page (index, search results, area and segment pages): what happens when a card is clicked and
	 * when the visitor comes back (context for the development page, scroll restore, the photo transition, prefetch).
	 * Printed inside the page script, after window.BKDEV is set.
	 */
	function blockke_bkn_nav_js() {
		// A card click writes sessionStorage bkn_ctx: id (the card's data-id), n (which copy of that card, when a list shows it
		// twice), path (where it leads), from (this list's URL), label (window.BKDEV.listLabel), src (data-bkn), beds (?beds= or
		// window.BKDEV.ctxBeds), vt (its photo moves), im (the file the card showed), k (opened from the keyboard), y, t; r once
		// the list has been restored. The development page reads it (blockke_bkn_dev_js()). The way back is also kept in the
		// list's own history entry (history.state.bkn: id, n, y, k; r once restored), because sessionStorage holds only the
		// last list clicked: after two lists, Back to the first still finds its card, and its paused scroll restoration is
		// switched back on. blockke_bkn_vt_head() reads it too, to hold the first paint until the card is parsed and centre it as
		// the page reveals; a card already in view then stays where it is (on a long list the page can be too short to centre
		// it at that moment, and a second move would undo the photo's landing). The focus ring shows only to visitors who
		// opened the card from the keyboard (or a screen reader), not after a tap or a click.
		// window.bknVT exists only while the photo transition is on. Print this after the list's own script, so the cards
		// are in place.
		$js = <<<'JS'
(function(){
  'use strict';
  if(window.bknNav){return;}window.bknNav=1;
  var B=window.BKDEV||{},V=window.bknVT,K='bkn_ctx',L=location;
  function get(){try{return JSON.parse(sessionStorage.getItem(K)||'null');}catch(e){return null;}}
  function put(x){try{sessionStorage.setItem(K,JSON.stringify(x));}catch(e){}}
  function st(){var s=history.state;return s&&typeof s==='object'?s:{};}
  function mark(r){try{history.replaceState(Object.assign({},st(),{bkn:r}),'');}catch(e){}}
  function manual(on){try{if('scrollRestoration' in history){history.scrollRestoration=on?'manual':'auto';}}catch(e){}}
  function all(id){return Array.prototype.slice.call(document.querySelectorAll('[id="d-'+(+id)+'"]'));}
  function seen(c){return c&&c.getClientRects().length>0;}
  function beds(){var b='';try{b=new URLSearchParams(L.search).get('beds')||'';}catch(e){}b=String(b||B.ctxBeds||'').toLowerCase();return /^(studio|\d)$/.test(b)?b:'';}
  /* a plain click on a card: remember the list, its place on it and the visitor's bedrooms; the URL stays clean */
  document.addEventListener('click',function(e){
    var a=e.target.closest&&e.target.closest('a[data-bkn]'),c,im,vt,x;
    if(!a||e.defaultPrevented||e.button!==0||e.metaKey||e.ctrlKey||e.shiftKey||e.altKey||(a.target&&a.target!=='_self')||a.hasAttribute('download')||a.origin!==L.origin){return;}
    c=a.closest('[data-id]');vt=!!(c&&a.classList.contains('bkn-a')&&c.getAttribute('data-vt')==='1');im=vt?c.querySelector('.bkn-img img'):null;
    x={id:c?+c.getAttribute('data-id'):0,n:c?all(c.getAttribute('data-id')).indexOf(c):0,path:a.pathname,from:L.href,label:String(B.listLabel||''),src:a.getAttribute('data-bkn')||'',beds:beds(),vt:vt?1:0,im:im?String(im.currentSrc||im.src||''):'',k:e.detail===0?1:0,y:Math.round(window.scrollY||window.pageYOffset||0),t:Date.now()};
    put(x);mark({id:x.id,n:x.n,y:x.y,k:x.k});
    manual(true); /* only when leaving by a card, so other ways back keep the browser's own scroll */
    if(V&&im){V.c();im.style.viewTransitionName='bkn-photo';V.i=im;V.h=a.href;} /* only the card's own photo moves */
  });
  /* back without the back/forward cache: the card in view and focused (its ring only after a keyboard Enter), else where the visitor was */
  function done(){var x=get();if(x&&x.from===L.href&&!x.r){x.r=1;put(x);}}
  function restore(late){
    var r=st().bkn,x=get(),l,c,f,s;if(!r){r=x&&x.from===L.href?x:null;}if(!r||r.r){return;}
    l=all(r.id);c=seen(l[r.n])?l[r.n]:l.filter(seen)[0];
    if(!c&&!late&&document.readyState==='loading'){document.addEventListener('DOMContentLoaded',function(){restore(1);});return;} /* cards a list script adds later */
    if(c){s=c.getBoundingClientRect();if(s.top<0||s.bottom>innerHeight){c.scrollIntoView({block:'center'});}f=c.querySelector('a[data-bkn]');
      if(f){if(!r.k){s=document.createElement('style');s.textContent='#bkd .bkn-quiet:focus-visible,#bkd .bkn-quiet:focus-visible::after{outline:0}';document.head.appendChild(s);f.classList.add('bkn-quiet');f.addEventListener('blur',function(){f.classList.remove('bkn-quiet');},{once:true});}
        try{f.focus({preventScroll:true});}catch(e){}}}else{window.scrollTo(0,+r.y||0);}
    mark({id:r.id,n:r.n,y:r.y,k:r.k,r:1});done();
    var auto=function(){setTimeout(function(){manual(false);},0);};
    if(document.readyState==='complete'){auto();}else{window.addEventListener('load',auto);}
  }
  var nv=window.performance&&performance.getEntriesByType?performance.getEntriesByType('navigation')[0]:null;
  if(nv&&nv.type==='back_forward'){restore();}
  window.addEventListener('pageshow',function(e){if(!e.persisted){return;}if(V){V.c();}var r=st().bkn;if(r&&!r.r){r.r=1;mark(r);}done();manual(false);});
})();
JS;
		if ( blockke_bkn_on( 'prefetch' ) ) { // fetched while the pointer rests on a card (or on touch); never prerendered, so analytics run once
			$js .= "\n(function(){var k=navigator.connection||{};if(k.saveData||/2g$/.test(k.effectiveType||'')||!(window.HTMLScriptElement&&HTMLScriptElement.supports&&HTMLScriptElement.supports('speculationrules'))){return;}"
				. "var s=document.createElement('script');s.type='speculationrules';s.textContent='{\"prefetch\":[{\"source\":\"document\",\"where\":{\"selector_matches\":\"a[data-bkn]\"},\"eagerness\":\"moderate\"}]}';document.head.appendChild(s);})();";
		}
		return "\n" . $js . "\n";
	}

	/**
	 * In <head> on every #bkd page when view_transitions is on: the transition styles and the early listener script. $list:
	 * the page is a list of cards (index, area, segment, search), which also gets the early part of the way back.
	 */
	function blockke_bkn_vt_head( $list = true ) {
		// pagereveal fires before deferred or footer scripts can listen, so this script stays inline and unoptimised. Slow
		// phones (Save-Data, 2G, 2 GB of memory or less) skip the transition. window.bknVT: c() clears the photo a card click
		// named (i), h is that card's link. Each handler gets the transition, the context, the hero image, m (this is the
		// development the card opened), b (this is the list the card was on) and the event.
		// - leaving: a photo named by a click that led elsewhere is cleared; the hero keeps its name only on the way back to its list
		// - arriving from a card: the hero image is backed with the very file the card showed (already in the browser), so the
		//   photo never lands on an empty box while the large file loads. On the image itself, not its box: the box stays in
		//   the page's cross-fade, and a photo there would show twice while the card's photo is still moving.
		// - arriving back on the list: the card's photo, when on screen, takes the hero's place, then loses its name
		// - back on a list the browser did not keep (history.state.bkn from blockke_bkn_nav_js(), not yet restored; lists
		//   only): the first paint waits until that card is parsed (<link rel=expect>, allowed while <body> is not), and the
		//   card is centred as the page reveals, before its photo is named. Without this a long list (the index on a phone)
		//   revealed at the top and jumped to the card only when the footer script ran, so the photo never moved back. The
		//   footer script still focuses the card, and does it all where this can't run.
		$css = '@view-transition{navigation:auto}@media (prefers-reduced-motion:reduce){@view-transition{navigation:none}}'
			. '#bkd .bkd-hero-media>img{view-transition-name:bkn-photo}'
			. '::view-transition-group(bkn-photo){animation-duration:280ms;animation-timing-function:cubic-bezier(.2,0,0,1)}'
			. '::view-transition-old(bkn-photo),::view-transition-new(bkn-photo){width:100%;height:100%;object-fit:cover;overflow:clip}'
			. '::view-transition-old(root),::view-transition-new(root){animation-duration:160ms}';
		$back = $list ? "try{r=history.state.bkn;q=performance.getEntriesByType('navigation')[0].type}catch(x){}if(r&&!r.r&&q=='back_forward'){q=d.createElement('link');q.rel='expect';q.href=h='#d-'+(r.id|0);q.blocking='render';d.head.appendChild(q);w.addEventListener('pagereveal',function(c){c=d.querySelectorAll(h);(c=c[r.n]||c[0])&&c.scrollIntoView({block:'center'})},{once:!0})}" : '';
		$js   = <<<'JS'
!function(w,d){var n=navigator,k=n.connection||{},s=k.saveData||/2g$/.test(k.effectiveType)||n.deviceMemory<=2,N='viewTransitionName',L=location,V=w.bknVT={c:function(){V.i&&(V.i.style[N]='',V.i=0)}},r,q,h;
JS;
		$js  .= $back . <<<'JS'
function on(t,f){w.addEventListener(t,function(e){var v=e.viewTransition,c,h=d.querySelector('#bkd-hero-media>img');if(v)if(s)v.skipTransition();else{try{c=JSON.parse(sessionStorage.bkn_ctx)}catch(x){}c=c||{};f(v,c,h,c.vt&&c.path==L.pathname,c.vt&&c.from==L.href,e)}})}on('pageswap',function(v,c,h,m,b,e){var t=e.activation.entry.url;t!=V.h&&V.c();h&&!(m&&t==c.from)&&(h.style[N]='none')});on('pagereveal',function(v,c,h,m,b,i){V.c();h&&m&&c.im&&(h.style.background='url('+JSON.stringify(c.im)+') 50%/cover');if(b&&(i=d.querySelectorAll('#d-'+(c.id|0)+' .bkn-img img'),i=i[c.n]||i[0])){h=i.getBoundingClientRect();h.top<innerHeight&&h.bottom>0&&(i.style[N]='bkn-photo',V.i=i,v.finished.then(V.c))}})}(window,document);
JS;
		return '<style id="bkn-vt">' . $css . "</style>\n" . '<script id="bkn-vt-js" data-cfasync="false" data-no-optimize="1" data-no-defer="1">' . $js . "</script>\n";
	}

	/* ---------------------------------------------------------------------
	   @v16-index: the new developments index (/new-developments/)
	   --------------------------------------------------------------------- */

	/** The page the index takes over (config dev_index_page). */
	function blockke_index_page_id() {
		return (int) blockke_dev_config()['dev_index_page'];
	}

	/** Sale price bands from config budget_bands: slug ("8-12", KES millions) => array( from, to (0 = no limit), label ). */
	function blockke_index_bands() {
		$cfg = blockke_dev_config();
		$out = array();
		foreach ( isset( $cfg['budget_bands']['sale'] ) ? (array) $cfg['budget_bands']['sale'] : array() as $b ) {
			$lo = (float) $b[0];
			$hi = (float) $b[1];
			$m  = function ( $v ) {
				return rtrim( rtrim( number_format( $v / 1000000, 1, '.', '' ), '0' ), '.' );
			};
			$slug         = $m( $lo ) . '-' . ( $hi > 0 ? $m( $hi ) : '' );
			$out[ $slug ] = array( $lo, $hi, $lo <= 0 ? 'Under KES ' . $m( $hi ) . 'M' : ( $hi > 0 ? 'KES ' . $m( $lo ) . '–' . $m( $hi ) . 'M' : 'KES ' . $m( $lo ) . 'M and above' ) );
		}
		return $out;
	}

	/** The completion filter's values (the Completion taxonomy's slugs, so older ?completion= links keep working). */
	function blockke_index_years() {
		$y   = (int) current_time( 'Y' );
		$out = array();
		for ( $i = 0; $i < 3; $i++ ) {
			$out[ (string) ( $y + $i ) ] = (string) ( $y + $i );
		}
		$out[ ( $y + 3 ) . '-plus' ] = ( $y + 3 ) . ' and later';
		$out['ready-now']            = 'Ready now';
		return $out;
	}

	/**
	 * Everything the index shows, from the developments: the grid (off-plan homes for sale with a cover, photos, a price and
	 * an area), Ready to move into, the A-Z, the lead and the picks. Cached with the developments.
	 */
	function blockke_index_data() {
		static $x = null;
		if ( null !== $x ) {
			return $x;
		}
		$cfg  = blockke_dev_config();
		$all  = blockke_bkn_devs();
		$ver  = blockke_bkn_ver() . '|ix2|' . gmdate( 'o-W' ) . '|' . md5( wp_json_encode( array( $cfg['index_picks'], $cfg['index_min_photos'], $cfg['index_ready_singles'], $cfg['index_new_days'], $cfg['neighbours'], $cfg['index_metro'] ) ) );
		$hit  = get_transient( 'bkn_index' );
		if ( is_array( $hit ) && isset( $hit['ver'] ) && $hit['ver'] === $ver ) {
			$x = $hit['data'];
			return $x;
		}
		$grid  = array();
		$ready = array();
		$az    = array();
		$skip  = array();
		$other = array( 'commercial' => 0, 'land' => 0 );
		foreach ( $all['devs'] as $k => $c ) {
			if ( ! in_array( 'sale', $c['acts'], true ) ) {
				continue;
			}
			if ( 'home' !== $c['kind'] ) {
				$other[ $c['kind'] ] = ( isset( $other[ $c['kind'] ] ) ? $other[ $c['kind'] ] : 0 ) + 1;
				continue;
			}
			if ( '' === $c['area'] && '' === $c['st'] ) {
				$skip[ (int) $c['id'] ] = $c['name']; // no area and no status: nowhere until it is tagged (the admin bar lists them)
				continue;
			}
			$az[ $k ] = $c;
			$ok       = $c['cover'] && $c['photos'] >= (int) $cfg['index_min_photos'] && $c['from'] > 0 && '' !== $c['area'];
			if ( $ok && 'offplan' === $c['st'] ) {
				$grid[ $k ] = $c;
			} elseif ( $ok && 'ready' === $c['st'] && ( 'i' !== $k[0] || ! empty( $cfg['index_ready_singles'] ) ) ) {
				$ready[ $k ] = $c;
			}
		}
		// order inside a completion year: featured, then pages on the new layout, soonest, most photos, newest
		$rank = function ( $a, $b ) {
			if ( $a['ft'] !== $b['ft'] ) {
				return $a['ft'] ? -1 : 1;
			}
			if ( $a['vt'] !== $b['vt'] ) {
				return $a['vt'] ? -1 : 1;
			}
			$ka = blockke_bkn_done_key( $a );
			$kb = blockke_bkn_done_key( $b );
			if ( $ka !== $kb ) {
				return $ka - $kb;
			}
			return $b['photos'] !== $a['photos'] ? $b['photos'] - $a['photos'] : $b['d'] - $a['d'];
		};
		uasort( $grid, $rank );
		uasort(
			$ready,
			function ( $a, $b ) {
				return $a['ft'] !== $b['ft'] ? ( $a['ft'] ? -1 : 1 ) : ( $b['photos'] !== $a['photos'] ? $b['photos'] - $a['photos'] : $b['d'] - $a['d'] );
			}
		);
		// lead and picks: the editor's, else the strongest off-plan pages on the new layout (rotating weekly)
		$lead  = '';
		$picks = array();
		$byid  = array();
		foreach ( $grid as $k => $c ) {
			$byid[ $c['id'] ] = $k;
		}
		$wide = function ( $c, $min ) {
			$m = wp_get_attachment_metadata( $c['cover'] );
			return is_array( $m ) && ! empty( $m['width'] ) && (int) $m['width'] >= $min;
		};
		foreach ( (array) $cfg['index_picks'] as $i => $id ) {
			if ( isset( $byid[ (int) $id ] ) ) {
				if ( '' === $lead ) {
					$lead = $byid[ (int) $id ];
				} elseif ( count( $picks ) < 2 ) {
					$picks[] = $byid[ (int) $id ];
				}
			}
		}
		if ( '' === $lead || count( $picks ) < 2 ) {
			$cand = array();
			foreach ( $grid as $k => $c ) {
				if ( $c['vt'] && $c['photos'] >= 10 && blockke_bkn_done_key( $c ) < 999999 && $k !== $lead && ! in_array( $k, $picks, true ) && $wide( $c, 1200 ) ) {
					$cand[ $k ] = $c;
				}
			}
			uasort(
				$cand,
				function ( $a, $b ) {
					return $a['ft'] !== $b['ft'] ? ( $a['ft'] ? -1 : 1 ) : ( $b['photos'] !== $a['photos'] ? $b['photos'] - $a['photos'] : $a['id'] - $b['id'] );
				}
			);
			$top  = array_slice( array_keys( $cand ), 0, 6 );
			$week = gmdate( 'o-W' );
			usort(
				$top,
				function ( $a, $b ) use ( $cand, $week ) {
					return crc32( $cand[ $a ]['id'] . $week ) - crc32( $cand[ $b ]['id'] . $week );
				}
			);
			foreach ( $top as $k ) {
				if ( '' === $lead && $wide( $cand[ $k ], 1920 ) ) {
					$lead = $k;
				} elseif ( count( $picks ) < 2 && $k !== $lead ) {
					$picks[] = $k;
				}
			}
			if ( '' === $lead && $picks ) { // nothing wide enough for the lead: the first pick leads
				$lead = array_shift( $picks );
			}
		}
		// "New on Block": published in the last index_new_days days, at most one card in five, newest first
		$new   = array();
		$since = time() - DAY_IN_SECONDS * (int) $cfg['index_new_days'];
		$fresh = array_filter(
			$grid,
			function ( $c ) use ( $since ) {
				return $c['d'] >= $since;
			}
		);
		uasort(
			$fresh,
			function ( $a, $b ) {
				return $b['d'] - $a['d'];
			}
		);
		$new = array_slice( array_keys( $fresh ), 0, (int) floor( count( $grid ) / 5 ) );
		$mod  = 0;
		$far  = 0; // off-plan developments outside index_metro (Diani, Nyali, Nanyuki...): not "Nairobi and nearby"
		$rows = blockke_bkn_rows();
		$near = array_map( 'strtolower', (array) $cfg['index_metro'] );
		foreach ( $grid as $c ) {
			$mod = max( $mod, (int) get_post_modified_time( 'U', true, $c['id'] ) );
			$cy  = isset( $rows[ $c['id'] ]['cy'] ) ? strtolower( $rows[ $c['id'] ]['cy'] ) : '';
			$far += '' !== $cy && ! in_array( $cy, $near, true ) ? 1 : 0;
		}
		$x = array(
			'grid'  => $grid,
			'ready' => $ready,
			'az'    => $az,
			'other' => $other,
			'lead'  => $lead,
			'picks' => $picks,
			'new'   => $new,
			'mod'   => $mod,
			'far'   => $far,
			'skip'  => $skip,
		);
		set_transient( 'bkn_index', array( 'ver' => $ver, 'data' => $x ), DAY_IN_SECONDS );
		return $x;
	}

	/** The completion group a development falls in: '2026', '2027', '2028', '2029-plus', or 'tbc'. */
	function blockke_index_group( $c ) {
		$y = (int) current_time( 'Y' );
		list( $dy, , , , $passed ) = $c['done'];
		if ( ! $dy || $passed ) {
			return 'tbc';
		}
		return $dy >= $y + 3 ? ( $y + 3 ) . '-plus' : (string) max( $y, $dy );
	}

	/** The visitor's filters from the query string, cleaned: area, beds, price, completion, sort, n, open. */
	function blockke_index_state() {
		$g     = function ( $k ) {
			return isset( $_GET[ $k ] ) && is_scalar( $_GET[ $k ] ) ? sanitize_text_field( wp_unslash( (string) $_GET[ $k ] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
		};
		$beds  = $g( 'beds' );
		$price = $g( 'price' );
		$comp  = $g( 'completion' );
		$sort  = $g( 'sort' );
		// group keys as strings: PHP turns the keys '2026', '2027'... into integers, which the strict checks below would miss
		$open  = array_values( array_intersect( array_map( 'strval', array_keys( blockke_index_years() + array( 'tbc' => 1 ) ) ), explode( ',', $g( 'open' ) ) ) );
		return array(
			'area'       => sanitize_title( $g( 'area' ) ),
			'beds'       => in_array( $beds, array( 'studio', '1', '2', '3', '4' ), true ) ? $beds : '',
			'price'      => isset( blockke_index_bands()[ $price ] ) ? $price : '',
			'completion' => isset( blockke_index_years()[ $comp ] ) ? $comp : '',
			'sort'       => in_array( $sort, array( 'soonest', 'price-asc', 'price-desc', 'newest' ), true ) ? $sort : '',
			'n'          => min( 240, max( (int) blockke_dev_config()['index_page_size'], (int) $g( 'n' ) ) ),
			'open'       => $open,
		);
	}

	/** Whether a development matches the filters (any of them may be left out with $skip, for the "relax" counts). */
	function blockke_index_match( $c, $f, $skip = '', $areas = array() ) {
		if ( 'area' !== $skip && '' !== $f['area'] ) {
			if ( 'other' === $f['area'] ? in_array( $c['area'], $areas, true ) : ! in_array( $f['area'], $c['areas'], true ) ) {
				return false;
			}
		}
		if ( 'beds' !== $skip && '' !== $f['beds'] ) {
			$b  = 'studio' === $f['beds'] ? 0 : (int) $f['beds'];
			$ok = false;
			foreach ( $c['beds'] as $x ) {
				$ok = $ok || ( 4 === $b ? $x >= 4 : $x === $b );
			}
			if ( ! $ok ) {
				return false;
			}
		}
		if ( 'price' !== $skip && '' !== $f['price'] ) {
			$band = blockke_index_bands()[ $f['price'] ];
			$to   = $c['to'] > 0 ? $c['to'] : $c['from'];
			if ( $c['from'] <= 0 || $to < $band[0] || ( $band[1] > 0 && $c['from'] >= $band[1] ) ) {
				return false;
			}
		}
		if ( 'completion' !== $skip && '' !== $f['completion'] && 'ready-now' !== $f['completion'] ) {
			$grp = blockke_index_group( $c );
			if ( $grp !== $f['completion'] ) {
				return false;
			}
		}
		return true;
	}

	/** A flat grid's order for a sort, as the script sorts it (ties keep the recommended order, on any PHP version). */
	function blockke_index_sort( $list, $sort ) {
		$pos = array_flip( array_keys( $list ) );
		uksort(
			$list,
			function ( $a, $b ) use ( $list, $sort, $pos ) {
				$x = $list[ $a ];
				$y = $list[ $b ];
				if ( 'soonest' === $sort ) {
					$d = blockke_bkn_done_key( $x ) - blockke_bkn_done_key( $y );
				} elseif ( 'price-asc' === $sort ) {
					$d = $x['from'] - $y['from'];
				} elseif ( 'price-desc' === $sort ) {
					$d = $y['from'] - $x['from'];
				} else {
					$d = $y['d'] - $x['d'];
				}
				return $d ? ( $d < 0 ? -1 : 1 ) : $pos[ $a ] - $pos[ $b ];
			}
		);
		return $list;
	}

	/** Areas offered in the filter: those with at least three developments in the grid, busiest first. */
	function blockke_index_areas( $grid ) {
		$n = array();
		foreach ( $grid as $c ) {
			$n[ $c['area'] ] = ( isset( $n[ $c['area'] ] ) ? $n[ $c['area'] ] : 0 ) + 1;
		}
		arsort( $n );
		$out = array();
		foreach ( $n as $slug => $cnt ) {
			$t = get_term_by( 'slug', $slug, 'property_area' );
			if ( $cnt >= 3 && $t ) {
				$out[ $slug ] = blockke_dev_text( $t->name );
			}
		}
		return $out;
	}

	/** "4 bed developments in Riverside completing in 2026" style phrase for the filters set. */
	function blockke_index_phrase( $f, $areas ) {
		$b = '' === $f['beds'] ? '' : ( 'studio' === $f['beds'] ? 'studio ' : ( '4' === $f['beds'] ? '4+ bed ' : $f['beds'] . ' bed ' ) );
		$s = $b . 'developments';
		if ( '' !== $f['area'] ) {
			$s .= ' in ' . ( 'other' === $f['area'] ? 'other areas' : ( isset( $areas[ $f['area'] ] ) ? $areas[ $f['area'] ] : $f['area'] ) );
		}
		if ( '' !== $f['price'] ) {
			$band = blockke_index_bands()[ $f['price'] ];
			$s   .= ' ' . ( $band[0] <= 0 ? 'under' : ( $band[1] > 0 ? 'at' : 'from' ) ) . ' ' . preg_replace( '/^(Under |and above$)/', '', str_replace( ' and above', '', $band[2] ) );
		}
		if ( '' !== $f['completion'] ) {
			$s .= 'ready-now' === $f['completion'] ? ' ready now' : ' completing in ' . blockke_index_years()[ $f['completion'] ];
		}
		return $s;
	}

	/** "200 developments selling off-plan in Nairobi and nearby, and 4 elsewhere in Kenya": only index_metro counts as nearby. */
	function blockke_index_where( $x, $what ) {
		$far  = isset( $x['far'] ) ? (int) $x['far'] : 0;
		$near = count( $x['grid'] ) - $far;
		$s    = $near . ' ' . ( 1 === $near ? str_replace( 'developments', 'development', $what ) : $what ) . ' in Nairobi and nearby';
		return $far ? $s . ', and ' . $far . ' elsewhere in Kenya' : $s;
	}

	/** The index page. */
	function blockke_index_render() {
		$cfg   = blockke_dev_config();
		$e     = 'esc_html';
		$x     = blockke_index_data();
		$grid  = $x['grid'];
		$f     = blockke_index_state();
		$areas = blockke_index_areas( $grid );
		$bands = blockke_index_bands();
		$years = blockke_index_years();
		$url   = get_permalink( blockke_index_page_id() );
		$ag    = blockke_dev_agent( 0 );
		$set   = '' !== $f['area'] || '' !== $f['beds'] || '' !== $f['price'] || '' !== $f['completion'];
		$flat  = '' !== $f['sort'] || 'ready-now' === $f['completion'];
		$size  = max( 3, (int) $cfg['index_group_size'] );
		$froms = array();
		foreach ( $grid as $c ) {
			$froms[] = $c['from'];
		}
		// What matches, as the script will work it out: the grid, or for "Ready now" the Ready set; a flat view in its sort
		// order (the recommended order without one), so a reload, Back or no JavaScript shows the same list
		$match = array();
		foreach ( $grid as $k => $c ) {
			if ( blockke_index_match( $c, $f, '', array_keys( $areas ) ) ) {
				$match[ $k ] = $c;
			}
		}
		$fl = array();
		if ( 'ready-now' === $f['completion'] ) {
			foreach ( $x['ready'] as $k => $c ) {
				if ( blockke_index_match( $c, $f, '', array_keys( $areas ) ) ) {
					$fl[ $k ] = $c;
				}
			}
		} elseif ( $flat ) {
			$fl = $match;
		}
		if ( $fl && '' !== $f['sort'] ) {
			$fl = blockke_index_sort( $fl, $f['sort'] );
		}
		$found = 'ready-now' === $f['completion'] ? count( $fl ) : count( $match );
		// every cover's attachment and its meta in one go, rather than a query or two per card
		$covers = array();
		foreach ( array_merge( array_values( $grid ), array_values( $x['ready'] ) ) as $c ) {
			$covers[] = (int) $c['cover'];
		}
		if ( function_exists( '_prime_post_caches' ) ) {
			_prime_post_caches( array_filter( $covers ), false, true );
		}
		$wa = 'https://wa.me/' . $ag['wa'] . '?text=' . rawurlencode( 'Hi ' . $cfg['brand'] . ", I'm looking at new developments on block.ke. Please send current options and price lists. " . $url );
		$o  = array();
		$o[] = '<div id="bkd" class="bkd bkn" data-bkn-page="index">' . blockke_dev_sprite();

		// Head: what this is, how many, as of when
		$o[] = '<header class="bkn-head"><div class="bkd-wrap"><nav class="bkn-crumbs" aria-label="Breadcrumb"><a href="' . esc_url( home_url( '/' ) ) . '">Home</a><span aria-hidden="true">/</span><span aria-current="page">New developments</span></nav>'
			. '<h1 class="bkn-h1">New developments in Nairobi</h1>'
			. '<p class="bkn-stand">' . $e( blockke_index_where( $x, 'developments selling off-plan' ) . ( $froms ? ', from ' . blockke_bkn_money( min( $froms ) ) : '' ) . '. Prices and dates are as published by each developer; updated ' . wp_date( 'j M Y', $x['mod'] ? $x['mod'] : time() ) . '.' ) . '</p>'
			. '<p class="bkn-ask-top"><a href="' . esc_url( $wa ) . '" target="_blank" rel="noopener" data-bkd-wa>Ask an advisor on WhatsApp</a></p></div></header>';

		// Filter bar: a plain GET form; the script filters in place and keeps the address up to date
		$sel = function ( $name, $label, $opts, $cur ) use ( $e ) {
			$h = '<label class="bkn-f"><span class="bkd-sr">' . $e( $label ) . '</span><select name="' . esc_attr( $name ) . '" data-bkn-f="' . esc_attr( $name ) . '">';
			foreach ( $opts as $v => $l ) {
				$h .= '<option value="' . esc_attr( $v ) . '"' . selected( (string) $v, (string) $cur, false ) . '>' . $e( $l ) . '</option>';
			}
			return $h . '</select></label>';
		};
		$bopt  = array( '' => 'Any price' );
		foreach ( $bands as $k => $b ) {
			$bopt[ $k ] = $b[2];
		}
		$aopt = array( '' => 'Any area' ) + $areas + array( 'other' => 'Other areas' );
		$nset = count( array_filter( array( $f['area'], $f['beds'], $f['price'], $f['completion'] ) ) );
		$o[]  = '<div class="bkn-main"><form class="bkn-bar" method="get" action="' . esc_url( $url ) . '" data-bkn-bar role="search" aria-label="Filter new developments"><div class="bkd-wrap bkn-bar-in">'
			. '<button class="bkn-fbtn" type="button" data-bkn-sheet aria-expanded="false" aria-controls="bkn-fields">Filters' . ( $nset ? ' · ' . $nset : '' ) . '</button>'
			. '<div class="bkn-fields" id="bkn-fields" data-bkn-fields><div class="bkn-sheet-head"><b>Filters</b><button type="button" class="bkn-x" data-bkn-close aria-label="Close filters">' . blockke_dev_icon( 'close' ) . '</button></div>'
			. $sel( 'area', 'Area', $aopt, $f['area'] )
			. $sel( 'beds', 'Bedrooms', array( '' => 'Any bedrooms', 'studio' => 'Studio', '1' => '1 bed', '2' => '2 bed', '3' => '3 bed', '4' => '4+ bed' ), $f['beds'] )
			. $sel( 'price', 'Price', $bopt, $f['price'] )
			. $sel( 'completion', 'Completion', array( '' => 'Any completion' ) + $years, $f['completion'] )
			. '<div class="bkn-sheet-foot"><button class="bkd-btn bkd-btn-navy bkd-btn-block" type="submit" data-bkn-show>Show developments</button></div></div>'
			. '<p class="bkn-count" data-bkn-count aria-live="polite"' . ( $set ? '>' . $e( $found . ( 1 === $found ? ' development' : ' developments' ) ) : ' hidden>' ) . '</p>'
			. '<a class="bkn-clear" href="' . esc_url( $url ) . '" data-bkn-clear' . ( $set || '' !== $f['sort'] ? '' : ' hidden' ) . '>Clear filters</a>'
			. $sel( 'sort', 'Sort', array( '' => 'Recommended', 'soonest' => 'Completion, soonest', 'price-asc' => 'Price, lowest first', 'price-desc' => 'Price, highest first', 'newest' => 'Newest on Block' ), $f['sort'] )
			. '<noscript><button class="bkn-apply" type="submit">Apply</button></noscript></div></form>';

		// Lead and picks: the unfiltered, recommended view only
		if ( ! $set && ! $flat && '' !== $x['lead'] && isset( $grid[ $x['lead'] ] ) ) {
			$o[] = '<section class="bkn-top" aria-label="Featured developments"><div class="bkd-wrap">' . blockke_card_html( $grid[ $x['lead'] ], 'lead', array( 'src' => 'index', 'pos' => 1 ) );
			if ( 2 === count( array_intersect( $x['picks'], array_keys( $grid ) ) ) ) { // a pair, or nothing: one pick alone looks like a gap
				$o[] = '<div class="bkn-picks">';
				foreach ( $x['picks'] as $i => $k ) {
					if ( isset( $grid[ $k ] ) ) {
						$o[] = blockke_card_html( $grid[ $k ], 'dev', array( 'src' => 'index', 'pos' => $i + 2, 'sizes' => '(min-width:1000px) 584px, (min-width:768px) 50vw, 100vw' ) );
					}
				}
				$o[] = '</div>';
			}
			$o[] = '</div></section>';
		}

		// The grid: completion years, or one flat grid for a sort or "ready now". Each card is printed once: in the flat grid
		// when the flat view shows it, else in its year (or in Ready); the script moves cards between them in place.
		$labels = array();
		foreach ( $years as $k => $l ) {
			if ( 'ready-now' !== $k ) {
				$labels[ $k ] = $l;
			}
		}
		$labels['tbc'] = '';
		$byg           = array_fill_keys( array_keys( $labels ), array() );
		foreach ( $grid as $k => $c ) {
			$byg[ blockke_index_group( $c ) ][ $k ] = $c;
		}
		$pos   = 10;
		$page  = (int) $cfg['index_page_size'];
		$range = '' !== $f['price']; // with a price filter, cards show their price range ("KES 6.2M–19.8M")
		$card  = function ( $k, $c, $hide, $ctx = array() ) use ( $x, &$pos, $range ) {
			$h = blockke_card_html( $c, 'dev', array_merge( array( 'src' => 'index', 'pos' => $pos++, 'range' => $range, 'tag' => in_array( $k, $x['new'], true ) ), $ctx ) );
			return $hide ? preg_replace( '/^<article /', '<article hidden ', $h ) : $h;
		};
		$o[] = '<div class="bkn-body" data-bkn-body data-size="' . $size . '" data-page="' . $page . '">';
		$o[] = '<section class="bkn-flat" data-bkn-flat' . ( $flat ? '' : ' hidden' ) . ' aria-label="Developments"><div class="bkd-wrap"><div class="bkn-grid" data-bkn-flatgrid>';
		$i   = 0;
		foreach ( $fl as $k => $c ) {
			$o[] = $card( $k, $c, $i++ >= $f['n'] );
		}
		$left = count( $fl ) - $f['n']; // kept in the page for the script, shown only when more remain; paging stops at 240
		$o[]  = '</div><a class="bkn-more" href="' . esc_url( add_query_arg( array_filter( array( 'area' => $f['area'], 'beds' => $f['beds'], 'price' => $f['price'], 'completion' => $f['completion'], 'sort' => $f['sort'], 'n' => min( 240, $f['n'] + $page ) ) ), $url ) ) . '" rel="nofollow" data-bkn-flatmore' . ( $left > 0 && $f['n'] < 240 ? '' : ' hidden' ) . '>'
			. $e( 'Show ' . min( $page, max( 0, $left ) ) . ' more' ) . '</a></div></section>';
		foreach ( $labels as $g => $l ) {
			$cards = $byg[ $g ];
			if ( ! $cards ) {
				continue;
			}
			$m    = array_intersect_key( $cards, $match );
			$open = in_array( (string) $g, $f['open'], true );
			$fr   = array();
			foreach ( $m as $c ) {
				$fr[] = $c['from'];
			}
			$head = 'tbc' === $g
				? '<div class="bkn-gh"><h2 class="bkn-gy bkn-gy-tbc">Completion date to be confirmed</h2><p class="bkn-gn" data-bkn-gn>' . $e( count( $m ) . ( 1 === count( $m ) ? ' development whose' : ' developments whose' ) . ' published date has passed or was never given.' ) . '</p></div>'
				: '<div class="bkn-gh"><h2 class="bkn-gy"><span class="bkn-gk">Completing in</span> ' . $e( $l ) . '</h2><p class="bkn-gn" data-bkn-gn>' . $e( count( $m ) . ( 1 === count( $m ) ? ' development' : ' developments' ) . ( $fr ? ', from ' . blockke_bkn_money( min( $fr ) ) : '' ) ) . '</p></div>';
			$o[]  = '<section class="bkn-group" id="g-' . esc_attr( $g ) . '" data-g="' . esc_attr( $g ) . '"' . ( $m && ! $flat ? '' : ' hidden' ) . '><div class="bkd-wrap"><div class="bkn-grid">' . $head;
			$i    = 0;
			foreach ( $cards as $k => $c ) {
				if ( isset( $fl[ $k ] ) ) {
					continue; // in the flat grid
				}
				$show = isset( $m[ $k ] ) && ( $open || $i < $size );
				if ( isset( $m[ $k ] ) ) {
					$i++;
				}
				$o[] = $card( $k, $c, ! $show );
			}
			$rest = count( $m ) - $size;
			$opn  = implode( ',', array_values( array_intersect( array_map( 'strval', array_keys( $labels ) ), array_merge( $f['open'], array( (string) $g ) ) ) ) ); // page order, so each set of open years has one address
			$o[]  = '</div><a class="bkn-more" href="' . esc_url( add_query_arg( array_filter( array( 'area' => $f['area'], 'beds' => $f['beds'], 'price' => $f['price'], 'completion' => $f['completion'], 'open' => $opn ) ), $url ) . '#g-' . $g ) . '" rel="nofollow" data-bkn-more="' . esc_attr( $g ) . '"' . ( $rest > 0 && ! $open ? '' : ' hidden' ) . '>'
				. $e( 'Show ' . max( 0, $rest ) . ' more ' . ( 'tbc' === $g ? 'without a confirmed date' : 'completing in ' . $l ) ) . '</a></div></section>';
		}
		// Ready to move into: the first index_ready_max of the Ready set; the rest are in the page, hidden, for "Ready now"
		$rmax = max( 1, (int) $cfg['index_ready_max'] );
		$all  = get_term_link( 'ready-now', 'property_completion' );
		$o[]  = '<section class="bkn-ready" data-bkn-ready data-max="' . $rmax . '"' . ( $set ? ' hidden' : '' ) . '><div class="bkd-wrap"><h2 class="bkn-h2">Ready to move into</h2><p class="bkn-sub">Completed buildings with homes for sale.</p><div class="bkn-grid bkn-grid-4">';
		$i    = 0;
		foreach ( $x['ready'] as $k => $c ) {
			if ( ! isset( $fl[ $k ] ) ) {
				$o[] = $card( $k, $c, $i >= $rmax, array( 'tag' => false, 'sizes' => '(min-width:1000px) 279px, (min-width:768px) calc(50vw - 52px), calc(100vw - 40px)' ) );
			}
			$i++;
		}
		$o[] = '</div>' . ( is_wp_error( $all ) ? '' : '<a class="bkn-link" href="' . esc_url( $all ) . '">See all ready homes</a>' ) . '</div></section>';
		$o[] = '</div>';

		// Nothing (or almost nothing) matches: say so, and offer the nearest ways out
		$none = $set && ! $found;
		$o[]  = '<section class="bkn-none" data-bkn-none' . ( $none ? '' : ' hidden' ) . '><div class="bkd-wrap"><p class="bkn-none-t" data-bkn-none-t>' . ( $none ? $e( 'No ' . blockke_index_phrase( $f, $areas ) . '.' ) : '' ) . '</p><ul class="bkn-relax" data-bkn-relax></ul>'
			. '<p><a class="bkn-link" href="' . esc_url( $wa ) . '" target="_blank" rel="noopener" data-bkd-wa data-bkn-wa>Send this search to an advisor</a></p></div></section></div>'; // the bar stays in view only over the listings

		// The one navy band: ask for a shortlist
		$adv = $cfg['index_advisor'];
		$who = ! empty( $adv['name'] ) && ! empty( $adv['line'] ) ? '<div class="bkn-adv">' . ( ! empty( $adv['photo'] ) ? wp_get_attachment_image( (int) $adv['photo'], 'thumbnail', false, array( 'alt' => '', 'loading' => 'lazy' ) ) : '' ) . '<p><b>' . $e( $adv['name'] ) . '</b><span>' . $e( $adv['line'] ) . '</span></p></div>' : '';
		$tel = ! empty( $adv['phone'] ) ? blockke_dev_phone( $adv['phone'] ) : '';
		$o[] = '<section class="bkn-ask" id="bkd-enquire"><div class="bkd-wrap bkn-ask-in"><div class="bkn-ask-t"><h2 class="bkn-h2">Ask for a shortlist</h2>'
			. '<p>Send your budget, bedrooms and move-in year. An advisor replies with developments that fit and their current price lists.</p>' . $who
			. '<p class="bkn-ask-direct"><a href="' . esc_url( $wa ) . '" target="_blank" rel="noopener" data-bkd-wa>' . blockke_dev_icon( 'wa', 'bkd-wa-ic' ) . 'WhatsApp an advisor</a>'
			. '<a href="tel:' . esc_attr( $tel ? '+' . $tel : $ag['phone'] ) . '">Call ' . $e( $tel ? blockke_dev_phone_display( $tel ) : $ag['phone_display'] ) . '</a></p></div>'
			. '<div class="bkn-ask-f">' . blockke_index_form() . '</div></div></section>';

		// A-Z: every development we list, by neighbourhood, busiest first. An area with one development, and developments
		// without an area, go together under "Other areas" at the end, each name followed by its area.
		$byarea = array();
		$single = array();
		foreach ( $x['az'] as $c ) {
			$byarea[ '' !== $c['area'] ? $c['area'] : 'other' ][] = $c;
		}
		foreach ( $byarea as $slug => $list ) {
			if ( 'other' === $slug || 1 === count( $list ) ) {
				$single = array_merge( $single, $list );
				unset( $byarea[ $slug ] );
			}
		}
		uksort(
			$byarea,
			function ( $a, $b ) use ( $byarea ) {
				return count( $byarea[ $b ] ) - count( $byarea[ $a ] ) ? count( $byarea[ $b ] ) - count( $byarea[ $a ] ) : strcmp( $a, $b );
			}
		);
		if ( $single ) {
			$byarea['other'] = $single;
		}
		$o[] = '<section class="bkn-az"><div class="bkd-wrap"><h2 class="bkn-h2">Every development we list, by neighbourhood</h2><div class="bkn-az-cols">';
		foreach ( $byarea as $slug => $list ) {
			usort(
				$list,
				function ( $a, $b ) {
					return strcasecmp( $a['name'], $b['name'] );
				}
			);
			$t    = 'other' === $slug ? null : get_term_by( 'slug', $slug, 'property_area' );
			$nm   = $t ? blockke_dev_text( $t->name ) : 'Other areas';
			$o[]  = '<details class="bkn-azg"><summary>' . $e( $nm ) . ' <span>(' . count( $list ) . ')</span></summary><ul>';
			foreach ( $list as $c ) {
				$p   = blockke_bkn_price( $c['from'], $c['to'] );
				$in  = ! $t && '' !== $c['area'] ? get_term_by( 'slug', $c['area'], 'property_area' ) : null;
				$o[] = '<li><a href="' . esc_url( $c['url'] ) . '" data-bkn="index">' . $e( $c['name'] . ( $in ? ', ' . blockke_dev_text( $in->name ) : '' ) ) . '</a>' . ( '' !== $p ? '<span>' . $e( $p ) . '</span>' : '' ) . '</li>';
			}
			if ( $t ) {
				$to  = function_exists( 'blockke_area_on' ) && blockke_area_on( $t ) ? blockke_area_url( $t, 'homes-for-sale' ) : get_term_link( $t );
				$o[] = '<li class="bkn-azall"><a href="' . esc_url( is_wp_error( $to ) ? '' : $to ) . '">' . $e( 'All homes for sale in ' . $nm ) . '</a></li>';
			}
			$o[] = '</ul></details>';
		}
		$o[]   = '</div>';
		$guide = get_post( 35028 );
		$more  = array();
		if ( $guide && 'publish' === $guide->post_status && 'page' === $guide->post_type ) {
			$more[] = '<a href="' . esc_url( get_permalink( $guide ) ) . '">Off-plan guides by corridor</a>';
		}
		foreach ( array( 'commercial' => array( 'commercial development', 'commercial developments' ), 'land' => array( 'land project', 'land projects' ) ) as $kind => $w ) {
			$tl = get_term_link( $kind, 'property_category' );
			if ( ! empty( $x['other'][ $kind ] ) && ! is_wp_error( $tl ) ) {
				$more[] = '<a href="' . esc_url( $tl ) . '">' . $e( $x['other'][ $kind ] . ' ' . ( 1 === $x['other'][ $kind ] ? $w[0] : $w[1] ) ) . '</a>';
			}
		}
		$o[] = ( $more ? '<p class="bkn-also">' . implode( '<span aria-hidden="true"> · </span>', $more ) . '</p>' : '' ) . '<p class="bkn-disc">' . $e( $cfg['area_disclaimer'] ) . '</p></div></section>';

		// Mobile contact bar and the enquiry dialog (the development layout's own)
		$o[] = '<nav class="bkd-mb" id="bkd-mb" aria-label="Quick contact"><a class="bkd-mb-wa" href="' . esc_url( $wa ) . '" target="_blank" rel="noopener" data-bkd-wa aria-label="WhatsApp">' . blockke_dev_icon( 'wa', 'bkd-wa-ic' ) . '</a>'
			. '<a class="bkd-btn bkd-btn-navy" href="#bkd-enquire">Ask for a shortlist</a></nav>';
		$o[] = '</div>';
		return implode( '', $o );
	}

	/** The shortlist form: name, phone, budget, message; posted to the development-lead endpoint as an index enquiry. */
	function blockke_index_form() {
		$cfg = blockke_dev_config();
		$h   = '<form class="bkd-form" data-bkd-lead="dev_index" novalidate><input class="bkd-hp" type="text" name="bkd_hp" tabindex="-1" autocomplete="off" aria-hidden="true">';
		$h  .= '<div class="bkd-two">' . blockke_dev_field( 'name', 'Full name', 'text', 'name' ) . blockke_dev_field( 'phone', 'Phone / WhatsApp', 'tel', 'tel', '07XX XXX XXX' ) . '</div>';
		$h  .= '<label class="bkd-f"><span>Budget <small>(optional)</small></span><select name="budget"><option value="">Not sure yet</option>';
		foreach ( isset( $cfg['budget_bands']['sale'] ) ? (array) $cfg['budget_bands']['sale'] : array() as $b ) {
			// the filter's words ("KES 8–12M"), so the email reads like the search the visitor made
			$h .= '<option>' . esc_html( function_exists( 'blockke_search_band_label' ) ? blockke_search_band_label( (float) $b[0], (float) $b[1], false ) : $b[2] ) . '</option>';
		}
		$h .= '</select></label><label class="bkd-f"><span>Message <small>(optional)</small></span><textarea name="message" placeholder="Bedrooms, areas, when you would like to move in."></textarea></label>';
		$h .= '<button class="bkd-btn bkd-btn-navy bkd-btn-block" type="submit">Send</button>';
		$privacy = blockke_dev_privacy_url();
		$h .= '<div class="bkd-consent">We\'ll reply by WhatsApp or phone. By sending this, you agree to be contacted about new developments.' . ( $privacy ? ' <a href="' . esc_url( $privacy ) . '" target="_blank" rel="noopener">Privacy policy</a>' : '' ) . '</div></form>' . blockke_dev_ok();
		return $h;
	}

	/** Rows the script filters on: one per grid and Ready card, in the page's order, plus the filter labels. */
	function blockke_index_json() {
		$x    = blockke_index_data();
		$rows = array();
		foreach ( array( 'grid', 'ready' ) as $set ) { // grid rows carry their completion group, Ready rows the group 'ready'
			foreach ( $x[ $set ] as $k => $c ) {
				$rows[] = array( (int) $c['id'], $c['areas'], $c['area'], $c['beds'], (int) $c['from'], (int) ( $c['to'] > 0 ? $c['to'] : $c['from'] ), 'ready' === $set ? 'ready' : blockke_index_group( $c ), blockke_bkn_done_key( $c ), (int) $c['d'], $c['ft'] ? 1 : 0, $c['vt'] ? 1 : 0, (int) $c['photos'] );
			}
		}
		$bands = array();
		foreach ( blockke_index_bands() as $k => $b ) {
			$bands[ $k ] = array( (float) $b[0], (float) $b[1], $b[2] );
		}
		return array(
			'rows'  => $rows,
			'bands' => $bands,
			'areas' => blockke_index_areas( $x['grid'] ),
			'years' => blockke_index_years(),
		);
	}

	/** Index styles, on top of the development layout's base and the card family. */
	function blockke_index_css() {
		return <<<'CSS'
#bkd.bkn{--sec:96px}
@media (max-width:999px){#bkd.bkn{--sec:72px}}
@media (max-width:639px){#bkd.bkn{--sec:48px}}
#bkd.bkn em{font-style:normal}
#bkd .bkn-head{padding:40px 0 48px}
#bkd .bkn-crumbs{display:flex;flex-wrap:wrap;gap:8px;font-size:13px;line-height:1.5;color:var(--muted)}
#bkd .bkn-crumbs a{display:inline-block;padding:13px 0;margin:-13px 0;color:var(--slate)!important;text-decoration:none}
#bkd .bkn-crumbs a:hover{text-decoration:underline;text-underline-offset:3px}
#bkd :is(.bkn-h1,.bkn-h2,.bkn-gy,.bkn-none-t){font-family:var(--display)!important;font-weight:var(--display-w)!important;letter-spacing:var(--display-ls)!important;text-transform:none!important}
#bkd .bkn-h1{margin:24px 0 0;max-width:20ch;font-size:64px!important;line-height:1.05!important;color:var(--navy)!important}
#bkd .bkn-stand{margin:20px 0 0;max-width:52ch;font-size:18px;line-height:1.6;color:var(--ink)}
#bkd .bkn-ask-top{margin:16px 0 0;font-size:15px}
#bkd .bkn-ask-top a,#bkd .bkn-none .bkn-link,#bkd .bkn-also a{display:inline-block;padding:11px 0;margin:-11px 0}
#bkd .bkn-ask-top a,#bkd .bkn-link,#bkd .bkn-more,#bkd .bkn-also a{color:var(--navy)!important;font-weight:500;text-decoration:underline;text-decoration-thickness:1px;text-underline-offset:4px}
#bkd .bkn-link:hover,#bkd .bkn-more:hover,#bkd .bkn-also a:hover{text-decoration-thickness:2px}
#bkd .bkn-h2{margin:0;font-size:40px!important;line-height:1.1!important;color:var(--navy)!important}
#bkd .bkn-sub{margin:10px 0 0;font-size:16px;color:var(--slate)}
#bkd .bkn-top{padding:48px 0 0}
#bkd .bkn-top[hidden]{display:none}
@media (max-width:767px){#bkd .bkn-top{padding-top:0}}
#bkd .bkn-lead-card .bkn-name{font-size:48px!important;line-height:1.05!important;margin-top:20px}
#bkd .bkn-lead-card .bkn-place{margin-top:10px;font-size:15px}
#bkd .bkn-lead-card .bkn-data{margin-top:4px;font-size:16px}
#bkd .bkn-picks{display:none}
@media (min-width:768px){#bkd .bkn-picks{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:32px;margin-top:56px}}
@media (min-width:1000px){
#bkd .bkn-lead-card{display:grid;grid-template-columns:minmax(0,7fr) minmax(0,5fr);grid-template-rows:1fr auto auto auto auto;column-gap:48px}
#bkd .bkn-lead-card .bkn-a{display:contents}
#bkd .bkn-lead-card .bkn-a::after{display:none}
#bkd .bkn-lead-card .bkn-img{grid-column:1;grid-row:1/-1}
#bkd .bkn-lead-card .bkn-name{grid-column:2;grid-row:2}#bkd .bkn-lead-card .bkn-place{grid-column:2;grid-row:3}#bkd .bkn-lead-card .bkn-data{grid-column:2;grid-row:4}#bkd .bkn-lead-card .bkn-line{grid-column:2;grid-row:5}
#bkd .bkn-lead-card .bkn-name{margin-top:0}
#bkd .bkn-lead-card > :last-child{margin-bottom:6px}
}
@media (max-width:767px){#bkd .bkn-lead-card .bkn-img{margin:0 calc(var(--gutter) * -1)}#bkd .bkn-lead-card .bkn-name{font-size:34px!important}}
@media (min-width:768px) and (max-width:999px){#bkd .bkn-lead-card .bkn-name{font-size:40px!important}}
#bkd .bkn-bar{position:sticky;top:var(--top);z-index:30;margin:0;background:var(--paper);border-top:1px solid var(--line);border-bottom:1px solid var(--line)}
#bkd .bkn-bar.bkn-up{z-index:100050}
#bkd .bkn-bar-in{display:flex;align-items:center;gap:8px 28px;min-height:60px}
#bkd .bkn-fields{display:flex;align-items:center;gap:8px 28px;flex-wrap:wrap}
#bkd .bkn-f{position:relative;display:block;margin:0}
#bkd .bkn-bar select{-webkit-appearance:none;appearance:none;height:44px;margin:0;padding:0 22px 0 0;border:0!important;border-bottom:1px solid var(--line-2)!important;border-radius:0!important;background:transparent url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath d='M1 1.5l5 5 5-5' fill='none' stroke='%230D2440' stroke-width='1.5'/%3E%3C/svg%3E") no-repeat right center!important;box-shadow:none!important;font:500 15px/1 var(--font)!important;color:var(--navy)!important;cursor:pointer}
#bkd .bkn-bar select:focus-visible{outline:2px solid var(--brass-d);outline-offset:3px}
#bkd .bkn-bar select.on{border-bottom:2px solid var(--brass)!important}
#bkd .bkn-count{margin:0 0 0 auto;font-size:14px;color:var(--slate);white-space:nowrap}
#bkd .bkn-count:not([hidden])+.bkn-clear{margin-left:0}
#bkd .bkn-clear{display:inline-flex;align-items:center;min-height:44px;margin-left:auto;font-size:14px;color:var(--navy)!important;text-decoration:underline;text-underline-offset:4px;white-space:nowrap}
#bkd .bkn-bar-in>.bkn-f:last-of-type{margin-left:0}
#bkd .bkn-count[hidden]+.bkn-clear[hidden]+.bkn-f{margin-left:auto}
#bkd .bkn-fbtn,#bkd .bkn-sheet-head,#bkd .bkn-sheet-foot{display:none}
#bkd .bkn-apply{height:40px;padding:0 16px;border:1px solid var(--navy);background:none;font:500 14px var(--font);color:var(--navy)}
@media (max-width:999px){
#bkd .bkn-bar-in{gap:8px 16px;min-height:52px}
#bkd .bkn-fbtn{display:inline-flex;align-items:center;height:44px;padding:0;border:0;border-bottom:1px solid var(--line-2);background:none;font:600 15px/1 var(--font);color:var(--navy);cursor:pointer}
#bkd .bkn-fields{display:none}
#bkd .bkn-fields.on{position:fixed;inset:0;z-index:100000;display:flex;flex-direction:column;align-items:stretch;gap:20px;padding:20px var(--gutter) calc(20px + env(safe-area-inset-bottom));background:var(--paper);overflow:auto}
#bkd .bkn-fields.on .bkn-sheet-head{display:flex;align-items:center;justify-content:space-between;font:600 17px var(--font);color:var(--navy)}
#bkd .bkn-fields.on .bkn-sheet-foot{display:block;margin-top:auto}
#bkd .bkn-fields.on select{width:100%;height:52px;font-size:16px!important}
#bkd .bkn-x{display:grid;place-items:center;width:44px;height:44px;border:0;background:none;color:var(--navy);cursor:pointer}
#bkd .bkn-x .bkd-i{width:20px;height:20px}
#bkd .bkn-count{display:none}
#bkd .bkn-clear{display:none}
#bkd .bkn-bar-in>.bkn-f{margin-left:auto}
}
#bkd .bkn-body{padding-bottom:var(--sec)}
#bkd .bkn-group{padding-top:56px}
#bkd .bkn-group+.bkn-group{content-visibility:auto;contain-intrinsic-size:auto 900px}
#bkd .bkn-grid{display:grid;grid-template-columns:minmax(0,1fr);gap:40px}
@media (min-width:768px){#bkd .bkn-grid{grid-template-columns:repeat(2,minmax(0,1fr));gap:48px 24px}#bkd .bkn-gh{grid-column:1/-1}}
@media (min-width:1000px){#bkd .bkn-grid{grid-template-columns:repeat(3,minmax(0,1fr));gap:56px 32px}#bkd .bkn-gh{grid-column:auto}#bkd .bkn-grid-4{grid-template-columns:repeat(4,minmax(0,1fr))}#bkd .bkn-grid-4 .bkn-name{font-size:22px}}
#bkd .bkn-gh{padding-top:2px}
#bkd .bkn-gy{margin:0;font-size:72px!important;line-height:.95!important;letter-spacing:-.02em!important;color:var(--navy)!important}
#bkd .bkn-gy-tbc{font-size:40px!important;line-height:1.1!important}
#bkd .bkn-gy .bkn-gk{display:block;margin:0 0 6px;font:400 14px/1.5 var(--font);letter-spacing:normal;color:var(--slate)}
#bkd .bkn-gn{margin:14px 0 0;max-width:30ch;font-size:14px;line-height:1.5;color:var(--ink)}
@media (max-width:999px){#bkd .bkn-gy{font-size:56px!important}#bkd .bkn-h1{font-size:52px!important}#bkd .bkn-h2{font-size:34px!important}}
@media (max-width:639px){#bkd .bkn-gy{font-size:44px!important}#bkd .bkn-h1{font-size:40px!important}#bkd .bkn-h2{font-size:30px!important}#bkd .bkn-stand{font-size:16px}#bkd .bkn-name{font-size:24px!important}#bkd .bkn-head{padding:24px 0 32px}#bkd .bkn-group{padding-top:48px}}
#bkd .bkn-more{display:inline-flex;align-items:center;min-height:44px;margin-top:22px;font-size:15px}
#bkd .bkn-flat{padding-top:48px}
#bkd .bkn-ready{padding-top:var(--sec)}
#bkd .bkn-ready .bkn-grid{margin-top:32px}
#bkd .bkn-ready .bkn-link{display:inline-flex;align-items:center;min-height:44px;margin-top:22px;font-size:15px}
#bkd .bkn-none{padding:56px 0 var(--sec)}
#bkd .bkn-none-t{margin:0;max-width:40ch;font-size:32px!important;line-height:1.2!important;color:var(--navy)!important}
#bkd .bkn-relax{margin:20px 0 24px}
#bkd .bkn-relax li{margin:0 0 8px}
#bkd .bkn-relax button{padding:0;border:0;background:none;font:500 15px var(--font);color:var(--navy);text-decoration:underline;text-underline-offset:4px;cursor:pointer;min-height:44px}
#bkd .bkn-ask{padding:var(--sec) 0;background:var(--navy);color:var(--on-navy)}
#bkd .bkn-ask .bkn-h2{color:#fff!important}
#bkd .bkn-ask-t,#bkd .bkn-ask-t p{color:var(--on-navy)!important}
#bkd .bkn-ask-in{display:grid;gap:40px}
@media (min-width:1000px){#bkd .bkn-ask-in{grid-template-columns:minmax(0,5fr) minmax(0,6fr);column-gap:80px;align-items:start}}
#bkd .bkn-ask-t>p{margin:16px 0 0;max-width:44ch;font-size:17px;line-height:1.6}
#bkd .bkn-ask-direct{display:flex;flex-wrap:wrap;gap:8px 28px;margin-top:28px!important}
#bkd .bkn-ask-direct a{display:inline-flex;align-items:center;gap:10px;min-height:44px;font-size:15px;font-weight:500;color:#fff!important;text-decoration:underline;text-underline-offset:4px}
#bkd .bkn-ask-direct .bkd-i{width:20px;height:20px}
#bkd .bkn-adv{display:flex;align-items:center;gap:16px;margin-top:28px}
#bkd .bkn-adv img{width:64px;height:64px;object-fit:cover}
#bkd .bkn-adv b{display:block;font-weight:600;color:#fff!important}
#bkd .bkn-adv span{display:block;font-size:14px}
#bkd .bkn-ask-f{padding:32px;background:#fff;color:var(--ink)}
@media (max-width:639px){#bkd[data-bkn-page="index"] .bkn-ask{padding-bottom:0}#bkd .bkn-ask-f{margin:0 calc(var(--gutter) * -1);padding:24px var(--gutter)}}
#bkd .bkn-az{padding:var(--sec) 0}
#bkd .bkn-az-cols{display:grid;grid-template-columns:minmax(0,1fr);gap:0 40px;align-items:start;margin-top:32px;border-top:1px solid var(--line)}
@media (min-width:768px){#bkd .bkn-az-cols{grid-template-columns:repeat(2,minmax(0,1fr))}}
@media (min-width:1000px){#bkd .bkn-az-cols{grid-template-columns:repeat(3,minmax(0,1fr))}}
#bkd .bkn-azg{border-bottom:1px solid var(--line)}
#bkd .bkn-azg summary{display:flex;align-items:center;justify-content:space-between;min-height:52px;cursor:pointer;list-style:none;font-size:16px;font-weight:600;color:var(--navy)}
#bkd .bkn-azg summary::-webkit-details-marker{display:none}
#bkd .bkn-azg summary span{font-weight:400;color:var(--muted)}
#bkd .bkn-azg[open]{grid-column:1/-1}
#bkd .bkn-azg ul{padding:0 0 16px}
#bkd .bkn-azg[open] ul{columns:3 240px;column-gap:40px}
#bkd .bkn-azg li{break-inside:avoid}
#bkd .bkn-azg li{display:flex;justify-content:space-between;gap:12px;padding:6px 0;font-size:14px;line-height:1.4}
#bkd .bkn-azg li a{color:var(--ink)!important;text-decoration:none}
#bkd .bkn-azg li a:hover{text-decoration:underline;text-underline-offset:3px}
#bkd .bkn-azg li span{flex:none;color:var(--muted);font-variant-numeric:tabular-nums}
#bkd .bkn-azall a{color:var(--navy)!important;font-weight:500;text-decoration:underline!important;text-underline-offset:4px}
@media (pointer:coarse){#bkd .bkn-azg li{align-items:center;padding:0}#bkd .bkn-azg li a{display:block;min-height:44px;padding:12px 0;box-sizing:border-box}}
#bkd .bkn-also{margin:32px 0 0;font-size:15px}
#bkd .bkn-disc{margin:32px 0 0;max-width:90ch;font-size:13px;line-height:1.6;color:var(--muted)}
CSS;
	}

	/** Index behaviour: filters and sort in place, "Show more", the phone sheet, and what visitors use. */
	function blockke_index_js() {
		return <<<'JS'
(function(){
  'use strict';
  var root=document.getElementById('bkd');if(!root||root.getAttribute('data-bkn-page')!=='index'){return;}
  var dl=(window.dataLayer=window.dataLayer||[]),C=window.BKDEV||{};
  function track(ev,o){var p={page_type:'dev_index'};for(var k in o){p[k]=o[k];}try{if(typeof window.gtag==='function'){window.gtag('event',ev,p);}else{p.event=ev;dl.push(p);}}catch(e){}}
  var D;try{D=JSON.parse(document.getElementById('bkn-data').textContent);}catch(e){return;}
  var R={};D.rows.forEach(function(r){R[r[0]]={areas:r[1],area:r[2],beds:r[3],from:r[4],to:r[5],g:r[6],done:r[7],d:r[8]};});
  var form=root.querySelector('[data-bkn-bar]'),body=root.querySelector('[data-bkn-body]'),size=+body.getAttribute('data-size')||8,page=+body.getAttribute('data-page')||18;
  var flatSec=root.querySelector('[data-bkn-flat]'),flatGrid=root.querySelector('[data-bkn-flatgrid]'),flatMore=root.querySelector('[data-bkn-flatmore]');
  var groups=[].slice.call(root.querySelectorAll('.bkn-group')),ready=root.querySelector('[data-bkn-ready]'),top=root.querySelector('.bkn-top');
  /* every grid and Ready card in the recommended order (the rows' order), each knowing its own section; Ready cards their place in it */
  var home={},byId={},cards=[],ri=0,rmax=ready?+ready.getAttribute('data-max')||8:8;
  groups.forEach(function(g){home[g.getAttribute('data-g')]=g.querySelector('.bkn-grid');});
  if(ready){home.ready=ready.querySelector('.bkn-grid');}
  [].slice.call(root.querySelectorAll('.bkn-group .bkn-card,.bkn-flat .bkn-card,.bkn-ready .bkn-card')).forEach(function(c){byId[c.getAttribute('data-id')]=c;});
  D.rows.forEach(function(r){var c=byId[r[0]];if(c&&r[6]==='ready'){c._ri=ri;}if(r[6]==='ready'){ri++;}if(c&&home[r[6]]){c._home=home[r[6]];cards.push(c);}});
  var gkeys=groups.map(function(g){return g.getAttribute('data-g');});
  var none=root.querySelector('[data-bkn-none]'),count=root.querySelector('[data-bkn-count]'),clear=root.querySelector('[data-bkn-clear]');
  var fields=root.querySelector('[data-bkn-fields]'),fbtn=root.querySelector('[data-bkn-sheet]'),show=root.querySelector('[data-bkn-show]');
  var KEYS=['area','beds','price','completion','sort','n','open'];
  var q=new URLSearchParams(location.search),want=(q.get('open')||'').split(','),open=gkeys.filter(function(k){return want.indexOf(k)>-1;}),n=Math.max(page,+q.get('n')||page);
  function read(){var f={};['area','beds','price','completion','sort'].forEach(function(k){var s=form.querySelector('[name="'+k+'"]');f[k]=s?s.value:'';});return f;}
  function inAreas(a){return Object.prototype.hasOwnProperty.call(D.areas,a);}
  function match(r,f,skip){
    if(skip!=='area'&&f.area){if(f.area==='other'?inAreas(r.area):r.areas.indexOf(f.area)<0){return false;}}
    if(skip!=='beds'&&f.beds){var b=f.beds==='studio'?0:+f.beds,ok=false;r.beds.forEach(function(x){ok=ok||(b===4?x>=4:x===b);});if(!ok){return false;}}
    if(skip!=='price'&&f.price){var bd=D.bands[f.price];if(!bd||!r.from||r.to<bd[0]||(bd[1]>0&&r.from>=bd[1])){return false;}}
    var w=skip==='completion'?'':f.completion; /* "Ready now" takes the Ready set; any other completion the grid */
    if(w==='ready-now'?r.g!=='ready':(r.g==='ready'||(w&&r.g!==w))){return false;}
    return true;
  }
  function plural(k,w){return k+' '+w+(k===1?'':'s');}
  function money(v){if(v>=1e6){var dp=v>=1e7?1:2,x=Math.ceil(Math.round(v/1e6*Math.pow(10,dp)*1e6)/1e6)/Math.pow(10,dp);return 'KES '+(+x.toFixed(dp))+'M';}return 'KES '+Math.ceil(Math.round(v/1e3*1e6)/1e6)+'K';}
  function phrase(f){var b=f.beds?(f.beds==='studio'?'studio ':(f.beds==='4'?'4+ bed ':f.beds+' bed ')):'',s=b+'developments';
    if(f.area){s+=' in '+(f.area==='other'?'other areas':(D.areas[f.area]||f.area));}
    if(f.price){var l=D.bands[f.price][2];s+=' '+(/^Under/.test(l)?'under '+l.replace(/^Under /,''):(/and above$/.test(l)?'from '+l.replace(/ and above$/,''):'at '+l));}
    if(f.completion){s+=f.completion==='ready-now'?' ready now':' completing in '+(D.years[f.completion]||f.completion);}
    return s;}
  /* the search as a list for the WhatsApp message: "Westlands, 2 bed, KES 8–12M, completing 2027" */
  function said(f){var p=[];if(f.area){p.push(f.area==='other'?'other areas':(D.areas[f.area]||f.area));}
    if(f.beds){p.push(f.beds==='studio'?'studios':(f.beds==='4'?'4+':f.beds)+' bed');}
    if(f.price){p.push(D.bands[f.price][2].replace(/^Under /,'under '));}
    if(f.completion){p.push(f.completion==='ready-now'?'ready now':'completing '+(D.years[f.completion]||f.completion));}
    return p.join(', ');}
  function cmp(s){return function(a,b){var x=R[a.getAttribute('data-id')],y=R[b.getAttribute('data-id')];
    if(s==='soonest'){return x.done-y.done;}if(s==='price-asc'){return x.from-y.from;}if(s==='price-desc'){return y.from-x.from;}return y.d-x.d;};}
  /* with a price filter a card shows its price range ("KES 6.2M–19.8M"), else "From KES 6.2M"; the page arrives drawn for its own filter */
  var priced=null;
  function prices(on){if(priced===on){return;}var first=priced===null;priced=on;
    cards.forEach(function(c){var b=c.querySelector('.bkn-data b'),r=R[c.getAttribute('data-id')],k=on?1:0;if(!b||!(r.to>r.from)){return;}
      if(first){b._t=[];b._t[k]=b.textContent;return;}
      if(!b._t[k]){b._t[k]=on?money(r.from)+'–'+money(r.to).replace(/^\D+/,''):'From '+money(r.from);}b.textContent=b._t[k];});}
  /* cards go back to their own section in the recommended order; in a flat view only those that are not shown there */
  function place(flat){var out=cards.some(function(c){return (!flat||!c._m)&&c.parentNode!==c._home;});
    if(out){cards.forEach(function(c){if(!flat||!c._m){c._home.appendChild(c);}});}}
  function sheetOpen(){return !!fields&&fields.classList.contains('on');}
  /* after a change the results start just under the bar, not above the screen */
  function toResults(){var t=(top&&!top.hidden?top:body).getBoundingClientRect().top-form.getBoundingClientRect().bottom;if(t<0){window.scrollBy(0,t);}}
  function link(o){var u=new URLSearchParams();Object.keys(o).forEach(function(k){if(o[k]){u.set(k,o[k]);}});var s=u.toString();return location.pathname+(s?'?'+s:'');}
  var lastKey='',lastNone='';
  function apply(user,sent){
    var f=read(),set=!!(f.area||f.beds||f.price||f.completion),flat=!!f.sort||f.completion==='ready-now',matched=[],nset=0;
    var base={area:f.area,beds:f.beds,price:f.price,completion:f.completion};
    ['area','beds','price','completion'].forEach(function(k){if(f[k]){nset++;}var s=form.querySelector('[name="'+k+'"]');if(s){s.classList.toggle('on',!!f[k]);}});
    var so=form.querySelector('[name="sort"]');if(so){so.classList.toggle('on',!!f.sort);}
    cards.forEach(function(c){c._m=match(R[c.getAttribute('data-id')],f);if(c._m){matched.push(c);}});
    if(top){top.hidden=set||flat;}
    place(flat);
    if(!flat){
      groups.forEach(function(g){var key=g.getAttribute('data-g'),grid=g.querySelector('.bkn-grid'),i=0,fr=[],isOpen=open.indexOf(key)>-1;
        [].slice.call(grid.querySelectorAll('.bkn-card')).forEach(function(c){if(c._m){i++;fr.push(R[c.getAttribute('data-id')].from);}c.hidden=!c._m||(!isOpen&&i>size);});
        g.hidden=i===0;var gn=g.querySelector('[data-bkn-gn]');
        if(gn){gn.textContent=key==='tbc'?plural(i,'development')+' whose published date has passed or was never given.':plural(i,'development')+(fr.length?', from '+money(Math.min.apply(null,fr)):'');}
        var mo=g.querySelector('[data-bkn-more]');if(mo){mo.hidden=isOpen||i<=size;mo.textContent='Show '+(i-size)+' more '+(key==='tbc'?'without a confirmed date':'completing in '+(D.years[key]||key));
          base.open=gkeys.filter(function(k){return k===key||open.indexOf(k)>-1;}).join(',');mo.href=link(base)+'#g-'+key;delete base.open;}});
      flatSec.hidden=true;flatMore.hidden=true;
    }else{
      groups.forEach(function(g){g.hidden=true;});
      if(f.sort){matched.sort(cmp(f.sort));}
      matched.forEach(function(c,i){if(flatGrid.children[i]!==c){flatGrid.insertBefore(c,flatGrid.children[i]||null);}c.hidden=i>=n;});
      flatSec.hidden=false;flatMore.hidden=matched.length<=n;flatMore.textContent='Show '+Math.min(page,matched.length-n)+' more';
      base.sort=f.sort;base.n=Math.min(240,n+page);flatMore.href=link(base);delete base.sort;delete base.n;
    }
    if(ready){ready.hidden=set;cards.forEach(function(c){if(c._ri!==undefined&&c.parentNode===c._home){c.hidden=c._ri>=rmax;}});}
    prices(!!f.price);
    var empty=!matched.length;
    none.hidden=!empty;
    if(empty){
      root.querySelector('[data-bkn-none-t]').textContent='No '+phrase(f)+'.';
      var rl=root.querySelector('[data-bkn-relax]');rl.innerHTML='';
      var opts=[];['completion','beds','price','area'].forEach(function(k){if(!f[k]){return;}var c=0;cards.forEach(function(el){if(match(R[el.getAttribute('data-id')],f,k)){c++;}});
        if(c){opts.push([c,k]);}});
      opts.sort(function(a,b){return b[0]-a[0];}).slice(0,3).forEach(function(o){var li=document.createElement('li'),b=document.createElement('button');b.type='button';
        b.textContent={completion:'Any completion',beds:'Any bedrooms',price:'Any price',area:'Any area'}[o[1]]+' ('+o[0]+')';
        b.addEventListener('click',function(){form.querySelector('[name="'+o[1]+'"]').value='';n=page;apply(true);track('index_relax',{removed:o[1]});toResults();
          var a=root.querySelector('[data-bkn-flat]:not([hidden]) .bkn-card:not([hidden]) a.bkn-a,.bkn-group:not([hidden]) .bkn-card:not([hidden]) a.bkn-a');if(a){a.focus({preventScroll:true});}});
        li.appendChild(b);rl.appendChild(li);});
      var wa=root.querySelector('[data-bkn-wa]');if(wa){wa.href=wa.href.split('?')[0]+'?text='+encodeURIComponent('Hi '+(C.brand||'Block')+', I\'m looking at new developments: '+said(f)+'. Please send current options and price lists. '+location.origin+link(base));}
      var ph=phrase(f);if(user&&ph!==lastNone&&(sent||!sheetOpen())){lastNone=ph;track('index_no_results',{filters:ph});} /* once per search; from the phone sheet only when it is applied */
    }else{lastNone='';}
    count.hidden=!set;count.textContent=plural(matched.length,'development');
    clear.hidden=!set&&!f.sort;
    if(fbtn){fbtn.textContent='Filters'+(nset?' · '+nset:'');}
    if(show){show.textContent='Show '+plural(matched.length,'development');}
    /* the address: the index's own keys first (the shortlist form sends its first 200 characters), then whatever else the
       visitor arrived with, such as campaign tags, untouched */
    var u=new URLSearchParams();['area','beds','price','completion','sort'].forEach(function(k){if(f[k]){u.set(k,f[k]);}});
    if(flat&&n>page){u.set('n',n);}if(!flat&&open.length){u.set('open',open.join(','));}
    var s=u.toString(),x=new URLSearchParams(location.search);KEYS.forEach(function(k){x.delete(k);});
    var all=[s,x.toString()].filter(Boolean).join('&'),url=location.pathname+(all?'?'+all:'')+location.hash;
    if(s!==lastKey){lastKey=s;try{history.replaceState(history.state,'',url);}catch(e){}}
    C.listLabel='new developments';C.ctxBeds=f.beds==='studio'?'studio':f.beds;
  }
  form.addEventListener('change',function(e){var t=e.target;if(!t.name){return;}n=page;
    if(t.name==='sort'){track('index_sort',{sort:t.value||'recommended'});}else{track('index_filter',{filter:t.name,value:t.value||'any'});}apply(true);if(!sheetOpen()){toResults();}});
  form.addEventListener('submit',function(e){e.preventDefault();apply(true,true);closeSheet();toResults();});
  root.addEventListener('click',function(e){
    var m=e.target.closest&&e.target.closest('[data-bkn-more]');
    if(m){e.preventDefault();var key=m.getAttribute('data-bkn-more'),g=m.closest('.bkn-group');if(open.indexOf(key)<0){open.push(key);open=gkeys.filter(function(k){return open.indexOf(k)>-1;});}apply();
      var nx=g?g.querySelectorAll('.bkn-card:not([hidden]) a.bkn-a')[size]:null;if(nx){nx.focus({preventScroll:true});} /* the first card it revealed */
      track('index_more',{group:key,shown:'all'});return;}
    if(e.target.closest&&e.target.closest('[data-bkn-flatmore]')){e.preventDefault();var was=n;n+=page;apply();var nf=flatGrid.querySelectorAll('.bkn-card:not([hidden]) a.bkn-a')[was];if(nf){nf.focus({preventScroll:true});}track('index_more',{group:'flat',shown:n});return;}
    var a=e.target.closest&&e.target.closest('a[data-bkn]');
    if(a){var card=a.closest('.bkn-card,.bkn-row'),sec=a.closest('.bkn-group,.bkn-top,.bkn-ready,.bkn-az,.bkn-flat'),rr=card?R[card.getAttribute('data-id')]:null;
      track('index_card_click',{target_id:card?card.getAttribute('data-id'):'',position:+(a.getAttribute('data-pos')||0),slot:sec?(sec.classList.contains('bkn-top')?(a.closest('.bkn-lead-card')?'lead':'pick'):sec.classList.contains('bkn-ready')||(rr&&rr.g==='ready'&&!sec.classList.contains('bkn-az'))?'ready':sec.classList.contains('bkn-az')?'az':'group'):'',group:sec&&sec.getAttribute('data-g')||'',vt:('onpagereveal' in window)?1:0,transport_type:'beacon'});}
    if(e.target.closest&&e.target.closest('[data-bkd-wa]')){track('index_whatsapp',{filters:phrase(read())});}
  });
  /* the phone and tablet sheet: a dialog above the theme's header, Tab kept inside it; it closes itself if the screen widens */
  function trap(e){if(e.key!=='Tab'||!sheetOpen()){return;}var f=[].slice.call(fields.querySelectorAll('button,select,a[href]')).filter(function(x){return x.offsetParent!==null&&!x.disabled;});if(!f.length){return;}
    if(!fields.contains(document.activeElement)){e.preventDefault();f[0].focus();}else if(e.shiftKey&&document.activeElement===f[0]){e.preventDefault();f[f.length-1].focus();}else if(!e.shiftKey&&document.activeElement===f[f.length-1]){e.preventDefault();f[0].focus();}}
  function openSheet(){form.classList.add('bkn-up');fields.classList.add('on');fields.setAttribute('role','dialog');fields.setAttribute('aria-modal','true');fields.setAttribute('aria-label','Filters');fbtn.setAttribute('aria-expanded','true');document.documentElement.classList.add('bkd-lock');document.addEventListener('keydown',trap);var s=fields.querySelector('select');if(s){s.focus();}}
  function closeSheet(back){if(!sheetOpen()){return;}fields.classList.remove('on');form.classList.remove('bkn-up');['role','aria-modal','aria-label'].forEach(function(k){fields.removeAttribute(k);});fbtn.setAttribute('aria-expanded','false');document.documentElement.classList.remove('bkd-lock');document.removeEventListener('keydown',trap);if(back!==false){fbtn.focus();}}
  if(fbtn){fbtn.addEventListener('click',openSheet);}
  root.addEventListener('click',function(e){if(e.target.closest&&e.target.closest('[data-bkn-close]')){closeSheet();}});
  document.addEventListener('keydown',function(e){if(e.key==='Escape'){closeSheet();}});
  var wide=window.matchMedia?window.matchMedia('(min-width:1000px)'):null,onWide=function(){if(wide.matches){closeSheet(false);}}; /* the Filters button is gone at this width, so focus stays put */
  if(wide){if(wide.addEventListener){wide.addEventListener('change',onWide);}else if(wide.addListener){wide.addListener(onWide);}}
  clear.addEventListener('click',function(e){e.preventDefault();['area','beds','price','completion','sort'].forEach(function(k){var s=form.querySelector('[name="'+k+'"]');if(s){s.value='';}});open=[];n=page;apply(true);track('index_filter',{filter:'clear',value:''});});
  apply(false);
})();
JS;
	}

	/** The index's description for search engines: counts and the lowest price, computed. */
	function blockke_index_seo() {
		$x     = blockke_index_data();
		$froms = array();
		foreach ( $x['grid'] as $c ) {
			$froms[] = $c['from'];
		}
		$cfg = blockke_dev_config();
		return array(
			'title' => 'Off-Plan & New Developments in Nairobi | ' . $cfg['brand_full'],
			'desc'  => blockke_index_where( $x, 'off-plan developments' ) . ( $froms ? ', from ' . blockke_bkn_money( min( $froms ) ) : '' ) . ', with bedrooms, prices and completion dates for each. Independent advice from ' . $cfg['brand'] . '.',
			'url'   => get_permalink( blockke_index_page_id() ),
			'img'   => '' !== $x['lead'] && ! empty( $x['grid'][ $x['lead'] ]['cover'] ) ? (int) $x['grid'][ $x['lead'] ]['cover'] : 0, // og:image: the lead's cover
		);
	}

	/**
	 * Rank Math's graph on the index: our BreadcrumbList in place of its own (same @id, so WebPage.breadcrumb still points
	 * at it), the page as a CollectionPage, no Article node from the page defaults, and our ItemList.
	 */
	function blockke_index_rank_math_graph( $data ) {
		if ( ! is_array( $data ) ) {
			return $data;
		}
		$seo = blockke_index_seo();
		foreach ( $data as $k => $node ) {
			if ( is_array( $node ) && isset( $node['@type'] ) && is_string( $node['@type'] ) && in_array( $node['@type'], array( 'Article', 'BlogPosting', 'NewsArticle' ), true ) ) {
				unset( $data[ $k ] );
			}
		}
		if ( isset( $data['WebPage'] ) && is_array( $data['WebPage'] ) ) {
			$data['WebPage']['@type'] = 'CollectionPage';
		}
		foreach ( blockke_index_jsonld() as $node ) {
			unset( $node['@context'] );
			if ( 'BreadcrumbList' === $node['@type'] ) {
				$id                     = isset( $data['BreadcrumbList']['@id'] ) ? $data['BreadcrumbList']['@id'] : $seo['url'] . '#breadcrumb';
				$data['BreadcrumbList'] = array( '@id' => $id ) + $node;
			} else {
				$data['bknItemList'] = $node;
			}
		}
		return $data;
	}

	/** ItemList (the lead, the picks and the first cards of each year) and BreadcrumbList, without prices. */
	function blockke_index_jsonld() {
		$x     = blockke_index_data();
		$size  = max( 3, (int) blockke_dev_config()['index_group_size'] );
		$take  = array_filter( array_merge( array( $x['lead'] ), $x['picks'] ) );
		$per   = array();
		foreach ( $x['grid'] as $k => $c ) {
			$g = blockke_index_group( $c );
			$per[ $g ] = isset( $per[ $g ] ) ? $per[ $g ] + 1 : 1;
			if ( $per[ $g ] <= $size ) {
				$take[] = $k;
			}
		}
		$items = array();
		foreach ( array_values( array_unique( $take ) ) as $i => $k ) {
			if ( isset( $x['grid'][ $k ] ) ) {
				$items[] = array( '@type' => 'ListItem', 'position' => $i + 1, 'url' => $x['grid'][ $k ]['url'], 'name' => $x['grid'][ $k ]['name'] );
			}
		}
		$seo = blockke_index_seo();
		return array(
			array( '@context' => 'https://schema.org', '@type' => 'ItemList', 'name' => 'New developments in Nairobi', 'url' => $seo['url'], 'numberOfItems' => count( $items ), 'itemListElement' => $items ),
			array( '@context' => 'https://schema.org', '@type' => 'BreadcrumbList', 'itemListElement' => array( array( '@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => home_url( '/' ) ), array( '@type' => 'ListItem', 'position' => 2, 'name' => 'New developments', 'item' => $seo['url'] ) ) ),
		);
	}

	add_filter(
		'template_include',
		function ( $template ) {
			$pid = blockke_index_page_id();
			if ( is_admin() || is_feed() || ! $pid || ! is_page( $pid ) || ! blockke_bkn_on( 'dev_index', 'bke_index' ) || ! function_exists( 'blockke_bkn_devs' ) ) {
				return $template;
			}
			$q   = array_diff( array_keys( $_GET ), array_merge( array( 'bke_index' ), (array) blockke_dev_config()['search_ignore'] ) ); // phpcs:ignore WordPress.Security.NonceVerification
			$idx = ! $q; // any filter, sort or paging in the address: kept out of search results, canonical to the plain page
			$GLOBALS['blockke_dev_active'] = true;
			if ( empty( blockke_dev_config()['price_check_popup'] ) ) {
				$_GET['blockpop'] = '0';
			}
			$seo = blockke_index_seo();
			add_filter(
				'body_class',
				function ( $c ) {
					$c[] = 'bkd-page';
					$c[] = 'bkn-page';
					return $c;
				}
			);
			add_filter( 'rank_math/frontend/disable_adjacent_rel_links', '__return_true' );
			add_filter(
				'pre_get_document_title',
				function () use ( $seo ) {
					return esc_html( $seo['title'] );
				},
				99
			);
			foreach ( array( 'rank_math/frontend/title' => 'title', 'rank_math/opengraph/facebook/og_title' => 'title', 'rank_math/opengraph/twitter/twitter_title' => 'title', 'rank_math/frontend/description' => 'desc', 'rank_math/opengraph/facebook/og_description' => 'desc', 'rank_math/opengraph/twitter/twitter_description' => 'desc', 'rank_math/frontend/canonical' => 'url', 'rank_math/opengraph/facebook/og_url' => 'url' ) as $h => $key ) {
				add_filter(
					$h,
					function () use ( $seo, $key ) {
						return $seo[ $key ];
					},
					99
				);
			}
			add_filter(
				'wp_robots',
				function ( $r ) use ( $idx ) {
					if ( ! $idx ) {
						unset( $r['index'] );
						$r['noindex'] = true;
						$r['follow']  = true;
					}
					return $r;
				},
				99
			);
			add_filter(
				'rank_math/frontend/robots',
				function ( $r ) use ( $idx ) {
					if ( ! $idx ) {
						$r['index']  = 'noindex';
						$r['follow'] = 'follow';
					}
					return $r;
				},
				99
			);
			add_filter( 'rank_math/json_ld', 'blockke_index_rank_math_graph', 99 );
			if ( $seo['img'] ) { // share previews show the lead's cover, not an image Rank Math found in the old page's content
				foreach ( array( 'facebook', 'twitter' ) as $net ) {
					add_action(
						'rank_math/opengraph/' . $net . '/add_additional_images',
						function ( $images ) use ( $seo ) {
							if ( is_object( $images ) && method_exists( $images, 'add_image_by_id' ) ) {
								$images->add_image_by_id( $seo['img'] );
							}
						}
					);
				}
				add_filter( 'rank_math/opengraph/pre_set_content_image', '__return_true' );
			}
			if ( ! defined( 'RANK_MATH_VERSION' ) ) {
				remove_action( 'wp_head', 'rel_canonical' ); // ours below is the one canonical
			}
			add_action(
				'wp_head',
				function () use ( $seo ) {
					if ( ! defined( 'RANK_MATH_VERSION' ) ) {
						$og = $seo['img'] ? wp_get_attachment_image_url( $seo['img'], 'large' ) : '';
						echo '<meta name="description" content="' . esc_attr( $seo['desc'] ) . '">' . "\n" . '<link rel="canonical" href="' . esc_url( $seo['url'] ) . '">' . "\n" . ( $og ? '<meta property="og:image" content="' . esc_url( $og ) . '">' . "\n" : '' );
						foreach ( blockke_index_jsonld() as $node ) {
							echo '<script type="application/ld+json">' . wp_json_encode( $node, JSON_UNESCAPED_SLASHES | JSON_HEX_TAG ) . "</script>\n";
						}
					}
					$font = blockke_dev_font_head( '#bkd' );
					echo $font[0] . '<style id="bkd-css">' . blockke_dev_css() . $font[1] . blockke_bkn_card_css() . blockke_index_css() . "</style>\n"; // phpcs:ignore WordPress.Security.EscapeOutput
				},
				99
			);
			add_action(
				'wp_footer',
				function () {
					$cfg = blockke_dev_config();
					$ag  = blockke_dev_agent( 0 );
					$js  = array(
						'id'        => 0,
						'slug'      => '',
						'name'      => 'New developments',
						'leadName'  => 'new developments', // the message adds " on block.ke" itself
						'title'     => 'New developments in Nairobi',
						'url'       => get_permalink( blockke_index_page_id() ),
						'brand'     => $cfg['brand'],
						'agent'     => '',
						'wa'        => $ag['wa'],
						'endpoint'  => esc_url_raw( rest_url( 'block/v1/development-lead' ) ),
						'gallery'   => array(),
						'lp'        => 0,
						'area'      => '',
						'bands'     => $cfg['budget_bands'],
						'back'      => 0,
						'bedsMode'  => $cfg['beds_mode'],
						'studioCat' => (string) $cfg['studio_cat'],
						'indexPage' => 1,
						'listLabel' => 'new developments',
						'okTail'    => 'with developments that fit and their current price lists',
					);
					echo '<script id="bkn-data" type="application/json">' . wp_json_encode( blockke_index_json(), JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP ) . "</script>\n"; // phpcs:ignore WordPress.Security.EscapeOutput
					echo '<script id="bkd-cfg" data-cfasync="false" data-no-optimize="1" data-no-defer="1">window.BKDEV=' . wp_json_encode( $js, JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS ) . ";</script>\n";
					echo '<script id="bkd-js" data-cfasync="false" data-no-optimize="1" data-no-defer="1">' . blockke_dev_js() . "\n" . blockke_index_js() . "\n" . blockke_bkn_nav_js() . "</script>\n"; // phpcs:ignore WordPress.Security.EscapeOutput
				},
				20
			);
			get_header();
			echo blockke_index_render(); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped while building.
			get_footer();
			$blank = WP_CONTENT_DIR . '/index.php';
			return file_exists( $blank ) ? $blank : '';
		},
		99998
	);

	// The completion chips another snippet adds to this page stand down while the index is on
	foreach ( array( 'option_blockke_completion', 'default_option_blockke_completion' ) as $bkn_hook ) {
		add_filter(
			$bkn_hook,
			function ( $v ) {
				if ( is_array( $v ) && isset( $v['list_pages'] ) && is_array( $v['list_pages'] ) && blockke_bkn_on( 'dev_index', 'bke_index' ) ) {
					$v['list_pages'] = array_values( array_diff( array_map( 'intval', $v['list_pages'] ), array( blockke_index_page_id() ) ) );
				}
				return $v;
			}
		);
	}
	unset( $bkn_hook );

	add_action(
		'admin_bar_menu',
		function ( $bar ) {
			$pid = blockke_index_page_id();
			if ( is_admin() || ! $pid || ! is_page( $pid ) || ! current_user_can( 'edit_posts' ) || ! function_exists( 'blockke_index_data' ) ) {
				return;
			}
			$cfg  = blockke_dev_config();
			$live = ! empty( $cfg['dev_index'] );
			$url  = get_permalink( $pid );
			$x    = blockke_index_data();
			$vt   = 0;
			$late = array();
			foreach ( $x['grid'] as $c ) {
				$vt += $c['vt'] ? 1 : 0;
				if ( $c['done'][4] ) {
					$late[] = $c;
				}
			}
			$bar->add_node( array( 'id' => 'bkn', 'title' => 'New developments: ' . ( $live ? 'on' : 'off' ), 'href' => add_query_arg( 'bke_index', $live ? '0' : '1', $url ) ) );
			$bar->add_node( array( 'parent' => 'bkn', 'id' => 'bkn-on', 'title' => 'Preview the new page', 'href' => add_query_arg( 'bke_index', '1', $url ) ) );
			$bar->add_node( array( 'parent' => 'bkn', 'id' => 'bkn-off', 'title' => 'Preview the classic page', 'href' => add_query_arg( 'bke_index', '0', $url ) ) );
			$bar->add_node( array( 'parent' => 'bkn', 'id' => 'bkn-vt', 'title' => esc_html( $vt . ' of ' . count( $x['grid'] ) . ' open on the new layout' ) ) );
			$bar->add_node( array( 'parent' => 'bkn', 'id' => 'bkn-late', 'title' => esc_html( count( $late ) . ' off-plan ' . ( 1 === count( $late ) ? 'date has' : 'dates have' ) . ' passed' ) ) );
			foreach ( array_slice( $late, 0, 10 ) as $c ) {
				$bar->add_node( array( 'parent' => 'bkn-late', 'id' => 'bkn-late-' . (int) $c['id'], 'title' => esc_html( $c['name'] ), 'href' => get_edit_post_link( $c['id'] ) ) );
			}
			$skip = isset( $x['skip'] ) ? (array) $x['skip'] : array(); // homes for sale with no area and no status: on no list until tagged
			if ( $skip ) {
				$bar->add_node( array( 'parent' => 'bkn', 'id' => 'bkn-skip', 'title' => esc_html( 'Not shown: ' . count( $skip ) . ' (no area or status)' ) ) );
				foreach ( array_slice( $skip, 0, 10, true ) as $id => $name ) {
					$bar->add_node( array( 'parent' => 'bkn-skip', 'id' => 'bkn-skip-' . (int) $id, 'title' => esc_html( $name . ' (' . (int) $id . ')' ), 'href' => get_edit_post_link( $id ) ) );
				}
			}
		},
		91
	);

	/* ---------------------------------------------------------------------
	   Area and segment pages on the v1.6 cards (config card_v16): the same developments, cards and wording as the index
	   --------------------------------------------------------------------- */

	/** An area's segments from the shared rows: same shape as blockke_area_data(), cards are development cards. */
	function blockke_area_v16_data( $term ) {
		static $memo = array();
		$tid = (int) $term->term_id;
		if ( isset( $memo[ $tid ] ) ) {
			return $memo[ $tid ];
		}
		$rows  = blockke_bkn_rows();
		$slugs = array( $term->slug );
		foreach ( (array) get_term_children( $tid, 'property_area' ) as $cid ) {
			$ct = get_term( $cid, 'property_area' );
			if ( $ct instanceof WP_Term ) {
				$slugs[] = $ct->slug;
			}
		}
		$in     = array();
		$cities = array();
		foreach ( $rows as $id => $r ) {
			if ( array_intersect( $slugs, $r['areas'] ) && '' !== $r['act'] ) {
				$in[ $id ] = $r;
				if ( '' !== $r['cy'] ) {
					$cities[ $r['cy'] ] = ( isset( $cities[ $r['cy'] ] ) ? $cities[ $r['cy'] ] : 0 ) + 1;
				}
			}
		}
		$segs = array();
		foreach ( blockke_area_segments() as $slug => $seg ) {
			$ids = array();
			foreach ( $in as $id => $r ) {
				if ( $r['act'] === $seg[1] && $r['kind'] === $seg[2] && ( '' === $seg[3] || $r['type'] === $seg[3] ) ) {
					$ids[] = $id;
				}
			}
			$rent  = 'rent' === $seg[1];
			$cards = array_values( blockke_bkn_group( $ids, $rent ) );
			usort(
				$cards,
				function ( $a, $b ) {
					if ( $a['ft'] !== $b['ft'] ) {
						return $a['ft'] ? -1 : 1;
					}
					if ( (bool) $a['cover'] !== (bool) $b['cover'] ) {
						return $a['cover'] ? -1 : 1;
					}
					return $b['d'] - $a['d'];
				}
			);
			$ps    = array();
			$homes = 0; // what the page counts: each card's homes (a development listed without them counts once)
			foreach ( $cards as $c ) {
				$homes += max( 1, count( $c['match'] ) );
				foreach ( $rent ? array( $c['rfrom'], $c['rto'] ) : array( $c['from'], $c['to'] ) as $v ) {
					if ( $v > 0 ) {
						$ps[] = $v;
					}
				}
			}
			$segs[ $slug ] = array(
				'n'     => count( $cards ),
				'homes' => $homes,
				'cards' => $cards,
				'min'   => $ps ? min( $ps ) : 0,
				'max'   => $ps ? max( $ps ) : 0,
			);
		}
		arsort( $cities );
		$memo[ $tid ] = array(
			'segs' => $segs,
			'city' => $cities ? (string) key( $cities ) : '',
			'v16'  => 1,
		);
		return $memo[ $tid ];
	}

	/** Price bands for a segment page: sale in KES millions ("8-12"), rent in thousands a month ("80-150"). */
	function blockke_area_v16_bands( $rent ) {
		$cfg = blockke_dev_config();
		$div = $rent ? 1000 : 1000000;
		$u   = $rent ? 'K' : 'M';
		$out = array();
		foreach ( isset( $cfg['budget_bands'][ $rent ? 'rent' : 'sale' ] ) ? (array) $cfg['budget_bands'][ $rent ? 'rent' : 'sale' ] : array() as $b ) {
			$lo   = (float) $b[0];
			$hi   = (float) $b[1];
			$n    = function ( $v ) use ( $div ) {
				return rtrim( rtrim( number_format( $v / $div, 1, '.', '' ), '0' ), '.' );
			};
			$m    = $rent ? ' a month' : '';
			$slug = $n( $lo ) . '-' . ( $hi > 0 ? $n( $hi ) : '' );
			// the search bar's words ("KES 8–12M", "KES 60M and above"), so the two bars read the same
			$out[ $slug ] = array( $lo, $hi, function_exists( 'blockke_search_band_label' ) ? blockke_search_band_label( $lo, $hi, $rent ) : ( $lo <= 0 ? 'Under KES ' . $n( $hi ) . $u . $m : ( $hi > 0 ? 'KES ' . $n( $lo ) . '–' . $n( $hi ) . $u . $m : 'KES ' . $n( $lo ) . $u . $m . ' and above' ) ) );
		}
		return $out;
	}

	/** A segment page's filters from the address, checked against what the bar offers. */
	function blockke_area_v16_state( $rent ) {
		$g    = function ( $k ) {
			return isset( $_GET[ $k ] ) && is_scalar( $_GET[ $k ] ) ? sanitize_text_field( wp_unslash( (string) $_GET[ $k ] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
		};
		$page = max( 6, (int) blockke_dev_config()['area_page_size'] );
		return array(
			'beds'       => in_array( $g( 'beds' ), array( 'studio', '1', '2', '3', '4' ), true ) ? $g( 'beds' ) : '',
			'price'      => isset( blockke_area_v16_bands( $rent )[ $g( 'price' ) ] ) ? $g( 'price' ) : '',
			'completion' => ! $rent && isset( blockke_index_years()[ $g( 'completion' ) ] ) ? $g( 'completion' ) : '',
			'sort'       => in_array( $g( 'sort' ), array( 'price-asc', 'price-desc', 'newest', 'soonest' ), true ) ? $g( 'sort' ) : '',
			'n'          => min( 480, max( $page, (int) $g( 'n' ) ) ),
			'page'       => $page,
		);
	}

	/** The same test the segment page's script runs, so a filtered address shows the right cards before any script. */
	function blockke_area_v16_match( $c, $f, $rent ) {
		if ( '' !== $f['beds'] ) {
			$b  = 'studio' === $f['beds'] ? 0 : (int) $f['beds'];
			$ok = false;
			foreach ( $c['beds'] as $x ) {
				$ok = $ok || ( 4 === $b ? $x >= 4 : $x === $b );
			}
			if ( ! $ok ) {
				return false;
			}
		}
		if ( '' !== $f['price'] ) {
			$band = blockke_area_v16_bands( $rent )[ $f['price'] ];
			$fr   = (int) ( $rent ? $c['rfrom'] : $c['from'] );
			$to   = (int) ( $rent ? $c['rto'] : $c['to'] );
			$to   = $to > 0 ? $to : $fr;
			if ( $fr <= 0 || $to < $band[0] || ( $band[1] > 0 && $fr >= $band[1] ) ) {
				return false;
			}
		}
		if ( '' !== $f['completion'] ) {
			$d  = blockke_bkn_done_key( $c );
			$cy = (int) floor( $d / 100 );
			if ( 'ready-now' === $f['completion'] ? 'ready' !== $c['st'] : ( $d >= 999999 || ( false !== strpos( $f['completion'], '-plus' ) ? $cy < (int) $f['completion'] : $cy !== (int) $f['completion'] ) ) ) {
				return false;
			}
		}
		return true;
	}

	/** "253 homes", "1 home"; commercial property and land count "listings". */
	function blockke_area_v16_count( $n, $kind ) {
		$w = 'home' === $kind ? 'home' : 'listing';
		return (int) $n . ' ' . $w . ( 1 === (int) $n ? '' : 's' );
	}

	/** A segment page's filters, mid-sentence: "2 bed homes for sale in Westlands at KES 8–12M completing in 2027". */
	function blockke_area_v16_phrase( $p, $f ) {
		$what = strtolower( $p['label'] );
		if ( 'studio' === $f['beds'] ) {
			$what = preg_match( '/^(homes|studios) /', $what ) ? preg_replace( '/^\w+/', 'studios', $what ) : 'studio ' . $what;
		} elseif ( '' !== $f['beds'] ) {
			$what = ( '4' === $f['beds'] ? '4+' : $f['beds'] ) . ' bed ' . $what;
		}
		$s = $what . ' in ' . $p['area'];
		if ( '' !== $f['price'] ) {
			$l  = blockke_area_v16_bands( 'rent' === $p['action'] )[ $f['price'] ][2];
			$s .= 0 === strpos( $l, 'Under ' ) ? ' under ' . substr( $l, 6 ) : ' at ' . $l;
		}
		if ( '' !== $f['completion'] ) {
			$s .= 'ready-now' === $f['completion'] ? ' ready to move into' : ' completing in ' . blockke_index_years()[ $f['completion'] ];
		}
		return $s;
	}

	/**
	 * What the area page shows of each kind it has (homes for sale, homes for rent...), in page order: 'strip' (the first
	 * kind's leading developments, which head the page, so its grid starts after them), 'grid' (whole rows of three, up to
	 * area_cards), 'rows' (phones: after the first card), 'text' (developments without a photo, by name, once the grid holds
	 * every other one), 'all' (every development of that kind is on the page) and 'phone' (and on a phone's screen). A kind
	 * that is all on the page needs no "See all"; a segment that is all its area has can then point its canonical here.
	 */
	function blockke_area_v16_sections( $data ) {
		$max = max( 3, (int) blockke_dev_config()['area_cards'] );
		$out = array();
		foreach ( blockke_area_segments() as $s => $seg ) {
			if ( '' !== $seg[4] || empty( $data['segs'][ $s ]['n'] ) ) {
				continue;
			}
			$cover = array();
			$text  = array();
			foreach ( $data['segs'][ $s ]['cards'] as $c ) {
				if ( $c['cover'] ) {
					$cover[] = $c;
				} else {
					$text[] = $c;
				}
			}
			$k = count( $cover );
			$t = 0; // the strip: three photos or one, whichever leaves the grid whole rows that show every other one (else three, from 6)
			if ( ! $out ) {
				$t = $k >= 6 ? 3 : ( $k >= 2 ? 1 : 0 );
				foreach ( array( true, false ) as $rows ) {
					foreach ( array( 3, 1 ) as $try ) {
						$left = $k - $try;
						if ( $left >= 1 && $left <= $max && ( $rows ? 0 === $left % 3 : $left < 3 ) ) {
							$t = $try;
							break 2;
						}
					}
				}
			}
			$strip = array_splice( $cover, 0, $t );
			$k     = count( $cover );
			$d     = $k < 3 ? $k : 3 * intdiv( min( $k, $max ), 3 );
			$all   = $d === $k;
			$out[ $s ] = array(
				'strip' => $strip,
				'grid'  => array_slice( $cover, 0, $d ),
				'rows'  => array_slice( $cover, 1, $all ? max( 0, $d - 1 ) : 3 ),
				'text'  => $all ? $text : array(),
				'all'   => $all,
				'phone' => $all || ( $k <= 4 && ! $text ),
			);
		}
		return $out;
	}

	/** The v1.6 area page (no segment) or segment page. */
	function blockke_area_render_v16( $p ) {
		$cfg   = blockke_dev_config();
		$e     = 'esc_html';
		$segs  = blockke_area_segments();
		$data  = $p['data'];
		$term  = $p['term'];
		$area  = $p['area'];
		$stub  = blockke_area_stub( $p );
		$ag    = $stub['agent'];
		$wa    = 'https://wa.me/' . $ag['wa'] . '?text=' . rawurlencode( 'Hi ' . $cfg['brand'] . ", I'm looking at " . $p['lname'] . ' on block.ke. ' . $p['url'] );
		$homes = 'home' === $p['kind'];
		$rent  = 'rent' === $p['action'];
		$s_url = blockke_dev_search_url( array( 'area' => $area, 'action' => $p['action'] ) );
		$about = trim( (string) $term->description );
		$paras = '' !== $about ? preg_split( '#(?<=</p>)\s*#', trim( wpautop( wp_kses_post( $about ) ) ), 2 ) : array();
		$card  = function ( $c, $i, $rent, $extra = array() ) use ( $term ) {
			return blockke_card_html( $c, 'dev', array_merge( array( 'src' => 'area', 'scope' => $term->slug, 'pos' => $i + 1, 'rent' => $rent ), $extra ) );
		};
		// developments listed without a photo: by name and price, never a placeholder tile ($h: a heading, when a grid is above)
		$names = function ( $list, $rent, $h ) use ( $e ) {
			$x = '<div class="bka16-text">' . ( $h ? '<' . $h . ' class="bka16-h3">' . $e( 'Also listed (' . count( $list ) . ')' ) . '</' . $h . '>' : '' ) . '<ul>';
			foreach ( $list as $c ) {
				$pr = $rent ? blockke_bkn_price( $c['rfrom'], $c['rto'], true, false, count( $c['beds'] ) > 1 ) : blockke_bkn_price( $c['from'], $c['to'], false, false, count( $c['beds'] ) > 1 );
				$x .= '<li><a href="' . esc_url( $c['url'] ) . '" data-bkn="area">' . $e( $c['name'] ) . '</a>' . ( '' !== $pr ? '<span>' . $e( $pr ) . '</span>' : '' ) . '</li>';
			}
			return $x . '</ul></div>';
		};
		$secs = $p['seg'] ? array() : blockke_area_v16_sections( $data );
		$o    = array();
		$o[] = '<div id="bkd" class="bkd bka bka16 bkn" data-area="' . (int) $term->term_id . '" data-seg="' . esc_attr( $p['slug'] ) . '">' . blockke_dev_sprite();

		// Head: where you are, what is here, and (on the area page) what the area is like
		$crumbs = '<span class="bka16-home"><a href="' . esc_url( home_url( '/' ) ) . '">Home</a><span aria-hidden="true">/</span></span>';
		if ( $p['seg'] ) {
			$crumbs .= '<a href="' . esc_url( blockke_area_url( $term ) ) . '">' . $e( $area ) . '</a><span aria-hidden="true">/</span>';
			if ( '' !== $p['seg'][4] ) {
				$crumbs .= '<a href="' . esc_url( blockke_area_url( $term, $p['seg'][4] ) ) . '">' . $e( $segs[ $p['seg'][4] ][0] ) . '</a><span aria-hidden="true">/</span>';
			}
			$crumbs .= '<span class="bka16-cur" aria-current="page">' . $e( $p['label'] ) . '</span>'; // phones: the heading says it
		} else {
			$crumbs .= '<span aria-current="page">' . $e( $area ) . '</span>';
		}
		$tools = ( ! empty( $cfg['search'] ) ? '<a href="' . esc_url( $s_url ) . '" rel="nofollow"' . ( $homes ? ' data-bkd-search="hero" aria-haspopup="dialog"' : '' ) . '>' . ( $homes ? 'Search homes' : 'Search property' ) . '</a>' : '' )
			. '<button type="button" data-bkd-share>Share</button>';
		$o[]   = '<header class="bkn-head bka16-head" id="bkd-top"><div class="bkd-wrap"><div class="bka16-top"><nav class="bkn-crumbs" aria-label="Breadcrumb">' . $crumbs . '</nav><div class="bka16-tools">' . $tools . '</div></div>'
			. '<h1 class="bkn-h1">' . $e( $p['seg'] ? $p['label'] . ' in ' . $area : 'Property in ' . $area ) . '</h1>'
			. ( '' !== $p['tag'] ? '<p class="bkn-stand">' . $e( $p['tag'] ) . '</p>' : '' );
		if ( ! $p['seg'] && $paras ) {
			$o[] = '<div class="bka16-about">' . $paras[0] . '</div>' . ( isset( $paras[1] ) && '' !== trim( wp_strip_all_tags( $paras[1] ) ) ? '<p class="bka16-more"><a class="bkn-link" href="#bka-about">' . $e( 'More about ' . $area ) . '</a></p>' : '' );
		}
		if ( $p['seg'] ) {
			$o[] = blockke_area_v16_tabs( $p );
		}
		$o[] = '</div></header>';

		// Area page: photos of its leading developments (three, or one when its first kind has few), captioned and linked; the
		// first section's grid starts after them, so no photo shows twice
		$lead = $secs ? reset( $secs )['strip'] : array();
		if ( $lead ) {
			// sizes as drawn: the large photo 7/12 of the 1200px wrap (790px), 448px at 768, the page width on phones; the
			// small ones beside it 395px, 224px, half the page on phones; one photo alone spans the wrap
			$sz  = 1 === count( $lead ) ? array( '(min-width:1240px) 1200px, (min-width:768px) calc(100vw - 80px), calc(100vw - 40px)' ) : array( '(min-width:1000px) 790px, (min-width:768px) 448px, calc(100vw - 40px)', '(min-width:1000px) 395px, (min-width:768px) 224px, calc(50vw - 26px)' );
			$o[] = '<div class="bkd-wrap"><ul class="bka16-strip bka16-n' . count( $lead ) . '">';
			foreach ( array_values( $lead ) as $i => $c ) {
				$o[] = '<li><a href="' . esc_url( $c['url'] ) . '" data-bkn="area" data-pos="' . ( $i + 1 ) . '"><span class="bkn-img">' . wp_get_attachment_image( $c['cover'], 0 === $i ? 'large' : 'medium_large', false, array( 'alt' => $c['name'], 'sizes' => $sz[ min( $i, count( $sz ) - 1 ) ], 'loading' => 0 === $i ? 'eager' : 'lazy', 'decoding' => 'async' ) ) . '</span><span class="bka16-cap">' . $e( $c['name'] ) . '</span></a></li>';
			}
			$o[] = '</ul></div>';
		}

		$o[] = '<main class="bka16-main">';
		if ( $p['seg'] ) {
			// The segment: every development of this one kind, filtered and sorted in place
			$sd    = $data['segs'][ $p['slug'] ];
			$show  = array();
			$text  = array();
			foreach ( $sd['cards'] as $c ) {
				if ( $c['cover'] ) {
					$show[] = $c;
				} else {
					$text[] = $c;
				}
			}
			$o[] = '<section class="bka16-list" id="bka-list">';
			if ( $show ) {
				// Filtered and sorted here too, so a shared or reloaded address is right before the script runs (and without it)
				$f    = blockke_area_v16_state( $rent );
				$list = array();
				foreach ( $show as $r => $c ) {
					if ( blockke_area_v16_match( $c, $f, $rent ) ) {
						$list[ $r ] = $c;
					}
				}
				if ( '' !== $f['sort'] ) {
					$v = function ( $c, $k ) use ( $rent ) {
						return (int) ( $rent ? $c[ 'r' . $k ] : $c[ $k ] );
					};
					uasort(
						$list,
						function ( $a, $b ) use ( $f, $v ) {
							switch ( $f['sort'] ) {
								case 'price-asc':
									return ( $v( $a, 'from' ) ? $v( $a, 'from' ) : PHP_INT_MAX ) <=> ( $v( $b, 'from' ) ? $v( $b, 'from' ) : PHP_INT_MAX );
								case 'price-desc':
									return $v( $b, 'from' ) <=> $v( $a, 'from' );
								case 'soonest':
									return blockke_bkn_done_key( $a ) <=> blockke_bkn_done_key( $b );
							}
							return $b['d'] <=> $a['d'];
						}
					);
				}
				$o[] = blockke_area_v16_bar( $p, $show, count( $list ), $f );
				// with a price filter, cards show the range of the homes in them ("KES 6.2M–19.8M"), as on the index
				$o[] = '<div class="bkd-wrap"><h2 class="bkd-sr">Listings</h2><div class="bkn-grid bka16-grid" data-bka-grid data-page="' . (int) $f['page'] . '"' . ( $rent ? ' data-rent="1"' : '' ) . '>';
				$i   = 0;
				foreach ( $list + $show as $r => $c ) { // the matches in order, then the rest, hidden; data-r keeps "Recommended"
					$h   = $card( $c, $r, $rent, array( 'lazy' => $i > 2, 'range' => '' !== $f['price'] ) );
					$o[] = preg_replace( '/^<article /', '<article data-r="' . (int) $r . '"' . ( isset( $list[ $r ] ) && $i < $f['n'] ? '' : ' hidden' ) . ' ', $h, 1 );
					$i++;
				}
				// nothing matches: the search in words ("No studios for sale in Westlands at KES 60M and above."); the script
				// adds the ways out, the filter that frees most first
				$o[] = '</div><div class="bkn-none bka16-none" data-bka-none data-what="' . esc_attr( strtolower( $p['label'] ) ) . '" data-where="' . esc_attr( $area ) . '"' . ( $list ? ' hidden' : '' ) . '><p class="bkn-none-t" data-bka-none-t>' . ( $list ? '' : $e( 'No ' . blockke_area_v16_phrase( $p, $f ) . '.' ) ) . '</p><ul class="bkn-relax" data-bka-relax></ul>'
					. '<p><button class="bkd-inline" type="button" data-bkd-enquire="find" data-unit="' . esc_attr( $p['name'] ) . '">Ask an advisor to find one</button></p></div>'
					. '<a class="bkn-more" href="' . esc_url( add_query_arg( 'n', $f['n'] + $f['page'] ) ) . '" rel="nofollow" data-bka-more' . ( count( $list ) > $f['n'] ? '' : ' hidden' ) . '>Show ' . min( $f['page'], max( 0, count( $list ) - $f['n'] ) ) . ' more</a>';
			} else {
				$o[] = '<div class="bkd-wrap">';
				if ( ! $text ) { // nothing at all; a development listed without photos is still listed, below
					$o[] = '<div class="bka16-empty"><h2 class="bkn-h2">' . $e( 'No ' . strtolower( $p['label'] ) . ' in ' . $area . ' listed right now' ) . '</h2><p class="bkn-sub">Tell us what you need and an advisor will send you matches as they come up.</p>'
						. '<p><button class="bkd-btn bkd-btn-navy" type="button" data-bkd-enquire="find" data-unit="' . esc_attr( $p['name'] ) . '">Ask an advisor to find one</button></p></div>';
				} else {
					$o[] = '<h2 class="bkd-sr">Listings</h2>';
				}
			}
			if ( $text ) { // listed without photos: by name, under "Also listed" when there are cards above
				$o[] = $names( $text, $rent, $show ? 'h2' : '' );
			}
			$other = array();
			foreach ( $segs as $s => $seg ) {
				if ( '' === $seg[4] && $s !== $p['slug'] && $s !== $p['seg'][4] && $data['segs'][ $s ]['n'] ) {
					$other[] = '<a href="' . esc_url( blockke_area_url( $term, $s ) ) . '">' . $e( $seg[0] ) . '</a>';
				}
			}
			$other[] = '<a href="' . esc_url( blockke_area_url( $term ) ) . '">' . $e( $paras ? 'About ' . $area : 'All property in ' . $area ) . '</a>';
			$o[]     = '<p class="bka16-also"><span>Also in ' . $e( $area ) . ':</span> ' . implode( '<span aria-hidden="true"> · </span>', $other ) . '</p></div></section>';
		} else {
			// The area page: one section per kind, never mixed. The count once, under the heading ("253 homes, from KES 4.2M");
			// "See all" only where the page leaves some out at that width (phones show one card and the next three as rows)
			$first = true;
			foreach ( $secs as $s => $x ) {
				$seg   = $segs[ $s ];
				$sd    = $data['segs'][ $s ];
				$r     = 'rent' === $seg[1];
				$cards = $x['grid'];
				$all   = 'See all ' . strtolower( $seg[0] ) . ' in ' . $area;
				$one   = 1 === (int) $sd['homes'] && $sd['max'] <= $sd['min'] && ( ! $sd['cards'] || count( $sd['cards'][0]['beds'] ) < 2 ); // one home at one price
				$sub   = blockke_area_v16_count( $sd['homes'], $seg[2] ) . ( $sd['min'] > 0 ? ( $one ? ' at ' : ', from ' ) . blockke_bkn_money( $sd['min'] ) . ( $r ? ' a month' : '' ) : '' );
				$o[]   = '<section class="bka16-sec" id="bka-' . esc_attr( $s ) . '"><div class="bkd-wrap"><div class="bka16-sh"><div><h2 class="bkn-h2">' . $e( $seg[0] ) . '</h2><p class="bkn-sub">' . $e( $sub ) . '</p></div>'
					. ( $x['all'] ? '' : '<a class="bkn-link bka16-all-top" href="' . esc_url( blockke_area_url( $term, $s ) ) . '">' . $e( $all ) . '</a>' ) . '</div>'
					. blockke_area_v16_tabs( $p, $s );
				if ( $cards ) {
					$o[] = '<div class="bkn-grid bka16-cards">';
					foreach ( $cards as $i => $c ) {
						$o[] = $card( $c, $i, $r, array( 'lazy' => ! $first || $i > 0 ) );
					}
					$o[] = '</div><div class="bka16-rows">'; // phones: one card, then the next three as rows (or all of them, when the page holds every one)
					foreach ( $x['rows'] as $i => $c ) {
						$o[] = blockke_card_html( $c, 'row', array( 'src' => 'area', 'scope' => $term->slug, 'pos' => $i + 2, 'rent' => $r ) );
					}
					$o[] = '</div>';
				}
				if ( $x['text'] ) {
					$o[] = $names( $x['text'], $r, $cards ? 'h3' : '' );
				}
				$o[] = ( $x['phone'] ? '' : '<a class="bkn-link bka16-all" href="' . esc_url( blockke_area_url( $term, $s ) ) . '">' . $e( $all ) . '</a>' ) . '</div></section>';
				if ( $first ) { // after the first grid: a short way to ask, as a noun ("Price lists for Westlands")
					$ask = 'sale' === $seg[1] ? array( 'Price lists for ' . $area, 'Tell us what you need and an advisor sends them by WhatsApp.' ) : array( 'New listings in ' . $area, 'Tell us what you need and an advisor sends matches by WhatsApp.' );
					$o[] = '<aside class="bka16-ask"><div class="bkd-wrap bka16-ask-in"><div><h2 class="bka16-h3">' . $e( $ask[0] ) . '</h2><p>' . $e( $ask[1] ) . '</p></div>' . blockke_area_quick_form( 'inline', 'Send', $p, false ) . '</div></aside>';
				}
				$first = false;
			}
			if ( $first ) {
				$o[] = '<section class="bka16-sec"><div class="bkd-wrap bka16-empty"><h2 class="bkn-h2">' . $e( 'Nothing listed in ' . $area . ' right now' ) . '</h2><p class="bkn-sub">Tell us what you need and an advisor will send you matches as they come up.</p>'
					. '<p><button class="bkd-btn bkd-btn-navy" type="button" data-bkd-enquire="find" data-unit="' . esc_attr( $p['name'] ) . '">Ask an advisor to find one</button></p></div></section>';
			}
		}

		// Nearby areas with the same kind of listing
		$near = blockke_area_nearby( $p );
		if ( $near ) {
			$what = $p['seg'] ? $p['label'] : 'Homes for sale';
			$o[]  = '<section class="bka16-sec bka16-near-sec" id="bka-nearby"><div class="bkd-wrap"><h2 class="bkn-h2">' . $e( $what . ' nearby' ) . '</h2><ul class="bka-near">';
			foreach ( $near as $n ) {
				$o[] = '<li><a href="' . esc_url( $n[2] ) . '"><b>' . $e( blockke_dev_text( $n[0]->name ) ) . '</b><span>' . $e( blockke_area_v16_count( $n[1], $p['kind'] ) ) . '</span></a></li>';
			}
			$o[] = '</ul></div></section>';
		}

		// The rest of the area's own description
		if ( ! $p['seg'] && isset( $paras[1] ) && '' !== trim( wp_strip_all_tags( $paras[1] ) ) ) {
			$o[] = '<section class="bka16-sec" id="bka-about"><div class="bkd-wrap bka16-about-sec"><h2 class="bkn-h2">' . $e( 'About ' . $area ) . '</h2><div class="bka16-about">' . $paras[1] . '</div></div></section>';
		}
		$o[] = '</main>';

		// The one navy band
		$o[] = '<section class="bkn-ask" id="bkd-enquire"><div class="bkd-wrap bkn-ask-in"><div class="bkn-ask-t"><h2 class="bkn-h2">' . $e( 'Ask about ' . $p['lname'] ) . '</h2>'
			. '<p>Tell us your budget and what you need. An advisor sends matching listings with prices and terms.</p>'
			. '<p class="bkn-ask-direct"><a href="' . esc_url( $wa ) . '" target="_blank" rel="noopener" data-bkd-wa>' . blockke_dev_icon( 'wa', 'bkd-wa-ic' ) . 'WhatsApp an advisor</a><a href="tel:' . esc_attr( $ag['phone'] ) . '">Call ' . $e( $ag['phone_display'] ) . '</a></p></div>'
			. '<div class="bkn-ask-f">' . blockke_area_v16_form( $p ) . '</div></div>'
			. '<div class="bkd-wrap"><p class="bkn-disc bka16-disc">' . $e( $cfg['area_disclaimer'] ) . '</p></div></section>';

		$o[] = '<nav class="bkd-mb" id="bkd-mb" aria-label="Quick contact"><a class="bkd-mb-wa" href="' . esc_url( $wa ) . '" target="_blank" rel="noopener" data-bkd-wa aria-label="WhatsApp">' . blockke_dev_icon( 'wa', 'bkd-wa-ic' ) . '</a>'
			. '<button class="bkd-btn bkd-btn-navy" type="button" data-bkd-enquire="mobile-bar" data-unit="' . esc_attr( $p['name'] ) . '">Ask an advisor</button></nav>';
		$o[] = '<div class="bkd-md" id="bkd-md" hidden role="dialog" aria-modal="true" aria-labelledby="bkd-md-t" aria-describedby="bkd-md-sub"><div class="bkd-md-box">'
			. '<button class="bkd-md-x" type="button" data-bkd-close aria-label="Close">' . blockke_dev_icon( 'close' ) . '</button>'
			. '<h2 class="bkd-h2" id="bkd-md-t">' . $e( 'Get matching ' . $p['things'] ) . '</h2><p class="bkd-md-sub" id="bkd-md-sub"></p>'
			. '<div id="bkd-md-form">' . blockke_area_quick_form( 'modal', 'Send me matches', $p, false ) . '</div></div></div>';
		if ( ! empty( $cfg['search'] ) && $homes ) {
			$o[] = blockke_dev_search_sheet( $stub );
		}
		$o[] = '</div>';
		return implode( '', $o );
	}

	/**
	 * Type tabs, as text: "All · Apartments 220 · Villas 2", homes counted as the standfirst counts them ($parent: the
	 * top-level segment; on a segment page, its own). "All" and the page's own tab carry no number: the standfirst, or the
	 * section's line on the area page, already says it, and a count appears once.
	 */
	function blockke_area_v16_tabs( $p, $parent = '' ) {
		$segs = blockke_area_segments();
		$data = $p['data'];
		$cur  = $p['slug'];
		$top  = '' !== $parent ? $parent : ( '' !== $p['seg'][4] ? $p['seg'][4] : $p['slug'] );
		$kids = array();
		foreach ( $segs as $s => $seg ) {
			if ( $seg[4] === $top && $data['segs'][ $s ]['n'] > 0 && ! blockke_area_same_as_parent( $data, $s ) ) {
				$kids[ $s ] = preg_replace( '/ for (sale|rent|lease)$/', '', $seg[0] );
			}
		}
		if ( ! $kids ) {
			return '';
		}
		$items = array( $top => 'All' ) + $kids;
		$h     = '<nav class="bka16-tabs" aria-label="' . esc_attr( $segs[ $top ][0] . ' by type' ) . '">';
		foreach ( $items as $s => $l ) {
			$n  = $s === $top || $s === $cur ? '' : ' <small>' . (int) $data['segs'][ $s ]['homes'] . '</small>';
			$h .= $s === $cur ? '<span aria-current="page">' . esc_html( $l ) . $n . '</span>' : '<a href="' . esc_url( blockke_area_url( $p['term'], $s ) ) . '">' . esc_html( $l ) . $n . '</a>';
		}
		return $h . '</nav>';
	}

	/**
	 * The segment page's filter bar, as on the index: bedrooms (homes), price, completion (homes for sale) and sort; a GET form.
	 * Below 1000px "Filters" opens the fields as a sheet. The count shows only while a filter is set ("12 matches"): the
	 * standfirst carries it otherwise. No bar when there is nothing to choose. $f: the page's state (blockke_area_v16_state()).
	 */
	function blockke_area_v16_bar( $p, $cards, $n = null, $f = null ) {
		$e     = 'esc_html';
		$rent  = 'rent' === $p['action'];
		$homes = 'home' === $p['kind'];
		$f     = null === $f ? blockke_area_v16_state( $rent ) : $f;
		$sel   = function ( $name, $label, $opts ) use ( $e, $f ) {
			$h = '<label class="bkn-f"><span class="bkd-sr">' . $e( $label ) . '</span><select name="' . esc_attr( $name ) . '" data-bka-f="' . esc_attr( $name ) . '"' . ( '' !== $f[ $name ] ? ' class="on"' : '' ) . '>';
			foreach ( $opts as $v => $l ) {
				$h .= '<option value="' . esc_attr( $v ) . '"' . selected( (string) $v, (string) $f[ $name ], false ) . '>' . $e( $l ) . '</option>';
			}
			return $h . '</select></label>';
		};
		$beds = array();
		foreach ( $cards as $c ) {
			foreach ( $c['beds'] as $b ) {
				$beds[ min( 4, $b ) ] = 1;
			}
		}
		ksort( $beds );
		$fs = '';
		if ( $homes && count( $beds ) > 1 ) {
			$bo = array( '' => 'Any bedrooms' );
			foreach ( array_keys( $beds ) as $b ) {
				$bo[ 0 === $b ? 'studio' : (string) $b ] = 0 === $b ? 'Studio' : ( 4 === $b ? '4+ bed' : $b . ' bed' );
			}
			$fs .= $sel( 'beds', 'Bedrooms', $bo );
		}
		$po = array( '' => 'Any price' );
		foreach ( blockke_area_v16_bands( $rent ) as $k => $b ) {
			$po[ $k ] = $b[2];
		}
		$fs .= count( $cards ) > 3 ? $sel( 'price', 'Price', $po ) : '';
		if ( $homes && ! $rent && count( $cards ) > 3 ) {
			$fs .= $sel( 'completion', 'Completion', array( '' => 'Any completion' ) + blockke_index_years() );
		}
		$sort = count( $cards ) > 1 ? $sel( 'sort', 'Sort', array( '' => 'Recommended' ) + ( $homes && ! $rent ? array( 'soonest' => 'Completion, soonest' ) : array() ) + array( 'price-asc' => 'Price, lowest first', 'price-desc' => 'Price, highest first', 'newest' => 'Newest on Block' ) ) : '';
		if ( '' === $fs && '' === $sort ) {
			return '';
		}
		$n    = null === $n ? count( $cards ) : (int) $n;
		$nset = count( array_filter( array( $f['beds'], $f['price'], $f['completion'] ) ) );
		$keep = ''; // an editor's previews survive the no-script Apply
		foreach ( array( 'bke_area' => ! empty( $GLOBALS['blockke_area_preview'] ) ? '1' : '', 'bke_cards' => isset( $GLOBALS['blockke_cards_preview'] ) ? (string) $GLOBALS['blockke_cards_preview'] : '' ) as $k => $v ) {
			$keep .= '' !== $v ? '<input type="hidden" name="' . $k . '" value="' . esc_attr( $v ) . '">' : '';
		}
		return '<form class="bkn-bar bka16-bar" method="get" data-bka-bar role="search" aria-label="' . esc_attr( 'Filter ' . strtolower( $p['label'] ) ) . '"><div class="bkd-wrap bkn-bar-in">'
			. ( '' !== $fs ? '<button class="bkn-fbtn" type="button" data-bka-sheet aria-expanded="false" aria-controls="bka-fields">Filters' . ( $nset ? ' · ' . $nset : '' ) . '</button>'
				. '<div class="bkn-fields" id="bka-fields" data-bka-fields><div class="bkn-sheet-head"><b>Filters</b><button type="button" class="bkn-x" data-bka-close aria-label="Close filters">' . blockke_dev_icon( 'close' ) . '</button></div>' . $fs
				. '<div class="bkn-sheet-foot"><button class="bkd-btn bkd-btn-navy bkd-btn-block" type="submit" data-bka-show>' . $e( $nset ? 'Show ' . $n . ( 1 === $n ? ' match' : ' matches' ) : 'Show all' ) . '</button></div></div>' : '' )
			. '<p class="bkn-count bka16-count" data-bka-count aria-live="polite"' . ( $nset && $n ? '>' . $e( $n . ( 1 === $n ? ' match' : ' matches' ) ) : ' hidden>' ) . '</p>'
			. '<a class="bkn-clear" href="' . esc_url( blockke_area_url( $p['term'], $p['slug'] ) ) . '" data-bka-clear' . ( $nset || '' !== $f['sort'] ? '' : ' hidden' ) . '>Clear filters</a>'
			. $sort . $keep . '<noscript><button class="bkn-apply" type="submit">Apply</button></noscript></div>'
			. '<script type="application/json" data-bka-bands>' . wp_json_encode( blockke_area_v16_bands( $rent ), JSON_HEX_TAG ) . '</script></form>';
	}

	/** The full enquiry form on v1.6 area pages: name, phone, email, budget, message; the qualifying questions come after. */
	function blockke_area_v16_form( $p ) {
		$cfg  = blockke_dev_config();
		$rent = 'rent' === $p['action'];
		$h    = '<form class="bkd-form" data-bkd-lead="contact" data-unit="' . esc_attr( $p['name'] ) . '" novalidate><input class="bkd-hp" type="text" name="bkd_hp" tabindex="-1" autocomplete="off" aria-hidden="true">';
		$h   .= '<div class="bkd-two">' . blockke_dev_field( 'name', 'Full name', 'text', 'name' ) . blockke_dev_field( 'phone', 'Phone / WhatsApp', 'tel', 'tel', '07XX XXX XXX' ) . '</div>';
		$h   .= blockke_dev_field( 'email', 'Email', 'email', 'email', '', true );
		$h   .= '<label class="bkd-f"><span>Budget <small>(optional)</small></span><select name="budget"><option value="">Not sure yet</option>';
		foreach ( isset( $cfg['budget_bands'][ $rent ? 'rent' : 'sale' ] ) ? (array) $cfg['budget_bands'][ $rent ? 'rent' : 'sale' ] : array() as $b ) {
			// the filter's words ("KES 8–12M"), so the email reads like the bar the visitor used
			$h .= '<option>' . esc_html( function_exists( 'blockke_search_band_label' ) ? blockke_search_band_label( (float) $b[0], (float) $b[1], $rent ) : $b[2] ) . '</option>';
		}
		$ph = 'commercial' === $p['kind'] ? 'Size, parking, move-in date.' : ( 'land' === $p['kind'] ? 'Plot size, intended use, title type.' : 'Bedrooms, streets you like, move-in date.' );
		$h .= '</select></label><label class="bkd-f"><span>Message <small>(optional)</small></span><textarea name="message" placeholder="' . esc_attr( $ph ) . '"></textarea></label>';
		$h .= '<button class="bkd-btn bkd-btn-navy bkd-btn-block" type="submit">Send</button>' . blockke_area_consent( $p ) . '</form>' . blockke_dev_ok();
		return $h;
	}

	/** v1.6 area page styles, on top of the index's and the card family's. */
	function blockke_area_css_v16() {
		return <<<'CSS'
#bkd.bka16 .bka16-top{display:flex;justify-content:space-between;align-items:center;gap:16px}
#bkd .bka16-top .bkn-crumbs{flex:1 1 auto;min-width:0}
#bkd .bka16-home{display:contents}
#bkd .bka16-tools{display:flex;align-items:center;flex:none;gap:20px;font-size:14px;white-space:nowrap}
#bkd .bka16-tools a,#bkd .bka16-tools button{display:inline-flex;align-items:center;margin:0;padding:0;border:0;background:none;min-height:44px;font:500 14px/1 var(--font)!important;letter-spacing:0!important;text-transform:none!important;color:var(--navy)!important;text-decoration:underline;text-underline-offset:4px;cursor:pointer}
@media (max-width:639px){#bkd .bka16-home,#bkd .bka16-cur,#bkd .bkn-crumbs span:has(+ .bka16-cur){display:none}#bkd .bka16-tools{gap:16px}}
#bkd .bka16-about{max-width:62ch;margin:20px 0 0;font-size:17px;line-height:1.65;color:var(--ink)}
#bkd .bka16-about p{margin:0 0 14px}
#bkd .bka16-more{margin:4px 0 0;font-size:15px}
#bkd .bka16-more a,#bkd .bka16-all-top{display:inline-block;padding:11px 0;margin:-11px 0}
#bkd .bka16-tabs{display:flex;flex-wrap:wrap;gap:6px 24px;margin:28px 0 0;padding:0 0 0;font-size:15px}
@media (max-width:767px){#bkd .bka16-tabs{flex-wrap:nowrap;gap:20px;overflow-x:auto;scrollbar-width:none;margin-right:calc(var(--gutter) * -1);padding-right:var(--gutter)}#bkd .bka16-tabs::-webkit-scrollbar{display:none}}
#bkd .bka16-tabs a,#bkd .bka16-tabs span{display:inline-flex;flex:none;align-items:center;gap:6px;min-height:44px;white-space:nowrap;color:var(--navy)!important;text-decoration:none;border-bottom:1px solid transparent}
#bkd .bka16-tabs a:hover{border-bottom-color:var(--line-2)}
#bkd .bka16-tabs span[aria-current]{font-weight:600;border-bottom:2px solid var(--brass)}
#bkd .bka16-tabs small{font-size:13px;font-weight:400;color:var(--muted)}
#bkd .bka16-strip{display:grid;grid-template-columns:minmax(0,2fr) minmax(0,1fr);gap:20px 16px;margin:8px 0 0;padding:0;list-style:none}
#bkd .bka16-strip.bka16-n1{grid-template-columns:1fr}
#bkd .bka16-strip.bka16-n2{grid-template-columns:1fr 1fr}
#bkd .bka16-strip li{margin:0;min-width:0}
#bkd .bka16-strip a{display:block;color:var(--navy)!important;text-decoration:none}
#bkd .bka16-strip .bkn-img img{position:absolute;inset:0}
#bkd .bka16-strip.bka16-n1 .bkn-img{aspect-ratio:21/9}
#bkd .bka16-strip.bka16-n3 li:first-child{grid-row:1/span 2}
#bkd .bka16-strip.bka16-n3 li:first-child a{display:grid;grid-template-rows:minmax(0,1fr) auto;height:100%}
#bkd .bka16-strip.bka16-n3 li:first-child .bkn-img{aspect-ratio:auto;height:100%}
#bkd .bka16-cap{display:block;margin-top:10px;font-size:14px;color:var(--slate)}
#bkd .bka16-strip a:hover .bka16-cap{text-decoration:underline;text-underline-offset:3px}
@media (max-width:767px){#bkd .bka16-strip,#bkd .bka16-strip.bka16-n2{grid-template-columns:1fr 1fr;gap:16px 12px}#bkd .bka16-strip li:first-child,#bkd .bka16-strip.bka16-n3 li:first-child{grid-column:1/-1;grid-row:auto}#bkd .bka16-strip.bka16-n3 li:first-child a{display:block;height:auto}#bkd .bka16-strip.bka16-n3 li:first-child .bkn-img,#bkd .bka16-strip.bka16-n1 .bkn-img{aspect-ratio:3/2;height:auto}#bkd .bka16-strip.bka16-n2 li:last-child{grid-column:1/-1}}
#bkd .bka16-main{display:block}
#bkd .bka16-sec{padding:var(--sec) 0 0}
#bkd .bka16-sh{display:flex;justify-content:space-between;align-items:flex-end;gap:24px;margin-bottom:32px}
#bkd .bka16-sh .bkn-link{font-size:15px;white-space:nowrap}
#bkd .bka16-sec .bka16-tabs{margin:-12px 0 28px}
#bkd .bka16-rows{display:none}
#bkd .bka16-all{display:none}
@media (max-width:767px){
#bkd .bka16-cards>.bkn-card:not(:first-child){display:none}
#bkd .bka16-rows{display:block;margin-top:24px;border-top:1px solid var(--line)}
#bkd .bka16-all{display:inline-flex;align-items:center;min-height:44px;margin-top:12px;font-size:15px}
#bkd .bka16-all-top{display:none}
}
#bkd .bka16-ask{margin:var(--sec) 0 0;padding:40px 0;border-top:1px solid var(--line);border-bottom:1px solid var(--line)}
#bkd .bka16-ask-in{display:grid;gap:20px;align-items:center}
@media (min-width:1000px){#bkd .bka16-ask-in{grid-template-columns:minmax(0,5fr) minmax(0,7fr);column-gap:64px}}
#bkd .bka16-h3{margin:0;font-family:var(--display)!important;font-weight:var(--display-w)!important;font-size:28px!important;line-height:1.15!important;letter-spacing:var(--display-ls)!important;color:var(--navy)!important;text-transform:none!important}
#bkd .bka16-ask p{margin:8px 0 0;font-size:16px;color:var(--slate)}
#bkd .bka16-ask .bkd-form{display:grid;gap:12px}
@media (min-width:768px){#bkd .bka16-ask .bkd-form{grid-template-columns:minmax(0,1fr) auto;align-items:end}#bkd .bka16-ask .bkd-qgrid{display:grid;grid-template-columns:1fr 1fr;gap:12px}#bkd .bka16-ask .bkd-consent{grid-column:1/-1}}
#bkd .bka16-list{padding:0}
#bkd .bka16-bar{margin:0 0 40px}
@media (max-width:999px){#bkd .bka16-bar .bkn-bar-in>.bkn-f{margin-left:auto}}
@media (scripting:none) and (max-width:999px){#bkd .bka16-bar .bkn-fbtn{display:none}#bkd .bka16-bar .bkn-fields{display:flex;flex-wrap:wrap;gap:0 24px}#bkd .bka16-bar .bkn-bar-in{flex-wrap:wrap}}
#bkd .bka16-grid>.bkn-card[hidden]{display:none}
#bkd .bka16-text{margin-top:56px;max-width:720px}
#bkd .bka16-text ul{margin-top:12px;border-top:1px solid var(--line)}
#bkd .bka16-text li{display:flex;justify-content:space-between;align-items:center;gap:16px;border-bottom:1px solid var(--line);font-size:15px}
#bkd .bka16-text li a{flex:1 1 auto;display:flex;align-items:center;min-height:48px;padding:10px 0;color:var(--navy)!important}
#bkd .bka16-sec .bka16-text{margin-top:48px}#bkd .bka16-sec .bka16-text .bka16-h3{font-size:24px!important}
#bkd .bka16-sec .bka16-sh+.bka16-text,#bkd .bka16-sec .bka16-tabs+.bka16-text,#bkd .bkd-sr+.bka16-text{margin-top:0}
#bkd .bka16-text li span{color:var(--muted);font-variant-numeric:tabular-nums}
#bkd .bka16-also{margin:56px 0 0;font-size:15px;line-height:1.9}
#bkd .bka16-also>span:first-child{color:var(--slate);margin-right:4px}
#bkd .bka16-also a{display:inline-block;padding:8px 0;margin:-8px 0;color:var(--navy)!important;text-decoration:underline;text-underline-offset:4px}
#bkd .bka16-none .bkd-inline{display:inline-flex;align-items:center;min-height:44px;padding:0;border:0;background:none;font:600 15px var(--font);cursor:pointer}
#bkd .bka16-empty{max-width:640px;padding:16px 0 0}
#bkd .bka16-empty .bkd-btn{margin-top:20px}
#bkd .bka16-near-sec .bka-near{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:0 32px;margin:28px 0 0;padding:0;list-style:none;border-top:1px solid var(--line)}
#bkd .bka16-near-sec .bka-near li{margin:0;border-bottom:1px solid var(--line)}
#bkd .bka16-near-sec .bka-near a{display:flex;justify-content:space-between;align-items:baseline;gap:12px;min-height:56px;padding:16px 0;color:var(--navy)!important;text-decoration:none}
#bkd .bka16-near-sec .bka-near b{font-weight:500;font-size:16px}
#bkd .bka16-near-sec .bka-near span{flex:none;font-size:14px;color:var(--muted);font-variant-numeric:tabular-nums}
#bkd .bka16-near-sec .bka-near a:hover b{text-decoration:underline;text-underline-offset:4px}
@media (max-width:999px){#bkd .bka16-near-sec .bka-near{grid-template-columns:repeat(2,minmax(0,1fr))}}
@media (max-width:639px){#bkd .bka16-near-sec .bka-near{grid-template-columns:1fr}}
#bkd .bka16-about-sec .bka16-about{margin-top:20px}
#bkd.bka16 .bkn-ask{margin-top:var(--sec)}
#bkd .bka16-disc{margin-top:56px;color:var(--on-navy)}
CSS;
	}

	/**
	 * v1.6 segment page behaviour: filters and sort in place, kept in the address; the phone and tablet sheet; "Show more"
	 * (focus moves to the first card it added); with nothing matching, the search in words and the ways out, most results first.
	 */
	function blockke_area_js_v16() {
		return <<<'JS'
(function(){
  'use strict';
  var root=document.getElementById('bkd');if(!root||!root.classList.contains('bka16')){return;}
  var dl=(window.dataLayer=window.dataLayer||[]),C=window.BKDEV||{};
  function track(ev,o){var p={area:C.area||'',segment:C.seg||''};for(var k in o){p[k]=o[k];}try{if(typeof window.gtag==='function'){window.gtag('event',ev,p);}else{p.event=ev;dl.push(p);}}catch(e){}}
  root.addEventListener('click',function(e){var a=e.target.closest&&e.target.closest('a[data-bkn]');if(a){var c=a.closest('[data-id]'),s=a.closest('section,ul');track('area_card_click',{target_id:c?c.getAttribute('data-id'):'',position:+(a.getAttribute('data-pos')||0),section:s&&s.id||'',vt:('onpagereveal' in window)?1:0,transport_type:'beacon'});}});
  var grid=root.querySelector('[data-bka-grid]'),form=root.querySelector('[data-bka-bar]');if(!grid||!form){return;}
  var bands={};try{bands=JSON.parse(form.querySelector('[data-bka-bands]').textContent);}catch(e){}
  var cards=[].slice.call(grid.querySelectorAll('.bkn-card')),page=+grid.getAttribute('data-page')||24,n=page,rent=grid.getAttribute('data-rent')==='1';
  var more=root.querySelector('[data-bka-more]'),none=root.querySelector('[data-bka-none]'),count=root.querySelector('[data-bka-count]'),clear=root.querySelector('[data-bka-clear]');
  var fields=root.querySelector('[data-bka-fields]'),fbtn=root.querySelector('[data-bka-sheet]'),show=root.querySelector('[data-bka-show]');
  var KEYS=['beds','price','completion','sort'],labels={beds:'Any bedrooms',price:'Any price',completion:'Any completion'};
  function read(){var f={};KEYS.forEach(function(k){var s=form.querySelector('[name="'+k+'"]');f[k]=s?s.value:'';});return f;}
  function num(c,k){return +c.getAttribute('data-'+k)||0;}
  function matches(k){return k===1?'1 match':k+' matches';}
  function match(c,f,skip){
    if(skip!=='beds'&&f.beds){var b=f.beds==='studio'?0:+f.beds,ok=false;(c.getAttribute('data-beds')||'').split(' ').filter(Boolean).forEach(function(x){x=+x;ok=ok||(b===4?x>=4:x===b);});if(!ok){return false;}}
    if(skip!=='price'&&f.price&&bands[f.price]){var bd=bands[f.price],fr=num(c,'from'),to=num(c,'to')||fr;if(!fr||to<bd[0]||(bd[1]>0&&fr>=bd[1])){return false;}}
    if(skip!=='completion'&&f.completion){var d=num(c,'done'),cy=Math.floor(d/100);
      if(f.completion==='ready-now'){if(c.getAttribute('data-st')!=='ready'){return false;}}
      else if(/-plus$/.test(f.completion)){if(d>=999999||cy<parseInt(f.completion,10)){return false;}}
      else if(cy!==+f.completion||d>=999999){return false;}}
    return true;}
  /* "KES 6.06M", "KES 25.3M", "KES 180K": rounded up, as the server writes them */
  function money(v){if(v>=1e6){var dp=v>=1e7?1:2,x=Math.ceil(Math.round(v/1e6*Math.pow(10,dp)*1e6)/1e6)/Math.pow(10,dp);return 'KES '+(+x.toFixed(dp))+'M';}return 'KES '+Math.ceil(Math.round(v/1e3*1e6)/1e6)+'K';}
  /* with a price filter a card shows the range of its homes ("KES 6.2M–19.8M"), else "From KES 6.2M"; the page arrives drawn for its own filter */
  var priced=null;
  function prices(on){if(priced===on){return;}var first=priced===null;priced=on;
    cards.forEach(function(c){var b=c.querySelector('.bkn-data b'),fr=num(c,'from'),to=num(c,'to'),k=on?1:0,m=rent?' a month':'';if(!b||!(to>fr)){return;}
      if(first){b._t=[];b._t[k]=b.textContent;return;}
      if(!b._t[k]){b._t[k]=on?money(fr)+'–'+money(to).replace(/^\D+/,'')+m:'From '+money(fr)+m;}b.textContent=b._t[k];});}
  /* the search in words: "studios for sale in Westlands at KES 60M and above" */
  function said(f,k){var s=form.querySelector('[name="'+k+'"]'),o=s?[].filter.call(s.options,function(x){return x.value===f[k];})[0]:null;return o?o.textContent:f[k];}
  function phrase(f){var w=none.getAttribute('data-what')||'';
    if(f.beds==='studio'){w=/^(homes|studios) /.test(w)?w.replace(/^\w+/,'studios'):'studio '+w;}else if(f.beds){w=(f.beds==='4'?'4+':f.beds)+' bed '+w;}
    var s=w+' in '+(none.getAttribute('data-where')||'');
    if(f.price&&bands[f.price]){var l=bands[f.price][2];s+=/^Under /.test(l)?' under '+l.slice(6):' at '+l;}
    if(f.completion){s+=f.completion==='ready-now'?' ready to move into':' completing in '+said(f,'completion');}
    return s;}
  function sheetOpen(){return !!fields&&fields.classList.contains('on');}
  /* after a change from the sticky bar the results start just under it, not above the screen */
  function toResults(){var t=grid.getBoundingClientRect().top-form.getBoundingClientRect().bottom;if(t<0){window.scrollBy(0,t);}}
  function first(){var a=grid.querySelector('.bkn-card:not([hidden]) a.bkn-a');if(a){a.focus({preventScroll:true});}}
  function apply(user){
    var f=read(),set=!!(f.beds||f.price||f.completion),nset=0,list=cards.filter(function(c){return match(c,f);});
    var s=f.sort;if(s){list.sort(function(a,b){if(s==='price-asc'){return (num(a,'from')||Infinity)-(num(b,'from')||Infinity);}if(s==='price-desc'){return num(b,'from')-num(a,'from');}if(s==='soonest'){return num(a,'done')-num(b,'done');}return num(b,'d')-num(a,'d');});}
    else{list.sort(function(a,b){return num(a,'r')-num(b,'r');});}
    cards.forEach(function(c){c.hidden=true;});
    list.forEach(function(c,i){grid.appendChild(c);c.hidden=i>=n;});
    KEYS.forEach(function(k){var el=form.querySelector('[name="'+k+'"]');if(el){el.classList.toggle('on',!!f[k]);}if(k!=='sort'&&f[k]){nset++;}});
    prices(!!f.price);
    /* the count only while a filter is set, and not at 0: the sentence below says so */
    if(count){count.hidden=!set||!list.length;count.textContent=matches(list.length);}
    if(clear){clear.hidden=!set&&!s;}
    if(fbtn){fbtn.textContent='Filters'+(nset?' · '+nset:'');}
    if(show){show.textContent=set?'Show '+matches(list.length):'Show all';}
    var u=new URLSearchParams(location.search);KEYS.forEach(function(k){if(f[k]){u.set(k,f[k]);}else{u.delete(k);}});if(n>page){u.set('n',n);}else{u.delete('n');}
    var qs=u.toString();try{history.replaceState(history.state,'',location.pathname+(qs?'?'+qs:'')+location.hash);}catch(e){}
    if(more){more.hidden=list.length<=n;more.textContent='Show '+Math.min(page,list.length-n)+' more';u.set('n',n+page);more.href=location.pathname+'?'+u.toString();}
    none.hidden=list.length>0;
    if(!list.length){root.querySelector('[data-bka-none-t]').textContent='No '+phrase(f)+'.';var rl=root.querySelector('[data-bka-relax]'),opts=[];rl.innerHTML='';
      ['beds','price','completion'].forEach(function(k){if(!f[k]){return;}var c=cards.filter(function(x){return match(x,f,k);}).length;if(c){opts.push([c,k]);}});
      opts.sort(function(a,b){return b[0]-a[0];}).forEach(function(o){var li=document.createElement('li'),b=document.createElement('button');b.type='button';b.textContent=labels[o[1]]+' ('+o[0]+')';
        b.addEventListener('click',function(){form.querySelector('[name="'+o[1]+'"]').value='';n=page;apply(true);track('area_filter',{filter:o[1],value:'any'});toResults();first();});li.appendChild(b);rl.appendChild(li);});}
    C.listLabel=(C.leadName||'').replace(/ on block\.ke$/,'');C.ctxBeds=f.beds;
  }
  /* the phone and tablet sheet: a dialog above the theme's header, Tab kept inside it; it closes itself if the screen widens */
  function trap(e){if(e.key!=='Tab'||!sheetOpen()){return;}var l=[].slice.call(fields.querySelectorAll('button,select,a[href]')).filter(function(x){return x.offsetParent!==null&&!x.disabled;});if(!l.length){return;}
    if(!fields.contains(document.activeElement)){e.preventDefault();l[0].focus();}else if(e.shiftKey&&document.activeElement===l[0]){e.preventDefault();l[l.length-1].focus();}else if(!e.shiftKey&&document.activeElement===l[l.length-1]){e.preventDefault();l[0].focus();}}
  function openSheet(){form.classList.add('bkn-up');fields.classList.add('on');fields.setAttribute('role','dialog');fields.setAttribute('aria-modal','true');fields.setAttribute('aria-label','Filters');fbtn.setAttribute('aria-expanded','true');document.documentElement.classList.add('bkd-lock');document.addEventListener('keydown',trap);var x=fields.querySelector('select');if(x){x.focus();}}
  function closeSheet(back){if(!sheetOpen()){return;}fields.classList.remove('on');form.classList.remove('bkn-up');['role','aria-modal','aria-label'].forEach(function(k){fields.removeAttribute(k);});fbtn.setAttribute('aria-expanded','false');document.documentElement.classList.remove('bkd-lock');document.removeEventListener('keydown',trap);if(back!==false){fbtn.focus();}}
  if(fbtn&&fields){fbtn.addEventListener('click',openSheet);
    root.addEventListener('click',function(e){if(e.target.closest&&e.target.closest('[data-bka-close]')){closeSheet();}});
    document.addEventListener('keydown',function(e){if(e.key==='Escape'){closeSheet();}});
    var wide=window.matchMedia?window.matchMedia('(min-width:1000px)'):null,onWide=function(){if(wide.matches){closeSheet(false);}};
    if(wide){if(wide.addEventListener){wide.addEventListener('change',onWide);}else if(wide.addListener){wide.addListener(onWide);}}}
  var q=new URLSearchParams(location.search);n=Math.max(page,+q.get('n')||page);
  form.addEventListener('change',function(e){var t=e.target;if(!t.name){return;}n=page;if(t.name==='sort'){track('area_sort',{sort:t.value||'recommended'});}else{track('area_filter',{filter:t.name,value:t.value||'any'});}apply(true);if(!sheetOpen()){toResults();}});
  form.addEventListener('submit',function(e){e.preventDefault();apply(true);closeSheet();toResults();});
  if(clear){clear.addEventListener('click',function(e){e.preventDefault();KEYS.forEach(function(k){var x=form.querySelector('[name="'+k+'"]');if(x){x.value='';}});n=page;apply(true);track('area_filter',{filter:'clear',value:''});});}
  if(more){more.addEventListener('click',function(e){e.preventDefault();var was=n;n+=page;apply();var nf=grid.querySelectorAll('.bkn-card:not([hidden]) a.bkn-a')[was];if(nf){nf.focus({preventScroll:true});}track('area_more',{shown:n});});} /* the first card it added */
  apply(false);
})();
JS;
	}

	/* ---------------------------------------------------------------------
	   @v16-search: search results on the new layout (/advanced-search/)
	   --------------------------------------------------------------------- */

	/** The results page (page 9, /advanced-search/, on block.ke); config search_page overrides it. */
	function blockke_search_page_id() {
		$cfg = blockke_dev_config();
		return (int) ( isset( $cfg['search_page'] ) ? $cfg['search_page'] : 9 );
	}

	/** Our type words (?type= and the tabs): slug => array( kind, type ('' = the whole kind), noun ). */
	function blockke_search_types() {
		return array(
			'homes'      => array( 'home', '', 'homes' ),
			'apartments' => array( 'home', 'apartment', 'apartments' ),
			'villas'     => array( 'home', 'villa', 'villas' ),
			'houses'     => array( 'home', 'house', 'houses' ),
			'studios'    => array( 'home', 'studio', 'studios' ),
			'commercial' => array( 'commercial', '', 'commercial property' ),
			'offices'    => array( 'commercial', 'office', 'offices' ),
			'warehouses' => array( 'commercial', 'warehouse', 'warehouses' ),
			'shops'      => array( 'commercial', 'shop', 'shops' ),
			'land'       => array( 'land', '', 'land' ),
		);
	}

	/** The site's categories (property_category[] from the theme's forms) that mean one of our types; the rest filter as categories. */
	function blockke_search_cat_types() {
		$cfg = blockke_dev_config();
		$map = array(
			'residential'        => 'homes',
			'apartments'         => 'apartments',
			'villas-residential' => 'villas',
			'houses-residential' => 'houses',
			'studio-apartments'  => 'studios',
			'commercial'         => 'commercial',
			'offices'            => 'offices',
			'warehouses'         => 'warehouses',
			'shop'               => 'shops',
			'land'               => 'land',
		);
		if ( '' !== (string) $cfg['studio_cat'] ) {
			$map[ (string) $cfg['studio_cat'] ] = 'studios';
		}
		return $map;
	}

	/** 8, 12.5: a band end for URLs and labels. */
	function blockke_search_num( $v ) {
		return rtrim( rtrim( number_format( (float) $v, 2, '.', '' ), '0' ), '.' );
	}

	/**
	 * "Under KES 8M", "KES 8–12M", "KES 60M and above"; rents "KES 80–150K a month", "KES 300K a month and above". Built from the
	 * numbers, not the config labels.
	 */
	function blockke_search_band_label( $lo, $hi, $rent ) {
		$cur  = blockke_dev_config()['currency'];
		$unit = $rent ? 1000 : 1000000;
		$sfx  = ( $rent ? 'K' : 'M' );
		$m    = $rent ? ' a month' : '';
		if ( $lo <= 0 && $hi > 0 ) {
			return 'Under ' . $cur . ' ' . blockke_search_num( $hi / $unit ) . $sfx . $m;
		}
		if ( $hi <= 0 ) {
			return $cur . ' ' . blockke_search_num( $lo / $unit ) . $sfx . $m . ' and above';
		}
		return $cur . ' ' . blockke_search_num( $lo / $unit ) . '–' . blockke_search_num( $hi / $unit ) . $sfx . $m;
	}

	/** Price bands from config budget_bands: "lo-hi" (KES millions; thousands for rents) => label. */
	function blockke_search_bands( $rent ) {
		$cfg  = blockke_dev_config();
		$unit = $rent ? 1000 : 1000000;
		$list = isset( $cfg['budget_bands'][ $rent ? 'rent' : 'sale' ] ) ? (array) $cfg['budget_bands'][ $rent ? 'rent' : 'sale' ] : array();
		$out  = array();
		foreach ( $list as $b ) {
			if ( is_array( $b ) && count( $b ) >= 2 ) {
				$key         = blockke_search_num( $b[0] / $unit ) . '-' . ( (float) $b[1] > 0 ? blockke_search_num( $b[1] / $unit ) : '' );
				$out[ $key ] = blockke_search_band_label( (float) $b[0], (float) $b[1], $rent );
			}
		}
		return $out;
	}

	/** "Studio", "2 bed", "4+ bed" ("2+ bed" when the site reads bedrooms as a minimum, config beds_mode). */
	function blockke_search_beds_label( $b ) {
		if ( 'studio' === $b ) {
			return 'Studio';
		}
		return $b . ( '4' === (string) $b || 'min' === blockke_dev_config()['beds_mode'] ? '+' : '' ) . ' bed';
	}

	/** "2027", "2029 and later", "Ready now" for 41011's completion slugs. */
	function blockke_search_done_label( $slug ) {
		if ( 'ready-now' === $slug ) {
			return 'Ready now';
		}
		return preg_match( '/^(\d{4})-plus$/', $slug, $m ) ? $m[1] . ' and later' : $slug;
	}

	/** Whether an editor is previewing (?bke_search=1): links and the bar keep the preview. */
	function blockke_search_preview() {
		return ! empty( $GLOBALS['blockke_search_preview'] );
	}

	/** The development a listing belongs to, or null. */
	function blockke_search_dev_of( $id ) {
		$d = blockke_bkn_devs();
		return isset( $d['of'][ $id ], $d['devs'][ $d['of'][ $id ] ] ) ? $d['devs'][ $d['of'][ $id ] ] : null;
	}

	/** Areas offered in the bar: main areas of at least 3 developments, busiest first (slug => name). The rest are "Other areas". */
	function blockke_search_areas() {
		static $out = null;
		if ( null !== $out ) {
			return $out;
		}
		$n = array();
		foreach ( blockke_bkn_devs()['devs'] as $d ) {
			if ( '' !== $d['area'] ) {
				$n[ $d['area'] ] = ( isset( $n[ $d['area'] ] ) ? $n[ $d['area'] ] : 0 ) + 1;
			}
		}
		arsort( $n );
		$out = array();
		foreach ( $n as $slug => $c ) {
			$t = $c >= 3 ? get_term_by( 'slug', $slug, 'property_area' ) : null;
			if ( $t instanceof WP_Term ) {
				$out[ $slug ] = blockke_dev_text( $t->name );
			}
		}
		return $out;
	}

	/**
	 * Reads a results-page query: the theme's fields (property_action_category[], property_category[], completion), the aliases in
	 * config search_params, and our clean ones (for, type, area, beds, price, completion, sort, n). Returns null for anything it
	 * cannot read (a keyword, an MLS number, a saved search, an unknown area or value): the theme then renders the page as today.
	 * The key that stopped it is kept in $GLOBALS['blockke_search_why'] for the admin bar.
	 */
	function blockke_search_parse( $get = null ) {
		$cfg = blockke_dev_config();
		$get = null === $get ? wp_unslash( $_GET ) : $get; // phpcs:ignore WordPress.Security.NonceVerification
		$GLOBALS['blockke_search_why'] = '';
		$q    = array( 'for' => array(), 'types' => array(), 'cats' => array(), 'area' => array(), 'beds' => '', 'price' => '', 'lo' => 0, 'hi' => 0, 'completion' => '', 'sort' => '', 'n' => 0 );
		$role = array( 'area' => 'area', 'beds' => 'beds' );
		$al   = isset( $cfg['search_params'] ) && is_array( $cfg['search_params'] ) ? $cfg['search_params'] : array();
		foreach ( array( 'area', 'beds', 'min', 'max' ) as $r ) {
			foreach ( isset( $al[ $r ] ) ? (array) $al[ $r ] : array() as $k ) {
				$role[ (string) $k ] = $r;
			}
		}
		$ignore   = array_map( 'strval', isset( $cfg['search_ignore'] ) ? (array) $cfg['search_ignore'] : array() );
		$ignore[] = 'bke_search'; // the editor preview
		$ignore[] = 'blockpop';   // the price-check popup's own switch (set by our pages too)
		$cats     = blockke_search_cat_types();
		$types    = blockke_search_types();
		$min      = 0;
		$max      = 0;
		foreach ( is_array( $get ) ? $get : array() as $k => $v ) {
			$k = (string) $k;
			if ( in_array( $k, $ignore, true ) ) {
				continue;
			}
			$vals = array();
			foreach ( is_array( $v ) ? $v : array( $v ) as $x ) {
				if ( is_array( $x ) || strlen( (string) $x ) > 100 ) {
					$GLOBALS['blockke_search_why'] = $k;
					return null;
				}
				$x = trim( (string) $x );
				if ( '' !== $x && ! in_array( strtolower( $x ), array( 'all', 'any' ), true ) ) {
					$vals[] = $x;
				}
			}
			if ( ! $vals ) {
				continue; // empty fields, "all" and "any" ask for nothing
			}
			if ( in_array( $k, array( 'area', 'type' ), true ) ) { // our own lists are comma-separated
				$vals = array_values( array_filter( array_map( 'trim', explode( ',', implode( ',', $vals ) ) ), 'strlen' ) );
			}
			$ok = true;
			$rk = isset( $role[ $k ] ) ? $role[ $k ] : $k;
			switch ( $rk ) {
				case 'property_action_category':
				case 'for':
					foreach ( $vals as $x ) {
						$x = strtolower( $x );
						if ( in_array( $x, array( 'for-sale', 'sale', 'buy' ), true ) ) {
							$q['for'][] = 'sale';
						} elseif ( in_array( $x, array( 'for-rent', 'rent', 'to-rent' ), true ) ) {
							$q['for'][] = 'rent';
						} else {
							$ok = false;
						}
					}
					break;
				case 'property_category':
					foreach ( $vals as $x ) {
						$x = sanitize_title( $x );
						if ( isset( $cats[ $x ] ) ) {
							$q['types'][] = $cats[ $x ];
						} elseif ( '' !== $x && term_exists( $x, 'property_category' ) ) {
							$q['cats'][] = $x;
						} else {
							$ok = false;
						}
					}
					break;
				case 'type':
					foreach ( $vals as $x ) {
						$x = sanitize_title( $x );
						if ( preg_match( '/^([a-z]+)-for-(sale|rent)$/', $x, $m ) ) { // a segment slug: apartments-for-sale
							$x          = $m[1];
							$q['for'][] = $m[2];
						}
						if ( isset( $types[ $x ] ) ) {
							$q['types'][] = $x;
						} else {
							$ok = false;
						}
					}
					break;
				case 'area':
					foreach ( $vals as $x ) {
						if ( 'other' === strtolower( $x ) ) {
							$q['area'][] = 'other';
							continue;
						}
						$t = get_term_by( 'slug', sanitize_title( $x ), 'property_area' ); // slug first, then the name the theme's forms send
						$t = $t instanceof WP_Term ? $t : get_term_by( 'name', $x, 'property_area' );
						if ( $t instanceof WP_Term ) {
							$q['area'][] = $t->slug;
						} else {
							$ok = false;
						}
					}
					break;
				case 'beds':
					$x = strtolower( $vals[0] );
					if ( 'studio' === $x || '0' === $x ) {
						$q['beds'] = 'studio';
					} elseif ( preg_match( '/^([1-9]\d?)\+?$/', $x, $m ) ) {
						$q['beds'] = (string) min( 4, (int) $m[1] ); // 4 = four and more
					} else {
						$ok = false;
					}
					break;
				case 'min':
				case 'max':
					$x = preg_replace( '/^kes|[\s,]/i', '', $vals[0] );
					if ( ! is_numeric( $x ) || (float) $x < 0 ) {
						$ok = false;
					} elseif ( 'min' === $rk ) {
						$min = (float) $x;
					} else {
						$max = 500000000 === (int) $x ? 0 : (float) $x; // the theme's slider end means no limit
					}
					break;
				case 'price':
					if ( preg_match( '/^\d{1,6}(?:\.\d{1,2})?-(?:\d{1,6}(?:\.\d{1,2})?)?$/', $vals[0] ) ) {
						$q['price'] = $vals[0];
					} else {
						$ok = false;
					}
					break;
				case 'completion':
					$x = strtolower( $vals[0] );
					if ( preg_match( '/^(?:20\d\d(?:-plus)?|ready-now)$/', $x ) ) {
						$q['completion'] = $x;
					} else {
						$ok = false;
					}
					break;
				case 'sort':
					$q['sort'] = in_array( $vals[0], array( 'soonest', 'price-asc', 'price-desc', 'newest' ), true ) ? $vals[0] : '';
					break;
				case 'n':
					$q['n'] = ctype_digit( $vals[0] ) ? (int) $vals[0] : 0;
					break;
				default:
					$ok = false;
			}
			if ( ! $ok ) {
				$GLOBALS['blockke_search_why'] = $k;
				return null;
			}
		}
		$q['for'] = array_values( array_intersect( array( 'sale', 'rent' ), $q['for'] ) );
		if ( 2 === count( $q['for'] ) ) {
			$q['for'] = array(); // both is either
		}
		$q['types'] = array_values( array_unique( $q['types'] ) );
		if ( in_array( 'homes', $q['types'], true ) ) { // "homes" already holds apartments, villas, houses and studios
			$q['types'] = array_values( array_diff( $q['types'], array( 'apartments', 'villas', 'houses', 'studios' ) ) );
		}
		$q['cats'] = array_values( array_unique( $q['cats'] ) );
		$q['area'] = array_values( array_unique( $q['area'] ) );
		// Price: ours ("8-12": KES millions, thousands for rents), else the theme's slider ends in KES
		$unit = array( 'rent' ) === $q['for'] ? 1000 : 1000000;
		if ( '' !== $q['price'] ) {
			$pp      = explode( '-', $q['price'] );
			$q['lo'] = (float) $pp[0] * $unit;
			$q['hi'] = '' !== $pp[1] ? (float) $pp[1] * $unit : 0;
		} elseif ( $min > 0 || $max > 0 ) {
			$q['lo']    = $min;
			$q['hi']    = $max;
			$q['price'] = blockke_search_num( $min / $unit ) . '-' . ( $max > 0 ? blockke_search_num( $max / $unit ) : '' );
		}
		if ( $q['hi'] > 0 && $q['hi'] <= $q['lo'] ) {
			$GLOBALS['blockke_search_why'] = 'price';
			return null;
		}
		if ( $q['lo'] <= 0 && $q['hi'] <= 0 ) {
			$q['price'] = '';
		}
		// n: cards shown per section, in steps of index_page_size (18), at most 240
		$step   = max( 6, (int) $cfg['index_page_size'] );
		$q['n'] = $q['n'] > $step ? (int) min( 240, ceil( $q['n'] / $step ) * $step ) : 0;
		return $q;
	}

	/** A results URL for a query, with some fields changed. Our clean parameters only, so the theme's are translated on the first refinement. */
	function blockke_search_url( $q, $set = array(), $preview = true ) {
		$q = array_merge( $q, $set );
		$a = array();
		if ( 1 === count( $q['for'] ) ) {
			$a['for'] = $q['for'][0];
		}
		if ( $q['types'] ) {
			$a['type'] = implode( ',', $q['types'] );
		}
		if ( $q['area'] ) {
			$a['area'] = implode( ',', $q['area'] );
		}
		foreach ( array( 'beds', 'price', 'completion', 'sort' ) as $k ) {
			if ( '' !== (string) $q[ $k ] ) {
				$a[ $k ] = (string) $q[ $k ];
			}
		}
		if ( $q['n'] ) {
			$a['n'] = (int) $q['n'];
		}
		if ( $preview && blockke_search_preview() ) {
			$a['bke_search'] = '1';
		}
		$url = add_query_arg( array_map( 'rawurlencode', $a ), get_permalink( blockke_search_page_id() ) );
		foreach ( $q['cats'] as $i => $c ) {
			$url .= ( false === strpos( $url, '?' ) ? '?' : '&' ) . rawurlencode( 'property_category[' . $i . ']' ) . '=' . rawurlencode( $c );
		}
		return $url;
	}

	/** The bedroom keys a listing offers ('studio', '1' … '4' for four and more): its own, a range in its title, or its development's. */
	function blockke_search_row_beds( $r ) {
		static $memo = array();
		if ( isset( $memo[ $r['id'] ] ) ) {
			return $memo[ $r['id'] ];
		}
		$k = array();
		if ( 'home' === $r['kind'] ) {
			if ( ! $r['dev'] && '' !== $r['k'] ) {
				$k = array( $r['k'] );
			} else {
				$tb = $r['dev'] ? array( '', array() ) : blockke_area_title_beds( $r['id'], true ); // "2 & 3 Bedroom Apartments"
				$k  = $tb[1];
				$d  = $k ? null : blockke_search_dev_of( $r['id'] );
				foreach ( $d ? $d['beds'] : array() as $b ) {
					$k[] = 0 === (int) $b ? 'studio' : (string) min( 4, (int) $b );
				}
			}
		}
		$memo[ $r['id'] ] = array_values( array_unique( array_map( 'strval', $k ) ) );
		return $memo[ $r['id'] ];
	}

	/** Whether a listing row answers the query. $skip: one filter to leave out ('type', 'area', 'beds', 'price', 'completion'). */
	function blockke_search_ok( $r, $q, $skip = '' ) {
		if ( '' === $r['act'] || ( $q['for'] && ! in_array( $r['act'], $q['for'], true ) ) ) {
			return false;
		}
		if ( 'type' !== $skip && ( $q['types'] || $q['cats'] ) ) {
			$tt = blockke_search_types();
			$ok = (bool) array_intersect( $r['cats'], $q['cats'] );
			foreach ( $q['types'] as $t ) {
				$ok = $ok || ( $r['kind'] === $tt[ $t ][0] && ( '' === $tt[ $t ][1] || $r['type'] === $tt[ $t ][1] ) );
			}
			if ( ! $ok ) {
				return false;
			}
		}
		if ( 'area' !== $skip && $q['area'] ) {
			$ok  = false;
			$top = blockke_search_areas();
			foreach ( $q['area'] as $a ) {
				if ( 'other' === $a ) {
					$ok = $ok || ( '' !== $r['ma'] && ! isset( $top[ $r['ma'] ] ) );
				} else {
					$ok = $ok || $a === $r['ma'] || in_array( $a, $r['areas'], true );
				}
			}
			if ( ! $ok ) {
				return false;
			}
		}
		if ( 'beds' !== $skip && '' !== $q['beds'] ) {
			if ( 'home' !== $r['kind'] ) {
				return false;
			}
			$want = 'studio' === $q['beds'] ? 0 : (int) $q['beds'];
			$min  = 4 === $want || 'min' === blockke_dev_config()['beds_mode'];
			$ok   = false;
			foreach ( blockke_search_row_beds( $r ) as $k ) {
				$have = 'studio' === $k ? 0 : (int) $k;
				$ok   = $ok || ( 0 === $want ? 0 === $have : ( $min ? $have >= $want : $have === $want ) );
			}
			if ( ! $ok ) {
				return false;
			}
		}
		if ( 'price' !== $skip && ( $q['lo'] > 0 || $q['hi'] > 0 ) ) {
			if ( 'unit' === $r['f'] ) {
				return false; // a price per square foot can't be compared with a band
			}
			$lo = $r['p'];
			$hi = $r['p'];
			if ( $r['dev'] ) { // a development's main listing: its from-to range overlaps the band
				$d  = blockke_search_dev_of( $r['id'] );
				$lo = $d ? ( 'rent' === $r['act'] ? $d['rfrom'] : $d['from'] ) : 0;
				$hi = $d ? ( 'rent' === $r['act'] ? $d['rto'] : $d['to'] ) : 0;
			}
			if ( $lo <= 0 || ( $q['hi'] > 0 && $lo >= $q['hi'] ) || $hi < $q['lo'] ) {
				return false;
			}
		}
		if ( 'completion' !== $skip && '' !== $q['completion'] ) {
			$c = $q['completion'];
			if ( 'ready-now' === $c ) {
				if ( 'ready' !== $r['st'] && 'ready-now' !== $r['ct'] ) {
					return false;
				}
			} else {
				$y = preg_match( '/^(20\d\d)/', $r['ct'], $m ) ? (int) $m[1] : 0;
				if ( ! $y && '' === $r['ct'] && 'ready' !== $r['st'] ) { // no Completion term: its development's stated date
					$d = blockke_search_dev_of( $r['id'] );
					$y = $d && ! $d['done'][4] ? (int) $d['done'][0] : 0;
				}
				$cy = (int) substr( $c, 0, 4 );
				if ( ! $y || ( '-plus' === substr( $c, 4 ) ? $y < $cy : $y !== $cy ) ) {
					return false;
				}
			}
		}
		return true;
	}

	/** A listing for several sizes ("Studio, 1-Bed & 2-Bed Apartments"): it stands for its development, not for one home. */
	function blockke_search_is_range( $r ) {
		return 'home' === $r['kind'] && ! $r['dev'] && '' === $r['k'];
	}

	/** Bedroom ranges are read from titles: load those listings in one query, once. */
	function blockke_search_prime_titles() {
		static $done = false;
		if ( $done || ! function_exists( '_prime_post_caches' ) ) {
			return;
		}
		$done = true;
		$ids  = array();
		foreach ( blockke_bkn_rows() as $id => $r ) {
			if ( blockke_search_is_range( $r ) ) {
				$ids[] = $id;
			}
		}
		_prime_post_caches( $ids, false, false );
	}

	/** Matching listings grouped: section ("home|sale") => development group key => listing IDs. Homes, commercial, land; sale before rent. */
	function blockke_search_match( $q, $skip = '' ) {
		$rows = blockke_bkn_rows();
		$of   = blockke_bkn_devs()['of'];
		if ( '' !== $q['beds'] && 'beds' !== $skip ) {
			blockke_search_prime_titles();
		}
		$out = array();
		foreach ( array( 'home', 'commercial', 'land' ) as $kind ) {
			foreach ( array( 'sale', 'rent' ) as $act ) {
				$out[ $kind . '|' . $act ] = array();
			}
		}
		foreach ( $rows as $id => $r ) {
			$key = $r['kind'] . '|' . $r['act'];
			if ( isset( $of[ $id ], $out[ $key ] ) && blockke_search_ok( $r, $q, $skip ) ) {
				$out[ $key ][ $of[ $id ] ][] = $id;
			}
		}
		return array_filter( $out );
	}

	/** How many cards (developments, or single homes) a query finds. */
	function blockke_search_count( $q, $skip = '' ) {
		$n = 0;
		foreach ( blockke_search_match( $q, $skip ) as $groups ) {
			$n += count( $groups );
		}
		return $n;
	}

	/** One section's cards, sorted: what each shows and how it sorts. Recommended is the theme's order: featured, then newest. */
	function blockke_search_cards( $groups, $key, $q ) {
		list( $kind, $act ) = explode( '|', $key );
		$rows               = blockke_bkn_rows();
		$devs               = blockke_bkn_devs()['devs'];
		$out                = array();
		foreach ( $groups as $g => $ids ) {
			if ( ! isset( $devs[ $g ] ) ) {
				continue;
			}
			$d     = $devs[ $g ];
			$units = array();
			$ft    = (bool) $d['ft'];
			$dt    = 0;
			$lo    = 0;
			$hi    = 0;
			foreach ( $ids as $id ) {
				$r  = $rows[ $id ];
				$ft = $ft || $r['ft'];
				$dt = max( $dt, (int) $r['d'] );
				if ( $r['dev'] ) {
					$a = 'rent' === $act ? $d['rfrom'] : $d['from'];
					$b = 'rent' === $act ? $d['rto'] : $d['to'];
				} elseif ( blockke_search_is_range( $r ) ) { // the development matches, with no single home to list
					$a = 'unit' === $r['f'] ? 0 : $r['p'];
					$b = $a;
				} else {
					$units[] = $r;
					$a       = 'unit' === $r['f'] ? 0 : $r['p'];
					$b       = $a;
				}
				if ( $a > 0 ) {
					$lo = $lo > 0 ? min( $lo, $a ) : $a;
					$hi = max( $hi, $b );
				}
			}
			usort(
				$units,
				function ( $x, $y ) {
					$px = $x['p'] > 0 && 'unit' !== $x['f'] ? $x['p'] : PHP_INT_MAX;
					$py = $y['p'] > 0 && 'unit' !== $y['f'] ? $y['p'] : PHP_INT_MAX;
					return $px === $py ? $x['id'] - $y['id'] : ( $px < $py ? -1 : 1 );
				}
			);
			// A single home gets the price-first card: the development's only listing in this section (one flat to rent in a
			// building selling off-plan is a single home here). A listing for a whole development (a range of sizes) keeps the development card
			$here = 0;
			foreach ( $d['ids'] as $id ) {
				$here += isset( $rows[ $id ] ) && $rows[ $id ]['act'] === $act && $rows[ $id ]['kind'] === $kind ? 1 : 0;
			}
			$unit  = 1 === $here && 1 === count( $units );
			$out[] = array(
				'g'     => $g,
				'units' => $units,
				'v'     => $unit ? 'unit' : 'group',
				'aid'   => $unit ? (int) $units[0]['id'] : (int) $d['id'],
				'img'   => $unit && $units[0]['img'] ? (int) $units[0]['img'] : (int) $d['cover'],
				'pub'   => $unit ? (int) $units[0]['d'] : ( isset( $rows[ $d['id'] ] ) ? (int) $rows[ $d['id'] ]['d'] : (int) $d['d'] ),
				'ft'    => $ft,
				'd'     => $dt,
				'lo'    => $lo,
				'hi'    => $hi,
				'done'  => blockke_bkn_done_key( $d ),
				'homes' => max( 1, count( $units ) ),
			);
		}
		$sort = $q['sort'];
		usort(
			$out,
			function ( $a, $b ) use ( $sort ) {
				if ( 'price-asc' === $sort ) {
					$x = $a['lo'] > 0 ? $a['lo'] : PHP_INT_MAX;
					$y = $b['lo'] > 0 ? $b['lo'] : PHP_INT_MAX;
					if ( $x !== $y ) {
						return $x < $y ? -1 : 1;
					}
				} elseif ( 'price-desc' === $sort ) {
					if ( $a['hi'] !== $b['hi'] ) {
						return $a['hi'] > $b['hi'] ? -1 : 1;
					}
				} elseif ( 'soonest' === $sort ) {
					if ( $a['done'] !== $b['done'] ) {
						return $a['done'] - $b['done'];
					}
				} elseif ( 'newest' !== $sort && $a['ft'] !== $b['ft'] ) {
					return $a['ft'] ? -1 : 1;
				}
				return $b['d'] !== $a['d'] ? $b['d'] - $a['d'] : strcmp( $a['g'], $b['g'] );
			}
		);
		return $out;
	}

	/**
	 * One card: a single home, else 'group' with the development's homes that match. A single home is set like every other card
	 * (its building's name, then what it is, then the price), so a row never mixes two kinds of card. With one area searched,
	 * the place line leaves that area out, as on the area pages.
	 */
	function blockke_search_card_html( $card, $sec, $q, $pos, $tag, $lazy ) {
		$d    = blockke_bkn_devs()['devs'][ $card['g'] ];
		$rent = 'rent' === $sec['act'];
		$home = 'home' === $sec['kind'];
		$ctx  = array( 'src' => 'search', 'scope' => 1 === count( $q['area'] ) && 'other' !== $q['area'][0] ? $q['area'][0] : '', 'pos' => $pos, 'rent' => $rent, 'tag' => $tag, 'lazy' => $lazy );
		$c    = $d;
		if ( 'unit' === $card['v'] ) {
			$r           = $card['units'][0];
			$p           = 'unit' === $r['f'] ? 0 : (float) $r['p'];
			$c['id']     = (int) $r['id'];
			$c['url']    = $r['u'];
			$c['from']   = $rent ? 0 : $p;
			$c['to']     = $c['from'];
			$c['rfrom']  = $rent ? $p : 0;
			$c['rto']    = $c['rfrom'];
			$c['beds']   = $home && '' !== $r['k'] ? array( 'studio' === $r['k'] ? 0 : max( 1, (int) $r['b'] ) ) : array();
			$c['types']  = '' !== $r['type'] ? array( $r['type'] ) : array();
			$c['sqm']    = $r['sq'] > 0 ? round( $r['sq'] * blockke_dev_sqft_factor() ) : 0;
			$c['cover']  = $r['img'] ? (int) $r['img'] : $d['cover'];
			$c['areas']  = $r['areas'] ? $r['areas'] : $d['areas'];
			$c['vt']     = (bool) $r['lo'];
			$c['d']      = (int) $r['d'];
			$h           = blockke_card_html( $c, 'dev', $ctx );
			// its size after the bedrooms: "Kilimani · 1 bed · 60 m²" (a place line of its own when the area is left out and there are no bedrooms)
			if ( $c['sqm'] > 0 ) {
				$sq = '<span class="bkn-nw">' . esc_html( blockke_dev_num( $c['sqm'] ) . ' m²' ) . '</span>';
				$h  = false !== strpos( $h, '<p class="bkn-place">' ) ? preg_replace( '#(<p class="bkn-place">.*?)</p>#', '$1 · ' . $sq . '</p>', $h, 1 ) : preg_replace( '#</a>#', '</a><p class="bkn-place">' . $sq . '</p>', $h, 1 );
			}
			return preg_replace( '/^<article class="bkn-card"/', '<article class="bkn-card bks-home"', $h ); // bks-home: the click event's kind 'unit'
		}
		if ( ! $home ) {
			$c['beds'] = array();
		} else { // every size the development lists, its range listings' titles included ("Studio, 1-Bed & 2-Bed")
			$rows = blockke_bkn_rows();
			blockke_search_prime_titles();
			foreach ( $d['ids'] as $id ) {
				foreach ( isset( $rows[ $id ] ) && $rows[ $id ]['act'] === $sec['act'] ? blockke_search_row_beds( $rows[ $id ] ) : array() as $k ) {
					$c['beds'][] = 'studio' === $k ? 0 : (int) $k;
				}
			}
			$c['beds'] = array_values( array_unique( array_map( 'intval', $c['beds'] ) ) );
			sort( $c['beds'] );
		}
		if ( ( $q['lo'] > 0 || $q['hi'] > 0 ) && $card['lo'] > 0 ) { // with a price filter: the range of the homes that match
			$c[ $rent ? 'rfrom' : 'from' ] = $card['lo'];
			$c[ $rent ? 'rto' : 'to' ]     = $card['hi'];
			$ctx['range']                  = true;
		}
		$match      = $card['units'];
		$c['match'] = $match;
		$h          = blockke_card_html( $c, 'group', $ctx );
		if ( '' !== $q['beds'] && '4' !== $q['beds'] && 'min' !== blockke_dev_config()['beds_mode'] ) { // "2 homes match", not ": 2 bed" again under "2 bed homes for sale"
			$h = preg_replace( '#(<p class="bkn-mh">[^<:]*):[^<]*</p>#', '$1</p>', $h, 1 );
		}
		foreach ( array_slice( $match, 0, 3 ) as $i => $r ) { // the home's ID, for search_match_click
			$h = preg_replace( '/data-row="' . ( $i + 1 ) . '"/', 'data-row="' . ( $i + 1 ) . '" data-unit="' . (int) $r['id'] . '"', $h, 1 );
		}
		return $h;
	}

	/** A result with no photo at all, as a line in "Also listed": name, then where, what and the price. Never a placeholder tile. */
	function blockke_search_text_item( $card, $sec ) {
		$d    = blockke_bkn_devs()['devs'][ $card['g'] ];
		$rent = 'rent' === $sec['act'];
		if ( 'unit' === $card['v'] ) {
			$r     = $card['units'][0];
			$p     = 'unit' === $r['f'] ? 0 : (float) $r['p'];
			$url   = $r['u'];
			$what  = 'home' === $r['kind'] && '' !== $r['k'] ? blockke_bkn_beds( array( 'studio' === $r['k'] ? 0 : max( 1, (int) $r['b'] ) ) ) : '';
			$price = blockke_bkn_price( $p, $p, $rent );
			$id    = (int) $r['id'];
		} else {
			$url   = $d['url'];
			$what  = 'home' === $sec['kind'] ? blockke_bkn_beds( $d['beds'], $d['types'] ) : '';
			$price = $rent ? blockke_bkn_price( $d['rfrom'], $d['rto'], true ) : blockke_bkn_price( $d['from'], $d['to'] );
			$id    = (int) $d['id'];
		}
		$bits = array_filter( array( $d['place'], $what, $price ) );
		return '<li><a href="' . esc_url( $url ) . '" data-bkn="search" data-bks-text="' . $id . '">' . esc_html( $d['name'] ) . '</a>' . ( $bits ? '<span>' . esc_html( implode( ' · ', $bits ) ) . '</span>' : '' ) . '</li>';
	}

	/**
	 * The query in words: "2 bed apartments for sale in Westlands", "studios for rent", "land for lease", "property for sale". The
	 * same phrases as the area pages and their addresses (homes-for-rent). $kinds and $acts: what the results hold (they name a
	 * query that asked for any kind or either action).
	 */
	function blockke_search_words( $q, $kinds, $acts, $beds = true, $area = true ) {
		$tt    = blockke_search_types();
		$nouns = array();
		foreach ( $q['types'] as $t ) {
			$nouns[] = $tt[ $t ][2];
		}
		foreach ( $q['cats'] as $c ) {
			$t = get_term_by( 'slug', $c, 'property_category' );
			if ( $t instanceof WP_Term ) {
				$nouns[] = strtolower( blockke_dev_text( $t->name ) );
			}
		}
		if ( ! $nouns ) {
			$nouns[] = '' !== $q['beds'] || array( 'home' ) === array_keys( $kinds ) ? 'homes' : 'property';
		}
		$noun = 1 === count( $nouns ) ? $nouns[0] : implode( ', ', array_slice( $nouns, 0, -1 ) ) . ' and ' . end( $nouns );
		if ( $beds && '' !== $q['beds'] ) {
			$noun = 'studio' === $q['beds'] && in_array( $noun, array( 'homes', 'apartments', 'studios', 'property' ), true ) ? 'studios' : strtolower( blockke_search_beds_label( $q['beds'] ) ) . ' ' . $noun;
		}
		$a   = $q['for'] ? $q['for'] : $acts;
		$act = 2 === count( $a ) ? ( 'land' === $noun ? 'for sale and lease' : 'for sale and rent' ) : ( 1 === count( $a ) ? ( 'rent' === $a[0] ? ( 'land' === $noun ? 'for lease' : 'for rent' ) : 'for sale' ) : '' );
		$in  = '';
		if ( $area && $q['area'] ) {
			$names = array();
			foreach ( $q['area'] as $s ) {
				$t       = 'other' === $s ? null : get_term_by( 'slug', $s, 'property_area' );
				$names[] = 'other' === $s ? 'other areas' : ( $t instanceof WP_Term ? blockke_dev_text( $t->name ) : $s );
			}
			$in = 'in ' . ( 1 === count( $names ) ? $names[0] : implode( ', ', array_slice( $names, 0, -1 ) ) . ' and ' . end( $names ) );
		}
		return implode( ' ', array_filter( array( $noun, $act, $in ) ) );
	}

	/**
	 * The price and completion asked for, mid-sentence, as the index says them: " at KES 8–12M completing in 2027", " from KES 60M",
	 * " under KES 8M ready to move into", " completing in 2029 and later".
	 */
	function blockke_search_tail( $q ) {
		$t = '';
		if ( $q['lo'] > 0 || $q['hi'] > 0 ) {
			$l  = blockke_search_band_label( $q['lo'], $q['hi'], array( 'rent' ) === $q['for'] );
			$t .= 0 === strpos( $l, 'Under' ) ? ' under' . substr( $l, 5 ) : ( $q['hi'] <= 0 ? ' from ' . preg_replace( '/ and above$/', '', $l ) : ' at ' . $l );
		}
		if ( '' !== $q['completion'] ) {
			$t .= 'ready-now' === $q['completion'] ? ' ready to move into' : ' completing in ' . blockke_search_done_label( $q['completion'] );
		}
		return $t;
	}

	/** The type tabs: array( label, URL, current ), only types this search finds (with its other filters), or none when there's one. */
	function blockke_search_tabs( $q ) {
		$rows = blockke_bkn_rows();
		$of   = blockke_bkn_devs()['of'];
		$tt   = blockke_search_types();
		$seen = array();
		foreach ( $rows as $id => $r ) {
			if ( ! isset( $of[ $id ] ) || ! blockke_search_ok( $r, $q, 'type' ) ) {
				continue;
			}
			foreach ( $tt as $slug => $t ) {
				if ( $r['kind'] === $t[0] && ( '' === $t[1] || $r['type'] === $t[1] ) ) {
					$seen[ $slug ][ $of[ $id ] ] = 1;
				}
			}
		}
		$cur   = $q['cats'] || count( $q['types'] ) > 1 ? null : ( $q['types'] ? $q['types'][0] : '' );
		$other = ! empty( $seen['commercial'] ) || ! empty( $seen['land'] ) || in_array( $cur, array( 'commercial', 'offices', 'warehouses', 'shops', 'land' ), true );
		$list  = $other ? array( '' => 'All', 'homes' => 'Homes' ) : array( '' => 'All homes' );
		if ( ! $other && 'homes' === $cur ) {
			$cur = ''; // only homes here: "All homes" is the same list
		}
		$more = array( 'apartments' => 'Apartments', 'villas' => 'Villas', 'houses' => 'Houses', 'studios' => 'Studios' );
		if ( $other ) {
			$more += array( 'commercial' => 'Commercial', 'offices' => 'Offices', 'warehouses' => 'Warehouses', 'shops' => 'Shops', 'land' => 'Land' );
		}
		foreach ( $more as $slug => $label ) {
			if ( ! empty( $seen[ $slug ] ) && ( ! in_array( $slug, array( 'offices', 'warehouses', 'shops' ), true ) || $slug === $cur ) ) {
				$list[ $slug ] = $label; // commercial sub-types only when asked for: few listings, one tab is enough
			} elseif ( $slug === $cur ) {
				$list[ $slug ] = $label;
			}
		}
		$out = array();
		foreach ( $list as $slug => $label ) {
			$slug  = (string) $slug;
			$out[] = array( $label, blockke_search_url( $q, array( 'types' => '' === $slug ? array() : array( $slug ), 'cats' => array(), 'n' => 0 ) ), $slug === $cur );
		}
		return count( $out ) > 1 ? $out : array();
	}

	/** With no or few results: up to three wider searches, by results gained (a filter dropped, or a nearby area), and more nearby areas. */
	function blockke_search_relax( $q, $have ) {
		$labels = array( 'completion' => 'Any completion', 'beds' => 'Any bedrooms', 'price' => 'Any price', 'type' => 'Any type', 'area' => 'Any area' );
		$clear  = array(
			'completion' => array( 'completion' => '' ),
			'beds'       => array( 'beds' => '' ),
			'price'      => array( 'price' => '', 'lo' => 0, 'hi' => 0 ),
			'type'       => array( 'types' => array(), 'cats' => array() ),
			'area'       => array( 'area' => array() ),
		);
		$set    = array(
			'completion' => '' !== $q['completion'],
			'beds'       => '' !== $q['beds'],
			'price'      => '' !== $q['price'],
			'type'       => $q['types'] || $q['cats'],
			'area'       => (bool) $q['area'],
		);
		$all    = array();
		foreach ( $labels as $k => $l ) {
			if ( $set[ $k ] ) {
				$n = blockke_search_count( $q, $k );
				if ( $n > $have ) {
					$all[] = array( $l, $n, blockke_search_url( $q, $clear[ $k ] + array( 'n' => 0 ) ), $k );
				}
			}
		}
		$near = array();
		if ( 1 === count( $q['area'] ) && 'other' !== $q['area'][0] ) {
			foreach ( blockke_dev_neighbours( $q['area'][0] ) as $ring ) {
				foreach ( $ring as $tid ) {
					$t = get_term( $tid, 'property_area' );
					if ( ! $t instanceof WP_Term ) {
						continue;
					}
					$n = blockke_search_count( array_merge( $q, array( 'area' => array( $t->slug ) ) ) );
					if ( $n > 0 ) {
						$near[] = array( blockke_dev_text( $t->name ), $n, blockke_search_url( $q, array( 'area' => array( $t->slug ), 'n' => 0 ) ), 'area:' . $t->slug );
					}
				}
			}
		}
		$top = array_merge( $all, $near );
		usort(
			$top,
			function ( $a, $b ) {
				return $b[1] - $a[1];
			}
		);
		$top  = array_slice( $top, 0, 3 );
		$used = wp_list_pluck( $top, 3 );
		$rest = array();
		foreach ( $near as $x ) {
			if ( ! in_array( $x[3], $used, true ) && count( $rest ) < 4 ) {
				$rest[] = $x;
			}
		}
		return array( $top, $rest );
	}

	/** "Map": the /search/ page (page 39161 on block.ke, config search_map_page) with the query in its own parameters, while it is published. */
	function blockke_search_map_url( $q ) {
		$cfg = blockke_dev_config();
		$pid = (int) ( isset( $cfg['search_map_page'] ) ? $cfg['search_map_page'] : 39161 );
		if ( ! $pid || 'publish' !== get_post_status( $pid ) ) {
			return '';
		}
		$a    = array();
		$cat  = array_flip( blockke_search_cat_types() ); // type => category slug
		$name = array();
		foreach ( array_merge( $q['types'], $q['cats'] ) as $x ) {
			$slug = isset( $cat[ $x ] ) && 'homes' !== $x ? $cat[ $x ] : ( in_array( $x, $q['cats'], true ) ? $x : '' );
			$t    = '' !== $slug ? get_term_by( 'slug', $slug, 'property_category' ) : null;
			if ( $t instanceof WP_Term ) {
				$name[] = blockke_dev_text( $t->name );
			}
		}
		if ( array( 'rent' ) === $q['for'] ) {
			$a['act'] = 'rent';
		}
		if ( 1 === count( $q['area'] ) && 'other' !== $q['area'][0] ) {
			$t = get_term_by( 'slug', $q['area'][0], 'property_area' );
			if ( $t instanceof WP_Term ) {
				$a['q'] = blockke_dev_text( $t->name );
			}
		}
		if ( $name ) {
			$a['type'] = implode( ',', $name );
		}
		if ( ctype_digit( $q['beds'] ) ) {
			$a['bd'] = $q['beds'];
		}
		if ( $q['lo'] > 0 ) {
			$a['pmin'] = (string) (int) $q['lo'];
		}
		if ( $q['hi'] > 0 ) {
			$a['pmax'] = (string) (int) $q['hi'];
		}
		return add_query_arg( array_map( 'rawurlencode', $a ), get_permalink( $pid ) );
	}

	/**
	 * Where a simple search belongs (config search_redirect), or '': one action and one area whose area page is on, one type (or
	 * bedrooms or a completion, which mean homes), a price band or none → that area's segment page; buying anywhere with a
	 * completion year → the new developments index. A search with no type stays on the results, which show every kind: a
	 * header search for "all types", or "Search property" on a commercial page, never lands on homes.
	 */
	function blockke_search_redirect_url( $q, $get = null ) {
		$cfg = blockke_dev_config();
		if ( $q['cats'] || count( $q['types'] ) > 1 || count( $q['area'] ) > 1 ) {
			return '';
		}
		$type = $q['types'] ? $q['types'][0] : ( '' !== $q['beds'] || '' !== $q['completion'] ? 'homes' : '' );
		$keep = array(); // campaign tags survive the redirect
		foreach ( (array) ( null === $get ? wp_unslash( $_GET ) : $get ) as $k => $v ) { // phpcs:ignore WordPress.Security.NonceVerification
			if ( is_string( $v ) && preg_match( '/^(?:utm_[a-z]+|gclid|gbraid|wbraid|fbclid|_gl)$/', (string) $k ) ) {
				$keep[ (string) $k ] = substr( $v, 0, 200 );
			}
		}
		$args = array();
		if ( 1 === count( $q['for'] ) && 1 === count( $q['area'] ) ) {
			if ( '' === $type ) {
				return '';
			}
			$term  = 'other' === $q['area'][0] ? null : get_term_by( 'slug', $q['area'][0], 'property_area' );
			$segs  = blockke_area_segments();
			$seg   = $type . '-for-' . $q['for'][0];
			$bands = blockke_search_bands( 'rent' === $q['for'][0] );
			if ( ! $term instanceof WP_Term || ! blockke_area_on( $term ) || ! isset( $segs[ $seg ] ) || ( '' !== $q['price'] && ! isset( $bands[ $q['price'] ] ) ) ) {
				return '';
			}
			if ( empty( $cfg['card_v16'] ) && ( '' !== $q['beds'] || '' !== $q['price'] || '' !== $q['completion'] || '' !== $q['sort'] ) ) {
				return ''; // v1.5 segment pages don't read these parameters: the visitor would lose them
			}
			// An empty segment page helps less than results with "Close matches". Counted from the cached rows: a WP_Query here
			// (blockke_area_data) would be narrowed by snippet 41011's ?completion= filter and cache the area page that way
			if ( ! blockke_search_count( array_merge( $q, array( 'beds' => '', 'price' => '', 'lo' => 0, 'hi' => 0, 'completion' => '' ) ) ) ) {
				return '';
			}
			foreach ( array( 'beds', 'price', 'completion', 'sort' ) as $k ) {
				if ( '' !== $q[ $k ] && ( 'completion' !== $k || 'sale' === $q['for'][0] ) ) {
					$args[ $k ] = $q[ $k ];
				}
			}
			$url = blockke_area_url( $term, $seg, false );
			return '' === $url ? '' : add_query_arg( array_map( 'rawurlencode', $args + $keep ), $url );
		}
		if ( ( ! $q['for'] || array( 'sale' ) === $q['for'] ) && ! $q['area'] && 'homes' === $type && '' !== $q['completion'] && blockke_bkn_on( 'dev_index', 'bke_index' ) ) {
			$pid   = (int) $cfg['dev_index_page'];
			$bands = blockke_search_bands( false );
			if ( ! $pid || 'publish' !== get_post_status( $pid ) || ( '' !== $q['price'] && ! isset( $bands[ $q['price'] ] ) ) ) {
				return '';
			}
			foreach ( array( 'completion', 'beds', 'price', 'sort' ) as $k ) {
				if ( '' !== $q[ $k ] ) {
					$args[ $k ] = $q[ $k ];
				}
			}
			return add_query_arg( array_map( 'rawurlencode', $args + $keep ), get_permalink( $pid ) );
		}
		return '';
	}

	/** Everything the results page shows for a query. */
	function blockke_search_state( $q ) {
		$cfg   = blockke_dev_config();
		$step  = max( 6, (int) $cfg['index_page_size'] );
		$tt    = blockke_search_types();
		$secs  = array();
		$kinds = array();
		$acts  = array();
		$devs  = 0;
		$homes = 0;
		foreach ( blockke_search_match( $q ) as $key => $groups ) {
			$ka    = explode( '|', $key );
			$cards = blockke_search_cards( $groups, $key, $q );
			if ( ! $cards ) {
				continue;
			}
			$kinds[ $ka[0] ] = 1;
			$acts[ $ka[1] ]  = 1;
			$devs           += count( $cards );
			foreach ( $cards as $c ) {
				$homes += $c['homes'];
			}
			$sub = array();
			foreach ( $q['types'] as $t ) {
				if ( $tt[ $t ][0] === $ka[0] && '' !== $tt[ $t ][1] ) {
					$sub[] = $t;
				}
			}
			$noun         = 1 === count( $sub ) ? ucfirst( $tt[ $sub[0] ][2] ) : ( 'home' === $ka[0] ? 'Homes' : ( 'land' === $ka[0] ? 'Land' : 'Commercial' ) );
			$secs[ $key ] = array(
				'kind'  => $ka[0],
				'act'   => $ka[1],
				'cards' => $cards,
				'h2'    => $noun . ( 'rent' === $ka[1] ? ( 'land' === $ka[0] ? ' for lease' : ' for rent' ) : ' for sale' ),
			);
		}
		$acts  = array_values( array_intersect( array( 'sale', 'rent' ), array_keys( $acts ) ) );
		$words = blockke_search_words( $q, $kinds, $acts );
		$only  = array( 'home' ) === array_keys( $kinds );
		$num   = function ( $n, $one, $many ) {
			return $n . ' ' . ( 1 === (int) $n ? $one : $many );
		};
		$url   = blockke_search_url( $q, array(), false );
		// WhatsApp: the search in words, then the link, worded as the index's ("Hi Block, I'm looking for apartments for sale:
		// Westlands, 2 bed, KES 8–12M, completing 2027. ..."; "studios", "under KES 8M", "completing 2029 and later", "ready now")
		$parts = array();
		foreach ( $q['area'] as $s ) {
			$t       = 'other' === $s ? null : get_term_by( 'slug', $s, 'property_area' );
			$parts[] = $t instanceof WP_Term ? blockke_dev_text( $t->name ) : 'other areas';
		}
		if ( '' !== $q['beds'] ) {
			$parts[] = 'studio' === $q['beds'] ? 'studios' : strtolower( blockke_search_beds_label( $q['beds'] ) );
		}
		if ( '' !== $q['price'] ) {
			$parts[] = preg_replace( '/^Under /', 'under ', blockke_search_band_label( $q['lo'], $q['hi'], array( 'rent' ) === $q['for'] ) );
		}
		if ( '' !== $q['completion'] ) {
			$parts[] = 'ready-now' === $q['completion'] ? 'ready now' : 'completing ' . blockke_search_done_label( $q['completion'] );
		}
		$ag    = blockke_dev_agent( 0 );
		$wa    = 'https://wa.me/' . $ag['wa'] . '?text=' . rawurlencode( 'Hi ' . $cfg['brand'] . ", I'm looking for " . blockke_search_words( $q, $kinds, $acts, false, false ) . ( $parts ? ': ' . implode( ', ', $parts ) : '' ) . '. Please send current options and price lists. ' . $url );
		// The count once, under the H1: "39 homes in 35 developments" only when some cards hold several homes; when each card is
		// one home (single rentals, resales, one flat per building), just "5 homes". Land is in projects, as on the index
		$count = '';
		if ( $devs ) {
			$count = $num( $homes, $only ? 'home' : 'listing', $only ? 'homes' : 'listings' );
			if ( $homes > $devs ) {
				$land   = array( 'land' ) === array_keys( $kinds );
				$count .= ' in ' . $num( $devs, $land ? 'project' : 'development', $land ? 'projects' : 'developments' );
			}
		}
		$filt  = array();
		foreach ( array( 'for' => implode( ',', $q['for'] ), 'type' => implode( ',', array_merge( $q['types'], $q['cats'] ) ), 'area' => implode( ',', $q['area'] ), 'beds' => $q['beds'], 'price' => $q['price'], 'completion' => $q['completion'] ) as $k => $v ) {
			if ( '' !== $v ) {
				$filt[] = $k . '=' . $v;
			}
		}
		$relax = $devs <= 2 ? blockke_search_relax( $q, $devs ) : array( array(), array() );
		return array(
			'q'      => $q,
			'secs'   => $secs,
			'devs'   => $devs,
			'homes'  => $homes,
			'step'   => $step,
			'show'   => $q['n'] ? $q['n'] : $step,
			'words'  => $words,
			'h1'     => ucfirst( $words ),
			'title'  => ucfirst( $words ) . ' | ' . $cfg['brand_full'],
			'count'  => $count,
			'none'   => 'No ' . $words . blockke_search_tail( $q ) . '.',
			'url'    => $url,
			'wa'     => $wa,
			'map'    => blockke_search_map_url( $q ),
			'tabs'   => blockke_search_tabs( $q ),
			'relax'  => $relax[0],
			'near'   => $relax[1],
			'filter' => implode( ',', $filt ),
			'nset'   => count( array_filter( array( (bool) $q['area'], '' !== $q['beds'], '' !== $q['price'], '' !== $q['completion'] ) ) ),
		);
	}

	/** The filter bar: a GET form (Apply without JavaScript; with it, changes apply at once), a sheet on phones. */
	function blockke_search_bar( $s ) {
		$q    = $s['q'];
		$cfg  = blockke_dev_config();
		$tt   = blockke_search_types();
		$rent = array( 'rent' ) === $q['for'];
		$sel  = function ( $name, $label, $opts, $cur, $attr = '' ) {
			$h = '<label class="bks-sel"' . ( 'completion' === $name ? ' data-bks-done' : '' ) . '><span class="bks-lab">' . esc_html( $label ) . '</span><select name="' . esc_attr( $name ) . '"' . ( '' !== (string) $cur ? ' class="on"' : '' ) . $attr . '>';
			foreach ( $opts as $v => $l ) {
				$h .= '<option value="' . esc_attr( (string) $v ) . '"' . selected( (string) $v, (string) $cur, false ) . '>' . esc_html( $l ) . '</option>';
			}
			return $h . '</select></label>';
		};
		$homes = ! $q['types'] && ! $q['cats'];
		foreach ( $q['types'] as $t ) {
			$homes = $homes || 'home' === $tt[ $t ][0];
		}
		$f    = array();
		$f[]  = $sel( 'for', 'Buy or rent', array( '' => 'Buy or rent', 'sale' => 'Buy', 'rent' => 'Rent' ), 1 === count( $q['for'] ) ? $q['for'][0] : '' );
		$opts = array( '' => 'Any area' ) + blockke_search_areas();
		foreach ( $q['area'] as $a ) {
			if ( ! isset( $opts[ $a ] ) && 'other' !== $a ) {
				$t          = get_term_by( 'slug', $a, 'property_area' );
				$opts[ $a ] = $t instanceof WP_Term ? blockke_dev_text( $t->name ) : $a;
			}
		}
		$cur = implode( ',', $q['area'] );
		if ( count( $q['area'] ) > 1 ) {
			$opts[ $cur ] = implode( ', ', array_map( function ( $a ) use ( $opts ) { return isset( $opts[ $a ] ) ? $opts[ $a ] : 'Other areas'; }, $q['area'] ) );
		}
		$opts['other'] = 'Other areas';
		$f[]           = $sel( 'area', 'Area', $opts, $cur );
		if ( $homes || '' !== $q['beds'] ) {
			$opts = array( '' => 'Any bedrooms' );
			foreach ( array( 'studio', '1', '2', '3', '4' ) as $b ) {
				$opts[ $b ] = blockke_search_beds_label( $b );
			}
			$f[] = $sel( 'beds', 'Bedrooms', $opts, $q['beds'] );
		}
		$bands = array();
		foreach ( array( 'sale', 'rent' ) as $m ) {
			foreach ( blockke_search_bands( 'rent' === $m ) as $k => $l ) {
				$bands[ $m ][] = array( (string) $k, $l );
			}
		}
		$opts = array( '' => 'Any price' ) + blockke_search_bands( $rent );
		if ( '' !== $q['price'] && ! isset( $opts[ $q['price'] ] ) ) {
			$opts[ $q['price'] ] = blockke_search_band_label( $q['lo'], $q['hi'], $rent );
		}
		$f[] = $sel( 'price', 'Price', $opts, $q['price'], ' data-bands="' . esc_attr( wp_json_encode( $bands ) ) . '"' );
		if ( ( $homes && ! $rent ) || '' !== $q['completion'] ) {
			$years = array();
			foreach ( blockke_bkn_rows() as $r ) {
				if ( preg_match( '/^(?:20\d\d(?:-plus)?|ready-now)$/', $r['ct'] ) ) {
					$years[ $r['ct'] ] = 1;
				}
			}
			if ( '' !== $q['completion'] ) {
				$years[ $q['completion'] ] = 1;
			}
			uksort(
				$years,
				function ( $a, $b ) {
					return ( 'ready-now' === $a ? 9999 : (int) $a + ( false !== strpos( $a, 'plus' ) ? 0.5 : 0 ) ) <=> ( 'ready-now' === $b ? 9999 : (int) $b + ( false !== strpos( $b, 'plus' ) ? 0.5 : 0 ) );
				}
			);
			$opts = array( '' => 'Any completion' );
			foreach ( array_keys( $years ) as $y ) {
				$opts[ (string) $y ] = blockke_search_done_label( (string) $y );
			}
			$f[] = $sel( 'completion', 'Completion', $opts, $q['completion'] );
		} else {
			$f[] = '<input type="hidden" name="completion" value="">'; // snippet 41011 adds its own select to search forms without this field
		}
		$sorts = array( '' => 'Recommended', 'price-asc' => 'Price, lowest first', 'price-desc' => 'Price, highest first', 'newest' => 'Newest on Block' );
		if ( 'soonest' === $q['sort'] ) {
			$sorts['soonest'] = 'Completion, soonest';
		}
		$hidden = '';
		if ( $q['types'] ) {
			$hidden .= '<input type="hidden" name="type" value="' . esc_attr( implode( ',', $q['types'] ) ) . '">';
		}
		foreach ( $q['cats'] as $c ) {
			$hidden .= '<input type="hidden" name="property_category[]" value="' . esc_attr( $c ) . '">';
		}
		if ( blockke_search_preview() ) {
			$hidden .= '<input type="hidden" name="bke_search" value="1">';
		}
		$clear = $s['nset'] ? '<a class="bks-clear" href="' . esc_url( blockke_search_url( $q, array( 'area' => array(), 'beds' => '', 'price' => '', 'lo' => 0, 'hi' => 0, 'completion' => '', 'sort' => '', 'n' => 0 ) ) ) . '">Clear filters</a>' : '';
		return '<form class="bkn-bar bks-bar" id="bks-bar" method="get" action="' . esc_url( get_permalink( blockke_search_page_id() ) ) . '" data-bke-own>' . $hidden
			. '<div class="bkd-wrap bks-bar-in"><a class="bks-ftog" href="#bks-f" aria-controls="bks-f" aria-expanded="false">Filters' . ( $s['nset'] ? ' · ' . (int) $s['nset'] : '' ) . '</a>'
			. '<div class="bks-f" id="bks-f" role="group" aria-label="Filters"><div class="bks-fh"><b>Filters</b><a class="bks-x" href="#" data-bks-close>Close</a></div>'
			. '<div class="bks-fl">' . implode( '', $f ) . '</div><div class="bks-ff"><button class="bks-apply" type="submit">Show results</button>' . $clear . '</div></div>'
			. str_replace( 'class="bks-sel"', 'class="bks-sel bks-sort"', $sel( 'sort', 'Sort', $sorts, $q['sort'] ) ) . '</div></form>';
	}

	/** The results page. */
	function blockke_search_render( $s ) {
		$cfg  = blockke_dev_config();
		$e    = 'esc_html';
		$q    = $s['q'];
		$o    = array();
		$o[]  = '<div id="bkd" class="bkd bkn bks" data-bkn-src="search" data-bkn-label="your search" data-devs="' . (int) $s['devs'] . '" data-homes="' . (int) $s['homes'] . '" data-segs="' . esc_attr( implode( ',', array_keys( $s['secs'] ) ) ) . '" data-filters="' . esc_attr( $s['filter'] ) . '"' . ( $s['devs'] ? '' : ' data-none' ) . '>' . blockke_dev_sprite( array( 'wa' ) );
		$tabs = '';
		foreach ( $s['tabs'] as $t ) {
			$tabs .= '<a href="' . esc_url( $t[1] ) . '"' . ( $t[2] ? ' aria-current="page"' : '' ) . '>' . $e( $t[0] ) . '</a>';
		}
		// bks-head-end: no type tabs to close the head (no or one kind of result), so it ends with its own space and the bar's hairline
		$o[] = '<div class="bks-head' . ( '' === $tabs ? ' bks-head-end' : '' ) . '"><div class="bkd-wrap"><nav class="bks-crumbs" aria-label="Breadcrumb"><a href="' . esc_url( home_url( '/' ) ) . '">Home</a><span aria-hidden="true">/</span><span aria-current="page">Search</span></nav>'
			. '<h1 class="bks-h1">' . $e( $s['h1'] ) . '</h1>'
			. ( $s['devs'] ? '<p class="bks-count"><span>' . $e( $s['count'] ) . '</span>' . ( '' !== $s['map'] ? '<a class="bks-map" href="' . esc_url( $s['map'] ) . '">Map</a>' : '' ) . '</p>' : '' )
			. ( '' !== $tabs ? '<nav class="bks-types" aria-label="Type of property">' . $tabs . '</nav>' : '' ) . '</div></div>';
		$o[] = blockke_search_bar( $s );
		$wa  = '<a class="bks-wa" href="' . esc_url( $s['wa'] ) . '" target="_blank" rel="noopener" data-bkd-wa>' . blockke_dev_icon( 'wa', 'bkd-wa-ic' ) . 'Send this search to an advisor</a>';
		$rl  = function ( $list ) use ( $e ) {
			$h = '<ul class="bks-relax">';
			foreach ( $list as $x ) {
				$h .= '<li><a href="' . esc_url( $x[2] ) . '" data-relax="' . esc_attr( $x[3] ) . '"><span class="bks-rl">' . $e( $x[0] ) . '</span> <span class="bks-rn">(' . (int) $x[1] . ')</span></a></li>';
			}
			return $h . '</ul>';
		};
		if ( ! $s['devs'] ) {
			$near = array();
			foreach ( $s['near'] as $x ) {
				$near[] = '<a href="' . esc_url( $x[2] ) . '" data-relax="' . esc_attr( $x[3] ) . '">' . $e( $x[0] ) . ' (' . (int) $x[1] . ')</a>';
			}
			$o[] = '<section class="bks-sec bks-none" aria-label="No results"><div class="bkd-wrap"><p class="bks-none-t">' . $e( $s['none'] ) . '</p>'
				. ( $s['relax'] ? $rl( $s['relax'] ) : '' ) . ( $near ? '<p class="bks-near">Nearby: ' . implode( ', ', $near ) . '</p>' : '' )
				. '<p class="bks-ask">' . $wa . '</p></div></section>';
		}
		$first = true;
		$ids   = array();
		foreach ( $s['secs'] as $key => $sec ) {
			$sid   = 's-' . str_replace( '|', '-', $key );
			$cards = array();
			$text  = array(); // no photo at all: a line in "Also listed", never a placeholder tile
			foreach ( $sec['cards'] as $c ) {
				if ( $c['img'] ) {
					$cards[] = $c;
				} else {
					$text[] = $c;
				}
			}
			$total = count( $cards );
			// "New on Block": the newest, at most 1 card in 5
			$tags = array();
			$room = (int) floor( $total / 5 );
			if ( $room > 0 ) {
				$cut = time() - DAY_IN_SECONDS * max( 1, (int) $cfg['index_new_days'] );
				$new = array();
				foreach ( $cards as $c ) {
					if ( $c['pub'] >= $cut ) {
						$new[ $c['g'] ] = $c['pub'];
					}
				}
				arsort( $new );
				$tags = array_slice( $new, 0, $room, true );
			}
			$o[] = '<section class="bks-sec" id="' . esc_attr( $sid ) . '" data-seg="' . esc_attr( $key ) . '" aria-labelledby="' . esc_attr( $sid ) . '-h"><div class="bkd-wrap">'
				. '<h2 class="bks-h2' . ( 1 === count( $s['secs'] ) ? ' bkd-sr' : '' ) . '" id="' . esc_attr( $sid ) . '-h">' . $e( $sec['h2'] ) . '</h2>' . ( $cards ? '<div class="bks-grid">' : '' );
			foreach ( array_slice( $cards, 0, $s['show'] ) as $i => $c ) {
				$h = blockke_search_card_html( $c, $sec, $q, $i + 1, isset( $tags[ $c['g'] ] ), ! ( $first && $i < 3 ) );
				if ( $first && $i < 3 ) { // the first row loads at once (WordPress adds loading="lazy" itself); the first photo first of all
					$h = str_replace( ' loading="lazy"', 0 === $i ? ' fetchpriority="high"' : '', $h );
				}
				if ( isset( $ids[ $c['aid'] ] ) ) { // one development in two sections (sale and rent): ids stay unique
					$h = str_replace( ' id="d-' . $c['aid'] . '"', ' id="d-' . $c['aid'] . '-' . $sid . '"', $h );
				}
				$ids[ $c['aid'] ] = 1;
				$o[]              = $h;
			}
			$o[] = $cards ? '</div>' : '';
			if ( $total > $s['show'] ) {
				$more = min( $s['step'], $total - $s['show'] );
				$o[]  = '<p class="bks-more-w"><a class="bks-more" href="' . esc_url( blockke_search_url( $q, array( 'n' => $s['show'] + $s['step'] ) ) . '#d-' . (int) $cards[ $s['show'] ]['aid'] ) . '" rel="nofollow">' . $e( 'Show ' . $more . ' more' ) . '</a></p>';
			}
			if ( $text ) {
				$o[] = '<div class="bks-also"><h3 class="bks-also-h">' . $e( 'Also listed (' . count( $text ) . ')' ) . '</h3><ul>';
				foreach ( $text as $c ) {
					$o[] = blockke_search_text_item( $c, $sec );
				}
				$o[] = '</ul></div>';
			}
			$o[]   = '</div></section>';
			$first = false;
		}
		if ( $s['devs'] && $s['relax'] ) {
			$o[] = '<section class="bks-sec bks-close" aria-labelledby="bks-close-h"><div class="bkd-wrap"><h2 class="bks-h3" id="bks-close-h">Close matches</h2>' . $rl( $s['relax'] ) . '<p class="bks-ask">' . $wa . '</p></div></section>';
		} elseif ( $s['devs'] ) {
			$o[] = '<div class="bkd-wrap"><p class="bks-ask">' . $wa . '</p></div>';
		}
		$o[] = '<div class="bkd-wrap"><p class="bks-disc">' . $e( $cfg['area_disclaimer'] ) . '</p></div></div>';
		return implode( '', $o );
	}

	/** Search results styles, on top of the development layout's and the card family's. */
	function blockke_search_css() {
		return <<<'CSS'
#bkd.bks{--sec:48px}
#bkd.bks .bkn-name{font-family:var(--display)!important;font-weight:var(--display-w)!important;letter-spacing:var(--display-ls)!important;text-transform:none!important}
#bkd.bks .bkd-ph{display:none}
#bkd.bks-fade .bkn-img img:not(.bks-in){opacity:0}
#bkd .bks-head{padding:24px 0 0}
#bkd .bks-head-end{padding-bottom:24px}
#bkd .bks-head-end+.bks-bar{border-top:1px solid var(--line)}
#bkd .bks-crumbs{display:flex;flex-wrap:wrap;gap:4px 8px;font-size:13px;line-height:1.5;color:var(--muted)}
#bkd .bks-crumbs a{display:inline-block;padding:13px 0;margin:-13px 0;color:var(--slate)!important}
#bkd .bks-crumbs a:hover{text-decoration:underline}
#bkd .bks-h1{margin:14px 0 0!important;max-width:20ch;font-family:var(--display)!important;font-weight:var(--display-w)!important;font-size:40px!important;line-height:1.05!important;letter-spacing:var(--display-ls)!important;text-transform:none!important;color:var(--navy)!important}
#bkd .bks-count{display:flex;flex-wrap:wrap;align-items:baseline;gap:0 24px;margin:12px 0 0;font-size:16px;line-height:1.5;color:var(--slate)}
#bkd .bks-map{display:inline-block;padding:12px 0;margin:-12px 0;font-size:14px;font-weight:600;color:var(--navy)!important;text-decoration:underline;text-decoration-thickness:1px;text-underline-offset:4px}
#bkd .bks-types{display:flex;gap:24px;margin:16px 0 0;overflow-x:auto;scrollbar-width:none;border-bottom:1px solid var(--line)}
#bkd .bks-types::-webkit-scrollbar{display:none}
#bkd .bks-types a{position:relative;flex:none;display:inline-flex;align-items:center;min-height:44px;font-size:14px;font-weight:500;line-height:1.2;color:var(--slate)!important;white-space:nowrap}
#bkd .bks-types a:hover{color:var(--navy)!important}
#bkd .bks-types a[aria-current]{color:var(--navy)!important}
#bkd .bks-types a[aria-current]::after{content:"";position:absolute;left:0;right:0;bottom:0;height:2px;background:var(--brass)}
#bkd .bks-bar{position:sticky;top:var(--top);z-index:30;margin:0;background:var(--paper);border-bottom:1px solid var(--line)}
#bkd .bks-bar.bks-up{z-index:100050}
#bkd .bks-bar-in{display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:0 16px;min-height:52px}
#bkd .bks-ftog,#bkd .bks-x{display:inline-flex;align-items:center;min-height:44px;font-size:15px;font-weight:600;color:var(--navy)!important;text-decoration:underline;text-decoration-thickness:1px;text-underline-offset:5px}
#bkd .bks-f{display:none;order:3;flex:1 0 100%;padding:4px 0 24px}
#bkd .bks-f:target,#bkd .bks-f.bks-open{display:block}
#bkd .bks-fh{display:flex;align-items:center;justify-content:space-between;gap:16px;min-height:56px}
#bkd .bks-fh b{font:var(--display-w) 30px/1.1 var(--display);letter-spacing:var(--display-ls);color:var(--navy)}
#bkd .bks-fl{display:grid;gap:18px}
#bkd .bks-sel{display:grid;gap:6px;min-width:0;margin:0}
#bkd .bks-sel[hidden]{display:none}
#bkd .bks-lab{font-size:14px;font-weight:600;line-height:1.4;color:var(--ink)}
#bkd .bks-bar select{display:block;width:100%;max-width:none;height:48px;min-height:0;margin:0;padding:0 40px 0 14px;border:1px solid var(--line-2)!important;border-radius:2px!important;background:#fff url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 8'%3E%3Cpath d='M1 1.5l5 5 5-5' fill='none' stroke='%230D2440' stroke-width='1.5'/%3E%3C/svg%3E") no-repeat right 14px center/12px 8px!important;box-shadow:none!important;font:400 16px/1.2 var(--font)!important;color:var(--navy)!important;-webkit-appearance:none;appearance:none;cursor:pointer}
#bkd .bks-ff{display:flex;flex-wrap:wrap;align-items:center;gap:12px 24px;margin-top:24px}
#bkd .bks-apply{display:inline-flex;align-items:center;justify-content:center;flex:1 1 auto;min-height:52px;margin:0;padding:0 24px!important;border:0!important;border-radius:0!important;background:var(--navy)!important;box-shadow:none!important;font:600 15px/1.2 var(--font)!important;letter-spacing:0!important;text-transform:none!important;color:#fff!important;cursor:pointer}
#bkd .bks-clear{display:inline-flex;align-items:center;min-height:44px;font-size:14px;font-weight:500;color:var(--slate)!important;text-decoration:underline;text-underline-offset:4px}
#bkd .bks-sort{display:block}
#bkd .bks-sort .bks-lab{position:absolute!important;width:1px;height:1px;overflow:hidden;clip:rect(0 0 0 0);white-space:nowrap}
#bkd .bks-bar .bks-sort select,#bkd .bks-bar .bks-sort select.on{width:auto;height:44px;padding:0 22px 0 0;border:0!important;border-radius:0!important;background:transparent url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 8'%3E%3Cpath d='M1 1.5l5 5 5-5' fill='none' stroke='%230D2440' stroke-width='1.5'/%3E%3C/svg%3E") no-repeat right center/10px 7px!important;font:500 14px/1.2 var(--font)!important}
#bkd .bks-f.bks-open{position:fixed;inset:0;z-index:100050;display:flex;flex-direction:column;padding:0;background:var(--paper);overflow:auto;overscroll-behavior:contain}
#bkd .bks-open .bks-fh{position:sticky;top:0;z-index:1;padding:0 20px;background:var(--paper);border-bottom:1px solid var(--line)}
#bkd .bks-open .bks-fl{padding:20px}
#bkd .bks-open .bks-ff{position:sticky;bottom:0;margin:auto 0 0;padding:12px 20px calc(12px + env(safe-area-inset-bottom));background:var(--paper);border-top:1px solid var(--line)}
#bkd .bks-sec{padding:32px 0 0}
#bkd .bks-sec+.bks-sec{padding-top:48px;content-visibility:auto;contain-intrinsic-size:auto 480px}
#bkd .bks-h2{margin:0 0 24px!important;font-family:var(--display)!important;font-weight:var(--display-w)!important;font-size:30px!important;line-height:1.1!important;letter-spacing:var(--display-ls)!important;text-transform:none!important;color:var(--navy)!important}
#bkd .bks-h3{margin:0 0 8px!important;font-family:var(--display)!important;font-weight:var(--display-w)!important;font-size:26px!important;line-height:1.15!important;letter-spacing:var(--display-ls)!important;text-transform:none!important;color:var(--navy)!important}
#bkd .bks-grid{display:grid;grid-template-columns:minmax(0,1fr);gap:40px 24px}
#bkd .bks-grid>.bkn-card{scroll-margin-top:calc(var(--top) + 88px)}
#bkd .bks-more-w{margin:40px 0 0}
#bkd .bks-more,#bkd .bks-wa{display:inline-flex;align-items:center;gap:10px;min-height:44px;font-size:15px;font-weight:600;color:var(--navy)!important;text-decoration:underline;text-decoration-thickness:1px;text-underline-offset:5px}
#bkd .bks-more[aria-busy]{opacity:.6;cursor:progress}
#bkd .bks-none-t{max-width:38ch;font-size:18px;line-height:1.5;color:var(--ink)}
#bkd .bks-relax{margin:12px 0 0;padding:0;list-style:none}
#bkd .bks-relax li{margin:0}
#bkd .bks-relax a{display:inline-flex;align-items:center;gap:6px;min-height:44px;font-size:15px;color:var(--navy)!important;text-decoration:none}
#bkd .bks-rl{font-weight:600;text-decoration:underline;text-decoration-thickness:1px;text-underline-offset:5px}
#bkd .bks-rn{color:var(--muted)}
#bkd .bks-near{margin:12px 0 0;font-size:15px;line-height:1.7;color:var(--slate)}
#bkd .bks-near a{display:inline-block;padding:10px 0;color:var(--navy)!important;text-decoration:underline;text-underline-offset:4px}
#bkd .bks-also{margin:48px 0 0;max-width:720px}
#bkd .bks-grid+.bks-also,#bkd .bks-more-w+.bks-also{padding-top:24px;border-top:1px solid var(--line)}
#bkd .bks-also-h{margin:0 0 4px!important;font:600 15px/1.4 var(--font)!important;letter-spacing:0!important;color:var(--ink)!important}
#bkd .bks-also ul{margin:0;padding:0;list-style:none}
#bkd .bks-also li{display:flex;flex-wrap:wrap;align-items:baseline;gap:0 12px;margin:0;border-bottom:1px solid var(--line)}
#bkd .bks-also li a{display:inline-flex;align-items:center;min-height:44px;font-size:15px;font-weight:600;color:var(--navy)!important;text-decoration:underline;text-decoration-thickness:1px;text-underline-offset:4px}
#bkd .bks-also li span{padding:0 0 10px;font-size:14px;color:var(--slate);font-variant-numeric:tabular-nums}
@media (min-width:768px){#bkd .bks-also li span{padding:0}}
#bkd .bks-wa .bkd-i{width:20px;height:20px}
#bkd .bks-ask{margin:40px 0 0}
#bkd .bks-none .bks-ask,#bkd .bks-close .bks-ask{margin-top:24px}
#bkd .bks-disc{margin:48px 0 0;padding:24px 0 48px;border-top:1px solid var(--line);font-size:13px;line-height:1.6;color:var(--muted)}
@media (min-width:640px){#bkd .bks-h1{font-size:52px!important}}
@media (min-width:768px){
#bkd .bks-head{padding-top:32px}
#bkd .bks-head-end{padding-bottom:32px}
#bkd .bks-count{font-size:17px}
#bkd .bks-sec{padding-top:40px}
#bkd .bks-sec+.bks-sec{padding-top:72px}
#bkd .bks-h2{font-size:34px!important;margin-bottom:28px!important}
#bkd .bks-grid{grid-template-columns:repeat(2,minmax(0,1fr));gap:48px 24px}
#bkd .bks-disc{margin-top:72px}
}
/* the bar inline from 1000px, as on the new developments page; phones and tablets get "Filters" and the sheet */
@media (min-width:1000px){
#bkd .bks-ftog,#bkd .bks-fh{display:none}
#bkd .bks-bar-in{flex-wrap:nowrap;align-items:flex-start;gap:24px;min-height:64px}
#bkd .bks-f,#bkd .bks-f:target{display:flex;flex-wrap:wrap;order:0;flex:0 1 auto;align-items:center;gap:0 24px;min-width:0;padding:10px 0}
#bkd .bks-fl{display:contents}
#bkd .bks-ff{display:contents}
#bkd .bks-sort{flex:none;margin-left:auto;padding:10px 0}
#bkd .bks-lab{position:absolute!important;width:1px;height:1px;overflow:hidden;clip:rect(0 0 0 0);white-space:nowrap}
#bkd .bks-bar select{width:auto;height:44px;padding:0 22px 0 0;border:0!important;border-bottom:1px solid var(--line-2)!important;border-radius:0!important;background:transparent url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 8'%3E%3Cpath d='M1 1.5l5 5 5-5' fill='none' stroke='%230D2440' stroke-width='1.5'/%3E%3C/svg%3E") no-repeat right center/10px 7px!important;font:500 14px/1.2 var(--font)!important}
#bkd .bks-bar select{field-sizing:content;min-width:72px}
#bkd .bks-bar select.on{border-bottom:2px solid var(--brass)!important}
#bkd .bks-apply{flex:none;min-height:44px;padding:0!important;background:none!important;color:var(--navy)!important;text-decoration:underline;text-underline-offset:5px}
#bkd.bkd-js .bks-apply{display:none}
#bkd .bks-head{padding-top:48px}
#bkd .bks-h1{font-size:64px!important}
#bkd .bks-count{font-size:18px}
#bkd .bks-sec{padding-top:48px}
#bkd .bks-sec+.bks-sec{padding-top:96px}
#bkd .bks-h2{font-size:40px!important;margin-bottom:32px!important}
#bkd .bks-grid{grid-template-columns:repeat(3,minmax(0,1fr));gap:56px 32px}
#bkd .bks-disc{margin-top:96px}
}
CSS;
	}

	/** Search results behaviour: the bar, the phone sheet, "Show 18 more" without a reload, and the search events. */
	function blockke_search_js() {
		return <<<'JS'
(function(){
  'use strict';
  var root=document.getElementById('bkd');if(!root||!root.classList.contains('bks')){return;}
  var dl=(window.dataLayer=window.dataLayer||[]),vt='onpagereveal' in window?1:0,html=document.documentElement;
  function track(ev,o){var p=o||{};try{if(typeof window.gtag==='function'){window.gtag('event',ev,p);}else{p.event=ev;dl.push(p);}}catch(e){}}
  function at(el,k){return el?el.getAttribute(k)||'':'';}
  /* photos fade in once loaded (200ms) */
  function fade(box){[].forEach.call(box.querySelectorAll('.bkn-img img'),function(im){var on=function(){im.classList.add('bks-in');};if(im.complete){on();}else{im.addEventListener('load',on,{once:true});im.addEventListener('error',on,{once:true});}});}
  fade(root);root.classList.add('bks-fade');
  track('search_view',{count_devs:+at(root,'data-devs'),count_homes:+at(root,'data-homes'),segments:at(root,'data-segs')});
  /* once per search: not again when the same empty search is reloaded or comes back from history */
  var nav=null;try{nav=performance.getEntriesByType('navigation')[0];}catch(x){}
  if(root.hasAttribute('data-none')&&(!nav||nav.type==='navigate')){track('index_no_results',{filters:at(root,'data-filters')});}
  root.addEventListener('click',function(e){
    var a=e.target&&e.target.closest?e.target.closest('a'):null;if(!a){return;}
    var card=a.closest('.bkn-card');
    if(card&&a.hasAttribute('data-row')){track('search_match_click',{target_id:at(card,'data-id'),unit_id:at(a,'data-unit'),row:+at(a,'data-row'),transport_type:'beacon'});}
    else if(card&&a.classList.contains('bkn-mall')){track('search_match_click',{target_id:at(card,'data-id'),unit_id:'',row:'all',transport_type:'beacon'});}
    else if(card&&a.classList.contains('bkn-a')){track('search_card_click',{target_id:at(card,'data-id'),position:+at(a,'data-pos'),kind:card.classList.contains('bks-home')?'unit':(card.querySelector('.bkn-match')?'group':'dev'),segment:at(a.closest('[data-seg]'),'data-seg'),vt:vt,transport_type:'beacon'});}
    else if(a.hasAttribute('data-relax')){track('index_relax',{removed:at(a,'data-relax'),filters:at(root,'data-filters'),transport_type:'beacon'});}
    else if(a.hasAttribute('data-bks-text')){track('search_card_click',{target_id:at(a,'data-bks-text'),position:0,kind:'text',segment:at(a.closest('[data-seg]'),'data-seg'),vt:0,transport_type:'beacon'});}
  });
  /* the bar: changes apply at once from 1000px (and Sort everywhere); on phones and tablets the sheet applies them together.
     A change made from the keyboard (arrow keys step through a closed select) waits for Enter or for the select to lose focus,
     so each key press doesn't reload the page */
  var form=document.getElementById('bks-bar');
  if(form){
    var box=document.getElementById('bks-f'),tog=form.querySelector('.bks-ftog'),wide=window.matchMedia('(min-width:1000px)'),last=null,kb=0,dirty=0;
    var all=function(){return [].slice.call(form.querySelectorAll('select,input'));};
    var go=function(){all().forEach(function(x){if(x.name&&x.value===''){x.disabled=true;}});form.submit();};
    var now=function(t){return wide.matches||!box||!box.contains(t);};
    form.addEventListener('submit',function(e){e.preventDefault();go();});
    window.addEventListener('pageshow',function(){all().forEach(function(x){x.disabled=false;});kb=0;dirty=0;});
    form.addEventListener('keydown',function(e){var t=e.target;if(!t||t.tagName!=='SELECT'){return;}
      if(e.key==='Enter'){setTimeout(function(){if(dirty){kb=0;dirty=0;if(now(t)){go();}}},0);}
      else if(e.key!=='Tab'&&e.key!=='Shift'&&e.key!=='Escape'){kb=1;}});
    form.addEventListener('pointerdown',function(){kb=0;});
    form.addEventListener('focusout',function(e){var t=e.target;if(t&&t.tagName==='SELECT'){kb=0;if(dirty){dirty=0;if(now(t)){go();}}}});
    form.addEventListener('change',function(e){var t=e.target;
      if(t.name==='for'){var p=form.querySelector('select[name="price"]'),c=form.querySelector('[data-bks-done]');
        if(p){var b={};try{b=JSON.parse(p.getAttribute('data-bands')||'{}');}catch(x){b={};}p.length=1;(b[t.value==='rent'?'rent':'sale']||[]).forEach(function(o){p.add(new Option(o[1],o[0]));});p.value='';p.classList.remove('on');}
        if(c){c.hidden=t.value==='rent';if(c.hidden){c.querySelector('select').value='';}}}
      if(kb){dirty=1;return;}
      if(now(t)){go();}
    });
    var trap=function(e){if(e.key==='Escape'){shut();return;}if(e.key!=='Tab'){return;}
      var f=[].slice.call(box.querySelectorAll('a[href],button,select')).filter(function(x){return x.offsetParent!==null&&!x.disabled;});if(!f.length){return;}
      if(e.shiftKey&&document.activeElement===f[0]){e.preventDefault();f[f.length-1].focus();}else if(!e.shiftKey&&document.activeElement===f[f.length-1]){e.preventDefault();f[0].focus();}};
    var open=function(){last=document.activeElement;form.classList.add('bks-up');box.classList.add('bks-open');box.setAttribute('role','dialog');box.setAttribute('aria-modal','true');tog.setAttribute('aria-expanded','true');html.classList.add('bkd-lock');document.addEventListener('keydown',trap);var f=box.querySelector('select');if(f){f.focus();}};
    var shut=function(){box.classList.remove('bks-open');form.classList.remove('bks-up');box.setAttribute('role','group');box.removeAttribute('aria-modal');tog.setAttribute('aria-expanded','false');html.classList.remove('bkd-lock');document.removeEventListener('keydown',trap);if(last&&last.focus){last.focus();}};
    if(box&&tog){
      tog.addEventListener('click',function(e){if(!wide.matches){e.preventDefault();open();}});
      box.addEventListener('click',function(e){if(e.target.closest&&e.target.closest('[data-bks-close]')){e.preventDefault();shut();}});
      if(wide.addEventListener){wide.addEventListener('change',function(){if(wide.matches&&box.classList.contains('bks-open')){shut();}});}
    }
  }
  /* "Show 18 more": the next cards without a reload; the address keeps n, so Back and a reload show the same list */
  root.addEventListener('click',function(e){
    var a=e.target&&e.target.closest?e.target.closest('a.bks-more'):null;
    if(!a||e.button!==0||e.metaKey||e.ctrlKey||e.shiftKey||e.altKey||!window.fetch||!window.DOMParser){return;}
    e.preventDefault();if(a.getAttribute('aria-busy')){return;}a.setAttribute('aria-busy','true');
    var href=a.href.split('#')[0];
    fetch(href,{credentials:'same-origin'}).then(function(r){if(!r.ok){throw new Error('more');}return r.text();}).then(function(txt){
      var doc=new DOMParser().parseFromString(txt,'text/html'),first=null;
      [].forEach.call(root.querySelectorAll('.bks-sec[data-seg]'),function(sec){
        var ns=doc.querySelector('.bks-sec[data-seg="'+sec.getAttribute('data-seg')+'"]'),grid=sec.querySelector('.bks-grid');if(!ns||!grid){return;}
        var ng=ns.querySelector('.bks-grid'),mine=sec.contains(a);
        [].slice.call(ng?ng.children:[],grid.children.length).forEach(function(c,i){var x=document.importNode(c,true);grid.appendChild(x);fade(x);if(mine&&!i){first=x;}});
        var ow=sec.querySelector('.bks-more-w'),nw=ns.querySelector('.bks-more-w');if(ow){if(nw){ow.parentNode.replaceChild(document.importNode(nw,true),ow);}else{ow.parentNode.removeChild(ow);}}
      });
      try{history.replaceState(history.state,'',href);}catch(x){}
      if(first){var l=first.querySelector('a.bkn-a');if(l){try{l.focus({preventScroll:true});}catch(x){}}}
      try{root.dispatchEvent(new CustomEvent('bkn:cards',{bubbles:true}));}catch(x){}
    }).catch(function(){location.href=a.href;});
  });
})();
JS;
	}

	add_action(
		'template_redirect',
		function () {
			// Simple searches go to the page made for them (302: the parameter names the theme sends are not yet confirmed)
			if ( is_admin() || ! is_page( blockke_search_page_id() ) || ! blockke_bkn_on( 'search_redirect' ) || isset( $_GET['bke_search'] ) || 'GET' !== ( isset( $_SERVER['REQUEST_METHOD'] ) ? strtoupper( (string) $_SERVER['REQUEST_METHOD'] ) : 'GET' ) ) { // phpcs:ignore WordPress.Security.NonceVerification
				return;
			}
			try {
				$q  = blockke_search_parse();
				$to = null === $q ? '' : blockke_search_redirect_url( $q );
			} catch ( Throwable $x ) {
				$to = '';
			}
			if ( '' !== $to ) {
				nocache_headers();
				wp_safe_redirect( $to, 302 );
				exit;
			}
		},
		5
	);

	add_filter(
		'template_include',
		function ( $template ) {
			if ( is_admin() || is_feed() || is_embed() || ! is_page( blockke_search_page_id() ) || ! blockke_bkn_on( 'search_layout', 'bke_search' ) ) {
				return $template;
			}
			$q = blockke_search_parse();
			if ( null === $q ) {
				return $template; // a keyword, MLS number, saved search or anything else we can't read: the theme's results
			}
			$GLOBALS['blockke_search_preview'] = isset( $_GET['bke_search'] ) && current_user_can( 'edit_posts' ); // phpcs:ignore WordPress.Security.NonceVerification
			try {
				if ( ! blockke_bkn_rows() ) {
					return $template;
				}
				$s    = blockke_search_state( $q );
				$html = blockke_search_render( $s );
			} catch ( Throwable $x ) {
				error_log( 'BLOCK search results: ' . $x->getMessage() . ' (' . $x->getFile() . ':' . $x->getLine() . ')' ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions
				return $template;
			}
			$GLOBALS['blockke_dev_active'] = true;
			if ( empty( blockke_dev_config()['price_check_popup'] ) ) {
				$_GET['blockpop'] = '0';
			}
			status_header( 200 );
			add_filter(
				'body_class',
				function ( $c ) {
					$c[] = 'bkd-page';
					$c[] = 'bks-page';
					return $c;
				}
			);
			// Results stay out of search engines (noindex, follow) with the page's own address as canonical
			add_filter(
				'wp_robots',
				function ( $r ) {
					unset( $r['index'], $r['max-image-preview'] );
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
			$canon = get_permalink( blockke_search_page_id() );
			foreach ( array( 'rank_math/frontend/canonical', 'rank_math/opengraph/facebook/og_url' ) as $h ) {
				add_filter(
					$h,
					function () use ( $canon ) {
						return $canon;
					},
					99
				);
			}
			add_filter( 'rank_math/frontend/disable_adjacent_rel_links', '__return_true' );
			add_filter(
				'pre_get_document_title',
				function () use ( $s ) {
					return esc_html( $s['title'] );
				},
				99
			);
			foreach ( array( 'rank_math/frontend/title', 'rank_math/opengraph/facebook/og_title', 'rank_math/opengraph/twitter/twitter_title' ) as $h ) {
				add_filter(
					$h,
					function () use ( $s ) {
						return $s['title'];
					},
					99
				);
			}
			add_action(
				'wp_head',
				function () {
					$font = blockke_dev_font_head( '#bkd' );
					if ( '' === $font[0] ) {
						$font[1] = '#bkd{--display:var(--font);--display-w:300;--display-ls:-.025em}';
					}
					echo $font[0] . '<style id="bkd-css">' . blockke_dev_css() . $font[1] . blockke_bkn_card_css() . blockke_search_css() . "</style>\n"; // phpcs:ignore WordPress.Security.EscapeOutput
					if ( blockke_bkn_on( 'view_transitions' ) && empty( $GLOBALS['blockke_bkn_vt_printed'] ) ) {
						$GLOBALS['blockke_bkn_vt_printed'] = true;
						echo blockke_bkn_vt_head(); // phpcs:ignore WordPress.Security.EscapeOutput
					}
				},
				99
			);
			add_action(
				'wp_footer',
				function () use ( $s ) {
					$cfg = blockke_dev_config();
					$ag  = blockke_dev_agent( 0 );
					$js  = array(
						'id'        => 0,
						'slug'      => '',
						'name'      => $s['h1'],
						'leadName'  => $s['words'],
						'title'     => $s['title'],
						'url'       => $s['url'],
						'brand'     => $cfg['brand'],
						'agent'     => '',
						'wa'        => $ag['wa'],
						'endpoint'  => esc_url_raw( rest_url( 'block/v1/development-lead' ) ),
						'gallery'   => array(),
						'lp'        => 0,
						'area'      => '',
						'bands'     => $cfg['budget_bands'],
						'back'      => 0,
						'bedsMode'  => $cfg['beds_mode'],
						'studioCat' => (string) $cfg['studio_cat'],
						'search'    => 1,
						'bkn'       => array( 'src' => 'search', 'label' => 'your search' ),
						'listLabel' => 'your search', // what the card context and "Back to …" read
					);
					echo '<script id="bkd-cfg" data-cfasync="false" data-no-optimize="1" data-no-defer="1">window.BKDEV=' . wp_json_encode( $js, JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS ) . ";</script>\n";
					echo '<script id="bkd-js" data-cfasync="false" data-no-optimize="1" data-no-defer="1">' . blockke_dev_js() . "\n" . blockke_search_js() . "\n" . blockke_bkn_nav_js() . "</script>\n"; // phpcs:ignore WordPress.Security.EscapeOutput
				},
				20
			);
			get_header();
			echo $html; // phpcs:ignore WordPress.Security.EscapeOutput -- escaped while building.
			get_footer();
			$blank = WP_CONTENT_DIR . '/index.php';
			return file_exists( $blank ) ? $blank : '';
		},
		99998
	);

	add_action(
		'admin_bar_menu',
		function ( $bar ) {
			if ( is_admin() || ! is_page( blockke_search_page_id() ) || ! current_user_can( 'edit_posts' ) ) {
				return;
			}
			$cfg  = blockke_dev_config();
			$live = ! empty( $cfg['search_layout'] );
			$here = remove_query_arg( 'bke_search', home_url( isset( $_SERVER['REQUEST_URI'] ) ? wp_unslash( $_SERVER['REQUEST_URI'] ) : '/' ) ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
			$bar->add_node( array( 'id' => 'bks', 'title' => 'Search results: ' . ( $live ? 'on' : 'off' ), 'href' => add_query_arg( 'bke_search', $live ? '0' : '1', $here ) ) );
			$bar->add_node( array( 'parent' => 'bks', 'id' => 'bks-on', 'title' => 'Preview the new search results', 'href' => add_query_arg( 'bke_search', '1', $here ) ) );
			$bar->add_node( array( 'parent' => 'bks', 'id' => 'bks-off', 'title' => 'Preview the classic search results', 'href' => add_query_arg( 'bke_search', '0', $here ) ) );
			$q = blockke_search_parse();
			if ( null === $q ) {
				$read = 'This search stays classic: it uses "' . mb_substr( (string) $GLOBALS['blockke_search_why'], 0, 40 ) . '"';
			} else {
				$to   = blockke_search_redirect_url( $q );
				$read = '' === $to ? 'This search can use the new layout' : 'This search ' . ( ! empty( $cfg['search_redirect'] ) ? 'goes' : 'would go' ) . ' to ' . (string) wp_parse_url( $to, PHP_URL_PATH );
			}
			$bar->add_node( array( 'parent' => 'bks', 'id' => 'bks-read', 'title' => esc_html( $read ) ) );
			$bar->add_node( array( 'parent' => 'bks', 'id' => 'bks-rd', 'title' => 'Redirects to area and new developments pages: ' . ( ! empty( $cfg['search_redirect'] ) ? 'on' : 'off' ) ) );
		},
		90
	);

	/* ---------------------------------------------------------------------
	   @v16-transitions: card to development and back (context, photo transition, prefetch)
	   --------------------------------------------------------------------- */

	/** The card-to-development photo transition (also the photo-first 3:2 hero); editors preview with ?bke_vt=1 / 0. Never on ad landing pages. */
	function blockke_bkn_vt_on() {
		return blockke_bkn_on( 'view_transitions', 'bke_vt' ) && ! blockke_dev_lp_target();
	}

	/**
	 * Development pages read the card context (bedrooms preset, "Back to {list}") while any list page writes it: the v1.6
	 * cards, the index, search results or the transition are on, or an editor may be previewing them.
	 */
	function blockke_bkn_ctx_on() {
		$cfg = blockke_dev_config();
		foreach ( array( 'card_v16', 'dev_index', 'search_layout', 'view_transitions' ) as $k ) {
			if ( ! empty( $cfg[ $k ] ) ) {
				return true;
			}
		}
		return is_user_logged_in() && current_user_can( 'edit_posts' );
	}

	/**
	 * The development page script with the v1.6 additions (blockke_dev_js() passes its script through here). Unchanged while
	 * the switches are off, on landing pages, or if the script no longer has every anchor this relies on, so an edit up
	 * there can never break the page: the additions just stay out until the anchors are updated.
	 */
	function blockke_bkn_dev_js( $js ) {
		if ( blockke_dev_lp_target() ) {
			return $js;
		}
		// While prefetch is on, a development page opened from a prefetched copy (our card prefetch, or any other speculation
		// rules) was not counted when it was fetched (blockke_dev_count_view()), so it counts now that it is shown, once,
		// through blockke_bkn_view(). Reloads and history visits are fetched again and counted there. Its own script, after
		// the page's, so it needs no anchor.
		$view = '';
		if ( blockke_bkn_on( 'prefetch' ) && is_singular( 'estate_property' ) ) {
			$view = "\n(function(){var C=window.BKDEV,n;try{n=performance.getEntriesByType('navigation')[0];if(C&&!C.lp&&n&&n.deliveryType==='navigational-prefetch'&&n.type==='navigate'&&navigator.sendBeacon){navigator.sendBeacon("
				. wp_json_encode( esc_url_raw( rest_url( 'block/v1/view' ) ), JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS )
				. ',new URLSearchParams({id:String(C.id)}));}}catch(e){}})();';
		}
		if ( ! ( blockke_bkn_ctx_on() || blockke_bkn_vt_on() ) ) {
			return $js . $view;
		}
		$ctx = <<<'JS'
  /* v1.6: the card the visitor clicked to get here (sessionStorage bkn_ctx): their bedrooms preset, then the way back */
  (function(){
    var hm=document.getElementById('bkd-hero-media'),hc=hm&&hm.getAttribute('data-cap'); /* the main photo's label now shows in the viewer */
    if(hc&&C.gallery&&C.gallery[0]&&!C.gallery[0][3]){C.gallery[0][3]=hc;}
    var x=null,rf=(document.referrer||'').split('#')[0];try{x=JSON.parse(sessionStorage.getItem('bkn_ctx')||'null');}catch(e){x=null;}
    /* this page, clicked in the last 30 minutes, and not reached since from somewhere else */
    if(C.lp||!x||typeof x.from!=='string'||x.path!==location.pathname||!(Date.now()-x.t<18e5)||(rf&&rf!==x.from.split('#')[0])){return;}
    C.bkn=x;
    var b=String(x.beds||''),f=/^(studio|\d)$/.test(b)?$('.bkd-filters button[data-beds="'+b+'"]'):null,tb=$('.bkd-tbl'),all=$('.bkd-filters button[data-g="*"]'),nv;
    if(!f||!tb||!all){return;}
    /* on the lists 4 means four and more (so does any number on search results while the site reads bedrooms as a minimum):
       when the table also has larger homes it stays on All, as pressing one size would hide some of them */
    if((b==='4'||(C.bedsMode==='min'&&x.src==='search'))&&$$('.bkd-filters button[data-beds]').some(function(o){return +o.getAttribute('data-beds')>+b;})){return;}
    C.quiet=1;f.click();C.quiet=0;
    var p=document.createElement('p');p.className='bkd-ctxb';
    p.innerHTML=esc('Showing '+(b==='studio'?'studios':b+' bed homes')+(x.src==='search'?' from your search.':' only.'))+' <button type="button">Show all</button>';
    tb.parentNode.insertBefore(p,tb);
    $$('.bkd-filters button').forEach(function(o){o.addEventListener('click',function(){if(!C.quiet){p.hidden=true;}});});
    p.querySelector('button').addEventListener('click',function(){all.click();try{all.focus({preventScroll:true});}catch(e){}});
    try{nv=performance.getEntriesByType('navigation')[0];}catch(e){nv=null;}
    if(!nv||nv.type==='navigate'){track('context_beds',{beds:b});} /* once: not again on a reload, Back or Forward */
  })();

JS;
		$back = <<<'JS'

    /* v1.6: back to the list the card was on, by its name ("Back to new developments"); the history.back() path stays */
    var bx=C.bkn,lab='',bsrc='other',bl=null,bp='';
    if(bx){var rf=(document.referrer||'').split('#')[0];from=rf&&rf===bx.from.split('#')[0]?document.referrer:bx.from;lab=String(bx.label||'');bsrc=bx.src||'other';
      try{sessionStorage.setItem('bkd_from',JSON.stringify({f:from,p:location.pathname}));sessionStorage.setItem('bkn_back',JSON.stringify({f:from,l:lab,s:bsrc}));}catch(x){}
      if(!history.state&&location.hash){try{history.replaceState({bkdLand:1},'');}catch(x){}}}
    else if(from){try{bl=JSON.parse(sessionStorage.getItem('bkn_back')||'null');bp=new URL(from).pathname;}catch(x){bl=null;}
      if(bl&&bl.f===from){lab=String(bl.l||'');bsrc=bl.s||'other';}
      else{bsrc=/^\/new-developments\/?$/.test(bp)?'index':/^\/advanced-search\//.test(bp)?'search':/^\/property_area\/[^\/]+\/[^\/]+/.test(bp)?'segment':/^\/property_area\//.test(bp)?'area':'other';}}
JS;
		$swap = array(
			"track('units_filter',{group:b.getAttribute('data-g')});"        => "if(!C.quiet){track('units_filter',{group:b.getAttribute('data-g')});}", // a preset from the card is not the visitor's filter
			'  /* "Back to results": '                                        => $ctx . '  /* "Back to results": ',
			"}catch(e){from='';}"                                             => "}catch(e){from='';}" . $back,
			"a.textContent='Back to results';"                               => "a.textContent='Back to results';if(lab){a.textContent='Back to '+lab;}",
			"track('back_to_results',{transport_type:'beacon'});"          => "track('back_to_results',{transport_type:'beacon',source:bsrc});",
		);
		foreach ( array_keys( $swap ) as $anchor ) {
			if ( 1 !== substr_count( $js, $anchor ) ) {
				return $js . $view;
			}
		}
		return strtr( $js, $swap ) . $view;
	}

	/**
	 * Development page styles for the v1.6 additions: the bedrooms note, the back link on a line of its own, and the
	 * photo-first 3:2 hero while the transition is on.
	 */
	function blockke_bkn_dev_css( $vt ) {
		// The way back (from a card, or the older "Back to results"): 44px to tap. From 700px it heads the breadcrumb on a line
		// of its own, without the "/" after it, so a long "Back to homes for sale in Westlands" never breaks the trail; where
		// :has() works, that line runs above Search and Share too, so the label keeps to one line beside a photo.
		$css = '#bkd .bkd-ctxb{display:flex;flex-wrap:wrap;align-items:center;gap:0 8px;margin:-8px 0 8px;font-size:14px;line-height:1.5;color:var(--slate)}'
			. '#bkd .bkd-ctxb button{display:inline-flex;align-items:center;min-height:44px;margin:0;padding:0!important;border:0!important;border-radius:0!important;background:none!important;box-shadow:none!important;font:600 14px/1.4 var(--font)!important;letter-spacing:0!important;text-transform:none!important;color:var(--navy)!important;text-decoration:underline;text-underline-offset:4px;cursor:pointer}'
			. '@media (max-width:699px){#bkd .bkd-crumbs .bkd-cr-back{min-height:44px;margin:-2px 0}}'
			. '@media (min-width:700px){#bkd .bkd-crumbs.bkd-has-back .bkd-cr-back{display:flex;align-items:center;width:100%;min-height:44px;margin:-10px 0 -4px}#bkd .bkd-crumbs.bkd-has-back .bkd-cr-back+span[aria-hidden]{display:none}'
			. '#bkd .bkd-hero-top:has(.bkd-has-back){position:relative;padding-top:24px}'
			. '#bkd .bkd-hero-top:has(.bkd-has-back) .bkd-cr-back{position:absolute;top:-20px;left:0;display:block;width:auto;max-width:100%;height:44px;min-height:0;margin:0;padding-top:20px;line-height:18px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}}';
		if ( $vt ) {
			// Photo first and full-bleed on phones, 7 of 12 columns from 1000px (held in view beside the text, with photos 2
			// and 3 under it so that column is not left half empty), 3:2 throughout like the cards
			$css .= '#bkd .bkd-hero-vt .bkd-hero-media{aspect-ratio:3/2}#bkd .bkd-hero-side{grid-area:media;min-width:0}#bkd .bkd-hero-thumbs{display:none}'
				. '@media (max-width:999px){#bkd .bkd-hero-vt{padding-top:0}#bkd .bkd-hero-vt .bkd-hero-grid{grid-template-areas:"media" "head" "body";row-gap:24px}}'
				. '@media (min-width:1000px){#bkd .bkd-hero-vt .bkd-hero-grid{grid-template-columns:minmax(0,5fr) minmax(0,7fr);column-gap:56px}'
				. '#bkd .bkd-hero-side{position:sticky;top:calc(var(--top) + 24px)}'
				. '#bkd .bkd-hero-thumbs{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:8px;margin-top:8px}'
				. '#bkd .bkd-hero-thumbs button{position:relative;display:block;aspect-ratio:3/2;margin:0;padding:0!important;border:0!important;border-radius:0!important;overflow:hidden;background:var(--mist)!important;box-shadow:none!important;cursor:zoom-in}'
				. '#bkd .bkd-hero-thumbs img{position:absolute;inset:0;width:100%!important;height:100%!important;max-width:none!important;object-fit:cover}}';
		}
		return $css;
	}

	/**
	 * POST block/v1/view (id): a development page opened from a prefetched copy counts its view (see blockke_bkn_dev_js()).
	 * Only a published listing on the development layout; not an editor's own visit (as on the page itself); one count per
	 * connection and listing every 10 minutes. Always answers 204, so nothing can be learned from it.
	 */
	function blockke_bkn_view( $req ) {
		$id = absint( $req->get_param( 'id' ) );
		$p  = $id ? get_post( $id ) : null;
		if ( ! $p || 'estate_property' !== $p->post_type || 'publish' !== $p->post_status || ! blockke_dev_layout_on( $id ) ) {
			return new WP_REST_Response( null, 204 );
		}
		$uid = (int) wp_validate_auth_cookie( '', 'logged_in' ); // REST calls without a nonce run as a visitor, so the cookie says who it is
		if ( $uid && user_can( $uid, 'edit_post', $id ) ) {
			return new WP_REST_Response( null, 204 );
		}
		$ip = '';
		foreach ( array( 'HTTP_CF_CONNECTING_IP', 'REMOTE_ADDR' ) as $h ) {
			if ( ! empty( $_SERVER[ $h ] ) ) {
				$ip = sanitize_text_field( wp_unslash( $_SERVER[ $h ] ) );
				break;
			}
		}
		$rk = 'bkd_rl_v_' . md5( $ip . '|' . $id );
		if ( get_transient( $rk ) ) {
			return new WP_REST_Response( null, 204 );
		}
		set_transient( $rk, 1, 10 * MINUTE_IN_SECONDS );
		blockke_dev_count_view( $id );
		return new WP_REST_Response( null, 204 );
	}

	add_action(
		'rest_api_init',
		function () {
			if ( empty( blockke_dev_config()['prefetch'] ) ) { // only while prefetch is on, as only then are prefetches left uncounted
				return;
			}
			register_rest_route(
				'block/v1',
				'/view',
				array(
					'methods'             => 'POST',
					'callback'            => 'blockke_bkn_view',
					'permission_callback' => '__return_true',
				)
			);
		}
	);

	// The transition's styles and listeners on every #bkd page (development layout, area, segment, index and search pages),
	// early in <head>; development pages also hold their first paint until the hero is parsed, so the photo lands on it.
	add_action(
		'wp_head',
		function () {
			if ( empty( $GLOBALS['blockke_dev_active'] ) || ! blockke_bkn_vt_on() || ! empty( $GLOBALS['blockke_bkn_vt_printed'] ) ) {
				return;
			}
			$GLOBALS['blockke_bkn_vt_printed'] = true; // once per page (the search page checks the same flag)
			echo blockke_bkn_vt_head( ! is_singular( 'estate_property' ) ); // phpcs:ignore WordPress.Security.EscapeOutput -- fixed markup.
			if ( is_singular( 'estate_property' ) ) {
				echo '<link rel="expect" href="#bkd-hero-media" blocking="render">' . "\n";
			}
		},
		1
	);

	// After the development layout's own styles (wp_head 99), so these win on equal terms.
	add_action(
		'wp_head',
		function () {
			if ( empty( $GLOBALS['blockke_dev_active'] ) || ! is_singular( 'estate_property' ) || blockke_dev_lp_target() ) {
				return;
			}
			$vt = blockke_bkn_vt_on();
			if ( $vt || blockke_bkn_ctx_on() ) {
				echo '<style id="bkn-dev-css">' . blockke_bkn_dev_css( $vt ) . "</style>\n"; // phpcs:ignore WordPress.Security.EscapeOutput -- fixed CSS.
			}
		},
		100
	);
}
