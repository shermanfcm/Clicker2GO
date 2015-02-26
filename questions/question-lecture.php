<?php


  // Initialize session
  session_start();

  // Jump to login page if uid not set
  if (!isset($_SESSION['uid']))
  {
          header('Location: ../');
  }
  else
  {
    $uid = $_SESSION['uid'];
  }

if(isset($_GET['qid'])) {
  // connect to mysql
  include_once ('../lib/dbCon.php');
  if (isset($_GET['seq'])) {
    $seq = $_GET['seq'];
  } else { $seq = ""; }

    $qid = $_GET['qid'];
    $question_row = ORM::for_table('questions')-> find_one($qid);

 if (!empty($question_row)) {

    $cuid = $question_row->id_cu;
    $courseName = ORM::for_table('course_units')->find_one($cuid)->course;
    $date = $question_row->date;

    $correct_answer = explode("|",$question_row-> correct);
    $question = $question_row-> question;
    $numbering_characters="ABCDEF";
    $answers = "";

    for ($i=1; $i<=6; $i++) 
    {
      
      $answer = $question_row->get('answer'.$i); 
      if (!empty($answer)) 
      {
        $isCorrect = "";
        if (in_array('answer'.$i, $correct_answer))
          $isCorrect = "<span style='font-size:12px;color:green;'>  (Correct)</span>";  
        $N=$numbering_characters[$i-1];
        
        $answers = $answers .
                    "<li>
                      $N. $answer  $isCorrect
                    </li>";
      }
    }

  include('graph.php');
  $comment = "<iframe src='comment.php?qid=$qid' seamless></iframe>";
  $placeholder = array("##graph##", "##question##", "##answers##",
                 "##comment##","##courseName##","##date##","##qnumber##","##name##");
  $replace = array($graph, $question, $answers,$comment,$courseName,$date
                     ,$seq,$_SESSION['name']);
  echo str_replace($placeholder, $replace, file_get_contents('question-lecture'));

//for errors
 }
 else {
  $information = "The question with id '$qid' is not exist.";
  echo str_replace("##information##", $information, file_get_contents('error'));
 }
}
else {
  $information = "The question id is empty.";
  echo str_replace("##information##", $information, file_get_contents('error'));
}
?>
