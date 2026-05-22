/**
 * Admin - Donations Management
 */
$(document).ready(function() {
    const getUnitLabel = function (unit) {
        const labels = window.unitLabels || {};
        return labels[unit] || unit || '';
    };
    const renderStars = function (rating) {
        var value = Math.max(0, Math.min(5, Number(rating) || 0));
        var html = '';
        for (var i = 1; i <= 5; i++) {
            html += '<i class="fas fa-star ' + (i <= value ? 'text-warning' : 'text-muted') + '"></i>';
        }
        return html;
    };

    const params = new URLSearchParams(window.location.search);
    const initialStatus = params.get('status') || '';
    if (initialStatus) {
        $('#donationStatusFilter').val(initialStatus);
    }

    let donationsUrl = window.routes.donationsDatatable + (initialStatus ? '?status=' + encodeURIComponent(initialStatus) : '');
    let donationsTable = initDataTable('donations-table', donationsUrl, [
        { data: 'id', name: 'id' },
        { data: 'donor_name', name: 'donor.name' },
        { data: 'items_summary', name: 'food_type', orderable: false },
        { data: 'quantities_summary', name: 'quantity', orderable: false },
        { data: 'status', name: 'status' },
        { data: 'pickup_time', name: 'pickup_time' },
        { data: 'created_at', name: 'created_at' }
    ]);

    $('#donationStatusFilter').on('change', function() {
        let val = $(this).val();
        donationsTable.ajax.url(window.routes.donationsDatatable + (val ? '?status=' + encodeURIComponent(val) : '')).load();
    });

    $('#donations-table tbody').on('click', 'tr', function() {
        let rowData = donationsTable.row(this).data();
        if (!rowData || !rowData.id) {
            return;
        }

        let donationId = rowData.id;
        $.get(window.routes.donationsShow.replace(':id', donationId), function(data) {
            let modal = $('#viewDonationModal');
            var itemsHtml = '';
            if (data.items && data.items.length > 0) {
                itemsHtml = '<ul class="mb-0 ps-3">';
                data.items.forEach(function(item) {
                    itemsHtml += '<li>' + item.food_type + ' — <strong>' + item.quantity + ' ' + getUnitLabel(item.quantity_unit) + '</strong>';
                    if (item.description) {
                        itemsHtml += ' <small class="text-muted">(' + item.description + ')</small>';
                    }
                    itemsHtml += '</li>';
                });
                itemsHtml += '</ul>';
            } else {
                itemsHtml = '<span class="text-muted">—</span>';
            }
            modal.find('#donation-items-list').html(itemsHtml);
            modal.find('#donation-status-badge').html(getStatusBadge(data.status));
            var loc = '';
            if (data.city_relation) loc = data.city_relation.name;
            if (data.town) loc += (loc ? ' / ' : '') + data.town.name;
            modal.find('#donation-city-town').text(loc || '-');
            var fc = '';
            if (data.food_category) {
                fc = (data.food_category.parent ? data.food_category.parent.name + ' — ' : '') + data.food_category.name;
            }
            modal.find('#donation-food-category').text(fc || '-');
            modal.find('#donation-pickup-address').text(data.pickup_address || '-');
            modal.find('#donation-pickup-time').text(data.pickup_time || '-');
            modal.find('#donation-expiry-time').text(data.expiry_time || '-');
            modal.find('#donation-created-at').text(data.created_at || '-');
            modal.find('#donation-description').text(data.description || '-');
            modal.find('#donation-notes').text(data.notes || '-');
            modal.find('#donation-donor').text(data.donor ? data.donor.name : '-');

            if (data.image) {
                modal.find('#donation-image-row').show();
                modal.find('#donation-image').attr('src', '/storage/' + data.image);
            } else {
                modal.find('#donation-image-row').hide();
            }

            if (data.donation_ratings && data.donation_ratings.length > 0) {
                var donationRatingsHtml = '';
                data.donation_ratings.forEach(function (rating) {
                    var rateableName = rating.rateable ? rating.rateable.name : 'User';
                    donationRatingsHtml += '<div class="border rounded p-2 mb-2 bg-light">';
                    donationRatingsHtml += '<div class="d-flex justify-content-between align-items-center">';
                    donationRatingsHtml += '<span><strong>' + (rating.rater ? rating.rater.name : 'Unknown') + '</strong> → ' + rateableName + '</span>';
                    donationRatingsHtml += '<span>' + renderStars(rating.rating) + '</span>';
                    donationRatingsHtml += '</div>';
                    if (rating.comment) {
                        donationRatingsHtml += '<small class="text-muted d-block mt-1">' + rating.comment + '</small>';
                    }
                    donationRatingsHtml += '</div>';
                });
                modal.find('#donation-ratings-list').html(donationRatingsHtml);
                modal.find('#donation-ratings-row').show();
            } else {
                modal.find('#donation-ratings-row').hide();
            }

            if (data.donor_ratings && data.donor_ratings.length > 0) {
                var donorRatingsHtml = '';
                data.donor_ratings.forEach(function (rating) {
                    donorRatingsHtml += '<div class="border rounded p-2 mb-2 bg-light">';
                    donorRatingsHtml += '<div class="d-flex justify-content-between align-items-center">';
                    donorRatingsHtml += '<span><strong>' + (rating.rater ? rating.rater.name : 'Unknown') + '</strong></span>';
                    donorRatingsHtml += '<span>' + renderStars(rating.rating) + '</span>';
                    donorRatingsHtml += '</div>';
                    if (rating.comment) {
                        donorRatingsHtml += '<small class="text-muted d-block mt-1">' + rating.comment + '</small>';
                    }
                    donorRatingsHtml += '</div>';
                });
                modal.find('#donor-ratings-list').html(donorRatingsHtml);
                modal.find('#donor-ratings-row').show();
            } else {
                modal.find('#donor-ratings-row').hide();
            }

            modal.modal('show');
        });
    });
});

function getStatusBadge(status) {
    const badges = {
        'pending': '<span class="badge bg-warning">Pending</span>',
        'in_progress': '<span class="badge bg-info">In Progress</span>',
        'completed': '<span class="badge bg-success">Completed</span>'
    };
    return badges[status] || '<span class="badge bg-secondary">' + status + '</span>';
}
