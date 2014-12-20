var xhr=null;
if (window.XMLHttpRequest)xhr=new XMLHttpRequest();
else if(window.ActiveXObject)
    try {xhr=new ActiveXObject("Msxml2.XMLHTTP");}
    catch(e){xhr=new ActiveXObject("Microsoft.XMLHTTP");}
xhr.open("GET","mediabox/mediaboxAdv-1.3.4b.js",false);xhr.send("");eval(xhr.responseText);
xhr.open("GET", "mediabox/mootools-1.2.6-core-yc.js",false);xhr.send("");eval(xhr.responseText);

$(function (){
	$('#gallery').each(function (){
		
	    var $container =$(this);
 	    $container.masonry({
		         columnWidth:280,
			     gutter: 10,
			     isFitWidth: true,
			     itemSelector:'.gallery-item'
			    });

		//$.getJSON('../json/json_data.json',function(data){
	          $.getJSON('./json/json_data.json',function(data){	
		        var elements =[];
	
			$.each(data,function(i,item){
				    var itemHTML='<li class="gallery-item is-loading">'+'<a href="'+item.image+'" rel="lightbox[set]">'+'<img src="'+item.image+'"alt="'+item.id+'"width="'+'260px">'+'</a>'+'</li>';
				    elements.push($(itemHTML).get(0));
				});
			    
			    $container.append(elements);
			    
			    $container.imagesLoaded(function(){
				    $(elements).removeClass('is-loading');
				    $container.masonry('appended',elements);
				});
			    });
		    });
	    });