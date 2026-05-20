<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * AuthFilter
 *
 * Filtre appliqué à toutes les routes protégées.
 * Vérifie que la session est ouverte ET que le rôle correspond.
 *
 * Usage dans app/Config/Filters.php :
 *
 *   public array $aliases = [
 *       'auth'  => \App\Filters\AuthFilter::class,
 *       'role'  => \App\Filters\AuthFilter::class,   // même classe, arguments différents
 *   ];
 *
 * Usage dans Routes.php (argument optionnel = rôle requis) :
 *
 *   $routes->group('/admin', ['filter' => 'auth:admin'], function ($routes) { … });
 *   $routes->group('/rh',    ['filter' => 'auth:rh'],    function ($routes) { … });
 *   $routes->group('/employe', ['filter' => 'auth'],     function ($routes) { … });
 */
class AuthFilter implements FilterInterface
{
    /**
     * @param array|null $arguments  Rôles autorisés (ex: ['admin'] ou ['rh','admin'])
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();

        // ── 1. Non connecté ──
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('/login')->with('error', 'Veuillez vous connecter.');
        }

        // ── 2. Vérification du rôle (si argument précisé) ──
        if (!empty($arguments)) {
            $userRole      = $session->get('user_role');
            $allowedRoles  = $arguments; // tableau de rôles acceptés

            if (!in_array($userRole, $allowedRoles, true)) {
                // Connecté mais rôle insuffisant → tableau de bord approprié
                return redirect()
                    ->to($this->dashboardForRole($userRole))
                    ->with('error', 'Accès non autorisé pour votre rôle.');
            }
        }

        // Accès accordé
        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Rien à faire après la requête
    }

    private function dashboardForRole(string $role): string
    {
        return match ($role) {
            'admin' => '/admin/dashboard',
            'rh'    => '/rh/dashboard',
            default => '/employe/dashboard',
        };
    }
}
