<?php
if (isset($_GET["category"]))
{
	switch ($_GET["category"])
	{
		case "US News":
			$url = "http://rss.cnn.com/rss/cnn_us.rss";
			break;
		case "World News":
			$url = "http://rss.cnn.com/rss/cnn_world.rss";
			break;
		case "Sports":
			$url = "http://api.foxsports.com/v1/rss?partnerKey=zBaFxRyGKCfxBagJG9b8pqLyndmvo7UU";
			break;
		case "Weather":
			$url = "http://www.rssweather.com/zipcode/14623/rss.php";
			break;
		case "Technology":
			$url = "https://news.google.com/news?cf=all&hl=en&ned=us&topic=t&output=rss";
			break;
		default:
			die();
	}
	
	echo file_get_contents($url);
}
?>