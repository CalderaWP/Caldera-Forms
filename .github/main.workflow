workflow "New workflow" {
  on = "push"
  resolves = ["docker://awhalen/docker-php-composer-node:latest"]
}

action "docker://awhalen/docker-php-composer-node:latest" {
  uses = "docker://awhalen/docker-php-composer-node:latest"
  args = "composer install && npm install && npm run package"
  secrets = ["GITHUB_TOKEN"]
}
