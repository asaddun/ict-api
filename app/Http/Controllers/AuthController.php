<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AuthController extends Controller
{
    public function index()
    {
        return view('login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Kirim ke API eksternal
        $response = Http::withoutVerifying()
            ->post('https://api-ess.adyawinsa.com/health/login', [
                'email' => $credentials['email'],
                'password' => $credentials['password'],
            ]);

        // Jika gagal login
        if ($response->failed()) {
            return back()->with([
                'status' => 'error',
                'message' => 'Wrong Email or Password.'
            ]);
        }

        // Ambil data dari respon
        $data = $response->json()['data'];
        $c_employee_id = $data['c_employee_id'];

        $is_ict = Employee::where('c_employee_id', $c_employee_id)
            ->where('c_department_id', 11)
            ->orWhere('c_employee_id', 484)
            ->exists();

        if (!$is_ict) {
            return back()->with([
                'status' => 'error',
                'message' => 'Who are you? You are not ICT you know!'
            ]);
        }

        session([
            'c_employee_id' => $c_employee_id,
            'fullname' => $data['fullname'],
            'qrcode' => $data['qrcode']
        ]);

        // Ambil URL yang sebelumnya disimpan (jika ada)
        $redirect = session('redirect_after_login');

        // Hapus dari session agar tidak dipakai lagi
        session()->forget('redirect_after_login');

        if ($redirect) {
            return redirect($redirect)->with([
                'status' => 'success',
                'message' => 'Hello ' . $data['fullname'] . '!'
            ]);
        }

        return redirect()->route('home')->with([
            'status' => 'success',
            'message' => 'Hello ' . $data['fullname'] . '!'
        ]);
    }

    public function logout(Request $request)
    {
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('auth.index');
    }
}
