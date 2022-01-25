<?php
?>
<html>
<head>
    <title>E-KTP CHECKER</title>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="E-KTP CHECKER" />
    <meta name="author" content="Taufan Septa" />
    <link rel="stylesheet" type="text/css" href="{{ asset('/public/css/bootstrap.css') }}">
    <script type="text/javascript" src="{{asset('/public/js/bootstrap.js')}}"></script>
    <script type="text/javascript" src="{{asset('/public/libjs/jquery.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('/public/js/func.bundle.js')}}"></script>
    <style type="text/css">
        .mb-3{
            width: 50%;
        }
        .align-content-center{
            margin-left: 30%;
        }
        /*.card{
            margin-left: 30%;
            width: 35%;
        }*/
        .card-title{
            font-weight: bolder;
        }
        #nodata{
            margin-left: 30%;
        }
    </style>
</head>
<body>
<!--<form class="align-content-center" action="#">-->
<div class="align-content-center">
    <div class="mb-3">
        <label class="form-label">NIK (E-KTP)</label>
        <input type="number" class="form-control" id="nik">
    </div>
    <div class="mb-3">
        <label class="form-label">Application No.</label>
        <input type="number" class="form-control" id="dataid">
    </div>
	<div class="mb-3">
        <label class="form-label">Debitur Option</label>
        <select class="form-select" aria-label="Default select example" id="deb">
			<option value='0'>Calon Debitur</option>
			<option value='1'>Pasangan Calon Debitur</option>
		</select>
    </div>
    <button type="submit" class="btn btn-primary" id="submit">Submit</button>
</div>
<!--</form>-->
<div id="nodata">
    <label class="form-label" id="txtNodata"></label>
</div>
<div class="container px-4" id="edata">
  <div class="row gx-5">
    <div class="col">
     <div class="p-3 border bg-light">
		<div class="card">
			<div class="card-header">
				DATA E-KTP
			</div>
			<div class="card-body">
				<h5 class="card-title">NIK</h5>
				<p class="card-text" id="rNik"></p>
				<h5 class="card-title">Nama</h5>
				<p class="card-text" id="rNama"></p>
				<h5 class="card-title">Tanggal Lahir</h5>
				<p class="card-text" id="rTglhr"></p>
				<h5 class="card-title">Tempat Lahir</h5>
				<p class="card-text" id="rTmplhr"></p>
				<h5 class="card-title">Jenis Kelamin</h5>
				<p class="card-text" id="rJenkel"></p>
				<h5 class="card-title">Status</h5>
				<p class="card-text" id="rStatus"></p>
				<h5 class="card-title">Pekerjaan</h5>
				<p class="card-text" id="rjob"></p>
				<!--<h5 class="card-title">Nama Ibu Kandung</h5>
				<p class="card-text" id="rNmibu"></p>-->
				<h5 class="card-title">Alamat</h5>
				<p class="card-text" id="rAlmt"></p>
				<h5 class="card-title">Kelurahan</h5>
				<p class="card-text" id="rkel"></p>
				<h5 class="card-title">Kecamatan</h5>
				<p class="card-text" id="rKec"></p>
				<h5 class="card-title">Kota / Kabupaten</h5>
				<p class="card-text" id="rkotakab"></p>
				<h5 class="card-title">Propinsi</h5>
				<p class="card-text" id="rProp"></p>
				<div style="font-style: italic;font-size: 10pt">*Sesuai/Tidak sesuai, berdasarkan hasil perbandingan antara data yang terekam di DUKCAPIL dan FAST</div>
			</div>
		</div>
	 </div>
    </div>
    <div class="col">
      <div class="p-3 border bg-light">
		<ul class="list-group">
		  <li class="list-group-item active" aria-current="true">DATA FAST</li>
		  <li class="list-group-item">NIK&nbsp:&nbsp<label id="fnik"></label></li>
		  <li class="list-group-item">Nama&nbsp:&nbsp<label id="fnama"></label></li>
		  <li class="list-group-item">Tanggal Lahir&nbsp:&nbsp<label id="ftglhr"></label></li>
		  <li class="list-group-item">Tempat Lahir&nbsp:&nbsp<label id="ftmplhr"></label></li>
		  <li class="list-group-item">Jenis Kelamin&nbsp:&nbsp<label id="fjenkel"></label></li>
		  <li class="list-group-item">Status&nbsp:&nbsp<label id="fstatus"></label></li>
		  <li class="list-group-item">Pekerjaan&nbsp:&nbsp<label id="fjob"></label></li>
		  <!--<li class="list-group-item">Nama Ibu Kandung&nbsp:&nbsp<label id="fnmibu"></label></li>-->
		  <li class="list-group-item">Alamat&nbsp:&nbsp<label id="falmt"></label></li>
		  <li class="list-group-item">Kelurahan&nbsp:&nbsp<label id="fkel"></label></li>
		  <li class="list-group-item">Kecamatan&nbsp:&nbsp<label id="fkec"></label></li>
		  <li class="list-group-item">Kota / Kabupaten&nbsp:&nbsp<label id="fkotakab"></label></li>
		  <li class="list-group-item">Propinsi&nbsp:&nbsp<label id="fprop"></label></li>
		</ul>
	  </div>
    </div>
  </div>
</div>
</body>
</html>
