# 📚 API LDVEH - Livre Dont Vous Êtes Le Héros

Bienvenue sur **api_LDVEH**, un projet Symfony qui permet de jouer à des Livres Dont Vous Êtes Le Héros (LDVEH) en mode API + front mobile. Ce projet gère les livres, les pages, les combats, les choix, les aventures… bref, une vraie app d’exploration narrative 🧙‍♂️🗺️

---

## ⚙️ Prérequis

- PHP ^8.3  
- Composer  
- Symfony CLI (optionnel mais recommandé)

---

## 🚀 Installation rapide

### 1. Cloner le projet

```bash
git clone https://github.com/clement-machtelinckx/api_LDVEH.git
cd api_LDVEH
```

### 2. Installer les dépendances

```bash
composer install
```

### 3. Créer la base de données et appliquer les migrations

```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

### 4. Importer les livres (4 tomes disponibles)

```bash
php bin/console app:import-books
```

### lancer les test unitaire

```bash
php vendor\phpunit\phpunit\phpunit

php vendor\bin\phpunit
```

---

## 💡 Fonctionnalités

- Navigation d’un paragraphe à l’autre via les choix  
- Gestion des monstres et des combats  
- Système d’aventure persistante par utilisateur  
- Écran de mort ou de victoire  
- Import JSON automatique des livres depuis un script  
- Structure extensible pour ajouter facilement d’autres livres

---

## 📱 Front mobile (React Native)

Un front mobile est également disponible ici 👉  
➡️ https://github.com/clement-machtelinckx/front_LDVEH

Il permet de :

- Se connecter / créer un compte  
- Visualiser la liste des livres  
- Créer un aventurier  
- Jouer et combattre, en gardant sa progression

---

# Tests

Ce projet utilise PHPUnit + Zenstruck Browser/Foundry pour les tests unitaires et fonctionnels.

## Prérequis

- PHP 8.3+
- Composer
- MySQL/MariaDB (recommandé pour les tests fonctionnels car les migrations utilisent des syntaxes MySQL)
- OpenSSL (pour générer les clés JWT de test)

## 1) Mise en place de l’environnement de test

### 1.1 Variables d’environnement `.env.test`

Crée/complète le fichier `.env.test` avec une base de test dédiée et des clés JWT de test :

```env
APP_ENV=test
APP_DEBUG=1
APP_SECRET='$ecretf0rt3st'

# DB MySQL de test (ne pas utiliser la DB de dev)
DATABASE_URL="mysql://root:YOUR_PASSWORD@127.0.0.1:3306/api_ldveh_test?serverVersion=8.0&charset=utf8mb4"

# JWT test keys
JWT_SECRET_KEY=%kernel.project_dir%/config/jwt/private-test.pem
JWT_PUBLIC_KEY=%kernel.project_dir%/config/jwt/public-test.pem
JWT_PASSPHRASE=test_passphrase
JWT_TOKEN_TTL=3600
```

⚠️ Si ta clé privée `private-test.pem` a été générée sans passphrase, mets :

```env
JWT_PASSPHRASE=
```

### 1.2 Générer les clés JWT de test

Crée le dossier :

```bash
mkdir -p config/jwt
```

Génère les clés (exemples OpenSSL) :

#### Sans passphrase (simple pour le test)

```bash
openssl genrsa -out config/jwt/private-test.pem 4096
openssl rsa -pubout -in config/jwt/private-test.pem -out config/jwt/public-test.pem
```

#### Avec passphrase

```bash
openssl genrsa -aes256 -passout pass:test_passphrase -out config/jwt/private-test.pem 4096
openssl rsa -pubout -passin pass:test_passphrase -in config/jwt/private-test.pem -out config/jwt/public-test.pem
```

### 1.3 Ignorer les clés test dans Git

Ajoute dans `.gitignore` :

```gitignore
/config/jwt/private-test.pem
/config/jwt/public-test.pem
```

## 2) Préparer la base de test (MySQL)

Les migrations utilisent des syntaxes MySQL (ex: COMMENT), donc SQLite n’est pas recommandé pour les tests.

⚠️ Vérifie que tu utilises une base dédiée : `api_ldveh_test`.

### Reset complet DB test

```bash
php bin/console doctrine:database:drop --env=test --force --if-exists
php bin/console doctrine:database:create --env=test
php bin/console doctrine:migrations:migrate --env=test --no-interaction
```

### Importer les livres (dataset réaliste)

Le projet contient une commande :

```bash
php bin/console app:import-books --env=test
```

Elle importe les livres JSON dans la DB test (books/pages/choices/monsters).  
⚠️ Cette commande n’est pas forcément idempotente : évite de la relancer sans reset DB si tu ne veux pas de doublons.

## 3) Lancer les tests

### Tous les tests

```bash
php vendor/bin/phpunit
```

### Un dossier

```bash
php vendor/bin/phpunit tests/Functional
php vendor/bin/phpunit tests/Unit
```

### Un fichier

```bash
php vendor/bin/phpunit tests/Functional/UserTest.php
```

## 4) Organisation recommandée des tests

### Tests unitaires

- rapides
- data minimale (Foundry factories)
- pas besoin d’importer les livres

Exemples :

- entités
- services (CombatService, AdventureService)
- listeners/processors

### Tests fonctionnels “API”

- testent les endpoints (auth, adventure, combat)
- utilisent Zenstruck Browser
- DB test reset + migrations

### Tests fonctionnels “dataset réel”

Pour tester la structure réelle des livres importés :

- reset DB
- migrations
- `app:import-books --env=test`
- tests sur `/api/books`, navigation, blocages de combat, fins, etc.

## 5) Routine rapide (commandes)

### Préparer DB test + importer + lancer tests

```bash
php bin/console doctrine:database:drop --env=test --force --if-exists
php bin/console doctrine:database:create --env=test
php bin/console doctrine:migrations:migrate --env=test --no-interaction
php bin/console app:import-books --env=test
php vendor/bin/phpunit
```

## 6) Notes utiles

Si tu vois des erreurs JWT en test (key not found / invalid key) :

- vérifie les chemins `JWT_SECRET_KEY` / `JWT_PUBLIC_KEY`
- vérifie que `JWT_PASSPHRASE` correspond à la manière dont la clé a été générée

Si un test fonctionnel retourne 401 :

- vérifie que tu passes bien les headers auth au format Zenstruck Browser :

```php
server => ['HTTP_AUTHORIZATION' => 'Bearer ...']
```
