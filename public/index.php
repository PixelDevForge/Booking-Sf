<?php

// Utilise la classe Kernel depuis l'espace de noms App
use App\Kernel;

// Inclut le fichier autoload_runtime.php pour charger les dépendances de Composer
require_once dirname(__DIR__).'/vendor/autoload_runtime.php';

// Retourne une fonction anonyme qui crée et retourne une instance de Kernel
// La fonction prend un tableau $context en paramètre
return function (array $context) {
    // Crée et retourne une nouvelle instance de Kernel avec l'environnement d'application (APP_ENV)
    // et le mode de débogage (APP_DEBUG) définis dans le contexte
    return new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
};
