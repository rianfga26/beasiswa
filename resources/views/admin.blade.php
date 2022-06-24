@extends('main')
@section('judul', 'Table Mahasiswa')

@section('content')


<nav class="navbar navbar-light bg-white my-4">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">Administrator</a>
        <a class="btn btn-outline-danger btn-sm float-end" onclick="logout()" href="javascript:void(0)"><i class="fa-solid fa-right-from-bracket"></i> Keluar</a>
    </div>
</nav>

@if(session()->has('status'))
<script>
    Swal.fire("{{ session('status') }}", '', 'error')
</script>
@endif
<div class="row justify-content-center align-items-center text-center">
    @if(count($berkas) != 0)
    <div class="col-lg-2 mb-3">
        <div class="input-group input-group-sm">
            <span class="input-group-text">Periode</span>
            <select class="form-select" aria-label="Default select example" onChange="fetchByOption()" name="periode">
                <option value>------ ------</option>
                @foreach($allperiode->dataPeriode as $periode)
                <?php
                $arr_word = substr($periode->periode, -1);
                $arr2_word = substr($periode->periode, 0, 4);
                ?>
                @if($arr_word != 3 && $arr2_word > 2019)
                <option value="{{ $periode->periode }}">Semester {{$periode->nama_periode}}</option>
                @endif
                @endforeach
            </select>
        </div>
    </div>
    @endif
    <div class="col-lg-3 mb-3">
        <div class="input-group input-group-sm">
            <span class="input-group-text">Program Studi</span>
            <select class="form-select" aria-label="Default select example" onChange="fetchByOption()" name="prodi">
                <option value>------ ------</option>
                <option value="S1 Pendidikan Dokter">S1 Pendidikan Dokter</option>
                <option value="S1 Kesehatan Masyarakat">S1 Kesehatan Masyarakat</option>
                <option value="S1 Gizi">S1 Gizi</option>
                <option value="S1 Keperawatan">S1 Keperawatan</option>
                <option value="S1 Kebidanan">S1 Kebidanan</option>
                <option value="S1 Pendidikan Guru Sekolah Dasar">S1 Pendidikan Guru Sekolah Dasar</option>
                <option value="S1 Pendidikan Guru PAUD">S1 Pendidikan Guru PAUD</option>
                <option value="S1 Pendidikan Bahasa Inggris">S1 Pendidikan Bahasa Inggris</option>
                <option value="S1 Manajemen">S1 Manajemen</option>
                <option value="S1 Akuntansi">S1 Akuntansi</option>
                <option value="S1 Sistem Informasi">S1 Sistem Informasi</option>
            </select>
        </div>
    </div>
    <div class="col-lg-2 mb-3">
        <div class="input-group input-group-sm">
            <span class="input-group-text">Status</span>
            <select class="form-select" aria-label="Default select example" onChange="fetchByOption()" name="status">
                <option value>------ ------</option>
                <option value="pending">belum validasi</option>
                <option value="valid">valid</option>
                <option value="unvalidated">belum valid</option>
                <option value="empty">belum pelaporan</option>
            </select>
        </div>
    </div>
</div>
<div class="d-flex justify-content-center">
    @if($periodeSet != null)
    <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#periodeModal" class="ms-2 text-decoration-none btn btn-outline-warning fw-bold btn-sm">
        <i class="fa-solid fa-pen"></i> Ubah Periode</a>
    @else
    <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#periodeModal" class="ms-2 text-decoration-none btn btn-outline-primary fw-bold btn-sm"><i class="fa-solid fa-circle-plus"></i> Periode Baru</a>
    @endif
</div>
<div class="d-flex justify-content-start align-items-center mt-5">
    @if($periodeSet != null)
    @foreach($allperiode->dataPeriode as $periode)
    <span class="p-0 m-0 fw-bold" style="color: #428bca;">
        @if($periode->periode == $periodeSet->periode)
        Periode Aktif : Semester {{ $periode->nama_periode }}
        @endif
    </span>
    @endforeach
    @endif
</div>
<div class="d-flex justify-content-center align-items-center">
    <div class="card">
        <div class="card-body">
            <!-- table -->
            <div class="d-flex justify-content-center mb-4">
                <h4 class="w-50 text-break text-center fw-bold">Tabel Pelaporan Mahasiswa Beasiswa</h4>
            </div>
            <div class="table-responsive">
                <table class="table table-striped overflow-auto" id="adminTable">
                    <thead>
                        <tr>
                            <th>NO</th>
                            <th id="periode">PERIODE</th>
                            <th>NIM</th>
                            <th>NAMA</th>
                            <th>PRODI</th>
                            <th>PENGGUNAAN BIAYA HIDUP</th>
                            <th>BUKTI PENCAIRAN BIAYA HIDUP</th>
                            <th>KHS</th>
                            <th>PRESTASI</th>
                            <th>STATUS</th>
                            <th>AKSI</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>


    <!-- Modal Gambar -->
    <div class="modal fade" id="adminModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable modal-fullscreen-lg-down">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Image And PDF Viewer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body ">
                    <iframe name="modal" width="100%" height="300px"></iframe>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Periode -->
    <div class="modal fade" id="periodeModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Ganti Periode Aktif</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ Route('admin.ganti.periode') }}" method="post">
                    @csrf
                    <div class="modal-body">
                            <div class="d-flex">
                                Periode :
                                <!-- semester -->
                                <select class="form-select w-50 form-select-sm mx-2" aria-label=".form-select-sm example" name="semester">
                                    <option value="ganjil">Semester Ganjil</option>
                                    <option value="genap">Semester Genap</option>
                                </select>
                                <!-- tahun -->
                                <select class="form-select w-25 form-select-sm" aria-label=".form-select-sm example" name="tahun">
                                    @foreach($allperiode->dataPeriode as $periode)
                                    <?php
                                    $arr_word = substr($periode->periode, -1); // hilangkan antara
                                    $tahun = explode(" ", $periode->nama_periode)[1]; // tahun
                                    ?>
                                    @if($arr_word != 2 && $arr_word != 3 && $tahun >= 1996)
                                    <option value="{{ $tahun }}">{{ $tahun }}</option>
                                    @endif
                                    @endforeach
                                </select>
                            </div>
                            <br>
                            <p class="fs-6 text-lowercase">Note : Mengubah Periode juga merubah data seluruh mahasiswa jadi pastikan periode yang dipilih benar.</h4>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection

