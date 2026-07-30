@props(['name', 'size' => 18])
<svg {{ $attributes->merge(['class' => 'ui-icon']) }} width="{{ $size }}" height="{{ $size }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
@switch($name)
    @case('dashboard')<rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/>@break
    @case('users')<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>@break
    @case('group')<path d="M8 21v-2a4 4 0 0 1 4-4h4a4 4 0 0 1 4 4v2"/><circle cx="14" cy="7" r="4"/><path d="M4 4v6M1 7h6"/>@break
    @case('truck')<path d="M3 6h11v10H3zM14 10h4l3 3v3h-7z"/><circle cx="7" cy="18" r="2"/><circle cx="18" cy="18" r="2"/>@break
    @case('map')<path d="m3 6 6-3 6 3 6-3v15l-6 3-6-3-6 3zM9 3v15M15 6v15"/>@break
    @case('box')<path d="m21 8-9 5-9-5 9-5zM3 8v8l9 5 9-5V8M12 13v8"/>@break
    @case('wallet')<path d="M4 5h14a2 2 0 0 1 2 2v12H4a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2z"/><path d="M16 12h6v4h-6a2 2 0 0 1 0-4z"/>@break
    @case('report')<path d="M4 3h16v18H4zM8 7h8M8 11h8M8 15h5"/>@break
    @case('filter')<path d="M4 5h16l-6 7v5l-4 2v-7z"/>@break
    @case('logout')<path d="M10 17l5-5-5-5M15 12H3M15 4h4a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2h-4"/>@break
    @case('pin')<path d="M20 10c0 5-8 11-8 11S4 15 4 10a8 8 0 1 1 16 0z"/><circle cx="12" cy="10" r="2"/>@break
    @default<circle cx="12" cy="12" r="8"/>
@endswitch
</svg>
