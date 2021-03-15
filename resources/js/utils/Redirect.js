import queryString from 'query-string';

export const Redirect = (url, object = false) => {

    if (object)
    {
        let parameters = queryString.stringify(object, {arrayFormat: 'bracket'});

        url += parameters
    }

    window.location = url;
};

export default Redirect;
