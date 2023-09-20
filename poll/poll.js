Event.observe(window, 'load', initPoll);
Event.observe(document.onresize ? document : window, "resize", resized);
var normalColor = '#4c4c4c';
var errorColor = '#891616';
var buttonEnabledText = 'Изпрати';
var buttonDisabledText = 'Изпращане...';
var submitButton = null;
var box = null;
var doNotHideBox = false;
var onPages = false;
var blanketed = false;
var pollStarted = false;

var Cookie = {
    set: function(name, value, daysToExpire){
        var expire = '';
        if (daysToExpire != undefined) {
            var d = new Date();
            d.setTime(d.getTime() + (86400000 * parseFloat(daysToExpire)));
            expire = '; expires=' + d.toGMTString();
        }
        return (document.cookie = escape(name) + '=' + escape(value || '') +
        expire +
        '; path=/; domain=.programata.bg');
    },
    get: function(name){
        var cookie = document.cookie.match(new RegExp('(^|;)\\s*' + escape(name) + '=([^;\\s]*)'));
        return (cookie ? unescape(cookie[2]) : null);
    },
    erase: function(name){
        var cookie = Cookie.get(name) || true;
        Cookie.set(name, '', -1);
        return cookie;
    },
    accept: function(){
        if (typeof navigator.cookieEnabled == 'boolean') {
            return navigator.cookieEnabled;
        }
        Cookie.set('_test', '1');
        return (Cookie.erase('_test') === '1');
    }
};

function resized(){
    if (!pollStarted) {
        showPopup();
    }
    
}

function showPopup(){
    var dims = document.viewport.getDimensions();
    if (dims.width < 600 || dims.height < 500) {
        return;
    }
    if (!blanketed) {
        $('blanket').setStyle({
            'height': $('body').getHeight() + 'px'
        });
        $('pollInfoBox').setStyle({
            position: 'absolute',
            top: '230px',
            left: '230px'
        });
        $('blanket').show();
        $('pollPopupBox').show();
        blanketed = true;
    }
}

function initPoll(){
    var yesButton = $('continue_yes');
    var noButton = $('continue_no');
    if (yesButton && noButton) {
        showPopup();
        yesButton.enable();
        noButton.enable();
        yesButton.observe('click', function(){
            self.location.href = '/?p=144&l=1&c=1';
            //_continue(false);
        });
        noButton.observe('click', function(){
            _continue(true);
        });
    }
    else {
		pollStarted = true;
        $('menu').hide();
        initPollControls();
    }
    
    box = $('pollInfoBox');
    document.observe('click', function check(e){
        if (doNotHideBox) {
            doNotHideBox = false;
            return;
        }
        if (e.target == null || e.target != box) {
            box.hide();
        }
    });
}

function _continue(reject){
    new Ajax.Updater('poll', '/template/inquery.php', {
        parameters: {
            action: ((reject) ? 'reject' : 'poll')
        },
        onComplete: function(t){
            initPollControls();
        }
    });
}

function initPollControls(){
    submitButton = $('submitButton');
    bindInputs();
    bindNextPage();
    bindSubmit();
}

function bindNextPage(){
    var nextPageButton = $('next_poll_page');
    if (nextPageButton == null) 
        return;
    onPages = true;
    var pages = $$('div.poll_page');
    $('poll_paging').innerHTML = '1/' + pages.size();
    nextPageButton.observe('click', function(){
        var currentPage = pages.find(function(div){
            return div.visible();
        });
        if (validatePoll(currentPage)) {
            doNotHideBox = true;
            infoBox('Трябва да отговорите на всички въпроси!', 'Грешка');
            return;
        }
        var nextPage = currentPage.next();
        currentPage.hide();
        nextPage.show();
        $('poll_paging').innerHTML = (pages.indexOf(nextPage) + 1) + '/' + pages.size();
        if (nextPage == pages.last()) {
            $('next_poll_page').hide();
            $('email_label').show();
            $('email_input').show();
            $('submitButton').show();
        }
    });
}

