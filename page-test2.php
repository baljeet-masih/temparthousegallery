<?php 
/**
* Template Name: Test Page Template2
*/
?>
<html>
  <head>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://unpkg.com/konva@3.2.7/konva.min.js"></script>
    <meta charset="utf-8" />
    <title>Konva Drag and Drop Demo</title>
    <style>
		body {
			margin: 0;
			padding: 0;		
			background-color: #f0f0f0;
		}

		.prod-image-block, .upload-block, .upload-block {
			float: left;
			margin-right: 25px;
		}
		
		.hidden_link {
			display: none;
		}
		
		
		div.konvajs-content canvas {
			height: auto!important;
			max-width: 100%!important;
		}
		
		div.konvajs-content {
			height: auto!important;
			max-width: 100%!important;
		}
		
		#container {
			max-width: 100%;
		}
    </style>
  </head>
  <body>
	<div class="prod-image-block">	
		<h3>Prod image:</h3>
		<span style="float: left;">Art Size: 80x61 sm</span><br>
		<span style="float: left;">image 544x417 px</span><br>
		<img width="554" height="417" src="http://temparthousegallery.site/wp-content/uploads/2019/04/RebeccaGeorge-007-copyCROP.jpg" 
		class="wp-post-image" alt="" title="RebeccaGeorge-007 copyCROP" 
		data-caption="" 
		data-src="http://temparthousegallery.site/wp-content/uploads/2019/04/RebeccaGeorge-007-copyCROP.jpg" 
		data-large_image="http://temparthousegallery.site/wp-content/uploads/2019/04/RebeccaGeorge-007-copyCROP.jpg" 
		data-large_image_width="554" data-large_image_height="417" 
		srcset="http://temparthousegallery.site/wp-content/uploads/2019/04/RebeccaGeorge-007-copyCROP.jpg 554w, http://temparthousegallery.site/wp-content/uploads/2019/04/RebeccaGeorge-007-copyCROP-300x226.jpg 300w, http://temparthousegallery.site/wp-content/uploads/2019/04/RebeccaGeorge-007-copyCROP-393x295.jpg 393w" 
		sizes="(max-width: 554px) 100vw, 554px" style="max-width: 200px; height: initial;">
	</div>
	
	<div class="upload-block">	
		<h3>Background image:</h3>
		<span style="float: left;">Wall Size: 400x230 sm</span><br>
		<span style="float: left;">image 1549x891 px</span><br>
		<input type="file" onchange="previewFile()"><br>
		<img id="background-img" src="" style="height: 200px;" alt="Image preview...">
		<br>
		<input id="btn-merge" class="" type="button" value="Merge!" />
	</div>
	
	<div class="upload-block">
		<h3>Result image:</h3>		
		<button type="button" id="save_me">Save img</button>
		<img src="#" alt="test" id="merged-image" style="display: block; max-width: 200px; max-height: 200px; border: 1px solid green;">
		
		<div class='ress_links'>
		</div>
	</div>
		
    <div id="container" style="float: left;"></div>
    <script>
		$ = jQuery.noConflict();
	
		// file upload preview function
        function previewFile(){
			var preview = document.getElementById('background-img'); //selects the query named img
			var file    = document.querySelector('input[type=file]').files[0]; //sames as here
			var reader  = new FileReader();

			reader.onloadend = function () {
				preview.src = reader.result;
				$('#btn-merge').show();
			}

			if (file) {
				reader.readAsDataURL(file); //reads the data as a URL
			} else {
				preview.src = "";
			}
        }
	
		
		function merge_photo(){
			var img = document.getElementById('background-img'); 
			var width = img.naturalWidth ;
			var height = img.naturalHeight ;

			console.log(width);
			console.log(height);

			stage = new Konva.Stage({
				container: 'container',
				width: width,
				height: height,
				fill: '#00D2FF',
			});

			var layer = new Konva.Layer();
			var layer2 = new Konva.Layer();		

			
			//-----PROD_IMAGE------
			wall_width = 400;//---from_page
			wall_height = 230;//---from_page
			wall_width_px = 1549;//---from_image_props
			wall_height_px = 891;//---from_image_props
			
			art_width = 80;//---from_page
			art_height = 61;//---from_page
			art_width_px = 544;//---from_image_props
			art_height_px = 417;//---from_image_props
			
			
			var prodimageObj = new Image();
			prodimageObj.src = $('.wp-post-image').attr('src');
				
			wall_width = 400;//---from_page
			wall_height = 230;//---from_page
			wall_width_px = 1549;//---from_image_props
			wall_height_px = 891;//---from_image_props
			//----------------------------------------
			art_width = 80;//---from_page
			art_height = 61;//---from_page
			art_width_px = 544;//---from_image_props
			art_height_px = 417;//---from_image_props
			
			wall_proportion = wall_width / wall_width_px;//---0.2583
			art_proportion = art_width / art_width_px;//---0.1444

			canvas_art_size_w = art_width / wall_proportion;//309.7px
			canvas_art_size_h = art_height / wall_proportion;//***
			
			console.log('canvas_art_size_w '+canvas_art_size_w);
			console.log('canvas_art_size_h '+canvas_art_size_h);
			
			
			prodimageObj.onload = function() {
				var prod_img = new Konva.Image({
					x: 0,
					y: 0,
					width: canvas_art_size_w,
					height: canvas_art_size_h,
					image: prodimageObj,
					draggable: true,
					visible: true, 
				});
				
				//add cursor styling
				prod_img.on('mouseover', function() {
					document.body.style.cursor = 'pointer'; 
				});
				prod_img.on('mouseout', function() {
					document.body.style.cursor = 'default';
				});
				
				layer2.add(prod_img);
				layer2.draw();
			};						
			//-----PROD_IMAGE------		
						
			//---CANVAS BACKGROUND---
			var imageObj = new Image();
			imageObj.src = $('#background-img').attr('src');
			
			imageObj.onload = function() {
				var background = new Konva.Image({
					x: 0,
					y: 0,
					width: stage.width(),
					height: stage.height(),
					image: imageObj,
					draggable: false,
					visible: true, 
				}); 
				
				layer.add(background);
				layer.draw();
			};			
			//---CANVAS BACKGROUND---

			
			stage.add(layer);		
			stage.add(layer2);	
			stage.draw();	

			
			//---SAVE IMAGE---
			$('#save_me').click(function(){			
				if (typeof stage !== 'undefined') {
					var mergedImage = stage.toDataURL({
					  pixelRatio: 0.5 // or other value you need
					});	
					jQuery( '#merged-image' ).attr( 'src', mergedImage );

					
					var link = document.createElement('a');
					link.className = "temp_donwload"; 
					link.download = 'stage.png';
					link.href = mergedImage;
					console.log(mergedImage);
					link.text = 'download me!';
					
					//links_to_save.push(link);		
					$('.ress_links a').remove();					
					$('.ress_links').append(link);
					link.click();
					delete stage; //return false
				}				
			});
			//---SAVE IMAGE--- 
		}

			
		 
		
		//MERGE TWO PHOTOS
		$('#btn-merge').click(function(){
			merge_photo();
		});
    </script>
  </body>
</html>