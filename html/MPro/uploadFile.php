<?php
ini_set("display_errors",1);
error_reporting(E_ALL);
header('Content-type: text/plain; charset=utf-8');

$ImageMime = array('gif' => 'image/gif', 'jpg' => 'image/jpeg', 'png'=>'image/png', 'tiff'=> 'image/tiff', 'bmp'=>'image/x-bmp');
$videoMime = array('mp4'=>'video/mp4');
$length = Count($_FILES['upFile']['tmp_name']);
try{
$db = new PDO('sqlite:Report.db');
if($db==null){
throw new Exception("データベースを開けませんでした");
}
$db->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
if(!isset($_POST['thisWeek'])||
   !isset($_POST['problem'])||
   !isset($_POST['nextWeek'])){
throw new Exception("空欄があります。すべての項目を入力してから送信してください");
}
$thisWeek = $_POST['thisWeek'];
$problem = $_POST['problem'];
$nextWeek = $_POST['nextWeek'];
$teamID = $_POST['teamName'];
$m = $db->query("SELECT MAX(reportID) FROM REPORT;")->fetch();

$m2 = $db->query("SELECT MAX(ID) FROM Image;")->fetch();
$m3 = $db->query("SELECT MAX(ID) FROM Video;")->fetch();


if($m[0] == NULL){
	$m[0] = 0;
}
$maxValue = $m[0]+1;


if($m2[0] == NULL){
	$m2[0] = 0;
}
$maxValue2 = $m2[0]+1;


if($m3[0] == NULL){
	$m3[0] = 0;
}
$maxValue3 = $m3[0]+1;

}
catch(Exception $e){
echo 'エラー:'.$e->getMessage();
}


$ImageSetFlag=0;
$VideoSetFlag=0;

//ここからIMAGEファイルのアップロード処理
for($i=0;$i<$length;$i++){
	try{
		if(!isset($_FILES['upFile']['error'][$i]) ||
		   !is_int($_FILES['upFile']['error'][$i])
		  ){
		 throw new RuntimeException('パラメータが不正です');
		}
		switch($_FILES['upFile']['error'][$i]){
		case UPLOAD_ERR_OK:
			break;
		case UPLOAD_ERR_NO_FILE:
		throw new RuntimeException('ファイルが選択されてません');
		case UPLOAD_ERR_INI_SIZE:
		case UPLOAD_ERR_FORM_SIZE:
		throw new RuntimeException('ファイルサイズが大きすぎます');
		default:
		throw new RuntimeException('そのほかのエラーが発生しました');
		}

		if($_FILES['upFile']['size'][$i] > 20000000){
			throw new RuntimeException('ファイルサイズが大きすぎます');
		}
		
		$finfo = new finfo(FILEINFO_MIME_TYPE);
		
		if($ext = array_search($finfo->file($_FILES['upFile']['tmp_name'][$i]),$ImageMime,true)){
			if(
			!move_uploaded_file($_FILES['upFile']['tmp_name'][$i],$path=sprintf('./upload/Image/%s.%s',sha1_file($_FILES['upFile']['tmp_name'][$i]),$ext))
			){
				throw new RuntimeException('ファイル保存時にエラーが発生しました');
			}
		}
		else{
			throw new RuntimeException('ファイル形式が不正です。画像ファイルはgif, jpg, png, bmpのいずれかを使用してください');
		}
		chmod($path, 0644);
		
		//画像番号
		$db->query("INSERT INTO Image VALUES($maxValue2, '$path', $maxValue);");
		$ImageSetFlag = 1;
		
		echo 'ファイルは正常にアップロードされました';
	}catch(RuntimeException $e){
	echo $e->getMessage();
	$ImageSetFlag = 0;
	}
}

//ここからVideoファイルのアップロード処理
$length = Count($_FILES['upVideo']['tmp_name']);
for($i=0;$i<$length;$i++){
	try{
		if(!isset($_FILES['upVideo']['error'][$i]) ||
		   !is_int($_FILES['upVideo']['error'][$i])
		  ){
		 throw new RuntimeException('パラメータが不正です');
		}
		switch($_FILES['upVideo']['error'][$i]){
		case UPLOAD_ERR_OK:
			break;
		case UPLOAD_ERR_NO_FILE:
		throw new RuntimeException('ファイルが選択されてません');
		case UPLOAD_ERR_INI_SIZE:
		case UPLOAD_ERR_FORM_SIZE:
		throw new RuntimeException('ファイルサイズが大きすぎます');
		default:
		throw new RuntimeException('そのほかのエラーが発生しました');
		}

		if($_FILES['upVideo']['size'][$i] > 20000000){
			throw new RuntimeException('ファイルサイズが大きすぎます');
		}
		
		$finfo = new finfo(FILEINFO_MIME_TYPE);
		
		if($ext = array_search($finfo->file($_FILES['upVideo']['tmp_name'][$i]),$videoMime,true)){
			if(
			!move_uploaded_file($_FILES['upVideo']['tmp_name'][$i],$path=sprintf('./upload/Video/%s.%s',sha1_file($_FILES['upVideo']['tmp_name'][$i]),$ext))
			){
				throw new RuntimeException('ファイル保存時にエラーが発生しました');
			}
		}
		else{
			throw new RuntimeException('ファイル形式が不正です。動画ファイルはmp4を使用してください');
		}
		chmod($path, 0644);
		
		$db->query("INSERT INTO Video VALUES($maxValue3, '$path', $maxValue);");
		$VideoSetFlag = 1;
		echo 'ファイルは正常にアップロードされました';
	}catch(RuntimeException $e){
	echo $e->getMessage();
	$VideoSetFlag = 0;
	}
}



try{
$thisWeek = preg_replace("/\n/","<br>",$thisWeek);
$problem = preg_replace("/\n/","<br>",$problem);
$nextWeek = preg_replace("/\n/","<br>", $nextWeek);

$stmt=$db->prepare("INSERT INTO REPORT VALUES(:reportID,:teamID,:date,:thisWeek,:problem,:nextWeek,:ImageID,:VideoID);");
$stmt->bindValue(':reportID', $maxValue,PDO::PARAM_INT);
$stmt->bindValue(':teamID', $teamID, PDO::PARAM_INT);
$stmt->bindParam(':date',date('Y-n-j'),PDO::PARAM_STR);
$stmt->bindParam(':thisWeek', $thisWeek, PDO::PARAM_STR);
$stmt->bindParam(':problem', $problem, PDO::PARAM_STR);
$stmt->bindParam(':nextWeek', $nextWeek, PDO::PARAM_STR);
if($ImageSetFlag == 1){
	$stmt->bindValue(':ImageID',$maxValue2, PDO::PARAM_INT);
}
else{
	$stmt->bindValue(':ImageID',0, PDO::PARAM_INT);
}

if($VideoSetFlag == 1){
	$stmt->bindValue(':VideoID',$maxValue3,PDO::PARAM_INT);
}
else{
	$stmt->bindValue(':VideoID',0, PDO::PARAM_INT);
}
$a = $stmt->execute();
}
catch(Exception $e){
echo $e->getMessage();
}


?>
