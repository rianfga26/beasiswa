<?php

namespace App\Http\Controllers;

use App\Models\Berkas;
use App\Models\Biodata;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\LoginController;
use App\Models\PeriodeAktif;
use Yajra\DataTables\Facades\DataTables;


class AdminController extends Controller
{
    public function index()
    {
        $berkas = Berkas::all();
        $periodeSet = PeriodeAktif::first();
        $allperiode = self::get_periode();
        return view('admin', compact('berkas', 'allperiode', 'periodeSet'));
    }

    public function getOptions(Request $request)
    {
        // return $request;
        $allperiode = self::get_periode();
        $getPeriodeAktif = PeriodeAktif::first();
        $periodeAktif = $getPeriodeAktif == null ? $allperiode->dataPeriode[0]->periode : $getPeriodeAktif->periode;

        // tampilkan semua data periode ini
        if ($request->prodi == '' && $request->periode == '' && $request->status == '') {
            $datas = Berkas::where('periode', $periodeAktif)->where('status', '!=', 'empty')->get();
            $txt = 'tampilkan semua data periode ini';
        }
        // tampilkan status
        else if ($request->prodi == '' && $request->periode == '') {
            if ($request->status == 'empty') {
                $datas = Biodata::with(['berkas' => function ($query) use ($periodeAktif) {
                    $query->where('periode', $periodeAktif);
                }])->where('status', 'empty')->get();

                $txt = 'tampilkan status'.$periodeAktif;
            } else {
                $datas = Berkas::where('status', $request->status)->get();
                $txt = 'tampilkan status bukan empty';
            }
        }
        // tampilkan periode
        else if ($request->prodi == '' && $request->status == '') {
            $datas = Berkas::where('periode', $request->periode)->where('status', '!=', 'empty')->get();
            $txt = 'tampilkan periode';
        }
        // tampilkan prodi
        else if ($request->status == '' && $request->periode == '') {
            $datas = Berkas::whereHas('biodata', function ($query) use ($request) {
                $query->where('prodi', $request->prodi);
            })->where('periode', $periodeAktif)->where('status', '!=', 'empty')->get();
            $txt = 'tampilkan prodi';
        }
        // tampilkan prodi dan status
        else if ($request->periode == '') {
            if ($request->status == 'empty') {
                $datas = Biodata::where('prodi', $request->prodi)->where('status', 'empty')->with('berkas', function ($query) use ($periodeAktif) {
                    $query->where('periode', $periodeAktif);
                })->get();
            } else {
                $datas = Berkas::whereHas('biodata', function ($query) use ($request) {
                    $query->where('prodi', $request->prodi);
                })->where('status', $request->status)->where('periode', $allperiode->dataPeriode[0]->periode)->get();
            }
            $txt = 'tampilkan prodi dan status';
        }
        // tampilkan periode dan prodi
        else if ($request->status == '') {
            $datas = Berkas::whereHas('biodata', function ($query) use ($request) {
                $query->where('prodi', $request->prodi);
            })->where('periode', $request->periode)->where('status', '!=', 'empty')->get();
            $txt = 'tampilkan periode dan prodi';
        }
        // tampilkan status dan periode
        else if ($request->prodi == '') {
            if ($request->status == 'empty') {
                $datas = Biodata::whereHas('berkas', function ($query) use ($request) {
                    $query->where('status', 'empty')->where('periode', $request->periode);
                })->get();
                $txt = 'tampilkan status dan periode berkas ada';
            } else {
                $datas = Berkas::where('status', $request->status)->where('periode', $request->periode)->get();
            }
        }
        // tampilkan semua
        else {
            if ($request->status == 'empty') {
                $datas = Biodata::where('prodi', $request->prodi)->where('status', 'empty')->with('berkas', function ($query) use ($request) {
                    $query->where('status', 'empty')->where('periode', $request->periode);
                })->get();
                foreach($datas as $data){
                    if(count($data->berkas) != 0){
                        $datas = Biodata::where('prodi', $request->prodi)->where('status', 'empty')->whereHas('berkas', function ($query) use ($request) {
                            $query->where('status', 'empty')->where('periode', $request->periode);
                        })->get();
                    }
                }
            } else {
                $datas = Berkas::whereHas('biodata', function ($query) use ($request) {
                    $query->where('prodi', $request->prodi);
                })->where('status', $request->status)->where('periode', $request->periode)->get();
            }
            $txt = 'tampilkan semua';
        }


        // return [$datas,$txt];
        if (count($datas) == 0) {
            return Datatables::of($datas)->make(true);
        } else {
            if ($request->status == 'empty') {
                return Datatables::of($datas)->addIndexColumn()
                    ->addColumn('berkas1', function ($data) use($request) {
                        if ($data != null) {
                            if (count($data->berkas) != 0) {
                                foreach($data->berkas as $berkas){
                                    if($berkas->penggunaan_bh != null && $berkas->status == 'empty' && $berkas->periode == $request->periode){
                                        return '<a href="' . url('storage/uploads/files/' . $berkas->penggunaan_bh) . '" class="text-dark text-decoration-none" data-bs-toggle="modal" data-bs-target="#adminModal" onclick="showModalSrc(this.href)">Lihat</a>';
                                    }else{
                                        return 'Berkas Belum Diisi.';
                                    }
                                }
                            }else{
                                return 'Berkas Belum Diisi.';
                            }
                        }
                    })
                    ->addColumn('berkas2', function ($data) use($request) {
                        if ($data != null) {
                            if (count($data->berkas) != 0 ) {
                                foreach($data->berkas as $berkas){
                                    if($berkas->bukti_pencairan_bh != null && $berkas->status == 'empty' && $berkas->periode == $request->periode){
                                        return '<a href="' . url('storage/uploads/files/' . $berkas->bukti_pencairan_bh) . '" class="text-dark text-decoration-none" data-bs-toggle="modal" data-bs-target="#adminModal" onclick="showModalSrc(this.href)">Lihat</a>';
                                    }else{
                                        return 'Berkas Belum Diisi.';
                                    }
                                }
                            }else{
                                return 'Berkas Belum Diisi.';
                            } 
                        }
                    })
                    ->addColumn('berkas3', function ($data) use($request) {
                        if ($data != null) {
                            if (count($data->berkas) != 0) {
                                foreach($data->berkas as $berkas){
                                    if($berkas->khs != null && $berkas->status == 'empty' && $berkas->periode == $request->periode){
                                        return '<a href="' . url('storage/uploads/files/' . $berkas->khs) . '" class="text-dark text-decoration-none" data-bs-toggle="modal" data-bs-target="#adminModal" onclick="showModalSrc(this.href)">Lihat</a>';
                                    }else{
                                        return 'Berkas Belum Diisi.';
                                    }
                                }
                            }else{
                                return 'Berkas Belum Diisi.';
                            }
                        }
                    })
                    ->addColumn('berkas4', function ($data) use($request) {
                        if ($data != null) {
                            if (count($data->berkas) != 0) {
                                foreach($data->berkas as $berkas){
                                    if($berkas->prestasi != null && $berkas->status == 'empty' && $berkas->periode == $request->periode){
                                        return '<a href="' . url('storage/uploads/files/' . $berkas->prestasi) . '" class="text-dark text-decoration-none" data-bs-toggle="modal" data-bs-target="#adminModal" onclick="showModalSrc(this.href)">Lihat</a>';
                                    }else{
                                        return 'Berkas Belum Diisi.';
                                    }
                                }
                            }else{
                                return 'Berkas Belum Diisi.';
                            }
                            
                        }
                    })
                    ->addColumn('aksi', function ($data) {
                        if ($data != null) {
                            $html = '';

                            if($data->nomor != null){
                                $html .= '<a class="btn btn-success btn-sm text-decoration-none mb-2" alt="Whatsapp" href="https://wa.me/'.$data->nomor.'" target="_blank"><i class="fa-brands fa-whatsapp"></i> WhatsApp</a>';
                            }

                            $html .='
                            <a class="btn btn-warning btn-sm" onclick="return konfirmasi(' . $data->nim . ')" href="javascript:void(0)">Cek Validasi</a>';
                            return $html;
                        }
                    })
                    ->addColumn('status', function ($data) {
                        if ($data != null) {
                            if ($data->status == 'empty') {
                                $html = '<p class="badge bg-secondary">Belum Lengkap</p>';
                            } else if ($data->status == 'pending') {
                                $html = '<p class="badge bg-warning text-dark">Belum Validasi</p>';
                            } else if ($data->status == 'valid') {
                                $html = '<p class="badge bg-success">Valid</p>';
                            } else if ($data->status == 'unvalidated') {
                                $html = '<p class="badge bg-danger">Belum Valid</p>';
                            }
                            return $html;
                        }
                    })
                    ->addColumn('periode', function ($data) {
                        if ($data != null) {
                            if (count($data->berkas) != 0) {
                                return $data->berkas[0]->periode;
                            } else {
                                return $data->periode_masuk;
                            }
                        }
                    })
                    ->rawColumns(['aksi', 'status', 'berkas1', 'berkas2', 'berkas3', 'berkas4', 'periode'])->make(true);
            } else {
                return Datatables::of($datas)->addIndexColumn()
                    ->addColumn('berkas1', function ($data) {
                        if ($data != null && !empty($data->penggunaan_bh)) {
                            return '<a href="' . url('storage/uploads/files/' . $data->penggunaan_bh) . '" class="text-dark text-decoration-none" data-bs-toggle="modal" data-bs-target="#adminModal" onclick="showModalSrc(this.href)">Lihat</a>';
                        } else {
                            return 'Berkas Belum Diisi.';
                        }
                    })
                    ->addColumn('berkas2', function ($data) {

                        if ($data != null && $data->bukti_pencairan_bh != null) {
                            return '<a href="' . url('storage/uploads/files/' . $data->bukti_pencairan_bh) . '" class="text-dark text-decoration-none" data-bs-toggle="modal" data-bs-target="#adminModal" onclick="showModalSrc(this.href)">Lihat</a>';
                        } else {
                            return 'Berkas Belum Diisi.';
                        }
                    })
                    ->addColumn('berkas3', function ($data) {
                        if ($data != null && $data->khs != null) {
                            return '<a href="' . url('storage/uploads/files/' . $data->khs) . '" class="text-dark text-decoration-none" data-bs-toggle="modal" data-bs-target="#adminModal" onclick="showModalSrc(this.href)">Lihat</a>';
                        } else {
                            return 'Berkas Belum Diisi.';
                        }
                    })
                    ->addColumn('berkas4', function ($data) {
                        if ($data != null && $data->prestasi != null) {
                            return '<a href="' . url('storage/uploads/files/' . $data->prestasi) . '" class="text-dark text-decoration-none" data-bs-toggle="modal" data-bs-target="#adminModal" onclick="showModalSrc(this.href)">Lihat</a>';
                        } else {
                            return 'Berkas Belum Diisi.';
                        }
                    })
                    ->addColumn('aksi', function ($data) {
                        if ($data != null) {
                            $html = '';
                            
                            if($data->biodata->nomor != null){
                                $html .= '<a class="btn btn-success btn-sm text-decoration-none mb-2" alt="Whatsapp" href="https://wa.me/'.$data->biodata->nomor.'" target="_blank"><i class="fa-brands fa-whatsapp"></i> WhatsApp</a>';
                            }

                            $html .= '<div class="d-flex"><a class="btn btn-warning btn-sm me-2" onclick="return konfirmasi(' . $data->nim . ')" href="javascript:void(0)"> Cek Validasi</a> </div>';
                            return $html;
                        }
                    })
                    ->addColumn('status', function ($data) {
                        if ($data != null) {
                            if ($data["status"] == 'empty') {
                                $html = '<p class="badge bg-secondary">Belum Lengkap</p>';
                            } else if ($data["status"] == 'pending') {
                                $html = '<p class="badge bg-warning text-dark">Belum Validasi</p>';
                            } else if ($data["status"] == 'valid') {
                                $html = '<p class="badge bg-success">Valid</p>';
                            } else if ($data["status"] == 'unvalidated') {
                                $html = '<p class="badge bg-danger">Belum Valid</p>';
                            }
                            return $html;
                        }
                    })
                    ->addColumn('nama', function ($data) {
                        if ($data != null) {
                            return $data->biodata->nama;
                        }
                    })
                    ->addColumn('prodi', function ($data) {
                        if ($data != null) {
                            return $data->biodata->prodi;
                        }
                    })
                    ->rawColumns(['aksi', 'status', 'berkas1', 'berkas2', 'berkas3', 'berkas4', 'nama', 'prodi'])->make(true);
            }
        }
    }

