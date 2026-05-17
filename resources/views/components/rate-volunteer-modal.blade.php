{{-- Rate Volunteer Modal - usable by donor & charity --}}
<div class="modal fade" id="rateVolunteerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header" style="background: linear-gradient(135deg, #198754, #20c997); color: white;">
                <h5 class="modal-title" id="rate-modal-title"><i class="fas fa-star me-2"></i>{{ __('Rate Volunteer') }}</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="rate-volunteer-id">
                <input type="hidden" id="rate-donation-id">
                <div class="text-center mb-3">
                    <h6 id="rate-volunteer-name" class="fw-bold">-</h6>
                </div>
                <div class="text-center mb-3">
                    <div id="star-rating" class="d-inline-flex gap-2" style="font-size: 2rem; cursor: pointer;">
                        <i class="fas fa-star text-muted" data-value="1"></i>
                        <i class="fas fa-star text-muted" data-value="2"></i>
                        <i class="fas fa-star text-muted" data-value="3"></i>
                        <i class="fas fa-star text-muted" data-value="4"></i>
                        <i class="fas fa-star text-muted" data-value="5"></i>
                    </div>
                    <input type="hidden" id="rate-value" value="0">
                </div>
                <div class="mb-3">
                    <label class="form-label">{{ __('Comment') }} <small class="text-muted">({{ __('optional') }})</small></label>
                    <textarea class="form-control" id="rate-comment" rows="3" maxlength="500" placeholder="{{ __('Share your experience...') }}"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('general.cancel') }}</button>
                <button type="button" class="btn btn-success" id="btn-submit-rating">
                    <i class="fas fa-paper-plane me-1"></i> {{ __('Submit Rating') }}
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    #star-rating .fa-star { transition: color .15s; }
</style>

@push('scripts')
<script>
    window.rateModalConfig = {
        storeUrl: @json(route('ratings.store'))
    };
    window.rateModalLabels = {
        submit: @json(__('Submit Rating')),
        error: @json(__('Error')),
        selectRating: @json(__('Please select a rating')),
        missingDonation: @json(__('Missing donation context. Close and reopen the donation details, then try again.')),
        failed: @json(__('Failed to submit rating')),
        rateDonor: @json(__('Rate Donor')),
        rateVolunteer: @json(__('Rate Volunteer'))
    };
</script>
<script src="{{ asset('js/rate-volunteer-modal.js') }}"></script>
@endpush
