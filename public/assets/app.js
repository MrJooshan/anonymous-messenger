// public/assets/app.js
(() => {
  'use strict';

  // ---------- 1) شمارش کاراکتر textarea ----------
  document.querySelectorAll('textarea[maxlength]').forEach((ta) => {
    const max = Number(ta.getAttribute('maxlength')) || 0;
    if (!max) return;

    const counter = document.createElement('div');
    counter.className = 'char-counter';
    counter.textContent = `0 / ${max}`;
    ta.insertAdjacentElement('afterend', counter);

    const update = () => {
      const len = ta.value.length;
      counter.textContent = `${len} / ${max}`;
      counter.classList.toggle('warn', len > max * 0.9);
    };
    ta.addEventListener('input', update);
    update();
  });

  // ---------- 2) کپی لینک اختصاصی ----------
  document.querySelectorAll('[data-copy]').forEach((btn) => {
    btn.addEventListener('click', async () => {
      const value = btn.getAttribute('data-copy');
      try {
        await navigator.clipboard.writeText(value);
        flash(btn, 'کپی شد ✅');
      } catch {
        // fallback ساده
        const ta = document.createElement('textarea');
        ta.value = value;
        document.body.appendChild(ta);
        ta.select();
        document.execCommand('copy');
        document.body.removeChild(ta);
        flash(btn, 'کپی شد ✅');
      }
    });
  });

  function flash(el, text) {
    const original = el.textContent;
    el.textContent = text;
    el.disabled = true;
    setTimeout(() => {
      el.textContent = original;
      el.disabled = false;
    }, 1400);
  }

  // ---------- 3) بستن خودکار پیام‌های alert ----------
  document.querySelectorAll('.msg.success').forEach((el) => {
    setTimeout(() => {
      el.style.transition = 'opacity .4s';
      el.style.opacity = '0';
      setTimeout(() => el.remove(), 400);
    }, 4000);
  });

  // ---------- 4) ارسال ajaxی فرم پیام ناشناس ----------
  const form = document.querySelector('form[data-ajax-send]');
  if (form) {
    form.addEventListener('submit', async (ev) => {
      ev.preventDefault();
      const btn = form.querySelector('button[type=submit]');
      const originalText = btn.textContent;
      btn.disabled = true;
      btn.textContent = 'در حال ارسال...';

      try {
        const res = await fetch(form.action || window.location.href, {
          method: 'POST',
          headers: { 'X-Requested-With': 'XMLHttpRequest' },
          body: new FormData(form),
        });
        const data = await res.json();

        showFormMessages(data.errors || [], data.success || '');
        if (data.success) form.reset();
      } catch (e) {
        showFormMessages(['خطا در ارتباط با سرور.'], '');
      } finally {
        btn.disabled = false;
        btn.textContent = originalText;
      }
    });

    function showFormMessages(errors, success) {
      form.querySelectorAll('.msg').forEach((n) => n.remove());
      const wrap = document.createElement('div');
      errors.forEach((m) => {
        const d = document.createElement('div');
        d.className = 'msg error';
        d.textContent = m;
        wrap.appendChild(d);
      });
      if (success) {
        const d = document.createElement('div');
        d.className = 'msg success';
        d.textContent = success;
        wrap.appendChild(d);
      }
      form.prepend(wrap);
    }
  }
})();