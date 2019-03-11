# Should find a smaller image
FROM awhalen/docker-php-composer-node:latest

# Labels for GitHub to read your action
LABEL "com.github.actions.name"="Install plugins for build"
LABEL "com.github.actions.description"="A description of your action"
# Here all of the available icons: https://feathericons.com/
LABEL "com.github.actions.icon"="play"
# And all of the available colors: https://developer.github.com/actions/creating-github-actions/creating-a-docker-container/#label
LABEL "com.github.actions.color"="gray-dark"

ADD entrypoint.sh /entrypoint.sh
ENTRYPOINT ["/entrypoint.sh"]
