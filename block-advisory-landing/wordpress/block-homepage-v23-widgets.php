<?php
/* marker line - keeps the connector from Markdown-converting this snippet; harmless in PHP */
/* <div> markup marker - the connector stores content as-is only when it sees a block-level HTML tag; harmless in PHP */
/**
 * BLOCK - Homepage Elementor widgets v2.3 (2026-11-04)
 * Eleven Elementor widgets in a "Block" category, one per homepage section, each with fields for its words, photos, links, switches
 * and counts, and a Style tab where the fonts can be changed: block_hero, block_strip, block_hoods, block_developments, block_listings,
 * block_diaspora, block_areas, block_founder, block_promise, block_homework, block_closing ("Block: Hero" ...). Every field starts at
 * what the homepage shows today, so a widget with nothing changed shows today's section. The hero's Style tab also has "Page style":
 * the primary and accent colours, the page and band backgrounds, the text colour and the corner style of every Block section on its
 * page (a small checked <style> printed with the hero; empty fields keep Block's own).
 * The sections themselves are drawn by the library snippet "BLOCK - Homepage library v2.3" (functions blockke_hm23_*), a shortcode-only
 * WPCode snippet whose id is in option blockke_home_widgets ( array( 'lib_snippet' => <id> ) ). A widget loads it once per request,
 * with $GLOBALS['blockke_hm23_lib_only'] set, by running [wpcode id=<id>]: run that way the library only defines its functions.
 * This snippet runs everywhere (WPCode location "everywhere"). At load it only adds four hooks and defines its own functions
 * (blockke_hm23w_*); it touches neither Elementor nor the database and prints nothing:
 *   - elementor/elements/categories_registered: the "Block" category;
 *   - elementor/widgets/register: the eleven widgets (their classes are declared there, once Elementor's Widget_Base exists);
 *   - template_redirect (before anything prints): on a page whose Elementor data holds Block widgets, Elementor's element cache of the
 *     page is deleted when it holds none of them (built while this snippet was off), so the same request builds it again with them;
 *   - wp_head, priority 99 (after the theme's and Elementor's stylesheets, before the site's Additional CSS at 101): on such a page, the
 *     library's stylesheet goes in the head (else it comes with the first section shown).
 * Without Elementor nothing happens. Without the library a widget shows a note in Elementor's editor and nothing on the public page.
 * Rollback: point page 44129 back to its Shortcode widget with [wpcode id=45262]; this snippet can stay on.
 */

