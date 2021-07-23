<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Video;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Config;
use Meema\MediaConverter\Providers\MediaConverterServiceProvider;
use Meema\MediaConverter\Facades\MediaConvert;
use Session;
use Response;
use Aws\S3\S3Client;
use View;
class VideoController extends Controller
{
    public $jobSettings = [];

    public function create()
    {
        return view('videos.create');
       
    }
    
    
    protected function getPackageProviders($app): array
     {
         return [MediaConverterServiceProvider::class];
     }
 
     public function initializeDotEnv()
     {
         if (! file_exists(__DIR__.'/../.env')) {
             return;
         }
 
         $dotenv = \Dotenv\Dotenv::createImmutable(dirname(__DIR__));
         $dotenv->load();
     }
 
     public function initializeSettings()
     {
         // let's make sure these config values are set
         Config::set('media-converter.credentials.key', env('AWS_ACCESS_KEY_ID'));
         Config::set('media-converter.credentials.secret', env('AWS_SECRET_ACCESS_KEY'));
         Config::set('media-converter.url', env('AWS_MEDIACONVERT_ACCOUNT_URL'));
         Config::set('media-converter.iam_arn', env('AWS_IAM_ARN'));
         Config::set('media-converter.queue_arn', env('AWS_QUEUE_ARN'));
 
         $configFile = file_get_contents(__DIR__.'/config/job.json');
         $this->jobSettings = json_decode($configFile, true);
     }

