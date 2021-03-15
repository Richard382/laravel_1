const getURLVars = () => {
    let vars = {};
    let parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m,key,value) {
        vars[key] = value;
    });
    return vars;
};

export const URLParams = (parameter, defaultvalue) => {
    let params = defaultvalue;

    if(window.location.href.indexOf(parameter) > -1)
    {
        params = getURLVars()[parameter];
    }

    return params;
}

export default URLParams;
