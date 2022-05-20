<?php

// cmd input eg:
// parser.php --file products_comma_separated.csv --unique-combinations=combination_count.csv
// should create file with count for each unique combination

include "linkedList.php";

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
    $uniqueList = new LinkedList();

    if (($data = fopen($fileName, "r")) !== false) { // open file
      $firstRow = true; // used to capture headers
      $row = 1; // used for item dividers
      $headers = [];

      while (($line = fgetcsv($data, 1000, ",")) !== false) { // while a row is present

        // check brand_name and model_name exist
        if ($firstRow) {
          if (!in_array("brand_name", $line) || !in_array("model_name", $line)) {
            throw new Exception("Error- fields \"brand_name\" and \"model_name\" required", 1);
          }

          // add "count" header, create file with headers
          $temp = $line;
          array_push($temp, "count");
          $uniqueList->addNode(implode(",", $temp));
        }
        
        $obj = [];
        $count = 0;
        
        
        if (!$firstRow) {
          // add to linkedList
          $uniqueList->addNode(implode(",", $line));
          
          // create a divider for each item
          echo "\nitem #$row\n";
          $row++;
        }

        /* on first iteration store headers in an array,
        on every other iteration, add line to object, print line, increment its count */
        foreach ($line as $item) { // for each item in row
          if ($firstRow) {
            $newHeader = preg_replace('/_name/', "", $item);
            $newHeader = preg_replace('/_/', " ", $newHeader);
            $headers[$count] = $newHeader;
          } else {  
            $obj[$headers[$count]] = $item;
            echo "$headers[$count] = $item\n"; // print each row
          }
          $count++;
        }

        if ($firstRow) {
          $firstRow = false;
          continue;
        }

      }
    }

  } catch (\Throwable $err) {
    echo "error: $err";
  }

  fclose($data);

  // add uniques to file
  addToFile($uniqueFileName, $uniqueList);
}

function addToFile($fileName, $list) {
  // create csv file with unique combination counts
  $current = $list->head;
  $firstRow = true;
  
  $currentFile = fopen($fileName, "w");
  
  while ($current != null) {
    $data = $current->readData();
    $count = $current->readCount();

    if (!$firstRow) {
      fwrite($currentFile, "$data,$count\n");

    } else {
      // first row is headers, no need for count
      fwrite($currentFile, "$data\n");
      $firstRow = false;
    }

    $current = $current->next;
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