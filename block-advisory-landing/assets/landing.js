/* =============================================================================
   Block Advisory: new development landing page
   Builds the whole page from two objects:
     window.BLOCK_AGENCY  (assets/agency.js, shared)
     window.DEVELOPMENT   (in each development's index.html)
   Any section without data is left out, so the same template fits every project.
   ========================================================================== */
(function () {
  'use strict';

  var A = window.BLOCK_AGENCY || {};
  var D = window.DEVELOPMENT;
  var app = document.getElementById('app');
  if (!D || !app) return;

  /* ---------- Helpers ---------- */
  var $ = function (s, r) { return (r || document).querySelector(s); };
  var $$ = function (s, r) { return Array.prototype.slice.call((r || document).querySelectorAll(s)); };
  var dl = (window.dataLayer = window.dataLayer || []);
  var reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  var esc = function (s) { return String(s == null ? '' : s).replace(/[&<>"']/g, function (c) { return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]; }); };
  var CUR = D.currency || 'KES';
  var money = function (n) { return n ? CUR + ' ' + Math.round(n).toLocaleString('en-KE') : 'On request'; };
  var short = function (n) {
    if (!n) return 'On request';
    if (n >= 1e6) return CUR + ' ' + (+(n / 1e6).toFixed(2)) + 'M';
    return CUR + ' ' + Math.round(n / 1e3) + 'K';
  };
  var digits = function (s) { return String(s || '').replace(/\D/g, ''); };
  var uidN = 0;
  var uid = function (p) { uidN += 1; return 'f-' + p + '-' + uidN; };
  var pic = function (x) { return typeof x === 'string' ? { src: x } : x; };
  var has = function (a) { return Array.isArray(a) && a.length > 0; };
  var waNumber = A.whatsapp || digits(A.phone);
  var waLink = function (t) { return 'https://wa.me/' + waNumber + '?text=' + encodeURIComponent(t); };
  var WA_DEFAULT = 'Hi ' + (A.name || 'Block Advisory') + ", I'm interested in " + D.name + '. Please share the price list and floor plans.';

  /* ---------- Icons ---------- */
  var ICONS = {
    arrow: '<path d="M5 12h14M13 6l6 6-6 6"/>',
    check: '<path d="M20 6 9 17l-5-5"/>',
    close: '<path d="M6 6l12 12M18 6 6 18"/>',
    phone: '<path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.8 19.8 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.12 4.18 2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.13.96.36 1.9.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.91.34 1.85.57 2.81.7A2 2 0 0 1 22 16.92z"/>',
    mail: '<rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-10 6L2 7"/>',
    pin: '<path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/>',
    grid: '<rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/>',
    waves: '<path d="M2 7c.6.5 1.2 1 2.5 1 2.5 0 2.5-2 5-2s2.4 2 5 2c2.5 0 2.5-2 5-2 1.3 0 1.9.5 2.5 1M2 13c.6.5 1.2 1 2.5 1 2.5 0 2.5-2 5-2s2.4 2 5 2c2.5 0 2.5-2 5-2 1.3 0 1.9.5 2.5 1M2 19c.6.5 1.2 1 2.5 1 2.5 0 2.5-2 5-2s2.4 2 5 2c2.5 0 2.5-2 5-2 1.3 0 1.9.5 2.5 1"/>',
    users: '<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>',
    book: '<path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2zM22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/>',
    shield: '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10Z"/><path d="m9 12 2 2 4-4"/>',
    leaf: '<path d="M11 20A7 7 0 0 1 9.8 6.1C15.5 5 17 4.48 19 2c1 2 2 4.18 2 8 0 5.5-4.78 10-10 10Z"/><path d="M2 21c0-3 1.85-5.36 5.08-6C9.5 14.52 12 13 13 12"/>',
    lotus: '<path d="M12 21c-4.5 0-9-3-9-8 3.5 0 6.5 1.6 9 4.5 2.5-2.9 5.5-4.5 9-4.5 0 5-4.5 8-9 8Z"/><path d="M12 17.5c-2-2.2-2.6-5.6 0-11 2.6 5.4 2 8.8 0 11Z"/>',
    dumbbell: '<path d="M6.5 6.5v11M17.5 6.5v11M3.5 9v6M20.5 9v6M6.5 12h11"/>',
    film: '<rect x="2" y="4" width="20" height="16" rx="2"/><path d="M7 4v16M17 4v16M2 9h5M2 15h5M17 9h5M17 15h5"/>',
    laptop: '<rect x="4" y="5" width="16" height="11" rx="1.5"/><path d="M2 19h20"/>',
    ball: '<circle cx="12" cy="12" r="9"/><path d="M12 3c3 3 3 15 0 18M3.5 9.5c5 1.5 12 1.5 17 0M3.5 14.5c5-1.5 12-1.5 17 0"/>',
    sun: '<circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41"/>',
    car: '<path d="M19 17h2c.6 0 1-.4 1-1v-3c0-.9-.7-1.7-1.5-1.9C18.7 10.6 16 10 16 10s-1.3-1.4-2.2-2.3c-.5-.4-1.1-.7-1.8-.7H5c-.6 0-1.1.4-1.4.9l-1.4 2.9A3.7 3.7 0 0 0 2 12v4c0 .6.4 1 1 1h2"/><circle cx="7" cy="17" r="2"/><path d="M9 17h6"/><circle cx="17" cy="17" r="2"/>',
    building: '<rect x="4" y="2" width="16" height="20" rx="1"/><path d="M9 22v-4h6v4M8 6h.01M12 6h.01M16 6h.01M8 10h.01M12 10h.01M16 10h.01M8 14h.01M12 14h.01M16 14h.01"/>',
    key: '<circle cx="7.5" cy="15.5" r="5.5"/><path d="m21 2-9.6 9.6M15.5 7.5l3 3L22 7l-3-3"/>',
    calendar: '<rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/>',
    sofa: '<path d="M20 9V7a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v2"/><path d="M2 11a2 2 0 0 1 4 0v2h12v-2a2 2 0 0 1 4 0v6a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1Z"/><path d="M5 18v2M19 18v2"/>',
    star: '<path d="m12 2 3.1 6.3 6.9 1-5 4.9 1.2 6.8L12 17.8 5.8 21l1.2-6.8-5-4.9 6.9-1z"/>'
  };
  var WA_PATH = '<path d="M17.47 14.38c-.3-.15-1.76-.87-2.03-.97-.27-.1-.47-.15-.67.15-.2.3-.77.97-.94 1.16-.17.2-.35.22-.64.07-.3-.15-1.26-.46-2.39-1.47-.88-.79-1.48-1.76-1.65-2.06-.17-.3-.02-.46.13-.6.13-.14.3-.35.45-.52.15-.18.2-.3.3-.5.1-.2.05-.37-.03-.52-.07-.15-.67-1.61-.91-2.2-.24-.58-.49-.5-.67-.51h-.57c-.2 0-.52.07-.79.37-.27.3-1.04 1.02-1.04 2.48s1.07 2.88 1.21 3.07c.15.2 2.1 3.2 5.08 4.49.71.3 1.26.49 1.7.63.71.22 1.36.19 1.87.12.57-.09 1.76-.72 2-1.41.25-.7.25-1.29.18-1.41-.08-.13-.27-.2-.57-.35m-5.42 7.4h-.01a9.87 9.87 0 0 1-5.03-1.38l-.36-.21-3.74.98 1-3.65-.24-.37a9.86 9.86 0 0 1-1.51-5.26c0-5.45 4.44-9.88 9.89-9.88a9.82 9.82 0 0 1 9.88 9.89c0 5.45-4.44 9.88-9.88 9.88m8.41-18.3A11.82 11.82 0 0 0 12.05 0C5.5 0 .16 5.34.16 11.89c0 2.1.55 4.14 1.59 5.95L.06 24l6.3-1.65a11.88 11.88 0 0 0 5.68 1.45h.01c6.55 0 11.89-5.34 11.89-11.89a11.82 11.82 0 0 0-3.48-8.41Z"/>';
  var sprite = '<svg width="0" height="0" style="position:absolute" aria-hidden="true">' +
    Object.keys(ICONS).map(function (k) { return '<symbol id="i-' + k + '" viewBox="0 0 24 24">' + ICONS[k] + '</symbol>'; }).join('') +
    '<symbol id="i-wa" viewBox="0 0 24 24">' + WA_PATH + '</symbol></svg>';
  var icon = function (n, cls) { return '<svg class="i ' + (n === 'wa' ? 'i-f ' : '') + (cls || '') + '" aria-hidden="true"><use href="#i-' + (ICONS[n] || n === 'wa' ? n : 'check') + '"/></svg>'; };

  /* ---------- Data ---------- */
  var images = (D.images || []).map(pic).filter(function (x) { return x && x.src; });
  var hero = D.heroImage ? pic(D.heroImage) : images[0];
  var gallery = hero && !images.some(function (x) { return x.src === hero.src; }) ? [hero].concat(images) : images;
  var units = D.units || [];
  var groups = [];
  units.forEach(function (u) { var g = u.group || u.type; if (groups.indexOf(g) === -1) groups.push(g); });
  var plan = D.paymentPlan || null;
  // Instalment months: a fixed number (months: 30) or counted from today to completion (until: '2029-12')
  var monthsUntil = function (ym) {
    var m = /^(\d{4})-(\d{1,2})$/.exec(String(ym || '')); if (!m) return 0;
    var now = new Date(), n = (+m[1]) * 12 + (+m[2]) - (now.getFullYear() * 12 + now.getMonth() + 1);
    return n > 0 ? n : 0;
  };
  var planMonths = plan ? (plan.months || monthsUntil(plan.until)) : 0;
  var loc = D.location || null;

  var phImg = function (label) { return '<div class="ph"><div><b>' + esc(label || D.name) + '</b><span>Images on request</span></div></div>'; };
  var imgTag = function (p, eager, label) {
    return '<img src="' + esc(p.src) + '" alt="' + esc(p.alt || '') + '"' + (eager ? ' fetchpriority="high"' : ' loading="lazy"') + ' decoding="async" data-ph="' + esc(label || D.name) + '">';
  };

  /* ---------- Form builders ---------- */
  var field = function (name, label, type, ac, ph, optional) {
    return '<label class="f" data-f="' + name + '"><span>' + label + (optional ? ' <small>(optional)</small>' : '') + '</span>' +
      '<input name="' + name + '" type="' + type + '" autocomplete="' + ac + '"' + (ph ? ' placeholder="' + ph + '"' : '') + (type === 'tel' ? ' inputmode="tel"' : '') + '>' +
      '<em class="err" aria-live="polite"></em></label>';
  };
  var chips = function (name, legend, options, checked) {
    return '<fieldset class="f"><legend>' + legend + '</legend><div class="chips">' + options.map(function (o) {
      return '<label class="chip"><input type="radio" name="' + name + '" value="' + esc(o) + '"' + (o === checked ? ' checked' : '') + '><span>' + esc(o) + '</span></label>';
    }).join('') + '</div></fieldset>';
  };
  var honeypot = '<input class="hp" type="text" name="company" tabindex="-1" autocomplete="off" aria-hidden="true">';
  var consent = '<p class="consent">We\'ll reply by WhatsApp or phone. By sending this you agree to be contacted about ' + esc(D.name) + '.</p>';
  var okBlock = function () {
    return '<div class="ok" hidden tabindex="-1"><div class="ok-tick">' + icon('check') + '</div><h3>Thank you<span data-first></span>.</h3>' +
      '<p data-okmsg></p><div class="ok-actions"><a class="btn btn-navy" data-asset hidden target="_blank" rel="noopener"></a>' +
      '<a class="btn btn-line" data-wa-ok href="#" target="_blank" rel="noopener">' + icon('wa', 'wa-ic') + 'Continue on WhatsApp</a></div></div>';
  };
  var quickForm = function (source, cta) {
    return '<form class="lead-form" data-lead="' + source + '" novalidate>' + honeypot +
      '<div class="qgrid">' + field('name', 'Full name', 'text', 'name') + field('phone', 'Phone / WhatsApp', 'tel', 'tel', '07XX XXX XXX') + '</div>' +
      '<button class="btn btn-navy btn-block" type="submit">' + cta + icon('arrow') + '</button>' + consent + '</form>' + okBlock();
  };
  var interestOptions = groups.length ? groups.concat(['Not sure yet']) : [];
  var fullForm = function () {
    return '<form class="lead-form" data-lead="contact" novalidate>' + honeypot +
      '<div class="two">' + field('name', 'Full name', 'text', 'name') + field('phone', 'Phone / WhatsApp', 'tel', 'tel', '07XX XXX XXX') + '</div>' +
      field('email', 'Email', 'email', 'email', '', true) +
      (interestOptions.length ? chips('unit', 'Interested in', interestOptions, 'Not sure yet') : '') +
      chips('purpose', 'Buying to', ['Live in', 'Invest', 'Both'], 'Live in') +
      chips('contact', 'Best way to reach you', ['WhatsApp', 'Phone call', 'Email'], 'WhatsApp') +
      '<label class="f"><span>Message <small>(optional)</small></span><textarea name="message" placeholder="Anything we should know: budget, timing, questions."></textarea></label>' +
      '<button class="btn btn-navy btn-block" type="submit">Send my enquiry' + icon('arrow') + '</button>' + consent + '</form>' + okBlock();
  };

  /* ---------- Sections ---------- */
  var navItems = [];
  var addNav = function (id, label) { navItems.push('<a href="#' + id + '">' + label + '</a>'); };
  if (D.overview) addNav('overview', 'Overview');
  if (has(units)) addNav('residences', 'Residences');
  if (has(D.amenities)) addNav('amenities', 'Amenities');
  if (plan) addNav('payment', 'Payment plan');
  if (loc) addNav('location', 'Location');
  if (has(D.faqs)) addNav('faq', 'FAQ');

  var logo = function (light) {
    var src = light ? (A.logoLight || A.logo) : A.logo;
    var text = '<span class="logo-t" ' + (src ? 'hidden' : '') + '>BLOCK<small>ADVISORY</small></span>';
    return '<a class="logo" href="' + esc(A.site || '#') + '" aria-label="' + esc(A.name || 'Block Advisory') + '">' +
      (src ? '<img src="' + esc(src) + '" alt="' + esc(A.name || 'Block Advisory') + '" data-logo>' : '') + text + '</a>';
  };

  var html = [];
  html.push(sprite);

  // Header
  html.push('<header class="hd" id="hd"><div class="wrap hd-in">' + logo(false) +
    '<nav class="hd-nav" aria-label="On this page">' + navItems.join('') + '</nav>' +
    '<div class="hd-r">' + (A.phone ? '<a class="hd-tel" href="tel:' + esc(A.phone) + '" data-call>' + icon('phone') + esc(A.phoneDisplay || A.phone) + '</a>' : '') +
    '<button class="btn btn-navy btn-sm" type="button" data-enquire="header">Get price list</button></div></div></header>');

  // Hero
  var hfacts = [];
  hfacts.push(D.priceFrom ? ['From', short(D.priceFrom)] : ['Price', 'On request']);
  if (D.completion) hfacts.push(['Completion', D.completion]);
  if (plan && plan.short) hfacts.push(['Payment', plan.short]);
  else if (D.area) hfacts.push(['Location', D.area.split(',')[0]]);
  html.push('<main id="main"><section class="hero" id="top"><div class="wrap hero-grid">' +
    '<div class="hero-head">' +
      '<p class="status">' + (D.status ? '<b>' + esc(D.status) + '</b>' : '') + '<span>' + esc(D.area || '') + '</span></p>' +
      '<h1>' + esc(D.name) + '</h1>' +
      (D.tagline ? '<p class="hero-tag">' + esc(D.tagline) + '</p>' : '') +
    '</div>' +
    '<figure class="hero-media">' + (hero ? imgTag(hero, true) + (hero.caption !== false ? '<figcaption>' + esc(hero.caption || "Artist's impression") + '</figcaption>' : '') : phImg()) +
      (gallery.length > 1 ? '<button class="btn btn-white btn-sm all" type="button" data-open="0">' + icon('grid') + 'View ' + gallery.length + ' photos</button>' : '') +
    '</figure>' +
    '<div class="hero-body">' +
      (hfacts.length ? '<dl class="hfacts">' + hfacts.map(function (f) { return '<div><dt>' + esc(f[0]) + '</dt><dd>' + esc(f[1]) + '</dd></div>'; }).join('') + '</dl>' : '') +
      '<div class="quick" id="quick"><p class="quick-t">' + esc(D.formTitle || 'Get the price list and floor plans') + '</p>' +
      '<p class="quick-s">' + esc(D.formSubtitle || 'Sent straight to your WhatsApp. Two details, no obligation.') + '</p>' +
      quickForm('hero', 'Send me the price list') +
      (D.brochure ? '<p class="quick-alt"><button class="link" type="button" data-enquire="brochure">Or download the brochure' + icon('arrow') + '</button></p>' : '') + '</div>' +
    '</div></div></section>');

  // Key facts band
  if (has(D.facts)) {
    html.push('<section class="facts" aria-label="Key facts"><div class="wrap"><dl style="--n:' + Math.min(D.facts.length, 6) + '">' +
      D.facts.map(function (f) { return '<div><dt>' + esc(f.label) + '</dt><dd>' + esc(f.value) + '</dd></div>'; }).join('') + '</dl></div></section>');
  }

  // Overview
  if (D.overview) {
    var o = D.overview;
    html.push('<section class="sec" id="overview"><div class="wrap split">' +
      '<div class="sticky rv"><p class="label">Overview</p><h2>' + esc(o.heading || D.name) + '</h2></div>' +
      '<div class="flow rv">' + (o.lead ? '<p class="lead">' + esc(o.lead) + '</p>' : '') +
        (o.body || []).map(function (p) { return '<p class="body">' + esc(p) + '</p>'; }).join('') +
        (has(o.highlights) ? '<ul class="hl">' + o.highlights.map(function (h) { return '<li>' + icon('check') + '<span>' + esc(h) + '</span></li>'; }).join('') + '</ul>' : '') +
      '</div></div></section>');
  }

  // Gallery
  if (gallery.length >= 3) {
    html.push('<section class="sec" id="gallery" style="padding-top:0"><div class="wrap">' +
      '<div class="gal rv">' + gallery.slice(0, 5).map(function (p, i) { return '<button type="button" data-open="' + i + '" aria-label="Open photo ' + (i + 1) + '">' + imgTag(p) + '</button>'; }).join('') + '</div>' +
      '<div class="sh-row" style="margin-top:22px"><p class="fine">' + esc(D.imageNote || "Images are artist's impressions.") + '</p>' +
      '<button class="link" type="button" data-open="0">' + icon('grid') + 'View all ' + gallery.length + ' photos</button></div>' +
      '</div></section>');
  }

  // Video (optional): D.video = { youtube: 'VIDEO_ID', caption: '...' }
  if (D.video && D.video.youtube) {
    var vid = encodeURIComponent(D.video.youtube);
    html.push('<section class="sec" id="video"' + (gallery.length >= 3 || D.overview ? ' style="padding-top:0"' : '') + '><div class="wrap">' +
      '<div class="video rv" data-video="' + vid + '"><button type="button" aria-label="Play video: ' + esc(D.video.caption || D.name) + '">' +
      '<img loading="lazy" alt="" src="https://i.ytimg.com/vi/' + vid + '/hqdefault.jpg"><span class="play"><svg viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg></span></button></div>' +
      (D.video.caption ? '<p class="fine" style="margin-top:16px">' + esc(D.video.caption) + '</p>' : '') + '</div></section>');
  }

  // Residences
  if (has(units)) {
    var filt = groups.length > 1 && units.length > 4
      ? '<div class="filters" role="group" aria-label="Filter residences"><button type="button" aria-pressed="true" data-g="*">All</button>' +
        groups.map(function (g) { return '<button type="button" aria-pressed="false" data-g="' + esc(g) + '">' + esc(g) + '</button>'; }).join('') + '</div>'
      : '';
    var hasSize = units.some(function (u) { return u.size; }), hasPrice = units.some(function (u) { return u.price; });
    html.push('<section class="sec sec-mist" id="residences"><div class="wrap">' +
      '<div class="sh sh-row rv"><div><p class="label">Residences</p><h2>' + esc(D.unitsHeading || 'Availability and pricing') + '</h2></div>' + filt + '</div>' +
      '<table class="tbl rv"><thead><tr><th>Residence</th>' + (hasSize ? '<th>Size</th>' : '') + (hasPrice ? '<th>Price from</th>' : '') + '<th><span class="sr">Actions</span></th></tr></thead><tbody>' +
      units.map(function (u) {
        var label = u.type + (u.size ? ' · ' + u.size : '');
        return '<tr data-g="' + esc(u.group || u.type) + '"><td class="t">' + esc(u.type) + (u.note ? '<small>' + esc(u.note) + '</small>' : '') + '</td>' +
          (hasSize ? '<td class="s">' + esc(u.size || '—') + '</td>' : '') + (hasPrice ? '<td class="p">' + esc(u.price ? money(u.price) : 'On request') + '</td>' : '') +
          '<td class="a">' + (u.url ? '<a class="link" href="' + esc(u.url) + '">Details</a>' : '') +
          '<button class="link" type="button" data-enquire="floorplan" data-unit="' + esc(label) + '"' + (u.plan ? ' data-asset="' + esc(u.plan) + '"' : '') + '>Floor plan &amp; price' + icon('arrow') + '</button></td></tr>';
      }).join('') + '</tbody></table>' +
      '<p class="fine tbl-note">' + esc(D.unitsNote || 'Starting prices. Ask for the latest price list for current availability.') + '</p>' +
      '</div></section>');
  }

  // Amenities
  if (has(D.amenities)) {
    html.push('<section class="sec" id="amenities"><div class="wrap">' +
      '<div class="sh rv"><p class="label">Amenities</p><h2>' + esc(D.amenitiesHeading || 'Everything close to home') + '</h2>' + (D.amenitiesIntro ? '<p class="body">' + esc(D.amenitiesIntro) + '</p>' : '') + '</div>' +
      '<div class="am">' + D.amenities.map(function (a) {
        return '<div class="am-i rv"><div class="am-ic">' + icon(a.icon || 'check') + '</div><h3>' + esc(a.title) + '</h3>' +
          (has(a.items) ? '<ul>' + a.items.map(function (x) { return '<li>' + esc(x) + '</li>'; }).join('') + '</ul>' : '') +
          (a.text ? '<p>' + esc(a.text) + '</p>' : '') + '</div>';
      }).join('') + '</div></div></section>');
  }

  // Payment plan
  if (plan) {
    var right;
    if (planMonths) {
      right = '<div class="panel rv"><p class="panel-t">Estimate your instalments</p><p class="fine" style="margin:0">Illustrative only. Ask for the official schedule.</p>' +
        '<div class="calc-row">' +
          '<label class="f"><span>Price (' + esc(CUR) + ')</span><input id="cp-price" inputmode="numeric" autocomplete="off" value="' + (D.priceFrom ? Math.round(D.priceFrom).toLocaleString('en-KE') : '') + '"></label>' +
          '<label class="f"><span>Deposit <output class="range-out" id="cp-dep-out">' + (plan.depositPct || 10) + '%</output></span><input id="cp-dep" type="range" min="0" max="50" step="5" value="' + (plan.depositPct || 10) + '"></label>' +
        '</div>' +
        '<div class="calc-out" aria-live="polite"><div class="main"><span>Monthly, over ' + planMonths + ' months' + (plan.until && D.completion ? ' to ' + esc(D.completion) : '') + '</span><strong id="cp-month">—</strong></div>' +
          '<div><span>Deposit</span><strong id="cp-deposit">—</strong></div><div><span>Balance</span><strong id="cp-balance">—</strong></div></div>' +
        '<p class="fine">Assumes equal monthly instalments after the deposit' + (plan.reservation ? ', with the ' + esc(money(plan.reservation)) + ' reservation counted towards it' : '') + '. The developer\'s schedule may differ.</p>' +
        '<button class="btn btn-navy btn-block" type="button" data-enquire="payment-plan">Get the official payment plan</button></div>';
    } else {
      right = '<div class="panel rv" id="plan-form"><p class="panel-t">Get the payment schedule</p><p class="quick-s">We\'ll send the current schedule with the price list.</p>' +
        quickForm('payment-plan', 'Send me the schedule') + '</div>';
    }
    html.push('<section class="sec sec-mist" id="payment"><div class="wrap split">' +
      '<div class="rv"><p class="label">Payment plan</p><h2>' + esc(plan.heading || 'Payment plan') + '</h2>' +
        (plan.text ? '<p class="body" style="margin-top:22px;max-width:520px">' + esc(plan.text) + '</p>' : '') +
        (has(plan.steps) ? '<ol class="steps">' + plan.steps.map(function (s) { return '<li><h3>' + esc(s.title) + '</h3><p>' + esc(s.text || '') + '</p></li>'; }).join('') + '</ol>' : '') +
      '</div>' + right + '</div></section>');
  }

  // Location
  if (loc) {
    var q = loc.lat && loc.lng ? loc.lat + ',' + loc.lng : (loc.query || D.address || D.name);
    html.push('<section class="sec" id="location"><div class="wrap split">' +
      '<div class="rv"><p class="label">Location</p><h2>' + esc(loc.heading || D.address || D.area) + '</h2>' +
        (loc.intro ? '<p class="body" style="margin-top:22px;max-width:520px">' + esc(loc.intro) + '</p>' : '') +
        (has(loc.places) ? '<ul class="places">' + loc.places.map(function (p) { return '<li><b style="font-weight:500">' + esc(p.name) + '</b><span>' + esc(p.time || '') + '</span></li>'; }).join('') + '</ul>' : '') +
        (loc.note ? '<p class="fine" style="margin-top:16px">' + esc(loc.note) + '</p>' : '') +
      '</div>' +
      '<div class="map rv"><iframe title="Map of ' + esc(D.name) + '" loading="lazy" referrerpolicy="no-referrer-when-downgrade" src="https://maps.google.com/maps?q=' + encodeURIComponent(q) + '&z=15&output=embed"></iframe>' +
        '<a class="link" target="_blank" rel="noopener" href="https://www.google.com/maps/search/?api=1&query=' + encodeURIComponent(q) + '">' + icon('pin') + 'Open in Google Maps</a></div>' +
      '</div></section>');
  }

  // Why Block Advisory
  if (has(A.why)) {
    html.push('<section class="sec" id="why"' + (loc ? ' style="padding-top:0"' : '') + '><div class="wrap">' +
      '<div class="sh rv"><p class="label">Why ' + esc(A.name || 'Block Advisory') + '</p><h2>Advice first. Then a decision.</h2></div>' +
      '<div class="why">' + A.why.map(function (w, i) { return '<div class="rv"><b>0' + (i + 1) + '</b><h3>' + esc(w.title) + '</h3><p>' + esc(w.text) + '</p></div>'; }).join('') + '</div>' +
      '</div></section>');
  }

  // Similar developments
  if (has(D.similar)) {
    html.push('<section class="sec sec-mist" id="similar"><div class="wrap">' +
      '<div class="sh sh-row rv"><div><p class="label">Also consider</p><h2>' + esc(D.similarHeading || 'Similar developments') + '</h2></div>' +
        '<a class="link" href="' + esc(A.site || '#') + '">See all developments' + icon('arrow') + '</a></div>' +
      '<div class="cards" style="--n:' + Math.min(D.similar.length, 4) + '">' + D.similar.map(function (s) {
        return '<a class="card rv" href="' + esc(s.url || '#') + '"><div class="card-img">' + (s.image ? imgTag(pic(s.image), false, s.name) : phImg(s.name)) + '</div>' +
          '<h3>' + esc(s.name) + '</h3><b>' + esc(s.price || '') + '</b><p>' + esc(s.meta || '') + '</p></a>';
      }).join('') + '</div></div></section>');
  }

  // FAQ
  if (has(D.faqs)) {
    html.push('<section class="sec" id="faq"><div class="wrap split">' +
      '<div class="sticky rv"><p class="label">FAQ</p><h2>Questions buyers ask</h2>' +
        '<p class="body" style="margin-top:20px;max-width:380px">Can\'t see yours? <a href="#" data-wa style="color:var(--navy);font-weight:600">Ask us on WhatsApp</a>.</p></div>' +
      '<div class="faq rv">' + D.faqs.map(function (f) { return '<details><summary>' + esc(f.q) + '</summary><p>' + esc(f.a) + '</p></details>'; }).join('') + '</div>' +
      '</div></section>');
  }

  // Contact
  var ag = A.agent || {};
  html.push('<section class="sec sec-mist" id="enquire"><div class="wrap split">' +
    '<div class="sticky rv"><p class="label">Enquire</p><h2>' + esc(D.contactHeading || 'Speak to an advisor about ' + D.name) + '</h2>' +
      '<p class="body" style="margin-top:22px;max-width:460px">Get the price list, floor plans and payment plan, or book a viewing. We usually reply within a few hours on working days.</p>' +
      (ag.name ? '<div class="agent">' + (ag.photo ? '<img src="' + esc(ag.photo) + '" alt="" loading="lazy" data-av>' : '') + '<div class="av"' + (ag.photo ? ' hidden' : '') + '>' + esc(ag.name.charAt(0)) + '</div>' +
        '<div><b>' + esc(ag.name) + '</b><span>' + esc(ag.role || '') + (A.name ? ', ' + esc(A.name) : '') + '</span></div></div>' : '') +
      '<div class="direct">' +
        (A.phone ? '<a href="tel:' + esc(A.phone) + '" data-call>' + icon('phone') + esc(A.phoneDisplay || A.phone) + '</a>' : '') +
        (waNumber ? '<a href="#" data-wa>' + icon('wa', 'wa-ic') + 'WhatsApp</a>' : '') +
        (A.email ? '<a href="mailto:' + esc(A.email) + '?subject=' + encodeURIComponent(D.name + ' enquiry') + '" data-email>' + icon('mail') + esc(A.email) + '</a>' : '') +
      '</div></div>' +
    '<div class="formcard rv">' + fullForm() + '</div>' +
    '</div></section></main>');

  // Footer
  html.push('<footer class="ft"><div class="wrap"><div class="ft-top">' +
    '<div>' + logo(true) + '<p>' + esc(D.name) + (D.address ? ', ' + esc(D.address) : '') + (D.developer ? '. Developed by ' + esc(D.developer) + '.' : '.') + '</p></div>' +
    '<div><h4>Contact</h4><div class="ft-links">' + (A.phone ? '<a href="tel:' + esc(A.phone) + '" data-call>' + esc(A.phoneDisplay || A.phone) + '</a>' : '') +
      (waNumber ? '<a href="#" data-wa>WhatsApp</a>' : '') + (A.email ? '<a href="mailto:' + esc(A.email) + '">' + esc(A.email) + '</a>' : '') + '</div></div>' +
    '<div><h4>Office</h4><div class="ft-links"><span>' + esc(A.address || '') + '</span><a href="' + esc(A.site || '#') + '">' + esc((A.site || '').replace(/^https?:\/\//, '')) + '</a></div></div>' +
    '</div><p class="disc">' + esc(A.disclaimer || '') + '</p><p class="disc" style="margin-top:8px">© ' + new Date().getFullYear() + ' ' + esc(A.name || '') + '</p></div></footer>');

  // Sticky actions, modal, lightbox
  html.push('<nav class="mb" id="mb" aria-label="Quick contact"><a class="wa" href="#" data-wa aria-label="WhatsApp">' + icon('wa', 'wa-ic') + '</a>' +
    '<button class="btn btn-navy" type="button" data-enquire="mobile-bar">Get the price list</button></nav>');
  if (waNumber) html.push('<a class="waf" href="#" data-wa aria-label="Chat on WhatsApp">' + icon('wa') + '</a>');
  html.push('<div class="md" id="md" hidden role="dialog" aria-modal="true" aria-labelledby="md-t"><div class="md-box">' +
    '<button class="md-x" type="button" data-close aria-label="Close">' + icon('close') + '</button>' +
    '<p class="label">' + esc(D.name) + '</p><h2 id="md-t">Get the price list</h2><p class="md-sub" id="md-sub">Floor plans and the payment plan, sent to your WhatsApp.</p>' +
    '<div id="md-form">' + quickForm('modal', 'Send it to me') + '</div></div></div>');
  html.push('<div class="lb" id="lb" hidden role="dialog" aria-modal="true" aria-label="Photo gallery"><div class="lb-top"><span id="lb-n"></span>' +
    '<button class="lb-x" type="button" data-lbclose aria-label="Close gallery">' + icon('close') + '</button></div>' +
    '<div class="lb-stage"><button class="lb-prev" type="button" aria-label="Previous photo">‹</button><img id="lb-img" alt=""><button class="lb-next" type="button" aria-label="Next photo">›</button></div>' +
    '<div class="lb-strip" id="lb-strip"></div></div>');

  app.innerHTML = html.join('');

  /* ---------- After render ---------- */
  if (!document.title) document.title = D.name + ' | ' + (A.name || '');
  $$('img[data-ph]').forEach(function (im) {
    im.addEventListener('error', function () { var box = im.parentNode; if (box) { im.remove(); box.insertAdjacentHTML('afterbegin', phImg(im.dataset.ph)); } }, { once: true });
  });
  $$('img[data-logo]').forEach(function (im) {
    im.addEventListener('error', function () { var t = im.parentNode.querySelector('.logo-t'); im.remove(); if (t) t.hidden = false; }, { once: true });
  });
  var av = $('img[data-av]');
  if (av) av.addEventListener('error', function () { av.remove(); var f = $('.agent .av'); if (f) f.hidden = false; }, { once: true });

  // Structured data
  var ld = { '@context': 'https://schema.org', '@type': 'ApartmentComplex', name: D.name, description: D.tagline || '', address: { '@type': 'PostalAddress', streetAddress: D.address || '', addressCountry: 'KE' } };
  if (hero) ld.image = hero.src;
  if (D.unitsCount) ld.numberOfAccommodationUnits = D.unitsCount;
  var s = document.createElement('script'); s.type = 'application/ld+json'; s.textContent = JSON.stringify(ld); document.head.appendChild(s);

  // Contact links
  $$('[data-wa]').forEach(function (a) { a.href = waLink(WA_DEFAULT); a.target = '_blank'; a.rel = 'noopener'; a.addEventListener('click', function () { dl.push({ event: 'click_whatsapp', development: D.slug }); }); });
  $$('[data-call]').forEach(function (a) { a.addEventListener('click', function () { dl.push({ event: 'click_call', development: D.slug }); }); });

  /* ---------- Attribution ---------- */
  var KEYS = ['utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content', 'utm_adid', 'gclid', 'gbraid', 'wbraid', 'gad_campaignid', 'fbclid'];
  var ATTR = {};
  try {
    var qs = new URLSearchParams(location.search);
    KEYS.forEach(function (k) { if (qs.get(k)) ATTR[k] = qs.get(k); });
    if (Object.keys(ATTR).length) { ATTR.landing_page = location.origin + location.pathname; localStorage.setItem('ba_attr', JSON.stringify(ATTR)); }
    else ATTR = JSON.parse(localStorage.getItem('ba_attr') || '{}');
  } catch (e) { /* storage unavailable */ }

  /* ---------- Lead forms ---------- */
  var remembered = {};
  try { remembered = JSON.parse(sessionStorage.getItem('ba_lead') || '{}'); } catch (e) { remembered = {}; }
  var prefill = function (form) {
    ['name', 'phone', 'email'].forEach(function (k) { var i = form.querySelector('[name="' + k + '"]'); if (i && !i.value && remembered[k]) i.value = remembered[k]; });
  };
  var message = function (p) {
    return ['Hi ' + (A.name || 'Block Advisory') + ', please send me the price list and floor plans for ' + D.name + '.',
      'Name: ' + p.name, 'Phone: ' + p.phone, p.email ? 'Email: ' + p.email : '', p.unit ? 'Interested in: ' + p.unit : '',
      p.purpose ? 'Buying to: ' + p.purpose : '', p.contact ? 'Best contact: ' + p.contact : '', p.message ? 'Note: ' + p.message : ''].filter(Boolean).join('\n');
  };
  function send(p) {
    dl.push({ event: 'generate_lead', development: D.slug, lead_source: p.source, lead_unit: p.unit || '' });
    try { if (window.fbq) window.fbq('track', 'Lead', { content_name: D.name }); } catch (e) { /* pixel optional */ }
    if (!A.formEndpoint) {
      // No CRM endpoint yet: open WhatsApp now, inside the click, so pop-up blockers allow it.
      window.open(waLink(message(p)), '_blank', 'noopener');
      return Promise.resolve('whatsapp');
    }
    return fetch(A.formEndpoint, { method: 'POST', headers: { 'Content-Type': 'application/json', Accept: 'application/json' }, body: JSON.stringify(p) })
      .then(function (r) { if (!r.ok) throw new Error('HTTP ' + r.status); return 'sent'; });
  }
  function success(form, p, via) {
    var ok = form.nextElementSibling;
    var first = (p.name || '').trim().split(/\s+/)[0];
    ok.querySelector('[data-first]').textContent = first ? ', ' + first : '';
    ok.querySelector('[data-okmsg]').textContent = via === 'whatsapp'
      ? "We've opened WhatsApp with your request ready to send. Tap send and an advisor will reply with the price list."
      : 'An advisor will be in touch shortly with the price list and floor plans for ' + D.name + '.';
    ok.querySelector('[data-wa-ok]').href = waLink(message(p));
    var asset = form.dataset.planUrl || D.brochure || '';
    var ab = ok.querySelector('[data-asset]');
    if (asset && ab) {
      ab.href = asset; ab.hidden = false;
      ab.innerHTML = (form.dataset.planUrl ? 'Open the floor plan' : 'Download the brochure') + icon('arrow');
      ab.addEventListener('click', function () { dl.push({ event: 'asset_open', development: D.slug, asset: asset }); }, { once: true });
    } else if (ab) ab.hidden = true;
    form.hidden = true; ok.hidden = false; ok.focus({ preventScroll: true });
  }
  document.addEventListener('submit', function (e) {
    var form = e.target.closest && e.target.closest('form[data-lead]');
    if (!form) return;
    e.preventDefault();
    var fd = new FormData(form), d = {};
    fd.forEach(function (v, k) { d[k] = typeof v === 'string' ? v.trim() : v; });
    if (d.company) return; // spam trap
    var errs = {};
    if (!d.name || d.name.length < 2) errs.name = 'Please enter your name.';
    var n = digits(d.phone);
    if (n.length < 9 || n.length > 15) errs.phone = 'Please enter a valid phone number.';
    if (d.email && !/^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(d.email)) errs.email = 'Please check your email address.';
    $$('[data-f]', form).forEach(function (w) { var k = w.dataset.f; w.classList.toggle('bad', !!errs[k]); var er = w.querySelector('.err'); if (er) er.textContent = errs[k] || ''; });
    var firstErr = Object.keys(errs)[0];
    if (firstErr) { form.querySelector('[name="' + firstErr + '"]').focus(); return; }
    delete d.company;
    var p = Object.assign({}, d, {
      source: form.dataset.lead, unit: d.unit || form.dataset.unit || '', intent: form.dataset.intent || '',
      development: D.name, slug: D.slug, page: location.href, submitted_at: new Date().toISOString()
    }, ATTR);
    remembered = { name: d.name, phone: d.phone, email: d.email || remembered.email || '' };
    try { sessionStorage.setItem('ba_lead', JSON.stringify(remembered)); } catch (er) { /* ignore */ }
    var btn = form.querySelector('[type=submit]');
    btn.disabled = true;
    send(p).then(function (via) { success(form, p, via); }).catch(function () {
      btn.disabled = false;
      var al = form.querySelector('.alert') || document.createElement('p');
      al.className = 'alert'; al.setAttribute('role', 'alert');
      al.innerHTML = 'Sorry, that didn\'t go through. <a target="_blank" rel="noopener" href="' + esc(waLink(message(p))) + '">Send it on WhatsApp instead</a>.';
      if (!al.parentNode) btn.insertAdjacentElement('beforebegin', al);
    });
  });
  $$('form[data-lead] input').forEach(function (i) {
    i.addEventListener('input', function () { var w = i.closest('[data-f]'); if (w) { w.classList.remove('bad'); var er = w.querySelector('.err'); if (er) er.textContent = ''; } });
  });

  /* ---------- Enquiry modal ---------- */
  var md = $('#md'), mdForm = $('#md-form'), lastFocus = null;
  function resetModalForm() {
    var f = $('form', mdForm), ok = $('.ok', mdForm);
    f.hidden = false; ok.hidden = true;
    var b = f.querySelector('[type=submit]'); b.disabled = false;
    var al = f.querySelector('.alert'); if (al) al.remove();
    return f;
  }
  function openModal(source, unit, asset) {
    var f = resetModalForm();
    f.dataset.unit = unit || '';
    f.dataset.intent = source || '';
    f.dataset.planUrl = asset || '';
    var titles = { 'payment-plan': 'Get the payment plan', brochure: 'Download the brochure' };
    $('#md-t').textContent = unit ? 'Floor plan and price' : (titles[source] || 'Get the price list');
    $('#md-sub').textContent = unit ? unit + ' at ' + D.name + '. Sent to your WhatsApp.'
      : source === 'brochure' ? 'Leave your details and the brochure opens straight away.' : 'Floor plans and the payment plan, sent to your WhatsApp.';
    prefill(f);
    lastFocus = document.activeElement;
    md.hidden = false; document.body.classList.add('lock');
    var first = f.querySelector('input[name="name"]');
    setTimeout(function () { (first.value ? f.querySelector('[type=submit]') : first).focus(); }, 30);
    dl.push({ event: 'enquiry_open', development: D.slug, source: source, unit: unit || '' });
  }
  function closeModal() { md.hidden = true; document.body.classList.remove('lock'); if (lastFocus) lastFocus.focus(); }
  document.addEventListener('click', function (e) {
    var t = e.target.closest && e.target.closest('[data-enquire]');
    if (t) { e.preventDefault(); openModal(t.dataset.enquire, t.dataset.unit, t.dataset.asset); return; }
    if (e.target === md || (e.target.closest && e.target.closest('[data-close]'))) closeModal();
  });

  /* ---------- Lightbox ---------- */
  var lb = $('#lb'), lbImg = $('#lb-img'), strip = $('#lb-strip'), cur = 0, lbFocus = null;
  gallery.forEach(function (p, i) {
    var b = document.createElement('button'); b.type = 'button'; b.setAttribute('aria-label', 'Photo ' + (i + 1));
    b.innerHTML = '<img loading="lazy" alt="" src="' + esc(p.src) + '">';
    b.addEventListener('click', function () { show(i); });
    strip.appendChild(b);
  });
  function show(i) {
    if (!gallery.length) return;
    cur = (i + gallery.length) % gallery.length;
    lbImg.src = gallery[cur].src; lbImg.alt = gallery[cur].alt || '';
    $('#lb-n').textContent = (cur + 1) + ' / ' + gallery.length;
    $$('button', strip).forEach(function (b, j) { b.classList.toggle('on', j === cur); });
    if (strip.children[cur]) strip.children[cur].scrollIntoView({ block: 'nearest', inline: 'center' });
  }
  function openLb(i) { if (!gallery.length) return; lbFocus = document.activeElement; lb.hidden = false; document.body.classList.add('lock'); show(i); $('.lb-x').focus(); dl.push({ event: 'gallery_open', development: D.slug }); }
  function closeLb() { lb.hidden = true; document.body.classList.remove('lock'); if (lbFocus) lbFocus.focus(); }
  document.addEventListener('click', function (e) {
    var o = e.target.closest && e.target.closest('[data-open]');
    if (o) openLb(Number(o.dataset.open));
    if (e.target.closest && e.target.closest('[data-lbclose]')) closeLb();
  });
  $('.lb-prev').addEventListener('click', function () { show(cur - 1); });
  $('.lb-next').addEventListener('click', function () { show(cur + 1); });

  document.addEventListener('keydown', function (e) {
    if (!lb.hidden) {
      if (e.key === 'Escape') closeLb();
      if (e.key === 'ArrowLeft') show(cur - 1);
      if (e.key === 'ArrowRight') show(cur + 1);
      return;
    }
    if (!md.hidden) {
      if (e.key === 'Escape') { closeModal(); return; }
      if (e.key === 'Tab') { // keep focus inside the dialog
        var f = $$('button, input, a[href]', md).filter(function (x) { return x.offsetParent !== null && !x.classList.contains('hp') && x.tabIndex !== -1; });
        if (!f.length) return;
        if (e.shiftKey && document.activeElement === f[0]) { e.preventDefault(); f[f.length - 1].focus(); }
        else if (!e.shiftKey && document.activeElement === f[f.length - 1]) { e.preventDefault(); f[0].focus(); }
      }
    }
  });

  /* ---------- Video ---------- */
  $$('[data-video] button').forEach(function (b) {
    b.addEventListener('click', function () {
      var box = b.parentNode, id = box.dataset.video;
      box.innerHTML = '<iframe src="https://www.youtube-nocookie.com/embed/' + id + '?autoplay=1&rel=0" title="' + esc(D.name) + ' video" allow="autoplay; encrypted-media; picture-in-picture" allowfullscreen></iframe>';
      dl.push({ event: 'video_play', development: D.slug });
    });
  });

  /* ---------- Residence filter ---------- */
  var rows = $$('.tbl tbody tr');
  $$('.filters button').forEach(function (b) {
    b.addEventListener('click', function () {
      $$('.filters button').forEach(function (x) { x.setAttribute('aria-pressed', String(x === b)); });
      rows.forEach(function (r) { r.hidden = b.dataset.g !== '*' && r.dataset.g !== b.dataset.g; });
    });
  });

  /* ---------- Payment calculator ---------- */
  var cpPrice = $('#cp-price'), cpDep = $('#cp-dep');
  if (cpPrice && cpDep && planMonths) {
    var calc = function () {
      var price = Number(digits(cpPrice.value)) || 0, dep = price * Number(cpDep.value) / 100, bal = price - dep;
      $('#cp-dep-out').textContent = cpDep.value + '%';
      cpDep.style.setProperty('--p', (Number(cpDep.value) / Number(cpDep.max) * 100) + '%');
      $('#cp-deposit').textContent = price ? money(dep) : '—';
      $('#cp-balance').textContent = price ? money(bal) : '—';
      $('#cp-month').textContent = price ? money(bal / planMonths) : '—';
    };
    cpPrice.addEventListener('input', calc);
    cpPrice.addEventListener('blur', function () { var v = Number(digits(cpPrice.value)); cpPrice.value = v ? v.toLocaleString('en-KE') : ''; calc(); });
    cpDep.addEventListener('input', calc);
    calc();
  }

  /* ---------- Header, sticky bar, scroll spy ---------- */
  var hd = $('#hd'), mb = $('#mb'), quick = $('#quick'), enquire = $('#enquire');
  var navLinks = $$('.hd-nav a'), targets = navLinks.map(function (a) { return document.getElementById(a.getAttribute('href').slice(1)); });
  var ticking = false;
  function onScroll() {
    ticking = false;
    var y = window.scrollY;
    hd.classList.toggle('on', y > 8);
    var qb = quick ? quick.getBoundingClientRect().bottom : 0;
    var eb = enquire ? enquire.getBoundingClientRect() : null;
    var inContact = eb && eb.top < window.innerHeight * 0.75 && eb.bottom > 0;
    mb.classList.toggle('on', qb < 0 && !inContact);
    var on = -1;
    targets.forEach(function (t, i) { if (t && t.getBoundingClientRect().top < 160) on = i; });
    navLinks.forEach(function (a, i) { a.classList.toggle('on', i === on); });
  }
  window.addEventListener('scroll', function () { if (!ticking) { ticking = true; requestAnimationFrame(onScroll); } }, { passive: true });
  onScroll();

  /* ---------- Reveal ---------- */
  var rv = $$('.rv');
  if ('IntersectionObserver' in window && !reduce) {
    var io = new IntersectionObserver(function (es) { es.forEach(function (en) { if (en.isIntersecting) { en.target.classList.add('in'); io.unobserve(en.target); } }); }, { rootMargin: '0px 0px -6% 0px', threshold: 0.06 });
    rv.forEach(function (x) { io.observe(x); });
  } else rv.forEach(function (x) { x.classList.add('in'); });
})();
