<li class="dropdown dropdown-list-toggle">
    <a href="#" data-toggle="dropdown" class="nav-link nav-link-lg d-flex align-items-center gap-2">
        @php
            $locale = session('locale', app()->getLocale());
            $flags = [
                'en' => '🇬🇧',
                'sw' => '🇰🇪',
                'fr' => '🇫🇷',
                'de' => '🇩🇪',
                'zh' => '🇨🇳',
            ];
            $names = [
                'en' => 'English',
                'sw' => 'Kiswahili',
                'fr' => 'Français',
                'de' => 'Deutsch',
                'zh' => '中文',
            ];
        @endphp
        <span style="font-size: 20px;">{{ $flags[$locale] ?? '🌍' }}</span>
        <span class="d-none d-sm-inline">{{ $names[$locale] ?? 'Language' }}</span>
        <i class="fas fa-chevron-down d-none d-sm-inline" style="font-size: 12px;"></i>
    </a>
    <div class="dropdown-menu dropdown-menu-right" style="min-width: 180px;">
        <div class="dropdown-title">Choose Language</div>
        @foreach(['en', 'sw', 'fr', 'de', 'zh'] as $code)
            <a href="{{ route('language.switch', $code) }}" class="dropdown-item d-flex align-items-center gap-3 {{ session('locale', app()->getLocale()) == $code ? 'active' : '' }}">
                <span style="font-size: 20px;">{{ $flags[$code] }}</span>
                <span>{{ $names[$code] }}</span>
                @if(session('locale', app()->getLocale()) == $code)
                    <i class="fas fa-check ms-auto text-primary"></i>
                @endif
            </a>
        @endforeach
    </div>
</li>