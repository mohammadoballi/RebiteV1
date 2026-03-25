/**
 * Rebite – shared utilities
 */
(function ($) {
    'use strict';

    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });

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
