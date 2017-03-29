<?php
// Default module config parameters.
// They can be override in application config by setting $config['modules']['params'].
return [
  //'changeStartPage' => true, // change default site startpage to modules startpage
    'changeStartPage' => false,
    'urlPrefix' => 'testapi',
    'tokenExpiredPeriod' => 3600, //sec
    'pageSize' => 5,
];
