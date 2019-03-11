workflow "Install and Build" {
  on = "push"
  resolves = ["Install"]
}

action "Install" {
  uses = "./install-plugin-for-build"
}
