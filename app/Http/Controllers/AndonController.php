<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class AndonController extends Controller
{
    public function index()
    {
        $file = Storage::get('json/mesin.json');
        if (!isset($file)) {
            $key = "N3v3rg1v3up!";
            $url = 'https://apik.adyawinsa.com/smsd/api/all_assets.php?key=' . $key;

            try {
                // 1. Melakukan GET request ke URL
                $response = Http::withOptions([
                    // Menonaktifkan verifikasi SSL
                    'verify' => false,
                ])->get($url);

                // 2. Memeriksa apakah request berhasil (status code 200-299)
                if ($response->successful()) {

                    $json = $response->json();

                    // Mengubah data kembali menjadi string JSON yang diformat
                    $jsonFile = json_encode($json, JSON_PRETTY_PRINT);

                    // 3. Menyimpan konten JSON ke dalam file di storage/app/json/mesin.json
                    // 'json' adalah folder di dalam 'storage/app/'
                    Storage::put('json/mesin.json', $jsonFile);
                } else {
                    // Log atau throw exception di sini
                }
            } catch (\Exception $e) {
                echo "Terjadi error saat request atau penyimpanan: " . $e->getMessage();
            }
        } else {
            $json = json_decode($file, true);
        }

        $sorted = collect($json)->sort(function ($a, $b) {
            return $this->parseCode($a['LINENO']) <=> $this->parseCode($b['LINENO']);
        })->values();

        $data['mesin'] = $sorted;

        return view('andon', compact('data'));
    }

    private function parseCode(string $code): array
    {
        preg_match(
            '/^([A-Z]+)(\d+)(?:\.(\d+))?(?:-(\d+))?$/i',
            $code,
            $m
        );

        return [
            strtoupper($m[1] ?? ''),
            (int) ($m[2] ?? 0),
            (int) ($m[3] ?? 0),
            (int) ($m[4] ?? 0),
        ];
    }
}
