# Pull Request Workflow


For Caldera Forms, we stick to a strict git flow, workflow.

Please see: https://github.com/CalderaWP/Caldera-Forms/blob/master/CONTRIBUTING.md

This page has detailed instructions for creating a pull request to fix a bug or add a new feature in Caldera Forms. CalderaWP employees and contractors are required to follow these steps. We encourage community contributions and will work with you.

## Creating A Pull Request

Every single change to Caldera Forms involves a pull request. Every pull request must be associated with an issue.

https://help.github.com/articles/proposing-changes-to-your-work-with-pull-requests/

To fix a bug, or add a feature, we submit a pull request against the develop branch. We do one pull request to merge the develop branch to the master branch per release.

Pull requests can only be merged to master or develop branch by Josh, Christie or Nico. 

### Step by Step
This is the general step by step process for fixing a bug or adding a new feature.

1. Identify issue number for your bug fix or feature.
    - You may need to open the issiue first. See "Opening An Issue" below
1. In your local environment, fetch the latest develop branch and then check it out
    - `git fetch origin develop`
    - `git checkout develop`
1. In your local environment, create a new branch
    - See "Branch Naming" below for what to name the branch,
    - `git checkout -B <branch-name>`
1. Begin development. Make one commit per change.
    - Unless we agree otherwise, all development should be test-fist.
      - See [testing documentation for more information](./contributing/testing.md)
    - See "Commit Messages" below
1. Push commits as you go to the branch.
    - Pushing changes early helps get feedback early
1. Create a pull request after the first commit is pushed to the branch.
    - See "Creating Pull Request" below
1. When your have completed the issue described in the issue and all tests are passing, request a code review.
    - See "Code Review" below

### More Detailed Information


#### Opening An Issue
Before we make any changes we open an issue to explain WHY we need to make the change and collect relevant information. 

There is an issue template, that you can follow. Here are a few extra notes:

Github issues should have a title that fits on one line in the UI. Describe the bug, describe the feature in one sentance. Don't feel the need to put everything in the title, there is a description.

In the description, fill out all requested items from the issue template.

If you are requesting a feature, describe WHY the feature is needed and how a user would use it. If you are describing a bug, explain steps to reproduce the bug, in an ordered list. Attatching links to forms on a WordPress site or form exports is super helpful.

#### Creating Pull Request

Name the pull request with a short title that describes the change.

In the issue description, you MUST include:

*   A reference to the issue(s) PR resolves.
  - Include the issue number in the description, not the title.
  - This links the issue and the PR, making it easier to find things.
*   A description of what has changed.
  - Do not write "fixed issue #218" say "added sanitization to input. fixes #218"
*   How to test.
  - Narrate the steps a user would take to use the feature being added/fixed
  - Attatch any forms that you used to test please!
  
#### Github Issue/ PR Tags
Tags should only be applied by Josh or Nico.

Once an issue has a pull request, add the  Has PR" tag to the issue PR is for. 

PR should have the "PR (patch)" tag.

#### Branch Naming

The branch you work on is named feature/<issue-id>. If you are working on issue 4242, then your branch MUST be called feature/4242.

#### Commit Messages

Each commit should change one thing. If your commit message uses "and" you're probably doing more than one thing in a commit.

Every single commit MUST have a descriptive title and a reference to the issue number in the commit message. If you are working on issue 4242 and the issue is to fix a missing dependency for conditionals then your commit message will be something like: 

*   ACCEPTABLE: “added missing dependency for conditional logic #4242”.
*   NOT ACCEPTABLE: “added missing dependency for conditional logic and fixed a typo in conditional editor #4242” <- Commit is for two things
*   NOT ACCEPTABLE: “added missing dependency for conditional logic 4242”. <- Commit message is missing # so Github will not associate it with the issue number.
*   NOT ACCEPTABLE: “fixed conditional logic #4242”. <- No explanation of what changed.  

#### Code Review

All pull requests must pass a code review by Nico or Josh. Once your PR is ready for review, request a review from Nico, unless you are Nico, then request it from Josh.

https://help.github.com/articles/requesting-a-pull-request-review/

