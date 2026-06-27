/*
 * Cocktails module — front-end behaviour
 * @copyright 2000-2026 XOOPS Project (https://xoops.org)
 * @license   GNU GPL 2.0 or later
 *
 * Vanilla JS, no dependencies. Powers:
 *   1. the dynamic measured-ingredient editor on the recipe form,
 *   2. the AJAX star-rating widget,
 *   3. the AJAX favourite toggle.
 */
(function () {
    'use strict';

    function ready(fn) {
        if (document.readyState !== 'loading') { fn(); }
        else { document.addEventListener('DOMContentLoaded', fn); }
    }

    function xoopsToken() {
        var el = document.querySelector('input[name="XOOPS_TOKEN_REQUEST"]');
        return el ? el.value : '';
    }

    function postForm(url, data) {
        var body = Object.keys(data).map(function (k) {
            return encodeURIComponent(k) + '=' + encodeURIComponent(data[k]);
        }).join('&');
        return fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin',
            body: body
        }).then(function (r) { return r.json(); });
    }

    /* ---------------------------------------------------------------
     * 1. Measured-ingredient editor
     * ------------------------------------------------------------- */
    function initIngredientEditor() {
        var editor = document.getElementById('cocktails-ing-editor');
        if (!editor) { return; }
        var body = editor.querySelector('.cocktails-ing-body');
        var tpl = document.getElementById('cocktails-ing-template');
        var addBtn = document.getElementById('cocktails-ing-add');

        if (addBtn && body && tpl) {
            addBtn.addEventListener('click', function () {
                var row;
                if ('content' in tpl) {
                    row = tpl.content.firstElementChild.cloneNode(true);
                } else {
                    var tmp = document.createElement('tbody');
                    tmp.innerHTML = tpl.innerHTML.trim();
                    row = tmp.firstElementChild;
                }
                body.appendChild(row);
            });
        }

        editor.addEventListener('click', function (e) {
            var btn = e.target.closest('.cocktails-ing-remove');
            if (!btn) { return; }
            var rows = body.querySelectorAll('.cocktails-ing-row');
            if (rows.length > 1) {
                btn.closest('.cocktails-ing-row').remove();
            } else {
                // keep one empty row: clear its inputs instead of removing
                btn.closest('.cocktails-ing-row').querySelectorAll('input').forEach(function (i) {
                    if (i.type === 'checkbox') { i.checked = false; } else { i.value = ''; }
                });
            }
        });
    }

    /* ---------------------------------------------------------------
     * 2. AJAX star rating
     * ------------------------------------------------------------- */
    function paint(stars, value) {
        stars.forEach(function (s) {
            var v = parseInt(s.getAttribute('data-stars'), 10);
            s.classList.toggle('is-on', v <= value);
        });
    }

    function initRating() {
        var widget = document.querySelector('.cocktails-rate');
        if (!widget) { return; }
        var url = widget.getAttribute('data-rate-url');
        var id = widget.getAttribute('data-recipe-id');
        var current = parseInt(widget.getAttribute('data-user-stars'), 10) || 0;
        var stars = Array.prototype.slice.call(widget.querySelectorAll('.cocktails-rate-star'));

        paint(stars, current);

        stars.forEach(function (star) {
            star.addEventListener('mouseenter', function () {
                paint(stars, parseInt(star.getAttribute('data-stars'), 10));
            });
            star.addEventListener('click', function () {
                var value = parseInt(star.getAttribute('data-stars'), 10);
                postForm(url, { op: 'rate', id: id, stars: value, ajax: 1, XOOPS_TOKEN_REQUEST: xoopsToken() })
                    .then(function (res) {
                        if (res && res.ok) {
                            current = value;
                            var avgEl = document.getElementById('cocktails-avg-num');
                            var cntEl = document.getElementById('cocktails-rate-count');
                            if (avgEl) { avgEl.textContent = res.avg; }
                            if (cntEl) { cntEl.textContent = res.count; }
                        } else if (res && res.error) {
                            alert(res.error);
                        }
                    })
                    .catch(function () {});
            });
        });

        widget.addEventListener('mouseleave', function () { paint(stars, current); });
    }

    /* ---------------------------------------------------------------
     * 3. AJAX favourite toggle
     * ------------------------------------------------------------- */
    function initFavorite() {
        var btn = document.querySelector('.cocktails-fav');
        if (!btn) { return; }
        var url = btn.getAttribute('data-fav-url');
        var id = btn.getAttribute('data-recipe-id');
        var labelEl = btn.querySelector('.cocktails-fav-label');
        var addLabel = btn.getAttribute('data-label-add') || (labelEl ? labelEl.textContent : '');
        var remLabel = btn.getAttribute('data-label-remove') || (labelEl ? labelEl.textContent : '');

        btn.addEventListener('click', function () {
            postForm(url, { op: 'favorite', id: id, ajax: 1, XOOPS_TOKEN_REQUEST: xoopsToken() })
                .then(function (res) {
                    if (res && res.ok) {
                        btn.classList.toggle('is-active', !!res.state);
                        if (labelEl) { labelEl.textContent = res.state ? remLabel : addLabel; }
                    } else if (res && res.error) {
                        alert(res.error);
                    }
                })
                .catch(function () {});
        });
    }

    ready(function () {
        initIngredientEditor();
        initRating();
        initFavorite();
    });
})();
