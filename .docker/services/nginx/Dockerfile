ARG NGINX_VERSION
ARG APP_CODE_PATH

FROM nginx:${NGINX_VERSION} as base

FROM base as local


RUN apk update \
    && apk upgrade \
    && apk --update add logrotate \
    && apk add --no-cache openssl \
    && apk add --no-cache bash \
    && apk add --no-cache curl

## Add new nginx config
COPY --chown=nginx:nginx conf/nginx.conf /etc/nginx/
COPY --chown=nginx:nginx conf/default.conf /etc/nginx/conf.d/

#
RUN sed -i "s#root __NGINX_ROOT;#root $APP_CODE_PATH/public;#" /etc/nginx/conf.d/default.conf

# SSL
COPY certs/* /tmp/certs/

# Add upstream servers
#COPY conf/extra/upstream.conf /etc/nginx/extra/

# Add vhost(s) to available
#

# Add docker volume to sites available
#

# Symlink available files to enabled
#

# Symlink available to enabled. available sites share volume with docker host
RUN ln -s /etc/nginx/sites-enabled/ /etc/nginx/sites-available

# Symlink errors
RUN ln -sf /dev/stdout /var/log/nginx/access.log && ln -sf /dev/stderr /var/log/nginx/error.log

# Create SSL Certifcates
COPY ssl_certs.sh  /docker-entrypoint.d/ssl_certs.sh
RUN chmod +x /docker-entrypoint.d/ssl_certs.sh

WORKDIR /etc/nginx
