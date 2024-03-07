#!/bin/bash

cd www/upload
ls -la
[ -d uploads ] || mkdir uploads
chown -R nobody:nobody ./
ls -la
cd -
