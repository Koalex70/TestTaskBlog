# Test Assignment: Blog Website (Pure PHP)

## 1) Task Statement

Build a simple but fully functional **Blog** website **without frameworks**.

## 2) Assignment Requirements

### Technology stack

- PHP 8.1+
- MySQL
- Smarty template engine
- Frameworks are not allowed

### Data structure

#### Category
- Name
- Description

#### Article
- Image
- Title
- Description
- Content
- Category (one or multiple)
- View count

### Required pages

#### Home page
- Display each category that contains articles
- For each such category, display 3 latest posts (by publication date)
- Display an "All Articles" button for each category

#### Category page
- Display category title
- Display category description
- Display list of category articles
- Implement article sorting:
  - by view count
  - by publication date
- Implement pagination

#### Article page
- Display full article information
- Display a block with 3 related articles

### Additional functionality

- Implement seeding for categories and articles

### Evaluation criteria

- Simplicity, readability, and code structure
- Project structure
- MySQL usage
- Level of independent implementation
- Depth of understanding of the solution

---

## 3) What Is Implemented

- Front controller + custom router (dynamic routes like `/category/{slug}`, `/post/{slug}`)
- Public pages:
  - Home (`/`)
  - Category (`/category/{slug}`) with sorting + pagination
  - Post (`/post/{slug}`) with related posts
  - Custom 404 page
- AJAX category pagination/sorting (`/api/category/{slug}`)
- Breadcrumbs:
  - `Blog / Category`
  - `Blog / Post` or `Blog / Category / Post` (based on navigation context)
- DB tools page (`/db-tools`, dev only):
  - Apply migrations
  - Fill DB
  - Clear DB
- Migrations + seeding:
  - Categories, posts, many-to-many relation (`post_category`)
  - Unique views table (`post_views`)
  - Random post publication dates
  - Post-to-multiple-categories seeding
- Unique post views by IP:
  - One view counted per `(post_id, REMOTE_ADDR)`
  - Fast list sorting still uses denormalized `posts.views_count`
- SCSS build pipeline with minified CSS output
- Docker environment for PHP, Nginx, and MySQL
- PHPUnit test setup with isolated test DB (`blog_test`)

---

## 4) Project Structure (key folders)

- `src/` — application code (controllers, repositories, services, DB)
- `templates/` — Smarty templates
- `public/` — entrypoint + static assets
- `config/` — configuration files
- `tests/` — PHPUnit tests
- `docker/` — Docker configs

---

## 5) Run with Docker (Primary)

### Prerequisites

- Docker + Docker Compose

### Setup

1. Copy environment file:

```bash
cp .env.example .env
```

2. Start containers:

```bash
docker compose up -d --build
```

3. Install PHP dependencies:

```bash
docker compose exec -T php composer install
```

4. Install Node dependencies (host):

```bash
npm install
```

5. Build CSS:

```bash
npm run build:css
```

6. Open app:

- [http://localhost:8080](http://localhost:8080)

7. Initialize DB (web UI):

- Open [http://localhost:8080/db-tools](http://localhost:8080/db-tools)
- Run:
  - `Apply Migrations`
  - `Fill DB`

### Watch SCSS

```bash
npm run watch:css
```

---

## 6) Local Alternative (Optional)

If you prefer running tooling locally:

```bash
composer install
npm install
npm run build:css
```

You still need MySQL/PHP web server configured with the same env settings.

---

## 7) Testing

### What is covered

- `PostViewService` unique-view behavior
- `PostRepository::findRelatedByPostId()` ordering logic
- HTTP smoke tests for:
  - `/api/category/{slug}`
  - `/post/{slug}`

### Run tests

```bash
docker compose exec -T php vendor/bin/phpunit
```

### Test DB isolation

Tests run against a separate DB (`blog_test`) from `tests/bootstrap.php`.
Optional env overrides are available:

- `DB_TEST_DATABASE`
- `DB_TEST_USERNAME`
- `DB_TEST_PASSWORD`

---

## 8) Useful Commands

### DB tools

- App URL: [http://localhost:8080/db-tools](http://localhost:8080/db-tools)

### CSS

```bash
npm run build:css
npm run watch:css
```

### PHP autoload refresh

```bash
docker compose exec -T php composer dump-autoload
```

---

## 9) Notes

- `/db-tools` is available only in `APP_ENV=dev`
- `post_views` stores unique viewers, `posts.views_count` is kept for fast sorting/UI
- Category page uses AJAX for pagination/sorting and updates URL via History API

---

## 10) AI Usage

AI was used for the following tasks:

1. Assistance with setting up the Docker environment.
2. Creating the concept/mockup for the 404 page.
3. Building the `db-tools` page layout.
4. Writing and structuring this `README.md` file.
