#!/usr/bin/env bash
. "$(dirname "$0")/includes.sh"
HOST_PORT=$(docker-compose port wordpress 80 | awk -F : '{printf $2}')
CURRENT_URL=$(docker-compose run -T --rm cli option get siteurl)

echo -e $(status_message "Server is running at:")
echo -e $(status_message "http://localhost:$HOST_PORT")
