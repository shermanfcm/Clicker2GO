<?php

  session_start();

  if (!isset($_SESSION['uid']))
  {
          header('Location: ../');
  }
  else
  {
    $uid = $_SESSION['uid'];

    include_once ('../lib/dbCon.php');
    // check if user is a lecturer (user type = 2)
    $is_lecturer = ORM::for_table('u_type')
                        ->where(array(
                               'uid'=>$uid,
                               'id_t'=>2
                         ))
                        ->find_one();
  }
  $qid = $_POST['qid'];
  $question_row = ORM::for_table('questions')-> find_one($qid);

  if (isset($_POST['start_timer']))
  {
    $countdown = (int) ($_POST['start_timer']);
    $question_row->countdown = $countdown;
    
    // set the starttime in the database to time=now
    $question_row->starttime = date("Y-m-d H:i:s", time());
    $question_row->endtime = date("Y-m-d H:i:s", time() + $countdown);
    $question_row->save();
  }

  // reset countdown only if submitted by post
  else if (isset($_POST['reset_timer']) && $_POST['reset_timer'] == 1)
  {
    $question_row->starttime = null;
    $question_row->save();
  }

  else if (isset($_POST['stop_timer']) && $_POST['stop_timer'] == 1)
  {
    // set the starttime in the database to NULL
    $question_row->endtime = $question_row->starttime;
    $question_row->save();
  }      
      
  else if (isset($_POST['extend_timer']))
  {
    $question_row->endtime = date("Y-m-d H:i:s"
                        , strtotime($question_row->endtime) + $_POST['extend_timer']);
    $question_row->save();
  }
        

  if ($question_row->starttime != NULL)
  {
    if (strtotime($question_row->endtime) <= time())
      $time_left = -1; 
    else
      $time_left = strtotime($question_row->endtime) - time(); 
    $timer_action = "document.getElementById('correct').style.color='green'";
    include('timer.php');
  }
  else
    $timer ="not start"; 
  
  echo $timer;


?>

