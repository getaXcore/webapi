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
		<div class="container-sm">
			<label class="form-label">DTTOT Data Checking</label>
		</div>
        <div class="container-sm">
			<form method="post" action="<?php echo url('/dttot')?>" class="form-control">
				<div class="mb-3 row">
					<label class="col-sm-2 col-form-label">NIK</label>
					<div class="col-sm-auto">
						<input type="text" class="form-control" name="nik" value="<?php if(isset($_REQUEST['submit'])){echo $_REQUEST["nik"];} ?>">
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-2 col-form-label">Nama</label>
					<div class="col-sm-auto">
						<input type="text" class="form-control" name="nama" value="<?php if(isset($_REQUEST['submit'])){echo $_REQUEST["nama"];} ?>">
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-2 col-form-label">Tempat Lahir</label>
					<div class="col-sm-auto">
						<input type="text" class="form-control" name="tempat_lahir" value="<?php if(isset($_REQUEST['submit'])){echo $_REQUEST["tempat_lahir"];} ?>">
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-2 col-form-label">Tanggal Lahir</label>
					<div class="col-sm-auto">
						<input type="date" class="form-control" name="tanggal_lahir" value="<?php if(isset($_REQUEST['submit'])){echo $_REQUEST["tanggal_lahir"];} ?>">
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-2 col-form-label">Alamat</label>
					<div class="col-sm-auto">
						<textarea class="form-control" name="alamat" ><?php if(isset($_REQUEST['submit'])){echo $_REQUEST["alamat"];} ?></textarea>
					</div>
				</div>
				<div class="mb-3 row">
					<label class="col-sm-2 col-form-label">&nbsp;</label>
					<div class="col-sm-auto">
						<input class="btn btn-primary" type="submit" value="Cek Data" name="submit">
					</div>
				</div>
			</form>
        </div>
		
	<?php
	if(isset($_REQUEST['submit'])){
		?>
		<div class="container-sm">
		<?php
		//if(count($data) > 0){
		if(count($dttot) > 0){
		?>
			<div class="alert alert-danger" role="alert">
				Debitur Terduga! Data ditemukan.
			</div>
		<?php
		}else{
		?>
			<div class="alert alert-primary" role="alert">
				Debitur Bersih! Data tidak ditemukan.
			</div>
		
		</div>
		<?php
		}
		?>
		<div class="container-sm" style="margin-top: 1%;">
			<?php
			$param = "nik=".$_REQUEST['nik']."&nama=".$_REQUEST['nama']."&tempat_lahir=".$_REQUEST['tempat_lahir']."&tanggal_lahir=".$_REQUEST['tanggal_lahir']."&alamat=".$_REQUEST['alamat'];
			?>
			&nbsp;<a class="btn btn-primary btn-lg" href="<?php echo url("cetak?".$param);?>" role="button" target="_blank">Cetak Data</a>
		</div>
		<div class="container-sm table-responsive">
			<div id="areacetak"  class="table-responsive">
				<table class="table table-striped table-hover">
					<caption>Total : {{ $dttot->total() }} data</caption>
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
							 <th scope="row"><?php echo ($dttot->currentpage()-1) * $dttot->perpage() + $no; //echo $no ?></th>
							 <td><?php echo $value->NIK; ?></td>
							 <td><?php echo $value->NAMA;?></td>
							 <td><?php echo $value->TEMPAT_LAHIR;?></td>
							 <td><?php echo $value->TANGGAL_LAHIR;?></td>
							 <td><?php echo $value->ALAMAT;?></td>
						</tr>	   
			<?php
			}
		}else{
			?>
						<tr>
							 <th scope="row"><?php echo $no+1 ?></th>
							 <td><?php if(!empty($_POST["nik"])){echo $_POST["nik"];}else{echo "-";}?></td>
							 <td><?php if(!empty($_POST["nama"])){echo $_POST["nama"];}else{echo "-";}?></td>
							 <td><?php if(!empty($_POST["tempat_lahir"])){echo $_POST["tempat_lahir"];}else{echo "-";}?></td>
							 <td><?php if(!empty($_POST["tanggal_lahir"])){$date = date_create($_POST["tanggal_lahir"]);echo date_format($date, 'd/m/Y');}else{echo "-";}?></td>
							 <td><?php if(!empty($_POST["alamat"])){echo $_POST["alamat"];}else{echo "-";}?></td>
						</tr>
			<?php
		}
		?>
					</tbody>
				</table>
			</div>	
			{{ $dttot->appends($_REQUEST)->links() }}	
		</div>
		<?php
	}
		?>
    </body>
</html>
