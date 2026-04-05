<?php

namespace App\Enums;

enum WorkOrderActivityAction: string
{
    case Created = 'created';
    case StatusChanged = 'status_changed';
    case Assigned = 'assigned';
    case Approved = 'approved';
    case Started = 'started';
    case Paused = 'paused';
    case Resumed = 'resumed';
    case Completed = 'completed';
    case Cancelled = 'cancelled';
    case NoteAdded = 'note_added';
    case PartAdded = 'part_added';
    case PartRemoved = 'part_removed';
    case ChecklistItemCompleted = 'checklist_item_completed';
    case PhotoAdded = 'photo_added';

    public function label(): string
    {
        return match ($this) {
            self::Created => 'Orden creada',
            self::StatusChanged => 'Estado cambiado',
            self::Assigned => 'Técnico asignado',
            self::Approved => 'Orden aprobada',
            self::Started => 'Trabajo iniciado',
            self::Paused => 'Trabajo pausado',
            self::Resumed => 'Trabajo reanudado',
            self::Completed => 'Orden completada',
            self::Cancelled => 'Orden cancelada',
            self::NoteAdded => 'Nota agregada',
            self::PartAdded => 'Repuesto agregado',
            self::PartRemoved => 'Repuesto removido',
            self::ChecklistItemCompleted => 'Ítem de checklist completado',
            self::PhotoAdded => 'Foto adjuntada',
        };
    }
}
