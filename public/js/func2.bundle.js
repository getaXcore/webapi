$(document).ready(function () {
	//mencegah input non numeric
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
        dataid = $.trim($("#dataid").val());
		userid = $.trim($("#uid").val());
		pass = $.trim($("#pwd").val());
		lenOfDataid = $("#dataid").val().length;
		validateUrl = "https://devapi.jto.co.id/webapi/v1/checkon";
		fetchingUrl = "https://devapi.jto.co.id/webapi/v1/fetching";
		
		
		if (lenOfDataid != 13){
            $("#dataid").css("border-color","red");
            $("#txtNodata").html("No.Kontrak harus diisi 13 digit!");
			$("#submit").prop('disabled', false);
		}else if(userid == ""){
			$("#uid").css("border-color","red");
            $("#txtNodata").html("User ID harus diisi!");
			$("#submit").prop('disabled', false);
		}else if(pass == ""){
			$("#pwd").css("border-color","red");
            $("#txtNodata").html("Password harus diisi!");
			$("#submit").prop('disabled', false);
		}else{
			
			datasend = {
                contractNo:dataid,
				uid:userid,
				pwd:pass
            }
			
			$("#txtNodata").html("Mohon menunggu...");
			$("#submit").prop('disabled', true);
			$("#dataid").css("border-color","");
			$("#uid").css("border-color","");
			$("#pwd").css("border-color","");

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
                    //$("#txtNodata").html("");
					$("#submit").prop('disabled', false);

                    console.log(data.code);
                    if (data.code == 1){ //data ada di tabel
						if(data.data == 1){
							$("#txtNodata").html("No.Kontrak ditemukan!");
						}else{
							$("#txtNodata").html("No.Kontrak tidak ditemukan!");
						}
					}else{
						$("#txtNodata").html("No.Kontrak tidak ditemukan!");
					}
				}
			});

		}

       
    });
	
	$("#colbutton").click(function () {
		$("#colbutton").css("display","none");
		$("#submit").css("display","");
	});
})