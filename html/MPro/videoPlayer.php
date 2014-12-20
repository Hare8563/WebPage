<!DOCTYPE HTML>
<html>
<head>
</head>
<body>
<?php
$videoPath = $_GET['videoPath'];
print "<video src=\"".$videoPath."\" type='video/mp4'></video>";
print "This is Video Player<br>";
?>
</body>
</html>