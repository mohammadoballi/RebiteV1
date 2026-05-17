/**
 * Admin - Donations Management
 */
$(document).ready(function() {
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
        { data: 'created_at', name: 'created_at' },
        { data: 'actions', name: 'actions', orderable: false, searchable: false }
    ]);

    $('#donationStatusFilter').on('change', function() {
        let val = $(this).val();
        donationsTable.ajax.url(window.routes.donationsDatatable + (val ? '?status=' + encodeURIComponent(val) : '')).load();
    });

    // View donation details
    $(document).on('click', '.btn-view-donation', function() {
        let donationId = $(this).data('id');
        $.get(window.routes.donationsShow.replace(':id', donationId), function(data) {
            let modal = $('#viewDonationModal');
            var itemsHtml = '';
            if (data.items && data.items.length > 0) {
                itemsHtml = '<ul class="mb-0 ps-3">';
                data.items.forEach(function(item) {
                    itemsHtml += '<li>' + item.food_type + ' — <strong>' + item.quantity + ' ' + item.quantity_unit + '</strong>';
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
            modal.find('#donation-food-type').text(data.items_summary || data.food_type || '—');
            modal.find('#donation-quantity').text(data.quantities_summary || (data.quantity + (data.quantity_unit && data.quantity_unit !== 'mixed' ? ' ' + data.quantity_unit : '')) || '—');
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

            // Status update dropdown
            let statusSelect = modal.find('#donation-status-select');
            statusSelect.val(data.status);
            modal.find('#update-donation-id').val(data.id);

            let isPublished = !!data.admin_approved_at && !data.accepted_charity_id;
            modal.find('#btn-approve-donation').toggleClass('d-none', isPublished);
            modal.find('#donation-approved-badge').toggleClass('d-none', !isPublished);

            modal.modal('show');
        });
    });

    $(document).on('click', '#btn-approve-donation', function() {
        let donationId = $('#update-donation-id').val();
        let btn = $(this);
        btn.prop('disabled', true);

        $.post(window.routes.donationsApprove.replace(':id', donationId), function(response) {
            showSuccess(response.message || 'Donation approved');
            $('#btn-approve-donation').addClass('d-none');
            $('#donation-approved-badge').removeClass('d-none');
            $('#donation-status-select').val('pending');
            $('#donation-status-badge').html(getStatusBadge('pending'));
            donationsTable.ajax.reload();
        }).fail(function(xhr) {
            showError(xhr.responseJSON?.message || 'Failed to approve donation');
        }).always(function() {
            btn.prop('disabled', false);
        });
    });

    // Update donation status
    $(document).on('click', '#btn-update-status', function() {
        let donationId = $('#update-donation-id').val();
        let newStatus = $('#donation-status-select').val();
        $.ajax({
            url: window.routes.donationsStatus.replace(':id', donationId),
            type: 'PUT',
            data: { status: newStatus },
            success: function(response) {
                showSuccess(response.message || 'Status updated');
                $('#viewDonationModal').modal('hide');
                donationsTable.ajax.reload();
            },
            error: function(xhr) {
                showError(xhr.responseJSON?.message || 'Failed to update status');
            }
        });
    });

    // Delete donation
    $(document).on('click', '.btn-delete-donation', function() {
        let donationId = $(this).data('id');
        confirmDelete(window.routes.donationsDestroy.replace(':id', donationId), 'donations-table');
    });
});

function getStatusBadge(status) {
    const badges = {
        'pending': '<span class="badge bg-warning">Pending</span>',
        'accepted': '<span class="badge bg-info">Accepted</span>',
        'assigned': '<span class="badge bg-primary">Assigned</span>',
        'in_transit': '<span class="badge bg-secondary">In Transit</span>',
        'delivered': '<span class="badge bg-success">Delivered</span>',
        'completed': '<span class="badge bg-success">Completed</span>',
        'cancelled': '<span class="badge bg-danger">Cancelled</span>'
    };
    return badges[status] || '<span class="badge bg-secondary">' + status + '</span>';
}
