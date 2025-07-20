<?php

namespace App\Services;

use App\Models\Village;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class VillageService
{
    /**
     * Get the village for a user, creating it if it doesn't exist,
     * and update its resources based on time passed.
     */
    public function getVillageForUser(User $user): Village
    {
        // We wrap this in a transaction to ensure data consistency.
        // If any part fails, the whole operation is rolled back.
        return DB::transaction(function () use ($user) {
            $village = $user->village()->firstOrCreate(
                ['user_id' => $user->id],
                [
                    'wood' => 100.0,
                    'clay' => 100.0,
                    'iron' => 100.0,
                    'woodcutter_level' => 1,
                    'clay_pit_level' => 1,
                    'iron_mine_level' => 1,
                    'last_updated' => now(),
                ]
            );

            $this->updateResources($village);

            return $village;
        });
    }

    /**
     * Handle the logic for upgrading a building.
     */
    public function upgradeBuilding(Village $village, string $building): array
    {
        return DB::transaction(function () use ($village, $building) {
            // First, ensure resources are up-to-date before spending.
            $this->updateResources($village);

            $levelField = $building. '_level';
            $currentLevel = $village->{$levelField};
            
            // Calculate the cost for the *next* level.
            $cost = $this->getUpgradeCost($currentLevel);

            if ($village->wood >= $cost['wood'] && $village->clay >= $cost['clay'] && $village->iron >= $cost['iron']) {
                $village->wood -= $cost['wood'];
                $village->clay -= $cost['clay'];
                $village->iron -= $cost['iron'];
                $village->{$levelField}++;
                
                $village->save();

                return ['success' => true, 'village' => $village];
            } else {
                return ['success' => false, 'message' => 'Not enough resources!'];
            }
        });
    }

    /**
     * Calculate resource generation since the last update.
     * This is a protected method, only intended for use within this service.
     */
    protected function updateResources(Village $village): void
    {
        $now = Carbon::now();
        $lastUpdated = $village->last_updated;
        $secondsPassed = $now->diffInSeconds($lastUpdated);

        if ($secondsPassed > 0) {
            // Production rates (per hour). These can be adjusted for game balance.
            $woodRatePerHour = 30 * $village->woodcutter_level;
            $clayRatePerHour = 30 * $village->clay_pit_level;
            $ironRatePerHour = 20 * $village->iron_mine_level;

            // Calculate production per second.
            $woodPerSecond = $woodRatePerHour / 3600;
            $clayPerSecond = $clayRatePerHour / 3600;
            $ironPerSecond = $ironRatePerHour / 3600;
            
            // Add generated resources.
            $village->wood += $woodPerSecond * $secondsPassed;
            $village->clay += $clayPerSecond * $secondsPassed;
            $village->iron += $ironPerSecond * $secondsPassed;
            
            // Update the timestamp and save.
            $village->last_updated = $now;
            $village->save();
        }
    }

    /**
     * Calculate the cost of the next building upgrade.
     * Uses an exponential formula for balanced scaling.
     */
    public function getUpgradeCost(int $level): array
    {
        // Formula: cost = base * rate ^ level
        // This makes each subsequent level significantly more expensive.
        $baseCost = 50;
        $scalingFactor = 1.5;

        $cost = floor($baseCost * pow($scalingFactor, $level));

        return [
            'wood' => $cost,
            'clay' => $cost,
            'iron' => $cost / 2 // Example: Iron is cheaper
        ];
    }
}