<?php
$PHP_DIR = './src';
require("$PHP_DIR/panda_helper.php");

$html_response = panda_response();
if (is_null($html_response))
  exit;
?>
<html>
  <head>
    <title>P A N D A</title>
    <style type="text/css">
      @import "<?php echo "$WEB_PUBLIC_DIR" ?>/main.css";
    </style>
    <script src="<?php echo "$WEB_PUBLIC_DIR" ?>/jquery-1.7.min.js" type="text/javascript"></script>
    <script src="<?php echo "$WEB_PUBLIC_DIR" ?>/application.js" type="text/javascript"></script>
  </head>
  <body>
    <h1>panda 6.1.7</h1>

    <div class="content-wrapper">
    <div id="panda-main" class="module">
      <?php echo $html_response ?>
    </div>
    </div>

    <div class="content-wrapper">

	<div id="talk" class="module">
      <h2>Talky panda</h2>
      <form enctype="multipart/form-data" action="/talk" method="POST">
        <input id="input-txtmsg" name="txtmsg" type="text" size="150" maxlength="300"/><br/>
        <div class="submit-area">
          <input id="talk-submit" type="submit" disabled="true"/>
        </div>
      </form>
	</div>
	
    <div id="recorder" class="module">
      <h2>Broadcast a very important announcement</h2>
      <applet
        CODE="com.softsynth.javasonics.recplay.RecorderUploadApplet"
        CODEBASE="<?php echo "$WEB_PUBLIC_DIR/java" ?>"
        ARCHIVE="JavaSonicsListenUp.jar"
        NAME="JavaSonicRecorderUploader"
        WIDTH="400" HEIGHT="120">
        <!-- Use a low sample rate that is good for voice. -->
        <param name="frameRate" value="11025.0">
        <!-- Most microphones are monophonic so use 1 channel. -->
        <param name="numChannels" value="1">
        <!-- Set maximum message length to whatever you want. -->
        <param name="maxRecordTime" value="20.0">
        <!-- Turn on dynamic range compression. -->
        <param name="compressorEnable" value="yes">
        <param name="compressorThreshold" value="0.2">
        <param name="compressorNoiseThreshold" value="0.05">
        <param name="compressorCurvature" value="0.05">
        <!-- Specify name of file uploaded.
	         There are alternatives that allow dynamic naming. -->
        <param name="uploadFileName" value="userfile">
        <!-- Server script to receive the multi-part form data. -->
        <param name="uploadURL" value="broadcast">
      </applet>
    </div>

    <div id="uploader" class="module">
      <h2>Add 2 panda</h2>
      <form enctype="multipart/form-data" action="/create" method="POST">
        <label>Title</label><input id="input-title" name="title" type="text"/><br/>
        <label>Image</label><input id="input-image" name="imagefile" type="file"/><br/>
        <label>Audio</label><input id="input-audio" name="audiofile" type="file"/><br/>
        <div class="submit-area">
          <input id="uploader-submit" value="Upload" type="submit" disabled="true"/>
        </div>
      </form>
    </div>

    </div>

  </body>
</html>
