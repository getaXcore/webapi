<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>DTTOT</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">

		<!-- Scripts -->
		<script type="text/javascript" src="{{asset('/public/js/bootstrap.min.js')}}"></script>
		<script type="text/javascript" src="{{asset('/public/js/jquery.min.js')}}"></script>
		<script type="text/javascript">
			var i;

			for (i = 0; i < close.length; i++) {
			  close[i].onclick = function(){
			var div = this.parentElement;
			div.style.opacity = "0";
				setTimeout(function(){ div.style.display = "none"; }, 600);
				  }
			}

			function printa(){
				var prtContent = document.getElementById("areacetak");
				var WinPrint = window.open('', '', 'left=0,top=0,width=800,height=900,toolbar=0,scrollbars=0,status=0');
				WinPrint.document.write(prtContent.innerHTML);
				WinPrint.document.close();
				WinPrint.focus();
				WinPrint.print();
				WinPrint.close();
			}
		</script>
        <!-- Styles -->
		<link rel="stylesheet" type="text/css" href="{{ asset('/public/css/bootstrap.css') }}">
        <style>
            body {
                font-family: 'Nunito', sans-serif;
            }
			.mb-3{
				margin-left:auto !important;
				margin-top:15px !important;
			}
			form{
				background-color: floralwhite !important;
			}
        </style>
    </head>
    <body>
		<div class="container-sm" style="margin-top: 1%;">
			&nbsp;<button type="button" class="btn btn-primary btn-lg" id="cetak" name="cetak" onclick="printa();">PRINT</button>
		</div>
		<div class="container-sm table-responsive">
			<div id="areacetak"  class="table-responsive">
			<div class="container-sm">
				<center><label class="form-label"><b><h2><u>Temuan data Debitur Terduga DTTOT</u></h2></b></label></center>
			</div>
				<table class="table table-striped table-hover">
					<thead>
						<tr>
							<th scope="col">No</th>
							<th scope="col">NIK</th>
							<th scope="col">Nama</th>
							<th scope="col">Tempat Lahir</th>
							<th scope="col">Tanggal Lahir</th>
							<th scope="col">Alamat</th>
						</tr>
					</thead>
					<tbody>
		<?php
		$no = 0;
		//if(count($data) > 0){
		if(count($dttot) > 0){
			//foreach($data as $value){
			foreach($dttot as $value){
				$no++;
			?>				  
						<tr>
							 <th scope="row"><?php echo  $no; ?></th>
							 <td><?php echo $value->NIK; ?></td>
							 <td><?php echo $value->NAMA;?></td>
							 <td><?php echo $value->TEMPAT_LAHIR;?></td>
							 <td><?php echo $value->TANGGAL_LAHIR;?></td>
							 <td><?php echo $value->ALAMAT;?></td>
						</tr>	   
			<?php
			}
		}
		?>
					</tbody>
				</table>
			</div>		
		</div>
    </body>
</html>
