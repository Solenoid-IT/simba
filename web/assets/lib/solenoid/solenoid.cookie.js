// Returns [object]
function getCookies ()
{
    // (Setting the value)
    let cookies = {};



    // (Getting the value)
    const entries = document.cookie.split(';');

    for (const cookie in entries)
    {// Processing each entry
        // (Getting the values)
        const [ key, value ] = entries[cookie].trim().split('=');

        // (Getting the value)
        cookies[ key ] = value;
    }



    // Returning the value
    return cookies;
}

// Returns [void]
function deleteCookie (name)
{
    // (Getting the value)
    document.cookie = name + '=; Max-Age=0';
}