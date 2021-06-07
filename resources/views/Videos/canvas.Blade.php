<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    
</head>
<body>
<canvas id="canvas" width="300" height="300"></canvas>
<div><button id="save">Save</button></div>
<!-- <div id="player-target">
</div> -->
<script>
var sun = new Image();
var moon = new Image();
var earth = new Image();
function init() {
  sun.src = 'images/Canvas_sun.png';
  moon.src = 'images/Canvas_moon.png';
  earth.src = 'images/Canvas_earth.png';
  window.requestAnimationFrame(draw);
}

function draw() {
  var ctx = document.getElementById('canvas').getContext('2d');

  ctx.globalCompositeOperation = 'destination-over';
  ctx.clearRect(0, 0, 300, 300); // clear canvas

  ctx.fillStyle = 'rgba(0, 0, 0, 0.4)';
  ctx.strokeStyle = 'rgba(0, 153, 255, 0.4)';
  ctx.save();
  ctx.translate(150, 150);

  // Earth
  var time = new Date();
  ctx.rotate(((2 * Math.PI) / 60) * time.getSeconds() + ((2 * Math.PI) / 60000) * time.getMilliseconds());
  ctx.translate(105, 0);
  ctx.fillRect(0, -12, 50, 24); // Shadow
  ctx.drawImage(earth, -12, -12);

  // Moon
  ctx.save();
  ctx.rotate(((2 * Math.PI) / 6) * time.getSeconds() + ((2 * Math.PI) / 6000) * time.getMilliseconds());
  ctx.translate(0, 28.5);
  ctx.drawImage(moon, -3.5, -3.5);
  ctx.restore();

  ctx.restore();
  
  ctx.beginPath();
  ctx.arc(150, 150, 105, 0, Math.PI * 2, false); // Earth orbit
  ctx.stroke();
 
  ctx.drawImage(sun, 0, 0, 300, 300);

  window.requestAnimationFrame(draw);
}
var canvas = document.querySelector('canvas');
// event handler for the save button
document.getElementById('save').addEventListener('click', function () {
  // retrieve the canvas data
  var canvasContents = canvas.toDataURL(); // a data URL of the current canvas image
  var data = { image: canvasContents, date: Date.now() };
  var string = JSON.stringify(data);

  // create a blob object representing the data as a JSON string
  var file = new Blob([string], {
    type: 'application/json'
  });
  
  // trigger a click event on an <a> tag to open the file explorer
  var a = document.createElement('a');
  a.href = URL.createObjectURL(file);
  a.download = 'data.json';
  document.body.appendChild(a);
  a.click();
  document.body.removeChild(a);
});


init();

var page = require('webpage').create();
page.open('http://127.0.0.1:8000/',function(status){
   page.render('phantom.png');
   phantom.exit();
});


// var snapshots = [
   
//    {"meta":{"id":"d990bd5454f43c927175a111","created":1421709529464,"modified":1421709529464,"author":"Anton Kotenko","description":"","name":"","copyright":"Copyright (c) by Anton Kotenko","duration":1.0,"numberOfScenes":1,"projectAccess":{"visibility":"PUBLIC","writeAccess":[],"readAccess":["PUBLIC"]},"authorUsername":"shamansir"},"anim":{"dimension":[550.0,450.0],"framerate":24.0,"loop":false,"background":"#ffffff","paths":["AjF1MAAAGIAAHUwMYrQAAAYgAAitABM=","KA4pCq5g0d9+AAAD","KP3oCq5ghMA="],"elements":[[2,"$$$LIBRARY$$$",10.0,[],{}],[2,"Scene1",1.0,[[2,"Shape 1","","",[30.0,30.0],"",4,[[4,[0.0,1.0],"",1],[4,[1.0],"",2]],{}]],{}],[3,"#2291ea","","",0]],"scenes":[1]}},
     
