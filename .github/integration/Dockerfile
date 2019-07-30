#Based on: https://github.com/blackrobot/docker-compose-action/blob/master/Dockerfile

ARG DOCKER_VERSION=latest
FROM docker:${DOCKER_VERSION}

ARG COMPOSE_VERSION=
ARG DOCKER_VERSION

RUN apk add --no-cache py-pip
RUN pip install --no-cache-dir "docker-compose${COMPOSE_VERSION:+==}${COMPOSE_VERSION}"

RUN ./bin/install-wp-tests.sh

COPY readme.md entrypoint.sh 


ENTRYPOINT ["/entrypoint.sh"]