<?php

/**
 * Project: Robi - Management tool to track software versions across different environments.
 * License: Non-Profit Open Software License 3.0 (NPOSL-3.0)
 * Version: V1.01
 * Author: Adrian Nowak | adriannowak.net
 * Notes: Protect admin folder with htaccess - http://www.htaccesstools.com/articles/password-protection/
 */

//Get Settings & Functions
include 'admin/functions.php';
include 'admin/settings.php';

//Show Errors
errors();

//Generate Table for homepage

	//db engine
	$db = new PDO('sqlite:admin/' . $dbFile);
	$result = $db->query('SELECT * FROM asset LEFT JOIN software ON asset.softwareID = software.softwareId LEFT JOIN environment ON asset.envID = environment.envId');
	foreach($result as $row)
		{
		  $asset[] = $row;
		}
	$dbh = null;
	//print_r($asset);
	//print("<pre>".print_r($asset,true)."</pre>");

	//asset count
	if (!empty($asset)){
		$Casset = count($asset);
		
		//javascript
		$js = '"Are you sure?"';
		
		//start table
		$output .= "<table id='myTable' style='text-align:left;'>";
		$output .= " <tr><th>Software</th><th>Environment</th><th>Version</th></tr>";
		//loop & generate output
		
		//for JS
		$js = '"Are you sure?"';
		
		for ($row = 0; $row < $Casset; $row++) {
			$output .= "\n<tr>";
			$output .= "\n<td>";
			$output .= $asset[$row]['softwareName'];
			$output .= "</td>";
			$output .= "\n<td>";
			$output .= $asset[$row]['envName'];
			$output .= "</td>";
			$output .= "\n<td>";
			$output .= $asset[$row]['version'];
			$output .= "</td>";
			$output .= "</tr>";
		}
		
		//end table
		$output .= "</table>";
	}

//Main Nav
$mainNav= "<a href='admin/asset.php'>ADMIN</a>"
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge" />
		<meta name="viewport" content="width=device-width, maximum-scale=1.0, minimum-scale=1.0, initial-scale=1.0" />
		
		<link rel="stylesheet" type="text/css" href="admin/theme/main.css">
		<title><?php echo $siteTitle;?></title>
	</head>
	<body>
		<header>
			<nav>
				<?php echo $mainNav ?>
			</nav>
		</header>
		<main>
			<section class="box">
				<div class="compLogo">
					<img src="https://www.adriannowak.net/img/placeholder_350x350-min.jpg" alt="company logo" />
				</div>
				<input class="homeFilter" type="text" id="myInput" onkeyup="myFunction()" placeholder="Software Name" title="Type in a name" />
				<article class="article">
					<?php echo $output; ?>
				</article>
			</section>
		</main>
		<footer>
			<?php echo $footer; ?>
		</footer>
		<script>
			//JS by w3schools - https://www.w3schools.com/howto/howto_js_filter_table.asp
			function myFunction() {
			  var input, filter, table, tr, td, i, txtValue;
			  input = document.getElementById("myInput");
			  filter = input.value.toUpperCase();
			  table = document.getElementById("myTable");
			  tr = table.getElementsByTagName("tr");
			  for (i = 0; i < tr.length; i++) {
				td = tr[i].getElementsByTagName("td")[0];
				if (td) {
				  txtValue = td.textContent || td.innerText;
				  if (txtValue.toUpperCase().indexOf(filter) > -1) {
					tr[i].style.display = "";
				  } else {
					tr[i].style.display = "none";
				  }
				}       
			  }
			}
		</script>
	</body>
</html>