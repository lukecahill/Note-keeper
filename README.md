# Note Keeper
Note keeping system which allows for the creating, updating, archiving and deleting of notes.

Each note may have several tags attached to it for reference. Clicking this tag will show similar notes which also have this tag. 

Search system which currently supports searching for the user defined options, which can be note title, text, or a combination of both of these. 

Supports the ability for users to create an account which is only associated with the notes that they create. 

User can change password and customize the color of the tags in the user options page. Now includes the option for the user to chose in what order the notes should be displayed. This can currently be oldest first, newest first, A-Z, Z-A, lsat edited and oldest edited.

Includes email confirmation on user registration. Users IP address is now logged when they log-in, and will keep the most recent 5.

Has now been updated to return the JSON, rather than the full HTML, for each note which is then built client-side. This will allow the potential use of AngularJS in the future.

The SQL used for this is provided in 'database.sql'.
