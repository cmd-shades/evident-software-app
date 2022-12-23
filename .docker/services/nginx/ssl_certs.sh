#!/usr/bin/env bash

mkdir /etc/ssl/private/

openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
    -subj '/C=GB/ST=London/L=London/CN=localhost' \
    -keyout /etc/ssl/private/floating-envx.key \
    -out /etc/ssl/certs/floating-envx.crt;

    # Creates a strong Diffie-Hellman group, which is used in negotiating Perfect Forward Secrecy with clients.
#openssl dhparam -out /etc/nginx/dhparam.pem 4096