    public function store(Request $request)
    {
     
        $request->validate([
            'video' => 'required'
            ], [
                'firstname.required' => 'Video is required'
                ]);
         $pathVideos=[];
        $pathWatermark='';
        $pathAudio='';
        $pathVideo = $request->file('video')->store('videos', 's3');


          $nbreOfVideos = $request->count;
          if($nbreOfVideos!=0){
   for($i = 1 ; $i <=$nbreOfVideos ; $i++){
       $vd = "video".$i;
       if($request->file($vd)){
       $pathVd = $request->file($vd)->store('videos', 's3');
  
            array_push($pathVideos, $pathVd);
       }
    }
        }
        if($request->file('watermark')){
            $pathWatermark = $request->file('watermark')->store('videos/watermarks', 's3');
      
        }
        if($request->file('audio')){
            $pathAudio = $request->file('audio')->store('videos/audio', 's3');
         
        }
        // $video=  Video::create([
        //   'filename'=>basename($pathVideo),
        // ]);
        // $pathVideo=$request->pathVideo;
        // $pathVideos=$request->pathVideos;
        // $pathWatermark=$request->pathWatermark;
        // $pathAudio=$request->pathAudio;
       
      

        $response = array(
          'status' => 'success',
          'pathVideo'    => $pathVideo,
           'pathVideos'    =>$pathVideos,
           'pathWatermark' =>$pathWatermark,
           'pathAudio' =>$pathAudio,
           
      );
        return Response::json($response);
      }
    public function transcode(Request $request){
      $pathVideo=$request->pathVideo;
      $pathVideos=$request->pathVideos;
      $pathWatermark=$request->pathWatermark;
      $pathAudio=$request->pathAudio;
      $pathVideos = explode("," , $pathVideos);
      if($request->scaling=="DEFAULT"){
          $scaling="DEFAULT";
      }
      else{
        $scaling="STRETCH_TO_OUTPUT";
      }
      if($request->audio){
        $audio='"ExternalAudioFileInput": "s3://'.env('AWS_BUCKET').'/'.$pathAudio.'",';
    }
    else{
      $audio="";
    }
    if($request->mute){
        $audioMp4="";
        $audioWebm="";
    }
    else{
      $audioMp4=',
      "AudioDescriptions": [
        {
          "AudioTypeControl": "FOLLOW_INPUT",
          "AudioSourceName": "Audio Selector 1",
          "CodecSettings": {
            "Codec": "AAC",
            "AacSettings": {
              "AudioDescriptionBroadcasterMix": "NORMAL",
              "Bitrate": 96000,
              "RateControlMode": "CBR",
              "CodecProfile": "LC",
              "CodingMode": "CODING_MODE_2_0",
              "RawFormat": "NONE",
              "SampleRate": 48000,
              "Specification": "MPEG4"
            }
          },
          "LanguageCodeControl": "FOLLOW_INPUT"
        }
      ]';
      $audioWebm=',
      "AudioDescriptions": [
        {
          "AudioTypeControl": "FOLLOW_INPUT",
          "CodecSettings": {
            "Codec": "OPUS",
            "OpusSettings": {
              "Bitrate": 96000,
              "Channels": 2,
              "SampleRate": 48000
            }
          },
          "LanguageCodeControl": "FOLLOW_INPUT"
        }
      ]';

    }
    if($request->resolution=="720p"){
      $width=1280;
      $height=720;
    }
    else{
      $width=1920;
      $height=1080;
     }
    if($request->watermark){
      $watermark='"ImageInserter": {
            "InsertableImages": [
              {
                "Width": 100,
                "Height": 100,
                "ImageX": 50,
                "ImageY": 50,
                "Layer": 10,
                "ImageInserterInput": "s3://'.env('AWS_BUCKET').'/'.$pathWatermark.'",
                "Opacity": 50
              }
            ]
          },';
    }
    else{
        $watermark="";
    }
$videos="";
$nbreOfVideos = $request->count;
$i = 1 ;
if($nbreOfVideos!=0){
foreach($pathVideos as $v ){
$vd = "video".$i;
$i++;
if($request->file($vd)){
  $videos=$videos.',
        {
          "AudioSelectors": {
            "Audio Selector 1": {
              "Offset": 0,
              "DefaultSelection": "DEFAULT",
              '.$audio.'
              "ProgramSelection": 1
            }
          },
          "VideoSelector": {
            "ColorSpace": "FOLLOW",
            "Rotate": "DEGREE_0",
            "AlphaBehavior": "DISCARD"
          },
          "FilterEnable": "AUTO",
          "PsiControl": "USE_PSI",
          "FilterStrength": 0,
          "DeblockFilter": "DISABLED",
          "DenoiseFilter": "DISABLED",
          "InputScanType": "AUTO",
          "TimecodeSource": "ZEROBASED",
          '.$watermark.'
          "FileInput": "s3://'.env('AWS_BUCKET').'/'.$v.'"
        }';
}
}
}
   // if($request->video2){
    //     $video2=',
    //     {
    //       "AudioSelectors": {
    //         "Audio Selector 1": {
    //           "Offset": 0,
    //           "DefaultSelection": "DEFAULT",
    //           '.$audio.'
    //           "ProgramSelection": 1
    //         }
    //       },
    //       "VideoSelector": {
    //         "ColorSpace": "FOLLOW",
    //         "Rotate": "DEGREE_0",
    //         "AlphaBehavior": "DISCARD"
    //       },
    //       "FilterEnable": "AUTO",
    //       "PsiControl": "USE_PSI",
    //       "FilterStrength": 0,
    //       "DeblockFilter": "DISABLED",
    //       "DenoiseFilter": "DISABLED",
    //       "InputScanType": "AUTO",
    //       "TimecodeSource": "ZEROBASED",
    //       '.$watermark.'
    //       "FileInput": "s3://'.env('AWS_BUCKET').'/'.$pathVideo2.'"
    //     }';
    // }
    // else{
    //  $video2="";
    // }
    
