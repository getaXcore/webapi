<?php
?>
<!doctype html>
<html>
<head>
    <title>Asuransi Sinar Mas - Policy Issuance</title>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="Policy Issuance of Sinar Mas Insurance" />
    <meta name="author" content="Taufan Septa" />
    <link rel="stylesheet" type="text/css" href="{{ asset('/public/css/bootstrap.css') }}">
    <script type="text/javascript" src="{{asset('/public/js/bootstrap.js')}}"></script>
    <script type="text/javascript" src="{{asset('/public/libjs/jquery.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('/public/js/func2.bundle.js')}}"></script>
    <style type="text/css">
        .mb-3{
            width: 100%;
			padding-left:20%;
			padding-right:20%;
			padding-top:5%;
        }
        .align-content-center{
            margin-left: none;
        }
        /*.card{
            margin-left: 30%;
            width: 35%;
        }*/
        .card-title{
            font-weight: bolder;
        }
    </style>
</head>
<body>
<div class="align-content-center">
    <div class="mb-3">
		<div class="p-3 mb-2 bg-light text-dark">
			<img src="{{asset('/public/media/logojtosmall.jpg')}}" class="img-fluid float-end">
			<label class="float-end" style="padding-right: 1%;">Penerbitan Polis Asuransi Sinar Mas X JTO Finance</label>
		</div>
        <!--<label class="form-label">No.Kontrak</label>-->
        <input type="text" class="form-control" id="dataid" placeholder="Masukkan No.Kontrak">
		<div class="collapse" id="collapseExample">
		  <div class="card card-body">
			<label class="form-label">Masukkan User ID dan Password FAST Anda</label>
			<label class="form-label">User ID </label>
			<input type="text" class="form-control" id="uid">
			<label class="form-label">Password</label>
			<input type="password" class="form-control" id="pwd">
		  </div>
		</div>
		<div class="alert alert-primary" role="alert">
			<label class="form-label" id="txtNodata"></label>
		</div>
		<button class="btn btn-primary" type="button" data-bs-toggle="collapse" data-bs-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample" id="colbutton">
			Submit
		</button>
		<button type="submit" class="btn btn-primary" id="submit" style="display:none">Submit</button>
    </div>
</div>

<div class="container px-4" id="edata">
  <div class="row gx-5">
    <div class="col">
     <div class="p-3 border bg-light">
		<div class="card">
			<div class="card-header">
				<!--Judul di sini-->
			</div>
			<div class="card-body">
				<!--Kontentnya di sini-->
			</div>
		</div>
	 </div>
    </div>
  </div>
</div>
</body>
</html>
