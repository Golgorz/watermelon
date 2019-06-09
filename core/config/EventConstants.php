<?php

//Modules load
define("LOAD_MODULES_BEFORE", "GFStarter.loadModules.before");
define("LOAD_MODULES_AFTER", "GFStarter.loadModules.after");

//Parse request process
define("PARSE_REQUEST_BEFORE", "Request.parseRequest.before");
define("MATCH_REQUEST_BEFORE", "Router.matchRequest.before");
define("EXECUTE_REQUEST_BEFORE","Request.executeRequest.before");
define("SEND_RESPONSE_BEFORE", "Request.sendResponse.before");

//Router
define("ROUTER_MATCH_SUCCESS", "Router.match.success");
define("ROUTER_MATCH_FAILED", "Router.match.failed");
define("ROUTER_PARSE_PARAMS_BEFORE", "Router.parseParams.before");
define("ROUTER_PARSE_PARAMS_AFTER", "Router.parseParams.after");