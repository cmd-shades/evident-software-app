#!/usr/bin/env bash

cwd=$(pwd)/;

# shellcheck disable=SC2034
config=${cwd}openssl.conf;

# shellcheck disable=SC2046
openssl req -config ${config} \
-new -sha256 -newkey rsa:2048 -nodes -keyout private.key \
-x509 -days 825 -out cert.crt
