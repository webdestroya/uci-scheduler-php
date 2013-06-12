
function bind(obj,method){obj=obj||window;var args=[];for(var ii=2;ii<arguments.length;ii++){args.push(arguments[ii]);}
return function(){var _args=[];for(var kk=0;kk<args.length;kk++){_args.push(args[kk]);}
for(var jj=0;jj<arguments.length;jj++){_args.push(arguments[jj]);}
if(typeof(method)=="string"){if(obj[method]){return obj[method].apply(obj,_args);}}else{return method.apply(obj,_args);}}}
Function.prototype.bind=function(context){var argv=[arguments[0],this]
var argc=arguments.length;for(var ii=1;ii<argc;ii++){argv.push(arguments[ii]);}
return bind.apply(null,argv);}
function chain(u,v){var calls=[];for(var ii=0;ii<arguments.length;ii++){calls.push(arguments[ii]);}
return function(){for(var ii=0;ii<calls.length;ii++){if(calls[ii]&&calls[ii].apply(null,arguments)===false){return false;}}
return true;}}
function copy_properties(u,v){for(var k in v){u[k]=v[k];}
return u;}
var Try={these:function(){var len=arguments.length;var res;for(var ii=0;ii<len;ii++){try{res=arguments[ii]();return res;}catch(anIgnoredException){}}
return res;}};var Util={isDevelopmentEnvironment:function(){return(typeof(HTTPRequest)!='undefined')&&HTTPRequest.dev;},warn:function(){Util.log(sprintf.apply(null,arguments),'warn');},error:function(){Util.log(sprintf.apply(null,arguments),'error');},log:function(msg,type){if(Util.isDevelopmentEnvironment()){if(typeof(window['infoConsole'])!='undefined'){infoConsole.addEvent(new fbinfoconsole.ConsoleEvent(['js',type],nl2br(msg)));}else{if(type!='deprecated'){alert(msg);}}}else{if(type=='error'){(typeof(window['debug_rlog'])=='function')&&debug_rlog(msg);}}},deprecated:function(what){if(!Util._deprecatedThings[what]){Util._deprecatedThings[what]=true;var msg=sprintf('Deprecated: %q is deprecated.\n\n%s',what,Util.whyIsThisDeprecated(what));Util.log(msg,'deprecated');}},whyIsThisDeprecated:function(what){return Util._deprecatedBecause[what.toLowerCase()]||'No additional information is available about this deprecation.';},_deprecatedBecause:{},_deprecatedThings:{}};var IConfigurable={getOption:function(opt){if(typeof(this.option[opt])=='undefined'){Util.warn('Failed to get option %q; it does not exist.',opt);return null;}
return this.option[opt];},setOption:function(opt,v){if(typeof(this.option[opt])=='undefined'){Util.warn('Failed to set option %q; it does not exist.',opt);}else{this.option[opt]=v;}
return this;},getOptions:function(){return this.option;}};function Ad(){}
copy_properties(Ad,{refreshRate:10000,lastRefreshTime:new Date(),refresh:function(){var delta=(new Date().getTime()-Ad.lastRefreshTime.getTime());if(delta>Ad.refreshRate){var f=Ad.getFrame();if(f){if(!f.osrc){f.osrc=f.src;}
f.src=f.osrc+'?'+Math.random();Ad.lastRefreshTime=new Date();}}},getFrame:function(){return ge('ssponsor')&&ge('ssponsor').getElementsByTagName('iframe')[0];}});