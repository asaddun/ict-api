<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Locker;
use App\Models\Employee;
use App\Models\LockerLog;
use App\Models\LockerAccess;
use Illuminate\Http\Request;
use App\Models\LockerLocation;
use Illuminate\Support\Facades\Http;

class LockerController extends Controller
{
    public function active()
    {
        $lockers = Locker::with('employee')
            ->where('isavailable', 'N')
            ->orderByDesc('booked_at')
            ->get();

        foreach ($lockers as $locker) {
            $start = Carbon::parse($locker->booked_at);
            $now = Carbon::now();

            $seconds = $start->diffInSeconds($now, false);
            $hours = floor($seconds / 3600);
            $minutes = floor(($seconds % 3600) / 60);
            $seconds = $seconds % 60;

            $locker->duration = sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
        }
        return view('locker.active', compact('lockers'));
    }

    public function history()
    {
        $logs = LockerLog::select('lkr_log.*')
            ->leftJoin('lkr_log as start_log', function ($join) {
                $join->on('lkr_log.lkr_locker_id', '=', 'start_log.lkr_locker_id')
                    ->on('lkr_log.c_employee_id', '=', 'start_log.c_employee_id')
                    ->where('start_log.action_type', '=', 1)
                    ->whereColumn('start_log.timestamp', '<', 'lkr_log.timestamp');
            })
            ->where('lkr_log.action_type', 2)
            ->selectRaw('TIMESTAMPDIFF(SECOND, MAX(start_log.timestamp), lkr_log.timestamp) as duration')
            ->selectRaw('MAX(start_log.timestamp) as start')
            ->groupBy(
                'lkr_log.lkr_log_id',
                'lkr_log.lkr_locker_id',
                'lkr_log.c_employee_id',
                'lkr_log.action_type',
                'lkr_log.timestamp'
            )
            ->with([
                'locker',
                'employee:c_employee_id,nama_karyawan'
            ])
            ->orderByDesc('lkr_log.timestamp')
            ->paginate(20);

        foreach ($logs as $log) {
            $seconds = (int) $log->duration;
            $hours = floor($seconds / 3600);
            $minutes = floor(($seconds % 3600) / 60);
            $seconds = $seconds % 60;

            $log->duration_formatted = sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
        }

        return view('locker.history', compact('logs'));
    }

    public function control(Request $request)
    {
        $locker = $request->input('locker');
        $location = $request->input('location');
        $lockers = Locker::with('location')
            ->when($locker, function ($query, $locker) {
                $query->where('locker_name', $locker);
            })
            ->when($location, function ($query, $location) {
                $query->whereHas('location', function ($q) use ($location) {
                    $q->where('lkr_location_id', $location);
                });
            })
            ->paginate(20);
        $locations = LockerLocation::all();
        return view('locker.control', compact('lockers', 'locations'));
    }

    public function update_control(Request $request, $id)
    {
        $locker = Locker::findOrFail($id);

        $locker->update([
            'isactive' => $request->isactive,
            'lkr_location_id' => $request->lkr_location_id,
        ]);

        return redirect()->back()->with('success', 'Locker updated successfully!');
    }

    public function access(Request $request)
    {
        $search = $request->input('search');
        $accesses = LockerAccess::with([
            'employee:nama_karyawan,c_employee_id',
            'location'
        ])
            ->when($search, function ($query, $search) {
                $query->whereHas('employee', function ($q) use ($search) {
                    $q->where('nama_karyawan', 'like', "%{$search}%");
                });
            })
            ->orderBy('c_employee_id', 'desc')
            ->paginate(20);

        $locations = LockerLocation::all();
        return view('locker.access', compact('accesses', 'locations'));
    }

    public function update_access(Request $request, $id)
    {
        $access = LockerAccess::where('c_employee_id', $id);

        $access->update([
            'lkr_location_id' => $request->lkr_location_id,
        ]);

        return redirect()->back()->with('success', 'Access updated successfully!');
    }

    public function sync_access(Request $request)
    {
        // Ambil semua karyawan aktif
        $activeEmployees = Employee::where('isactive', 'Y')->pluck('c_employee_id');

        // Ambil semua karyawan yang sudah punya akses
        $existingAccess = LockerAccess::pluck('c_employee_id');

        // Hitung siapa yang perlu ditambahkan
        $toAdd = $activeEmployees->diff($existingAccess);
        $addedCount = 0;

        foreach ($toAdd as $id) {
            LockerAccess::create([
                'c_employee_id' => $id,
                'lkr_location_id' => 0, // lokasi default atau null sesuai kebutuhan
            ]);
            $addedCount++;
        }

        // Hapus yang tidak aktif lagi
        $toDelete = LockerAccess::whereNotIn('c_employee_id', $activeEmployees)->get();
        $deletedCount = $toDelete->count();

        foreach ($toDelete as $access) {
            $access->delete();
        }

        // Buat pesan hasil
        if ($addedCount === 0 && $deletedCount === 0) {
            $message = 'Syncronized! There is no change.';
        } else {
            $message = sprintf(
                'Syncronized! %d added, %d deleted.',
                $addedCount,
                $deletedCount
            );
        }

        // Redirect dengan pesan
        return redirect()->back()->with([
            'status' => 'success',
            'message' => $message,
        ]);
    }

    public function open(Request $request)
    {
        $token = '66ae1553a20eb59bb1d4db65f4c4c2d1';
        $ip = $request->input('ip');
        $io = $request->input('io');
        $url = "http://$ip/locker/$io";

        $response = Http::asForm() // ini penting! biar sama kayak http_build_query()
            ->timeout(5)
            ->withoutVerifying()
            ->post($url, [
                'token' => $token,
            ]);

        if ($response->successful()) {
            return response()->json([
                'status'  => 'success',
                'message' => 'Locker has been opened.',
            ], 200); // 200 OK
        }

        return response()->json([
            'status'  => 'error',
            'message' => 'Failed to open Locker',
        ], 500);
    }

    public function force_unlock(Request $request, $lockerId)
    {
        $locker = Locker::findOrFail($lockerId);
        $token = '66ae1553a20eb59bb1d4db65f4c4c2d1';
        $ip = $locker->ip_address;
        $io = $locker->io;
        $url = "http://$ip/locker/$io";

        $response = Http::asForm() // ini penting! biar sama kayak http_build_query()
            ->timeout(5)
            ->withoutVerifying()
            ->post($url, [
                'token' => $token,
            ]);

        if ($response->successful()) {
            $log = LockerLog::create([
                'lkr_locker_id' => $locker->lkr_locker_id,
                'c_employee_id' => $locker->c_employee_id,
                'action_type' => 2,
                'timestamp' => now(),
            ]);

            $locker->update([
                'isavailable' => 'Y',
                'c_employee_id' => null,
                'pin_number' => null,
                'booked_at' => null,
            ]);

            if (!$log) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Failed to log the unlock action.',
                ], 500);
            }

            return response()->json([
                'status'  => 'success',
                'message' => 'Locker has been opened.',
            ], 200); // 200 OK
        }

        return response()->json([
            'status'  => 'error',
            'message' => 'Failed to open Locker',
        ], 500);
    }
}
