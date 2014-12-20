<html lang="ja">
<head>
<meta charset="UTF-8">
<title>作業報告システム</title>
<style>
textarea{
resize: none;
width: 480px;
height: 240px;
}
</style>
</head>
<body>
<h1>作業報告システム</h1>
<div widht="640">

<?php
ini_set("display_errors", 1);

$id=$_GET['id'];
$db = new PDO('sqlite:Report.db');

if(isset($_POST['teamName'])&&
   isset($_POST['thisWeek'])&&
   isset($_POST['nextWeek'])&&
   isset($_POST['problem'])){
$teamName = $_POST['teamName'];
$thisWeek = preg_replace("/\n/","<br>",$_POST['thisWeek']);
$nextWeek = preg_replace("/n/", "<br>",$_POST['nextWeek']);
$problem = preg_replace("/n/","<br>",$_POST['problem']);

$stmt = $db->prepare("UPDATE REPORT SET teamID=$teamName,thisWeek=\"$thisWeek\",problem=\"$problem\",nextWeek=\"$nextWeek\" WHERE reportID=$id;");
$stmt->execute();

print "<p>更新作業が完了しました。</p><br><a href=\"./manageSelect.html\">管理画面へ</a>";

}
else{
print "<form method=\"POST\" action=\"EditReport.php?id=$id\" enctype=\"multipart/form-data\">
<table border=\"4\">
<tbody>";

$result = $db->query("SELECT * FROM REPORT WHERE reportID=$id;")->fetch();
	print "	<tr><td>チーム名</td><td>
	<select name=\"teamName\">
	<option value=\"1\" ";

	if($result[1] == 1) print "selected ";
	print ">Team:Rocket</option>
	<option value=\"2\" ";

	if($result[1] == 2)print "selected ";
	print ">Team Clutch</option>
	<option value=\"3\" ";

	if($result[1] == 3) print "selected ";
	print ">Team Violet</option>
	<option value=\"4\" ";

	if($result[1] == 4) print "selected ";
	print ">Team Riot</option>
	<option value=\"5\" ";

	if($result[1] == 5) print "selected ";
	
	
	$thisWeek=preg_replace("/<br>/", "\n", $result[3]);
	$problem=preg_replace("/<br>/", "\n", $result[4]);
	$nextWeek=preg_replace("/<br>/", "\n", $result[5]);
	print ">Team Sky</option>
	</select></td></tr>
	<tr><td>今週やったこと</td><td><textArea name=\"thisWeek\">$thisWeek</textArea></td></tr>
	<tr><td>発生した問題点</td><td><textArea name=\"problem\">$problem</textArea></td></tr>
	<tr><td>来週やること</td><td><textArea name=\"nextWeek\">$nextWeek</textArea></td></tr>";
print "</tbody>
</table>
<input type=\"submit\" value=\"送信\">
</form>";

}
?>

<!--
<tr><td>投稿画像</td><td><input type="file" name="upFile[]" multiple="multiple" accept="image/*"/></td></tr>
<tr><td>投稿動画</td><td><input type="file" name="upVideo[]" multiple="multiple"accept="video/*"/></td></tr>-->
</div>
</body>
</html>
