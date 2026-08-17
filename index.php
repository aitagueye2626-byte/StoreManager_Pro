<?php


use App\Controllers\AuthController;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$uri = parse_url(
    $_SERVER['REQUEST_URI'],
    PHP_URL_PATH
);

switch (true) {


    case $uri === '/connexion' && $_SERVER['REQUEST_METHOD'] === 'POST':

    require __DIR__ . '/src/Core/database.php';
    require __DIR__ . '/src/Service/AuthManager.php';
    require __DIR__ . '/src/Controller/AuthController.php';

    (new \App\Controllers\AuthController())->traiterConnexion();

    break;

  case $uri === '/connexion':

    require __DIR__ . '/src/Core/database.php';
    require __DIR__ . '/src/Service/AuthManager.php';
    require __DIR__ . '/src/Controller/AuthController.php';

    (new \App\Controllers\AuthController())->afficherConnexion();

    break;

    case $uri === '/deconnexion':

    require __DIR__ . '/src/Core/database.php';
    require __DIR__ . '/src/Service/AuthManager.php';
    require __DIR__ . '/src/Controller/AuthController.php';

    (new \App\Controllers\AuthController())->deconnecter();

    break;


    case $uri === '/pos/vente'
        && $_SERVER['REQUEST_METHOD'] === 'POST':

        require __DIR__ . '/src/Core/database.php';
        require __DIR__ . '/src/Model/Entity/Client.php';
        require __DIR__ . '/src/Model/Entity/Produit.php';
        require __DIR__ . '/src/Repository/ClientRepository.php';
        require __DIR__ . '/src/Repository/ProduitRepository.php';
        require __DIR__ . '/src/Service/VenteService.php';
        require __DIR__ . '/src/Controller/POSController.php';

        (new POSController())->traiterVente();

        break;



    case $uri === '/pos':

        require __DIR__ . '/src/Core/database.php';
        require __DIR__ . '/src/Model/Entity/Client.php';
        require __DIR__ . '/src/Model/Entity/Produit.php';
        require __DIR__ . '/src/Repository/ClientRepository.php';
        require __DIR__ . '/src/Repository/ProduitRepository.php';
        require __DIR__ . '/src/Service/VenteService.php';
        require __DIR__ . '/src/Controller/POSController.php';

        (new POSController())->afficherFormulaire();

        break;



    case $uri === '/dettes':

        require __DIR__ . '/views/dettes/index.php';

        break;


    case $uri === '/approvisionnements':

        require __DIR__ . '/views/supplies/index.php';

        break;

    case $uri === '/'
        || $uri === '':

        header('Location: /connexion');

        exit;



    default:

        http_response_code(404);

        echo "Page introuvable : "
            . htmlspecialchars($uri);

        break;
}