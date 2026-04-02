# timurhankaya.dev

Personal website and playground project built with Laravel + Livewire.

This repository includes:
- Portfolio homepage and contact pages
- CV page
- Blog (listing and post detail pages)
- Multiplayer mini-games:
  - Imposter
  - Vampire
- SEO utilities (localized URLs, sitemap, robots)
- EN/TR content support

## Tech Stack

- PHP 8.4
- Laravel 12
- Livewire 4
- Tailwind CSS 4
- Vite 7
- GSAP
- Pest (testing)
- Optional Redis support via Predis

## Quick Start

### 1) Install dependencies and prepare app

```bash
composer run setup
```

This script installs PHP/Node dependencies, prepares `.env`, generates `APP_KEY`, runs migrations, and builds assets.

### 2) Start local development

```bash
composer run dev
```

This runs:
- Laravel dev server (`0.0.0.0`)
- Queue listener
- Log tailing (`pail`)
- Vite dev server

## Useful Commands

```bash
# Run test suite
composer run test

# Run imposter-focused coverage check
composer run test:imposter-coverage

# Build frontend assets for production
npm run build
```

## Main Routes

- `/` - Home / portfolio
- `/contact` - Contact
- `/cv` - CV
- `/games` - Games hub
- `/games/imposter`
- `/games/vampire`
- `/blog`
- `/sitemap.xml`
- `/robots.txt`

## Project Structure (high level)

- `app/Livewire/Games` - Interactive game flows
- `app/Services` - Domain/game/business services
- `resources/views/pages` - Main page views
- `resources/views/livewire` - Livewire blade views
- `resources/content` - Portfolio/game content (EN/TR)
- `lang` + `resources/lang` - Localization strings
- `tests` - Pest feature and unit tests

## Environment Notes

- Copy `.env.example` to `.env` if you do not use the setup script.
- Configure database credentials in `.env`.
- Optional: configure Redis if you want cache/session/queue improvements.

## License

MIT (see `LICENSE`).
