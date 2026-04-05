<?php

namespace App\Observers;

use App\Enums\WorkOrderActivityAction;
use App\Enums\WorkOrderStatus;
use App\Models\WorkOrder;
use App\Models\WorkOrderActivity;
use Illuminate\Support\Facades\Auth;

class WorkOrderObserver
{
    public function created(WorkOrder $workOrder): void
    {
        $this->log($workOrder, WorkOrderActivityAction::Created);
    }

    public function updated(WorkOrder $workOrder): void
    {
        if ($workOrder->isDirty('status')) {
            $original = $workOrder->getOriginal('status');
            $oldStatus = $original instanceof WorkOrderStatus ? $original : WorkOrderStatus::from($original);
            $newStatus = $workOrder->status;

            $this->log($workOrder, WorkOrderActivityAction::StatusChanged, [
                'from' => $oldStatus->value,
                'to' => $newStatus->value,
                'from_label' => $oldStatus->label(),
                'to_label' => $newStatus->label(),
            ]);

            $this->logStatusTransitionAction($workOrder, $newStatus);
        }

        if ($workOrder->isDirty('assigned_to')) {
            $this->log($workOrder, WorkOrderActivityAction::Assigned, [
                'user_id' => $workOrder->assigned_to,
            ]);
        }
    }

    private function logStatusTransitionAction(WorkOrder $workOrder, WorkOrderStatus $newStatus): void
    {
        $action = match ($newStatus) {
            WorkOrderStatus::InProgress => WorkOrderActivityAction::Started,
            WorkOrderStatus::OnHold => WorkOrderActivityAction::Paused,
            WorkOrderStatus::Completed => WorkOrderActivityAction::Completed,
            WorkOrderStatus::Cancelled => WorkOrderActivityAction::Cancelled,
            default => null,
        };

        if ($action !== null) {
            $this->log($workOrder, $action);
        }
    }

    /**
     * @param  array<string, mixed>  $metadata
     */
    private function log(WorkOrder $workOrder, WorkOrderActivityAction $action, array $metadata = []): void
    {
        WorkOrderActivity::create([
            'work_order_id' => $workOrder->id,
            'user_id' => Auth::id(),
            'action' => $action,
            'metadata' => empty($metadata) ? null : $metadata,
            'created_at' => now(),
        ]);
    }
}
