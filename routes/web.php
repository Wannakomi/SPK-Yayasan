    <?php

    use Illuminate\Support\Facades\Route;
    use App\Http\Controllers\DashboardController;
    use App\Http\Controllers\AkunController;
    use App\Http\Controllers\CalonPenerimaController;
    use App\Http\Controllers\PenilaianController;
    use App\Http\Controllers\KriteriaController;
    use App\Http\Controllers\SawController;
    use App\Http\Controllers\RankingController;
    use App\Http\Controllers\LaporanController;
    use App\Http\Controllers\SettingsController;
    use App\Http\Controllers\AuthController;
    use App\Http\Controllers\ProfileController;
    use App\Http\Controllers\KetuaController;
    use App\Http\Controllers\ChatbotController;

    /* ─── AUTH (guest only) ─────────────────────────────── */
    Route::middleware('guest')->group(function () {
        Route::get('/login',          [AuthController::class, 'showLogin'])->name('login');
        Route::post('/login',         [AuthController::class, 'login'])->name('login.post');
    });

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

    /* ─── AUTHENTICATED ROUTES ──────────────────────────── */
    Route::middleware(['auth'])->group(function () {

        /* Dashboard */
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        /* Kelola Akun — superadmin only (edit/delete) */
        Route::resource('akun', AkunController::class)->names('akun');
        Route::patch('akun/{akun}/toggle-status', [AkunController::class, 'toggleStatus'])->name('akun.toggle-status');

        /* Profile */
        Route::get('profile',          [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('profile',        [ProfileController::class, 'update'])->name('profile.update');
        Route::patch('profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

        /* Data Calon Penerima */
        Route::resource('calon-penerima', CalonPenerimaController::class)->names('calon-penerima');
        Route::get('calon-penerima-export', [CalonPenerimaController::class, 'export'])->name('calon-penerima.export');

        /* Data Penilaian */
        Route::resource('penilaian', PenilaianController::class)->names('penilaian');

        /* Bobot & Kriteria */
        Route::resource('kriteria', KriteriaController::class)->names('kriteria');
        Route::post('kriteria/reorder', [KriteriaController::class, 'reorder'])->name('kriteria.reorder');

        /* Proses SAW */
        Route::get('saw',              [SawController::class, 'index'])->name('saw.index');
        Route::post('saw/hitung',      [SawController::class, 'hitung'])->name('saw.hitung');
        Route::post('saw/reset',       [SawController::class, 'reset'])->name('saw.reset');
        // Route::get('saw/preview',      [SawController::class, 'preview'])->name('saw.preview');

        /* Hasil Ranking */
        Route::get('ranking',          [RankingController::class, 'index'])->name('ranking.index');
        Route::get('ranking/export',   [RankingController::class, 'export'])->name('ranking.export');

        /* Cetak Laporan */
        Route::get('laporan',          [LaporanController::class, 'index'])->name('laporan.index');
        Route::get('laporan/cetak',    [LaporanController::class, 'cetak'])->name('laporan.cetak');
        Route::get('laporan/export-pdf', [LaporanController::class, 'exportPdf'])->name('laporan.export-pdf');
        Route::get('/laporan/export-csv', [LaporanController::class, 'csv'])->name('laporan.csv');
        
        /* Settings */
        Route::get('settings',         [SettingsController::class, 'index'])->name('settings.index');
        Route::patch('settings/umum',  [SettingsController::class, 'updateUmum'])->name('settings.umum');
        Route::patch('settings/spk',   [SettingsController::class, 'updateSpk'])->name('settings.spk');
        Route::patch('settings/periode', [SettingsController::class, 'updatePeriode'])->name('settings.periode');
        Route::post('settings/periode/buka', [SettingsController::class, 'bukaPeriode'])->name('settings.periode.buka');

        /* ─── PORTAL KETUA YAYASAN ──────────────────────── */
        Route::prefix('ketua')->name('ketua.')->group(function () {
            Route::get('/',          [KetuaController::class, 'index'])->name('index');
            Route::get('/ranking',   [KetuaController::class, 'ranking'])->name('ranking');
            Route::get('/laporan',   [KetuaController::class, 'laporan'])->name('laporan');
            Route::get('/cetak-pdf', [KetuaController::class, 'cetakPdf'])->name('cetak-pdf');
            Route::get('/download-pdf', [KetuaController::class, 'downloadPdf'])->name('download-pdf');
            Route::get('/export-csv',[KetuaController::class, 'exportCsv'])->name('export-csv');
        });

        Route::post('/chatbot', [ChatbotController::class, 'chat'])->name('chatbot');

    });
