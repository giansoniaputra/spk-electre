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
                    url: "/moora-create",
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
            url: "/moora-update/" + uuid,
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
            url: "/moora-normalisasi",
            type: "GET",
            dataType: 'json',
            success: function (response) {
                // console.log(response.hasil)
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
                        table += "<td>" + transposedMatrix[i][j].toFixed(3) + "</td>";
                    }

                    table += "</tr>";
                }

                table += "</table></div></div></div></div>";

                $("#matrix-normalisasi").html(table)
                $("#spinner").html("")
            }
        });
    })
    // Fungsi untuk mentranspose matriks
    function transpose(matrix) {
        return matrix[0].map((col, i) => matrix.map(row => row[i]));
    }
})
