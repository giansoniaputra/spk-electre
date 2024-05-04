<?php

use App\Models\Kriteria;
use App\Models\Alternatif;
use App\Models\SubKriteria;
use App\Models\PerhitunganMoora;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\KriteriaController;
use App\Http\Controllers\AlternatifController;
use App\Http\Controllers\SubKriteriaController;
use App\Http\Controllers\PerhitunganMooraController;
use App\Http\Controllers\PerhitunganElectreController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    $data = [
        'title' => 'Dashboard',
        'alternatif' => Alternatif::count('id'),
        'kriteria' => Kriteria::count('id'),
    ];
    return view('dashboard', $data);
})->middleware('auth');
Route::get('/home', function () {
    $data = [
        'title' => 'Dashboard',
        'alternatif' => Alternatif::count('id'),
        'kriteria' => Kriteria::count('id'),
    ];
    return view('dashboard', $data);
})->middleware('auth');

Route::get('/rekomendasi', function () {
    $getSub = new SubKriteria;
    $data = [
        'title' => 'Rekomendasi Tempat Wisata Terbaik',
        'mooras' => DB::table('perhitungan_mooras as a')
            ->join('alternatifs as b', 'a.alternatif_uuid', '=', 'b.uuid')
            ->select('a.*', 'b.alternatif', 'b.keterangan')
            ->orderBy('b.alternatif', 'asc'),
        'kriterias' => DB::table('kriterias as a')->get(),
        'alternatifs' => Alternatif::orderBy('alternatif', 'asc')->get(),
        'sub_kriterias' => $getSub,
        'sum_kriteria' => Kriteria::count('id'),
    ];
    return view('rekomendasi.index', $data);
})->middleware('guest');
// AUTH
Route::get('/auth', [AuthController::class, 'index'])->name('login')->middleware('guest');
Route::post('/authenticate', [AuthController::class, 'authenticate']);
Route::get('/logout', [AuthController::class, 'logout']);
Route::get('/register', [AuthController::class, 'register']);
Route::post('/register-create', [AuthController::class, 'create']);
Route::get('/dataTablesUser', [AuthController::class, 'dataTables']);

// KRITERIA
Route::resource('/kriteria', KriteriaController::class)->middleware('auth');
Route::get('/dataTablesKriteria', [KriteriaController::class, 'dataTablesKriteria'])->middleware('auth');
Route::get('/kriteriaEdit/{kreteria:uuid}', [KriteriaController::class, 'edit'])->middleware('auth');
// SUB KRITERIA
Route::resource('/subKriteria', SubKriteriaController::class);
Route::get('/dataTablesSubKriteria', [SubKriteriaController::class, 'dataTablesSubKriteria'])->middleware('auth');
Route::get('/setBobot/{uuid}/{kreteria_uuid}', [SubKriteriaController::class, 'set_bobot'])->middleware('auth');
// Alternatif
Route::get('alternatif', [AlternatifController::class, 'index'])->middleware('auth');
Route::get('/dataTablesAlternatif', [AlternatifController::class, 'dataTablesAlternatif'])->middleware('auth');
Route::post('/alternatif-store', [AlternatifController::class, 'store'])->middleware('auth');
Route::get('/alternatif-edit/{alternatif:uuid}', [AlternatifController::class, 'edit'])->middleware('auth');
Route::post('/alternatif-update/{alternatif:uuid}', [AlternatifController::class, 'update'])->middleware('auth');
Route::post('/alternatif-destroy/{alternatif:uuid}', [AlternatifController::class, 'destroy'])->middleware('auth');

// Perhitungan Electre
Route::get('/electre', [PerhitunganElectreController::class, 'index'])->middleware('auth');
Route::get('/electre-create', [PerhitunganElectreController::class, 'create'])->middleware('auth');
Route::get('/electre-update/{electre:uuid}', [PerhitunganElectreController::class, 'update'])->middleware('auth');
Route::get('/electre-normalisasi', [PerhitunganElectreController::class, 'normalisasi'])->middleware('auth');
Route::get('/electre-normalisasi-bobot', [PerhitunganElectreController::class, 'bobot_normalisasi'])->middleware('auth');
Route::get('/concordance-electre', [PerhitunganElectreController::class, 'concordance_electre'])->middleware('auth');
Route::get('/matrix_dis', [PerhitunganElectreController::class, 'matrix_dis'])->middleware('auth');
Route::get('/electre-normalisasi-user', [PerhitunganElectreController::class, 'normalisasi_user']);
Route::get('/electre-normalisasi-bobot-user', [PerhitunganElectreController::class, 'bobot_normalisasi_user']);
Route::get('/concordance-electre-user', [PerhitunganElectreController::class, 'concordance_electre_user']);
Route::get('/matrix_dis-user', [PerhitunganElectreController::class, 'matrix_dis_user']);

// Perhitunga Moora
Route::get('/moora', [PerhitunganMooraController::class, 'index'])->middleware('auth');
Route::get('/moora-create', [PerhitunganMooraController::class, 'create'])->middleware('auth');
Route::get('/moora-update/{electre:uuid}', [PerhitunganMooraController::class, 'update'])->middleware('auth');
Route::get('/moora-normalisasi', [PerhitunganMooraController::class, 'normalisasi'])->middleware('auth');
Route::get('/moora-preferensi', [PerhitunganMooraController::class, 'preferensi'])->middleware('auth');
Route::get('/saw', [PerhitunganMooraController::class, 'index_saw'])->middleware('auth');
// REKAP
Route::get('/rekap', [PerhitunganMooraController::class, 'rekap']);
