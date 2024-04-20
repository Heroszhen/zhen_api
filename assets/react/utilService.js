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

/**
 * 
 * @param {string} value 
 * @returns {void}
 */
export function copyToClipboard(value) {
    let input = document.createElement("input");
    document.body.appendChild(input);
    input.value = value;
    input.focus();
    input.select();
    document.execCommand('copy');
    input.parentNode.removeChild(input);
}