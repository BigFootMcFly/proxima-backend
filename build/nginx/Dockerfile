ARG ALPINE_VERSION=3.21

FROM alpine:${ALPINE_VERSION}

# initialize arguments
# php versions: 82, 83, 84
ARG PHP_VERSION=84
ARG USER_ID=1000
ARG GROUP_ID=1000
ARG WORKDIR=/app/Backend
ARG PHP_FPM_LISTEN_DIR=/app/run
ARG LOG_DIR=/app/log

ENV BUILD_CONTENTS=build/nginx
ENV PHP_INI_DIR=/etc/php${PHP_VERSION}

# install dependencies
RUN apk add \
    bash \
    supervisor \
    nginx \
    icu-data-full \
    php${PHP_VERSION} \
    php${PHP_VERSION}-fpm \
    php${PHP_VERSION}-curl \
    php${PHP_VERSION}-intl \
    php${PHP_VERSION}-dom \
    php${PHP_VERSION}-fileinfo \
    php${PHP_VERSION}-iconv \
    php${PHP_VERSION}-mbstring \
    php${PHP_VERSION}-openssl \
    php${PHP_VERSION}-pdo \
    php${PHP_VERSION}-pdo_sqlite \
    php${PHP_VERSION}-phar \
    php${PHP_VERSION}-opcache \
    php${PHP_VERSION}-session \
    php${PHP_VERSION}-simplexml \
    php${PHP_VERSION}-sqlite3 \
    php${PHP_VERSION}-tokenizer \
    php${PHP_VERSION}-xml \
    php${PHP_VERSION}-xmlreader \
    php${PHP_VERSION}-xmlwriter \
    php${PHP_VERSION}-zip \
    && ln -s /usr/bin/php${PHP_VERSION} /usr/bin/php

# add user and group for the app
RUN addgroup -g ${GROUP_ID} bot \
    && adduser -u ${USER_ID} -G bot -D bot

# install composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# copy the custom php.ini
COPY ${BUILD_CONTENTS}/config/99_custom_php.ini ${PHP_INI_DIR}/conf.d/99_custom_php.ini

# copy the custom fpm.ini
COPY ${BUILD_CONTENTS}/config/www.conf ${PHP_INI_DIR}/php-fpm.d/www.conf

# copy nginx config
COPY ${BUILD_CONTENTS}/config/nginx.conf /etc/nginx/nginx.conf

# copy nginx server config
COPY ${BUILD_CONTENTS}/config/default.conf /etc/nginx/http.d/default.conf

# copy supervisord config
COPY --chown=${USER_ID}:${GROUP_ID} ${BUILD_CONTENTS}/config/supervisord.conf /etc/supervisord.conf

# set php version for supervisord
RUN sed -i.bak "s/command=php-fpm -F/command=php-fpm${PHP_VERSION} -F/g" /etc/supervisord.conf

# setup workdir and php-fpm listener dir
RUN mkdir -p ${WORKDIR} ${PHP_FPM_LISTEN_DIR} ${LOG_DIR}

# change ownership of folders to the user
RUN chown -R ${USER_ID}:${GROUP_ID} ${WORKDIR} ${PHP_FPM_LISTEN_DIR} ${LOG_DIR}

# set tue workdir
WORKDIR ${WORKDIR}

# copy the application
COPY --chown=${USER_ID}:${GROUP_ID} src/ .

# change to the user
USER ${USER_ID}:${GROUP_ID}

# initialize composer
RUN mkdir -p storage/framework/views \
    && composer install \
    --no-interaction \
    --no-dev \
    --optimize-autoloader

#NOTE: this is a messy hack, there is no guaranty it wont be changed, use this only until a better way is found
RUN sed -i.bak "s/->modalWidth('md')/->modalWidth('xl')/g" vendor/joaopaulolndev/filament-edit-profile/src/Livewire/SanctumTokens.php

# copy the entrypoint script
COPY --chown=${USER_ID}:${GROUP_ID} --chmod=0555 ${BUILD_CONTENTS}/entrypoint.sh .

# define the storage volume
VOLUME [ "/app/Backend/storage" ]

VOLUME [ "/app/Backend/database/sqlite" ]

# define the exposed port
EXPOSE 9000

# start the app
CMD ["/usr/bin/supervisord"]
