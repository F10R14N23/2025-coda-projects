<?php

require_once __DIR__ . '/../inc/page.inc.php';

$page = new HTMLPage("Lowify - Artistes");

$page->addCSS('../CSS/Artists.css');

$htmlContent = '
<header>
    <h1>Lowify - Artistes</h1>
</header>

<main>
    <p>Bienvenue sur la page des artistes. La liste sera ajoutée bientôt.</p>
</main>
';

$page->setContent($htmlContent);

echo $page->render();
