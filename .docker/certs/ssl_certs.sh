#!/usr/bin/env bash

openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
    -subj '/C=GB/ST=London/L=London/CN=localhost' \
    -keyout /etc/ssl/private/localhost.key \
    -out /etc/ssl/certs/localhost.crt;

openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
    -subj '/C=GB/ST=London/L=London/CN=68.183.255.38' \
    -keyout /etc/ssl/private/app-floating-localhost.key \
    -out /etc/ssl/certs/app-floating-localhost.crt;

    # Creates a strong Diffie-Hellman group, which is used in negotiating Perfect Forward Secrecy with clients.
#openssl dhparam -out /etc/nginx/dhparam.pem 4096
