workflow "Install and zip" {
  on = "push"
  resolves = ["Composer install"]
}

action "GitHub Action for npm" {
  uses = "actions/npm@59b64a598378f31e49cb76f27d6f3312b582f680"
  args = "install"
}

action "Composer install" {
  uses = "docker://composer:latest"
  needs = ["GitHub Action for npm"]
  args = "composer install"
}
