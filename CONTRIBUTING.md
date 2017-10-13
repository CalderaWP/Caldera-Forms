Caldera Forms, starting with 1.5.6.2 adpoted a new acceptance testing driven development approach. We record a baseline test, based on a protoype, or using last version that a bug was not present on. We then make a branch from develop, named for the github issue, for example feature/42 for issue 42, develop the fix or feature there and then once that branch passes the test, we run all the tests on that branch, if those pass, we merge to develop and then run the tests again on develop.

If you work for Caldera Labs, you have access in drive to the folder "Acceptance Testing" and are expected to follow those steps.

If you don't work for us, just put in a pull request against the develop branch, we appreicate it and will handle testing.

# TL;DR
* Open Github issue
* Make a test (don't worry about this if you're a community contributor.)
* Make a branch from develop, call it `feature/<issue-number>` where `<issue-number>` is the issue number.
* Fix bug.
* Submit pull request for your branch against develop branch.
* Use tests to prove it is resolved
	* If you are a community contributor, don't worry about this.
	* If you are employed by Caldera Labs, follow testing procedures documented in Google Drive folder "Acceptance Testing" or discuss alternative testing methodology with Josh.
	
