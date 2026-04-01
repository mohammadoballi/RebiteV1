/**
 * Admin - Users Management
 */
$(document).ready(function() {
    let usersTable = initDataTable('users-table', window.routes.usersDatatable, [
        { data: 'id', name: 'id' },
        { data: 'name', name: 'name' },
        { data: 'email', name: 'email' },
        { data: 'role', name: 'role', orderable: false, searchable: false },
        { data: 'status_badge', name: 'status', orderable: true, searchable: false },
        { data: 'created_at', name: 'created_at' },
        { data: 'actions', name: 'actions', orderable: false, searchable: false }
    ]);

    // Status filter
    $('#statusFilter').on('change', function() {
        let val = $(this).val();
        usersTable.ajax.url(window.routes.usersDatatable + (val ? '?status=' + val : '')).load();
    });

    // ── View User ──
    $(document).on('click', '.btn-view-user', function() {
        let userId = $(this).data('id');
        $.get(window.routes.usersShow.replace(':id', userId), function(data) {
            let modal = $('#viewUserModal');
            modal.find('#view-user-id').val(data.id);
            modal.find('#user-name').text(data.name);
            modal.find('#user-email').text(data.email);
            modal.find('#user-phone').text(data.phone || '-');
            modal.find('#user-role').text(data.roles && data.roles.length ? data.roles.map(r => r.display_name).join(', ') : '-');
            modal.find('#user-status').html(getStatusBadge(data.status));
            modal.find('#user-address').text(data.address || '-');
            modal.find('#user-city').text(data.city || '-');
            modal.find('#user-organization').text(data.organization_name || '-');

            if (data.health_certificate) {
                modal.find('#user-health-certificate').attr('href', '/storage/' + data.health_certificate);
                modal.find('#health-certificate-row').show();
            } else {
                modal.find('#health-certificate-row').hide();
            }

            if (data.organization_license) {
                modal.find('#user-organization-license').attr('href', '/storage/' + data.organization_license);
                modal.find('#organization-license-row').show();
            } else {
                modal.find('#organization-license-row').hide();
            }

            if (data.status === 'rejected' && data.rejection_reason) {
                modal.find('#user-rejection-reason').text(data.rejection_reason);
                modal.find('#rejection-reason-row').show();
            } else {
                modal.find('#rejection-reason-row').hide();
            }

            // Status action buttons in footer
            let actionsHtml = '';
            if (data.status === 'pending') {
                actionsHtml += '<button class="btn btn-success btn-sm me-1" onclick="approveUser(' + data.id + ')"><i class="fas fa-check me-1"></i> Approve</button>';
                actionsHtml += '<button class="btn btn-danger btn-sm me-1" onclick="rejectUserPrompt(' + data.id + ')"><i class="fas fa-ban me-1"></i> Reject</button>';
            } else if (data.status === 'approved') {
                actionsHtml += '<button class="btn btn-warning btn-sm me-1" onclick="changeUserStatus(' + data.id + ', \'pending\')"><i class="fas fa-pause me-1"></i> Suspend</button>';
                actionsHtml += '<button class="btn btn-danger btn-sm me-1" onclick="rejectUserPrompt(' + data.id + ')"><i class="fas fa-ban me-1"></i> Reject</button>';
            } else if (data.status === 'rejected') {
                actionsHtml += '<button class="btn btn-success btn-sm me-1" onclick="approveUser(' + data.id + ')"><i class="fas fa-check me-1"></i> Approve</button>';
                actionsHtml += '<button class="btn btn-warning btn-sm me-1" onclick="changeUserStatus(' + data.id + ', \'pending\')"><i class="fas fa-pause me-1"></i> Set Pending</button>';
            }
            modal.find('#view-status-actions').html(actionsHtml);
            modal.modal('show');
        });
    });

    // ── Edit User ──
    $(document).on('click', '.btn-edit-user', function() {
        let userId = $(this).data('id');
        $.get(window.routes.usersShow.replace(':id', userId), function(data) {
            let form = $('#editUserForm');
            form[0].reset();
            $('#edit-user-id').val(data.id);
            form.find('[name="name"]').val(data.name);
            form.find('[name="email"]').val(data.email);
            form.find('[name="phone"]').val(data.phone || '');
            form.find('[name="status"]').val(data.status);
            form.find('[name="city"]').val(data.city || '');
            form.find('[name="address"]').val(data.address || '');
            form.find('[name="organization_name"]').val(data.organization_name || '');
            form.find('[name="rejection_reason"]').val(data.rejection_reason || '');
            toggleRejectionField(data.status);
            $('#editUserModal').modal('show');
        });
    });

    // Show/hide rejection reason when status changes
    $('#edit-status-select').on('change', function() {
        toggleRejectionField($(this).val());
    });

    // Save user
    $(document).on('click', '#btn-save-user', function() {
        let userId = $('#edit-user-id').val();
        let form = $('#editUserForm');
        let btn = $(this);
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>...');

        $.ajax({
            url: window.routes.usersUpdate.replace(':id', userId),
            type: 'PUT',
            data: form.serialize(),
            success: function(response) {
                showSuccess(response.message || 'User updated');
                $('#editUserModal').modal('hide');
                usersTable.ajax.reload();
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    let errors = xhr.responseJSON?.errors;
                    if (errors) {
                        let msg = Object.values(errors).flat().join('<br>');
                        Swal.fire({ icon: 'error', title: 'Validation Error', html: msg });
                    }
                } else {
                    showError(xhr.responseJSON?.message || 'Update failed');
                }
            },
            complete: function() {
                btn.prop('disabled', false).html('<i class="fas fa-save me-1"></i> Save');
            }
        });
    });

    // ── Inline Approve ──
    $(document).on('click', '.btn-approve-inline', function() {
        let userId = $(this).data('id');
        approveUser(userId);
    });

    // ── Inline Reject ──
    $(document).on('click', '.btn-reject-inline', function() {
        let userId = $(this).data('id');
        rejectUserPrompt(userId);
    });

    // ── Delete User ──
    $(document).on('click', '.btn-delete-user', function() {
        confirmDelete(window.routes.usersDestroy.replace(':id', $(this).data('id')), 'users-table');
    });
});

