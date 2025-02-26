# User Login Module
This module allows you to incorporate **Login** in your MVC app using Custom/Google Login.

## Requirements ##
* [PHP 7.4.0 or higher](https://www.php.net/)
* [Laminas MVC](https://docs.laminas.dev/tutorials/getting-started/skeleton-application/)

## Installation ##

You can use **Composer** to start the installation.

### Composer

The preferred method is via [composer](https://getcomposer.org/). Follow the
[installation instructions](https://getcomposer.org/doc/00-intro.md) if you do not already have
composer installed.

Once composer is installed, execute the following command in your project root to install this library:

```shell script
composer require google/apiclient:^2.12.1
```

This library relies on `google/apiclient-services`. That library provides up-to-date API wrappers for a large number of Google APIs. In order that users may make use of the latest API clients, this library does not pin to a specific version of `google/apiclient-services`. **In order to prevent the accidental installation of API wrappers with breaking changes**, it is highly recommended that you pin to the [latest version](https://github.com/googleapis/google-api-php-client-services/releases) yourself prior to using this library in production.

**IMPORTANT**: Make sure the `composer.json` has the following configurations in it.

```shell script
    "minimum-stability": "dev",
    "repositories": [
        {
            "type": "vcs",
            "url": "git@bitbucket.org:iss-module/user-management.git"
        }
    ],
    "require": {   
        ...,
        ...,
        ...,     
        "iss-module/user-module": "*",
        "robmorgan/phinx": "dev-master"
    },
```    

## Run ##
You can view the module in your browser by running the php built-in web server through `composer serve`.

```shell script
$ composer run --timeout=3600 serve
```

And then browsing to the host and port you specified (in the above case, `http://localhost:82/user/login`). This will keep the server running for 3600 seconds or 1 hour.

**STEP-BY-STEP PROCEDURE**

**STEP 1:**
* Go to [Google Cloud Console](https://console.cloud.google.com) and login with your Google account. 

**STEP 2:**
* Open the dropdown menu next to `Google Could Platform/Console`.

    ![Create Project](./assets/images/Step2-Create-Project-1.png "Create Project")
    
* Click on `New Project` in the top-right corner.

    ![Create Project](./assets/images/Step2-Create-Project-2.png "Create Project")
    
* Enter `Project Name` in the text field.

    ![Create Project](./assets/images/Step2-Create-Project-3.png "Create Project")
    
* Select the newly created project.

    ![Create Project](./assets/images/Step2-Create-Project-4.png "Create Project")

**STEP 3:**

* In the top-left corner, click the menu icon. Click `APIs and services`.

    ![Select API and Services](assets/images/Step3-Select-Project-1.png "Select API and Services")
    
**STEP 4:**

* Click on `Credentials` link and then click `CONFIGURE CONSENT SCREEN`

    ![Credentials](./assets/images/Step4-Credentials-1.png "Credentials")
    
* Select User Type `External`.

    ![Credentials](./assets/images/Step4-Credentials-2.png "Credentials")
    
* Fill the relevant fields for Oauth screen and save.

    ![Credentials](./assets/images/Step4-Credentials-3.png "Credentials")
    
* Add test users. Even developer will have to add himself in order to access.

    ![Credentials](assets/images/Step4-Credentials-4.png "Credentials")
    
* Save and Continue.

    ![Credentials](assets/images/Step4-Credentials-5.png "Credentials")
    
* Select `OAuth Client ID` to create `Credentials`.

    ![Credentials](assets/images/Step4-Credentials-6.png "Credentials")
    
* Select `Web Application` as Application Type.

    ![Credentials](assets/images/Step4-Credentials-7.png "Credentials")
    
* Fill in relevant fields for creating Client ID for Web application and save.

    ![Credentials](assets/images/Step4-Credentials-8.png "Credentials")
    
* Add required scopes `email` and `profile` in `OAuth Consent Screen`.

    ![Credentials](assets/images/Step4-Credentials-8a.png "Credentials")
    
* Download Credentials JSON.

    ![Credentials](assets/images/Step4-Credentials-9.png "Credentials")
    
* ***Repeat `OAuth Client ID` creation for multiple clients through 
`Credentials` link and create another migration `oauth_clients_insert_google_client.php` 
and fill in appropriate data and run the migration (as per STEP 5 i.e. Next).***    

**STEP 5:**
    
* Google API settings from downloaded Credentials JSON need to be updated in `/db/migrations/20220621073931_oauth_clients_insert_google_client.php`.

* Copy `config/oauth2.local.php.dist` into Laminas MVC `config/autoload` directory and remove `.dist` from the end. Fill in appropriate DB configuration.

    e.g. Docker Environment
```shell script
'db' => [
            'dsn'      => "mysql:dbname=idealsales;host=host.docker.internal",
            'username' => "dev",
            'password' => "dev",
        ],
```

* Copy `config/login.local.php.dist` into Laminas MVC `config/autoload` directory and remove `.dist` from the end. Fill in appropriate configuration.
`login_success_redirect` will be the URL which will be redirected to after successful login. `client_id` will be the `client_id` and  `client_secret` will be the `client_secret` from downloaded Credentials JSON.

Below section should be populated accordingly as per Google Cloud Console configurations for Client Id. 
`<LOCALHOST-OR-CDN>` needs to be either a CDN or `localhost[:ANY-PORT]` as it should match the `redirect_uri` 
from Google Cloud Console otherwise the session data storing the Google OAuth detail will be lost due to 
change of host. 
```
**********************************************************************
* IMPORTANT!!! Google Cloud Console doesn't allow any Redirect Uri   *
* other than localhost[:PORT] or a CDN therefore any other hostname  *
* e.g. portal.local is not acceptable                                *
**********************************************************************
```

```shell script
    "login_credentials" => [
            "login_success_redirect" => "http://localhost",
            "client_id" => "",
            "client_secret" => "",
        ], 
```

* Run migration files in `vendor/iss-module/user-module/db/migrations`. For testing purpose, use any email address to login. `authenticate_uri` in `config/autoload/login.local.php` config file should be URL for authenticating API which returns below mentioned responses on _success_ or _failure_.

***Success***
```
[
    "token" => "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJlbWFpbCI6InRlc3QxN0BpZGVhbHNhbGVzc29sdXRpb25zLmNvLnVrIiwicGFnZUlkIjo4MTMsImlhdCI6MTY2NDk2NjUyNCwiZXhwIjoxNjY0OTc4NTI0fQ.ObvGGgTONEBnsevghR4XI5ds5AcJVxZdXf9-Kerdluw",
    "exp" => 19274658382,
    "hasRetailFinance" => false,
    "hasRetailFinanceZeroPercent" => false,
    "hasLoansWarehouse" => true,
    "userRole" => "GUEST",
    "oauthEmail" => "user1@gmail.com",
],
```

***Failure***
```
"status" => "ERROR",
"message" => "Invalid Credentials",
```

* The `role_uri` in `config/autoload/login.local.php` config file will return a response as below example.
```
[
    "email" => "user1@gmail.com",
    "userRole" => "GUEST"
]
```  

* Access below URL in browser.

`http://<SERVER>/user`

* ***LoginService*** has some useful methods for assistance in target application e.g. `getAuthIdentity()`, `getLoggedInUserRole()` and `isUserLoggedIn()`.

    ***Example***
    ```shell script
    $this->getLoginService()->getAuthIdentity()
    ```
**LINK WITH GOOGLE**

* Use `http://<SERVER>/user/googleLogin` URL _(`/googleLogin` route)_ to link with `Google`. This will `insert` a record in `oauth_users` table which will allow the user to login with `Google ID` next time. 

**LOGOUT**

* Access below URL in browser to logout.

`http://<SERVER>/user/logout`

**RBAC**

* Adjust RBAC permissions in `config/autoload/login.local.php` as per below pattern.

```
"rbac" => [
    "GUEST" => [
        "<module>.<controller>.<action>.<task>",
        "<module>.<controller>.<action>.<task>",
    ],
    "USER" => [
        "<module>.<controller>.<action>.<task>",
        <module>.<controller>.<action>.*,
        "+GUEST"
    ],
    "ADMIN" => [
        <module>.<controller>.<action>.<task>,
        <module>.<controller>.*,
        <module>.*,
        "+USER"
    ],
]
```

In the above example terms surrounded by `<>` should be replaced with relevant elements *i.e. modules, controllers, actions* and *tasks* whereas `*` denotes **all** *controllers, actions* and *tasks* and `+` signifies that all the permissions of specified role are also applied. ***(`+` should always be before the role specified.)*** Role should match with the actual role in the storage and the temporary adjustment in files `config/autoload/login.local.php` (i.e. `_fixture`) and `vendor/user-management/src/Service/LoginService.php` (i.e. method `getLoggedInUserRole()`) should be adjusted accordingly.   



**LOGOUT**

* Access below URL in browser to logout.

`http://<SERVER>/user/logout`

**POSSIBLE ISSUES**

* You may encounter the following error. 

```
Additional information:
InvalidArgumentException
File:
.........\vendor\google\apiclient\src\Client.php:523
Message:
Invalid token format
```

**SOLUTION:** Refresh the original `Redirect URI` without `?CODE=` part.

* Following error can come up while granting the access to application using Google Sign in.

```
Authorization Error
Error 403: access_denied
The developer hasn’t given you access to this app. It’s currently being tested and it hasn’t been verified by Google. If you think you should have access, contact the developer (developer's email).
```

**SOLUTION:** It happens if the testing user's email has not been provided in the `OAUTH Consent Screen` as a test user. Add the user's email address even if the developer.


## Test ##

