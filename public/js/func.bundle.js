$(document).ready(function () {
	//mencegah input non numeric
    document.getElementById('nik').addEventListener('keydown',function (eventNik){
        let key = window.eventNik ? eventNik.keyCode : eventNik.which;

        if (eventNik.keyCode == 8 || eventNik.keyCode == 46 || eventNik.keyCode == 37 || key.keyCode == 39) {
            return true;
        }else if ( key < 48 || key > 57 ) {
            eventNik.preventDefault();
        }else return true;
    });
    document.getElementById('dataid').addEventListener('keydown',function (event){
        let key = window.event ? event.keyCode : event.which;

        if (event.keyCode == 8 || event.keyCode == 46 || event.keyCode == 37 || key.keyCode == 39) {
            return true;
        }else if ( key < 48 || key > 57 ) {
            event.preventDefault();
        }else return true;
    });
	
    $("#edata").hide();  //hide sementara box hasil

    $("#submit").click(function () {
        nik = $.trim($("#nik").val());
        dataid = $.trim($("#dataid").val());
		optDeb = $.trim($("#deb").val());
		lenOfDataid = $("#dataid").val().length;
		lenOfnik = $("#nik").val().length;
		validateUrl = "https://devapi.jto.co.id/webapi/v1/validate";
		fetchingUrl = "https://devapi.jto.co.id/webapi/v1/fetching";

        if (!nik){
            $("#nik").css("border-color","red");
        }else if (lenOfDataid < 11){
            $("#dataid").css("border-color","red");
            $("#txtNodata").html("Application No. harus lebih dari 10 digit!");
        }else if (lenOfnik < 16 || lenOfnik > 16){
            $("#nik").css("border-color","red");
            $("#txtNodata").html("NIK harus 16 digit!");
        }else if (!dataid){
            $("#dataid").css("border-color","red");
        }else {
            //remove red bolor border
            $("#nik").css("border-color","");
            $("#dataid").css("border-color","");

            datasend = {
                nik:nik,
				dataid:dataid,
                type:"1",
				optDeb:optDeb
            }

            $("#txtNodata").html("Mohon menunggu...");

            $.ajax({
                url: validateUrl, //cek ada datanya atau tidak
                type: 'post',
                data: JSON.stringify(datasend),
                headers: {
                    Authorization: 'Basic dGF1ZmFuOnNlcHRhdWZhbmk=',
                    'Content-Type': 'application/json'
                },
                dataType: 'json',
                success: function (data) {
                    $("#txtNodata").html("");

                    console.log(data.code);
                    if (data.code == 1){ //data ada di tabel

                        datasend = {
                            nik:nik,
							dataid:dataid,
                            type:"2",
							optDeb:optDeb
                        }

                        $.ajax({
                            url: validateUrl, //ambil datanya dari tabel
                            type: 'post',
                            data: JSON.stringify(datasend),
                            headers: {
                                Authorization: 'Basic dGF1ZmFuOnNlcHRhdWZhbmk=',
                                'Content-Type': 'application/json'
                            },
                            dataType: 'json',
                            success: function (data) {
								if (data.code == 1){
									//isikan ke elemen2 di field edata
									$("#rNik").html(data.data.nik);
									$("#rNama").html(data.data.nama);
									$("#rTglhr").html(data.data.tglahir);
									$("#rTmplhr").html(data.data.tempatlahir);
									$("#rJenkel").html(data.data.jeniskelamin);
									$("#rStatus").html(data.data.status);
									$("#rjob").html(data.data.jenispekerjaan);
									//$("#rNmibu").html(data.data.namalengkapibu);
									$("#rAlmt").html(data.data.alamat);
									$("#rkel").html(data.data.kelurahan);
									$("#rKec").html(data.data.kecamatan);
									$("#rkotakab").html(data.data.kotakabupaten);
									$("#rProp").html(data.data.propinsi);
									
									$("#fnik").html(data.sysdata.nik);
									$("#fnama").html(data.sysdata.nama);
									$("#ftglhr").html(data.sysdata.tglahir);
									$("#ftmplhr").html(data.sysdata.tempatlahir);
									$("#fjenkel").html(data.sysdata.jeniskelamin);
									$("#fstatus").html(data.sysdata.status);
									$("#fjob").html(data.sysdata.jenispekerjaan);
									//$("#fnmibu").html(data.sysdata.namalengkapibu);
									$("#falmt").html(data.sysdata.alamat);
									$("#fkel").html(data.sysdata.kelurahan);
									$("#fkec").html(data.sysdata.kecamatan);
									$("#fkotakab").html(data.sysdata.kotakabupaten);
									$("#fprop").html(data.sysdata.propinsi);

									$("#edata").fadeIn(1000); //tampilkan data

									//console.log(data.data);
								}else{
									$("#edata").hide();
									$("#txtNodata").html("Data Tidak Ditemukan");
								}
                            }
                        });
                    }else {
                        //jika tidak ada datanya
                        //ambil langsung ke dukcapil
                        datasend = {
                            nik:nik,
                            dataid:dataid,
							optDeb:optDeb
                        }
						
						$("#txtNodata").html("Mohon menunggu...");

                        $.ajax({
                            url: fetchingUrl, //fetching dulu
                            type: 'post',
                            data: JSON.stringify(datasend),
                            headers: {
                                Authorization: 'Basic dGF1ZmFuOnNlcHRhdWZhbmk=',
                                'Content-Type': 'application/json'
                            },
                            dataType: 'json',
                            success: function (data) {
								$("#txtNodata").html("");
								
                                if (data.code == 1){
                                    datasend = {
                                        nik:nik,
										dataid:dataid,
                                        type:"2",
										optDeb:optDeb
                                    }

                                    $.ajax({
                                        url: validateUrl, //ambil datanya dari tabel
                                        type: 'post',
                                        data: JSON.stringify(datasend),
                                        headers: {
                                            Authorization: 'Basic dGF1ZmFuOnNlcHRhdWZhbmk=',
                                            'Content-Type': 'application/json'
                                        },
                                        dataType: 'json',
                                        success: function (data) {
											if (data.code == 1){
												//isikan ke elemen2 di field edata
												$("#rNik").html(data.data.nik);
												$("#rNama").html(data.data.nama);
												$("#rTglhr").html(data.data.tglahir);
												$("#rTmplhr").html(data.data.tempatlahir);
												$("#rJenkel").html(data.data.jeniskelamin);
												$("#rStatus").html(data.data.status);
												$("#rjob").html(data.data.jenispekerjaan);
												//$("#rNmibu").html(data.data.namalengkapibu);
												$("#rAlmt").html(data.data.alamat);
												$("#rkel").html(data.data.kelurahan);
												$("#rKec").html(data.data.kecamatan);
												$("#rkotakab").html(data.data.kotakabupaten);
												$("#rProp").html(data.data.propinsi);
												
												$("#fnik").html(data.sysdata.nik);
												$("#fnama").html(data.sysdata.nama);
												$("#ftglhr").html(data.sysdata.tglahir);
												$("#ftmplhr").html(data.sysdata.tempatlahir);
												$("#fjenkel").html(data.sysdata.jeniskelamin);
												$("#fstatus").html(data.sysdata.status);
												$("#fjob").html(data.sysdata.jenispekerjaan);
												//$("#fnmibu").html(data.sysdata.namalengkapibu);
												$("#falmt").html(data.sysdata.alamat);
												$("#fkel").html(data.sysdata.kelurahan);
												$("#fkec").html(data.sysdata.kecamatan);
												$("#fkotakab").html(data.sysdata.kotakabupaten);
												$("#fprop").html(data.sysdata.propinsi);

												$("#edata").fadeIn(1000); //tampilkan data

												//console.log(data.data);
											}else{
												$("#edata").hide();
												$("#txtNodata").html("Data Tidak Ditemukan");
											}
                                        }
                                    });
                                }
                                else {
                                    $("#edata").hide();
                                    $("#txtNodata").html("Data Tidak Ditemukan");
                                }
                            }
                        });


                    }
                }
            });
        }
    })
})