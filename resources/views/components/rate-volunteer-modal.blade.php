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
<script>
function updateStars(val) {
    document.querySelectorAll('#star-rating .fa-star').forEach(function(star) {
        var sv = parseInt(star.getAttribute('data-value'));
        if (sv <= val) {
            star.classList.remove('text-muted');
            star.classList.add('text-warning');
        } else {
            star.classList.remove('text-warning');
            star.classList.add('text-muted');
        }
    });
}

document.addEventListener('mouseover', function(e) {
    var star = e.target.closest('#star-rating .fa-star');
    if (star) {
        updateStars(parseInt(star.getAttribute('data-value')));
    }
});

document.addEventListener('mouseout', function(e) {
    var star = e.target.closest('#star-rating .fa-star');
    if (star) {
        var container = document.getElementById('star-rating');
        var related = e.relatedTarget;
        if (container && !container.contains(related)) {
            var selected = parseInt(document.getElementById('rate-value').value) || 0;
            updateStars(selected);
        }
    }
});

document.addEventListener('click', function(e) {
    var star = e.target.closest('#star-rating .fa-star');
    if (star) {
        var val = parseInt(star.getAttribute('data-value'));
        document.getElementById('rate-value').value = val;
        updateStars(val);
    }
});

$(function() {
    // Submit rating
    $(document).on('click', '#btn-submit-rating', function() {
        let rating = parseInt($('#rate-value').val());
        if (rating < 1) {
            showError('Please select a rating');
            return;
        }
        let btn = $(this);
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>...');

        $.ajax({
            url: '{{ route("ratings.store") }}',
            type: 'POST',
            data: {
                rateable_id: $('#rate-volunteer-id').val(),
                rateable_type: 'user',
                rating: rating,
                comment: $('#rate-comment').val()
            },
            success: function(res) {
                showSuccess(res.message || 'Rating submitted!');
                $('#rateVolunteerModal').modal('hide');
            },
            error: function(xhr) {
                showError(xhr.responseJSON?.message || 'Failed to submit rating');
            },
            complete: function() {
                btn.prop('disabled', false).html('<i class="fas fa-paper-plane me-1"></i> Submit Rating');
            }
        });
    });
});

function openRateModal(volunteerId, volunteerName, role) {
    document.getElementById('rate-volunteer-id').value = volunteerId;
    document.getElementById('rate-volunteer-name').textContent = volunteerName;
    document.getElementById('rate-value').value = 0;
    document.getElementById('rate-comment').value = '';
    updateStars(0);
    var title = role === 'donor' ? '{{ __("Rate Donor") }}' : '{{ __("Rate Volunteer") }}';
    document.getElementById('rate-modal-title').innerHTML = '<i class="fas fa-star me-2"></i>' + title;
    $('#rateVolunteerModal').modal('show');
}
</script>
