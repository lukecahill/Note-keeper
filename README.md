# Note Keeper
Note keeping system which allows for the creating, updating, archiving and deleting of notes.

Each note may have several tags attached to it for reference. Clicking this tag will show similar notes which also have this tag. 

Search system which currently supports searching for the user defined options, which can be note title, text, or a combination of both of these. 

Supports the ability for users to create an account which is only associated with the notes that they create. 

User can change password and customize the color of the tags in the user options page. Now includes the option for the user to chose in what order the notes should be displayed. This can currently be oldest first, newest first, A-Z, Z-A, last edited and oldest edited. User can also customise the layout by choosing to use the provided dark or light theme.

Includes email confirmation on user registration. Users IP address is now logged when they log-in, and will keep the most recent 5. Also allows for account lock-out, where the user will be locked out of their account after 5 wrong password attempts. A password reset form can then be used to email the user a link where they can then reset their password. 

Makes use of the browsers Geolocation API to find the users coordinates and displays a map with these coordinates, then saves these to the database for future use.

BootStrap is being used for styling, with SCSS now being used to generate the custom CSS. Gulp.js is being used to minify the JS, and also for JSLint.

The SQL used for this is provided in 'database.sql' (this is for MySQL, but may work with other database systems - though this is not yet tested).

This requires version 5.5 or greater of PHP. 