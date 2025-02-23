<?php

namespace App\Http\Controllers;

use App\Models\Monitor;
use Illuminate\Support\Carbon;

class IngestController extends Controller
{
    public function __invoke(Monitor $monitor)
    {
        info('Received ingest request', [request()]);

        try {
            $data = request()->validate([
                'ip_address' => ['required', 'ip'],
                'scans' => ['required', 'array'],
                'scans.*.timestamp_ms' => ['required', 'int'],
                'scans.*.mac_address' => ['required', 'mac_address'],
                'scans.*.ssid' => ['nullable', 'string']
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            info('Validation failed', ['errors' => $e->errors()]);
            throw $e;
        }

        $monitor->scans()->createMany(
            collect($data['scans'])->map(fn($scan) => [
                'location_id' => $monitor->location_id,
                'mac_address' => $scan['mac_address'],
                'ssid' => $scan['ssid'],
                'scan_at' => Carbon::createFromTimestampMs($scan['timestamp_ms'])
            ])
        );

        return response()->noContent();
    }
}
