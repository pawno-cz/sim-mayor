<!DOCTYPE html>
<html>
<head>	
<meta charset="UTF-8">
<link rel="stylesheet" type="text/css" href="style.css">
<title>SimMayor</title>
</head>

<body>

<header>
	<img src="logo.png" alt="logo">
	<?php include "menu.php";?>
</header>

<div id="page">

	
	<div id="content">
			
					<?php 
			if(!isset($_SESSION['prihlasen']) || $_SESSION['prihlasen'] == "")
			{
				header('Location: index.php');
			}else{
				require "./classes/Database.class.php";
				require "./classes/Player.class.php";
				require "./classes/Obchod.class.php";
				include_once "./cfg/host.php";
				$db = new Database(DB_HOST,DB_USER,DB_PASS,DB_NAME);
				$player = new Player($db,array("*",$_SESSION['prihlasen']));

				// ************* VYHODNOCENÍ DOTAZŮ  ****************//
				echo $player->PrepniOstrov();
				echo $player->VymazOstrov();
				$player->KupOstrov();
				$player->setKongresman();
				@Obchod::giveToShop($db,$player);
				//*****************************//

				// 23/24.10.2015 Poslední úprava
				// ************* FORMULÁŘE A VÝPISY  ****************//
				echo "<div id='usershow'>";
				echo "<table width='100%'>";
				echo "<tr><td>".$player->getAvatar()."</td><td>".$player->getName()."</td></tr>";
				echo "<tr><td rowspan='2'>".$player->KoupeOstrova()."<br>".@Obchod::sellForm($player->getComodities())."<br>".$player->KongresForm()."</td><td>".$player->statsTable()."</td></tr>";
				echo "<tr><td>".$player->VolbaOstrova()."</td></tr>";
				echo "</table>";
				echo "</div>";
				//*****************************//
			}
			?>
			
			



	<div style="clear: left;"></div>
</div>
</div>
<footer>Copyright Jan "Jenkings" Škoda | Code by <a href="http://jenkings.eu">Jenkings</a> | Design by <a href="#">Ex_0</footer>
<?php include "analytics.php";?>
</body>
</html>

