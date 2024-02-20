# Hashing
⚠️ **NOTE: THIS PROJECT IS FOR EDUCATIONAL USES ONLY. DO NOT USE THIS FOR MALPRACTICE, OR IN ANY KIND OF PRODUCTION ENVIRONMENT!**

This hashing project is part of a tech-talk about password security. It was built to demonstrate the effectiveness (or the lack thereof) of weaker hashing algorithms, and to show how stronger algorithms increase the security of your password handling.

For the latest recommendations about password storage, please check OWASP: https://cheatsheetseries.owasp.org/cheatsheets/Password_Storage_Cheat_Sheet.html

## Setup
This project runs completely in Docker. To start the container, just run the command below. The first time you run this command, some images will be downloaded before the container is built.
```shell
docker compose up -d
````

After the container is built, install all dependencies using: 
```shell
docker compose exec -ti hashing composer install
```

Before using, you can check if everything works in order, by running the test suite:
```shell
docker compose exec -ti hashing vendor/bin/phpunit
```

## Understanding hashes and their algorithms
Of course, you can dive right in. But understanding the idea behind hashing, what it exactly means, and what hashes are built of gives a better understanding of the process itself. Since this project is part of a tech-talk, you can find the slides in the `resources/slides` folder, which might help you better understand the idea of hashing. For a little explanation on the different algorithms, see the blocks below.

### The MD5 hashing algorithm
MD5 (Message Digest) is a very fast way of hashing a string. Due to its fast nature it is not recommended using this any longer.

Hashes made with `md5()` are of a fixed 32 character length, for example:
```php
echo hash('md5', 'black_pearl'); // e8287934a8dfa8748cd5e55004cfec72
```

### The SHA hashing algorithm (SHA1 / SHA 256)
Two other very fast hashing algorithms. Both have a longer hash than the MD5-algorithm, but these are also insecure to store passwords in.

Example hashes of SHA1 / SHA256 are:
```php
echo hash('sha1', 'pirateship'); // c89720b88c227c8319950437aa6bbd7b1f10b9e1
echo hash('sha256', 'captainjacksparrow'); // 8a2bb5c683a6e540ff545b6eb1b556eb67d9448d8d7c761dd3c6baeb6565fd31
```

### The Bcrypt hashing algorithm with the Blowfish encryption algorithm
At this time, one of the go-to algorithm for storing passwords. When you generate a hash with bcrypt (with the Blowfish encryption algorithm), your hash looks as follows:
```php
echo password_hash('barbossa', PASSWORD_BCRYPT); // $2y$10$S8xYSsM1AdzzhwFzjqG5P.jiuQ7.Df4qwkHRVcbJrNg.ODY7Pz0a6
```
The password hash is a string of exactly 60 characters, and consists of a total of four parts, separated by the $-sign:
1. `2y`, this is the algorithm used, in this case Blowfish
2. `10`, after the algorithm, the _cost_ is defined, which is the amount of rounds used for hashing
3. `S8xYSsM1AdzzhwFzjqG5P.`, the first 22 characters of the final part is the salt used for your hash
4. `jiuQ7.Df4qwkHRVcbJrNg.ODY7Pz0a6`, the final 31 characters is the hash of the password

⚠️ Due to the working of bcrypt, any character of your raw password after 72 will be trimmed, so to prevent strange behaviour it is recommended using a password no longer than that.

### The Argon hashing algorithm
The recommended hashing algorithm at this time is Argon. The difference between Argon2i and Argon2id is that the latter uses a combination of Argon2i and Argon2d. A typical generated hash looks like this:
```php
echo password_hash('elizabeth_swann', PASSWORD_ARGON2I); // $argon2i$v=19$m=65536,t=4,p=1$R0Mwb1hDdVNkZDNZbjMydA$WiOWwe6VXFdp3ABB326Is5tUClKmJlNr6fFD3dfHbeA
```
Like bcrypt, these hashes are separable by the $-sign, and each part has their own meaning:
1. `argon2i`, the exact algorithm used
2. `v=19`, the version of the algorithm that is used
3. `m=65536,t=4,p=1`, the allocated memory, time and threads for the hashing operation
4. `R0Mwb1hDdVNkZDNZbjMydA`, the generated salt for the hashing operation
5. `WiOWwe6VXFdp3ABB326Is5tUClKmJlNr6fFD3dfHbeA`, the hash of the password itself

For Argon, it is possible to allocate more resources into generating a password hash. To do this, you can tweak these settings while calling the hashing-method.

## Using the tool
When the Docker containers are running, and the dependencies are installed, you can see the available commands with: 
```shell
docker compose exec -ti hashing php artisan
```

### Brute Forcing
If you want to try and brute force passwords, add your password files to `resources/passwords` so they can be read. The password files should have one password per line.

Two commands are made for brute forcing, one for the weaker algorithms (MD5, SHA1, SHA2), and one for the stronger algorithms (bcrypt and Argon).

```shell
docker compose exec -ti hashing php artisan hash:bruteforce:weak '9fce85a18bbfb53d0ededa47a7302ccba1e1d4f7'
```

```shell
docker compose exec -ti hashing php artisan hash:bruteforce:strong '$2y$04$J1oM/mBABB5f1OcwezmK7u3/nDwOsP.AS6yj82c/Ug0ocUfzW4kcu'
```

Please keep in mind that especially the strong algorithms can take a long time to run. If you want to do some tests on the stronger algorithms, try and generate some hashes with lower resources.

### Benchmarking
To demonstrate how fast or slow certain algorithms are, you can run the benchmarks. These benchmarks will hash 16bits random strings.

For the stronger algorithms (Bcrypt and Argon), you can tweak the settings to make stronger hashes. Use the commands below:
```shell
# Generate 100 Argon hashes with a maximum of 32000 KiB of memory per hash operation, increasing this value uses more memory (and more time), but generates more secure hashes
# Other options are --t for maximum time per operation and --p for the amount of parallel threads per operation
docker compose exec -ti hashing php artisan hash:benchmark:argon 100 --m=32000
```

```shell
# Generate 100 Bcrypt hashes with 4 rounds per hash operation, increasing this value takes more time, but generates more secure hashes
# By default, the cost is set to 10
docker compose exec -ti hashing php artisan hash:benchmark:bcrypt 100 --cost=4
```

```shell
# Generate 1000000 MD5 hashes, replace md5 with sha1 or sha256 for other algorithms
docker compose exec -ti hashing php artisan hash:benchmark:md5 1000000
```

## Development
### Running Tests & Code Quality
When PhpStorm is set up correctly, you can run the tests through Docker. To run them manually (including coverage), use: 
```shell
docker compose exec -ti hashing vendor/bin/phpunit
```

When building further on this code (and maybe creating a PR?), you can run PHPStan for static analysis: 
```shell
docker compose exec -ti hashing vendor/bin/phpstan
```

If you have `nektos/act` running locally, it is possible to check if the GitHub workflow passes, you can use the following command:
```shell
act -P ubuntu-latest=shivammathur/node:latest
```