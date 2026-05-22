/**
 * Charity - Browse & Request Donations + My Requests
 */
let currentDonationId = null;

function getUnitLabel(unit) {
    const labels = window.unitLabels || {};
    return labels[unit] || unit || '';
}

function buildRateRow(userId, userName, role, ratedUserIds, donationId) {
    const isRated = ratedUserIds.some(function (id) { return String(id) === String(userId); });
    const roleBadge = role === 'donor'
        ? '<span class="badge bg-info ms-1">Donor</span>'
        : '<span class="badge bg-primary ms-1">Volunteer</span>';
    const icon = role === 'donor'
        ? '<i class="fas fa-hand-holding-heart text-success me-1"></i>'
        : '<i class="fas fa-user-circle text-success me-1"></i>';

    let html = '<div class="d-flex justify-content-between align-items-center border-bottom py-2" data-rateable-id="' + userId + '">';
    html += '<div>' + icon + ' <strong>' + userName + '</strong> ' + roleBadge + '</div>';

    if (isRated) {
        html += '<span class="badge bg-success"><i class="fas fa-check me-1"></i> Rated</span>';
    } else {
        html += '<button type="button" class="btn btn-sm btn-outline-warning btn-rate-user" data-user-id="' + userId + '" data-user-name="' + userName.replace(/"/g, '&quot;') + '" data-role="' + role + '" data-donation-id="' + (donationId || '') + '"><i class="fas fa-star me-1"></i> Rate</button>';
    }

    html += '</div>';
    return html;
}

$(document).ready(function() {
    // My requests table
    if ($('#my-requests-table').length) {
        initDataTable('my-requests-table', window.routes.myRequestsDatatable, [
            { data: 'id', name: 'id' },
            { data: 'donation_food_type', name: 'donation.food_type' },
            { data: 'donation_quantity', name: 'donation.quantity' },
            { data: 'display_status', name: 'status', orderable: false },
            { data: 'created_at', name: 'created_at' },
            { data: 'actions', name: 'actions', orderable: false, searchable: false }
        ]);
    }

    $(document).on('click', '.btn-rate-user', function() {
        const userId = $(this).data('user-id');
        const userName = $(this).data('user-name');
        const role = $(this).data('role');
        const donationId = $(this).data('donation-id') || currentDonationId;
        if (typeof openRateModal === 'function') {
            openRateModal(userId, userName, role, donationId);
        }
    });

    // View approved request - show donation with rate buttons
    $(document).on('click', '.btn-view-request', function() {
        let donationId = $(this).data('donation-id');
        if (!window.routes.donationsShow) return;

        currentDonationId = donationId;

        $.get(window.routes.donationsShow.replace(':id', donationId), function(data) {
            let modal = $('#viewRequestModal');
            const ratedUserIds = data.rated_user_ids || [];

            let html = '<div class="row g-3">';
            html += '<div class="col-md-6"><strong>Food:</strong> ' + (data.food_type || '-') + '</div>';
            html += '<div class="col-md-6"><strong>Status:</strong> ' + (data.status || '-') + '</div>';

            if (data.items && data.items.length > 0) {
                html += '<div class="col-12"><strong>Items:</strong><ul class="mb-0">';
                data.items.forEach(function(i) {
                    html += '<li>' + i.food_type + ' — ' + i.quantity + ' ' + getUnitLabel(i.quantity_unit) + '</li>';
                });
                html += '</ul></div>';
            }

            html += '<div class="col-12"><strong>Address:</strong> ' + (data.pickup_address || '-') + '</div>';
            html += '</div>';
            modal.find('#req-donation-details').html(html);

            let rateHtml = '';
            if (data.donor) {
                rateHtml += buildRateRow(data.donor.id, data.donor.name, 'donor', ratedUserIds, donationId);
            }
            if (data.assignments && data.assignments.length > 0) {
                data.assignments.forEach(function(a) {
                    if (a.volunteer) {
                        rateHtml += buildRateRow(a.volunteer.id, a.volunteer.name, 'volunteer', ratedUserIds, donationId);
                    }
                });
            }

            if (rateHtml) {
                modal.find('#req-rateable-list').html(rateHtml);
                modal.find('#req-rate-section').show();
            } else {
                modal.find('#req-rate-section').hide();
            }

            var viewEl = document.getElementById('viewRequestModal');
            if (viewEl && typeof bootstrap !== 'undefined') {
                bootstrap.Modal.getOrCreateInstance(viewEl).show();
            }
        });
    });
});

window.onRatingSubmitted = function(rateableId) {
    const row = $('#req-rateable-list [data-rateable-id="' + rateableId + '"]');
    row.find('.btn-rate-user').replaceWith('<span class="badge bg-success"><i class="fas fa-check me-1"></i> Rated</span>');

    if ($('#my-requests-table').length && $.fn.DataTable.isDataTable('#my-requests-table')) {
        $('#my-requests-table').DataTable().ajax.reload(null, false);
    }
};
