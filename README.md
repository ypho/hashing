# Hashing
⚠️ NOTE: THIS PROJECT IS FOR EDUCATIONAL USES ONLY.

This hashing project is part of a tech-talk about password security. It was built to demonstrate the effectiveness (or the lack thereof) of weaker hashing algorithms, and to show how stronger algorithms increase the security of your password handling.

## Setup
This project runs completely in Docker. To start the container, just run: `docker compose up -d`

After the container is built, install all dependencies using: `docker compose exec -ti hashing composer install`

## Using the tool
When the Docker containers are running, and the dependencies are installed, you can run the different commands with: `docker compose exec -ti hashing php artisan [command]`

Make sure you add your password files to `resources/passwords` so they can be read.

## Understanding hashes
### Bcrypt / Blowfish
When you generate a hash with bcrypt and the Blowfish algorithm, your hash looks as follows: 
```
$2y$12$XBpA7aICGkWIL.w/5.Ze/OcRdEFGQ.wAoaCGWhWd9Es47F55eLncy
```
The password hash is a string of exactly 60 characters, and consists of a total of four parts, separated by the $-sign:
1. `2y`, this is the algorithm used, in this case Blowfish
2. `12`, after the algorithm, the _cost_ is defined, which is the amount of rounds used for hashing
3. `XBpA7aICGkWIL.w/5.Ze/O`, the first 22 characters of the final part is the salt used for your hash
4. `cRdEFGQ.wAoaCGWhWd9Es47F55eLncy`, the final 31 characters is the hash of the salt+password

Due to the working of bcrypt, any character of your raw password after 72 will be trimmed, so to prevent strange behaviour it is recommended using a password no longer than that.

Some example hashes:

| Password      | Cost | Hash                                                           |
|:--------------|:-----|:---------------------------------------------------------------|
| jacksparrow   | 4    | `$2y$04$SxExysyI8EF4G0eIszWQKuQ//PfYSN66QUb8m/TFHj7wPNHTwlQZe` |
| black_pearl   | 7    | `$2y$07$z/FSVOHhTC99F/IffmW9JO/iYeDkr568oyUUM6WWCgDMwu7CpXp.O` |
| bootstrapbill | 10   | `$2y$10$U9/KVfn4tlfRxI1Qa7Ml2eODNLC7sZLRcQuaL0D6MUcRiildpL7nW` |




## Development
### Running Tests & Code Quality
When PhpStorm is set up correctly, you can run the tests through Docker. To run them manually, use: `docker compose exec -ti hashing vendor/bin/phpunit`

When building further on this code (and maybe creating a PR?), you can run PHPStan for static analysis.