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
   
      <input type="submit" class="btn btn-primary" id="submit" value="Upload Video" name="submit">
    </form>
    <br/>
    <div id="progress">-1</div>

</div>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7/jquery.js"></script>
<script src="http://malsup.github.com/jquery.form.js"></script>
 
<script type="text/javascript">
 
    // (function() {
    //   var id=0;
    //   var i=0;
    // $('form').ajaxForm({
//         beforeSend: function() {
//         },
//         uploadProgress: function(event, position, total, percentComplete) {
//              id=  setInterval(function(){
//                 $.getJSON('/progress', function(data) {
//                     $('#progress').html(data[0]);
//                     console.log(data[0]);
//                     console.log("1");
//                 });
//             },1000);
//             // id = setInterval(function(){
//             //   if (i <=99 ){
//             //   var percentVal = i + '%';
//             //   i++;
//             //   console.log(percentVal);
//             // }}, 1000);
//         },
// success: function() {
//         },
//         complete: function (data){
//            // alert('File has been uploaded successfully');
//            clearInterval(id);
//             console.log(data);
//         }
//     });
     
//     })();
$(document).ready(function() {
    $.ajaxSetup({
headers: {
'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
}
});
function f1(form){
  var formData = new FormData(form);
            $.ajax({
            type:'POST',
            async: true,
            url: "{{ url('/store')}}",
            data: formData,
            cache:false,
            contentType: false,
            processData: false,
            success: (data) => {
           // form.reset();
            console.log(data);
            },
            error: function(data){
            console.log(data);
            }
            });
}
function f2(){
    $.ajax({
            type:'GET',
            async: true,
            url: "{{ url('/progress')}}",
            success: (data) => {
              $('#progress').html(data[0]);
                    // for(var i=0;i<=15;i++){
                    console.log(data[0]);
                    //}
               // setTimeout(f2, 1000); 
            },
            error: function(data){
            console.log(data);
            id=100;
            }
            });

                // $.getJSON('/progress', function(data) {
                //     $('#progress').html(data[0]);
                //     console.log(data[0]);
                //     console.log("1");
                // });
}
    $("form").on('submit', function (e) {
   //ajax call here
    setInterval(function(){
      $('#progress').html( "{{ Session::get('progress') }}" );
       console.log( "{{ Session::get('progress') }}" );
          }, 1000);

            f1(this);

            return false;

   //stop form submission
   e.preventDefault();
          
 });    
});
</script>   
  </body>
</html>