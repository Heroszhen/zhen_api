export const getToken = async () => {
    let response = await fetch(
        '/get-login-token',
        {headers: {'X-Requested-With': 'XMLHttpRequest'}}
    )
    response = await response.json();
    return response;
}