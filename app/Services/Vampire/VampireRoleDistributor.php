<?php

namespace App\Services\Vampire;

use App\Enums\VampireRole;

final class VampireRoleDistributor
{
    /**
     * @param  array{vampireCount?: mixed, villagerCount?: mixed, hasDoktor?: mixed, hasDedektif?: mixed, hasAvci?: mixed}  $config
     * @return list<array{role: string, alignment: string}>
     */
    public function buildRolePool(array $config): array
    {
        $rolePool = [];

        for ($i = 0; $i < (int) ($config['vampireCount'] ?? 1); $i++) {
            $rolePool[] = ['role' => VampireRole::Vampire->value, 'alignment' => 'vampire'];
        }

        for ($i = 0; $i < (int) ($config['villagerCount'] ?? 2); $i++) {
            $rolePool[] = ['role' => VampireRole::Villager->value, 'alignment' => 'villager'];
        }

        if ((bool) ($config['hasDoktor'] ?? false)) {
            $rolePool[] = ['role' => VampireRole::Doctor->value, 'alignment' => 'villager'];
        }

        if ((bool) ($config['hasDedektif'] ?? false)) {
            $rolePool[] = ['role' => VampireRole::Detective->value, 'alignment' => 'villager'];
        }

        if ((bool) ($config['hasAvci'] ?? false)) {
            $rolePool[] = ['role' => VampireRole::Hunter->value, 'alignment' => 'villager'];
        }

        shuffle($rolePool);

        return $rolePool;
    }
}
