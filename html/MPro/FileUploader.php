<!doctype html>
<html>
<head>
<meta charset="UTF-8">
<title>FILE UPLOADER</title>
<script src="./js/jquery-1.10.2.min" type="text/javascript"></script>
</head>

<body>
<h1>FILE UPLOADER</h1>
<h2>以下のフォーマットを守ってください</h2>
<p>画像ファイルはJPG, PNG, GIFのいずれかでお願いします。それ以外のフォーマットは使えません(limit: 20MB)</p>
<p>動画ファイルはH.264エンコーダーでエンコードしたmp4動画でお願いします(limit: 100MB)</p>
<p>オブジェクトファイルは.obj, .mtl, テクスチャ, サムネイル画像(thumb.jpg)をzipファイルに圧縮し、アップロードしてください。(limit: 20MB)</p>
<form action="FileUploader.php" id="uploadForm" method="POST" enctype="multipart/form-data">
    <table width="466" border="1" >
    <tr>
    <td width="174">ファイルタイプ</td><td width="276">
    <select name="fileType" id="selector">
    <option value="1">Image</option>
    <option value="2">Video</option>
    <option value="3">Object</option>
    </select>
    </td>
    </tr>
    <tr>
    <td width="174">対象ファイル</td><td><input type="file"  name="file[]" multiple></td>
    </tr>
    </table>
    <input type="submit"/>
