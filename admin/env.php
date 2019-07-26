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

	//env array
	$env = array();

	//db engine
	$db = new PDO('sqlite:' . $dbFile);
	$result = $db->query('SELECT * FROM environment');
	foreach($result as $row)
		{
		  $env[] = $row;
		}
	$dbh = null;

	//software count
	$Cenv= count($env);
	
	//javascript
	$js = '"Are you sure?"';
	
	//start table
	$output .= "<table style='text-align:left;'>";
	$output .= " <tr><th>Name</th><th>Info</th><th>Options</th></tr>";
	
	//loop & generate output
	for ($row = 0; $row < $Cenv; $row++) {
		$output .= "\n<tr>";
		$output .= "\n<td>";
		$output .=$env[$row]['envName'];
		$output .= "</td>";
		$output .= "\n<td>";
		$output .=$env[$row]['envInfo'];
		$output .= "</td>";
		$output .= "\n<td>";
		$output .="<a href='env.php?q=d&id=".$env[$row]['envId']."' onclick='return confirm(" . $js . ")' >";
		$output .= "DELETE";
		$output .="</a>\n";
		$output .="<a href='env.php?q=e&id=".$env[$row]['envId']."' >";
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
	Add environment Form
	*********/
	$output = '<form class="pageForm" action="env.php?q=b" method="post">';
	$output .='<input type="text" name="envName" placeholder="Environment Name" required/><br />';
	$output .='<input type="text" name="envInfo" placeholder="Environment Info" required/><br />';
	$output .='<input type="submit" value="Submit">';
	$output .= "</form>";
	
	}elseif($_GET["q"] == "d"){
		 
		/**********
		Delete env
		***********/
		
		if (isset($_GET["id"])){
			if (!empty($_GET["id"])){
				
				$db = new PDO('sqlite:' . $dbFile);
				$db->exec("DELETE FROM environment WHERE envId = " . $_GET['id'] . ";");
				$db->exec("DELETE FROM environment WHERE envID = " . $_GET['id'] . ";");
				$db = NULL;
				$output = "SUCCESSFULLY DELETED";
				
			}else{ $error .= "Invalid environment ID!";}
		}else{ $error .= "Invalid environment ID!";}
	}elseif($_GET["q"] == "e"){
		
		/********
		Form to edit env
		*********/
		$output = "";
		if (isset($_GET["id"])){
			if (!empty($_GET["id"])){
				
				//Get asset ID
				$envID = $_GET['id'];
				
				//DB
				$db = new PDO('sqlite:' . $dbFile);
				$result = $db->query('SELECT * FROM environment WHERE envId = ' . $envID);
				foreach($result as $row)
				{
				  $env[] = $row;
				}
				//print_r ($asset);
				
				//output form
				$output .= "<form class='pageForm' action='env.php?q=f' method='post'>\n";
				$output .='<input class="input envID" type="text" name="envID" value="' . $env[0]['envId'] . '" readonly="readonly"/><br />';
				$output .='<input class="input envName" type="text" name="envNAME" value="' . $env[0]['envName'] . '" required/><br />';
				$output .='<input class="input envInfo" type="text" name="envINFO" value="' . $env[0]['envInfo'] . '" required/><br />';
				$output .='<input type="submit" value="Submit">';
				$output .="</form>";
				
				//close db
				$db = null;
				
			}else{ $error .= "Invalid env ID!";}
		}else{ $error .= "Invalid env ID!";}
	
	}elseif($_GET["q"] == "f"){
		/********
		Engine to edit software
		*********/
		$output ="";
		if (isset($_POST['envID'])){
			if (!empty($_POST['envID'])){
				if (isset($_POST['envNAME'])){
					if (!empty($_POST['envNAME'])){
						if (isset($_POST['envINFO'])){
							if (!empty($_POST['envINFO'])){
			
								$envID = $_POST['envID'];
								$envNAME = $_POST['envNAME'];
								$envINFO = $_POST['envINFO'];

								$db = new PDO('sqlite:' . $dbFile);
								$db->exec("UPDATE environment SET envName = '" . $envNAME . "', envInfo = '" . $envINFO . "' WHERE envId = '" . $envID . "';");
								$db = NULL;
								
								$output .="ALL GOOD";
						
							}else{ $error .= "Empty env info!";}
						}else{ $error .= "Empty env info!";}
					}else{ $error .= "Empty env Name!";}
				}else{ $error .= "Empty env Name!";}
			}else{ $error .= "Empty env ID!";}
		}else{ $error .= "Empty env ID!";}
	}
	else{
		
		/********
		Engine to add environment
		*********/
		if (isset($_POST['envName'])){
			if (!empty($_POST['envName'])){
				if (isset($_POST['envInfo'])){
					if (!empty($_POST['envInfo'])){
			
						$newEnvName = $_POST['envName'];
						$newEnvInfo = $_POST['envInfo'];
						
						$db = new PDO('sqlite:' . $dbFile);
						
						$db->exec("INSERT INTO environment (envName, envInfo) VALUES ('" . $newEnvName . "', '" . $newEnvInfo . "');");
						$db = NULL;
				
				
					}else{ $error .= "Empty environment info!";}
				}else{ $error .= "Empty environment info!";}
			}else{ $error .= "Empty environment name!";}
		}else{ $error .= "Empty environment name!";}
		
		$output = "All Good";
		
	}
}

$pageTitle = "Environments";
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge" />
		<meta name="viewport" content="width=device-width, maximum-scale=1.0, minimum-scale=1.0, initial-scale=1.0" />
		
		<link rel="stylesheet" type="text/css" href="../theme/main.css">
		
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
					<a style="text-decoration:none;" href='env.php?q=a'>
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