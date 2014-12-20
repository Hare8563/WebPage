<?php
if(empty($_POST['id']) || empty($_POST['passwd'])){
print "IDまたはパスワードが空欄です。入力してからログインしてください<br>";
}
else{
$db = new PDO('sqlite:Users.db');

$id = $_POST['id'];
$pass = $_POST['passwd'];
$result = $db->query("SELECT * FROM UserData WHERE ID='".$id."' AND Pass='".$pass."';")->fetchAll(PDO::FETCH_NUM);
$r = Count($result);
$c = Count($result[0]);
if($r > 0){
print $result[0][0]."さんがログインしました<br>";
header("LOCATION:manageSelect.html");
}
else{
print "ID番号かまたはパスワードが間違ってます。もう一度確認してください<br>";
}
}
?>
