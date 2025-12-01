<?php


// app/artists.php

// Inclusion obligatoire de la classe page
require_once __DIR__ . '/inc/page.inc.php';

// création de la page
$page = new HTMLPage("Lowify - Artistes");

// initialisation CSS/JS (chemins relatifs depuis /app)
$page->addCSS('css/style.css');
$page->addJS('js/script.js');

// contenu HTML (pour l'instant un simple h1)
$htmlContent = '<header><h1>Lowify - Artistes</h1></header>
<main>
  <p>Bienvenue sur la page des artistes. La liste sera ajoutée bientôt.</p>
</main>';

$page->setContent($htmlContent);

// affichage de la page
$page->render();
