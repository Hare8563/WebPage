<?php
$db = new PDO('sqlite:test.db');
$result = $db->query("SELECT * FROM test;")->fetchAll(PDO::FETCH_NUM);

$rowNum = Count($result);
$columNum = Count($result[0]);
print "<table border=1 width=80%>";

	for($i = 0; $i < $rowNum;$i++){
	print "<tr>";
		for($j = 0; $j < $columNum; $j++){
			print "<td>".$result[$i][$j]."</td>";
		}
	print "</tr>";
	}
print "</table>";
?>
