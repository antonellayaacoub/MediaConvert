<!DOCTYPE html>
<html lang="en">

<head>
  <title>MediaConvert</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-BmbxuPwQa2lc/FVzBcNJ7UAyJxM6wuqIj61tLrc4wSX0szH/Ev+nYRRuWlolflfl" crossorigin="anonymous">
    </head>
    <style>
    #progress {
    width: 0;
    height: 10px;
    background: blue;
    text-align: center;
    line-height: 30px;
    transition-duration: 5s;
    transition-timing-function: ease;
    }
    </style>
    <body>
    <div class="container">
    <h1>Using Amazon Elementary MediaConvert</h1>
    <hr>
    <form action="/" method="post" enctype="multipart/form-data" id="form">
      {{ csrf_field() }}
      <div class="row mb-3">
        <label for="video" class="col-sm-2 col-form-label">Select video to upload:</label>
        <div class="col-sm-3">
          <input type="file" class="form-control" name="video" id="video" accept="video/*" required />
        </div>
        <div class="col-sm-1">
          <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" id="flexSwitchCheckChecked" name="mute" value="1">
            <label class="form-check-label" for="mute">Mute</label>
          </div>
        </div>
      </div>
      <div class="row mb-3">
        <label for="video2" class="col-sm-2 col-form-label">Stitch another video:</label>
        <div class="col-sm-3">
          <input type="file" class="form-control" name="video2" id="video2" accept="video/*" />
        </div>
      </div>
      <div class="row mb-3">
        <label for="audio" class="col-sm-2 col-form-label">Add audio:</label>
        <div class="col-sm-3">
          <input type="file" class="form-control" name="audio" accept="audio/*" />
        </div>
      </div>
      <div class="row mb-3">
        <label for="resolution" class="col-sm-2 col-form-label">Choose Resolution:</label>
        <div class="col-sm-3">
          <select class="form-select" aria-label="Default select example" name="resolution">
            <option value="720p">720p</option>
            <option value="1080p">1080p</option>
          </select>
        </div>
      </div>
      <div class="row mb-3">
        <label for="scaling" class="col-sm-2 col-form-label">Choose Scaling:</label>
        <div class="col-sm-3">
          <select class="form-select" aria-label="Default select example" name="scaling">
            <option value="DEFAULT">Default</option>
            <option value="STRETCH">Stretch</option>
          </select>
        </div>
      </div>
      <div class="row mb-3">
        <label for="watermark" class="col-sm-2 col-form-label">Watermark:</label>
        <div class="col-sm-3">
          <input type="file" class="form-control" name="watermark" accept="image/*" />
        </div>
      </div>
      <input type="submit" class="btn btn-primary" value="Upload Video" name="submit" onclick="speed()">
    </form>
    <br />
    <div id="progress"><div>
    <br />
    <div id="test">0<div>
</div>
<script type="text/javascript">
function speed(){
  var fileInput =  document.getElementById("video");
            var fileSize= fileInput.files[0].size;
            var userImageLink = 
"https://media.geeksforgeeks.org/wp-content/cdn-uploads/20200714180638/CIP_Launch-banner.png";
            var time_start, end_time;
            
            // The size in bytes
            var downloadSize = 5616998;
            var downloadImgSrc = new Image();
  
           downloadImgSrc.onload = function () {
                end_time = new Date().getTime();
             //   displaySpeed();
             var timeDuration = (end_time - time_start) / 1000;
                var loadedBits = downloadSize * 8;
                /* Converts a number into string
                   using toFixed(2) rounding to 2 */
                var bps = (loadedBits / timeDuration);
                var time= (fileSize/ bps) ;
                var bar =0;
                var progressBar = document.getElementById("progress");
                var id = setInterval(function(){
                 bar++;
            progressBar.style.width = bar + "%"
            document.getElementById("progress").innerHTML=bar;
        if (bar >=100){
        clearInterval(id)
          }
          }, time)
            };
            
        
            time_start = new Date().getTime();
            downloadImgSrc.src = userImageLink;
            //document.write("time start: " + time_start);
            //document.write("<br>");
           // function displaySpeed() {
            
               
            }
            
        </script>
  </body>
</html>