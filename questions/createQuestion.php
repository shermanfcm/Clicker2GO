<?php
  // Initialize session
  session_start();

  // Jump to login page if uid not set
  if (!isset($_SESSION['uid'])) {
        header('Location: ../');
  }

  $courseName = $_POST['courseName'];
  $date = $_POST['date'];

  $placeholder = array("##courseName##","##date##");
  $replace = array($courseName,$date);
  echo str_replace($placeholder, $replace, file_get_contents('createQuestion'));
?>

