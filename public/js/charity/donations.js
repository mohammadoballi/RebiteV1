/**
 * Charity - Browse & Request Donations + My Requests
 */
$(document).ready(function() {
    // My requests table
    if ($('#my-requests-table').length) {
        let requestsTable = initDataTable('my-requests-table', window.routes.myRequestsDatatable, [
            { data: 'id', name: 'id' },
            { data: 'donation_food_type', name: 'donation.food_type' },
            { data: 'donation_quantity', name: 'donation.quantity' },
            { data: 'status', name: 'status' },
            { data: 'created_at', name: 'created_at' },
            { data: 'actions', name: 'actions', orderable: false, searchable: false }
        ]);
    }

    // View approved request - show donation with rate buttons
    $(document).on('click', '.btn-view-request', function() {
        let donationId = $(this).data('donation-id');
        if (!window.routes.donationsShow) return;

        $.get(window.routes.donationsShow.replace(':id', donationId), function(data) {
            let modal = $('#viewRequestModal');

            let html = '<div class="row g-3">';
            html += '<div class="col-md-6"><strong>Food:</strong> ' + (data.food_type || '-') + '</div>';
            html += '<div class="col-md-6"><strong>Status:</strong> ' + data.status + '</div>';

            if (data.items && data.items.length > 0) {
                html += '<div class="col-12"><strong>Items:</strong><ul class="mb-0">';
                data.items.forEach(function(i) {
                    html += '<li>' + i.food_type + ' — ' + i.quantity + ' ' + i.quantity_unit + '</li>';
                });
                html += '</ul></div>';
            }

            html += '<div class="col-12"><strong>Address:</strong> ' + (data.pickup_address || '-') + '</div>';
            html += '</div>';
            modal.find('#req-donation-details').html(html);

            // Rate section: show donor and volunteers for completed donations
            let rateHtml = '';
            if (data.donor) {
                rateHtml += '<div class="d-flex justify-content-between align-items-center border-bottom py-2">';
                rateHtml += '<div><i class="fas fa-hand-holding-heart text-success me-1"></i> <strong>' + data.donor.name + '</strong> <span class="badge bg-info ms-1">Donor</span></div>';
                rateHtml += '<button class="btn btn-sm btn-outline-warning" onclick="openRateModal(' + data.donor.id + ', \'' + data.donor.name.replace(/'/g, "\\'") + '\', \'donor\')"><i class="fas fa-star me-1"></i> Rate</button>';
                rateHtml += '</div>';
            }
            if (data.assignments && data.assignments.length > 0) {
                data.assignments.forEach(function(a) {
                    if (a.volunteer) {
                        rateHtml += '<div class="d-flex justify-content-between align-items-center border-bottom py-2">';
                        rateHtml += '<div><i class="fas fa-user-circle text-success me-1"></i> <strong>' + a.volunteer.name + '</strong> <span class="badge bg-primary ms-1">Volunteer</span></div>';
                        rateHtml += '<button class="btn btn-sm btn-outline-warning" onclick="openRateModal(' + a.volunteer.id + ', \'' + a.volunteer.name.replace(/'/g, "\\'") + '\', \'volunteer\')"><i class="fas fa-star me-1"></i> Rate</button>';
                        rateHtml += '</div>';
                    }
                });
            }
            if (rateHtml) {
                modal.find('#req-rateable-list').html(rateHtml);
                modal.find('#req-rate-section').show();
            } else {
                modal.find('#req-rate-section').hide();
            }

            modal.modal('show');
        });
    });
});
