<?php
/* marker line - keeps the connector from Markdown-converting this snippet; harmless in PHP */
/* <div> markup marker - the connector stores content as-is only when it sees a block-level HTML tag; harmless in PHP */
/* BLOCK - Homepage safety net v2.3: the library's page where the Block widgets cannot show it (as when Elementor's element cache holds
   none of them), else the rollback's (v2.2.1). */

if ( ! function_exists( 'blockke_hm23s_widgets' ) ) {

	/** Elementor, or null. */
	function blockke_hm23s_el() {
		return class_exists( 'Elementor\Plugin', false ) && isset( \Elementor\Plugin::$instance ) ? \Elementor\Plugin::$instance : null;
	}

	/** True when the Block widgets are declared. */
	function blockke_hm23s_widgets() {
		$el = blockke_hm23s_el();
		$wm = isset( $el->widgets_manager ) ? $el->widgets_manager : null;
		if ( ! class_exists( 'Blockke_Hm23w_Hero', false ) && function_exists( 'blockke_hm23w_register' ) && is_object( $wm ) && method_exists( $wm, 'get_widget_types' ) ) {
			$wm->get_widget_types();
		}
		return class_exists( 'Blockke_Hm23w_Hero', false );
	}

	/** True in Elementor's editor. */
	function blockke_hm23s_editor() {
		$pv = isset( $_GET['elementor-preview'] ) ? $_GET['elementor-preview'] : null; // phpcs:ignore WordPress.Security.NonceVerification
		if ( ( is_string( $pv ) && ctype_digit( $pv ) && current_user_can( 'edit_post', (int) $pv ) ) || ( wp_doing_ajax() && isset( $_REQUEST['action'] ) && 'elementor_ajax' === $_REQUEST['action'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
			return true;
		}
		$el = blockke_hm23s_el();
		return isset( $el->preview ) && is_object( $el->preview ) && method_exists( $el->preview, 'is_preview_mode' ) && $el->preview->is_preview_mode();
	}

	/** What snippet $k prints (fallback: 45262), or ''. */
	function blockke_hm23s_page( $k ) {
		$o  = (array) get_option( 'blockke_home_widgets', array() );
		$id = isset( $o[ $k ] ) && is_numeric( $o[ $k ] ) ? (int) $o[ $k ] : ( 'lib_snippet' === $k ? 0 : 45262 );
		$h  = $id > 0 ? (string) do_shortcode( '[wpcode id="' . $id . '"]' ) : '';
		return '' === trim( $h ) ? '' : $h;
	}

	/** The page (in the editor, a warning first), or ''. */
	function blockke_hm23s_run() {
		static $done = false;
		$live = did_action( 'wp_head' ) && ! doing_action( 'wp_head' ); // not a render before the body
		if ( $live && $done ) {
			return '';
		}
		$done = $done || $live;
		try {
			$on  = blockke_hm23s_widgets();
			$pid = (int) get_the_ID();
			$raw = $on && $pid ? get_post_meta( $pid, '_elementor_data', true ) : '';
			$raw = is_array( $raw ) ? wp_json_encode( $raw ) : (string) $raw;
			$ed  = blockke_hm23s_editor();
			if ( ! shortcode_exists( 'wpcode' ) || ( $on && '' === $raw ) ) {
				return '';
			}
			if ( $on && preg_match( '/"widgetType"\s*:\s*"block_/', $raw ) && ( $ed || ! function_exists( 'blockke_hm23w_stale' ) || ! blockke_hm23w_stale( $pid ) ) ) {
				if ( ! function_exists( 'blockke_hm23w_lib' ) || blockke_hm23w_lib() ) {
					return '';
				}
				$why = 'the homepage library snippet is missing or off: visitors see the rollback (v2.2.1).';
				$h   = $ed ? '' : blockke_hm23s_page( 'fallback_snippet' );
			} else {
				$why = $on ? 'the Block widgets are missing from this page (it shows the homepage with Block\'s own settings). Restore the last version that has them: Elementor > History > Revisions.'
					: 'the Block widgets snippet is off. Elementor cannot see the Block widgets here: do not press Update until it is switched back on in WPCode, or they will be removed from this page.';
				unset( $GLOBALS['blockke_hm23_lib_only'] ); // a plain run
				$h = blockke_hm23s_page( 'lib_snippet' );
				$h = '' !== $h ? $h : blockke_hm23s_page( 'fallback_snippet' );
			}
			return ( $ed ? '<div role="alert" style="padding:16px;border:2px solid #B3261E;background:#FDF3F2;color:#22303C;font:600 14px/1.5 sans-serif">' . esc_html( 'Block safety net: ' . $why ) . '</div>' : '' ) . $h;
		} catch ( Throwable $e ) {
			error_log( 'BLOCK homepage safety net: ' . $e->getMessage() ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions
			return '';
		}
	}
}

echo blockke_hm23s_run(); // phpcs:ignore WordPress.Security.EscapeOutput -- built from escaped parts
