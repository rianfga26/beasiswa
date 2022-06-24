<?php

use App\Models\Biodata;
use App\Http\Middleware\CheckAdmin;
use App\Http\Middleware\CheckMhs;
use App\Http\Middleware\CheckLogin;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\PDFController;
use App\Models\PeriodeAktif;
use GuzzleHttp\Psr7\Request;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


// logout
Route::get('logout', [LoginController::class, 'logout'])->name('logout');

Route::middleware(CheckLogin::class)->group(function () {
    // redirect '/' to login page
    Route::get('/', function () {
        return view('login');
    });

    // login
    Route::post('login', [LoginController::class, 'do_login'])->name('do_login');
});

// admin
Route::middleware(CheckAdmin::class)->group(function () {
    Route::get('admin', [AdminController::class, 'index'])->name('admin');
    Route::post('admin/getOptions', [AdminController::class, 'getOptions'])->name('admin.options');
    Route::post('admin/changeStatus', [AdminController::class, 'changeStatus'])->name('admin.change.status');
    Route::post('admin/gantiPeriode', [AdminController::class, 'gantiPeriode'])->name('admin.ganti.periode');
});

// mahasiswa
Route::middleware(CheckMhs::class)->group(function () {
    Route::get('validasi', [PostController::class, 'validasi'])->name('validasi');
    Route::get('mahasiswa/', function () {
        $periodeAktif = PeriodeAktif::first();
        if($periodeAktif != null){
            $bio = Biodata::with(['berkas' => function ($query) use($periodeAktif) {
                $query->where('periode', $periodeAktif->periode);
            }])->where('nim', session('nim'))->first();
        }else{
            $bio = Biodata::where('nim', session('nim'))->first();
        }
        return view('index', compact('bio', 'periodeAktif'));
    })->name('mhs');
    Route::post('post', [PostController::class, 'post'])->name('post');
    Route::post('post/nomor', [PostController::class, 'postNomor'])->name('post_nomor');
    Route::get('delete/{name?}/{nim}', [PostController::class, 'destroy'])->name('delete');
    Route::get('generate-pdf/{nim}', [PDFController::class, 'generatePDF'])->name('pdf.generate');
});
