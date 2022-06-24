<?php

namespace App\Http\Controllers;

use App\Models\Biodata;
use Illuminate\Support\Facades\Http;

use Illuminate\Http\Request;

class LoginController extends Controller
{
    public static function get_token()
    {
        $response = Http::get('http://langit.unusa.ac.id/api/get_token');
        return $response->object();
    }

    public static function get_biodata($username, $password)
    {
        $token = self::get_token()->msgToken;

        $response = Http::asForm()->post('http://langit.unusa.ac.id/api/do_login', [
            'token' =>  $token,
            'username' => $username,
            'password' => $password
        ]);

        return $response->object();
    }

    public function cek_login($user, $req)
    {   
        if ($user == 'admin') {
            $req->session()->put('role', $user);
            
            return redirect()->route('admin');
        } else if ($user->success) {
            $req->session()->put('nim', $user->dataBiodata[0]->username);

            return redirect()->route('mhs');
        }
    }

    public function do_login(Request $request)
    {
        $jalurActive = ['Beasiswa'];
        $login = $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);

        if ($login["username"] == 'admin' && $login["password"] == 'adminkipk@unusa') {
            return $this->cek_login('admin', $request);
        } else {
            $mhs = self::get_biodata($login["username"], $login["password"]);
            $jalur = $mhs->dataBiodata[0]->jalurpenerimaan;
            if(!in_array($jalur, $jalurActive)){
                return redirect()->back()->with('status', 'Jalur Penerimaan Bukan Beasiswa');
            }

            if ($mhs->success == false) {
                return redirect()->back()->with('status', $mhs->msgServer);
            }

            $bio = Biodata::where('nim', '=', $mhs->dataBiodata[0]->username)->first();

            if ($bio === null) {
                $biodata = new Biodata;
                $biodata->nim = $mhs->dataBiodata[0]->username;
                $biodata->nama = $mhs->dataBiodata[0]->nama;
                $biodata->prodi = str_replace('Prodi ', '', $mhs->dataBiodata[0]->prodi);
                $biodata->fakultas = $mhs->dataBiodata[0]->fakultas;
                $biodata->periode_masuk = $mhs->dataBiodata[0]->periodemasuk;
                $biodata->link_photo = $mhs->linkFoto;
                $biodata->jalur_penerimaan = $mhs->dataBiodata[0]->jalurpenerimaan;
                $biodata->status = 'empty';

                if ($biodata->save()) {
                    return $this->cek_login($mhs, $request);
                }
            } else {
                return $this->cek_login($mhs, $request);
            }

        }
    }

    public function logout()
    {
        session()->flush();
        return redirect('/');
    }
}
