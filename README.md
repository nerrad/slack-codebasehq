##Slack to Codebase Webhook

This is a php application you can put on your server to serve as the endpoint for a [Slack](http://slack.com) ougoing webhook.  It's purpose is to enable you to use trigger words in your chat to communicate with your [CodebaseHQ](http://codebashq.com) account and trigger the folowing events:

### Get a message about of all the commands that can be used

```
cbtkthelp
```

### Get the last 5 tickets posted in a given project

```
cbgettkt [project:my-project]
```

### Get a specific ticket using the ticket number

```
cbgettkt [tkt: 1234]
```

### Create a new ticket in codebase

```
cbposttkt [project:my-project] [assigned:nerrad] [type:bug] [priority:low] [status:new] [summary: This is an optional summary, if not included, a condensed version of chat message will be used]  Your chat message becomes the initial ticket description.
```

### Update an existing ticket in codebase  (not implemented yet)

```
cbupdatetkt [tkt: 1234] [type:bug] [priority:high] [status:completed] [assigned:nerrad] This chat message gets appended as a note to the ticket.
```

## How does it work?
More credits will go in this space.  But look at the composer.json and you'll see who laid some groundwork to make this possible.

## Installation and setup

More notes will eventually go here.  However here's some basic components

1. Clone this to the server you will use as the webhook endpoint and make sure you've got the ip address listening for http requests.  
2. Use `composer install` to load in all the dependencies.
3. Copy `app-config-sample.php` to `app-config.php` and add in all the necessary credentials there (follow inline docs).
4. Setup the outgoing webhook in your slack account (where you'll get the token to add to `app-config.php`.  You'll also need to add the trigger words in that webhook.


## Want more trigger words?

It's fairly easy to add them, while I'm likely going to abstract things a bit further as time goes on, for now all the trigger word logic/handling is done in `/include/React.php`  You just add a method in there that matches your trigger word and then the resulting logic for what happens with that trigger word.  **Pull requests welcome!!**
