#https://github.com/blackrobot/docker-compose-action/blob/master/Dockerfile
ARG DOCKER_VERSION=latest
FROM docker:${DOCKER_VERSION}

ARG COMPOSE_VERSION=
ARG DOCKER_VERSION

RUN apk add --no-cache py-pip
RUN pip install --no-cache-dir "docker-compose${COMPOSE_VERSION:+==}${COMPOSE_VERSION}"

COPY readme.md entrypoint.sh /

LABEL com.github.actions.name="GitHub Action for docker-compose" \
    com.github.actions.description="Wraps the docker-compose cli." \
    com.github.actions.icon="package" \
    com.github.actions.color="blue"

ENTRYPOINT ["/entrypoint.sh"]