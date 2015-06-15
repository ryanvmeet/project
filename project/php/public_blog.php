<?php
$content = new TemplatePower("template/files/blog.tpl");
$content->prepare();

if(isset($_GET['action']))
{
    $action = $_GET['action'];
}else{
    $action = NULL;
}
switch ($action) {

    case "details":
        $get_blogs = $db->prepare("SELECT blog.content, blog.title, accounts.Username, blog.idblog
                                      FROM blog, accounts
                                      WHERE blog.Accounts_idAccounts = accounts.idAccounts
                                      AND blog.idblog = :idblog");
        $get_blogs->bindParam(":idblog", $_GET['idblog']);
        $get_blogs->execute();
        $blogs = $get_blogs->fetch(PDO::FETCH_ASSOC);
        $content->newBlock("BLOGROW");
        $content->assign(array(
            "CONTENT" => $blogs['content'],
            "TITLE" => $blogs['title'],
            "USERNAME" => $blogs['Username'],
            "BLOGID" => $blogs['idblog']));
        if(isset($_SESSION['accountid'])){
            $content->newBlock("DETAILSFORM");
            $content->assign("BLOGID", $blogs['idblog']);
        }
        else{
            $content->newBlock("NEE");
        }

        $get_comments = $db->prepare("SELECT c.text, a.Username, b.idblog, c.idcomments
                                      FROM comments as c,blog as b, accounts as a
                                      WHERE b.idblog = c.blog_idblog
                                      AND c.blog_idblog = :idblog
                                      AND c.accounts_idaccounts = a.idaccounts
                                      ORDER BY c.idcomments DESC
                                      ");
        $get_comments->bindParam(":idblog", $_GET['idblog']);
        $get_comments->execute();
        while ($comments = $get_comments->fetch(PDO::FETCH_ASSOC)) {
            $content->newBlock("COMMENTS");
            $content->assign(array(
                "TEXT" => $comments['text'],
                "USERNAME" => $comments['Username'],
                "COMMENTID" => $comments['idcomments'],
                "BLOGID" => $comments['idblog']
            ));
            if(isset($_SESSION['accountid']) && $_SESSION['roleid'] == 2){
                $content->newBlock("COMMENTSADMIN");
                $content->assign("COMMENTID", $comments['idcomments']);
            }
        }


        break;
    case "wijzigen":

        break;
    case "verwijderen":

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


        } else {

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