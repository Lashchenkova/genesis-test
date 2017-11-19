FROM       php:7.1-fpm-alpine

RUN adduser -s /bin/ash -D docker
WORKDIR /var/www
RUN rm -rf /var/www/html

#Install PHP extensions
RUN echo "@main36 http://dl-cdn.alpinelinux.org/alpine/v3.6/main" >> /etc/apk/repositories \
    && apk update \
    && apk --no-cache add \
        $PHPIZE_DEPS \
        libxslt-dev \
        icu-dev \
        libjpeg-turbo \
        libpng-dev \
        libpng \
        libjpeg-turbo-dev \
        freetype-dev \
        freetype \
    && docker-php-ext-configure gd \
        --with-gd \
        --with-freetype-dir=/usr/include/ \
        --with-png-dir=/usr/include/ \
        --with-jpeg-dir=/usr/include/ \
    && pecl install \
        apcu \
    && docker-php-ext-install \
        opcache \
        pdo \
        pdo_mysql \
        xsl \
        intl \
        gd \
    && docker-php-ext-enable \
        apcu \
    && apk del --no-cache \
        freetype-dev \
        libpng-dev \
        libjpeg-turbo-dev

#Install utils
RUN apk add --no-cache \
        nano \
        git \
        apache-ant \
        openssl \
        nodejs

#Install Composer
ENV COMPOSER_HOME /home/docker/.composer
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php -r "if (hash_file('SHA384', 'composer-setup.php') === '544e09ee996cdf60ece3804abc52599c22b1f40f4323403c44d44fdfdd586475ca9813a858088ffbc1f233e9b180f061') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;" \
    && php composer-setup.php --version=1.5.2 --install-dir=/bin \
    && php -r "unlink('composer-setup.php');" \
    && ln -s /bin/composer.phar /bin/composer \
    && mkdir /home/docker/.composer/cache \
    && chown -R docker:docker /home/docker/.composer \
    && chmod -R g+w /home/docker/.composer
USER docker
RUN composer global require hirak/prestissimo \
    && chmod -R g+w /home/docker/.composer
USER root

COPY www.conf /usr/local/etc/php-fpm.d/www.conf
COPY php.ini /usr/local/etc/php/

#Install RabbitMQ
ENV RABBITMQ_C_VER 0.8.0

RUN set -xe \
  && apk add --update --no-cache \
    wget \
    cmake \
    openssl-dev \
  && rm -rf /var/cache/apk/* \

  # RabbitMQ C client
  && wget -qO- https://github.com/alanxz/rabbitmq-c/releases/download/v${RABBITMQ_C_VER}/rabbitmq-c-${RABBITMQ_C_VER}.tar.gz | tar xz -C /tmp/ \
  && cd /tmp/rabbitmq-c-${RABBITMQ_C_VER} \
  && mkdir -p build \
  && cd build \
  && cmake .. \
    -DCMAKE_INSTALL_PREFIX=/usr \
    -DCMAKE_INSTALL_LIBDIR=lib \
    -DCMAKE_C_FLAGS="$CFLAGS" \
  && cmake --build . --target install \
  && apk del \
    wget \
    cmake \
    openssl-dev

# Install tools...
RUN set -x \
  && docker-php-ext-install \
    bcmath \
    pcntl \
  && pecl install \
    amqp \
  && docker-php-ext-enable amqp