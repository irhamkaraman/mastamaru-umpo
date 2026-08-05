<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Mentor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\ValidationException;

class MentorAuthController extends Controller
{
    /**
     * Tampilkan form login mentor
     */
    public function showLoginForm()
    {
        if (session('mentor_id')) {
            return redirect('/mentor/dashboard');
        }
        return view('auth.index');
    }

    /**
     * Proses login mentor
     */
    public function login(Request $request)
    {
        $request->validate([
            'student_id' => 'required|string',
            'password' => 'required|string',
        ], [
            'student_id.required' => 'ID Pendamping/Mentor wajib diisi.',
            'password.required' => 'Kata sandi wajib diisi.',
        ]);

        // Cari mentor berdasarkan student_id
        $mentor = Mentor::where('student_id', $request->student_id)->first();

        // Periksa apakah mentor ditemukan dan password cocok
        if (!$mentor || !Hash::check($request->password, $mentor->password)) {
            throw ValidationException::withMessages([
                'student_id' => 'ID atau kata sandi tidak valid.',
            ]);
        }

        // Login mentor menggunakan session
        session([
            'mentor_id' => $mentor->id,
            'mentor_name' => $mentor->name,
            'mentor_student_id' => $mentor->student_id,
            'mentor_group_id' => $mentor->group_id,
        ]);

        // Redirect ke dashboard atau halaman yang diinginkan
        return redirect()->route('mentor.dashboard')->with('success', 'Login berhasil! Selamat datang, ' . $mentor->name);
    }

    /**
     * Logout mentor
     */
    public function logout(Request $request)
    {
        // Hapus session mentor
        $request->session()->forget([
            'mentor_id',
            'mentor_name',
            'mentor_student_id',
            'mentor_group_id'
        ]);

        // Hapus cache mentor
        self::clearDashboardCache();

        return redirect('/mentor/login')->with('success', 'Anda telah berhasil logout.');
    }

    /**
     * Dashboard mentor (sementara)
     */
    public function dashboard()
    {
        try {
            $mentorId = session('mentor_id');

            // Periksa apakah mentor_id ada di session
            if (!$mentorId) {
                return redirect('/mentor/login')->with('error', 'Sesi tidak valid. Silakan login kembali.');
            }

            // Cache data mentor dengan durasi 10 menit
            $mentor = Cache::remember('mentor_dashboard_' . $mentorId, 600, function () use ($mentorId) {
                return Mentor::with(['group', 'attendances'])->find($mentorId);
            });

            // Periksa apakah data mentor ditemukan di database
            if (!$mentor) {
                // Hapus cache dan session yang tidak valid
                Cache::forget('mentor_dashboard_' . $mentorId);
                session()->forget(['mentor_id', 'mentor_name', 'mentor_student_id', 'mentor_group_id']);
                return redirect('/mentor/login')->with('error', 'Data mentor tidak ditemukan. Silakan login kembali.');
            }

            // Cache data sesi aktif dengan durasi 1 menit untuk real-time updates
            $activeSessions = Cache::remember('active_sessions_all', 60, function () {
                return \App\Models\PresenceSession::where('is_active', true)
                    ->orderBy('created_at', 'desc')
                    ->get();
            });

            // Cache statistik hari ini dengan durasi 1 menit untuk real-time updates
            $todayStats = Cache::remember('today_stats_' . date('Y-m-d'), 60, function () {
                return \App\Models\PresenceSession::where('is_active', true)
                    ->whereDate('start_time', today())
                    ->count();
            });

            return view('mentor.dashboard', compact('mentor', 'activeSessions', 'todayStats'));
        } catch (\Exception $e) {
            // Log error untuk debugging
            Log::error('Error di dashboard mentor: ' . $e->getMessage());

            // Hapus cache dan session
            if (isset($mentorId)) {
                Cache::forget('mentor_dashboard_' . $mentorId);
            }
            session()->forget(['mentor_id', 'mentor_name', 'mentor_student_id', 'mentor_group_id']);
            return redirect('/mentor/login')->with('error', 'Terjadi kesalahan. Silakan login kembali.');
        }
    }

    /**
     * Halaman detail presensi
     */
    public function presenceDetail($slug)
    {
        try {
            $mentorId = session('mentor_id');

            // Periksa apakah mentor_id ada di session
            if (!$mentorId) {
                return redirect('/mentor/login')->with('error', 'Sesi tidak valid. Silakan login kembali.');
            }

            // Verifikasi mentor masih ada di database
            $mentor = Mentor::find($mentorId);
            if (!$mentor) {
                // Hapus session yang tidak valid
                session()->forget(['mentor_id', 'mentor_name', 'mentor_student_id', 'mentor_group_id']);
                return redirect('/mentor/login')->with('error', 'Data mentor tidak ditemukan. Silakan login kembali.');
            }

            return view('mentor.presence-detail', compact('slug'));
        } catch (\Exception $e) {
            // Log error untuk debugging
            Log::error('Error di presence detail mentor: ' . $e->getMessage());

            // Hapus session dan redirect ke login
            session()->forget(['mentor_id', 'mentor_name', 'mentor_student_id', 'mentor_group_id']);
            return redirect('/mentor/login')->with('error', 'Terjadi kesalahan. Silakan login kembali.');
        }
    }

    /**
     * Method untuk menghapus cache dashboard
     */
    public static function clearDashboardCache()
    {
        Cache::forget('active_sessions_all');
        Cache::forget('today_stats_' . date('Y-m-d'));

        // Hapus cache mentor untuk semua mentor (opsional)
        // Cache::flush(); // Hati-hati, ini akan menghapus semua cache
    }
}
