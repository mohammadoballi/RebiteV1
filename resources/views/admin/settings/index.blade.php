@extends('layouts.admin')

@section('title', __('Settings'))

@section('content')
<div class="page-header d-flex flex-wrap align-items-center justify-content-between gap-2">
    <h1><i class="fas fa-cogs me-2"></i>{{ __('Settings') }}</h1>
</div>

<div class="card">
    <div class="card-body">
        <form id="settings-form">
            @csrf
            <div class="row g-3">
                @foreach($settings as $i => $setting)
                <div class="col-md-6">
                    <label class="form-label fw-semibold">{{ $setting->label }}</label>
                    <input type="hidden" name="settings[{{ $i }}][key]" value="{{ $setting->key }}">
                    <input type="{{ $setting->type === 'number' ? 'number' : 'text' }}"
                           class="form-control"
                           name="settings[{{ $i }}][value]"
                           value="{{ $setting->value }}"
                           step="{{ $setting->type === 'number' ? '0.01' : '' }}">
                    <small class="text-muted">{{ $setting->key }}</small>
                </div>
                @endforeach
            </div>

            <div class="text-end mt-4">
                <button type="submit" class="btn btn-success" id="btn-save-settings">
                    <i class="fas fa-save me-1"></i> {{ __('general.save') }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#settings-form').on('submit', function(e) {
        e.preventDefault();
        var btn = $('#btn-save-settings');
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>...');

        $.ajax({
            url: '{{ route("admin.settings.update") }}',
            type: 'POST',
            data: $(this).serialize(),
            success: function(res) {
                showSuccess(res.message || 'Saved!');
            },
            error: function(xhr) {
                showError(xhr.responseJSON?.message || 'Failed to save settings');
            },
            complete: function() {
                btn.prop('disabled', false).html('<i class="fas fa-save me-1"></i> {{ __("general.save") }}');
            }
        });
    });
});
</script>
@endpush
