$(function (){
	$('#gallary').each(function (){
		
		var $container =$(this);
		$container.masonry({
			columnWidth: 230,
			    gutter: 10,
			    itemSelector: '.gallery-item'
			    });
		
		//$.getJSON('../json/json_data.json',function(data){
	          $.getJSON('../json_data.json',function(data){	
		        var elements =[];
			alert("Hello");
			$.each(data,function(i,item){{
				    var itemHTML='<li class="gallery-item is loading">'+'<a href="' +item.images+ '">'+'<img src="' +item.images+'"alt="' +item.id+'">'+'</a>'+'</li>';
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