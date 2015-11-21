<?php

/**
 * Created by PhpStorm.
 * User: Martin
 * Date: 20. 11. 2015
 * Time: 21:28
 */
class AdminPlayers
{
    private $seznam;
    private $db;

    function  __construct($spojeni){
        $this->db=$spojeni;
    }

    public function uzivatele_prehled(){
        $this->seznam=$this->db->queryOne("SELECT COUNT(*),SUM(penize),SUM(uhli),SUM(ropa),SUM(rubin) FROM accounts");
        $hracu = $this->seznam['COUNT(*)'];
        $rubin= $this->seznam['SUM(rubin)'];
        $rtrn = "<div class='miniinfo'><h1>".$this->seznam['COUNT(*)']."</h1><span>Lidí ve hře</span></div>";
        $rtrn .= "<div class='miniinfo'><h1>$".number_format($this->seznam['SUM(penize)'], 0, ',', ' ')."</h1><span>Peněz v oběhu</span></div>";
        /****************************************************************/
        $this->seznam=$this->db->queryOne("SELECT COUNT(*) FROM accounts WHERE kongresmando>='".Cas::DB_DatumCas()."'");
        $kongresmani = $this->seznam['COUNT(*)'];
        $this->seznam=$this->db->queryOne("SELECT COUNT(*) FROM accounts WHERE vipdo>='".Cas::DB_DatumCas()."'");
        $vip = $this->seznam['COUNT(*)'];
        $this->seznam=$this->db->queryOne("SELECT COUNT(*) FROM islands");
        $ostrohra = round($this->seznam['COUNT(*)'] / $hracu,2);
        $rtrn .= "<div class='miniinfo'><h1>".$ostrohra."</h1><span>Ostrovů na hráče</span></div>";
        $rtrn .= "<div class='miniinfo'><h1>".$rubin."</h1><span>Rubínů ve hře</span></div>";
        $rtrn .= "<div class='miniinfo'><h1>".$kongresmani."</h1><span>Kongresmanů</span></div>";
        $rtrn .= "<div class='miniinfo'><h1>".$vip."</h1><span>VIP</span></div>";
        return $rtrn;
    }

    public function uzivatele_menu(){
        $rtrn = "<div class='podmenu-1'><span><a href='index.php?page=uzivatele&sekce=1'>Přehled sekce</a></span></div>";
        $rtrn .= "<div class='podmenu-1'><span><a href='index.php?page=uzivatele&sekce=2'>Tabulka uživatelů</a></span></div>";
        $rtrn .= "<div class='podmenu-1'><span><a href='index.php?page=uzivatele&sekce=3'>VIP a Kongres</a></span></div>";
        $rtrn .= "<div class='podmenu-1'><span><a href='index.php?page=uzivatele&sekce=4'>Odměny hráčům</a></span></div>";
        $rtrn .= "<div class='podmenu-1'><span><a href='index.php?page=uzivatele&sekce=5'>Tresty</a></span></div>";
        return $rtrn;
    }

    public function uzivatele_sekce(){
        if(!isset($_GET['sekce']) OR $_GET['sekce'] == ""){
            $_GET['sekce'] = 1;
        }

        if($_GET['sekce']==1){

        }elseif($_GET['sekce']==2){

            echo "<h3>Vyhledej uživatele:</h3>";

            $this->uzivatele_hledani();

            $this->uzivatele_smaz();

            $this->uzivatele_editace();

            echo "<h3>Tabulka uživatelů:</h3>";


            $this->uzivatele_paginace();

            $this->uzivatele_tabulka();

            $this->uzivatele_paginace();

        }elseif($_GET['sekce']==3){

            echo "<h3>Vyhledej uživatele:</h3>";

            $this->uzivatele_hledani();

            $this->uzivatele_vip();

            $this->uzivatele_kongres();

        }elseif($_GET['sekce']==4){

            $this->uzivatele_odmena();

        }elseif($_GET['sekce']==5){

            echo "Sekce tresty zatím nefunguje...";

        }
    }

