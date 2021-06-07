<!DOCTYPE html>
<html lang="en">

<head>
  <title>MediaConvert</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-BmbxuPwQa2lc/FVzBcNJ7UAyJxM6wuqIj61tLrc4wSX0szH/Ev+nYRRuWlolflfl" crossorigin="anonymous">
    </head>
       <style>
        .progress { position:relative; width:100%;  }
        .bar { background-color: #0d6efd; width:0%; height:20px; }
        .percent { position:absolute; display:inline-block; left:50%; color: #040608;}
        #status { color:green ;}
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
   
      <input type="submit" class="btn btn-primary" value="Upload Video" name="submit" >
    </form>
    <br/>
    <div class="progress">
                        <div class="bar"></div >
                        <div class="percent">0%</div>
                    </div>
    <h2 id="status"> </h2>
   
</div>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7/jquery.js"></script>
<script src="http://malsup.github.com/jquery.form.js"></script>
 
<script type="text/javascript">
 
    (function() {
    var progress=$('.progress');
    var bar = $('.bar');
    var percent = $('.percent');
    var status = $('#status');
    var i=0;
    var id=0;
    var p=0;
    $('form').ajaxForm({
        beforeSend: function() {
            status.empty();
            var percentVal = '0%';
            var posterValue = $('input').fieldValue();
        },
        uploadProgress: function(event, position, total, percentComplete) {
         //progress[0].style.display = "";
         // bar[0].style.display = "block";
          //percent[0].style.display = "inline-block";
     
           id = setInterval(function(){

            if (i <=99 && p==0){
            var percentVal = i + '%';
            bar.width(percentVal)
            percent.html(percentVal);
            i++;
            console.log(percentVal);
            }
        if (i >=99 || p==1){
        clearInterval(id)
          }
          }, 2000);
        },
        success: function() {
        },
        complete: function(xhr) {
          p=1;
             bar.width('100%');
            percent.html('100%');
            status.html('Uploaded Successfully');
            //progress[0].style.display = "none";
            //bar[0].style.display = "none";
            //percent[0].style.display = "none";
           // alert('Uploaded Successfully!');
            //window.location.href = "/";
        }
    });
     
    })();
</script>
  </body>
</html>