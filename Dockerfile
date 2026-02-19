FROM php:8.2-cli

RUN apt-get update && apt-get install -y \
    libzip-dev \
    unzip \
    && docker-php-ext-install mysqli

WORKDIR /app

COPY . .

# Dar permisos de escritura
RUN chmod -R 777 /app/uploads

EXPOSE 8080

CMD ["sh", "-c", "php -S 0.0.0.0:${PORT:-8080} -t ."]

