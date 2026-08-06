<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\Auth\MentorAuthController;
use App\Http\Controllers\PresenceController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ImportProgressController;

// Route All Fallback - MASTAMARU 2025 TELAH SELESAI
// Semua route diarahkan ke halaman ucapan terima kasih

// Route utama - Halaman ucapan terima kasih
// Route::get('/', function () {
//     return view('thanks');
// });

// Route::get('/thanks', function () {
//     return view('thanks');
// })->name('mastamaru.thanks');

// // Redirect semua route lain ke halaman /thanks
// Route::fallback(function () {
//     return redirect()->route('mastamaru.thanks');
// });


// SEMUA ROUTE DIBAWAH INI SUDAH TIDAK AKTIF - MASTAMARU 2025 TELAH SELESAI
// Uncomment jika diperlukan untuk maintenance
Route::get('/storage-link', function () {
    $targetFolder = base_path() . '/storage/app/public';
    $linkFolder =  $_SERVER['DOCUMENT_ROOT'] . '/storage';

    if (!file_exists($linkFolder)) {
        symlink($targetFolder, $linkFolder);
        return redirect()->back()->with('success', 'Penyimpanan di server sudah diaktifkan!');
    } else {
        return redirect()->back()->with('error', 'Penyimpanan di server telah tersedia!');
    }
})->name('storage-link');

// Route khusus untuk mengatasi masalah Permission/Menu hilang di server Production (cPanel/Shared Hosting)
Route::get('/super-fix', function () {
    try {
        \Illuminate\Support\Facades\Artisan::call('permission:cache-reset');
        \Illuminate\Support\Facades\Artisan::call('shield:generate', ['--all' => true]);
        \Illuminate\Support\Facades\Artisan::call('optimize:clear');
        return "<h1>✅ SUKSES!</h1><p>Cache web server telah dibersihkan dan Permission berhasil di-generate ulang.</p><p>Silakan kembali ke <a href='/admin'>Dashboard Admin</a>, masuk ke menu <b>Roles</b>, edit role Anda (Super Admin), lalu pastikan mencentang hak akses untuk API Configuration & API Data Record, lalu Save.</p>";
    } catch (\Exception $e) {
        return "<h1>❌ ERROR!</h1><p>" . $e->getMessage() . "</p>";
    }
});

// Route untuk menampilkan daftar semua perintah cache
Route::get('/cache-commands', function () {
    $commands = [
        'clear' => [
            'clear-all' => 'Bersihkan Semua Cache',
            'config-clear' => 'Bersihkan Cache Konfigurasi',
            'cache-clear' => 'Bersihkan Cache Aplikasi',
            'route-clear' => 'Bersihkan Cache Rute',
            'view-clear' => 'Bersihkan Cache View'
        ],
        'create' => [
            'optimize-cache' => 'Buat Cache Optimisasi (Semua)',
            'config-cache' => 'Buat Cache Konfigurasi',
            'route-cache' => 'Buat Cache Rute',
            'view-cache' => 'Buat Cache View'
        ]
    ];

    $html = '<!DOCTYPE html><html><head><title>Cache Commands</title><style>body{font-family:Arial,sans-serif;margin:40px;background:#f5f5f5}h1{color:#333;text-align:center}h2{color:#666;border-bottom:2px solid #ddd;padding-bottom:10px}.command-group{background:white;padding:20px;margin:20px 0;border-radius:8px;box-shadow:0 2px 4px rgba(0,0,0,0.1)}.command-link{display:block;padding:10px 15px;margin:5px 0;background:#007bff;color:white;text-decoration:none;border-radius:5px;transition:background 0.3s}.command-link:hover{background:#0056b3}.clear-commands .command-link{background:#dc3545}.clear-commands .command-link:hover{background:#c82333}.create-commands .command-link{background:#28a745}.create-commands .command-link:hover{background:#1e7e34}</style></head><body>';
    $html .= '<h1>🚀 Laravel Cache Management</h1>';

    $html .= '<div class="command-group clear-commands"><h2>🧹 Bersihkan Cache</h2>';
    foreach ($commands['clear'] as $route => $description) {
        $html .= '<a href="/' . $route . '" class="command-link">' . $description . '</a>';
    }
    $html .= '</div>';

    $html .= '<div class="command-group create-commands"><h2>⚡ Buat Cache</h2>';
    foreach ($commands['create'] as $route => $description) {
        $html .= '<a href="/' . $route . '" class="command-link">' . $description . '</a>';
    }
    $html .= '</div>';

    $html .= '<div class="command-group"><h2>ℹ️ Informasi</h2><p>Gunakan perintah di atas untuk mengelola cache Laravel. Setelah mengklik link, Anda akan melihat response JSON yang menunjukkan status operasi.</p><p><strong>Tips:</strong> Gunakan "Bersihkan Semua Cache" saat development, dan "Buat Cache Optimisasi" saat production untuk performa terbaik.</p></div>';

    $html .= '</body></html>';

    return response($html);
})->name('cache-commands');

