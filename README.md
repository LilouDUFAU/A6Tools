<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com/)**
- **[Tighten Co.](https://tighten.co)**
- **[WebReinvent](https://webreinvent.com/)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel/)**
- **[Cyber-Duck](https://cyber-duck.co.uk)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Jump24](https://jump24.co.uk)**
- **[Redberry](https://redberry.international/laravel/)**
- **[Active Logic](https://activelogic.com)**
- **[byte5](https://byte5.de)**
- **[OP.GG](https://op.gg)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

# 📦 Projet de Stage BUT2 - A6Tools : Application web collaborative de gestion
<p align="center">
  <img src="./public/images/logo_a6tools.png" alt="logo_a6Tools" width="200"/>
</p>

## 🧾 Présentation du projet

Ce projet a été réalisé dans le cadre d’un stage de deuxième année de BUT Informatique. Il s’intègre dans le système d’information de l’entreprise **A6Tools**, spécialisée dans la vente, la gestion et le suivi de matériel informatique et technique. L’objectif principal du projet est la conception et la mise en place d’un **système de gestion centralisé**, permettant de suivre efficacement les commandes, les livraisons, les produits, les stocks, les interventions et les différents acteurs internes/externes.

---

## 🎯 Objectifs

- Modéliser une base de données complète répondant aux besoins métier.
- Développer une interface web fonctionnelle pour la gestion du système.
- Assurer la traçabilité des produits depuis le fournisseur jusqu’au client.
- Automatiser les processus internes : commandes, préparation, livraison, suivi des pannes.
- Faciliter l’interaction entre les différents utilisateurs : clients, fournisseurs, employés.

---

## 🧱 Architecture de la base de données

Le schéma relationnel comprend les entités principales suivantes :

- **Employé / Client / Fournisseur / Utilisateur**
- **Produit / Stock / Commande / Bon de Livraison**
- **Panne / Action / Préparation Atelier**
- **Rôle / Permission**

Les relations ont été conçues pour garantir la cohérence des données, comme présenté dans le fichier `bd_finale.pdf`.

---

## 📚 Fonctionnalités

- Gestion des utilisateurs avec rôles et permissions
- Suivi des produits, des stocks et des commandes
- Préparation et suivi des installations
- Historique des interventions et pannes
- Communication structurée avec les fournisseurs

---

## 🛠️ Technologies utilisées

### 🔧 Environnement & Frameworks
- **Laravel** – Framework PHP MVC utilisé pour la structure du projet
- **Tailwind CSS** – Framework CSS pour un design moderne et réactif
- **WAMP** – Serveur local (Windows, Apache, MySQL, PHP)
- **IONOS** – Hébergement du projet (domaine + base de données)

### 💻 Langages
- **HTML / CSS**
- **JavaScript**
- **PHP**
- **SQL**

### 🗃️ Base de données
- **MySQL** – Base de données relationnelle
- **phpMyAdmin** – Interface de gestion de la base de données

---

## 📄 Documentation jointe

- `Cahier_des_charges.pdf` – Spécifications fonctionnelles et techniques
- `bd_finale.pdf` – Schéma relationnel complet de la base de données

---

## 🔄 Évolutions futures

- Mise en place de notifications et alertes pour les actions critiques
- Ajout de statistiques et de tableaux de bord pour le suivi d’activité
- Interconnexion avec EBP via API pour une gestion comptable fluide
- Sécurisation avancée des accès et des données

