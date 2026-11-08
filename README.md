# DIAMONDX BD — Free Fire Player Information Checker

PHP 8.1+ and MySQL project. The public interface displays only readable visual cards. It does not expose raw JSON or source data to visitors.

## Important API inspection note
The configured endpoint is `https://auraffxserverinfocheckerapi.onrender.com/api/player?uid=...`.
During project generation, the API endpoint could not be fetched from the build environment, so its live JSON schema could not be independently verified. To avoid inventing API keys, the frontend reads the actual returned JSON recursively and displays only fields that exist. Known field names receive Bengali/English labels; unknown fields are shown using their original readable key. Missing values are shown as N/A. Account age / days since last login / expiry are calculated or shown only when the API supplies a usable creation, last-login, or expiry date field. No fake player values are generated.

## Requirements
- PHP 8.1+
- MySQL 5.7+ / MariaDB 10.4+
- PHP extensions: PDO MySQL, cURL (recommended), JSON, OpenSSL
- HTTPS strongly recommended

## Installation
1. Upload the complete project folder to your PHP hosting.
2. Create a MySQL database and import `database/install.sql` using phpMyAdmin or the MySQL CLI.
3. Copy `.env.example` to `.env` in the project root and set `DB_HOST`, `DB_NAME`, `DB_USER`, and `DB_PASS`.
4. Ensure the web server can read `.env` but cannot serve it publicly. The root `.htaccess` blocks direct access on Apache. Prefer placing secrets outside the document root when your host supports it.
5. Set the website document root to the project folder, or open `/public/index.php`. If using the project root as document root, configure your host to route `/` to `public/index.php` and deny direct access to `includes`, `database`, and `scripts`.
6. Create the first administrator from the server terminal:
   `php scripts/create_admin.php admin 'Use-A-Unique-Password-12chars-Minimum'`
   Replace the example password with a unique password of at least 12 characters. Do not run this from a public browser.
7. Open `/public/index.php` and test a real UID. Admin login is at `/admin/index.php`.

## API behavior
- Public browser calls only the local `/api/player.php` endpoint.
- PHP validates UID and calls the upstream API server-side.
- Upstream timeout: 15 seconds; connect timeout: 7 seconds.
- Upstream JSON is parsed and returned as data only after validation.
- Error states: invalid UID, not found, empty response, invalid JSON, timeout/network/server error, rate limit.
- The UI's 1–100 progress bar is explicitly visual-only and does not claim to represent real API progress.
- `player.php` reads `PLAYER_API_URL` from `.env`; admin settings currently save a display setting for API URL. For production changes, update `.env` (the server-side environment variable takes priority) and keep the endpoint trusted.

## Actual field mapping
No fixed schema is assumed. The UI recursively reads the actual API JSON response, groups fields by their real key names (BR, CS, guild, pet, equipment, identity and other), and shows values only if present. The label dictionary includes common field aliases. Extend `labelMap` in `public/assets/js/app.js` after inspecting your API's real response to improve translations. It does not fabricate fields.

## Security notes
- SQL uses PDO prepared statements.
- Admin passwords use `password_hash()` / `password_verify()`.
- Admin routes require a session login.
- Admin forms use CSRF tokens.
- API UID validation and per-IP rate limiting are enabled.
- Never commit `.env` to a public repository.
- Add HTTPS, server-side backups, log rotation, and hosting-level WAF/rate limits before public launch.
- Restrict access to the admin folder at the hosting layer if possible.
- Delete or protect the setup script after creating the administrator.

## Admin
- Dashboard: search counts, successes, failures, distinct IP count.
- Settings: site announcement and maintenance mode.
- History: latest 200 API search events.
- Password change: current password verification and new password hashing.
- Logo and visual branding are maintained in the stylesheet and page markup; upload your own logo asset and update the brand mark if needed.

## Notes
Like/comment is intentionally not presented as a global community feature because a real public shared system requires moderation, user identity controls, abuse prevention, and a database-backed posting workflow. Local recent searches are stored in the visitor's browser. The profile itself uses only API-returned data.
