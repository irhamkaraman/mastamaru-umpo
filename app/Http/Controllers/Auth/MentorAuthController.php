<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\CertificateTemplate;
use App\Models\Mentor;
use App\Models\PresenceSession;
use App\Services\WordCertificateService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
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
        $mentor = Mentor::where('student_id', $request->student_id)->first();
        if (! $mentor || ! Hash::check($request->password, $mentor->password)) {
            throw ValidationException::withMessages([
                'student_id' => 'ID atau kata sandi tidak valid.',
            ]);
        }
        session([
            'mentor_id' => $mentor->id,
            'mentor_name' => $mentor->name,
            'mentor_student_id' => $mentor->student_id,
            'mentor_group_id' => $mentor->group_id,
        ]);

        return redirect()->route('mentor.dashboard')->with('success', 'Login berhasil! Selamat datang, '.$mentor->name);
    }

    /**
     * Logout mentor
     */
    public function logout(Request $request)
    {
        $request->session()->forget([
            'mentor_id',
            'mentor_name',
            'mentor_student_id',
            'mentor_group_id',
        ]);
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
            if (! $mentorId) {
                return redirect('/mentor/login')->with('error', 'Sesi tidak valid. Silakan login kembali.');
            }
            $mentor = Cache::remember('mentor_dashboard_'.$mentorId, 600, function () use ($mentorId) {
                return Mentor::with(['group', 'attendances'])->find($mentorId);
            });
            if (! $mentor) {
                Cache::forget('mentor_dashboard_'.$mentorId);
                session()->forget(['mentor_id', 'mentor_name', 'mentor_student_id', 'mentor_group_id']);

                return redirect('/mentor/login')->with('error', 'Data mentor tidak ditemukan. Silakan login kembali.');
            }
            $activeSessions = Cache::remember('active_sessions_all', 60, function () {
                return PresenceSession::where('is_active', true)
                    ->orderByRaw('
                        CASE 
                            WHEN NOW() BETWEEN start_time AND end_time THEN 1 
                            WHEN NOW() < start_time THEN 2 
                            ELSE 3 
                        END ASC
                    ')
                    ->orderBy('start_time', 'asc')
                    ->get();
            });
            $todayStats = Cache::remember('today_stats_'.date('Y-m-d'), 60, function () {
                return PresenceSession::where('is_active', true)
                    ->whereDate('start_time', today())
                    ->count();
            });

            return view('mentor.dashboard', compact('mentor', 'activeSessions', 'todayStats'));
        } catch (Exception $e) {
            Log::error('Error di dashboard mentor: '.$e->getMessage());
            if (isset($mentorId)) {
                Cache::forget('mentor_dashboard_'.$mentorId);
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
            if (! $mentorId) {
                return redirect('/mentor/login')->with('error', 'Sesi tidak valid. Silakan login kembali.');
            }
            $mentor = Mentor::find($mentorId);
            if (! $mentor) {
                session()->forget(['mentor_id', 'mentor_name', 'mentor_student_id', 'mentor_group_id']);

                return redirect('/mentor/login')->with('error', 'Data mentor tidak ditemukan. Silakan login kembali.');
            }

            return view('mentor.presence-detail', compact('slug'));
        } catch (Exception $e) {
            Log::error('Error di presence detail mentor: '.$e->getMessage());
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
        if (! $mentorId) {
            return redirect('/mentor/login')->with('error', 'Sesi tidak valid. Silakan login kembali.');
        }
        $request->validate([
            'phone_number' => 'nullable|string|max:20',
        ], [
            'phone_number.max' => 'Nomor WhatsApp / telepon maksimal 20 karakter.',
        ]);
        $mentor = Mentor::find($mentorId);
        if (! $mentor) {
            return redirect('/mentor/login')->with('error', 'Data mentor tidak ditemukan.');
        }
        $mentor->update([
            'phone_number' => $request->input('phone_number'),
        ]);
        Cache::forget('mentor_dashboard_'.$mentorId);

        return redirect()->route('mentor.dashboard')->with('success', 'Nomor telepon / WhatsApp berhasil diperbarui!');
    }

    /**
     * Method untuk menghapus cache dashboard
     */
    public static function clearDashboardCache()
    {
        Cache::forget('active_sessions_all');
        Cache::forget('today_stats_'.date('Y-m-d'));
    }

    /**
     * Halaman daftar peserta kelompok
     */
    public function participants()
    {
        try {
            $mentorId = session('mentor_id');
            if (! $mentorId) {
                return redirect('/mentor/login')->with('error', 'Sesi tidak valid.');
            }
            $mentor = Mentor::with('group')->find($mentorId);
            if (! $mentor || ! $mentor->group_id) {
                return redirect()->route('mentor.dashboard')->with('error', 'Anda tidak memiliki kelompok yang ditugaskan.');
            }
            $participants = Attendance::with('assessment')
                ->where('group_id', $mentor->group_id)
                ->get();

            return view('mentor.participants', compact('mentor', 'participants'));
        } catch (Exception $e) {
            Log::error('Error di participants mentor: '.$e->getMessage());

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
            if (! $mentorId) {
                return redirect('/mentor/login')->with('error', 'Sesi tidak valid.');
            }
            $request->validate([
                'selected_students' => 'required|string',
                'mentor_nim' => 'required|string',
                'mentor_password' => 'required|string',
            ]);
            $selectedIds = json_decode($request->input('selected_students'), true);
            if (! is_array($selectedIds) || empty($selectedIds)) {
                return redirect()->back()->with('error', 'Tidak ada peserta yang dipilih.');
            }
            $mentor = Mentor::find($mentorId);
            if (! $mentor || ! $mentor->group_id) {
                return redirect()->route('mentor.dashboard')->with('error', 'Anda tidak memiliki kelompok yang ditugaskan.');
            }
            if ($mentor->student_id !== $request->input('mentor_nim') || ! \Hash::check($request->input('mentor_password'), $mentor->password)) {
                return redirect()->back()->with('error', 'Otorisasi gagal! NIM atau Password yang Anda masukkan tidak sesuai.');
            }
            $templateLulus = CertificateTemplate::getActiveFor('lulus') ?? CertificateTemplate::getActiveFor('semua');
            $templateGagal = CertificateTemplate::getActiveFor('gagal') ?? CertificateTemplate::getActiveFor('semua');
            if (! $templateLulus && ! $templateGagal) {
                return redirect()->back()->with('error', 'Template sertifikat belum diatur atau belum aktif.');
            }
            $participants = Attendance::where('group_id', $mentor->group_id)
                ->whereIn('id', $selectedIds)
                ->get();
            if ($participants->isEmpty()) {
                return redirect()->back()->with('error', 'Peserta yang Anda pilih tidak valid atau belum ada peserta di kelompok Anda.');
            }
            $service = app(WordCertificateService::class);
            $generatedCount = 0;
            foreach ($participants as $participant) {
                $status = $participant->status ?? 'gagal';
                if ($status === 'proses') {
                    $status = 'lulus';
                }
                $template = $status === 'lulus' ? $templateLulus : $templateGagal;
                if (! $template) {
                    continue;
                }
                $service->generate($participant, $template);
                $participant->update(['status' => $status]);
                if ($participant->assessment) {
                    $participant->assessment->update(['status' => $status]);
                }
                $generatedCount++;
            }
            if ($generatedCount === 0) {
                return redirect()->back()->with('error', 'Gagal men-generate sertifikat. Pastikan template untuk status peserta tersedia.');
            }

            return redirect()->back()->with('success', "Berhasil men-generate {$generatedCount} sertifikat.");
        } catch (Exception $e) {
            Log::error('Error generate sertifikat mentor: '.$e->getMessage());
            $errorMessage = $e->getMessage();
            if (str_contains($errorMessage, 'File template Word tidak ditemukan')) {
                return redirect()->back()->with('error', 'Mohon maaf, file master (template) sertifikat kelulusan belum tersedia di server. Silakan hubungi tim Kesekretariatan untuk segera mengunggah file template tersebut.');
            }

            return redirect()->back()->with('error', 'Terjadi kesalahan saat men-generate sertifikat: '.$errorMessage);
        }
    }

    public function setParticipantStatus(Request $request)
    {
        try {
            $mentorId = session('mentor_id');
            if (! $mentorId) {
                return redirect()->back()->with('error', 'Sesi tidak valid.');
            }
            $request->validate([
                'selected_students' => 'required|string',
                'mentor_nim' => 'required|string',
                'mentor_password' => 'required|string',
                'status' => 'required|in:lulus,gagal',
            ]);
            $selectedIds = json_decode($request->input('selected_students'), true);
            if (! is_array($selectedIds) || empty($selectedIds)) {
                return redirect()->back()->with('error', 'Tidak ada peserta yang dipilih.');
            }
            $mentor = Mentor::find($mentorId);
            if (! $mentor || ! $mentor->group_id) {
                return redirect()->back()->with('error', 'Anda tidak memiliki kelompok yang ditugaskan.');
            }
            if ($mentor->student_id !== $request->input('mentor_nim') || ! \Hash::check($request->input('mentor_password'), $mentor->password)) {
                return redirect()->back()->with('error', 'Otorisasi gagal! NIM atau Password salah.');
            }
            $participants = Attendance::where('group_id', $mentor->group_id)
                ->whereIn('id', $selectedIds)
                ->get();
            if ($participants->isEmpty()) {
                return redirect()->back()->with('error', 'Peserta tidak valid.');
            }
            $status = $request->input('status');
            $updatedCount = 0;
            foreach ($participants as $participant) {
                $participant->update(['status' => $status]);
                if ($participant->assessment) {
                    $participant->assessment->update(['status' => $status]);
                }
                $updatedCount++;
            }

            return redirect()->back()->with('success', "Berhasil mengatur status $status untuk $updatedCount peserta.");
        } catch (Exception $e) {
            Log::error('Error set status peserta mentor: '.$e->getMessage());

            return redirect()->back()->with('error', 'Terjadi kesalahan sistem: '.$e->getMessage());
        }
    }
}