    public function uzivatele_tabulka(){
        if(!isset($_GET['paginace']) OR $_GET['paginace']<=0){
            $_GET['paginace'] = 0;
        }
        $minimum=$_GET['paginace'];

        $this->seznam=$this->db->queryAll("SELECT * FROM accounts LIMIT ".$minimum.",20");
        $vysledek = array();
        $vysledna = 0;
        foreach($this->seznam as $promena){
            $vysledna = "<tr align='center'><td>".$promena['jmeno']."</td><td>". number_format($promena['penize'],1, ',',' ')."</td><td>".$promena['rubin']."</td><td>".$promena['vipdo']."</td><td>".$promena['admin']."</td><td><a href='/admin/index.php?page=uzivatele&sekce=2&editace=".$promena['id']."'>Editovat</a> / <a href='/admin/index.php?page=uzivatele&sekce=2&smazat=".$promena['id']."'>Smazat</a></td></tr>";
            $vysledek[] = $vysledna;
        }
        $vraceni = implode("\n",$vysledek);
        $hlavicka = "<tr align='center'><th>Jméno uživatele:</th><th>Peněz:</th><th>Rubínů:</th><th>VIP do:</th><th>Admin level:</th><th>Možné akce:</th></tr>";
        echo "<table width='98%' border='1px'>".$hlavicka . $vraceni."</table>";
    }

    public function uzivatele_editace(){
        if(isset($_POST['zmenitudaje'])){
            $jmeno=$_POST['jmeno'];
            $heslo=$_POST['heslo'];
            $avatar=$_POST['avatar'];
            $penize=$_POST['penize'];
            $dluh=$_POST['dluh'];
            $uhli=$_POST['uhli'];
            $ropa=$_POST['ropa'];
            $rubin=$_POST['rubin'];
            $maxprodej=$_POST['maxprodej'];
            $kongresmando=$_POST['kongresmando'];
            $vipdo=$_POST['vipdo'];
            $admin=$_POST['admin'];

            $this->db->query("UPDATE `accounts` SET `jmeno` = '$jmeno', `heslo` = '$heslo', `avatar` = '$avatar', `penize` = '$penize', `dluh` = '$dluh', `uhli` = '$uhli', `ropa` = '$ropa', `rubin` = '$rubin', `maxprodej` = '$maxprodej', `kongresmando` = '$kongresmando', `vipdo` = '$vipdo', `admin` = '$admin' WHERE `id` = '".$_GET['editace']."';");
        }

        if(isset($_GET['editace'])){
            $radku=$this->db->queryOne("SELECT COUNT(*) FROM accounts WHERE id='".$_GET['editace']."'");
            if($radku['COUNT(*)']=="1"){
                $hrac=$this->db->queryOne("SELECT * FROM accounts WHERE id='".$_GET['editace']."'");
                echo "<div class='editace'>";
                echo "<form method='post'>";
                echo "Editace uživatele: ".$hrac['id'];
                echo "<table width='100%' border='1px'>";
                echo "<tr align='center'><td>Uživatel jméno:</td><td>".$hrac['jmeno']."</td><td><input type='text' name='jmeno' value='".$hrac['jmeno']."'></td></tr>";
                echo "<tr align='center'><td>Uživatel heslo:</td><td>".$hrac['heslo']."</td><td><input type='text' name='heslo' value='".$hrac['heslo']."'></td></tr>";
                echo "<tr align='center'><td>Uživatel avatar:</td><td>".$hrac['avatar']."</td><td><input type='text' name='avatar' value='".$hrac['avatar']."'></td></tr>";
                echo "<tr align='center'><td>Uživatel peněz:</td><td>".number_format($hrac['penize'],1, ',',' ')."</td><td><input type='text' name='penize' value='".$hrac['penize']."'></td></tr>";
                echo "<tr align='center'><td>Uživatel dluh:</td><td>".$hrac['dluh']."</td><td><input type='text' name='dluh' value='".$hrac['dluh']."'></td></tr>";
                echo "<tr align='center'><td>Uživatel uhlí:</td><td>".$hrac['uhli']."</td><td><input type='text' name='uhli' value='".$hrac['uhli']."'></td></tr>";
                echo "<tr align='center'><td>Uživatel ropy:</td><td>".$hrac['ropa']."</td><td><input type='text' name='ropa' value='".$hrac['ropa']."'></td></tr>";
                echo "<tr align='center'><td>Uživatel rubínů:</td><td>".$hrac['rubin']."</td><td><input type='text' name='rubin' value='".$hrac['rubin']."'></td></tr>";
                echo "<tr align='center'><td>Uživatel maxprodej:</td><td>".$hrac['maxprodej']."</td><td><input type='text' name='maxprodej' value='".$hrac['maxprodej']."'></td></tr>";
                echo "<tr align='center'><td>LastSave:</td><td>".$hrac['lastsave']."</td><td>--Nelze editovat--</td></tr>";
                echo "<tr align='center'><td>Kongres:</td><td>".$hrac['kongresmando']."</td><td><input type='text' name='kongresmando' value='".$hrac['kongresmando']."'></td></tr>";
                echo "<tr align='center'><td>VIP do:</td><td>".$hrac['vipdo']."</td><td><input type='text' name='vipdo' value='".$hrac['vipdo']."'></td></tr>";
                echo "<tr align='center'><td>Admin levl:</td><td>".$hrac['admin']."</td><td><input type='text' name='admin' value='".$hrac['admin']."'></td></tr>";
                echo "<tr align='center'><td colspan='3'><input type='submit' name='zmenitudaje' value='..: Změnit údaje :..'></td></tr>";
                echo "</table>";
                echo "</form>";
                echo "</div>";
            }else{
                echo "<div class='chyba'>Hledaný uživatel neexistuje</div>";
            }
        }
    }

