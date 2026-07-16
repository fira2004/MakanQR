FROM php:8.2-apache

# mysqli - for all the database connections (db.php uses this)
# curl  - for the Stripe/Fiuu API calls (stripe_helper.php, createFiuuPayment.php)
RUN apt-get update && apt-get install -y libcurl4-openssl-dev \
    && docker-php-ext-install mysqli curl \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Apache serves out of /var/www/html by default
COPY . /var/www/html/

# Render expects the container to listen on the $PORT it provides,
# but Apache's default config listens on 80. This makes Apache follow
# whatever port Render assigns.
RUN sed -i 's/80/${PORT}/g' /etc/apache2/ports.conf /etc/apache2/sites-enabled/000-default.conf
ENV PORT=80
EXPOSE 80

CMD ["apache2-foreground"]
