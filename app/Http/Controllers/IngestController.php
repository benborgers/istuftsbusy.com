<?php

namespace App\Http\Controllers;

use App\Models\Monitor;
use Illuminate\Support\Carbon;

class IngestController extends Controller
{
    public function __invoke(Monitor $monitor)
    {
        info('request', [request()->all()]);

        $data = request()->validate([
            'ip_address' => ['nullable', 'ip'],
            'scans' => ['array'],
            'scans.*.timestamp_ms' => ['required', 'int'],
            'scans.*.mac_address' => ['required', 'mac_address'],
            'scans.*.ssid' => ['nullable', 'string']
        ]);

        if($data['ip_address']) {
            $monitor->update([
                'ip_address' => $data['ip_address']
            ]);
        }

        // We only store scans without an SSID
        // These are more likely to be mobile devices instead of WAPs
        // It also cuts down the number of scans we need to store by a lot
        $monitor->scans()->createMany(
            collect($data['scans'])
                ->whereNull('ssid')
                ->map(fn($scan) => [
                    'location_id' => $monitor->location_id,
                    'mac_address' => $scan['mac_address'],
                    'scan_at' => Carbon::createFromTimestampMs($scan['timestamp_ms'])
                ])
        );

        return response()->noContent();
    }
}
