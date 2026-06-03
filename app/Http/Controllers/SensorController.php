<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SensorController extends Controller
{
    public function index()
    {
        $file = Storage::get('json/mesin.json');
        $machines = json_decode($file, true);

        $lineOrder = ['A1', 'A2', 'B1.1', 'B1.2', 'B2'];
        $lines = [
            'A1' => [],
            'A2' => [],
            'B1.1' => [],
            'B1.2' => [],
            'B2' => [],
        ];

        $total = 0;
        foreach ($machines as $m) {
            $line = $m['LINENO'] ?? '';
            if (str_starts_with($line, 'A1')) $lines['A1'][] = $m;
            elseif (str_starts_with($line, 'A2')) $lines['A2'][] = $m;
            elseif (str_starts_with($line, 'B1.1')) $lines['B1.1'][] = $m;
            elseif (str_starts_with($line, 'B1.2')) $lines['B1.2'][] = $m;
            elseif (str_starts_with($line, 'B2')) $lines['B2'][] = $m;
            $total += 1;
        }

        uksort($lines, fn($a, $b) => array_search($a, $lineOrder) <=> array_search($b, $lineOrder));

        // Sort numeric order inside each line
        foreach ($lines as $key => &$group) {
            usort($group, function ($a, $b) {
                // Ambil angka setelah '-' di LINENO
                preg_match('/-(\d+)/', $a['LINENO'], $matchA);
                preg_match('/-(\d+)/', $b['LINENO'], $matchB);
                return intval($matchA[1] ?? 0) <=> intval($matchB[1] ?? 0);
            });
        }
        unset($group);

        return view('sensor', compact('lines', 'total'));
    }
}
