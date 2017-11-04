<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<?php
//Include classes
include_once 'base.php';
include_once 'classes/Url.php';
include_once 'classes/Game.php';
include_once 'classes/Parser.php';
//include_once 'classes/Import.php';
include_once 'classes/Navbar.php';

//Get get data
$searchString = getGetAsString("search", "");
$platform = getGetAsInt("platform", 0);
$orderBy = getGetAsString("orderby", "name");
$orderDirection = getGetAsInt("order", 0);
$page = getGetAsInt("page", 0);
$pageSize = getGetAsInt("pagesize", 20);

//Get the list of games from the database
$gameList = $GLOBALS['db']->searchGames($searchString, $platform, $orderBy, $orderDirection, $page, $pageSize);

//Create navbar object
$navbar = new Navbar();
?>
<html>
    <head>
        <meta charset="UTF-8">
        <title>CompareGames</title>
    </head>
    <body>
        <?= $navbar->printNavbar() ?>
        <article>
            <form method="get">
                Search: 
                <input type="text" name="search" value="<?= $searchString ?>" />
                <select name='platform' onchange='this.form.submit()'>
                    <option value=0>--</option>
                    <?php
                    //print full list
                    foreach ($GLOBALS['db']->getPlatformList() as $id => $name) {
                        //Highlight the currently set platform
                        if ($platform == $id) {
                            print("<option selected value='" . $id . "'>" . $name . "</option>\n");
                        } else {
                            print("<option value='" . $id . "'>" . $name . "</option>\n");
                        }
                    }
                    ?>
                </select>
                <input type="submit" value="Search" />
            </form>
            <table>
                <?php
                //Print table header with order buttons
                $fields = array("name", "price", "platform", "store");
                print("<tr>");
                foreach ($fields as $field) {
                    //These headers include buttons which allow for sorting
                    print("<th><a href='index.php?search=" . $searchString . "&platform=" . $platform . "&orderby=" . $field . "&order=0'>↑</a> " . ucfirst($field) . " <a href='index.php?search=" . $searchString . "&platform=" . $platform . "&orderby=" . $field . "&order=1'>↓</a></th>");
                }
                print("</tr>\n");

                //Show a set of games depending on if the search was used
                foreach ($gameList as $game) {
                    //Get data from the game object
                    $data = $game->returnData();
                    //Print the table row
                    print("<tr>");
                    print("<td><a href='" . $data["link"] . "'>" . htmlspecialchars($data["name"]) . "</a></td>");
                    print("<td>&euro;" . sprintf('%01.2f', $data["price"]) . "</td>");
                    print("<td>" . htmlspecialchars($data["platform"]) . "</td>");
                    print("<td>" . htmlspecialchars($data["store"]) . "</td>");
                    print("</tr>\n");
                }
                ?>
            </table>
        </article>
        <footer>
            <?php
            if ($page > 0) {
                print("<a href='index.php?search=" . $searchString . "&platform=" . $platform . "&orderby=" . $orderBy . "&order=" . $orderDirection . "&page=" . ($page - 1) . "&pagesize=" . $pageSize . "'>previous page</a> ");
            } else {
                print("<u>previous page</u> ");
            }
            print("<a href='index.php?search=" . $searchString . "&platform=" . $platform . "&orderby=" . $orderBy . "&order=" . $orderDirection . "&page=" . ($page + 1) . "&pagesize=" . $pageSize . "'>next page</a> ");
            ?>
        </footer>
    </body>
</html>