function toggleRejectionField(status) {
    if (status === 'rejected') {
        $('#rejection-reason-field').show();
    } else {
        $('#rejection-reason-field').hide();
    }
}

function approveUser(userId) {
    Swal.fire({
        title: 'Approve User',
        text: 'Are you sure you want to approve this user?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#198754',
        confirmButtonText: 'Approve'
    }).then((result) => {
        if (result.isConfirmed) {
            $.post(window.routes.usersApprove.replace(':id', userId), function(response) {
                showSuccess(response.message || 'User approved');
                $('#viewUserModal').modal('hide');
                $('#users-table').DataTable().ajax.reload();
            }).fail(function(xhr) {
                showError(xhr.responseJSON?.message || 'Failed');
            });
        }
    });
}

function rejectUserPrompt(userId) {
    Swal.fire({
        title: 'Reject User',
        input: 'textarea',
        inputLabel: 'Rejection Reason',
        inputPlaceholder: 'Enter reason for rejection...',
        inputAttributes: { required: true },
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        confirmButtonText: 'Reject',
        inputValidator: (value) => {
            if (!value) return 'Reason is required';
        }
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: window.routes.usersReject.replace(':id', userId),
                type: 'POST',
                data: { reason: result.value },
                success: function(response) {
                    showSuccess(response.message || 'User rejected');
                    $('#viewUserModal').modal('hide');
                    $('#users-table').DataTable().ajax.reload();
                },
                error: function(xhr) {
                    showError(xhr.responseJSON?.message || 'Failed');
                }
            });
        }
    });
}

function changeUserStatus(userId, newStatus) {
    let label = newStatus === 'pending' ? 'Suspend / Set Pending' : newStatus.charAt(0).toUpperCase() + newStatus.slice(1);
    Swal.fire({
        title: label,
        text: 'Change this user\'s status to ' + newStatus + '?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ffc107',
        confirmButtonText: 'Confirm'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: window.routes.usersUpdate.replace(':id', userId),
                type: 'PUT',
                data: { name: '', status: newStatus },
                beforeSend: function() {},
                success: function(response) {
                    showSuccess(response.message || 'Status changed');
                    $('#viewUserModal').modal('hide');
                    $('#users-table').DataTable().ajax.reload();
                },
                error: function(xhr) {
                    // If validation fails (name required), fetch user first
                    $.get(window.routes.usersShow.replace(':id', userId), function(userData) {
                        $.ajax({
                            url: window.routes.usersUpdate.replace(':id', userId),
                            type: 'PUT',
                            data: { name: userData.name, email: userData.email, status: newStatus },
                            success: function(resp) {
                                showSuccess(resp.message || 'Status changed');
                                $('#viewUserModal').modal('hide');
                                $('#users-table').DataTable().ajax.reload();
                            },
                            error: function(x) {
                                showError(x.responseJSON?.message || 'Failed');
                            }
                        });
                    });
                }
            });
        }
    });
}

function getStatusBadge(status) {
    const badges = {
        'pending': '<span class="badge bg-warning text-dark">Pending</span>',
        'approved': '<span class="badge bg-success">Approved</span>',
        'rejected': '<span class="badge bg-danger">Rejected</span>'
    };
    return badges[status] || '<span class="badge bg-secondary">' + status + '</span>';
}
