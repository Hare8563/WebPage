<!DOCTYPE html>
<html>
  <head>
    <link rel="stylesheet" href="./css/mediaboxAdvBlack21.css" type="text/css" media="screen">
    <style type="text/css">p { font-size: 60px; }</style>
    <meta charset="UTF-8">
      <title>CONTENTS</title>
      <style>
       body{
         color: #FFFFFF;
         background-color: #000000;
        } 
         header{
          text-align: center;
          width: 100%;
        }
     　.gallary{
          width: 100%;
          position: relative;
          margin: 0 auto;
        }
       .gallary-item{
          float: center;
          position: relative;
        }

        .btn{
            background: -moz-linear-gradient(top, #BFD9E5, #63B0CF 50%, #0080B3 50%, #09C);
            background: -webkit-gradient(linear, left top, left bottom, from(#BFD9E5), color-stop(0.5, #63B0CF), color-stop(0.5, #0080B3), to(#09C));
            text-align: center;
            font-weight: bold;
            border-top: 3px solid #DDF;
            border-left: 3px solid #DDF;
            border-right: 3px solid #AAC;
            border-bottom: 3px solid #AAC;
            color: #DEE;
            width: 100px;
            padding: 10px 0;
        }
        .btn:active{
            background: -moz-linear-gradient(top, #BFD9E5, #63B0CF 50%, #0080B3 50%, #09C);
            background: -webkit-gradient(linear, left top, left bottom, from(#BFD9E5), color-stop(0.5, #63B0CF), color-stop(0.5, #0080B3), to(#09C));
            text-align: center;
            font-weight: bold;
            border-top: 3px solid #AAC;
            border-left: 3px solid #AAC;
            border-right: 3px solid #DDF;
            border-bottom: 3px solid #DDF;
            color: #DEE;
            width: 100px;
            padding: 10px 0;

        }
      </style>
      <script src="./js/mootools-1.2.6-core-yc.js" type="text/javascript"></script>
      <script src="./js/swfobject.js" type="text/javascript"></script>
      <script src="./js/mediaboxAdv-1.3.4b.js" type="text/javascript"></script>
      <script src="./js/jquery-1.10.2.min.js" type="text/javascript"></script>
      <script src="./js/mooMasonry.js" type="text/javascript"></script>
    <script>
       $(function(){
          var config = {                      
              singleMode: true,
              isAnimated: true,
              columnWidth: 210,
              itemSelector: ".gallary-item",
              resizeable: true,
              gutter: 5 
              //appendedContent: '.new_content'
          };
          document.id('gallary').masonry(config);
       });
    </script>
   </head>
   <body>
     <!-- Start your code here -->
      <!--どのコンテンツを表示するかを判別するためのフィールド
      ALL: 0
      VIDEO: 1
      IMAGE: 2
      OBJ: 3 
      -->
      <input value="0" name="contentsValue" type="hidden">
      <header>
      <h1>CONTENTS PAGE</h1>
        <a href="contents.php" class="btn">ALL</a>
        <a href="contents.php?filter=1" class="btn">IMAGE</a>
        <a href="contents.php?filter=2" class="btn">VIDEO</a>
        <a href="contents.php?filter=3" class="btn">OBJECT</a>
        <a href="../index.html" class="btn">BACK TO TOP</a>
	</header>
      <br>
      <hr>
    <div id="gallary">
    <?php
	if(isset($_GET['filter'])){
    		$filter = $_GET['filter'];
	}
	else{
	$filter = 0;
	}
	try{
    	$db= new PDO('sqlite:mediaContents.db');
    	}catch(Exception $e){
    		print('エラーメッセージ:'.$e->getMessage());
    	}
    	
    	$result=$db->query("SELECT * FROM MEDIA;")->fetchAll(PDO::FETCH_NUM);

      for($j=count($result)-1; $j >=0;$j--){
          $item = $result[$j];
	if($filter==0){
          print("<a class=");
          print("\"gallary-item\"");
          if($item[1]==1){
            print(" href=\"$item[2]\" rel=\"lightbox [set] \" title=\"ImageFile\"><img src=\"$item[4]\" alt=\"$item[0]\"/></a>\n");
          }
          else if($item[1]==2){
            //Videoの処理
            print(" href=\"$item[2]\" rel=\"lightbox [flash 960 600] \" title=\"VIDEOFILE\"><img src=\"$item[4]\" alt=\"$item[0]\"/></a>\n");
          }
          else{
            //Objectの処理
            print(" href=\"fbxPlayer.php?dst=$item[2]\" rel=\"lightbox [inline 960 600] \" title=\"OBJECTFILE\"><img src=\"$item[4]\" alt=\"$item[0]\"/></a>\n");
          }
	}
	if($filter==1){
	 if($item[1]==1){
	print("<a class=");
        print("\"gallary-item\"");
	print(" href=\"$item[2]\" rel=\"lightbox [set] \" title=\"ImageFile\"><img src=\"$item[4]\" alt=\"$item[0]\"/></a>\n");
         
	 }
	}
	else if($filter==2){
         if($item[1]==2){
	print("<a class=");
        print("\"gallary-item\"");	
	    print(" href=\"$item[2]\" rel=\"lightbox [flash 960 600] \" title=\"VIDEOFILE\"><img src=\"$item[4]\" alt=\"$item[0]\"/></a>\n");
	 }	
	}
	else if($filter==3){
	 if($item[1]==3){
	print("<a class=");
        print("\"gallary-item\"");
	print(" href=\"fbxPlayer.php?dst=$item[2]\" rel=\"lightbox [inline 960 600] \" title=\"OBJECTFILE\"><img src=\"$item[4]\" alt=\"$item[0]\"/></a>\n");
	 }
	}
        }  
    ?>
    </div>
  </body>
</html>
