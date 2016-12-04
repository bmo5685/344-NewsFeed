<?php
$cookieName = "lastVisit";
$timeOfLastVisit = NULL;
if (isset($_COOKIE[$cookieName]))
{
	$timeOfLastVisit = $_COOKIE[$cookieName];
}
$now = time();
setcookie($cookieName, date("m-d-Y H:i:s",time()));

session_start();
?>

<!DOCTYPE HTML>
<html>
	<head>
		<title>News Feed</title>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
		<script type="text/javascript">

		var categories =
		[
			{ displayName: "US News",		queryParameter: "US+News",		cbId: "usNewsCb" },
			{ displayName: "World News",	queryParameter: "World+News",	cbId: "worldNewsCb" },
			{ displayName: "Sports",		queryParameter: "Sports",		cbId: "sportsCb" },
			{ displayName: "Weather",		queryParameter: "Weather",		cbId: "weatherCb" },
			{ displayName: "Technology",	queryParameter: "Technology",	cbId: "technologyCb" }
		];
		
		var items = [];
		
		var user = <?php if (isset($_SESSION["user"])) { echo "\"" . $_SESSION["user"] . "\""; } else { echo "null"; } ?>;
		
		function onCategoryToggled(category)
		{
			var checked = $("#" + category.cbId)[0].checked;
			
			if (checked)
			{
				getFeed(category);
			}
			else
			{
				items = items.filter(function(item)
				{
					return (item.category != category.displayName);
				});
			}
			
			updateDom();
		}
		
		function onLoginPressed()
		{
			var username = $("#username").val();
			var password = $("#password").val();
			
			if (username == "")
			{
				$("#loginMessage").css("color", "red");
				$("#loginMessage").text("Please enter a username");
				return;
			}
			
			if (password == "")
			{
				$("#loginMessage").css("color", "red");
				$("#loginMessage").text("Please enter a password");
				return;
			}
			
			$.get("login.php?username=" + encodeURIComponent(username) + "&password=" + encodeURIComponent(password)).done(function(retJson)
			{
				var ret = $.parseJSON(retJson);
				if (ret.success)
				{
					user = username;
					showUserPanel();
				}
				else
				{
					$("#loginMessage").css("color", "red");
					$("#loginMessage").text(ret.errorMessage);
					return;
				}
			});
		}
		
		function onRegisterPressed()
		{
			var username = $("#username").val();
			var password = $("#password").val();
			
			if (username == "")
			{
				$("#loginMessage").css("color", "red");
				$("#loginMessage").text("Please enter a username");
				return;
			}
			
			if (password == "")
			{
				$("#loginMessage").css("color", "red");
				$("#loginMessage").text("Please enter a password");
				return;
			}
			
			$.get("register.php?username=" + encodeURIComponent(username) + "&password=" + encodeURIComponent(password)).done(function(retJson)
			{
				var ret = $.parseJSON(retJson);
				if (ret.success)
				{
					$("#loginMessage").css("color", "green");
					$("#loginMessage").text("Success");
				}
				else
				{
					$("#loginMessage").css("color", "red");
					$("#loginMessage").text(ret.errorMessage);
					return;
				}
			});
		}
		
		function onLogoutPressed()
		{
			$.get("logout.php").done(function()
			{
				user = null;
				showLoginPanel();
			});
		}
		
		function onAllOrFavorites()
		{
			
		}
		
		function getFeed(category)
		{
			$.get("getFeed.php?category=" + category.queryParameter).done(function(data)
			{
				var xmlDoc = $.parseXML(data);
				var xml = $(xmlDoc);
				var xmlItems = xml.find("item");
				
				xmlItems.each(function()
				{
					var pubDate = $(this).find("pubDate");
					var date = (pubDate.length > 0) ? new Date(pubDate.text()) : new Date();
					
					items.push(
					{
						category: category.displayName,
						title: $(this).find("title").text(),
						url: $(this).find("link").text(),
						date: date
					});
				});
				
				items.sort(function(a, b)
				{
					return b.date - a.date;
				});
				
				updateDom();
			});
		}
		
		function updateDom()
		{
			$("#items").empty();
			
			$.each(items, function(index, item)
			{
				$("#items").append("[" + item.category + "] <a href=\"" + item.url + "\">" + item.title + "</a><br>");
			});
		}
		
		function showLoginPanel()
		{
			$("#userLogin").empty();
			$("#userLogin").append("Username: <input type=\"text\" id=\"username\"> Password: <input type=\"text\" id=\"password\"> <button id=\"login\">Log In</button> <button id=\"register\">Register</button> <span id=\"loginMessage\">");
			$("#login").on("click", function()
			{
				onLoginPressed();
			});
			$("#register").on("click", function()
			{
				onRegisterPressed();
			});
		}
		
		function showUserPanel()
		{
			$("#userLogin").empty();
			$("#userLogin").append("Logged in as " + user + " <select id=\"allOrFavorites\"><option value=\"all\">Show All</option><option value=\"favorites\">Show Favorites</option></select> <button id=\"logout\">Log Out</button>");
			$("#allOrFavorites").on("change", function()
			{
				onAllOrFavorites();
			});
			$("#logout").on("click", function()
			{
				onLogoutPressed();
			});
		}	
		
		$(document).ready(function()
		{
			// add a check box for each category
			$.each(categories, function(index, category)
			{
				$("#filters").append("<label><input type=\"checkbox\" id=\"" + category.cbId + "\" checked>" + category.displayName + "</label>");
				$("#" + category.cbId)[0].onchange = function()
				{
					onCategoryToggled(category);
				};
			});
			
			$.each(categories, function(index, category)
			{
				getFeed(category);
			});
			
			if (user !== null)
			{
				showUserPanel();
			}
			else
			{
				showLoginPanel();
			}
		});
		
		</script>
	</head>
	<body>
		<?php
		if ($timeOfLastVisit != NULL)
		{
		?>
		<div id="lastVisit">Your last visit was on <?php echo $timeOfLastVisit; ?></div> 
		<?php
		}
		?>
		<div id="userLogin"></div>
		<div id="filters"></div>
		<div id="items"></div>
	</body>
</html>