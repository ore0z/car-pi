<?php
$command = $_POST['command'];
if ($command == trim($command) && strpos($command, ' ') !== false) {
  $parts = explode(" ", $command);
  $cmd = $parts[0];
  $file = $parts[1];
} else {
  $cmd = $command;
}

switch ($cmd) {
  case "reboot":
    $run = "sudo /sbin/reboot";
    break;
  case "startrec":
    $run = "/home/pi/git/car-pi/record-cam.py &";
    break;
  case "stoprec":
    $run = "sudo kill -9 $(ps aux | grep record | grep -v grep | awk '{print $2}')";
    break;
  case "conv":
	  $baseFile = basename($file, ".h264").PHP_EOL;
	  $trimFile = trim($baseFile);
    $run = "sudo /usr/bin/avconv -loglevel 8 -r 30 -i /video/$file -vcodec copy /video/$trimFile.mp4 -n";
    // echo "This may take a few minutes... Please refresh page.\n";
    break;
  case "rm":
    $run = "sudo /bin/rm /video/$file";
    break;
}
system($run);
echo "Command input: $command\n";
echo "Command run: $run\n";
?>
