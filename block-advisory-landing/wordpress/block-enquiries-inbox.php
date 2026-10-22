<?php
/* marker line - keeps the connector from Markdown-converting this snippet; harmless in PHP */
/* <div> markup marker - the connector stores content as-is only when it sees a block-level HTML tag; harmless in PHP */
/**
 * BLOCK - Enquiries inbox v1.0 (2026-10-10)
 *
 * Every website enquiry in one place inside WordPress, whether or not the emails arrive.
 *
 * - Enquiries (admin menu, administrators only): the price-check popup's leads (option
 *   blockke_buyer_leads) and the development and landing page leads (option blockke_dev_leads),
 *   newest first, with WhatsApp, call and email links; every email the site tries to send, theme
 *   contact forms included, and whether WordPress could hand it to the mail server; and Zoho CRM's
 *   reply to each lead, to find out why leads don't show up there.
 * - A "Latest enquiries" box on the WordPress dashboard, and a count of new ones on the menu.
 * - Download every enquiry as a CSV file, and send a test email to the enquiry addresses.
 * - Enquiries the theme's contact forms send to an agent are copied (Bcc) to the enquiry addresses:
 *   the price-check popup's notify list, set in option blockke_buyer_popup['notify_emails'].
 * Keeps the last 300 emails (option blockke_mail_log) and 50 Zoho replies (blockke_zoho_log),
 * neither autoloaded. Emails about passwords, logins or codes are listed without their text.
 * Rollback: deactivate this snippet. Nothing on the public site changes.
 * Created by Claude session 2026-10-10.
 */

