# Tampakan Directory 🏙️

![Tampakan Directory Banner](file:///Users/nc/.gemini/antigravity/brain/1ca228c7-2a4d-4412-ae23-438bd017bff3/github_repo_banner_1774986233657.png)

The **Tampakan Directory** is a high-performance, community-focused platform designed to connect residents, tourists, and businesses in Tampakan, South Cotabato. 

Built with a "Zero-Dependency" philosophy, it prioritizes speed, accessibility, and a premium user experience across all devices.

## 🚀 Key Features

- **Global Dark Mode (Clean Sweep)**: A robust, semantic-token-based theme system that adapts seamlessly to user preferences.
- **Role-Based Access Control**: Secure authentication for standard users, business owners, and administrative staff.
- **Smart Search & Explore**: Real-time business search and interactive maps powered by Leaflet and OpenStreetMap.
- **SEO & AEO Optimized**: Automated Schema.org (JSON-LD) generation to ensure maximum visibility in search engines and AI answer engines.
- **PWA Ready**: Offline capabilities and mobile-first design for the "local highland" environment.

## 🛠️ Tech Stack

- **Backend**: PHP 8.1+ (Modular Architecture)
- **Database**: SQLite (Local Dev) / MySQL (Production Support)
- **Frontend**: Vanilla CSS (CSS Variables & Semantic Tokens)
- **Scripts**: Vanilla JS (No Frameworks, Fast Interaction)
- **Icons**: [Lucide Icons](https://lucide.dev/)
- **Maps**: [Leaflet.js](https://leafletjs.com/)

## 📦 Setup & Installation

### 1. Requirements
- PHP 8.1 or higher
- SQLite Extension (for local dev)
- Apache/Nginx (recommended but optional for local)

### 2. Local Initialization
1. Clone the repository: `git clone https://github.com/songpig-frank/city-directory.git`
2. Create your configuration: `cp config.example.php config.php`
3. Initialize the database and demo data:
   ```bash
   php database/setup-local.php
   ```
4. Start the local server:
   ```bash
   php -S localhost:8080 -t .
   ```
5. Open `http://localhost:8080` in your browser.

## 🛡️ Security
This project follows strict security practices:
- **CSRF Protection** on all forms.
- **Rate Limiting** on authentication endpoints.
- **Security Headers** enabled by default (CSP, X-Frame-Options, etc.).
- **Password Hashing** via Bcrypt.

## 🏗️ Work in Progress
Active development continues on the `dev` branch. See [PROGRESS.md](file:///Users/nc/tampakan_com/PROGRESS.md) for the latest updates and roadmap.
