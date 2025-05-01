#!/bin/bash

# constants
dataBaseFile=database/sqlite/database.sqlite

# ---------------------------------------------------------------------------
# functions

# Creating storage folders
function create_storage_folders {

    # create storage folders for laravel
    mkdir -p ./storage/framework/{cache,sessions,testing,views} || {
        # fail if something went wrong
        php artisan cli:error "Making storage folders failed!"
        exit 1
    }

    # create log folder
    mkdir -p ./storage/logs || {
        # fail if something went wrong
        php artisan cli:error "Making log folder failed!"
        exit 1
    }

}

# Initalizing caches
function initalize_cache {

    # populating/updating caches
    php artisan optimize \
    && php artisan filament:cache-components || {
        # fail if something went wrong
        php artisan cli:error "Clearing cache failed!"
        exit 5
    }

}

# initialize environment file
function initalize_environment_file() {

    # if the .env doesn't exists, create it
    [[ ! -f .env ]] && {

        # fail if environment cofig doesn't exists
        [[ -f .env.config ]] || {
            php artisan cli:error "Please provide a \".env.config\" file!"
            exit 7
        }

        # fail if environment cofig is not readable
        [[ -r .env.config ]] || {
            php artisan cli:error "Cannot read \".env.config\" file! Please check permissions!"
            exit 7
        }

        # create the custom environment file
        cp .env.config .env
        return
    }

    # if .env already exists and no config file is found, nothing to do
    [[ ! -f .env.config ]] && {
        return
    }

    # if the config file has ben updated since creating the .env file
    [[ ".env.config" -nt ".env" ]] && {

        # backup the current configuration
        cp .env .env.old

        # fail if environment cofig isn't readable
        [[ -r .env.config ]] || {
            php artisan cli:error "Cannot read \".env.config\" file! Please check permissions!"
            exit 7
        }

        # create our custom environment file
        cp .env.config .env
    }

}

# create and save a new encryption key if it does not exists
function generate_application_key {

    php artisan state:initialized --silent || {

        php artisan cli:info "Running for the forst time, generating application key"

        local key=$(dd if=/dev/urandom bs=32 count=1 2>/dev/null | base64) \
        && sed s~APP_KEY=.*\$~APP_KEY\=base64:$key~ .env > /tmp/.env \
        && cat /tmp/.env > .env || {
            # fail if something went wrong
            php artisan cli:error "Generating the encription key failed!"
            exit 2
        }
    }

}

# run database migration
function migrate_database {

    # fail if the database file is not writeable
    [[ ! -w $dataBaseFile ]] && {
        php artisan cli:error "Cannot write databese! Please check permissions!"
        exit 3
    }

    # create a backup - NOTE: this may not be a good practice...
    cp $dataBaseFile $dataBaseFile.bak

    php artisan migrate --force  || {
        # fail if something went wrong
        php artisan cli:error "Migration failed! See storage/logs/laravel.log for more info."
        exit 3
    }

}

# run databesa seeding
function seed_database {

    #NOTE: this only creates the admin user if it does not exists yet
    php artisan db:seed --force || {
        # fail if something went wrong
        php artisan cli:error "Seeding failed! See storage/logs/laravel.log for more info."
        exit 4
    }

}

# start a supervisord process
# @internal
# @param string $1 command name example: "bash"
# @param string $2 command display name example: "Bourne Again Shell"
function start_process {

    # variables
    local process=$1
    local process_name=$2

    # log message
    php artisan cli:info "Starting ${process_name} process..."

    # start the process
    supervisorctl start ${process} || {
        # log and fail if the process creation failed
        php artisan cli:error "Could not start ${process_name} process, exiting :("
        supervisorctl shutdown
        exit 99 # this should never be called
    }

}

# start supervisord defined server processes
function start_server_processes {

    # start php
    start_process "php-fpm" "PHP-Fpm"

    # start nginx
    start_process "nginx" "Nginx"

    # start schedule:work
    start_process "scheduler" "Scheduler"

}

# ---- main -----------------------------------------------------------------

# Initalizing the environment file
initalize_environment_file

# Creating storage folders
create_storage_folders

# generating application encryption key
generate_application_key

# migrating the database
migrate_database

# seed the database (if not already seeded)
seed_database

# clear cache on (re)start
initalize_cache

# adding notice to the app log
echo -e "$(date +'[%Y-%m-%d %H:%M:%S]') production.NOTICE: Entrypoint reached.\n" >> storage/logs/laravel.log

# starting server processes
start_server_processes

# confirm successfull program startup
php artisan cli:info "Program stared succesfully."

exit 0
