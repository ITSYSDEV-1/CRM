# PT Ramayana Global Pratama – CRM System

CRM (Customer Relationship Management) system used across all hotel units under **PT Ramayana Global Pratama**. The system is designed to support daily hotel operations, manage guest data, and enable customer segmentation.

It is integrated with **Pepipost** for automated email notifications, such as guest reminders and promotional campaigns.

## ✨ Key Features

- Guest profile and data management
- Email notifications via Pepipost
- Customer segmentation and status tracking
- Daily activity logging
- Role-based access control

## 🛠 Tech Stack

- **PHP** (Laravel Framework)
- **MySQL**
- **Pepipost API**
- **JavaScript**

## ⚙️ Setup Guide

Make sure `.env` and database backups are not committed (see `.gitignore`).

```bash
cp .env.example .env
composer install
php artisan key:generate
php artisan migrate --seed
php artisan serve
