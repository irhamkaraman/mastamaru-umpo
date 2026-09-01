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
                    ->orderByRaw("
                        CASE 
                            WHEN NOW() BETWEEN start_time AND end_time THEN 1 
                            WHEN NOW() < start_time THEN 2 
                            ELSE 3 
                        END ASC
                    ")
                    ->orderBy('start_time', 'asc')
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
     * Update profil / nomor telepon mentor dari dashboard
     */
    public function updateProfile(Request $request)
    {
        $mentorId = session('mentor_id');
        if (!$mentorId) {
            return redirect('/mentor/login')->with('error', 'Sesi tidak valid. Silakan login kembali.');
        }

        $request->validate([
            'phone_number' => 'nullable|string|max:20',
        ], [
            'phone_number.max' => 'Nomor WhatsApp / telepon maksimal 20 karakter.',
        ]);

        $mentor = Mentor::find($mentorId);
        if (!$mentor) {
            return redirect('/mentor/login')->with('error', 'Data mentor tidak ditemukan.');
        }

        $mentor->update([
            'phone_number' => $request->input('phone_number'),
        ]);

        // Bersihkan cache dashboard mentor dan view cache
        Cache::forget('mentor_dashboard_' . $mentorId);

        return redirect()->route('mentor.dashboard')->with('success', 'Nomor telepon / WhatsApp berhasil diperbarui!');
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
    /**
     * Halaman daftar peserta kelompok
     */
    public function participants()
    {
        try {
            $mentorId = session('mentor_id');
            if (!$mentorId) {
                return redirect('/mentor/login')->with('error', 'Sesi tidak valid.');
            }

            $mentor = Mentor::with('group')->find($mentorId);
            if (!$mentor || !$mentor->group_id) {
                return redirect()->route('mentor.dashboard')->with('error', 'Anda tidak memiliki kelompok yang ditugaskan.');
            }

            // Ambil peserta dalam kelompok
            $participants = \App\Models\Attendance::with('assessment')
                ->where('group_id', $mentor->group_id)
                ->get();

            return view('mentor.participants', compact('mentor', 'participants'));
        } catch (\Exception $e) {
            Log::error('Error di participants mentor: ' . $e->getMessage());
            return redirect()->route('mentor.dashboard')->with('error', 'Terjadi kesalahan saat memuat daftar peserta.');
        }
    }

    /**
     * Generate sertifikat masal untuk peserta di kelompok
     */
    public function generateCertificates(Request $request)
    {
        try {
            $mentorId = session('mentor_id');
            if (!$mentorId) {
                return redirect('/mentor/login')->with('error', 'Sesi tidak valid.');
            }

            $request->validate([
                'selected_students' => 'required|string',
                'mentor_nim' => 'required|string',
                'mentor_password' => 'required|string',
            ]);

            // Decode the JSON string from the hidden input
            $selectedIds = json_decode($request->input('selected_students'), true);

            if (!is_array($selectedIds) || empty($selectedIds)) {
                return redirect()->back()->with('error', 'Tidak ada peserta yang dipilih.');
            }

            $mentor = Mentor::find($mentorId);
            if (!$mentor || !$mentor->group_id) {
                return redirect()->route('mentor.dashboard')->with('error', 'Anda tidak memiliki kelompok yang ditugaskan.');
            }

            // Otorisasi NIM dan Password
            if ($mentor->student_id !== $request->input('mentor_nim') || !\Hash::check($request->input('mentor_password'), $mentor->password)) {
                return redirect()->back()->with('error', 'Otorisasi gagal! NIM atau Password yang Anda masukkan tidak sesuai.');
            }

            // Ambil template aktif
            $template = \App\Models\CertificateTemplate::getActiveFor('lulus');
            if (!$template) {
                return redirect()->back()->with('error', 'Template sertifikat lulus belum diatur atau belum aktif.');
            }

            // Ambil peserta yang dipilih
            $participants = \App\Models\Attendance::where('group_id', $mentor->group_id)
                ->whereIn('id', $selectedIds)
                ->get();

            if ($participants->isEmpty()) {
                return redirect()->back()->with('error', 'Peserta yang Anda pilih tidak valid atau belum ada peserta di kelompok Anda.');
            }

            $service = app(\App\Services\WordCertificateService::class);
            $generatedCount = 0;

            foreach ($participants as $participant) {
                $service->generate($participant, $template);
                $generatedCount++;
            }

            return redirect()->back()->with('success', "Berhasil men-generate {$generatedCount} sertifikat untuk peserta yang lulus.");
        } catch (\Exception $e) {
            Log::error('Error generate sertifikat mentor: ' . $e->getMessage());
            
            $errorMessage = $e->getMessage();
            
            // Tangkap error khusus jika file template fisik tidak ditemukan di storage
            if (str_contains($errorMessage, 'File template Word tidak ditemukan')) {
                return redirect()->back()->with('error', 'Mohon maaf, file master (template) sertifikat kelulusan belum tersedia di server. Silakan hubungi tim Kesekretariatan untuk segera mengunggah file template tersebut.');
            }
            
            return redirect()->back()->with('error', 'Terjadi kesalahan saat men-generate sertifikat: ' . $errorMessage);
        }
    }
}
