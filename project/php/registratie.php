<?php

$content = new TemplatePower("template/files/registratie.tpl");
$content->prepare();

    if(isset($_SESSION['accountid'])) {
        //je bent al ingelogd
        $content->newBlock("MELDING");
        $content->assign("MELDING", "je bent al ingelogd");
    }
    else {
        $content->newBlock("USERFORM");
        $content->assign("ACTION", "index.php?pageid=2&action=toevoegen");
        $content->assign("BUTTON", "Toevoegen Gebruiker");
    }
















