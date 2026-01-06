<x-filament-panels::page>
    <div class="space-y-6">
        <form wire:submit="loadEmployees">
            {{ $this->form }}
            
            <div class="mt-4">
                <x-filament::button type="button" wire:click="loadEmployees">
                    {{ tr('actions.show_results', [], null, 'dashboard') ?: 'Show Employees' }}
                </x-filament::button>
            </div>
        </form>

        @if($this->employees->isNotEmpty())
            <div class="space-y-4">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b">
                                <th class="text-left p-2">{{ tr('fields.employee_number', [], null, 'dashboard') ?: 'Employee Number' }}</th>
                                <th class="text-left p-2">{{ tr('fields.employee_name', [], null, 'dashboard') ?: 'Employee Name' }}</th>
                                <th class="text-left p-2">{{ tr('fields.department', [], null, 'dashboard') ?: 'Department' }}</th>
                                <th class="text-left p-2">{{ tr('fields.position', [], null, 'dashboard') ?: 'Position' }}</th>
                                <th class="text-left p-2">{{ tr('fields.work_place', [], null, 'dashboard') ?: 'Work Place' }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($this->employees as $employee)
                                <tr class="border-b">
                                    <td class="p-2">{{ $employee->employee_number }}</td>
                                    <td class="p-2">{{ $employee->full_name }}</td>
                                    <td class="p-2">{{ $employee->department->name ?? '' }}</td>
                                    <td class="p-2">{{ $employee->position->title ?? '' }}</td>
                                    <td class="p-2">
                                        <select 
                                            wire:model="assignments.{{ $employee->id }}"
                                            class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"
                                        >
                                            <option value="">{{ tr('fields.none', [], null, 'dashboard') ?: 'None' }}</option>
                                            @foreach(\App\Models\HR\WorkPlace::active()->get() as $workPlace)
                                                <option value="{{ $workPlace->id }}">{{ $workPlace->name }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div>
                    <x-filament::button wire:click="saveAssignments" color="success">
                        {{ tr('actions.save', [], null, 'dashboard') ?: 'Save' }}
                    </x-filament::button>
                </div>
            </div>
        @endif
    </div>
</x-filament-panels::page>

