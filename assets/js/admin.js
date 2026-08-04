/* ==========================================================================
   Pak-Everests — Admin panel behaviour
   ========================================================================== */
(function () {
  'use strict';

  var $  = function (s, c) { return (c || document).querySelector(s); };
  var $$ = function (s, c) { return Array.prototype.slice.call((c || document).querySelectorAll(s)); };

  /* ---- Sidebar (mobile) ---- */
  var sidebar  = $('#adminSidebar');
  var toggle   = $('#sidebarToggle');
  var backdrop = $('#sidebarBackdrop');
  if (sidebar && toggle && backdrop) {
    function setOpen(open) {
      sidebar.classList.toggle('is-open', open);
      backdrop.classList.toggle('is-open', open);
      document.body.style.overflow = open ? 'hidden' : '';
    }
    toggle.addEventListener('click', function () { setOpen(!sidebar.classList.contains('is-open')); });
    backdrop.addEventListener('click', function () { setOpen(false); });
    document.addEventListener('keydown', function (e) { if (e.key === 'Escape') setOpen(false); });
  }

  /* ---- Dark mode ---- */
  var themeBtn = $('#adminThemeToggle');
  if (themeBtn) {
    themeBtn.addEventListener('click', function () {
      var root = document.documentElement;
      var next = root.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
      root.setAttribute('data-theme', next);
      try { localStorage.setItem('pe_admin_theme', next); } catch (e) {}
      document.cookie = 'pe_admin_theme=' + next + ';path=/;max-age=31536000;samesite=Lax';
    });
  }

  /* ---- Auto slug from title ---- */
  $$('[data-slug-source]').forEach(function (src) {
    var target = document.getElementById(src.dataset.slugSource);
    if (!target) return;
    src.addEventListener('blur', function () {
      if (target.value.trim() !== '') return;
      target.value = src.value.toLowerCase()
        .replace(/[^\w\s-]/g, '')
        .trim()
        .replace(/[\s_]+/g, '-')
        .replace(/-+/g, '-');
    });
  });

  /* ---- Character counters for meta fields ---- */
  $$('[data-counter]').forEach(function (field) {
    var max = parseInt(field.dataset.counter, 10) || 160;
    var out = document.createElement('span');
    out.className = 'form-hint';
    field.parentNode.appendChild(out);
    function update() {
      var len = field.value.length;
      out.textContent = len + ' / ' + max + ' characters' + (len > max ? ' — this will be truncated in search results' : '');
      out.style.color = len > max ? 'var(--a-bad)' : 'var(--a-muted)';
    }
    field.addEventListener('input', update);
    update();
  });

  /* ---- Confirm destructive actions ---- */
  $$('form[data-confirm]').forEach(function (form) {
    form.addEventListener('submit', function (e) {
      if (!window.confirm(form.dataset.confirm)) e.preventDefault();
    });
  });

  /* ---- Select-all checkbox in tables ---- */
  $$('[data-check-all]').forEach(function (master) {
    master.addEventListener('change', function () {
      $$('input[name="ids[]"]').forEach(function (cb) { cb.checked = master.checked; });
    });
  });

  /* ---- Copy to clipboard ---- */
  $$('[data-copy]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var text = btn.dataset.copy;
      var done = function () {
        var old = btn.textContent;
        btn.textContent = 'Copied';
        setTimeout(function () { btn.textContent = old; }, 1500);
      };
      if (navigator.clipboard) { navigator.clipboard.writeText(text).then(done); }
    });
  });

  /* ---- Quotation line-item calculator ---- */
  var quoteForm = $('#quotationForm');
  if (quoteForm) {
    function recalc() {
      var subtotal = 0;
      $$('.quote-line', quoteForm).forEach(function (row) {
        var qty  = parseFloat($('.q-qty', row).value) || 0;
        var rate = parseFloat($('.q-rate', row).value) || 0;
        var line = qty * rate;
        $('.q-total', row).textContent = 'Rs ' + line.toLocaleString('en-PK');
        row.querySelector('.q-total-input').value = line.toFixed(2);
        subtotal += line;
      });
      var discount = parseFloat($('#quoteDiscount').value) || 0;
      var delivery = parseFloat($('#quoteDelivery').value) || 0;
      var total = subtotal - discount + delivery;
      $('#quoteSubtotal').textContent = 'Rs ' + subtotal.toLocaleString('en-PK');
      $('#quoteTotal').textContent    = 'Rs ' + total.toLocaleString('en-PK');
      $('#quoteSubtotalInput').value  = subtotal.toFixed(2);
      $('#quoteTotalInput').value     = total.toFixed(2);
    }
    quoteForm.addEventListener('input', recalc);

    var addBtn = $('#addQuoteLine');
    if (addBtn) {
      addBtn.addEventListener('click', function () {
        var host = $('#quoteLines');
        var tpl  = $('.quote-line', host);
        var copy = tpl.cloneNode(true);
        $$('input', copy).forEach(function (i) { if (i.type !== 'hidden') i.value = i.classList.contains('q-qty') ? '1' : ''; });
        host.appendChild(copy);
        recalc();
      });
    }

    document.addEventListener('click', function (e) {
      var rm = e.target.closest('.remove-line');
      if (!rm) return;
      var rows = $$('.quote-line', quoteForm);
      if (rows.length > 1) { rm.closest('.quote-line').remove(); recalc(); }
    });

    recalc();
  }

  /* ---- Live ticker preview ---- */
  var tickerPreview = $('#tickerPreview');
  if (tickerPreview) {
    var map = {
      tickerBg: '--ticker-bg', tickerColor: '--ticker-color', tickerHighlight: '--ticker-highlight'
    };
    function applyPreview() {
      Object.keys(map).forEach(function (id) {
        var el = document.getElementById(id);
        if (el) tickerPreview.style.setProperty(map[id], el.value);
      });
      var size = $('#tickerSize'), weight = $('#tickerWeight'), speed = $('#tickerSpeed'), family = $('#tickerFamily');
      if (size)   tickerPreview.style.setProperty('--ticker-size', size.value + 'px');
      if (weight) tickerPreview.style.setProperty('--ticker-weight', weight.value);
      if (speed)  tickerPreview.style.setProperty('--ticker-duration', speed.value + 's');
      if (family) tickerPreview.style.setProperty('--ticker-family', family.value);
    }
    $$('#tickerBg,#tickerColor,#tickerHighlight,#tickerSize,#tickerWeight,#tickerSpeed,#tickerFamily').forEach(function (el) {
      el.addEventListener('input', applyPreview);
      el.addEventListener('change', applyPreview);
    });
    applyPreview();
  }

  /* ---- Unsaved-changes guard on editor forms ---- */
  $$('form[data-dirty-guard]').forEach(function (form) {
    var dirty = false;
    form.addEventListener('input', function () { dirty = true; });
    form.addEventListener('submit', function () { dirty = false; });
    window.addEventListener('beforeunload', function (e) {
      if (dirty) { e.preventDefault(); e.returnValue = ''; }
    });
  });

})();

/* Image preview helper used by image_field() */
function peImagePreview(input, targetId) {
  if (!input.files || !input.files[0]) return;
  var img = document.getElementById(targetId);
  if (!img) return;
  var reader = new FileReader();
  reader.onload = function (e) { img.src = e.target.result; };
  reader.readAsDataURL(input.files[0]);
}

/* Simple rich-text helper: wraps the selection in the given tag */
function peWrap(textareaId, before, after) {
  var ta = document.getElementById(textareaId);
  if (!ta) return;
  var start = ta.selectionStart, end = ta.selectionEnd;
  var sel = ta.value.substring(start, end);
  ta.value = ta.value.substring(0, start) + before + sel + after + ta.value.substring(end);
  ta.focus();
  ta.selectionStart = start + before.length;
  ta.selectionEnd = end + before.length;
}
