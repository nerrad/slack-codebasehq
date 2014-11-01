These are various commands that you can use and thier format to trigger various codebase interactions.  To use a command, start a new chat with the command and a space before your message.  Note that some commands have different params you can set using a special format that will be explained with the command:

`testaction`: Just used to verify the bot is working.
`cbtkthelp` : What you used to get this :smile:
`cbposttkt` : Use this to create a new tkt.  You can use the following format to set ticket parameters:

> cbposttkt [project:project-slug] [assigned:nerrad] [type:bug] [priority:low] [status:please-fix] [summary:This is a summary of the ticket] The content of your message here will be the initial description for the ticket.

After posting the ticket the bot will return a link to the new ticket and the id if you wish to update it.

`cbupdatetkt` : When used with the tkt param, this will update the given ticket with a note by you containing the text of your message in chat.  Example:

> cbupdatetkt [tkt:1025] [type:bug] [priority:high] [status:completed] [assigned:nerrad] Just noticed something else going wrong here.

After posting the ticket the bot will return a message to indicate if the tkt was updated or not.

`cbgettkt` : When used with the tkt param, this will retrieve the given ticket details and display in chat along with the last comment on the ticket.   When used without the tkt param, this will retrieve the summaries and numbers of the last 5 tickets created.

> cbgettkt [tkt: 1025]
