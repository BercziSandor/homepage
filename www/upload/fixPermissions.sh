#!/bin/bash

ls -la
chown -R nobody:nobody ./
[ -d uploads ] || mkdir uploads
chown -R nobody:nobody uploads
ls -la