    public function changeStatus(Request $request)
    {
        $periodeAktif = (PeriodeAktif::first() ?? self::get_periode()->dataPeriode[0]);
        $berkas = Berkas::where('nim', $request->nim)->where('periode', $periodeAktif->periode)->whereIn('status', ['pending', 'unvalidated','valid'])->first();
        if ($berkas == null) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Mahasiswa belum mengkonfirmasi berkas!'
            ]);
        }

        if ($request->valid) {
            $berkas->status = 'valid';
            $berkas->biodata()->update(['status' => 'valid']);
            $response = [
                'status' => 'success',
                'message' => 'Berkas sudah divalidasi.'
            ];
            $responseFalse = [
                'status' => 'failed',
                'message' => 'Berkas gagal divalidasi.'
            ];
        } else {
            $berkas->status = 'unvalidated';
            $berkas->biodata()->update(['status' => 'unvalidated']);
            $response = [
                'status' => 'success',
                'message' => 'Status telah diganti.'
            ];
            $responseFalse = [
                'status' => 'failed',
                'message' => 'Status gagal diganti.'
            ];
        }

        $berkas->save();

        return $berkas ? response()->json($response) : response()->json($responseFalse);
    }


    public function gantiPeriode(Request $request)
    {
        $allperiode = self::get_periode();
        $request_periode = $request->tahun . ($request->semester == 'ganjil' ? 1 : 2);
        $checkPeriode = PeriodeAktif::first();
        $arr = [];
        foreach ($allperiode->dataPeriode as $periode) {
            if ($periode->periode == $request_periode) {
                $arr[] = $periode->periode;
            }
        }

        if($checkPeriode != null){
            $bio = Biodata::where('status', 'empty')->with(['berkas' => function($query) use($checkPeriode){
                $query->where('periode', $checkPeriode->periode);
            }])->get();
    
            foreach($bio as $data){
                $berkas_validasi = Berkas::where('nim', $data->nim)->where('periode',$checkPeriode->periode)->get();
                if(count($berkas_validasi) == 0){
                    $input = [
                        'nim' => $data->nim, 
                        'periode' => $checkPeriode->periode,
                        'penggunaan_bh' => null,
                        'bukti_pencairan_bh' => null,
                        'khs' => null,
                        'prestasi' => null,
                        'status' => 'empty',
                        'created_at' => now(),
                        'updated_at' => now()
                    ];
                    Berkas::insert([$input]);
                };
            }
        }

        if (count($arr) > 0) {
            if ($checkPeriode != null) {
                $periode = $checkPeriode;
            } else {
                $periode = new PeriodeAktif;
            }
            $periode->periode = $request_periode;
            $periode->save();

            $biodata = Biodata::where('status','!=','empty');
            if($biodata){
                $biodata->update(['status' => 'empty']);
                return redirect()->back();
            }

            return redirect()->back();

        } else {
            return redirect()->back()->with('status', 'Periode ' . $request_periode . ' tidak ada!');
        }
    }


    public static function get_periode()
    {
        $token = LoginController::get_token()->msgToken;

        $response = Http::asForm()->post('http://langit.unusa.ac.id/api/get_periode', [
            'token' =>  $token
        ]);

        return $response->object();
    }
}
