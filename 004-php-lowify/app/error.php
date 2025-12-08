<?php

require_once "inc/page.inc.php";

// récupération du message envoyé dans l’URL
$message = "Une erreur est survenue.";
if (isset($_GET["message"])) {
    $message = $_GET["message"];
}

$page = new HTMLPage("Erreur");

$html = "
<div class='container p-4'>
    <h1 style='color:red;'>Erreur</h1>
    <p>$message</p>
    <a href='index.php'>Retour à l’accueil</a>
</div>
";

$page->addContent($html);
echo $page->render();
