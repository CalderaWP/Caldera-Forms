#!/usr/bin/env bash
docker-compose run --rm cli wp plugin activate caldera-forms
exit 0;
# Install test form importer/Ghost Inspector runner
docker-compose run --rm cli wp plugin activate ghost-runner/plugin
cd wp-content/plugins/ghost-runner
if [ ! -d wp-content/plugins/ghost-runner/vendor ]
then
    composer install
fi

if [  -d wp-content/plugins/ghost-runner/vendor ]
then
    composer update --no-dev
fi