![Docker build](https://proxima.goliath.hu/proxima/backend/actions/workflows/latest.yaml/badge.svg)
![Docker build](https://proxima.goliath.hu/proxima/backend/actions/workflows/testing.yaml/badge.svg?branch=dev)

![Proxima Discord bot](res/logo.svg)

# Proxima Backend

[Proxima](https://proxima.goliath.hu) is a **Remainder Application**, like many others, which primarily uses [Discord](https://discord.com/) to deliver the remainders to the user.

It provides an API interface to be embedded in other services, using Bearer Token Authentication.

This is the source code for the backend application.

This backend handles all the data managment and provides the API interface for the clients to be used.

This backend application is intended to be run as a docker container, see below.

For more details, see: [Proxima -> Backend](https://proxima.goliath.hu#backend)

# Main Technologies used
- [PHP 8.4+](https://www.php.net/)
- [composer](https://getcomposer.org/)
- [Laravel 11+ ](https://laravel.com/)
- [Sanctum](https://laravel.com/docs/11.x/sanctum)
- [Livevire](https://laravel-livewire.com/)
- [Filament 3+](https://filamentphp.com/)
- [Scribe](https://scribe.knuckles.wtf/laravel)
- and many more...

# What is needed to run the backend

- A working docker instance with compose.

# Runninig with docker

### Follow these easy steps to setup and run the backend application:

> **NOTE** The program inside the container is running under a **non-privileged** user with
>
> `UID:1000` and `GID:1000`.
>
> The database file/folder must be writable for that user!
>
> In the example we just make it writable to everyone (please use proper access control instead!)
>
> *Or jou can build your own image with different `UID`/`GID` values.*

#### 1. Create the directory structure (bash):

```bash
mkdir -p volumes/backend/{database,env}
```

#### 2. Update directory permissions to allow write access to the restricted docker user

```bash
chmod o+w volumes/backend/database
```

#### 3. (OPTIONAL) If you already have a working database file, you can copy it to the app:

```bash
  cp PATH/TO/DATABASE/<database.sqlite> volumes/backend/database/
  chmod o+w volumes/backend/database/database.sqlite
```

#### 4. Create the `.env.config` file from [src/.env.example](src/.env.example) file.

```bash
wget -O volumes/backend/env/.env.config https://proxima.goliath.hu/proxima/backend/raw/branch/main/src/.env.example

-OR-

curl -f -o volumes/backend/env/.env.config https://proxima.goliath.hu/proxima/backend/raw/branch/main/src/.env.example

```
#### 5. Customize the `.env.config` file.

> **NOTE**: The program creates its own `.env` file based on the `.env.config` file.<br>
> If you modify your `.env.config` file, simply restart the container and the new settings will take effect immediately.

```bash
nano volumes/backend/env/.env.config
```
1. Replace or fill in the following values (after the "=" sign)

    1. **APP_NAME** - The application's name <br>
        *defualt:* "`Backend`"
    1. **APP_TIMEZONE** - The application's timezone<br>
      *default:* "`UTC`"
    1. **APP_URL** - The application's url<br>
      *default:* "`localhost:9000`"
    1. **ADMIN_EMAIL** - The admin email for the backend<br>
      *default:* "`admin@example.com`"
    1. **ADMIN_PASSWORD** - The admin password for the backend<br>
      *default:* "`PlEaSe_ChAnGe_Me`"

#### 6.1 Run with docker

```bash
docker run \
    -it \
    --rm \
    --name "proxima-backend" \
    -v "./volumes/backend/env/.env.config:/app/Backend/.env.config" \
    -v "./volumes/backend/database/:/app/Backend/database/sqlite/" \
    -p "9000:9000" \
    git.magrathea.hu/proxima/backend:latest
```

#### 6.2 Running with docker compose

#### 6.2.1 Create docker compose file [`docker-compose.yaml`](res/docker-compose.yaml):

```yaml
---
services:
    backend:
      image: proxima.goliath.hu/proxima/backend:latest
      container_name: backend
      ports:
        - "9000:9000"
      volumes:
        - "./volumes/backend/env/.env.config:/app/Backend/.env.config"
        - "./volumes/backend/database/:/app/Backend/database/sqlite/"
      restart: unless-stopped
```

#### 6.2.2 Start up service

```bash
docker compose up -d
```

#### 6.3 You can watch the logs of the application

```bash
docker compose logs -f
```


