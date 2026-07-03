<?php

namespace App\Http\Services\Financing;

use App\Models\MoraRecord;
use App\Models\Financing;

class MoraCalculationService
{
    /**
     * Calculate the next mora record data for a given financing.
     *
     * @param int $financingId
     * @param float $baseAmount The amount over which the percentage is applied (usually the installment value)
     * @return array
     */
    public static function getNextMoraData(int $financingId, float $baseAmount): array
    {
        $paidCount = MoraRecord::where('financing_id', $financingId)
            ->whereIn('status', ['paid', 'approved'])
            ->count();

        $nextOccurrence = $paidCount + 1;
        $reconnectionFee = 5000;

        if ($nextOccurrence <= 10) {
            $percentage = 10;
            $index = $nextOccurrence;
        } elseif ($nextOccurrence <= 20) {
            $percentage = 20;
            $index = $nextOccurrence - 10;
        } else {
            $percentage = 30;
            $index = $nextOccurrence - 20;
        }

        $moraAmount = $baseAmount * ($percentage / 100);
        $totalAmount = $moraAmount + $reconnectionFee;

        return [
            'financing_id' => $financingId,
            'base_amount' => $baseAmount,
            'reconnection_fee' => $reconnectionFee,
            'total_amount' => $totalAmount,
            'percentage' => $percentage,
            'occurrence_index' => $index,
            'total_paid_count' => $paidCount,
            'next_occurrence' => $nextOccurrence
        ];
    }

    /**
     * Create a new mora record if one doesn't already exist for the current context.
     * (e.g., for a specific due date or week)
     */
    public static function createMoraRecord(int $financingId, float $baseAmount): MoraRecord
    {
        $data = self::getNextMoraData($financingId, $baseAmount);
        
        return MoraRecord::create([
            'financing_id' => $data['financing_id'],
            'base_amount' => $data['base_amount'],
            'reconnection_fee' => $data['reconnection_fee'],
            'total_amount' => $data['total_amount'],
            'percentage' => $data['percentage'],
            'occurrence_index' => $data['occurrence_index'],
            'status' => 'pending'
        ]);
    }
}
