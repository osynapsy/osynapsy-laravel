var Osynapsy = Osynapsy || {};

Osynapsy.History =
{
    save : function()
    {
        let hst = sessionStorage.history ? JSON.parse(sessionStorage.history) : [];
        let fields = [];        
        document.querySelectorAll('input,select,textarea').forEach(function(elm){
            if (elm.classList.contains('history-skip')) {
                return true;
            }
            if (['submit', 'button', 'file'].includes(elm.getAttribute('type'))) {
                return true;
            }
            if (elm.getAttribute('type') === 'checkbox' && elm.checked) {
                return true;
            }            
            if (elm.getAttribute('name')) {
                fields.push([elm.name, elm.value]);
            }            
        });
        hst.push({url : window.location.href, parameters : fields});
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
