<?php 

class Node {
  public $data;
  public $count;
  public $next;
  
  public function __construct($data) {
    $this->data = $data;
    $this->count = 1;
    $this->next = null;
  }

  public function readData() {
    return $this->data;
  }

  public function readCount() {
    return $this->count;
  }

  public function addOne() {
    $this->count++;
  }
}

class LinkedList {
  public $head;
  public $tail;

  public function __construct() {
    $this->head = null;
    $this->tail = null;
  }

  public function addNode($data) {
    $newTail = new Node($data); // move me TODO
    $currentTail = $this->tail;
    $head = $this->head;

    if ($currentTail == null && $head == null) {
      // if list empty create head and tail
      $this->head = $newTail;
    } else if ($currentTail == null && $head != null) {
      // if head exists and tail doesn't
      if ($head->readData() == $data) {
        // if data is a copy increment count
        $head->addOne();
      } else {
        $this->tail = $newTail;
        $head->next = $newTail;
      }
    } else {
      // if list > 2
      $updated = false;

      while ($head != null) {
        if ($head->readData() == $data) {
          $head->addOne();
          $updated = true;
          break;
        }
        $head = $head->next;
      }
      
      if (!$updated) {
        // if no match was found, add to tail
        $currentTail->next = $newTail;
        $this->tail = $newTail;
      }
    }
  }
}

?>