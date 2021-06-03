## Setup ##
This application is not deployed. Make sure you have a local server running on port 80 and MySQL running on port 3006. You'll also need to run 'npm -start' from the 'React-frontend' React project and then check the results on localhost:3000. A database example already populated has been provided as well, which needs to be imported. 

## Backend ##
The backend gets the information from APIs, save it in the database 'server_info' in the table 'server_status'. For each server it will save the information, so 5 rows will be created every minute (one for each server). I've created an SQL event in order to repeat the operation every minute. 
For this exercise I've used mysqli queries, although using PDO is best in professional environments to avoid database injection. 
Issues encountered: 
- Not all APIs are working, at least not for me: the first one has always worked, the others are unreacheable (the connection timed out both from the browser and when connecting with CURL). There may be an issue with the https protocol. I've tried to pass paramethers in order to bypass the certificate permissions, but it didn't work properly. This needs to be investigated further. For the purpose of the exercise I've created a JSON with a list of the URLs and then another JSON using only the urls that work, which is one repeated 6 times. I'll be using this one, so the graphs displaying the data will be showing the same data repeated 6 times. 
- At first I wanted to create a cron job, but I was doing this exercise on a Windows machine and the setup is quite different than Linux. I couldn't make it work properly, so I've used an infinite while loop with a sleep function. 

## Frontend ##
The frontend consume the API endpoint created in the backend. The GET request is placed through Axios. The charts are created wih React-charts-js-2 and are refreshed every minute using an event. 
Issues encountered:
- When trying to get data from the API created in the backend, which sits on https://localhost/php_test, I had a cross origin error. After investigating it, I decided to install the chrome extension Moesif Origin & CORS Changer, which allows to send cross-domain requests. 