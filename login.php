<?php
session_start();
//msgs array stores all types of messages like errors or successes and display to user
$msgs = array();
$email = ''; $pwd = '';
if (isset($_POST['didSubmit'])) {
    $email = isset($_POST['email']) ? $_POST['email'] : '';
    $pwd = isset($_POST['password']) ? $_POST['password'] : '';
    if (empty($email) || empty($pwd)) {
      $msgs[] = 'Email and/or password required to login!';
    }
    if (count($msgs) === 0) {
      require_once('includes/dbconn.php');
      $connect = new Connection;
      $connection = $connect->getConnection();
      $sql = $connection->prepare('CALL sp_login(?, ?)');
      $sql->execute(array($email, $pwd));
      $results = $sql->fetch();
      if ($results === false) {
          $msgs[] = 'Wrong username or password';
      } else {
          $_SESSION['uid'] = $results['userID'];
      header('Location: index.php');

      $msgs[] = "Success! UserID = {$_SESSION['uid']} ";
      header('Location: ./');
    }
  }
}
 ?>


 <!DOCTYPE html>
 <html>
   <head>
     <meta charset="utf-8">
     <title>Login</title>
   </head>
   <body>
     <div class="wrapper">
       <form class="loginForm" action="<?=$_SERVER["SCRIPT_NAME"]?>" method="post">
         <fieldset>
           <legend>Login</legend>
           <?php
            if (count($msgs) > 0) {
              echo '<ul>';
              foreach ($msgs as $error ) {
                echo "<li>$error</li>";
              }
              echo "</ul>";
            }
            ?>
            <dl>
              <dt>
                <label for="email">Use Email To Login</label>
              </dt>
              <dd>
                <input type="text" name="email" placeholder="email@gmail.com"
                value="<?=htmlentities($email);?>" autofocus="autofocus">
              </dd>
              <dt>
                <label for="password">Password</label>
              </dt>
              <dd>
                <input type="password" name="password" placeholder="Password">
              </dd>
            </dl>
            <button type="submit" name="submit">Submit</button>
            <input type="hidden" name="didSubmit" value="1">
            <hr>
            <a href="register.php">No Account? Create one!</a>
            <hr>
            <a href="logout.php">Logout?</a>
         </fieldset>

       </form>
     </div>
   </body>
 </html>
