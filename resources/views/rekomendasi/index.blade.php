@extends('layouts.main')
@section('container')
<div class="accordion d-none" id="accordionExample">
    <div class="card">
        <div class="card-header" id="headingZero" data-toggle="collapse" data-target="#collapseZero" aria-expanded="true" aria-controls="collapseZero">
            <h2 class="mb-0 d-flex justify-content-between">
                <button class="btn btn-link" type="button">
                    <h2>RANKING</h2>
                </button>
                <small><i class="fas fa-angle-down"></i></small>
            </h2>
        </div>
        <div id="collapseZero" class="collapse show" aria-labelledby="headingZero" data-parent="#accordionExample">
            <div class="card-body">
                <div id=""></div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header" id="headingOne" data-toggle="collapse" data-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
            <h2 class="mb-0 d-flex justify-content-between">
                <button class="btn btn-link" type="button">
                    Matrix Normalisasi
                </button>
                <small><i class="fas fa-angle-down"></i></small>
            </h2>
        </div>
        <div id="collapseOne" class="collapse" aria-labelledby="headingOne" data-parent="#accordionExample">
            <div class="card-body">
                <div id="matrix-normalisasi"></div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header" id="headingTwo" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
            <h2 class="mb-0 d-flex justify-content-between">
                <button class="btn btn-link collapsed" type="button">
                    Bobot Normalisasi
                </button>
                <small><i class="fas fa-angle-down"></i></small>
            </h2>
        </div>
        <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionExample">
            <div class="card-body">
                <div id="bobot-normalisasi"></div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header" id="headingThree" data-toggle="collapse" data-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
            <h2 class="mb-0 d-flex justify-content-between">
                <button class="btn btn-link collapsed" type="button">
                    Concordance Index
                </button>
                <small><i class="fas fa-angle-down"></i></small>
            </h2>
        </div>
        <div id="collapseThree" class="collapse" aria-labelledby="headingThree" data-parent="#accordionExample">
            <div class="card-body">
                <div id="concordance-index"></div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header" id="headingFive" data-toggle="collapse" data-target="#collapsefive" aria-expanded="false" aria-controls="collapsefive">
            <h2 class="mb-0 d-flex justify-content-between">
                <button class="btn btn-link collapsed" type="button">
                    Matrix Concordance
                </button>
                <small><i class="fas fa-angle-down"></i></small>
            </h2>
        </div>
        <div id="collapsefive" class="collapse" aria-labelledby="headingFive" data-parent="#accordionExample">
            <div class="card-body">
                <div id="matrix-concordance"></div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header" id="headingSix" data-toggle="collapse" data-target="#collapseSix" aria-expanded="false" aria-controls="collapseSix">
            <h2 class="mb-0 d-flex justify-content-between">
                <button class="btn btn-link collapsed" type="button">
                    Hasil Matrix Concordance
                </button>
                <small><i class="fas fa-angle-down"></i></small>
            </h2>
        </div>
        <div id="collapseSix" class="collapse" aria-labelledby="headingSix" data-parent="#accordionExample">
            <div class="card-body">
                <div id="hasil-matrix-concordance"></div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header" id="headingEight" data-toggle="collapse" data-target="#collapseEight" aria-expanded="false" aria-controls="collapseEight">
            <h2 class="mb-0 d-flex justify-content-between">
                <button class="btn btn-link collapsed" type="button">
                    Concordance
                </button>
                <small><i class="fas fa-angle-down"></i></small>
            </h2>
        </div>
        <div id="collapseEight" class="collapse" aria-labelledby="headingEight" data-parent="#accordionExample">
            <div class="card-body">
                <div id="concordance"></div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header" id="headingFour" data-toggle="collapse" data-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
            <h2 class="mb-0 d-flex justify-content-between">
                <button class="btn btn-link collapsed" type="button">
                    Discordance Index
                </button>
                <small><i class="fas fa-angle-down"></i></small>
            </h2>
        </div>
        <div id="collapseFour" class="collapse" aria-labelledby="headingFour" data-parent="#accordionExample">
            <div class="card-body">
                <div id="discordance-index"></div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header" id="headingFive" data-toggle="collapse" data-target="#collapseFive" aria-expanded="false" aria-controls="collapseFive">
            <h2 class="mb-0 d-flex justify-content-between">
                <button class="btn btn-link collapsed" type="button">
                    Matrix Disconcordance
                </button>
                <small><i class="fas fa-angle-down"></i></small>
            </h2>
        </div>
        <div id="collapseFive" class="collapse" aria-labelledby="headingFive" data-parent="#accordionExample">
            <div class="card-body">
                <div id="matrix-disconcordance"></div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header" id="headingSeven" data-toggle="collapse" data-target="#collapseSeven" aria-expanded="false" aria-controls="collapseSeven">
            <h2 class="mb-0 d-flex justify-content-between">
                <button class="btn btn-link collapsed" type="button">
                    Hasil Matrix Disconcordance
                </button>
                <small><i class="fas fa-angle-down"></i></small>
            </h2>
        </div>
        <div id="collapseSeven" class="collapse" aria-labelledby="headingSeven" data-parent="#accordionExample">
            <div class="card-body">
                <div id="hasil-matrix-disconcordance"></div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header" id="headingNine" data-toggle="collapse" data-target="#collapseNine" aria-expanded="false" aria-controls="collapseNine">
            <h2 class="mb-0 d-flex justify-content-between">
                <button class="btn btn-link collapsed" type="button">
                    Disconcordance
                </button>
                <small><i class="fas fa-angle-down"></i></small>
            </h2>
        </div>
        <div id="collapseNine" class="collapse" aria-labelledby="headingNine" data-parent="#accordionExample">
            <div class="card-body">
                <div id="disconcordance"></div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header" id="headingTen" data-toggle="collapse" data-target="#collapseTen" aria-expanded="false" aria-controls="collapseTen">
            <h2 class="mb-0 d-flex justify-content-between">
                <button class="btn btn-link collapsed" type="button">
                    Agregate Dominan Matrix
                </button>
                <small><i class="fas fa-angle-down"></i></small>
            </h2>
        </div>
        <div id="collapseTen" class="collapse" aria-labelledby="headingTen" data-parent="#accordionExample">
            <div class="card-body">
                <div id="disconcordance-agregate"></div>
            </div>
        </div>
    </div>
</div>
<div class="card text-start">
    <div class="card-body">
        <form action="javascript:;" id="form-rekomendasi">
            <div class="row">
                @foreach($kriterias as $kriteria)
                @php
                $sub = $sub_kriterias->getSub($kriteria->uuid);
                @endphp
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="" class="form-label">{{ $kriteria->kriteria }}</label>
                        <select class="form-control" name="c{{ $kriteria->kode }}">
                            {{-- <option value="">Pilih {{ $kriteria->kriteria }}</option> --}}
                            @foreach($sub as $row)
                            <option value="{{ $row->bobot }}">{{ $row->sub_kriteria }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                @endforeach
                <div class="col-sm-6">
                    <button type="button" id="search-rekomendasi" class="btn btn-primary">
                        Cari Rekomendasi TEmpat Wisata
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
<div id="perankingan"></div>
<script src="/ex-script/rekomendasi.js"></script>
@endsection
