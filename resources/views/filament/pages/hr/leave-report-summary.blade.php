<div class="space-y-4">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-primary-50 dark:bg-primary-900/20 p-4 rounded-lg border border-primary-200 dark:border-primary-800">
            <div class="text-sm font-medium text-primary-600 dark:text-primary-400">
                {{ tr('stats.total_requests', [], null, 'dashboard') ?: 'Total Requests' }}
            </div>
            <div class="text-2xl font-bold text-primary-900 dark:text-primary-100 mt-1">
                {{ $stats['total'] ?? 0 }}
            </div>
        </div>
        
        <div class="bg-warning-50 dark:bg-warning-900/20 p-4 rounded-lg border border-warning-200 dark:border-warning-800">
            <div class="text-sm font-medium text-warning-600 dark:text-warning-400">
                {{ tr('stats.pending_requests', [], null, 'dashboard') ?: 'Pending' }}
            </div>
            <div class="text-2xl font-bold text-warning-900 dark:text-warning-100 mt-1">
                {{ $stats['pending'] ?? 0 }}
            </div>
        </div>
        
        <div class="bg-success-50 dark:bg-success-900/20 p-4 rounded-lg border border-success-200 dark:border-success-800">
            <div class="text-sm font-medium text-success-600 dark:text-success-400">
                {{ tr('stats.approved_requests', [], null, 'dashboard') ?: 'Approved' }}
            </div>
            <div class="text-2xl font-bold text-success-900 dark:text-success-100 mt-1">
                {{ $stats['approved'] ?? 0 }}
            </div>
        </div>
        
        <div class="bg-danger-50 dark:bg-danger-900/20 p-4 rounded-lg border border-danger-200 dark:border-danger-800">
            <div class="text-sm font-medium text-danger-600 dark:text-danger-400">
                {{ tr('stats.rejected_requests', [], null, 'dashboard') ?: 'Rejected' }}
            </div>
            <div class="text-2xl font-bold text-danger-900 dark:text-danger-100 mt-1">
                {{ $stats['rejected'] ?? 0 }}
            </div>
        </div>
    </div>
</div>

