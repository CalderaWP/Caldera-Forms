

##Overview
Caldera Forms, starting with 1.5.6.2 adpoted a new acceptance testing driven development approach. We record a baseline test, based on a protoype, or using last version that a bug was not present on. We then make a branch from develop, named for the github issue, for example feature/42 for issue 42, develop the fix or feature there and then once that branch passes the test, we run all the tests on that branch, if those pass, we merge to develop and then run the tests again on develop.

If you work for Caldera Labs, you have access in drive to the folder "Acceptance Testing" (SA1+) and are expected to follow those steps.

If you don't work for us, just put in a pull request against the develop branch, we appreicate it and will handle testing.

## Current Git Workflow & PHP Compatibility
The develop branch is currently being used for 1.7 and therefore PHP 5.6 is the minimum supported PHP version.

If 1.6.3+ is developed, a 1.6.x branch (branced from master) will be used. That branch must support PHP 5.2.

Work on the [GDPR compliance](https://github.com/CalderaWP/Caldera-Forms/projects/3) is happening in a branch called "focus3" and is tagged for 1.7, so it can be PHP 5.6+.

## TL;DR
* Open Github issue
* Make a test (don't worry about this if you're a community contributor.)
* Make a branch from develop, call it `feature/<issue-number>` where `<issue-number>` is the issue number.
* Fix bug.
* Submit pull request for your branch against develop branch.
* Use tests to prove it is resolved
	* If you are a community contributor, don't worry about this.
	* If you are employed by Caldera Labs, follow testing procedures documented in Google Drive folder "Acceptance Testing" or discuss alternative testing methodology with Josh.
	

## Release Flow
Starting from completing dev.

Please also review the Google Doc in our drive "How To Release Caldera Forms To WordPress.org" if you have access - SA2+.

* Run tests on develop
* Change version number in package.json to `<version>-rc-<n>` where `<version>` is the version we're making and `<n>` is 1, unless we already have an `rc1`.
* Use `grunt version_number` (make sure to do `npm install` first) to set version number.
	* This command overwrites version number in the 4 or so other places its set.
* Merge develop to master.
* Run tests on master.
* If tests fail on master, revert merge to master.
* If tests pass, in master, change version number in package.json to the final version number (no rc or beta or whatever)
* Use `grunt version_number` to update version number in other places.
* Use `grunt build` to build tag for WordPress.org and then follow steps in "How To Release Caldera Forms To WordPress.org" to get it to WordPress/ release post/ etc.
* Tag release
	* If you skip this CDN will not work with new release, which is bad.
* Merge master to develop.
* Use grunt to change version number to <next-version>-b-1.

* Merge 
