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
  <button class="btn btn-warning" data-record-id="reboot" data-record-title="Reboot" data-toggle="modal" data-target="#confirm-action"><span class="oi oi-power-standby"></span> Reboot</button>
  <a href="<?php header("Refresh");?>"type="button" class="btn btn-info"><span class="oi oi-reload"></span> Refresh</a>


  <table class="table table-striped">
    <thead>
      <tr>
        <th>Filename</th>
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
            $viewbutn = "disabled";
          };
        echo"<tr>
              <td style=\"width: 80%\">$file</td>
              <td style=\"width: 5%\"><a href=\"/video/$file\"><button type=\"button\" class=\"btn btn-info oi oi-eye\" $viewbtn></button></a></td>
              <td style=\"width: 5%\"><button type=\"button\" class=\"btn btn-warning oi oi-code\" $convbtn data-record-id=\"conv $file\" data-record-title=\"Convert $file to .mp4\" data-toggle=\"modal\" data-target=\"#confirm-action\"></button></td>
              <td style=\"width: 5%\"><a href=\"/video/$file\" download><button type=\"button\" class=\"btn btn-success oi oi-data-transfer-download\" download></button></a></td>
              <td style=\"width: 5%\"><button class=\"btn btn-danger oi oi-trash\" data-record-id=\"rm $file\" data-record-title=\"Delete $file\" data-toggle=\"modal\" data-target=\"#confirm-action\"></button></td>
            </tr>";
        unset($viewbtn);
        unset($covbtn);
      }
    }

    ?>
    </tbody>
  </table>


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

<script>
  // Bind click to action button in popup
  $('#confirm-action').on('click', '.btn-ok', function(e) {
    var $modalDiv = $(e.delegateTarget);
    var id = $(this).data('recordId');
  $.ajax({
        type: 'POST',
        url: 'cmd.php',
        data: { command:id },
        success: function(data) {
            //alert(data); // For debugging
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

</div>
</body>
</html>
