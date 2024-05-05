<?php

namespace App\Http\Controllers;

use App\Models\Kriteria;
use App\Models\Alternatif;
use App\Models\SubKriteria;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\BobotNormalisasi;
use App\Models\PerhitunganElectre;
use Illuminate\Support\Facades\DB;
use App\Models\MatrikDisconcordance;
use App\Models\HasilMatrixCorcodance;
use App\Models\HasilMatrixCorcodance2;

class PerhitunganElectreController extends Controller
{
    public function index()
    {
        $data = [
            'title' => 'Perhitunga Electre',
            'electres' => DB::table('perhitungan_electres as a')
                ->join('alternatifs as b', 'a.alternatif_uuid', '=', 'b.uuid')
                ->select('a.*', 'b.alternatif', 'b.keterangan')
                ->orderBy('b.alternatif', 'asc'),
            'kriterias' => Kriteria::orderBy('kode', 'asc')->get(),
            'alternatifs' => Alternatif::orderBy('alternatif', 'asc')->get(),
            'sum_kriteria' => Kriteria::count('id'),
            'bobot_w' => new SubKriteria()
        ];
        return view('electre.index', $data);
    }

    public function create()
    {
        $cek = PerhitunganElectre::first();
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
                    PerhitunganElectre::create($data);
                }
            }
            return response()->json(['success' => 'Perhitungan Baru Berhasil Ditambahkan! Silahkan Masukan Nilainya']);
        } else {
            $kriterias = Kriteria::orderBy('kode', 'asc')->get();
            $alternatifs = Alternatif::orderBy('alternatif', 'asc')->get();
            foreach ($alternatifs as $alternatif) {
                $query = PerhitunganElectre::where('alternatif_uuid', $alternatif->uuid)->first();
                if (!$query) {
                    foreach ($kriterias as $kriteria) {
                        $data = [
                            'uuid' => Str::orderedUuid(),
                            'alternatif_uuid' => $alternatif->uuid,
                            'kriteria_uuid' => $kriteria->uuid,
                            'bobot' => 0
                        ];
                        PerhitunganElectre::create($data);
                    }
                }
            }
            foreach ($kriterias as $kriteria) {
                $query = PerhitunganElectre::where('kriteria_uuid', $kriteria->uuid)->first();
                if (!$query) {
                    foreach ($alternatifs as $alternatif) {
                        $data = [
                            'uuid' => Str::orderedUuid(),
                            'alternatif_uuid' => $alternatif->uuid,
                            'kriteria_uuid' => $kriteria->uuid,
                            'bobot' => 0
                        ];
                        PerhitunganElectre::create($data);
                    }
                }
            }
            return response()->json(['success' => 'Perhitungan Baru Berhasil Ditambahkan! Silahkan Masukan Nilainya']);
        }
    }

    public function update(PerhitunganElectre $electre, Request $request)
    {
        PerhitunganElectre::where('uuid', $electre->uuid)->update(['bobot' => $request->bobot]);
        return response()->json(['success' => $request->bobot]);
    }

    public function normalisasi()
    {
        return response()->json(['hasil' => $this->_normalisasi()]);
    }

    public function bobot_normalisasi(Request $request)
    {
        $array1 = [];
        $sub = new SubKriteria();
        $alternatifs = Alternatif::all();
        $kriterias = Kriteria::all();
        foreach ($kriterias as $kriteria) {
            $array1[] = $sub->get_bobot($kriteria->uuid);
        }
        $array2 = $request->data;

        $result = []; // array untuk menyimpan hasil perkalian

        // Iterasi melalui setiap elemen $array1
        for ($i = 0; $i < count($array1); $i++) {
            // Inisialisasi array untuk setiap elemen $array1
            $temp = [];

            // Iterasi melalui setiap elemen $array2 yang sesuai
            for ($j = 0; $j < count($array2[$i]); $j++) {
                // Perkalian elemen $array1 dengan elemen $array2 yang sesuai
                $temp[] = $array1[$i] * $array2[$i][$j];
            }

            // Menyimpan hasil perkalian dalam $result
            $result[] = $temp;
        }
        DB::table('bobot_normalisasis')->truncate();
        for ($i = 0; $i < count($alternatifs); $i++) {
            foreach ($this->_transpose($result)[$i] as $index => $row) {
                $data = [
                    'A' => $i + 1,
                    'C' => $index + 1,
                    'nilai' => round($row, 4)
                ];
                BobotNormalisasi::create($data);
            }
        }
        return response()->json(['hasil' => $this->_transpose($result)]);
    }

    public function concordance_electre(Request $request)
    {
        $array1 = [];
        $sub = new SubKriteria();
        $kriterias = Kriteria::all();
        foreach ($kriterias as $kriteria) {
            $array1[] = $sub->get_bobot($kriteria->uuid);
        }
        $array = $this->_transpose($request->data);

        $alternatif = Alternatif::all();
        $kriteria = Kriteria::all();

        $rows = count($array);
        $columns = count($array[0]);
        $array_baru = [];

        for ($h = 0; $h < count($alternatif); $h++) {
            for ($i = 0; $i < $rows; $i++) {
                for ($j = 0; $j < $columns; $j++) {
                    if ($j == $h) {
                        continue; // Lewati perbandingan jika indeks j sama dengan 0
                    }
                    if ($array[$i][$h] >= $array[$i][$j]) {
                        $array_baru[] = 1;
                    } elseif ($array[$i][$h] <= $array[$i][$j]) {
                        $array_baru[] = 0;
                    } else {
                        echo 0;
                    }
                }
            }
        }
        $hasil = array_chunk(array_chunk($array_baru, count($alternatif) - 1), count($kriteria));
        return response()->json([
            'hasil' => $hasil,
            'alternatif' => count($alternatif),
            'kriteria' => count($kriteria),
            'bobot' => $array1
        ]);
    }

    public function matrix_dis(Request $request)
    {
        $dis = $request->array;
        $alternatifs = Alternatif::all();
        $kriterias = Kriteria::all();
        $newDis = array_chunk($dis, count($alternatifs) - 1);
        DB::table('matrik_disconcordances')->truncate();
        $fake_database = [];
        for ($i = 0; $i < count($newDis); $i++) {
            for ($j = 0; $j < count($alternatifs) - 1; $j++) {
                for ($k = 0; $k < count($kriterias); $k++) {
                    if ($j >= $i) {
                        if ($newDis[$i][$j][$k] != 0) {
                            $fake_database[] = [
                                'AI' => $i + 1,
                                'AJ' => $j + 2,
                                'C' => $k + 1,
                                'index' => $newDis[$i][$j][$k],
                            ];
                            // MatrikDisconcordance::create($data);
                        }
                    } else {
                        if ($newDis[$i][$j][$k] != 0) {
                            $fake_database[] = [
                                'AI' => $i + 1,
                                'AJ' => $j + 1,
                                'C' => $k + 1,
                                'index' => $newDis[$i][$j][$k],
                            ];
                            // MatrikDisconcordance::create($data);
                        }
                    }
                }
            }
        }

        // MATRIX CONCORDANCE
        // $matrix_concordance = [];
        $matrixs = $fake_database;
        $lastAI = 0;
        $lastAJ = 0;
        $hasil = [];
        $fake_database2 = [];
        DB::table('hasil_matrix_corcodances')->truncate();
        foreach ($matrixs as $index => $matrix) {
            $AI = $matrix['AI'];
            $AJ = $matrix['AJ'];

            $C = $matrix['C'];

            $atas = BobotNormalisasi::where('A', $AI)->where('C', $C)->first();
            $bawah = BobotNormalisasi::where('A', $AJ)->where('C', $C)->first();
            // PEMBILANG ALTERNATIF
            // PEMBAGI ALTERNATIF

            $fake_database2[] = [
                'A' => $AI,
                'B' => $AJ,
                'nilai' =>  $atas->nilai . '-' . $bawah->nilai,
            ];
            // HasilMatrixCorcodance::create($data);

            // $matrix_concordance["A$AI - A$AJ"] = round((max($pembilang) / max($pembagi)), 4);
            // RESET
            // $lastAI *= 0;
            // $lastAJ *= 0;
            // $lastAI += $AI;
            // $lastAJ += $AJ;
        }

        DB::table('hasil_matrix_corcodance2s')->truncate();
        for ($i = 1; $i <= count($alternatifs); $i++) {
            for ($j = 1; $j <= count($alternatifs); $j++) {
                $hasil_matrixs = HasilMatrixCorcodance::where('A', $i)->where('B', $j)->get();
                if (count($hasil_matrixs) > 0) {

                    $atas = [];
                    foreach ($hasil_matrixs as $row) {
                        $split = explode('-', $row->nilai);
                        $atas[] = abs($split[0] - $split[1]);
                    }
                    $bawah = [];
                    $bawahAllA = BobotNormalisasi::where('A', $i)->get();
                    $bawahAllB = BobotNormalisasi::where('A', $j)->get();
                    for ($k = 0; $k < count($kriterias); $k++) {
                        $bawah[] = abs($bawahAllA[$k]->nilai - $bawahAllB[$k]->nilai);
                    }
                    $data2 = [
                        'name' => 'A' . $i . '-' . 'A' . $j,
                        'nilai' => round((max($atas) / max($bawah)), 4)
                    ];

                    HasilMatrixCorcodance2::create($data2);
                } else if (count($hasil_matrixs) == 0 && $j != $i) {
                    $data2 = [
                        'name' => 'A' . $i . '-' . 'A' . $j,
                        'nilai' => 0
                    ];
                    HasilMatrixCorcodance2::create($data2);
                }
            }
        }
        $query = HasilMatrixCorcodance2::all();
        $rows = [];
        foreach ($query as $row) {
            $rows[] = $row->nilai;
        }
        // return response()->json(['success' => array_chunk($dis, count($alternatifs) - 1), 'matrix_concordance' => $matrix_concordance]);
        return response()->json(['success' => $query, 'hasil' => array_chunk($rows, count($alternatifs) - 1)]);
    }
    public function normalisasi_user(Request $request)
    {
        $array_pembilang = [];
        $array_pembagi = [];
        $count_alternatif = Alternatif::count('id');
        $kriterias = Kriteria::orderBy('kode', 'asc')->get();
        $alternatifs = Alternatif::orderBy('alternatif', 'asc')->get();
        $mooras = PerhitunganElectre::all();
        $last_id = Alternatif::orderBy('alternatif', 'desc')->first();
        $last_id_moora = PerhitunganElectre::orderBy('id', 'desc')->first();
        // MENAMBAH ALTERNATIF BARU
        $newRecord = new Alternatif();
        $newRecord->id = $last_id->id + 1;
        $newRecord->uuid = Str::orderedUuid();
        $newRecord->alternatif = count($alternatifs) + 1;
        $newRecord->keterangan = 'Pilihan Anda';
        $newRecord->created_at = Carbon::now();
        $newRecord->updated_at = Carbon::now();
        $alternatifs->push($newRecord);
        // MENAMBAH ALTERNATIF BARU
        // MENAMBAH PERHITUNGAN BARU
        $i = 0;
        foreach ($request->all() as $index => $row) {
            $newMoora = new PerhitunganElectre();
            $newMoora->id =  $last_id_moora->id++;
            $newMoora->uuid = Str::orderedUuid();
            $newMoora->alternatif_uuid = $alternatifs[count($alternatifs) - 1]->uuid;
            $newMoora->kriteria_uuid = $kriterias[$i++]->uuid;
            $newMoora->bobot = $row;
            $newMoora->created_at = Carbon::now();
            $newMoora->updated_at = Carbon::now();
            $mooras->push($newMoora);
        }
        // $query = $mooras->where('alternatif_uuid', $alternatifs[5]->uuid)->first();
        // return response()->json(['alternatifs' => $mooras]);
        // return response()->json(['success' => $alternatifs[5]->uuid . '  /  ' . $kriterias[1]->uuid]);
        // MENAMBAH PERHITUNGAN BARU
        foreach ($kriterias as $kriteria) {
            foreach ($alternatifs as $alternatif) {
                $query = $mooras->where('alternatif_uuid', $alternatif->uuid)->where('kriteria_uuid', $kriteria->uuid)->first();
                $array_pembagi[] = pow($query->bobot, 2);
                $array_pembilang[] = $query->bobot;
            }
        }
        $kuadrat = array_chunk($array_pembagi, $count_alternatif + 1);
        $pembilang = array_chunk($array_pembilang, $count_alternatif + 1);
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
        return response()->json(['hasil' => $hasil, 'alternatifs' => $alternatifs]);
    }

    public function bobot_normalisasi_user(Request $request)
    {
        $array1 = [];
        $sub = new SubKriteria();
        $alternatifs = $request->alternatifs;
        $kriterias = Kriteria::all();
        foreach ($kriterias as $kriteria) {
            $array1[] = $sub->get_bobot($kriteria->uuid);
        }
        $array2 = $request->data;

        $result = []; // array untuk menyimpan hasil perkalian

        // Iterasi melalui setiap elemen $array1
        for ($i = 0; $i < count($array1); $i++) {
            // Inisialisasi array untuk setiap elemen $array1
            $temp = [];

            // Iterasi melalui setiap elemen $array2 yang sesuai
            for ($j = 0; $j < count($array2[$i]); $j++) {
                // Perkalian elemen $array1 dengan elemen $array2 yang sesuai
                $temp[] = $array1[$i] * $array2[$i][$j];
            }

            // Menyimpan hasil perkalian dalam $result
            $result[] = $temp;
        }
        DB::table('bobot_normalisasis')->truncate();
        for ($i = 0; $i < count($alternatifs); $i++) {
            foreach ($this->_transpose($result)[$i] as $index => $row) {
                $data = [
                    'A' => $i + 1,
                    'C' => $index + 1,
                    'nilai' => round($row, 4)
                ];
                BobotNormalisasi::create($data);
            }
        }
        return response()->json(['hasil' => $this->_transpose($result), 'alternatifs' => $alternatifs]);
    }

    public function concordance_electre_user(Request $request)
    {
        $array1 = [];
        $sub = new SubKriteria();
        $kriterias = Kriteria::all();
        foreach ($kriterias as $kriteria) {
            $array1[] = $sub->get_bobot($kriteria->uuid);
        }
        $array = $this->_transpose($request->data);

        $alternatif = $request->alternatifs;
        $kriteria = Kriteria::all();

        $rows = count($array);
        $columns = count($array[0]);
        $array_baru = [];

        for ($h = 0; $h < count($alternatif); $h++) {
            for ($i = 0; $i < $rows; $i++) {
                for ($j = 0; $j < $columns; $j++) {
                    if ($j == $h) {
                        continue; // Lewati perbandingan jika indeks j sama dengan 0
                    }
                    if ($array[$i][$h] >= $array[$i][$j]) {
                        $array_baru[] = 1;
                    } elseif ($array[$i][$h] <= $array[$i][$j]) {
                        $array_baru[] = 0;
                    } else {
                        echo 0;
                    }
                }
            }
        }
        $hasil = array_chunk(array_chunk($array_baru, count($alternatif) - 1), count($kriteria));
        return response()->json([
            'hasil' => $hasil,
            'alternatif' => count($alternatif),
            'alternatifs' => $alternatif,
            'kriteria' => count($kriteria),
            'bobot' => $array1
        ]);
    }

    public function matrix_dis_user(Request $request)
    {
        $dis = $request->array;
        $alternatifs = $request->alternatifs;
        $kriterias = Kriteria::all();
        $newDis = array_chunk($dis, count($alternatifs) - 1);
        DB::table('matrik_disconcordances')->truncate();
        // foreach ($newDis as $index => $row) {
        //     foreach ($alternatifs as $index2 => $row2) {
        //         foreach ($kriterias as $index3 => $row3) {
        //             if ($index == $index2) {
        //                 continue;
        //             }
        //             $data = [
        //                 'AI' => $index + 1,
        //                 'AJ' => $index2 + 1,
        //                 'index' => $newDis[$index][$index2][$index3]
        //             ];
        //             MatrikDisconcordance::create($data);
        //         }
        //     }
        // }
        for ($i = 0; $i < count($newDis); $i++) {
            for ($j = 0; $j < count($alternatifs) - 1; $j++) {
                for ($k = 0; $k < count($kriterias); $k++) {
                    if ($j >= $i) {
                        if ($newDis[$i][$j][$k] != 0) {

                            $data = [
                                'AI' => $i + 1,
                                'AJ' => $j + 2,
                                'C' => $k + 1,
                                'index' => $newDis[$i][$j][$k],
                            ];
                            MatrikDisconcordance::create($data);
                        }
                    } else {
                        if ($newDis[$i][$j][$k] != 0) {
                            $data = [
                                'AI' => $i + 1,
                                'AJ' => $j + 1,
                                'C' => $k + 1,
                                'index' => $newDis[$i][$j][$k],
                            ];
                            MatrikDisconcordance::create($data);
                        }
                    }
                }
            }
        }

        // MATRIX CONCORDANCE
        // $matrix_concordance = [];
        $matrixs = MatrikDisconcordance::all();
        $lastAI = 0;
        $lastAJ = 0;
        $hasil = [];
        DB::table('hasil_matrix_corcodances')->truncate();
        foreach ($matrixs as $index => $matrix) {
            $AI = $matrix->AI;
            $AJ = $matrix->AJ;

            $C = $matrix->C;

            $atas = BobotNormalisasi::where('A', $AI)->where('C', $C)->first();
            $bawah = BobotNormalisasi::where('A', $AJ)->where('C', $C)->first();
            // PEMBILANG ALTERNATIF
            // PEMBAGI ALTERNATIF

            $data = [
                'A' => $AI,
                'B' => $AJ,
                'nilai' =>  $atas->nilai . '-' . $bawah->nilai,
            ];
            HasilMatrixCorcodance::create($data);

            // $matrix_concordance["A$AI - A$AJ"] = round((max($pembilang) / max($pembagi)), 4);
            // RESET
            // $lastAI *= 0;
            // $lastAJ *= 0;
            // $lastAI += $AI;
            // $lastAJ += $AJ;
        }

        DB::table('hasil_matrix_corcodance2s')->truncate();
        for ($i = 1; $i <= count($alternatifs); $i++) {
            for ($j = 1; $j <= count($alternatifs); $j++) {
                $hasil_matrixs = HasilMatrixCorcodance::where('A', $i)->where('B', $j)->get();
                if (count($hasil_matrixs) > 0) {

                    $atas = [];
                    foreach ($hasil_matrixs as $row) {
                        $split = explode('-', $row->nilai);
                        $atas[] = abs($split[0] - $split[1]);
                    }
                    $bawah = [];
                    $bawahAllA = BobotNormalisasi::where('A', $i)->get();
                    $bawahAllB = BobotNormalisasi::where('A', $j)->get();
                    for ($k = 0; $k < count($kriterias); $k++) {
                        $bawah[] = abs($bawahAllA[$k]->nilai - $bawahAllB[$k]->nilai);
                    }
                    $data2 = [
                        'name' => 'A' . $i . '-' . 'A' . $j,
                        'nilai' => round((max($atas) / max($bawah)), 4)
                    ];

                    HasilMatrixCorcodance2::create($data2);
                } else if (count($hasil_matrixs) == 0 && $j != $i) {
                    $data2 = [
                        'name' => 'A' . $i . '-' . 'A' . $j,
                        'nilai' => 0
                    ];
                    HasilMatrixCorcodance2::create($data2);
                }
            }
        }
        $query = HasilMatrixCorcodance2::all();
        $rows = [];
        foreach ($query as $row) {
            $rows[] = $row->nilai;
        }
        // return response()->json(['success' => array_chunk($dis, count($alternatifs) - 1), 'matrix_concordance' => $matrix_concordance]);
        return response()->json(['success' => $query, 'hasil' => array_chunk($rows, count($alternatifs) - 1)]);
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
                $query = PerhitunganElectre::where('alternatif_uuid', $alternatif->uuid)->where('kriteria_uuid', $kriteria->uuid)->first();
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


    function _transpose($matrix)
    {
        $transposedMatrix = [];
        foreach ($matrix as $rowIndex => $row) {
            foreach ($row as $colIndex => $value) {
                $transposedMatrix[$colIndex][$rowIndex] = $value;
            }
        }
        return $transposedMatrix;
    }
}
