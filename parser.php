<?php

// cmd input eg:
// parser.php --file products_comma_separated.csv --unique-combinations=combination_count.csv
// should show readable row for each item
// then create file with count for each unique combination

// ----- functions
function getFileNames(&$fileName, &$uniqueFileName, $argv) {
  // get file names from terminal
  try {
    for ($i = 0; $i < count($argv); $i++) {
      // check current arg is --file and next arg is file name
      if ($argv[$i] == "--file" && preg_match('/--unique-combinations=/', $argv[$i + 1]) !== 1) {
        $fileName = $argv[$i + 1];
      }
      // check if --unique-combinations is defined
      if (preg_match('/--unique-combinations=/', $argv[$i])) {
        $uniqueFileName = preg_replace('/--unique-combinations=/', "", $argv[$i]);
      }

    }

    if (!$fileName) { // error thrown if no file name given
      throw new Exception("No file name found. Please try again\n");
    }

    if (!$uniqueFileName) { // default unique-combinations file if none given
      echo "No --unique-combinations file name found. Default = \"combination_count.csv\"\n";
      $uniqueFileName = "combination_count.csv";
    }

  } catch (\Throwable $err) {
    exit($err);
  }
}

function main($fileName, $uniqueFileName) {
  try {
    $data;

    if (($data = fopen($fileName, "r")) !== false) { // open file
      $firstRow = true; // used to capture headers
      $row = 1; // used for item dividers
      $headers = [];
      $oldObj = []; // for adding to file
      $count = 1;
      
      while (($line = fgetcsv($data, 1000, ",")) !== false) { // while a row is present

        // check brand_name and model_name exist
        if ($firstRow) {
          if (!in_array("brand_name", $line) || !in_array("model_name", $line)) {
            throw new Exception("Error- fields \"brand_name\" and \"model_name\" required", 1);
          }

          // add "count" header, create file with headers
          $temp = $line;
          array_push($temp, "count");
          addToFile($uniqueFileName, $temp, 0, true);
        } else {
          // create a divider for each item
          echo "\nitem #$row\n";
          $row++;
        }

        // compare current line to old obj
        if ($line == $oldObj) {
          $count++;
        } else if($row > 2) {
          // if current line does not match, add to file
          addToFile($uniqueFileName, $oldObj, $count);
          $count = 1;
        }

        /* on first iteration store headers in an array,
        on every other iteration, add line to object, print line, increment its index (for headers) */
        $index = 0;
        foreach ($line as $item) { // for each item in row
          if ($firstRow) {
            $newHeader = preg_replace('/_name/', "", $item);
            $newHeader = preg_replace('/_/', " ", $newHeader);
            $headers[$index] = $newHeader;
          } else {  
            $oldObj[$index] = $item; // add to obj
            echo "$headers[$index] = $item\n"; // print each row
          }
          $index++;
        }

        if ($firstRow) {
          $firstRow = false;
        }

      }
      // add last row
      addToFile($uniqueFileName, $oldObj, $count);
    }

  } catch (\Throwable $err) {
    echo "error: $err";
  }

  fclose($data);
}

function addToFile($fileName, $data, $count, $isHeaders = false) {
  // create csv file with unique combination counts
  $mode = $isHeaders ? "w" : "a";
  
  $currentFile = fopen($fileName, $mode);
  
  $data = implode(",", $data);

  if (!$isHeaders) {
    fwrite($currentFile, "$data,$count\n");

  } else {
    // first row is headers, no need for count
    fwrite($currentFile, "$data\n");
    $firstRow = false;
  }

  fclose($currentFile);
}

// ----- run program

// globals
$fileName = null;
$uniqueFileName = null;

getFileNames($fileName, $uniqueFileName, $argv);
main($fileName, $uniqueFileName);

?>