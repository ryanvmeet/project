<?php
$content = new TemplatePower("template/files/forgot_pass.tpl");
$content->prepare();
if(isset($_GET['action']))
{
    $action = $_GET['action'];
}else{
    $action = NULL;
}

switch ($action) {
    case "check":
        if(!empty($_POST['email'])&&
            !empty($_POST['gnaam'])){
        //check
            $get_account = $db->prepare("SELECT count(*) FROM accounts a, users u
                                         WHERE a.Username = :username
                                         AND u.email = :email
                                         AND a.Users_idUsers = u.idUsers
            ");
            $get_account->bindParam(":username", $_POST['gnaam'] );
            $get_account->bindParam(":email", $_POST['email'] );
            $get_account->execute();
            if($get_account->fetchColumn()== 1){
                //account bestaat, reset password random genereren en opslaan in database. zelfde code opsturen naar email.
                $code=rand(100,99999);
                $email = $_POST['email'];
                //update database
                $update_account = $db->prepare("UPDATE accounts SET Reset = :code
                                                WHERE Username = :username
                                                ");
                $update_account->bindParam(":code", $code);
                $update_account->bindParam(":username", $_POST['gnaam'] );
                $update_account->execute();

                //stuur dezelfde code naar de email van de persoon via de url
                //$message="Your activation link is: index.php?pageid=10&action=reset&email=$email&code=$code";
                //mail($email,"reset password", $message);

                //geef melding dat de mail is verzonden
                $content->newBlock("MELDING");
                $content->assign("MELDING", "de mail is verzonden, je activatielink is: index.php?pageid=10&action=reset&code=$code");
            }
            else{
                $content->newBlock("MELDING");
                $content->assign("MELDING", "dit account bestaat niet!!");

                $content->newBlock("FORGOTFORM");
            }
        }
        elseif((empty($_POST['email'])&& !empty($_POST['gnaam']))||(!empty($_POST['email'])&& empty($_POST['gnaam'])))
        {
           //error vul alles in

            $content->newBlock("MELDING");
            $content->assign("MELDING", "vul alles in!!!");

            $content->newBlock("FORGOTFORM");
        }
        else{
            //show formulier forgot pass
            $content->newBlock("FORGOTFORM");

        }
        break;

    case "reset":
        if(isset($_GET['code'])){
            //check of de code klopt met de code in de database.
            $get_account = $db->prepare("SELECT count(*) FROM accounts a, users u
                                         WHERE a.Reset = :code
                                         AND a.Users_idUsers = u.idUsers
            ");
            $get_account->bindParam(":code", $_GET['code'] );
            $get_account->execute();
            if($get_account->fetchColumn()== 1){
                //deze code bestaat dus geef het reset formulier
                $content->newBlock("RESETFORM");
                $content->assign("CODE",$_GET['code'] );
            }
            else{
                //error, deze code bestaat niet.
                $content->newBlock("MELDING");
                $content->assign("MELDING", "deze code bestaat niet");
            }
        }
        elseif(!empty($_POST['codeid'])){
            //controleer of alles is ingevuld
            if((!empty($_POST['password']) && !empty($_POST['password1']))&&(!empty($_POST['password']) == !empty($_POST['password1']))){

                $password = password_hash($_POST['password'],PASSWORD_BCRYPT);

                $update_account = $db->prepare("UPDATE Accounts SET
                                                Password = :password,
                                                Reset = NULL,
                                                Salt = :salt
                                                WHERE Reset = :code");
                $update_account->bindParam(":code", $_POST['codeid']);
                $update_account->bindParam(":password", $password);
                $update_account->bindParam(":salt", $salt);
                $update_account->execute();

                $content->newBlock("MELDING");
                $content->assign("MELDING", "wachtwoord is gewijzigd");
                $content->newBlock("REFRESH");
                $content->assign("PAGE", "4");
            }
            else{
                // error vul alles in
                $content->newBlock("MELDING");
                $content->assign("MELDING", "vul alles in");
                $content->newBlock("RESETFORM");
            }

        }
        else{
            //error je hoort hier niet te komen.
            $content->newBlock("MELDING");
            $content->assign("MELDING", "wat doe je hier");
        }
        break;
    default:
        //form reset pass
        if(isset($_SESSION['accountid'])){
            $content->newBlock("MELDING");
            $content->assign("MELDING", "je bent al ingelogd");
        }else {
            $content->newBlock("FORGOTFORM");
        }
}
