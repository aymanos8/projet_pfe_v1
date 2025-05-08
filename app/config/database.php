<?php

function getConnection() {
    try {
        $cnx = new PDO("mysql:host=localhost;dbname=projet_pfe", "root", "");
        $cnx->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $cnx;
    } catch(PDOException $e) {
        die("Erreur de connexion : " . $e->getMessage());
    }
} 