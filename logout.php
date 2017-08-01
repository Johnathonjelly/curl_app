<?php
session_start();
 ?>
 <!DOCTYPE html>
 <html>
   <head>
     <meta charset="utf-8">
     <title>Logout</title>
   </head>
   <body>
     <p>You have been logged out.</p>
     <script type="text/javascript">
      window.setTimeout( () => {
        window.location = 'login.php';
      }, 2400);

     </script>
   </body>
 </html>
 <?php
 session_destroy(); 
 ?>
