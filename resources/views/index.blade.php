@extends('main')

@section('content')


@if(session()->has('status'))
<script>
    Swal.fire("{{ session('status') }}", '', 'error')
</script>
@endif
@if($bio->nomor == null)
<script>
    Swal.fire({
        title: 'Masukkan No.WhatsApp Anda!',
        input: 'number',
        inputLabel: 'Contoh: 0897xxxx',
        inputValue: '08xx',
        allowOutsideClick: false,
        showCancelButton: false,
        confirmButtonText: 'Submit',
        showLoaderOnConfirm: true,
        preConfirm: (nomor) => {
            if (!nomor) {
                Swal.showValidationMessage('inputan harus diisi')
            } else if (nomor.length <= 10 || nomor.length >= 14) {
                Swal.showValidationMessage('nomor tidak valid!')
            } else {
                return fetch("{{ Route('post_nomor') }}", {
                        method: "POST",
                        headers: headers,
                        body: JSON.stringify({
                            nomor
                        })
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(response.message)
                        }
                        return location.reload()
                    })
                    .catch(error => {
                        Swal.showValidationMessage(
                            `Request failed: ${error}`
                        )
                    })
            }
        },
        allowOutsideClick: () => !Swal.isLoading()
    })
</script>

@endif
<div class="d-flex justify-content-center align-items-center mb-5">
    <div class="col-md-8 mt-4">
        @if($periodeAktif != null)
        <span class="p-0 m-0 fw-bold" style="color: #428bca;">
            Periode Aktif : {{ $periodeAktif->periode }}
        </span>
        @endif
        <div class="card text-center">
            <div class="card-header fw-bold">
                PELAPORAN PENGGUNAAN BIAYA HIDUP, BUKTI PENCAIRAN BIAYA HIDUP, KHS/IPK, PRESTASI TINGKAT WILAYAH NASIONAL DAN INTERNASIONAL
            </div>
            <div class="card-body text-start">
                <div class="d-flex flex-column justify-content-center align-items-center">
                    <div class="col-md-12">
                        <table class="w-100 table table-bordered">
                            <tr class="bg-dark">
                                <th colspan="4" class="fs-4 text-center text-white">Biodata</th>
                            </tr>
                            <tr>
                                <th class="w-50">Nama</th>
                                <td>:</td>
                                <td class="w-100">{{ $bio->nama }}</td>
                            </tr>
                            <tr>
                                <th>NIM</th>
                                <td>:</td>
                                <td>{{ $bio->nim }}</td>
                            </tr>
                            <tr>
                                <th>Prodi</th>
                                <td>:</td>
                                <td>{{ $bio->prodi }}</td>
                            </tr>
                            <tr>
                                <th>Fakultas</th>
                                <td>:</td>
                                <td>{{ $bio->fakultas }}</td>
                            </tr>
                            <tr>
                                <th>Periode Masuk</th>
                                <td>:</td>
                                <td>{{ $bio->periode_masuk }}</td>
                            </tr>
                            <tr>
                                <th>Jalur Penerima</th>
                                <td>:</td>
                                <td>{{ $bio->jalur_penerimaan }}</td>
                            </tr>
                            <tr>
                                <th>Nomor WhatsApp</th>
                                <td>:</td>
                                <td>
                                    {{ $bio->nomor ? '0'.substr($bio->nomor, 2) : 'kosong' }}
                                    @if($bio->nomor == null)
                                    <a href="" class="badge bg-warning text-dark ms-2 text-decoration-none">Isi?</a>
                                    @else
                                    <?php
                                    $nomor = $bio->nomor ? '0' . substr($bio->nomor, 2) : '';
                                    ?>
                                    <a href="#" class="badge bg-warning text-dark pe-auto ms-2 text-decoration-none" onclick="gantiNomor('{{$nomor}}')">Ganti?</a>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-12 mt-5">
                        <div class="d-flex justify-content-between">
                            @if(count($bio->berkas) > 0)
                            @foreach($bio->berkas as $berkas)
                            @if($berkas->pesan != null)
                            <p onclick="alertMessage(`{{ $berkas->pesan }}`)" class="badge bg-dark animate__heartBeat" style="cursor: pointer;">
                                <i class="fa-regular fa-envelope pe-auto">
                                </i> Pesan
                            </p>
                            @endif
                            @endforeach
                            <?php
                            if ($bio->status == 'empty') {
                                if ($bio->berkas[0]->penggunaan_bh != null && $bio->berkas[0]->bukti_pencairan_bh != null && $bio->berkas[0]->khs != null) {
                                    $html = '<p>Status : <span class="badge bg-secondary">Lengkap</span></p>';
                                } else {
                                    $html = '<p>Status : <span class="badge bg-secondary">Belum Lengkap</span></p>';
                                }
                            } else if ($bio->status == 'pending') {
                                $html = '<p>Status : <span class="badge bg-warning text-dark ">Menunggu Validasi</span></p>';
                            } else if ($bio->status == 'valid') {
                                $html = '<p>Status : <span class="badge bg-success">Valid</span></p>';
                            } else if ($bio->status == 'unvalidated') {
                                $html = '<p>Status : <span class="badge bg-danger">Belum Valid</span>';
                            }
                            echo $html;
                            ?>

                            @endif
                        </div>
                        @if (count($errors) > 0)
                        <div class="alert alert-danger">
                            <strong>Maaf!</strong> Mungkin ada beberapa kesalahan dari inputan anda.<br><br>
                            <ul>
                                @foreach ($errors->all() as $value)
                                <li>{{ $value }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif

                        <form action="{{ Route('post') }}" enctype="multipart/form-data" method="post" id="input-form">
                            @csrf
                            @if(count($bio->berkas) > 0)
                            <table class="w-100 table table-bordered">
                                <tr>
                                    <th class="text-capitalize">upload penggunaan biaya hidup <span class="text-danger">*</span></th>
                                    <td>:</td>
                                    <td class="w-50">
                                        @if($bio->berkas[0]->penggunaan_bh != null)
                                        <input type="hidden" name="penggunaan_bh" value="{{ $bio->berkas[0]->penggunaan_bh }}">
                                        <a href="{{url('storage/uploads/files',$bio->berkas[0]->penggunaan_bh)}}" target="_blank">
                                            {{ Str::limit($bio->berkas[0]->penggunaan_bh, 30) }}
                                        </a>
                                        @if($bio->berkas[0]->status == 'empty' || $bio->berkas[0]->status == 'unvalidated')
                                        <a href="{{ Route('delete',['penggunaan_bh', $bio->berkas[0]->nim]) }}">
                                            <span class="text-danger float-end"><i class="fa-regular fa-trash-can"></i></span>
                                        </a>
                                        @endif
                                        @else
                                        <input class="form-control @error('files.0') is-invalid @enderror" type="file" id="input-file" name="files[0]"></input>
                                        @error('files.0')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                        @enderror
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th class="text-capitalize">upload bukti penerimaan pencairan biaya hidup <span class="text-danger">*</span></th>
                                    <td>:</td>
                                    <td class="w-50">
                                        @if($bio->berkas[0]->bukti_pencairan_bh != null)
                                        <input type="hidden" name="bukti_pencairan_bh" value="{{ $bio->berkas[0]->bukti_pencairan_bh }}">
                                        <a href="{{url('storage/uploads/files',$bio->berkas[0]->bukti_pencairan_bh)}}" target="_blank">{{ Str::limit($bio->berkas[0]->bukti_pencairan_bh, 30) }}</a>
                                        @if($bio->berkas[0]->status == 'empty' || $bio->berkas[0]->status == 'unvalidated')
                                        <a href="{{ Route('delete',['bukti_pencairan_bh', $bio->berkas[0]->nim]) }}">
                                            <span class="text-danger float-end"><i class="fa-regular fa-trash-can"></i></span>
                                        </a>
                                        @endif
                                        @else
                                        <input class="form-control @error('files.1') is-invalid @enderror" type="file" id="input-file" name="files[1]"></input>
                                        @error('files.1')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                        @enderror
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th class="text-capitalize">upload KHS atau IPK <span class="text-danger">*</span></th>
                                    <td>:</td>
                                    <td class="w-50">
                                        @if($bio->berkas[0]->khs != null)
                                        <input type="hidden" name="khs" value="{{ $bio->berkas[0]->khs }}">
                                        <a href="{{url('storage/uploads/files',$bio->berkas[0]->khs)}}" target="_blank">{{ Str::limit($bio->berkas[0]->khs, 30) }}</a>
                                        @if($bio->berkas[0]->status == 'empty' || $bio->berkas[0]->status == 'unvalidated')
                                        <a href="{{ Route('delete',['khs', $bio->berkas[0]->nim]) }}">
                                            <span class="text-danger float-end"><i class="fa-regular fa-trash-can"></i></span>
                                        </a>
                                        @endif
                                        @else
                                        <input class="form-control @error('files.2') is-invalid @enderror" type="file" id="input-file" name="files[2]"></input>
                                        @error('files.2')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                        @enderror
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th class="text-capitalize">upload prestasi tingkat nasional dan internasional</th>
                                    <td>:</td>
                                    <td class="w-50">
                                        @if($bio->berkas[0]->prestasi != null)
                                        <input type="hidden" name="prestasi" value="{{ $bio->berkas[0]->prestasi }}">
                                        <a href="{{url('storage/uploads/files',$bio->berkas[0]->prestasi)}}" target="_blank">{{ Str::limit($bio->berkas[0]->prestasi, 30) }}</a>
                                        @if($bio->berkas[0]->status == 'empty' || $bio->berkas[0]->status == 'unvalidated')
                                        <a href="{{ Route('delete', ['prestasi', $bio->berkas[0]->nim]) }}">
                                            <span class="text-danger float-end"><i class="fa-regular fa-trash-can"></i></span>
                                        </a>
                                        @endif
                                        @else
                                        <input class="form-control @error('files.3') is-invalid @enderror" type="file" id="input-file" name="files[3]"></input>
                                        @error('files.3')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                        @enderror
                                        @endif
                                    </td>
                                </tr>
                            </table>
                            <div class="text-center">
                                <!-- button 1 -->
                                <a class="mr-3 mt-2 btn btn-danger btn-sm" onclick="logout()" href="javascript:void(0)"><i class="fa-solid fa-right-from-bracket"></i> Keluar</a>
                                @if($bio->berkas[0]->penggunaan_bh == null || $bio->berkas[0]->bukti_pencairan_bh == null || $bio->berkas[0]->khs == null)
                                <!-- button 2 -->
                                <button onclick="document.getElementById('input-form').reset(); document.getElementById('input-file').value = null; return false;" class="mr-3 mt-2 btn btn-secondary btn-sm"><i class="fa-solid fa-rotate-left"></i> Reset</button>
                                <!-- button 3 -->
                                <button type="submit" class="btn btn-warning btn-sm mt-2 text-white"><i class="fa-solid fa-floppy-disk"></i> Simpan</button>
                                @else
                                @if($bio->berkas[0]->status == 'empty')
                                <a href="javascript:void(0)" onclick="return SubmitSekarang()" class="btn btn-outline-warning btn-sm mt-2">Submit Sekarang!</a>
                                @endif
                                @endif
                                <!-- button valid -->
                                @if($bio->status == 'valid')
                                <a href="{{ Route('pdf.generate', $bio->nim) }}" class="mr-3 mt-2 btn btn-outline-success btn-sm"><i class="fa-solid fa-download"></i> Cetak Bukti Pelaporan</a>
                                @endif
                            </div>
                            @else
                            <table class="w-100 table table-bordered">
                                <tr>
                                    <th class="text-capitalize">upload penggunaan biaya hidup <span class="text-danger">*</span></th>
                                    <td>:</td>
                                    <td class="w-50">
                                        <input class="form-control @error('files.0') is-invalid @enderror" type="file" id="input-file" name="files[0]"></input>
                                        @error('files.0')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                        @enderror
                                    </td>
                                </tr>
                                <tr>
                                    <th class="text-capitalize">upload bukti penerimaan pencairan biaya hidup <span class="text-danger">*</span></th>
                                    <td>:</td>
                                    <td class="w-50">
                                        <input class="form-control @error('files.1') is-invalid @enderror" type="file" id="input-file" name="files[1]"></input>
                                        @error('files.1')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                        @enderror
                                    </td>
                                </tr>
                                <tr>
                                    <th class="text-capitalize">upload KHS atau IPK <span class="text-danger">*</span></th>
                                    <td>:</td>
                                    <td class="w-50">
                                        <input class="form-control @error('files.2') is-invalid @enderror" type="file" id="input-file" name="files[2]"></input>
                                        @error('files.2')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                        @enderror
                                    </td>
                                </tr>
                                <tr>
                                    <th class="text-capitalize">upload prestasi tingkat nasional dan internasional</th>
                                    <td>:</td>
                                    <td class="w-50">
                                        <input class="form-control @error('files.3') is-invalid @enderror" type="file" id="input-file" name="files[3]"></input>
                                        @error('files.3')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                        @enderror
                                    </td>
                                </tr>
                            </table>
                            <div class="text-center">
                                <!-- button 1 -->
                                <a class="mr-3 mt-2 btn btn-danger btn-sm" onclick="logout()" href="javascript:void(0)"><i class="fa-solid fa-right-from-bracket"></i> Keluar</a>
                                <!-- button 2 -->
                                <button onclick="document.getElementById('input-form').reset(); document.getElementById('input-file').value = null; return false;" class="mr-3 mt-2 btn btn-secondary btn-sm"><i class="fa-solid fa-rotate-left"></i> Reset</button>
                                <!-- button 3 -->
                                <button type="submit" class="btn btn-warning btn-sm mt-2 text-white"><i class="fa-solid fa-floppy-disk"></i> Simpan</button>
                            </div>
                            @endif
                        </form>
                    </div>
                </div>
            </div>
            <div class="card-footer text-muted">
                Powered by <a href="https://www.unusa.ac.id" class="text-decoration-none" target="_blank">unusa.ac.id</a>
            </div>
        </div>
    </div>
</div>
@endsection
@section('js')
<script>
    function SubmitSekarang() {
        return Swal.fire({
            title: 'Submit Sekarang!',
            text: 'Apakah Anda Ingin Memvalidasi Berkas Ke Admin?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya!',
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#dc3545',
            cancelButtonText: 'Tidak!',
        }).then((result) => {
            if (result.isConfirmed) {
                fetch("{{ Route('validasi') }}")
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(response.message)
                        }
                        return location.reload()
                    })
                    .catch(error => {
                        Swal.showValidationMessage(
                            `Request failed: ${error}`
                        )
                    })
            }
        })
    }

    function gantiNomor(nomor) {
        Swal.fire({
            title: 'Masukkan No.WhatsApp Anda!',
            input: 'number',
            inputLabel: 'cth: 0897xxxx',
            inputValue: nomor,
            allowOutsideClick: false,
            showCancelButton: false,
            confirmButtonText: 'Submit',
            showLoaderOnConfirm: true,
            preConfirm: (nomor) => {
                if (!nomor) {
                    Swal.showValidationMessage('inputan harus diisi')
                } else if (nomor.length <= 10 || nomor.length >= 14) {
                    Swal.showValidationMessage('nomor tidak valid!')
                } else {
                    return fetch("{{ Route('post_nomor') }}", {
                            method: "POST",
                            headers: headers,
                            body: JSON.stringify({
                                nomor
                            })
                        })
                        .then(response => {
                            if (!response.ok) {
                                throw new Error(response.message)
                            }
                            return location.reload()
                        })
                        .catch(error => {
                            Swal.showValidationMessage(
                                `Request failed: ${error}`
                            )
                        })
                }
            },
            allowOutsideClick: () => !Swal.isLoading()
        })
    }
</script>
@endsection