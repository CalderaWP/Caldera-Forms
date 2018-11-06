Contributing to Caldera Forms!


## PHP Version
Caldera Forms supports PHP 5.6 or later.

## Git Workflow
* All commits should have a commit message that includes an issue.
* All commits should be made to a branch that is branched off of develop.
** Name branch `feature/<issue-number>` where `<issue-number>` is the issue number.
* We sometimes use a projec branch for large [projects](https://github.com/CalderaWP/Caldera-Forms/projects)
* If a pull request is submitted against master branch, we will attempt to merge to develop.
	* Please __do not submit pull requests against the master branch!__

## Step by Step
### Submitting A Pull Request
* Open Github issue explaining the reason for the change, if one does not exist.
* Fork Caldera Forms if you do not have permission to create a branch in Caldera Forms.
* Make a branch from develop, call it `feature/<issue-number>` where `<issue-number>` is the issue number.
* Write tests to prove new feature works or bug is resolved (not required if you don't work here.)
* Commit the failing tests - including the commit number.
* Push the branch to Github.
* Open a PR to merge your branch to develop.
	- In the title, briefly summarize the change. Add a [WIP] prefix to title.
	- In the PR description, mention the issue number and describe what has changed.
* Fix bug
	- Make incrimental commits.
	- Include issue number in  each commit message
* In the pull request, remove [WIP] from PR title.
* Request a review from Josh (@Shelob9) or (@New0) of the PR.

### PR Testing And Review
* All PRs should be approved by Josh or Nico, which ever one didn't write the patch.
	- Christie can also merge to develop or master or grant others access.
* PRs should only be merged once review is approved and tests are passing.

## Release Flow
[See internal documentation](http://handbook.calderawp.com/how-to-release-caldera-forms-to-wordpress-org/)
