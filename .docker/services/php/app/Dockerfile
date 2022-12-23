ARG BASE_IMAGE
FROM ${BASE_IMAGE} as base

RUN apk add --no-cache --update \
        openssh \
    # Install PHP extentions  \
    # @see https://dev.to/vikbert/my-checklist-of-php-extensions-used-in-alpine-docker-image-1g3j
        php8-openssl

ARG APP_SSH_PASSWORD
RUN echo "$APP_USER_NAME:$APP_SSH_PASSWORD" | chpasswd 2>&1

# Required to start sshd, otherwise the container will error out on startup with the message
# "sshd: no hostkeys available -- exiting."
# @see https://stackoverflow.com/a/65348102/413531
RUN ssh-keygen -A

# we use SSH deployment configuration in PhpStorm for local development
EXPOSE 22

CMD ["/usr/sbin/sshd", "-D"]

FROM base as local
