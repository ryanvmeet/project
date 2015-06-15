<?php
$content = new TemplatePower("template/files/admin_comments.tpl");
$content->prepare();

if(isset($_GET['action']))
{
    $action = $_GET['action'];
}else{
    $action = NULL;
}

        switch ($action) {

            case "toevoegen":
                if (!empty($_POST['text']))
                {
                    $productid=$_POST['productid'];
                    $blogid=$_POST['blogid'];
                    if(empty($_POST['productid'])){
                        $productid= NULL;
                    }
                    elseif(empty($_POST['blogid'])){
                        $blogid=NULL;
                    }

                        $accountid = $_SESSION['accountid'];

                        $insert_comment = $db->prepare("INSERT INTO comments SET
                      Text = :text,
                      Blog_idBlog = :blogid,
                      Accounts_idAccounts = :accountid,
                      Products_idProducts = :productid
                      ");

                        $insert_comment->bindParam(":text", $_POST['text']);
                        $insert_comment->bindParam(":blogid", $blogid);
                        $insert_comment->bindParam(":accountid", $accountid);
                        $insert_comment->bindParam(":productid", $productid);
                        $insert_comment->execute();
                    if(empty($_POST['productid'])){
                        $content->newBlock("HOI");
                        $content->assign("PAGE", "?pageid=6&action=details&idblog=".$_POST['blogid']);
                    }
                    elseif(empty($_POST['blogid'])){
                        $content->newBlock("HOI");
                        $content->assign("PAGE", "?pageid=9&action=details&projectid=".$_POST['productid']);
                    }

                }

                break;

            case "wijzigen":
                if (isset($_SESSION['accountid'])) {
                    if ($_SESSION['roleid'] == 2) {


                        if (!empty($_POST['text']) && !empty($_POST['commentid'])) {
                            $update_comment = $db->prepare("UPDATE Comments Set
                                                        Text = :text
                                                        WHERE idComments = :comment");
                            $update_comment->bindParam(":comment", $_POST['commentid']);
                            $update_comment->bindParam(":text", $_POST['text']);
                            $update_comment->execute();
                            if (!empty($_POST['blogid'])) {
                                $content->newBlock("HOI");
                                $content->assign("PAGE", "?pageid=6&action=details&idblog=" . $_POST['blogid']);
                            }
                            elseif(!empty($_POST['productid'])) {
                                $content->newBlock("HOI");
                                $content->assign("PAGE", "?pageid=9&action=details&idproducts=" . $_POST['productid']);
                            }

                        } elseif ($_GET['idcomment']) {
                            $get_comment = $db->prepare("SELECT c.*, a.* FROM Comments c, Accounts a
                                          WHERE a.idAccounts=c.Accounts_idAccounts
                                          AND idComments = :commentid
                                          ");
                            $get_comment->bindParam(":commentid", $_GET['idcomment']);
                            $get_comment->execute();

                            $comment = $get_comment->fetch(PDO::FETCH_ASSOC);

                            $content->newBlock("COMMENTFORM");
                            $content->assign("ACTION", "index.php?pageid=7&action=wijzigen");
                            $content->assign("BUTTON", "Wijzigen comment");
                            $content->assign(array(
                                "TEXT" => $comment['Text'],
                                "BLOGID" => $comment['Blog_idBlog'],
                                "PRODUCTID" => $comment['Products_idProducts'],
                                "COMMENTID" => $comment['idComments']
                            ));
                        } else {
                            $content->newBlock("MELDING");
                            $content->assign("MELDING", "er was eens een persoon, dit persoon was op een pagina beland die niet bestaat");
                        }
                    }
                    else {
                            $content->newBlock("MELDING");
                            $content->assign("MELDING", "u heeft geen rechten om deze pagina te zien");
                        }
                    }
                    else{
                        $content->newBlock("MELDING");
                        $content->assign("MELDING", "eerst inloggen aub");
                    }

                break;
            case "verwijderen":

                if (isset($_SESSION['accountid'])) {
                    if ($_SESSION['roleid'] == 2) {


                        if (!empty($_POST['commentid'])) {
                            $check_comment = $db->prepare("SELECT count(*) FROM Comments c, Accounts a
                                          WHERE a.idAccounts=c.Accounts_idAccounts
                                          AND idComments = :commentid
                                          ");
                            $check_comment->bindParam(":commentid", $_POST['commentid']);
                            $check_comment->execute();

                            if ($check_comment->fetchColumn() == 1) {
                                $delete_comment = $db->prepare("DELETE FROM Comments
                                                    WHERE idComments = :comment");
                                $delete_comment->bindParam(":comment", $_POST['commentid']);
                                $delete_comment->execute();

                                if (!empty($_POST['blogid'])) {
                                    $content->newBlock("HOI");
                                    $content->assign("PAGE", "?pageid=6&action=details&idblog=" . $_POST['blogid']);
                                }
                                elseif(!empty($_POST['productid'])) {
                                    $content->newBlock("HOI");
                                    $content->assign("PAGE", "?pageid=9&action=details&idproducts=" . $_POST['productid']);
                                }

                            } else {
                                $content->newBlock("MELDING");
                                $content->assign("MELDING", "deze comment bestaat niet.. Blijf van de url af!!");
                            }

                            if (!empty($_POST['blogid'])) {
                                $content->newBlock("HOI");
                                $content->assign("PAGE", "?pageid=6&action=details&idblog=" . $_POST['blogid']);
                            }

                        } elseif (isset($_GET['idcomment'])) {
                            $check_comment = $db->prepare("SELECT count(*) FROM Comments c, Accounts a
                                          WHERE a.idAccounts=c.Accounts_idAccounts
                                          AND idComments = :commentid
                                          ");
                            $check_comment->bindParam(":commentid", $_GET['idcomment']);
                            $check_comment->execute();

                            if ($check_comment->fetchColumn() == 1) {
                                $get_comment = $db->prepare("SELECT c.*, a.* FROM Comments c, Accounts a
                                          WHERE a.idAccounts=c.Accounts_idAccounts
                                          AND idComments = :commentid
                                          ");
                                $get_comment->bindParam(":commentid", $_GET['idcomment']);
                                $get_comment->execute();

                                $comment = $get_comment->fetch(PDO::FETCH_ASSOC);

                                $content->newBlock("COMMENTFORM");
                                $content->assign("READONLY", "readonly");
                                $content->assign("ACTION", "index.php?pageid=7&action=verwijderen");
                                $content->assign("BUTTON", "verwijder comment");
                                $content->assign(array(
                                    "TEXT" => $comment['Text'],
                                    "BLOGID" => $comment['Blog_idBlog'],
                                    "PRODUCTID" => $comment['Products_idProducts'],
                                    "COMMENTID" => $comment['idComments']
                                ));
                            } else {
                                $content->newBlock("MELDING");
                                $content->assign("MELDING", "deze comment bestaat niet.. Blijf van de url af!!");
                            }
                        } else {
                            $content->newBlock("MELDING");
                            $content->assign("MELDING", "er was eens een persoon, dit persoon was op een pagina beland die niet bestaat");
                        }
                    }
            else {
                $content->newBlock("MELDING");
                $content->assign("MELDING", "u heeft geen rechten om deze pagina te zien");
            }
        }
else{
    $content->newBlock("MELDING");
    $content->assign("MELDING", "eerst inloggen aub");
}

                break;


            default:


                    $content->newBlock("MELDING");
                    $content->assign("MELDING", "deze pagina is niet toegankelijk");
    }

