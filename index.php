<?php
session_start();
require_once('includes/dbconn.php');

if (isset($_SESSION['uid'])) {
  $uid = $_SESSION['uid'];
} else {
  header('Location: login.php');
}

$connect = new Connection;
$connection = $connect->getConnection();
$msgs = array();
$rss = ''; $friendly = '';
if (isset($_POST['didSubmit'])) {
  $rss = isset($_POST['rss-url']) ? $_POST['rss-url'] : '';
  $friendly = isset($_POST['friendly']) ? $_POST['friendly'] : '';
  $friendDropDown = isset($_POST['dropDown']) ? $_POST['dropDown'] : '';
  if (empty($rss) || empty($friendly)) {
    $msgs[] = 'You must submit both friendly name and URl';
  }
  if (count($msgs) === 0) {
    $ch = curl_init();
    //set a url
    //set curl options
    curl_setopt($ch, CURLOPT_URL, $rss);
    //this returns the results rather than displaying it?
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    //this line is needed for https NOTE: this is note secure method
    // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    //execute the cURL
    $curl_data = curl_exec($ch);
    file_put_contents('feeds/xml_data.xml', $curl_data);

    //close the cURL session
    curl_close($ch);
    //set up xml result from cURL
    //do a loop for some results
    // echo "<pre>" . htmlentities($curl_data) . "</pre>";
    $sql = $connection->prepare('CALL sp_addRSS(?, ?, ?)');
    $sql->execute(array($uid, $friendly, $rss));
  } elseif (count($msgs === 1) && !empty($friendDropDown)) {
    /*if there is one error message because you don't want to submit a friendly name
    that shouldnt stop user from choosing a friendly name and loading that as a rss*/
  }
}


if (file_exists('feeds/xml_data.xml')) {
  $returned_data = file_get_contents('feeds/xml_data.xml');
  $xml = simplexml_load_string($returned_data, null, LIBXML_NOCDATA);
  $json = json_encode($xml);
}
?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="css/master.css">
    <title>Download and display RSS</title>
  </head>
  <body>
<?php
//if user didnt submit rss feed or friendly name, and no friendly tag is selected . . . but
//isnt the option selection always populated? what then?
    if(!empty($msgs) && empty($friendDropDown)) {
      foreach($msgs as $error) {
        echo $error;
      }
    }
?>
    <form class="rss-form" action="<?=$_SERVER["SCRIPT_NAME"]?>" method="post">
      <fieldset>
        <legend>RSS Input</legend>
        <dl>
          <dt><label for="rss-feed-url">RSS Feed URL</label></dt>
          <dd><input id="rss-feed-url" class="large" type="url" name="rss-url" placeholder="http://www.bbc.co.uk/news/10628494"></dd>
          <br>
          <dt><label for="friendly-name">User Friendly Name of RSS</label></dt>
          <dd><input id="friendly-name" type="text" name="friendly" placeholder="Friendly URL Name">
          <button type="submit" name="didSubmit" value="1">Submit</button></dd>
          <br>
          <dt><label for="saved-rss-list">Saved RSS</label></dt>
          <dd>
            <select id="drop" class="dropDown" name="dropDown"> <option value="">Select RSS Feed</option>
              <?php
              $fetchFriend = $connection->prepare("SELECT DISTINCT rss_url, friendly FROM rss WHERE userID = ? AND rss_url != '' ORDER BY friendly");
              $fetchFriend->execute(array($uid));
                  while($row = $fetchFriend->fetch(PDO::FETCH_ASSOC)) {
                    echo "<option value=\"" . htmlentities($row['rss_url']) . "\">" . htmlentities($row['friendly']) . "</option>";
                    }
                  ?>
            </select>
        </dd>
        </dl>

        <br>
        <hr>
        <a href="logout.php">Logout</a>
      </fieldset>
    </form>
<?php
$data = new RecursiveIteratorIterator(
    new RecursiveArrayIterator(json_decode($json, TRUE)),
    RecursiveIteratorIterator::SELF_FIRST);
?>

<table>
  <tbody>
    <tr>
      <td>Title</td>
      <td>Description</td>
      <td>Link</td>
      <td>GUID</td>
      <td>PUB Date</td>
    </tr>


<?php
foreach ($data as $key => $outerValue) {
    if ($key === 'channel') {
      foreach ($outerValue as $channelKey => $channelVal) {
        if ($channelKey === "item") {
          foreach ($channelVal as $item) {
            $pubDate = htmlentities(isset($item["pubDate"]) ? $item["pubDate"] : "");
            echo "<tr>" . "<td>" . $item['title'] . "</td>";
            echo "<td>" . $item['description'] . "</td>";
            echo "<td><small>" . $item['link'] . "</small></td>";
            echo "<td><small>" . $item['guid'] . "</small></td>";
            echo "<td>" . $pubDate . "</td>"  . "</tr>";
          }
        }
      }
    }
}
echo "</tbody>" . "</table>";
?>


<script type="text/javascript">
  var dropdown = document.getElementById('drop');
  var friendly = document.getElementById('friendly-name');
  var rss_input = document.getElementById('rss-feed-url');
  dropdown.addEventListener('change', function() {
    rss_input.value = this.options[this.selectedIndex].value;
    friendly.value = this.options[this.selectedIndex].text;
  });
</script>
  </body>
</html>
