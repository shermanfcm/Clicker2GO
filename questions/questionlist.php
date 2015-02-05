<!DOCTYPE html>
<?php
// Initialize session
session_start();

// Jump to login page if username not set
if (!isset($_SESSION['username'])) {
        header('Location: ../login/login.php');
}

  $username = $_SESSION['username'];
  $courseName= $_GET['courseName'];
  $date=$_GET['date'];
  include_once ('../dbCon.php');

  $questions_id = ORM::for_table('questions')
                     ->select('id')
                     ->where(array(
                         'course' => $courseName,
                         'date' => $date
                       ))
                     ->find_many();
  $questions_count = $questions_id -> count();

  $user_type = ORM::for_table('user')
          ->select('type')
          ->where('username', $username)
          ->find_one();
?>
<html>
  <head>
    <meta charset="utf-8">
  <link href='http://fonts.googleapis.com/css?family=Pacifico' rel='stylesheet' type='text/css'>
    <title>Question List | Clicker2GO</title>
    <link rel="stylesheet" href="../style.css">
  </head>

  <body class="homepage">
      <div>
      <span><a href='../index.php'>Home</a></span>>
      <span><a href="datePage.php?courseName=<?php echo $courseName; ?>"><?php echo $courseName; ?></a></span>>
      <span><?php echo $date; ?></span></div>
  <ol>
  <?php
  $i=1;
  if ($questions_count != 0) {
  foreach ($questions_id as $each_qid) {
    $qid = $each_qid->id
    echo "<li><a href='question-answer.php?qid=$qid'>Question $i</a></li>";
    $i++;
    }}
  else {
    echo "<li>No Question</br>(This date page will not be saved if no question added)</li>"; 
  } 
  ?>
  </ol>

  <form method="POST" action="createQuestion.php"
  <?php if($user_type == 'student') {
    echo 'style="visibility:hidden"'; }?> >
  <input type="text" name="courseName" style="visibility:hidden" value="<?php echo $courseName;?>"/>
  <input type="text" name="date" style="visibility:hidden" value="<?php echo $date;?>"/></br>
  <input type="submit" class="button" value="creat new"/>
  </form>
   
  </body>
</html>
