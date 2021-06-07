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
    <div class="container" style="width:50%;">
    <h1>Using Amazon Elementary MediaConvert</h1>
    <hr>
   <form  action="/store" method="post" id="form" enctype="multipart/form-data">
     {{csrf_field() }}
     <div class="form-group">
     <label for="video">Select video to upload:</label>
     <input type="file" class="form-control" name="video" id="video" accept="video/*" required />
  </div>
  <div class="form-check">
  <input class="form-check-input" type="checkbox" id="flexSwitchCheckChecked" name="mute" value="1">
   <label class="form-check-label" for="mute">Mute</label>
  </div>
  <div class="form-group">
  <label for="audio" >Add audio:</label>
          <input type="file" class="form-control" name="audio" accept="audio/*" />
  </div>
 
  <div class="form-group">
        <label for="resolution">Choose Resolution:</label>
          <select class="form-select" aria-label="Default select example" name="resolution">
            <option value="720p">720p</option>
            <option value="1080p">1080p</option>
          </select>
      </div>
      <div class="form-group">
        <label for="scaling">Choose Scaling:</label>
          <select class="form-select" aria-label="Default select example" name="scaling">
            <option value="DEFAULT">Default</option>
            <option value="STRETCH">Stretch</option>
          </select>
      </div>
      <div class="form-group">
        <label for="watermark" >Watermark:</label>
          <input type="file" class="form-control" name="watermark" accept="image/*" />
        </div>
        <br>
    <div class="form-group">
                <input type="button" id="addVideo" class="btn btn-primary" value="Add video"/>
                <input type="button" id="removeVideo" class="btn btn-primary" value="Remove video" disabled />
                <input type="number" id="count" name="count" value="0" style="display: none;">
            </div>
            <br>
            <div class="videos">
                
                </div>
                <br>
      <input type="submit" class="btn btn-primary" id="submit" value="Upload Video" name="submit">
    </form>
    <br>
    <input type="submit" class="btn btn-primary" id="cancel" value="Cancel" name="cancel" disabled>
    <p>
    <div  id="progress">
</div>
</p>
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

    $("form").on('submit', function (e) {
   //ajax call here
   $("#submit").prop('disabled', true);
   $("#cancel").prop('disabled', false);
   $('#progress').html("<button class='btn btn-primary' type='button' disabled> <span class='spinner-border spinner-border-sm'role='status' aria-hidden='true'></span> Uploading...</button>");
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
             pathVideo=data.pathVideo;
             pathVideos=data.pathVideos;
             pathWatermark=data.pathWatermark;
             pathAudio=data.pathAudio;
            console.log(data);
            $('#progress').html("<button class='btn btn-primary' type='button' disabled> <span class='spinner-border spinner-border-sm'role='status' aria-hidden='true'></span> Transcoding...</button>");
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
            $(".container").html(data);
            },
            error: function(data){
            console.log(data);
            $('#progress').html("<div class='alert alert-danger' role='alert'>Something happened, please try again!</div>");
            }
            });
            },
            error: function(data){
            console.log(data);
            console.log(data.error);
            $('#progress').html("<div class='alert alert-danger' role='alert'>Something happened, please try again!</div>");
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
     $("#progress").html("");
     }
     if(f2){
     f2.abort();
     $("#progress").html("");
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
  $("#removeVideo").prop('disabled', false);
  $('.videos').append( "<div class='form-group' id='"+i+"'><label for='addVideo' >"+i+":</label><input type='file' class='form-control' name='video"+i+"' id='video"+i+"' accept='video/*' /></div>");
  });
  $('#removeVideo').click(function(){
  var i= document.getElementById("count").value;

  $("#"+i).remove();
  document.getElementById("count").stepDown();
    i= document.getElementById("count").value;
  if(i==0){
    $("#removeVideo").prop('disabled', true);
  }
  });

</script>   
  </body>
</html>