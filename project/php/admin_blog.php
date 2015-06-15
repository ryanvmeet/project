<?php
$content = new TemplatePower("template/files/admin_blog.tpl");
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
                    $blog = $_POST['gnaam'];
                    $get_blog = $db->query("SELECT count(*) FROM accounts
                                          where Username='$blog'");
                    $get_blog->execute();


                    if ($get_blog->fetchcolumn() == 1) {

                        $accountid = $_SESSION['accountid'];

                        $insert_blog = $db->prepare("INSERT INTO blog SET
                      Title = :title,
                      Content = :content,
                      Accounts_idAccounts = :accountid
                      ");

                        $insert_blog->bindParam(":title", $_POST['title']);
                        $insert_blog->bindParam(":content", $_POST['content']);
                        $insert_blog->bindParam(":accountid", $accountid);
                        $insert_blog->execute();
                    }
                } else {
                    $content->newBlock("BLOGFORM");
                    $content->assign("BUTTON", "toevoegen blog");
                    $content->assign("GEBRUIKERSNAAM", $_SESSION['username']);
                    $content->assign("READONLY", "readonly");
                }


                break;
            case "wijzigen":

                if (isset($_POST['blogid'])
                    && !empty($_POST['title'])
                    && !empty($_POST['content'])
                ) {
                    $update_blog = $db->prepare("UPDATE blog
                                                SET Title = :title,
                                                  Content = :content
                                              WHERE idBlog = :blogid");
                    $update_blog->bindParam(":title", $_POST['title']);
                    $update_blog->bindParam(":content", $_POST['content']);
                    $update_blog->bindParam(":blogid", $_POST['blogid']);
                    $update_blog->execute();

                } else {

                    $get_blogg = $db->prepare("SELECT blog.*, Accounts.* FROM blog, Accounts
                                          WHERE Accounts.idAccounts=blog.Accounts_idAccounts
                                          AND idBlog = :blogid");
                    $get_blogg->bindParam(":blogid", $_GET['idblog']);
                    $get_blogg->execute();

                    $blogg = $get_blogg->fetch(PDO::FETCH_ASSOC);

                    $content->newBlock("BLOGFORM");
                    $content->assign("ACTION", "index.php?pageid=3&action=wijzigen");
                    $content->assign("BUTTON", "Wijzigen Blog");
                    $content->assign("READONLY", "readonly");
                    $content->assign(array(
                        "TITLE" => $blogg['Title'],
                        "CONTENT" => $blogg['Content'],
                        "GEBRUIKERSNAAM" => $blogg['Username'],
                        "BLOGID" => $blogg['idBlog']
                    ));

                }
                break;
            case "verwijderen":

                if (isset($_POST['blogid'])) {
                    $delete_blog = $db->prepare("DELETE FROM blog
                                        WHERE idBlog = :blogid");
                    $delete_blog->bindParam(":blogid", $_POST['blogid']);
                    $delete_blog->execute();
                } else {

                    $get_blogg = $db->prepare("SELECT blog.*, Accounts.* FROM blog, Accounts
                                          WHERE Accounts.idAccounts=blog.Accounts_idAccounts
                                          AND idBlog = :blogid");
                    $get_blogg->bindParam(":blogid", $_GET['idblog']);
                    $get_blogg->execute();

                    $blogg = $get_blogg->fetch(PDO::FETCH_ASSOC);

                    $content->newBlock("BLOGFORM");
                    $content->assign("ACTION", "index.php?pageid=3&action=verwijderen");
                    $content->assign("BUTTON", "verwijder Blog");
                    $content->assign("READONLY1", "readonly");
                    $content->assign(array(
                        "TITLE" => $blogg['Title'],
                        "CONTENT" => $blogg['Content'],
                        "GEBRUIKERSNAAM" => $blogg['Username'],
                        "BLOGID" => $blogg['idBlog']
                    ));
                }
                break;


            default:
                $content->newBlock("BLOGLIST");
                if (!empty($_POST['search'])) {
                    $get_blogs = $db->prepare("SELECT blog.content, blog.title, accounts.Username, blog.idblog
                                      FROM blog, accounts
                                      WHERE blog.Accounts_idAccounts = accounts.idAccounts
                                      AND (blog.title LIKE :search
                                      OR accounts.Username LIKE :search1)
                                      ORDER BY blog.idblog DESC");
                    $search = "%" . $_POST['search'] . "%";
                    $get_blogs->bindParam(":search", $search);
                    $get_blogs->bindParam(":search1", $search);
                    $get_blogs->execute();
                    $content->assign("SEARCH", $_POST['search']);


                }
                else {

                    $get_blogs = $db->query("SELECT blog.content, blog.title, accounts.Username, blog.idblog
                                      FROM blog, accounts
                                      WHERE blog.Accounts_idAccounts = accounts.idAccounts
                                      ORDER BY blog.idblog DESC");

                }
                while ($blogs = $get_blogs->fetch(PDO::FETCH_ASSOC)) {
                    $inhoud = $blogs['content'];
                    if(strlen($inhoud)>25){
                        $inhoud = substr($blogs['content'],0,25)."...";
                    }
                    $content->newBlock("BLOGROWA");
                    $content->assign(array(
                        "CONTENT" => $inhoud,
                        "TITLE" => $blogs['title'],
                        "USERNAME" => $blogs['Username'],
                        "BLOGID" => $blogs['idblog']
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