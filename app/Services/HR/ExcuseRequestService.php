<?php

namespace App\Services\HR;

use App\Models\HR\ExcuseRequest;
use App\Models\User;
use App\Services\HR/HrNotificationService;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class ExcuseRequestService
{
    protected HrNotificationService $notificationService;

    public function __construct(HrNotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function getAll(array $filters = [])
    {
        $query = ExcuseRequest::with(['employee', 'approver']);

        if (isset($filters['employee_id'])) {
            $query->where('employee_id', $filters['employee_id']);
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['date_from'])) {
            $query->where('date', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->where('date', '<=', $filters['date_to']);
        }

        return $query->orderBy('date', 'desc')->get();
    }

    public function create(array $data): ExcuseRequest
    {
        $validated = $this->validate($data);
        
        // Calculate end_time from start_time + hours
        $startTime = Carbon::parse($validated['start_time']);
        $endTime = $startTime->copy()->addHours($validated['hours']);
        $validated['end_time'] = $endTime->format('H:i:s');
        
        $excuseRequest = ExcuseRequest::create($validated);
        
        // Send notification to branch managers
        $employee = \App\Models\HR\Employee::findOrFail($validated['employee_id']);
        $this->notificationService->notifyExcuseRequestCreated($employee, $excuseRequest->id);
        
        return $excuseRequest;
    }

    public function approve(ExcuseRequest $request, User $approver): ExcuseRequest
    {
        $request->update([
            'status' => 'approved',
            'approved_by' => $approver->id,
            'approved_at' => now(),
        ]);

        return $request->fresh();
    }

    public function reject(ExcuseRequest $request, User $approver): ExcuseRequest
    {
        $request->update([
            'status' => 'rejected',
            'approved_by' => $approver->id,
            'approved_at' => now(),
        ]);

        return $request->fresh();
    }

    public function delete(ExcuseRequest $request): bool
    {
        return $request->delete();
    }

    protected function validate(array $data, ?ExcuseRequest $request = null): array
    {
        $rules = [
            'employee_id' => 'required|exists:hr_employees,id',
            'date' => 'required|date',
            'hours' => 'required|numeric|min:0.5|max:24',
            'start_time' => 'required|date_format:H:i',
            'reason' => 'required|string|max:1000',
        ];

        $validator = Validator::make($data, $rules);
        
        return $validator->validate();
    }
}

