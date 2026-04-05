<?php

namespace App\Enums;

enum ModuleKey: string
{
    case Core = 'core';
    case Erp = 'erp';
    case Iot = 'iot';
    case AiPredictive = 'ai_predictive';
    case DigitalTwin = 'digital_twin';
    case HrTechnicians = 'hr_technicians';
    case CrmPortal = 'crm_portal';
    case Audits = 'audits';
    case EnergyEsg = 'energy_esg';
    case LotoSecurity = 'loto_security';
    case VerticalModule = 'vertical_module';
    case PharmaModule = 'pharma_module';
    case WhiteLabelApp = 'white_label_app';
    case Lms = 'lms';
    case MultiSite = 'multi_site';

    public function label(): string
    {
        return match ($this) {
            self::Core => 'Core CMMS',
            self::Erp => 'Integración ERP',
            self::Iot => 'IoT & Sensores',
            self::AiPredictive => 'IA Predictiva',
            self::DigitalTwin => 'Gemelo Digital',
            self::HrTechnicians => 'RRHH Técnicos',
            self::CrmPortal => 'Portal CRM',
            self::Audits => 'Auditorías',
            self::EnergyEsg => 'Energía & ESG',
            self::LotoSecurity => 'LOTO / Seguridad',
            self::VerticalModule => 'Módulo Vertical',
            self::PharmaModule => 'Módulo Pharma',
            self::WhiteLabelApp => 'White Label App',
            self::Lms => 'LMS Capacitación',
            self::MultiSite => 'Multi-Site',
        };
    }

    public function price(): float
    {
        return match ($this) {
            self::Core => 0.0,
            self::Erp => 45.0,
            self::Iot => 35.0,
            self::AiPredictive => 55.0,
            self::DigitalTwin => 65.0,
            self::HrTechnicians => 30.0,
            self::CrmPortal => 35.0,
            self::Audits => 40.0,
            self::EnergyEsg => 40.0,
            self::LotoSecurity => 30.0,
            self::VerticalModule => 50.0,
            self::PharmaModule => 80.0,
            self::WhiteLabelApp => 150.0,
            self::Lms => 25.0,
            self::MultiSite => 40.0,
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::Core => 'Gestión de activos, órdenes de trabajo y mantenimiento preventivo.',
            self::Erp => 'Integración bidireccional con SAP, Oracle y otros ERPs.',
            self::Iot => 'Monitoreo en tiempo real de sensores y equipos conectados.',
            self::AiPredictive => 'Análisis predictivo con machine learning para prevenir fallas.',
            self::DigitalTwin => 'Gemelo digital de planta y equipos en 3D.',
            self::HrTechnicians => 'Gestión de técnicos, turnos, certificaciones y capacitación.',
            self::CrmPortal => 'Portal de clientes para solicitudes y seguimiento de servicios.',
            self::Audits => 'Auditorías internas, hallazgos y acciones correctivas (CAPA).',
            self::EnergyEsg => 'Monitoreo de consumo energético y reportes de sostenibilidad.',
            self::LotoSecurity => 'Permisos de trabajo, LOTO y gestión de seguridad industrial.',
            self::VerticalModule => 'Módulo especializado para industria vertical específica.',
            self::PharmaModule => 'Cumplimiento GMP/FDA para industria farmacéutica.',
            self::WhiteLabelApp => 'App móvil con marca blanca personalizada.',
            self::Lms => 'Sistema de gestión de aprendizaje para técnicos.',
            self::MultiSite => 'Gestión centralizada de múltiples plantas o sitios.',
        };
    }
}
