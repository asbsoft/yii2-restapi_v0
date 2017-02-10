
Test version of REST API based on Yii2 framework
Alexandr Belogolovsky, ab2014box@gmail.com

Usage

- Install basic version of Yii2.
  In this example used https://github.com/yiisoft/yii2/releases/download/2.0.6/yii-basic-app-2.0.6.tgz

- Copy files from this module distributive to corresponding folder of web site
  @vendor/asbsoft/modules/restapi_v0/
  and tune configs in @app/config/ folder:
    - add to file web.php before latest operator "return $config;" such strings
        $config['aliases']['@asb/yii2'] = '@vendor/asbsoft'; // if module in @vendor/asbsoft/modules/restapi_v0/
        $moduleId = 'restapi0';
        $config['bootstrap'][] = $moduleId;
        $config['modules'][$moduleId] = [
            'class' => 'asb\yii2\modules\restapi_v0\Module',
            'params' => [ // you can change some parameters here - they override default module parameters
                //'changeStartPage' => false,   // default = true for change site startpage to this module startpage
                //'urlPrefix' => 'testapi',     // frontend URL prefix
                //'tokenExpiredPeriod' => 3600, // seconds to expire access token
                //'pageSize' => 20,             // posts listing page size
            ],
        ];
        return $config; // latest string in default application config
    - db.php - put database tunes here
    - web.php - don't forget to fill cookieValidationKey:
      'components' => [
          'request' => [
              'cookieValidationKey' => '...', // insert a secret key here (if it is empty)
          ],
      ],

- Apply migrations to create data tables and fill tables by test data:
    yii migrate/up --migrationPath=%PATH_TO_MODULE%/migrations
  where %PATH_TO_MODULE% is directory @vendor/asbsoft/modules/restapi_v0,

- If you apply 'test_data' migration users table will have some test users with such login/password:
  - tester@example.com/test1234
  - user@example.com/user1234
  and posts table will have some test posts.

- You can use installed module immediately just run http://%BASE_URL% in you browser
  where %BASE_URL% is base URL of you Yii2-site.
  This module replace start URL of you site for demo purpose.
  You can change in 'params' of this module 'changeStartPage' => false
  and use module by full link http://%BASE_URL%/testapi/auth not from start page.
  You can change URL prefix 'urlPrefix' => 'testapi' in module params too.

- There is simple form here for registration new users and get access token.
  Also this test form allow to list/create/update/view post(s).

- You can test this API from command line by curl:
  - create new user with %LOGIN% and %PASSWORD%:
    curl -i -X POST -d "login=%LOGIN%&password=%PASSWORD%" "http://%BASE_URL%/testapi/users"
    - return status 200 if creation OK
    - return status 403 if user with such login already exists
    - return status 422 if validation fail
  - if you need new access token:
    curl -i -H "Accept:application/json" "http://%BASE_URL%/testapi/users?login=user@example.com&password=user1234"
    - return status 200 if user found and return user id, access token and token expire time in format:
      [{"id":11,"access_token":"9d312894d7a3817a67fc2c72f2e9985b","token_expired":"2016-03-24 20:51:06"}]  
  - if you already get %ACCESS_TOKEN% use it for get list (first page) of posts:
    curl -i -H "Accept:application/json" "http://%BASE_URL%/testapi/posts?access-token=%ACCESS_TOKEN%"
    - return status 200 and JSON-list of posts if OK
    - return status 401 if unauthorized (illegal token)
  - next page:
    curl -i -H "Accept:application/json" "http://%BASE_URL%/testapi/posts?access-token=%ACCESS_TOKEN%&page=1"
  - get one post by %ID%:
    curl -i -H "Accept:application/json" "http://%BASE_URL%/testapi/posts/%ID%?access-token=%ACCESS_TOKEN%"
    - return status 200 and JSON-data of post if OK  
    - return status 403 if post owner another user
    - return status 404 if not found
  - create post with %TEXT%
    curl -i -d text=%TEXT% "http://%BASE_URL%/testapi/posts?access-token=..."
    - return status 201 and JSON-data of post if creation OK
  - edit post %ID% with %NEW_TEXT%
    curl -i -X PUT -d text=%NEW_TEXT% "http://%BASE_URL%/testapi/posts/%ID%?access-token=..."
    - return status 200 and JSON-data of post if edition OK
  - delete post by %ID%:
    curl -i -X DELETE "http://%BASE_URL%/testapi/posts/%ID%?access-token=%ACCESS_TOKEN%"
    - return status 204 if deletion OK
    - return status 403 if post owner another user
    - return status 404 if not found
