-------------------------------------------
Logaholic Installation Instructions
-------------------------------------------

If you are upgrading from an earlier version 
of Logaholic, please follow instructions in
UpgradeInstructions.txt. If not, please continue 
below.

-------------------------------------------
Step 1: COPY FILES
-------------------------------------------

Copy the contents of the zip file to a
new subdirectory in your web root.

I.e. If you have a website:
www.example.com

copy the files to 
www.example.com/logaholic/

-------------------------------------------
Step 2: SET FILE PERMISSIONS
-------------------------------------------

Change the file permissions so your webserver can
write to the subdirectories.

If you are running Windows, you can probably skip 
this step, otherwise, please read on.

You need to allow write access to a surdirectory 
in your logaholic folder. The directory is 
called "files"

Most FTP clients will also enable you to do this. 
Just select the 'Properties' for the 'files' folder and try 
to change the rights to 'Write' for everyone.

-------------------------------------------
Step 3: RUN SETUP WIZARD
-------------------------------------------

Point you browser to:
http://www.example.com/logaholic/install.php
and follow the instructions.

-------------------------------------------
That's it
------------------------------------------- 

After you've installed, please click the
"Global Settings" tab for more post-installation
tasks like adding a password





-------------------------------------------
A NOTE ABOUT LOG FILES
-------------------------------------------
You will only need to worry about this if the information in your logaholic reports seem incomplete.

Logaholic supports both NCSA Common log files (default for Apache) and
W3C Extended log files (default for IIS).

However, log file formats can be custom defined in both Apache and IIS.
For both, make sure you include the referrer information in the log file.

For IIS users, using the IIS manager, select the extended log file 
options and add a check to the box next to referrer and user agent.

For apache users, it comes down to defining the log file like such in
httpd.conf file:

CustomLog /your_path/access_log "%h %l %u %t \"%r\" %s %b \"%{Referer}i\"
\"%{User-Agent}i\""

However, this seems to be enough in most cases:

CustomLog /your_path/access_log "combined"

Each virtual host should have it's own log file.

Most log file are configured correctly, so again you only need 
to take this into consideration if your Logaholic reports are 
missing referrer information.

 
-------------------------------------------
Adding my own Logo to the software
------------------------------------------- 
You can add your own logo to the software by editing the mylogo-example.php file
and rename it to mylogo.php.


-------------------------------------------
Update all logaholic profiles (updateall.php RENEWED!)
-------------------------------------------
The updateall.php command line script allows you to automatically update all logaholic profiles at once from a single command.
The update script is located in the following directory:
	logaholic/components/MaintenanceScripts/updateall-example.php 

To use this you must rename the updateall-example.php file to updateall.php.
for more information go to our manual:
	http://www.logaholic.com/manual/LogaholicManual/UpdatingAllProfiles
	
If you are using a older version of updateall.php you must insert your settings 
and rename it to use this latest version of the script.