@section('js')
<script>
    var dataTable;

    function showModalSrc(href) {
        // console.log(href);
        var modal = $('iframe[name=modal]');
        modal.attr('src', href);
    }


    function konfirmasi(nim) {
        return Swal.fire({
            title: 'Validasi!',
            text: 'Apakah berkas sudah valid?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Valid!',
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#dc3545',
            cancelButtonText: 'Belum!',
        }).then((result) => {
            if (result.isConfirmed) {
                fetch("{{ Route('admin.change.status') }}", {
                        method: "POST",
                        headers: headers,
                        body: JSON.stringify({
                            valid: true,
                            nim: nim
                        })
                    })
                    .then(response => {
                        return response.json();
                    }).then(data => {
                        if (data.status == 'failed') {
                            Swal.fire(data.message, '', 'error')
                        } else {
                            Swal.fire(data.message, '', 'success')
                            dataTable.ajax.reload();
                        }
                    })
                    .catch(error => {
                        console.log(error)
                    });
            } else if (result.dismiss === Swal.DismissReason.cancel) {
                return fetch("{{ Route('admin.change.status') }}", {
                        method: "POST",
                        headers: headers,
                        body: JSON.stringify({
                            valid: false,
                            nim: nim
                        })
                    })
                    .then(response => {
                        return response.json();
                    }).then(data => {
                        if (data.status == 'failed') {
                            Swal.fire(data.message, '', 'error')
                        } else {
                            Swal.fire(data.message, '', 'success')
                            dataTable.ajax.reload();
                        }
                    })
                    .catch(error => {
                        console.log(error)
                    })
            }
        })

    }

    function fetchByOption() {
        dataTable.ajax.reload();
    }


    $(document).ready(function() {
        dataTable = $('#adminTable').DataTable({
            processing: false,
            serverSide: true,
            dom: 'Bfrtip',
            buttons: [{
                extend: 'excelHtml5',
                title: 'Data export',
                className: 'btn btn-outline-success btn-sm',
                text: '<i class="fa-solid fa-download"></i> Export Excel',
                exportOptions: {
                    columns: [1, 2, 3, 4, 5, 6, 7, 8, 9],
                    format: {
                        body: function(data, row, column, node) {
                            if (column >= 4 && column <= 7) {
                                let posisi = data.search('"');
                                let pisah = data.substr(posisi + 1);
                                let split = pisah.split('"');

                                return split[0];
                            } else if (column == 8) {
                                let posisi = data.search(">");
                                let pisah = data.substr(posisi + 1);
                                let split = pisah.split('<');
                                return split[0];
                            }
                            return data;
                        }
                    }
                },
            }],
            "ajax": {
                "url": "{{ Route('admin.options') }}",
                "type": "POST",
                "data": {
                    "_token": $('meta[name="csrf-token"]').attr('content'),
                    "periode": function() {
                        return $('select[name=periode] option').filter(':selected').val();
                    },
                    "prodi": function() {
                        return $('select[name=prodi] option').filter(':selected').val();
                    },
                    "status": function() {
                        return $('select[name=status] option').filter(':selected').val();
                    }
                }
            },
            "columns": [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: "periode",
                    name: "periode"
                },
                {
                    data: "nim",
                    name: "nim"
                },
                {
                    data: "nama",
                    name: "nama"
                },
                {
                    data: "prodi",
                    name: "prodi"
                },
                {
                    data: "berkas1",
                    name: "penggunaan biaya hidup",
                    orderable: false,
                    searchable: false
                },
                {
                    data: "berkas2",
                    name: "bukti pencairan biaya hidup",
                    orderable: false,
                    searchable: false
                },
                {
                    data: "berkas3",
                    name: "khs",
                    orderable: false,
                    searchable: false
                },
                {
                    data: "berkas4",
                    name: "prestasi",
                    orderable: false,
                    searchable: false
                },
                {
                    data: "status",
                    name: "status",
                    orderable: false,
                    searchable: false
                },
                {
                    data: "aksi",
                    name: "aksi",
                    orderable: false,
                    searchable: false
                }
            ],
            "columnDefs": [{
                "defaultContent": "",
                "targets": "_all",
            }]
        });
    });
</script>
@endsection