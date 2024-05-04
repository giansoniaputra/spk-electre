<?php

namespace App\Http\Controllers;

use App\Models\Kriteria;
use App\Models\Alternatif;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\PerhitunganMoora;
use Illuminate\Support\Facades\DB;
use FPDF;

class PerhitunganMooraController extends Controller
{
    public function index()
    {
        $data = [
            'title' => 'Perhitungan Moora',
            'mooras' => DB::table('perhitungan_mooras as a')
                ->join('alternatifs as b', 'a.alternatif_uuid', '=', 'b.uuid')
                ->select('a.*', 'b.alternatif', 'b.keterangan')
                ->orderBy('b.alternatif', 'asc'),
            'kriterias' => Kriteria::orderBy('kode', 'asc')->get(),
            'alternatifs' => Alternatif::orderBy('alternatif', 'asc')->get(),
            'sum_kriteria' => Kriteria::count('id'),
        ];
        return view('moora.index', $data);
    }

    public function index_saw()
    {
        $data = [
            'title' => 'Perhitungan SAW',
            'mooras' => DB::table('perhitungan_mooras as a')
                ->join('alternatifs as b', 'a.alternatif_uuid', '=', 'b.uuid')
                ->select('a.*', 'b.alternatif', 'b.keterangan')
                ->orderBy('b.alternatif', 'asc'),
            'kriterias' => Kriteria::orderBy('kode', 'asc')->get(),
            'alternatifs' => Alternatif::orderBy('alternatif', 'asc')->get(),
            'sum_kriteria' => Kriteria::count('id'),
        ];
        return view('saw.index', $data);
    }

    public function create()
    {
        $cek = PerhitunganMoora::first();
        if (!$cek) {
            $kriterias = Kriteria::orderBy('kode', 'asc')->get();
            $alternatifs = Alternatif::orderBy('alternatif', 'asc')->get();
            foreach ($alternatifs as $alternatif) {
                foreach ($kriterias as $kriteria) {
                    $data = [
                        'uuid' => Str::orderedUuid(),
                        'alternatif_uuid' => $alternatif->uuid,
                        'kriteria_uuid' => $kriteria->uuid,
                        'bobot' => 0
                    ];
                    PerhitunganMoora::create($data);
                }
            }
            return response()->json(['success' => 'Perhitungan Baru Berhasil Ditambahkan! Silahkan Masukan Nilainya']);
        } else {
            $kriterias = Kriteria::orderBy('kode', 'asc')->get();
            $alternatifs = Alternatif::orderBy('alternatif', 'asc')->get();
            foreach ($alternatifs as $alternatif) {
                $query = PerhitunganMoora::where('alternatif_uuid', $alternatif->uuid)->first();
                if (!$query) {
                    foreach ($kriterias as $kriteria) {
                        $data = [
                            'uuid' => Str::orderedUuid(),
                            'alternatif_uuid' => $alternatif->uuid,
                            'kriteria_uuid' => $kriteria->uuid,
                            'bobot' => 0
                        ];
                        PerhitunganMoora::create($data);
                    }
                }
            }
            foreach ($kriterias as $kriteria) {
                $query = PerhitunganMoora::where('kriteria_uuid', $kriteria->uuid)->first();
                if (!$query) {
                    foreach ($alternatifs as $alternatif) {
                        $data = [
                            'uuid' => Str::orderedUuid(),
                            'alternatif_uuid' => $alternatif->uuid,
                            'kriteria_uuid' => $kriteria->uuid,
                            'bobot' => 0
                        ];
                        PerhitunganMoora::create($data);
                    }
                }
            }
            return response()->json(['success' => 'Perhitungan Baru Berhasil Ditambahkan! Silahkan Masukan Nilainya']);
        }
    }

    public function update(PerhitunganMoora $moora, Request $request)
    {
        PerhitunganMoora::where('uuid', $moora->uuid)->update(['bobot' => $request->bobot]);
        return response()->json(['success' => $request->bobot]);
    }

    public function normalisasi()
    {
        return response()->json(['hasil' => $this->_normalisasi()]);
    }

    public function preferensi(Request $request)
    {
        $hasil = $this->_preferensi($request->data);
        return response()->json([
            'result' => $hasil['result'],
            'hasil' => $hasil['hasil']
        ]);
    }

