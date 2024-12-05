<!-- Dashboard Sidebar
        ================================================== -->
<div class="dashboard-sidebar">
    <div class="dashboard-sidebar-inner" data-simplebar>
        <div class="dashboard-nav-container">

            <!-- Responsive Navigation Trigger -->
            <a href="#" class="dashboard-responsive-nav-trigger">
					<span class="hamburger hamburger--collapse">
						<span class="hamburger-box">
							<span class="hamburger-inner"></span>
						</span>
					</span>
                <span class="trigger-title">{{ ___('Dashboard Navigation') }}</span>
            </a>
            <!-- Navigation -->
            <div class="dashboard-nav">
                <div class="dashboard-nav-inner">

                    <ul data-submenu-title="{{ ___('My Account') }}">
                        <li class="{{ request()->route()->getName() == 'dashboard' ? 'active' : '' }}"><a
                                href="{{ route('dashboard') }}"><i
                                    class="icon-feather-grid"></i> {{ ___('Dashboard') }}</a></li>

                        <li class="{{ request()->route()->getName() == 'subscription' ? 'active' : '' }}"><a
                                href="{{ route('subscription') }}"><i
                                    class="icon-feather-gift"></i> {{ ___('Membership') }}</a></li>
                    </ul>

                    <ul data-submenu-title="{{ ___('Organize and Manage') }}">
                        <li class="{{ request()->route()->getName() == 'restaurants.create' ? 'active' : '' }}"><a
                                href="{{ route('restaurants.create') }}"><i
                                    class="icon-feather-plus-square"></i> {{ ___('Add Restaurant') }}</a></li>
                        <li class="{{ request()->route()->getName() == 'restaurants.index' || request()->route()->getName() == 'restaurants.qrbuilder' ? 'active' : '' }}"><a
                                href="{{ route('restaurants.index') }}"><i
                                    class="far fa-utensils"></i> {{ ___('My Restaurants') }}</a></li>
                        <li class="{{ request()->route()->getName() == 'restaurants.orders' ? 'active' : '' }}"><a
                                href="{{ route('restaurants.orders') }}"><i
                                    class="icon-feather-package"></i> {{ ___('Orders') }}</a></li>
                    </ul>

                    <ul data-submenu-title="{{ ___('Account') }}">
                        <li class="{{ request()->route()->getName() == 'transactions' ? 'active' : '' }}"><a
                                href="{{ route('transactions') }}"><i
                                    class="icon-feather-file-text"></i> {{ ___('Transactions') }}</a></li>
                        <li class="{{ request()->route()->getName() == 'settings' ? 'active' : '' }}"><a
                                href="{{ route('settings') }}"><i
                                    class="icon-feather-settings"></i> {{ ___('Account Setting') }}</a>
                        </li>
                        <li><a href="{{ route('logout') }}"
                               onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i
                                    class="icon-feather-log-out"></i> {{ ___('Logout') }}</a>
                        </li>
                    </ul>

                </div>
            </div>
            <!-- Navigation / End -->
        </div>
    </div>
</div>
<!-- Dashboard Sidebar / End -->
