<?php

namespace App\Domain\Attendance;

use Carbon\Carbon;
use RuntimeException;

/**
 * Biometric Attendance Ingest Engine
 * Parses standard ZKTeco biometric punch files (.dat, .txt, .csv)
 * Compatible with ZKSoftware, ZKBioTime, and standard biometric punch dumps.
 */
class ZkTeCoAttendanceParser
{
    /**
     * Parse raw ZKTeco biometric content into structured daily punches per employee.
     *
     * @param string $content Raw string from uploaded file
     * @return array<string, array<string, array{time_in: string, time_out: string, punch_count: int}>>
     * Structure: [employee_number => [YYYY-MM-DD => [time_in, time_out, punch_count]]]
     */
    public function parse(string $content): array
    {
        $lines = preg_split('/\r\n|\r|\n/', trim($content));
        if (empty($lines)) {
            return [];
        }

        $rawPunches = [];

        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '') {
                continue;
            }

            // Detect delimiter: tab (\t), comma (,), or multiple spaces
            if (str_contains($line, "\t")) {
                $cols = explode("\t", $line);
            } elseif (str_contains($line, ',')) {
                $cols = str_getcsv($line);
            } else {
                $cols = preg_split('/\s+/', $line);
            }

            // Expected columns: [0] => Employee PIN/ID, [1] => Timestamp (or Date), [2] => Time (if separate)
            if (count($cols) < 2) {
                continue;
            }

            $empNumber = trim($cols[0]);
            
            // Check if column 1 contains full datetime e.g. "2026-10-01 07:55:12"
            $dateTimeCandidate = trim($cols[1]);
            if (isset($cols[2]) && preg_match('/^\d{2}:\d{2}(:\d{2})?$/', trim($cols[2]))) {
                $dateTimeCandidate = trim($cols[1]) . ' ' . trim($cols[2]);
            }

            try {
                $timestamp = Carbon::parse($dateTimeCandidate);
            } catch (\Exception $e) {
                continue; // Skip header rows or malformed lines
            }

            $dateKey = $timestamp->toDateString();
            $rawPunches[$empNumber][$dateKey][] = $timestamp;
        }

        // Aggregate daily punches: earliest punch = time_in, latest punch = time_out
        $aggregated = [];
        foreach ($rawPunches as $empNumber => $dates) {
            foreach ($dates as $date => $punches) {
                usort($punches, fn($a, $b) => $a->timestamp <=> $b->timestamp);

                $earliest = $punches[0];
                $latest = (count($punches) > 1) ? end($punches) : null;

                $aggregated[$empNumber][$date] = [
                    'time_in'      => $earliest->toDateTimeString(),
                    'time_out'     => $latest ? $latest->toDateTimeString() : $earliest->toDateTimeString(),
                    'punch_count'  => count($punches),
                ];
            }
        }

        return $aggregated;
    }
}

