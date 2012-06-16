<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>
<?php $this->output('title') ?>
</title>
<meta http-equiv="Content-Type" content="application/xhtml+xml; charset=UTF-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<!-- IE render the webpage using lastest version, eg: IE9 or IE8 -->
<?php echo $this->stylesheets; ?>
<link type="text/css" rel="stylesheet" href="web/css/common.css" />
<script type="text/javascript" src="web/javascript/jquery.min.js"></script>
<!-- <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>  -->
<script type="text/javascript" src="web/javascript/common.js"></script>
<?php echo $this->javascripts; ?>
</head>

<body>
<ul>
	<li><a href="./">Home</a></li>
    <li><a href="?ac=area">Area</a></li>
	<li><a href="?ac=carrera">Carrera</a></li>
</ul>
<div id="content">
  <?php $this->output('content') ?>
</div>
<div id="footer"> Copyright &copy; <?PHP echo date("Y");?> Developed by LibroSoft Team<br />
  Page processed in <?PHP echo round(microtime(true)-$GLOBALS["start_time"],3);?> seconds </div>
</div>
</body>
</html>
<?PHP echo "<!-- ".memory_get_peak_usage()."-".memory_get_peak_usage(true)." -->";?>
