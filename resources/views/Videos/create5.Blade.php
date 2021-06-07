<!DOCTYPE html>
<html lang="en">

<head>
  <title>MediaConvert</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-BmbxuPwQa2lc/FVzBcNJ7UAyJxM6wuqIj61tLrc4wSX0szH/Ev+nYRRuWlolflfl" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    </head>
    <body>
    <div class="container">
    <h1>Using Amazon Elementary MediaConvert</h1>
    <hr>
   <form  action="/store" method="post" id="form" enctype="multipart/form-data">
     {{csrf_field() }}
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
      <div class="videoss">
                
            </div>
    <div class="floor_container">
                <a id="addVideo" >Add Video</a>
                <input type="number" id="count" name="count" value="0" style="display: none;">
            </div>
            <br>
      <input type="submit" class="btn btn-primary" id="submit" value="Upload Video" name="submit">
    </form>
    <br>
    <input type="submit" class="btn btn-primary" id="cancel" value="Cancel" name="cancel" disabled>
<div id="progress"></div>
<br>
</div>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7/jquery.js"></script>
 
<script type="text/javascript">
var pathVideo;
 $(document).ready(function() {
    $.ajaxSetup({
headers: {
'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
}
});
var f1;
var f2;
$(".preview").hide();

    $("form").on('submit', function (e) {
   //ajax call here
   $("#submit").prop('disabled', true);
   $("#cancel").prop('disabled', false);
   $('#progress').html("Uploading");
  var formData = new FormData(this);
           f1= $.ajax({
            type:'POST',
            async: true,
            url: "{{ url('/store')}}",
            data: formData,
            cache:false,
            contentType: false,
            processData: false,
            success: (data) => {
           // form.reset();
             pathVideo=data.pathVideo;
             pathVideos=data.pathVideos;
             pathWatermark=data.pathWatermark;
             pathAudio=data.pathAudio;
            console.log(data);
            $('#progress').html("transcoding");
    formData.append('pathVideo', pathVideo);
    formData.append('pathVideos', pathVideos);
    formData.append('pathWatermark', pathWatermark);
    formData.append('pathAudio', pathAudio);
    f2=$.ajax({
            type:'POST',
            async: true,
            url: "{{ url('/transcode')}}",
            data: formData,
            cache:false,
            contentType: false,
            processData: false,
            success: function (data) {
           // form.reset();
           // console.log(data);
            $(".container").html(data);
            //$('#progress').html('<form  action="/transcode" method="get" id="form2" enctype="multipart/form-data"><input type="submit" class="btn btn-primary" id="preview" value="Preview" name="preview"></form>');
            // var pathMp4=data.mp4;
            // var pathWebm=data.webm;
            // var vid = $('.preview');
            // var mp4 = document.getElementById('mp4');
            // var webm = document.getElementById('webm');
            // mp4.setAttribute('src', pathMp4);
            // webm.setAttribute('src', pathWebm);
            // vid.load();
            },
            error: function(data){
            console.log(data);
            $('#progress').html("Error2");
            }
            });
            },
            error: function(data){
            console.log(data);
            console.log(data.error);
            $('#progress').html("Error1");
            }
            });
   //stop form submission
   e.preventDefault();         
 });    
 $("#cancel").click(function() {
    $("#submit").prop('disabled', false);
    $("#cancel").prop('disabled', true);
     if(f1){
     f1.abort();
     }
     if(f2){
     f2.abort();
     }
    });
});
function del(){
$.ajax({
            type:'POST',
            async: true,
            url: "{{ url('/delete')}}",
            data:{pathVideo: pathVideo},
            success: (data) => {
           // form.reset();
            console.log(data);
            window.location.reload(true);
            },
            error: function(data){
            console.log(data);
            }
            });
 
}
$('#addVideo').click(function(){
  document.getElementById("count").stepUp();
  var i= document.getElementById("count").value;
  $('.videoss').append( "<input type='file' class='form-control' name='video"+i+"' id='video"+i+"' accept='video/*' /> </div></div>");
  });

</script>   
  </body>
</html>