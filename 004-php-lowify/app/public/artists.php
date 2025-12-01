<?php

// Inclusion obligatoire de la classe page
require_once __DIR__ . '/../inc/page.inc.php';

// création de la page
$page = new HTMLPage("Lowify - Artistes");

// initialisation CSS/JS
// Note : Comme artists.php est dans /public, on est déjà à la bonne profondeur
$page->addCSS('css/Artists.css');
$page->addJS('js/Artists.js');

// contenu HTML
$htmlContent = '
<header>
    <h1>Lowify - Artistes</h1>
</header>

<main>
    <p>Bienvenue sur la page des artistes. La liste sera ajoutée bientôt.</p>
</main>
';

$page->setContent($htmlContent);

// affichage de la page
echo $page->render();