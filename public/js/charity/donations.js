/**
 * Charity - Browse & Request Donations
 */
$(document).ready(function() {
    // Available donations table
    if ($('#available-donations-table').length) {
        let availableTable = initDataTable('available-donations-table', window.routes.donationsDatatable, [
            { data: 'id', name: 'id' },
            { data: 'donor_name', name: 'donor.name' },
            { data: 'food_type', name: 'food_type' },
            { data: 'quantity', name: 'quantity' },
            { data: 'pickup_address', name: 'pickup_address' },
            { data: 'pickup_time', name: 'pickup_time' },
            { data: 'actions', name: 'actions', orderable: false, searchable: false }
        ]);
    }

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

    // View donation details
    $(document).on('click', '.btn-view-donation', function() {
        let donationId = $(this).data('id');
        $.get(window.routes.donationsShow.replace(':id', donationId), function(data) {
            let modal = $('#viewDonationModal');
            modal.find('#view-food-type').text(data.food_type);
            modal.find('#view-quantity').text(data.quantity + ' ' + data.quantity_unit);
            modal.find('#view-address').text(data.pickup_address);
            modal.find('#view-pickup-time').text(data.pickup_time);
            modal.find('#view-description').text(data.description || '-');
            modal.find('#view-donor').text(data.donor ? data.donor.name : '-');
            if (data.image) {
                modal.find('#view-image').html('<img src="/' + data.image + '" class="img-fluid rounded" style="max-height:200px">');
            } else {
                modal.find('#view-image').html('-');
            }
            modal.find('#request-donation-id').val(data.id);
            modal.modal('show');
        });
    });

    // Request donation
    $(document).on('click', '#btn-request-donation', function() {
        let donationId = $('#request-donation-id').val();
        let message = $('#request-message').val();
        
        confirmAction(
            window.routes.donationsRequest.replace(':id', donationId),
            'POST',
            { message: message },
            'Are you sure you want to request this donation?',
            'available-donations-table'
        );
        $('#viewDonationModal').modal('hide');
    });
});
