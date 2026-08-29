# Info-Sup Digital

## 🌐 Présentation du projet

**Info-Sup Digital** est une application web développée en **PHP natif** pour présenter les services d'une agence digitale basée à Casablanca.

Le projet propose trois principaux axes :

* 💻 Création de sites web
* 🖥️ Vente de matériel informatique et de sécurité
* 🤖 Création d'agents d'intelligence artificielle

L'application comprend également un **espace d'administration sécurisé** permettant de gérer les produits et les messages reçus depuis le formulaire de contact.

---

## 🚀 Fonctionnalités

### 👤 Partie publique

* Page d'accueil
* Présentation des services
* Création de sites web
* Catalogue de matériel informatique
* Présentation des solutions d'intelligence artificielle
* Formulaire de contact
* Envoi du formulaire en AJAX
* Panier côté navigateur avec `localStorage`
* Commande via WhatsApp
* Interface responsive

### 🔐 Administration

* Authentification administrateur
* Dashboard
* Gestion des produits
* Ajout de produits
* Suppression de produits
* Upload d'images
* Consultation des messages clients
* Suppression des messages
* Statistiques sur les produits et contacts

---

## 🛠️ Technologies utilisées

| Technologie  | Utilisation                |
| ------------ | -------------------------- |
| PHP          | Backend et logique serveur |
| MySQL        | Base de données            |
| PDO          | Connexion et requêtes SQL  |
| HTML5        | Structure des pages        |
| CSS3         | Design et responsive       |
| JavaScript   | Interactions et AJAX       |
| Apache       | Serveur web                |
| `.htaccess`  | Routage et sécurité        |
| Git / GitHub | Gestion du code source     |

---

## 🏗️ Architecture

Le projet utilise une architecture **PHP natif avec une approche MVC légère**.

```text
CMS-WordPress/
│
├── index.php
├── web.php
├── materiel.php
├── ia.php
├── contact.php
├── .htaccess
│
├── includes/
│   ├── config.php
│   ├── functions.php
│   ├── header.php
│   └── footer.php
│
├── admin/
│   ├── index.php
│   ├── products.php
│   └── contacts.php
│
└── assets/
    ├── css/
    │   ├── style.css
    │   └── admin.css
    │
    ├── js/
    │   └── main.js
    │
    └── images/
```

### Architecture MVC légère

**Model**

* PDO
* MySQL
* `getDB()`
* `getProducts()`
* `getContacts()`
* fonctions d'accès aux données

**View**

* `index.php`
* `web.php`
* `materiel.php`
* `ia.php`
* `header.php`
* `footer.php`

**Controller**

* `contact.php`
* `admin/index.php`
* `admin/products.php`
* `admin/contacts.php`

---

## 🗄️ Base de données

Le projet utilise **MySQL** avec principalement deux tables :

### `products`

Stocke les produits du catalogue :

```text
id
category
brand
name
tag
price
old_price
description
features
image_url
is_featured
```

### `contacts`

Stocke les demandes envoyées par les visiteurs :

```text
id
name
email
phone
whatsapp
message
created_at
```

---

## 🔐 Sécurité

Plusieurs mécanismes de sécurité sont intégrés :

* Requêtes préparées avec PDO
* Protection CSRF
* `password_verify()` pour l'authentification
* Sessions PHP sécurisées
* Protection contre les tentatives de connexion répétées
* Validation des données utilisateur
* Validation des emails et téléphones
* Validation des fichiers uploadés
* Protection XSS avec `htmlspecialchars`
* Headers HTTP de sécurité
* Protection des fichiers sensibles avec `.htaccess`

---

## 🛒 Panier et WhatsApp

Le panier fonctionne côté navigateur avec :

```text
localStorage
```

Les produits sélectionnés sont stockés sous la clé :

```text
infosup_cart
```

Lors de la commande, JavaScript construit automatiquement un message contenant les produits et le montant estimé, puis l'utilisateur peut poursuivre la commande via WhatsApp.

---

## 📩 Formulaire de contact

Le formulaire fonctionne avec un système AJAX :

```text
Utilisateur
    ↓
Formulaire
    ↓
JavaScript
    ↓
AJAX POST
    ↓
contact.php
    ↓
Validation CSRF
    ↓
Validation des données
    ↓
MySQL
    ↓
Réponse JSON
    ↓
Message de succès
    ↓
WhatsApp
```

Cette approche permet d'envoyer le formulaire sans recharger complètement la page.

---

## 🔗 URLs principales

```text
/              → Accueil
/web           → Création Web
/materiel      → Catalogue matériel
/ia            → Agents IA
/contact       → Formulaire de contact
/admin/        → Administration
```

Les URLs propres sont gérées avec **Apache `.htaccess`**.

---

## ⚙️ Installation

### 1. Cloner le projet

```bash
git clone https://github.com/en-naji/PHP.git
```

### 2. Accéder au projet

```bash
cd PHP
```

### 3. Configurer MySQL

Créer la base de données :

```sql
CREATE DATABASE `info-sup`
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;
```

Créer ensuite les tables `products` et `contacts`.

### 4. Configurer la connexion

Dans :

```text
includes/config.php
```

configurer :

```text
DB_HOST
DB_NAME
DB_USER
DB_PASS
```

### 5. Lancer Apache et MySQL

Avec XAMPP, démarrer :

```text
Apache
MySQL
```

Puis accéder au projet depuis le navigateur.

---

## 👨‍💻 Créateurs

### EN-NAJI

GitHub :
https://github.com/en-naji

### AYOUB

GitHub :
https://github.com/ayoubELN

---

## 🎓 Objectif pédagogique

Ce projet a été réalisé dans le cadre d'un projet universitaire afin de mettre en pratique :

* PHP natif
* Programmation web
* MySQL
* Architecture MVC
* AJAX
* JavaScript
* Sécurité web
* Gestion des sessions
* Git et GitHub
* Administration d'une application web

---

## 📚 Documentation

La documentation technique complète est disponible dans :

```text
DOCUMENTATION_INFO_SUP.md
```

Elle présente notamment :

* l'architecture du projet ;
* le fonctionnement MVC ;
* la base de données ;
* les flux utilisateurs ;
* les flux administrateur ;
* la sécurité ;
* les différents contrôleurs ;
* les questions possibles de soutenance.

---

## 📄 Licence

Projet réalisé dans un cadre pédagogique.
