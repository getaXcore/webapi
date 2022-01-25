<html>
<head>
	<title>Bilangan prima</title>
</head>
<body>
	<div>
		<label>Menghitung bilangan prima<label>
		<form action="<?php echo url('/hit');?>" method="post">
			<label>Muali dari</label>
			<input type="number" name="input1" required>
			<label>hingga</label>
			<input type="number" name="input2" required>
			<input type="submit" value="Hit!">
		</form>
	</div>
</body>
</html>