if ( ! function_exists( 'blockke_hm23w_lib' ) ) {

	/** Loads the library snippet once (true when its functions are there). */
	function blockke_hm23w_lib() {
		static $tried = false;
		if ( function_exists( 'blockke_hm23_section' ) ) {
			return true;
		}
		if ( $tried ) {
			return false;
		}
		$tried = true;
		$opt   = get_option( 'blockke_home_widgets', array() );
		$id    = is_array( $opt ) && isset( $opt['lib_snippet'] ) && is_numeric( $opt['lib_snippet'] ) ? (int) $opt['lib_snippet'] : 0;
		if ( $id < 1 || ! shortcode_exists( 'wpcode' ) ) {
			return false;
		}
		$GLOBALS['blockke_hm23_lib_only'] = true; // the library then defines its functions and shows nothing
		ob_start();
		try {
			do_shortcode( '[wpcode id="' . $id . '"]' );
		} catch ( Throwable $e ) {
			error_log( 'BLOCK homepage widgets: the library snippet ' . $id . ' failed: ' . $e->getMessage() ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions
		}
		ob_end_clean();
		unset( $GLOBALS['blockke_hm23_lib_only'] );
		return function_exists( 'blockke_hm23_section' );
	}

	/** True in Elementor's editor and its preview (?elementor-preview only for a user who may edit that page, as Elementor checks it). */
	function blockke_hm23w_editing() {
		if ( function_exists( 'wp_doing_ajax' ) && wp_doing_ajax() && isset( $_REQUEST['action'] ) && 'elementor_ajax' === $_REQUEST['action'] ) { // phpcs:ignore WordPress.Security.NonceVerification
			return true;
		}
		if ( class_exists( 'Elementor\Plugin', false ) && isset( \Elementor\Plugin::$instance ) && is_object( \Elementor\Plugin::$instance ) ) {
			$el = \Elementor\Plugin::$instance;
			if ( isset( $el->editor ) && is_object( $el->editor ) && method_exists( $el->editor, 'is_edit_mode' ) && $el->editor->is_edit_mode() ) {
				return true;
			}
			if ( isset( $el->preview ) && is_object( $el->preview ) && method_exists( $el->preview, 'is_preview_mode' ) && $el->preview->is_preview_mode() ) {
				return true;
			}
		}
		$pv = isset( $_GET['elementor-preview'] ) ? $_GET['elementor-preview'] : null; // phpcs:ignore WordPress.Security.NonceVerification
		return is_string( $pv ) && ctype_digit( $pv ) && function_exists( 'current_user_can' ) && current_user_can( 'edit_post', (int) $pv );
	}

	/** Each widget's title, icon and search words in Elementor's panel. */
	function blockke_hm23w_meta() {
		return array(
			'hero'         => array( 'Block: Hero', 'eicon-banner', array( 'hero', 'search', 'photo', 'header' ) ),
			'strip'        => array( 'Block: Proof strip', 'eicon-counter', array( 'numbers', 'counts', 'strip', 'proof' ) ),
			'hoods'        => array( 'Block: Neighbourhood grid', 'eicon-apps', array( 'areas', 'neighbourhoods', 'grid' ) ),
			'developments' => array( 'Block: Developments', 'eicon-slider-push', array( 'developments', 'off-plan', 'carousel', 'projects' ) ),
			'listings'     => array( 'Block: Listings', 'eicon-posts-grid', array( 'listings', 'worth a viewing', 'for sale', 'for rent', 'cards' ) ),
			'diaspora'     => array( 'Block: Diaspora', 'eicon-globe', array( 'diaspora', 'abroad', 'steps' ) ),
			'areas'        => array( 'Block: Know your block', 'eicon-gallery-grid', array( 'areas', 'neighbourhoods', 'know your block' ) ),
			'founder'      => array( 'Block: Founder', 'eicon-person', array( 'founder', 'loyd', 'team', 'about' ) ),
			'promise'      => array( 'Block: Our promise', 'eicon-info-box', array( 'promise', 'services', 'valuation' ) ),
			'homework'     => array( 'Block: The homework', 'eicon-post-list', array( 'articles', 'blog', 'homework' ) ),
			'closing'      => array( 'Block: Closing', 'eicon-call-to-action', array( 'contact', 'whatsapp', 'call', 'closing' ) ),
		);
	}

	/**
	 * Each widget's fields: its Content tab as panels of controls ( panel id => array( label, array( control id => args ) ) ). A control id
	 * is the library's setting of the same name (blockke_hm23_defaults()), which gives its default.
	 */
	function blockke_hm23w_schema( $name ) {
		$txt = function ( $label, $x = array() ) {
			return array_merge( array( 'type' => \Elementor\Controls_Manager::TEXT, 'label' => $label, 'label_block' => true ), $x );
		};
		$area = function ( $label, $x = array() ) {
			return array_merge( array( 'type' => \Elementor\Controls_Manager::TEXTAREA, 'label' => $label, 'rows' => 3 ), $x );
		};
		$url = function ( $label ) {
			return array( 'type' => \Elementor\Controls_Manager::URL, 'label' => $label, 'label_block' => true, 'options' => array( 'url', 'is_external', 'nofollow' ) );
		};
		$sw = function ( $label, $x = array() ) {
			return array_merge( array( 'type' => \Elementor\Controls_Manager::SWITCHER, 'label' => $label, 'label_on' => 'Yes', 'label_off' => 'No', 'return_value' => 'yes' ), $x );
		};
		$num = function ( $label, $min, $max ) {
			return array( 'type' => \Elementor\Controls_Manager::NUMBER, 'label' => $label, 'min' => $min, 'max' => $max, 'step' => 1 );
		};
		$img = function ( $label ) {
			return array( 'type' => \Elementor\Controls_Manager::MEDIA, 'label' => $label );
		};
		$note = function ( $text ) {
			return array( 'type' => \Elementor\Controls_Manager::RAW_HTML, 'raw' => esc_html( $text ), 'content_classes' => 'elementor-descriptor' );
		};
		$lap  = array( 'bk_layout' => array( 'Layout', array( 'overlap' => $sw( 'Overlap the section above', array( 'description' => 'The section starts a little higher, over the foot of the one above. Its top corners follow the corner style (Block: Hero, Style tab, Page style).' ) ) ) ) );
		$head = function ( $more = true, $intro = true ) use ( $txt, $area, $url ) {
			$c = array( 'eyebrow' => $txt( 'Small line above the heading', array( 'description' => 'Leave empty to hide it.' ) ), 'title' => $area( 'Heading', array( 'rows' => 2, 'description' => 'Left empty, the usual heading shows.' ) ) );
			if ( $intro ) {
				$c['intro'] = $area( 'Text under the heading', array( 'description' => 'Leave empty to hide it.' ) );
			}
			if ( $more ) {
				$c['more']      = $txt( 'Link words (top right)' );
				$c['more_link'] = $url( 'Link' );
			}
			return $c;
		};
		switch ( $name ) {
			case 'hero':
				$pos = array();
				foreach ( array( 'center center', 'center top', 'center bottom', 'left center', 'left top', 'left bottom', 'right center', 'right top', 'right bottom' ) as $p ) {
					$pos[ $p ] = ucfirst( $p );
				}
				$pos['custom'] = 'Custom';
				return array(
					'bk_photo'  => array(
						'Photo',
						array(
							'image'        => $img( 'Photo' ),
							'image_phone'  => $img( 'Phone photo (optional)' ),
							'phone_note'   => $note( 'An upright crop of the same photo for phones held upright. Leave it empty to use the photo above.' ),
							'focus'        => array( 'type' => \Elementor\Controls_Manager::SELECT, 'label' => 'Photo position', 'options' => $pos ),
							'focus_custom' => $txt( 'Custom position', array( 'description' => 'Words or numbers, for example "30% 70%" or "left bottom".', 'condition' => array( 'focus' => 'custom' ) ) ),
							'caption'      => $txt( 'Caption over the photo', array( 'description' => 'Leave empty for none.' ) ),
						),
					),
					'bk_text'   => array(
						'Text',
						array(
							'eyebrow' => $txt( 'Small line above the heading', array( 'description' => 'Leave empty to hide it.' ) ),
							'title'   => $area( 'Heading', array( 'rows' => 2, 'description' => 'Left empty, the usual heading shows.' ) ),
							'sub'     => $area( 'Text under the heading', array( 'description' => 'Leave empty to hide it.' ) ),
						),
					),
					'bk_search' => array(
						'Search',
						array(
							'search'      => $sw( 'Show the search box' ),
							'search_note' => $note( 'Buy | Rent | Off-plan and one box for an area, a development or an estate. Its names come from the site\'s listings.' ),
						),
					),
				);
			case 'strip':
				return $lap + array(
					'bk_text' => array(
						'Numbers',
						array(
							'counts'         => $sw( 'Show the live counts' ),
							'label_sale'     => $txt( 'Words under the homes for sale count', array( 'description' => 'Leave empty to hide that count.' ) ),
							'label_projects' => $txt( 'Words under the developments count', array( 'description' => 'Leave empty to hide that count.' ) ),
							'label_areas'    => $txt( 'Words under the neighbourhoods count', array( 'description' => 'Leave empty to hide that count.' ) ),
							'reply_value'    => $txt( 'Last item: big words' ),
							'reply_label'    => $txt( 'Last item: words under them' ),
						),
					),
				);
			case 'hoods':
				return $lap + array(
					'bk_text' => array( 'Text and link', $head( true, false ) ),
					'bk_grid' => array( 'Grid', array( 'count' => $num( 'Number of neighbourhoods (0 hides the grid)', 0, 60 ) ) ),
				);
			case 'developments':
				return $lap + array(
					'bk_text'  => array( 'Text and link', $head( true, true ) ),
					'bk_cards' => array(
						'Cards',
						array(
							'slugs' => $area( 'Developments shown first', array( 'rows' => 6, 'description' => 'Project slugs, one per line (or separated by commas). Other off-plan developments fill the rest. Left empty, the usual list shows (in grey) and follows the site\'s settings.' ) ),
							'count' => $num( 'Number of cards', 1, 20 ),
							'cta'   => $txt( 'Card link words' ),
						),
					),
				);
			case 'listings':
				return $lap + array(
					'bk_style' => array(
						'Cards',
						array(
							'style'      => array( 'type' => \Elementor\Controls_Manager::SELECT, 'label' => 'Card style', 'options' => array( 'light' => 'Light: photo on top, words below', 'dark' => 'Dark: tall photo cards' ) ),
							'background' => array( 'type' => \Elementor\Controls_Manager::SELECT, 'label' => 'Background', 'options' => array( 'white' => 'Page background (white)', 'paper' => 'Band background (light grey)' ), 'condition' => array( 'style' => 'light' ) ),
							'count'      => $num( 'Cards per tab (shown in whole rows of three)', 1, 12 ),
							'badge_sale' => $txt( 'Badge on homes for sale', array( 'condition' => array( 'style' => 'light' ) ) ),
							'badge_rent' => $txt( 'Badge on rentals', array( 'condition' => array( 'style' => 'light' ) ) ),
						),
					),
					'bk_text'  => array(
						'Text',
						array(
							'eyebrow_featured' => $txt( 'Small line when every card is featured' ),
							'eyebrow_newest'   => $txt( 'Small line otherwise' ),
							'title'            => $area( 'Heading', array( 'rows' => 2 ) ),
							'intro_featured'   => $area( 'Text when every card is featured' ),
							'intro_mixed'      => $area( 'Text when some cards are featured' ),
							'intro_newest'     => $area( 'Text when none is featured' ),
							'tab_sale'         => $txt( 'Tab: for sale' ),
							'tab_rent'         => $txt( 'Tab: for rent' ),
						),
					),
					'bk_links' => array(
						'Buttons',
						array(
							'button_sale' => $txt( 'Button under the homes for sale', array( 'description' => '{count} is replaced by the live number of homes for sale.' ) ),
							'sale_link'   => $url( 'Its link' ),
							'button_rent' => $txt( 'Button under the rentals', array( 'description' => '{count} is replaced by the live number of rentals.' ) ),
							'rent_link'   => $url( 'Its link' ),
						),
					),
				);
			case 'diaspora':
				$rep = class_exists( 'Elementor\Repeater' ) ? new \Elementor\Repeater() : null;
				if ( $rep ) {
					$rep->add_control( 'title', $txt( 'Step' ) );
					$rep->add_control( 'text', $area( 'Text' ) );
				}
				return $lap + array(
					'bk_text'    => array( 'Text', $head( false, true ) ),
					'bk_numbers' => array(
						'Numbers',
						array(
							'numbers' => $sw( 'Show the enquiry numbers and the map', array( 'description' => 'Counted from the website\'s stored enquiries, shown only when there are enough. Off: the countries and the line below. Check the countries before switching this on: on the live enquiries the phone codes put Belarus and Belgium in the top five, which looks misread.' ) ),
							'chips'   => $txt( 'Countries (with the numbers off)', array( 'description' => 'Separated by commas.' ) ),
							'proof'   => $txt( 'Line after them' ),
						),
					),
					'bk_steps'   => array(
						'Steps',
						$rep ? array( 'steps' => array( 'type' => \Elementor\Controls_Manager::REPEATER, 'label' => 'Steps', 'fields' => $rep->get_controls(), 'title_field' => '{{{ title }}}' ) ) : array(),
					),
					'bk_buttons' => array(
						'Buttons',
						array(
							'wa_text'    => $txt( 'WhatsApp button words' ),
							'wa_topic'   => $txt( 'WhatsApp message ends with', array( 'description' => 'The message starts "Hi Block — I\'m on block.ke and I\'d like to talk about".' ) ),
							'guide_text' => $txt( 'Second button words' ),
							'guide_link' => $url( 'Its link' ),
						),
					),
				);
			case 'areas':
				return $lap + array(
					'bk_text'  => array( 'Text and link', $head( true, true ) ),
					'bk_tiles' => array( 'Tiles', array( 'slugs' => $area( 'Neighbourhoods', array( 'rows' => 6, 'description' => 'Area slugs, one per line (or separated by commas), in order. Left empty, the usual list shows (in grey) and follows the site\'s settings.' ) ) ) ),
				);
			case 'founder':
				return $lap + array(
					'bk_photo'   => array(
						'Photo',
						array(
							'image'         => $img( 'Photo' ),
							'alt'           => $txt( 'Photo description (for screen readers)' ),
							'photo_caption' => $txt( 'Caption on the photo', array( 'description' => 'Leave empty for none.' ) ),
						),
					),
					'bk_text'    => array(
						'Text',
						array(
							'eyebrow' => $txt( 'Small line above the heading', array( 'description' => 'Leave empty to hide it.' ) ),
							'title'   => $area( 'Heading', array( 'rows' => 2 ) ),
							'quote'   => $area( 'Quote' ),
							'body'    => $area( 'Text', array( 'rows' => 6 ) ),
							'name'    => $txt( 'Name' ),
							'role'    => $txt( 'Role' ),
						),
					),
					'bk_buttons' => array(
						'Buttons',
						array(
							'wa_text'      => $txt( 'WhatsApp button words' ),
							'wa_topic'     => $txt( 'WhatsApp message ends with' ),
							'consult_text' => $txt( 'Second button words' ),
							'consult_link' => $url( 'Its link' ),
						),
					),
					'bk_team'    => array(
						'Team',
						array(
							'team'           => $txt( 'Team members (agent IDs)', array( 'description' => 'Agent IDs, separated by commas. Their photos and first names show; an ID that is not a published agent is left out. Left empty, the usual team shows (in grey) and follows the site\'s settings.' ) ),
							'team_text'      => $txt( 'Words after the names' ),
							'team_link_text' => $txt( 'Link words' ),
							'team_link'      => $url( 'Link' ),
						),
					),
				);
			case 'promise':
				$rep = class_exists( 'Elementor\Repeater' ) ? new \Elementor\Repeater() : null;
				if ( $rep ) {
					$rep->add_control( 'title', $txt( 'Title' ) );
					$rep->add_control( 'text', $area( 'Text' ) );
					$rep->add_control( 'link', $url( 'Link' ) + array( 'description' => 'Optional: a card without a link shows without its arrow.' ) );
				}
				return $lap + array(
					'bk_photo'    => array(
						'Background photo',
						array(
							'image'      => $img( 'Photo' ),
							'photo_note' => $note( 'Remove the photo for a navy band. The band is navy too when this is the hero\'s photo.' ),
							'caption'    => $txt( 'Caption', array( 'description' => 'Leave empty for none.' ) ),
						),
					),
					'bk_text'     => array(
						'Promise',
						array(
							'eyebrow'     => $txt( 'Small line above the heading', array( 'description' => 'Leave empty to hide it.' ) ),
							'title'       => $area( 'Heading', array( 'rows' => 2 ) ),
							'statement'   => $area( 'Statement', array( 'rows' => 5 ) ),
							'button'      => $txt( 'Button words' ),
							'button_link' => $url( 'Button link' ),
						),
					),
					'bk_services' => array(
						'Service cards',
						$rep ? array( 'services' => array( 'type' => \Elementor\Controls_Manager::REPEATER, 'label' => 'Cards', 'fields' => $rep->get_controls(), 'title_field' => '{{{ title }}}' ) ) : array(),
					),
					'bk_val'      => array(
						'Valuation band',
						array(
							'val_title'  => $txt( 'Heading' ),
							'val_text'   => $area( 'Text' ),
							'val_button' => $txt( 'Button words' ),
							'val_link'   => $url( 'Button link' ),
						),
					),
				);
			case 'homework':
				return $lap + array(
					'bk_text'  => array( 'Text and link', $head( true, true ) ),
					'bk_items' => array( 'Articles', array( 'count' => $num( 'Number of articles (the newest one large)', 1, 7 ) ) ),
				);
			case 'closing':
				return $lap + array(
					'bk_text'    => array( 'Text', $head( false, true ) ),
					'bk_buttons' => array(
						'Buttons',
						array(
							'wa_text'      => $txt( 'WhatsApp button words' ),
							'wa_topic'     => $txt( 'WhatsApp message ends with' ),
							'call_text'    => $txt( 'Call button words', array( 'description' => '{phone} is replaced by the phone number (the site\'s settings), so the words and the number it dials always agree. {phone} works in every text field.' ) ),
							'alt'          => $txt( 'Words before the consultation link', array( 'description' => 'Leave empty to hide them.' ) ),
							'consult_text' => $txt( 'Consultation link words' ),
							'consult_link' => $url( 'Consultation link' ),
							'hours'        => $txt( 'Opening hours', array( 'description' => 'Leave empty to hide them.' ) ),
						),
					),
					'bk_bar'     => array(
						'Phone bar',
						array(
							'bar'       => $sw( 'Show the WhatsApp | Call bar on phones', array( 'description' => 'Fixed at the foot of the screen once the hero has scrolled away.' ) ),
							'bar_wa'    => $txt( 'WhatsApp words', array( 'condition' => array( 'bar' => 'yes' ) ) ),
							'bar_call'  => $txt( 'Call words', array( 'condition' => array( 'bar' => 'yes' ), 'description' => '{phone} is replaced by the phone number.' ) ),
							'bar_topic' => $txt( 'WhatsApp message ends with', array( 'condition' => array( 'bar' => 'yes' ) ) ),
						),
					),
				);
		}
		return array();
	}

	/** A library setting as an Elementor control's value (its default). */
	function blockke_hm23w_value( $type, $v ) {
		if ( \Elementor\Controls_Manager::MEDIA === $type ) {
			if ( is_numeric( $v ) && (int) $v > 0 ) {
				$u = wp_get_attachment_url( (int) $v );
				return array( 'id' => (int) $v, 'url' => is_string( $u ) ? $u : '' );
			}
			return array( 'id' => '', 'url' => is_string( $v ) ? $v : '' );
		}
		if ( \Elementor\Controls_Manager::URL === $type ) {
			return is_array( $v ) && isset( $v['url'] ) ? array( 'url' => (string) $v['url'], 'is_external' => empty( $v['ext'] ) ? '' : 'on', 'nofollow' => empty( $v['nf'] ) ? '' : 'on' ) : array( 'url' => '', 'is_external' => '', 'nofollow' => '' );
		}
		if ( \Elementor\Controls_Manager::SWITCHER === $type ) {
			return $v ? 'yes' : '';
		}
		if ( \Elementor\Controls_Manager::NUMBER === $type ) {
			return (int) $v;
		}
		if ( \Elementor\Controls_Manager::REPEATER === $type ) {
			$rows = array();
			foreach ( (array) $v as $row ) {
				$r = array( 'title' => (string) $row['title'], 'text' => (string) $row['text'] );
				if ( isset( $row['link'] ) ) {
					$r['link'] = blockke_hm23w_value( \Elementor\Controls_Manager::URL, $row['link'] );
				}
				$rows[] = $r;
			}
			return $rows;
		}
		if ( is_array( $v ) ) { // a list (slugs one per line, agent ids with commas)
			return implode( is_int( reset( $v ) ) ? ', ' : "\n", $v );
		}
		return is_scalar( $v ) ? (string) $v : '';
	}

	/** The fields' CSS (Elementor's typography controls): !important, so the owner's choice wins over the page's own CSS. */
	function blockke_hm23w_type_options() {
		$o = array( 'font_family' => array( 'selectors' => array( '{{SELECTOR}}' => 'font-family: "{{VALUE}}", sans-serif !important;' ) ) );
		foreach ( array( 'font_size', 'line_height', 'letter_spacing', 'word_spacing' ) as $k ) {
			$o[ $k ] = array( 'selectors' => array( '{{SELECTOR}}' => str_replace( '_', '-', $k ) . ': {{SIZE}}{{UNIT}} !important;' ) );
		}
		foreach ( array( 'font_weight', 'text_transform', 'font_style', 'text_decoration' ) as $k ) {
			$o[ $k ] = array( 'selectors' => array( '{{SELECTOR}}' => str_replace( '_', '-', $k ) . ': {{VALUE}} !important;' ) );
		}
		return $o;
	}

	/**
	 * The hero's "Page style" fields: control id => array( label, description, the library's page style key ). The colours have no
	 * Global colours (their value is printed by the hero, not by Elementor's CSS file); empty keeps Block's own. An id here must not be
	 * a section id too (bk_photo, bk_text, ...): Elementor keeps sections and controls in one list and drops a control named like a section.
	 */
	function blockke_hm23w_style_fields() {
		return array(
			'bk_primary' => array( 'Primary colour', 'Buttons, headings and the dark bands. Empty: Block\'s navy.', 'primary' ),
			'bk_accent'  => array( 'Accent colour', 'The short line over each heading, small labels and the bar under the promise. Empty: Block\'s brass.', 'accent' ),
			'bk_page'    => array( 'Page background', 'Empty: white.', 'page' ),
			'bk_band'    => array( 'Band background', 'The light grey sections. Empty: Block\'s light grey.', 'band' ),
			'bk_ink'     => array( 'Text colour', 'Empty: Block\'s ink.', 'text' ), // not bk_text: that is the id of a Content section
		);
	}

	/**
	 * register_controls(): the Content tab from the schema (defaults from the library) and the Style tab's three typography groups. The
	 * first Update in Elementor saves every non-empty text default with the page (Elementor keeps text and textarea values even when they
	 * equal the default), so those fields are the page's from then on; the lists that come from the site's settings (developments,
	 * neighbourhoods, team) start empty with today's list as a placeholder, and the call button's words carry {phone}.
	 */
	function blockke_hm23w_controls( $w, $name ) {
		$lib = blockke_hm23w_lib();
		$d   = $lib ? blockke_hm23_defaults( $name ) : array();
		foreach ( blockke_hm23w_schema( $name ) as $pid => $panel ) {
			if ( ! $panel[1] ) {
				continue;
			}
			$w->start_controls_section( $pid, array( 'label' => $panel[0], 'tab' => \Elementor\Controls_Manager::TAB_CONTENT ) );
			foreach ( $panel[1] as $id => $args ) {
				if ( 'focus' === $id && array_key_exists( 'focus', $d ) ) { // a position that is not one of the choices is a custom one
					$f               = is_string( $d['focus'] ) ? strtolower( trim( $d['focus'] ) ) : '';
					$args['default'] = isset( $args['options'][ $f ] ) && 'custom' !== $f ? $f : 'custom';
					$d['focus_custom'] = isset( $args['options'][ $f ] ) ? '' : $f;
				} elseif ( array_key_exists( $id, $d ) && in_array( $id, array( 'slugs', 'team' ), true ) ) {
					$args['default']     = ''; // empty = the usual list, from the option: Elementor would save a text default with the first Update and freeze it
					$args['placeholder'] = blockke_hm23w_value( $args['type'], $d[ $id ] );
				} elseif ( array_key_exists( $id, $d ) ) {
					$args['default'] = blockke_hm23w_value( $args['type'], $d[ $id ] );
				}
				$w->add_control( $id, $args );
			}
			$w->end_controls_section();
		}
		$w->start_controls_section( 'bk_fonts', array( 'label' => 'Fonts', 'tab' => \Elementor\Controls_Manager::TAB_STYLE ) );
		$w->add_control( 'bk_fonts_note', array( 'type' => \Elementor\Controls_Manager::RAW_HTML, 'raw' => esc_html( 'The page uses Instrument Serif for headings and Montserrat for text. Choose another font here for this section only.' ), 'content_classes' => 'elementor-descriptor' ) );
		foreach ( array(
			'bk_head'    => array( 'Heading', '{{WRAPPER}} .bkh-display' ),
			'bk_body'    => array( 'Body text', '{{WRAPPER}} .bkh p' ),
			'bk_eyebrow' => array( 'Small line above headings', '{{WRAPPER}} .bkh-eyebrow' ),
		) as $gid => $g ) {
			$w->add_group_control( \Elementor\Group_Control_Typography::get_type(), array( 'name' => $gid, 'label' => $g[0], 'selector' => $g[1], 'fields_options' => blockke_hm23w_type_options() ) );
		}
		$w->end_controls_section();
		if ( 'hero' !== $name ) {
			return;
		}
		$w->start_controls_section( 'bk_page_style', array( 'label' => 'Page style', 'tab' => \Elementor\Controls_Manager::TAB_STYLE ) );
		$w->add_control( 'bk_page_style_note', array( 'type' => \Elementor\Controls_Manager::RAW_HTML, 'raw' => esc_html( 'Colours and corners for every Block section on this page. Leave a colour empty to keep Block\'s own. Small text that would be hard to read on a colour chosen here is made darker or lighter until it passes.' ), 'content_classes' => 'elementor-descriptor' ) );
		foreach ( blockke_hm23w_style_fields() as $id => $f ) {
			$w->add_control( $id, array( 'type' => \Elementor\Controls_Manager::COLOR, 'label' => $f[0], 'description' => $f[1], 'default' => '', 'alpha' => false, 'global' => array( 'active' => false ) ) );
		}
		$w->add_control( 'bk_corners', array( 'type' => \Elementor\Controls_Manager::SELECT, 'label' => 'Corner style', 'description' => 'Photos, cards, buttons, fields, tabs and panels, and the top of a section set to overlap the one above. Rounded gives the softer look of the earlier homepage.', 'default' => 'square', 'options' => array( 'square' => 'Square', 'soft' => 'Soft', 'rounded' => 'Rounded' ) ) );
		$w->end_controls_section();
	}

	/** render(): the section with the widget's settings, from the library; without it, a note in the editor and nothing on the page. */
	function blockke_hm23w_render( $w, $name ) {
		if ( ! blockke_hm23w_lib() ) {
			if ( blockke_hm23w_editing() ) {
				echo '<div style="padding:16px 20px;border:1px dashed #b98a44;background:#fbf9f6;color:#22303c;font:500 13px/1.5 Montserrat,sans-serif">' . esc_html( 'Block: the homepage library snippet is not available (option blockke_home_widgets, lib_snippet). Nothing of this section shows on the public page.' ) . '</div>';
			}
			return;
		}
		$set = $w->get_settings_for_display();
		$s   = is_array( $set ) ? array_intersect_key( $set, blockke_hm23_defaults( $name ) + array( 'focus_custom' => true ) ) : array(); // the section's own settings only
		if ( 'hero' === $name && is_array( $set ) && function_exists( 'blockke_hm23_page_style' ) ) { // the page style, for every Block section on the page
			$in = array();
			foreach ( blockke_hm23w_style_fields() as $id => $f ) {
				$in[ $f[2] ] = isset( $set[ $id ] ) && is_string( $set[ $id ] ) ? $set[ $id ] : '';
			}
			$in['corners'] = isset( $set['bk_corners'] ) && is_string( $set['bk_corners'] ) ? $set['bk_corners'] : '';
			echo blockke_hm23_page_style( $in ); // phpcs:ignore WordPress.Security.EscapeOutput -- built from checked colours only
		}
		echo blockke_hm23_section( $name, $s ); // phpcs:ignore WordPress.Security.EscapeOutput -- the library escapes every part
	}

	/** elementor/elements/categories_registered: the "Block" category. */
	function blockke_hm23w_category( $elements_manager ) {
		if ( is_object( $elements_manager ) && method_exists( $elements_manager, 'add_category' ) ) {
			$elements_manager->add_category( 'block', array( 'title' => 'Block', 'icon' => 'eicon-home' ) );
		}
	}

	/** elementor/widgets/register: the widget classes (declared here, once Elementor's Widget_Base exists) and the eleven widgets. */
	function blockke_hm23w_register( $widgets_manager ) {
		if ( ! is_object( $widgets_manager ) || ! class_exists( 'Elementor\Widget_Base' ) ) {
			return;
		}
		if ( ! class_exists( 'Blockke_Hm23w_Base', false ) ) {
			/* Each method declares its return type: valid whether Elementor's own declares the same type or none (a method without
			   one under a parent that has one is a fatal error on every Elementor page). Each subclass's $bk is a key of the meta. */
			abstract class Blockke_Hm23w_Base extends \Elementor\Widget_Base {
				protected $bk = '';
				public function get_name(): string {
					return 'block_' . $this->bk;
				}
				public function get_title(): string {
					$m = blockke_hm23w_meta();
					return $m[ $this->bk ][0];
				}
				public function get_icon(): string {
					$m = blockke_hm23w_meta();
					return $m[ $this->bk ][1];
				}
				public function get_categories(): array {
					return array( 'block' );
				}
				public function get_keywords(): array {
					$m = blockke_hm23w_meta();
					return array_merge( array( 'block', 'homepage' ), $m[ $this->bk ][2] );
				}
				protected function register_controls(): void {
					blockke_hm23w_controls( $this, $this->bk );
				}
				protected function render(): void {
					blockke_hm23w_render( $this, $this->bk );
				}
				/**
				 * Elementor's plain text of the page (post_content, written on every Update): none, as Elementor's own Shortcode widget writes
				 * only its shortcode; the safety net's shortcode is enough. (": void" is compatible whether Elementor's own has a return type or not.)
				 */
				public function render_plain_content(): void {}
			}
			class Blockke_Hm23w_Hero extends Blockke_Hm23w_Base {
				protected $bk = 'hero';
			}
			class Blockke_Hm23w_Strip extends Blockke_Hm23w_Base {
				protected $bk = 'strip';
			}
			class Blockke_Hm23w_Hoods extends Blockke_Hm23w_Base {
				protected $bk = 'hoods';
			}
			class Blockke_Hm23w_Developments extends Blockke_Hm23w_Base {
				protected $bk = 'developments';
			}
			class Blockke_Hm23w_Listings extends Blockke_Hm23w_Base {
				protected $bk = 'listings';
			}
			class Blockke_Hm23w_Diaspora extends Blockke_Hm23w_Base {
				protected $bk = 'diaspora';
			}
			class Blockke_Hm23w_Areas extends Blockke_Hm23w_Base {
				protected $bk = 'areas';
			}
			class Blockke_Hm23w_Founder extends Blockke_Hm23w_Base {
				protected $bk = 'founder';
			}
			class Blockke_Hm23w_Promise extends Blockke_Hm23w_Base {
				protected $bk = 'promise';
			}
			class Blockke_Hm23w_Homework extends Blockke_Hm23w_Base {
				protected $bk = 'homework';
			}
			class Blockke_Hm23w_Closing extends Blockke_Hm23w_Base {
				protected $bk = 'closing';
			}
		}
		foreach ( array_keys( blockke_hm23w_meta() ) as $bk ) {
			$cls = 'Blockke_Hm23w_' . ucfirst( $bk );
			if ( class_exists( $cls, false ) ) {
				if ( method_exists( $widgets_manager, 'register' ) ) {
					$widgets_manager->register( new $cls() );
				} elseif ( method_exists( $widgets_manager, 'register_widget_type' ) ) {
					$widgets_manager->register_widget_type( new $cls() );
				}
			}
		}
	}

	/** The id of the page being shown when it is built with Elementor and its data holds Block widgets, else 0. */
	function blockke_hm23w_page() {
		if ( ! class_exists( 'Elementor\Plugin', false ) || ! is_singular() ) {
			return 0;
		}
		$id = (int) get_queried_object_id();
		if ( ! $id || 'builder' !== get_post_meta( $id, '_elementor_edit_mode', true ) ) {
			return 0;
		}
		$raw = get_post_meta( $id, '_elementor_data', true );
		$raw = is_array( $raw ) ? wp_json_encode( $raw ) : $raw;
		return is_string( $raw ) && false !== strpos( $raw, '"widgetType":"block_' ) ? $id : 0;
	}

	/**
	 * True when Elementor's element cache of page $id is in use (the cache on and the copy not expired, as Elementor reads it) and holds
	 * no Block widget: none stored as HTML (its Cache set to "Active") and no placeholder of one ([elementor-element ... data="..."], a
	 * widget drawn afresh on each request, its data in base64). Such a copy was built while the Block widgets were not registered (this
	 * snippet off): Elementor left them out, so the page it serves has none of them. The safety net asks this too.
	 */
	function blockke_hm23w_stale( $id ) {
		if ( 'disable' === get_option( 'elementor_element_cache_ttl', '' ) ) {
			return false;
		}
		$c = get_post_meta( $id, '_elementor_element_cache', true );
		$c = is_string( $c ) ? json_decode( $c, true ) : $c;
		if ( ! is_array( $c ) || empty( $c['timeout'] ) || current_time( 'timestamp' ) > $c['timeout'] || ! isset( $c['value'] ) || ! is_array( $c['value'] ) ) {
			return false; // no copy, or one Elementor will not use: it builds a new one, with the Block widgets
		}
		$s = isset( $c['value']['content'] ) && is_string( $c['value']['content'] ) ? $c['value']['content'] : '';
		if ( preg_match( '/class="[^"]*elementor-widget-block_/', $s ) ) {
			return false; // a Block widget's own wrapper (the stylesheet's [class*="elementor-widget-block_"] is not one)
		}
		preg_match_all( '/\[elementor-element [^\]]*?data="([A-Za-z0-9+\/=]+)"/', $s, $m );
		foreach ( $m[1] as $b ) {
			if ( false !== strpos( (string) base64_decode( $b ), '"widgetType":"block_' ) ) {
				return false;
			}
		}
		return true;
	}

	/**
	 * template_redirect, before anything prints (an SEO plugin may render the content inside wp_head): deletes the element cache that
	 * blockke_hm23w_stale() finds, so Elementor builds it again in this request, with the Block widgets. Kept, the page would show none
	 * of them until the copy expired (24 hours by default). Never in Elementor's editor or preview. Nothing is deleted while the library
	 * is missing (the Block widgets would show nothing either; the safety net shows the rollback with or without the copy).
	 */
	function blockke_hm23w_heal() {
		$id = blockke_hm23w_page();
		if ( $id && ! blockke_hm23w_editing() && blockke_hm23w_stale( $id ) && blockke_hm23w_lib() ) {
			delete_post_meta( $id, '_elementor_element_cache' );
		}
	}

	/** wp_head, after the theme's and Elementor's stylesheets (8): on a page whose data holds Block widgets, the library's stylesheet. */
	function blockke_hm23w_head() {
		if ( blockke_hm23w_page() && blockke_hm23w_lib() && function_exists( 'blockke_hm23_head_css' ) ) {
			blockke_hm23_head_css();
		}
	}

	add_action( 'elementor/elements/categories_registered', 'blockke_hm23w_category' );
	add_action( 'elementor/widgets/register', 'blockke_hm23w_register' );
	add_action( 'template_redirect', 'blockke_hm23w_heal' );
	add_action( 'wp_head', 'blockke_hm23w_head', 99 );
}
