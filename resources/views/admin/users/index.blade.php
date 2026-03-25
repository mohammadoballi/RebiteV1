@extends('layouts.admin')

@section('title', __('users.manage_users'))

@section('content')
<div class="page-header d-flex flex-wrap align-items-center justify-content-between gap-2">
    <h1><i class="fas fa-users me-2"></i>{{ __('users.manage_users') }}</h1>
    <div class="d-flex align-items-center gap-2">
        <label for="statusFilter" class="form-label mb-0 fw-semibold small text-muted">{{ __('general.status') }}:</label>
        <select id="statusFilter" class="form-select form-select-sm" style="width:auto">
            <option value="">{{ __('All') }}</option>
            <option value="pending">{{ __('general.pending') }}</option>
            <option value="approved">{{ __('general.approved') }}</option>
            <option value="rejected">{{ __('general.rejected') }}</option>
        </select>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table id="users-table" class="table table-hover w-100">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>{{ __('users.name') }}</th>
                        <th>{{ __('users.email') }}</th>
                        <th>{{ __('users.role') }}</th>
                        <th>{{ __('users.status') }}</th>
                        <th>{{ __('Created At') }}</th>
                        <th>{{ __('general.actions') }}</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
@endsection

@section('modals')
{{-- View User Modal --}}
<div class="modal fade" id="viewUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header" style="background: linear-gradient(135deg, #198754, #20c997); color: white;">
                <h5 class="modal-title"><i class="fas fa-user-circle me-2"></i>{{ __('users.user_details') }}</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="view-user-id">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="p-2 rounded bg-light">
                            <small class="text-muted d-block">{{ __('users.name') }}</small>
                            <strong id="user-name">-</strong>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-2 rounded bg-light">
                            <small class="text-muted d-block">{{ __('users.email') }}</small>
                            <strong id="user-email">-</strong>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-2 rounded bg-light">
                            <small class="text-muted d-block">{{ __('Phone') }}</small>
                            <strong id="user-phone">-</strong>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-2 rounded bg-light">
                            <small class="text-muted d-block">{{ __('users.role') }}</small>
                            <strong id="user-role">-</strong>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-2 rounded bg-light">
                            <small class="text-muted d-block">{{ __('users.status') }}</small>
                            <span id="user-status">-</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-2 rounded bg-light">
                            <small class="text-muted d-block">{{ __('City') }}</small>
                            <strong id="user-city">-</strong>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-2 rounded bg-light">
                            <small class="text-muted d-block">{{ __('Address') }}</small>
                            <strong id="user-address">-</strong>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-2 rounded bg-light">
                            <small class="text-muted d-block">{{ __('Organization') }}</small>
                            <strong id="user-organization">-</strong>
                        </div>
                    </div>
                    <div class="col-12" id="health-certificate-row" style="display:none">
                        <div class="p-2 rounded bg-light">
                            <small class="text-muted d-block">{{ __('users.health_certificate') }}</small>
                            <a href="#" id="user-health-certificate" target="_blank" class="btn btn-sm btn-outline-success mt-1">
                                <i class="fas fa-file-medical me-1"></i> {{ __('general.view') }}
                            </a>
                        </div>
                    </div>
                    <div class="col-12" id="rejection-reason-row" style="display:none">
                        <div class="p-2 rounded bg-danger bg-opacity-10">
                            <small class="text-muted d-block">{{ __('users.rejection_reason') }}</small>
                            <strong id="user-rejection-reason" class="text-danger">-</strong>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer" id="view-modal-footer">
                <div id="view-status-actions"></div>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('general.close') }}</button>
            </div>
        </div>
    </div>
</div>

{{-- Edit User Modal --}}
<div class="modal fade" id="editUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow">
            <div class="modal-header" style="background: linear-gradient(135deg, #198754, #20c997); color: white;">
                <h5 class="modal-title"><i class="fas fa-user-edit me-2"></i>{{ __('Edit User') }}</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="editUserForm">
                <input type="hidden" id="edit-user-id">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">{{ __('users.name') }} <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('users.email') }} <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" name="email" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('Phone') }}</label>
                            <input type="text" class="form-control" name="phone">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('users.status') }} <span class="text-danger">*</span></label>
                            <select class="form-select" name="status" id="edit-status-select" required>
                                <option value="pending">{{ __('general.pending') }}</option>
                                <option value="approved">{{ __('general.approved') }}</option>
                                <option value="rejected">{{ __('general.rejected') }}</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('City') }}</label>
                            <input type="text" class="form-control" name="city">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('Organization') }}</label>
                            <input type="text" class="form-control" name="organization_name">
                        </div>
                        <div class="col-12">
                            <label class="form-label">{{ __('Address') }}</label>
                            <textarea class="form-control" name="address" rows="2"></textarea>
                        </div>
                        <div class="col-12" id="rejection-reason-field" style="display:none">
                            <label class="form-label">{{ __('users.rejection_reason') }} <span class="text-danger">*</span></label>
                            <textarea class="form-control" name="rejection_reason" rows="2" placeholder="{{ __('Enter reason for rejection...') }}"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('general.cancel') }}</button>
                    <button type="button" class="btn btn-success" id="btn-save-user">
                        <i class="fas fa-save me-1"></i> {{ __('general.save') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    window.routes = {
        usersDatatable: '{{ route("admin.users.datatable") }}',
        usersShow:      '{{ route("admin.users.show", ":id") }}',
        usersUpdate:    '{{ route("admin.users.update", ":id") }}',
        usersApprove:   '{{ route("admin.users.approve", ":id") }}',
        usersReject:    '{{ route("admin.users.reject", ":id") }}',
        usersDestroy:   '{{ route("admin.users.destroy", ":id") }}'
    };
</script>
<script src="{{ asset('js/admin/users.js') }}"></script>
@endpush
