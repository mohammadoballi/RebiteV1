<div class="dropdown" id="notificationDropdown">
    <button class="btn btn-link nav-link position-relative" data-bs-toggle="dropdown"
            data-bs-auto-close="outside" aria-expanded="false">
        <i class="fas fa-bell fa-lg"></i>
        <span class="badge bg-danger badge-notify rounded-pill d-none" id="notifCount">0</span>
    </button>

    <div class="dropdown-menu dropdown-menu-end notification-dropdown p-0">
        <div class="d-flex justify-content-between align-items-center px-3 py-2 border-bottom bg-light">
            <strong class="fs-6">{{ __('general.notifications') }}</strong>
            <a href="#" class="text-decoration-none small" id="markAllRead">{{ __('Mark all as read') }}</a>
        </div>

        <div id="notifList">
            <div class="text-center text-muted py-4" id="notifEmpty">
                <i class="fas fa-bell-slash mb-1"></i>
                <p class="mb-0 small">{{ __('No notifications') }}</p>
            </div>
        </div>
    </div>
</div>

@once
@push('scripts')
<script>
(function () {
    const $count   = $('#notifCount');
    const $list    = $('#notifList');
    const $empty   = $('#notifEmpty');
    const countUrl = '{{ route("notifications.unread-count") }}';
    const indexUrl = '{{ route("notifications.index") }}';

    function loadNotifications() {
        $.getJSON(countUrl, function (res) {
            const n = res.count || 0;
            if (n > 0) {
                $count.text(n > 99 ? '99+' : n).removeClass('d-none');
            } else {
                $count.addClass('d-none');
            }
        });

        $.getJSON(indexUrl, function (res) {
            const items = res.data || res;
            if (!items.length) { $empty.show(); return; }
            $empty.hide();

            let html = '';
            items.slice(0, 8).forEach(function (n) {
                const cls = n.read_at ? '' : ' unread';
                html += '<a href="#" class="dropdown-item' + cls + '" data-id="' + n.id + '">'
                      + '<div>' + (n.data?.message || n.message || '') + '</div>'
                      + '<div class="notif-time">' + (n.created_at_human || n.created_at || '') + '</div>'
                      + '</a>';
            });
            $list.html(html);
        });
    }

    loadNotifications();
    setInterval(loadNotifications, 60000);

    $(document).on('click', '#markAllRead', function (e) {
        e.preventDefault();
        $.post('{{ route("notifications.read-all") }}', function () {
            $count.addClass('d-none');
            $list.find('.unread').removeClass('unread');
        });
    });

    $(document).on('click', '#notifList .dropdown-item', function (e) {
        e.preventDefault();
        const id = $(this).data('id');
        if (id) {
            $.post('/notifications/' + id + '/read', function () {
                loadNotifications();
            });
        }
    });
})();
</script>
@endpush
@endonce
