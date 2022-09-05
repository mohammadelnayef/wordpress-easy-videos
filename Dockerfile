FROM wordpress:latest
ENV WORDPRESS_DEBUG: 1
WORKDIR /var/www/html
EXPOSE 80