@php
    $sidenav = json_decode($sidenav);

    $settings = file_get_contents(resource_path('views/admin/setting/settings.json'));
    $settings = json_decode($settings);

    $routesData = [];
    foreach (\Illuminate\Support\Facades\Route::getRoutes() as $route) {
        $name = $route->getName();
        if (strpos($name, 'admin') !== false) {
            $routeData = [
                $name => url($route->uri()),
            ];

            $routesData[] = $routeData;
        }
    }
@endphp

<!-- navbar-wrapper start -->
<nav class="navbar-wrapper d-flex flex-wrap">
    <div class="navbar__left">
        <button class="res-sidebar-open-btn me-3" type="button"><i class="las la-bars"></i></button>
        <form class="navbar-search">
            <input class="navbar-search-field" id="searchInput" name="#0" type="search" autocomplete="off" placeholder="@lang('Search here...')">
            <i class="las la-search"></i>
            <ul class="search-list"></ul>
        </form>
    </div>
    <div class="navbar__right">
        <ul class="navbar__action-list">
            @if (version_compare(gs('available_version'), systemDetails()['version'], '>'))
                <li><button class="primary--layer" data-bs-toggle="tooltip" data-bs-placement="bottom" type="button" title="@lang('Update Available')"><a class="primary--layer" href="{{ route('admin.system.update') }}"><i class="las la-download text--warning"></i></a> </button></li>
            @endif
            @permit('admin.notifications')
                <li class="dropdown">
                    <button class="primary--layer notification-bell" data-bs-toggle="dropdown" data-display="static" type="button" aria-haspopup="true" aria-expanded="false">
                        <span data-bs-toggle="tooltip" data-bs-placement="bottom" title="@lang('Unread Notifications')">
                            <i class="las la-bell @if ($adminNotificationCount > 0) icon-left-right @endif"></i>
                        </span>
                        @if ($adminNotificationCount > 0)
                            <span class="notification-count">{{ $adminNotificationCount <= 9 ? $adminNotificationCount : '9+' }}</span>
                        @endif
                    </button>
                    <div class="dropdown-menu dropdown-menu--md p-0 border-0 box--shadow1 dropdown-menu-right">
                        <div class="dropdown-menu__header">
                            <span class="caption">@lang('Notification')</span>
                            @if ($adminNotificationCount > 0)
                                <p>@lang('You have') {{ $adminNotificationCount }} @lang('unread notification')</p>
                            @endif
                        </div>
                        <div class="dropdown-menu__body @if (blank($adminNotifications)) d-flex justify-content-center align-items-center @endif">
                            @forelse($adminNotifications as $notification)
                                <a class="dropdown-menu__item" href="{{ route('admin.notification.read', $notification->id) }}">
                                    <div class="navbar-notifi">
                                        <div class="navbar-notifi__right">
                                            <h6 class="notifi__title">{{ __($notification->title) }}</h6>
                                            <span class="time"><i class="far fa-clock"></i>
                                                {{ diffForHumans($notification->created_at) }}</span>
                                        </div>
                                    </div>
                                </a>
                            @empty
                                <div class="empty-notification text-center">
                                    <img src="{{ getImage('assets/images/empty_list.png') }}" alt="empty">
                                    <p class="mt-3">@lang('No unread notification found')</p>
                                </div>
                            @endforelse
                        </div>
                        <div class="dropdown-menu__footer">
                            <a class="view-all-message" href="{{ route('admin.notifications') }}">@lang('View all notifications')</a>
                        </div>
                    </div>
                </li>
            @endpermit
            @permit('admin.system.setting')
                <li>
                    <button class="primary--layer" data-bs-toggle="tooltip" data-bs-placement="bottom" type="button" title="@lang('System Setting')">
                        <a href="{{ route('admin.setting.system') }}"><i class="las la-wrench"></i></a>
                    </button>
                </li>
            @endpermit
            <li class="dropdown d-flex profile-dropdown">
                <button data-bs-toggle="dropdown" data-display="static" type="button" aria-haspopup="true" aria-expanded="false">
                    <span class="navbar-user">
                        <span class="navbar-user__thumb"><img src="{{ getImage(getFilePath('adminProfile') . '/' . auth()->guard('admin')->user()->image, getFileSize('adminProfile')) }}" alt="image"></span>
                        <span class="navbar-user__info">
                            <span class="navbar-user__name">{{ auth()->guard('admin')->user()->username }}</span>
                        </span>
                        <span class="icon"><i class="las la-chevron-circle-down"></i></span>
                    </span>
                </button>
                <div class="dropdown-menu dropdown-menu--sm p-0 border-0 box--shadow1 dropdown-menu-right">
                    <a class="dropdown-menu__item d-flex align-items-center px-3 py-2" href="{{ route('admin.profile') }}">
                        <i class="dropdown-menu__icon las la-user-circle"></i>
                        <span class="dropdown-menu__caption">@lang('Profile')</span>
                    </a>

                    <a class="dropdown-menu__item d-flex align-items-center px-3 py-2" href="{{ route('admin.password') }}">
                        <i class="dropdown-menu__icon las la-key"></i>
                        <span class="dropdown-menu__caption">@lang('Password')</span>
                    </a>

                    <a class="dropdown-menu__item d-flex align-items-center px-3 py-2" href="{{ route('admin.logout') }}">
                        <i class="dropdown-menu__icon las la-sign-out-alt"></i>
                        <span class="dropdown-menu__caption">@lang('Logout')</span>
                    </a>
                </div>
                <button class="breadcrumb-nav-open ms-2 d-none" type="button">
                    <i class="las la-sliders-h"></i>
                </button>
            </li>
        </ul>
    </div>
</nav>
<!-- navbar-wrapper end -->

@push('script')
    <script>
        "use strict";
        var routes = @json($routesData);
        var settingsData = Object.assign({}, @json($settings), @json($sidenav));

        $('.navbar__action-list .dropdown-menu').on('click', function(event) {
            event.stopPropagation();
        });
    </script>
    <script src="{{ asset('assets/admin/js/search.js') }}"></script>
    <script>
        "use strict";

        function getEmptyMessage() {
            return `<li class="text-muted">
                <div class="empty-search text-center">
                    <img src="{{ getImage('assets/images/empty_list.png') }}" alt="empty">
                    <p class="text-muted">No search result found</p>
                </div>
            </li>`
        }
    </script>
@endpush
