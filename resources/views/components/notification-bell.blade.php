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

    function parseData(n) {
        var d = n.data;
        if (typeof d === 'string') {
            try { d = JSON.parse(d); } catch(e) { d = {}; }
        }
        return d || {};
    }

    function loadNotifications() {
        $.getJSON(countUrl, function (res) {
            var n = res.count || 0;
            if (n > 0) {
                $count.text(n > 99 ? '99+' : n).removeClass('d-none');
            } else {
                $count.addClass('d-none');
            }
        });

        $.getJSON(indexUrl, function (res) {
            var items = res.data || res;
            if (!items.length) { $empty.show(); return; }
            $empty.hide();

            var html = '';
            items.slice(0, 10).forEach(function (n) {
                var d = parseData(n);
                var cls = n.read_at ? '' : ' unread';
                var title = d.title || '';
                var message = d.message || '';
                var time = n.created_at_human || '';

                html += '<a href="#" class="dropdown-item' + cls + '" data-id="' + n.id + '">';
                if (title) {
                    html += '<div class="fw-semibold small">' + title + '</div>';
                }
                html += '<div class="small">' + message + '</div>';
                if (time) {
                    html += '<div class="notif-time">' + time + '</div>';
                }
                html += '</a>';
            });
            $list.html(html);
        });
    }

    loadNotifications();
    setInterval(loadNotifications, 30000);

    $(document).on('click', '#markAllRead', function (e) {
        e.preventDefault();
        $.post('{{ route("notifications.read-all") }}', function () {
            $count.addClass('d-none');
            $list.find('.unread').removeClass('unread');
        });
    });

    $(document).on('click', '#notifList .dropdown-item', function (e) {
        e.preventDefault();
        var id = $(this).data('id');
        if (id) {
            var $item = $(this);
            $.post('/notifications/' + id + '/read', function () {
                $item.removeClass('unread');
                var current = parseInt($count.text()) || 0;
                if (current > 1) {
                    $count.text(current - 1);
                } else {
                    $count.addClass('d-none');
                }
            });
        }
    });
})();
</script>
@endpush
@endonce
