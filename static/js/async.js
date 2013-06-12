
function AsyncRequest(){var dispatchResponse=bind(this,function(asyncResponse){try{this.handler(asyncResponse);}catch(exception){Util.error('Async handler threw an exception for URI %q: %x.',this.uri,exception);}});var dispatchErrorResponse=bind(this,function(asyncResponse){try{this.errorHandler(asyncResponse);}catch(exception){Util.error('Async error handler threw an exception for URI %q, when processing a '+'%d error: %s.',this.uri,asyncResponse.getError(),exception);}});var invokeResponseHandler=bind(this,function(){var r=new AsyncResponse();if(this.handler){try{if(!this.getOption('suppressEvaluation')){eval('response = ('+this.transport.responseText+')');copy_properties(r,response);}else{r.payload=this.transport;}
if(r.getError()){dispatchErrorResponse(r);}else{dispatchResponse(r);}}catch(exception){var desc='An error occurred during a request to a remote server. '+'This failure may be temporary, try repeating the request.';if(Util.isDevelopmentEnvironment()){desc=sprintf('An error occurred when decoding the JSON payload of the '+'AsyncResponse associated with an AsyncRequest to %q. The server '+'returned <a href="javascript:alert(%e);">a garbled response</a>,'+'then Javascript threw an exception: %x.',this.uri,this.transport.responseText,exception);}
copy_properties(r,{error:-1,errorSummary:'Data Error',errorDescription:desc});if(this.errorHandler){dispatchErrorResponse(r);}else{Util.error('Something bad happened -- write an error handler to figure out '+'what!');}}}});var invokeErrorHandler=bind(this,function(){var r=new AsyncResponse();var err=this.transport.status||-2;if(this.errorHandler){copy_properties(r,{error:err,errorSummary:AsyncRequest.getHTTPErrorSummary(err),errorDescription:AsyncRequest.getHTTPErrorDescription(err)});dispatchErrorResponse(r);}else{Util.error('Async request to %q failed with a %d error, but there was no error '+'handler available to deal with it.',this.uri,err);}});var onStateChange=function(){try{if(this.transport.readyState==4){if(this.transport.status>=200&&this.transport.status<300){invokeResponseHandler();}else{invokeErrorHandler();}}}catch(exception){Util.error('AsyncRequest exception when attempting to handle a state change: %x.',exception);}};var buildTransport=function(obj){var transport=Try.these(function(){return new XMLHttpRequest();},function(){return new ActiveXObject("Msxml2.XMLHTTP");},function(){return new ActiveXObject("Microsoft.XMLHTTP");})||null;if(transport){transport.onreadystatechange=bind(obj,onStateChange);}else{Util.error('Unable to build XMLHTTPRequest transport.');}
return transport;};copy_properties(this,{transport:buildTransport(this),method:'POST',uri:'',handler:null,errorHandler:null,data:null,option:{asynchronous:true,suppressErrorHandlerWarning:false,suppressEvaluation:false}});return this;}
copy_properties(AsyncRequest,{getHTTPErrorSummary:function(errCode){return AsyncRequest._getHTTPError(errCode).summary;},getHTTPErrorDescription:function(errCode){return AsyncRequest._getHTTPError(errCode).description;},pingURI:function(uri,data,synchronous){return new AsyncRequest().setURI(uri).setData(data).setOption('asynchronous',!synchronous).setOption('suppressErrorHandlerWarning',true).send();},_getHTTPError:function(errCode){var e=AsyncRequest._HTTPErrors[errCode]||AsyncRequest._HTTPErrors[errCode-(errCode%100)]||{summary:'HTTP Error',description:'Unknown HTTP error #'+errCode};return e;},_HTTPErrors:{400:{summary:'Bad Request',description:'Bad HTTP request.'},401:{summary:'Unauthorized',description:'Not authorized.'},403:{summary:'Forbidden',description:'Access forbidden.'},404:{summary:'Not Found',description:'URI does not exist.'}}});copy_properties(AsyncRequest.prototype,{setMethod:function(m){this.method=m.toString().toUpperCase();return this;},getMethod:function(){return this.method;},setData:function(obj){this.data=obj;return this;},getData:function(){return this.data;},setURI:function(uri){this.uri=uri;return this;},getURI:function(){return this.uri;},setHandler:function(fn){if(typeof(fn)!='function'){Util.error('AsyncRequest response handlers must be functions. Pass a function, '+'or use bind() to build one.');}else{this.handler=fn;}
return this;},getHandler:function(fn){return this.handler;},setErrorHandler:function(fn){if(typeof(fn)!='function'){Util.error('AsyncRequest error handlers must be functions. Pass a function, or '+'use bind() to build one.');}else{this.errorHandler=fn;}
return this;},getErrorHandler:function(fn){return this.handler;},setOption:function(opt,v){if(typeof(this.option[opt])!='undefined'){this.option[opt]=v;}else{Util.warn('AsyncRequest option %q does not exist; request to set it was ignored.',opt);}
return this;},getOption:function(opt){if(typeof(this.option[opt])=='undefined'){Util.warn('AsyncRequest option %q does not exist, get request failed.',opt);}
return this.option[opt];},send:function(){var query=ajaxArrayToQueryString(this.data);var uri;if(!this.uri){Util.error('Attempt to dispatch an AsyncRequest without an endpoint URI! This is '+'all sorts of silly and impossible, so the request failed.');return false;}
if(!this.errorHandler&&!this.getOption('suppressErrorHandlerWarning')){Util.warn('Dispatching an AsyncRequest that does not have an error handler. '+'You SHOULD supply one, or use AsyncRequest.pingURI(). If this '+'omission is intentional and well-considered, set the %q option to '+'suppress this warning.','suppressErrorHandlerWarning');}
if(this.method=='GET'){uri=this.uri+(query?'?'+query:'');query='';}else{uri=this.uri;}
this.transport.open(this.method,uri,this.getOption('asynchronous'));if(this.method=='POST'){this.transport.setRequestHeader('Content-Type','application/x-www-form-urlencoded');}
this.transport.send(query);return true;}});function AsyncResponse(){copy_properties(this,{error:0,errorSummary:null,errorDescription:null,payload:null});return this;}
copy_properties(AsyncResponse.prototype,{getPayload:function(){return this.payload;},getError:function(){return this.error;},getErrorSummary:function(){return this.errorSummary;},getErrorDescription:function(){return this.errorDescription;}});