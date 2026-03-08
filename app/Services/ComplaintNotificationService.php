<?php

namespace App\Services;

use App\Models\Complaint;
use App\Models\ComplaintNotification;
use Carbon\Carbon;

class ComplaintNotificationService
{
    public function checkOverdueComplaints(): void
    {
        $complaints = Complaint::whereIn('status', ['in_progress', 'resolved'])
            ->where('updated_at', '<', Carbon::now()->subHours(48))
            ->get();

        foreach ($complaints as $complaint) {
            $lastNotification = ComplaintNotification::where('complaint_id', $complaint->id)
                ->where('type', 'overdue')
                ->where('status', 'unread')
                ->latest()
                ->first();

            if (!$lastNotification) {
                ComplaintNotification::create([
                    'complaint_id' => $complaint->id,
                    'type' => 'overdue',
                    'message' => "الشكوى رقم {$complaint->complaint_no} لم يتم تحديثها منذ أكثر من 48 ساعة",
                    'status' => 'unread',
                ]);
            }
        }
    }

    public function notifyOnResolved(Complaint $complaint): void
    {
        if ($complaint->status === 'resolved') {
            ComplaintNotification::create([
                'complaint_id' => $complaint->id,
                'type' => 'resolved',
                'message' => "تم حل الشكوى رقم {$complaint->complaint_no}",
                'status' => 'unread',
            ]);
        }
    }

    public function notifyOnActionTaken(Complaint $complaint): void
    {
        if ($complaint->branch_action_taken) {
            $existingNotification = ComplaintNotification::where('complaint_id', $complaint->id)
                ->where('type', 'action_taken')
                ->where('status', 'unread')
                ->latest()
                ->first();

            if (!$existingNotification) {
                ComplaintNotification::create([
                    'complaint_id' => $complaint->id,
                    'type' => 'action_taken',
                    'message' => "تم اتخاذ إجراء من الفرع المختص للشكوى رقم {$complaint->complaint_no}",
                    'status' => 'unread',
                ]);
            }
        }
    }
}
