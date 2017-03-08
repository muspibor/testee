<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="description" content="Paypal Account Checker - Version 2.0" />
	<meta name="author" content="v3ch4j (at) VHC-VE" />
	<title>Paypal's Account Checker - Contact Y!M: nvlqn</title>
	<link href="style.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.9.1/jquery-ui.min.js"></script>

</head>
<body>
<?php

$emailArea = <<< email
admin@ugcode.com|pAssw0rd
root@ugcode.com|p@sswORd
email;
$sockArea = <<< sock
127.0.0.1:1080
sock;
?>
<div align="center">
    <h1>Paypal's Account Checker</h1>
    <h4>Code by King Billy</h4>
</div>
<form method="post">
	<div align="center">
		<textarea name="mailpass" id="mailpass" cols="90" rows="10"><?php

echo $emailArea;

?></textarea>
		<br />
		Delimiter: <input type="text" name="delim" id="delim" value="|" size="1" />
		&nbsp;<input type="checkbox" name="email" id="email" />Check Email |
        Change socks if fail: <input type="text" name="fail" id="fail" value="5" size="1" /> time(s)<br />
		<input type="button" class="submit-button" value=" START CHECK " id="submit" />&nbsp;<input type="button" class="submit-button" value=" STOP " id="stop" /><br /><br />
        <img id="loading" src="clear.gif" /><br />
        <span id="checkStatus"></span>
	</div>
</form>
<div id="result">
    <fieldset class="fieldset">
        <legend class="pplive">LIVE: <span id="pplive_count">0</span></legend>
        <div id="pplive"></div>
    </fieldset>
    <fieldset class="fieldset">
        <legend class="ppdie">DIE: <span id="ppdie_count">0</span></legend>
        <div id="ppdie"></div>
    </fieldset>
    <fieldset class="fieldset">
        <legend class="pplive">Live (No Card):</legend>
        <div id="wrong"></div>
    </fieldset>
    <fieldset class="fieldset">
        <legend class="ppdie">Bad Socks:</legend>
        <div id="badsock"></div>
    </fieldset>
</div>
<script type="text/javascript" src="script.js"></script>
</body>
</html>