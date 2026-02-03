@php
    $menuBuilder = app(\App\Services\SidebarMenuBuilder::class);
    $menuItems = $menuBuilder->build();
@endphp

<nav class="fi-sidebar-nav">
    <ul class="fi-sidebar-nav-items space-y-1" role="list">
        @foreach($menuItems as $item)
            <x-sidebar.item :item="$item" :level="0" />
        @endforeach
    </ul>
</nav>