    public function _preferensi($data)
    {
        $kriterias = Kriteria::orderBy('kode', 'asc');

        $bobot = [];
        foreach ($kriterias->get() as $kriteria) {
            $bobot[] = kriteria::bobot($kriteria->bobot);
        }
        $result_array = [];
        for ($i = 0; $i < count($data); $i++) {
            for ($j = 0; $j < count($bobot); $j++) {
                $result_array[] = floatval(number_format($data[$i][$j] * $bobot[$j], 3));
            }
        }
        $final_result = array_chunk($result_array, $kriterias->count('id'));
        $rangking = [];
        $atribut = [];
        foreach ($kriterias->get() as $row) {
            $atribut[] = $row->atribut;
        }
        //     COST   0     BENEFIT 1    COST 2      BENEFIT 3    BENEFIT 4   BENEFIT 5   BENEFIT 6  BENEFIT  7
        // -----------------------------------------------------------------------------------------------------
        // 0 | 0.098058068	0.04472136	0.08479983	0.060633906	0.05547002	0.043759497	0.050709255	0.036514837
        // 1 | 0.098058068	0.04472136	0.08479983	0.036380344	0.041602515	0.058345997	0.050709255	0.036514837
        // 2 | 0.098058068	0.04472136	0.08479983	0.036380344	0.041602515	0.043759497	0.03380617	0.054772256
        // 3 | 0.039223227	0.04472136	0.105999788	0.048507125	0.041602515	0.043759497	0.03380617	0.036514837
        // 4 | 0.098058068	0.04472136	0.08479983	0.036380344	0.041602515	0.029172998	0.050709255	0.054772256
        $result = [];

        // // Loop melalui setiap array (SIPA)
        // for ($k = 0; $k < count($final_result); $k++) {
        //     for ($l = 0; $l < count($bobot); $l++) {
        //         $jumlah = 0;
        //         if ($atribut[$l] == 'BENEFIT') {
        //             $jumlah += $final_result[$k][$l];
        //         } else {
        //             $jumlah -= $final_result[$k][$l];
        //         }
        //         $rangking[] = $jumlah;
        //     }
        // }
        // Loop melalui setiap array (RIZAL)
        for ($k = 0; $k < count($final_result); $k++) {
            for ($l = 0; $l < count($bobot); $l++) {
                $jumlah = 0;
                if ($atribut[$l] == 'BENEFIT') {
                    $jumlah += $final_result[$k][$l];
                } else {
                    $jumlah += $final_result[$k][$l];
                }
                $rangking[] = $jumlah;
            }
        }

        $rangking_result = array_chunk($rangking, $kriterias->count('id'));
        $final_ranking = [];
        for ($u = 0; $u < count($rangking_result); $u++) {
            $final_ranking[] = array_sum($rangking_result[$u]);
        }

        $nama = Alternatif::orderBy('alternatif', 'asc')->get();
        $rangking_assoc = [];
        foreach ($final_ranking as $index => $nilai) {
            $rangking_assoc[] = [$nama[$index]->keterangan, $nilai];
        }

        $names = array_column($rangking_assoc, 0);
        $scores = array_column($rangking_assoc, 1);

        // Menggunakan array_multisort untuk mengurutkan scores secara menurun
        array_multisort($scores, SORT_DESC, $names);

        // Menggabungkan kembali array setelah diurutkan
        $result2 = array_map(function ($name, $score) {
            return [$name, $score];
        }, $names, $scores);


        return [
            'result' => $final_result,
            'hasil' => $result2
        ];
    }
    public function _normalisasi()
    {
        $array_pembilang = [];
        $array_pembagi = [];
        $count_alternatif = Alternatif::count('id');
        $kriterias = Kriteria::orderBy('kode', 'asc')->get();
        $alternatifs = Alternatif::orderBy('alternatif', 'asc')->get();
        foreach ($kriterias as $kriteria) {
            foreach ($alternatifs as $alternatif) {
                $query = PerhitunganMoora::where('alternatif_uuid', $alternatif->uuid)->where('kriteria_uuid', $kriteria->uuid)->first();
                $array_pembagi[] = pow($query->bobot, 2);
                $array_pembilang[] = $query->bobot;
            }
        }
        $kuadrat = array_chunk($array_pembagi, $count_alternatif);
        $pembilang = array_chunk($array_pembilang, $count_alternatif);
        $pembagi = [];
        foreach ($kuadrat as $row) {
            $jumlah = array_sum($row);
            $akarKuadrat = floatval(number_format(sqrt($jumlah), 3));
            $pembagi[] = $akarKuadrat;
        }
        $hasil = [];
        foreach ($pembilang as $row => $val) {
            $hasil[$row] = array_map(function ($value) use ($row, $pembagi) {
                return floatval(number_format($value / $pembagi[$row], 3));
            }, $val);
        }
        return $hasil;
    }
}
