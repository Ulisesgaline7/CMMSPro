export type WorkOrderStatus =
    | 'draft'
    | 'pending'
    | 'in_progress'
    | 'on_hold'
    | 'completed'
    | 'cancelled';

export type WorkOrderType = 'preventive' | 'corrective' | 'predictive';

export type WorkOrderPriority = 'low' | 'medium' | 'high' | 'critical';

export type AssetStatus = 'active' | 'inactive' | 'under_maintenance' | 'retired';

export type AssetCriticality = 'low' | 'medium' | 'high' | 'critical';

export interface TenantUser {
    id: number;
    name: string;
    email: string;
    role: string;
    employee_code: string | null;
}

export interface AssetCategory {
    id: number;
    name: string;
    code: string | null;
}

export interface Location {
    id: number;
    name: string;
    code: string | null;
    type: string;
    parent: Location | null;
}

export interface Asset {
    id: number;
    name: string;
    code: string;
    serial_number: string | null;
    brand: string | null;
    model: string | null;
    manufacture_year: number | null;
    status: AssetStatus;
    criticality: AssetCriticality;
    location: Location | null;
    category: AssetCategory | null;
    specs: Record<string, unknown> | null;
}

export interface WorkOrderChecklistItem {
    id: number;
    description: string;
    is_completed: boolean;
    completed_by: TenantUser | null;
    completed_at: string | null;
    sort_order: number;
}

export interface WorkOrderChecklist {
    id: number;
    name: string;
    items: WorkOrderChecklistItem[];
}

export interface WorkOrderPart {
    id: number;
    part_name: string;
    quantity: number;
    unit: string;
    unit_cost: number | null;
    part: { id: number; name: string; unit: string } | null;
}

export interface WorkOrderActivity {
    id: number;
    action: string;
    notes: string | null;
    metadata: Record<string, unknown> | null;
    user: TenantUser | null;
    created_at: string;
}

export interface WorkOrder {
    id: number;
    code: string;
    title: string;
    description: string | null;
    type: WorkOrderType;
    status: WorkOrderStatus;
    priority: WorkOrderPriority;
    due_date: string | null;
    started_at: string | null;
    completed_at: string | null;
    estimated_duration: number | null;
    actual_duration: number | null;
    failure_cause: string | null;
    resolution_notes: string | null;
    asset: Asset;
    assigned_to: TenantUser | null;
    requested_by: TenantUser | null;
    approved_by: TenantUser | null;
    checklists: WorkOrderChecklist[];
    parts: WorkOrderPart[];
    activities: WorkOrderActivity[];
    created_at: string;
    updated_at: string;
}

export interface WorkOrderSummary {
    id: number;
    code: string;
    title: string;
    type: WorkOrderType;
    status: WorkOrderStatus;
    priority: WorkOrderPriority;
    due_date: string | null;
    started_at: string | null;
    asset: Pick<Asset, 'id' | 'name' | 'code' | 'criticality'> & {
        location: Pick<Location, 'id' | 'name'> | null;
    };
    assigned_to: Pick<TenantUser, 'id' | 'name'> | null;
    created_at: string;
}

export interface WorkOrderStats {
    total: number;
    pending: number;
    in_progress: number;
    completed_today: number;
    overdue: number;
}

// Label and color helpers (mirrors PHP Enums)
export const WORK_ORDER_STATUS_LABELS: Record<WorkOrderStatus, string> = {
    draft: 'Borrador',
    pending: 'Pendiente',
    in_progress: 'En Progreso',
    on_hold: 'En Pausa',
    completed: 'Completada',
    cancelled: 'Cancelada',
};

export const WORK_ORDER_STATUS_COLORS: Record<WorkOrderStatus, string> = {
    draft: 'bg-gray-100 text-gray-600',
    pending: 'bg-yellow-100 text-yellow-700',
    in_progress: 'bg-blue-100 text-blue-700',
    on_hold: 'bg-orange-100 text-orange-700',
    completed: 'bg-green-100 text-green-700',
    cancelled: 'bg-red-100 text-red-700',
};

export const WORK_ORDER_TYPE_LABELS: Record<WorkOrderType, string> = {
    preventive: 'Preventivo',
    corrective: 'Correctivo',
    predictive: 'Predictivo',
};

export const WORK_ORDER_TYPE_ABBREV: Record<WorkOrderType, string> = {
    preventive: 'PM',
    corrective: 'CM',
    predictive: 'PdM',
};

export const WORK_ORDER_TYPE_COLORS: Record<WorkOrderType, string> = {
    preventive: 'bg-blue-100 text-blue-700',
    corrective: 'bg-red-100 text-red-700',
    predictive: 'bg-purple-100 text-purple-700',
};

export const WORK_ORDER_PRIORITY_LABELS: Record<WorkOrderPriority, string> = {
    low: 'Baja',
    medium: 'Media',
    high: 'Alta',
    critical: 'Crítica',
};

export const WORK_ORDER_PRIORITY_COLORS: Record<WorkOrderPriority, string> = {
    low: 'bg-gray-100 text-gray-600',
    medium: 'bg-blue-100 text-blue-700',
    high: 'bg-orange-100 text-orange-700',
    critical: 'bg-red-100 text-red-700',
};

export const ASSET_CRITICALITY_LABELS: Record<AssetCriticality, string> = {
    low: 'Baja',
    medium: 'Media',
    high: 'Alta',
    critical: 'Crítica',
};
