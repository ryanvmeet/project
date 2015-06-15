<?php
$content = new TemplatePower("template/files/product.tpl");
$content->prepare();

if(isset($_GET['action']))
{
    $action = $_GET['action'];
}else{
    $action = NULL;
}
switch ($action) {

    case "details":
        $check_products = $db->prepare("SELECT count(*)
                                        FROM products
                                        WHERE idproducts = :idproducts");
        $check_products->bindParam(":idproducts", $_GET['projectid']);
        $check_products->execute();
        if($check_products->fetchColumn() == 1 ) {
            $get_products = $db->prepare("SELECT products.content, products.title, accounts.Username, products.idproducts
                                      FROM products, accounts
                                      WHERE products.Accounts_idAccounts = accounts.idAccounts
                                      AND products.idproducts = :idproducts");
            $get_products->bindParam(":idproducts", $_GET['projectid']);
            $get_products->execute();
            $products = $get_products->fetch(PDO::FETCH_ASSOC);
            $content->newBlock("PRODROW");
            $content->assign(array(
                "CONTENT" => $products['content'],
                "TITLE" => $products['title'],
                "USERNAME" => $products['Username'],
                "PRODUCTID" => $products['idproducts']));
            if (isset($_SESSION['accountid'])) {
                $content->newBlock("DETAILSFORM");
                $content->assign("PRODUCTID", $products['idproducts']);
            } else {
                $content->newBlock("NEE");
            }

            $get_comments = $db->prepare("SELECT c.text, a.Username, p.idproducts, c.idcomments
                                      FROM comments as c,products as p, accounts as a
                                      WHERE p.idproducts = c.products_idproducts
                                      AND c.products_idproducts = :idproducts
                                      AND c.accounts_idaccounts = a.idaccounts
                                      ORDER BY c.idcomments DESC
                                      ");
            $get_comments->bindParam(":idproducts", $_GET['projectid']);
            $get_comments->execute();
            while ($comments = $get_comments->fetch(PDO::FETCH_ASSOC)) {
                $content->newBlock("COMMENTS");
                $content->assign(array(
                    "TEXT" => $comments['text'],
                    "USERNAME" => $comments['Username'],
                    "COMMENTID" => $comments['idcomments'],
                    "PRODUCTID" => $comments['idproducts']
                ));
                if (isset($_SESSION['accountid']) && $_SESSION['roleid'] == 2) {
                    $content->newBlock("COMMENTSADMIN");
                    $content->assign("COMMENTID", $comments['idcomments']);
                }
            }
        }
        else{
            //error
        }


        break;

    default:

        $check_projects = $db->query("SELECT count(*) FROM products");
        if($check_projects->fetchColumn() > 0 ){
            $get_projects = $db->query("SELECT * FROM products");
            $teller = 0;
            while($projects = $get_projects->fetch(PDO::FETCH_ASSOC)){
                $teller++;
                if($teller % 3 == 1){
                    // div openen
                    $content->newBlock("BEGIN");
                }
                // block van een element oproepen
                $projectcontent = substr($projects['Content'], 0, 150)." ...";
                $content->newBlock("PROJECT");
                $content->assign(array("TITLE" => $projects['Title'],
                    "CONTENT" => $projectcontent,
                    "PROJECTID" => $projects['idProducts']));
                if($teller % 3 == 0){
                    // div sluiten
                    $content->newBlock("END");
                }
            }
            if($teller % 3 != 0){
                $content->newBlock("END");
            }
        }else{
            // geen projecten gevonden
        }
}