    public function uzivatele_smaz(){
        if(isset($_POST['smaz_hrace'])){
            $this->db->query("DELETE FROM `accounts` WHERE `id` = '".$_GET['smazat']."'");
            echo "<div class='centruj'><h3>Hráč byl smazán</h3></div>";
        }

        if(isset($_GET['smazat'])){
            $radku=$this->db->queryOne("SELECT COUNT(*) FROM accounts WHERE id='".$_GET['smazat']."'");
            if($radku['COUNT(*)']=="1"){
                $id = $_GET['smazat'];
                $hrac=$this->db->queryOne("SELECT * FROM accounts WHERE id='".$_GET['smazat']."'");
                echo "<form method='post'>";
                echo "<div class='centruj'>";
                echo "<fieldset class='okenko'>";
                echo "Opravdu chcete smazat tohoto hráče: <b>".$hrac['jmeno']."</b><br>";
                echo "<input type='submit' name='smaz_hrace' value='..:Smazat uživatele:..'>";
                echo "</fieldset>";
                echo "</div>";
                echo "</form>";
            }else{
                echo "<div class='chyba'>Nelze smazat hráče, který neexistuje</div>";
            }
        }
    }

    public function uzivatele_hledani(){
        if(isset($_POST['hledej_hrace'])){
            $hrac = $_POST['nickname'];
            $id = $_POST['id'];
            if(!empty($id)){
                $_GET['editace'] = $id;
            }
            if(!empty($hrac)){
                $hrac=$this->db->queryOne("SELECT * FROM accounts WHERE jmeno='".$hrac ."'");
                $_GET['editace'] = $hrac['id'];
                $id = $hrac['id'];
            }
            if($_GET['sekce']=="2"){
                header('Location: index.php?page=uzivatele&sekce=2&editace='.$id.'');
            }elseif($_GET['sekce']=="3"){
                header('Location: index.php?page=uzivatele&sekce=3&editace='.$id.'');
            }
        }

        echo "<form method='post'>";
        echo "<div class='centruj'>";
        echo "<fieldset class='okenko'>";
        echo "<table>";
        echo "<tr><td>Jméno:</td><td><input type='text' name='nickname'></td></tr>";
        if(isset($_GET['editace'])){
            echo "<tr><td>ID:</td><td><input type='text' name='id' value='".$_GET['editace']."'></td></tr>";
        }elseif(isset($_GET['smazat'])){
            echo "<tr><td>ID:</td><td><input type='text' name='id' value='".$_GET['smazat']."'></td></tr>";
        }else{
            echo "<tr><td>ID:</td><td><input type='text' name='id' value=''></td></tr>";
        }
        echo "<tr><td colspan='2'><input type='submit' name='hledej_hrace' value='..:Hledat uživatele:..'></td></tr>";
        echo "</table>";
        echo "</fieldset>";
        echo "</div>";
        echo "</form>";
    }

    public function uzivatele_paginace(){
        if(!isset($_GET['paginace']) OR $_GET['paginace']<=0){
            $_GET['paginace'] = 0;
        }
        $minimum=$_GET['paginace']-20;
        $maximum=$_GET['paginace']+20;
        echo "<div class='centruj'><a href='index.php?page=uzivatele&sekce=2&paginace=$minimum'><<< Posunout o dvacet</a> / <a href='index.php?page=uzivatele&sekce=2&paginace=$maximum'>Posunout o dvacet >>></a></div>";

    }

