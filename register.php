<?php
session_start();
$msgs = array();
$email = '';
$pwd = '';
if (isset($_POST['didSubmit'])) {
  $email = isset($_POST['email']) ? $_POST['email'] : '';
  $pwd = isset($_POST['pwd']) ? $_POST['pwd'] : '';

  if (empty($email) || empty($pwd)) {
    $msgs[] = 'Email and Password fields are required';
  }

  if(count($msgs) === 0) {
    require_once('includes/dbconn.php');
    $connect = new Connection;
    $connection = $connect->getConnection();
    $sql = $connection->prepare('CALL sp_addUser(?, ?)');
    $sql->execute(array($email, $pwd));
    header('Location: index.php');
  }

}
 ?>


<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Register</title>
    <meta name="author" content="Johnathon Southworth">
    <link rel="stylesheet" href="css/master.css">
  </head>
  <body>
    <h1 class ="text-center">Register to begin note taking</h1>
    <form class="register" action="register.php" method="post">
      <fieldset>
        <legend>Register</legend>
        <?php
        //if the count of msgs is greater than zero than an error message was stored. Display those to the user
          if (count($msgs) > 0) {
            echo '<ul>';
            foreach ($msgs as $errors) {
              echo "<li>$errors</li>";
            }
            echo "</ul>";
          }
         ?>
        <input type="hidden" name="submitted" id="submitted" value="1">
        <dl>
          <dt>
            <label for="email">email:*</label>
          </dt>

          <dd>
            <input type="text" autofocus="autofocus" name="email" placeholder="Email Address" maxlength="50" required >
          </dd>

          <dt>
            <label for="password">Password:*</label>
          </dt>

          <dd>
            <input type="password" name="pwd" placeholder="Password" maxlength="230" required>
          </dd>

            <input type="submit" name="didSubmit" value="Submit">
        </dl>
        <a href="login.php">Login</a>
        <a href="logout.php">Logout?</a>
      </fieldset>

    </form>
  </body>
</html>
