<?php

//Get Settings & Functions
include 'functions.php';
include 'settings.php';

//Show Errors
errors();

//asset array
$asset = array();

if(!isset($_GET["q"])){

	//db engine
	$db = new PDO('sqlite:' . $dbFile);
	$result = $db->query('SELECT * FROM asset LEFT JOIN software ON asset.softwareID = software.softwareId LEFT JOIN environment ON asset.envID = environment.envId');
	foreach($result as $row)
		{
		  $asset[] = $row;
		}
	$dbh = null;
	//print_r($asset);
	//print("<pre>".print_r($asset,true)."</pre>");

	//asset count
	$Casset = count($asset);
	
	//javascript
	$js = '"Are you sure?"';
	
	//start table
	$output .= "<table class='pageForm' style='text-align:left;'>";
	$output .= " <tr><th>Software</th><th>Environment</th><th>Version</th><th>Options</th></tr>";
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
		
		$output .= "\n<td>";
		$output .= "<a href='asset.php?q=d&id=" . $asset[$row]['assetId'] . "' onclick='return confirm(" . $js . ")'>DELETE</a>";
		$output .= "<a href='asset.php?q=e&id=" . $asset[$row]['assetId'] . "'>  EDIT</a>";
		$output .= "</td>";
		
		$output .= "</tr>";
	}
	
	//end table
	$output .= "</table>";
}else{
	if($_GET["q"] == "d"){
		/**********
		Engine to Delete Software
		***********/
		
		if (isset($_GET["id"])){
			if (!empty($_GET["id"])){
				
				$db = new PDO('sqlite:' . $dbFile);
				$db->exec("DELETE FROM asset WHERE assetId = " . $_GET['id'] . ";");
				$db = NULL;
				$output .= "SUCCESSFULLY DELETED";
				
			}else{ $error .= "Invalid asset ID!";}
		}else{ $error .= "Invalid asset ID!";}
		
	}elseif($_GET["q"] == "a"){
		
		/**********
		Add Asset
		***********/
		
		//get data from db
		$db = new PDO('sqlite:' . $dbFile);
		$result = $db->query('SELECT * FROM software');
		foreach($result as $row){
			$software[] = $row;
		}
		$dbh = null;
		
		//software count
		$Csoftware = count($software);
	
		//output
		$output .= "<form class='pageForm' action='asset.php?q=b' method='post'>\n";
		
		$output .= "<select name='software'>\n";
		//looped output
		for ($row = 0; $row < $Csoftware; $row++) {
		$output .= "<option value='";
		$output .= $software[$row]['softwareId'];
		$output .="'>";
		$output .=$software[$row]['softwareName'];
		$output .="</option>\n";
		}
		$output .= "</select>\n";
		
		//get data from db
		$db = new PDO('sqlite:' . $dbFile);
		$result = $db->query('SELECT * FROM environment');
		foreach($result as $row){
			$environment[] = $row;
		}
		$dbh = null;
		
		//environment count
		$Csoftware = count($environment);
		
		$output .= "<select name='environment'>\n";
		//looped output
		for ($row = 0; $row < $Csoftware; $row++) {
		$output .= "<option value='";
		$output .= $environment[$row]['envId'];
		$output .="'>";
		$output .=$environment[$row]['envName'];
		$output .="</option>\n";
		}
		$output .= "</select>\n";
		
		//continue output
		$output .='<input type="text" name="assetVERSION" placeholder="Asset Version" required/><br />';
		$output .='<input type="submit" value="Submit">';
		$output .= "</form>";
	}elseif($_GET["q"] == "b"){
		
		/********
		Engine to add asset
		*********/
		if (isset($_POST['software'])){
			if (!empty($_POST['software'])){
				if (isset($_POST['environment'])){
					if (!empty($_POST['environment'])){
						if (isset($_POST['assetVERSION'])){
							if (!empty($_POST['assetVERSION'])){
			
								$newSoftwareID = $_POST['software'];
								$newEnvID = $_POST['environment'];
								$newAssetVERSION = $_POST['assetVERSION'];
								
								$db = new PDO('sqlite:' . $dbFile);
								
								$db->exec("INSERT INTO asset (softwareID, envID, version) VALUES ('" . $newSoftwareID . "', '" . $newEnvID . "', '" . $newAssetVERSION . "');");
								$db = NULL;
				
							}else{ $error .= "Empty assetVERSION!";}
						}else{ $error .= "Empty assetVERSION!";}
					}else{ $error .= "Empty environment info!";}
				}else{ $error .= "Empty environment info!";}
			}else{ $error .= "Empty software name!";}
		}else{ $error .= "Empty software name!";}
		
		$output = "All Good";

	}elseif($_GET["q"] == "e"){
		
		/********
		Form to edit asset
		*********/
		//$output .= "Edit asset version:  ";
		if (isset($_GET["id"])){
			if (!empty($_GET["id"])){
				
				//Get asset ID
				$assetID = $_GET['id'];
				
				//DB
				$db = new PDO('sqlite:' . $dbFile);
				$result = $db->query('SELECT * FROM asset WHERE assetId = ' . $assetID);
				foreach($result as $row)
				{
				  $asset[] = $row;
				}
				
				//output form
				$output .= "<form class='pageForm' action='asset.php?q=g' method='post'>\n";
				$output .='<input class="input assetID" type="text" name="assetID" value="' . $asset[0]['assetId'] . '" readonly="readonly"/><br />';
				$output .='<input class="input assetVERSION" type="text" name="assetVERSION" value="' . $asset[0]['version'] . '" required/><br />';
				$output .='<input type="submit" value="Submit">';
				$output .="</form>";
				
				//close db
				$db = null;
				
			}else{ $error .= "Invalid asset ID!";}
		}else{ $error .= "Invalid asset ID!";}
	}elseif($_GET["q"] == "g"){
		if (isset($_POST['assetID'])){
			if (!empty($_POST['assetID'])){
				if (isset($_POST['assetVERSION'])){
					if (!empty($_POST['assetVERSION'])){
			
						$ASSETID = $_POST['assetID'];
						$ASSETVERSION = $_POST['assetVERSION'];

						$db = new PDO('sqlite:' . $dbFile);
						$db->exec("UPDATE asset SET version = '" . $ASSETVERSION . "' WHERE assetId = '" . $ASSETID . "';");
						$db = NULL;

						$output .="ALL GOOD";
				
					}else{ $error .= "Empty software info!";}
				}else{ $error .= "Empty software info!";}
			}else{ $error .= "Empty software name1!";}
		}else{ $error .= "Empty software name2!";}
	}
}

$pageTitle = "Assets";
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge" />
		<meta name="viewport" content="width=device-width, maximum-scale=1.0, minimum-scale=1.0, initial-scale=1.0" />
		
		<link rel="stylesheet" type="text/css" href="theme/main.css">
		
		<title><?php echo $siteTitle;?></title>
	</head>
	<header>
		<nav>
			<?php echo $mainNav ?>
		</nav>
	</header>
	<body>
		<main>
			<section class="box">
				<h1><?php echo $pageTitle; ?></h1>
				<article class="article">
					<?php echo $output; ?>
				</article>
				<article class="subnav">
					<a style="text-decoration:none;" href='asset.php?q=a'>
						<img style="width:35px; margin-top:10px;" src='https://www.adriannowak.net/img/add.png' alt='Add Software' />
					</a>
				</article>
				<article class="article">
					<?php if (!empty($error)){echo $error; }?>
				</article>
			</section>
		</main>
		<footer>
			<?php echo $footer; ?>
		</footer>
	</body>
</html>