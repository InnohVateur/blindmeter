# BlindMeter | Version 0.1  

## Installation  
1. Download and install [wamp](https://www.wampserver.com/).  
2. Download the source code from the main branch. The main branch is the content of the website himself, while the misc branch contains other resources that may be useful.<br/><br/>
   **Note on the songs<encoding>.sql files :**<br/><br/>
   If you're using the latest version of wampserver, the utf8-mb3 version should work fine.  However, you may need to use the utf8 version if you're using an oldest version.  
3. Start wampmanager. Then, go to [localhost/phpmyadmin](localhost/phpmyadmin) and sign in (The username should be root, and the password should be either root or nothing). Then go to Databases and create a new database named "blindtest". Then click on the blindtest link, on the sidebar. If you want to import the table I use directly, go to import and select the file. If you want to create the table yourself, go to SQL and enter this command :
   ```sql
   CREATE TABLE songs
   (deez_id BIGINT,
   title TEXT NOT NULL,
   preview TEXT NOT NULL,
   picture TEXT NOT NULL,
   artists TEXT NOT NULL,
   PRIMARY KEY(deez_id));
   ```
4. Download the .env file from the misc branch. Then, add it, with the source code you downloaded at step 2, to the www file of wamp (located in the wamp directory, usually located in C:).

## How to use
Go to [localhost](localhost) after you ran wampmanager. then click on blindtest and type the password specified in the .env file (the default one is "root"), and enjoy !<br/>
If you want to add musics, click on the + button, and search for the music you would like to add. Once you find it, click on "Add to Database".<br/>
**NOTE : You cannot add the exact same song several times, but if the song exists several times on deezer, with a different id, then duplicates could appear, as the primary key is the id.**
