var Osynapsy = Osynapsy || {};

Osynapsy.History =
{
    save : function()
    {
        var hst = [];
        var arr = [];
        if (sessionStorage.history){
            hst = JSON.parse(sessionStorage.history);
        }
        document.querySelectorAll('input,select,textarea').each(function(elm){
            //TODO implemente skip not history class .not('.history-skip')
            switch (elm.getAttribute('type')) {
                case 'submit':
                case 'button':
                case 'file':
                    return true;
                case 'checkbox':
                    if (!$(this).is(':checked')) {
                        return true;
                    }
                    break;
            }
            if (elm.getAttribute('name')) {
                arr.push([elm.name, elm.value]);
            }
        });
        hst.push({url : window.location.href, parameters : arr});
        sessionStorage.history = JSON.stringify(hst);
    },
    back : function()
    {
        if (!sessionStorage.history) {
            history.back();
        }
        var hst = JSON.parse(sessionStorage.history);
        var stp = hst.pop();
        if (Osynapsy.isEmpty(stp)) {
            history.back();
            return;
        }
        sessionStorage.history = JSON.stringify(hst);
        Osynapsy.post(stp.url, stp.parameters);
    },
    popLastStep : function()
    {
        if (!sessionStorage.history) {
            return;
        }
        let history = JSON.parse(sessionStorage.history);
        let lastUri = history.pop();
        sessionStorage.history = JSON.stringify(history);
        return lastUri;
    }
};
