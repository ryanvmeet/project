<?php

$content = new TemplatePower("template/files/admin_users.tpl");
$content->prepare();

if(isset($_GET['action']))
{
$action = $_GET['action'];
}else{
    $action = NULL;
}

switch($action)
{
    case "toevoegen":
        if(!empty($_POST['vnaam'])
            && !empty($_POST['anaam'])
            && !empty($_POST['gnaam'])
            && !empty($_POST['email'])
            && !empty($_POST['password1'])
            && !empty($_POST['password2'])){
            // insert
            if($_POST['password1'] == $_POST['password2'])
            {
                $user = $_POST['gnaam'];
                $get_user = $db->prepare("SELECT count(*) FROM accounts
                                      where Username LIKE :user");
                $get_user->bindParam(":user", $_POST['gnaam'] );
                $get_user->execute();

                if ($get_user->fetchColumn()>0) {
                    $content->newBlock("USERFORM");
                    $content->assign("GEBRUIKERSNAAM1", "deze naam bestaat al");
                    $content->assign("BUTTON", "toevoegen user");
                    $content->assign(array(
                        "VOORNAAM" => $_POST['vnaam'],
                        "ACHTERNAAM" => $_POST['anaam'],
                        "EMAIL" => $_POST['email'],
                        "USERNAME" => $_POST['gnaam']

                    ));
                }
                else {
                    // insert
                    $insert_user = $db->prepare("INSERT INTO users SET
                  Surename = :anaam,
                  Name = :vnaam,
                  Email = :email");
                    $insert_user->bindParam(":anaam", $_POST['anaam']);
                    $insert_user->bindParam(":vnaam", $_POST['vnaam']);
                    $insert_user->bindParam(":email", $_POST['email']);
                    $insert_user->execute();

                    $userid = $db->lastInsertId();


                    $password = password_hash($_POST['password1'], PASSWORD_BCRYPT);
                    $insert_account = $db->prepare("INSERT INTO accounts SET
                  Username = :username,
                  Password = :password,
                  Users_idUsers = :userid,
                  Role_idRole = :roleid");
                    $insert_account->bindParam(":username", $_POST['gnaam']);
                    $insert_account->bindParam(":password", $password);
                    $insert_account->bindParam(":userid", $userid);
                    $insert_account->bindValue(":roleid", 1);
                    $insert_account->execute();

                    $content->newBlock("MELDING");
                    $content->assign("MELDING", "Klik hier om naar het inlogscherm te gaan.");

                }

            }
            else{
                $errors->newBlock("ERRORS");
                $errors->assign("ERROR", "Wachtwoord komt niet overeen SUKKEL!");
                $content->newBlock("USERFORM");
                $content->assign("ACTION", "index.php?pageid=2&action=toevoegen");
                $content->assign("BUTTON", "Toevoegen Gebruiker");
                $content->assign(array(
                    "VOORNAAM" => $_POST['vnaam'],
                    "ACHTERNAAM" => $_POST['anaam'],
                    "EMAIL" => $_POST['email'],
                    "USERNAME" => $_POST['gnaam']
                ));
            }

        }else{
            // formulier
            $content->newBlock("USERFORM");
            $content->assign("ACTION", "index.php?pageid=2&action=toevoegen");
            $content->assign("BUTTON", "Toevoegen Gebruiker");
        }
        break;
    case "wijzigen":
                        if(isset($_SESSION['accountid'])) {
                            if ($_SESSION['roleid'] == 2) {

                                if (isset($_POST['accountid'])
                                ) {
                                    if (isset($_POST['role'])) {
                                        $role = 2;
                                    } else {
                                        $role = 1;
                                    }
                                    $update_account = $db->prepare("UPDATE accounts
                                          SET Username = :username,
                                          Role_idRole = :role
                                          WHERE idAccounts = :accountid");
                                    $update_account->bindParam(":username", $_POST['gnaam']);
                                    $update_account->bindParam(":role", $role);
                                    $update_account->bindParam(":accountid", $_POST['accountid']);
                                    $update_account->execute();

                                    $update_user = $db->prepare("UPDATE users
                                            SET Surename = :achternaam,
                                            Name = :voornaam,
                                            Email = :email
                                            WHERE idUsers = :userid");
                                    $update_user->bindParam(":achternaam", $_POST['anaam']);
                                    $update_user->bindParam(":voornaam", $_POST['vnaam']);
                                    $update_user->bindParam(":email", $_POST['email']);
                                    $update_user->bindParam(":userid", $_POST['userid']);
                                    $update_user->execute();


                                } else {

                                    $get_user = $db->prepare("SELECT users.*, accounts.* FROM users, accounts
                                      WHERE users.idUsers=accounts.Users_idUsers
                                      AND accounts.idAccounts = :accountid");
                                    $get_user->bindParam(":accountid", $_GET['accountid']);
                                    $get_user->execute();

                                    $user = $get_user->fetch(PDO::FETCH_ASSOC);

                                    $content->newBlock("USERFORM");
                                    $content->assign("ACTION", "index.php?pageid=2&action=wijzigen");
                                    $content->assign("BUTTON", "Wijzigen Gebruiker");

                                    $content->assign(array(
                                        "VOORNAAM" => $user['Name'],
                                        "ACHTERNAAM" => $user['Surename'],
                                        "EMAIL" => $user['Email'],
                                        "USERNAME" => $user['Username'],
                                        "ACCOUNTID" => $user['idAccounts'],
                                        "USERID" => $user['idUsers']
                                    ));
                                    $content->newBlock("ROLE");

                                }
                            }
                            else {
                                //melding geen rechten
                                $errors->newBlock("ERRORS");
                                $errors->assign("ERROR", "je hebt de rechten niet voor deze pagina");
                            }
                        }
                        else {
                            // melding log in!!
                            $errors->newBlock("ERRORS");
                            $errors->assign("ERROR", "je moet eerst inloggen");
                        }

        break;
    case "verwijderen":
        if(isset($_SESSION['accountid'])) {
            if ($_SESSION['roleid'] == 2) {

                if (isset($_POST['userid'])) {
                    $delete_blog = $db->prepare("DELETE FROM Blog
                                    WHERE Accounts_idAccounts = :accountid");
                    $delete_blog->bindParam(":accountid", $_POST['accountid']);

                    $delete_blog->execute();

                    $delete_account = $db->prepare("DELETE FROM Accounts
                                    WHERE idAccounts = :accountid");
                    $delete_account->bindParam(":accountid", $_POST['accountid']);

                    $delete_account->execute();

                    $delete_user = $db->prepare("DELETE FROM users
                                    WHERE idUsers = :userid");
                    $delete_user->bindParam(":userid", $_POST['userid']);
                    $delete_user->execute();

                } else {

                    $get_user = $db->prepare("SELECT users.*, Accounts.* FROM users, Accounts
                                      WHERE users.idUsers = accounts.Users_idUsers
                                      AND accounts.idAccounts = :accountid");
                    $get_user->bindParam(":accountid", $_GET['accountid']);
                    $get_user->execute();


                    $users = $get_user->fetch(PDO::FETCH_ASSOC);

                    $content->newBlock("USERFORM");
                    $content->assign("ACTION", "index.php?pageid=2&action=verwijderen");
                    $content->assign("BUTTON", "verwijder user");
                    $content->assign("READONLY1", "readonly");
                    $content->assign(array(
                        "VOORNAAM" => $users['Name'],
                        "ACHTERNAAM" => $users['Surename'],
                        "EMAIL" => $users['Email'],
                        "USERNAME" => $users['Username'],
                        "ACCOUNTID" => $users['idAccounts'],
                        "USERID" => $users['idUsers']
                    ));
                }
            }
            else {
                //melding geen rechten
                $errors->newBlock("ERRORS");
                $errors->assign("ERROR", "je hebt de rechten niet om deze pagina te zien");
            }
        }
        else {
            // melding log in!!
            $errors->newBlock("ERRORS");
            $errors->assign("ERROR", "je moet eerst inloggen");
        }
        break;

    default:
        if(isset($_SESSION['accountid'])) {
            if ($_SESSION['roleid'] == 2) {
                $content->newBlock("USERLIST");

                $get_users = $db->query("SELECT users.idUsers, users.Surename, users.Name, users.Email, accounts.Username, accounts.idAccounts
                                  FROM users, accounts
        WHERE users.idUsers = accounts.Users_idUsers");


                while ($users = $get_users->fetch(PDO::FETCH_ASSOC)) {
                    $content->newBlock("USERROW");
                    $content->assign(array(
                        "VOORNAAM" => $users['Name'],
                        "ACHTERNAAM" => $users['Surename'],
                        "EMAIL" => $users['Email'],
                        "USERNAME" => $users['Username'],
                        "ACCOUNTID" => $users['idAccounts'],
                        "USERID" => $users['idUsers']
                    ));
                }
            }else{
//melding geen rechten
            $errors->newBlock("ERRORS");
            $errors->assign("ERROR", "je hebt geen rechten om deze pagina te zien");
            }
    }
// melding log in!!
        else{
        $errors->newBlock("ERRORS");
        $errors->assign("ERROR", "eerst inloggen!!");
        }
}