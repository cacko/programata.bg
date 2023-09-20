google.load('visualization', '1', {
    'packages': ['piechart', 'barchart', 'columnchart']
});
Event.observe(window, 'load', init);
var loginMode = true;

function init(){
    bindLogin();
    if (!loginMode) {
        bindControls();
    }
    var pe = new PeriodicalExecuter(updateCount, 3);
    function updateCount(){
        var oldVotes = parseInt($('votes').innerHTML);
        var oldRejects = parseInt($('rejects').innerHTML);
        new Ajax.Request('/poll/?action=totals', {
            onSuccess: function(t){
                var newVotes = parseInt(t.responseJSON['votes']);
                var newRejects = parseInt(t.responseJSON['rejects']);
                $('votes').update(newVotes);
                $('rejects').update(newRejects);
                if (newRejects != oldRejects) {
                    Effect.Pulsate('rejects', {
                        pulses: 5,
                        duration: 1
                    });
                }
                if (oldVotes != newVotes) {
                    Effect.Pulsate('votes', {
                        pulses: 5,
                        duration: 1
                    });
                }
            }
        })
        
    }
    
    //
    //showTotalsChart();
    //createChart();
}

function bindLogin(){
    var password = $('password_field');
    if (password == null) {
        loginMode = false;
        return;
    }
    password.observe('keydown', function(e){
        if (e.keyCode == 13) {
            new Ajax.Request('/poll/?action=login', {
                parameters: {
                    password: password.value
                },
                onSuccess: function(t){
                    $('body').update(t.responseText);
                    bindControls();
                },
                onFailure: function(t){
                    alert('invalid password');
                }
            });
        }
    });
}

function bindControls(){
    var first = $('firstQuestion');
    var second = $('secondQuestion');
    if (!first.value.blank()) {
        updateSecond();
    }
    if (!second.value.blank()) {
        updateChecks();
    }
    first.observe('change', updateSecond);
    function updateSecond(){
        $('values').innerHTML = '';
        new Ajax.Updater(second, '/poll/?action=questions', {
            parameters: {
                exclude: first.value
            },
            onSuccess: function(){
                second.enable();
            }
        });
    }
    second.observe('change', updateChecks);
    function updateChecks(){
        if (second.value.blank()) {
            $('values').innerHTML = '';
            return;
        }
        new Ajax.Updater('values', '/poll/?action=checks', {
            parameters: {
                question: second.value
            }
        });
    }
    var button = $('createButton');
    var form = $('chartForm');
    button.observe('click', createChart);
    function validateForm(){
    
        if (first.value.blank()) {
            return true;
        }
        if (!second.value.blank()) {
            if (form.getInputs('checkbox').findAll(function(el){
                return el.checked;
            }).size() ==
            0) {
                return true;
            }
        }
    }
    function createChart(){
        if (validateForm()) {
            alert('не може');
            return;
        }
        var newId = createNewChartContainer()
        $('ChartIdInput').value = newId;
        form.request({
            onComplete: function(t){
                $(newId).scrollTo();
            },
            onFailure: function(t){
                alert(t.responseText);
            }
        });
    }
}

function createNewChartContainer(){
    var container = new Element('div', {
        'class': 'chart'
    });
    var chart = new Element('div');
    var chart_id = chart.identify();
    container.insert(chart);
    $('main').insert(container);
    return chart_id;
}

