<?php

namespace App\Domain\Tenancy;

/**
 * Global Thread/Request Tenant Context Manager.
 * Safely resolves and stores the active tenant in memory for scoping queries, storage, and jobs.
 */
class TenantContext
{
    private static ?string $tenantId = null;
    private static ?object $tenantModel = null;

    public static function setTenant(object $tenant): void
    {
        self::$tenantModel = $tenant;
        self::$tenantId = $tenant->id ?? null;
    }

    public static function setTenantId(?string $id): void
    {
        self::$tenantId = $id;
        self::$tenantModel = null;
    }

    public static function getTenantId(): ?string
    {
        return self::$tenantId;
    }

    public static function getTenant(): ?object
    {
        return self::$tenantModel;
    }

    public static function hasTenant(): bool
    {
        return self::$tenantId !== null;
    }

    public static function clear(): void
    {
        self::$tenantId = null;
        self::$tenantModel = null;
    }
}

