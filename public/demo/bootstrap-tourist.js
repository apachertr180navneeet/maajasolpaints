(function(window,factory){if(typeof define==='function'&&define.amd){return define(['jquery'],function(jQuery){return window.Tour=factory(jQuery);});}else if(typeof exports==='object'){return module.exports=factory(require('jquery'));}else{return window.Tour=factory(window.jQuery);}})(window,function($){const DOMID_BACKDROP="#tourBackdrop";const DOMID_BACKDROP_TEMP="#tourBackdrop-temp";const DOMID_HIGHLIGHT="#tourHighlight";const DOMID_HIGHLIGHT_TEMP="#tourHighlight-temp";const DOMID_PREVENT="#tourPrevent";var Tour,document,objTemplates,objTemplatesButtonTexts;document=window.document;Tour=(function(){function Tour(options)
{var storage;try
{storage=window.localStorage;}
catch(error)
{storage=false;}
objTemplatesButtonTexts={prevButton:"Prev",nextButton:"Next",pauseButton:"Pause",resumeButton:"Resume",endTourButton:"End Tour"};this._options=$.extend(true,{name:'tour',steps:[],container:'body',autoscroll:true,keyboard:true,storage:storage,debug:false,backdrop:false,backdropContainer:'body',backdropOptions:{highlightOpacity:0.9,highlightColor:"#FFF",backdropSibling:false,animation:{backdropShow:function(domElement,step)
{domElement.fadeIn();},backdropHide:function(domElement,step)
{domElement.fadeOut("slow")},highlightShow:function(domElement,step)
{step.fnPositionHighlight();domElement.fadeIn();},highlightTransition:"tour-highlight-animation",highlightHide:function(domElement,step)
{domElement.fadeOut("slow")}},},redirect:true,orphan:false,showIfUnintendedOrphan:false,duration:false,delay:false,basePath:'',template:null,localization:{buttonTexts:objTemplatesButtonTexts},framework:'bootstrap3',sanitizeWhitelist:[],sanitizeFunction:null,showProgressBar:true,showProgressText:true,getProgressBarHTML:null,getProgressTextHTML:null,afterSetState:function(key,value){},afterGetState:function(key,value){},afterRemoveState:function(key){},onStart:function(tour){},onEnd:function(tour){},onShow:function(tour){},onShown:function(tour){},onHide:function(tour){},onHidden:function(tour){},onNext:function(tour){},onPrev:function(tour){},onPause:function(tour,duration){},onResume:function(tour,duration){},onRedirectError:function(tour){},onElementUnavailable:null,onPreviouslyEnded:null,onModalHidden:null,},options);if($(this._options.backdropContainer).length==0)
{this._options.backdropContainer="body";}
if(this._options.framework!=="bootstrap3"&&this._options.framework!=="bootstrap4")
{this._debug('Invalid framework specified: '+this._options.framework);throw "Bootstrap Tourist: Invalid framework specified";}
objTemplates={bootstrap3:'<div class="popover" role="tooltip"> <div class="arrow"></div> <h3 class="popover-title"></h3> <div class="popover-content"></div> <div class="popover-navigation"> <div class="btn-group"> <button class="btn btn-sm btn-default" data-role="prev">&laquo; '+this._options.localization.buttonTexts.prevButton+'</button> <button class="btn btn-sm btn-default" data-role="next">'+this._options.localization.buttonTexts.nextButton+' &raquo;</button> <button class="btn btn-sm btn-default" data-role="pause-resume" data-pause-text="'+this._options.localization.buttonTexts.pauseButton+'" data-resume-text="'+this._options.localization.buttonTexts.resumeButton+'">'+this._options.localization.buttonTexts.pauseButton+'</button> </div> <button class="btn btn-sm btn-default" data-role="end">'+this._options.localization.buttonTexts.endTourButton+'</button> </div> </div>',bootstrap4:'<div class="popover" role="tooltip"> <div class="arrow"></div> <h3 class="popover-header"></h3> <div class="popover-body"></div> <div class="popover-navigation"> <div class="btn-group"> <button class="btn btn-sm btn-outline-secondary" data-role="prev">&laquo; '+this._options.localization.buttonTexts.prevButton+'</button> <button class="btn btn-sm btn-outline-secondary" data-role="next">'+this._options.localization.buttonTexts.nextButton+' &raquo;</button> <button class="btn btn-sm btn-outline-secondary" data-role="pause-resume" data-pause-text="'+this._options.localization.buttonTexts.pauseButton+'" data-resume-text="'+this._options.localization.buttonTexts.resumeButton+'">'+this._options.localization.buttonTexts.pauseButton+'</button> </div> <button class="btn btn-sm btn-outline-secondary" data-role="end">'+this._options.localization.buttonTexts.endTourButton+'</button> </div> </div>',};if(this._options.template===null)
{if(objTemplates[this._options.framework]!=null&&objTemplates[this._options.framework]!=undefined)
{this._options.template=objTemplates[this._options.framework];this._debug('Using framework template: '+this._options.framework);}
else
{this._debug('Warning: '+this._options.framework+' specified for template (no template option set), but framework is unknown. Tour will not work!');}}
else
{this._debug('Using custom template');}
if(typeof(this._options.sanitizeFunction)=="function")
{this._debug("Using custom sanitize function in place of bootstrap - security implications, be careful");}
else
{this._options.sanitizeFunction=null;this._debug("Extending Bootstrap sanitize options");var defaultWhiteList=[];if(this._options.framework=="bootstrap4"&&$.fn.popover.Constructor.Default.whiteList!==undefined)
{defaultWhiteList=$.fn.popover.Constructor.Default.whiteList;}
if(this._options.framework=="bootstrap3"&&$.fn.popover.Constructor.DEFAULTS.whiteList!==undefined)
{defaultWhiteList=$.fn.popover.Constructor.DEFAULTS.whiteList;}
var whiteListAdditions={"button":["data-role","style"],"img":["style"],"div":["style"]};var whiteList=$.extend(true,{},defaultWhiteList);$.each(whiteListAdditions,function(index,value)
{if(whiteList[index]==undefined)
{whiteList[index]=[];}
$.merge(whiteList[index],value);});$.each(this._options.sanitizeWhitelist,function(index,value)
{if(whiteList[index]==undefined)
{whiteList[index]=[];}
$.merge(whiteList[index],value);});this._options.sanitizeWhitelist=whiteList;}
this._current=null;this.backdrops=[];return this;}
Tour.prototype.addSteps=function(steps){var j,len,step;for(j=0,len=steps.length;j<len;j++){step=steps[j];this.addStep(step);}
return this;};Tour.prototype.addStep=function(step){this._options.steps.push(step);return this;};Tour.prototype.getStepCount=function(){return this._options.steps.length;};Tour.prototype.getStep=function(i){if(this._options.steps[i]!=null){if(typeof(this._options.steps[i].element)=="function")
{this._options.steps[i].element=this._options.steps[i].element();}
this._options.steps[i]=$.extend(true,{id:"step-"+i,path:'',host:'',placement:'right',positioning:{adjustRelative:null},title:'',content:'<p></p>',next:i===this._options.steps.length-1?-1:i+1,prev:i-1,animation:true,container:this._options.container,autoscroll:this._options.autoscroll,backdrop:this._options.backdrop,redirect:this._options.redirect,preventInteraction:false,orphan:this._options.orphan,showIfUnintendedOrphan:this._options.showIfUnintendedOrphan,duration:this._options.duration,delay:this._options.delay,delayOnElement:null,template:this._options.template,showProgressBar:this._options.showProgressBar,showProgressText:this._options.showProgressText,getProgressBarHTML:this._options.getProgressBarHTML,getProgressTextHTML:this._options.getProgressTextHTML,onShow:this._options.onShow,onShown:this._options.onShown,onHide:this._options.onHide,onHidden:this._options.onHidden,onNext:this._options.onNext,onPrev:this._options.onPrev,onPause:this._options.onPause,onResume:this._options.onResume,onRedirectError:this._options.onRedirectError,onElementUnavailable:this._options.onElementUnavailable,onModalHidden:this._options.onModalHidden,internalFlags:{elementModal:null,elementModalOriginal:null,elementBootstrapSelectpicker:null}},this._options.steps[i]);this._options.steps[i].backdropOptions=$.extend(true,{},this._options.backdropOptions,this._options.steps[i].backdropOptions);if(this._options.steps[i].reflexOnly==true)
{this._options.steps[i].reflex=true;}
return this._options.steps[i];}};Tour.prototype._setStepFlag=function(stepNumber,flagName,value)
{if(this._options.steps[stepNumber]!=null)
{this._options.steps[stepNumber].internalFlags[flagName]=value;}};Tour.prototype._getStepFlag=function(stepNumber,flagName)
{if(this._options.steps[stepNumber]!=null)
{return this._options.steps[stepNumber].internalFlags[flagName];}};Tour.prototype.init=function()
{console.log('You should remove Tour.init() from your code. It\'s not required with Bootstrap Tourist');}
Tour.prototype.start=function()
{if(this.ended())
{if(this._options.onPreviouslyEnded!=null&&typeof(this._options.onPreviouslyEnded)=="function")
{this._debug('Tour previously ended, exiting. Call tour.restart() to force restart. Firing onPreviouslyEnded()');this._options.onPreviouslyEnded(this);}
else
{this._debug('Tour previously ended, exiting. Call tour.restart() to force restart');}
return this;}
this.setCurrentStep();this._createOverlayElements();this._initMouseNavigation();this._initKeyboardNavigation();var _this=this;$(window).on("resize.tour-"+_this._options.name,function()
{_this.reshowCurrentStep();});var promise=this._makePromise(this._options.onStart!=null?this._options.onStart(this):void 0);this._callOnPromiseDone(promise,this.showStep,this._current);return this;};Tour.prototype.next=function(){var promise;promise=this.hideStep();return this._callOnPromiseDone(promise,this._showNextStep);};Tour.prototype.prev=function(){var promise;promise=this.hideStep();return this._callOnPromiseDone(promise,this._showPrevStep);};Tour.prototype.goTo=function(i){var promise;this._debug("goTo step "+i);promise=this.hideStep();return this._callOnPromiseDone(promise,this.showStep,i);};Tour.prototype.end=function()
{this._debug("Tour.end() called");var endHelper,promise;endHelper=(function(_this){return function(e){$(document).off("click.tour-"+_this._options.name);$(document).off("keyup.tour-"+_this._options.name);$(window).off("resize.tour-"+_this._options.name);$(window).off("scroll.tour-"+_this._options.name);_this._setState('end','yes');_this._clearTimer();$(".tour-step-element-reflex").removeClass("tour-step-element-reflex");$(".tour-step-element-reflexOnly").removeClass("tour-step-element-reflexOnly");_this._hideBackdrop();_this._destroyOverlayElements();if(_this._options.onEnd!=null)
{return _this._options.onEnd(_this);}};})(this);promise=this.hideStep();return this._callOnPromiseDone(promise,endHelper);};Tour.prototype.ended=function(){return this._getState('end')=='yes';};Tour.prototype.restart=function()
{this._removeState('current_step');this._removeState('end');this._removeState('redirect_to');return this.start();};Tour.prototype.pause=function(){var step;step=this.getStep(this._current);if(!(step&&step.duration)){return this;}
this._paused=true;this._duration-=new Date().getTime()-this._start;window.clearTimeout(this._timer);this._debug("Paused/Stopped step "+(this._current+1)+" timer ("+this._duration+" remaining).");if(step.onPause!=null){return step.onPause(this,this._duration);}};Tour.prototype.resume=function(){var step;step=this.getStep(this._current);if(!(step&&step.duration)){return this;}
this._paused=false;this._start=new Date().getTime();this._duration=this._duration||step.duration;this._timer=window.setTimeout((function(_this)
{return function()
{if(_this._isLast())
{return _this.end();}
else
{return _this.next();}};})(this),this._duration);this._debug("Started step "+(this._current+1)+" timer with duration "+this._duration);if((step.onResume!=null)&&this._duration!==step.duration){return step.onResume(this,this._duration);}};Tour.prototype.reshowCurrentStep=function()
{this._debug("Reshowing current step "+this.getCurrentStepIndex());var promise;promise=this.hideStep();return this._callOnPromiseDone(promise,this.showStep,this._current);};Tour.prototype.hideStep=function()
{var hideDelay,hideStepHelper,promise,step;step=this.getStep(this.getCurrentStepIndex());if(!step)
{return;}
this._clearTimer();promise=this._makePromise(step.onHide!=null?step.onHide(this,this.getCurrentStepIndex()):void 0);hideStepHelper=(function(_this)
{return function(e)
{var $element;$element=$(step.element);if(!($element.data('bs.popover')||$element.data('popover')))
{$element=$('body');}
if(_this._options.framework=="bootstrap3")
{$element.popover('destroy');}
if(_this._options.framework=="bootstrap4")
{$element.popover('dispose');}
$element.removeClass("tour-"+_this._options.name+"-element tour-"+_this._options.name+"-"+_this.getCurrentStepIndex()+"-element").removeData('bs.popover');if(step.reflex)
{$element.removeClass('tour-step-element-reflex').off((_this._reflexEvent(step.reflex))+".tour-"+_this._options.name);$element.removeClass('tour-step-element-reflexOnly');}
_this._unfixBootstrapSelectPickerZindex(step);var tmpModalOriginalElement=_this._getStepFlag(_this.getCurrentStepIndex(),"elementModalOriginal");if(tmpModalOriginalElement!=null)
{_this._setStepFlag(_this.getCurrentStepIndex(),"elementModalOriginal",null);step.element=tmpModalOriginalElement;}
if(step.onHidden!=null)
{return step.onHidden(_this);}};})(this);hideDelay=step.delay.hide||step.delay;if({}.toString.call(hideDelay)==='[object Number]'&&hideDelay>0){this._debug("Wait "+hideDelay+" milliseconds to hide the step "+(this._current+1));window.setTimeout((function(_this){return function(){return _this._callOnPromiseDone(promise,hideStepHelper);};})(this),hideDelay);}else{this._callOnPromiseDone(promise,hideStepHelper);}
return promise;};Tour.prototype.showStep=function(i){var path,promise,showDelay,showStepHelper,skipToPrevious,step,$element;if(this.ended())
{this._debug('Tour ended, showStep prevented.');if(this._options.onEnd!=null)
{this._options.onEnd(this);}
return this;}
step=this.getStep(i);if(!step){return;}
skipToPrevious=i<this._current;promise=this._makePromise(step.onShow!=null?step.onShow(this,i):void 0);this.setCurrentStep(i);path=(function(){switch({}.toString.call(step.path)){case '[object Function]':return step.path();case '[object String]':return this._options.basePath+step.path;default:return step.path;}}).call(this);if(step.redirect&&this._isRedirect(step.host,path,document.location)){this._redirect(step,i,path);if(!this._isJustPathHashDifferent(step.host,path,document.location)){return;}}
var $modalObject=null;if(step.orphan===false&&($(step.element).hasClass("modal")||$(step.element).data('bs.modal')))
{$modalObject=$(step.element);this._setStepFlag(this.getCurrentStepIndex(),"elementModalOriginal",step.element);step.element=$(step.element).find(".modal-content:first");}
$element=$(step.element);if($modalObject===null&&$element.parents(".modal:first").length)
{$modalObject=$element.parents(".modal:first");}
if($modalObject&&$modalObject.length>0)
{this._debug("Modal identified, onModalHidden callback available");this._setStepFlag(i,"elementModal",$modalObject)
var funcModalHelper=function(_this,$_modalObject)
{return function()
{_this._debug("Modal close triggered");if(typeof(step.onModalHidden)=="function")
{var rslt=step.onModalHidden(_this,i);if(rslt===false)
{_this._debug("onModalHidden returned exactly false, tour step unchanged");return;}
if(Number.isInteger(rslt))
{_this._debug("onModalHidden returned int, tour moving to step "+rslt+1);$_modalObject.off("hidden.bs.modal",funcModalHelper);return _this.goTo(rslt);}
_this._debug("onModalHidden did not return false or int, continuing tour");}
$_modalObject.off("hidden.bs.modal",funcModalHelper);if(_this._isLast())
{_this._debug("Modal close reached end of tour");return _this.end();}
else
{_this._debug("Modal close: next step called");return _this.next();}};}(this,$modalObject);$modalObject.off("hidden.bs.modal",funcModalHelper).on("hidden.bs.modal",funcModalHelper);}
showStepHelper=(function(_this)
{return function(e)
{if(_this._isOrphan(step))
{if(step.orphan===false&&step.showIfUnintendedOrphan===false)
{_this._debug("Skip the orphan step "+(_this._current+1)+".\nOrphan option is false and the element "+step.element+" does not exist or is hidden.");if(typeof(step.onElementUnavailable)=="function")
{_this._debug("Calling onElementUnavailable callback");step.onElementUnavailable(_this,_this._current);}
if(skipToPrevious){_this._showPrevStep(true);}else{_this._showNextStep(true);}
return;}
if(step.orphan===false&&step.showIfUnintendedOrphan===true)
{_this._debug("Show the unintended orphan step "+(_this._current+1)+". showIfUnintendedOrphan option is true.");}
else
{_this._debug("Show the orphan step "+(_this._current+1)+". Orphans option is true.");}}
if(step.autoscroll&&!_this._isOrphan(step))
{_this._scrollIntoView(i);}
else
{_this._showPopoverAndOverlay(i);}
if(step.duration){return _this.resume();}};})(this);showDelay=step.delay.show||step.delay;if({}.toString.call(showDelay)==='[object Number]'&&showDelay>0){this._debug("Wait "+showDelay+" milliseconds to show the step "+(this._current+1));window.setTimeout((function(_this){return function(){return _this._callOnPromiseDone(promise,showStepHelper);};})(this),showDelay);}
else
{if(step.delayOnElement)
{var $delayElement=null;var delayFunc=null;var _this=this;var revalidateDelayElement=function(){if(typeof(step.delayOnElement.delayElement)=="function")
return step.delayOnElement.delayElement();else if(step.delayOnElement.delayElement=="element")
return $(step.element);else
return $(step.delayOnElement.delayElement);};var $delayElement=revalidateDelayElement();var delayElementLog=$delayElement.length>0?$delayElement[0].tagName:step.delayOnElement.delayElement;var delayMax=(step.delayOnElement.maxDelay?step.delayOnElement.maxDelay:2000);this._debug("Wait for element "+delayElementLog+" visible or max "+delayMax+" milliseconds to show the step "+(this._current+1));delayFunc=window.setInterval(function()
{_this._debug("Wait for element "+delayElementLog+": checking...");if($delayElement.length===0){$delayElement=revalidateDelayElement();}
if($delayElement.is(':visible'))
{_this._debug("Wait for element "+delayElementLog+": found, showing step");window.clearInterval(delayFunc);delayFunc=null;return _this._callOnPromiseDone(promise,showStepHelper);}},250);if(delayMax<250)
delayMax=251;window.setTimeout(function()
{if(delayFunc)
{_this._debug("Wait for element "+delayElementLog+": max timeout reached without element found");window.clearInterval(delayFunc);return _this._callOnPromiseDone(promise,showStepHelper);}},delayMax);}
else
{this._callOnPromiseDone(promise,showStepHelper);}}
return promise;};Tour.prototype.getCurrentStepIndex=function(){return this._current;};Tour.prototype.setCurrentStep=function(value){if(value!=null)
{this._current=value;this._setState('current_step',value);}
else
{this._current=this._getState('current_step');this._current=this._current===null?0:parseInt(this._current,10);}
return this;};Tour.prototype._setState=function(key,value){var e,keyName;if(this._options.storage){keyName=this._options.name+"_"+key;try{this._options.storage.setItem(keyName,value);}catch(error){e=error;if(e.code===DOMException.QUOTA_EXCEEDED_ERR){this._debug('LocalStorage quota exceeded. State storage failed.');}}
return this._options.afterSetState(keyName,value);}else{if(this._state==null){this._state={};}
return this._state[key]=value;}};Tour.prototype._removeState=function(key){var keyName;if(this._options.storage){keyName=this._options.name+"_"+key;this._options.storage.removeItem(keyName);return this._options.afterRemoveState(keyName);}else{if(this._state!=null){return delete this._state[key];}}};Tour.prototype._getState=function(key){var keyName,value;if(this._options.storage){keyName=this._options.name+"_"+key;value=this._options.storage.getItem(keyName);}else{if(this._state!=null){value=this._state[key];}}
if(value===void 0||value==='null'){value=null;}
this._options.afterGetState(key,value);return value;};Tour.prototype._showNextStep=function(skipOrphan){var promise,showNextStepHelper,step;var skipOrphan=skipOrphan||false;showNextStepHelper=(function(_this){return function(e){return _this.showStep(_this._current+1);};})(this);promise=void 0;step=this.getStep(this._current);if(skipOrphan===false&&step.onNext!=null)
{var rslt=step.onNext(this);if(rslt===false)
{this._debug("onNext callback returned false, preventing move to next step");return this.showStep(this._current);}
promise=this._makePromise(rslt);}
return this._callOnPromiseDone(promise,showNextStepHelper);};Tour.prototype._showPrevStep=function(skipOrphan){var promise,showPrevStepHelper,step;var skipOrphan=skipOrphan||false;showPrevStepHelper=(function(_this){return function(e){return _this.showStep(step.prev);};})(this);promise=void 0;step=this.getStep(this._current);if(skipOrphan===false&&step.onPrev!=null)
{var rslt=step.onPrev(this);if(rslt===false)
{this._debug("onPrev callback returned false, preventing move to previous step");return this.showStep(this._current);}
promise=this._makePromise(rslt);}
return this._callOnPromiseDone(promise,showPrevStepHelper);};Tour.prototype._debug=function(text){if(this._options.debug){return window.console.log("[ Bootstrap Tourist: '"+this._options.name+"' ] "+text);}};Tour.prototype._isRedirect=function(host,path,location){var currentPath;if((host!=null)&&host!==''&&(({}.toString.call(host)==='[object RegExp]'&&!host.test(location.origin))||({}.toString.call(host)==='[object String]'&&this._isHostDifferent(host,location)))){return true;}
currentPath=[location.pathname,location.search,location.hash].join('');return(path!=null)&&path!==''&&(({}.toString.call(path)==='[object RegExp]'&&!path.test(currentPath))||({}.toString.call(path)==='[object String]'&&this._isPathDifferent(path,currentPath)));};Tour.prototype._isHostDifferent=function(host,location){switch({}.toString.call(host)){case '[object RegExp]':return!host.test(location.origin);case '[object String]':return this._getProtocol(host)!==this._getProtocol(location.href)||this._getHost(host)!==this._getHost(location.href);default:return true;}};Tour.prototype._isPathDifferent=function(path,currentPath){return this._getPath(path)!==this._getPath(currentPath)||!this._equal(this._getQuery(path),this._getQuery(currentPath))||!this._equal(this._getHash(path),this._getHash(currentPath));};Tour.prototype._isJustPathHashDifferent=function(host,path,location){var currentPath;if((host!=null)&&host!==''){if(this._isHostDifferent(host,location)){return false;}}
currentPath=[location.pathname,location.search,location.hash].join('');if({}.toString.call(path)==='[object String]'){return this._getPath(path)===this._getPath(currentPath)&&this._equal(this._getQuery(path),this._getQuery(currentPath))&&!this._equal(this._getHash(path),this._getHash(currentPath));}
return false;};Tour.prototype._redirect=function(step,i,path){var href;if($.isFunction(step.redirect)){return step.redirect.call(this,path);}else{href={}.toString.call(step.host)==='[object String]'?""+step.host+path:path;this._debug("Redirect to "+href);if(this._getState('redirect_to')===(""+i)){this._debug("Error redirection loop to "+path);this._removeState('redirect_to');if(step.onRedirectError!=null){return step.onRedirectError(this);}}else{this._setState('redirect_to',""+i);return document.location.href=href;}}};Tour.prototype._isOrphan=function(step)
{var isOrphan=(step.orphan==true)||(step.element==null)||!$(step.element).length||$(step.element).is(':hidden')&&($(step.element)[0].namespaceURI!=='http://www.w3.org/2000/svg');return isOrphan;};Tour.prototype._isLast=function(){return this._current>=this._options.steps.length-1;};Tour.prototype._showPopoverAndOverlay=function(i)
{var step;if(this.getCurrentStepIndex()!==i||this.ended()){return;}
step=this.getStep(i);this._updateBackdropElements(step);this._updateOverlayElements(step);this._fixBootstrapSelectPickerZindex(step);this._showPopover(step,i);if(step.onShown!=null)
{step.onShown(this);}
return this;};Tour.prototype._showPopover=function(step,i){var $element,$tip,isOrphan,options,title,content,percentProgress,modalObject;isOrphan=this._isOrphan(step);if($(document).find(".popover.tour-"+this._options.name+".tour-"+this._options.name+"-"+this.getCurrentStepIndex()).length==0)
{$(".tour-"+this._options.name).remove();step.template=this._template(step,i);if(isOrphan)
{step.element='body';step.placement='top';if(step.reflexOnly)
{this._debug("Step is an orphan, and reflexOnly is set: ignoring reflexOnly");}}
$element=$(step.element);$element.addClass("tour-"+this._options.name+"-element tour-"+this._options.name+"-"+i+"-element");if(step.reflex&&!isOrphan)
{$element.addClass('tour-step-element-reflex');$element.off((this._reflexEvent(step.reflex))+".tour-"+this._options.name).on((this._reflexEvent(step.reflex))+".tour-"+this._options.name,(function(_this){return function()
{if(_this._isLast())
{return _this.end();}
else
{return _this.next();}};})(this));if(step.reflexOnly)
{$element.addClass('tour-step-element-reflexOnly');var $objNext=$(step.template).find('[data-role="next"]').clone();if($objNext.length)
{var strNext=$objNext[0].outerHTML;$objNext.hide();var strHidden=$objNext[0].outerHTML;step.template=step.template.replace(strNext,strHidden);}}}
title=step.title;content=step.content;percentProgress=parseInt(((i+1)/this.getStepCount())*100);if(step.showProgressBar)
{if(typeof(step.getProgressBarHTML)=="function")
{content=step.getProgressBarHTML(percentProgress)+content;}
else
{content='<div class="progress"><div class="progress-bar progress-bar-striped" role="progressbar" style="width: '+percentProgress+'%;"></div></div>'+content;}}
if(step.showProgressText)
{if(typeof(step.getProgressTextHTML)=="function")
{title+=step.getProgressTextHTML(i,percentProgress,this.getStepCount());}
else
{if(this._options.framework=="bootstrap3")
{title+='<span class="pull-right">'+(i+1)+'/'+this.getStepCount()+'</span>';}
if(this._options.framework=="bootstrap4")
{title+='<span class="float-right">'+(i+1)+'/'+this.getStepCount()+'</span>';}}}
var popOpts={placement:step.placement,trigger:'manual',title:title,content:content,html:true,whiteList:this._options.sanitizeWhitelist,sanitizeFn:this._options.sanitizeFunction,animation:step.animation,container:step.container,template:step.template,selector:step.element,};if(this._options.framework=="bootstrap4")
{if(isOrphan)
{popOpts.offset=function(obj)
{var top=Math.max(0,(($(window).height()-obj.popper.height)/2));var left=Math.max(0,(($(window).width()-obj.popper.width)/2));obj.popper.position="fixed";obj.popper.top=top;obj.popper.bottom=top+obj.popper.height;obj.popper.left=left;obj.popper.right=top+obj.popper.width;return obj;};}
else
{popOpts.selector="#"+step.element[0].id;if(step.positioning.adjustRelative!==null&&step.positioning.adjustRelative.length>0)
{if(typeof step.positioning.adjustRelative=="function")
{popOpts.offset=step.positioning.adjustRelative();}
else
{popOpts.offset=step.positioning.adjustRelative;}}}}
$element.popover(popOpts);$element.popover('show');if(this._options.framework=="bootstrap3")
{$tip=$element.data('bs.popover')?$element.data('bs.popover').tip():$element.data('popover').tip();if($element.css('position')==='fixed')
{$tip.css('position','fixed');}
if(isOrphan)
{this._center($tip);$tip.css('position','fixed');}
else
{this._reposition($tip,step);}}
if(this._options.framework=="bootstrap4")
{$tip=$(($element.data('bs.popover')?$element.data('bs.popover').getTipElement():$element.data('popover').getTipElement()));}
$tip.attr('id',step.id);this._debug("Step "+(this._current+1)+" of "+this._options.steps.length);}
else
{if(isOrphan)
{}
else
{}}};Tour.prototype._template=function(step,i){var $navigation,$next,$prev,$resume,$template,template;template=step.template;if(this._isOrphan(step)&&{}.toString.call(step.orphan)!=='[object Boolean]'){template=step.orphan;}
$template=$.isFunction(template)?$(template(i,step)):$(template);$navigation=$template.find('.popover-navigation');$prev=$navigation.find('[data-role="prev"]');$next=$navigation.find('[data-role="next"]');$resume=$navigation.find('[data-role="pause-resume"]');if(this._isOrphan(step)){$template.addClass('orphan');}
$template.addClass("tour-"+this._options.name+" tour-"+this._options.name+"-"+i);if(step.reflex){$template.addClass("tour-"+this._options.name+"-reflex");}
if(step.prev<0){$prev.addClass('disabled').prop('disabled',true).prop('tabindex',-1);}
if(step.next<0){$next.addClass('disabled').prop('disabled',true).prop('tabindex',-1);}
if(!step.duration){$resume.remove();}
return $template.clone().wrap('<div>').parent().html();};Tour.prototype._reflexEvent=function(reflex){if({}.toString.call(reflex)==='[object Boolean]'){return 'click';}else{return reflex;}};Tour.prototype._reposition=function($tip,step){var offsetBottom,offsetHeight,offsetRight,offsetWidth,originalLeft,originalTop,tipOffset;offsetWidth=$tip[0].offsetWidth;offsetHeight=$tip[0].offsetHeight;tipOffset=$tip.offset();originalLeft=tipOffset.left;originalTop=tipOffset.top;offsetBottom=$(document).height()-tipOffset.top-$tip.outerHeight();if(offsetBottom<0){tipOffset.top=tipOffset.top+offsetBottom;}
offsetRight=$('html').outerWidth()-tipOffset.left-$tip.outerWidth();if(offsetRight<0){tipOffset.left=tipOffset.left+offsetRight;}
if(tipOffset.top<0){tipOffset.top=0;}
if(tipOffset.left<0){tipOffset.left=0;}
$tip.offset(tipOffset);if(step.placement==='bottom'||step.placement==='top'){if(originalLeft!==tipOffset.left){return this._replaceArrow($tip,(tipOffset.left-originalLeft)*2,offsetWidth,'left');}}else{if(originalTop!==tipOffset.top){return this._replaceArrow($tip,(tipOffset.top-originalTop)*2,offsetHeight,'top');}}};Tour.prototype._center=function($tip)
{$tip.css('top',$(window).outerHeight()/2-$tip.outerHeight()/2);return $tip.css('left',$(window).outerWidth()/2-$tip.outerWidth()/2);};Tour.prototype._replaceArrow=function($tip,delta,dimension,position){return $tip.find('.arrow').css(position,delta?50*(1-delta/dimension)+'%':'');};Tour.prototype._scrollIntoView=function(i){var $element,$window,counter,height,offsetTop,scrollTop,step,windowHeight;step=this.getStep(i);$element=$(step.element);if(this._isOrphan(step))
{return this._showPopoverAndOverlay(i);}
if(!$element.length)
{return this._showPopoverAndOverlay(i);}
$window=$(window);offsetTop=$element.offset().top;height=$element.outerHeight();windowHeight=$window.height();scrollTop=0;switch(step.placement){case 'top':scrollTop=Math.max(0,offsetTop-(windowHeight/2));break;case 'left':case 'right':scrollTop=Math.max(0,(offsetTop+height/2)-(windowHeight/2));break;case 'bottom':scrollTop=Math.max(0,(offsetTop+height)-(windowHeight/2));}
this._debug("Scroll into view. ScrollTop: "+scrollTop+". Element offset: "+offsetTop+". Window height: "+windowHeight+".");counter=0;return $('body, html').stop(true,true).animate({scrollTop:Math.ceil(scrollTop)},(function(_this){return function(){if(++counter===2){_this._showPopoverAndOverlay(i);return _this._debug("Scroll into view.\nAnimation end element offset: "+($element.offset().top)+".\nWindow height: "+($window.height())+".");}};})(this));};Tour.prototype._initMouseNavigation=function(){var _this;_this=this;return $(document).off("click.tour-"+this._options.name,".popover.tour-"+this._options.name+" *[data-role='prev']").off("click.tour-"+this._options.name,".popover.tour-"+this._options.name+" *[data-role='next']").off("click.tour-"+this._options.name,".popover.tour-"+this._options.name+" *[data-role='end']").off("click.tour-"+this._options.name,".popover.tour-"+this._options.name+" *[data-role='pause-resume']").on("click.tour-"+this._options.name,".popover.tour-"+this._options.name+" *[data-role='next']",(function(_this){return function(e){e.preventDefault();return _this.next();};})(this)).on("click.tour-"+this._options.name,".popover.tour-"+this._options.name+" *[data-role='prev']",(function(_this){return function(e){e.preventDefault();if(_this._current>0){return _this.prev();}};})(this)).on("click.tour-"+this._options.name,".popover.tour-"+this._options.name+" *[data-role='end']",(function(_this){return function(e){e.preventDefault();return _this.end();};})(this)).on("click.tour-"+this._options.name,".popover.tour-"+this._options.name+" *[data-role='pause-resume']",function(e){var $this;e.preventDefault();$this=$(this);$this.text(_this._paused?$this.data('pause-text'):$this.data('resume-text'));if(_this._paused){return _this.resume();}else{return _this.pause();}});};Tour.prototype._initKeyboardNavigation=function(){if(!this._options.keyboard){return;}
return $(document).on("keyup.tour-"+this._options.name,(function(_this){return function(e){if(!e.which){return;}
switch(e.which)
{case 39:if($(".tour-step-element-reflexOnly").length==0)
{e.preventDefault();if(_this._isLast())
{return _this.end();}
else
{return _this.next();}}
break;case 37:if($(".tour-step-element-reflexOnly").length==0)
{e.preventDefault();if(_this._current>0)
{return _this.prev();}}
break;case 27:e.preventDefault();return _this.end();break;}};})(this));};Tour.prototype._makePromise=function(possiblePromise)
{if(possiblePromise&&$.isFunction(possiblePromise.then))
{return possiblePromise;}
else
{return null;}};Tour.prototype._callOnPromiseDone=function(promise,callback,arg)
{if(promise)
{return promise.then((function(_this)
{return function(e)
{return callback.call(_this,arg);};})(this));}
else
{return callback.call(this,arg);}};Tour.prototype._fixBootstrapSelectPickerZindex=function(step)
{if(this._isOrphan(step))
{return;}
if($(document).find(".popover.tour-"+this._options.name+".tour-"+this._options.name+"-"+this.getCurrentStepIndex()).length!=0)
{return;}
var $selectpicker;if($(step.element)[0].tagName.toLowerCase()=="select")
{$selectpicker=$(step.element);}
else
{$selectpicker=$(step.element).find("select:first");}
if($selectpicker.length>0&&$selectpicker.parent().hasClass("bootstrap-select"))
{this._debug("Fixing Bootstrap SelectPicker");$selectpicker.parent().css("z-index","1111");this._setStepFlag(this.getCurrentStepIndex(),"elementBootstrapSelectpicker",$selectpicker);}};Tour.prototype._unfixBootstrapSelectPickerZindex=function(step)
{var $selectpicker=this._getStepFlag(this.getCurrentStepIndex(),"elementBootstrapSelectpicker");if($selectpicker)
{this._debug("Unfixing Bootstrap SelectPicker");$selectpicker.parent().css("z-index","auto");}};Tour.prototype._createOverlayElements=function()
{var $backdrop=$('<div class="tour-backdrop" id="'+DOMID_BACKDROP.substr(1)+'"></div>');var $highlight=$('<div class="tour-highlight" id="'+DOMID_HIGHLIGHT.substr(1)+'" style="width:0px;height:0px;top:0px;left:0px;"></div>');if($(DOMID_BACKDROP).length===0)
{$(this._options.backdropContainer).append($backdrop);}
if($(DOMID_HIGHLIGHT).length===0)
{$(this._options.backdropContainer).append($highlight);}};Tour.prototype._destroyOverlayElements=function(step)
{$(DOMID_BACKDROP).remove();$(DOMID_HIGHLIGHT).remove();$(DOMID_PREVENT).remove();$(".tour-highlight-element").removeClass("tour-highlight-element");};Tour.prototype._hideBackdrop=function(step)
{var step=step||null;if(step)
{this._hideHighlightOverlay(step);if(typeof step.backdropOptions.animation.backdropHide=="function")
{step.backdropOptions.animation.backdropHide($(DOMID_BACKDROP));}
else
{$(DOMID_BACKDROP).addClass(step.backdropOptions.animation.backdropHide);$(DOMID_BACKDROP).hide(0,function()
{$(this).removeClass(step.backdropOptions.animation.backdropHide);});}}
else
{$(DOMID_BACKDROP).hide(0);$(DOMID_HIGHLIGHT).hide(0);$(DOMID_BACKDROP_TEMP).remove();$(DOMID_HIGHLIGHT_TEMP).remove();}};Tour.prototype._showBackdrop=function(step)
{var step=step||null;$(DOMID_BACKDROP).removeClass().addClass("tour-backdrop").hide(0);if(step)
{if(typeof step.backdropOptions.animation.backdropShow=="function")
{step.backdropOptions.animation.backdropShow($(DOMID_BACKDROP));}
else
{$(DOMID_BACKDROP).addClass(step.backdropOptions.animation.backdropShow);$(DOMID_BACKDROP).show(0,function()
{$(this).removeClass(step.backdropOptions.animation.backdropShow);});}
if(this._isOrphan(step))
{if($(DOMID_HIGHLIGHT).is(':visible'))
{this._hideHighlightOverlay(step);}
else
{}}
else
{if($(DOMID_HIGHLIGHT).is(':visible'))
{this._positionHighlightOverlay(step);}
else
{this._showHighlightOverlay(step);}}}
else
{$(DOMID_BACKDROP).show(0);$(DOMID_HIGHLIGHT).show(0);}};Tour.prototype._createStepSubset=function(step)
{var _this=this;var _stepElement=$(step.element);var stepSubset={element:_stepElement,container:step.container,autoscroll:step.autoscroll,backdrop:step.backdrop,preventInteraction:step.preventInteraction,isOrphan:this._isOrphan(step),orphan:step.orphan,showIfUnintendedOrphan:step.showIfUnintendedOrphan,duration:step.duration,delay:step.delay,fnPositionHighlight:function()
{_this._debug("Positioning highlight (fnPositionHighlight) over step element "+_stepElement[0].id+":\nWidth = "+_stepElement.outerWidth()+", height = "+_stepElement.outerHeight()+"\nTop: "+_stepElement.offset().top+", left: "+_stepElement.offset().left);$(DOMID_HIGHLIGHT).width(_stepElement.outerWidth()).height(_stepElement.outerHeight()).offset(_stepElement.offset());},};return stepSubset;};Tour.prototype._showHighlightOverlay=function(step)
{var $elemTmp=$(".tour-highlight-element");if($elemTmp.length>0)
{$elemTmp.removeClass('tour-highlight-element');}
var $modalCheck=$(step.element).parents(".modal:first");if($modalCheck.length)
{$modalCheck.addClass('tour-highlight-element');}
else
{$(step.element).addClass('tour-highlight-element');}
$(DOMID_HIGHLIGHT).removeClass().addClass("tour-highlight").hide(0);if(typeof step.backdropOptions.animation.highlightShow=="function")
{step.backdropOptions.animation.highlightShow($(DOMID_HIGHLIGHT),this._createStepSubset(step));}
else
{$(DOMID_HIGHLIGHT).css({"opacity":step.backdropOptions.highlightOpacity,"background-color":step.backdropOptions.highlightColor});$(DOMID_HIGHLIGHT).width(0).height(0).offset({top:0,left:0});$(DOMID_HIGHLIGHT).show(0);$(DOMID_HIGHLIGHT).addClass(step.backdropOptions.animation.highlightShow);$(DOMID_HIGHLIGHT).width($(step.element).outerWidth()).height($(step.element).outerHeight()).offset($(step.element).offset());$(DOMID_HIGHLIGHT).one('webkitAnimationEnd oanimationend msAnimationEnd animationend',function()
{$(DOMID_HIGHLIGHT).removeClass(step.backdropOptions.animation.highlightShow);});}};Tour.prototype._positionHighlightOverlay=function(step)
{var $elemTmp=$(".tour-highlight-element");if($elemTmp.length>0)
{$elemTmp.removeClass('tour-highlight-element');}
var $modalCheck=$(step.element).parents(".modal:first");if($modalCheck.length)
{$modalCheck.addClass('tour-highlight-element');}
else
{$(step.element).addClass('tour-highlight-element');}
if(typeof step.backdropOptions.animation.highlightTransition=="function")
{step.backdropOptions.animation.highlightTransition($(DOMID_HIGHLIGHT),this._createStepSubset(step));}
else
{$(DOMID_HIGHLIGHT).removeClass().addClass("tour-highlight");$(DOMID_HIGHLIGHT).css({"opacity":step.backdropOptions.highlightOpacity,"background-color":step.backdropOptions.highlightColor});$(DOMID_HIGHLIGHT).addClass(step.backdropOptions.animation.highlightTransition);$(DOMID_HIGHLIGHT).width($(step.element).outerWidth()).height($(step.element).outerHeight()).offset($(step.element).offset());$(DOMID_HIGHLIGHT).one('webkitAnimationEnd oanimationend msAnimationEnd animationend',function()
{$(DOMID_HIGHLIGHT).removeClass(step.backdropOptions.animation.highlightTransition);});}};Tour.prototype._hideHighlightOverlay=function(step)
{$(".tour-highlight-element").removeClass('tour-highlight-element');if(typeof step.backdropOptions.animation.highlightHide=="function")
{step.backdropOptions.animation.highlightHide($(DOMID_HIGHLIGHT),this._createStepSubset(step));}
else
{$(DOMID_HIGHLIGHT).addClass(step.backdropOptions.animation.highlightHide);$(DOMID_HIGHLIGHT).one('webkitAnimationEnd oanimationend msAnimationEnd animationend',function()
{$(DOMID_HIGHLIGHT).removeClass().addClass("tour-highlight");$(DOMID_HIGHLIGHT).hide(0);});}};Tour.prototype._updateBackdropElements=function(step)
{if(step.backdrop!=$(DOMID_BACKDROP).is(':visible'))
{if(step.backdrop)
{this._showBackdrop(step);}
else
{this._hideBackdrop(step);}}
else
{if(step.backdrop)
{if(this._isOrphan(step))
{if($(DOMID_HIGHLIGHT).is(':visible'))
{this._hideHighlightOverlay(step);}
else
{}}
else
{if($(DOMID_HIGHLIGHT).is(':visible'))
{this._positionHighlightOverlay(step);}
else
{this._showHighlightOverlay(step);}}}
else
{if($(DOMID_HIGHLIGHT).is(':visible'))
{this._hideHighlightOverlay(step);}}}
$(DOMID_BACKDROP_TEMP).remove();$(DOMID_HIGHLIGHT_TEMP).remove();if(step.backdropOptions.backdropSibling==true)
{$(DOMID_HIGHLIGHT).addClass('tour-behind');$(DOMID_BACKDROP).addClass('tour-zindexFix');$(DOMID_HIGHLIGHT).clone().prop('id',DOMID_HIGHLIGHT_TEMP.substring(1)).removeClass('tour-behind').insertAfter(".tour-highlight-element");$(DOMID_BACKDROP).clone().prop('id',DOMID_BACKDROP_TEMP.substring(1)).removeClass('tour-zindexFix').insertAfter(".tour-highlight-element");}
else
{$(DOMID_HIGHLIGHT).removeClass('tour-behind');$(DOMID_BACKDROP).removeClass('tour-zindexFix');}};Tour.prototype._updateOverlayElements=function(step)
{if(step.preventInteraction)
{this._debug("preventInteraction == true, adding overlay");if($(DOMID_PREVENT).length===0)
{$('<div class="tour-prevent" id="'+DOMID_PREVENT.substr(1)+'" style="width:0px;height:0px;top:0px;left:0px;"></div>').insertAfter(DOMID_HIGHLIGHT);}
$(DOMID_PREVENT).width($(step.element).outerWidth()).height($(step.element).outerHeight()).offset($(step.element).offset());}
else
{$(DOMID_PREVENT).remove();}};Tour.prototype._clearTimer=function(){window.clearTimeout(this._timer);this._timer=null;return this._duration=null;};Tour.prototype._getProtocol=function(url){url=url.split('://');if(url.length>1){return url[0];}else{return 'http';}};Tour.prototype._getHost=function(url){url=url.split('//');url=url.length>1?url[1]:url[0];return url.split('/')[0];};Tour.prototype._getPath=function(path){return path.replace(/\/?$/,'').split('?')[0].split('#')[0];};Tour.prototype._getQuery=function(path){return this._getParams(path,'?');};Tour.prototype._getHash=function(path){return this._getParams(path,'#');};Tour.prototype._getParams=function(path,start){var j,len,param,params,paramsObject;params=path.split(start);if(params.length===1){return{};}
params=params[1].split('&');paramsObject={};for(j=0,len=params.length;j<len;j++){param=params[j];param=param.split('=');paramsObject[param[0]]=param[1]||'';}
return paramsObject;};Tour.prototype._equal=function(obj1,obj2){var j,k,len,obj1Keys,obj2Keys,v;if({}.toString.call(obj1)==='[object Object]'&&{}.toString.call(obj2)==='[object Object]'){obj1Keys=Object.keys(obj1);obj2Keys=Object.keys(obj2);if(obj1Keys.length!==obj2Keys.length){return false;}
for(k in obj1){v=obj1[k];if(!this._equal(obj2[k],v)){return false;}}
return true;}else if({}.toString.call(obj1)==='[object Array]'&&{}.toString.call(obj2)==='[object Array]'){if(obj1.length!==obj2.length){return false;}
for(k=j=0,len=obj1.length;j<len;k=++j){v=obj1[k];if(!this._equal(v,obj2[k])){return false;}}
return true;}else{return obj1===obj2;}};return Tour;})();return Tour;});