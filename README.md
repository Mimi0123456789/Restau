## Vite & Gourmand — Application Web de Gestion de Menus et Commandes

# Présentation du projet

**Vite & Gourmand** est une application web développée afin d'augmenter la visibilité et l'accessibilité de l'entreprise.

L’objectif principal de cet applicatif est de permettre de :

* consulter les menus ;
* gérer ses commandes ;
* administrer les menus et es plats ;
* gérer les employés ;
* suivre les commandes ;
* gérer les avis clients ;
* consulter des statistiques.

Le projet a été développé avec :

* **Laravel 11**
* **PHP 8.2**
* **MariaDB**
* **Laravel Breeze** pour l’authentification
* **Vite** pour la gestion des assets frontend
* **TailwindCSS** pour l’interface utilisateur

---

# Choix techniques et justification

## Laravel 11

Le framework Laravel 11 a été choisi car :

### Avantages

* architecture MVC ;
* système de routing ;
* ORM Eloquent ;
* migrations facilitant les versions de base de données ;
* gestion native des mails ;
* système d’authentification ;
* middleware permettant la sécurisation des routes ;
* bonne documentation ;
* rapidité de développement.

Laravel est adapté à une application CRUD comme celle-ci, en particulier pour :

* la gestion des menus ;
* les commandes ;
* les employés ;
* les différents paramètres (allergènes, thèmes...) ;
* les avis clients.

### Inconvénients

* framework relativement lourd ;
* nécessite une bonne compréhension des conventions Laravel.

## MariaDB

MariaDB a été utilisé comme système de gestion de base de données relationnelle.

### Avantages

* simple à mettre en place ;
* bonne compatibilité avec Laravel ;
* performances adaptées aux applications web ;
* bonne gestion des relations entre données ;
* solution open source.

La structure relationnelle était particulièrement adaptée pour gérer :

* les utilisateurs ;
* les rôles ;
* les menus ;
* les plats ;
* les allergènes ;
* les commandes ;
* les avis.

### Inconvénients

* moins performant que PostgreSQL pour certaines requêtes complexes ;
* fonctionnalités analytiques plus limitées ;
* gestion JSON moins avancée.

---

# Architecture du projet

Le projet suit l’architecture MVC de Laravel.

## Structure principale

```bash
app/
 ├── Http/
 │    ├── Controllers/
 │    ├── Middleware/
 │
 ├── Models/
 │
resources/
 ├── views/
 │
routes/
 ├── web.php
 │
database/
 ├── migrations/
 ├── seeders/
```

---

# Gestion des rôles

L’application possède plusieurs rôles :

| Rôle           | Description                          |
| -------------- | ------------------------------------ |
| Administrateur | Gestion complète de l’application    |
| Employé        | Gestion des menus, commandes et avis |
| Client         | Consultation et commandes            |

Les accès sont sécurisés via des middlewares Laravel.

---

# Authentification et sécurité

L’authentification repose sur Laravel Breeze.

Fonctionnalités mises en place :

* inscription ;
* connexion ;
* réinitialisation du mot de passe ;
* protection CSRF ;
* hash des mots de passe ;
* contrôle des rôles ;
* middleware de sécurité.

---

# 🍽️ Fonctionnalités principales

## Visiteur

* consultation des menus ;
* filtrage dynamique ;
* consultation des avis ;
* formulaire de contact ;
* création de compte.

## Client

* commande de menus ;
* panier ;
* suivi des commandes ;
* annulation/modification ;
* dépôt d’avis.

## Employé

* gestion des menus ;
* gestion des plats ;
* gestion des commandes ;
* validation des avis.

## Administrateur

* gestion des employés ;
* statistiques ;
* paramètres globaux ;
* gestion complète de l’application.

---

# Installation du projet

## 1. Cloner le projet

```bash
git clone Mimi0123456789/Restau
cd Restau
```

---

## 2. Installer les dépendances PHP

```bash
composer install
```

---

## 3. Installer les dépendances Node.js

```bash
npm install
```

---

## 4. Configurer l’environnement

Créer le fichier `.env` :

```bash
cp .env.example .env
```

Configurer :

```env
APP_NAME="Vite & Gourmand"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=restau
DB_USERNAME=root
DB_PASSWORD=

MONGODB_URI=mongodb://127.0.0.1:27017
MONGODB_DATABASE=restau_stats

MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@restau.fr"
MAIL_FROM_NAME="Vite & Gourmand"
```

---

## 5. Générer la clé Laravel

```bash
php artisan key:generate
```

---

## 6. Lancer les migrations

```bash
php artisan migrate
```

---

## 7. Lancer les seeders

```bash
php artisan db:seed
```

---

## 8. Compiler les assets frontend

```bash
npm run dev
```

ou

```bash
npm run build
```

---

## 9. Démarrer le serveur

```bash
php artisan serve
```

Application disponible sur :

```bash
http://127.0.0.1:8000
```

---

# Tests

Lancer les tests Laravel :

```bash
php artisan test
```

---

# Gestion des mails

Les mails sont utilisés pour :

* confirmation de commande ;
* création de compte ;
* réinitialisation de mot de passe ;
* notifications de commandes ;
* notifications de retour de matériel.

---

# Statistiques

Les statistiques administrateur permettent :

* le calcul du chiffre d’affaires ;
* le nombre de commandes par menu ;
* l’affichage graphique des données.

MongoDB est utilisé pour cette partie analytique.

---

# Accessibilité

Le projet tente de respecter les recommandations RGAA :

* structure HTML sémantique ;
* contrastes ;
* formulaires accessibles ;
* labels associés aux champs ;
* navigation clavier.

---

# RGPD et sécurité

Le projet prend en compte plusieurs éléments RGPD :

* stockage sécurisé des mots de passe ;
* limitation des accès ;
* gestion des rôles ;
* protection des données utilisateurs ;
* validation des formulaires.

---

# Déploiement

Le projet peut être déployé sur :

* Apache ;
* Nginx ;
* serveur Linux.

Configuration recommandée :

* PHP 8.2+
* MariaDB 10+
* Composer
* Node.js

---

# Perspectives d’amélioration

Améliorations possibles :

* paiement en ligne ;
* génération de factures PDF ;
* notifications temps réel ;
* suivi des stocks ;
* application mobile ;
* optimisation RGAA ;
* dashboard statistiques avancé.

---

# Auteur

Projet développé dans le cadre de la formation **Développeur web Full-stack** avec l'organisme STUDI.