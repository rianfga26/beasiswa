<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <!-- font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <title>Bukti Pelaporan Beasiswa UNUSA | {{$data->nama}} </title>
</head>
<style>
    *{
        font-family: 'Open Sans', sans-serif;
    }

    .table-laporan td {
      border-top: 1px solid #e3e3e3;
      padding: 10px;
    }
</style>
<body class="m-0 p-0">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-8">
                <h4 class="text-center fw-bold"><center>BUKTI PENERIMAAN BERKAS <br> VERIFIKASI LAPORAN BEASISWA</center></h4>
                <div class="row mt-4 justify-content-center">
                    <div class="col-10">
                        <table class="table table-borderless mx-auto w-auto table-sm align-middle">
                            <tr>
                                <td>Nama Lengkap </td>
                                <td>: {{ $data->nama }}</td>
                            </tr>
                            <tr>
                                <td>NIM</td>
                                <td>: {{ $data->nim }}</td>
                            </tr>
                            <tr>
                                <td>Program Studi</td>
                                <td>: {{ $data->prodi }}</td>
                            </tr>
                            <tr>
                                <td>Fakultas</td>
                                <td>: {{ $data->fakultas }}</td>
                            </tr>
                            <tr>
                                <td>Periode Masuk</td>
                                <td>: {{ $data->periode_masuk }}</td>
                            </tr>
                            <tr>
                                <td>Jalur Penerimaan</td>
                                <td>: {{ $data->jalur_penerimaan }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-sm table-laporan">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Kelengkapan</th>
                                <th>Hasil Pemeriksaan</th>
                                <th>Akses Berkas</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data->berkas as $berkas)
                            <tr>
                                <td>1</td>
                                <td align="center">Penggunaan Biaya Hidup</td>
                                <td align="center">{{ $berkas->status }}</td>
                                <td style="width: 50%;">{{ url('storage/uploads/files/'.$berkas->penggunaan_bh) }}</td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td align="center">Bukti Pencairan Biaya Hidup</td>
                                <td align="center">{{ $berkas->status }}</td>
                                <td style="width: 50%;">{{ url('storage/uploads/files/'.$berkas->bukti_pencairan_bh) }}</td>
                            </tr>
                            <tr>
                                <td>3</td>
                                <td align="center">KHS</td>
                                <td align="center">{{ $berkas->status }}</td>
                                <td style="width: 50%;">{{ url('storage/uploads/files/'.$berkas->khs) }}</td>
                            </tr>
                            <tr>
                                <td>4</td>
                                <td align="center">Prestasi (Opsional)</td>
                                <td align="center">{{ $berkas->status }}</td>
                                <td style="width: 50%;">{{ $berkas->prestasi == null ? 'Berkas Belum Diisi.' : url('storage/uploads/files/'.$berkas->prestasi) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Optional JavaScript; choose one of the two! -->

    <!-- Option 1: Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

    <!-- Option 2: Separate Popper and Bootstrap JS -->
    <!--
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>
    -->
</body>

</html>