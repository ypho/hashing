# Hashing
⚠️ NOTE: THIS PROJECT IS FOR EDUCATIONAL USES ONLY.

This hashing project is part of a tech-talk about password security. It was built to demonstrate the effectiveness (or the lack thereof) of weaker hashing algorithms, and to show how stronger algorithms increase the security of your password handling.

## Setup
This project runs completely in Docker. To start the container, just run: `docker compose up -d`

After the container is built, install all dependencies using: `docker compose exec -ti hashing composer install`

## Using the tool
When the Docker containers are running, and the dependencies are installed, you can run the different commands with: `docker compose exec -ti hashing php artisan [command]`

### Running Tests & Code Quality
When PhpStorm is set up correctly, you can run the tests through Docker. To run them manually, use: `docker compose exec -ti hashing vendor/bin/phpunit`

When building further on this code (and maybe creating a PR?), you can run PHPStan for static analysis.