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
require_once 'classes/Store.php';
require_once 'classes/Navbar.php';

//Create navbar object
$navbar = new Navbar();

//Get get data
$selectedStore = filter_input(INPUT_GET, "store");
$selectedPlatform = filter_input(INPUT_GET, "platform");

$update = filter_input(INPUT_POST, "update");
if(isset($update)){
    $required = array("storeid","platformid","url","product","name","price","link","nextpage");
    $createObjectArray = array();
    foreach($required as $d){
        $input = htmlspecialchars_decode(filter_input(INPUT_POST, $d));
        $createObjectArray[$d] = $input;
    }
    
    //Add some other required data to the arry which isn't important, but required for creating a ParseDataObject
    $createObjectArray["store"] = "";
    $createObjectArray["platform"] = "";
    $createObjectArray["lastupdate"] = "";
    
    //Add data to database
    $GLOBALS["db"]->addParse(new ParseDataObject($createObjectArray));
}


//Get data from the parse database
if (!empty($selectedStore) && !empty($selectedPlatform)) {
    $parseDataObject = $GLOBALS["db"]->getParseDataObject($selectedStore, $selectedPlatform);
    $data = $parseDataObject->getData();
}
?>
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
    </head>
    <body>
        <?= $navbar->printNavbar() ?>
        <form>
            <select name='store'>
                <?php
                //print full list
                foreach ($GLOBALS['db']->getStores() as $store) {
                    //Highlight the currently set platform
                    $id = $store->getId();
                    if ($selectedStore == $id) {
                        print("<option selected value='" . $id . "'>" . $store . "</option>\n");
                    } else {
                        print("<option value='" . $id . "'>" . $store . "</option>\n");
                    }
                }
                ?>
            </select>
            <select name='platform'>
                <?php
                //print full list
                foreach ($GLOBALS['db']->getPlatformList() as $id => $name) {
                    //Highlight the currently set platform
                    if ($selectedPlatform == $id) {
                        print("<option selected value='" . $id . "'>" . $name . "</option>\n");
                    } else {
                        print("<option value='" . $id . "'>" . $name . "</option>\n");
                    }
                }
                ?>
            </select>
            <input type="submit" value="Pick">
        </form>
        <form method="post">
            <?php if (!empty($selectedStore) && !empty($selectedPlatform)) { ?>
                <table>
                    <tr>
                        <td>Url:</td>
                        <td><input name="url" value="<?= htmlspecialchars($data["url"]) ?>"></td>
                    </tr>
                    <tr>
                        <td>Product query:</td>
                        <td><input name="product" value="<?= htmlspecialchars($data["product"]) ?>"></td>
                    </tr>
                    <tr>
                        <td>Name query:</td>
                        <td><input name="name" value="<?= htmlspecialchars($data["name"]) ?>"></td>
                    </tr>
                    <tr>
                        <td>Price query:</td>
                        <td><input name="price" value="<?= htmlspecialchars($data["price"]) ?>"></td>
                    </tr>
                    <tr>
                        <td>Link query:</td>
                        <td><input name="link" value="<?= htmlspecialchars($data["link"]) ?>"></td>
                    </tr>
                    <tr>
                        <td>Next page query:</td>
                        <td><input name="nextpage"  value="<?= htmlspecialchars($data["nextpage"]) ?>"></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>
                            <input type="hidden" name="storeid" value="<?= $data["storeid"] ?>" />
                            <input type="hidden" name="platformid" value="<?= $data["platformid"] ?>" />
                            <input type="submit" name="update" value="Update" />
                        </td>
                    </tr>
                </table>
            </form>
        <?php }
        ?>
    </body>
</html>
