<?php

namespace Core;

use PDO;
use PDOException;

abstract class Model
{
    protected static $db = null; // Instance de la connexion PDO
    protected $table;
    protected $primaryKey = 'id';
    protected $fillable = [];

    // Fournit une connexion PDO sécurisée à la base de données
    protected static function getDB()
    {
        if (self::$db === null) {
            try {
                $config = require '../config/config.php'; // Charge la configuration
                $dsn = 'mysql:host=' . $config['db']['host'] . ';dbname=' . $config['db']['dbname'] . ';charset=' . $config['db']['charset'];
                
                $options = [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
                    PDO::ATTR_EMULATE_PREPARES => false
                ];
                
                self::$db = new PDO($dsn, $config['db']['user'], $config['db']['password'], $options);
            } catch (PDOException $e) {
                throw new \Exception("Erreur de connexion à la base de données: " . $e->getMessage());
            }
        }

        return self::$db; // Retourne l'instance de la connexion
    }

    // Méthode pour exécuter une requête préparée
    protected function query($sql, $params = [])
    {
        try {
            $stmt = self::getDB()->prepare($sql); // Prépare la requête
            $stmt->execute($params); // Exécute la requête avec les paramètres
            return $stmt; // Retourne l'objet statement
        } catch (PDOException $e) {
            throw new \Exception("Erreur d'exécution de la requête: " . $e->getMessage());
        }
    }

    // Trouver tous les enregistrements
    public function findAll()
    {
        return $this->query("SELECT * FROM {$this->table}")->fetchAll();
    }

    // Trouver par ID
    public function findById($id)
    {
        return $this->query(
            "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = ?", 
            [$id]
        )->fetch();
    }

    // Créer un nouvel enregistrement
    public function create(array $data)
    {
        // Filtrer les données pour ne garder que les champs autorisés
        $data = array_intersect_key($data, array_flip($this->fillable));
        
        $fields = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        
        $sql = "INSERT INTO {$this->table} ($fields) VALUES ($placeholders)";
        
        $this->query($sql, array_values($data));
        return self::getDB()->lastInsertId();
    }

    // Mettre à jour un enregistrement
    public function update($id, array $data)
    {
        // Filtrer les données pour ne garder que les champs autorisés
        $data = array_intersect_key($data, array_flip($this->fillable));
        
        $fields = [];
        foreach (array_keys($data) as $field) {
            $fields[] = "$field = ?";
        }
        
        $fields = implode(', ', $fields);
        
        $sql = "UPDATE {$this->table} SET $fields WHERE {$this->primaryKey} = ?";
        
        $values = array_values($data);
        $values[] = $id;
        
        return $this->query($sql, $values)->rowCount();
    }

    // Supprimer un enregistrement
    public function delete($id)
    {
        $sql = "DELETE FROM {$this->table} WHERE {$this->primaryKey} = ?";
        return $this->query($sql, [$id])->rowCount();
    }

    // Méthodes CRUD peuvent être ajoutées ici
}
