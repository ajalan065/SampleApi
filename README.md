# SampleApi

This code aims to create an API which would help the twitter users to post their favorite quotes as images on their twitter wall without going to the website.The API returns HTML. On successful operation it gives a message "Updated", or throws an error under Twitter Authentication problem/any other issue.

Requirements
============
The users need to get their consumer key, consumer secret, oauth access token and oauth access token secret from Twitter in order to use this API.

How to get these?
=================
1. Go to https://apps.twitter.com/app
2. Create a new App by providing all the information asked in the form and create the app. Once done, you will be redirected to the Application Management page.
3. Go to 'Keys and Access Tokens' tab and get the Consumer Key and Consumer Secret. Scroll down and click on 'Create my Access Token'. This will generate the token key and secret.
4. Once done, we are ready to use the API.

How to make use of API?
=======================
Let us have an example for this:
I have created a custom form to demonstrate just the use of API Endpoint created to tweet images on your account.

The format of the API call is:
http://localhost/<DIR_NAME>/Api.php?quote=YOUR+QUOTE.&oauth_access_token=YOUR_ACCESS_TOKEN&oauth_access_token_secret=YOUR_TOKEN_SECRET&consumer_key=CONSUMER_KEY&consumer_secret=CONSUMER_SECRET

By default, the DIR_NAME is SampleApi-master. You may change the directory name as per your choice.
ss
So make sure that the form field names are in accordance to avoid setting it later on while API calling.

Download the entire repository, rename it to create_api and run it on your localhost.

Now, when the form is submitted, the  quote in the form of the image gets published on the twitter wall.
