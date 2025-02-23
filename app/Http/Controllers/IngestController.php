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

        if ($data['ip_address']) {
            $monitor->update([
                'ip_address' => $data['ip_address']
            ]);
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
