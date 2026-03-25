@extends('layouts.volunteer')

@section('title', __('My Ratings & Points'))

@section('content')
<div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
    <h1><i class="fas fa-star me-2"></i>{{ __('My Ratings & Points') }}</h1>
</div>

{{-- Stats Cards --}}
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width:52px;height:52px;background:rgba(255,193,7,.15)">
                    <i class="fas fa-star fa-lg text-warning"></i>
                </div>
                <h3 class="fw-bold mb-0" id="stat-avg">-</h3>
                <small class="text-muted">{{ __('Average Rating') }}</small>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width:52px;height:52px;background:rgba(25,135,84,.12)">
                    <i class="fas fa-comments fa-lg text-success"></i>
                </div>
                <h3 class="fw-bold mb-0" id="stat-count">-</h3>
                <small class="text-muted">{{ __('Total Reviews') }}</small>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width:52px;height:52px;background:rgba(13,202,240,.12)">
                    <i class="fas fa-coins fa-lg" style="color:#0dcaf0"></i>
                </div>
                <h3 class="fw-bold mb-0" id="stat-points">-</h3>
                <small class="text-muted">{{ __('Total Points') }}</small>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width:52px;height:52px;background:rgba(255,193,7,.15)">
                    <i class="fas fa-trophy fa-lg text-warning"></i>
                </div>
                <h3 class="fw-bold mb-0"><span id="stat-level" class="badge">-</span></h3>
                <small class="text-muted">{{ __('Level') }}</small>
            </div>
        </div>
    </div>
</div>

{{-- Points Progress --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <h6 class="fw-bold mb-3"><i class="fas fa-chart-line me-1 text-success"></i> {{ __('Points Progress') }}</h6>
        <div class="d-flex justify-content-between small text-muted mb-1">
            <span>{{ __('Starter') }} (0)</span>
            <span>{{ __('Bronze') }} (50)</span>
            <span>{{ __('Silver') }} (200)</span>
            <span>{{ __('Gold') }} (500)</span>
        </div>
        <div class="progress" style="height:10px">
            <div class="progress-bar bg-success" id="points-bar" style="width:0%"></div>
        </div>
        <div class="mt-3">
            <small class="text-muted">
                <i class="fas fa-info-circle me-1"></i>
                {{ __('Earn points by: Volunteering (+5), Picking up (+5), Delivering (+25), Receiving ratings (+3), Donations (+10)') }}
            </small>
        </div>
    </div>
</div>

{{-- Ratings List --}}
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white fw-semibold border-0">
        <i class="fas fa-star me-1 text-warning"></i> {{ __('Reviews From Others') }}
    </div>
    <div class="card-body" id="ratings-list">
        <div class="text-center py-4">
            <div class="spinner-border text-success" role="status"></div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $.get('{{ route("ratings.my") }}', function(data) {
        // Stats
        $('#stat-avg').text(data.average || '0.0');
        $('#stat-count').text(data.count || 0);
        $('#stat-points').text(data.points || 0);
        $('#stat-level').text(data.level || 'Starter')
            .removeClass().addClass('badge bg-' + (data.level_color || 'light'));

        // Progress bar
        let pct = Math.min((data.points / 500) * 100, 100);
        $('#points-bar').css('width', pct + '%');

        // Ratings list
        let html = '';
        if (data.ratings && data.ratings.length > 0) {
            data.ratings.forEach(function(r) {
                let stars = '';
                for (let i = 1; i <= 5; i++) {
                    stars += '<i class="fas fa-star ' + (i <= r.rating ? 'text-warning' : 'text-muted') + '"></i>';
                }
                html += '<div class="border-bottom py-3">';
                html += '<div class="d-flex justify-content-between align-items-start">';
                html += '<div>';
                html += '<strong><i class="fas fa-user-circle text-success me-1"></i> ' + (r.rater ? r.rater.name : 'Anonymous') + '</strong>';
                html += '<div class="mt-1">' + stars + '</div>';
                html += '</div>';
                html += '<small class="text-muted">' + new Date(r.created_at).toLocaleDateString() + '</small>';
                html += '</div>';
                if (r.comment) {
                    html += '<p class="text-muted mt-2 mb-0"><i class="fas fa-quote-left text-success me-1 small"></i> ' + r.comment + '</p>';
                }
                html += '</div>';
            });
        } else {
            html = '<div class="text-center py-4 text-muted"><i class="fas fa-star-half-alt fa-3x mb-2"></i><p>{{ __("No ratings yet. Keep volunteering!") }}</p></div>';
        }
        $('#ratings-list').html(html);
    });
});
</script>
@endpush
