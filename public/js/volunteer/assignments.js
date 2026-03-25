/**
 * Volunteer - Assignments Management
 */
$(document).ready(function() {
    let assignmentsTable = initDataTable('assignments-table', window.routes.assignmentsDatatable, [
        { data: 'id', name: 'id' },
        { data: 'donation_food_type', name: 'donation.food_type' },
        { data: 'assignment_type', name: 'assignment_type' },
        { data: 'status', name: 'status' },
        { data: 'pickup_at', name: 'pickup_at' },
        { data: 'delivered_at', name: 'delivered_at' },
        { data: 'actions', name: 'actions', orderable: false, searchable: false }
    ]);

    // View assignment
    $(document).on('click', '.btn-view-assignment', function() {
        let assignmentId = $(this).data('id');
        $.get(window.routes.assignmentsShow.replace(':id', assignmentId), function(data) {
            let modal = $('#viewAssignmentModal');
            modal.find('#assign-food-type').text(data.donation ? data.donation.food_type : '-');
            modal.find('#assign-quantity').text(data.donation ? data.donation.quantity + ' ' + data.donation.quantity_unit : '-');
            modal.find('#assign-address').text(data.donation ? data.donation.pickup_address : '-');
            modal.find('#assign-type').text(data.assignment_type);
            modal.find('#assign-status').html(getStatusBadge(data.status));
            modal.find('#assign-pickup-at').text(data.pickup_at || '-');
            modal.find('#assign-delivered-at').text(data.delivered_at || '-');
            modal.find('#assign-notes').text(data.notes || '-');
            modal.find('#current-assignment-id').val(data.id);

            // Show/hide action buttons based on status
            modal.find('.action-buttons button').hide();
            switch(data.status) {
                case 'pending':
                    modal.find('#btn-accept-assignment').show();
                    break;
                case 'accepted':
                    modal.find('#btn-pickup-assignment').show();
                    break;
                case 'in_progress':
                    modal.find('#btn-deliver-assignment').show();
                    break;
            }
            
            modal.modal('show');
        });
    });

    // Accept assignment
    $(document).on('click', '#btn-accept-assignment', function() {
        let id = $('#current-assignment-id').val();
        $.post(window.routes.assignmentsAccept.replace(':id', id), function(response) {
            showSuccess(response.message || 'Assignment accepted');
            $('#viewAssignmentModal').modal('hide');
            assignmentsTable.ajax.reload();
        });
    });

    // Mark picked up
    $(document).on('click', '#btn-pickup-assignment', function() {
        let id = $('#current-assignment-id').val();
        $.post(window.routes.assignmentsPickup.replace(':id', id), function(response) {
            showSuccess(response.message || 'Marked as picked up');
            $('#viewAssignmentModal').modal('hide');
            assignmentsTable.ajax.reload();
        });
    });

    // Mark delivered
    $(document).on('click', '#btn-deliver-assignment', function() {
        let id = $('#current-assignment-id').val();
        $.post(window.routes.assignmentsDeliver.replace(':id', id), function(response) {
            showSuccess(response.message || 'Marked as delivered');
            $('#viewAssignmentModal').modal('hide');
            assignmentsTable.ajax.reload();
        });
    });
});

function getStatusBadge(status) {
    const badges = {
        'pending': '<span class="badge bg-warning">Pending</span>',
        'accepted': '<span class="badge bg-info">Accepted</span>',
        'in_progress': '<span class="badge bg-primary">In Progress</span>',
        'completed': '<span class="badge bg-success">Completed</span>',
        'cancelled': '<span class="badge bg-danger">Cancelled</span>'
    };
    return badges[status] || '<span class="badge bg-secondary">' + status + '</span>';
}
