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
        .container{
          width: 100%;
          position: relative;
        }
     　.gallery{
         margin : 0;
         padding: 0;
        }
       .gallery-item{
          float: left;
          position: relative;
          list-style: none;
          margin-top: 10px;
        }
       .gallery-item.is-loading{
         opacity: 0;    
        } 
        .filler{
          float: left;
          position: relative;
        }
      </style>
      <script src="./js/mootools-1.2.6-core-yc.js" type="text/javascript"></script>
      <script src="./js/mediaboxAdv-1.3.4b.js" type="text/javascript"></script>
      <script src="./js/jquery-1.10.2.min.js" type="text/javascript"></script>
      <script src="./js/mason.js" type="text/javascript"></script>
      <script>
      $(function(){
          var config = {
            itemSelector: '.gallery-item', //整理される要素のclassを指定
            ratio: 1.3, //The ratio to base all element sizes
            sizes: [  //the sizes to use for elements
                [1,1]
                [1,2]
                [1,1]
            ]
            //columns:  //The defined breakpoints
            promoted:[  //Array of specific sized elements
              [1,3, 'wide'] 
              [2,3,'big'] 
            ] 
            filler: { itemSelector: '.filler', filler_class: 'custom_filler' }, 
            gutter: 3, //gutter the element
            layout: 'fluid'
          }
          $('#container').mason(config);
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
      <button onclick="document.getElementsByName('contentsValue').value = '0';">ALL</button>
      <button onclick="document.getElementsByName('contentsValue').value = '1';">VIDEO</button>
      <button onclick="document.getElementsByName('contentsValue').value = '2';">IMAGE</button>
      <button onclick="document.getElementsByName('contentsValue').value = '3';">OBJECT</button>
      </header>
      <hr>
    <div id="container" class="centered">
    <?php
    	try{
    	$db= new PDO('sqlite:Report.db');
    	}catch(Exception $e){
    		print('エラーメッセージ:'.$e->getMessage());
    	}
    	
    	$result=$db->query("SELECT * FROM Image;")->fetchAll(PDO::FETCH_NUM);

      for($j=count($result)-1; $j >=0;$j--){
          $item = $result[$j];
          print("<a class=\"gallery-item\" href=\"$item[1]\" rel=\"lightbox [set] \" title=\"ImageFile\"><img src=\"$item[1]\" alt=\"$item[0]\" width=\"260px\"/></a>\n");
        }  
    ?>
    </div>
  </body>
</html>