    public function uzivatele_vip(){
        if(!isset($_GET['editace']) OR $_GET['editace']==""){
            $_GET['editace'] = 0;
        }
        if($_GET['editace']>=1){
            if(isset($_POST['udel_vip'])){

                $idcko=$_GET['editace'];
                $hrac=$this->db->queryOne("SELECT * FROM accounts WHERE jmeno=?",array($idcko));
                $doba=$_POST['doba'];
                $dokdy = date("Y-m-d H:i:s",strtotime(date("Y-m-d H:i:s") . "+".$doba." days"));
                echo $dokdy;
                $this->db->query("UPDATE `accounts` SET `vipdo` = ? WHERE `id` = ?;",array($dokdy,$idcko));
            }
            echo "<form method='post'>";
            echo "<div class='centruj'>";
            echo "<fieldset class='okenko'>";
            echo "<table width='100%'>";
            echo "<tr><td>ID:</td><td>".$_GET['editace']."</td></tr>";
            echo "<tr><td>Doba:</td><td><select name='doba'><option value='30'>30 dní</option><option value='60' selected='selected'>60 dní</option></select></td></tr>";
            echo "<tr><td colspan='2'><input type='submit' name='udel_vip' value='..:Udělit VIP:..'></td></tr>";
            echo "</table>";
            echo "</fieldset>";
            echo "</div>";
            echo "</form>";
        }
    }

    public function uzivatele_kongres(){
        if(!isset($_GET['editace']) OR $_GET['editace']==""){
            $_GET['editace'] = 0;
        }
        if(isset($_POST['udel_kongres'])){

            $idcko=$_GET['editace'];
            $hrac=$this->db->queryOne("SELECT * FROM accounts WHERE jmeno=?",array($idcko));
            $doba=$_POST['doba'];
            $dokdy = date("Y-m-d H:i:s",strtotime(date("Y-m-d H:i:s") . "+".$doba." days"));
            echo $dokdy;
            $this->db->query("UPDATE `accounts` SET `kongresmando` = ? WHERE `id` = ?;",array($dokdy,$idcko));
        }
        if($_GET['editace']>=1){
            echo "<form method='post'>";
            echo "<div class='centruj'>";
            echo "<fieldset class='okenko'>";
            echo "<table width='100%'>";
            echo "<tr><td>ID:</td><td>".$_GET['editace']."</td></tr>";
            echo "<tr><td>Doba:</td><td><select name='doba'><option value='30'>30 dní</option><option value='60' selected='selected'>60 dní</option></select></td></tr>";
            echo "<tr><td colspan='2'><input type='submit' name='udel_kongres' value='..:Udělit kongres..'></td></tr>";
            echo "</table>";
            echo "</fieldset>";
            echo "</div>";
            echo "</form>";
        }
    }

    public function uzivatele_odmena(){
        if(isset($_POST['udel_odmenu'])){
            $hrac3 = $_POST['jmeno'];
            $id = $_POST['idcko'];
            $typodmeny =$_POST['odmenatyp'];
            $pocet =$_POST['pocet'];
            if(!empty($id)){
                $hrac=$this->db->queryOne("SELECT * FROM accounts WHERE id='".$id ."'");
                $id = $hrac['id'];
                $jmeno = $hrac['jmeno'];
            }
            if(!empty($hrac3)){
                $hrac2=$this->db->queryOne("SELECT * FROM accounts WHERE jmeno='".$hrac ."'");
                $id = $hrac['id'];
                $jmeno = $hrac2['jmeno'];
            }
            if($typodmeny == "rub") {
                $this->db->query("UPDATE accounts SET rubin = ? WHERE id = ?;",array($pocet,$id));
            }elseif($typodmeny == "rop"){
                $this->db->query("UPDATE accounts SET ropa = ? WHERE id = ?;",array($pocet,$id));
            }elseif($typodmeny == "uhli"){
                $this->db->query("UPDATE accounts SET uhli = ? WHERE id = ?;",array($pocet,$id));
            }
        }
        echo "<form method='post'>";
        echo "<div class='centruj'>";
        echo "<fieldset class='okenko'>";
        echo "<table width='100%'>";
        echo "<tr><td>ID:</td><td><input type='text' name='idcko' value='"; if(isset($id)){echo $id;} echo"'></td></tr>";
        echo "<tr><td>Jméno:</td><td><input type='text' name='jmeno' value='"; if(isset($jmeno)){echo $jmeno;} echo"'></td></tr>";
        echo "<tr><td>Typ odměny:</td><td><select name='odmenatyp'><option value='rub'>Rubíny</option><option value='rop'>Ropa</option><option value='uhli'>Uhlí</option></select></td></tr>";
        echo "<tr><td>Počet:</td><td><input type='text' name='pocet'></td></tr>";
        //echo "<tr><td>Důvod:</td><td><input type='text' name='duvod'></td></tr>";
        echo "<tr><td colspan='2'><input type='submit' name='udel_odmenu' value='..:Udělit odměnu..'></td></tr>";
        echo "</table>";
        echo "</fieldset>";
        echo "</div>";
        echo "</form>";
    }

}