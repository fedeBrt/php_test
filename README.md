# php_test

## Setup ##
This application is not deployed. Make sure you have a local server running on port 80 and MySQL running on port 3006. You'll also need to run 'npm -start' from the 'React-frontend' React project and then check the results on localhost:3000. A .sql file example already populated has been provided as well, which needs to be imported in the local database. 

## Backend ##
The backend gets the information from APIs, save it in the database 'server_info' in the table 'server_status'. For each server it will save the information, so 6 rows will be created every minute (one for each server). I've used an infinite while loop with a sleep function. 
For this exercise I've used mysqli queries, although using PDO is best in professional environments to avoid database injection. 
Also since the project is relatively small, it was faster to write the code in a procedural way, although it would be best to use classes. 
Issues encountered: 
- Not all APIs provided are working, at least not for me: one has always worked (https://glai-tls1.transperfect.com/aiportal/v1.1/stats), the others are unreacheable (the connection timed out both from the browser and when connecting with CURL). There may be an issue with the https protocol. I've tried to pass paramethers in order to bypass the certificate permissions, but it didn't work properly. This would need to be investigated further. For the purpose of the exercise I've created a JSON with a list of the URLs given and then another JSON using only the urls that work, which is one repeated 6 times. I'll be using this one with the purpose of simulating getting data from 6 APIs. The data from all 6 will be collected and saved in the database (6 rows for every minute) but since 5 have no data, there will be only one graph displaying the data for one API. 
- The infinite loop doesn't seem to work properly together with the frontend and unfortunately I didn't have the time to fix it. 

## Frontend ##
The frontend consume the API endpoint created in the backend. The GET request is placed through Axios. The charts are created wih React-charts-js-2 and are refreshed every minute using an event. 
Issues encountered:
- When trying to get data from the API created in the backend, which sits on https://localhost/php_test, I had a cross origin error. After investigating it, I decided to install the chrome extension Moesif Origin & CORS Changer, which allows to send cross-domain requests. 
- Beware that the graph for the jobs in progress and queued jobs doesn't display any data because both values coming from the API are 0.
