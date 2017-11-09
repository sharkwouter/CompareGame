<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<?php
//Include classes
include_once 'base.php';

require_once 'classes/ParseDataObject.php';
require_once 'classes/Navbar.php';

//Create navbar object
$navbar = new Navbar();

$parseDataObjects = $GLOBALS["db"]->getParseDataObjects();
?>
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
    </head>
    <body>
        <?= $navbar->printNavbar() ?>
        <table>
            <tr><th>Store</th><th>XML Paths</th><th>Submit</th></tr>
            <?php
            foreach ($parseDataObjects as $p) {
                $data = $p->getData();?>
              <tr>
                <td>
                    <b><?=$data["store"]?></b><br>
                    <?=$data["platform"]?><br>
                </td>
            <form>
                <td>
                    <table>
                        <tr>
                            <td>Url:</td>
                            <td><input name="url" value="<?=htmlspecialchars($data["url"])?>"></td>
                        </tr>
                        <tr>
                            <td>Product query:</td>
                            <td><input name="product" value="<?=htmlspecialchars($data["product"])?>"></td>
                        </tr>
                        <tr>
                            <td>Name query:</td>
                            <td><input name="name" value="<?=htmlspecialchars($data["name"])?>"></td>
                        </tr>
                        <tr>
                            <td>Price query:</td>
                            <td><input name="price" value="<?=htmlspecialchars($data["price"])?>"></td>
                        </tr>
                        <tr>
                            <td>Link query:</td>
                            <td><input name="link" value="<?=htmlspecialchars($data["link"])?>"></td>
                        </tr>
                        <tr>
                            <td>Next page query:</td>
                            <td><input name="nextpage"  value="<?=htmlspecialchars($data["nextpage"])?>"></td>
                        </tr>
                    </table>
                </td>
                <td>
                    <input type="hidden" name="storeid" value="<?=$data["storeid"]?>" />
                    <input type="hidden" name="platformid" value="<?=$data["platformid"]?>" />
                    <input type="submit" value="Update" />
                </td>
            </form>
            </tr>
            <?php
        }
        ?>
    </table>
</body>
</html>
