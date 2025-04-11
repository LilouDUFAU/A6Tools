# 📦 Projet de Stage BUT2 - A6Tools : Application web collaborative de gestion
<p align="center">
  <img src="./public/images/logo_a6tools.png" alt="logo_a6Tools" width="400"/>
</p>

## 🧾 Présentation du projet

Ce projet a été réalisé dans le cadre d’un stage de deuxième année de BUT Informatique. Il s’intègre dans le système d’information de l’entreprise **A6Landes Informatique**, spécialisée dans la vente, la gestion et le suivi de matériel informatique et technique. L’objectif principal du projet est la conception et la mise en place d’un **système de gestion centralisé**, permettant de suivre efficacement les commandes, les livraisons, les produits, les stocks, les interventions et les différents acteurs internes/externes.

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

