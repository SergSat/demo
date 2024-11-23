FROM nginx:1.21

COPY ./docker/vhost.conf /etc/nginx/conf.d/default.conf
COPY ./docker/php.ini /usr/local/etc/php/php.ini

RUN ln -sf /dev/stdout /var/log/nginx/access.log \
	&& ln -sf /dev/stderr /var/log/nginx/error.log