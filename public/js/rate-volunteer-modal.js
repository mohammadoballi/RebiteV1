/**
 * Rate donor / volunteer modal (Bootstrap 5)
 */
(function ($) {
    'use strict';

    var submitLabel = window.rateModalLabels?.submit || 'Submit Rating';

    function showRatingError(message) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'error',
                title: window.rateModalLabels?.error || 'Error',
                html: message,
                confirmButtonColor: '#28a745'
            });
        } else {
            alert(String(message).replace(/<br\s*\/?>/gi, '\n'));
        }
    }

    function updateStars(val) {
        document.querySelectorAll('#star-rating .fa-star').forEach(function (star) {
            var sv = parseInt(star.getAttribute('data-value'), 10);
            if (sv <= val) {
                star.classList.remove('text-muted');
                star.classList.add('text-warning');
            } else {
                star.classList.remove('text-warning');
                star.classList.add('text-muted');
            }
        });
    }

    function showRateModal() {
        var el = document.getElementById('rateVolunteerModal');
        if (!el || typeof bootstrap === 'undefined') {
            return;
        }
        bootstrap.Modal.getOrCreateInstance(el).show();
    }

    function hideRateModal() {
        var el = document.getElementById('rateVolunteerModal');
        if (!el || typeof bootstrap === 'undefined') {
            return;
        }
        var instance = bootstrap.Modal.getInstance(el);
        if (instance) {
            instance.hide();
        }
    }

    document.addEventListener('mouseover', function (e) {
        var star = e.target.closest('#star-rating .fa-star');
        if (star) {
            updateStars(parseInt(star.getAttribute('data-value'), 10));
        }
    });

    document.addEventListener('mouseout', function (e) {
        var star = e.target.closest('#star-rating .fa-star');
        if (star) {
            var container = document.getElementById('star-rating');
            var related = e.relatedTarget;
            if (container && !container.contains(related)) {
                var selected = parseInt(document.getElementById('rate-value').value, 10) || 0;
                updateStars(selected);
            }
        }
    });

    document.addEventListener('click', function (e) {
        var star = e.target.closest('#star-rating .fa-star');
        if (star) {
            var val = parseInt(star.getAttribute('data-value'), 10);
            document.getElementById('rate-value').value = val;
            updateStars(val);
        }
    });

    $(document).on('click', '#btn-submit-rating', function () {
        var rating = parseInt($('#rate-value').val(), 10);
        if (rating < 1) {
            showRatingError(window.rateModalLabels?.selectRating || 'Please select a rating');
            return;
        }

        var btn = $(this);
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>...');

        var donationId = $('#rate-donation-id').val();
        if (!donationId) {
            showRatingError(window.rateModalLabels?.missingDonation || 'Missing donation context.');
            btn.prop('disabled', false).html('<i class="fas fa-paper-plane me-1"></i> ' + submitLabel);
            return;
        }

        $.ajax({
            url: window.rateModalConfig?.storeUrl,
            type: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                rateable_id: $('#rate-volunteer-id').val(),
                rateable_type: 'user',
                rating: rating,
                comment: $('#rate-comment').val(),
                donation_id: donationId
            },
            success: function (res) {
                if (typeof showSuccess === 'function') {
                    showSuccess(res.message || 'Rating submitted!');
                }
                hideRateModal();
                if (typeof window.onRatingSubmitted === 'function' && res.rateable_id) {
                    window.onRatingSubmitted(res.rateable_id);
                }
            },
            error: function (xhr) {
                var msg = xhr.responseJSON?.message;
                if (xhr.responseJSON?.errors) {
                    msg = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                }
                showRatingError(msg || (window.rateModalLabels?.failed || 'Failed to submit rating'));
            },
            complete: function () {
                btn.prop('disabled', false).html('<i class="fas fa-paper-plane me-1"></i> ' + submitLabel);
            }
        });
    });

    window.openRateModal = function (volunteerId, volunteerName, role, donationId) {
        document.getElementById('rate-volunteer-id').value = volunteerId;
        document.getElementById('rate-donation-id').value = donationId || '';
        document.getElementById('rate-volunteer-name').textContent = volunteerName;
        document.getElementById('rate-value').value = '0';
        document.getElementById('rate-comment').value = '';
        updateStars(0);

        $('#btn-submit-rating').prop('disabled', false).html('<i class="fas fa-paper-plane me-1"></i> ' + submitLabel);

        var title = role === 'donor'
            ? (window.rateModalLabels?.rateDonor || 'Rate Donor')
            : (window.rateModalLabels?.rateVolunteer || 'Rate Volunteer');
        document.getElementById('rate-modal-title').innerHTML = '<i class="fas fa-star me-2"></i>' + title;

        showRateModal();
    };
})(jQuery);
