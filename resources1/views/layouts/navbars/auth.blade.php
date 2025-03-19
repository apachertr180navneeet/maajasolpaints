<div class="sidebar" data-color="white" data-active-color="danger">
    <div class="logo">
        <span class="simple-text logo-normal text-center">
            {{ __(config('app.name')) }}
        </span>
    </div>
    <div class="sidebar-wrapper">
        <ul class="nav">
            <!-- Dashboard Menu Item -->
            <li class="{{ ($activeSection ?? 'false') == 'dashboard' ? 'active' : '' }}">
                <a href="{{ route('admin.dashboard') }}">
                    <i class="nc-icon nc-bank"></i>
                    <p>{{ __('Dashboard') }}</p>
                </a>
            </li>

            <li class="{{ ($activeSection ?? 'false') == 'slider' ? 'active' : '' }}">
                <a href="{{ route('admin.sliders.index') }}">
                    <i class="fas fa-sliders-h"></i>
                    <span class="sidebar-normal" style="font-weight: 600;">{{ __('Sliders') }}</span>
                </a>
            </li>
            <li
                class="nav-item {{ ($activeSection ?? '') == 'qr' || ($activeSection ?? '') == 'used_qr' ? 'active' : '' }}">
                <a class="nav-link" data-toggle="collapse" href="#qrDropdown" aria-expanded="false">
                    <i class="fas fa-qrcode"></i>
                    <p>
                        {{ __('QR Management') }}
                        <b class="caret"></b>
                    </p>
                </a>
                <div class="collapse {{ ($activeSection ?? '') == 'qr' || ($activeSection ?? '') == 'used_qr' ? 'show' : '' }}"
                    id="qrDropdown">
                    <ul class="nav">
                        <li class="nav-item ml-3 {{ ($activeSection ?? '') == 'qr' ? 'active' : '' }}">
                            <!-- Added ml-3 for left margin -->
                            <a class="nav-link" href="{{ route('admin.qr') }}">
                                <i class="fas fa-qrcode"></i>
                                <span class="sidebar-normal">{{ __('QR') }}</span>
                            </a>
                        </li>
                        <li class="nav-item ml-3 {{ ($activeSection ?? '') == 'used_qr' ? 'active' : '' }}">
                            <!-- Added ml-3 for left margin -->
                            <a class="nav-link" href="{{ route('admin.usedQr') }}">
                                <i class="fas fa-qrcode"></i>
                                <span class="sidebar-normal">{{ __('Used QR') }}</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            <li
                class="{{ ($activeSection ?? 'false') == 'gift' || ($activeSection ?? 'false') == 'gifts' ? 'active' : '' }}">
                <a href="{{ route('admin.gift') }}">
                    <i class="fas fa-gift"></i>
                    <span class="sidebar-normal" style="font-weight: 600;">{{ __('Gift') }}</span>
                </a>
            </li>
            <li
                class="nav-item {{ ($activeSection ?? 'false') == 'redeem' || ($activeSection ?? 'false') == 'redemption' ? 'active' : '' }}">
                <a class="nav-link" data-toggle="collapse" href="#redeemManagement" aria-expanded="false">
                    <i class="fas fa-star"></i>
                    <p>
                        {{ __('Redeem Requests') }}
                        <b class="caret"></b>
                    </p>
                </a>
                <div class="collapse {{ ($activeSection ?? 'false') == 'redeem' || ($activeSection ?? 'false') == 'redemption' ? 'show' : '' }}"
                    id="redeemManagement">
                    <ul class="nav">
                        <li class="nav-item ml-3 {{ request()->is('admin/redemption/pending') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ route('admin.redemption', ['status' => 'pending']) }}">
                                <i class="fas fa-clock"></i>
                                <span class="sidebar-normal">{{ __('Pending Requests') }}</span>
                            </a>
                        </li>
                        <li class="nav-item ml-3 {{ request()->is('admin/redemption/approved') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ route('admin.redemption', ['status' => 'approved']) }}">
                                <i class="fas fa-check"></i>
                                <span class="sidebar-normal">{{ __('Approved Requests') }}</span>
                            </a>
                        </li>
                        {{-- <li class="nav-item ml-3 {{ request()->is('admin/redemption/completed') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ route('admin.redemption', ['status' => 'completed']) }}">
                                <i class="fas fa-check-double"></i>
                                <span class="sidebar-normal">{{ __('Completed Requests') }}</span>
                            </a>
                        </li> --}}
                        <li class="nav-item ml-3 {{ request()->is('admin/redemption/rejected') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ route('admin.redemption', ['status' => 'rejected']) }}">
                                <i class="fas fa-times"></i>
                                <span class="sidebar-normal">{{ __('Rejected Requests') }}</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            <li
                class="nav-item {{ ($activeSection ?? 'false') == 'users' || ($activeSection ?? 'false') == 'user-management' ? 'active' : '' }}">
                <a class="nav-link" data-toggle="collapse" href="#userManagement" aria-expanded="false">
                    <i class="fas fa-users"></i>
                    <p>
                        {{ __('User Management') }}
                        <b class="caret"></b>
                    </p>
                </a>
                <div class="collapse {{ ($activeSection ?? 'false') == 'users' || ($activeSection ?? 'false') == 'user-management' ? 'show' : '' }}"
                    id="userManagement">
                    <ul class="nav">
                        <li class="nav-item ml-3 {{ request()->is('admin/users-status/all') || request()->is('admin/users-status') ? 'active' : '' }}">
                            <!-- Added ml-3 for left margin -->
                            <a class="nav-link" href="{{ route('admin.users', ['status' => 'all']) }}">
                                <i class="fas fa-users"></i> <!-- All users icon -->
                                <span class="sidebar-normal">{{ __('All Users') }}</span>
                            </a>
                        </li>
                        <li class="nav-item ml-3 {{ request()->is('admin/users-status/active') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ route('admin.users', 'active') }}">
                                <i class="fas fa-user-check"></i> <!-- Active users icon -->
                                <span class="sidebar-normal">{{ __('Active Users') }}</span>
                            </a>
                        </li>
                        <li class="nav-item ml-3 {{ request()->is('admin/users-status/inactive') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ route('admin.users', 'inactive') }}">
                                <i class="fas fa-user-times"></i> <!-- Inactive users icon -->
                                <span class="sidebar-normal">{{ __('Inactive Users') }}</span>
                            </a>
                        </li>
                        <li class="nav-item ml-3 {{ request()->is('admin/users-status/pending') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ route('admin.users', 'pending') }}">
                                <i class="fas fa-user-clock"></i> <!-- Pending users icon -->
                                <span class="sidebar-normal">{{ __('Pending Users') }}</span>
                            </a>
                        </li>
                        <li class="nav-item ml-3 {{ request()->is('admin/users-status/blocked') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ route('admin.users', 'blocked') }}">
                                <i class="fas fa-user-lock"></i> <!-- Blocked users icon -->
                                <span class="sidebar-normal">{{ __('Blocked Users') }}</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>


            <li class="{{ ($activeSection ?? 'false') == 'settings' ? 'active' : '' }}">
                <div class="" id="settingsSection">
                    <ul class="nav">
                        <li class="{{ ($activeSection ?? 'false') == 'settings' ? 'active' : '' }}">
                            <a href="{{ route('admin.settings.edit') }}">
                                <i class="fas fa-cogs"></i>
                                <span class="sidebar-normal" style="font-weight: 600;">{{ __('Settings') }}</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
        </ul>
    </div>
</div>