// Route untuk menjalankan semua perintah sekaligus
Route::get('/clear-all', function () {
    try {
        Artisan::call('config:clear');
        Artisan::call('cache:clear');
        Artisan::call('route:clear');
        Artisan::call('view:clear');
        Artisan::call('optimize:clear');

        return response()->json([
            'status' => 'success',
            'message' => 'Semua cache berhasil dibersihkan!',
            'details' => [
                'config:clear' => 'Berhasil',
                'cache:clear' => 'Berhasil',
                'route:clear' => 'Berhasil',
                'view:clear' => 'Berhasil',
                'optimize:clear' => 'Berhasil'
            ]
        ]);
    } catch (Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Gagal membersihkan cache: ' . $e->getMessage()
        ], 500);
    }
})->name('clear-all');

// Route untuk membuat cache optimisasi
Route::get('/optimize-cache', function () {
    try {
        Artisan::call('config:cache');
        Artisan::call('route:cache');
        Artisan::call('view:cache');
        Artisan::call('optimize');

        return response()->json([
            'status' => 'success',
            'message' => 'Cache optimisasi berhasil dibuat! Situs sekarang lebih optimal.',
            'details' => [
                'config:cache' => 'Berhasil',
                'route:cache' => 'Berhasil',
                'view:cache' => 'Berhasil',
                'optimize' => 'Berhasil'
            ]
        ]);
    } catch (Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Gagal membuat cache optimisasi: ' . $e->getMessage()
        ], 500);
    }
})->name('optimize-cache');

// Route untuk membersihkan cache individual
Route::get('/config-clear', function () {
    try {
        Artisan::call('config:clear');
        return response()->json([
            'status' => 'success',
            'message' => 'Cache konfigurasi berhasil dibersihkan!'
        ]);
    } catch (Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Gagal membersihkan cache konfigurasi: ' . $e->getMessage()
        ], 500);
    }
})->name('config-clear');

Route::get('/cache-clear', function () {
    try {
        Artisan::call('cache:clear');
        return response()->json([
            'status' => 'success',
            'message' => 'Cache aplikasi berhasil dibersihkan!'
        ]);
    } catch (Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Gagal membersihkan cache aplikasi: ' . $e->getMessage()
        ], 500);
    }
})->name('cache-clear');

Route::get('/route-clear', function () {
    try {
        Artisan::call('route:clear');
        return response()->json([
            'status' => 'success',
            'message' => 'Cache rute berhasil dibersihkan!'
        ]);
    } catch (Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Gagal membersihkan cache rute: ' . $e->getMessage()
        ], 500);
    }
})->name('route-clear');

Route::get('/view-clear', function () {
    try {
        Artisan::call('view:clear');
        return response()->json([
            'status' => 'success',
            'message' => 'Cache view berhasil dibersihkan!'
        ]);
    } catch (Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Gagal membersihkan cache view: ' . $e->getMessage()
        ], 500);
    }
})->name('view-clear');

// Route untuk membuat cache individual
Route::get('/config-cache', function () {
    try {
        Artisan::call('config:cache');
        return response()->json([
            'status' => 'success',
            'message' => 'Cache konfigurasi berhasil dibuat!'
        ]);
    } catch (Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Gagal membuat cache konfigurasi: ' . $e->getMessage()
        ], 500);
    }
})->name('config-cache');

