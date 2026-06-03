<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class AndonController extends Controller
{
    public function reset(Request $request)
    {
        $action = $request->input('action');
        $id     = $request->input('id');
        $button = $request->input('button');

        if (empty($action) || empty($id)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Data input tidak lengkap.',
            ], 400); // 400 Bad Request
        }

        // Dapatkan waktu saat ini dalam format UNIX timestamp
        $time = time();

        $ip = '192.168.1.41';

        // Buat array JSON
        $data = [
            "action" => $action,
            "id" => (int) $id,
            "button" => (int) $button,
            "time" => $time,
            "ip" => $ip
        ];

        return response()->json([
            'status'  => 'success',
            'message' => $data,
        ], 200); // 200 OK
    }

    public function update()
    {
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

                return response()->json([
                    'status'  => 'success',
                    'message' => 'Berhasil update',
                ], 200); // 200 OK
            } else {
                return response()->json([
                    'status'  => 'success',
                    'message' => 'Gagal update',
                ], 500); // 500 Error
            }
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'success',
                'message' => 'Gagal update',
            ], 500); // 500 Error
        }
    }
}
