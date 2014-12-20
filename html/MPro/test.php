<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>作業報告ページ</title>
</head>
<body>
<?php
ini_set("display_errors",1);
error_reporting(E_ALL);
$id = $_GET['id'];
$db = new PDO('sqlite:Report.db');
$result = $db->query("SELECT * FROM REPORT WHERE reportID=$id;")->fetch();
print "
<p><h1>作業報告</h1></p>
<h2>
";
switch($result[1]){
case 1:
print "Team:Rocket";
break;
case 2:
print "Team Clutch";
break;
case 3:
print "Team Violet";
break;
case 4:
print "Team Riot";
break;
case 5:
print "Team Sky";
break;
}
print "</h2><br>";
print "
<p><h3>今週やったこと</h3></p>
$result[3]<br>
<br>
<p><h3>発生した問題点</h3></p>
$result[4]<br>
<br>
<p><h3>来週やること</h3></p>
$result[5]<br>
<br>
<p><h3>今週の画像</h3></p>
";
$query="SELECT * FROM Image WHERE ID=$result[6];";
$images = $db->query($query)->fetchAll(PDO::FETCH_NUM);
for($i=0;$i<Count($images);$i++){
print "<img src='".$images[$i][1]."'/><br>";
}

?>
</body>
</html>

