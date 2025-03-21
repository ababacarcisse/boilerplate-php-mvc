<?php

namespace App\Lib;

class Auth
{
    private static $instance = null;
    private $user = null;
    private $permissions = [];

    private function __construct()
    {
        $this->initialize();
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function initialize()
    {
        // Vérifier si un token JWT existe
        $token = JWT::extractTokenFromHeader();
        if ($token) {
            $decoded = JWT::decode($token);
            if ($decoded && isset($decoded->data)) {
                $this->user = (array)$decoded->data;
                $this->loadUserPermissions();
            }
        }
    }

    private function loadUserPermissions()
    {
        if (!$this->user || !isset($this->user['id'])) {
            return;
        }

        try {
            $pdo = new \PDO(
                "mysql:host=" . getenv('DB_HOST') . ";dbname=" . getenv('DB_NAME'),
                getenv('DB_USER'),
                getenv('DB_PASS')
            );
            
            $stmt = $pdo->prepare("
                SELECT p.permission_code 
                FROM user_permissions up
                JOIN permissions p ON up.permission_id = p.id
                WHERE up.user_id = ?
            ");
            
            $stmt->execute([$this->user['id']]);
            $this->permissions = $stmt->fetchAll(\PDO::FETCH_COLUMN);
            
        } catch (\PDOException $e) {
            error_log("Erreur lors du chargement des permissions: " . $e->getMessage());
        }
    }

    public function isAuthenticated(): bool
    {
        return $this->user !== null;
    }

    public function getUser(): ?array
    {
        return $this->user;
    }

    public function hasRole(string $role): bool
    {
        return $this->user && isset($this->user['role']) && $this->user['role'] === $role;
    }

    public function hasType(string $type): bool
    {
        return $this->user && isset($this->user['type']) && $this->user['type'] === $type;
    }

    public function isPharmacy(): bool
    {
        return $this->hasType('pharmacie');
    }

    public function isMangazin(): bool
    {
        return $this->hasType('mangazin');
    }

    public function canAccessSales(): bool
    {
        return $this->isPharmacy() && $this->hasPermission('sales');
    }

    public function canManageStock(): bool
    {
        return $this->hasAnyPermission(['entries', 'outputs']);
    }

    public function getStockContext(): string
    {
        return $this->user['type'] ?? '';
    }

    public function hasPermission(string $permission): bool
    {
        // Vérifier d'abord si l'utilisateur a la permission de base
        if (!in_array($permission, $this->permissions)) {
            return false;
        }

        // Restrictions spécifiques basées sur le type
        switch ($permission) {
            case 'sales':
                // Seule la pharmacie peut avoir accès aux ventes
                return $this->isPharmacy();
            
            case 'entries':
            case 'outputs':
                // Les entrées/sorties sont accessibles selon le type
                return true;
            
            case 'stats':
                // Les stats sont accessibles selon les permissions et le type
                return true;
            
            default:
                return true;
        }
    }

    public function hasAnyPermission(array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if ($this->hasPermission($permission)) {
                return true;
            }
        }
        return false;
    }

    public function hasAllPermissions(array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if (!$this->hasPermission($permission)) {
                return false;
            }
        }
        return true;
    }

    public function requirePermission(string $permission): void
    {
        if (!$this->hasPermission($permission)) {
            $this->redirectToUnauthorized();
        }
    }

    public function requireRole(string $role): void
    {
        if (!$this->hasRole($role)) {
            $this->redirectToUnauthorized();
        }
    }

    public function requireType(string $type): void
    {
        if (!$this->hasType($type)) {
            $this->redirectToUnauthorized();
        }
    }

    private function redirectToUnauthorized(): void
    {
        require_once dirname(__DIR__) . '/config.php';
        header('HTTP/1.1 403 Forbidden');
        header('Location: ' . BASE_URL . '/unauthorized');
        exit;
    }

    private function redirectToLogin(): void
    {
        require_once dirname(__DIR__) . '/config.php';
        header('Location: ' . BASE_URL . '/login');
        exit;
    }
} 