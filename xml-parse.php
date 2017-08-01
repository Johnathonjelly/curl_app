<?php
echo <<<EOS
<style type="text/css" media="all">
.rss-table {
	border: 1px solid;
	border-collapse: collapse;
}
.rss-table th, .rss-table td {
	border: 1px solid;
}
</style>
EOS;

$debug = false;

$xml = file_get_contents("bbc.xml");
$dom = simplexml_load_string($xml, null, LIBXML_NOCDATA);
$json = json_encode($dom);
$obj = json_decode($json, true);

if ($debug) {
	echo "<pre>" . print_r($obj, true) . "</pre>";
}

$headingFormat = "<tr><th>%s</th> <th>%s</th> <th>%s</th> <th>%s</th> <th>%s</th> </tr>";
$rowFormat = "<tr><td>%s</td> <td>%s</td> <td>%s</td> <td>%s</td> <td>%s</td> </tr>";

foreach ($obj as $key => $outerVal) {
	if ($key === "channel") {
		foreach ($outerVal as $channelKey => $channelVal) {
			if ($channelKey === "item") {
				echo "<table class=\"rss-table\"><thead>";
				printf($headingFormat, "Title", "Description", "Link", "GUID", "Publication Date");
				echo "</thead><tbody>";
				foreach ($channelVal as $item) {
					$title = htmlentities(isset($item["title"]) ? $item["title"] : "");
					$description = htmlentities(isset($item["description"]) ? $item["description"] : "");
					$link = htmlentities(isset($item["link"]) ? $item["link"] : "");
					$guid = htmlentities(isset($item["guid"]) ? $item["guid"] : "");
					$pubDate = htmlentities(isset($item["pubDate"]) ? $item["pubDate"] : "");
					printf(
						$rowFormat,
						$title,
						$description,
						$link,
						$guid,
						$pubDate
					);
				}
				echo "</tbody></table>";
			}
		}
	}
}