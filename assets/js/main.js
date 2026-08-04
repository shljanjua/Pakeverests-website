/* ==========================================================================
   Pak-Everests — front-end behaviour
   Vanilla JS, no dependencies. Every block guards for its own markup.
   ========================================================================== */
(function () {
  'use strict';

  var $  = function (sel, ctx) { return (ctx || document).querySelector(sel); };
  var $$ = function (sel, ctx) { return Array.prototype.slice.call((ctx || document).querySelectorAll(sel)); };

  /* ----------------------------------------------------------------------
     Theme toggle (light / dark) — persisted in localStorage
     -------------------------------------------------------------------- */
  (function theme() {
    var root = document.documentElement;
    var btn  = $('#themeToggle');
    if (!btn) return;

    btn.addEventListener('click', function () {
      var next = root.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
      root.setAttribute('data-theme', next);
      try { localStorage.setItem('pe_theme', next); } catch (e) {}
      document.cookie = 'pe_theme=' + next + ';path=/;max-age=31536000;samesite=Lax';
      btn.setAttribute('aria-label', next === 'dark' ? 'Switch to light mode' : 'Switch to dark mode');
    });
  })();

  /* ----------------------------------------------------------------------
     Mobile navigation
     -------------------------------------------------------------------- */
  (function nav() {
    var toggle = $('#navToggle');
    var drawer = $('#mobileNav');
    if (!toggle || !drawer) return;

    function setOpen(open) {
      drawer.classList.toggle('is-open', open);
      drawer.setAttribute('aria-hidden', String(!open));
      toggle.setAttribute('aria-expanded', String(open));
      toggle.setAttribute('aria-label', open ? 'Close menu' : 'Open menu');
      document.body.style.overflow = open ? 'hidden' : '';
      if (open) {
        var firstLink = drawer.querySelector('.mobile-nav-close');
        if (firstLink) firstLink.focus();
      } else {
        toggle.focus();
      }
    }

    toggle.addEventListener('click', function () {
      setOpen(!drawer.classList.contains('is-open'));
    });

    // Anything marked data-close (overlay, close button, links, CTAs) closes it.
    drawer.addEventListener('click', function (e) {
      if (e.target.closest('[data-close]')) setOpen(false);
    });

    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape' && drawer.classList.contains('is-open')) setOpen(false);
    });

    // Accordion behaviour for the dropdown sections inside the drawer.
    $$('.mobile-nav-list .has-dropdown > a').forEach(function (link) {
      link.addEventListener('click', function (e) {
        var parent = link.parentElement;
        // First tap expands the section; the parent link still navigates on a
        // second tap (the section is already open).
        if (!parent.classList.contains('is-open')) {
          e.preventDefault();
          $$('.mobile-nav-list .has-dropdown.is-open').forEach(function (o) {
            if (o !== parent) o.classList.remove('is-open');
          });
          parent.classList.add('is-open');
        }
      });
    });

    window.addEventListener('resize', function () {
      if (window.innerWidth >= 1100) setOpen(false);
    });
  })();

  /* ----------------------------------------------------------------------
     Sticky header shadow + back to top
     -------------------------------------------------------------------- */
  (function scrollUi() {
    var header = $('#siteHeader');
    var top    = $('#backToTop');
    var ticking = false;

    function update() {
      var y = window.pageYOffset;
      if (header) header.classList.toggle('is-stuck', y > 12);
      if (top)    top.classList.toggle('is-visible', y > 480);
      ticking = false;
    }
    window.addEventListener('scroll', function () {
      if (!ticking) { window.requestAnimationFrame(update); ticking = true; }
    }, { passive: true });
    update();

    if (top) {
      top.addEventListener('click', function () {
        window.scrollTo({ top: 0, behavior: 'smooth' });
      });
    }
  })();

  /* ----------------------------------------------------------------------
     Scroll reveal
     -------------------------------------------------------------------- */
  (function reveal() {
    var items = $$('.reveal');
    if (!items.length) return;
    if (!('IntersectionObserver' in window)) {
      items.forEach(function (el) { el.classList.add('is-visible'); });
      return;
    }
    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          entry.target.classList.add('is-visible');
          io.unobserve(entry.target);
        }
      });
    }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });
    items.forEach(function (el, i) {
      el.style.transitionDelay = Math.min(i % 6, 5) * 70 + 'ms';
      io.observe(el);
    });
  })();

  /* ----------------------------------------------------------------------
     Hero bubbles
     -------------------------------------------------------------------- */
  (function bubbles() {
    var host = $('.hero-bubbles');
    if (!host) return;
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

    var count = window.innerWidth < 700 ? 12 : 22;
    var frag  = document.createDocumentFragment();
    for (var i = 0; i < count; i++) {
      var b = document.createElement('span');
      var size = 8 + Math.random() * 46;
      b.style.width = size + 'px';
      b.style.height = size + 'px';
      b.style.left = Math.random() * 100 + '%';
      b.style.animationDuration = (10 + Math.random() * 16) + 's';
      b.style.animationDelay = (Math.random() * 12) + 's';
      b.style.setProperty('--drift', (Math.random() * 120 - 60) + 'px');
      frag.appendChild(b);
    }
    host.appendChild(frag);
  })();

  /* ----------------------------------------------------------------------
     Accordions (FAQ etc.)
     -------------------------------------------------------------------- */
  (function accordion() {
    $$('.accordion-trigger').forEach(function (btn) {
      btn.setAttribute('aria-expanded', 'false');
      btn.addEventListener('click', function () {
        var item  = btn.closest('.accordion-item');
        var panel = $('.accordion-panel', item);
        var open  = item.classList.contains('is-open');
        var group = btn.closest('.accordion');

        if (group && group.dataset.single === 'true') {
          $$('.accordion-item.is-open', group).forEach(function (other) {
            if (other !== item) {
              other.classList.remove('is-open');
              $('.accordion-panel', other).style.maxHeight = null;
              $('.accordion-trigger', other).setAttribute('aria-expanded', 'false');
            }
          });
        }

        item.classList.toggle('is-open', !open);
        btn.setAttribute('aria-expanded', String(!open));
        panel.style.maxHeight = open ? null : panel.scrollHeight + 'px';
      });
    });

    // Open the first item of any accordion marked data-open-first.
    $$('.accordion[data-open-first="true"]').forEach(function (group) {
      var first = $('.accordion-trigger', group);
      if (first) first.click();
    });
  })();

  /* ----------------------------------------------------------------------
     Gallery filter + lightbox
     -------------------------------------------------------------------- */
  (function gallery() {
    var chips = $$('.filter-chip');
    if (chips.length) {
      chips.forEach(function (chip) {
        chip.addEventListener('click', function () {
          var filter = chip.dataset.filter;
          var target = chip.dataset.target || '.gallery-grid';
          chips.forEach(function (c) { if (c.dataset.target === chip.dataset.target) c.classList.remove('is-active'); });
          chip.classList.add('is-active');
          $$(target + ' [data-category]').forEach(function (item) {
            var show = filter === 'all' || item.dataset.category === filter;
            item.style.display = show ? '' : 'none';
          });
        });
      });
    }

    var triggers = $$('[data-lightbox]');
    if (!triggers.length) return;

    var box = document.createElement('div');
    box.className = 'lightbox';
    box.setAttribute('role', 'dialog');
    box.setAttribute('aria-modal', 'true');
    box.innerHTML = '<button class="lightbox-close" type="button" aria-label="Close">&times;</button>' +
                    '<div><img src="" alt=""><p class="lightbox-caption"></p></div>';
    document.body.appendChild(box);

    var img = $('img', box), cap = $('.lightbox-caption', box);

    function close() { box.classList.remove('is-open'); document.body.style.overflow = ''; }

    triggers.forEach(function (t) {
      t.addEventListener('click', function (e) {
        e.preventDefault();
        img.src = t.dataset.lightbox;
        img.alt = t.dataset.caption || '';
        cap.textContent = t.dataset.caption || '';
        box.classList.add('is-open');
        document.body.style.overflow = 'hidden';
      });
    });
    box.addEventListener('click', function (e) { if (e.target === box || e.target.classList.contains('lightbox-close')) close(); });
    document.addEventListener('keydown', function (e) { if (e.key === 'Escape') close(); });
  })();

  /* ----------------------------------------------------------------------
     Order form — quantity controls and live total
     -------------------------------------------------------------------- */
  (function orderForm() {
    var form = $('#orderForm');
    if (!form) return;

    var fmt = function (n) { return 'Rs ' + Number(n).toLocaleString('en-PK'); };

    function recalc() {
      var water = 0, deposit = 0, lines = [];
      $$('.order-item', form).forEach(function (row) {
        var input = $('input[type="number"]', row);
        var qty   = parseInt(input.value, 10) || 0;
        row.classList.toggle('is-selected', qty > 0);
        if (qty <= 0) return;
        var price = parseFloat(row.dataset.price) || 0;
        var dep   = parseFloat(row.dataset.deposit) || 0;
        water   += price * qty;
        deposit += dep * qty;
        lines.push({ name: row.dataset.name, qty: qty, total: price * qty });
      });

      var list = $('#summaryLines');
      if (list) {
        list.innerHTML = lines.length
          ? lines.map(function (l) {
              return '<div class="summary-row"><span>' + l.name + ' &times; ' + l.qty + '</span><span>' + fmt(l.total) + '</span></div>';
            }).join('')
          : '<p class="form-hint">No products selected yet. Choose a quantity to see your total.</p>';
      }
      var setText = function (id, val) { var el = document.getElementById(id); if (el) el.textContent = val; };
      setText('sumWater', fmt(water));
      setText('sumDeposit', fmt(deposit));
      setText('sumTotal', fmt(water + deposit));

      var depRow = $('#depositRow');
      if (depRow) depRow.style.display = deposit > 0 ? '' : 'none';
    }

    $$('.qty-control', form).forEach(function (ctrl) {
      var input = $('input', ctrl);
      $$('button', ctrl).forEach(function (btn) {
        btn.addEventListener('click', function () {
          var step = parseInt(btn.dataset.step, 10) || 1;
          var next = (parseInt(input.value, 10) || 0) + step;
          input.value = Math.max(0, Math.min(999, next));
          recalc();
        });
      });
      input.addEventListener('input', recalc);
    });

    form.addEventListener('submit', function () {
      var btn = $('button[type="submit"]', form);
      if (btn) { btn.disabled = true; btn.textContent = 'Sending your order…'; }
    });

    recalc();
  })();

  /* ----------------------------------------------------------------------
     Bulk water calculator (Rs per litre)
     -------------------------------------------------------------------- */
  (function calculator() {
    var box = $('#bulkCalculator');
    if (!box) return;

    var rate    = parseFloat(box.dataset.rate) || 6;
    var litres  = $('#calcLitres', box);
    var freq    = $('#calcFrequency', box);
    var out     = {
      per:     $('#calcPerFill', box),
      daily:   $('#calcDaily', box),
      monthly: $('#calcMonthly', box),
      yearly:  $('#calcYearly', box),
      litres:  $('#calcLitresOut', box)
    };

    function fmt(n) { return 'Rs ' + Math.round(n).toLocaleString('en-PK'); }

    function run() {
      var l = parseFloat(litres.value) || 0;
      var perFill = l * rate;
      var perDay  = perFill * (parseFloat(freq.value) || 1);
      if (out.per)     out.per.textContent = fmt(perFill);
      if (out.daily)   out.daily.textContent = fmt(perDay);
      if (out.monthly) out.monthly.textContent = fmt(perDay * 30);
      if (out.yearly)  out.yearly.textContent = fmt(perDay * 365);
      if (out.litres)  out.litres.textContent = l.toLocaleString('en-PK') + ' litres';
    }

    [litres, freq].forEach(function (el) {
      if (el) { el.addEventListener('input', run); el.addEventListener('change', run); }
    });
    run();
  })();

  /* ----------------------------------------------------------------------
     Star rating input
     -------------------------------------------------------------------- */
  (function ratingInput() {
    var group = $('#ratingInput');
    if (!group) return;
    var inputs = $$('input[type="radio"]', group);
    inputs.forEach(function (input) {
      input.addEventListener('change', function () {
        var label = $('#ratingLabel');
        var words = { 1: 'Poor', 2: 'Fair', 3: 'Good', 4: 'Very good', 5: 'Excellent' };
        if (label) label.textContent = words[input.value] || '';
      });
    });
  })();

  /* ----------------------------------------------------------------------
     Prevent double submission on every public form
     -------------------------------------------------------------------- */
  (function guardForms() {
    $$('form[data-guard="true"]').forEach(function (form) {
      form.addEventListener('submit', function () {
        var btn = $('button[type="submit"]', form);
        if (btn) {
          btn.disabled = true;
          btn.dataset.original = btn.textContent;
          btn.textContent = 'Please wait…';
          // Re-enable if the browser restores the page from cache.
          setTimeout(function () { btn.disabled = false; btn.textContent = btn.dataset.original; }, 12000);
        }
      });
    });
  })();

  /* ----------------------------------------------------------------------
     Copy-to-clipboard (payment details)
     -------------------------------------------------------------------- */
  (function copyable() {
    $$('[data-copy]').forEach(function (btn) {
      btn.addEventListener('click', function () {
        var text = btn.dataset.copy;
        var done = function () {
          var old = btn.textContent;
          btn.textContent = 'Copied';
          setTimeout(function () { btn.textContent = old; }, 1600);
        };
        if (navigator.clipboard) {
          navigator.clipboard.writeText(text).then(done);
        } else {
          var ta = document.createElement('textarea');
          ta.value = text; document.body.appendChild(ta); ta.select();
          try { document.execCommand('copy'); done(); } catch (e) {}
          document.body.removeChild(ta);
        }
      });
    });
  })();

  /* ----------------------------------------------------------------------
     Count-up statistics
     -------------------------------------------------------------------- */
  (function counters() {
    var nodes = $$('[data-count]');
    if (!nodes.length || !('IntersectionObserver' in window)) return;

    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (!entry.isIntersecting) return;
        var el = entry.target;
        var target = parseFloat(el.dataset.count) || 0;
        var suffix = el.dataset.suffix || '';
        var start = null;
        function step(ts) {
          if (!start) start = ts;
          var p = Math.min((ts - start) / 1400, 1);
          var eased = 1 - Math.pow(1 - p, 3);
          el.textContent = Math.round(target * eased).toLocaleString('en-PK') + suffix;
          if (p < 1) window.requestAnimationFrame(step);
        }
        window.requestAnimationFrame(step);
        io.unobserve(el);
      });
    }, { threshold: 0.4 });

    nodes.forEach(function (n) { io.observe(n); });
  })();

})();
