<?php

namespace App\Http\Controllers;

use App\Models\Berkas;
use App\Models\Biodata;
use App\Models\PeriodeAktif;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    public function post(Request $request)
    {
        $request->validate(
            [
                'files' => 'required',
                'files.*' => 'required|mimes:jpeg,jpg,png,pdf|max:1024'
            ],
            [
                'files.required' => 'setiap kolom wajib diisi.',
                'files.*.mimes' => 'kolom harus bertype (jpg,png,jpeg,pdf).',
                'files.*.max' => 'file harus memiliki besar maximal 1 MB.'
            ]
        );
        
        $periodeAktif = PeriodeAktif::first();
        
        // check periode aktif
        if($periodeAktif == null){
            return redirect()->back()->with('status', 'Periode Belum Diaktifkan!');
        }
        
        $data = Berkas::where('nim', session('nim'))->where('periode', $periodeAktif->periode)->first();
        $biodata = Biodata::where('nim', session('nim'))->first();
        $files = [];

        if ($request->hasfile('files')) {

            foreach ($request->file('files') as $key => $file) {
                $path = 'uploads/files';
                $name = time() . rand(1, 100) . '.' . $file->extension();
                $file->storeAs($path, $name);
                $files[$key] = $name;
            }

            if (count($request->request) > 1) {
                if ($request->penggunaan_bh != null) {
                    $files[0] = $request->penggunaan_bh;
                }
                if ($request->bukti_pencairan_bh != null) {
                    $files[1] = $request->bukti_pencairan_bh;
                }
                if ($request->khs != null) {
                    $files[2] = $request->khs;
                }
                if ($request->prestasi != null) {
                    $files[3] = $request->prestasi;
                }
            }
        }

        // dd($data);
        if ($data) {
            $file = $data;
        } else {
            $file = new Berkas;
            $file->status = 'empty';
            $biodata->update(['status' => 'empty']);
        }

        $file->nim = session('nim');
        $file->periode = $periodeAktif->periode;
        $file->penggunaan_bh = array_key_exists(0, $files) ? $files[0] : null;
        $file->bukti_pencairan_bh = array_key_exists(1, $files) ? $files[1] : null;
        $file->khs = array_key_exists(2, $files) ? $files[2] : null;
        $file->prestasi = array_key_exists(3, $files) ? $files[3] : null;

        $file->save();
        return redirect()->back();
    }

    public function destroy($name, $nim)
    {
        $periodeAktif = PeriodeAktif::first();
        $datas = Berkas::where('nim', $nim)->where('periode', $periodeAktif->periode)->first();
        Storage::delete('uploads/files/' . $datas->$name);
        Berkas::where('nim', $nim)->where('periode', $periodeAktif->periode)->update([$name => null]);

        return redirect()->back();
    }

    public function validasi()
    {
        $periodeAktif = PeriodeAktif::first();
        $data = Berkas::where('nim', session('nim'))->where('periode', $periodeAktif->periode)->first();
        $data->status = 'pending';
        $data->biodata()->update(['status' => 'pending']);
        $data->save();
        return redirect()->back();
    }

    public function postNomor(Request $request){
        $angkaPertama = substr($request->nomor, 0,1);
        if($angkaPertama == 0){
            $nomor = "62".substr($request->nomor,1);
        }else{
            $nomor = $request->nomor;
        }
        $nim = session('nim');
        $bio = Biodata::where('nim', $nim)->first();
        $bio->nomor = (int) $nomor;
        $bio->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Nomor Telah Ditambahkan!'
        ]);
    }

}
