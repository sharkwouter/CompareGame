<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<?php
//Include classes
include_once 'base.php';
require_once 'classes/Navbar.php';

//Create navbar object
$navbar = new Navbar();

//Add platform
$newPlatform = filter_input(INPUT_POST, "newplatform");
if (!empty($newPlatform)) {
    $GLOBALS["db"]->addPlatform($newPlatform);
}

//Remove platform
$remove = (int) filter_input(INPUT_POST, "remove", FILTER_VALIDATE_INT);
if (!empty($remove) && $remove <> false) {
    $GLOBALS["db"]->removePlatform($remove);
}
?>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Platforms</title>
    </head>
    <body>
        <?= $navbar->printNavbar() ?>
        <article>
            <form method="post">
                New platform: <input name="newplatform" /><input type="submit" value="Add" />
            </form>
            <table>
                <tr><th>Platforms</th><th>Action</th></tr>
                <?php foreach ($GLOBALS['db']->getPlatformList() as $id => $name) { ?>
                    <tr>
                        <td><?= $name ?></td>
                        <td>
                            <form method='post'>
                                <input type='hidden' name='remove' value='<?= $id ?>' />
                                <input type='submit' value='Remove' />
                            </form>
                        </td>
                    </tr>
                    <?php }
                ?>
            </table>
        </article>
    </body>
</html>
