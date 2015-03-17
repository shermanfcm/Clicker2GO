<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <link href='http://fonts.googleapis.com/css?family=Pacifico' rel='stylesheet' type='text/css'>
    <title>Answer Question | Clicker2GO</title>
    <link rel="stylesheet" href="../style.css">

    <?php

      // get qid
      $qid = $_GET['qid'];
      echo "Question $qid";

      // connect to mysql
      include_once ('../lib/dbCon.php');

      $question_row = ORM::for_table('questions')-> find_one($qid);
      $starttime = $question_row->starttime;
      $countdown = $question_row->countdown;
      $currenttime = time();

      // check if countdown already started
      if ($starttime == NULL)
        $countdownstarted = false;
      else
      {
        $starttime = strtotime($starttime);
        $countdownstarted = ($currenttime >= $starttime);
      }

      // variables to hold the default submit action and button text
      if ($countdownstarted)
      {
        $postaction = 'resetcountdown';
        $buttontext = "Reset CountDown";
      }
      else
      {
        $postaction = 'startcountdown';
        $buttontext = "Start CountDown";
      }


      // start countdown only if submitted by post
      if (isset($_POST['startcountdown']) && $_POST['startcountdown'] == 1)
      {

        // set the starttime in the database to time=now
        $question_row->starttime = date("Y-m-d H:i:s", time());
        $question_row->endtime = date("Y-m-d H:i:s", time() + $countdown);
        $question_row->save();
        // make a new query to get updated time results
        $starttime = strtotime($question_row->starttime);
        $countdownstarted = true;
        $buttontext = "Reset CountDown";
        $postaction = 'resetcountdown';
      }

      // reset countdown only if submitted by post
      else if (isset($_POST['resetcountdown']) && $_POST['resetcountdown'] == 1)
      {
        // set the starttime in the database to NULL
        $question_row->starttime = null;
        $question_row->save();
        // make a new query to get updated time results
        $starttime = strtotime($question_row->starttime);
        $countdownstarted = false;
        $buttontext = "Start CountDown";
        $postaction = 'startcountdown';
      }

      // reset all given answers from all users for the specific question
      if (isset($_POST['reset_answers']) && $_POST['reset_answers'] == 1)
      {
        $reset_answers = ORM::for_table('answers')
                         ->where('qid',$qid)
                         ->delete_many();
      }

      // set countdown in seconds for the specific question
      if (isset($_POST['set_countdown']))
      {
        $new_countdown = (int) ($_POST['set_countdown']);
        $question_row->countdown = $new_countdown;
        $question_row->save();
        if ($countdownstarted)
        {
          // set the starttime in the database to NULL
          $question_row->starttime = null;
          $question_row->save();
          // make a new query to get updated time results
          $starttime = strtotime($question_row->starttime);
          $countdownstarted = false;
          $buttontext = "Start CountDown";
          $postaction = 'startcountdown';
        }
      }

      // this page url to refresh
      $thispage = "test-countdown.php?qid=$qid"
    ?>

  </head>

  <body class="homepage">
    <p>
      <?php
        echo "current time = $currenttime <br>";
        echo "recorded startime = $starttime <br>";
        if ($countdownstarted)
        {

          echo "Question CountDown already started <br> Click to Reset CountDown";
        }
        else
          echo "Click to start countdown";
      ?>

    </p>
    <br/>
    <form action="<?php echo $thispage ?>" method="POST">
      <input name="<?php echo $postaction ?>" type='hidden' value=1 />
      <input type="submit" value="<?php echo $buttontext ?>" >
    </form>

    <form action="<?php echo $thispage?>" method="POST">
      <input name='reset_answers' type='hidden' value=1 />
      <input type="submit" value='Reset given answers to question' >
    </form>

    <form action="<?php echo $thispage?>" method="POST">
      <input name='set_countdown' type='text' />
      <input type="submit" value='Set question countdown' >
    </form>

  </body>
</html>