if ( ! function_exists( 'blockke_inbox_recipients' ) ) {

	/** Where enquiry emails go: the popup's and development pages' lists, else the admin email. */
	function blockke_inbox_recipients() {
		$to = array();
		if ( function_exists( 'blockke_bp_config' ) ) {
			$to = array_merge( $to, (array) blockke_bp_config()['notify_emails'] );
		}
		if ( function_exists( 'blockke_dev_config' ) ) {
			$to = array_merge( $to, (array) blockke_dev_config()['notify_emails'] );
		}
		$to = array_values( array_unique( array_filter( array_map( 'sanitize_email', $to ) ) ) );
		return $to ? $to : array( get_option( 'admin_email' ) );
	}

	/** Account and security emails (password resets, login links, codes) are logged without their text. */
	function blockke_inbox_is_private( $subject ) {
		return (bool) preg_match( '/password|reset|log ?in|sign ?in|verif|one.time|\botp\b|2fa|two.factor|security code|magic link|activation/i', (string) $subject );
	}

	/** Agents' email addresses, for copying their contact-form enquiries to the enquiry addresses. */
	function blockke_inbox_agent_emails() {
		static $emails = null;
		if ( null === $emails ) {
			$emails = array();
			foreach ( get_posts( array( 'post_type' => 'estate_agent', 'post_status' => 'publish', 'posts_per_page' => 50, 'fields' => 'ids' ) ) as $aid ) {
				$em = strtolower( sanitize_email( (string) get_post_meta( $aid, 'agent_email', true ) ) );
				if ( $em ) {
					$emails[] = $em;
				}
			}
		}
		return $emails;
	}

	/** Logs every email WordPress tries to send, and copies agents' enquiries to the enquiry addresses. */
	function blockke_inbox_log_mail( $atts ) {
		$to_list = isset( $atts['to'] ) ? ( is_array( $atts['to'] ) ? $atts['to'] : explode( ',', (string) $atts['to'] ) ) : array();
		$to_list = array_values( array_filter( array_map( 'trim', $to_list ) ) );
		$subject = isset( $atts['subject'] ) ? wp_strip_all_tags( (string) $atts['subject'] ) : '';
		$private = blockke_inbox_is_private( $subject );

		$copied = array();
		if ( ! $private && 0 !== strpos( $subject, '[Block]' ) ) { // the site's own enquiry emails already go to the list
			$lower  = array_map( 'strtolower', $to_list );
			$agents = array_intersect( $lower, blockke_inbox_agent_emails() );
			if ( $agents ) {
				$copied = array_diff( array_map( 'strtolower', blockke_inbox_recipients() ), $lower );
				if ( $copied ) {
					$headers = isset( $atts['headers'] ) ? $atts['headers'] : array();
					if ( ! is_array( $headers ) ) {
						$headers = '' === trim( (string) $headers ) ? array() : explode( "\n", str_replace( "\r\n", "\n", (string) $headers ) );
					}
					foreach ( $copied as $em ) {
						$headers[] = 'Bcc: ' . $em;
					}
					$atts['headers'] = $headers;
				}
			}
		}

		$body = '';
		if ( ! $private && isset( $atts['message'] ) ) {
			$body = preg_replace( '#<(br|/p|/div|/tr|/li|/h[1-6])\b[^>]*>#i', "\n", (string) $atts['message'] );
			$body = html_entity_decode( wp_strip_all_tags( $body ), ENT_QUOTES, 'UTF-8' );
			$body = trim( preg_replace( "/\n{3,}/", "\n\n", preg_replace( "/[ \t]+/", ' ', $body ) ) );
		}
		$log = get_option( 'blockke_mail_log', array() );
		if ( ! is_array( $log ) ) {
			$log = array();
		}
		$id = wp_generate_password( 12, false, false );
		array_unshift(
			$log,
			array(
				'id'      => $id,
				'time'    => current_time( 'mysql' ),
				'to'      => mb_substr( implode( ', ', $to_list ), 0, 300 ),
				'copied'  => implode( ', ', $copied ),
				'subject' => mb_substr( $subject, 0, 300 ),
				'body'    => mb_substr( $body, 0, 2500 ),
				'private' => $private,
				'status'  => 'pending',
			)
		);
		update_option( 'blockke_mail_log', array_slice( $log, 0, 300 ), false );
		$GLOBALS['blockke_inbox_mail_id'] = $id;
		return $atts;
	}
	add_filter( 'wp_mail', 'blockke_inbox_log_mail', 9999 );

	function blockke_inbox_mail_status( $status ) {
		$id = isset( $GLOBALS['blockke_inbox_mail_id'] ) ? $GLOBALS['blockke_inbox_mail_id'] : '';
		if ( '' === $id ) {
			return;
		}
		$GLOBALS['blockke_inbox_mail_id'] = '';
		$log = get_option( 'blockke_mail_log', array() );
		foreach ( (array) $log as $k => $row ) {
			if ( isset( $row['id'] ) && $row['id'] === $id ) {
				$log[ $k ]['status'] = $status;
				update_option( 'blockke_mail_log', $log, false );
				break;
			}
		}
	}
	add_action(
		'wp_mail_succeeded',
		function () {
			blockke_inbox_mail_status( 'sent' );
		}
	);
	add_action(
		'wp_mail_failed',
		function ( $err ) {
			blockke_inbox_mail_status( 'failed: ' . ( is_wp_error( $err ) ? $err->get_error_message() : 'unknown error' ) );
		}
	);

	/** Zoho CRM's reply to each Web-to-Lead post: the price-check popup only records the HTTP status. */
	add_action(
		'http_api_debug',
		function ( $response, $context, $class, $args, $url ) {
			if ( 'response' !== $context || false === stripos( (string) $url, 'WebToLeadForm' ) ) {
				return;
			}
			$code = 0;
			$loc  = '';
			$html = '';
			if ( is_wp_error( $response ) ) {
				$text = $response->get_error_message();
			} else {
				$code = (int) wp_remote_retrieve_response_code( $response );
				$loc  = (string) wp_remote_retrieve_header( $response, 'location' );
				$html = (string) wp_remote_retrieve_body( $response );
				$text = preg_replace( '#<(script|style)\b[^>]*>.*?</\1>#is', ' ', $html );
				$text = preg_replace( '#<(br|/p|/div|/li|/td|/tr|/h[1-6])\b[^>]*>#i', ' ', $text );
				$text = trim( preg_replace( '/\s+/u', ' ', html_entity_decode( wp_strip_all_tags( $text ), ENT_QUOTES, 'UTF-8' ) ) );
			}
			$b   = isset( $args['body'] ) && is_array( $args['body'] ) ? $args['body'] : array();
			$who = trim( ( isset( $b['First Name'] ) ? $b['First Name'] : '' ) . ' ' . ( isset( $b['Last Name'] ) && ( ! isset( $b['First Name'] ) || $b['Last Name'] !== $b['First Name'] ) ? $b['Last Name'] : '' ) );
			$log = get_option( 'blockke_zoho_log', array() );
			if ( ! is_array( $log ) ) {
				$log = array();
			}
			array_unshift(
				$log,
				array(
					'time'     => current_time( 'mysql' ),
					'name'     => mb_substr( $who, 0, 80 ),
					'code'     => $code,
					'location' => mb_substr( $loc, 0, 300 ),
					'text'     => mb_substr( $text, 0, 600 ),
					'html'     => mb_substr( $html, 0, 1500 ),
				)
			);
			update_option( 'blockke_zoho_log', array_slice( $log, 0, 50 ), false );
		},
		10,
		5
	);

	/** Website enquiries from the price-check popup and the development pages, newest first. */
	function blockke_inbox_leads() {
		$s    = function ( $a, $k ) {
			return isset( $a[ $k ] ) && is_scalar( $a[ $k ] ) ? trim( (string) $a[ $k ] ) : '';
		};
		$rows = array();
		foreach ( (array) get_option( 'blockke_buyer_leads', array() ) as $l ) {
			if ( ! is_array( $l ) ) {
				continue;
			}
			$areas  = isset( $l['areas'] ) && is_array( $l['areas'] ) ? implode( ', ', $l['areas'] ) : '';
			$rows[] = array(
				'time'     => $s( $l, 'time' ),
				'name'     => $s( $l, 'name' ),
				'phone'    => preg_replace( '/\D+/', '', $s( $l, 'phone' ) ),
				'email'    => $s( $l, 'email' ),
				'interest' => '' !== $s( $l, 'unit' ) ? $s( $l, 'unit' ) : 'Free price check',
				'details'  => implode( '; ', array_filter( array( $s( $l, 'budget' ), $areas, ! empty( $l['alerts'] ) ? 'wants new-listing alerts' : '' ) ) ),
				'form'     => 'Price-check popup',
				'source'   => $s( $l, 'channel' ),
				'page'     => $s( $l, 'page' ),
				'mail'     => ! empty( $l['mail'] ),
				'zoho'     => $s( $l, 'zoho' ),
			);
		}
		foreach ( (array) get_option( 'blockke_dev_leads', array() ) as $l ) {
			if ( ! is_array( $l ) ) {
				continue;
			}
			$rows[] = array(
				'time'     => $s( $l, 'time' ),
				'name'     => $s( $l, 'name' ),
				'phone'    => preg_replace( '/\D+/', '', $s( $l, 'phone' ) ),
				'email'    => $s( $l, 'email' ),
				'interest' => $s( $l, 'dev' ) . ( '' !== $s( $l, 'unit' ) ? ' – ' . $s( $l, 'unit' ) : '' ),
				'details'  => implode( '; ', array_filter( array( '' !== $s( $l, 'purpose' ) ? 'Buying to ' . strtolower( $s( $l, 'purpose' ) ) : '', '' !== $s( $l, 'timeline' ) ? 'Timeline: ' . $s( $l, 'timeline' ) : '' ) ) ),
				'form'     => 'Development page' . ( '' !== $s( $l, 'form' ) ? ' (' . $s( $l, 'form' ) . ')' : '' ),
				'source'   => $s( $l, 'channel' ),
				'page'     => $s( $l, 'page' ),
				'mail'     => ! empty( $l['mail'] ),
				'zoho'     => $s( $l, 'zoho' ),
			);
		}
		usort(
			$rows,
			function ( $a, $b ) {
				return strcmp( $b['time'], $a['time'] );
			}
		);
		return $rows;
	}

	/** True when a plugin sends the site's email through an email service instead of the web server. */
	function blockke_inbox_has_mailer() {
		return defined( 'WPMS_PLUGIN_VER' ) || defined( 'FLUENTMAIL' ) || defined( 'POST_SMTP_VER' ) || class_exists( 'PostmanOptions' )
			|| defined( 'EASY_WP_SMTP_PLUGIN_VERSION' ) || class_exists( 'EasyWPSMTP' ) || defined( 'SENDINBLUE_WC_PLUGIN_VERSION' ) || class_exists( 'SIB_Manager' );
	}

	function blockke_inbox_phone_links( $digits ) {
		if ( '' === $digits ) {
			return '–';
		}
		return '<a href="' . esc_url( 'https://wa.me/' . $digits ) . '" target="_blank" rel="noopener">+' . esc_html( $digits ) . '</a>'
			. ' <a class="bki-call" href="' . esc_url( 'tel:+' . $digits ) . '">call</a>';
	}

	function blockke_inbox_when( $mysql ) {
		$ts = strtotime( $mysql );
		return $ts ? date_i18n( 'D j M, H:i', $ts ) : $mysql;
	}

	add_action(
		'admin_menu',
		function () {
			$seen  = (string) get_user_meta( get_current_user_id(), 'blockke_inbox_seen', true );
			$fresh = 0;
			foreach ( blockke_inbox_leads() as $r ) {
				if ( $r['time'] > $seen ) {
					++$fresh;
				}
			}
			$badge = $fresh && '' !== $seen ? ' <span class="awaiting-mod">' . (int) $fresh . '</span>' : '';
			add_menu_page( 'Enquiries', 'Enquiries' . $badge, 'manage_options', 'blockke-enquiries', 'blockke_inbox_page', 'dashicons-email-alt', 3 );
		}
	);

	function blockke_inbox_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		update_user_meta( get_current_user_id(), 'blockke_inbox_seen', current_time( 'mysql' ) );
		$leads = blockke_inbox_leads();
		$mails = (array) get_option( 'blockke_mail_log', array() );
		$zoho  = (array) get_option( 'blockke_zoho_log', array() );
		$to    = blockke_inbox_recipients();
		echo '<div class="wrap bki"><style>.bki table{margin-top:8px}.bki td{vertical-align:top}.bki .bki-call{color:#646970;font-size:12px}.bki .bki-muted{color:#646970}.bki .bki-bad{color:#b32d2e;font-weight:600}.bki .bki-ok{color:#00733b}.bki details pre{white-space:pre-wrap;max-width:720px;margin:8px 0 0;font:12px/1.5 monospace}.bki .bki-actions{display:flex;gap:8px;flex-wrap:wrap;align-items:center;margin:12px 0 4px}.bki .bki-actions form{margin:0}</style>';
		echo '<h1>Enquiries</h1>';
		$test = get_transient( 'blockke_inbox_test_' . get_current_user_id() );
		if ( is_array( $test ) ) {
			delete_transient( 'blockke_inbox_test_' . get_current_user_id() );
			if ( $test['ok'] ) {
				echo '<div class="notice notice-success"><p>WordPress handed a test email to the mail server for <b>' . esc_html( implode( ', ', $test['to'] ) ) . '</b>. Check those inboxes, and their spam folders, in a few minutes. If it doesn\'t arrive, the server isn\'t delivering the site\'s email.</p></div>';
			} else {
				echo '<div class="notice notice-error"><p>The test email could not be sent: ' . esc_html( $test['err'] ? $test['err'] : 'the mail server refused it.' ) . '</p></div>';
			}
		}
		if ( ! blockke_inbox_has_mailer() ) {
			echo '<div class="notice notice-warning"><p><b>Enquiry emails may not be arriving.</b> This site sends email straight from its web server, and many inboxes reject or hide those emails. Connect an email service with a plugin such as WP Mail SMTP (Gmail, Google Workspace, Zoho Mail or Brevo). Until then, every enquiry is kept on this page.</p></div>';
		}
		echo '<p>Every enquiry from the website, newest first. Enquiry emails go to <b>' . esc_html( implode( ', ', $to ) ) . '</b>; enquiries sent to an agent are copied to them too.</p>';
		echo '<div class="bki-actions"><form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '"><input type="hidden" name="action" value="blockke_inbox_test">' . wp_nonce_field( 'blockke_inbox_test', '_wpnonce', true, false ) . '<button class="button">Send a test email</button></form>'
			. '<a class="button" href="' . esc_url( wp_nonce_url( admin_url( 'admin-post.php?action=blockke_inbox_csv' ), 'blockke_inbox_csv' ) ) . '">Download all as CSV</a></div>';

		echo '<h2>Website enquiries (' . count( $leads ) . ')</h2>';
		if ( ! $leads ) {
			echo '<p class="bki-muted">No enquiries yet.</p>';
		} else {
			echo '<table class="widefat striped"><thead><tr><th>When</th><th>Name</th><th>Phone / WhatsApp</th><th>Email</th><th>Interested in</th><th>Details</th><th>Came from</th><th>Email sent</th><th>Zoho</th></tr></thead><tbody>';
			foreach ( $leads as $r ) {
				echo '<tr><td>' . esc_html( blockke_inbox_when( $r['time'] ) ) . '</td><td><b>' . esc_html( $r['name'] ) . '</b></td><td>' . blockke_inbox_phone_links( $r['phone'] ) . '</td>'
					. '<td>' . ( $r['email'] ? '<a href="' . esc_url( 'mailto:' . $r['email'] ) . '">' . esc_html( $r['email'] ) . '</a>' : '–' ) . '</td>'
					. '<td>' . ( $r['page'] ? '<a href="' . esc_url( $r['page'] ) . '" target="_blank" rel="noopener">' . esc_html( $r['interest'] ) . '</a>' : esc_html( $r['interest'] ) ) . '<br><span class="bki-muted">' . esc_html( $r['form'] ) . '</span></td>'
					. '<td>' . esc_html( $r['details'] ) . '</td><td>' . esc_html( $r['source'] ) . '</td>'
					. '<td>' . ( $r['mail'] ? 'Handed over' : '<span class="bki-bad">No</span>' ) . '</td><td class="bki-muted">' . esc_html( $r['zoho'] ) . '</td></tr>';
			}
			echo '</tbody></table>';
		}

		echo '<h2>Emails the site sent (' . count( $mails ) . ')</h2><p class="description">Every email WordPress tried to send, contact forms included. "Handed to the mail server" means WordPress passed it on; only the inbox can confirm it arrived.</p>';
		if ( ! $mails ) {
			echo '<p class="bki-muted">None yet. Emails are recorded from when this snippet was switched on.</p>';
		} else {
			echo '<table class="widefat striped"><thead><tr><th>When</th><th>To</th><th>Subject</th><th>Result</th></tr></thead><tbody>';
			foreach ( $mails as $m ) {
				if ( ! is_array( $m ) ) {
					continue;
				}
				$st     = isset( $m['status'] ) ? (string) $m['status'] : '';
				$result = 'sent' === $st ? '<span class="bki-ok">Handed to the mail server</span>' : ( 0 === strpos( $st, 'failed' ) ? '<span class="bki-bad">' . esc_html( ucfirst( $st ) ) . '</span>' : '<span class="bki-muted">No result reported</span>' );
				echo '<tr><td>' . esc_html( blockke_inbox_when( isset( $m['time'] ) ? $m['time'] : '' ) ) . '</td><td>' . esc_html( isset( $m['to'] ) ? $m['to'] : '' ) . ( ! empty( $m['copied'] ) ? '<br><span class="bki-muted">Copied to ' . esc_html( $m['copied'] ) . '</span>' : '' ) . '</td>'
					. '<td>' . esc_html( isset( $m['subject'] ) ? $m['subject'] : '' ) . ( ! empty( $m['body'] ) ? '<details><summary>Show the email</summary><pre>' . esc_html( $m['body'] ) . '</pre></details>' : ( ! empty( $m['private'] ) ? '<br><span class="bki-muted">Account email, text not stored</span>' : '' ) ) . '</td>'
					. '<td>' . $result . '</td></tr>';
			}
			echo '</tbody></table>';
		}

		echo '<h2>Zoho CRM replies (' . count( $zoho ) . ')</h2><p class="description">What Zoho answered when the website sent it a lead. A thank-you page or a redirect means the lead was accepted; an error message explains why it was not.</p>';
		if ( ! $zoho ) {
			echo '<p class="bki-muted">None yet. Replies are recorded from when this snippet was switched on.</p>';
		} else {
			echo '<table class="widefat striped"><thead><tr><th>When</th><th>Lead</th><th>Reply</th></tr></thead><tbody>';
			foreach ( $zoho as $z ) {
				if ( ! is_array( $z ) ) {
					continue;
				}
				echo '<tr><td>' . esc_html( blockke_inbox_when( isset( $z['time'] ) ? $z['time'] : '' ) ) . '</td><td>' . esc_html( isset( $z['name'] ) ? $z['name'] : '' ) . '</td><td>HTTP ' . (int) ( isset( $z['code'] ) ? $z['code'] : 0 )
					. ( ! empty( $z['location'] ) ? ', redirected to ' . esc_html( $z['location'] ) : '' ) . ( ! empty( $z['text'] ) ? '<br>' . esc_html( $z['text'] ) : '' )
					. ( ! empty( $z['html'] ) ? '<details><summary>Show the reply</summary><pre>' . esc_html( $z['html'] ) . '</pre></details>' : '' ) . '</td></tr>';
			}
			echo '</tbody></table>';
		}
		echo '</div>';
	}

	add_action(
		'wp_dashboard_setup',
		function () {
			if ( current_user_can( 'manage_options' ) ) {
				wp_add_dashboard_widget( 'blockke_inbox_widget', 'Latest enquiries', 'blockke_inbox_widget' );
			}
		}
	);

	function blockke_inbox_widget() {
		$leads = array_slice( blockke_inbox_leads(), 0, 8 );
		if ( ! $leads ) {
			echo '<p>No enquiries yet.</p>';
		} else {
			echo '<ul>';
			foreach ( $leads as $r ) {
				echo '<li><b>' . esc_html( $r['name'] ) . '</b> · ' . blockke_inbox_phone_links( $r['phone'] ) . '<br><span style="color:#646970">' . esc_html( blockke_inbox_when( $r['time'] ) . ' · ' . $r['interest'] ) . '</span></li>';
			}
			echo '</ul>';
		}
		echo '<p><a href="' . esc_url( admin_url( 'admin.php?page=blockke-enquiries' ) ) . '">All enquiries</a></p>';
	}

	/** Spreadsheet-safe cell: text starting with = + - @ would otherwise run as a formula. */
	function blockke_inbox_cell( $v ) {
		$v = (string) $v;
		return preg_match( '/^[=+\-@\t\r]/', $v ) ? "'" . $v : $v;
	}

	function blockke_inbox_csv_rows() {
		$rows = array( array( 'Date', 'Name', 'Phone', 'WhatsApp', 'Email', 'Interested in', 'Details', 'Form', 'Came from', 'Page', 'Email handed over', 'Zoho' ) );
		foreach ( blockke_inbox_leads() as $r ) {
			$rows[] = array(
				$r['time'],
				blockke_inbox_cell( $r['name'] ),
				$r['phone'] ? trim( chunk_split( $r['phone'], 3, ' ' ) ) : '',
				$r['phone'] ? 'https://wa.me/' . $r['phone'] : '',
				blockke_inbox_cell( $r['email'] ),
				blockke_inbox_cell( $r['interest'] ),
				blockke_inbox_cell( $r['details'] ),
				$r['form'],
				blockke_inbox_cell( $r['source'] ),
				$r['page'],
				$r['mail'] ? 'yes' : 'no',
				blockke_inbox_cell( $r['zoho'] ),
			);
		}
		return $rows;
	}

	add_action(
		'admin_post_blockke_inbox_csv',
		function () {
			if ( ! current_user_can( 'manage_options' ) ) {
				wp_die( 'Not allowed.' );
			}
			check_admin_referer( 'blockke_inbox_csv' );
			nocache_headers();
			header( 'Content-Type: text/csv; charset=UTF-8' );
			header( 'Content-Disposition: attachment; filename="block-enquiries-' . wp_date( 'Y-m-d' ) . '.csv"' );
			$out = fopen( 'php://output', 'w' );
			fwrite( $out, "\xEF\xBB\xBF" ); // so Excel reads it as UTF-8
			foreach ( blockke_inbox_csv_rows() as $row ) {
				fputcsv( $out, $row );
			}
			fclose( $out );
			exit;
		}
	);

	add_action(
		'admin_post_blockke_inbox_test',
		function () {
			if ( ! current_user_can( 'manage_options' ) ) {
				wp_die( 'Not allowed.' );
			}
			check_admin_referer( 'blockke_inbox_test' );
			$to    = blockke_inbox_recipients();
			$err   = '';
			$catch = function ( $e ) use ( &$err ) {
				$err = is_wp_error( $e ) ? $e->get_error_message() : 'unknown error';
			};
			add_action( 'wp_mail_failed', $catch );
			$ok = wp_mail( $to, '[Block] Test email from the website', "This is a test of the website's enquiry emails, sent " . wp_date( 'D j M Y, H:i' ) . ".\n\nIf you can read this, enquiries will reach this inbox. If it landed in spam, mark it as not spam.\n\nAll enquiries: " . admin_url( 'admin.php?page=blockke-enquiries' ) );
			remove_action( 'wp_mail_failed', $catch );
			set_transient( 'blockke_inbox_test_' . get_current_user_id(), array( 'ok' => (bool) $ok, 'to' => $to, 'err' => $err ), 300 );
			wp_safe_redirect( admin_url( 'admin.php?page=blockke-enquiries' ) );
			exit;
		}
	);
}
