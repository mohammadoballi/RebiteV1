/**
 * Donor - Donations Management (Multi-Item Support)
 */
let itemIndex = 0;

function getItemRowHtml(index, data) {
    data = data || {};
    return `
    <div class="item-row border rounded p-3 mb-2 bg-light" data-index="${index}">
        <div class="row g-2 align-items-end">
            <div class="col-md-4">
                <label class="form-label small mb-1">Food Type <span class="text-danger">*</span></label>
                <input type="text" class="form-control form-control-sm" name="items[${index}][food_type]" value="${data.food_type || ''}" placeholder="e.g. Rice, Bread" required>
            </div>
            <div class="col-md-2">
                <label class="form-label small mb-1">Quantity <span class="text-danger">*</span></label>
                <input type="text" class="form-control form-control-sm" name="items[${index}][quantity]" value="${data.quantity || ''}" placeholder="10" required>
            </div>
            <div class="col-md-2">
                <label class="form-label small mb-1">Unit</label>
                <select class="form-select form-select-sm" name="items[${index}][quantity_unit]">
                    <option value="kg" ${(data.quantity_unit === 'kg') ? 'selected' : ''}>Kg</option>
                    <option value="pieces" ${(data.quantity_unit === 'pieces') ? 'selected' : ''}>Pieces</option>
                    <option value="boxes" ${(data.quantity_unit === 'boxes') ? 'selected' : ''}>Boxes</option>
                    <option value="bags" ${(data.quantity_unit === 'bags') ? 'selected' : ''}>Bags</option>
                    <option value="plates" ${(data.quantity_unit === 'plates') ? 'selected' : ''}>Plates</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small mb-1">Description</label>
                <input type="text" class="form-control form-control-sm" name="items[${index}][description]" value="${data.description || ''}" placeholder="Optional">
            </div>
            <div class="col-md-1 text-end">
                <button type="button" class="btn btn-sm btn-outline-danger btn-remove-item" title="Remove">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
    </div>`;
}

function addItemRow(data) {
    $('#items-container').append(getItemRowHtml(itemIndex, data));
    itemIndex++;
    updateRemoveButtons();
}

function updateRemoveButtons() {
    let rows = $('.item-row');
    if (rows.length <= 1) {
        rows.find('.btn-remove-item').prop('disabled', true);
    } else {
        rows.find('.btn-remove-item').prop('disabled', false);
    }
}

