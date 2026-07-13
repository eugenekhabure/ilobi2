@php
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\DB;
    use Carbon\Carbon;

    $userId           = Auth::id();
    $showBackendMenus = true;
    $eligibleDate     = null;
    $expired          = false;
    $daysDifference   = null;

    // Properly fetch the approved package request with package details
    if ($userId != 1) {
        $packageRequest = DB::table('package_requests')
            ->join('packages', 'package_requests.package_id', '=', 'packages.id')
            ->where('package_requests.user_id', $userId)
            ->where('package_requests.status', 'approved')
            ->select('package_requests.*', 'packages.days')
            ->latest('package_requests.id')
            ->first();

        if ($packageRequest) {
            $eligibleDate = Carbon::parse($packageRequest->approval_date ?? $packageRequest->created_at)
                                ->addDays($packageRequest->days ?? 30);

            if (Carbon::now()->greaterThan($eligibleDate)) {
                $showBackendMenus = false;
                $expired = true;
                $daysDifference = $eligibleDate->diffInDays(Carbon::now());
            } elseif (Carbon::now()->isSameDay($eligibleDate)) {
                $showBackendMenus = false;
                $expired = true;
                $daysDifference = 0;
            } else {
                $daysDifference = Carbon::now()->diffInDays($eligibleDate);
            }
        } else {
            $showBackendMenus = false;
        }
    }

    // Get the current user's facility type
    $user = Auth::user();
    $facilityType = null;
    if ($user && $user->facility_id) {
        $facility = DB::table('facilities')->where('id', $user->facility_id)->first();
        if ($facility) {
            $facilityType = $facility->type;
        }
    }

    // Determine if user is Super Admin (user_id = 1)
    $isSuperAdmin = ($userId == 1);
    // Determine if user is Client Admin (has organization_id but not super admin)
    $isClientAdmin = ($userId != 1 && $user && $user->organization_id);
    // Determine if user is Employee or Resident
    $isEmployee = ($userId != 1 && $user && $user->employee);
    $isResident = ($userId != 1 && $user && $user->person && $user->person->residentProfile);
@endphp


