/**
 * Register page: enforce complex password rules (matches server-side RegisterRequest).
 */
(function ($) {
    'use strict';

    function messages() {
        return window.RebiteRegisterPassword || {};
    }

    function text(key, fallback) {
        var m = messages();
        var v = m[key];
        return v !== undefined && v !== '' ? v : fallback;
    }

    function checkPassword(password) {
        return {
            len: password.length >= 8,
            upper: /[A-Z]/.test(password),
            num: /\d/.test(password),
        };
    }

    function isComplete(rules) {
        return rules.len && rules.upper && rules.num;
    }

    function setRuleVisual($item, satisfied) {
        var $icon = $item.find('i').first();
        if (!$icon.length) {
            return;
        }
        $icon.removeClass('fa-circle fa-check-circle fa-times-circle text-muted text-success text-danger');
        if (satisfied) {
            $icon.addClass('fa-check-circle text-success');
            $item.removeClass('text-muted').addClass('text-success');
        } else {
            $icon.addClass('fa-circle text-muted');
            $item.removeClass('text-success').addClass('text-muted');
        }
    }

    $(function () {
        var $form = $('#registerForm');
        var $pwd = $('#password');
        var $confirm = $('#password_confirmation');
        if (!$form.length || !$pwd.length || !$confirm.length) {
            return;
        }

        var $items = $('[data-password-rule]');

        function syncPasswordFieldState() {
            var value = $pwd.val();
            var rules = checkPassword(value);
            $items.each(function () {
                var $el = $(this);
                var key = $el.data('password-rule');
                if (key === 'len') {
                    setRuleVisual($el, rules.len);
                } else if (key === 'upper') {
                    setRuleVisual($el, rules.upper);
                } else if (key === 'num') {
                    setRuleVisual($el, rules.num);
                }
            });

            if (value.length === 0) {
                $pwd.removeClass('is-invalid');
                return rules;
            }
            $pwd.toggleClass('is-invalid', !isComplete(rules));
            return rules;
        }

        function syncConfirmationState() {
            var p = $pwd.val();
            var c = $confirm.val();
            if (!c.length) {
                $confirm.removeClass('is-invalid');
                return true;
            }
            var ok = p === c;
            $confirm.toggleClass('is-invalid', !ok);
            return ok;
        }

        $pwd.on('input change blur', syncPasswordFieldState);
        $confirm.on('input change blur', syncConfirmationState);

        $form.on('submit', function (e) {
            var rules = checkPassword($pwd.val());
            if (!isComplete(rules)) {
                e.preventDefault();
                syncPasswordFieldState();
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'error',
                        title: text('titleError', 'Error'),
                        text: text('passwordComplexity', ''),
                        confirmButtonColor: '#3a7d44',
                    });
                }
                $pwd.focus();
                return false;
            }
            if ($pwd.val() !== $confirm.val()) {
                e.preventDefault();
                syncConfirmationState();
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'error',
                        title: text('titleError', 'Error'),
                        text: text('passwordMismatch', ''),
                        confirmButtonColor: '#3a7d44',
                    });
                }
                $confirm.focus();
                return false;
            }
        });

        syncPasswordFieldState();
        syncConfirmationState();
    });
})(jQuery);