$(document).ready(function() {
    // City -> Town dynamic loading for donation form
    $('#donation_city_id').on('change', function () {
        var cityId = $(this).val();
        var $town = $('#donation_town_id');
        $town.html('<option value="">Select Town</option>');
        if (!cityId) return;

        $.get('/api/cities/' + cityId + '/towns', function (towns) {
            towns.forEach(function (t) {
                $town.append('<option value="' + t.id + '">' + t.name + '</option>');
            });
        });
    });

    // DataTable
    let donationsTable = initDataTable('donations-table', window.routes.donationsDatatable, [
        { data: 'id', name: 'id' },
        { data: 'items_summary', name: 'food_type', orderable: false },
        { data: 'volunteer_info', name: 'volunteers_needed', orderable: false },
        { data: 'status', name: 'status' },
        { data: 'pickup_time', name: 'pickup_time' },
        { data: 'created_at', name: 'created_at' },
        { data: 'actions', name: 'actions', orderable: false, searchable: false }
    ]);

    // Add item row
    $(document).on('click', '#btn-add-item', function() { addItemRow(); });

    // Remove item row
    $(document).on('click', '.btn-remove-item', function() {
        $(this).closest('.item-row').remove();
        updateRemoveButtons();
    });

    // Open create modal
    $(document).on('click', '#btn-add-donation', function() {
        clearFormErrors('#donationForm');
        $('#donationForm')[0].reset();
        $('#items-container').empty();
        itemIndex = 0;
        addItemRow();
        $('#donationModalLabel').text('Add Donation');
        $('#donationForm').attr('data-action', 'create');
        $('#donationForm').removeAttr('data-id');
        $('#donationModal').modal('show');
    });

    // Open edit modal
    $(document).on('click', '.btn-edit-donation', function() {
        let donationId = $(this).data('id');
        clearFormErrors('#donationForm');
        $.get(window.routes.donationsShow.replace(':id', donationId), function(data) {
            $('#donationForm')[0].reset();
            $('#donationModalLabel').text('Edit Donation');
            $('#donationForm').attr('data-action', 'edit');
            $('#donationForm').attr('data-id', data.id);

            // Populate items
            $('#items-container').empty();
            itemIndex = 0;
            if (data.items && data.items.length > 0) {
                data.items.forEach(function(item) { addItemRow(item); });
            } else {
                addItemRow({ food_type: data.food_type, quantity: data.quantity, quantity_unit: data.quantity_unit });
            }

            // Populate other fields
            $('#donationForm [name="description"]').val(data.description);
            $('#donationForm [name="pickup_address"]').val(data.pickup_address);
            $('#donationForm [name="delivery_volunteers_needed"]').val(data.delivery_volunteers_needed ?? 1);
            $('#donationForm [name="packaging_volunteers_needed"]').val(data.packaging_volunteers_needed ?? 0);
            $('#donationForm [name="pickup_time"]').val(data.pickup_time ? data.pickup_time.substring(0, 16) : '');
            $('#donationForm [name="expiry_time"]').val(data.expiry_time ? data.expiry_time.substring(0, 16) : '');
            $('#donationForm [name="notes"]').val(data.notes);

            // Populate city and town
            if (data.city_id) {
                $('#donation_city_id').val(data.city_id);
                $.get('/api/cities/' + data.city_id + '/towns', function (towns) {
                    var $town = $('#donation_town_id');
                    $town.html('<option value="">Select Town</option>');
                    towns.forEach(function (t) {
                        var sel = (data.town_id && data.town_id == t.id) ? 'selected' : '';
                        $town.append('<option value="' + t.id + '" ' + sel + '>' + t.name + '</option>');
                    });
                });
            }

            $('#donationModal').modal('show');
        });
    });

    // Save donation
    $(document).on('click', '#btn-save-donation', function(e) {
        e.preventDefault();
        let form = $('#donationForm');
        let action = form.attr('data-action');
        let formData = new FormData(form[0]);
        let url, method;

        if (action === 'edit') {
            url = window.routes.donationsUpdate.replace(':id', form.attr('data-id'));
            formData.append('_method', 'PUT');
        } else {
            url = window.routes.donationsStore;
        }

        let btn = $(this);
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Saving...');

        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                showSuccess(response.message || 'Saved successfully');
                $('#donationModal').modal('hide');
                donationsTable.ajax.reload();
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    let errors = xhr.responseJSON?.errors;
                    if (errors) {
                        let msg = Object.values(errors).flat().join('<br>');
                        Swal.fire({ icon: 'error', title: 'Validation Error', html: msg });
                    }
                } else {
                    showError(xhr.responseJSON?.message || 'An error occurred');
                }
            },
            complete: function() {
                btn.prop('disabled', false).html('<i class="fas fa-save me-1"></i> Save');
            }
        });
    });

    // View donation
    $(document).on('click', '.btn-view-donation', function() {
        let donationId = $(this).data('id');
        $.get(window.routes.donationsShow.replace(':id', donationId), function(data) {
            let modal = $('#viewDonationModal');

            // Items
            let itemsHtml = '';
            if (data.items && data.items.length > 0) {
                data.items.forEach(function(item) {
                    itemsHtml += '<div class="d-flex justify-content-between align-items-center border-bottom py-2">';
                    itemsHtml += '<span><i class="fas fa-check-circle text-success me-1"></i> ' + item.food_type + '</span>';
                    itemsHtml += '<span class="badge bg-light text-dark border">' + item.quantity + ' ' + item.quantity_unit + '</span>';
                    itemsHtml += '</div>';
                });
            } else {
                itemsHtml = '<span>' + (data.food_type || '-') + ' — ' + (data.quantity || '') + ' ' + (data.quantity_unit || '') + '</span>';
            }
            modal.find('#view-items-list').html(itemsHtml);

            let statusBadges = {
                'pending': 'warning', 'accepted': 'info', 'assigned': 'primary',
                'in_transit': 'secondary', 'delivered': 'success', 'completed': 'success', 'cancelled': 'danger'
            };
            modal.find('#view-status').html('<span class="badge bg-' + (statusBadges[data.status] || 'secondary') + '">' + data.status + '</span>');
            modal.find('#view-delivery-volunteers').html((data.delivery_volunteers_needed || 0) + ' <i class="fas fa-truck text-success"></i>');
            modal.find('#view-packaging-volunteers').html((data.packaging_volunteers_needed || 0) + ' <i class="fas fa-box text-info"></i>');
            modal.find('#view-pickup-time').text(data.pickup_time ? new Date(data.pickup_time).toLocaleString() : '-');
            modal.find('#view-address').text(data.pickup_address || '-');

            if (data.description) { modal.find('#view-description').text(data.description); modal.find('#view-desc-wrap').show(); }
            else { modal.find('#view-desc-wrap').hide(); }

            if (data.notes) { modal.find('#view-notes').text(data.notes); modal.find('#view-notes-wrap').show(); }
            else { modal.find('#view-notes-wrap').hide(); }

            if (data.image) {
                modal.find('#view-image').attr('src', '/storage/' + data.image);
                modal.find('#view-image-container').show();
            } else {
                modal.find('#view-image-container').hide();
            }

            // Show assigned volunteers with rate buttons
            if (data.assignments && data.assignments.length > 0) {
                let volHtml = '';
                data.assignments.forEach(function(a) {
                    if (a.volunteer) {
                        volHtml += '<div class="d-flex justify-content-between align-items-center border-bottom py-2">';
                        volHtml += '<div>';
                        volHtml += '<i class="fas fa-user-circle text-success me-1"></i> ';
                        volHtml += '<strong>' + a.volunteer.name + '</strong>';
                        volHtml += ' <span class="badge bg-' + (a.status === 'completed' ? 'success' : 'info') + ' ms-1">' + a.status + '</span>';
                        volHtml += '</div>';
                        volHtml += '</div>';
                    }
                });
                modal.find('#view-volunteers-list').html(volHtml);
                modal.find('#view-volunteers-section').show();
            } else {
                modal.find('#view-volunteers-section').hide();
            }

            modal.modal('show');
        });
    });

    // Delete
    $(document).on('click', '.btn-delete-donation', function() {
        confirmDelete(window.routes.donationsDestroy.replace(':id', $(this).data('id')), 'donations-table');
    });
});
