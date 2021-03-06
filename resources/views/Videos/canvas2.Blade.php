<!doctype html>

<html>
<head>
  <title>Canvas</title>

  <style>
  @import url('https://fonts.googleapis.com/css?family=Open+Sans:400,800');
  body {
    width: 100%;
    height: 100%;
    margin: 0;
    padding: 0;
  }
  header {
    width: 100%;
    min-height:56px;
    height:56px;
    background:#fff;
    border-bottom:1px solid #eee;
    z-index:1000;
  }
  aside {
    background: #eaeaea;
    width:310px;
    float:left;
    height: 800px;
  }
  main {
    background: #fff;
    width: calc( 100% - 380px );
    float: left;
    margin-left: 50px;
    position: relative;
    overflow:hidden;
  }
  h1 {
    font-family: sans-serif;
    color: #3d3d3d;
    font-size: 20px;
    margin: 0 0 7px 0;
  }
  .title {
    padding: 17px;
    text-transform: uppercase;
  }
  .options {
    width: 90%;
    padding: 5%;
  }
  .control-items {
    padding: 14px 0 20px 0;
    border-bottom: 1px solid #afafaf;
  }
  #saveimage {
    bottom: 10px;
    margin: 20px 10px 20px 0px;
    background: #2c4bff;
    color: #fff;
    padding: 10px 0px;
    text-transform: uppercase;
    text-decoration: none;
    font-family: sans-serif;
    font-weight: bold;
    border-radius: 10px;
    text-align: center;
    display: block;
  }
  #saveimage:hover {
    background:#1f35b3;
  }
  #saveimage:active {
    bottom:8px;
  }
  .gui {
    position: absolute;
    z-index: 100;
    width: 360px;
    height: 640px;
    pointer-events: none;
    left:220px;
    top:80px;
  }
  </style>
</head>
<body>

  <header id="header">
    <div class="title">
      <h1>Canvas</h1>
    </div>
  </header>

  <aside>
    <div class="options">
      <div id="pick-image-1" class="control-items">
        <h1>Pick image</h1>
        <input id="backgroundpick" type="file" name="">
      </div>
      <a href="" id="saveimage">download image</a>
    </div>
  </aside>

  <main id="main-area">
    <canvas id="c" width="800" height="800"></canvas>
      <!-- <img class="gui" src="images/gui-overlay.png"> -->
  </main>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/fabric.js/2.6.0/fabric.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/webfont/1.6.28/webfontloader.js"></script>

  <script>
  // Global var for the canvas - global so our saving functions can access it
  var canvas;

  // Pul all canvas code in a function so we can call it after google fonts have loaded
  function template1() {

    // Make canvas a fabric canvas
    canvas = new fabric.Canvas('c', {
      preserveObjectStacking: true
    });
    canvas.backgroundColor = 'rgb(140,140,140)';

    // Clip to 360 x 640 section in middle of 800 x 800 canvas - 360 x 640 with multiplier 3 in export gives 1080 x 1920
    canvas.clipTo = function(ctx) {
      ctx.rect(220, 80, 360, 640);
    };
    canvas.controlsAboveOverlay = true;

    // Set up overlay image 
    canvas.setOverlayImage('images/overlay-bg.png', canvas.renderAll.bind(canvas), {
      opacity: 0.5,
      angle: 0,
      left: 0,
      top: 0,
      originX: 'left',
      originY: 'top',
      crossOrigin: 'anonymous'
    });

    // Loading image onto canvas
    var tempImg = 'images/image.png';

    var birdbox;
    var birdboxImg = new Image();
    birdboxImg.onload = function (img) {    
         birdbox = new fabric.Image(birdboxImg, {
            angle: 0,
            width: 1171,
            height: 1950,
            left: 200,
            top: 80,
            scaleX: .33,
            scaleY: .33,
            selectable: true,      
            borderColor: 'red',
            cornerColor: 'green',
            cornerSize: 12,
            transparentCorners: false
        });
        canvas.add(birdbox);
        // This is like z-index, this keeps the image behind the text
        canvas.moveTo(birdbox, 0);
    };
    birdboxImg.src = tempImg;

    // Load text onto canvas
    var textbox = new fabric.Textbox('Caption goes here - you can resize the text with the handles', {
      left: 240,
      top: 455,
      width: 320,
      fontSize: 28,
      fill: '#fff',
      fontFamily: 'Open Sans',
      fontWeight: 800,
      textAlign: 'center',      
      borderColor: 'red',
      cornerColor: 'green',
      cornerSize: 12,
      transparentCorners: false
    });
    // Add shadow to the textbox with this line
    textbox.setShadow("0px 0px 10px rgba(0, 0, 0, 1)");
    canvas.add(textbox);

    // Function to update image src - timout added to fix image loading bug
    function addImage(img) {
      birdbox.setSrc(img);
      setTimeout(function(){ 
        canvas.renderAll(); 
      }, 1);
    }

    // Pick new background image
    var bgpicker = document.querySelector('#backgroundpick');
      bgpicker.onchange = function(e) {
      var file = e.target.files[0];
      var reader = new FileReader();
      reader.onload = function(file) {
          addImage(file.target.result);
      }
      reader.readAsDataURL(file);
    }
    
  }

  // dataURLtoBlob function for saving
  function dataURLtoBlob(dataurl) {
      var arr = dataurl.split(','), mime = arr[0].match(/:(.*?);/)[1],
          bstr = atob(arr[1]), n = bstr.length, u8arr = new Uint8Array(n);
      while(n--){
          u8arr[n] = bstr.charCodeAt(n);
      }
      return new Blob([u8arr], {type:mime});
  }
  // Save image
  var link = document.getElementById('saveimage');   
  link.addEventListener('click', function() {
    // Remove overlay image
    canvas.overlayImage = null;
    canvas.renderAll.bind(canvas);
    // Remove canvas clipping so export the image
    canvas.clipTo = null;
    // Export the canvas to dataurl at 3 times the size and crop to the active area
    var imgData = canvas.toDataURL({
      format:'jpeg', 
      quality: 1,
      multiplier: 3,
      left: 220,
      top: 80,
      width: 360,
      height: 640
    });
    var strDataURI = imgData.substr(22, imgData.length);
    var blob = dataURLtoBlob(imgData);
    var objurl = URL.createObjectURL(blob);
    link.download = "story.jpg";
    link.href = objurl;
    // Reset the clipping path to what it was
    canvas.clipTo = function(ctx) {
      ctx.rect(220, 80, 360, 640);
    };
    // Reset overlay image
    canvas.setOverlayImage('images/overlay-bg.png', canvas.renderAll.bind(canvas), {
      opacity: 0.5,
      angle: 0,
      left: 0,
      top: 0,
      originX: 'left',
      originY: 'top',
      crossOrigin: 'anonymous'
    });
    canvas.renderAll();
  }, false);

  // Check if web fonts are loaded - if web font not loaded textbox error happens
  WebFont.load({
    google: {
      families: ['Open Sans:400,800']
    },
    active: function() {
      // Call template1 - all our code for the canvas
      template1();
    },
  });
  </script>

</body>
</html>