    $setting='{
        "TimecodeConfig": {
          "Source": "ZEROBASED"
        },
        "OutputGroups": [
          {
            "CustomName": "MP4",
            "Name": "File Group",
            "Outputs": [
              {
                "ContainerSettings": {
                  "Container": "MP4",
                  "Mp4Settings": {
                    "CslgAtom": "INCLUDE",
                    "CttsVersion": 0,
                    "FreeSpaceBox": "EXCLUDE",
                    "MoovPlacement": "PROGRESSIVE_DOWNLOAD",
                    "AudioDuration": "DEFAULT_CODEC_DURATION"
                  }
                },
                "VideoDescription": {
                  "ScalingBehavior": "'.$scaling.'",
                  "TimecodeInsertion": "DISABLED",
                  "AntiAlias": "ENABLED",
                  "Sharpness": 50,
                  "CodecSettings": {
                    "Codec": "H_264",
                    "H264Settings": {
                      "InterlaceMode": "PROGRESSIVE",
                      "ScanTypeConversionMode": "INTERLACED",
                      "NumberReferenceFrames": 3,
                      "Syntax": "DEFAULT",
                      "Softness": 0,
                      "GopClosedCadence": 1,
                      "GopSize": 90,
                      "Slices": 1,
                      "GopBReference": "DISABLED",
                      "SlowPal": "DISABLED",
                      "EntropyEncoding": "CABAC",
                      "Bitrate": 2200000,
                      "FramerateControl": "INITIALIZE_FROM_SOURCE",
                      "RateControlMode": "CBR",
                      "CodecProfile": "MAIN",
                      "Telecine": "NONE",
                      "MinIInterval": 0,
                      "AdaptiveQuantization": "AUTO",
                      "CodecLevel": "AUTO",
                      "FieldEncoding": "PAFF",
                      "SceneChangeDetect": "ENABLED",
                      "QualityTuningLevel": "SINGLE_PASS",
                      "FramerateConversionAlgorithm": "DUPLICATE_DROP",
                      "UnregisteredSeiTimecode": "DISABLED",
                      "GopSizeUnits": "FRAMES",
                      "ParControl": "INITIALIZE_FROM_SOURCE",
                      "NumberBFramesBetweenReferenceFrames": 2,
                      "RepeatPps": "DISABLED",
                      "DynamicSubGop": "STATIC"
                    }
                  },
                  "AfdSignaling": "NONE",
                  "DropFrameTimecode": "ENABLED",
                  "RespondToAfd": "NONE",
                  "ColorMetadata": "INSERT",
                  "Width": '.$width.',
                  "Height": '.$height.'
                }
                '.$audioMp4.'
              }
            ],
            "OutputGroupSettings": {
              "Type": "FILE_GROUP_SETTINGS",
              "FileGroupSettings": {
                "Destination": "s3://'.env('AWS_BUCKET_OUTPUT').'/mp4/"
              }
            }
          },
          {
            "Name": "File Group",
            "OutputGroupSettings": {
              "Type": "FILE_GROUP_SETTINGS",
              "FileGroupSettings": {
                "Destination": "s3://'.env('AWS_BUCKET_OUTPUT').'/webm/"
              }
            },
            "Outputs": [
              {
                "VideoDescription": {
                  "ScalingBehavior": "'.$scaling.'",
                  "TimecodeInsertion": "DISABLED",
                  "AntiAlias": "ENABLED",
                  "Sharpness": 50,
                  "CodecSettings": {
                    "Codec": "VP8",
                    "Vp8Settings": {
                      "QualityTuningLevel": "MULTI_PASS",
                      "RateControlMode": "VBR",
                      "GopSize": 90,
                      "FramerateControl": "INITIALIZE_FROM_SOURCE",
                      "FramerateConversionAlgorithm": "DUPLICATE_DROP",
                      "ParControl": "INITIALIZE_FROM_SOURCE",
                      "Bitrate": 2400000
                    }
                  },
                  "DropFrameTimecode": "ENABLED",
                  "ColorMetadata": "INSERT",
                  "Width": '.$width.',
                  "Height": '.$height.'
                }
                '.$audioWebm.',
                "ContainerSettings": {
                  "Container": "WEBM"
                }
              }
            ],
            "CustomName": "WEBM"
          },
          {
            "Name": "File Group",
            "OutputGroupSettings": {
              "Type": "FILE_GROUP_SETTINGS",
              "FileGroupSettings": {
                "Destination": "s3://'.env('AWS_BUCKET_OUTPUT').'/thumbnails/"
              }
            },
            "Outputs": [
              {
                "VideoDescription": {
                  "ScalingBehavior": "'.$scaling.'",
                  "TimecodeInsertion": "DISABLED",
                  "AntiAlias": "ENABLED",
                  "Sharpness": 50,
                  "CodecSettings": {
                    "Codec": "FRAME_CAPTURE",
                    "FrameCaptureSettings": {
                      "FramerateNumerator": 1,
                      "FramerateDenominator": 5,
                      "MaxCaptures": 2,
                      "Quality": 80
                    }
                  },
                  "DropFrameTimecode": "ENABLED",
                  "ColorMetadata": "INSERT",
                  "Width": '.$width.',
                  "Height": '.$height.'
                },
                "ContainerSettings": {
                  "Container": "RAW"
                }
              }
            ],
            "CustomName": "Thumbnails"
          }
        ],
        "AdAvailOffset": 0,
        "Inputs": [
          {
            "AudioSelectors": {
              "Audio Selector 1": {
                "Offset": 0,
                "DefaultSelection": "DEFAULT",
                '.$audio.'
                "ProgramSelection": 1
              }
            },
            "VideoSelector": {
              "ColorSpace": "FOLLOW",
              "Rotate": "DEGREE_0",
              "AlphaBehavior": "DISCARD"
            },
            "FilterEnable": "AUTO",
            "PsiControl": "USE_PSI",
            "FilterStrength": 0,
            "DeblockFilter": "DISABLED",
            "DenoiseFilter": "DISABLED",
            "InputScanType": "AUTO",
            "TimecodeSource": "ZEROBASED",
            '.$watermark.'
            "FileInput": "s3://'.env('AWS_BUCKET').'/'.$pathVideo.'"
          }
          '.$videos.'
        ]}';
    $settings=json_decode($setting, true);
    $priority = 0; 
    $metaData =[];
    $result = MediaConvert::createJob($settings,$metaData,$priority);
    $jobId = $result->get('Job')['Id'];
    $readResult = MediaConvert::getJob($jobId);
    $status=$readResult->get('Job')['Status'];
    while($status!="COMPLETE"){
       $readResult = MediaConvert::getJob($jobId);
     $status=$readResult->get('Job')['Status'];
     }
    $path=pathinfo($pathVideo,PATHINFO_FILENAME);
    //  $data['mp4'] = 'https://outputtestcoverter.s3.eu-central-1.amazonaws.com/mp4/'.$path.'.mp4';
    //    $data['webm'] = 'https://outputtestcoverter.s3.eu-central-1.amazonaws.com/webm/'.$path.'.webm';

    //Creating a presigned URL

