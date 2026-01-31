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
        <input
            type="text"
            x-model="searchQuery"
            @input="filterSidebar()"
            placeholder="Search sidebar..."
            class="fi-input-wrp block w-full rounded-lg border-none bg-white px-3 py-1.5 text-base text-gray-950 outline-none transition duration-75 placeholder:text-gray-400 focus:ring-2 focus:ring-primary-500 dark:bg-white/5 dark:text-white dark:placeholder:text-gray-500 dark:focus:ring-primary-400 sm:text-sm sm:leading-6"
        />
        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-2">
            <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
        </div>
    </div>
</div>