//    {"meta":{"id":"e793bd547fa7a8bf98c2554b","created":1421710311393,"modified":1421710311393,"author":"Anton Kotenko","description":"","name":"Circle","copyright":"Copyright (c) by Anton Kotenko","duration":1.0,"numberOfScenes":1,"projectAccess":{"visibility":"PUBLIC","writeAccess":[],"readAccess":["PUBLIC"]},"authorUsername":"shamansir"},"anim":{"dimension":[550.0,450.0],"framerate":24.0,"loop":false,"background":"#ffffff","paths":["IAAAw1E8AAShF6u4qJD2veAAJ7XvAAD1dwruHgABr3zwAA1731EhXcPlCIAAnyhEAAPqJFRIeAAJQjg=","KA6mCNmg0d6EAAAD","KP3oCNmghMA="],"elements":[[2,"$$$LIBRARY$$$",10.0,[],{}],[2,"Scene1",1.0,[[2,"Shape 1","","",[25.0,25.0],"",4,[[2,[],"",[1.2]],[4,[0.0,1.0],"",1],[4,[1.0],"",2]],{}]],{}],[3,"#e51c23","","",0]],"scenes":[1]}},
     
      
//    {"meta":{"id":"0c95bd5414ac319aa10677be","created":1421710603753,"modified":1421710603753,"author":"Anton Kotenko","description":"","name":"Chick","copyright":"Copyright (c) by Anton Kotenko","duration":1.0,"numberOfScenes":1,"projectAccess":{"visibility":"PUBLIC","writeAccess":[],"readAccess":["PUBLIC"]},"authorUsername":"shamansir"},"anim":{"dimension":[550.0,450.0],"framerate":24.0,"loop":false,"background":"#ffffff","paths":["IrDVeUVN+gWsWiLbLDeLNuqd0IWALsuG5sd0K2B03rjKeburjWtKNyv5w7JfbtYZRJXIImxPNSQ6iXIvQ3W0ATFnnBcN621L\nRCKav9mjTXHchBy/56xuSkSAOaqDG9NV3mBmiVFV83zCqKaxFOnt+gpqptNz/02qUHQbayMUzJeTGGxdw3zVZx8ucUPIdLv8\nFVadsTIfs3Lg09bebaWNj+n2ZtVs6S2kKYzbQhK7zbXqOhrYFmg0b6k3lquWkwOpdJipPTk1hWAza3r8htTeHw5kcTSTZQGE\nxtlYnjMkDSVbfPCqtsaAG5tXYba2m3m0baPIaS5dBUXJxMK5h8HQg9c38xiZhLBE2b+keauABNynV6JqF4WWyqi7pCDY5HI+\navQxJN0tzJG4aL9UutdomdoWgLkBzWZVIAAyqQABlUgAEzExsNg3Lu7CsG05ostFW0Q24ca3c3FS0Q+jU4dbqWelkIPv5JBc\nwA==","JhhqHBN7R6UHAAAM","KoBq8cE4ITA="],"elements":[[2,"$$$LIBRARY$$$",10.0,[],{}],[2,"Scene1",1.0,[[2,"chick 1","","",[60.239,31.575],"",4,[[2,[],"",[0.983]],[4,[0.0,1.0],"",1],[4,[1.0],"",2]],{}]],{}],[3,[[72.602,7.073,46.851,56.275],["#2291ea","#c6c36c","#a24d4d"],[0.0,0.513,1.0]],"",[-5.0,-5.0,12.0,"rgba(139,195,74,0.5)"],0]],"scenes":[1]}}
     
//    ];
   
//    anm.Player.EMPTY_BG = 'transparent';
//    anm.Player.EMPTY_STROKE = 'transparent';
//    anm.Player.EMPTY_STROKE_WIDTH = 0;
   
//    var animatronImporter = anm.importers.create('animatron');
//    var player = new anm.Player();
   
//    var currentSnapshot = 0;
//    function loadNextSnapshot() {
//        if (!snapshots[currentSnapshot]) return;
//        player.stop();
//        setTimeout(function() { 
//          player.load(snapshots[currentSnapshot], animatronImporter); 
//          currentSnapshot++;                 
//        }, 1);
//    }
   
//    player.init('player-target', { 
//        width: 400,
//        height: 327,
//        autoPlay: true,
//        ribbonsColor: 'transparent',
//        controlsEnabled: false });
//    loadNextSnapshot();
//    player.on('complete', loadNextSnapshot);

</script>
</body>
</html>