</form>
	<?php
		$ImageMime = array('gif' => 'image/gif', 'jpg' => 'image/jpeg', 'png'=>'image/png', 'bmp'=>'image/x-bmp');
		$videoMime = array('mp4'=>'video/mp4');
		$objMime = array('zip'=>'application/x-compress', 'zip'=>'application/x-lha-compressed', 'zip'=>'application/x-zip-compressed', 'zip'=>'application/zip');
		if(isset($_POST['fileType'])){
			//ファイルの数を確認
			$length = Count($_FILES['file']['tmp_name']);
			$fileSelection = $_POST['fileType'];
			
			try{
				$db = new PDO('sqlite:mediaContents.db');
				if($db==null){
					throw new Exception("データベースを開けませんでした");
				}
			}catch(Exception $e){
				echo 'エラー:'.$e->getMessage();
			}
			
			switch($fileSelection){
				case 1:
				upLoadImage($length);
				break;
				case 2:
				upLoadVideo($length);
				break;
				case 3:
				upLoadObj($length);
				break;
			}
		}
		
		
		//For Upload Image
		function upLoadImage($length){
			global $ImageMime,$db;					

			
			$m = $db->query("SELECT MAX(ID) FROM MEDIA;")->fetch();
			$maxValue = $m[0]+1;
			
			//ここからIMAGEファイルのアップロード処理
			for($i=0;$i<$length;$i++){
				try{
					if(!isset($_FILES['file']['error'][$i]) ||
					   !is_int($_FILES['file']['error'][$i])
					  ){
					 throw new RuntimeException('パラメータが不正です');
					}
					switch($_FILES['file']['error'][$i]){
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
			
					if($_FILES['file']['size'][$i] > 20000000){
						throw new RuntimeException('ファイルサイズが大きすぎます');
					}
					
					$finfo = new finfo(FILEINFO_MIME_TYPE);
					
					if($ext = array_search($finfo->file($_FILES['file']['tmp_name'][$i]),$ImageMime,true)){
						if(
						!move_uploaded_file($_FILES['file']['tmp_name'][$i],$path=sprintf('./upload/Image/%s.%s',sha1_file($_FILES['file']['tmp_name'][$i]),$ext))
						){
							throw new RuntimeException('ファイル保存時にエラーが発生しました。FILENAME:'.$path);
						}
					}
					else{
						throw new RuntimeException('ファイル形式が不正です。画像ファイルはgif, jpg, pngのいずれかを使用してください');
					}
					chmod($path, 0644);
					print $ext;
					//サムネイル生成
					switch($ext){
						case 'jpg':
						$image = imagecreatefromjpeg($path);
						break;
						case 'png':
						$image = imagecreatefrompng($path);
						break;
						case 'gif':
						$image = imagecreatefromgif($path);
						break;
					}
					$thumbnail_width=200;
					$thumbnail_height=200;
					$width  = imagesx( $image );
					$height = imagesy( $image );
					if ( $width >= $height ) {
						$thumbnail_height = $thumbnail_width * ($height / $width);
						
						
						//横長の画像の時
						$side = $height;
						$x = floor( ( $width - $height ) / 2 );
						$y = 0;
						$width = $side;
					} else {
						$thumbnail_width = $thumbnail_height * ($width / $height);
						
						//縦長の画像の時
						$side = $width;
						$y = floor( ( $height - $width ) / 2 );
						$x = 0;
						$height = $side;
					}
					$thumbnail = imagecreatetruecolor($thumbnail_width, $thumbnail_height);
					imagecopyresized( $thumbnail, $image, 0, 0, $x, $y, $thumbnail_width, $thumbnail_height, $width, $height);
					$thumbPath = sprintf("./upload/Thumbnail/%sjpg", basename($path, $ext));
					imagejpeg( $thumbnail, $thumbPath);
					
					//画像番号
					$date = date('Y-n-j');
					print("<p><img src=\"".$thumbPath."\" alt=\"thumb\"/></p>\n");
					$db->query("INSERT INTO MEDIA VALUES($maxValue, 1, '$path', '$date', '$thumbPath');");
					
					echo 'ファイルは正常にアップロードされました';
				}catch(RuntimeException $e){
					echo $e->getMessage();
				}//endof try catch
				$maxValue++;
			}//end of for
		}//end of function
		
		
		
		
		//For Upload Video
		function upLoadVideo($length){
			global $videoMime, $db;
			$m = $db->query("SELECT MAX(ID) FROM MEDIA;")->fetch();
			$maxValue = $m[0]+1;
			
			//ここからVIDEOファイルのアップロード処理
			for($i=0;$i<$length;$i++){
				try{
					if(!isset($_FILES['file']['error'][$i]) ||
					   !is_int($_FILES['file']['error'][$i])
					  ){
					 throw new RuntimeException('パラメータが不正です');
					}
					switch($_FILES['file']['error'][$i]){
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
			
					if($_FILES['file']['size'][$i] > 100000000){
						throw new RuntimeException('ファイルサイズが大きすぎます');
					}
					
					$finfo = new finfo(FILEINFO_MIME_TYPE);
					
					if($ext = array_search($finfo->file($_FILES['file']['tmp_name'][$i]),$videoMime,true)){
						if(
						!move_uploaded_file($_FILES['file']['tmp_name'][$i],$path=sprintf('./upload/Video/%s.%s',sha1_file($_FILES['file']['tmp_name'][$i]),$ext))
						){
							throw new RuntimeException('ファイル保存時にエラーが発生しました');
						}
					}
					else{
						throw new RuntimeException('ファイル形式が不正です。動画ファイルはmp4を使用してください');
					}
					
					chmod($path, 0644);
					//Thumbnail生成  http://ladyclea.blog.fc2.com/blog-entry-10.html
					$thumbPath = "./upload/Thumbnail/".basename($path, $ext)."jpg";
					//1分の1フレーム目をサムネイル化、、だと思う。
					$result=convThumbnail($path, $thumbPath, 1, 1, "200x150", $output);
					
					if($result==0){
					  echo "<video src='".$path."' poster='".$thumbPath."'>";
					}else{
					  echo "Error:".$result." 画像の取得に失敗しました。<br />";
					  echo $output."<br />";
					}
					
					$date = date('Y-n-j');
					//画像番号
					$db->query("INSERT INTO MEDIA VALUES($maxValue, 2, '$path', '$date', '$thumbPath');");
					
					echo 'ファイルは正常にアップロードされました';
				}catch(RuntimeException $e){
					echo $e->getMessage();
				}//endof try catch
				$maxValue++;
			}//end of for
		}//end of function
		
		
		
		
		
		//For upload Obj
		function upLoadObj($length){
			global $objMime,$db;
			$m = $db->query("SELECT MAX(ID) FROM MEDIA;")->fetch();
			$maxValue = $m[0]+1;
			
			//ここからIMAGEファイルのアップロード処理
			for($i=0;$i<$length;$i++){
				try{
					if(!isset($_FILES['file']['error'][$i]) ||
					   !is_int($_FILES['file']['error'][$i])
					  ){
					 throw new RuntimeException('パラメータが不正です');
					}
					switch($_FILES['file']['error'][$i]){
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
			
					if($_FILES['file']['size'][$i] > 20000000){
						throw new RuntimeException('ファイルサイズが大きすぎます');
					}
					
					$finfo = new finfo(FILEINFO_MIME_TYPE);
					
					if($ext = array_search($finfo->file($_FILES['file']['tmp_name'][$i]),$objMime,true)){
						if(
						!move_uploaded_file($_FILES['file']['tmp_name'][$i],$path=sprintf('./upload/tmp/%s.%s',sha1_file($_FILES['file']['tmp_name'][$i]),$ext))
						){
							throw new RuntimeException('ファイル保存時にエラーが発生しました');
						}
					}
					else{
						throw new RuntimeException('ファイル形式が不正です。ファイルはZipを使用してください');
					}
					chmod($path, 0644);
					
					//zipファイル解凍
					
					$zip = new ZipArchive();
					$res = $zip->open($path);
					$thumbPath;
					if($res == ture){
						for($j=0;$j<$zip->numFiles;$j++){
							$fileName = $zip->getNameIndex($j);
							if($fileName === "thumb.jpg"){
								$thumbPath = "./upload/Thumbnail/".basename($path,$ext)."jpg";
								$zip->extractTo("./upload/Thumbnail", array($fileName));
								rename("./upload/Thumbnail/thumb.jpg", $thumbPath);
							}
							else{
							$zip->extractTo("./upload/Obj", array($fileName));	
							}
							
							if(preg_match('/(\w+)\.obj/',$fileName)){		
								$objFile = $fileName;
							}
						}
						
						
					}
					$zip->close();
					unlink($path);//ファイル削除
					$path = "./upload/Obj/".$objFile;
					
					$date = date('Y-n-j');
					//画像番号
					$db->query("INSERT INTO MEDIA VALUES($maxValue, 3,'$path', '$date', '$thumbPath');");
					print"<p><img src='".$thumbPath."'/></p>";
					echo 'ファイルは正常にアップロードされました';
				}catch(RuntimeException $e){
					echo $e->getMessage();
				}//endof try catch
				$maxValue++;
			}//end of for
		}//end of function
		
		
		
		
		
		function convThumbnail($file_path, $img_path, $vframes=1, $ss=1, $size="160x128", &$output=null){
 			 $COMMAND_FFMPEG = "/usr/bin/ffmpeg";

  			//オプションの指定
  			$com_video = " -i " . $file_path;
  			$com_extension = " -f image2";
  			$com_vframes = " -vframes " . $vframes;
  			$com_ss = " -ss " . $ss;
  			$com_size = " -s " . $size;
  			$com_img = " -an -deinterlace " . $img_path;

  			//サムネイル作成
  			$command = $COMMAND_FFMPEG . $com_video . $com_extension . $com_vframes . $com_ss . $com_size . $com_img . " 2>&1";
			exec($command, $output, $result);

  			if($result!=0){
    			$output=$command;
  			}
  			return $result;
		}
	?>
</body>
</html>