Route::get('/route-cache', function () {
    try {
        Artisan::call('route:cache');
        return response()->json([
            'status' => 'success',
            'message' => 'Cache rute berhasil dibuat!'
        ]);
    } catch (Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Gagal membuat cache rute: ' . $e->getMessage()
        ], 500);
    }
})->name('route-cache');

Route::get('/view-cache', function () {
    try {
        Artisan::call('view:cache');
        return response()->json([
            'status' => 'success',
            'message' => 'Cache view berhasil dibuat!'
        ]);
    } catch (Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Gagal membuat cache view: ' . $e->getMessage()
        ], 500);
    }
})->name('view-cache');

// Route untuk performance testing dengan k6
Route::middleware(['throttle:1000,1'])->prefix('performance-test')->group(function () {
    // Token untuk autentikasi performance test
    $validToken = 'k6-test-token-2025-mastaumpo';

    // Validasi token untuk semua route dalam group ini
    Route::group([], function () use ($validToken) {
        // Middleware validasi token akan diterapkan di setiap route individual

        // Endpoint ringan untuk baseline test
        Route::get('/light', function (\Illuminate\Http\Request $request) use ($validToken) {
            $token = $request->header('X-Performance-Token') ?? $request->get('token');
            if ($token !== $validToken) {
                return response()->json([
                    'error' => 'Invalid or missing performance test token',
                    'message' => 'Gunakan header X-Performance-Token atau parameter token dengan nilai yang valid'
                ], 401);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Light endpoint test',
                'timestamp' => now()->toISOString(),
                'server_time' => microtime(true)
            ]);
        })->name('perf.light');

        // Endpoint medium dengan simulasi database query
        Route::get('/medium', function (\Illuminate\Http\Request $request) use ($validToken) {
            $token = $request->header('X-Performance-Token') ?? $request->get('token');
            if ($token !== $validToken) {
                return response()->json([
                    'error' => 'Invalid or missing performance test token',
                    'message' => 'Gunakan header X-Performance-Token atau parameter token dengan nilai yang valid'
                ], 401);
            }

            // Simulasi query database
            $startTime = microtime(true);

            // Simulasi beban kerja medium
            $data = [];
            for ($i = 0; $i < 1000; $i++) {
                $data[] = [
                    'id' => $i,
                    'name' => 'User ' . $i,
                    'email' => 'user' . $i . '@test.com',
                    'created_at' => now()->subDays(rand(1, 365))->toISOString()
                ];
            }

            $processingTime = microtime(true) - $startTime;

            return response()->json([
                'status' => 'success',
                'message' => 'Medium endpoint test',
                'data_count' => count($data),
                'processing_time_ms' => round($processingTime * 1000, 2),
                'timestamp' => now()->toISOString(),
                'memory_usage' => memory_get_usage(true)
            ]);
        })->name('perf.medium');

        // Endpoint heavy dengan beban kerja tinggi
        Route::get('/heavy', function (\Illuminate\Http\Request $request) use ($validToken) {
            $token = $request->header('X-Performance-Token') ?? $request->get('token');
            if ($token !== $validToken) {
                return response()->json([
                    'error' => 'Invalid or missing performance test token',
                    'message' => 'Gunakan header X-Performance-Token atau parameter token dengan nilai yang valid'
                ], 401);
            }

            $startTime = microtime(true);

            // Simulasi beban kerja berat
            $result = [];
            for ($i = 0; $i < 10000; $i++) {
                $hash = hash('sha256', 'performance-test-' . $i . '-' . time());
                $result[] = [
                    'iteration' => $i,
                    'hash' => $hash,
                    'random' => rand(1000, 9999)
                ];
            }

            // Simulasi operasi file
            $tempFile = tempnam(sys_get_temp_dir(), 'k6_test_');
            file_put_contents($tempFile, json_encode($result));
            $fileSize = filesize($tempFile);
            unlink($tempFile);

            $processingTime = microtime(true) - $startTime;

            return response()->json([
                'status' => 'success',
                'message' => 'Heavy endpoint test',
                'iterations' => count($result),
                'file_size_bytes' => $fileSize,
                'processing_time_ms' => round($processingTime * 1000, 2),
                'timestamp' => now()->toISOString(),
                'memory_peak' => memory_get_peak_usage(true)
            ]);
        })->name('perf.heavy');

        // Endpoint untuk test POST request
        Route::post('/post-test', function (\Illuminate\Http\Request $request) {
            $startTime = microtime(true);

            $data = $request->all();
            $dataSize = strlen(json_encode($data));

            // Simulasi validasi dan pemrosesan data
            $processedData = [];
            foreach ($data as $key => $value) {
                $processedData[$key] = [
                    'original' => $value,
                    'processed' => is_string($value) ? strtoupper($value) : $value,
                    'hash' => hash('md5', serialize($value))
                ];
            }

            $processingTime = microtime(true) - $startTime;

            return response()->json([
                'status' => 'success',
                'message' => 'POST endpoint test',
                'input_size_bytes' => $dataSize,
                'fields_processed' => count($data),
                'processing_time_ms' => round($processingTime * 1000, 2),
                'timestamp' => now()->toISOString()
            ]);
        })->name('perf.post');

        // Endpoint untuk test dengan delay
        Route::get('/delay/{seconds}', function (\Illuminate\Http\Request $request, $seconds) use ($validToken) {
            $token = $request->header('X-Performance-Token') ?? $request->get('token');
            if ($token !== $validToken) {
                return response()->json([
                    'error' => 'Invalid or missing performance test token',
                    'message' => 'Gunakan header X-Performance-Token atau parameter token dengan nilai yang valid'
                ], 401);
            }

            $seconds = min(max((int)$seconds, 1), 10); // Limit 1-10 detik

            sleep($seconds);

            return response()->json([
                'status' => 'success',
                'message' => 'Delay endpoint test',
                'delay_seconds' => $seconds,
                'timestamp' => now()->toISOString()
            ]);
        })->name('perf.delay');

        // Endpoint untuk mendapatkan informasi sistem
        Route::get('/system-info', function (\Illuminate\Http\Request $request) use ($validToken) {
            $token = $request->header('X-Performance-Token') ?? $request->get('token');
            if ($token !== $validToken) {
                return response()->json([
                    'error' => 'Invalid or missing performance test token',
                    'message' => 'Gunakan header X-Performance-Token atau parameter token dengan nilai yang valid'
                ], 401);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'System information',
                'php_version' => PHP_VERSION,
                'laravel_version' => app()->version(),
                'memory_limit' => ini_get('memory_limit'),
                'max_execution_time' => ini_get('max_execution_time'),
                'current_memory_usage' => memory_get_usage(true),
                'peak_memory_usage' => memory_get_peak_usage(true),
                'server_load' => sys_getloadavg(),
                'timestamp' => now()->toISOString()
            ]);
        })->name('perf.system');

        // Endpoint untuk test error handling
        Route::get('/error-test/{code}', function (\Illuminate\Http\Request $request, $code) use ($validToken) {
            $token = $request->header('X-Performance-Token') ?? $request->get('token');
            if ($token !== $validToken) {
                return response()->json([
                    'error' => 'Invalid or missing performance test token',
                    'message' => 'Gunakan header X-Performance-Token atau parameter token dengan nilai yang valid'
                ], 401);
            }

            $validCodes = [400, 401, 403, 404, 422, 500, 503];
            $code = in_array((int)$code, $validCodes) ? (int)$code : 500;

            $messages = [
                400 => 'Bad Request Test',
                401 => 'Unauthorized Test',
                403 => 'Forbidden Test',
                404 => 'Not Found Test',
                422 => 'Unprocessable Entity Test',
                500 => 'Internal Server Error Test',
                503 => 'Service Unavailable Test'
            ];

            return response()->json([
                'error' => true,
                'code' => $code,
                'message' => $messages[$code],
                'timestamp' => now()->toISOString()
            ], $code);
        })->name('perf.error');

        // Endpoint untuk mendapatkan daftar semua endpoint test
        Route::get('/endpoints', function (\Illuminate\Http\Request $request) use ($validToken) {
            $token = $request->header('X-Performance-Token') ?? $request->get('token');
            if ($token !== $validToken) {
                return response()->json([
                    'error' => 'Invalid or missing performance test token',
                    'message' => 'Gunakan header X-Performance-Token atau parameter token dengan nilai yang valid'
                ], 401);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Available performance test endpoints',
                'token_info' => [
                    'required' => true,
                    'header' => 'X-Performance-Token',
                    'parameter' => 'token',
                    'note' => 'Gunakan salah satu cara untuk menyertakan token'
                ],
                'endpoints' => [
                    'GET /performance-test/light' => 'Endpoint ringan untuk baseline test',
                    'GET /performance-test/medium' => 'Endpoint medium dengan simulasi database',
                    'GET /performance-test/heavy' => 'Endpoint berat dengan beban kerja tinggi',
                    'POST /performance-test/post-test' => 'Test POST request dengan data processing',
                    'GET /performance-test/delay/{1-10}' => 'Test dengan delay 1-10 detik',
                    'GET /performance-test/system-info' => 'Informasi sistem server',
                    'GET /performance-test/error-test/{code}' => 'Test error handling (400,401,403,404,422,500,503)',
                    'GET /performance-test/endpoints' => 'Daftar endpoint ini'
                ],
                'example_k6_script' => [
                    'import http from "k6/http";',
                    'export default function() {',
                    '  const params = { headers: { "X-Performance-Token": "k6-test-token-2025-mastaumpo" } };',
                    '  http.get("http://localhost:8000/performance-test/light", params);',
                    '}'
                ],
                'timestamp' => now()->toISOString()
            ]);
        })->name('perf.endpoints');
    });
});

