<div
    x-data="{
        searchQuery: '',
        filterSidebar() {
            const query = this.searchQuery.toLowerCase().trim();
            const sidebarItems = document.querySelectorAll('.fi-sidebar-item, .fi-sidebar-group-item');
            const sidebarGroups = document.querySelectorAll('.fi-sidebar-group');
            
            if (!query) {
                sidebarItems.forEach(item => {
                    item.style.display = '';
                });
                sidebarGroups.forEach(group => {
                    group.style.display = '';
                });
                return;
            }
            
            let hasVisibleItems = false;
            
            sidebarItems.forEach(item => {
                const text = item.textContent?.toLowerCase() || '';
                if (text.includes(query)) {
                    item.style.display = '';
                    hasVisibleItems = true;
                    
                    let parentGroup = item.closest('.fi-sidebar-group');
                    while (parentGroup) {
                        parentGroup.style.display = '';
                        parentGroup = parentGroup.parentElement?.closest('.fi-sidebar-group') || null;
                    }
                } else {
                    item.style.display = 'none';
                }
            });
            
            sidebarGroups.forEach(group => {
                const groupItems = group.querySelectorAll('.fi-sidebar-item, .fi-sidebar-group-item');
                const hasVisibleChildren = Array.from(groupItems).some(item => 
                    item.style.display !== 'none' && 
                    (item.textContent?.toLowerCase() || '').includes(query)
                );
                
                if (!hasVisibleChildren) {
                    const groupLabel = group.querySelector('.fi-sidebar-group-label');
                    const groupLabelText = groupLabel?.textContent?.toLowerCase() || '';
                    
                    if (!groupLabelText.includes(query)) {
                        group.style.display = 'none';
                    } else {
                        group.style.display = '';
                    }
                } else {
                    group.style.display = '';
                }
            });
        }
    }"
    class="flex items-center gap-2"
>
    <div class="relative flex-1 max-w-md">
        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
        </div>
        <input
            type="text"
            x-model="searchQuery"
            @input="filterSidebar()"
            placeholder="Search"
            class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg bg-white text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent sm:text-sm"
        />
    </div>
</div>
