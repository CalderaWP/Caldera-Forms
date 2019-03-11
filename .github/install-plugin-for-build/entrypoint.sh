#!/usr/bin/env bash
pwd
echo "${GITHUB_WORKSPACE}"
composer install
npm install
npm run build