// Route untuk halaman utama peserta
Route::get('/', [HomeController::class, 'index'])->name('home.index');
Route::post('/check-student', [HomeController::class, 'checkStudent'])->name('home.check-student');
Route::get('/check-student', function () {
    return redirect()->route('home.index')->with('error', 'Silakan gunakan form di halaman utama untuk memeriksa data peserta.');
});
// Route::post('/refresh-code', [HomeController::class, 'refreshCode'])->name('home.refresh-code');

Route::get('/groups', [HomeController::class, 'groups'])->name('home.groups');
Route::get('/remake', [HomeController::class, 'remake'])->name('home.remake');
Route::post('/remake', [HomeController::class, 'storeParticipant'])->name('home.store-participant');

// Route untuk autentikasi mentor
Route::prefix('mentor')->group(function () {
    Route::get('/', function () {
        return redirect()->route('mentor.login');
    });
    // Route untuk login
    Route::get('/login', [MentorAuthController::class, 'showLoginForm'])->name('mentor.login');
    Route::post('/login', [MentorAuthController::class, 'login'])->name('mentor.login.post');

    // Route untuk logout
    Route::post('/logout', [MentorAuthController::class, 'logout'])->name('mentor.logout');

    // Route untuk dashboard (protected)
    Route::get('/dashboard', [MentorAuthController::class, 'dashboard'])->name('mentor.dashboard')->middleware('mentor.auth');

    // Rute untuk detail presensi mentor
    Route::get('/presence/{slug}', [PresenceController::class, 'show'])->name('mentor.presence.detail')->middleware('mentor.auth');

    // Route untuk proses scan QR dan manual input
    Route::post('/presence/{slug}/scan', [PresenceController::class, 'processScan'])->name('mentor.presence.scan')->middleware('mentor.auth');
    Route::post('/presence/{slug}/manual', [PresenceController::class, 'processManual'])->name('mentor.presence.manual')->middleware('mentor.auth');
    Route::post('/presence/{slug}/check-session', [PresenceController::class, 'checkSessionStatus'])->name('presence.check-session')->middleware('mentor.auth');

    // Route untuk mengambil data presensi via AJAX
    Route::get('/presence/{slug}/data', [PresenceController::class, 'getAttendanceData'])->name('mentor.presence.data')->middleware('mentor.auth');

    // Route untuk membuat record presensi baru (untuk peserta yang belum hadir)
    Route::post('/presence/{slug}/create-record', [PresenceController::class, 'createAttendanceRecord'])->name('mentor.presence.create-record')->middleware('mentor.auth');
});

// Fallback route untuk menangani 404 - harus diletakkan di akhir
Route::fallback(function () {
    return response()->view('errors.404', [], 404);
});
