This page will be useful to you if you've been assigned a task to review a bug that has been reported in Caldera Forms. Bugs that get reported by users or the support team may not have enough information to decide if it is a real bug or not.

Your goal when assigned this task is to either:

*   Confirm this issue is a bug and provide additional details to developers HOW to reproduce the bug.
*   Demonstrate HOW you tested the bug and were not able to reproduce the issue. 

Our goal is to find what the user expected to happen, what happened instead and if it was reasonable for them to expect what they expected.

The screenshots you see in this post are [from this issue](https://github.com/CalderaWP/Caldera-Forms/issues/2788).

## Is The Bug Ready To Test?

The issue that you are assigned may not have all of the information you need to test. Scan over the issue first and make sure you can answer these questions:

*   Do you know the PHP, WordPress and Caldera Forms versions the person reporting the issue has?
*   Do you have enough informatiopn 

If the answer is no:

*   Leave notes _**on the ticket**_ about what information you need.
*   DM the support person or developer (Josh if you do not know who that is) that can get more information with a link to the comment asking them to look at it. 

## KEEP CONVERSATION IN THE TICKET

When you have Slack DMs about an issue, that is not searchable, and is not accessible to those who are not in the DM.

## Have Some Incomplete Thoughts? 
Leave A NOTE IN THE TICKET

It may seem like beacuse this is a formal process you are reading here, that you should wait until you have all of your research completed and everything is perfect before you should leave a comment. FALSE.

It could take hours, or days to complete the task. You might get distracted. if you have relevant information about a ticket, even if it’s not confirmed, leave a comment before you forget it. For example, `I am not done testing, I see an error in my console (unexpected token line 27) when I click the button, but I'm not sure if that is a conflict with WooCommerce or that actual error.` This could help someone else find the issue, and they would not know this unless you left the comment. Or you could be wrong, that’s fine, you’ll help others. Keeping the information in your head doesn’t help.

## Setup Test Environment
![](http://handbook.calderawp.com/wp-content/uploads/2018/10/Screen-Shot-2018-10-24-at-10.42.27-AM.png)

In the Github issue, the person who reported the issue who reported the issue should have included the PHP, WordPress and Caldera Forms version that they are using. Make sure your WordPress site you are testing with has the same versions.

PHP version does not need to be the same. If the user is using PHP 5.6, test with PHP 5.6. If they are using PHP 5.5 or below, that is unsupported. If they are using any version of PHP7, you can probably test with PHP 7.1 and be fine.

### Options For Creating A Test Environment

*   [poopy.life][(https://poopy.life) Creates disposable test WordPress sites.
*   Pantheon Sandbox sites are free.
*   DesktopServer
*   [https://joshpress.net/create-a-wordpres-site-with-lando/](https://joshpress.net/create-a-wordpres-site-with-lando/)
*   Caldera Forms 1.8+ has a built in Docker-based development environment


### Include Your Debug Information

Whenever you leave a comment on the ticket, include the short debug information from the Caldera Forms support submenu, under the “Debug” tab.

![](http://handbook.calderawp.com/wp-content/uploads/2018/10/Screen-Shot-2018-10-24-at-11.23.23-AM.png)

![](http://handbook.calderawp.com/wp-content/uploads/2018/10/Screen-Shot-2018-10-24-at-11.22.25-AM-1.png)

### Have Error Logging On When You Test The Issue

*   https://calderaforms.com/2016/05/wordpress-debug-logging/
*   https://webmasters.stackexchange.com/a/77337

## Reproduce The Bug

TL;DR – Do the thing that the user says doesn not work. Either report that it doesn’t work and what PHP and JavaScript errors happened or why you think it's not a bug.

In the Github issue, there should be steps to reproduce the bug. Follow each one, step by step. If you do not understand a step, leave a comment in the ticket.

After every UI interaction — page load, click, change of a setting — make sure you have no new errors in your JavaScript console. If you find a JavaScript error, report it in the ticket. Make sure to note what you did that triggered the error.

After every page load, every time you save a form, or save settings, or submit a form, check for PHP errors in your PHP error log. If you find an error, mention it in the ticket and explain what you did to trigger the bug.

![](http://handbook.calderawp.com/wp-content/uploads/2018/10/Screen-Shot-2018-10-24-at-11.32.06-AM.png)

* https://github.com/CalderaWP/Caldera-Forms/issues/2788#issuecomment-432707743</figcaption>


### Can't Reproduce The Bug?

Sometimes you’re not going to be able to reproduce the bug. When this happens you need to explain in detail what you did. Your goal isn’t to prove the user wrong. Something is probably wrong with their site. Your goal, by explaining how you tested the bug is to invite one of two things from the user:

*   How to better communicate how the issue can be reproduced.
    *   We need to learn more.
    * By listing every step you took, it invites the user to show you why you are testing differently.
*   Teach the user the correct way to use Caldera Forms
    *   Sometimes it's not a bug, someone is using the plugin wrong.
    *   Sometimes it's not a bug, we are the wrong plugin for the job.
