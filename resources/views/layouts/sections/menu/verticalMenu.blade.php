@php
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\Route;

    $user = Auth::user();
    $pegawai = $user->pegawai ?? null;
    $jabatanUser = strtolower($pegawai->jabatan ?? ''); // Contoh: 'hr', 'supervisor', 'staff', 'admin', dll

    /**
     * Fungsi rekursif untuk filter menu & submenu berdasarkan visibleTo dan hidden
     */
    function filterMenuByRole($menuItems, $jabatanUser)
    {
        $filtered = [];

        foreach ($menuItems as $menu) {
            // Skip jika hidden
            if (isset($menu->hidden) && $menu->hidden) {
                continue;
            }

            // Skip jika ada visibleTo tapi user tidak termasuk
            if (isset($menu->visibleTo)) {
                $allowedRoles = collect($menu->visibleTo)->map(fn($r) => strtolower($r))->toArray();
                if (!in_array($jabatanUser, $allowedRoles)) {
                    continue;
                }
            }

            // Jika ada submenu → filter ulang secara rekursif
            if (isset($menu->submenu)) {
                $menu->submenu = filterMenuByRole($menu->submenu, $jabatanUser);

                // Jika setelah difilter submenu kosong → skip parent-nya
                if (empty($menu->submenu)) {
                    continue;
                }
            }

            $filtered[] = $menu;
        }

        return $filtered;
    }

    // Filter menu utama
    $filteredMenu = filterMenuByRole($menuData[0]->menu, $jabatanUser);
@endphp

<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <!-- Brand -->
    <div class="app-brand demo">
        <a href="{{ url('/') }}" class="app-brand-link">
            <span class="app-brand-logo demo">@include('_partials.macros', ['width' => 25, 'withbg' => 'var(--bs-primary)'])</span>
            <span class="app-brand-text demo menu-text fw-bold ms-2">SB</span>
        </a>

        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
            <i class="bx bx-chevron-left bx-sm d-flex align-items-center justify-content-center"></i>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">
        @foreach ($filteredMenu as $menu)
            {{-- Skip jika hidden (double guard) --}}
            @continue(isset($menu->hidden) && $menu->hidden)

            {{-- Menu Header --}}
            @if (isset($menu->menuHeader))
                <li class="menu-header small text-uppercase">
                    <span class="menu-header-text">{{ __($menu->menuHeader) }}</span>
                </li>
            @else
                @php
                    $activeClass = null;
                    $currentRouteName = Route::currentRouteName();

                    // Exact match untuk slug tunggal
                    if ($currentRouteName === $menu->slug) {
                        $activeClass = 'active';
                    }

                    // Jika slug berupa array
                    if (is_array($menu->slug ?? null)) {
                        foreach ($menu->slug as $slug) {
                            if (str_contains($currentRouteName, $slug) && strpos($currentRouteName, $slug) === 0) {
                                $activeClass = 'active';
                                break;
                            }
                        }
                    }
                    // Jika punya submenu
                    elseif (isset($menu->submenu)) {
                        if (
                            str_contains($currentRouteName, $menu->slug ?? '') &&
                            strpos($currentRouteName, $menu->slug ?? '') === 0
                        ) {
                            $activeClass = 'active open';
                        }
                    }
                @endphp

                {{-- Main Menu --}}
                <li class="menu-item {{ $activeClass }}">
                    <a href="{{ isset($menu->url) ? url($menu->url) : 'javascript:void(0);' }}"
                        class="{{ isset($menu->submenu) ? 'menu-link menu-toggle' : 'menu-link' }}"
                        @if (isset($menu->target) && !empty($menu->target)) target="_blank" @endif>
                        @isset($menu->icon)
                            <i class="{{ $menu->icon }}"></i>
                        @endisset
                        <div>{{ isset($menu->name) ? __($menu->name) : '' }}</div>

                        @isset($menu->badge)
                            <div class="badge rounded-pill bg-{{ $menu->badge[0] }} text-uppercase ms-auto">
                                {{ $menu->badge[1] }}
                            </div>
                        @endisset
                    </a>

                    {{-- Submenu --}}
                    @isset($menu->submenu)
                        @include('layouts.sections.menu.submenu', ['menu' => $menu->submenu])
                    @endisset
                </li>
            @endif
        @endforeach
    </ul>
</aside>
