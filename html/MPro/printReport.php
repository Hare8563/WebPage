<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>作業報告</title>
</head>
<body>
<?php
ini_set("display_errors",1);
error_reporting(E_ALL);
$db = new PDO('sqlite:Report.db');
$result = $db->query("SELECT * FROM REPORT;")->fetchAll(PDO::FETCH_NUM);
$row=Count($result);
//$column=Count($result[0]);

print "<table border=1 width=80%>";
for($i=$row-1;$i>=0;$i--){
	print "<tr>";
	print "<td>".$result[$i][0]."</td>";
	switch($result[$i][1]){
	case 1:
	print "<td>Team:Rocket</td>";
		break;
	case 2:
	print "<td>Team Clutch</td>";
		break;
	case 3:
	print "<td>Team Violet</td>";
		break;
	case 4:
	print "<td>Team Riot</td>";
		break;
	case 5:
	print "<td>Team Sky</td>";
		break;
	}
	print "<td>".$result[$i][2]."</td>";
	print "<td><a href='./test.php?id=".$result[$i][0]."' target='_blank'>開く</a></td>";
	print "<td><a href='./EditReport.php?id=".$result[$i][0]."' target='_blank'>編集</a></td>";
	print "</tr>";
}
print "</table>"

?>
</body>
</html>

