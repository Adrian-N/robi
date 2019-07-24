<?php

//Get Settings & Functions
include 'functions.php';
include 'settings.php';

//Show Errors
errors();

if(!isset($_GET["q"])){
	
	/********
	Default page
	*********/
	
	//software array
	$software = array();

	//db engine
	$db = new PDO('sqlite:' . $dbFile);
	$result = $db->query('SELECT * FROM software');
	foreach($result as $row)
		{
		  $software[] = $row;
		}
	$db = null;

	//software count
	$Csoftware = count($software);
	
	//javascript
	$js = '"Are you sure?"';
	
	//start table
	$output .= "<table style='text-align:left;'>";
	$output .= " <tr><th>Name</th><th>Info</th><th>Options</th></tr>";
	
	//loop & generate output
	for ($row = 0; $row < $Csoftware; $row++) {
		$output .= "\n<tr>";
		$output .= "\n<td>";
		$output .=$software[$row]['softwareName'];
		$output .= "</td>";
		$output .= "\n<td>";
		$output .=$software[$row]['softwareInfo'];
		$output .= "</td>";
		$output .= "\n<td>";
		$output .="<a href='software.php?q=d&id=".$software[$row]['softwareId']."' onclick='return confirm(" . $js . ")' >";
		$output .= "DELETE";
		$output .="</a>\n";
		$output .="<a href='software.php?q=e&id=".$software[$row]['softwareId']."'' >";
		$output .= "EDIT";
		$output .="</a>\n";
		$output .= "</td>";
		$output .= "</td>";
		$output .= "</tr>";
	}
	
	//end table
	$output .= "</table>";
	
}else{
	
	if($_GET["q"] == "a"){
	
	/********
	Add Software Form
	*********/
	$output = '<form class="pageForm" action="software.php?q=b" method="post">';
	$output .='<input type="text" name="softwareName" placeholder="Software Name" required/><br />';
	$output .='<input type="text" name="softwareInfo" placeholder="Software Info" required/><br />';
	$output .='<input type="submit" value="Submit">';
	$output .= "</form>";
	
	}elseif($_GET["q"] == "d"){
		
		/**********
		Delete Software
		***********/
		
		if (isset($_GET["id"])){
			if (!empty($_GET["id"])){
				
				$db = new PDO('sqlite:' . $dbFile);
				$db->exec("DELETE FROM software WHERE softwareId = " . $_GET['id'] . ";");
				$db->exec("DELETE FROM asset WHERE softwareID = " . $_GET['id'] . ";");
				$db = NULL;
				$output = "SUCCESSFULLY DELETED";
			}else{ $error .= "Invalid software ID!";}
		}else{ $error .= "Invalid software ID!";}
		
	}elseif($_GET["q"] == "e"){
		
		/********
		Form to edit software
		*********/
		$output = "";
		if (isset($_GET["id"])){
			if (!empty($_GET["id"])){
				
				//Get asset ID
				$softwareID = $_GET['id'];
				
				//DB
				$db = new PDO('sqlite:' . $dbFile);
				$result = $db->query('SELECT * FROM software WHERE softwareId = ' . $softwareID);
				foreach($result as $row)
				{
				  $software[] = $row;
				}
				
				//output form
				$output .= "<form class='pageForm' action='software.php?q=f' method='post'>\n";
				$output .='<input class="input softwareID" type="text" name="softwareID" value="' . $software[0]['softwareId'] . '" readonly="readonly"/><br />';
				$output .='<input class="input softwareName" type="text" name="softwareNAME" value="' . $software[0]['softwareName'] . '" required/><br />';
				$output .='<input class="input softwareInfo" type="text" name="softwareINFO" value="' . $software[0]['softwareInfo'] . '" required/><br />';
				$output .='<input type="submit" value="Submit">';
				$output .="</form>";
				
				//close db
				$db = null;
				
			}else{ $error .= "Invalid software ID!";}
		}else{ $error .= "Invalid software ID!";}
	}elseif($_GET["q"] == "f"){
		/********
		Engine to edit software
		*********/
		$output ="";
		if (isset($_POST['softwareID'])){
			if (!empty($_POST['softwareID'])){
				if (isset($_POST['softwareNAME'])){
					if (!empty($_POST['softwareNAME'])){
						if (isset($_POST['softwareINFO'])){
							if (!empty($_POST['softwareINFO'])){
			
								$softwareID = $_POST['softwareID'];
								$softwareNAME = $_POST['softwareNAME'];
								$softwareINFO = $_POST['softwareINFO'];

								$db = new PDO('sqlite:' . $dbFile);
								$db->exec("UPDATE software SET softwareName = '" . $softwareNAME . "', softwareInfo = '" . $softwareINFO . "' WHERE softwareId = '" . $softwareID . "';");
								$db = NULL;
								
								$output .="ALL GOOD";
						
							}else{ $error .= "Empty software info!";}
						}else{ $error .= "Empty software info!";}
					}else{ $error .= "Empty software Name!";}
				}else{ $error .= "Empty software Name!";}
			}else{ $error .= "Empty software ID!";}
		}else{ $error .= "Empty software ID!";}
	}else{
		
		/********
		Engine to add software
		*********/
		if (isset($_POST['softwareName'])){
			if (!empty($_POST['softwareName'])){
				if (isset($_POST['softwareInfo'])){
					if (!empty($_POST['softwareInfo'])){
			
						$newSoftwareName = $_POST['softwareName'];
						$newSoftwareInfo = $_POST['softwareInfo'];
						$db = new PDO('sqlite:' . $dbFile);
						$db->exec("INSERT INTO software (softwareName, softwareInfo) VALUES ('" . $newSoftwareName . "', '" . $newSoftwareInfo . "');");
						$db = NULL;
						
					}else{ $error .= "Empty software info!";}
				}else{ $error .= "Empty software info!";}
			}else{ $error .= "Empty software name!";}
		}else{ $error .= "Empty software name!";}
		$output = "All Good";
	}
}

$pageTitle = "Software";
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
	<body>
		<header>
			<nav>
				<?php echo $mainNav ?>
			</nav>
		</header>
		<main>
			<section class="box">
				<h1><?php echo $pageTitle; ?></h1>
				<article class="article">
					<?php echo $output; ?>
				</article>
				<article class="subnav">
					<a style="text-decoration:none;" href='software.php?q=a'>
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