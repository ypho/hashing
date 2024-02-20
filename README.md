# Hashing
⚠️ NOTE: THIS PROJECT IS FOR EDUCATIONAL USES ONLY. DO NOT USE THIS FOR MALPRACTICE, OR IN ANY KIND OF PRODUCTION ENVIRONMENT!

This hashing project is part of a tech-talk about password security. It was built to demonstrate the effectiveness (or the lack thereof) of weaker hashing algorithms, and to show how stronger algorithms increase the security of your password handling.

For the latest recommendations about password storage, please check OWASP: https://cheatsheetseries.owasp.org/cheatsheets/Password_Storage_Cheat_Sheet.html

## Setup
This project runs completely in Docker. To start the container, just run: `docker compose up -d`

After the container is built, install all dependencies using: `docker compose exec -ti hashing composer install`

## Using the tool
When the Docker containers are running, and the dependencies are installed, you can run the different commands with: `docker compose exec -ti hashing php artisan [command]`

Make sure you add your password files to `resources/passwords` so they can be read.

## Understanding hashes and their algorithms
### The MD5 hashing algorithm
MD5 (Message Digest) is a very fast way of hashing a string. Due to its fast nature it is not recommended using this any longer.

Hashes made with `md5()` are of a fixed 32 character length, for example:
```
e8287934a8dfa8748cd5e55004cfec72
```

### The SHA hashing algorithm (SHA1 / SHA 256)
Two other very fast hashing algorithms. Both have a longer hash than the MD5-algorithm, but these are also insecure to store passwords in.

Example hashes of SHA1 / SHA256 are:
```
8c4fa960d7325151842e8fa355471d234b8c68d7 // SHA1
e733609dd3cf8948ef3711dcc5dd3a11f0d260dc3f32372f79d62e398555393d // SHA256
```

### The Bcrypt hashing algorithm with the Blowfish encryption algorithm
At this time, one of the go-to algorithm for storing passwords. When you generate a hash with bcrypt (and use the Blowfish encryption algorithm), your hash looks as follows: 
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
| pirate        | 4    | `$2y$04$J1oM/mBABB5f1OcwezmK7u3/nDwOsP.AS6yj82c/Ug0ocUfzW4kcu` |
| jacksparrow   | 4    | `$2y$04$SxExysyI8EF4G0eIszWQKuQ//PfYSN66QUb8m/TFHj7wPNHTwlQZe` |
| black_pearl   | 7    | `$2y$07$z/FSVOHhTC99F/IffmW9JO/iYeDkr568oyUUM6WWCgDMwu7CpXp.O` |
| bootstrapbill | 10   | `$2y$10$U9/KVfn4tlfRxI1Qa7Ml2eODNLC7sZLRcQuaL0D6MUcRiildpL7nW` |

### The Argon2i hashing algorithm
The recommended hashing algorithm at this time is Argon. The difference between Argon2i and Argon2id is that the latter uses a combination of Argon2i and Argon2d. A typical generated hash looks like this:
```
$argon2i$v=19$m=65536,t=4,p=1$cG95d0NpT3ZUYlp3RDdJYw$bomqAkyuAnwhxsoghgIASAl8zomckPX+aIbDnIDFggg
```
Like bcrypt, these hashes are separable by the $-sign, and each part has their own meaning:
1. `argon2i`, the exact algorithm used
2. `v=19`, the version of the algorithm that is used
3. `m=65536,t=4,p=1`, the allocated memory, time and threads for the hashing operation
4. `cG95d0NpT3ZUYlp3RDdJYw`, the generated salt for the hashing operation
5. `bomqAkyuAnwhxsoghgIASAl8zomckPX+aIbDnIDFggg`, the hash of the password itself

For Argon, it is possible to allocate more resources into generating a password hash. To do this, you can tweak these settings while calling the hashing-method.

## Development
### Running Tests & Code Quality
When PhpStorm is set up correctly, you can run the tests through Docker. To run them manually (including coverage), use: `docker compose exec -ti hashing vendor/bin/phpunit`

When building further on this code (and maybe creating a PR?), you can run PHPStan for static analysis: `docker compose exec -ti hashing vendor/bin/phpstan`