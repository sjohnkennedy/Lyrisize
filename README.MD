# Lyrisize

Lyrisize is a USSD based application which leverages Ideamart APIs. It uses [Musixmatch](https://developer.musixmatch.com) API to search and retrieve song lyrics.

## Setup

This document assumes the you have already downloded and install the Ideamart API simulator and you have worked with it before. IF you like to find out more on how to use the Ideamart Simulator and the APIs please follow this [link](http://ideamart.lk).

To get the app running on your system:

1. Clone the repository
2. Create a database on your MySQL DB and import the SQL in the file system.
4. Update credentials on the __dbinteract.php__ file.
5. Create a copy of the __config-sample.php__ file and rename it to __config.php__
6. Add the URLs to the simulators USSD tab.
7. Start testing.

## TODO

- [ ] Clean up and comment code.

Note : As this app uses a testng API Key from Musixmatch, only part of the lyrics is returned. 