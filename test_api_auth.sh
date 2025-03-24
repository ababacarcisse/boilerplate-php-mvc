#!/bin/bash

# Configuration
BASE_URL="http://localhost/gestion-pharmacie"
API_URL="$BASE_URL/api/auth"

# Couleurs pour le formatage
GREEN='\033[0;32m'
RED='\033[0;31m'
NC='\033[0m'

echo "🔍 Test des API d'authentification"
echo "================================="

# Test 1: Inscription
echo -e "\n${GREEN}Test 1: Inscription${NC}"
curl -X POST "$API_URL/register" \
  -H "Content-Type: application/json" \
  -d '{
    "matricule": "MAT2025001",
    "nom": "Diop",
    "prenom": "Alioune",
    "date_naissance": "2002-05-14",
    "email": "ababacarcisse18@gmail.com",
    "password": "motdepasse123"
  }'

# Test 2: Connexion
echo -e "\n\n${GREEN}Test 2: Connexion${NC}"
curl -X POST "$API_URL/login" \
  -H "Content-Type: application/json" \
  -d '{
    "matricule": "MAT2025001",
    "password": "motdepasse123"
  }'

# Test 3: Demande de réinitialisation de mot de passe
echo -e "\n\n${GREEN}Test 3: Demande de réinitialisation de mot de passe${NC}"
curl -X POST "$API_URL/forgot-password" \
  -H "Content-Type: application/json" \
  -d '{
    "email": "ababacarcisse18@gmail.com",
    "matricule": "MAT2025001"
  }'

# Test 4: Vérification du token de réinitialisation
echo -e "\n\n${GREEN}Test 4: Vérification du token de réinitialisation${NC}"
curl -X POST "$API_URL/verify-reset-token" \
  -H "Content-Type: application/json" \
  -d '{
    "token": "test_token"
  }'

# Test 5: Réinitialisation du mot de passe
echo -e "\n\n${GREEN}Test 5: Réinitialisation du mot de passe${NC}"
curl -X POST "$API_URL/reset-password" \
  -H "Content-Type: application/json" \
  -d '{
    "token": "test_token",
    "password": "nouveau_mot_de_passe123"
  }'

# Test 6: Déconnexion
echo -e "\n\n${GREEN}Test 6: Déconnexion${NC}"
curl -X POST "$API_URL/logout" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer test_token"

echo -e "\n\n${GREEN}Tests terminés${NC}" 