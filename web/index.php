<!DOCTYPE html>
<html lang="en">
<head>
  <title>Car-Pi Videos</title>
  <meta charset="utf-8">
  <link rel="stylesheet" href="css/bootstrap.min.css">
  <link rel="stylesheet" href="css/open-iconic-bootstrap.min.css">
  <script src="js/jquery-3.3.1.min.js"></script>
  <script src="js/bootstrap.min.js"></script>
  </head>
<body>
<div class="container">
  <h3>Video Files</h3>
  <button type="button" class="btn btn-info btn-danger" data-record-id="stoprec" data-record-title="Stop recording" data-toggle="modal" data-target="#confirm-action"><span class="oi oi-media-stop"></span> Stop Rec</button>
  <button class="btn btn-success" data-record-id="startrec" data-record-title="Start recording" data-toggle="modal" data-target="#confirm-action"><span class="oi oi-media-record"></span> Start Rec</button>
  <button class="btn btn-danger" data-record-id="stoppwr" data-record-title="Stop Pwr Ctrl" data-toggle="modal" data-target="#confirm-action"><span class="oi oi-media-stop"></span> Stop Pwr Ctrl</button>
  <button class="btn btn-success" data-record-id="startpwr" data-record-title="Start Pwr Ctrl" data-toggle="modal" data-target="#confirm-action"><span class="oi oi-media-play"></span> Start Pwr Ctrl</button>
  <button class="btn btn-primary" data-record-id="h264tomp4" data-record-title="Convert All H264 Files to MP4" data-toggle="modal" data-target="#confirm-action"><span class="oi oi-code"></span> ConvertAll h264</button>
  <button class="btn btn-warning" data-record-id="reboot" data-record-title="Reboot" data-toggle="modal" data-target="#confirm-action"><span class="oi oi-power-standby"></span> Reboot</button>
  <a href="<?php header("Refresh");?>"type="button" class="btn btn-info"><span class="oi oi-reload"></span> Refresh</a>


  <table class="table table-striped">
    <thead>
      <tr>
        <th>Filename</th>
        <th>Size</th>
        <th>View</th>
        <th>Convt</th>
        <th>Save</th>
        <th>Trash</th>
      </tr>
    </thead>
    <tbody>

    <?php

    $dir = "/video/";
    // Open directory, read its contents
    if (is_dir($dir)){
      chdir($dir);
      array_multisort(array_map('filemtime', ($files = glob("*.*"))), SORT_DESC, $files);
      foreach($files as $file){
        $filesize = filesize($file);
        $filesize = round($filesize / 1024 / 1024, 2);
        $viewbtn = "disabled"; // Disable buttons by default for all file typs
        $convbtn = "disabled"; // Disable convert button for all file types
          if (($file == "." || $file == "..")){
            continue; // Ignore current/partent dir
          };
          if (\strpos($file, '.mp4') == true){
            $viewbtn = ""; // We can view mp4, enable button
            $convbtn = "disabled";
          };
          if (\strpos($file, '.h264') == true){
            $convbtn = ""; // We can convert h264, enabled button
            $viewbtn = "disabled";
          };
        echo"<tr id=\"123\">
	      <td style=\"width: 75%\">$file</td>
	      <td style=\"width: 5%\">${filesize}MB</td>
	      <td style=\"width: 5%\"><button type=\"button\" class=\"btn btn-primary oi oi-eye video-btn\" data-toggle=\"modal\" data-src=\"video/$file\" data-target=\"#vidModal\" $viewbtn></button></td>
              <td style=\"width: 5%\"><button type=\"button\" class=\"btn btn-warning oi oi-code\" $convbtn data-record-id=\"conv $file\" data-record-title=\"Convert $file to .mp4\" data-toggle=\"modal\" data-target=\"#confirm-action\"></button></td>
              <td style=\"width: 5%\"><a href=\"/video/$file\" download><button type=\"button\" class=\"btn btn-success oi oi-data-transfer-download\" download></button></a></td>
              <td style=\"width: 5%\"><button class=\"btn btn-danger oi oi-trash btnDelete\" data-record-id=\"rm $file\" data-record-title=\"Delete $file\" data-toggle=\"modal\" data-target=\"#confirm-action\"></button></td>
            </tr>";
        unset($viewbtn);
        unset($covbtn);
      }
    }

    ?>
    </tbody>
  </table>

<!-- Action modal -->
<div class="modal fade" id="confirm-action" tabindex="-1" role="dialog" aria-labelledby="actionModal" aria-hidden="true">
<div class="modal-dialog">
  <div class="modal-content">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
      <h4 class="modal-title" id="actionModal">Confirm action</h4>
    </div>
    <div class="modal-body">
      <p>You are about to run action: <b><i class="title"></i></b></p>
      <p>Do you want to continue?</p>
    </div>
    <div class="modal-footer">
      <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
      <button type="button" class="btn btn-danger btn-ok">Confirm</button>
    </div>
  </div>
</div>
</div>

<!-- Video Modal -->
<div class="modal fade" id="vidModal" tabindex="-1" role="dialog" aria-labelledby="Preview" aria-hidden="true">
  <div style="width: 70%" class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-body">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
           <span aria-hidden="true">&times;</span>
        </button>
        <!-- 16:9 Aspect Ratio -->
        <div class="embed-responsive embed-responsive-16by9">
          <iframe class="embed-responsive-item" src="" id="video"  allowscriptaccess="always"></iframe>
        </div>
      </div>
    </div>
  </div>
</div>


<script>
  // Bind click to action button in popup
  $('#confirm-action').on('click', '.btn-ok', function(e) {
    var $modalDiv = $(e.delegateTarget);
    var $id = $(this).data('recordId');
  $.ajax({
        type: 'POST',
        url: 'cmd.php',
        data: { command:$id },
    success: function(data) {
      // alert(data); // For debugging
      if ( $id.includes("conv") || $id.includes("rm") ) {
        location.reload();
      }
    }
  });
    $modalDiv.addClass('loading');
    setTimeout(function() {
      $modalDiv.modal('hide').removeClass('loading');
    });
  });

  $('#confirm-action').on('show.bs.modal', function(e) {
    var data = $(e.relatedTarget).data();
    $('.title', this).text(data.recordTitle);
    $('.btn-ok', this).data('recordId', data.recordId);
  });
</script>

<script>
  $(document).ready(function() {
  // Gets the video src from the data-src on each button
  var $videoSrc;
   $('.video-btn').click(function() {
   $videoSrc = $(this).data( "src" );
   });
   console.log($videoSrc);
   // when the modal is opened autoplay it
   $('#vidModal').on('shown.bs.modal', function (e) {
   // set the video src to autoplay
   $("#video").attr('src',$videoSrc + "?rel=0&amp;showinfo=0&amp;modestbranding=1&amp;autoplay=1" );
   })
   // stop playing video when modal closes
   $('#vidModal').on('hide.bs.modal', function (e) {
     // poor man's stop video
     $("#video").attr('src',$videoSrc);
   })
});
</script>

</div>
</body>
</html>
