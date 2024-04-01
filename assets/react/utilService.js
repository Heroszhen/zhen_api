export const getToken = async () => {
    let response = await fetch(
        '/get-login-token',
        {headers: {'X-Requested-With': 'XMLHttpRequest'}}
    )
    response = await response.json();
    return response;
}

export const getHeaders = async (method='', token=null) => {
    if(token === null)token = (await getToken())['data'];
    let contentType = 'application/ld+json';
    if (method.toLowerCase() === 'patch')contentType = 'application/merge-patch+json';
    return {
        'Authorization': `Bearer ${token}`,
        'Content-type': contentType
    }
}