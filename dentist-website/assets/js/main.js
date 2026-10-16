/* Brightwater Dental: small progressive enhancements. Every page works without this file.
   The .js class is set by an inline script in <head>; contact details are read from the page, not stored here. */
(function () {
  'use strict';

  var doc = document;
  var root = doc.documentElement;
  root.classList.add('js');
  var reduceMotion = window.matchMedia ? window.matchMedia('(prefers-reduced-motion: reduce)') : { matches: false };

  /* ---------- Copyright year (static text in the HTML, refreshed here) ---------- */
  Array.prototype.forEach.call(doc.querySelectorAll('[data-year]'), function (el) {
    el.textContent = String(new Date().getFullYear());
  });

  /* ---------- Pages with <base href> (404.html): "#main" would resolve to the home page, so keep the skip link here ---------- */
  if (doc.querySelector('base[href]')) {
    var skip = doc.querySelector('.skip-link');
    if (skip) skip.href = location.href.split('#')[0] + '#main';
  }

  /* ---------- Mobile navigation disclosure ---------- */
  var toggle = doc.querySelector('[data-nav-toggle]');
  var nav = toggle ? doc.getElementById(toggle.getAttribute('aria-controls')) : null;

  if (toggle && nav) {
    var header = toggle.closest('header') || nav.parentNode;
    var desktop = window.matchMedia('(min-width: 68em)');
    var isOpen = function () { return toggle.getAttribute('aria-expanded') === 'true'; };

    // The panel may scroll on its own: size it to the space left below the header (landscape phones)
    var fit = function () {
      nav.style.setProperty('--nav-max', Math.max(120, window.innerHeight - header.getBoundingClientRect().bottom) + 'px');
    };

    // The button keeps its "Menu" name; aria-expanded (and the bars turning into an X) carry the open state
    var setOpen = function (open, returnFocus) {
      if (open) {
        // Very short screens at the top of the page (template notice above the sticky header):
        // bring the header to the top first, so the panel gets the whole screen below it
        var top = header.getBoundingClientRect().top;
        if (top > 0 && window.innerHeight < 480) window.scrollBy(0, top);
        fit();
      }
      toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
      nav.classList.toggle('is-open', open);
      if (!open && returnFocus) toggle.focus();
    };

    toggle.addEventListener('click', function () {
      var open = !isOpen();
      setOpen(open);
      if (open) {
        var first = nav.querySelector('a');
        if (first) first.focus();
      }
    });

    // Escape closes and returns focus to the toggle
    doc.addEventListener('keydown', function (event) {
      if (event.key === 'Escape' && isOpen()) setOpen(false, true);
    });

    // A click or tap outside the header closes the menu
    doc.addEventListener('click', function (event) {
      if (isOpen() && !header.contains(event.target)) setOpen(false);
    });

    // Tabbing out of the open menu closes it, so it never hides the page behind it
    header.addEventListener('focusout', function (event) {
      if (isOpen() && event.relatedTarget && !header.contains(event.relatedTarget)) setOpen(false);
    });

    // Following a link (including same-page anchors) closes the menu
    nav.addEventListener('click', function (event) {
      if (event.target.closest('a')) setOpen(false);
    });

    window.addEventListener('resize', function () { if (isOpen()) fit(); });
    window.addEventListener('scroll', function () { if (isOpen()) fit(); }, { passive: true });

    var onChange = function (event) { if (event.matches) setOpen(false); };
    if (desktop.addEventListener) desktop.addEventListener('change', onChange);
    else if (desktop.addListener) desktop.addListener(onChange);
  }

  /* ---------- Odontogram: a one-off "charting pass" when it comes into view, and tooth names on hover ---------- */
  var chart = doc.querySelector('[data-odontogram]');
  if (chart) {
    if (!reduceMotion.matches && 'IntersectionObserver' in window) {
      var seen = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
          if (entry.isIntersecting) {
            chart.classList.add('is-charting');
            seen.disconnect();
          }
        });
      }, { threshold: 0.35 });
      seen.observe(chart);
    }

    var readout = doc.querySelector('[data-odo-readout]');
    if (readout) {
      var kinds = ['third molar (wisdom tooth)', 'second molar', 'first molar', 'second premolar', 'first premolar', 'canine', 'lateral incisor', 'central incisor'];
      var defaultText = readout.textContent;
      var nameOf = function (n) {
        if (n <= 8) return 'upper right ' + kinds[n - 1];
        if (n <= 16) return 'upper left ' + kinds[16 - n];
        if (n <= 24) return 'lower left ' + kinds[n - 17];
        return 'lower right ' + kinds[32 - n];
      };
      chart.addEventListener('mouseover', function (event) {
        var tooth = event.target.closest('[data-tooth]');
        if (!tooth) return;
        var n = parseInt(tooth.getAttribute('data-tooth'), 10);
        readout.textContent = 'Tooth ' + n + ': ' + nameOf(n);
      });
      chart.addEventListener('mouseleave', function () { readout.textContent = defaultText; });
    }
  }

  /* ---------- Breathing cue (comfort band): user-started, stoppable (WCAG 2.2.2) ---------- */
  Array.prototype.forEach.call(doc.querySelectorAll('[data-breath]'), function (box) {
    var button = box.querySelector('[data-breath-toggle]');
    var cue = box.querySelector('[data-breath-cue]');
    if (!button || !cue) return;
    var restingCue = cue.innerHTML;
    var timer = null;
    var phase = 0;

    var step = function () {
      // 4 s in, 6 s out: matches the CSS animation; text cue works with or without motion
      cue.textContent = phase === 0 ? 'Breathe in' : 'Breathe out';
      timer = window.setTimeout(step, phase === 0 ? 4000 : 6000);
      phase = 1 - phase;
    };
    var stop = function () {
      window.clearTimeout(timer);
      timer = null;
      phase = 0;
      cue.innerHTML = restingCue;
      box.classList.remove('is-on');
      button.setAttribute('aria-pressed', 'false');
    };

    button.addEventListener('click', function () {
      if (button.getAttribute('aria-pressed') === 'true') { stop(); return; }
      button.setAttribute('aria-pressed', 'true');
      box.classList.add('is-on');
      step();
    });
  });

  /* ---------- Appointment request form ----------
     <form data-appointment-form data-endpoint="" data-phone-href="tel:+15550142368" data-phone-text="(555) 014-2368">
     Empty endpoint: validate, then explain that online requests are not connected yet (with the phone number).
     Endpoint set (e.g. a Formspree URL): POST FormData with Accept: application/json; report success only if it worked.
     Status messages are announced by the role="status" live region; focus stays where the visitor is.
     contact.html?reason=nervous#request preselects the reason <option data-reason="nervous">. */
  Array.prototype.forEach.call(doc.querySelectorAll('form[data-appointment-form]'), function (form) {
    var status = form.querySelector('[data-form-status]');
    var submit = form.querySelector('[type="submit"], button:not([type])');
    var phoneHref = form.getAttribute('data-phone-href') || '';
    var phoneText = form.getAttribute('data-phone-text') || '';
    var attempted = false;
    var sending = false;
    var summaryCount = 0; // how many fields the error summary currently counts (0: no summary shown)

    // Arriving from a specific button (e.g. "Request a slower first visit"): preselect the matching reason
    var want = window.URLSearchParams ? new URLSearchParams(window.location.search).get('reason') : null;
    if (want && /^[a-z0-9-]+$/.test(want)) {
      var option = form.querySelector('option[data-reason="' + want + '"]');
      if (option) option.selected = true;
    }

    // Status messages are built as one <span> (text + phone link) so the icon layout stays intact
    var say = function (kind, before, after) {
      if (!status) return;
      var span = doc.createElement('span');
      span.appendChild(doc.createTextNode(before));
      if (after !== undefined && phoneText) {
        var link = doc.createElement('a');
        link.href = phoneHref || '#';
        link.textContent = phoneText;
        span.appendChild(link);
        span.appendChild(doc.createTextNode(after));
      } else if (after !== undefined) {
        span.appendChild(doc.createTextNode('us' + after));
      }
      status.textContent = '';
      status.setAttribute('data-kind', kind);
      status.appendChild(span);
      summaryCount = 0;
    };

    var clearStatus = function () {
      if (status) { status.textContent = ''; status.removeAttribute('data-kind'); }
      summaryCount = 0;
    };

    var summarize = function (count) {
      say('error', count === 1
        ? 'One field needs attention. It is marked above.'
        : count + ' fields need attention. They are marked above.');
      summaryCount = count;
    };

    var messageFor = function (field) {
      var v = field.validity;
      var custom = field.getAttribute('data-msg');
      if (field.type === 'checkbox') return custom || 'Please check this box so we can contact you.';
      if (field.type === 'radio') return custom || 'Please choose one option.';
      if (v.valueMissing || field.validationMessage === 'blank') return custom || 'Please fill in this field.';
      if (field.type === 'email') return 'Please enter an email address like name@example.com.';
      if (field.type === 'tel') return 'Please enter a phone number with 7 to 15 digits. Spaces, dashes, dots, brackets and + are fine.';
      if (v.tooLong) return 'Please shorten this to ' + field.maxLength + ' characters or fewer.';
      return custom || field.validationMessage;
    };

    // Checks the browser doesn't make: a required text field holding only spaces, and phone numbers
    // (7 to 15 digits, using only digits, spaces and ( ) + - .)
    var customCheck = function (field) {
      if (!field.setCustomValidity) return;
      var value = field.value;
      var problem = '';
      if (field.required && /^(text|tel|textarea)$/.test(field.type) && value && !value.trim()) {
        problem = 'blank';
      } else if (field.type === 'tel' && value) {
        var digits = value.replace(/\D/g, '').length;
        if (!/^[0-9()+.\s-]+$/.test(value) || digits < 7 || digits > 15) problem = 'phone';
      }
      field.setCustomValidity(problem);
    };

    // Validate one control (radio groups are checked as a group, keyed by name)
    var validate = function (field) {
      customCheck(field);
      var key = field.type === 'radio' ? field.name : field.id;
      var ok = field.checkValidity();
      var group = field.type === 'radio' ? form.querySelectorAll('input[type="radio"][name="' + field.name + '"]') : [field];
      Array.prototype.forEach.call(group, function (el) { el.setAttribute('aria-invalid', ok ? 'false' : 'true'); });
      var error = key ? form.querySelector('[data-error-for="' + key + '"]') : null;
      if (error) error.textContent = ok ? '' : messageFor(field);
      return ok;
    };

    var fields = function () {
      var seenGroups = {};
      return Array.prototype.filter.call(form.elements, function (el) {
        if (!el.willValidate || el.type === 'submit' || el.closest('.hp-field')) return false;
        if (el.type === 'radio') {
          if (seenGroups[el.name]) return false;
          seenGroups[el.name] = true;
        }
        return true;
      });
    };

    form.setAttribute('novalidate', '');

    // After the first attempt, errors clear as soon as a field is fixed, and the summary keeps count
    var revalidate = function (event) {
      var el = event.target;
      if (!attempted || !el.willValidate || el.getAttribute('aria-invalid') !== 'true') return;
      validate(el);
      if (!summaryCount) return;
      var left = fields().filter(function (f) { return f.getAttribute('aria-invalid') === 'true'; }).length;
      if (!left) clearStatus();
      else if (left !== summaryCount) summarize(left);
    };
    form.addEventListener('input', revalidate);
    form.addEventListener('change', revalidate);

    form.addEventListener('submit', function (event) {
      event.preventDefault();
      if (sending) return;
      attempted = true;
      var button = event.submitter || submit;

      var invalid = fields().filter(function (el) { return !validate(el); });
      if (invalid.length) {
        summarize(invalid.length);
        invalid[0].focus();
        return;
      }

      // A filled-in spam trap (the hidden _gotcha field) means a bot: send nothing
      var trap = form.querySelector('.hp-field input');
      var endpoint = (form.getAttribute('data-endpoint') || '').trim();
      if (!endpoint || (trap && trap.value)) {
        say('info', 'Online requests aren’t connected yet, so this form hasn’t been sent. Please call ', ' and we’ll find you a time.');
        return;
      }

      // aria-disabled (not disabled) keeps keyboard focus on the button while the request is on its way
      sending = true;
      if (button) button.setAttribute('aria-disabled', 'true');
      form.setAttribute('aria-busy', 'true');
      say('info', 'Sending your request…');

      // Give up after 15 seconds, so a service that never answers can't leave the form stuck on "Sending"
      var controller = window.AbortController ? new AbortController() : null;
      var timer = controller ? window.setTimeout(function () { controller.abort(); }, 15000) : null;

      fetch(endpoint, { method: 'POST', body: new FormData(form), headers: { Accept: 'application/json' }, signal: controller ? controller.signal : undefined })
        .then(function (response) {
          if (!response.ok) throw new Error('HTTP ' + response.status);
          form.reset();
          attempted = false;
          say('success', 'Thank you. Your request has reached us, and we’ll contact you to confirm a time. If it’s urgent, call ', '.');
        })
        .catch(function () {
          // The request may still have arrived (for example if the service's reply can't be read), so don't invite a resend
          say('error', 'Sorry, we couldn’t confirm that your request reached us. To be sure, please call ', ' rather than sending it again.');
        })
        .then(function () {
          window.clearTimeout(timer);
          sending = false;
          if (button) button.removeAttribute('aria-disabled');
          form.removeAttribute('aria-busy');
        });
    });
  });
})();
