/**
 * Rebite – shared utilities
 */
(function ($) {
    'use strict';

    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });

    function pad2(value) {
        return String(value).padStart(2, '0');
    }

    function parseDateValue(value) {
        if (value === null || value === undefined || value === '') {
            return null;
        }
        if (value instanceof Date) {
            return isNaN(value.getTime()) ? null : value;
        }
        if (typeof value === 'number') {
            var numericDate = new Date(value);
            return isNaN(numericDate.getTime()) ? null : numericDate;
        }
        if (typeof value !== 'string') {
            return null;
        }

        var normalized = value.trim();
        if (!normalized) {
            return null;
        }

        // Normalize common backend datetime format: YYYY-MM-DD HH:mm:ss
        var candidate = normalized.indexOf(' ') > -1 && normalized.indexOf('T') === -1
            ? normalized.replace(' ', 'T')
            : normalized;

        var parsed = new Date(candidate);
        if (isNaN(parsed.getTime())) {
            return null;
        }
        return parsed;
    }

    function formatAsGlobalDateTime(value) {
        var parsed = parseDateValue(value);
        if (!parsed) {
            return value;
        }

        var day = pad2(parsed.getDate());
        var month = pad2(parsed.getMonth() + 1);
        var year = parsed.getFullYear();
        var hours24 = parsed.getHours();
        var hours12 = hours24 % 12 || 12;
        var minutes = pad2(parsed.getMinutes());
        var seconds = pad2(parsed.getSeconds());
        var meridiem = hours24 >= 12 ? 'PM' : 'AM';

        // Requested format: dd/mm/yyyy- hh:mm:ss:AM/PM
        return '' + day + '/' + month + '/' + year + '- ' + pad2(hours12) + ':' + minutes + ':' + seconds + ':' + meridiem;
    }

    function shouldFormatDateColumn(settings, meta) {
        var column = settings && settings.aoColumns ? settings.aoColumns[meta.col] : null;
        if (!column) {
            return false;
        }

        var dataKey = column.data;
        var nameKey = column.name;
        var key = '';
        if (typeof dataKey === 'string') {
            key += dataKey.toLowerCase() + ' ';
        }
        if (typeof nameKey === 'string') {
            key += nameKey.toLowerCase();
        }

        return /(_at|date|time|created|updated|pickup|expiry|delivered|paid)/.test(key);
    }

    /**
     * Initialize a DataTable with AJAX source.
     */
    window.initDataTable = function (tableId, ajaxUrl, columns, extraOpts) {
        var defaults = {
            processing: true,
            serverSide: true,
            responsive: true,
            ajax: ajaxUrl,
            columns: columns,
            order: [[0, 'desc']],
            columnDefs: [{
                targets: '_all',
                render: function (data, type, row, meta) {
                    if (type !== 'display' && type !== 'filter') {
                        return data;
                    }
                    var settings = meta && meta.settings ? meta.settings : null;
                    if (!shouldFormatDateColumn(settings, meta)) {
                        return data;
                    }
                    return formatAsGlobalDateTime(data);
                }
            }],
            language: {
                emptyTable: 'No data available',
                processing: '<div class="spinner-border spinner-border-sm text-success" role="status"><span class="visually-hidden">Loading...</span></div>'
            }
        };
        return $('#' + tableId).DataTable($.extend(true, defaults, extraOpts || {}));
    };

    /**
     * SweetAlert2 confirmation dialog then AJAX submit.
     */
    window.confirmAction = function (opts) {
        var defaults = {
            title: 'Are you sure?',
            text: '',
            icon: 'warning',
            confirmText: 'Yes',
            cancelText: 'Cancel',
            method: 'POST',
            url: '',
            data: {},
            onSuccess: null
        };
        var o = $.extend({}, defaults, opts);

        Swal.fire({
            title: o.title,
            text: o.text,
            icon: o.icon,
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: o.confirmText,
            cancelButtonText: o.cancelText
        }).then(function (result) {
            if (!result.isConfirmed) return;

            $.ajax({
                url: o.url,
                type: o.method,
                data: o.data,
                success: function (res) {
                    Swal.fire({
                        icon: 'success',
                        title: res.message || 'Done!',
                        confirmButtonColor: '#28a745'
                    });
                    if (typeof o.onSuccess === 'function') o.onSuccess(res);
                },
                error: function (xhr) {
                    var msg = xhr.responseJSON?.message || 'Something went wrong.';
                    Swal.fire({ icon: 'error', title: 'Error', text: msg, confirmButtonColor: '#28a745' });
                }
            });
        });
    };

    /**
     * Confirm-and-delete shorthand. Sends DELETE then reloads the given table.
     */
    window.confirmDelete = function (url, tableId) {
        confirmAction({
            title: 'Are you sure?',
            text: 'This action cannot be undone.',
            icon: 'warning',
            method: 'DELETE',
            url: url,
            onSuccess: function () {
                if (tableId && $.fn.DataTable.isDataTable('#' + tableId)) {
                    $('#' + tableId).DataTable().ajax.reload();
                }
            }
        });
    };

    /**
     * Show a success toast (top-end).
     */
    window.showToast = function (message, icon) {
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: icon || 'success',
            title: message,
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });
    };

    window.showSuccess = function (message) {
        showToast(message, 'success');
    };

    window.showError = function (message) {
        showToast(message, 'error');
    };

    /**
     * Display Laravel validation errors on a form.
     * Clears previous errors, then adds .is-invalid and feedback divs.
     */
    window.displayFormErrors = function (formSelector, errors) {
        var $form = $(formSelector);
        $form.find('.is-invalid').removeClass('is-invalid');
        $form.find('.invalid-feedback').remove();

        $.each(errors, function (field, messages) {
            var $input = $form.find('[name="' + field + '"]');
            $input.addClass('is-invalid');
            $input.after('<div class="invalid-feedback">' + messages[0] + '</div>');
        });
    };

    /**
     * Clear all validation errors from a form.
     */
    window.clearFormErrors = function (formSelector) {
        var $form = $(formSelector);
        $form.find('.is-invalid').removeClass('is-invalid');
        $form.find('.invalid-feedback').remove();
    };

})(jQuery);
