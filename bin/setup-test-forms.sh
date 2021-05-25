#!/usr/bin/env bash
docker-compose run --rm cli wp cf import-test-forms
docker-compose run --rm cli wp cf create-test-pages