function bindInputs(){
    $$('input[type="radio"]').each(function(input){
        input.observe('click', function(){
            var dd = input.up();
            var dt = dd.previous('dt');
            dt.setStyle({
                color: normalColor
            });
            $('pollForm').getInputs('radio', dt.id).each(function(radio){
                var inner_dl = radio.next('dl');
                if (inner_dl == null) 
                    return;
                if (radio.checked) 
                    inner_dl.show();
                else 
                    inner_dl.hide();
            });
        });
    });
    $$('input[type="checkbox"]').each(function(input){
        input.observe('click', function(){
            var dd = input.up();
            var dt = dd.previous('dt');
            dt.setStyle({
                color: (isChecked($('pollForm').getInputs('checkbox', dt.id + '[]'))) ? normalColor : errorColor
            });
            var inner_dl = input.next('dl');
            if (inner_dl != null) {
                inner_dl.toggle();
            }
        });
    });
}

function isChecked(inputs){
    return inputs.find(function(input){
        return input.checked;
    });
}

function validatePoll(currentPage){
    var error = false;
    $$('dt.question').each(function(dt){
        if (currentPage == null || dt.descendantOf(currentPage)) {
            var radios = $('pollForm').getInputs('radio', dt.id);
            var checkboxes = $('pollForm').getInputs('checkbox', dt.id + '[]');
            var inputs = (radios.size() > 0) ? radios : checkboxes;
            if (inputs.size() > 0) {
                var color = normalColor;
                if (!isChecked(inputs)) {
                    color = errorColor;
                    error = true;
                }
                dt.setStyle({
                    color: color
                });
            }
        }
    });
    return error;
}

function validateEmail(){
    if ($('user_email') == null) 
        return;
    return !/^[^@]+@[-a-z0-9.]+$/i.test($F('user_email'));
}

function buttonState(enabled, button){
    if (enabled) {
        submitButton.value = buttonEnabledText;
        submitButton.disabled = false;
    }
    else {
        submitButton.value = buttonDisabledText;
        submitButton.disabled = true;
    }
}

function infoBox(msg, title){
    box.innerHTML = '<h1>' + title + '</h1>' + '<p>' + msg + '</p>';
    box.show();
}

function waitRedirect(timeout, url, timeout_span){
    var waiter = new PeriodicalExecuter(oneSecond, 1);
    waiter.counter = 0;
    waiter.timeout = timeout;
    waiter.url = url;
    waiter.timeout_span = timeout_span;
    
    function oneSecond(){
        if (this.counter >= this.timeout) {
            self.location.href = this.url;
            this.stop();
            return;
        }
        this.counter++;
        timeout_span.innerHTML = this.timeout - this.counter;
        
    }
}

function bindSubmit(){
    if (submitButton == null) 
        return;
    submitButton.observe('click', function(){
        buttonState(false);
        var pages = $$('div.poll_page');
        var currentPage = (pages.size() > 0) ? pages.last() : null;
        if (validatePoll(currentPage)) {
            doNotHideBox = true;
            infoBox('Трябва да отговорите на всички въпроси!', 'Грешка');
            buttonState(true);
            return;
        }
        if (validateEmail()) {
            doNotHideBox = true;
            infoBox('Неправилно въведен емейл адрес!', 'Грешка');
            buttonState(true);
            return;
        }
        $('pollForm').request({
            onSuccess: function(t){
                Cookie.set('poll201003', '1', 30);
                if (onPages) {
                    $('poll').innerHTML = t.responseText;
                    var timeout_span = $('timeout_span');
                    if (timeout_span != null) {
                        waitRedirect(10, 'http://programata.bg', timeout_span);
                    }
                }
                else {
                    blanketed = true;
                    $('pollPopupBox').hide();
                    $('blanket').hide();
                    return;
                }
                
            },
            onFailure: function(t){
                infoBox(t.responseText, 'Грешка');
            },
            onComplete: function(){
                buttonState(true);
            }
        });
    });
}
