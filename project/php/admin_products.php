<?php
$content = new TemplatePower("template/files/admin_products.tpl");
$content->prepare();

if(isset($_GET['action']))
{
    $action = $_GET['action'];
}else{
    $action = NULL;
}
if (isset($_SESSION['accountid'])) {
    if ($_SESSION['roleid'] == 2) {
        switch ($action) {

            case "toevoegen":


                if (!empty($_POST['title'])
                    && !empty($_POST['gnaam'])
                    && !empty($_POST['content'])
                ) {
                    $product = $_POST['gnaam'];
                    $get_account = $db->prepare("SELECT count(*) FROM accounts
                                          where Username like :naam");
                    $get_account->bindParam(":naam", $_POST['gnaam']);
                    $get_account->execute();


                    if ($get_account->fetchcolumn() == 1) {

                        $accountid = $_SESSION['accountid'];

                        $insert_product = $db->prepare("INSERT INTO products SET
                      Title = :title,
                      Content = :content,
                      Accounts_idAccounts = :accountid
                      ");

                        $insert_product->bindParam(":title", $_POST['title']);
                        $insert_product->bindParam(":content", $_POST['content']);
                        $insert_product->bindParam(":accountid", $accountid);
                        $insert_product->execute();
                    }
                } else {
                    $content->newBlock("PRODFORM");
                    $content->assign("BUTTON", "toevoegen product");
                    $content->assign("GEBRUIKERSNAAM", $_SESSION['username']);
                    $content->assign("READONLY", "readonly");
                }


                break;
            case "wijzigen":

                if (isset($_POST['productid'])
                    && !empty($_POST['title'])
                    && !empty($_POST['content'])
                ) {
                    $update_product = $db->prepare("UPDATE products
                                                SET Title = :title,
                                                  Content = :content
                                              WHERE idProducts = :productsid");
                    $update_product->bindParam(":title", $_POST['title']);
                    $update_product->bindParam(":content", $_POST['content']);
                    $update_product->bindParam(":productsid", $_POST['productid']);
                    $update_product->execute();

                } else {

                    $get_product = $db->prepare("SELECT products.*, Accounts.* FROM products, Accounts
                                          WHERE Accounts.idAccounts=products.Accounts_idAccounts
                                          AND idProducts = :productid");
                    $get_product->bindParam(":productid", $_GET['idproduct']);
                    $get_product->execute();

                    $product = $get_product->fetch(PDO::FETCH_ASSOC);

                    $content->newBlock("PRODFORM");
                    $content->assign("ACTION", "index.php?pageid=8&action=wijzigen");
                    $content->assign("BUTTON", "Wijzigen product");
                    $content->assign("READONLY", "readonly");
                    $content->assign(array(
                        "TITLE" => $product['Title'],
                        "CONTENT" => $product['Content'],
                        "GEBRUIKERSNAAM" => $product['Username'],
                        "PRODUCTID" => $product['idProducts']
                    ));

                }
                break;
            case "verwijderen":

                if (isset($_POST['productid'])) {
                    $delete_product = $db->prepare("DELETE FROM products
                                        WHERE idProducts = :productid");
                    $delete_product->bindParam(":productid", $_POST['productid']);
                    $delete_product->execute();
                } else {

                    $get_product = $db->prepare("SELECT products.*, Accounts.* FROM products, Accounts
                                          WHERE Accounts.idAccounts=products.Accounts_idAccounts
                                          AND idProducts = :productid");
                    $get_product->bindParam(":productid", $_GET['idproduct']);
                    $get_product->execute();

                    $product = $get_product->fetch(PDO::FETCH_ASSOC);

                    $content->newBlock("PRODFORM");
                    $content->assign("ACTION", "index.php?pageid=8&action=verwijderen");
                    $content->assign("BUTTON", "verwijder Product");
                    $content->assign("READONLY1", "readonly");
                    $content->assign(array(
                        "TITLE" => $product['Title'],
                        "CONTENT" => $product['Content'],
                        "GEBRUIKERSNAAM" => $product['Username'],
                        "PRODUCTID" => $product['idProducts']
                    ));
                }
                break;


            default:
                $content->newBlock("PRODLIST");
                if (!empty($_POST['search'])) {
                    $get_products = $db->prepare("SELECT products.content, products.title, accounts.Username, products.idproducts
                                      FROM products, accounts
                                      WHERE products.Accounts_idAccounts = accounts.idAccounts
                                      AND (products.title LIKE :search
                                      OR accounts.Username LIKE :search1)
                                      ORDER BY products.idproducts DESC");
                    $search = "%" . $_POST['search'] . "%";
                    $get_products->bindParam(":search", $search);
                    $get_products->bindParam(":search1", $search);
                    $get_products->execute();
                    $content->assign("SEARCH", $_POST['search']);


                }
                else {

                    $get_products = $db->query("SELECT products.content, products.title, accounts.Username, products.idproducts
                                      FROM products, accounts
                                      WHERE products.Accounts_idAccounts = accounts.idAccounts
                                      ORDER BY products.idproducts DESC");

                }
                while ($products = $get_products->fetch(PDO::FETCH_ASSOC)) {
                    $inhoud = $products['content'];
                    if(strlen($inhoud)>25){
                        $inhoud = substr($products['content'],0,25)."...";
                    }
                    $content->newBlock("PRODROWA");
                    $content->assign(array(
                        "CONTENT" => $inhoud,
                        "TITLE" => $products['title'],
                        "USERNAME" => $products['Username'],
                        "PRODUCTID" => $products['idproducts']
                    ));

                }

        }
    } else {
        $content->newBlock("MELDING");
        $content->assign("MELDING", "u heeft geen rechten om deze pagina te zien");
    }
}
else{
    $content->newBlock("MELDING");
    $content->assign("MELDING", "eerst inloggen aub");
}