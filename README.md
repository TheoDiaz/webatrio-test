# Webatrio Test Technique

Application web pour gérer des personnes et leurs emplois. Ce projet est composé d’un **backend** en Symfony qui fournit une API REST et d’un **frontend** en Angular pour l’interface utilisateur.

## Fonctionnalités

### Backend (Symfony)
Le backend est une API REST qui gère les données des personnes et de leurs emplois. Voici les fonctionnalités principales :

- **Gestion des personnes** :
  - Créer une personne (nom, prénom, date de naissance) via `POST /api/persons`.
  - Lister toutes les personnes avec leurs emplois actuels (ceux sans date de fin ou avec une date de fin dans le futur) via `GET /api/persons`. Retourne aussi l’âge calculé à partir de la date de naissance.
  
- **Gestion des emplois** :
  - Ajouter un emploi pour une personne (nom de l’entreprise, poste, date de début, date de fin optionnelle) via `POST /api/persons/{id}/employments`.
  - Lister les entreprises disponibles via `GET /api/companies`.
  - Lister tous les emplois regroupés par entreprise via `GET /api/employments`.
  - Récupérer l’historique des emplois d’une personne sur une période donnée (avec une date de début et une date de fin) via `GET /api/persons/{id}/employments/between-dates`.

### Frontend (Angular)
Le frontend est une interface utilisateur avec quatre onglets pour interagir avec l’API :

- **Onglet "Ajouter"** : Permet de créer une nouvelle personne (nom, prénom, date de naissance) ou d’ajouter un emploi pour une personne existante (entreprise, poste, dates).
- **Onglet "Personnes"** : Affiche la liste des personnes avec leur nom, prénom, âge, et leurs emplois actuels.
- **Onglet "Emplois"** : Permet de sélectionner une entreprise dans une liste déroulante et affiche tous les emplois associés (poste, personne, dates).
- **Onglet "Historique"** : Permet de choisir une personne et une période (date de début et date de fin) pour voir tous ses emplois sur cette période.

L’interface utilise Bootstrap pour un design clair avec des onglets, et affiche des messages de chargement ou d’erreur si besoin.

## Structure
- `/backend` : API REST développée avec Symfony.
- `/frontend` : Interface utilisateur développée avec Angular.

## Prérequis
- **PHP** : 8.1 ou supérieur
- **Composer** : Pour gérer les dépendances PHP
- **Node.js** : 18 ou supérieur (recommandé : version LTS comme 20.17.0 pour éviter les avertissements)
- **MySQL** : Pour la base de données
- **Symfony CLI** : Pour lancer le serveur Symfony (optionnel, mais recommandé)

## Installation

### 1. Cloner le projet
```bash
git clone <https://github.com/TheoDiaz/webatrio-test.git>
cd webatrio-test
```

### 2. Configurer le backend
1. Aller dans le dossier du backend :
   ```bash
   cd backend
   ```
2. Installer les dépendances PHP :
   ```bash
   composer install
   ```
3. Configurer la base de données :
   - Crée une base de données MySQL (par exemple, `webatrio`).
   - Configure les informations de connexion dans le fichier `.env` :
     ```env
     DATABASE_URL="mysql://utilisateur:motdepasse@127.0.0.1:3306/webatrio?serverVersion=8.0"
     ```
4. Exécuter les migrations pour créer les tables :
   ```bash
   php bin/console doctrine:migrations:migrate
   ```
5. Lancer le serveur Symfony :
   ```bash
   symfony server:start
   ```
   L’API sera accessible à `http://127.0.0.1:8000`.

### 3. Configurer le frontend
1. Aller dans le dossier du frontend :
   ```bash
   cd frontend
   ```
2. Installer les dépendances Node.js :
   ```bash
   npm install
   ```
3. Lancer l’application Angular :
   ```bash
   ng serve
   ```
   L’interface sera accessible à `http://localhost:4200`.

## Utilisation
1. Assurez-vous que le backend est lancé (`http://127.0.0.1:8000`).
2. Lancez le frontend et ouvrez `http://localhost:4200` dans votre navigateur.
3. Utilisez les onglets pour :
   - Ajouter une personne ou un emploi dans l’onglet "Ajouter".
   - Voir la liste des personnes dans l’onglet "Personnes".
   - Consulter les emplois par entreprise dans l’onglet "Emplois".
   - Voir l’historique des emplois d’une personne dans l’onglet "Historique".

## Tests
Les tests n’ont pas été implémentés dans ce projet. J’ai vérifié le bon fonctionnement des endpoints en utilisant **Postman** pour faire des appels manuels à l’API (par exemple, créer une personne, lister les emplois, etc.). Cependant, il n’y a pas de tests unitaires ou d’intégration codés, ce qui serait à ajouter pour une version plus robuste.

## Documentation de l’API
La documentation de l’API n’a pas pu être générée. J’ai essayé d’utiliser `NelmioApiDocBundle` pour créer une page Swagger à l’adresse `/api/doc`, mais j’ai rencontré plusieurs erreurs (configuration non reconnue, route introuvable, etc.) et j’ai fini par abandonner. Une solution alternative, comme **API Platform**, pourrait être envisagée pour générer automatiquement une documentation.

## Problèmes connus
- **Angular** : J’ai galéré avec Angular car je n’ai pas l’habitude de ce framework. Il pourrait y avoir des optimisations à faire dans le code frontend.
- **Node.js** : Un avertissement apparaît avec Node.js v23.4.0. Il est recommandé de passer à une version LTS comme 20.17.0.
- **Tests** : Absence de tests automatisés, ce qui pourrait poser des problèmes pour la maintenance.
- **Documentation** : Pas de documentation Swagger pour l’API, ce qui rend l’utilisation de l’API moins intuitive pour un nouvel utilisateur.

## Améliorations possibles
- Ajouter des tests unitaires et d’intégration pour le backend et le frontend.
- Mettre en place une documentation API avec API Platform ou une autre solution.
- Optimiser le code Angular pour une meilleure performance et lisibilité.
- Ajouter des fonctionnalités comme la modification ou la suppression de personnes et d’emplois.

## Auteur
Théo DIAZ