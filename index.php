
<!DOCTYPE html>
<html>
<head>
	<title>Upload test</title>
	<script src="https://code.jquery.com/jquery-1.12.4.min.js"
  integrity="sha256-ZosEbRLbNQzLpnKIkEdrPv7lOy9C27hHQ+Xp8a4MxAQ="
  crossorigin="anonymous"></script>
  <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
  <link rel="stylesheet" href="font-awesome-4.7.0/css/font-awesome.min.css"/>
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
  <style type="text/css">
      .uploader{
          width: 100px;
          border:2px dashed #777;
          background:#eaeaea;
          width: 100px;
          height: 100px;
          position:relative;
          color:dodgerblue
      }
      .uploader .icon{
          position:absolute;
          top:10px;
          left:6px;
          z-index:0;
      }
      input[type=file] {
          
        display: inline-block;
        width: 100px;
        padding: 100px 0 0 0;
        height: 100px;
        overflow: hidden;
        -webkit-box-sizing: border-box;
        -moz-box-sizing: border-box;
        box-sizing: border-box;
        position:relative;
        z-index:1;
        
      }
      #progressbar{
          position:relative;
      }
      .progress-label{
            
            display: none;
            position: absolute;
            left: 10px;
            top: 8px;
      }
  </style>
</head>
<body>
<form id="uploader" action="upload.php" method="POST" enctype="multipart/form-data" >
<!-- <input type="hidden" name="<?php echo ini_get("session.upload_progress.name"); ?>" value="123" />-->
<div id="progressbar">
    <div class="progress-label">Loading...</div>
</div>
<br/>
<div class="uploader">
    <i class="fa fa-cloud-upload fa-5x icon"></i>
   <input type="file" name="file1" />
</div>
<!-- <iframe id="upload_target" name="upload_target" src="#" style="width:0;height:0;border:0px solid #fff;"></iframe>  -->
<!-- <input type="submit" />-->
</form>
<br/>

<script type="text/javascript">

	var intervalPromise = null;
	$(function(){

		
		var fileObject  = null;
                var progressbar = $("#progressbar");
                var progressbarLabel = progressbar.find('.progress-label');
		var loaded =  0;
                var total  =  0;
		var incr = (64*1024);
                var bufferWrite = 0;
                var speed = 0;

		$("input[type=file]").change(function(event) {

		  	$.each(event.target.files, function(index, file) {
                            $.ajax({
                               method:'post',
                               url:'check.php',
                               data:{filename:file.name},
                               beforeSend:function(){
                                   $("#progressbar").progressbar({
                                        value:0
                                        
                                    });
                                    progressbarLabel.show();
                                    progressbarLabel.text(file.name+" Loading...");
                               },
                               success:function(e){
                                    loaded = parseInt(e);
                                   
                            
                                    var reader = new FileReader();
                                    total = file.size;
                                    total = total+incr;
                                    reader.onload = function(event) {  
                                            if(loaded <= total){

                                                object = {};
                                                object.filename = file.name;
                                                object.data = event.target.result;

                                                $("#progressbar").progressbar({
                                                        value: Math.round((loaded/total)*100),
                                                        change: function() {
                                                            progressbarLabel.show();
                                                            progressbarLabel.html( file.name+" uploaded "+progressbar.progressbar( "value" ) + "% -  [<span id='speed'>"+speed+"</span>]" );
                                                        },
                                                });

                                                $.ajax({
                                                        method:'post',
                                                        url:'upload.php',
                                                        data:object,
                                                        error:function(e){
                                                            
                                                            progressbarLabel.html("unable to uploaded some problem occured. please try later");
                                                        },
                                                        success:function(e){
                                                            if(e !== "" && e>0){
                                                                var tempIncr = loaded+incr;
                                                                var blob = file.slice(loaded,tempIncr);
                                                                loaded = tempIncr;
                                                                bufferWrite += parseInt(e);
                                                                reader.readAsDataURL(blob);
                                                            }else{
                                                                
                                                                progressbarLabel.html("unable to uploaded some problem occured. please try later");
                                                            }
                                                        }
                                                });


                                            }else{
                                                loaded = total;

                                            }

                                            if(loaded==total){
                                                   // window.location.reload();
//                                                $("#result").progressbar({
//                                                        value: 0
//                                                });
                                            }
                                        };  


                                        var startAt = (loaded>0)? loaded : 0;

                                        loaded =  (loaded>0)? (parseInt(loaded)+parseInt(incr)) : incr ;
//                                        console.log(startAt,loaded,total);
                                        
                                        var blob = file.slice(startAt,loaded);
                                        reader.readAsDataURL(blob);
                                        
                                        var bufferWriteInterval = setInterval(function(){
                                            if(!bufferWrite){
                                                clearInterval(bufferWriteInterval);
                                            }
                                            if(bufferWrite){
                                                speed = Math.round(bufferWrite/1024);
                                                console.log((speed%1024));
                                                if((speed%1024)){
                                                    speed = Math.round(speed/1024);
                                                    speed = speed + "Mbps";
                                                    $("#speed").text(speed);
                                                }else{
                                                    speed = speed + "Kbps";
                                                    $("#speed").text(speed);
                                                }
                                            }
                                            
                                            bufferWrite = 0;
                                        },1000);

                                    }
                            });
                                    

		  	});

		});

		/*var loadProgress = function(){
			$.ajax({
				url:'progress.php',
				success:function(e){
					var data = $.parseJSON(e);
					var percentage = 0;
					
					var contentLength = (data.upload_progress_123 != undefined)? data.upload_progress_123.content_length : null;
						var byteProcessed = (data.upload_progress_123 != undefined)? data.upload_progress_123.bytes_processed : null;
						
						if(contentLength != null && byteProcessed != null){
							percentage = Math.round((byteProcessed/contentLength)*100);
						}

						if(contentLength == byteProcessed){
							clearInterval(intervalPromise);
						}
							
						console.log(data.upload_progress_123);
						$("div#result").progressbar({
					      value: percentage
					    });
							
					
					
				}
			});
		}*/
	});
</script>
</body>
</html>
