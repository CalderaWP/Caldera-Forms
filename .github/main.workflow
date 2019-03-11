workflow "Build and test" {
  on = "push"
}

action "GitHub Action for npm" {
  uses = "actions/npm"
  args = "install"
}
