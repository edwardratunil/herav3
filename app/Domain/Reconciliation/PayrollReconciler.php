<?php

namespace App\Domain\Reconciliation;

use App\Models\PayrollRun;
use RuntimeException;

/**
 * Pilot Payroll Reconciliation & Dual-Run Analysis Engine
 * Compares legacy payroll registers against HERA v3 calculation runs.
 */
class PayrollReconciler
{
    public const SCALE = 4;
    public const ROUNDING_TOLERANCE = '0.0500';

    /**
     * Reconcile a HERA v3 payroll run against legacy payroll data rows.
     *
     * @param PayrollRun $run
     * @param array<int, array{
     *   employee_number: string,
     *   basic_pay: string,
     *   overload_pay?: string,
     *   gross_pay: string,
     *   sss_ee?: string,
     *   gsis_ee?: string,
     *   philhealth_ee?: string,
     *   pagibig_ee?: string,
     *   withholding_tax?: string,
     *   net_pay: string
     * }> $legacyRows
     * @return array{
     *   run_id: string,
     *   total_employees_compared: int,
     *   summary: array{
     *     hera_total_net: string,
     *     legacy_total_net: string,
     *     net_variance: string,
     *     perfect_matches: int,
     *     rounding_variants: int,
     *     material_discrepancies: int,
     *     is_pilot_passed: bool
     *   },
     *   discrepancies: array<int, array>
     * }
     */
    public function reconcile(PayrollRun $run, array $legacyRows): array
    {
        $items = $run->items()->with('employee')->get()->keyBy(fn($item) => $item->employee->employee_number);

        $totalHeraNet = '0.0000';
        $totalLegacyNet = '0.0000';
        $perfectMatches = 0;
        $roundingVariants = 0;
        $materialDiscrepancies = 0;
        $discrepancyList = [];

        foreach ($legacyRows as $legacy) {
            $empNum = trim($legacy['employee_number']);
            $heraItem = $items->get($empNum);

            if (!$heraItem) {
                $materialDiscrepancies++;
                $discrepancyList[] = [
                    'employee_number' => $empNum,
                    'status'          => 'MISSING_IN_HERA',
                    'legacy_net'      => $legacy['net_pay'],
                    'hera_net'        => '0.00',
                    'variance'        => '-' . $legacy['net_pay'],
                ];
                continue;
            }

            $heraNet = (string)$heraItem->net_pay;
            $legNet = (string)$legacy['net_pay'];

            $totalHeraNet = bcadd($totalHeraNet, $heraNet, self::SCALE);
            $totalLegacyNet = bcadd($totalLegacyNet, $legNet, self::SCALE);

            $diffNet = bcsub($heraNet, $legNet, self::SCALE);
            $absDiff = bccomp($diffNet, '0.0000', self::SCALE) < 0 ? bcmul($diffNet, '-1.0000', self::SCALE) : $diffNet;

            if (bccomp($absDiff, '0.0000', self::SCALE) === 0) {
                $perfectMatches++;
                $status = 'EXACT_MATCH';
            } elseif (bccomp($absDiff, self::ROUNDING_TOLERANCE, self::SCALE) <= 0) {
                $roundingVariants++;
                $status = 'ROUNDING_VARIANT';
            } else {
                $materialDiscrepancies++;
                $status = 'MATERIAL_DISCREPANCY';
            }

            // Check field-level diffs
            $diffs = [
                'net_pay'         => ['hera' => $heraNet, 'legacy' => $legNet, 'delta' => $diffNet],
                'gross_pay'       => ['hera' => (string)$heraItem->gross_pay, 'legacy' => $legacy['gross_pay'], 'delta' => bcsub((string)$heraItem->gross_pay, (string)$legacy['gross_pay'], self::SCALE)],
                'withholding_tax' => ['hera' => (string)$heraItem->withholding_tax, 'legacy' => (string)($legacy['withholding_tax'] ?? '0.00'), 'delta' => bcsub((string)$heraItem->withholding_tax, (string)($legacy['withholding_tax'] ?? '0.00'), self::SCALE)],
            ];

            if ($status !== 'EXACT_MATCH') {
                $discrepancyList[] = [
                    'employee_number' => $empNum,
                    'name'            => $heraItem->employee->full_name,
                    'status'          => $status,
                    'variance'        => $diffNet,
                    'field_deltas'    => $diffs,
                ];
            }
        }

        $netVariance = bcsub($totalHeraNet, $totalLegacyNet, self::SCALE);
        $isPilotPassed = ($materialDiscrepancies === 0);

        return [
            'run_id'                   => $run->id,
            'total_employees_compared' => count($legacyRows),
            'summary'                  => [
                'hera_total_net'         => number_format((float)$totalHeraNet, 2, '.', ''),
                'legacy_total_net'       => number_format((float)$totalLegacyNet, 2, '.', ''),
                'net_variance'           => number_format((float)$netVariance, 2, '.', ''),
                'perfect_matches'        => $perfectMatches,
                'rounding_variants'      => $roundingVariants,
                'material_discrepancies' => $materialDiscrepancies,
                'is_pilot_passed'        => $isPilotPassed,
            ],
            'discrepancies'            => $discrepancyList,
        ];
    }
}

