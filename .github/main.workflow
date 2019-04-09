workflow "PHP Unit Tests" {
  on = "push"
  resolves = ["TestUnit"]
}

action "Upate" {
  uses = "MilesChou/composer-action@master"
  args = "update"
}

action "TestUnit" {
  uses = "MilesChou/composer-action@master"
  needs = "Upate"
  args = "test:unit"
}
