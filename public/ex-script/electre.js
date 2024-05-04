$(document).ready(function () {
    $("#btn-add-perhitungan").on("click", function () {
        Swal.fire({
            title: "Yakin ingin menambah data baru?",
            text: "Anda akan mereset data sebelumnya",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, Buat!",
        }).then((result) => {
            if (result.isConfirmed) {
                $("#spinner").html(loader)
                $.ajax({
                    url: "/electre-create",
                    type: "GET",
                    dataType: "json",
                    success: function (response) {
                        $("#spinner").html("")
                        Swal.fire({
                            position: "center",
                            icon: "success",
                            title: response.success,
                            showConfirmButton: false,
                            timer: 1500
                        });
                        setTimeout(() => {
                            document.location.reload();
                        }, 2000)
                    },
                });
            }
        });
    });
    $("#table-perhitungan").on("click", "#nilai-bobot", function () {
        let current_input = document.querySelectorAll(".input-bobot")
        current_input.forEach((a) => {
            a.classList.add('d-none')
            a.parentElement.previousElementSibling.classList.remove('d-none')
        })
        $(this).children().eq(0).addClass("d-none")
        $(this).children().eq(1).children().eq(0).removeClass("d-none")
        $(this).children().eq(1).children().eq(0).focus()
    })
    $("#table-perhitungan").on("change", ".input-bobot", function () {
        let thiss = $(this)
        let p = $(this).parent().prev()
        let uuid = thiss.data("uuid")
        $.ajax({
            data: { bobot: thiss.val() },
            url: "/electre-update/" + uuid,
            type: "get",
            dataType: 'json',
            success: function (response) {
                p.html(response.success)
                thiss.val(response.success)
            }
        });
    })

    // KEPUTUSAN
    $("#btn-normalisasi").on("click", function () {
        $("#spinner").html(loader)
        $.ajax({
            url: "/electre-normalisasi",
            type: "GET",
            dataType: 'json',
            success: function (response) {
                let transposedMatrix = transpose(response.hasil);
                // Membuat tabel HTML
                let table = `<div class="row">
                                <div class="col-sm-12">
                                    <div class="card">
                                        <div class="card-header">
                                        <h2>Matrix Normalisasi</h2>
                                        </div>
                                        <div class="card-body">
                                        <table class='table table-bordered table-hover dtr-inline'><tr><th>Alternatif</th>`;
                // Menambahkan kolom untuk masing-masing transposedMatrix
                for (let i = 0; i < transposedMatrix[0].length; i++) {
                    table += "<th>C " + (i + 1) + "</th>";
                }

                table += "</tr>";

                // Mengisi tabel dengan data transposedMatrix
                for (let i = 0; i < transposedMatrix.length; i++) {
                    table += "<tr><td>A" + (i + 1) + "</td>";

                    for (let j = 0; j < transposedMatrix[i].length; j++) {
                        table += "<td>" + transposedMatrix[i][j].toFixed(4) + "</td>";
                    }

                    table += "</tr>";
                }

                table += "</table></div></div></div></div>";

                $("#matrix-normalisasi").html(table)

                // PEMBBOTAN NORMALISASI
                $.ajax({
                    data: { data: response.hasil },
                    url: "/electre-normalisasi-bobot",
                    type: "GET",
                    dataType: 'json',
                    success: function (response) {
                        let bobot_normalisasi = [];
                        // console.log(response.hasil);
                        let table2 = `<div class="row">
                                <div class="col-sm-12">
                                    <div class="card">
                                        <div class="card-header">
                                        <h2>Bobot Matrix Normalisasi</h2>
                                        </div>
                                        <div class="card-body">
                                        <table class='table table-bordered table-hover dtr-inline'><tr><th>Alternatif</th>`;
                        // Menambahkan kolom untuk masing-masing transposedMatrix
                        for (let i = 0; i < response.hasil[0].length; i++) {
                            table2 += "<th>C " + (i + 1) + "</th>";
                        }

                        table2 += "</tr>";

                        // Mengisi tabel dengan data response.hasil
                        for (let i = 0; i < response.hasil.length; i++) {
                            table2 += `<tr><td class="BA${i + 1}">A${i + 1}</td>`;

                            for (let j = 0; j < response.hasil[i].length; j++) {
                                table2 += "<td>" + response.hasil[i][j].toFixed(4) + "</td>";
                                bobot_normalisasi.push(response.hasil[i][j].toFixed(4))
                            }

                            table2 += "</tr>";
                        }

                        table2 += "</table></div></div></div></div>";

                        $("#bobot-normalisasi").html(table2)
                        $.ajax({
                            data: { data: response.hasil },
                            url: "/concordance-electre",
                            type: "GET",
                            dataType: 'json',
                            success: function (response) {
                                let data = response.hasil;
                                let alternatif = response.alternatif;
                                let kriteria = response.kriteria;
                                let bobot = response.bobot;
                                let table3 = `<div class="row">
                                <div class="col-sm-12">
                                    <div class="card">
                                        <div class="card-header">
                                        <h2>Table Corcondance Index</h2>
                                        </div>
                                        <div class="card-body">
                                        <table class='table table-bordered table-hover dtr-inline'><tr><th>Perbandingan Alternatif</th>`;

                                // Menambahkan kolom untuk masing-masing transposedMatrix
                                for (let i = 0; i < data[0].length; i++) {
                                    table3 += "<th>C " + (i + 1) + "</th>";
                                }
                                table3 += "<th></th>";

                                table3 += "</tr>";
                                for (h = 0; h < alternatif; h++) {

                                    // Mengisi tabel dengan data response.hasil
                                    for (let i = 0; i < transpose(data[h]).length; i++) {
                                        if ((h + 1) == (i + 1) || (h + 1) < (i + 1)) {
                                            var angka = i + 2
                                        } else {
                                            var angka = i + 1
                                        }
                                        table3 += `<tr><td>A${h + 1} - A${angka}</td>`;
                                        let collect = [];
                                        for (let j = 0; j < transpose(data[h])[i].length; j++) {
                                            table3 += "<td>" + transpose(data[h])[i][j] + "</td>";
                                            if (transpose(data[h])[i][j] == 1) {
                                                collect.push(j + 1)
                                            }
                                        }
                                        table3 += `<td>${collect}</td>`;
                                        table3 += "</tr>";
                                    }
                                    table3 += "<tr><td colspan='" + data[0].length + 1 + "'></td></tr>";

                                }
                                table3 += "</table></div></div></div></div>";
                                $("#concordance-index").html(table3)

                                // Discordance
                                let collect_dis = [];
                                let table4 = `<div class="row">
                                <div class="col-sm-12">
                                    <div class="card">
                                        <div class="card-header">
                                        <h2>Table Corcondance Index</h2>
                                        </div>
                                        <div class="card-body">
                                        <table class='table table-bordered table-hover dtr-inline'><tr><th>Perbandingan Alternatif</th>`;

                                // Menambahkan kolom untuk masing-masing transposedMatrix
                                for (let i = 0; i < data[0].length; i++) {
                                    table4 += "<th>C " + (i + 1) + "</th>";
                                }
                                table4 += "<th></th>";

                                table4 += "</tr>";
                                for (h = 0; h < alternatif; h++) {
                                    // Mengisi tabel dengan data response.hasil
                                    let disconcordance = [];
                                    for (let i = 0; i < transpose(data[h]).length; i++) {
                                        if ((h + 1) == (i + 1) || (h + 1) < (i + 1)) {
                                            var angka = i + 2
                                        } else {
                                            var angka = i + 1
                                        }
                                        table4 += `<tr class="disconcordance" onload="return bobot_dis(this)" data-satu="BA${h + 1}" data-dua="BA${angka}"><td>A${h + 1} - A${angka}</td>`;
                                        let collect = [];
                                        let collect2 = [];
                                        for (let j = 0; j < transpose(data[h])[i].length; j++) {
                                            if (transpose(data[h])[i][j] == 1) {
                                                var nilai = 0
                                            } else {
                                                var nilai = 1
                                            }
                                            table4 += "<td>" + nilai + "</td>";
                                            disconcordance.push(nilai)
                                            if (transpose(data[h])[i][j] == 0) {
                                                collect.push(j + 1)
                                                collect2.push(j + 1)
                                            } else {
                                                collect2.push(0)
                                            }
                                        }
                                        collect_dis.push(collect2)
                                        table4 += `<td>${collect}</td>`;
                                        table4 += "</tr>";
                                    }
                                    table4 += "<tr><td colspan='" + data[0].length + 1 + "'></td></tr>";
                                }
                                table4 += "</table></div></div></div></div>";
                                $("#discordance-index").html(table4)
                                // MATRIX CONCORDANCE
                                let table5 = `<div class="row">
                                <div class="col-sm-12">
                                    <div class="card">
                                        <div class="card-header">
                                        <h2>Table Matrix Corcondance</h2>
                                        </div>
                                        <div class="card-body">
                                        <table class='table table-bordered table-hover dtr-inline'><tr><th>Perbandingan Alternatif</th>`;

                                // Menambahkan kolom untuk masing-masing transposedMatrix
                                for (let i = 0; i < data[0].length; i++) {
                                    table5 += "<th>C " + (i + 1) + "</th>";
                                }
                                table5 += "<th>(Σ Concordance) Penjumlahan</th>";

                                table5 += "</tr>";
                                let hasil_matrix = [];
                                for (h = 0; h < alternatif; h++) {

                                    // Mengisi tabel dengan data response.hasil
                                    for (let i = 0; i < transpose(data[h]).length; i++) {
                                        if ((h + 1) == (i + 1) || (h + 1) < (i + 1)) {
                                            var angka = i + 2
                                        } else {
                                            var angka = i + 1
                                        }
                                        table5 += `<tr><td>A${h + 1} - A${angka}</td>`;
                                        let collect = 0;
                                        let itterasi = 0;
                                        for (let j = 0; j < transpose(data[h])[i].length; j++) {
                                            if (transpose(data[h])[i][j] == 1) {
                                                var nilai = bobot[j]
                                            } else {
                                                var nilai = 0
                                            }
                                            table5 += "<td>" + nilai + "</td>";
                                            if (transpose(data[h])[i][j] == 1) {
                                                collect += parseInt(nilai)
                                            }
                                        }
                                        table5 += `<td class="sum-matrix-concordance">${collect}</td>`;
                                        table5 += "</tr>";
                                        hasil_matrix.push(collect);
                                    }
                                    table5 += "<tr><td colspan='" + data[0].length + 1 + "'></td></tr>";

                                }
                                table5 += "</table></div></div></div></div>";
                                $("#matrix-concordance").html(table5)

                                // HASIL MATRIX CONCORDANCE
                                let array = createMultiDimArray(hasil_matrix, alternatif - 1);
                                let arrayBaru = [];
                                array.forEach((a, b) => {
                                    a.splice(b, 0, 0);
                                    arrayBaru.push(a);
                                })
                                let table6 = `<div class="row">
                                <div class="col-sm-12">
                                    <div class="card">
                                        <div class="card-header">
                                        <h2>Table Hasil Matrix Corcondance</h2>
                                        </div>
                                        <div class="card-body">
                                        <table class='table table-bordered table-hover dtr-inline'><tr><th>Alternatif</th>`;
                                // Menambahkan kolom untuk masing-masing transposedMatrix
                                for (let i = 0; i < arrayBaru[0].length; i++) {
                                    table6 += "<th>A " + (i + 1) + "</th>";
                                }
                                table6 += '</tr>'
                                for (h = 0; h < alternatif; h++) {
                                    table6 += '<tr>'
                                    table6 += "<th>A " + (h + 1) + "</th>";
                                    // Mengisi tabel dengan arrayBaru response.hasil
                                    for (let i = 0; i < arrayBaru[h].length; i++) {
                                        if (arrayBaru[h][i] == 0) {
                                            table6 += "<td style='background-color:#f1c40f' class='hasil-matrix-concordance'>" + arrayBaru[h][i] + "</td>";
                                        } else {
                                            table6 += "<td class='hasil-matrix-concordance'>" + arrayBaru[h][i] + "</td>";
                                        }
                                    }
                                    table6 += '</tr>'

                                }
                                table6 += "</table></div></div></div></div>";
                                $("#hasil-matrix-concordance").html(table6)
                                // HASIL MATRIX DISCONCORDANCE
                                setTimeout(() => {
                                    $.ajax({
                                        data: { array: collect_dis },
                                        url: "/matrix_dis",
                                        type: "GET",
                                        dataType: 'json',
                                        success: function (response) {
                                            $("#spinner").html("")
                                            let hasil = response.hasil
                                            let matrix_disconcordance = response.success
                                            let matrix_element = $("#matrix-disconcordance")
                                            let table7 = `<div class="row">
                                            <div class="col-sm-12">
                                                <div class="card">
                                                    <div class="card-header">
                                                        <h2>Table Hasil Matrix Corcondance</h2>
                                                    </div>
                                                    <div class="card-body">
                                                        <table class='table table-bordered table-hover dtr-inline'>
                                                            <thead>
                                                                <th>Perbandingan</th>
                                                                <th>(Σ Disconcordance) Nilai</th>
                                                            </thead>
                                                            <tbody>
                                                            `;
                                            matrix_disconcordance.map((a) => {
                                                table7 += `
                                                <tr>
                                                    <td>${a.name}</td>
                                                    <td class="sum-matrix-disconcordance">${a.nilai}</td>
                                                </tr>
                                                `
                                            })
                                            table7 += `</tbody></table></div></div></div>`;
                                            matrix_element.html(table7)
    
                                            // HASIL MATRIX DISCONCORDANCE
                                            let table8 = `<div class="row">
                                                            <div class="col-sm-12">
                                                                <div class="card">
                                                                    <div class="card-header">
                                                                    <h2>Table Hasil Matrix Discorcondance</h2>
                                                                    </div>
                                                                    <div class="card-body">
                                                                        <table class='table table-bordered table-hover dtr-inline'><tr><th>Alternatif</th>`;
                                            // Menambahkan kolom untuk masing-masing transposedMatrix
                                            for (let i = 0; i <= hasil[0].length; i++) {
                                                table8 += "<th>A " + (i + 1) + "</th>";
                                            }
                                            table8 += '</tr>'
                                            for (h = 0; h <= hasil[0].length; h++) {
                                                table8 += '<tr>'
                                                table8 += "<th>A " + (h + 1) + "</th>";
                                                // Mengisi tabel dengan arrayBaru response.hasil
                                                for (let i = 0; i < hasil[h].length; i++) {
                                                    if (h == i) {
                                                        table8 += "<td style='background-color:#f1c40f' class='hasil-matrix-disconcordance'>0</td>";
                                                        table8 += "<td class='hasil-matrix-disconcordance'>" + hasil[h][i] + "</td>";
                                                    } else {
                                                        table8 += "<td class='hasil-matrix-disconcordance'>" + hasil[h][i] + "</td>";
                                                    }
                                                }
                                                table8 += '</tr>'
                                            }
                                            table8 += "</table></div></div></div></div>";
                                            $("#hasil-matrix-disconcordance").html(table8)
                                            // console.log(collect_dis);
                                            let collect = document.querySelectorAll(".sum-matrix-concordance")
                                            let collect2 = document.querySelectorAll(".sum-matrix-disconcordance")
                                            let hasil_con = document.querySelectorAll(".hasil-matrix-concordance")
                                            let hasil_discon = document.querySelectorAll(".hasil-matrix-disconcordance")
                                            let c = [];
                                            let d = [];
                                            let con = [];
                                            let discon = [];
                                            collect.forEach((a) => {
                                                c.push(parseInt(a.innerHTML))
                                            })
                                            collect2.forEach((a) => {
                                                d.push(parseFloat(a.innerHTML))
                                            })
                                            hasil_con.forEach((a) => {
                                                con.push(parseInt(a.innerHTML))
                                            })
                                            hasil_discon.forEach((a) => {
                                                discon.push(parseFloat(a.innerHTML))
                                            })
                                            discon.push(0)
                                            let sum = c.reduce((accumulator, currentValue) => (accumulator + currentValue), 0);
                                            let sum2 = d.reduce((accumulator, currentValue) => (accumulator + currentValue), 0);
    
                                            let sum_c = (sum / (alternatif * (alternatif - 1))).toFixed(4);
                                            let sum_d = (sum2 / (alternatif * (alternatif - 1))).toFixed(4);
                                            let array_con = createMultiDimArray(con, alternatif);
                                            let array_discon = createMultiDimArray(discon, alternatif);
    
                                            let con_baru = array_con.map(sub_array => sub_array.map(nilai => nilai > sum_c ? 1 : 0));
                                            let discon_baru = array_discon.map(sub_array => sub_array.map(nilai => nilai > sum_d ? 1 : 0));
    
                                            let resultArray = [];
                                            if (con_baru.length === discon_baru.length) {
                                                for (let i = 0; i < con_baru.length; i++) {
                                                    let tempArray = [];
                                                    // Pastikan array dalam con_baru dan discon_baru memiliki panjang yang sama
                                                    if (con_baru[i].length === discon_baru[i].length) {
                                                        for (let j = 0; j < con_baru[i].length; j++) {
                                                            tempArray.push(con_baru[i][j] * discon_baru[i][j]);
                                                        }
                                                        resultArray.push(tempArray);
                                                    } else {
                                                        console.log("Panjang array di indeks " + i + " tidak sama.");
                                                    }
                                                }
                                            } else {
                                                console.log("Panjang array tidak sama.");
                                            }
    
                                            // RENDER TABLE CONCORDANCE
                                            let table10 = `<div class="row">
                                                               <div class="col-sm-12">
                                                                   <div class="card">
                                                                       <div class="card-header">
                                                                       <h2>Table Concordance</h2>
                                                                       </div>
                                                                       <div class="card-body">
                                                                       <table class='table table-bordered table-hover dtr-inline'><tr><th>Alternatif</th>`;
    
                                            // Menambahkan kolom untuk masing-masing transposedMatrix
                                            for (let i = 0; i < con_baru[0].length; i++) {
                                                table10 += "<th>A " + (i + 1) + "</th>";
                                            }
                                            table10 += "</tr>";
                                            for (h = 0; h < con_baru.length; h++) {
                                                // Mengisi tabel dengan data response.hasil
                                                table10 += `<tr><td>A${h + 1}</td>`;
                                                for (let i = 0; i < con_baru[h].length; i++) {
                                                    table10 += "<td>" + con_baru[h][i] + "</td>";
                                                }
                                            }
    
                                            table10 += "</table></div></div></div></div>";
                                            $("#concordance").html(table10)
    
                                            // RENDER TABLE DISCONCORDANCE
                                            let table11 = `<div class="row">
                                                               <div class="col-sm-12">
                                                                   <div class="card">
                                                                       <div class="card-header">
                                                                       <h2>Table Concordance</h2>
                                                                       </div>
                                                                       <div class="card-body">
                                                                       <table class='table table-bordered table-hover dtr-inline'><tr><th>Alternatif</th>`;
    
                                            // Menambahkan kolom untuk masing-masing transposedMatrix
                                            for (let i = 0; i < discon_baru[0].length; i++) {
                                                table11 += "<th>A " + (i + 1) + "</th>";
                                            }
                                            table11 += "</tr>";
                                            for (h = 0; h < discon_baru.length; h++) {
                                                // Mengisi tabel dengan data response.hasil
                                                table11 += `<tr><td>A${h + 1}</td>`;
                                                for (let i = 0; i < discon_baru[h].length; i++) {
                                                    table11 += "<td>" + discon_baru[h][i] + "</td>";
                                                }
                                            }
    
                                            table11 += "</table></div></div></div></div>";
                                            $("#disconcordance").html(table11)
    
                                            // RENDER TABLE DISCONCORDANCE
                                            let table12 = `<div class="row">
                                                               <div class="col-sm-12">
                                                                   <div class="card">
                                                                       <div class="card-header">
                                                                       <h2>Table Concordance</h2>
                                                                       </div>
                                                                       <div class="card-body">
                                                                       <table class='table table-bordered table-hover dtr-inline'><tr><th>Alternatif</th>`;
    
                                            // Menambahkan kolom untuk masing-masing transposedMatrix
                                            for (let i = 0; i < resultArray[0].length; i++) {
                                                table12 += "<th>A " + (i + 1) + "</th>";
                                            }
                                            table12 += "</tr>";
                                            for (h = 0; h < resultArray.length; h++) {
                                                // Mengisi tabel dengan data response.hasil
                                                table12 += `<tr><td>A${h + 1}</td>`;
                                                for (let i = 0; i < resultArray[h].length; i++) {
                                                    table12 += "<td>" + resultArray[h][i] + "</td>";
                                                }
                                            }
    
                                            table12 += "</table></div></div></div></div>";
                                            $("#disconcordance-agregate").html(table12)
    
                                            // PERANGKINGAN
                                            let array_c = createMultiDimArray(c, alternatif - 1)
                                            let array_d = createMultiDimArray(d, alternatif - 1)
                                            let sum_array_c = sum_array_multi(array_c)
                                            let sum_array_d = sum_array_multi(array_d)
    
                                            // RENDER TABLE DISCONCORDANCE
                                            let tableRanking = `<div class="row">
                                                               <div class="col-sm-12">
                                                                   <div class="card">
                                                                       <div class="card-header">
                                                                       <h2>Table Ranking</h2>
                                                                       </div>
                                                                       <div class="card-body">
                                                                       <table class='table table-bordered table-hover dtr-inline'>
                                                                        <thead>
                                                                            <tr>
                                                                                <th>Alternatif</th>
                                                                                <th>(Σ Concordance)</th>
                                                                                <th>(Σ Disoncordance)</th>
                                                                                <th>Σ Concordance - Σ Disconcordance</th>
                                                                                <th>Ranking</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                        `;
    
                                            // Menambahkan kolom untuk masing-masing transposedMatrix
                                            let rank = [];
                                            // RANKING
                                            for (h = 0; h < sum_array_c.length; h++) {
                                                rank.push(parseFloat(sum_array_c[h]) - parseFloat(sum_array_d[h]))
                                            }
                                            let final_rank = ranking(rank);
                                            let rankTable = [];
                                            // /RANKING
                                            for (h = 0; h < sum_array_c.length; h++) {
                                                rankTable.push([`A${h + 1}`, sum_array_c[h], sum_array_d[h].toFixed(4), (parseFloat(sum_array_c[h]) - parseFloat(sum_array_d[h])).toFixed(4), final_rank[h]])
                                            }
                                            let tableBaru = sortedArray(rankTable, 3);
                                            for (h = 0; h < tableBaru.length; h++) {
                                                tableRanking += `<tr>`;
                                                tableRanking += `<td>${tableBaru[h][0]}</td>`;
                                                tableRanking += `<td>${tableBaru[h][1]}</td>`;
                                                tableRanking += `<td>${tableBaru[h][2]}</td>`;
                                                tableRanking += `<td>${tableBaru[h][3]}</td>`;
                                                tableRanking += `<td>${tableBaru[h][4]}</td>`;
                                                tableRanking += `</tr>`;
                                            }
    
                                            tableRanking += "</tbody></table></div></div></div></div>";
                                            $("#perankingan").html(tableRanking)
                                        }
                                    });
                                },5000)
                                

                            }
                        });
                    }
                });
            }
        });
    })
    // Fungsi untuk mentranspose matriks
    function transpose(matrix) {
        return matrix[0].map((col, i) => matrix.map(row => row[i]));
    }

    function createMultiDimArray(array, chunkSize) {
        let multiDimArray = [];
        for (let i = 0; i < array.length; i += chunkSize) {
            multiDimArray.push(array.slice(i, i + chunkSize));
        }
        return multiDimArray;
    }
    function createMultiDimArray2(array, chunkSize) {
        const chunks = [];
        for (let i = 0; i < array.length; i++) {
            const subArray = array[i];
            const subArrayChunks = [];

            for (let j = 0; j < subArray.length; j += chunkSize) {
                subArrayChunks.push(subArray.slice(j, j + chunkSize));
            }

        }
        return chunks;
    }


    function bobot_dis() {
        console.log(this.data("satu"));
    }

    function sum_array_multi(array) {
        let newArray = array.map(subArray => {
            return subArray.reduce((acc, curr) => acc + curr, 0);
        });

        return newArray;
    }

    function ranking(arrayAwal) {
        // Buat salinan array awal untuk diurutkan
        const sortedArray = [...arrayAwal].sort((a, b) => b - a);

        // Buat objek untuk menetapkan peringkat ke setiap elemen dalam array
        const peringkat = {};
        let rank = 1;
        sortedArray.forEach((value, index) => {
            if (!(value in peringkat)) {
                peringkat[value] = rank++;
            }
        });

        // Buat array baru yang berisi peringkat dari setiap elemen dalam array awal
        const arrayBaru = arrayAwal.map(value => peringkat[value]);

        return arrayBaru;
    }

    function sortedArray(data, number) {
        const sortedData = data.sort((a, b) => {
            // Jika nilai kedua sama, gunakan urutan nama sebagai penentu
            if (b[number] === a[number]) {
                return a[0].localeCompare(b[0]);
            }
            // Jika nilai kedua berbeda, urutkan secara menurun berdasarkan nilai kedua
            return b[number] - a[number];
        });
        return sortedData
    }
});
