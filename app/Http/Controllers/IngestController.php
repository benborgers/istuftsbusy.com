<?php

namespace App\Http\Controllers;

use App\Models\Monitor;
use Illuminate\Support\Carbon;

class IngestController extends Controller
{
    public function __invoke(Monitor $monitor)
    {
        $data = request()->validate([
            'ip_address' => ['required', 'ip'],
            'scans' => ['array'],
            'scans.*.timestamp_ms' => ['required', 'int'],
            'scans.*.mac_address' => ['required', 'mac_address'],
            'scans.*.ssid' => ['nullable', 'string']
        ]);

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