<div class="main-sidebar">
    <aside id="sidebar-wrapper">
        <div class="sidebar-brand">
            <a href="{{ route('admin.dashboard.index') }}">{{ setting('site_name') }}</a>
        </div>
        <div class="sidebar-brand sidebar-brand-sm">
            <a href="{{ route('admin.dashboard.index') }}">
                <?php 
                    if(setting('site_name')) {
                        $sitenames = explode(' ', setting('site_name'));
                        if(count($sitenames) > 1) {
                            foreach ($sitenames as $sitename) {
                                echo $sitename[0];
                            }
                        } else {
                            echo substr(setting('site_name'), 0, 2);
                        }
                    }
                ?>
            </a>
        </div>

        <ul class="sidebar-menu">
            {{-- Show backend menus (from database) --}}
            @if($showBackendMenus)
                {!! $backendMenus !!}
            @endif

            {{-- ============================================ --}}
            {{-- 🏠 RESIDENTIAL MODULE (Only for Super Admin) --}}
            {{-- ============================================ --}}
            @if($isSuperAdmin)
            <li class="nav-item dropdown">
                <a class="nav-link has-dropdown" href="#">
                    <i class="fas fa-home"></i> <span>🏠 Residential</span>
                </a>
                <ul class="dropdown-menu">
                    <li><a class="nav-link" href="{{ route('admin.sub-units.index') }}">
                        <i class="fas fa-layer-group"></i> Sub Units (Blocks/Apartments)</a>
                    </li>
                    <li><a class="nav-link" href="{{ route('admin.resident-profiles.index') }}">
                        <i class="fas fa-users"></i> Resident Profiles</a>
                    </li>
                    <li><a class="nav-link" href="{{ route('admin.people.index') }}">
                        <i class="fas fa-user"></i> People</a>
                    </li>
                    <li><a class="nav-link" href="{{ route('admin.vehicles.index') }}">
                        <i class="fas fa-car"></i> Vehicles</a>
                    </li>
                    <li><a class="nav-link" href="{{ route('admin.deliveries.index') }}">
                        <i class="fas fa-box"></i> Deliveries</a>
                    </li>
                    <li><a class="nav-link" href="{{ route('admin.invitations.index') }}">
                        <i class="fas fa-envelope"></i> Invitations</a>
                    </li>
                </ul>
            </li>
            @endif

            {{-- ============================================ --}}
            {{-- 🏢 RESIDENTIAL MODULE (For Client Admin with Residential facility) --}}
            {{-- ============================================ --}}
            @if($isClientAdmin && $facilityType == 'residential')
            <li class="nav-item dropdown">
                <a class="nav-link has-dropdown" href="#">
                    <i class="fas fa-home"></i> <span>🏠 Residential</span>
                </a>
                <ul class="dropdown-menu">
                    <li><a class="nav-link" href="{{ route('admin.sub-units.index') }}">
                        <i class="fas fa-layer-group"></i> Sub Units (Blocks/Apartments)</a>
                    </li>
                    <li><a class="nav-link" href="{{ route('admin.resident-profiles.index') }}">
                        <i class="fas fa-users"></i> Resident Profiles</a>
                    </li>
                    <li><a class="nav-link" href="{{ route('admin.people.index') }}">
                        <i class="fas fa-user"></i> People</a>
                    </li>
                    <li><a class="nav-link" href="{{ route('admin.vehicles.index') }}">
                        <i class="fas fa-car"></i> Vehicles</a>
                    </li>
                    <li><a class="nav-link" href="{{ route('admin.deliveries.index') }}">
                        <i class="fas fa-box"></i> Deliveries</a>
                    </li>
                    <li><a class="nav-link" href="{{ route('admin.invitations.index') }}">
                        <i class="fas fa-envelope"></i> Invitations</a>
                    </li>
                </ul>
            </li>
            @endif

            {{-- ============================================ --}}
            {{-- 🏢 COMMERCIAL MODULE (For Client Admin with Commercial facility) --}}
            {{-- ============================================ --}}
            @if($isClientAdmin && $facilityType == 'commercial')
            <li class="nav-item dropdown">
                <a class="nav-link has-dropdown" href="#">
                    <i class="fas fa-building"></i> <span>🏢 Commercial</span>
                </a>
                <ul class="dropdown-menu">
                    <li><a class="nav-link" href="{{ route('admin.sub-units.index') }}">
                        <i class="fas fa-layer-group"></i> Floors</a>
                    </li>
                    <li><a class="nav-link" href="{{ route('admin.people.index') }}">
                        <i class="fas fa-user"></i> People</a>
                    </li>
                    <li><a class="nav-link" href="{{ route('admin.vehicles.index') }}">
                        <i class="fas fa-car"></i> Vehicles</a>
                    </li>
                    <li><a class="nav-link" href="{{ route('admin.deliveries.index') }}">
                        <i class="fas fa-box"></i> Deliveries</a>
                    </li>
                    <li><a class="nav-link" href="{{ route('admin.invitations.index') }}">
                        <i class="fas fa-envelope"></i> Invitations</a>
                    </li>
                </ul>
            </li>
            @endif

            {{-- ============================================ --}}
            {{-- 🏢 CORPORATE MODULE (For Client Admin with Corporate facility) --}}
            {{-- ============================================ --}}
            @if($isClientAdmin && $facilityType == 'corporate')
            <li class="nav-item dropdown">
                <a class="nav-link has-dropdown" href="#">
                    <i class="fas fa-office"></i> <span>🏢 Corporate</span>
                </a>
                <ul class="dropdown-menu">
                    <li><a class="nav-link" href="{{ route('admin.people.index') }}">
                        <i class="fas fa-user"></i> People</a>
                    </li>
                    <li><a class="nav-link" href="{{ route('admin.vehicles.index') }}">
                        <i class="fas fa-car"></i> Vehicles</a>
                    </li>
                    <li><a class="nav-link" href="{{ route('admin.deliveries.index') }}">
                        <i class="fas fa-box"></i> Deliveries</a>
                    </li>
                    <li><a class="nav-link" href="{{ route('admin.invitations.index') }}">
                        <i class="fas fa-envelope"></i> Invitations</a>
                    </li>
                </ul>
            </li>
            @endif

            {{-- ============================================ --}}
            {{-- 📦 SUBSCRIPTION (Only for Super Admin) --}}
            {{-- ============================================ --}}
            @if($isSuperAdmin)
            <li class="nav-item dropdown">
                <a class="nav-link has-dropdown">
                    <i class="fas fa-archive"></i> <span>Subscription</span>
                </a>
                <ul class="dropdown-menu">
                    <li><a class="nav-link" href="{{ route('subscription.purchase') }}">
                        <i class="fas fa-gift"></i> <span>Purchase</span></a>
                    </li>
                    <li><a class="nav-link" href="{{ route('admin.packages.index') }}">
                        <i class="fas fa-gift"></i> <span>Manage Packages</span></a>
                    </li>
                    <li><a class="nav-link" href="{{ route('subscription.requests') }}">
                        <i class="fas fa-history"></i> <span>Purchase Requests</span></a>
                    </li>
                </ul>
            </li>
            @endif

            {{-- ============================================ --}}
            {{-- 👤 EMPLOYEE / RESIDENT LIMITED VIEW --}}
            {{-- ============================================ --}}
            @if($isEmployee || $isResident)
            <li class="nav-item">
                <a class="nav-link" href="{{ route('check-in') }}">
                    <i class="fas fa-sign-in-alt"></i> <span>Check In</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('checkout.index') }}">
                    <i class="fas fa-sign-out-alt"></i> <span>Check Out</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('admin.visitors.index') }}">
                    <i class="fas fa-users"></i> <span>My Visitors</span>
                </a>
            </li>
            @endif

            {{-- ============================================ --}}
            {{-- 📟 ACCESS DEVICES (Super Admin only) --}}
            {{-- ============================================ --}}
            @if($isSuperAdmin)
            <li class="nav-item">
                <a class="nav-link" href="{{ route('admin.access-devices.index') }}">
                    <i class="fas fa-microchip"></i> <span>Access Devices</span>
                </a>
            </li>
            @endif

            {{-- ============================================ --}}
            {{-- 📊 ANALYTICS (Super Admin & Client Admin) --}}
            {{-- ============================================ --}}
            @if($isSuperAdmin || $isClientAdmin)
            <li class="nav-item">
                <a class="nav-link" href="{{ route('admin.analytics.index') }}">
                    <i class="fas fa-chart-line"></i> <span>Analytics</span>
                </a>
            </li>
            @endif

            {{-- ============================================ --}}
            {{-- 📝 PRE-REGISTERS --}}
            {{-- ============================================ --}}
            @if($isSuperAdmin || $isClientAdmin)
            <li class="nav-item">
                <a class="nav-link" href="{{ route('admin.pre-registers.index') }}">
                    <i class="fas fa-calendar-plus"></i> <span>Pre-Registers</span>
                </a>
            </li>
            @endif

            {{-- ============================================ --}}
            {{-- 🚨 EMERGENCY ALERTS (Client Admin only) --}}
            {{-- ============================================ --}}
            @if($isClientAdmin && !$isSuperAdmin)
            <li class="nav-item">
                <a class="nav-link" href="{{ route('admin.emergency-alerts.index') }}">
                    <i class="fas fa-bell"></i> <span>Emergency Alerts</span>
                </a>
            </li>
            @endif

            {{-- ============================================ --}}
            {{-- 📢 BROADCASTS (Client Admin only) --}}
            {{-- ============================================ --}}
            @if($isClientAdmin && !$isSuperAdmin)
            <li class="nav-item">
                <a class="nav-link" href="{{ route('admin.broadcasts.index') }}">
                    <i class="fas fa-bullhorn"></i> <span>Broadcasts</span>
                </a>
            </li>
            @endif

            {{-- ============================================ --}}
            {{-- 📢 ANNOUNCEMENTS (Client Admin only) --}}
            {{-- ============================================ --}}
            @if($isClientAdmin && !$isSuperAdmin)
            <li class="nav-item">
                <a class="nav-link" href="{{ route('admin.announcements.index') }}">
                    <i class="fas fa-bullhorn"></i> <span>Announcements</span>
                </a>
            </li>
            @endif

            {{-- ============================================ --}}
            {{-- 📝 FEEDBACK (Client Admin only) --}}
            {{-- ============================================ --}}
            @if($isClientAdmin && !$isSuperAdmin)
            <li class="nav-item">
                <a class="nav-link" href="{{ route('admin.feedback.index') }}">
                    <i class="fas fa-star"></i> <span>Visitor Feedback</span>
                </a>
            </li>
            @endif

            {{-- ============================================ --}}
            {{-- 🔧 MAINTENANCE (Client Admin & Resident) --}}
            {{-- ============================================ --}}
            @if($isClientAdmin || $isResident)
            <li class="nav-item">
                <a class="nav-link" href="{{ route('admin.maintenance.index') }}">
                    <i class="fas fa-tools"></i> <span>Maintenance</span>
                </a>
            </li>
            @endif

            {{-- ============================================ --}}
            {{-- 🔧 MAINTENANCE CATEGORIES (Client Admin only) --}}
            {{-- ============================================ --}}
            @if($isClientAdmin && !$isSuperAdmin)
            <li class="nav-item">
                <a class="nav-link" href="{{ route('admin.maintenance-categories.index') }}">
                    <i class="fas fa-tags"></i> <span>Categories</span>
                </a>
            </li>
            @endif

            {{-- ============================================ --}}
            {{-- 🏊 AMENITIES (Client Admin only) --}}
            {{-- ============================================ --}}
            @if($isClientAdmin && !$isSuperAdmin)
            <li class="nav-item">
                <a class="nav-link" href="{{ route('admin.amenities.index') }}">
                    <i class="fas fa-swimmer"></i> <span>Amenities</span>
                </a>
            </li>
            @endif

            {{-- ============================================ --}}
            {{-- 📢 COMMUNITY FEED (Client Admin & Resident) --}}
            {{-- ============================================ --}}
            @if($isClientAdmin || $isResident)
            <li class="nav-item">
                <a class="nav-link" href="{{ route('admin.community.index') }}">
                    <i class="fas fa-users"></i> <span>Community Feed</span>
                </a>
            </li>
            @endif

            {{-- ============================================ --}}
            {{-- 👤 STAFF DIRECTORY (Client Admin & Resident) --}}
            {{-- ============================================ --}}
            @if($isClientAdmin || $isResident)
            <li class="nav-item">
                <a class="nav-link" href="{{ route('admin.staff.index') }}">
                    <i class="fas fa-address-book"></i> <span>Staff Directory</span>
                </a>
            </li>
            @endif

            {{-- ============================================ --}}
            {{-- 🏢 STAFF DEPARTMENTS (Client Admin only) --}}
            {{-- ============================================ --}}
            @if($isClientAdmin && !$isSuperAdmin)
            <li class="nav-item">
                <a class="nav-link" href="{{ route('admin.staff-departments.index') }}">
                    <i class="fas fa-building"></i> <span>Staff Depts</span>
                </a>
            </li>
            @endif

            {{-- ============================================ --}}
            {{-- 🔐 ZKTECO DEVICES (Super Admin only) --}}
            {{-- ============================================ --}}
            @if($isSuperAdmin)
            <li class="nav-item">
                <a class="nav-link" href="{{ route('admin.zkteco.index') }}">
                    <i class="fas fa-fingerprint"></i> <span>ZKTeco</span>
                </a>
            </li>
            @endif

            {{-- ============================================ --}}
            {{-- 📹 HIKVISION DEVICES (Super Admin only) --}}
            {{-- ============================================ --}}
            @if($isSuperAdmin)
            <li class="nav-item">
                <a class="nav-link" href="{{ route('admin.hikvision.index') }}">
                    <i class="fas fa-video"></i> <span>Hikvision</span>
                </a>
            </li>
            @endif

            {{-- ============================================ --}}
            {{-- 📅 GOOGLE CALENDAR (Super Admin & Client Admin) --}}
            {{-- ============================================ --}}
            @if($isSuperAdmin || $isClientAdmin)
            <li class="nav-item">
                <a class="nav-link" href="{{ route('admin.google-calendar.settings') }}">
                    <i class="fab fa-google"></i> <span>Google Calendar</span>
                </a>
            </li>
            @endif

            {{-- Show Subscription Info (for non-super-admin users) --}}
            @if($userId != 1 && $eligibleDate)
                <li class="menu-header">Subscription Info</li>
                <li class="nav-item">
                    <div class="p-3 m-2 rounded-lg text-center" style="background: #f8fafc; border: 1px solid #ddd;">
                        <i class="fas fa-calendar-alt fa-2x text-primary mb-2"></i>

                        @if($expired)
                            <div class="font-weight-bold text-danger">
                                {{ $daysDifference === 0 ? 'Expired Today' : 'Expired On' }}
                            </div>
                            <div class="text-danger">{{ $eligibleDate->format('d M Y') }}</div>
                            @if($daysDifference > 0)
                                <small class="text-muted">({{ $daysDifference }} days ago)</small>
                            @endif
                        @else
                            <div class="font-weight-bold text-success">
                                {{ $daysDifference === 0 ? 'Ends Today' : 'Ends On' }}
                            </div>
                            <div class="text-dark">{{ $eligibleDate->format('d M Y') }}</div>
                            @if($daysDifference > 0)
                                <small class="text-muted">({{ $daysDifference }} days left)</small>
                            @endif
                        @endif
                    </div>
                </li>
            @endif
        </ul>
    </aside>
</div>