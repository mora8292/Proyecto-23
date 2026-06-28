<?php
	session_start();
	if (($_SESSION["usuario"]["nombre"])!= ''){
?>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title></title>
</head>
<body>
holaaaaaaa

<?php
echo $_SESSION["usuario"]["nombre"];
?>
<br><br>
<a href="destruir.php"> Cerrar </a>
</body>
</html>

<?php
}else{
header("Location: index.php");
}
?>
