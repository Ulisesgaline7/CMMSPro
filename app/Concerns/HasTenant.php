<?php

namespace App\Concerns;

use App\Scopes\TenantScope;

/**
 * Applies TenantScope globally so all queries are automatically
 * filtered by the authenticated user's tenant_id.
 */
trait HasTenant
{
    public static function bootHasTenant(): void
    {
        static::addGlobalScope(new TenantScope());
    }
}