$client = S3Client::factory(
  array(
      'credentials' => array(
          'key' => 'AKIARAK3ETSTME7CGDUO',
          'secret' => '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'
      ),
      'version' => 'latest',
      'region'  => 'eu-central-1'
  )
);
$cmd = $client->getCommand('GetObject', [
  'Bucket' => 'outputtestcoverter',
  'Key' => 'mp4/'.$path.'.mp4'
]);
$request = $client->createPresignedRequest($cmd, '+20 minutes');
$presignedUrl = (string) $request->getUri();
$data['mp4'] =$presignedUrl;

$cmd2 = $client->getCommand('GetObject', [
  'Bucket' => 'outputtestcoverter',
  'Key' => 'webm/'.$path.'.webm'
]);
$request2 = $client->createPresignedRequest($cmd2, '+20 minutes');
$presignedUrl2 = (string) $request2->getUri();
$data['webm']= $presignedUrl2;
    return view('videos.video',compact('data'))->render();
      

  //    $response = array(
  //     'status' => 'success',
  //     'mp4'    => 'https://outputtestcoverter.s3.eu-central-1.amazonaws.com/mp4/'.$path.'.mp4',
  //     'webm'   => 'https://outputtestcoverter.s3.eu-central-1.amazonaws.com/webm/'.$path.'.webm'
  // );
  // return Response::json($response);


    //$fileName = pathinfo($pathVideo,PATHINFO_FILENAME);
    //return response()->json(["https://outputtestcoverter.s3.eu-central-1.amazonaws.com/mp4/$fileName"]);
    //     $this->initializeDotEnv();
    //     $this->initializeSettings();
    //    $framerateNumerator = 1;
    //    $framerateDenominator = 2;
    //    $maxCaptures = 2;
    //    $imageQuality = 75;
    //    $outputName = 'my-video.mp4';
    //    $bucket = 'outputtestcoverter';
    //    $width =100;
    //    $nameModifier = '.$w$x$h$';
    //    $converter = MediaConvert::path($pathVideo, 'inputtestcoverter')
    //    ->optimizeForWeb()
    //    ->withThumbnails($framerateNumerator, $framerateDenominator, $maxCaptures, $width, $nameModifier, $imageQuality)
    //    ->saveTo($outputName, $bucket);
    //    $response = MediaConvert::createJob($converter->jobSettings);
    //$url="https://s3-eu-central-1.amazonaws.com/outputtestcoverter/webm/'.$pathVideo;
    //return response()->json(['success'=>'You have successfully upload file.']);
    }
    public function store2(Request $request)
    {
                Session::put('progress', 0);
                Session::save(); // Remember to call save()
                  $file=$request->file('video');
                 $fileName = $file->getClientOriginalName();
                 $file_name = time(). $fileName;
                  $temp_file_location = $file->getRealPath();
                  $client = S3Client::factory(
                    array(
                        'credentials' => array(
                            'key' => 'AKIARAK3ETSTME7CGDUO',
                            'secret' => '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'
                        ),
                        'version' => 'latest',
                        'region'  => 'eu-central-1'
                    )
                );
                  $result = $client->putObject([
                      'Bucket'     => 'inputtestcoverter',
                      'Key'        => $file_name,
                      'SourceFile' => $temp_file_location,
                      '@http' => [
                        'progress' => function ($downloadTotalSize, $downloadSizeSoFar, $uploadTotalSize, $uploadSizeSoFar) {
                            
                                          if($uploadTotalSize!=0 && $uploadSizeSoFar!=0) 
                                        {
                                        $per=$uploadSizeSoFar*100/$uploadTotalSize;
                                        $perint=(int)$per;
                                          Session::put('progress',$perint );
                                          Session::save(); // Remember to call save() 
                                        }
                          }
                      ]
                  ]);
              $response = array(
                'status' => 'success',
                'msg'    => 'https://outputtestcoverter.s3.eu-central-1.amazonaws.com//mp4//'.$pathVideo,
            );
              return Response::json($response);
            }
            public function getProgess() {
              return Response::json(array(Session::get('progress')));
          }
          public function delete(Request $request){
            $pathVideo=$request->pathVideo;
            $path=pathinfo($pathVideo,PATHINFO_FILENAME);
            Storage::disk('s3')->delete($pathVideo);
            $client = S3Client::factory(
              array(
                  'credentials' => array(
                      'key' => 'AKIARAK3ETSTME7CGDUO',
                      'secret' => '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'
                  ),
                  'version' => 'latest',
                  'region'  => 'eu-central-1'
              )
          );
            $bucket = env('AWS_BUCKET_OUTPUT');
            $key1 = 'mp4/'.$path.'.mp4';
            $key2 = 'webm/'.$path.'.webm';
            $key3 = 'thumbnails/'.$path.'.0000000.jpg';
            $key4 = 'thumbnails/'.$path.'.0000001.jpg';
            $result = $client->deleteObjects([
              'Bucket' => $bucket,
              'Delete' => [
                  'Objects' => [
                      [
                          'Key' => $key1,
                      ],
                      [
                        'Key' => $key2,
                      ],
                      [
                        'Key' => $key3,
                    ],
                    [
                      'Key' => $key4,
                  ],
                  ],
              ],
          ]);
            return "done";
          }

}
