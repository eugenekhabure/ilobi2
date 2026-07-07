@php
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\DB;
    use Carbon\Carbon;

    $userId           = Auth::id();
    $showBackendMenus = true;
    $eligibleDate     = null;
    $expired          = false;
    $daysDifference   = null;

    if ($userId != 1) {
        $packageRequest = DB::table('package_requests')
            ->where('userid', $userId)
            ->whereNotNull('admin_approved')
            ->latest('id')
            ->first();

        if ($packageRequest) {
            $eligibleDate = Carbon::parse($packageRequest->admin_approved)
                                ->addDays($packageRequest->days);

            if (Carbon::now()->greaterThan($eligibleDate)) {
                // Expired
                $showBackendMenus = false;
                $expired = true;
                $daysDifference = $eligibleDate->diffInDays(Carbon::now());
            } elseif (Carbon::now()->isSameDay($eligibleDate)) {
                // Today is last day (consider expired today)
                $showBackendMenus = false;
                $expired = true;
                $daysDifference = 0;
            } else {
                // Still active
                $daysDifference = Carbon::now()->diffInDays($eligibleDate);
            }
        } else {
            $showBackendMenus = false;
        }
    }
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
            @if($showBackendMenus)
                {!! $backendMenus !!}
            @endif

            <li class="nav-item dropdown">
                <a class="nav-link has-dropdown">
                    <i class="fas fa-archive"></i> <span>Subscription</span>
                </a>
                <ul class="dropdown-menu">
                    <li><a class="nav-link" href="{{ route('subscription.purchase') }}">
                        <i class="fas fa-gift"></i> <span>Purchase</span></a>
                    </li>
                    @if($userId == 1)
                        <li><a class="nav-link" href="{{ route('admin.packages.index') }}">
                            <i class="fas fa-gift"></i> <span>Manage Packages</span></a>
                        </li>
                        <li><a class="nav-link" href="{{ route('subscription.requests') }}">
                            <i class="fas fa-history"></i> <span>Purchase Requests</span></a>
                        </li>
                    @endif
                </ul>
            </li>

            {{-- Show Subscription Info --}}
            @if($userId != 1 && $eligibleDate)
                <li class="menu-header">Subscription Info</li>
                <li class="nav-item">
                    <div class="p-3 m-2 rounded-lg text-center" style="background: #f8f9fa; border: 1px solid #ddd;">
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
